<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Person;
use App\Company;
use App\JuniorData;
use App\TableReturn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Validator;
use DB;
use Image;

class DemoLoginController extends Controller
{
    public $successStatus = 200;
    public $response_true = True;
    public $response_false = False;

    # return the company id and url's on the behalf of user name
    # this is first step for login  starts here  
    # below function is for all company please check before modificatons!!!!!!!!....!!!
    public function check_user_company(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'uname' => 'required',
            'v_name'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
        }
        $uname = $request->uname;
        if (strpos($uname, '@') !== false)
        {
            $replace_company = explode('@',$uname);
            $company_name = $replace_company[1];
            if(!empty($company_name))
            {
                $company_id_query = Company::join('interface_url','interface_url.company_id','=','company.id')
                                    ->select('sync_post_url','company.id as company_id','company.base_url as base_url','interface_url.signin_url as login_url','interface_url.test_url as test_url','title','company.image_url as image_url','interface_url.image_url as sync_image_url','company.company_image','company.address as address','company.email as email','company.website','footer_message','footer_link','company.contact_per_name','company.other_numbers as company_per_mobile')
                                    ->where('name',$company_name)
                                    ->where('version_code',$request->v_name)
                                    ->where('interface_url.status',1)
                                    ->where('company.status',1)
                                    // ->orderBy('interface_url.id','DESC')
                                    ->first();
                                    // dd($company_id_query);
                if(!empty($company_id_query))
                {
                    $company_id = $company_id_query->company_id;
                    $base_url = $company_id_query->base_url;
                    $login_url = $company_id_query->login_url;
                    $test_url = $company_id_query->test_url;
                    $company_name_title = $company_id_query->title;
                    $image_url = $company_id_query->image_url;
                    $sync_url = $company_id_query->sync_post_url;
                    $sync_image_url = $company_id_query->sync_image_url;
                    $company_image = $company_id_query->company_image;

                    $contact_per_name = $company_id_query->contact_per_name;
                    $company_per_mobile = $company_id_query->company_per_mobile;
                    $email = $company_id_query->email;
                    $website = $company_id_query->website;
                    $address = $company_id_query->address;
    
                    if(!empty($request->imei))
                    {
                        $check = DB::table('person')->join('person_login','person_login.person_id','=','person.id')
                            ->where('person_username',$uname)
                            ->where('person_status',1)
                            ->where('person.company_id',$company_id)
                            ->whereNull('person.imei_number')
                            ->first();
                            // dd($check);
                        if(isset($check))
                        {
                            // dd($check);
                            $update_person_query = DB::table('person')->join('person_login','person_login.person_id','=','person.id')
                                            ->where('person_username',$uname)
                                            ->where('person.company_id',$company_id)
                                            ->update(['imei_number'=>$request->imei]);

                            return response()->json([ 'response' =>True,'message'=>'Success','company_image'=> $company_image,'company_image_url'=> $image_url,'sync_image_url'=>$sync_image_url,'company_name'=>$company_name_title,'company_id'=>"$company_id",'base_url'=>$base_url,'login_url'=>$login_url,'test_url'=>$test_url,'sync_url'=>$sync_url,

                                'company_website'=> $website,
                                'company_address'=>$address,
                                'company_email'=>$email,
                                'contact_per_name'=>$contact_per_name,
                                'contact_per_mobile'=>$company_per_mobile,

                                ]);
                        }
                        else
                        {
                            $check_imei = DB::table('person')->join('person_login','person_login.person_id','=','person.id')
                            ->where('person_username',$uname)
                            ->where('person_status',1)
                            ->where('person.company_id',$company_id)
                            ->where('imei_number',$request->imei)
                            ->first();

                            if(!empty($check_imei))
                            {
                                return response()->json([ 'response' =>True,'message'=>'Success','company_image'=> $company_image,'company_image_url'=> $image_url,'sync_image_url'=>$sync_image_url,'company_name'=>$company_name_title,'company_id'=>"$company_id",'base_url'=>$base_url,'login_url'=>$login_url,'test_url'=>$test_url,'sync_url'=>$sync_url,

                                    'company_website'=> $website,
                                    'company_address'=>$address,
                                    'company_email'=>$email,
                                    'contact_per_name'=>$contact_per_name,
                                    'contact_per_mobile'=>$company_per_mobile,

                                    ]);
                            }
                            else
                            {
                                return response()->json([ 'response' =>False,'message'=>'IMEI already exist !!','company_id'=>'','base_url'=>'','login_url'=>'','test_url'=>'']);
                            }
                        }
                    }
                    else
                    {
                        return response()->json([ 'response' =>False,'message'=>'IMEI Not Found !!','company_id'=>'','base_url'=>'','login_url'=>'','test_url'=>'']);

                    }
                    // return response()->json([ 'response' =>True,'message'=>'Success','company_image'=> $company_image,'company_image_url'=> $image_url,'sync_image_url'=>$sync_image_url,'company_name'=>$company_name_title,'company_id'=>"$company_id",'base_url'=>$base_url,'login_url'=>$login_url,'test_url'=>$test_url,'sync_url'=>$sync_url]);
                }
                else
                {
                    return response()->json([ 'response' =>False,'message'=>'Data Not Found','company_id'=>'','base_url'=>'','login_url'=>'','test_url'=>'']);
                }    
    
            }
            else
            {
                return response()->json([ 'response' =>False,'message'=>'Company Not Found','company_id'=>'','base_url'=>'','login_url'=>'','test_url'=>'']);
            }
        }
        else
        {
            return response()->json([ 'response' =>False,'message'=>'Incorrect Username!!','company_id'=>'','base_url'=>'','login_url'=>'','test_url'=>'']);

        }
        
            


    }
    # first step ends here!!!!!!!!!!!!!!!!!!!!!!!!!!!

    # now second step starts here for login
    # below function is gateway for entery in our software starts here
    public function login_demo(Request $request)
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
        $token = $request->token;
        $current_date = date('Y-m-d');
        $table_name = TableReturn::table_return($current_date,$current_date);

        // dd($_POST);
        if(!empty($company_id))
        {    

            // $data = DB::table('insert_json')->insert(['data'=>$_POST]);
            // $imei_insert = ['imei_number'=>$imei];
            // $data_insert = Person::join('person_login','person_login.person_id','=','person.id')
            //             ->where('person_username',$uname)
            //             ->where('person_password',DB::raw("AES_ENCRYPT('".trim($pass)."', '".Lang::get('common.db_salt')."')"))
            //             ->update($imei_insert);
            

            // $imei_query = Person::join('person_login','person_login.person_id','=','person.id')->where('person_username',$uname)
            // ->where('person_password',DB::raw("AES_ENCRYPT('".trim($pass)."', '".Lang::get('common.db_salt')."')"))->first();
            
            $person_query = Person::where('person_username',$uname)
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('location_3','location_3.id','=','person.state_id')
                        ->join('person_details','person_details.person_id','=','person.id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->join('company','company.id','=','person.company_id')
                        ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as full_name"),'person_login.person_image as person_image','rate_list_flag','person.state_id as state_id','is_mtp_enabled','person_username','person.id as user_id','mobile','imei_number','person.email as user_email','rolename as designation','person.role_id as designation_id','emp_code','person_details.address as user_address','location_3.name as state','head_quar as head_quater','person_details.created_on as user_created_date')
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


                    $updateFcmToken = Person::where('id',$value->user_id)->update(['fcm_token'=>$token]);
                    
                    
                    $zone_data = DB::table('location_view')->where('l3_id',$value->state_id)->where('l3_company_id',$company_id)->first();
                    $head_quater_id = DB::table('location_4')->join('person','person.head_quater_id','=','location_4.id')->select('location_4.name as name')->where('location_4.company_id',$company_id)->first();
                    $user_personal_data['is_mtp_enabled'] = $value->is_mtp_enabled;
                    $user_personal_data['user_id'] = $value->user_id;
                    $user_personal_data['person_username'] = $value->person_username;
                    $user_personal_data['full_name'] = $value->full_name;
                    $image_name = !empty($value->person_image)?str_replace('users-profile', '', $value->person_image):'';
                    $user_personal_data['person_image'] = !empty($value->person_image)?'users-profile/'.$image_name:'';
                    $user_personal_data['mobile'] = $value->mobile;
                    $user_personal_data['imei_number'] = $value->imei_number;
                    $user_personal_data['user_email'] = $value->user_email;
                    $user_personal_data['designation_id'] = $value->designation_id;
                    $user_personal_data['designation'] = $value->designation;
                    $user_personal_data['emp_code'] = $value->emp_code;
                    $user_personal_data['user_address'] = $value->user_address;
                    $user_personal_data['state'] = $value->state;
                    $user_personal_data['zone'] = !empty($zone_data->l2_name)?$zone_data->l2_name:'';
                    $user_personal_data['head_quater'] = !empty($head_quater_id->name)?$head_quater_id->name:'';
                    $user_personal_data['user_created_date'] = $value->user_created_date;

                    if($company_id == 49){  // condition added by rupak sir only for oyster bath
                        $user_personal_data['is_junior'] = True;
                    }
                    else{
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

                }
                // dd($person_query);
                $user_id = $person_query[0]->user_id; // return user id
                $check_role_id = $person_query[0]->designation_id; // return user id
                $state_id = $person_query[0]->state_id; // return user id
                $rate_list_flag = $person_query[0]->rate_list_flag; // return user id
                $myArr=['version_code_name'=>"Version: $v_name/$v_code"];
                $update_query = DB::table('person')->where('id',$user_id)->update($myArr);
                
                ##................................... return the dealer details on the behalf of user id ................................##
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
                    $csa_id = array();
                    foreach ($user_dealer_retailer_query as $key => $value)
                    {
                        $dealer_id[]=$value->dealer_id;
                        $csa_id[]=$value->csa_id;
                        $dealer_data_string['dealer_id'] = "$value->dealer_id"; // return the data in string
                        $dealer_data_string['lid'] = "$value->lid"; // return the data in string
                        $dealer_data_string['lname'] = $value->lname;
                        $dealer_data_string['name'] = $value->name;
                        $final_dealer_data[] = $dealer_data_string; // merge all data in one array
                    }

                ##............................... return the CSA details  on the behalf of dealer_id ................................##
                     $csa_data = DB::table('csa')
                                ->join('location_3','location_3.id','=','csa.state_id')
                                ->select('csa.*','location_3.name as state_name')
                                ->whereIn('csa.c_id',$csa_id)
                                ->where('csa.company_id',$company_id)
                                ->where('location_3.company_id',$company_id)
                                ->groupBy('csa.c_id')
                                ->get();

                ##............................... return the CSA details  on the behalf of dealer_id ................................##



                ##............................... return the beat details  on the behalf of dealer_id ................................##
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
                        $beat_data_string['beat_id'] = "$value->beat_id"; // return the data in string
                        $beat_data_string['dealer_id'] = "$value->dealer_id"; // return the data in string
                        $beat_data_string['name'] = "$value->name"; // return the data in string
                        $final_data_beat[] = $beat_data_string; // merge all data in one array
                    }
                ##................................ return the retailer details on the behalf of beat id  ......................##   
                    $retailer_id_data = DB::table('retailer')->select('retailer.other_numbers','verfiy_retailer_status','sequence_id as seq_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'_role.rolename as designation','retailer.created_on as created_on','created_by_person_id','retailer.tin_no as tin_no','location_7.name as beat_name','retailer.id as id','lat_long','retailer.name as retailer_name','location_id','address','retailer.email as email','contact_per_name','landline','retailer.image_name')
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
                        // dd($retailer_id_data);

                    // $retailer_sequence = DB::table('user_retailer_sequence')
                    //                     ->join('retailer','retailer.id','=','user_retailer_sequence.retailer_id')
                    //                     ->where('retailer.company_id',$company_id)
                    // 					->where('user_retailer_sequence.company_id',$company_id)
                    //                     ->whereIn('retailer.location_id',$beat_id)
                    //                     ->pluck('user_retailer_sequence.sequence_id','retailer_id');

                    // $retailer_user_sequence = DB::table('user_retailer_sequence')
                    //                     ->join('retailer','retailer.id','=','user_retailer_sequence.retailer_id')
                    //                     ->where('retailer.company_id',$company_id)
                    // 					->where('user_retailer_sequence.company_id',$company_id)
                    //                     ->whereIn('retailer.location_id',$beat_id)
                    //                     ->pluck('user_retailer_sequence.sequence_id',DB::raw("CONCAT(user_id,retailer_id)"));


                    $last_order_book = DB::table($table_name)
                                    // ->select(DB::raw("CONCAT_WS(' ',date,time) as date_time"),'retailer_id')
                                    ->where('company_id',$company_id)
                                    ->whereIn('location_id',$beat_id)
                                    ->groupBy('retailer_id')
                                    // ->orderBy('date_time','DESC')
                                    // ->pluck('date_time','retailer_id');
                                    ->pluck(DB::raw("MAX(date) as date"),'retailer_id');

                    $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();


                    $saleOrderData = array();
                    foreach ($last_order_book as $lokey => $lovalue) {
                    if(empty($check)){
                         $saleOrderData[$lokey] = DB::table($table_name)
                                        ->select(DB::raw("ROUND(SUM((case_rate*user_sales_order_details.case_qty)+(rate*quantity)),2) as sale"),DB::raw("COUNT(DISTINCT product_id) as sku"))
                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                                        ->where('user_sales_order_details.company_id',$company_id)
                                        ->where('date',$lovalue)
                                        ->where('retailer_id',$lokey)
                                        ->first();
                    }else{
                          $saleOrderData[$lokey] = DB::table($table_name)
                                        ->select(DB::raw("ROUND(SUM(final_secondary_rate*final_secondary_qty),2) as sale"),DB::raw("COUNT(DISTINCT product_id) as sku"))
                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                                        ->where('user_sales_order_details.company_id',$company_id)
                                        ->where('date',$lovalue)
                                        ->where('retailer_id',$lokey)
                                        ->first();
                    }
                       
                    }

                    // dd($saleOrderData);


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

                        if(!empty($last_order_book[$retailer_id])){

                            $date_cus = $last_order_book[$retailer_id];
                            $date1 = date('Y-m-d');
                            $date2 = $date_cus;

                            $diff = abs(strtotime($date2) - strtotime($date1));
                            $years = floor($diff / (365*60*60*24));
                            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                            $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

                            if($days == '0'){
                            $daysago = 'Visited Today';
                            }elseif($days > '0'){
                            $daysago = $days.' Days Ago';
                            }else{
                            $daysago = 'Not Visited Yet';
                            }
                        }
                        else{
                            $daysago = 'Not Visited Yet';
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
                        $retailer_data['last_visit_date'] = $daysago;
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

                        $retailer_data['last_sale_amt'] = !empty($saleOrderData[$retailer_id]->sale)?$saleOrderData[$retailer_id]->sale:'0';
                        $retailer_data['sku_count'] = !empty($saleOrderData[$retailer_id]->sku)?$saleOrderData[$retailer_id]->sku:'0';


                        $final_retailer[] = $retailer_data;
                    }
                                    // dd($final_retailer);

                #.............................return dealer , beat and retailer array with all details ................................##

                #........................................... location master starts here.................................................##
                    // $location_master = DB::table('location_view')
                    //                 ->where('l1_company_id',$company_id)
                    //                 ->where('l2_company_id',$company_id)
                    //                 ->where('l3_company_id',$company_id)
                    //                 ->where('l4_company_id',$company_id)
                    //                 ->where('l5_company_id',$company_id)
                    //                 ->where('l6_company_id',$company_id)
                    //                 ->where('l7_company_id',$company_id)
                    //                 ->get();
                    // foreach ($location_master as $key => $value)
                    // {
                    //     $_location_master_array['l1_id'] = "$value->l1_id";
                    //     $_location_master_array['l1_name'] = $value->l1_name;
                    //     $_location_master_array['l2_id'] = "$value->l2_id";
                    //     $_location_master_array['l2_name'] = $value->l2_name;
                    //     $_location_master_array['l3_id'] = "$value->l3_id";
                    //     $_location_master_array['l3_name'] = $value->l3_name;
                    //     $_location_master_array['l4_id'] = "$value->l4_id";
                    //     $_location_master_array['l4_name'] = $value->l4_name;
                    //     $_location_master_array['l5_id'] = "$value->l5_id";
                    //     $_location_master_array['l5_name'] = $value->l5_name;
                    //     $_location_master_array['l6_id'] = "$value->l6_id";
                    //     $_location_master_array['l6_name'] = $value->l6_name;
                    //     $_location_master_array['l7_id'] = "$value->l7_id";
                    //     $_location_master_array['l7_name'] = $value->l7_name;
                    //     $location_master_details[] = $_location_master_array;

                    // }

                #............................................ location master ends here ................................................##

                #..........................................return colleague data starts here ........................................##
                    // dd('1234');
                    // for juniors **************************
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
                                                    ->select('master_list_module.icon_image as module_icon_image','master_list_module.id as module_id','role_app_module.title_name as module_name','master_list_module.url as module_url','role_app_module.app_view_status as bottom','role_app_module.center_app_view_status as center','role_app_module.left_app_view_status as left')
                                                    ->where('role_app_module.company_id',$company_id)
                                                    ->where('role_app_module.status',1)
                                                    ->where('role_app_module.status',1)
                                                    ->where('role_app_module.role_id',$check_role_id)
                                                    ->orderBy('role_app_module.module_sequence','ASC')
                                                    ->get();
                    // dd($check_role_wise_assing_module);
                    # retailer creation with otp condition start here
                    $retailer_with_otp_creation  = DB::table('app_other_module_assign')
                                ->join('master_other_app_module','master_other_app_module.id','=','app_other_module_assign.module_id')
                                ->select('master_other_app_module.image_name as other_module_icon_image','app_other_module_assign.title_name as other_module_name','app_other_module_assign.module_id as other_module_id')
                                ->where('app_other_module_assign.status',1)
                                ->where('app_other_module_assign.company_id',$company_id)
                                ->where('master_other_app_module.id',2)
                                ->where('master_other_app_module.status',1)
                                ->orderBy('app_other_module_assign.module_sequence','ASC')
                                ->get();
                        $retailer_with_otp_creation_array = array();
                        foreach ($retailer_with_otp_creation as $key => $value)
                        {
                            $retailer_with_otp_creation_array[$key]['other_module_icon_image'] = !empty($value->other_module_icon_image)?$value->other_module_icon_image:'';
                            $retailer_with_otp_creation_array[$key]['other_module_id'] = "$value->other_module_id";
                            $retailer_with_otp_creation_array[$key]['other_module_name'] = !empty($value->other_module_name)?$value->other_module_name:'';
                        }
                    # retailer creation with otp condition ends here
                    if(COUNT($check_role_wise_assing_module)>0)
                    {
                        $module = array();
                        foreach ($check_role_wise_assing_module as $key => $value)
                        {
                            $module[$key]['module_icon_image'] = !empty($value->module_icon_image)?$value->module_icon_image:'';
                            $module[$key]['module_id'] = "$value->module_id";
                            $module[$key]['module_name'] = !empty($value->module_name)?$value->module_name:'';
                            $module[$key]['module_url'] = !empty($value->module_url)?$value->module_url:'';
                            // $module[$key]['bottom'] = '0';
                            // $module[$key]['center'] = '0';
                            // $module[$key]['left'] = '0';
                              $module[$key]['bottom'] = $value->bottom;
                            $module[$key]['center'] = $value->center;
                            $module[$key]['left'] = $value->left;
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
                        if($company_id == '44')
                        {
                            $other_module = DB::table('app_other_module_assign')
                                ->join('master_other_app_module','master_other_app_module.id','=','app_other_module_assign.module_id')
                                ->select('master_other_app_module.image_name as other_module_icon_image','app_other_module_assign.title_name as other_module_name','app_other_module_assign.module_id as other_module_id')
                                ->where('app_other_module_assign.status',1)
                                ->where('app_other_module_assign.company_id',$company_id)
                                ->where('master_other_app_module.id','!=',2) 
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
                        
                        // dd($other_module_arr);

                    }
                    else
                    {
                        $custom_id_dms = array(28,34,35,36,37);
                        $app_module = DB::table('app_module')
                                ->join('master_list_module','master_list_module.id','=','app_module.module_id')
                                ->select('master_list_module.icon_image as module_icon_image','master_list_module.id as module_id','app_module.title_name as module_name','master_list_module.url as module_url','app_module.app_view_status as bottom','app_module.center_app_view_status as center','app_module.left_app_view_status as left')
                                ->where('app_module.company_id',$company_id)
                                ->where('app_module.status',1)
                                ->where('master_list_module.status',1)
                                ->whereNotIn('master_list_module.id',$custom_id_dms)
                                ->orderBy('app_module.module_sequence','ASC')
                                ->get();
                        $module = array();
                        foreach ($app_module as $key => $value)
                        {
                            $module[$key]['module_icon_image'] = !empty($value->module_icon_image)?$value->module_icon_image:'';
                            $module[$key]['module_id'] = "$value->module_id";
                            $module[$key]['module_name'] = !empty($value->module_name)?$value->module_name:'';
                            $module[$key]['module_url'] = !empty($value->module_url)?$value->module_url:'';
                            $module[$key]['bottom'] = $value->bottom;
                            $module[$key]['center'] = $value->center;
                            $module[$key]['left'] = $value->left;
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

                    

                    

                #.................................state and town array on the behalf of distributor assign on user starts here .........##

                    $state_array = array();
                    $town_array = array();
                    $state_array = DB::table('location_3')
                                ->join('dealer','dealer.state_id','=','location_3.id')
                                ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                                ->select('location_3.name as state_name',DB::raw("location_3.id as l3_id"))
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
                    if($company_id == 50)
                    {
                        $product_array = array();
                    }
                    else
                    {
                        if($rate_list_flag == 2) // dealer
                        {
                            // $product_array = DB::table('catalog_product')
                            //             ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
                            //             ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                            //             ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                            //             ->select('state_id','ss_id','distributor_id','product_type_primary','other_retailer_rate','other_dealer_rate','quantiy_per_other_type','product_type_id','retailer_rate as retailer_case_rate','catalog_product.product_type as product_type','catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','catalog_product.quantity_per_case as quantity_per_case','unit','catalog_product.name','product_rate_list.retailer_pcs_rate as base_price','product_rate_list.mrp_pcs as mrp', 'product_rate_list.mrp_pcs','hsn_code','catalog_2.name as cname', 'product_rate_list.dealer_rate as dealer_rate','product_rate_list.dealer_pcs_rate as dealer_pcs_rate')
                            //             ->where('catalog_1.status',1)
                            //             ->where('catalog_2.status',1)
                            //             ->where('catalog_product.status',1)
                            //             ->whereIn('distributor_id',$dealer_id)
                            //             ->where('catalog_product.company_id',$company_id)
                            //             ->groupBy('product_rate_list.template_id','product_rate_list.product_id')
                            //             ->get()->toArray();
                            $product_array = DB::table('catalog_product')
                                        ->join('product_rate_list_template','product_rate_list_template.product_id','=','catalog_product.id')
                                        ->join('dealer','dealer.template_id','=','product_rate_list_template.template_type')
                                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                                        ->select('catalog_product.image_name','state_id','csa_id as ss_id','dealer.id as distributor_id','product_type_primary','other_retailer_rate','other_dealer_rate','quantiy_per_other_type','product_type_id','retailer_rate as retailer_case_rate','catalog_product.product_type as product_type','catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','catalog_product.quantity_per_case as quantity_per_case','unit','catalog_product.name','product_rate_list_template.retailer_pcs_rate as base_price','product_rate_list_template.mrp_pcs as mrp', 'product_rate_list_template.mrp_pcs','hsn_code','catalog_2.name as cname', 'product_rate_list_template.dealer_rate as dealer_rate','product_rate_list_template.dealer_pcs_rate as dealer_pcs_rate')
                                        ->where('catalog_1.status',1)
                                        ->where('catalog_2.status',1)
                                        ->where('catalog_product.status',1)
                                        ->whereIn('dealer.id',$dealer_id)
                                        ->where('catalog_product.company_id',$company_id)
                                        ->groupBy('product_rate_list_template.template_type','product_rate_list_template.product_id')
                                        ->orderBy('product_sequence','ASC')
                                        ->get()->toArray();
                        }
                        elseif($rate_list_flag == 3) // for ss
                        {
                            $product_array = DB::table('catalog_product')
                                        ->join('product_rate_list_template','product_rate_list_template.product_id','=','catalog_product.id')
                                        ->join('dealer','dealer.template_id','=','product_rate_list_template.template_type')
                                        ->join('csa','csa.template_id','=','product_rate_list_template.template_type')
                                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                                        ->select('catalog_product.image_name','csa.state_id','csa.id as ss_id','dealer.id as distributor_id','product_type_primary','other_retailer_rate','other_dealer_rate','quantiy_per_other_type','product_type_id','retailer_rate as retailer_case_rate','catalog_product.product_type as product_type','catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','catalog_product.quantity_per_case as quantity_per_case','unit','catalog_product.name','product_rate_list_template.retailer_pcs_rate as base_price','product_rate_list_template.mrp_pcs as mrp', 'product_rate_list_template.mrp_pcs','hsn_code','catalog_2.name as cname', 'product_rate_list_template.dealer_rate as dealer_rate','product_rate_list_template.dealer_pcs_rate as dealer_pcs_rate')
                                        ->where('catalog_1.status',1)
                                        ->where('catalog_2.status',1)
                                        ->where('catalog_product.status',1)
                                        ->whereIn('csa.id',$csa_id)
                                        ->where('catalog_product.company_id',$company_id)
                                        ->groupBy('product_rate_list_template.template_type','product_rate_list_template.product_id')
                                        ->orderBy('product_sequence','ASC')
                                        ->get()->toArray();
                        }
                        else // for state
                        {
                            $product_array = DB::table('catalog_product')
                                        ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
                                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                                        ->select('catalog_product.image_name','state_id','ss_id','distributor_id','product_type_primary','other_retailer_rate','other_dealer_rate','quantiy_per_other_type','product_type_id','retailer_rate as retailer_case_rate','catalog_product.product_type as product_type','catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','catalog_product.quantity_per_case as quantity_per_case','unit','catalog_product.name','product_rate_list.retailer_pcs_rate as base_price','product_rate_list.mrp_pcs as mrp', 'product_rate_list.mrp_pcs','hsn_code','catalog_2.name as cname', 'product_rate_list.dealer_rate as dealer_rate','product_rate_list.dealer_pcs_rate as dealer_pcs_rate')
                                        ->where('catalog_1.status',1)
                                        ->where('catalog_2.status',1)
                                        ->where('catalog_product.status',1)
                                        ->where('product_rate_list.status','=','1')
                                        ->where('state_id',$state_id)
                                        ->where('catalog_product.company_id',$company_id)
                                        ->groupBy('product_rate_list.product_id')
                                        ->orderBy('product_sequence','ASC')
                                        ->get()->toArray();
                        }
                    }
                    

                    
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
                        
                        // $focust_query = DB::table('focus_product_users_target')
                        //             ->select('target_value as target_qty')
                        //             ->where('company_id',$company_id)
                        //             ->where('product_id',$value->id)
                        //             ->where('user_id',$user_id)
                        //             ->whereRaw("DATE_FORMAT(start_date,'%Y-%m-%d')>='date(Y-m-d)' AND DATE_FORMAT(end_date,'%Y-%m-%d')<='date(Y-m-d)'")
                        //             ->first();

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
                            $productArray['product_image'] = !empty($value->image_name)?$value->image_name:'';
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
                    if($company_id == '43'){ // for BTW condition
                        if($check_role_id == '148' || $check_role_id == '145' || $check_role_id == '168'){
                             $daily_schedule_query = DB::table('_daily_schedule')
                                        ->select('id','name')
                                        ->where('company_id',$company_id)
                                        ->where('status',1)
                                        ->orderBy('id','ASC')
                                        ->get();
                        }else{
                            $daily_schedule_query = DB::table('_daily_schedule')
                                        ->select('id','name')
                                        ->where('company_id',$company_id)
                                        ->where('status',1)
                                        ->whereNotIn('id',[236])
                                        ->orderBy('id','ASC')
                                        ->get();
                        }
                    }else{
                    $daily_schedule_query = DB::table('_daily_schedule')
                                        ->select('id','name')
                                        ->where('company_id',$company_id)
                                        ->where('status',1)
                                        ->orderBy('id','ASC')
                                        ->get();
                    }
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
                        $mtd_target_query  = DB::table($table_name)
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
                #...........................................product wise scheme starts here ............................................##
                    $current_date_r = date('Y-m-d');
                    $product_wise_schme = DB::table('product_wise_scheme_plan')
                                        ->join('product_wise_scheme_plan_details','product_wise_scheme_plan_details.scheme_id','=','product_wise_scheme_plan.id')
                                        ->select('product_wise_scheme_plan.*','product_wise_scheme_plan_details.*')
                                        ->where('product_wise_scheme_plan.company_id',$company_id)
                                        ->where('product_wise_scheme_plan_details.state_id',$state_id)
                                        ->where('product_wise_scheme_plan_details.company_id',$company_id)
                                        ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$current_date_r' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$current_date_r'")
                                        // ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d')>='$current_date_r' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')<='$current_date_r'")
                                        ->where('product_wise_scheme_plan.status',1)
                                        ->groupBy('product_wise_scheme_plan_details.product_id')
                                        ->get();
                    // dd($product_wise_schme);
                    $final_product_wise_scheme_array = array();
                    foreach ($product_wise_schme as $ps_key => $ps_value) 
                    {
                        if($ps_value->sale_unit==1)
                        {
                            $sale_unit_name = 'weight';
                        }
                        elseif($ps_value->sale_unit==2)
                        {
                            $sale_unit_name = 'cases';
                        }
                        elseif($ps_value->sale_unit==3)
                        {
                            $sale_unit_name = 'pcs';
                        }
                        elseif($ps_value->sale_unit==4)
                        {
                            $sale_unit_name = 'range';
                        }
                        else
                        {
                            $sale_unit_name = 'other';
                        }

                        if($ps_value->incentive_type==1)
                        {
                            $incentive_type = 'Percentage';
                        }
                        elseif($ps_value->incentive_type==2)
                        {
                            $incentive_type = 'Amount';
                        }
                        elseif($ps_value->incentive_type==3)
                        {
                            $incentive_type = 'Free Quantity';
                        }
                        else
                        {
                            $incentive_type = 'other';
                        }
                        $product_wise_scheme_array['product_scheme_name'] = $ps_value->scheme_name;
                        $product_wise_scheme_array['product_id'] = "$ps_value->product_id";
                        $product_wise_scheme_array['sale_unit_id'] = "$ps_value->sale_unit";
                        $product_wise_scheme_array['sale_unit_name'] = $sale_unit_name;
                        $product_wise_scheme_array['sale_value_range_first'] = $ps_value->sale_value_range_first;
                        $product_wise_scheme_array['sale_value_range_last'] = $ps_value->sale_value_range_last;
                        $product_wise_scheme_array['incentive_type'] = "$ps_value->incentive_type";
                        $product_wise_scheme_array['incentive_type_name'] = $incentive_type;
                        $product_wise_scheme_array['reward'] = $ps_value->value_amount_percentage;
                        $final_product_wise_scheme_array[] = $product_wise_scheme_array;


                    }

                    
    #.............................................class type starts here ..............................................##
    
                        // $flag = array(5,6);
						$class_type_query = DB::table('class_type')
                                    ->join('role_wise_assign','role_wise_assign.class_type_id','=','class_type.id')
                                    ->select('class_type.id','class_type.name','class_type.km_limit','role_wise_assign.da_for_class')
                                    ->where('class_type.company_id',$company_id)
                                    ->where('class_type.status',1)
                                    ->where('role_wise_assign.flag_status',5)
                                    ->where('role_wise_assign.role_id',$check_role_id)
                                    ->groupBy('class_type.id')
                                    ->get();
                        $class_type = array();
                        foreach ($class_type_query as $key => $value) 
                        {
                            $data_class['id'] = "$value->id";
                            $data_class['name'] = $value->name;
                            $data_class['km'] = $value->km_limit;
                            $data_class['DA'] = $value->da_for_class;
                            $class_type[] = $data_class;
                        }
                    ///////////////////////////////////////////////////////////////////////////////////////////////        
                        $class_type_details_query = DB::table('class_type')
                                ->join('role_wise_assign','role_wise_assign.class_type_id','=','class_type.id')
                                ->select('class_type.id','class_type.name','class_type.km_limit','role_wise_assign.da_for_class')
                                ->where('class_type.company_id',$company_id)
                                ->where('class_type.status',1)
                                ->where('role_wise_assign.flag_status',6)
                                ->where('role_wise_assign.role_id',$check_role_id)
                                ->groupBy('class_type.id')
                                ->get();
                        $class_type_details = array();
                        foreach ($class_type_details_query as $keyd => $valued) 
                        {
                            $data_class_details['id'] = "$valued->id";
                            $data_class_details['name'] = $valued->name;
                            $data_class_details['km'] = $valued->km_limit;
                            $data_class_details['DA'] = "";
                            $class_type_details[] = $data_class_details;

                        }

                        $final_class_array = array_merge($class_type,$class_type_details);

                        $data_class_type_details = DB::table('class_type')
                                        ->join('class_type_details','class_type_details.class_id','=','class_type.id')
                                        ->join('role_wise_assign','role_wise_assign.class_type_details_id','=','class_type_details.id')
                                        ->select('class_type_details.id','class_type_details.name','class_type_details.class_id','role_wise_assign.da_for_class as limit')
                                        ->where('class_type.company_id',$company_id)
                                        ->where('class_type.status',1)
                                        ->where('role_wise_assign.flag_status',6)
                                        ->where('role_wise_assign.role_id',$check_role_id)
                                        ->groupBy('class_type_details.id')
                                        ->get();


                        $expense_details = DB::table('role_wise_assign')
                                        ->select('role_wise_assign.TA','role_wise_assign.telephone_expense')
                                        ->where('role_wise_assign.company_id',$company_id)
                                        ->where('role_wise_assign.flag_status',4)
                                        ->where('role_wise_assign.role_id',$check_role_id)
                                        ->groupBy('role_wise_assign.role_id')
                                        ->get();

                #...........................................product wise scheme ends here ..............................................##
                #......................................payment modes starts here ................................................................................................##
                $payment_modes  = DB::table('_payment_modes')
                                ->where('status',1)
                                ->get();

                #......................................outlet type  starts here ................................................................................................##
                $retailer_outelet_types  = DB::table('_retailer_outlet_type')
                                ->select('_retailer_outlet_type.*','_retailer_outlet_type.outlet_type as name')
                                ->where('status',1)
                                ->where('company_id',$company_id)
                                ->orderBy('sequence','ASC')
                                ->get();
                #......................................outlet category modes starts here ................................................................................................##
                $retailer_outelet_category  = DB::table('_retailer_outlet_category')
                                ->select('_retailer_outlet_category.*','_retailer_outlet_category.outlet_category as name')
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
                // $type_of_meeting = array();
                $type_of_meeting  = DB::table('_meeting_with_type')
                                ->where('status',1)
                                ->orderBy('sequence','ASC')
                                ->where('company_id',$company_id)
                                ->get();

                $merchandise_item  = DB::table('_retailer_mkt_gift')
                                    ->where('company_id',$company_id)
                                    ->get();                
                #below array run over all company like gloabally
                $not_contacted_reason = DB::table('_not_contacted_reason')
                                    ->where('status',1)
                                    ->orderBy('sequence','ASC')
                                    ->get();

                $_leave_type = DB::table('_leave_type')
                                    ->where('status',1)
                                    ->where('company_id',$company_id)
                                    ->orderBy('sequence','ASC')
                                    ->get();

                // $rajdhani_expense_town = array(array("id"=>"1","name"=>"Dheeru"),array("id"=>"2","name"=>"Lal"));

                $multipleState = DB::table('user_multiple_state')
                                ->where('user_id',$user_id)
                                ->groupBy('user_multiple_state.state_id')
                                ->pluck('state_id')->toArray();

                $rajdhani_expense_town = DB::table('location_4_townexp')
                                        ->select('location_4_townexp.id','location_4_townexp.name')
                                        ->whereIn('location_3_id',$multipleState)
                                        ->groupBy('location_4_townexp.id')
                                        ->orderBy('location_4_townexp.name','ASC')
                                        ->get();


                // $rajdhani_expense_town = array();


                $custom_id_dms = array(28,34,35,36,37);
                $app_module_test_ar = DB::table('app_module')
                                ->join('master_list_module_test','master_list_module_test.id','=','app_module.module_id')
                                ->select('master_list_module_test.icon_image as module_icon_image','master_list_module_test.id as module_id','app_module.title_name as module_name','master_list_module_test.url as module_url','app_module.app_view_status as app_view_status','app_module.app_view_status as bottom','app_module.center_app_view_status as center','app_module.left_app_view_status as left')
                                ->where('app_module.company_id',$company_id)
                                ->where('app_module.status',1)
                                ->where('master_list_module_test.status',1)
                                ->whereNotIn('master_list_module_test.id',$custom_id_dms)
                                ->orderBy('app_module.module_sequence','ASC')
                                ->get();
                        $app_module_test = array();
                        foreach ($app_module_test_ar as $key => $value)
                        {
                            $app_module_test[$key]['module_icon_image'] = !empty($value->module_icon_image)?$value->module_icon_image:'';
                            $app_module_test[$key]['module_id'] = "$value->module_id";
                            $app_module_test[$key]['module_name'] = !empty($value->module_name)?$value->module_name:'';
                            $app_module_test[$key]['module_url'] = !empty($value->module_url)?$value->module_url:'';
                            $app_module_test[$key]['app_view_status'] = !empty($value->app_view_status)?$value->app_view_status:'1';
                            $app_module_test[$key]['bottom'] = $value->bottom;
                            $app_module_test[$key]['center'] = $value->center;
                            $app_module_test[$key]['left'] = $value->left;
                        }

                $set_rf_arr_out = array();
                $retailer_filter_data = DB::table('_retailer_filter_master')
                                        ->where('company_id',$company_id)
                                        ->where('status',1)
                                        ->orderBy('sequence','ASC')
                                        ->get();
                        foreach ($retailer_filter_data as $rf_key => $rf_value) {
                            // code...
                            // $data_set = 
                            if($rf_value->name == 'Outlet Type')
                            {   
                                $finalOutletType = array();
                                foreach ($retailer_outelet_types as $rokey => $rovalue) {
                                    $finalOutlet['retailer_filer_id'] = $rf_value->id;
                                    $finalOutlet['id'] = $rovalue->id;
                                    $finalOutlet['company_id'] = $rovalue->company_id;
                                    $finalOutlet['outlet_type'] = $rovalue->outlet_type;
                                    $finalOutlet['status'] = $rovalue->status;
                                    $finalOutlet['sequence'] = $rovalue->sequence;
                                    $finalOutlet['created_by'] = $rovalue->created_by;
                                    $finalOutlet['created_at'] = $rovalue->created_at;
                                    $finalOutlet['updated_at'] = $rovalue->updated_at;
                                    $finalOutlet['updated_by'] = $rovalue->updated_by;
                                    $finalOutlet['name'] = $rovalue->name;

                                    $finalOutletType[] = $finalOutlet;
                                }

                                $set_rf_arr['id'] = $rf_value->id;
                                $set_rf_arr['name'] = $rf_value->name;
                                $set_rf_arr['sub_arr'] = $finalOutletType;
                            }
                            elseif($rf_value->name == 'Outlet Category'){

                                 $finalOutletCategory = array();
                                foreach ($retailer_outelet_category as $rockey => $rocvalue) {
                                    $finalOutletCat['retailer_filer_id'] = $rf_value->id;
                                    $finalOutletCat['id'] = $rocvalue->id;
                                    $finalOutletCat['company_id'] = $rocvalue->company_id;
                                    $finalOutletCat['outlet_category'] = $rocvalue->outlet_category;
                                    $finalOutletCat['status'] = $rocvalue->status;
                                    $finalOutletCat['sequence'] = $rocvalue->sequence;
                                    $finalOutletCat['created_by'] = $rocvalue->created_by;
                                    $finalOutletCat['created_at'] = $rocvalue->created_at;
                                    $finalOutletCat['updated_at'] = $rocvalue->updated_at;
                                    $finalOutletCat['updated_by'] = $rocvalue->updated_by;
                                    $finalOutletCat['name'] = $rocvalue->name;

                                    $finalOutletCategory[] = $finalOutletCat;
                                }

                                $set_rf_arr['id'] = $rf_value->id;
                                $set_rf_arr['name'] = $rf_value->name;
                                $set_rf_arr['sub_arr'] = $finalOutletCategory;
                            }

                            elseif($rf_value->name == 'Created By'){

                                $finalJuniors = array();
                                if(!empty($junior_data)){
                                    foreach ($juniors_name as $jkey => $jvalue) {
                                        $finalJr['retailer_filer_id'] = $rf_value->id;
                                        $finalJr['id'] = $jvalue->id;
                                        $finalJr['company_id'] = $company_id;
                                        $finalJr['status'] = '';
                                        $finalJr['sequence'] = '';
                                        $finalJr['created_by'] = '';
                                        $finalJr['created_at'] = '';
                                        $finalJr['updated_at'] = '';
                                        $finalJr['updated_by'] = '';
                                        $finalJr['name'] = $jvalue->user_name;

                                        $finalJuniors[] = $finalJr;
                                    }
                                }


                                $set_rf_arr['id'] = $rf_value->id;
                                $set_rf_arr['name'] = $rf_value->name;
                                $set_rf_arr['sub_arr'] = $finalJuniors;
                            }
                            else
                            {
                                $retailer_filter_data_arra = DB::table('_retailer_filter_master_details')
                                        ->where('company_id',$company_id)
                                        ->where('retailer_filer_id','=',$rf_value->id)
                                        ->where('status',1)
                                        ->orderBy('sequence','ASC')
                                        ->get();

                                $set_rf_arr['id'] = $rf_value->id;
                                $set_rf_arr['name'] = $rf_value->name;
                                $set_rf_arr['sub_arr'] = !empty($retailer_filter_data_arra)?$retailer_filter_data_arra:array();
                            }

                            
                            $set_rf_arr_out[] = $set_rf_arr;
                        }


                #......................................reponse parameters starts here ..................................................##
                    return response()->json([
                        'response' =>True,

                        'url_list'=>$url_list,
                        'url_details'=>$url_details,
                        'company_id'=>$company_id,
                        'app_module'=> $module,
                        'app_module_test'=> $app_module_test,
                        'sub_module'=> $sub_module_arr,
                        'other_module_arr'=> $other_module_arr,
                        'retailer_with_otp_creation_array'=> $retailer_with_otp_creation_array,
                        'user_details'=>!empty($user_personal_data)?$user_personal_data:array(), // user data
                        'dealer'=>!empty($final_dealer_data)?$final_dealer_data:array(), // dealer data
                        'beat'=>!empty($final_data_beat)?$final_data_beat:array(), // beat data (location_5)
                        'final_csa_data'=>!empty($csa_data)?$csa_data:array(), // beat data (location_5)
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
                        'type_of_meeting'=> $type_of_meeting,
                        'merchandise_item'=> $merchandise_item,
                        'not_contacted_reason'=> $not_contacted_reason,
                        'final_product_wise_scheme_array'=> $final_product_wise_scheme_array,
                        'class_type'=> $final_class_array,
                        'class_type_details'=> $data_class_type_details,
                        'expense_details'=> $expense_details,
                        'leave_type'=> $_leave_type,
                        'rajdhani_expense_town'=> $rajdhani_expense_town,
                        'retailer_filter_arr'=> $set_rf_arr_out,
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

    # now second step starts here for login
    # below function is gateway for entery in our software starts here
    // public function test_login_demo(Request $request)
    // public function test_login_demo(Request $request)
    // {
    //     $validator=Validator::make($request->all(),[
    //         'uname' => 'required',
    //         'imei' => 'required',   
    //         'v_name' => 'required',
    //         'v_code' => 'required',
    //         'pass' => 'required',
    //         'company_id' => 'required',
          
    //     ]);
    //     if($validator->fails())
    //     {
    //         return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
    //     }
    //     $uname = $request->uname;
    //     $pass = $request->pass;
    //     $imei = $request->imei;
    //     $v_code = $request->v_code;
    //     $v_name = $request->v_name;
    //     $company_id = $request->company_id;
    //     if(!empty($company_id))
    //     {    
    //         // $imei_insert = ['imei_number'=>$imei];
    //         // $data_insert = Person::join('person_login','person_login.person_id','=','person.id')
    //         //             ->where('person_username',$uname)
    //         //             ->where('person_password',DB::raw("AES_ENCRYPT('".trim($pass)."', '".Lang::get('common.db_salt')."')"))
    //         //             ->update($imei_insert);
            

    //         // $imei_query = Person::join('person_login','person_login.person_id','=','person.id')->where('person_username',$uname)
    //         // ->where('person_password',DB::raw("AES_ENCRYPT('".trim($pass)."', '".Lang::get('common.db_salt')."')"))->first();
            
    //         $person_query = Person::where('person_username',$uname)
    //                     ->join('person_login','person_login.person_id','=','person.id')
    //                     ->join('location_3','location_3.id','=','person.state_id')
    //                     ->join('person_details','person_details.person_id','=','person.id')
    //                     ->join('_role','_role.role_id','=','person.role_id')
    //                     ->join('company','company.id','=','person.company_id')
    //                     ->select('person.state_id as state_id','is_mtp_enabled','person_username','person.id as user_id','mobile','imei_number','person.email as user_email','rolename as designation','person.role_id as designation_id','emp_code','person_details.address as user_address','location_3.name as state','head_quar as head_quater','person_details.created_on as user_created_date')
    //                     ->where('person_password',DB::raw("AES_ENCRYPT('".trim($pass)."', '".Lang::get('common.db_salt')."')"))
    //                     ->where('imei_number',$imei)
    //                     ->where('person_status',1)
    //                     ->where('person_id_senior','!=',0)
    //                     ->where('company.id',$company_id)
    //                     ->get();
    //         // dd($person_query);
    //         if(COUNT($person_query)>0)
    //         {
    //             $url_details = DB::table('interface_url')
    //                         ->select('image_url as sync_image_url','company_id','signin_url','sync_post_url','test_url','version_code','status')
    //                         ->where('version_code',$v_name)
    //                         ->where('status',1)
    //                         ->where('company_id',$company_id)
    //                         ->get();

    //             $url_list = DB::table('url_list')
    //                         ->select('url_list.code as code','url_list.url_list as url_list')
    //                         ->join('assign_url_list','assign_url_list.url_list_id','=','url_list.id')
    //                         ->join('version_management','version_management.id','=','assign_url_list.v_name')
    //                         ->where('version_management.version_name',$v_name)
    //                         ->where('assign_url_list.status',1)
    //                         ->where('assign_url_list.company_id',$company_id)
    //                         ->where('version_management.company_id',$company_id)
    //                         ->get();

    //             foreach ($person_query as $key => $value)
    //             {
                    
    //                 $zone_data = DB::table('location_view')->where('l3_id',$value->state_id)->first();
    //                 $user_personal_data['is_mtp_enabled'] = $value->is_mtp_enabled;
    //                 $user_personal_data['user_id'] = $value->user_id;
    //                 $user_personal_data['person_username'] = $value->person_username;
    //                 $user_personal_data['mobile'] = $value->mobile;
    //                 $user_personal_data['imei_number'] = $value->imei_number;
    //                 $user_personal_data['user_email'] = $value->user_email;
    //                 $user_personal_data['designation_id'] = $value->designation_id;
    //                 $user_personal_data['designation'] = $value->designation;
    //                 $user_personal_data['emp_code'] = $value->emp_code;
    //                 $user_personal_data['user_address'] = $value->user_address;
    //                 $user_personal_data['state'] = $value->state;
    //                 $user_personal_data['zone'] = $zone_data->l1_name;
    //                 $user_personal_data['head_quater'] = $value->head_quater;
    //                 $user_personal_data['user_created_date'] = $value->user_created_date;
                    
    //                 $check_junior_data=JuniorData::getJuniorUser($value->user_id,$company_id);
    //                 $junior_data_check = Session::get('juniordata');
    //                 if(empty($junior_data_check))
    //                 {
    //                     $user_personal_data['is_junior'] = False;
    //                 }
    //                 else
    //                 {
    //                     $user_personal_data['is_junior'] = True;
    //                 }

    //             }
    //             // dd($person_query);
    //             $user_id = $person_query[0]->user_id; // return user id
    //             $state_id = $person_query[0]->state_id; // return user id
    //             $myArr=['version_code_name'=>"Version: $v_name/$v_code"];
    //             $update_query = DB::table('person')->where('id',$user_id)->update($myArr);
                
    //             ##................................... return the dealer details on the behalf of user id ................................##
    //                $user_dealer_retailer_query = DB::table('dealer')
    //                                            ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
    //                                            ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
    //                                            ->select('dealer.name as name','dealer.id as dealer_id','l6_name as lname','l6_id as lid')
    //                                            ->where('dealer_location_rate_list.user_id',$user_id)
    //                                            ->where('dealer.dealer_status',1)
    //                                            ->where('dealer.company_id',$company_id)
    //                                            ->where('dealer_location_rate_list.company_id',$company_id)
    //                                            ->groupBy('dealer.id')
    //                                            ->get();
    //                 $dealer_id = array();
    //                 foreach ($user_dealer_retailer_query as $key => $value)
    //                 {
    //                     $dealer_id[]=$value->dealer_id;
    //                     $dealer_data_string['dealer_id'] = "$value->dealer_id"; // return the data in string
    //                     $dealer_data_string['lid'] = "$value->lid"; // return the data in string
    //                     $dealer_data_string['lname'] = $value->lname;
    //                     $dealer_data_string['name'] = $value->name;
    //                     $final_dealer_data[] = $dealer_data_string; // merge all data in one array
    //                 }
    //             ##............................... return the beat details  on the behalf of dealer_id ................................##
    //                 $beat_data = DB::table('dealer_location_rate_list')
    //                             ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
    //                             ->select('dealer_location_rate_list.location_id as beat_id','location_7.name as name','dealer_location_rate_list.dealer_id as dealer_id')
    //                             ->whereIn('dealer_location_rate_list.dealer_id',$dealer_id)
    //                                ->where('dealer_location_rate_list.company_id',$company_id)
    //                                ->where('location_7.company_id',$company_id)
    //                             ->groupBy('dealer_location_rate_list.location_id','dealer_id')
    //                             ->get();
    //                 $beat_id = array();
    //                 foreach($beat_data as $key => $value)
    //                 {
    //                     $beat_id[] = $value->beat_id;
    //                     $beat_data_string['beat_id'] = "$value->beat_id"; // return the data in string
    //                     $beat_data_string['dealer_id'] = "$value->dealer_id"; // return the data in string
    //                     $beat_data_string['name'] = "$value->name"; // return the data in string
    //                     $final_data_beat[] = $beat_data_string; // merge all data in one array
    //                 }
    //             ##................................ return the retailer details on the behalf of beat id  ......................##   
    //                 $retailer_id_data = DB::table('retailer')->select('sequence_id as seq_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'_role.rolename as designation','retailer.created_on as created_on','created_by_person_id','retailer.tin_no as tin_no','location_7.name as beat_name','retailer.id as id','lat_long','retailer.name as retailer_name','location_id','address','retailer.email as email','contact_per_name','landline')
    //                     ->join('location_7','location_7.id','=','retailer.location_id')
    //                     ->join('person','person.id','=','retailer.created_by_person_id')
    //                     ->join('_role','_role.role_id','=','person.role_id')
    //                     ->whereIn('retailer.location_id',$beat_id)
    //                     ->where('retailer.company_id',$company_id)
    //                     ->where('location_7.company_id',$company_id)
    //                     ->where('_role.company_id',$company_id)
    //                     ->where('retailer_status',1)
    //                     ->groupBy('retailer.id')->get();


    //                 $last_order_book = DB::table("user_sales_order")
    //                                 ->select(DB::raw("CONCAT_WS(' ',date,time) as date_time"),'retailer_id')
    //                                 ->where('company_id',$company_id)
    //                                 ->groupBy('retailer_id')
    //                                 ->orderBy('date_time','DESC')
    //                                 ->pluck('date_time','retailer_id');
    //                 // dd($last_order_book);
    //                 foreach($retailer_id_data as $key => $value)
    //                 {
    //                     $retailer_id = $value->id;
    //                     $payment_collection_query = DB::table('payment_collection')->select(DB::raw('sum(total_amount) as paid'))->where('retailer_id',$retailer_id)->first();
    //                     $challan_data_query = DB::table('challan_order')->select(DB::raw('sum(amount) as ch_amt'))->where('ch_retailer_id',$retailer_id)->first();
    //                     $retailer_amt  = DB::table('payment_collection')->select('total_amount')->where('retailer_id',$retailer_id)->orderBy('pay_date_time','DESC')->first();
    //                     $retailer_data['retailer_id'] = "$value->id";
    //                     $retailer_data['retailer_name'] = $value->retailer_name;
    //                     $retailer_data['lat_long'] = !empty($value->lat_long)?$value->lat_long:'';
    //                     if(!empty($retailer_data['lat_long']))
    //                     {
    //                         $lat_lng = explode(',',$retailer_data['lat_long']);
    //                         $lat = $lat_lng[0];
    //                         $lng = $lat_lng[1];
    //                     }
    //                     else
    //                     {
    //                         $lat ='0.0' ;
    //                         $lng ='0.0' ;
    //                     }
                        
    //                     $retailer_data['lat'] = $lat;
    //                     $retailer_data['lng'] = $lng;
    //                     $retailer_data['location_id'] = "$value->location_id";
    //                     $retailer_data['address'] = $value->address;
    //                     $retailer_data['email'] = !empty($value->email)?$value->email:'';
    //                     $retailer_data['tin_no'] = $value->tin_no;
    //                     $retailer_data['contact_per_name'] = !empty($value->contact_per_name)?$value->contact_per_name:'';
    //                     $retailer_data['landline'] = $value->landline;
    //                     $retailer_data['seq_id'] = "$value->seq_id";
    //                     $retailer_data['created_by'] = $value->user_name;
    //                     $retailer_data['created_by_designation'] = $value->designation;
    //                     $retailer_data['created_at'] = $value->created_on;
    //                     $retailer_data['last_visit_date'] = !empty($last_order_book[$retailer_id])?$last_order_book[$retailer_id]:"No Oder book Yet";
    //                     $retailer_data['beat_name'] = $value->beat_name;
    //                     $outstanding = !empty($payment_collection_query)?($payment_collection_query->paid)-($challan_data_query->ch_amt):0;
    //                     $retailer_data['outstanding'] = "$outstanding";
    //                     $last_amt = !empty($retailer_amt)?$retailer_amt:0;
    //                     $retailer_data['last_amt'] = "$last_amt";
    //                     $retailer_data['achieved'] = !empty($challan_data_query->ch_amt)?$challan_data_query->ch_amt:'';
    //                     $retailer_data['last_date'] = "no date";
    //                     $final_retailer[] = $retailer_data;
    //                 }
    //             #.............................return dealer , beat and retailer array with all details ................................##

    //             #........................................... location master starts here.................................................##
    //                 // $location_master = DB::table('location_view')
    //                 //                 ->where('l1_company_id',$company_id)
    //                 //                 ->where('l2_company_id',$company_id)
    //                 //                 ->where('l3_company_id',$company_id)
    //                 //                 ->where('l4_company_id',$company_id)
    //                 //                 ->where('l5_company_id',$company_id)
    //                 //                 ->where('l6_company_id',$company_id)
    //                 //                 ->where('l7_company_id',$company_id)
    //                 //                 ->get();
    //                 // foreach ($location_master as $key => $value)
    //                 // {
    //                 //     $_location_master_array['l1_id'] = "$value->l1_id";
    //                 //     $_location_master_array['l1_name'] = $value->l1_name;
    //                 //     $_location_master_array['l2_id'] = "$value->l2_id";
    //                 //     $_location_master_array['l2_name'] = $value->l2_name;
    //                 //     $_location_master_array['l3_id'] = "$value->l3_id";
    //                 //     $_location_master_array['l3_name'] = $value->l3_name;
    //                 //     $_location_master_array['l4_id'] = "$value->l4_id";
    //                 //     $_location_master_array['l4_name'] = $value->l4_name;
    //                 //     $_location_master_array['l5_id'] = "$value->l5_id";
    //                 //     $_location_master_array['l5_name'] = $value->l5_name;
    //                 //     $_location_master_array['l6_id'] = "$value->l6_id";
    //                 //     $_location_master_array['l6_name'] = $value->l6_name;
    //                 //     $_location_master_array['l7_id'] = "$value->l7_id";
    //                 //     $_location_master_array['l7_name'] = $value->l7_name;
    //                 //     $location_master_details[] = $_location_master_array;

    //                 // }

    //             #............................................ location master ends here ................................................##

    //             #..........................................return colleague data starts here ........................................##

    //                 // for juniors **************************
    //                 Session::forget('juniordata');
    //                 $user_data=JuniorData::getJuniorUser($user_id,$company_id);
    //                 // dd($user_data);
    //                 $junior_data = Session::get('juniordata');
    //                 // dd($junior_data);
    //                 Session::forget('seniorData');
    //                    $fetch_senior_id = JuniorData::getSeniorUser($user_id,$company_id);
    //                    $senior_data = Session::get('seniorData');
    //                    // dd($senior_data);
    //                 $out = array();
    //                 $custom = 1;
                    
    //                 if(!empty($senior_data) && !empty($junior_data))
    //                 {
    //                     $juniors_name = Person::select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id')
    //                                      ->where('company_id',$company_id)
    //                                      ->whereIn('id',$junior_data)
    //                                      ->get();

    //                     $serniors_name = Person::select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id')
    //                                      ->where('company_id',$company_id)
    //                                      ->whereIn('id',$senior_data)
    //                                      ->get();
    //                     // dd($juniors_name);

    //                      $out=[0=>['id'=>'0','name'=>'SELF']];
                        
    //                     foreach($serniors_name as $s_key => $s_value)
    //                     {
    //                         $out[$custom]['id'] = $s_value->id;
    //                         $out[$custom]['name'] = $s_value->user_name;
    //                         $custom++;
    //                     }
    //                     foreach ($juniors_name as $key => $value)
    //                     {
    //                         $out[$custom]['id'] = $value->id;
    //                         $out[$custom]['name'] = $value->user_name;
    //                         $custom++;
    //                     }
    //                 }
    //                 elseif(!empty($senior_data))
    //                 {
    //                     $serniors_name = Person::select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id')
    //                                      ->where('company_id',$company_id)
    //                                      ->whereIn('id',$senior_data)
    //                                      ->get();
    //                     // dd($juniors_name);

    //                      $out=[0=>['id'=>'0','name'=>'SELF']];
                        
    //                     foreach($serniors_name as $s_key => $s_value)
    //                     {
    //                         $out[$custom]['id'] = $s_value->id;
    //                         $out[$custom]['name'] = $s_value->user_name;
    //                         $custom++;
    //                     }
    //                 }
    //                 elseif(!empty($junior_data))
    //                 {
    //                     $juniors_name = Person::select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id')
    //                                      ->where('company_id',$company_id)
    //                                      ->whereIn('id',$junior_data)
    //                                      ->get();
    //                     // dd($juniors_name);

    //                      $out=[0=>['id'=>'0','name'=>'SELF']];
                        
    //                     foreach ($juniors_name as $key => $value)
    //                     {
    //                         $out[$custom]['id'] = $value->id;
    //                         $out[$custom]['name'] = $value->user_name;
    //                         $custom++;
    //                     }
    //                 }
    //                 else
    //                 {
    //                     $junior_data[]=$user_id;
    //                      $out=[0=>['id'=>'0','name'=>'SELF']];
    //                 }
                    
                     
    //                 $collegueArr = $out;
    //                 // dd($collegueArr);
    //                 // working_with Ends here!!!!!!!!!!!

    //                 // ***** for working status drop down starts here ****
    //                 $working_status = DB::table('_working_status')
    //                                 ->select('name','id','company_id')
    //                                 ->where('company_id',$company_id)
    //                                 ->orderBy('sequence','ASC')
    //                                 ->where('status',1)
    //                                 ->get();


    //                 $app_module = DB::table('app_module')
    //                             ->join('master_list_module','master_list_module.id','=','app_module.module_id')
    //                             ->select('master_list_module.icon_image as module_icon_image','master_list_module.id as module_id','app_module.title_name as module_name','master_list_module.url as module_url')
    //                             ->where('app_module.company_id',$company_id)
    //                             ->where('app_module.status',1)
    //                             ->where('master_list_module.status',1)
    //                             ->orderBy('app_module.module_sequence','ASC')
    //                             ->get();
    //                 $module = array();
    //                 foreach ($app_module as $key => $value)
    //                 {
    //                     $module[$key]['module_icon_image'] = !empty($value->module_icon_image)?$value->module_icon_image:'';
    //                     $module[$key]['module_id'] = "$value->module_id";
    //                     $module[$key]['module_name'] = !empty($value->module_name)?$value->module_name:'';
    //                     $module[$key]['module_url'] = !empty($value->module_url)?$value->module_url:'';
    //                 }
    //                 $sub_module = DB::table('_sub_modules')
    //                             ->join('master_list_sub_module','master_list_sub_module.id','=','_sub_modules.sub_module_id')
    //                             ->select('master_list_sub_module.module_id as module_id','_sub_modules.sub_module_name as sub_module_name','master_list_sub_module.id as sub_module_id','master_list_sub_module.path as sub_module_url','master_list_sub_module.image_name as sub_module_icon_image')
    //                             ->where('_sub_modules.company_id',$company_id)
    //                             ->where('_sub_modules.status',1)
    //                             ->where('master_list_sub_module.status',1)
    //                             ->orderBy('_sub_modules.module_sequence','ASC')
    //                             ->get();
    //                 $sub_module_arr = array();
    //                 foreach ($sub_module as $key => $value)
    //                 {
    //                     $sub_module_arr[$key]['sub_module_icon_image'] = !empty($value->sub_module_icon_image)?$value->sub_module_icon_image:'';
    //                     $sub_module_arr[$key]['module_id'] = "$value->module_id";
    //                     $sub_module_arr[$key]['sub_module_id'] = "$value->sub_module_id";
    //                     $sub_module_arr[$key]['sub_module_name'] = !empty($value->sub_module_name)?$value->sub_module_name:'';
    //                     $sub_module_arr[$key]['sub_module_url'] = !empty($value->sub_module_url)?$value->sub_module_url:'';
    //                 }

    //                 $other_module = DB::table('app_other_module_assign')
    //                             ->join('master_other_app_module','master_other_app_module.id','=','app_other_module_assign.module_id')
    //                             ->select('master_other_app_module.image_name as other_module_icon_image','app_other_module_assign.title_name as other_module_name','app_other_module_assign.module_id as other_module_id')
    //                             ->where('app_other_module_assign.status',1)
    //                             ->where('app_other_module_assign.company_id',$company_id)
    //                             ->where('master_other_app_module.status',1)
    //                             ->orderBy('app_other_module_assign.module_sequence','ASC')
    //                             ->get();
    //                 $other_module_arr = array();
    //                 foreach ($other_module as $key => $value)
    //                 {
    //                     $other_module_arr[$key]['other_module_icon_image'] = !empty($value->other_module_icon_image)?$value->other_module_icon_image:'';
    //                     $other_module_arr[$key]['other_module_id'] = "$value->other_module_id";
    //                     $other_module_arr[$key]['other_module_name'] = !empty($value->other_module_name)?$value->other_module_name:'';
    //                 }

    //             #.................................state and town array on the behalf of distributor assign on user starts here .........##

    //                 $state_array = array();
    //                 $town_array = array();
    //                 $state_array = DB::table('location_3')
    //                             ->join('dealer','dealer.state_id','=','location_3.id')
    //                             ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
    //                             ->select('location_3.name as state_name',DB::raw("convert(location_3.id,CHAR) as l3_id"))
    //                             ->where('dealer_status',1)
    //                             ->where('location_3.status',1)
    //                             ->where('location_3.company_id',$company_id)
    //                             ->where('user_id',$user_id)
    //                             ->where('dealer.company_id',$company_id)
    //                             ->groupBy('location_3.id')
    //                             ->get();

    //                 $town_arr = array();
    //                 $town_array = array();
    //                 $town_array_data = DB::table('location_7')
    //                             ->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','location_7.id')
    //                             ->join('location_6','location_6.id','=','location_7.location_6_id')
    //                             ->join('location_5','location_5.id','=','location_6.location_5_id')
    //                             ->join('location_4','location_4.id','=','location_5.location_4_id')
    //                             ->join('location_3','location_3.id','=','location_4.location_3_id')
                                
    //                             ->select('location_6.name as town_name','location_6.id as l6_id','location_3.id as l3_id')
    //                             ->where('location_6.company_id',$company_id)
                            
                                
    //                             ->where('location_6.status',1)
    //                             ->where('user_id',$user_id)
                            
    //                             ->groupBy('location_6.id','location_3.id')
    //                             ->get();
    //                 foreach($town_array_data as $t_key => $t_value)
    //                 {
    //                     $town_arr['l4_id'] = "$t_value->l6_id";
    //                     $town_arr['town_name'] = $t_value->town_name;
    //                     $town_arr['l3_id'] = "$t_value->l3_id";
    //                     $town_array[] = $town_arr;
    //                 }
    //             #......................................Product overall data return starts here .........................................##
    //                 $product_array = DB::table('catalog_product')
    //                                 ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
    //                                 ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
    //                                 ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
    //                                 ->select('catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','unit','catalog_product.name','product_rate_list.retailer_pcs_rate as base_price','product_rate_list.mrp_pcs as mrp', 'product_rate_list.mrp_pcs','hsn_code','catalog_2.name as cname', 'product_rate_list.dealer_rate as dealer_rate','product_rate_list.dealer_pcs_rate as dealer_pcs_rate')
    //                                 ->where('catalog_1.status',1)
    //                                 ->where('catalog_2.status',1)
    //                                 ->where('catalog_product.status',1)
    //                                 ->where('state_id',$state_id)
    //                                 ->where('catalog_product.company_id',$company_id)
    //                                 ->get()->toArray();
    //                 $final_catalog_product_details = array();
    //                 foreach ($product_array as $key => $value)
    //                 {
    //                     $focus_query = DB::table('focus')
    //                                 ->select('product_id')
    //                                 ->where('company_id',$company_id)
    //                                 ->where('product_id',$value->id)
    //                                 ->get();
    //                     if(COUNT($focus_query)>0)
    //                     {
    //                         $focus_status = 1;
    //                     }
    //                     else
    //                     {
    //                         $focus_status = 0;
    //                     }
    //                     $focust_query = DB::table('focus_product_users_target')
    //                                 ->select('target_value as target_qty')
    //                                 ->where('company_id',$company_id)
    //                                 ->where('product_id',$value->id)
    //                                 ->where('user_id',$user_id)
    //                                 ->whereRaw("DATE_FORMAT(start_date,'%Y-%m-%d')>='date(Y-m-d)' AND DATE_FORMAT(end_date,'%Y-%m-%d')<='date(Y-m-d)'")
    //                                 ->first();

    //                     $querytax = DB::table('_gst')
    //                                 ->select('igst as tax')
    //                                 ->where('company_id',$company_id)
    //                                 ->where('hsn_code',$value->hsn_code)
    //                                 ->first();

    //                     $productArray['id'] = "$value->id";
    //                     $productArray['classification_id'] = "$value->classification_id";
    //                     $productArray['classification_name'] = $value->classification_name;
    //                     $productArray['category'] = "$value->catalog_id";
    //                     $productArray['hsn_code'] = $value->hsn_code;
    //                     $productArray['category_name'] = $value->cname;
    //                     $productArray['name'] = $value->name;
    //                     $productArray['weight'] = $value->weight;
    //                     $productArray['base_price'] = $value->base_price;
    //                     $productArray['dealer_rate'] = $value->dealer_rate;
    //                     $productArray['dealer_pcs_rate'] = $value->dealer_pcs_rate;
    //                     $productArray['mrp'] = $value->mrp;
    //                     $productArray['pcs_mrp'] = $value->mrp_pcs;
    //                     $productArray['unit'] = $value->unit;
    //                     $productArray['focus'] = "$focus_status";
    //                     $productArray['focus_target'] = !empty($focust_query->target_qty)?$focust_query->target_qty:'';
    //                     $productArray['tax'] = !empty($querytax->tax)?$querytax->tax:'';
    //                     $final_catalog_product_details[] = $productArray;

    //                 }

    //             #........................................product classification starts here ............................................##
    //                 $product_classification_query = DB::table('catalog_1')
    //                                             ->join('catalog_view','catalog_view.c1_id','=','catalog_1.id')
    //                                             ->select('catalog_1.id as id','catalog_1.name as name')
    //                                             ->where('catalog_1.company_id',$company_id)
    //                                             ->where('catalog_1.status',1)
    //                                             ->groupBy('c1_id')
    //                                             ->get()->toArray();
    //                 $final_product_classification_details = array();
    //                 foreach ($product_classification_query as $key => $value)
    //                 {
    //                     $classification_array['id']= "$value->id";
    //                     $classification_array['name']= $value->name;
    //                     $final_product_classification_details[] = $classification_array;
    //                 }
    //             #..........................................cataegory part starts here ..................................................##
    //                 $category_data = DB::table('catalog_2')
    //                         ->join('catalog_view','catalog_view.c2_id','=','catalog_2.id')
    //                         ->select('id','name', 'catalog_view.c1_id as classification_id', 'catalog_view.c1_name as classification_name')
    //                         ->where('catalog_2.company_id',$company_id)
    //                         ->where('catalog_2.status',1)
    //                         ->groupBy('c2_id')
    //                         ->get()->toArray();
    //                 $final_category_array = array();
    //                 foreach ($category_data as $key => $value)
    //                 {
    //                     $category_array['id'] = "$value->id";
    //                     $category_array['classification_id'] = "$value->classification_id";
    //                     $category_array['classification_name'] = $value->classification_name;
    //                     $category_array['name'] = $value->name;
    //                     $final_category_array[] = $category_array;
    //                 }
                
    //             #/.........................................mtp starts here .............................................................##
                    
    //                     $date = date('Y-m');
    //                     $mtp_query = DB::table('monthly_tour_program')
    //                                 ->select('rd','total_sales','working_date','locations','task_of_the_day')
    //                                 ->where('company_id',$company_id)
    //                                 ->where('person_id',$user_id)
    //                                 ->whereRaw("DATE_FORMAT(working_date,'%Y-%m')='$date'")
    //                                 ->get();
    //                     $mtp_array = array();
    //                     foreach ($mtp_query as $key => $value)
    //                     {
    //                         $beat_data = DB::table('location_7')->where('id',$value->locations)->first();
    //                         $data['total_sale'] = $value->total_sales;
    //                         $data['rd'] = $value->rd;
    //                         $data['date'] = $value->working_date;
    //                         $data['today'] = !empty($beat_data->name)?$beat_data->name:'';
    //                         $data['today_id'] = $value->locations;
    //                         $mtp_array[] = $data;
    //                     }
    //             #.............................................travelling mode starts here ..............................................##

    //                     $travelling_mode = DB::table('_travelling_mode')
    //                                     ->select('id','mode')
    //                                     ->where('company_id',$company_id)
    //                                     ->where('status',1)
    //                                     ->get();
    //                     $travel_array = array();
    //                     foreach ($travelling_mode as $key => $value)
    //                     {
    //                         $data_t['id'] = "$value->id";
    //                         $data_t['mode'] = $value->mode;
    //                         $travel_array[] = $data_t;
    //                     }
    //             #.............................................mtd target acheivement starts here........................................##
    //                     $current_date = date('Y-m-d');
    //                     $current_month = date('Y-m');
    //                     $mtd_target = '';
    //                     $mtd_achievement = '';
    //                     $mtd_target_query  = DB::table('user_sales_order')
    //                                         ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$current_date'")
    //                                         ->where('company_id',$company_id)
    //                                         ->where('user_id',$user_id)
    //                                         ->SUM('amount');

    //                     $mtd_second_query = DB::table('monthly_tour_program')
    //                                     ->whereRaw("DATE_FORMAT(working_date,'%Y-%m')='$current_month'")
    //                                     ->where('company_id',$company_id)
    //                                     ->where('person_id',$user_id)                                        
    //                                     ->SUM('rd');
                        
    //                     if(!empty($mtd_target_query) && !empty($mtd_second_query))
    //                     {
    //                         $percentage_ratio=($mtd_target_query/$mtd_second_query)*100;
    //                     }
    //                     else
    //                     {
    //                         $mtd_target=!empty($mtd_second_query)?$mtd_second_query:0;
    //                         $mtd_achievement=!empty($mtd_target_query)?$mtd_target_query:0;
    //                     }
    //             #...........................................product wise scheme starts here ............................................##

    //                 $product_wise_schme = DB::table('product_wise_scheme_plan')
    //                                     ->join('product_wise_scheme_plan_details','product_wise_scheme_plan_details.scheme_id','=','product_wise_scheme_plan.id')
    //                                     ->select('product_wise_scheme_plan.*','product_wise_scheme_plan_details.*')
    //                                     ->where('product_wise_scheme_plan.company_id',$company_id)
    //                                     ->where('product_wise_scheme_plan.status',1)
    //                                     ->groupBy('product_wise_scheme_plan_details.product_id')
    //                                     ->get();
    //                 // dd($product_wise_schme);
    //                 $final_product_wise_scheme_array = array();
    //                 foreach ($product_wise_schme as $ps_key => $ps_value) 
    //                 {
    //                     if($ps_value->sale_unit==1)
    //                     {
    //                         $sale_unit = 'weight';
    //                     }
    //                     elseif($ps_value->sale_unit==2)
    //                     {
    //                         $sale_unit = 'cases';
    //                     }
    //                     elseif($ps_value->sale_unit==3)
    //                     {
    //                         $sale_unit = 'pcs';
    //                     }
    //                     elseif($ps_value->sale_unit==4)
    //                     {
    //                         $sale_unit = 'range';
    //                     }
    //                     else
    //                     {
    //                         $sale_unit = 'other';
    //                     }

    //                     if($ps_value->incentive_type==1)
    //                     {
    //                         $incentive_type = 'Percentage';
    //                     }
    //                     elseif($ps_value->incentive_type==2)
    //                     {
    //                         $incentive_type = 'Amount';
    //                     }
    //                     elseif($ps_value->incentive_type==3)
    //                     {
    //                         $incentive_type = 'Free Quantity';
    //                     }
    //                     else
    //                     {
    //                         $incentive_type = 'other';
    //                     }
    //                     $product_wise_scheme_array['product_scheme_name'] = $ps_value->scheme_name;
    //                     $product_wise_scheme_array['product_id'] = "$ps_value->product_id";
    //                     $product_wise_scheme_array['sale_unit_id'] = $sale_unit_id;
    //                     $product_wise_scheme_array['sale_unit_name'] = $sale_unit_name;
    //                     $product_wise_scheme_array['sale_value_range_first'] = $ps_value->sale_value_range_first;
    //                     $product_wise_scheme_array['sale_value_range_last'] = $ps_value->sale_value_range_last;
    //                     $product_wise_scheme_array['incentive_type'] = $ps_value->incentive_type;
    //                     $product_wise_scheme_array['reward'] = $ps_value->value_amount_percentage;
    //                     $final_product_wise_scheme_array[] = $product_wise_scheme_array;


    //                 }

    //             #...........................................product wise scheme ends here ..............................................##
    //             #......................................... non productive reason starts here ...........................................##
    //                 $non_productive_reason_query  = DB::table('_no_sale_reason')
    //                                             ->select('id','name')
    //                                             ->where('company_id',$company_id)
    //                                             ->where('status',1)
    //                                             ->get();
    //                 $final_non_productive_query = array();
    //                 foreach ($non_productive_reason_query as $key => $value)
    //                 {
    //                     $non_productive_array['id'] = "$value->id";
    //                     $non_productive_array['name'] = $value->name;
    //                     $final_non_productive_query[] = $non_productive_array;
    //                 }
    //             #..........................................Daily schedule starts here ..................................................##
    //                 $daily_schedule_query = DB::table('_daily_schedule')
    //                                     ->select('id','name')
    //                                     ->where('company_id',$company_id)
    //                                     ->where('status',1)
    //                                     ->orderBy('id','ASC')
    //                                     ->get();
    //                 $daily_schedule_details = array();
    //                 foreach ($daily_schedule_query as $key => $value)
    //                 {
    //                     $daily_schedule_array['id'] = "$value->id";
    //                     $daily_schedule_array['name'] = $value->name;
    //                     $daily_schedule_details[] = $daily_schedule_array;  
    //                 }
    //             #.....................................task of the day starts here ......................................................##
    //                 $task_query = DB::table('_task_of_the_day')
    //                             ->where('company_id',$company_id)
    //                             ->where('status',1)
    //                             ->get();
    //                 $task = array();
    //                 foreach ($task_query as $key => $value)
    //                 {
    //                     $task_array['id'] = "$value->id";
    //                     $task_array['name'] = $value->task;
    //                     $task[] = $task_array;
    //                 }
    //             #......................................payment modes starts here ................................................................................................##
    //             $payment_modes  = DB::table('_payment_modes')
    //                             ->where('status',1)
    //                             ->get();

    //             #......................................outlet type  starts here ................................................................................................##
    //             $retailer_outelet_types  = DB::table('_retailer_outlet_type')
    //                             ->where('status',1)
    //                             ->where('company_id',$company_id)
    //                             ->orderBy('sequence','ASC')
    //                             ->get();
    //             #......................................outlet category modes starts here ................................................................................................##
    //             $retailer_outelet_category  = DB::table('_retailer_outlet_category')
    //                             ->where('status',1)
    //                             ->where('company_id',$company_id)
    //                             ->orderBy('sequence','ASC')
    //                             ->get();
    //             #......................................schedule type starts here ................................................................................................##
    //             $daily_schedule  = DB::table('_daily_schedule')
    //                             ->where('status',1)
    //                             ->where('company_id',$company_id)
    //                             ->orderBy('sequence','ASC')
    //                             ->get();
    //             #......................................return type starts here ................................................................................................##
    //             $return_type  = DB::table('_return_type_damage')
    //                             ->where('status',1)
    //                             ->where('company_id',$company_id)
    //                             ->get();
    //             #......................................no sale reason starts here ................................................................................................##
    //             $reason_type  = DB::table('_no_sale_reason')
    //                             ->where('status',1)
    //                             ->orderBy('sequence','ASC')
    //                             ->where('company_id',$company_id)
    //                             ->get();
    //             $meeting_type = array();
    //             $meeting_type  = DB::table('_meeting_type')
    //                             ->where('status',1)
    //                             ->orderBy('sequence','ASC')
    //                             ->where('company_id',$company_id)
    //                             ->get();
    //             #below array run over all company like gloabally
    //             $not_contacted_reason = DB::table('_not_contacted_reason')
    //                                 ->where('status',1)
    //                                 ->orderBy('sequence','ASC')
    //                                 ->get();
    //             #......................................reponse parameters starts here ..................................................##
    //                 return response()->json([
    //                     'response' =>True,

    //                     'url_list'=>$url_list,
    //                     'url_details'=>$url_details,
    //                     'company_id'=>$company_id,
    //                     'app_module'=> $module,
    //                     'sub_module'=> $sub_module_arr,
    //                     'user_details'=>!empty($user_personal_data)?$user_personal_data:array(), // user data
    //                     'dealer'=>!empty($final_dealer_data)?$final_dealer_data:array(), // dealer data
    //                     'beat'=>!empty($final_data_beat)?$final_data_beat:array(), // beat data (location_5)
    //                     'retailer'=>!empty($final_retailer)?$final_retailer:array(), // retailer all above response data dependend on each other
    //                     'colleague' => $collegueArr,
    //                     'working_status'=>$working_status,
    //                     'state_array'=>$state_array,
    //                     'town_array'=>$town_array,
    //                     'product'=>$final_catalog_product_details,
    //                     'product_classification'=>$product_classification_query,
    //                     'category' => $final_category_array,
    //                     'non_productive_reason'=> $final_non_productive_query,
    //                     'daily_schedule' => $daily_schedule_details,
    //                     'task_of_the_day'=>$task,
    //                     'mtp'=>$mtp_array,
    //                     'travelling_modes'=>$travel_array,
    //                     'mtd_target'=>$mtd_target,
    //                     'mtd_achievement'=>$mtd_achievement,
    //                     'payment_modes'=> $payment_modes,
    //                     'retailer_outelet_types'=>$retailer_outelet_types,
    //                     'retailer_outelet_category'=> $retailer_outelet_category,
    //                     // 'daily_schedule'=> $daily_schedule,
    //                     'return_type' => $return_type,
    //                     'reason_type'=> $reason_type,
    //                     'meeting_type'=> $meeting_type,
    //                     'other_module_arr'=> $other_module_arr,
    //                     'not_contacted_reason'=> $not_contacted_reason,
    //                     'final_product_wise_scheme_array'=> $final_product_wise_scheme_array,
    //                     // 'location_master_details'=> $location_master_details,
    //                     'message'=>'Success!!']);
    //             #......................................reponse parameters ends here ..................................................##

                   
                     
    //         } // person_query !empty ends here
    //         else
    //         {
    //             return response()->json([ 'response' =>False,'message'=>'!!User Data Record Not Found!!']);        
    //         }
    //     } // company id !empty ends here
    //     else
    //     {
    //         return response()->json([ 'response' =>False,'message'=>'!!Company Id Not Found!!']);        
    //     }
            


    // }
    # above function is gateway for entery in our software ends here

    // # for xotic login
    // public function xotik_login_v1(Request $request)
    // {
    //     $validator=Validator::make($request->all(),[
 //            'uname' => 'required',
 //            'imei' => 'required',   
 //            'v_name' => 'required',
 //            'v_code' => 'required',
 //            'pass' => 'required',
 //            'company_id' => 'required',
          
 //        ]);
 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
 //        }
    //     $uname = $request->uname;
    //     $pass = $request->pass;
    //     $imei = $request->imei;
    //     $v_code = $request->v_code;
    //     $v_name = $request->v_name;
    //     $company_id = $request->company_id;
    //     if(!empty($company_id))
    //     {    
    //         // $imei_insert = ['imei_number'=>$imei];
    //         // $data_insert = Person::join('person_login','person_login.person_id','=','person.id')
    //         //             ->where('person_username',$uname)
    //         //             ->where('person_password',DB::raw("AES_ENCRYPT('".trim($pass)."', '".Lang::get('common.db_salt')."')"))
    //         //             ->update($imei_insert);
            

    //         // $imei_query = Person::join('person_login','person_login.person_id','=','person.id')->where('person_username',$uname)
    //         // ->where('person_password',DB::raw("AES_ENCRYPT('".trim($pass)."', '".Lang::get('common.db_salt')."')"))->first();
            
    //         $person_query = Person::where('person_username',$uname)
    //                     ->join('person_login','person_login.person_id','=','person.id')
    //                     ->join('location_3','location_3.id','=','person.state_id')
    //                     ->join('person_details','person_details.person_id','=','person.id')
    //                     ->join('_role','_role.role_id','=','person.role_id')
    //                     ->join('company','company.id','=','person.company_id')
    //                     ->select('person.state_id as state_id','is_mtp_enabled','person_username','person.id as user_id','mobile','imei_number','person.email as user_email','rolename as designation','person.role_id as designation_id','emp_code','person_details.address as user_address','location_3.name as state','head_quar as head_quater','person_details.created_on as user_created_date')
    //                     ->where('person_password',DB::raw("AES_ENCRYPT('".trim($pass)."', '".Lang::get('common.db_salt')."')"))
    //                     ->where('imei_number',$imei)
    //                     ->where('person_status',1)
    //                     ->where('person_id_senior','!=',0)
    //                     ->where('company.id',$company_id)
    //                     ->get();
    //         // dd($person_query);
    //         if(COUNT($person_query)>0)
    //         {
    //             $url_details = DB::table('interface_url')
    //                         ->select('image_url as sync_image_url','company_id','signin_url','sync_post_url','test_url','version_code','status')
    //                         ->where('version_code',$v_name)
    //                         ->where('status',1)
    //                         ->where('company_id',$company_id)
    //                         ->get();

    //             $url_list = DB::table('url_list')
    //                         ->select('url_list.code as code','url_list.url_list as url_list')
    //                         ->join('assign_url_list','assign_url_list.url_list_id','=','url_list.id')
    //                         ->join('version_management','version_management.id','=','assign_url_list.v_name')
    //                         ->where('version_management.version_name',$v_name)
    //                         ->where('assign_url_list.status',1)
    //                         ->where('assign_url_list.company_id',$company_id)
    //                         ->where('version_management.company_id',$company_id)
    //                         ->get();

    //             foreach ($person_query as $key => $value)
    //             {
                    
    //                 $zone_data = DB::table('location_view')->where('l3_id',$value->state_id)->first();
    //                 $user_personal_data['is_mtp_enabled'] = $value->is_mtp_enabled;
    //                 $user_personal_data['user_id'] = $value->user_id;
    //                 $user_personal_data['person_username'] = $value->person_username;
    //                 $user_personal_data['mobile'] = $value->mobile;
    //                 $user_personal_data['imei_number'] = $value->imei_number;
    //                 $user_personal_data['user_email'] = $value->user_email;
    //                 $user_personal_data['designation_id'] = $value->designation_id;
    //                 $user_personal_data['designation'] = $value->designation;
    //                 $user_personal_data['emp_code'] = $value->emp_code;
    //                 $user_personal_data['user_address'] = $value->user_address;
    //                 $user_personal_data['state'] = $value->state;
    //                 $user_personal_data['zone'] = $zone_data->l1_name;
    //                 $user_personal_data['head_quater'] = $value->head_quater;
    //                 $user_personal_data['user_created_date'] = $value->user_created_date;
                    
    //                 $check_junior_data=JuniorData::getJuniorUser($value->user_id,$company_id);
    //                 $junior_data_check = Session::get('juniordata');
    //                 if(empty($junior_data_check))
    //                 {
    //                     $user_personal_data['is_junior'] = False;
    //                 }
    //                 else
    //                 {
    //                     $user_personal_data['is_junior'] = True;
    //                 }

    //             }
    //             // dd($person_query);
 //                $user_id = $person_query[0]->user_id; // return user id
 //                $check_role_id = $person_query[0]->designation_id; // return user id
 //                $state_id = $person_query[0]->state_id; // return user id
    //             $myArr=['version_code_name'=>"Version: $v_name/$v_code"];
 //                $update_query = DB::table('person')->where('id',$user_id)->update($myArr);
                
    //             ##................................... return the dealer details on the behalf of user id ................................##
    //                $user_dealer_retailer_query = DB::table('dealer')
    //                                            ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
    //                                            ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
    //                                            ->select('dealer.name as name','dealer.id as dealer_id','l6_name as lname','l6_id as lid')
    //                                            ->where('dealer_location_rate_list.user_id',$user_id)
    //                                            ->where('dealer.dealer_status',1)
    //                                            ->where('dealer.company_id',$company_id)
    //                                            ->where('dealer_location_rate_list.company_id',$company_id)
    //                                            ->groupBy('dealer.id')
    //                                            ->get();
    //                 $dealer_id = array();
    //                 foreach ($user_dealer_retailer_query as $key => $value)
    //                 {
    //                     $dealer_id[]=$value->dealer_id;
    //                     $dealer_data_string['dealer_id'] = "$value->dealer_id"; // return the data in string
    //                     $dealer_data_string['lid'] = "$value->lid"; // return the data in string
    //                     $dealer_data_string['lname'] = $value->lname;
    //                     $dealer_data_string['name'] = $value->name;
    //                     $final_dealer_data[] = $dealer_data_string; // merge all data in one array
    //                 }
    //             ##............................... return the beat details  on the behalf of dealer_id ................................##
    //                 $beat_data = DB::table('dealer_location_rate_list')
    //                             ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
    //                             ->select('dealer_location_rate_list.location_id as beat_id','location_7.name as name','dealer_location_rate_list.dealer_id as dealer_id')
    //                             ->whereIn('dealer_location_rate_list.dealer_id',$dealer_id)
 //                                   ->where('dealer_location_rate_list.company_id',$company_id)
 //                                   ->where('location_7.company_id',$company_id)
    //                             ->groupBy('dealer_location_rate_list.location_id','dealer_id')
    //                             ->get();
    //                 $beat_id = array();
    //                 foreach($beat_data as $key => $value)
    //                 {
    //                     $beat_id[] = $value->beat_id;
    //                     $beat_data_string['beat_id'] = "$value->beat_id"; // return the data in string
    //                     $beat_data_string['dealer_id'] = "$value->dealer_id"; // return the data in string
    //                     $beat_data_string['name'] = "$value->name"; // return the data in string
    //                     $final_data_beat[] = $beat_data_string; // merge all data in one array
    //                 }
    //             ##................................ return the retailer details on the behalf of beat id  ......................##   
    //                 $retailer_id_data = DB::table('retailer')->select('sequence_id as seq_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'_role.rolename as designation','retailer.created_on as created_on','created_by_person_id','retailer.tin_no as tin_no','location_7.name as beat_name','retailer.id as id','lat_long','retailer.name as retailer_name','location_id','address','retailer.email as email','contact_per_name','landline')
    //                     ->join('location_7','location_7.id','=','retailer.location_id')
    //                     ->join('person','person.id','=','retailer.created_by_person_id')
    //                     ->join('_role','_role.role_id','=','person.role_id')
    //                     ->whereIn('retailer.location_id',$beat_id)
    //                     ->where('retailer.company_id',$company_id)
    //                     ->where('location_7.company_id',$company_id)
    //                     ->where('_role.company_id',$company_id)
    //                     ->where('retailer_status',1)
    //                     ->groupBy('retailer.id')->get();


    //                 $last_order_book = DB::table("user_sales_order")
    //                                 ->select(DB::raw("CONCAT_WS(' ',date,time) as date_time"),'retailer_id')
    //                                 ->where('company_id',$company_id)
    //                                 ->groupBy('retailer_id')
    //                                 ->orderBy('date_time','DESC')
    //                                 ->pluck('date_time','retailer_id');
    //                 // dd($last_order_book);
    //                 foreach($retailer_id_data as $key => $value)
    //                 {
    //                     $retailer_id = $value->id;
    //                     $payment_collection_query = DB::table('payment_collection')->select(DB::raw('sum(total_amount) as paid'))->where('retailer_id',$retailer_id)->first();
    //                     $challan_data_query = DB::table('challan_order')->select(DB::raw('sum(amount) as ch_amt'))->where('ch_retailer_id',$retailer_id)->first();
    //                     $retailer_amt  = DB::table('payment_collection')->select('total_amount')->where('retailer_id',$retailer_id)->orderBy('pay_date_time','DESC')->first();
    //                     $retailer_data['retailer_id'] = "$value->id";
    //                     $retailer_data['retailer_name'] = $value->retailer_name;
    //                     $retailer_data['lat_long'] = !empty($value->lat_long)?$value->lat_long:'';
    //                     if(!empty($retailer_data['lat_long']))
    //                     {
    //                         $lat_lng = explode(',',$retailer_data['lat_long']);
    //                         $lat = $lat_lng[0];
    //                         $lng = $lat_lng[1];
    //                     }
    //                     else
    //                     {
    //                         $lat ='0.0' ;
    //                         $lng ='0.0' ;
    //                     }
                        
    //                     $retailer_data['lat'] = $lat;
    //                     $retailer_data['lng'] = $lng;
    //                     $retailer_data['location_id'] = "$value->location_id";
    //                     $retailer_data['address'] = $value->address;
    //                     $retailer_data['email'] = !empty($value->email)?$value->email:'';
    //                     $retailer_data['tin_no'] = $value->tin_no;
    //                     $retailer_data['contact_per_name'] = !empty($value->contact_per_name)?$value->contact_per_name:'';
    //                     $retailer_data['landline'] = $value->landline;
    //                     $retailer_data['seq_id'] = "$value->seq_id";
    //                     $retailer_data['created_by'] = $value->user_name;
    //                     $retailer_data['created_by_designation'] = $value->designation;
    //                     $retailer_data['created_at'] = $value->created_on;
    //                     $retailer_data['last_visit_date'] = !empty($last_order_book[$retailer_id])?$last_order_book[$retailer_id]:"No Oder book Yet";
    //                     $retailer_data['beat_name'] = $value->beat_name;
    //                     $outstanding = !empty($payment_collection_query)?($payment_collection_query->paid)-($challan_data_query->ch_amt):0;
    //                     $retailer_data['outstanding'] = "$outstanding";
    //                     $last_amt = !empty($retailer_amt)?$retailer_amt:0;
    //                     $retailer_data['last_amt'] = "$last_amt";
    //                     $retailer_data['achieved'] = !empty($challan_data_query->ch_amt)?$challan_data_query->ch_amt:'';
    //                     $retailer_data['last_date'] = "no date";
    //                     $final_retailer[] = $retailer_data;
    //                 }
    //             #.............................return dealer , beat and retailer array with all details ................................##

    //             #........................................... location master starts here.................................................##
    //                 // $location_master = DB::table('location_view')
    //                 //                 ->where('l1_company_id',$company_id)
    //                 //                 ->where('l2_company_id',$company_id)
    //                 //                 ->where('l3_company_id',$company_id)
    //                 //                 ->where('l4_company_id',$company_id)
    //                 //                 ->where('l5_company_id',$company_id)
    //                 //                 ->where('l6_company_id',$company_id)
    //                 //                 ->where('l7_company_id',$company_id)
    //                 //                 ->get();
    //                 // foreach ($location_master as $key => $value)
    //                 // {
    //                 //     $_location_master_array['l1_id'] = "$value->l1_id";
    //                 //     $_location_master_array['l1_name'] = $value->l1_name;
    //                 //     $_location_master_array['l2_id'] = "$value->l2_id";
    //                 //     $_location_master_array['l2_name'] = $value->l2_name;
    //                 //     $_location_master_array['l3_id'] = "$value->l3_id";
    //                 //     $_location_master_array['l3_name'] = $value->l3_name;
    //                 //     $_location_master_array['l4_id'] = "$value->l4_id";
    //                 //     $_location_master_array['l4_name'] = $value->l4_name;
    //                 //     $_location_master_array['l5_id'] = "$value->l5_id";
    //                 //     $_location_master_array['l5_name'] = $value->l5_name;
    //                 //     $_location_master_array['l6_id'] = "$value->l6_id";
    //                 //     $_location_master_array['l6_name'] = $value->l6_name;
    //                 //     $_location_master_array['l7_id'] = "$value->l7_id";
    //                 //     $_location_master_array['l7_name'] = $value->l7_name;
    //                 //     $location_master_details[] = $_location_master_array;

    //                 // }

    //             #............................................ location master ends here ................................................##

    //             #..........................................return colleague data starts here ........................................##

    //                 // for juniors **************************
    //                 Session::forget('juniordata');
    //                 $user_data=JuniorData::getJuniorUser($user_id,$company_id);
    //                 // dd($user_data);
    //                 $junior_data = Session::get('juniordata');
 //                    // dd($junior_data);
    //                 Session::forget('seniorData');
 //                       $fetch_senior_id = JuniorData::getSeniorUser($user_id,$company_id);
 //                       $senior_data = Session::get('seniorData');
 //                       // dd($senior_data);
    //                 $out = array();
    //                 $custom = 1;
                    
    //                 if(!empty($senior_data) && !empty($junior_data))
    //                 {
    //                     $juniors_name = Person::select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id')
    //                                      ->where('company_id',$company_id)
    //                                      ->whereIn('id',$junior_data)
    //                                      ->get();

    //                     $serniors_name = Person::select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id')
    //                                      ->where('company_id',$company_id)
    //                                      ->whereIn('id',$senior_data)
    //                                      ->get();
    //                     // dd($juniors_name);

    //                      $out=[0=>['id'=>'0','name'=>'SELF']];
                        
    //                     foreach($serniors_name as $s_key => $s_value)
    //                     {
    //                         $out[$custom]['id'] = $s_value->id;
    //                         $out[$custom]['name'] = $s_value->user_name;
    //                         $custom++;
    //                     }
    //                     foreach ($juniors_name as $key => $value)
    //                     {
    //                         $out[$custom]['id'] = $value->id;
    //                         $out[$custom]['name'] = $value->user_name;
    //                         $custom++;
    //                     }
    //                 }
    //                 elseif(!empty($senior_data))
    //                 {
    //                     $serniors_name = Person::select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id')
    //                                      ->where('company_id',$company_id)
    //                                      ->whereIn('id',$senior_data)
    //                                      ->get();
    //                     // dd($juniors_name);

    //                      $out=[0=>['id'=>'0','name'=>'SELF']];
                        
    //                     foreach($serniors_name as $s_key => $s_value)
    //                     {
    //                         $out[$custom]['id'] = $s_value->id;
    //                         $out[$custom]['name'] = $s_value->user_name;
    //                         $custom++;
    //                     }
    //                 }
    //                 elseif(!empty($junior_data))
    //                 {
    //                     $juniors_name = Person::select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id')
    //                                      ->where('company_id',$company_id)
    //                                      ->whereIn('id',$junior_data)
    //                                      ->get();
    //                     // dd($juniors_name);

    //                      $out=[0=>['id'=>'0','name'=>'Not Available']];
                        
    //                     foreach ($juniors_name as $key => $value)
    //                     {
    //                         $out[$custom]['id'] = $value->id;
    //                         $out[$custom]['name'] = $value->user_name;
    //                         $custom++;
    //                     }
    //                 }
    //                 else
    //                 {
    //                     $junior_data[]=$user_id;
    //                      $out=[0=>['id'=>'0','name'=>'Not Available']];
    //                 }
                    
                     
    //                 $collegueArr = $out;
    //                 // dd($collegueArr);
    //                 // working_with Ends here!!!!!!!!!!!

    //                 // ***** for working status drop down starts here ****
    //                 $working_status = DB::table('_working_status')
    //                                 ->select('name','id','company_id')
    //                                 ->where('company_id',$company_id)
    //                                 ->orderBy('sequence','ASC')
    //                                 ->where('status',1)
    //                                 ->get();

    //                 $area_of_work = DB::table('_area_of_work')
    //                                 ->select('name','id','company_id')
    //                                 ->where('company_id',$company_id)
    //                                 ->orderBy('sequence','ASC')
    //                                 ->where('status',1)
    //                                 ->get();


    //                 $working_with_type = DB::table('_working_with_type')
    //                                 ->select('name','id','company_id')
    //                                 ->where('company_id',$company_id)
    //                                 ->orderBy('sequence','ASC')
    //                                 ->where('status',1)
    //                                 ->get();


    //                 $working_activity = DB::table('_working_activity')
    //                                 ->select('name','id','company_id')
    //                                 ->where('company_id',$company_id)
    //                                 ->orderBy('sequence','ASC')
    //                                 ->where('status',1)
    //                                 ->get();            

    //                 $check_role_wise_assing_module = DB::table('role_app_module')
    //                                                 ->join('master_list_module','master_list_module.id','=','role_app_module.module_id')
    //                                                 ->select('master_list_module.icon_image as module_icon_image','master_list_module.id as module_id','role_app_module.title_name as module_name','master_list_module.url as module_url')
    //                                                 ->where('role_app_module.company_id',$company_id)
    //                                                 ->where('role_app_module.status',1)
    //                                                 ->where('role_app_module.status',1)
    //                                                 ->where('role_app_module.role_id',$check_role_id)
    //                                                 ->orderBy('role_app_module.module_sequence','ASC')
    //                                                 ->get();
    //                 # retailer creation with otp condition start here
    //                 $retailer_with_otp_creation = $other_module = DB::table('app_other_module_assign')
    //                             ->join('master_other_app_module','master_other_app_module.id','=','app_other_module_assign.module_id')
    //                             ->select('master_other_app_module.image_name as other_module_icon_image','app_other_module_assign.title_name as other_module_name','app_other_module_assign.module_id as other_module_id')
    //                             ->where('app_other_module_assign.status',1)
    //                             ->where('app_other_module_assign.company_id',$company_id)
    //                             ->where('master_other_app_module.id',2)
    //                             ->where('master_other_app_module.status',1)
    //                             ->orderBy('app_other_module_assign.module_sequence','ASC')
    //                             ->get();
    //                     $retailer_with_otp_creation_array = array();
    //                     foreach ($retailer_with_otp_creation as $key => $value)
    //                     {
    //                         $retailer_with_otp_creation_array[$key]['other_module_icon_image'] = !empty($value->other_module_icon_image)?$value->other_module_icon_image:'';
    //                         $retailer_with_otp_creation_array[$key]['other_module_id'] = "$value->other_module_id";
    //                         $retailer_with_otp_creation_array[$key]['other_module_name'] = !empty($value->other_module_name)?$value->other_module_name:'';
    //                     }
    //                 # retailer creation with otp condition ends here

    //                 if(COUNT($check_role_wise_assing_module)>0)
    //                 {
    //                     $module = array();
    //                     foreach ($check_role_wise_assing_module as $key => $value)
    //                     {
    //                         $module[$key]['module_icon_image'] = !empty($value->module_icon_image)?$value->module_icon_image:'';
    //                         $module[$key]['module_id'] = "$value->module_id";
    //                         $module[$key]['module_name'] = !empty($value->module_name)?$value->module_name:'';
    //                         $module[$key]['module_url'] = !empty($value->module_url)?$value->module_url:'';
    //                     }
    //                     $role_sub_module = DB::table('role_sub_modules')
    //                                 ->join('master_list_sub_module','master_list_sub_module.id','=','role_sub_modules.sub_module_id')
    //                                 ->select('master_list_sub_module.module_id as module_id','role_sub_modules.sub_module_name as sub_module_name','master_list_sub_module.id as sub_module_id','master_list_sub_module.path as sub_module_url','master_list_sub_module.image_name as sub_module_icon_image')
    //                                 ->where('role_sub_modules.company_id',$company_id)
    //                                 ->where('role_sub_modules.status',1)
    //                                 ->where('master_list_sub_module.status',1)
    //                                 ->where('role_sub_modules.role_id',$check_role_id)
    //                                 ->orderBy('role_sub_modules.module_sequence','ASC')
    //                                 ->get();
    //                     $sub_module_arr = array();
    //                     foreach ($role_sub_module as $key => $value)
    //                     {
    //                         $sub_module_arr[$key]['sub_module_icon_image'] = !empty($value->sub_module_icon_image)?$value->sub_module_icon_image:'';
    //                         $sub_module_arr[$key]['module_id'] = "$value->module_id";
    //                         $sub_module_arr[$key]['sub_module_id'] = "$value->sub_module_id";
    //                         $sub_module_arr[$key]['sub_module_name'] = !empty($value->sub_module_name)?$value->sub_module_name:'';
    //                         $sub_module_arr[$key]['sub_module_url'] = !empty($value->sub_module_url)?$value->sub_module_url:'';
    //                     }

    //                     $other_module = DB::table('role_app_other_module_assign')
    //                             ->join('master_other_app_module','master_other_app_module.id','=','role_app_other_module_assign.module_id')
    //                             ->select('master_other_app_module.image_name as other_module_icon_image','role_app_other_module_assign.title_name as other_module_name','role_app_other_module_assign.module_id as other_module_id')
    //                             ->where('role_app_other_module_assign.status',1)
    //                             ->where('role_app_other_module_assign.company_id',$company_id)
    //                             ->where('master_other_app_module.id','!=',2) // beacaue this id belongs to retailer creation with otp so this condition true above the role wise module condition starts
    //                             ->where('master_other_app_module.status',1)
    //                             ->where('role_app_other_module_assign.role_id',$check_role_id)
    //                             ->orderBy('role_app_other_module_assign.module_sequence','ASC')
    //                             ->get();
    //                     $other_module_arr = array();
    //                     foreach ($other_module as $key => $value)
    //                     {
    //                         $other_module_arr[$key]['other_module_icon_image'] = !empty($value->other_module_icon_image)?$value->other_module_icon_image:'';
    //                         $other_module_arr[$key]['other_module_id'] = "$value->other_module_id";
    //                         $other_module_arr[$key]['other_module_name'] = !empty($value->other_module_name)?$value->other_module_name:'';
    //                     }
    //                 }
    //                 else
    //                 {
    //                     $app_module = DB::table('app_module')
    //                             ->join('master_list_module','master_list_module.id','=','app_module.module_id')
    //                             ->select('master_list_module.icon_image as module_icon_image','master_list_module.id as module_id','app_module.title_name as module_name','master_list_module.url as module_url')
    //                             ->where('app_module.company_id',$company_id)
    //                             ->where('app_module.status',1)
    //                             ->where('master_list_module.status',1)
    //                             ->orderBy('app_module.module_sequence','ASC')
    //                             ->get();
    //                     $module = array();
    //                     foreach ($app_module as $key => $value)
    //                     {
    //                         $module[$key]['module_icon_image'] = !empty($value->module_icon_image)?$value->module_icon_image:'';
    //                         $module[$key]['module_id'] = "$value->module_id";
    //                         $module[$key]['module_name'] = !empty($value->module_name)?$value->module_name:'';
    //                         $module[$key]['module_url'] = !empty($value->module_url)?$value->module_url:'';
    //                     }
    //                     $sub_module = DB::table('_sub_modules')
    //                                 ->join('master_list_sub_module','master_list_sub_module.id','=','_sub_modules.sub_module_id')
    //                                 ->select('master_list_sub_module.module_id as module_id','_sub_modules.sub_module_name as sub_module_name','master_list_sub_module.id as sub_module_id','master_list_sub_module.path as sub_module_url','master_list_sub_module.image_name as sub_module_icon_image')
    //                                 ->where('_sub_modules.company_id',$company_id)
    //                                 ->where('_sub_modules.status',1)
    //                                 ->where('master_list_sub_module.status',1)
    //                                 ->orderBy('_sub_modules.module_sequence','ASC')
    //                                 ->get();
    //                     $sub_module_arr = array();
    //                     foreach ($sub_module as $key => $value)
    //                     {
    //                         $sub_module_arr[$key]['sub_module_icon_image'] = !empty($value->sub_module_icon_image)?$value->sub_module_icon_image:'';
    //                         $sub_module_arr[$key]['module_id'] = "$value->module_id";
    //                         $sub_module_arr[$key]['sub_module_id'] = "$value->sub_module_id";
    //                         $sub_module_arr[$key]['sub_module_name'] = !empty($value->sub_module_name)?$value->sub_module_name:'';
    //                         $sub_module_arr[$key]['sub_module_url'] = !empty($value->sub_module_url)?$value->sub_module_url:'';
    //                     }
    //                     $other_module = DB::table('app_other_module_assign')
    //                             ->join('master_other_app_module','master_other_app_module.id','=','app_other_module_assign.module_id')
    //                             ->select('master_other_app_module.image_name as other_module_icon_image','app_other_module_assign.title_name as other_module_name','app_other_module_assign.module_id as other_module_id')
    //                             ->where('app_other_module_assign.status',1)
    //                             ->where('app_other_module_assign.company_id',$company_id)
    //                             ->where('master_other_app_module.id','!=',2) // beacaue this id belongs to retailer creation with otp so this condition true above the role wise module condition starts
    //                             ->where('master_other_app_module.status',1)
    //                             ->orderBy('app_other_module_assign.module_sequence','ASC')
    //                             ->get();
    //                     $other_module_arr = array();
    //                     foreach ($other_module as $key => $value)
    //                     {
    //                         $other_module_arr[$key]['other_module_icon_image'] = !empty($value->other_module_icon_image)?$value->other_module_icon_image:'';
    //                         $other_module_arr[$key]['other_module_id'] = "$value->other_module_id";
    //                         $other_module_arr[$key]['other_module_name'] = !empty($value->other_module_name)?$value->other_module_name:'';
    //                     }
    //                 }

                    

                    

    //             #.................................state and town array on the behalf of distributor assign on user starts here .........##

    //                 $state_array = array();
    //                 $town_array = array();
    //                 $state_array = DB::table('location_3')
    //                             ->join('dealer','dealer.state_id','=','location_3.id')
    //                             ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
    //                             ->select('location_3.name as state_name',DB::raw("convert(location_3.id,CHAR) as l3_id"))
    //                             ->where('dealer_status',1)
    //                             ->where('location_3.status',1)
    //                             ->where('location_3.company_id',$company_id)
    //                             ->where('user_id',$user_id)
    //                             ->where('dealer.company_id',$company_id)
    //                             ->groupBy('location_3.id')
    //                             ->get();

    //                 $town_arr = array();
    //                 $town_array = array();
    //                 $town_array_data = DB::table('location_7')
    //                             ->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','location_7.id')
    //                             ->join('location_6','location_6.id','=','location_7.location_6_id')
    //                             ->join('location_5','location_5.id','=','location_6.location_5_id')
    //                             ->join('location_4','location_4.id','=','location_5.location_4_id')
    //                             ->join('location_3','location_3.id','=','location_4.location_3_id')
                                
    //                             ->select('location_6.name as town_name','location_6.id as l6_id','location_3.id as l3_id')
    //                             ->where('location_6.company_id',$company_id)
                            
                                
    //                             ->where('location_6.status',1)
    //                             ->where('user_id',$user_id)
                            
    //                             ->groupBy('location_6.id','location_3.id')
    //                             ->get();
    //                 foreach($town_array_data as $t_key => $t_value)
    //                 {
    //                     $town_arr['l4_id'] = "$t_value->l6_id";
    //                     $town_arr['town_name'] = $t_value->town_name;
    //                     $town_arr['l3_id'] = "$t_value->l3_id";
    //                     $town_array[] = $town_arr;
    //                 }
    //             #......................................Product overall data return starts here .........................................##
    //                 $product_array = DB::table('catalog_product')
    //                                 ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
    //                                 ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
    //                                 ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
    //                                 ->select('catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','unit','catalog_product.name','product_rate_list.retailer_pcs_rate as base_price','product_rate_list.mrp_pcs as mrp', 'product_rate_list.mrp_pcs','hsn_code','catalog_2.name as cname', 'product_rate_list.dealer_rate as dealer_rate','product_rate_list.dealer_pcs_rate as dealer_pcs_rate')
    //                                 ->where('catalog_1.status',1)
    //                                 ->where('catalog_2.status',1)
    //                                 ->where('catalog_product.status',1)
    //                                 ->where('state_id',$state_id)
    //                                 ->where('catalog_product.company_id',$company_id)
    //                                 ->get()->toArray();
    //                 $final_catalog_product_details = array();
    //                 foreach ($product_array as $key => $value)
    //                 {
    //                     $focus_query = DB::table('focus')
    //                                 ->select('product_id')
    //                                 ->where('company_id',$company_id)
    //                                 ->where('product_id',$value->id)
    //                                 ->get();
    //                     if(COUNT($focus_query)>0)
    //                     {
    //                         $focus_status = 1;
    //                     }
    //                     else
    //                     {
    //                         $focus_status = 0;
    //                     }
    //                     $focust_query = DB::table('focus_product_users_target')
    //                                 ->select('target_value as target_qty')
    //                                 ->where('company_id',$company_id)
    //                                 ->where('product_id',$value->id)
    //                                 ->where('user_id',$user_id)
    //                                 ->whereRaw("DATE_FORMAT(start_date,'%Y-%m-%d')>='date(Y-m-d)' AND DATE_FORMAT(end_date,'%Y-%m-%d')<='date(Y-m-d)'")
    //                                 ->first();

    //                     $querytax = DB::table('_gst')
    //                                 ->select('igst as tax')
    //                                 ->where('company_id',$company_id)
    //                                 ->where('hsn_code',$value->hsn_code)
    //                                 ->first();

    //                     $productArray['id'] = "$value->id";
    //                     $productArray['classification_id'] = "$value->classification_id";
    //                     $productArray['classification_name'] = $value->classification_name;
    //                     $productArray['category'] = "$value->catalog_id";
    //                     $productArray['hsn_code'] = $value->hsn_code;
    //                     $productArray['category_name'] = $value->cname;
    //                     $productArray['name'] = $value->name;
    //                     $productArray['weight'] = $value->weight;
    //                     $productArray['base_price'] = $value->base_price;
    //                     $productArray['dealer_rate'] = $value->dealer_rate;
    //                     $productArray['dealer_pcs_rate'] = $value->dealer_pcs_rate;
    //                     $productArray['mrp'] = $value->mrp;
    //                     $productArray['pcs_mrp'] = $value->mrp_pcs;
    //                     $productArray['unit'] = $value->unit;
    //                     $productArray['focus'] = "$focus_status";
    //                     $productArray['focus_target'] = !empty($focust_query->target_qty)?$focust_query->target_qty:'';
    //                     $productArray['tax'] = !empty($querytax->tax)?$querytax->tax:'';
    //                     $final_catalog_product_details[] = $productArray;

    //                 }

    //             #........................................product classification starts here ............................................##
    //                 $product_classification_query = DB::table('catalog_1')
    //                                             ->join('catalog_view','catalog_view.c1_id','=','catalog_1.id')
    //                                             ->select('catalog_1.id as id','catalog_1.name as name')
    //                                             ->where('catalog_1.company_id',$company_id)
    //                                             ->where('catalog_1.status',1)
    //                                             ->groupBy('c1_id')
    //                                             ->get()->toArray();
    //                 $final_product_classification_details = array();
    //                 foreach ($product_classification_query as $key => $value)
    //                 {
    //                     $classification_array['id']= "$value->id";
    //                     $classification_array['name']= $value->name;
    //                     $final_product_classification_details[] = $classification_array;
    //                 }
    //             #..........................................cataegory part starts here ..................................................##
    //                 $category_data = DB::table('catalog_2')
    //                         ->join('catalog_view','catalog_view.c2_id','=','catalog_2.id')
    //                         ->select('id','name', 'catalog_view.c1_id as classification_id', 'catalog_view.c1_name as classification_name')
    //                         ->where('catalog_2.company_id',$company_id)
    //                         ->where('catalog_2.status',1)
    //                         ->groupBy('c2_id')
    //                         ->get()->toArray();
    //                 $final_category_array = array();
    //                 foreach ($category_data as $key => $value)
    //                 {
    //                     $category_array['id'] = "$value->id";
    //                     $category_array['classification_id'] = "$value->classification_id";
    //                     $category_array['classification_name'] = $value->classification_name;
    //                     $category_array['name'] = $value->name;
    //                     $final_category_array[] = $category_array;
    //                 }
    //             #......................................... non productive reason starts here ...........................................##
    //                 $non_productive_reason_query  = DB::table('_no_sale_reason')
    //                                             ->select('id','name')
    //                                             ->where('company_id',$company_id)
    //                                             ->where('status',1)
    //                                             ->get();
    //                 $final_non_productive_query = array();
    //                 foreach ($non_productive_reason_query as $key => $value)
    //                 {
    //                     $non_productive_array['id'] = "$value->id";
    //                     $non_productive_array['name'] = $value->name;
    //                     $final_non_productive_query[] = $non_productive_array;
    //                 }
    //             #..........................................Daily schedule starts here ..................................................##
    //                 $daily_schedule_query = DB::table('_daily_schedule')
    //                                     ->select('id','name')
    //                                     ->where('company_id',$company_id)
    //                                     ->where('status',1)
    //                                     ->orderBy('id','ASC')
    //                                     ->get();
    //                 $daily_schedule_details = array();
    //                 foreach ($daily_schedule_query as $key => $value)
    //                 {
    //                     $daily_schedule_array['id'] = "$value->id";
    //                     $daily_schedule_array['name'] = $value->name;
    //                     $daily_schedule_details[] = $daily_schedule_array;  
    //                 }
    //             #.....................................task of the day starts here ......................................................##
    //                 $task_query = DB::table('_task_of_the_day')
    //                             ->where('company_id',$company_id)
    //                             ->where('status',1)
    //                             ->get();
    //                 $task = array();
    //                 foreach ($task_query as $key => $value)
    //                 {
    //                     $task_array['id'] = "$value->id";
    //                     $task_array['name'] = $value->task;
    //                     $task[] = $task_array;
    //                 }
    //             #/.........................................mtp starts here .............................................................##
                    
    //                     $date = date('Y-m');
    //                     $mtp_query = DB::table('monthly_tour_program')
    //                                 ->select('rd','total_sales','working_date','locations','task_of_the_day')
    //                                 ->where('company_id',$company_id)
    //                                 ->where('person_id',$user_id)
    //                                 ->whereRaw("DATE_FORMAT(working_date,'%Y-%m')='$date'")
    //                                 ->get();
    //                     $mtp_array = array();
    //                     foreach ($mtp_query as $key => $value)
    //                     {
    //                         $beat_data = DB::table('location_7')->where('id',$value->locations)->first();
    //                         $data['total_sale'] = $value->total_sales;
    //                         $data['rd'] = $value->rd;
    //                         $data['date'] = $value->working_date;
    //                         $data['today'] = !empty($beat_data->name)?$beat_data->name:'';
    //                         $data['today_id'] = $value->locations;
    //                         $mtp_array[] = $data;
    //                     }
    //             #.............................................travelling mode starts here ..............................................##

    //                     $travelling_mode = DB::table('_travelling_mode')
    //                                     ->select('id','mode')
    //                                     ->where('company_id',$company_id)
    //                                     ->where('status',1)
    //                                     ->get();
    //                     $travel_array = array();
    //                     foreach ($travelling_mode as $key => $value)
    //                     {
    //                         $data_t['id'] = "$value->id";
    //                         $data_t['mode'] = $value->mode;
    //                         $travel_array[] = $data_t;
    //                     }
    //             #.............................................mtd target acheivement starts here........................................##
    //                     $current_date = date('Y-m-d');
    //                     $current_month = date('Y-m');
    //                     $mtd_target = '';
    //                     $mtd_achievement = '';
    //                     $mtd_target_query  = DB::table('user_sales_order')
    //                                         ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$current_date'")
    //                                         ->where('company_id',$company_id)
    //                                         ->where('user_id',$user_id)
    //                                         ->SUM('amount');

    //                     $mtd_second_query = DB::table('monthly_tour_program')
    //                                     ->whereRaw("DATE_FORMAT(working_date,'%Y-%m')='$current_month'")
    //                                     ->where('company_id',$company_id)
    //                                     ->where('person_id',$user_id)                                        
    //                                     ->SUM('rd');
                        
    //                     if(!empty($mtd_target_query) && !empty($mtd_second_query))
    //                     {
    //                         $percentage_ratio=($mtd_target_query/$mtd_second_query)*100;
    //                     }
    //                     else
    //                     {
    //                         $mtd_target=!empty($mtd_second_query)?$mtd_second_query:0;
    //                         $mtd_achievement=!empty($mtd_target_query)?$mtd_target_query:0;
    //                     }
    //             #......................................payment modes starts here ................................................................................................##
    //             $payment_modes  = DB::table('_payment_modes')
    //                             ->where('status',1)
    //                             ->get();

    //             #......................................outlet type  starts here ................................................................................................##
    //             $retailer_outelet_types  = DB::table('_retailer_outlet_type')
    //                             ->where('status',1)
    //                             ->where('company_id',$company_id)
    //                             ->orderBy('sequence','ASC')
    //                             ->get();
    //             #......................................outlet category modes starts here ................................................................................................##
    //             $retailer_outelet_category  = DB::table('_retailer_outlet_category')
    //                             ->where('status',1)
    //                             ->where('company_id',$company_id)
    //                             ->orderBy('sequence','ASC')
    //                             ->get();
    //             #......................................schedule type starts here ................................................................................................##
    //             $daily_schedule  = DB::table('_daily_schedule')
    //                             ->where('status',1)
    //                             ->where('company_id',$company_id)
    //                             ->orderBy('sequence','ASC')
    //                             ->get();
    //             #......................................return type starts here ................................................................................................##
    //             $return_type  = DB::table('_return_type_damage')
    //                             ->where('status',1)
    //                             ->where('company_id',$company_id)
    //                             ->get();
    //             #......................................no sale reason starts here ................................................................................................##
    //             $reason_type  = DB::table('_no_sale_reason')
    //                             ->where('status',1)
    //                             ->orderBy('sequence','ASC')
    //                             ->where('company_id',$company_id)
    //                             ->get();
    //             $meeting_type = array();
    //             $meeting_type  = DB::table('_meeting_type')
    //                             ->where('status',1)
    //                             ->orderBy('sequence','ASC')
    //                             ->where('company_id',$company_id)
    //                             ->get();
    //             $mtp_status_array = DB::table('_mtp_status')
    //                             ->where('status',1)
    //                             ->orderBy('sequence','ASC')
    //                             ->where('company_id',$company_id)
    //                             ->get();
    //             $mtp_not_interested_array = DB::table('_mtp_not_interested')
    //                             ->where('status',1)
    //                             ->orderBy('sequence','ASC')
    //                             ->where('company_id',$company_id)
    //                             ->get();
    //             $not_contacted_reason = DB::table('_not_contacted_reason')
    //                                 ->where('status',1)
    //                                 ->orderBy('sequence','ASC')
    //                                 ->where('company_id',$company_id)
    //                                 ->get();
    //             $leave_type = DB::table('_leave_type')
    //                         ->where('status',1)
    //                         ->orderBy('sequence','ASC')
    //                         ->where('company_id',$company_id)
    //                         ->get();

    //             #......................................reponse parameters starts here ..................................................##
    //                 return response()->json([
    //                     'response' =>True,

    //                     'url_list'=>$url_list,
    //                     'url_details'=>$url_details,
    //                     'company_id'=>$company_id,
    //                     'app_module'=> $module,
    //                     'sub_module'=> $sub_module_arr,
    //                     'other_module_arr'=> $other_module_arr,
    //                     'retailer_with_otp_creation_array'=> $retailer_with_otp_creation_array,
    //                     'user_details'=>!empty($user_personal_data)?$user_personal_data:array(), // user data
    //                     'dealer'=>!empty($final_dealer_data)?$final_dealer_data:array(), // dealer data
    //                     'beat'=>!empty($final_data_beat)?$final_data_beat:array(), // beat data (location_5)
    //                     'retailer'=>!empty($final_retailer)?$final_retailer:array(), // retailer all above response data dependend on each other
    //                     'colleague' => $collegueArr,
    //                     'working_status'=>$working_status,
    //                     'state_array'=>$state_array,
    //                     'town_array'=>$town_array,
    //                     'product'=>$final_catalog_product_details,
    //                     'product_classification'=>$product_classification_query,
    //                     'category' => $final_category_array,
    //                     'non_productive_reason'=> $final_non_productive_query,
    //                     'daily_schedule' => $daily_schedule_details,
    //                     'task_of_the_day'=>$task,
    //                     'mtp'=>$mtp_array,
    //                     'travelling_modes'=>$travel_array,
    //                     'mtd_target'=>$mtd_target,
    //                     'mtd_achievement'=>$mtd_achievement,
    //                     'payment_modes'=> $payment_modes,
    //                     'retailer_outelet_types'=>$retailer_outelet_types,
    //                     'retailer_outelet_category'=> $retailer_outelet_category,
    //                     // 'daily_schedule'=> $daily_schedule,
    //                     'return_type' => $return_type,
    //                     'reason_type'=> $reason_type,
    //                     'meeting_type'=> $meeting_type,
    //                     'other_module_arr'=> $other_module_arr,
    //                     'area_of_work'=>$area_of_work,
    //                     'working_activity'=>$working_activity,
    //                     'working_with_type'=>$working_with_type,
    //                     'mtp_status_array'=> $mtp_status_array,
    //                     'mtp_not_interested_array'=> $mtp_not_interested_array,
    //                     'not_contacted_reason'=> $not_contacted_reason,
    //                     'leave_type'=> $leave_type,

    //                     // 'location_master_details'=> $location_master_details,
    //                     'message'=>'Success!!']);
    //             #......................................reponse parameters ends here ..................................................##

                   
                     
    //         } // person_query !empty ends here
    //         else
    //         {
    //             return response()->json([ 'response' =>False,'message'=>'!!User Data Record Not Found!!']);        
    //         }
    //     } // company id !empty ends here
    //     else
    //     {
    //         return response()->json([ 'response' =>False,'message'=>'!!Company Id Not Found!!']);        
    //     }
            


    // }
    # xotic login ends
    
    # this function for only update the data on attendance or other page
    public function update_attendance_page(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'company_id'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
        }
        $company_id = $request->company_id;
        $user_id = $request->user_id;
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
        if(empty($junior_data))
        {
            $junior_data[]=$user_id;
             $out=[0=>['id'=>'0','name'=>'SELF']];
        }
        else
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
        return response()->json([ 'response' =>True,'working_status'=>$working_status,'colleague'=>$collegueArr]);        
        
    }
}