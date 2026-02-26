<?php

namespace NextDeveloper\IPAAS\Database\Models;

use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\IPAAS\Database\Observers\WorkflowExecutionsPerspectiveObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Database\Traits\HasObject;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;

/**
 * WorkflowExecutionsPerspective model.
 *
 * @package  NextDeveloper\IPAAS\Database\Models
 * @property integer $id
 * @property string $uuid
 * @property string $external_execution_id
 * @property string $status
 * @property string $trigger_mode
 * @property \Carbon\Carbon $started_at
 * @property \Carbon\Carbon $finished_at
 * @property integer $duration_ms
 * @property string $error_message
 * @property string $error_node
 * @property integer $retry_of_execution_id
 * @property integer $ipaas_workflow_id
 * @property string $workflow_name
 * @property integer $ipaas_provider_id
 * @property string $provider_name
 * @property string $provider_type
 * @property integer $iam_account_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class WorkflowExecutionsPerspective extends Model
{
    use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator, HasObject;

    public $timestamps = true;

    protected $table = 'ipaas_workflow_executions_perspective';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'external_execution_id',
            'status',
            'trigger_mode',
            'started_at',
            'finished_at',
            'duration_ms',
            'error_message',
            'error_node',
            'retry_of_execution_id',
            'ipaas_workflow_id',
            'workflow_name',
            'ipaas_provider_id',
            'provider_name',
            'provider_type',
            'iam_account_id',
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
    'external_execution_id' => 'string',
    'status' => 'string',
    'trigger_mode' => 'string',
    'started_at' => 'datetime',
    'finished_at' => 'datetime',
    'duration_ms' => 'integer',
    'error_message' => 'string',
    'error_node' => 'string',
    'retry_of_execution_id' => 'integer',
    'ipaas_workflow_id' => 'integer',
    'workflow_name' => 'string',
    'ipaas_provider_id' => 'integer',
    'provider_name' => 'string',
    'provider_type' => 'string',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
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
        parent::observe(WorkflowExecutionsPerspectiveObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('ipaas.scopes.global');
        $modelScopes = config('ipaas.scopes.ipaas_workflow_executions_perspective');

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
