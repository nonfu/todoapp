<?php

namespace App\Transformers;

use App\Task;
use League\Fractal\TransformerAbstract;

class TaskTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user'];

    public function transform(Task $task)
    {
        return [
            'id' => $task->id,
            'text' => $task->text,
            'completed' => $task->is_completed ? 'yes' : 'no',
            'link' => app(\Dingo\Api\Routing\UrlGenerator::class)->version('v1')->route('task.detail', ['id' => $task->id])
        ];
    }

    public function includeUser(Task $task)
    {
        $user = $task->user;
        return $this->item($user, new UserTransformer());
    }
}
