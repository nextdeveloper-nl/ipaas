<?php

namespace NextDeveloper\IPAAS\Http\Requests\WorkflowExecutions;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class WorkflowExecutionsUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'ipaas_workflow_id' => 'nullable|exists:ipaas_workflows,uuid|uuid',
        'ipaas_provider_id' => 'nullable|exists:ipaas_providers,uuid|uuid',
        'external_execution_id' => 'nullable|string|exists:external_executions,uuid|uuid',
        'status' => 'nullable|string',
        'trigger_mode' => 'nullable|string',
        'started_at' => 'nullable|date',
        'finished_at' => 'nullable|date',
        'duration_ms' => 'nullable|integer',
        'error_message' => 'nullable|string',
        'error_node' => 'nullable|string',
        'error_stack' => 'nullable|string',
        'retry_of_execution_id' => 'nullable|exists:retry_of_executions,uuid|uuid',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}