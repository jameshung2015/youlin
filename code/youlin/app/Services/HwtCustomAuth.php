<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2019/11/24 2:23
 */


namespace App\Services;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class HwtCustomAuth
{
    private   $hash;
    private   $key;
    private   $issuer;
    private   $audience;
    private   $id;
    protected $sign;
    private   $token;

    /**
     * @var mixed 过期时长
     */
    private $time;

    public function __construct()
    {
        $config         = config('jwt');
        $this->hash     = new Sha256();
        $this->key      = new Key($config['content'], $config['passphrase']);
        $this->time     = $config['exp_time'];
        $this->issuer   = $config['issuer'];
        $this->audience = $config['audience'];
        $this->id       = $config['id'];
        $this->token    = '';
    }

    /**
     *
     * @param $string
     * @return array
     * @throws \Exception
     */
    public function parse($string)
    {
        if (empty($string)) {
            throw new \Exception('请求头中必有auth');
        }

        $token = (new Parser())->parse($string);
        if (!$token->verify($this->hash, $this->key)) {
            throw new \Exception('非法Token');
        }

        $validate = $this->validateData();
        if (!$token->validate($validate)) {
            throw new \Exception('授权登陆已经过期，请重新登陆');
        }

        $claims = [];
        $noUse  = ['iss', 'aud', 'jti', 'iat', 'exp', ''];
        $noUse  = [];
        foreach ($token->getClaims() as $key => $claim) {
            if (in_array(strtolower($key), $noUse)) {
                continue;
            }

            $claims[$key] = $claim->getValue();
        }

        $validate->setCurrentTime(time() + 300); // changing the validation time to future
        if (!$token->validate($validate)) {
            $this->token = $this->createToken($claims);
        }

        return $claims;
    }

    /**
     * @param $claims
     * @return string
     */
    public function createToken($claims)
    {
        $time    = time();
        $builder = (new Builder())->issuedBy($this->issuer)
            ->permittedFor($this->audience)
            ->identifiedBy($this->id, true)
            ->issuedAt($time)
            ->expiresAt($time + $this->time);

        foreach ($claims as $key => $claim) {
            $builder->withClaim($key, $claim);
        }

        $this->token = (string)$builder->getToken($this->hash, $this->key); // Retrieves the generated token;
        return $this->token;
    }

    public function getToken()
    {
        return $this->token;
    }

    private function validateData()
    {
        $data = new ValidationData();
        $data->setIssuer($this->issuer);
        $data->setAudience($this->audience);
        $data->setId($this->id);
        $data->setCurrentTime(time());
        return $data;
    }

}
