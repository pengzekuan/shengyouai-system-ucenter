<?php

namespace Shengyouai\App\Services\SMS;

use Carbon\Carbon;
use Shengyouai\App\UCModels\UCSMS;

/**
 * 短信继承方法
 * Trait SMSVerifyService
 * @package Shengyouai\App\Services\SMS
 */
trait SMSVerifyService
{
    public function sendCode($cellphone)
    {
        // 生成验证码
        $code = $this->getVerifyCode();

        // 添加短信记录
        $model = new UCSMS();

        $sms = $model->add($this->_appId, $this->templateId, $cellphone, $code);

        // 发送短信
        $res = $this->sendVerifyCode($cellphone, $code);

        if (count($res)) {
            $sms->sid = $res['sid'];
            $sms->sent = 1;
            $sms->sendTime = Carbon::now()->format(Carbon::DEFAULT_TO_STRING_FORMAT);
            $sms->save();
        }

        return $sms;
    }

    /**
     * 短信验证
     * @param $cellphone
     * @param $code
     * @return boolean
     */
    public function verifyCode($cellphone, $code)
    {
        // 查询短信记录
        $sms = UCSMS::getOne([
            ['cellphone', '=', $cellphone],
            ['content', '=', $code],
            ['sent', '=', 1], // 已发送
            ['used', '=', 0], // 未使用
            ['sendTime', '>', Carbon::now()->addSeconds(-$this->expires_in)->format(Carbon::DEFAULT_TO_STRING_FORMAT)]
        ]);

        if (!$sms) {
            return false;
        }

        $sms->used = 1;
        $sms->save();

        return true;
    }

    /**
     * 验证码生成
     * @param int $len
     * @return string
     */
    public function getVerifyCode($len = 6)
    {
        return str_pad(mt_rand(0, 999999), $len, "0", STR_PAD_LEFT);
    }
}
