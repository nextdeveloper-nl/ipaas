<?php

namespace NextDeveloper\IPAAS\Http\Controllers\ExecutionDailyStats;

use Illuminate\Http\Request;
use NextDeveloper\IPAAS\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\IPAAS\Http\Requests\ExecutionDailyStats\ExecutionDailyStatsUpdateRequest;
use NextDeveloper\IPAAS\Database\Filters\ExecutionDailyStatsQueryFilter;
use NextDeveloper\IPAAS\Database\Models\ExecutionDailyStats;
use NextDeveloper\IPAAS\Services\ExecutionDailyStatsService;
use NextDeveloper\IPAAS\Http\Requests\ExecutionDailyStats\ExecutionDailyStatsCreateRequest;
use NextDeveloper\Commons\Http\Traits\Tags as TagsTrait;use NextDeveloper\Commons\Http\Traits\Addresses as AddressesTrait;
class ExecutionDailyStatsController extends AbstractController
{
    private $model = ExecutionDailyStats::class;

    use TagsTrait;
    use AddressesTrait;
    /**
     * This method returns the list of executiondailystats.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  ExecutionDailyStatsQueryFilter $filter  An object that builds search query
     * @param  Request                        $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ExecutionDailyStatsQueryFilter $filter, Request $request)
    {
        $data = ExecutionDailyStatsService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This function returns the list of actions that can be performed on this object.
     *
     * @return void
     */
    public function getActions()
    {
        $data = ExecutionDailyStatsService::getActions();

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * Makes the related action to the object
     *
     * @param  $objectId
     * @param  $action
     * @return array
     */
    public function doAction($objectId, $action)
    {
        $actionId = ExecutionDailyStatsService::doAction($objectId, $action, request()->all());

        return $this->withArray(
            [
            'action_id' =>  $actionId
            ]
        );
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $executionDailyStatsId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = ExecutionDailyStatsService::getByRef($ref);

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method returns the list of sub objects the related object. Sub object means an object which is preowned by
     * this object.
     *
     * It can be tags, addresses, states etc.
     *
     * @param  $ref
     * @param  $subObject
     * @return void
     */
    public function relatedObjects($ref, $subObject)
    {
        $objects = ExecutionDailyStatsService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created ExecutionDailyStats object on database.
     *
     * @param  ExecutionDailyStatsCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(ExecutionDailyStatsCreateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = ExecutionDailyStatsService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates ExecutionDailyStats object on database.
     *
     * @param  $executionDailyStatsId
     * @param  ExecutionDailyStatsUpdateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($executionDailyStatsId, ExecutionDailyStatsUpdateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = ExecutionDailyStatsService::update($executionDailyStatsId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates ExecutionDailyStats object on database.
     *
     * @param  $executionDailyStatsId
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($executionDailyStatsId)
    {
        $model = ExecutionDailyStatsService::delete($executionDailyStatsId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
