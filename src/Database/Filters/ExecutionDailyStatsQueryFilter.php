<?php

namespace NextDeveloper\IPAAS\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
        

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class ExecutionDailyStatsQueryFilter extends AbstractQueryFilter
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

    public function iamAccountId($value)
    {
            $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::where('uuid', $value)->first();

        if($iamAccount) {
            return $this->builder->where('iam_account_id', '=', $iamAccount->id);
        }
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
    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE










}
