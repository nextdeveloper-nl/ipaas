<?php

namespace NextDeveloper\IPAAS\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\IPAAS\Database\Filters\IpaasProviderQueryFilter;
use NextDeveloper\IPAAS\Services\AbstractServices\AbstractIpaasProviderService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait IpaasProviderTestTraits
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

    public function test_http_ipaasprovider_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/ipaas/ipaasprovider',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_ipaasprovider_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/ipaas/ipaasprovider', [
            'form_params'   =>  [
                'name'  =>  'a',
                'description'  =>  'a',
                'provider_type'  =>  'a',
                'external_account_id'  =>  'a',
                'region'  =>  'a',
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
    public function test_ipaasprovider_model_get()
    {
        $result = AbstractIpaasProviderService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_ipaasprovider_get_all()
    {
        $result = AbstractIpaasProviderService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_ipaasprovider_get_paginated()
    {
        $result = AbstractIpaasProviderService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_ipaasprovider_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasprovider_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_ipaasprovider_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::first();

            event(new \NextDeveloper\IPAAS\Events\IpaasProvider\IpaasProviderRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasprovider_event_name_filter()
    {
        try {
            $request = new Request(
                [
                'name'  =>  'a'
                ]
            );

            $filter = new IpaasProviderQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasprovider_event_description_filter()
    {
        try {
            $request = new Request(
                [
                'description'  =>  'a'
                ]
            );

            $filter = new IpaasProviderQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasprovider_event_provider_type_filter()
    {
        try {
            $request = new Request(
                [
                'provider_type'  =>  'a'
                ]
            );

            $filter = new IpaasProviderQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasprovider_event_external_account_id_filter()
    {
        try {
            $request = new Request(
                [
                'external_account_id'  =>  'a'
                ]
            );

            $filter = new IpaasProviderQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasprovider_event_region_filter()
    {
        try {
            $request = new Request(
                [
                'region'  =>  'a'
                ]
            );

            $filter = new IpaasProviderQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasprovider_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new IpaasProviderQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasprovider_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new IpaasProviderQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasprovider_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new IpaasProviderQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasprovider_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasProviderQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasprovider_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasProviderQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasprovider_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasProviderQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasprovider_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasProviderQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasprovider_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasProviderQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_ipaasprovider_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new IpaasProviderQueryFilter($request);

            $model = \NextDeveloper\IPAAS\Database\Models\IpaasProvider::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}