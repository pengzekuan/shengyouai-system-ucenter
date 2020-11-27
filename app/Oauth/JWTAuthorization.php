<?php


namespace Shengyouai\App\Oauth;


use Firebase\JWT\JWT;

class JWTAuthorization
{

    private $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function encode($payload)
    {
        return JWT::encode($payload, $this->key);
    }

    public function decode($token)
    {
        return JWT::decode($token, $this->key);
    }
}
