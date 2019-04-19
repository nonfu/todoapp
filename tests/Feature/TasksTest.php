<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Task;
use App\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TasksTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * 测试认证用户可以创建任务
     */
    public function testUserCanCreateTask()
    {
        $user = factory(User::class)->create();
        $task = [
            'text' => 'New task text',
            'user_id' => $user->id
        ];

        Passport::actingAs($user, ['*']);
        $response = $this->json('POST', 'api/task', $task);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', $task);
    }

    /**
     * 测试访客不能创建任务
     */
    public function testGuestCantCreateTask()
    {
        $task = [
            'text' => 'new text',
            'user_id' => 1
        ];

        $response = $this->json('POST', 'api/task', $task);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('tasks', $task);
    }

    /**
     * 测试认证用户可以删除任务
     */
    public function testUserCanDeleteTask()
    {
        $user = factory(User::class)->create();
        $task = factory(Task::class)->create([
            'text' => 'task to delete',
            'user_id' => $user->id
        ]);

        Passport::actingAs($user, ['*']);
        $response = $this->json('DELETE', "api/task/$task->id");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /**
     * 测试认证用户可以完成任务
     */
    public function testUserCanCompleteTask()
    {
        $user = factory(User::class)->create();
        $task = factory(Task::class)->create([
            'text' => 'task to complete',
            'user_id' => $user->id
        ]);

        Passport::actingAs($user, ['*']);
        $response = $this->json('PUT', "api/task/$task->id", ['is_completed' => Task::IS_COMPLETED]);

        $response->assertStatus(200);
        $this->assertNotNull($task->fresh()->is_completed);
    }
}
