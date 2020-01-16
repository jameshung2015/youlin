<?php

namespace App\Models;


class Statistics extends BaseModel
{
    protected $table = 'yl_statistics';

    public function getLeavingMessageAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getActiveTypeAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getSystemBrowseAttribute($value)
    {
        return json_decode($value, true);
    }
}
