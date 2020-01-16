<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2019/12/13 0:20
 */


namespace App\Services\SmallProgram;


use App\Services\HttpRequestServices;
use App\Unit;

class SmallUserService extends BaseService
{

    /**
     * 微信小程序解析 getUserInfo
     *
     * @param string $sessionKey 用户的sessionKey
     * @param string $encryptedData 敏感数据
     * @param string $iv 加密算法的初始向量
     * @return array
     */
    public function authorization($sessionKey, $encryptedData, $iv)
    {
        $jsUserInfo           = openssl_decrypt(base64_decode($encryptedData), 'aes-128-cbc', Base64_Decode($sessionKey), true, Base64_Decode($iv));
        $userInfo             = json_decode($jsUserInfo, true);

        return $userInfo;
    }


    /**
     * 获取用户基本信息(小程序login)
     * 返回内容 openid    用户唯一标识 session_key    会话密钥 unionid    string    用户在开放平台的唯一标识符。 errcode 错误码 errmsg 错误信息
     * @param $code
     * @return array
     */
    public function loginUserInfo($code)
    {
        if (empty($code)) {
            return ['errcode' => 0, 'errmsg' => '请输入code'];
        }

        $url    = sprintf(
            'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code',
            $this->appId,
            $this->secret,
            $code
        );
        $result = json_decode(HttpRequestServices::httpGet($url), true);

        $result['unionid'] = isset($result['unionid']) ? $result['unionid'] : '';
        return $result;
    }

}
