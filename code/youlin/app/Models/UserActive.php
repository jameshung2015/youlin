<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 用户发布的活动列表
 * @property $title,
 * @property $user_id
 * @property $content
 * @property $longitude
 * @property $latitude
 * @property $label
 * @property $active_time
 * Class UserActive
 * @package App\Models
 */
class UserActive extends Model
{
    use SoftDeletes;
    protected $table = 'yl_user_active';

    protected $fillable = [
        'title',
        'user_id',
        'content',
        'longitude',
        'latitude',
        'label',
        'active_time',
    ];

    public function times()
    {
        return $this->hasMany(UserTime::class, 'model_id', 'id');
    }

    public function logs()
    {
        return $this->morphOne(UserTime::class,'modelable','modelable', 'model_able', 'model_id');
    }

}
