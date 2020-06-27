<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2019/12/8 4:20
 */


namespace App\Http\Resources;


use App\Models\ActivePay;
use App\Models\SystemActive;
use App\Models\User;
use App\Unit;

class TimeLogResource extends BaseResource
{

    public static $Actives = [];
    public static $Image = [];

    /**
     * 活动id
     *
     * @param $userId 当前用户id
     * @param $sourceUser 发布人id
     * @param $source  来源id
     * @param $type   类型
     * @return bool
     */
    public static function payActives($userId, $source, $sourceUser, $type)
    {
        if ('话题' === $type || 0 === intval($source) || intval($userId) === intval($sourceUser)) {
            return true;
        }

        if (!TimeLogResource::$Actives) {
            TimeLogResource::$Actives = ActivePay::query()->where('user_id', $userId)->pluck('active_id')->toArray();
        }

        return in_array($source, TimeLogResource::$Actives);
    }


    public function images($source)
    {
        if (empty($source)) {
            return '';
        }

        if (empty(static::$Image)) {
            static::$Image = SystemActive::query()->pluck('image','id')->toArray();
        }

        return static::$Image[$source] ?? '';
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            "content"   => $this->modelable->content,
            "longitude" => $this->modelable->longitude,
            "latitude"  => $this->modelable->latitude,
            "label"     => $this->modelable->label,
        ];
        if ($this->type === Unit::TYPE_ACTIVE) {
            $data['images'] = [$this->images($this->source)];
        }

        if ($this->type === Unit::TYPE_SUBJECT) {
            $data['images'] = $this->modelable->images;
        }

        $user = $request->user();

        return [
            "time_id"     => $this->id,
            'master_name' => $this->user->nickname,
            'master_type' => $this->masterType($this->source, $this->user_id, $user['user_id']),
            "title"       => $this->title,
            "source"      => $this->source,
            'source_user' => $this->user_id,
            "type"        => $this->type,
            'authority'   => $this->payActives($user['user_id'], $this->source, $this->user_id, $this->type),
            "created_at"  => $this->created_at->toDateTimeString(),
            'active_time' => $this->modelable->active_time,
            'data'        => $data,
            'comment'     => TimeLogComment::collection($this->comment),
        ];
    }

    //  master_type 1自己，2系统，3其他
    private function masterType($source, $originId, $userId)
    {
        if (!empty($source)) {
            return 2;
        }

        return $originId === $userId ? 1 : 3;
    }
}
