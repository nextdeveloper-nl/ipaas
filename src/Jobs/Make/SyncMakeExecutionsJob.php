<?php

namespace NextDeveloper\IPAAS\Jobs\Make;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\IPAAS\Integrations\WapIntegrationFactory;

/**
 * SyncMakeExecutionsJob
 *
 * Iterates every active Providers record with provider_type = 'make' and
 * pulls execution logs from the corresponding Make.com team into the
 * ipaas_workflow_executions table via MakeIntegration::syncExecutions().
 *
 * Usage:
 *   // Sync executions for the last 24 hours across all make providers:
 *   SyncMakeExecutionsJob::dispatch();
 *
 *   // Sync executions for the last 7 days for a single provider:
 *   SyncMakeExecutionsJob::dispatch($provider, now()->subDays(7));
 */
class SyncMakeExecutionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 600;
    public int $backoff = 60;

    private ?Providers $provider;
    private ?int       $sinceTimestamp;

    public function __construct(?Providers $provider = null, ?\DateTimeInterface $since = null)
    {
        $this->provider       = $provider;
        $this->sinceTimestamp = $since ? $since->getTimestamp() : null;
        $this->queue          = 'ipaas-sync';
    }

    public function handle(): void
    {
        $since = $this->sinceTimestamp
            ? Carbon::createFromTimestamp($this->sinceTimestamp)
            : null;

        foreach ($this->resolveProviders() as $provider) {
            $this->syncProvider($provider, $since);
        }
    }

    private function resolveProviders(): iterable
    {
        if ($this->provider !== null) {
            return [$this->provider];
        }

        return Providers::where('provider_type', 'make')->cursor();
    }

    private function syncProvider(Providers $provider, ?Carbon $since): void
    {
        $context = [
            'provider_id'   => $provider->uuid ?? $provider->id,
            'provider_name' => $provider->name ?? 'unnamed',
            'since'         => $since ? $since->toIso8601String() : now()->subDay()->toIso8601String(),
        ];

        try {
            $integration = WapIntegrationFactory::make($provider);
            $count       = $integration->syncExecutions($since);

            Log::info('[IPAAS] Make execution sync completed', array_merge($context, [
                'executions_upserted' => $count,
            ]));
        } catch (\Throwable $e) {
            Log::error('[IPAAS] Make execution sync failed', array_merge($context, [
                'error' => $e->getMessage(),
            ]));

            throw $e;
        }
    }
}
