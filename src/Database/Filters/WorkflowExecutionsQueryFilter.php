<?php

namespace NextDeveloper\IPAAS\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
                    

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class WorkflowExecutionsQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function externalExecutionId($value)
    {
        return $this->builder->where('external_execution_id', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of externalExecutionId
    public function external_execution_id($value)
    {
        return $this->externalExecutionId($value);
    }
        
    public function status($value)
    {
        return $this->builder->where('status', 'ilike', '%' . $value . '%');
    }

        
    public function triggerMode($value)
    {
        return $this->builder->where('trigger_mode', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of triggerMode
    public function trigger_mode($value)
    {
        return $this->triggerMode($value);
    }
        
    public function errorMessage($value)
    {
        return $this->builder->where('error_message', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of errorMessage
    public function error_message($value)
    {
        return $this->errorMessage($value);
    }
        
    public function errorNode($value)
    {
        return $this->builder->where('error_node', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of errorNode
    public function error_node($value)
    {
        return $this->errorNode($value);
    }
        
    public function errorStack($value)
    {
        return $this->builder->where('error_stack', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of errorStack
    public function error_stack($value)
    {
        return $this->errorStack($value);
    }
    
    public function durationMs($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('duration_ms', $operator, $value);
    }

        //  This is an alias function of durationMs
    public function duration_ms($value)
    {
        return $this->durationMs($value);
    }
    
    public function startedAtStart($date)
    {
        return $this->builder->where('started_at', '>=', $date);
    }

    public function startedAtEnd($date)
    {
        return $this->builder->where('started_at', '<=', $date);
    }

    //  This is an alias function of startedAt
    public function started_at_start($value)
    {
        return $this->startedAtStart($value);
    }

    //  This is an alias function of startedAt
    public function started_at_end($value)
    {
        return $this->startedAtEnd($value);
    }

    public function finishedAtStart($date)
    {
        return $this->builder->where('finished_at', '>=', $date);
    }

    public function finishedAtEnd($date)
    {
        return $this->builder->where('finished_at', '<=', $date);
    }

    //  This is an alias function of finishedAt
    public function finished_at_start($value)
    {
        return $this->finishedAtStart($value);
    }

    //  This is an alias function of finishedAt
    public function finished_at_end($value)
    {
        return $this->finishedAtEnd($value);
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

    public function ipaasWorkflowId($value)
    {
            $ipaasWorkflow = \NextDeveloper\IPAAS\Database\Models\Workflows::where('uuid', $value)->first();

        if($ipaasWorkflow) {
            return $this->builder->where('ipaas_workflow_id', '=', $ipaasWorkflow->id);
        }
    }

        //  This is an alias function of ipaasWorkflow
    public function ipaas_workflow_id($value)
    {
        return $this->ipaasWorkflow($value);
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
    
    public function iamAccountId($value)
    {
            $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::where('uuid', $value)->first();

        if($iamAccount) {
            return $this->builder->where('iam_account_id', '=', $iamAccount->id);
        }
    }

    
    public function externalExecutionId($value)
    {
            $externalExecution = \NextDeveloper\\Database\Models\ExternalExecutions::where('uuid', $value)->first();

        if($externalExecution) {
            return $this->builder->where('external_execution_id', '=', $externalExecution->id);
        }
    }

        //  This is an alias function of externalExecution
    public function external_execution_id($value)
    {
        return $this->externalExecution($value);
    }
    
    public function retryOfExecutionId($value)
    {
            $retryOfExecution = \NextDeveloper\\Database\Models\RetryOfExecutions::where('uuid', $value)->first();

        if($retryOfExecution) {
            return $this->builder->where('retry_of_execution_id', '=', $retryOfExecution->id);
        }
    }

        //  This is an alias function of retryOfExecution
    public function retry_of_execution_id($value)
    {
        return $this->retryOfExecution($value);
    }
    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE











}
