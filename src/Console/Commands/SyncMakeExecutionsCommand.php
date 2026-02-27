<?php

namespace NextDeveloper\IPAAS\Console\Commands;

use Illuminate\Console\Command;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\IPAAS\Jobs\Make\SyncMakeExecutionsJob;

/**
 * SyncMakeExecutionsCommand
 *
 * Dedicated command for syncing Make.com execution logs into ipaas_workflow_executions.
 *
 * Examples:
 *   php artisan ipaas:sync-make-executions
 *   php artisan ipaas:sync-make-executions --provider=<uuid>
 *   php artisan ipaas:sync-make-executions --since="7 days ago"
 *   php artisan ipaas:sync-make-executions --since="2026-01-01"
 *   php artisan ipaas:sync-make-executions --sync
 */
class SyncMakeExecutionsCommand extends Command
{
    protected $signature = 'ipaas:sync-make-executions
        {--provider= : UUID of a single make provider to sync (syncs all if omitted)}
        {--since=    : Pull executions started at or after this time, e.g. "7 days ago" or "2026-01-01" (defaults to 24 h ago)}
        {--sync      : Run synchronously in this process instead of dispatching to the queue}';

    protected $description = 'Sync Make.com execution logs for all (or a specific) registered make provider';

    public function handle(): int
    {
        UserHelper::setAdminAsCurrentUser();

        $since = null;
        if ($sinceOption = $this->option('since')) {
            try {
                $since = new \DateTime($sinceOption);
            } catch (\Exception $e) {
                $this->error("Invalid --since value: {$sinceOption}");
                return self::FAILURE;
            }
        }

        $provider = null;
        if ($providerUuid = $this->option('provider')) {
            $provider = Providers::where('uuid', $providerUuid)
                ->where('provider_type', 'make')
                ->first();

            if (!$provider) {
                $this->error("No make provider found with UUID: {$providerUuid}");
                return self::FAILURE;
            }
        }

        $label  = $provider ? ($provider->name ?? $providerUuid) : 'all make providers';
        $window = $since ? $since->format('Y-m-d H:i:s') : 'last 24 hours';

        $this->info("Syncing executions for {$label} (since: {$window})...");

        if ($this->option('sync')) {
            try {
                (new SyncMakeExecutionsJob($provider, $since))->handle();
                $this->info('Execution sync completed.');
            } catch (\Throwable $e) {
                $this->error('Execution sync failed: ' . $e->getMessage());
                return self::FAILURE;
            }
        } else {
            SyncMakeExecutionsJob::dispatch($provider, $since);
            $this->info('Execution sync job dispatched to the ipaas-sync queue.');
        }

        return self::SUCCESS;
    }
}
