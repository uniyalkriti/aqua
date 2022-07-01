<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDailyAttandence extends Model
{
	protected $table='user_daily_attendance';
    protected $guarded = array();
    public $timestamps = false;
    
}
