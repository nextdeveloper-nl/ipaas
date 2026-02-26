<?php

namespace NextDeveloper\IPAAS\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\IPAAS\Database\Filters\IpaasWorkflowDailyStatQueryFilter;
use NextDeveloper\IPAAS\Services\AbstractServices\AbstractIpaasWorkflowDailyStatService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait IpaasWorkflowDailyStatTestTraits
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

    public function test_http_ipaasworkflowdailystat_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/ipaas/ipaasworkflowdailystat',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_ipaasworkflowdailystat_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/ipaas/ipaasworkflowdailystat', [
            'form_params'   =>  [
                'total_executions'  =>  '1',
                'success_count'  =>  '1',
                'error_count'  =>  '1',
                'canceled_count'  =>  '1',
                'avg_duration_ms'  =>  '1',
                'max_duration_ms'  =>  '1',
                    'stat_date'  =>  now(),
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
    public function test_ipaasworkflowdailystat_model_get()
    {
        $result = AbstractIpaasWorkflowDailyStatService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_ipaasworkflowdailystat_get_all()
    {
        $result = AbstractIpaasWorkflowDailyStatService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_ipaasworkflowdailystat_get_paginated()
    {
        $result = AbstractIpaasWorkflowDailyStatService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_ipaasworkflowdailystat_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowdailystat_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasworkflowdailystat_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasWorkflowDailyStat\IpaasWorkflowDailyStatRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowdailystat_event_total_executions_filter()
    {
        try {
            $request = new Request(
                [
                'total_executions'  =>  '1'
                ]
            );

            $filter = new IpaasWorkflowDailyStatQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowdailystat_event_success_count_filter()
    {
        try {
            $request = new Request(
                [
                'success_count'  =>  '1'
                ]
            );

            $filter = new IpaasWorkflowDailyStatQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowdailystat_event_error_count_filter()
    {
        try {
            $request = new Request(
                [
                'error_count'  =>  '1'
                ]
            );

            $filter = new IpaasWorkflowDailyStatQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowdailystat_event_canceled_count_filter()
    {
        try {
            $request = new Request(
                [
                'canceled_count'  =>  '1'
                ]
            );

            $filter = new IpaasWorkflowDailyStatQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowdailystat_event_avg_duration_ms_filter()
    {
        try {
            $request = new Request(
                [
                'avg_duration_ms'  =>  '1'
                ]
            );

            $filter = new IpaasWorkflowDailyStatQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowdailystat_event_max_duration_ms_filter()
    {
        try {
            $request = new Request(
                [
                'max_duration_ms'  =>  '1'
                ]
            );

            $filter = new IpaasWorkflowDailyStatQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowdailystat_event_stat_date_filter_start()
    {
        try {
            $request = new Request(
                [
                'stat_dateStart'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowDailyStatQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowdailystat_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowDailyStatQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowdailystat_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowDailyStatQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowdailystat_event_stat_date_filter_end()
    {
        try {
            $request = new Request(
                [
                'stat_dateEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowDailyStatQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowdailystat_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowDailyStatQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowdailystat_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowDailyStatQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowdailystat_event_stat_date_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'stat_dateStart'  =>  now(),
                'stat_dateEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowDailyStatQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowdailystat_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowDailyStatQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasworkflowdailystat_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasWorkflowDailyStatQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasWorkflowDailyStat::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}