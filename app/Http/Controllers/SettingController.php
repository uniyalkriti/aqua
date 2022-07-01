<?php

namespace App\Http\Controllers;

use App\Company;
use App\Location1;
use App\Location3;
use App\PersonDetail;
use App\PersonLogin;
use DB;
use Auth;
use App\User;
use App\Person;
use App\_role;
use App\UserTodaysAttendanceEnabledLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class SettingController extends Controller
{
	public function user_monthly_target(Request $request)
	{
		$user_details = Person::join('person_login','person_login.person_id','=','person.id')
						->join('_role','_role.role_id','=','person.role_id')
						->join('location_view','location_view.l3_id','=','person.state_id')
						->groupBy('person.id')
						->get();

		 return view('userMonthlyTarget.index', [
            'records' => $user_details,
        ]);
	}

	public function save_monthly_target(Request $request)
	{
		if(!empty($request->month))
		{
				$arr=[];
				$insert=[];
			
				$month = $request->month;
				$company_id = Auth::user()->company_id;

				foreach ($request->target as $k=>$d)
				{  
					$arr['target']=$d;
					$arr['user_id']=$request->user_id[$k];

					$insert[]=$arr; 
				}
				// dd($insert);
				foreach ($insert as $ik=>$id)
				{

					$array=[
						'company_id'=> $company_id,
						'user_id'=> $id["user_id"],
						'month'=> $month,
						'target'=> $id['target'],
						'status'=> 1
					];

						$check=DB::table('user_target')->insert($array);
					
				}
			
					if ((count($check)>0)) {
					
						Session::flash('message', 'Target Inserted successfully');
						Session::flash('class', 'success');
					} else {
						
					
						Session::flash('message', 'Something went wrong!');
						Session::flash('class', 'danger');
					}

					return redirect()->intended('/user_monthly_target');
			}
			else{
				return redirect()->intended('/user_monthly_target');
			}
		
	}
}