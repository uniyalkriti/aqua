<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class PtuInspection extends Model
{
    use Notifiable,HasApiTokens;


    protected $table = 'ptu_inspection';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'inspection_id', 'maintenance_status', 'ptu_id','priority','remarks', 'location_2_id','draft_date_time','data_unique_id',
         'location_1_id', 'lat', 'lng','geo_address','inspection_address','inspected_by','site_image','status'
    ];
 

     /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
