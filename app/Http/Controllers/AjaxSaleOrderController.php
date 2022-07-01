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
use Illuminate\Http\Request;
use DB;
use Auth;
use Session;
use DateTime;

class AjaxSaleOrderController extends Controller
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



    public function saleOrderReport(Request $request)
    {
        if ($request->ajax() && !empty($request->from_date) && !empty($request->to_date)) {

            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $data1 = DB::table('user_sales_order_view')
            ->leftJoin('location_view','location_view.l5_id','=','user_sales_order_view.location_id')
                ->whereRaw("DATE_FORMAT(user_sales_order_view.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_sales_order_view.date,'%Y-%m-%d') <='$to_date'");
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data1->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $data1->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data1->whereIn('user_id', $user);
            }
            $user_record = $data1->get();

           // dd($query);
            return view('reports.sale-order.ajax', [
                'records' => $user_record,
                'from_date' => $from_date,
                'to_date' => $to_date
            ]);

        }
    }

    public function userSalesSummaryReport(Request $request)
    {
        if ($request->ajax() && !empty($request->date_range_picker)) {
            $company_id = Auth::user()->company_id;
            $explodeDate = explode(" -", $request->date_range_picker);

            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

            $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50 )
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
            $data1 = DB::table('location_3')
                    ->join('location_2','location_2.id','=','location_2_id')
                    ->join('location_1','location_1.id','=','location_1_id')
                    ->select('location_3.id as l3_id','location_3.name as l3_name','location_2.id as l2_id','location_2.name as l2_name','location_1.id as l1_id','location_1.name as l1_name');
           
            #Region filter
            if (!empty($request->zone)) {
                $zone = $request->zone;
                $data1->whereIn('location_1.id', $zone);
            }
            #State filter
            if (!empty($request->state)) {
                $state = $request->state;
                $data1->whereIn('location_3.id', $state);
            }
            // #User filter
            // if (!empty($request->user)) {
            //     $user = $request->user;
            //     $data1->whereIn('user_id', $user);
            // }

            ####
                if (!empty($datasenior)) 
            {
                $data1 = DB::table('location_3')
                ->join('location_2','location_2.id','=','location_2_id')
                ->join('location_1','location_1.id','=','location_1_id')
                 ->join('person','location_3.id','=','person.state_id')
                ->select('location_3.id as l3_id','location_3.name as l3_name','location_2.id as l2_id','location_2.name as l2_name','location_1.id as l1_id','location_1.name as l1_name');

                $data1->where('location_3.company_id',$company_id)->whereIn('person.id', $datasenior);
            }
            $user_record = $data1->where('location_3.company_id',$company_id)->get();
            // dd($user_record);

            $new_outlet = DB::table('retailer')
                ->join('location_view','location_view.l7_id','=','retailer.location_id')
                ->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(retailer.created_on,'%Y-%m-%d') <='$to_date'")
                ->where('retailer_status','1')
                ->where('retailer.company_id',$company_id)
                ->groupBy('l3_id')
                ->pluck(DB::raw("count(retailer.id) AS new_outlet"),'l3_id');

            $total_outlet = DB::table('retailer')
                ->join('location_view','location_view.l7_id','=','retailer.location_id')
                // ->where('l3_id',$l3id)
                ->where('retailer_status','1')
                ->where('retailer.company_id',$company_id)
                ->groupBy('l3_id')
                ->pluck(DB::raw("count(retailer.id) AS total_outlet"),'l3_id');

            $total_pc = DB::table('user_sales_order_view')
                    //->leftJoin('location_view','location_view.l5_id','=','user_sales_order_view.location_id')
                    // ->select(DB::raw("count(call_status) AS pc"))
                    ->whereRaw("DATE_FORMAT(user_sales_order_view.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_sales_order_view.date,'%Y-%m-%d') <='$to_date'")
                    // ->where('l3_id',$l3id)
                    ->where('call_status','1')
                    ->where('user_sales_order_view.company_id',$company_id)
                    ->groupBy('l3_id')
                    ->pluck(DB::raw("count(DISTINCT retailer_id,date) AS total_outlet"),'l3_id');

            if(empty($check)){
            $total_sale_value=DB::table('secondary_sale')
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        // ->where('l3_id','=',$l3id)
                        ->where('secondary_sale.company_id',$company_id)
                        // ->select()
                        ->groupBy('l3_id')
                        ->pluck(DB::raw("sum(rate*quantity) AS total_sale_value"),'l3_id');
            }
            else{
            $total_sale_value=DB::table('secondary_sale')
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        // ->where('l3_id','=',$l3id)
                        ->where('secondary_sale.company_id',$company_id)
                        ->groupBy('l3_id')
                        ->pluck(DB::raw("sum(final_secondary_rate*final_secondary_qty) AS total_sale_value"),'l3_id');
            }
            $total_user = DB::table('person')
                ->join('person_login','person_login.person_id','=','person.id')
                ->where('person_login.person_status','1')
                // ->where('state_id',$l3id)
                ->where('person.company_id',$company_id)
                ->groupBy('state_id')
                ->pluck(DB::raw("count(person.id) AS total_user"),'state_id');

            $active_user = DB::table('user_sales_order_view')
                // ->select()
                // ->where('person_state_id',$l3id)
                ->whereRaw("DATE_FORMAT(user_sales_order_view.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_sales_order_view.date,'%Y-%m-%d') <='$to_date'")
                ->where('user_sales_order_view.company_id',$company_id)
                ->groupBy('l3_id')
                ->pluck(DB::raw("count(DISTINCT user_id) AS user_id"),'l3_id');
                // dd($active_user);
            $total_work_status_leave = DB::table('person')
                            ->join('user_daily_attendance','user_daily_attendance.user_id','=','person.id')
                            ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') <='$to_date'")
                            ->where('person.company_id',$company_id)
                            ->groupBy('state_id','work_status')
                            ->pluck(DB::raw("count(DISTINCT person.id) AS w_s_l"),DB::raw("concat(state_id,work_status)"));

            // $total_work_status_present = DB::table('person')
            //                 ->join('user_daily_attendance','user_daily_attendance.user_id','=','person.id')
            //                 ->whereRaw('user_daily_attendance.work_status IN (12,13)')
            //                 ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') <='$to_date'")
            //                 ->where('person.company_id',$company_id)
            //                 ->groupBy('state_id')
            //                 ->pluck(DB::raw("count(DISTINCT person.id) AS w_s_l"),'state_id');

            $work_status = DB::table('_working_status')->where('company_id',$company_id)->where('status',1)->pluck('name','id');
            $dsr=[];
            foreach ($user_record as $key => $value) {
                $l3id=$value->l3_id;                                
                $dsr[$l3id]['l1_name']=$value->l1_name;
                $dsr[$l3id]['l3_name']=$value->l3_name;
                $dsr[$l3id]['l2_name']=$value->l2_name;
                $dsr[$l3id]['l3_id']=$value->l3_id;
                $dsr[$l3id]['new_outlet']=!empty($new_outlet[$value->l3_id])?$new_outlet[$value->l3_id]:'0';

                $dsr[$l3id]['total_outlet']=!empty($total_outlet[$value->l3_id])?$total_outlet[$value->l3_id]:'0';

                $dsr[$l3id]['pc']=!empty($total_pc[$value->l3_id])?$total_pc[$value->l3_id]:'0';
                $dsr[$l3id]['total_sale_value'] = !empty($total_sale_value[$value->l3_id])?$total_sale_value[$value->l3_id]:'0';
                // $dsr[$l3id]['total_sale_value']=DB::table('user_sales_order_view')
                // ->select(DB::raw("sum(total_sale_value) AS total_sale_value"))
                // ->where('l3_id',$l3id)
                // ->whereRaw("DATE_FORMAT(user_sales_order_view.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_sales_order_view.date,'%Y-%m-%d') <='$to_date'")->first();
                

                $dsr[$l3id]['total_user']=!empty($total_user[$value->l3_id])?$total_user[$value->l3_id]:'0';

                $dsr[$l3id]['active_user']=!empty($active_user[$value->l3_id])?$active_user[$value->l3_id]:'0';

                $dsr[$l3id]['work_status_leave']=!empty($total_work_status_leave[$value->l3_id])?$total_work_status_leave[$value->l3_id]:'0';
            

                $dsr[$l3id]['work_status_present']=!empty($total_work_status_present[$value->l3_id])?$total_work_status_present[$value->l3_id]:'0';

                // foreach ($work_status as $w_key => $w_value) 
                // {
                //     $dsr[$l3id][$w_key]['work_status']=!empty($total_work_status_leave[$value->l3_id.$w_key])?$total_work_status_present[$value->l3_id.$w_key]:'0';
                    
                // }

              
            }

        //    dd($dsr);
            return view('reports.user_sales_summary.ajax', [
                'records' => $user_record,
                'dsr' => $dsr,
                'work_status'=> $work_status,
                'from_date' => $from_date,
                'total_work_status_leave' => $total_work_status_leave,
                'to_date' => $to_date
            ]);

        }
    }

public function getActiveUserSale(Request $request)
{
    if ($request->ajax() && !empty($request->id)) {
        $id = $request->id;
        $to_date = $request->to_date;
        $from_date = $request->from_date;
        $flag = $request->flag;
        $company_id = Auth::user()->company_id;
       $data1 = array();
         $data = array();
         $datas = array();
         if($flag == 'A')
         {
            $user_query = DB::table('user_sales_order_view')
            ->join('location_view','location_view.l7_id','=','user_sales_order_view.location_id')
            ->select(DB::raw('DISTINCT user_id as user_id'),'user_name','emp_code','mobile','role_name',DB::raw('SUM(total_sale_value) as total_sale_value'),DB::raw('SUM(call_status) as call_status'))
            ->where('person_state_id',$id)
            ->where('user_sales_order_view.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(user_sales_order_view.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_sales_order_view.date,'%Y-%m-%d') <='$to_date'")->groupBy('user_id')->get();
     
         }
         elseif($flag == 'T'){
            $user_query=DB::table('person')
                ->join('person_login','person_login.person_id','=','person.id')
                ->join('_role','_role.role_id','=','person.role_id')
                ->where('person.company_id',$company_id)
                ->select(DB::raw('DISTINCT id as user_id'),DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'emp_code','mobile','rolename as role_name')
                ->where('person_login.person_status','1')->where('state_id',$id)
                ->get();
 
         }
         elseif($flag == 'L'){
            $user_query=DB::table('person')
            ->join('user_daily_attendance','user_daily_attendance.user_id','=','person.id')
            ->join('_role','_role.role_id','=','person.role_id')
            ->select(DB::raw('DISTINCT person.id as user_id'),DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'emp_code','mobile','rolename as role_name')
            ->where('person.company_id',$company_id)
            ->whereRaw('user_daily_attendance.work_status IN (12,13)')->where('state_id',$id)
            ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') <='$to_date'")
            ->get();

           
         }

         else{
            $user_query=DB::table('person')
            ->join('user_daily_attendance','user_daily_attendance.user_id','=','person.id')
            ->join('_role','_role.role_id','=','person.role_id')
            ->select(DB::raw('person.id as user_id'),DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'emp_code','mobile','rolename as role_name')
            ->where('user_daily_attendance.work_status',$flag)
            ->where('person.company_id',$company_id)
            ->where('state_id',$id)
            ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') <='$to_date'")
            ->get();
         }
 
              
        $sno =1;
        foreach($user_query as $key=>$value)
        {
            $dataId = $value->user_id;
            $datas['sno'] = $sno;
            $datas['user_name'] = $value->user_name;
            $datas['emp_code'] = $value->emp_code;
            $datas['mobile'] = $value->mobile;
            $datas['role_name'] = $value->role_name;
            if($flag == 'A')
            {
                $datas['call_status'] = $value->call_status;
                $datas['total_sale_value'] = $value->total_sale_value;
            }
            else
            {
                $datas['call_status'] = '-';
                $datas['total_sale_value'] = '-';
            }
          
            $sno++;
            $data1[$dataId] = $datas;
            
        }
        $data['user_data'] = $data1;
        $data['code'] = 200;
        $data['message'] = 'success';
    } else {
        $data['code'] = 401;
        $data['result'] = '';
        $data['message'] = 'unauthorized request';
    }
    // dd(json_encode($data));
    return json_encode($data);

}


         public function userTargetAchReport(Request $request)
    {
        //dd($request);   
        if ($request->ajax() && !empty($request->month)) {

            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $month = $request->month;
            $data1=DB::table('person')
            ->Join('person_login','person_login.person_id','=','person.id')
            ->Join('_role','_role.role_id','=','person.role_id')
            ->Join('location_view','location_view.l3_id','=','person.state_id')
            ->where('person_login.person_status','1')
            ->whereRaw("person.id!=1")
            ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'l1_name','l2_name','rolename AS role_name','emp_code','person.id AS user_id')
            ->groupBy('person.id')
            ->orderBy('user_name');
            // $data1 = DB::table('user_sales_order_view')
            // ->leftJoin('location_view','location_view.l5_id','=','user_sales_order_view.location_id')
            //     ->whereRaw("DATE_FORMAT(user_sales_order_view.date,'%Y-%m') ='$month'");
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data1->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $data1->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data1->whereIn('person.id', $user);
            }
            $user_record = $data1->get();
            $dsr=[];
            foreach ($user_record as $key => $value) {
                $user_id=$value->user_id;                                
                $dsr[$user_id]['l1_name']=$value->l1_name;
                $dsr[$user_id]['l2_name']=$value->l2_name;
                $dsr[$user_id]['emp_code']=$value->emp_code;
                $dsr[$user_id]['user_name']=$value->user_name;
                $dsr[$user_id]['emp_code']=$value->emp_code;
                $dsr[$user_id]['role_name']=$value->role_name;
                $dsr[$user_id]['tt']=DB::table('user_monthly_target')->select('target AS tt')->where('user_id',$user_id)->whereRaw("user_monthly_target.month='$month'")->first();
                $dsr[$user_id]['ta']=DB::table('user_sales_order_view')->select(DB::raw("sum(total_sale_value) AS ta"))->where('user_id',$user_id)->whereRaw("DATE_FORMAT(user_sales_order_view.date,'%Y-%m') ='$month'")->first();
            }

          //  dd($dsr);
            return view('reports.user_target_ach.ajax', [
                'records' => $user_record,
                'dsr' => $dsr,
                'month' => $month
            ]);

        }
    }

    public function timeSaleReport(Request $request)
    {
        if ($request->ajax() && !empty($request->month)) {

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

            $total_days=cal_days_in_month(CAL_GREGORIAN,$m,2005);
            $monthName=array('01' =>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');

            for($i = 1; $i <=  $total_days; $i++)
        {
        // add the date to the dates array
        $datesArr[] =  $y. "-" . $m2 . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
        $datesDisplayArr[] =str_pad($i, 2, '0', STR_PAD_LEFT)."-".$monthName[$m2]."-".$y ;
        }
            $data1 = DB::table('person')
            ->Join('_role','_role.role_id','=','person.role_id')
            ->Join('location_view','location_view.l3_id','=','person.state_id')
            ->whereNotIn('person.role_id',[5,6])
            ->where('person.status','1')
            ->whereRaw("person.id!=1")
            ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'l1_name','l2_name','rolename AS role_name','emp_code','person.id AS user_id')
            ->groupBy('person.id')
            ->orderBy('user_name');
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data1->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $data1->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data1->whereIn('person.id', $user);
            }
            $user_records = $data1->get();
            $user_record=[];
            foreach ($user_records as $key => $value) {
                $user_id=$value->user_id;
                $user_record[$user_id]['user_name']=$value->user_name;
                $user_record[$user_id]['l1_name']=$value->l1_name;
                $user_record[$user_id]['l2_name']=$value->l2_name;
                $user_record[$user_id]['emp_code']=$value->emp_code;
                $user_record[$user_id]['role_name']=$value->role_name;

                foreach ($datesArr as $keyd => $valued) {
                $user_record[$user_id][$keyd]['total_sale_value']=DB::table('user_sales_order_view')
                ->where('user_id',$user_id)
                ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$valued'")
                ->select(DB::raw("sum(total_sale_value) AS total_sale_value"))
                ->first();
                 if(empty($user_record[$user_id][$keyd]['total_sale_value']))
                    $user_record[$user_id][$keyd]['total_sale_value']='';

                $user_record[$user_id][$keyd]['pc']=DB::table('user_sales_order_view')
                ->where('user_id',$user_id)
                ->where('call_status',1)
                ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$valued'")
                ->select(DB::raw("count(call_status) AS pc"))
                ->first();
                if(empty($user_record[$user_id][$keyd]['pc']))
                    $user_record[$user_id][$keyd]['pc']='';

                $user_record[$user_id][$keyd]['tc']=DB::table('user_sales_order_view')
                ->where('user_id',$user_id)
                ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')='$valued'")
                ->select(DB::raw("count(call_status) AS tc"))
                ->first();
                if(empty($user_record[$user_id][$keyd]['tc']))
                    $user_record[$user_id][$keyd]['tc']='';
                }
            }

            //dd($user_record);
            return view('reports.time-sale.ajax', [
                'records' => $user_record,
                'month' => $month,
                'datesArr'=>$datesArr,
                'datesDisplayArr'=>$datesDisplayArr,
                'total_days'=>$total_days
            ]);

        }
    }
     ##############3
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
    #.............................................. monthly progressive report .......................#

    public function monthlyProgressiveReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax()) 
        {
        // dd($request);

            $state_id = $request->area;
            $month = !empty($request->month)?$request->month:date('Y-m');
            $start_date =date('Y-m-d',strtotime($month));
            $end_date= date("Y-m-t", strtotime($start_date));
            $startTime = strtotime($start_date);
            $endTime = strtotime($end_date);

 
            for ($currentDate = $startTime; $currentDate <= $endTime; $currentDate += (86400)) 
            {                                   
                $Store = date('Y-m-d', $currentDate); 
                $datearray[] = $Store; 
            } 

            $out =[];
            $statedatad = Location3::where('status', 1)
            ->select('id','name')
            ->where('company_id',$company_id);
            if(!empty($state_id))
            {
               $statedatad->whereIn('id',$state_id);
            }
            $statedata = $statedatad->get();
            $outlet_type_name = DB::table('_retailer_outlet_type')->where('_retailer_outlet_type.company_id',$company_id)->select('id','outlet_type as name')->get();
        
            $main_query = Retailer::join('location_view','location_view.l7_id','=','retailer.location_id')->join('_retailer_outlet_type','_retailer_outlet_type.id','=','retailer.outlet_type_id')->select('outlet_type_id','_retailer_outlet_type.outlet_type as outlet_type_name','location_view.l3_id as state_id','location_view.l3_name as state_name',
                DB::raw('DATE_FORMAT(created_on, "%Y-%m-%d") as created_on'), DB::raw("COUNT(distinct retailer.id) as outlet_count"))->whereRaw("DATE_FORMAT(`created_on`,'%Y-%m') = '$month'")->where('retailer_status','=','1')->where('retailer.company_id',$company_id)->groupBy('location_view.l3_id','_retailer_outlet_type.id','created_on');
            if (!empty($state_id)) 
            {
               $main_query->whereIn('location_view.l3_id',$state_id);
            }

            $records = $main_query->orderBy('location_view.l3_name','ASC')->orderBy('_retailer_outlet_type.sequence','ASC')->get();
            //dd($records);
            foreach($records as $key=>$value)
            {        
                    $state_id = $value->state_id;
                    $created_on = $value->created_on;
                    $outlet_type_id = $value->outlet_type_id;
                    $out[$created_on][$outlet_type_id][$state_id]['outlet_count']=$value->outlet_count;   
            }   
              // dd($out);                
            return view('reports.monthly-progressive.ajax', [
                    'records' => $records,
                    'datearray' => $datearray,
                    'year'=>$month,
                    'details' => $out,
                    'statedata' => $statedata,
                    'outlet_type_name' => $outlet_type_name
                    
                ]);
        } 
    }   
    
    #..............................................Ends here monthly progressive report .......................#

    public function merchandiseOrderReport(Request $request)
    {
        if ($request->ajax() && !empty($request->from_date) && !empty($request->to_date)) {

            $company_id = Auth::user()->company_id;
            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $data1 = DB::table('merchandise')
            ->join('person','person.id','merchandise.user_id')
            ->join('_role','_role.role_id','person.role_id')
            ->leftJoin('_retailer_mkt_gift','_retailer_mkt_gift.id','=','merchandise.merchandise_id')
            ->leftJoin('retailer','retailer.id','=','merchandise.retailer_id')
            ->join('dealer','dealer.id','retailer.dealer_id')
            ->where('merchandise.company_id',$company_id)
            ->leftJoin('location_view','location_view.l7_id','=','retailer.location_id')
            ->whereRaw("DATE_FORMAT(merchandise.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(merchandise.date,'%Y-%m-%d') <='$to_date'  ")
             ->select('dealer.id as dealer_id','retailer.id as retailer_id','user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'l2_name','l3_name','l4_name','head_quar','rolename','mobile','emp_code','retailer.name as retailername','merchandise.date as date','merchandise_name','qty','dealer.name as dealername','person_id_senior','merchandise.time as time','merchandise.lat as lat','merchandise.lng as lng',DB::raw("(SELECT CONCAT_WS(' ',first_name,middle_name,last_name) from person as p WHERE p.id=person.person_id_senior) AS seniorname"))
             ->groupBy('merchandise.id');
             // ->groupBy('retailer_id');
           //seniorname
           
                 #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data1->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $data1->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data1->whereIn('user_id', $user);
            }
            $user_record = $data1->get();

           // dd($query);
            return view('reports.merchandise_sale_order.ajax', [
                'records' => $user_record,
                'from_date' => $from_date,
                'to_date' => $to_date
            ]);

        }
    }
    // end merchandise report

    public function retailerStockReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax()) {

            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            // $from_date = $request->from_date;
            // $to_date = $request->to_date;

            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

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


            $data1=DB::table('retailer_stock')
              ->join('retailer','retailer.id','retailer_stock.retailer_id')
            
              ->join('person','person.id','retailer_stock.user_id')
              ->join('dealer','dealer.id','retailer_stock.dealer_id')    
              ->join('_role','_role.role_id','person.role_id')
              ->join('location_view','location_view.l7_id','retailer_stock.location_id')
              ->select('rolename','person.id as user_id','dealer.id as dealer_id','retailer.id as retailer_id','retailer_stock.order_id','retailer.name as rname','retailer_stock.id as id','retailer_stock.user_id as user_id','retailer_stock.dealer_id as dealer_id', 'l3_name as l3_name', 'l4_name as l4_name','l5_name','l6_name','l7_name',DB::raw("DATE_FORMAT(retailer_stock.date,'%d-%m-%Y') AS stock_date"),'person.mobile as mobile',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"), 'person.role_id', '_role.rolename as role_name','dealer.name as dealer_name','person_id_senior')
             ->whereRaw("DATE_FORMAT(retailer_stock.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(retailer_stock.date,'%Y-%m-%d') <='$to_date'")
             ->where('retailer_stock.company_id',$company_id)
             ->groupBy('retailer_stock.order_id') ;


            #Region filter
            if (!empty($datasenior)) 
            {
                $data1->whereIn('person.id', $datasenior);
            }
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $data1->whereIn('l3_id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $data1->whereIn('l4_id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $data1->whereIn('l5_id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $data1->whereIn('l6_id', $location_6);
            }
            if (!empty($request->location_7)) 
            {
                $location_7 = $request->location_7;
                $data1->whereIn('l7_id', $location_7);
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $data1->whereIn('person.role_id', $role);
            }
            // if (!empty($request->region)) {
            //     $region = $request->region;
            //     $data1->whereIn('l2_id', $region);
            // }
            // #State filter
            // if (!empty($request->area)) {
            //     $area = $request->area;
            //     $data1->whereIn('l3_id', $area);
            // }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data1->whereIn('user_id', $user);
            }
            $user_record = $data1->get();
            // dd($data1);
            $dsr=[];
            foreach ($user_record as $key => $value) {
             $id = $value->id;
             $orderid = $value->order_id;
                $dsr[$orderid]['date']=$value->stock_date;                                
                $dsr[$orderid]['rname']=$value->rname; 

                $dsr[$orderid]['person_id_senior']=$value->person_id_senior;                                
                $dsr[$orderid]['l3_name']=$value->l3_name;                                
                $dsr[$orderid]['l4_name']=$value->l4_name;
                $dsr[$orderid]['l5_name']=$value->l5_name;
                $dsr[$orderid]['l6_name']=$value->l6_name;
                $dsr[$orderid]['l7_name']=$value->l7_name;

                // $dsr[$orderid]['state']=$value->state;
                // $dsr[$orderid]['town']=$value->town;
                $dsr[$orderid]['user_name']=$value->user_name;
                $dsr[$orderid]['role_name']=$value->role_name;
                $dsr[$orderid]['mobile']=$value->mobile;
                // $dsr[$orderid]['person_id_senior']=$value->person_id_senior;
                // $dsr[$orderid]['seniorname']=DB::table('person')->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS seniorname"))->where('id',$value->person_id_senior)->where('person.company_id',$company_id)->first();
                $dsr[$orderid]['dealer_name']=$value->dealer_name;
      
                $proout = DB::table('retailer_stock_details')
                     ->join('catalog_product','catalog_product.id','=','retailer_stock_details.product_id')
                     ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
                  ->where('order_id', $orderid)
                  ->where('retailer_stock_details.company_id',$company_id)
                  // ->select('retailer_stock_details.quantity as pieces','catalog_product.name as product_name','catalog_product.base_price_per as mrp','catalog_product.base_price as base_price',DB::raw("(catalog_product.base_price_per*retailer_stock_details.quantity) as total_sale_value"));
                   ->select('retailer_stock_details.quantity as pieces','catalog_product.name as product_name','product_rate_list.retailer_pcs_rate as mrp','product_rate_list.retailer_pcs_rate as base_price',DB::raw("(product_rate_list.retailer_pcs_rate*retailer_stock_details.quantity) as total_sale_value")); // changed by shree 

                  $dsr[$orderid]=$proout->groupBy('retailer_stock_details.id')->get(); 

            }

          //  dd($dsr);
            return view('reports.retailerStockReport.ajax', [
                'records' => $user_record,
                'dsr' => $dsr,
                'from_date' => $from_date,
                'to_date' => $to_date
            ]);

        }
    }



    public function userMeetingOrderReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax() && !empty($request->from_date) && !empty($request->to_date)) {

            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
	    	$array = array(99,100,101,102); // for oyster


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

            $meeting_type = DB::table('_meeting_with_type')->where('status',1)->where('company_id',$company_id)->pluck('name','id')->toArray();
            // dd($meeting_type);  


            $data1=DB::table('meeting_order_booking')
            
              ->join('person','person.id','meeting_order_booking.user_id')

              ->join('location_3','location_3.id','=','person.state_id')
              ->join('location_2','location_2.id','=','location_3.location_2_id')

              ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'meeting_with','meet_address','meet_name','type_of_meet','time_in','time_out','meeting_remark','followup_date','contact_no','current_date_m','followup_time','person.id as user_id','location_3.name as state','person.mobile')
          
             
             ->whereRaw("DATE_FORMAT(meeting_order_booking.current_date_m,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(meeting_order_booking.current_date_m,'%Y-%m-%d') <='$to_date'")
             ->where('meeting_order_booking.company_id',$company_id);
             // ->groupBy('retailer_stock.order_id') ;
             if($login_user == 2833){
                $data1->whereNotIn('person.state_id',$array);		
             }
                #Junior filter
            if (!empty($datasenior)) 
            {
                $data1->whereIn('user_id', $datasenior);
            }


            #Region filter
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
                $data1->whereIn('user_id', $user);
            }
            $user_record = $data1->get();
            // dd($user_record);
         

          //  dd($dsr);
            return view('reports.userMeetingOrderReport.ajax', [
                'records' => $user_record,
                'meeting_type' => $meeting_type,
                'from_date' => $from_date,
                'to_date' => $to_date
            ]);

        }
    }

      ####################### Retailer and Distributor Stock Ends ######################
    #..............................................Daily Tracking Report Starts here .......................#
   public function dailyTrackingReport(Request $request)
    {
        if ($request->ajax() && !empty($request->date_range_picker)) {

            $explodeDate = explode(" -", $request->date_range_picker);
            $company_id = Auth::user()->company_id;
            $state = $request->state;
            $city = $request->city;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            // $role_id = Auth::user()->is_admin;
	    	$array = array(99,100,101,102); // for oyster

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
                // Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }


            $constant = DB::table('_constant')->pluck('tracking_count');
            $tracking = DB::table('user_work_tracking')->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as person_fullname'),'user_work_tracking.user_id','user_work_tracking.track_time','user_work_tracking.track_date','user_work_tracking.track_address as address','location_3.name as state_name','location_3.name as l3_name', 'location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','location_2.name as l2_name','rolename as role_name','person.person_id_senior','person.mobile')
                ->join('person','person.id','=','user_work_tracking.user_id')
                ->join('_role','_role.role_id','=','person.role_id')
                ->join('location_3','location_3.id','=','person.state_id')
                ->join('location_2','location_2.id','=','location_3.location_2_id')
                ->join('location_6', 'location_6.id', '=', 'person.town_id')
                ->join('location_5','location_5.id','=','location_6.location_5_id')
                ->join('location_4','location_4.id','=','location_5.location_4_id')
                // ->join('location_3','location_3.id','=','person.state_id')
                ->whereRaw("DATE_FORMAT(user_work_tracking.track_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_work_tracking.track_date,'%Y-%m-%d') <='$to_date'")
                // ->where('user_id','!=',4)
                ->where('user_work_tracking.company_id',$company_id)
                ->groupBy('user_work_tracking.user_id','user_work_tracking.track_date')
                ->orderBy('track_date','ASC');
                if($login_user == 2833){
                    $tracking->whereNotIn('location_3.id',$array);		
                 }
            #state filter
            if (!empty($state)) {
                $tracking->whereIn('location_3.id', $state);
            }
            #city filter
            if (!empty($city)) {
                $tracking->whereIn('user_sales_order_view.l3_id', $city);
            }
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $tracking->whereIn('location_3.id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $tracking->whereIn('location_4.id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $tracking->whereIn('location_5.id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $tracking->whereIn('location_6.id', $location_6);
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $tracking->whereIn('person.role_id', $role);
            }
            #User filter
            if (!empty($user)) {
                $tracking->whereIn('user_id', $user);
            }
            if (!empty($datasenior)) 
            {
                $tracking->whereIn('user_id', $datasenior);
            }

            $tracking_record = $tracking->get();

            $out=array();
           if (!empty($tracking_record)) 
           {
                if($company_id == 43)
                {
                    // $loop_array = array('9','10','11','12','13','14','15','16','17','18','19');
                    // $data = DB::table('user_work_tracking')
                    //         ->select('status','gps_status','battery_status','track_time','user_work_tracking.track_address as location')
                    //         ->where('user_work_tracking.company_id',$company_id)
                    //         // ->where('track_date',$date)
                    //         // ->where('user_id', $uid)
                    //         ->groupBy('user_id','track_date')
                    //         ->pluck(DB::raw("CONCAT_WS('|',status,gps_status,battery_status,track_time,track_address) as data"),DB::raw("CONCAT(track_date,user_id) as view"));

                    // foreach ($tracking_record as $k => $d) {
                    //     $uid=$d->user_id;
                    //     $date=$d->track_date;
                    //     $explode_array = !empty($data[$uid.$date])?$data[$uid.$date]:''; 
                    //     $out[$uid][$date]['status']=!empty($explode_array[0])?$explode_array[0]:''; 
                    //     $out[$uid][$date]['gps_status']=!empty($explode_array[1])?$explode_array[1]:''; 
                    //     $out[$uid][$date]['battery_status']=!empty($explode_array[2])?$explode_array[2]:''; 
                    //     $out[$uid][$date]['track_time']=!empty($explode_array[3])?$explode_array[3]:''; 
                    //     $out[$uid][$date]['location']=!empty($explode_array[4])?$explode_array[4]:''; 
                         
                    // }
                    // // dd($out);
                    foreach ($tracking_record as $k => $d) {
                        $uid=$d->user_id;
                        $date=$d->track_date;
                         $out[$uid][$date][]= DB::table('user_work_tracking')->select('status','gps_status','battery_status','track_time','user_work_tracking.track_address as location','lat_lng')->where('user_work_tracking.company_id',$company_id)->where('track_date',$date)->where('user_id', $uid)->get();
                         
                    }
                    return view('reports.daily-tracking.btwAjax', [
                        'records' => $tracking_record,
                        'constant' => $constant,
                        'from_date' => $from_date,
                        'to_date' => $to_date,
                        'track_time'=> $out
                    ]);

                }
                else
                {

                    foreach ($tracking_record as $k => $d) {
                        $uid=$d->user_id;
                        $date=$d->track_date;
                         $out[$uid][$date][]= DB::table('user_work_tracking')
                         ->select('status','gps_status','battery_status','track_time','user_work_tracking.track_address as location')
                         ->where('user_work_tracking.company_id',$company_id)
                         ->where('track_date',$date)
                         ->where('user_id', $uid)
                         ->whereRaw("DATE_FORMAT(user_work_tracking.track_time,'%H:%i:%s') >='09:00:00' AND DATE_FORMAT(user_work_tracking.track_time,'%H:%i:%s') <='20:00:00'")
                         ->where('lat_lng','!=','NULL')
                         ->where('lat_lng','!=','')
                         ->where('lat_lng','!=','0,0')
                         ->where('lat_lng','!=','0.0,0.0')
                         ->get();
                         
                    }


                    // for all
                    $userkey = array();
                    foreach ($out as $ukey => $uvalue) {
                         foreach ($uvalue as $dkey => $dvalue) {
                            $lastout = array();
                             foreach ($dvalue as $fkey => $fvalue) {
                                $user_work_tracking_array = array();
                                foreach ($fvalue as $lkey => $lvalue) {

                                        $unix_timew = strtotime($lvalue->track_time); 
                                        $startTimew = $unix_timew;

                                        if($lkey == 0){
                                        $user_work_tracking_array[] = $lvalue;
                                        $plusthirtytimew = strtotime(date('H:i:s',strtotime('+30 minutes',$unix_timew)));
                                        }

                                        if($plusthirtytimew <= $startTimew){
                                        $user_work_tracking_array[] = $lvalue;
                                         $plusthirtytimew = strtotime(date('H:i:s',strtotime('+30 minutes',$unix_timew)));
                                        }
                                }

                             $lastout[] = $user_work_tracking_array;

                             }
                             $dateout[$dkey] = $lastout;
                         }
                         $userkey[$ukey] = $dateout;
                    }
                    $out = $userkey;
                    // for all





                    return view('reports.daily-tracking.ajax', [
                        'records' => $tracking_record,
                        'constant' => $constant,
                        'from_date' => $from_date,
                        'to_date' => $to_date,
                        'track_time'=> $out
                    ]);
                    // dd($out);
                }
            }
            

        }
    }
    public function dailyTrackingReportTest(Request $request)
    {
        if ($request->ajax() && !empty($request->date_range_picker)) {

            $explodeDate = explode(" -", $request->date_range_picker);
            $company_id = Auth::user()->company_id;
            $state = $request->state;
            $city = $request->city;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $role_id = Auth::user()->role_id;
            if($role_id==1 || $role_id==50 || $is_admin = 1 || $this->without_junior == 0)
            {
                $datasenior='';
            }
            else
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

            $constant = DB::table('_constant')->pluck('tracking_count');
            $tracking = DB::table('user_work_tracking')
            ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as person_fullname'),'user_work_tracking.user_id','user_work_tracking.track_time','user_work_tracking.track_date','user_work_tracking.track_address as address','location_3.name as state_name')
            ->join('person','person.id','=','user_work_tracking.user_id')
            ->join('location_3','location_3.id','=','person.state_id')
            ->join('location_6', 'location_6.id', '=', 'person.town_id')
            ->join('location_5','location_5.id','=','location_6.location_5_id')
            ->join('location_4','location_4.id','=','location_5.location_4_id')
            ->whereRaw("DATE_FORMAT(user_work_tracking.track_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_work_tracking.track_date,'%Y-%m-%d') <='$to_date'")
                // ->where('user_id','!=',4)
                ->where('user_work_tracking.company_id',$company_id)
                ->groupBy('user_work_tracking.user_id','user_work_tracking.track_date')
                ->orderBy('track_date','ASC');
                if (!empty($$request->location_3)) {
                $tracking->whereIn('location_3.id', $$request->location_3);
                }
                if (!empty($$request->location_4)) {
                $tracking->whereIn('location_4.id', $$request->location_4);
                }
                if (!empty($$request->location_5)) {
                $tracking->whereIn('location_5.id', $$request->location_5);
                }
                  if (!empty($$request->location_6)) {
                $tracking->whereIn('location_6.id', $$request->location_6);
                }
                if (!empty($user)) {
                $tracking->whereIn('user_id', $user);
                 }
            #state filter
            // if (!empty($state)) {
            //     $tracking->whereIn('location_3.id', $state);
            // }
            // #city filter
            // if (!empty($city)) {
            //     $tracking->whereIn('user_sales_order_view.l3_id', $city);
            // }
            // #User filter
            // if (!empty($user)) {
            //     $tracking->whereIn('user_id', $user);
            // }
            if (!empty($datasenior)) 
            {
                $tracking->whereIn('user_id', $datasenior);
            }

            $tracking_record = $tracking->get();

            $out=array();
           if (!empty($tracking_record)) 
           {
                foreach ($tracking_record as $k => $d) {
                    $uid=$d->user_id;
                    $date=$d->track_date;
                     $out[$uid][$date][]= DB::table('user_work_tracking')->select('status','gps_status','battery_status','track_time','user_work_tracking.track_address as location')->where('user_work_tracking.company_id',$company_id)->where('track_date',$date)->where('user_id', $uid)->get();
                     
                }
            }
            return view('reports.daily-tracking.kbzajax', [
                'records' => $tracking_record,
                'constant' => $constant,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'track_time'=> $out
            ]);

        }
    }
    #..............................................Daily Tracking Report Ends here .......................#
    #...................................................no booking starts here........................................................#
    public function noBookingReport(Request $request)
    {
        if ($request->ajax() ) {

            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $company_id = Auth::user()->company_id;
             $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));


            $location_4 = $request->location_4;
            $location_5 = $request->location_5;
            $location_6 = $request->location_6;


            $start = strtotime($from_date);
            $end = strtotime($to_date);
    
    
            $datearray = array();
            $datediff =  ($end - $start)/60/60/24;
            $datearray[] = $from_date;
    
            for($i=0 ; $i<$datediff;$i++)
            {
                $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
            }

            // $explodeDate = explode(" -", $request->date_range_picker);
            // $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            // $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            // $month = !empty($request->month)?$request->month:date('Y-m');
            // $m1=explode('-', $month);
            // $y=$m1[0];
            // $m2=$m1[1];
            // if($m2<10)
            // $m=ltrim($m2, '0');
            // else
            // $m=$m2;

            // $total_days=cal_days_in_month(CAL_GREGORIAN,$m,$y);
            // $monthName=array('01' =>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');

            // for($i = 1; $i <=  $total_days; $i++)
            // {
            // // add the date to the dates array
            // $datesArr[] = $y . "-" . $m2 . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
            // $datesArr2 = $y . "-" . $m2 . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
            // $datesDisplayArr[$datesArr2] =str_pad($i, 2, '0', STR_PAD_LEFT)."-".$monthName[$m2]."-".$y ;
            // }
            // dd($datesDisplayArr);
          
           $data1 = DB::table('person')
           ->join('person_login','person_login.person_id','=','person.id')
           ->join('_role','_role.role_id','=','person.role_id')
        //    ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
        //    ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
           ->join('location_3', 'location_3.id', '=', 'person.state_id')
           ->Join('location_2', 'location_2.id', '=', 'location_3.location_2_id')
           ->Join('location_1', 'location_1.id', '=', 'location_2.location_1_id')
           ->join('location_6','location_6.id','=','person.town_id')
           ->join('location_5','location_5.id','=','location_6.location_5_id')
           ->join('location_4','location_4.id','=','location_5.location_4_id')
        //    ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
           ->where('person_login.person_status','1')
           ->where('person.id','!=','1')
           ->where('person.company_id',$company_id)
           ->where('person_login.company_id',$company_id);
          //  ->whereNotIn('person.id',function($query) use($from_date,$company_id)
          //  {
          //   $query->select('user_id')->from('user_sales_order')
          //   ->where('company_id',$company_id)
          //   ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') ='$from_date'");
          // });
            #Region filter
            if (!empty($request->role)) {
                $role = $request->role;
                $data1->whereIn('person.role_id', $role);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $data1->whereIn('location_3.id', $area);
            }
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $data1->whereIn('location_3.id', $location_3);
            }
            if (!empty($location_4)) {
                $data1->whereIn('location_4.id', $location_4);
            }  
            if (!empty($location_5)) {
                $data1->whereIn('location_5.id', $location_5);
            }  
            if (!empty($location_6)) {
                $data1->whereIn('location_6.id', $location_6);
            }  
            #User filter
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $data1->whereIn('person.id', $user);
            }
           $data1->select('person.id as user_id','person.person_id_senior as person_id_senior1','person.*',DB::raw("(SELECT CONCAT_WS(' ',first_name,middle_name,last_name)  FROM person
           WHERE person.id=person_id_senior1) as srname"),'person.id','location_4.name as l4_name','location_3.name as l3_name','_role.rolename','location_6.name as l6_name','location_3.id as l3_id',DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) AS user_name"),'location_5.name as l5_name')
           ->groupBy('person.id','l3_id','rolename')
           ->orderBy('person.id','ASC');
            
            $user_record = $data1->get();

            $data_no_booking = DB::table('user_sales_order')
                    ->where('company_id',$company_id)
                    ->groupBy('date','user_id')
                    ->pluck(DB::raw("COUNT(order_id) as data"),DB::raw('CONCAT(date,user_id) as id'));

            // dd($data);

             // dd($user_record);
            return view('reports.no-booking.ajax', [
                'records' => $user_record,
                'from_date' => $from_date,
                // 'datesArr'=>$datesArr,
                'data_no_booking'=> $data_no_booking,
                'datearray'=> $datearray,
                // 'datesDisplayArr'=>$datesDisplayArr,
                
            ]);

        }
    }
    #............................................................................no attendance report starts here...................................................................##
    public function noAttendanceReport(Request $request)
    {
        if ($request->ajax() ) {

            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $company_id = Auth::user()->company_id;
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

            $location_4 = $request->location_4;
            $location_5 = $request->location_5;
            $location_6 = $request->location_6;

            $start = strtotime($from_date);
            $end = strtotime($to_date);
    
    
            $datearray = array();
            $datediff =  ($end - $start)/60/60/24;
            $datearray[] = $from_date;
    
            for($i=0 ; $i<$datediff;$i++)
            {
                $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
            }

             // $explodeDate = explode(" -", $request->date_range_picker);
            // $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            // $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            // $month = !empty($request->month)?$request->month:date('Y-m');
            // $m1=explode('-', $month);
            // $y=$m1[0];
            // $m2=$m1[1];
            // if($m2<10)
            // $m=ltrim($m2, '0');
            // else
            // $m=$m2;

            // $total_days=cal_days_in_month(CAL_GREGORIAN,$m,$y);
            // $monthName=array('01' =>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');

            // for($i = 1; $i <=  $total_days; $i++)
            // {
            // // add the date to the dates array
            // $datesArr[] = $y . "-" . $m2 . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
            // $datesArr2 = $y . "-" . $m2 . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
            // $datesDisplayArr[$datesArr2] =str_pad($i, 2, '0', STR_PAD_LEFT)."-".$monthName[$m2]."-".$y ;
            // }

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

          
           $data1 = DB::table('person')
            ->join('person_login','person_login.person_id','=','person.id')
            ->join('_role','_role.role_id','=','person.role_id')
            ->join('location_3', 'location_3.id', '=', 'person.state_id')
            ->Join('location_2', 'location_2.id', '=', 'location_3.location_2_id')
            ->Join('location_1', 'location_1.id', '=', 'location_2.location_1_id')
            ->join('location_6','location_6.id','=','person.town_id')
            ->join('location_5','location_5.id','=','location_6.location_5_id')
            ->join('location_4','location_4.id','=','location_5.location_4_id')
            ->where('person_login.person_status','1')  
            ->where('person.id','!=',1)
            ->where('person.company_id',$company_id)
            ->where('person_login.company_id',$company_id);
          //  ->whereNotIn('person.id',function($query) use($from_date,$company_id)
          //  {
          //   $query->select('user_id')->from('user_daily_attendance')
          //       ->where('company_id',$company_id)
          //         ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') ='$from_date'");
          // });
            #Region filter
              if (!empty($datasenior)) 
            {
                $data1->whereIn('person.id', $datasenior);
            }
            if (!empty($request->role)) {
                $role = $request->role;
                $data1->whereIn('person.role_id', $role);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $data1->whereIn('location_3.id', $area);
            }
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $data1->whereIn('location_3.id', $location_3);
            }
            if (!empty($location_4)) {
                $data1->whereIn('location_4.id', $location_4);
            }  
            if (!empty($location_5)) {
                $data1->whereIn('location_5.id', $location_5);
            }  
            if (!empty($location_6)) {
                $data1->whereIn('location_6.id', $location_6);
            }  
         
            #User filter
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $data1->whereIn('person.id', $user);
            }
           $data1->select('person.id as user_id','person.person_id_senior as person_id_senior1','person.*',DB::raw("(SELECT CONCAT_WS(' ',first_name,middle_name,last_name) FROM person
           WHERE person.id=person_id_senior1) as srname"),'person.id','location_4.name as l4_name','location_3.name as state','location_6.name as l6_name','_role.rolename','location_5.name as l5_name',DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) AS user_name"));
            
           
            $user_record = $data1->groupBy('person.id','rolename')->get();
            // $data_no_booking = array();
            $data_no_booking = DB::table('user_daily_attendance')
                    ->where('company_id',$company_id)
                    ->groupBy('work_date','user_id')
                    ->pluck(DB::raw("COUNT(id) as data"),DB::raw("CONCAT(DATE_FORMAT(work_date,'%Y-%m-%d'),user_id) as id"));
            // dd($data_no_booking);
             // dd($user_record);
            return view('reports.no-attendance.ajax', [
                'records' => $user_record,
                'data_no_booking'=> $data_no_booking,
                // 'datesArr' => $datesArr,
                // 'datesDisplayArr' => $datesDisplayArr,
                 'datearray'=> $datearray,
                'from_date' => $from_date,
                
            ]);

        }
    }

      #..............................................Modern Report start here ...............................#


    public function merchandiseVisitReport(Request $request)
    {
        if ($request->ajax() && !empty($request->date_range_picker)) {

            $company_id = Auth::user()->company_id;
            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

            //  $from_date = $request->from_date;
            // $to_date = $request->to_date;
            $data1 = DB::table('merchandiser_checkin')
            ->leftJoin('merchandiser_checkout','merchandiser_checkout.orderId','=','merchandiser_checkin.orderId')
            ->join('person','person.id','=','merchandiser_checkin.userId')
            ->join('_role','_role.role_id','=','person.role_id')
            ->join('dealer','dealer.id','=','merchandiser_checkin.dealerId')
            ->join('location_view','person.state_id','=','location_view.l3_id')
            ->whereRaw("DATE_FORMAT(merchandiser_checkin.workDate,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(merchandiser_checkin.workDate,'%Y-%m-%d') <='$to_date'")
            ->where('merchandiser_checkin.company_id',$company_id)
            ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'dealer.name AS dealer_name',DB::raw("DATE_FORMAT(workDate,'%d-%m-%Y') AS date"),DB::raw("DATE_FORMAT(workDate,'%H:%i:%s') AS check_in"),'rolename AS role_name','l3_name','emp_code','merchandiser_checkout.time AS check_out','l1_name','l2_name','workDate','merchandiser_checkin.userId')
            ->groupBy('user_name','dealer_name','date','check_in','check_out')
            ->orderBy('date');
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data1->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $data1->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data1->whereIn('merchandiser_checkin.userId', $user);
            }
            $user_record = $data1->get();


            $sale=[];
            foreach ($user_record as $key => $value) {
                $index=$value->userId.$value->workDate;
                $sales=DB::table('user_sales_order')
                ->where('user_id',$value->userId)
                ->where('user_sales_order.company_id',$company_id)
                ->whereRaw("DATE_FORMAT(date,'%d-%m-%Y')='$value->date'")
                ->select('order_id')
                ->first();
            $sale[$index]=!empty($sales->order_id)?'YES':'NO';
            }
            return view('reports.merchandise-visit.ajax', [
                'records' => $user_record,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'sale' => $sale
            ]);

        }
    }

    public function supervisorVisitReport(Request $request)
    {
        if ($request->ajax() && !empty($request->date_range_picker)) {

            $company_id = Auth::user()->company_id;
            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
             $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $data1 = DB::table('coverage_checkin')
            ->leftJoin('coverage_checkout','coverage_checkout.orderId','=','coverage_checkin.orderId')
            ->join('person','person.id','=','coverage_checkin.userId')
            ->join('_role','_role.role_id','=','person.role_id')
            ->join('dealer','dealer.id','=','coverage_checkin.dealerId')
            ->join('location_view','person.state_id','=','location_view.l3_id')
            ->where('coverage_checkin.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(coverage_checkin.workDate,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(coverage_checkin.workDate,'%Y-%m-%d') <='$to_date'")
            ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'dealer.name AS dealer_name',DB::raw("DATE_FORMAT(workDate,'%d-%m-%Y') AS date"),DB::raw("DATE_FORMAT(workDate,'%H:%i:%s') AS check_in"),'rolename AS role_name','l3_name','emp_code','coverage_checkout.time AS check_out','l1_name','l2_name','workDate','coverage_checkin.userId')
            ->groupBy('user_name','dealer_name','date','check_in','check_out')
            ->orderBy('date');
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data1->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $data1->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data1->whereIn('coverage_checkin.userId', $user);
            }
            $user_record = $data1->get();

            $orders=[];
            foreach ($user_record as $key => $value) {
                $index=$value->userId.$value->workDate;
                $order=DB::table('user_primary_sales_order')
                ->where('created_person_id',$value->userId)
                ->where('user_primary_sales_order.company_id',$company_id)
                ->whereRaw("DATE_FORMAT(created_date,'%d-%m-%Y')='$value->date'")
                ->select('order_id')
                ->first();
            $orders[$index]=!empty($order->order_id)?'YES':'NO';
            }

           // dd($query);
            return view('reports.supervisor-visit.ajax', [
                'records' => $user_record,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'order' => $orders
            ]);

        }
    }


     public function retailerCaptureImagesReport(Request $request)
    {
        if ($request->ajax() && !empty($request->date_range_picker)) {

            $company_id = Auth::user()->company_id;
            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
           $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $data1 = DB::table('retailer_capture_images')
            ->join('person','person.id','=','retailer_capture_images.user_id')
            ->join('_role','_role.role_id','=','person.role_id')
            ->join('dealer','dealer.id','=','retailer_capture_images.dealer_id')
            ->join('location_view','person.state_id','=','location_view.l3_id')
            ->where('retailer_capture_images.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(retailer_capture_images.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(retailer_capture_images.date,'%Y-%m-%d') <='$to_date'")
            ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'dealer.name AS dealer_name',DB::raw("DATE_FORMAT(retailer_capture_images.date,'%d-%m-%Y') AS date"),'retailer_capture_images.time AS check_in','rolename AS role_name','l3_name','emp_code','l1_name','l2_name','retailer_capture_images.user_id','image_name1','image_name2','image_name3')
            ->groupBy('user_name','dealer_name','date','check_in')
            ->orderBy('date');
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data1->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $data1->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data1->whereIn('retailer_capture_images.user_id', $user);
            }
            $user_record = $data1->get();

           // dd($query);
            return view('reports.retailer_capture_images.ajax', [
                'records' => $user_record,
                'from_date' => $from_date,
                'to_date' => $to_date
            ]);

        }
    }


    public function coverageCaptureImagesReport(Request $request)
    {
        if ($request->ajax() && !empty($request->date_range_picker)) {

            $company_id = Auth::user()->company_id;
            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            $data1 = DB::table('coverage_capture_images')
            ->join('person','person.id','=','coverage_capture_images.user_id')
            ->join('_role','_role.role_id','=','person.role_id')
            ->join('dealer','dealer.id','=','coverage_capture_images.dealer_id')
            ->join('location_view','person.state_id','=','location_view.l3_id')
            ->where('coverage_capture_images.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(coverage_capture_images.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(coverage_capture_images.date,'%Y-%m-%d') <='$to_date'")
            ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'dealer.name AS dealer_name',DB::raw("DATE_FORMAT(coverage_capture_images.date,'%d-%m-%Y') AS date"),'coverage_capture_images.time AS check_in','rolename AS role_name','l3_name','emp_code','l1_name','l2_name','coverage_capture_images.user_id','image_name1','image_name2','image_name3')
            ->groupBy('user_name','dealer_name','check_in')
            ->orderBy('date');
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data1->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $data1->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data1->whereIn('coverage_capture_images.user_id', $user);
            }
            $user_record = $data1->get();

           // dd($query);
            return view('reports.coverage_capture_images.ajax', [
                'records' => $user_record,
                'from_date' => $from_date,
                'to_date' => $to_date
            ]);

        }
    }

    

    #..............................................Modern Report ends here ...............................#

    public function distributorAjaxTargetReport(Request $request)
    {
    	$company_id = Auth::user()->company_id;
    	$auth_id = Auth::user()->id;
    	$month = $request->month;
    	$location_3 = $request->location_3;
    	$location_4 = $request->location_4;
    	$location_5 = $request->location_5;
    	$location_6 = $request->location_6;
    	$dealer = $request->dealer;
    	$catalog_2 = $request->catalog_2;
    	$catalog_product = $request->catalog_product;
    	// $explodeDate = explode(" -", $request->date_range_picker);
     //    $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
     //    $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
    	$user_data_data = DB::table('dealer_location_rate_list')
                    ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
                    ->join('person','person.id','=','dealer_location_rate_list.user_id')
                    ->join('users','users.id','=','dealer_location_rate_list.user_id')
                    ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
                    ->select(DB::raw("group_concat(Distinct person.id  SEPARATOR '|') as user_id"),DB::raw("group_concat(Distinct CONCAT_WS(' ',first_name,middle_name,last_name) SEPARATOR '|') as user_name"),'dealer.id as dealer_id','dealer.name as dealer_name','l5_id','l5_name','l6_id','l6_name','l3_name','l4_name')
                    ->where('dealer_location_rate_list.company_id',$company_id)
                    ->where('dealer_status',1)
                    ->where('is_admin','!=',1)
                    ->groupBy('dealer.id','dealer.name');

                    if(!empty($location_3))
                    {
                    	$user_data_data->whereIn('l3_id',$location_3);
                    }
                    if(!empty($location_4))
                    {
                    	$user_data_data->whereIn('l4_id',$location_4);
                    }
                    if(!empty($location_5))
                    {
                    	$user_data_data->whereIn('l5_id',$location_5);
                    }
                    if(!empty($location_6))
                    {
                    	$user_data_data->whereIn('l6_id',$location_6);
                    }
                    if(!empty($dealer))
                    {
                    	$user_data_data->whereIn('dealer.id',$dealer);
                    }
                   
    	$user_data = $user_data_data->get();
    	$master_target_data = DB::table('master_target')
    					->join('dealer','dealer.id','=','master_target.distributor_id')
                    	->join('dealer_location_rate_list','dealer.id','=','dealer_location_rate_list.dealer_id')
                        ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
    					->where('month',$month)
                        ->where('master_target.company_id',$company_id)
    					->where('flag',2)
    					->groupBy('product_id','distributor_id');
    					if(!empty($location_3))
	                    {
	                    	$master_target_data->whereIn('l3_id',$location_3);
	                    }
	                    if(!empty($location_4))
	                    {
	                    	$master_target_data->whereIn('l4_id',$location_4);
	                    }
	                    if(!empty($location_5))
	                    {
	                    	$master_target_data->whereIn('l5_id',$location_5);
	                    }
	                    if(!empty($location_6))
	                    {
	                    	$master_target_data->whereIn('l6_id',$location_6);
	                    }
	                    if(!empty($dealer))
	                    {
	                    	$master_target_data->whereIn('dealer.id',$dealer);
	                    }

		$master_target = $master_target_data->pluck(DB::raw("SUM(quantity_cases)"),DB::raw("CONCAT(product_id,dealer.id) as data"));
        // dd($master_target);
        $achievement_sale_data = DB::table('user_primary_sales_order')
                        ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                        ->join('dealer','dealer.id','=','user_primary_sales_order.dealer_id')
                        ->join('dealer_location_rate_list','dealer.id','=','dealer_location_rate_list.dealer_id')
                        ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                        ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
                        ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m')='$month'")
                        ->where('user_primary_sales_order.company_id',$company_id)
                        ->groupBy('product_id','dealer.id');
                        if(!empty($location_3))
                        {
                            $achievement_sale_data->whereIn('l3_id',$location_3);
                        }
                        if(!empty($location_4))
                        {
                            $achievement_sale_data->whereIn('l4_id',$location_4);
                        }
                        if(!empty($location_5))
                        {
                            $achievement_sale_data->whereIn('l5_id',$location_5);
                        }
                        if(!empty($location_6))
                        {
                            $achievement_sale_data->whereIn('l6_id',$location_6);
                        }
                        if(!empty($dealer))
                        {
                            $achievement_sale_data->whereIn('dealer.id',$dealer);
                        }

        $achievement_sale = $achievement_sale_data->pluck(DB::raw("SUM((cases+(quantity/quantity_per_case))*pr_rate) as sum_data"),DB::raw("CONCAT(product_id,dealer.id) as data"));
        // dd($achievement_sale);
        $category_data_data = DB::table('catalog_1')
                        ->where('status',1)
                        ->where('company_id',$company_id);
                        if(!empty($catalog_2))
                        {
                        	$category_data_data->whereIn('id',$catalog_2);
                        }
        $category_data = $category_data_data->pluck('name','id');
        foreach ($category_data as $key => $value) 
        {
            // $output .=",,,,,,,,,".$value.',';
            // dd($value);
            $out['id'] = $key;
            $out['name'] = $value;
            $sku_data_data = DB::table('catalog_product')
                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                        ->where('catalog_product.company_id',$company_id)
                        ->where('catalog_2.catalog_1_id',$key)
                        ->where('catalog_product.status',1)
                        ->orderBy('catalog_product.id','ASC');
                        if(!empty($catalog_product))
                        {
                        	$sku_data_data->whereIn('catalog_product.id',$catalog_product);
                        }
            $sku_data = $sku_data_data->pluck('catalog_product.name as name','catalog_product.id as id');
            $out['details'] = $sku_data;
            $f_out[] = $out;
        }
        return view('reports.target_db_report.gurujiAjax', [
                'records' => $user_data,
                'category_data'=> $category_data,
                'f_out' => $f_out,
                'month' => $month,
                'master_target'=> $master_target,
                'achievement_sale'=> $achievement_sale,
                // 'to_date' => $to_date
            ]);
    }




    public function dailyTrackingNehaReport(Request $request)
    {
        if ($request->ajax() && !empty($request->date_range_picker)) {

            $explodeDate = explode(" -", $request->date_range_picker);
            $company_id = Auth::user()->company_id;
            $state = $request->state;
            $city = $request->city;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            // $role_id = Auth::user()->is_admin;
            $array = array(99,100,101,102); // for oyster

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
                // Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }


            $trackingIntervals = array("10:00:00","11:00:00","12:00:00","13:00:00","14:00:00","15:00:00","16:00:00","17:00:00","18:00:00","19:00:00");

            // dd($trackingIntervals);


            $attLocation = DB::table('user_daily_attendance')
                ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') <='$to_date'")
                ->where('company_id',$company_id)
                ->pluck('track_addrs',DB::raw("CONCAT(DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d'),user_id) as concat"));


            $checkoutLocation = DB::table('check_out')
                ->whereRaw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(check_out.work_date,'%Y-%m-%d') <='$to_date'")
                ->where('company_id',$company_id)
                ->pluck('attn_address',DB::raw("CONCAT(DATE_FORMAT(check_out.work_date,'%Y-%m-%d'),user_id) as concat"));



            $monthlyTourProgram = DB::table('monthly_tour_program')
                ->join('location_7','location_7.id','=','monthly_tour_program.locations')
                ->whereRaw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d') <='$to_date'")
                ->where('monthly_tour_program.company_id',$company_id)
                ->where('location_7.company_id',$company_id)
                ->pluck('location_7.name',DB::raw("CONCAT(DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d'),person_id) as concat"));

                // dd($attLocation);


            $attWorkStatus = DB::table('user_daily_attendance')
                ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') <='$to_date'")
                ->where('user_daily_attendance.company_id',$company_id)
                ->where('_working_status.company_id',$company_id)
                ->pluck('_working_status.name',DB::raw("CONCAT(DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d'),user_id) as concat"));


            $attTime = DB::table('user_daily_attendance')
                ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') <='$to_date'")
                ->where('company_id',$company_id)
                ->pluck(DB::raw("DATE_FORMAT(user_daily_attendance.work_date,'%H:%i:%s') as time"),DB::raw("CONCAT(DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d'),user_id) as concat"));


            $checkoutTime = DB::table('check_out')
                ->whereRaw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(check_out.work_date,'%Y-%m-%d') <='$to_date'")
                ->where('company_id',$company_id)
                ->pluck(DB::raw("DATE_FORMAT(check_out.work_date,'%H:%i:%s') as time"),DB::raw("CONCAT(DATE_FORMAT(check_out.work_date,'%Y-%m-%d'),user_id) as concat"));



            // $constant = DB::table('_constant')->pluck('tracking_count');
            $constant = array("0"=>"10");
            $tracking = DB::table('user_work_tracking')->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as person_fullname'),'user_work_tracking.user_id','user_work_tracking.track_time','user_work_tracking.track_date','user_work_tracking.track_address as address','location_3.name as state_name','location_3.name as l3_name', 'location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','location_2.name as l2_name','rolename as role_name','person.person_id_senior','person.mobile','person_details.address')
                ->join('person','person.id','=','user_work_tracking.user_id')
                ->join('person_details','person_details.person_id','=','person.id')
                ->join('_role','_role.role_id','=','person.role_id')
                ->join('location_3','location_3.id','=','person.state_id')
                ->join('location_2','location_2.id','=','location_3.location_2_id')
                ->join('location_6', 'location_6.id', '=', 'person.town_id')
                ->join('location_5','location_5.id','=','location_6.location_5_id')
                ->join('location_4','location_4.id','=','location_5.location_4_id')
                // ->join('location_3','location_3.id','=','person.state_id')
                ->whereRaw("DATE_FORMAT(user_work_tracking.track_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_work_tracking.track_date,'%Y-%m-%d') <='$to_date'")
                // ->where('user_id','!=',4)
                ->where('user_work_tracking.company_id',$company_id)
                ->groupBy('user_work_tracking.user_id','user_work_tracking.track_date')
                ->orderBy('track_date','ASC');
                if($login_user == 2833){
                    $tracking->whereNotIn('location_3.id',$array);      
                 }
            #state filter
            if (!empty($state)) {
                $tracking->whereIn('location_3.id', $state);
            }
            #city filter
            if (!empty($city)) {
                $tracking->whereIn('user_sales_order_view.l3_id', $city);
            }
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $tracking->whereIn('location_3.id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $tracking->whereIn('location_4.id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $tracking->whereIn('location_5.id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $tracking->whereIn('location_6.id', $location_6);
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $tracking->whereIn('person.role_id', $role);
            }
            #User filter
            if (!empty($user)) {
                $tracking->whereIn('user_id', $user);
            }
            if (!empty($datasenior)) 
            {
                $tracking->whereIn('user_id', $datasenior);
            }

            $tracking_record = $tracking->get();

            $out=array();
           if (!empty($tracking_record)) 
           {
                
               

                    foreach ($tracking_record as $k => $d) {
                        $uid=$d->user_id;
                        $date=$d->track_date;
                         $out[$uid][$date][]= DB::table('user_work_tracking')
                         ->select('status','gps_status','battery_status','track_time','user_work_tracking.track_address as location')
                         ->where('user_work_tracking.company_id',$company_id)
                         ->where('track_date',$date)
                         ->where('user_id', $uid)
                         ->where('status','!=', 'Attendance')
                         ->where('status','!=', 'CheckOut')
                         ->groupBy(DB::raw('hour(track_time)'))
                         ->get();

                         
                    }
                    return view('reports.daily-tracking-neha.ajax', [
                        'records' => $tracking_record,
                        'constant' => $constant,
                        'from_date' => $from_date,
                        'to_date' => $to_date,
                        'track_time'=> $out,
                        'attLocation'=> $attLocation,
                        'checkoutLocation'=> $checkoutLocation,
                        'monthlyTourProgram'=> $monthlyTourProgram,
                        'attWorkStatus'=> $attWorkStatus,
                        'attTime'=> $attTime,
                        'checkoutTime'=> $checkoutTime,
                    ]);
                    // dd($out);
                
            }
            

        }
    }





    public function dailyTrackingLogReport(Request $request)
    {
        if ($request->ajax() && !empty($request->date_range_picker)) {

            $explodeDate = explode(" -", $request->date_range_picker);
            $company_id = Auth::user()->company_id;
            $state = $request->state;
            $city = $request->city;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            // $role_id = Auth::user()->is_admin;
            $array = array(99,100,101,102); // for oyster

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
                // Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }


            $constant = DB::table('_constant')->pluck('tracking_count');
            $tracking = DB::table('user_work_tracking_log')->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as person_fullname'),'user_work_tracking_log.user_id','user_work_tracking_log.track_time','user_work_tracking_log.track_date','user_work_tracking_log.track_address as address','location_3.name as state_name','location_3.name as l3_name', 'location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','location_2.name as l2_name','rolename as role_name','person.person_id_senior','person.mobile')
                ->join('person','person.id','=','user_work_tracking_log.user_id')
                ->join('_role','_role.role_id','=','person.role_id')
                ->join('location_3','location_3.id','=','person.state_id')
                ->join('location_2','location_2.id','=','location_3.location_2_id')
                ->join('location_6', 'location_6.id', '=', 'person.town_id')
                ->join('location_5','location_5.id','=','location_6.location_5_id')
                ->join('location_4','location_4.id','=','location_5.location_4_id')
                // ->join('location_3','location_3.id','=','person.state_id')
                ->whereRaw("DATE_FORMAT(user_work_tracking_log.track_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_work_tracking_log.track_date,'%Y-%m-%d') <='$to_date'")
                // ->where('user_id','!=',4)
                ->where('user_work_tracking_log.company_id',$company_id)
                ->groupBy('user_work_tracking_log.user_id','user_work_tracking_log.track_date')
                ->orderBy('track_date','ASC');
                if($login_user == 2833){
                    $tracking->whereNotIn('location_3.id',$array);      
                 }
            #state filter
            if (!empty($state)) {
                $tracking->whereIn('location_3.id', $state);
            }
            #city filter
            if (!empty($city)) {
                $tracking->whereIn('user_sales_order_view.l3_id', $city);
            }
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $tracking->whereIn('location_3.id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $tracking->whereIn('location_4.id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $tracking->whereIn('location_5.id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $tracking->whereIn('location_6.id', $location_6);
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $tracking->whereIn('person.role_id', $role);
            }
            #User filter
            if (!empty($user)) {
                $tracking->whereIn('user_id', $user);
            }
            if (!empty($datasenior)) 
            {
                $tracking->whereIn('user_id', $datasenior);
            }

            $tracking_record = $tracking->get();

            $out=array();
           if (!empty($tracking_record)) 
           {
           
                
                    foreach ($tracking_record as $k => $d) {
                        $uid=$d->user_id;
                        $date=$d->track_date;
                         $out[$uid][$date][]= DB::table('user_work_tracking_log')->select('status','gps_status','battery_status','track_time','user_work_tracking_log.track_address as location')->where('user_work_tracking_log.company_id',$company_id)->where('track_date',$date)->where('user_id', $uid)->get();
                         
                    }
                    return view('reports.daily-tracking.ajaxlog', [
                        'records' => $tracking_record,
                        'constant' => $constant,
                        'from_date' => $from_date,
                        'to_date' => $to_date,
                        'track_time'=> $out
                    ]);
                    // dd($out);
                
            }
            

        }
    }



    public function dailyTrackingKoyasReport(Request $request)
    {
        if ($request->ajax() && !empty($request->date_range_picker)) {

            $explodeDate = explode(" -", $request->date_range_picker);
            $company_id = Auth::user()->company_id;
            $state = $request->state;
            $city = $request->city;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

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
                // Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }


            $constant = DB::table('_constant')->pluck('tracking_count');
            $tracking = DB::table('user_work_tracking')->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as person_fullname'),'user_work_tracking.user_id','user_work_tracking.track_time','user_work_tracking.track_date','user_work_tracking.track_address as address','location_3.name as state_name','location_3.name as l3_name', 'location_4.name as l4_name','location_5.name as l5_name','location_6.name as l6_name','location_2.name as l2_name','rolename as role_name','person.person_id_senior','person.mobile')
                ->join('person','person.id','=','user_work_tracking.user_id')
                ->join('_role','_role.role_id','=','person.role_id')
                ->join('location_3','location_3.id','=','person.state_id')
                ->join('location_2','location_2.id','=','location_3.location_2_id')
                ->join('location_6', 'location_6.id', '=', 'person.town_id')
                ->join('location_5','location_5.id','=','location_6.location_5_id')
                ->join('location_4','location_4.id','=','location_5.location_4_id')
                // ->join('location_3','location_3.id','=','person.state_id')
                ->whereRaw("DATE_FORMAT(user_work_tracking.track_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_work_tracking.track_date,'%Y-%m-%d') <='$to_date'")
                // ->where('user_id','!=',4)
                ->where('user_work_tracking.company_id',$company_id)
                ->groupBy('user_work_tracking.user_id','user_work_tracking.track_date')
                ->orderBy('track_date','ASC');
              
            #state filter
            if (!empty($state)) {
                $tracking->whereIn('location_3.id', $state);
            }
            #city filter
            if (!empty($city)) {
                $tracking->whereIn('user_sales_order_view.l3_id', $city);
            }
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $tracking->whereIn('location_3.id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $tracking->whereIn('location_4.id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $tracking->whereIn('location_5.id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $tracking->whereIn('location_6.id', $location_6);
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $tracking->whereIn('person.role_id', $role);
            }
            #User filter
            if (!empty($user)) {
                $tracking->whereIn('user_id', $user);
            }
            if (!empty($datasenior)) 
            {
                $tracking->whereIn('user_id', $datasenior);
            }

            $tracking_record = $tracking->get();

          


             $trackDetails = DB::table('user_work_tracking')
                            ->select('status','gps_status','battery_status','track_time','track_date','user_id','user_work_tracking.track_address as location')
                            ->where('company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(user_work_tracking.track_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_work_tracking.track_date,'%Y-%m-%d') <='$to_date'")
                            ->groupBy('id')
                            ->get();

            $finalTrack = array();
            foreach ($trackDetails as $key => $value) {

                $finalTrack[$value->user_id.$value->track_date][] = $value;



                // $unix_time = strtotime($value->track_time); 
                // $startTime = $unix_time;


                // if($key == 0 || $value->status == 'Attendance'){
                // $finalTrack[$value->user_id.$value->track_date][] = $value;
                // $plusthirtytime = strtotime(date('H:i:s',strtotime('+60 minutes',$unix_time)));
                // }


                // if($plusthirtytime <= $startTime || $value->status == 'CheckOut'){
                // $finalTrack[$value->user_id.$value->track_date][] = $value;
                // $plusthirtytime = strtotime(date('H:i:s',strtotime('+60 minutes',$unix_time)));
                // }

            }

            // dd($finalTrack);
            $latestFinal = array();
            foreach ($finalTrack as $fkey => $fvalue) {

                $finalSubTrack = array();
                foreach ($fvalue as $ffkey => $ffvalue) {

                    $unix_time = strtotime($ffvalue->track_time); 
                    $startTime = $unix_time;

                    // $finalSubTrack[] = $ffvalue;


                    if($ffkey == 0 || $ffvalue->status == 'Attendance'){
                    $latestFinal[$fkey][] = $ffvalue;
                    $plusthirtytime = strtotime(date('H:i:s',strtotime('+60 minutes',$unix_time)));
                    }


                    if($plusthirtytime <= $startTime || $ffvalue->status == 'CheckOut'){
                    $latestFinal[$fkey][] = $ffvalue;
                    $plusthirtytime = strtotime(date('H:i:s',strtotime('+60 minutes',$unix_time)));
                    }


                }
               


            }


            // dd($latestFinal);




            // dd($finalTrack);


            $out=array();
           if (!empty($tracking_record)) 
           {
               

                    foreach ($tracking_record as $k => $d) {
                        $uid=$d->user_id;
                        $date=$d->track_date;

                        $finalDetails = !empty($latestFinal[$uid.$date])?$latestFinal[$uid.$date]:array();

                        $out[$uid][$date][] = $finalDetails;

                         // $out[$uid][$date][]= DB::table('user_work_tracking')->select('status','gps_status','battery_status','track_time','user_work_tracking.track_address as location')->where('user_work_tracking.company_id',$company_id)->where('track_date',$date)->where('user_id', $uid)->get();
                         
                    }
                    return view('reports.daily-tracking-koyas.ajax', [
                        'records' => $tracking_record,
                        'constant' => $constant,
                        'from_date' => $from_date,
                        'to_date' => $to_date,
                        'track_time'=> $out
                    ]);
                    // dd($out);
            }

        }
    }


}
