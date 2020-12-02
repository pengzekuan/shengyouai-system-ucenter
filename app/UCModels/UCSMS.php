<?php

namespace Shengyouai\App\UCModels;

class UCSMS extends UCModel
{
    protected $table = 'uc_sms';

    /**
     * @param $appId
     * @param $tid
     * @param $cellphone
     * @param $content
     * @param $sid
     * @param int $sent
     * @param null $sendTime
     * @return mixed|void
     */
    public function add($appId, $tid, $cellphone, $content, $sid = '', $sent = 0, $sendTime = null)
    {
        $this->sid = $sid;
        $this->tid = $tid;
        $this->appId = $appId;
        $this->cellphone = $cellphone;
        $this->content = $content;
        $this->sent = $sent;
        $this->sendTime = $sendTime;

        $this->save();

        return $this;
    }
}
