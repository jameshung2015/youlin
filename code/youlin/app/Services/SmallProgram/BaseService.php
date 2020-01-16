<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2019/12/13 0:08
 */


namespace App\Services\SmallProgram;

use App\Services\HttpRequestServices;
use App\Services\WeChatService;

class BaseService extends WeChatService
{

    /**
     * 解密复杂信息
     * @param $encryptedData
     * @param $iv
     * @param $session_key
     * @param string $method
     * @return mixed
     */
    public static function decode($encryptedData, $iv, $session_key, $method = 'aes-128-cbc')
    {
        $data = openssl_decrypt(base64_decode($encryptedData), $method, Base64_Decode($session_key), true, Base64_Decode($iv));
        return json_decode($data, true);
    }

    public function __construct()
    {
        $this->appId  = config('wechat.small.appid');
        $this->secret = config('wechat.small.secret');
        parent::__construct();
    }

    public function type()
    {
        return '微信小程序';
    }

}
