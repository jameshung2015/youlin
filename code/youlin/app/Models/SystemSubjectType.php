<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSubjectType extends Model
{
    protected $table = 'yl_system_subject_type';

    public static function baseQuery()
    {
        return static::query()->where('is_closed', 0);
    }

}
