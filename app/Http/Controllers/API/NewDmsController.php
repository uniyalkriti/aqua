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
use App\TableReturn;
use App\Dealer;
use App\JuniorData;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Session;
use DateTime;
use Lang;

class NewDmsController extends Controller
{
	public function company_login_dms(Request $request) 
	{	
		$validator=Validator::make($request->all(),[
            'uname' => 'required',
       
            'v_name' => 'required',
            'v_code' => 'required',
            'pass' => 'required',
            // 'company_id' => 'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $uname = $request->uname;
        $pass = $request->pass;
        $imei = $request->imei;
        $v_code = $request->v_code;
        $v_name = $request->v_name;
        $token = !empty($request->token)?$request->token:'0';
        $current_date = date('Y-m-d');
        $table_name = TableReturn::table_return($current_date,$current_date);
        $rate_list_flag = 1;

          if(!empty($request->imei))
        {
             $check = DB::table('person')->join('person_login','person_login.person_id','=','person.id')
                        ->where('person_username',$uname)
                        ->where('person_status',1)
                        ->whereNull('person.imei_number')
                        ->first();

                        if(isset($check))
                        {
                            // dd($check);
                            $update_person_query = DB::table('person')->join('person_login','person_login.person_id','=','person.id')
                                            ->where('person_username',$uname)
                                            ->update(['imei_number'=>$request->imei]);
                        }
                          else
                        {
                            $check_imei = DB::table('person')->join('person_login','person_login.person_id','=','person.id')
                            ->where('person_username',$uname)
                            ->where('person_status',1)
                            ->where('imei_number',$request->imei)
                            ->first();

                            if(empty($check_imei))
                            {
                                return response()->json([ 'response' =>False,'message'=>'IMEI already exist !!']);
                                
                            }
                        }

        }

           
        $person_query = Person::where('person_username',$uname)
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->join('location_3','location_3.id','=','person.state_id')
                    ->join('person_details','person_details.person_id','=','person.id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->join('company','company.id','=','person.company_id')
                    ->select('person.company_id as c_company_id','person.state_id as state_id','is_mtp_enabled','person_username','rate_list_flag','person.id as user_id','mobile','imei_number','person.email as user_email','rolename as designation','person.role_id as designation_id','emp_code','person_details.address as user_address','head_quar as head_quater','person_details.created_on as user_created_date',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"))
                    ->where('person_password',DB::raw("AES_ENCRYPT('".trim($pass)."', '".Lang::get('common.db_salt')."')"))
                    ->where('person_status',1)
                    ->where('person_id_senior','!=',0)
                    ->get();
        $company_id = !empty($person_query[0]->c_company_id)?$person_query[0]->c_company_id:"0";
        if(COUNT($person_query)>0)
        {
            foreach ($person_query as $key => $value)
            {
                
                $user_personal_data['is_mtp_enabled'] = $value->is_mtp_enabled;
                $user_personal_data['user_id'] = $value->user_id;
                $user_personal_data['person_username'] = $value->person_username;
                $user_personal_data['mobile'] = $value->mobile;
                $user_personal_data['imei_number'] = !empty($value->imei_number)?$value->imei_number:'';
                $user_personal_data['user_email'] = $value->user_email;
                $user_personal_data['designation_id'] = $value->designation_id;
                $user_personal_data['designation'] = $value->designation;
                $user_personal_data['emp_code'] = $value->emp_code;
                $user_personal_data['user_address'] = $value->user_address;
                $user_personal_data['state_id'] = $value->state_id;
                $user_personal_data['user_created_date'] = $value->user_created_date;
                $user_personal_data['emp_name'] = $value->user_name;
                $company_id = $value->c_company_id;

            }
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

           
            $user_id = $person_query[0]->user_id; // return user id
            $check_role_id = $person_query[0]->designation_id; // return user id
            $rate_list_flag = !empty($person_query[0]->rate_list_flag)?$person_query[0]->rate_list_flag:'0'; // return user id
            $state_id = $person_query[0]->state_id; // return user id
            $myArr=['version_code_name'=>"Version: $v_name/$v_code",'dms_token'=>$token];
            $update_query = DB::table('person')->where('id',$user_id)->update($myArr);
          
            #............................................ location master ends here ................................................##
            
             $schemePlanDealer = DB::table('scheme_assign_dealer')
                                ->where('scheme_assign_dealer.company_id',$company_id)
                                ->whereRaw("date_format(plan_assigned_from_date,'%Y-%m-%d')<='$current_date' AND date_format(plan_assigned_to_date,'%Y-%m-%d')>='$current_date'")
                                ->groupBy('dealer_id')
                                ->pluck(DB::raw("GROUP_CONCAT(DISTINCT plan_id) as plans"),'dealer_id')->toArray();
                                
            	$user_dealer_retailer_query = DB::table('dealer')
                                               ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                                               ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
                                               ->select('dealer.name as name','dealer.id as dealer_id','l6_name as lname','l6_id as lid','csa_id','dealer.contact_person','dealer.other_numbers','dealer.landline','dealer.address')
                                               ->where('dealer_location_rate_list.user_id',$user_id)
                                               ->where('dealer.dealer_status',1)
                                               ->where('dealer.company_id',$company_id)
                                               ->where('dealer_location_rate_list.company_id',$company_id)
                                               ->groupBy('dealer.id')
                                               ->get();
                $dealer_id = array();
                $final_dealer_data = array();
                $schemePlanId = array();

                foreach ($user_dealer_retailer_query as $key => $value)
                {
                    
                     $schemeDealerData = !empty($schemePlanDealer[$value->dealer_id])?explode(',',$schemePlanDealer[$value->dealer_id]):array();

                    if(!empty($schemeDealerData)){
                        foreach ($schemeDealerData as $sdkey => $sdvalue) {
                            $schemePlanId[$sdvalue] = $sdvalue;
                        }
                    }
                    
                    $dealer_id[]=$value->dealer_id;
                    $csa_id[]=$value->csa_id;
                    $dealer_data_string['dealer_id'] = "$value->dealer_id"; // return the data in string
                    $dealer_data_string['lid'] = "$value->lid"; // return the data in string
                    $dealer_data_string['lname'] = $value->lname;
                    $dealer_data_string['name'] = $value->name;
                    
                    $dealer_data_string['contact_person'] = !empty($value->contact_person)?$value->contact_person:'';
                    $dealer_data_string['contact_number'] = !empty($value->other_numbers)?$value->other_numbers:$value->landline;
                    $dealer_data_string['address'] = !empty($value->address)?$value->address:'';
                    
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
                        $beat_data_string['id'] = "$value->beat_id"; // return the data in string
                        $beat_data_string['dealer_id'] = "$value->dealer_id"; // return the data in string
                        $beat_data_string['name'] = "$value->name"; // return the data in string
                        $final_data_beat[] = $beat_data_string; // merge all data in one array
                    }
                ##................................ return the retailer details on the behalf of beat id  ......................##   
                    
                    // 
                    
                    $schemePlanRetailer = DB::table('scheme_assign_retailer')
                                ->where('scheme_assign_retailer.company_id',$company_id)
                                ->whereRaw("date_format(plan_assigned_from_date,'%Y-%m-%d')<='$current_date' AND date_format(plan_assigned_to_date,'%Y-%m-%d')>='$current_date'")
                                ->groupBy('retailer_id')
                                ->pluck(DB::raw("GROUP_CONCAT(DISTINCT plan_id) as plans"),'retailer_id')->toArray();
                                

                    $date = date('Y-m-d');
                    $sending_array = array();
                    $currmonth = date('Y-m');
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

                    // dd($check_retailer_sales);
                    $data = DB::table('retailer')
                            ->join('location_7','location_7.id','=','retailer.location_id')
                            ->select('retailer.id','retailer.name','retailer.contact_per_name','retailer.other_numbers','retailer.address','location_7.id as beat_id','location_7.name as beat_name')
                            ->whereIn('retailer.location_id',$beat_id)
                            ->where('retailer.company_id',$company_id)
                            ->where('retailer_status',1)
                            ->groupBy('retailer.id')
                            ->get();

                    $retailerArray = array();
                    foreach ($data as $key => $value) {
                        $retailer_id = $value->id; 
                        
                        $schemeRetailerData = !empty($schemePlanRetailer[$retailer_id])?explode(',',$schemePlanRetailer[$retailer_id]):array();

                        if(!empty($schemeRetailerData)){
                            foreach ($schemeRetailerData as $rdkey => $rdvalue) {
                                $schemePlanId[$rdvalue] = $rdvalue;
                            }
                        }
                        

                        $retailerArray[] = $value->id; 


                        if(empty($check_retailer_productive_sales[$retailer_id]) && empty($check_retailer_non_productive_sales[$retailer_id])){
                            $sales_status = '0'; // for not sale anything
                        }elseif (!empty($check_retailer_productive_sales[$retailer_id])) {
                            $sales_status = '1'; // for productive sale
                        }elseif (!empty($check_retailer_non_productive_sales[$retailer_id])) {
                            $sales_status = '2'; // for visit only
                        }

                        // $lastSaleId = DB::table('user_sales_order')
                        //             ->select('user_sales_order.order_id')
                        //             ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        //             ->where('retailer_id',$retailer_id)
                        //             ->where('user_sales_order.company_id',$company_id)
                        //             ->groupBy('retailer_id')
                        //             ->orderBy('user_sales_order.id','DESC')
                        //             ->first();

                        $lastDetails = array();
                        // if(!empty($lastSaleId)){
                        // $lastDetails = DB::table('user_sales_order')
                        //             ->select(DB::raw("concat_ws(' ',first_name,middle_name,last_name) as user_name"),DB::raw("SUM(user_sales_order_details.rate*quantity) as lastSale"),'date','time')
                        //             ->join('person','person.id','=','user_sales_order.user_id')
                        //             ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        //             ->where('user_sales_order.order_id',$lastSaleId->order_id)
                        //             ->where('user_sales_order.company_id',$company_id)
                        //             ->groupBy('user_sales_order.order_id')
                        //             ->first();
                        // }

                        $lastDate = !empty($lastDetails->date)?$lastDetails->date:'';
                        $lastTime = !empty($lastDetails->time)?$lastDetails->time:''; 


                         // $lastMonthDetails = DB::table('user_sales_order')
                         //            ->select(DB::raw("SUM(user_sales_order_details.rate*quantity) as Sale"),DB::raw("SUM(user_sales_order_details.quantity) as Quantity"))
                         //            ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                         //            ->where('user_sales_order.retailer_id',$retailer_id)
                         //            ->where('user_sales_order.company_id',$company_id)
                         //            ->whereRaw("date_format(date,'%Y-%m')='$currmonth'")
                         //            ->groupBy('user_sales_order.order_id')
                         //            ->first();

                        $monthBill = !empty($lastMonthDetails->Sale)?$lastMonthDetails->Sale:'';
                        $monthBillBox = !empty($lastMonthDetails->Quantity)?$lastMonthDetails->Quantity:'';
                       // $sales_status = !empty($check_retailer_sales[$retailer_id])?'1':'0'; // 1 for sales done on retailer 0 for not


                        $final_array_retailser['id'] = $value->id; 
                        $final_array_retailser['name'] = $value->name; 
                         $final_array_retailser['contact_per_name'] = $value->contact_per_name; 
                        $final_array_retailser['contact_number'] = $value->other_numbers; 
                        $final_array_retailser['address'] = $value->address; 
                        $final_array_retailser['sale_status'] = $sales_status; 

                        $final_array_retailser['beat_id'] = $value->beat_id; 
                        $final_array_retailser['beat_name'] = $value->beat_name; 



                        $final_array_retailser['last_invoice_date'] = $lastDate.' '.$lastTime; 
                        $final_array_retailser['last_bill_value'] = !empty($lastDetails->lastSale)?$lastDetails->lastSale:''; 
                        $final_array_retailser['sale_person'] = !empty($lastDetails->user_name)?$lastDetails->user_name:''; 

                        $final_array_retailser['monthly_bill_value'] = $monthBill; 
                        $final_array_retailser['total_bill_box'] = $monthBillBox; 
                        $final_array_retailser['total_gift_deliever'] = '0'; 

                        $sending_array_retailer[] = $final_array_retailser;
                    }
                    // 


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

                $productType = DB::table('product_type')->where('company_id',$company_id)->pluck('name','id')->toArray();


                #......................................Product overall data return starts here .........................................##
                    $product_array = DB::table('catalog_product')
                        ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                        ->select('catalog_2.*')
                        ->where('catalog_1.status',1)
                        ->where('catalog_2.status',1)
                        ->where('catalog_product.status',1)
                        ->where('state_id',$state_id)
                        ->where('catalog_product.company_id',$company_id)
                        ->groupBy('catalog_2.id')
                        ->orderBy('catalog_product.product_sequence','ASC')
                        ->get()->toArray();
                    foreach ($product_array as $key => $value) 
                    {
                        $first_layer['id']=$value->id;
                        $first_layer['name']=$value->name;
                        $query_details_array = [];
                        $query_details=DB::table('catalog_product')
                                        ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
                                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                                        ->select('catalog_product.brand_details','catalog_product.description_hind','catalog_product.description_eng','catalog_product.image_name as image_name','product_id','state_id','ss_id','distributor_id','product_type_primary','other_retailer_rate','other_dealer_rate','quantiy_per_other_type','product_type_id','retailer_rate as retailer_case_rate','catalog_product.product_type as product_type','catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','catalog_product.quantity_per_case as quantity_per_case','unit','catalog_product.name as product_name','product_rate_list.retailer_pcs_rate as base_price','product_rate_list.mrp_pcs as mrp', 'product_rate_list.mrp_pcs','hsn_code','catalog_2.name as category_name', 'product_rate_list.dealer_rate as dealer_rate','product_rate_list.dealer_pcs_rate as dealer_pcs_rate','catalog_product.gst_percent as gst','video_name','ingredriants_details','final_product_type')
                                        ->where('catalog_1.status',1)
                                        ->where('catalog_2.status',1)
                                        ->where('catalog_product.status',1)
                                        ->where('state_id',$state_id)
                                        ->where('catalog_id',$value->id)
                                        ->where('catalog_product.company_id',$company_id)
                                        ->groupBy('catalog_product.id')
                                        ->orderBy('catalog_product.product_sequence','ASC')
                                        ->get()->toArray();

                        if(!empty($query_details)){
                            foreach ($query_details as $q_key => $q_value) {
                                // code...

                                if($q_value->catalog_id == '1984'){
                                    $catgol = '1';
                                }else{
                                    $catgol = '0';
                                }


                                $out_query['brand_details'] = !empty($q_value->brand_details)?$q_value->brand_details:'';
                                $out_query['description_hind'] = !empty($q_value->description_hind)?$q_value->description_hind:'';
                                $out_query['description_eng'] = !empty($q_value->description_eng)?$q_value->description_eng:'';
                                $out_query['image_name'] = !empty($q_value->image_name)?$q_value->image_name:'';
                                $out_query['video_name'] = !empty($q_value->video_name)?$q_value->video_name:'';
                                $out_query['product_id'] = $q_value->product_id;
                                $out_query['state_id'] = $q_value->state_id;
                                $out_query['ss_id'] = $q_value->ss_id;
                                $out_query['distributor_id'] = $q_value->distributor_id;
                                $out_query['product_type_primary'] = $q_value->product_type_primary;
                                $out_query['other_retailer_rate'] = $q_value->other_retailer_rate;
                                $out_query['other_dealer_rate'] = $q_value->other_dealer_rate;
                                $out_query['quantiy_per_other_type'] = $q_value->quantiy_per_other_type;
                                $out_query['product_type_id'] = $q_value->product_type_id;
                                $out_query['retailer_case_rate'] = $q_value->retailer_case_rate;
                                $out_query['product_type'] = $q_value->product_type;
                                $out_query['id'] = $q_value->id;
                                $out_query['weight'] = $q_value->weight;
                                $out_query['classification_id'] = $q_value->classification_id;
                                $out_query['classification_name'] = $q_value->classification_name;
                                $out_query['catalog_id'] = $q_value->catalog_id;
                                $out_query['is_golden'] = $catgol;
                                $out_query['quantity_per_case'] = $q_value->quantity_per_case;
                                // $out_query['unit'] = $q_value->unit; // change by dheeru
                                $out_query['unit'] = $q_value->quantity_per_case;
                                $out_query['product_name'] = $q_value->product_name;
                                $out_query['base_price'] = $q_value->base_price;
                                $out_query['mrp'] = $q_value->mrp;
                                $out_query['mrp_pcs'] = $q_value->mrp_pcs;
                                $out_query['hsn_code'] = $q_value->hsn_code;
                                $out_query['category_name'] = $q_value->category_name;
                                $out_query['dealer_rate'] = $q_value->dealer_rate;
                                $out_query['dealer_pcs_rate'] = $q_value->dealer_pcs_rate;
                                $out_query['gst'] = $q_value->gst;
                                $out_query['pack_name'] = !empty($productType[$q_value->final_product_type])?$productType[$q_value->final_product_type]:'';

                                $out_query['ingredriants_details'] = !empty($q_value->ingredriants_details)?$q_value->ingredriants_details:'';


                                $query_details_array[] = $out_query;

                            }
                        }
                        $first_layer['catalog_product_details'] = $query_details_array;
                        $final_array_details[] = $first_layer;
                    }
                    // foreach ($product_array as $key => $value) 
                    // {
                    //     $first_layer['id']=$value->id;
                    //     $first_layer['name']=$value->name;
                    //     $first_layer['catalog_product_details']=DB::table('catalog_product')
                    //                                         ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
                    //                                         ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                    //                                         ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                    //                                         ->select('catalog_product.brand_details','catalog_product.description_hind','catalog_product.description_eng','catalog_product.image_name as image_name','product_id','state_id','ss_id','distributor_id','product_type_primary','other_retailer_rate','other_dealer_rate','quantiy_per_other_type','product_type_id','retailer_rate as retailer_case_rate','catalog_product.product_type as product_type','catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','catalog_product.quantity_per_case as quantity_per_case','unit','catalog_product.name as product_name','product_rate_list.retailer_pcs_rate as base_price','product_rate_list.mrp_pcs as mrp', 'product_rate_list.mrp_pcs','hsn_code','catalog_2.name as category_name', 'product_rate_list.dealer_rate as dealer_rate','product_rate_list.dealer_pcs_rate as dealer_pcs_rate','catalog_product.gst_percent as gst')
                    //                                         ->where('catalog_1.status',1)
                    //                                         ->where('catalog_2.status',1)
                    //                                         ->where('catalog_product.status',1)
                    //                                         ->where('state_id',$state_id)
                    //                                         ->where('catalog_id',$value->id)
                    //                                         ->where('catalog_product.company_id',$company_id)
                    //                                         ->groupBy('product_id')
                    //                                         ->orderBy('catalog_product.product_sequence','ASC')
                    //                                         ->get()->toArray();
                    //     $final_array_details[] = $first_layer;
                    // }   

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
                        $module[$key]['module_icon_image'] = !empty($value->module_icon_image)? str_replace('app_icon_image/20201109165936.png','app_icon_image/20201130232736.jpg', $value->module_icon_image):'';
                        // $module[$key]['module_icon_image'] = !empty($value->module_icon_image)?$value->module_icon_image:'';
                        $module[$key]['module_id'] = "$value->module_id";
                        $module[$key]['module_name'] = !empty($value->module_name)?str_replace('Outlet Visit', 'Secondary Sale', $value->module_name):'';
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
                        $module[$key]['module_icon_image'] = !empty($value->module_icon_image)? str_replace('app_icon_image/20201109165936.png','app_icon_image/20201130232736.jpg', $value->module_icon_image):'';
                        $module[$key]['module_id'] = "$value->module_id";
                        $module[$key]['module_name'] = !empty($value->module_name)?str_replace('Outlet Visit', 'Secondary Sale', $value->module_name):'';
                        // $module[$key]['module_name'] = !empty($valu/e->module_name)?$value->module_name:'';
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

                $company_id = !empty($company_id)?$company_id:'';
                $working_status_adata = DB::table('_working_status')->where('status',1)->where('company_id',$company_id)->get();

            #......................................scheme data starts here ..................................................##
            $schemedate = date('Y-m-d');

            $scheme_details_dealer = DB::table('scheme_plan_details')
                            ->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
                            ->join('scheme_assign_dealer','scheme_assign_dealer.plan_id','=','scheme_plan.id')
                            ->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id','dealer_id')
                            ->where('scheme_plan_details.company_id',$company_id)
                                ->whereRaw("date_format(plan_assigned_from_date,'%Y-%m-%d')<='$schemedate' AND date_format(plan_assigned_to_date,'%Y-%m-%d')>='$schemedate'")
                            ->where('sale_unit',3)
                            ->where('incentive_type',3)
                            ->whereIn('dealer_id',$dealer_id)
                            ->groupBy('plan_id','product_id')
                            ->orderBy('scheme_assign_dealer.id','DESC')
                            ->get();


            $scheme_details_retailer = DB::table('scheme_plan_details')
                            ->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
                            ->join('scheme_assign_retailer','scheme_assign_retailer.plan_id','=','scheme_plan.id')
                            ->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id','retailer_id')
                            ->where('scheme_plan_details.company_id',$company_id)
                                ->whereRaw("date_format(plan_assigned_from_date,'%Y-%m-%d')<='$schemedate' AND date_format(plan_assigned_to_date,'%Y-%m-%d')>='$schemedate'")
                            ->where('sale_unit',3)
                            ->where('incentive_type',3)
                            ->whereIn('retailer_id',$retailerArray)
                            ->groupBy('plan_id','product_id')
                            ->orderBy('scheme_assign_retailer.id','DESC')
                            ->get();

            #......................................scheme data ends here ..................................................##

            $work_status_query = DB::table('_daily_schedule')
                        ->select('id','name')
                        ->where('_daily_schedule.company_id',$company_id)
                        ->where('status',1)
                        ->get();
                        
            $masterScheme = DB::table('scheme_plan_details')
                            ->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
                            ->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id')
                            ->where('scheme_plan_details.company_id',$company_id)
                            ->where('sale_unit',3)
                            ->where('incentive_type',3)
                            ->whereIn('scheme_id',$schemePlanId)
                            ->groupBy('plan_id','product_id')
                            ->orderBy('scheme_plan_details.id','DESC')
                            ->get();

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
                    // working_with Ends here!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
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
   
            #......................................reponse parameters starts here ..................................................##
                return response()->json([
                    'response' =>True,
                    'company_id'=>$company_id,
                    'outletType'=>$outletType,
					'outletCategory'=>$outletCategory,
                    'app_module'=> $module,
                    'final_dealer_data'=>$final_dealer_data,
                    'beat'=>!empty($final_data_beat)?$final_data_beat:array(), // beat data (location_5)
                    'final_csa_data'=>!empty($csa_data)?$csa_data:array(), // beat data (location_5)
                    // 'retailer'=>!empty($final_retailer)?$final_retailer:array(), // retailer all above response data dependend on each other
                    'sub_module'=> $sub_module_arr,
                    'other_module_arr'=> $other_module_arr,
                    'user_details'=>!empty($user_personal_data)?$user_personal_data:array(),
                    'working_status_adata'=> $working_status_adata, // user data
                    'state_array'=>$state_array,
                    'town_array'=>$town_array,
                    'retailer'=>!empty($sending_array_retailer)?$sending_array_retailer:array(),
                    'product_details'=>$final_array_details,
                    'product_classification'=>$final_product_classification_details,
                    'category' => $final_category_array,
                    'non_productive_reason'=> $final_non_productive_query,
                    'scheme_details_dealer'=> $scheme_details_dealer,
                    'scheme_details_retailer'=> $scheme_details_retailer,
                    'visit_type'=> $work_status_query,
                     'colleague'=> $collegueArr,
                     'masterScheme'=> $masterScheme,

                    'message'=>'Success!!']);
            #......................................reponse parameters ends here ..................................................##

               
                 
        } // person_query !empty ends here
        else
        {
            return response()->json([ 'response' =>False,'message'=>'!!User Data Record Not Found!!']);        
        }
      


    }
    public function dms_company_dealer_list(Request $request)
    {
    	$validator=Validator::make($request->all(),[
            'user_id' => 'required',
            'company_id'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $user_id = $request->user_id;
        $company_id = $request->company_id;
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
                $final_dealer_data = array();
                
                foreach ($user_dealer_retailer_query as $key => $value)
                {
                    $dealer_id[]=$value->dealer_id;
                    $dealer_data_string['dealer_id'] = "$value->dealer_id"; // return the data in string
                    $dealer_data_string['lid'] = "$value->lid"; // return the data in string
                    $dealer_data_string['lname'] = $value->lname;
                    $dealer_data_string['name'] = $value->name;
                    $final_dealer_data[] = $dealer_data_string; // merge all data in one array
                }
		        return response()->json([
                    'response' =>True,

                    'final_dealer_data'=>$final_dealer_data,

                    'message'=>'Success!!']);
    }
	public function dms_login(Request $request)
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
        $v_code = $request->v_code;
        $v_name = $request->v_name;
		$token = $request->token;


		$query_login = DB::table('dealer_person_login')
                    ->join('dealer','dealer.id','=','dealer_person_login.dealer_id')
					->join('_role','_role.role_id','=','dealer_person_login.role_id')
					->join('location_3','location_3.id','=','dealer_person_login.state_id')
					->select('rolename','dealer_id','dealer_person_login.email','dealer_person_login.state_id','dealer_person_login.dpId','_role.role_id','dealer_person_login.person_name','dealer_person_login.company_id','dealer_person_login.phone','location_3.name as l3_name','dealer.dealer_code')
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
					// $dms_login_array['state_id'] = !empty($location_details_arra->l3_id)?$location_details_arra->l3_id:'0';
					$dms_login_array['state_id'] = !empty($query_login[0]->state_id)?$query_login[0]->state_id:'0';
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
                $working_status_adata = DB::table('_working_status')->where('status',1)->where('company_id',$company_id)->get();

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
                            'working_status_adata'=> $working_status_adata,
	                        'message'=>'Success!!']);
	                #......................................reponse parameters ends here ..................................................##
			}
			else
			{
	            return response()->json([ 'response' =>False,'message'=>'!!Credentials not match with our records!!']);        

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
				$dms_login_array['user_id'] = $value->dealer_id;
				$dms_login_array['company_id'] = $value->company_id;
                $dms_login_array['state_name'] = $value->l3_name;
				$dms_login_array['dealer_code'] = $value->dealer_code;
				$dms_login_array['user_type'] = '2';

			}
            $user_id = $query_login[0]->dealer_id; // return user id
            $myArr=['version_code_name'=>"Version: $v_name/$v_code",'dms_token'=>$token];
            $update_query = DB::table('dealer_person_login')->where('dealer_id',$user_id)->update($myArr);
            $fcm_token = $request->token;
            $msg = 'login';
            $data = [
                        'msg' => $msg,
                        'body' => 'test12',

                    ];
            $notification = self::sendNotification($fcm_token, $data);
            // dd($notification);
            $working_status_adata = DB::table('_working_status')->where('status',1)->where('company_id',$company_id)->get();
            
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
                            'working_status_adata'=> $working_status_adata,
	                        'message'=>'Success!!']);
	                #......................................reponse parameters ends here ..................................................##
			
		}

		else
		{
            return response()->json([ 'response' =>False,'message'=>'!!Credentials not match with our records!!']);        

		}
	}

	public function dms_product_details(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'state_id' => 'required',
            'company_id'=>'required',
            'dealer_id'=>'required',
            'status'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $state_id = $request->state_id;
        $company_id = $request->company_id;
        $dealer_id = $request->dealer_id;
        $retailer_id = !empty($request->retailer_id)?$request->retailer_id:'0';
        $scheme_details = array();
        $out_scheme_details = array();
        $product_array = array();

        $productType = DB::table('product_type')->where('company_id',$company_id)->pluck('name','id')->toArray();


        // $product_array = DB::table('catalog_product')
        //                 ->join('product_rate_list_template','product_rate_list_template.product_id','=','catalog_product.id')
        //                 ->join('dealer','dealer.template_id','=','product_rate_list_template.template_type')
        //                 ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
        //                 ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
        //                 ->select('catalog_2.*')
        //                 ->where('catalog_1.status',1)
        //                 ->where('catalog_2.status',1)
        //                 ->where('catalog_product.status',1)
        //                 ->where('dealer.id',$dealer_id)
        //                 ->where('catalog_product.company_id',$company_id)
        //                 ->groupBy('catalog_2.id')
        //                 ->orderBy('catalog_product.product_sequence','ASC')
        //                 ->get()->toArray();

        if($retailer_id == 0){
            $further_details = array();
            $date = date('Y-m-d');
            $scheme_details = DB::table('scheme_plan_details')
                            ->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
                            ->join('scheme_assign_dealer','scheme_assign_dealer.plan_id','=','scheme_plan.id')
                            ->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id')
                            ->where('scheme_plan_details.company_id',$company_id)
                            ->where('scheme_assign_dealer.dealer_id',$dealer_id)
                                ->whereRaw("date_format(plan_assigned_from_date,'%Y-%m-%d')<='$date' AND date_format(plan_assigned_to_date,'%Y-%m-%d')>='$date'")
                            // ->whereRaw("date_format(plan_assigned_from_date,'%Y-%m-%d')<='$date' AND date_format(plan_assigned_to_date,'%Y-%m-%d')>='$date'")
                            ->where('sale_unit',3)
                            ->where('incentive_type',3)
                            ->groupBy('plan_id','product_id')
                            ->orderBy('scheme_assign_dealer.id','DESC')
                            ->get();
        }
        elseif($dealer_id == 0){
            $further_details = array();
            $date = date('Y-m-d');
            $scheme_details = DB::table('scheme_plan_details')
                            ->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
                            ->join('scheme_assign_retailer','scheme_assign_retailer.plan_id','=','scheme_plan.id')
                            ->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id')
                            ->where('scheme_plan_details.company_id',$company_id)
                            ->where('scheme_assign_retailer.retailer_id',$retailer_id)
                                ->whereRaw("date_format(plan_assigned_from_date,'%Y-%m-%d')<='$date' AND date_format(plan_assigned_to_date,'%Y-%m-%d')>='$date'")
                            // ->whereRaw("date_format(plan_assigned_from_date,'%Y-%m-%d')<='$date' AND date_format(plan_assigned_to_date,'%Y-%m-%d')>='$date'")
                            ->where('sale_unit',3)
                            ->where('incentive_type',3)
                            ->groupBy('plan_id','product_id')
                            ->orderBy('scheme_assign_retailer.id','DESC')
                            ->get();
        }
        if(!empty($scheme_details)){
            foreach ($scheme_details as $s_key => $s_value) {
// code...
                $bsdk = $s_value->product_id;
                $out_scheme_details[$bsdk]['range_second']= $s_value->range_second;
                $out_scheme_details[$bsdk]['range_first']= $s_value->range_first;
                $out_scheme_details[$bsdk]['free_qty']= $s_value->free_qty;
                $out_scheme_details[$bsdk]['plan_id']= $s_value->plan_id;
                $out_scheme_details[$bsdk]['product_id']= $s_value->product_id;
            }    
        }
        // $scheme_details = $out_scheme_details;

        $descriptionImage = DB::table('sku_description_images')
                            ->where('company_id',$company_id)
                            ->groupBy('id')
                            ->get()->toArray();

        $descImage = array();
        foreach ($descriptionImage as $dkey => $dvalue) {
            $finalOutDesc['image_url'] = 'sku_description_images/'.$dvalue->image;
            $descImage[$dvalue->product_id][] = $finalOutDesc;
        }


        
        $final_array_details = [];
        if(COUNT($product_array)>0)
        {
	        foreach ($product_array as $key => $value) 
	        {
	        	$first_layer['id']=$value->id;
	        	$first_layer['name']=$value->name;
	        	$first_layer['catalog_product_details']=DB::table('catalog_product')
					                                ->join('product_rate_list_template','product_rate_list_template.product_id','=','catalog_product.id')
                                                    ->join('dealer','dealer.template_id','=','product_rate_list_template.template_type')
							                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
							                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
							                        ->select('catalog_product.brand_details','catalog_product.description_hind','catalog_product.description_eng','catalog_product.image_name as image_name','product_id','state_id','dealer.csa_id as ss_id','dealer.id as distributor_id','product_type_primary','other_retailer_rate','other_dealer_rate','quantiy_per_other_type','product_type_id','retailer_rate as retailer_case_rate','catalog_product.product_type as product_type','catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','catalog_product.quantity_per_case as quantity_per_case','unit','catalog_product.name as product_name','product_rate_list_template.retailer_pcs_rate as base_price','product_rate_list_template.mrp_pcs as mrp', 'product_rate_list_template.mrp_pcs','hsn_code','catalog_2.name as category_name', 'product_rate_list_template.dealer_rate as dealer_rate','product_rate_list_template.dealer_pcs_rate as dealer_pcs_rate','catalog_product.gst_percent as gst','final_product_type','video_name','catalog_product.ingredriants_details')
							                        ->where('catalog_1.status',1)
							                        ->where('catalog_2.status',1)
							                        ->where('catalog_product.status',1)
							                        ->where('dealer.id',$dealer_id)
							                        ->where('catalog_id',$value->id)
							                        ->where('catalog_product.company_id',$company_id)
							                        ->groupBy('product_id')
                                                    ->orderBy('catalog_product.name','ASC')
							                        ->get()->toArray();
	            $final_array_details[] = $first_layer;
	    	}

        }
        else
        {
        	$product_array = DB::table('catalog_product')
                        ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                        ->select('catalog_2.*')
                        ->where('catalog_1.status',1)
                        ->where('catalog_2.status',1)
                        ->where('catalog_product.status',1)
                        ->where('state_id',$state_id)
                        ->where('catalog_product.company_id',$company_id)
                        ->groupBy('catalog_2.id')
                        ->orderBy('catalog_2.sequence','ASC')
                        ->get()->toArray();
            foreach ($product_array as $key => $value) 
	        {
	        	$first_layer['id']=$value->id;
	        	$first_layer['name']=$value->name;
                $query_details_array = [];
	        	$query_details=DB::table('catalog_product')
		                        ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
		                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
		                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
		                        ->select('catalog_product.brand_details','catalog_product.description_hind','catalog_product.description_eng','catalog_product.image_name as image_name','product_id','state_id','ss_id','distributor_id','product_type_primary','other_retailer_rate','other_dealer_rate','quantiy_per_other_type','product_type_id','retailer_rate as retailer_case_rate','catalog_product.product_type as product_type','catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','catalog_product.quantity_per_case as quantity_per_case','unit','catalog_product.name as product_name','product_rate_list.retailer_pcs_rate as base_price','product_rate_list.mrp_pcs as mrp', 'product_rate_list.mrp_pcs','hsn_code','catalog_2.name as category_name', 'product_rate_list.dealer_rate as dealer_rate','product_rate_list.dealer_pcs_rate as dealer_pcs_rate','catalog_product.gst_percent as gst','final_product_type','video_name','catalog_product.ingredriants_details')
		                        ->where('catalog_1.status',1)
		                        ->where('catalog_2.status',1)
		                        ->where('catalog_product.status',1)
		                        ->where('state_id',$state_id)
		                        ->where('catalog_id',$value->id)
		                        ->where('catalog_product.company_id',$company_id)
		                        ->groupBy('product_id')
                                ->orderBy('catalog_product.name','ASC')
		                        ->get()->toArray();
                if(!empty($query_details)){
                    foreach ($query_details as $q_key => $q_value) {
                        // code...

                        if($q_value->catalog_id == '1984'){
                            $catgol = '1';
                        }else{
                            $catgol = '0';
                        }


                        $out_query['brand_details'] = $q_value->brand_details;
                        $out_query['description_hind'] = $q_value->description_hind;
                        $out_query['description_eng'] = $q_value->description_eng;
                        $out_query['image_name'] = $q_value->image_name;
                        $out_query['video_name'] = $q_value->video_name;;
                        $out_query['product_id'] = $q_value->product_id;
                        $out_query['state_id'] = $q_value->state_id;
                        $out_query['ss_id'] = $q_value->ss_id;
                        $out_query['distributor_id'] = $q_value->distributor_id;
                        $out_query['product_type_primary'] = $q_value->product_type_primary;
                        $out_query['other_retailer_rate'] = $q_value->other_retailer_rate;
                        $out_query['other_dealer_rate'] = $q_value->other_dealer_rate;
                        $out_query['quantiy_per_other_type'] = $q_value->quantiy_per_other_type;
                        $out_query['product_type_id'] = $q_value->product_type_id;
                        $out_query['retailer_case_rate'] = $q_value->retailer_case_rate;
                        $out_query['product_type'] = $q_value->product_type;
                        $out_query['id'] = $q_value->id;
                        $out_query['weight'] = $q_value->weight;
                        $out_query['classification_id'] = $q_value->classification_id;
                        $out_query['classification_name'] = $q_value->classification_name;
                        $out_query['catalog_id'] = $q_value->catalog_id;
                        $out_query['is_golden'] = $catgol;
                        $out_query['quantity_per_case'] = $q_value->quantity_per_case;
                        // $out_query['unit'] = $q_value->unit; // change by dheeru
                        $out_query['unit'] = $q_value->quantity_per_case;
                        $out_query['product_name'] = $q_value->product_name;
                        $out_query['base_price'] = $q_value->base_price;
                        $out_query['mrp'] = $q_value->mrp;
                        $out_query['mrp_pcs'] = $q_value->mrp_pcs;
                        $out_query['hsn_code'] = $q_value->hsn_code;
                        $out_query['category_name'] = $q_value->category_name;
                        $out_query['dealer_rate'] = $q_value->dealer_rate;
                        $out_query['dealer_pcs_rate'] = $q_value->dealer_pcs_rate;
                        $out_query['gst'] = $q_value->gst;
                        $out_query['pack_name'] = !empty($productType[$q_value->final_product_type])?$productType[$q_value->final_product_type]:'';

                        $out_query['ingredriants_details'] = !empty($q_value->ingredriants_details)?$q_value->ingredriants_details:'';


                        $out_query['range_second']= !empty($out_scheme_details[$q_value->product_id]['range_second'])?$out_scheme_details[$q_value->product_id]['range_second']:'0';
                        $out_query['range_first']= !empty($out_scheme_details[$q_value->product_id]['range_first'])?$out_scheme_details[$q_value->product_id]['range_first']:'0';
                        $out_query['free_qty']= !empty($out_scheme_details[$q_value->product_id]['free_qty'])?$out_scheme_details[$q_value->product_id]['free_qty']:'0';
                        $out_query['plan_id']= !empty($out_scheme_details[$q_value->product_id]['plan_id'])?$out_scheme_details[$q_value->product_id]['plan_id']:'0';
                        $out_query['product_id_scheme']= !empty($out_scheme_details[$q_value->product_id]['product_id'])?$out_scheme_details[$q_value->product_id]['product_id']:'0';

                        $out_query['descriptionImages']= !empty($descImage[$q_value->product_id])?$descImage[$q_value->product_id]:array();


                        $query_details_array[] = $out_query;

                    }
                }
                $first_layer['catalog_product_details'] = $query_details_array;
                $final_array_details[] = $first_layer;
                
	    	}	

        }
        
    	
        // dd($scheme_details);
    	if($request->status == 1) // purchase order
    	{


    		$further_details = DB::table('purchase_order')
						->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
						->select('cases as cases','product_id','scheme_qty','purchase_order.order_id as order_id','quantity')
						->where('purchase_order.dealer_id',$dealer_id)
						->where('app_flag',1)
						->where('purchase_order.company_id',$company_id)
						->get();
		}
		elseif ($request->status == 2) 
		{
			$further_details = DB::table('counter_sale_summary')
						->join('counter_sale_details','counter_sale_details.order_id','=','counter_sale_summary.order_id')
						->select('cases','case_rate','counter_sale_summary.order_id','product_id','pcs as quantity')
						->where('app_flag',1)
						->where('counter_sale_summary.dealer_id',$dealer_id)
						->where('counter_sale_summary.company_id',$company_id)
						->get();
		}

        return response()->json(['response' =>True,'message'=>'!!Found!!','product_details'=>$final_array_details,'further_details'=>$further_details,'scheme_details'=>$out_scheme_details,'minimum_sale_value'=>500]);        

	}
	public function dms_ecart_product_details(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'state_id' => 'required',
            'company_id'=>'required',
            'dealer_id'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $state_id = $request->state_id;
        $company_id = $request->company_id;
        $dealer_id = $request->dealer_id;	

        $product_arary = DB::table('catalog_product')
                        ->join('product_rate_list_template','product_rate_list_template.product_id','=','catalog_product.id')
                        ->join('dealer','dealer.template_id','=','product_rate_list_template.template_type')
                        ->join('purchase_order_details','purchase_order_details.product_id','=','catalog_product.id')
                        ->join('purchase_order','purchase_order_details.order_id','=','purchase_order.order_id')
                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                        ->select('purchase_order_details.scheme_qty as primary_scheme','purchase_order_details.pcs as primary_pcs','purchase_order_details.cases as primary_cases','purchase_order.order_id','catalog_product.image_name as image_name','product_rate_list_template.product_id as product_id','state_id','dealer.csa_id as ss_id','dealer.id as distributor_id','product_type_primary','other_retailer_rate','other_dealer_rate','quantiy_per_other_type','product_type_id','retailer_rate as retailer_case_rate','catalog_product.product_type as product_type','catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','catalog_product.quantity_per_case as quantity_per_case','unit','catalog_product.name','product_rate_list_template.retailer_pcs_rate as base_price','product_rate_list_template.mrp_pcs as mrp', 'product_rate_list_template.mrp_pcs','hsn_code','catalog_2.name as cname', 'product_rate_list_template.dealer_rate as dealer_rate','product_rate_list_template.dealer_pcs_rate as dealer_pcs_rate')
                        ->where('catalog_1.status',1)
                        ->where('catalog_2.status',1)
                        ->where('catalog_product.status',1)
                        ->where('dealer.id',$dealer_id)
                        ->where('app_flag','=',1)
                        ->where('purchase_order.dealer_id',$dealer_id)
                        // ->where('catalog_id',$value->id)
                        ->where('catalog_product.company_id',$company_id)
                        ->groupBy('product_id')
                        ->get()->toArray();

        $product_type_new = DB::table('product_type')
                            ->where('status',1)
                            ->where('company_id',$company_id)
                            ->groupBy('id')
                            ->pluck('name','id');
        
        $final_catalog_product_details = array();
    	if(COUNT($product_arary)>0)
    	{
    		foreach ($product_arary as $key => $value) 
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
                $productArray['product_name'] = $value->name;
                $productArray['weight'] = $value->weight;
                $productArray['base_price'] = $value->base_price;
                $productArray['case_base_price'] = $value->retailer_case_rate;
                $productArray['dealer_rate'] = $value->dealer_rate;
                $productArray['dealer_pcs_rate'] = $value->dealer_pcs_rate;
                $productArray['mrp'] = $value->mrp;
                $productArray['pcs_mrp'] = $value->mrp_pcs;
                $productArray['unit'] = !empty($value->unit)?$value->unit:'';
                $productArray['quantity_per_case'] = !empty($value->quantity_per_case)?$value->quantity_per_case:'';
                $productArray['quantiy_per_other_type'] = !empty($value->quantiy_per_other_type)?$value->quantiy_per_other_type:'';
                $productArray['sku_product_type_id_primary'] = !empty($value->product_type_primary)?$value->product_type_primary:'';
                $productArray['sku_product_type_name_primary'] = !empty($product_type_new[$value->product_type_primary])?$product_type_new[$value->product_type_primary]:'';
                $productArray['sku_product_type_id'] = !empty($value->product_type)?$value->product_type:'';
                $productArray['sku_product_type_name'] = !empty($product_type_new[$value->product_type])?$product_type_new[$value->product_type]:'';
                $productArray['product_type_id_rate_list'] = !empty($value->product_type_id)?$value->product_type_id:'';
                $productArray['product_type_name_rate_list'] = !empty($product_type_new[$value->product_type_id])?$product_type_new[$value->product_type_id]:'';
                $productArray['other_retailer_rate'] = !empty($value->other_retailer_rate)?$value->other_retailer_rate:'';
                $productArray['other_dealer_rate'] = !empty($value->other_dealer_rate)?$value->other_dealer_rate:'';
                $productArray['image_name'] = !empty($value->image_name)?$value->image_name:'';
                $productArray['primary_cases'] = !empty($value->primary_cases)?$value->primary_cases:'';
                $productArray['primary_pcs'] = !empty($value->primary_pcs)?$value->primary_pcs:'';
                $productArray['scheme_qty'] = !empty($value->primary_scheme)?$value->primary_scheme:'';
                $productArray['dms_order_id'] = !empty($value->order_id)?$value->order_id:'';
                $final_catalog_product_details[] = $productArray;
            }

            $date = date('Y-m-d');
            $scheme_details = DB::table('scheme_plan_details')
                                            ->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
                                            ->join('scheme_assign_dealer','scheme_assign_dealer.plan_id','=','scheme_plan.id')
                                            ->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id')
                                            ->where('scheme_plan_details.company_id',$company_id)
                                            ->where('scheme_assign_dealer.dealer_id',$dealer_id)
                        ->whereRaw("date_format(plan_assigned_from_date,'%Y-%m-%d')<='$date' AND date_format(plan_assigned_to_date,'%Y-%m-%d')>='$date'")
                                            // ->whereRaw("date_format(plan_assigned_from_date,'%Y-%m-%d')>='$date' AND date_format(plan_assigned_to_date,'%Y-%m-%d')<='$date'")
                                            ->where('sale_unit',2)
                                            ->where('incentive_type',3)
                                            ->groupBy('plan_id','product_id')
                                            ->get();
                    $mode_details = DB::table('_payment_modes')->select('mode as name','id')->where('status',1)->get();
                    $travell_mode = DB::table('_vehicle_details')->select('name as name','id')->where('status',1)->where('company_id',$company_id)->get();
                    $travelling_type = DB::table('_dms_travelling_type')->select('name as name','id')->where('status',1)->where('company_id',$company_id)->get();

            return response()->json(['response' =>True,'message'=>'!!Found!!','product_details'=>$final_catalog_product_details,'scheme_details'=>$scheme_details,'payment_mode'=>$mode_details,'travelling_type'=>$travelling_type,'travell_mode'=>$travell_mode,'minimum_sale_value'=>500]);
    	}
    	else
    	{
    		$product_arary = DB::table('catalog_product')
                        ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
                        ->join('dealer','dealer.state_id','=','product_rate_list.state_id')
                        ->join('purchase_order_details','purchase_order_details.product_id','=','catalog_product.id')
                        ->join('purchase_order','purchase_order_details.order_id','=','purchase_order.order_id')
                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                        ->select('purchase_order_details.scheme_qty as primary_scheme','purchase_order_details.pcs as primary_pcs','purchase_order_details.cases as primary_cases','purchase_order.order_id','catalog_product.image_name as image_name','product_rate_list.product_id as product_id','product_rate_list.state_id','dealer.csa_id as ss_id','dealer.id as distributor_id','product_type_primary','other_retailer_rate','other_dealer_rate','quantiy_per_other_type','product_type_id','retailer_rate as retailer_case_rate','catalog_product.product_type as product_type','catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','catalog_product.quantity_per_case as quantity_per_case','unit','catalog_product.name','product_rate_list.retailer_pcs_rate as base_price','product_rate_list.mrp_pcs as mrp', 'product_rate_list.mrp_pcs','hsn_code','catalog_2.name as cname', 'product_rate_list.dealer_rate as dealer_rate','product_rate_list.dealer_pcs_rate as dealer_pcs_rate')
                        ->where('catalog_1.status',1)
                        ->where('catalog_2.status',1)
                        ->where('catalog_product.status',1)
                        ->where('product_rate_list.state_id',$state_id)
                        ->where('app_flag','=',1)
                        ->where('purchase_order.dealer_id',$dealer_id)
                        // ->where('catalog_id',$value->id)
                        ->where('catalog_product.company_id',$company_id)
                        ->groupBy('product_id')
                        ->get()->toArray();

            $product_type_new = DB::table('product_type')
                        ->where('status',1)
                        ->where('company_id',$company_id)
                        ->groupBy('id')
                        ->pluck('name','id');
            $final_catalog_product_details = array();
            foreach ($product_arary as $key => $value) 
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
                $productArray['product_name'] = $value->name;
                $productArray['weight'] = $value->weight;
                $productArray['base_price'] = $value->base_price;
                $productArray['case_base_price'] = $value->retailer_case_rate;
                $productArray['dealer_rate'] = $value->dealer_rate;
                $productArray['dealer_pcs_rate'] = $value->dealer_pcs_rate;
                $productArray['mrp'] = $value->mrp;
                $productArray['pcs_mrp'] = $value->mrp_pcs;
                $productArray['unit'] = !empty($value->unit)?$value->unit:'';
                $productArray['quantity_per_case'] = !empty($value->quantity_per_case)?$value->quantity_per_case:'';
                $productArray['quantiy_per_other_type'] = !empty($value->quantiy_per_other_type)?$value->quantiy_per_other_type:'';
                $productArray['sku_product_type_id_primary'] = !empty($value->product_type_primary)?$value->product_type_primary:'';
                $productArray['sku_product_type_name_primary'] = !empty($product_type_new[$value->product_type_primary])?$product_type_new[$value->product_type_primary]:'';
                $productArray['sku_product_type_id'] = !empty($value->product_type)?$value->product_type:'';
                $productArray['sku_product_type_name'] = !empty($product_type_new[$value->product_type])?$product_type_new[$value->product_type]:'';
                $productArray['product_type_id_rate_list'] = !empty($value->product_type_id)?$value->product_type_id:'';
                $productArray['product_type_name_rate_list'] = !empty($product_type_new[$value->product_type_id])?$product_type_new[$value->product_type_id]:'';
                $productArray['other_retailer_rate'] = !empty($value->other_retailer_rate)?$value->other_retailer_rate:'';
                $productArray['other_dealer_rate'] = !empty($value->other_dealer_rate)?$value->other_dealer_rate:'';
                $productArray['image_name'] = !empty($value->image_name)?$value->image_name:'';
                $productArray['primary_cases'] = !empty($value->primary_cases)?$value->primary_cases:'';
                $productArray['primary_pcs'] = !empty($value->primary_pcs)?$value->primary_pcs:'';
                $productArray['scheme_qty'] = !empty($value->primary_scheme)?$value->primary_scheme:'';
                $productArray['dms_order_id'] = !empty($value->order_id)?$value->order_id:'';
                $final_catalog_product_details[] = $productArray;
            }

            $date = date('Y-m-d');
            $scheme_details = DB::table('scheme_plan_details')
                                            ->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
                                            ->join('scheme_assign_dealer','scheme_assign_dealer.plan_id','=','scheme_plan.id')
                                            ->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id')
                                            ->where('scheme_plan_details.company_id',$company_id)
                                            ->where('scheme_assign_dealer.dealer_id',$dealer_id)
                        ->whereRaw("date_format(plan_assigned_from_date,'%Y-%m-%d')<='$date' AND date_format(plan_assigned_to_date,'%Y-%m-%d')>='$date'")
                                            // ->whereRaw("date_format(plan_assigned_from_date,'%Y-%m-%d')>='$date' AND date_format(plan_assigned_to_date,'%Y-%m-%d')<='$date'")
                                            ->where('sale_unit',2)
                                            ->where('incentive_type',3)
                                            ->groupBy('plan_id','product_id')
                                            ->get();
                    $mode_details = DB::table('_payment_modes')->select('mode as name','id')->where('status',1)->get();
                    $travelling_type = DB::table('_dms_travelling_type')->select('name as name','id')->where('status',1)->where('company_id',$company_id)->get();
                    $travell_mode = DB::table('_vehicle_details')->select('name as name','id')->where('status',1)->where('company_id',$company_id)->get();

            return response()->json(['response' =>True,'message'=>'!!Found!!','product_details'=>$final_catalog_product_details,'scheme_details'=>$scheme_details,'payment_mode'=>$mode_details,'travelling_type'=>$travelling_type,'travell_mode'=>$travell_mode,'minimum_sale_value'=>500]);

    		
    	}

	}

	public function dms_counter_draft_product_details(Request $request)
	{

		$validator=Validator::make($request->all(),[
            'state_id' => 'required',
            'company_id'=>'required',
            'dealer_id'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $state_id = $request->state_id;
        $company_id = $request->company_id;
        $dealer_id = $request->dealer_id;	

        $product_arary = DB::table('catalog_product')
                        ->join('product_rate_list_template','product_rate_list_template.product_id','=','catalog_product.id')
                        ->join('dealer','dealer.template_id','=','product_rate_list_template.template_type')
                        ->join('counter_sale_details','counter_sale_details.product_id','=','catalog_product.id')
                        ->join('counter_sale_summary','counter_sale_summary.order_id','=','counter_sale_details.order_id')
                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                        ->select('counter_sale_details.scheme_qty as primary_scheme','counter_sale_details.pcs as primary_pcs','counter_sale_details.cases as primary_cases','counter_sale_details.order_id','catalog_product.image_name as image_name','product_rate_list_template.product_id as product_id','state_id','dealer.csa_id as ss_id','dealer.id as distributor_id','product_type_primary','other_retailer_rate','other_dealer_rate','quantiy_per_other_type','product_type_id','retailer_rate as retailer_case_rate','catalog_product.product_type as product_type','catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','catalog_product.quantity_per_case as quantity_per_case','unit','catalog_product.name','product_rate_list_template.retailer_pcs_rate as base_price','product_rate_list_template.mrp_pcs as mrp', 'product_rate_list_template.mrp_pcs','hsn_code','catalog_2.name as cname', 'product_rate_list_template.dealer_rate as dealer_rate','product_rate_list_template.dealer_pcs_rate as dealer_pcs_rate')
                        ->where('catalog_1.status',1)
                        ->where('catalog_2.status',1)
                        ->where('catalog_product.status',1)
                        ->where('counter_sale_summary.dealer_id',$dealer_id)
                        ->where('dealer.id',$dealer_id)
                        ->where('app_flag','=',1)
                        // ->where('catalog_id',$value->id)
                        ->where('catalog_product.company_id',$company_id)
                        ->groupBy('product_id')
                        ->get()->toArray();

        $product_type_new = DB::table('product_type')
	                        ->where('status',1)
	                        ->where('company_id',$company_id)
	                        ->groupBy('id')
	                        ->pluck('name','id');

        $final_catalog_product_details = array();

    	if(COUNT($product_arary)>0)
    	{
    		foreach ($product_arary as $key => $value) 
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
	            $productArray['product_name'] = $value->name;
	            $productArray['weight'] = $value->weight;
	            $productArray['base_price'] = $value->base_price;
	            $productArray['case_base_price'] = $value->retailer_case_rate;
	            $productArray['dealer_rate'] = $value->dealer_rate;
	            $productArray['dealer_pcs_rate'] = $value->dealer_pcs_rate;
	            $productArray['mrp'] = $value->mrp;
	            $productArray['pcs_mrp'] = $value->mrp_pcs;
	            $productArray['unit'] = !empty($value->unit)?$value->unit:'';
	            $productArray['quantity_per_case'] = !empty($value->quantity_per_case)?$value->quantity_per_case:'';
	            $productArray['quantiy_per_other_type'] = !empty($value->quantiy_per_other_type)?$value->quantiy_per_other_type:'';
	            $productArray['sku_product_type_id_primary'] = !empty($value->product_type_primary)?$value->product_type_primary:'';
	            $productArray['sku_product_type_name_primary'] = !empty($product_type_new[$value->product_type_primary])?$product_type_new[$value->product_type_primary]:'';
	            $productArray['sku_product_type_id'] = !empty($value->product_type)?$value->product_type:'';
	            $productArray['sku_product_type_name'] = !empty($product_type_new[$value->product_type])?$product_type_new[$value->product_type]:'';
	            $productArray['product_type_id_rate_list'] = !empty($value->product_type_id)?$value->product_type_id:'';
	            $productArray['product_type_name_rate_list'] = !empty($product_type_new[$value->product_type_id])?$product_type_new[$value->product_type_id]:'';
	            $productArray['other_retailer_rate'] = !empty($value->other_retailer_rate)?$value->other_retailer_rate:'';
	            $productArray['other_dealer_rate'] = !empty($value->other_dealer_rate)?$value->other_dealer_rate:'';
	            $productArray['image_name'] = !empty($value->image_name)?$value->image_name:'';
	            $productArray['primary_cases'] = !empty($value->primary_cases)?$value->primary_cases:'';
	            $productArray['primary_pcs'] = !empty($value->primary_pcs)?$value->primary_pcs:'';
	            $productArray['scheme_qty'] = !empty($value->primary_scheme)?$value->primary_scheme:'';
	            $productArray['dms_order_id'] = !empty($value->order_id)?$value->order_id:'';
	            $final_catalog_product_details[] = $productArray;
	        }
    	}
    	else
    	{
    		
    		$product_arary = DB::table('catalog_product')
                        ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
                        ->join('counter_sale_details','counter_sale_details.product_id','=','catalog_product.id')
                        ->join('counter_sale_summary','counter_sale_summary.order_id','=','counter_sale_details.order_id')
                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                        ->select('counter_sale_details.scheme_qty as primary_scheme','counter_sale_details.pcs as primary_pcs','counter_sale_details.cases as primary_cases','counter_sale_details.order_id','catalog_product.image_name as image_name','product_rate_list.product_id as product_id','state_id','ss_id','distributor_id','product_type_primary','other_retailer_rate','other_dealer_rate','quantiy_per_other_type','product_type_id','retailer_rate as retailer_case_rate','catalog_product.product_type as product_type','catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','catalog_product.quantity_per_case as quantity_per_case','unit','catalog_product.name','product_rate_list.retailer_pcs_rate as base_price','product_rate_list.mrp_pcs as mrp', 'product_rate_list.mrp_pcs','hsn_code','catalog_2.name as cname', 'product_rate_list.dealer_rate as dealer_rate','product_rate_list.dealer_pcs_rate as dealer_pcs_rate')
                        ->where('catalog_1.status',1)
                        ->where('catalog_2.status',1)
                        ->where('catalog_product.status',1)
                        ->where('dealer_id',$dealer_id)
                        ->where('state_id',$state_id)
                        ->where('app_flag','=',1)
                        // ->where('catalog_id',$value->id)
                        ->where('catalog_product.company_id',$company_id)
                        ->groupBy('product_id')
                        ->get()->toArray();
        

        	foreach ($product_arary as $key => $value) 
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
                $productArray['product_name'] = $value->name;
                $productArray['weight'] = $value->weight;
                $productArray['base_price'] = $value->base_price;
                $productArray['case_base_price'] = $value->retailer_case_rate;
                $productArray['dealer_rate'] = $value->dealer_rate;
                $productArray['dealer_pcs_rate'] = $value->dealer_pcs_rate;
                $productArray['mrp'] = $value->mrp;
                $productArray['pcs_mrp'] = $value->mrp_pcs;
                $productArray['unit'] = !empty($value->unit)?$value->unit:'';
                $productArray['quantity_per_case'] = !empty($value->quantity_per_case)?$value->quantity_per_case:'';
                $productArray['quantiy_per_other_type'] = !empty($value->quantiy_per_other_type)?$value->quantiy_per_other_type:'';
                $productArray['sku_product_type_id_primary'] = !empty($value->product_type_primary)?$value->product_type_primary:'';
                $productArray['sku_product_type_name_primary'] = !empty($product_type_new[$value->product_type_primary])?$product_type_new[$value->product_type_primary]:'';
                $productArray['sku_product_type_id'] = !empty($value->product_type)?$value->product_type:'';
                $productArray['sku_product_type_name'] = !empty($product_type_new[$value->product_type])?$product_type_new[$value->product_type]:'';
                $productArray['product_type_id_rate_list'] = !empty($value->product_type_id)?$value->product_type_id:'';
                $productArray['product_type_name_rate_list'] = !empty($product_type_new[$value->product_type_id])?$product_type_new[$value->product_type_id]:'';
                $productArray['other_retailer_rate'] = !empty($value->other_retailer_rate)?$value->other_retailer_rate:'';
                $productArray['other_dealer_rate'] = !empty($value->other_dealer_rate)?$value->other_dealer_rate:'';
                $productArray['image_name'] = !empty($value->image_name)?$value->image_name:'';
                $productArray['primary_cases'] = !empty($value->primary_cases)?$value->primary_cases:'';
                $productArray['primary_pcs'] = !empty($value->primary_pcs)?$value->primary_pcs:'';
                $productArray['scheme_qty'] = !empty($value->primary_scheme)?$value->primary_scheme:'';
                $productArray['dms_order_id'] = !empty($value->order_id)?$value->order_id:'';
                $final_catalog_product_details[] = $productArray;
            }
    	}
        

        $date = date('Y-m-d');
        $scheme_details = DB::table('scheme_plan_details')
        				->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
        				->join('scheme_assign_dealer','scheme_assign_dealer.plan_id','=','scheme_plan.id')
        				->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id')
        				->where('scheme_plan_details.company_id',$company_id)
        				->where('scheme_assign_dealer.dealer_id',$dealer_id)
                            ->whereRaw("date_format(plan_assigned_from_date,'%Y-%m-%d')<='$date' AND date_format(plan_assigned_to_date,'%Y-%m-%d')>='$date'")
        				// ->whereRaw("date_format(plan_assigned_from_date,'%Y-%m-%d')>='$date' AND date_format(plan_assigned_to_date,'%Y-%m-%d')<='$date'")
        				->where('sale_unit',2)
        				->where('incentive_type',3)
        				->groupBy('plan_id','product_id')
        				->get();
		$mode_details = DB::table('_payment_modes')->select('mode as name','id')->where('status',1)->get();
        return response()->json(['response' =>True,'message'=>'!!Found!!','product_details'=>$final_catalog_product_details,'scheme_details'=>$scheme_details,'payment_mode'=>$mode_details]);        

	}

	public function dms_primary_sale_submit(Request $request)
	{
		$validator=Validator::make($request->all(),[
            "order_id"=>'required',
			"dealer_id"=>'required',
			"sale_date"=>'required',
			"created_date"=>'required',
			"date_time"=>'required',
			"battery_status"=>'required',
			"gps_status"=>'required',
			"lat"=>'required',
			"lng"=>'required',
			"address"=>'required',
			"mcc_mnc_lac_cellid"=>'required',
			"user_id"=>'required',
            "company_id"=>'required',
            "total_weight"=>'required',
			"vehicle_id"=>'required',

        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

		$order_id = $request->order_id;
		$dealer_id = $request->dealer_id;
		$sale_date = $request->sale_date;
		$created_date = $request->created_date;
		$date_time = $request->date_time;
		$battery_status = $request->battery_status;
		$gps_status = $request->gps_status;
		$lat = $request->lat;
		$lng = $request->lng;
		$address = $request->address;
		$mcc_mnc_lac_cellid = $request->mcc_mnc_lac_cellid;
		$user_id = $request->user_id;
        $company_id = $request->company_id;
        $total_weight = $request->total_weight;
		$vehicle_id = $request->vehicle_id;
		$primary_sale_summary = json_decode($request->primary_sale_summary);
		$insertDetailsArr = array();
		DB::beginTransaction();
        // dd($primary_sale_summary);/
		foreach ($primary_sale_summary as $key => $value) 
		{
			$check = DB::table('purchase_order')
					->where('order_id',$value->existing_order)
					->count();
					// ->update(['order_id'=>$value->order_id]);
			if($check>0)
			{
				// $delete 
				$delete_order = DB::table('purchase_order')->where('order_id',$value->order_id)->delete();

				$check = DB::table('purchase_order')
					->where('order_id',$value->existing_order)
					// ->count();
					->update(['order_id'=>$value->order_id]);

				$delete_details = DB::table('purchase_order_details')->where('order_id',$value->existing_order)->delete();

					$detailsArr = [
						'order_id' => $value->order_id,
						'id' => $value->order_id,
						'product_id' => $value->product_id,
						'rate' => $value->rate,
						'quantity' => $value->quantity,
						'barcode' => $value->Barcode,
						'scheme_qty' => $value->scheme_qty,
						'cases' => $value->case,
						'pcs' => $value->pcs,
						'total_value' => $value->value,
						'pr_rate' => $value->case_rate,
						'company_id'=>$company_id,
						'app_flag' => $value->app_flag,
					];
					$insertDetailsArr[] = $detailsArr;
			}
			else
			{
				$check = DB::table('purchase_order')
					->where('order_id',$value->order_id)
					->count();
					// ->update(['order_id'=>$value->order_id]);
				if($check>0)
				{
					$detailsArr = [
						'order_id' => $value->order_id,
						'id' => $value->order_id,
						'product_id' => $value->product_id,
						'rate' => $value->rate,
						'quantity' => $value->quantity,
						'barcode' => $value->Barcode,
						'scheme_qty' => $value->scheme_qty,
						'cases' => $value->case,
						'pcs' => $value->pcs,
						'total_value' => $value->value,
						'pr_rate' => $value->case_rate,
						'company_id'=>$company_id,
						'app_flag' => $value->app_flag,
					];
					$insertDetailsArr[] = $detailsArr;
				}
				else
				{
					$myArr = [
						'order_id'=>$value->order_id,
						'id' => $value->order_id,
						'dealer_id'=>$dealer_id,
						'created_person_id'=>0,
						'retailer_id'=>0,
						'sale_date'=>$sale_date,
						'created_date'=>$created_date,
						'receive_date'=>$created_date,
						'dispatch_date'=>$created_date,
						'date_time'=>$date_time,
						'battery_status'=>$battery_status,
						'gps_status'=>$gps_status,
						'lat'=>$lat,
						'lng'=>$lng,
                        'address'=>$address,
                        'total_weight'=>$total_weight,
						'vehicle_id'=>$vehicle_id,
						'mcc_mnc_lac_cellid'=>$mcc_mnc_lac_cellid,
						'company_id'=>$company_id,
					];
					$insert_order = DB::table('purchase_order')->insert($myArr);

					$detailsArr = [
						'order_id' => $value->order_id,
						'id' => $value->order_id,
						'product_id' => $value->product_id,
						'rate' => $value->rate,
						'quantity' => $value->quantity,
						'barcode' => $value->Barcode,
						'scheme_qty' => $value->scheme_qty,
						'cases' => $value->case,
						'pcs' => $value->pcs,
						'total_value' => $value->value,
						'pr_rate' => $value->case_rate,
						'company_id'=>$company_id,
						'app_flag' => $value->app_flag,
					];
					$insertDetailsArr[] = $detailsArr;
				}

			}

			
		}
		$insert_details = DB::table('purchase_order_details')->insert($insertDetailsArr);

		if($insert_details )
		{
			DB::commit();
        	return response()->json(['response' =>True,'message'=>'Successfully Submitted!!']);        

		}
		else
		{
			DB::rollback();
        	return response()->json(['response' =>False,'message'=>'Not Submitted!!']);        

		}

	}
	public function dms_banner_images(Request $request)
	{
		$validator=Validator::make($request->all(),[
            
			"company_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $company_id = $request->company_id;
        $data_omage = DB::table('banner_images')
        			->select('image_name','id')
        			->where('company_id',$company_id)
        			->where('status',1)
        			->get();
    	return response()->json(['response' =>True,'message'=>'Found!!','data'=>$data_omage]);        


	}
	public function dms_total_ecart(Request $request)
	{
		$validator=Validator::make($request->all(),[
            "state_id"=>'required',
			"dealer_id"=>'required',
			"company_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $company_id = $request->company_id;
        $state_id = $request->state_id;
        $dealer_id = $request->dealer_id;
        $user_id = !empty($request->user_id)?$request->user_id:'0';

    	$dealer_id_data = DB::table('dealer')
	       ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
	       ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
	       // ->select('dealer.name as name','dealer.id as dealer_id','l6_name as lname','l6_id as lid')
	       ->where('dealer_location_rate_list.user_id',$user_id)
	       ->where('dealer.dealer_status',1)
	       ->where('dealer.company_id',$company_id)
	       ->where('dealer_location_rate_list.company_id',$company_id)
	       ->groupBy('dealer.id')
	       ->pluck('dealer_id');

        $user_data = DB::table('purchase_order')
    		->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
			->where('app_flag',1)
			// ->where('purchase_order.state_id',$state_id)
			->whereIn('purchase_order.dealer_id',$dealer_id_data)
			->where('purchase_order.company_id',$company_id)
			->COUNT();

        $data = DB::table('purchase_order')
    		->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
			->where('app_flag',1)
			// ->where('purchase_order.state_id',$state_id)
			->where('purchase_order.dealer_id',$dealer_id)
			->where('purchase_order.company_id',$company_id)
			->COUNT();
    	return response()->json(['response' =>True,'message'=>'Found!!','data'=>$data,'user_data'=>$user_data]);        

	}
	public function dms_counter_sale_report(Request $request)
	{
		$validator=Validator::make($request->all(),[
			"dealer_id"=>'required',
			"from_date"=>'required',
			"to_date"=>'required',
			"company_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        // $state_id = $request->state_id;
        $dealer_id = $request->dealer_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $company_id = $request->company_id;

       $data = DB::table('counter_sale_summary')
       		->join('counter_sale_details','counter_sale_details.order_id','=','counter_sale_summary.order_id')
   			->join('dealer','dealer.id','=','counter_sale_summary.dealer_id')
   			->select('counter_remarks','counter_sale_summary.order_id','sale_date','date_time',DB::raw("SUM(cases+pcs) as total_sale_qty"),'dealer.name as dealer_name')
   			->whereRaw("date_format(sale_date,'%Y-%m-%d')>='$from_date' AND date_format(sale_date,'%Y-%m-%d')<='$to_date' ")
   			->where('counter_sale_summary.company_id',$company_id)
   			->where('counter_sale_summary.dealer_id',$dealer_id)
   			->where('app_flag',2)
   			// ->where('purchase_order.order_id',$order_id)
   			->groupBy('counter_sale_summary.order_id')
   			->get();
		if(!empty($data))
		{
			// $dms_reason_data = DB::table('_dms_reason')->where('status',1)->pluck('name','id');
			// $dms_reason_data_status_time = DB::table('_dms_reason')->where('status',1)->pluck('created_at','id');
			$finalArr = [];
			foreach ($data as $key => $value) 
			{
				$first_layer['order_id'] = $value->order_id;
				$first_layer['sale_date'] = $value->sale_date;
				$first_layer['total_sale_qty'] = $value->total_sale_qty;
                $first_layer['supplier_name'] =  $value->dealer_name;
				$first_layer['remarks'] = $value->counter_remarks;
				// $first_layer['current_reason_status_id'] = $value->dms_order_reason_id;
				// $first_layer['current_reason_status'] = !empty($dms_reason_data[$value->dms_order_reason_id])?$dms_reason_data[$value->dms_order_reason_id]:'Order Placed';
				// $first_layer['reason_status_time'] = !empty($dms_reason_data[$value->dms_order_reason_id])?$dms_reason_data[$value->dms_order_reason_id]:$value->date_time;
				$first_layer['details'] = DB::table('counter_sale_summary')
							       		->join('counter_sale_details','counter_sale_details.order_id','=','counter_sale_summary.order_id')
							       		->join ('catalog_product','catalog_product.id','=','counter_sale_details.product_id')
							   			->join('dealer','dealer.id','=','counter_sale_summary.dealer_id')
							   			->select('image_name','product_id','catalog_product.name as product_name','rate','quantity','cases','pcs','case_rate as case_rate')
							   			->whereRaw("date_format(sale_date,'%Y-%m-%d')>='$from_date' AND date_format(sale_date,'%Y-%m-%d')<='$to_date'")
							   			->where('counter_sale_summary.company_id',$company_id)
							   			->where('counter_sale_summary.dealer_id',$dealer_id)
							   			->where('counter_sale_summary.order_id',$value->order_id)
							   			// ->groupBy('counter_sale_details.id','product_id')
							   			->groupBy('product_id')
							   			->get();

	   			// $first_layer['reason_log'] = DB::table('dms_order_reason_log')
	   			// 							->join('_dms_reason','_dms_reason.id','=','dms_order_reason_log.dms_reason_id')
	   			// 							->select('_dms_reason.name as status_name','order_id','dms_order_reason_log.id')
	   			// 							->where('order_id',$value->order_id)
	   			// 							->where('dms_order_reason_log.company_id',$company_id)
	   			// 							->get();
				$finalArr[] = $first_layer; 
			}	



    		return response()->json(['response' =>True,'message'=>'Found!!','data'=>$finalArr]);        
    	}
    	else
    	{
    		return response()->json(['response' =>False,'message'=>'NotFound!!','data'=>array()]);        
    	}
	}
	public function dms_dealer_jar_return_report(Request $request)
	{
		$validator=Validator::make($request->all(),[
			"dealer_id"=>'required',
			"from_date"=>'required',
			"to_date"=>'required',
			"company_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        // $state_id = $request->state_id;
        $dealer_id = $request->dealer_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $company_id = $request->company_id;

       $data = DB::table('dms_dealer_return_quantity')
   			->join('dealer','dealer.id','=','dms_dealer_return_quantity.dealer_id')
   			->select(DB::raw("SUM(jar_quantity) as jar_quantity"),'dms_dealer_return_quantity.*','dealer.name as dealer_name')
   			->whereRaw("date_format(dms_dealer_return_quantity.date,'%Y-%m-%d')>='$from_date' AND date_format(dms_dealer_return_quantity.date,'%Y-%m-%d')<='$to_date' ")
   			->where('dms_dealer_return_quantity.company_id',$company_id)
   			->where('dms_dealer_return_quantity.dealer_id',$dealer_id)
   			// ->where('app_flag',2)
   			// ->where('purchase_order.order_id',$order_id)
   			->groupBy('dms_dealer_return_quantity.order_id')
   			->get();
		if(!empty($data))
		{
    		return response()->json(['response' =>True,'message'=>'Found!!','data'=>$data]);        
    	}
    	else
    	{
    		return response()->json(['response' =>False,'message'=>'NotFound!!','data'=>array()]);        
    	}
	}
    public function dms_primary_sale_report_user_wise(Request $request)
    {
        $validator=Validator::make($request->all(),[
            // "state_id"=>'required',
            "user_id"=>'required',
            "from_date"=>'required',
            "to_date"=>'required',
            "company_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $state_id = $request->state_id;
        $user_id = $request->user_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $company_id = $request->company_id;

        $dealer_id = DB::table('dealer')
           ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
           ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
           // ->select('dealer.name as name','dealer.id as dealer_id','l6_name as lname','l6_id as lid')
           ->where('dealer_location_rate_list.user_id',$user_id)
           ->where('dealer.dealer_status',1)
           ->where('dealer.company_id',$company_id)
           ->where('dealer_location_rate_list.company_id',$company_id)
           ->groupBy('dealer.id')
           ->pluck('dealer_id');

       $data = DB::table('purchase_order')
            ->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
            ->join('dealer','dealer.id','=','purchase_order.dealer_id')
            ->select('dealer.name as dealer_name','dealer.id as dealer_id','purchase_order.order_id','sale_date','dms_order_reason_id','date_time')
            ->whereRaw("date_format(sale_date,'%Y-%m-%d')>='$from_date' AND date_format(sale_date,'%Y-%m-%d')<='$to_date' ")
            ->where('purchase_order.company_id',$company_id)
            ->whereIn('purchase_order.dealer_id',$dealer_id)
            ->where('app_flag',2)
            // ->where('purchase_order.order_id',$order_id)
            ->groupBy('purchase_order.order_id')
            ->get();
        if(!empty($data))
        {
            $dms_reason_data = DB::table('_dms_reason')->where('status',1)->pluck('name','id');
            $dms_reason_data_status_time = DB::table('_dms_reason')->where('status',1)->pluck('created_at','id');
            $finalArr = [];
            foreach ($data as $key => $value) 
            {
                $first_layer['order_id'] = $value->order_id;
                $first_layer['sale_date'] = $value->sale_date;
                $first_layer['dealer_id'] = $value->dealer_id;
                $first_layer['dealer_name'] = $value->dealer_name;
                $first_layer['supplier_name'] = 'Patanjali';
                $first_layer['current_reason_status_id'] = $value->dms_order_reason_id;
                $first_layer['current_reason_status'] = !empty($dms_reason_data[$value->dms_order_reason_id])?$dms_reason_data[$value->dms_order_reason_id]:'Order Placed';
                $first_layer['reason_status_time'] = !empty($dms_reason_data[$value->dms_order_reason_id])?$dms_reason_data[$value->dms_order_reason_id]:$value->date_time;
                $first_layer['details'] = DB::table('purchase_order')
                                        ->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
                                        ->join ('catalog_product','catalog_product.id','=','purchase_order_details.product_id')
                                        ->join('dealer','dealer.id','=','purchase_order.dealer_id')
                                        ->select('image_name','product_id','catalog_product.name as product_name','rate','quantity','cases','pcs','pr_rate as case_rate','image_name')
                                        ->whereRaw("date_format(sale_date,'%Y-%m-%d')>='$from_date' AND date_format(sale_date,'%Y-%m-%d')<='$to_date'")
                                        ->where('purchase_order.company_id',$company_id)
                                        ->whereIn('purchase_order.dealer_id',$dealer_id)
                                        ->where('purchase_order.order_id',$value->order_id)
                                        ->groupBy('purchase_order_details.id','product_id')
                                        ->get();

                $first_layer['reason_log'] = DB::table('dms_order_reason_log')
                                            ->join('_dms_reason','_dms_reason.id','=','dms_order_reason_log.dms_reason_id')
                                            ->select('_dms_reason.name as status_name','order_id','dms_order_reason_log.id')
                                            ->where('order_id',$value->order_id)
                                            ->where('dms_order_reason_log.company_id',$company_id)
                                            ->get();
                $finalArr[] = $first_layer; 
            }   



            return response()->json(['response' =>True,'message'=>'Found!!','data'=>$finalArr]);        
        }
        else
        {
            return response()->json(['response' =>False,'message'=>'NotFound!!','data'=>array()]);        
        }
    }
	public function dms_primary_sale_report(Request $request)
	{
		$validator=Validator::make($request->all(),[
            "state_id"=>'required',
			"dealer_id"=>'required',
			"from_date"=>'required',
			"to_date"=>'required',
			"company_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $state_id = $request->state_id;
        $user_id = $request->user_id;
        $dealer_id = $request->dealer_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $company_id = $request->company_id;
        
        $data = DB::table('user_sales_order')
                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                 ->select('user_sales_order.order_id','user_sales_order.date')
                // ->select('user_sales_order.order_id','user_sales_order.date',DB::raw("ROUND(SUM((user_sales_order_details.quantity*user_sales_order_details.rate)+(user_sales_order_details.case_rate*user_sales_order_details.case_qty)),2) as sale_value"),DB::raw("COUNT(DISTINCT product_id) as no_sku"))
                ->where('user_sales_order.dealer_id',$dealer_id)
                ->where('user_sales_order.company_id',$company_id)
                ->where('user_sales_order.flag_fullfillment','=','1') // 1 as not filled
                ->where('user_sales_order.call_status','=','1')
                ->whereRaw("date_format(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND date_format(user_sales_order.date,'%Y-%m-%d')<='$to_date' ")
                ->groupBy('user_sales_order.order_id')
                ->get();


		if(!empty($data))
		{
			
			$finalArr = [];
			foreach ($data as $key => $value) 
			{
				$first_layer['order_id'] = $value->order_id;
                $first_layer['sale_date'] = $value->date;
				$first_layer['supplier_name'] = 'Aqualab';
				$first_layer['current_reason_status_id'] = '0';
				$first_layer['current_reason_status'] = 'Order Placed';
				$first_layer['reason_status_time'] = '';
                $first_layer['pdf_path'] = '';

                $details = DB::table('user_sales_order')
                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                        ->join ('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                                        ->select('user_sales_order.image_name','product_id','catalog_product.name as product_name','rate','scheme_qty','catalog_product.weight','quantity','user_sales_order_details.case_qty as cases','user_sales_order_details.quantity as pcs','user_sales_order_details.case_rate as case_rate','user_sales_order.image_name','catalog_2.id as c2_id','catalog_product.quantity_per_case','catalog_product.unit as unit','catalog_product.base_price as base_price','catalog_product.gst_percent')
                                        ->whereRaw("date_format(date,'%Y-%m-%d')>='$from_date' AND date_format(date,'%Y-%m-%d')<='$to_date'")
                                        ->where('user_sales_order.company_id',$company_id)
                                        ->where('user_sales_order.dealer_id',$dealer_id)
                                        ->where('user_sales_order.order_id',$value->order_id)
                                        ->groupBy('user_sales_order_details.id','product_id')
                                        ->get()->toArray();

                $grandTotal = array();
                $grandSku = array();
                $goldenSku = array();
                $goldenTotal = array();
                $finalout = array();
                foreach ($details as $dkey => $dvalue) {
                    $grandTotal[] = ($dvalue->rate*$dvalue->pcs)+($dvalue->case_rate*$dvalue->cases);
                    $grandSku[] = $dvalue->product_id;
                    if($dvalue->c2_id == 1984){
                        $goldenTotal[] = ($dvalue->rate*$dvalue->pcs)+($dvalue->case_rate*$dvalue->cases);
                        $goldenSku[] = $dvalue->product_id;
                    }

                    $out['image_name'] = $dvalue->image_name;
                    $out['product_id'] = $dvalue->product_id;
                    $out['product_name'] = $dvalue->product_name;
                    $out['rate'] = $dvalue->rate;
                    $out['scheme_qty'] = $dvalue->scheme_qty;
                    $out['weight'] = $dvalue->weight;
                    $out['quantity'] = $dvalue->quantity;
                    $out['cases'] = $dvalue->cases;
                    $out['pcs'] = $dvalue->pcs;
                    $out['case_rate'] = $dvalue->case_rate;
                    $out['c2_id'] = $dvalue->c2_id;
                    $out['quantity_per_case'] = $dvalue->quantity_per_case;
                    $out['pack'] = $dvalue->quantity_per_case;
                    $out['unit'] = $dvalue->unit;
                    $out['base_price'] = $dvalue->base_price;
                    $out['mrp'] = $dvalue->base_price;
                    $out['mrp_pcs'] = $dvalue->base_price;
                    $out['retailer_case_rate'] = $dvalue->base_price;
                    $out['gst'] = $dvalue->gst_percent;
                    $out['scheme'] = '';
                    $out['value'] = '';


                    $finalout[] = $out;

                }

                 $first_layer['grand_total'] = array_sum($grandTotal);
                $first_layer['no_of_sku'] = COUNT(array_unique($grandSku));

                $first_layer['golden_value'] = array_sum($goldenTotal);
                $first_layer['golden_sku'] = COUNT(array_unique($goldenSku));

                 $first_layer['total_value'] = array_sum($grandTotal);



                 $first_layer['details'] = $finalout;
                
				$finalArr[] = $first_layer; 
			}	



    		return response()->json(['response' =>True,'message'=>'Found!!','data'=>$finalArr]);        
    	}
    	else
    	{
    		return response()->json(['response' =>False,'message'=>'NotFound!!','data'=>array()]);        
    	}
	}

	public function dms_dealer_stock(Request $request)
	{
		$validator=Validator::make($request->all(),[
			"dealer_id"=>'required',
			"company_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $company_id = $request->company_id;
        $dealer_id = $request->dealer_id;

        $details_array = DB::table('catalog_product')
    			->select('id as product_id','name as product_name')
    			->where('catalog_product.company_id',$company_id)
                ->where('catalog_product.status',1)
    			->get();

		$stock_cases = DB::table('dealer_balance_stock')
					->where('dealer_id',$dealer_id)
					->where('company_id',$company_id)
					->groupBy('product_id')
					->pluck(DB::raw("SUM(stock_case)"),'product_id');

		$stock_qty = DB::table('dealer_balance_stock')
					->where('dealer_id',$dealer_id)
					->where('company_id',$company_id)
					->groupBy('product_id')
					->pluck(DB::raw("SUM(stock_qty)"),'product_id');
		foreach ($details_array as $key => $value) 
		{
			$details['product_id'] = $value->product_id;
			$details['product_name'] = $value->product_name;
			$details['stock_qty'] = !empty($stock_qty[$value->product_id])?$stock_qty[$value->product_id]:'0';
			$details['stock_case'] = !empty($stock_cases[$value->product_id])?$stock_cases[$value->product_id]:'0';
			$details['dealer_id'] = $dealer_id;
			$finalArr[]	= $details;
		}

		return response()->json(['response' =>True,'message'=>'NotFound!!','data'=>$finalArr]);        

	}

	public function dms_cart_product_update_patanjali(Request $request)
	{
		$validator=Validator::make($request->all(),[
			"dealer_id"=>'required',
			"company_id"=>'required',
			"payment_mode_id"=>'required',
			"amount"=>'required',
			"remarks"=>'required',
			"date"=>'required',
			"time"=>'required',
			"primary_sale_summary"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $primary_sale_summary = json_decode($request->primary_sale_summary);

        if(!empty($primary_sale_summary))
        {
        	foreach ($primary_sale_summary as $key => $value) 
	        {
	        	$myArr = [
	        		'rate' => $value->rate,
	        		'cases' => $value->case,
	        		'pcs' => $value->pcs,
	        		'quantity' => $value->pcs,
	        		'pr_rate'=>$value->case_rate,
	        		'scheme_qty'=> $value->sch_qty,
	        		'total_value'=> $value->value,
	        		'app_flag'=> $value->app_flag,

	        	];
	        	$update_data = DB::table('purchase_order_details')
	    					->where('order_id',$value->order_id)
	    					->where('product_id',$value->product_id)
	    					->update($myArr);
	        
		        $paymentArr = [
		        	'dealer_id'=> $request->dealer_id,
		        	'company_id'=> $request->company_id,
		        	'order_id'=>$value->order_id,
		        	'payment_mode'=> $request->payment_mode_id,
		        	'amount'=> $request->amount,
		        	'remarks_app'=> $request->remarks,
		        	'payment_date'=> $request->date,
		        	'payment_time'=> $request->time,
		        	'bank_branch'=> 'NA',
		        	'cheque_no'=> '0',
		        	'cheque_date'=> '0000-00-00',
		        	'trans_no'=> '0',
		        	'trans_date'=> $request->date,
		        	'remarks'=>'NA',
		        	'user_id'=>'0',

		        ];


	    	}
	    	$update = DB::table('purchase_order')->where('order_id',$primary_sale_summary[0]->order_id)
	    			->update([
	    				'travelling_type_id'=>$request->travelling_type_id,
	    				'travelling_mode_id'=>$request->travelling_mode_id,
	    				

	    			]);
	        $insert_payment = DB::table('payment_collect_dealer')->insert($paymentArr);
            $user_data  = DB::table('dealer_location_rate_list')
                        // ->join('dealer_person_login','dealer_person_login.dealer_id','=','dealer_location_rate_list.dealer_id')
                        ->join('person','person.id','=','dealer_location_rate_list.user_id')
                        ->join('users','users.id','=','dealer_location_rate_list.user_id')
                        ->select(DB::raw("concat_ws(' ',first_name,middle_name,last_name) as user_name"),'person.dms_token','person.id as user_id')
                        ->where('is_admin','!=',1)
                        ->where('dealer_location_rate_list.company_id','=',$request->company_id)
                        ->where('dealer_location_rate_list.dealer_id',$request->dealer_id)
                        ->get();
            // dd($user_data);
            $token_data = DB::table('dealer_person_login')->where('dealer_id',$request->dealer_id)->first();
            $fcm_token = !empty($token_data->dms_token)?$token_data->dms_token:'';
            $msg = 'Your orderid is confirmed and order id is :- '.$primary_sale_summary[0]->order_id;
            $data = [
                        'msg' => $msg,
                        'body' => $msg,
                        'title' => 'Order Place',
                        'flag' => '1',
                        'flag_means' => 'product_update_cart',
                        'sound' => 'mySound'/*Default sound*/

                    ];
                
            $notification = self::sendNotification($fcm_token, $data);
            $notification_return_details = json_decode($notification);
            // dd($notification_return_details->success);
            if($notification_return_details->success == 1)
            {
                $insert_data = DB::table('dms_notification_details')
                        ->insert([
                            'user_id'=>0,
                            'dealer_id'=>$request->dealer_id,
                            'title'=>'Order Place',
                            'msg' => $msg,
                            'body' => $msg,
                            'flag' => '1',
                            'flag_means' => 'Order_Place',
                            'order_id'=> $primary_sale_summary[0]->order_id,
                            'company_id'=>$request->company_id,
                            'notification_status' => 1, 
                            'created_at'=>date('Y-m-d H:i:s'),
                        ]);
            }

            foreach ($user_data as $key => $value) 
            {
                $fcm_token = $value->dms_token;
                $msg = 'Your orderid is confirmed and order id is :- '.$primary_sale_summary[0]->order_id;
                $data = [
                            'msg' => $msg,
                            'body' => $msg,
                            'title' => 'Order Place',
                            'flag' => '1',
                            'flag_means' => 'Order_Place',
                            'sound' => 'mySound'/*Default sound*/

                        ];
                
                $notification = self::sendNotification($fcm_token, $data);
                $notification_return_details = json_decode($notification);
                // dd($notification_return_details->success);
                if($notification_return_details->success == 1)
                {
                    $insert_data = DB::table('dms_notification_details')
                            ->insert([
                                'user_id'=>$value->user_id,
                                'dealer_id'=>0,
                                'title'=>'Order Place',
                                'msg' => $msg,
                                'body' => $msg,
                                'flag' => '1',
                                'flag_means' => 'Order_Place',
                                'order_id'=> $primary_sale_summary[0]->order_id,
                                'company_id'=>$request->company_id,
                                'notification_status' => 1, 
                                'created_at'=>date('Y-m-d H:i:s'),
                            ]);
                }
            }
            


			return response()->json(['response' =>True,'message'=>'Submitted Successfully!!','notification'=> $notification]);        

        }
		return response()->json(['response' =>False,'message'=>'JSON Blank!!','notification'=> '']);        

	}
    public function dms_cart_product_edit(Request $request)
    {
        $validator=Validator::make($request->all(),[
            // "dealer_id"=>'required',
            "company_id"=>'required',
            "user_id"=>'required',
            "primary_sale_summary"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $primary_sale_summary = json_decode($request->primary_sale_summary);

        if(!empty($primary_sale_summary))
        {
            foreach ($primary_sale_summary as $key => $value) 
            {
                $myArr = [
                    'rate' => $value->rate,
                    'cases' => $value->case,
                    'pcs' => $value->pcs,
                    'quantity' => $value->pcs,
                    'pr_rate'=>$value->case_rate,
                    'scheme_qty'=> $value->sch_qty,
                    'total_value'=> $value->value,
                    'dms_updated_at'=> $request->user_id,

                ];
                $update_data = DB::table('purchase_order_details')
                            ->where('order_id',$value->order_id)
                            ->where('product_id',$value->product_id)
                            ->update($myArr);
            
                

            }
            if($update_data){
                return response()->json(['response' =>True,'message'=>'Submitted Successfully!!']);        
            }
            else{
            return response()->json(['response' =>False,'message'=>'JSON Blank!!']);        
            }

        }
    }
	public function dms_cart_product_update(Request $request)
	{
		$validator=Validator::make($request->all(),[

			"primary_sale_summary"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $primary_sale_summary = json_decode($request->primary_sale_summary);

        if(!empty($primary_sale_summary))
        {
        	foreach ($primary_sale_summary as $key => $value) 
	        {
	        	$myArr = [
	        		'rate' => $value->rate,
	        		'cases' => $value->case,
	        		'pcs' => $value->pcs,
	        		'quantity' => $value->pcs,
	        		'pr_rate'=>$value->case_rate,
	        		// 'scheme_qty'=> $value->sch_qty,
	        		'total_value'=> $value->value,
	        		'app_flag'=> $value->app_flag,

	        	];
	        	$update_data = DB::table('purchase_order_details')
	    					->where('order_id',$value->order_id)
	    					->where('product_id',$value->product_id)
	    					->update($myArr);
	        
		      
	    	}
	        // $insert_payment = DB::table('payment_collect_dealer')->insert($paymentArr);
			return response()->json(['response' =>True,'message'=>'Submitted Successfully!!']);        

        }
		return response()->json(['response' =>False,'message'=>'JSON Blank!!']);        

	}

	public function dms_draft_product_update(Request $request)
	{
		$validator=Validator::make($request->all(),[
	
			"primary_sale_summary"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $primary_sale_summary = json_decode($request->primary_sale_summary);

        if(!empty($primary_sale_summary))
        {
        	foreach ($primary_sale_summary as $key => $value) 
	        {
	        	$myArr = [
	        		'rate' => $value->rate,
	        		'cases' => $value->case,
	        		'pcs' => $value->pcs,
	        		'quantity' => $value->pcs,
	        		'pr_rate'=>$value->case_rate,
	        		// 'scheme_qty'=> $value->sch_qty,
	        		'total_value'=> $value->value,
	        		'app_flag'=> $value->app_flag,

	        	];
	        	$update_data = DB::table('purchase_order_details')
	    					->where('order_id',$value->order_id)
	    					->where('product_id',$value->product_id)
	    					->update($myArr);
	        
	
	    	}
			return response()->json(['response' =>True,'message'=>'Submitted Successfully!!']);        

        }
		return response()->json(['response' =>False,'message'=>'JSON Blank!!']);        

	}

	public function dms_send_empty_jar_data(Request $request)
	{
		$validator=Validator::make($request->all(),[
			"jar_quantity"=>'required',
			'jar_remarks'=>'required',
			'jar_return_type'=>'required',
			"company_id"=>'required',
			"dealer_id"=>'required',
			"date"=>'required',
			"time"=>'required',
			'order_id'=>'required',
			
		
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $myArr = [
        	'jar_quantity' =>$request->jar_quantity,
        	'jar_remarks' =>$request->jar_remarks,
        	'jar_return_type' =>$request->jar_return_type,
        	'dealer_id' =>$request->dealer_id,
        	'date' =>$request->date,
        	'time' =>$request->time,
        	'company_id' =>$request->company_id,
        	'order_id' =>$request->order_id,
        	'server_date_time' =>date("Y-m-d H:i:s"),

        ];

        $insert = DB::table('dms_dealer_return_quantity')->insert($myArr);
		return response()->json(['response' =>True,'message'=>'Submitted Successfully!!']);        


	}
    public function dms_send_order_enquiry_data(Request $request)
    {
        $validator=Validator::make($request->all(),[
            "remarks"=>'required',
            "company_id"=>'required',
            "dealer_id"=>'required',
            "date"=>'required',
            "time"=>'required',
            'order_id'=>'required',
            'user_id'=>'required',
        
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $check = DB::table('dms_dealer_enquiry_data')->where('order_id',$request->order_id)->count();
        if($check>0)
        {
        	return response()->json(['response' =>False,'message'=>'Duplicate Entry!!']);        
        }
        $myArr = [
            'remarks' =>$request->remarks,
            'dealer_id' =>$request->dealer_id,
            'date' =>$request->date,
            'time' =>$request->time,
            'company_id' =>$request->company_id,
            'order_id' =>$request->order_id,
            'user_id' =>$request->user_id,
            'server_date_time' =>date("Y-m-d H:i:s"),

        ];

        $insert = DB::table('dms_dealer_enquiry_data')->insert($myArr);
        return response()->json(['response' =>True,'message'=>'Submitted Successfully!!']);        


    }
	public function dms_receive_product_update(Request $request)
	{
		$validator=Validator::make($request->all(),[
			'primary_sale_summary'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $primary_sale_summary = json_decode($request->primary_sale_summary);
        // dd($primary_sale_summary);
        $value = $primary_sale_summary;
        if(!empty($value->order_id))
        {
            $myArr = [
                'order_id' =>$value->order_id,
                'dealer_id' =>$value->dealer_id,
                'company_id' =>$value->company_id,
                'product_id' =>$value->product_id,
                'case_rate' =>$value->case_rate,
                'case_fullfillment_qty' =>$value->case_fullfillment_qty,
                'remarks' =>!empty($value->remarks)?$value->remarks:'NA',
                'damage_qty' =>!empty($value->damage_qty)?$value->damage_qty:'NA',
                'server_date_time' =>date('Y-m-d H:i:s'),
                'status' =>1,

            ];
            $finalArr[] = $myArr;
            $dealer_data = DB::table('dealer')->where('id',$value->dealer_id)->first();

            $check = DB::table('stock')
                    ->where('dealer_id',$value->dealer_id)
                    ->where('product_id',$value->product_id)
                    // ->where('company_id',Auth::user()->company_id)
                    ->first();
                    // dd($check);
                $product_details = DB::table('product_rate_list')->where('state_id',$dealer_data->state_id)->where('company_id',$value->company_id)->first();
                if(!empty($check))
                {
                    $update_stock = DB::table('stock')
                                ->where('dealer_id',$value->dealer_id)
                                // ->where('company_id',$company_id)
                                ->where('product_id',$value->product_id)
                                ->update(['qty'=>$check->qty+$value->case_fullfillment_qty]);
                }
                else
                {
                    $insert_stock = DB::table('stock')->insert([
                                    'qty'=>$value->case_fullfillment_qty,
                                    'product_id'=> $value->product_id,
                                    'dealer_id'=> $value->dealer_id,
                                    'mrp' => $product_details->mrp,
                                    'dealer_rate' => $value->case_rate,
                                    'person_id' =>0,
                                    'csa_id'=> $dealer_data->csa_id,
                                    'date'=>date('Y-m-d H:i:s'),
                                    // 'update_date_time '=>date('Y-m-d H:i:s'),
                                    'company_id'=>$value->company_id,
                                ]);

                }

                $check2 = DB::table('dealer_balance_stock')
                    ->where('product_id',$value->product_id)
                    // ->where('company_id',$company_id)
                    ->where('dealer_id',$value->dealer_id)
                    ->first();
                if(!empty($check2))
                {
                    $update_stock2= DB::table('dealer_balance_stock')
                                    ->where('dealer_id',$value->dealer_id)
                                    ->where('product_id',$value->product_id)
                                    // ->where('company_id',$company_id)
                                    ->update([
                                        // 'stock_qty'=>!empty($fullfillment_pcs[$key])?$check2->stock_qty+$fullfillment_pcs[$key]:$check2->stock_qty,
                                        'stock_case'=>$check2->stock_case+$value->case_fullfillment_qty,
                                        'server_date_time'=>date('Y-m-d H:i:s'),
                                    ]);
                }
                else
                {
                    
                    $insert_stock2 = DB::table('dealer_balance_stock')->insert([
                                    'order_id'=>$value->order_id,
                                    'stock_qty'=>0,
                                    'stock_case'=>$value->case_fullfillment_qty,
                                    'product_id'=> $value->product_id,
                                    'dealer_id'=> $value->dealer_id,
                                    'mrp' => $product_details->mrp,
                                    'pcs_mrp' => $product_details->mrp_pcs,
                                    'submit_date_time'=>date('Y-m-d H:i:s'),
                                    'server_date_time'=>date('Y-m-d H:i:s'),
                                    'sstatus'=>1,
                                    'company_id'=>$value->company_id,
                                ]);

                }
        // }
            $insert = DB::table('dms_order_recieved_dealer')->insert($finalArr);
            return response()->json(['response' =>True,'message'=>'Submitted Successfully!!']);  
        }
        else
        {


            foreach ($primary_sale_summary as $key => $value) {
            	# code...
    	        $myArr = [
    	        	'order_id' =>$value->order_id,
    	        	'dealer_id' =>$value->dealer_id,
    	        	'company_id' =>$value->company_id,
    	        	'product_id' =>$value->product_id,
    	        	'case_rate' =>$value->case_rate,
    	        	'case_fullfillment_qty' =>$value->case_fullfillment_qty,
    	        	'remarks' =>!empty($value->remarks)?$value->remarks:'NA',
    	        	'damage_qty' =>!empty($value->damage_qty)?$value->damage_qty:'NA',
    	        	'server_date_time' =>date('Y-m-d H:i:s'),
    	        	'status' =>1,

    	        ];
    	        $finalArr[] = $myArr;
            	$dealer_data = DB::table('dealer')->where('id',$value->dealer_id)->first();

    	        $check = DB::table('stock')
                        ->where('dealer_id',$value->dealer_id)
                        ->where('product_id',$value->product_id)
                        // ->where('company_id',Auth::user()->company_id)
                        ->first();
                        // dd($check);
                    $product_details = DB::table('product_rate_list')->where('state_id',$dealer_data->state_id)->where('company_id',$value->company_id)->first();
                    if(!empty($check))
                    {
                        $update_stock = DB::table('stock')
                                    ->where('dealer_id',$value->dealer_id)
                                    // ->where('company_id',$company_id)
                                    ->where('product_id',$value->product_id)
                                    ->update(['qty'=>$check->qty+$value->case_fullfillment_qty]);
                    }
                    else
                    {
                        $insert_stock = DB::table('stock')->insert([
                                        'qty'=>$value->case_fullfillment_qty,
                                        'product_id'=> $value->product_id,
                                        'dealer_id'=> $value->dealer_id,
                                        'mrp' => $product_details->mrp,
                                        'dealer_rate' => $value->case_rate,
                                        'person_id' =>0,
                                        'csa_id'=> $dealer_data->csa_id,
                                        'date'=>date('Y-m-d H:i:s'),
                                        // 'update_date_time '=>date('Y-m-d H:i:s'),
                                        'company_id'=>$value->company_id,
                                    ]);

                    }

                    $check2 = DB::table('dealer_balance_stock')
                        ->where('product_id',$value->product_id)
                        // ->where('company_id',$company_id)
                        ->where('dealer_id',$value->dealer_id)
                        ->first();
                    if(!empty($check2))
                    {
                        $update_stock2= DB::table('dealer_balance_stock')
                                        ->where('dealer_id',$value->dealer_id)
                                        ->where('product_id',$value->product_id)
                                        // ->where('company_id',$company_id)
                                        ->update([
                                            // 'stock_qty'=>!empty($fullfillment_pcs[$key])?$check2->stock_qty+$fullfillment_pcs[$key]:$check2->stock_qty,
                                            'stock_case'=>$check2->stock_case+$value->case_fullfillment_qty,
                                            'server_date_time'=>date('Y-m-d H:i:s'),
                                        ]);
                    }
                    else
                    {
                        
                        $insert_stock2 = DB::table('dealer_balance_stock')->insert([
                                        'order_id'=>$value->order_id,
                                        'stock_qty'=>0,
                                        'stock_case'=>$value->case_fullfillment_qty,
                                        'product_id'=> $value->product_id,
                                        'dealer_id'=> $value->dealer_id,
                                        'mrp' => $product_details->mrp,
                                        'pcs_mrp' => $product_details->mrp_pcs,
                                        'submit_date_time'=>date('Y-m-d H:i:s'),
                                        'server_date_time'=>date('Y-m-d H:i:s'),
                                        'sstatus'=>1,
                                        'company_id'=>$value->company_id,
                                    ]);

                    }
    	    }
        }
        $insert = DB::table('dms_order_recieved_dealer')->insert($finalArr);
		return response()->json(['response' =>True,'message'=>'Submitted Successfully!!']);        
        

	}
	public function complaint_feedback_array(Request $request)
	{
		$validator=Validator::make($request->all(),[
			'company_id'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $data = DB::table('_complaint_type')
        		->select('id','name')
        		// ->where('status',1)
        		->get();

		return response()->json(['response' =>True,'data'=>$data,'message'=>'Found!!']);        



	}

	public function complaint_feedback_submit(Request $request)
	{
		$validator=Validator::make($request->all(),[
			'company_id'=>'required',
			'order_id'=>'required',
			'dealer_id'=>'required',
			'feedback_complaint_id'=>'required',
			'remarks'=>'required',
			'date'=>'required',
			'time'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $check = DB::table('complaint_feedback_data')->where('order_id',$request->order_id)->count();
        if($check>0)
        {
            return response()->json(['response' =>False,'message'=>'Duplicate Entry!!']);        
        }
        $insert_data = DB::table('complaint_feedback_data')->insert([

        	'company_id'=>$request->company_id,
        	'order_id'=> $request->order_id,
        	'dealer_id'=>$request->dealer_id,
        	'retailer_id'=>0,
        	'date'=>$request->date,
        	'time'=>$request->time,
        	'remarks'=>$request->remarks,
        	'complaint_feedback_id'=>$request->feedback_complaint_id,
        	'server_date_time'=>date('Y-m-d H:i:s'),

        ]);
		return response()->json(['response' =>True,'message'=>'Submitted Successfully!!']);        


	}

	public function dms_order_cancel_reason(Request $request)
	{
		$validator=Validator::make($request->all(),[
			'company_id'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $data = DB::table('dms_order_cancel_reason')
        		->select('id','name as reason')
        		->where('status',1)
        		->where('company_id',$request->company_id)
        		->get();

		return response()->json(['response' =>True,'data'=>$data,'message'=>'Found!!']);        



	}

	public function dms_cancel_order_update(Request $request)
	{
		$validator=Validator::make($request->all(),[
			'company_id'=>'required',
			'order_id'=>'required',
			'status_id'=>'required',
			'order_cancel_reason_id'=>'required',
			'remarks'=>'required',
			'date_time'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $fisrt_insert = DB::table('dms_order_reason_log')->insert([
        				'order_id' =>$request->order_id,
        				'dms_reason_id' =>$request->status_id,
        				'date' =>date('Y-m-d'),
        				'time' =>date('H:i:s'),
        				'company_id' =>$request->company_id,
        				'server_date_time' =>date('Y-m-d H:i:s'),
        		]);
    	$layer_updte = DB::table('purchase_order')
    				->where('order_id',$request->order_id)
    				->update(['dms_order_reason_id'=>$request->status_id,'cancel_order_reason_id'=>$request->order_cancel_reason_id,'remarks'=>$request->remarks,'date_time'=>$request->date_time]);

		if($layer_updte && $fisrt_insert)
		{
			return response()->json(['response' =>True,'message'=>'Submitted!!']);        

		}
		else
		{
			return response()->json(['response' =>False,'message'=>'Not Submitted!!']);        

		}
	}

	public function dms_counter_sale_submit(Request $request)
	{
		$validator=Validator::make($request->all(),[
            "order_id"=>'required',
			"dealer_id"=>'required',
			"sale_date"=>'required',
			"created_date"=>'required',
			"date_time"=>'required',
			"battery_status"=>'required',
			"gps_status"=>'required',
			"lat"=>'required',
			"lng"=>'required',
			"address"=>'required',
			"mcc_mnc_lac_cellid"=>'required',
			"user_id"=>'required',
            "company_id"=>'required',
			"remarks"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

		$order_id = $request->order_id;
		$dealer_id = $request->dealer_id;
		$sale_date = $request->sale_date;
		$created_date = $request->created_date;
		$date_time = $request->date_time;
		$battery_status = $request->battery_status;
		$gps_status = $request->gps_status;
		$lat = $request->lat;
		$lng = $request->lng;
		$address = $request->address;
		$mcc_mnc_lac_cellid = $request->mcc_mnc_lac_cellid;
		$user_id = $request->user_id;
        $company_id = $request->company_id;
        $remarks = $request->remarks;
        // dd($request->primary_sale_summary);
        $primary_sale_summary = json_decode($request->primary_sale_summary);
        // dd($primary_sale_summary);
		DB::beginTransaction();
		$myArr = [
				'order_id'=>$order_id,
				'dealer_id'=>$dealer_id,
				'created_by_person'=>0,
				'retailer_id'=>0,
				'sale_date'=>$sale_date,
				'created_date'=>$created_date,
				'date_time'=>$date_time,
				'battery_status'=>$battery_status,
				'gps_status'=>$gps_status,
				'lat'=>$lat,
				'lng'=>$lng,
                'address'=>$address,
				'counter_remarks'=>$remarks,
				'mcc_mnc_lac_cellid'=>$mcc_mnc_lac_cellid,
				'company_id'=>$company_id,
				'server_date'=>date('Y-m-d H:i:s'),

			];

		foreach ($primary_sale_summary as $key => $value) {
                // dd($value->order_id);
				$detailsArr = [
					'order_id' => $value->order_id,
					'product_id' => $value->product_id,
					'rate' => $value->rate,
					'pcs_rate' => $value->rate,
					'quantity' => $value->quantity,
					'barcode' => $value->barcode,
					'scheme_qty' => $value->scheme_qty,
					'cases' => $value->case,
					'pcs' => $value->pcs,
					'value' => $value->value,
					'case_rate' => $value->case_rate,
					'company_id'=>$company_id,
					'app_flag' => $value->app_flag,
					'created_by'=>0,
					'server_date_time'=>date('Y-m-d H:i:s'),
                ];
                // dd($detailsArr);
                
				$insertDetailsArr[] = $detailsArr;

            $check = DB::table('stock')
                    ->where('dealer_id',$dealer_id)
                    ->where('product_id',$value->product_id)
                    ->first();
            // $product_details = DB::table('product_rate_list')->where('state_id',$dealer_data->state_id)->where('company_id',$company_id)->first();
            if(!empty($check) && isset($check))
            {
                $update_stock = DB::table('stock')
                            ->where('dealer_id',$dealer_id)
                            // ->where('company_id',$company_id)
                            ->where('product_id',$value->product_id)
                            ->update(['qty'=>$check->qty-$value->case]);
            }


            $check2 = DB::table('dealer_balance_stock')
                ->where('product_id',$value->product_id)
                // ->where('company_id',$company_id)
                ->where('dealer_id',$dealer_id)
                ->first();
            if(!empty($check2) && isset($check2))
            {
                $update_stock2= DB::table('dealer_balance_stock')
                                ->where('dealer_id',$dealer_id)
                                ->where('product_id',$value->product_id)
                                // ->where('company_id',$company_id)
                                ->update([
                                    // 'stock_qty'=>$check2->stock_qty-$fullfillment_pcs[$key]:$check2->stock_qty,
                                    'stock_case'=>$check2->stock_case-$value->case,
                                    'server_date_time'=>date('Y-m-d H:i:s'),
                                ]);
            }

		}
		$insert_order = DB::table('counter_sale_summary')->insert($myArr);
		$insert_order_details = DB::table('counter_sale_details')->insert($insertDetailsArr);
		if($insert_order && $insert_order_details)
		{
			DB::commit();
			return response()->json(['response' =>True,'message'=>'Submitted!!']);        


		}
		else
		{
			DB::rollback();
			return response()->json(['response' =>False,'message'=>'Not Submitted!!']);        

		}


	}

    public function dms_vehicle_type(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            "company_id"=>'required',
            "dealer_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   
        $data = DB::table('dealer_vechicle_assign')
                ->join('_vehicle_details','_vehicle_details.id','=','dealer_vechicle_assign.vehicle_details_id')
                ->select('_vehicle_details.*')
                ->where('dealer_id',$request->dealer_id)
                ->where('dealer_vechicle_assign.company_id',$request->company_id)
                ->where('_vehicle_details.company_id',$request->company_id)
                ->where('_vehicle_details.status',1)
                ->get();
        // $data = DB::table('_vehicle_details')->where('company_id',$request->company_id)->where('status',1)->get();
        return response()->json(['response' =>True,'message'=>'Found!!','data'=>$data]);        

    }

    public function dms_social_master_data(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            "company_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   

        $data = DB::table('dms_social_form_master')
                ->where('status',1)
                ->where('company_id',$request->company_id)
                ->get();
        return response()->json(['response' =>True,'message'=>'Found!!','data'=>$data]);        

    }
    public function dms_aboutus_data(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            "company_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   

        $data = DB::table('dms_about_us_master')
                ->where('status',1)
                ->where('company_id',$request->company_id)
                ->get();
        return response()->json(['response' =>True,'message'=>'Found!!','data'=>$data]);        

    }
    public function dms_notification_data(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            "company_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   

        $data = DB::table('dms_notification_data')
                ->where('status',1)
                ->where('company_id',$request->company_id)
                ->get();
        return response()->json(['response' =>True,'message'=>'Found!!','data'=>$data]);        

    }
    public function dms_feedback_form_data_submit(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            "company_id"=>'required',
            "name"=>'required',
            "mobile_no"=>'required|min:10|max:10',
            "message"=>'required',
            "created_by"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   
        $data_submit = DB::table('dms_feedback_data')
                    ->insert([
                        'company_id' => $request->company_id,
                        'name' => $request->name,
                        'message' => $request->message,
                        'mobile_no' => $request->mobile_no,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $request->created_by,


                    ]);

        if($data_submit)
        {
            return response()->json(['response' =>True,'message'=>'submitted!!']);        
        }
        else
        {
            return response()->json(['response' =>False,'message'=>'not submitted!!']);        
        }

    } 
    public function dms_feedback_form_data(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            "company_id"=>'required',
            "dealer_id"=>'required',
            
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   
        $data_submit = DB::table('dms_feedback_data')
                    ->where('created_by',$request->dealer_id)
                    ->where('company_id',$request->company_id)
                    ->get();
                    

        if(!empty($data_submit))
        {
            return response()->json(['response' =>True,'message'=>'Found!!' ,'data'=>$data_submit]);        
        }
        else
        {
            return response()->json(['response' =>False,'message'=>'not Found!!','data'=>$data_submit]);        
        }

    } 
    public function dms_feedback_form_data_update(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            "company_id"=>'required',
            "name"=>'required',
            "mobile_no"=>'required|min:10|max:10',
            "message"=>'required',
            "updated_by"=>'required',
            "primary_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   
        $data_submit = DB::table('dms_feedback_data')
                    ->where('id',$request->primary_id)
                    ->where('company_id',$request->company_id)
                    ->update([
                        'name' => $request->name,
                        'message' => $request->message,
                        'mobile_no' => $request->mobile_no,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => $request->created_by,


                    ]);

        if($data_submit)
        {
            return response()->json(['response' =>True,'message'=>'updated!!']);        
        }
        else
        {
            return response()->json(['response' =>False,'message'=>'not updated!!']);        
        }

    } 

    public function dms_dealer_profile_details(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            "company_id"=>'required',
            "dealer_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   

        $dealer_data = DB::table('dealer_personal_details')
                    ->join('dealer','dealer.id','=','dealer_personal_details.dealer_id')
                    ->select('dealer_personal_details.*','dealer.tin_no','dealer.fssai_no','dealer.dealer_code','dealer.name as dealer_name','dealer.address','dealer.pin_no','dealer_code','dealer.contact_person','dealer.email')
                    ->where('dealer_personal_details.dealer_id',$request->dealer_id)
                    ->where('dealer_personal_details.company_id',$request->company_id)
                    ->get();

        $dealer_doc_data = DB::table('dms_dealer_document_details')
                    ->join('dms_document_master','dms_document_master.id','=','dms_dealer_document_details.document_id')
                    ->select('dms_dealer_document_details.*','dms_document_master.name as document_name')
                    ->where('dms_dealer_document_details.dealer_id',$request->dealer_id)
                    ->where('dms_dealer_document_details.company_id',$request->company_id)
                    ->get();

        return response()->json(['response' =>True,'message'=>'Found!!','dealer_data'=> $dealer_data,'dealer_doc_data'=> $dealer_doc_data]);        

    }
    public function dms_dealer_profile_details_submit(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            'dealer_code'=>'required',
            'company_id'=>'required',
            'dealer_id'=>'required',
            'dealer_name'=>'required',
            'contact_person'=>'required',
            'email'=>'required',
            'pancard'=>'required',
            'gstin'=>'required',
            'fssai'=>'required',
            'aadhar_no'=>'required',
            'address'=>'required',
            'city'=>'required',
            'pincode'=>'required',
            'date'=>'required',
            'time'=>'required',
            'user_id'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   
            $dealer_code = $request->dealer_code;
            $company_id = $request->company_id;
            $dealer_id = $request->dealer_id;
            $dealer_name = $request->dealer_name;
            $contact_person = $request->contact_person;
            $email = $request->email;


            $pancard = $request->pancard;


            $gstin = $request->gstin;

            $fssai = $request->fssai;
            $aadhar_no = $request->aadhar_no;
            $address = $request->address;
            $city = $request->city;
            $pincode = $request->pincode;
            $date = $request->date;
            $time = $request->time;
            $user_id = $request->user_id;
        DB::beginTransaction();
        $data = Dealer::where('id',$dealer_id)
                ->update([
                    'name' => $dealer_name,
                    'contact_person' => $contact_person,
                    'email' => $email,
                    'tin_no' => $gstin,
                    'fssai_no' => $fssai,
                    'address' => $address,
                    'pin_no' => $pincode,
                    'dealer_code' => $dealer_code,

                ]);
       

        if($data)
        {

             $data_personla = DB::table('dealer_personal_details')->where('dealer_id',$dealer_id)
                ->update([
                    'city' => $city,
                    'pan_no' => $pancard,
                    'aadar_no' => $aadhar_no,
                    'updated_at' => $date.' '.$time,
                    'updated_by' => $user_id,
                    

                ]);
            DB::commit();

            return response()->json(['response' =>True,'message'=>'Updated!!']);        

        }
        else
        {
            DB::rollback();
            return response()->json(['response' =>False,'message'=>'Not Updated!!']);        
        }



    }

    public function dms_dealer_bank_details_submit(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            'company_id'=>'required',
            'dealer_id'=>'required',
            'bank_name' =>'required',
            'account_no' =>'required',
            'ifsc_code' =>'required',
            'branch_name' =>'required',
            'date'=>'required',
            'time'=>'required',
            'user_id'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   
            $company_id = $request->company_id;
            $dealer_id = $request->dealer_id;
            $bank_name = $request->bank_name;
            $account_no = $request->account_no;
            $ifsc_code = $request->ifsc_code;
            $branch_name = $request->branch_name;
            $date = $request->date;
            $time = $request->time;
            $user_id = $request->user_id;


        DB::beginTransaction();
         $data_personla = DB::table('dealer_personal_details')->where('dealer_id',$dealer_id)
                        ->update([
                            'bank_name' => $bank_name,
                            'account_no' => $account_no,
                            'ifsc_code' => $ifsc_code,
                            'branch_name' => $branch_name,
                           
                            'updated_at' => $date.' '.$time,
                            'updated_by' => $user_id,
                            

                        ]);
       

        if($data_personla)
        {

            
            DB::commit();

            return response()->json(['response' =>True,'message'=>'Updated!!']);        

        }
        else
        {
            DB::rollback();
            return response()->json(['response' =>False,'message'=>'Not Updated!!']);        
        }
    }
	
    public function dms_dealer_image_submit(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            'company_id'=>'required',
            'dealer_id'=>'required',
            'image_source'=>'required',
            
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   

        if ($request->hasFile('image_source')) {
          
            try {

                $files = $request->file('image_source');
                $inc = 0;

                foreach($files as $file_key => $file)
                {
                    $name_random = date('YmdHis').$inc;
                    $str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
                    $random_no = substr($str, 0,2);  // return always a new string 
                    $custom_image_name = date('YmdHis').$random_no;
                    $imageName = $custom_image_name . '.' . $file->getClientOriginalExtension();
                    $file_name[] = $imageName;
                    $destinationPath = public_path('/dealer_documents/');
                    $file->move($destinationPath , $imageName);
                    if($file_key == 0) // for pancard
                    {
                        $document_id = 1;

                        $delete_data = DB::table('dms_dealer_document_details')
                                ->where('dealer_id',$request->dealer_id)
                                ->where('document_id',$document_id)
                                ->delete();

                        $personImage = DB::table('dms_dealer_document_details')->insert([
                                    'document_image' => 'dealer_documents/'.$imageName,
                                    'document_id' => $document_id,
                                    'dealer_id' => $request->dealer_id,
                                    'company_id' => $request->company_id,
                                    'date' => date('Y-m-d'),
                                    'time' => date('H:i:s'),
                                    'server_date_time' => date('Y-m-d H:i:s'),
                                ]);
                    }
                    elseif($file_key == 1) // for addhar card
                    {
                        $document_id = 2;
                        $delete_data = DB::table('dms_dealer_document_details')
                                ->where('dealer_id',$request->dealer_id)
                                ->where('document_id',$document_id)
                                ->delete();

                        $personImage = DB::table('dms_dealer_document_details')->insert([
                                        'document_image' => 'dealer_documents/'.$imageName,
                                    'document_id' => $document_id,
                                    'dealer_id' => $request->dealer_id,
                                    'company_id' => $request->company_id,
                                    'date' => date('Y-m-d'),
                                    'time' => date('H:i:s'),
                                    'server_date_time' => date('Y-m-d H:i:s'),
                                ]);
                    }
                    elseif($file_key == 2) // for gst card
                    {
                        $document_id = 4;
                        $delete_data = DB::table('dms_dealer_document_details')
                                ->where('dealer_id',$request->dealer_id)
                                ->where('document_id',$document_id)
                                ->delete();

                        $personImage = DB::table('dms_dealer_document_details')->insert([
                                    'document_image' => 'dealer_documents/'.$imageName,
                                    'document_id' => $document_id,
                                    'dealer_id' => $request->dealer_id,
                                    'company_id' => $request->company_id,
                                    'date' => date('Y-m-d'),
                                    'time' => date('H:i:s'),
                                    'server_date_time' => date('Y-m-d H:i:s'),
                                ]);
                    }
                    elseif($file_key == 3) // for fssai card
                    {
                        $document_id = 5;
                        $delete_data = DB::table('dms_dealer_document_details')
                                ->where('dealer_id',$request->dealer_id)
                                ->where('document_id',$document_id)
                                ->delete();

                        $personImage = DB::table('dms_dealer_document_details')->insert([
                                    'document_image' => 'dealer_documents/'.$imageName,
                                    'document_id' => $document_id,
                                    'dealer_id' => $request->dealer_id,
                                    'company_id' => $request->company_id,
                                    'date' => date('Y-m-d'),
                                    'time' => date('H:i:s'),
                                    'server_date_time' => date('Y-m-d H:i:s'),
                                ]);
                    }
                    
                    $inc++;

                }

              
            } catch (Illuminate\Filesystem\FileNotFoundException $e) {

            }
           
            
            return response()->json(['response' =>True,'message'=>'Submitted!!']);        
        }
            
        else {
            
            return response()->json(['response' =>False,'message'=>'Not Submitted!!']);        
        }
    }
    public function sendNotification($fcm_token, $data)
    {
        $url = "https://fcm.googleapis.com/fcm/send";
        $fields = array(
            'to' => $fcm_token,
            'notification' => $data,
            'data' => ['complaint_id' =>  'Test', 'notify_type' => 1], #1 for complaint notification
           

        );
        // dd(json_encode($fields));
        $headers = array(
            // 'Authorization: key= ' . config('app.FCM_API_ACCESS_KEY'),
            'Authorization: key= AAAAUrhAVlA:APA91bHEHv86QD2n1Fqe96lulbR3DxljuaZ0RvNYHj5__X_73r7IO1Br-G0ucFpxrvAD9XPN-LGaFTMVEWP728qKvjHBt0UwqMqD-WfFcGlpikbsD4nEtUJNiIV7d0x0l6waPOJBMPiy',
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
        // if ($result === FALSE) {
        //     die('Curl Failed: ' . curl_error($ch));
        // }
        // dd($result);
        return $result;
    }
    public function dms_plant_details(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            'company_id'=>'required',
            
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   
        $company_id = $request->company_id;
        $data = DB::table('_dms_plant_master')
                ->where('status',1)
                ->where('company_id',$company_id)
                ->get();

        return response()->json(['response' =>True,'message'=>'Found!!','data'=> $data]);        

    }
    public function dms_plant_stock_details(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            'company_id'=>'required',
            'plant_id'=>'required',
            
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   
        $company_id = $request->company_id;
        $plant_id = $request->plant_id;
        $data = DB::table('dms_plant_stock')
                ->join('catalog_product','catalog_product.id','=','dms_plant_stock.product_id')
                ->select('dms_plant_stock.*','catalog_product.name as product_name')
                ->where('dms_plant_stock.status',1)
                ->where('dms_plant_stock.company_id',$company_id)
                ->where('dms_plant_stock.plant_id',$plant_id)
                ->get();

        return response()->json(['response' =>True,'message'=>'Found!!','data'=> $data]);        

    }
    public function dms_contacts_custom(Request $request)
    {
        $company_id = $request->company_id;
        $second_layer = DB::table('dms_contact_details')->where('status',1)->where('company_id',$company_id)->pluck('link','name');
        return response()->json(['response' =>True,'message'=>'Found!!','data'=> $second_layer]);        

    }
    public function dms_inner_notification_details(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            'company_id'=>'required',
            'dealer_id'=>'required',
            'user_id'=>'required',
            
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   
        $company_id = $request->company_id;
        $dealer_id = $request->dealer_id;
        $user_id = $request->user_id;
        if($user_id == 0)
        {
            $data = DB::table('dms_notification_details')
                ->where('company_id',$company_id)
                ->where('dealer_id',$dealer_id)
                ->get();
        }
        elseif($dealer_id == 0)
        {
            $data = DB::table('dms_notification_details')
                ->where('company_id',$company_id)
                ->where('user_id',$user_id)
                ->get();
        }
        else
        {
            $data = array();
        }
        
        return response()->json(['response' =>True,'message'=>'Found!!','data'=> $data]);        

    }

     public function dms_dealer_beat(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            'company_id'=>'required',
            'dealer_id'=>'required',
            
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   
        $company_id = $request->company_id;
        $dealer_id = $request->dealer_id;

        $data = DB::table('dealer_location_rate_list')
                ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
                ->select('location_7.id as id','location_7.name as name')
                ->where('dealer_location_rate_list.company_id',$company_id)
                ->where('location_7.company_id',$company_id)
                ->where('dealer_location_rate_list.dealer_id',$dealer_id)
                ->groupBy('location_7.id')
                ->get();


        return response()->json(['response' =>True,'message'=>'Found!!','data'=> $data]);        

    }

      public function dms_dealer_beat_retailer(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            'company_id'=>'required',
            'beat_id'=>'required',
            
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   
        $company_id = $request->company_id;
        $beat_id = $request->beat_id;
        $date = date('Y-m-d');
        $sending_array = array();
        $currmonth = date('Y-m');
        

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

        // dd($check_retailer_sales);
        $data = DB::table('retailer')
                ->select('id','name','contact_per_name','other_numbers','address','is_golden')
                ->where('location_id',$beat_id)
                ->where('company_id',$company_id)
                ->where('retailer_status',1)
                ->groupBy('retailer.id')
                ->get();


        foreach ($data as $key => $value) {
            $retailer_id = $value->id; 

            if(empty($check_retailer_productive_sales[$retailer_id]) && empty($check_retailer_non_productive_sales[$retailer_id])){
                $sales_status = '0'; // for not sale anything
            }elseif (!empty($check_retailer_productive_sales[$retailer_id])) {
                $sales_status = '1'; // for productive sale
            }elseif (!empty($check_retailer_non_productive_sales[$retailer_id])) {
                $sales_status = '2'; // for visit only
            }

            $lastSaleId = array();
            $lastSaleId = DB::table('user_sales_order')
                        ->select('user_sales_order.order_id')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->where('retailer_id',$retailer_id)
                        ->where('user_sales_order.company_id',$company_id)
                        // ->groupBy('retailer_id')
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
           // $sales_status = !empty($check_retailer_sales[$retailer_id])?'1':'0'; // 1 for sales done on retailer 0 for not


            $final_array['id'] = $value->id; 
            $final_array['name'] = $value->name; 
            $final_array['is_golden'] = $value->is_golden; 
             $final_array['contact_per_name'] = $value->contact_per_name; 
            $final_array['contact_number'] = $value->other_numbers; 
            $final_array['address'] = $value->address; 
            $final_array['sale_status'] = $sales_status; 

            $final_array['last_invoice_date'] = $lastDate.' '.$lastTime; 
            $final_array['last_bill_value'] = !empty($lastDetails->lastSale)?$lastDetails->lastSale:''; 
            $final_array['sale_person'] = !empty($lastDetails->user_name)?$lastDetails->user_name:''; 

            $final_array['monthly_bill_value'] = $monthBill; 
            $final_array['total_bill_box'] = $monthBillBox; 
            $final_array['total_gift_deliever'] = '0'; 

            $sending_array[] = $final_array;
        }

      

        return response()->json(['response' =>True,'message'=>'Found!!','data'=> $sending_array]);        

    }

        public function dms_secondary_supply(Request $request)
    {
        $validator=Validator::make($request->all(),[
            "order_id"=>'required',
            'dealer_id'=>'required',
            'location_id'=>'required',
            "retailer_id"=>'required',
            "call_status"=>'required',
            "non_productive_id"=>'required',
            "total_sale_value"=>'required',
            'total_sale_qty'=>'required',
            'lat_lng'=>'required',
            'mccmnclatcellid'=>'required',
            'track_address'=>'required',
            'date'=>'required',
            'time'=>'required',
            'battery_status'=>'required',
            'gps_status'=>'required',
            'discount'=>'required',
            'geo_status'=>'required',
            'address'=>'required',
            'finalvalue'=>'required',
            'override_status'=>'required',
            // 'remarks'=>'required',
            'user_id'=>'required',
            'company_id'=>'required',
            'direct_sale_summary'=>'required',
            
        
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $detailsArray = json_decode($request->direct_sale_summary);
        $date =$request->date;
        $company_id =$request->company_id;

        $data_details = DB::table('user_sales_order')->whereRaw("date_format(date,'%Y-%m-%d')='$date'")->where('company_id',$company_id)->where("retailer_id",$request->retailer_id)->get();
        if(!empty($data_details)){
            foreach ($data_details as $key => $value) {
                // code...
                $data_details_one = DB::table('user_sales_order_details')->where('company_id',$company_id)->where("order_id",$value->order_id)->delete();
            }
            $data_details = DB::table('user_sales_order')->whereRaw("date_format(date,'%Y-%m-%d')='$date'")->where('company_id',$company_id)->where("retailer_id",$request->retailer_id)->delete();
        }

        if(!empty($detailsArray)){
            $callStatus = '1';
        }else{
            $callStatus = '0';
        }
        
        $myArr = [
            'order_id' =>$request->order_id,
            'user_id' =>$request->user_id,
            'dealer_id' =>$request->dealer_id,
            'location_id' =>$request->location_id,
            'retailer_id' =>$request->retailer_id,
            'company_id' =>$request->company_id,
            // 'call_status' =>$request->call_status,
            'call_status' =>$callStatus,
            'total_sale_value' =>$request->total_sale_value,
            'discount' =>$request->discount,
            'amount' =>$request->total_sale_value,
            'total_sale_qty' =>$request->total_sale_qty,
            'lat_lng' =>$request->lat_lng,
            'geo_status' =>$request->geo_status,
            'track_address' =>$request->track_address,
            'date' =>$request->date,
            'time' =>$request->time,
            'override_status' =>$request->override_status,
            // 'order_status' =>$request->address,
            'remarks' =>!empty($request->remarks)?$request->remarks:'',
            'battery_status' =>$request->battery_status,
            'gps_status' =>$request->gps_status,
            'total_dispatch_qty' =>"0",
            'order_status' =>"0",
            // 'reason' =>$request->user_id,
            // 'company_id' =>$request->company_id,
            // 'server_date_time' =>date("Y-m-d H:i:s"),


        ];
        $check = DB::table('user_sales_order')->where("order_id",$request->order_id)->get();
        if(COUNT($check)>0)
        {
            return response()->json(['response' =>False,'message'=>'Duplicate Entry Check And then Submit Again !!']);        
        }
        $insert = DB::table('user_sales_order')->insert($myArr);


        if(!empty($detailsArray)){
            foreach ($detailsArray as $key => $value) {

                $myDetailsArr[] = [
                    'company_id' => $request->company_id,
                    'order_id' => $value->order_id,
                    'product_id' => $value->product_id,
                    'rate' => $value->rate,
                    'quantity' => $value->quantity,
                    'scheme_qty' => $value->scheme_qty,
                    'product_value' => $value->product_value,
                ];


            }
            $insertDetails = DB::table('user_sales_order_details')->insert($myDetailsArr);
        }


        if($insert)
        {
            return response()->json(['response' =>True,'message'=>'Submitted!!']);        
        }
        else
        {
            return response()->json(['response' =>False,'message'=>'Not Submitted!!']);        
        }      


    }


         public function dms_dealer_detail(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            'company_id'=>'required',
            'dealer_id'=>'required',
            
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   
        $company_id = $request->company_id;
        $dealer_id = $request->dealer_id;

        $data = DB::table('dealer')
                ->join('dealer_personal_details','dealer_personal_details.dealer_id','=','dealer.id')
                ->select('dealer.*','dealer_personal_details.pan_no as gst_no','dealer_personal_details.aadar_no as aadhar_no')
                ->where('dealer.id',$dealer_id)
                ->where('dealer.company_id',$company_id)
                ->groupBy('dealer.id')
                ->first();



        return response()->json(['response' =>True,'message'=>'Found!!','data'=> $data]);        

    }

     public function dms_retailer_detail(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            'company_id'=>'required',
            'retailer_id'=>'required',
            
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   
        $company_id = $request->company_id;
        $retailer_id = $request->retailer_id;

        $data = DB::table('retailer')
                ->select('retailer.*',DB::raw("IF(retailer.contact_per_name IS NULL,'',retailer.contact_per_name) as contact_person"),DB::raw("IF(retailer.landline IS NULL,'',retailer.landline) as landline"),DB::raw("IF(retailer.other_numbers IS NULL,'',retailer.other_numbers) as other_numbers"))
                ->where('retailer.id',$retailer_id)
                ->where('retailer.company_id',$company_id)
                ->groupBy('retailer.id')
                ->first();



        return response()->json(['response' =>True,'message'=>'Found!!','data'=> $data]);        

    }



      public function attendenceDetails(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            'company_id'=>'required',
            'user_id'=>'required',
            'date'=>'required',
            
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   
        $company_id = $request->company_id;
        $user_id = $request->user_id;
        $date = $request->date;

        $data = DB::table('user_daily_attendance')
                ->select(DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d') as work_date"),DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as work_time"))
                ->where('user_id',$user_id)
                ->where('company_id',$company_id)
                ->whereRaw("date_format(work_date,'%Y-%m-%d')='$date'")
                ->first();


  
    if($data){
        return response()->json(['response' =>True,'message'=>'Found!!','data'=> $data]);        

    }else{
        return response()->json(['response' =>False,'message'=>'Not Found!!','data'=> array()]);        
    }


    }





      public function dms_distributor_wise_details(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            'company_id'=>'required',
            'user_id'=>'required',
            'dealer_id'=>'required',
            
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   
        $company_id = $request->company_id;
        $user_id = $request->user_id;
        $dealer_id = $request->dealer_id;
        $currmonth = date('Y-m');



        $data = DB::table('dealer')
                ->where('company_id',$company_id)
                ->where('id',$dealer_id)
                ->first();

        $lastSaleId = DB::table('user_sales_order')
                    ->select('user_sales_order.order_id')
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                    ->where('dealer_id',$dealer_id)
                    ->where('user_sales_order.company_id',$company_id)
                    ->orderBy('user_sales_order.date','DESC')
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
                    ->where('user_sales_order.dealer_id',$dealer_id)
                    ->where('user_sales_order.company_id',$company_id)
                    ->whereRaw("date_format(date,'%Y-%m')='$currmonth'")
                    ->groupBy('user_sales_order.order_id')
                    ->first();

        $monthBill = !empty($lastMonthDetails->Sale)?$lastMonthDetails->Sale:'';
        $monthBillBox = !empty($lastMonthDetails->Quantity)?$lastMonthDetails->Quantity:'';


        $finalArray['distributor_name'] = $data->name;
        $finalArray['contact_person'] = $data->contact_person;
        $finalArray['contact_number'] = $data->other_numbers;
        $finalArray['address'] = $data->address;
        $finalArray['last_invoice_date'] = $lastDate.' '.$lastTime; 
        $finalArray['last_bill_value'] = !empty($lastDetails->lastSale)?$lastDetails->lastSale:''; 
        $finalArray['sale_person'] = !empty($lastDetails->user_name)?$lastDetails->user_name:''; 
        $finalArray['monthly_billed_value'] = $monthBill;
        $finalArray['total_billed_box'] = $monthBillBox;
        $finalArray['total_gift_deliever'] = '0';

          

  
    if($finalArray){
        return response()->json(['response' =>True,'message'=>'Found!!','data'=> $finalArray]);        

    }else{
        return response()->json(['response' =>False,'message'=>'Not Found!!','data'=> array()]);        
    }


    }

    public function gift_scheme_details(Request $request){

        $validator=Validator::make($request->all(),[
            
            'company_id'=>'required',
            'retailer_id'=>'required',
            'user_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
        
            
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   
        $retailer_id = $request->retailer_id;
        $user_id = $request->user_id;
        $company_id = $request->company_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;


        $step1 = DB::table('scheme_assign_retailer')
                ->join('scheme_plan_details','scheme_plan_details.scheme_id','=','scheme_assign_retailer.plan_id')
                ->where('scheme_assign_retailer.retailer_id',$retailer_id)
                ->where('scheme_assign_retailer.company_id',$company_id)
                ->where('scheme_plan_details.incentive_type',4)
                ->whereRaw("date_format(scheme_assign_retailer.plan_assigned_from_date,'%Y-%m-%d')<='$from_date' AND date_format(scheme_assign_retailer.plan_assigned_to_date,'%Y-%m-%d')>='$to_date'")
                ->groupBy('scheme_assign_retailer.plan_id')
                ->pluck('scheme_plan_details.scheme_id')->toArray();
                // dd($step1);
        if(!empty($step1)){
            
            $step2 = DB::table('scheme_plan_details')
                ->whereIn('scheme_id',$step1)
                ->groupBy('scheme_plan_details.id')
                ->get();

            $step3 = DB::table('user_sales_order')
                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                ->select(DB::raw("SUM(user_sales_order_details.quantity*user_sales_order_details.rate) as sale_value"))
                ->where('user_sales_order.company_id',$company_id)
                ->where('user_sales_order.retailer_id',$retailer_id)
                // ->whereRaw("date_format(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND date_format(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                ->first();
                // dd($step3);
            $step4 = DB::table('gift_retailer_details')
                ->select(DB::raw("SUM(gift_retailer_details.total_value) as total_value"),'scheme_value')
                ->where('gift_retailer_details.company_id',$company_id)
                ->where('gift_retailer_details.retailer_id',$retailer_id)
                // ->whereRaw("date_format(gift_retailer_details.date,'%Y-%m-%d')>='$from_date' AND date_format(gift_retailer_details.date,'%Y-%m-%d')<='$to_date'")
                ->first();

            // dd($step2);
                

            $step3_final = !empty($step3->sale_value)?$step3->sale_value:'0';
            $step4_final = !empty($step4->total_value)?$step4->total_value*$step4->scheme_value:'0';
            // dd();

            $final_step = $step3_final - $step4_final;
            // dd($final_step,$step4_final,$step3_final);

            $set_data = [];
            if(!empty($step2)){
                foreach ($step2 as $key => $value) {
                    // code...
                    if($final_step >= $value->sale_value_range_first){
                        $data_correct['scheme_id'] = !empty($value->scheme_id)?$value->scheme_id:'-';
                        $data_correct['sale_unit'] = !empty($value->sale_unit)?$value->sale_unit:'-';
                        $data_correct['sale_value_range_first'] = !empty($value->sale_value_range_first)?$value->sale_value_range_first:'-';
                        $data_correct['sale_value_range_last'] = !empty($value->sale_value_range_last)?$value->sale_value_range_last:'-';
                        $data_correct['incentive_type'] = !empty($value->incentive_type)?$value->incentive_type:'-';
                        $data_correct['value_amount_percentage'] = !empty($value->value_amount_percentage)?$value->value_amount_percentage:'-';
                        $data_correct['image'] = !empty($value->image)?'/schemeplandetails/'.$value->image:'-';
                        $data_correct['total_balance'] = $final_step;
                        $set_data[] = $data_correct;
                    }


                }
            }
            return response()->json(['response' =>True,'message'=>'Found!!','set_data'=>$set_data]);        
        }
        else{

            return response()->json(['response' =>False,'message'=>'Found!!']);        

        }
        



    }
    public function gift_scheme_submit(Request $request){

        $data_get_json = json_decode($request->scheme_detail_data);
        if(!empty($data_get_json)){
            foreach ($data_get_json as $key => $value) {
                // code...
                $set_details= [
                    'company_id'=>$value->company_id,
                    'user_id'=>$value->user_id,
                    'order_id'=>$value->order_id,
                    'retailer_id'=>$value->retailer_id,
                    'total_value'=>$value->total_value,
                    'scheme_id'=>$value->scheme_id,
                    'scheme_value'=>$value->sale_value_range_first,
                    'server_date_time'=>date('Y-m-d H:i:s'),
                    'date'=>$value->date,
                ];
            }
            $data_submit = DB::table('gift_retailer_details')->insert($set_details);
            return response()->json(['response' =>True,'message'=>'Succefully Submitted!!']);        
        }

        return response()->json(['response' =>False,'message'=>'Not submitted']);        
    }



     public function dealerBalanceStock(Request $request){


         $validator=Validator::make($request->all(),[
            
            'company_id'=>'required',
            'dealer_id'=>'required',
            'user_id'=>'required',
            
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   
        $dealer_id = $request->dealer_id;
        $user_id = $request->user_id;
        $company_id = $request->company_id;

        $assign_state = DB::table('person')->where('id',$user_id)->first();


        $details_array = DB::table('catalog_product')
                        ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
                        ->select('catalog_product.id as product_id','catalog_product.name as product_name','catalog_product.gst_percent','catalog_product.quantity_per_case','product_rate_list.*')
                        ->where('catalog_product.company_id',$company_id)
                        ->where('product_rate_list.company_id',$company_id)
                        ->where('product_rate_list.state_id',$assign_state->state_id)
                        ->groupBy('catalog_product.id')
                        ->get()->toArray();


        $stock_qty = DB::table('dealer_balance_stock')
                    ->where('dealer_id',$dealer_id)
                    ->where('company_id',$company_id)
                    ->groupBy('product_id')
                    ->pluck(DB::raw("SUM(stock_qty)"),'product_id');



        $purchaseOrder = DB::table('purchase_order')
                            ->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
                            ->join ('catalog_product','catalog_product.id','=','purchase_order_details.product_id')
                            ->join('dealer','dealer.id','=','purchase_order.dealer_id')
                            ->where('purchase_order.company_id',$company_id)
                            ->where('purchase_order.dealer_id',$dealer_id)
                            ->groupBy('product_id')
                            ->pluck(DB::raw("SUM(quantity) as pcs"),'product_id');


        $fullfilmentOrder = DB::table('fullfillment_order')
                            ->join('fullfillment_order_details','fullfillment_order_details.order_id','=','fullfillment_order.order_id')
                            ->join ('catalog_product','catalog_product.id','=','fullfillment_order_details.product_id')
                            ->join('dealer','dealer.id','=','fullfillment_order.dealer_id')
                            ->where('fullfillment_order.company_id',$company_id)
                            ->where('fullfillment_order.dealer_id',$dealer_id)
                            ->groupBy('product_id')
                            ->pluck(DB::raw("SUM(fullfillment_order_details.product_qty) as pcs"),'product_id');


        if(!empty($details_array)){
            $finalArr = array();
              foreach ($details_array as $key => $value) 
            {
                $details['sku_id'] = $value->product_id;
                $details['sku_name'] = $value->product_name;
                $details['mrp'] = $value->mrp_pcs;
                $details['pts'] = $value->dealer_pcs_rate;
                $details['ptr'] = $value->retailer_pcs_rate;
                $details['gst'] = $value->gst_percent;
                $details['pack'] = $value->quantity_per_case;

                $openingStock = !empty($stock_qty[$value->product_id])?$stock_qty[$value->product_id]:'0';
                $purchaseStock = !empty($purchaseOrder[$value->product_id])?$purchaseOrder[$value->product_id]:'0';
                $fulfillStock = !empty($fullfilmentOrder[$value->product_id])?$fullfilmentOrder[$value->product_id]:'0';

                $details['opening_stock'] = $openingStock;
                $details['purchase_stock'] = $purchaseStock;
                $details['fulfilment_stock'] = $fulfillStock;

                $finalStock = ($openingStock+$purchaseStock-$fulfillStock);

                $details['qty'] = $finalStock;

                $finalArr[] = $details;
            }

            return response()->json(['response' =>True,'message'=>'Succefully Found!!','data'=>$finalArr]);        
        }else{
            return response()->json(['response' =>False,'message'=>'Not Found','data'=>array()]);        
        }

    

    }





    public function ssBalanceStock(Request $request){

         $validator=Validator::make($request->all(),[
            'company_id'=>'required',
            'user_id'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   
        $user_id = $request->user_id;
        $company_id = $request->company_id;

        $assign_state = DB::table('person')->where('id',$user_id)->where('company_id',$company_id)->first();

        $assignCsa = DB::table('dealer_location_rate_list')
                    ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                    ->join('csa','csa.c_id','=','dealer.csa_id')
                    ->where('dealer_location_rate_list.user_id',$user_id)
                    ->where('dealer_location_rate_list.company_id',$company_id)
                    ->where('dealer.company_id',$company_id)
                    ->where('csa.company_id',$company_id)
                    ->groupBy('csa.c_id')
                    ->pluck('csa.c_id')->toArray();


        $details_array = DB::table('catalog_product')
                        ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
                        ->select('catalog_product.id as product_id','catalog_product.name as product_name','catalog_product.gst_percent','catalog_product.quantity_per_case','product_rate_list.*')
                        ->where('catalog_product.company_id',$company_id)
                        ->where('product_rate_list.company_id',$company_id)
                        ->where('product_rate_list.state_id',$assign_state->state_id)
                        ->groupBy('catalog_product.id')
                        ->get()->toArray();

        if(!empty($details_array) && !empty($assignCsa)){

        $stock_qty = DB::table('ss_balance_stock')
                    ->whereIn('csa_id',$assignCsa)
                    ->where('company_id',$company_id)
                    ->groupBy('product_id')
                    ->orderBy('ss_balance_stock.id','DESC')
                    ->pluck(DB::raw("stock_qty"),'product_id');

        $dealerArray = DB::table('dealer')
                        ->where('company_id',$company_id)
                        ->whereIn('csa_id',$assignCsa)
                        ->groupBy('id')
                        ->pluck('id')->toArray();



        $fullfilmentOrder = DB::table('fullfillment_order')
                            ->join('fullfillment_order_details','fullfillment_order_details.order_id','=','fullfillment_order.order_id')
                            ->join('dealer','dealer.id','=','fullfillment_order.dealer_id')
                            ->where('fullfillment_order.company_id',$company_id)
                            ->whereIn('fullfillment_order.dealer_id',$dealerArray)
                            ->groupBy('product_id')
                            ->pluck(DB::raw("SUM(fullfillment_order_details.product_qty) as pcs"),'product_id');

            $finalArr = array();
              foreach ($details_array as $key => $value) 
            {
                $details['sku_id'] = $value->product_id;
                $details['sku_name'] = $value->product_name;
                $details['mrp'] = $value->mrp_pcs;
                $details['pts'] = $value->dealer_pcs_rate;
                $details['ptr'] = $value->retailer_pcs_rate;
                $details['gst'] = $value->gst_percent;
                $details['pack'] = $value->quantity_per_case;

                $openingStock = !empty($stock_qty[$value->product_id])?$stock_qty[$value->product_id]:'0';
                $fulfillStock = !empty($fullfilmentOrder[$value->product_id])?$fullfilmentOrder[$value->product_id]:'0';

                $details['opening_stock'] = $openingStock;
                $details['fulfilment_stock'] = $fulfillStock;

                $finalStock = ($openingStock-$fulfillStock);

                $details['qty'] = $finalStock;

                $finalArr[] = $details;
            }

            return response()->json(['response' =>True,'message'=>'Succefully Found!!','data'=>$finalArr]);        
        }else{
            return response()->json(['response' =>False,'message'=>'Not Found','data'=>array()]);        
        }

    }




    public function dms_social_link_data(Request $request)
    {
        $validator=Validator::make($request->all(),[
            
            "company_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }   

        $data = DB::table('dms_social_link_master')
                ->where('status',1)
                ->where('company_id',$request->company_id)
                ->get();
        return response()->json(['response' =>True,'message'=>'Found!!','data'=>$data]);        

    }




    public function dms_counter_sale_submit_new(Request $request)
    {
        $validator=Validator::make($request->all(),[
            "order_id"=>'required',
            "dealer_id"=>'required',
            "sale_date"=>'required',
            "created_date"=>'required',
            "date_time"=>'required',
            "battery_status"=>'required',
            "gps_status"=>'required',
            "lat"=>'required',
            "lng"=>'required',
            "address"=>'required',
            "mcc_mnc_lac_cellid"=>'required',
            "user_id"=>'required',
            "company_id"=>'required',

        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }

        $order_id = $request->order_id;
        $dealer_id = $request->dealer_id;
        $sale_date = $request->sale_date;
        $created_date = $request->created_date;
        $date_time = $request->date_time;
        $battery_status = $request->battery_status;
        $gps_status = $request->gps_status;
        $lat = $request->lat;
        $lng = $request->lng;
        $address = $request->address;
        $mcc_mnc_lac_cellid = $request->mcc_mnc_lac_cellid;
        $user_id = $request->user_id;
        $company_id = $request->company_id;
        $counter_sale_details = json_decode($request->counter_sale_details);
        $insertDetailsArr = array();
        DB::beginTransaction();
        dd($counter_sale_details);
        foreach ($counter_sale_details as $key => $value) 
        {
            $check = DB::table('counter_sale_summary')
                    ->where('order_id',$value->order_id)
                    ->count();
                    // ->update(['order_id'=>$value->order_id]);
            if($check>0)
            {
                // $delete 
                $delete_order = DB::table('counter_sale_summary')->where('order_id',$value->order_id)->delete();

                $check = DB::table('counter_sale_summary')
                    ->where('order_id',$value->order_id)
                    // ->count();
                    ->update(['order_id'=>$value->order_id]);

                $delete_details = DB::table('counter_sale_details')->where('order_id',$value->order_id)->delete();

                    $detailsArr = [
                        'order_id' => $value->order_id,
                        'product_id' => $value->product_id,
                        'rate' => $value->rate,
                        'quantity' => $value->quantity,
                        'barcode' => $value->Barcode,
                        'cases' => $value->case,
                        'pcs' => $value->pcs,
                        'value' => $value->value,
                        'case_rate' => $value->case_rate,
                        'pcs_rate' => $value->pcs_rate,
                        'company_id'=>$company_id,
                        'secondary_qty' => $value->secondary_qty,
                        'created_by' => $user_id,
                        'server_date_time' => date('Y-m-d H:i:s'),
                    ];
                    $insertDetailsArr[] = $detailsArr;
            }
            else
            {
                $check = DB::table('counter_sale_summary')
                    ->where('order_id',$value->order_id)
                    ->count();
                    // ->update(['order_id'=>$value->order_id]);
                if($check>0)
                {
                    $detailsArr = [
                        'order_id' => $value->order_id,
                        'product_id' => $value->product_id,
                        'rate' => $value->rate,
                        'quantity' => $value->quantity,
                        'barcode' => $value->Barcode,
                        'cases' => $value->case,
                        'pcs' => $value->pcs,
                        'value' => $value->value,
                        'case_rate' => $value->case_rate,
                        'pcs_rate' => $value->pcs_rate,
                        'company_id'=>$company_id,
                        'secondary_qty' => $value->secondary_qty,
                         'created_by' => $user_id,
                        'server_date_time' => date('Y-m-d H:i:s'),
                    ];
                    $insertDetailsArr[] = $detailsArr;
                }
                else
                {
                    $myArr = [
                        'order_id'=>$value->order_id,
                        'dealer_id'=>$dealer_id,
                        'created_by_person'=>0,
                        'retailer_id'=>0,
                        'sale_date'=>$sale_date,
                        'created_date'=>$created_date,
                        'date_time'=>$date_time,
                        'battery_status'=>$battery_status,
                        'gps_status'=>$gps_status,
                        'lat'=>$lat,
                        'lng'=>$lng,
                        'address'=>$address,
                        'mcc_mnc_lac_cellid'=>$mcc_mnc_lac_cellid,
                        'company_id'=>$company_id,
                        'created_by_person'=>$user_id,
                        'server_date' => date('Y-m-d H:i:s'),

                    ];
                    $insert_order = DB::table('counter_sale_summary')->insert($myArr);

                    $detailsArr = [
                       'order_id' => $value->order_id,
                        'product_id' => $value->product_id,
                        'rate' => $value->rate,
                        'quantity' => $value->quantity,
                        'barcode' => $value->Barcode,
                        'cases' => $value->case,
                        'pcs' => $value->pcs,
                        'value' => $value->value,
                        'case_rate' => $value->case_rate,
                        'pcs_rate' => $value->pcs_rate,
                        'company_id'=>$company_id,
                        'secondary_qty' => $value->secondary_qty,
                         'created_by' => $user_id,
                        'server_date_time' => date('Y-m-d H:i:s'),
                    ];
                    $insertDetailsArr[] = $detailsArr;
                }

            }

            
        }
        $insert_details = DB::table('counter_sale_details')->insert($insertDetailsArr);

        if($insert_details )
        {
            DB::commit();
            return response()->json(['response' =>True,'message'=>'Successfully Submitted!!']);        

        }
        else
        {
            DB::rollback();
            return response()->json(['response' =>False,'message'=>'Not Submitted!!']);        

        }

    }



}