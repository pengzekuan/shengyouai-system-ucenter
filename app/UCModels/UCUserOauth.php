<?php

namespace Shengyouai\App\UCModels;

use Illuminate\Database\Eloquent\Model;

class UCUserOauth extends Model
{
    protected $table = 'uc_u_oauth';

    /**
     * 添加授权信息
     * @param $uid
     * @param $token
     * @param $accessDateTime
     * @param $accessDeadline
     * @param int $pid
     * @return $this
     */
    public function add($uid, $token, $accessDateTime, $accessDeadline, $pid = 0)
    {
        $this->uid = $uid;
        $this->accessDateTime = $accessDateTime;
        $this->accessDeadline = $accessDeadline;
        $this->sessionKey = $token;
        $this->pid = $pid;
        $this->save();

        return $this;
    }
}
