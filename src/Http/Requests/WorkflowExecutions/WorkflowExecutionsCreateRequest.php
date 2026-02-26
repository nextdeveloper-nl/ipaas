<?php

namespace NextDeveloper\IPAAS\Http\Requests\WorkflowExecutions;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class WorkflowExecutionsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'ipaas_workflow_id' => 'required|exists:ipaas_workflows,uuid|uuid',
        'ipaas_provider_id' => 'required|exists:ipaas_providers,uuid|uuid',
        'external_execution_id' => 'required|string|exists:external_executions,uuid|uuid',
        'status' => 'required|string',
        'trigger_mode' => 'required|string',
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