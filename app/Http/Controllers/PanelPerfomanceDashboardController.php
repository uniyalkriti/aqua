<?php

namespace App\Http\Controllers;

use App\_module;
use App\_subModule;
use App\Person;
use Illuminate\Http\Request;
use App\Dealer;
use App\Retailer;
use App\JuniorData;
use App\Location2;
use App\Location3;
use DB;
use DateTime;
use Auth;
use Session;
use App\UserSalesOrder;
use App\SecondarySale;
use Illuminate\Support\Facades\Crypt;
use App\ChallanOrder;

class PanelPerfomanceDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->menu = 'hello';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $check_dashboard = Session::get('dashboard');// this variable get the session values 
        $company_id = Auth::user()->company_id;
        // dd($check_dashboard[0]['is_set']);
        $current_menu='DASHBOARD ';
        $user=Auth::user();
        $person_name = DB::table('person')
        ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"))
        ->where('id',$user->id)
        ->where('company_id',$company_id)
        ->first();
 
        // dd($user->role_id);
       
        // dd($junior_data);
            
                
             // seesion check end here     
            $cdate=date('Y-m-d');
            $location_3_filter = $request->location3;
            $division_filter = $request->division;
            // dd($location_3_filter);
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
                // $last = date('')
            }
            // dd($from_date);
        $new_from_date = str_replace('-','',$from_date);
        $new_to_date = date('Y-m-d',strtotime($to_date .' +1 day')); 
        $new_to_date = str_replace('-','',$new_to_date);
        // $new_to_date = 
        // dd($new_to_date);
        $start = strtotime($from_date);
        $end = strtotime($to_date);    
        $datearray = array();
        $datediff =  ($end - $start)/60/60/24;
        $datearray[] = $from_date;

        for($i=0 ; $i<$datediff;$i++)
        {
            $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
        }

        $days = count($datearray);
        $month = date('Y-m');
        $m1=explode('-', $month);
        $y=$m1[0];
        $m2=$m1[1];
        if($m2<10)
        $m=ltrim($m2, '0');
        else
        $m=$m2;
        // $total_days=cal_days_in_month(CAL_GREGORIAN,$y,2005);
        $monthName=array('1' =>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');
        $monthName_id=array('1' =>'01','2'=>'02','3'=>'03','4'=>'04','5'=>'05','6'=>'06','7'=>'07','8'=>'08','9'=>'09','10'=>'10','11'=>'11','12'=>'12');

        for($i = 1; $i <=  '12' ; $i++)
        {
            $datesArr[] = $y . "-" . $monthName_id[$i];
            $datesDisplayArr[] =$y . "-" . $monthName[$i];
        }
        // dd($datesArr);
        // $startTime = strtotime($from_date);
        // $endTime = strtotime($to_date);
        // for ($currentDate = $startTime; $currentDate <= $endTime;  $currentDate += (86400))
        // {
        //     $Store = date('Y-m', $currentDate);
        //     $datesArr[] = $Store;
        //     $datesDisplayArr[] = $Store;
        // }
        // $new_from_date = '2020';
        $from_year = '2019';
        $to_year = '2021';
        $saleOrderValueData = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                        ->where('user_sales_order.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(date,'%Y')>='$from_year' and DATE_FORMAT(date,'%Y')<='$to_year'")
                        ->groupBy(DB::raw("DATE_FORMAT(date,'%Y-%m')"));
                        // if(!empty($division_filter))
                        // {
                        //     $saleOrderValueData->join('person','person.id','=','user_sales_order.user_id')->whereIn('person.division',$division_filter);
                           
                        // }  
                        // if(($days != '12'))
                        // {
                            $saleOrderValueData->whereBetween("user_sales_order.order_id",["$from_year","$to_year"]);
                            // $saleOrderValueData->whereBetween("user_sales_order.order_id",["2019","2021"]);
                            // $saleOrderValueData->whereBetween("user_sales_order.order_id",["2019","2021"]);
                        // }
                        // else{
                            // $saleOrderValueData->where("user_sales_order.order_id",'LIKE','%'.'2019'.'%');
                            // $saleOrderValueData->where("user_sales_order.order_id",'LIKE','%'.'2020'.'%');

                        // }
                        // if(!empty($junior_data))
                        // {
                        //     $saleOrderValueData->whereIn('user_sales_order.user_id',$junior_data);
                        // }
                        // if(!empty($location_3_filter))
                        // {
                        //     $saleOrderValueData->join('location_view','location_view.l5_id','=','user_sales_order.location_id')->whereIn('l2_id',$location_3_filter);
                           
                        // }  
                        
        $saleOrderValue = $saleOrderValueData->pluck(DB::raw("SUM(rate*quantity) as sale_value"),DB::raw("DATE_FORMAT(date,'%Y-%m')"));
        // dd($saleOrderValue);
        // $saleOrderValue = array();
        $role_wise_data = DB::table('user_sales_order')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->where('user_sales_order.company_id',$company_id)
                        ->where('person.company_id',$company_id)
                        // ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        // ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')  
                        ->select('rolename','person.role_id')
                        ->where('person_status','1')
                        // ->where('call_status','1')
                        // ->whereRaw("DATE_FORMAT(date,'%Y')='2020'")
                        // ->whereRaw("DATE_FORMAT(date,'%Y')>='2019' and DATE_FORMAT(date,'%Y')<='2020'")
                        ->groupBy('person.role_id');
                      
        // $role_wise_data->whereBetween("user_sales_order.order_id",["2019","2021"]);
        // $role_wise_data->where("user_sales_order.order_id",'LIKE','%'.'2020'.'%');

                       
                        
        $role_wise_data = $role_wise_data->get();
                        // dd($role_wise_data);
        foreach($datesArr as $dkey=>$dateVal)
        {
            // $totalChallanValue[]=ChallanOrder::where(DB::raw("(DATE_FORMAT(ch_date,'%Y-%m-%d'))"), "=", ''.$dateVal.'')->sum('amount');
            $totalChallanValue[]=0;

           
            $totalOrderValue1[]=!empty($saleOrderValue[$dateVal])?$saleOrderValue[$dateVal]:'';
            
        }
        for($i = 1; $i <=  '12' ; $i++)
        {
            $datesArr2[] = '2019' . "-" . $monthName_id[$i];
            // $datesDisplayArr[] =$y . "-" . $monthName[$i];
        }
        foreach($datesArr2 as $dkey=>$dateVal)
        {
            // $totalChallanValue[]=ChallanOrder::where(DB::raw("(DATE_FORMAT(ch_date,'%Y-%m-%d'))"), "=", ''.$dateVal.'')->sum('amount');
            $totalChallanValue[]=0;

           
            $totalOrderValue2[]=!empty($saleOrderValue[$dateVal])?$saleOrderValue[$dateVal]:'';
            
        }
        $totalOrderValue  = array();
        $totalOrderValue = array_map('round',$totalOrderValue1);
        $location3 = location3::where('status',1)->pluck('name','id');
        // $division = DB::table('_product_division')->pluck('type','id')->toArray();
        $location_1 = DB::table('location_3')
                    ->where('company_id',$company_id)
                    ->where('status',1)
                    ->orderBy('id','ASC')
                    ->pluck('name','id')
                    ->toArray();

        $location_1_display = DB::table('location_3')
                    ->where('company_id',$company_id)
                    ->where('status',1)
                    ->orderBy('id','ASC')
                    ->pluck('name','id')
                    ->toArray();
        // $dashboard_arr this array push the dashboard values in session

        // $dashboard_arr array ends here

        return view('perfomance',
            [
                'menu' => $this->menu,
                'current_menu' => $current_menu,
                'location_1_display'=> $location_1_display,
                'location_1'=> $location_1,
                'blank_array' => array(),
               //  'totalSalesTeam' => $totalSalesTeam,
               //  'totalDistributor'=>$totalDistributor,
               //  'totalOutlet'=>$totalOutlet,
               //  'roleWiseTeam'=>$roleWiseTeam,
               //  'location_3_filter'=>'',
               //  'division_filter'=>'',
                'role_wise_data'=> $role_wise_data,
                'totalOrderValue2'=> $totalOrderValue2,
               'from_date'=> $from_date,
               'to_date'=> $to_date,
               //  'catalog1Sale'=>$catalog1Sale,
               //  'location3' => $location3,
               //  'division' => $division,
                // 'catalog1challan'=>$catalog1challan,
                'datesArr'=>$datesDisplayArr,
                'totalOrderValue'=>$totalOrderValue,
                'totalChallanValue'=>$totalChallanValue,
                // 'totalAttd'=>$totalAttd,
                // 'totalOrder'=>$totalOrder,
                // 'totalPrimaryOrder'=>$totalPrimaryOrder,
                'mdate' =>$mdate,
                // 'location_5'=>$location_5,
                // 'totalDistributorSale'=>$totalDistributorSale,
                // 'work_status_array'=> $work_status_array,
                // 'totalOutletSale' =>$totalOutletSale,
                // 'totalBeatSale' => $totalBeatSale,
                // 'totalCall' => $totalCall,
                // 'role_wise_attendance'=> $role_wise_attendance,
                // 'attendance_details'=> $attendance_details,
                // 'location_3_filter'=> $location_3_filter,
                // 'division_filter'=> $division_filter,

                // 'productiveCall' => $productiveCall
            ]);
  

        

    }
    public function get_role_id_sale_value(Request $request)
    {
        $role_id = $request->role_id;
        $from_year = !empty($request->from_year)?$request->from_year:'2019';
        $to_year = !empty($request->to_year)?$request->to_year:'2021';
        $company_id = Auth::user()->company_id;
        $role_wise_data = DB::table('user_sales_order')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')  
                        ->select('rolename','person.role_id',DB::raw("SUM(rate*quantity) as sale_value"),DB::raw("DATE_FORMAT(date,'%Y') as year"))
                        ->where('person_status','1')
                        ->where('user_sales_order.company_id',$company_id)
                        // ->whereRaw("DATE_FORMAT(date,'%Y')='2020'")
                        ->where('person.role_id',$role_id)
                        ->whereRaw("DATE_FORMAT(date,'%Y')>='$from_year' and DATE_FORMAT(date,'%Y')<='$to_year'")
                        // ->whereRaw("DATE_FORMAT(date,'%Y')>='2019' and DATE_FORMAT(date,'%Y')<='2020'")
                        ->groupBy('person.role_id',DB::raw("DATE_FORMAT(date,'%Y')"));
                      
        $role_wise_data->whereBetween("user_sales_order.order_id",["$from_year","$to_year"]);
        // $role_wise_data->whereBetween("user_sales_order.order_id",["2019","2021"]);
        // $role_wise_data->where("user_sales_order.order_id",'LIKE','%'.'2020'.'%');

                       
                        
        $role_wise_data = $role_wise_data->get();
        // dd($role_wise_data);
        $data['code'] = 200;
        $data['result'] = $role_wise_data;
        $data['message'] = 'done';
        return json_encode($data);

    }
    public function get_location_1_id_sale_value(Request $request)
    {
        $l2_id = $request->l2_id;
        $from_year = !empty($request->from_year)?$request->from_year:'2019';
        $to_year = !empty($request->to_year)?$request->to_year:'2021';
        $company_id = Auth::user()->company_id;
        $role_wise_data = DB::table('user_sales_order')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->join('location_3','location_3.id','=','person.state_id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')  
                        ->select(DB::raw("SUM(rate*quantity) as sale_value"),DB::raw("DATE_FORMAT(date,'%Y') as year"))
                        ->where('person_status','1')
                        ->where('user_sales_order.company_id',$company_id)
                        // ->whereRaw("DATE_FORMAT(date,'%Y')='2020'")
                        ->where('person.state_id',$l2_id)
                        ->whereRaw("DATE_FORMAT(date,'%Y')>='$from_year' and DATE_FORMAT(date,'%Y')<='$to_year'")
                        // ->whereRaw("DATE_FORMAT(date,'%Y')>='2019' and DATE_FORMAT(date,'%Y')<='2020'")
                        ->groupBy('person.state_id',DB::raw("DATE_FORMAT(date,'%Y')"));
                      
        $role_wise_data->whereBetween("user_sales_order.order_id",["$from_year","$to_year"]);
        // $role_wise_data->whereBetween("user_sales_order.order_id",["2019","2021"]);
        // $role_wise_data->where("user_sales_order.order_id",'LIKE','%'.'2020'.'%');

                       
                        
        $role_wise_data = $role_wise_data->get();
        // dd($role_wise_data);
        $data['code'] = 200;
        $data['result'] = $role_wise_data;
        $data['message'] = 'done';
        return json_encode($data);

    }
    public function get_user_details_perfomance(Request $request)
    {
        $role_id = $request->role_id;
        $from_year = !empty($request->from_year)?$request->from_year:'2019';
        $to_year = !empty($request->to_year)?$request->to_year:'2021';
        $company_id = Auth::user()->company_id;
        $role_wise_data = DB::table('user_sales_order')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')  
                        ->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("SUM(rate*quantity) as sale_value"),DB::raw("DATE_FORMAT(date,'%Y') as year"))
                        ->where('person_status','1')
                        ->where('user_sales_order.company_id',$company_id)
                        // ->whereRaw("DATE_FORMAT(date,'%Y')='2020'")
                        ->where('person.role_id',$role_id)
                        ->whereRaw("DATE_FORMAT(date,'%Y')>='$from_year' and DATE_FORMAT(date,'%Y')<='$to_year'")
                        // ->whereRaw("DATE_FORMAT(date,'%Y')>='2019' and DATE_FORMAT(date,'%Y')<='2020'")
                        ->groupBy('person.id',DB::raw("DATE_FORMAT(date,'%Y')"));
                      
        $role_wise_data->whereBetween("user_sales_order.order_id",["$from_year","$to_year"]);
        // $role_wise_data->whereBetween("user_sales_order.order_id",["2019","2021"]);
        // $role_wise_data->where("user_sales_order.order_id",'LIKE','%'.'2020'.'%');

                       
                        
        $role_wise_data = $role_wise_data->get();
        // dd($role_wise_data);

        foreach ($role_wise_data as $key => $value) 
        {
            // if()
            $user_id = $value->user_id;

            $out[$user_id][] = $value;
            // $out['']
            // foreach ($role_wise_data as $key => $value) {
            //     # code...
            // }
        }
        // dd($out);
        // dd($role_wise_data);
        $data['code'] = 200;
        $data['result'] = $out;
        $data['message'] = 'done';
        return json_encode($data);

    }
    public function get_state_wise_sale(Request $request)
    {
        $state_id = $request->state_id;
        $monthName=array('1' =>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');
        $monthName_id=array('1' =>'01','2'=>'02','3'=>'03','4'=>'04','5'=>'05','6'=>'06','7'=>'07','8'=>'08','9'=>'09','10'=>'10','11'=>'11','12'=>'12');
        $from_year = !empty($request->from_year)?$request->from_year:'2019';
        $to_year = !empty($request->to_year)?$request->to_year:'2021';
        for($i = 1; $i <=  '12' ; $i++)
        {
            $datesArr[] = $to_year . "-" . $monthName_id[$i];
            $datesDisplayArr[] =$to_year . "-" . $monthName[$i];
        }
        for($i = 1; $i <=  '12' ; $i++)
        {
            $datesArr2[] = $from_year . "-" . $monthName_id[$i];
        }
        $company_id = Auth::user()->company_id;
        $role_wise_data = DB::table('user_sales_order')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->join('location_3','location_3.id','=','person.state_id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')  
                        // ->select('location_1.name as state','location_1.id',,)
                        ->where('user_sales_order.company_id',$company_id)
                        ->where('person_status','1')
                        // ->whereRaw("DATE_FORMAT(date,'%Y')='2020'")
                        // ->where('person.role_id',$role_id)
                        ->whereRaw("DATE_FORMAT(date,'%Y')>='$from_year' and DATE_FORMAT(date,'%Y')<='2021'")
                        ->groupBy(DB::raw("DATE_FORMAT(date,'%Y')"),'location_3.id');
                      
        $role_wise_data->whereBetween("user_sales_order.order_id",["$from_year","$to_year"]);
        // $role_wise_data->where("user_sales_order.order_id",'LIKE','%'.'2020'.'%');
        $role_wise_data = $role_wise_data->pluck(DB::raw("SUM(rate*quantity) as sale_value"),DB::raw("concat(DATE_FORMAT(date,'%Y'),location_3.id) as id"));

        $location_1_show = DB::table('location_3')
                        ->where('status',1)
                        ->where('company_id',$company_id)
                        ->orderBy('id','ASC')
                        ->groupBy('location_3.id')
                        ->pluck('name')
                        ->toArray();
        $location_1 = DB::table('location_3')
                    ->where('company_id',$company_id)
                    ->where('status',1)
                    ->orderBy('id','ASC')
                    ->pluck('name','id')
                    ->toArray();
        // dd($location_1_show);
        foreach($location_1 as $dkey=>$dateVal)
        {
            $dateValF = '2019'.$dkey;
            // dd($dateValF);
            $totalOrderValue1[]=!empty($role_wise_data[$dateValF])?$role_wise_data[$dateValF]:'';
        
            // $totalOrderValue2[]=!empty($role_wise_data[$dateVal])?$role_wise_data[$dateVal]:'';
          
        }
        foreach($location_1 as $dkey=>$dateVal)
        {
            $dateValK = '2020'.$dkey;
            $totalOrderValue2[]=!empty($role_wise_data[$dateValK])?$role_wise_data[$dateValK]:'';
            
        }
        // dd($location_1_show);
        $data['code'] = 200;
        $data['result_from_year'] = $totalOrderValue1;
        $data['datesArr'] = $location_1_show;
        $data['result_to_year'] = $totalOrderValue2;
        $data['message'] = 'done';
        return json_encode($data);

    }

    // public function getJuniorUser($code)
    // {
    //     $res1="";
    //     $res2="";
    //     $details = DB::table('person')
    //         ->join('person_login','person_login.person_id','=','person.id')
    //         ->where('person_id_senior',$code)
    //         ->where('person_login.person_status','=','1')
    //         ->select('person.id as user_id')
    //         ->get();
    //     $num = count($details);  
    //     if($num>0)
    //     {
    //         foreach($details as $key=>$res2)
    //         {
    //             if($res2->user_id!="")
    //             {
    //                 //$product = collect([1,2,3,4]);
    //                 Session::push('juniordata', $res2->user_id);
    //                // $_SESSION['juniordata'][]=$res2->user_id;
    //                 $this->getJuniorUser($res2->user_id);
    //             }
    //         }
            
    //     }
    //     else
    //     {
    //         foreach($details as $key1=>$res1)
    //         {
    //             if($res1->user_id!="")
    //             {
    //                 Session::push('juniordata', $res1->user_id);
    //                 // $_SESSION['juniordata'][]= $res1->user_id;
    //             }
    //         }

            
    //     }
    //     return 1;
    // }


    public function getTotalSalesTeamHome(Request $request)
    {
        $user=Auth::user();
        // $company_id = Auth::user()->company_id;

        if($user->role_id==1)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        // dd($junior_data);
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $division_filter = !empty($request->division_id)?explode(',',$request->division_id):'';
        // $location_3_filter = $request->state_id;
        $user_details_data = DB::table('person')
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    // ->join('users','users.id','=','person.id')
                    ->leftJoin('location_1','location_1.id','=','person.state_id')
                    ->select('person.id as user_id','person_id_senior',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'location_1.name as state','_role.rolename as role','mobile',DB::raw("(SELECT CONCAT_WS(' ',first_name,middle_name,last_name) from person where person.id = person_id_senior LIMIT 1) as senior_name"))
                    ->where('person_status','=','1');
                    // ->where('person_status',1);
                    if(!empty($junior_data))
                    {
                        $user_details_data->whereIn('person.id',$junior_data);
                    }   
                    if(!empty($location_3_filter))
                    {
                        $user_details_data->whereIn('person.location_2_id',$location_3_filter);
                    } 
                    if(!empty($division_filter))
                    {
                        $user_details_data->whereIn('person.division',$division_filter);
                    } 

        $user_details = $user_details_data->get();

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
                $f_out[] = $out;
            }
            // dd($not_visit_list_query_15);
            $data['user_details'] = $f_out;
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
        if($user->role_id==1)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $division_filter = !empty($request->division_id)?explode(',',$request->division_id):'';
        // $location_3_filter = $request->state_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $new_from_date = str_replace('-','',$from_date);
        $new_to_date = str_replace('-','',$to_date);


        $start = strtotime($from_date);
        $end = strtotime($to_date);    
        $datearray = array();
        $datediff =  ($end - $start)/60/60/24;
        $datearray[] = $from_date;

        for($i=0 ; $i<$datediff;$i++)
        {
            $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
        }

        $days = count($datearray);



        $beat_coverage_detailsData = DB::table('user_sales_order')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                // ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                                ->join('location_5','location_5.id','=','user_sales_order.location_id')
                                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                                // ->whereBetween("user_sales_order_details.order_id",["$new_from_date","$new_to_date"])
                                ->select('location_5.name as beat',DB::raw("SUM(rate*quantity) as total_sale_value"))
                                ->groupBy('user_sales_order.location_id');
                                if(($days != '1'))
                                {
                                    // dd('q');
                                    $beat_coverage_detailsData->whereBetween("user_sales_order.order_id",["$new_from_date","$new_to_date"]);
                                }
                                else{
                                    $beat_coverage_detailsData->where("user_sales_order.order_id",'LIKE','%'.$new_from_date.'%');

                                }
                                if(!empty($junior_data))
                                {
                                    $beat_coverage_detailsData->whereIn('user_sales_order.user_id',$junior_data);
                                }
                                if(!empty($location_3_filter))
                                {
                                    $beat_coverage_detailsData->join('location_view','location_view.l5_id','=','user_sales_order.location_id')->whereIn('l2_id',$location_3_filter);
                                }
                                 if(!empty($division_filter))
                                {
                                    $beat_coverage_detailsData->join('person','person.id','=','user_sales_order.user_id')->whereIn('person.division',$division_filter);
                                }
                                $beat_coverage_details = $beat_coverage_detailsData->get();
        
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
        if($user->role_id==1)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $division_filter = !empty($request->division_id)?explode(',',$request->division_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $new_from_date = str_replace('-','',$from_date);
        $new_to_date = str_replace('-','',$to_date);

        $start = strtotime($from_date);
        $end = strtotime($to_date);    
        $datearray = array();
        $datediff =  ($end - $start)/60/60/24;
        $datearray[] = $from_date;

        for($i=0 ; $i<$datediff;$i++)
        {
            $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
        }

        $days = count($datearray);


        $productve_call_details_data = DB::table('user_sales_order')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                ->join('person','person.id','=','user_sales_order.user_id')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->select('user_id',DB::raw('SUM(rate*quantity) as total_sale_value'),DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'mobile')
                                ->where('call_status',1)
                                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                                // ->whereBetween("user_sales_order_details.order_id",["$new_from_date","$new_to_date"])
                                ->groupBy('user_id');
                                if(($days != '1'))
                                {
                                    // dd('q');
                                    $productve_call_details_data->whereBetween("user_sales_order.order_id",["$new_from_date","$new_to_date"]);
                                }
                                else{
                                    $productve_call_details_data->where("user_sales_order.order_id",'LIKE','%'.$new_from_date.'%');

                                }
                                if(!empty($junior_data))
                                {
                                    $productve_call_details_data->whereIn('user_sales_order.user_id',$junior_data);
                                }
                                if(!empty($location_3_filter))
                                {
                                    $productve_call_details_data->join('location_view','location_view.l5_id','=','user_sales_order.location_id')->whereIn('l2_id',$location_3_filter);
                                }
                                 if(!empty($division_filter))
                                {
                                    $productve_call_details_data->whereIn('person.division',$division_filter);
                                }
        $productve_call_details = $productve_call_details_data->get();
       
        if(!empty($productve_call_details))
        {
            $call_status_count_data = DB::table('user_sales_order')
                            ->where('call_status',1)
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                            ->groupBy('user_id');
                            if(!empty($junior_data))
                            {
                                $call_status_count_data->whereIn('user_sales_order.user_id',$junior_data);
                            }
                            if(!empty($location_3_filter))
                            {
                                $call_status_count_data->join('location_view','location_view.l5_id','=','user_sales_order.location_id')->whereIn('l2_id',$location_3_filter);
                            }
                            if(!empty($division_filter))
                            {
                                $call_status_count_data->join('person','person.id','=','user_sales_order.user_id')->whereIn('person.division',$division_filter);
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
        if($user->role_id==1)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $division_filter = !empty($request->division_id)?explode(',',$request->division_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        // $company_id = Auth::user()->company_id;
        $new_from_date = str_replace('-','',$from_date);
        $new_to_date = str_replace('-','',$to_date);

        $start = strtotime($from_date);
        $end = strtotime($to_date);    
        $datearray = array();
        $datediff =  ($end - $start)/60/60/24;
        $datearray[] = $from_date;

        for($i=0 ; $i<$datediff;$i++)
        {
            $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
        }
        $days = count($datearray);



        $beat_query_data = DB::table('user_sales_order')
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                    ->join('location_view','location_view.l5_id','=','user_sales_order.location_id')
                    ->select(DB::raw("SUM(rate*quantity) as data"),'l1_name as label1',DB::raw("CONCAT(l1_name,' ',' : ',' ',' [ ',' ',round(SUM(rate*quantity),2),' ',' ] ') as label") )
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    // ->whereBetween("user_sales_order_details.order_id",["$new_from_date","$new_to_date"])
                    ->groupBy('l1_id');
                     if(($days != '1'))
                    {
                        // dd('q');
                        $beat_query_data->whereBetween("user_sales_order.order_id",["$new_from_date","$new_to_date"]);
                    }
                    else{
                        $beat_query_data->where("user_sales_order.order_id",'LIKE','%'.$new_from_date.'%');

                    }
                    if(!empty($junior_data))
                    {
                        $beat_query_data->whereIn('user_sales_order.user_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $beat_query_data->whereIn('l2_id',$location_3_filter);
                    }
                    if(!empty($division_filter))
                    {
                        $beat_query_data->join('person','person.id','=','user_sales_order.user_id')->whereIn('person.division',$division_filter);
                    }
        $beat_query =  $beat_query_data->get();
       
        if(!empty($beat_query))
        {
            
            $data['beat_query'] = $beat_query;
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
        if($user->role_id==1)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $division_filter = !empty($request->division_id)?explode(',',$request->division_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        // $company_id = Auth::user()->company_id;

        // $beat_query_data = DB::table('purchase_order')
        //                 ->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
        //                 ->join('purchase_order_details as pod','pod.purchase_inv','=','purchase_order.challan_no')
        //                 ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','purchase_order.dealer_id')
        //                 ->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
        //                 ->select(DB::raw("ROUND(SUM(purchase_order_details.rate*purchase_order_details.quantity),2) as data"),'l1_name as label1',DB::raw("CONCAT(l1_name,' ',' : ',' ',' [ ',' ',round(SUM(purchase_order_details.rate*purchase_order_details.quantity),2),' ',' ] ') as label") )
        //                 ->whereRaw("DATE_FORMAT(purchase_order.order_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(purchase_order.order_date,'%Y-%m-%d')<='$to_date'")
        //                 ->groupBy('l1_id');
        //                 if(!empty($location_3_filter))
        //                 {
        //                     $beat_query_data->whereIn('l1_id',$location_3_filter);
        //                 }

        // $beat_query =  $beat_query_data->get();
        
        // $beat_query_data = DB::table('primary_sale_view')
        //             ->join('dealer','dealer.id','=','primary_sale_view.dealer_id')
        //             ->join('location_3','location_3.id','=','dealer.state_id')
        //             ->select(DB::raw("SUM(rate*quantity) as data"),'location_3.name as label1',DB::raw("CONCAT(location_1.name,' ',' : ',' ',' [ ',' ',round(SUM(rate*quantity),2),' ',' ] ') as label") )
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
        $beat_query =  array();
        
        if(!empty($beat_query))
        {
            
            $data['beat_query'] = $beat_query;
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
        if($user->role_id==1)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $division_filter = !empty($request->division_id)?explode(',',$request->division_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        // $company_id = Auth::user()->company_id;
        // dd($location_3_filter);
        $data_query_data = Dealer::select('dealer.name as dealer_name','dealer.other_numbers as mobile','l1_name',DB::raw("GROUP_CONCAT(DISTINCT l4_name) as l4_name"),'dealer.id as dealer_id')
                ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                ->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
                ->where('dealer_status',1)
                ->groupBy('dealer_id');
                if(!empty($location_3_filter))
                {
                    $data_query_data->whereIn('l2_id',$location_3_filter);
                }
                 if(!empty($division_filter))
                {
                    $data_query_data->join('person','person.id','=','dealer_location_rate_list.user_id')->whereIn('person.division',$division_filter);
                }
                if(!empty($junior_data))
                {
                    $data_query_data->whereIn('dealer_location_rate_list.user_id',$junior_data);
                }
        $data_query = $data_query_data->get();
        $data_retailer_data = DB::table('retailer')
                        ->where('retailer_status',1)
                        ->groupBy('retailer.dealer_id');
                        if(!empty($location_3_filter))
                        {
                            $data_retailer_data->join('location_view','location_view.l5_id','=','retailer.location_id');
                            $data_retailer_data->whereIn('l2_id',$location_3_filter);
                        }
                        if(!empty($junior_data))
                        {
                            $data_retailer_data->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','retailer.dealer_id')
                                            // ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                                            ->whereIn('dealer_location_rate_list.user_id',$junior_data);
                        }
                         if(!empty($division_filter))
                        {
                            $data_retailer_data->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','retailer.dealer_id')
                                            ->join('person','person.id','=','dealer_location_rate_list.user_id')
                                            ->whereIn('person.division',$division_filter);
                        }

        $data_retailer = $data_retailer_data->pluck(DB::raw("COUNT(distinct retailer.id) as retailer_id"),'retailer.dealer_id');

        $data_beat_data= DB::table('dealer_location_rate_list')
                        ->groupBy('dealer_id');
                        if(!empty($location_3_filter))
                        {
                            $data_beat_data->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id');
                            $data_beat_data->whereIn('l2_id',$location_3_filter);
                        }
                        if(!empty($division_filter))
                        {
                            $data_beat_data->join('person','person.id','=','dealer_location_rate_list.user_id');
                            $data_beat_data->whereIn('person.division',$division_filter);
                        }
                        if(!empty($junior_data))
                        {
                            $data_beat_data->whereIn('dealer_location_rate_list.user_id',$junior_data);
                        }
        $data_beat = $data_beat_data->pluck(DB::raw("COUNT(distinct location_id) as retailer_id"),'dealer_id');
                        
        $data_user_name_data = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                        ->where('person_status','=','1')
                        // ->where('person_status',1)
                        ->groupBy('dealer_id');
                        if(!empty($location_3_filter))
                        {
                            $data_user_name_data->whereIn('person.location_2_id',$location_3_filter);
                        }
                        if(!empty($division_filter))
                        {
                            $data_user_name_data->whereIn('person.division',$division_filter);
                        }
                        if(!empty($junior_data))
                        {
                            $data_user_name_data->whereIn('person.id',$junior_data);
                        }
        $data_user_name = $data_user_name_data->pluck(DB::raw("GROUP_CONCAT(DISTINCT CONCAT_WS(' ',first_name,middle_name,last_name)) as user_name"),'dealer_id');

       
        // $dealer_sale_data = DB::table('user_sales_order')
        //                 ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        //                 ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        //                 ->groupBy('dealer_id');
        //             if(!empty($junior_data))
        //             {
        //                 $dealer_sale_data->whereIn('user_sales_order.user_id',$junior_data);
        //             }
        //             if(!empty($location_3_filter))
        //             {
        //                 $dealer_sale_data->join('location_view','location_view.l5_id','=','user_sales_order.location_id');
        //                 $dealer_sale_data->whereIn('l1_id',$location_3_filter);
        //             }
        // $dealer_sale = $dealer_sale_data->pluck(DB::raw("sum(rate*quantity) as saleorder"),'dealer_id');
       
       
        if(!empty($data_query))
        {
            $f_out = [];
            foreach ($data_query as $key => $value) {
                $out['dealer_id'] = $value->dealer_id;
                $out['dealer_name'] = $value->dealer_name;
                $out['l3_name'] = $value->l1_name;
                $out['l6_name'] = $value->l4_name;
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
        if($user->role_id==1)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $division_filter = !empty($request->division_id)?explode(',',$request->division_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        // $company_id = Auth::user()->company_id;
        
        $data_query_data = Dealer::select('dealer.name as dealer_name','dealer.other_numbers as mobile','l3_name',DB::raw("GROUP_CONCAT(DISTINCT l6_name) as l6_name"),'dealer.id as dealer_id')
                ->join('user_sales_order','user_sales_order.dealer_id','=','dealer.id')
                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                ->join('location_view','location_view.l5_id','=','user_sales_order.location_id')
                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                ->where('dealer_status',1)
                ->groupBy('dealer_id');
                if(!empty($location_3_filter))
                {
                    $data_query_data->whereIn('l2_id',$location_3_filter);
                }
                if(!empty($division_filter))
                {
                    $data_query_data->join('person','person.id','=','user_sales_order.user_id')->whereIn('person.division',$division_filter);
                }
        $data_query = $data_query_data->get();
        $data_retailer_data = DB::table('retailer')
                        ->where('retailer_status',1)
                        ->groupBy('dealer_id');
                        if(!empty($location_3_filter))
                        {
                            $data_retailer_data->join('location_view','location_view.l5_id','=','retailer.location_id');
                            $data_retailer_data->whereIn('l2_id',$location_3_filter);
                        }
                         if(!empty($division_filter))
                        {
                            $data_retailer_data->join('dealer','dealer.id','=','retailer.dealer_id');
                            $data_retailer_data->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id');
                            $data_retailer_data->join('person','person.id','=','dealer_location_rate_list.user_id');
                            $data_retailer_data->whereIn('person.division',$division_filter);
                        }
        $data_retailer = $data_retailer_data->pluck(DB::raw("COUNT(distinct retailer.id) as retailer_id"),'dealer_id');

        $data_beat_data= DB::table('dealer_location_rate_list')
                        ->groupBy('dealer_id');
                        if(!empty($location_3_filter))
                        {
                            $data_beat_data->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id');
                            $data_beat_data->whereIn('l2_id',$location_3_filter);
                        }
                         if(!empty($division_filter))
                        {
                            $data_beat_data->join('person','person.id','=','dealer_location_rate_list.user_id');
                            $data_beat_data->whereIn('person.division',$division_filter);
                        }
        $data_beat = $data_beat_data->pluck(DB::raw("COUNT(distinct location_id) as retailer_id"),'dealer_id');
                        
        $data_user_name_data = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                        ->where('person_status','=','1')
                        // ->where('person_status',1)
                        ->groupBy('dealer_id');
                        if(!empty($location_3_filter))
                        {
                            $data_user_name_data->whereIn('person.location_2_id',$location_3_filter);
                        }
                        if(!empty($division_filter))
                        {
                            $data_user_name_data->whereIn('person.division',$division_filter);
                        }
        $data_user_name = $data_user_name_data->pluck(DB::raw("GROUP_CONCAT(DISTINCT CONCAT_WS(' ',first_name,middle_name,last_name)) as user_name"),'dealer_id');

        
        $dealer_sale_data = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        ->groupBy('dealer_id');
                    if(!empty($junior_data))
                    {
                        $dealer_sale_data->whereIn('user_sales_order.user_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $dealer_sale_data->join('location_view','location_view.l5_id','=','user_sales_order.location_id');
                        $dealer_sale_data->whereIn('l2_id',$location_3_filter);
                    }
                     if(!empty($division_filter))
                    {
                        $dealer_sale_data->join('person','person.id','=','user_sales_order.user_id');
                        $dealer_sale_data->whereIn('person.division',$division_filter);
                    }
        $dealer_sale = $dealer_sale_data->pluck(DB::raw("sum(rate*quantity) as saleorder"),'dealer_id');
        
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
        if($user->role_id==1)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $division_filter = !empty($request->division_id)?explode(',',$request->division_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        // $company_id = Auth::user()->company_id;
        
        $data_query_data = Retailer::select('created_on','retailer.name as retailer_name','retailer.landline as mobile','dealer.name as dealer_name','l4_name','l5_name','l1_name','dealer.id as dealer_id','retailer.id as retailer_id')
                ->join('dealer','dealer.id','=','retailer.dealer_id')
                ->join('location_view','location_view.l5_id','=','retailer.location_id')
                ->where('retailer_status',1)
                ->where('dealer_status',1)
                ->groupBy('retailer.id');
                if(!empty($location_3_filter))
                {
                    $data_query_data->whereIn('l2_id',$location_3_filter);
                }
                if(!empty($junior_data))
                {
                    $data_query_data->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','retailer.dealer_id')
                                    ->whereIn('dealer_location_rate_list.user_id',$junior_data);
                }
                  if(!empty($division_filter))
                {
                    $data_query_data->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','retailer.dealer_id')->join('person','person.dealer_id','=','dealer_location_rate_list.user_id')->whereIn('person.division',$division_filter);
                }
        $data_query = $data_query_data->get();
        $added__by_person_data = DB::table('retailer')
                        ->join('person','person.id','=','retailer.created_by_person_id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->where('person_status','=','1')
                        ->where('retailer_status',1)
                        ->groupBy('retailer.id');
                        if(!empty($location_3_filter))
                        {
                            $added__by_person_data->whereIn('person.location_2_id',$location_3_filter);
                        }
                        if(!empty($division_filter))
                        {
                            $added__by_person_data->whereIn('person.division',$division_filter);
                        }
                        if(!empty($junior_data))
                        {
                            $added__by_person_data->whereIn('person_login.person_id',$junior_data);
                        }
        $added__by_person = $added__by_person_data->pluck(DB::raw("GROUP_CONCAT(DISTINCT CONCAT_WS(' ',first_name,middle_name,last_name)) as user_name"),'retailer.id');

        $assin_user_retailer= DB::table('dealer_location_rate_list')
                        ->join('retailer','dealer_location_rate_list.location_id','=','retailer.location_id')
                        ->join('person','dealer_location_rate_list.user_id','=','person.id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('users','users.id','=','person.id')
                        ->groupBy('retailer.id');
                        if(!empty($location_3_filter))
                        {
                            $assin_user_retailer->whereIn('person.location_2_id',$location_3_filter);
                        }
                         if(!empty($division_filter))
                        {
                            $assin_user_retailer->whereIn('person.division',$division_filter);
                        }
                        if(!empty($junior_data))
                        {
                            $assin_user_retailer->whereIn('person_login.person_id',$junior_data);
                        }
        $assin_user_retailer_data = $assin_user_retailer->pluck(DB::raw("GROUP_CONCAT(DISTINCT CONCAT_WS(' ',first_name,middle_name,last_name)) as user_name"),'retailer.id');
                        
        
       
        // $retailer_sale_data = DB::table('user_sales_order')
        //                 ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        //                 ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        //                 ->groupBy('retailer_id');
        //             if(!empty($junior_data))
        //             {
        //                 $retailer_sale_data->whereIn('user_sales_order.user_id',$junior_data);
        //             }
        //             if(!empty($location_3_filter))
        //             {
        //                 $retailer_sale_data->join('location_view','location_view.l5_id','=','user_sales_order.location_id');
        //                 $retailer_sale_data->whereIn('l1_id',$location_3_filter);
        //             }
        // $retailer_sale = $retailer_sale_data->pluck(DB::raw("sum(rate*quantity) as saleorder"),'retailer_id');
      
       

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
        if($user->role_id==1 )
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $division_filter = !empty($request->division_id)?explode(',',$request->division_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        // $company_id = Auth::user()->company_id;
        $new_from_date = str_replace('-','',$from_date);
        $new_to_date = str_replace('-','',$to_date);

        $start = strtotime($from_date);
        $end = strtotime($to_date);    
        $datearray = array();
        $datediff =  ($end - $start)/60/60/24;
        $datearray[] = $from_date;

        for($i=0 ; $i<$datediff;$i++)
        {
            $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
        }

        $days = count($datearray);


        $data_query_data = Retailer::select('created_on','retailer.name as retailer_name','retailer.landline as mobile','dealer.name as dealer_name','l4_name as l6_name','l5_name as l7_name','l1_name as l3_name','dealer.id as dealer_id','retailer.id as retailer_id')
                // ->join('user_sales_order','user_sales_order.retailer_id','=','retailer.id')
                // ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                ->join('location_view','location_view.l5_id','=','retailer.location_id')
                ->join('dealer','dealer.id','=','retailer.dealer_id')
                // ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                ->where('retailer_status',1)
                ->where('dealer_status',1)
                ->groupBy('retailer.id');
                if(!empty($location_3_filter))
                {
                    $data_query_data->whereIn('l2_id',$location_3_filter);
                }
                if(!empty($junior_data))
                {
                    $data_query_data->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','retailer.dealer_id')
                                    ->whereIn('dealer_location_rate_list.user_id',$junior_data);
                }
                  if(!empty($division_filter))
                {
                    $data_query_data->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','retailer.dealer_id')->join('person','person.id','=','dealer_location_rate_list.user_id')->whereIn('person.division',$division_filter);
                }
        $data_query = $data_query_data->get();
        $added__by_person_data = DB::table('retailer')
                        ->join('person','person.id','=','retailer.created_by_person_id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        // ->join('users','users.id','=','person.id')
                        // ->where('retailer.company_id',$company_id)
                        // ->where('is_admin','!=',1)
                        ->where('person_status','=','1')
                        ->where('retailer_status',1)
                        ->groupBy('retailer.id');
                        if(!empty($location_3_filter))
                        {
                            $added__by_person_data->whereIn('person.location_2_id',$location_3_filter);
                        }
                        if(!empty($junior_data))
                        {
                            $added__by_person_data->whereIn('person.id',$junior_data);
                        }
                        if(!empty($division_filter))
                        {
                            $added__by_person_data->whereIn('person.division',$division_filter);
                        }
        $added__by_person = $added__by_person_data->pluck(DB::raw("GROUP_CONCAT(DISTINCT CONCAT_WS(' ',first_name,middle_name,last_name)) as user_name"),'retailer.id');

        $assin_user_retailer= DB::table('dealer_location_rate_list')
                        ->join('retailer','dealer_location_rate_list.location_id','=','retailer.location_id')
                        ->join('person','dealer_location_rate_list.user_id','=','person.id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->join('users','users.id','=','person.id')
                        ->groupBy('retailer.id');
                        if(!empty($location_3_filter))
                        {
                            $assin_user_retailer->whereIn('person.location_2_id',$location_3_filter);
                        }
                         if(!empty($division_filter))
                        {
                            $assin_user_retailer->whereIn('person.division',$division_filter);
                        }
        $assin_user_retailer_data = $assin_user_retailer->pluck(DB::raw("GROUP_CONCAT(DISTINCT CONCAT_WS(' ',first_name,middle_name,last_name)) as user_name"),'retailer.id');
                        
        
       
        $retailer_sale_data = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        // ->whereBetween("user_sales_order_details.order_id",["$new_from_date","$new_to_date"])
                        ->groupBy('retailer_id');
                    if(($days != '1'))
                    {
                        // dd('q');
                        $retailer_sale_data->whereBetween("user_sales_order.order_id",["$new_from_date","$new_to_date"]);
                    }
                    else{
                        $retailer_sale_data->where("user_sales_order.order_id",'LIKE','%'.$new_from_date.'%');

                    }
                    if(!empty($junior_data))
                    {
                        $retailer_sale_data->whereIn('user_sales_order.user_id',$junior_data);
                    }
                    if(!empty($location_3_filter))
                    {
                        $retailer_sale_data->join('location_view','location_view.l5_id','=','user_sales_order.location_id');
                        $retailer_sale_data->whereIn('l2_id',$location_3_filter);
                    }
                     if(!empty($division_filter))
                    {
                        $retailer_sale_data->join('person','person.id','=','user_sales_order.user_id');
                        $retailer_sale_data->whereIn('person.division',$division_filter);
                    }
        $retailer_sale = $retailer_sale_data->pluck(DB::raw("sum(rate*quantity) as saleorder"),'retailer_id');
        
       

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
                if(!empty($retailer_sale[$value->retailer_id]))
                {
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
        if($user->role_id==1)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $division_filter = !empty($request->division_id)?explode(',',$request->division_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        // $company_id = Auth::user()->company_id;
        
        $data_query_data = DB::table('location_view')
                ->join('location_5','location_5.id','=','location_view.l5_id')
                ->select('l5_name','l4_name','l1_name','l5_id')
                ->where('location_5.status',1)
                ->groupBy('l5_id');
                if(!empty($location_3_filter))
                {
                    $data_query_data->whereIn('l2_id',$location_3_filter);
                }
                if(!empty($junior_data))
                {
                    $data_query_data->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','location_view.l5_id')
                                    ->whereIn('dealer_location_rate_list.user_id',$junior_data);
                }
                if(!empty($division_filter))
                {
                    $data_query_data->join('dealer_location_rate_list','dealer_location_rate_list.location_id','=','location_view.l5_id')
                                    ->join('person','person.id','=','dealer_location_rate_list.user_id')
                                    ->whereIn('person.division',$division_filter);
                }
        $data_query = $data_query_data->get();
        $retailer_count_data = DB::table('retailer')
                        ->join('location_view','location_view.l5_id','=','retailer.location_id')
                        ->where('retailer_status',1)
                        ->groupBy('retailer.location_id');
                        if(!empty($location_3_filter))
                        {
                            $retailer_count_data->whereIn('l2_id',$location_3_filter);
                        }
                        if(!empty($junior_data))
                        {
                            $retailer_count_data->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','retailer.dealer_id')
                                            ->whereIn('dealer_location_rate_list.user_id',$junior_data);
                        }
                         if(!empty($division_filter))
                        {
                            $retailer_count_data->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','retailer.dealer_id')->join('person','person.id','=','dealer_location_rate_list.user_id')->whereIn('person.division',$division_filter);
                        }
        $retailer_count = $retailer_count_data->pluck(DB::raw("COUNT(DISTINCT retailer.id) as user_name"),'retailer.location_id');

        $dealer_count_data = DB::table('dealer')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                        ->where('dealer_status',1)
                        ->groupBy('location_id');
                        if(!empty($location_3_filter))
                        {
                            $dealer_count_data->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id');
                            $dealer_count_data->whereIn('l2_id',$location_3_filter);
                        }
                        if(!empty($junior_data))
                        {
                            $dealer_count_data->whereIn('dealer_location_rate_list.user_id',$junior_data);
                        }
                         if(!empty($division_filter))
                        {
                            $dealer_count_data->join('person','person.id','=','dealer_location_rate_list.user_id')->whereIn('person.division',$division_filter);
                        }
        $dealer_count = $dealer_count_data->pluck(DB::raw("COUNT(DISTINCT dealer.id) as user_name"),'location_id');
                        
        
        
        // $beat_sale_data = DB::table('user_sales_order')
        //                 ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
        //                 ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
        //                 ->groupBy('location_id');
        //             if(!empty($junior_data))
        //             {
        //                 $beat_sale_data->whereIn('user_sales_order.user_id',$junior_data);
        //             }
        //             if(!empty($location_3_filter))
        //             {
        //                 $beat_sale_data->join('location_view','location_view.l5_id','=','user_sales_order.location_id');
        //                 $beat_sale_data->whereIn('l1_id',$location_3_filter);
        //             }
        // $beat_sale = $beat_sale_data->pluck(DB::raw("sum(rate*quantity) as saleorder"),'location_id');
        
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
                $out['l3_name'] = $value->l1_name;
                $out['l6_name'] = $value->l4_name;
                $out['l7_name'] = $value->l5_name;
                $out['retailer_count'] = !empty($retailer_count[$value->l5_id])?$retailer_count[$value->l5_id]:'0';
                $out['dealer_count'] = !empty($dealer_count[$value->l5_id])?$dealer_count[$value->l5_id]:'0';
                $out['sale_value'] = !empty($beat_sale[$value->l5_id])?round($beat_sale[$value->l5_id],2):'0';
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
        if($user->role_id==1)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($user->id);
            Session::push('juniordata', $user->id);
            $junior_data = Session::get('juniordata');
        }
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $division_filter = !empty($request->division_id)?explode(',',$request->division_id):'';
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        // $company_id = Auth::user()->company_id;
        
        $new_from_date = str_replace('-','',$from_date);
        // $new_to_date = str_replace('-','',$to_date);
         $new_to_date = date('Y-m-d',strtotime($to_date .' +1 day')); 
        $new_to_date = str_replace('-','',$new_to_date);

         $start = strtotime($from_date);
        $end = strtotime($to_date);    
        $datearray = array();
        $datediff =  ($end - $start)/60/60/24;
        $datearray[] = $from_date;

        for($i=0 ; $i<$datediff;$i++)
        {
            $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
        }

        $days = count($datearray);

        $productive_count_data = DB::table('location_view')
                        ->join('retailer','retailer.location_id','=','location_view.l5_id')
                        ->join('user_sales_order','user_sales_order.retailer_id','=','retailer.id')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('location_5','location_5.id','=','location_view.l5_id')
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        // ->whereBetween("user_sales_order_details.order_id",["$new_from_date","$new_to_date"])
                        ->where('location_5.status',1)
                        ->where('call_status',1)
                        ->where('retailer_status',1)
                        ->groupBy('date');
                        if(($days != '1'))
                        {
                            // dd('q');
                            $productive_count_data->whereBetween("user_sales_order.order_id",["$new_from_date","$new_to_date"]);
                        }
                        else{
                            $productive_count_data->where("user_sales_order.order_id",'LIKE','%'.$new_from_date.'%');

                        }
                        if(!empty($location_3_filter))
                        {
                            $productive_count_data->whereIn('l2_id',$location_3_filter);
                        }
                        if(!empty($junior_data))
                        {
                            $productive_count_data->whereIn('user_sales_order.user_id',$junior_data);
                        }
                        if(!empty($division_filter))
                        {
                            $productive_count_data->join('person','person.id','=','user_sales_order.user_id')->whereIn('person.division',$division_filter);
                        }
        $productive_count = $productive_count_data->pluck(DB::raw("COUNT(DISTINCT retailer_id) as retailer_count"),'date');

        $total_count_data = DB::table('location_view')
                        ->join('retailer','retailer.location_id','=','location_view.l5_id')
                        ->join('user_sales_order','user_sales_order.retailer_id','=','retailer.id')
                        // ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('location_5','location_5.id','=','location_view.l5_id')
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                        ->where('location_5.status',1)
                        // ->where('call_status',1)
                        ->where('retailer_status',1)
                        ->groupBy('date');
                        if(!empty($location_3_filter))
                        {
                            $total_count_data->whereIn('l2_id',$location_3_filter);
                        }
                        if(!empty($junior_data))
                        {
                            $total_count_data->whereIn('user_sales_order.user_id',$junior_data);
                        }
                         if(!empty($division_filter))
                        {
                            $total_count_data->join('person','person.id','=','user_sales_order.user_id')->whereIn('person.division',$division_filter);
                        }
        $total_count = $total_count_data->pluck(DB::raw("COUNT(DISTINCT retailer_id) as retailer_count"),'date');

                        
        
      
            $data_query_data = DB::table('location_view')
                    ->join('retailer','retailer.location_id','=','location_view.l5_id')
                    ->join('user_sales_order','user_sales_order.retailer_id','=','retailer.id')
                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                    ->join('location_5','location_5.id','=','location_view.l5_id')
                    ->select('l5_name','l4_name','l1_name','l5_id',DB::raw("sum(rate*quantity) as saleorder"),'date')
                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                    // ->whereBetween("user_sales_order_details.order_id",["$new_from_date","$new_to_date"])
                    ->where('location_5.status',1)
                    ->where('retailer_status',1)
                    ->groupBy('date');
                    if(($days != '1'))
                    {
                        // dd('q');
                        $data_query_data->whereBetween("user_sales_order.order_id",["$new_from_date","$new_to_date"]);
                    }
                    else{
                        $data_query_data->where("user_sales_order.order_id",'LIKE','%'.$new_from_date.'%');

                    }
                    if(!empty($junior_data))
                    {
                        // $data_query_data->join('location_view','location_view.l5_id','=','user_sales_order.location_id');
                        $data_query_data->whereIn('user_sales_order.user_id',$junior_data);
                    }
                    if(!empty($division_filter))
                    {
                        // $data_query_data->join('location_view','location_view.l5_id','=','user_sales_order.location_id');
                        $data_query_data->join('person','person.id','=','user_sales_order.user_id')->whereIn('person.division',$division_filter);
                    }
                    if(!empty($location_3_filter))
                    {
                        $data_query_data->whereIn('l2_id',$location_3_filter);
                    }
            $data_query = $data_query_data->get();
        
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


    public function getTotalDistributorPrimarySales(Request $request)
    {
        // dd($request);
        // $user=Auth::user();
        // $company_id = Auth::user()->company_id;

        // if($user->role_id==1)
        // {
        //     $junior_data = array();
        // }
        // else
        // {
        //     Session::forget('juniordata');
        //     $user_data=JuniorData::getJuniorUser($user->id);
        //     Session::push('juniordata', $user->id);
        //     $junior_data = Session::get('juniordata');
        // }
        // dd($junior_data);
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $from_date = !empty($request->from_date)?$request->from_date:'';
        $to_date = !empty($request->to_date)?$request->to_date:'';
        // $location_3_filter = $request->state_id;

        $user_details_data = DB::table('purchase_order');

            $user_details_data->join('purchase_order_details', function($join)
                 {
                   $join->on('purchase_order_details.order_id', '=', 'purchase_order.order_id');
                   $join->on('purchase_order_details.purchase_inv', '=', 'purchase_order.challan_no');

                 })
            ->join('dealer','dealer.id','=','purchase_order.dealer_id')
            ->whereRaw("DATE_FORMAT(purchase_order.order_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(purchase_order.order_date,'%Y-%m-%d')<='$to_date'")
            ->select('dealer.id as dealer_id','dealer.name as dealer_name','dealer.dealer_code','dealer.other_numbers as mobile',DB::raw("ROUND(SUM(purchase_order_details.total_amount),3) AS total_sale_value"));
             if(!empty($location_3_filter))
                {
                    $user_details_data->whereIn('person.location_2_id',$location_3_filter);
                } 
        $user_details = $user_details_data->groupBy('dealer.id')->get();

         if(!empty($user_details))
        {
            $f_out = array();
            foreach ($user_details as $key => $value) 
            {
                $out['dealer_id'] = $value->dealer_id;
                $out['dealer_name'] = $value->dealer_name;
                $out['dealer_code'] = $value->dealer_code;
                $out['dealer_n'] = Crypt::encryptString($value->dealer_id);
                $out['mobile'] = $value->mobile;
                $out['total_sale_value'] = $value->total_sale_value;
                $f_out[] = $out;
            }
            // dd($not_visit_list_query_15);
            $data['user_details'] = $f_out;
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



    public function getTotalDistributorProductPrimarySales(Request $request)
    {
        // dd($request);
        // $user=Auth::user();
        // $company_id = Auth::user()->company_id;

        // if($user->role_id==1)
        // {
        //     $junior_data = array();
        // }
        // else
        // {
        //     Session::forget('juniordata');
        //     $user_data=JuniorData::getJuniorUser($user->id);
        //     Session::push('juniordata', $user->id);
        //     $junior_data = Session::get('juniordata');
        // }
        // dd($junior_data);
        $location_3_filter = !empty($request->state_id)?explode(',',$request->state_id):'';
        $from_date = !empty($request->from_date)?$request->from_date:'';
        $to_date = !empty($request->to_date)?$request->to_date:'';
        $dealer_id = !empty($request->dealer_id)?$request->dealer_id:'';
        // $location_3_filter = $request->state_id;

        $user_details_data = DB::table('purchase_order');

            $user_details_data->join('purchase_order_details', function($join)
                 {
                   $join->on('purchase_order_details.order_id', '=', 'purchase_order.order_id');
                   $join->on('purchase_order_details.purchase_inv', '=', 'purchase_order.challan_no');

                 })
            ->join('dealer','dealer.id','=','purchase_order.dealer_id')
            ->join('catalog_product','catalog_product.id','purchase_order_details.product_id')
            ->whereRaw("DATE_FORMAT(purchase_order.order_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(purchase_order.order_date,'%Y-%m-%d')<='$to_date'")
            ->where('dealer.id','=',$dealer_id)
            ->select('catalog_product.id as product_id','catalog_product.name as product_name',DB::raw("ROUND(SUM(purchase_order_details.total_amount),3) AS total_sale_value"));
             if(!empty($location_3_filter))
                {
                    $user_details_data->whereIn('person.location_2_id',$location_3_filter);
                } 
        $user_details = $user_details_data->groupBy('catalog_product.id')->get();

         if(!empty($user_details))
        {
            $f_out = array();
            foreach ($user_details as $key => $value) 
            {
                $out['product_id'] = $value->product_id;
                $out['product_name'] = $value->product_name;
                $out['total_sale_value'] = $value->total_sale_value;
                $f_out[] = $out;
            }
            // dd($not_visit_list_query_15);
            $data['user_details'] = $f_out;
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
}
