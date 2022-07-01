<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
	protected $table='check_out';
    protected $guarded = array();
    public $timestamps = false;
    
}
