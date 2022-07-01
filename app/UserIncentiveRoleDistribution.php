<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserIncentiveRoleDistribution extends Model
{
    protected $table='incentive_role_wise_distribution';
    protected $timestamp = False;
    protected $guarded = array();
}
