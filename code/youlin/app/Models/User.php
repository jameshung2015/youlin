<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 *
 * @property $sex
 * @property $user_id
 * @property $headimgurl
 * @property $nickname
 * @property $wx_nickname
 * @property $mobile
 * @property $openid
 * @property $wx_openid
 * @property $unionid
 * @property $password
 * @property $session_key
 * @property $created_at
 *
 * Class User
 * @package App\Models
 */
class User extends Model
{
    use SoftDeletes;

    protected $table = 'yl_users';

    public $primaryKey = 'user_id';

    protected $fillable = [
        'sex',
        'headimgurl',
        'nickname',
        'mobile',
        'openid',
        'wx_openid',
        'unionid',
        'password',
    ];


    public function member()
    {

    }

    public function memberShip()
    {
        return $this->hasMany(MemberShip::class, 'user_id', 'user_id');
    }

    public function family()
    {
        return $this->hasMany(Family::class, 'user_id', 'user_id');
    }

    /**
     * 根据微信unionid
     * @param $unionid
     * @param $openid
     * @return mixed
     */
    public static function unionid($unionid, $openid = '')
    {
        $user = static::query()->where('unionid', $unionid ?: false)->first();
        if ($user) {
            return $user;
        }

        return static::query()->where('openid', $openid ?: false)->first();
    }
}
