<?php

namespace NextDeveloper\IPAAS\Services;

use NextDeveloper\Commons\Exceptions\NotAllowedException;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\IPAAS\Services\AbstractServices\AbstractProvidersService;

/**
 * This class is responsible from managing the data for Providers
 *
 * Class ProvidersService.
 *
 * @package NextDeveloper\IPAAS\Database\Models
 */
class ProvidersService extends AbstractProvidersService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    /**
     * @throws NotAllowedException
     */
    public static function create(array $data)
    {
        $baseUrl  = $data['base_url'] ?? null;
        $apiToken = $data['api_token'] ?? null;

        if ($baseUrl && $apiToken) {
            $duplicate = Providers::withoutGlobalScope(AuthorizationScope::class)
                ->where('base_url', $baseUrl)
                ->where('api_token', $apiToken)
                ->first();

            if ($duplicate) {
                throw new NotAllowedException('A provider with the same base URL and API token already exists.');
            }
        }

        return parent::create($data);
    }
}