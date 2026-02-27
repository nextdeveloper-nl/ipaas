<?php

namespace NextDeveloper\IPAAS\Database\Models;

use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\IPAAS\Database\Observers\AccountProviderOverviewsObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Database\Traits\HasObject;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;

/**
 * AccountProviderOverviews model.
 *
 * @package  NextDeveloper\IPAAS\Database\Models
 * @property integer $iam_account_id
 * @property integer $provider_id
 * @property string $provider_uuid
 * @property string $provider_name
 * @property string $provider_type
 * @property boolean $is_default_wap
 * @property string $base_url
 * @property integer $total_workflows
 * @property integer $total_automation_engines
 * @property integer $executions_today
 * @property integer $success_today
 * @property integer $errors_today
 * @property $success_rate_today
 * @property \Carbon\Carbon $provider_created_at
 * @property \Carbon\Carbon $provider_updated_at
 */
class AccountProviderOverviews extends Model
{
    use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator, HasObject;

    public $timestamps = false;

    protected $table = 'ipaas_account_provider_overview';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'iam_account_id',
            'provider_id',
            'provider_uuid',
            'provider_name',
            'provider_type',
            'is_default_wap',
            'base_url',
            'total_workflows',
            'total_automation_engines',
            'executions_today',
            'success_today',
            'errors_today',
            'success_rate_today',
            'provider_created_at',
            'provider_updated_at',
    ];

    /**
      Here we have the fulltext fields. We can use these for fulltext search if enabled.
     */
    protected $fullTextFields = [

    ];

    /**
     @var array
     */
    protected $appends = [

    ];

    /**
     We are casting fields to objects so that we can work on them better
     *
     @var array
     */
    protected $casts = [
    'provider_id' => 'integer',
    'provider_name' => 'string',
    'provider_type' => 'string',
    'is_default_wap' => 'boolean',
    'base_url' => 'string',
    'total_workflows' => 'integer',
    'total_automation_engines' => 'integer',
    'executions_today' => 'integer',
    'success_today' => 'integer',
    'errors_today' => 'integer',
    'provider_created_at' => 'datetime',
    'provider_updated_at' => 'datetime',
    ];

    /**
     We are casting data fields.
     *
     @var array
     */
    protected $dates = [
    'provider_created_at',
    'provider_updated_at',
    ];

    /**
     @var array
     */
    protected $with = [

    ];

    /**
     @var int
     */
    protected $perPage = 20;

    /**
     @return void
     */
    public static function boot()
    {
        parent::boot();

        //  We create and add Observer even if we wont use it.
        parent::observe(AccountProviderOverviewsObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('ipaas.scopes.global');
        $modelScopes = config('ipaas.scopes.ipaas_account_provider_overview');

        if(!$modelScopes) { $modelScopes = [];
        }
        if (!$globalScopes) { $globalScopes = [];
        }

        $scopes = array_merge(
            $globalScopes,
            $modelScopes
        );

        if($scopes) {
            foreach ($scopes as $scope) {
                static::addGlobalScope(app($scope));
            }
        }
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
