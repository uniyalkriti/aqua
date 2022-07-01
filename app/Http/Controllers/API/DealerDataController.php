<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Person;
use App\UserDetail;
use App\Company;
use App\JuniorData;
use App\Dealer;
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

class DealerDataController extends Controller
{
    public $successStatus = 200;
    public $response_true = True;
    public $response_false = False;

   
    public function dealer_submission(Request $request)
    {
    	$validator=Validator::make($request->all(),[
			'user_id'=> 'required',
			'company_id'=> 'required',
			'state_id'=> 'required',
			'town_id'=> 'required',
			'csa_id'=> 'required',
			'date'=> 'required',
			'time'=> 'required',
			'lng'=> 'required',
			'lat'=> 'required',
			'mcc_mnc_lac_cellId'=> 'required',
			'gps_status'=> 'required',
			'battery_status'=> 'required',
			'contact_person'=> 'required',
			'name'=> 'required',
			'dealer_code'=> 'required',
			'address'=> 'required',
			'geo_address'=>'required',
			'email'=> 'required',
			'landline'=> 'required',
			'other_numbers'=> 'required',
			'tin_no'=> 'required',
			'pin_no'=> 'required',
			'ownership_type_id'=> 'required',
			'avg_per_month_pur'=> 'required',
          
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
		}
		$user_id = $request->user_id;
		$company_id = $request->company_id;
		$state_id = $request->state_id;
		$town_id = $request->town_id;
		$csa_id = $request->csa_id;
		$terms = 'NA';
		$dealer_status = 1;
		$dms_status = 0;
		$edit_stock = 0;
		$date = $request->date;
		$time = $request->time;
		$lng = $request->lng;
		$lat = $request->lat;
		$geo_address = $request->geo_address;
       	$lat_lng = $lat.' '.$lng;
       	$mcc_mnc_lac_cellId = $request->mcc_mnc_lac_cellId;
		$gps_status = $request->gps_status;
		$battery_status = $request->battery_status;
		$contact_person = $request->contact_person;
		$name = $request->name;
		$dealer_code = !empty($request->dealer_code)?$request->dealer_code:'0';
		$address = $request->address;
		$email = $request->email;
		$landline = $request->landline;
		$other_numbers = $request->other_numbers;
		$tin_no = $request->tin_no;
		$fssai_no = 0;
		$pin_no = $request->pin_no;
		$ownership_type_id = $request->ownership_type_id;
		$avg_per_month_pur = $request->avg_per_month_pur;


        DB::beginTransaction();
    	
        
        $myArr = [
			'name'=> $name,
			'contact_person'=> $contact_person,
			'dealer_code'=> $dealer_code,
			'address'=> $address,
			'email'=> $email,
			'landline'=> $landline,
			'other_numbers'=> $other_numbers,
			'tin_no'=> $tin_no,
			'fssai_no'=> $fssai_no,
			'pin_no'=> $pin_no,
			'ownership_type_id'=> $ownership_type_id,
			'avg_per_month_pur'=> $avg_per_month_pur,
			'state_id'=> $state_id,
			'town_id'=> $town_id,
			'csa_id'=> $csa_id,
			'terms'=> $terms,
			'dealer_status'=> $dealer_status,
			'dms_status'=> $dms_status,
			'edit_stock'=> $edit_stock,
			'date'=> $date,
			'time'=> $time,
			'lng'=> $lng,
			'lat'=> $lat,
			'mcc_mnc_lac_cellId'=> $mcc_mnc_lac_cellId,
			'created_at'=> date('Y-m-d H:i:s'),
			'created_by'=> $user_id,
			'gps_status' => $gps_status,
			'battery_status' => $battery_status,
			'company_id'=> $company_id,

		];
		$myArr2 = [

			'user_id' => $user_id,
			'track_date' => $date,
			'track_time' => $time,
			'mnc_mcc_lat_cellid' => $mcc_mnc_lac_cellId,
			'lat_lng' => $lat_lng,
			'track_address' => $geo_address,
			'status' => 'Distribution Creation',
			'server_date_time' => date('Y-m-d H:i:s'),
			'battery_status' => $battery_status,
			'gps_status' => $gps_status,
			'company_id' => $company_id,

		];

		$dealer_insertion = Dealer::create($myArr);
		if($dealer_insertion)
		{
			$tracking_ins = DB::table('user_work_tracking')->insert($myArr2);
			if($tracking_ins)
			{
        		DB::commit();
				return response()->json([ 'response' =>True,'message'=>'Distributor Successfully Inserted', 'dealer_id' => '$dealer_insertion->id']);
			}
			else
			{
				DB::rollback();
				return response()->json([ 'response' =>False,'message'=>'Please try again','dealer_id'=>'']);

			}
		}
		else
		{
			DB::rollback();
			return response()->json([ 'response' =>False,'message'=>'Please try again!!','dealer_id'=> '']);


		}
		
    }

}
