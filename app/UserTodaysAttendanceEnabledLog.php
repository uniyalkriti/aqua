<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTodaysAttendanceEnabledLog extends Model
{
    protected $table='user_todays_attendance_enabled_log';
    public $timestamps=false;

    protected $guarded=[];
}
