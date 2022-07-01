<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Location1;
use App\Location2;
use App\Location3;
use App\Location4;
use App\Location5;
use App\UserDetail;
use App\Location6;
use App\Location7;
use App\JuniorData;
use Auth;
use Session;
use DB;

class CommonFilter extends Model
{
    protected $guarded = array();
    protected $table='person';
    
    public static function comon_data($var)
    {	
    	$company_id = Auth::user()->company_id;
    	// $var = str_replace("'",'', $var);
    	// dd($var);
        $location3 = DB::table($var)->where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
	     
	     return $location3;
	}
    public static function user_filter($var)
    {   
        $company_id = Auth::user()->company_id;
        // $var = str_replace("'",'', $var);
        // dd($var);
        $user_auth=Auth::user();
       
         if($user_auth->role_id==1 || $user_auth->is_admin=='1' || $user_auth->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user_auth->id,$company_id);
            Session::push('juniordata', $user_auth->id);
            $junior_data = Session::get('juniordata');
        }




        $location3data = DB::table('person')
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->join('users','users.id','=','person.id')
                    ->where('person_status', 1)
                    ->where('is_admin','!=', 1)
                    ->where('person.company_id',$company_id)
                    ->orderBy('person.first_name', 'asc');
                    if (!empty($junior_data)) 
                    {
                        $location3data->whereIn('person.id', $junior_data);
                    }
        $location3 = $location3data->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"), 'person.id as user_id');
         
         return $location3;
    }
    public static function dealer_filter($var)
    {   
        $company_id = Auth::user()->company_id;
        // $var = str_replace("'",'', $var);
        // dd($var);
        $location3 = DB::table($var)
                    ->where('dealer_status', 1)
                    ->where('company_id',$company_id)
                    ->orderBy('dealer.name', 'asc')
                    ->pluck('name', 'id');
         
         return $location3;
    }
	public static function role_name($var)
    {	
    	$company_id = Auth::user()->company_id;
    	// $var = str_replace("'",'', $var);
    	// dd($var);
        $location3 = DB::table($var)->where('status', 1)->where('company_id',$company_id)->orderBy('rolename', 'asc')->pluck('rolename', 'role_id');
	     
	     return $location3;
	}

        public static function emp_code($var)
    {   
        $company_id = Auth::user()->company_id;
        // $var = str_replace("'",'', $var);
        // dd($var);
        $location3 = DB::table($var)->where('company_id',$company_id)->pluck('emp_code', 'id');
         
         return $location3;
    }

          public static function senior_name($var)
    {   
        $company_id = Auth::user()->company_id;
        // $var = str_replace("'",'', $var);
        // dd($var);
        $location3 = DB::table($var)->where('company_id',$company_id)->groupBy('person.id')->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"), 'id');
         
         return $location3;
    }


    public static function companyData()
    {   
        $company_id = Auth::user()->company_id;
       
        $location3 = DB::table('company')->where('id',$company_id)->first();
         
         return $location3;
    }
  
	
}
