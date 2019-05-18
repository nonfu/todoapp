<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\CreateTaskRequest;
use App\Task;
use App\Transformers\TaskTransformer;
use Dingo\Api\Auth\Auth;
use Dingo\Api\Dispatcher;
use Dingo\Api\Http\Response;
use Illuminate\Http\Request;
use League\Fractal\Pagination\Cursor;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Serializer\JsonApiSerializer;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * APIs For Task Resources
 * @package App\Http\Controllers\Api
 * @group 任务管理
 */
class TaskController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Task List
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @authenticated
     * @queryParam page required The number of the page. Example:1
     * @queryParam limit required Task items per page.Example:10
     * @responseFile responses/tasks.list.json
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit') ? : 10;
        // 获取认证用户实例
        $user = $request->user('api');
        $tasks = Task::where('user_id', $user->id)->paginate($limit);
        return $this->response->paginator($tasks, new TaskTransformer());
    }

    /**
     * New Task
     *
     * @param  CreateTaskRequest $request
     * @return \Illuminate\Http\Response
     * @authenticated
     * @bodyParam text string required the body of task. Example: Test Task
     * @bodyParam is_completed boolean required task is completed or not. Example: 0
     * @responseFile responses/task.get.json
     */
    public function store(CreateTaskRequest $request)
    {
        $request->validate([
            'text' => 'required|string'
        ]);

        $task = Task::create([
            'text' => $request->post('text'),
            'user_id' => auth('api')->user()->id,
            'is_completed' => Task::NOT_COMPLETED
        ]);

        return $this->response->item($task, new TaskTransformer());
    }

    /**
     * Task Detail
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @authenticated
     * @queryParam id required The id of the task. Example: 1
     * @responseFile responses/task.get.json
     * @responseFile 404 responses/task.not_found.json
     */
    public function show($id)
    {
        $task = Task::findOrFail($id);
        return $this->response->item($task, new TaskTransformer());
    }

    /**
     * Update Task
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @authenticated
     * @queryParam id required The id of the task. Example:1
     * @bodyParam text string required the body of task. Example: Test Task
     * @bodyParam is_completed boolean required task is completed or not. Example: 1
     * @responseFile responses/task.get.json
     * @responseFile 404 responses/task.not_found.json
     */
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $updatedTask = tap($task)->update(request()->only(['is_completed', 'text']))->fresh();
        return $this->response->item($updatedTask, new TaskTransformer());
    }

    /**
     * Delete Task
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @authenticated
     * @queryParam id required The id of the task. Example: 1
     * @response {"message": "Task deleted"}
     * @response 404 {"message":"404 not found", "status_code": 404}
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return response()->json(['message' => 'Task deleted'], 200);
    }
}
