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

Route::get('/fractal/resource/item', function () {
    $task = \App\Task::findOrFail(1);
    $resource = new \League\Fractal\Resource\Item($task, function (\App\Task $task) {
        return [
            'id' => $task->id,
            'text' => $task->text,
            'is_completed' => $task->is_completed ? 'yes' : 'no'
        ];
    });
    $fractal = new \League\Fractal\Manager();
    return $fractal->createData($resource)->toJson();
});

Route::get('/fractal/resource/collection', function () {
    $tasks = \App\Task::all();
    $resource = new \League\Fractal\Resource\Collection($tasks, function (\App\Task $task) {
        return [
            'id' => $task->id,
            'text' => $task->text,
            'is_completed' => $task->is_completed ? 'yes' : 'no'
        ];
    });
    $fractal = new \League\Fractal\Manager();
    return $fractal->createData($resource)->toJson();
});

Route::get('/fractal/serializers', function () {
    $task = \App\Task::findOrFail(1);
    $resource = new \League\Fractal\Resource\Item($task, function (\App\Task $task) {
        return [
            'id' => $task->id,
            'text' => $task->text,
            'is_completed' => $task->is_completed ? 'yes' : 'no'
        ];
    });
    $fractal = new \League\Fractal\Manager();
    $fractal->setSerializer(new \League\Fractal\Serializer\JsonApiSerializer());
    return $fractal->createData($resource)->toJson();
});

Route::get('/fractal/transformer', function () {
    $task = \App\Task::findOrFail(1);
    $resource = new \League\Fractal\Resource\Item($task, new \App\Transformers\TaskTransformer());
    $tasks = \App\Task::all();
    $resources = new \League\Fractal\Resource\Collection($tasks, new \App\Transformers\TaskTransformer());
    $fractal = new \League\Fractal\Manager();
    return $fractal->parseIncludes('user')->createData($resource)->toJson();
});

Route::get('fractal/paginator', function () {
    $paginator = \App\Task::paginate();
    $tasks = $paginator->getCollection();

    $resource = new \League\Fractal\Resource\Collection($tasks, new \App\Transformers\TaskTransformer());
    $resource->setPaginator(new \League\Fractal\Pagination\IlluminatePaginatorAdapter($paginator));

    $fractal = new \League\Fractal\Manager();
    return $fractal->createData($resource)->toJson();
});

Route::get('fractal/cursor', function (Request $request) {
    $current = $request->input('current');
    $previous = $request->input('previous');
    $limit = $request->input('limit', 10);

    if ($current) {
        $tasks = \App\Task::where('id', '>', $current)->take($limit)->get();
    } else {
        $tasks =\App\Task::take($limit)->get();
    }

    $next = $tasks->last()->id;
    $cursor = new \League\Fractal\Pagination\Cursor($current, $previous, $next, $tasks->count());

    $resource = new \League\Fractal\Resource\Collection($tasks, new \App\Transformers\TaskTransformer());
    $resource->setCursor($cursor);

    $fractal = new \League\Fractal\Manager();
    return $fractal->createData($resource)->toJson();
});

// Dingo API Endpoint
$api = app(\Dingo\Api\Routing\Router::class);

$api->version('v1', function ($api) {
    $api->get('/task/{id}', function ($id) {
        return \App\Task::findOrFail($id);
    })->name('task.detail');
    $api->get('/task/{id}/url', function ($id) {
        $url = app(\Dingo\Api\Routing\UrlGenerator::class)
            ->version('v1')
            ->route('task.detail', ['id' => $id]);
        return $url;
    });
    $api->get('/fractal/transformer', function () {
        $task = \App\Task::findOrFail(1);
        $resource = new \League\Fractal\Resource\Item($task, new \App\Transformers\TaskTransformer());
        $fractal = new \League\Fractal\Manager();
        return $fractal->parseIncludes('user')->createData($resource)->toJson();
    });
    $api->put('task/{id}', function ($id) {
        $task = \App\Task::find($id);

        if ($task->updated_at > app('request')->get('last_updated')) {
            throw new \Symfony\Component\HttpKernel\Exception\ConflictHttpException('Task was updated prior to your request.');
        }

        // No error, we can continue to update the user as per usual.
    });
    $api->post('tasks', function () {
        throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException('Basic');
        $rules = [
            'text' => ['required', 'string'],
            'is_completed' => ['required', 'boolean']
        ];

        $payload = app('request')->only('text', 'is_completed');

        $validator = app('validator')->make($payload, $rules);

        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Could not create new task.', $validator->errors());
        }

        // Create user as per usual.
    });
});

$api->version('v2', function ($api) {
    $api->get('/task/{id}', function ($id) {
        return \App\Task::findOrFail($id);
    });
});

$api->version('v3', function ($api) {
    $api->post('user/auth', function () {
        $credentials = app('request')->only('email', 'password');
        try {
            if (! $token = \Tymon\JWTAuth\Facades\JWTAuth::attempt($credentials)) {
                throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException('Invalid credentials');
            }
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException('Create token failed');
        }

        return compact('token');
    });
    $api->post('user/token', ['middleware' => 'api.throttle', function () {
        app('request')->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $http = new \GuzzleHttp\Client();
        // 发送相关字段到后端应用获取授权令牌
        $response = $http->post(route('passport.token'), [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => env('CLIENT_ID'),
                'client_secret' => env('CLIENT_SECRET'),
                'username' => app('request')->input('email'),  // 这里传递的是邮箱
                'password' => app('request')->input('password'), // 传递密码信息
                'scope' => '*'
            ],
        ]);

        return response()->json($response->getBody()->getContents());
    }]);
    $api->resource('tasks', \App\Http\Controllers\Api\TaskController::class);
});
