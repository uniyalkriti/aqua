<?php

namespace App\Http\Controllers;


use App\Location1;
use App\Location2;
use App\Location6;
use App\Location7;
use App\Location3;
use App\Location4;
use App\Location5;
use App\MonthlyTourProgram;
use App\ReceiveOrder;
use App\Retailer;
use App\User;
use App\UserDealerRetailer;
use App\UserDetail;
use App\UserExpenseReport;
use App\UserSalesOrder;
use App\Person;
use App\Vendor;
use App\CatalogProduct;
use App\UsersDetail;
use App\DealerLocation;
use App\TableReturn;
use Illuminate\Http\Request;
use DB;
use Auth;
use DateTime;

class ExportController extends Controller
{

    #showing filter for master data onl;y 
    public function ExportData(Request $requuest)
    {
        $company_id = Auth::user()->company_id;
        $zone = Location1::where('company_id',$company_id)->where('status',1)->pluck('name','id');
        $region = Location2::where('company_id',$company_id)->where('status',1)->pluck('name','id');
        $state = Location3::where('company_id',$company_id)->where('status',1)->pluck('name','id');
        $town = Location4::where('company_id',$company_id)->where('status',1)->pluck('name','id');
        return view('Export.index', [
            'zone' => $zone,
            'region' => $region,
            'town' => $town,
            'state' => $state,
        ]);
    }
    #showing filter for transactional data onl;y 
    public function ExportTransactionalData(Request $requuest)
    {
        $company_id = Auth::user()->company_id;
        $zone = Location1::where('company_id',$company_id)->where('status',1)->pluck('name','id');
        $region = Location2::where('company_id',$company_id)->where('status',1)->pluck('name','id');
        $state = Location3::where('company_id',$company_id)->where('status',1)->pluck('name','id');
        $town = Location4::where('company_id',$company_id)->where('status',1)->pluck('name','id');
        return view('Export.transactionalIndex', [
            'zone' => $zone,
            'region' => $region,
            'town' => $town,
            'state' => $state,
        ]);
    }
    #show count for master data on ajax blade 
    public function ShowExportMasterData(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $zone = $request->zone;
        $region = $request->region;
        $state = $request->state;
        $town = $request->town;

        # for retailer export
        $Retailer_Query_data = DB::table('retailer')
                            ->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','retailer.location_id')
                            ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                            ->join('location_view','location_view.l7_id','=','retailer.location_id')
                            ->leftJoin('person','person.id','=','retailer.created_by_person_id')
                            ->leftJoin('_retailer_outlet_type','_retailer_outlet_type.id','=','retailer.outlet_type_id')
                            ->where('retailer.company_id',$company_id)
                            ->where('retailer_status','!=',9)
                            ->where('dealer_status','!=',9)
                            ->orderBy('l1_name','ASC');
        // dd($Retailer_Query);
        if(!empty($zone))
        {
            $Retailer_Query_data->whereIn('location_view.l1_id',$zone);
        }
        if(!empty($region))
        {
            $Retailer_Query_data->whereIn('location_view.l2_id',$region);
        }
        if(!empty($state))
        {
            $Retailer_Query_data->whereIn('location_view.l3_id',$state);
        }
        if(!empty($town))
        {
            $Retailer_Query_data->whereIn('location_view.l4_id',$town);
        }

        $count_retailer = $Retailer_Query_data->distinct('retailer.id')->count('retailer.id');

        #user count query 
       $User_Query_data = DB::table('person')
        ->join('person_login','person_login.person_id','person.id')
        ->join('company','company.id','=','person.company_id')
        ->join('_role','_role.role_id','=','person.role_id')
        ->join('location_view','location_view.l3_id','=','person.state_id')
        ->select('person.id','person.first_name','person.middle_name','person.last_name','person.emp_code','company.name as c_name','person.role_id as p_role_id','_role.rolename as p_role_name','person.person_id_senior as senior_id',DB::raw('(select(CONCAT_WS(" ",first_name,middle_name,last_name))from person where id=senior_id) as senior_name'),
        DB::raw('(select(person.role_id)from person where person.id=senior_id) as senior_role_id1'),
        DB::raw('(select(rolename) from _role where role_id = senior_role_id1) as senior_role_name'),'person.mobile','person.email','location_view.l2_id as state_id','location_view.l2_name as state_name','person.head_quar','person.version_code_name')
        ->where('person.company_id',$company_id)
        ->where('person_status','!=',9);
         if(!empty($zone))
        {
            $User_Query_data->whereIn('location_view.l1_id',$zone);
        }
        if(!empty($region))
        {
            $User_Query_data->whereIn('location_view.l2_id',$region);
        }
        if(!empty($state))
        {
            $User_Query_data->whereIn('location_view.l3_id',$state);
        }
        if(!empty($town))
        {
            $User_Query_data->whereIn('location_view.l4_id',$town);
        }
        $user_data_count = $User_Query_data->distinct('person.id')->count('person.id');

        #dealer count
        $Dealer_Query_data = DB::table('dealer')
        ->join('csa','csa.c_id','=','dealer.csa_id')
        ->join('_dealer_ownership_type','_dealer_ownership_type.id','=','dealer.ownership_type_id')
        ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
        ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
        ->select('dealer.*','csa.*','location_view.*','dealer.id as dealer_id','dealer.name as dealer_name','dealer.contact_person as d_cont_per','dealer.address as d_addr','dealer.email as d_email','dealer.landline as landline','dealer.other_numbers as other_number','dealer.tin_no as tin_no','dealer.pin_no as d_pin_no','dealer.ownership_type_id as ownership_type_id','_dealer_ownership_type.ownership_type as ownership_type','dealer.avg_per_month_pur as avg_per_mon','dealer.dealer_status as d_status')
        ->where('dealer.company_id',$company_id)
        ->where('dealer_status','!=',9);
         if(!empty($zone))
        {
            $Dealer_Query_data->whereIn('location_view.l1_id',$zone);
        }
        if(!empty($region))
        {
            $Dealer_Query_data->whereIn('location_view.l2_id',$region);
        }
        if(!empty($state))
        {
            $Dealer_Query_data->whereIn('location_view.l3_id',$state);
        }
        if(!empty($town))
        {
            $Dealer_Query_data->whereIn('location_view.l4_id',$town);
        }
        $dealer_count = $Dealer_Query_data->distinct('dealer.id','location_id')->count('dealer.id','location_id');
        # common for all 
        $count_zone = count(array($zone));
        $count_region = count(array($region));
        $count_state = count(array($state));
        $count_town = count(array($town));

        return view('Export.ajax', [
            'retailer_count' => $count_retailer,
            'dealer_count' => $dealer_count,
            'user_count' => $user_data_count,
            'count_zone' => $count_zone,
            'count_region' => $count_region,
            'count_state' => $count_state,
            'count_town' => $count_town,
            'zone' => $zone,
            'region' => $region,
            'state' => $state,
            'town' => $town,

          
        ]);
    }

    #show count for transactional data on ajax blade 
    public function ShowExportTransactionalData(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $zone = !empty($request->zone)?$request->zone:array();
        $region = !empty($request->region)?$request->region:array();
        $state = !empty($request->state)?$request->state:array();
        $town = !empty($request->town)?$request->town:array();
        $table_name = TableReturn::table_return($from_date,$to_date);
        #for sale order 
        // $table_name = $table_return->table;
        $sale_order_query_data = DB::table($table_name)
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                                ->join('location_view','location_view.l7_id','=',$table_name.'.location_id')
                                ->where($table_name.'.company_id',$company_id);

        if(!empty($from_date) && !empty($to_date))
        {
            $sale_order_query_data->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' and DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'");
        }
        if(!empty($zone))
        {
            $sale_order_query_data->whereIn('location_view.l1_id',$zone);
        }
        if(!empty($region))
        {
            $sale_order_query_data->whereIn('location_view.l2_id',$region);
        }
        if(!empty($state))
        {
            $sale_order_query_data->whereIn('location_view.l3_id',$state);
        }
        if(!empty($town))
        {
            $sale_order_query_data->whereIn('location_view.l4_id',$town);
        }

        $sale_order_query_count = $sale_order_query_data->distinct('user_sales_order_details.id')->count('user_sales_order_details.id');
        // dd($sale_order_query_count);
        // $sale_order_query_count = count($sale_order_query);
        
        # common for all 
        $count_zone = count($zone);
        $count_region = count($region);
        $count_state = count($state);
        $count_town = count($town);

        return view('Export.transactional', [
            'sale_order_query_count' => $sale_order_query_count,
            'to_date' => $to_date,
            'from_date' => $from_date,
            'count_zone' => $count_zone,
            'count_region' => $count_region,
            'count_state' => $count_state,
            'count_town' => $count_town,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'zone' => $zone,
            'region' => $region,
            'state' => $state,
            'town' => $town,

          
        ]);
    }

   
    public function RetailerExport(Request $request)
    {
        ini_set('memory_limit', '256M');
       
        $company_id = Auth::user()->company_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $zone = $request->zone;
        $region = $request->region;
        $state = $request->state;
        $town = $request->town;
        $output ='';
        $Retailer_Query_data = DB::table('retailer')
                            ->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','retailer.location_id')
                            ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                            ->join('location_view','location_view.l7_id','=','retailer.location_id')
                            ->leftJoin('person','person.id','=','retailer.created_by_person_id')
                            // ->join('_retailer_outlet_type','_retailer_outlet_type.id','=','retailer.outlet_type_id')
                            // ->select('retailer.name as retailer_name','retailer.id as retailer_id','retailer.*','location_view.*','dealer.*','dealer.name as dealerName','person.*','_retailer_outlet_type.id as retailer_type_id','_retailer_outlet_type.outlet_type as outletType','retailer.landline','retailer.other_numbers')
                              // ->select('retailer.name as retailer_name','retailer.id as retailer_id','retailer.*','location_view.*','dealer.*','dealer.name as dealerName','person.*','retailer.landline','retailer.other_numbers')
                               ->select('class','retailer.landline','retailer.other_numbers','contact_per_name','retailer.name as retailer_name','first_name','last_name','l6_name','dealer.name as dealerName','l1_name','l3_name','l7_name','person.email','retailer.address','retailer.track_address','retailer.id as retailer_id','retailer.class','retailer.created_on','retailer_status','retailer.created_by_person_id','person.emp_code','dealer.dealer_code','dealer.id as dealer_id','l1_id','l3_id','l7_id','retailer.pin_no')
                            ->where('retailer_status','!=',9)
                            ->where('dealer_status','!=',9)
                            ->where('retailer.company_id',$company_id)
        // ->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')>='2020-07-01' AND DATE_FORMAT(retailer.created_on,'%Y-%m-%d')<='2020-08-31'")
                            ->where('dealer.company_id',$company_id)
                            ->groupBy('retailer.id')
                            ->orderBy('retailer_code','ASC');

        if(!empty($from_date) && !empty($to_date))
        {
            $Retailer_Query_data->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')>='$from_date' and DATE_FORMAT(retailer.created_on,'%Y-%m-%d')<='$to_date'");
        }
        if(!empty($zone))
        {
            $Retailer_Query_data->whereIn('location_view.l1_id',$zone);
        }
        if(!empty($region))
        {
            $Retailer_Query_data->whereIn('location_view.l2_id',$region);
        }
        if(!empty($state))
        {
            $Retailer_Query_data->whereIn('location_view.l3_id',$state);
        }
        if(!empty($town))
        {
            $Retailer_Query_data->whereIn('location_view.l4_id',$town);
        }
        $Retailer_Query = $Retailer_Query_data->get();
        $retailer_category = DB::table('_retailer_outlet_category')->where('status',1)->where('company_id',$company_id)->pluck('outlet_category','id');
            $output .="S.No,Retailer id,Retailer Name,Retailer Number,Retailer Type Id,Retailer Type,Retailer Category Id,Retailer Category,Created Date & Time,Owner Name,Retailer Status Id,Retailer Status,Created By Id,Created By,Employee code,Distributor Town,Distributor code,Distributor Id,Distributor Name,Country id,Country,State Id,State Name,Beat id,Beat Name,Email,Mobile,Address,Tracking Address,Pin No";
            $output .="\n";
            $i=1;

            foreach ($Retailer_Query as $key => $value) 
            {
                // dd($value);
                    // switch($value->class)
                    // {
                    //     case 0: $class = "None"; break; 
                    //     case 1: $class = "Platinum"; break; 
                    //     case 2: $class = "Diamond"; break; 
                    //     case 3: $class = "Gold"; break; 
                    //     case 4: $class = "Silver"; break; 
                    //     case 5: $class = "Semi-WS"; break; 
                    //     case 6: $class = "WS"; break; 
                    // }



                    $class_name=!empty($retailer_category[$value->class])?$retailer_category[$value->class]:'None';
                    // $class_name=$value->class==0?'None':$class;
                    $status = !empty($value->retailer_status==1)?'Active':'De-Active';


                    $landline = !empty($value->landline)?$value->landline:'0';
                    $retailer_mobile = !empty($value->other_numbers)?$value->other_numbers:$landline;


                    $contact_per_name = str_replace(",","|",$value->contact_per_name);
                    $retailer_name = !empty($value->retailer_name)?str_replace(",","|",$value->retailer_name):'NA';
                    $fname = str_replace(",","|",$value->first_name);
                    $lname = str_replace(",","|",$value->last_name);
                    $l6_name = str_replace(",","|",$value->l6_name);
                    $dealerName = str_replace(",","|",$value->dealerName);
                    $l1_name = str_replace(",","|",$value->l1_name);
                    $l3_name = str_replace(",","|",$value->l3_name);
                    $l7_name = str_replace(",","|",$value->l7_name);
                    $beat_name = str_replace("\r\n"," ",$l7_name);
                    $email = str_replace(",","|",$value->email);
                    $address = str_replace(",","|",$value->address);
                    $track_address = str_replace(",","|",$value->track_address);

                    $output .=$i.',';
                    $output .='#'.$value->retailer_id.'#'.',';
                    $output .=$retailer_name.',';
                    $output .=$retailer_mobile.',';
                    // $output .=$value->retailer_type_id.',';
                    // $output .=$value->outletType.',';
                     $output .=''.',';
                    $output .=''.',';
                    $output .=$value->class.',';
                    $output .=$class_name.',';
                    $output .=$value->created_on.',';
                    $output .=$contact_per_name.',';
                    $output .=$value->retailer_status.',';
                    $output .= $status.',';

                    



                    $output .=$value->created_by_person_id.',';                  
                    $output .=$fname." ".$lname.',';
                    $output .=$value->emp_code.',';
                    $output .=$l6_name.',';
                    $output .=$value->dealer_code.',';
                    $output .=$value->dealer_id.',';
                    $output .=$dealerName.',';
                    $output .=$value->l1_id.',';
                    $output .=$l1_name.',';
                    $output .=$value->l3_id.',';
                    $output .=$l3_name.',';
                    $output .=$value->l7_id.',';
                    $output .=$beat_name.',';
                    $output .=$email.',';
                    $output .=$value->landline.',';
                    $output .=$address.',';
                    $output .=$track_address.',';
                    $output .=$value->pin_no.',';                   
                   
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;

            }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=RetailerExport.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                
                echo $output;    
        
        

    }


  public function userExport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $zone = $request->zone;
        $region = $request->region;
        $state = $request->state;
        $town = $request->town;

        $output ='';

        $dealerCount = DB::table('dealer_location_rate_list')
                    ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                    ->where('dealer_location_rate_list.company_id',$company_id)
                    ->where('dealer.company_id',$company_id)
                    ->where('dealer.dealer_status','=','1')
                    ->groupBy('dealer_location_rate_list.user_id')
                    ->pluck(DB::raw("COUNT(DISTINCT dealer_location_rate_list.dealer_id) as dealerCount"),'dealer_location_rate_list.user_id')->toArray();


        $User_Query_data = DB::table('person')
        ->join('person_details','person_details.person_id','=','person.id')
        ->join('person_login','person_login.person_id','=','person.id')
        ->join('company','company.id','=','person.company_id')
        ->join('_role','_role.role_id','=','person.role_id')
        ->join('location_6','location_6.id','=','person.town_id')
        ->join('location_5','location_5.id','=','location_6.location_5_id')
        ->join('location_3','location_3.id','=','person.state_id')
        ->join('location_2','location_2.id','=','location_3.location_2_id')
        ->join('location_1','location_1.id','=','location_2.location_1_id')
        ->select('person.id','person.first_name','person.middle_name','person.last_name','person.emp_code','company.name as c_name','person.role_id as p_role_id','_role.rolename as p_role_name','person.person_id_senior as senior_id',DB::raw('(select(CONCAT_WS(" ",first_name,middle_name,last_name))from person where id=senior_id) as senior_name'),
        DB::raw('(select(person.role_id)from person where person.id=senior_id) as senior_role_id1'),
        DB::raw('(select(rolename) from _role where role_id = senior_role_id1) as senior_role_name'),'person.mobile','person.email','location_3.id as state_id','location_3.name as state_name','person.head_quar','person.version_code_name')
        ->where('person.company_id',$company_id)
        ->where('location_3.company_id',$company_id)
        ->where('location_2.company_id',$company_id)
        ->where('location_6.company_id',$company_id)
        // ->whereRaw("DATE_FORMAT(person_details.created_on,'%Y-%m-%d')>='2020-07-01' AND DATE_FORMAT(person_details.created_on,'%Y-%m-%d')<='2020-08-31'")
        ->where('person_status','!=',9)
        ->groupBy('person.id');

        if(!empty($zone))
        {
            $User_Query_data->whereIn('location_1.id',$zone);
        }
        if(!empty($region))
        {
            $User_Query_data->whereIn('location_2.id',$region);
        }
        if(!empty($state))
        {
            $User_Query_data->whereIn('location_3.id',$state);
        }
        if(!empty($town))
        {
            $User_Query_data->whereIn('location_4.id',$town);
        }

        $User_Query = $User_Query_data->get()->toarray();

        $device_details = DB::table('user_mobile_details')->groupBy('user_id')->pluck(DB::raw("CONCAT('Device : ',device_manuf,' Device Name : ',device_name,' Device Version : ',device_version ) device_details"),'user_id');

            $output .="S.No,User id,User First Name,User Middle Name,User Last Name,User FullName,Emplopyee Code,Company Name,User Role Id,User Role Name,Senior Id,Senior Name,Senior Role Id,Senior Role Name,Mobile,Email,State Id,State Name,Head Quarter,App Version , Device Details , Dealer Count";
            $output .="\n";
            $i=1;

            foreach ($User_Query as $key => $value) 
            {
                $first_name = str_replace(",","|",$value->first_name);
                $middle_name = str_replace(",","|",$value->middle_name);
                $last_name = str_replace(",","|",$value->last_name);
                $company_name = str_replace(",","|",$value->c_name);
                $c_name = str_replace("\n"," ",$company_name);
                $p_role_name = str_replace(",","|",$value->p_role_name);
                $senior_name = str_replace(",","|",$value->senior_name);
                $senior_role_name = str_replace(",","|",$value->senior_role_name);
                $email = str_replace(",","|",$value->email);
                $state_name = str_replace(",","|",$value->state_name);
                $head_quarter = str_replace(",","|",$value->head_quar);

                $output .=$i.',';
                $output .=''.$value->id.''.',';
                $output .=$first_name.',';
                $output .=$middle_name.',';
                $output .=$last_name.',';
                $output .=$first_name.' '.$middle_name.' '.$last_name.',';
                $output .=$value->emp_code.',';
                $output .=$c_name.',';
                $output .=$value->p_role_id.',';
                $output .=$p_role_name.',';
                $output .= $value->senior_id.',';
                $output .=$senior_name.',';                  
                $output .=$value->senior_role_id1.',';
                $output .=$senior_role_name.',';
                $output .=$value->mobile.',';
                $output .=$email.',';
                $output .=$value->state_id.',';
                $output .=$state_name.',';
                $output .=$head_quarter.',';
                $output .=$value->version_code_name.',';
                $output .=!empty($device_details[$value->id])?$device_details[$value->id]:''.',';

                $output .=!empty($dealerCount[$value->id])?$dealerCount[$value->id]:''.',';
                

                $output .="\n";
                $i++;
     
                $dataCount=1;


            }
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=UserExport.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            echo $output;
        
       
    }
    public function dealerExport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $zone = $request->zone;
        $region = $request->region;
        $state = $request->state;
        $town = $request->town;
        $output ='';
        
        if($company_id == 52)
        {
            $Dealer_Query_data = DB::table('dealer')
                    ->join('csa','csa.c_id','=','dealer.csa_id')
                    ->join('dealer_personal_details','dealer_personal_details.dealer_id','=','dealer.id')
                    ->leftJoin('_dealer_ownership_type','_dealer_ownership_type.id','=','dealer.ownership_type_id')
                    ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                    ->leftJoin('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
                    ->select('dealer.*','csa.*','location_view.*','dealer.id as dealer_id','dealer.name as dealer_name','dealer.contact_person as d_cont_per','dealer.address as d_addr','dealer.email as d_email','dealer.landline as landline','dealer.other_numbers as other_number','dealer.tin_no as tin_no','dealer.pin_no as d_pin_no','dealer.ownership_type_id as ownership_type_id','_dealer_ownership_type.ownership_type as ownership_type','dealer.avg_per_month_pur as avg_per_mon','dealer.dealer_status as d_status','bank_name','security_amt','refrence_no','security_date','reciept_issue_date','security_remarks','commencement_date','termination_date','certificate_issue_date','agreement_remarks','refund_amt','refund_ref_no','refund_date','refund_remarks','food_license','pan_no','aadar_no')
                    ->where('dealer.company_id',$company_id)
                    ->groupBy('dealer.id','location_id');

            $dealer_credtianls = DB::table('dealer_person_login')->where('company_id',$company_id)->groupBy('dealer_id')->pluck('uname','dealer_id');
        }
        else
        {
            $Dealer_Query_data = DB::table('dealer')
                    ->join('csa','csa.c_id','=','dealer.csa_id')
                    ->join('_dealer_ownership_type','_dealer_ownership_type.id','=','dealer.ownership_type_id')
                    ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                    ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
                    ->select('dealer.*','csa.*','location_view.*','dealer.id as dealer_id','dealer.name as dealer_name','dealer.contact_person as d_cont_per','dealer.address as d_addr','dealer.email as d_email','dealer.landline as landline','dealer.other_numbers as other_number','dealer.tin_no as tin_no','dealer.pin_no as d_pin_no','dealer.ownership_type_id as ownership_type_id','_dealer_ownership_type.ownership_type as ownership_type','dealer.avg_per_month_pur as avg_per_mon','dealer.dealer_status as d_status')
        ->whereRaw("DATE_FORMAT(dealer.created_at,'%Y-%m-%d')>='2020-07-01' AND DATE_FORMAT(dealer.created_at,'%Y-%m-%d')<='2020-08-31'")
                    ->where('dealer.company_id',$company_id)
                    ->groupBy('dealer.id','location_id');
        }
   
        if(!empty($zone))
        {
            $Dealer_Query_data->whereIn('location_view.l1_id',$zone);
        }
        if(!empty($region))
        {
            $Dealer_Query_data->whereIn('location_view.l2_id',$region);
        }
        if(!empty($state))
        {
            $Dealer_Query_data->whereIn('location_view.l3_id',$state);
        }
        if(!empty($town))
        {
            $Dealer_Query_data->whereIn('location_view.l4_id',$town);
        }
        $Dealer_Query = $Dealer_Query_data->get()->toarray();

            
        if($company_id == 52)
        {
            $output .="S.No,Distributor id,Distributor Name,User Name,Contact Person,State Id,State Name,Town Id,Town Name,Beat Name,Distributor Address,Distributor Email,Csa Id,Csa Name ,Distributor Landline,Distributor Other Number,Distributor Tin No.,Distributor Pin no.,Distributor Ownership Type Id,Distributor Ownership Type Name,Distributor avg per month purchase,Bank Name,Security Amt,Refrence No,Security Date,Reciept issue Date,Security Remarks,Commencement Date,Termination Date,Certificate Issue Date,Agreement Remarks,Refund Amt,Refund Ref No,Refund Date,Refund Remarks,Food License,Pan No,Aadar No,Distributor Status";
            $output .="\n";
            $i=1;
            foreach ($Dealer_Query as $key => $value) 
            {

                $status = !empty($value->d_status==1)?'Active':'De-Active'.',';
                $d_cont_per = !empty($value->d_cont_per)?str_replace(",","|",$value->d_cont_per):'NA';
                $dealer_name = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value->dealer_name);
                // $dealer_name = !empty($value->dealer_name)?str_replace(",","|",$value->dealer_name):'NA';
                $state_name =  str_replace(",","|",$value->l3_name);
                $town_name = str_replace(",","|",$value->l4_name);
                $beat_name = str_replace(",","|",$value->l7_name);
                $d_addr = str_replace(",","|",$value->d_addr);
                $d_email = str_replace(",","|",$value->d_email);
                $csa_name = str_replace(",","|",$value->csa_name);
                $ownership_type = str_replace(",","|",$value->ownership_type);
                $user_name = !empty($dealer_credtianls[$value->dealer_id])?$dealer_credtianls[$value->dealer_id]:'';
              

                    $output .=$i.',';
                    $output .=''.$value->dealer_id.''.',';
                    $output .=$dealer_name.',';
                    $output .=$user_name.',';
                    $output .=$d_cont_per.',';
                    $output .=$value->l3_id.',';
                    $output .=$state_name.',';
                    $output .=$value->l4_id.',';
                    $output .=$town_name.',';
                    $output .=$beat_name.',';
                    $output .=$d_addr.',';
                    $output .=$d_email.',';
                    $output .=$value->csa_id.',';
                    $output .=$csa_name.',';
                    $output .= $value->landline.',';
                    $output .=$value->other_number.',';                  
                    $output .=$value->tin_no.',';
                    $output .=$value->d_pin_no.',';
                    $output .=$value->ownership_type_id.',';
                    $output .=$ownership_type.',';
                    $output .=$value->avg_per_mon.',';
                    $output .=str_replace(',',' ',$value->bank_name).',';
                    $output .=str_replace(',',' ',$value->security_amt).',';
                    $output .=str_replace(',',' ',$value->refrence_no).',';
                    $output .=str_replace(',',' ',$value->security_date).',';
                    $output .=str_replace(',',' ',$value->reciept_issue_date).',';
                    $output .=str_replace(',',' ',$value->security_remarks).',';
                    $output .=str_replace(',',' ',$value->commencement_date).',';
                    $output .=str_replace(',',' ',$value->termination_date).',';
                    $output .=str_replace(',',' ',$value->certificate_issue_date).',';
                    $output .=str_replace(',',' ',$value->agreement_remarks).',';
                    $output .=str_replace(',',' ',$value->refund_amt).',';
                    $output .=str_replace(',',' ',$value->refund_ref_no).',';
                    $output .=str_replace(',',' ',$value->refund_date).',';
                    $output .=str_replace(',',' ',$value->refund_remarks).',';
                    $output .=str_replace(',',' ',$value->food_license).',';
                    $output .=str_replace(',',' ',$value->pan_no).',';
                    $output .=str_replace(',',' ',$value->aadar_no).',';
                    $output .=$status.',';

                
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;
            }
        }
        else
        {
            // $output .="";
            $output .="S.No,Distributor id,Distributor Name,Contact Person,State Id,State Name,Town Id,Town Name,Beat Name,Distributor Address,Distributor Email,Csa Id,Csa Name ,Distributor Landline,Distributor Other Number,Distributor Tin No.,Distributor Pin no.,Distributor Ownership Type Id,Distributor Ownership Type Name,Distributor avg per month purchase,Distributor Status";
            $output .="\n";
            $i=1;
            foreach ($Dealer_Query as $key => $value) 
            {

                $status = !empty($value->d_status==1)?'Active':'De-Active'.',';
                $d_cont_per = !empty($value->d_cont_per)?str_replace(",","|",$value->d_cont_per):'NA';
                $dealer_name = !empty($value->dealer_name)?str_replace(",","|",$value->dealer_name):'NA';
                $state_name =  str_replace(",","|",$value->l3_name);
                $town_name = str_replace(",","|",$value->l4_name);
                $beat_name = str_replace(",","|",$value->l7_name);
                $d_addr = str_replace(",","|",$value->d_addr);
                $d_email = str_replace(",","|",$value->d_email);
                $csa_name = str_replace(",","|",$value->csa_name);
                $ownership_type = str_replace(",","|",$value->ownership_type);
              

                    $output .=$i.',';
                    $output .=''.$value->dealer_id.''.',';
                    $output .=$dealer_name.',';
                    $output .=$d_cont_per.',';
                    $output .=$value->l3_id.',';
                    $output .=$state_name.',';
                    $output .=$value->l4_id.',';
                    $output .=$town_name.',';
                    $output .=$beat_name.',';
                    $output .=$d_addr.',';
                    $output .=$d_email.',';
                    $output .=$value->csa_id.',';
                    $output .=$csa_name.',';
                    $output .= $value->landline.',';
                    $output .=$value->other_number.',';                  
                    $output .=$value->tin_no.',';
                    $output .=$value->d_pin_no.',';
                    $output .=$value->ownership_type_id.',';
                    $output .=$ownership_type.',';
                    $output .=$value->avg_per_mon.',';
                    $output .=$status.',';
                
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;
            }
        }
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=DistributorExport.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            echo $output;
      
    }

    // public function saleOrderData(Request $request)
    // {
    //     $company_id = Auth::user()->company_id;
    //     $output ='';
    //     $from_date = $request->from_date;
    //     $to_date = $request->to_date;
    //     $zone = $request->zone;
    //     $region = $request->region;
    //     $state = $request->state;
    //     $town = $request->town;
    //     $sale_order_query = DB::table('export_sale_order')->where('uso_company_id',$company_id);

    //     if(!empty($from_date) && !empty($to_date))
    //     {
    //         $sale_order_query->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' and DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'");
    //     }
    //     if(!empty($zone))
    //     {
    //         $sale_order_query->whereIn('export_sale_order.l1_id',$zone);
    //     }
    //     if(!empty($region))
    //     {
    //         $sale_order_query->whereIn('export_sale_order.l2_id',$region);
    //     }
    //     if(!empty($state))
    //     {
    //         $sale_order_query->whereIn('export_sale_order.l3_id',$state);
    //     }
    //     if(!empty($town))
    //     {
    //         $sale_order_query->whereIn('export_sale_order.l4_id',$town);
    //     }
    //     $sale_order_query_data = $sale_order_query->get();
       
    //         $output .="S.No,Order id,User Id,User Name,Distributor Id,Distributor Name,Zone Id,Zone Name,Region Id,Region Name,State Id,State Name,Town Id,Town Name,Beat Id,Beat name,Retailer id,Retailer Name,Status Id(True:productive|False:Non-Productive),Status,Tota Sale Value,Discount,Amount,Case Qty,Total Sale Qty,Total Dispatch Qty,Lat_Lng,Track Address,Date,Time,Image Name,Order Status Id,Order Status,Remarks,Catalog Id,Catalog Name,Catalog 1 Id,Catalog 1 Name,Catalog 2 Id,Catalog 2 Name,Product Id,Product Nmae,Rate,Case Qty,Quanting,'Remaining qty','scheme_qty'";
    //         $output .="\n";
    //         $i=1;
    //         foreach ($sale_order_query_data as $key => $value) 
    //         {
    //                 switch($value->order_status)
    //                 {
    //                     case 0: $order_status = "order recieved"; break; 
    //                     case 1: $order_status = "challan generated"; break; 
    //                     case 2: $order_status = "challan cancel"; break; 
    //                     case 3: $order_status = "invoice generated"; break; 
    //                     case 4: $order_status = "invoice cancel"; break; 
    //                     case 5: $order_status = "order dispatch"; break; 
    //                     case 6: $order_status = "challan closed"; break; 
    //                     case 8: $order_status = "cancel"; break; 
    //                 }

    //                 $retailer_name = !empty($value->retailer_name)?str_replace(",","|",$value->retailer_name):'NA';
    //                 $product_name = !empty($value->product_name)?str_replace(",","|",$value->product_name):'NA';
    //                 $fname = str_replace(",","|",$value->first_name);
    //                 $lname = str_replace(",","|",$value->last_name);
    //                 $dealerName = str_replace(",","|",$value->dealer_name);
    //                 $zone_name = str_replace(",","|",$value->l1_name);
    //                 $region_name = str_replace(",","|",$value->l2_name);
    //                 $state_name = str_replace(",","|",$value->l3_name);
    //                 $town_name = str_replace(",","|",$value->l4_name);
    //                 $l5_name = str_replace(",","|",$value->l5_name);
    //                 $beat_name=str_replace("\r\n"," ",$l5_name);
    //                 $catlog_name = str_replace(",","|",$value->c0_name);
    //                 $c1_name = str_replace(",","|",$value->c1_name);
    //                 $catlog1_name = str_replace("\n"," ",$c1_name);
    //                 $c2_name = str_replace(",","|",$value->c2_name);
    //                 $catlog2_name = preg_replace('/[^A-Za-z0-9\-]/', ' ', $c2_name);




    //                 $track_address = str_replace(",","|",$value->track_address);
    //                 $status = !empty($value->call_status==1)?'productive':'Non-Productive';

    //                 $output .=$i.',';
    //                 $output .=' '.$value->order_id.' '.',';
    //                 $output .=$value->user_id.',';
    //                 $output .=$fname.' '.$lname.',';
    //                 $output .=$value->dealer_id.',';
    //                 $output .=$dealerName.',';
    //                 $output .=$value->l1_id.',';
    //                 $output .=$zone_name.',';
    //                 $output .=$value->l2_id.',';
    //                 $output .=$region_name.',';
    //                 $output .=$value->l3_id.',';
    //                 $output .=$state_name.',';
    //                 $output .=$value->l4_id.',';
    //                 $output .=$town_name.',';
    //                 $output .=$value->l5_id.',';
    //                 $output .=$beat_name.',';
    //                 $output .=$value->retailer_code.',';
    //                 $output .= $retailer_name.',';                
    //                 $output .=$value->call_status.',';
    //                 $output .=$status.',';
    //                 $output .=$value->total_sale_value.',';
    //                 $output .=$value->discount.',';
    //                 $output .=$value->amount.',';
    //                 $output .=$value->case_qty.',';
    //                 $output .=$value->total_sale_qty.',';
    //                 $output .=$value->total_dispatch_qty.',';
    //                 $output .=$value->lat_lng.',';
    //                 $output .=$track_address .',';
    //                 $output .=$value->date.',';
    //                 $output .=$value->time.',';
    //                 $output .=$value->image_name.',';
    //                 $output .=$value->order_status.',';
    //                 $output .=$order_status.',';
    //                 $output .=$value->remarks.',';                   
    //                 $output .=$value->c0_id.',';                   
    //                 $output .=$catlog_name.',';                   
    //                 $output .=$value->c1_id.',';                   
    //                 $output .=$catlog1_name.',';                   
    //                 $output .=$value->c2_id.',';                                     
    //                 $output .=$catlog2_name.',';                   
    //                 $output .=$value->product_id.',';                   
    //                 $output .=$product_name.',';                   
    //                 $output .=$value->rate.',';                   
    //                 $output .=$value->case_qty.',';                   
    //                 $output .=$value->quantity.',';                   
    //                 $output .=$value->remaining_qty.',';                   
    //                 $output .=$value->scheme_qty.',';                   
                   
    //                 $output .="\n";
    //                 $i++;
         
    //                 $dataCount=1;

    //         }

    //             header("Content-type: text/csv");
    //             header("Content-Disposition: attachment; filename=SaleData.csv");
    //             header("Pragma: no-cache");
    //             header("Expires: 0");
    //             echo $output;
                


    // }

    ############# Export For Day Wise Performance ####################
 public function ExportdailyPerformanceReport(Request $request)
 {
    
    
   
        
        $output ='';
         $explodeDate = explode(" -", $request->date_range_picker);
         $from = date('Y-m-d',strtotime(trim($explodeDate[0])));
         $to = date('Y-m-d',strtotime(trim($explodeDate[1])));

        
  
         $status = $request->status;
         $state = $request->area;
         $region = $request->region;
         $user = $request->user;
      
         $query = [];
         $new_arr =[];
         $otherArr =[];
          $role_id=Auth::user()->role_id;           
             if($role_id==1 || $role_id==50)
             {
                $datasenior='';
             }else
             { 
                 
                 Session::forget('juniordata');
                 $login_user=Auth::user()->id;
                 $datasenior_call=self::getJuniorUser($login_user);
                 $datasenior = $request->session()->get('juniordata');
                  if(empty($datasenior))
                  {
                      $datasenior[]=$login_user;
                   }
             }
         #Working Status master data
         $working_status = DB::table('_working_status')
             ->pluck('name', 'id');
 
         #Catalog2 master data
         $catalog = DB::table('catalog_0')
             ->orderBy('sequence')->where('status',1)
             ->pluck('catalog_0.name', 'catalog_0.id');
 
 
 
         $temp_rv_data = DB::table('secondary_sale')
             ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from' and DATE_FORMAT(date,'%Y-%m-%d') <='$to'")
             ->groupBy('date','user_id');
         
         if(!empty($state))
         {
             $temp_rv_data->whereIn('l3_id',$request->area);
         }
 
         $temp_rv = $temp_rv_data->pluck(DB::raw("SUM(quantity*rate) as total_price"),DB::raw("CONCAT(user_id,date)"));
 
         $temp_kg_data = DB::table('secondary_sale')
             ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from' and DATE_FORMAT(date,'%Y-%m-%d') <='$to'")
             ->groupBy('date','user_id');
 
         if(!empty($state))
         {
             $temp_kg_data->whereIn('l3_id',$request->area);
         }
         $temp_kg = $temp_kg_data->pluck(DB::raw("SUM(quantity*weight) as total_weight"),DB::raw("CONCAT(user_id,date)"));
 
  
 
         $time_of_first_call_data=DB::table('secondary_sale')
         ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from' and DATE_FORMAT(date,'%Y-%m-%d') <='$to'")
         ->groupBy('user_id','date')
         ->orderBy('time','ASC');
         if(!empty($state))
         {
             $time_of_first_call_data->whereIn('l3_id',$request->area);
         }
         $time_of_first_call = $time_of_first_call_data->pluck('time',DB::raw("CONCAT(user_id,date)"));
 
         $time_of_last_call_data =DB::table('secondary_sale')
         ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from' and DATE_FORMAT(date,'%Y-%m-%d') <='$to'")
         ->groupBy('user_id','date');
         if(!empty($state))
         {
             $time_of_last_call_data->whereIn('l3_id',$request->area);
         }
         $time_of_last_call = $time_of_last_call_data->pluck(DB::raw("MAX(time)"),DB::raw("CONCAT(user_id,date)"));
        
         // dd($time_of_first_call);
 
         $checkout_data=DB::table('check_out')
         ->join('person','person.id','=','check_out.user_id')
         ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d') >='$from' and DATE_FORMAT(work_date,'%Y-%m-%d') <='$to'")
         ->groupBy('work_date','user_id');
 
         if(!empty($state))
         {
             $checkout_data->whereIn('state_id',$request->area);
         }
         $checkout = $checkout_data->pluck('work_date',DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"));
     
                     
        $new_arr_data_data = DB::table('secondary_sale')
             ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from' and DATE_FORMAT(date,'%Y-%m-%d') <='$to'")
             ->select('date','user_id','c0_id',DB::raw("SUM(quantity*rate) as total_price"), DB::raw("SUM(quantity*weight) as total_weight"), DB::raw("COUNT(Distinct order_id) as total_row"))
             ->groupBy('c0_id','user_id','date');
             if(!empty($state))
             {
                 $new_arr_data_data->whereIn('l3_id',$request->area);
             }
             $new_arr_data = $new_arr_data_data->get();
             
         foreach ($new_arr_data as $product_data => $product_value) 
         {
            
             $c0_id = $product_value->c0_id;
             $date = $product_value->date;
             $user_id = $product_value->user_id;
             $new_arr[$user_id.$date][$c0_id]['total_price'] = $product_value->total_price;
             $new_arr[$user_id.$date][$c0_id]['total_weight'] = $product_value->total_weight;
             $new_arr[$user_id.$date][$c0_id]['total_row'] = $product_value->total_row;
             
         }
        
        
        $visit_count_data = DB::table('user_sales_order')->join('location_view','location_view.l5_id','=','user_sales_order.location_id')->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from' and DATE_FORMAT(date,'%Y-%m-%d') <='$to'")->groupBy('date','user_id');
        if(!empty($state))
         {
             $visit_count_data->whereIn('l3_id',$request->area);
         }
         if (!empty($region)) 
         {
             
             $visit_count_data->whereIn('location_view.l2_id', $region);
         }
         if (!empty($user)) 
         {
             
             $visit_count_data->whereIn('user_id', $user);
         }
 
        $visit_count = $visit_count_data->pluck(DB::raw("COUNT(DISTINCT id) as count"),DB::raw("CONCAT(user_id,date)"));
 
 
        $productive_calls = DB::table('user_sales_order')->where('call_status',1)->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from' and DATE_FORMAT(date,'%Y-%m-%d') <='$to'")->groupBy('user_id','date')->pluck(DB::raw("COUNT(id) as productive_count"),DB::raw("CONCAT(user_id,date)"));
 
 
        $other_data_data = DB::table('user_sales_order')
         ->leftJoin('location_view', 'location_view.l5_id', '=', 'user_sales_order.location_id')
         ->join('dealer', 'dealer.id', '=', 'user_sales_order.dealer_id')
         ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from' and DATE_FORMAT(date,'%Y-%m-%d') <='$to'")
         ->select('user_id AS sale_user_id','date as sale_date','location_id', 'location_view.l5_name', 'location_view.l4_name', 'user_sales_order.dealer_id', 'dealer.name as dealer_name','l5_id',
             DB::raw("(select COUNT(id) from retailer where location_id = user_sales_order.location_id )as total_outlet"))
         ->groupBy('sale_user_id','sale_date')
         ->distinct();
         if(!empty($state))
         {
             $other_data_data->whereIn('l3_id',$request->area);
         }
         if (!empty($region)) {
             
             $other_data_data->whereIn('location_view.l2_id', $region);
         }
         if (!empty($user)) {
             
             $other_data_data->whereIn('user_id', $user);
         }
 
        $other_data = $other_data_data->get();
   
         if (!empty($other_data)) 
         {
             foreach ($other_data as $other_key => $other_value) 
             {
                 $user_id = $other_value->sale_user_id;
                 $date = $other_value->sale_date;
 
                 $otherArr[$user_id.$date]['beat'] = $other_value->l5_name;
                 $otherArr[$user_id.$date]['town'] = $other_value->l4_name;
                 $otherArr[$user_id.$date]['dealer'] = $other_value->dealer_name;
                 $otherArr[$user_id.$date]['beat_id'] = $other_value->location_id;
                 $otherArr[$user_id.$date]['total_outlet'] = $other_value->total_outlet;
 
             }
         }
            
           
         $new_outlet_data  = DB::table('retailer')
             ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','retailer.dealer_id')
             ->join('location_view','location_view.l5_id','=','retailer.location_id')
             ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d') >='$from' and DATE_FORMAT(created_on,'%Y-%m-%d') <='$to'")
             ->groupBy('user_id','retailer.location_id');
              if(!empty($state))
             {
                 $new_outlet_data->whereIn('l3_id',$request->area);
             }
             if (!empty($region)) {
                 
                 $new_outlet_data->whereIn('l2_id', $region);
             }
             if (!empty($user)) {
                 
                 $new_outlet_data->whereIn('user_id', $user);
             }
             $new_outlet = $new_outlet_data->pluck(DB::raw("COUNT(DISTINCT retailer.id) as retailer_id"),DB::raw("CONCAT(user_id,DATE_FORMAT(created_on,'%Y-%m-%d'))"));
 
             // dd($new_outlet);
         $awsome_query = DB::table('person')->join('person_login','person_login.person_id','=','person.id')->join('user_daily_attendance', 'user_daily_attendance.user_id', 'person.id')
         ->join('location_view','location_view.l3_id','=','person.state_id')
         ->join('_role','_role.role_id','=','person.role_id')
         ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
         ->select('person_login.person_status as status',DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as uname"),'person.id as user_id','location_view.l5_id as l5_id','location_view.l1_name','location_view.l2_name','location_view.l3_name','person.emp_code','person.head_quar','person.region_txt','user_daily_attendance.work_date','_role.rolename',DB::raw("(select CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) from person WHERE person.id=user_daily_attendance.working_with limit 0,1) as working_with"),'_working_status.name as w_s',DB::raw("DATE_FORMAT(work_date,'%d-%m-%Y') AS work_dates"),DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d') AS work_date"),DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') AS work_time"))
         ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$from' AND DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$to'")
         ->where('person_status','!=',9)
         ->groupBy('uname','w_s','person.id','user_daily_attendance.work_date','rolename','working_with')
         ->orderBy('user_daily_attendance.work_date','ASC');
 
        
         //dd($awsome_query);
           #Senior Data
         if (!empty($datasenior)) 
         {
             $awsome_query->whereIn('person.id', $datasenior);
         }
 
             #Region filter
         if (!empty($request->region)) {
             $region = $request->region;
             $awsome_query->whereIn('location_view.l2_id', $region);
         }
         #State filter
         if (!empty($request->area)) {
             $state = $request->area;
             $awsome_query->whereIn('location_view.l3_id', $state);
         }
         #Status filter
         if (!empty($status)) {
             $awsome_query->whereIn('person_login.person_status', $status);
         }
 
         #Role filter
         if (!empty($request->role)) {
             $role_id = $request->role;
             $awsome_query->whereIn('person.role_id', $role_id);
         }
         #User Filter
         if (!empty($request->user)) 
         {
             $ud = $request->user;
             $awsome_query->whereIn('person.id', $ud);
         }
 
         $query=$awsome_query->get();


         $output .="S.No,Zone,Region,State,Emp Code,User FullName,User HQ,Belt/Tert/Region/Area,Day,Date,Designation,Worked With,As Per Tour Program(Town),As Per Tour Program(Distributor),As Per Tour Program(Belt),Start Time,Today's Task,Actual(Town),Actual(Distributor),Actual(Beat),Status,Time Of First Call,Time Of Last Call,End Time,Total Outlets,Visit Today,Today's Productive Calls,New Outlets Added Today,SEC SALES(KG),SEC SALES(RV),INT. DIST SALE(KG),INT. DIST SALE(RV),TOTAL SALE SEC + ID(KG),TOTAL SALE SEC + ID(RV),FCT(CALLS),FCT(RD KG),FCT(RV),FPC(CALLS),FPC(RD KG),FPC(RV),B-KOOL(CALLS),B-KOOL(RD KG),B-KOOL(RV),ELAICHI(CAllS),ELAICHI(RD KG),ELAICHI(RV),CANDY(CALLS),CANDY(RD KG),CANDY(RV),AGARBATTI(CALLS),AGARBATTI(RD KG),AGARBATTI(RV),PELLET(CALLS),PELLET(RD KG),PELLET(RV),CHIPS(CALLS),CHIPS(RD KG),CHIPS(RV),RINGS & PUFFS(CALLS),RINGS & PUFFS(RD KG),RINGS & PUFFS(RV),TEDHA KRUNCH(CALLS),TEDHA KRUNCH(RD KG),TEDHA KRUNCH(RV),NAMKEEN(CALLS),NAMKEEN(RD KG),NAMKEEN(RV),MATCHBOX(CALLS),MATCHBOX(RD KG),MATCHBOX(RV)";
         $output .="\n";
         $i=1;
 
         $sa = [];
         $beatArr = [];
         $townArr = [];
         $dealer_name = [];
       
         $mtp_beat=[];
         $out=[];
                        
             foreach ($query as $key => $data) {
                 
                 $user=$data->user_id;
                 $date=$data->work_date;
                 $index=$user.$date;

               $out[$index]['mtp']=DB::table('monthly_tour_program')
                 ->leftJoin('dealer','dealer.id','=','monthly_tour_program.dealer_id')
                 ->leftJoin('location_6','location_6.id','=','monthly_tour_program.town')
                 ->leftJoin('location_7','location_7.id','=','monthly_tour_program.locations')
                 ->select('location_6.name as l4_name','dealer.name as dname','location_7.name as l5_name','location_7.id AS l5_id')
                 ->where('monthly_tour_program.person_id',$user)
                 ->whereRaw("DATE_FORMAT(monthly_tour_program.working_date, '%Y-%m-%d') = DATE_FORMAT('$date', '%Y-%m-%d')")
                 ->first();
              //    $mtp_beat[$index]=!empty($out[$index]['mtp']->l5_id)?$out[$index]['mtp']->l5_id:'0';


              $workedWith = preg_replace('/[^A-Za-z0-9\-]/', ' ', !empty($data->working_with)?$data->working_with:'SELF');
              $outMtpL4 = preg_replace('/[^A-Za-z0-9\-]/', ' ', !empty($out[$index]['mtp']->l4_name)?$out[$index]['mtp']->l4_name:'N/A');
              $outMtpDname = preg_replace('/[^A-Za-z0-9\-]/', ' ', !empty($out[$index]['mtp']->dname)?$out[$index]['mtp']->dname:'N/A');
              $outMtpL5 = preg_replace('/[^A-Za-z0-9\-]/', ' ', !empty($out[$index]['mtp']->l5_name)?$out[$index]['mtp']->l5_name:'N/A');


              $otherArrTown = preg_replace('/[^A-Za-z0-9\-]/', ' ', !empty($otherArr[$index]['town'])?$otherArr[$index]['town']:'N/A');
              $otherArrDealer = preg_replace('/[^A-Za-z0-9\-]/', ' ', !empty($otherArr[$index]['dealer'])?$otherArr[$index]['dealer']:'N/A');
              $otherArrBeat = preg_replace('/[^A-Za-z0-9\-]/', ' ', !empty($otherArr[$index]['beat'])?$otherArr[$index]['beat']:'N/A');


              $time_of_first_call1 = preg_replace('/[^A-Za-z0-9\-:]/', ' ', !empty($time_of_first_call[$index])?$time_of_first_call[$index]:'N/A');
             
              $time_of_last_call1 = preg_replace('/[^A-Za-z0-9\-:]/', ' ', !empty($time_of_last_call[$index])?$time_of_last_call[$index]:'N/A');
              $endTime = preg_replace('/[^A-Za-z0-9\-:]/', ' ', !empty($checkout[$index])?date('H:i:s',strtotime($checkout[$index])):'N/A');


              $otherArrTotalOutlet = preg_replace('/[^A-Za-z0-9\-]/', ' ', !empty($otherArr[$index]['total_outlet'])?$otherArr[$index]['total_outlet']:'0');
              $visitCount = preg_replace('/[^A-Za-z0-9\-]/', ' ', !empty($visit_count[$index])?$visit_count[$index]:'0');
              $productiveCalls = preg_replace('/[^A-Za-z0-9\-]/', ' ', !empty($productive_calls[$index])?$productive_calls[$index]:'0');
              $newOutlet = preg_replace('/[^A-Za-z0-9\-]/', ' ', !empty($new_outlet[$index])?$new_outlet[$index]:'0');
              $userStatus = preg_replace('/[^A-Za-z0-9\-]/', ' ', !empty($data->status==1)?'Active':'De-Active');

                 
                  $output .=$i.',';
                  $output .=' '.$data->l1_name.' '.',';
                  $output .=' '.$data->l2_name.' '.',';
                  $output .=' '.$data->l3_name.' '.',';
                  $output .=$data->emp_code.',';
                  $output .=$data->uname.',';
                  $output .=$data->head_quar.',';
                  $output .=$data->region_txt.',';
                  $output .=date('l',strtotime($data->work_date)).',';
                  $output .=$data->work_date.',';
                  $output .=$data->rolename.',';
                  $output .=$workedWith.',';
                  $output .=$outMtpL4.',';
                  $output .=$outMtpDname.',';
                  $output .=$outMtpL5.',';
                  $output .=$data->work_time.',';
                  $output .=$data->w_s.',';
                  $output .=$otherArrTown.',';
                  $output .=$otherArrDealer.',';
                  $output .=$otherArrBeat.',';  
                  $output .=$userStatus.',';
                  $output .=$time_of_first_call1.',';
                  $output .=$time_of_last_call1.',';
                  $output .=$endTime.',';
                  $output .= $otherArrTotalOutlet.',';  
                  $output .= $visitCount.',';  
                  $output .= $productiveCalls.',';  
                  $output .= $newOutlet.',';  
                
                  $temp_kg1 = !empty($temp_kg[$index])?$temp_kg[$index]/'1000':'0';
                  $temp_rv1 = !empty($temp_rv[$index])?$temp_rv[$index]:'0';

                  
                  
                  $output .= $temp_kg1.','; 
                  $output .= $temp_rv1.',';  

                 
                  $output .= '0'.',';
                  $output .= '0'.',';  

                 
                  $output .= $temp_kg1.',';  
                  $output .= $temp_rv1.',';  

              
                  foreach ($catalog as $key5 => $data2)
                  {
                    
                    $total_row = !empty($new_arr[$index][$key5]['total_row'])?$new_arr[$index][$key5]['total_row']:'0';

                    $total_weight = !empty($new_arr[$index][$key5]['total_weight'])?$new_arr[$index][$key5]['total_weight']/'1000':'0';

                    $total_price = !empty($new_arr[$index][$key5]['total_price'])?$new_arr[$index][$key5]['total_price']:'0';


                    $output .= $total_row.',';  

                    $output .=$total_weight.',';

                    $output .=$total_price.',';
                   

            }
           
//  dd($output);
            $output .="\n";
            $i++;
           
            $dataCount=1;

      }
    
      header("Content-type: text/csv");
      header("Content-Disposition: attachment; filename=DailyPerformanceReport.csv");
      header("Pragma: no-cache");
      header("Expires: 0");
      echo $output;
 }
 ############# Export End For Day Wise Performance ################

    public function rds_monthly(Request $request)
    {
        // dd($request);
        $state = $request->state; 
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $company_id = Auth::user()->company_id;
        $catalog_product = DB::table('catalog_product')->where('catalog_product.company_id',$company_id)->where('status',1)->groupBy('id')->orderBy('id','asc')->pluck("name","id")->toArray();
        
        $person_details = Person::join('person_login','person_login.person_id','=','person.id')
                        ->join('location_3','location_3.id','=','person.state_id')
                        ->join('user_sales_order','user_sales_order.user_id','=','person.id')
                        ->select('user_sales_order.date as date','location_3.name as state_name',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as person_fullname"),'person.id as user_id','person.state_id as state_id')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
                        ->where('user_sales_order.company_id',$company_id)
                        ->where('person_login.company_id',$company_id)
                        ->groupBy('user_id','date')->orderBy('date','ASC')->orderBy('user_id','ASC');

        if(!empty($state))
        {
            $person_details->where('person.state_id',$state);
        }

        $person = $person_details->get();
        // dd($person);
        $dsr =  DB::table('user_sales_order')->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")->where('user_sales_order.company_id',$company_id)->groupBy('product_id','user_id','date')->pluck(DB::raw("SUM(user_sales_order_details.quantity) as product_quantity"),DB::raw("CONCAT(user_id,product_id,date)"));
        // dd($dsr);
        $market_data  = DB::table('user_sales_order as uso')->join('location_7','location_7.id','=','uso.location_id')->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")->groupBy('user_id','date')->where('uso.company_id',$company_id)->pluck('location_7.name as market',DB::raw("CONCAT(user_id,date)"));

        $total_call_data = DB::table('user_sales_order')->where('user_sales_order.company_id',$company_id)->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")->groupBy('user_id','date')->pluck(DB::raw('count(call_status) as total_call'),DB::raw("CONCAT(user_id,date)"));

        $productive_call_data = DB::table('user_sales_order')->where('user_sales_order.company_id',$company_id)->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")->where('call_status',1)->groupBy('user_id','date')->pluck(DB::raw('count(call_status) as total_call'),DB::raw("CONCAT(user_id,date)"));

        $product_amount_data = DB::table('user_sales_order')->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')->where('user_sales_order.company_id',$company_id)->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")->groupBy('user_id','date')->pluck(DB::raw("SUM(quantity * rate) as product_quantity_amount"),DB::raw("CONCAT(user_id,date)"));

        $out=array();
        $total = array();
        if (!empty($person)) {
                foreach ($person as $k => $d) {
                    $uid=$d->user_id;
                    $date= $d->date;
                    $out[$uid][$date]['user'] = $uid;
                    $out[$uid][$date]['date'] = $date;
                    $out[$uid][$date]['market'] = !empty($market_data[$uid.$date])?$market_data[$uid.$date]:'0';

                    $out[$uid][$date]['total_call'] = !empty($total_call_data[$uid.$date])?$total_call_data[$uid.$date]:'0';

                    $out[$uid][$date]['productive_call'] = !empty($productive_call_data[$uid.$date])?$productive_call_data[$uid.$date]:'0';

                    $out[$uid][$date]['product_amount'] = !empty($product_amount_data[$uid.$date])?$product_amount_data[$uid.$date]:'0';
                }
            }
             // dd($catalog_product);
            
            $string = implode(',',$catalog_product);
            // dd($string);
            $output = '';
            $output .="S.No,Date,State,Name of Emp,Market,T.C,P.C,% Productivity,$string,Product Amount";
            $output .="\n";
            $i=1;
            foreach ($person as $key => $value) 
            {
                        
                    $date = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value["date"]);
                    $state_name = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value["state_name"]);
                    $person_fullname = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value["person_fullname"]);
                    $market = preg_replace('/[^A-Za-z0-9\-]/', ' ', $out[$value->user_id][$value->date]["market"]);
                    $total_call = preg_replace('/[^A-Za-z0-9\-]/', ' ', $out[$value->user_id][$value->date]["total_call"]);
                    $productive_call = preg_replace('/[^A-Za-z0-9\-]/', ' ', $out[$value->user_id][$value->date]["productive_call"]);
                    $product_amount = preg_replace('/[^A-Za-z0-9\-.]/', ' ', $out[$value->user_id][$value->date]["product_amount"]);
                    $g_product_amount[] = $product_amount;

                    $output .=$i.',';

                    $output .=$date.',';
                    $output .=$state_name.',';
                    $output .=$person_fullname.',';
                    $output .=$market.',';
                    $output .=$total_call.',';
                    $output .=$productive_call.',';
                    $output .=round($productive_call/$total_call*100).',';
                    foreach ($catalog_product as $c_key => $c_value) 
                    {
                        $product_value = !empty($dsr[$value->user_id.$c_key.$value->date])?$dsr[$value->user_id.$c_key.$value->date]:'0';
                        $total[$c_key][] = !empty($dsr[$value->user_id.$c_key.$value->date])?$dsr[$value->user_id.$c_key.$value->date]:'0';
                        $output .=$product_value.',';
                    }
                    $output .=$product_amount.',';

                    
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;
            }
            foreach ($catalog_product as $g_key => $g_value) 
            {

                $grand_total_string_data[] = !empty($total[$g_key])?array_sum($total[$g_key]):'0';
            }
            $grand_total_string = implode(',',$grand_total_string_data);
            $grand_product_amount = !empty($g_product_amount)?array_sum($g_product_amount):'0';
            $output .="Grand Total, , , , , , , ,$grand_total_string,$grand_product_amount";



                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=DSR Monthly.csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                echo $output;
                


    }

     public function export_sale_data(Request $request)
    {
        // $explodeDate = explode(" -", $request->date_range_picker);
        // $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        // $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $user_id = $request->user_id;
        $state = $request->region;
        $location_5 = $request->location5;
        $dealer = $request->dealer;
        $division_id = $request->division_id;
        $location_6 = $request->location6;
        $location_7 = $request->location7;
        $call_status = $request->call_status;
        $product_id = $request->product_id;
        $company_id = Auth::user()->company_id;
        $table_name = TableReturn::table_return($from_date,$to_date);

         $product_percentage = DB::table('product_wise_scheme_plan_details')
                                ->where('incentive_type',1)
                                ->where('company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$from_date' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$to_date'")
                                ->pluck('value_amount_percentage',DB::raw("concat(product_id,state_id) as data"));


        $sale_data = DB::table($table_name)
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                    ->join('location_view','location_view.l7_id','=',$table_name.'.location_id')
                    ->join('person','person.id','=',$table_name.'.user_id')
                    ->join('dealer','dealer.id','=',$table_name.'.dealer_id')
                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                    // ->join('_division','_division.id','=','dealer.division_id')
                    ->join('retailer','retailer.id','=',$table_name.'.retailer_id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->select('product_id','call_status','rolename','user_sales_order_details.order_id as oid',DB::raw("DATE_FORMAT($table_name.date,'%d-%m-%Y') AS sale_date"),'l3_name as state','l3_id','l4_name as head_quar','l5_name as district','l6_name as town_city','l7_name as beat','dealer.name as dealer_name','retailer.name as retailer_name',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'catalog_product.name as product_name',DB::raw("SUM(rate*quantity) as sale_value"),DB::raw("SUM(quantity) as quantity"),$table_name.'.remarks as remarks','user_sales_order_details.scheme_qty as weight','user_sales_order_details.rate','user_sales_order_details.product_id')
                    ->whereRaw("DATE_FORMAT($table_name.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT($table_name.date,'%Y-%m-%d')<='$to_date'")
                    ->where('person_status',1)
                    ->where($table_name.'.company_id',$company_id)
                    ->where('person.company_id',$company_id)
                    ->groupBy('user_sales_order_details.product_id','user_sales_order_details.order_id',$table_name.'.user_id');
                    if(!empty($user_id))
                    {
                        $sale_data->whereIn($table_name.'.user_id',$user_id);
                    }
                    if(!empty($prduct_id))
                    {
                        $sale_data->whereIn('user_sales_order_details.prduct_id',$prduct_id);
                    }
                    if(!empty($state))
                    {
                        $sale_data->whereIn('l3_id',$state);
                    }
                    if(!empty($location_5))
                    {
                        $sale_data->whereIn('l5_id',$location_5);
                    }
                    if(!empty($location_6))
                    {
                        $sale_data->whereIn('l6_id',$location_6);
                    }
                    if(!empty($dealer))
                    {
                        $sale_data->whereIn($table_name.'.dealer_id',$dealer);
                    }
                    if(!empty($division_id))
                    {
                        $sale_data->whereIn('division_id',$division_id);
                    }
                    if(!empty($location_7))
                    {
                        $sale_data->whereIn('l7_id',$location_7);
                    }
                    if(!empty($call_status))
                    {
                        $sale_data->whereIn('call_status',$call_status);
                    }
                    $sale_data_fetch = $sale_data->get();
                    // dd($sale_data_fetch);    
        // $product_division = DB::table('catalog_product')
        //                     ->join('_division','_division.id','=','catalog_product.division')
        //                     ->where('catalog_product.status',1)
        //                     ->where('catalog_product.company_id',$company_id)
        //                     ->groupBy('catalog_product.id')
        //                     ->pluck('_division.name as product_division','catalog_product.id as id');

        $output = '';
        $output .='S.No,Date,State,Head Quater,District/City,Town/Area/Zone,Beat,Dealer Name,Retailer Name,User Name,Role,Call Status,Product Name,Rate,Quantity,Scheme Qty,Total,Remark';
        $output .="\n";
        $i = 1;
        foreach ($sale_data_fetch as $key => $value) 
        {

            $product_percent = !empty($product_percentage[$value->product_id.$value->l3_id])?$product_percentage[$value->product_id.$value->l3_id].'%':'';
            if(!empty($product_percentage[$value->product_id.$value->l3_id])){
                $value_find = ($value->rate*$value->quantity)*$product_percentage[$value->product_id.$value->l3_id]/100;
            }else{
                $value_find = 0;
            }

            $final_sale = ($value->rate*$value->quantity - $value_find);

            // $retailer_name = preg_replace('/][^A-Za-z0-9\-.@|[]/', ' ',$value->retailer_name);
            $retailer_name = str_replace(',', ' ',$value->retailer_name);
            // $dealer_name = preg_replace('/][^A-Za-z0-9\-.@|[]/', ' ',$value->dealer_name);
            $dealer_name = str_replace(',', ' ',$value->dealer_name);
            // $product_name = preg_replace('/][^A-Za-z0-9\-.@|[]/', ' ',$value->product_name);
            $product_name = str_replace(',', ' ',$value->product_name);
            // $division_name = preg_replace('/][^A-Za-z0-9\-.@|[]/', ' ',$value->division_name);
            // $division_name = str_replace(',', ' ',$value->division_name);
            // $user_name = preg_replace('/][^A-Za-z0-9\-.@|[]/', ' ',$value->user_name);
            $user_name = str_replace(',', ' ',$value->user_name);
            // $beat = preg_replace('/][^A-Za-z0-9\-.@|[]/', ' ',$value->beat);
            $beat = str_replace(',', ' ',$value->beat);
            // $town_city = preg_replace('/][^A-Za-z0-9\-.@|[]/', ' ',$value->town_city);
            $town_city = str_replace(',', ' ',$value->town_city);
            // $head_quar = preg_replace('/][^A-Za-z0-9\-.@|[]/', ' ',$value->head_quar);
            $head_quar = str_replace(',', ' ',$value->head_quar);
            // $state = preg_replace('/][^A-Za-z0-9\-.@|[]/', ' ',$value->state);
            $state = str_replace(',', ' ',$value->state);
            // $district = preg_replace('/][^A-Za-z0-9\-.@|[]/', ' ',$value->district);
            $district = str_replace(',', ' ',$value->district);
            // $sale_date = preg_replace('/][^A-Za-z0-9\-.@|[]/', ' ',$value->sale_date);
            $sale_date = str_replace(',', ' ',$value->sale_date);
            // $rolename = preg_replace('/][^A-Za-z0-9\-.@|[]/', ' ',$value->rolename);
            $rolename = str_replace(',', ' ',$value->rolename);
            // $remarks = preg_replace('/][^A-Za-z0-9\-.@|[]/', ' ',$value->remarks);
            $remarks = str_replace(',', ' ',$value->remarks);
            // $product_division_name = !empty($product_division[$value->product_id])?preg_replace('/][^A-Za-z0-9\-.@|[]/', ' ',$product_division[$value->product_id]):'';
            // $product_division_name = !empty($product_division[$value->product_id])?str_replace(',', ' ',$product_division[$value->product_id]):'';
            $call_status = ($value->call_status==1)?'Productive':'Non-Productive';

                    $output .=$i.',';
                    $output .=$sale_date.',';
                    $output .=$state.',';
                    $output .=$head_quar.',';
                    $output .=$district.',';
                    $output .=$town_city.',';
                    $output .=$beat.',';
                    // $output .=$division_name.',';
                    $output .=$dealer_name.',';
                    $output .=$retailer_name.',';
                    $output .=$user_name.',';
                    $output .=$rolename.',';
                    $output .=$call_status.',';
                    $output .=$product_name.',';
                    // $output .=$product_division_name.',';
                    $output .=$value->rate.',';
                    $output .=$value->quantity.',';
                    $output .=$product_percent.',';
                    $output .=round($final_sale,2).',';
                    $output .=$remarks.',';
                    $output .="\n";
                    $i++;
        }
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=SaleData.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $output;
    }

}
