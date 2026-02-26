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

    Route::prefix('workflow-executions')->group(
        function () {
            Route::get('/', 'WorkflowExecutions\WorkflowExecutionsController@index');
            Route::get('/actions', 'WorkflowExecutions\WorkflowExecutionsController@getActions');

            Route::get('{ipaas_workflow_executions}/tags ', 'WorkflowExecutions\WorkflowExecutionsController@tags');
            Route::post('{ipaas_workflow_executions}/tags ', 'WorkflowExecutions\WorkflowExecutionsController@saveTags');
            Route::get('{ipaas_workflow_executions}/addresses ', 'WorkflowExecutions\WorkflowExecutionsController@addresses');
            Route::post('{ipaas_workflow_executions}/addresses ', 'WorkflowExecutions\WorkflowExecutionsController@saveAddresses');

            Route::get('/{ipaas_workflow_executions}/{subObjects}', 'WorkflowExecutions\WorkflowExecutionsController@relatedObjects');
            Route::get('/{ipaas_workflow_executions}', 'WorkflowExecutions\WorkflowExecutionsController@show');

            Route::post('/', 'WorkflowExecutions\WorkflowExecutionsController@store');
            Route::post('/{ipaas_workflow_executions}/do/{action}', 'WorkflowExecutions\WorkflowExecutionsController@doAction');

            Route::patch('/{ipaas_workflow_executions}', 'WorkflowExecutions\WorkflowExecutionsController@update');
            Route::delete('/{ipaas_workflow_executions}', 'WorkflowExecutions\WorkflowExecutionsController@destroy');
        }
    );

    Route::prefix('workflow-daily-stats')->group(
        function () {
            Route::get('/', 'WorkflowDailyStats\WorkflowDailyStatsController@index');
            Route::get('/actions', 'WorkflowDailyStats\WorkflowDailyStatsController@getActions');

            Route::get('{ipaas_workflow_daily_stats}/tags ', 'WorkflowDailyStats\WorkflowDailyStatsController@tags');
            Route::post('{ipaas_workflow_daily_stats}/tags ', 'WorkflowDailyStats\WorkflowDailyStatsController@saveTags');
            Route::get('{ipaas_workflow_daily_stats}/addresses ', 'WorkflowDailyStats\WorkflowDailyStatsController@addresses');
            Route::post('{ipaas_workflow_daily_stats}/addresses ', 'WorkflowDailyStats\WorkflowDailyStatsController@saveAddresses');

            Route::get('/{ipaas_workflow_daily_stats}/{subObjects}', 'WorkflowDailyStats\WorkflowDailyStatsController@relatedObjects');
            Route::get('/{ipaas_workflow_daily_stats}', 'WorkflowDailyStats\WorkflowDailyStatsController@show');

            Route::post('/', 'WorkflowDailyStats\WorkflowDailyStatsController@store');
            Route::post('/{ipaas_workflow_daily_stats}/do/{action}', 'WorkflowDailyStats\WorkflowDailyStatsController@doAction');

            Route::patch('/{ipaas_workflow_daily_stats}', 'WorkflowDailyStats\WorkflowDailyStatsController@update');
            Route::delete('/{ipaas_workflow_daily_stats}', 'WorkflowDailyStats\WorkflowDailyStatsController@destroy');
        }
    );

    Route::prefix('workflow-executions-perspective')->group(
        function () {
            Route::get('/', 'WorkflowExecutionsPerspective\WorkflowExecutionsPerspectiveController@index');
            Route::get('/actions', 'WorkflowExecutionsPerspective\WorkflowExecutionsPerspectiveController@getActions');

            Route::get('{iwep}/tags ', 'WorkflowExecutionsPerspective\WorkflowExecutionsPerspectiveController@tags');
            Route::post('{iwep}/tags ', 'WorkflowExecutionsPerspective\WorkflowExecutionsPerspectiveController@saveTags');
            Route::get('{iwep}/addresses ', 'WorkflowExecutionsPerspective\WorkflowExecutionsPerspectiveController@addresses');
            Route::post('{iwep}/addresses ', 'WorkflowExecutionsPerspective\WorkflowExecutionsPerspectiveController@saveAddresses');

            Route::get('/{iwep}/{subObjects}', 'WorkflowExecutionsPerspective\WorkflowExecutionsPerspectiveController@relatedObjects');
            Route::get('/{iwep}', 'WorkflowExecutionsPerspective\WorkflowExecutionsPerspectiveController@show');

            Route::post('/', 'WorkflowExecutionsPerspective\WorkflowExecutionsPerspectiveController@store');
            Route::post('/{iwep}/do/{action}', 'WorkflowExecutionsPerspective\WorkflowExecutionsPerspectiveController@doAction');

            Route::patch('/{iwep}', 'WorkflowExecutionsPerspective\WorkflowExecutionsPerspectiveController@update');
            Route::delete('/{iwep}', 'WorkflowExecutionsPerspective\WorkflowExecutionsPerspectiveController@destroy');
        }
    );

// EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

});
