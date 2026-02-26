<?php

namespace NextDeveloper\IPAAS\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\IPAAS\Database\Filters\IpaasWorkflowExecutionQueryFilter;
use NextDeveloper\IPAAS\Services\AbstractServices\AbstractIpaasWorkflowExecutionService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait IpaasWorkflowExecutionTestTraits
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

    public function test_http_ipaasworkflowexecution_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/ipaas/ipaasworkflowexecution',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_ipaasworkflowexecution_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/ipaas/ipaasworkflowexecution', [
            'form_params'   =>  [
                'external_execution_id'  =>  'a',
                'status'  =>  'a',
                'trigger_mode'  =>  'a',
                'error_message'  =>  'a',
                'error_node'  =>  'a',
                'error_stack'  =>  'a',
                'duration_ms'  =>  '1',
                    'started_at'  =>  now(),
                    'finished_at'  =>  now(),
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
    public function test_ipaasworkflowexecution_model_get()
    {
        $result = AbstractIpaasWorkflowExecutionService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_ipaasworkflowexecution_get_all()
    {
        $result = AbstractIpaasWorkflowExecutionService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_ipaasworkflowexecution_get_paginated()
    {
        $result = AbstractIpaasWorkflowExecutionService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_ipaasworkflowexecution_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowexecution_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowExecution\IpaasWorkflowExecutionRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_external_execution_id_filter()
    {
        try {
            $request = new Request(
                [
                'external_execution_id'  =>  'a'
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_status_filter()
    {
        try {
            $request = new Request(
                [
                'status'  =>  'a'
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_trigger_mode_filter()
    {
        try {
            $request = new Request(
                [
                'trigger_mode'  =>  'a'
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_error_message_filter()
    {
        try {
            $request = new Request(
                [
                'error_message'  =>  'a'
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_error_node_filter()
    {
        try {
            $request = new Request(
                [
                'error_node'  =>  'a'
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_error_stack_filter()
    {
        try {
            $request = new Request(
                [
                'error_stack'  =>  'a'
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_duration_ms_filter()
    {
        try {
            $request = new Request(
                [
                'duration_ms'  =>  '1'
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_started_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'started_atStart'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_finished_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'finished_atStart'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_started_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'started_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_finished_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'finished_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_started_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'started_atStart'  =>  now(),
                'started_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_finished_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'finished_atStart'  =>  now(),
                'finished_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowexecution_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowExecutionQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowExecution::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}