<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DemandOrderCart extends Model
{
    protected $table='demand_order_cart';

    protected $guarded = array();
    public $timestamps=false;
}
