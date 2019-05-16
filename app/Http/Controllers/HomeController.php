<?php

namespace App\Http\Controllers;

use Dingo\Api\Dispatcher;
use Dingo\Api\Routing\Helpers;
use Dingo\Api\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     * @param Request $request
     * @param Dispatcher $dispatcher
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Dispatcher $dispatcher)
    {
        $params = [];
        if ($request->has('page')) {
            $params['page'] = $request->get('page');
        }
        if ($request->has('limit')) {
            $params['limit'] = $request->get('limit');
        }
        $token = $request->user()->createToken('Internal Request Token')->accessToken;
        $tasks = $dispatcher->on('todo.test')->header('Authorization', 'Bearer ' . $token)->version('v3')->get('dingoapi/tasks', $params);
        return view('home', ['tasks' => json_encode($tasks->items())]);
    }
}
