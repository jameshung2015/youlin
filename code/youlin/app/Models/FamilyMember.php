<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 家庭成员
 * @property $family_id
 * @property $member_id
 * Class FamilyMember
 * @package App\Models
 */
class FamilyMember extends Model
{

    protected $table = 'yl_family_member';

    protected $with = ['user'];

    protected $fillable = [
        'family_id',
        'member_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'member_id', 'user_id');
    }
}
