<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppModule extends Model
{
    protected $table = 'app_module';
    protected $timestamp = false;
    protected $guarded = [];
}
