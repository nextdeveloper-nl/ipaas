<?php

namespace NextDeveloper\IPAAS\Jobs\N8N;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\IPAAS\Integrations\WapIntegrationFactory;

/**
 * SyncN8NWorkflowsJob
 *
 * Iterates every active Providers record with provider_type = 'n8n' and
 * pulls all workflows from the corresponding N8N instance into the
 * ipaas_workflows table via N8NIntegration::syncWorkflows().
 *
 * Dispatch this job periodically (e.g. every hour) via the scheduler or
 * by dispatching it manually from a controller action.
 *
 * Usage:
 *   // Sync all n8n providers:
 *   SyncN8NWorkflowsJob::dispatch();
 *
 *   // Sync a single provider:
 *   SyncN8NWorkflowsJob::dispatch($provider);
 */
class SyncN8NWorkflowsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted before failing.
     */
    public int $tries = 3;

    /**
     * Maximum number of seconds the job may run.
     * Syncing many workflows from a slow N8N instance can take a while.
     */
    public int $timeout = 300;

    /**
     * Number of seconds to wait before retrying a failed attempt.
     */
    public int $backoff = 60;

    /**
     * When set, only the given provider is synced.
     * When null, all n8n providers are synced.
     */
    private ?Providers $provider;

    /**
     * @param  Providers|null $provider  Limit sync to a single provider (optional).
     */
    public function __construct(?Providers $provider = null)
    {
        $this->provider = $provider;
        $this->queue    = 'ipaas-sync';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $providers = $this->resolveProviders();

        foreach ($providers as $provider) {
            $this->syncProvider($provider);
        }
    }

    /**
     * Return the list of providers to sync.
     *
     * @return iterable<Providers>
     */
    private function resolveProviders(): iterable
    {
        if ($this->provider !== null) {
            return [$this->provider];
        }

        return Providers::where('provider_type', 'n8n')->cursor();
    }

    /**
     * Run syncWorkflows() for a single provider, logging outcomes.
     */
    private function syncProvider(Providers $provider): void
    {
        $context = [
            'provider_id'   => $provider->uuid ?? $provider->id,
            'provider_name' => $provider->name ?? 'unnamed',
        ];

        try {
            $integration = WapIntegrationFactory::make($provider);
            $count       = $integration->syncWorkflows();

            Log::info('[IPAAS] N8N workflow sync completed', array_merge($context, [
                'workflows_upserted' => $count,
            ]));
        } catch (\Throwable $e) {
            Log::error('[IPAAS] N8N workflow sync failed', array_merge($context, [
                'error' => $e->getMessage(),
            ]));

            // Re-throw so the queue driver records the failure and can retry
            throw $e;
        }
    }
}
