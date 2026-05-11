<?php

namespace NextDeveloper\IPAAS\Services;

use Illuminate\Support\Facades\Log;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\IPAAS\Database\Filters\WorkflowsQueryFilter;
use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\IPAAS\Integrations\WapIntegrationFactory;
use NextDeveloper\IPAAS\Services\AbstractServices\AbstractWorkflowsService;

/**
 * This class is responsible from managing the data for Workflows
 *
 * Class WorkflowsService.
 *
 * @package NextDeveloper\IPAAS\Database\Models
 */
class WorkflowsService extends AbstractWorkflowsService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    public static function get(WorkflowsQueryFilter $filter = null, array $params = [])
    {
        // Pull fresh workflows from every n8n provider belonging to the current account
        // before querying the local DB, so the caller always sees up-to-date data.
        $account = UserHelper::currentAccount();

        if ($account) {
            $providers = Providers::withoutGlobalScope(AuthorizationScope::class)
                ->where('iam_account_id', $account->id)
                ->get();

            foreach ($providers as $provider) {
                try {
                    $integration = WapIntegrationFactory::make($provider);
                    $integration->syncWorkflows();
                } catch (\Throwable $e) {
                    Log::warning('[IPAAS] WorkflowsService::get — sync failed for provider ' . $provider->id . ': ' . $e->getMessage());
                }
            }
        }

        return parent::get($filter, $params);
    }
}
