<?php

namespace NextDeveloper\IPAAS\Http\Requests\Providers;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class ProvidersUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name'                    => 'nullable|string',
            'description'             => 'nullable|string',
            'provider_type'           => 'nullable|string',
            'is_default_wap'          => 'boolean',
            'iaas_virtual_machine_id' => 'nullable|exists:iaas_virtual_machines,uuid|uuid',
            'base_url'                => 'nullable|url',
            'api_token'               => 'nullable|string',
            'api_secret'              => 'nullable|string',
            'external_account_id'     => 'nullable|string',
            'region'                  => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}