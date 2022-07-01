<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Person;
use App\PersonLogin;
use App\PersonDetail;
// use App\User;
use App\Company;
use App\Location7;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Validator;
use DB;
use Image;

class SubmissionController extends Controller
{
	public function fullfilment_data(Request $request)
	{
		$validator=Validator::make($request->all(),[
          'user_id' => 'required',
          'retailer_id' => 'required',
          'retailer_name' => 'required',
          'order_id' => 'required',
          'current_date' => 'required',
          'current_time' => 'required',
          'invoice_no' => 'required',
		  'date' => 'required',
		  'company_id'=>'required',
       ]);
       // return response()->json(['response'=>True,'Post'=>$_POST,'message'=>'POST DATA'],200);
       if($validator->fails()){
        return response()->json(['response'=>FALSE,'Error'=>$validator->errors(),'message'=>'Validation Error! Please try again..'],200);
       }

		$user_id = $request->user_id;
		$retailer_id = $request->retailer_id;
		$retailer_name = $request->retailer_name;
		$order_id = $request->order_id;
		$date = $request->current_date;
		$time = $request->current_time;
		$order_date = $request->date;
		$invoice_no = $request->invoice_no;
		$company_id = $request->company_id;
		$details = json_decode($request->fullfillment_details);
		$myArr= 
		[
			'order_id' => $order_id,
			'retailer_id' => $retailer_id,
			'retailer_name' => $retailer_name,
			'date' => $date,
			'time' => $time,
			'order_date' => $order_date,
			'invoice_number' => $invoice_no,
			'server_date' => date('Y-m-d H:i:s'),
			'company_id'=> $company_id,
			'created_by' => $user_id,

		];
		
		foreach ($details->fullfillment_details as $key => $value) 
		{
			$arr['product_id'] = $value->product_id;
			$arr['product_name'] = $value->product_name;
			$arr['order_id'] = $value->order_id;
			$arr['product_fullfiment_qty'] = $value->product_fullfillment_qty;
			$arr['product_value'] = $value->product_value;
			$arr['product_rate'] = $value->product_rate;
			$arr['product_qty'] = $value->product_qty;
			$arr['company_id']  = $company_id;
			$arr['created_at'] = date('Y-m-d');
			$detailArr[] = $arr;
		}	
        DB::beginTransaction();

		$update_flag_fullfillment = DB::table('user_sales_order')->where('order_id',$order_id)->update(['flag_fullfillment'=>2,'updated_at'=>date('Y-m-d H:i:s')]);

		if($update_flag_fullfillment)
		{
			if(!empty($myArr))
	        {
	        	 $fullfillment_data_query = DB::table('fullfillment_order')->insert($myArr);
	        	 if(!$fullfillment_data_query)
	        	 {
	        	 	DB::rollback();
	                return response()->json([
	                'response' =>false,'order_id'=>$order_id,'message'=>"Try again: !!Something w12ent wrog!!"
	                ]);
	        	 }
	        }
	        if(!empty($detailArr))
	        {
	        	$fullfillmentr_details_query = DB::table('fullfillment_order_details')->insert($detailArr);
	        	if(!$fullfillmentr_details_query)
	        	{
	        		DB::rollback();
	                return response()->json([
	                'response' =>false,'order_id'=>$order_id,'message'=>"Try again: !!Something went wrog!!"
	                ]);
	        	}
	        }
		}
		else
		{
			DB::rollback();
            return response()->json([
            'response' =>false,'order_id'=>$order_id,'message'=>"Try again: !!Something went wrog!!"
            ]);
		}
        

        DB::commit();
		return response()->json([ 'response' =>True,'message'=>'Success Fully Inserted']);		

		
	}

	public function create_beat(Request $request)
	{
		$validator=Validator::make($request->all(),[
          'user_id' => 'required',
          'company_id' => 'required',
          'location_6_id'=> 'required',
          'beat_name'=> 'required',
       ]);
       // return response()->json(['response'=>True,'Post'=>$_POST,'message'=>'POST DATA'],200);
       if($validator->fails()){
        return response()->json(['response'=>FALSE,'Error'=>$validator->errors(),'message'=>'Validation Error! Please try again..'],200);
       }

       $check = Location7::where('location_6_id',$request->location_6_id)
						->where('name',$request->beat_name)
						->where('company_id',$request->company_id)
						->where('status',1)
						->get();
		// dd($check);
		if(COUNT($check)<=0)
		{
			$myArr = [
       		'name'=> $request->beat_name,
       		'company_id'=> $request->company_id,
       		'location_6_id'=> $request->location_6_id,
       		'status'=>1,
       		'created_at'=>date('Y-m-d H:i:s'),
       		'created_by'=> $request->user_id,
	       ];
	       $inser_beat = Location7::create($myArr);
	       if($inser_beat)
	       {
				return response()->json([ 'response' =>True,'message'=>'SuccessFully Inserted']);		

	       }
	       else
	       {
				return response()->json([ 'response' =>False,'message'=>'Not Inserted']);		

	       }
		}
		else
		{
			return response()->json([ 'response' =>False,'message'=>'Already Exist!!']);		

		}
       
	}


	public function dailyreportingSubmit(Request $request)
	{
		$validator=Validator::make($request->all(),[
          'user_id' => 'required',
          'working_date' => 'required',
          'dialy_schdule_id'=> 'required',
          'mnc_mcc_lat_cellid'=> 'required',
          'lat'=> 'required',
          'lng'=> 'required',
          'track_address'=> 'required',
          'remarks'=> 'required',
          'company_id'=> 'required',
          'order_id'=> 'required',
          'working_with'=> 'required',
       ]);
       // return response()->json(['response'=>True,'Post'=>$_POST,'message'=>'POST DATA'],200);
       if($validator->fails()){
        return response()->json(['response'=>FALSE,'Error'=>$validator->errors(),'message'=>'Validation Error! Please try again..'],200);
       }

       $user_id = $request->user_id;
       $working_date = $request->working_date;
       $dialy_schdule_id = $request->dialy_schdule_id;
       $mnc_mcc_lat_cellid = $request->mnc_mcc_lat_cellid;
       $lat = $request->lat;
       $lng = $request->lng;
       $track_address = $request->track_address;
       $remarks = $request->remarks;
       $company_id = $request->company_id;
       $order_id = $request->order_id;
       $working_with = $request->working_with;

       $lat_lng = $lat.','.$lng;


       $check = DB::table('daily_reporting')->where('order_id',$order_id)
						->get();
		// dd($check);
		if(COUNT($check)<=0)
		{
			$myArr = [
       		'company_id'=> $company_id,
       		'user_id'=> $user_id,
       		'work_date'=> $working_date,
       		'server_date_time'=> date('Y-m-d H:i:s'),
       		'daily_schedule_id'=> $dialy_schdule_id,
       		'working_with'=> $working_with,
       		'mnc_mcc_lat_cellid'=> $mnc_mcc_lat_cellid,
       		'lat_lng'=> $lat_lng,
       		'remarks'=> $remarks,
       		'attn_address'=> $track_address,
       		'order_id'=> $order_id,
	       ];
	       $inser_daily_reporting = DB::table('daily_reporting')->insert($myArr);
	       if($inser_daily_reporting)
	       {
				return response()->json([ 'response' =>True,'message'=>'SuccessFully Inserted']);		

	       }
	       else
	       {
				return response()->json([ 'response' =>False,'message'=>'Not Inserted']);		

	       }
		}
		else
		{
			return response()->json([ 'response' =>False,'message'=>'Already Exist!!']);		

		}
       
	}



	public function createRetailer(Request $request)
	{
		$validator=Validator::make($request->all(),[
          'user_id' => 'required',
          'dealer_id' => 'required',
          'beat_id'=> 'required',
          'created_date'=> 'required',
          'mnc_mcc_lat_cellid'=> 'required',
          'lat'=> 'required',
          'lng'=> 'required',
          'geo_address'=> 'required',
          'id'=> 'required',
          'outlet_name'=> 'required',
          'owner_name'=> 'required',
          'contact_name'=> 'required',
          'contact_no'=> 'required',
          'address'=> 'required',
          'pincode'=> 'required',
          'gstn'=> 'required',
          'email'=> 'required',
          'battery_status'=> 'required',
          'gps_status'=> 'required',
          'company_id'=> 'required',
          'outlet_type_id'=> 'required',
          'outlet_category_id'=> 'required',
       ]);
       // return response()->json(['response'=>True,'Post'=>$_POST,'message'=>'POST DATA'],200);
       if($validator->fails()){
        return response()->json(['response'=>FALSE,'Error'=>$validator->errors(),'message'=>'Validation Error! Please try again..'],200);
       }

       $id = $request->id;
       $name = $request->outlet_name;
       $dealer_id = $request->dealer_id;
       $location_id = $request->beat_id;
       $company_id = $request->company_id;
       $address = $request->address;
       $email = $request->email;
       $contact_per_name = $request->contact_name;
       // $landline = $request->contact_no;
       $other_numbers = $request->contact_no;
       $tin_no = $request->gstn;
       $pin_no = $request->pincode;
       $outlet_type_id = $request->outlet_type_id;
       $lat = $request->lat;
       $lng = $request->lng;
       $mncmcclatcellid = $request->mnc_mcc_lat_cellid;
       $track_address = $request->geo_address;
       $created_on = $request->created_date;
       $created_by_person_id = $request->user_id;
       $battery_status = $request->battery_status;
       $gps_status = $request->gps_status;
       $outlet_category_id = $request->outlet_category_id;

       $is_golden = !empty($request->is_golden)?$request->is_golden:'0';

       $whatsapp_no = !empty($request->whatsapp_no)?$request->whatsapp_no:'';
       $date_of_anniversary = !empty($request->date_of_anniversary)?$request->date_of_anniversary:'';
       $drug_licence = !empty($request->drug_licence)?$request->drug_licence:'';
       $date_of_birth = !empty($request->date_of_birth)?$request->date_of_birth:'';

       $owner_name = !empty($request->owner_name)?$request->owner_name:'';
       



       $lat_lng = $lat.','.$lng;


       $check = DB::table('retailer')->where('id',$id)
						->get();
		// dd($check);
		if(COUNT($check)<=0)
		{
			$myArr = [
				'id'=> $id,
				'name'=> $name,
				'class'=> $outlet_category_id,
				'dealer_id'=> $dealer_id,
				'location_id'=> $location_id,
				'company_id'=> $company_id,
				'address'=> $address,
				'email'=> $email,
				'contact_per_name'=> $contact_per_name,
				'other_numbers'=> $other_numbers,
				'tin_no'=> $tin_no,
				'pin_no'=> $pin_no,
				'lat_long'=> $lat_lng,
				'mncmcclatcellid'=> $mncmcclatcellid,
				'track_address'=> $track_address,
				'created_on'=> $created_on,
				'created_by_person_id'=> $created_by_person_id,
				'battery_status'=> $battery_status,
				'gps_status'=> $gps_status,
				'is_golden'=> $is_golden,
				'whatsapp_no'=> $whatsapp_no,
				'date_of_anniversary'=> $date_of_anniversary,
				'drug_licence'=> $drug_licence,
				'date_of_birth'=> $date_of_birth,
				'owner_name'=> $owner_name,


	       ];
	       $inser_daily_reporting = DB::table('retailer')->insert($myArr);

	       // insert 9+1 by default
	       $insertNine = [
	       		'plan_id' => '1',
	       		'retailer_id' => $id,
	       		'plan_assigned_from_date' => date('Y-m-d'),
	       		'plan_assigned_to_date' => date('Y-m-d', strtotime('+1 year')),
	       		'company_id' => $company_id,
	       		'status' => '1',
	       		'created_by' => '2196',
	       		'created_at' => date('Y-m-d H:i:s'),
	       		'updated_at' => date('Y-m-d H:i:s'),
	       ];

	       $insernine = DB::table('scheme_assign_retailer')->insert($insertNine);


	       // insert uphaar by default
	       $insertUphaar = [
	       		'plan_id' => '28',
	       		'retailer_id' => $id,
	       		'plan_assigned_from_date' => date('Y-m-d'),
	       		'plan_assigned_to_date' => date('Y-m-d', strtotime('+1 year')),
	       		'company_id' => $company_id,
	       		'status' => '1',
	       		'created_by' => '2196',
	       		'created_at' => date('Y-m-d H:i:s'),
	       		'updated_at' => date('Y-m-d H:i:s'),
	       ];

	       $inseruphaar = DB::table('scheme_assign_retailer')->insert($insertUphaar);






	       if($inser_daily_reporting)
	       {
				return response()->json([ 'response' =>True,'message'=>'SuccessFully Inserted']);		

	       }
	       else
	       {
				return response()->json([ 'response' =>False,'message'=>'Not Inserted']);		

	       }
		}
		else
		{
			return response()->json([ 'response' =>False,'message'=>'Already Exist!!']);		

		}
       
	}
	public function customer_order_form_aeris(Request $request)
	{
		$validator=Validator::make($request->all(),[
          'customer_name' => 'required',
          'customer_contact_no' => 'required',
          'customer_email_id'=> 'required',
          'customer_add'=> 'required',
          'product_id'=> 'required',
          'lat'=> 'required',
          'lng'=> 'required',
          'mcc_mnc_lat_cellid'=> 'required',
          'geo_address'=> 'required',
          'number_of_unit'=> 'required',
          'batter_status'=> 'required',
          'gps_status'=> 'required',
       
          'created_by'=> 'required',
                 
       ]);
       // return response()->json(['response'=>True,'Post'=>$_POST,'message'=>'POST DATA'],200);
       if($validator->fails()){
        return response()->json(['response'=>FALSE,'Error'=>$validator->errors(),'message'=>'Validation Error! Please try again..'],200);
       }

		$customer_name = $request->customer_name;
		$customer_contact_no = $request->customer_contact_no;
		$customer_email_id = $request->customer_email_id;
		$customer_add = $request->customer_add;
		$product_id = $request->product_id;
		$lat = $request->lat;
		$lng = $request->lng;
		$mcc_mnc_lat_cellid = $request->mcc_mnc_lat_cellid;
		$geo_address = $request->geo_address;
		$number_of_unit = $request->number_of_unit;
		$batter_status = $request->batter_status;
		$gps_status = $request->gps_status;
		// $track_addr = $request->track_addr;
		$created_by = $request->created_by;
		$created_at = $request->created_at;


			$myArr = [
				'customer_name' => $customer_name,
				'customer_contact_no' => $customer_contact_no,
				'customer_email_id' => $customer_email_id,
				'customer_add' => $customer_add,
				'product_id' => $product_id,
				'lat' => $lat,
				'lng' => $lng,
				'mcc_mnc_lat_cellid' => $mcc_mnc_lat_cellid,
				'track_addr' => $geo_address,
				'number_of_unit' => $number_of_unit,
				'batter_status' => $batter_status,
				'gps_status' => $gps_status,
				// 'track_addr' => $track_addr,
				'created_by' => $created_by,
				'created_at' => date('Y-m-d H:i:s'),

	       ];
	       $inser_daily_reporting = DB::table('customer_order_form_aeris')->insert($myArr);
	       if($inser_daily_reporting)
	       {
				return response()->json([ 'response' =>True,'message'=>'SuccessFully Submitted']);		

	       }
	       else
	       {
				return response()->json([ 'response' =>False,'message'=>'Not Submitted']);		

	       }
		
       
	}
	public function registerUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'fname' => 'required',
            // 'lname' => 'required',
            // 'mobile' => 'required',
            // 'email' => 'required|email',
            // 'empcode' => 'required',
            // 'address' => 'required',
            // 'imei' => 'required',
            // 'date_time' => 'required',
            // 'device_type' => 'required',
            // 'state_id' => 'required',
            // 'state_name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }


       	$fname = $request->fname;
        $lname = $request->lname;
        $mobile = $request->mobile;
        $email = $request->email;
        $empcode = $request->empcode;
        $state = '33';
        $address = $request->address;
        $imei = $request->imei;
        $date_time = $request->date_time;
        $device_type = 'Android';
        $state_id = '33';
        $state_name = 'Delhi';
        $company_id = 37;

        $mobile_uname = $mobile.'@demo';
        $check_user = DB::table('person_login')->where('person_username',$mobile_uname)->first();

        if($check_user){
            return response()->json([ 'response' =>FALSE,'message'=>'User Already Exist']);
        }



     


        $myArr = [
            'first_name' => trim($fname),
            'middle_name' => trim($request->middle_name),
            'last_name' => trim($lname),
            'role_id' => '97',
            'person_id_senior' => '1384',
            'version_code_name' => '',
            'resigning_date' => date('Y-m-d'),
            'head_quar' =>trim($state_name),
            'mobile' => trim($mobile),
            'email' => trim($email),
            'state_id' => trim($state_id),
            'emp_code' => trim($mobile),
            'company_id' => $company_id,
            'joining_date' => date('Y-m-d'),
//            'created_by' => Auth::user()->id,
            'status' => '0',
            'env_flag' => '2',
        ];


        $person=Person::create($myArr);



         $myArr2=[
            'person_id'=>$person->id,
            'address'=>trim(!empty($request->address)?$request->address:''),
            'company_id' => $company_id,
            'gender'=>'M',
            'created_on'=>date('Y-m-d H:i:s')
        ];
        $person2=PersonDetail::create($myArr2);


        $myArr3=[
            'person_id'=>$person->id,
            'emp_id'=>trim($mobile),
            'company_id' => $company_id,
            'person_username'=>trim($mobile_uname),
            'person_password'=>DB::raw("AES_ENCRYPT('".trim($mobile)."', '".Lang::get('common.db_salt')."')"),
            'person_status'=> '0'
        ];
        $person3=PersonLogin::create($myArr3);



        $myArr4=[
            'id'=>$person->id,
            'role_id'=>'97',
            'email'=>trim($mobile_uname),
            'password'=>bcrypt(trim($mobile)),
            'original_pass'=>$mobile,
            'company_id' => $company_id,
            'status'=>0,
            'created_at'=>date('Y-m-d H:i:s'),

        ];
        $person4=User::create($myArr4);


            return response()->json([ 'response' =>TRUE,'message'=>'User Created']);
        
    }
    public function primary_sale_submission(Request $request){

    	$validator=Validator::make($request->all(),[
          'user_id' => 'required',
          'dealer_id' => 'required',
          'order_id' => 'required',
          'current_date' => 'required',
          'current_time' => 'required',
		  'company_id'=>'required',
		  'discount_type'=>'required',
		  'discount_value'=>'required',
       ]);
       // return response()->json(['response'=>True,'Post'=>$_POST,'message'=>'POST DATA'],200);
       if($validator->fails()){
        return response()->json(['response'=>FALSE,'Error'=>$validator->errors(),'message'=>'Validation Error! Please try again..'],200);
       }

		$user_id = $request->user_id;
		$dealer_id = $request->dealer_id;
		$order_id = $request->order_id;
		$date = $request->current_date;
		$discount_type = $request->discount_type;
		$discount_value = $request->discount_value;
		$time = $request->current_time;
		$order_date = $request->date;
		$remarks = !empty($request->remarks)?$request->remarks:'';
		$invoice_no = $request->invoice_no;
		$company_id = $request->company_id;
		$details = json_decode($request->primary_details);

		foreach ($details as $key => $value) 
        {

            $myArrDetails = [
                'product_id'=> $value->product_id,
                'primary_unit'=> 0,
                'rate'=> $value->rate,
                'quantity'=> $value->qty,
                'scheme_qty'=> $value->scheme_qty,
                'order_id'=> $value->order_id,
            	'id' => $value->order_id,
                'secondary_rate'=> 0,
                'secondary_qty'=> 0,
                'final_secondary_rate'=> "0",
                'final_secondary_qty'=> 0,
                'company_id'=> $company_id,
                'server_date_time'=>date('Y-m-d H:i:s'),

            ];

            $finalArr[] = $myArrDetails;


        	$detailsArr = [
					'order_id' => $value->order_id,
					'id' => $value->order_id,
					'product_id' => $value->product_id,
					'rate' => $value->rate,
					'quantity' => $value->qty,
					'barcode' => '',
					'scheme_qty' => $value->scheme_qty,
					'cases' => '0',
					'pcs' => $value->qty,
					'total_value' => $value->rate*$value->qty,
					'pr_rate' => '0',
					'company_id'=>$company_id,
					'app_flag' => '2',
				];
			$insertDetailsArr[] = $detailsArr;


        }
	 	$myArr = [
            'order_id' => $order_id,
            'id' => $order_id,
            'created_date' => date('Y-m-d'),
            'created_person_id' => $user_id,
            'sale_date' => date('Y-m-d'),
            'receive_date' => date('Y-m-d'),
            'dispatch_date' => date('Y-m-d'),
            'date_time' => date('Y-m-d H:i:s'),
            'company_id'=> $company_id,
            'dealer_id'=> $dealer_id,
            'discount_type'=>$discount_type,
            'discount_value'=>$discount_value,
            'remarks'=> $remarks,
            'comment'=> $remarks,
            'dispatch_through'=> '',
            'destination'=> '',
            'order_from'=> "1",
            'pdf_name'=>'',
            'janak_order_sequence'=> 0,
            'amount_before_discount'=> 0,
            'amount_after_discount'=> 0,
            

        ];


        $myArrPur = [
				'order_id'=>$order_id,
				'id' => $order_id,
				'dealer_id'=>$dealer_id,
				'created_person_id'=>0,
				'retailer_id'=>0,
				'sale_date'=>date('Y-m-d'),
				'created_date'=>date('Y-m-d'),
				'receive_date'=>date('Y-m-d'),
				'dispatch_date'=>date('Y-m-d'),
				'date_time'=>date('Y-m-d H:i:s'),
				'battery_status'=>'0',
				'gps_status'=>'0',
				'lat'=>'0',
				'lng'=>'0',
                'address'=>'',
                'total_weight'=>'0',
				'vehicle_id'=>'4',
				'mcc_mnc_lac_cellid'=>'',
				'company_id'=>$company_id,
			];
		$insert_order = DB::table('purchase_order')->insert($myArrPur);
		$insert_details = DB::table('purchase_order_details')->insert($insertDetailsArr);



     	$primary_order_insert = DB::table('user_primary_sales_order')->insert($myArr);
        $primary_order_details_insert = DB::table('user_primary_sales_order_details')->insert($finalArr);
		
		
        DB::beginTransaction();

		if(!$primary_order_insert && !$primary_order_details_insert){

			DB::rollback();
            return response()->json([
            'response' =>false,'order_id'=>$order_id,'message'=>"Try again: !!Something went wrog!!"
            ]);
		}
        

        DB::commit();
		return response()->json([ 'response' =>True,'message'=>'Success Fully Inserted']);		

		
    }

    // public function signup_registration(Request $request)
    // {

    // }


    public function retailerInfoEdit(Request $request){

    	$validator=Validator::make($request->all(),[
          'retailer_id' => 'required',
          'retailer_name' => 'required',
          'contact_person' => 'required',
          'contact_no' => 'required',
          'date_of_birth' => 'required',
		  'company_id'=>'required',
		  'email_id'=>'required',
		  'gst'=>'required',
		  'address'=>'required',
		  'user_id'=>'required',
       ]);
       if($validator->fails()){
        return response()->json(['response'=>FALSE,'Error'=>$validator->errors(),'message'=>'Validation Error! Please try again..'],200);
       }

		$retailer_id = $request->retailer_id;

		$retailer_name = $request->retailer_name;
		$contact_person = $request->contact_person;
		$contact_no = $request->contact_no;
		$date_of_birth = $request->date_of_birth;
		$company_id = $request->company_id;
		$email_id = $request->email_id;
		$gst = $request->gst;
		$address = $request->address;
		$user_id = $request->user_id;

		$owner_name = !empty($request->owner_name)?$request->owner_name:'';
		$whatsapp_no = !empty($request->whatsapp_no)?$request->whatsapp_no:'';
		$date_of_anniversary = !empty($request->date_of_anniversary)?$request->date_of_anniversary:'';
		$drug_licence = !empty($request->drug_licence)?$request->drug_licence:'';
		$pin_code = !empty($request->pin_code)?$request->pin_code:'';

		

            $myArrDetails = [
                'name'=> $retailer_name,
                'contact_per_name'=> $contact_person,
                'other_numbers'=> $contact_no,
                'date_of_birth'=> $date_of_birth,
                'email'=> $email_id,
                'tin_no'=> $gst,
            	'address' => $address,
            	'updated_at' => date('Y-m-d H:i:s'),
            	'updated_by' => $user_id,
            	'owner_name' => $owner_name,
            	'whatsapp_no' => $whatsapp_no,
            	'date_of_anniversary' => $date_of_anniversary,
            	'drug_licence' => $drug_licence,
            	'pin_no' => $pin_code,


            ];

     	$primary_order_update = DB::table('retailer')->where('company_id',$company_id)->where('id',$retailer_id)->update($myArrDetails);

		if($primary_order_update){
			return response()->json([ 'response' =>True,'message'=>'Success Fully Data Updated']);		
		}
        else{
			return response()->json([ 'response' =>False,'message'=>'Not Updated']);		
        }


		
    }



    public function isGoldenData(Request $request){

    	$validator=Validator::make($request->all(),[
          'retailer_id' => 'required',
		  'company_id'=>'required',
		  'is_golden'=>'required',
       ]);
       if($validator->fails()){
        return response()->json(['response'=>FALSE,'Error'=>$validator->errors(),'message'=>'Validation Error! Please try again..'],200);
       }

		$retailer_id = $request->retailer_id;
		$is_golden = $request->is_golden;
		$duration = !empty($request->duration)?$request->duration:'';
		$company_id = $request->company_id;

		 if ($is_golden) 
		{
      //       $image = $request->file('image_source');
      //    	$str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
    		// $random_no = substr($str, 0,2);  // return always a new string 
    		// // $custom_image_name = date('YmdHis').$retailer_id;
    		// $custom_image_name = $retailer_id;
      //       $imageName = $custom_image_name . '.' . $image->getClientOriginalExtension();
      //       $destinationPath = public_path('/is_golden_images/' . $imageName);
      //       Image::make($image)->save($destinationPath);
      //       $content = "/is_golden_images/".$imageName;

              $myArrDetails = [
                'is_golden'=> $is_golden,
                'duration'=> $duration,
            ];

     	$primary_order_update = DB::table('retailer')->where('company_id',$company_id)->where('id',$retailer_id)->update($myArrDetails);
			return response()->json([ 'response' =>True,'message'=>'Success Fully Data Updated']);		
     	

        }
        else{
			return response()->json([ 'response' =>False,'message'=>'Not Updated']);		
        }
		
    }




}