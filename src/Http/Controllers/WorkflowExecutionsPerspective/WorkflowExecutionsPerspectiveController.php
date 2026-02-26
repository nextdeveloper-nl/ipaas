<?php

namespace NextDeveloper\IPAAS\Http\Controllers\WorkflowExecutionsPerspective;

use Illuminate\Http\Request;
use NextDeveloper\IPAAS\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\IPAAS\Http\Requests\WorkflowExecutionsPerspective\WorkflowExecutionsPerspectiveUpdateRequest;
use NextDeveloper\IPAAS\Database\Filters\WorkflowExecutionsPerspectiveQueryFilter;
use NextDeveloper\IPAAS\Database\Models\WorkflowExecutionsPerspective;
use NextDeveloper\IPAAS\Services\WorkflowExecutionsPerspectiveService;
use NextDeveloper\IPAAS\Http\Requests\WorkflowExecutionsPerspective\WorkflowExecutionsPerspectiveCreateRequest;
use NextDeveloper\Commons\Http\Traits\Tags as TagsTrait;use NextDeveloper\Commons\Http\Traits\Addresses as AddressesTrait;
class WorkflowExecutionsPerspectiveController extends AbstractController
{
    private $model = WorkflowExecutionsPerspective::class;

    use TagsTrait;
    use AddressesTrait;
    /**
     * This method returns the list of workflowexecutionsperspectives.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  WorkflowExecutionsPerspectiveQueryFilter $filter  An object that builds search query
     * @param  Request                                  $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(WorkflowExecutionsPerspectiveQueryFilter $filter, Request $request)
    {
        $data = WorkflowExecutionsPerspectiveService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This function returns the list of actions that can be performed on this object.
     *
     * @return void
     */
    public function getActions()
    {
        $data = WorkflowExecutionsPerspectiveService::getActions();

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
        $actionId = WorkflowExecutionsPerspectiveService::doAction($objectId, $action, request()->all());

        return $this->withArray(
            [
            'action_id' =>  $actionId
            ]
        );
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $workflowExecutionsPerspectiveId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = WorkflowExecutionsPerspectiveService::getByRef($ref);

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
        $objects = WorkflowExecutionsPerspectiveService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created WorkflowExecutionsPerspective object on database.
     *
     * @param  WorkflowExecutionsPerspectiveCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(WorkflowExecutionsPerspectiveCreateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = WorkflowExecutionsPerspectiveService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates WorkflowExecutionsPerspective object on database.
     *
     * @param  $workflowExecutionsPerspectiveId
     * @param  WorkflowExecutionsPerspectiveUpdateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($workflowExecutionsPerspectiveId, WorkflowExecutionsPerspectiveUpdateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = WorkflowExecutionsPerspectiveService::update($workflowExecutionsPerspectiveId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates WorkflowExecutionsPerspective object on database.
     *
     * @param  $workflowExecutionsPerspectiveId
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($workflowExecutionsPerspectiveId)
    {
        $model = WorkflowExecutionsPerspectiveService::delete($workflowExecutionsPerspectiveId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
