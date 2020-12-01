<?php

namespace Shengyouai\App\Http\Resources;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Shengyouai\App\Oauth\UCUserAuthorization;
use Shengyouai\App\UCModels\UCUser;
use Shengyouai\App\UCModels\UCUserOauth;
use UnexpectedValueException;

class ApiResource
{
    /**
     * 请求成功响应
     * @param Response $response
     * @param mixed $data
     * @return Response
     */
    public static function success(Response $response, $data)
    {
        return self::sendData($response, 200, $data);
    }

    /**
     * 发送请求数据
     * @param Response $response
     * @param int $statusCode
     * @param mixed $data
     * @return Response
     */
    private static function sendData(Response $response, int $statusCode, $data)
    {
        if (!$data) {
            return self::send($response, $statusCode);
        }

        return self::send($response, $statusCode, ResponseResource::success($data));
    }

    /**
     * @param Response $response
     * @param int $statusCode
     * @param ResponseResource|null $responseResource
     * @return Response
     */
    private static function send(Response $response, int $statusCode, ResponseResource $responseResource = null)
    {
        $response->setStatusCode($statusCode);
        if ($responseResource) {
            $response->setContent($responseResource);
        }
        return $response;
    }

    /**
     * 创建新资源成功响应
     * @param Response $response
     * @param mixed $data
     * @return Response
     */
    public static function created(Response $response, $data)
    {
        return self::sendData($response, 201, $data);
    }

    /**
     * 请求成功，无返回，需重置视图
     * @param Response $response
     * @return Response
     */
    public static function updated(Response $response)
    {
        return self::send($response, 201);
    }

    /**
     * 请求参数有误，提示用户信息
     * @param Response $response
     * @param string $errMsg
     * @param string $errCode
     * @return Response
     */
    public static function warning(Response $response, string $errMsg, string $errCode = '')
    {
        $errCode = $errCode ?: 'USER_NOTIFICATION';
        $errMsg = $errMsg ?: '用户提示';
        return self::sendMsg($response, 400, $errCode, $errMsg);
    }

    /**
     * 发送提示消息
     * @param Response $response
     * @param int $statusCode
     * @param string $errCode
     * @param string $errMsg
     * @return Response
     */
    private static function sendMsg(Response $response, int $statusCode, string $errCode, string $errMsg)
    {
        return self::send($response, $statusCode, ResponseResource::error($errCode, $errMsg));
    }

    public static function authorization(Request $request, Response $response)
    {
        $authorization = $request->header(UCUserOauth::AUTHORIZATION_TYPE);
        $pattern = '/^' . UCUserOauth::AUTHORIZATION_PREFIX . '(.*)/';
        $match = preg_match($pattern, $authorization, $arr);

        if (!$match || empty($arr[1])) {
            return self::notfound($response);
        }

        try {
            $token = ltrim($arr[1]);

            // 密钥解码
            $authorization = new UCUserAuthorization();
            $payload = $authorization->decode($token);

            // 授权平台标识
            $pid = property_exists($payload, 'pid') ? $payload->pid : 0;

            // 数据库查询用户

            if (!property_exists($payload, 'uid')) {
                return self::notfound($response);
            }

            $userId = $payload->uid;

            $user = UCUser::find($userId);

            if (!$user) {
                return self::notfound($response);
            }

            $oauth = UCUserOauth::findByUserId($userId, $pid);

            // 检测超时
            if (!$oauth || !$oauth->sessionKey || $oauth->sessionKey !== $token) { // 登录失效
                return self::unauthorized($response);
            }

            if (Carbon::parse($oauth->accessDeadline)->isBefore(Carbon::now())) { // 登录超时
                return self::unauthorized($response);
            }

            $user->pid = $pid;

            $user->oauth = $oauth;

            return $user;
        } catch (UnexpectedValueException $unexpectedValueException) {
            return self::error($request, $response, $unexpectedValueException);
        } catch (Exception $exception) {
            return self::error($request, $response, $exception);
        }
    }

    /**
     * 访问不存在的资源，不希望用户看到的资源统一返回404
     * @param Response $response
     * @return Response
     */
    public static function notfound(Response $response)
    {
        return self::send($response, 404);
    }

    /**
     * 用户没有访问权限
     * @param Response $response
     * @return Response
     */
    public static function unauthorized(Response $response)
    {
        return self::send($response, 401);
    }

    /**
     * 服务器异常，或者程序设计缺陷导致的问题
     * @param Response $response
     * @param Request $request
     * @param Exception $exception
     * @return Response
     */
    public static function error(Request $request, Response $response, Exception $exception)
    {
        // 后台记录错误日志
        if ($exception) {
            Log::error('SERVER_ERROR', [
                'path' => $request->path(),
                'params' => $request->all(),
                'headers' => $request->headers,
                'errCode' => $exception->getCode(),
                'errMsg' => $exception->getMessage()
            ]);
        }
        return self::sendMsg($response, 500, 'SERVER_ERROR', '服务器错误');
    }

    /***
     * @param Request $request
     * @return string
     */
    public static function getClientIp(Request $request)
    {
        return $request->getClientIp();
    }

    /**
     * 获取设备号
     * @param Request $request
     * @return string
     */
    public static function getDevice(Request $request)
    {
        return '';
    }

    /**
     * 获取网络
     * @param Request $request
     * @return string
     */
    public static function getNetwork(Request $request)
    {
        return '';
    }

    public static function redirect(Response $response, $url)
    {
        return header('Location:' . $url);
    }
}
