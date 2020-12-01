<?php


namespace Shengyouai\App\Oauth;


use Firebase\JWT\JWT;

class JWTAuthorization
{

    protected $privateKey;

    public $publicKey;

    protected $algorithm;

    protected function __construct($algorithm, $privateKey, $publicKey = '')
    {

        $this->algorithm = $algorithm;
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;

        if ($algorithm === 'RS256') {
            $this->privateKey = is_file($this->privateKey) ?
                file_get_contents($this->privateKey) : $this->privateKey;
            $this->publicKey = is_file($this->publicKey) ?
                file_get_contents($this->publicKey) : $this->publicKey;
        }
    }

    public static function RSA($privateKey, $publicKey)
    {
        return new self('RS256', $privateKey, $publicKey);
    }

    public static function HSA($key = '')
    {
        return new self('HS256', $key, $key);
    }

    public function encode($payload)
    {
        return JWT::encode($payload, $this->privateKey, $this->algorithm);
    }

    /**
     * @param $token
     * @return object
     * @throw UnexpectedValueException $e
     */
    public function decode($token)
    {
        return JWT::decode($token, $this->privateKey, array($this->algorithm));
    }
}
