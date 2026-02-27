<?php

namespace NextDeveloper\IPAAS\Services;

use NextDeveloper\IAM\Database\Models\Roles;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\IAM\Services\RolesService;

class IntegrationPlatformService
{
    public static function enable() {
        RolesService::assignUserToRole(
            user: UserHelper::me(),
            role: Roles::withoutGlobalScopes()->where('name', 'integration-platform-user')->first()
        );
    }
}
