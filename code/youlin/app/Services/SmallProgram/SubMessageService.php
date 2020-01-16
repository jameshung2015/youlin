<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2019/12/29 20:22
 */


namespace App\Services\SmallProgram;


use App\Models\User;
use App\Services\HttpRequestServices;

/**
 * 发送微信模板消息
 *
 * Class SubMessageService
 * @package App\Services\SmallProgram
 */
class SubMessageService extends BaseService
{
    const NEW_ACTIVE = '6xYb9N0N06_Bjc7o9_21rd4FYe3HrnR3ufc_qSeDP28';

    /**
     * @param string $openid 小程序
     * @param string $title 活动标题
     * @param string $nickname 发起人
     * @param string $area 地点
     * @param string $time 2019年12月29日 19:07:04
     * @param string $page
     * @return bool|string|null
     */
    public function sendSubActiveMessage($openid, $title, $nickname, $area, $time, $page = '/pages/home/main')
    {
        $params = [
            "touser"      => $openid,
            'template_id' => static::NEW_ACTIVE,
            "page"        => $page,
            'data'        => [
                'thing6' => [
                    'value' => $title,
                ],
                'thing1' => [
                    'value' => $nickname,
                ],
                'thing4' => [
                    'value' => $area,
                ],
                'date2'  => [
                    'value' => $time,
                ],
            ],
        ];

        return HttpRequestServices::httpPost($this->url(), $params);
    }

    private function url()
    {
        return 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=' . $this->accessToken;
    }
}
