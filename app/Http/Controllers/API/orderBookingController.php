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

class orderBookingController extends Controller
{
    public $successStatus = 200;
    public $response_true = True;
    public $response_false = False;

    public function order_check_otp(Request $request)
    {
    	$validator=Validator::make($request->all(),[
			'retailer_id'=>'required',
			'company_id'=>'required',
			'user_id'=>'required',
			'retailer_number'=>'required',
			'date_time'=>'required',
			
			'order_id'=>'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
		}
    	// return response()->json([ 'response' =>True,'message'=>'OTP Generated Successfully']);
    	$str = str_shuffle("123456789123456789123234567890456789123456789");
        $otp = substr($str, 0,6);  // return always a new string 
        $msg = "Your Otp for this order is $otp"; // custom message
        // $msg = "$otp is the OTP for creating OUTLET $request->retailer_name";
        $mobile_no = $request->retailer_number;

        $check_retailer =  Retailer::where('id',$request->retailer_id)->count();
        $check_order_id = DB::table('order_check_sms')->where('order_id',$request->order_id)->count();
		if(($check_order_id)>0)
		{
			
        	return response()->json([ 'response' =>True,'message'=>'Otp Already Generated!!']);

			
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
	        		'order_id' => $request->order_id,
	        		'date_time' => $request->date_time,
	        		'server_date_time' => date('Y-m-d H:i:s'),
	        	];
	        	$insert_array = DB::table('order_check_sms')->insert($myArr);
	        	return response()->json([ 'response' =>True,'message'=>'OTP Generated Successfully']);
	        }
	        else
	        {
	        	return response()->json([ 'response' =>False,'message'=>'Not Generated!!']);
	        }

		}
        
        
    }

    public function order_submission(Request $request)
    {
    	$validator=Validator::make($request->all(),[
			'company_id'=>'required',
			'retailer_id'=>'required',
			'user_id'=>'required',
			'order_id'=>'required',
			'otp' => 'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
		}


        DB::beginTransaction();

		$user_id = $request->user_id;
		$retailer_id = $request->retailer_id;
		$order_id = $request->order_id;
		$otp = $request->otp;
		$mobile_no = $request->mobile_no;
		$company_id = $request->company_id;
		$check_otp = DB::table('order_check_sms')->where('otp_number',$otp)->where('company_id',$company_id)->where('retailer_id',$retailer_id)->where('order_id',$order_id)->where('generated_by',$user_id)->first();

		if(empty($check_otp))
		{
			return response()->json(['response'=>False,'message'=>'Otp Does not match Please Try Again!!']);
		}
		else
		{
			
            return response()->json(['response'=>True,'message'=>'Succesfully Matched!!']);
         
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

}
