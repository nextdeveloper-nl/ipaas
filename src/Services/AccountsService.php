<?php

namespace NextDeveloper\IPAAS\Services;

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
}
