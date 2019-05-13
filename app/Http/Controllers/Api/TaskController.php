<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\CreateTaskRequest;
use App\Task;
use App\Transformers\TaskTransformer;
use Dingo\Api\Auth\Auth;
use Dingo\Api\Http\Response;
use Illuminate\Http\Request;
use League\Fractal\Pagination\Cursor;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Serializer\JsonApiSerializer;
use Symfony\Component\HttpFoundation\Cookie;

class TaskController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit') ? : 10;
        // 获取认证用户实例
        $user = $request->user();
        $tasks = Task::where('user_id', $user->id)->paginate($limit);
        return $this->response->paginator($tasks, new TaskTransformer());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTaskRequest $request)
    {
        // 表单验证成功，继续后续处理
        return $this->response->errorUnauthorized();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!is_numeric($id)) {
            return $this->response->errorBadRequest();
        }
        $task = Task::find($id);
        if (!$task) {
            return $this->response->errorNotFound();
        }
        Response::addFormatter('json', new Response\Format\Jsonp);
        return $this->response->item($task, new TaskTransformer());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $this->response->errorForbidden();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->response->errorInternal();
    }
}
