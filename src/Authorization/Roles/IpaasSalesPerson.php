<?php

namespace NextDeveloper\IPAAS\Authorization\Roles;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use NextDeveloper\Commons\Helpers\DatabaseHelper;
use NextDeveloper\CRM\Database\Models\AccountManagers;
use NextDeveloper\IAM\Authorization\Roles\AbstractRole;
use NextDeveloper\IAM\Authorization\Roles\IAuthorizationRole;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Helpers\UserHelper;

class IpaasSalesPerson extends AbstractRole implements IAuthorizationRole
{
    public const NAME = 'ipaas-sales-person';

    public const LEVEL = 140;

    public const DESCRIPTION = 'iPaaS sales person can read all iPaaS records across tenants and update iPaaS account settings to support customers.';

    public const DB_PREFIX = 'ipaas';

    public function apply(Builder $builder, Model $model)
    {
        // Mirror /crm/accounts-perspective visibility: sales-admin sees everything.
        if (UserHelper::hasRole('sales-admin') || UserHelper::hasRole('sales-manager-admin')) {
            return;
        }

        $accountId = UserHelper::currentAccount()->id;

        $managedIamAccountsSql = '(
            select ca.iam_account_id from crm_accounts ca
            join crm_account_managers cam on cam.crm_account_id = ca.id
            where cam.iam_account_id = ' . $accountId . '
        )';

        if ($model->getTable() === 'ipaas_accounts') {
            $builder->whereRaw('iam_account_id IN ' . $managedIamAccountsSql);

            return;
        }

        if (DatabaseHelper::isColumnExists($model->getTable(), 'iam_account_id')) {
            $builder->whereRaw('iam_account_id IN ' . $managedIamAccountsSql);
        }
    }

    public function checkUpdatePolicy(Model $model, Users $user): bool
    {
        if (!in_array($model->getTable() . ':update', $this->allowedOperations(), true)) {
            return false;
        }

        if (!isset($model->iam_account_id)) {
            return false;
        }

        return AccountManagers::withoutGlobalScopes()
            ->where('iam_account_id', UserHelper::currentAccount()->id)
            ->whereIn('crm_account_id', function ($q) use ($model) {
                $q->select('id')
                    ->from('crm_accounts')
                    ->where('iam_account_id', $model->iam_account_id);
            })
            ->exists();
    }

    public function checkDeletePolicy(Model $model, Users $user): bool
    {
        return false;
    }

    public function getModule()
    {
        return 'ipaas';
    }

    public function allowedOperations(): array
    {
        return [
            'ipaas_accounts:read',
            'ipaas_accounts:create',
            'ipaas_accounts:update',
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

    public function checkRules(Users $users): bool
    {
        return true;
    }
}
