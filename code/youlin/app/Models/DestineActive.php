<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property  $cover_url
 * @property  $url
 * @property  $title
 *
 * Class DestineActive
 * @package App\Models
 */
class DestineActive extends Model
{
    protected $table = 'yl_destine_actives';
    protected $guarded = [];

    public static function type($typeId, $search = '')
    {
        return static::query()->where('type_id', $typeId)
            ->when($search, function ($query, $value) {
                $query->where('title', 'like', "%{$value}%");
            });
    }
}
