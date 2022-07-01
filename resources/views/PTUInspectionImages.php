<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PTUInspectionImages extends Model
{
    protected $table='ptu_inspection_images';  
    protected $fillable = [
        'inspection_no','image_name'
    ];
}
