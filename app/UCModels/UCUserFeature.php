<?php

namespace Shengyouai\App\UCModels;

use Illuminate\Database\Eloquent\Model;

class UCUserFeature extends UCModel
{
    protected $table = 'uc_u_feature';

    protected $fillable = ['uid', 'nickName', 'avatar', 'sex', 'idType', 'realName', 'idCard'];

    const SEX_MALE = 1; // 性别 男

    const SEX_FEMALE = 2; // 性别 女

    const SEX_UNKNOWN = 0; // 未知性别

    const ID_CARD_TYPE = 1; // 身份证

    const PASSPORT_TYPE = 2; // 护照

    public static function findByUserId($uid)
    {
        return self::getOne([
            ['uid', '=', $uid]
        ]);
    }


}
