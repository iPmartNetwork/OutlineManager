<?php

use App\Services\OutlineVPN\ApiClient;
use Carbon\Carbon;

if (! function_exists('get_server_usage_metrics')) {
    function get_server_usage_metrics(ApiClient $api, int $serverId): object
    {
        return cache()->remember("server-$serverId-metrics", now()->addMinute(), function () use ($api) {
            $usageRequest = $api->metricsTransfer();

            return $usageRequest->result->bytesTransferredByUserId;
        });
    }
}

if (! function_exists('format_bytes')) {
    function format_bytes(int $bytes, bool $asArray = false): string|array
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $unitIndex = 0;

        while ($bytes >= 1024) {
            $bytes /= 1024;
            $unitIndex++;
        }

        $value = round($bytes, 2);
        $unit = $units[$unitIndex];

        $result = ['value' => $value, 'unit' => $unit];

        if ($asArray) {
            return $result;
        }

        return implode(' ', $result);
    }
}

if (! function_exists('format_as_duration')) {
    function format_as_duration(Carbon $start, Carbon $end): string
    {
        $duration = $end->diff($start);
        $hours = $duration->h;
        $minutes = $duration->i;
        $seconds = $duration->s;

        return sprintf('%dd %dh %dm %ds', $duration->days, $hours, $minutes, $seconds);
    }
}
