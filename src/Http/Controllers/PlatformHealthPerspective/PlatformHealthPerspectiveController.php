<?php

namespace NextDeveloper\IPAAS\Http\Controllers\PlatformHealthPerspective;

use Illuminate\Http\Request;
use NextDeveloper\IPAAS\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\IPAAS\Http\Requests\PlatformHealthPerspective\PlatformHealthPerspectiveUpdateRequest;
use NextDeveloper\IPAAS\Database\Filters\PlatformHealthPerspectiveQueryFilter;
use NextDeveloper\IPAAS\Database\Models\PlatformHealthPerspective;
use NextDeveloper\IPAAS\Services\PlatformHealthPerspectiveService;
use NextDeveloper\IPAAS\Http\Requests\PlatformHealthPerspective\PlatformHealthPerspectiveCreateRequest;
use NextDeveloper\Commons\Http\Traits\Tags as TagsTrait;use NextDeveloper\Commons\Http\Traits\Addresses as AddressesTrait;
class PlatformHealthPerspectiveController extends AbstractController
{
    private $model = PlatformHealthPerspective::class;

    use TagsTrait;
    use AddressesTrait;
    /**
     * This method returns the list of platformhealthperspectives.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  PlatformHealthPerspectiveQueryFilter $filter  An object that builds search query
     * @param  Request                              $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PlatformHealthPerspectiveQueryFilter $filter, Request $request)
    {
        $data = PlatformHealthPerspectiveService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This function returns the list of actions that can be performed on this object.
     *
     * @return void
     */
    public function getActions()
    {
        $data = PlatformHealthPerspectiveService::getActions();

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
        $actionId = PlatformHealthPerspectiveService::doAction($objectId, $action, request()->all());

        return $this->withArray(
            [
            'action_id' =>  $actionId
            ]
        );
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $platformHealthPerspectiveId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = PlatformHealthPerspectiveService::getByRef($ref);

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
        $objects = PlatformHealthPerspectiveService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created PlatformHealthPerspective object on database.
     *
     * @param  PlatformHealthPerspectiveCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(PlatformHealthPerspectiveCreateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = PlatformHealthPerspectiveService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates PlatformHealthPerspective object on database.
     *
     * @param  $platformHealthPerspectiveId
     * @param  PlatformHealthPerspectiveUpdateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($platformHealthPerspectiveId, PlatformHealthPerspectiveUpdateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = PlatformHealthPerspectiveService::update($platformHealthPerspectiveId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates PlatformHealthPerspective object on database.
     *
     * @param  $platformHealthPerspectiveId
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($platformHealthPerspectiveId)
    {
        $model = PlatformHealthPerspectiveService::delete($platformHealthPerspectiveId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
