<?php

namespace NextDeveloper\IPAAS\Http\Requests\Workflows;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class WorkflowsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
        'description' => 'nullable|string',
        'trigger_type' => 'required|string',
        'status' => 'string',
        'current_version_id' => 'nullable|exists:current_versions,uuid|uuid',
        'ipaas_provider_id' => 'required|exists:ipaas_providers,uuid|uuid',
        'external_workflow_id' => 'required|string|exists:external_workflows,uuid|uuid',
        'last_synched_at' => 'date',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}