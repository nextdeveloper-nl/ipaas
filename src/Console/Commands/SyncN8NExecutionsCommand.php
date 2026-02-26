<?php

namespace NextDeveloper\IPAAS\Console\Commands;

use Illuminate\Console\Command;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\IPAAS\Jobs\N8N\SyncN8NExecutionsJob;

/**
 * SyncN8NExecutionsCommand
 *
 * Dedicated command for syncing N8N execution history into ipaas_workflow_executions.
 *
 * Examples:
 *   php artisan ipaas:sync-n8n-executions
 *   php artisan ipaas:sync-n8n-executions --provider=<uuid>
 *   php artisan ipaas:sync-n8n-executions --since="7 days ago"
 *   php artisan ipaas:sync-n8n-executions --since="2026-01-01"
 *   php artisan ipaas:sync-n8n-executions --sync
 */
class SyncN8NExecutionsCommand extends Command
{
    protected $signature = 'ipaas:sync-n8n-executions
        {--provider= : UUID of a single n8n provider to sync (syncs all if omitted)}
        {--since=    : Pull executions started at or after this time, e.g. "7 days ago" or "2026-01-01" (defaults to 24 h ago)}
        {--sync      : Run synchronously in this process instead of dispatching to the queue}';

    protected $description = 'Sync N8N execution history for all (or a specific) registered n8n provider';

    public function handle(): int
    {
        UserHelper::setAdminAsCurrentUser();

        // Resolve --since
        $since = null;
        if ($sinceOption = $this->option('since')) {
            try {
                $since = new \DateTime($sinceOption);
            } catch (\Exception $e) {
                $this->error("Invalid --since value: {$sinceOption}");
                return self::FAILURE;
            }
        }

        // Resolve --provider
        $provider = null;
        if ($providerUuid = $this->option('provider')) {
            $provider = Providers::where('uuid', $providerUuid)
                ->where('provider_type', 'n8n')
                ->first();

            if (!$provider) {
                $this->error("No n8n provider found with UUID: {$providerUuid}");
                return self::FAILURE;
            }
        }

        $label = $provider ? ($provider->name ?? $providerUuid) : 'all n8n providers';
        $window = $since
            ? $since->format('Y-m-d H:i:s')
            : 'last 24 hours';

        $this->info("Syncing executions for {$label} (since: {$window})...");

        if ($this->option('sync')) {
            try {
                (new SyncN8NExecutionsJob($provider, $since))->handle();
                $this->info('Execution sync completed.');
            } catch (\Throwable $e) {
                $this->error('Execution sync failed: ' . $e->getMessage());
                return self::FAILURE;
            }
        } else {
            SyncN8NExecutionsJob::dispatch($provider, $since);
            $this->info('Execution sync job dispatched to the ipaas-sync queue.');
        }

        return self::SUCCESS;
    }
}
