<?php

namespace NextDeveloper\IPAAS\Jobs\Zapier;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\IPAAS\Integrations\WapIntegrationFactory;

/**
 * SyncZapierWorkflowsJob
 *
 * Iterates every active Providers record with provider_type = 'zapier' and
 * pulls all zaps from the corresponding Zapier account into the
 * ipaas_workflows table via ZapierIntegration::syncWorkflows().
 *
 * Note: Zapier does not expose execution history via the public API,
 * so there is no corresponding SyncZapierExecutionsJob.
 *
 * Usage:
 *   // Sync all zapier providers:
 *   SyncZapierWorkflowsJob::dispatch();
 *
 *   // Sync a single provider:
 *   SyncZapierWorkflowsJob::dispatch($provider);
 */
class SyncZapierWorkflowsJob implements ShouldQueue
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

        return Providers::where('provider_type', 'zapier')->cursor();
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

            Log::info('[IPAAS] Zapier workflow sync completed', array_merge($context, [
                'workflows_upserted' => $count,
            ]));
        } catch (\Throwable $e) {
            Log::error('[IPAAS] Zapier workflow sync failed', array_merge($context, [
                'error' => $e->getMessage(),
            ]));

            throw $e;
        }
    }
}
