<?php

namespace NextDeveloper\IPAAS\Http\Requests\AccountStats;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class AccountStatsUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'total_providers' => 'nullable|integer',
        'total_workflows' => 'nullable|integer',
        'executions_today' => 'nullable|integer',
        'success_today' => 'nullable|integer',
        'errors_today' => 'nullable|integer',
        'success_rate_today' => 'nullable',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}