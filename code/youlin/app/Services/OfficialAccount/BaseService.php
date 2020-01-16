<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2019/11/26 23:46
 */


namespace App\Services\OfficialAccount;

use App\Models\WeChat;
use App\Services\HttpRequestServices;
use App\Services\WeChatService;

/**
 * 微信公众号
 * Class BaseService
 * @package App\Services\OfficialAccount
 */
class BaseService extends WeChatService
{
    /**
     * @var string 微信access_token
     */
    protected $accessToken = '';
    private   $appId;
    private   $secret;

    public function __construct()
    {
        $this->appId       = config('wechat.official.appid');
        $this->secret      = config('wechat.official.secret');
        parent::__construct();
    }

    public function type()
    {
        return '微信公众号';
    }


    /**
     * 前端获取到的code
     * @param $code
     * @return array|mixed
     */
    public function getUserInfo($code)
    {
        try {
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->AppId}&secret={$this->Secret}&code={$code}&grant_type=authorization_code";
            #获取webAccessToken以及openid
            $result = json_decode(file_get_contents($url), true);
            $token  = $result['access_token'];
            $url    = "https://api.weixin.qq.com/sns/userinfo?access_token={$token}&openid={$result['openid']}&lang=zh_CN";
            return json_decode(file_get_contents($url), true);
        } catch (\Exception $ex) {
            return [];
        }
    }
}
