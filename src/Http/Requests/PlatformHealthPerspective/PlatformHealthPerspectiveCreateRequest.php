<?php

namespace NextDeveloper\IPAAS\Http\Requests\PlatformHealthPerspective;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class PlatformHealthPerspectiveCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'provider_id' => 'nullable|exists:providers,uuid|uuid',
        'provider_name' => 'nullable|string',
        'provider_type' => 'nullable|string',
        'is_default_wap' => 'nullable|boolean',
        'active_workflows' => 'nullable|integer',
        'executions_today' => 'nullable|integer',
        'success_today' => 'nullable|integer',
        'errors_today' => 'nullable|integer',
        'running_today' => 'nullable|integer',
        'success_rate_today' => 'nullable',
        'last_execution_at' => 'nullable|date',
        'last_execution_status' => 'nullable|string',
        'health_status' => 'nullable|string',
        'provider_created_at' => 'nullable|date',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}