<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2019/12/13 0:12
 */


namespace App\Services;


use App\Models\WeChat;

abstract class WeChatService
{
    /**
     * @var string 微信access_token
     */
    protected $accessToken = '';
    protected $appId;
    protected $secret;

    public function __construct()
    {
        $this->accessToken = $this->weChatAccessToken();
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    protected function weChatAccessToken()
    {
        $token = WeChat::query()->where('token_type', $this->type())->first();
        if ($token && time() + 200 <= $token->expire_time) {
            return $token->access_token;
        }

        $tokenUrl    = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appId}&secret={$this->secret}";
        $data        = HttpRequestServices::httpGet($tokenUrl);
        $accessToken = json_decode($data, true);
        if (isset($accessToken['errcode'])) {
            return '';
        }

        if (!$token) {
            $token = new WeChat();
        }

        $token->token_type   = $this->type();
        $token->access_token = $accessToken['access_token'];
        $token->expire_time  = time() + $accessToken['expires_in'];
        $token->save();

        return $accessToken['access_token'];
    }

    /**
     * @return string 微信小程序/公众号
     */
    abstract public function type();
}
