<?php

namespace App\Providers;

use Dingo\Api\Auth\Auth;
use Dingo\Api\Auth\Provider\Basic;
use Dingo\Api\Auth\Provider\JWT;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Tymon\JWTAuth\JWTAuth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
        // API 认证路由注册
        Passport::routes();

        // Dingo 认证驱动注册
        /*$this->app->make(Auth::class)->extend('basic', function ($app) {
            return new Basic($app['auth'], 'email');
        });

        $this->app->make(Auth::class)->extend('jwt', function ($app) {
            return new JWT($app[JWTAuth::class]);
        });*/
    }
}
