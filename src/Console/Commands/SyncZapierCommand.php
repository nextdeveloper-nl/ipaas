<?php

namespace NextDeveloper\IPAAS\Console\Commands;

use Illuminate\Console\Command;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\IPAAS\Jobs\Zapier\SyncZapierWorkflowsJob;

/**
 * SyncZapierCommand
 *
 * Artisan command that dispatches the Zapier sync jobs.
 *
 * Note: Zapier does not expose execution history via the public API.
 * Only zap (workflow) sync is available.
 *
 * Examples:
 *   php artisan ipaas:sync-zapier
 *   php artisan ipaas:sync-zapier --provider=<uuid>
 *   php artisan ipaas:sync-zapier --sync          (run inline, no queue)
 */
class SyncZapierCommand extends Command
{
    protected $signature = 'ipaas:sync-zapier
        {--provider= : UUID of a single zapier provider to sync (syncs all if omitted)}
        {--sync      : Run synchronously in this process instead of dispatching to the queue}';

    protected $description = 'Sync Zapier zaps for all (or a specific) registered zapier provider';

    public function handle(): int
    {
        UserHelper::setAdminAsCurrentUser();

        $runSync      = (bool) $this->option('sync');
        $providerUuid = $this->option('provider');

        $provider = null;
        if ($providerUuid) {
            $provider = Providers::where('uuid', $providerUuid)
                ->where('provider_type', 'zapier')
                ->first();

            if (!$provider) {
                $this->error("No zapier provider found with UUID: {$providerUuid}");
                return self::FAILURE;
            }
        }

        $providerLabel = $provider
            ? ($provider->name ?? $providerUuid)
            : 'all zapier providers';

        $this->info("Syncing workflows for {$providerLabel}...");

        if ($runSync) {
            try {
                (new SyncZapierWorkflowsJob($provider))->handle();
                $this->info('Workflow sync completed.');
            } catch (\Throwable $e) {
                $this->error('Workflow sync failed: ' . $e->getMessage());
                return self::FAILURE;
            }
        } else {
            SyncZapierWorkflowsJob::dispatch($provider);
            $this->info('Workflow sync job dispatched to queue.');
        }

        $this->line('');
        $this->comment('Note: Zapier execution history is not available via the public API.');

        return self::SUCCESS;
    }
}
