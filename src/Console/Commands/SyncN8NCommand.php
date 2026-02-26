<?php

namespace NextDeveloper\IPAAS\Console\Commands;

use Illuminate\Console\Command;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\IPAAS\Jobs\N8N\SyncN8NExecutionsJob;
use NextDeveloper\IPAAS\Jobs\N8N\SyncN8NWorkflowsJob;

/**
 * SyncN8NCommand
 *
 * Artisan command that dispatches the N8N sync jobs.
 *
 * By default both workflows and executions are synced for every registered
 * n8n provider. Use flags to limit the scope.
 *
 * Examples:
 *   php artisan ipaas:sync-n8n
 *   php artisan ipaas:sync-n8n --workflows-only
 *   php artisan ipaas:sync-n8n --executions-only
 *   php artisan ipaas:sync-n8n --provider=<uuid>
 *   php artisan ipaas:sync-n8n --since="7 days ago"
 *   php artisan ipaas:sync-n8n --sync          (run inline, no queue)
 */
class SyncN8NCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ipaas:sync-n8n
        {--workflows-only  : Only sync workflows, skip executions}
        {--executions-only : Only sync executions, skip workflows}
        {--provider=       : UUID of a single provider to sync (syncs all if omitted)}
        {--since=          : Execution sync window start, e.g. "7 days ago" or "2026-01-01"}
        {--sync            : Run synchronously in this process instead of dispatching to the queue}';

    /**
     * The console command description.
     */
    protected $description = 'Sync N8N workflows and/or executions for all (or a specific) registered n8n provider';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        UserHelper::setAdminAsCurrentUser();

        $syncWorkflows  = !$this->option('executions-only');
        $syncExecutions = !$this->option('workflows-only');
        $runSync        = (bool) $this->option('sync');
        $providerUuid   = $this->option('provider');
        $sinceOption    = $this->option('since');

        // Resolve the --since option into a DateTimeInterface
        $since = null;
        if ($sinceOption) {
            try {
                $since = new \DateTime($sinceOption);
            } catch (\Exception $e) {
                $this->error("Invalid --since value: {$sinceOption}");
                return self::FAILURE;
            }
        }

        // Resolve the optional --provider option
        $provider = null;
        if ($providerUuid) {
            $provider = Providers::where('uuid', $providerUuid)
                ->where('provider_type', 'n8n')
                ->first();

            if (!$provider) {
                $this->error("No n8n provider found with UUID: {$providerUuid}");
                return self::FAILURE;
            }
        }

        $providerLabel = $provider
            ? ($provider->name ?? $providerUuid)
            : 'all n8n providers';

        if ($syncWorkflows) {
            $this->info("Syncing workflows for {$providerLabel}...");

            if ($runSync) {
                (new SyncN8NWorkflowsJob($provider))->handle();
                $this->info('Workflow sync completed.');
            } else {
                SyncN8NWorkflowsJob::dispatch($provider);
                $this->info('Workflow sync job dispatched to queue.');
            }
        }

        if ($syncExecutions) {
            $this->info("Syncing executions for {$providerLabel}...");

            if ($runSync) {
                (new SyncN8NExecutionsJob($provider, $since))->handle();
                $this->info('Execution sync completed.');
            } else {
                SyncN8NExecutionsJob::dispatch($provider, $since);
                $this->info('Execution sync job dispatched to queue.');
            }
        }

        return self::SUCCESS;
    }
}
