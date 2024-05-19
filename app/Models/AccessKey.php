<?php

namespace App\Models;

use App\Services\OutlineVPN\ApiAccessKey;
use App\Services\OutlineVPN\ApiClient;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessKey extends Model
{
    protected $fillable = [
        'server_id',
        'api_id',
        'name',
        'password',
        'method',
        'access_url',
        'port',
        'data_limit',
        'data_usage',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (AccessKey $accessKey) {
            $api = new ApiClient($accessKey->server->api_url, $accessKey->server->api_cert_sha256);
            $newKeyRequest = $api->createKey();

            if (! $newKeyRequest->succeed) {
                $newKeyRequest->throw();
            }

            $outlineAccessKey = ApiAccessKey::fromObject($newKeyRequest->result);
            $renameRequest = $api->renameKey($outlineAccessKey->id, $accessKey->name);

            if (! $renameRequest->succeed) {
                $renameRequest->throw();
            }

            if ($accessKey->data_limit) {
                $dataLimitRequest = $api->setDataLimitForKey($outlineAccessKey->id, $accessKey->data_limit);

                if (! $dataLimitRequest->succeed) {
                    $dataLimitRequest->throw();
                }
            }

            $accessKey->api_id = $outlineAccessKey->id;
            $accessKey->password = $outlineAccessKey->password;
            $accessKey->method = $outlineAccessKey->method;
            $accessKey->port = $outlineAccessKey->port;
            $accessKey->access_url = "$outlineAccessKey->accessUrl";
        });

        static::updating(function (AccessKey $accessKey) {
            $api = new ApiClient($accessKey->server->api_url, $accessKey->server->api_cert_sha256);
            $renameRequest = $api->renameKey($accessKey->api_id, $accessKey->name);

            if (! $renameRequest->succeed) {
                $renameRequest->throw();
            }

            $dataLimitRequest = $accessKey->data_limit ?
                $api->setDataLimitForKey($accessKey->api_id, $accessKey->data_limit) :
                $api->removeDataLimitForKey($accessKey->api_id);

            if (! $dataLimitRequest->succeed) {
                $dataLimitRequest->throw();
            }
        });

        static::deleting(function (AccessKey $accessKey) {
            $api = new ApiClient($accessKey->server->api_url, $accessKey->server->api_cert_sha256);
            $deleteKeyRequest = $api->deleteKey($accessKey->api_id);

            if (! $deleteKeyRequest->succeed) {
                $deleteKeyRequest->throw();
            }
        });
    }

    protected function isExpired(): Attribute
    {
        return Attribute::make(
            get: fn () => now()->gt($this->expires_at)
        );
    }

    public function disable(): void
    {
        $limit = 1024; // 1KB
        $api = new ApiClient($this->server->api_url, $this->server->api_cert_sha256);
        $api->removeDataLimitForKey($this->api_id);
        $api->setDataLimitForKey($this->api_id, $limit);

        $this->data_limit = $limit;
        $this->saveQuietly();
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
