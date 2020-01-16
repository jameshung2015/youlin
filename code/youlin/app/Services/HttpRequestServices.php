<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2019/11/28 23:36
 */


namespace App\Services;


use App\Models\ApiLog;

class HttpRequestServices
{
    private $debug;
    /**
     * @var HttpRequestServices
     */
    static public $instance;

    public static function httpGet($url, $params = null, $header = null)
    {
        $instance = static::instance();
        $result   = $instance->requestGet($url, $params, $header);
        $instance->afterHttpRequest($url, $params, $header, $result, 'HTTP_GET');
        return $result;
    }

    public static function httpPost($url, array $params = [], array $header = [])
    {
        $instance = static::instance();
        $result   = $instance->requestPost($url, $params, $header);
        $instance->afterHttpRequest($url, $params, $header, $result, 'HTTP_POST');
        return $result;
    }

    public function requestPost($url, $params, $header)
    {
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回数据不直接输出
        curl_setopt($ch, CURLOPT_ENCODING, "gzip"); //指定gzip压缩
        //add header
        if(!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        //add ssl support
        if(substr($url, 0, 5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    //SSL 报错时使用
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);    //SSL 报错时使用
        }
        //add post data support
        if(!empty($params)) {
            curl_setopt($ch,CURLOPT_POST, 1);
            curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($params));
        }
        $content = curl_exec($ch); //执行并存储结果
        curl_close($ch);
        return $content;
    }

    private function requestGet($url, $params, $header)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回数据不直接输出
        //add header
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        //add ssl support
        if (substr($url, 0, 5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    //SSL 报错时使用
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);    //SSL 报错时使用
        }

        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    private function afterHttpRequest($url, $params, $header, $result, $name)
    {
        if ($this->debug) {
            ApiLog::logInfo($url, compact('params', 'header'), $result, $name, static::class);
        }
    }

    public static function instance()
    {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __construct()
    {
        $this->debug = config('app.debug');
    }

}
