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

class AnalysisController extends Controller
{
    public $successStatus = 401;
    public $response_true = True;
    public $response_false = False;



    public function overAllRetailerDetails(Request $request)
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

    	$beat_id = $request->beat_id; 
    	$flag = $request->flag;  // 1 for total retailer and 2 for not visited


    	Session::forget('juniordata');		
        $check_junior_data=JuniorData::getJuniorUser($request->user_id,$request->company_id);
        Session::push('juniordata', $request->user_id);
		$junior_data_check = Session::get('juniordata');

		if($flag == 1){
		$total_retailer_data = DB::table('dealer_location_rate_list')
						->select('retailer.id','retailer.name as retailer_name','retailer.address','retailer.class')
						->join('retailer','retailer.location_id','=','dealer_location_rate_list.location_id')
						->whereIn('dealer_location_rate_list.user_id',$junior_data_check)
						->where('dealer_location_rate_list.company_id',$company_id)
						->where('retailer.company_id',$company_id);
						if(!empty($beat_id)){
							$total_retailer_data->where('retailer.location_id',$beat_id);
						}
						
		$total_retailer = $total_retailer_data->groupBy('retailer.id')->get();
		}
		elseif($flag == 2){
			$total_visit_retailer = DB::table($table_name)
                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
									->where($table_name.'.company_id',$company_id)
									->whereIn($table_name.'.user_id',$junior_data_check)
									->groupBy('retailer_id')
									->pluck('retailer_id');

			$total_retailer_data = DB::table('dealer_location_rate_list')
							->select('retailer.id','retailer.name as retailer_name','retailer.address','retailer.class')
							->join('retailer','retailer.location_id','=','dealer_location_rate_list.location_id')
							->whereIn('dealer_location_rate_list.user_id',$junior_data_check)
							->where('dealer_location_rate_list.company_id',$company_id)
							->where('retailer.company_id',$company_id)
							->whereNotIn('retailer.id',$total_visit_retailer);
							if(!empty($beat_id)){
								$total_retailer_data->where('retailer.location_id',$beat_id);
							}
							
			$total_retailer = $total_retailer_data->groupBy('retailer.id')->get();
		}

		$retCat = DB::table('_retailer_outlet_category')
				->where('company_id',$company_id)
				->pluck('outlet_category','id');

		$lastSaleData = DB::table('user_sales_order')	
    				->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
    				->whereIn('user_sales_order.user_id',$junior_data_check)
    				->where('user_sales_order.company_id',$company_id)
    				->groupBy('retailer_id')
    				->pluck(DB::raw("MAX(date) as lastDate"),'user_sales_order.retailer_id');

        $final_retailer = array();
        foreach($total_retailer as $key => $value)
        {

            $retailer_id = $value->id;

            $days = '';
        	$date1 = date('Y-m-d');
			$date2 = !empty($lastSaleData[$retailer_id])?$lastSaleData[$retailer_id]:'';

			$diff = abs(strtotime($date2) - strtotime($date1));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

			if($days == '0'){
    		$retailer_data['last_visit'] = 'Visited Today';
			}elseif($days > '0'){
    		$retailer_data['last_visit'] = $days.' Days Ago';
			}else{
    		$retailer_data['last_visit'] = 'Not Visited Yet';
			}


            $retailer_data['retailer_id'] = "$value->id";
            $retailer_data['retailer_name'] = $value->retailer_name;
            $retailer_data['address'] = !empty($value->address)?$value->address:'';
            $retailer_data['retailer_category'] = !empty($retCat[$value->class])?$retCat[$value->class]:'';
            $retailer_data['last_sale_value'] = "";
            $retailer_data['last_visited_by'] = "";
        
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


    public function overAllBeatDetails(Request $request)
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

        $flag = $request->flag; // 1 for total beat, 2 for visited , 3 for not visited

    	$user_id = $request->user_id; 
    	$company_id = $request->company_id; 
    	$from_date = $request->from_date; 
    	$to_date = $request->to_date; 
        $table_name = TableReturn::table_return($from_date,$to_date);


    	Session::forget('juniordata');		
        $check_junior_data=JuniorData::getJuniorUser($request->user_id,$request->company_id);
        Session::push('juniordata', $request->user_id);
		$junior_data_check = Session::get('juniordata');

		if($flag == 1){
		$total_beat = DB::table('dealer_location_rate_list')
							->select('location_7.id as beat_id','location_7.name as beat_name',DB::raw("COUNT(DISTINCT dealer_location_rate_list.dealer_id) as dbcount"),DB::raw("COUNT(DISTINCT retailer.id) as retailercount"))
							->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
							->leftJoin('retailer','retailer.location_id','=','location_7.id')
							->whereIn('dealer_location_rate_list.user_id',$junior_data_check)
							->where('dealer_location_rate_list.company_id',$company_id)
							->where('location_7.company_id',$company_id)
							->groupBy('location_7.id')
							->get();
		}elseif($flag == 2){
			$total_beat = DB::table($table_name)
									->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
									->join('location_7','location_7.id','=',$table_name.'.location_id')
									->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','location_7.id')
									->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
									->leftJoin('retailer','retailer.location_id','=','location_7.id')
									->select('location_7.id as beat_id','location_7.name as beat_name',DB::raw("COUNT(DISTINCT dealer_location_rate_list.dealer_id) as dbcount"),DB::raw("COUNT(DISTINCT retailer.id) as retailercount"))
                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
									->where('dealer_status',1)
									->where('dealer_location_rate_list.company_id',$company_id)
									->where($table_name.'.company_id',$company_id)
									->whereIn($table_name.'.user_id',$junior_data_check)
									->groupBy('location_7.id')
									->get();
		}elseif($flag == 3){
			$total_visit_beat = DB::table($table_name)
									->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
									->join('location_7','location_7.id','=',$table_name.'.location_id')
									->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','location_7.id')
									->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
									->leftJoin('retailer','retailer.location_id','=','location_7.id')
									// ->select('location_7.id as beat_id')
                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
									->where('dealer_status',1)
									->where('dealer_location_rate_list.company_id',$company_id)
									->where($table_name.'.company_id',$company_id)
									->whereIn($table_name.'.user_id',$junior_data_check)
									->groupBy('location_7.id')
									->pluck('location_7.id');

			$total_beat = DB::table('dealer_location_rate_list')
							->select('location_7.id as beat_id','location_7.name as beat_name',DB::raw("COUNT(DISTINCT dealer_location_rate_list.dealer_id) as dbcount"),DB::raw("COUNT(DISTINCT retailer.id) as retailercount"))
							->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
							->leftJoin('retailer','retailer.location_id','=','location_7.id')
							->whereIn('dealer_location_rate_list.user_id',$junior_data_check)
							->where('dealer_location_rate_list.company_id',$company_id)
							->where('location_7.company_id',$company_id)
							->whereNotIn('location_7.id',$total_visit_beat)
							->groupBy('location_7.id')
							->get();

		}else{
				$total_beat = DB::table('dealer_location_rate_list')
							->select('location_7.id as beat_id','location_7.name as beat_name',DB::raw("COUNT(DISTINCT dealer_location_rate_list.dealer_id) as dbcount"),DB::raw("COUNT(DISTINCT retailer.id) as retailercount"))
							->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
							->leftJoin('retailer','retailer.location_id','=','location_7.id')
							->whereIn('dealer_location_rate_list.user_id',$junior_data_check)
							->where('dealer_location_rate_list.company_id',$company_id)
							->where('location_7.company_id',$company_id)
							->groupBy('location_7.id')
							->get();
		}

		$lastVisit = DB::table($table_name)
							->whereIn($table_name.'.user_id',$junior_data_check)
							->where($table_name.'.company_id',$company_id)
							->where($table_name.'.company_id',$company_id)
							->groupBy($table_name.'.location_id')
							->pluck(DB::raw("MAX(".$table_name.".date) as lastVisit"),$table_name.'.location_id');

		// dd($lastVisit);


        $final_retailer = array();
        foreach($total_beat as $key => $value)
        {


            $retailer_data['beat_id'] = "$value->beat_id";
            $retailer_data['beat_name'] = $value->beat_name;
            $retailer_data['dbcount'] = $value->dbcount;
            $retailer_data['retailercount'] = $value->retailercount;

            $date1 = date('Y-m-d');
			$date2 = !empty($lastVisit[$value->beat_id])?$lastVisit[$value->beat_id]:'';
			if(empty($date2)){
	    		$retailer_data['last_visit'] = 'Not Visited Yet';
			}else{
				$diff = abs(strtotime($date2) - strtotime($date1));
				$years = floor($diff / (365*60*60*24));
				$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
				$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

				if($days == '0'){
	    		$retailer_data['last_visit'] = 'Visited Today';
				}elseif($days > '0'){
	    		$retailer_data['last_visit'] = $days.' Days Ago';
				}else{
	    		$retailer_data['last_visit'] = 'Not Visited Yet';
				}
			}
          

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


    public function overAllTotalProductiveDetails(Request $request)
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
        $check = DB::table('app_other_module_assign')->where('company_id',$request->company_id)->where('module_id',6)->first();



    	Session::forget('juniordata');		
        $check_junior_data=JuniorData::getJuniorUser($request->user_id,$request->company_id);
        Session::push('juniordata', $request->user_id);
		$junior_data_check = Session::get('juniordata');

		if(empty($check)){
		$retailer_sale_query = DB::table($table_name)
									->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
									->join('dealer','dealer.id','=',$table_name.'.dealer_id')
									->join('retailer','retailer.id','=',$table_name.'.retailer_id')
									->join('person','person.id','=',$table_name.'.user_id')
									->select('retailer.id','retailer.name as retailer_name','retailer.address',$table_name.'.date','retailer.class',DB::raw("MAX(".$table_name.".date) as lastVisit"),DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("sum(rate*quantity) as total_sale_value"))
                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
									->where('retailer_status',1)
									->whereIn('user_id',$junior_data_check)
									->where($table_name.'.company_id',$company_id)
									->where('dealer.company_id',$company_id)
									->where('retailer.company_id',$company_id)
									->groupBy('retailer_id')
									->orderBy($table_name.'.date','DESC')
									->get();
		}else{
				$retailer_sale_query = DB::table($table_name)
									->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
									->join('dealer','dealer.id','=',$table_name.'.dealer_id')
									->join('retailer','retailer.id','=',$table_name.'.retailer_id')
									->join('person','person.id','=',$table_name.'.user_id')
									->select('retailer.id','retailer.name as retailer_name','retailer.address',$table_name.'.date','retailer.class',DB::raw("MAX(".$table_name.".date) as lastVisit"),DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("sum(final_secondary_rate*final_secondary_qty) as total_sale_value"))
                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
									->where('retailer_status',1)
									->whereIn('user_id',$junior_data_check)
									->where($table_name.'.company_id',$company_id)
									->where('dealer.company_id',$company_id)
									->where('retailer.company_id',$company_id)
									->groupBy('retailer_id')
									->orderBy($table_name.'.date','DESC')
									->get();
		}

		$retCat = DB::table('_retailer_outlet_category')
				->where('company_id',$company_id)
				->pluck('outlet_category','id');



        $final_retailer = array();
        foreach($retailer_sale_query as $key => $value)
        {
              $retailer_id = $value->id;

            $days = '';
        	$date1 = date('Y-m-d');
			$date2 = $value->lastVisit;

			$diff = abs(strtotime($date2) - strtotime($date1));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

			if($days == '0'){
    		$retailer_data['last_visit'] = 'Visited Today';
			}elseif($days > '0'){
    		$retailer_data['last_visit'] = $days.' Days Ago';
			}else{
    		$retailer_data['last_visit'] = 'Not Visited Yet';
			}

            $retailer_data['retailer_id'] = "$value->id";
            $retailer_data['retailer_name'] = $value->retailer_name;
            $retailer_data['address'] = !empty($value->address)?$value->address:'';
            $retailer_data['retailer_category'] = !empty($retCat[$value->class])?$retCat[$value->class]:'';;
            $retailer_data['last_sale_value'] = $value->total_sale_value;
            $retailer_data['last_visited_by'] = $value->user_name;
        
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




    public function overAllTotalNonProductiveDetails(Request $request)
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
        $check = DB::table('app_other_module_assign')->where('company_id',$request->company_id)->where('module_id',6)->first();



    	Session::forget('juniordata');		
        $check_junior_data=JuniorData::getJuniorUser($request->user_id,$request->company_id);
        Session::push('juniordata', $request->user_id);
		$junior_data_check = Session::get('juniordata');

		if(empty($check)){
		$retailer_sale_query = DB::table($table_name)
									// ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
									->join('dealer','dealer.id','=',$table_name.'.dealer_id')
									->join('retailer','retailer.id','=',$table_name.'.retailer_id')
									->join('person','person.id','=',$table_name.'.user_id')
									->select('retailer.id','retailer.name as retailer_name','retailer.address',$table_name.'.date','retailer.class',DB::raw("MAX(".$table_name.".date) as lastVisit"),DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"))
                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
									->where('retailer_status',1)
									->where($table_name.'.call_status','=','0')
									->whereIn('user_id',$junior_data_check)
									->where($table_name.'.company_id',$company_id)
									->where('dealer.company_id',$company_id)
									->where('retailer.company_id',$company_id)
									->groupBy('retailer_id')
									->orderBy($table_name.'.date','DESC')
									->get();
		}else{
				$retailer_sale_query = DB::table($table_name)
									// ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
									->join('dealer','dealer.id','=',$table_name.'.dealer_id')
									->join('retailer','retailer.id','=',$table_name.'.retailer_id')
									->join('person','person.id','=',$table_name.'.user_id')
									->select('retailer.id','retailer.name as retailer_name','retailer.address',$table_name.'.date','retailer.class',DB::raw("MAX(".$table_name.".date) as lastVisit"),DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"))
                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
									->where('retailer_status',1)
									->where($table_name.'.call_status','=','0')
									->whereIn('user_id',$junior_data_check)
									->where($table_name.'.company_id',$company_id)
									->where('dealer.company_id',$company_id)
									->where('retailer.company_id',$company_id)
									->groupBy('retailer_id')
									->orderBy($table_name.'.date','DESC')
									->get();
		}

		$retCat = DB::table('_retailer_outlet_category')
				->where('company_id',$company_id)
				->pluck('outlet_category','id');



        $final_retailer = array();
        foreach($retailer_sale_query as $key => $value)
        {
              $retailer_id = $value->id;

            $days = '';
        	$date1 = date('Y-m-d');
			$date2 = $value->lastVisit;

			$diff = abs(strtotime($date2) - strtotime($date1));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

			if($days == '0'){
    		$retailer_data['last_visit'] = 'Visited Today';
			}elseif($days > '0'){
    		$retailer_data['last_visit'] = $days.' Days Ago';
			}else{
    		$retailer_data['last_visit'] = 'Not Visited Yet';
			}

            $retailer_data['retailer_id'] = "$value->id";
            $retailer_data['retailer_name'] = $value->retailer_name;
            $retailer_data['address'] = !empty($value->address)?$value->address:'';
            $retailer_data['retailer_category'] = !empty($retCat[$value->class])?$retCat[$value->class]:'';;
            $retailer_data['last_sale_value'] = '0.00';
            $retailer_data['last_visited_by'] = $value->user_name;
        
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



    public function overAllTotalNotVisitedDetails(Request $request)
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


		$retailer_sale_query = DB::table($table_name)
                            		->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
									->whereIn('user_id',$junior_data_check)
									->where($table_name.'.company_id',$company_id)
									->groupBy('retailer_id')
									->orderBy($table_name.'.date','DESC')
									->pluck($table_name.'.retailer_id')->toArray();

		// dd($retailer_sale_query);



		$total_retailer = DB::table('dealer_location_rate_list')
						->select('retailer.id','retailer.name as retailer_name','retailer.address','retailer.class')
						->join('retailer','retailer.location_id','=','dealer_location_rate_list.location_id')
						->whereIn('dealer_location_rate_list.user_id',$junior_data_check)
						->where('dealer_location_rate_list.company_id',$company_id)
						->where('retailer.company_id',$company_id)
						->groupBy('retailer.id')
						->get();

		$retCat = DB::table('_retailer_outlet_category')
				->where('company_id',$company_id)
				->pluck('outlet_category','id');

		$lastSaleData = DB::table('user_sales_order')	
    				->whereIn('user_sales_order.user_id',$junior_data_check)
    				->where('user_sales_order.company_id',$company_id)
    				->groupBy('retailer_id')
    				->pluck(DB::raw("MAX(date) as lastDate"),'user_sales_order.retailer_id');

        $final_retailer = array();
        foreach($total_retailer as $key => $value)
        {

            $retailer_id = $value->id;

            if(in_array($retailer_id, $retailer_sale_query)){

            }else{
	            $days = '';
	        	$date1 = date('Y-m-d');
				$date2 = !empty($lastSaleData[$retailer_id])?$lastSaleData[$retailer_id]:'';

				$diff = abs(strtotime($date2) - strtotime($date1));
				$years = floor($diff / (365*60*60*24));
				$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
				$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

				if($days == '0'){
	    		$retailer_data['last_visit'] = 'Visited Today';
				}elseif($days > '0'){
	    		$retailer_data['last_visit'] = $days.' Days Ago';
				}else{
	    		$retailer_data['last_visit'] = 'Not Visited Yet';
				}


	            $retailer_data['retailer_id'] = "$value->id";
	            $retailer_data['retailer_name'] = $value->retailer_name;
	            $retailer_data['address'] = !empty($value->address)?$value->address:'';
	            $retailer_data['retailer_category'] = !empty($retCat[$value->class])?$retCat[$value->class]:'';
	            $retailer_data['last_sale_value'] = "";
	            $retailer_data['last_visited_by'] = "";
	        
	            $final_retailer[] = $retailer_data;
        	}
        }
	
		if($final_retailer){
			return response()->json([ 'response' =>TRUE,'result'=>$final_retailer]);
		}
		else
		{
			return response()->json([ 'response' =>FALSE,'result'=>array()]);
		}

    }


    public function retailerFilter(Request $request)
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
        $filter_id = $request->filter_id;
        $main_filter_id = $request->main_filterId;
         $current_date = date('Y-m-d');
        $table_name = TableReturn::table_return($current_date,$current_date);


        if($main_filter_id == '13'){ // for outlet type
        	$explodeType = explode(',',$filter_id);
        }elseif($main_filter_id == '20'){ // for outlet category
        	$explodeCategory = explode(',',$filter_id);
        }elseif ($main_filter_id == '21') {
        	$explodeCreatedBy = explode(',',$filter_id);
        }

        // dd($explodeType);

        $user_dealer_retailer_query = DB::table('dealer')
                                               ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                                               // ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
                                               ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id') // added
                                               ->join('location_6','location_6.id','=','location_7.location_6_id') //added
                                               ->select('dealer.name as name','dealer.id as dealer_id','location_6.name as lname','location_6.id as lid','csa_id')
                                               ->where('dealer_location_rate_list.user_id',$user_id)
                                               ->where('dealer.dealer_status',1)
                                               ->where('location_7.status',1) // added
                                               ->where('dealer.company_id',$company_id)
                                               ->where('dealer_location_rate_list.company_id',$company_id)
                                               ->groupBy('dealer.id')
                                               ->get();
        $dealer_id = array();
        foreach ($user_dealer_retailer_query as $key => $value)
        {
            $dealer_id[]=$value->dealer_id;
          
        }


        $beat_data = DB::table('dealer_location_rate_list')
                                ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
                                ->select('dealer_location_rate_list.location_id as beat_id','location_7.name as name','dealer_location_rate_list.dealer_id as dealer_id')
                                ->whereIn('dealer_location_rate_list.dealer_id',$dealer_id)
                                ->where('dealer_location_rate_list.user_id',$user_id)
                                   ->where('dealer_location_rate_list.company_id',$company_id)
                                   ->where('location_7.company_id',$company_id)
                                ->groupBy('dealer_location_rate_list.location_id','dealer_id')
                                ->get();
        $beat_id = array();
        foreach($beat_data as $key => $value)
        {
            $beat_id[] = $value->beat_id;
        }


        $retailer_id = DB::table('retailer')->select('retailer.other_numbers','verfiy_retailer_status','sequence_id as seq_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'_role.rolename as designation','retailer.created_on as created_on','created_by_person_id','retailer.tin_no as tin_no','location_7.name as beat_name','retailer.id as id','lat_long','retailer.name as retailer_name','location_id','address','retailer.email as email','contact_per_name','landline','retailer.image_name')
                        ->join('location_7','location_7.id','=','retailer.location_id')
                        ->join('person','person.id','=','retailer.created_by_person_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->whereIn('retailer.location_id',$beat_id)
                        ->where('retailer.company_id',$company_id)
                        ->where('location_7.company_id',$company_id)
                        ->where('_role.company_id',$company_id)
                        ->where('retailer_status',1)
                        ->groupBy('retailer.id');
        if(!empty($explodeType))
        {
            $retailer_id->whereIn('retailer.outlet_type_id',$explodeType);
        }

          if(!empty($explodeCategory))
        {
            $retailer_id->whereIn('retailer.class',$explodeCategory);
        }

           if(!empty($explodeCreatedBy))
        {
            $retailer_id->whereIn('retailer.created_by_person_id',$explodeCreatedBy);
        }

        $retailer_id_data =  $retailer_id->get();

        $last_order_book = DB::table($table_name)
                        // ->select(DB::raw("CONCAT_WS(' ',date,time) as date_time"),'retailer_id')
                        ->where('company_id',$company_id)
                        ->whereIn('location_id',$beat_id)
                        ->groupBy('retailer_id')
                        // ->orderBy('date_time','DESC')
                        // ->pluck('date_time','retailer_id');
                        ->pluck(DB::raw("MAX(date) as date"),'retailer_id');

        // $last_order_book = arary();
        // dd($last_order_book);

        $payment_collection_data = DB::table('payment_collection')->where('company_id',$company_id)->pluck(DB::raw('sum(total_amount) as paid'),'retailer_id');
        $challan_order_data = DB::table('challan_order')->where('company_id',$company_id)->pluck(DB::raw('sum(amount) as ch_amt'),'ch_retailer_id');
        $last_payment_collection_data = DB::table('payment_collection')->where('company_id',$company_id)->orderBy('pay_date_time','DESC')->pluck('total_amount','retailer_id');

        $retailer_scheme = DB::table('scheme_assign_retailer')
        				->join('scheme_plan','scheme_plan.id','=','scheme_assign_retailer.plan_id')
        				->where('scheme_assign_retailer.company_id',$company_id)
        				->groupBy('retailer_id','plan_id')
        				->pluck(DB::raw("CONCAT(plan_assigned_from_date,'|',plan_assigned_to_date,'|','scheme_name') as id"),'retailer_id');
        foreach($retailer_id_data as $key => $value)
        {
            $retailer_id = $value->id;
            $payment_collection_query = !empty($payment_collection_data[$retailer_id])?$payment_collection_data[$retailer_id]:0;
            $challan_data_query = !empty($challan_order_data[$retailer_id])?$challan_order_data[$retailer_id]:0;
            $retailer_amt  = !empty($last_payment_collection_data[$retailer_id])?$last_payment_collection_data[$retailer_id]:0;
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
            if(!empty($retailer_user_sequence[$value->created_by_person_id.$retailer_id]))
            {
                $sequence_id = $retailer_user_sequence[$value->created_by_person_id.$retailer_id];
            }
            elseif(!empty($retailer_sequence[$retailer_id]))
            {
                $sequence_id = $retailer_sequence[$retailer_id];
            }
            else
            {
                $sequence_id = 0;
            }
            if(!empty($retailer_scheme[$retailer_id]))
            {
            	$implode_array = explode('|', $retailer_scheme[$retailer_id]); 
            	$implode_array_1 = $implode_array[0];
            	$implode_array_2 = $implode_array[1];
            	$implode_array_3 = $implode_array[2];
            }
            else
            {
				$implode_array_1 = 'NA';
				$implode_array_2 = 'NA';
				$implode_array_3 = 'NA';
            }
            $retailer_data['lat'] = $lat;
            $retailer_data['lng'] = $lng;
            $retailer_data['location_id'] = "$value->location_id";
            $retailer_data['address'] = !empty($value->address)?$value->address:'';
            $retailer_data['email'] = !empty($value->email)?$value->email:'';
            $retailer_data['tin_no'] = $value->tin_no;
            $retailer_data['contact_per_name'] = !empty($value->contact_per_name)?$value->contact_per_name:'';
            $retailer_data['landline'] = !empty($value->other_numbers)?$value->other_numbers:$value->landline;
            $retailer_data['seq_id'] = "$sequence_id";
            $retailer_data['created_by'] = $value->user_name;
            $retailer_data['created_by_designation'] = $value->designation;
            $retailer_data['created_at'] = $value->created_on;
            $retailer_data['last_visit_date'] = !empty($last_order_book[$retailer_id])?$last_order_book[$retailer_id]:"No Order book Yet";
            $retailer_data['beat_name'] = $value->beat_name;
            $outstanding = !empty($payment_collection_query)?($payment_collection_query)-($challan_data_query):0;
            $retailer_data['outstanding'] = "$outstanding";
            $last_amt = !empty($retailer_amt)?$retailer_amt:0;
            $retailer_data['last_amt'] = "$last_amt";
            $retailer_data['achieved'] = !empty($challan_data_query)?$challan_data_query:'';
            $retailer_data['last_date'] = "no date";
            $retailer_data['verify_status'] = ($value->verfiy_retailer_status);
            $retailer_data['scheme_from_date'] = $implode_array_1;
            $retailer_data['scheme_to_date'] = $implode_array_2;
            $retailer_data['scheme_plan_name'] = $implode_array_3;
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


}
