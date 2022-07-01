<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class PtuInspectionHandling extends Model
{
    use Notifiable,HasApiTokens;


    protected $table = 'ptu_inspection_handling';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'inspection_id', 'ptu_id', 'comments','lat', 'lng', 'geo_address','comment_image', 'officer_id', 'status'
    ];
 

     /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
