<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class _module extends Model
{
    public function submenu()
    {
        return $this->hasMany('App\_subModule','module_id');
    }
}
