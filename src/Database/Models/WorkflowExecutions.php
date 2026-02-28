<?php

namespace NextDeveloper\IPAAS\Database\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\IPAAS\Database\Observers\WorkflowExecutionsObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Database\Traits\HasObject;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;

/**
 * WorkflowExecutions model.
 *
 * @package  NextDeveloper\IPAAS\Database\Models
 * @property integer $id
 * @property string $uuid
 * @property integer $ipaas_workflow_id
 * @property integer $ipaas_provider_id
 * @property integer $iam_account_id
 * @property string $external_execution_id
 * @property string $status
 * @property string $trigger_mode
 * @property \Carbon\Carbon $started_at
 * @property \Carbon\Carbon $finished_at
 * @property integer $duration_ms
 * @property string $error_message
 * @property string $error_node
 * @property string $error_stack
 * @property integer $retry_of_execution_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class WorkflowExecutions extends Model
{
    use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator, HasObject;
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'ipaas_workflow_executions';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'ipaas_workflow_id',
            'ipaas_provider_id',
            'iam_account_id',
            'external_execution_id',
            'status',
            'trigger_mode',
            'started_at',
            'finished_at',
            'duration_ms',
            'error_message',
            'error_node',
            'error_stack',
            'retry_of_execution_id',
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
    'ipaas_workflow_id' => 'integer',
    'ipaas_provider_id' => 'integer',
    'external_execution_id' => 'string',
    'status' => 'string',
    'trigger_mode' => 'string',
    'started_at' => 'datetime',
    'finished_at' => 'datetime',
    'duration_ms' => 'integer',
    'error_message' => 'string',
    'error_node' => 'string',
    'error_stack' => 'string',
    'retry_of_execution_id' => 'integer',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
    ];

    /**
     We are casting data fields.
     *
     @var array
     */
    protected $dates = [
    'started_at',
    'finished_at',
    'created_at',
    'updated_at',
    'deleted_at',
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
        parent::observe(WorkflowExecutionsObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('ipaas.scopes.global');
        $modelScopes = config('ipaas.scopes.ipaas_workflow_executions');

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
