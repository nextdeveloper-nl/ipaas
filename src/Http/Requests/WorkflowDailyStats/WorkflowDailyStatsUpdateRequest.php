<?php

namespace NextDeveloper\IPAAS\Http\Requests\WorkflowDailyStats;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class WorkflowDailyStatsUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'stat_date' => 'nullable|date',
        'ipaas_workflow_id' => 'nullable|exists:ipaas_workflows,uuid|uuid',
        'total_executions' => 'integer',
        'success_count' => 'integer',
        'error_count' => 'integer',
        'canceled_count' => 'integer',
        'avg_duration_ms' => 'integer',
        'max_duration_ms' => 'integer',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}