<?php

namespace NextDeveloper\IPAAS\Authorization\Roles;

use Exceptions\MustHaveNIN;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use NextDeveloper\Commons\Helpers\DatabaseHelper;
use NextDeveloper\IAAS\Authorization\Rules\ServiceAvailability\TurkishMustHaveNIN;
use NextDeveloper\IAM\Authorization\Roles\AbstractRole;
use NextDeveloper\IAM\Authorization\Roles\IAuthorizationRole;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Helpers\UserHelper;

class IntegrationPlatformUser extends AbstractRole implements IAuthorizationRole
{
    public const NAME = 'integration-platform-user';

    public const LEVEL = 150;

    public const DESCRIPTION = 'This is the integration platform user role.';

    public const DB_PREFIX = 'ipaas';

    /**
     * Applies basic member role sql for Eloquent
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('iam_account_id', UserHelper::currentAccount()->id);
    }

    public function checkPrivileges(Users $users = null)
    {
        //return UserHelper::hasRole(self::NAME, $users);
    }

    public function checkRules(Users $users): bool
    {
        return true;
    }

    public function getModule()
    {
        return 'iaas';
    }

    public function checkUpdatePolicy(Model $model, Users $user): bool
    {
        return true;
    }

    public function allowedOperations(): array
    {
        return [
            'ipaas_account_stats:read',
            'ipaas_account_provider_overviews:read',
            'ipaas_platform_health_perspective:read',
            'ipaas_execution_daily_stats:read',
            'ipaas_workflow_executions_perspective:read',

            'ipaas_accounts:read',
            'ipaas_accounts:create',
            'ipaas_accounts:update',
            'ipaas_providers:read',
            'ipaas_providers:create',
            'ipaas_providers:update',
            'ipaas_providers:delete',
            'ipaas_workflow_daily_stats:read',
            'ipaas_workflow_daily_stats:create',
            'ipaas_workflow_executions:read',
            'ipaas_workflow_executions:create',
            'ipaas_workflow_executions:update',
            'ipaas_workflow_executions:delete',
            'ipaas_workflows:read',
            'ipaas_workflows:create',
            'ipaas_workflows:update',
            'ipaas_workflows:delete'
        ];
    }

    public function getLevel(): int
    {
        return self::LEVEL;
    }

    public function getDescription(): string
    {
        return self::DESCRIPTION;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function canBeApplied($column)
    {
        if (self::DB_PREFIX === '*') {
            return true;
        }

        if (Str::startsWith($column, self::DB_PREFIX)) {
            return true;
        }

        return false;
    }

    public function getDbPrefix()
    {
        return self::DB_PREFIX;
    }
}
