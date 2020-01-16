<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2019/12/9 0:29
 */


namespace App\Http\Resources;


class TimeLogComment extends BaseResource
{
    public function toArray($request)
    {
        return [
            'user_id'    => $this->user_id,
            'content'    => $this->content,
            'nickname'   => $this->user->nickname,
            'headimgurl' => $this->user->headimgurl,
            'created_at' => $this->created_at->toDateString(),
        ];
    }
}
