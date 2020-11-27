<?php


namespace Shengyouai\App\Oauth;


use Carbon\Carbon;

class UCUserAuthorization extends JWTAuthorization
{
    private $deadline;

    /**
     * UCUserAuthorization constructor.
     * @param string $key
     * @param int $deadline
     */
    public function __construct($key = 'CXtLM5ECe2dYySFoFWnL2OaUvKiuUxM5', $deadline = 7200)
    {
        parent::__construct($key);

        $this->deadline = $deadline;
    }

    /**
     * @param $uid
     * @param $cellphone
     * @param $accessDateTime
     * @return string
     */
    public function getAccessToken($uid, $cellphone, $accessDateTime)
    {
        $payload = [
            'uid' => $uid,
            'cellphone' => $cellphone,
            'accessDateTime' => $accessDateTime,
            'accessDeadline' => $this->getAccessDeadLine($accessDateTime)
        ];
        return parent::encode($payload);
    }

    public function getAccessDeadLine($accessDateTime)
    {
        return Carbon::parse($accessDateTime)->addSeconds($this->deadline)->format(Carbon::DEFAULT_TO_STRING_FORMAT);
    }
}
