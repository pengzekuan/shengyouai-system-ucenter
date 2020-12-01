<?php

namespace Shengyouai\App\UCModels;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Shengyouai\App\Oauth\UCUserAuthorization;

class UCUserOauth extends UCModel
{

    const AUTHORIZATION_TYPE = 'Authorization';

    const AUTHORIZATION_PREFIX = 'Bearer';

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
            $builder->where('accessDeadline', '>', Carbon::now()->format(Carbon::DEFAULT_TO_STRING_FORMAT));
        });

        static::addGlobalScope('enable', function (Builder $builder) {
            $builder->where('disabled', '=', 0);
        });

        static::addGlobalScope('deadlineOrder', function (Builder $builder) {
            $builder->orderByDesc('accessDeadline');
        });
    }

    /**
     * 添加授权信息
     * @param int $uid 用户id
     * @param string $cellphone 用户手机号
     * @param int $pid 授权平台id， 默认0
     * @param string $ip 授权客户端IP
     * @param string $device 授权客户端设备
     * @param string $network 授权客户端网络
     * @return $this
     */
    public function add($uid = 0, $cellphone = '', $pid = 0, $ip = '', $device = '', $network = '')
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

    public static function findByUserId($userId, $pid)
    {
        return self::where([
            ['uid', '=', $userId],
            ['pid', '=', $pid]
        ])->first();
    }

    /**
     * 取消授权
     * @param $userId
     * @param $pid
     * @return void
     */
    public function disabled($userId, $pid)
    {
        $where = [
            ['uid', '=', $userId]
        ];
        if ($pid) {
            $where[] = ['pid', '=', $pid];
        }
        $data = self::getAll($where);

        foreach ($data as $auth) {
            $auth->disabled = 1;
            $auth->sessionKey = null;
            $auth->save();
        }
    }
}
