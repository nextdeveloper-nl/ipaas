<?php

namespace NextDeveloper\IPAAS\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
    

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class AccountStatsQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;

    public function totalProviders($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('total_providers', $operator, $value);
    }

        //  This is an alias function of totalProviders
    public function total_providers($value)
    {
        return $this->totalProviders($value);
    }
    
    public function totalWorkflows($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('total_workflows', $operator, $value);
    }

        //  This is an alias function of totalWorkflows
    public function total_workflows($value)
    {
        return $this->totalWorkflows($value);
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
    
    public function iamAccountId($value)
    {
            $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::where('uuid', $value)->first();

        if($iamAccount) {
            return $this->builder->where('iam_account_id', '=', $iamAccount->id);
        }
    }

    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
