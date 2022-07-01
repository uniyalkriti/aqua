<?php

namespace App\Http\Controllers;


use App\Person;
use Illuminate\Http\Request;
use App\Dealer;
use App\Retailer;
use App\JuniorData;
use App\Location3;
use DB;
use DateTime;
use Auth;
use Session;
use App\UserSalesOrder;
use App\TableReturn;
use App\SecondarySale;
use Illuminate\Support\Facades\Crypt;
use App\ChallanOrder;

class HomeControllerNew extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->menu = DB::table('_modules')->orderBy('module_sequence')->get();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        // dd($request);
        $check_dashboard = Session::get('dashboard_new_new');// this variable get the session values
        // dd($check_dashboard);
        // dd($check_dashboard[0]['is_set']);
        $current_menu='DASHBOARD';
        $user=Auth::user();
        $company_id = Auth::user()->company_id;
        $location_3_filter = $request->location3;
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

        

            if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
            {
                $junior_data = array();
            }
            else
            {
                Session::forget('juniordata');
                $user_data=JuniorData::getJuniorUser($user->id,$company_id);
                Session::push('juniordata', $user->id);
                $junior_data = Session::get('juniordata');
            }

            
            
             // seesion check end here     
            $cdate=date('Y-m-d'); // current date
            // dd($cdate);
            if(isset($request->date_range_picker))
            {
                // dd($request);
                $explodeDate = explode(" -", $request->date_range_picker);
                $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
                $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
                $mdate = date('Y-m',strtotime(trim($explodeDate[0])));
            }
            else
            {
                $mdate=date('Y-m');
               
                $from_date = date('Y-m-01');
                $to_date = date('Y-m-t');
            }
        

        // get table name for sales 

        $table_name = TableReturn::table_return($from_date,$to_date);
        // dd($table_name);

        // ends here 

        # for filter 
        $location3 = Location3::where('company_id',$company_id)->where('status',1)->pluck('name','id');
        // dd($to_date);
        $work_status_array = DB::table('_working_status')->where('company_id',$company_id)->where('status',1)->orderBy('sequence','ASC')->pluck('name','id');

        $role_wise_attendance_data = DB::table('user_daily_attendance')
                                ->join('person','person.id','=','user_daily_attendance.user_id')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('_role','_role.role_id','=','person.role_id')
                                ->where('user_daily_attendance.company_id',$company_id)
                                ->where('person_status',1)
                                ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$cdate'")
                                ->groupBy('person.role_id','work_status');
                                if(!empty($junior_data))
                                {
                                    $role_wise_attendance_data->whereIn('person.id',$junior_data);
                                }
                                if(!empty($location_3_filter))
                                {
                                    $role_wise_attendance_data->whereIn('person.state_id',$location_3_filter);
                                }
        $role_wise_attendance = $role_wise_attendance_data->pluck(DB::raw("COUNT(work_status)"),DB::raw("CONCAT(person.role_id,work_status) as id"));
        // USER DETAILS
        $totalSalesTeamData=Person::join('person_login','person_login.person_id','=','person.id')
                        ->join('users','users.id','=','person.id')
                        ->join('person_details','person_details.person_id','=','person.id')
                        // ->whereRaw("DATE_FORMAT(person_details.created_on,'%Y-%m-%d')<='$to_date'")
                        ->where('person_status',1)
                        ->where('is_admin','!=',1)
                        ->where('person.company_id',$company_id)
                        ->where('person_login.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $totalSalesTeamData->whereIn('person.id',$junior_data);
                        }
                        if(!empty($location_3_filter))
                        {
                            $totalSalesTeamData->whereIn('person.state_id',$location_3_filter);
                        }
        $totalSalesTeam = $totalSalesTeamData->count();


        $deactivateTeam = Person::join('person_login','person_login.person_id','=','person.id')
                        ->join('users','users.id','=','person.id')
                        ->join('person_details','person_details.person_id','=','person.id')
                        ->whereRaw("DATE_FORMAT(person_details.deleted_deactivated_on,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(person_details.deleted_deactivated_on,'%Y-%m-%d')<='$to_date'")
                        ->where('person_status','!=',1)
                        ->where('is_admin','!=',1)
                        ->where('person.company_id',$company_id)
                        ->where('person_login.company_id',$company_id)
                        // ->groupBy('person.id')
                        ->count();
                        

                        // dd($deactivateTeam);
        $totalSalesTeam = $totalSalesTeam+$deactivateTeam;



        $totalAttdData=DB::table('user_daily_attendance')
                ->where('user_daily_attendance.company_id',$company_id)
                ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$cdate'");
                if(!empty($junior_data))
                {
                    $totalAttdData->whereIn('user_id',$junior_data);
                }
                if(!empty($location_3_filter))
                {
                    $totalAttdData->join('person','person.id','=','user_daily_attendance.user_id')->whereIn('person.state_id',$location_3_filter);
                }
        $totalAttd = $totalAttdData->count();
                // dd($totalAttd);
        if(empty($totalAttd))
        {
            $totalAttd=0;
        }

        $dynamic_status = array("0","1");
        // END OF USER DETAILE
        // SS DETAILS
        // END OF SS DETAILS
        // DISTRIBUTOR DETAILS
        $totalDistributorData=Dealer::whereIn('dealer_status',$dynamic_status)
                        ->where('dealer.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $totalDistributorData->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id');
                            $totalDistributorData->whereIn('dealer_location_rate_list.user_id',$junior_data);

                        }
                        if(!empty($location_3_filter))
                        {
                            $totalDistributorData->whereIn('dealer.state_id',$location_3_filter);
                        }
                        $totalDistributor = $totalDistributorData->count();

        $totalDistributorSaleData=DB::table($table_name)
                            ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                            ->where('company_id',$company_id)
                            ->select(DB::raw('count(DISTINCT dealer_id) as dealersale'));
                            if(!empty($junior_data))
                            {
                                $totalDistributorSaleData->whereIn('user_id',$junior_data);

                            }
                            if(!empty($location_3_filter))
                            {
                                $totalDistributorSaleData->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
                            }
        $totalDistributorSale = $totalDistributorSaleData->first();
        // END OF DISTRIBUTOR DETAILS
        // OUTLET DETAILS

        // $Retailer_Query_data = DB::table('retailer')
        //                     ->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','retailer.location_id')
        //                     ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
        //                     ->join('location_view','location_view.l7_id','=','retailer.location_id')
        //                     ->leftJoin('person','person.id','=','retailer.created_by_person_id')
        //                     ->leftJoin('_retailer_outlet_type','_retailer_outlet_type.id','=','retailer.outlet_type_id')
        //                     ->where('retailer.company_id',$company_id)
        //                     ->where('retailer_status','!=',9)
        //                     ->where('dealer_status','!=',9)
        //                     ->orderBy('l1_name','ASC');
        // // dd($Retailer_Query);
        //    if(!empty($junior_data))
        // {
        //     $Retailer_Query_data->whereIn('created_by_person_id',$junior_data);
        // }
        // if(!empty($location_3_filter))
        // {
        //     $Retailer_Query_data->join('location_view','location_view.l7_id','=','retailer.location_id')->whereIn('l3_id',$location_3_filter);
        // }

        // $totalOutlet = $Retailer_Query_data->distinct('retailer.id')->count('retailer.id');

        $totalOutlet = 0;


        // $totalOutletData=Retailer::whereIn('retailer_status',$dynamic_status)
        //             ->where('company_id',$company_id);
        //             if(!empty($junior_data))
        //             {
        //                 $totalOutletData->whereIn('created_by_person_id',$junior_data);
        //             }
        //             if(!empty($location_3_filter))
        //             {
        //                 $totalOutletData->join('location_view','location_view.l7_id','=','retailer.location_id')->whereIn('l3_id',$location_3_filter);
        //             }
        // $totalOutlet =  $totalOutletData->count();
        // dd($totalOutlet);

        $totalOutletSaleData=DB::table($table_name)
                        ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                        // ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                        ->where('company_id',$company_id)
                        ->select(DB::raw('count(DISTINCT retailer_id) as outletsale'));
                        if(!empty($junior_data))
                        {
                            $totalOutletSaleData->whereIn('user_id',$junior_data);

                        }
                        if(!empty($location_3_filter))
                        {
                            $totalOutletSaleData->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
                        }
        $totalOutletSale = $totalOutletSaleData->first();
        // dd($totalOutletSale);
        // END OF OUTLET DETAILS
        // BEAT DETAILS
        $location_5Data = DB::table('location_7')
                    ->where('location_7.status',1)
                    ->where('location_7.company_id',$company_id);
                    if(!empty($junior_data))
                    {
                        $location_5Data->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','location_7.id');
                        $location_5Data->whereIn('dealer_location_rate_list.user_id',$junior_data);

                    }
                    if(!empty($location_3_filter))
                    {
                        $location_5Data->join('location_view','location_view.l7_id','=','location_7.id')->whereIn('l3_id',$location_3_filter);
                    }

        $location_5 = $location_5Data->count();

        $totalBeatSaleData=DB::table($table_name)
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                    ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                    ->where($table_name.'.company_id',$company_id)
                    ->select(DB::raw('count(DISTINCT location_id) as beatsale'));
                    if(!empty($junior_data))
                    {
                        $totalBeatSaleData->whereIn('user_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $totalBeatSaleData->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
                    }
        $totalBeatSale = $totalBeatSaleData->first();
        //  beat coverage starts here
        
        //  beat coverage ends here

       // END OF BEAT DETAILS
        // CALL
        $totalCallData=DB::table($table_name)
                    ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                    ->where('company_id',$company_id)
                    ->select(DB::raw('count(DISTINCT retailer_id,date) as total_call'));
                    if(!empty($junior_data))
                    {
                        $totalCallData->whereIn($table_name.'.user_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $totalCallData->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
                    }
        $totalCall =  $totalCallData->first();

        $productiveCallData=DB::table($table_name)
                        ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                        // ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                        ->where('company_id',$company_id)
                        ->select(DB::raw('count(DISTINCT retailer_id,date) as productive_call'))->where('call_status',1);
                        if(!empty($junior_data))
                        {
                            $productiveCallData->whereIn($table_name.'.user_id',$junior_data);
                        }
                        if(!empty($location_3_filter))
                        {
                            $productiveCallData->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
                        }
        $productiveCall = $productiveCallData->first();

        // productive data details starts here
        
                                // dd($productve_call_details);
        $call_status_count = array();
        
        // dd($call_status_count);
        // END CALL
        // SALE START
        // if(empty($check)){
        // $totalOrderData=DB::table('user_sales_order')
        //             ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        //             ->select(DB::raw("sum(rate*quantity) AS total_sale_value"))
        //             ->where('user_sales_order.company_id',$company_id)
        //             ->where('user_sales_order_details.company_id',$company_id)
        //             ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'");
        //             if(!empty($junior_data))
        //             {
        //                 $totalOrderData->whereIn('user_sales_order.user_id',$junior_data);
        //             }
        //             if(!empty($location_3_filter))
        //             {
        //                 $totalOrderData->join('location_view','location_view.l7_id','=','user_sales_order.location_id')->whereIn('l3_id',$location_3_filter);
        //             }
        // $totalOrder = $totalOrderData->first();
        // }else{
        //     $totalOrderData=DB::table('user_sales_order')
        //     ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        //     ->select(DB::raw("round(sum(final_secondary_rate*final_secondary_qty),2) AS total_sale_value"))
        //     ->where('user_sales_order.company_id',$company_id)
        //     ->where('user_sales_order_details.company_id',$company_id)
        //     ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'");
        //     if(!empty($junior_data))
        //     {
        //         $totalOrderData->whereIn('user_sales_order.user_id',$junior_data);
        //     }
        //     if(!empty($location_3_filter))
        //     {
        //         $totalOrderData->join('location_view','location_view.l7_id','=','user_sales_order.location_id')->whereIn('l3_id',$location_3_filter);
        //     }
        //     $totalOrder = $totalOrderData->first();
        // }
        if(empty($totalOrder))
            $totalOrder=0;
        // if(empty($check)){
        // $totalPrimaryOrderData=DB::table('primary_sale_view')
        //                 ->whereRaw("DATE_FORMAT(primary_sale_view.sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(primary_sale_view.sale_date,'%Y-%m-%d')<='$to_date'")
        //                 ->select(DB::raw("sum((rate*pcs)+(cases*pr_rate)) AS total_sale_value"))
        //                 ->where('primary_sale_view.company_id',$company_id);
        //                 if(!empty($junior_data))
        //                 {
        //                     $totalPrimaryOrderData->whereIn('primary_sale_view.created_person_id',$junior_data);
        //                 }
        //                 if(!empty($location_3_filter))
        //                 {
        //                     $totalPrimaryOrderData->join('dealer','dealer.id','=','primary_sale_view.dealer_id')->whereIn('dealer.state_id',$location_3_filter);
        //                 }
        // $totalPrimaryOrder = $totalPrimaryOrderData->first();
        // }else{
        //     $totalPrimaryOrderData=DB::table('primary_sale_view')
        //     ->whereRaw("DATE_FORMAT(primary_sale_view.sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(primary_sale_view.sale_date,'%Y-%m-%d')<='$to_date'")
        //     ->select(DB::raw("round(sum(final_secondary_rate*final_secondary_qty),2) AS total_sale_value"))
        //     ->where('primary_sale_view.company_id',$company_id);
        //     if(!empty($junior_data))
        //     {
        //         $totalPrimaryOrderData->whereIn('primary_sale_view.created_person_id',$junior_data);
        //     }
        //     if(!empty($location_3_filter))
        //     {
        //         $totalPrimaryOrderData->join('dealer','dealer.id','=','primary_sale_view.dealer_id')->whereIn('dealer.state_id',$location_3_filter);
        //     }
        //     $totalPrimaryOrder = $totalPrimaryOrderData->first();
        // }
        // $totalPrimaryOrder = array();
        // if(empty($totalPrimaryOrder))
            $totalPrimaryOrder=0;
        // SALE END
        $roleWiseTeamData=Person::join('person_login','person_login.person_id','=','person.id','inner')
                    ->join('_role','_role.role_id','=','person.role_id','inner')
                    ->join('users','users.id','=','person.id','inner')
                    ->select('person.role_id','_role.rolename',DB::raw("count(person.id) as count"))
                    ->where('person_login.company_id',$company_id)
                    ->where('person.company_id',$company_id)
                    ->where('_role.company_id',$company_id)
                    ->where('person_status',1)
                    ->where('is_admin','!=',1)
                    ->where('rolename','!=','Super Admin')
                    ->groupBy('person.role_id')
                    ->orderBY('role_id','ASC');
                    if(!empty($junior_data))
                    {
                        $roleWiseTeamData->whereIn('person.id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $roleWiseTeamData->whereIn('person.state_id',$location_3_filter);
                    }
        $roleWiseTeam = $roleWiseTeamData->get();
       
        // $catalog1Sale=UserSalesOrder::join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id','inner')
        // ->join('catalog_view','catalog_view.product_id','=','user_sales_order_details.product_id','inner')
        // ->select('c1_id','c1_name',DB::raw("SUM(user_sales_order_details.rate*quantity) as sale"))
        // ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$mdate'")
        // ->groupBy('c1_id')->orderBY('c1_name','ASC')->get();
        if(empty($check)){            
        // $catalog1SaleData = SecondarySale::select('c1_color_code as color_code','c2_name as c1_name',DB::raw("SUM(rate*quantity) as sale"))
        //             ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        //             ->where('company_id',$company_id)
        //             ->groupBy('c2_id')
        //             ->orderBY('c2_name','ASC');
        //             if(!empty($junior_data))
        //             {
        //                 $catalog1SaleData->whereIn('user_id',$junior_data);
        //             }
        //             if(!empty($location_3_filter))
        //             {
        //                 $catalog1SaleData->whereIn('l3_id',$location_3_filter);
        //             }
        // $catalog1Sale = $catalog1SaleData->get();
            $catalog1Sale = array();
        }else{
            // $catalog1SaleData = SecondarySale::select('c1_color_code as color_code','c2_name as c1_name',DB::raw("SUM(final_secondary_rate*final_secondary_qty) as sale"))
            // ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
            // ->where('company_id',$company_id)
            // ->groupBy('c2_id')
            // ->orderBY('c2_name','ASC');
            // if(!empty($junior_data))
            // {
            //     $catalog1SaleData->whereIn('user_id',$junior_data);
            // }
            // if(!empty($location_3_filter))
            // {
            //     $catalog1SaleData->whereIn('l3_id',$location_3_filter);
            // }
            // $catalog1Sale = $catalog1SaleData->get();
            $catalog1Sale = array();
        }
        // dd($catalog1Sale);
       
         // for pie char
        
                    // dd($beat_query);
        //  ends here
        // attendance_detais starts here
        $attendance_details_data = DB::table('user_daily_attendance')
                                ->join('person','person.id','=','user_daily_attendance.user_id')
                                ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                                ->select('user_id','_working_status.name as work_status_name','mobile',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'work_status','work_date','user_daily_attendance.remarks','person.emp_code')
                                ->where('user_daily_attendance.company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$cdate'");
                                if(!empty($junior_data))
                                {
                                    $attendance_details_data->whereIn('user_daily_attendance.user_id',$junior_data);
                                }
                                if(!empty($location_3_filter))
                                {
                                    $attendance_details_data->whereIn('person.state_id',$location_3_filter);
                                }   
        $attendance_details= $attendance_details_data->get();
        // attendance_detais ends here
        // user_details starts here
        

        
            // dd($division_sale);
        // $catalog1challan=ChallanOrder::join('challan_order_details','challan_order_details.ch_id','=','challan_order.id','inner')
        // ->join('catalog_view','catalog_view.product_id','=','challan_order_details.product_id','inner')
        // ->select('c1_id','c1_name',DB::raw("SUM(challan_order_details.taxable_amt) as challan_value"))
        // ->groupBy('c1_id')->orderBY('c1_name','ASC')->get();
        $month=substr($mdate,5);
        $year=substr($mdate,0,4);
        // dd($year);
        $monthNum  = $month;
        $dateObj   = DateTime::createFromFormat('!m', $monthNum);
        $monthName = $dateObj->format('F');
        for($i = 1; $i <=  date('t'); $i++)
        {
            // add the date to the dates array
            $datesArr[] = $year . "-" . $month . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
            $test[] = $i;
            // $datesDisplayArr[] =  $monthName . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        // dd($test);
        $startTime = strtotime($from_date);
        $endTime = strtotime($to_date);
        for ($currentDate = $startTime; $currentDate <= $endTime;  $currentDate += (86400))
        {
            $Store = date('Y-m-d', $currentDate);
            $datesArr_new[] = $Store;
            $datesDisplayArr[] = $Store;
        }
        // dd($datesArr);
        if(empty($check)){
        $saleOrderValueData = DB::table($table_name)
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                        ->where($table_name.'.company_id',$company_id)
                        ->where('user_sales_order_details.company_id',$company_id)
                        ->groupBy($table_name.'.date');
                        if(!empty($junior_data))
                        {
                            $saleOrderValueData->whereIn($table_name.'.user_id',$junior_data);
                        }
                        if(!empty($location_3_filter))
                        {
                            $saleOrderValueData->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
                        }    
        $saleOrderValue = $saleOrderValueData->pluck(DB::raw("SUM(rate*quantity)"),$table_name.'.date as date');
        }else{
            $saleOrderValueData = DB::table($table_name)
            ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
            ->where($table_name.'.company_id',$company_id)
            ->where('user_sales_order_details.company_id',$company_id)
            ->groupBy($table_name.'.date');
            if(!empty($junior_data))
            {
                $saleOrderValueData->whereIn($table_name.'.user_id',$junior_data);
            }
            if(!empty($location_3_filter))
            {
                $saleOrderValueData->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
            }    
            $saleOrderValue = $saleOrderValueData->pluck(DB::raw("SUM(final_secondary_rate*final_secondary_qty)"),$table_name.'.date as date');   
        }
      
            // dd($dateVal);
            // $totalChallanValue[]=ChallanOrder::where(DB::raw("(DATE_FORMAT(ch_date,'%Y-%m-%d'))"), "=", ''.$dateVal.'')->sum('amount');
        $totalChallanValue[]=0;
            // $totalOrderValue1[] = 0;
            
        foreach ($datesArr_new as $key_new => $value_new)
        {
            $set_value_cus = !empty($saleOrderValue[$value_new])?$saleOrderValue[$value_new]:'0';
            $date_set = date('d-M-y',strtotime($value_new));
            $dataPointsSetBar[] =array("y" => round($set_value_cus), "label" => "$date_set");
            $out[$value_new]=!empty($saleOrderValue[$value_new])?$saleOrderValue[$value_new]:'0';
        }
       
        foreach ($datesArr_new as $key => $value)
        {
            if(!empty($out[$value]))
            {
                $totalOrderValue1[] = $out[$value];
            }
            else
            {
                $totalOrderValue1[] = 0;
            }
        }
        // dd($totalOrderValue1);

        $totalOrderValue  = array();
        $totalOrderValue = array_map('round',$totalOrderValue1);
        ################################################################################### inactive retailer starts here 
            // $from_date = date('Y-m-d');
            $last_15_days = date('Y-m-d', strtotime($from_date. ' - 15 days')); 
            $last_30_days = date('Y-m-d', strtotime($from_date. ' - 30 days')); 
            $last_45_days = date('Y-m-d', strtotime($from_date. ' - 45 days')); 
            // dd($last_15_days);
            // $query_data_total =DB::table('user_sales_order')
            //     ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
            //     ->where('user_sales_order.company_id',$company_id);
            // if(!empty($location_3_filter))
            // {
            //     $saleOrderValueData->whereIn('l3_id',$location_3_filter);
            // }    
            // $retailer_id_sale_total = $query_data_total->pluck('retailer_id');
            // // dd($retailer_id_sale_total);
            // // $not_visit_list_query_data_total = DB::table('retailer')
            // //                         ->join('location_view','location_view.l7_id','=','retailer.location_id')
            // //                         ->select(DB::raw("count(retailer.id) AS rid"))
            // //                         ->where('retailer.company_id',$company_id)
            // //                         ->whereNotIn('retailer.id',$retailer_id_sale_total)
            // //                         ->where('retailer_status',1);
            // //                         if(!empty($location_3_filter))
            // //                         {
            // //                             $not_visit_list_query_data_total->whereIn('l3_id',$location_3_filter);
            // //                         }    
                                    
            // // $not_visit_list_query_total = $not_visit_list_query_data_total->first();

            // $state_array_data = DB::table('location_3')->where('status',1);
            // if(!empty($location_3_filter))
            // {
            //     $state_array_data->whereIn('id',$location_3_filter);
            // }   
            // $state_array = $state_array_data->pluck('name','id');

            // ////////////////////////// for last 15 days ////////////////////////////////////////////////
            // $query_data_15 =DB::table('user_sales_order')
            //     ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
            //     ->where('user_sales_order.company_id',$company_id)
            //     ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') <= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') >= '$last_15_days'");
            // if(!empty($location_3_filter))
            // {
            //     $query_data_15->whereIn('l3_id',$location_3_filter);
            // }   
            // $retailer_id_sale_15 = $query_data_15->pluck('retailer_id');
            // // dd($retailer_id_sale_15);
           
            // $not_visit_list_query_data_15 = DB::table('retailer')
            //                         ->join('location_view','location_view.l7_id','=','retailer.location_id')
            //                         ->where('retailer.company_id',$company_id)
            //                         ->whereNotIn('retailer.id',$retailer_id_sale_15)
            //                         ->where('retailer_status',1)
            //                         ->groupBy('l3_id');
            //                         if(!empty($location_3_filter))
            //                         {
            //                             $not_visit_list_query_data_15->whereIn('l3_id',$location_3_filter);
            //                         }  
                                    
            //                         $not_visit_list_query_15 = $not_visit_list_query_data_15->pluck(DB::raw("count(retailer.id) AS rid"),'l3_id');
            //                         // dd($not_visit_list_query_15);
            // // //////////////////////////end of for last 15 days ////////////////////////////////////////////////


            //                          // ////////////////////////// for last 30 days ////////////////////////////////////////////////
            //  $query_data_30 =DB::table('user_sales_order')
            //                 ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
            //                 ->where('user_sales_order.company_id',$company_id)
            //                 ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') <= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') >= '$last_30_days'");
            //                 if(!empty($location_3_filter))
            //                 {
            //                     $query_data_30->whereIn('l3_id',$location_3_filter);
            //                 }  
            // $retailer_id_sale_30 = $query_data_30->pluck('retailer_id');
            // // dd($retailer_id_sale);
           
            // $not_visit_list_query_data_30 = DB::table('retailer')
            //                         ->join('location_view','location_view.l7_id','=','retailer.location_id')
            //                         ->where('retailer.company_id',$company_id)
            //                         ->whereNotIn('retailer.id',$retailer_id_sale_30)
            //                         ->where('retailer_status',1)
            //                         ->groupBy('l3_id');
            //                         if(!empty($location_3_filter))
            //                         {
            //                             $not_visit_list_query_data_30->whereIn('l3_id',$location_3_filter);
            //                         } 
                                    
            //                         $not_visit_list_query_30 = $not_visit_list_query_data_30->pluck(DB::raw("count(retailer.id) AS rid"),'l3_id');
            // // //////////////////////////end of for last 30 days ////////////////////////////////////////////////


            //                          // ////////////////////////// for last 45 days ////////////////////////////////////////////////
            //  $query_data_45 =DB::table('user_sales_order')
            //     ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
            //     ->where('user_sales_order.company_id',$company_id)
            //     ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') <= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') >= '$last_45_days'");
            // if(!empty($location_3_filter))
            // {
            //     $query_data_45->whereIn('l3_id',$location_3_filter);
            // } 
            // $retailer_id_sale_45 = $query_data_45->pluck('retailer_id');
            // // dd($retailer_id_sale);
           
            // $not_visit_list_query_data_45 = DB::table('retailer')
            //                         ->join('location_view','location_view.l7_id','=','retailer.location_id')
            //                         ->where('retailer.company_id',$company_id)
            //                         ->whereNotIn('retailer.id',$retailer_id_sale_45)
            //                         ->where('retailer_status',1)
            //                         ->groupBy('l3_id');
            //                         if(!empty($location_3_filter))
            //                         {
            //                             $not_visit_list_query_data_45->whereIn('l3_id',$location_3_filter);
            //                         }
                                    
            // $not_visit_list_query_45 = $not_visit_list_query_data_45->pluck(DB::raw("count(retailer.id) AS rid"),'l3_id');

            $state_data = Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        #############################################################################################################
        // $dashboard_arr array ends here
        //xotik particulars sumaary starts 
        $summary_part1_retailer_data = DB::table('retailer')
                        ->where('retailer_status',1)
                        ->where('retailer.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $summary_part1_retailer_data->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','retailer.location_id')
                                               ->whereIn('dealer_location_rate_list.user_id',$junior_data);
                        }
        $summary_part1_retailer = $summary_part1_retailer_data->distinct('retailer.id')->count('retailer.id');

        $summary_part1_dealer_data = DB::table('dealer')
                        ->where('dealer_status',1)
                        ->where('dealer.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $summary_part1_dealer_data->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                                               ->whereIn('dealer_location_rate_list.user_id',$junior_data);
                        }
        $summary_part1_dealer = $summary_part1_dealer_data->distinct('dealer.id')->count('dealer.id');


        $summary_part1_beat_data = DB::table('location_7')
                        ->where('status',1)
                        ->where('location_7.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $summary_part1_beat_data->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','location_7.id')
                                               ->whereIn('dealer_location_rate_list.user_id',$junior_data);
                        }
        $summary_part1_beat = $summary_part1_beat_data->distinct('location_7.id')->count('location_7.id');

        $summary_part1_csa_data = DB::table('csa')
                        ->where('active_status',1)
                        ->where('csa.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $summary_part1_csa_data->join('dealer','dealer.csa_id','=','csa.c_id')
                                                ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                                               ->whereIn('dealer_location_rate_list.user_id',$junior_data);
                        }
        $summary_part1_csa = $summary_part1_csa_data->distinct('csa.c_id')->count('csa.c_id');

        // retailer visted or not starts here 
        $sumaary_part2_retailer_data = DB::table($table_name)
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                                ->where('company_id',$company_id);
                                if(!empty($junior_data))
                                {
                                    $sumaary_part2_retailer_data->whereIn('user_id',$junior_data);
                                }
                                $sumaary_part2_retailer = 0;

        $sumaary_part2_retailer_id = DB::table($table_name)
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                                ->where('company_id',$company_id)
                                ->groupBy('retailer_id');
                                if(!empty($junior_data))
                                {
                                    $sumaary_part2_retailer_id->whereIn('user_id',$junior_data);
                                }
                                $retailer_id_sale = array();

        $retailer_count_data = DB::table('retailer')
                        ->whereNotIn('retailer.id',$retailer_id_sale)
                        ->where('retailer.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $retailer_count_data->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','retailer.location_id')->whereIn('user_id',$junior_data);
                        }
                
        $retailer_count = $retailer_count_data->distinct('retailer.id')->count('retailer.id');


        // retailer not visited or not ends here 

        $sumaary_part2_dealer_data = DB::table('user_primary_sales_order')
                                ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(sale_date,'%Y-%m-%d')<='$to_date'")
                                ->where('company_id',$company_id);
                                if(!empty($junior_data))
                                {
                                    $sumaary_part2_dealer_data->whereIn('created_person_id',$junior_data);
                                }
                                $sumaary_part2_dealer = $sumaary_part2_dealer_data->distinct('dealer_id')->count('dealer_id');

        $sumaary_part2_dealer_id = DB::table('user_primary_sales_order')
                                ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(sale_date,'%Y-%m-%d')<='$to_date'")
                                ->where('company_id',$company_id)
                                ->groupBy('dealer_id');
                                if(!empty($junior_data))
                                {
                                    $sumaary_part2_dealer_id->whereIn('created_person_id',$junior_data);
                                }
                                $dealer_id_sale = $sumaary_part2_dealer_id->pluck('dealer_id')->toArray();

        $dealer_count_data = DB::table('dealer')
                        ->whereNotIn('dealer.id',$dealer_id_sale)
                        ->where('dealer.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $dealer_count_data->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')->whereIn('user_id',$junior_data);
                        }
        $dealer_count = $dealer_count_data->distinct('dealer.id')->count('dealer.id');

        $totalMeetingData=DB::table('meeting_order_booking')
                ->whereRaw("DATE_FORMAT(meeting_order_booking.current_date_m,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(meeting_order_booking.current_date_m,'%Y-%m-%d')<='$to_date'")
                ->where('company_id',$company_id)
                ->select(DB::raw('count(DISTINCT order_id) as meetings'));
                if(!empty($junior_data))
                {
                    $totalMeetingData->whereIn('user_id',$junior_data);

                }
        $totalMeeting = $totalMeetingData->first();

        // dd($location_3_filter);

        $companyDetails = DB::table('company')
                        ->where('id',$company_id)
                        ->first();


        $companyDetails = DB::table('company')
                        ->where('id',$company_id)
                        ->first();

        if(empty($check)){
            $user_perfomance_data_data = DB::table($table_name)
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                                ->join('person','person.id','=',$table_name.'.user_id')
                                ->join('users','users.id','=','person.id')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('_role','_role.role_id','=','person.role_id')
                                ->select('rolename',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person_image','person.mobile',DB::raw("SUM(rate*quantity) as sale_value"))
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                                ->where($table_name.'.company_id',$company_id)
                                ->where('person.company_id',$company_id);
                                if(!empty($junior_data))
                                {
                                    $user_perfomance_data_data->whereIn($table_name.'.user_id',$junior_data);
                                }
            $user_perfomance_data = $user_perfomance_data_data->groupBy('user_id')
                                ->orderBy('sale_value','DESC')
                                ->take(5)->get();
        }
        else{
            $user_perfomance_data_data = DB::table($table_name)
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                                ->join('person','person.id','=',$table_name.'.user_id')
                                ->join('users','users.id','=','person.id')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('_role','_role.role_id','=','person.role_id')
                                ->select('rolename',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person_image','person.mobile',DB::raw("SUM(final_secondary_rate*final_secondary_qty) as sale_value"))
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                                ->where($table_name.'.company_id',$company_id)
                                ->where('person.company_id',$company_id);
                                if(!empty($junior_data))
                                {
                                    $user_perfomance_data_data->whereIn($table_name.'.user_id',$junior_data);
                                }
                                
            $user_perfomance_data = $user_perfomance_data_data->groupBy('user_id')
                                ->orderBy('sale_value','DESC')
                                ->take(5)->get();
        }



        //xotik particulars sumaary ends here 
        return view('home',
            [
                'menu' => $this->menu,
                'current_menu' => $current_menu,
                'company_id' => $company_id,
                'companyDetails' => $companyDetails,
                'totalMeeting' => $totalMeeting,
                'totalSalesTeam' => $totalSalesTeam,
                'totalDistributor'=>$totalDistributor,
                'totalOutlet'=>$totalOutlet,
                'roleWiseTeam'=>$roleWiseTeam,
                'catalog1Sale'=>$catalog1Sale,
                'user_perfomance_data'=>$user_perfomance_data,
                'datesArr'=>$datesDisplayArr,
                'totalOrderValue'=>$totalOrderValue,
                'totalChallanValue'=>$totalChallanValue,
                'totalAttd'=>$totalAttd,
                'totalOrder'=>$totalOrder,
                'totalPrimaryOrder'=>$totalPrimaryOrder,
                'mdate' =>$mdate,
                'from_date'=> $from_date,
                'to_date'=> $to_date,
                'location_5'=>$location_5,
                'totalDistributorSale'=>$totalDistributorSale,
                'totalOutletSale' =>$totalOutletSale,
                'totalBeatSale' => $totalBeatSale,
                'totalCall' => $totalCall,
                'productiveCall' => $productiveCall,
                'dataPointsSetBar'=>$dataPointsSetBar,
                'call_status_count'=> $call_status_count,
                'attendance_details' => $attendance_details,
                // 'user_details' => $user_details,
                // 'productve_call_details' => $productve_call_details,
                // 'beat_coverage_details' => $beat_coverage_details,
                'work_status_array' => $work_status_array,
                'role_wise_attendance'=> $role_wise_attendance,

                // 'not_visit_list_query_15' => $not_visit_list_query_15,
                // 'not_visit_list_query_30' => $not_visit_list_query_30,
                // 'not_visit_list_query_45' => $not_visit_list_query_45,
                'records'=> $state_data,
                'from_date'=> $from_date,
                // 'not_visit_list_query_total'=> $not_visit_list_query_total,
                'location3'=> $location3,

                'summary_part1_retailer'=> $summary_part1_retailer,
                'summary_part1_dealer'=> $summary_part1_dealer,
                'summary_part1_beat'=> $summary_part1_beat,
                'summary_part1_csa'=> $summary_part1_csa,
                'sumaary_part2_retailer'=> $sumaary_part2_retailer,
                'retailer_count'=> $retailer_count,
                'sumaary_part2_dealer'=> $sumaary_part2_dealer,
                'dealer_count'=> $dealer_count,
                'location_3_filter'=> $location_3_filter,
            ]);

    }

    public function home_test(Request $request)
    {   
        // dd($request);
        $check_dashboard = Session::get('dashboard_new_new');// this variable get the session values
        // dd($check_dashboard);
        // dd($check_dashboard[0]['is_set']);
        $current_menu='DASHBOARD';
        $user=Auth::user();
        $company_id = Auth::user()->company_id;
        $location_3_filter = $request->location3;
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

        

            if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
            {
                $junior_data = array();
            }
            else
            {
                Session::forget('juniordata');
                $user_data=JuniorData::getJuniorUser($user->id,$company_id);
                Session::push('juniordata', $user->id);
                $junior_data = Session::get('juniordata');
            }

            
            
             // seesion check end here     
            $cdate=date('Y-m-d'); // current date
            // dd($cdate);
            if(isset($request->date_range_picker))
            {
                // dd($request);
                $explodeDate = explode(" -", $request->date_range_picker);
                $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
                $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
                $mdate = date('Y-m',strtotime(trim($explodeDate[0])));
            }
            else
            {
                $mdate=date('Y-m');
               
                $from_date = date('Y-m-01');
                $to_date = date('Y-m-t');
            }
        

        // get table name for sales 

        $table_name = TableReturn::table_return($from_date,$to_date);
        // dd($table_name);

        // ends here 

        # for filter 
        $location3 = Location3::where('company_id',$company_id)->where('status',1)->pluck('name','id');
        // dd($to_date);
        $work_status_array = DB::table('_working_status')->where('company_id',$company_id)->where('status',1)->orderBy('sequence','ASC')->pluck('name','id');

        $role_wise_attendance_data = DB::table('user_daily_attendance')
                                ->join('person','person.id','=','user_daily_attendance.user_id')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('_role','_role.role_id','=','person.role_id')
                                ->where('user_daily_attendance.company_id',$company_id)
                                ->where('person_status',1)
                                ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$cdate'")
                                ->groupBy('person.role_id','work_status');
                                if(!empty($junior_data))
                                {
                                    $role_wise_attendance_data->whereIn('person.id',$junior_data);
                                }
                                if(!empty($location_3_filter))
                                {
                                    $role_wise_attendance_data->whereIn('person.state_id',$location_3_filter);
                                }
        $role_wise_attendance = $role_wise_attendance_data->pluck(DB::raw("COUNT(work_status)"),DB::raw("CONCAT(person.role_id,work_status) as id"));
        // USER DETAILS
        $totalSalesTeamData=Person::join('person_login','person_login.person_id','=','person.id')
                        ->join('users','users.id','=','person.id')
                        ->join('person_details','person_details.person_id','=','person.id')
                        // ->whereRaw("DATE_FORMAT(person_details.created_on,'%Y-%m-%d')<='$to_date'")
                        ->where('person_status',1)
                        ->where('is_admin','!=',1)
                        ->where('person.company_id',$company_id)
                        ->where('person_login.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $totalSalesTeamData->whereIn('person.id',$junior_data);
                        }
                        if(!empty($location_3_filter))
                        {
                            $totalSalesTeamData->whereIn('person.state_id',$location_3_filter);
                        }
        $totalSalesTeam = $totalSalesTeamData->count();


        $deactivateTeam = Person::join('person_login','person_login.person_id','=','person.id')
                        ->join('users','users.id','=','person.id')
                        ->join('person_details','person_details.person_id','=','person.id')
                        ->whereRaw("DATE_FORMAT(person_details.deleted_deactivated_on,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(person_details.deleted_deactivated_on,'%Y-%m-%d')<='$to_date'")
                        ->where('person_status','!=',1)
                        ->where('is_admin','!=',1)
                        ->where('person.company_id',$company_id)
                        ->where('person_login.company_id',$company_id)
                        // ->groupBy('person.id')
                        ->count();
                        

                        // dd($deactivateTeam);
        $totalSalesTeam = $totalSalesTeam+$deactivateTeam;



        $totalAttdData=DB::table('user_daily_attendance')
                ->where('user_daily_attendance.company_id',$company_id)
                ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$cdate'");
                if(!empty($junior_data))
                {
                    $totalAttdData->whereIn('user_id',$junior_data);
                }
                if(!empty($location_3_filter))
                {
                    $totalAttdData->join('person','person.id','=','user_daily_attendance.user_id')->whereIn('person.state_id',$location_3_filter);
                }
        $totalAttd = $totalAttdData->count();
                // dd($totalAttd);
        if(empty($totalAttd))
        {
            $totalAttd=0;
        }

        $dynamic_status = array("0","1");
        // END OF USER DETAILE
        // SS DETAILS
        // END OF SS DETAILS
        // DISTRIBUTOR DETAILS
        $totalDistributorData=Dealer::whereIn('dealer_status',$dynamic_status)
                        ->where('dealer.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $totalDistributorData->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id');
                            $totalDistributorData->whereIn('dealer_location_rate_list.user_id',$junior_data);

                        }
                        if(!empty($location_3_filter))
                        {
                            $totalDistributorData->whereIn('dealer.state_id',$location_3_filter);
                        }
                        $totalDistributor = $totalDistributorData->count();

        $totalDistributorSaleData=DB::table($table_name)
                            ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                            ->where('company_id',$company_id)
                            ->select(DB::raw('count(DISTINCT dealer_id) as dealersale'));
                            if(!empty($junior_data))
                            {
                                $totalDistributorSaleData->whereIn('user_id',$junior_data);

                            }
                            if(!empty($location_3_filter))
                            {
                                $totalDistributorSaleData->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
                            }
        $totalDistributorSale = $totalDistributorSaleData->first();
        // END OF DISTRIBUTOR DETAILS
        // OUTLET DETAILS
        $totalOutletData=Retailer::whereIn('retailer_status',$dynamic_status)
                    ->where('company_id',$company_id);
                    if(!empty($junior_data))
                    {
                        $totalOutletData->whereIn('created_by_person_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $totalOutletData->join('location_view','location_view.l7_id','=','retailer.location_id')->whereIn('l3_id',$location_3_filter);
                    }
        $totalOutlet =  $totalOutletData->count();
        // dd($totalOutlet);

        $totalOutletSaleData=DB::table($table_name)
                        ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                        // ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                        ->where('company_id',$company_id)
                        ->select(DB::raw('count(DISTINCT retailer_id) as outletsale'));
                        if(!empty($junior_data))
                        {
                            $totalOutletSaleData->whereIn('user_id',$junior_data);

                        }
                        if(!empty($location_3_filter))
                        {
                            $totalOutletSaleData->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
                        }
        $totalOutletSale = $totalOutletSaleData->first();
        // dd($totalOutletSale);
        // END OF OUTLET DETAILS
        // BEAT DETAILS
        $location_5Data = DB::table('location_7')
                    ->where('location_7.status',1)
                    ->where('location_7.company_id',$company_id);
                    if(!empty($junior_data))
                    {
                        $location_5Data->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','location_7.id');
                        $location_5Data->whereIn('dealer_location_rate_list.user_id',$junior_data);

                    }
                    if(!empty($location_3_filter))
                    {
                        $location_5Data->join('location_view','location_view.l7_id','=','location_7.id')->whereIn('l3_id',$location_3_filter);
                    }

        $location_5 = $location_5Data->count();

        $totalBeatSaleData=DB::table($table_name)
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                    ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                    ->where($table_name.'.company_id',$company_id)
                    ->select(DB::raw('count(DISTINCT location_id) as beatsale'));
                    if(!empty($junior_data))
                    {
                        $totalBeatSaleData->whereIn('user_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $totalBeatSaleData->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
                    }
        $totalBeatSale = $totalBeatSaleData->first();
        //  beat coverage starts here
        
        //  beat coverage ends here

       // END OF BEAT DETAILS
        // CALL
        $totalCallData=DB::table($table_name)
                    ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                    // ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                    ->where('company_id',$company_id)
                    ->select(DB::raw('count(DISTINCT retailer_id,date) as total_call'));
                    if(!empty($junior_data))
                    {
                        $totalCallData->whereIn($table_name.'.user_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $totalCallData->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
                    }
        $totalCall =  $totalCallData->first();

        $productiveCallData=DB::table($table_name)
                        ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                        // ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                        ->where('company_id',$company_id)
                        ->select(DB::raw('count(DISTINCT retailer_id,date) as productive_call'))->where('call_status',1);
                        if(!empty($junior_data))
                        {
                            $productiveCallData->whereIn($table_name.'.user_id',$junior_data);
                        }
                        if(!empty($location_3_filter))
                        {
                            $productiveCallData->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
                        }
        $productiveCall = $productiveCallData->first();

        // productive data details starts here
        
                                // dd($productve_call_details);
        $call_status_count = array();
        
        // dd($call_status_count);
        // END CALL
        // SALE START
        // if(empty($check)){
        // $totalOrderData=DB::table('user_sales_order')
        //             ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        //             ->select(DB::raw("sum(rate*quantity) AS total_sale_value"))
        //             ->where('user_sales_order.company_id',$company_id)
        //             ->where('user_sales_order_details.company_id',$company_id)
        //             ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'");
        //             if(!empty($junior_data))
        //             {
        //                 $totalOrderData->whereIn('user_sales_order.user_id',$junior_data);
        //             }
        //             if(!empty($location_3_filter))
        //             {
        //                 $totalOrderData->join('location_view','location_view.l7_id','=','user_sales_order.location_id')->whereIn('l3_id',$location_3_filter);
        //             }
        // $totalOrder = $totalOrderData->first();
        // }else{
        //     $totalOrderData=DB::table('user_sales_order')
        //     ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        //     ->select(DB::raw("round(sum(final_secondary_rate*final_secondary_qty),2) AS total_sale_value"))
        //     ->where('user_sales_order.company_id',$company_id)
        //     ->where('user_sales_order_details.company_id',$company_id)
        //     ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'");
        //     if(!empty($junior_data))
        //     {
        //         $totalOrderData->whereIn('user_sales_order.user_id',$junior_data);
        //     }
        //     if(!empty($location_3_filter))
        //     {
        //         $totalOrderData->join('location_view','location_view.l7_id','=','user_sales_order.location_id')->whereIn('l3_id',$location_3_filter);
        //     }
        //     $totalOrder = $totalOrderData->first();
        // }
        if(empty($totalOrder))
            $totalOrder=0;
        if(empty($check)){
        $totalPrimaryOrderData=DB::table('primary_sale_view')
                        ->whereRaw("DATE_FORMAT(primary_sale_view.sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(primary_sale_view.sale_date,'%Y-%m-%d')<='$to_date'")
                        ->select(DB::raw("sum((rate*pcs)+(cases*pr_rate)) AS total_sale_value"))
                        ->where('primary_sale_view.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $totalPrimaryOrderData->whereIn('primary_sale_view.created_person_id',$junior_data);
                        }
                        if(!empty($location_3_filter))
                        {
                            $totalPrimaryOrderData->join('dealer','dealer.id','=','primary_sale_view.dealer_id')->whereIn('dealer.state_id',$location_3_filter);
                        }
        $totalPrimaryOrder = $totalPrimaryOrderData->first();
        }else{
            $totalPrimaryOrderData=DB::table('primary_sale_view')
            ->whereRaw("DATE_FORMAT(primary_sale_view.sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(primary_sale_view.sale_date,'%Y-%m-%d')<='$to_date'")
            ->select(DB::raw("round(sum(final_secondary_rate*final_secondary_qty),2) AS total_sale_value"))
            ->where('primary_sale_view.company_id',$company_id);
            if(!empty($junior_data))
            {
                $totalPrimaryOrderData->whereIn('primary_sale_view.created_person_id',$junior_data);
            }
            if(!empty($location_3_filter))
            {
                $totalPrimaryOrderData->join('dealer','dealer.id','=','primary_sale_view.dealer_id')->whereIn('dealer.state_id',$location_3_filter);
            }
            $totalPrimaryOrder = $totalPrimaryOrderData->first();
        }
        if(empty($totalPrimaryOrder))
            $totalPrimaryOrder=0;
        // SALE END
        $roleWiseTeamData=Person::join('person_login','person_login.person_id','=','person.id','inner')
                    ->join('_role','_role.role_id','=','person.role_id','inner')
                    ->join('users','users.id','=','person.id','inner')
                    ->select('person.role_id','_role.rolename',DB::raw("count(person.id) as count"))
                    ->where('person_login.company_id',$company_id)
                    ->where('person.company_id',$company_id)
                    ->where('_role.company_id',$company_id)
                    ->where('person_status',1)
                    ->where('is_admin','!=',1)
                    ->where('rolename','!=','Super Admin')
                    ->groupBy('person.role_id')
                    ->orderBY('role_id','ASC');
                    if(!empty($junior_data))
                    {
                        $roleWiseTeamData->whereIn('person.id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $roleWiseTeamData->whereIn('person.state_id',$location_3_filter);
                    }
        $roleWiseTeam = $roleWiseTeamData->get();
       
        // $catalog1Sale=UserSalesOrder::join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id','inner')
        // ->join('catalog_view','catalog_view.product_id','=','user_sales_order_details.product_id','inner')
        // ->select('c1_id','c1_name',DB::raw("SUM(user_sales_order_details.rate*quantity) as sale"))
        // ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$mdate'")
        // ->groupBy('c1_id')->orderBY('c1_name','ASC')->get();
        if(empty($check)){            
        // $catalog1SaleData = SecondarySale::select('c1_color_code as color_code','c2_name as c1_name',DB::raw("SUM(rate*quantity) as sale"))
        //             ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        //             ->where('company_id',$company_id)
        //             ->groupBy('c2_id')
        //             ->orderBY('c2_name','ASC');
        //             if(!empty($junior_data))
        //             {
        //                 $catalog1SaleData->whereIn('user_id',$junior_data);
        //             }
        //             if(!empty($location_3_filter))
        //             {
        //                 $catalog1SaleData->whereIn('l3_id',$location_3_filter);
        //             }
        // $catalog1Sale = $catalog1SaleData->get();
            $catalog1Sale = array();
        }else{
            // $catalog1SaleData = SecondarySale::select('c1_color_code as color_code','c2_name as c1_name',DB::raw("SUM(final_secondary_rate*final_secondary_qty) as sale"))
            // ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
            // ->where('company_id',$company_id)
            // ->groupBy('c2_id')
            // ->orderBY('c2_name','ASC');
            // if(!empty($junior_data))
            // {
            //     $catalog1SaleData->whereIn('user_id',$junior_data);
            // }
            // if(!empty($location_3_filter))
            // {
            //     $catalog1SaleData->whereIn('l3_id',$location_3_filter);
            // }
            // $catalog1Sale = $catalog1SaleData->get();
            $catalog1Sale = array();
        }
        // dd($catalog1Sale);
       
         // for pie char
        
                    // dd($beat_query);
        //  ends here
        // attendance_detais starts here
        $attendance_details_data = DB::table('user_daily_attendance')
                                ->join('person','person.id','=','user_daily_attendance.user_id')
                                ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                                ->select('user_id','_working_status.name as work_status_name','mobile',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'work_status','work_date')
                                ->where('user_daily_attendance.company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$cdate'");
                                if(!empty($junior_data))
                                {
                                    $attendance_details_data->whereIn('user_daily_attendance.user_id',$junior_data);
                                }
                                if(!empty($location_3_filter))
                                {
                                    $attendance_details_data->whereIn('person.state_id',$location_3_filter);
                                }   
        $attendance_details= $attendance_details_data->get();
        // attendance_detais ends here
        // user_details starts here
        

        
            // dd($division_sale);
        // $catalog1challan=ChallanOrder::join('challan_order_details','challan_order_details.ch_id','=','challan_order.id','inner')
        // ->join('catalog_view','catalog_view.product_id','=','challan_order_details.product_id','inner')
        // ->select('c1_id','c1_name',DB::raw("SUM(challan_order_details.taxable_amt) as challan_value"))
        // ->groupBy('c1_id')->orderBY('c1_name','ASC')->get();
        $month=substr($mdate,5);
        $year=substr($mdate,0,4);
        // dd($year);
        $monthNum  = $month;
        $dateObj   = DateTime::createFromFormat('!m', $monthNum);
        $monthName = $dateObj->format('F');
        for($i = 1; $i <=  date('t'); $i++)
        {
            // add the date to the dates array
            $datesArr[] = $year . "-" . $month . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
            $test[] = $i;
            // $datesDisplayArr[] =  $monthName . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        // dd($test);
        $startTime = strtotime($from_date);
        $endTime = strtotime($to_date);
        for ($currentDate = $startTime; $currentDate <= $endTime;  $currentDate += (86400))
        {
            $Store = date('Y-m-d', $currentDate);
            $datesArr_new[] = $Store;
            $datesDisplayArr[] = $Store;
        }
        // dd($datesArr);
        if(empty($check)){
        $saleOrderValueData = DB::table($table_name)
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                        ->where($table_name.'.company_id',$company_id)
                        ->where('user_sales_order_details.company_id',$company_id)
                        ->groupBy($table_name.'.date');
                        if(!empty($junior_data))
                        {
                            $saleOrderValueData->whereIn($table_name.'.user_id',$junior_data);
                        }
                        if(!empty($location_3_filter))
                        {
                            $saleOrderValueData->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
                        }    
        $saleOrderValue = $saleOrderValueData->pluck(DB::raw("SUM(rate*quantity)"),$table_name.'.date as date');
        }else{
            $saleOrderValueData = DB::table($table_name)
            ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
            ->where($table_name.'.company_id',$company_id)
            ->where('user_sales_order_details.company_id',$company_id)
            ->groupBy($table_name.'.date');
            if(!empty($junior_data))
            {
                $saleOrderValueData->whereIn($table_name.'.user_id',$junior_data);
            }
            if(!empty($location_3_filter))
            {
                $saleOrderValueData->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
            }    
            $saleOrderValue = $saleOrderValueData->pluck(DB::raw("SUM(final_secondary_rate*final_secondary_qty)"),$table_name.'.date as date');   
        }
      
            // dd($dateVal);
            // $totalChallanValue[]=ChallanOrder::where(DB::raw("(DATE_FORMAT(ch_date,'%Y-%m-%d'))"), "=", ''.$dateVal.'')->sum('amount');
        $totalChallanValue[]=0;
            // $totalOrderValue1[] = 0;
            
        foreach ($datesArr_new as $key_new => $value_new)
        {
       
            $set_value_cus = !empty($saleOrderValue[$value_new])?$saleOrderValue[$value_new]:'0';
            $date_set = date('d-M-y',strtotime($value_new));
            $dataPointsSetBar[] =array("y" => round($set_value_cus), "label" => "$date_set");
            $out[$value_new]=!empty($saleOrderValue[$value_new])?$saleOrderValue[$value_new]:'0';
        }
       
        foreach ($datesArr_new as $key => $value)
        {
            if(!empty($out[$value]))
            {
                $totalOrderValue1[] = $out[$value];
            }
            else
            {
                $totalOrderValue1[] = 0;
            }
        }
        // dd($totalOrderValue1);

        $totalOrderValue  = array();
        $totalOrderValue = array_map('round',$totalOrderValue1);
        ################################################################################### inactive retailer starts here 
            // $from_date = date('Y-m-d');
            $last_15_days = date('Y-m-d', strtotime($from_date. ' - 15 days')); 
            $last_30_days = date('Y-m-d', strtotime($from_date. ' - 30 days')); 
            $last_45_days = date('Y-m-d', strtotime($from_date. ' - 45 days')); 
            // dd($last_15_days);
            // $query_data_total =DB::table('user_sales_order')
            //     ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
            //     ->where('user_sales_order.company_id',$company_id);
            // if(!empty($location_3_filter))
            // {
            //     $saleOrderValueData->whereIn('l3_id',$location_3_filter);
            // }    
            // $retailer_id_sale_total = $query_data_total->pluck('retailer_id');
            // // dd($retailer_id_sale_total);
            // // $not_visit_list_query_data_total = DB::table('retailer')
            // //                         ->join('location_view','location_view.l7_id','=','retailer.location_id')
            // //                         ->select(DB::raw("count(retailer.id) AS rid"))
            // //                         ->where('retailer.company_id',$company_id)
            // //                         ->whereNotIn('retailer.id',$retailer_id_sale_total)
            // //                         ->where('retailer_status',1);
            // //                         if(!empty($location_3_filter))
            // //                         {
            // //                             $not_visit_list_query_data_total->whereIn('l3_id',$location_3_filter);
            // //                         }    
                                    
            // // $not_visit_list_query_total = $not_visit_list_query_data_total->first();

            // $state_array_data = DB::table('location_3')->where('status',1);
            // if(!empty($location_3_filter))
            // {
            //     $state_array_data->whereIn('id',$location_3_filter);
            // }   
            // $state_array = $state_array_data->pluck('name','id');

            // ////////////////////////// for last 15 days ////////////////////////////////////////////////
            // $query_data_15 =DB::table('user_sales_order')
            //     ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
            //     ->where('user_sales_order.company_id',$company_id)
            //     ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') <= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') >= '$last_15_days'");
            // if(!empty($location_3_filter))
            // {
            //     $query_data_15->whereIn('l3_id',$location_3_filter);
            // }   
            // $retailer_id_sale_15 = $query_data_15->pluck('retailer_id');
            // // dd($retailer_id_sale_15);
           
            // $not_visit_list_query_data_15 = DB::table('retailer')
            //                         ->join('location_view','location_view.l7_id','=','retailer.location_id')
            //                         ->where('retailer.company_id',$company_id)
            //                         ->whereNotIn('retailer.id',$retailer_id_sale_15)
            //                         ->where('retailer_status',1)
            //                         ->groupBy('l3_id');
            //                         if(!empty($location_3_filter))
            //                         {
            //                             $not_visit_list_query_data_15->whereIn('l3_id',$location_3_filter);
            //                         }  
                                    
            //                         $not_visit_list_query_15 = $not_visit_list_query_data_15->pluck(DB::raw("count(retailer.id) AS rid"),'l3_id');
            //                         // dd($not_visit_list_query_15);
            // // //////////////////////////end of for last 15 days ////////////////////////////////////////////////


            //                          // ////////////////////////// for last 30 days ////////////////////////////////////////////////
            //  $query_data_30 =DB::table('user_sales_order')
            //                 ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
            //                 ->where('user_sales_order.company_id',$company_id)
            //                 ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') <= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') >= '$last_30_days'");
            //                 if(!empty($location_3_filter))
            //                 {
            //                     $query_data_30->whereIn('l3_id',$location_3_filter);
            //                 }  
            // $retailer_id_sale_30 = $query_data_30->pluck('retailer_id');
            // // dd($retailer_id_sale);
           
            // $not_visit_list_query_data_30 = DB::table('retailer')
            //                         ->join('location_view','location_view.l7_id','=','retailer.location_id')
            //                         ->where('retailer.company_id',$company_id)
            //                         ->whereNotIn('retailer.id',$retailer_id_sale_30)
            //                         ->where('retailer_status',1)
            //                         ->groupBy('l3_id');
            //                         if(!empty($location_3_filter))
            //                         {
            //                             $not_visit_list_query_data_30->whereIn('l3_id',$location_3_filter);
            //                         } 
                                    
            //                         $not_visit_list_query_30 = $not_visit_list_query_data_30->pluck(DB::raw("count(retailer.id) AS rid"),'l3_id');
            // // //////////////////////////end of for last 30 days ////////////////////////////////////////////////


            //                          // ////////////////////////// for last 45 days ////////////////////////////////////////////////
            //  $query_data_45 =DB::table('user_sales_order')
            //     ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
            //     ->where('user_sales_order.company_id',$company_id)
            //     ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') <= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') >= '$last_45_days'");
            // if(!empty($location_3_filter))
            // {
            //     $query_data_45->whereIn('l3_id',$location_3_filter);
            // } 
            // $retailer_id_sale_45 = $query_data_45->pluck('retailer_id');
            // // dd($retailer_id_sale);
           
            // $not_visit_list_query_data_45 = DB::table('retailer')
            //                         ->join('location_view','location_view.l7_id','=','retailer.location_id')
            //                         ->where('retailer.company_id',$company_id)
            //                         ->whereNotIn('retailer.id',$retailer_id_sale_45)
            //                         ->where('retailer_status',1)
            //                         ->groupBy('l3_id');
            //                         if(!empty($location_3_filter))
            //                         {
            //                             $not_visit_list_query_data_45->whereIn('l3_id',$location_3_filter);
            //                         }
                                    
            // $not_visit_list_query_45 = $not_visit_list_query_data_45->pluck(DB::raw("count(retailer.id) AS rid"),'l3_id');

            $state_data = Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        #############################################################################################################
        // $dashboard_arr array ends here
        //xotik particulars sumaary starts 
        $summary_part1_retailer_data = DB::table('retailer')
                        ->where('retailer_status',1)
                        ->where('retailer.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $summary_part1_retailer_data->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','retailer.location_id')
                                               ->whereIn('dealer_location_rate_list.user_id',$junior_data);
                        }
        $summary_part1_retailer = $summary_part1_retailer_data->distinct('retailer.id')->count('retailer.id');

        $summary_part1_dealer_data = DB::table('dealer')
                        ->where('dealer_status',1)
                        ->where('dealer.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $summary_part1_dealer_data->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                                               ->whereIn('dealer_location_rate_list.user_id',$junior_data);
                        }
        $summary_part1_dealer = $summary_part1_dealer_data->distinct('dealer.id')->count('dealer.id');


        $summary_part1_beat_data = DB::table('location_7')
                        ->where('status',1)
                        ->where('location_7.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $summary_part1_beat_data->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','location_7.id')
                                               ->whereIn('dealer_location_rate_list.user_id',$junior_data);
                        }
        $summary_part1_beat = $summary_part1_beat_data->distinct('location_7.id')->count('location_7.id');

        $summary_part1_csa_data = DB::table('csa')
                        ->where('active_status',1)
                        ->where('csa.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $summary_part1_csa_data->join('dealer','dealer.csa_id','=','csa.c_id')
                                                ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                                               ->whereIn('dealer_location_rate_list.user_id',$junior_data);
                        }
        $summary_part1_csa = $summary_part1_csa_data->distinct('csa.c_id')->count('csa.c_id');

        // retailer visted or not starts here 
        $sumaary_part2_retailer_data = DB::table($table_name)
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                                ->where('company_id',$company_id);
                                if(!empty($junior_data))
                                {
                                    $sumaary_part2_retailer_data->whereIn('user_id',$junior_data);
                                }
                                $sumaary_part2_retailer = 0;

        $sumaary_part2_retailer_id = DB::table($table_name)
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                                ->where('company_id',$company_id)
                                ->groupBy('retailer_id');
                                if(!empty($junior_data))
                                {
                                    $sumaary_part2_retailer_id->whereIn('user_id',$junior_data);
                                }
                                $retailer_id_sale = array();

        $retailer_count_data = DB::table('retailer')
                        ->whereNotIn('retailer.id',$retailer_id_sale)
                        ->where('retailer.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $retailer_count_data->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','retailer.location_id')->whereIn('user_id',$junior_data);
                        }
                
        $retailer_count = $retailer_count_data->distinct('retailer.id')->count('retailer.id');


        // retailer not visited or not ends here 

        $sumaary_part2_dealer_data = DB::table('user_primary_sales_order')
                                ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(sale_date,'%Y-%m-%d')<='$to_date'")
                                ->where('company_id',$company_id);
                                if(!empty($junior_data))
                                {
                                    $sumaary_part2_dealer_data->whereIn('created_person_id',$junior_data);
                                }
                                $sumaary_part2_dealer = $sumaary_part2_dealer_data->distinct('dealer_id')->count('dealer_id');

        $sumaary_part2_dealer_id = DB::table('user_primary_sales_order')
                                ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(sale_date,'%Y-%m-%d')<='$to_date'")
                                ->where('company_id',$company_id)
                                ->groupBy('dealer_id');
                                if(!empty($junior_data))
                                {
                                    $sumaary_part2_dealer_id->whereIn('created_person_id',$junior_data);
                                }
                                $dealer_id_sale = $sumaary_part2_dealer_id->pluck('dealer_id')->toArray();

        $dealer_count_data = DB::table('dealer')
                        ->whereNotIn('dealer.id',$dealer_id_sale)
                        ->where('dealer.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $dealer_count_data->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')->whereIn('user_id',$junior_data);
                        }
        $dealer_count = $dealer_count_data->distinct('dealer.id')->count('dealer.id');

        $totalMeetingData=DB::table('meeting_order_booking')
                ->whereRaw("DATE_FORMAT(meeting_order_booking.current_date_m,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(meeting_order_booking.current_date_m,'%Y-%m-%d')<='$to_date'")
                ->where('company_id',$company_id)
                ->select(DB::raw('count(DISTINCT order_id) as meetings'));
                if(!empty($junior_data))
                {
                    $totalMeetingData->whereIn('user_id',$junior_data);

                }
        $totalMeeting = $totalMeetingData->first();

        // dd($location_3_filter);

        $companyDetails = DB::table('company')
                        ->where('id',$company_id)
                        ->first();

        if(empty($check)){
            $user_perfomance_data = DB::table($table_name)
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                                ->join('person','person.id','=',$table_name.'.user_id')
                                ->join('users','users.id','=','person.id')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('_role','_role.role_id','=','person.role_id')
                                ->select('rolename',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person_image','person.mobile',DB::raw("SUM(rate*quantity) as sale_value"))
                                ->groupBy('user_id')
                                ->orderBy('sale_value','DESC')
                                ->take(5)->get();
        }
        else{
            $user_perfomance_data = DB::table($table_name)
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                                ->join('person','person.id','=',$table_name.'.user_id')
                                ->join('users','users.id','=','person.id')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('_role','_role.role_id','=','person.role_id')
                                ->select('rolename',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person_image','person.mobile',DB::raw("SUM(final_secondary_rate*final_secondary_qty) as sale_value"))
                                ->groupBy('user_id')
                                ->orderBy('sale_value','DESC')
                                ->take(5)->get();
        }




        
        // dd($dataPoints);
        //xotik particulars sumaary ends here 
        return view('testHome',
            [
                'menu' => $this->menu,
                'current_menu' => $current_menu,
                'company_id' => $company_id,
                'companyDetails' => $companyDetails,
                'totalMeeting' => $totalMeeting,
                
                'totalSalesTeam' => $totalSalesTeam,
                'totalDistributor'=>$totalDistributor,
                'totalOutlet'=>$totalOutlet,
                'roleWiseTeam'=>$roleWiseTeam,
                'catalog1Sale'=>$catalog1Sale,
                'user_perfomance_data'=>$user_perfomance_data,
                'datesArr'=>$datesDisplayArr,
                'totalOrderValue'=>$totalOrderValue,
                'totalChallanValue'=>$totalChallanValue,
                'totalAttd'=>$totalAttd,
                'totalOrder'=>$totalOrder,
                'totalPrimaryOrder'=>$totalPrimaryOrder,
                'mdate' =>$mdate,
                'from_date'=> $from_date,
                'to_date'=> $to_date,
                'location_5'=>$location_5,
                'totalDistributorSale'=>$totalDistributorSale,
                'totalOutletSale' =>$totalOutletSale,
                'totalBeatSale' => $totalBeatSale,
                'totalCall' => $totalCall,
                'productiveCall' => $productiveCall,
                // 'beat_query'=>$beat_query,
                'call_status_count'=> $call_status_count,
                'attendance_details' => $attendance_details,
                'dataPointsSetBar' => $dataPointsSetBar,
                // 'productve_call_details' => $productve_call_details,
                // 'beat_coverage_details' => $beat_coverage_details,
                'work_status_array' => $work_status_array,
                'role_wise_attendance'=> $role_wise_attendance,

                // 'not_visit_list_query_15' => $not_visit_list_query_15,
                // 'not_visit_list_query_30' => $not_visit_list_query_30,
                // 'not_visit_list_query_45' => $not_visit_list_query_45,
                'records'=> $state_data,
                'from_date'=> $from_date,
                // 'not_visit_list_query_total'=> $not_visit_list_query_total,
                'location3'=> $location3,

                'summary_part1_retailer'=> $summary_part1_retailer,
                'summary_part1_dealer'=> $summary_part1_dealer,
                'summary_part1_beat'=> $summary_part1_beat,
                'summary_part1_csa'=> $summary_part1_csa,
                'sumaary_part2_retailer'=> $sumaary_part2_retailer,
                'retailer_count'=> $retailer_count,
                'sumaary_part2_dealer'=> $sumaary_part2_dealer,
                'dealer_count'=> $dealer_count,
                'location_3_filter'=> $location_3_filter,
            ]);

    }
    public function get_month_wise_data_user_wise(Request $request){

        $company_id = Auth::user()->company_id;
        // $from_date = $request->from_date;
        // $to_date = $request->to_date;
        $label = date('Y-m-d',strtotime($request->label));
        $table_name = TableReturn::table_return($label,$label);
        $user = Auth::user();
        if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id,$company_id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }


        if(empty($check)){

            $query_data = DB::table($table_name)
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                    ->join('person','person.id','=',$table_name.'.user_id')
                    ->join('users','users.id','=','person.id')
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->select('rolename',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person_image','person.mobile',DB::raw('SUM(rate*quantity+user_sales_order_details.case_rate*user_sales_order_details.case_qty) as sale_value'))
                    ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$label' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$label'")
                    ->where($table_name.'.company_id',$company_id)
                    ->where('user_sales_order_details.company_id',$company_id)
                    ->groupBy('user_id')->orderBy('sale_value','ASC');
                    if(!empty($junior_data)){
                        $query_data->whereIn('user_id',$junior_data);
                    }
        }
        else{

             $query_data = DB::table($table_name)
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                    ->join('person','person.id','=',$table_name.'.user_id')
                    ->join('users','users.id','=','person.id')
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->select('rolename',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person_image','person.mobile',DB::raw("SUM(final_secondary_rate*final_secondary_qty) as sale_value"))
                    ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$label' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$label'")
                    ->where($table_name.'.company_id',$company_id)
                    ->where('user_sales_order_details.company_id',$company_id)
                    ->groupBy('user_id')->orderBy('sale_value','ASC');
                    if(!empty($junior_data)){
                        $query_data->whereIn('user_id',$junior_data);
                    }
           
        }
       $grapData= $query_data->get();


       

        if(!empty($grapData))
        {
            // dd($not_visit_list_query_15);
            $data['grapData'] = $grapData;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['grapData'] = [];
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    public function get_year_wise_data(Request $request)
    {
        $company_id = Auth::user()->company_id;

        $user = Auth::user();
        if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id,$company_id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }

        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
        $cur_year = date('Y');
        if(empty($check)){

            $query_data = DB::table('user_sales_order')
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                    ->select(DB::raw('SUM(rate*quantity+user_sales_order_details.case_rate*user_sales_order_details.case_qty) as count'),
                    DB::raw('DATE_FORMAT(date, "%Y-%m") as months'))
                    ->whereRaw('DATE_FORMAT(date, "%Y")='.$cur_year)
                    ->where('user_sales_order.company_id',$company_id)
                    ->where('user_sales_order_details.company_id',$company_id)
                    // ->whereRaw("DATE_FORMAT(complaint.created_at,'%Y') = '$year'")
                    ->groupBy('months')->orderBy('months','ASC');
                    if(!empty($junior_data)){
                        $query_data->whereIn('user_id',$junior_data);
                    }
        }
        else{
            $query_data = DB::table('user_sales_order')
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                    ->select(DB::raw('SUM(final_secondary_rate*final_secondary_qty) as count'),
                    DB::raw('DATE_FORMAT(date, "%Y-%m") as months'))
                    ->whereRaw('DATE_FORMAT(date, "%Y")='.$cur_year)
                    ->where('user_sales_order.company_id',$company_id)
                    ->where('user_sales_order_details.company_id',$company_id)
                    // ->whereRaw("DATE_FORMAT(complaint.created_at,'%Y') = '$year'")
                    ->groupBy('months')->orderBy('months','ASC');
                    if(!empty($junior_data)){
                        $query_data->whereIn('user_id',$junior_data);
                    }
        }
       $grapData= $query_data->get();


        $dataPoints=array();
        $totalCount=array();
        foreach($grapData as $graphValue)
        {
            $dataPoints[] =array("y" => round($graphValue->count,2), "label" => "$graphValue->months");
            $totalCount[]=round($graphValue->count,2);
        }

        if(!empty($totalCount))
        {
            // dd($not_visit_list_query_15);
            $data['totalCount'] = $totalCount;
            $data['dataPoints'] = $dataPoints;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['totalCount'] = [];
            $data['dataPoints'] = [];
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    public function get_year_wise_data_product(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
        $cur_year = date('Y');

        $from_date = date('Y-m').'-01';
        $to_date = date('Y-m-t');
        $user = Auth::user();
        if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id,$company_id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }


        // $from_date = !empty($request->from_date)?$request->from_date:$cur_from_date;
        // $to_date = !empty($request->to_date)?$request->to_date:$cur_to_date;
        
        $table_name = TableReturn::table_return($from_date,$to_date);

        if(empty($check)){

            $query_data = DB::table($table_name)
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                    ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                    ->select(DB::raw('SUM(rate*quantity+user_sales_order_details.case_rate*user_sales_order_details.case_qty) as count'),'catalog_2.name as months','catalog_2.id as id')
                    ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                    ->where($table_name.'.company_id',$company_id)
                    ->where('catalog_product.company_id',$company_id)
                    ->where('catalog_2.company_id',$company_id)
                    ->where('user_sales_order_details.company_id',$company_id)
                    // ->whereRaw("DATE_FORMAT(complaint.created_at,'%Y') = '$year'")
                    ->groupBy('months')->orderBy('months','ASC');
                    if(!empty($junior_data)){
                        $query_data->whereIn('user_id',$junior_data);
                    }
        }
        else{
            $query_data = DB::table($table_name)
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                    ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                    ->select(DB::raw('SUM(final_secondary_rate*final_secondary_qty) as count'),'catalog_2.name as months','catalog_2.id as id')
                    ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                    ->where($table_name.'.company_id',$company_id)
                    ->where('catalog_product.company_id',$company_id)
                    ->where('catalog_2.company_id',$company_id)
                    ->where('user_sales_order_details.company_id',$company_id)
                    // ->whereRaw("DATE_FORMAT(complaint.created_at,'%Y') = '$year'")
                    ->groupBy('months')->orderBy('months','ASC');
                    if(!empty($junior_data)){
                        $query_data->whereIn('user_id',$junior_data);
                    }
        }
       $grapData= $query_data->get();


        $dataPoints=array();
        $totalCount=array();
        foreach($grapData as $graphValue)
        {
            $dataPoints[] =array("y" => round($graphValue->count,2), "label" => "$graphValue->months",'symbol'=>"$graphValue->id");
            $totalCount[]=round($graphValue->count,2);
        }
        // dd($dataPoints);

        if(!empty($totalCount))
        {
            // dd($not_visit_list_query_15);
            $data['totalCount'] = $totalCount;
            $data['dataPoints'] = $dataPoints;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['totalCount'] = [];
            $data['dataPoints'] = [];
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function get_year_wise_data_product_cat_wise(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
        $cur_year = date('Y');

        $from_date = date('Y-m').'-01';
        $to_date = date('Y-m-t');
        $cat_id = $request->cat_id;
        $user = Auth::user();
        if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id,$company_id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        // $from_date = !empty($request->from_date)?$request->from_date:$cur_from_date;
        // $to_date = !empty($request->to_date)?$request->to_date:$cur_to_date;
        
        $table_name = TableReturn::table_return($from_date,$to_date);

        if(empty($check)){

            $query_data = DB::table($table_name)
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                    ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                    ->select(DB::raw('SUM(rate*quantity+user_sales_order_details.case_rate*user_sales_order_details.case_qty) as count'),'catalog_product.name as months','catalog_product.id as id')
                    ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                    ->where($table_name.'.company_id',$company_id)
                    ->where('catalog_product.company_id',$company_id)
                    ->where('catalog_2.company_id',$company_id)
                    ->where('catalog_2.id',$cat_id)
                    ->where('user_sales_order_details.company_id',$company_id)
                    // ->whereRaw("DATE_FORMAT(complaint.created_at,'%Y') = '$year'")
                    ->groupBy('months')->orderBy('months','ASC');
                    if(!empty($junior_data)){
                        $query_data->whereIn('user_id',$junior_data);
                    }
        }
        else{
            $query_data = DB::table($table_name)
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                    ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                    ->select(DB::raw('SUM(final_secondary_rate*final_secondary_qty) as count'),'catalog_product.name as months','catalog_product.id as id')
                    ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                    ->where($table_name.'.company_id',$company_id)
                    ->where('catalog_product.company_id',$company_id)
                    ->where('catalog_2.company_id',$company_id)
                    ->where('user_sales_order_details.company_id',$company_id)
                    // ->whereRaw("DATE_FORMAT(complaint.created_at,'%Y') = '$year'")
                    ->groupBy('months')->orderBy('months','ASC');
                    if(!empty($junior_data)){
                        $query_data->whereIn('user_id',$junior_data);
                    }
        }
       $grapData= $query_data->get();


        $dataPoints=array();
        $totalCount=array();
        foreach($grapData as $graphValue)
        {
            $dataPoints[] =array("y" => round($graphValue->count,2), "label" => "$graphValue->months",'symbol'=>"$graphValue->id");
            $totalCount[]=round($graphValue->count,2);
        }
        // dd($dataPoints);

        if(!empty($totalCount))
        {
            // dd($not_visit_list_query_15);
            $data['totalCount'] = $totalCount;
            $data['dataPoints'] = $dataPoints;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['totalCount'] = [];
            $data['dataPoints'] = [];
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    public function retailer_sluggish_data(Request $request)
    {
        $from_date = !empty($request->from_date)?$request->from_date:date('Y-m-d');
        $location_3_filter = $request->state_id;
        $company_id = Auth::user()->company_id;

         ################################################################################### inactive retailer starts here 
        
            $last_15_days = date('Y-m-d', strtotime($from_date. ' - 15 days')); 
            $last_30_days = date('Y-m-d', strtotime($from_date. ' - 30 days')); 
            $last_45_days = date('Y-m-d', strtotime($from_date. ' - 45 days')); 
            // dd($last_15_days);
            $query_data_total =DB::table('user_sales_order')
                ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                ->where('user_sales_order.company_id',$company_id);
            if(!empty($location_3_filter))
            {
                $saleOrderValueData->whereIn('l3_id',$location_3_filter);
            }    
            $retailer_id_sale_total = $query_data_total->pluck('retailer_id');
            // dd($retailer_id_sale_total);
            $not_visit_list_query_data_total = DB::table('retailer')
                                    ->join('location_view','location_view.l7_id','=','retailer.location_id')
                                    ->select(DB::raw("count(retailer.id) AS rid"))
                                    ->where('retailer.company_id',$company_id)
                                    ->whereNotIn('retailer.id',$retailer_id_sale_total)
                                    ->where('retailer_status',1);
                                    if(!empty($location_3_filter))
                                    {
                                        $not_visit_list_query_data_total->whereIn('l3_id',$location_3_filter);
                                    }    
                                    
            $not_visit_list_query_total = $not_visit_list_query_data_total->first();

            $state_array_data = DB::table('location_3')->where('status',1);
            if(!empty($location_3_filter))
            {
                $state_array_data->whereIn('id',$location_3_filter);
            }   
            $state_array = $state_array_data->pluck('name','id');

            // ////////////////////////// for last 15 days ////////////////////////////////////////////////
            $query_data_15 =DB::table('user_sales_order')
                ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                ->where('user_sales_order.company_id',$company_id)
                ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') <= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') >= '$last_15_days'");
            if(!empty($location_3_filter))
            {
                $query_data_15->whereIn('l3_id',$location_3_filter);
            }   
            $retailer_id_sale_15 = $query_data_15->pluck('retailer_id');
            // dd($retailer_id_sale_15);
           
            $not_visit_list_query_data_15 = DB::table('retailer')
                                    ->join('location_view','location_view.l7_id','=','retailer.location_id')
                                    ->where('retailer.company_id',$company_id)
                                    ->whereNotIn('retailer.id',$retailer_id_sale_15)
                                    ->where('retailer_status',1)
                                    ->groupBy('l3_id');
                                    if(!empty($location_3_filter))
                                    {
                                        $not_visit_list_query_data_15->whereIn('l3_id',$location_3_filter);
                                    }  
                                    
                                    $not_visit_list_query_15 = $not_visit_list_query_data_15->pluck(DB::raw("count(retailer.id) AS rid"),'l3_id');
                                    // dd($not_visit_list_query_15);
            // //////////////////////////end of for last 15 days ////////////////////////////////////////////////


                                     // ////////////////////////// for last 30 days ////////////////////////////////////////////////
             $query_data_30 =DB::table('user_sales_order')
                            ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                            ->where('user_sales_order.company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') <= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') >= '$last_30_days'");
                            if(!empty($location_3_filter))
                            {
                                $query_data_30->whereIn('l3_id',$location_3_filter);
                            }  
            $retailer_id_sale_30 = $query_data_30->pluck('retailer_id');
            // dd($retailer_id_sale);
           
            $not_visit_list_query_data_30 = DB::table('retailer')
                                    ->join('location_view','location_view.l7_id','=','retailer.location_id')
                                    ->where('retailer.company_id',$company_id)
                                    ->whereNotIn('retailer.id',$retailer_id_sale_30)
                                    ->where('retailer_status',1)
                                    ->groupBy('l3_id');
                                    if(!empty($location_3_filter))
                                    {
                                        $not_visit_list_query_data_30->whereIn('l3_id',$location_3_filter);
                                    } 
                                    
                                    $not_visit_list_query_30 = $not_visit_list_query_data_30->pluck(DB::raw("count(retailer.id) AS rid"),'l3_id');
            // //////////////////////////end of for last 30 days ////////////////////////////////////////////////


                                     // ////////////////////////// for last 45 days ////////////////////////////////////////////////
             $query_data_45 =DB::table('user_sales_order')
                ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                ->where('user_sales_order.company_id',$company_id)
                ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') <= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') >= '$last_45_days'");
            if(!empty($location_3_filter))
            {
                $query_data_45->whereIn('l3_id',$location_3_filter);
            } 
            $retailer_id_sale_45 = $query_data_45->pluck('retailer_id');
            // dd($retailer_id_sale);
           
            $not_visit_list_query_data_45 = DB::table('retailer')
                                    ->join('location_view','location_view.l7_id','=','retailer.location_id')
                                    ->where('retailer.company_id',$company_id)
                                    ->whereNotIn('retailer.id',$retailer_id_sale_45)
                                    ->where('retailer_status',1)
                                    ->groupBy('l3_id');
                                    if(!empty($location_3_filter))
                                    {
                                        $not_visit_list_query_data_45->whereIn('l3_id',$location_3_filter);
                                    }
                                    
            $not_visit_list_query_45 = $not_visit_list_query_data_45->pluck(DB::raw("count(retailer.id) AS rid"),'l3_id');

            $state_data_data = Location3::where('status',1)->where('company_id',$company_id);
                        if(!empty($location_3_filter))
                        {
                            $state_data_data->whereIn('id',$location_3_filter);
                        }
                        $state_data = $state_data_data->pluck('name','id');
        #############################################################################################################
    }

    public function sluggish_retailer_list(Request $request)
    {
        $from_date = $request->from_date;
        $flag = $request->flag;
        $state_id = $request->state_id;
        $company_id = Auth::user()->company_id;

        $last_15_days = date('Y-m-d', strtotime($from_date. ' - 15 days')); 
        $last_30_days = date('Y-m-d', strtotime($from_date. ' - 30 days')); 
        $last_45_days = date('Y-m-d', strtotime($from_date. ' - 45 days')); 


 


        if($flag == 1){
           // ////////////////////////// for last 15 days ////////////////////////////////////////////////
             $query_data_15 =DB::table('user_sales_order')
                ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                ->where('l3_id',$state_id)
                ->where('user_sales_order.company_id',$company_id)
                ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') <= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') >= '$last_15_days'");
        
            $retailer_id_sale_15 = $query_data_15->pluck('retailer_id');
            // dd($retailer_id_sale_15);
           
            $not_visit_list_query_data_15 = DB::table('retailer')
                                    ->join('location_view','location_view.l7_id','=','retailer.location_id')
                                    ->join('dealer','dealer.id','=','retailer.dealer_id')
                                    ->select('retailer.name as retailer_name','retailer.contact_per_name','retailer.landline','dealer.name as dealer_name','l3_name','l7_name')
                                    ->whereNotIn('retailer.id',$retailer_id_sale_15)
                                    ->where('retailer.company_id',$company_id)
                                    ->where('retailer_status',1)
                                    ->where('l3_id',$state_id)
                                    ->groupBy('retailer.id');
                                    
                                    $not_visit_list_query_15 = $not_visit_list_query_data_15->get()->toArray();
                                    // dd($not_visit_list_query_15);
            // //////////////////////////end of for last 15 days ////////////////////////////////////////////////
        }

        elseif($flag == 2){

             $query_data_15 =DB::table('user_sales_order')
                ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                ->where('user_sales_order.company_id',$company_id)
                ->where('l3_id',$state_id)
                ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') <= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') >= '$last_30_days'");
        
            $retailer_id_sale_15 = $query_data_15->pluck('retailer_id');
            // dd($retailer_id_sale_15);
           
            $not_visit_list_query_data_15 = DB::table('retailer')
                                    ->join('location_view','location_view.l7_id','=','retailer.location_id')
                                    ->join('dealer','dealer.id','=','retailer.dealer_id')
                                    ->select('retailer.name as retailer_name','retailer.contact_per_name','retailer.landline','dealer.name as dealer_name','l3_name','l7_name')
                                    ->where('retailer.company_id',$company_id)
                                    ->whereNotIn('retailer.id',$retailer_id_sale_15)
                                    ->where('retailer_status',1)
                                    ->where('l3_id',$state_id)
                                    ->groupBy('retailer.id');
                                    
                                    $not_visit_list_query_15 = $not_visit_list_query_data_15->get()->toArray();

        }

        elseif($flag == 3){

              $query_data_15 =DB::table('user_sales_order')
                ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                ->where('user_sales_order.company_id',$company_id)
                ->where('l3_id',$state_id)
                ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') <= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') >= '$last_45_days'");
        
            $retailer_id_sale_15 = $query_data_15->pluck('retailer_id');
            // dd($retailer_id_sale_15);
           
            $not_visit_list_query_data_15 = DB::table('retailer')
                                    ->join('location_view','location_view.l7_id','=','retailer.location_id')
                                    ->join('dealer','dealer.id','=','retailer.dealer_id')
                                    ->select('retailer.name as retailer_name','retailer.contact_per_name','retailer.landline','dealer.name as dealer_name','l3_name','l7_name')
                                    ->where('retailer.company_id',$company_id)
                                    ->whereNotIn('retailer.id',$retailer_id_sale_15)
                                    ->where('retailer_status',1)
                                    ->where('l3_id',$state_id)
                                    ->groupBy('retailer.id');
                                    
                                    $not_visit_list_query_15 = $not_visit_list_query_data_15->get()->toArray();

        }

        else
        {
            $not_visit_list_query_15 = array();

        }
        



      
        if(!empty($not_visit_list_query_15))
        {
            // dd($not_visit_list_query_15);
            $data['retailer_result'] = $not_visit_list_query_15;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);

    }
    public function getTotalSalesTeamHome(Request $request)
    {
        $user=Auth::user();
        $company_id = Auth::user()->company_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id,$company_id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        // dd($junior_data);

         // get table name for sales 

        $table_name = TableReturn::table_return($from_date,$to_date);
        // dd($table_name);


        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        // $location_3_filter = $request->state_id;
        $user_details_data = DB::table('person')
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->join('users','users.id','=','person.id')
                        ->join('person_details','person_details.person_id','=','person.id')
                    ->leftJoin('location_3','location_3.id','=','person.state_id')
                    ->select('person.id as user_id','person_id_senior',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'location_3.name as state','_role.rolename as role','mobile',DB::raw("(SELECT CONCAT_WS(' ',first_name,middle_name,last_name) from person where person.id = person_id_senior LIMIT 1) as senior_name"),'deleted_deactivated_on','person_status')
                    ->where('person.company_id',$company_id)
                    ->where('person_login.company_id',$company_id)
                    ->where('_role.company_id',$company_id)
                    ->where('is_admin','!=',1)
                    ->where('person_status',1);
                    if(!empty($junior_data))
                    {
                        $user_details_data->whereIn('person.id',$junior_data);
                    }   
                    if(!empty($location_3_filter))
                    {
                        $user_details_data->whereIn('person.state_id',$location_3_filter);
                    } 
        $user_details = $user_details_data->get();


        $deactivateTeamData = DB::table('person')->join('person_login','person_login.person_id','=','person.id')
                        ->join('users','users.id','=','person.id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->join('person_details','person_details.person_id','=','person.id')
                        ->leftJoin('location_3','location_3.id','=','person.state_id')
                        ->select('person.id as user_id','person_id_senior',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'location_3.name as state','_role.rolename as role','mobile',DB::raw("(SELECT CONCAT_WS(' ',first_name,middle_name,last_name) from person where person.id = person_id_senior LIMIT 1) as senior_name"),'deleted_deactivated_on','person_status')
                        ->whereRaw("DATE_FORMAT(person_details.deleted_deactivated_on,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(person_details.deleted_deactivated_on,'%Y-%m-%d')<='$to_date'")
                        ->where('person_status','!=',1)
                        ->where('is_admin','!=',1)
                        ->where('person.company_id',$company_id)
                        ->where('person_login.company_id',$company_id);
                         if(!empty($junior_data))
                        {
                            $deactivateTeamData->whereIn('person.id',$junior_data);
                        }   
                        // ->groupBy('person.id')
        $deactivateTeam = $deactivateTeamData->get();

        // dd($deactivateTeam);



        $att_count = DB::table('user_daily_attendance')
                     ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')<='$to_date'")
                     ->groupBy('user_id')
                     ->pluck(DB::raw("COUNT(DISTINCT work_date,user_id) as count"),"user_id");

                     // dd($att_count);


        if(!empty($user_details))
        {
            $f_out = array();
            foreach ($user_details as $key => $value) 
            {
                $out['user_id'] = $value->user_id;
                $out['user_name'] = $value->user_name;
                $out['senior_name'] = $value->senior_name;
                $out['user_n'] = Crypt::encryptString($value->user_id);
                $out['role'] = $value->role;
                $out['state'] = $value->state;
                $out['mobile'] = $value->mobile;
                $out['mobile'] = $value->mobile;

                $out['status'] = ($value->person_status==1)?'Active':'Deactivated/Deleted';

                if($value->person_status==1){
                $out['deleted_deactivated_on'] = '';
                }else{
                $out['deleted_deactivated_on'] = $value->deleted_deactivated_on;
                }


                $out['att_count'] = !empty($att_count[$value->user_id])?$att_count[$value->user_id]:'0';



                $f_out[] = $out;
            }

            $fd_out = array();
             foreach ($deactivateTeam as $dkey => $dvalue) 
            {
                $out['user_id'] = $dvalue->user_id;
                $out['user_name'] = $dvalue->user_name;
                $out['senior_name'] = $dvalue->senior_name;
                $out['user_n'] = Crypt::encryptString($dvalue->user_id);
                $out['role'] = $dvalue->role;
                $out['state'] = $dvalue->state;
                $out['mobile'] = $dvalue->mobile;
                $out['mobile'] = $value->mobile;

                $out['status'] = ($dvalue->person_status==1)?'Active':'Deactivated/Deleted';

                 if($dvalue->person_status==1){
                $out['deleted_deactivated_on'] = '';
                }else{
                $out['deleted_deactivated_on'] = $dvalue->deleted_deactivated_on;
                }

                $out['att_count'] = !empty($att_count[$value->user_id])?$att_count[$value->user_id]:'0';


                $fd_out[] = $out;
            }

            $finalMerge = array_merge($f_out,$fd_out);


            // dd($finalMerge);
            // dd($not_visit_list_query_15);
            $data['user_details'] = $finalMerge;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);

    }
    public function total_beat_coverage_details(Request $request)
    {
        $user=Auth::user();
        $company_id = Auth::user()->company_id;
        if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id,$company_id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        // $location_3_filter = $request->state_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        // get table name for sales 

        $table_name = TableReturn::table_return($from_date,$to_date);
        // dd($table_name);


        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
        if(empty($check)){
        $beat_coverage_detailsData = DB::table($table_name)
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                                // ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                                ->join('location_7','location_7.id','=',$table_name.'.location_id')
                                ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                                // ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                                ->where($table_name.'.company_id',$company_id)
                                ->select('location_7.name as beat',DB::raw("SUM(rate*quantity) as total_sale_value"))
                                ->groupBy($table_name.'.location_id');
                                if(!empty($junior_data))
                                {
                                    $beat_coverage_detailsData->whereIn($table_name.'.user_id',$junior_data);
                                }
                                if(!empty($location_3_filter))
                                {
                                    $beat_coverage_detailsData->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
                                }
                                $beat_coverage_details = $beat_coverage_detailsData->get();
        }else{
        $beat_coverage_detailsData = DB::table($table_name)
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                                // ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                                ->join('location_7','location_7.id','=',$table_name.'.location_id')
                                ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                                // ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                                ->where($table_name.'.company_id',$company_id)
                                ->select('location_7.name as beat',DB::raw("SUM(final_secondary_rate*final_secondary_qty) as total_sale_value"))
                                ->groupBy($table_name.'.location_id');
                                if(!empty($junior_data))
                                {
                                    $beat_coverage_detailsData->whereIn($table_name.'.user_id',$junior_data);
                                }
                                if(!empty($location_3_filter))
                                {
                                    $beat_coverage_detailsData->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
                                }
                                $beat_coverage_details = $beat_coverage_detailsData->get();
        }
        if(!empty($beat_coverage_details))
        {
            
            $data['beat_coverage_details'] = $beat_coverage_details;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    public function total_productive_coverage_details(Request $request)
    {
        $user=Auth::user();
        $company_id = Auth::user()->company_id;
        if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id,$company_id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;

         // get table name for sales 

        $table_name = TableReturn::table_return($from_date,$to_date);
        // dd($table_name);

        $company_id = Auth::user()->company_id;
        // $company_id = Auth::user()->company_id;
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
        if(empty($check)){
        $productve_call_details_data = DB::table($table_name)
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                                ->join('person','person.id','=',$table_name.'.user_id')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->select('user_id',DB::raw('SUM(rate*quantity) as total_sale_value'),DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'mobile')
                                ->where('call_status',1)
                                ->where($table_name.'.company_id',$company_id)
                                ->where('person_login.company_id',$company_id)
                                ->where('person.company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                                // ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                                ->groupBy('user_id');
                                if(!empty($junior_data))
                                {
                                    $productve_call_details_data->whereIn($table_name.'.user_id',$junior_data);
                                }
                                if(!empty($location_3_filter))
                                {
                                    $productve_call_details_data->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
                                }
        $productve_call_details = $productve_call_details_data->get();
        }else{
            $productve_call_details_data = DB::table($table_name)
            ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
            ->join('person','person.id','=',$table_name.'.user_id')
            ->join('person_login','person_login.person_id','=','person.id')
            ->select('user_id',DB::raw('SUM(final_secondary_rate*final_secondary_qty) as total_sale_value'),DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'mobile')
            ->where('call_status',1)
            ->where($table_name.'.company_id',$company_id)
            ->where('person_login.company_id',$company_id)
            ->where('person.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
            // ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
            ->groupBy('user_id');
            if(!empty($junior_data))
            {
                $productve_call_details_data->whereIn($table_name.'.user_id',$junior_data);
            }
            if(!empty($location_3_filter))
            {
                $productve_call_details_data->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
            }
            $productve_call_details = $productve_call_details_data->get();
        }
        if(!empty($productve_call_details))
        {
            $call_status_count_data = DB::table($table_name)
                            ->where('call_status',1)
                            ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                            // ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                            ->where($table_name.'.company_id',$company_id)
                            ->groupBy('user_id');
                            if(!empty($junior_data))
                            {
                                $call_status_count_data->whereIn($table_name.'.user_id',$junior_data);
                            }
                            if(!empty($location_3_filter))
                            {
                                $call_status_count_data->join('location_view','location_view.l7_id','=',$table_name.'.location_id')->whereIn('l3_id',$location_3_filter);
                            }
            $call_status_count = $call_status_count_data->pluck(DB::raw("COUNT(DISTINCT retailer_id,date) as cout"),'user_id');
            $f_out = array();
            foreach ($productve_call_details as $key => $value) 
            {
                $out['user_id'] = $value->user_id;
                $out['user_name'] = $value->user_name;
                $out['mobile'] = $value->mobile;
                $out['call_status_count'] = !empty($call_status_count[$value->user_id])?$call_status_count[$value->user_id]:'';
                $out['user_n'] = Crypt::encryptString($value->user_id);
                $out['total_sale_value'] = round($value->total_sale_value,2);
                $f_out[] = $out;
            }
            
            $data['productve_call_details'] = $f_out;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    public function get_state_wise_details_home(Request $request)
    {
        $user=Auth::user();
        $company_id = Auth::user()->company_id;
        if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id,$company_id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;

         // get table name for sales 

        $table_name = TableReturn::table_return($from_date,$to_date);
        // dd($table_name);

        // $company_id = Auth::user()->company_id;
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
        if(empty($check)){
        $beat_query_data = DB::table($table_name)
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                    ->join('location_view','location_view.l7_id','=',$table_name.'.location_id')
                    ->select(DB::raw("SUM(rate*quantity) as data"),'l3_name as label1',DB::raw("CONCAT(l3_name,' ',' : ',' ',' [ ',' ',round(SUM(rate*quantity),2),' ',' ] ') as label") )
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    ->where($table_name.'.company_id',$company_id)
                    ->groupBy('l3_id');
                    if(!empty($junior_data))
                    {
                        $beat_query_data->whereIn($table_name.'.user_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $beat_query_data->whereIn('l3_id',$location_3_filter);
                    }
        $beat_query =  $beat_query_data->get();
        }else{
            $beat_query_data = DB::table($table_name)
            ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
            ->join('location_view','location_view.l7_id','=',$table_name.'.location_id')
            ->select(DB::raw("SUM(final_secondary_rate*final_secondary_qty) as data"),'l3_name as label1',DB::raw("CONCAT(l3_name,' ',' : ',' ',' [ ',' ',round(SUM(final_secondary_rate*final_secondary_qty),2),' ',' ] ') as label") )
            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
            ->where($table_name.'.company_id',$company_id)
            ->groupBy('l3_id');
            if(!empty($junior_data))
            {
                $beat_query_data->whereIn($table_name.'.user_id',$junior_data);
            }
            if(!empty($location_3_filter))
            {
                $beat_query_data->whereIn('l3_id',$location_3_filter);
            }
            $beat_query =  $beat_query_data->get();
        }
        if(!empty($beat_query))
        {

            $dataPoints=array();
            $totalCount=array();
            foreach($beat_query as $graphValue)
            {
                $dataPoints[] =array("y" => round($graphValue->data,2), "label" => "$graphValue->label1",'symbol'=>"$graphValue->label1");
                $totalCount[]=round($graphValue->data,2);
            }
            
            $data['beat_query'] = $beat_query;
            $data['dataPoints'] = $dataPoints;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    public function get_state_wise_primary_booking_details_home(Request $request)
    {
        $user=Auth::user();
        $company_id = Auth::user()->company_id;
        if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id,$company_id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;

         // get table name for sales 

        $table_name = TableReturn::table_return($from_date,$to_date);
        // dd($table_name);

        // $company_id = Auth::user()->company_id;
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
        if(empty($check)){

        $beat_query_data = DB::table('user_primary_sales_order')
                    ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                    ->join('dealer','dealer.id','=','user_primary_sales_order.dealer_id')
                    ->join('location_3','location_3.id','=','dealer.state_id')
                    ->select(DB::raw("sum((user_primary_sales_order_details.rate*user_primary_sales_order_details.pcs)+(user_primary_sales_order_details.cases*user_primary_sales_order_details.pr_rate)) as data"),'location_3.name as label1',DB::raw("CONCAT(location_3.name,' ',' : ',' ',' [ ',' ',round(sum((user_primary_sales_order_details.rate*user_primary_sales_order_details.pcs)+(user_primary_sales_order_details.cases*user_primary_sales_order_details.pr_rate)),2),' ',' ] ') as label") )
                    ->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d')<='$to_date'")
                    ->where('user_primary_sales_order.company_id',$company_id)
                    ->groupBy('state_id');
                    if(!empty($junior_data))
                    {
                        $beat_query_data->whereIn('user_primary_sales_order.created_person_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $beat_query_data->whereIn('state_id',$location_3_filter);
                    }
        $beat_query =  $beat_query_data->get();


        // $beat_query_data = DB::table('primary_sale_view')
        //             ->join('dealer','dealer.id','=','primary_sale_view.dealer_id')
        //             ->join('location_3','location_3.id','=','dealer.state_id')
        //             ->select(DB::raw("sum((rate*pcs)+(cases*pr_rate)) as data"),'location_3.name as label1',DB::raw("CONCAT(location_3.name,' ',' : ',' ',' [ ',' ',round(sum((rate*pcs)+(cases*pr_rate)),2),' ',' ] ') as label") )
        //             ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(sale_date,'%Y-%m-%d')<='$to_date'")
        //             ->where('primary_sale_view.company_id',$company_id)
        //             ->groupBy('state_id');
        //             if(!empty($junior_data))
        //             {
        //                 $beat_query_data->whereIn('primary_sale_view.created_person_id',$junior_data);
        //             }
        //             if(!empty($location_3_filter))
        //             {
        //                 $beat_query_data->whereIn('state_id',$location_3_filter);
        //             }
        // $beat_query =  $beat_query_data->get();
        }else{

            $beat_query_data = DB::table('user_primary_sales_order')
                    ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                    ->join('dealer','dealer.id','=','user_primary_sales_order.dealer_id')
                    ->join('location_3','location_3.id','=','dealer.state_id')
                    ->select(DB::raw("sum(final_secondary_rate*final_secondary_qty) as data"),'location_3.name as label1',DB::raw("CONCAT(location_3.name,' ',' : ',' ',' [ ',' ',round(sum((user_primary_sales_order_details.rate*user_primary_sales_order_details.pcs)+(user_primary_sales_order_details.cases*user_primary_sales_order_details.pr_rate)),2),' ',' ] ') as label") )
                    ->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d')<='$to_date'")
                    ->where('user_primary_sales_order.company_id',$company_id)
                    ->groupBy('state_id');
                    if(!empty($junior_data))
                    {
                        $beat_query_data->whereIn('user_primary_sales_order.created_person_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $beat_query_data->whereIn('state_id',$location_3_filter);
                    }
        $beat_query =  $beat_query_data->get();

            // $beat_query_data = DB::table('primary_sale_view')
            //         ->join('dealer','dealer.id','=','primary_sale_view.dealer_id')
            //         ->join('location_3','location_3.id','=','dealer.state_id')
            //         ->select(DB::raw("SUM(final_secondary_rate*final_secondary_qty) as data"),'location_3.name as label1',DB::raw("CONCAT(location_3.name,' ',' : ',' ',' [ ',' ',round(SUM(final_secondary_rate*final_secondary_qty),2),' ',' ] ') as label") )
            //         ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(sale_date,'%Y-%m-%d')<='$to_date'")
            //         ->where('primary_sale_view.company_id',$company_id)
            //         ->groupBy('state_id');
            // if(!empty($junior_data))
            // {
            //     $beat_query_data->whereIn('primary_sale_view.created_person_id',$junior_data);
            // }
            // if(!empty($location_3_filter))
            // {
            //     $beat_query_data->whereIn('state_id',$location_3_filter);
            // }
            // $beat_query =  $beat_query_data->get();
        }
        // dd($beat_query);
        if(!empty($beat_query))
        {

            $dataPoints=array();
            $totalCount=array();
            foreach($beat_query as $graphValue)
            {
                $dataPoints[] =array("y" => round($graphValue->data,2), "label" => "$graphValue->label1",'symbol'=>"$graphValue->label1");
                $totalCount[]=round($graphValue->data,2);
            }
            
            $data['beat_query'] = $beat_query;
            $data['dataPoints'] = $dataPoints;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    public function get_dealer_details_home(Request $request)
    {
        $user=Auth::user();
        $company_id = Auth::user()->company_id;
        if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id,$company_id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $status = $request->status;

        // get table name for sales 

        $table_name = TableReturn::table_return($from_date,$to_date);
        // dd($table_name);

        // $company_id = Auth::user()->company_id;
        
        $data_query_data = Dealer::select('dealer.name as dealer_name','dealer.other_numbers as mobile','l3_name',DB::raw("GROUP_CONCAT(DISTINCT l6_name) as l6_name"),'dealer.id as dealer_id')
                // ->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','dealer.id')
                ->join('location_view','location_view.l6_id','=','dealer.town_id')
                // ->where('dealer_status',1)
                ->where('dealer.company_id',$company_id)
                ->groupBy('dealer_id');
                if (!empty($status)) {
                    if($status==2)
                    {
                        $data_query_data->where('dealer_status',0);
                    }
                    else
                    {
                        $data_query_data->where('dealer_status',$status);
                    }
                }
                if(!empty($location_3_filter))
                {
                    $data_query_data->whereIn('dealer.state_id',$location_3_filter);
                }
                if(!empty($junior_data))
                {
                    $data_query_data->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')->whereIn('dealer_location_rate_list.user_id',$junior_data);
                }
        $data_query = $data_query_data->get();



        $data_retailer_data = DB::table('retailer')
                        ->where('company_id',$company_id)
                        ->where('retailer_status',1)
                        ->groupBy('dealer_id');
                        if(!empty($location_3_filter))
                        {
                            $data_retailer_data->join('location_view','location_view.l7_id','=','retailer.location_id');
                            $data_retailer_data->whereIn('l3_id',$location_3_filter);
                        }
        $data_retailer = $data_retailer_data->pluck(DB::raw("COUNT(distinct retailer.id) as retailer_id"),'dealer_id');

        $data_beat_data= DB::table('dealer_location_rate_list')
                        ->where('company_id',$company_id)
                        ->groupBy('dealer_id');
                        if(!empty($location_3_filter))
                        {
                            $data_beat_data->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id');
                            $data_beat_data->whereIn('l3_id',$location_3_filter);
                        }
        $data_beat = $data_beat_data->pluck(DB::raw("COUNT(distinct location_id) as retailer_id"),'dealer_id');
                        
        $data_user_name_data = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('users','users.id','=','person.id')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                        ->where('person.company_id',$company_id)
                        ->where('person_status',1)
                        ->where('is_admin','!=',1)
                        ->groupBy('dealer_id');
                        if(!empty($location_3_filter))
                        {
                            $data_user_name_data->whereIn('person.state_id',$location_3_filter);
                        }
        $data_user_name = $data_user_name_data->pluck(DB::raw("GROUP_CONCAT(DISTINCT CONCAT_WS(' ',first_name,middle_name,last_name)) as user_name"),'dealer_id');

        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
        if(empty($check)){
        $dealer_sale_data = DB::table($table_name)
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                        ->where($table_name.'.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        ->groupBy('dealer_id');
                    if(!empty($junior_data))
                    {
                        $dealer_sale_data->whereIn($table_name.'.user_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $dealer_sale_data->join('location_view','location_view.l7_id','=',$table_name.'.location_id');
                        $dealer_sale_data->whereIn('l3_id',$location_3_filter);
                    }
        $dealer_sale = $dealer_sale_data->pluck(DB::raw("sum(rate*quantity) as saleorder"),'dealer_id');
        }else{
            $dealer_sale_data = DB::table($table_name)
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                        ->where($table_name.'.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        ->groupBy('dealer_id');
                    if(!empty($junior_data))
                    {
                        $dealer_sale_data->whereIn($table_name.'.user_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $dealer_sale_data->join('location_view','location_view.l7_id','=',$table_name.'.location_id');
                        $dealer_sale_data->whereIn('l3_id',$location_3_filter);
                    }
            $dealer_sale = $dealer_sale_data->pluck(DB::raw("sum(final_secondary_rate*final_secondary_qty) as saleorder"),'dealer_id');
            
        }
       
        if(!empty($data_query))
        {
            $f_out = [];
            foreach ($data_query as $key => $value) {
                $out['dealer_id'] = $value->dealer_id;
                $out['dealer_name'] = $value->dealer_name;
                $out['l3_name'] = $value->l3_name;
                $out['l6_name'] = $value->l6_name;
                // $out['dealer_beat_count'] = $value->beat_count;
                $out['dealer_beat_count'] = !empty($data_beat[$value->dealer_id])?$data_beat[$value->dealer_id]:'0';
                $out['dealer_retailer_count'] = !empty($data_retailer[$value->dealer_id])?$data_retailer[$value->dealer_id]:'0';
                $out['dealer_user_name'] = !empty($data_user_name[$value->dealer_id])?$data_user_name[$value->dealer_id]:'0';
                $out['dealer_sale'] = !empty($dealer_sale[$value->dealer_id])?round($dealer_sale[$value->dealer_id],2):'0';
                $out['dealer_n'] = Crypt::encryptString($value->dealer_id);
                $out['mobile'] = $value->mobile;
                $f_out[] = $out;
            }
            
            $data['dealer_details'] = $f_out;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    public function get_dealer_coverage_details_home(Request $request)
    {
        $user=Auth::user();
        $company_id = Auth::user()->company_id;
        if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id,$company_id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        // get table name for sales 

        $table_name = TableReturn::table_return($from_date,$to_date);
        // dd($table_name);
        // $company_id = Auth::user()->company_id;
        
        $data_query_data = Dealer::select('dealer.name as dealer_name','dealer.other_numbers as mobile','l3_name',DB::raw("GROUP_CONCAT(DISTINCT l6_name) as l6_name"),'dealer.id as dealer_id')
                ->join($table_name,$table_name.'.dealer_id','=','dealer.id')
                ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                ->join('location_view','location_view.l7_id','=',$table_name.'.location_id')
                ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                // ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                ->where('dealer_status',1)
                ->where('dealer.company_id',$company_id)
                ->groupBy('dealer_id');
                if(!empty($location_3_filter))
                {
                    $data_query_data->whereIn('dealer.state_id',$location_3_filter);
                }
        $data_query = $data_query_data->get();
        $data_retailer_data = DB::table('retailer')
                        ->where('company_id',$company_id)
                        ->where('retailer_status',1)
                        ->groupBy('dealer_id');
                        if(!empty($location_3_filter))
                        {
                            $data_retailer_data->join('location_view','location_view.l7_id','=','retailer.location_id');
                            $data_retailer_data->whereIn('l3_id',$location_3_filter);
                        }
        $data_retailer = $data_retailer_data->pluck(DB::raw("COUNT(distinct retailer.id) as retailer_id"),'dealer_id');

        $data_beat_data= DB::table('dealer_location_rate_list')
                        ->where('company_id',$company_id)
                        ->groupBy('dealer_id');
                        if(!empty($location_3_filter))
                        {
                            $data_beat_data->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id');
                            $data_beat_data->whereIn('l3_id',$location_3_filter);
                        }
        $data_beat = $data_beat_data->pluck(DB::raw("COUNT(distinct location_id) as retailer_id"),'dealer_id');
                        
        $data_user_name_data = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('users','users.id','=','person.id')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                        ->where('person.company_id',$company_id)
                        ->where('person_status',1)
                        ->where('is_admin','!=',1)
                        ->groupBy('dealer_id');
                        if(!empty($location_3_filter))
                        {
                            $data_user_name_data->whereIn('person.state_id',$location_3_filter);
                        }
        $data_user_name = $data_user_name_data->pluck(DB::raw("GROUP_CONCAT(DISTINCT CONCAT_WS(' ',first_name,middle_name,last_name)) as user_name"),'dealer_id');

        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
        if(empty($check)){
        $dealer_sale_data = DB::table($table_name)
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                        ->where($table_name.'.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        ->groupBy('dealer_id');
                    if(!empty($junior_data))
                    {
                        $dealer_sale_data->whereIn($table_name.'.user_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $dealer_sale_data->join('location_view','location_view.l7_id','=',$table_name.'.location_id');
                        $dealer_sale_data->whereIn('l3_id',$location_3_filter);
                    }
        $dealer_sale = $dealer_sale_data->pluck(DB::raw("sum(rate*quantity) as saleorder"),'dealer_id');
        }else{
            $dealer_sale_data = DB::table($table_name)
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                        ->where($table_name.'.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        ->groupBy('dealer_id');
                    if(!empty($junior_data))
                    {
                        $dealer_sale_data->whereIn($table_name.'.user_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $dealer_sale_data->join('location_view','location_view.l7_id','=',$table_name.'.location_id');
                        $dealer_sale_data->whereIn('l3_id',$location_3_filter);
                    }
            $dealer_sale = $dealer_sale_data->pluck(DB::raw("sum(final_secondary_rate*final_secondary_qty) as saleorder"),'dealer_id');
            
        }
       
        if(!empty($data_query))
        {
            $f_out = [];
            foreach ($data_query as $key => $value) {
                $out['dealer_id'] = $value->dealer_id;
                $out['dealer_name'] = $value->dealer_name;
                $out['l3_name'] = $value->l3_name;
                $out['l6_name'] = $value->l6_name;
                // $out['dealer_beat_count'] = $value->beat_count;
                $out['dealer_beat_count'] = !empty($data_beat[$value->dealer_id])?$data_beat[$value->dealer_id]:'0';
                $out['dealer_retailer_count'] = !empty($data_retailer[$value->dealer_id])?$data_retailer[$value->dealer_id]:'0';
                $out['dealer_user_name'] = !empty($data_user_name[$value->dealer_id])?$data_user_name[$value->dealer_id]:'0';
                $out['dealer_sale'] = !empty($dealer_sale[$value->dealer_id])?round($dealer_sale[$value->dealer_id],2):'0';
                $out['dealer_n'] = Crypt::encryptString($value->dealer_id);
                $out['mobile'] = $value->mobile;
                $f_out[] = $out;
            }
            
            $data['dealer_details'] = $f_out;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    public function get_retailer_details_home(Request $request)
    {
        $user=Auth::user();
        $company_id = Auth::user()->company_id;
        if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id,$company_id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        // get table name for sales 

        $table_name = TableReturn::table_return($from_date,$to_date);
        // dd($table_name);
        // $company_id = Auth::user()->company_id;
        
        $data_query_data = Retailer::select('created_on','retailer.name as retailer_name','retailer.landline as mobile','dealer.name as dealer_name','l6_name','l7_name','l3_name','dealer.id as dealer_id','retailer.id as retailer_id')
                ->join('dealer','dealer.id','=','retailer.dealer_id')
                ->join('location_view','location_view.l7_id','=','retailer.location_id')
                ->where('retailer_status',1)
                ->where('dealer_status',1)
                ->where('dealer.company_id',$company_id)
                ->where('retailer.company_id',$company_id)
                ->groupBy('retailer.id');
                if(!empty($location_3_filter))
                {
                    $data_query_data->whereIn('l3_id',$location_3_filter);
                }
        $data_query = $data_query_data->get();
        $added__by_person_data = DB::table('retailer')
                        ->join('person','person.id','=','retailer.created_by_person_id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('users','users.id','=','person.id')
                        ->where('retailer.company_id',$company_id)
                        ->where('is_admin','!=',1)
                        ->where('person_status',1)
                        ->where('retailer_status',1)
                        ->groupBy('retailer.id');
                        if(!empty($location_3_filter))
                        {
                            $added__by_person_data->whereIn('person.state_id',$location_3_filter);
                        }
        $added__by_person = $added__by_person_data->pluck(DB::raw("GROUP_CONCAT(DISTINCT CONCAT_WS(' ',first_name,middle_name,last_name)) as user_name"),'retailer.id');

        $assin_user_retailer= DB::table('dealer_location_rate_list')
                        ->join('retailer','dealer_location_rate_list.location_id','=','retailer.location_id')
                        ->join('person','dealer_location_rate_list.user_id','=','person.id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('users','users.id','=','person.id')
                        ->where('dealer_location_rate_list.company_id',$company_id)
                        ->where('retailer.company_id',$company_id)
                        ->groupBy('retailer.id');
                        if(!empty($location_3_filter))
                        {
                            $assin_user_retailer->whereIn('person.state_id',$location_3_filter);
                        }
        $assin_user_retailer_data = $assin_user_retailer->pluck(DB::raw("GROUP_CONCAT(DISTINCT CONCAT_WS(' ',first_name,middle_name,last_name)) as user_name"),'retailer.id');
                        
        
        // $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
        // if(empty($check)){
        // $retailer_sale_data = DB::table('user_sales_order')
        //                 ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        //                 ->where('user_sales_order.company_id',$company_id)
        //                 ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        //                 ->groupBy('retailer_id');
        //             if(!empty($junior_data))
        //             {
        //                 $retailer_sale_data->whereIn('user_sales_order.user_id',$junior_data);
        //             }
        //             if(!empty($location_3_filter))
        //             {
        //                 $retailer_sale_data->join('location_view','location_view.l7_id','=','user_sales_order.location_id');
        //                 $retailer_sale_data->whereIn('l3_id',$location_3_filter);
        //             }
        // $retailer_sale = $retailer_sale_data->pluck(DB::raw("sum(rate*quantity) as saleorder"),'retailer_id');
        // }else{
        //     $retailer_sale_data = DB::table('user_sales_order')
        //                 ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        //                 ->where('user_sales_order.company_id',$company_id)
        //                 ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        //                 ->groupBy('retailer_id');
        //             if(!empty($junior_data))
        //             {
        //                 $retailer_sale_data->whereIn('user_sales_order.user_id',$junior_data);
        //             }
        //             if(!empty($location_3_filter))
        //             {
        //                 $retailer_sale_data->join('location_view','location_view.l7_id','=','user_sales_order.location_id');
        //                 $retailer_sale_data->whereIn('l3_id',$location_3_filter);
        //             }
        //     $retailer_sale = $retailer_sale_data->pluck(DB::raw("sum(final_secondary_rate*final_secondary_qty) as saleorder"),'retailer_id');
            
        // }
       

        // $retailer_sale_data = DB::table('user_sales_order')
        //                 ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        //                 ->where('user_sales_order.company_id',$company_id)
        //                 ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        //                 ->groupBy('retailer_id');
        //                 if(!empty($location_3_filter))
        //                 {
        //                     $retailer_sale_data->join('location_view','location_view.l7_id','=','user_sales_order.location_id');
        //                     $retailer_sale_data->whereIn('l3_id',$location_3_filter);
        //                 }
        // $retailer_sale = $retailer_sale_data->pluck(DB::raw("sum(rate*quantity) as saleorder"),'retailer_id');
        if(!empty($data_query))
        {
            $f_out = [];
            foreach ($data_query as $key => $value) {
                $out['dealer_id'] = $value->dealer_id;
                $out['dealer_name'] = $value->dealer_name;
                $out['retailer_name'] = $value->retailer_name;
                $out['retailer_id'] = $value->retailer_id;
                $out['l3_name'] = $value->l3_name;
                $out['l6_name'] = $value->l6_name;
                $out['l7_name'] = $value->l7_name;
                // $out['dealer_beat_count'] = $value->beat_count;
                $out['retailer_user_name'] = !empty($assin_user_retailer_data[$value->retailer_id])?$assin_user_retailer_data[$value->retailer_id]:'';
                $out['added_by_user'] = !empty($added__by_person[$value->retailer_id])?$added__by_person[$value->retailer_id]:'';
                $out['retailer_date'] = $value->created_on;
                $out['retailer_sale'] = !empty($retailer_sale[$value->retailer_id])?round($retailer_sale[$value->retailer_id],2):'0';
                $out['dealer_n'] = Crypt::encryptString($value->dealer_id);
                $out['retailer_n'] = Crypt::encryptString($value->retailer_id);
                $out['mobile'] = $value->mobile;
                $f_out[] = $out;
            }
            
            $data['dealer_details'] = $f_out;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    public function get_retailer_coverage_details_home(Request $request)
    {
        $user=Auth::user();
        $company_id = Auth::user()->company_id;
        if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id,$company_id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        // get table name for sales 

        $table_name = TableReturn::table_return($from_date,$to_date);
        // dd($table_name);
        // $company_id = Auth::user()->company_id;
        
        $data_query_data = Retailer::select('created_on','retailer.name as retailer_name','retailer.landline as mobile','dealer.name as dealer_name','l6_name','l7_name','l3_name','dealer.id as dealer_id','retailer.id as retailer_id')
                ->join($table_name,$table_name.'.retailer_id','=','retailer.id')
                ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                ->join('location_view','location_view.l7_id','=',$table_name.'.location_id')
                ->join('dealer','dealer.id','=',$table_name.'.dealer_id')
                ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                // ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                ->where('retailer_status',1)
                ->where('dealer_status',1)
                ->where('dealer.company_id',$company_id)
                ->where('retailer.company_id',$company_id)
                ->groupBy('retailer.id');
                if(!empty($location_3_filter))
                {
                    $data_query_data->whereIn('l3_id',$location_3_filter);
                }
        $data_query = $data_query_data->get();
        $added__by_person_data = DB::table('retailer')
                        ->join('person','person.id','=','retailer.created_by_person_id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('users','users.id','=','person.id')
                        ->where('retailer.company_id',$company_id)
                        ->where('is_admin','!=',1)
                        ->where('person_status',1)
                        ->where('retailer_status',1)
                        ->groupBy('retailer.id');
                        if(!empty($location_3_filter))
                        {
                            $added__by_person_data->whereIn('person.state_id',$location_3_filter);
                        }
        $added__by_person = $added__by_person_data->pluck(DB::raw("GROUP_CONCAT(DISTINCT CONCAT_WS(' ',first_name,middle_name,last_name)) as user_name"),'retailer.id');

        $assin_user_retailer= DB::table('dealer_location_rate_list')
                        ->join('retailer','dealer_location_rate_list.location_id','=','retailer.location_id')
                        ->join('person','dealer_location_rate_list.user_id','=','person.id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('users','users.id','=','person.id')
                        ->where('dealer_location_rate_list.company_id',$company_id)
                        ->where('retailer.company_id',$company_id)
                        ->groupBy('retailer.id');
                        if(!empty($location_3_filter))
                        {
                            $assin_user_retailer->whereIn('person.state_id',$location_3_filter);
                        }
        $assin_user_retailer_data = $assin_user_retailer->pluck(DB::raw("GROUP_CONCAT(DISTINCT CONCAT_WS(' ',first_name,middle_name,last_name)) as user_name"),'retailer.id');
                        
        
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
        if(empty($check)){
        $retailer_sale_data = DB::table($table_name)
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                        ->where($table_name.'.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        ->groupBy('retailer_id');
                    if(!empty($junior_data))
                    {
                        $retailer_sale_data->whereIn($table_name.'.user_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $retailer_sale_data->join('location_view','location_view.l7_id','=',$table_name.'.location_id');
                        $retailer_sale_data->whereIn('l3_id',$location_3_filter);
                    }
        $retailer_sale = $retailer_sale_data->pluck(DB::raw("sum(rate*quantity) as saleorder"),'retailer_id');
        }else{
            $retailer_sale_data = DB::table($table_name)
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                        ->where($table_name.'.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(".$table_name.".date,'%Y-%m-%d')<='$to_date'")
                        // ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        ->groupBy('retailer_id');
                    if(!empty($junior_data))
                    {
                        $retailer_sale_data->whereIn($table_name.'.user_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $retailer_sale_data->join('location_view','location_view.l7_id','=',$table_name.'.location_id');
                        $retailer_sale_data->whereIn('l3_id',$location_3_filter);
                    }
            $retailer_sale = $retailer_sale_data->pluck(DB::raw("sum(final_secondary_rate*final_secondary_qty) as saleorder"),'retailer_id');
            
        }
       

        // $retailer_sale_data = DB::table('user_sales_order')
        //                 ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        //                 ->where('user_sales_order.company_id',$company_id)
        //                 ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        //                 ->groupBy('retailer_id');
        //                 if(!empty($location_3_filter))
        //                 {
        //                     $retailer_sale_data->join('location_view','location_view.l7_id','=','user_sales_order.location_id');
        //                     $retailer_sale_data->whereIn('l3_id',$location_3_filter);
        //                 }
        // $retailer_sale = $retailer_sale_data->pluck(DB::raw("sum(rate*quantity) as saleorder"),'retailer_id');
        if(!empty($data_query))
        {
            $f_out = [];
            foreach ($data_query as $key => $value) {
                $out['dealer_id'] = $value->dealer_id;
                $out['dealer_name'] = $value->dealer_name;
                $out['retailer_name'] = $value->retailer_name;
                $out['retailer_id'] = $value->retailer_id;
                $out['l3_name'] = $value->l3_name;
                $out['l6_name'] = $value->l6_name;
                $out['l7_name'] = $value->l7_name;
                // $out['dealer_beat_count'] = $value->beat_count;
                $out['retailer_user_name'] = !empty($assin_user_retailer_data[$value->retailer_id])?$assin_user_retailer_data[$value->retailer_id]:'';
                $out['added_by_user'] = !empty($added__by_person[$value->retailer_id])?$added__by_person[$value->retailer_id]:'';
                $out['retailer_date'] = $value->created_on;
                $out['retailer_sale'] = !empty($retailer_sale[$value->retailer_id])?round($retailer_sale[$value->retailer_id],2):'0';
                $out['dealer_n'] = Crypt::encryptString($value->dealer_id);
                $out['retailer_n'] = Crypt::encryptString($value->retailer_id);
                $out['mobile'] = $value->mobile;
                $f_out[] = $out;
            }
            
            $data['dealer_details'] = $f_out;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function get_beat_details_home(Request $request)
    {
        $user=Auth::user();
        $company_id = Auth::user()->company_id;
        if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id,$company_id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        // get table name for sales 

        $table_name = TableReturn::table_return($from_date,$to_date);
        // dd($table_name);
        // $company_id = Auth::user()->company_id;
        
        $data_query_data = DB::table('location_view')
                ->join('location_7','location_7.id','=','location_view.l7_id')
                ->select('l7_name','l6_name','l3_name','l7_id')
                ->where('l7_company_id',$company_id)
                ->where('l6_company_id',$company_id)
                ->where('location_7.status',1)
                ->groupBy('l7_id');
                if(!empty($location_3_filter))
                {
                    $data_query_data->whereIn('l3_id',$location_3_filter);
                }
        $data_query = $data_query_data->get();
        $retailer_count_data = DB::table('retailer')
                        ->join('location_view','location_view.l7_id','=','retailer.location_id')
                        ->where('retailer_status',1)
                        ->where('retailer.company_id',$company_id)
                        ->groupBy('location_id');
                        if(!empty($location_3_filter))
                        {
                            $retailer_count_data->whereIn('l3_id',$location_3_filter);
                        }
        $retailer_count = $retailer_count_data->pluck(DB::raw("COUNT(DISTINCT retailer.id) as user_name"),'location_id');

        $dealer_count_data = DB::table('dealer')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                        ->where('dealer_status',1)
                        ->where('dealer.company_id',$company_id)
                        ->groupBy('location_id');
                        if(!empty($location_3_filter))
                        {
                            $dealer_count_data->whereIn('state_id',$location_3_filter);
                        }
        $dealer_count = $dealer_count_data->pluck(DB::raw("COUNT(DISTINCT dealer.id) as user_name"),'location_id');
                        
        
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
        if(empty($check)){
        $beat_sale_data = DB::table($table_name)
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                        ->where($table_name.'.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        ->groupBy('location_id');
                    if(!empty($junior_data))
                    {
                        $beat_sale_data->whereIn($table_name.'.user_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $beat_sale_data->join('location_view','location_view.l7_id','=',$table_name.'.location_id');
                        $beat_sale_data->whereIn('l3_id',$location_3_filter);
                    }
        $beat_sale = $beat_sale_data->pluck(DB::raw("sum(rate*quantity) as saleorder"),'location_id');
        }else{
            $beat_sale_data = DB::table($table_name)
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                        ->where($table_name.'.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        ->groupBy('location_id');
                    if(!empty($junior_data))
                    {
                        // $beat_sale_data->join('location_view','location_view.l7_id','=','user_sales_order.location_id');
                        $beat_sale_data->whereIn($table_name.'.user_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $beat_sale_data->join('location_view','location_view.l7_id','=',$table_name.'.location_id');
                        $beat_sale_data->whereIn('l3_id',$location_3_filter);
                    }
            $beat_sale = $beat_sale_data->pluck(DB::raw("sum(final_secondary_rate*final_secondary_qty) as saleorder"),'location_id');
            
        }
        // $beat_sale_data = DB::table('user_sales_order')
        //                 ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        //                 ->where('user_sales_order.company_id',$company_id)
        //                 ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        //                 ->groupBy('location_id');
        //                 if(!empty($location_3_filter))
        //                 {
        //                     $beat_sale_data->join('location_view','location_view.l7_id','=','user_sales_order.location_id');
        //                     $beat_sale_data->whereIn('l3_id',$location_3_filter);
        //                 }
        // $beat_sale = $beat_sale_data->pluck(DB::raw("sum(rate*quantity) as saleorder"),'location_id');
        if(!empty($data_query))
        {
            $f_out = [];
            foreach ($data_query as $key => $value) {
                $out['l3_name'] = $value->l3_name;
                $out['l6_name'] = $value->l6_name;
                $out['l7_name'] = $value->l7_name;
                $out['retailer_count'] = !empty($retailer_count[$value->l7_id])?$retailer_count[$value->l7_id]:'0';
                $out['dealer_count'] = !empty($dealer_count[$value->l7_id])?$dealer_count[$value->l7_id]:'0';
                $out['sale_value'] = !empty($beat_sale[$value->l7_id])?round($beat_sale[$value->l7_id],2):'0';
                $f_out[] = $out;
            }
            
            $data['beat_details'] = $f_out;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    public function get_total_call_details_home(Request $request)
    {
        $user=Auth::user();
        $company_id = Auth::user()->company_id;
        if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id,$company_id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        // get table name for sales 

        $table_name = TableReturn::table_return($from_date,$to_date);
        // dd($table_name);
        // $company_id = Auth::user()->company_id;
        
       

        $productive_count_data = DB::table('location_view')
                        ->join('retailer','retailer.location_id','=','location_view.l7_id')
                        ->join($table_name,$table_name.'.retailer_id','=','retailer.id')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                        ->join('location_7','location_7.id','=','location_view.l7_id')
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        ->where('l7_company_id',$company_id)
                        ->where('l6_company_id',$company_id)
                        ->where('retailer.company_id',$company_id)
                        ->where('location_7.status',1)
                        ->where('call_status',1)
                        ->where('retailer_status',1)
                        ->groupBy('date');
                        if(!empty($location_3_filter))
                        {
                            $productive_count_data->whereIn('l3_id',$location_3_filter);
                        }
                        if(!empty($junior_data))
                        {
                            $productive_count_data->whereIn($table_name.'.user_id',$junior_data);
                        }
        $productive_count = $productive_count_data->pluck(DB::raw("COUNT(DISTINCT retailer_id) as retailer_count"),'date');

        $total_count_data = DB::table('location_view')
                        ->join('retailer','retailer.location_id','=','location_view.l7_id')
                        ->join($table_name,$table_name.'.retailer_id','=','retailer.id')
                        // ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('location_7','location_7.id','=','location_view.l7_id')
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        ->where('l7_company_id',$company_id)
                        ->where('l6_company_id',$company_id)
                        ->where('retailer.company_id',$company_id)
                        ->where('location_7.status',1)
                        // ->where('call_status',1)
                        ->where('retailer_status',1)
                        ->groupBy('date');
                        if(!empty($location_3_filter))
                        {
                            $total_count_data->whereIn('l3_id',$location_3_filter);
                        }
                        if(!empty($junior_data))
                        {
                            $total_count_data->whereIn($table_name.'.user_id',$junior_data);
                        }
        $total_count = $total_count_data->pluck(DB::raw("COUNT(DISTINCT retailer_id) as retailer_count"),'date');

                        
        
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
        if(empty($check)){
            $data_query_data = DB::table('location_view')
                    ->join('retailer','retailer.location_id','=','location_view.l7_id')
                    ->join($table_name,$table_name.'.retailer_id','=','retailer.id')
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                    ->join('location_7','location_7.id','=','location_view.l7_id')
                    ->select('l7_name','l6_name','l3_name','l7_id',DB::raw("sum(rate*quantity) as saleorder"),'date')
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    ->where('l7_company_id',$company_id)
                    ->where('l6_company_id',$company_id)
                    ->where('retailer.company_id',$company_id)
                    ->where('location_7.status',1)
                    ->where('retailer_status',1)
                    ->groupBy('date');
                    if(!empty($junior_data))
                    {
                        $data_query_data->join('location_view','location_view.l7_id','=',$table_name.'.location_id');
                        $data_query_data->whereIn($table_name.'.user_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $data_query_data->whereIn('l3_id',$location_3_filter);
                    }
            $data_query = $data_query_data->get();
        }else{
            $data_query_data = DB::table('location_view')
                    ->join('retailer','retailer.location_id','=','location_view.l7_id')
                    ->join($table_name,$table_name.'.retailer_id','=','retailer.id')
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                    ->join('location_7','location_7.id','=','location_view.l7_id')
                    ->select(DB::raw("sum(final_secondary_rate*final_secondary_qty) as saleorder"),'date')
                    ->where('l7_company_id',$company_id)
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    ->where('l6_company_id',$company_id)
                    ->where('retailer.company_id',$company_id)
                    ->where('location_7.status',1)
                    ->where('retailer_status',1)
                    ->groupBy('date');
                    if(!empty($junior_data))
                    {
                        $data_query_data->join('location_view','location_view.l7_id','=',$table_name.'.location_id');
                        $data_query_data->whereIn($table_name.'.user_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $data_query_data->whereIn('l3_id',$location_3_filter);
                    }
            $data_query = $data_query_data->get();
            
        }
        // $beat_sale_data = DB::table('user_sales_order')
        //                 ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        //                 ->where('user_sales_order.company_id',$company_id)
        //                 ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        //                 ->groupBy('location_id');
        //                 if(!empty($location_3_filter))
        //                 {
        //                     $beat_sale_data->join('location_view','location_view.l7_id','=','user_sales_order.location_id');
        //                     $beat_sale_data->whereIn('l3_id',$location_3_filter);
        //                 }
        // $beat_sale = $beat_sale_data->pluck(DB::raw("sum(rate*quantity) as saleorder"),'location_id');
        if(!empty($data_query))
        {
            $f_out = [];
            foreach ($data_query as $key => $value) {
                // $out['retailer_count'] = $value->retailer_count;
                $out['date'] = $value->date;
                $out['sale_value'] = round($value->saleorder,2);
               
                $out['productive_count'] = !empty($productive_count[$value->date])?$productive_count[$value->date]:'0';
                $out['retailer_count'] = !empty($total_count[$value->date])?$total_count[$value->date]:'0';
                $f_out[] = $out;
            }
            
            $data['total_call_details'] = $f_out;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    public function test_vedio(Request $request)
    {
        // dd($request);
        $vedio_id = $request->id;
        $company_id = Auth::user()->company_id;
        $data = DB::table('vedios_data')->where('company_id',$company_id)->get();
        $main_vedio_data = DB::table('vedios_data')->where('company_id',$company_id);
                            if(!empty($vedio_id))
                            {
                                $main_vedio_data->where('id',$vedio_id);
                            }
                        $main_vedio = $main_vedio_data->orderBY('id','DESC')->first();
                        // dd($main_vedio);
        return view('test_vedio',
            [
                'menu' => 'test',
                'videos'=> $data,
                'main_vedio'=> $main_vedio,
            ]);
    }
    public function TraningModuleWork($id)
    {
        // dd($id);
        $vedio_id = $id;
        $company_id = Auth::user()->company_id;
        $data = DB::table('vedios_data')->where('company_id',$company_id)->get();
        $main_vedio_data = DB::table('vedios_data')->where('company_id',$company_id);
                            if(!empty($vedio_id))
                            {
                                $main_vedio_data->where('id',$vedio_id);
                            }
                        $main_vedio = $main_vedio_data->orderBY('id','DESC')->first();
                        // dd($main_vedio);
        return view('test_vedio',
            [
                'menu' => 'test',
                'videos'=> $data,
                'main_vedio'=> $main_vedio,
            ]);
    }
    public function user_details_on_roles(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $work_status = $request->work_status;
        $cdate = date('Y-m-d');
        $role_id = $request->role_id;
        // dd($work_status);
        if($work_status == 'Absent')
        {
            $attendance_details_data = DB::table('person')
                                ->join('person_login','person.id','=','person_login.person_id')
                                ->join('location_3','location_3.id','=','person.state_id')
                                ->join('user_daily_attendance','user_daily_attendance.user_id','=','person.id')
                                ->join('_role','_role.role_id','=','person.role_id')
                                // ->select('location_3.name as l3_name','rolename','person.id as user_id','mobile',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"))    
                                ->where('person.company_id',$company_id)
                                ->where('person_status',1)
                                // ->where('user_daily_attendance.work_status',$work_status)
                                ->where('person.role_id',$role_id)
                                ->orderBy('person.first_name','ASC')
                                ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$cdate'");
                                if(!empty($junior_data))
                                {
                                    $attendance_details_data->whereIn('user_daily_attendance.user_id',$junior_data);
                                }
                                // if(!empty($work_status))
                                // {
                                //     $attendance_details_data->whereIn('user_daily_attendance.work_status',$work_status);
                                // }
                                if(!empty($location_3_filter))
                                {
                                    $attendance_details_data->whereIn('person.state_id',$location_3_filter);
                                }   
            $attendance_details= $attendance_details_data->pluck('user_id');
            // dd($attendance_details);
            $attendance_details_data = DB::table('person')
                                ->join('person_login','person.id','=','person_login.person_id')
                                ->join('location_3','location_3.id','=','person.state_id')
                                // ->join('user_daily_attendance','user_daily_attendance.user_id','=','person.id')
                                ->join('_role','_role.role_id','=','person.role_id')
                                ->select('location_3.name as l3_name','rolename','person.id as user_id','mobile',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"))    
                                ->where('person.company_id',$company_id)
                                ->where('person_status',1)
                                // ->where('user_daily_attendance.work_status',$work_status)
                                ->where('person.role_id',$role_id)
                                ->whereNotIn('person.id',$attendance_details)
                                ->orderBy('person.first_name','ASC')
                                ->groupBy('person.id');
                                // ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$cdate'");
                                if(!empty($junior_data))
                                {
                                    $attendance_details_data->whereIn('person.id',$junior_data);
                                }
                                // if(!empty($work_status))
                                // {
                                //     $attendance_details_data->whereIn('user_daily_attendance.work_status',$work_status);
                                // }
                                if(!empty($location_3_filter))
                                {
                                    $attendance_details_data->whereIn('person.state_id',$location_3_filter);
                                }   
            $attendance_details= $attendance_details_data->get();
            // dd($attendance_details);
        }

        elseif($work_status == 0)
        {
            $attendance_details_data = DB::table('person')
                                ->join('person_login','person.id','=','person_login.person_id')
                                ->join('location_3','location_3.id','=','person.state_id')
                                // ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                                ->join('_role','_role.role_id','=','person.role_id')
                                ->select('location_3.name as l3_name','rolename','person.id as user_id','mobile',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"))    
                                ->where('person.company_id',$company_id)
                                ->where('person_status',1)
                                // ->where('user_daily_attendance.work_status',$work_status)
                                ->where('person.role_id',$role_id)
                                ->orderBy('person.first_name','ASC');
                                // ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$cdate'");
                                if(!empty($junior_data))
                                {
                                    $attendance_details_data->whereIn('person.id',$junior_data);
                                }
                                // if(!empty($work_status))
                                // {
                                //     $attendance_details_data->whereIn('user_daily_attendance.work_status',$work_status);
                                // }
                                if(!empty($location_3_filter))
                                {
                                    $attendance_details_data->whereIn('person.state_id',$location_3_filter);
                                }   
            $attendance_details= $attendance_details_data->get();
        }
        else
        {
            $attendance_details_data = DB::table('user_daily_attendance')
                                ->join('person','person.id','=','user_daily_attendance.user_id')
                                ->join('location_3','location_3.id','=','person.state_id')
                                ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                                ->join('_role','_role.role_id','=','person.role_id')
                                ->select('location_3.name as l3_name','rolename','user_id','_working_status.name as work_status_name','mobile',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'work_status','work_date')
                                ->where('user_daily_attendance.company_id',$company_id)
                                // ->where('user_daily_attendance.work_status',$work_status)
                                ->where('person.role_id',$role_id)
                                ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$cdate'")
                                ->orderBy('person.first_name','ASC');
                                if(!empty($junior_data))
                                {
                                    $attendance_details_data->whereIn('user_daily_attendance.user_id',$junior_data);
                                }
                                if(!empty($work_status))
                                {
                                    $attendance_details_data->where('user_daily_attendance.work_status',$work_status);
                                }
                                if(!empty($location_3_filter))
                                {
                                    $attendance_details_data->whereIn('person.state_id',$location_3_filter);
                                }   
            $attendance_details= $attendance_details_data->get();
        }
        // dd($attendance_details);
        $f_out = array();
        if(!empty($attendance_details))
        {
            $out = [];
            foreach ($attendance_details as $key => $value) 
            {
                $out['user_id'] = $value->user_id;
                $out['user_name'] = $value->user_name;
                $out['user_n'] = Crypt::encryptString($value->user_id);
                $out['rolename'] = $value->rolename;
                $out['l3_name'] = $value->l3_name;
                $out['mobile'] = $value->mobile;
                $out['work_date'] = !empty($value->work_date)?$value->work_date:'-';
                $f_out[] = $out;
            }
            $data['attendance_details'] = $f_out;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
        
    }


     public function get_retailer_details_neha_home(Request $request)
    {


        $user=Auth::user();
        $company_id = Auth::user()->company_id;
        if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id,$company_id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        // get table name for sales 

        $table_name = TableReturn::table_return($from_date,$to_date);
        // dd($table_name);
        // $company_id = Auth::user()->company_id;
        
        $data_query_data = Retailer::select('created_on','retailer.name as retailer_name','retailer.landline as mobile','dealer.name as dealer_name','l6_name','l7_name','l3_name','dealer.id as dealer_id','retailer.id as retailer_id')
                ->join('dealer','dealer.id','=','retailer.dealer_id')
                ->join('location_view','location_view.l7_id','=','retailer.location_id')
                ->where('retailer_status',1)
                // ->where('dealer_status',1)
                ->where('dealer.company_id',$company_id)
                ->where('retailer.company_id',$company_id);
                // ->groupBy('retailer.id');
                if(!empty($location_3_filter))
                {
                    $data_query_data->whereIn('l3_id',$location_3_filter);
                }
        $data_query = $data_query_data->count("retailer.id");


        $deactive_data_query_data = Retailer::select('created_on','retailer.name as retailer_name','retailer.landline as mobile','dealer.name as dealer_name','l6_name','l7_name','l3_name','dealer.id as dealer_id','retailer.id as retailer_id')
                ->join('dealer','dealer.id','=','retailer.dealer_id')
                ->join('location_view','location_view.l7_id','=','retailer.location_id')
                ->where('retailer_status',0)
                // ->where('dealer_status',1)
                ->where('dealer.company_id',$company_id)
                ->where('retailer.company_id',$company_id);
                // ->groupBy('retailer.id');
                if(!empty($location_3_filter))
                {
                    $deactive_data_query_data->whereIn('l3_id',$location_3_filter);
                }
        $deactive_data_query = $deactive_data_query_data->count("retailer.id");
      
        $final_loop_array = array("ACTIVE"=>$data_query,"DE-ACTIVE"=>$deactive_data_query);

        $status_array = array("ACTIVE"=>"1","DE-ACTIVE"=>"2");



       
        
        
       
        if(!empty($final_loop_array))
        {
            $f_out = [];
            foreach ($final_loop_array as $key => $value) {
                $out['status'] = $key;
                $out['count'] = $value;
                $out['retailer_status'] = $status_array[$key];

                $out['state_id'] = $location_3_filter;

                $f_out[] = $out;
            }
            
            $data['dealer_details'] = $f_out;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }

        // dd($data);

        return json_encode($data);
    }

     public function get_distributor_details_home_common(Request $request)
    {


         $user=Auth::user();
        $company_id = Auth::user()->company_id;
        if($user->role_id==1 || $user->is_admin=='1' || $user->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id,$company_id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        // get table name for sales 

        $table_name = TableReturn::table_return($from_date,$to_date);
        // dd($table_name);
        // $company_id = Auth::user()->company_id;
        
        $data_query_data = Dealer::where('dealer_status','=','1')
                ->where('dealer.company_id',$company_id);
                if(!empty($location_3_filter))
                {
                    $data_query_data->whereIn('dealer.state_id',$location_3_filter);
                }
                if(!empty($junior_data))
                {
                    $data_query_data->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')->whereIn('dealer_location_rate_list.user_id',$junior_data);
                }
        $data_query = $data_query_data->count("dealer.id");




         $deactive_data_query_data = Dealer::where('dealer_status','=','0')
                ->where('dealer.company_id',$company_id);
                if(!empty($location_3_filter))
                {
                    $deactive_data_query_data->whereIn('dealer.state_id',$location_3_filter);
                }
                if(!empty($junior_data))
                {
                    $deactive_data_query_data->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')->whereIn('dealer_location_rate_list.user_id',$junior_data);
                }
        $deactive_data_query = $deactive_data_query_data->count("dealer.id");


      
        $final_loop_array = array("ACTIVE"=>$data_query,"DE-ACTIVE"=>$deactive_data_query);

        $status_array = array("ACTIVE"=>"1","DE-ACTIVE"=>"2");



       
        
        
       
        if(!empty($final_loop_array))
        {
            $f_out = [];
            foreach ($final_loop_array as $key => $value) {
                $out['status'] = $key;
                $out['count'] = $value;
                $out['dealer_status'] = $status_array[$key];

                $out['state_id'] = $location_3_filter;

                $f_out[] = $out;
            }
            
            $data['dealer_details'] = $f_out;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }

        // dd($data);

        return json_encode($data);
    }



     public function getDayWisePrimary(Request $request)
    {


        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $company_id = Auth::user()->company_id;
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

        
        if(empty($check)){

            $query = DB::table('user_primary_sales_order')
                    ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                    ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                    ->select(DB::raw('SUM(rate*pcs+user_primary_sales_order_details.pr_rate*user_primary_sales_order_details.cases) as count'),
                    DB::raw('DATE_FORMAT(user_primary_sales_order.sale_date, "%Y-%m-%d") as sale_date'),DB::raw("SUM((user_primary_sales_order_details.cases)+(user_primary_sales_order_details.pcs/catalog_product.quantity_per_case)) as total_cases"),'user_primary_sales_order_details.product_id')
                    ->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d')<='$to_date'")
                    ->where('user_primary_sales_order.company_id',$company_id);
                    //->whereRaw('DATE_FORMAT(date, "%Y")='.$cur_year);
                    // ->whereRaw("DATE_FORMAT(complaint.created_at,'%Y') = '$year'")
        }else{

            $query = DB::table('user_primary_sales_order')
                    ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                    ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                    ->select(DB::raw('SUM(final_secondary_rate*final_secondary_qty) as count'),
                    DB::raw('DATE_FORMAT(user_primary_sales_order.sale_date, "%Y-%m-%d") as sale_date'),DB::raw("SUM(final_secondary_qty) as total_cases"),'user_primary_sales_order_details.product_id')
                    ->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d')<='$to_date'")
                    ->where('user_primary_sales_order.company_id',$company_id);

        }
                    
       
       $grapData= $query->groupBy(DB::raw('DATE_FORMAT(user_primary_sales_order.sale_date, "%Y-%m-%d")'))->orderBy('user_primary_sales_order.sale_date','ASC')->get();

       $grapDataCases= $query->groupBy(DB::raw('DATE_FORMAT(user_primary_sales_order.sale_date, "%Y-%m-%d")'),'product_id')->orderBy('user_primary_sales_order.sale_date','ASC')->get();

       $finalCases = array();
       foreach ($grapDataCases as $gkey => $gvalue) {
           $finalCases[$gvalue->sale_date][] = $gvalue->total_cases;
       }


        $primaryValue=array();
        foreach($grapData as $graphValue)
        {
            $primaryValue[$graphValue->sale_date] = round($graphValue->count,2);
        }
        // $primaryValueCases=array();
        // foreach($finalCases as $gmon =>  $val)
        // {
        //     $primaryValueCases[$graphValue->sale_date] = round(array_sum($val),2);
        // }



        $startTime = strtotime($from_date);
        $endTime = strtotime($to_date);
        for ($currentDate = $startTime; $currentDate <= $endTime;  $currentDate += (86400))
        {
            $Store = date('Y-m-d', $currentDate);
            $datesArr_new[] = $Store;
            $datesDisplayArr[] = $Store;
        }

        $dataPoints=array();
        $dataPointsCases=array();
         foreach ($datesArr_new as $key_new => $value_new)
        {
            $set_value_cus = !empty($primaryValue[$value_new])?$primaryValue[$value_new]:'0';
            $set_case_cus = !empty($finalCases[$value_new])?array_sum($finalCases[$value_new]):'0';
             $date_set = date('d-M-y',strtotime($value_new));
            $dataPoints[] =array("y" => round($set_value_cus,2), "label" => "$date_set");
            $dataPointsCases[] =array("y" => round($set_case_cus,2), "label" => "$date_set");

            //    $dataPoints[] =array("x" => "$date_set","y" => round($set_value_cus,2));
            // $dataPointsCases[] =array("x" => "$date_set","y" => round($set_case_cus,2));
        }   



        if(!empty($datesArr_new))
        {
            // dd($not_visit_list_query_15);
            // $data['totalCount'] = $totalCount;
            $data['dataPoints'] = $dataPoints;
            $data['dataPointsCases'] = $dataPointsCases;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['totalCount'] = [];
            $data['dataPoints'] = [];
            $data['dataPointsCases'] = [];
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        // dd(json_encode($data['dataPoints']));
        return json_encode($data);
    }




     public function getMonthWisePrimary(Request $request)
    {


        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $cur_year = date('Y');
        $company_id = Auth::user()->company_id;
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();


        
        if(empty($check)){

            $query = DB::table('user_primary_sales_order')
                    ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                    ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                    ->select(DB::raw('SUM(rate*pcs+user_primary_sales_order_details.pr_rate*user_primary_sales_order_details.cases) as count'),
                    DB::raw('DATE_FORMAT(user_primary_sales_order.sale_date, "%Y-%m") as sale_date'),DB::raw("SUM((user_primary_sales_order_details.cases)+(user_primary_sales_order_details.pcs/catalog_product.quantity_per_case)) as total_cases"),'user_primary_sales_order_details.product_id')
                    // ->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m')>='$from_date' AND DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m')<='$to_date'")
                    ->where('user_primary_sales_order.company_id',$company_id)
                    ->whereRaw('DATE_FORMAT(user_primary_sales_order.sale_date, "%Y")='.$cur_year);
                    // ->whereRaw("DATE_FORMAT(complaint.created_at,'%Y') = '$year'")
        }else{

              $query = DB::table('user_primary_sales_order')
                    ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                    ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                    ->select(DB::raw('SUM(final_secondary_rate*final_secondary_qty) as count'),
                    DB::raw('DATE_FORMAT(user_primary_sales_order.sale_date, "%Y-%m") as sale_date'),DB::raw("SUM(final_secondary_qty) as total_cases"),'user_primary_sales_order_details.product_id')
                    // ->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m')>='$from_date' AND DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m')<='$to_date'")
                    ->where('user_primary_sales_order.company_id',$company_id)
                    ->whereRaw('DATE_FORMAT(user_primary_sales_order.sale_date, "%Y")='.$cur_year);

        }
                    
       
       $grapData= $query->groupBy(DB::raw('DATE_FORMAT(user_primary_sales_order.sale_date, "%Y-%m")'))->orderBy('user_primary_sales_order.sale_date','ASC')->get();

       $grapDataCases= $query->groupBy(DB::raw('DATE_FORMAT(user_primary_sales_order.sale_date, "%Y-%m")'),'product_id')->orderBy('user_primary_sales_order.sale_date','ASC')->get();


       $finalCases = array();
       foreach ($grapDataCases as $gkey => $gvalue) {
           $finalCases[$gvalue->sale_date][] = $gvalue->total_cases;
       }

       $dataPointsCases=array();
        foreach($finalCases as $gmon =>  $val)
        {
            $dataPointsCases[] =array("y" => round(array_sum($val),2), "label" => "$gmon");
        }


        $dataPoints=array();
        foreach($grapData as $graphValue)
        {
            $dataPoints[] =array("y" => round($graphValue->count,2), "label" => "$graphValue->sale_date");
        }
    



        if(!empty($cur_year))
        {
            // $data['totalCount'] = $totalCount;
            $data['dataPoints'] = $dataPoints;
            $data['dataPointsCases'] = $dataPointsCases;
            $data['code'] = 200;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['totalCount'] = [];
            $data['dataPoints'] = [];
            $data['dataPointsCases'] = [];
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }





}

