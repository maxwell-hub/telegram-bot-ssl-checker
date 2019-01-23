<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => '/v1/subscribers'
], function () {
    /* Get collection of users */
    Route::get('/', 'ApiController@index');

    /* Unsubscribe users */
    Route::delete('/', 'ApiController@unsubscribe');

    /* Send message to selected subscribers */
    Route::post('/send-message', 'ApiController@sendMessage');
});

/* Routes for testing */
Route::group([
    'prefix' => '/v1/test'
], function () {
    /* Seed fake subscribers into DB */
    Route::get('/populate', 'ApiController@populate');

    /* Remove seeded subscribers from DB */
    Route::get('/clean', 'ApiController@clean');
});