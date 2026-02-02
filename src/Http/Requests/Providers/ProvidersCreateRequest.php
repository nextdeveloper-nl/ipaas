<?php

namespace NextDeveloper\IPAAS\Http\Requests\Providers;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class ProvidersCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
        'description' => 'nullable|string',
        'provider_type' => 'string',
        'is_default_wap' => 'boolean',
        'iaas_virtual_machine_id' => 'nullable|exists:iaas_virtual_machines,uuid|uuid',
        'external_account_id' => 'nullable|string|exists:external_accounts,uuid|uuid',
        'region' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}