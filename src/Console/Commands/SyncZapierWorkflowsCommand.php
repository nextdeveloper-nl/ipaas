<?php

namespace NextDeveloper\IPAAS\Console\Commands;

use Illuminate\Console\Command;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\IPAAS\Jobs\Zapier\SyncZapierWorkflowsJob;

/**
 * SyncZapierWorkflowsCommand
 *
 * Dedicated command for syncing Zapier zaps into ipaas_workflows.
 *
 * Note: Zapier does not expose execution history via the public API,
 * so there is no corresponding executions command.
 *
 * Examples:
 *   php artisan ipaas:sync-zapier-workflows
 *   php artisan ipaas:sync-zapier-workflows --provider=<uuid>
 *   php artisan ipaas:sync-zapier-workflows --sync
 */
class SyncZapierWorkflowsCommand extends Command
{
    protected $signature = 'ipaas:sync-zapier-workflows
        {--provider= : UUID of a single zapier provider to sync (syncs all if omitted)}
        {--sync      : Run synchronously in this process instead of dispatching to the queue}';

    protected $description = 'Sync Zapier zaps for all (or a specific) registered zapier provider';

    public function handle(): int
    {
        UserHelper::setAdminAsCurrentUser();

        $provider = null;
        if ($providerUuid = $this->option('provider')) {
            $provider = Providers::where('uuid', $providerUuid)
                ->where('provider_type', 'zapier')
                ->first();

            if (!$provider) {
                $this->error("No zapier provider found with UUID: {$providerUuid}");
                return self::FAILURE;
            }
        }

        $label = $provider ? ($provider->name ?? $providerUuid) : 'all zapier providers';

        $this->info("Syncing workflows for {$label}...");

        if ($this->option('sync')) {
            try {
                (new SyncZapierWorkflowsJob($provider))->handle();
                $this->info('Workflow sync completed.');
            } catch (\Throwable $e) {
                $this->error('Workflow sync failed: ' . $e->getMessage());
                return self::FAILURE;
            }
        } else {
            SyncZapierWorkflowsJob::dispatch($provider);
            $this->info('Workflow sync job dispatched to the ipaas-sync queue.');
        }

        return self::SUCCESS;
    }
}
