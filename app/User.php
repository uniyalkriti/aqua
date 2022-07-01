<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Auth;
use DB;


class User extends Authenticatable
{
    use Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email','company_id','is_admin', 'password','profile_image','role_id','status','id','original_pass','updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    # Relation with users_details table
    public function userDetails()
    {
        return $this->hasOne('App\UserDetail','user_id','id');
    }
    public static function getCompanyId()
    {
        $company_id = Auth::user()->company_id;
         return ($company_id);
    }

     public function getCustomAttribute()
    {
        $company_id = Auth::user()->company_id;
        $cdata = DB::table('company')->where('id',$company_id)->first();
        $return = $cdata->source;
        return $return;

    }


}
