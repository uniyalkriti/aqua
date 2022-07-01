<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonLogin extends Model
{
    protected $table='person_login';
    protected $guarded=[];
    public $timestamps=false;
}
