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
 * @OA\Info(
 *     version="3.0",
 *     title="Task Resource OpenApi",
 *     @OA\Contact(
 *         name="学院君",
 *         url="http://xueyuanjun.com",
 *         email="support@todo.test"
 *     )
 * ),
 * @OA\Server(
 *     url="http://todo.test/dingoapi/tasks"
 * ),
 * @OA\SecurityScheme(
 *     type="oauth2",
 *     description="Use a global client_id / client_secret and your email / password combo to obtain a token",
 *     name="passport",
 *     in="header",
 *     scheme="http",
 *     securityScheme="passport",
 *     @OA\Flow(
 *         flow="password",
 *         authorizationUrl="/oauth/authorize",
 *         tokenUrl="/oauth/token",
 *         refreshUrl="/oauth/token/refresh",
 *         scopes={}
 *     )
 * )
 */
class TaskController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @OA\Get(
     *     path="/",
     *     operationId="getTaskList",
     *     tags={"Tasks"},
     *     summary="Get list of tasks",
     *     description="Returns list of tasks",
     *     @OA\Parameter(
     *         name="Accept",
     *         description="Accept header to specify api version",
     *         required=false,
     *         in="header",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         description="The page num of the list",
     *         required=false,
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         description="The item num per page",
     *         required=false,
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="The result of tasks"
     *     ),
     *     security={
     *         {"passport": {}},
     *     }
     * )
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
     * @OA\Post(
     *     path="/",
     *     operationId="newTaskItem",
     *     tags={"Tasks"},
     *     summary="Add New Task",
     *     description="create new task",
     *     @OA\Parameter(
     *         name="Accept",
     *         description="Accept header to specify api version",
     *         required=false,
     *         in="header",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         request="text",
     *         required=true,
     *         description="The text of the task",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         request="is_completed",
     *         required=true,
     *         description="If the task is completed or not",
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="The task item created",
     *         @OA\JsonContent(ref="#/components/schemas/task-transformer")
     *     ),
     *     security={
     *         {"passport": {}},
     *     }
     * )
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
     * @OA\Get(
     *     path="/{id}",
     *     operationId="getTaskItem",
     *     tags={"Tasks"},
     *     summary="Get Task",
     *     description="Get specify task by id",
     *     @OA\Parameter(
     *         name="Accept",
     *         description="Accept header to specify api version",
     *         required=false,
     *         in="header",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         description="The id of the task",
     *         required=true,
     *         in="path",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="The task item",
     *         @OA\JsonContent(ref="#/components/schemas/task-transformer")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="404 not found"
     *     ),
     *     security={
     *         {"passport": {}},
     *     }
     * )
     */
    public function show($id)
    {
        $task = Task::findOrFail($id);
        return $this->response->item($task, new TaskTransformer());
    }

    /**
     * @OA\Put(
     *     path="/{id}",
     *     operationId="updateTaskItem",
     *     tags={"Tasks"},
     *     summary="Update Task",
     *     description="update existed task by id",
     *     @OA\Parameter(
     *         name="Accept",
     *         description="Accept header to specify api version",
     *         required=false,
     *         in="header",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         description="The id of the task",
     *         required=true,
     *         in="path",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         request="task_in_body",
     *         required=true,
     *         description="The task to update",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/task-model"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="The task item updated",
     *         @OA\JsonContent(ref="#/components/schemas/task-transformer")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="404 not found"
     *     ),
     *     security={
     *         {"passport": {}},
     *     }
     * )
     */
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $updatedTask = tap($task)->update(request()->only(['is_completed', 'text']))->fresh();
        return $this->response->item($updatedTask, new TaskTransformer());
    }

    /**
     * @OA\Delete(
     *     path="/{id}",
     *     operationId="deleteTaskItem",
     *     tags={"Tasks"},
     *     summary="Delete Task",
     *     description="delete existed task by id",
     *     @OA\Parameter(
     *         name="Accept",
     *         description="Accept header to specify api version",
     *         required=false,
     *         in="header",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         description="The id of the task",
     *         required=true,
     *         in="path",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="The task is deleted successful"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="404 not found"
     *     ),
     *     security={
     *         {"passport": {}},
     *     }
     * )
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return response()->json(['message' => 'Task deleted'], 200);
    }
}
