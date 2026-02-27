<?php

namespace NextDeveloper\IPAAS\Console\Commands;

use Illuminate\Console\Command;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\IPAAS\Jobs\Make\SyncMakeExecutionsJob;
use NextDeveloper\IPAAS\Jobs\Make\SyncMakeScenariosJob;

/**
 * SyncMakeCommand
 *
 * Artisan command that dispatches the Make.com sync jobs.
 *
 * By default both scenarios and executions are synced for every registered
 * make provider. Use flags to limit the scope.
 *
 * Examples:
 *   php artisan ipaas:sync-make
 *   php artisan ipaas:sync-make --scenarios-only
 *   php artisan ipaas:sync-make --executions-only
 *   php artisan ipaas:sync-make --provider=<uuid>
 *   php artisan ipaas:sync-make --since="7 days ago"
 *   php artisan ipaas:sync-make --sync          (run inline, no queue)
 */
class SyncMakeCommand extends Command
{
    protected $signature = 'ipaas:sync-make
        {--scenarios-only  : Only sync scenarios, skip executions}
        {--executions-only : Only sync executions, skip scenarios}
        {--provider=       : UUID of a single make provider to sync (syncs all if omitted)}
        {--since=          : Execution sync window start, e.g. "7 days ago" or "2026-01-01"}
        {--sync            : Run synchronously in this process instead of dispatching to the queue}';

    protected $description = 'Sync Make.com scenarios and/or executions for all (or a specific) registered make provider';

    public function handle(): int
    {
        UserHelper::setAdminAsCurrentUser();

        $syncScenarios  = !$this->option('executions-only');
        $syncExecutions = !$this->option('scenarios-only');
        $runSync        = (bool) $this->option('sync');
        $providerUuid   = $this->option('provider');
        $sinceOption    = $this->option('since');

        $since = null;
        if ($sinceOption) {
            try {
                $since = new \DateTime($sinceOption);
            } catch (\Exception $e) {
                $this->error("Invalid --since value: {$sinceOption}");
                return self::FAILURE;
            }
        }

        $provider = null;
        if ($providerUuid) {
            $provider = Providers::where('uuid', $providerUuid)
                ->where('provider_type', 'make')
                ->first();

            if (!$provider) {
                $this->error("No make provider found with UUID: {$providerUuid}");
                return self::FAILURE;
            }
        }

        $providerLabel = $provider
            ? ($provider->name ?? $providerUuid)
            : 'all make providers';

        if ($syncScenarios) {
            $this->info("Syncing scenarios for {$providerLabel}...");

            if ($runSync) {
                try {
                    (new SyncMakeScenariosJob($provider))->handle();
                    $this->info('Scenario sync completed.');
                } catch (\Throwable $e) {
                    $this->error('Scenario sync failed: ' . $e->getMessage());
                    return self::FAILURE;
                }
            } else {
                SyncMakeScenariosJob::dispatch($provider);
                $this->info('Scenario sync job dispatched to queue.');
            }
        }

        if ($syncExecutions) {
            $this->info("Syncing executions for {$providerLabel}...");

            if ($runSync) {
                try {
                    (new SyncMakeExecutionsJob($provider, $since))->handle();
                    $this->info('Execution sync completed.');
                } catch (\Throwable $e) {
                    $this->error('Execution sync failed: ' . $e->getMessage());
                    return self::FAILURE;
                }
            } else {
                SyncMakeExecutionsJob::dispatch($provider, $since);
                $this->info('Execution sync job dispatched to queue.');
            }
        }

        return self::SUCCESS;
    }
}
