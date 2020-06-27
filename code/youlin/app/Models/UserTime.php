<?php

namespace App\Models;

use App\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 时光记录
 * Class UserTime
 *
 * @package App\Models
 */
class UserTime extends BaseModel
{
    use SoftDeletes;

    protected $table = 'yl_user_times';

    protected $guarded = [];

    public function comments()
    {
        return $this->hasMany(Comment::class, 'time_id');
    }

    public function apply()
    {
        return $this->hasMany(UserApplyTime::class, 'time_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachment()
    {
        return $this->hasMany(TimeAttachment::class, 'time_id', 'id');
    }

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

    public static function who($userId, $with = null)
    {
        $query = static::query()->where(function ($query) use ($userId) {
            $query->where('open_type', '=', 0)->orWhere('user_id', $userId);
        });

        if ($with) {
            $query->with($with);
        }

        return $query;
    }

    public static function formatUserTime($userTimes, $apply = true, $userIds = [])
    {
        $result = [];
        $users  = User::query()->whereIn('user_id', $userIds)->get(['user_id', 'nickname', 'headimgurl'])->keyBy('user_id');

        foreach ($userTimes as $userTime) {
            $user = $users->get($userTime->user_id);

            $result[] = [
                'id'        => $userTime->id,
                'own_id'    => $userTime->user_id,
                'source'    => $userTime->source,
                'click'     => $userTime->click,
                'party'     => $userTime->party,
                'address'     => $userTime->address,
                'transmit'  => $userTime->transmit,
                'open_type' => $userTime->open_type,
                'comment'   => $userTime->comment,
                'images'    => array_map(function ($image) {
                    return ['filepath' => $image['filepath']];
                }, $userTime->attachment->toArray()),
                'active'    => [
                    'title'        => $userTime->modelable->title,
                    'detail'       => $userTime->modelable->detail ?? '',
                    'content'      => $userTime->modelable->content,
                    'longitude'    => $userTime->modelable->longitude,
                    'latitude'     => $userTime->modelable->latitude,
                    'label'        => $userTime->modelable->label,
                    'cover_url'    => $userTime->modelable->cover_url,
                    'coverIndex'   => $userTime->modelable->coverIndex,
                    'active_time'  => $userTime->modelable->active_time,
                    'introduction' => $userTime->modelable->introduction ?? '',
                ],
                'user'      => [
                    'create'   => $userTime->created_at->toDateTimeString(),
                    'nickname' => $user->nickname,
                    'header'   => $user->headimgurl,
                ],
                'apply'     => $apply ? array_map(function ($apply) {
                    return ['header' => $apply['header'], 'nickname' => $apply['nickname']];
                }, $userTime->apply->toArray()) : [],
            ];
        }

        return $result;
    }

}
