<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Person;
use App\Company;
use App\JuniorData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Validator;
use DB;
use Image;

class TestLoginController extends Controller
{
    public $successStatus = 200;
    public $response_true = True;
    public $response_false = False;

    # return the company id and url's on the behalf of user name 
    # this is first step for login  starts here  
    # below function is for all company please check before modificatons!!!!!!!!....!!!

    # above function is gateway for entery in our software ends here   

	# now second step starts here for login 
    # below function is gateway for entery in our software starts here 
	// public function test_login_demo(Request $request)
	public function test_login_demo(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'uname' => 'required',
            'imei' => 'required',   
            'v_name' => 'required',
            'v_code' => 'required',
            'pass' => 'required',
            'company_id' => 'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
        }
		$uname = $request->uname;
		$pass = $request->pass;
		$imei = $request->imei;
		$v_code = $request->v_code;
		$v_name = $request->v_name;
		$company_id = $request->company_id;
		if(!empty($company_id))
		{	
			// $imei_insert = ['imei_number'=>$imei];
			// $data_insert = Person::join('person_login','person_login.person_id','=','person.id')
			// 			->where('person_username',$uname)
			// 			->where('person_password',DB::raw("AES_ENCRYPT('".trim($pass)."', '".Lang::get('common.db_salt')."')"))
			// 			->update($imei_insert);
			

			// $imei_query = Person::join('person_login','person_login.person_id','=','person.id')->where('person_username',$uname)
			// ->where('person_password',DB::raw("AES_ENCRYPT('".trim($pass)."', '".Lang::get('common.db_salt')."')"))->first();
			
			$person_query = Person::where('person_username',$uname)
						->join('person_login','person_login.person_id','=','person.id')
						->join('location_3','location_3.id','=','person.state_id')
						->join('person_details','person_details.person_id','=','person.id')
						->join('_role','_role.role_id','=','person.role_id')
						->join('company','company.id','=','person.company_id')
						->select('person.state_id as state_id','is_mtp_enabled','person_username','person.id as user_id','mobile','imei_number','person.email as user_email','rolename as designation','person.role_id as designation_id','emp_code','person_details.address as user_address','location_3.name as state','head_quar as head_quater','person_details.created_on as user_created_date')
						->where('person_password',DB::raw("AES_ENCRYPT('".trim($pass)."', '".Lang::get('common.db_salt')."')"))
						->where('imei_number',$imei)
						->where('person_status',1)
						->where('person_id_senior','!=',0)
						->where('company.id',$company_id)
						->get();
			// dd($person_query);
			if(COUNT($person_query)>0)
			{
				$url_details = DB::table('interface_url')
							->select('image_url as sync_image_url','company_id','signin_url','sync_post_url','test_url','version_code','status')
							->where('version_code',$v_name)
							->where('status',1)
							->where('company_id',$company_id)
							->get();

				$url_list = DB::table('url_list')
							->select('url_list.code as code','url_list.url_list as url_list')
							->join('assign_url_list','assign_url_list.url_list_id','=','url_list.id')
							->join('version_management','version_management.id','=','assign_url_list.v_name')
							->where('version_management.version_name',$v_name)
							->where('assign_url_list.status',1)
							->where('assign_url_list.company_id',$company_id)
							->where('version_management.company_id',$company_id)
							->get();

				foreach ($person_query as $key => $value) 
				{
					
					$zone_data = DB::table('location_view')->where('l3_id',$value->state_id)->first();
					$user_personal_data['is_mtp_enabled'] = $value->is_mtp_enabled;
					$user_personal_data['user_id'] = $value->user_id;
					$user_personal_data['person_username'] = $value->person_username;
					$user_personal_data['mobile'] = $value->mobile;
					$user_personal_data['imei_number'] = $value->imei_number;
					$user_personal_data['user_email'] = $value->user_email;
					$user_personal_data['designation_id'] = $value->designation_id;
					$user_personal_data['designation'] = $value->designation;
					$user_personal_data['emp_code'] = $value->emp_code;
					$user_personal_data['user_address'] = $value->user_address;
					$user_personal_data['state'] = $value->state;
					$user_personal_data['zone'] = $zone_data->l1_name;
					$user_personal_data['head_quater'] = $value->head_quater;
					$user_personal_data['user_created_date'] = $value->user_created_date;
					
					$check_junior_data=JuniorData::getJuniorUser($value->user_id,$company_id);
	                $junior_data_check = Session::get('juniordata');
	                if(empty($junior_data_check))
	                {
						$user_personal_data['is_junior'] = False;
	                }
	                else
	                {
						$user_personal_data['is_junior'] = True;
	                }

				}
				// dd($person_query);
    			$user_id = $person_query[0]->user_id; // return user id 
    			$check_role_id = $person_query[0]->designation_id; // return user id 
    			$state_id = $person_query[0]->state_id; // return user id 
				$myArr=['version_code_name'=>"Version: $v_name/$v_code"];
    			$update_query = DB::table('person')->where('id',$user_id)->update($myArr);
    			
				##................................... return the dealer details on the behalf of user id ................................##
			       $user_dealer_retailer_query = DB::table('dealer')
			       							->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
			       							->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
			       							->select('dealer.name as name','dealer.id as dealer_id','l6_name as lname','l6_id as lid')
			       							->where('dealer_location_rate_list.user_id',$user_id)
			       							->where('dealer.dealer_status',1)
			       							->where('dealer.company_id',$company_id)
			       							->where('dealer_location_rate_list.company_id',$company_id)
			       							->groupBy('dealer.id')
			       							->get();
					$dealer_id = array();
			        foreach ($user_dealer_retailer_query as $key => $value)
			        {
			            $dealer_id[]=$value->dealer_id;
			            $dealer_data_string['dealer_id'] = "$value->dealer_id"; // return the data in string 
			            $dealer_data_string['lid'] = "$value->lid"; // return the data in string 
			            $dealer_data_string['lname'] = $value->lname;
			            $dealer_data_string['name'] = $value->name;
			            $final_dealer_data[] = $dealer_data_string; // merge all data in one array 
			        }
			    ##............................... return the beat details  on the behalf of dealer_id ................................##
			        $beat_data = DB::table('dealer_location_rate_list')
			        			->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
			        			->select('dealer_location_rate_list.location_id as beat_id','location_7.name as name','dealer_location_rate_list.dealer_id as dealer_id')
			        			->whereIn('dealer_location_rate_list.dealer_id',$dealer_id)
       							->where('dealer_location_rate_list.company_id',$company_id)
       							->where('location_7.company_id',$company_id)
			        			->groupBy('dealer_location_rate_list.location_id','dealer_id')
			        			->get();
			        $beat_id = array();
			        foreach($beat_data as $key => $value) 
			        {
			            $beat_id[] = $value->beat_id;
			            $beat_data_string['beat_id'] = "$value->beat_id"; // return the data in string 
			            $beat_data_string['dealer_id'] = "$value->dealer_id"; // return the data in string 
			            $beat_data_string['name'] = "$value->name"; // return the data in string 
			            $final_data_beat[] = $beat_data_string; // merge all data in one array 
			        }
		        ##................................ return the retailer details on the behalf of beat id  ......................##   
			       $retailer_id_data = DB::table('retailer')->select('sequence_id as seq_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'_role.rolename as designation','retailer.created_on as created_on','created_by_person_id','retailer.tin_no as tin_no','location_7.name as beat_name','retailer.id as id','lat_long','retailer.name as retailer_name','location_id','address','retailer.email as email','contact_per_name','landline')
			            ->join('location_7','location_7.id','=','retailer.location_id')
			            ->join('person','person.id','=','retailer.created_by_person_id')
			            ->join('_role','_role.role_id','=','person.role_id')
			            ->whereIn('retailer.location_id',$beat_id)
			            ->where('retailer.company_id',$company_id)
			            ->where('location_7.company_id',$company_id)
			            ->where('_role.company_id',$company_id)
			            ->where('retailer_status',1)
			            ->groupBy('retailer.id')
			            ->get();

		            $retailer_sequence = DB::table('user_retailer_sequence')
		            					->pluck('sequence_id','retailer_id');

		            $retailer_user_sequence = DB::table('user_retailer_sequence')
		            						->pluck('sequence_id',DB::raw("CONCAT(user_id,retailer_id)"));

			        $last_order_book = DB::table("user_sales_order")
			                        ->select(DB::raw("CONCAT_WS(' ',date,time) as date_time"),'retailer_id')
			                        ->where('company_id',$company_id)
			                        ->groupBy('retailer_id')
			                        ->orderBy('date_time','DESC')
			                        ->pluck('date_time','retailer_id');
			        // dd($last_order_book);
			        foreach($retailer_id_data as $key => $value) 
			        {
			            $retailer_id = $value->id;
			            $payment_collection_query = DB::table('payment_collection')->select(DB::raw('sum(total_amount) as paid'))->where('retailer_id',$retailer_id)->first();
			            $challan_data_query = DB::table('challan_order')->select(DB::raw('sum(amount) as ch_amt'))->where('ch_retailer_id',$retailer_id)->first();
			            $retailer_amt  = DB::table('payment_collection')->select('total_amount')->where('retailer_id',$retailer_id)->orderBy('pay_date_time','DESC')->first();
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
						if(!empty($retailer_user_sequence[$user_id.$retailer_id]))
						{
							$sequence_id = $retailer_user_sequence[$user_id.$retailer_id];
						}
						elseif(!empty($retailer_sequence[$retailer_id]))
						{
							$sequence_id = $retailer_sequence[$retailer_id];
						}
						else
						{
							$sequence_id = 0;
						}
			            
			            $retailer_data['lat'] = $lat;
			            $retailer_data['lng'] = $lng;
			            $retailer_data['location_id'] = "$value->location_id";
			            $retailer_data['address'] = $value->address;
			            $retailer_data['email'] = !empty($value->email)?$value->email:'';
			            $retailer_data['tin_no'] = $value->tin_no;
			            $retailer_data['contact_per_name'] = !empty($value->contact_per_name)?$value->contact_per_name:'';
			            $retailer_data['landline'] = $value->landline;
			            $retailer_data['seq_id'] = "$sequence_id";
			            $retailer_data['created_by'] = $value->user_name;
			            $retailer_data['created_by_designation'] = $value->designation;
			            $retailer_data['created_at'] = $value->created_on;
			            $retailer_data['last_visit_date'] = !empty($last_order_book[$retailer_id])?$last_order_book[$retailer_id]:"No Oder book Yet";
			            $retailer_data['beat_name'] = $value->beat_name;
			            $outstanding = !empty($payment_collection_query)?($payment_collection_query->paid)-($challan_data_query->ch_amt):0;
			            $retailer_data['outstanding'] = "$outstanding";
			            $last_amt = !empty($retailer_amt)?$retailer_amt:0;
			            $retailer_data['last_amt'] = "$last_amt";
			            $retailer_data['achieved'] = !empty($challan_data_query->ch_amt)?$challan_data_query->ch_amt:'';
			            $retailer_data['last_date'] = "no date";
			            $final_retailer[] = $retailer_data;
			        }
		        #.............................return dealer , beat and retailer array with all details ................................##

		        #........................................... location master starts here.................................................##
					// $location_master = DB::table('location_view')
					// 				->where('l1_company_id',$company_id)
					// 				->where('l2_company_id',$company_id)
					// 				->where('l3_company_id',$company_id)
					// 				->where('l4_company_id',$company_id)
					// 				->where('l5_company_id',$company_id)
					// 				->where('l6_company_id',$company_id)
					// 				->where('l7_company_id',$company_id)
					// 				->get();
					// foreach ($location_master as $key => $value) 
					// {
					// 	$_location_master_array['l1_id'] = "$value->l1_id";
					// 	$_location_master_array['l1_name'] = $value->l1_name;
					// 	$_location_master_array['l2_id'] = "$value->l2_id";
					// 	$_location_master_array['l2_name'] = $value->l2_name;
					// 	$_location_master_array['l3_id'] = "$value->l3_id";
					// 	$_location_master_array['l3_name'] = $value->l3_name;
					// 	$_location_master_array['l4_id'] = "$value->l4_id";
					// 	$_location_master_array['l4_name'] = $value->l4_name;
					// 	$_location_master_array['l5_id'] = "$value->l5_id";
					// 	$_location_master_array['l5_name'] = $value->l5_name;
					// 	$_location_master_array['l6_id'] = "$value->l6_id";
					// 	$_location_master_array['l6_name'] = $value->l6_name;
					// 	$_location_master_array['l7_id'] = "$value->l7_id";
					// 	$_location_master_array['l7_name'] = $value->l7_name;
					// 	$location_master_details[] = $_location_master_array;

					// }

		        #............................................ location master ends here ................................................##

		        #..........................................return colleague data starts here ........................................##

			        // for juniors **************************
			        Session::forget('juniordata');
	                $user_data=JuniorData::getJuniorUser($user_id,$company_id);
	                // dd($user_data);
	                $junior_data = Session::get('juniordata');
                	// dd($junior_data);
			        Session::forget('seniorData');
           			$fetch_senior_id = JuniorData::getSeniorUser($user_id,$company_id);
           			$senior_data = Session::get('seniorData');
           			// dd($senior_data);
	                $out = array();
	                $custom = 1;
	                
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
					// dd($collegueArr);
					// working_with Ends here!!!!!!!!!!!

					// ***** for working status drop down starts here **** 
					$working_status = DB::table('_working_status')
									->select('name','id','company_id')
									->where('company_id',$company_id)
									->orderBy('sequence','ASC')
									->where('status',1)
									->get();

					$check_role_wise_assing_module = DB::table('role_app_module')
													->join('master_list_module','master_list_module.id','=','role_app_module.module_id')
													->select('master_list_module.icon_image as module_icon_image','master_list_module.id as module_id','role_app_module.title_name as module_name','master_list_module.url as module_url')
													->where('role_app_module.company_id',$company_id)
													->where('role_app_module.status',1)
													->where('role_app_module.status',1)
													->where('role_app_module.role_id',$check_role_id)
													->orderBy('role_app_module.module_sequence','ASC')
													->get();
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
					}

					

					

				#.................................state and town array on the behalf of distributor assign on user starts here .........##

					$state_array = array();
					$town_array = array();
					$state_array = DB::table('location_3')
								->join('dealer','dealer.state_id','=','location_3.id')
								->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
								->select('location_3.name as state_name',DB::raw("convert(location_3.id,CHAR) as l3_id"))
								->where('dealer_status',1)
								->where('location_3.status',1)
								->where('location_3.company_id',$company_id)
								->where('user_id',$user_id)
								->where('dealer.company_id',$company_id)
								->groupBy('location_3.id')
								->get();

					$town_arr = array();
					$town_array = array();
					$town_array_data = DB::table('location_7')
								->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','location_7.id')
								->join('location_6','location_6.id','=','location_7.location_6_id')
								->join('location_5','location_5.id','=','location_6.location_5_id')
								->join('location_4','location_4.id','=','location_5.location_4_id')
								->join('location_3','location_3.id','=','location_4.location_3_id')
								
								->select('location_6.name as town_name','location_6.id as l6_id','location_3.id as l3_id')
								->where('location_6.company_id',$company_id)
							
								
								->where('location_6.status',1)
								->where('user_id',$user_id)
							
								->groupBy('location_6.id','location_3.id')
								->get();
					foreach($town_array_data as $t_key => $t_value)
					{
						$town_arr['l4_id'] = "$t_value->l6_id";
						$town_arr['town_name'] = $t_value->town_name;
						$town_arr['l3_id'] = "$t_value->l3_id";
						$town_array[] = $town_arr;
					}
		        #......................................Product overall data return starts here .........................................##
					$product_array = DB::table('catalog_product')
									->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
									->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
									->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
									->select('catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','unit','catalog_product.name','product_rate_list.retailer_pcs_rate as base_price','product_rate_list.mrp_pcs as mrp', 'product_rate_list.mrp_pcs','hsn_code','catalog_2.name as cname', 'product_rate_list.dealer_rate as dealer_rate','product_rate_list.dealer_pcs_rate as dealer_pcs_rate')
									->where('catalog_1.status',1)
									->where('catalog_2.status',1)
									->where('catalog_product.status',1)
									->where('state_id',$state_id)
									->where('catalog_product.company_id',$company_id)
									->get()->toArray();
					$final_catalog_product_details = array();
					foreach ($product_array as $key => $value) 
					{
						$focus_query = DB::table('focus')
									->select('product_id')
									->where('company_id',$company_id)
									->where('product_id',$value->id)
									->get();
						if(COUNT($focus_query)>0)
						{
							$focus_status = 1;
						}
						else
						{
							$focus_status = 0;
						}
						$focust_query = DB::table('focus_product_users_target')
									->select('target_value as target_qty')
									->where('company_id',$company_id)
									->where('product_id',$value->id)
									->where('user_id',$user_id)
									->whereRaw("DATE_FORMAT(start_date,'%Y-%m-%d')>='date(Y-m-d)' AND DATE_FORMAT(end_date,'%Y-%m-%d')<='date(Y-m-d)'")
									->first();

						$querytax = DB::table('_gst')
									->select('igst as tax')
									->where('company_id',$company_id)
									->where('hsn_code',$value->hsn_code)
									->first();

						$productArray['id'] = "$value->id";
						$productArray['classification_id'] = "$value->classification_id";
						$productArray['classification_name'] = $value->classification_name;
						$productArray['category'] = "$value->catalog_id";
						$productArray['hsn_code'] = $value->hsn_code;
						$productArray['category_name'] = $value->cname;
						$productArray['name'] = $value->name;
						$productArray['weight'] = $value->weight;
						$productArray['base_price'] = $value->base_price;
						$productArray['dealer_rate'] = $value->dealer_rate;
						$productArray['dealer_pcs_rate'] = $value->dealer_pcs_rate;
						$productArray['mrp'] = $value->mrp;
						$productArray['pcs_mrp'] = $value->mrp_pcs;
						$productArray['unit'] = $value->unit;
						$productArray['focus'] = "$focus_status";
						$productArray['focus_target'] = !empty($focust_query->target_qty)?$focust_query->target_qty:'';
						$productArray['tax'] = !empty($querytax->tax)?$querytax->tax:'';
						$final_catalog_product_details[] = $productArray;

					}

				#........................................product classification starts here ............................................##
					$product_classification_query = DB::table('catalog_1')
												->join('catalog_view','catalog_view.c1_id','=','catalog_1.id')
												->select('catalog_1.id as id','catalog_1.name as name')
												->where('catalog_1.company_id',$company_id)
												->where('catalog_1.status',1)
												->groupBy('c1_id')
												->get()->toArray();
					$final_product_classification_details = array();
					foreach ($product_classification_query as $key => $value) 
					{
						$classification_array['id']= "$value->id";
						$classification_array['name']= $value->name;
						$final_product_classification_details[] = $classification_array;
					}
				#..........................................cataegory part starts here ..................................................##
					$category_data = DB::table('catalog_2')
							->join('catalog_view','catalog_view.c2_id','=','catalog_2.id')
							->select('id','name', 'catalog_view.c1_id as classification_id', 'catalog_view.c1_name as classification_name')
							->where('catalog_2.company_id',$company_id)
							->where('catalog_2.status',1)
							->groupBy('c2_id')
							->get()->toArray();
					$final_category_array = array();
					foreach ($category_data as $key => $value) 
					{
						$category_array['id'] = "$value->id";
						$category_array['classification_id'] = "$value->classification_id";
						$category_array['classification_name'] = $value->classification_name;
						$category_array['name'] = $value->name;
						$final_category_array[] = $category_array;
					}
				#......................................... non productive reason starts here ...........................................##
					$non_productive_reason_query  = DB::table('_no_sale_reason')
												->select('id','name')
												->where('company_id',$company_id)
												->where('status',1)
												->get();
					$final_non_productive_query = array();
					foreach ($non_productive_reason_query as $key => $value) 
					{
						$non_productive_array['id'] = "$value->id"; 
						$non_productive_array['name'] = $value->name;
						$final_non_productive_query[] = $non_productive_array;
					}
				#..........................................Daily schedule starts here ..................................................##
					$daily_schedule_query = DB::table('_daily_schedule')
										->select('id','name')
										->where('company_id',$company_id)
										->where('status',1)
										->orderBy('id','ASC')
										->get();
					$daily_schedule_details = array();
					foreach ($daily_schedule_query as $key => $value) 
					{
						$daily_schedule_array['id'] = "$value->id";
						$daily_schedule_array['name'] = $value->name;
						$daily_schedule_details[] = $daily_schedule_array;  
					}
				#.....................................task of the day starts here ......................................................##
					$task_query = DB::table('_task_of_the_day')
								->where('company_id',$company_id)
								->where('status',1)
								->get();
					$task = array();
					foreach ($task_query as $key => $value) 
					{
						$task_array['id'] = "$value->id";
						$task_array['name'] = $value->task;
						$task[] = $task_array;
					}
				#/.........................................mtp starts here .............................................................##
					
						$date = date('Y-m');
						$mtp_query = DB::table('monthly_tour_program')
									->select('rd','total_sales','working_date','locations','task_of_the_day')
									->where('company_id',$company_id)
									->where('person_id',$user_id)
									->whereRaw("DATE_FORMAT(working_date,'%Y-%m')='$date'")
									->get();
						$mtp_array = array();
						foreach ($mtp_query as $key => $value) 
						{
							$beat_data = DB::table('location_7')->where('id',$value->locations)->first();
							$data['total_sale'] = $value->total_sales;
							$data['rd'] = $value->rd;
							$data['date'] = $value->working_date;
							$data['today'] = !empty($beat_data->name)?$beat_data->name:'';
							$data['today_id'] = $value->locations;
							$mtp_array[] = $data;
						}
				#.............................................travelling mode starts here ..............................................##

						$travelling_mode = DB::table('_travelling_mode')
										->select('id','mode')
										->where('company_id',$company_id)
										->where('status',1)
										->get();
						$travel_array = array();
						foreach ($travelling_mode as $key => $value) 
						{
							$data_t['id'] = "$value->id";
							$data_t['mode'] = $value->mode;
							$travel_array[] = $data_t;
						}
				#.............................................mtd target acheivement starts here........................................##
						$current_date = date('Y-m-d');
						$current_month = date('Y-m');
						$mtd_target = '';
						$mtd_achievement = '';
						$mtd_target_query  = DB::table('user_sales_order')
											->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$current_date'")
											->where('company_id',$company_id)
											->where('user_id',$user_id)
											->SUM('amount');

						$mtd_second_query = DB::table('monthly_tour_program')
										->whereRaw("DATE_FORMAT(working_date,'%Y-%m')='$current_month'")
										->where('company_id',$company_id)
										->where('person_id',$user_id)										
										->SUM('rd');
						
						if(!empty($mtd_target_query) && !empty($mtd_second_query))
						{
							$percentage_ratio=($mtd_target_query/$mtd_second_query)*100;
						}
						else
						{
							$mtd_target=!empty($mtd_second_query)?$mtd_second_query:0;
							$mtd_achievement=!empty($mtd_target_query)?$mtd_target_query:0;
						}
				#......................................payment modes starts here ................................................................................................##
				$payment_modes  = DB::table('_payment_modes')
								->where('status',1)
								->get();

				#......................................outlet type  starts here ................................................................................................##
				$retailer_outelet_types  = DB::table('_retailer_outlet_type')
								->where('status',1)
								->where('company_id',$company_id)
								->orderBy('sequence','ASC')
								->get();
				#......................................outlet category modes starts here ................................................................................................##
				$retailer_outelet_category  = DB::table('_retailer_outlet_category')
								->where('status',1)
								->where('company_id',$company_id)
								->orderBy('sequence','ASC')
								->get();
				#......................................schedule type starts here ................................................................................................##
				$daily_schedule  = DB::table('_daily_schedule')
								->where('status',1)
								->where('company_id',$company_id)
								->orderBy('sequence','ASC')
								->get();
				#......................................return type starts here ................................................................................................##
				$return_type  = DB::table('_return_type_damage')
								->where('status',1)
								->where('company_id',$company_id)
								->get();
				#......................................no sale reason starts here ................................................................................................##
				$reason_type  = DB::table('_no_sale_reason')
								->where('status',1)
								->orderBy('sequence','ASC')
								->where('company_id',$company_id)
								->get();
				$meeting_type = array();
				$meeting_type  = DB::table('_meeting_type')
								->where('status',1)
								->orderBy('sequence','ASC')
								->where('company_id',$company_id)
								->get();
				$meeting_with_type  = DB::table('_meeting_with_type')
								->where('status',1)
								->orderBy('sequence','ASC')
								->where('company_id',$company_id)
								->get();
		        #......................................reponse parameters starts here ..................................................##
		            return response()->json([ 
		            	'response' =>True,

		            	'url_list'=>$url_list,
		            	'url_details'=>$url_details,
		            	'company_id'=>$company_id,
		            	'app_module'=> $module,
		            	'sub_module'=> $sub_module_arr,
		            	'user_details'=>!empty($user_personal_data)?$user_personal_data:array(), // user data
		            	'dealer'=>!empty($final_dealer_data)?$final_dealer_data:array(), // dealer data 
		            	'beat'=>!empty($final_data_beat)?$final_data_beat:array(), // beat data (location_5)
		            	'retailer'=>!empty($final_retailer)?$final_retailer:array(), // retailer all above response data dependend on each other 
		            	'colleague' => $collegueArr,
		            	'working_status'=>$working_status,
		            	'state_array'=>$state_array,
						'town_array'=>$town_array,
						'product'=>$final_catalog_product_details,
						'product_classification'=>$product_classification_query,
						'category' => $final_category_array,
						'non_productive_reason'=> $final_non_productive_query,
						'daily_schedule' => $daily_schedule_details,
						'task_of_the_day'=>$task,
						'mtp'=>$mtp_array,
						'travelling_modes'=>$travel_array,
						'mtd_target'=>$mtd_target,
						'mtd_achievement'=>$mtd_achievement,
						'payment_modes'=> $payment_modes,
						'retailer_outelet_types'=>$retailer_outelet_types,
						'retailer_outelet_category'=> $retailer_outelet_category,
						// 'daily_schedule'=> $daily_schedule,
						'return_type' => $return_type,
						'reason_type'=> $reason_type,
						'meeting_type'=> $meeting_type,
						'meeting_with_type'=> $meeting_with_type,
						'other_module_arr'=> $other_module_arr,
						// 'location_master_details'=> $location_master_details,
		            	'message'=>'Success!!']);
		        #......................................reponse parameters ends here ..................................................##

			       
			         
			} // person_query !empty ends here 
			else
			{
				return response()->json([ 'response' =>False,'message'=>'!!User Data Record Not Found!!']);		
			}
		} // company id !empty ends here 
		else
		{
			return response()->json([ 'response' =>False,'message'=>'!!Company Id Not Found!!']);		
		}
			

	}
	# above function is gateway for entery in our software ends here
	public function send_sms_whtsapp(Request $request)
	{

	    $messages = array(
	        // Put parameters here such as force or test
	        'send_channel' => 'whatsapp',
	        'messages' => array(
	            array(
	                'number' => 919709146928,
	                'message'=>'test',
	                'template' => array(
	                    'id' => '12345',
	                    'merge_fields' => array(
	                        'FirstName' => 'Aleisha',
	                        'LastName' => 'Britt',
	                        'Custom1' => 'test',
	                      
	                    )
	                ),
	                
	            )
	        )
	    );
	
     
	    // Prepare data for POST request
	    $data_final = array(
	        'apikey' => '64hne6Ar9t4-k6TmqMJLL6mRI5R04RaFH6Nn5vKi0g',
	        'data' => json_encode($messages),
	    );
     // dd($data);
	    // Send the POST request with cURL
	    $ch = curl_init('https://api.textlocal.in/bulk_json/');
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_final);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $response = curl_exec($ch);
	    curl_close($ch);
	     
	    echo $response;
	}

}