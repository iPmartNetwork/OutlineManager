<?php

namespace App\Jobs;

use App\Models\AccessKey;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DisableExpiredAccessKeys implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        AccessKey::with('server')
            ->where('expires_at', '<=', now())
            ->chunk(100, function (Collection $expiredKeys) {
                $expiredKeys->each(fn (AccessKey $accessKey) => $accessKey->disable());
            });
    }
}
