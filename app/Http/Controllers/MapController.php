<?php
namespace App\Http\Controllers;


use DB;
use Image;
use App\Person;
use App\_role;
use App\Location3;
use App\Circular;
use App\SendSms;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DateTime;

Class MapController extends Controller
{
	public function __construct()
	{
        $this->current_menu='MapView';

	}
	public function userLiveLocation(Request $request)
	{
		$state = $request->state;
		$user_id = $request->user_id;
		$company_id = Auth::user()->company_id;

		$currDate = date('Y-m-d');

		// dd($currDate);

		$currTime = date('H:i:s');



		$tracking_query_data = DB::table('user_work_tracking')
						->select(DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as user_name"),'person.id as user_id','user_work_tracking.lat_lng','user_work_tracking.status','user_work_tracking.track_time','user_work_tracking.track_date')
						->join('person','person.id','=','user_work_tracking.user_id')
						->where('user_work_tracking.company_id',$company_id)
						->where('user_work_tracking.track_date',$currDate)
						->groupBy('user_id')
						->orderBy('track_time','ASC');
						if(!empty($state)){
							$tracking_query_data->where('person.state_id',$state);
						}
						if(!empty($user_id)){
							$tracking_query_data->where('person.id',$user_id);
						}
		$tracking_query = $tracking_query_data->get();



//dd($tracking_query);



		$out = array();
		$final_out = array();
		foreach ($tracking_query as $key => $value) 
		{
			$cordinatesDetails = DB::table('user_work_tracking')
						->where('user_id',$value->user_id)
						->whereRaw("date_format(user_work_tracking.track_date,'%Y-%m-%d')='$currDate'")
						->where('lat_lng','!=','NULL')
						->where('lat_lng','!=','')
						->where('lat_lng','!=','0,0')
						->where('lat_lng','!=','0.0,0.0')
						->orderBy('track_time','DESC')
						->first();

			
			if(!empty($cordinatesDetails->lat_lng)){
			$explode = preg_split('/[\ \n\,]+/',$cordinatesDetails->lat_lng);

			$lat = $explode[0];
			$lng = $explode[1];

			$out['user_id'] = $cordinatesDetails->user_id;
			$out['lat'] = $lat;
			$out['lng'] = $lng;


			$start = strtotime($cordinatesDetails->track_time); 

			$end = strtotime($currTime); 

			$totaltime = ($end - $start)  ; 

			$hours = intval($totaltime / 3600);   
			$seconds_remain = ($totaltime - ($hours * 3600)); 

			$minutes = intval($seconds_remain / 60);   
			$seconds = ($seconds_remain - ($minutes * 60)); 

			$diff =  "$hours:$minutes:$seconds"; 

			$diffFormat = date('H:i:s',strtotime($diff));
			$break_time = explode(':',$diffFormat);
			$HOUR = $break_time[0];
			$MINUTE = $break_time[1];
			$SECOND = $break_time[2];



			if($HOUR == '00' && $MINUTE <= '30'){
				$status = '1'; // green
			}elseif($HOUR == '00' && $MINUTE >= '30'){
				$status = '2'; // yellow
			}else{
				$status = '3'; // red
			}



			$final_out[] = $lat.",".$lng.",".$value->user_name.",".$status.",".$cordinatesDetails->track_time.",".$value->user_id.",".$value->track_date;

			}

		}

// dd($final_out);


        $way= !empty($final_out)?json_encode($final_out):'';

        $usersData = DB::table('person')
        		->join('users','users.id','=','person.id')
        		->join('person_login','person_login.person_id','=','person.id')
        		->join('location_5','location_5.id','=','person.head_quater_id')
        		->join('_role','_role.role_id','=','person.role_id')
        		->select(DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as user_name"),'person.id','location_5.name as l5_name','_role.rolename')
        		->where('person_status','=','1')
        		->where('users.is_admin','!=','1')
        		->where('person.company_id',$company_id)
        		->groupBy('person.id')
        		->get()->toArray();
        		// ->pluck(DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as user_name"),'person.id');

       	foreach ($usersData as $ukey => $uvalue) {
       		$users[$uvalue->id] = $uvalue->user_name.'-'.$uvalue->rolename.'-'.$uvalue->l5_name;
       	}

       	$state = DB::table('location_3')
       			->where('company_id',$company_id)
       			->groupBy('id')
       			->pluck('name','id');



		return view('MapView.index',[
		
			'user_data'=>$final_out,
        	'records' => $way,
        	'users' => $users,
        	'state' => $state,
        	'tracking_query' => $tracking_query,
			'current_menu'=>$this->current_menu,
			]);
	} 


	public function userMapTracking(Request $request)
	{
        	$arr=[];
        	$user_work_tracking_neha=[];
        	$user_id = $request->user_id;
        	$track_date = $request->track_date;
		$company_id = Auth::user()->company_id;

		$totalCall = DB::table('user_sales_order')
			     ->select(DB::raw("COUNT(DISTINCT retailer_id) as totalCall"))
			     ->where('company_id',$company_id)
			     ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') ='$track_date'")
			     ->where('user_id',$user_id)
			     ->first();

		$productiveCall = DB::table('user_sales_order')
			     ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
			     ->select(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as productiveCall"),DB::raw("SUM(user_sales_order_details.rate*user_sales_order_details.quantity) as finalSale"))
			     ->where('user_sales_order.company_id',$company_id)
			     ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') ='$track_date'")
			     ->where('user_sales_order.user_id',$user_id)
			     ->first();




		$userDetails = DB::table('person')
				->join('_role','_role.role_id','=','person.role_id')
				->select(DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as user_name"),'person.id as user_id','rolename')
				->where('person.company_id',$company_id)
				->where('id',$user_id)
				->first();


        	$user_work_tracking_query=DB::table('user_work_tracking')
		             ->whereRaw("DATE_FORMAT(user_work_tracking.track_date,'%Y-%m-%d') ='$track_date'")
		             ->whereRaw("lat_lng!='0.0 0.0' AND lat_lng!='0.0,0.0' AND lat_lng!=' ' AND lat_lng!='' AND lat_lng!='0,0' AND lat_lng!=' ' AND lat_lng!='NULL'" )
		             ->where('user_id',$user_id)
		             ->where('company_id',$company_id);
		             // if($company_id == '44'){
		             // 	$user_work_tracking_query->whereRaw("DATE_FORMAT(user_work_tracking.track_time,'%H:%i:%s') >='10:00:00' AND DATE_FORMAT(user_work_tracking.track_time,'%H:%i:%s') <='19:00:00'");
		             // }else{
		             // 	$user_work_tracking_query->whereRaw("DATE_FORMAT(user_work_tracking.track_time,'%H:%i:%s') >='09:00:00' AND DATE_FORMAT(user_work_tracking.track_time,'%H:%i:%s') <='21:00:00'");
		             // }
		             // ->where('status','!=','Daily Reporting')
		             // ->where('status','!=','Daily Reporting')
		             // ->where('status','!=','Primary Booking')
		             // ->where('status','!=','Counter Sale')
		             // ->where('status','!=','Dealer Balance Stock')
		             // ->where('status','!=','Retailer Creation')
		             // ->where('status','!=','Tracking')
		$user_work_tracking = $user_work_tracking_query->orderBy('track_time','ASC')->get()->toArray();
		             // dd($user_work_tracking);
		            if (!empty($user_work_tracking))
		            {
			                foreach ($user_work_tracking as $dataRow)
			                {
			                    $latlng=str_replace(' ',',',$dataRow->lat_lng);

			                    $trackdatetime = $dataRow->track_date.' '.$dataRow->track_time;

			                    $arr[$dataRow->track_date." ".$dataRow->track_time]=$latlng.",".$trackdatetime.",".$dataRow->status.",".$dataRow->battery_status.",".$dataRow->track_time.",".$dataRow->gps_status;

			                }

		        		// foreach ($user_work_tracking as $dataKey => $dataRow)
			         //        {
			         //            $latlng=str_replace(' ',',',$dataRow->lat_lng);
			         //            $trackdatetime = $dataRow->track_date.' '.$dataRow->track_time;
		          //                   $unix_timew = strtotime($dataRow->track_time); 
		          //                   $startTimew = $unix_timew;

		          //                   	if($dataKey == 0){
			         //                $user_work_tracking_neha[] = $dataRow;

            // 					if($company_id != '44'){
			         //                $plusthirtytimew = strtotime(date('H:i:s',strtotime('+5 minutes',$unix_timew)));
			         //        	}else{
			         //                $plusthirtytimew = strtotime(date('H:i:s',strtotime('+15 minutes',$unix_timew)));
			         //        	}

			         //                }

			         //                if($plusthirtytimew <= $startTimew){
			         //                $user_work_tracking_neha[] = $dataRow;

            // 					if($company_id != '44'){
			         //                 $plusthirtytimew = strtotime(date('H:i:s',strtotime('+5 minutes',$unix_timew)));
			         //         	}else{
			         //                 $plusthirtytimew = strtotime(date('H:i:s',strtotime('+15 minutes',$unix_timew)));
			         //         	}


			         //                }

			         //        }

		            }
            	ksort($arr);

            	// dd($user_work_tracking_neha);
            	$temp = [];

            	if($company_id != '44'){


   //          		$finalarr = array_values($arr);

			// foreach ($finalarr as $key => $value){
   //                      	$finalTime =  explode(',',$value);

   //                      	 $unix_time = strtotime($finalTime['5']); 
   //                      	$startTime = $unix_time;

   //                      	if($key == 0){
	  //                       $temp[] = $value;
	  //                       $plusthirtytime = strtotime(date('H:i:s',strtotime('+5 minutes',$unix_time)));
	  //                       }

	  //                       if($plusthirtytime <= $startTime){
	  //                        $temp[] = $value;
	  //                        $plusthirtytime = strtotime(date('H:i:s',strtotime('+5 minutes',$unix_time)));
	  //                       }

		 //            }
		 //            // dd($temp);
		 //            $way= !empty($temp)?json_encode($temp):'';
		 //            $user_work_tracking = $user_work_tracking_neha;


            		// commented by BP Sir
	            	foreach ($arr as $key => $value) 
		            {
		                $temp[] = $value; 
		            }
		            // dd($temp);
		            $way= !empty($temp)?json_encode($temp):'';





		}else{ // neha condition for 15 minutes
            		$finalarr = array_values($arr);

			foreach ($finalarr as $key => $value){
                        	$finalTime =  explode(',',$value);

                        	 $unix_time = strtotime($finalTime['5']); 
                        	$startTime = $unix_time;

                        	if($key == 0){
	                        $temp[] = $value;
	                        $plusthirtytime = strtotime(date('H:i:s',strtotime('+15 minutes',$unix_time)));
	                        }

	                        if($plusthirtytime <= $startTime){
	                         $temp[] = $value;
	                         $plusthirtytime = strtotime(date('H:i:s',strtotime('+15 minutes',$unix_time)));
	                        }

		            }
		            // dd($temp);
		            $way= !empty($temp)?json_encode($temp):'';
		            $user_work_tracking = $user_work_tracking_neha;
		}


	           

	            return view('MapView.userMapTracking',[
	                'way' => $way,
	                'temp' => $temp,
	                'start' => '',
                	'last' => '',
	                'id' => $user_id,
	                'userDetails' => $userDetails,
                	'user_work_tracking'=> $user_work_tracking,
                	'track_date'=> $track_date,
                	'totalCall'=> $totalCall,
                	'productiveCall'=> $productiveCall,


	            ]);


	}
	
}