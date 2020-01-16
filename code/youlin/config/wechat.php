<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2019/11/28 0:10
 */


return [
    /**
     * 公众号配置
     */
    'official' => [
        'appid'  => env('OFFICIAL_APPID'),
        'secret' => env('OFFICIAL_SECRET'),
    ],

    /**
     * 小程序配置
     */
    'small'    => [
        'appid'  => env('SMALL_APPID'),
        'secret' => env('SMALL_SECRET'),
    ],
];
