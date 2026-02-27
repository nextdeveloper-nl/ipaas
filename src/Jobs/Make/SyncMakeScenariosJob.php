<?php

namespace NextDeveloper\IPAAS\Jobs\Make;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\IPAAS\Integrations\WapIntegrationFactory;

/**
 * SyncMakeScenariosJob
 *
 * Iterates every active Providers record with provider_type = 'make' and
 * pulls all scenarios from the corresponding Make.com team into the
 * ipaas_workflows table via MakeIntegration::syncWorkflows().
 *
 * Usage:
 *   // Sync all make providers:
 *   SyncMakeScenariosJob::dispatch();
 *
 *   // Sync a single provider:
 *   SyncMakeScenariosJob::dispatch($provider);
 */
class SyncMakeScenariosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 300;
    public int $backoff = 60;

    private ?Providers $provider;

    public function __construct(?Providers $provider = null)
    {
        $this->provider = $provider;
        $this->queue    = 'ipaas-sync';
    }

    public function handle(): void
    {
        foreach ($this->resolveProviders() as $provider) {
            $this->syncProvider($provider);
        }
    }

    private function resolveProviders(): iterable
    {
        if ($this->provider !== null) {
            return [$this->provider];
        }

        return Providers::where('provider_type', 'make')->cursor();
    }

    private function syncProvider(Providers $provider): void
    {
        $context = [
            'provider_id'   => $provider->uuid ?? $provider->id,
            'provider_name' => $provider->name ?? 'unnamed',
        ];

        try {
            $integration = WapIntegrationFactory::make($provider);
            $count       = $integration->syncWorkflows();

            Log::info('[IPAAS] Make scenario sync completed', array_merge($context, [
                'scenarios_upserted' => $count,
            ]));
        } catch (\Throwable $e) {
            Log::error('[IPAAS] Make scenario sync failed', array_merge($context, [
                'error' => $e->getMessage(),
            ]));

            throw $e;
        }
    }
}
