<?php

use Illuminate\Routing\Router;

Route::group(['middleware' => ['auth', 'sametime_login', 'check_smart_api_version']], function (Router $router): void {
    // authenticate
    $router->get('logout', 'AuthenticateController@logout')->name('logout');
    $router->get('me', 'AuthenticateController@me');
    $router->post('change_password', 'AuthenticateController@changePassword');
    $router->post('init_change_password', 'AuthenticateController@initLoginChangePassword');

    // 今後、認証が必要なrouteはここより下に書くこと

    // SearchConditionTexts
    Route::group(['prefix' => 'search_condition_texts'], function (Router $router): void {
        $router->post('/rating', 'SearchConditionTextController@rating')->middleware(['check_exists_custom_indicator']);
        $router->post('/programTable', 'SearchConditionTextController@programTable')->middleware(['check_exists_custom_indicator']);
        $router->post('/programList', 'SearchConditionTextController@programList')->middleware(['check_exists_custom_indicator']);
        $router->post('/periodAverage', 'SearchConditionTextController@periodAverage');
        $router->post('/grp', 'SearchConditionTextController@grp');
        $router->post('/list', 'SearchConditionTextController@list');
        $router->post('/advertising', 'SearchConditionTextController@advertising');
        $router->post('/analysis', 'SearchConditionTextController@analysis');
        $router->post('/two', 'SearchConditionTextController@two');
        $router->post('/five', 'SearchConditionTextController@five');
        $router->post('/rankingCommercial', 'SearchConditionTextController@rankingCommercial');
    });
});

// authenticate
Route::post('login', 'AuthenticateController@login')->name('login');
Route::post('forgot_password', 'AuthenticateController@sendResetLinkEmail')->name('forgot.password');
Route::post('reset_password', 'AuthenticateController@resetPassword')->name('reset.password');
Route::post('prepare_reset_password', 'AuthenticateController@prepareResetPassword')->name('prepare.resetPassword');

Route::group(['prefix' => 'check'], function (): void {
    Route::get('log', 'CheckController@log');
    Route::get('long-response', 'CheckController@longResponse');
    Route::group(['prefix' => 'connection'], function (): void {
        Route::get('', 'CheckController@connection');
        Route::get('db', 'CheckController@connectionDb');
    });
});

// healthCheck
Route::get('test', 'HealthCheckController@index'); // TODO - kinoshita: ALBのヘルスチェックのパスを/healthcheck.htmlに変更してから削除する
