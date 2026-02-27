<?php

namespace NextDeveloper\IPAAS\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
        

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class WorkflowDailyStatsQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;

    public function totalExecutions($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('total_executions', $operator, $value);
    }

        //  This is an alias function of totalExecutions
    public function total_executions($value)
    {
        return $this->totalExecutions($value);
    }
    
    public function successCount($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('success_count', $operator, $value);
    }

        //  This is an alias function of successCount
    public function success_count($value)
    {
        return $this->successCount($value);
    }
    
    public function errorCount($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('error_count', $operator, $value);
    }

        //  This is an alias function of errorCount
    public function error_count($value)
    {
        return $this->errorCount($value);
    }
    
    public function canceledCount($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('canceled_count', $operator, $value);
    }

        //  This is an alias function of canceledCount
    public function canceled_count($value)
    {
        return $this->canceledCount($value);
    }
    
    public function avgDurationMs($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('avg_duration_ms', $operator, $value);
    }

        //  This is an alias function of avgDurationMs
    public function avg_duration_ms($value)
    {
        return $this->avgDurationMs($value);
    }
    
    public function maxDurationMs($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('max_duration_ms', $operator, $value);
    }

        //  This is an alias function of maxDurationMs
    public function max_duration_ms($value)
    {
        return $this->maxDurationMs($value);
    }
    
    public function statDateStart($date)
    {
        return $this->builder->where('stat_date', '>=', $date);
    }

    public function statDateEnd($date)
    {
        return $this->builder->where('stat_date', '<=', $date);
    }

    //  This is an alias function of statDate
    public function stat_date_start($value)
    {
        return $this->statDateStart($value);
    }

    //  This is an alias function of statDate
    public function stat_date_end($value)
    {
        return $this->statDateEnd($value);
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
    
    public function iamAccountId($value)
    {
            $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::where('uuid', $value)->first();

        if($iamAccount) {
            return $this->builder->where('iam_account_id', '=', $iamAccount->id);
        }
    }

    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
