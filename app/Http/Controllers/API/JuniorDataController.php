<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\UserDetail;
use App\Person;
use Illuminate\Support\Facades\Auth;
use App\TableReturn;
use Validator;
use DB;
use Session;
use DateTime;

class JuniorDataController extends Controller
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

	public function one_view_junior_data(Request $request)
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

    	// get table name for sales 

        $table_name = TableReturn::table_return($from_date,$to_date);
        // dd($table_name);


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

					$junior_array_data[$user_id_junior]['total_primary_sale_value'] = DB::table('primary_sale_view')
										->join('person','person.id','=','primary_sale_view.created_person_id')
										->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(sale_date,'%Y-%m-%d')<='$to_date'")
										->select(DB::raw("sum((rate*pcs)+(cases*pr_rate)) AS total_primary_sale_value"))
										->whereIn('person.id',$datasenior_id)
										->where('primary_sale_view.company_id',$company_id)
										->where('person.company_id',$company_id)
										->first();

					$junior_array_data[$user_id_junior]['total_secondary_sale_value'] = DB::table($table_name)
								->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
								->join('person','person.id','=',$table_name.'.user_id')
								->select(DB::raw("sum(rate*quantity) AS total_secondary_sale_value"))
                            	->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
								// ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date'AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
								->whereIn('user_id',$datasenior_id)
								->where($table_name.'.company_id',$company_id)
								->where('user_sales_order_details.company_id',$company_id)
								->first();

					$junior_array_data[$user_id_junior]['beatsale'] = DB::table($table_name)
									->join('person','person.id','=',$table_name.'.user_id')
                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
									// ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date'AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
				    				->select(DB::raw('count(DISTINCT location_id) as beatsale'))->whereIn('user_id',$datasenior_id)
				    				->where($table_name.'.company_id',$company_id)
				    				->where('person.company_id',$company_id)
				    				->first();


				    $junior_array_data[$user_id_junior]['outletsale'] = DB::table($table_name)
									->join('person','person.id','=',$table_name.'.user_id')
									->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date'AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
				    				->select(DB::raw('count(DISTINCT retailer_id,date) as outletsale'))->whereIn('user_id',$datasenior_id)
				    				->where($table_name.'.company_id',$company_id)
				    				->where('person.company_id',$company_id)
				    				->first();

					$junior_array_data[$user_id_junior]['retailer_count'] = DB::table('retailer')
									->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')>='$from_date'AND DATE_FORMAT(created_on,'%Y-%m-%d')<='$to_date'")
				    				->select(DB::raw('count(DISTINCT id) as count'))->whereIn('created_by_person_id',$datasenior_id)
				    				->where('company_id',$company_id)
				    				->first();

    				$junior_array_data[$user_id_junior]['total_retailer_count'] = DB::table('dealer_location_rate_list')
																				->join('retailer','retailer.location_id','=','dealer_location_rate_list.location_id')
																				->select(DB::raw("COUNT(DISTINCT retailer.id) as total_retailer_count"))
				    															->where('dealer_location_rate_list.company_id',$company_id)
																				->whereIn('dealer_location_rate_list.user_id',$datasenior_id)
																				->first();

    				$junior_array_data[$user_id_junior]['target'] = DB::table('monthly_tour_program')
														->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d')>='$from_date'AND DATE_FORMAT(working_date,'%Y-%m-%d')<='$to_date'")
	    												->select(DB::raw('sum(rd) target_sum'))
	    												->whereIn('person_id',$datasenior_id)
	    												->where('company_id',$company_id)
	    												->first();
					// lpsc starts here 
					$junior_array_data[$user_id_junior]['lpsc'] = DB::table($table_name)
																->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
																->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date'AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
																->whereIn('user_id',$datasenior_id)
																->where($table_name.'.company_id',$company_id)
																->where('user_sales_order_details.company_id',$company_id)
																// ->groupBy('product_id')
																->count();

					
					$sku_date_wise = $junior_array_data[$user_id_junior]['lpsc']*count($datearray);
					// dd($sku_date_wise);
					// lpsc ends here 

					$junior_array_data[$user_id_junior]['total_call'] = DB::table($table_name)
								->join('person','person.id','=',$table_name.'.user_id')
								->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date'AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
			        			->select(DB::raw('count(call_status) as total_call'))->whereIn('user_id',$datasenior_id)
			        			->where($table_name.'.company_id',$company_id)
			        			->where('person.company_id',$company_id)
			        			->first();

					$junior_array_data[$user_id_junior]['productive_call'] = DB::table($table_name)
								->join('person','person.id','=',$table_name.'.user_id')
								->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date'AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
								->where('call_status',1)
			        			->select(DB::raw('count(order_id) as productive_call'))->whereIn('user_id',$datasenior_id)
			        			->where($table_name.'.company_id',$company_id)
			        			->where('person.company_id',$company_id)
			        			->first();

					$beat_id_data = DB::table('monthly_tour_program')
								->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d')>='$from_date'AND DATE_FORMAT(working_date,'%Y-%m-%d')<='$to_date'")->where('company_id',$company_id);
								if(!empty($value))
								{
									$beat_id_data->whereIn('person_id',$datasenior_id);
								}
								$G_beat_id_data = $beat_id_data->pluck('locations');
					// dd($G_beat_id_data);
					$junior_array_data[$user_id_junior]['planned_call'] = DB::table('retailer')
									->whereIn('location_id',$G_beat_id_data)
									->where('company_id',$company_id)
									->distinct('id')->count('id');

					$further_junior_data = DB::table('person')->join('person_login','person_login.person_id','=','person.id')
									->join('_role','_role.role_id','=','person.role_id')
									->select('person.role_id as role_id','rolename',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as use_name"),'person.id as uid','person.mobile')
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
						$final_junior_data['user_name'] = $f_value->use_name."\n".$f_value->mobile;
						$final_junior_data['sales_team']=$junior_array_data[$u_id]['total_sales_team'];
						$final_junior_data['attendance']=$junior_array_data[$u_id]['attendance'];
						$final_junior_data['primary_sale']=!empty($junior_array_data[$u_id]['total_primary_sale_value']->total_primary_sale_value)?$junior_array_data[$u_id]['total_primary_sale_value']->total_primary_sale_value:'0';
						$final_junior_data['secondary_sale']=!empty($junior_array_data[$u_id]['total_secondary_sale_value']->total_secondary_sale_value)?$junior_array_data[$u_id]['total_secondary_sale_value']->total_secondary_sale_value:'0';
						$final_junior_data['beat_coverage']=$junior_array_data[$u_id]['beatsale']->beatsale;
						$final_junior_data['outlet_coverage']=$junior_array_data[$u_id]['outletsale']->outletsale;
						$final_junior_data['retailer_count']=$junior_array_data[$u_id]['retailer_count']->count;
						$final_junior_data['total_retailer_count']=$junior_array_data[$u_id]['total_retailer_count']->total_retailer_count;

						$final_junior_data['target_data']=!empty($junior_array_data[$u_id]['target']->target_sum)?$junior_array_data[$u_id]['target']->target_sum:'0';

						$final_junior_data['total_call']=$junior_array_data[$u_id]['total_call']->total_call;

						if($junior_array_data[$u_id]['planned_call'] == 0)
						{
							$final_junior_data['effective_coverage'] = "0%";
						}
						else{
						$final_junior_data['effective_coverage']=  ($junior_array_data[$u_id]['total_call']->total_call==0)?'0':round((($junior_array_data[$u_id]['total_call']->total_call/$junior_array_data[$u_id]['planned_call'])*100)).'%';
						}

						if($junior_array_data[$u_id]['planned_call'] == 0)
						{
							$final_junior_data['productivity'] = "0%";
						}
						else {
						$final_junior_data['productivity']= ($junior_array_data[$u_id]['productive_call']->productive_call==0)?'0':round((($junior_array_data[$u_id]['productive_call']->productive_call/$junior_array_data[$u_id]['planned_call'])*100)).'%';
						}

						$final_junior_data['lpsc']= ($junior_array_data[$u_id]['productive_call']->productive_call==0)?'0':round((($sku_date_wise)/($junior_array_data[$u_id]['productive_call']->productive_call)));

						$final_junior_data['total_productive_call']=$junior_array_data[$u_id]['productive_call']->productive_call;
						$final_junior_data['planned_call']=$junior_array_data[$u_id]['planned_call'];
						$final_junior_data['junior_id']=$u_id;
						$final_junior_data['junior_role_id']=$junior_role_id;

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

		// $total_attendance  = DB::table('user_daily_attendance')
		// 					->where('company_id',$company_id)
		// 					->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d')<='$to_date'");
		// 					if(!empty($datasenior))
  //       					{
  //       						$total_attendance->whereIn('user_id',$datasenior);
  //       					}
		// 					$total_attendance_data = $total_attendance->count();

        $total_attendance  = DB::table('user_daily_attendance')
        					->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
        					->select('user_daily_attendance.id','_working_status.name')
							->where('user_daily_attendance.company_id',$company_id)
							->where('_working_status.company_id',$company_id)
							->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$to_date'");
							if(!empty($datasenior))
        					{
        						$total_attendance->whereIn('user_id',$datasenior);
        					}
		$total_attendance_data = $total_attendance->groupBy('user_daily_attendance.id')->get();

		$attCount = array();
		$absent = array();
		$working = array();
		foreach ($total_attendance_data as $akey => $avalue) {
			$attCount[] = $avalue->id;

			if(0 === strcasecmp($avalue->name,'leave') || 0 === strcasecmp($avalue->name,'absent') || 0 === strcasecmp($avalue->name,'Holiday') || 0 === strcasecmp($avalue->name,'Holidays') || 0 === strcasecmp($avalue->name,'Weekly Off')){
				$absent[] = '1';
			}else{
				$working[] = '1';
			}



		}


		$primary_sale = DB::table('primary_sale_view')
							->where('company_id',$company_id)
							->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(sale_date,'%Y-%m-%d')<='$to_date'")
							->select(DB::raw("sum((rate*pcs)+(cases*pr_rate)) AS total_primary_sale_value"),DB::raw("COUNT(DISTINCT dealer_id) as dealer_visit"));
							if(!empty($datasenior))
							{
								$primary_sale->whereIn('created_person_id',$datasenior);
							}
							$primary_sale_data = $primary_sale->first();

		$secondary_sale = DB::table($table_name)
					->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
					->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date'AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
					->where($table_name.'.company_id',$company_id)
					->where('user_sales_order_details.company_id',$company_id)
					->select(DB::raw("sum(rate*quantity) AS total_secondary_sale_value"),DB::raw("COUNT(DISTINCT user_sales_order_details.product_id) as unique_sku"));
					if(!empty($datasenior))
					{
						$secondary_sale->whereIn('user_id',$datasenior);
					}
					$secondary_sale_data = $secondary_sale->first();

		$beat_coverage = DB::table($table_name)
						->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date'AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
						->where('company_id',$company_id)
	    				->select(DB::raw('count(DISTINCT location_id) as beatsale'));
	    				if(!empty($datasenior))
						{
							$beat_coverage->whereIn('user_id',$datasenior);
						}
						$beat_coverage_data = $beat_coverage->first();

		$outlet_coverage = DB::table($table_name)
						->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date'AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
						->where('company_id',$company_id)
	    				->select(DB::raw('count(DISTINCT retailer_id,date) as outletsale'));
	    				if(!empty($datasenior))
						{
							$outlet_coverage->whereIn('user_id',$datasenior);
						}
						$outlet_coverage_data = $outlet_coverage->first();


		$retailer_count = DB::table('retailer')
						->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')>='$from_date'AND DATE_FORMAT(created_on,'%Y-%m-%d')<='$to_date'")
						->where('company_id',$company_id)
	    				->select(DB::raw('count(DISTINCT id) as count'));
	    				if(!empty($datasenior))
						{
							$retailer_count->whereIn('created_by_person_id',$datasenior);
						}
						$retailer_count_data = $retailer_count->first();

		$total_retailer_count_data = DB::table('dealer_location_rate_list')
						->join('retailer','retailer.location_id','=','dealer_location_rate_list.location_id')
						->where('dealer_location_rate_list.company_id',$company_id)
	    				->select(DB::raw('count(DISTINCT retailer.id) as total_retailer_count'));
	    				if(!empty($datasenior))
						{
							$total_retailer_count_data->whereIn('dealer_location_rate_list.user_id',$datasenior);
						}
		$total_retailer_count = $total_retailer_count_data->first();

		$target = DB::table('monthly_tour_program')
						->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d')>='$from_date'AND DATE_FORMAT(working_date,'%Y-%m-%d')<='$to_date'")
	    				->select(DB::raw('sum(rd) target_sum'),DB::raw('sum(primary_ord) target_sum_primary'))
	    				->where('company_id',$company_id)
	    				->groupBy('person_id');
	    				if(!empty($datasenior))
						{
							$target->whereIn('person_id',$datasenior);
						}
						$target_data = $target->first();


		$total_call = DB::table($table_name)
					->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date'AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
					->where('company_id',$company_id)
        			->select(DB::raw('count(call_status) as total_call'));
    				if(!empty($datasenior))
					{
						$total_call->whereIn('user_id',$datasenior);
					}
					$total_call_data = $total_call->first();

		$total_productive_call = DB::table($table_name)
					->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date'AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
					->where('call_status',1)
					->where('company_id',$company_id)
        			->select(DB::raw('count(order_id) as productive_call'));
    				if(!empty($datasenior))
					{
						$total_productive_call->whereIn('user_id',$datasenior);
					}
					$total_productive_call_data = $total_productive_call->first();

		$lpsc_data = DB::table($table_name)
					->join('user_sales_order_details',$table_name.'.order_id','=','user_sales_order_details.order_id')
					->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date'AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
					->where($table_name.'.company_id',$company_id)
					->where('user_sales_order_details.company_id',$company_id);
        			// ->groupBy('product_id');
    				if(!empty($datasenior))
					{
						$total_productive_call->whereIn('user_id',$datasenior);
					}
					$lpsc = $lpsc_data->count();
		$grand_sku_data = $lpsc;
					// dd($grand_sku_data);

		$beat_id = DB::table('monthly_tour_program')
					->where('monthly_tour_program.company_id',$company_id)
					->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d')>='$from_date'AND DATE_FORMAT(working_date,'%Y-%m-%d')<='$to_date'");
					if(!empty($datasenior))
					{
						$beat_id->whereIn('person_id',$datasenior);
					}
					$beat_id_data = $beat_id->pluck('locations');
		// dd($beat_id_data);
		$planned_call = DB::table('retailer')
						->whereIn('location_id',$beat_id_data)
						->where('company_id',$company_id)
						->distinct('id')->count('id');

		$person_data = DB::table('person')->join('person_login','person_login.person_id','=','person.id')
						->join('_role','_role.role_id','=','person.role_id')
						->select('person.role_id as role_id','rolename',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as use_name"),'person.mobile')
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
			$user_details['user_name'] = $value->use_name."\n".$value->mobile;
			$user_details['grand_total_sales_team'] = !empty($total_sales_team_data)?$total_sales_team_data:'0';
			// $user_details['grand_total_attendance'] = !empty($total_attendance_data)?$total_attendance_data:'0';
			$user_details['grand_total_attendance'] = !empty($attCount)?COUNT($attCount):'0';
			$user_details['working'] = !empty($working)?COUNT($working):'0';
			$user_details['absent'] = !empty($absent)?COUNT($absent):'0';
			$user_details['grand_total_primary_sale'] = !empty($primary_sale_data->total_primary_sale_value)?$primary_sale_data->total_primary_sale_value:'0';
			$user_details['distributor'] = !empty($primary_sale_data->dealer_visit)?$primary_sale_data->dealer_visit:'0';
			$user_details['grand_total_secondary_sale'] = !empty($secondary_sale_data->total_secondary_sale_value)?$secondary_sale_data->total_secondary_sale_value:'0';
			$user_details['grand_total_beat_coverage'] = !empty($beat_coverage_data->beatsale)?$beat_coverage_data->beatsale:'0';
			$user_details['grand_total_outlet_coverage'] = !empty($outlet_coverage_data->outletsale)?$outlet_coverage_data->outletsale:'0';
			$user_details['grand_retailer_count_data'] = !empty($retailer_count_data->count)?$retailer_count_data->count:'0';
			$user_details['grand_total_retailer_count'] = !empty($total_retailer_count->total_retailer_count)?$total_retailer_count->total_retailer_count:'0';
			$user_details['grand_target_data'] = !empty($target_data->target_sum)?$target_data->target_sum:'0';
			$user_details['grand_total_call'] = !empty($total_call_data->total_call)?$total_call_data->total_call:'0';
			$user_details['grand_total_productive_call'] = !empty($total_productive_call_data->productive_call)?$total_productive_call_data->productive_call:'0';
			$user_details['grand_total_planned_call'] = !empty($planned_call)?$planned_call:'0'; 
			$user_details['no_of_sku'] = !empty($secondary_sale_data->unique_sku)?$secondary_sale_data->unique_sku:'0';
			$user_details['primary_target'] = !empty($target_data->target_sum_primary)?$target_data->target_sum_primary:'0';


			if($planned_call == 0)
			{
				$user_details['grand_total_effective_coverage'] = "0%";
			}
			else{
			$user_details['grand_total_effective_coverage'] = ($total_call_data->total_call==0)?'0':round((($total_call_data->total_call/$planned_call)*100)).'%';
			}

			if($planned_call == 0)
			{
				$user_details['grand_total_productivity'] = "0%";
			}
			else{
			$user_details['grand_total_productivity'] = ($total_productive_call_data->productive_call==0)?'0':round(((($total_productive_call_data->productive_call)/($planned_call))*100)).'%';
			}

			$user_details['grand_total_lpsc'] = ($total_productive_call_data->productive_call==0)?'0':round((($grand_sku_data)/($total_productive_call_data->productive_call)));

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

	public function one_view_date_wise_self_data(Request $request)
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

        $table_name = TableReturn::table_return($from_date,$to_date);
        

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
	        $out[$value]['primary_sale'] = DB::table('primary_sale_view')
							->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')='$value'")
							->select(DB::raw("sum((rate*pcs)+(cases*pr_rate)) AS total_primary_sale_value"),'sale_date')
							->where('created_person_id',$user_id)
							->where('company_id',$company_id)
							->first();

			$out[$value]['secondary_sale'] = DB::table($table_name)
						->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
						->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$value'")
						->select(DB::raw("sum(rate*quantity) AS total_secondary_sale_value"),'date')
						->where($table_name.'.company_id',$company_id)
						->where('user_sales_order_details.company_id',$company_id)
						->where('user_id',$user_id)
						->first();

			$out[$value]['attendance'] = DB::table('user_daily_attendance')
						->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
						->select(DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as work_date"),'_working_status.name as work_status','user_daily_attendance.remarks')
						->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$value'")
						->where('user_daily_attendance.company_id',$company_id)
						->where('_working_status.company_id',$company_id)
						->where('user_id',$user_id)
						->first();

			$out[$value]['check_out'] = DB::table('check_out')
						// ->join('_working_status','_working_status.id','=','check_out.work_status')
						->select(DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as work_date"),'check_out.remarks')
						->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$value'")
						->where('check_out.company_id',$company_id)
						->where('user_id',$user_id)
						->first();

			$out[$value]['total_call'] = DB::table($table_name)
						->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$value'")
	        			->select(DB::raw('count(call_status) as total_call'),'date')
	        			->where('company_id',$company_id)
	    				->where('user_id',$user_id)
	    				->first();

			$out[$value]['productive_call'] = DB::table($table_name)
						->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$value'")
	        			->select(DB::raw('count(order_id) as productive_call'),'date')
	        			->where('company_id',$company_id)
	        			->where('user_id',$user_id)
						->where('call_status',1)
	    				->first();

	    	$out[$value]['first_call'] = DB::table($table_name)
						->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$value'")
	        			->select(DB::raw('MIN(time) as first_call'))
	        			->where('company_id',$company_id)
	        			->where('user_id',$user_id)
	    				->first();

	    	$out[$value]['last_call'] = DB::table($table_name)
						->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$value'")
	        			->select(DB::raw('MAX(time) as last_call'))
	        			->where('company_id',$company_id)
	        			->where('user_id',$user_id)
	    				->first();

			$user_daily_tracking[$value] = DB::table('user_work_tracking')
									->where('user_id',$user_id)
									->where('track_date',$value)
									->where("lat_lng","!=","0.0")
									->where("lat_lng","!=","0.0,0.0")
									->where('lat_lng','!=','NULL')
									->where('lat_lng','!=','')
									->where('lat_lng','!=','0,0')
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
		

			$out[$value]['retailer_count'] = DB::table('retailer')
	    				->select(DB::raw('count(DISTINCT id) as count'))
						->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')='$value'")
	    				->where('created_by_person_id',$user_id)
	    				->where('company_id',$company_id)
	    				->first();

			$out[$value]['target'] = DB::table('monthly_tour_program')
	    				->select(DB::raw('sum(rd) target_sum'))
						->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d')='$value'")
	    				->where('person_id',$user_id)
	    				->where('company_id',$company_id)
	    				->groupBy('person_id')
	    				->first();

			$beat_id_data = DB::table('monthly_tour_program')
							->where('company_id',$company_id)
							->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d')='$value'");
							if(!empty($value))
							{
								$beat_id_data->where('person_id',$user_id);
							}
							$G_beat_id_data = $beat_id_data->pluck('locations');
					// dd($G_beat_id_data);
			$planned_call = DB::table('retailer')
						->whereIn('location_id',$G_beat_id_data)
						->where('company_id',$company_id)
						->distinct('id')->count('id');

			$lpsc = DB::table($table_name)
					->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
					->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$value'")
					->where('user_id',$user_id)
					->where($table_name.'.company_id',$company_id)
					->where('user_sales_order_details.company_id',$company_id)
					// ->groupBy('product_id')
					->count();

					
			$sku_date_wise = $lpsc*count($datearray);



			// dd($user_daily_tracking);
			$final_array[$key]['date'] = $value;
			$final_array[$key]['primary_sale'] = !empty($out[$value]['primary_sale']->total_primary_sale_value)?$out[$value]['primary_sale']->total_primary_sale_value:'0';
			$final_array[$key]['secondary_sale'] = !empty($out[$value]['secondary_sale']->total_secondary_sale_value)?$out[$value]['secondary_sale']->total_secondary_sale_value:'0';
			$final_array[$key]['work_status'] = !empty($out[$value]['attendance'])?$out[$value]['attendance']->work_status:'';
			$final_array[$key]['check_in_time'] = !empty($out[$value]['attendance'])?$out[$value]['attendance']->work_date:'';
			$final_array[$key]['check_in_remarks'] = !empty($out[$value]['attendance'])?$out[$value]['attendance']->remarks:'';
			$final_array[$key]['check_out'] = !empty($out[$value]['check_out']->work_date)?$out[$value]['check_out']->work_date:'';
			$final_array[$key]['check_out_remarks'] = !empty($out[$value]['check_out']->remarks)?$out[$value]['check_out']->remarks:'';
			$final_array[$key]['total_call'] = !empty($out[$value]['total_call']->total_call)?$out[$value]['total_call']->total_call:'';
			$final_array[$key]['productive_call'] = !empty($out[$value]['productive_call']->productive_call)?$out[$value]['productive_call']->productive_call:'';

			$final_array[$key]['first_call'] = !empty($out[$value]['first_call']->first_call)?$out[$value]['first_call']->first_call:'';
			$final_array[$key]['last_call'] = !empty($out[$value]['last_call']->last_call)?$out[$value]['last_call']->last_call:'';


			$final_array[$key]['retailer_count'] = !empty($out[$value]['retailer_count']->count)?$out[$value]['retailer_count']->count:'0';
			$final_array[$key]['target_sum'] = !empty($out[$value]['target']->target_sum)?$out[$value]['target']->target_sum:'0';

			$final_array[$key]['effective_coverage'] = ($out[$value]['total_call']->total_call==0)?'0%':round((($planned_call)/($out[$value]['total_call']->total_call)*100)).'%';
			$final_array[$key]['productivity'] = ($out[$value]['productive_call']->productive_call==0)?'0':round((($planned_call)/($out[$value]['productive_call']->productive_call)*100)).'%';
			$final_array[$key]['lpsc'] = ($out[$value]['productive_call']->productive_call==0)?'0':round((($sku_date_wise)/($out[$value]['productive_call']->productive_call)));

			$final_array[$key]['tracking'] = !empty($tracking_final_array[$value])?$tracking_final_array[$value]:array();
		// dd()

		}
		$user_personal_data = array();
		$person_query = Person::join('person_login','person_login.person_id','=','person.id')
                        ->join('location_3','location_3.id','=','person.state_id')
                        ->join('person_details','person_details.person_id','=','person.id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->join('company','company.id','=','person.company_id')
                        ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as full_name"),'person_login.person_image as person_image','rate_list_flag','person.state_id as state_id','is_mtp_enabled','person_username','person.id as user_id','mobile','imei_number','person.email as user_email','rolename as designation','person.role_id as designation_id','emp_code','person_details.address as user_address','location_3.name as state','head_quar as head_quater','person_details.created_on as user_created_date')
                        ->where('person.id',$user_id)
                        // ->where('person_status',1)
                        ->where('person_id_senior','!=',0)
                        ->where('company.id',$company_id)
                        ->get();
        foreach ($person_query as $f_key => $f_value)
        {
        	$user_personal_data['user_id'] = $f_value->user_id;
	        $user_personal_data['person_username'] = $f_value->person_username;
	        $user_personal_data['full_name'] = $f_value->full_name;
	        $image_name = !empty($f_value->person_image)?str_replace('users-profile', '', $f_value->person_image):'';
	        $user_personal_data['person_image'] = !empty($f_value->person_image)?'users-profile/'.$image_name:'';
	        $user_personal_data['mobile'] = $f_value->mobile;
	        $user_personal_data['imei_number'] = $f_value->imei_number;
	        $user_personal_data['user_email'] = $f_value->user_email;
	        $user_personal_data['designation_id'] = $f_value->designation_id;
	        $user_personal_data['designation'] = $f_value->designation;
	        $user_personal_data['emp_code'] = $f_value->emp_code;
	        $user_personal_data['user_address'] = $f_value->user_address;
	        $user_personal_data['state'] = $f_value->state;
	        $user_personal_data['user_created_date'] = $f_value->user_created_date;
        }	
        
        return response()->json(['response' => true,'data'=>$final_array,'user_personal_data'=>$user_personal_data]);
		
	}

	// forcefully update starts here 
	public function force_fully_update(Request $request)
    {
		$validator = Validator::make($request->all(), [
            'version_code' => 'required',
			'version_name' => 'required',
			'company_id'=>'required',
			'user_id'=>'required',
        ]);
		if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $version_code = trim($request->version_code);
        $version_name = trim($request->version_name);
        $user_id = $request->user_id;
        $company_id = $request->company_id;
        # 2 as ignore ; 1 as forcefully updated;

        $veriosn_for_update = "Version: ".$version_name."/".$version_code;
		$versionArr = [
            'version_code_name' => $veriosn_for_update
        ];
		$update_person_veriosn = DB::table('person')->where('company_id',$company_id)->where('id',$user_id)->update($versionArr);

        $check = DB::table('version_management')->where('company_id',$company_id)->orderBy('id','DESC')->first();
        
        $manual_on_off_forcefully_query = DB::table('company')->where('id',$company_id)->first(); 
        $manual_on_off_forcefully_status = $manual_on_off_forcefully_query->manual_on_off_forcefully;
        if(empty($manual_on_off_forcefully_status))
        {
        	return response()->json(['response' => False,'force_status' => 0,'status'=> 0]);
        }
        
        $person = DB::table('person_login')->select('person_status')->where('company_id',$company_id)->where('person_id',$user_id)->first();
        
        $version_name_data_fetch = !empty($check->version_name)?$check->version_name:'';
        
        if(!empty($person))
        {
        	$status = $person->person_status;
        }
        else
        {
        	$status = 0;
        }
      	
      	if($version_name== $version_name_data_fetch)
        {
            $force_status="2";
        }
        else
        {
            $force_status="$manual_on_off_forcefully_status";
            // $force_status="$check->force_status";
        }
        //
        // dd($force_status);

        return response()->json(['response' => TRUE,'force_status' => $force_status,'status'=> $status]);
    }
    public function return_update_status_for_app(Request $request)
    {
    	$validator = Validator::make($request->all(), [
			'company_id'=>'required',
			'version_code' => 'required',
			'version_name' => 'required',
        ]);
		if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $version_check = DB::table('version_management')
        				->where('company_id',$request->company_id)
        				->orderBy('id','DESC')
        				->first();
		if($version_check->version_name == $request->version_name)
		{
			if($version_check->version_code == $request->version_code)
			{
				$status_for_update = FALSE;
			}
			else
			{
				$status_for_update = TRUE;
			}
		}
		else
		{
			$status_for_update = TRUE;
		}
		$message = DB::table('company')->where('id',$request->company_id)->orderBy('id','DESC')->first();
		$message_send = !empty($message->message_dynamic)?$message->message_dynamic:'';
        return response()->json(['response' => TRUE,'status_for_update' => $status_for_update,'message'=> $message_send]);


        				// ->where('version_name',)
    }
	// forcefully update ends here 

	// 
	public function mtp_user_data(Request $request)
	{
		$validator = Validator::make($request->all(), [
            'user_id' => 'required',
			'date' => 'required',
			'company_id'=>'required',
        ]);
		if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
		$user_id = $request->user_id;
		$date = $request->date;
		$company_id = $request->company_id;
		$query_data = array();
		$query_data = DB::table('monthly_tour_program')
					// ->leftJoin('location_view','location_view.l7_id','=','monthly_tour_program.locations')
					->join('person','person.id','=','monthly_tour_program.person_id')
					->join('person_login','person_login.person_id','=','person.id')
					->join('_task_of_the_day','_task_of_the_day.id','=','monthly_tour_program.working_status_id')
					->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'locations','_task_of_the_day.task as work_status')
					->where('monthly_tour_program.person_id',$user_id)
					->where('monthly_tour_program.company_id',$company_id)
					->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d')='$date'")
					// ->whereRaw('working_date',$date)
					->groupBy('monthly_tour_program.person_id','working_date')
					->get();

		$location_data = DB::table('location_view')
						->join('monthly_tour_program','location_view.l7_id','=','monthly_tour_program.locations')
						->where('monthly_tour_program.company_id',$company_id)
						->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d')='$date'")
						->where('monthly_tour_program.person_id',$user_id)
						->where('l7_company_id',$company_id)
						->groupBy('l7_id')
						->pluck(DB::raw("concat(l3_name,'|',l3_id,'|',l7_name,'|',l7_id,'|',l6_name,'|',l6_id) as location_details"),'locations');

		$final_out = [];
		foreach ($query_data as $key => $value) 
		{
			if(!empty($location_data[$value->locations]))
			{
				$break = explode('|',$location_data[$value->locations]);
				$l3_name = $break[0]; 
				$l3_id = $break[1]; 
				$l7_name = $break[2]; 
				$l7_id = $break[3]; 
				$l6_name = $break[4]; 
				$l6_id = $break[5]; 
			}
			$out['user_name'] = !empty($value->user_name)?$value->user_name:''; 
			$out['state_id'] = !empty($l3_id)?$l3_id:''; 
			$out['state'] = !empty($l3_name)?$l3_name:''; 
			$out['beat_id'] = !empty($l7_id)?$l7_id:''; 
			$out['beat'] = !empty($l7_name)?$l7_name:''; 
			$out['town_id'] = !empty($l6_id)?$l6_id:''; 
			$out['town'] = !empty($l6_name)?$l6_name:''; 
			$out['work_status'] = !empty($value->work_status)?$value->work_status:''; 
			$final_out[]  = $out;
		}
		// dd($query_data);
		if(COUNT($query_data))
		{
			return response()->json(['response' => TRUE,'date' => $final_out]);

		}
		else
		{
			return response()->json(['response' => False,'date' => $final_out]);

		}


	}
	// 
	

}

