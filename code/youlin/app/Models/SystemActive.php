<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 系统活动
 * Class SystemActive
 * @property $type_id
 * @property $id
 * @property $title
 * @property $content
 * @property $money
 * @package App\Models
 */
class SystemActive extends BaseModel
{
    use SoftDeletes;

    protected $table = 'yl_system_active';

    public static function type($type)
    {
        return static::query()->when($type, function ($query, $value) {
            $query->where('type_id', $value);
        });
    }
}
