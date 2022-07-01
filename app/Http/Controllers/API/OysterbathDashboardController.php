<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\UserDetail;
use App\Person;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Session;
use DateTime;

class OysterbathDashboardController extends Controller
{
    public $successStatus = 200;

    ####################
     public function getJuniorUser($code)
    {
        $res1="";
        $res2="";
        $details = UserDetail::where('person_id_senior',$code)
        	->join('person_login','person_login.person_id','=','person.id')
        	->where('person_status',1)
            ->select('id as user_id')->get();
            $num = count($details);  
            if($num>0)
            {
                foreach($details as $key=>$res2)
                {
                    if($res2->user_id!="")
                    {
                        //$product = collect([1,2,3,4]);
                        Session::push('juniordata', $res2->user_id);
                       // $_SESSION['juniordata'][]=$res2->user_id;
                        $this->getJuniorUser($res2->user_id);
                    }
                }
                
            }
            else
            {
                foreach($details as $key1=>$res1)
                {
                    if($res1->user_id!="")
                    {
                        Session::push('juniordata', $res1->user_id);
                        // $_SESSION['juniordata'][]= $res1->user_id;
                    }
                }

                
            }
            return 1;
        }

	public function one_view_junior_data_oyster(Request $request)
	{
		$validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'role_id' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'company_id' => 'required',
        ]);
		if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
		$user_id = $request->user_id;	
		$role_id=$request->role_id;
		$from_date = $request->from_date; 
		$to_date = $request->to_date;
		$company_id = $request->company_id;
		$startTime = strtotime($from_date);
		$endTime = strtotime($to_date);
	    for ($currentDate = $startTime; $currentDate <= $endTime;  $currentDate += (86400)) 
	    { 
		    $Store = date('Y-m-d', $currentDate); 
		    $datearray[] = $Store; 
	    }
		$datasenior = [];
		$user_details = [];
		$senior_details = [];
		$datasenior[]=$user_id;

		// dd($datasenior);
        if($role_id==1 || $role_id==50)
        {
           $datasenior='';
        }
        else
        { 
            
            Session::forget('juniordata');
            $login_user=$user_id;
             
            $datasenior_call=self::getJuniorUser($login_user);
            $datasenior = session('juniordata');
            // dd($datasenior);
			if(!empty($datasenior))
			{
				$datasenior[]=$login_user;
			}
			else
			{
				$datasenior[] = $user_id;
			}
        }
        // dd($datasenior);
        /*// 
			*******junior data module starts here on the behalf of role id ********
        //*/
     //    $get_role_group = DB::table('_role')->where('role_id',$role_id)->first();
    	// $role_group_data[] = $get_role_group->role_sequence;
    	// $role_group_data[] = $get_role_group->role_sequence+1;
		$junior_id_array = array();
		$finaly_junior_data = array();

    	// dd($role_group_data);
    	$check_and_get_user_id = DB::table('person')
    							->join('person_login','person_login.person_id','=','person.id')
    							->join('_role','_role.role_id','=','person.role_id')
    							->where('person_status',1)
    							->where('person_id_senior',$user_id)
    							->where('person.company_id',$company_id)
    							->where('person_login.company_id',$company_id)
    							->get();
    							// dd()
    							// dd($check_and_get_user_id);
		if(!empty($check_and_get_user_id))
		{
			foreach ($check_and_get_user_id as $c_key => $c_value) 
			{
				$junior_id_array[] = $c_value->id;
			}
			// dd($junior_id_array);
			$datasenior_id = array();
			foreach ($junior_id_array as $key => $value) 
			{
					$user_id_junior = $value;
				
				Session::forget('juniordata');
	            $login_user=$user_id_junior;
	             
	            $datasenior_call=self::getJuniorUser($login_user);
	            $datasenior_id = session('juniordata');
	            // dd($datasenior);
				if(!empty($datasenior))
				{
					$datasenior_id[]=$login_user;
				}
				else
				{
					$datasenior_id[] = $user_id_junior;
				}
				// dd($datasenior_id);

				 	$junior_array_data[$user_id_junior]['total_sales_team'] = COUNT($datasenior_id);	

					$junior_array_data[$user_id_junior]['attendance']  = DB::table('user_daily_attendance')
										->join('person','person.id','=','user_daily_attendance.user_id')
										->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d')<='$to_date'")->whereIn('user_id',$datasenior_id)
										->where('user_daily_attendance.company_id',$company_id)
										->where('person.company_id',$company_id)
										->count();

					$junior_array_data[$user_id_junior]['total_meeting'] = DB::table('meeting_order_booking')
																		->whereIn('user_id',$datasenior_id)
																		->where('company_id',$company_id)
																		->where('status',1)
																		->count();

					$junior_array_data[$user_id_junior]['meeting_with_dealer'] = DB::table('meeting_order_booking')
																		->where('meeting_id',1)
																		->whereIn('user_id',$datasenior_id)
																		->where('company_id',$company_id)
																		->where('status',1)
																		->count();

					$junior_array_data[$user_id_junior]['meeting_with_customer'] = DB::table('meeting_order_booking')
																		->where('meeting_id',3)
																		->whereIn('user_id',$datasenior_id)
																		->where('company_id',$company_id)
																		->where('status',1)
																		->count();

					$junior_array_data[$user_id_junior]['meeting_with_arc'] = DB::table('meeting_order_booking')
																		->where('meeting_id',2)
																		->whereIn('user_id',$datasenior_id)
																		->where('company_id',$company_id)
																		->where('status',1)
																		->count();

					$junior_array_data[$user_id_junior]['meeting_with_builder'] = DB::table('meeting_order_booking')
																		->where('meeting_id',4)
																		->whereIn('user_id',$datasenior_id)
																		->where('company_id',$company_id)
																		->where('status',1)
																		->count();

					$junior_array_data[$user_id_junior]['working_in_office'] = DB::table('meeting_order_booking')
																		->where('meeting_id',5)
																		->whereIn('user_id',$datasenior_id)
																		->where('company_id',$company_id)
																		->where('status',1)
																		->count();
					

					$further_junior_data = DB::table('person')->join('person_login','person_login.person_id','=','person.id')
									->join('_role','_role.role_id','=','person.role_id')
									->select('person.role_id as role_id','rolename',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as use_name"),'person.id as uid')
									->where('person_status',1)
									->where('person.company_id',$company_id)
									->where('person_login.company_id',$company_id)
									->where('_role.company_id',$company_id)
									->where('person.id',$value)->get();

									// dd($junior_array_data);
					foreach ($further_junior_data as $f_key => $f_value) 
					{
						$u_id = $f_value->uid;
						$junior_role_id = $f_value->role_id;
						// dd($junior_array_data[$u_id]);
						$final_junior_data['designation'] = $f_value->rolename;
						$final_junior_data['junior_id'] = $f_value->uid;
						$final_junior_data['user_name'] = $f_value->use_name;
						$final_junior_data['sales_team']=!empty($junior_array_data[$u_id]['total_sales_team'])?$junior_array_data[$u_id]['total_sales_team']:'0';
						$final_junior_data['attendance']=!empty($junior_array_data[$u_id]['attendance'])?$junior_array_data[$u_id]['attendance']:'0';
						$final_junior_data['total_meeting']=!empty($junior_array_data[$u_id]['total_meeting'])?$junior_array_data[$u_id]['total_meeting']:'0';
						$final_junior_data['meeting_with_dealer']=!empty($junior_array_data[$u_id]['meeting_with_dealer'])?$junior_array_data[$u_id]['meeting_with_dealer']:'0';
						$final_junior_data['meeting_with_customer']=!empty($junior_array_data[$u_id]['meeting_with_customer'])?$junior_array_data[$u_id]['meeting_with_customer']:'0';
						$final_junior_data['meeting_with_arc']=!empty($junior_array_data[$u_id]['meeting_with_arc'])?$junior_array_data[$u_id]['meeting_with_arc']:'0';
						$final_junior_data['meeting_with_builder']=!empty($junior_array_data[$u_id]['meeting_with_builder'])?$junior_array_data[$u_id]['meeting_with_builder']:'0';
						$final_junior_data['working_in_office']=!empty($junior_array_data[$u_id]['working_in_office'])?$junior_array_data[$u_id]['working_in_office']:'0';
						

						$finaly_junior_data[] = $final_junior_data;

					}
			}
			// dd($qwerty);
		}
		else
		{
			$finaly_junior_data = [];
		}
    				
		// dd($finaly_junior_data);			
		/*// 
			*******junior data module ends here on the behalf of role id ********
        //*/

		/*// 
			*******overall data module starts here on the behalf of user_id and only all grand total data display  ********
        //*/
        $total_sales_team = Person::join('person_login','person_login.person_id','=','person.id')->where('person_status',1)
    						->where('person.company_id',$company_id);
        					if(!empty($datasenior))
        					{
        						$total_sales_team->whereIn('person.id',$datasenior);
        					}
        					$total_sales_team_data = $total_sales_team->count();

		$total_attendance  = DB::table('user_daily_attendance')
							->where('company_id',$company_id)
							->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d')<='$to_date'");
							if(!empty($datasenior))
        					{
        						$total_attendance->whereIn('user_id',$datasenior);
        					}
							$total_attendance_data = $total_attendance->count();

							

		$working_in_office_data = DB::table('meeting_order_booking')
							->where('company_id',$company_id)
							->where('meeting_id',5)
							->whereRaw("DATE_FORMAT(current_date_m,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(current_date_m,'%Y-%m-%d')<='$to_date'");
							if(!empty($datasenior))
							{
								$working_in_office_data->whereIn('user_id',$datasenior);
							}
							$working_in_office = $working_in_office_data->count();

		$builder_meeting_data = DB::table('meeting_order_booking')
							->where('company_id',$company_id)
							->where('meeting_id',4)
							->whereRaw("DATE_FORMAT(current_date_m,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(current_date_m,'%Y-%m-%d')<='$to_date'");
							if(!empty($datasenior))
							{
								$builder_meeting_data->whereIn('user_id',$datasenior);
							}
							$builder_meeting = $builder_meeting_data->count();


		$customer_meeting_data = DB::table('meeting_order_booking')
							->where('company_id',$company_id)
							->where('meeting_id',3)
							->whereRaw("DATE_FORMAT(current_date_m,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(current_date_m,'%Y-%m-%d')<='$to_date'");
							if(!empty($datasenior))
							{
								$customer_meeting_data->whereIn('user_id',$datasenior);
							}
							$customer_meeting = $customer_meeting_data->count();

		$arc_meeting_data = DB::table('meeting_order_booking')
							->where('company_id',$company_id)
							->where('meeting_id',2)
							->whereRaw("DATE_FORMAT(current_date_m,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(current_date_m,'%Y-%m-%d')<='$to_date'");
							if(!empty($datasenior))
							{
								$arc_meeting_data->whereIn('user_id',$datasenior);
							}
							$arc_meeting = $arc_meeting_data->count();


		$dealer_meeting_data = DB::table('meeting_order_booking')
							->where('company_id',$company_id)
							->where('meeting_id',1)
							->whereRaw("DATE_FORMAT(current_date_m,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(current_date_m,'%Y-%m-%d')<='$to_date'");
							if(!empty($datasenior))
							{
								$dealer_meeting_data->whereIn('user_id',$datasenior);
							}
							$dealer_meeting = $dealer_meeting_data->count();
		

		$person_data = DB::table('person')->join('person_login','person_login.person_id','=','person.id')
						->join('_role','_role.role_id','=','person.role_id')
						->select('person.role_id as role_id','rolename',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as use_name"))
						->where('person_status',1)
						->where('person.company_id',$company_id)
						->where('person_login.company_id',$company_id)
						->where('person.id',$user_id);
						$details_data = $person_data->get();
						// dd($details_data);
		/*// 
			*******overall data module ends here on the behalf of user_id and only all grand total data display  ********
        //*/

		/*// 
			*******foreach of return all data junior data or overall data starts here   ********
        //*/
		foreach ($details_data as $key => $value) 
		{
			// dd($value);
			$user_details['designation']= $value->rolename;
			$user_details['user_name'] = $value->use_name;
			$user_details['grand_total_sales_team'] = !empty($total_sales_team_data)?$total_sales_team_data:'0';
			$user_details['grand_total_attendance'] = !empty($total_attendance_data)?$total_attendance_data:'0';
			$user_details['grand_dealer_meeting_data'] = !empty($dealer_meeting)?$dealer_meeting:'0';
			$user_details['grand_arc_meeting_data'] = !empty($arc_meeting)?$arc_meeting:'0';
			$user_details['grand_customer_meeting_data'] = !empty($customer_meeting)?$customer_meeting:'0';
			$user_details['grand_builder_meeting_data'] = !empty($builder_meeting)?$builder_meeting:'0';
			$user_details['grand_working_in_office_data'] = !empty($working_in_office)?$working_in_office:'0';
		
			if(empty($finaly_junior_data))
			{
				$user_details['is_next'] = false; 
			}
			else
			{
				$user_details['is_next'] = true; 

			}

		}
        
        return response()->json(['response' => true,'data'=>$user_details,'junior_details'=>$finaly_junior_data]);
		/*// 
			*******foreach of return all data junior data or overall data ends here   ********
        //*/
	}
	# **********************ends hre *****************************#

	public function one_view_date_wise_self_data_oyster(Request $request)
	{
		$validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'company_id' => 'required',
        ]);
		if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $user_id = $request->user_id;
        $company_id = $request->company_id;

		$startTime = strtotime($from_date);
		$endTime = strtotime($to_date);

	    for ($currentDate = $startTime; $currentDate <= $endTime;  
	                                    $currentDate += (86400)) { 
	                                        
	    $Store = date('Y-m-d', $currentDate); 
	    $datearray[] = $Store; 
	    } 
	    // dd($datearray);
	    rsort($datearray);
	    $out = array();
	    foreach ($datearray as $key => $value) 
	    {    
	    	// dd($value);
	       

			$out[$value]['attendance'] = DB::table('user_daily_attendance')
						->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
						->select('work_date','_working_status.name as work_status')
						->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$value'")
						->where('user_daily_attendance.company_id',$company_id)
						->where('_working_status.company_id',$company_id)
						->where('user_id',$user_id)
						->first();

			$out[$value]['check_out'] = DB::table('check_out')
						->join('_working_status','_working_status.id','=','check_out.work_status')
						->select('work_date','_working_status.name as work_status')
						->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$value'")
						->where('check_out.company_id',$company_id)
						->where('user_id',$user_id)
						->first();

			$out[$value]['meeting'] = DB::table('meeting_order_booking')
							->where('company_id',$company_id)
							->where('user_id',$user_id)
							->whereRaw("DATE_FORMAT(current_date_m,'%Y-%m-%d')='$value'")
							->count();
							

		

			$user_daily_tracking[$value] = DB::table('user_work_tracking')
									->where('user_id',$user_id)
									->where('track_date',$value)
									->where("lat_lng","!=","0.0")
									->where("lat_lng","!=","0.0,0.0")
									->where('company_id',$company_id)
									->select('battery_status','gps_status','track_date','track_time','lat_lng','track_address','status')
									->orderBy('track_date','DESC')
									->get();
				// dd($user_daily_tracking[$value]);
				// $tracking_final_array = array();
				foreach ($user_daily_tracking[$value] as $key_tracking => $value_tracking) 
				{
					$tracking_out['battery_status'] = $value_tracking->battery_status;
					$tracking_out['gps_status'] = $value_tracking->gps_status;
					$tracking_out['track_date'] = $value_tracking->track_date;
					$tracking_out['track_time'] = $value_tracking->track_time;
					$tracking_out['lat_lng'] = str_replace(' ', ',',$value_tracking->lat_lng);
					$tracking_out['track_address'] = $value_tracking->track_address;
					$tracking_out['status'] = $value_tracking->status;
					$tracking_final_array[$value][] = $tracking_out;
				}
		

			
			



			// dd($user_daily_tracking);
			$final_array[$key]['date'] = $value;
			
			$final_array[$key]['work_status'] = !empty($out[$value]['attendance'])?$out[$value]['attendance']->work_status:'';
			$final_array[$key]['check_in_time'] = !empty($out[$value]['attendance'])?$out[$value]['attendance']->work_date:'';
			$final_array[$key]['check_out'] = !empty($out[$value]['check_out']->work_date)?$out[$value]['check_out']->work_date:'';
			$final_array[$key]['meeting'] = !empty($out[$value]['meeting'])?$out[$value]['meeting']:'';
			

			$final_array[$key]['tracking'] = !empty($tracking_final_array[$value])?$tracking_final_array[$value]:array();
		// dd()

		}
        return response()->json(['response' => true,'data'=>$final_array]);
		
	}

	
	

}

