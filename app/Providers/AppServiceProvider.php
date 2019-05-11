<?php

namespace App\Providers;

use App\Task;
use App\Transformers\TaskTransformer;
use Dingo\Api\Exception\Handler;
use Dingo\Api\Transformer\Factory;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // app(Factory::class)->register(Task::class, TaskTransformer::class);
        app(\Dingo\Api\Exception\Handler::class)->register(function (\Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException $exception) {
            return \Illuminate\Support\Facades\Response::make(['error' => 'Hey, what do you think you are doing!?'], 401);
        });
    }
}
