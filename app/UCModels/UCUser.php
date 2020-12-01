<?php

namespace Shengyouai\App\UCModels;

use Carbon\Carbon;

class UCUser extends UCModel
{
    const CELLPHONE_PATTERN = '/^[1](([3][0-9])|([4][5-9])|([5][0-3,5-9])|([6][5,6])|([7][0-8])|'
    . '([8][0-9])|([9][1,5,8,9]))[0-9]{8}$/';
    protected $table = 'uc_u_basic';

    protected $hidden = [
        'updated_at',
    ];

    public static function validCellphone($cellphone)
    {
        // 正则校验
        return preg_match(self::CELLPHONE_PATTERN, $cellphone);
    }

    public static function unique($cellphone)
    {
        return (self::count([
                'cellphone' => $cellphone
            ])) === 0;
    }

    public static function findByCellphone($cellphone)
    {
        return self::getOne([
            'cellphone' => $cellphone
        ]);
    }

    public function getCreatedAtAttribute($v)
    {
        return Carbon::parse($v)->format(Carbon::DEFAULT_TO_STRING_FORMAT);
    }

    public function authorizations()
    {
        return $this->hasMany('App\UCModels\UCUserOauth', 'uid');
    }

    public function registry($cellphone, $options = [])
    {
        // 用户信息存储
        $this->cellphone = $cellphone;

        $this->save();

        // 用户来源登记
        if ($options && !empty($options['cid'])) {
            $cId = $options['cId']; // 来源id标记
            $cType = isset($options['ct']) ? $options['ct'] : 0; // 来源类型
            $scene = isset($options['scene']) ? $options['scene'] : null; // 来源场景值

            $ucChannelModel = new UCUserChannel();
            $ucChannelModel->uid = $this->id;
            $ucChannelModel->type = $cType;
            $ucChannelModel->appId = $cId;
            $ucChannelModel->scene = $scene;
            $ucChannelModel->save();
        }

        // 注册用户登录
        $oauth = new UCUserOauth();
        $oauth->add($this->id, $cellphone, $options['pid'], $options['clientIp'], $options['device'], $options['network']);

        $this->oauth = $oauth;

        return $this;
    }
}
