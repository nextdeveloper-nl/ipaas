<?php

namespace NextDeveloper\IPAAS\Http\Requests\ExecutionDailyStats;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class ExecutionDailyStatsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'stat_date' => 'nullable|date',
        'ipaas_provider_id' => 'nullable|exists:ipaas_providers,uuid|uuid',
        'provider_name' => 'nullable|string',
        'provider_type' => 'nullable|string',
        'total_executions' => 'nullable|integer',
        'success_count' => 'nullable|integer',
        'error_count' => 'nullable|integer',
        'canceled_count' => 'nullable|integer',
        'success_rate' => 'nullable',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}