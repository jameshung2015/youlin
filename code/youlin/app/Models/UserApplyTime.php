<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *  参与记录表
 *
 * Class UserApplyTimer
 * @package App\Models
 */
class UserApplyTime extends Model
{
    use SoftDeletes;

    protected $table = 'yl_user_apply_times';

    protected $guarded = [];
}
