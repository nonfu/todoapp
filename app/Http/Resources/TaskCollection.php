<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TaskCollection extends ResourceCollection
{
    public $collects = \App\Task::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'tasks' => $this->collection->map(function ($task) {
                return $task->only(['id', 'text', 'is_completed']);
            }),
            'user' => $this->collection->first()->user->only(['id', 'name']),
        ];
    }
}
