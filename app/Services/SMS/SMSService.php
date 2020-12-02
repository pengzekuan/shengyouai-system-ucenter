<?php
namespace Shengyouai\App\Services\SMS;

/**
 * 短信验证码
 * Class SMSService
 * @package Shengyouai\App\Services
 */
interface SMSService
{

    /**
     * 发送短信验证码
     * @param $cellphone
     * @param $code
     * @return array
     */
    public function sendVerifyCode($cellphone, $code) : array;

    /**
     * 发送短信
     * @param $type
     * @param $nationCode
     * @param $phoneNumber
     * @param $msg
     * @param string $extend
     * @param string $ext
     * @return mixed
     */
    public function send($type, $nationCode, $phoneNumber, $msg, $extend = "", $ext = "");

    /**
     * @param $nationCode
     * @param $phoneNumber
     * @param $templId
     * @param $params
     * @param string $sign
     * @param string $extend
     * @param string $ext
     * @return mixed
     */
    public function sendWithParam($nationCode, $phoneNumber, $templId, $params,
                                  $sign = "", $extend = "", $ext = "");
}
