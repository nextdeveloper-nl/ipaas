<?php

namespace NextDeveloper\IPAAS\Database\Models;

use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\IPAAS\Database\Observers\WorkflowDailyStatsObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Database\Traits\HasObject;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;

/**
 * WorkflowDailyStats model.
 *
 * @package  NextDeveloper\IPAAS\Database\Models
 * @property integer $id
 * @property string $uuid
 * @property \Carbon\Carbon $stat_date
 * @property integer $ipaas_workflow_id
 * @property integer $iam_account_id
 * @property integer $total_executions
 * @property integer $success_count
 * @property integer $error_count
 * @property integer $canceled_count
 * @property integer $avg_duration_ms
 * @property integer $max_duration_ms
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class WorkflowDailyStats extends Model
{
    use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator, HasObject;

    public $timestamps = true;

    protected $table = 'ipaas_workflow_daily_stats';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'stat_date',
            'ipaas_workflow_id',
            'iam_account_id',
            'total_executions',
            'success_count',
            'error_count',
            'canceled_count',
            'avg_duration_ms',
            'max_duration_ms',
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
    'id' => 'integer',
    'stat_date' => 'datetime',
    'ipaas_workflow_id' => 'integer',
    'total_executions' => 'integer',
    'success_count' => 'integer',
    'error_count' => 'integer',
    'canceled_count' => 'integer',
    'avg_duration_ms' => 'integer',
    'max_duration_ms' => 'integer',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    ];

    /**
     We are casting data fields.
     *
     @var array
     */
    protected $dates = [
    'stat_date',
    'created_at',
    'updated_at',
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
        parent::observe(WorkflowDailyStatsObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('ipaas.scopes.global');
        $modelScopes = config('ipaas.scopes.ipaas_workflow_daily_stats');

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
