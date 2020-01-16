<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 用户发布的话题
 *
 * @property $user_id
 * @property $title
 * @property $content
 * @property $longitude
 * @property $latitude
 * @property $label
 * @property $images
 *
 * Class UserSubject
 * @package App\Models
 */
class UserSubject extends BaseModel
{
    use SoftDeletes;

    protected $table = 'yl_user_subject';

    public function times()
    {
        return $this->hasMany(UserTime::class, 'model_id', 'id');
    }

    public function logs()
    {
        return $this->morphOne(UserTime::class, 'modelable', 'modelable', 'model_able', 'model_id');
    }

    public function getImagesAttribute($value)
    {
        return json_decode($value, true);
    }


}
