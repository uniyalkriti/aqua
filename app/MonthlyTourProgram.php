<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonthlyTourProgram extends Model
{
    protected $table='monthly_tour_program';

    public function locations()
    {
        return $this->hasMany('App\DealerLocation','dealer_id','dealer_id');
    }
    public function getDealer(){
        return $this->hasOne('App\Dealer','id','dealer_id');
    }
    public function getBeat(){
        return $this->hasOne('App\Location7','id','locations');
    }
}
