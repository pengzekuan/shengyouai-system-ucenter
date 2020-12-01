<?php

namespace Shengyouai\App\UCModels;

class UCUserChannel extends UCModel
{
    protected $table = 'uc_u_channel';

    const CHANNEL_UNKNOWN = -1; // 未定义

    const CHANNEL_CUSTOM_SHARE = 0; // 客户分享

    const CHANNEL_WX_MINI = 1; // 小程序应用

    const CHANNEL_WX_OFFICIAL = 2; // 公众号

    const CHANNEL_WX_OPEN = 3; // 三方平台
}
