<?php


namespace Shengyouai\App\Services\SMS;

use Illuminate\Support\Facades\Log;
use Qcloud\Sms\SmsSingleSender;

class QCloudSMSService extends SmsSingleSender implements SMSService
{

    use SMSVerifyService;

    private $_appId;
    /**
     * 短信服务签名
     * @var $appSign
     */
    private $appSign;

    /**
     * 短信验证码模板
     * @var $templateId
     */
    private $templateId;

    private $expires_in;

    /**
     * 构造函数
     * QCloudSMSService constructor.
     * @param $appid
     * @param $appkey
     * @param $appSign
     * @param $templateId
     * @param int $expires_in
     */
    public function __construct($appid, $appkey, $appSign, $templateId, $expires_in = 300)
    {
        parent::__construct($appid, $appkey);

        $this->_appId = $appid;

        $this->appSign = $appSign;

        $this->templateId = $templateId;

        $this->expires_in = $expires_in;
    }

    /**
     * @param $cellphone
     * @param string $code
     * @return array
     */
    public function sendVerifyCode($cellphone, $code = ''): array
    {
        $code = $code ?: $this->getVerifyCode();
        $res = $this->sendWithParam("86", $cellphone, $this->templateId, [$code], $this->appSign, "", "");
        $rsp = json_decode($res);
        Log::debug('sendVerifyCode', [
            'cellphone' => $cellphone,
            'code' => $code,
            'rsp' => $rsp
        ]);
        if ($rsp->result === 0 && $rsp->errmsg === 'OK') {
            return [
                'appId' => $this->_appId,
                'cellphone' => $cellphone,
                'content' => $code,
                'sid' => $rsp->sid
            ];
        }

        return [];
    }
}
