<?php

namespace NextDeveloper\IPAAS\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
        

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class PlatformHealthPerspectiveQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function providerName($value)
    {
        return $this->builder->where('provider_name', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of providerName
    public function provider_name($value)
    {
        return $this->providerName($value);
    }
        
    public function providerType($value)
    {
        return $this->builder->where('provider_type', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of providerType
    public function provider_type($value)
    {
        return $this->providerType($value);
    }
        
    public function lastExecutionStatus($value)
    {
        return $this->builder->where('last_execution_status', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of lastExecutionStatus
    public function last_execution_status($value)
    {
        return $this->lastExecutionStatus($value);
    }
        
    public function healthStatus($value)
    {
        return $this->builder->where('health_status', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of healthStatus
    public function health_status($value)
    {
        return $this->healthStatus($value);
    }
    
    public function activeWorkflows($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('active_workflows', $operator, $value);
    }

        //  This is an alias function of activeWorkflows
    public function active_workflows($value)
    {
        return $this->activeWorkflows($value);
    }
    
    public function executionsToday($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('executions_today', $operator, $value);
    }

        //  This is an alias function of executionsToday
    public function executions_today($value)
    {
        return $this->executionsToday($value);
    }
    
    public function successToday($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('success_today', $operator, $value);
    }

        //  This is an alias function of successToday
    public function success_today($value)
    {
        return $this->successToday($value);
    }
    
    public function errorsToday($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('errors_today', $operator, $value);
    }

        //  This is an alias function of errorsToday
    public function errors_today($value)
    {
        return $this->errorsToday($value);
    }
    
    public function runningToday($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('running_today', $operator, $value);
    }

        //  This is an alias function of runningToday
    public function running_today($value)
    {
        return $this->runningToday($value);
    }
    
    public function isDefaultWap($value)
    {
        return $this->builder->where('is_default_wap', $value);
    }

        //  This is an alias function of isDefaultWap
    public function is_default_wap($value)
    {
        return $this->isDefaultWap($value);
    }
     
    public function lastExecutionAtStart($date)
    {
        return $this->builder->where('last_execution_at', '>=', $date);
    }

    public function lastExecutionAtEnd($date)
    {
        return $this->builder->where('last_execution_at', '<=', $date);
    }

    //  This is an alias function of lastExecutionAt
    public function last_execution_at_start($value)
    {
        return $this->lastExecutionAtStart($value);
    }

    //  This is an alias function of lastExecutionAt
    public function last_execution_at_end($value)
    {
        return $this->lastExecutionAtEnd($value);
    }

    public function providerCreatedAtStart($date)
    {
        return $this->builder->where('provider_created_at', '>=', $date);
    }

    public function providerCreatedAtEnd($date)
    {
        return $this->builder->where('provider_created_at', '<=', $date);
    }

    //  This is an alias function of providerCreatedAt
    public function provider_created_at_start($value)
    {
        return $this->providerCreatedAtStart($value);
    }

    //  This is an alias function of providerCreatedAt
    public function provider_created_at_end($value)
    {
        return $this->providerCreatedAtEnd($value);
    }

    public function iamAccountId($value)
    {
            $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::where('uuid', $value)->first();

        if($iamAccount) {
            return $this->builder->where('iam_account_id', '=', $iamAccount->id);
        }
    }

    
    public function providerId($value)
    {
            $provider = \NextDeveloper\\Database\Models\Providers::where('uuid', $value)->first();

        if($provider) {
            return $this->builder->where('provider_id', '=', $provider->id);
        }
    }

        //  This is an alias function of provider
    public function provider_id($value)
    {
        return $this->provider($value);
    }
    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE










}
