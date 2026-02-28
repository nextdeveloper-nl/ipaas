<?php

namespace NextDeveloper\IPAAS\Database\Models;

use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\IPAAS\Database\Observers\ExecutionDailyStatsObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Database\Traits\HasObject;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;

/**
 * ExecutionDailyStats model.
 *
 * @package  NextDeveloper\IPAAS\Database\Models
 * @property \Carbon\Carbon $stat_date
 * @property integer $iam_account_id
 * @property integer $ipaas_provider_id
 * @property string $provider_name
 * @property string $provider_type
 * @property integer $total_executions
 * @property integer $success_count
 * @property integer $error_count
 * @property integer $canceled_count
 * @property $success_rate
 */
class ExecutionDailyStats extends Model
{
    use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator, HasObject;

    public $timestamps = false;

    protected $table = 'ipaas_execution_daily_stats';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'stat_date',
            'iam_account_id',
            'ipaas_provider_id',
            'provider_name',
            'provider_type',
            'total_executions',
            'success_count',
            'error_count',
            'canceled_count',
            'success_rate',
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
    'stat_date' => 'datetime',
    'ipaas_provider_id' => 'integer',
    'provider_name' => 'string',
    'provider_type' => 'string',
    'total_executions' => 'integer',
    'success_count' => 'integer',
    'error_count' => 'integer',
    'canceled_count' => 'integer',
    ];

    /**
     We are casting data fields.
     *
     @var array
     */
    protected $dates = [
    'stat_date',
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
        parent::observe(ExecutionDailyStatsObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('ipaas.scopes.global');
        $modelScopes = config('ipaas.scopes.ipaas_execution_daily_stats');

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
