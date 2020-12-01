<?php

namespace Shengyouai\App\UCModels;

use Carbon\Carbon;

class UCUserPlatform extends UCModel
{
    const PLATFORM_WX_MINI_PROGRAM = 1;
    const PLATFORM_WX_OFFICIAL = 2; // 微信小程序平台
    const PLATFORM_WX_OPEN = 3; // 微信公众号平台
    const PLATFORM_ZFB = 4; // 微信开放平台
    protected $table = 'uc_u_platform'; // 支付宝平台

    public static function findByOpenId($openId)
    {
        return self::getOne([
            ['openId', '=', $openId]
        ]);
    }

    /**
     * 三方授权注册
     * @param $uid
     * @param $appId
     * @param $openId
     * @param $sessionKey
     * @param array $options
     * @return $this
     */
    public function registry($uid, $appId, $openId, $sessionKey, $options = [])
    {
        $this->uid = $uid;
        $this->appId = $appId;
        $this->openId = $openId;
        $this->sessionKey = $sessionKey;

        $this->platformId = isset($options['platformId'])
            ? intval($options['platformId']) : UCUserPlatform::PLATFORM_WX_MINI_PROGRAM;
        $this->subscribe = isset($options['subscribe']) ? $options['subscribe'] : 0;
        $this->remark = isset($options['remark']) ? $options['remark'] : '';

        $this->accessDateTime = isset($options['accessDateTime']) ? $options['accessDateTime']
            : Carbon::now()->format(Carbon::DEFAULT_TO_STRING_FORMAT);
        $this->accessDeadline = isset($options['accessDeadline']) ? $options['accessDeadline']
            : Carbon::now()->addHours(2)->format(Carbon::DEFAULT_TO_STRING_FORMAT);
        $this->nickName = !empty($options['nickName']) ? $options['nickName'] : '';
        $this->avatar = !empty($options['avatar']) ? $options['avatar'] : '';
        $this->sex = !empty($options['sex']) ? intval($options['sex']) : UCUserFeature::SEX_UNKNOWN;

        $this->save();

        if (!UCUserFeature::findByUserId($uid) && $this->nickName) {
            $model = new UCUserFeature();
            $model->add([
                'nickName' => $this->nickName,
                'avatar' => $this->avatar,
                'sex' => $this->sex
            ]);
        }

        return $this;
    }
}
