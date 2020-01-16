<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2019/11/28 0:22
 */

namespace App\Services\OfficialAccount;


use App\Services\HttpRequestServices;

class SendTemplateService extends BaseService
{

    public function testTemplate($toUser)
    {
        $post = [
            'touser'      => $toUser,
            'template_id' => 'RUY9-Q5Ow1Ubo29q1IILs2qv6IeuT-dbRHoJSF-cidg',
            'data'        => [
                'first'    => [
                    'value' => '新增',
                    'color' => '#173177',
                ],
                'keyword1' => [
                    'value' => '2019-11-02',
                    'color' => '#173177',
                ],
                'keyword2' => [
                    'value' => date('Y-m-d H:i:s'),
                    'color' => '#173177',
                ],
                'remark'   => [
                    'value' => '今日',
                    'color' => '#173177',
                ],
            ],
        ];

        $url = sprintf('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s', $this->accessToken);
        HttpRequestServices::httpPost($url, $post);
    }

}
