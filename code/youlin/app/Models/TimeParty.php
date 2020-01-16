<?php

namespace App\Models;


/**
 * 参与表
 * Class TimeParty
 * @package App\Models
 */
class TimeParty extends BaseModel
{
    protected $table = 'yl_time_party';

    public function timeLogs()
    {
        return $this->hasMany(UserTime::class, 'id', 'time_id');
    }
}
