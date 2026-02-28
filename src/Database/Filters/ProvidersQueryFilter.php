<?php

namespace NextDeveloper\IPAAS\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
                

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class ProvidersQueryFilter extends AbstractQueryFilter
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

        
    public function providerType($value)
    {
        return $this->builder->where('provider_type', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of providerType
    public function provider_type($value)
    {
        return $this->providerType($value);
    }
        
    public function externalAccountId($value)
    {
        return $this->builder->where('external_account_id', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of externalAccountId
    public function external_account_id($value)
    {
        return $this->externalAccountId($value);
    }
        
    public function region($value)
    {
        return $this->builder->where('region', 'ilike', '%' . $value . '%');
    }

        
    public function baseUrl($value)
    {
        return $this->builder->where('base_url', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of baseUrl
    public function base_url($value)
    {
        return $this->baseUrl($value);
    }
        
    public function apiToken($value)
    {
        return $this->builder->where('api_token', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of apiToken
    public function api_token($value)
    {
        return $this->apiToken($value);
    }
        
    public function apiSecret($value)
    {
        return $this->builder->where('api_secret', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of apiSecret
    public function api_secret($value)
    {
        return $this->apiSecret($value);
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

    public function iaasVirtualMachineId($value)
    {
            $iaasVirtualMachine = \NextDeveloper\IAAS\Database\Models\VirtualMachines::where('uuid', $value)->first();

        if($iaasVirtualMachine) {
            return $this->builder->where('iaas_virtual_machine_id', '=', $iaasVirtualMachine->id);
        }
    }

        //  This is an alias function of iaasVirtualMachine
    public function iaas_virtual_machine_id($value)
    {
        return $this->iaasVirtualMachine($value);
    }
    
    public function externalAccountId($value)
    {
            $externalAccount = \NextDeveloper\\Database\Models\ExternalAccounts::where('uuid', $value)->first();

        if($externalAccount) {
            return $this->builder->where('external_account_id', '=', $externalAccount->id);
        }
    }

        //  This is an alias function of externalAccount
    public function external_account_id($value)
    {
        return $this->externalAccount($value);
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
