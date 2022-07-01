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

class PerfomanceController extends Controller
{
    public $successStatus = 200;

    #starts here for junior id 
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
    #end here 

    #attendance data for particular data starts here
	public function user_attendance_data(Request $request)
	{
		$validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
        ]);
		if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

		$user_id = $request->user_id;
		$from_date = $request->from_date; 
		$to_date = $request->to_date;
		$out = array();
		$final_summary_data = array();

		$attendance_data = DB::table('user_daily_attendance')
							->join('person','person.id','=','user_daily_attendance.user_id')
							->join('person_login','person_login.person_id','=','person.id')
							->where('person_status',1)
							->where('user_id',$user_id)
							->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d')<='$to_date'")
							->groupBy('work_status')
							->pluck(DB::raw("COUNT(work_status)"),'work_status');
		// dd($attendance_data);
		$work_status_data = DB::table("_working_status")->get();
		foreach ($work_status_data as $key => $value) 
		{
			$work_name = $value->name;
			$work_id = $value->id;
			$final_array['name'] = $work_name; 
			$final_array['count'] = !empty($attendance_data[$work_id])?$attendance_data[$work_id]:'0'; 
			$out[] = $final_array;
		}
		// dd(sizeof($out));
		$attendance_summary = DB::table('daily_attendance_view')
							->join('person','person.id','=','daily_attendance_view.user_id')
							->join('person_login','person_login.person_id','=','person.id')
							->select('work_date as check_in_date','check_in_remarks','check_out_remarks','check_out_date','work')
							->where('person_status',1)
							->where('daily_attendance_view.user_id',$user_id)
							->whereRaw("DATE_FORMAT(daily_attendance_view.work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(daily_attendance_view.work_date,'%Y-%m-%d')<='$to_date'")
							->groupBy('check_in_date')
							->get();
		foreach ($attendance_summary as $key => $value) 
		{
			$summary_data['check_in_time'] = !empty($value->check_in_date)?$value->check_in_date:'';
			$summary_data['check_in_remarks'] = !empty($value->check_in_remarks)?$value->check_in_remarks:'';
			$summary_data['check_out_date'] = !empty($value->check_out_date)?$value->check_out_date:'';
			$summary_data['check_out_remarks'] = !empty($value->check_out_remarks)?$value->check_out_remarks:'';
			$summary_data['work'] = !empty($value->work)?$value->work:'';
			$final_summary_data[] = $summary_data;
		}
		// dd($summary_data);

    return response()->json(['response' => TRUE,'date' => $out,'attendance_summary'=>$final_summary_data]);

// 9582277270 - priya
// new marhava cen
	}  
    #attendance data for particular data ends here 

    #top perfomer data starts here
	public function overall_ranking_data(Request $request)
	{
		$validator = Validator::make($request->all(), [
            'module_id' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            // 'user_id' => 'required',
            'company_id' => 'required',
        ]);
		if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $user_id = $request->user_id;
        $module_id = $request->module_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $company_id = $request->company_id;
        $check = DB::table('app_other_module_assign')->where('company_id',$request->company_id)->where('module_id',6)->first();


        // get table name for sales 

        $table_name = TableReturn::table_return($from_date,$to_date);
        // dd($table_name);

        #return junior data starts here
		$role_id_data = DB::table('person')
					->join('users','users.id','=','person.id')
					->join('person_login','person_login.person_id','=','person.id')
					->select('person.role_id as role_id')
					->where('person_status',1)
					->where('person.company_id',$company_id)
					->where('person_login.company_id',$company_id)
					->where('person.id',$user_id)
					->where('is_admin','!=',1)
					->first();
		$role_id = !empty($role_id_data->role_id)?$role_id_data->role_id:'';
		// $is_admin = $role_id_data->role_id;
        if($role_id==1 || $role_id==50 )
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
        #ends here 
        // dd($datasenior);
        if($module_id == 1) // for distributor perfomance
        {

        if(empty($check)){    

        	$distributor_sale_query = DB::table($table_name)
									->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
									->join('dealer','dealer.id','=',$table_name.'.dealer_id')
									->join('location_3','location_3.id','=','dealer.state_id')
									->select('location_3.name as state','dealer.name as distributor_name',DB::raw("sum(rate*quantity) as total_sale_value"),'dealer.id as dealer_id','dealer.landline','dealer.other_numbers')
									->where('dealer_status',1)
                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
									// ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
									->whereIn('user_id',$datasenior)
									->where($table_name.'.company_id',$company_id)
									->where('dealer.company_id',$company_id)
									->where('location_3.company_id',$company_id)
									->groupBy('dealer_id')
									->orderBy('total_sale_value','DESC')
									->get();
		}else{
			$distributor_sale_query = DB::table($table_name)
									->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
									->join('dealer','dealer.id','=',$table_name.'.dealer_id')
									->join('location_3','location_3.id','=','dealer.state_id')
									->select('location_3.name as state','dealer.name as distributor_name',DB::raw("sum(final_secondary_rate*final_secondary_qty) as total_sale_value"),'dealer.id as dealer_id','dealer.landline','dealer.other_numbers')
									->where('dealer_status',1)
                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
									// ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
									->whereIn('user_id',$datasenior)
									->where($table_name.'.company_id',$company_id)
									->where('dealer.company_id',$company_id)
									->where('location_3.company_id',$company_id)
									->groupBy('dealer_id')
									->orderBy('total_sale_value','DESC')
									->get();
			
		}

		$distributor_sale = array();
		$distributor_secondary_sale = array();
		foreach ($distributor_sale_query as $key => $value) {

			$contact = !empty($value->landline)?$value->landline:$value->other_numbers;

			$distributor_sale_data['state'] = $value->state;
			$distributor_sale_data['distributor_name'] = $value->distributor_name."\n".$contact;
			$distributor_sale_data['total_sale_value'] = $value->total_sale_value;
			$distributor_sale_data['dealer_id'] = $value->dealer_id;

			$distributor_secondary_sale[] = $value->total_sale_value;

			$distributor_sale[] = $distributor_sale_data;
		}


		$primaryCoverage = DB::table('user_primary_sales_order')
							->select(DB::raw("sum((cases*pr_rate)+(pcs*rate)) as total_sale_value"),DB::raw("COUNT(DISTINCT dealer_id) as dealerCoverage"),'dealer_id')
							->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
							->whereIn('created_person_id',$datasenior)
							->where('user_primary_sales_order.company_id',$company_id)
							->where('user_primary_sales_order_details.company_id',$company_id)
							->groupBy('dealer_id')
							->get();

		$primary_sale = array();
		$primary_coverage = array();
		foreach ($primaryCoverage as $pkey => $pvalue) {
			$primary_sale[] = $pvalue->total_sale_value;
			$primary_coverage[] = $pvalue->dealer_id;
		}


			// for new purpose
			if(empty($check)){    

	        	$retailer_sale_query = DB::table($table_name)
										->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
										->join('dealer','dealer.id','=',$table_name.'.dealer_id')
										->join('location_3','location_3.id','=','dealer.state_id')
										->join('retailer','retailer.id','=',$table_name.'.retailer_id')
										->join('location_7','location_7.id','=','retailer.location_id')
										->select('location_3.name as state','dealer.name as distributor_name','location_7.name as beat','retailer.name as retailer_name',DB::raw("sum(rate*quantity) as total_sale_value"),'retailer.id as retailer_id',$table_name.'.date',$table_name.'.time')
	                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
										// ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
										->where('retailer_status',1)
										->whereIn('user_id',$datasenior)
										->where($table_name.'.company_id',$company_id)
										->where('dealer.company_id',$company_id)
										->where('retailer.company_id',$company_id)
										->where('location_7.company_id',$company_id)
										->where('location_3.company_id',$company_id)
										->groupBy('retailer_id','date')
										->orderBy($table_name.'.order_id','DESC')
										->get();
			}else{
				$retailer_sale_query = DB::table($table_name)
										->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
										->join('dealer','dealer.id','=',$table_name.'.dealer_id')
										->join('location_3','location_3.id','=','dealer.state_id')
										->join('retailer','retailer.id','=',$table_name.'.retailer_id')
										->join('location_7','location_7.id','=','retailer.location_id')
										->select('location_3.name as state','dealer.name as distributor_name','location_7.name as beat','retailer.name as retailer_name',DB::raw("sum(final_secondary_rate*final_secondary_qty) as total_sale_value"),'retailer.id as retailer_id',$table_name.'.date',$table_name.'.time')
	                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
										// ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
										->where('retailer_status',1)
										->whereIn('user_id',$datasenior)
										->where($table_name.'.company_id',$company_id)
										->where('dealer.company_id',$company_id)
										->where('retailer.company_id',$company_id)
										->where('location_7.company_id',$company_id)
										->where('location_3.company_id',$company_id)
										->groupBy('retailer_id','date')
										// ->groupBy('retailer_id')
										->orderBy($table_name.'.order_id','DESC')
										->get();	
			}
			$out = array();
			$array_sum_var = array();
			$finaly_out = array();
			foreach($retailer_sale_query as $key => $value)
			{
				$out[$value->retailer_id.$value->date] = '1';
				$array_sum_var[] = $value->total_sale_value;

			}
			$last_visited_step_1 = COUNT($retailer_sale_query);
			// $last_visited_step_2 =!empty($retailer_sale_query[$last_visited_step_1-1]->date)?date('d-M-y',strtotime($retailer_sale_query[$last_visited_step_1-1]->date)).' '.$retailer_sale_query[$last_visited_step_1-1]->time:'-';

			$last_visited_step_2 =!empty($retailer_sale_query[$last_visited_step_1-1]->date)?date('Y-m-d',strtotime($retailer_sale_query[$last_visited_step_1-1]->date)):'-';

			$retId =!empty($retailer_sale_query[$last_visited_step_1-1]->retailer_id)?$retailer_sale_query[$last_visited_step_1-1]->retailer_id:'-';


			//
			$date1 = date('Y-m-d');
			$date2 = $last_visited_step_2;

			$diff = abs(strtotime($date2) - strtotime($date1));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

			if($days == '0'){
    		$daysago = 'Visited Today';
			}elseif($days > '0'){
    		$daysago = $days.' Days Ago';
			}else{
    		$daysago = 'Not Visited Yet';
			}
			//


			$total_retailer = DB::table('dealer_location_rate_list')
							->join('retailer','retailer.location_id','=','dealer_location_rate_list.location_id')
							->whereIn('dealer_location_rate_list.user_id',$datasenior)
							->where('dealer_location_rate_list.company_id',$company_id)
							->where('retailer.company_id',$company_id)
							->distinct('retailer.id')->count('retailer.id');

			$total_beat = DB::table('dealer_location_rate_list')
							->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
							->whereIn('dealer_location_rate_list.user_id',$datasenior)
							->where('dealer_location_rate_list.company_id',$company_id)
							->where('location_7.company_id',$company_id)
							->distinct('location_7.id')->count('location_7.id');

			$total_distibutor = DB::table('dealer_location_rate_list')
							->whereIn('dealer_location_rate_list.user_id',$datasenior)
							->where('dealer_location_rate_list.company_id',$company_id)
							->where('dealer_location_rate_list.company_id',$company_id)
							->distinct('dealer_location_rate_list.dealer_id')->count('dealer_location_rate_list.dealer_id');

			$tota_non_productive = DB::table($table_name)
								->where('company_id',$company_id)
								->whereIn('user_id',$datasenior)
								->where('call_status',0)
								->count($table_name.'.order_id');

			$set_sum = array_sum($array_sum_var);
			$finaly_out['t.beat'] = ($total_beat);
			$finaly_out['t.retailer'] = ($total_retailer);

			$finaly_out['total_distributor'] = ($total_distibutor);


			$finaly_out['total_distributor_secondary_coverage'] = COUNT($distributor_sale);
			$finaly_out['total_distributor_secondary_sales'] = ROUND(array_sum($distributor_secondary_sale),2);

			$finaly_out['total_distributor_primary_coverage'] = COUNT($primary_coverage);
			$finaly_out['total_distributor_primary_sales'] = ROUND(array_sum($primary_sale),2);


			// $finaly_out['last_visited'] = $last_visited_step_2;
			// $finaly_out['retId'] = $retId;
			$finaly_out['last_visited'] = $daysago;
			$finaly_out['t.productive'] = COUNT($out);
			$finaly_out['non_productive'] = $tota_non_productive;
			$finaly_out['t.stock'] = 0;
			$finaly_out['t.sale'] = round($set_sum,2);
			return response()->json(['response' => TRUE,'data' => $distributor_sale,'distributor_summary'=>$finaly_out]);

			

        }
        elseif($module_id == 2) // for retailer perfomance
        {
        if(empty($check)){    

        	$retailer_sale_query = DB::table($table_name)
									->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
									->join('dealer','dealer.id','=',$table_name.'.dealer_id')
									->join('location_3','location_3.id','=','dealer.state_id')
									->join('retailer','retailer.id','=',$table_name.'.retailer_id')
									->join('location_7','location_7.id','=','retailer.location_id')
									->select('location_3.name as state','dealer.name as distributor_name','location_7.name as beat','retailer.name as retailer_name',DB::raw("sum(rate*quantity) as total_sale_value"),'retailer.id as retailer_id',$table_name.'.date','retailer.track_address',DB::raw("MAX($table_name.date) as latestSaleDate"),'dealer.landline as dealer_land','dealer.other_numbers as dealer_other','retailer.landline as retailer_land','retailer.other_numbers as retailer_other')
                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
									// ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
									->where('retailer_status',1)
									->whereIn('user_id',$datasenior)
									->where($table_name.'.company_id',$company_id)
									->where('dealer.company_id',$company_id)
									->where('retailer.company_id',$company_id)
									->where('location_7.company_id',$company_id)
									->where('location_3.company_id',$company_id)
									->groupBy('retailer_id')
									->orderBy('total_sale_value','DESC')
									->get();
		}else{
			$retailer_sale_query = DB::table($table_name)
									->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
									->join('dealer','dealer.id','=',$table_name.'.dealer_id')
									->join('location_3','location_3.id','=','dealer.state_id')
									->join('retailer','retailer.id','=',$table_name.'.retailer_id')
									->join('location_7','location_7.id','=','retailer.location_id')
									->select('location_3.name as state','dealer.name as distributor_name','location_7.name as beat','retailer.name as retailer_name',DB::raw("sum(final_secondary_rate*final_secondary_qty) as total_sale_value"),'retailer.id as retailer_id',$table_name.'.date','retailer.track_address',DB::raw("MAX($table_name.date) as latestSaleDate"),'dealer.landline as dealer_land','dealer.other_numbers as dealer_other','retailer.landline as retailer_land','retailer.other_numbers as retailer_other')
                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
									// ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
									->where('retailer_status',1)
									->whereIn('user_id',$datasenior)
									->where($table_name.'.company_id',$company_id)
									->where('dealer.company_id',$company_id)
									->where('retailer.company_id',$company_id)
									->where('location_7.company_id',$company_id)
									->where('location_3.company_id',$company_id)
									->groupBy('retailer_id')
									->orderBy('total_sale_value','DESC')
									->get();	
		}



		$productCount = DB::table($table_name)	
    				->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
    				->where('call_status','=','1')
					->whereIn('user_id',$datasenior)
    				->where($table_name.'.company_id',$company_id)
    				->where('user_sales_order_details.company_id',$company_id)
            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
    				->groupBy('retailer_id','date')
    				->pluck(DB::raw("COUNT(DISTINCT product_id) as uniqueProduct"),DB::raw("CONCAT(retailer_id,date) as concat"));

    	// dd($productCount);


		// $lastSaleData = DB::table('user_sales_order')	
  //   				->select(DB::raw("MAX(date) as lastDate"))
  //   				->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
  //   				->where('call_status','=','1')
		// 			->whereIn('user_id',$datasenior)
  //   				->where('user_sales_order.company_id',$company_id)
  //   				->where('user_sales_order_details.company_id',$company_id)
  //   				->groupBy('retailer_id','date')
  //   				->first();





		$finalrsarray = array();
		foreach ($retailer_sale_query as $rskey => $rsvalue) {

			$dealerCont = !empty($rsvalue->dealer_land)?$rsvalue->dealer_land:$rsvalue->dealer_other;
			$retailerCont = !empty($rsvalue->retailer_land)?$rsvalue->retailer_land:$rsvalue->retailer_other;

			$rsarray['state'] = $rsvalue->state;
			$rsarray['distributor_name'] = $rsvalue->distributor_name."\n".$dealerCont;
			$rsarray['beat'] = $rsvalue->beat;
			$rsarray['retailer_name'] = $rsvalue->retailer_name."\n".$retailerCont;
			$rsarray['total_sale_value'] = $rsvalue->total_sale_value;
			$rsarray['retailer_id'] = $rsvalue->retailer_id;
			$rsarray['date'] = $rsvalue->date;
			$rsarray['trackAddress'] = $rsvalue->track_address;

			$rsarray['uniqueProductOnLastSale'] = !empty($productCount[$rsvalue->retailer_id.$rsvalue->latestSaleDate])?$productCount[$rsvalue->retailer_id.$rsvalue->latestSaleDate]:'0';




			$date1 = date('Y-m-d');
			$date2 = $rsvalue->latestSaleDate;

			$diff = abs(strtotime($date2) - strtotime($date1));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

			if($days == '0'){
    		$rsarray['daysAgo'] = 'Visited Today';
			}else{
    		$rsarray['daysAgo'] = $days.' Days Ago';
			}




			$finalrsarray[] = $rsarray;
		}





		// for new purpose
			if(empty($check)){    

	        	$retailer_sale_query_cus = DB::table($table_name)
										->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
										->join('dealer','dealer.id','=',$table_name.'.dealer_id')
										->join('location_3','location_3.id','=','dealer.state_id')
										->join('retailer','retailer.id','=',$table_name.'.retailer_id')
										->join('location_7','location_7.id','=','retailer.location_id')
										->select('location_3.name as state','dealer.name as distributor_name','location_7.name as beat','retailer.name as retailer_name',DB::raw("sum(rate*quantity) as total_sale_value"),'retailer.id as retailer_id',$table_name.'.date',$table_name.'.time')
	                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
										// ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
										->where('retailer_status',1)
										->whereIn('user_id',$datasenior)
										->where($table_name.'.company_id',$company_id)
										->where('dealer.company_id',$company_id)
										->where('retailer.company_id',$company_id)
										->where('location_7.company_id',$company_id)
										->where('location_3.company_id',$company_id)
										->groupBy('retailer_id','date')
										->orderBy($table_name.'.order_id','DESC')
										->get();
			}else{
				$retailer_sale_query_cus = DB::table($table_name)
										->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
										->join('dealer','dealer.id','=',$table_name.'.dealer_id')
										->join('location_3','location_3.id','=','dealer.state_id')
										->join('retailer','retailer.id','=',$table_name.'.retailer_id')
										->join('location_7','location_7.id','=','retailer.location_id')
										->select('location_3.name as state','dealer.name as distributor_name','location_7.name as beat','retailer.name as retailer_name',DB::raw("sum(final_secondary_rate*final_secondary_qty) as total_sale_value"),'retailer.id as retailer_id',$table_name.'.date',$table_name.'.time')
	                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
										// ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
										->where('retailer_status',1)
										->whereIn('user_id',$datasenior)
										->where($table_name.'.company_id',$company_id)
										->where('dealer.company_id',$company_id)
										->where('retailer.company_id',$company_id)
										->where('location_7.company_id',$company_id)
										->where('location_3.company_id',$company_id)
										->groupBy('retailer_id','date')
										// ->groupBy('retailer_id')
										->orderBy($table_name.'.order_id','DESC')
										->get();	
			}
		$out = array();
		$array_sum_var = array();
		$finaly_out = array();
		foreach($retailer_sale_query_cus as $key => $value)
		{
			$out[$value->retailer_id.$value->date] = '1';
			$array_sum_var[] = $value->total_sale_value;

		}
		$total_retailer = DB::table('dealer_location_rate_list')
						->join('retailer','retailer.location_id','=','dealer_location_rate_list.location_id')
						->whereIn('dealer_location_rate_list.user_id',$datasenior)
						->where('dealer_location_rate_list.company_id',$company_id)
						->where('retailer.company_id',$company_id)
						->distinct('retailer.id')->count('retailer.id');

		$tota_non_productive = DB::table($table_name)
							->where('company_id',$company_id)
							->whereIn('user_id',$datasenior)
							->where('call_status',0)
                            ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
							->count($table_name.'.order_id');
		$total_contacted = COUNT($out)+$tota_non_productive;
		$set_sum = array_sum($array_sum_var);
		$finaly_out['t.universe'] = ($total_retailer);
		$finaly_out['t.contacted'] = COUNT($out)+$tota_non_productive;
		$finaly_out['t.productive'] = COUNT($out);
		$finaly_out['non_productive'] = $tota_non_productive;
		$finaly_out['non_contacted'] = ($total_retailer-$total_contacted);
		$finaly_out['t.sale'] = round($set_sum,2);

									// dd($retailer_sale_query);
			return response()->json(['response' => TRUE,'data' => $finalrsarray,'retailer_summary'=>$finaly_out]);

        }
        elseif($module_id == 3) // for beat perfomance
        {

        if(empty($check)){    

        	$beat_sale_query = DB::table($table_name)
									->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
									->join('dealer','dealer.id','=',$table_name.'.dealer_id')
									->join('location_view','location_view.l7_id','=',$table_name.'.location_id')
									->select('l3_name as state','dealer.name as distributor_name','l7_name as beat',DB::raw("sum(rate*quantity) as total_sale_value"),'l7_id as beat_id')
                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
									// ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
									->where('dealer_status',1)
									->where('dealer.company_id',$company_id)
									->where($table_name.'.company_id',$company_id)
									->whereIn('user_id',$datasenior)
									->groupBy('location_id')
									->orderBy('total_sale_value','DESC')
									->get();
		}else{
			$beat_sale_query = DB::table($table_name)
									->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
									->join('dealer','dealer.id','=',$table_name.'.dealer_id')
									->join('location_view','location_view.l7_id','=',$table_name.'.location_id')
									->select('l3_name as state','dealer.name as distributor_name','l7_name as beat',DB::raw("sum(final_secondary_rate*final_secondary_qty) as total_sale_value"),'l7_id as beat_id')
                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
									// ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
									->where('dealer_status',1)
									->where('dealer.company_id',$company_id)
									->where($table_name.'.company_id',$company_id)
									->whereIn('user_id',$datasenior)
									->groupBy('location_id')
									->orderBy('total_sale_value','DESC')
									->get();
		}

		// for new purpose
		if(empty($check)){    

        	$retailer_sale_query = DB::table($table_name)
									->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
									->join('dealer','dealer.id','=',$table_name.'.dealer_id')
									->join('location_3','location_3.id','=','dealer.state_id')
									->join('retailer','retailer.id','=',$table_name.'.retailer_id')
									->join('location_7','location_7.id','=','retailer.location_id')
									->select('location_3.name as state','dealer.name as distributor_name','location_7.name as beat','retailer.name as retailer_name',DB::raw("sum(rate*quantity) as total_sale_value"),'retailer.id as retailer_id',$table_name.'.date',$table_name.'.time')
                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
									// ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
									->where('retailer_status',1)
									->whereIn('user_id',$datasenior)
									->where($table_name.'.company_id',$company_id)
									->where('dealer.company_id',$company_id)
									->where('retailer.company_id',$company_id)
									->where('location_7.company_id',$company_id)
									->where('location_3.company_id',$company_id)
									->groupBy('retailer_id','date')
									->orderBy($table_name.'.order_id','DESC')
									->get();
		}else{
			$retailer_sale_query = DB::table($table_name)
									->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
									->join('dealer','dealer.id','=',$table_name.'.dealer_id')
									->join('location_3','location_3.id','=','dealer.state_id')
									->join('retailer','retailer.id','=',$table_name.'.retailer_id')
									->join('location_7','location_7.id','=','retailer.location_id')
									->select('location_3.name as state','dealer.name as distributor_name','location_7.name as beat','retailer.name as retailer_name',DB::raw("sum(final_secondary_rate*final_secondary_qty) as total_sale_value"),'retailer.id as retailer_id',$table_name.'.date',$table_name.'.time')
                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
									// ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
									->where('retailer_status',1)
									->whereIn('user_id',$datasenior)
									->where($table_name.'.company_id',$company_id)
									->where('dealer.company_id',$company_id)
									->where('retailer.company_id',$company_id)
									->where('location_7.company_id',$company_id)
									->where('location_3.company_id',$company_id)
									->groupBy('retailer_id','date')
									// ->groupBy('retailer_id')
									->orderBy($table_name.'.order_id','DESC')
									->get();	
		}
		$out = array();
		$visitedRetailer = array();
		$array_sum_var = array();
		$finaly_out = array();
		// dd($retailer_sale_query);
		foreach($retailer_sale_query as $key => $value)
		{
			$date = date('Ymd',strtotime($value->date));
			$out[$value->retailer_id.$date] = '1';
			$array_sum_var[] = $value->total_sale_value;

			$visitedRetailer[$value->retailer_id] = $value->retailer_id;


		}
		$finalVisitedRetailer = array_values($visitedRetailer);

		// dd($finalVisitedRetailer);


		$last_visited_step_1 = COUNT($retailer_sale_query);
		// $last_visited_step_2 =!empty($retailer_sale_query[$last_visited_step_1-1]->date)?date('d-M-y',strtotime($retailer_sale_query[$last_visited_step_1-1]->date)).' '.$retailer_sale_query[$last_visited_step_1-1]->time:'-';
		$last_visited_step_2 =!empty($retailer_sale_query[$last_visited_step_1-1]->date)?date('Y-m-d',strtotime($retailer_sale_query[$last_visited_step_1-1]->date)):'-';


		//
		$date1 = date('Y-m-d');
		$date2 = $last_visited_step_2;

		$diff = abs(strtotime($date2) - strtotime($date1));
		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

		if($days == '0'){
		$daysago = 'Visited Today';
		}elseif($days > '0'){
		$daysago = $days.' Days Ago';
		}else{
		$daysago = 'Not Visited Yet';
		}
		//

		$total_retailer = DB::table('dealer_location_rate_list')
						->select('retailer.id','retailer.name as retailer_name','retailer.address','retailer.class')
						->join('retailer','retailer.location_id','=','dealer_location_rate_list.location_id')
						->whereIn('dealer_location_rate_list.user_id',$datasenior)
						->where('dealer_location_rate_list.company_id',$company_id)
						->where('retailer.company_id',$company_id)
						->groupBy('retailer.id')
						->get()->toArray();

		$totalRetailer = array();
		$totalNotVisitedRetailer = array();
		  foreach($total_retailer as $key => $value)
        {
            $retailer_id = $value->id;
            $totalRetailer[] = $retailer_id;

            if(in_array($retailer_id, $finalVisitedRetailer)){

            }else{
            	$totalNotVisitedRetailer[] = $retailer_id;
            }


        }


		// $total_retailer = DB::table('dealer_location_rate_list')
		// 				->join('retailer','retailer.location_id','=','dealer_location_rate_list.location_id')
		// 				->whereIn('dealer_location_rate_list.user_id',$datasenior)
		// 				->where('dealer_location_rate_list.company_id',$company_id)
		// 				->where('retailer.company_id',$company_id)
		// 				->distinct('retailer.id')->count('retailer.id');

		$tota_non_productive = DB::table($table_name)
							->where('company_id',$company_id)
							->whereIn('user_id',$datasenior)
							->where('call_status',0)
                            ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
							->count($table_name.'.order_id');

		$total_beat = DB::table('dealer_location_rate_list')
							->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
							->whereIn('dealer_location_rate_list.user_id',$datasenior)
							->where('dealer_location_rate_list.company_id',$company_id)
							->where('location_7.company_id',$company_id)
							->distinct('location_7.id')->count('location_7.id');

		$set_sum = array_sum($array_sum_var);
		$finaly_out['t.retailer'] = COUNT($totalRetailer);
		$finaly_out['t.beat'] = ($total_beat);
		$finaly_out['visited'] = COUNT($beat_sale_query);
		$finaly_out['not_visited'] = ($total_beat-COUNT($beat_sale_query));
		// $finaly_out['last_visited'] = $last_visited_step_2;
		$finaly_out['last_visited'] = $daysago;
		$finaly_out['t.productive'] = COUNT($out);
		$finaly_out['non_productive'] = $tota_non_productive;
		$finaly_out['not_contacted'] = COUNT($totalNotVisitedRetailer);
		$finaly_out['t.sale'] = round($set_sum,2);
									// dd($retailer_sale_query);
			return response()->json(['response' => TRUE,'data' => $beat_sale_query,'beat_summary'=>$finaly_out]);

        }
        elseif($module_id == 4) // for user perfomance
        {
        	$out = array();
	        if(empty($check)){    
	        	$user_sale_query = DB::table($table_name)
										->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
										->join('person','person.id','=',$table_name.'.user_id')
										->join('person_login','person_login.person_id','=','person.id')
										->join('location_3','location_3.id','=','person.state_id')						
										->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'location_3.name as state',DB::raw("sum(rate*quantity) as total_sale_value"),'person.id as user_id','person.mobile')
										->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
										->where('person_status',1)
										->where($table_name.'.company_id',$company_id)
										->where('person.company_id',$company_id)
										->where('location_3.company_id',$company_id)
										->whereIn('user_id',$datasenior)
										->groupBy('user_id')
										->orderBy('total_sale_value','DESC')
										->get();
			}else{
				$user_sale_query = DB::table($table_name)
										->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
										->join('person','person.id','=',$table_name.'.user_id')
										->join('person_login','person_login.person_id','=','person.id')
										->join('location_3','location_3.id','=','person.state_id')						
										->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'location_3.name as state',DB::raw("ROUND(sum(final_secondary_rate*final_secondary_qty),2) as total_sale_value"),'person.id as user_id','person.mobile')
										->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
										->where('person_status',1)
										->where($table_name.'.company_id',$company_id)
										->where('person.company_id',$company_id)
										->where('location_3.company_id',$company_id)
										->whereIn('user_id',$datasenior)
										->groupBy('user_id')
										->orderBy('total_sale_value','DESC')
										->get();
			}

			$total_call = DB::table($table_name)		
						->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
						->where($table_name.'.company_id',$company_id)
						->whereIn('user_id',$datasenior)
						->groupBy('user_id')
						->pluck(DB::raw("COUNT(DISTINCT retailer_id) as count"),'user_id');

			$productive_call = DB::table($table_name)		
						->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
						->where($table_name.'.company_id',$company_id)
						->where($table_name.'.call_status',1)
						->whereIn('user_id',$datasenior)
						->groupBy('user_id')
						->pluck(DB::raw("COUNT(DISTINCT retailer_id) as count"),'user_id');

			$beat_cover = DB::table($table_name)
						->select(DB::raw('COUNT(DISTINCT location_id) as beat_cover'))		
						->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
						->where($table_name.'.company_id',$company_id)
						->whereIn('user_id',$datasenior)
						->first();

			$analyticsCount = DB::table('dealer_location_rate_list')
							->join('retailer','retailer.location_id','=','dealer_location_rate_list.location_id')
							->select(DB::raw('COUNT(DISTINCT dealer_location_rate_list.dealer_id) as dealerCount'),DB::raw('COUNT(DISTINCT dealer_location_rate_list.location_id) as beatCount'),DB::raw('COUNT(DISTINCT retailer.id) as retailerCount'),'dealer_location_rate_list.user_id')
							->where('dealer_location_rate_list.company_id',$company_id)
							->where('retailer.company_id',$company_id)
							->whereIn('dealer_location_rate_list.user_id',$datasenior)
							->groupBy('dealer_location_rate_list.user_id')
							->get()->toArray();

			$finalArray = array();
			foreach ($analyticsCount as $ackey => $acvalue) {
				$finalArray[$acvalue->user_id]['dealerCount'] =  $acvalue->dealerCount;
				$finalArray[$acvalue->user_id]['beatCount'] =  $acvalue->beatCount;
				$finalArray[$acvalue->user_id]['retailerCount'] =  $acvalue->retailerCount;
			}


			$plannedCalls = DB::table('monthly_tour_program')
						->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(working_date,'%Y-%m-%d')<='$to_date'")
						->where('monthly_tour_program.company_id',$company_id)
						->whereIn('person_id',$datasenior)
						->groupBy('person_id')
						->pluck(DB::raw("SUM(total_calls) as planCall"),'person_id');

			// dd($finalArray);
			$final_sale = array();
			$final_tc = array();
			$final_pc = array();
			foreach ($user_sale_query as $key => $value) 
			{

				$out[$key]['user_name'] =  $value->user_name."\n".$value->mobile;
				$out[$key]['full_name'] =  $value->user_name;
				$out[$key]['user_id'] =  $value->user_id;
				$out[$key]['state'] =  $value->state;
				$out[$key]['total_sale_value'] =  $value->total_sale_value;
				$out[$key]['productivity'] =  '0%';
				$out[$key]['effective_coverage'] =  '0%';
				$out[$key]['lpsc'] =  '0%';
				$out[$key]['TC'] =  !empty($total_call[$value->user_id])?$total_call[$value->user_id]:0;
				$out[$key]['PC'] =  !empty($productive_call[$value->user_id])?$productive_call[$value->user_id]:0;

				$out[$key]['dealerCount'] =  !empty($finalArray[$value->user_id]['dealerCount'])?$finalArray[$value->user_id]['dealerCount']:0;
				$out[$key]['beatCount'] =  !empty($finalArray[$value->user_id]['beatCount'])?$finalArray[$value->user_id]['beatCount']:0;
				$out[$key]['retailerCount'] =  !empty($finalArray[$value->user_id]['retailerCount'])?$finalArray[$value->user_id]['retailerCount']:0;

				$out[$key]['plannedCalls'] =  !empty($plannedCalls[$value->user_id])?$plannedCalls[$value->user_id]:0;


				$final_sale[] = $value->total_sale_value;
				$final_tc[] = !empty($total_call[$value->user_id])?$total_call[$value->user_id]:0;
				$final_pc[] = !empty($productive_call[$value->user_id])?$productive_call[$value->user_id]:0;

				

			}


		$finaly_out['beat_coverage'] = !empty($beat_cover->beat_cover)?$beat_cover->beat_cover:'0';
		$finaly_out['overall_sale'] = array_sum($final_sale);
		$finaly_out['productive_call'] = array_sum($final_pc);
		$finaly_out['total_call'] = array_sum($final_tc);
	

			return response()->json(['response' => TRUE,'data' => $out,'user_summary'=>$finaly_out]);

        }
        elseif($module_id == 5) // for product perfomane 
        {

        	$product_sale_query_data = DB::table($table_name)
        							->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
        							->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
        							->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
        							->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
        							->join('location_7','location_7.id','=',$table_name.'.location_id')
        							->join('location_6','location_6.id','=','location_7.location_6_id')
        							->join('location_5','location_5.id','=','location_6.location_5_id')
        							->join('location_4','location_4.id','=','location_5.location_4_id')
        							->join('location_3','location_3.id','=','location_4.location_3_id')
        							->select(DB::raw("SUM(user_sales_order_details.quantity) as sku_quantity"),'location_3.name as state',DB::raw("SUM(user_sales_order_details.rate*user_sales_order_details.quantity) as total_sale_value"),'catalog_2.name as sub_product','catalog_2.id as sub_product_id','catalog_1.name as product','catalog_1.id as product_id',DB::raw("round(SUM(catalog_product.weight/1000),4) as weight"),DB::raw("IF(color_code IS NULL,'#42d4f5',color_code) as color_code"))
									->where('user_sales_order_details.product_id','!=',0)
									->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
									->where('user_sales_order_details.company_id',$company_id)
									->where('location_7.company_id',$company_id)
									->where('catalog_product.company_id',$company_id);
									if(!empty($request->user_id)){
										$product_sale_query_data->whereIn($table_name.'.user_id',$datasenior);
									}
									if(!empty($request->beat_id)){
										$product_sale_query_data->where($table_name.'.location_id',$request->beat_id);
									}
									if(!empty($request->retailer_id)){
										$product_sale_query_data->where($table_name.'.retailer_id',$request->retailer_id);
									}
									if(!empty($request->dealer_id)){
										$product_sale_query_data->where($table_name.'.dealer_id',$request->dealer_id);
									}

        	$product_sale_query = $product_sale_query_data->groupBy('catalog_2.id')->get();



        // 	$product_sale_query = DB::table('secondary_sale')
        // 						->join('location_3','location_3.id','=','secondary_sale.l3_id')
								// ->select(DB::raw("SUM(quantity) as sku_quantity"),'location_3.name as state',DB::raw("SUM(rate*quantity) as total_sale_value"),'c2_name as sub_product','c2_id as sub_product_id','c1_name as product','c1_id as product_id',DB::raw("round(SUM(weight/1000),4) as weight"),DB::raw("IF(color_code IS NULL,'#42d4f5',color_code) as color_code"))
								// ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
								// ->where('location_3.company_id',$company_id)
								// ->where('secondary_sale.company_id',$company_id)
								// ->whereIn('user_id',$datasenior)
								// ->groupBy('sub_product')
								// ->where('product_id','!=',0)
								// ->get();

        	$sub_product_query_data = DB::table($table_name)
        							->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
        							->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
        							->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
        							->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
        							->join('location_7','location_7.id','=',$table_name.'.location_id')
        							->join('location_6','location_6.id','=','location_7.location_6_id')
        							->join('location_5','location_5.id','=','location_6.location_5_id')
        							->join('location_4','location_4.id','=','location_5.location_4_id')
        							->join('location_3','location_3.id','=','location_4.location_3_id')
        							->select(DB::raw("SUM(user_sales_order_details.quantity) as sku_quantity"),'location_3.name as state',DB::raw("SUM(user_sales_order_details.rate*user_sales_order_details.quantity) as total_sale_value"),'catalog_1.id as product_id','catalog_2.id as sub_product_id','user_sales_order_details.product_id as sub_sub_product_id','catalog_product.name as sub_sub_product_name',DB::raw("round(sum(catalog_product.weight/1000),4) as weight"))
									->where('user_sales_order_details.product_id','!=',0)
									->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
									->where('user_sales_order_details.company_id',$company_id)
									->where('location_7.company_id',$company_id)
									->where('catalog_product.company_id',$company_id);
									if(!empty($request->user_id)){
										$sub_product_query_data->whereIn($table_name.'.user_id',$datasenior);
									}
									if(!empty($request->beat_id)){
										$sub_product_query_data->where($table_name.'.location_id',$request->beat_id);
									}
									if(!empty($request->retailer_id)){
										$sub_product_query_data->where($table_name.'.retailer_id',$request->retailer_id);
									}
									if(!empty($request->dealer_id)){
										$sub_product_query_data->where($table_name.'.dealer_id',$request->dealer_id);
									}

        	$sub_product_query = $sub_product_query_data->groupBy('user_sales_order_details.product_id')->get();
								
			// $sub_product_query = DB::table('secondary_sale')
   //      						->join('location_3','location_3.id','=','secondary_sale.l3_id')
   //      						->select(DB::raw("SUM(quantity) as sku_quantity"),'location_3.name as state',DB::raw("SUM(rate*quantity) as total_sale_value"),'c1_id as product_id','c2_id as sub_product_id','product_id as sub_sub_product_id','secondary_sale.name as sub_sub_product_name',DB::raw("round(sum(weight/1000),4) as weight"))
			// 					->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
			// 					->where('secondary_sale.company_id',$company_id)
			// 					->where('location_3.company_id',$company_id)
			// 					->whereIn('user_id',$datasenior)
			// 					->where('secondary_sale.product_id','!=',0)
			// 					->groupBy('secondary_sale.product_id')
			// 					->get();

			return response()->json(['response' => TRUE,'data' => $product_sale_query ,'sub_product_query'=> $sub_product_query]);

        }
        elseif($module_id == 6) // for attendance data 
        {
        	$attendance_data = DB::table('user_daily_attendance')
							->join('person','person.id','=','user_daily_attendance.user_id')
							->join('person_login','person_login.person_id','=','person.id')
							->where('person_status',1)
							->whereIn('user_id',$datasenior)
							->where('location_3.company_id',$company_id)
							->where('user_daily_attendance.company_id',$company_id)
							->where('person.company_id',$company_id)
							->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d')<='$to_date'")
							->groupBy('work_status')
							->pluck(DB::raw("COUNT(work_status)"),'work_status');
			// dd($attendance_data);
			$work_status_data = DB::table("_working_status")->where('status',1)->where('company_id',$company_id)->get();
			foreach ($work_status_data as $key => $value) 
			{
				$work_name = $value->name;
				$work_id = $value->id;
				$final_array['name'] = $work_name; 
				$final_array['count'] = !empty($attendance_data[$work_id])?$attendance_data[$work_id]:'0'; 
				$out[] = $final_array;
			}
			// dd($datasenior);
			$attendance_query = DB::table('user_daily_attendance')
        						->join('person','person.id','=','user_daily_attendance.user_id')
        						->join('location_3','location_3.id','=','person.state_id')
        						->join('person_login','person_login.person_id','=','person.id')
        						->leftJoin('_working_status','_working_status.id','=','user_daily_attendance.work_status')
        						->select('work_status','user_id',DB::raw("COUNT(work_status) as count_status"),DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"))
								->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d')<='$to_date'")
        						->where('person_status',1)
								->where('person.company_id',$company_id)
								->where('location_3.company_id',$company_id)
								->where('_working_status.company_id',$company_id)
								->where('user_daily_attendance.company_id',$company_id)
        						->whereIn('user_id',$datasenior)
        						->groupBy('user_id')
        						->get();

			$detailArr= array();
			$temp_id= array();

			foreach ($attendance_query as $Akey => $Avalue) 
			{
				$attendance_data_test = DB::table('user_daily_attendance')
							->join('person','person.id','=','user_daily_attendance.user_id')
							->join('person_login','person_login.person_id','=','person.id')
    						->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
							->select(DB::raw("COUNT(work_status) as count"),'_working_status.name as work_status')
							->where('person_status',1)
							->where('user_daily_attendance.company_id',$company_id)
							->where('_working_status.company_id',$company_id)
    						->where('user_id',$Avalue->user_id)
							->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d')<='$to_date'")
							->groupBy('work_status')
							->get();
				
				// $temp_id[] = $Avalue->work_status;

							$detailArr[] = array(
								'user_id'=>$Avalue->user_id,
								'user_name'=>$Avalue->user_name,
								'count'=>$Avalue->count_status,
								
								'details'=>$attendance_data_test,
								);

			

			}
			// dd($temp_id);
			// dd($detailArr);
			// dd($summary_data);
			$summray_attendance_query = DB::table('user_daily_attendance')
        						->join('person','person.id','=','user_daily_attendance.user_id')
        						->join('location_3','location_3.id','=','person.state_id')
        						->join('person_login','person_login.person_id','=','person.id')
        						->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
        						->select('work_date as check_in',DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d') as work_date"),'user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),"_working_status.name as daily_status",'location_3.name as state')
								->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d')<='$to_date'")
        						->where('person_status',1)
        						->whereIn('user_id',$datasenior)
        						->groupBy('user_id','work_date')
        						->get();


			return response()->json(['response' => TRUE,'data' => $detailArr,'berif_summray_date'=>$summray_attendance_query,'overall_attendance_data'=>$out]);

        }

        elseif($module_id == 7) // for user perfomance
        {
        	$out = array();
        	$user_sale_query = DB::table('meeting_order_booking')
							->join('person','person.id','=','meeting_order_booking.user_id')
							->join('location_3','location_3.id','=','person.state_id')
							->join('person_login','person_login.person_id','=','person.id')
							->select('location_3.name as state',DB::raw("COUNT(DISTINCT meeting_order_booking.order_id) as count_meeting"),DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"))
							->whereRaw("DATE_FORMAT(current_date_m,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(current_date_m,'%Y-%m-%d')<='$to_date'")
							->where('meeting_order_booking.company_id',$company_id)
							->where('person_status',1)
							->where('person.company_id',$company_id)
							->groupBy('location_3.id','user_id')
							->get();
			// dd($user_sale_query);
			foreach ($user_sale_query as $key => $value) 
			{

				$out[$key]['user_name'] =  $value->user_name;
				$out[$key]['state'] =  $value->state;
				$out[$key]['count_meeting'] =  $value->count_meeting;
			}

			return response()->json(['response' => TRUE,'data' => $out]);

        }

	}
	
    #top perfomer data ends here 

}

