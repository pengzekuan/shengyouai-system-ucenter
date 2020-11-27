<?php
namespace Shengyouai\App\Http\Controllers\UCenter;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Shengyouai\App\Http\Resources\ApiResource;
use Shengyouai\App\Http\Resources\Model\UCUserResource;
use Shengyouai\App\Http\Resources\TestResource;
use Shengyouai\App\UCModels\UCUser;

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
     * @return Response
     */
    public function registry(Request $request, Response $response)
    {
        // 参数处理
        $params = $request->input();

        $cellphone = isset($params['cellphone']) ? $params['cellphone'] : null;

        // 手机号正则验证
        if (!UCUser::validCellphone($cellphone)) {
            return ApiResource::warning($response, '手机号格式有误');
        }

        // 手机唯一性验证
        if (!UCUser::unique($cellphone)) {
            return ApiResource::warning($response, '该手机号已被他人注册，请更换注册手机号');
        }

        // TODO 短信验证

        $user = new UCUser();
        $user = $user->registry($cellphone);

        return ApiResource::created($response, new UCUserResource($user));
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
