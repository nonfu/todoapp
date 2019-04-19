<?php

namespace Tests\Browser;

use App\Task;
use App\User;
use Laravel\Passport\Passport;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TasksTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @var User
     */
    protected $user;

    /**
     * 通过模型工厂初始化测试用户
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    /**
     * 测试创建任务
     * @throws \Throwable
     */
    public function testCreateTask()
    {
        $this->browse(function (Browser $browser) {
            // 以认证用户身份访问待办任务首页
            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Tasks');

            /**
             * 测试新增一个待办任务：
             * 输入「First Task」-> 点击提交「Add」-> 提交成功后断言列表里出现刚刚新增的任务
             */
            $browser
                ->waitForText('Tasks')
                ->type('@task-input', 'First Task')
                ->click('@task-submit')
                ->waitForText('First Task')
                ->assertSee('First Task');

            /**
             * 测试新增第二个任务
             */
            $browser->type('@task-input', 'Second Task')
                ->press('@task-submit')
                ->waitForText('Second Task')
                ->assertSee('Second Task');

            // 断言数据库是否包含刚刚新增的任务
            $this->assertDatabaseHas('tasks', ['text' => 'First Task']);
            $this->assertDatabaseHas('tasks', ['text' => 'Second Task']);
        });
    }

    /**
     * 测试移除任务
     * @throws \Throwable
     */
    public function testRemoveTask()
    {
        // 使用模型工厂创建一个待测试任务「Test Task」
        $task = factory(Task::class)->create([
            'text' => 'Test Task',
            'user_id' => $this->user->id
        ]);

        $this->browse(function (Browser $browser) {
            // 以认证用户身份访问首页
            $browser->loginAs($this->user)
                ->visit('/home')
                ->waitForText('Tasks');

            // 点击移除任务按钮，0.5秒后断言任务是否已删除（对应任务不存在）
            $browser->click("@remove-task1")
                ->pause(500)
                ->assertDontSee('Test Task');
        });

        // 断言数据库不包含对应任务确认后端删除成功
        $this->assertDatabaseMissing('tasks', $task->only(['id', 'text']));
    }

    /**
     * 测试完成任务（修改）
     * @throws \Throwable
     */
    public function testCompleteTask()
    {
        // 还是使用模型工厂创建一个测试任务
        $task = factory(Task::class)->create(['user_id' => $this->user->id]);

        $this->browse(function (Browser $browser) use ($task) {
            // 以认证用户身份访问首页
            $browser->loginAs($this->user)
                ->visit('/home')
                ->waitForText('Tasks');

            // 勾选任务已完成，如果 `line-through` 选择器出现则说明操作成功
            $browser->click("@check-task{$task->first()->id}")
                ->waitFor('.line-through');
        });

        // 断言数据库已完成任务不为空来确认后端数据库记录已更新
        $this->assertNotEmpty($task->fresh()->is_completed);
    }
}
