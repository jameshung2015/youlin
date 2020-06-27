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
    const TYPE_FAMILY  = '家庭活动'; // 第二版新增

    const OPEN_SELF = 1;  # 可见性： 仅自己
    const OPEN_ALL  = 0;  # 可见性：所有人

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
