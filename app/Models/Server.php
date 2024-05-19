<?php

namespace App\Models;

use App\Services\OutlineVPN\ApiAccessKey;
use App\Services\OutlineVPN\ApiClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Throwable;

class Server extends Model
{
    protected $fillable = [
        'api_url',
        'api_cert_sha256',
        'api_id',
        'name',
        'version',
        'hostname_or_ip',
        'hostname_for_new_access_keys',
        'port_for_new_access_keys',
        'total_data_usage',
        'is_metrics_enabled',
        'is_available',
        'api_created_at',
    ];

    protected $casts = [
        'api_created_at' => 'datetime',
        'is_metrics_enabled' => 'boolean',
        'is_available' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Server $server) {
            $api = new ApiClient($server->api_url, $server->api_cert_sha256);
            $serverInfoRequest = $api->server();

            if (!$serverInfoRequest->succeed) {
                $serverInfoRequest->throw();
            }

            $serverInfo = $serverInfoRequest->result;

            static::mapApiResult($server, $serverInfo);
        });

        static::updating(function (Server $server) {
            $api = new ApiClient($server->api_url, $server->api_cert_sha256);

            $nameUpdateRequest = $api->setServerName($server->name);

            if (!$nameUpdateRequest->succeed) {
                $nameUpdateRequest->throw();
            }

            $hostnameUpdateRequest = $api->setHostNameForNewKeys($server->hostname_for_new_access_keys);

            if (!$hostnameUpdateRequest->succeed) {
                $hostnameUpdateRequest->throw();
            }

            $portUpdateRequest = $api->setPortForNewKeys($server->port_for_new_access_keys);

            if (!$portUpdateRequest->succeed) {
                $portUpdateRequest->throw();
            }
        });

        static::retrieved(function (Server $server) {
            if (!$server->api_url) {
                return;
            }

            try {
                $api = new ApiClient($server->api_url, $server->api_cert_sha256);
                $maxRetry = 3;
                $try = 0;

                do {
                    try {
                        $serverInfoRequest = $api->server();
                        $serverInfo = $serverInfoRequest->result;

                        $metrics = get_server_usage_metrics($api, $server->id);

                        static::mapApiResult($server, $serverInfo, (array) $metrics);

                        $server->syncKeys($api, $metrics);

                        break;
                    } catch (Throwable $exception) {
                        // TODO: report error to sentry
                        $try++;
                        //                        dd($exception);
                    }
                } while ($try < $maxRetry);

                $server->is_available = $try < $maxRetry;
            } catch (Throwable $exception) {
                $server->is_available = false;
                // TODO: report error to sentry
                //                dd($exception);
            } finally {
                $server->saveQuietly();
            }
        });
    }

    public function keys(): HasMany
    {
        return $this->hasMany(AccessKey::class);
    }

    private function syncKeys(ApiClient $api, ?object $metrics = null): void
    {
        // Get the server keys
        $keysRequest = $api->keys();
        if (!$keysRequest->succeed) {
            $keysRequest->throw();
        }

        $serverKeys = collect($keysRequest->result->accessKeys)->map(fn ($serverKey) => ApiAccessKey::fromObject($serverKey));

        // Get the keys in local db
        $localKeys = $this->keys;

        // Create missing keys & update existing keys
        $serverKeys->each(function ($serverKey) use ($localKeys, $metrics) {
            if ($localKey = $localKeys->first(fn ($localKey) => $localKey->api_id === $serverKey->id)) {
                $localKey->name = $serverKey->name;
                $localKey->data_limit = $serverKey->dataLimitInBytes;

                if (isset($metrics->{$serverKey->id})) {
                    $localKey->data_usage = $metrics->{$serverKey->id};
                }

                $localKey->saveQuietly();
            } else {
                $newLocalKey = new AccessKey();
                $newLocalKey->server_id = $this->id;
                $newLocalKey->name = $serverKey->name;
                $newLocalKey->port = $serverKey->port;
                $newLocalKey->api_id = $serverKey->id;
                $newLocalKey->method = $serverKey->method;
                $newLocalKey->password = $serverKey->password;
                $newLocalKey->access_url = $serverKey->accessUrl;
                $newLocalKey->data_limit = $serverKey->dataLimitInBytes;
                $newLocalKey->saveQuietly();
            }
        });

        // Remove missing keys
        $localKeys->each(function ($localKey) use ($serverKeys) {
            if ($serverKeys->contains(fn ($serverKey) => $serverKey->id === $localKey->api_id)) {
                return;
            }

            $localKey->deleteQuietly();
        });
    }

    private static function mapApiResult(Server $server, object $apiResult, array $metrics = []): void
    {
        $server->api_id = $apiResult->serverId;
        $server->name = $apiResult->name;
        $server->version = $apiResult->version;
        $server->is_metrics_enabled = $apiResult->metricsEnabled;
        $server->api_created_at = now()->parse($apiResult->createdTimestampMs / 1000);
        $server->port_for_new_access_keys = $apiResult->portForNewAccessKeys;
        $server->hostname_for_new_access_keys = $apiResult->hostnameForAccessKeys;

        $server->total_data_usage = collect($metrics)->sum();
    }
}
