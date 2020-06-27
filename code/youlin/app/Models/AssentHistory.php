<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssentHistory extends Model
{
    use SoftDeletes;

    protected $table = 'yl_assent_history';

    protected $guarded = [];

    public static function log($userId, $commentId)
    {
        return static::query()->where('user_id', $userId)->where('comment_id', $commentId);
    }
}
