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
 * Task Resource Controller
 * @package App\Http\Controllers\Api
 * @Resource("Tasks", uri="/tasks")
 */
class TaskController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @GET("/{?page,limit}")
     * @Versions({"v3"})
     * @Parameters({
     *     @Parameter("page", description="page number", type="integer", required=false, default=1),
     *     @Parameter("limit", description="task item number per page", type="integer", required=false, default=10)
     * })
     * @Request(headers={
     *         "Authorization": "Bearer {API Access Token}"
     * })
     * @Response(200, body={"data":{{"id":1,"text":"Test Task 1","completed":"no","link":"http://todo.test/dingoapi/task/1"}},"meta":{"pagination":{"total":4,"count":1,"per_page":1,"current_page":1,"total_pages":4,"links":{"next":"http://todo.test/dingoapi/tasks?page=2"}}}})
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
     * Store a newly created resource in storage.
     *
     * @param  CreateTaskRequest $request
     * @return \Illuminate\Http\Response
     * @POST("/")
     * @Versions({"v3"})
     * @Request({"text":"test task", "is_completed":0}, headers={
     *     "Authorization": "Bearer {API Access Token}"
     * }, attributes={
     *     @Attribute("text", type="string", required=true, description="the body of task", sample="test task"),
     *     @Attribute("is_completed", type="boolean", required=true, description="task is completed or not", sample=0)
     * })
     * @Response(200, body={"data":{"id":1,"text":"Test Task 1","completed":"no","link":"http://todo.test/dingoapi/task/1"}}, attributes={
     *     @Attribute("id", type="integer", description="the id of task", sample=1),
     *     @Attribute("text", type="string", description="the body of task", sample="test task"),
     *     @Attribute("completed", type="string", description="task is completed or not", sample="no"),
     *     @Attribute("link", type="string", description="task link", sample="http://todo.test/dingoapi/task/1")
     * })
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @GET("/{id}")
     * @Parameters({
     *     @Parameter("id", type="integer", description="the ID of the task", required=true)
     * })
     * @Versions({"v3"})
     * @Transaction({
     *     @Request(headers={
     *         "Authorization": "Bearer {API Access Token}"
     *     }),
     *     @Response(200, body={"data":{"id":1,"text":"Test Task 1","completed":"no","link":"http://todo.test/dingoapi/task/1"}}, attributes={
     *         @Attribute("id", type="integer", description="the id of task", sample=1),
     *         @Attribute("text", type="string", description="the body of task", sample="test task"),
     *         @Attribute("completed", type="string", description="task is completed or not", sample="no"),
     *         @Attribute("link", type="string", description="task link", sample="http://todo.test/dingoapi/task/1")
     *     }),
     *     @Response(404, body={"message":"404 not found", "status_code": 404})
     * })
     */
    public function show($id)
    {
        $task = Task::findOrFail($id);
        return $this->response->item($task, new TaskTransformer());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @PUT("/{id}")
     * @Parameters({
     *     @Parameter("id", type="integer", description="the ID of the task", required=true)
     * })
     * @Versions({"v3"})
     * @Transaction({
     *     @Request({"text":"test task", "is_completed":1}, headers={
     *         "Authorization": "Bearer {API Access Token}"
     *     }, attributes={
     *         @Attribute("text", type="string", required=true, description="the body of task", sample="test task"),
     *         @Attribute("is_completed", type="boolean", required=true, description="task is completed or not", sample=1)
     *     }),
     *     @Response(200, body={"data":{"id":1,"text":"Test Task 1","completed":"no","link":"http://todo.test/dingoapi/task/1"}}, attributes={
     *         @Attribute("id", type="integer", description="the id of task", sample=1),
     *         @Attribute("text", type="string", description="the body of task", sample="test task"),
     *         @Attribute("completed", type="string", description="task is completed or not", sample="no"),
     *         @Attribute("link", type="string", description="task link", sample="http://todo.test/dingoapi/task/1")
     *     }),
     *     @Response(404, body={"message":"404 not found", "status_code": 404})
     * })
     */
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $updatedTask = tap($task)->update(request()->only(['is_completed', 'text']))->fresh();
        return $this->response->item($updatedTask, new TaskTransformer());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @DELETE("/{id}")
     * @Parameters({
     *     @Parameter("id", type="integer", description="the ID of the task", required=true)
     * })
     * @Versions({"v3"})
     * @Transaction({
     *     @Request(headers={
     *         "Authorization": "Bearer {API Access Token}"
     *     }),
     *     @Response(200, body={"message": "Task deleted"}),
     *     @Response(404, body={"message":"404 not found", "status_code": 404})
     * })
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return response()->json(['message' => 'Task deleted'], 200);
    }
}
