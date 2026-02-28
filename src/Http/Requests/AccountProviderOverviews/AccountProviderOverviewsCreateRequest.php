<?php

namespace NextDeveloper\IPAAS\Http\Requests\AccountProviderOverviews;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class AccountProviderOverviewsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'ipaas_provider_id' => 'nullable|exists:ipaas_providers,uuid|uuid',
        'ipaas_provider_name' => 'nullable|string',
        'provider_type' => 'nullable|string',
        'is_default_wap' => 'nullable|boolean',
        'base_url' => 'nullable|string',
        'total_workflows' => 'nullable|integer',
        'total_automation_engines' => 'nullable|integer',
        'executions_today' => 'nullable|integer',
        'success_today' => 'nullable|integer',
        'errors_today' => 'nullable|integer',
        'success_rate_today' => 'nullable',
        'provider_created_at' => 'nullable|date',
        'provider_updated_at' => 'nullable|date',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}