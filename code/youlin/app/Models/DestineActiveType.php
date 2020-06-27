<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestineActiveType extends Model
{
    protected $table   = 'yl_destine_active_type';
    protected $guarded = [];

    public static function open()
    {
        return static::query()->where('is_close', '=', 0);
    }
}
