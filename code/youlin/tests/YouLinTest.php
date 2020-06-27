<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2020/5/17 1:04
 */


namespace Tests;


use App\Models\User;
use Illuminate\Support\Str;

class YouLinTest extends TestCase
{
    public function testPDO()
    {
        $str = '12021007097';

        dd(preg_match('/^1[3456789]\d{9}/', $str));
        dd(Str::length('fwefw手动复位法为'));
        $model = User::query()->whereKey(1)->first();
        dd(User::query()->whereKey(1)->first());
    }
}
