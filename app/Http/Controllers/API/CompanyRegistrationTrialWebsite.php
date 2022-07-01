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
use Mail;
use Image;

// use App\Company;
use App\_role;
// use App\User;
use App\PersonDetail;
use App\PersonLogin;
// use App\Person;
use App\Url;
use App\Version;


use App\Location1;
use App\Location2;
use App\Location3;
use App\Location4;
use App\Location5;
use App\Location6;
use App\Location7;
use App\SS;
use App\Dealer;
use App\Urlassign;
use App\Catalog1;
use App\Catalog2;
use App\Catalog3;
use App\CatalogProduct;
use App\AppModule;
use App\SendSms;


class CompanyRegistrationTrialWebsite extends Controller
{
    public $successStatus = 401;
    public $response_true = True;
    public $response_false = False;

	public function otp_send_status(Request $request)
	{
		$mobile_no = $request->number;
		$str = str_shuffle("123456789123456789123234567890456789123456789");
        $otp = substr($str, 0,6);  // return always a new string
		// $msg = "$otp is the OTP for creating OUTLET $request->title"
        $msg = "$otp use this OTP for registration ,plz don't share with Anyone.-manacle technologies"; // custom message
		$send_sms = SendSms::send_sms($mobile_no,$msg); // send sms and get return message
	        if($send_sms->status=='success')
	        {
	        	$myArr = [
	        		'retailer_id' => date('YmdHis'),
	        		'company_id' => 0,
	        		'generated_by' => 0,
	        		'otp_number' => $otp,
	        		'mobile_no' => $mobile_no,
	        		'date_time' => date('Y-m-d H:i:s'),
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
    public function trial_demo_website_api(Request $request)
    {
    	// dd($request);
    	// $request = $request->all_data;
    	$msg = "Not Submitted";
    	
		DB::beginTransaction();		

		$company_name = trim(strtolower($request->company_name));
		$checker = Company::where('name',$company_name)->get();
		if(COUNT($checker))
		{
			return response()->json([ 'response' =>False,'msg'=>'Duplicate Entry']);
		}
		$checker = Company::where('title',$request->title)->get();
		if(COUNT($checker))
		{
			return response()->json([ 'response' =>False,'msg'=>'Duplicate Entry']);
		}

		$time_fetch = strtotime(date('Y-m-d H:i:s'));
		$date_time = date('Y-m-d H:i:s');
		// dd($time_fetch);

        $time = $time_fetch - (25 * 60);

        $from_date = date("Y-m-d H:i:s", $time);
        $otp = $request->otp;
        $mobile_no = $request->number;
        
        $check_otp = DB::table('retailer_check_sms')->where('otp_number',$otp)->where('mobile_no',$mobile_no)->orderBy("id",'DESC')->first();
        if(empty($check_otp))
        {
            return response()->json(['response'=>False,'message'=>'Enter Otp not found with our records!!']);
        }
        $otp_generated_date = $check_otp->server_date_time;


        if($otp_generated_date>=$from_date && $otp_generated_date<=$date_time)
        {

        }
        else
        {
            return response()->json(['response'=>False,'message'=>'You Excedded the Maximum Time For Enter The Otp!!']);

        }
		
		$message = " mSELL, developed and powered by Manacle Technologies Pvt. Ltd";
		$message_link = "http://manacleindia.com";
		$domain_url = "demo.msell.in";
		$myArr = [
			'name'=> trim(strtolower($request->company_name)),
			'title'=> $request->title,
			'website'=> $request->website,
			'status'=> !empty($request->status)?$request->status:'1',
			'email'=> $request->email,
			'address'=> $request->address,
			'other_numbers'=> $request->number,
			'contact_per_name'=>$request->contact_per_name,
			'domain_url'=>$domain_url,
			'landline'=> $request->landline,
			'footer_message'=>$message,
			'footer_link'=>$message_link,
			'created_by'=>'0',
			'source'=>1,
			'created_at'=> date('Y-m-d H:i:s'),

		];
		$insert_query = Company::create($myArr);
		$company_id = $insert_query->id;
		
		$url = Url::create([
            'company_id' => $insert_query->id,
            'signin_url' => 'login_demo',
            'sync_post_url' => 'sync_post_v35.php',
            'image_url'=>'image_sync',
            'version_code' => '2.0.1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            

        ]);

        $version = Version::create([
            'company_id' => $insert_query->id,
            // 'company_id' => $company_id,
            'version_name' => '2.0.1',
            'version_code' => '5',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),

        ]);
	
		$role = _role::create(['company_id'=>$insert_query->id,'rolename'=>"Super Admin",'senior_role_id'=>'0','created_at'=>date('Y-m-d H:i:s')]);
		$userArr = 
		[
			'email'=>str_replace('@','_',$request->user_name).'@'.$request->company_name,
			'company_id'=>$insert_query->id,
			'is_admin'=>1,
            'original_pass'=>$request->password,
			'password'=>bcrypt($request->password),
			'role_id'=>$role->id,
			'status'=>1,
			'created_at'=>date('Y-m-d H:i:s'),
		];
		$user = User::create($userArr);

		$personArr = [
			'id'=>$user->id,
            'first_name' => trim($request->user_name),
            'last_name' => trim($request->user_name),
            'role_id' => trim($role->id),
            'person_id_senior' => trim($request->senior_person),
            'version_code_name' => '',
            'resigning_date' => date('Y-m-d'),
            'head_quar' => 'NA',
            'mobile' => trim($request->number),
            'email' => trim($request->email),
            'state_id' => 0,
            'emp_code' => 01,
            'company_id' => $insert_query->id,
			'joining_date' => date('Y-m-d'),
            'status' => 1,
		];
		$person=Person::create($personArr);

		$personLogArr=[
            'person_id'=>$user->id,
            'address'=>trim($request->address),
            'company_id' => $insert_query->id,
            'gender'=>'M',
            'created_on'=>date('Y-m-d H:i:s'),
        ];
        $person_log=PersonDetail::create($personLogArr);

		$person_login_arr=[
            'person_id'=>$user->id,
            'emp_id'=>'01',
            'company_id' => $insert_query->id,
            'person_username'=>trim(str_replace('@','_',$request->user_name).'@'.$request->company_name),
            'person_password'=>DB::raw("AES_ENCRYPT('".trim($request->password)."', '".Lang::get('common.db_salt')."')"),
            'person_status'=>1,
        ];
        $person_login=PersonLogin::create($person_login_arr);
		//you can use this code for upload image to `public/storage/ directory :

		

		// temporariy data insertion starts here 
		$location_1 = Location1::create([
    					'name'=>'North Zone',
    					'company_id'=>$company_id,
    				]);

		$location_2 = Location2::create([
    					'location_1_id'=>$location_1->id,
    					'name'=>'North Region',
    					'company_id'=>$company_id
    				]);

		$location_3 = Location3::create([
    					'location_2_id'=>$location_2->id,
    					'name'=>'Uttar Pardesh',
    					'company_id'=>$company_id
    				]);

		$location_4 = Location4::create([
    					'location_3_id'=>$location_3->id,
    					'name'=>'Noida Area',
    					'company_id'=>$company_id
    				]);
		$location_5 = Location5::create([
    					'location_4_id'=>$location_4->id,
    					'name'=>'Noida Area Head Quater',
    					'company_id'=>$company_id
    				]);
		$location_6 = Location6::create([
    					'location_5_id'=>$location_5->id,
    					'name'=>'North Delhi',
    					'company_id'=>$company_id
    				]);

		$location_7 = Location7::create([
    					'location_6_id'=>$location_6->id,
    					'name'=>'North Beat',
    					'company_id'=>$company_id
    				]);

        $role_array = array('General Manager','Sales Manager','Frontliner');
        foreach($role_array as $rkey => $rvalue){
        	$select_query = _role::where('company_id',$company_id)->orderBy('role_id','DESC')->first();
        	// dd($select_query);
			$role = _role::create([
						'company_id'=>$insert_query->id,
						'rolename'=> $rvalue,
						'senior_role_id'=>!empty($select_query->role_id)?$select_query->role_id:'0',
						'created_at'=>date('Y-m-d H:i:s')
					]);
        }
		$select_query_role_wise = _role::where('company_id',$company_id)->orderBy('role_id','DESC')->get();

        // dealer insetion start here 

        $data_insert_ownership = DB::table('_dealer_ownership_type')->insert([
        							'company_id'=>$company_id,
        							'ownership_type'=>'Organization',

        						]);

    	$data_insert_ownership_select = DB::table('_dealer_ownership_type')->where('company_id',$company_id)->orderBy('id','DESC')->first();

    	$csaMyArr = [
        	'state_id' => trim($location_3->id),
        	'active_status' => 1,
            'town' => $location_4->id,
            'csa_name' => "Trial Super Stockiest",
            'csa_code' => "001",
            'adress' => "E-71 Noida Manacle Technologies Pvt. ltd.",
            'mobile' => "9873320050",
            'email' =>"info@msell.in",
            'gst_no' =>"111111",
            'company_id' => $company_id,
            'contact_person' => "Suraj Pratap Singh",
            'created_date_time' => date('Y-m-d H:i:s')
        ];


        $csaMyArrInsert=SS::create($csaMyArr);

        $DealerMyArr = [
            'name' => 'Trial Distributor',
            'contact_person' => "Suraj Pratap Singh",
            'dealer_code' => '001',
            'address' => "E-71 Noida Manacle Technologies Pvt. ltd.",
            'email' => "info@msell.in",
            'landline' => "0000000",
            'other_numbers' => "9873350200",
            'tin_no' =>"11111",
            'fssai_no' =>'',
            'company_id' => $insert_query->id,
            'pin_no' => "201301",
            'ownership_type_id' => trim($data_insert_ownership_select->id),
            'avg_per_month_pur' => "100000",           
            'state_id' => $location_3->id,
            'town_id' => trim($location_4->id),
            'csa_id' => trim($csaMyArrInsert->id),
            'template_id'=>'0',
            'terms' => '',
            'dealer_status' => 1,
            'dms_status' => 0,
            'created_at'=>date('Y-m-d H:i:s'),
            'edit_stock' => 0
        ];


        $dealer=Dealer::create($DealerMyArr);

        // dealer insetion ends here 

        // dd($company_id);
        $role_id = 0;
        $array_dynamic_user = array('1','2','3','4','5');
        foreach($array_dynamic_user as $key => $value){

        	$user_name = "trial".$key;
			$first_name = "Trail";
			$middle_name = "user";
			$email = $first_name.$key."@gmail.com";
			$number = "999999999".$key;
			$address = "E-71 Noida Manacle Technologies Pvt. ltd.";
			if($key == 0)
			{
				$senior_id = $user->id;
        		$select_query = _role::where('company_id',$company_id)->where('rolename','General Manager')->orderBy('role_id','DESC')->first();
				$role_id = $select_query->role_id;
			}
			elseif ($key == 1) {
				// code...
        		$select_query = _role::where('company_id',$company_id)->where('rolename','General Manager')->orderBy('role_id','DESC')->first();
				$senior_role_id = $select_query->role_id;
				$get_senior_id = Person::where('company_id',$company_id)->where('role_id',$senior_role_id)->orderBy('id','DESC')->first();
				$senior_id = $get_senior_id->id;

        		$select_query = _role::where('company_id',$company_id)->where('rolename','Sales Manager')->orderBy('role_id','DESC')->first();
				$role_id = $select_query->role_id;
			}
			elseif ($key >= 2) {
				// code...
				$select_query = _role::where('company_id',$company_id)->where('rolename','Frontliner')->orderBy('role_id','DESC')->first();
				$role_id = $select_query->role_id;

        		$select_query = _role::where('company_id',$company_id)->where('rolename','Sales Manager')->orderBy('role_id','DESC')->first();
				$senior_role_id = $select_query->role_id;
				$get_senior_id = Person::where('company_id',$company_id)->where('role_id',$senior_role_id)->orderBy('id','DESC')->first();
				$senior_id = $get_senior_id->id;
			}
			// dd($role_id);

			$userArr = 
			[
				'email'=>str_replace('@','_',$user_name).'@'.$request->company_name,
				'company_id'=>$company_id,
				'is_admin'=>0,
	            'original_pass'=>$user_name.$key,
				'password'=>bcrypt($user_name.$key),
				'role_id'=>!empty($role_id)?$role_id:'0',
				'status'=>1,
				'created_at'=>date('Y-m-d H:i:s'),
			];
			$user = User::create($userArr);

			$personArr = [
				'id'=>$user->id,
	            'first_name' => trim($first_name),
	            'middle_name' => trim($middle_name),
	            'last_name' => $key,
	            'role_id' => trim(!empty($role_id)?$role_id:'0'),
	            'person_id_senior' => $senior_id,
	            'version_code_name' => '',
	            'resigning_date' => date('Y-m-d'),
	            'head_quar' => 'NA',
	            'mobile' => trim($number),
	            'email' => trim($email),
	            'state_id' => $location_3->id,
	            'town_id'=>$location_6->id,
	            'head_quater_id'=>$location_5->id,
	            'emp_code' => 01,
	            'company_id' => $insert_query->id,
				'joining_date' => date('Y-m-d'),
	            'status' => 1,
			];
			$person=Person::create($personArr);

			$personLogArr=[
	            'person_id'=>$user->id,
	            'address'=>trim($address),
	            'company_id' => $insert_query->id,
	            'gender'=>'M',
	            'created_on'=>date('Y-m-d H:i:s'),
	        ];
	        $person_log=PersonDetail::create($personLogArr);

			$person_login_arr=[
	            'person_id'=>$user->id,
	            'emp_id'=>'01',
	            'company_id' => $insert_query->id,
	            'person_username'=>trim(str_replace('@','_',$user_name).'@'.$request->company_name),
	            'person_password'=>DB::raw("AES_ENCRYPT('".trim($user_name.$key)."', '".Lang::get('common.db_salt')."')"),
	            'person_status'=>1,
	        ];
	        $person_login=PersonLogin::create($person_login_arr);


	        $ddealer_user_mapping = DB::table('dealer_location_rate_list')->insert([
        							'user_id'=> $user->id,
        							'dealer_id'=> $dealer->id,
        							'location_id'=> $location_7->id,
        							'company_id'=>$company_id,

        					]);
        }

		// dynamcic user insertions ends here  

        // product insetion start here 
        // product insetion ends here 

        // master insetion ends here 
	        $work_arr_cus = array('Working','Leave','Meeting');
	        foreach($work_arr_cus as $wk => $wv){
	        	$work_arr[] = [
								'company_id'=>$company_id,
								'name'=>$wv,
								'sequence'=>0,
								'color_status'=>'#C39BD3',
				];
	        }
	        $data_insert_working = DB::table('_working_status')->insert($work_arr); 

	        $retailer_type_category_arr = array('GOLD','PLATINUM','SILVER','WHOLE SELLER','DIAMOND','OTHER');
	        foreach($work_arr_cus as $wk => $wv){
	        	$retailer_cat_arr[] = [
								'company_id'=>$company_id,
								'outlet_category'=>$wv,
								'sequence'=>0,
				];
	        }
	        $retailer_type_category_ins = DB::table('_retailer_outlet_category')->insert($retailer_cat_arr); 

	        $retailer_type_arr_cus = array('Wholesale','Mall Outlet','Mart','Medicose, Medical Store','Chemist, Pharrmacy','General Store','Ayurvedic Store','Kirana');
	        foreach($retailer_type_arr_cus as $wk => $wv){
	        	$retailer_type_arr_cus_arr[] = [
								'company_id'=>$company_id,
								'outlet_type'=>$wv,
								'sequence'=>0,
								// 'color_status'=>'#C39BD3',
				];
	        }
	        
	        $data_insert_retailer_type = DB::table('_retailer_outlet_type')->insert($retailer_type_arr_cus_arr); 

	        $task_of_day_cus_arr_cus = array('RETAILING','WEEKLY OFF','HO','MEETING','LEAVE','HOLIDAY','PROSPECTING','ABSENT','DISTRIBUTOR VISIT');
	        foreach($task_of_day_cus_arr_cus as $wk => $wv){
	        	$task_of_day_cus_arr[] = [
								'company_id'=>$company_id,
								'task'=>$wv,
								'sequence'=>0,
								// 'color_status'=>'#C39BD3',
				];
	        }
	        
	        $task_of_day_cus_arr_ins = DB::table('_task_of_the_day')->insert($task_of_day_cus_arr); 


	        $daily_report_cus_arr_cus = array('Current Distributor visit','Distributor Replacement Monthly','Distributors Stock Taking','Primary Order Taking','Retailing General','Daily Target');
	        foreach($daily_report_cus_arr_cus as $wk => $wv){
	        	$daily_report_cus_arr_cus_arr[] = [
								'company_id'=>$company_id,
								'name'=>$wv,
								'sequence'=>0,
								// 'color_status'=>'#C39BD3',
				];
	        }
	        
	        $daily_report_cus_arr_cus_arr_ins = DB::table('_daily_schedule')->insert($daily_report_cus_arr_cus_arr); 

        //  web asssigning part starts here 
	        $module_array_cus = array('6','7','8','9','17','18','33','35','36');
	        $sub_module_array_cus = array('11','12','13','14','15','16','17','18','19','20','21','25','28','29','30','31','32','33','34','35','37','39','40','41','42','43','44','45','46','47','49','50','54','55','56','61','62','67');
	        $sub_sub_module_array_cus = array('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','26','27','30','31','41','43','45','49','67','53','54','55','56');
    		$module_id = DB::table('modules_bucket')
							->groupBy('id')
							->whereIn('id',$module_array_cus)
							->pluck('id');

	        $delete_web_module = DB::table('web_module')->where('company_id',$company_id)->delete();
			foreach ($module_id as $m_key => $m_value) 
			{
				$module_name = DB::table('modules_bucket')
							->select('name')
							->where('id',$m_value)
							->first();
				$moduleArr = 
				[
					'module_id'=> $m_value,
					'created_at'=> date('Y-m-d H:i:s'),
					'company_id'=>$company_id,
					'title'=>$module_name->name,
					'sequence'=>$request->sequence[$m_key],
					'status' => 1

				]; 
				$fModuleArr[]=$moduleArr;
			}

			$sub_module_id = DB::table('sub_web_module_bucket')
							->groupBy('id')
							->whereIn('id',$sub_module_array_cus)
							->pluck('id');
			$delete_web_sub_module = DB::table('sub_web_module')->where('company_id',$company_id)->delete();
			foreach ($sub_module_id as $s_key => $s_value) 
			{
				$sub_module_name = DB::table('sub_web_module_bucket')
							->select('sub_module_name as name')
							->where('id',$s_value)
							->first();
				$subModuleArr = 
				[
					'sub_module_id'=> $s_value,
					'created_at'=> date('Y-m-d H:i:s'),
					'title'=>$sub_module_name->name,
					'company_id'=>$company_id,
					'status' => 1

				]; 
				$fSubModuleArr[]=$subModuleArr;
			}

			$sub_sub_module_id = DB::table('sub_sub_web_module_bucket')
							->groupBy('id')
							->whereIn('id',$sub_sub_module_array_cus)
							->pluck('id');
				// dd($sub_sub_module_id);
			$delete_web_sub_sub_module = DB::table('sub_sub_web_module')->where('company_id',$company_id)->delete();
			
			foreach (($sub_sub_module_id) as $key => $value) 
			{
				$sub_module_name = DB::table('sub_sub_web_module_bucket')
							->select('sub_module_name as name')
							->where('id',$value)
							->first();
						// dd($sub_module_name);
				$remaining_sub_module_id_new = 
				[
					'sub_sub_module_id'=>  $value,
					'created_at'=> date('Y-m-d H:i:s'),
					'title'=>$sub_module_name->name,
					'company_id'=>$company_id,
					'status' => 1
				];
				$fSubSubModuleArr[] = $remaining_sub_module_id_new;
			}
			// dd();
			$new_sub_module = $fSubModuleArr;
			$insert_web_module = DB::table('web_module')->insert($fModuleArr); 
			$insert_web_sub_module = DB::table('sub_web_module')->insert($new_sub_module); 
			$insert_web_sub_sub_module = DB::table('sub_sub_web_module')->insert($fSubSubModuleArr); 

		// web designing parts ends heremaster_list_module

		// app module starts here
			$module_arr = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,61,62,53);
			$sub_module_arr = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,38,39,43,50,51,54,63,61,83,84,85,86);

			$delete_app_module = DB::table('app_module')->where('company_id',$company_id)->delete();

       		$delete_sub_module = DB::table('_sub_modules')->where('company_id',$company_id)->delete();


			$master_bocuket_app = DB::table('master_list_module')
								->whereIn('id',$module_arr)
								->get();

			foreach($master_bocuket_app as $ap_key => $ap_value){

				if($ap_value->id == '62' || $ap_value->id == '10' || $ap_value->id == '4'|| $ap_value->id == '3'){
					if($ap_value->id == '62'){
						$bottom_module = 1; 
						$center_module = 0; 
						$left_module = 0; 
					}
					else
					{
						$bottom_module = 1; 
						$center_module = 1; 
						$left_module = 0; 
					}
					
				}
				elseif($ap_value->id == '19' || $ap_value->id == '15'|| $ap_value->id == '16' || $ap_value->id == '18' || $ap_value->id == '21' || $ap_value->id == '22'){
					$bottom_module = 0; 
					$center_module = 1; 
					$left_module = 1;	
				}
				else
				{
					$bottom_module = 0; 
					$center_module = 1; 
					$left_module = 0;	
				}
				$insert_app_nmodule_arr = [
                    'company_id' => $company_id,
                    'module_id' => $ap_value->id,
                    'title_name' => $ap_value->title_name,
                    'module_sequence' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'app_view_status' => $bottom_module,
                    'center_app_view_status' =>  $center_module,
                    'left_app_view_status' => $left_module,
                    'status' => 1
                ];
                $insert_app_nmodule_arr_cus[] = $insert_app_nmodule_arr;

			}
            $app_module_insert_query = AppModule::insert($insert_app_nmodule_arr_cus);
			// dd(1);

			$sub_master_bocuket_app = DB::table('master_list_sub_module')
								->whereIn('id',$sub_module_arr)
								->get();

			foreach($sub_master_bocuket_app as $sap_key => $sap_value){

				$_sub_module_insert_arr = [
                    'company_id' => $company_id,
                    'sub_module_id' => $sap_value->id,
                    'sub_module_name' => $sap_value->title_name,
                    'path' => "",
                    'image_name' => "",
                    'module_sequence' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'status' => 1
                ];
                $_sub_module_insert_arr_cus[] = $_sub_module_insert_arr; 
			}
			$sub_module_arr_insert_query = DB::table('_sub_modules')->insert($_sub_module_insert_arr_cus);
		// app module assigning ends here

		//  api url assign starts here 
			$url_list_bucket = DB::table('url_list')
							->get();

			foreach($url_list_bucket as $ur_key => $ur_value){
				$insert_url= [
                    'company_id' => $company_id,
                    'url_list_id' => $ur_value->id,
                    'code' => $ur_value->code,
                    'url_list' => $ur_value->url_list,
                    'v_name' => '2.0.1',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => 0,
                    'status' => 1
                ];
                $insert_url_cus[] = $insert_url;
			}
			// dd($insert_url_cus);
            $app_module = Urlassign::insert($insert_url_cus);
		// api url assign ends hete  

        // module product master starte here 

            $catalog0myArr = [
	            'name' => "Catalog",
	            'created_by' => 0,
	            'created_at' => date('Y-m-d'),
	            'company_id' => $company_id,
	            'status' => 1
	        ];

	        $catlog_1_insert_query  = Catalog1::create($catalog0myArr);

	        $arraysize2 = array('1','2');
	        $arraysize3 = array('1','2','3');
	        foreach($arraysize2 as $key => $value)
	        {
	        	$myArr2 = [
		            'name' => "Catalog ".$value,
		            'catalog_0_id' => trim($catlog_1_insert_query->id),
		            'company_id' => $company_id,
		            'created_by' => 0,
		            'created_at' => date('Y-m-d'),
		            'c1_color_code' => "#fd7e14",
		            'status' => 1
		        ];

		        $catalog2InsertQuery = Catalog2::create($myArr2);
		        foreach ($arraysize3 as $key3 => $value3) {
		        	// code...
	        	 	$myArr3 = [
			            'name' => 'Product '.$value.$value3,
			            'catalog_1_id' => trim($catalog2InsertQuery->id),
			            'status' => 1,
			            'company_id' => $company_id,
			            'created_by' => 0,
			            'color_code' => "#6f42c".$value,
			            'created_at' => date('Y-m-d'),
			        ];

			        $catalog3insertquery = Catalog3::create($myArr3);

		        	foreach ($arraysize3 as $key4 => $value4) {
		        		$myArr4 = [
				            'itemcode' => $value.$value3.$value4,
				            'name' => "SKU ".$value.$value3.$value4,
				            'weight' => "100",
				            'hsn_code' => "0",
				            'catalog_id' => trim($catalog3insertquery->id),
				            'company_id' => $company_id,
				            'product_type_primary' => '0',
				            'quantity_per_case' => "10",
				            'product_type' => '0',
				            'quantiy_per_other_type'=> '0',
				            'gst_percent' => "0",
				            'product_sequence' => "0",
				            'final_product_type' => "0",
				            'created_by' => "0",
				            'status' => 1
				        ];

				        $catalog_product = CatalogProduct::create($myArr4);
				        $myArrRateList = [
				            'product_id' => trim($catalog_product->id),
				            'mrp' => '13'.$value4,
				            'mrp_pcs' => '12'.$value4,
				            'ss_case_rate' => '8'.$value4,
				            'ss_pcs_rate' => '7'.$value4,
				            'dealer_rate' => '9'.$value4,
				            'dealer_pcs_rate' => '8'.$value4,
				            'retailer_rate' => '12'.$value4,
				            'retailer_pcs_rate' => '11'.$value4, 

				            'product_type_id'=>'0',
				            'other_retailer_rate'=>'0',
				            'other_dealer_rate'=>'0',

				            'company_id' => $company_id,

				            'ss_id' => 0,
				            'is_temp' => 0,
				            'state_id' =>trim($location_3->id),
				            'created_at' => date('Y-m-d H:i:s'),
				        ];
				        $data_list = DB::table('product_rate_list')->insert($myArrRateList);
	        		}
		        }

	        }
	        
        //  modules product master ends here 

		if($insert_query && $user && $role && $person && $person_log && $person_login && $data_list && $catalog_product && $sub_module_arr_insert_query && $app_module && $catlog_1_insert_query&& $insert_web_module && $insert_web_sub_module && $insert_web_sub_sub_module && $app_module_insert_query)
		{
			DB::commit();
			$msg = 'submitted';
			Session::flash('message', Lang::get('common.company').' created successfully');
            Session::flash('class', 'success');
		}
		else
		{
			DB::rollback();
			Session::flash('message', Lang::get('common.company').'Please try again later!');
            Session::flash('class', 'danger');
		}
		
			// DB::rollback();

		$users_data = UserDetail::user_details_fetch($company_id);
		$users_data_record_for_admin = User::where('is_admin',1)->where('company_id',$company_id)->orderBy('id','DESC')->first();

		$date = date('d-M-y');
		$mailId = $request->email;
		$company_name = $request->contact_per_name;
		$send=Mail::send('trial-mail-content/mail',array(
			'company_name'=>$request->contact_per_name,
			'url'=>"demo.msell.in",
			'email'=>$users_data_record_for_admin->email,
			'users_data'=>$users_data,
			'pass'=>$users_data_record_for_admin->original_pass,
			'app_url'=>"https://play.google.com/store/apps/details?id=sfa.solution"



		), function ($message) use($mailId,$company_name,$date)
        {
            // $cc_mail = "marketing@msell.in , jatin.singhal@manacleindia.com, bhoopendranath@manacleindia.com, sales@manacleindia.com";
            $cc_mail = "marketing@msell.in";
            $cc_mail1 = "jatin.singhal@manacleindia.com";
            $cc_mail2 = "sales@manacleindia.com";
            $cc_mail3 = "sales@msell.in";
            $cc_mail4 = "bhoopendranath@manacleindia.com";
            $cc_mail5 = "karan@manacleindia.com";
          	$message->to($mailId,$mailId)->cc([$cc_mail,$cc_mail1,$cc_mail2,$cc_mail3,$cc_mail4,$cc_mail5])
	            ->subject('Trial: mSELL For '.$company_name.' @'.$date);
        });


		$send=Mail::send('trial-mail-content/demo-mail',array(
			'name'=> $request->title,
			'email'=> $mailId,
			'phone_no'=> $request->number,
			'website'=> $request->website,
			'status'=>3,



		), function ($message) use($mailId,$company_name,$date)
        {
            // $cc_mail = "marketing@msell.in , jatin.singhal@manacleindia.com, bhoopendranath@manacleindia.com, sales@manacleindia.com";
            $cc_mail = "marketing@msell.in";
            $cc_mail1 = "jatin.singhal@manacleindia.com";
            $cc_mail2 = "sales@manacleindia.com";
            $cc_mail3 = "sales@msell.in";
            $cc_mail4 = "bhoopendranath@manacleindia.com";
            $cc_mail5 = "karan@manacleindia.com";
          	$message->to([$cc_mail2,$cc_mail3])->cc([$cc_mail,$cc_mail1,$cc_mail2,$cc_mail3,$cc_mail4,$cc_mail5])
	            ->subject('Trial: mSELL For '.$company_name.' @'.$date);
        });
		return response()->json([ 'response' =>True,'msg'=>$msg]);
    }

    public function demo_trail_for_mail(Request $request){

    	// dd($request);
    	$msg = "submitted";
    	$name = $request->name;
    	$email = $request->email;
    	$phone_no = $request->phone;
    	$website = $request->website;
    	$message = $request->message;

    	$data = DB::table('demo_trial')->insert([
    			'name'=> $name,
    			'email'=> $email,
    			'phone_no'=> $phone_no,
    			'website'=> $website,
    			'message'=> $message,
    			'server_date_time'=>date('Y-m-d H:i:s'),

    	]);

    	$date = date('d-M-y');
		$mailId = $email;
		if(!empty($message))
		{
			$send=Mail::send('trial-mail-content/demo-mail',array(
				'name'=> $name,
				'email'=> $email,
				'phone_no'=> $phone_no,
				'website'=> $website,
				'message'=> $message,
				'status'=>2,



			), function ($message) use($mailId,$name,$date)
	        {
	            // $cc_mail = "marketing@msell.in , jatin.singhal@manacleindia.com, bhoopendranath@manacleindia.com, sales@manacleindia.com";
	            $cc_mail = "marketing@msell.in";
	            $cc_mail1 = "jatin.singhal@manacleindia.com";
	            $cc_mail2 = "sales@manacleindia.com";
	            $cc_mail3 = "sales@msell.in";
	            $cc_mail4 = "bhoopendranath@manacleindia.com";
	            $cc_mail5 = "karan@manacleindia.com";
	          	$message->to([$cc_mail2,$cc_mail3])->cc([$cc_mail,$cc_mail1,$cc_mail2,$cc_mail3,$cc_mail4,$cc_mail5])
		            ->subject('Demo: mSELL For '.$name.' @'.$date);
	        });
		}
		else
		{
			$send=Mail::send('trial-mail-content/demo-mail',array(
				'url'=>"demo.msell.in",
				'email'=>$email,
				'name'=>$name,
				'status'=>1,



			), function ($message) use($mailId,$name,$date)
	        {
	            // $cc_mail = "marketing@msell.in , jatin.singhal@manacleindia.com, bhoopendranath@manacleindia.com, sales@manacleindia.com";
	            // $cc_mail = "marketing@msell.in";
	            // $cc_mail1 = "jatin.singhal@manacleindia.com";
	            $cc_mail2 = "sales@manacleindia.com";
	            $cc_mail3 = "sales@msell.in";
	            $cc_mail4 = "bhoopendranath@manacleindia.com";
	            $cc_mail5 = "karan@manacleindia.com";
	          	$message->to($mailId,$mailId)->cc([$cc_mail2,$cc_mail3,$cc_mail4,$cc_mail5])
		            ->subject('Demo: mSELL For '.$name.' @'.$date);
	        });

		}
		

        
		return response()->json([ 'response' =>True,'msg'=>$msg]);

    }

}
