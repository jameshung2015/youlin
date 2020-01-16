<?php

namespace App\Models;


/**
 * 家庭关系表
 *
 * Class MemberShip
 * @package App\Models
 */
class MemberShip extends BaseModel
{
    protected $table = 'yl_member_ship';

    protected $with = ['user'];

    public function user()
    {
        return $this->belongsTo(User::class, 'member_id', 'user_id');
    }

}
