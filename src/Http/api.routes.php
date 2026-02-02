<?php

Route::prefix('ipaas')->group(function () {
    Route::prefix('accounts')->group(
        function () {
            Route::get('/', 'Accounts\AccountsController@index');
            Route::get('/actions', 'Accounts\AccountsController@getActions');

            Route::get('{ipaas_accounts}/tags ', 'Accounts\AccountsController@tags');
            Route::post('{ipaas_accounts}/tags ', 'Accounts\AccountsController@saveTags');
            Route::get('{ipaas_accounts}/addresses ', 'Accounts\AccountsController@addresses');
            Route::post('{ipaas_accounts}/addresses ', 'Accounts\AccountsController@saveAddresses');

            Route::get('/{ipaas_accounts}/{subObjects}', 'Accounts\AccountsController@relatedObjects');
            Route::get('/{ipaas_accounts}', 'Accounts\AccountsController@show');

            Route::post('/', 'Accounts\AccountsController@store');
            Route::post('/{ipaas_accounts}/do/{action}', 'Accounts\AccountsController@doAction');

            Route::patch('/{ipaas_accounts}', 'Accounts\AccountsController@update');
            Route::delete('/{ipaas_accounts}', 'Accounts\AccountsController@destroy');
        }
    );

    Route::prefix('providers')->group(
        function () {
            Route::get('/', 'Providers\ProvidersController@index');
            Route::get('/actions', 'Providers\ProvidersController@getActions');

            Route::get('{ipaas_providers}/tags ', 'Providers\ProvidersController@tags');
            Route::post('{ipaas_providers}/tags ', 'Providers\ProvidersController@saveTags');
            Route::get('{ipaas_providers}/addresses ', 'Providers\ProvidersController@addresses');
            Route::post('{ipaas_providers}/addresses ', 'Providers\ProvidersController@saveAddresses');

            Route::get('/{ipaas_providers}/{subObjects}', 'Providers\ProvidersController@relatedObjects');
            Route::get('/{ipaas_providers}', 'Providers\ProvidersController@show');

            Route::post('/', 'Providers\ProvidersController@store');
            Route::post('/{ipaas_providers}/do/{action}', 'Providers\ProvidersController@doAction');

            Route::patch('/{ipaas_providers}', 'Providers\ProvidersController@update');
            Route::delete('/{ipaas_providers}', 'Providers\ProvidersController@destroy');
        }
    );

    Route::prefix('workflows')->group(
        function () {
            Route::get('/', 'Workflows\WorkflowsController@index');
            Route::get('/actions', 'Workflows\WorkflowsController@getActions');

            Route::get('{ipaas_workflows}/tags ', 'Workflows\WorkflowsController@tags');
            Route::post('{ipaas_workflows}/tags ', 'Workflows\WorkflowsController@saveTags');
            Route::get('{ipaas_workflows}/addresses ', 'Workflows\WorkflowsController@addresses');
            Route::post('{ipaas_workflows}/addresses ', 'Workflows\WorkflowsController@saveAddresses');

            Route::get('/{ipaas_workflows}/{subObjects}', 'Workflows\WorkflowsController@relatedObjects');
            Route::get('/{ipaas_workflows}', 'Workflows\WorkflowsController@show');

            Route::post('/', 'Workflows\WorkflowsController@store');
            Route::post('/{ipaas_workflows}/do/{action}', 'Workflows\WorkflowsController@doAction');

            Route::patch('/{ipaas_workflows}', 'Workflows\WorkflowsController@update');
            Route::delete('/{ipaas_workflows}', 'Workflows\WorkflowsController@destroy');
        }
    );

// EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

});
