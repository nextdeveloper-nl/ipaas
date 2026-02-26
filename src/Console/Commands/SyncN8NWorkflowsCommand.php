<?php

namespace NextDeveloper\IPAAS\Console\Commands;

use Illuminate\Console\Command;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\IPAAS\Jobs\N8N\SyncN8NWorkflowsJob;

/**
 * SyncN8NWorkflowsCommand
 *
 * Dedicated command for syncing N8N workflows into ipaas_workflows.
 *
 * Examples:
 *   php artisan ipaas:sync-n8n-workflows
 *   php artisan ipaas:sync-n8n-workflows --provider=<uuid>
 *   php artisan ipaas:sync-n8n-workflows --sync
 */
class SyncN8NWorkflowsCommand extends Command
{
    protected $signature = 'ipaas:sync-n8n-workflows
        {--provider= : UUID of a single n8n provider to sync (syncs all if omitted)}
        {--sync      : Run synchronously in this process instead of dispatching to the queue}';

    protected $description = 'Sync N8N workflows for all (or a specific) registered n8n provider';

    public function handle(): int
    {
        UserHelper::setAdminAsCurrentUser();

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

        $this->info("Syncing workflows for {$label}...");

        if ($this->option('sync')) {
            try {
                (new SyncN8NWorkflowsJob($provider))->handle();
                $this->info('Workflow sync completed.');
            } catch (\Throwable $e) {
                $this->error('Workflow sync failed: ' . $e->getMessage());
                return self::FAILURE;
            }
        } else {
            SyncN8NWorkflowsJob::dispatch($provider);
            $this->info('Workflow sync job dispatched to the ipaas-sync queue.');
        }

        return self::SUCCESS;
    }
}
