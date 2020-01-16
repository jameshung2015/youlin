<?php

namespace App\Models;

use App\Unit;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 时光记录
 * Class UserTime
 * @package App\Models
 */
class UserTime extends BaseModel
{
    use SoftDeletes;

    protected $table = 'yl_user_times';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timeParty()
    {
        return $this->hasMany(TimeParty::class, 'time_id', 'id');
    }

    public function modelable()
    {
        return $this->morphTo('modelable', 'model_able', 'model_id');
    }

    public function subject()
    {
        return $this->hasOne(UserSubject::class, 'id', 'model_id');
    }

    public function active()
    {
        return $this->hasOne(UserActive::class, 'id', 'model_id');
    }

    public function comment()
    {
        return $this->hasMany(TimeComment::class, 'time_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }


    /**
     * @return bool 是否话题
     */
    public function getCategoryAttributes()
    {
        return $this->type === Unit::TYPE_SUBJECT ? true : false;
    }

    public static function party($type, $userId)
    {
        return static::query()->where('user_id', $userId)
            ->where('type', $type)
            ->where('source', '>', 0)
            ->pluck('source')
            ->toArray();
    }

}
