<?php

namespace NextDeveloper\IPAAS\Services;

use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IPAAS\Database\Models\Accounts;
use NextDeveloper\IPAAS\Services\AbstractServices\AbstractAccountsService;

/**
 * This class is responsible from managing the data for Accounts
 *
 * Class AccountsService.
 *
 * @package NextDeveloper\IPAAS\Database\Models
 */
class AccountsService extends AbstractAccountsService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
    public static function create(array $data) : Accounts
    {
        return parent::create($data);
    }

    public static function suspend(Accounts $account): Accounts
    {
        $account->update([
            'is_service_enabled' => false,
        ]);

        return $account->fresh();
    }

    /**
     * Suspends the IPAAS account belonging to the given IAM account, if one
     * exists. IPAAS accounts are opt-in, so a customer without one is a
     * no-op here, not an error.
     */
    public static function suspendWithIamAccount(\NextDeveloper\IAM\Database\Models\Accounts $account): ?Accounts
    {
        $ipaasAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('iam_account_id', $account->id)
            ->first();

        if (!$ipaasAccount) {
            return null;
        }

        return self::suspend($ipaasAccount);
    }
}
