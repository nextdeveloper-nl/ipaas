<?php

namespace NextDeveloper\IPAAS\Jobs\N8N;

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
 * SyncN8NExecutionsJob
 *
 * Iterates every active Providers record with provider_type = 'n8n' and
 * pulls execution history from the corresponding N8N instance into the
 * ipaas_workflow_executions table via N8NIntegration::syncExecutions().
 *
 * By default the sync window is the last 24 hours. Pass a $since datetime
 * to extend or narrow the window.
 *
 * Usage:
 *   // Sync executions for the last 24 hours across all n8n providers:
 *   SyncN8NExecutionsJob::dispatch();
 *
 *   // Sync executions for the last 7 days for a single provider:
 *   SyncN8NExecutionsJob::dispatch($provider, now()->subDays(7));
 */
class SyncN8NExecutionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted before failing.
     */
    public int $tries = 3;

    /**
     * Maximum number of seconds the job may run.
     * Execution history can be large — allow generous time.
     */
    public int $timeout = 600;

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
     * Pull executions that started at or after this datetime.
     * Serialised as a Unix timestamp to survive the queue.
     */
    private ?int $sinceTimestamp;

    /**
     * @param  Providers|null         $provider  Limit sync to a single provider (optional).
     * @param  \DateTimeInterface|null $since     Sync window start (defaults to 24 h ago).
     */
    public function __construct(?Providers $provider = null, ?\DateTimeInterface $since = null)
    {
        $this->provider       = $provider;
        $this->sinceTimestamp = $since ? $since->getTimestamp() : null;
        $this->queue          = 'ipaas-sync';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $since     = $this->sinceTimestamp
            ? Carbon::createFromTimestamp($this->sinceTimestamp)
            : null;

        $providers = $this->resolveProviders();

        foreach ($providers as $provider) {
            $this->syncProvider($provider, $since);
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
     * Run syncExecutions() for a single provider, logging outcomes.
     */
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

            Log::info('[IPAAS] N8N execution sync completed', array_merge($context, [
                'executions_upserted' => $count,
            ]));
        } catch (\Throwable $e) {
            Log::error('[IPAAS] N8N execution sync failed', array_merge($context, [
                'error' => $e->getMessage(),
            ]));

            throw $e;
        }
    }
}
