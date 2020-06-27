<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 评论表
 *
 * Class Comment
 * @package App\Models
 */
class Comment extends Model
{

    protected $table = 'yl_comments';

    protected $guarded = [];
}
