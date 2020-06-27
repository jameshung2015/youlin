<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2020/2/17 22:04
 */


namespace App\Services\SmallProgram;

use App\Services\HttpRequestServices;

/**
 * 微信小程序  内容安全接口开发文档
 * @link https://mp.weixin.qq.com/cgi-bin/announce?action=getannouncement&key=11522142966rk3L2&version=1&lang=zh_CN&platform=2
 * Class ContentSecurityService
 * @package App\Services\SmallProgram
 */
class ContentSecurityService extends BaseService
{

    public function validateAll($contents)
    {
//        $contents = is_array($contents) ? $contents : [$contents];
//        foreach ($contents as $content) {
//            if (is_file($content)) {
//                if (!$this->imageSecurity($content)) {
//                    return false;
//                }
//                continue;
//            }
//
//            if (is_string($content)) {
//                if (!$this->contentSecurity($content)) {
//                    return false;
//                }
//            }
//
//        }

        return true;
    }

    public function imageSecurity($imageFile)
    {
        $obj      = new \CURLFile($imageFile);
        $obj->setMimeType("image/jpeg");
        $info = HttpRequestServices::httpPost($this->imageSecurityUrl(), ['media' => $obj], null, false);

        return json_decode($info, true);
    }

    public function contentSecurity($content)
    {
        if (empty($content)) {
            return true;
        }

        $security = HttpRequestServices::httpPost($this->contentSecurityUrl(), ['content' => $content]);
        $result   = json_decode($security, true);
        $errCode  = intval($result['errcode']);
        if ($errCode === 0) {
            return true;
        }

        return false;
    }

    /**
     * 文本安全内容检测接口
     * @return string
     */
    protected function contentSecurityUrl()
    {
        return 'https://api.weixin.qq.com/wxa/msg_sec_check?access_token=' . $this->accessToken;
    }

    /**
     * 图片安全内容检测接口
     * @return string
     */
    protected function imageSecurityUrl()
    {
        return 'https://api.weixin.qq.com/wxa/img_sec_check?access_token=' . $this->accessToken;
    }

}
