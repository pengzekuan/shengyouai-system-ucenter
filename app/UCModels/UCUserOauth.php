<?php

namespace Shengyouai\App\UCModels;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Shengyouai\App\Oauth\UCUserAuthorization;

class UCUserOauth extends UCModel
{
    protected $table = 'uc_u_oauth';

    /**
     * 检测用户登录状态
     * @param $uid
     * @param $pid
     * @return object|false
     */
    public static function check($uid, $pid = 0)
    {
        $find = self::getOne([
            'uid' => $uid,
            'pid' => $pid
        ]);

        if (!$find) {
            return false;
        }

        return $find;
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('beforeDeadline', function (Builder $builder) {
            $builder->whereDate('accessDeadline', '>', Carbon::now()->format(Carbon::DEFAULT_TO_STRING_FORMAT));
        });

        static::addGlobalScope('deadlineOrder', function (Builder $builder) {
            $builder->orderByDesc('accessDeadline');
        });
    }

    /**
     * 添加授权信息
     * @param $uid
     * @param string $cellphone
     * @param int $pid
     * @return $this
     */
    public function add($uid, $cellphone = '', $pid = 0)
    {
        $now = Carbon::now()->format(Carbon::DEFAULT_TO_STRING_FORMAT);
        $author = new UCUserAuthorization();
        $accessToken = $author->getAccessToken($uid, $cellphone, $now);

        $deadline = $author->getAccessDeadLine($now);
        $this->uid = $uid;
        $this->accessDateTime = $now;
        $this->accessDeadline = $deadline;
        $this->sessionKey = $accessToken;
        $this->pid = $pid;
        $this->save();

        return $this;
    }
}
