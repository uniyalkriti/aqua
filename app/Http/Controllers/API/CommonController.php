<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Person;
use App\UserDetail;
use App\Company;
use App\JuniorData;
use App\Circular;
use App\TableReturn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use App\UserIncentiveDetails;
use App\UserIncentiveSlabs;
use App\UserIncentiveRoleDistribution;
use Validator;
use DB;
use Image;

class CommonController extends Controller
{
    public $successStatus = 401;
    public $response_true = True;
    public $response_false = False;

    
    public function mtp_data(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'month'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $dealer_name = DB::table('dealer')->where('company_id',$request->company_id)->pluck('name','id')->toArray();
        $location_6 = DB::table('location_6')->where('company_id',$request->company_id)->pluck('name','id')->toArray();
        $location_7 = DB::table('location_7')->where('company_id',$request->company_id)->pluck('name','id')->toArray();
        $task_of_the_day = DB::table('_task_of_the_day')->where('company_id',$request->company_id)->pluck('task','id')->toArray();
        
    	$mtp_data_query = DB::table('monthly_tour_program')
						->select('monthly_tour_program.*','monthly_tour_program.dealer_id as dealer_id', 'monthly_tour_program.locations as beat', 'monthly_tour_program.town as town','monthly_tour_program.working_status_id as working_status')
						// ->leftJoin('dealer','dealer.id','=','monthly_tour_program.dealer_id')
						// ->leftJoin('location_6','location_6.id','=','monthly_tour_program.town')
						// ->leftJoin('location_7','location_7.id','=','monthly_tour_program.locations')
						// ->leftJoin('_task_of_the_day','_task_of_the_day.id','=','monthly_tour_program.working_status_id')
						->where('person_id',$request->user_id)
						->where('monthly_tour_program.company_id',$request->company_id)
						->whereRaw("DATE_FORMAT(working_date,'%Y-%m')='$request->month'")
						->orderBy('working_date','ASC')
						->get();

		$final_array = array();
		foreach ($mtp_data_query as $key => $value) {
			$dealer_id = $value->dealer_id;
			$beat_explode = explode(',',$value->beat);
			$town_explode = explode(',',$value->town);

		// $beat_list = DB::table('location_7')->select(DB::raw("group_concat(DISTINCT name) as beat"))->where('company_id',$value->company_id)->whereIn('id',$beat_explode)->first();
		// $town_list =  DB::table('location_6')->select(DB::raw("group_concat(DISTINCT name) as town"))->where('company_id',$value->company_id)->whereIn('id',$town_explode)->first();

			$out['id'] = $value->id;
			$out['company_id'] = $value->company_id;
			$out['person_id'] = $value->person_id;
			$out['working_date'] = $value->working_date;
			$out['dayname'] = $value->dayname;
			$out['working_status_id'] = $value->working_status_id;
			$out['dealer_id'] = $value->dealer_id;

			$town_array = array();
			foreach ($town_explode as $vkey => $vvalue) {
				$town_array[] = !empty($location_6[$vvalue])?$location_6[$vvalue]:'';
			}

			$out['town'] = implode(',',$town_array);
			// $out['town'] = $town_list->town;
			$out['locations'] = $value->locations;
			$out['total_calls'] = $value->total_calls;
			$out['total_sales'] = $value->total_sales;
			$out['ss_id'] = $value->ss_id;
			$out['travel_mode'] = $value->travel_mode;
			$out['from'] = $value->from;
			$out['to'] = $value->to;
			$out['travel_distance'] = $value->travel_distance;
			$out['category_wise'] = $value->category_wise;
			$out['task_of_the_day'] = $value->task_of_the_day;
			$out['mobile_save_date_time'] = $value->mobile_save_date_time;
			$out['upload_date_time'] = $value->upload_date_time;
			$out['admin_approved'] = $value->admin_approved;
			$out['admin_remark'] = $value->admin_remark;
			$out['pc'] = $value->pc;
			$out['rd'] = $value->rd;
			$out['arch'] = $value->arch;
			$out['collection'] = $value->collection;
			$out['primary_ord'] = $value->primary_ord;
			$out['new_outlet'] = $value->new_outlet;
			$out['any_other_task'] = $value->any_other_task;
			$out['approved_by'] = $value->approved_by;
			$out['approved_on'] = $value->approved_on;
			$out['submit_from'] = $value->submit_from;
			$out['submit_by'] = $value->submit_by;

			
			$out['dealer_name'] = !empty($dealer_name[$dealer_id])?$dealer_name[$dealer_id]:'';

			$beat_array = array();
			foreach ($beat_explode as $bkey => $bvalue) {
				$beat_array[] = !empty($location_7[$bvalue])?$location_7[$bvalue]:'';
			}
			$out['beat'] = implode(',',$beat_array);
			// $out['beat'] = $beat_list->beat;

			$out['working_status'] = !empty($task_of_the_day[$value->working_status])?$task_of_the_day[$value->working_status]:'';

			$final_array[] = $out;
			
		}








			if(!empty($final_array))
			{
				return response()->json([ 'response' =>True,'message'=>'MTP Data','data'=>$final_array]);
			}
			else
			{
				return response()->json([ 'response' =>False,'message'=>'MTP Data','data'=>$final_array]);
			}

    }
    public function xotik_mtp_data(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'month'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        
    	$mtp_data_query = DB::table('monthly_tour_program_xotik')
						->select('monthly_tour_program_xotik.*','dealer.name as dealer_name', 'location_7.name as beat', 'location_6.name as town','_task_of_the_day.task as working_status')
						->leftJoin('dealer','dealer.id','=','monthly_tour_program_xotik.dealer_id')
						->leftJoin('location_6','location_6.id','=','monthly_tour_program_xotik.town')
						->leftJoin('location_7','location_7.id','=','monthly_tour_program_xotik.locations')
						->leftJoin('_task_of_the_day','_task_of_the_day.id','=','monthly_tour_program_xotik.working_status_id')
						->where('person_id',$request->user_id)
						->where('monthly_tour_program_xotik.company_id',$request->company_id)
						->whereRaw("DATE_FORMAT(working_date,'%Y-%m')='$request->month'")
						->orderBy('working_date','ASC')
						->get();
						if(!empty($mtp_data_query))
						{
							return response()->json([ 'response' =>True,'message'=>'MTP Data','data'=>$mtp_data_query]);
						}
						else
						{
							return response()->json([ 'response' =>False,'message'=>'MTP Data','data'=>$mtp_data_query]);
						}

    }
    #........................return attendnce report ....starts here ..................###
    public function user_daily_attendance_report(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		
		
		$status_check = !empty($request->report_status)?$request->report_status:'1';

		// dd($junior_data_check);
		if($status_check == '2'){
			$junior_data_check = array($request->user_id);
		}
		else
		{
			Session::forget('juniordata');		
	        $check_junior_data=JuniorData::getJuniorUser($request->user_id,$request->company_id);
	        Session::push('juniordata', $request->user_id);

			$junior_data_check = Session::get('juniordata');
		}

		$array = array(100,101,102);
        
        $attendance_query_data = DB::table('person')
        				->join('person_login','person_login.person_id','=','person.id')
    					->join('user_daily_attendance','user_daily_attendance.user_id','=','person.id')
    					->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
    					->select('person.mobile as per_mobile','person.id as id','person.head_quar as hq',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') as work_date"),'_working_status.name as working_status',DB::raw("DATE_FORMAT(user_daily_attendance.work_date,'%H:%i:%s') as checkin_time"),'user_daily_attendance.remarks as remarks','user_daily_attendance.image_name','user_daily_attendance.track_addrs','user_daily_attendance.lat_lng')
    					->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$request->from_date' AND DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$request->to_date'")
						->whereIn('person.id',$junior_data_check);
						if($request->user_id == 2833){
						   $attendance_query_data->whereNotIn('person.state_id',$array);		
						}
        $attendance_query = $attendance_query_data->where('person.company_id',$request->company_id)
    					->where('user_daily_attendance.company_id',$request->company_id)
    					->where('person_status',1)
    					->get();
		$final_data = array();
		foreach ($attendance_query as $key => $value) 
		{
			$check_out_query = DB::table('check_out')
						->select(DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as check_out_time"),'remarks','attn_address','lat_lng')
						->where('company_id',$request->company_id)
						->whereRaw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d')='$value->work_date'")
						->where('user_id',$value->id)
						->first();

			$checkOutTime = !empty($check_out_query->check_out_time)?$check_out_query->check_out_time:'';
			$checkOutRemarks = !empty($check_out_query->remarks)?$check_out_query->remarks:'';
			$chk_address = !empty($check_out_query->attn_address)?$check_out_query->attn_address:'';
			$chk_lat_lng = !empty($check_out_query->lat_lng)?explode(',',$check_out_query->lat_lng):'';
			$chk_lat = !empty($chk_lat_lng[0])?$chk_lat_lng[0]:'';
			$chk_lng = !empty($chk_lat_lng[1])?$chk_lat_lng[1]:'';

			$att_lat_lng = !empty($value->lat_lng)?explode(',',$value->lat_lng):'';
			$att_lat = !empty($att_lat_lng[0])?$att_lat_lng[0]:'';
			$att_lng = !empty($att_lat_lng[1])?$att_lat_lng[1]:'';

			if($request->company_id == 52){
			$data['checkout_time'] = $checkOutTime."\n".$checkOutRemarks;
			}else{
			$data['checkout_time'] = $checkOutTime;
			}
			$data['id'] = "$value->id";
			$data['hq'] = $value->hq;
			$data['checkin_time'] = $value->checkin_time;
			$data['checking_lat'] = $att_lat;
			$data['checking_lng'] = $att_lng;
			$data['checking_address'] = $value->track_addrs;
			$data['checkout_address'] = $chk_address;
			$data['checkout_lat'] = $chk_lat;
			$data['checkout_lng'] = $chk_lng;
			$data['hq'] = $value->hq;
			$data['fullname'] = $value->user_name."\n".$value->per_mobile;
			$data['date'] = $value->work_date;
			$data['working_status'] = $value->working_status;
			$data['remarks'] = $value->remarks;
			if($value->image_name != NULL){
			$data['att_image'] = "attendance_images/".$value->image_name;
			}else{
			$data['att_image'] = "msell/images/avatars/profile-pic.jpg";
			}



			$final_data[] = $data;

		}
		if(!empty($final_data))
		{
			return response()->json([ 'response' =>True,'result'=>$final_data]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$final_data]);
		}
    }
 	
 	#...................................................no attendance report starts ..........................................##
    public function no_attendance_report(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $check_junior_data=JuniorData::getJuniorUser($request->user_id,$request->company_id);
        Session::push('juniordata', $request->user_id);

        $junior_data_check = Session::get('juniordata');

        $startTime = strtotime($request->from_date);
		$endTime = strtotime($request->to_date);

	    for ($currentDate = $startTime; $currentDate <= $endTime;  
	                                    $currentDate += (86400)) { 
	                                        
	    $Store = date('Y-m-d', $currentDate); 
	    $datearray[] = $Store; 
	    } 
	    // dd($datearray);
		$no_attendance_array = array();
		$array = array(100,101,102);
		
	    foreach ($datearray as $key => $value) 
	    {
	    	$data1_data = DB::table('person')
           ->join('person_login','person_login.person_id','=','person.id')
           ->join('_role','_role.role_id','=','person.role_id')
           ->where('person_login.person_status','1')  
		   ->whereIn('person.id',$junior_data_check);
		   if($request->user_id == 2833){
				$data1_data->whereNotIn('person.state_id',$array);		
			}
			$data1 = $data1_data->where('person.company_id',$request->company_id)
           ->whereNotIn('person.id',function($query) use($value)
           {
            $query->select('user_id')->from('user_daily_attendance')
                  ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') ='$value'");
          	});

            $data1->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as fullname"),'rolename','head_quar','person.id as user_id','person.mobile as per_mobile','person_login.person_image');
            
            $no_attendance_query = $data1->get();

			if(!empty($no_attendance_query))
			{
				foreach ($no_attendance_query as $no_key => $no_value) 
				{
					$data['fullname'] = $no_value->fullname."\n".$no_value->per_mobile;
					$data['rolename'] = $no_value->rolename;
					$data['date'] = $value;
					$data['id'] = $no_value->user_id;
					$data['hq'] = $no_value->head_quar;

					if($no_value->person_image != NULL){

						$explode = explode('/',$no_value->person_image);

						if(isset($explode[1])){
							$data['att_image'] = "users-profile/".$explode[1];
						}elseif(isset($explode[0])){
							$data['att_image'] = "users-profile/".$no_value->person_image;
						}else{
						$data['att_image'] = "users-profile/".$no_value->person_image;
						}


					}else{
					$data['att_image'] = "msell/images/avatars/profile-pic.jpg";
					}


					$no_attendance_array[] = $data;
				}
			}
			else
			{
				$no_attendance_array = array();
			}
			

	    }
	    if(!empty($no_attendance_array))
	    {
			return response()->json([ 'response' =>True,'result'=>$no_attendance_array]);
	    }
	    else
	    {
			return response()->json([ 'response' =>False,'result'=>$no_attendance_array]);

	    }

        
    }

    #-----------------------------------------------------user wise sale report starts here .................................##

    public function user_wise_sales_report(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $check_junior_data=JuniorData::getJuniorUser($request->user_id,$request->company_id);
        Session::push('juniordata', $request->user_id);

        $junior_data_check = Session::get('juniordata');
        // dd($junior_data_check);
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $table_name = TableReturn::table_return($from_date,$to_date);
   		$sale_data_query = DB::table($table_name)
   							->join('person','person.id','=',$table_name.'.user_id')
   							->join('person_login','person_login.person_id','=','person.id')
							->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as fullname"),'person.mobile as per_mobile',$table_name.'.company_id as company_id',$table_name.'.date as sale_date',$table_name.'.user_id as user_id',DB::raw("MIN(time) as first_call"),DB::raw("MAX(time) as last_call"))
							->whereIn($table_name.'.user_id',$junior_data_check)
							->where($table_name.'.company_id',$request->company_id)
							->where('person_status',1)
							->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$request->from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$request->to_date'")
							->groupBy('user_id','date')
							->get();
							// dd($sale_data_query) 	;

		 $scheme_amount = DB::table($table_name)
            ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$request->from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$request->to_date'")
            ->where('company_id',$request->company_id)
			->whereIn($table_name.'.user_id',$junior_data_check)
            ->groupBy('user_id')
            ->groupBy('date')
            ->pluck(DB::raw('SUM(total_sale_value) as sale'),DB::raw("CONCAT(user_id,date) as concat"));
            // ->pluck(DB::raw('SUM(amount) as sale'),DB::raw("CONCAT(user_id,date) as concat"));


		$user_final_details = array();
		if(!empty($sale_data_query))
		{
			foreach ($sale_data_query as $key => $value) 
			{
				$retailer_total_count_query = DB::table($table_name)
									->where('company_id',$value->company_id)
									->where('user_id',$value->user_id)
									->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$value->sale_date'")
									->count('retailer_id');

				$retailer_pro_count_query = DB::table($table_name)
									->where('company_id',$value->company_id)
									->where('user_id',$value->user_id)
									->where('call_status',1)
									->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$value->sale_date'")
									->count('retailer_id');

				if($value->company_id == '50'){
					$total_sale_value = DB::table($table_name)
								->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
								->select(DB::raw("SUM(final_secondary_rate*final_secondary_qty) as total_sale_value"),DB::raw("COUNT(DISTINCT product_id) as line_per_sold"))
								->where($table_name.'.company_id',$value->company_id)
								->where($table_name.'.user_id',$value->user_id)
								->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')='$value->sale_date'")
								->first();
				}else{
				$total_sale_value = DB::table($table_name)
								->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
								->select(DB::raw("SUM(rate*quantity) as total_sale_value"),DB::raw("COUNT(DISTINCT product_id) as line_per_sold"))
								->where($table_name.'.company_id',$value->company_id)
								->where($table_name.'.user_id',$value->user_id)
								->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')='$value->sale_date'")
								->first();
				}



				$counter_sale = DB::table('counter_sale_summary')
								->join('counter_sale_details','counter_sale_details.order_id','=','counter_sale_summary.order_id')
								->select(DB::raw("SUM(rate*quantity) as total_sale_value"))
								->where('counter_sale_summary.company_id',$value->company_id)
								->where('counter_sale_summary.created_by_person',$value->user_id)
								->whereRaw("DATE_FORMAT(counter_sale_summary.date_time,'%Y-%m-%d')='$value->sale_date'")
								->first();

				$finalSchemeSale = !empty($scheme_amount[$value->user_id.$value->sale_date])?$scheme_amount[$value->user_id.$value->sale_date]:'0';

				$overall_data['name'] = $value->fullname."\n".$value->per_mobile;
				$overall_data['tot_calls'] = !empty($retailer_total_count_query)?"$retailer_total_count_query":'0';
				$overall_data['productive'] = !empty($retailer_pro_count_query)?"$retailer_pro_count_query":'0';
				$overall_data['date'] = $value->sale_date;
				$overall_data['non_productive'] = $retailer_total_count_query-$retailer_pro_count_query;
				$overall_data['total_sale_value'] = !empty($total_sale_value->total_sale_value)?round($total_sale_value->total_sale_value,2):'';
				$overall_data['counter_sale'] = !empty($counter_sale->total_sale_value)?round($counter_sale->total_sale_value,2):'';
				$overall_data['user_id'] = "$value->user_id";
				$overall_data['schemeSale'] = !empty($finalSchemeSale)?round($finalSchemeSale,2):'';
				$overall_data['first_call'] = !empty($value->first_call)?$value->first_call:'0';
				$overall_data['last_call'] = !empty($value->last_call)?$value->last_call:'0';
				$overall_data['sku'] = !empty($total_sale_value->line_per_sold)?$total_sale_value->line_per_sold:'0';


				$user_final_details[] = $overall_data;

			}
			return response()->json([ 'response' =>True,'result'=>$user_final_details]);

		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$user_final_details]);
		}

    }

    #........................................................ junior distributor wise sales ..........................................#
    public function junior_distributor_wise_sale(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'date'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $beat_wise_sale_query = DB::table('user_sales_order')
        					->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
    						->join('dealer','dealer.id','=','user_sales_order.dealer_id')
    						->select('dealer.id as dealer_id','dealer.name as dealer_name','dealer.landline','user_id',DB::raw("SUM(rate*quantity) as total_sale"))
    						->where('user_sales_order.company_id',$request->company_id)
    						->where('user_id',$request->user_id)
    						->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$request->date'")
    						->groupBy('dealer_id')
    						->get();
		$final_data = array();
		if(COUNT($beat_wise_sale_query)>0)
		{
			foreach ($beat_wise_sale_query as $key => $value) 
			{
				$data['dealer_id']= $value->dealer_id;
				$data['dealer_name']= $value->dealer_name;
				$data['dealer_mobile']= $value->landline;
				$data['user_id']= $value->user_id;
				$data['sale']= $value->total_sale;
				$final_data[]=$data;
			}
			return response()->json([ 'response' =>True,'result'=>$final_data]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$final_data]);
		}

		

    }
    #........................................................ junior distributor wise sales ...........................................#






    #...............................................junior wise beat wise sale report starts here .................................##

    public function junior_beat_wise_sale(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            // 'date'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $dealer_id = $request->dealer_id;
        $request_from_date = $request->from_date;
        $request_to_date = $request->to_date;

        if(empty($request->date)){

        $from_date = $request->from_date;
        $to_date = $request->to_date;

        }elseif(!empty($request->date)){

       	$from_date = $request->date;
        $to_date = $request->date;

        }else{

        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');

        }




        $table_name = TableReturn::table_return($from_date,$to_date);


        $check = DB::table('app_other_module_assign')->where('company_id',$request->company_id)->where('module_id',6)->first();

        if(empty($check)){    
        $beat_wise_sale_query_data = DB::table($table_name)
        					->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
    						->join('location_7','location_7.id','=',$table_name.'.location_id')
    						->join('retailer','retailer.id','=',$table_name.'.retailer_id')
    						->select($table_name.'.location_id as beat_id','location_7.name as beat_name',DB::raw("SUM(rate*quantity) as total_sale"),DB::raw("COUNT(DISTINCT retailer.id) as retailer_count"))
    						->where($table_name.'.company_id',$request->company_id)
    						->where('user_id',$request->user_id);

    						if(!empty($dealer_id)){
    							$beat_wise_sale_query_data->where($table_name.'.dealer_id',$dealer_id);
    						}

    	$beat_wise_sale_query = $beat_wise_sale_query_data->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
    						->groupBy('beat_id')
    						->get();
    	}else{

    	$beat_wise_sale_query_data = DB::table($table_name)
        					->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
    						->join('location_7','location_7.id','=',$table_name.'.location_id')
    						->join('retailer','retailer.id','=',$table_name.'.retailer_id')
    						->select($table_name.'.location_id as beat_id','location_7.name as beat_name',DB::raw("SUM(final_secondary_rate*final_secondary_qty) as total_sale"),DB::raw("COUNT(DISTINCT retailer.id) as retailer_count"))

    						->where($table_name.'.company_id',$request->company_id)
    						->where('user_id',$request->user_id);

    						// if(!empty($dealer_id)){
    						// 	$beat_wise_sale_query_data->where('user_sales_order.dealer_id',$dealer_id);
    						// }

    	$beat_wise_sale_query = $beat_wise_sale_query_data->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
    						->groupBy($table_name.'.location_id')
    						->get();

    	}

    	 $scheme_amount = DB::table('user_sales_order')
            ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') = '$request->date'")
            ->where('user_sales_order.company_id',$request->company_id)
    		->where('user_id',$request->user_id)
            ->groupBy('location_id')
            ->pluck(DB::raw('SUM(total_sale_value) as sale'),DB::raw("CONCAT(location_id) as concat"));

            

		$final_data = array();
		if(COUNT($beat_wise_sale_query)>0)
		{
			foreach ($beat_wise_sale_query as $key => $value) 
			{
				$data['beat_id']= $value->beat_id;
				$data['beat_name']= $value->beat_name;
				$data['sale']= $value->total_sale;
				$data['retailer_count']= $value->retailer_count;
				$data['schemeSale']= !empty($scheme_amount[$value->beat_id])?$scheme_amount[$value->beat_id]:'0';
				$final_data[]=$data;
			}
			return response()->json([ 'response' =>True,'result'=>$final_data]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$final_data]);
		}

		

    }

    #.................................................junior  retailer wise starts here ............................................##

    public function junior_retailer_wise_data(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            // 'date'=>'required',
            'userid'=>'required',
            // 'beatid'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $request_from_date = $request->from_date;
        $request_to_date = $request->to_date;

        if(empty($request->date)){

        $from_date = $request->from_date;
        $to_date = $request->to_date;

        }elseif(!empty($request->date)){

       	$from_date = $request->date;
        $to_date = $request->date;

        }else{

        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');

        }


        $check = DB::table('app_other_module_assign')->where('company_id',$request->company_id)->where('module_id',6)->first();


        if(empty($check)){    
        $retailer_data_query_data = DB::table('user_sales_order')
        					->join('user_sales_order_details','user_sales_order.order_id','=','user_sales_order_details.order_id')
        					->join('retailer','retailer.id','=','user_sales_order.retailer_id')
        					->select(DB::raw("SUM(rate*quantity) as total_sale_value"),'retailer.name as retailer_name','retailer_id')
        					->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        					->where('user_sales_order.user_id',$request->userid)
        					->where('user_sales_order.company_id',$request->company_id)
        					->groupBy('retailer_id');
        					if(!empty($request->beatid)){
        					$retailer_data_query_data->where('user_sales_order.location_id',$request->beatid);
        					}
        $retailer_data_query = $retailer_data_query_data->get();

        }else{
    	 $retailer_data_query_data = DB::table('user_sales_order')
    					->join('user_sales_order_details','user_sales_order.order_id','=','user_sales_order_details.order_id')
    					->join('retailer','retailer.id','=','user_sales_order.retailer_id')
    					->select(DB::raw("SUM(final_secondary_rate*final_secondary_qty) as total_sale_value"),'retailer.name as retailer_name','retailer_id')
    					// ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$request->date'")
        				->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
    					->where('user_sales_order.user_id',$request->userid)
    					// ->where('user_sales_order.location_id',$request->beatid)
    					->where('user_sales_order.company_id',$request->company_id)
    					->groupBy('retailer_id');
    					if(!empty($request->beatid)){
        					$retailer_data_query_data->where('user_sales_order.location_id',$request->beatid);
        					}
        $retailer_data_query = $retailer_data_query_data->get();
        }





         $scheme_amount = DB::table('user_sales_order')
            ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' ANd DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")
            ->where('user_sales_order.user_id',$request->userid)
        	->where('user_sales_order.location_id',$request->beatid)
            ->where('company_id',$request->company_id)
            ->groupBy('retailer_id')
            ->pluck(DB::raw('SUM(total_sale_value) as sale'),DB::raw("CONCAT(retailer_id) as concat"));




		$final_retailer_data = array();
		foreach ($retailer_data_query as $key => $value) 
		{
			$data['retailer_id'] = !empty($value->retailer_id)?$value->retailer_id:'';
			$data['retailer_name'] = !empty($value->retailer_name)?$value->retailer_name:'';
			$data['sale'] = !empty($value->total_sale_value)?$value->total_sale_value:'';
			$data['schemeSale'] = !empty($scheme_amount[$value->retailer_id])?$scheme_amount[$value->retailer_id]:'0';
			$final_retailer_data[] = $data;
		}
		if(COUNT($retailer_data_query)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$final_retailer_data]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$final_retailer_data]);
		}
    }

     public function junior_retailer_wise_data_all(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'date'=>'required',
            'userid'=>'required',
            'beatid'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $retailer_data = DB::table('user_sales_order')
        					->join('user_sales_order_details','user_sales_order.order_id','=','user_sales_order_details.order_id')
        					->join('retailer','retailer.id','=','user_sales_order.retailer_id')
        					->select(DB::raw("SUM(rate*quantity) as total_sale_value"),'retailer.name as retailer_name','retailer_id')
        					->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$request->date'")
        					->where('user_sales_order.user_id',$request->userid)
        					->where('user_sales_order.location_id',$request->beatid)
        					->where('user_sales_order.company_id',$request->company_id)
        					->groupBy('retailer_id')
        					->pluck(DB::raw("SUM(rate*quantity) as total_sale_value"),'retailer_id');

        $retailer_data_query = DB::table('user_sales_order')
        					// ->join('user_sales_order_details','user_sales_order.order_id','=','user_sales_order_details.order_id')
        					->join('retailer','retailer.id','=','user_sales_order.retailer_id')
        					->select('retailer.name as retailer_name','retailer_id')
        					->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$request->date'")
        					->where('user_sales_order.user_id',$request->userid)
        					->where('user_sales_order.location_id',$request->beatid)
        					->where('user_sales_order.company_id',$request->company_id)
        					->groupBy('retailer_id')
        					->get();
		$final_retailer_data = array();
		foreach ($retailer_data_query as $key => $value) 
		{
			$data['retailer_id'] = !empty($value->retailer_id)?$value->retailer_id:'';
			$data['retailer_name'] = !empty($value->retailer_name)?$value->retailer_name:'';
			$data['sale'] = !empty($retailer_data[$value->retailer_id])?$retailer_data[$value->retailer_id]:'0.00';
			$final_retailer_data[] = $data;
		}
		if(COUNT($retailer_data_query)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$final_retailer_data]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$final_retailer_data]);
		}
    }

    #.................................................junior  product wise starts here ............................................##

    public function junior_product_wise_data(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            // 'date'=>'required',
            'userid'=>'required',
            // 'beatid'=>'required',
            'retailerid'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }


          $request_from_date = $request->from_date;
        $request_to_date = $request->to_date;

        if(empty($request->date)){

        $from_date = $request->from_date;
        $to_date = $request->to_date;

        }elseif(!empty($request->date)){

       	$from_date = $request->date;
        $to_date = $request->date;

        }else{

        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');

        }


        $check = DB::table('app_other_module_assign')->where('company_id',$request->company_id)->where('module_id',6)->first();

        // $scheme_amount = DB::table('user_sales_order')
        // 					->where('user_id',$request->userid)
    				// 		->where('retailer_id',$request->retailerid)
    				// 		->where('location_id',$request->beatid)
    				// 		->where('company_id',$company_id)
    				// 		->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$request->date'")
    				// 		->
    				// 		->pluck(DB::raw("SUM(total_sale_value) as sale"))

        $product_percentage_data = DB::table('product_wise_scheme_plan_details')
                                ->where('incentive_type',1)
                                ->where('company_id',$request->company_id)
                                // ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$request->date' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$request->date'")
                                ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$from_date' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$from_date'")
                                ->get();

                                // dd($product_percentage_data);

        if(empty($check)){    
                               
        $product_data_query = DB::table('sale_order_product_view')
        					->join('catalog_product','catalog_product.id','=','sale_order_product_view.product_id')
    						->select(DB::raw("SUM(sale_order_product_view.weight) as weight"),'product_id','catalog_product.name as product_name',DB::raw("SUM(quantity) as qty"),DB::raw("SUM(rate*quantity) as sale"))
    						->where('user_id',$request->userid)
    						->where('retailer_id',$request->retailerid)
    						// ->where('location_id',$request->beatid)
    						->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
    						->where('catalog_product.company_id',$request->company_id)
    						// ->where('sale_order_product_view.company_id',$request->company_id)
    						->where('person_status',1);
    						if(!empty($request->beatid)){
    						 $product_data_query->where('location_id',$request->beatid);
    						}

    						$product_data_query->groupBy('product_id');


    						$product_data = $product_data_query->get();
    						$count_product_data = $product_data_query->COUNT();

    	}else{
		 $product_data_query = DB::table('sale_order_product_view')
    					->join('catalog_product','catalog_product.id','=','sale_order_product_view.product_id')
						->select(DB::raw("SUM(sale_order_product_view.weight) as weight"),'product_id','catalog_product.name as product_name',DB::raw("SUM(final_secondary_qty) as qty"),DB::raw("SUM(final_secondary_rate*final_secondary_qty) as sale"))
						->where('user_id',$request->userid)
						->where('retailer_id',$request->retailerid)
						// ->where('location_id',$request->beatid)
						->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
						->where('catalog_product.company_id',$request->company_id)
						// ->where('sale_order_product_view.company_id',$request->company_id)
						->where('person_status',1);
						if(!empty($request->beatid)){
						 $product_data_query->where('location_id',$request->beatid);
						}

    					$product_data_query->groupBy('product_id');
						$product_data = $product_data_query->get();
						$count_product_data = $product_data_query->COUNT();
    	}



    	// $scheme_amount = DB::table('user_sales_order')
    	// 	->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
     //        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$request->date'")
     //        ->where('user_id',$request->userid)
    	// 	->where('retailer_id',$request->retailerid)
    	// 	->where('location_id',$request->beatid)
     //        ->where('user_sales_order.company_id',$request->company_id)
     //        ->groupBy('user_sales_order_details.product_id')
     //        ->pluck(DB::raw('(total_sale_value) as sale'),DB::raw("CONCAT(product_id) as concat"));

		$final_product_data = array();
		foreach ($product_data as $key => $value) 
		{

			$value_percent = !empty($product_percentage_data[$value->product_id])?$product_percentage_data[$value->product_id]:'0';

			$subAmt = $value->sale*($value_percent/100);

			$finalAmt = $value->sale-$subAmt;

			// $schemeAmount = !empty($scheme_amount[$value->product_id])?$scheme_amount[$value->product_id]:'0';



			$data['product_id'] = !empty($value->product_id)?$value->product_id:'';
			$data['product_name'] = !empty($value->product_name)?$value->product_name:'';
			$data['sale'] = !empty($value->sale)?$value->sale:'';
			$data['qty'] = !empty($value->qty)?$value->qty:'';
			$data['weight'] = !empty($value->weight)?$value->weight:'';
			$data['final_value'] = !empty($finalAmt)?$finalAmt:'0';
			$final_product_data[] = $data;
		}
		if(($count_product_data)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$final_product_data]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$final_product_data]);
		}
    }

    #.......................................................list all dealer with sale starts here .....................................##

    public function list_all_dealer_with_sale(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'userid'=>'required',
            'fromdate'=>'required',
            'todate'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        // return response()->json(['response'=>True,'data'=>$_POST ],401);

        // dd($request->company_id);
        $company_id = $request->company_id;
        $check_junior_data=JuniorData::getJuniorUser($request->userid,$company_id);
        Session::push('juniordata', $request->userid);
        $junior_data_check = Session::get('juniordata');
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

        if(empty($check)){
        $list_sale_Q = DB::table('user_sales_order')
        				->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        				->join('dealer','dealer.id','=','user_sales_order.dealer_id')
        				->select('dealer.id as dealer_id','user_sales_order.date as date','dealer.name as dealer_name',DB::raw("SUM(rate*quantity) as sale"),'dealer.other_numbers','dealer.landline')
        				->whereIn('user_id',$junior_data_check)
        				->where('user_sales_order.company_id',$company_id)
        				->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$request->fromdate' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$request->todate'")
        				->groupBy('dealer_id');
						$count_q_sale=$list_sale_Q->count();
        				$list_sale_query=$list_sale_Q->get();
        }else{
        	 $list_sale_Q = DB::table('user_sales_order')
        				->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        				->join('dealer','dealer.id','=','user_sales_order.dealer_id')
        				->select('dealer.id as dealer_id','user_sales_order.date as date','dealer.name as dealer_name',DB::raw("SUM(final_secondary_rate*final_secondary_qty) as sale"),'dealer.other_numbers','dealer.landline')
        				->whereIn('user_id',$junior_data_check)
        				->where('user_sales_order.company_id',$company_id)
        				->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$request->fromdate' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$request->todate'")
        				->groupBy('dealer_id');
						$count_q_sale=$list_sale_Q->count();
        				$list_sale_query=$list_sale_Q->get();
        }

        				// dd($list_sale_Q);


        $scheme_amount = DB::table('user_sales_order')
            ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$request->fromdate' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$request->todate'")
        	->whereIn('user_id',$junior_data_check)
            ->where('company_id',$company_id)
            ->groupBy('dealer_id')
            ->pluck(DB::raw('SUM(total_sale_value) as sale'),DB::raw("CONCAT(dealer_id) as concat"));
		
			$final_list_data = array();
			foreach ($list_sale_query as $key => $value) 
			{

				$schemeSale = !empty($scheme_amount[$value->dealer_id])?$scheme_amount[$value->dealer_id]:'0';

				$landline = !empty($value->landline)?$value->landline:'';
				$dealer_number_no = !empty($value->other_numbers)?$value->other_numbers:$landline;
				$data['dealerid'] = !empty($value->dealer_id)?$value->dealer_id:'';
				$data['date'] = !empty($value->date)?$value->date:'';
				$data['dname'] = !empty($value->dealer_name)?$value->dealer_name."\n".$dealer_number_no:'';
				$data['sale'] = !empty($value->sale)?$value->sale:'';
				$landline = !empty($value->landline)?$value->landline:'';
				$data['other_numbers'] = !empty($value->other_numbers)?$value->other_numbers:$landline;
				$data['schemeSale'] = $schemeSale;
				$final_list_data[] = $data;
			}
			
			if($count_q_sale>0)
			{
				return response()->json([ 'response' =>True,'result'=>$final_list_data]);
			}
		
			else
			{
				return response()->json([ 'response' =>False,'result'=>$final_list_data]);
			}
    }

    #.....................................................list user delaer with total sale starts here.....................................## 	
    public function list_all_user_for_dealerid_with_total_sale(Request $request)
    {
    	$validator=Validator::make($request->all(),[
           'userid'=>'required',
           'fromdate'=>'required',
           'todate'=>'required',
           'dealerid'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $company_id = $request->company_id;
        $check_junior_data=JuniorData::getJuniorUser($request->userid,$company_id);
        Session::push('juniordata', $request->userid);
        $junior_data_check = Session::get('juniordata');
        $check = DB::table('app_other_module_assign')->where('company_id',$request->company_id)->where('module_id',6)->first();


        if(empty($check)){
        $list_dealer_total_sale_query = DB::table('user_sales_order')
    								->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
    								->join('dealer','dealer.id','=','user_sales_order.dealer_id')
    								->join('person','person.id','=','user_sales_order.user_id')
    								->select('person.id as person_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("SUM(rate*quantity) as sale"),'person.mobile')
    								->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$request->fromdate' AND  DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$request->todate'")
    								->where('dealer.id',$request->dealerid)
									->whereIn('person.id',$junior_data_check)
									->where('user_sales_order.company_id',$company_id)
    								->groupBy('person.id')
    								->get();
    	}else{
		$list_dealer_total_sale_query = DB::table('user_sales_order')
								->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
								->join('dealer','dealer.id','=','user_sales_order.dealer_id')
								->join('person','person.id','=','user_sales_order.user_id')
								->select('person.id as person_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("SUM(final_secondary_rate*final_secondary_qty) as sale"),'person.mobile')
								->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$request->fromdate' AND  DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$request->todate'")
								->where('dealer.id',$request->dealerid)
								->whereIn('person.id',$junior_data_check)
								->where('user_sales_order.company_id',$company_id)
								->groupBy('person.id')
								->get();
    	}



    	$scheme_amount = DB::table('user_sales_order')
            ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$request->fromdate' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$request->todate'")
        	->whereIn('user_id',$junior_data_check)
    		->where('user_sales_order.dealer_id',$request->dealerid)
            ->where('company_id',$company_id)
            ->groupBy('user_id')
            ->pluck(DB::raw('SUM(total_sale_value) as sale'),DB::raw("CONCAT(user_id) as concat"));


		$final_list_d_t_s = array();
		foreach ($list_dealer_total_sale_query as $key => $value) 
		{

			$schemeSale = !empty($scheme_amount[$value->person_id])?$scheme_amount[$value->person_id]:'0';


			$data['person_id'] = $value->person_id;
			$data['person_name'] = $value->user_name;
			$data['sale'] = $value->sale;
			$data['user_mobile'] = $value->mobile;
			$data['schemeSale'] = $schemeSale;
			$final_list_d_t_s[] = $data;
		}
		if(!empty($final_list_d_t_s))
		{
			return response()->json([ 'response' =>True,'result'=>$final_list_d_t_s]);

		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$final_list_d_t_s]);

		}


    }

    #.................................................list all locations for dealer id for userid with total sale .........................##

    public function list_all_locations_for_dealer_id_for_user_id_with_total_sale(Request $request)
    {
    	$validator=Validator::make($request->all(),[
           'userid'=>'required',
           'fromdate'=>'required',
           'todate'=>'required',
           'dealerid'=>'required',
           'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $company_id = $request->company_id;
        $check_junior_data=JuniorData::getJuniorUser($request->userid,$company_id);
        Session::push('juniordata', $request->userid);
		$junior_data_check = Session::get('juniordata');
        $check = DB::table('app_other_module_assign')->where('company_id',$request->company_id)->where('module_id',6)->first();
		
        if(empty($check)){
        $dealer_user_sale_query = DB::table('dealer')
        						->join('user_sales_order','user_sales_order.dealer_id','=','dealer.id')
        						->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        						->join('location_7','location_7.id','=','user_sales_order.location_id')
        						->join('person','person.id','=','user_sales_order.user_id')
        						->select('location_7.id as location_id','location_7.name as location_name',DB::raw("SUM(rate*quantity) as sale"))
        						->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$request->fromdate' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$request->todate'")
        						->whereIn('user_id',$junior_data_check)
        						->where('dealer_id',$request->dealerid)
        						->where('user_sales_order.company_id',$company_id)
        						->where('dealer.company_id',$company_id)
        						->groupBy('location_id')
								->get();
		}else{
			$dealer_user_sale_query = DB::table('dealer')
								->join('user_sales_order','user_sales_order.dealer_id','=','dealer.id')
								->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
								->join('location_7','location_7.id','=','user_sales_order.location_id')
								->join('person','person.id','=','user_sales_order.user_id')
								->select('location_7.id as location_id','location_7.name as location_name',DB::raw("SUM(final_secondary_rate*final_secondary_qty) as sale"))
								->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$request->fromdate' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$request->todate'")
								->whereIn('user_id',$junior_data_check)
								->where('dealer_id',$request->dealerid)
								->where('user_sales_order.company_id',$company_id)
								->where('dealer.company_id',$company_id)
								->groupBy('location_id')
								->get();

		}


        $scheme_amount = DB::table('user_sales_order')
            ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$request->fromdate' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$request->todate'")
            ->where('company_id',$company_id)
            ->whereIn('user_id',$junior_data_check)
        	->where('dealer_id',$request->dealerid)
            ->groupBy('location_id')
            ->pluck(DB::raw('SUM(total_sale_value) as sale'),DB::raw("CONCAT(location_id) as concat"));


		$final_sale_data_user_dealer = array();
		foreach ($dealer_user_sale_query as $key => $value) 
		{

			$schemeSale = !empty($scheme_amount[$value->location_id])?$scheme_amount[$value->location_id]:'0';


			$data_sale['location_id'] = $value->location_id;
			$data_sale['location_name'] = $value->location_name;
			$data_sale['sale'] = $value->sale;
			$data_sale['schemeSale'] = $schemeSale;
			$final_sale_data_user_dealer[] = $data_sale;
		}
		if(COUNT($final_sale_data_user_dealer)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$final_sale_data_user_dealer]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$final_sale_data_user_dealer]);

		}
    }

    #...............................................list_all_dealer_for_users_with_total_payment.........................................##

    public function list_all_dealer_for_users_with_total_payment(Request $request)
    {
    	$validator=Validator::make($request->all(),[
           'userid'=>'required',
           'fromdate'=>'required',
           'todate'=>'required',
           'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $paymet_query = DB::table('payment_collect_retailer')
    				->join('dealer','dealer.id','=','payment_collect_retailer.dealer_id')
    				->select('dealer_id','name',DB::raw("SUM(amount) as amount"))
    				->whereRaw("DATE_FORMAT(payment_date,'%Y-%m-%d')>='$request->fromdate' AND DATE_FORMAT(payment_date,'%Y-%m-%d')<='$request->todate'")
    				->where('user_id',$request->userid)
    				->where('payment_collect_retailer.company_id',$request->company_id)
    				->where('dealer.company_id',$request->company_id)
    				->groupBy('dealer_id')
    				->get();
		$final_payment_data = array();
		foreach ($paymet_query as $key => $value) 
		{
			$payment_data['dealer_id'] = $value->dealer_id;
			$payment_data['name'] = $value->name;
			$payment_data['amount'] = $value->amount;
			$final_payment_data[] = $payment_data;
		}
		if(COUNT($paymet_query)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$final_payment_data]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$final_payment_data]);
		}

    }

    #..................................................list all retailer payment starts here ...........................................##


    public function list_all_retailer_payment(Request $request)
    {
    	$validator=Validator::make($request->all(),[
           'userid'=>'required',
           'fromdate'=>'required',
           'todate'=>'required',
           'company_id'=>'required',
           'dealerid'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $retailer_payment_query = DB::table('payment_collect_retailer')
        						->join('retailer','retailer.id','=','payment_collect_retailer.tr_code')
        						->select('retailer.id as retailer_id','name',DB::raw("SUM(amount) as amount"))
        						->whereRaw("DATE_FORMAT(payment_date,'%Y-%m-%d')>='$request->fromdate' AND DATE_FORMAT(payment_date,'%Y-%m-%d')<='$request->todate'")
			    				->where('user_id',$request->userid)
			    				->where('payment_collect_retailer.company_id',$request->company_id)
			    				->where('retailer.company_id',$request->company_id)
			    				->where('payment_collect_retailer.dealer_id',$request->dealerid)
			    				->groupBy('tr_code')
			    				->get();
		$final_retailer_payment_data = array();
		foreach ($retailer_payment_query as $key => $value) 
		{
			$payment_retailer['retailer_id'] = $value->retailer_id;
			$payment_retailer['retailer_name'] = $value->name;
			$payment_retailer['amount'] = $value->amount;
			$final_retailer_payment_data[] = $payment_retailer;
		}
		if(COUNT($retailer_payment_query)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$final_retailer_payment_data]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$final_retailer_payment_data]);
		}
    }

    #...................................................list_dealer_for_user starts here ..........................................##
    public function list_dealer_for_user(Request $request)
    {
    	$validator=Validator::make($request->all(),[
           'userid'=>'required',
           'from_date'=>'required',
           'to_date'=>'required',
           'company_id'=>'required',
          
		]);
		// return response()->json(['response'=>FALSE,'message'=>$_POST,'Error'=>$validator->errors()],401);
		
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $check_junior_data=JuniorData::getJuniorUser($request->userid,$request->company_id);
        Session::push('juniordata', $request->userid);
        $junior_data_check = Session::get('juniordata');

        // dd($junior_data_check);

        $list_dealer_user_query = DB::table('dealer_balance_stock')
        						->join('dealer','dealer.id','=','dealer_balance_stock.dealer_id')
        						->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
        						->select('dealer.id as dealer_id','dealer.name as dealer_name','dealer.other_numbers')
        						->whereRaw("DATE_FORMAT(submit_date_time,'%Y-%m-%d')>='$request->from_date' AND DATE_FORMAT(submit_date_time,'%Y-%m-%d')<='$request->to_date'")
        						->whereIn('dealer_location_rate_list.user_id',$junior_data_check)
        						// ->where('dealer_balance_stock.user_id',$request->userid)
        						->where('dealer_balance_stock.company_id',$request->company_id)
        						->where('dealer.company_id',$request->company_id)
        						->groupBy('dealer_balance_stock.dealer_id')
        						->get();
		$final_dealer_balance = array();
		foreach ($list_dealer_user_query as $key => $value) 
		{
			$landline = !empty($value->landline)?$value->landline:'';
			$dealer_number_no = !empty($value->other_numbers)?$value->other_numbers:$landline;
			$list_dealer_balance['dealer_id'] = $value->dealer_id;
			$list_dealer_balance['dealer_name'] = $value->dealer_name."\n".$dealer_number_no;
			$final_dealer_balance[] = $list_dealer_balance;
		}
		if(COUNT($list_dealer_user_query)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$final_dealer_balance]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$final_dealer_balance]);
		}
    }
	#........................................................dealer stock for user starts here........................................................##
	public function dealer_stock_for_user(Request $request)
	{
		$validator=Validator::make($request->all(),[
			'user_id'=>'required',
			'from_date'=>'required',
			'to_date'=>'required',
			'company_id'=>'required',
			'dealer_id'=>'required',
		   
		 ]);
		//  return response()->json(['response'=>FALSE,'message'=>$_POST,'Error'=>$validator->errors()],401);
		 
		 if($validator->fails())
		 {
			 return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		 }
		$dealer_balance_stock_query = DB::table('dealer_balance_stock')
									->join('catalog_view','catalog_view.product_id','=','dealer_balance_stock.product_id')
									->select(DB::raw("DATE_FORMAT(`submit_date_time`,'%d-%m-%Y')AS sdate"),'catalog_view.product_name as product_name','dealer_balance_stock.stock_qty as stock_qty','dealer_balance_stock.cases as cases','dealer_balance_stock.mrp as mrp','dealer_balance_stock.pcs_mrp as pcs_mrp','dealer_balance_stock.balance_secondary_qty as secondary_qty')
									->where('dealer_id',$request->dealer_id)
									->where('user_id',$request->user_id)
									->whereRaw("DATE_FORMAT(submit_date_time,'%Y-%m-%d')>='$request->from_date' AND DATE_FORMAT(submit_date_time,'%Y-%m-%d')<='$request->to_date'")
									->where('dealer_balance_stock.company_id',$request->company_id)
									->get();
		$balance_array = array();
		foreach($dealer_balance_stock_query as $key=>$value)
		{
			$out['date'] = $value->sdate;
			$out['product_name'] = $value->product_name;
			$out['stock'] = $value->stock_qty;
			$out['cases'] = $value->cases;
			$out['mrp'] = $value->mrp;
			$out['pcs_mrp'] = $value->pcs_mrp;
			$out['secondary_qty'] = $value->secondary_qty;
			$out['total'] = ROUND(($value->cases*$value->mrp)+($value->stock_qty*$value->pcs_mrp),2);
			$balance_array[] = $out;
		}
		if(COUNT($dealer_balance_stock_query)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$balance_array]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$balance_array]);
		}
	} 
	#.......................................................list all dealer payments starts here .....................................##
	public function list_all_dealer_payment(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'userid'=>'required',
            'fromdate'=>'required',
            'todate'=>'required',
            'company_id'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

         $company_id = $request->company_id;
         $out = array();
         $expense = array();

         $list_all_dealer_payment_query = DB::table('dealer_payments')
         							->join('dealer','dealer.id','=','dealer_payments.dealer_id')
         							->select('dealer_id','name',DB::raw("SUM(amount) as amount"))
         							->whereRaw("DATE_FORMAT(dealer_payments.payment_recevied_date,'%Y-%m-%d')>='$request->fromdate' AND DATE_FORMAT(dealer_payments.payment_recevied_date,'%Y-%m-%d')<='$request->todate'")
         							->where('dealer_payments.user_id',$request->userid)
									->where('dealer_payments.company_id',$company_id)
									->where('dealer.company_id',$company_id)
									->groupBy('dealer_id')
									->get();
        $final_list_data = array();							
        foreach ($list_all_dealer_payment_query as $key => $value) 
		{
			$data['dealer_id'] = !empty($value->dealer_id)?$value->dealer_id:'';
			$data['dealer_name'] = !empty($value->name)?$value->name:'';
			$data['amount'] = !empty($value->amount)?$value->amount:'';
			$final_list_data[] = $data;
		}
		if(COUNT($list_all_dealer_payment_query)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$final_list_data]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$final_list_data]);
		}							
    }
    #.......................................................list all dealer payments ends here .....................................##
	#.......................................................list all dealer details starts here .....................................##
	
	public function list_all_dealer_payment_details(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'userid'=>'required',
            'dealerid'=>'required',
            'fromdate'=>'required',
            'todate'=>'required',
            'company_id'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
		$dealer_all_payment_query = DB::table('dealer_payments')
								->join('dealer','dealer.id','=','dealer_payments.dealer_id')
								->select('dealer_id','dealer.name as dealer_name',DB::raw("SUM(amount) as amount"),DB::raw("DATE_FORMAT(payment_recevied_date,'%d-%m-%Y') as pdate"))
								->whereRaw("DATE_FORMAT(payment_recevied_date,'%Y-%m-%d')>='$request->fromdate' AND DATE_FORMAT(payment_recevied_date,'%Y-%m-%d')<='$request->todate' ")
								->where('dealer_id',$request->dealerid)
								->where('user_id',$request->userid)
								->where('dealer_payments.company_id',$request->company_id)
								->groupBy('dealer_id','payment_recevied_date')
								->get();
		$final_list_payment_dealer = array();
		foreach($dealer_all_payment_query as $key => $value)
		{
			$out['dealer_id'] = $value->dealer_id;
			$out['dealer_name'] = $value->dealer_name;
			$out['date'] = $value->pdate; 
			$out['amount'] = $value->amount; 
			$final_list_payment_dealer[] = $out;
		}
		if(COUNT($dealer_all_payment_query)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$final_list_payment_dealer]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$final_list_payment_dealer]);
		}

	}
	#.....................................................................self expense starts here .....................................................##
	public function selfExpense(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'userid'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
            'company_id'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}

		//  $check_junior_data=JuniorData::getJuniorUser($request->userid,$request->company_id);
  //       Session::push('juniordata', $request->userid);
		// $junior_data_check = Session::get('juniordata');


		$self_expense_query = DB::table('travelling_expense_bill')
							->select('travellingDate','status',DB::raw('round(total) as expense'))
							->whereRaw("DATE_FORMAT(travellingDate,'%Y-%m-%d')>='$request->from_date' AND DATE_FORMAT(travellingDate,'%Y-%m-%d')<='$request->to_date'")
							->where('user_id',$request->userid)
							->groupBy('travellingDate','user_id')
							->get();
		$final_expense_array = array();
		foreach($self_expense_query as $key => $value)
		{
			$out['date'] = $value->travellingDate;
			$out['value'] = $value->expense;
			$out['status'] = ($value->status==0)?'Pending':'Approved';
			$final_expense_array[] = $out;
		}
		if(COUNT($self_expense_query)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$final_expense_array]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$final_expense_array]);
		}

	}
	#...................................................................close of the day start here..................................................##
	public function close_of_the_day(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
            'company_id'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}


		 $check_junior_data=JuniorData::getJuniorUser($request->user_id,$request->company_id);
        Session::push('juniordata', $request->user_id);
		$junior_data_check = Session::get('juniordata');

		$close_day_query = array();
		$close_day_query = DB::table('close_of_the_day')
						->join('person','person.id','=','close_of_the_day.created_by')
						->join('person_login','person_login.person_id','=','person.id')
						->select('close_of_the_day.*',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"))
						->whereRaw("DATE_FORMAT(currentDate,'%Y-%m-%d')>='$request->from_date' AND DATE_FORMAT(currentDate,'%Y-%m-%d')<='$request->to_date'")
						->where('close_of_the_day.company_id',$request->company_id)
						->whereIn('close_of_the_day.created_by',$junior_data_check)
						->where('person_status',1)
						->get();
		
		if(COUNT($close_day_query)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$close_day_query]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$close_day_query]);
		}
	}
	#.......................................................................daily comments starts here .................................................##
	public function daily_comments(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
            'company_id'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		$daily_comments_query = array();
		$daily_comments_query = DB::table('daily_comments')
						->join('person','person.id','=','daily_comments.created_by')
						->join('person_login','person_login.person_id','=','person.id')
						->select('daily_comments.*',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"))
						->where('person_status',1)
						->whereRaw("DATE_FORMAT(date_time,'%Y-%m-%d')>='$request->from_date' AND DATE_FORMAT(date_time,'%Y-%m-%d')<='$request->to_date'")
						->where('daily_comments.created_by',$request->user_id)
						->where('daily_comments.company_id',$request->company_id)
						->groupBy('daily_comments.id')
						->get();
		if(COUNT($daily_comments_query)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$daily_comments_query]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$daily_comments_query]);
		}
	}
	#..................................................................mordern_trade_meeting starts here ............................................##
	public function mordern_trade_meeting(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
            'company_id'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}

		$mordern_trade_meeting_query = array();
		$mordern_trade_meeting_query = DB::table('mordern_trade_meeting')
									->join('person','person.id','=','mordern_trade_meeting.user_id')
									->join('person_login','person_login.person_id','=','person.id')
									->select('mordern_trade_meeting.*',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"))
									->where('person_status',1)
									->whereRaw("DATE_FORMAT(cur_date,'%Y-%m-%d')>='$request->from_date' AND DATE_FORMAT(cur_date,'%Y-%m-%d')<='$request->to_date'")
									->where('user_id',$request->user_id)
									->where('mordern_trade_meeting.company_id',$request->company_id)
									->groupBy('mordern_trade_meeting.id')
									->get();

		if(COUNT($mordern_trade_meeting_query)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$mordern_trade_meeting_query]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$mordern_trade_meeting_query]);
		}
	}
	#..................................................................general_trade starts here ............................................##
	public function general_trade_meeting(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
            'company_id'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}

		$general_trade_meeting_query = array();
		$general_trade_meeting_query = DB::table('general_trade_meeting')
									->join('person','person.id','=','general_trade_meeting.user_id')
									->join('person_login','person_login.person_id','=','person.id')
									->select('general_trade_meeting.*',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"))
									->where('person_status',1)
									->whereRaw("DATE_FORMAT(cur_date,'%Y-%m-%d')>='$request->from_date' AND DATE_FORMAT(cur_date,'%Y-%m-%d')<='$request->to_date'")
									->where('user_id',$request->user_id)
									->where('general_trade_meeting.company_id',$request->company_id)
									->groupBy('general_trade_meeting.id')
									->get();

		if(COUNT($general_trade_meeting_query)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$general_trade_meeting_query]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$general_trade_meeting_query]);
		}
	}
	#.......................................................................market report 1 starts here ..........................................##
	public function market_report_1(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
            'company_id'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}

		$market_report_1_query = array();
		$market_report_1_query = DB::table('market_report_1')
									->join('person','person.id','=','market_report_1.created_by')
									->join('person_login','person_login.person_id','=','person.id')
									->select('market_report_1.*',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"))
									->where('person_status',1)
									->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$request->from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$request->to_date'")
									->where('market_report_1.created_by',$request->user_id)
									->where('market_report_1.company_id',$request->company_id)
									->groupBy('market_report_1.id')
									->get();

		if(COUNT($market_report_1_query)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$market_report_1_query]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$market_report_1_query]);
		}
	}

	#.......................................................................market report 2 starts here ..........................................##
	public function market_report_2(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
            'company_id'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}

		$market_report_2_query = array();
		$market_report_2_query = DB::table('market_report_2')
									->join('person','person.id','=','market_report_2.created_by')
									->join('person_login','person_login.person_id','=','person.id')
									->select('market_report_2.*',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"))
									->where('person_status',1)
									->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$request->from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$request->to_date'")
									->where('market_report_2.created_by',$request->user_id)
									->where('market_report_2.company_id',$request->company_id)
									->groupBy('market_report_2.id')
									->get();

		if(COUNT($market_report_2_query)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$market_report_2_query]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$market_report_2_query]);
		}
	}
	public function daily_reporting(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
            'company_id'=>'required',
		]);
		if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		$daily_reporting_query = array();


		$check_junior_data=JuniorData::getJuniorUser($request->user_id,$request->company_id);
        Session::push('juniordata', $request->user_id);
		$junior_data_check = Session::get('juniordata');
		// $daily_reporting_query_data = DB::table('daily_reporting')
		// 						->leftJoin('location_7','location_7.id','=','daily_reporting.location_id')
		// 						->Join('person','person.id','=','daily_reporting.user_id')
		// 						->Join('person_login','person_login.person_id','=','person.id')
		// 						->leftJoin('_daily_schedule','_daily_schedule.id','=','daily_reporting.working_with')
		// 						->leftJoin('dealer','dealer.id','=','daily_reporting.dealer_id')
		// 						->leftJoin('_working_status','_working_status.id','=','daily_reporting.work_status_id')
		// 						->select("dealer.name as dealer_name","_daily_schedule.name as work_status_name",DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("DATE_FORMAT(daily_reporting.work_date,'%d-%m-%Y') as wdate"),"daily_reporting.work_status as working_with","location_7.name as beat","_working_status.name as work_status","daily_reporting.remarks as remarks")
		// 		

		$schedule_name =  DB::table('_daily_schedule')->where('company_id',$request->company_id)->pluck('name','id');	
					
		$daily_reporting_query = DB::table('daily_reporting')
				            // ->leftJoin('_working_with','daily_reporting.working_with','=','_working_with.id')
				            ->Join('person','person.id','=','daily_reporting.user_id')
				            ->join('person_login','person_login.person_id','=','person.id')
							->Join('_role','_role.role_id','=','person.role_id')
							->join('location_3','location_3.id','=','person.state_id')
							->join('location_2','location_2.id','=','location_3.location_2_id')
							->join('location_1','location_1.id','=','location_2.location_1_id')
				            // ->Join('location_view','location_view.l3_id','=','person.state_id')
				            ->leftJoin('dealer','dealer.id','=','daily_reporting.dealer_id')
				            ->select('person.mobile as per_mobile',"dealer.name as dealer_name",'dealer.other_numbers','dealer.landline',DB::raw("DATE_FORMAT(daily_reporting.work_date,'%d-%m-%Y') as wdate"),'daily_reporting.working_with as working_with_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'location_1.name as l1_name','location_2.name as l2_name','emp_code','rolename AS role_name',DB::raw("DATE_FORMAT(work_date,'%d-%m-%Y') AS date"),'work_status','work_date','person.id AS user_id','remarks','daily_reporting.daily_schedule_id')
				            ->whereRaw("DATE_FORMAT(daily_reporting.work_date,'%Y-%m-%d')>='$request->from_date' AND DATE_FORMAT(daily_reporting.work_date,'%Y-%m-%d')<='$request->to_date'")
							->whereIn('person.id',$junior_data_check)
							->where('person_status',1)
							->where('daily_reporting.company_id',$request->company_id)
							->groupBy('person.id','work_date')
							->orderBy('work_date','ASC')
							->orderBy('person.id','ASC')
							->get();
	 	$person_name = DB::table('person')
                	->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as working_with_name"),'id');
        $final_out_array = array();
		foreach ($daily_reporting_query as $key => $value) 
		{
			$working_with_id=$value->working_with_id;
		 	if($working_with_id == 0)
            {
                $working_with_name = "SELF";
            }
            else
            {
                $working_with_name = !empty($person_name[$working_with_id])?$person_name[$working_with_id]:'';
            }
            $landline = !empty($value->landline)?$value->landline:'';
            $dealer_no = !empty($value->other_numbers)?$value->other_numbers:$landline;
			$out['dealer_name'] = !empty($value->dealer_name)?$value->dealer_name."\n".$dealer_no:'';
			$out['work_status_name'] = $value->work_status;
			$out['wdate'] = $value->wdate;
			$out['working_with'] = $working_with_name;
			$out['work_status'] = $value->work_status;
			$out['remarks'] = $value->remarks;
			$out['user_name'] = $value->user_name."\n".$value->per_mobile;
			$out['schedule_name'] = !empty($schedule_name[$value->daily_schedule_id])?$schedule_name[$value->daily_schedule_id]:'';
			$final_out_array[] = $out;
		}
		if(COUNT($final_out_array)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$final_out_array]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$final_out_array]);
		}

	}
	public function primary_sale_report(Request $request)
	{
		$validator=Validator::make($request->all(),[
            // 'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
            'company_id'=>'required',
		]);
		if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		$from_date = $request->from_date;
		$to_date = $request->to_date;
		$company_id = $request->company_id;
		$user_id = $request->user_id;
		$dealer_id = $request->dealer_id;

        $check = DB::table('app_other_module_assign')->where('company_id',$request->company_id)->where('module_id',6)->first();


		if(!empty($check)){
			$dealer_primary_query = DB::table('user_primary_sales_order')
								->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
								->leftJoin('person','person.id','=','user_primary_sales_order.created_person_id')
								->leftJoin('person_login','person_login.person_id','=','person.id')
								->join('dealer','dealer.id','=','user_primary_sales_order.dealer_id')
								->select(DB::raw("ROUND(sum(final_secondary_rate*final_secondary_qty),2) as total_sale_value"),DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'dealer.name as dealer_name','user_primary_sales_order.order_id as order_id','dealer_id','sale_date')
								// ->where('person.company_id',$request->company_id)
								->where('user_primary_sales_order.company_id',$request->company_id)
								->where('dealer.company_id',$request->company_id)
								
								->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(sale_date,'%Y-%m-%d')<='$to_date'")
								->where('dealer_status',1)
								->where('person_status',1);
								if(!empty($user_id)){
										$dealer_primary_query_data->where('created_person_id',$user_id);
								}
								if(!empty($dealer_id)){
										$dealer_primary_query_data->where('dealer.id',$dealer_id);
								}
			$dealer_primary_query = $dealer_primary_query_data->groupBy('dealer_id','order_id')->get();
								// dd($dealer_primary_query);
			$final_dealer_saler_data = array();
			if(COUNT($dealer_primary_query)>0)
			{
				foreach($dealer_primary_query as $key => $value)
				{
					$order_id = $value->order_id;
					$date = $value->sale_date;
					
					
					$primary_sale_details_query = DB::table('user_primary_sales_order_details')
												->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
												// ->select(DB::raw("SUM(user_primary_sales_order_details.weight) as weight"),'catalog_product.name as product_name','user_primary_sales_order_details.rate as rate',DB::raw("SUM(user_primary_sales_order_details.pcs) as quantity1"),DB::raw("(user_primary_sales_order_details.pcs)+(user_primary_sales_order_details.cases*catalog_product.quantity_per_case) as quantity"),DB::raw("sum(final_secondary_rate*final_secondary_qty) as total_sale_value"))
												->select(DB::raw("SUM(user_primary_sales_order_details.weight) as weight"),'catalog_product.name as product_name','user_primary_sales_order_details.final_secondary_rate as rate',DB::raw("SUM(user_primary_sales_order_details.final_secondary_qty) as quantity1"),DB::raw("SUM(user_primary_sales_order_details.final_secondary_qty) as quantity"),DB::raw("ROUND(sum(final_secondary_rate*final_secondary_qty),2) as total_sale_value"))
												->where('order_id',$order_id)
												->where('user_primary_sales_order_details.company_id',$company_id)
												->where('catalog_product.company_id',$company_id)
												->groupBy('product_id')
												->get();
					$out['dealer_name'] = $value->dealer_name;
					$out['user_name'] = $value->user_name;
					$out['order_id'] = $value->order_id;
					$out['date'] = $value->sale_date;
					// $out['weight'] = $value->weight;
					$out['total_sale_value'] = $value->total_sale_value;
					$out['product_details'] = $primary_sale_details_query;
					$final_dealer_saler_data[] = $out; 

				}
				if(COUNT($final_dealer_saler_data)>0)
				{
					return response()->json([ 'response' =>True,'result'=>$final_dealer_saler_data]);
				}
				else
				{
					return response()->json([ 'response' =>False,'result'=>$final_dealer_saler_data]);
				}

			}
			else
			{
				return response()->json([ 'response' =>False,'result'=>$final_dealer_saler_data]);

			}

		}
		else{

			$dealer_primary_query_data = DB::table('user_primary_sales_order')
								->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
								->leftJoin('person','person.id','=','user_primary_sales_order.created_person_id')
								->leftJoin('person_login','person_login.person_id','=','person.id')
								->join('dealer','dealer.id','=','user_primary_sales_order.dealer_id')
								->select(DB::raw("sum((rate*quantity)+(cases*pr_rate)) as total_sale_value"),DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'dealer.name as dealer_name','user_primary_sales_order.order_id as order_id','dealer_id','sale_date')
								// ->where('person.company_id',$request->company_id)
								->where('user_primary_sales_order.company_id',$request->company_id)
								->where('dealer.company_id',$request->company_id)
								// ->where('created_person_id',$user_id)
								->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(sale_date,'%Y-%m-%d')<='$to_date'");
								// ->where('dealer_status',1);
								// ->where('person_status',1);
								if(!empty($user_id)){
										$dealer_primary_query_data->where('created_person_id',$user_id);
								}
								if(!empty($dealer_id)){
										$dealer_primary_query_data->where('dealer.id',$dealer_id);
								}
			$dealer_primary_query = $dealer_primary_query_data->groupBy('dealer_id','order_id')->get();
								// dd($dealer_primary_query);
			$final_dealer_saler_data = array();
			if(COUNT($dealer_primary_query)>0)
			{
				foreach($dealer_primary_query as $key => $value)
				{
					$order_id = $value->order_id;
					$date = $value->sale_date;
					
					
					$primary_sale_details_query = DB::table('user_primary_sales_order_details')
												->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
												->select(DB::raw("SUM(user_primary_sales_order_details.weight) as weight"),'catalog_product.name as product_name','user_primary_sales_order_details.rate as rate',DB::raw("SUM(user_primary_sales_order_details.quantity) as quantity1"),DB::raw("(user_primary_sales_order_details.quantity)+(user_primary_sales_order_details.cases*catalog_product.quantity_per_case) as quantity"),DB::raw("sum((rate*quantity)+(cases*pr_rate)) as total_sale_value"))
												->where('order_id',$order_id)
												->where('user_primary_sales_order_details.company_id',$company_id)
												->where('catalog_product.company_id',$company_id)
												->groupBy('product_id')
												->get();
					$out['dealer_name'] = $value->dealer_name;
					$out['user_name'] = $value->user_name;
					$out['order_id'] = $value->order_id;
					$out['date'] = $value->sale_date;
					// $out['weight'] = $value->weight;
					$out['total_sale_value'] = $value->total_sale_value;
					$out['product_details'] = $primary_sale_details_query;
					$final_dealer_saler_data[] = $out; 

				}
				if(COUNT($final_dealer_saler_data)>0)
				{
					return response()->json([ 'response' =>True,'result'=>$final_dealer_saler_data]);
				}
				else
				{
					return response()->json([ 'response' =>False,'result'=>$final_dealer_saler_data]);
				}

			}
			else
			{
				return response()->json([ 'response' =>False,'result'=>$final_dealer_saler_data]);

			}
		}
		
	
		
	}

	public function list_all_retailer_for_dealerid_for_locationid_with_total_sale(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'userid'=>'required',
            'dealerid'=>'required',
            'fromdate'=>'required',
            'todate'=>'required',
            'company_id'=>'required',
            'locationid'=>'required',
            'salespersonid'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}

		$locationid = $request->locationid;
		$dealerid = $request->dealerid;
		$company_id = $request->company_id;
		$userid = $request->userid;
		$salespersonid = $request->salespersonid;
		$fromdate = $request->fromdate;
		$todate = $request->todate;

		$check_junior_data=JuniorData::getJuniorUser($userid,$company_id);
        Session::push('juniordata', $userid);
		$junior_data_check = Session::get('juniordata');
		

		$query_data = DB::table('dealer')
					->select(DB::raw("IF(retailer.id IS NULL,'',retailer.id) as retailer_id"),'retailer.name as retailer_name',DB::raw("SUM(rate*quantity) as sale"),'retailer.other_numbers','retailer.landline')
					->join('user_sales_order','user_sales_order.dealer_id','=','dealer.id')
					->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
					->join('retailer','retailer.id','=','user_sales_order.retailer_id')
					->join('location_7','location_7.id','=','user_sales_order.location_id')
					->join('person','person.id','=','user_sales_order.user_id')
					->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$fromdate' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$todate'")
					->where('dealer.id',$dealerid)
					->where('location_7.id',$locationid)
					->where('location_7.company_id',$company_id)
					->where('user_sales_order.company_id',$company_id)
					->where('retailer.company_id',$company_id)
					->where('retailer_status',1)
					->whereIn('person.id',$junior_data_check)
					->where('person.id',$salespersonid)
					->groupBy('retailer_id')
					->get();


		$scheme_amount = DB::table('user_sales_order')
            ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$fromdate' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$todate'")
            ->where('dealer_id',$dealerid)
			->where('location_id',$locationid)
			->whereIn('user_id',$junior_data_check)
			->where('user_id',$salespersonid)
            ->where('company_id',$company_id)
            ->groupBy('retailer_id')
            ->pluck(DB::raw('SUM(total_sale_value) as sale'),DB::raw("CONCAT(retailer_id) as concat"));


		$f_out = array();
		foreach ($query_data as $key => $value) {

			$schemeSale = !empty($scheme_amount[$value->retailer_id])?$scheme_amount[$value->retailer_id]:'0';

			# code...
			$out['retailer_id'] = !empty($value->retailer_id)?$value->retailer_id:'';
			$out['retailer_name'] = !empty($value->retailer_name)?$value->retailer_name:'';
			$out['sale'] = !empty($value->sale)?$value->sale:'';
			$landline = !empty($value->landline)?$value->landline:'';
			$out['other_numbers'] = !empty($value->other_numbers)?$value->other_numbers:$landline;
			$out['schemeSale'] = $schemeSale;
			$f_out[] = $out;
		}
		if(COUNT($f_out)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$f_out]);

		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$f_out]);
		}

	}

	public function list_all_product_for_dealerid_userid_locationid_with_total_sale(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'userid'=>'required',
            'dealerid'=>'required',
            'fromdate'=>'required',
            'todate'=>'required',
            'company_id'=>'required',
            'locationid'=>'required',
            'salespersonid'=>'required',
            'retailerid'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}

		$locationid = $request->locationid;
		$dealerid = $request->dealerid;
		$company_id = $request->company_id;
		$userid = $request->userid;
		$salespersonid = $request->salespersonid;
		$retailerid = $request->retailerid;
		$fromdate = $request->fromdate;
		$todate = $request->todate;
		$retailerid = $request->retailerid;

        $check = DB::table('app_other_module_assign')->where('company_id',$request->company_id)->where('module_id',6)->first();


		$check_junior_data=JuniorData::getJuniorUser($userid,$company_id);
        Session::push('juniordata', $userid);
		$junior_data_check = Session::get('juniordata');


		 $product_percentage_data = DB::table('product_wise_scheme_plan_details')
                                ->where('incentive_type',1)
                                ->where('company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$request->date' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$request->date'")
                                ->get();
		
        if(empty($check)){
		$query_data_final_layer = DB::table('dealer')
								->join('user_sales_order','user_sales_order.dealer_id','=','dealer.id') 
								->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id') 
								->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id') 
								->join('retailer','retailer.id','=','user_sales_order.retailer_id') 
								->join('location_7','location_7.id','=','user_sales_order.location_id') 
								->join('person','person.id','=','user_sales_order.user_id')
								->join('person_login','person_login.person_id','=','person.id')
								->select(DB::raw("SUM(user_sales_order_details.weight) as weight"),'catalog_product.id as prod_id','quantity','catalog_product.name as prod_name',DB::raw("SUM(rate*quantity) as sale"))
								->where('person_status',1)
								->where('dealer_status',1)
								->where('retailer_status',1)
								->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$fromdate' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$todate'")
								->where('dealer.id',$dealerid)
								->where('user_sales_order.company_id',$company_id)
								->where('person.company_id',$company_id)
								->where('location_7.id',$locationid)
								->where('person.id',$salespersonid)
								->where('retailer.id',$retailerid)
								->whereIn('person.id',$junior_data_check)
								->groupBy('product_id')
								->get();
		}else{
		$query_data_final_layer = DB::table('dealer')
								->join('user_sales_order','user_sales_order.dealer_id','=','dealer.id') 
								->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id') 
								->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id') 
								->join('retailer','retailer.id','=','user_sales_order.retailer_id') 
								->join('location_7','location_7.id','=','user_sales_order.location_id') 
								->join('person','person.id','=','user_sales_order.user_id')
								->join('person_login','person_login.person_id','=','person.id')
								->select(DB::raw("SUM(user_sales_order_details.weight) as weight"),'catalog_product.id as prod_id',DB::raw("SUM(final_secondary_qty) as quantity"),'catalog_product.name as prod_name',DB::raw("SUM(final_secondary_rate*final_secondary_qty) as sale"))
								->where('person_status',1)
								->where('dealer_status',1)
								->where('retailer_status',1)
								->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$fromdate' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$todate'")
								->where('dealer.id',$dealerid)
								->where('user_sales_order.company_id',$company_id)
								->where('person.company_id',$company_id)
								->where('location_7.id',$locationid)
								->where('person.id',$salespersonid)
								->where('retailer.id',$retailerid)
								->whereIn('person.id',$junior_data_check)
								->groupBy('product_id')
								->get();
		}


		$final_product_data = array();
		foreach ($query_data_final_layer as $key => $value) 
		{

			$value_percent = !empty($product_percentage_data[$value->prod_id])?$product_percentage_data[$value->prod_id]:'0';

			$subAmt = $value->sale*($value_percent/100);

			$finalAmt = $value->sale-$subAmt;


			$data['weight'] = !empty($value->weight)?$value->weight:'';
			$data['prod_id'] = !empty($value->prod_id)?$value->prod_id:'';
			$data['quantity'] = !empty($value->quantity)?$value->quantity:'';
			$data['prod_name'] = !empty($value->prod_name)?$value->prod_name:'';
			$data['sale'] = !empty($value->sale)?$value->sale:'';
			$data['final_value'] = !empty($finalAmt)?$finalAmt:'0';
			$final_product_data[] = $data;
		}






		if(COUNT($final_product_data)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$final_product_data]);

		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$final_product_data]);
		}

	}

	public function retailer_stock_report(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'company_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		$user_id = $request->user_id;
		$company_id = $request->company_id;
		$from_date = $request->from_date;
		$to_date = $request->to_date;

		$check_junior_data=JuniorData::getJuniorUser($user_id,$company_id);
        Session::push('juniordata', $user_id);
		$junior_data_check = Session::get('juniordata');

		$user_array = DB::table('retailer_stock')
					->join('person','person.id','=','retailer_stock.user_id')
					->join('person_login','person_login.person_id','=','person.id')
					->join('_role','_role.role_id','=','person.role_id')
					->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'user_id','rolename')
					->where('person_status',1)
					->where('retailer_stock.company_id',$company_id)
					->whereIn('retailer_stock.user_id',$junior_data_check)
					->where('person.company_id',$company_id)
					->groupBy('user_id')
					->get();
		$second_layer = DB::table('retailer_stock')
					->join('dealer','dealer.id','=','retailer_stock.dealer_id')
					->join('retailer','retailer.id','=','retailer_stock.retailer_id')
					->join('location_view','location_view.l7_id','=','retailer_stock.location_id')
					->select('retailer_stock.date as date','retailer.name as retailer_name','dealer.name as dealer_name','user_id','dealer.id as dealer_id','retailer_id','order_id','l7_name as beat','l7_id as beat_id')
					->whereRaw("DATE_FORMAT(retailer_stock.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(retailer_stock.date,'%Y-%m-%d')<='$to_date'")
					->where('retailer_stock.company_id',$company_id)
					->whereIn('retailer_stock.user_id',$junior_data_check)
					->where('retailer_status',1)
					->where('dealer_status',1)
					->groupBy('retailer_id','retailer_stock.date','dealer_id','l7_id')
					->get();

		$third_layer = DB::table('retailer_stock')
					->join('retailer_stock_details','retailer_stock_details.order_id','=','retailer_stock.order_id')
					->join('catalog_product','catalog_product.id','=','retailer_stock_details.product_id')
					->select('catalog_product.name as product_name','product_id','user_id','dealer_id','retailer_id','retailer_stock.order_id as order_id','quantity','catalog_product.base_price_per as mrp','catalog_product.base_price as base_price',DB::raw("(catalog_product.base_price_per*retailer_stock_details.quantity) as total_sale_value"))
					->whereRaw("DATE_FORMAT(retailer_stock.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(retailer_stock.date,'%Y-%m-%d')<='$to_date'")
					->where('retailer_stock.company_id',$company_id)
					->whereIn('retailer_stock.user_id',$junior_data_check)
					->groupBy('product_id','retailer_id','dealer_id','location_id','retailer_stock_details.order_id')
					->get();
		$final_third_layer = array();
		foreach ($third_layer as $key => $value) 
		{
		 	$out['product_name']= $value->product_name; 
            $out['product_id']= $value->product_id; 
            $out['user_id']= $value->user_id; 
            $out['dealer_id']= $value->dealer_id; 
            $out['retailer_id']= $value->retailer_id;
            $out['order_id']= $value->order_id; 
            $out['quantity']= !empty($value->quantity)?$value->quantity:''; 
            $out['mrp']= !empty($value->mrp)?$value->mrp:'';
            $out['base_price']= !empty($value->base_price)?$value->base_price:'';
            $out['total_sale_value']= !empty($value->total_sale_value)?$value->total_sale_value:''; 
            $final_third_layer[] = $out;
		}

		if(COUNT($second_layer)>0 && COUNT($user_array)>0  && COUNT($third_layer)>0)
		{
			return response()->json([ 'response' =>True,'first_layer'=>$user_array,'second_layer'=>$second_layer,'third_layer'=>$final_third_layer]);

		}
		else
		{
			return response()->json([ 'response' =>False,'first_layer'=>array(),'second_layer'=>array(),'third_layer'=>array()]);
		}
	}

	public function retailer_comment_report(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'company_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		$company_id = $request->company_id;
		$user_id = $request->user_id;
		$from_date = $request->from_date;
		$to_date = $request->to_date;

		$check_junior_data=JuniorData::getJuniorUser($user_id,$company_id);
        Session::push('juniordata', $user_id);
		$junior_data_check = Session::get('juniordata');

		$first_layer = DB::table('retailer_comment')
					 ->join('person','person.id','=','retailer_comment.user_id')
					 ->join('person_login','person_login.person_id','=','person.id')
					 ->join('_role','_role.role_id','=','person.role_id')
					 ->select(DB::raw("CONCAT_WS(first_name,middle_name,last_name) as user_name"),'user_id','_role.rolename as designation')
					 ->where('retailer_comment.company_id',$company_id)
					 ->whereIn('user_id',$junior_data_check)
					 ->groupBy('user_id')
					 ->get();

	 	$second_layer = DB::table('retailer_comment')
                                ->join('retailer','retailer.id','=','retailer_comment.retailer_id')
                                ->join('person','person.id','=','retailer_comment.user_id')
                                ->join('dealer','dealer.id','=','retailer_comment.dealer_id')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('_role','_role.role_id','=','person.role_id')
                                ->join('location_3','location_3.id','=','person.state_id')
                                ->select(DB::raw("CONCAT_WS(first_name,middle_name,last_name) as user_name"), 'location_3.name as state','_role.rolename as designation','dealer.name as dealer_name','retailer.name as retailer_name','retailer_comment.*')
                                ->whereRaw("DATE_FORMAT(retailer_comment.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(retailer_comment.date,'%Y-%m-%d')<='$to_date'")
                                ->where('person_status',1)
                                ->where('retailer_status',1)
                                ->where('dealer_status',1)
                                ->whereIn('user_id',$junior_data_check)
                                ->groupBy('retailer.id','user_id','dealer_id','retailer_comment.date')
                                ->where('retailer_comment.company_id',$company_id)
                                ->get();
        if(COUNT($first_layer)>0 && COUNT($second_layer)>0)
		{
			return response()->json([ 'response' =>True,'first_layer'=>$first_layer,'second_layer'=>$second_layer]);

		}
		else
		{
			return response()->json([ 'response' =>False,'first_layer'=>array(),'second_layer'=>array()]);
		}
	}

	public function distributor_stock_report(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'company_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		$from_date = $request->from_date;
		$to_date = $request->to_date;
		$user_id = $request->user_id;
		$company_id = $request->company_id;

		$dealer_id = DB::table('dealer_location_rate_list')->where('company_id',$company_id)->where('user_id',$user_id)->pluck('dealer_id')->toArray();
		// dd($dealer_id);



		$distributor_stock_data = DB::table('dealer_balance_stock')
								->join('dealer','dealer.id','=','dealer_balance_stock.dealer_id')
								->join('catalog_product','catalog_product.id','=','dealer_balance_stock.product_id')
								->select('catalog_product.id as product_id','dealer.name as dealer_name','dealer_id','user_id','catalog_product.name as product_name','dealer_balance_stock.mrp as mrp','pcs_mrp','stock_qty','submit_date_time')
								->whereIn('dealer_balance_stock.dealer_id',$dealer_id)
								->where('dealer_balance_stock.company_id',$company_id)
								->whereRaw("DATE_FORMAT(submit_date_time,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(submit_date_time,'%Y-%m-%d')<='$to_date'")
								->get();
		// foreach ($distributor_stock_data as $key => $value) 
		// {
		// 	# code...
		// }
		// dd($distributor_stock_data);
		if(COUNT($distributor_stock_data)>0)
		{
			return response()->json([ 'response' =>True,'distributor_stock'=>$distributor_stock_data]);
		}
		else
		{
			return response()->json([ 'response' =>False,'distributor_stock'=>array()]);
		}
	}

	public function dms_dealer_retailer_beat_details(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'dealer_id'=>'required',
            'company_id'=>'required',
         
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		$company_id = $request->company_id;
		$dealer_id = $request->dealer_id;
		$beat_retailer_data = DB::table('dealer_location_rate_list')
							->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
							->join('retailer','retailer.location_id','=','dealer_location_rate_list.location_id')
							->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
							->select('dealer.name as dealer_name','retailer.name as retailer_name','location_7.name as beat_name','retailer.id as retailer_id','dealer_location_rate_list.dealer_id as dealer_id','dealer_location_rate_list.location_id as beat_id')
							->where('dealer_location_rate_list.dealer_id',$dealer_id)
							->where('dealer_location_rate_list.company_id',$company_id)
							->groupBy('retailer.id','dealer_location_rate_list.location_id','dealer.id')
							->get();

		$retailer_array = DB::table('dealer_location_rate_list')
						->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
						->join('retailer','retailer.location_id','=','dealer_location_rate_list.location_id')
						->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
						->select('retailer.name as retailer_name','retailer.id as retailer_id','retailer.location_id as beat_id')
						->where('dealer_location_rate_list.dealer_id',$dealer_id)
						->where('dealer_location_rate_list.company_id',$company_id)
						->groupBy('retailer.id')
						->get();

		$beat_array = DB::table('dealer_location_rate_list')
						->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
						->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
						->select('location_7.name as beat_name','location_7.id as beat_id')
						->where('dealer_location_rate_list.dealer_id',$dealer_id)
						->where('dealer_location_rate_list.company_id',$company_id)
						->groupBy('dealer_location_rate_list.location_id')
						->get();


		// foreach ($beat_retailer_data as $key => $value) 
		// {
			
		// }

		if(COUNT($beat_retailer_data)>0)
		{
			return response()->json([ 'response' =>True,'data'=>$beat_retailer_data ,'beat_array' => $beat_array , 'retailer_array'=>$retailer_array]);
		}
		else
		{
			return response()->json([ 'response' =>False,'data'=>array() ,'beat_array' => array() , 'retailer_array'=>array()]);
		}
	}

	public function pending_claim(Request $request)
	{
		$validator=Validator::make($request->all(),[
            // 'dealer_id'=>'required',
            'company_id'=>'required',
            'user_id'=> 'required',
            'from_date'=> 'required',
            'to_date'=> 'required',
         
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		$dealer_id = $request->dealer_id;
		$company_id = $request->company_id;
		$user_id = $request->user_id;
		$from_date = $request->from_date;
		$to_date = $request->to_date;

		$pending_claim_fetch_data = DB::table('pending_claim')
								->join('dealer','dealer.id','=','pending_claim.distributor_id')
								->join('person','person.id','=','pending_claim.user_id')
								->join('person_login','person_login.person_id','=','person.id')
								->join('_role','_role.role_id','=','person.role_id')
								->select('pending_claim.*','dealer.name as dealer_name',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'_role.rolename as designation')
								->whereRaw("DATE_FORMAT(submission_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(submission_date,'%Y-%m-%d')<='$to_date'")
								->where('user_id',$user_id)
								// ->where('distributor_id',$dealer_id)
								->where('pending_claim.company_id',$company_id)
								->where('person.company_id',$company_id)
								->where('dealer.company_id',$company_id)
								->where('person_status',1)
								->get();
		if(COUNT($pending_claim_fetch_data)>0)
		{
			return response()->json([ 'response' =>True,'data'=>$pending_claim_fetch_data]);
		}
		else
		{
			return response()->json([ 'response' =>False,'data'=>array() ]);
		}

	}
	public function metting_order_booking(Request $request)
	{
		$validator=Validator::make($request->all(),[
            // 'dealer_id'=>'required',
            'company_id'=>'required',
            'user_id'=> 'required',
            'from_date'=> 'required',
            'to_date'=> 'required',
         
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		$company_id =$request->company_id ; 
		$user_id =$request->user_id ; 
		$to_date =$request->to_date ; 
		$from_date =$request->from_date ; 

		$meeting_array = array('1'=>'Meeting with Dealer','2'=>'Meeting with Architect','3'=>'Meeting With Customer','4'=>'Meeting With Builder','5'=>'Office work');

		$meeting_array_id = array(1,2,3,4,5);

		// dd($meeting_array_id);
		
		$meeting_with_type = DB::table('meeting_order_booking')
							->select(DB::raw("COUNT(order_id) as count"),'meeting_order_booking.meeting_id as meeting_id')
							->where('meeting_order_booking.company_id',$company_id)
							->whereRaw("DATE_FORMAT(current_date_m,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(current_date_m,'%Y-%m-%d')<='$to_date'")
							->where('user_id',$user_id)
							->whereIn('meeting_id',$meeting_array_id)
							->groupBy('meeting_order_booking.meeting_id')
							->get();
			// dd($meeting_with_type);
		$final_out = array();
		foreach ($meeting_with_type as $key => $value) 
		{
			$out['id'] = $value->meeting_id;
			$out['metting_name'] = !empty($meeting_array[$value->meeting_id])?$meeting_array[$value->meeting_id]:'N/A';
			$out['metting_count'] = $value->count;
			$out['metting_order_booking'] = DB::table('meeting_order_booking')
											->join('_meeting_with_type','_meeting_with_type.id','=','meeting_order_booking.type_of_meet')
											->select('_meeting_with_type.name as type_of_meetname','meeting_order_booking.*',DB::raw("DATE_FORMAT(current_datetime,'%Y-%m-%d') as curr_date"),DB::raw("DATE_FORMAT(current_datetime,'%H:%i:%s') as curr_time"))
											->where('user_id',$user_id)
											->whereRaw("DATE_FORMAT(current_date_m,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(current_date_m,'%Y-%m-%d')<='$to_date'")
											->where('meeting_order_booking.company_id',$company_id)
											->where('_meeting_with_type.company_id',$company_id)
											->where('meeting_id',$value->meeting_id)
											->get();
			$final_out[] = $out;
		}
		if(COUNT($final_out)>0)
		{
			return response()->json([ 'response' =>True,'first_layer'=>$final_out]);
		}
		else
		{
			return response()->json([ 'response' =>False,'first_layer'=>array() ]);
		}

	}


	public function summary_report(Request $request)
	{
		$validator=Validator::make($request->all(),[
            // 'dealer_id'=>'required',
            'company_id'=>'required',
            'user_id'=> 'required',
            'from_date'=> 'required',
            'to_date'=> 'required',
         
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		$company_id =$request->company_id ; 
		$user_id =$request->user_id ; 
		$to_date =$request->to_date ; 
		$from_date =$request->from_date ; 


		  $date1 = strtotime($from_date);
          $date2 = strtotime($to_date); 

	        $diff = abs($date2 - $date1); 

	        $years = floor($diff / (365*60*60*24));  

	          $months = floor(($diff - $years * 365*60*60*24) 
                                               / (30*60*60*24));  

          $days = floor(($diff - $years * 365*60*60*24 -  
                             $months*30*60*60*24)/ (60*60*24)); //day difference


		################################# pjp object #################################################################
		$mtp_data_query = DB::table('monthly_tour_program')
						->select(DB::raw('COUNT(DISTINCT retailer.id) as total_outlet'))
						->join('user_sales_order','user_sales_order.location_id','=','monthly_tour_program.locations')
						->join('retailer','retailer.location_id','=','user_sales_order.location_id')
						->where('person_id',$request->user_id)
						->where('monthly_tour_program.company_id',$request->company_id)
						->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(working_date,'%Y-%m-%d')<='$to_date'")
						->get();

		$total_pjp_total_calls = DB::table('monthly_tour_program')
		                			  ->join('user_sales_order','user_sales_order.location_id','=','monthly_tour_program.locations')
						              ->where('monthly_tour_program.person_id',$request->user_id)
						              ->where('user_sales_order.user_id',$request->user_id)
				               		  ->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(working_date,'%Y-%m-%d')<='$to_date'")
				               		  ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
				               		  ->where('monthly_tour_program.company_id',$company_id)
				               		  ->count(DB::raw('user_sales_order.retailer_id'));				



		$total_pjp_productive_calls = DB::table('monthly_tour_program')
		                			  ->join('user_sales_order','user_sales_order.location_id','=','monthly_tour_program.locations')
						              ->where('user_id',$request->user_id)
						              ->where('person_id',$request->user_id)
				               		  ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
				               		  ->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(working_date,'%Y-%m-%d')<='$to_date'")
				               		  ->where('user_sales_order.company_id',$company_id)
				               		  ->where('call_status',1)
				               		  ->count(DB::raw('user_sales_order.retailer_id'));	

		$total_pjp_unique_total_calls = DB::table('monthly_tour_program')
		                			  ->join('user_sales_order','user_sales_order.location_id','=','monthly_tour_program.locations')
						              ->where('user_id',$request->user_id)
				               		  ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
				               		  ->where('user_sales_order.company_id',$company_id)
				               		  ->count(DB::raw('DISTINCT user_sales_order.retailer_id'));	

		$total_pjp_unique_productive_calls = DB::table('monthly_tour_program')
		                			  ->join('user_sales_order','user_sales_order.location_id','=','monthly_tour_program.locations')
						              ->where('user_id',$request->user_id)
				               		  ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
				               		  ->where('user_sales_order.company_id',$company_id)
				               		  ->where('call_status',1)
				               		  ->count(DB::raw('DISTINCT user_sales_order.retailer_id'));		

		$total_pjp_total_product_count = DB::table('monthly_tour_program')
		                			  ->join('user_sales_order','user_sales_order.location_id','=','monthly_tour_program.locations')
		                			  ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
						              ->where('monthly_tour_program.person_id',$request->user_id)
						              ->where('user_sales_order.user_id',$request->user_id)
				               		  ->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(working_date,'%Y-%m-%d')<='$to_date'")
				               		  ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
				               		  ->where('monthly_tour_program.company_id',$company_id)
				               		  ->count(DB::raw('user_sales_order_details.product_id'));			


		$total_pjp_total_product_count_unique = DB::table('monthly_tour_program')
		                			  ->join('user_sales_order','user_sales_order.location_id','=','monthly_tour_program.locations')
		                			  ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
						              ->where('monthly_tour_program.person_id',$request->user_id)
						              ->where('user_sales_order.user_id',$request->user_id)
				               		  ->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(working_date,'%Y-%m-%d')<='$to_date'")
				               		  ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
				               		  ->where('monthly_tour_program.company_id',$company_id)
				               		  ->count(DB::raw('DISTINCT user_sales_order_details.product_id'));	



			 foreach ($mtp_data_query as $key => $value) 
                    {
                    	$pjp_object['total_outlet'] = $value->total_outlet;
                    	$pjp_object['total_calls'] =  !empty($total_pjp_total_calls)?$total_pjp_total_calls:'0';
                    	$pjp_object['total_productive_calls'] = !empty($total_pjp_productive_calls)?$total_pjp_productive_calls:'0';
                    	$pjp_object['unique_total_calls'] = !empty($total_pjp_unique_total_calls)?$total_pjp_unique_total_calls:'0';
                    	$pjp_object['unique_productive_calls'] = !empty($total_pjp_unique_productive_calls)?$total_pjp_unique_productive_calls:'0';
                    }			
		################################# pjp object end #################################################################

		################################# outlet object #################################################################


                $new_outlet = DB::table('retailer')
					       ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(created_on,'%Y-%m-%d')<='$to_date'")
	                      ->where('retailer.company_id',$company_id)
	                      ->where('retailer.created_by_person_id',$user_id)
	                      ->count('retailer.id');

	            $sale_order_retailer = DB::table('user_sales_order')
	            						   ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
					                      ->where('user_sales_order.company_id',$company_id)
					                      ->where('user_sales_order.user_id',$user_id)
					                      ->pluck(DB::raw("DISTINCT retailer_id"));


				$not_visited_retailer = DB::table('retailer')	  
										->join('user_sales_order','user_sales_order.location_id','=','retailer.location_id')
										->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','retailer.location_id')
										->join('person','person.id','=','dealer_location_rate_list.user_id')
					                     ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
										  ->where('retailer.company_id',$company_id)
					                      ->where('user_sales_order.user_id',$user_id)
					                      ->whereNotIn('retailer.id',$sale_order_retailer)
					                      ->count('retailer.id');

			     $outlet_object['outlet_added'] = !empty($new_outlet)?$new_outlet:'0';
			     $outlet_object['not_visited_outlet'] = !empty($not_visited_retailer)?$not_visited_retailer:'0';


		################################# outlet object end #################################################################

		################################# productivity array #################################################################
			     if($total_pjp_total_calls == 0){
			     	$productivity['productivity'] = 0;
			     }
			     else{
			        $productivity['productivity'] = ($total_pjp_productive_calls/$total_pjp_total_calls)*100;
			     }

			     if($days == 0){
			     	$productivity['tlsd'] = 0;
			     }
			     else{
			     	$productivity['tlsd'] = $total_pjp_total_product_count_unique/$days ;
			     }

			      if($total_pjp_productive_calls == 0){
			     	$productivity['lpsc'] = 0;
			     }
			     else{
			     	$productivity['lpsc'] = $total_pjp_total_product_count/$total_pjp_productive_calls;
			     }


			     $total_non_productive = $total_pjp_total_calls-$total_pjp_productive_calls;
			     if($total_non_productive ==0)
			     {
			     	$productivity['dropsize'] = 0;
			     }
			     else{
			        $productivity['dropsize'] = ($total_non_productive/$total_pjp_total_calls)*100;

			     }

			    


		################################# productivity array end #################################################################

		################################# productivity based on unique array #################################################################

			     if($total_pjp_unique_total_calls == 0){
			     	$productivity_unique['productivity'] = 0;
			     }
			     else{
			        $productivity_unique['productivity'] = ($total_pjp_unique_productive_calls/$total_pjp_unique_total_calls)*100;
			     }

			     if($total_pjp_unique_productive_calls == 0){
			     	$productivity_unique['lpsc'] = 0;
			     }
			     else{
			     	$productivity_unique['lpsc'] = $total_pjp_total_product_count/$total_pjp_unique_productive_calls;
			     }

			      $total_non_productive_unique = $total_pjp_unique_total_calls-$total_pjp_unique_productive_calls;
			     if($total_non_productive_unique ==0)
			     {
			     	$productivity_unique['dropsize'] = 0;
			     }
			     else{
			        $productivity_unique['dropsize'] = ($total_non_productive_unique/$total_pjp_unique_total_calls)*100;

			     }

		################################# productivity based on unique array end #################################################################



		################################# sku wise array #################################################################
			     $sku_sales = DB::table('user_sales_order')
			     			  ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
			     			  ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
			     			  ->select('catalog_product.name as product_name',DB::raw("SUM(case_rate) as case_rate"),DB::raw("SUM(quantity) as quantity"),DB::raw("SUM((case_rate*user_sales_order_details.case_qty)+(rate*quantity)) as sale") )
			     			   ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
							  ->where('user_sales_order.company_id',$company_id)
		                      ->where('user_sales_order.user_id',$user_id)
		                      ->groupBy('product_id')
		                      ->get()->toArray();		
		################################# sku wise array end #################################################################


                  ########################### non pjp #####################################################################
			                  $non_mtp_data_query = DB::table('monthly_tour_program')
											->join('user_sales_order','user_sales_order.location_id','=','monthly_tour_program.locations')
											->join('retailer','retailer.location_id','=','user_sales_order.location_id')
											->where('person_id',$request->user_id)
											->where('monthly_tour_program.company_id',$request->company_id)
											->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(working_date,'%Y-%m-%d')<='$to_date'")
											->groupBy('working_date')
											->pluck(DB::raw("group_concat(distinct locations) as locations"),'working_date');

								foreach ($non_mtp_data_query as $nkey => $nvalue) 
				                    {
				                    	$non_pjp_total_call = DB::table('user_sales_order')
				                    						   ->where('user_sales_order.user_id',$request->user_id)
										               		  ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$nkey'")
										               		  ->where('user_sales_order.company_id',$company_id)
										               		  ->whereNotIn('location_id',[$nvalue])
										               		  ->count(DB::raw('user_sales_order.retailer_id'));	
										$non_pjp_total_call_array[] = $non_pjp_total_call;     
										
										$non_pjp_productive_call = DB::table('user_sales_order')
				                    						   ->where('user_sales_order.user_id',$request->user_id)
										               		  ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$nkey'")
										               		  ->where('user_sales_order.company_id',$company_id)
										               		  ->where('call_status',1)
										               		  ->whereNotIn('location_id',[$nvalue])
										               		  ->count(DB::raw('user_sales_order.retailer_id'));	 
										$non_pjp_productive_call_array[] = $non_pjp_productive_call;  

										$non_pjp_total_unique_call = DB::table('user_sales_order')
				                    						   ->where('user_sales_order.user_id',$request->user_id)
										               		  ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$nkey'")
										               		  ->where('user_sales_order.company_id',$company_id)
										               		  ->whereNotIn('location_id',[$nvalue])
										               		  ->count(DB::raw('DISTINCT user_sales_order.retailer_id'));	
										$non_pjp_total_unique_call_array[] = $non_pjp_total_unique_call;  

										$non_pjp_unique_productive_call = DB::table('user_sales_order')
				                    						   ->where('user_sales_order.user_id',$request->user_id)
										               		  ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$nkey'")
										               		  ->where('user_sales_order.company_id',$company_id)
										               		  ->where('call_status',1)
										               		  ->whereNotIn('location_id',[$nvalue])
										               		  ->count(DB::raw('DISTINCT user_sales_order.retailer_id'));	 
										$non_pjp_unique_productive_call_array[] = $non_pjp_unique_productive_call;      
										               		           		  				

				                    }

				                    $non_pjp_object['total_call'] = !empty(array_sum($non_pjp_total_call_array))?array_sum($non_pjp_total_call_array):'0';
				                    $non_pjp_object['productive_call'] = !empty(array_sum($non_pjp_productive_call_array))?array_sum($non_pjp_productive_call_array):'0';
				                    $non_pjp_object['unique_total_call'] = !empty(array_sum($non_pjp_total_call_array))?array_sum($non_pjp_total_unique_call_array):'0';
				                    $non_pjp_object['unique_productive_call'] = !empty(array_sum($non_pjp_unique_productive_call_array))?array_sum($non_pjp_unique_productive_call_array):'0';

                  ########################### non pjp end #####################################################################


						if(!empty($mtp_data_query))
						{
							return response()->json([ 'response' =>True,'message'=>'Summary Data','pjp'=>$pjp_object,'non_pjp'=>$non_pjp_object,'outlet_object'=>$outlet_object,'productivity_object'=>$productivity,'productivity_unique'=>$productivity_unique,'sku_detail'=>$sku_sales]);
						}
						else
						{
							return response()->json([ 'response' =>True,'message'=>'Summary Data','pjp'=>$pjp_object,'non_pjp'=>$non_pjp_object,'outlet_object'=>$outlet_object,'productivity_object'=>$productivity,'productivity_unique'=>$productivity_unique,'sku_detail'=>$sku_sales]);
						}









	}
	public function push_sku_layer_detail_janak(Request $request)
	{
		$company_id = 50;
		$data = array();
		$weight_type = array();
		$unit_type = array();
		$data = DB::table('catalog_2')
			->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
			->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
			->select('catalog_2.id as third_layer_id','catalog_2.name as third_layer_name','catalog_1.id as second_layer_id','catalog_1.name as second_layer_name','catalog_0.id as first_layer_id','catalog_0.name as first_layer_name')
			->where('catalog_2.status',1)
			->where('catalog_1.status',1)
			->where('catalog_0.status',1)
			->where('catalog_2.company_id',$company_id)
			->where('catalog_1.company_id',$company_id)
			->where('catalog_0.company_id',$company_id)
			->groupBy('catalog_2.id')
			->get();
		
		$weight_type = DB::table('weight_type')
					->select('type as name','id')
					->where('company_id',$company_id)
					->where('status',1)
					->get();

		$unit_type = DB::table('product_type')
					->select('name','id')
					->where('status',1)
					->where('company_id',$company_id)
					->where('flag_neha',2)
					->get();

		return response()->json([ 'response' =>True,'message'=>'Data Found','data'=>$data,'weight_type'=>$weight_type,'unit_type'=>$unit_type]);


	}
	public function ecart_primary_order_details_api(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'dealer_id'=>'required',
            'company_id'=>'required',
            'from_date'=> 'required',
            'to_date'=> 'required',
         
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}

		$from_date  = $request->from_date;
		$to_date  = $request->to_date;
		$dealer_id  = $request->dealer_id;
		$company_id  = $request->company_id;
		$first_layer = DB::table('user_sales_order')
					->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
					->join('person','person.id','=','user_sales_order.user_id')
					->join('person_details','person_details.person_id','=','person.id')
					->select('user_sales_order.date as date','person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'mobile','person_details.address as address',DB::raw("SUM((rate*quantity)) as sale_valu"),DB::raw("SUM(quantity) as quantity"))
					->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
					->where('user_sales_order.company_id',$request->company_id)
					->where('user_sales_order.dealer_id',$request->dealer_id)
					->groupBy('person.id','user_sales_order.date')
					->get();

		$second_layer = DB::table('user_sales_order')
					->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
					->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
					->join('person','person.id','=','user_sales_order.user_id')
					->join('person_details','person_details.person_id','=','person.id')
					->select('user_sales_order.date as date','catalog_product.name as product_name','person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'mobile','person_details.address as address',DB::raw("SUM((rate*quantity)) as sale_valu"),DB::raw("SUM(quantity) as quantity"))
					->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
					->where('user_sales_order.company_id',$company_id)
					->where('user_sales_order.dealer_id',$request->dealer_id)
					->groupBy('person.id','product_id','user_sales_order.date')
					->get(); 

		return response()->json([ 'response' =>True,'message'=>'Data Found','first_layer'=>$first_layer,'second_layer'=>$second_layer]);


	}
	public function get_machine_data(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'token'=>'required',
            'emp_id'=>'required',
            'in_time'=> 'required',
            'out_time'=> 'required',
            'mask_status'=> 'required',
            'temprature'=> 'required',
            'date'=> 'required',
         
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		$user_id = User::where('remember_token',$request->token)->first(); // bijxbjn8sekErjAUuHRMl2SgmJEWdZ34C14ZKgKCd0x2PCotdXcWlNVQzMUP
		if(empty($user_id))
		{
			return response()->json([ 'response' =>False,'message'=>'Token Mismatched !!'],500);
		}
		$myArr = [

			'token'=>$request->token,
            'emp_id'=>$request->emp_id,
            'in_time'=>$request->in_time,
            'out_time'=>$request->out_time,
            'mask_status'=>$request->mask_status,
            'temprature'=>$request->temprature,
            'date'=>$request->date,
            'company_id'=>56,
            'created_by'=> $user_id->id,
            'created_at'=> date('Y-m-d H:i:s'),
		];
		 $insert_data = DB::table('machine_attendance_data')->insert($myArr);
		 if($insert_data)
		 {
			return response()->json([ 'response' =>True,'message'=>'Data Inserted Sucessfully !!'],200);
		 }
	}

	// ////////////////////////////////////////// for oyster ///////////////////////////////////////////

	public function user_daily_attendance_report_oyster(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		Session::forget('juniordata');		
        $check_junior_data=JuniorData::getJuniorUser($request->user_id,$request->company_id);
        Session::push('juniordata', $request->user_id);

		$junior_data_check = Session::get('juniordata');
		
		// dd($junior_data_check);

	
        
        $attendance_query = DB::table('person')
        				->join('person_login','person_login.person_id','=','person.id')
    					->join('user_daily_attendance','user_daily_attendance.user_id','=','person.id')
    					->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
    					->select('person.id as id','person.head_quar as hq',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') as work_date"),'_working_status.name as working_status',DB::raw("DATE_FORMAT(user_daily_attendance.work_date,'%H:%i:%s') as checkin_time"),'user_daily_attendance.remarks as remarks')
    					->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$request->from_date' AND DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$request->to_date'")
    					->whereIn('person.id',$junior_data_check)
    					->where('person.company_id',$request->company_id)
    					->where('user_daily_attendance.company_id',$request->company_id)
    					->where('person_status',1)
    					->get();
		$final_data = array();
		foreach ($attendance_query as $key => $value) 
		{
			$check_out_query = DB::table('check_out')
						->select(DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as check_out_time"))
						->where('company_id',$request->company_id)
						->whereRaw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d')>='$value->work_date'")
						->where('user_id',$value->id)
						->first();
			$data['checkout_time'] = !empty($check_out_query->check_out_time)?$check_out_query->check_out_time:'';
			$data['id'] = "$value->id";
			$data['hq'] = $value->hq;
			$data['checkin_time'] = $value->checkin_time;
			$data['hq'] = $value->hq;
			$data['fullname'] = $value->user_name;
			$data['date'] = $value->work_date;
			$data['working_status'] = $value->working_status;
			$data['remarks'] = $value->remarks;
			$final_data[] = $data;

		}
		if(!empty($final_data))
		{
			return response()->json([ 'response' =>True,'result'=>$final_data]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$final_data]);
		}
    }



////////////////////////////////////////////////////// role wise expense details starts here ///////////////////////////////
public function role_wise_expense_details(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'role_id'=>'required',
            'company_id'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		$role_wise_expense_query = array();
		$role_wise_expense_query = DB::table('role_wise_assign')
						->select('TA','DA','telephone_expense')
						->where('role_wise_assign.company_id',$request->company_id)
						->where('role_wise_assign.role_id',$request->role_id)
						->where('role_wise_assign.flag_status',4)
						->get();
		
		if(COUNT($role_wise_expense_query)>0)
		{
			return response()->json([ 'response' =>True,'result'=>$role_wise_expense_query]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$role_wise_expense_query]);
		}
	}
/////////////////////////////////////////////////////// role wise expense details ends here ////////////////////////////////

public function user_tracking_details(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'date'=>'required',
            'company_id'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		
		$tracking_query = DB::table('user_work_tracking')
						->where('user_work_tracking.company_id',$request->company_id)
						->where('user_work_tracking.user_id',$request->user_id)
						->where('user_work_tracking.track_date',$request->date)
						->orderBy('track_time','ASC')
						->get();



		$out = array();
		$final_out = array();
		foreach ($tracking_query as $key => $value) 
		{
			if(!empty($value->lat_lng)){
			$explode = preg_split('/[\ \n\,]+/',$value->lat_lng);

			$lat = $explode[0];
			$lng = $explode[1];

			$out['user_id'] = $value->user_id;
			$out['lat'] = $lat;
			$out['lng'] = $lng;
			}

			$final_out[] = $out;
		}
		if(COUNT($final_out)>0)
		{
			return response()->json([ 'response' =>True,'tracking'=>$final_out]);
		}
		else
		{
			return response()->json([ 'response' =>False,'tracking'=>array() ]);
		}
	}


	// public function holiday_check(Request $request)
	// {
	// 	$validator=Validator::make($request->all(),[
 //            'user_id'=>'required',
 //            'date'=>'required',
 //            'company_id'=>'required',
          
 //        ]);

 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
	// 	}

	// 	$user_id = $request->user_id;
	// 	$date = $request->date;
	// 	$company_id = $request->company_id;

	// 	$check_holiday = DB::table('holiday')->where('status',1)->where('date',$date)->where('company_id',$company_id)->first();

	// 	$check_person_holiday_enabled = DB::table('person')->where('id',$user_id)->where('company_id',$company_id)->first();

	// 	// dd($check_holiday);

	// 	$time = date('H:i:s');


	// 	$day = date("l",strtotime($date));

	// 	if($check_holiday || ($day == 'Sunday'))  // condition check holiday or sunday
	// 	{
	// 		if(($check_person_holiday_enabled->is_holiday_enabled == '2')){			
	// 		return response()->json([ 'response' =>False]);
	// 		}
	// 		else{
	// 		return response()->json([ 'response' =>True]);
	// 		}
	// 	}
	// 	elseif(($check_person_holiday_enabled->is_holiday_enabled == '1')) // condition for chech after 10::00 AM condition
	// 	{
	// 		return response()->json([ 'response' =>True]);
	// 	}
	// 	elseif(($check_person_holiday_enabled->is_holiday_enabled == '2') && ($day != 'Sunday') && ($time <= '10:00:00')) // condition for chech after 10::00 AM condition
	// 	{
	// 		return response()->json([ 'response' =>True]);

	// 	}
	// 	else
	// 	{
	// 		return response()->json([ 'response' =>False]);
	// 	}
	// 	// elseif($check_person_holiday_enabled->is_holiday_enabled == '2')
	// 	// {
	// 	// 	return response()->json([ 'response' =>True]);
	// 	// }
		


	// }


	public function holiday_check(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'date'=>'required',
            'company_id'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}

		$user_id = $request->user_id;
		$date = $request->date;
		$company_id = $request->company_id;

		$check_holiday = DB::table('holiday')->where('status',1)->where('date',$date)->where('company_id',$company_id)->first();

		$check_person_holiday_enabled = DB::table('person')->where('id',$user_id)->where('company_id',$company_id)->first();

		// dd($check_holiday);

		$time = date('H:i:s');
		// $time = "09:59:59";
		// dd($time);

		$day = date("l",strtotime($date));

		
		if(($time > '10:30:00')){
			if(($check_person_holiday_enabled->is_holiday_enabled == '2')){			
			return response()->json([ 'response' =>False]);
			}
			else{
			return response()->json([ 'response' =>True]);
			}
		}
		// elseif(($time <= '10:00:00') || ($day == 'Sunday')){
		// 	return response()->json([ 'response' =>True]);
		// }
		elseif($check_holiday || ($day == 'Sunday'))  // condition check holiday or sunday
		{
			if(($check_person_holiday_enabled->is_holiday_enabled == '2')){			
			return response()->json([ 'response' =>False]);
			}
			else{
			return response()->json([ 'response' =>True]);
			}
		}
		elseif(($check_person_holiday_enabled->is_holiday_enabled == '1')) // condition for chech after 10::00 AM condition
		{
			return response()->json([ 'response' =>True]);
		}
		elseif(($check_person_holiday_enabled->is_holiday_enabled == '2') && ($day != 'Sunday') && ($time <= '10:30:00')) // condition for chech after 10::00 AM condition
		{
			return response()->json([ 'response' =>True]);
		}
		else
		{
			return response()->json([ 'response' =>False]);
		}
		// elseif($check_person_holiday_enabled->is_holiday_enabled == '2')
		// {
		// 	return response()->json([ 'response' =>True]);
		// }
		


	}

	public function janak_template_product_details(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'dealer_id'=>'required',
            'user_id'=>'required',
            'company_id'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		$dealer_id = $request->dealer_id;
		$user_id = $request->user_id;
		$company_id = $request->company_id;

		$product_array = DB::table('catalog_product')
                                    ->join('product_rate_list_template','product_rate_list_template.product_id','=','catalog_product.id')
                                    ->join('dealer','dealer.template_id','=','product_rate_list_template.template_type')
                                    ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                                    ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                                    ->select('state_id','csa_id as ss_id','dealer.id as distributor_id','product_type_primary','other_retailer_rate','other_dealer_rate','quantiy_per_other_type','product_type_id','retailer_rate as retailer_case_rate','catalog_product.product_type as product_type','catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','catalog_product.quantity_per_case as quantity_per_case','unit','catalog_product.name','product_rate_list_template.retailer_pcs_rate as base_price','product_rate_list_template.mrp_pcs as mrp', 'product_rate_list_template.mrp_pcs','hsn_code','catalog_2.name as cname', 'product_rate_list_template.dealer_rate as dealer_rate','product_rate_list_template.dealer_pcs_rate as dealer_pcs_rate')
                                    ->where('catalog_1.status',1)
                                    ->where('catalog_2.status',1)
                                    ->where('catalog_product.status',1)
                                    ->where('dealer.id',$dealer_id)
                                    ->where('catalog_product.company_id',$company_id)
                                    ->groupBy('product_rate_list_template.template_type','product_rate_list_template.product_id','dealer.id')
                                    ->orderBy('product_sequence','ASC')
                                    ->get()->toArray();

        $final_catalog_product_details = array();

        $focust_query = DB::table('focus_product_users_target')
                        ->select('target_value as target_qty')
                        ->where('company_id',$company_id)
                        ->where('user_id',$user_id)
                        ->whereRaw("DATE_FORMAT(start_date,'%Y-%m-%d')>='date(Y-m-d)' AND DATE_FORMAT(end_date,'%Y-%m-%d')<='date(Y-m-d)'")
                        ->groupBy('product_id')
                        ->pluck('target_value','product_id');

        $focus_query_new = DB::table('focus')
                        ->where('company_id',$company_id)
                        ->groupBy('product_id')
                        ->pluck('product_id','product_id');

        $querytax = DB::table('_gst')
                    ->select('igst as tax')
                    ->where('company_id',$company_id)
                    // ->where('hsn_code',$value->hsn_code)
                    ->groupBy('hsn_code')
                    ->pluck('igst','hsn_code');

        $product_type_new = DB::table('product_type')
                        ->where('status',1)
                        ->where('company_id',$company_id)
                        ->groupBy('id')
                        ->pluck('name','id');
        foreach ($product_array as $key => $value)
        {
            $focus_status = !empty($focus_query_new[$value->id])?1:0;

            if($value->mrp==0 && $value->mrp_pcs==0 && $value->dealer_rate==0 && $value->dealer_pcs_rate==0 && $value->base_price==0 && $value->retailer_case_rate==0 && $value->other_retailer_rate==0 && $value->other_dealer_rate==0 )
            {
                $product_message = 'requirement';
            }
            else
            {

                $productArray['id'] = "$value->id";
                $productArray['dealer_id'] = "$value->distributor_id";
                $productArray['ss_id'] = "$value->ss_id";
                $productArray['state_id'] = "$value->state_id";
                $productArray['classification_id'] = "$value->classification_id";
                $productArray['classification_name'] = $value->classification_name;
                $productArray['category'] = "$value->catalog_id";
                $productArray['hsn_code'] = $value->hsn_code;
                $productArray['category_name'] = $value->cname;
                $productArray['name'] = $value->name;
                $productArray['weight'] = $value->weight;
                $productArray['base_price'] = $value->base_price;
                $productArray['case_base_price'] = $value->retailer_case_rate;
                $productArray['dealer_rate'] = $value->dealer_rate;
                $productArray['dealer_pcs_rate'] = $value->dealer_pcs_rate;
                $productArray['mrp'] = $value->mrp;
                $productArray['pcs_mrp'] = $value->mrp_pcs;
                $productArray['unit'] = !empty($value->unit)?$value->unit:'';
                $productArray['quantity_per_case'] = !empty($value->quantity_per_case)?$value->quantity_per_case:'';
                $productArray['quantity_per_other'] = !empty($value->quantiy_per_other_type)?$value->quantiy_per_other_type:'';
                $productArray['sku_product_type_id_primary'] = !empty($value->product_type_primary)?$value->product_type_primary:'';
                $productArray['sku_product_type_name_primary'] = !empty($product_type_new[$value->product_type_primary])?$product_type_new[$value->product_type_primary]:'';
                $productArray['sku_product_type_id'] = !empty($value->product_type)?$value->product_type:'';
                $productArray['sku_product_type_name'] = !empty($product_type_new[$value->product_type])?$product_type_new[$value->product_type]:'';
                $productArray['product_type_id_rate_list'] = !empty($value->product_type_id)?$value->product_type_id:'';
                $productArray['product_type_name_rate_list'] = !empty($product_type_new[$value->product_type_id])?$product_type_new[$value->product_type_id]:'';
                $productArray['other_retailer_type_rate'] = !empty($value->other_retailer_rate)?$value->other_retailer_rate:'0.00';
                $productArray['other_dealer_type_rate'] = !empty($value->other_dealer_rate)?$value->other_dealer_rate:'0.00';
                $productArray['focus'] = "$focus_status";
                $productArray['focus_target'] = !empty($focust_query[$value->id])?$focust_query[$value->id]:'';
                $productArray['tax'] = !empty($querytax[$value->hsn_code])?$querytax[$value->hsn_code]:'';
                $final_catalog_product_details[] = $productArray;
            }


        }
		return response()->json([ 'response' =>True,'data'=>$final_catalog_product_details]);

	}




	/////////////////////////////////////////////////////////////////////////////////////////////


  	public function working_status(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'user_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $user_id = $request->user_id;
        $company_id = $request->company_id;
     	Session::forget('juniordata');
        $user_data=JuniorData::getJuniorUser($user_id,$company_id);
        // dd($user_data);
        // $junior_data = [];
        $junior_data = Session::get('juniordata');
        // dd($junior_data);
        Session::forget('seniorData');
           $fetch_senior_id = JuniorData::getSeniorUser($user_id,$company_id);
           $senior_data = Session::get('seniorData');
           // $senior_data = [];
           // print_r($senior_data); exit;
        $out = array();
        $custom = 1;
        // dd('1223');
        if(!empty($senior_data) && !empty($junior_data))
        {
            $juniors_name = Person::select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id')
                             ->where('company_id',$company_id)
                             ->whereIn('id',$junior_data)
                             ->get();

            $serniors_name = Person::select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id')
                             ->where('company_id',$company_id)
                             ->whereIn('id',$senior_data)
                             ->get();
            // dd($juniors_name);

             $out=[0=>['id'=>'0','name'=>'SELF']];
            
            foreach($serniors_name as $s_key => $s_value)
            {
                $out[$custom]['id'] = $s_value->id;
                $out[$custom]['name'] = $s_value->user_name;
                $custom++;
            }
            foreach ($juniors_name as $key => $value)
            {
                $out[$custom]['id'] = $value->id;
                $out[$custom]['name'] = $value->user_name;
                $custom++;
            }
        }
        elseif(!empty($senior_data))
        {
            $serniors_name = Person::select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id')
                             ->where('company_id',$company_id)
                             ->whereIn('id',$senior_data)
                             ->get();
            // dd($juniors_name);

             $out=[0=>['id'=>'0','name'=>'SELF']];
            
            foreach($serniors_name as $s_key => $s_value)
            {
                $out[$custom]['id'] = $s_value->id;
                $out[$custom]['name'] = $s_value->user_name;
                $custom++;
            }
        }
        elseif(!empty($junior_data))
        {
            $juniors_name = Person::select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id')
                             ->where('company_id',$company_id)
                             ->whereIn('id',$junior_data)
                             ->get();
            // dd($juniors_name);

             $out=[0=>['id'=>'0','name'=>'SELF']];
            
            foreach ($juniors_name as $key => $value)
            {
                $out[$custom]['id'] = $value->id;
                $out[$custom]['name'] = $value->user_name;
                $custom++;
            }
        }
        else
        {
            $junior_data[]=$user_id;
             $out=[0=>['id'=>'0','name'=>'SELF']];
        }
        
         
        $collegueArr = $out;
        
    	$work_status_query = DB::table('_working_status')
						->select('id','name')
						->where('_working_status.company_id',$request->company_id)
						->where('status',1)
						->get();
						if(!empty($work_status_query))
						{
							return response()->json([ 'response' =>True,'data'=>$work_status_query,'collegueArr'=>$collegueArr]);
						}
						else
						{
							return response()->json([ 'response' =>False,'data'=>$work_status_query,'collegueArr'=>$collegueArr]);
						}

    }


      public function daily_schedule(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        
    	$work_status_query = DB::table('_daily_schedule')
						->select('id','name')
						->where('_daily_schedule.company_id',$request->company_id)
						->where('status',1)
						->get();
						if(!empty($work_status_query))
						{
							return response()->json([ 'response' =>True,'data'=>$work_status_query]);
						}
						else
						{
							return response()->json([ 'response' =>False,'data'=>$work_status_query]);
						}

    }



     public function availableStock(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            // 'dealer_id'=>'required',
            'company_id'=>'required',
            'retailer_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $dealer_id = $request->dealer_id;
        $company_id = $request->company_id;
        $retailer_id = $request->retailer_id;


        $checkOpeningStock = DB::table('dealer_balance_stock')
        					->where('mantra_retailer_id',$retailer_id)
        					->where('dealer_balance_stock.company_id',$company_id)
        					->get()->toArray();

        					// dd($checkOpeningStock);


        $openingStatus = !empty($checkOpeningStock)?'Available':'Not Available';


          $dealer_data = DB::table('mantra_stock')
                            ->join('retailer','retailer.id','=','mantra_stock.mantra_retailer_id')
                            ->join('catalog_product','catalog_product.id','=','mantra_stock.product_id')
                            ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                            ->select('retailer.id as retailer_id','retailer.name as retailer_name','catalog_product.name as product_name','qty as quantity','rate as rate',DB::raw("(rate*qty) as total_stock"),'catalog_product.id as product_id','catalog_2.id as category_id')
                            ->where('mantra_stock.company_id',$company_id)
                            ->where('retailer.company_id',$company_id)
                            ->where('catalog_product.company_id',$company_id)
                            ->where('mantra_stock.mantra_retailer_id',$retailer_id)
                            ->groupBy('mantra_stock.mantra_retailer_id','mantra_stock.product_id')
                            ->get();


        
    
						if(!empty($dealer_data))
						{
							return response()->json([ 'response' =>True,'data'=>$dealer_data,'openingStatus'=>$openingStatus]);
						}
						else
						{
							return response()->json([ 'response' =>False,'data'=>array(),'openingStatus'=>$openingStatus]);
						}

    }


       public function outletTypeAndCategory(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $company_id = $request->company_id;

        $outletType = DB::table('_retailer_outlet_type')
        			->select('id','outlet_type as name')
        			->where('company_id',$company_id)
        			->where('status',1)
        			->get();

        $outletCategory = DB::table('_retailer_outlet_category')
        				->select('id','outlet_category as name')
        				->where('company_id',$company_id)
        				->where('status',1)
        				->get();	
        
    	return response()->json([ 'response' =>TRUE,'outletType'=>$outletType, 'outletCategory'=>$outletCategory]);

    }
    public function dsr_report_new(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'user_id'=>'required',
            'date'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $company_id = $request->company_id;
		$user_id = $request->user_id;
		$from_date = $request->date;
		$to_date = $request->date;

        $data_sale = DB::table('user_sales_order')
        			->select(DB::raw("COUNT(call_status) as tc"),'user_id')
        			->where('company_id',$company_id)
        			->where('user_id',$user_id)
        			->whereRaw("date_format(date,'%Y-%m-%d')>='$from_date' and date_format(date,'%Y-%m-%d')<='$to_date'")
        			->groupBy('user_id')
        			->get();

		$data_sale_pc = DB::table('user_sales_order')
        			// ->select()
        			->where('company_id',$company_id)
        			->where('user_id',$user_id)
        			->where('call_status',1)
        			->whereRaw("date_format(date,'%Y-%m-%d')>='$from_date' and date_format(date,'%Y-%m-%d')<='$to_date'")
        			->groupBy('user_id')
        			->pluck(DB::raw("COUNT(call_status) as pc"),'user_id');

		$data_sale_sale_value = DB::table('user_sales_order')
					->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        			->where('user_sales_order.company_id',$company_id)
        			->where('user_id',$user_id)
        			// ->where('call_status')
        			->whereRaw("date_format(date,'%Y-%m-%d')>='$from_date' and date_format(date,'%Y-%m-%d')<='$to_date'")
        			->groupBy('user_id')
        			->pluck(DB::raw("sum(rate*quantity) as sale_value"),'user_id');


		$retailer_count = DB::table('retailer')
						->where('company_id',$company_id)
        				->whereRaw("date_format(created_on,'%Y-%m-%d')>='$from_date' and date_format(created_on,'%Y-%m-%d')<='$to_date'")
						->groupBy('created_by_person_id')
						->pluck(DB::raw("COUNT(retailer.id) as retailer_id"),'created_by_person_id');

		$f_out = array();
		foreach ($data_sale as $key => $value) {
			$out['tc'] = $value->tc;
			$out['pc'] = !empty($data_sale_pc[$value->user_id])?$data_sale_pc[$value->user_id]:'0';
			$out['sale_value'] = !empty($data_sale_sale_value[$value->user_id])?round($data_sale_sale_value[$value->user_id],2):'0';
			$out['retailer_count'] = !empty($retailer_count[$value->user_id])?$retailer_count[$value->user_id]:'0';
			$f_out[] = $out;
		}
		if(empty($f_out))
		{
    		return response()->json([ 'response' =>FALSE,'final_data'=>$f_out]);
		}
    	return response()->json([ 'response' =>TRUE,'final_data'=>$f_out]);


    }

    public function list_aeris(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $company_id = $request->company_id;
		$user_id = $request->user_id;
		$from_date = $request->from_date;
		$to_date = $request->to_date;

        $data_sale = DB::table('customer_order_form_aeris')
        			->join('catalog_product','catalog_product.id','=','customer_order_form_aeris.product_id')
        			->join('person','person.id','=','customer_order_form_aeris.created_by')
        			->select('customer_order_form_aeris.*','catalog_product.name as product_name',DB::raw("CONCAT(first_name,' ',middle_name,' ',last_name,'/',customer_name) as customer_name"))
        			// ->where('company_id',$company_id)
        			->where('customer_order_form_aeris.created_by',$user_id)
        			->whereRaw("date_format(customer_order_form_aeris.created_at,'%Y-%m-%d')>='$from_date' and date_format(customer_order_form_aeris.created_at,'%Y-%m-%d')<='$to_date'")
        			// ->groupBy('customer_order_form_aeris.created_by')
        			->get();

        			    	return response()->json([ 'response' =>TRUE,'final_data'=>$data_sale]);

    }
    public function attendance_module_status(Request $request)
    {
    	$cur_date = date('Y-m-d');
    	$data_attendance_submit = DB::table('user_daily_attendance')
    							->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')= '$cur_date'")
    							->where('user_id',$request->user_id)
    							->groupBy('user_id')
    							->get();

		if(COUNT($data_attendance_submit)>0)
		{
	    	return response()->json([ 'response' =>TRUE,'message'=>'']);
		}
		else
		{
	    	return response()->json([ 'response' =>FALSE,'message'=>'Mark Your Attendance First .']);
		}
    }



     public function juniorTrackDetails(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'user_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $company_id = $request->company_id;
		$user_id = $request->user_id;
		$curr_date = date('Y-m-d');

		$checkIsAdmin = DB::table('users')
						->where('id',$user_id)
						->where('company_id',$company_id)
						->first();

		if($checkIsAdmin->is_admin == '1'){
			$junior_data_check = '';
		}else{

			Session::forget('juniordata');		
	        $check_junior_data=JuniorData::getJuniorUser($user_id,$company_id);
	        Session::push('juniordata', $user_id);

			$junior_data_check = Session::get('juniordata');
		}




		$juniorTrackDataDetails = DB::table('user_work_tracking')
							->join('person','person.id','=','user_work_tracking.user_id')
							->join('person_login','person_login.person_id','=','person.id')
							->join('_role','_role.role_id','=','person.role_id')
							->select('user_id',DB::raw("CONCAT(first_name,' ',middle_name,' ',last_name) as user_name"),'person_login.person_image','person.mobile','_role.rolename')
							->where('user_work_tracking.company_id',$company_id);
							if(!empty($junior_data_check)){
								$juniorTrackDataDetails->whereIn('user_work_tracking.user_id',$junior_data_check);
							}

		$juniorTrackData = $juniorTrackDataDetails->whereRaw("date_format(user_work_tracking.track_date,'%Y-%m-%d')='$curr_date'")
							->groupBy('user_work_tracking.user_id')
		 					->get()->toArray();
		$final_array = array();
		foreach ($juniorTrackData as $jtdkey => $jtdvalue) {

			$user_name = !empty($jtdvalue->user_name)?$jtdvalue->user_name:'';
			$role_name = !empty($jtdvalue->rolename)?$jtdvalue->rolename:'';
			$mobile_no = !empty($jtdvalue->mobile)?$jtdvalue->mobile:'';

			$out['user_id'] = $jtdvalue->user_id;
			$out['user_name'] = $user_name.'/'.$role_name.'  | '.$mobile_no;
			$out['mobile'] = !empty($jtdvalue->mobile)?$jtdvalue->mobile:'';

			$cordinatesDetails = DB::table('user_work_tracking')
						->where('user_id',$jtdvalue->user_id)
						->whereRaw("date_format(user_work_tracking.track_date,'%Y-%m-%d')='$curr_date'")
						->where('lat_lng','!=','NULL')
						->where('lat_lng','!=','')
						->where('lat_lng','!=','0,0')
						->orderBy('id','DESC')
						->first();

			$lat_lng = !empty($cordinatesDetails->lat_lng)?$cordinatesDetails->lat_lng:'';
			if(!empty($lat_lng)){
				if(!empty($lat_lng)){
					$explode = preg_split('/(\s|&|,)/',$lat_lng);
					$lat = !empty($explode[0])?$explode[0]:'';
					$lng = !empty($explode[1])?$explode[1]:'';
				}
				else{
					$lat = "";
					$lng = "";
				}

				$out['lat'] = $lat;
				$out['lng'] = $lng;
				$out['track_time'] = $cordinatesDetails->track_time;

			      if($jtdvalue->person_image != NULL){
			      $out['profile_image'] = "users-profile/".$jtdvalue->person_image;
			      }else{
			      $out['profile_image'] = "msell/images/avatars/profile-pic.jpg";
			      }
			}

			$final_array[] = $out;
			
		}

		if(!empty($final_array)){
        	return response()->json([ 'response' =>TRUE,'trackDetails'=>$final_array]);

		}else{
        	return response()->json([ 'response' =>FALSE,'trackDetails'=>$final_array]);

		}



    }

    public function notification_update_msell(Request $request)
    {

    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'circular_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

    	$circular_id = $request->circular_id; 
    	$company_id = $request->company_id; 

    	$update_query = DB::table('circular')
    					->where('id',$circular_id)
    					->update([
							'status'=>'Read',
							'updated_at'=>date('Y-m-d H:i:s')
    					]);

		if($update_query){
			return response()->json([ 'response' =>TRUE,'message'=>'Update Successfully']);
		}
		else
		{
			return response()->json([ 'response' =>FALSE,'message'=>'Not Updated']);
		}

    }



     public function BtwDistanceDetails(Request $request)
    {

    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'user_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        if(!empty($request->date)){
        	$date = $request->date;
        }else{
        	$date = date('Y-m-d');
        }


    	$user_id = $request->user_id; 
    	$company_id = $request->company_id; 

    	$distanceQuery = DB::table('user_distance_tracking')
    					->select('user_distance_tracking.*',DB::raw('ROUND(user_distance_tracking.total_km,3) as total_km'))
    					->where('user_id',$user_id)
    					->where('company_id',$company_id)
						->whereRaw("date_format(user_distance_tracking.track_date,'%Y-%m-%d')='$date'")
    					->get();




		if($distanceQuery){
			return response()->json([ 'response' =>TRUE,'result'=>$distanceQuery]);
		}
		else
		{
			return response()->json([ 'response' =>FALSE,'result'=>array()]);
		}

    }




     public function pdfDetails(Request $request)
    {

    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'user_id'=>'required',
            // 'distributor_id'=>'required',
            // 'date'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
            'status'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        	$company_id = $request->company_id;
        	$user_id = $request->user_id;
        	// $distributor_id = $request->distributor_id;
        	// $date = $request->date;
        	$from_date = $request->from_date;
        	$to_date = $request->to_date;
        	$status = $request->status; // 1 for sale , 2 for purchase, 3 for stock 
      		
      		$personDetail = DB::table('person')
        				->select('person.id as person_id',DB::raw("CONCAT(first_name,' ',middle_name,' ',last_name) as user_name"),'person.mobile as mobile','state_id')
        				->where('company_id',$company_id)
        				->where('person.id',$user_id)
        				->first();

        	// $distributor = DB::table('dealer')
        	// 			->select('dealer.id as dealer_id','dealer.name as dealer_name','dealer.other_numbers as dealer_mobile','dealer.address','dealer.state_id')
        	// 			->where('company_id',$company_id)
        	// 			->where('dealer.id',$distributor_id)
        	// 			->first();

        	$stateId = $personDetail->state_id;

            $productMrp = DB::table('product_rate_list')
                            ->where('company_id',$company_id)
                            ->where('state_id',$stateId)
                            ->pluck('mrp_pcs','product_id');
        	/////////////////////////////////// retailer category sale details starts ///////////////////////////
        	$retailerCategoryWiseSales = DB::table('user_sales_order')
        								->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        								->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
        								->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
										->select('catalog_2.id as category_id','catalog_2.name as category_name','retailer_id as retailer_id')
										// ->where('user_sales_order.dealer_id',$distributor_id)
										->where('user_sales_order.user_id',$user_id)
										->where('user_sales_order.company_id',$company_id)
										->where('user_sales_order_details.company_id',$company_id)
										->where('catalog_product.company_id',$company_id)
										->where('catalog_2.company_id',$company_id)
										->whereRaw("date_format(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND date_format(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
										->groupBy('retailer_id','catalog_2.id')
										->get();


			$finalRetailerCategoryOut = array();
			foreach ($retailerCategoryWiseSales as $rcwkey => $rcwvalue) {
				$retailerCategoryOut[$rcwvalue->retailer_id][$rcwvalue->category_id]['retailer_id'] = $rcwvalue->retailer_id;
				$retailerCategoryOut[$rcwvalue->retailer_id][$rcwvalue->category_id]['category_id'] = $rcwvalue->category_id;
				$retailerCategoryOut[$rcwvalue->retailer_id][$rcwvalue->category_id]['category_name'] = $rcwvalue->category_name;

				$finalRetailerCategoryOut = $retailerCategoryOut;
			}

			// dd($finalRetailerCategoryOut);

        	///////////////////////////////////retailer category sale details ends ///////////////////////////



        	  	/////////////////////////////////// retailer category product sale details starts ///////////////////////////
        	$retailerCategoryProductWiseSales = DB::table('user_sales_order')
        								->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        								->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
        								->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
										->select('catalog_2.id as category_id','catalog_2.name as category_name','retailer_id as retailer_id','user_sales_order_details.product_id','catalog_product.name as product_name','user_sales_order_details.rate','user_sales_order_details.case_rate','user_sales_order_details.quantity','user_sales_order_details.case_quantity','user_sales_order_details.scheme_qty',DB::raw("((user_sales_order_details.rate*user_sales_order_details.quantity)+(user_sales_order_details.case_rate*user_sales_order_details.case_quantity)) as finalSale"))
										// ->where('user_sales_order.dealer_id',$distributor_id)
										->where('user_sales_order.user_id',$user_id)
										->where('user_sales_order.company_id',$company_id)
										->where('user_sales_order_details.company_id',$company_id)
										->where('catalog_product.company_id',$company_id)
										->where('catalog_2.company_id',$company_id)
										->whereRaw("date_format(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND date_format(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
										->groupBy('retailer_id','catalog_2.id','user_sales_order_details.product_id')
										->get();

			// dd($retailerCategoryProductWiseSales);

			$finalRetailerCategoryProductOut = array();
			foreach ($retailerCategoryProductWiseSales as $rcwpkey => $rcwpvalue) {

				$proMrp = !empty($productMrp[$rcwpvalue->product_id])?$productMrp[$rcwpvalue->product_id]:'0';


				$retailerCategoryProductOut[$rcwpvalue->retailer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['retailer_id'] = $rcwpvalue->retailer_id;
				$retailerCategoryProductOut[$rcwpvalue->retailer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['category_id'] = $rcwpvalue->category_id;
				$retailerCategoryProductOut[$rcwpvalue->retailer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['category_name'] = $rcwpvalue->category_name;
				$retailerCategoryProductOut[$rcwpvalue->retailer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['product_id'] = $rcwpvalue->product_id;
				$retailerCategoryProductOut[$rcwpvalue->retailer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['product_name'] = $rcwpvalue->product_name;
				$retailerCategoryProductOut[$rcwpvalue->retailer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['rate'] = $rcwpvalue->rate;
				$retailerCategoryProductOut[$rcwpvalue->retailer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['case_rate'] = $rcwpvalue->case_rate;
				$retailerCategoryProductOut[$rcwpvalue->retailer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['mrp'] = $proMrp;
				$retailerCategoryProductOut[$rcwpvalue->retailer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['quantity'] = $rcwpvalue->quantity;
				$retailerCategoryProductOut[$rcwpvalue->retailer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['case_quantity'] = $rcwpvalue->case_quantity;
				$retailerCategoryProductOut[$rcwpvalue->retailer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['scheme_quantity'] = $rcwpvalue->scheme_qty;
				$retailerCategoryProductOut[$rcwpvalue->retailer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['finalSale'] = $rcwpvalue->finalSale;

				$finalRetailerCategoryProductOut = $retailerCategoryProductOut;
			}

        	///////////////////////////////////retailer category product sale details ends ///////////////////////////

			// dd($finalRetailerCategoryProductOut);



    		$retailerCategoryProductSaleDetails = DB::table('user_sales_order')
    											->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
    											->join('retailer','retailer.id','=','user_sales_order.retailer_id')
    											->join('dealer','dealer.id','=','retailer.dealer_id')
    											->select('retailer.name as retailer_name','retailer.id as retailer_id','retailer.landline','retailer.track_address','user_sales_order.order_id','dealer.name as dealer_name')
    											// ->where('user_sales_order.dealer_id',$distributor_id)
    											->where('user_sales_order.user_id',$user_id)
    											->where('user_sales_order.company_id',$company_id)
												->where('user_sales_order_details.company_id',$company_id)
												->whereRaw("date_format(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND date_format(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
    											->groupBy('retailer_id')
    											->get();
    		// dd($retailerCategoryProductSaleDetails);

    		$retailerOut = array();
    		foreach ($retailerCategoryProductSaleDetails as $rcpkey => $rcpvalue) {
    				
    			$retailerId = $rcpvalue->retailer_id;
    			$retailer_name = $rcpvalue->retailer_name;
    			$dealer_name = $rcpvalue->dealer_name;
    			$landline = $rcpvalue->landline;
    			$track_address = $rcpvalue->track_address;	

    			$retailerOut[$retailerId]['retailer_id'] = $retailerId;
    			$retailerOut[$retailerId]['retailer_name'] = $retailer_name;
    			$retailerOut[$retailerId]['dealer_name'] = $dealer_name;
    			$retailerOut[$retailerId]['landline'] = $landline;
    			$retailerOut[$retailerId]['track_address'] = $track_address;

    			// $retailerOut[$retailerId]['catalogDetails'] = !empty($finalRetailerCategoryOut[$retailerId])?$finalRetailerCategoryOut[$retailerId]:array();

    			$categoryDetailsArray = !empty($finalRetailerCategoryOut[$retailerId])?$finalRetailerCategoryOut[$retailerId]:array();

    			$categoryDetailsArrayFinal = array();
    			$categoryDetailOut = array(); // added when error received
    			foreach ($categoryDetailsArray as $cdakey => $cdavalue) {
    				$categoryDetailOut[$cdavalue['category_id']]['category_id'] = $cdavalue['category_id'];
    				$categoryDetailOut[$cdavalue['category_id']]['category_name'] = $cdavalue['category_name'];

    				$categoryDetailOut[$cdavalue['category_id']]['productDetails'] = !empty($finalRetailerCategoryProductOut[$retailerId][$cdavalue['category_id']])?$finalRetailerCategoryProductOut[$retailerId][$cdavalue['category_id']]:array();

    				$categoryDetailsArrayFinal = $categoryDetailOut;


    			}

    			if(!empty($categoryDetailsArrayFinal)){
    				$retailerOut[$retailerId]['order_id'] = $rcpvalue->order_id;
    			}else{
    				$retailerOut[$retailerId]['order_id'] = '';
    			}

    			$retailerOut[$retailerId]['catalogDetails'] = (object)$categoryDetailsArrayFinal;

    		}


        				// dd($retailerOut);



		if(!empty($retailerOut)){
			return response()->json([ 'response' =>TRUE,'personDetail'=>$personDetail,'retailerDetails'=>$retailerOut]);
		}
		else
		{
			return response()->json([ 'response' =>FALSE,'personDetail'=>$personDetail,'retailerDetails'=>array()]);
		}

    }





     public function pdfPrimaryDetails(Request $request)
    {

    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'user_id'=>'required',
            'distributor_id'=>'required',
            'date'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        	$company_id = $request->company_id;
        	$user_id = $request->user_id;
        	$distributor_id = $request->distributor_id;
        	$date = $request->date;
      

        	$distributor = DB::table('dealer')
        				->select('dealer.id as dealer_id','dealer.name as dealer_name','dealer.other_numbers as dealer_mobile','dealer.address')
        				->where('company_id',$company_id)
        				->where('dealer.id',$distributor_id)
        				->first();
        	/////////////////////////////////// retailer category sale details starts ///////////////////////////
        	$retailerCategoryWiseSales = DB::table('user_primary_sales_order')
        								->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
        								->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
        								->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
										->select('catalog_2.id as category_id','catalog_2.name as category_name','dealer_id as dealer_id')
										->where('user_primary_sales_order.dealer_id',$distributor_id)
										->where('user_primary_sales_order.company_id',$company_id)
										->where('user_primary_sales_order_details.company_id',$company_id)
										->where('catalog_product.company_id',$company_id)
										->where('catalog_2.company_id',$company_id)
										->whereRaw("date_format(user_primary_sales_order.sale_date,'%Y-%m-%d')='$date'")
										->groupBy('dealer_id','catalog_2.id')
										->get();

			$finalRetailerCategoryOut = array();
			foreach ($retailerCategoryWiseSales as $rcwkey => $rcwvalue) {
				$retailerCategoryOut[$rcwvalue->dealer_id][$rcwvalue->category_id]['dealer_id'] = $rcwvalue->dealer_id;
				$retailerCategoryOut[$rcwvalue->dealer_id][$rcwvalue->category_id]['category_id'] = $rcwvalue->category_id;
				$retailerCategoryOut[$rcwvalue->dealer_id][$rcwvalue->category_id]['category_name'] = $rcwvalue->category_name;

				$finalRetailerCategoryOut = $retailerCategoryOut;
			}

        	///////////////////////////////////retailer category sale details ends ///////////////////////////

			// dd($finalRetailerCategoryOut);


        	  	/////////////////////////////////// retailer category product sale details starts ///////////////////////////
        	$retailerCategoryProductWiseSales = DB::table('user_primary_sales_order')
        								->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
        								->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
        								->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
										->select('catalog_2.id as category_id','catalog_2.name as category_name','dealer_id as dealer_id','user_primary_sales_order_details.product_id','catalog_product.name as product_name','user_primary_sales_order_details.rate','user_primary_sales_order_details.pr_rate as case_rate','user_primary_sales_order_details.pcs as quantity','user_primary_sales_order_details.cases as case_quantity',DB::raw("((user_primary_sales_order_details.rate*user_primary_sales_order_details.pcs)+(user_primary_sales_order_details.pr_rate*user_primary_sales_order_details.cases)) as finalSale"))
										->where('user_primary_sales_order.dealer_id',$distributor_id)
										->where('user_primary_sales_order.company_id',$company_id)
										->where('user_primary_sales_order_details.company_id',$company_id)
										->where('catalog_product.company_id',$company_id)
										->where('catalog_2.company_id',$company_id)
										->whereRaw("date_format(user_primary_sales_order.sale_date,'%Y-%m-%d')='$date'")
										->groupBy('dealer_id','catalog_2.id','user_primary_sales_order_details.product_id')
										->get();

			$finalRetailerCategoryProductOut = array();
			foreach ($retailerCategoryProductWiseSales as $rcwpkey => $rcwpvalue) {
				$retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['dealer_id'] = $rcwpvalue->dealer_id;
				$retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['category_id'] = $rcwpvalue->category_id;
				$retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['category_name'] = $rcwpvalue->category_name;
				$retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['product_id'] = $rcwpvalue->product_id;
				$retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['product_name'] = $rcwpvalue->product_name;
				$retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['rate'] = $rcwpvalue->rate;
				$retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['case_rate'] = $rcwpvalue->case_rate;
				$retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['quantity'] = $rcwpvalue->quantity;
				$retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['case_quantity'] = $rcwpvalue->case_quantity;
				$retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['finalSale'] = $rcwpvalue->finalSale;

				$finalRetailerCategoryProductOut = $retailerCategoryProductOut;
			}

        	///////////////////////////////////retailer category product sale details ends ///////////////////////////

			// dd($finalRetailerCategoryProductOut);



    		$retailerCategoryProductSaleDetails = DB::table('user_primary_sales_order')
    											->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
    											->join('dealer','dealer.id','=','user_primary_sales_order.dealer_id')
    											->select('dealer.name as dealer_name','dealer.id as dealer_id','dealer.other_numbers as landline','dealer.address as track_address')
    											->where('user_primary_sales_order.dealer_id',$distributor_id)
    											->where('user_primary_sales_order.company_id',$company_id)
												->where('user_primary_sales_order_details.company_id',$company_id)
												->whereRaw("date_format(user_primary_sales_order.sale_date,'%Y-%m-%d')='$date'")
    											->groupBy('dealer_id')
    											->get();

    		$retailerOut = array();
    		foreach ($retailerCategoryProductSaleDetails as $rcpkey => $rcpvalue) {
    				
    			$dealerId = $rcpvalue->dealer_id;
    			$dealer_name = $rcpvalue->dealer_name;
    			$landline = $rcpvalue->landline;
    			$track_address = $rcpvalue->track_address;	

    			$retailerOut[$dealerId]['dealer_id'] = $dealerId;
    			$retailerOut[$dealerId]['dealer_name'] = $dealer_name;
    			$retailerOut[$dealerId]['landline'] = $landline;
    			$retailerOut[$dealerId]['track_address'] = $track_address;

    			// $retailerOut[$dealerId]['catalogDetails'] = !empty($finalRetailerCategoryOut[$dealerId])?$finalRetailerCategoryOut[$dealerId]:array();

    			$categoryDetailsArray = !empty($finalRetailerCategoryOut[$dealerId])?$finalRetailerCategoryOut[$dealerId]:array();

    			$categoryDetailsArrayFinal = array();
    			foreach ($categoryDetailsArray as $cdakey => $cdavalue) {
    				$categoryDetailOut[$cdavalue['category_id']]['category_id'] = $cdavalue['category_id'];
    				$categoryDetailOut[$cdavalue['category_id']]['category_name'] = $cdavalue['category_name'];

    				$categoryDetailOut[$cdavalue['category_id']]['productDetails'] = !empty($finalRetailerCategoryProductOut[$dealerId][$cdavalue['category_id']])?$finalRetailerCategoryProductOut[$dealerId][$cdavalue['category_id']]:array();

    				$categoryDetailsArrayFinal = $categoryDetailOut;


    			}

    			$retailerOut[$dealerId]['catalogDetails'] = $categoryDetailsArrayFinal;

    		}


        				// dd($retailerOut);



		if(!empty($retailerOut)){
			return response()->json([ 'response' =>TRUE,'dealerDetails'=>$distributor,'finalDetails'=>$retailerOut]);
		}
		else
		{
			return response()->json([ 'response' =>FALSE,'dealerDetails'=>array(),'finalDetails'=>array()]);
		}

    }




     public function checkOutWithDsr(Request $request)
    {

    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'user_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $date = date('Y-m-d');
    	$user_id = $request->user_id; 
    	$company_id = $request->company_id; 


    	$userData = DB::table('person')
    				->select('person.id as user_id','role_id')
    				->where('company_id',$company_id)
    				->where('person.id',$user_id)
    				->first();


    	$totalCall = DB::table('user_sales_order')
    				->select(DB::raw("COUNT(DISTINCT retailer_id) as totalCall"))
    				->where('company_id',$company_id)
    				->where('user_id',$user_id)
					->whereRaw("date_format(user_sales_order.date,'%Y-%m-%d')='$date'")
					->first();


		$productiveCall = DB::table('user_sales_order')
					->select(DB::raw("COUNT(DISTINCT retailer_id) as productiveCall"))
    				->where('company_id',$company_id)
    				->where('user_id',$user_id)
    				->where('call_status','=','1')
					->whereRaw("date_format(user_sales_order.date,'%Y-%m-%d')='$date'")
					->first();



		$totalSale = DB::table('user_sales_order')
					->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
					->select(DB::raw("ROUND(SUM((user_sales_order_details.rate*user_sales_order_details.quantity)+(user_sales_order_details.case_rate*user_sales_order_details.case_quantity)),2) as totalSale"))
    				->where('user_sales_order.company_id',$company_id)
    				->where('user_sales_order.user_id',$user_id)
    				->where('call_status','=','1')
					->whereRaw("date_format(user_sales_order.date,'%Y-%m-%d')='$date'")
					->first();


		$role_id = $userData->role_id;

		if($company_id == 43){ // for BTW
				if($role_id == 145 || $role_id == 148 || $role_id == 168){

					$targetDetails =  DB::table('daily_reporting')
										->select('secondary_target')
										->where('company_id',$company_id)
										->where('user_id',$user_id)
										->first();

					$finaTarget = !empty($targetDetails->secondary_target)?$targetDetails->secondary_target:'0';
				}else{
					$finaTarget = '12500';
				}
		}else{

			$targetDetails = DB::table('monthly_tour_program')
							->select('rd as secondary_target')
							->where('company_id',$company_id)
							->where('person_id',$user_id)
							->first();

			$finaTarget = !empty($targetDetails->secondary_target)?$targetDetails->secondary_target:'0';

		}







		$outDsr['totalCall'] = !empty($totalCall->totalCall)?$totalCall->totalCall:'0';
		$outDsr['productiveCall'] = !empty($productiveCall->productiveCall)?$productiveCall->productiveCall:'0';
		$outDsr['totalSecondarySale'] = !empty($totalSale->totalSale)?$totalSale->totalSale:'0';
		$outDsr['totalSecondaryTarget'] = !empty($finaTarget)?$finaTarget:'0';

		$finanlDsr = $outDsr;







		if(!empty($finanlDsr)){
			return response()->json([ 'response' =>TRUE,'result'=>$finanlDsr]);
		}
		else
		{
			return response()->json([ 'response' =>FALSE,'result'=>array()]);
		}

    }




      public function juniorDetailsData(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $user_id = $request->user_id;
        $company_id = $request->company_id;
        $curr_date = date('Y-m-d');

        Session::forget('juniordata');      
        $check_junior_data=JuniorData::getJuniorUser($user_id,$company_id);
        Session::push('juniordata', $user_id);

        $junior_data_check = Session::get('juniordata');

        // dd($junior_data_check);

        $attendanceData = DB::table('user_daily_attendance')
                        ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$curr_date'")
                        ->whereIn('user_id',$junior_data_check)
                        ->where('company_id',$company_id)
                        ->groupBy('user_id')
                        ->pluck('user_id','user_id')->toArray();


        $saleData = DB::table('user_sales_order')
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$curr_date'")
                        ->whereIn('user_id',$junior_data_check)
                        ->where('company_id',$company_id)
                        ->groupBy('user_id')
                        ->pluck('user_id','user_id')->toArray();



        $juniorDetails = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->select('person.id as user_id',DB::raw("CONCAT(first_name,' ',middle_name,' ',last_name) as user_name"),'rolename','person.mobile','person.version_code_name','person_login.person_image')
                        ->where('person_status','=','1')
                        ->whereIn('person.id',$junior_data_check)
                        ->where('person.company_id',$company_id)
                        ->groupBy('person.id')
                        ->get()->toArray();




    
        $final_array = array();
        foreach ($juniorDetails as $jtdkey => $jtdvalue) {

            $userId = !empty($jtdvalue->user_id)?$jtdvalue->user_id:'';
            $user_name = !empty($jtdvalue->user_name)?$jtdvalue->user_name:'';
            $role_name = !empty($jtdvalue->rolename)?$jtdvalue->rolename:'';
            $mobile_no = !empty($jtdvalue->mobile)?$jtdvalue->mobile:'';

            if(!empty($jtdvalue->version_code_name)){
                $version = str_replace('Version:','',$jtdvalue->version_code_name);
            }else{
                $version = "";
            }

            // $version = !empty($jtdvalue->version_code_name)?$jtdvalue->version_code_name:'';


            $out['user_id'] = $userId;
            $out['user_name'] = $user_name;
            $out['role_name'] = $role_name;
            $out['version'] = $version;
            $out['mobile'] = !empty($jtdvalue->mobile)?$jtdvalue->mobile:'';
            $out['att_status'] = !empty($attendanceData[$userId])?"YES":"NO";
            $out['sale_status'] = !empty($saleData[$userId])?"YES":"NO";
            $out['seniorColor'] = "";
            $out['juniorColor'] = "";


              if($jtdvalue->person_image != NULL){
              $out['profile_image'] = "users-profile/".$jtdvalue->person_image;
              }else{
              $out['profile_image'] = "msell/images/avatars/profile-pic.jpg";
              }

            $final_array[] = $out;
            
        }

        if(!empty($final_array)){
            return response()->json([ 'response' =>TRUE,'trackDetails'=>$final_array]);

        }else{
            return response()->json([ 'response' =>FALSE,'trackDetails'=>array()]);

        }

    }


    public function sendMessageToJunior(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'senderId'=>'required',
            'RecieverId'=>'required',
            // 'content'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $senderId = $request->senderId; // senior id who sends message
        $RecieverId = $request->RecieverId; // junior id who receive message from senior
        $company_id = $request->company_id; 
        $curr_date = date('Y-m-d');
        $category = 'notifi';
        $dateTime  = date('Y-m-d H:i:s');

        if ($request->hasFile('image_source')) 
		{
            $image = $request->file('image_source');
         	$str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
    		$random_no = substr($str, 0,2);  // return always a new string 
    		$custom_image_name = date('YmdHis').$random_no.$senderId;
            $imageName = $custom_image_name . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/chat_images/' . $imageName);

           

            Image::make($image)->save($destinationPath);

            $content = "/chat_images/".$imageName;
        }
	    else{
        $content = !empty($request->content)?$request->content:'NA'; // meesage to junior
	    }    







        $seniorName = DB::table('person')
                    ->where('id',$senderId)
                    ->where('company_id',$company_id)
                    ->select(DB::raw("CONCAT(first_name,' ',middle_name,' ',last_name) as seniorName"))
                    ->first();

        $subject = 'Message From '.$seniorName->seniorName;

        $circularInsertArray = [
            'company_id' => $company_id,
            'circular_type' => $category,
            'title' => $subject,
            'content' => $content,
            'issued_by_person_id' => $senderId,
            'issued_time' => $dateTime,
            'circular_for_persons' => $RecieverId,
            'image' => '',

        ];

        $insertCircular = DB::table('circular')->insert($circularInsertArray);

  




        $juniorDetails = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->select('person.id as user_id',DB::raw("CONCAT(first_name,' ',middle_name,' ',last_name) as user_name"),'rolename','person.mobile','person.version_code_name','person_login.person_image','fcm_token')
                        ->where('person_status','=','1')
                        ->where('person.id',$RecieverId)
                        ->where('person.company_id',$company_id)
                        ->groupBy('person.id')
                        ->get()->toArray();




    
        // $final_array = array();
        foreach ($juniorDetails as $jtdkey => $jtdvalue) {

            $fcm_token = $jtdvalue->fcm_token;

            $data = [
                        'msg' => $content,
                        'body' => $content,
                        'click_action' => 'fmcg.newmsale.MainActivity',
                        'title' => $subject,
                ];
            $notification = $this->sendNotification($fcm_token, $data); 
            
        }

        $Data = [
        	'sender_id' => $senderId,
        	'reciever_id' => $RecieverId,
        	'company_id' => $company_id,
        	'server_date_time' => date('Y-m-d H:i:s'),
        	'message' => $content,
        	'status' => 1,
        	'date' => $curr_date,
        	'time' => date('h:i:s')
        ];
      	$out = DB::table('chat_messages')->insert($Data);

        if(!empty($insertCircular)){
            return response()->json([ 'response' =>TRUE,'message'=>'Message Sent Successfully!!!']);

        }else{
            return response()->json([ 'response' =>FALSE,'message'=>'Message Not Sent!!!']);

        }

    }


    public function return_chat_Api(Request $request){

	   	$validator = Validator::make($request->all(),[
	       'sender_id' => 'required',
	       'receiver_id' => 'required',
	       'company_id' => 'required',
	       'user_id' => 'required',
	       // 'time' => 'required',

	  	]);

      	if($validator->fails())
     	{
          	return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
      	}

		$sender_id = $request->sender_id;
		$receiver_id = $request->receiver_id;
		$company_id = $request->company_id;
		$date = date("Y-m-d",strtotime($request->date));
		$time = date("H:i:s",strtotime($request->time));
        $page=5;
		// $paginate = $reciever_i

		$data = DB::table('chat_messages')
		          ->select('*')
		          ->where('sender_id',$sender_id)
		          ->where('reciever_id',$receiver_id)
		          ->where('company_id',$company_id)
		          // ->where('date',$date)
		          // ->where('time',$time)
		          ->where('status',1)
		          ->paginate($page);

	 
      // dd($data);

		$data1 = DB::table('chat_messages')
		          ->select('*')
		          ->where('sender_id',$receiver_id)
		          ->where('reciever_id',$sender_id)
		          ->where('company_id',$company_id)
		          // ->where('date',$date)
		          // ->where('time',$time)
		          ->where('status',1)
		          ->paginate($page);

	 

	                $final_out = [];
        foreach($data as $key => $value){

            $dateFormat = date("Y-m-d",strtotime($value->server_date_time));
            $timeFormat = date("H:i:s",strtotime($value->server_date_time));

            $out['sender_id'] = $value->sender_id;
            $out['reciever_id'] = $value->reciever_id;
            $out['company_id'] = $value->company_id;
            $out['date'] = date("d-M-Y",strtotime($value->date));
            $out['time'] = date("H:i:s",strtotime($value->time));
            $out['message'] = $value->message;
            $out['chatkey'] = '1'; // for sender message
            $out['date_time'] = $dateFormat.' '.$timeFormat;



            $final_out[] = $out;
                                    
      	}
      	// dd($final_out);
	 
      	$final_out_1 = [];
       	foreach($data1 as $key => $value){
       		 $dateFormat = date("Y-m-d",strtotime($value->server_date_time));
            $timeFormat = date("H:i:s",strtotime($value->server_date_time));

	        $out_1['sender_id'] = $value->sender_id;
	        $out_1['reciever_id'] = $value->reciever_id;
	        $out_1['company_id'] = $value->company_id;
	        $out_1['date'] = date("d-M-Y",strtotime($value->date));
	        $out_1['time'] = date("H:i:s",strtotime($value->time));
	        $out_1['message'] = $value->message;
            $out_1['chatkey'] = '2'; // for receiver message
            $out_1['date_time'] = $dateFormat.' '.$timeFormat;




	        $final_out_1[] = $out_1;
	                                
      	}



      	$finalChatMerger = array_merge($final_out,$final_out_1);

      	$price = array_column($finalChatMerger, 'date_time');

        array_multisort($price, SORT_ASC, $finalChatMerger);

        $total1 = $data1->total();
     	$total=$data->total();
     	$total = $total+$total1;

        $lastPage=$data->hasMorePages();

         if($data->currentPage()==1){
        $message="Total $total records found";
        }elseif (!$lastPage) {
           $message="No more records found";
        }else{
           $message="";
        }


      	$senior_user_list = DB::table('chat_messages')
      						->join('person','person.id','=','chat_messages.sender_id')
      						->join('person_login','person_login.person_id','=','person.id')
      						->join('_role','_role.role_id','=','person.role_id')
      						->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'rolename','person.id as sender_id')
      						->where('reciever_id',$request->user_id)
      						->groupBy('reciever_id')
      						->get();
	                  

		if(!empty($final_out) || !empty($final_out_1) || !empty($senior_user_list)){
			// return response()->json([ 'response' =>True,'message'=>'Success' ,'sender_data' => $final_out , 'receiver_data' => $final_out_1,'senior_user_list'=> $senior_user_list,'finalChatList' => $finalChatMerger]);
		
			 return response()->json(['response' => TRUE,'message'=>'Success' ,'sender_data' => $final_out , 'receiver_data' => $final_out_1,'senior_user_list'=> $senior_user_list,'finalChatList' => $finalChatMerger,
                'total_record' =>$total, 'per_page' => $data->perPage(),'nextPage'=>$data->currentPage()+1,
                'lastPage' => $data->lastPage(), 'hasMorePages' => $data->hasMorePages(),
            ]);

		}
		else{
			return response()->json([ 'response' =>false,'message'=>'Data not found']);
		}


	}

 	public function sendNotification($fcm_token,$data)
    {
      
            $url = "https://fcm.googleapis.com/fcm/send";
        $fields = array(
            'to' => $fcm_token,
            'notification' => $data,
            'data' => ['complaint_id' =>  'Test', 'notify_type' => 1], #1 for complaint notification
           

        );

        //   $headers = array(
        //     'Authorization: key=AAAAxjJqtKA:APA91bGHNnQHaNzwdPzOSV-G0EhtRb-AfdbfoYJVGNFG8vQyn2HLFjKUd9f34LfrYt9KeAR5L9FMK1tzNcOtbPUzTLbMuawzQLHAV_us3AOtJIxE21WBmc-qTETSdq-yUSpRu1nOs4sV',
        //     'Content-Type: application/json'
        // );
         $headers = array(
            'Authorization: key=AAAAb24dj8M:APA91bFX59Ir7r2ffohnlPPSK_TzxXny8yx8AKIUjaycsn-_tYtObjaSzmgj7IiOIwEmqdjTlRqZYm8KfVo-Y7BNKhzZJ2jPN-H-RaFwQKLFEK3rFf4U2vChR8hHPq-ckaiG2WSzi4rW',
            'Content-Type: application/json'
        );

      


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result === FALSE) {
            die('Curl Failed: ' . curl_error($ch));
        }
        // echo $result;die;
        return $result;

           
    }





      public function DistributorCatalogPdfDetails(Request $request)
    {

        $validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'user_id'=>'required',
            'distributor_id'=>'required',
            'date'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

            $company_id = $request->company_id;
            $user_id = $request->user_id;
            $distributor_id = $request->distributor_id;
            $date = $request->date;
            $status = $request->status; // 1 for sale , 2 for purchase, 3 for stock 
            
            $personDetail = DB::table('person')
                        ->select('person.id as person_id',DB::raw("CONCAT(first_name,' ',middle_name,' ',last_name) as user_name"),'person.mobile as mobile')
                        ->where('company_id',$company_id)
                        ->where('person.id',$user_id)
                        ->first();

            $distributor = DB::table('dealer')
                        ->select('dealer.id as dealer_id','dealer.name as dealer_name','dealer.other_numbers as dealer_mobile','dealer.address','dealer.state_id')
                        ->where('company_id',$company_id)
                        ->where('dealer.id',$distributor_id)
                        ->first();

            $stateId = $distributor->state_id;

            $productMrp = DB::table('product_rate_list')
                            ->where('company_id',$company_id)
                            ->where('state_id',$stateId)
                            ->pluck('mrp_pcs','product_id');
            /////////////////////////////////// retailer category sale details starts ///////////////////////////
            $retailerCategoryWiseSales = DB::table('user_sales_order')
                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                                        ->select('catalog_2.id as category_id','catalog_2.name as category_name','dealer_id as dealer_id')
                                        ->where('user_sales_order.dealer_id',$distributor_id)
                                        ->where('user_sales_order.user_id',$user_id)
                                        ->where('user_sales_order.company_id',$company_id)
                                        ->where('user_sales_order_details.company_id',$company_id)
                                        ->where('catalog_product.company_id',$company_id)
                                        ->where('catalog_2.company_id',$company_id)
                                        ->whereRaw("date_format(user_sales_order.date,'%Y-%m-%d')='$date'")
                                        ->groupBy('dealer_id','catalog_2.id')
                                        ->get();

            $finalRetailerCategoryOut = array();
            foreach ($retailerCategoryWiseSales as $rcwkey => $rcwvalue) {
                $retailerCategoryOut[$rcwvalue->dealer_id][$rcwvalue->category_id]['dealer_id'] = $rcwvalue->dealer_id;
                $retailerCategoryOut[$rcwvalue->dealer_id][$rcwvalue->category_id]['category_id'] = $rcwvalue->category_id;
                $retailerCategoryOut[$rcwvalue->dealer_id][$rcwvalue->category_id]['category_name'] = $rcwvalue->category_name;

                $finalRetailerCategoryOut = $retailerCategoryOut;
            }




            ///////////////////////////////////retailer category sale details ends ///////////////////////////



                /////////////////////////////////// retailer category product sale details starts ///////////////////////////
            $retailerCategoryProductWiseSales = DB::table('user_sales_order')
                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                                        ->select('catalog_2.id as category_id','catalog_2.name as category_name','dealer_id as dealer_id','user_sales_order_details.product_id','catalog_product.name as product_name','user_sales_order_details.rate','user_sales_order_details.rate as case_rate','user_sales_order_details.quantity','user_sales_order_details.case_qty','user_sales_order_details.scheme_qty',DB::raw("((user_sales_order_details.rate*user_sales_order_details.quantity)+(user_sales_order_details.rate*user_sales_order_details.case_qty)) as finalSale"))
                                        ->where('user_sales_order.dealer_id',$distributor_id)
                                        // ->where('user_sales_order.user_id',$user_id)
                                        ->where('user_sales_order.company_id',$company_id)
                                        ->where('user_sales_order_details.company_id',$company_id)
                                        ->where('catalog_product.company_id',$company_id)
                                        ->where('catalog_2.company_id',$company_id)
                                        ->whereRaw("date_format(user_sales_order.date,'%Y-%m-%d')='$date'")
                                        ->groupBy('dealer_id','catalog_2.id','user_sales_order_details.product_id')
                                        ->get();

            $finalRetailerCategoryProductOut = array();
            foreach ($retailerCategoryProductWiseSales as $rcwpkey => $rcwpvalue) {

                $proMrp = !empty($productMrp[$rcwpvalue->product_id])?$productMrp[$rcwpvalue->product_id]:'0';


                $retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['dealer_id'] = $rcwpvalue->dealer_id;
                $retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['category_id'] = $rcwpvalue->category_id;
                $retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['category_name'] = $rcwpvalue->category_name;
                $retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['product_id'] = $rcwpvalue->product_id;
                $retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['product_name'] = $rcwpvalue->product_name;
                $retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['rate'] = $rcwpvalue->rate;
                $retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['case_rate'] = $rcwpvalue->case_rate;
                $retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['mrp'] = $proMrp;
                $retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['quantity'] = $rcwpvalue->quantity;
                $retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['case_quantity'] = $rcwpvalue->case_qty;
                $retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['scheme_quantity'] = $rcwpvalue->scheme_qty;
                $retailerCategoryProductOut[$rcwpvalue->dealer_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['finalSale'] = $rcwpvalue->finalSale;

                $finalRetailerCategoryProductOut = $retailerCategoryProductOut;
            }

            ///////////////////////////////////retailer category product sale details ends ///////////////////////////

            // dd($finalRetailerCategoryProductOut);



            $retailerCategoryProductSaleDetails = DB::table('user_sales_order')
                                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                // ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                                                ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
                                                ->select('dealer.name as dealer_name','dealer.id as dealer_id','dealer.other_numbers as landline','dealer.address as track_address')
                                                ->where('user_sales_order.dealer_id',$distributor_id)
                                                ->where('user_sales_order.company_id',$company_id)
                                                ->where('user_sales_order_details.company_id',$company_id)
                                                ->whereRaw("date_format(user_sales_order.date,'%Y-%m-%d')='$date'")
                                                ->groupBy('retailer_id')
                                                ->get();

            $retailerOut = array();
            foreach ($retailerCategoryProductSaleDetails as $rcpkey => $rcpvalue) {
                    
                $retailerId = $rcpvalue->dealer_id;
                $retailer_name = $rcpvalue->dealer_name;
                $landline = $rcpvalue->landline;
                $track_address = $rcpvalue->track_address;  

                $retailerOut[$retailerId]['dealer_id'] = $retailerId;
                $retailerOut[$retailerId]['dealer_name'] = $retailer_name;
                $retailerOut[$retailerId]['landline'] = $landline;
                $retailerOut[$retailerId]['track_address'] = $track_address;

                // $retailerOut[$retailerId]['catalogDetails'] = !empty($finalRetailerCategoryOut[$retailerId])?$finalRetailerCategoryOut[$retailerId]:array();

                $categoryDetailsArray = !empty($finalRetailerCategoryOut[$retailerId])?$finalRetailerCategoryOut[$retailerId]:array();

                $categoryDetailsArrayFinal = array();
                foreach ($categoryDetailsArray as $cdakey => $cdavalue) {
                    $categoryDetailOut[$cdavalue['category_id']]['category_id'] = $cdavalue['category_id'];
                    $categoryDetailOut[$cdavalue['category_id']]['category_name'] = $cdavalue['category_name'];

                    $categoryDetailOut[$cdavalue['category_id']]['productDetails'] = !empty($finalRetailerCategoryProductOut[$retailerId][$cdavalue['category_id']])?$finalRetailerCategoryProductOut[$retailerId][$cdavalue['category_id']]:array();

                    $categoryDetailsArrayFinal = $categoryDetailOut;


                }

                $retailerOut[$retailerId]['catalogDetails'] = $categoryDetailsArrayFinal;

            }


                        // dd($retailerOut);



        if(!empty($retailerOut)){
            return response()->json([ 'response' =>TRUE,'personDetail'=>$personDetail,'dealerDetails'=>$distributor,'dealerWiseProductsDetails'=>$retailerOut]);
        }
        else
        {
            return response()->json([ 'response' =>FALSE,'personDetail'=>$personDetail,'dealerDetails'=>array(),'dealerWiseProductsDetails'=>array()]);
        }

    }




    public function juniorDashboard(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'month'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $from_date = $request->month.'-01';
        $to_date = date('Y-m-t',strtotime($request->month));

        $start = strtotime($from_date);
        $end = strtotime($to_date);

        $table_name = TableReturn::table_return($from_date,$to_date);

        $datearray = array();
        $datediff =  ($end - $start)/60/60/24;
        $datearray[] = $from_date;

        for($i=0 ; $i<$datediff;$i++)
        {
            $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
        }

        // dd($datearray);

        $check = DB::table('app_other_module_assign')->where('company_id',$request->company_id)->where('module_id',6)->first();

        if(empty($check)){    
        $totalSaleData = DB::table($table_name)
        					->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
    						// ->select('user_id as user_id',,DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as total_call"))
    						->where($table_name.'.company_id',$request->company_id)
    						->where('user_id',$request->user_id);

    	$totalSale = $totalSaleData->whereRaw("DATE_FORMAT(date,'%Y-%m')='$request->month'")
    						->groupBy('date')
    						->pluck(DB::raw("SUM(rate*quantity) as total_sale"),'date');


    	}else{

    	$totalSaleData = DB::table($table_name)
        					->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
    						->where($table_name.'.company_id',$request->company_id)
    						->where('user_id',$request->user_id);
    	$totalSale = $totalSaleData->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$request->date'")
    						->groupBy('date')
    						->pluck(DB::raw("SUM(final_secondary_rate*final_secondary_qty) as total_sale"),'date');

    	}

    	

    	$totalCallData = DB::table($table_name)
        					->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
    						->where($table_name.'.company_id',$request->company_id)
    						->where('user_id',$request->user_id)
    						->whereRaw("DATE_FORMAT(date,'%Y-%m')='$request->month'")
    						->groupBy('date')
    						->pluck(DB::raw("COUNT(DISTINCT ".$table_name.".retailer_id) as total_call"),'date');

    						// dd($totalCallData);

    	$totalTargetData = DB::table('monthly_tour_program')
    						->where('monthly_tour_program.company_id',$request->company_id)
    						->where('person_id',$request->user_id)
    						->whereRaw("DATE_FORMAT(working_date,'%Y-%m')='$request->month'")
    						->groupBy('working_date')
    						->pluck(DB::raw("SUM(rd) as total_sale"),'working_date');



		$final_data = array();
		if(COUNT($datearray)>0)
		{
			foreach ($datearray as $key => $value) 
			{

				$finalSale = !empty($totalSale[$value])?$totalSale[$value]:'0';
				$finalTotalCall = !empty($totalCallData[$value])?$totalCallData[$value]:'0';
				$finalTotalTarget = !empty($totalTargetData[$value])?$totalTargetData[$value]:'0';


				$data['date']= $value;
				$data['sale']= $finalSale;
				$data['total_call']= $finalTotalCall;
				$data['target']= $finalTotalTarget;
				$final_data[]=$data;
			}
			return response()->json([ 'response' =>True,'result'=>$final_data]);
		}
		else
		{
			return response()->json([ 'response' =>False,'result'=>$final_data]);
		}

		

    }






    public function msellNotificationAPI(Request $request)
    {
	  	// $time = date('H:i:s');
	  	// // dd($time);
	  	// if($time >= '10:00:00' && $time <= '19:00:00')
	  	// {
	  	// 	$company_id = array('37','69','52');

	   //      $authDetail = DB::table('users')
	   //      			->whereIn('company_id',$company_id)
	   //      			->where('is_admin','1')
	   //      			->first();

	   //      $issued_by_person_id = $authDetail->id;


	   //      $juniorDetails = DB::table('person')
	   //                      ->join('person_login','person_login.person_id','=','person.id')
	   //                      ->join('_role','_role.role_id','=','person.role_id')
	   //                      ->select('person.company_id as company_id','person.id as user_id',DB::raw("CONCAT(first_name,' ',middle_name,' ',last_name) as user_name"),'rolename','person.mobile','person.version_code_name','person_login.person_image','fcm_token')
	   //                      ->where('person_status','=','1')
	   //                      ->whereIn('person.company_id',$company_id)
	   //                      // ->where('person.id','=','2091')
	   //                      ->groupBy('person.id')
	   //                      ->get()->toArray();

	   //      // dd($juniorDetails);
	        


	   //      $content = '';
	   //      $subject = 'mSELL';

	   //      // $final_array = array();
	   //      foreach ($juniorDetails as $jtdkey => $jtdvalue) {

	   //          $fcm_token = $jtdvalue->fcm_token;
	   //          $user_id = $jtdvalue->user_id;

	   //          $data = [
	   //                      'msg' => $content,
	   //                      'body' => $content,
	   //                      // 'click_action' => 'sfa.solution.NotificationLayoutActivity',
	   //                      'title' => $subject,
	   //              ];
    //             if($jtdvalue->company_id == '52')
    //             {
	   //          	$notification = $this->sendNotificationCustom($fcm_token, $data); 

    //             }
    //             else
    //             {
	   //          	$notification = $this->sendNotificationMsell($fcm_token, $data); 
    //             }


	   //          $arr['circular_type'] = 'notifi';
				// $arr['title'] = $subject;
				// $arr['content'] = $content;
				// $arr['issued_by_person_id'] = $issued_by_person_id;
				// $arr['company_id'] = $jtdvalue->company_id;
				// $arr['issued_time'] = date('Y-m-d H:i:s');
				// $arr['circular_for_persons'] = $user_id;
				// $arr['firebase_response'] = $notification;
				// // dd($notification);
				// $circular_insert = Circular::create($arr);


	   //          // dd($notification);
	            
	   //      }

	        

	   //      if(!empty($notification)){
	   //          return response()->json([ 'response' =>TRUE,'message'=>'Message Sent Successfully!!!']);

	   //      }else{
	   //          return response()->json([ 'response' =>FALSE,'message'=>'Message Not Sent!!!']);

	   //      }

	  	// }     
        
    }




     public function sendNotificationMsell($fcm_token,$data)
    {
      
        $url = "https://fcm.googleapis.com/fcm/send";
        // $fields = array(
        //     'to' => $fcm_token,
        //     'notification' => $data,
        //     'data' => ['complaint_id' =>  'Test', 'notify_type' => 1], #1 for complaint notification
           

        // );

   		$extraNotificationData = ["message" => $data,"click_action" =>'sfa.solution.NotificationLayoutActivity'];
        

      	$fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to'        => $fcm_token, //single token
            'notification' => $data,
            'data' => $extraNotificationData
        ];

        //    $headers = array(
        //     'Authorization: key=AAAAYKeUdAk:APA91bEx9MFbQVpo_Aaql0M9ZXU33DE9zAnM-Q7G8PY8-kAig7lDvDdZYZaCyisp1dAoKlVttqBTa6nZfU5UkG5a09FE7O7WBnOhK18hbBGf_iUMWkx8rGzWKZhmhpW5RAhHzSSWFXHS',
        //     'Content-Type: application/json'
        // );
        $headers = array(
            'Authorization: key=AAAAcmM-bsendNotificationF0:APA91bEmNq9OEWySv8I8IfdTyeJZ8w18jWEiN5MyY2LDlKICOwy52kh921S6wNTZ9jGgSWTmtOySOPF_SyoJ0vkocJG24trvb-Fv2BtNhZO15MoRxymueZSKPcnYeMXBZdVVxQseDdiR',
            'Content-Type: application/json'
        );

		$data_string = json_encode($fcmNotification); 


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result === FALSE) {
            die('Curl Failed: ' . curl_error($ch));
        }
        // echo $result;die;
        return $result;
            // 'Authorization: key= ' . config('app.FCM_API_ACCESS_KEY'),

           
    }
    public function sendNotificationCustom($fcm_token,$data)
    {
      
        $url = "https://fcm.googleapis.com/fcm/send";
        // $fields = array(
        //     'to' => $fcm_token,
        //     'notification' => $data,
        //     'data' => ['complaint_id' =>  'Test', 'notify_type' => 1], #1 for complaint notification
           

        // );

   		$extraNotificationData = ["message" => $data,"click_action" =>'sfa.solution.NotificationLayoutActivity'];
        

      	$fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to'        => $fcm_token, //single token
            'notification' => $data,
            'data' => $extraNotificationData
        ];

        //    $headers = array(
        //     'Authorization: key=AAAAYKeUdAk:APA91bEx9MFbQVpo_Aaql0M9ZXU33DE9zAnM-Q7G8PY8-kAig7lDvDdZYZaCyisp1dAoKlVttqBTa6nZfU5UkG5a09FE7O7WBnOhK18hbBGf_iUMWkx8rGzWKZhmhpW5RAhHzSSWFXHS',
        //     'Content-Type: application/json'
        // );
        $headers = array(
            'Authorization: key= ' . config('app.FCM_API_ACCESS_KEY'),
            // 'Authorization: key=AAAAcmM-bsendNotificationF0:APA91bEmNq9OEWySv8I8IfdTyeJZ8w18jWEiN5MyY2LDlKICOwy52kh921S6wNTZ9jGgSWTmtOySOPF_SyoJ0vkocJG24trvb-Fv2BtNhZO15MoRxymueZSKPcnYeMXBZdVVxQseDdiR',
            'Content-Type: application/json'
        );

		$data_string = json_encode($fcmNotification); 


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result === FALSE) {
            die('Curl Failed: ' . curl_error($ch));
        }
        // echo $result;die;
        return $result;

           
    }

  //   public function change_lat_lng_mnc_mcc_lat_cellid(Request $request)
  //   {
  //   	$company_id = array('69,37');
  //   	$date = !empty($request->track_date)?$request->track_date:date('Y-m-d');
  //   	$data_set = DB::table('user_daily_tracking')
  //   				->where('track_date',$date)
		// 			->where('lat_lng','=','0.0,0.0')
		// 			// ->where('lat_lng','=','0,0')
		// 			// ->where('lat_lng','=','0')
		// 			// ->where('user_id','5010')
  //   				->whereIn('company_id',$company_id)
  //   				->orderBy('id','desc')
  //   				->get();
  //   	$count_key[] = '0';
		// foreach($data_set as $key => $value)
		// {
		// 	$explode_data = explode(':',$value->mnc_mcc_lat_cellid);
		// 	$first = !empty($explode_data[0])?$explode_data[0]:'0';
		// 	$second = !empty($explode_data[1])?$explode_data[1]:'0';
		// 	$third = !empty($explode_data[2])?$explode_data[2]:'0';
		// 	$forth = !empty($explode_data[3])?$explode_data[3]:'0';
  //   		$str = "https://opencellid.org/ajax/searchCell.php?mcc=".$first."&mnc=".$second."&lac=".$third."&cell_id=".$forth;
  //   		// dd($str);

  //   		$ch = curl_init($str);
	 //        // curl_setopt($ch, CURLOPT_POST, true);
	 //        // curl_setopt($ch, CURLOPT_POSTFIELDS, $sending_array);
	 //        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	 //        $response = curl_exec($ch);
	 //        $return_decode = json_decode($response);
	 //        // dd($return_decode);
	 //        if($return_decode != false)
	 //        {
	 //        	$count_key[] = $key;
	 //        	$lat = !empty($return_decode->lat)?$return_decode->lat:'0.0';
	 //        	$lon = !empty($return_decode->lon)?$return_decode->lon:'0.0';
	 //        	$update_query = DB::table('user_daily_tracking')
		// 					->where('user_id',$value->user_id)
  //   						->where('id',$value->id)
  //   						->update([
  //   							'lat_lng'=>$lat.','.$lon,
  //   						]);
	 //        }
	 //        // dd('1');
    		

		// }
		// $msg = array_sum($count_key).' Count Updated Successfully';
  //       return response()->json([ 'response' =>True,'message'=>$msg]);


  //   }
  //   public function change_addr_mnc_mcc_lat_cellid(Request $request)
  //   {
  //   	$company_id = array('69,37');
  //   	$date = !empty($request->track_date)?$request->track_date:date('Y-m-d');
  //   	$data_set = DB::table('user_daily_tracking')
  //   				->where('track_date',$date)
		// 			->where('lat_lng','!=','0.0,0.0')
		// 			->Where('track_address','NA')
		// 			// ->orWhere('track_address','')
		// 			// ->orWhere('track_address',NULL)
		// 			// ->where('lat_lng','=','0,0')
		// 			// ->where('lat_lng','=','0')
		// 			->where('user_id','5010')
  //   				->whereIn('company_id',$company_id)
  //   				->orderBy('id','desc')
  //   				->get();
		// // dd($data_set);
		// foreach($data_set as $key => $value)
		// {
		// 	$explode_data = explode(':',$value->mnc_mcc_lat_cellid);
		// 	$first = $explode_data[0];
		// 	$second = $explode_data[1];
		// 	$third = $explode_data[2];
		// 	$forth = $explode_data[3];
  //   		$str = "https://www.latlong.net/Show-Latitude-Longitude.html";

  //   		$sending_array = [

  //   			'latitude'=>"10.934727",
  //   			'latitude'=>"78.429596",
  //   		];

  //   		$ch = curl_init($str);
	 //        curl_setopt($ch, CURLOPT_POST, true);
	 //        curl_setopt($ch, CURLOPT_POSTFIELDS, $sending_array);
	 //        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	 //        $response = curl_exec($ch);
	 //        $return_decode = json_decode($response);
	 //        dd($return_decode);
	 //        if($return_decode != false)
	 //        {
	 //        	$update_query = DB::table('user_daily_tracking')
		// 					->where('user_id',$value->user_id)
  //   						->where('id',$value->id)
  //   						->update([
  //   							'lat_lng'=>$return_decode->lat.','.$return_decode->lon,
  //   						]);
	 //        }
	 //        // dd('1');
    		

		// }
  //       return response()->json([ 'response' =>True,'message'=>'Success']);

        
  //   }





    public function juniorSaleDetails(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'company_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $user_id = $request->user_id;
        $company_id = $request->company_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        Session::forget('juniordata');      
        $check_junior_data=JuniorData::getJuniorUser($user_id,$company_id);
        Session::push('juniordata', $user_id);

        $junior_data_check = Session::get('juniordata');

        $table_name = TableReturn::table_return($from_date,$to_date);

    		
    	$saleData = DB::table($table_name)
					->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
					->join('dealer','dealer.id','=',$table_name.'.dealer_id')
					->join('location_7','location_7.id','=',$table_name.'.location_id')
					->select('dealer.id as dealer_id','dealer.name as dealer_name','location_7.id as beat_id','location_7.name as beat_name',$table_name.'.user_id as user_id')
                    ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
					->where($table_name.'.company_id',$company_id)
					->where('user_sales_order_details.company_id',$company_id)
					->whereIn($table_name.'.user_id',$junior_data_check)
					->groupBy($table_name.'.dealer_id',$table_name.'.location_id',$table_name.'.user_id')
					->get();

		$outArray = array();
		foreach($saleData as $sdkey => $sdval){

			$uid = $sdval->user_id;

			$outArray[$uid][] = $sdval;

		}








        $juniorDetails = DB::table('person')
        				->join($table_name,$table_name.'.user_id','=','person.id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->join('location_3','location_3.id','=','person.state_id')
                        ->select('person.id as user_id',DB::raw("CONCAT(first_name,' ',middle_name,' ',last_name) as user_name"),'rolename','person.mobile','person.version_code_name','person_login.person_image','location_3.name as state_name')
                        ->where('person_status','=','1')
                        ->where($table_name.'.call_status','1')
                    	->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                        ->whereIn('person.id',$junior_data_check)
                        ->where('person.company_id',$company_id)
                        ->where($table_name.'.company_id',$company_id)
                        ->groupBy('person.id')
                        ->get()->toArray();




    
        $final_array = array();
        foreach ($juniorDetails as $jtdkey => $jtdvalue) {

            $userId = !empty($jtdvalue->user_id)?$jtdvalue->user_id:'';
            $user_name = !empty($jtdvalue->user_name)?$jtdvalue->user_name:'';
            $role_name = !empty($jtdvalue->rolename)?$jtdvalue->rolename:'';
            $mobile_no = !empty($jtdvalue->mobile)?$jtdvalue->mobile:'';
            $state_name = !empty($jtdvalue->state_name)?$jtdvalue->state_name:'';

          


            $out['user_id'] = $userId;
            $out['user_name'] = $user_name;
            $out['role_name'] = $role_name;
            $out['mobile'] = !empty($jtdvalue->mobile)?$jtdvalue->mobile:'';
            $out['state_name'] = $state_name;

            $out['dealerBeatDetails'] = !empty($outArray[$userId])?$outArray[$userId]:array();



            $final_array[] = $out;
            
        }


		// dd($final_array);


        if(!empty($final_array)){
            return response()->json([ 'response' =>TRUE,'finalDetails'=>$final_array]);

        }else{
            return response()->json([ 'response' =>FALSE,'finalDetails'=>array()]);

        }

    }



    public function juniorSaleDetailsReport(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'company_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
            'dealer_id'=>'required',
            'beat_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }


        $user_id = $request->user_id;
        $company_id = $request->company_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $dealer_id = $request->dealer_id;
        $beat_id = $request->beat_id;

        // product percentage starts//

        $datearray = array();
        $startTime = strtotime($from_date);
        $endTime = strtotime($to_date);
        // $table_name = TableReturn::table_return($from_date,$to_date);

        for ($currentDate = $startTime; $currentDate <= $endTime; $currentDate += (86400)) 
        {                                       
            $Store = date('Y-m-d', $currentDate); 
            $datearray[] = $Store; 
        }
        $product_percentage = array();


         foreach ($datearray as $key => $value) 
        {
            $product_percentage_data = DB::table('product_wise_scheme_plan_details')
                            ->where('incentive_type',1)
                            ->where('company_id',$company_id)
                            ->where('value_amount_percentage','!=','0')
                            ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$value' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$value'")
                            // ->select()
                            ->get();

            foreach ($product_percentage_data as $keyi => $valuei) 
            {
                $ans = $valuei->product_id.$valuei->state_id;
                $product_percentage[$ans] = $valuei->value_amount_percentage;
            }
        }


        // dd($product_percentage);

        // product percentage ends // 


        $table_name = TableReturn::table_return($from_date,$to_date);


         $finalCatalogProduct = DB::table('product_type')
                                ->where('product_type.company_id',$company_id)
                                ->groupBy('product_type.id')
                                ->pluck('flag_neha','product_type.id')->toArray();

        $scheme_amount = DB::table($table_name)
                            ->where('company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d') <= '$to_date' ")
                            ->groupBy('user_id')
                            ->groupBy('date')
                            ->pluck(DB::raw("SUM(total_sale_value) as sale"),DB::raw("CONCAT(user_id,date)"));



    		
    	$saleData = DB::table($table_name)
					->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
					->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
					->join('location_view','location_view.l7_id','=',$table_name.'.location_id')
					->select('catalog_product.name as sku_name','catalog_product.final_product_type','catalog_product.quantity_per_case as quantity_per_case','catalog_product.quantiy_per_other_type as quantiy_per_other_type',DB::raw("SUM(user_sales_order_details.quantity) as product_quantity"),DB::raw("SUM(user_sales_order_details.quantity*user_sales_order_details.rate) as product_quantity_amount"),'l3_id as state_id','catalog_product.id as product_id')
                    ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
					->where($table_name.'.company_id',$company_id)
					->where('user_sales_order_details.company_id',$company_id)
					->where('catalog_product.company_id',$company_id)
					->where($table_name.'.dealer_id',$dealer_id)
					->where($table_name.'.user_id',$user_id)
					->where($table_name.'.location_id',$beat_id)
					->groupBy('user_sales_order_details.product_id')
					->get();


		$finalOut = array();
		foreach($saleData as $salekey => $saleval){
			$sku_name = $saleval->sku_name;
			$final_product_type = $saleval->final_product_type;
			$quantity_per_case = $saleval->quantity_per_case;
			$quantiy_per_other_type = $saleval->quantiy_per_other_type;
			$product_quantity = $saleval->product_quantity;
			$product_quantity_amount = $saleval->product_quantity_amount;
			$state_id = $saleval->state_id;
			$product_id = $saleval->product_id;


            $flagNeha = !empty($finalCatalogProduct[$final_product_type])?$finalCatalogProduct[$final_product_type]:'0';


            $out['sku_name'] = $sku_name; 
            $out['final_product_type'] = $final_product_type; 
            $out['quantity_per_case'] = $quantity_per_case; 
            $out['quantiy_per_other_type'] = $quantiy_per_other_type; 
            $out['product_quantity'] = $product_quantity; 
            $out['totalSaleValue'] = ROUND($product_quantity_amount,2); 
            $out['state_id'] = $state_id; 
            $out['product_id'] = $product_id; 


           if(!empty($product_percentage[$product_id.$state_id])){
	            $value = ($product_quantity_amount)*$product_percentage[$product_id.$state_id]/100; 
           }
	        else{
	             $value = 0; 
	        }


            $out['totalSaleValueWithScheme'] = ROUND($product_quantity_amount-$value,2); 



            if($flagNeha == '0'){
             $out['finalQty'] =  $saleval->product_quantity;
            }else{


                if($flagNeha == '1'){
                    $out['finalQty'] =  ROUND(($saleval->product_quantity/$saleval->quantity_per_case),2);
                }elseif($flagNeha == '2'){
                    $out['finalQty'] =  ROUND(($saleval->product_quantity/$saleval->quantiy_per_other_type),2);
                }else{
                    $out['finalQty'] =  '0';
                }
            }


            $finalOut[] = $out;


		}

		 if(!empty($finalOut)){
            return response()->json([ 'response' =>TRUE,'finalProductDetails'=>$finalOut]);

        }else{
            return response()->json([ 'response' =>FALSE,'finalProductDetails'=>array()]);

        }


    }



     public function distribubtorAnalyticsDetails(Request $request)
    {

    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'dealer_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        if(!empty($request->date)){
        	$date = $request->date;
        }else{
        	$date = date('Y-m-d');
        }


    	$dealer_id = $request->dealer_id; 
    	$company_id = $request->company_id; 

    	for ($i = 0; $i < 6; $i++) {
		  $monthArr[] =  date('Y-m', strtotime("-$i month"));
		}

		$firstMonth = $monthArr[5];
		$lastMonth = $monthArr[0];


		$monthWiseSale = DB::table('user_sales_order')
					//->select(DB::raw("ROUND(SUM((case_rate*user_sales_order_details.case_qty)+(rate*quantity)),2) as sale"),DB::raw("DATE_FORMAT(user_sales_order.date,'%Y-%m') as month"))
    				->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m')>='$firstMonth' AND DATE_FORMAT(user_sales_order.date,'%Y-%m')<='$lastMonth'")
                    ->where('user_sales_order.dealer_id',$dealer_id)
    				->where('user_sales_order.company_id',$company_id)
    				->groupBy(DB::raw("DATE_FORMAT(user_sales_order.date,'%Y-%m')"))
    				->pluck(DB::raw("ROUND(SUM((case_rate*user_sales_order_details.case_qty)+(rate*quantity)),2) as sale"),DB::raw("DATE_FORMAT(user_sales_order.date,'%Y-%m') as month"));

    	// dd($monthWiseSale);


    	$detailsData = DB::table('dealer')
    					->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
    					->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
    					->join('retailer','retailer.location_id','=','location_7.id')
    					->select(DB::raw("COUNT(DISTINCT location_7.id) as beat_count"),DB::raw("COUNT(DISTINCT retailer.id) as retailer_count"))
    					->where('dealer.id',$dealer_id)
    					->where('dealer.company_id',$company_id)
    					->where('dealer_location_rate_list.company_id',$company_id)
    					->where('location_7.company_id',$company_id)
    					->where('retailer.company_id',$company_id)
    					->groupBy('dealer.id')
    					->first();


    	$saleData = DB::table('user_sales_order')	
    				->select(DB::raw("ROUND(SUM((case_rate*user_sales_order_details.case_qty)+(rate*quantity)),2) as sale"))
    				->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$date'")
    				->where('user_sales_order.dealer_id',$dealer_id)
    				->where('user_sales_order.company_id',$company_id)
    				->first();


    	$lastSaleData = DB::table('user_sales_order')	
    				->select(DB::raw("MAX(date) as lastDate"))
    				->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
    				->where('user_sales_order.dealer_id',$dealer_id)
    				->where('user_sales_order.company_id',$company_id)
    				->first();



    	$dealerDetails = DB::table('dealer')
    					->select('dealer.id as dealer_id','dealer.name as dealer_name','dealer.other_numbers as dealer_contact')
    					->where('company_id',$company_id)
    					->where('dealer.id',$dealer_id)
    					->groupBy('dealer.id')
    					->get();

    	$dout = array();
    	foreach($dealerDetails as $ddkey => $ddval){

    		$dout['dealer_id'] = $ddval->dealer_id;
    		$dout['dealer_name'] = $ddval->dealer_name;
    		$dout['dealer_contact'] = $ddval->dealer_contact;

    		$dout['beat_count'] = $detailsData->beat_count;
    		$dout['retailer_count'] = $detailsData->retailer_count;

    		$dout['totalSale'] = $saleData->sale;

    		$dout['lastSaleDate'] = $lastSaleData->lastDate;

    		$date1 = $date;
			$date2 = $lastSaleData->lastDate;

			$diff = abs(strtotime($date2) - strtotime($date1));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

			if($days == '0'){
    		$dout['daysAgo'] = 'Visited Today';
			}else{
    		$dout['daysAgo'] = $days.' Days Ago';
			}

			// month array start
			foreach($monthArr as $mkey => $mval){
				$monthFormat = date('M-y', strtotime($mval));
				$dout['monthName'][] = $monthFormat;
			}
			// month array ends


			// sale array start
			foreach($monthArr as $mkey => $mval){
				$monthFormat = date('M-y', strtotime($mval));
				$dout['monthSale'][] = !empty($monthWiseSale[$mval])?$monthWiseSale[$mval]:'0';
			}
			// sale array ends
    	}
    	// dd($dout);

		if($dout){
			return response()->json([ 'response' =>TRUE,'result'=>$dout]);
		}
		else
		{
			return response()->json([ 'response' =>FALSE,'result'=>array()]);
		}

    }




    public function userAnalyticsDetails(Request $request)
    {

    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'user_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        if(!empty($request->date)){
        	$date = $request->date;
        }else{
        	$date = date('Y-m-d');
        }


    	$user_id = $request->user_id; 
    	$company_id = $request->company_id; 

    	for ($i = 0; $i < 6; $i++) {
		  $monthArr[] =  date('Y-m', strtotime("-$i month"));
		}

		$firstMonth = $monthArr[5];
		$lastMonth = $monthArr[0];


		$monthWiseSale = DB::table('user_sales_order')
					//->select(DB::raw("ROUND(SUM((case_rate*user_sales_order_details.case_qty)+(rate*quantity)),2) as sale"),DB::raw("DATE_FORMAT(user_sales_order.date,'%Y-%m') as month"))
    				->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m')>='$firstMonth' AND DATE_FORMAT(user_sales_order.date,'%Y-%m')<='$lastMonth'")
                    ->where('user_sales_order.user_id',$user_id)
    				->where('user_sales_order.company_id',$company_id)
    				->groupBy(DB::raw("DATE_FORMAT(user_sales_order.date,'%Y-%m')"))
    				->pluck(DB::raw("ROUND(SUM((case_rate*user_sales_order_details.case_qty)+(rate*quantity)),2) as sale"),DB::raw("DATE_FORMAT(user_sales_order.date,'%Y-%m') as month"));

    	// dd($monthWiseSale);


    	$detailsData = DB::table('person')
    					->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
    					->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
    					->join('retailer','retailer.location_id','=','location_7.id')
    					->select(DB::raw("COUNT(DISTINCT location_7.id) as beat_count"),DB::raw("COUNT(DISTINCT retailer.id) as retailer_count"))
    					->where('person.id',$user_id)
    					->where('person.company_id',$company_id)
    					->where('dealer_location_rate_list.company_id',$company_id)
    					->where('location_7.company_id',$company_id)
    					->where('retailer.company_id',$company_id)
    					->groupBy('person.id')
    					->first();


    	$saleData = DB::table('user_sales_order')	
    				->select(DB::raw("ROUND(SUM((case_rate*user_sales_order_details.case_qty)+(rate*quantity)),2) as sale"))
    				->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$date'")
    				->where('user_sales_order.user_id',$user_id)
    				->where('user_sales_order.company_id',$company_id)
    				->first();


    	$lastSaleData = DB::table('user_sales_order')	
    				->select(DB::raw("MAX(date) as lastDate"))
    				->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
    				->where('call_status','=','1')
    				->where('user_sales_order.user_id',$user_id)
    				->where('user_sales_order.company_id',$company_id)
    				->first();



    	$dealerDetails = DB::table('person')
    					->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.mobile as user_contact')
    					->where('company_id',$company_id)
    					->where('person.id',$user_id)
    					->groupBy('person.id')
    					->get();

    	$dout = array();
    	foreach($dealerDetails as $ddkey => $ddval){

    		$dout['user_id'] = $ddval->user_id;
    		$dout['user_name'] = $ddval->user_name;
    		$dout['user_contact'] = $ddval->user_contact;

    		$dout['beat_count'] = $detailsData->beat_count;
    		$dout['retailer_count'] = $detailsData->retailer_count;

    		$dout['totalSale'] = $saleData->sale;

    		$dout['lastSaleDate'] = $lastSaleData->lastDate;

    		$date1 = $date;
			$date2 = $lastSaleData->lastDate;

			$diff = abs(strtotime($date2) - strtotime($date1));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

			if($days == '0'){
    		$dout['daysAgo'] = 'Productive Today';
			}else{
    		$dout['daysAgo'] = 'Productive '.$days.' Days Ago';
			}

			// month array start
			foreach($monthArr as $mkey => $mval){
				$monthFormat = date('M-y', strtotime($mval));
				$dout['monthName'][] = $monthFormat;
			}
			// month array ends


			// sale array start
			foreach($monthArr as $mkey => $mval){
				$monthFormat = date('M-y', strtotime($mval));
				$dout['monthSale'][] = !empty($monthWiseSale[$mval])?$monthWiseSale[$mval]:'0';
			}
			// sale array ends
    	}
    	// dd($dout);

		if($dout){
			return response()->json([ 'response' =>TRUE,'result'=>$dout]);
		}
		else
		{
			return response()->json([ 'response' =>FALSE,'result'=>array()]);
		}

    }



    public function retailerAnalyticsDetails(Request $request)
    {

    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'retailer_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        if(!empty($request->date)){
        	$date = $request->date;
        }else{
        	$date = date('Y-m-d');
        }


    	$retailer_id = $request->retailer_id; 
    	$company_id = $request->company_id; 

    	for ($i = 0; $i < 6; $i++) {
		  $monthArr[] =  date('Y-m', strtotime("-$i month"));
		}

		$firstMonth = $monthArr[5];
		$lastMonth = $monthArr[0];


		$monthWiseSale = DB::table('user_sales_order')
					//->select(DB::raw("ROUND(SUM((case_rate*user_sales_order_details.case_qty)+(rate*quantity)),2) as sale"),DB::raw("DATE_FORMAT(user_sales_order.date,'%Y-%m') as month"))
    				->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m')>='$firstMonth' AND DATE_FORMAT(user_sales_order.date,'%Y-%m')<='$lastMonth'")
                    ->where('user_sales_order.retailer_id',$retailer_id)
    				->where('user_sales_order.company_id',$company_id)
    				->groupBy(DB::raw("DATE_FORMAT(user_sales_order.date,'%Y-%m')"))
    				->pluck(DB::raw("ROUND(SUM((case_rate*user_sales_order_details.case_qty)+(rate*quantity)),2) as sale"),DB::raw("DATE_FORMAT(user_sales_order.date,'%Y-%m') as month"));

    	// dd($monthWiseSale);


    	// $detailsData = DB::table('person')
    	// 				->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
    	// 				->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
    	// 				->join('retailer','retailer.location_id','=','location_7.id')
    	// 				->select(DB::raw("COUNT(DISTINCT location_7.id) as beat_count"),DB::raw("COUNT(DISTINCT retailer.id) as retailer_count"))
    	// 				->where('person.id',$user_id)
    	// 				->where('person.company_id',$company_id)
    	// 				->where('dealer_location_rate_list.company_id',$company_id)
    	// 				->where('location_7.company_id',$company_id)
    	// 				->where('retailer.company_id',$company_id)
    	// 				->groupBy('person.id')
    	// 				->first();


    	$saleData = DB::table('user_sales_order')	
    				->select(DB::raw("ROUND(SUM((case_rate*user_sales_order_details.case_qty)+(rate*quantity)),2) as sale"))
    				->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$date'")
    				->where('user_sales_order.retailer_id',$retailer_id)
    				->where('user_sales_order.company_id',$company_id)
    				->first();


    	$lastSaleData = DB::table('user_sales_order')	
    				->select(DB::raw("MAX(date) as lastDate"))
    				->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
    				->where('call_status','=','1')
    				->where('user_sales_order.retailer_id',$retailer_id)
    				->where('user_sales_order.company_id',$company_id)
    				->first();



    	$dealerDetails = DB::table('retailer')
    					->select('retailer.id as retailer_id','retailer.name as retailer_name','retailer.other_numbers as retailer_contact','landline')
    					->where('company_id',$company_id)
    					->where('retailer.id',$retailer_id)
    					->groupBy('retailer.id')
    					->get();

    	$dout = array();
    	foreach($dealerDetails as $ddkey => $ddval){

    		$dout['retailer_id'] = $ddval->retailer_id;
    		$dout['retailer_name'] = $ddval->retailer_name;
    		$dout['retailer_contact'] = !empty($ddval->retailer_contact)?$ddval->retailer_contact:$ddval->landline;

    		// $dout['beat_count'] = $detailsData->beat_count;
    		// $dout['retailer_count'] = $detailsData->retailer_count;

    		$dout['totalSale'] = $saleData->sale;

    		$dout['lastSaleDate'] = $lastSaleData->lastDate;

    		$date1 = $date;
			$date2 = $lastSaleData->lastDate;

			$diff = abs(strtotime($date2) - strtotime($date1));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

			if($days == '0'){
    		$dout['daysAgo'] = 'Visited Today';
			}else{
    		$dout['daysAgo'] = $days.' Days Ago';
			}

			// month array start
			foreach($monthArr as $mkey => $mval){
				$monthFormat = date('M-y', strtotime($mval));
				$dout['monthName'][] = $monthFormat;
			}
			// month array ends


			// sale array start
			foreach($monthArr as $mkey => $mval){
				$monthFormat = date('M-y', strtotime($mval));
				$dout['monthSale'][] = !empty($monthWiseSale[$mval])?$monthWiseSale[$mval]:'0';
			}
			// sale array ends
    	}
    	// dd($dout);

		if($dout){
			return response()->json([ 'response' =>TRUE,'result'=>$dout]);
		}
		else
		{
			return response()->json([ 'response' =>FALSE,'result'=>array()]);
		}

    }



    public function beatAnalyticsDetails(Request $request)
    {

    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'beat_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        if(!empty($request->date)){
        	$date = $request->date;
        }else{
        	$date = date('Y-m-d');
        }


    	$location_id = $request->beat_id; 
    	$company_id = $request->company_id; 

    	for ($i = 0; $i < 6; $i++) {
		  $monthArr[] =  date('Y-m', strtotime("-$i month"));
		}

		$firstMonth = $monthArr[5];
		$lastMonth = $monthArr[0];


		$monthWiseSale = DB::table('user_sales_order')
    				->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m')>='$firstMonth' AND DATE_FORMAT(user_sales_order.date,'%Y-%m')<='$lastMonth'")
                    ->where('user_sales_order.location_id',$location_id)
    				->where('user_sales_order.company_id',$company_id)
    				->groupBy(DB::raw("DATE_FORMAT(user_sales_order.date,'%Y-%m')"))
    				->pluck(DB::raw("ROUND(SUM((case_rate*user_sales_order_details.case_qty)+(rate*quantity)),2) as sale"),DB::raw("DATE_FORMAT(user_sales_order.date,'%Y-%m') as month"));

    	// dd($monthWiseSale);


    	$detailsData = DB::table('retailer')
    					->select(DB::raw("COUNT(DISTINCT retailer.id) as retailer_count"))
    					->where('retailer.location_id',$location_id)
    					->where('retailer.company_id',$company_id)
    					->groupBy('retailer.location_id')
    					->first();


    	$saleData = DB::table('user_sales_order')	
    				->select(DB::raw("ROUND(SUM((case_rate*user_sales_order_details.case_qty)+(rate*quantity)),2) as sale"))
    				->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$date'")
    				->where('user_sales_order.location_id',$location_id)
    				->where('user_sales_order.company_id',$company_id)
    				->first();


    	$lastSaleData = DB::table('user_sales_order')	
    				->select(DB::raw("MAX(date) as lastDate"))
    				->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
    				->where('call_status','=','1')
    				->where('user_sales_order.location_id',$location_id)
    				->where('user_sales_order.company_id',$company_id)
    				->first();



    	$dealerDetails = DB::table('location_7')
    					->join('location_6','location_6.id','=','location_7.location_6_id')
    					->select('location_7.id as beat_id','location_7.name as beat_name','location_6.name as town')
    					->where('location_7.company_id',$company_id)
    					->where('location_7.id',$location_id)
    					->groupBy('location_7.id')
    					->get();

    	$dout = array();
    	foreach($dealerDetails as $ddkey => $ddval){

    		$dout['beat_id'] = $ddval->beat_id;
    		$dout['beat_name'] = $ddval->beat_name;
    		$dout['beat_town'] = !empty($ddval->town)?$ddval->town:'';

    		// $dout['beat_count'] = $detailsData->beat_count;
    		$dout['retailer_count'] = $detailsData->retailer_count;

    		$dout['totalSale'] = $saleData->sale;

    		$dout['lastSaleDate'] = $lastSaleData->lastDate;

    		$date1 = $date;
			$date2 = $lastSaleData->lastDate;

			$diff = abs(strtotime($date2) - strtotime($date1));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

			if($days == '0'){
    		$dout['daysAgo'] = 'Visited Today';
			}else{
    		$dout['daysAgo'] = $days.' Days Ago';
			}

			// month array start
			foreach($monthArr as $mkey => $mval){
				$monthFormat = date('M-y', strtotime($mval));
				$dout['monthName'][] = $monthFormat;
			}
			// month array ends


			// sale array start
			foreach($monthArr as $mkey => $mval){
				$monthFormat = date('M-y', strtotime($mval));
				$dout['monthSale'][] = !empty($monthWiseSale[$mval])?$monthWiseSale[$mval]:'0';
			}
			// sale array ends
    	}
    	// dd($dout);

		if($dout){
			return response()->json([ 'response' =>TRUE,'result'=>$dout]);
		}
		else
		{
			return response()->json([ 'response' =>FALSE,'result'=>array()]);
		}

    }


     public function filterDetails(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $user_id = $request->user_id;
        $company_id = $request->company_id;
      

        Session::forget('juniordata');      
        $check_junior_data=JuniorData::getJuniorUser($user_id,$company_id);
        Session::push('juniordata', $user_id);

        $junior_data_check = Session::get('juniordata');

        // first filter start
        $outletTypeData = DB::table('_retailer_outlet_type')
        				->select('id','outlet_type')
        				->where('status','1')
        				->where('company_id',$company_id)
        				->get()->toArray();

        $finalOutletType = array();
        foreach ($outletTypeData as $okey => $ovalue) {
        	$outOutletType['id'] = $ovalue->id;
        	$outOutletType['outlet_type'] = $ovalue->outlet_type;
        	$outOutletType['parentId'] = "1";

        	$finalOutletType[] = $outOutletType;
        }
        // first filter end

        // second filter start
        $outletClassData = DB::table('_retailer_outlet_category')
        				->select('id','outlet_category')
        				->where('status','1')
        				->where('company_id',$company_id)
        				->get()->toArray();


        $finaloutletClass = array();
        foreach ($outletClassData as $ockey => $ocvalue) {
        	$outoutletClass['id'] = $ocvalue->id;
        	$outoutletClass['outlet_type'] = $ocvalue->outlet_category;
        	$outoutletClass['parentId'] = "2";

        	$finaloutletClass[] = $outoutletClass;
        }
        // second filter ends


        // third filter start
        $juniorDetailsData = DB::table('person')
        				->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id as user_id')
        				->join('person_login','person_login.person_id','=','person.id')
        				->where('person.company_id',$company_id)
        				->whereIn('person.id',$junior_data_check)
        				->get()->toArray();

        $finaljuniorDetails = array();
        foreach ($juniorDetailsData as $jkey => $jvalue) {
        	$outjuniorDetails['id'] = $jvalue->user_id;
        	$outjuniorDetails['user_name'] = $jvalue->user_name;
        	$outjuniorDetails['parentId'] = "3";

        	$finaljuniorDetails[] = $outjuniorDetails;
        }
        // third filter ends

        // fourth filter starts
        $lastVisitData = array("Today","1 Days Ago","2 Days Ago","3 Days Ago","4 Days Ago","Last Week");

            $finallastVisit = array();
        foreach ($lastVisitData as $lkey => $lvalue) {
        	$outlastVisit['id'] = $lvalue;
        	$outlastVisit['parentId'] = "4";

        	$finallastVisit[] = $outlastVisit;
        }
        // fourth filter ends



        $outletTypeArray = array("id"=>"1","name"=>"outletType","Details"=>$finalOutletType);
        $outletClassArray = array("id"=>"2","name"=>"outletClass","Details"=>$finaloutletClass);
        $juniorDetailsArray = array("id"=>"3","name"=>"juniorDetails","Details"=>$finaljuniorDetails);
        $lastVisitArray = array("id"=>"4","name"=>"lastVisit","Details"=>$finallastVisit);

        $finalArray = array("outletType" => $outletTypeArray,"outletClass" => $outletClassArray,"juniorDetails" => $juniorDetailsArray,"lastVisit" => $lastVisitArray);


        if($finalArray){
			return response()->json([ 'response' =>TRUE,'result'=>$finalArray]);
		}
		else
		{
			return response()->json([ 'response' =>FALSE,'result'=>array()]);
		}

    }




     public function beatAnalysis(Request $request)
    {

    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'beat_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $date = date('Y-m-d');
    	$location_id = $request->beat_id; 
    	$company_id = $request->company_id; 
        $check = DB::table('app_other_module_assign')->where('company_id',$request->company_id)->where('module_id',6)->first();

        // for today visit starts
        if(empty($check)){
			$saleData = DB::table('user_sales_order')	
					->select(DB::raw("ROUND(SUM((case_rate*user_sales_order_details.case_qty)+(rate*quantity)),2) as sale"),DB::raw("COUNT(DISTINCT retailer_id,date) as productiveCall"))
					->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
	                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$date'")
					->where('user_sales_order.location_id',$location_id)
					->where('user_sales_order.company_id',$company_id)
					->first();
		}else{
			$saleData = DB::table('user_sales_order')	
					->select(DB::raw("ROUND(SUM(final_secondary_rate*final_secondary_qty),2) as sale"),DB::raw("COUNT(DISTINCT retailer_id,date) as productiveCall"))
					->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
	                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$date'")
					->where('user_sales_order.location_id',$location_id)
					->where('user_sales_order.company_id',$company_id)
					->first();
		}

		$totalCall = DB::table('user_sales_order')	
					->select(DB::raw("COUNT(DISTINCT retailer_id,date) as totalCall"),'date')
	                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$date'")
					->where('user_sales_order.location_id',$location_id)
					->where('user_sales_order.company_id',$company_id)
					->first();

		$todayNotContacted = DB::table('sale_reason_remarks')
						->select(DB::raw("COUNT(DISTINCT retailer_id,date) as notContacted"))
						->join('retailer','retailer.id','=','sale_reason_remarks.retailer_id')
						->whereRaw("DATE_FORMAT(sale_reason_remarks.date,'%Y-%m-%d')='$date'")
						->where('retailer.location_id',$location_id)
						->where('sale_reason_remarks.company_id',$company_id)
						->first();

    	
        // for today visit ends


    
    	


    	$lastSaleData = DB::table('user_sales_order')	
    				->select('date')
    				->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
    				->where('call_status','=','1')
    				->where('user_sales_order.location_id',$location_id)
    				->where('user_sales_order.company_id',$company_id)
    				->groupBy('date')
    				->orderBy('date','DESC')
    				->take(2)->get()->toArray();


    	// for previous visit data starts
		if(empty($lastSaleData)){
			$previousDate = date('Y-m-d');
		}
		else
		{
			if($lastSaleData['0']->date == date('Y-m-d')){
	    		$previousDate = $lastSaleData['1']->date;
	    	}else{
	    		$previousDate = $lastSaleData['0']->date;
	    	}
		}
    	



    	 if(empty($check)){
			$previoussaleData = DB::table('user_sales_order')	
					->select(DB::raw("ROUND(SUM((case_rate*user_sales_order_details.case_qty)+(rate*quantity)),2) as sale"),DB::raw("COUNT(DISTINCT retailer_id,date) as productiveCall"))
					->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
	                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$previousDate'")
					->where('user_sales_order.location_id',$location_id)
					->where('user_sales_order.company_id',$company_id)
					->first();
		}else{
			$previoussaleData = DB::table('user_sales_order')	
					->select(DB::raw("ROUND(SUM(final_secondary_rate*final_secondary_qty),2) as sale"),DB::raw("COUNT(DISTINCT retailer_id,date) as productiveCall"))
					->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
	                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$previousDate'")
					->where('user_sales_order.location_id',$location_id)
					->where('user_sales_order.company_id',$company_id)
					->first();
		}

		$previoustotalCall = DB::table('user_sales_order')	
					->select(DB::raw("COUNT(DISTINCT retailer_id,date) as totalCall"))
	                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$previousDate'")
					->where('user_sales_order.location_id',$location_id)
					->where('user_sales_order.company_id',$company_id)
					->first();

		$previousNotContacted = DB::table('sale_reason_remarks')
						->select(DB::raw("COUNT(DISTINCT retailer_id,date) as notContacted"))
						->join('retailer','retailer.id','=','sale_reason_remarks.retailer_id')
						->whereRaw("DATE_FORMAT(sale_reason_remarks.date,'%Y-%m-%d')='$previousDate'")
						->where('retailer.location_id',$location_id)
						->where('sale_reason_remarks.company_id',$company_id)
						->first();

    	// for previous visit data ends




    	$detailsData = DB::table('retailer')
    					->select(DB::raw("COUNT(DISTINCT retailer.id) as retailer_count"))
    					->where('retailer.location_id',$location_id)
    					->where('retailer.company_id',$company_id)
    					->groupBy('retailer.location_id')
    					->first();

    	$dealerDetails = DB::table('location_7')
    					->join('location_6','location_6.id','=','location_7.location_6_id')
    					->select('location_7.id as beat_id','location_7.name as beat_name','location_6.name as town')
    					->where('location_7.company_id',$company_id)
    					->where('location_7.id',$location_id)
    					->groupBy('location_7.id')
    					->get();

    	$dout = array();
    	foreach($dealerDetails as $ddkey => $ddval){
    		$beatId = $ddval->beat_id;

    		$dout['beat_id'] = $ddval->beat_id;
    		$dout['beat_name'] = $ddval->beat_name;
    		$dout['beat_town'] = !empty($ddval->town)?$ddval->town:'';
    		$dout['retailer_count'] = $detailsData->retailer_count;

    		$todayData['todayVisitDate'] =  !empty($totalCall->date)?date('d M,Y',strtotime($totalCall->date)):'';
    		$todayData['todayTotalCall'] =  !empty($totalCall->totalCall)?$totalCall->totalCall:'0';
    		$todayData['todayProductiveCall'] =  !empty($saleData->productiveCall)?$saleData->productiveCall:'0';
    		$todayData['todaySaleValue'] =  !empty($saleData->sale)?$saleData->sale:'0';
    		$todayData['todayNotContacted'] =  !empty($todayNotContacted->notContacted)?$todayNotContacted->notContacted:'0';

    		$finalTodayData = $todayData;

    		$dout['todayVisit'] = $finalTodayData;



    		 $date1 = $date;
			$date2 = $previousDate;
			$diff = abs(strtotime($date2) - strtotime($date1));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

			$dayShown = $days.' Days Ago,'.date('d M,Y',strtotime($previousDate));

    		$previousData['previousVisitDate'] =  !empty($dayShown)?$dayShown:'';
    		$previousData['previousTotalCall'] =  !empty($previoustotalCall->totalCall)?$previoustotalCall->totalCall:'0';
    		$previousData['previousProductiveCall'] =  !empty($previoussaleData->productiveCall)?$previoussaleData->productiveCall:'0';
    		$previousData['previousSaleValue'] =  !empty($previoussaleData->sale)?$previoussaleData->sale:'0';
    		$previousData['previousNotContacted'] =  !empty($previousNotContacted->notContacted)?$previousNotContacted->notContacted:'0';

    		$finalPreviousData = $previousData;

    		$dout['previousVisit'] = $finalPreviousData;

			
    	}

		if($dout){
			return response()->json([ 'response' =>TRUE,'result'=>$dout]);
		}
		else
		{
			return response()->json([ 'response' =>FALSE,'result'=>array()]);
		}

    }



    public function userWiseDistributorAnalysis(Request $request)
    {

    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'user_id'=>'required',
            'date'=>'required',
            'flag'=>'required', // 1 for secondary and 2 for primary
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $date = !empty($request->date)?$request->date:date('Y-m-d');
    	$user_id = $request->user_id; 
    	$company_id = $request->company_id; 
    	$flag = $request->flag; 
        $check = DB::table('app_other_module_assign')->where('company_id',$request->company_id)->where('module_id',6)->first();

        $table_name = TableReturn::table_return($date,$date);

		// dd($date);

        // for today visit starts


        $dealerDetails = DB::table('dealer_location_rate_list')
						->select('dealer.id as dealer_id','dealer.name as dealer_name',DB::raw("COUNT(DISTINCT location_7.id) as totalBeat"),DB::raw("COUNT(DISTINCT retailer.id) as totalRetailer"),'location_3.name as state_name')
						->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
						->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
						->join('retailer','retailer.location_id','=','location_7.id')
						->join('location_3','location_3.id','=','dealer.state_id')
						->where('dealer_location_rate_list.user_id',$user_id)
						->where('dealer_location_rate_list.company_id',$company_id)
						->where('dealer.company_id',$company_id)
						->where('retailer.company_id',$company_id)
						->where('location_7.status','=','1')
						->where('retailer.retailer_status','=','1')
						->where('dealer.dealer_status','=','1')
						->groupBy('dealer.id')
						->get();

		$latestStockDate = DB::table('dealer_balance_stock')
						->select('user_id','dealer_id','company_id',DB::raw("MAX(submit_date_time) as latestStockDate"))
						->where('dealer_balance_stock.user_id',$user_id)
						->where('dealer_balance_stock.company_id',$company_id)
						->groupBy('dealer_id')
						->get();

		foreach ($latestStockDate as $lskey => $lsvalue) {
				
		$Stock[$lsvalue->dealer_id] = DB::table('dealer_balance_stock')
										->select(DB::raw("SUM(cases) as cases"),DB::raw("SUM(stock_qty) as pieces"),DB::raw("SUM((cases*mrp)+(stock_qty*pcs_mrp)) as stockValue"))
										->where('dealer_balance_stock.user_id',$lsvalue->user_id)
										->where('dealer_balance_stock.dealer_id',$lsvalue->dealer_id)
										->where('dealer_balance_stock.submit_date_time',$lsvalue->latestStockDate)
										->where('dealer_balance_stock.company_id',$company_id)
										->first();
		}



    if($flag == 1){
        if(empty($check)){
			$saleData = DB::table($table_name)	
					->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
					->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=',$table_name.'.dealer_id')
	                ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')='$date'")
	                ->where('dealer_location_rate_list.user_id',$user_id)
					->where($table_name.'.company_id',$company_id)
					->where('dealer_location_rate_list.company_id',$company_id)
					->groupBy('dealer_location_rate_list.dealer_id')
					->pluck(DB::raw("ROUND(SUM((case_rate*user_sales_order_details.case_qty)+(rate*quantity)),2) as sale"),'dealer_location_rate_list.dealer_id');
		}else{
			$saleData = DB::table($table_name)	
					->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
					->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=',$table_name.'.dealer_id')
	                ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')='$date'")
	                ->where('dealer_location_rate_list.user_id',$user_id)
					->where($table_name.'.company_id',$company_id)
					->where('dealer_location_rate_list.company_id',$company_id)
					->groupBy('dealer_location_rate_list.dealer_id')
					->pluck(DB::raw("ROUND(SUM(final_secondary_rate*final_secondary_qty),2) as sale"),'dealer_location_rate_list.dealer_id');
		}

		// dd($saleData);

		$lastSaleData = DB::table($table_name)	
    				->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
    				->where($table_name.'.user_id',$user_id)
    				->where($table_name.'.company_id',$company_id)
    				->groupBy('dealer_id')
    				->pluck(DB::raw("MAX(date) as lastDate"),$table_name.'.dealer_id');

		

		$finalOut = array();
    	foreach($dealerDetails as $ddkey => $ddval){
    		$dealerId = $ddval->dealer_id;

    		$dout['dealer_id'] = $ddval->dealer_id;
    		$dout['dealer_name'] = $ddval->dealer_name;
    		$dout['state_name'] = $ddval->state_name;

    		$dout['totalBeat'] = !empty($ddval->totalBeat)?$ddval->totalBeat:'0';
    		$dout['totalRetailer'] = !empty($ddval->totalRetailer)?$ddval->totalRetailer:'0';

    		$dout['totalSale'] = !empty($saleData[$dealerId])?$saleData[$dealerId]:'0';

    		$dout['lastVisited'] = !empty($lastSaleData[$dealerId])?$lastSaleData[$dealerId]:'0';

    		$date1 = $date;
			$date2 = !empty($lastSaleData[$dealerId])?$lastSaleData[$dealerId]:'';

			$diff = abs(strtotime($date2) - strtotime($date1));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

			if($days == '0'){
    		$dout['last_visit'] = 'Visited Today';
			}elseif($days > '0'){
    		$dout['last_visit'] = $days.' Days Ago';
			}else{
    		$dout['last_visit'] = 'Not Visited Yet';
			}

    		$dout['stockCases'] = !empty($Stock[$dealerId]->cases)?$Stock[$dealerId]->cases:'0';

    		$dout['stockPcs'] = !empty($Stock[$dealerId]->pieces)?$Stock[$dealerId]->pieces:'0';

    		$dout['stockValue'] = !empty($Stock[$dealerId]->stockValue)?$Stock[$dealerId]->stockValue:'0';
    	

    		$finalOut[] = $dout;
			
    	}
	}
	elseif($flag == 2){
        if(empty($check)){
		$dealer_primary_query = DB::table('user_primary_sales_order')
								->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
								->where('user_primary_sales_order.company_id',$request->company_id)
								->where('user_primary_sales_order_details.company_id',$request->company_id)
								->where('created_person_id',$user_id)
								->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')='$date'")
								->groupBy('dealer_id')
								->pluck(DB::raw("sum((rate*pcs)+(cases*pr_rate)) as total_sale_value"),'user_primary_sales_order.dealer_id');

		}else{
			$dealer_primary_query = DB::table('user_primary_sales_order')
								->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
								->where('user_primary_sales_order.company_id',$request->company_id)
								->where('user_primary_sales_order_details.company_id',$request->company_id)
								->where('created_person_id',$user_id)
								->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')='$date'")
								->groupBy('dealer_id')
								->pluck(DB::raw("sum(final_secondary_rate*final_secondary_qty) as total_sale_value"),'user_primary_sales_order.dealer_id');
		}


		$lastPrimarySaleData = DB::table('user_primary_sales_order')	
    				->where('user_primary_sales_order.created_person_id',$user_id)
    				->where('user_primary_sales_order.company_id',$company_id)
    				->groupBy('dealer_id')
    				->pluck(DB::raw("MAX(sale_date) as lastDate"),'user_primary_sales_order.dealer_id');


    	$finalOut = array();
    	foreach($dealerDetails as $ddkey => $ddval){
    		$dealerId = $ddval->dealer_id;

    		$dout['dealer_id'] = $ddval->dealer_id;
    		$dout['dealer_name'] = $ddval->dealer_name;
    		$dout['state_name'] = $ddval->state_name;

    		$dout['totalBeat'] = !empty($ddval->totalBeat)?$ddval->totalBeat:'0';
    		$dout['totalRetailer'] = !empty($ddval->totalRetailer)?$ddval->totalRetailer:'0';

    		

    		$dout['stockCases'] = !empty($Stock[$dealerId]->cases)?$Stock[$dealerId]->cases:'0';

    		$dout['stockPcs'] = !empty($Stock[$dealerId]->pieces)?$Stock[$dealerId]->pieces:'0';

    		$dout['stockValue'] = !empty($Stock[$dealerId]->stockValue)?$Stock[$dealerId]->stockValue:'0';
    		


    		$dout['totalSalePrimary'] = !empty($dealer_primary_query[$dealerId])?$dealer_primary_query[$dealerId]:'0';

    		$dout['PrimaryLastVisit'] = !empty($lastPrimarySaleData[$dealerId])?$lastPrimarySaleData[$dealerId]:'0';

    		$datep1 = $date;
			$datep2 = !empty($lastPrimarySaleData[$dealerId])?$lastPrimarySaleData[$dealerId]:'';

			$diffp = abs(strtotime($datep2) - strtotime($datep1));
			$yearsp = floor($diffp / (365*60*60*24));
			$monthsp = floor(($diffp - $yearsp * 365*60*60*24) / (30*60*60*24));
			$daysp = floor(($diffp - $yearsp * 365*60*60*24 - $monthsp*30*60*60*24)/ (60*60*24));

			if($daysp == '0'){
    		$dout['last_visit_primary'] = 'Visited Today';
			}elseif($daysp > '0'){
    		$dout['last_visit_primary'] = $daysp.' Days Ago';
			}else{
    		$dout['last_visit_primary'] = 'Not Visited Yet';
			}



    		$finalOut[] = $dout;
			
    	}
    }
		// dd($lastPrimarySaleData);

		// dd($Stock);

    	

		if($finalOut){
			return response()->json([ 'response' =>TRUE,'result'=>$finalOut]);
		}
		else
		{
			return response()->json([ 'response' =>FALSE,'result'=>array()]);
		}

    }




    public function totalCallDetails(Request $request)
    {

    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

    	$user_id = $request->user_id; 
    	$company_id = $request->company_id; 
    	$from_date = $request->from_date; 
    	$to_date = $request->to_date; 
        $table_name = TableReturn::table_return($from_date,$to_date);


    	Session::forget('juniordata');		
        $check_junior_data=JuniorData::getJuniorUser($request->user_id,$request->company_id);
        Session::push('juniordata', $request->user_id);
		$junior_data_check = Session::get('juniordata');


		$total_call = DB::table($table_name)	
					->select('retailer_id')	
					->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
					->where($table_name.'.company_id',$company_id)
					->whereIn('user_id',$junior_data_check)
					->groupBy('retailer_id','date')
					->get();

		$totalCall = array();
		foreach ($total_call as $key => $value) {
			$totalCall[$value->retailer_id] = $value->retailer_id;
		}


		$retailer_id_data = DB::table('retailer')->select('retailer.other_numbers','verfiy_retailer_status','sequence_id as seq_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'_role.rolename as designation','retailer.created_on as created_on','created_by_person_id','retailer.tin_no as tin_no','location_7.name as beat_name','retailer.id as id','lat_long','retailer.name as retailer_name','location_id','address','retailer.email as email','contact_per_name','landline','retailer.image_name')
                        ->join('location_7','location_7.id','=','retailer.location_id')
                        ->join('person','person.id','=','retailer.created_by_person_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->whereIn('retailer.id',$totalCall)
                        ->where('retailer.company_id',$company_id)
                        ->where('location_7.company_id',$company_id)
                        ->where('_role.company_id',$company_id)
                        ->where('retailer_status',1)
                        ->groupBy('retailer.id')
                        ->get();

        $final_retailer = array();
        foreach($retailer_id_data as $key => $value)
        {
            $retailer_id = $value->id;
            $payment_collection_query = '0';
            $challan_data_query = '0';
            $retailer_amt  = '0';
            $retailer_data['retailer_id'] = "$value->id";
            $retailer_data['retailer_name'] = $value->retailer_name;
            $retailer_data['lat_long'] = !empty($value->lat_long)?$value->lat_long:'';
            if(!empty($retailer_data['lat_long']))
            {
                $lat_lng = explode(',',$retailer_data['lat_long']);
                $lat = $lat_lng[0];
                $lng = $lat_lng[1];
            }
            else
            {
                $lat ='0.0' ;
                $lng ='0.0' ;
            }
            $retailer_data['lat'] = $lat;
            $retailer_data['lng'] = $lng;
            $retailer_data['location_id'] = "$value->location_id";
            $retailer_data['address'] = !empty($value->address)?$value->address:'';
            $retailer_data['email'] = !empty($value->email)?$value->email:'';
            $retailer_data['tin_no'] = $value->tin_no;
            $retailer_data['contact_per_name'] = !empty($value->contact_per_name)?$value->contact_per_name:'';
            $retailer_data['landline'] = !empty($value->other_numbers)?$value->other_numbers:$value->landline;
            $retailer_data['seq_id'] = "";
            $retailer_data['created_by'] = $value->user_name;
            $retailer_data['created_by_designation'] = $value->designation;
            $retailer_data['created_at'] = $value->created_on;
            $retailer_data['last_visit_date'] = "No Order book Yet";
            $retailer_data['beat_name'] = $value->beat_name;
            $outstanding = '0';
            $retailer_data['outstanding'] = "";
            $last_amt = '0';
            $retailer_data['last_amt'] = "";
            $retailer_data['achieved'] = '';
            $retailer_data['last_date'] = "no date";
            $retailer_data['verify_status'] = ($value->verfiy_retailer_status);
            $retailer_data['scheme_from_date'] = '';
            $retailer_data['scheme_to_date'] = '';
            $retailer_data['scheme_plan_name'] = '';
            $retailer_data['image_name'] = 'retailer_image/'.$value->image_name;

            $final_retailer[] = $retailer_data;
        }

  
	
		if($final_retailer){
			return response()->json([ 'response' =>TRUE,'result'=>$final_retailer]);
		}
		else
		{
			return response()->json([ 'response' =>FALSE,'result'=>array()]);
		}

    }



    public function productiveCallDetails(Request $request)
    {

    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

    	$user_id = $request->user_id; 
    	$company_id = $request->company_id; 
    	$from_date = $request->from_date; 
    	$to_date = $request->to_date; 
        $table_name = TableReturn::table_return($from_date,$to_date);


    	Session::forget('juniordata');		
        $check_junior_data=JuniorData::getJuniorUser($request->user_id,$request->company_id);
        Session::push('juniordata', $request->user_id);
		$junior_data_check = Session::get('juniordata');


		$productive_call = DB::table($table_name)	
					->select('retailer_id')	
					->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
					->where($table_name.'.company_id',$company_id)
					->whereIn('user_id',$junior_data_check)
					->where('call_status','=','1')
					->groupBy('retailer_id','date')
					->get();

		$productiveCall = array();
		foreach ($productive_call as $key => $value) {
			$productiveCall[$value->retailer_id] = $value->retailer_id;
		}

		$retailer_id_data = DB::table('retailer')->select('retailer.other_numbers','verfiy_retailer_status','sequence_id as seq_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'_role.rolename as designation','retailer.created_on as created_on','created_by_person_id','retailer.tin_no as tin_no','location_7.name as beat_name','retailer.id as id','lat_long','retailer.name as retailer_name','location_id','address','retailer.email as email','contact_per_name','landline','retailer.image_name')
                        ->join('location_7','location_7.id','=','retailer.location_id')
                        ->join('person','person.id','=','retailer.created_by_person_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->whereIn('retailer.id',$productiveCall)
                        ->where('retailer.company_id',$company_id)
                        ->where('location_7.company_id',$company_id)
                        ->where('_role.company_id',$company_id)
                        ->where('retailer_status',1)
                        ->groupBy('retailer.id')
                        ->get();

        $final_retailer = array();
        foreach($retailer_id_data as $key => $value)
        {
            $retailer_id = $value->id;
            $payment_collection_query = '0';
            $challan_data_query = '0';
            $retailer_amt  = '0';
            $retailer_data['retailer_id'] = "$value->id";
            $retailer_data['retailer_name'] = $value->retailer_name;
            $retailer_data['lat_long'] = !empty($value->lat_long)?$value->lat_long:'';
            if(!empty($retailer_data['lat_long']))
            {
                $lat_lng = explode(',',$retailer_data['lat_long']);
                $lat = $lat_lng[0];
                $lng = $lat_lng[1];
            }
            else
            {
                $lat ='0.0' ;
                $lng ='0.0' ;
            }
            $retailer_data['lat'] = $lat;
            $retailer_data['lng'] = $lng;
            $retailer_data['location_id'] = "$value->location_id";
            $retailer_data['address'] = !empty($value->address)?$value->address:'';
            $retailer_data['email'] = !empty($value->email)?$value->email:'';
            $retailer_data['tin_no'] = $value->tin_no;
            $retailer_data['contact_per_name'] = !empty($value->contact_per_name)?$value->contact_per_name:'';
            $retailer_data['landline'] = !empty($value->other_numbers)?$value->other_numbers:$value->landline;
            $retailer_data['seq_id'] = "";
            $retailer_data['created_by'] = $value->user_name;
            $retailer_data['created_by_designation'] = $value->designation;
            $retailer_data['created_at'] = $value->created_on;
            $retailer_data['last_visit_date'] = "No Order book Yet";
            $retailer_data['beat_name'] = $value->beat_name;
            $outstanding = '0';
            $retailer_data['outstanding'] = "";
            $last_amt = '0';
            $retailer_data['last_amt'] = "";
            $retailer_data['achieved'] = '';
            $retailer_data['last_date'] = "no date";
            $retailer_data['verify_status'] = ($value->verfiy_retailer_status);
            $retailer_data['scheme_from_date'] = '';
            $retailer_data['scheme_to_date'] = '';
            $retailer_data['scheme_plan_name'] = '';
            $retailer_data['image_name'] = 'retailer_image/'.$value->image_name;

            $final_retailer[] = $retailer_data;
        }

  
	
		if($final_retailer){
			return response()->json([ 'response' =>TRUE,'result'=>$final_retailer]);
		}
		else
		{
			return response()->json([ 'response' =>FALSE,'result'=>array()]);
		}

    }


    public function nonProductiveCallDetails(Request $request)
    {

    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

    	$user_id = $request->user_id; 
    	$company_id = $request->company_id; 
    	$from_date = $request->from_date; 
    	$to_date = $request->to_date; 
        $table_name = TableReturn::table_return($from_date,$to_date);


    	Session::forget('juniordata');		
        $check_junior_data=JuniorData::getJuniorUser($request->user_id,$request->company_id);
        Session::push('juniordata', $request->user_id);
		$junior_data_check = Session::get('juniordata');


		$non_productive_call = DB::table($table_name)	
					->select('retailer_id')	
					->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
					->where($table_name.'.company_id',$company_id)
					->whereIn('user_id',$junior_data_check)
					->where('call_status','=','0')
					->groupBy('retailer_id','date')
					->get();

		$nonproductiveCall = array();
		foreach ($non_productive_call as $key => $value) {
			$nonproductiveCall[$value->retailer_id] = $value->retailer_id;
		}

		$retailer_id_data = DB::table('retailer')
						->select('retailer.other_numbers','verfiy_retailer_status','sequence_id as seq_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'_role.rolename as designation','retailer.created_on as created_on','created_by_person_id','retailer.tin_no as tin_no','location_7.name as beat_name','retailer.id as id','lat_long','retailer.name as retailer_name','location_id','address','retailer.email as email','contact_per_name','landline','retailer.image_name')
                        ->join('location_7','location_7.id','=','retailer.location_id')
                        ->join('person','person.id','=','retailer.created_by_person_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->whereIn('retailer.id',$nonproductiveCall)
                        ->where('retailer.company_id',$company_id)
                        ->where('location_7.company_id',$company_id)
                        ->where('_role.company_id',$company_id)
                        ->where('retailer_status',1)
                        ->groupBy('retailer.id')
                        ->get();

        $final_retailer = array();
        foreach($retailer_id_data as $key => $value)
        {
            $retailer_id = $value->id;
            $payment_collection_query = '0';
            $challan_data_query = '0';
            $retailer_amt  = '0';
            $retailer_data['retailer_id'] = "$value->id";
            $retailer_data['retailer_name'] = $value->retailer_name;
            $retailer_data['lat_long'] = !empty($value->lat_long)?$value->lat_long:'';
            if(!empty($retailer_data['lat_long']))
            {
                $lat_lng = explode(',',$retailer_data['lat_long']);
                $lat = $lat_lng[0];
                $lng = $lat_lng[1];
            }
            else
            {
                $lat ='0.0' ;
                $lng ='0.0' ;
            }
            $retailer_data['lat'] = $lat;
            $retailer_data['lng'] = $lng;
            $retailer_data['location_id'] = "$value->location_id";
            $retailer_data['address'] = !empty($value->address)?$value->address:'';
            $retailer_data['email'] = !empty($value->email)?$value->email:'';
            $retailer_data['tin_no'] = $value->tin_no;
            $retailer_data['contact_per_name'] = !empty($value->contact_per_name)?$value->contact_per_name:'';
            $retailer_data['landline'] = !empty($value->other_numbers)?$value->other_numbers:$value->landline;
            $retailer_data['seq_id'] = "";
            $retailer_data['created_by'] = $value->user_name;
            $retailer_data['created_by_designation'] = $value->designation;
            $retailer_data['created_at'] = $value->created_on;
            $retailer_data['last_visit_date'] = "No Order book Yet";
            $retailer_data['beat_name'] = $value->beat_name;
            $outstanding = '0';
            $retailer_data['outstanding'] = "";
            $last_amt = '0';
            $retailer_data['last_amt'] = "";
            $retailer_data['achieved'] = '';
            $retailer_data['last_date'] = "no date";
            $retailer_data['verify_status'] = ($value->verfiy_retailer_status);
            $retailer_data['scheme_from_date'] = '';
            $retailer_data['scheme_to_date'] = '';
            $retailer_data['scheme_plan_name'] = '';
            $retailer_data['image_name'] = 'retailer_image/'.$value->image_name;

            $final_retailer[] = $retailer_data;
        }

  
	
		if($final_retailer){
			return response()->json([ 'response' =>TRUE,'result'=>$final_retailer]);
		}
		else
		{
			return response()->json([ 'response' =>FALSE,'result'=>array()]);
		}

    }


       public function patanjaliAttendanceAPI(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'date'=>'required',
            'token'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $company_id = '52';
        $date = $request->date;
        $token = $request->token;

        if($token == 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9'){
        $dailyAttendanceData = DB::table('user_daily_attendance')
        						->select('person.emp_code as EmployeeCode',DB::raw("DATE_FORMAT(work_date,'%m/%d/%Y %H:%i:%s') as Datetime"))
        						// ->select('person.emp_code as EmployeeCode',DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d') as Date"),DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as Time"))
        						->join('person','person.id','=','user_daily_attendance.user_id')
        						->where('user_daily_attendance.company_id',$company_id)
        						->where('person.company_id',$company_id)
								->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')='$date'")
								->groupBy('person.id')
								->get()->toArray();

			$finalData = array();
			foreach ($dailyAttendanceData as $dkey => $dvalue) {
				$Data['EmployeeCode'] = $dvalue->EmployeeCode;
				$Data['Datetime'] = preg_replace('/\//',"/",$dvalue->Datetime);

				// $explodeDate = explode('-',$dvalue->Date);
				// $explodeTime = explode(':',$dvalue->Time);
				// $Date = $explodeDate[1].'/'.$explodeDate[2].'/'.$explodeDate[0];
				// $Time = $explodeTime[0].':'.$explodeTime[1].':'.$explodeTime[2];
				// $Data['Datetime'] = $Date.' '.$Time;


				$finalData[] = $Data;
			}

			if(!empty($finalData))
			{
				return response()->json([ 'response' =>True,'message'=>'Record Found','Data'=>$finalData]);
			}
			else
			{
				return response()->json([ 'response' =>False,'message'=>'No Record Found','Data'=>array()]);
			}


        }else{
		return response()->json([ 'response' =>False,'message'=>'Token Not Matched!!','Data'=>array()]);

        }



    }

    public function gift_master_details_user_wise(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

       	$company_id = $request->company_id;
       	$user_id = $request->user_id;
    	// dd($company_id);
    	$data_fetch = DB::table('gift_master_logs')->where('user_id',$user_id)->where('company_id',$company_id)->get();
    	// dd($data_fetch);
    	if(COUNT($data_fetch)>0)
		{
			return response()->json([ 'response' =>True,'message'=>'Record Found','Data'=>$data_fetch]);
		}
		else
		{
			return response()->json([ 'response' =>False,'message'=>'No Record Found','Data'=>array()]);
		}
    }

    public function profile_details_aqua(Request $request){
    	$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'company_id'=>'required',
            'login_status'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $company_id = $request->company_id;
       	$user_id = $request->user_id;
       	$login_status = $request->login_status;
       
        
        if($login_status == 2){
        	$out_details = DB::table('dealer_person_login')
					->join('dealer','dealer.id','=','dealer_person_login.dealer_id')
					->join('dealer_personal_details','dealer_personal_details.dealer_id','=','dealer.id')
					->join('_role','_role.role_id','=','dealer_person_login.role_id')
					->join('location_3','location_3.id','=','dealer_person_login.state_id')
					->select('dealer.dealer_code','rolename','dealer.id as dealer_id','dealer.email','dealer.state_id','dpId','_role.role_id','person_name','dealer_person_login.company_id','phone','location_3.name as l3_name','dealer.tin_no as gst_no','dealer_personal_details.food_license as drug_license_no','dealer.address')
					->where('dealer.id',$user_id)
					->where('dealer.dealer_status',1)
					// ->where('person_password',DB::raw("AES_ENCRYPT('".trim($pass)."', '".Lang::get('common.db_salt')."')"))
					// ->whereRaw("AES_DECRYPT(pass, 'demo') = '$pass'")
					->where('activestatus',1)
					->get();
			$user_details = array();
        }
        else{
        	 $user_details = UserDetail::user_details_fetch_details($user_id,$company_id);

	        $user_id = $request->user_id;
	        $company_id = $request->company_id;
	     	Session::forget('juniordata');
	        $user_data_details=JuniorData::getJuniorUser($user_id,$company_id);
			$user_data = Session::get('juniordata');
	        // dd($user_data);
	        $out_details = [];
	        if(!empty($user_data)){
	        	foreach ($user_data as $key => $value) {
		        	$out_details[] = UserDetail::user_details_fetch_details($value,$company_id);
		        }
	        }
        }

       	if(COUNT($user_details)>0 || COUNT($out_details)>0)
		{
			return response()->json([ 'response' =>True,'message'=>'Record Found','Data'=>$user_details,'out_details'=>$out_details]);
		}
		else
		{
			return response()->json([ 'response' =>False,'message'=>'No Record Found','Data'=>array(),'out_details'=>$out_details]);
		}

    }
    // public function get_dashboard_api(Request $request)
    // {
    // 	$data_set = DB::table('purchase_order')
    // 				->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
    // 				->where('company_id',$company_id)
    // 				->where('')

    // }
    public function master_details_retailer_beat_ss_personal(Request $request){
		$validator=Validator::make($request->all(),[
			'company_id'=>'required',
			// 'user_id'=>'required',
		]);
		if($validator->fails())
		{
			return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}

	 	$golden_status = !empty($request->golden_status)?$request->golden_status:'0';
	 	$uphaar_status = !empty($request->uphaar_status)?$request->uphaar_status:'0';
		$company_id = !empty($request->company_id)?$request->company_id:'0';
		$beat_id = !empty($request->beat_id)?$request->beat_id:'0';
		$dealer_id = !empty($request->dealer_id)?$request->dealer_id:'0';
		$user_id = !empty($request->user_id)?$request->user_id:'0';
		$retailer_id = !empty($request->retailer_id)?$request->retailer_id:'0';
        $date = date('Y-m-d');
        $currmonth = date('Y-m');
		$retailers_details = array();
		$dealer_detail = array();
		$beat_details = array();
		if($golden_status != 1)
		{

			 $dealer_detail_data = DB::table('dealer')
				->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
				->select('dealer.*')
				->where('dealer.company_id',$company_id)
				->where('dealer_status',1);
				if(!empty($beat_id))
				{
					$dealer_detail_data->where('location_id',$beat_id);
				}
				if(!empty($user_id))
				{
					$dealer_detail_data->where('dealer_location_rate_list.user_id',$user_id);
				}
			$dealer_detail = $dealer_detail_data->groupBy('dealer.id')->get();

	 		$beat_detail_data = DB::table('dealer')
				->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
				->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
				->select('location_7.*')
				->where('dealer.company_id',$company_id)
				->where('location_7.company_id',$company_id)
				->where('dealer_status',1)
				->where('location_7.status',1);
				if(!empty($dealer_id))
				{
					$beat_detail_data->where('dealer.id',$dealer_id);
				}
				if(!empty($user_id))
				{
					$dealer_detail_data->where('dealer_location_rate_list.user_id',$user_id);
				}
			$beat_details = $beat_detail_data->groupBy('location_7.id')->get();

		 }

		 $upharClaimRetailer = array();
		 if($uphaar_status == 1){
		 	$upharClaimRetailer = DB::table('gift_retailer_details')
		 							->where('user_id',$user_id)
		 							->where('company_id',$company_id)
		 							->pluck('retailer_id')->toArray();
		 }

	 	$retailers_detail_data = DB::table('retailer')
			->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','retailer.location_id')
			->join('location_7','location_7.id','=','retailer.location_id')
			->select('retailer.id','retailer.dealer_id','retailer.name','retailer.is_golden_approved','retailer.contact_per_name','retailer.other_numbers','retailer.address','location_7.name as beat_name','retailer.tin_no','retailer.email','retailer.date_of_birth','retailer.owner_name','retailer.whatsapp_no','retailer.pin_no','retailer.date_of_anniversary','retailer.drug_licence')
			->where('retailer.company_id',$company_id)
			->where('location_7.company_id',$company_id)
			->where('retailer_status',1);
			
			if(!empty($beat_id))
			{
				$retailers_detail_data->where('retailer.location_id',$beat_id);
			}
			if(!empty($retailer_id))
			{
				$retailers_detail_data->where('retailer.id',$retailer_id);
			}
			if(!empty($golden_status))
			{
				$retailers_detail_data->where('is_golden',$golden_status);
			}
			if(!empty($user_id))
			{
				$retailers_detail_data->where('dealer_location_rate_list.user_id',$user_id);
			}
			if(!empty($dealer_id))
			{
				$retailers_detail_data->where('dealer_location_rate_list.dealer_id',$dealer_id);
			}
			if(!empty($upharClaimRetailer))
			{
				$retailers_detail_data->whereIn('retailer.id',$upharClaimRetailer);
			}
		$retailers_details = $retailers_detail_data->groupBy('retailer.id')->get();

		

		$check_retailer_productive_sales = DB::table('user_sales_order')
                                ->where('company_id',$company_id)
                                ->where('call_status',1)
                                ->whereRaw("date_format(date,'%Y-%m-%d')='$date'")
                                ->groupBy('retailer_id')
                                ->pluck('order_id','retailer_id')
                                ->toArray();

        $check_retailer_non_productive_sales = DB::table('user_sales_order')
                                ->where('company_id',$company_id)
                                ->where('call_status',0)
                                ->whereRaw("date_format(date,'%Y-%m-%d')='$date'")
                                ->groupBy('retailer_id')
                                ->pluck('order_id','retailer_id')
                                ->toArray();

		$finalRetailerDetails = array();
		if(!empty($retailers_details)){
			foreach ($retailers_details as $rkey => $rvalue) {
            		$retailer_id = $rvalue->id; 

            		   if(empty($check_retailer_productive_sales[$retailer_id]) && empty($check_retailer_non_productive_sales[$retailer_id])){
			                $sales_status = '0'; // for not sale anything
			            }elseif (!empty($check_retailer_productive_sales[$retailer_id])) {
			                $sales_status = '1'; // for productive sale
			            }elseif (!empty($check_retailer_non_productive_sales[$retailer_id])) {
			                $sales_status = '2'; // for visit only
			            }

			$lastSaleId = DB::table('user_sales_order')
                        ->select('user_sales_order.order_id')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->where('retailer_id',$retailer_id)
                        ->where('user_sales_order.company_id',$company_id)
                        ->groupBy('retailer_id')
                        ->orderBy('user_sales_order.id','DESC')
                        ->first();

            $lastDetails = array();
            if(!empty($lastSaleId)){
            $lastDetails = DB::table('user_sales_order')
                        ->select(DB::raw("concat_ws(' ',first_name,middle_name,last_name) as user_name"),DB::raw("SUM(user_sales_order_details.rate*quantity) as lastSale"),'date','time')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->where('user_sales_order.order_id',$lastSaleId->order_id)
                        ->where('user_sales_order.company_id',$company_id)
                        ->groupBy('user_sales_order.order_id')
                        ->first();
            }

            $lastDate = !empty($lastDetails->date)?$lastDetails->date:'';
            $lastTime = !empty($lastDetails->time)?$lastDetails->time:''; 


             $lastMonthDetails = DB::table('user_sales_order')
                        ->select(DB::raw("SUM(user_sales_order_details.rate*quantity) as Sale"),DB::raw("SUM(user_sales_order_details.quantity) as Quantity"))
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->where('user_sales_order.retailer_id',$retailer_id)
                        ->where('user_sales_order.company_id',$company_id)
                        ->whereRaw("date_format(date,'%Y-%m')='$currmonth'")
                        ->groupBy('user_sales_order.order_id')
                        ->first();

            $monthBill = !empty($lastMonthDetails->Sale)?$lastMonthDetails->Sale:'';
            $monthBillBox = !empty($lastMonthDetails->Quantity)?$lastMonthDetails->Quantity:'';

					$ret_details['id'] = $rvalue->id;
					$ret_details['dealer_id'] = $rvalue->dealer_id;
					$ret_details['name'] = $rvalue->name;
					$ret_details['is_golden_approved'] = $rvalue->is_golden_approved;
					$ret_details['contact_per_name'] = $rvalue->contact_per_name;
					$ret_details['contact_num'] = $rvalue->other_numbers;
					$ret_details['address'] = $rvalue->address;

					$ret_details['owner_name'] = $rvalue->owner_name;
					$ret_details['whatsapp_no'] = $rvalue->whatsapp_no;
					$ret_details['pin_code'] = $rvalue->pin_no;


					$ret_details['tin_no'] = $rvalue->tin_no;
					$ret_details['email'] = $rvalue->email;
					$ret_details['date_of_birth'] = $rvalue->date_of_birth;
					$ret_details['gst'] = $rvalue->tin_no;
					$ret_details['date_of_anniversary'] = $rvalue->date_of_anniversary;
					$ret_details['drug_licence'] = $rvalue->drug_licence;


					$ret_details['sale_status'] = $sales_status;
					$ret_details['last_invoice_date'] = $lastDate.' '.$lastTime;
					$ret_details['last_bill_value'] = !empty($lastDetails->lastSale)?$lastDetails->lastSale:'';
					$ret_details['sale_person'] = !empty($lastDetails->user_name)?$lastDetails->user_name:'';
					$ret_details['monthly_bill_value'] = $monthBill;
					$ret_details['total_bill_box'] = $monthBillBox;
					$ret_details['total_gift_deliever'] = '0';

					$finalRetailerDetails[] = $ret_details;
			}
			$retailers_details = $finalRetailerDetails;
		}

		$user_details = Person::join('person_details','person_details.person_id','=','person.id','inner')
                    ->join('person_login','person_login.person_id','=','person.id','inner')
                    ->join('_role','_role.role_id','=','person.role_id','inner')
                    ->join('location_3','person.state_id','=','location_3.id')
                    ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
					->join('retailer','retailer.location_id','=','dealer_location_rate_list.location_id')
                     ->select('person.id as user_id',DB::raw(" CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person_details.created_on as created_on','person_details.address as personaddress','person.town_id as town_name','person.head_quater_id as head_quater','person.*','person_login.person_status as person_status','person_login.person_username','person_login.person_image',DB::raw("AES_DECRYPT(person_password,'".Lang::get('common.db_salt')."') AS person_password"),'location_3.name as state','_role.rolename',DB::raw("(SELECT CONCAT_WS(' ',first_name,middle_name,last_name)  FROM person AS p1 
                        WHERE p1.id=person.person_id_senior LIMIT 1) as srname"),'person_details.deleted_deactivated_on as deactivate_date','person_login.last_mobile_access_on as last_sync')
                    ->where('person.company_id',$company_id)
                    ->where('location_3.company_id',$company_id)
                    ->where('_role.company_id',$company_id)
					->where('retailer.id',$retailer_id)
                    // ->where('person.id',$user_id)
                    ->where('person_status','=','1')
                    ->where('person.id','!=','1')
                    ->orderBy('first_name','ASC')
                    ->get();

    
        $set_out = array();

        if(!empty($user_details)){
            foreach ($user_details as $key => $value) {
                // code...
                $mobile = !empty($value->mobile)?$value->mobile:'-';
                $out_details['contact_head_quar']= !empty($value->head_quar)?$mobile.'&'.$value->head_quar:$mobile;
                $out_details['user_id']= !empty($value->user_id)?$value->user_id:'';
                $out_details['user_name']= !empty($value->user_name)?$value->user_name:'';
                $out_details['created_on']= !empty($value->created_on)?$value->created_on:'';
                $out_details['personaddress']= !empty($value->personaddress)?$value->personaddress:'';
                $out_details['town_name']= !empty($value->town_name)?$value->town_name:'';
                $out_details['head_quater']= !empty($value->head_quater)?$value->head_quater:'';
                $out_details['id']= !empty($value->id)?$value->id:'';
                $out_details['position_id']= !empty($value->position_id)?$value->position_id:'';
                $out_details['first_name']= !empty($value->first_name)?$value->first_name:'';
                $out_details['middle_name']= !empty($value->middle_name)?$value->middle_name:'';
                $out_details['last_name']= !empty($value->last_name)?$value->last_name:'';
                $out_details['company_id']= !empty($value->company_id)?$value->company_id:'';
                $out_details['role_id']= !empty($value->role_id)?$value->role_id:'';
                $out_details['person_id_senior']= !empty($value->person_id_senior)?$value->person_id_senior:'';
                $out_details['mobile']= !empty($value->mobile)?$value->mobile:'';
                $out_details['email']= !empty($value->email)?$value->email:'';
                $out_details['imei_number']= !empty($value->imei_number)?$value->imei_number:'';
                $out_details['version_code_name']= !empty($value->version_code_name)?$value->version_code_name:'';
                $out_details['state_id']= !empty($value->state_id)?$value->state_id:'';
                $out_details['town_id']= !empty($value->town_id)?$value->town_id:'';
                $out_details['head_quater_id']= !empty($value->head_quater_id)?$value->head_quater_id:'';
                $out_details['weekly_off_data']= !empty($value->weekly_off_data)?$value->weekly_off_data:'';
                $out_details['manual_attendance']= !empty($value->manual_attendance)?$value->manual_attendance:'';
                $out_details['is_mtp_enabled']= !empty($value->is_mtp_enabled)?$value->is_mtp_enabled:'';
                $out_details['today_att_enabled']= !empty($value->today_att_enabled)?$value->today_att_enabled:'';
                $out_details['today_att_enabled_at']= !empty($value->today_att_enabled_at)?$value->today_att_enabled_at:'';
                $out_details['product_division']= !empty($value->product_division)?$value->product_division:'';
                $out_details['rate_list_flag']= !empty($value->rate_list_flag)?$value->rate_list_flag:'';
                $out_details['emp_code']= !empty($value->emp_code)?$value->emp_code:'';
                $out_details['joining_date']= !empty($value->joining_date)?$value->joining_date:'';
                $out_details['resigning_date']= !empty($value->resigning_date)?$value->resigning_date:'';
                $out_details['head_quar']= !empty($value->head_quar)?$value->head_quar:'';
                $out_details['status']= !empty($value->status)?$value->status:'';
                $out_details['created_by']= !empty($value->created_by)?$value->created_by:'';
                $out_details['region_txt']= !empty($value->region_txt)?$value->region_txt:'';
                $out_details['dms_token']= !empty($value->dms_token)?$value->dms_token:'';
                $out_details['is_holiday_enabled']= !empty($value->is_holiday_enabled)?$value->is_holiday_enabled:'';
                $out_details['is_holiday_updated_by']= !empty($value->is_holiday_updated_by)?$value->is_holiday_updated_by:'';
                $out_details['is_holiday_updated_at']= !empty($value->is_holiday_updated_at)?$value->is_holiday_updated_at:'';
                $out_details['fcm_token']= !empty($value->fcm_token)?$value->fcm_token:'';
                $out_details['env_flag']= !empty($value->env_flag)?$value->env_flag:'';
                $out_details['person_status']= !empty($value->person_status)?$value->person_status:'';
                $out_details['person_username']= !empty($value->person_username)?$value->person_username:'';
                $out_details['person_image']= !empty($value->person_image)?$value->person_image:'';
                $out_details['person_password']= !empty($value->person_password)?$value->person_password:'';
                $out_details['state']= !empty($value->state)?$value->state:'';
                $out_details['rolename']= !empty($value->rolename)?$value->rolename:'';
                $out_details['srname']= !empty($value->srname)?$value->srname:'';
                $out_details['deactivate_date']= !empty($value->deactivate_date)?$value->deactivate_date:'';
                $out_details['last_sync']= !empty($value->last_sync)?$value->last_sync:'';
                $set_out[] = $out_details;
            }
        }

 		return response()->json([ 'response' =>True,'message'=>'Record Found','retailer_details'=>$retailers_details,'dealer_details'=>$dealer_detail,'beat_details'=>$beat_details,'user_details'=>$set_out]);

	}


	public function aquaDashboard(Request $request){
    	$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $company_id = $request->company_id;
       	$user_id = $request->user_id;
       	$curr_month = date('Y-m-d'); // change by dheeru
       	// $curr_month = date('Y-m');
       	$data_tc = DB::table('user_sales_order')
       			->select(DB::raw("COUNT(user_sales_order.id) as tc"))
				// ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$curr_month'")
				->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$curr_month'")
       			->where('company_id',$company_id)
       			->where('user_id',$user_id)
   				->first();

		$data_pc = DB::table('user_sales_order')
       			->select(DB::raw("COUNT(user_sales_order.id) as pc"))
				// ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$curr_month'")
				->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$curr_month'")
       			->where('company_id',$company_id)
       			->where('user_id',$user_id)
       			->where('call_status',1)
   				->first();


   		$purchase_order_value = DB::table('user_sales_order')
								->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
								->select(DB::raw("sum((rate*quantity)) as total_sale_value"))
								->where('user_sales_order.company_id',$company_id)
								->where('user_sales_order.user_id',$user_id)
								->where('user_sales_order_details.company_id',$company_id)
								// ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m')='$curr_month'")
								->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$curr_month'")
								->first();
        
        // $purchase_order_value = DB::table('user_primary_sales_order')
								// ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
								// ->select(DB::raw("sum((rate*quantity)+(cases*pr_rate)) as total_sale_value"))
								// ->where('user_primary_sales_order.company_id',$company_id)
								// ->where('user_primary_sales_order.created_person_id',$user_id)
								// ->where('user_primary_sales_order_details.company_id',$company_id)
								// // ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m')='$curr_month'")
								// ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')='$curr_month'")
								// ->first();

		$pc = !empty($data_pc->pc)?$data_pc->pc:'0';
		$tc = !empty($data_tc->tc)?$data_tc->tc:'0';
		$total_sale_value = !empty($purchase_order_value->total_sale_value)?$purchase_order_value->total_sale_value:'0';
     	$sendingArray = array("TC"=>$tc,"PC"=>$pc,"purchaseOrder"=>$total_sale_value,"dispatchOrder"=>'0');

       	if(COUNT($sendingArray)>0)
		{
			return response()->json([ 'response' =>True,'message'=>'Record Found','Data'=>$sendingArray]);
		}
		else
		{
			return response()->json([ 'response' =>False,'message'=>'No Record Found','Data'=>array()]);
		}

    }


    public function productDescriptionImage(Request $request){
    	$validator=Validator::make($request->all(),[
            'product_id'=>'required',
            'company_id'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $company_id = $request->company_id;
       	$product_id = $request->product_id;

       	 $descriptionImage = DB::table('sku_description_images')
                            ->where('company_id',$company_id)
                            ->where('product_id',$product_id)
                            ->groupBy('id')
                            ->get()->toArray();

        $descImage = array();
        foreach ($descriptionImage as $dkey => $dvalue) {
            $finalOutDesc['image_url'] = 'sku_description_images/'.$dvalue->image;
            $descImage[] = $finalOutDesc;
        }



       	if(!empty($descImage))
		{
			return response()->json([ 'response' =>True,'message'=>'Record Found','Data'=>$descImage]);
		}
		else
		{
			return response()->json([ 'response' =>False,'message'=>'No Record Found','Data'=>array()]);
		}

    }


     public function aqualabTrackDetails(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $company_id = $request->company_id;
		$user_id = $request->user_id;
		$from_date = $request->from_date;
		$to_date = $request->to_date;
		$curr_date = date('Y-m-d');

		$checkIsAdmin = DB::table('users')
						->where('id',$user_id)
						->where('company_id',$company_id)
						->first();

		if($checkIsAdmin->is_admin == '1'){
			$junior_data_check = '';
		}else{

			Session::forget('juniordata');		
	        $check_junior_data=JuniorData::getJuniorUser($user_id,$company_id);
	        Session::push('juniordata', $user_id);

			$junior_data_check = Session::get('juniordata');
		}

		// dd($junior_data_check);

		$attendanceDataQuery = DB::table('user_daily_attendance')
							->select(DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d') as work_date"),DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as work_time"),'lat_lng','track_addrs','battery_status','gps_status','user_id')
							->where('company_id',$company_id)
							->whereIn('user_id',$junior_data_check)
							->whereRaw("date_format(user_daily_attendance.work_date,'%Y-%m-%d')>='$from_date' AND date_format(user_daily_attendance.work_date,'%Y-%m-%d')<='$to_date'")
							->groupBy('work_date','user_id')
							->get()->toArray();
		$attendanceArray = array();
		foreach ($attendanceDataQuery as $adkey => $advalue) {

			$attArray['track_date'] = $advalue->work_date;
			$attArray['track_time'] = $advalue->work_time;
			$attArray['lat_lng'] = $advalue->lat_lng;
			$attArray['track_address'] = $advalue->track_addrs;
			$attArray['status'] = 'Attendance';
			$attArray['battery_status'] = !empty($advalue->battery_status)?$advalue->battery_status:'';
			$attArray['gps_status'] = !empty($advalue->gps_status == 0)?'ON':'OFF';

			$attendanceArray[$advalue->user_id.$advalue->work_date] = $attArray;

		}

		// dd($attendanceArray);


		$checkoutDataQuery = DB::table('check_out')
							->select(DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d') as work_date"),DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as work_time"),'lat_lng','attn_address as track_addrs','battery_status','gps_status','user_id')
							->where('company_id',$company_id)
							->whereIn('user_id',$junior_data_check)
							->whereRaw("date_format(check_out.work_date,'%Y-%m-%d')>='$from_date' AND date_format(check_out.work_date,'%Y-%m-%d')<='$to_date'")
							->groupBy('work_date','user_id')
							->get()->toArray();
		$checkoutArray = array();
		foreach ($checkoutDataQuery as $chkey => $chvalue) {

			$chkArray['track_date'] = $chvalue->work_date;
			$chkArray['track_time'] = $chvalue->work_time;
			$chkArray['lat_lng'] = $chvalue->lat_lng;
			$chkArray['track_address'] = $chvalue->track_addrs;
			$chkArray['status'] = 'Check Out';
			$chkArray['battery_status'] = $chvalue->battery_status;
			$chkArray['gps_status'] = !empty($chvalue->gps_status == 0)?'ON':'OFF';

			$checkoutArray[$chvalue->user_id.$chvalue->work_date] = $chkArray;

		}


		$cordinatesDetails = DB::table('user_work_tracking')
						->whereIn('user_id',$junior_data_check)
						->whereRaw("date_format(user_work_tracking.track_date,'%Y-%m-%d')>='$from_date' AND date_format(user_work_tracking.track_date,'%Y-%m-%d')<='$to_date'")
						->where('lat_lng','!=','NULL')
						->where('lat_lng','!=','')
						->where('lat_lng','!=','0,0')
						->where('lat_lng','!=','0.0,0.0')
						->groupBy('id')
						->get()->toArray();

		$TrackArr = array();
		foreach ($cordinatesDetails as $ckey => $cvalue) {

			$attData = !empty($attendanceArray[$cvalue->user_id.$cvalue->track_date])?$attendanceArray[$cvalue->user_id.$cvalue->track_date]:array();
			$chkData = !empty($checkoutArray[$cvalue->user_id.$cvalue->track_date])?$checkoutArray[$cvalue->user_id.$cvalue->track_date]:array();


			if($cvalue->status != 'Attendance' && $cvalue->status != 'CheckOut'){
				if(!empty($attData) && !empty($chkData)){
					if(($cvalue->track_time > $attData['track_time']) && ($cvalue->track_time < $chkData['track_time'])){
					$ocval['track_date'] = $cvalue->track_date;
					$ocval['track_time'] = $cvalue->track_time;
					$ocval['lat_lng'] = $cvalue->lat_lng;
					$ocval['track_address'] = $cvalue->track_address;
					$ocval['status'] = $cvalue->status;	
					$ocval['battery_status'] = $cvalue->battery_status;
					$ocval['gps_status'] = !empty($cvalue->gps_status == 0)?'ON':'OFF';

					$TrackArr[$cvalue->user_id.$cvalue->track_date][] = $ocval;

					}
				}
				elseif (!empty($attData) && ($cvalue->track_time > $attData['track_time']) && ($cvalue->track_time < '21:00:00')) {
					$ocval['track_date'] = $cvalue->track_date;
					$ocval['track_time'] = $cvalue->track_time;
					$ocval['lat_lng'] = $cvalue->lat_lng;
					$ocval['track_address'] = $cvalue->track_address;
					$ocval['status'] = $cvalue->status;
					$ocval['battery_status'] = $cvalue->battery_status;
					$ocval['gps_status'] = !empty($cvalue->gps_status == 0)?'ON':'OFF';

					$TrackArr[$cvalue->user_id.$cvalue->track_date][] = $ocval;

				}
			}
			// $TrackArr[$cvalue->user_id.$cvalue->track_date][] = $ocval;
		}


		// $juniorTrackDataDetails = DB::table('user_work_tracking')
		// 					->join('person','person.id','=','user_work_tracking.user_id')
		// 					->join('person_login','person_login.person_id','=','person.id')
		// 					->join('_role','_role.role_id','=','person.role_id')
		// 					->select('user_id',DB::raw("CONCAT(first_name,' ',middle_name,' ',last_name) as user_name"),'person_login.person_image','person.mobile','_role.rolename','track_date')
		// 					->where('user_work_tracking.company_id',$company_id);
		// 					if(!empty($junior_data_check)){
		// 						$juniorTrackDataDetails->whereIn('user_work_tracking.user_id',$junior_data_check);
		// 						// $juniorTrackDataDetails->where('user_work_tracking.user_id','3228');
		// 					}

		// $juniorTrackData = $juniorTrackDataDetails->whereRaw("date_format(user_work_tracking.track_date,'%Y-%m-%d')>='$from_date' AND date_format(user_work_tracking.track_date,'%Y-%m-%d')<='$to_date'")
		// 					->groupBy('user_work_tracking.user_id','user_work_tracking.track_date')
		// 					->orderBy('user_name','ASC')
		// 					->orderBy('track_date','ASC')
		//  					->get()->toArray();


		$juniorTrackDataDetails = DB::table('user_daily_attendance')
							->join('person','person.id','=','user_daily_attendance.user_id')
							->join('person_login','person_login.person_id','=','person.id')
							->join('_role','_role.role_id','=','person.role_id')
							->select('user_id',DB::raw("CONCAT(first_name,' ',middle_name,' ',last_name) as user_name"),'person_login.person_image','person.mobile','_role.rolename',DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d') as track_date"))
							->where('user_daily_attendance.company_id',$company_id);
							if(!empty($junior_data_check)){
								$juniorTrackDataDetails->whereIn('user_daily_attendance.user_id',$junior_data_check);
								// $juniorTrackDataDetails->where('user_daily_attendance.user_id','3228');
							}

		$juniorTrackData = $juniorTrackDataDetails->whereRaw("date_format(user_daily_attendance.work_date,'%Y-%m-%d')>='$from_date' AND date_format(user_daily_attendance.work_date,'%Y-%m-%d')<='$to_date'")
							->groupBy('user_daily_attendance.user_id','user_daily_attendance.work_date')
							// ->orderBy('user_name','ASC')
							// ->orderBy('track_date','ASC')
							->orderBy('role_sequence','ASC')
		 					->get()->toArray();

		$final_array = array();
		foreach ($juniorTrackData as $jtdkey => $jtdvalue) {

			$user_id = !empty($jtdvalue->user_id)?$jtdvalue->user_id:'';
			$user_name = !empty($jtdvalue->user_name)?$jtdvalue->user_name:'';
			$role_name = !empty($jtdvalue->rolename)?$jtdvalue->rolename:'';
			$mobile_no = !empty($jtdvalue->mobile)?$jtdvalue->mobile:'';
			$track_date = !empty($jtdvalue->track_date)?$jtdvalue->track_date:'';

			$out['user_id'] = $jtdvalue->user_id;
			$out['user_name'] = $user_name.'/'.$role_name.'  | '.$mobile_no;
			$out['mobile'] = !empty($jtdvalue->mobile)?$jtdvalue->mobile:'';
			$out['track_date'] = !empty($jtdvalue->track_date)?$jtdvalue->track_date:'';

			$attData = !empty($attendanceArray[$user_id.$track_date])?$attendanceArray[$user_id.$track_date]:array();
			$chkData = !empty($checkoutArray[$user_id.$track_date])?$checkoutArray[$user_id.$track_date]:array();
			$lvTrack = !empty($TrackArr[$user_id.$track_date])?$TrackArr[$user_id.$track_date]:array();


			$finaltrackArray = array();
			if(!empty($attData) || !empty($lvTrack) || !empty($chkData)){
				$finaltrackArray = array_merge(array($attData),$lvTrack,array($chkData));
			}

			$filterfinaltrackArray = array_filter($finaltrackArray);


			$finalLastTrackArray = array();
			if(!empty($filterfinaltrackArray)){
				foreach ($filterfinaltrackArray as $fkey => $fvalue) {
					
					// if(empty($fvalue['track_time']))
					// {
					// 	dd($user_id.$track_date);
					// }

                    $unix_time = strtotime($fvalue['track_time']); 
                    $startTime = $unix_time;

                    if($fkey == 0){
	                    $finalLastTrackArray[] = $fvalue;
	                    $plusthirtytime = strtotime(date('H:i:s',strtotime('+10 minutes',$unix_time))); // as said by pawan sir
                    }

                    if($plusthirtytime <= $startTime){
                      $finalLastTrackArray[] = $fvalue;
                      $plusthirtytime = strtotime(date('H:i:s',strtotime('+10 minutes',$unix_time)));
                    }

				}
			}
		

		
			$out['track_count'] = !empty($finalLastTrackArray)?COUNT($finalLastTrackArray):'0';

			$out['trackArray'] = !empty($finalLastTrackArray)?$finalLastTrackArray:array();


			// $out['track_count'] = !empty($TrackArr[$user_id.$track_date])?COUNT($TrackArr[$user_id.$track_date]):'0';

			// $out['trackArray'] = !empty($TrackArr[$user_id.$track_date])?$TrackArr[$user_id.$track_date]:array();

			

		      // if($jtdvalue->person_image != NULL){
		      // $out['profile_image'] = "users-profile/".$jtdvalue->person_image;
		      // }else{
		      // $out['profile_image'] = "msell/images/avatars/profile-pic.jpg";
		      // }

			$final_array[] = $out;
			
		}


		// dd($final_array);

		if(!empty($final_array)){
        	return response()->json([ 'response' =>TRUE,'trackDetails'=>$final_array]);

		}else{
        	return response()->json([ 'response' =>FALSE,'trackDetails'=>$final_array]);

		}



    }




    public function UserCatalogPdfDetails(Request $request)
    {

        $validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

            $company_id = $request->company_id;
            $user_id = $request->user_id;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            
            $personDetail = DB::table('person')
                        ->select('person.id as person_id',DB::raw("CONCAT(first_name,' ',middle_name,' ',last_name) as user_name"),'person.mobile as mobile','state_id')
                        ->where('company_id',$company_id)
                        ->where('person.id',$user_id)
                        ->first();

            $distributor = DB::table('dealer')
            			->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                        ->select('dealer.id as dealer_id','dealer.name as dealer_name','dealer.other_numbers as dealer_mobile','dealer.address','dealer.state_id')
                        ->where('dealer.company_id',$company_id)
                        ->where('dealer_location_rate_list.user_id',$user_id)
                        ->get();

                        // dd($distributor);

            $stateId = $personDetail->state_id;

            $productMrp = DB::table('product_rate_list')
                            ->where('company_id',$company_id)
                            ->where('state_id',$stateId)
                            ->pluck('mrp_pcs','product_id');
            /////////////////////////////////// retailer category sale details starts ///////////////////////////
            $retailerCategoryWiseSales = DB::table('user_sales_order')
                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                                        ->select('catalog_2.id as category_id','catalog_2.name as category_name','user_id as user_id')
                                        ->where('user_sales_order.user_id',$user_id)
                                        ->where('user_sales_order.company_id',$company_id)
                                        ->where('user_sales_order_details.company_id',$company_id)
                                        ->where('catalog_product.company_id',$company_id)
                                        ->where('catalog_2.company_id',$company_id)
                                        ->whereRaw("date_format(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND date_format(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                                        ->groupBy('user_id','catalog_2.id')
                                        ->get();

            // dd($retailerCategoryWiseSales);


            $finalRetailerCategoryOut = array();
            foreach ($retailerCategoryWiseSales as $rcwkey => $rcwvalue) {
                $retailerCategoryOut[$rcwvalue->user_id][$rcwvalue->category_id]['user_id'] = $rcwvalue->user_id;
                $retailerCategoryOut[$rcwvalue->user_id][$rcwvalue->category_id]['category_id'] = $rcwvalue->category_id;
                $retailerCategoryOut[$rcwvalue->user_id][$rcwvalue->category_id]['category_name'] = $rcwvalue->category_name;

                $finalRetailerCategoryOut = $retailerCategoryOut;
            }




            ///////////////////////////////////retailer category sale details ends ///////////////////////////



                /////////////////////////////////// retailer category product sale details starts ///////////////////////////
            $retailerCategoryProductWiseSales = DB::table('user_sales_order')
                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                                        ->select('catalog_2.id as category_id','catalog_2.name as category_name','user_id as user_id','user_sales_order_details.product_id','catalog_product.name as product_name','user_sales_order_details.rate','user_sales_order_details.rate as case_rate','user_sales_order_details.quantity','user_sales_order_details.case_qty','user_sales_order_details.scheme_qty',DB::raw("((user_sales_order_details.rate*user_sales_order_details.quantity)+(user_sales_order_details.rate*user_sales_order_details.case_qty)) as finalSale"))
                                        // ->where('user_sales_order.dealer_id',$distributor_id)
                                        ->where('user_sales_order.user_id',$user_id)
                                        ->where('user_sales_order.company_id',$company_id)
                                        ->where('user_sales_order_details.company_id',$company_id)
                                        ->where('catalog_product.company_id',$company_id)
                                        ->where('catalog_2.company_id',$company_id)
                                        ->whereRaw("date_format(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND date_format(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                                        ->groupBy('user_id','catalog_2.id','user_sales_order_details.product_id')
                                        ->get();

            $finalRetailerCategoryProductOut = array();
            foreach ($retailerCategoryProductWiseSales as $rcwpkey => $rcwpvalue) {

                $proMrp = !empty($productMrp[$rcwpvalue->product_id])?$productMrp[$rcwpvalue->product_id]:'0';


                $retailerCategoryProductOut[$rcwpvalue->user_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['user_id'] = $rcwpvalue->user_id;
                $retailerCategoryProductOut[$rcwpvalue->user_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['category_id'] = $rcwpvalue->category_id;
                $retailerCategoryProductOut[$rcwpvalue->user_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['category_name'] = $rcwpvalue->category_name;
                $retailerCategoryProductOut[$rcwpvalue->user_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['product_id'] = $rcwpvalue->product_id;
                $retailerCategoryProductOut[$rcwpvalue->user_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['product_name'] = $rcwpvalue->product_name;
                $retailerCategoryProductOut[$rcwpvalue->user_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['rate'] = $rcwpvalue->rate;
                $retailerCategoryProductOut[$rcwpvalue->user_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['case_rate'] = $rcwpvalue->case_rate;
                $retailerCategoryProductOut[$rcwpvalue->user_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['mrp'] = $proMrp;
                $retailerCategoryProductOut[$rcwpvalue->user_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['quantity'] = $rcwpvalue->quantity;
                $retailerCategoryProductOut[$rcwpvalue->user_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['case_quantity'] = $rcwpvalue->case_qty;
                $retailerCategoryProductOut[$rcwpvalue->user_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['scheme_quantity'] = $rcwpvalue->scheme_qty;
                $retailerCategoryProductOut[$rcwpvalue->user_id][$rcwpvalue->category_id][$rcwpvalue->product_id]['finalSale'] = $rcwpvalue->finalSale;

                $finalRetailerCategoryProductOut = $retailerCategoryProductOut;
            }

            ///////////////////////////////////retailer category product sale details ends ///////////////////////////

            // dd($finalRetailerCategoryProductOut);



            $retailerCategoryProductSaleDetails = DB::table('user_sales_order')
                                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                // ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                                                ->join('person','person.id','=','user_sales_order.user_id')
                                                ->join('person_details','person_details.person_id','=','person.id')
                                                ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id as user_id','person.mobile as landline','person_details.address as track_address')
                                                ->where('user_sales_order.user_id',$user_id)
                                                ->where('user_sales_order.company_id',$company_id)
                                                ->where('user_sales_order_details.company_id',$company_id)
                                                ->whereRaw("date_format(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND date_format(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                                                ->groupBy('user_id')
                                                ->get();

            $retailerOut = array();
            foreach ($retailerCategoryProductSaleDetails as $rcpkey => $rcpvalue) {
                    
                $userId = $rcpvalue->user_id;
                $user_name = $rcpvalue->user_name;
                $landline = $rcpvalue->landline;
                $track_address = $rcpvalue->track_address;  

                $retailerOut[$userId]['user_id'] = $userId;
                $retailerOut[$userId]['user_name'] = $user_name;
                $retailerOut[$userId]['landline'] = $landline;
                $retailerOut[$userId]['track_address'] = $track_address;


                $categoryDetailsArray = !empty($finalRetailerCategoryOut[$userId])?$finalRetailerCategoryOut[$userId]:array();

                $categoryDetailsArrayFinal = array();
                foreach ($categoryDetailsArray as $cdakey => $cdavalue) {
                    $categoryDetailOut[$cdavalue['category_id']]['category_id'] = $cdavalue['category_id'];
                    $categoryDetailOut[$cdavalue['category_id']]['category_name'] = $cdavalue['category_name'];

                    $categoryDetailOut[$cdavalue['category_id']]['productDetails'] = !empty($finalRetailerCategoryProductOut[$userId][$cdavalue['category_id']])?$finalRetailerCategoryProductOut[$userId][$cdavalue['category_id']]:array();

                    $categoryDetailsArrayFinal = $categoryDetailOut;


                }

                $retailerOut[$userId]['catalogDetails'] = $categoryDetailsArrayFinal;

            }


                        // dd($retailerOut);



        if(!empty($retailerOut)){
            return response()->json([ 'response' =>TRUE,'personDetail'=>$personDetail,'dealerDetails'=>$distributor,'userWiseProductsDetails'=>$retailerOut]);
        }
        else
        {
            return response()->json([ 'response' =>FALSE,'personDetail'=>$personDetail,'dealerDetails'=>array(),'userWiseProductsDetails'=>array()]);
        }

    }


     public function purchaseOrderStatus(Request $request){
    	$validator=Validator::make($request->all(),[
            'dealer_id'=>'required',
            'company_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $company_id = $request->company_id;
       	$dealer_id = $request->dealer_id;
       	$from_date = $request->from_date;
       	$to_date = $request->to_date;

       	$orderReason = DB::table('_dms_reason')
       					->where('company_id',$company_id)
       					->pluck('name','id');

       	$allOrders = DB::table('purchase_order')
       				->select('sale_date','dms_order_reason_id','order_id')
       				->where('dealer_id',$dealer_id)
       				->where('company_id',$company_id)
       				->whereRaw("DATE_FORMAT(purchase_order.sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(purchase_order.sale_date,'%Y-%m-%d')<='$to_date'")
       				->groupBy('order_id')
       				->get();




       

        $finalOut = array();
        foreach ($allOrders as $dkey => $dvalue) {

        	$dms_order_reason_id = $dvalue->dms_order_reason_id;

        	$out['order_id'] = $dvalue->order_id;
        	$out['sale_date'] = $dvalue->sale_date;
        	$out['dms_order_reason_id'] = $dvalue->dms_order_reason_id;
        	$out['status'] = !empty($orderReason[$dms_order_reason_id])?$orderReason[$dms_order_reason_id]:'Pending';

            $finalOut[] = $out;
        }



       	if(!empty($finalOut))
		{
			return response()->json([ 'response' =>True,'message'=>'Record Found','Data'=>$finalOut]);
		}
		else
		{
			return response()->json([ 'response' =>False,'message'=>'No Record Found','Data'=>array()]);
		}

    }



}
