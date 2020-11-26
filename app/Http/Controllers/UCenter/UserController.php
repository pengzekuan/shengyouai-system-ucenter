<?php
namespace Shengyouai\App\Http\Controllers\UCenter;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * 用户数据入口
 * Class UserController
 * @package Shengyouai\App\Http\Controllers\UCenter
 */
class UserController extends UCenterController
{
    /**
     * 用户注册
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function registry(Request $request, Response $response)
    {
        $response->setContent('注冊成功')->setStatusCode(200)->send();
    }

    /**
     * 用户登录
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function login(Request $request, Response $response)
    {
        $response->setContent('登录成功')->setStatusCode(200)->send();
    }

    /**
     * 用户登录
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function thirtyLogin(Request $request, Response $response)
    {
        $response->setContent('授权成功')->setStatusCode(200)->send();
    }

    /**
     * 退出登录
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function logout(Request $request, Response $response)
    {
        $response->setContent('退出成功')->setStatusCode(200)->send();
    }

}
