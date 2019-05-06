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

\Laravel\Passport\Passport::ignoreCsrfToken(true);
Route::middleware('auth:api')->group(function (){
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::resource('tasks', 'TaskController', ['except' => ['create', 'edit']]);
});

// Dingo API Endpoint
$api = app(\Dingo\Api\Routing\Router::class);

$api->version('v1', function ($api) {
    $api->get('/task/{id}', function ($id) {
        return \App\Task::findOrFail($id);
    })->name('task.detail');
    $api->get('/task/{id}/{url}', function ($id) {
        $url = app(\Dingo\Api\Routing\UrlGenerator::class)
            ->version('v1')
            ->route('task.detail', ['id' => $id]);
        return $url;
    });
});

$api->version('v2', function ($api) {
    $api->get('/task/{id}', function ($id) {
        return \App\Task::findOrFail($id);
    });
    $api->get('/task/{id}/{url}', function ($id) {
        $url = app(\Dingo\Api\Routing\UrlGenerator::class)
            ->version('v2')
            ->route('task.detail', ['id' => $id]);
        return $url;
    });
});


