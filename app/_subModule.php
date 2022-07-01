<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class _subModule extends Model
{
    public function menu()
    {
        return $this->belongsTo('App\_module','module_id');
    }
}
