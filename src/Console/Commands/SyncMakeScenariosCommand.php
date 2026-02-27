<?php

namespace NextDeveloper\IPAAS\Console\Commands;

use Illuminate\Console\Command;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\IPAAS\Jobs\Make\SyncMakeScenariosJob;

/**
 * SyncMakeScenariosCommand
 *
 * Dedicated command for syncing Make.com scenarios into ipaas_workflows.
 *
 * Examples:
 *   php artisan ipaas:sync-make-scenarios
 *   php artisan ipaas:sync-make-scenarios --provider=<uuid>
 *   php artisan ipaas:sync-make-scenarios --sync
 */
class SyncMakeScenariosCommand extends Command
{
    protected $signature = 'ipaas:sync-make-scenarios
        {--provider= : UUID of a single make provider to sync (syncs all if omitted)}
        {--sync      : Run synchronously in this process instead of dispatching to the queue}';

    protected $description = 'Sync Make.com scenarios for all (or a specific) registered make provider';

    public function handle(): int
    {
        UserHelper::setAdminAsCurrentUser();

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

        $label = $provider ? ($provider->name ?? $providerUuid) : 'all make providers';

        $this->info("Syncing scenarios for {$label}...");

        if ($this->option('sync')) {
            try {
                (new SyncMakeScenariosJob($provider))->handle();
                $this->info('Scenario sync completed.');
            } catch (\Throwable $e) {
                $this->error('Scenario sync failed: ' . $e->getMessage());
                return self::FAILURE;
            }
        } else {
            SyncMakeScenariosJob::dispatch($provider);
            $this->info('Scenario sync job dispatched to the ipaas-sync queue.');
        }

        return self::SUCCESS;
    }
}
