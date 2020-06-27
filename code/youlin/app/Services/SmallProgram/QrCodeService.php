<?php

namespace App\Services\SmallProgram;

use App\Services\HttpRequestServices;

/**
 * 小程序二维码和太阳码
 * @url https://developers.weixin.qq.com/miniprogram/dev/framework/open-ability/qr-code.html
 * Class QrCodeService
 * @package App\Services\SmallProgram
 */
class QrCodeService extends BaseService
{
    public function youLin($id)
    {
        $qrCode = [
            'scene'      => 'transmit=1&id=' . $id,
            'width'      => 200,
            'path'       => 'pages/find/activityInner/main?id=' . $id,//二维码跳转路径（要已发布小程序）
            'auto_color' => true,
        ];

        $response = $this->httpContent($qrCode);
        $judge    = json_decode($response, true);

        if (isset($judge['errcode'])) {
            throw new \Exception($judge['errmsg']);
        }

        $path = sprintf('images/qr_code/%s/%s.%s', date('Ym/d'), uniqid(), 'jpg');
        $save = public_path($path);

        if (!file_exists(dirname($save))) {
            mkdir(dirname($save), 0775, true);
        }

        $handler = fopen($path, 'w');
        fwrite($handler, $response);
        fclose($handler);

        return $path;
    }

    // 二维码 C类
    protected function httpContent($path)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=' . $this->getAccessToken();

        return HttpRequestServices::httpPost($url, $path);
    }

}
