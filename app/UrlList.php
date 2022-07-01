<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UrlList extends Model
{
    protected $table='url_list';

    protected $guarded = array();
    
    public $timestamps = false;

}
