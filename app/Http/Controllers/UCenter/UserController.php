<?php

namespace Shengyouai\App\Http\Controllers\UCenter;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Shengyouai\App\Http\Resources\ApiResource;
use Shengyouai\App\Oauth\UCUserAuthorization;
use Shengyouai\App\UCModels\UCUser;
use Shengyouai\App\UCModels\UCUserOauth;

/**
 * 用户数据入口
 * Class UserController
 * @package Shengyouai\App\Http\Controllers\UCenter
 */
class UserController extends UCenterController
{

    /**
     * @param Request $request
     * @param Response $response
     * @return array|Response
     */
    public function oauth(Request $request, Response $response)
    {
        return (new UCUserAuthorization())->oauth($request, $response);
    }

    /**
     * 手机号绑定
     */
    public function bindPhone()
    {

    }

    /**
     * 退出登录
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function logout(Request $request, Response $response)
    {
        // token 验证
        $auth = ApiResource::authorization($request, $response);
        if ($auth instanceof Response) {
            return $auth;
        }

        if ($auth instanceof UCUser) {
            $model = new UCUserOauth();
            $model->disabled($auth->id, $auth->pid);
            return ApiResource::success($response, null);
        }

        return ApiResource::warning($response, '未知错误');
    }

}
