<?php

namespace App\Transformers;

use App\Task;
use League\Fractal\TransformerAbstract;

/**
 * @OA\Schema(
 *     schema="task-transformer",
 *     type="object",
 *     title="Task Transformer"
 * )
 */
class TaskTransformer extends TransformerAbstract
{
    /**
     * The id of the task
     * @var integer
     * @OA\Property(format="int64", example=1)
     */
    public $id;
    /**
     * The text of the task
     * @var string
     * @OA\Property(format="string", example="Test Task")
     */
    public $text;
    /**
     * If the task is completed or not
     * @var string
     * @OA\Property(format="string", example="yes")
     */
    public $completed;
    /**
     * The URL of the task detail page
     * @var string
     * @OA\Property(format="string", example="http://todo.test/dingoapi/task/1")
     */
    public $link;

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
