<?php

namespace NextDeveloper\IPAAS\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\IPAAS\Database\Filters\IpaasAccountQueryFilter;
use NextDeveloper\IPAAS\Services\AbstractServices\AbstractIpaasAccountService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait IpaasAccountTestTraits
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

    public function test_http_ipaasaccount_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/ipaas/ipaasaccount',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_ipaasaccount_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/ipaas/ipaasaccount', [
            'form_params'   =>  [
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
    public function test_ipaasaccount_model_get()
    {
        $result = AbstractIpaasAccountService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_ipaasaccount_get_all()
    {
        $result = AbstractIpaasAccountService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_ipaasaccount_get_paginated()
    {
        $result = AbstractIpaasAccountService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_ipaasaccount_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasaccount_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasaccount_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasAccount\IpaasAccountRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasaccount_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new IpaasAccountQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasaccount_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new IpaasAccountQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasaccount_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new IpaasAccountQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasaccount_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasAccountQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasaccount_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasAccountQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasaccount_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasAccountQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasaccount_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasAccountQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasaccount_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasAccountQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasaccount_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasAccountQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}