<?php

namespace App\Throttles;

use Dingo\Api\Contract\Http\RateLimit\HasRateLimiter;
use Dingo\Api\Http\RateLimit\Throttle\Throttle;
use Dingo\Api\Http\Request;
use Illuminate\Container\Container;

class CustomThrottle extends Throttle implements HasRateLimiter
{
    protected $options = ['limit' => 5, 'expires' => 1];

    /**
     * Attempt to match the throttle against a given condition.
     *
     * @param \Illuminate\Container\Container $container
     *
     * @return bool
     */
    public function match(Container $container)
    {
        return ! $container['api.auth']->check();
    }

    // 通过域名+IP识别客户端
    public function getRateLimiter(Container $app, Request $request)
    {
        return $request->route()->getDomain() . '|' . $request->getClientIp();
    }
}
