<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\UserDetail;
use App\Person;
use App\Location5;
use App\Location4;
use App\Location3;
use App\Location6;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Session;
use DateTime;

class DmsController extends Controller
{
	public function dms_login(Request $request)
	{
	 	$validator=Validator::make($request->all(),[
            'uname' => 'required',
            'pass'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
        }
		$uname = $request->uname;
		$pass = $request->pass;

		$query_login = DB::table('dealer_person_login')
					->join('_role','_role.role_id','=','dealer_person_login.role_id')
					->join('location_3','location_3.id','=','dealer_person_login.state_id')
					->select('rolename','dealer_id','email','state_id','dpId','_role.role_id','person_name','dealer_person_login.company_id','phone','location_3.name as l3_name')
					->where('uname',$uname)
					// ->where('person_password',DB::raw("AES_ENCRYPT('".trim($pass)."', '".Lang::get('common.db_salt')."')"))
					->whereRaw("AES_DECRYPT(pass, 'demo') = '$pass'")
					->where('activestatus',1)
					->get();
		// dd($query_login);
		$dms_login_array = array();
		if(count($query_login)<=0)
		{
			$query_login_second = DB::table('retailer')
					// ->join('_role','_role.role_id','=','dealer_person_login.role_id')
					->select('dealer_id','email','location_id','id','name','company_id','landline','other_numbers','username as user_name','state_id_retailer')
					->where('username',$uname)
					// ->where('person_password',DB::raw("AES_ENCRYPT('".trim($pass)."', '".Lang::get('common.db_salt')."')"))
					->whereRaw("AES_DECRYPT(password, 'demo') = '$pass'")
					->where('retailer_status',1)
					->get();
			$dms_login_array = array();
			if(count($query_login_second)>0)
			{
				$company_id = 0;
				foreach ($query_login_second as $key => $value) 
				{
				    // $dms_primary_id = $value->dpId;
					// $person_fullname = $value->person_name
					// $mobile = $value->phone;
					// $email = $value->email;
					// $state_id = $value->state_id;
					
					$company_id = $value->company_id;
					// $location_id = $value->location_id;
					if($value->location_id == 0)
					{
						$location_details_arra = DB::table('location_view')->where('l3_id',$value->state_id_retailer)->where('l4_company_id',$value->company_id)->first();

					}
					else
					{
						$location_details_arra = DB::table('location_view')->where('l7_id',$value->location_id)->where('l7_company_id',$value->company_id)->first();

					}
					$dms_login_array['retailer_primary_id'] = $value->id;
					$dms_login_array['name'] = $value->name;
					$dms_login_array['person_fullname'] = $value->name;
					$dms_login_array['mobile'] = !empty($value->landline)?$value->landline:$value->other_numbers;
					$dms_login_array['email'] = !empty($value->email)?$value->email:'';
					$dms_login_array['location_id'] = $value->location_id;
					$dms_login_array['state_id'] = !empty($location_details_arra->l3_id)?$location_details_arra->l3_id:'0';
					$dms_login_array['person_role_id'] = 0;
					$dms_login_array['person_role_name'] = 'Retailer';
					$dms_login_array['state_name'] = !empty($location_details_arra->l3_name)?$location_details_arra->l3_name:'NA';
					// $dms_login_array['person_role_id'] = $value->role_id;
					// $dms_login_array['person_role_name'] = $value->rolename;
					$dms_login_array['dealer_id'] = $value->dealer_id;
					$dms_login_array['company_id'] = $value->company_id;
					$dms_login_array['user_type'] = '1';
				}
				$check_role_id_data = DB::table('_role')->where('company_id',$company_id)->where('rolename','Retailer')->first();
				if(empty($check_role_id_data))
				{
            		return response()->json(['response'=>False,'message'=>'Make role first']);

				}
				$check_role_id = $check_role_id_data->role_id;
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
	            return response()->json([ 'response' =>False,'message'=>'!!Credentials not match w1ith our records!!']);        

			}
		}
		if(COUNT($query_login)>0)
		{
			$company_id = 0;
			foreach ($query_login as $key => $value) 
			{
			 // 	$dms_primary_id = $value->dpId;
				// $person_fullname = $value->person_name
				// $mobile = $value->phone;
				// $email = $value->email;
				// $state_id = $value->state_id;
				$check_role_id = $value->role_id;
				$company_id = $value->company_id;

				$dms_login_array['dms_primary_id'] = $value->dpId;
				$dms_login_array['person_fullname'] = $value->person_name;
				$dms_login_array['mobile'] = !empty($value->phone)?$value->phone:'0';
				$dms_login_array['email'] = !empty($value->email)?$value->email:'';
				$dms_login_array['state_id'] = $value->state_id;
				$dms_login_array['person_role_id'] = $value->role_id;
				$dms_login_array['person_role_name'] = $value->rolename;
				$dms_login_array['dealer_id'] = $value->dealer_id;
				$dms_login_array['company_id'] = $value->company_id;
				$dms_login_array['state_name'] = $value->l3_name;
				$dms_login_array['user_type'] = '2';

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
                    $module[$key]['module_name'] = !empty($value->m