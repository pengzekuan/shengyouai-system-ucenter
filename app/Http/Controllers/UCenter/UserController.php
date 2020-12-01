<?php

namespace Shengyouai\App\Http\Controllers\UCenter;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Shengyouai\App\Http\Resources\ApiResource;
use Shengyouai\App\Http\Resources\Model\UCUserResource;
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

        unset($params['cellphone']);

        // 手机号正则验证
        if (!UCUser::validCellphone($cellphone)) {
            return ApiResource::warning($response, '手机号格式有误');
        }

        // 手机唯一性验证
        if (!UCUser::unique($cellphone)) {
            return ApiResource::warning($response, '该手机号已被他人注册，请更换注册手机号');
        }

        // TODO 短信验证

        $params['pid'] = 0;
        $params['clientIp'] = ApiResource::getClientIp($request);
        $params['device'] = ApiResource::getDevice($request);
        $params['network'] = ApiResource::getNetwork($request);

        $user = new UCUser();
        $user = $user->registry($cellphone, $params);

        return ApiResource::created($response, new UCUserResource($user));
    }

    /**
     * 用户登录
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function login(Request $request, Response $response)
    {
        // 参数校验
        $params = $request->input();
        // 手机号登录
        $cellphone = !empty($params['cellphone']) ? $params['cellphone'] : null;

        unset($params['cellphone']);

        // 手机号正则验证
        if (!UCUser::validCellphone($cellphone)) {
            return ApiResource::warning($response, '手机号格式有误');
        }

        // TODO 短信验证

        // 查询用户
        $find = UCUser::findByCellphone($cellphone);

        // 手机号登录即注册
        if (!$find) {
            $user = new UCUser();
            $user = $user->registry($cellphone, $params);
            return ApiResource::success($response, new UCUserResource($user));
        }

        // 查询登录状态
        $oauth = UCUserOauth::check($find->id);

        if (!$oauth) {
            // 授权
            $oauth = new UCUserOauth();
            $oauth->add(
                $find->id,
                0,
                ApiResource::getClientIp($request),
                ApiResource::getDevice($request),
                ApiResource::getNetwork($request)
            );
        }

        $find->oauth = $oauth;

        return ApiResource::success($response, new UCUserResource($find));
    }

    /**
     * 三方授权登录 支持微信公众号、小程序登录，
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function thirtyLogin(Request $request, Response $response)
    {
        $params = $request->input();
        $platform = isset($params['p']) ? $params['p'] : 0;
        if ($platform === UCUserOauth::PLATFORM_SELF) {
            return (new UCUserAuthorization())->oauth();
        }

        if ($platform === UCUserOauth::PLATFORM_WX_MINI) {
            return (new UCUserAuthorization())->wxMiniOauth();
        }

        if ($platform === UCUserOauth::PLATFORM_WX_OFFICIAL) {
            return (new UCUserAuthorization())->wxMiniOauth();
        }

        $response->setContent('授权成功')->setStatusCode(200)->send();
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
