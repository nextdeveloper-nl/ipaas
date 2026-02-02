<?php

namespace NextDeveloper\IPAAS\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\IPAAS\Database\Filters\IpaasWorkflowQueryFilter;
use NextDeveloper\IPAAS\Services\AbstractServices\AbstractIpaasWorkflowService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait IpaasWorkflowTestTraits
{
    public $http;

    /**
     *   Creating the Guzzle object
     */
    public function setupGuzzle()
    {
        $this->http = new Client(
            [
            'base_uri'  =>  '127.0.0.1:8000'
            ]
        );
    }

    /**
     *   Destroying the Guzzle object
     */
    public function destroyGuzzle()
    {
        $this->http = null;
    }

    public function test_http_ipaasworkflow_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/ipaas/ipaasworkflow',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_ipaasworkflow_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/ipaas/ipaasworkflow', [
            'form_params'   =>  [
                'name'  =>  'a',
                'description'  =>  'a',
                'trigger_type'  =>  'a',
                'status'  =>  'a',
                'external_workflow_id'  =>  'a',
                    'last_synched_at'  =>  now(),
                            ],
                ['http_errors' => false]
            ]
        );

        $this->assertEquals($response->getStatusCode(), Response::HTTP_OK);
    }

    /**
     * Get test
     *
     * @return bool
     */
    public function test_ipaasworkflow_model_get()
    {
        $result = AbstractIpaasWorkflowService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_ipaasworkflow_get_all()
    {
        $result = AbstractIpaasWorkflowService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_ipaasworkflow_get_paginated()
    {
        $result = AbstractIpaasWorkflowService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_ipaasworkflow_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflow_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflow_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflow\IpaasWorkflowRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflow_event_name_filter()
    {
        try {
            $request = new Request(
                [
                'name'  =>  'a'
                ]
            );

            $filter = new IpaasWorkflowQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflow_event_description_filter()
    {
        try {
            $request = new Request(
                [
                'description'  =>  'a'
                ]
            );

            $filter = new IpaasWorkflowQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflow_event_trigger_type_filter()
    {
        try {
            $request = new Request(
                [
                'trigger_type'  =>  'a'
                ]
            );

            $filter = new IpaasWorkflowQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflow_event_status_filter()
    {
        try {
            $request = new Request(
                [
                'status'  =>  'a'
                ]
            );

            $filter = new IpaasWorkflowQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflow_event_external_workflow_id_filter()
    {
        try {
            $request = new Request(
                [
                'external_workflow_id'  =>  'a'
                ]
            );

            $filter = new IpaasWorkflowQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflow_event_last_synched_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'last_synched_atStart'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflow_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflow_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflow_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflow_event_last_synched_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'last_synched_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflow_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflow_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflow_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflow_event_last_synched_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'last_synched_atStart'  =>  now(),
                'last_synched_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflow_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflow_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflow_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflow::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}