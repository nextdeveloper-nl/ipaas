<?php

namespace NextDeveloper\IPAAS\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
                    

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class WorkflowsQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function name($value)
    {
        return $this->builder->where('name', 'ilike', '%' . $value . '%');
    }

        
    public function description($value)
    {
        return $this->builder->where('description', 'ilike', '%' . $value . '%');
    }

        
    public function triggerType($value)
    {
        return $this->builder->where('trigger_type', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of triggerType
    public function trigger_type($value)
    {
        return $this->triggerType($value);
    }
        
    public function status($value)
    {
        return $this->builder->where('status', 'ilike', '%' . $value . '%');
    }

        
    public function externalWorkflowId($value)
    {
        return $this->builder->where('external_workflow_id', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of externalWorkflowId
    public function external_workflow_id($value)
    {
        return $this->externalWorkflowId($value);
    }
    
    public function lastSynchedAtStart($date)
    {
        return $this->builder->where('last_synched_at', '>=', $date);
    }

    public function lastSynchedAtEnd($date)
    {
        return $this->builder->where('last_synched_at', '<=', $date);
    }

    //  This is an alias function of lastSynchedAt
    public function last_synched_at_start($value)
    {
        return $this->lastSynchedAtStart($value);
    }

    //  This is an alias function of lastSynchedAt
    public function last_synched_at_end($value)
    {
        return $this->lastSynchedAtEnd($value);
    }

    public function createdAtStart($date)
    {
        return $this->builder->where('created_at', '>=', $date);
    }

    public function createdAtEnd($date)
    {
        return $this->builder->where('created_at', '<=', $date);
    }

    //  This is an alias function of createdAt
    public function created_at_start($value)
    {
        return $this->createdAtStart($value);
    }

    //  This is an alias function of createdAt
    public function created_at_end($value)
    {
        return $this->createdAtEnd($value);
    }

    public function updatedAtStart($date)
    {
        return $this->builder->where('updated_at', '>=', $date);
    }

    public function updatedAtEnd($date)
    {
        return $this->builder->where('updated_at', '<=', $date);
    }

    //  This is an alias function of updatedAt
    public function updated_at_start($value)
    {
        return $this->updatedAtStart($value);
    }

    //  This is an alias function of updatedAt
    public function updated_at_end($value)
    {
        return $this->updatedAtEnd($value);
    }

    public function deletedAtStart($date)
    {
        return $this->builder->where('deleted_at', '>=', $date);
    }

    public function deletedAtEnd($date)
    {
        return $this->builder->where('deleted_at', '<=', $date);
    }

    //  This is an alias function of deletedAt
    public function deleted_at_start($value)
    {
        return $this->deletedAtStart($value);
    }

    //  This is an alias function of deletedAt
    public function deleted_at_end($value)
    {
        return $this->deletedAtEnd($value);
    }

    public function currentVersionId($value)
    {
            $currentVersion = \NextDeveloper\\Database\Models\CurrentVersions::where('uuid', $value)->first();

        if($currentVersion) {
            return $this->builder->where('current_version_id', '=', $currentVersion->id);
        }
    }

        //  This is an alias function of currentVersion
    public function current_version_id($value)
    {
        return $this->currentVersion($value);
    }
    
    public function ipaasProviderId($value)
    {
            $ipaasProvider = \NextDeveloper\IPAAS\Database\Models\Providers::where('uuid', $value)->first();

        if($ipaasProvider) {
            return $this->builder->where('ipaas_provider_id', '=', $ipaasProvider->id);
        }
    }

        //  This is an alias function of ipaasProvider
    public function ipaas_provider_id($value)
    {
        return $this->ipaasProvider($value);
    }
    
    public function externalWorkflowId($value)
    {
            $externalWorkflow = \NextDeveloper\\Database\Models\ExternalWorkflows::where('uuid', $value)->first();

        if($externalWorkflow) {
            return $this->builder->where('external_workflow_id', '=', $externalWorkflow->id);
        }
    }

        //  This is an alias function of externalWorkflow
    public function external_workflow_id($value)
    {
        return $this->externalWorkflow($value);
    }
    
    public function iamAccountId($value)
    {
            $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::where('uuid', $value)->first();

        if($iamAccount) {
            return $this->builder->where('iam_account_id', '=', $iamAccount->id);
        }
    }

    
    public function iamUserId($value)
    {
            $iamUser = \NextDeveloper\IAM\Database\Models\Users::where('uuid', $value)->first();

        if($iamUser) {
            return $this->builder->where('iam_user_id', '=', $iamUser->id);
        }
    }

    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE


}
