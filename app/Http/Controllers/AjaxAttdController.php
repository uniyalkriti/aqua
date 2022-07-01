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
use App\Vendor;
use App\CatalogProduct;
use App\UsersDetail;
use App\TableReturn;
use Illuminate\Http\Request;
use DB;
use Auth;
use Session;
use DateTime;

class AjaxAttdController extends Controller
{

    public function __construct()
    {

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->role_id = Auth::user()->role_id;
            $this->company_id = Auth::user()->company_id;
            $this->is_admin = Auth::user()->is_admin;
            $this->without_junior = UserDetail::checkReportJunior($this->role_id,$this->company_id,$this->is_admin);

            return $next($request);
        });
    }

    # it is for regions of state
    ///////////////////// NEW DAILY ATTENDANCE ////////////////////////



    public function dailyAttendanceReport(Request $request)
    {
        if ($request->ajax() && !empty($request->date_range_picker)) {

            $explodeDate = explode(" -", $request->date_range_picker);
             $company_id = Auth::user()->company_id;
            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $work_from = $request->work_from;
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $table_name = TableReturn::table_return($from_date,$to_date);
            $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

            $checkoutarr =[];

            $dgm_rsm_for_btw = DB::table('person')
                                ->join('_role','_role.role_id','=','person.role_id')
                                ->whereIn('person.role_id',[145,168])
                                ->pluck( DB::raw("CONCAT(person.first_name,' ',person.middle_name,' ',person.last_name,'(',rolename,')') as uname"),'id');
$btwdatasenior = array();
            foreach($dgm_rsm_for_btw as $btkey => $btval){

                Session::forget('juniordata');
                $login_user=$btkey;
                 
                $btwdatasenior_call=self::getJuniorUser($login_user);
                $btwdatasenior[$btkey] = $request->session()->get('juniordata');
                 if(empty($btwdatasenior)){
                     $btwdatasenior[]=$login_user;
                            }
            }

            // dd($btwdatasenior);
           
            $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50 || $this->without_junior == 0)
            {
               $datasenior='';
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                 
                $datasenior_call=self::getJuniorUser($login_user);
                $datasenior = $request->session()->get('juniordata');
                 if(empty($datasenior)){
                     $datasenior[]=$login_user;
                            }
            }
            $data1 = UserDetail::join('person_login','person_login.person_id','=','person.id')
                ->join('users','users.id','=','person.id')
                ->join('person_details','person_details.person_id','=','person.id')
                ->join('location_3', 'location_3.id', '=', 'person.state_id')
                ->join('location_2','location_2.id','=','location_3.location_2_id')
                ->join('location_1','location_1.id','=','location_2.location_1_id')

                ->join('location_6', 'location_6.id', '=', 'person.town_id')
                ->join('location_5','location_5.id','=','location_6.location_5_id')
                ->join('location_4','location_4.id','=','location_5.location_4_id')


                ->join('_role', '_role.role_id', '=', 'person.role_id')
                ->select('residential_lat_lng','person.person_id_senior as senior_id','person.mobile as mobile','person.id as person_id','location_3.name as region_txt', 'person.emp_code as emp_code',DB::raw("(select CONCAT_WS(' ',first_name,middle_name,last_name) from person where id = senior_id) as senior_name"), DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as uname"), 'location_3.name as l3_name', 'location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name', '_role.rolename as role')
                ->distinct('person.id')
                ->where('person.company_id',$company_id)
                ->where('person_login.person_status','=', 1);
          #Junior filter
                if($company_id != 37)
                {
                    $data1->where('is_admin', '!=', 1);

                }
            if (!empty($datasenior)) 
            {
                $data1->whereIn('person.id', $datasenior);
            }
         
                #Region filter
            if (!empty($request->region)) 
            {
                $region = $request->region;
                $data1->whereIn('location_2.id', $region);
            }
            #State filter
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $data1->whereIn('location_3.id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $data1->whereIn('location_4.id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $data1->whereIn('location_5.id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $data1->whereIn('location_6.id', $location_6);
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $data1->whereIn('person.role_id', $role);
            }
            // if (!empty($request->location_7)) 
            // {
            //     $location_7 = $request->location_7;
            //     $data1->whereIn('location_7.id', $location_7);
            // }
            #User filter
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $data1->whereIn('person.id', $user);
            }
            $user_record = $data1->get();

            // dd($user_record);

            $mtp_towm_data = DB::table('monthly_tour_program')->join('location_view','location_view.l6_id','=','monthly_tour_program.town') ->where('monthly_tour_program.company_id',$company_id)->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(working_date,'%Y-%m-%d') <='$to_date'")->groupBy('person_id','working_date');
            if (!empty($datasenior)) 
            {
                $mtp_towm_data->whereIn('person_id', $datasenior);
            }
         
                #Region filter
            if (!empty($request->region)) 
            {
                $region = $request->region;
                $mtp_towm_data->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) 
            {
                $area = $request->area;
                $mtp_towm_data->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $mtp_towm_data->whereIn('person_id', $user);
            }
            $mtp_towm = $mtp_towm_data->pluck('l6_name as l4_name',DB::raw("CONCAT(person_id,working_date)"));

            $mtp_beat_data = DB::table('monthly_tour_program')->join('location_view','location_view.l7_id','=','monthly_tour_program.locations') ->where('monthly_tour_program.company_id',$company_id)->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(working_date,'%Y-%m-%d') <='$to_date'")->groupBy('person_id','working_date');
            if (!empty($datasenior)) 
            {
                $mtp_beat_data->whereIn('person_id', $datasenior);
            }
         
                #Region filter
            if (!empty($request->region)) 
            {
                $region = $request->region;
                $mtp_beat_data->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) 
            {
                $area = $request->area;
                $mtp_beat_data->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $mtp_beat_data->whereIn('person_id', $user);
            }
            $mtp_beat = $mtp_beat_data->pluck('l7_name as l7_name',DB::raw("CONCAT(person_id,working_date)"));




            $mtp_task_of_the_day_data = DB::table('monthly_tour_program')->join('location_view','location_view.l6_id','=','monthly_tour_program.town') ->where('monthly_tour_program.company_id',$company_id)->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(working_date,'%Y-%m-%d') <='$to_date'")->groupBy('person_id','working_date');
            if (!empty($datasenior)) 
            {
                $mtp_task_of_the_day_data->whereIn('person_id', $datasenior);
            }
         
                #Region filter
            if (!empty($request->region)) 
            {
                $region = $request->region;
                $mtp_task_of_the_day_data->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) 
            {
                $area = $request->area;
                $mtp_task_of_the_day_data->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $mtp_task_of_the_day_data->whereIn('person_id', $user);
            }
            $mtp_task_of_the_day = $mtp_task_of_the_day_data->pluck('working_status_id',DB::raw("CONCAT(person_id,working_date)"));

            $work_status = DB::table('_task_of_the_day')->where('company_id',$company_id)->pluck('task', 'id');




      
          
            if(empty($check)){        
            $sale_value = DB::table($table_name)
                            ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.".order_id")
                            ->where($table_name.'.company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(date,'%Y-%m-%d') <='$to_date'")
                            ->groupBy('user_id','date')
                            ->pluck(DB::raw("SUM(rate*quantity) as totale_sale_value"),DB::raw("CONCAT(user_id,date)"));  
            }else{
                $sale_value = DB::table($table_name)
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.".order_id")
                            ->where($table_name.'.company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(date,'%Y-%m-%d') <='$to_date'")
                            ->groupBy('user_id','date')
                            ->pluck(DB::raw("SUM(final_secondary_rate*final_secondary_qty) as totale_sale_value"),DB::raw("CONCAT(user_id,date)"));  
            }                 
            $productive_calls = DB::table($table_name)
                            ->where($table_name.'.company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(date,'%Y-%m-%d') <='$to_date'")
                            ->where('call_status','1')
                            ->groupBy('user_id','date')
                            ->pluck(DB::raw('COUNT(DISTINCT '.$table_name.'.retailer_id) as count'),DB::raw("CONCAT(user_id,date)"));


            $total_calls = DB::table($table_name)
                            ->where($table_name.'.company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(date,'%Y-%m-%d') <='$to_date'")
                            ->groupBy('user_id','date')
                            ->pluck(DB::raw('COUNT(DISTINCT '.$table_name.'.retailer_id) as count'),DB::raw("CONCAT(user_id,date)"));


            $daily_reporting = DB::table('daily_reporting')
                            ->where('daily_reporting.company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(work_date,'%Y-%m-%d') <='$to_date'")
                            ->groupBy('user_id',DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d')"))
                            ->pluck(DB::raw('COUNT(DISTINCT daily_reporting.order_id) as count'),DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d')) as concat"));

            $checkout=DB::table('check_out')
            ->join('person','person.id','=','check_out.user_id')
            ->where('check_out.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(work_date,'%Y-%m-%d') <='$to_date'")
            ->groupBy('work_date','user_id')
            ->select('work_date',DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d')) as concat"),'total_call as tc','total_pc as tpc','total_sale_value as tsv')
            ->get();
            
            foreach ($checkout as $checkout_data => $checkout_value) 
            {
            $concat = $checkout_value->concat;
            $checkoutarr[$concat]['tc'] = $checkout_value->tc;
            $checkoutarr[$concat]['tpc'] = $checkout_value->tpc;
            $checkoutarr[$concat]['tsv'] = $checkout_value->tsv;
            }


            $queryQ = DB::table('daily_attendance_view')->join('person','person.id','=','daily_attendance_view.user_id')
             ->join('location_3', 'location_3.id', '=', 'person.state_id')
             ->where('person.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(daily_attendance_view.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(daily_attendance_view.work_date,'%Y-%m-%d') <='$to_date'");
              #User filter
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $queryQ->whereIn('user_id', $user);
            }
             if (!empty($datasenior)) 
            {
                $queryQ->whereIn('user_id', $datasenior);
            }
                #Region filter
            if (!empty($request->region)) 
            {
                $region = $request->region;
                $queryQ->whereIn('location_3.location_2_id', $region);
            }
            #State filter
            if (!empty($request->area)) 
            {
                $area = $request->area;
                $queryQ->whereIn('location_3.id', $area);
            }

            $query=$queryQ->groupBy('work_date','user_id','track_addrs','work','check_out_date','image_name')
            ->get();
            // dd($query);
            $arr=[];
            //$productive_calls='';
            $outuser_idrv='';
            $firstCallData='';
            $lastCallData='';
            $first_call = DB::table($table_name)
                        ->where('company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(".$table_name.".date,'%Y-%m-%d') <='$to_date'")
                        ->groupBy('date','user_id')
                        ->pluck(DB::raw("MIN(time)"),DB::raw("CONCAT(date,user_id)"));

            $last_call = DB::table($table_name)
                        ->where('company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(".$table_name.".date,'%Y-%m-%d') <='$to_date'")
                        ->groupBy('date','user_id')
                        ->pluck(DB::raw("MAX(time)"),DB::raw("CONCAT(date,user_id)"));



            $call_location = DB::table($table_name)
                        ->where('company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(".$table_name.".date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(".$table_name.".date,'%Y-%m-%d') <='$to_date'")
                        ->groupBy('date','user_id')
                        ->pluck('track_address',DB::raw("CONCAT(date,user_id,time)"));


   
            foreach ($query as $k=>$q)
            {

                $date=!empty($q->work_date)?date('Y-m-d',strtotime($q->work_date)):0;
                $arr[$date][$q->user_id]=$q;
               
               
            }
            $weekly_off_query = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->where('person.company_id',$company_id)
                                ->pluck(DB::raw("DATE_FORMAT(weekly_off_data,'%Y-%m-%d') as date"),DB::raw("CONCAT(person.id,DATE_FORMAT(weekly_off_data,'%Y-%m-%d')) as data"));


            $newOutletAdded = DB::table('retailer')
                                ->join('location_view','location_view.l7_id','=','retailer.location_id')
                                ->where('retailer.company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(created_on,'%Y-%m-%d') <='$to_date'")
                                ->groupBy('created_by_person_id',DB::raw("DATE_FORMAT(created_on,'%Y-%m-%d')"))
                                ->pluck(DB::raw("COUNT(DISTINCT retailer.id) as retailer_id"),DB::raw("CONCAT(created_by_person_id,DATE_FORMAT(created_on,'%Y-%m-%d'))"));

// dd($arr);
            if($company_id == 65){

                  return view('reports.daily-attendance.paulsalesajax', [
                'records' => $arr,
                'users' => $user_record,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'mtp_towm'=>$mtp_towm,
                'weekly_off_query' => $weekly_off_query,
                'productive_calls'=>$productive_calls,
                'total_calls'=>$total_calls,
                'daily_reporting'=>$daily_reporting,
                'checkoutarr' => $checkoutarr,
                'sale_value'=>$sale_value,
                'first_call'=>$first_call,
                'last_call' =>$last_call,
                'mtp_beat'=> $mtp_beat,
                'dgm_rsm_for_btw'=> $dgm_rsm_for_btw,
                'btwdatasenior'=> $btwdatasenior,
                'company_id'=> $company_id,
                'newOutletAdded'=> $newOutletAdded,
                'work_from'=> $work_from,
                'work_status'=> $work_status,
                'mtp_task_of_the_day'=> $mtp_task_of_the_day,
                'call_location'=> $call_location,
                
            ]);

            }else{

              return view('reports.daily-attendance.ajax', [
                'records' => $arr,
                'users' => $user_record,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'mtp_towm'=>$mtp_towm,
                'weekly_off_query' => $weekly_off_query,
                'productive_calls'=>$productive_calls,
                'total_calls'=>$total_calls,
                'daily_reporting'=>$daily_reporting,
                'checkoutarr' => $checkoutarr,
                'sale_value'=>$sale_value,
                'first_call'=>$first_call,
                'last_call' =>$last_call,
                'mtp_beat'=> $mtp_beat,
                'dgm_rsm_for_btw'=> $dgm_rsm_for_btw,
                'btwdatasenior'=> $btwdatasenior,
                'company_id'=> $company_id,
                'newOutletAdded'=> $newOutletAdded,
                'work_from'=> $work_from,
                 'work_status'=> $work_status,
                'mtp_task_of_the_day'=> $mtp_task_of_the_day,
                'company_id'=> $company_id,
                'call_location'=> $call_location,
            ]);

            }

          

        }
    }
    ####################
     public function getJuniorUser($code)
    {
        $res1="";
        $res2="";
        $details = UserDetail::where('person_id_senior',$code)
            ->select('id as user_id')->get();
            $num = count($details);  
            if($num>0)
            {
                foreach($details as $key=>$res2)
                {
                    if($res2->user_id!="")
                    {
                        //$product = collect([1,2,3,4]);
                        Session::push('juniordata', $res2->user_id);
                       // $_SESSION['juniordata'][]=$res2->user_id;
                        $this->getJuniorUser($res2->user_id);
                    }
                }
                
            }
            else
            {
                foreach($details as $key1=>$res1)
                {
                    if($res1->user_id!="")
                    {
                        Session::push('juniordata', $res1->user_id);
                        // $_SESSION['juniordata'][]= $res1->user_id;
                    }
                }

                
            }
            return 1;
        }

    public function timeAttdReport(Request $request)
    {   
        if ($request->ajax() && !empty($request->date_range_picker)) {
            $company_id = Auth::user()->company_id;
            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            // $month = $request->month;
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

	    	$array = array(99,100,101,102);

            $start = strtotime($from_date);
            $end = strtotime($to_date);

             $datesArr = array();
             $datesDisplayArr = array();
            $datediff =  (($end - $start)/60/60/24);
            $datesArr[] = $from_date;
            $datesDisplayArr[] = date("j-F-Y",strtotime($from_date));;
            // dd($datediff);
            for($i=0 ; $i<$datediff;$i++)
            {
                $datesArr[] = date('Y-m-d', strtotime($datesArr[$i] .' +1 day'));
                $datesDisplayArr[] = date("j-F-Y",strtotime($datesArr[$i] .' +1 day'));

            }

            $total_days = count($datesArr);

            // dd($datesDisplayArr);

           // $month='2019-01';
        //     $m1=explode('-', $month);
        //     $y=$m1[0];
        //     $m2=$m1[1];
        //     if($m2<10)
        //     $m=ltrim($m2, '0');
        //     else
        //     $m=$m2;

        //     $total_days=cal_days_in_month(CAL_GREGORIAN,$m,$y);
        //     $monthName=array('01' =>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');

        //     for($i = 1; $i <=  $total_days; $i++)
        // {
        // // add the date to the dates array
        // $datesArr[] = $y . "-" . $m2 . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
        // $datesDisplayArr[] =str_pad($i, 2, '0', STR_PAD_LEFT)."-".$monthName[$m2]."-".$y ;
        // }

        // dd($datesDisplayArr);
             if(!empty($request->location_3))
            {
                $location_3_name = DB::table('location_3')->select(DB::raw("GROUP_CONCAT(name) as l3_name"))->where('company_id',$company_id)->whereIn('id',$request->location_3)->first();
                $l3_name = $location_3_name->l3_name;
            }else{
                $l3_name = "ALL";
            }

            if(!empty($request->location_4))
            {
                $location_4_name = DB::table('location_4')->select(DB::raw("GROUP_CONCAT(name) as l4_name"))->where('company_id',$company_id)->whereIn('id',$request->location_4)->first();
                $l4_name = $location_4_name->l4_name;
            }else{
                $l4_name = "ALL";
            }

            if(!empty($request->location_5))
            {
                $location_5_name = DB::table('location_5')->select(DB::raw("GROUP_CONCAT(name) as l5_name"))->where('company_id',$company_id)->whereIn('id',$request->location_5)->first();
                $l5_name = $location_5_name->l5_name;
            }else{
                $l5_name = "ALL";
            }

             if(!empty($request->location_6))
            {
                $location_6_name = DB::table('location_6')->select(DB::raw("GROUP_CONCAT(name) as l6_name"))->where('company_id',$company_id)->whereIn('id',$request->location_6)->first();
                $l6_name = $location_6_name->l6_name;
            }else{
                $l6_name = "ALL";
            }

             if(!empty($request->user))
            {
                $filter_user_name = DB::table('person')->select(DB::raw("GROUP_CONCAT(CONCAT_WS(' ',first_name,middle_name,last_name)) as user_name"))->where('company_id',$company_id)->whereIn('id',$request->user)->first();
                $filter_user = $filter_user_name->user_name;
            }else{
                $filter_user = "ALL";
            }

             if(!empty($request->role))
            {
                $filter_role_name = DB::table('_role')->select(DB::raw("GROUP_CONCAT(rolename) as role_name"))->where('company_id',$company_id)->whereIn('role_id',$request->role)->first();
                $role_name = $filter_role_name->role_name;
            }else{
                $role_name = "ALL";
            }

            // dd($role_name);


        $role_id=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50 || $this->without_junior == 0)
        {
           $datasenior='';
           $login_user=Auth::user()->id;
        }else
        { 
            
            Session::forget('juniordata');
            $login_user=Auth::user()->id;
             
            $datasenior_call=self::getJuniorUser($login_user);
            $datasenior = $request->session()->get('juniordata');
             if(empty($datasenior)){
                 $datasenior[]=$login_user;
                        }
        }

            $location_4_name =DB::table('person')
                        ->join('location_6', 'location_6.id', '=', 'person.town_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->pluck('location_4.name','person.id');
            $location_5_name = DB::table('person')
                        ->join('location_6', 'location_6.id', '=', 'person.town_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->pluck('location_5.name','person.id');
            $location_6_name =DB::table('person')
                        ->join('location_6', 'location_6.id', '=', 'person.town_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->pluck('location_6.name','person.id');


            $data1 = DB::table('person')
            ->join('users','users.id','=','person.id')
            ->join('person_login','person_login.person_id','=','person.id')
            ->join('person_details','person_details.person_id','=','person.id')
            ->join('_role','_role.role_id','=','person.role_id')
            // ->join('location_view','location_view.l3_id','=','person.state_id')
            ->join('location_3','location_3.id','=','person.state_id')
            ->join('location_2','location_2.id','=','location_3.location_2_id')
            ->join('location_1','location_1.id','=','location_2.location_1_id')

            
            ->where('is_admin','!=',1)
            ->where('person_status','=','1')
            // ->where('person_status','1')
            ->where('person.company_id',$company_id)
            ->whereRaw("person.id!=1")
            // ->select('created_on','person_status','deleted_deactivated_on',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'l2_name','l3_name','rolename AS role_name','emp_code','person.id AS user_id')
            ->select('person_status','deleted_deactivated_on','person.person_id_senior as senior_id',DB::raw("(select CONCAT_WS(' ',first_name,middle_name,last_name) from person where id = senior_id) as senior_name"),'person.mobile as mobile',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'location_2.name as l2_name','location_3.name as l3_name','rolename AS role_name','emp_code','person.id AS user_id','location_1.name as l1_name')
            ->groupBy('person.id')
            ->orderBy('user_name');
            if($login_user == 2833){
                $data1->whereNotIn('person.state_id',$array);		
             }
            if (!empty($datasenior)) 
            {
                $data1->whereIn('person.id', $datasenior);
            }
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data1->whereIn('location_2.id', $region);
            }

            #State filter
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $data1->whereIn('location_3.id', $location_3);
            }
            // if (!empty($request->location_4)) 
            // {
            //     $location_4 = $request->location_4;
            //     $data1->join('location_6', 'location_6.id', '=', 'person.town_id')
            //     ->join('location_5','location_5.id','=','location_6.location_5_id')
            //     ->join('location_4','location_4.id','=','location_5.location_4_id')
            //     ->whereIn('location_4.id', $location_4);
            // }
            // if (!empty($request->location_5)) 
            // {
            //     $location_5 = $request->location_5;
            //     $data1->join('location_6', 'location_6.id', '=', 'person.town_id')
            //             ->join('location_5','location_5.id','=','location_6.location_5_id')
            //             ->whereIn('location_5.id', $location_5);
            // }
            if (!empty($request->location_6) && !empty($request->location_5) && !empty($request->location_4)) 
            {
                $location_6 = $request->location_6;
                $location_4 = $request->location_4;
                $location_5 = $request->location_5;
                $data1->join('location_6', 'location_6.id', '=', 'person.town_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->whereIn('location_5.id', $location_5)
                        ->whereIn('location_4.id', $location_4)
                        ->whereIn('location_6.id', $location_6);
            }
            else
            {
                if (!empty($request->location_5) && !empty($request->location_4)) 
                {
                    $location_5 = $request->location_5;
                    $location_4 = $request->location_4;
                    $data1->join('location_6', 'location_6.id', '=', 'person.town_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->whereIn('location_5.id', $location_5)
                        ->whereIn('location_4.id', $location_4);
                }
                elseif(!empty($request->location_6) && !empty($request->location_4))
                {
                    $location_6 = $request->location_6;
                    $location_4 = $request->location_4;
                    $data1->join('location_6', 'location_6.id', '=', 'person.town_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        // ->whereIn('location_5.id', $location_5)
                        ->whereIn('location_4.id', $location_4)
                        ->whereIn('location_6.id', $location_6);
                }
                else
                {
                     if (!empty($request->location_6)) 
                    {
                        $location_6 = $request->location_6;
                        $data1->join('location_6', 'location_6.id', '=', 'person.town_id')
                                ->join('location_5','location_5.id','=','location_6.location_5_id')
                                ->whereIn('location_6.id', $location_6);
                    }
                    if (!empty($request->location_5)) 
                    {
                        $location_5 = $request->location_5;
                        $data1->join('location_6', 'location_6.id', '=', 'person.town_id')
                                ->join('location_5','location_5.id','=','location_6.location_5_id')
                                ->whereIn('location_5.id', $location_5);
                    }
                    if (!empty($request->location_4)) 
                    {
                        $location_4 = $request->location_4;
                        $data1->join('location_6', 'location_6.id', '=', 'person.town_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->whereIn('location_4.id', $location_4);
                    }
                }
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $data1->whereIn('person.role_id', $role);
            }
            
            #User filter
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $data1->whereIn('person.id', $user);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $data1->whereIn('location_3.id', $area);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data1->whereIn('person.id', $user);
            }
            $user_records_data = $data1->get()->toArray();





        $deactivedatadata = DB::table('person')
            ->join('users','users.id','=','person.id')
            ->join('person_login','person_login.person_id','=','person.id')
            ->join('person_details','person_details.person_id','=','person.id')
            ->join('_role','_role.role_id','=','person.role_id')
            // ->join('location_view','location_view.l3_id','=','person.state_id')
            ->join('location_3','location_3.id','=','person.state_id')
            ->join('location_2','location_2.id','=','location_3.location_2_id')
            ->join('location_1','location_1.id','=','location_2.location_1_id')
            ->where('person_status','!=','1')
            // ->where('person_status','1')
            ->where('person.company_id',$company_id)
            ->where('is_admin','!=',1)
            ->whereRaw("DATE_FORMAT(person_details.deleted_deactivated_on,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(person_details.deleted_deactivated_on,'%Y-%m-%d')<='$to_date'")
            // ->select('created_on','person_status','deleted_deactivated_on',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'l2_name','l3_name','rolename AS role_name','emp_code','person.id AS user_id')
            ->select('person_status','deleted_deactivated_on','person.person_id_senior as senior_id',DB::raw("(select CONCAT_WS(' ',first_name,middle_name,last_name) from person where id = senior_id) as senior_name"),'person.mobile as mobile',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'location_2.name as l2_name','location_3.name as l3_name','rolename AS role_name','emp_code','person.id AS user_id','location_1.name as l1_name')
            ->groupBy('person.id')
            ->orderBy('user_name');
              if (!empty($datasenior)) 
            {
                $deactivedatadata->whereIn('person.id', $datasenior);
            }
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $deactivedatadata->whereIn('location_2.id', $region);
            }

            #State filter
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $deactivedatadata->whereIn('location_3.id', $location_3);
            }
          
            if (!empty($request->location_6) && !empty($request->location_5) && !empty($request->location_4)) 
            {
                $location_6 = $request->location_6;
                $location_4 = $request->location_4;
                $location_5 = $request->location_5;
                $deactivedatadata->join('location_6', 'location_6.id', '=', 'person.town_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->whereIn('location_5.id', $location_5)
                        ->whereIn('location_4.id', $location_4)
                        ->whereIn('location_6.id', $location_6);
            }
            else
            {
                if (!empty($request->location_5) && !empty($request->location_4)) 
                {
                    $location_5 = $request->location_5;
                    $location_4 = $request->location_4;
                    $deactivedatadata->join('location_6', 'location_6.id', '=', 'person.town_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->whereIn('location_5.id', $location_5)
                        ->whereIn('location_4.id', $location_4);
                }
                elseif(!empty($request->location_6) && !empty($request->location_4))
                {
                    $location_6 = $request->location_6;
                    $location_4 = $request->location_4;
                    $deactivedatadata->join('location_6', 'location_6.id', '=', 'person.town_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        // ->whereIn('location_5.id', $location_5)
                        ->whereIn('location_4.id', $location_4)
                        ->whereIn('location_6.id', $location_6);
                }
                else
                {
                    if (!empty($request->location_5)) 
                    {
                        $location_5 = $request->location_5;
                        $deactivedatadata->join('location_6', 'location_6.id', '=', 'person.town_id')
                                ->join('location_5','location_5.id','=','location_6.location_5_id')
                                ->whereIn('location_5.id', $location_5);
                    }
                    if (!empty($request->location_4)) 
                    {
                        $location_4 = $request->location_4;
                        $deactivedatadata->join('location_6', 'location_6.id', '=', 'person.town_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->whereIn('location_4.id', $location_4);
                    }
                }
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $deactivedatadata->whereIn('person.role_id', $role);
            }
            
            #User filter
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $deactivedatadata->whereIn('person.id', $user);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $deactivedatadata->whereIn('location_3.id', $area);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $deactivedatadata->whereIn('person.id', $user);
            }
        $deactivedata = $deactivedatadata->get()->toArray();



            $user_records =  array_merge($user_records_data,$deactivedata);




            $user_record=[];
            foreach ($user_records as $key => $value) {
                $user_id=$value->user_id;
                $user_record[$user_id]['user_name']=$value->user_name;
                $user_record[$user_id]['user_id']=$value->user_id;
                $user_record[$user_id]['mobile']=$value->mobile;
                $user_record[$user_id]['senior_name']=$value->senior_name;
                $user_record[$user_id]['l2_name']=$value->l2_name;
                $user_record[$user_id]['l3_name']=$value->l3_name;
                $user_record[$user_id]['l1_name']=$value->l1_name;
                $user_record[$user_id]['emp_code']=$value->emp_code;
                $user_record[$user_id]['role_name']=$value->role_name;
                $user_record[$user_id]['status']=($value->person_status==1)?'Active':'De-Activated/Deleted';

                if($value->person_status==1){
                $user_record[$user_id]['date_de']='';
                }else{
                $user_record[$user_id]['date_de']=$value->deleted_deactivated_on;
                }
                


                // $user_record[$user_id]['data_cse']=$value->created_on;

                foreach ($datesArr as $keyd => $valued) {
                $user_record[$user_id][$keyd]=DB::table('user_daily_attendance')
                ->join('_working_status','user_daily_attendance.work_status','=','_working_status.id')
                ->where('user_id',$user_id)
                ->where('user_daily_attendance.company_id',$company_id)
                ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$valued'")
                ->select('_working_status.name AS work_status','_working_status.id')
                ->first();
                if(empty($user_record[$user_id][$keyd]))
                    $user_record[$user_id][$keyd]='';
                }
            }

            $working_status = DB::table('_working_status')->where('company_id',$company_id)->where('status',1)->pluck('name','id');
            $holiday = DB::table('holiday')->where('company_id',$company_id)->where('status',1)->pluck('name','date')->toArray();

            // dd($working_status);

            // dd($user_record);   
            if($company_id == 61){
                return view('reports.time-report.gurujiajax', [
                    'records' => $user_record,
                    'working_status' => $working_status,
                    // 'month' => $month,
                    'datesArr'=>$datesArr,
                    'datesDisplayArr'=>$datesDisplayArr,
                    'total_days'=>$total_days,
                    'holiday'=>$holiday,
                    'location_4_name' => $location_4_name,
                    'location_5_name' => $location_5_name,
                    'location_6_name' => $location_6_name,
                    'l3_name' => $l3_name,
                    'l4_name' => $l4_name,
                    'l5_name' => $l5_name,
                    'l6_name' => $l6_name,
                    'filter_user' => $filter_user,
                    'role_name' => $role_name,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'company_id' => $company_id,

                ]);
            }
            elseif($company_id == 56)
            {
                $data1 = DB::table('ramanujan_attaendance_sheet')
                ->groupBy('user_name')
                ->orderBy('user_name');
                if($login_user == 2833){
                    $data1->whereNotIn('person.state_id',$array);       
                 }
                if (!empty($datasenior)) 
                {
                    $data1->whereIn('person.id', $datasenior);
                }
               
                $user_records = $data1->get();
                $user_record=[];
                foreach ($user_records as $key => $value) {
                    $user_id=$value->user_name;
                    $user_record[$user_id]['user_name']=$value->user_name;
                    $user_record[$user_id]['user_action']=$value->user_action;
            

                    foreach ($datesArr as $keyd => $valued) {
                    $user_record[$user_id][$keyd]=DB::table('ramanujan_attaendance_sheet')
                    ->join('_working_status','ramanujan_attaendance_sheet.status','=','_working_status.id')
                    ->where('user_name',$user_id)
                    ->where('_working_status.company_id',$company_id)
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$valued'")
                    ->select('_working_status.name AS work_status','_working_status.id')
                    ->first();
                    if(empty($user_record[$user_id][$keyd]))
                        $user_record[$user_id][$keyd]='';
                    }
                }
                return view('reports.time-report.ramanujanAjax', [
                    'records' => $user_record,
                    'working_status' => $working_status,
                    // 'month' => $month,
                    'datesArr'=>$datesArr,
                    'datesDisplayArr'=>$datesDisplayArr,
                    'total_days'=>$total_days,
                    'location_4_name' => $location_4_name,
                    'location_5_name' => $location_5_name,
                    'location_6_name' => $location_6_name,
                    'company_id' => $company_id,

                ]);
            }

            else{
            return view('reports.time-report.ajax', [
                'records' => $user_record,
                'working_status' => $working_status,
                // 'month' => $month,
                'datesArr'=>$datesArr,
                'datesDisplayArr'=>$datesDisplayArr,
                'total_days'=>$total_days,
                'location_4_name' => $location_4_name,
                'location_5_name' => $location_5_name,
                'location_6_name' => $location_6_name,
                    'company_id' => $company_id,
                
            ]);
            }

        }
    }

    

    public function timeAttdBtwReport(Request $request)
    {
        if ($request->ajax() && !empty($request->month)) {
            $company_id = Auth::user()->company_id;
            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            // dd($request);
            $month = $request->month;
           // $month='2019-01';
            $m1=explode('-', $month);
            $y=$m1[0];
            $m2=$m1[1];
            if($m2<10)
            $m=ltrim($m2, '0');
            else
            $m=$m2;

            $total_days=cal_days_in_month(CAL_GREGORIAN,$m,$y);
            $monthName=array('01' =>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');

            for($i = 1; $i <=  $total_days; $i++)
        {
        // add the date to the dates array
        $datesArr[] = $y . "-" . $m2 . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
        $datesDisplayArr[] =str_pad($i, 2, '0', STR_PAD_LEFT)."-".$monthName[$m2]."-".$y ;
        }
            $data1 = DB::table('person')
            ->join('users','users.id','=','person.id')
            ->join('person_login','person_login.person_id','=','person.id')
            ->join('person_details','person_details.person_id','=','person.id')
            ->join('_role','_role.role_id','=','person.role_id')
            ->join('location_3','location_3.id','=','person.state_id')
            ->join('location_2','location_2.id','=','location_3.location_2_id')

            ->join('location_6', 'location_6.id', '=', 'person.town_id')
            ->join('location_5','location_5.id','=','location_6.location_5_id')
            ->join('location_4','location_4.id','=','location_5.location_4_id')

            ->where('person_status','=','1') 
            ->where('person.company_id',$company_id)
            ->where('is_admin','!=','1')
            ->select('person_status','deleted_deactivated_on',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'location_2.name as l2_name','location_3.name as l3_name','rolename AS role_name','emp_code','person.id AS user_id','person.mobile','person.email','person.person_id_senior')
            ->groupBy('person.id')
            ->orderBy('user_name');

            #State filter
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $data1->whereIn('location_3.id', $location_3);
            }
              if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $data1->whereIn('location_4.id', $location_4);
            }
              if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $data1->whereIn('location_5.id', $location_5);
            }
              if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $data1->whereIn('location_6.id', $location_6);
            }
         
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $data1->whereIn('person.role_id', $role);
            }
            
            #User filter
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $data1->whereIn('person.id', $user);
            }

            // #Region filter
            // if (!empty($request->region)) {
            //     $region = $request->region;
            //     $data1->whereIn('location_2.id', $region);
            // }
            // #State filter
            // if (!empty($request->area)) {
            //     $area = $request->area;
            //     $data1->whereIn('location_3.id', $area);
            // }
            // #User filter
            // if (!empty($request->user)) {
            //     $user = $request->user;
            //     $data1->whereIn('person.id', $user);
            // }
            $user_records_data = $data1->get()->toArray();



                 $deactivedata = DB::table('person')
            ->join('users','users.id','=','person.id')
            ->join('person_login','person_login.person_id','=','person.id')
            ->join('person_details','person_details.person_id','=','person.id')
            ->join('_role','_role.role_id','=','person.role_id')
            // ->join('location_view','location_view.l3_id','=','person.state_id')
            ->join('location_3','location_3.id','=','person.state_id')
            ->join('location_2','location_2.id','=','location_3.location_2_id')
            ->join('location_1','location_1.id','=','location_2.location_1_id')

            

            ->where('person_status','!=','1')
            // ->where('person_status','1')
            ->where('person.company_id',$company_id)
            ->where('is_admin','!=','1')
            ->whereRaw("DATE_FORMAT(person_details.deleted_deactivated_on,'%Y-%m')='$month'")
            // ->select('created_on','person_status','deleted_deactivated_on',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'l2_name','l3_name','rolename AS role_name','emp_code','person.id AS user_id')
            ->select('person_status','deleted_deactivated_on','person.person_id_senior as senior_id',DB::raw("(select CONCAT_WS(' ',first_name,middle_name,last_name) from person where id = senior_id) as senior_name"),'person.mobile as mobile','person.email',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'location_2.name as l2_name','location_3.name as l3_name','rolename AS role_name','emp_code','person.id AS user_id','location_1.name as l1_name')
            ->groupBy('person.id')
            ->orderBy('user_name')
            ->get()->toArray();



            $user_records =  array_merge($user_records_data,$deactivedata);




            // dd($user_records);
            $user_record=[];
            foreach ($user_records as $key => $value) {
                $user_id=$value->user_id;
                $user_record[$user_id]['user_name']=$value->user_name;
                $user_record[$user_id]['user_id']=$value->user_id;
                $user_record[$user_id]['l2_name']=$value->l2_name;
                $user_record[$user_id]['l3_name']=$value->l3_name;
                $user_record[$user_id]['emp_code']=$value->emp_code;
                $user_record[$user_id]['role_name']=$value->role_name;
                $user_record[$user_id]['mobile']=$value->mobile;
                $user_record[$user_id]['email']=!empty($value->email)?$value->email:'';
                $user_record[$user_id]['person_id_senior']=!empty($value->person_id_senior)?$value->person_id_senior:'';

                   $user_record[$user_id]['status']=($value->person_status==1)?'Active':'De-Activated/Deleted';

                if($value->person_status==1){
                $user_record[$user_id]['date_de']='';
                }else{
                $user_record[$user_id]['date_de']=$value->deleted_deactivated_on;
                }

                foreach ($datesArr as $keyd => $valued) {
                $user_record[$user_id][$keyd]=DB::table('user_daily_attendance')
                ->join('_working_status','user_daily_attendance.work_status','=','_working_status.id')
                ->where('user_id',$user_id)
                ->where('user_daily_attendance.company_id',$company_id)
                ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$valued'")
                 ->select('_working_status.name AS work_status','_working_status.id',DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as check_in"),DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d') as check_in_date"),DB::raw("(SELECT DATE_FORMAT(work_date,'%H:%i:%s') from check_out where company_id = $company_id and user_id = $user_id and DATE_FORMAT(work_date,'%Y-%m-%d') = '$valued') as check_out "))
                ->first();
                if(empty($user_record[$user_id][$keyd]))
                    $user_record[$user_id][$keyd]='';
                }
            }

            $working_status = DB::table('_working_status')->where('company_id',$company_id)->where('status',1)->whereNotIn('id',[89,91,92,94,97])->pluck('name','id');

            $senior = DB::table('person')->where('company_id',$company_id)->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'person.id');

            $senior_role = DB::table('person')->join('_role','_role.role_id','=','person.role_id')->where('person.company_id',$company_id)->pluck('rolename','person.id');

            // dd($working_status);

            // dd($user_record);   
            return view('reports.time-report.btwajax', [
                'records' => $user_record,
                'working_status' => $working_status,
                'month' => $month,
                'datesArr'=>$datesArr,
                'datesDisplayArr'=>$datesDisplayArr,
                'total_days'=>$total_days,
                'senior'=>$senior,
                'senior_role'=>$senior_role,
            ]);

        }
    }


    public function dailyAttendanceReportBtw(Request $request)
    {
        if ($request->ajax() && !empty($request->date_range_picker)) {

            $explodeDate = explode(" -", $request->date_range_picker);
             $company_id = Auth::user()->company_id;
            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $checkoutarr =[];

           
            $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50)
            {
               $datasenior='';
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                 
                $datasenior_call=self::getJuniorUser($login_user);
                $datasenior = $request->session()->get('juniordata');
                 if(empty($datasenior)){
                     $datasenior[]=$login_user;
                            }
            }
            $data1 = UserDetail::join('person_login','person_login.person_id','=','person.id')
                ->join('users','users.id','=','person.id')
                // ->join('location_view', 'location_view.l3_id', '=', 'person.state_id')
                ->join('location_3', 'location_3.id', '=', 'person.state_id')
                ->join('location_2','location_2.id','=','location_3.location_2_id')
                ->join('location_1','location_1.id','=','location_2.location_1_id')

                ->join('location_6', 'location_6.id', '=', 'person.town_id')
                ->join('location_5','location_5.id','=','location_6.location_5_id')
                ->join('location_4','location_4.id','=','location_5.location_4_id')

                ->join('_role', '_role.role_id', '=', 'person.role_id')
                ->select('person.person_id_senior as senior_id','person.mobile as mobile','person.id as person_id','location_3.name as l3_name as region_txt', 'person.emp_code as emp_code',DB::raw("(select CONCAT_WS(' ',first_name,middle_name,last_name) from person where id = senior_id) as senior_name"), DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as uname"), 'location_1.name as zone', 'location_2.name as region', '_role.rolename as role')
                ->distinct('person.id')
                ->where('is_admin', '!=', 1)
                ->where('person.company_id',$company_id)
                ->where('person_login.person_status','=', 1);
          #Junior filter
            if (!empty($datasenior)) 
            {
                $data1->whereIn('person.id', $datasenior);
            }
         
                #Region filter
            if (!empty($request->region)) 
            {
                $region = $request->region;
                $data1->whereIn('location_2.id', $region);
            }
            #State filter
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $data1->whereIn('location_3.id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $data1->whereIn('location_4.id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $data1->whereIn('location_5.id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $data1->whereIn('location_6.id', $location_6);
            }
            if (!empty($request->location_7)) 
            {
                $location_7 = $request->location_7;
                $data1->whereIn('location_7.id', $location_7);
            }
            #User filter
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $data1->whereIn('person.id', $user);
            }
            $user_record = $data1->get();

            // dd($user_record);

            $mtp_towm_data = DB::table('monthly_tour_program')->join('location_view','location_view.l6_id','=','monthly_tour_program.town') ->where('monthly_tour_program.company_id',$company_id)->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(working_date,'%Y-%m-%d') <='$to_date'")->groupBy('person_id','working_date');
            if (!empty($datasenior)) 
            {
                $mtp_towm_data->whereIn('person_id', $datasenior);
            }
         
                #Region filter
            if (!empty($request->region)) 
            {
                $region = $request->region;
                $mtp_towm_data->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) 
            {
                $area = $request->area;
                $mtp_towm_data->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $mtp_towm_data->whereIn('person_id', $user);
            }
            $mtp_towm = $mtp_towm_data->pluck('l6_name as l4_name',DB::raw("CONCAT(person_id,working_date)"));
      
          
                    
            $sale_value = DB::table('secondary_sale')->where('company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(date,'%Y-%m-%d') <='$to_date'")->groupBy('user_id','date')->pluck(DB::raw("SUM(rate*quantity) as totale_sale_value"),DB::raw("CONCAT(user_id,date)"));                   
            $productive_calls = DB::table('user_sales_order')->where('company_id',$company_id)->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(date,'%Y-%m-%d') <='$to_date'")->groupBy('user_id','date')->where('call_status','1')->pluck(DB::raw('COUNT(DISTINCT order_id) as count'),DB::raw("CONCAT(user_id,date)"));

            $checkout=DB::table('check_out')
            ->join('person','person.id','=','check_out.user_id')
            ->where('check_out.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(work_date,'%Y-%m-%d') <='$to_date'")
            ->groupBy('work_date','user_id')
            ->select('work_date',DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d')) as concat"),'total_call as tc','total_pc as tpc','total_sale_value as tsv')
            ->get();
            
            foreach ($checkout as $checkout_data => $checkout_value) 
            {
            $concat = $checkout_value->concat;
            $checkoutarr[$concat]['tc'] = $checkout_value->tc;
            $checkoutarr[$concat]['tpc'] = $checkout_value->tpc;
            $checkoutarr[$concat]['tsv'] = $checkout_value->tsv;
            }


            $queryQ = DB::table('daily_attendance_view')->join('person','person.id','=','daily_attendance_view.user_id')
             ->join('location_3', 'location_3.id', '=', 'person.state_id')
             ->where('person.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(daily_attendance_view.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(daily_attendance_view.work_date,'%Y-%m-%d') <='$to_date'");
              #User filter
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $queryQ->whereIn('user_id', $user);
            }
             if (!empty($datasenior)) 
            {
                $queryQ->whereIn('user_id', $datasenior);
            }
                #Region filter
            if (!empty($request->region)) 
            {
                $region = $request->region;
                $queryQ->whereIn('location_3.location_2_id', $region);
            }
            #State filter
            if (!empty($request->area)) 
            {
                $area = $request->area;
                $queryQ->whereIn('location_3.id', $area);
            }

            $query=$queryQ->groupBy('work_date','user_id','track_addrs','work','check_out_date','image_name')
            ->get();
            // dd($query);
            $arr=[];
            //$productive_calls='';
            $outuser_idrv='';
            $firstCallData='';
            $lastCallData='';
            $first_call = DB::table('user_sales_order')->where('company_id',$company_id)->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') <='$to_date'")->groupBy('date','user_id')->pluck(DB::raw("MIN(time)"),DB::raw("CONCAT(date,user_id)"));
            $last_call = DB::table('user_sales_order')->where('company_id',$company_id)->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') <='$to_date'")->groupBy('date','user_id')->pluck(DB::raw("MAX(time)"),DB::raw("CONCAT(date,user_id)"));
   
            foreach ($query as $k=>$q)
            {

                $date=!empty($q->work_date)?date('Y-m-d',strtotime($q->work_date)):0;
                $arr[$date][$q->user_id]=$q;
               
               
            }
            $weekly_off_query = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->where('person.company_id',$company_id)
                                ->pluck(DB::raw("DATE_FORMAT(weekly_off_data,'%Y-%m-%d') as date"),DB::raw("CONCAT(person.id,DATE_FORMAT(weekly_off_data,'%Y-%m-%d')) as data"));
// dd($arr);
            return view('reports.daily-attendance.btwajax', [
                'records' => $arr,
                'users' => $user_record,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'mtp_towm'=>$mtp_towm,
                'weekly_off_query' => $weekly_off_query,
                'productive_calls'=>$productive_calls,
                'checkoutarr' => $checkoutarr,
                'sale_value'=>$sale_value,
                'first_call'=>$first_call,
                'last_call' =>$last_call,
            ]);

        }
    }


    public function ssMonthlyTargetReport(Request $request)
    {
        if ($request->ajax() && !empty($request->month)) {
            $company_id = Auth::user()->company_id;
            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $month = $request->month;
           // $month='2019-01';
            $m1=explode('-', $month);
            $y=$m1[0];
            $m2=$m1[1];
            if($m2<10)
            $m=ltrim($m2, '0');
            else
            $m=$m2;

            $total_days=cal_days_in_month(CAL_GREGORIAN,$m,$y);
            $monthName=array('01' =>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');

            for($i = 1; $i <=  $total_days; $i++)
        {
        // add the date to the dates array
        $datesArr[] = $y . "-" . $m2 . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
        $datesDisplayArr[] =str_pad($i, 2, '0', STR_PAD_LEFT)."-".$monthName[$m2]."-".$y ;
        }

        $data1 = DB::table('master_target')
                 ->join('csa','csa.c_id','=','master_target.csa_id')
                 ->join('location_3','location_3.id','=','csa.state_id')
                 ->where('master_target.company_id',$company_id)
                 ->whereRaw("DATE_FORMAT(from_date,'%Y-%m')='$month'")
                 ->select('location_3.name as state','csa.csa_name',DB::raw("SUM(quantity_cases) as quantity_cases"),'csa.c_id as csa_id')
                 ->groupBy('master_target.csa_id');  
                 
                  if (!empty($request->region)) {
                        $region = $request->region;
                        $data1->whereIn('location_2.id', $region);
                    }
                    #State filter
                    if (!empty($request->area)) {
                        $area = $request->area;
                        $data1->whereIn('location_3.id', $area);
                    }
                    #User filter
                    if (!empty($request->user)) {
                        $user = $request->user;
                        $data1->whereIn('person.id', $user);
                    } 

                     $user_record = $data1->get();


        $date_wise_achievement = DB::table('ss_user_primary_sales_order')
                                ->join('ss_user_primary_sales_order_details','ss_user_primary_sales_order_details.order_id','=','ss_user_primary_sales_order.order_id')
                                ->join('catalog_product','catalog_product.id','=','ss_user_primary_sales_order_details.product_id')
                                ->where('ss_user_primary_sales_order.company_id',$company_id) 
                                ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m')='$month'")
                                ->groupBy('dealer_id','sale_date') // remove product_id when consult with android team data insert multiple
                                ->pluck(DB::raw("SUM(cases) as quantity_cases"),DB::raw("CONCAT(dealer_id,sale_date)"));


        $ss_district = DB::table('csa')
                    //    ->join('csa_location_5','csa_location_5.csa_id','=','csa.c_id')
                    //    ->join('location_5','location_5.id','=','csa_location_5.csa_location_5_id')
                       ->join('location_view','location_view.l3_id','=','csa.state_id') 
                       ->where('csa.company_id',$company_id)
                       ->groupBy('csa.c_id')
                       ->pluck(DB::raw("group_concat(distinct(l5_name))"),'csa.c_id');    


         $user_name = DB::table('person')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                        ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                        ->join('csa','c_id','=','dealer.csa_id')
                        ->where('csa.company_id',$company_id)
                        ->where('person.role_id',196)
                        ->groupBy('csa.c_id')
                        ->pluck(DB::raw("group_concat(distinct(first_name))"),'csa.c_id');    


          $mtd_target_amount = DB::table('master_target')
                           ->join('csa','csa.c_id','=','master_target.csa_id')
                           ->join('product_rate_list','product_rate_list.product_id','=','master_target.product_id')
                            ->where('master_target.company_id',$company_id)
                            ->where('flag',2) 
                             ->whereRaw("DATE_FORMAT(master_target.from_date, '%Y-%m') = '$month'")
                             ->groupBy('csa.c_id')
                             ->pluck(DB::raw("sum((quantity_cases)*(product_rate_list.ss_case_rate)) as total"),'csa.c_id');


           $mtd_achievement_amount = DB::table('ss_user_primary_sales_order')
                                    ->join('ss_user_primary_sales_order_details','ss_user_primary_sales_order_details.order_id','=','ss_user_primary_sales_order.order_id')
                                    ->where('ss_user_primary_sales_order.company_id',$company_id) 
                                    ->whereRaw("DATE_FORMAT(ss_user_primary_sales_order.sale_date, '%Y-%m') = '$month'")
                                    ->groupBy('dealer_id')
                                    ->pluck(DB::raw("sum((cases)*(pr_rate)) as total"),'dealer_id');



            // dd($user_record);   
            return view('reports.ss-monthly.ajax', [
                'records' => $user_record,
                'month' => $month,
                'datesArr'=>$datesArr,
                'datesDisplayArr'=>$datesDisplayArr,
                'total_days'=>$total_days,
                'date_wise_achievement'=>$date_wise_achievement,
                'ss_district'=>$ss_district,
                'user_name'=>$user_name,
                'mtd_target_amount'=>$mtd_target_amount,
                'mtd_achievement_amount'=>$mtd_achievement_amount,
            ]);

        }
    }



     public function distributorMonthlyTargetReport(Request $request)
    {
        if ($request->ajax() && !empty($request->month)) {
            $company_id = Auth::user()->company_id;
            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $month = $request->month;
           // $month='2019-01';
            $m1=explode('-', $month);
            $y=$m1[0];
            $m2=$m1[1];
            if($m2<10)
            $m=ltrim($m2, '0');
            else
            $m=$m2;

            $total_days=cal_days_in_month(CAL_GREGORIAN,$m,$y);
            $monthName=array('01' =>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');

            for($i = 1; $i <=  $total_days; $i++)
        {
        // add the date to the dates array
        $datesArr[] = $y . "-" . $m2 . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
        $datesDisplayArr[] =str_pad($i, 2, '0', STR_PAD_LEFT)."-".$monthName[$m2]."-".$y ;
        }

        $data1 = DB::table('master_target')
                 ->join('dealer','dealer.id','=','master_target.distributor_id')
                 ->join('csa','csa.c_id','=','dealer.csa_id')
                 ->join('location_3','location_3.id','=','dealer.state_id')
                 ->where('master_target.company_id',$company_id)
                 ->whereRaw("DATE_FORMAT(from_date,'%Y-%m')='$month'")
                 ->select('location_3.name as state','dealer.name as dealer_name',DB::raw("SUM(quantity_cases) as quantity_cases"),'dealer.id as dealer_id','csa.csa_name')
                 ->groupBy('master_target.distributor_id');  
                 
                  if (!empty($request->region)) {
                        $region = $request->region;
                        $data1->whereIn('location_2.id', $region);
                    }
                    #State filter
                    if (!empty($request->area)) {
                        $area = $request->area;
                        $data1->whereIn('location_3.id', $area);
                    }
                    #User filter
                    if (!empty($request->user)) {
                        $user = $request->user;
                        $data1->whereIn('person.id', $user);
                    } 

                     $user_record = $data1->get();


            $so_user_name = DB::table('person')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                        ->where('person.company_id',$company_id)
                        // ->where('person.role_id',198)
                        ->groupBy('dealer_location_rate_list.dealer_id')
                        ->pluck(DB::raw("group_concat(distinct(first_name))"),'dealer_location_rate_list.dealer_id');




           // $ado_user_name = DB::table('person')
           //          ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
           //          ->where('person.company_id',$company_id)
           //          ->where('person.role_id',140)
           //          ->groupBy('dealer_location_rate_list.dealer_id')
           //          ->pluck(DB::raw("group_concat(distinct(first_name))"),'dealer_location_rate_list.dealer_id');  




        $date_wise_achievement = DB::table('user_primary_sales_order')
                                ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                                ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                                ->where('user_primary_sales_order.company_id',$company_id) 
                                ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m')='$month'")
                                ->groupBy('dealer_id','sale_date') // remove product_id when consult with android team data insert multiple
                                ->pluck(DB::raw("SUM(cases) as quantity_cases"),DB::raw("CONCAT(dealer_id,sale_date)"));


       


       


          $mtd_target_amount = DB::table('master_target')
                           ->join('dealer','dealer.id','=','master_target.distributor_id')
                           ->join('product_rate_list','product_rate_list.product_id','=','master_target.product_id')
                            ->where('master_target.company_id',$company_id)
                            ->where('flag',2) 
                             ->whereRaw("DATE_FORMAT(master_target.from_date, '%Y-%m') = '$month'")
                             ->groupBy('dealer.id')
                             ->pluck(DB::raw("sum((quantity_cases)*(product_rate_list.ss_case_rate)) as total"),'dealer.id');




           $mtd_achievement_amount = DB::table('user_primary_sales_order')
                                    ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                                    ->where('user_primary_sales_order.company_id',$company_id) 
                                    ->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date, '%Y-%m') = '$month'")
                                    ->groupBy('dealer_id')
                                    ->pluck(DB::raw("sum((cases)*(pr_rate)) as total"),'dealer_id');

                                    


            // dd($user_record);   
            return view('reports.distributor-monthly.ajax', [
                'records' => $user_record,
                'month' => $month,
                'datesArr'=>$datesArr,
                'datesDisplayArr'=>$datesDisplayArr,
                'total_days'=>$total_days,
                'date_wise_achievement'=>$date_wise_achievement,
                'so_user_name'=>$so_user_name,
                // 'ado_user_name'=>$ado_user_name,
                'mtd_target_amount'=>$mtd_target_amount,
                'mtd_achievement_amount'=>$mtd_achievement_amount,
            ]);

        }
    }


    public function dailyAttendanceReportOyster(Request $request)
    {
        if ($request->ajax() && !empty($request->date_range_picker)) {

            $explodeDate = explode(" -", $request->date_range_picker);
             $company_id = Auth::user()->company_id;
            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $checkoutarr =[];
		    $array = array(99,100,101,102);


           
            $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50)
            {
               $datasenior='';
               $login_user=Auth::user()->id;
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                 
                $datasenior_call=self::getJuniorUser($login_user);
                Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                 if(empty($datasenior)){
                     $datasenior[]=$login_user;
                            }
            }
            $data1 = UserDetail::join('person_login','person_login.person_id','=','person.id')
                ->join('users','users.id','=','person.id')
                ->join('location_3', 'location_3.id', '=', 'person.state_id')
                ->join('location_2','location_2.id','=','location_3.location_2_id')
                ->join('location_1','location_1.id','=','location_2.location_1_id')

                ->join('location_6', 'location_6.id', '=', 'person.town_id')
                ->join('location_5','location_5.id','=','location_6.location_5_id')
                ->join('location_4','location_4.id','=','location_5.location_4_id')


                ->join('_role', '_role.role_id', '=', 'person.role_id')
                ->select('person.person_id_senior as senior_id','person.mobile as mobile','person.id as person_id','location_3.name as region_txt', 'person.emp_code as emp_code',DB::raw("(select CONCAT_WS(' ',first_name,middle_name,last_name) from person where id = senior_id) as senior_name"), DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as uname"), 'location_1.name as l1_name as zone', 'location_2.name as l2_name as region', '_role.rolename as role')
                ->distinct('person.id')
                ->where('is_admin', '!=', 1)
                ->where('person.company_id',$company_id)
                ->where('person_login.person_status','=', 1);
          #Junior filter
            if (!empty($datasenior)) 
            {
                $data1->whereIn('person.id', $datasenior);
            }
         
                #Region filter
            if (!empty($request->region)) 
            {
                $region = $request->region;
                $data1->whereIn('location_2.id', $region);
            }
            #State filter
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $data1->whereIn('location_3.id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $data1->whereIn('location_4.id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $data1->whereIn('location_5.id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $data1->whereIn('location_6.id', $location_6);
            }
            // if (!empty($request->location_7)) 
            // {
            //     $location_7 = $request->location_7;
            //     $data1->whereIn('location_7.id', $location_7);
            // }
            #User filter
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $data1->whereIn('person.id', $user);
            }
            if($login_user == 2833){
                $data1->whereNotIn('person.state_id',$array);		
             }
            $user_record = $data1->get();

            // dd($user_record);

            $mtp_towm_data = DB::table('monthly_tour_program')->join('location_view','location_view.l6_id','=','monthly_tour_program.town') ->where('monthly_tour_program.company_id',$company_id)->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(working_date,'%Y-%m-%d') <='$to_date'")->groupBy('person_id','working_date');
            if (!empty($datasenior)) 
            {
                $mtp_towm_data->whereIn('person_id', $datasenior);
            }
         
                #Region filter
            if (!empty($request->region)) 
            {
                $region = $request->region;
                $mtp_towm_data->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) 
            {
                $area = $request->area;
                $mtp_towm_data->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $mtp_towm_data->whereIn('person_id', $user);
            }
            $mtp_towm = $mtp_towm_data->pluck('l6_name as l4_name',DB::raw("CONCAT(person_id,working_date)"));

            $mtp_beat_data = DB::table('monthly_tour_program')->join('location_view','location_view.l7_id','=','monthly_tour_program.locations') ->where('monthly_tour_program.company_id',$company_id)->whereRaw("DATE_FORMAT(working_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(working_date,'%Y-%m-%d') <='$to_date'")->groupBy('person_id','working_date');
            if (!empty($datasenior)) 
            {
                $mtp_beat_data->whereIn('person_id', $datasenior);
            }
         
                #Region filter
            if (!empty($request->region)) 
            {
                $region = $request->region;
                $mtp_beat_data->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) 
            {
                $area = $request->area;
                $mtp_beat_data->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $mtp_beat_data->whereIn('person_id', $user);
            }
            $mtp_beat = $mtp_beat_data->pluck('l7_name as l7_name',DB::raw("CONCAT(person_id,working_date)"));

      
          
                    
            $sale_value = DB::table('secondary_sale')->where('company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(date,'%Y-%m-%d') <='$to_date'")->groupBy('user_id','date')->pluck(DB::raw("SUM(rate*quantity) as totale_sale_value"),DB::raw("CONCAT(user_id,date)"));                   
            $productive_calls = DB::table('user_sales_order')->where('company_id',$company_id)->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(date,'%Y-%m-%d') <='$to_date'")->groupBy('user_id','date')->where('call_status','1')->pluck(DB::raw('COUNT(DISTINCT order_id) as count'),DB::raw("CONCAT(user_id,date)"));

            $checkout=DB::table('check_out')
            ->join('person','person.id','=','check_out.user_id')
            ->where('check_out.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(work_date,'%Y-%m-%d') <='$to_date'")
            ->groupBy('work_date','user_id')
            ->select('work_date',DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d')) as concat"),'total_call as tc','total_pc as tpc','total_sale_value as tsv')
            ->get();
            
            foreach ($checkout as $checkout_data => $checkout_value) 
            {
            $concat = $checkout_value->concat;
            $checkoutarr[$concat]['tc'] = $checkout_value->tc;
            $checkoutarr[$concat]['tpc'] = $checkout_value->tpc;
            $checkoutarr[$concat]['tsv'] = $checkout_value->tsv;
            }


            $queryQ = DB::table('daily_attendance_view')->join('person','person.id','=','daily_attendance_view.user_id')
             ->join('location_3', 'location_3.id', '=', 'person.state_id')
             ->where('person.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(daily_attendance_view.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(daily_attendance_view.work_date,'%Y-%m-%d') <='$to_date'");
              #User filter
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $queryQ->whereIn('user_id', $user);
            }
             if (!empty($datasenior)) 
            {
                $queryQ->whereIn('user_id', $datasenior);
            }
                #Region filter
            if (!empty($request->region)) 
            {
                $region = $request->region;
                $queryQ->whereIn('location_3.location_2_id', $region);
            }
            #State filter
            if (!empty($request->area)) 
            {
                $area = $request->area;
                $queryQ->whereIn('location_3.id', $area);
            }

            $query=$queryQ->groupBy('work_date','user_id','track_addrs','work','check_out_date','image_name')
            ->get();
            // dd($query);
            $arr=[];
            //$productive_calls='';
            $outuser_idrv='';
            $firstCallData='';
            $lastCallData='';
            $first_call = DB::table('user_sales_order')->where('company_id',$company_id)->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') <='$to_date'")->groupBy('date','user_id')->pluck(DB::raw("MIN(time)"),DB::raw("CONCAT(date,user_id)"));
            $last_call = DB::table('user_sales_order')->where('company_id',$company_id)->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') <='$to_date'")->groupBy('date','user_id')->pluck(DB::raw("MAX(time)"),DB::raw("CONCAT(date,user_id)"));
   
            foreach ($query as $k=>$q)
            {

                $date=!empty($q->work_date)?date('Y-m-d',strtotime($q->work_date)):0;
                $arr[$date][$q->user_id]=$q;
               
               
            }
            $weekly_off_query = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->where('person.company_id',$company_id)
                                ->pluck(DB::raw("DATE_FORMAT(weekly_off_data,'%Y-%m-%d') as date"),DB::raw("CONCAT(person.id,DATE_FORMAT(weekly_off_data,'%Y-%m-%d')) as data"));
// dd($arr);
            return view('reports.daily-attendance.oysterajax', [
                'records' => $arr,
                'users' => $user_record,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'mtp_towm'=>$mtp_towm,
                'weekly_off_query' => $weekly_off_query,
                'productive_calls'=>$productive_calls,
                'checkoutarr' => $checkoutarr,
                'sale_value'=>$sale_value,
                'first_call'=>$first_call,
                'last_call' =>$last_call,
                'mtp_beat'=> $mtp_beat,
            ]);

        }
    }


    public function dailyAttendancePatanjaliReport(Request $request)
    {
        if ($request->ajax() && !empty($request->date_range_picker)) {

            $explodeDate = explode(" -", $request->date_range_picker);
             $company_id = Auth::user()->company_id;
            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

            $checkoutarr =[];

          

            // dd($btwdatasenior);
           
            $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50)
            {
               $datasenior='';
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                 
                $datasenior_call=self::getJuniorUser($login_user);
                $datasenior = $request->session()->get('juniordata');
                 if(empty($datasenior)){
                     $datasenior[]=$login_user;
                            }
            }

            $data1 = DB::table('user_daily_attendance')
                    ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                    ->join('person','person.id','=','user_daily_attendance.user_id')
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->join('person_details','person_details.person_id','=','person.id')
                    ->join('users','users.id','=','person.id')
                    ->join('location_3', 'location_3.id', '=', 'person.state_id')
                    ->join('location_2','location_2.id','=','location_3.location_2_id')
                    ->join('location_1','location_1.id','=','location_2.location_1_id')
                    ->join('location_6', 'location_6.id', '=', 'person.town_id')
                    ->join('location_5','location_5.id','=','location_6.location_5_id')
                    ->join('location_4','location_4.id','=','location_5.location_4_id')
                    ->join('_role', '_role.role_id', '=', 'person.role_id')
                    ->select('user_daily_attendance.server_date',DB::raw("DATE_FORMAT(work_date,'%Y-%m-%d') as work_date"),DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as work_time"), DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as uname"),'rolename','_working_status.name as work_status','user_daily_attendance.working_with','person_details.address','user_daily_attendance.track_addrs','user_daily_attendance.remarks','user_daily_attendance.image_name','person.id as user_id','person.person_id_senior as senior_id','location_3.name as l3_name','location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name')
                    ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(work_date,'%Y-%m-%d') <='$to_date'")
                    ->where('person.company_id',$company_id)
                    ->where('user_daily_attendance.company_id',$company_id)
                    ->where('person_login.company_id',$company_id)
                    ->where('location_3.company_id',$company_id)
                    ->where('person_login.person_status','=', 1);
                       #Junior filter
            if (!empty($datasenior)) 
            {
                $data1->whereIn('person.id', $datasenior);
            }
         
                #Region filter
            if (!empty($request->region)) 
            {
                $region = $request->region;
                $data1->whereIn('location_2.id', $region);
            }
            #State filter
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $data1->whereIn('location_3.id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $data1->whereIn('location_4.id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $data1->whereIn('location_5.id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $data1->whereIn('location_6.id', $location_6);
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $data1->whereIn('person.role_id', $role);
            }
            // if (!empty($request->location_7)) 
            // {
            //     $location_7 = $request->location_7;
            //     $data1->whereIn('location_7.id', $location_7);
            // }
            #User filter
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $data1->whereIn('person.id', $user);
            }
            $user_record = $data1->groupBy('user_daily_attendance.work_date','user_daily_attendance.user_id')->get();

            // dd($user_record);



         
           

      
          
          

            $checkout=DB::table('check_out')
            ->join('person','person.id','=','check_out.user_id')
            ->where('check_out.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(work_date,'%Y-%m-%d') <='$to_date'")
            ->groupBy('work_date','user_id')
            ->select(DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as work_time"),DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d')) as concat"),'check_out.attn_address','check_out.remarks','check_out.image_name')
            ->get();
            
            foreach ($checkout as $checkout_data => $checkout_value) 
            {
            $concat = $checkout_value->concat;
            $checkoutarr[$concat]['work_time'] = $checkout_value->work_time;
            $checkoutarr[$concat]['attn_address'] = $checkout_value->attn_address;
            $checkoutarr[$concat]['remarks'] = $checkout_value->remarks;
            $checkoutarr[$concat]['image_name'] = $checkout_value->image_name;
            }


           // dd($checkoutarr);
        
          
// dd($arr);
            return view('reports.dailyAttendancePatanjaliReport.ajax', [
                'users' => $user_record,
                'checkoutarr' => $checkoutarr,
                'company_id'=> $company_id,
            ]);

        }
    }



      public function get_mtp_details(Request $request)
    {
        $state_id = $request->state_id;
        $month = date('Y').'-'.$request->month;
        $outlet_type = $request->outlet_type;
        // dd($request);

        $query = DB::table('retailer')
                ->join('location_view','location_view.l5_id','=','retailer.location_id')
                ->join('person','person.id','=','retailer.created_by_person_id')
                ->join('dealer','dealer.id','=','retailer.dealer_id')
                ->select('retailer.name as retailer_name',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'dealer.name as dealer_name','l5_name as beat',DB::raw("DATE_FORMAT(created_on,'%Y-%m-%d') created_on"),'retailer.id as retailer_id','dealer.id as dealer_id','person.id as user_id')
                ->where('l2_id',$state_id)
                ->where('outlet_type_id','=',$outlet_type)
                ->whereRaw(" DATE_FORMAT(created_on,'%Y-%m')= '$month'")
                ->groupBy('retailer.id')
                ->get();


        $f_out = array();
        foreach ($query as $key => $value) 
        {
            $out['retailer_name'] = $value->retailer_name;
            $out['user_name'] = $value->user_name;
            $out['dealer_name'] = $value->dealer_name;
            $out['beat'] = $value->beat;
            $out['created_on'] = $value->created_on;
            $out['user_n'] = Crypt::encryptString($value->user_id);
            $out['retailer_n'] = Crypt::encryptString($value->retailer_id);
            $out['dealer_n'] = Crypt::encryptString($value->dealer_id);
           
            $f_out[] = $out;
        }

                // dd($query);
      
        if(!empty($query))
        {
            $data['code'] = 200;
            $data['result_data'] = $f_out;
            
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
          
            $data['result'] = '';
        }
        return json_encode($data);
        
    }
    

}
