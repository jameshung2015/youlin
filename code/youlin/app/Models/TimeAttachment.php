<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 时光活动上传的附件
 * @package $id
 * @package $time_id
 * @package $user_id
 * @package $filepath
 *
 * Class ActiveAttachment
 * @package App\Models
 */
class TimeAttachment extends Model
{
    use SoftDeletes;

    protected $table = 'yl_time_attachments';

    protected $guarded  = [];
}
