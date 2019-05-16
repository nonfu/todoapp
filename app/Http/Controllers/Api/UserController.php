<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends ApiController
{
    // 获取 Json Web Token
    public function getTokenByJwt(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                throw new UnauthorizedHttpException('Invalid credentials');
            }
        } catch (JWTException $e) {
            throw new UnauthorizedHttpException('Create token failed');
        }

        return compact('token');
    }

    // 通过 OAuth 密码授权获取令牌
    public function getTokenByOauth(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $http = new Client();
        // 发送相关字段到后端应用获取授权令牌
        $response = $http->post(route('passport.token'), [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => env('CLIENT_ID'),
                'client_secret' => env('CLIENT_SECRET'),
                'username' => app('request')->input('email'),  // 这里传递的是邮箱
                'password' => app('request')->input('password'), // 传递密码信息
                'scope' => '*'
            ],
        ]);

        return response()->json($response->getBody()->getContents());
    }
}
