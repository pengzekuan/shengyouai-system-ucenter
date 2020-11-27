<?php

namespace Shengyouai\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\Log;
use PHPUnit\Util\Json;

class ApiResource
{
    /**
     * 请求成功响应
     * @param Response $response
     * @param JsonResource $jsonResource
     * @return Response
     */
    public static function success(Response $response, JsonResource $jsonResource)
    {
        return self::sendData($response, 200, $jsonResource);
    }

    /**
     * @param Response $response
     * @param int $statusCode
     * @param JsonResource|null $jsonResource
     * @return Response
     */
    private static function send(Response $response, int $statusCode, JsonResource $jsonResource = null)
    {
        $response->setStatusCode($statusCode);
        if ($jsonResource) {
            $response->setContent($jsonResource);
        }
        return $response;
    }

    /**
     * 发送请求数据
     * @param Response $response
     * @param int $statusCode
     * @param JsonResource $jsonResource
     * @return Response
     */
    private static function sendData(Response $response, int $statusCode, JsonResource $jsonResource)
    {
        return self::send($response, $statusCode, ResponseResource::success($jsonResource));
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

    /**
     * 创建新资源成功响应
     * @param Response $response
     * @param JsonResource $jsonResource
     * @return Response
     */
    public static function created(Response $response, JsonResource $jsonResource)
    {
        return self::sendData($response, 201, $jsonResource);
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
     * 用户没有访问权限
     * @param Response $response
     * @return Response
     */
    public static function unauthorized(Response $response)
    {
        return self::send($response, 401);
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
     * 访问不存在的资源，不希望用户看到的资源统一返回404
     * @param Response $response
     * @return Response
     */
    public static function notfound(Response $response)
    {
        return self::send($response, 404);
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
}
