<?php

namespace App\Http\Controllers;


use App\Person;
use Illuminate\Http\Request;
use App\Dealer;
use App\Retailer;
use App\JuniorData;
use DB;
use DateTime;
use Auth;
use Session;
use App\UserSalesOrder;
use App\SecondarySale;
use App\ChallanOrder;

class TestHomeController extends Controller
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
        
        // dd($to_date);
        // USER DETAILS
        $totalSalesTeamData=Person::join('person_login','person_login.person_id','=','person.id')
                        ->where('person_status',1)
                        ->where('person.company_id',$company_id)
                        ->where('person_login.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $totalSalesTeamData->whereIn('person.id',$junior_data);
                        }
        $totalSalesTeam = $totalSalesTeamData->count();


        $totalAttdData=DB::table('user_daily_attendance')
                ->where('company_id',$company_id)
                ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$cdate'");
                if(!empty($junior_data))
                {
                    $totalAttdData->whereIn('user_id',$junior_data);
                }
        $totalAttd = $totalAttdData->count();
                // dd($totalAttd);
        if(empty($totalAttd))
        {
            $totalAttd=0;
        }
        // END OF USER DETAILE
        // SS DETAILS
        // END OF SS DETAILS
        // DISTRIBUTOR DETAILS
        $totalDistributorData=Dealer::where('dealer_status',1)
                        ->where('dealer.company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $totalDistributorData->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id');
                            $totalDistributorData->whereIn('dealer_location_rate_list.user_id',$junior_data);

                        }
                        $totalDistributor = $totalDistributorData->count();

        $totalDistributorSaleData=DB::table('user_sales_order')
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                            ->where('company_id',$company_id)
                            ->select(DB::raw('count(DISTINCT dealer_id) as dealersale'));
                            if(!empty($junior_data))
                            {
                                $totalDistributorSaleData->whereIn('user_id',$junior_data);

                            }
        $totalDistributorSale = $totalDistributorSaleData->first();
        // END OF DISTRIBUTOR DETAILS
        // OUTLET DETAILS
        $totalOutletData=Retailer::where('retailer_status',1)
                    ->where('company_id',$company_id);
                    if(!empty($junior_data))
                    {
                        $totalOutletData->whereIn('created_by_person_id',$junior_data);
                    }
        $totalOutlet =  $totalOutletData->count();

        $totalOutletSaleData=DB::table('user_sales_order')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                        ->where('company_id',$company_id)
                        ->select(DB::raw('count(DISTINCT retailer_id) as outletsale'));
                        if(!empty($junior_data))
                        {
                            $totalOutletSaleData->whereIn('user_id',$junior_data);

                        }
        $totalOutletSale = $totalOutletSaleData->first();
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

        $location_5 = $location_5Data->count();

        $totalBeatSaleData=DB::table('user_sales_order')
                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                    ->where('company_id',$company_id)
                    ->select(DB::raw('count(DISTINCT location_id) as beatsale'));
                    if(!empty($junior_data))
                    {
                        $totalBeatSaleData->whereIn('user_id',$junior_data);
                    }
        $totalBeatSale = $totalBeatSaleData->first();
        //  beat coverage starts here
        $beat_coverage_detailsData = DB::table('user_sales_order')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                ->leftJoin('retailer','retailer.id','=','user_sales_order.retailer_id')
                                ->leftJoin('location_7','location_7.id','=','user_sales_order.location_id')
                                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                                ->where('user_sales_order.company_id',$company_id)
                                ->select('location_7.name as beat','retailer.name as retailer_name','contact_per_name as contact_person_name',DB::raw("SUM(rate*quantity) as total_sale_value"))
                                ->groupBy('user_sales_order.location_id');
                                if(!empty($junior_data))
                                {
                                    $beat_coverage_detailsData->whereIn('user_sales_order.user_id',$junior_data);
                                }
                                $beat_coverage_details = $beat_coverage_detailsData->get();
        //  beat coverage ends here

       // END OF BEAT DETAILS
        // CALL
        $totalCallData=DB::table('user_sales_order')
                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                    ->where('company_id',$company_id)
                    ->select(DB::raw('count(call_status) as total_call'));
                    if(!empty($junior_data))
                    {
                        $totalCallData->whereIn('user_sales_order.user_id',$junior_data);
                    }
        $totalCall =  $totalCallData->first();

        $productiveCallData=DB::table('user_sales_order')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                        ->where('company_id',$company_id)
                        ->select(DB::raw('count(order_id) as productive_call'))->where('call_status',1);
                        if(!empty($junior_data))
                        {
                            $productiveCallData->whereIn('user_sales_order.user_id',$junior_data);
                        }
        $productiveCall = $productiveCallData->first();

        // productive data details starts here
        $productve_call_details_data = DB::table('user_sales_order')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                ->join('person','person.id','=','user_sales_order.user_id')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->select('user_id',DB::raw('SUM(rate*quantity) as total_sale_value'),DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'mobile')
                                ->where('call_status',1)
                                ->where('user_sales_order.company_id',$company_id)
                                ->where('person_login.company_id',$company_id)
                                ->where('person.company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                                ->groupBy('user_id');
                                if(!empty($junior_data))
                                {
                                    $productve_call_details_data->whereIn('user_sales_order.user_id',$junior_data);
                                }
        $productve_call_details = $productve_call_details_data->get();
                                // dd($productve_call_details);
        $call_status_count = array();
        $call_status_count_data = DB::table('user_sales_order')
                            ->where('call_status',1)
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                            ->where('user_sales_order.company_id',$company_id)
                            ->groupBy('user_id');
                            if(!empty($junior_data))
                            {
                                $call_status_count_data->whereIn('user_sales_order.user_id',$junior_data);
                            }
        $call_status_count = $call_status_count_data->pluck(DB::raw("COUNT(call_status) as cout"),'user_id');
        // dd($call_status_count);
        // END CALL
        // SALE START
        $totalOrderData=DB::table('user_sales_order')
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                    ->select(DB::raw("sum(rate*quantity) AS total_sale_value"))
                    ->where('user_sales_order.company_id',$company_id)
                    ->where('user_sales_order_details.company_id',$company_id)
                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'");
                    if(!empty($junior_data))
                    {
                        $totalOrderData->whereIn('user_sales_order.user_id',$junior_data);
                    }
        $totalOrder = $totalOrderData->first();
        if(empty($totalOrder))
            $totalOrder=0;

        $totalPrimaryOrderData=DB::table('primary_sale_view')
                        ->whereRaw("DATE_FORMAT(primary_sale_view.sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(primary_sale_view.sale_date,'%Y-%m-%d')<='$to_date'")
                        ->select(DB::raw("sum((rate*pcs)+(cases*pr_rate)) AS total_sale_value"))
                        ->where('company_id',$company_id);
                        if(!empty($junior_data))
                        {
                            $totalPrimaryOrderData->whereIn('primary_sale_view.created_person_id',$junior_data);
                        }
        $totalPrimaryOrder = $totalPrimaryOrderData->first();
        if(empty($totalPrimaryOrder))
            $totalPrimaryOrder=0;
        // SALE END
        $roleWiseTeamData=Person::join('person_login','person_login.person_id','=','person.id','inner')
                    ->join('_role','_role.role_id','=','person.role_id','inner')
                    ->select('person.role_id','_role.rolename',DB::raw("count(person.id) as count"))
                    ->where('person_login.company_id',$company_id)
                    ->where('person.company_id',$company_id)
                    ->where('_role.company_id',$company_id)
                    ->where('person_status',1)
                    ->groupBy('person.role_id')
                    ->orderBY('role_sequence','ASC');
                    if(!empty($junior_data))
                    {
                        $roleWiseTeamData->whereIn('person.id',$junior_data);
                    }
        $roleWiseTeam = $roleWiseTeamData->get();
       
        // $catalog1Sale=UserSalesOrder::join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id','inner')
        // ->join('catalog_view','catalog_view.product_id','=','user_sales_order_details.product_id','inner')
        // ->select('c1_id','c1_name',DB::raw("SUM(user_sales_order_details.rate*quantity) as sale"))
        // ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$mdate'")
        // ->groupBy('c1_id')->orderBY('c1_name','ASC')->get();

        $catalog1SaleData = SecondarySale::select('c1_color_code as color_code','c2_name as c1_name',DB::raw("SUM(rate*quantity) as sale"))
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    ->where('company_id',$company_id)
                    ->groupBy('c2_id')
                    ->orderBY('c2_name','ASC');
                    if(!empty($junior_data))
                    {
                        $catalog1SaleData->whereIn('user_id',$junior_data);
                    }
        $catalog1Sale = $catalog1SaleData->get();
        // dd($catalog1Sale);
       
         // for pie char
        $beat_query_data = DB::table('user_sales_order')
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                    ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                    ->select(DB::raw("SUM(rate*quantity) as data"),'l6_name as label1',DB::raw("CONCAT(l6_name,' ',' : ',' ',' [ ',' ',SUM(rate*quantity),' ',' ] ') as label") )
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    ->where('user_sales_order.company_id',$company_id)
                    ->groupBy('l6_id');
                    if(!empty($junior_data))
                    {
                        $beat_query_data->whereIn('user_sales_order.user_id',$junior_data);
                    }
        $beat_query =  $beat_query_data->get();
                    // dd($beat_query);
        //  ends here
        // attendance_detais starts here
        $attendance_details_data = DB::table('user_daily_attendance')
                                ->leftJoin('person','person.id','=','user_daily_attendance.user_id')
                                ->leftJoin('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                                ->select('_working_status.name as work_status_name','mobile',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'work_status','work_date')
                                ->where('user_daily_attendance.company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$cdate'");
                                if(!empty($junior_data))
                                {
                                    $attendance_details_data->whereIn('user_daily_attendance.user_id',$junior_data);
                                }   
        $attendance_details= $attendance_details_data->get();
        // attendance_detais ends here
        // user_details starts here
        $user_details_data = DB::table('person')
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->leftJoin('location_3','location_3.id','=','person.state_id')
                    ->select('person_id_senior',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'location_3.name as state','_role.rolename as role','mobile',DB::raw("(SELECT CONCAT_WS(' ',first_name,middle_name,last_name) from person where person.id = person_id_senior LIMIT 1) as senior_name"))
                    ->where('person.company_id',$company_id)
                    ->where('person_login.company_id',$company_id)
                    ->where('_role.company_id',$company_id)
                    ->where('person_status',1);
                    if(!empty($junior_data))
                    {
                        $user_details_data->whereIn('person.id',$junior_data);
                    }   
        $user_details = $user_details_data->get();

        
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
        $saleOrderValueData = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->where('user_sales_order.company_id',$company_id)
                        ->where('user_sales_order_details.company_id',$company_id)
                        ->groupBy('user_sales_order.date');
                        if(!empty($junior_data))
                        {
                            $saleOrderValueData->whereIn('user_sales_order.user_id',$junior_data);
                        }   
        $saleOrderValue = $saleOrderValueData->pluck(DB::raw("SUM(rate*quantity)"),'user_sales_order.date as date');
      
            // dd($dateVal);
            // $totalChallanValue[]=ChallanOrder::where(DB::raw("(DATE_FORMAT(ch_date,'%Y-%m-%d'))"), "=", ''.$dateVal.'')->sum('amount');
        $totalChallanValue[]=0;
            // $totalOrderValue1[] = 0;
            
        foreach ($datesArr_new as $key_new => $value_new)
        {
       
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

        $totalMeetingData=DB::table('meeting_order_booking')
                                ->whereRaw("DATE_FORMAT(meeting_order_booking.current_date_m,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(meeting_order_booking.current_date_m,'%Y-%m-%d')<='$to_date'")
                                ->where('company_id',$company_id)
                                ->select(DB::raw('count(DISTINCT order_id) as meetings'));
                                if(!empty($junior_data))
                                {
                                    $totalMeetingData->whereIn('user_id',$junior_data);

                                }
        $totalMeeting = $totalMeetingData->first();


        // $dashboard_arr array ends here

        return view('testHome',
            [
                'menu' => $this->menu,
                'current_menu' => $current_menu,
                'company_id' => $company_id,
                'totalMeeting' => $totalMeeting,
                'totalSalesTeam' => $totalSalesTeam,
                'totalDistributor'=>$totalDistributor,
                'totalOutlet'=>$totalOutlet,
                'roleWiseTeam'=>$roleWiseTeam,
                'catalog1Sale'=>$catalog1Sale,
                // 'catalog1challan'=>$catalog1challan,
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
                'beat_query'=>$beat_query,
                'call_status_count'=> $call_status_count,
                'attendance_details' => $attendance_details,
                'user_details' => $user_details,
                'productve_call_details' => $productve_call_details,
                'beat_coverage_details' => $beat_coverage_details
            ]);

    }

  
}


