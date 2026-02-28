<?php

namespace NextDeveloper\IPAAS\Http\Requests\WorkflowExecutionsPerspective;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class WorkflowExecutionsPerspectiveUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'nullable|string',
        'trigger_mode' => 'nullable|string',
        'started_at' => 'nullable|date',
        'finished_at' => 'nullable|date',
        'duration_ms' => 'nullable|integer',
        'error_message' => 'nullable|string',
        'error_node' => 'nullable|string',
        'ipaas_workflow_id' => 'nullable|exists:ipaas_workflows,uuid|uuid',
        'workflow_name' => 'nullable|string',
        'ipaas_provider_id' => 'nullable|exists:ipaas_providers,uuid|uuid',
        'provider_name' => 'nullable|string',
        'provider_type' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}