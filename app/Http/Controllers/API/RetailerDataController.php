<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Person;
use App\UserDetail;
use App\Company;
use App\JuniorData;
use App\Retailer;
use App\Location3;
use App\Location6;
use App\Location5;
use App\Location4;
use App\SendSms;
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

class RetailerDataController extends Controller
{
    public $successStatus = 401;
    public $response_true = True;
    public $response_false = False;

    public function verify_retailer(Request $request)
    {
    	$validator=Validator::make($request->all(),[
			'retailer_id'=>'required',
			'company_id'=>'required',
			'user_id'=>'required',
			'retailer_number'=>'required',
			'date_time'=>'required',
			'retailer_name'=> 'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		$otp_number = $request->otp_number; 
    	// return response()->json([ 'response' =>True,'message'=>'OTP Generated Successfully']);
    	$str = str_shuffle("123456789123456789123234567890456789123456789");
        $otp = substr($str, 0,6);  // return always a new string 
        $msg = '';
        if($request->company_id == 47)
        {
        	$msg = "Thanks for Registering with Guruji Product Pvt Ltd. Indore Your confirmation pin is $otp , Looking forward for long term association with you"; // custom message
        }
        elseif($request->company_id == 50)
        {

        	$msg = "Your Janak Perfumers outlet registration number is: $otp-manacle technologies";
        	// $msg = "Your Janak Perfumers outlet registration number is: $otp"; // custom message
        }
        else
        {
        	$msg = "$otp is the OTP for creating OUTLET $request->retailer_name -Manacle Technologies Pvt Ltd";
        	// $msg = "$otp is the OTP for creating OUTLET $request->retailer_name"; // custom message
        }
        // $msg = "$otp is the OTP for creating OUTLET $request->retailer_name";
        $mobile_no = $request->retailer_number;
        $company_id = $request->company_id;
        
        $retailer_id = $request->retailer_id;

        $check_retailer =  Retailer::where('other_numbers',$mobile_no)->where('retailer_status','!=',9)->where('company_id',$company_id)->where('verfiy_retailer_status',1)->count();
        $check_retailer_secod =  Retailer::where('id',$retailer_id)->where('retailer_status','!=',9)->where('company_id',$company_id)->where('verfiy_retailer_status',1)->count();
        // dd($check_retailer);
		if(($check_retailer_secod)>0)
		{
			return response()->json([ 'response' =>False,'message'=>'This Retailer Already Verified!!']);
		}
		if(($check_retailer)>0)
		{
			return response()->json([ 'response' =>False,'message'=>'No. Already Exist on any other Retailer!!']);
		}
		elseif(!empty($otp_number))
		{
			// dd('1');
			$check_otp = DB::table('retailer_check_sms')->where('otp_number',$otp_number)->where('mobile_no',$mobile_no)->where('retailer_id',$retailer_id)->orderBy("id",'DESC')->first();
			// dd($check_otp);
			if(!empty($check_otp))
			{
	        	return response()->json([ 'response' =>True,'message'=>'Verified Successfully']);

			}
			else
			{
	        	return response()->json([ 'response' =>False,'message'=>'Not Verified!!']);

			}


		}
		else
		{
			$send_sms = SendSms::send_sms($mobile_no,$msg); // send sms and get return message
	        if($send_sms->status=='success')
	        {
	        	$myArr = [
	        		'retailer_id' => $request->retailer_id,
	        		'company_id' => $request->company_id,
	        		'generated_by' => $request->user_id,
	        		'otp_number' => $otp,
	        		'mobile_no' => $mobile_no,
	        		'date_time' => $request->date_time,
	        		'server_date_time' => date('Y-m-d H:i:s'),
	        	];
	        	$insert_array = DB::table('retailer_check_sms')->insert($myArr);
	        	$retailer_status = DB::table('retailer')->where('id',$retailer_id)->update(['verfiy_retailer_status'=>1,'updated_at'=>date('Y-m-d H:i:s')]);
	        	return response()->json([ 'response' =>True,'message'=>'OTP Generated Successfully']);
	        }
	        else
	        {
	        	return response()->json([ 'response' =>False,'message'=>'Not Generated!!']);
	        }

		}


    }
    public function retailer_check_otp(Request $request)   
    {
    	$validator=Validator::make($request->all(),[
			'retailer_id'=>'required',
			'company_id'=>'required',
			'user_id'=>'required',
			'retailer_number'=>'required',
			'date_time'=>'required',
			'retailer_name'=> 'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
    	// return response()->json([ 'response' =>True,'message'=>'OTP Generated Successfully']);
    	$str = str_shuffle("123456789123456789123234567890456789123456789");
        $otp = substr($str, 0,6);  // return always a new string 
        $msg = ""; // custom message
        if($request->company_id == 47)
        {
        	$msg = "Thanks for Registering with Guruji Product Pvt Ltd. Indore Your confirmation pin is $otp , Looking forward for long term association with you"; // custom message
        }
        elseif($request->company_id == 50)
        {
        	$msg = "Your Janak Perfumers outlet registration number is: $otp"; // custom message
        }
        else
        {
        	$msg = "$otp is the OTP for creating OUTLET $request->retailer_name"; // custom message
        }
        // $msg = "$otp is the OTP for creating OUTLET $request->retailer_name";
        $mobile_no = $request->retailer_number;

        $check_retailer =  Retailer::where('other_numbers',$mobile_no)->where('retailer_status','!=',9)->count();
		if(($check_retailer)>0)
		{
			return response()->json([ 'response' =>False,'message'=>'Mobile number Already exist on any existing retailer!!']);
		}
		else
		{
			$send_sms = SendSms::send_sms($mobile_no,$msg); // send sms and get return message
	        if($send_sms->status=='success')
	        {
	        	$myArr = [
	        		'retailer_id' => $request->retailer_id,
	        		'company_id' => $request->company_id,
	        		'generated_by' => $request->user_id,
	        		'otp_number' => $otp,
	        		'mobile_no' => $mobile_no,
	        		'date_time' => $request->date_time,
	        		'server_date_time' => date('Y-m-d H:i:s'),
	        	];
	        	$insert_array = DB::table('retailer_check_sms')->insert($myArr);
	        	return response()->json([ 'response' =>True,'message'=>'OTP Generated Successfully']);
	        }
	        else
	        {
	        	return response()->json([ 'response' =>False,'message'=>'Not Generated!!']);
	        }

		}
        
        
    }

    public function retailer_submission(Request $request)
    {
    	$validator=Validator::make($request->all(),[
			'd_code'=>'required',
			'cr_time'=>'required',
			'user_id'=>'required',
			'company_id'=>'required',
			// 'add_str'=>'required',
			// 'full_address'=>'required',
			'r_type'=>'required',
			'long'=>'required',
			'r_name'=>'required',
			'id'=>'required',
			'category'=>'required',
			'l_code'=>'required',
			'mccmnclaccellid'=>'required',
			'r_pin_no'=>'required',
			'cr_date'=>'required',
			'cont_name'=>'required',
			'r_contact_no'=>'required',
			'lat'=>'required',
			'seq_no'=>'required',
			'battery_status'=>'required',
			'gps_status'=>'required',
			'otp' => 'required'
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		$user_id = $request->user_id;
		$company_id = $request->company_id;
        $cr_time = $request->cr_time;
		$dealer_id = $request->d_code;
		$retailer_address = $request->add_str;
		$track_address = $request->full_address;
		$retailer_type = $request->r_type;
		$lat = $request->lat;
		$lng = $request->long;
		$retailer_name = $request->r_name;
		$retailer_id = $request->id;
		$beat_id = $request->l_code;
		$mccmnclaccellid = $request->mccmnclaccellid;
		$pin_no = $request->r_pin_no;
		$email = $request->r_email;
		$created_date = $request->cr_date;
		$tin_no = $request->r_tin;
		$contact_person_name = $request->cont_name;
		$mobile_no = $request->r_contact_no;
		$sequence_id = $request->seq_no;
		$battery_status = $request->battery_status;
		$gps_status = $request->gps_status;
		$class = $request->category;
		$date_time = $created_date.' '.$cr_time;
		$lat_lng = $lat . ',' . $lng;
		$land_line = !empty($request->landline)?$request->landline:'0';
		$otp = $request->otp;


        DB::beginTransaction();


    	$time_fetch = strtotime($date_time);
        $time = $time_fetch - (25 * 60);
        $from_date = date("Y-m-d H:i:s", $time);
		$check_otp = DB::table('retailer_check_sms')->where('otp_number',$otp)->where('mobile_no',$mobile_no)->orderBy("id",'DESC')->first();

		if(empty($check_otp))
		{
			return response()->json(['response'=>False,'message'=>'Otp Does not match Please Try Again!!']);
		}
		else
		{
			$otp_generated_date = $check_otp->date_time;
			if($otp_generated_date>=$from_date && $otp_generated_date<=$date_time)
            {
                
            	if ($request->hasFile('image_source')) 
				{
		            $image = $request->file('image_source');
		            $imageName = date('YmdHis') . '.' . $image->getClientOriginalExtension();
		            $destinationPath = public_path('/retailer_image/' . $imageName);
		            Image::make($image)->save($destinationPath);
		        }
		        if (empty($imageName)) 
		        {
		            $imageName = NULL;
		        }
		        $retailer_data = Retailer::orderBy('id','DESC')->first();
		        $str = str_shuffle("123456789123456789123234567890456789123456789");
        		$str_sequence = substr($str, 0,3);
    			if(!empty($request->town_id))
    			{
					$myArr = [
						'id'=> $retailer_id,
						'retailer_code'=> $str_sequence,
						'name'=> $retailer_name,
						'class'=> $class,
						'image_name'=> $imageName,
						'dealer_id'=> $dealer_id,
						'location_id'=> $beat_id,
						'town_id_retailer'=> $request->town_id,
						'address'=> $retailer_address,
						'email'=> $email,
						'contact_per_name'=> $contact_person_name,
						'landline'=> $land_line,
						'other_numbers'=> $mobile_no,
						'tin_no'=> !empty($tin_no)?$tin_no:'0',
						'pin_no'=> $pin_no,
						'outlet_type_id'=> $retailer_type,
						'lat_long'=> $lat_lng,
						'mncmcclatcellid'=> $mccmnclaccellid,
						'track_address'=> $track_address,
						'created_on'=> $date_time,
						'created_by_person_id'=> $user_id,
						'status'=> 1,
						'sequence_id'=> $sequence_id,
						'retailer_status'=> 1,
						'battery_status'=> $battery_status,
						'gps_status'=> $gps_status,
						'company_id'=> $company_id,
						'verfiy_retailer_status'=> 1,


					];
    			}
    			else
    			{
    				$myArr = [
						'id'=> $retailer_id,
						'retailer_code'=> $str_sequence,
						'name'=> $retailer_name,
						'class'=> $class,
						'image_name'=> $imageName,
						'dealer_id'=> $dealer_id,
						'location_id'=> $beat_id,
						'address'=> $retailer_address,
						'email'=> $email,
						'contact_per_name'=> $contact_person_name,
						'landline'=> $land_line,
						'other_numbers'=> $mobile_no,
						'tin_no'=> !empty($tin_no)?$tin_no:'0',
						'pin_no'=> $pin_no,
						'outlet_type_id'=> $retailer_type,
						'lat_long'=> $lat_lng,
						'mncmcclatcellid'=> $mccmnclaccellid,
						'track_address'=> $track_address,
						'created_on'=> $date_time,
						'created_by_person_id'=> $user_id,
						'status'=> 1,
						'sequence_id'=> $sequence_id,
						'retailer_status'=> 1,
						'battery_status'=> $battery_status,
						'gps_status'=> $gps_status,
						'company_id'=> $company_id,
						'verfiy_retailer_status'=> 1,


					];
    			}
		        
				$myArr2 = [

					'user_id' => $user_id,
					'track_date' => $created_date,
					'track_time' => $cr_time,
					'mnc_mcc_lat_cellid' => $mccmnclaccellid,
					'lat_lng' => $lat_lng,
					'track_address' => $track_address,
					'status' => 'Retailer Creation',
					'server_date_time' => date('Y-m-d H:i:s'),
					'battery_status' => $battery_status,
					'gps_status' => $gps_status,
					'company_id' => $company_id,

				];

				$retailer_insertion = Retailer::create($myArr);
				if($retailer_insertion)
				{
					$tracking_ins = DB::table('user_work_tracking')->insert($myArr2);
					if($tracking_ins)
					{
		        		DB::commit();
						return response()->json([ 'response' =>True,'message'=>'Successfully Inserted With Verification']);
					}
					else
					{
						DB::rollback();
						return response()->json([ 'response' =>False,'message'=>'Please try again']);

					}
				}
				else
				{
					DB::rollback();
					return response()->json([ 'response' =>False,'message'=>'Please try again!!']);


				}
            } // end if condition
            else
            {
                return response()->json(['response'=>False,'message'=>'You Excedded the Maximum Time For Enter The Otp!!']);
            }
		}


		

		
		
    }

    public function send_sms($custom_numbers,$custom_messages)
    {
    	// Account details
		$apiKey = urlencode('64hne6Ar9t4-k6TmqMJLL6mRI5R04RaFH6Nn5vKi0g');
		
		// Message details
		$numbers = array($custom_numbers);
		$sender = urlencode('TXTLCL');
		$message = rawurlencode($custom_messages);
	 
		$numbers = implode(',', $numbers);
	 
		// Prepare data for POST request
		$data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
	 
		// Send the POST request with cURL
		$ch = curl_init('https://api.textlocal.in/send/');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		$response_decode = json_decode($response);
		// Process your response here
		return response()->json([ 'response' =>TRUE,'message'=>$response_decode]);
		// echo $response;
    }

 //    public function outlet_sale_order_submission(Request $request)
	// {
	// 	$validator=Validator::make($request->all(),[
 //            'order_id' => 'required',
 //            'retailer_id'=>'required',
 //            'dealer_id'=>'required',
 //            'location_id'=>'required',
 //            'sale_date'=>'required',
 //            'created_date'=>'required',
 //            'date_time'=>'required',
 //            'battery_status'=>'required',
 //            'gps_status'=>'required',
 //            'lat'=>'required',
 //            'lng'=>'required',
 //            'address'=>'required',
 //            'mcc_mnc_lac_cellid'=>'required',
 //            'primary_sale_summary'=>'required',
 //            'user_id'=>'required',
 //            'company_id'=>'required',
 //        ]);
 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
 //        }

	// 	$order_id = $request->order_id;
	// 	$dealer_id = $request->dealer_id;
	// 	$retailer_id = $request->retailer_id;
	// 	$location_id = $request->location_id;
	// 	$sale_date = $request->sale_date;
	// 	$created_date = $request->created_date;
	// 	$date_time = $request->date_time;
	// 	$battery_status = $request->battery_status;
	// 	$gps_status = $request->gps_status;
	// 	$lat = $request->lat;
	// 	$lng = $request->lng;
	// 	$address = $request->address;
	// 	$mcc_mnc_lac_cellid = $request->mcc_mnc_lac_cellid;
	// 	$user_id = $request->user_id;
	// 	$total_sale_value = $request->total_sale_value;
	// 	$total_sale_qty = $request->total_sale_qty;
	// 	$time = $request->time;
	// 	$remarks = $request->remarks;
	// 	$company_id = $request->company_id;
	// 	$primary_sale_summary = json_decode($request->primary_sale_summary);

	// 	$myArr = [
	// 		'order_id' => $order_id, 
	// 		'dealer_id' => $dealer_id, 
	// 		'date' => $sale_date, 
	// 		'time' => $time, 
	// 		'user_id' => $user_id,
	// 		'location_id'=>$location_id,
	// 		'retailer_id'=>$retailer_id,
	// 		'call_status'=>$call_status,
	// 		'total_sale_value'=>$total_sale_value,
	// 		'total_sale_qty'=>$total_sale_qty,
	// 		'date_time' => $date_time, 
	// 		'receive_date' => date('Y-m-d H:i:s'),
	// 		'battery_status' => $battery_status, 
	// 		'gps_status' => $gps_status, 
	// 		'lat' => $lat.' '.$lng
	// 		'lng' => $lng, 
	// 		'address' => $address,
	// 		'company_id' => $company_id, 
	// 		'mcc_mnc_lac_cellid' => $mcc_mnc_lac_cellid,
	// 		'server_date_time' => date('Y-m-d H:i:s'), 
	// 	];
	// 	$insert_first_layer = DB::table('user_sales_order')->insert($myArr);

	// 	if($insert_first_layer)
	// 	{
	// 		foreach ($primary_sale_summary as $key => $value) 
	// 		{
	// 			$arr['order_id'] = $value->order_id;
	// 			// $arr['id'] = $value->order_id;
	// 			$arr['product_id'] = $value->product_id;
	// 			$arr['rate'] = $value->rate;
	// 			$arr['quantity'] = $value->quantity;
	// 			$arr['barcode'] = $value->Barcode;   // 
	// 			$arr['scheme_qty'] = $value->scheme_qty;
	// 			$arr['cases'] = $value->case;
	// 			$arr['pcs'] = $value->pcs;
	// 			$arr['value'] = $value->value;
	// 			$arr['case_rate'] = $value->case_rate;
	// 			$arr['pr_rate'] = $value->pcs_rate;
	// 			$final_arr[] = $arr;

	// 		}

	// 		$insert_second_layer = DB::table('user_sales_order_details')->insert($arr);

	// 		if($insert_second_layer)
	// 		{
 //        		DB::commit();
	// 			return response()->json([ 'response' =>True,'message'=>'Success Fully Inserted']);		


	// 		}
	// 		else
	// 		{
	// 			DB::rollback();
	// 			return response()->json([ 'response' =>False,'message'=>'Something went wrong !!']);		

	// 		}
	// 	}
	// 	else
	// 	{
	// 		DB::rollback();
	// 		return response()->json([ 'response' =>False,'message'=>'Something went wrong !!']);	

	// 	}



	// }
	public function retailer_login(Request $request)
	{

	 	$validator=Validator::make($request->all(),[
            'uname' => 'required',
            'pass'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
		$uname = $request->uname;
		$pass = $request->pass;

		$query_login = DB::table('retailer')
					// ->join('_role','_role.role_id','=','dealer_person_login.role_id')
					->select('dealer_id','email','location_id','id','name','company_id','landline','other_numbers','username as user_name')
					->where('username',$uname)
					// ->where('person_password',DB::raw("AES_ENCRYPT('".trim($pass)."', '".Lang::get('common.db_salt')."')"))
					->whereRaw("AES_DECRYPT(password, 'demo') = '$pass'")
					->where('retailer_status',1)
					->get();
		$dms_login_array = array();
		if(!empty($query_login))
		{
			foreach ($query_login as $key => $value) 
			{
			 // 	$dms_primary_id = $value->dpId;
				// $person_fullname = $value->person_name
				// $mobile = $value->phone;
				// $email = $value->email;
				// $state_id = $value->state_id;
				$check_role_id = 0;
				$company_id = $value->company_id;

				$dms_login_array['retailer_primary_id'] = $value->id;
				$dms_login_array['name'] = $value->name;
				$dms_login_array['user_name'] = $value->user_name;
				$dms_login_array['mobile'] = !empty($value->landline)?$value->landline:$value->other_numbers;
				$dms_login_array['email'] = $value->email;
				$dms_login_array['location_id'] = $value->location_id;
				// $dms_login_array['person_role_id'] = $value->role_id;
				// $dms_login_array['person_role_name'] = $value->rolename;
				$dms_login_array['dealer_id'] = $value->dealer_id;
				$dms_login_array['company_id'] = $value->company_id;
			}

		 	$check_role_wise_assing_module = DB::table('role_app_module')
                                            ->join('master_list_module','master_list_module.id','=','role_app_module.module_id')
                                            ->select('master_list_module.icon_image as module_icon_image','master_list_module.id as module_id','role_app_module.title_name as module_name','master_list_module.url as module_url')
                                            ->where('role_app_module.company_id',$company_id)
                                            ->where('role_app_module.status',1)
                                            ->where('role_app_module.status',1)
                                            ->where('role_app_module.role_id',$check_role_id)
                                            ->orderBy('role_app_module.module_sequence','ASC')
                                            ->get();
                    // dd($check_role_wise_assing_module);
                   
            if(COUNT($check_role_wise_assing_module)>0)
            {
                $module = array();
                foreach ($check_role_wise_assing_module as $key => $value)
                {
                    $module[$key]['module_icon_image'] = !empty($value->module_icon_image)?$value->module_icon_image:'';
                    $module[$key]['module_id'] = "$value->module_id";
                    $module[$key]['module_name'] = !empty($value->module_name)?$value->module_name:'';
                    $module[$key]['module_url'] = !empty($value->module_url)?$value->module_url:'';
                }
                $role_sub_module = DB::table('role_sub_modules')
                            ->join('master_list_sub_module','master_list_sub_module.id','=','role_sub_modules.sub_module_id')
                            ->select('master_list_sub_module.module_id as module_id','role_sub_modules.sub_module_name as sub_module_name','master_list_sub_module.id as sub_module_id','master_list_sub_module.path as sub_module_url','master_list_sub_module.image_name as sub_module_icon_image')
                            ->where('role_sub_modules.company_id',$company_id)
                            ->where('role_sub_modules.status',1)
                            ->where('master_list_sub_module.status',1)
                            ->where('role_sub_modules.role_id',$check_role_id)
                            ->orderBy('role_sub_modules.module_sequence','ASC')
                            ->get();
                $sub_module_arr = array();
                foreach ($role_sub_module as $key => $value)
                {
                    $sub_module_arr[$key]['sub_module_icon_image'] = !empty($value->sub_module_icon_image)?$value->sub_module_icon_image:'';
                    $sub_module_arr[$key]['module_id'] = "$value->module_id";
                    $sub_module_arr[$key]['sub_module_id'] = "$value->sub_module_id";
                    $sub_module_arr[$key]['sub_module_name'] = !empty($value->sub_module_name)?$value->sub_module_name:'';
                    $sub_module_arr[$key]['sub_module_url'] = !empty($value->sub_module_url)?$value->sub_module_url:'';
                }

                $other_module = DB::table('role_app_other_module_assign')
                        ->join('master_other_app_module','master_other_app_module.id','=','role_app_other_module_assign.module_id')
                        ->select('master_other_app_module.image_name as other_module_icon_image','role_app_other_module_assign.title_name as other_module_name','role_app_other_module_assign.module_id as other_module_id')
                        ->where('role_app_other_module_assign.status',1)
                        ->where('role_app_other_module_assign.company_id',$company_id)
                        ->where('master_other_app_module.id','!=',2) // beacaue this id belongs to retailer creation with otp so this condition true above the role wise module condition starts
                        ->where('master_other_app_module.status',1)
                        ->where('role_app_other_module_assign.role_id',$check_role_id)
                        ->orderBy('role_app_other_module_assign.module_sequence','ASC')
                        ->get();
                $other_module_arr = array();
                foreach ($other_module as $key => $value)
                {
                    $other_module_arr[$key]['other_module_icon_image'] = !empty($value->other_module_icon_image)?$value->other_module_icon_image:'';
                    $other_module_arr[$key]['other_module_id'] = "$value->other_module_id";
                    $other_module_arr[$key]['other_module_name'] = !empty($value->other_module_name)?$value->other_module_name:'';
                }
                // dd($other_module_arr);

            }
            else
            {
                $app_module = DB::table('app_module')
                        ->join('master_list_module','master_list_module.id','=','app_module.module_id')
                        ->select('master_list_module.icon_image as module_icon_image','master_list_module.id as module_id','app_module.title_name as module_name','master_list_module.url as module_url')
                        ->where('app_module.company_id',$company_id)
                        ->where('app_module.status',1)
                        ->where('master_list_module.status',1)
                        ->orderBy('app_module.module_sequence','ASC')
                        ->get();
                $module = array();
                foreach ($app_module as $key => $value)
                {
                    $module[$key]['module_icon_image'] = !empty($value->module_icon_image)?$value->module_icon_image:'';
                    $module[$key]['module_id'] = "$value->module_id";
                    $module[$key]['module_name'] = !empty($value->module_name)?$value->module_name:'';
                    $module[$key]['module_url'] = !empty($value->module_url)?$value->module_url:'';
                }
                $sub_module = DB::table('_sub_modules')
                            ->join('master_list_sub_module','master_list_sub_module.id','=','_sub_modules.sub_module_id')
                            ->select('master_list_sub_module.module_id as module_id','_sub_modules.sub_module_name as sub_module_name','master_list_sub_module.id as sub_module_id','master_list_sub_module.path as sub_module_url','master_list_sub_module.image_name as sub_module_icon_image')
                            ->where('_sub_modules.company_id',$company_id)
                            ->where('_sub_modules.status',1)
                            ->where('master_list_sub_module.status',1)
                            ->orderBy('_sub_modules.module_sequence','ASC')
                            ->get();
                $sub_module_arr = array();
                foreach ($sub_module as $key => $value)
                {
                    $sub_module_arr[$key]['sub_module_icon_image'] = !empty($value->sub_module_icon_image)?$value->sub_module_icon_image:'';
                    $sub_module_arr[$key]['module_id'] = "$value->module_id";
                    $sub_module_arr[$key]['sub_module_id'] = "$value->sub_module_id";
                    $sub_module_arr[$key]['sub_module_name'] = !empty($value->sub_module_name)?$value->sub_module_name:'';
                    $sub_module_arr[$key]['sub_module_url'] = !empty($value->sub_module_url)?$value->sub_module_url:'';
                }
                $other_module = DB::table('app_other_module_assign')
                        ->join('master_other_app_module','master_other_app_module.id','=','app_other_module_assign.module_id')
                        ->select('master_other_app_module.image_name as other_module_icon_image','app_other_module_assign.title_name as other_module_name','app_other_module_assign.module_id as other_module_id')
                        ->where('app_other_module_assign.status',1)
                        ->where('app_other_module_assign.company_id',$company_id)
                        // ->where('master_other_app_module.id','!=',2) // beacaue this id belongs to retailer creation with otp so this condition true above the role wise module condition starts
                        ->where('master_other_app_module.status',1)
                        ->orderBy('app_other_module_assign.module_sequence','ASC')
                        ->get();
                $other_module_arr = array();
                foreach ($other_module as $key => $value)
                {
                    $other_module_arr[$key]['other_module_icon_image'] = !empty($value->other_module_icon_image)?$value->other_module_icon_image:'';
                    $other_module_arr[$key]['other_module_id'] = "$value->other_module_id";
                    $other_module_arr[$key]['other_module_name'] = !empty($value->other_module_name)?$value->other_module_name:'';
                }
                // dd($other_module_arr);
            }
			#......................................reponse parameters starts here ..................................................##
                    return response()->json([
                        'response' =>True,
                        'details'=>$dms_login_array,
              		 	'app_module'=> $module,
                        'sub_module'=> $sub_module_arr,
                        'message'=>'Success!!']);
                #......................................reponse parameters ends here ..................................................##
		}
		else
		{
            return response()->json([ 'response' =>False,'message'=>'!!Credentials not match with our records!!']);        

		}
	
	}

	public function layer_for_retailer_signup(Request $request)
	{
		$validator=Validator::make($request->all(),[
			'company_id'=>'required',
			// 'state_id'=>'required',
				
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
		$company_id = $request->company_id;
		// $state_id = $request->state_id;

		$state = Location3::select('name as name','id as id')
				// ->where('location_3_id',$state_id)
				->where('company_id',$company_id)
				->groupBy('id')
				->get();

		$town = Location6::join('location_5','location_5.id','=','location_6.location_5_id')
				->join('location_4','location_4.id','=','location_5.location_4_id')
				->select('location_6.name as name','location_6.id as id','location_3_id as l3_id')
				// ->where('location_3_id',$state_id)
				->where('location_6.company_id',$company_id)
				->groupBy('location_6.id')
				->get();

		$distributor = DB::table('dealer')
					->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
					->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
					->select('dealer.name as name','dealer.id as id','location_6_id as l6_id','state_id')
					// ->where('dealer.state_id',$state_id)
					->where('dealer.company_id',$company_id)
					->groupBy('dealer.id')
					->get();

		$retailer_type = DB::table('_retailer_outlet_type')
						->where('company_id',$company_id)
						->where('status',1)
						->get();

		$retailer_category = DB::table('_retailer_outlet_category')
						->where('company_id',$company_id)
						->where('status',1)
						->get();



		return response()->json([
                    'response' =>True,
                    'state'=>$state,
                    'town'=>$town,
                    'distributor'=>$distributor,
                    'retailer_type'=> $retailer_type,
                    'retailer_category'=> $retailer_category,
                    'message'=>'Success!!']);
	}

	public function retailer_otp_for_signup(Request $request)   // not in use
    {
    	$validator=Validator::make($request->all(),[
			
			'company_id'=>'required',
			
			'mobile_no'=>'required',
			'date_time'=>'required',
			// 'retailer_name'=> 'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
    	// return response()->json([ 'response' =>True,'message'=>'OTP Generated Successfully']);
    	$str = str_shuffle("123456789123456789123234567890456789123456789");
        $otp = substr($str, 0,6);  // return always a new string
        $retailer_name = 'test'; 
        $msg = "Your otp for btw Retailership is:- $otp"; // custom message
        // $msg = "$otp is the OTP for creating OUTLET $request->retailer_name";
        $mobile_no = $request->mobile_no;

        $check_retailer =  Retailer::where('other_numbers',$mobile_no)->count();
		if(($check_retailer)>0)
		{
			return response()->json([ 'response' =>False,'message'=>'Mobile number Already exist on any existing retailer!!']);
		}
		else
		{
			$send_sms = SendSms::send_sms($mobile_no,$msg); // send sms and get return message
	        if($send_sms->status=='success')
	        {
	        	$myArr = [
	        		'retailer_id' => 0,
	        		'company_id' => $request->company_id,
	        		'generated_by' => 0,
	        		'otp_number' => $otp,
	        		'mobile_no' => $mobile_no,
	        		'date_time' => $request->date_time,
	        		'server_date_time' => date('Y-m-d H:i:s'),
	        	];
	        	$insert_array = DB::table('retailer_check_sms')->insert($myArr);
	        	return response()->json([ 'response' =>True,'message'=>'OTP Generated Successfully']);
	        }
	        else
	        {
	        	return response()->json([ 'response' =>False,'message'=>'Not Generated!!']);
	        }

		}
        
        
    }
    public function verify_retailer_for_signup(Request $request)
    {
    	$validator=Validator::make($request->all(),[
			
			'company_id'=>'required',
			'otp'=>'required',
			
			'mobile_no'=>'required',
			'date_time'=>'required',
			// 'retailer_name'=> 'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		$date_time = $request->date_time;
		$otp = $request->otp;
		$mobile_no = $request->mobile_no;
		$time_fetch = strtotime($date_time);
        $time = $time_fetch - (25 * 60);
        $from_date = date("Y-m-d H:i:s", $time);
		$check_otp = DB::table('retailer_check_sms')->where('otp_number',$otp)->where('mobile_no',$mobile_no)->orderBy("id",'DESC')->first();
		// dd($check_otp);
		if(empty($check_otp))
		{
			return response()->json(['response'=>False,'message'=>'Otp Does not match Please Try Again!!']);
		}
		else
		{
			$otp_generated_date = $check_otp->date_time;
			if($otp_generated_date>=$from_date && $otp_generated_date<=$date_time)
            {
	        	return response()->json([ 'response' =>True,'message'=>'Successfully Verified!!']);

            }
            else
            {
	        	return response()->json([ 'response' =>False,'message'=>'Time out Better luck next time!!']);

            }
        }
    }
    public function retailer_submission_btw(Request $request)
    {
    	$validator=Validator::make($request->all(),[
			'd_code'=>'required',
			'cr_time'=>'required',
			'user_id'=>'required',
			'company_id'=>'required',
			'add_str'=>'required',
			'full_address'=>'required',
			'r_type'=>'required',
			'long'=>'required',
			'r_name'=>'required',
			'id'=>'required',
			'category'=>'required',
			'l_code'=>'required',
			'mccmnclaccellid'=>'required',
			'r_pin_no'=>'required',
			'cr_date'=>'required',
			'cont_name'=>'required',
			'r_contact_no'=>'required',
			'lat'=>'required',
			'seq_no'=>'required',
			'battery_status'=>'required',
			'gps_status'=>'required',
			'otp' => 'required',
			'town_id'=>'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
		}
		$user_id = $request->user_id;
		$company_id = $request->company_id;
        $cr_time = $request->cr_time;
		$dealer_id = $request->d_code;
		$retailer_address = $request->add_str;
		$track_address = $request->full_address;
		$retailer_type = $request->r_type;
		$lat = $request->lat;
		$lng = $request->long;
		$retailer_name = $request->r_name;
		$retailer_id = $request->id;
		$beat_id = $request->l_code;
		$mccmnclaccellid = $request->mccmnclaccellid;
		$pin_no = $request->r_pin_no;
		$email = $request->r_email;
		$created_date = $request->cr_date;
		$tin_no = $request->r_tin;
		$contact_person_name = $request->cont_name;
		$mobile_no = $request->r_contact_no;
		$sequence_id = $request->seq_no;
		$battery_status = $request->battery_status;
		$gps_status = $request->gps_status;
		$class = $request->category;
		$date_time = $created_date.' '.$cr_time;
		$lat_lng = $lat . ',' . $lng;
		$land_line = !empty($request->landline)?$request->landline:'0';
		$otp = $request->otp;

        DB::beginTransaction();
                
    	if ($request->hasFile('image_source')) 
		{
            $image = $request->file('image_source');
            $imageName = date('YmdHis') . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/retailer_image/' . $imageName);
            Image::make($image)->save($destinationPath);
        }
        if (empty($imageName)) 
        {
            $imageName = NULL;
        }
        $retailer_data = Retailer::orderBy('id','DESC')->first();
        $str = str_shuffle("123456789123456789123234567890456789123456789");
		$str_sequence = substr($str, 0,3);
		if(!empty($request->town_id))
		{
			$myArr = [
				'id'=> $retailer_id,
				'retailer_code'=> $str_sequence,
				'name'=> $retailer_name,
				'class'=> $class,
				'image_name'=> $imageName,
				'dealer_id'=> $dealer_id,
				'location_id'=> $beat_id,
				'state_id_retailer'=> $request->town_id,
				'address'=> $retailer_address,
				'email'=> $email,
				'contact_per_name'=> $contact_person_name,
				'landline'=> $land_line,
				'other_numbers'=> $mobile_no,
				'tin_no'=> !empty($tin_no)?$tin_no:'0',
				'pin_no'=> $pin_no,
				'outlet_type_id'=> $retailer_type,
				'lat_long'=> $lat_lng,
				'username'=> $mobile_no,
				'password'=> DB::raw("AES_ENCRYPT('".trim($mobile_no)."', '".Lang::get('common.db_salt')."')"),
				'mncmcclatcellid'=> $mccmnclaccellid,
				'track_address'=> $track_address,
				'created_on'=> $date_time,
				'created_by_person_id'=> $user_id,
				'status'=> 1,
				'sequence_id'=> $sequence_id,
				'retailer_status'=> 1,
				'battery_status'=> $battery_status,
				'gps_status'=> $gps_status,
				'company_id'=> $company_id,
				'verfiy_retailer_status'=> 1,


			];
		}
		else
		{
			$myArr = [
				'id'=> $retailer_id,
				'retailer_code'=> $str_sequence,
				'name'=> $retailer_name,
				'class'=> $class,
				'image_name'=> $imageName,
				'dealer_id'=> $dealer_id,
				'location_id'=> $beat_id,
				'address'=> $retailer_address,
				'email'=> $email,
				'contact_per_name'=> $contact_person_name,
				'landline'=> $land_line,
				'other_numbers'=> $mobile_no,
				'tin_no'=> !empty($tin_no)?$tin_no:'0',
				'pin_no'=> $pin_no,
				'outlet_type_id'=> $retailer_type,
				'lat_long'=> $lat_lng,
				'mncmcclatcellid'=> $mccmnclaccellid,
				'track_address'=> $track_address,
				'created_on'=> $date_time,
				'created_by_person_id'=> $user_id,
				'status'=> 1,
				'sequence_id'=> $sequence_id,
				'retailer_status'=> 1,
				'battery_status'=> $battery_status,
				'gps_status'=> $gps_status,
				'company_id'=> $company_id,
				'verfiy_retailer_status'=> 1,


			];
		}
        
		$myArr2 = [

			'user_id' => $user_id,
			'track_date' => $created_date,
			'track_time' => $cr_time,
			'mnc_mcc_lat_cellid' => $mccmnclaccellid,
			'lat_lng' => $lat_lng,
			'track_address' => $track_address,
			'status' => 'Retailer Creation',
			'server_date_time' => date('Y-m-d H:i:s'),
			'battery_status' => $battery_status,
			'gps_status' => $gps_status,
			'company_id' => $company_id,

		];

		$retailer_insertion = Retailer::create($myArr);

		if($retailer_insertion)
		{
        	

			$tracking_ins = DB::table('user_work_tracking')->insert($myArr2);
			if($tracking_ins)
			{
				$msg = "Username: $mobile_no,Password: $mobile_no for BTW RetailerShip.";
				$send_sms = SendSms::send_sms($mobile_no,$msg); // send sms and get return message

		        if($send_sms->status=='success')
		        {
		        	DB::commit();
		        	return response()->json([ 'response' =>True,'message'=>'Submitted Succefuly With Verification!! Also username and paswword sent to you mobile number !!']);
		        }
		        else
		        {
		        	DB::commit();
		        	return response()->json([ 'response' =>False,'message'=>'Submitted But Error Occured While Sending the SMS!!']);
		        }
        		
				// return response()->json([ 'response' =>True,'message'=>'Successfully Inserted With Verification']);
			}
			else
			{
				
				return response()->json([ 'response' =>False,'message'=>'Please try again']);

			}
		}
		else
		{
			DB::rollback();
			return response()->json([ 'response' =>False,'message'=>'Please try again!!']);


		}
           
		


		

		
		
    }
}
