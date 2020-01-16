<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 用户家庭分组表
 * @property $user_id
 * @property $family_name
 * @property $family_id
 * Class Family
 * @package App\Models
 */
class Family extends Model
{

    protected $table = 'yl_family';

    protected $primaryKey = 'family_id';

    public function member()
    {
        return $this->hasMany(FamilyMember::class, 'family_id');
    }

}
