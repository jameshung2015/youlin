<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2019/12/8 3:06
 */


namespace App;


class Unit
{
    const TYPE_ACTIVE  = '活动';
    const TYPE_SUBJECT = '话题';

    /**
     * 微信昵称字符编码转换
     * @param $string
     * @return bool|false|string
     */
    public static function characterTransform($string)
    {
        return empty($string) ? '' : iconv('ISO-8859-1', 'UTF-8', $string);
    }


}
