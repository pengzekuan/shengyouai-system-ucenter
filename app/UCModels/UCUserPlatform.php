<?php

namespace Shengyouai\App\UCModels;

use Illuminate\Database\Eloquent\Model;

class UCUserPlatform extends Model
{
    protected $table = 'uc_u_platform';

    const PLATFORM_WX_MINI_PROGRAM = 1; // 微信小程序平台

    const PLATFORM_WX_OFFICIAL = 2; // 微信公众号平台

    const PLATFORM_WX_OPEN = 3; // 微信开放平台

    const PLATFORM_ZFB = 4; // 支付宝平台
}
