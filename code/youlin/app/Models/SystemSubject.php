<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 系统话题
 * Class SystemSubject
 * @package App\Models
 */
class SystemSubject extends Model
{
    use SoftDeletes;

   protected $table = 'yl_system_subject';

   public static function type($type)
   {
       return static::query()->where('type_id', $type);
   }
}
