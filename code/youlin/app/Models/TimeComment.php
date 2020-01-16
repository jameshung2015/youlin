<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 评论表
 * Class TimeComment
 * @package App\Models
 */
class TimeComment extends BaseModel
{
    protected $table = 'yl_time_comments';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
