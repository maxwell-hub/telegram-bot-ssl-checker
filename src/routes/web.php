<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('telegram-bot', 'TelegramBotController@run');

Route::get('/kernel-populate-subscribers', function (\Illuminate\Http\Request $request) {
    $count = $request->get('count', 10);
    \Illuminate\Support\Facades\Artisan::call('app:populate_subscribers', [
        '--count' => $count
    ]);
});

Route::get('/kernel-clear-subscribers', function () {
    \Illuminate\Support\Facades\Artisan::call('app:clear_subscribers');
});

Route::get('/test-db', function () {
    dd(
        \App\Subscriber::all()
    );
});