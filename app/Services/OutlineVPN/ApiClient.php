<?php

namespace App\Services\OutlineVPN;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class ApiClient
{
    protected PendingRequest $httpSession;

    public function __construct(protected string $apiUrl, protected string $certSha256)
    {
        $this->createHttpSession();
    }

    public function server(): ApiResponse
    {
        $response = $this->httpSession->get('/server');

        return $this->createApiResponse($response);
    }

    public function setHostNameForNewKeys(string $hostnameOrIpAddress): ApiResponse
    {
        $response = $this->httpSession->put('/server/hostname-for-access-keys', [
            'hostname' => $hostnameOrIpAddress,
        ]);

        return $this->createApiResponse($response);
    }

    public function setServerName(string $name): ApiResponse
    {
        $response = $this->httpSession->put('/name', [
            'name' => $name,
        ]);

        return $this->createApiResponse($response);
    }

    public function setPortForNewKeys(int $port): ApiResponse
    {
        $response = $this->httpSession->put('/server/port-for-new-access-keys', [
            'port' => $port,
        ]);

        return $this->createApiResponse($response);
    }

    public function metricsTransfer(): ApiResponse
    {
        $response = $this->httpSession->get('/metrics/transfer');

        return $this->createApiResponse($response);
    }

    public function keys(): ApiResponse
    {
        $response = $this->httpSession->get('/access-keys');

        return $this->createApiResponse($response);
    }

    public function createKey(): ApiResponse
    {
        $response = $this->httpSession->post('/access-keys');

        return $this->createApiResponse($response);
    }

    public function renameKey(int $id, string $name): ApiResponse
    {
        $response = $this->httpSession->put("/access-keys/{$id}/name", [
            'name' => $name,
        ]);

        return $this->createApiResponse($response);
    }

    public function deleteKey(int $id): ApiResponse
    {
        $response = $this->httpSession->delete("/access-keys/${id}");

        return $this->createApiResponse($response);
    }

    public function setDataLimitForKey(int $id, int $limitInBytes): ApiResponse
    {
        $response = $this->httpSession->put("/access-keys/{$id}/data-limit", [
            'limit' => [
                'bytes' => $limitInBytes,
            ],
        ]);

        return $this->createApiResponse($response);
    }

    public function removeDataLimitForKey(int $id): ApiResponse
    {
        $response = $this->httpSession->delete("/access-keys/{$id}/data-limit");

        return $this->createApiResponse($response);
    }

    protected function createHttpSession(): void
    {
        $sslOptions = [
            'verify_peer' => true,
            'verify_peer_name' => true,
            'allow_self_signed' => true,
            'peer_fingerprint' => [['sha256' => $this->certSha256]],
        ];

        $this->httpSession = Http::baseUrl($this->apiUrl)
            ->asJson()
            ->acceptJson()
            ->withoutVerifying()
            ->withOptions(['ssl' => $sslOptions])
            ->timeout(config('outline.server_availability_check_timeout'));
    }

    protected function createApiResponse(\Illuminate\Http\Client\Response $response): ApiResponse
    {
        $statusCode = $response->status();
        $result = json_decode($response->getBody()->getContents());

        if ($statusCode >= Response::HTTP_OK && $statusCode <= Response::HTTP_IM_USED) {
            return ApiResponse::succeed(
                statusCode: $statusCode,
                result: $result
            );
        }

        if ($statusCode === Response::HTTP_UNAUTHORIZED) {
            ApiResponse::unauthenticated();
        }

        if ($statusCode === Response::HTTP_FORBIDDEN) {
            ApiResponse::unauthorized();
        }

        return ApiResponse::error(statusCode: $statusCode, message: $result->message ?? null, errors: $result->errors ?? []);
    }
}
