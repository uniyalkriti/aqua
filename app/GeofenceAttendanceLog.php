<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeofenceAttendanceLog extends Model
{
    protected $table='geofence_attendance_log';
    public $timestamps=false;

    protected $guarded=[];
}
