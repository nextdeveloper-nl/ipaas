<?php

namespace NextDeveloper\IPAAS\Database\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\IPAAS\Database\Observers\ProvidersObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Database\Traits\HasObject;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;

/**
 * Providers model.
 *
 * @package  NextDeveloper\IPAAS\Database\Models
 * @property integer $id
 * @property string $uuid
 * @property string $name
 * @property string $description
 * @property string $provider_type
 * @property boolean $is_default_wap
 * @property integer $iaas_virtual_machine_id
 * @property string $external_account_id
 * @property string $region
 * @property integer $iam_account_id
 * @property integer $iam_user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property string $base_url
 * @property string $api_token
 * @property string $api_secret
 */
class Providers extends Model
{
    use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator, HasObject;
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'ipaas_providers';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'name',
            'description',
            'provider_type',
            'is_default_wap',
            'iaas_virtual_machine_id',
            'external_account_id',
            'region',
            'iam_account_id',
            'iam_user_id',
            'base_url',
            'api_token',
            'api_secret',
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
    'name' => 'string',
    'description' => 'string',
    'provider_type' => 'string',
    'is_default_wap' => 'boolean',
    'iaas_virtual_machine_id' => 'integer',
    'external_account_id' => 'string',
    'region' => 'string',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
    'base_url' => 'string',
    'api_token' => 'string',
    'api_secret' => 'string',
    ];

    /**
     We are casting data fields.
     *
     @var array
     */
    protected $dates = [
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
        parent::observe(ProvidersObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('ipaas.scopes.global');
        $modelScopes = config('ipaas.scopes.ipaas_providers');

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
