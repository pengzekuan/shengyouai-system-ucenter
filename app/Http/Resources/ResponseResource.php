<?php

namespace Shengyouai\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 请求响应消息
 * Class ResponseResource
 * @package Shengyouai\App\Http\Resources
 */
class ResponseResource extends JsonResource
{
    private $errCode;

    private $errMsg;

    private $data;

    public function __construct($errCode, $errMsg, $data)
    {
        parent::__construct([
            'errCode' => $errCode,
            'errMsg' => $errMsg,
            'data' => $data
        ]);

        $this->errCode = $errCode;
        $this->errMsg = $errMsg;
        $this->data = $data;
    }

    /**
     * 请求成功返回数据
     * @param $data
     * @return $this
     */
    public static function success($data)
    {
        return new self(0, 'ok', $data);
    }

    /**
     * 错误信息提示
     * @param $errCode
     * @param $errMsg
     * @return $this
     */
    public static function error($errCode, $errMsg)
    {
        return new self($errCode, $errMsg, null);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
