<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2019/11/29 0:54
 */


namespace App\Services\OfficialAccount;


use App\Services\HttpRequestServices;

class UserInformation extends BaseService
{
    /**
     * 获取用户基本信息(普通token)
     *
     * @param $openid
     * @return array
     */
    public function userInfo($openid)
    {
        $url    = sprintf('https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN', $this->accessToken, $openid);
        $result = json_decode(HttpRequestServices::httpGet($url), true);

        $result['unionid'] = isset($result['unionid']) ? $result['unionid'] : '';
        return $result;
    }
}
