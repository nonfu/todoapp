<?php

namespace App\Providers;

use App\Task;
use App\Transformers\TaskTransformer;
use Dingo\Api\Transformer\Factory;
use Illuminate\Support\ServiceProvider;

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
    }
}
