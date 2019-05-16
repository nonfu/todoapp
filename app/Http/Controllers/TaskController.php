<?php

namespace App\Http\Controllers;

use App\Events\TaskStatusUpdated;
use App\Http\Resources\TaskCollection;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::where('user_id', auth('api')->user()->id)->with('user')->paginate(10);
        return new TaskCollection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required'
        ]);


        return Task::create([
            'text' => $request->post('text'),
            'user_id' => auth('api')->user()->id,
            'is_completed' => Task::NOT_COMPLETED
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new \App\Http\Resources\Task(Task::with(['user' => function($query) {
            $query->select('id', 'name');
        }])->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Task $task)
    {
        $task = tap($task)->update(request()->only(['is_completed', 'text']))->fresh();
        event(new TaskStatusUpdated($task));
        return $task;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(['message' => 'Task deleted'], 200);
    }
}
