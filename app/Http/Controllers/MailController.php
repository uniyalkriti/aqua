<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Location2;
use App\Person;
use App\Location3;
use App\Dealer;
use App\UserDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

use Response;

class MailController extends Controller
{
	public $successStatus=200;
	public $salt="Rajdhani";
	public $otpString = '0123456789';

	

	public function mail_sent(Request $request)
	{
		$subject = !empty($request->subject)?$request->subject:'Report';
   		// $mail_id = 'bhoopendranath@manacleindia.com';

   		
        $data_company = DB::table('send_mail_details')
                        ->where('status',1)
                        ->groupBy('company_id')
                        ->get();
                        // dd($data_company);
        if(COUNT($data_company)<=0)
        {
            // dd('table');
            return redirect('home');

        }
        foreach ($data_company as $comp_key => $comp_value) 
        {
            # code...
            $company_id = $comp_value->company_id;
            $fetch_mail_id = DB::table('send_mail_details')
                        ->where('status',1)
                        ->where('company_id',$company_id)
                        ->orderBy('sequence','ASC')
                        ->get();
            // dd($fetch_mail_id);
            if($company_id == '44')
            {
                $state = array('48');
            }
            else
            {
                $state = $request->area;
            }
            if(count($fetch_mail_id)>0)
            {
                foreach ($fetch_mail_id as $mail_key => $mail_value) 
                {
                    // dd($mail_value->user_id);
                    if($mail_value->user_id != '0')
                    {
                        Session::forget('juniordata');
                        // $login_user=Auth::user()->id;
                        $user=self::getJuniorUser($mail_value->user_id);
                        Session::push('juniordata', $mail_value->user_id);
                        $user = $request->session()->get('juniordata');
                        if(empty($user))
                        {
                            $user[]=$mail_value->user_id;
                        }
                    }
                    // dd($user);
                    $hq = $request->hq;
                    $town = $request->town;
                    $role = $request->role;
                    $user_id_id = $request->user;
                    $find_month = date('Y-m-d',strtotime("-1 days"));
                    // dd($find_month);
                    $month=date('Y-m',strtotime($find_month));

                    $m1=explode('-', $month);
                    $y=$m1[0];
                    $m2=$m1[1];
                    if($m2<10)
                    $m=ltrim($m2, '0');
                    else
                    $m=$m2;

                    $total_days=cal_days_in_month(CAL_GREGORIAN,$m,2005);
                    $monthName=array('01' =>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');

                    // for($i = 1; $i <=  $total_days; $i++)
                    // {
                    // // add the date to the dates array
                    
                    // }
                    $datesArr = array();
                    $datesDisplayArr = array();
                    $datesArr[] = $find_month;
                    $datesDisplayArr[] =$find_month ;
                    $person_query_data = Person::join('person_login','person_login.person_id','=','person.id')
                        ->join('_role','_role.role_id','=','person.role_id')->join('location_view','location_view.l3_id','=','person.state_id')
                        ->select('head_quater_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'_role.rolename as role_name','_role.role_id as role_id','location_view.l3_name as state_name','person.id as user_id','person.emp_code as emp_code')
                        ->where('person.company_id',$company_id)
                        ->where('person_status',1)
                        ->groupBy('person.id');
                    if(!empty($state))
                    {
                        $person_query_data->whereIn('person.state_id',$state);
                    }
                     if(!empty($hq))
                    {
                        $person_query_data->whereIn('person.head_quater_id',$hq);
                    }
                        if(!empty($town))
                    {
                        $person_query_data->whereIn('person.town_id',$town);
                    }
                      if(!empty($role))
                    {
                        $person_query_data->whereIn('person.role_id',$role);
                    }
                    if(!empty($user))
                    {
                        $person_query_data->whereIn('person.id',$user);
                    }
                    $person_query = $person_query_data->get();
                    // dd($person_query);

                    $location_5 = DB::table('person')
                                  ->join('person_login','person_login.person_id','=','person.id')  
                                  ->join('location_5','location_5.id','=','person.head_quater_id')
                                  ->where('person.company_id',$company_id)  
                                  ->pluck('location_5.name','person.id');

                     $location_6 = DB::table('person')
                                  ->join('person_login','person_login.person_id','=','person.id')  
                                  ->join('location_6','location_6.id','=','person.town_id')
                                  ->where('person.company_id',$company_id)  
                                  ->pluck('location_6.name','person.id');              

                    // dd($location_5);              

                    $upto_check_in_data = DB::table('user_daily_attendance')->where('company_id',$company_id)->groupBy('user_id')->whereRaw("DATE_FORMAT(work_date,'%Y-%m')='$month'")->whereRaw("DATE_FORMAT(work_date,'%H:%i:%s')>='09:30:00'");
                    if(!empty($user))
                    {
                        $upto_check_in_data->whereIn('user_id',$user);
                    }
                    $upto_check_in = $upto_check_in_data->pluck(DB::raw("COUNT(user_id)"),"user_id");

                    $count_total_att_data = DB::table('user_daily_attendance')->where('company_id',$company_id)->groupBy('user_id')->whereRaw("DATE_FORMAT(work_date,'%Y-%m')='$month'");

                    if(!empty($user))
                    {
                        $count_total_att_data->whereIn('user_id',$user);
                    }
                    $count_total_att = $count_total_att_data->pluck(DB::raw("COUNT(DISTINCT order_id) AS DATA"),"user_id");

                    $upto_check_out_data = DB::table('check_out')->where('company_id',$company_id)->groupBy('user_id')->whereRaw("DATE_FORMAT(work_date,'%Y-%m')='$month'")->whereRaw("DATE_FORMAT(work_date,'%H:%i:%s')>='21:30:00'");
                    if(!empty($user))
                    {
                        $upto_check_out_data->whereIn('user_id',$user);
                    }
                    $upto_check_out = $upto_check_out_data->pluck(DB::raw("COUNT(user_id)"),"user_id");

                    $first_time_data = DB::table('user_daily_attendance')->where('company_id',$company_id)->groupBy('work_date','user_id');
                    if(!empty($user))
                    {
                        $first_time_data->whereIn('user_id',$user);
                    }
                    $first_time = $first_time_data->pluck(DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as time"),DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"));

                    $last_time_data = DB::table('check_out')->groupBy('work_date','user_id');
                    if(!empty($user))
                    {
                        $last_time_data->whereIn('user_id',$user);
                    }
                    $last_time = $last_time_data->pluck(DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as time"),DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"));


                    $first_address_data = DB::table('user_daily_attendance')->where('company_id',$company_id)->groupBy('work_date','user_id');
                    if(!empty($user))
                    {
                        $first_time_data->whereIn('user_id',$user);
                    }
                    $first_address = $first_address_data->pluck('track_addrs',DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"));

                    $last_address_data = DB::table('check_out')->groupBy('work_date','user_id');
                    if(!empty($user))
                    {
                        $last_address_data->whereIn('user_id',$user);
                    }
                    $last_address = $last_address_data->pluck('attn_address',DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"));



                    // user_wise and date_wise data  starts here 
                    if($company_id == '44')
                    {
                        $sale_data_part1 = DB::table('user_sales_order_view')
                                ->join('user_sales_order_details_view','user_sales_order_details_view.order_id','=','user_sales_order_view.order_id')
                                ->join('product_wise_scheme_plan_details','product_wise_scheme_plan_details.product_id','=','user_sales_order_details_view.product_id')
                                ->whereRaw("DATE_FORMAT(user_sales_order_view.date,'%Y-%m')='$month'")
                                ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$find_month' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$find_month'")
                                ->where('user_sales_order_view.company_id',$company_id)
                                ->groupBy('user_sales_order_view.user_id','user_sales_order_view.date');
                                if(!empty($state))
                                {
                                    $sale_data_part1->whereIn('product_wise_scheme_plan_details.state_id',$state);
                                }
                                if(!empty($user))
                                {
                                    $sale_data_part1->whereIn('user_sales_order_view.user_id',$user);
                                }
                        $sale_data = $sale_data_part1->pluck(DB::raw("SUM((rate*quantity)-(rate*quantity*(value_amount_percentage/100))) as sale_value"),DB::raw("CONCAT(user_id,user_sales_order_view.date) as d"));
                        // dd($sale_data);
                    }
                    else
                    {
                        $sale_data = DB::table('user_sales_order_view')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order_view.order_id')
                                ->whereRaw("DATE_FORMAT(user_sales_order_view.date,'%Y-%m')='$month'")
                                ->where('user_sales_order_view.company_id',$company_id)
                                ->groupBy('user_sales_order_view.user_id','user_sales_order_view.date')
                                ->pluck(DB::raw("SUM(rate*quantity) as sale_value"),DB::raw("CONCAT(user_id,DATE_FORMAT(user_sales_order_view.date,'%Y-%m-%d')) as d"));
                    }
                    
                 // dd($sale_data);
                    $sale_data_working_town = DB::table('user_sales_order')
                                ->join('location_7','location_7.id','=','user_sales_order.location_id')
                                ->join('location_6','location_6.id','=','location_7.location_6_id')
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                ->where('user_sales_order.company_id',$company_id)
                                ->groupBy('user_id','date')
                                ->pluck(DB::raw("group_concat(distinct location_6.name ) as l6"),DB::raw("CONCAT(user_id,DATE_FORMAT(date,'%Y-%m-%d'))"));            

                    $primary_sale_data = array(); 

                     $primary_sale_data_working_town = array();            
                                // dd($sale_data_working_town);

                    $travelling_expense_data = array();            

                             // dd($primary_sale_data);             

                    $total_call_data = DB::table('user_sales_order_view')
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                ->where('user_sales_order_view.company_id',$company_id)
                                ->where('call_status',1)
                                ->groupBy('user_id','date')
                                ->pluck(DB::raw("COUNT(order_id) as sale_value"),DB::raw("CONCAT(user_id,DATE_FORMAT(date,'%Y-%m-%d'))"));

                    $total_t_call_data = DB::table('user_sales_order_view')
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                ->where('user_sales_order_view.company_id',$company_id)
                                ->groupBy('user_id','date')
                                ->pluck(DB::raw("COUNT(order_id) as sale_value"),DB::raw("CONCAT(user_id,DATE_FORMAT(date,'%Y-%m-%d'))"));

                                // dd($total_t_call_data);
                    // user_wise and date_wise data  ends here 

                    // user_wise total starts here 
                    if($company_id == '44')
                    {
                        $sale_data_grand_data = DB::table('user_sales_order_view')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order_view.order_id')
                                ->join('product_wise_scheme_plan_details','product_wise_scheme_plan_details.product_id','=','user_sales_order_details.product_id')
                                ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$find_month' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$find_month'")
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                ->where('user_sales_order_view.company_id',$company_id)
                                ->groupBy('user_id');
                                if(!empty($state))
                                {
                                    $sale_data_grand_data->whereIn('product_wise_scheme_plan_details.state_id',$state);
                                    $sale_data_grand_data->whereIn('l3_id',$state);
                                }
                                if(!empty($user))
                                {
                                    $sale_data_grand_data->whereIn('user_sales_order_view.user_id',$user);
                                }
                        $sale_data_grand = $sale_data_grand_data->pluck(DB::raw("SUM((rate*quantity)-((rate*quantity)*(value_amount_percentage/100))) as sale_value"),DB::raw("CONCAT(user_id)"));


                    }
                    else
                    {
                        $sale_data_grand = DB::table('user_sales_order_view')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order_view.order_id')
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                ->where('user_sales_order_view.company_id',$company_id)
                                ->groupBy('user_id')
                                ->pluck(DB::raw("SUM(rate*quantity) as sale_value"),DB::raw("CONCAT(user_id)"));
                    }
                    
                    // dd($sale_data_grand);


                    $primary_sale_data_grand = array(); 

                    $travelling_expense_data_grand = array();                       

                    $total_call_data_grand_cus = DB::table('user_sales_order_view')
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                ->where('user_sales_order_view.company_id',$company_id)
                                ->where('call_status',1)
                                ->groupBy('user_id');
                                if(!empty($state))
                                {
                                    $total_call_data_grand_cus->whereIn('l3_id',$state);
                                }
                                if(!empty($user))
                                {
                                    $total_call_data_grand_cus->whereIn('user_sales_order_view.user_id',$user);
                                }
                    $total_call_data_grand = $total_call_data_grand_cus->pluck(DB::raw("COUNT(order_id) as sale_value"),DB::raw("CONCAT(user_id)"));

                    $total_call_t_data_grand_cus = DB::table('user_sales_order_view')
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                ->where('user_sales_order_view.company_id',$company_id)
                                ->groupBy('user_id');
                                if(!empty($state))
                                {
                                    $total_call_t_data_grand_cus->whereIn('l3_id',$state);
                                }
                                if(!empty($user))
                                {
                                    $total_call_t_data_grand_cus->whereIn('user_sales_order_view.user_id',$user);
                                }

                    $total_call_t_data_grand = $total_call_t_data_grand_cus->pluck(DB::raw("COUNT(order_id) as sale_value"),DB::raw("CONCAT(user_id)"));
                    // user_wise total ends here 

                    // date wise total start here 
                    if($company_id == '44')
                    {
                         $sale_data_grand_date_filt = DB::table('user_sales_order_view')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order_view.order_id')
                                ->join('product_wise_scheme_plan_details','product_wise_scheme_plan_details.product_id','=','user_sales_order_details.product_id')
                                ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$find_month' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$find_month'")
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                ->where('user_sales_order_view.company_id',$company_id)
                                ->groupBy('date');
                                if(!empty($user))
                                {
                                    $sale_data_grand_date_filt->whereIn('user_sales_order_view.user_id',$user);
                                }
                                if(!empty($state))
                                {
                                    $sale_data_grand_date_filt->whereIn('l3_id',$state);
                                    $sale_data_grand_date_filt->whereIn('product_wise_scheme_plan_details.state_id',$state);
                                }
                        $sale_data_grand_date = $sale_data_grand_date_filt->pluck(DB::raw("SUM((rate*quantity)-((rate*quantity)*(value_amount_percentage/100))) as sale_value"),DB::raw("CONCAT(date)"));

                    }
                    else
                    {
                         $sale_data_grand_date_filt = DB::table('user_sales_order_view')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order_view.order_id')
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                ->where('user_sales_order_view.company_id',$company_id)
                                ->groupBy('date');
                                 if(!empty($user))
                                    {
                                        $sale_data_grand_date_filt->whereIn('user_sales_order_view.user_id',$user);
                                    }
                        $sale_data_grand_date = $sale_data_grand_date_filt->pluck(DB::raw("SUM(rate*quantity) as sale_value"),DB::raw("CONCAT(date)"));

                    }
                   // dd($sale_data_grand_date);

                    // $primary_sale_data_grand_date_filt = DB::table('user_primary_sales_order')
                    //             ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                    //             ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m')='$month'")
                    //             ->where('user_primary_sales_order.company_id',$company_id)
                    //             ->groupBy('sale_date');
                    //              if(!empty($user))
                    //                 {
                    //                     $primary_sale_data_grand_date_filt->whereIn('user_primary_sales_order.created_person_id',$user);
                    //                 }
                    $primary_sale_data_grand_date = array();  

                    // $travelling_expense_data_grand_date_filt = DB::table('travelling_expense_bill')
                    //             ->whereRaw("DATE_FORMAT(travellingDate,'%Y-%m')='$month'")
                    //             ->where('travelling_expense_bill.company_id',$company_id)
                    //             ->groupBy('travellingDate');
                    //              if(!empty($user))
                    //                 {
                    //                     $travelling_expense_data_grand_date_filt->whereIn('travelling_expense_bill.user_id',$user);
                    //                 }
                    $travelling_expense_data_grand_date = array();                       

                    $total_call_data_grand_date_filt = DB::table('user_sales_order_view')
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                ->where('call_status',1)
                                ->where('user_sales_order_view.company_id',$company_id)
                                ->groupBy('date');
                                 if(!empty($user))
                                    {
                                        $total_call_data_grand_date_filt->whereIn('user_sales_order_view.user_id',$user);
                                    }
                                    if(!empty($state))
                                    {
                                        $total_call_data_grand_date_filt->whereIn('l3_id',$state);
                                    }
                    $total_call_data_grand_date =   $total_call_data_grand_date_filt->pluck(DB::raw("COUNT(order_id) as sale_value"),DB::raw("CONCAT(date)"));

                    $total_call_t_data_grand_date_filt = DB::table('user_sales_order_view')
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                ->where('user_sales_order_view.company_id',$company_id)
                                ->groupBy('date');
                             if(!empty($user))
                                {
                                    $total_call_t_data_grand_date_filt->whereIn('user_sales_order_view.user_id',$user);
                                }
                            if(!empty($state))
                            {
                                $total_call_t_data_grand_date_filt->whereIn('l3_id',$state);
                            }
                    $total_call_t_data_grand_date =  $total_call_t_data_grand_date_filt->pluck(DB::raw("COUNT(order_id) as sale_value"),DB::raw("CONCAT(date)"));
                                // dd($total_call_t_data_grand_date);
                    // date wise total ends here 

                    // grand total starts here 
                    if($company_id == '44')
                    {
                        $grand_sale_data_grand_date_filt = DB::table('user_sales_order_view')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order_view.order_id')
                                ->join('product_wise_scheme_plan_details','product_wise_scheme_plan_details.product_id','=','user_sales_order_details.product_id')
                                ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$find_month' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$find_month'")
                                ->where('user_sales_order_view.company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                ->select(DB::raw("SUM((rate*quantity)-((rate*quantity)*(value_amount_percentage/100))) as sale_value"));
                                if(!empty($user))
                                    {
                                        $grand_sale_data_grand_date_filt->whereIn('user_sales_order_view.user_id',$user);
                                    }
                                if(!empty($state))
                                {
                                    $grand_sale_data_grand_date_filt->whereIn('product_wise_scheme_plan_details.state_id',$state);
                                    $grand_sale_data_grand_date_filt->whereIn('l3_id',$state);
                                }
                        $grand_sale_data_grand_date = $grand_sale_data_grand_date_filt->first();
                    }
                    else
                    {
                        $grand_sale_data_grand_date_filt = DB::table('user_sales_order_view')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order_view.order_id')
                                ->where('user_sales_order_view.company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                ->select(DB::raw("SUM(rate*quantity) as sale_value"));
                                if(!empty($user))
                                    {
                                        $grand_sale_data_grand_date_filt->whereIn('user_sales_order_view.user_id',$user);
                                    }
                        $grand_sale_data_grand_date = $grand_sale_data_grand_date_filt->first();
                    }
                    // dd($grand_sale_data_grand_date);

                    $grand_primary_sale_data_grand_date_filt = DB::table('user_primary_sales_order')
                                ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                                ->where('user_primary_sales_order.company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m')='$month'")
                                ->select(DB::raw("SUM((rate*cases)+(pcs*pr_rate)) as sale_value"));
                                if(!empty($user))
                                    {
                                        $grand_primary_sale_data_grand_date_filt->whereIn('user_primary_sales_order.created_person_id',$user);
                                    }
                    $grand_primary_sale_data_grand_date = $grand_primary_sale_data_grand_date_filt->first();  

                    $grand_travelling_expense_data_grand_date_filt = DB::table('travelling_expense_bill')
                                ->where('travelling_expense_bill.company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(travellingDate,'%Y-%m')='$month'")
                                ->select(DB::raw("SUM(total) as sale_value"));
                                if(!empty($user))
                                    {
                                        $grand_travelling_expense_data_grand_date_filt->whereIn('travelling_expense_bill.user_id',$user);
                                    }
                    $grand_travelling_expense_data_grand_date = $grand_travelling_expense_data_grand_date_filt->first();                      

                    $grand_total_call_data_grand_date_filt = DB::table('user_sales_order_view')
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                ->where('user_sales_order_view.company_id',$company_id)
                                ->where('call_status',1)
                                ->select(DB::raw("COUNT(order_id) as pc"));
                                 if(!empty($user))
                                    {
                                        $grand_total_call_data_grand_date_filt->whereIn('user_sales_order_view.user_id',$user);
                                    }
                                if(!empty($state))
                                    {
                                        $grand_total_call_data_grand_date_filt->whereIn('l3_id',$state);
                                    }
                              $grand_total_call_data_grand_date = $grand_total_call_data_grand_date_filt->first();

                    $grand_total_call_t_data_grand_date_filt = DB::table('user_sales_order_view')
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                ->where('user_sales_order_view.company_id',$company_id)
                                ->select(DB::raw("COUNT(order_id) as tc"));
                                 if(!empty($user))
                                    {
                                        $grand_total_call_t_data_grand_date_filt->whereIn('user_sales_order_view.user_id',$user);
                                    }
                                if(!empty($state))
                                    {
                                        $grand_total_call_t_data_grand_date_filt->whereIn('l3_id',$state);
                                    }
                    $grand_total_call_t_data_grand_date = $grand_total_call_t_data_grand_date_filt->first();
                    // grand total ends here 

                    $dealer_count_user_wise_data = DB::table('dealer_location_rate_list')
                    						->where('company_id',$company_id)
                    						->groupBy('user_id');
                                            if(!empty($user))
                                            {
                                                $dealer_count_user_wise_data->whereIn('dealer_location_rate_list.user_id',$user);
                                            }
        			$dealer_count_user_wise = $dealer_count_user_wise_data->pluck(DB::raw("COUNT(DISTINCT dealer_id) as dealer_id"),'user_id');

            		$retailer_count_user_wise_data = DB::table('dealer_location_rate_list')
            								->join('retailer','retailer.location_id','=','dealer_location_rate_list.location_id')
                    						->where('dealer_location_rate_list.company_id',$company_id)
                    						->groupBy('dealer_location_rate_list.user_id');
                                            if(!empty($user))
                                            {
                                                $retailer_count_user_wise_data->whereIn('dealer_location_rate_list.user_id',$user);
                                            }
        			$retailer_count_user_wise = $retailer_count_user_wise_data->pluck(DB::raw("COUNT(DISTINCT retailer.id) as dealer_id"),'dealer_location_rate_list.user_id');
            		$date = date('Y-m-d');
            		$retailer_count_added_per_day = DB::table('retailer')
                                				->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')='$find_month'")
            									->where('company_id',$company_id)
            									->groupBy('created_by_person_id',DB::raw("date_format(created_on,'%Y-%m-%d')"))
            									->pluck(DB::raw("COUNT(DISTINCT retailer.id) as dealer_id"),DB::raw("CONCAT(created_by_person_id,date_format(created_on,'%Y-%m-%d')) as data"));

                    if($company_id == 44)
                    {
                        $retailer_sale_added_per_day_data = DB::table('retailer')
                                                ->join('user_sales_order_view','user_sales_order_view.retailer_id','=','retailer.id')
                                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order_view.order_id')
                                                ->join('product_wise_scheme_plan_details','product_wise_scheme_plan_details.product_id','=','user_sales_order_details.product_id')
                                                ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$find_month' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$find_month'")
                                                ->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')='$find_month'")
                                                ->where('retailer.company_id',$company_id)
                                                ->groupBy('retailer.created_by_person_id',DB::raw("date_format(created_on,'%Y-%m-%d')"));
                                                if(!empty($state))
                                                {
                                                    $retailer_sale_added_per_day_data->whereIn('product_wise_scheme_plan_details.state_id',$state);
                                                }
                                                if(!empty($user))
                                                {
                                                    $retailer_count_user_wise_data->whereIn('retailer.created_by_person_id',$user);
                                                }
                        $retailer_sale_added_per_day = $retailer_sale_added_per_day_data->pluck(DB::raw("SUM((rate*quantity)-((rate*quantity)*(value_amount_percentage/100))) as sale_value"),DB::raw("CONCAT(created_by_person_id,date_format(retailer.created_on,'%Y-%m-%d')) as data"));
                    }
                    else
                    {
                        $retailer_sale_added_per_day = DB::table('retailer')
                                                ->join('user_sales_order_view','user_sales_order_view.retailer_id','=','retailer.id')
                                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order_view.order_id')
                                                // ->join('product_wise_scheme_plan_details','product_wise_scheme_plan_details.product_id','=','user_sales_order_details.product_id')
                                                ->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')='$find_month'")
                                                ->where('retailer.company_id',$company_id)
                                                ->groupBy('retailer.created_by_person_id',DB::raw("date_format(created_on,'%Y-%m-%d')"))
                                                ->pluck(DB::raw("SUM(rate*quantity) as sale_value"),DB::raw("CONCAT(created_by_person_id,date_format(retailer.created_on,'%Y-%m-%d')) as data"));
                    }
                    // dd($retailer_sale_added_per_day);
            		$retailer_count_added_month = DB::table('retailer')
            									->where('company_id',$company_id)
                                				->whereRaw("DATE_FORMAT(created_on,'%Y-%m')='$month'")
            									->groupBy('created_by_person_id')
            									->pluck(DB::raw("COUNT(DISTINCT retailer.id) as dealer_id"),'created_by_person_id');
            									
                    $distributor_name = DB::table('user_sales_order')
                                        ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
                                        ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$find_month'")
                                        ->where('user_sales_order.company_id',$company_id)
                                        ->groupBy('user_sales_order.user_id')
                                        ->pluck(DB::raw("group_concat(DISTINCT dealer.name) as dealer_name"),DB::raw("concat(user_sales_order.date,user_sales_order.user_id) as d"));

                    $beat_name = DB::table('user_sales_order')
                                ->join('location_7','location_7.id','=','user_sales_order.location_id')
                                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$find_month'")
                                ->where('user_sales_order.company_id',$company_id)
                                ->groupBy('user_sales_order.user_id')
                                ->pluck(DB::raw("group_concat(DISTINCT location_7.name) as dealer_name"),DB::raw("concat(user_sales_order.date,user_sales_order.user_id) as b"));

                    $working_status = DB::table('user_daily_attendance')
                                    ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                                    ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')='$find_month'")
                                    ->where('user_daily_attendance.company_id',$company_id)
                                    ->where('_working_status.company_id',$company_id)
                                    ->groupBy('user_daily_attendance.user_id','user_daily_attendance.work_date')
                                    ->pluck('_working_status.name as name',DB::raw("concat(date_format(user_daily_attendance.work_date,'%Y-%m-%d'),user_daily_attendance.user_id) as a"));


                    $working_with = DB::table('user_daily_attendance')
                                    ->join('person','person.id','=','user_daily_attendance.working_with')
                                    ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')='$find_month'")
                                    ->where('user_daily_attendance.company_id',$company_id)
                                    ->where('person.company_id',$company_id)
                                    ->groupBy('user_daily_attendance.user_id','user_daily_attendance.work_date')
                                    ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("concat(date_format(user_daily_attendance.work_date,'%Y-%m-%d'),user_daily_attendance.user_id) as a"));
                    // if(count($fetch_mail_id)>0)
                    // {
                    //     foreach ($fetch_mail_id as $mail_key => $mail_value) 
                    //     {
                            $mail_id = $mail_value->name;
                            $mail = Mail::send('reports/time-report-sale/send_mail', array(
                                                    'records' => $person_query,
                                                    'month' => $month,
                                                    'datesArr'=>$datesArr,
                                                    'location_5'=>$location_5,
                                                    'location_6'=>$location_6,
                                                    'datesDisplayArr'=>$datesDisplayArr,
                                                    'total_days'=>$total_days,
                                                    'first_time'=>$first_time,
                                                    'last_time'=>$last_time,
                                                    'upto_check_in' =>$upto_check_in,
                                                    'upto_check_out'=>$upto_check_out,
                                                    'count_total_att'=>$count_total_att,
                                                    'total_call_data'=> $total_call_data,
                                                    'sale_data'=> $sale_data,
                                                    'sale_data_working_town'=> $sale_data_working_town,
                                                    'primary_sale_data'=> $primary_sale_data,
                                                    'primary_sale_data_working_town'=> $primary_sale_data_working_town,
                                                    'travelling_expense_data'=> $travelling_expense_data,
                                                    'total_call_data_grand'=> $total_call_data_grand,
                                                    'sale_data_grand'=> $sale_data_grand,
                                                    'primary_sale_data_grand'=> $primary_sale_data_grand,
                                                    'travelling_expense_data_grand'=> $travelling_expense_data_grand,
                                                    'total_t_call_data'=> $total_t_call_data,
                                                    'total_call_t_data_grand'=> $total_call_t_data_grand,
                                                    'sale_data_grand_date'=> $sale_data_grand_date,
                                                    'primary_sale_data_grand_date'=> $primary_sale_data_grand_date,
                                                    'travelling_expense_data_grand_date'=> $travelling_expense_data_grand_date,
                                                    'total_call_data_grand_date'=> $total_call_data_grand_date,
                                                    'total_call_t_data_grand_date'=> $total_call_t_data_grand_date,
                                                    'grand_sale_data_grand_date'=> $grand_sale_data_grand_date,
                                                    'grand_primary_sale_data_grand_date'=> $grand_primary_sale_data_grand_date,
                                                    'grand_travelling_expense_data_grand_date'=> $grand_travelling_expense_data_grand_date,
                                                    'grand_total_call_data_grand_date'=> $grand_total_call_data_grand_date,
                                                    'grand_total_call_t_data_grand_date'=> $grand_total_call_t_data_grand_date,
                                                    'dealer_count_user_wise'=> $dealer_count_user_wise,
                                                    'retailer_count_user_wise'=> $retailer_count_user_wise,
                                                    'retailer_count_added_per_day'=> $retailer_count_added_per_day,
                                                    // 'retailer_count_added_per_day_on'=> $retailer_count_added_per_day_on,
                                                    'retailer_count_added_month'=> $retailer_count_added_month,
                                                    'first_address'=> $first_address,
                                                    'last_address'=> $last_address,
                                                    'distributor_name'=> $distributor_name,
                                                    'beat_name'=> $beat_name,
                                                    'working_status'=> $working_status,
                                                    'working_with'=> $working_with,
                                                    'retailer_sale_added_per_day'=> $retailer_sale_added_per_day,
                                                    

                                    ) , function($message) use($mail_id,$subject)
                            {
                                    
                                $message->from('manacle.php1@gmail.com');

                                $message->to($mail_id)->subject($subject);

                            });
                }
            }
            

    		
        } // company foreach ends here 
        // dd('done');
	
	} 


    public function btwAutoMail(Request $request)
    {
        $subject = !empty($request->subject)?$request->subject:'Report';
        // $mail_id = 'bhoopendranath@manacleindia.com';
        $role_id = array("149","167","148","146","145","155","154","149","167","168","301","200");

        
            $fetch_mail_id = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->select('person.company_id','person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.email')
                        ->where('person_status',1)
                        ->whereIn('person.role_id',$role_id)
                        // ->where('person.id',3040)
                        ->groupBy('person.id')
                        ->get();

           $company_id = '43';     
            // dd($fetch_mail_id);
            if($company_id == '44')
            {
                $state = array('48');
            }
            else
            {
                $state = $request->area;
            }
            if(count($fetch_mail_id)>0)
            {
                foreach ($fetch_mail_id as $mail_key => $mail_value) 
                {
                    // dd($mail_value->user_id);
                    $sent_mail_email = $mail_value->email;
                    if(!empty($sent_mail_email))
                    {
                        if($mail_value->user_id != '0')
                        {
                            Session::forget('juniordata');
                            // $login_user=Auth::user()->id;
                            $user=self::getJuniorUser($mail_value->user_id);
                            Session::push('juniordata', $mail_value->user_id);
                            $user = $request->session()->get('juniordata');
                            if(empty($user))
                            {
                                $user[]=$mail_value->user_id;
                            }
                        }
                        // dd($user);
                        $company_id = $mail_value->company_id;
                        $hq = $request->hq;
                        $town = $request->town;
                        $role = $request->role;
                        $user_id_id = $request->user;
                        $find_month = date('Y-m-d',strtotime("-1 days"));
                        // dd($find_month);
                        $month=date('Y-m',strtotime($find_month));

                        $m1=explode('-', $month);
                        $y=$m1[0];
                        $m2=$m1[1];
                        if($m2<10)
                        $m=ltrim($m2, '0');
                        else
                        $m=$m2;

                        $total_days=cal_days_in_month(CAL_GREGORIAN,$m,2005);
                        $monthName=array('01' =>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');

                        // for($i = 1; $i <=  $total_days; $i++)
                        // {
                        // // add the date to the dates array
                        
                        // }
                        $datesArr = array();
                        $datesDisplayArr = array();
                        $datesArr[] = $find_month;
                        $datesDisplayArr[] =$find_month ;
                        $person_query_data = Person::join('person_login','person_login.person_id','=','person.id')
                            ->join('_role','_role.role_id','=','person.role_id')->join('location_view','location_view.l3_id','=','person.state_id')
                            ->select('head_quater_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'_role.rolename as role_name','_role.role_id as role_id','location_view.l3_name as state_name','person.id as user_id','person.emp_code as emp_code')
                            ->where('person.company_id',$company_id)
                            ->where('person_status',1)
                            ->groupBy('person.id');
                        if(!empty($state))
                        {
                            $person_query_data->whereIn('person.state_id',$state);
                        }
                         if(!empty($hq))
                        {
                            $person_query_data->whereIn('person.head_quater_id',$hq);
                        }
                            if(!empty($town))
                        {
                            $person_query_data->whereIn('person.town_id',$town);
                        }
                          if(!empty($role))
                        {
                            $person_query_data->whereIn('person.role_id',$role);
                        }
                        if(!empty($user))
                        {
                            $person_query_data->whereIn('person.id',$user);
                        }
                        $person_query = $person_query_data->get();
                        // dd($person_query);

                        $location_5 = DB::table('person')
                                      ->join('person_login','person_login.person_id','=','person.id')  
                                      ->join('location_5','location_5.id','=','person.head_quater_id')
                                      ->where('person.company_id',$company_id)  
                                      ->pluck('location_5.name','person.id');

                         $location_6 = DB::table('person')
                                      ->join('person_login','person_login.person_id','=','person.id')  
                                      ->join('location_6','location_6.id','=','person.town_id')
                                      ->where('person.company_id',$company_id)  
                                      ->pluck('location_6.name','person.id');              

                        // dd($location_5);              

                        $upto_check_in_data = DB::table('user_daily_attendance')->where('company_id',$company_id)->groupBy('user_id')->whereRaw("DATE_FORMAT(work_date,'%Y-%m')='$month'")->whereRaw("DATE_FORMAT(work_date,'%H:%i:%s')>='09:30:00'");
                        if(!empty($user))
                        {
                            $upto_check_in_data->whereIn('user_id',$user);
                        }
                        $upto_check_in = $upto_check_in_data->pluck(DB::raw("COUNT(user_id)"),"user_id");

                        $count_total_att_data = DB::table('user_daily_attendance')->where('company_id',$company_id)->groupBy('user_id')->whereRaw("DATE_FORMAT(work_date,'%Y-%m')='$month'");

                        if(!empty($user))
                        {
                            $count_total_att_data->whereIn('user_id',$user);
                        }
                        $count_total_att = $count_total_att_data->pluck(DB::raw("COUNT(DISTINCT order_id) AS DATA"),"user_id");

                        $upto_check_out_data = DB::table('check_out')->where('company_id',$company_id)->groupBy('user_id')->whereRaw("DATE_FORMAT(work_date,'%Y-%m')='$month'")->whereRaw("DATE_FORMAT(work_date,'%H:%i:%s')>='21:30:00'");
                        if(!empty($user))
                        {
                            $upto_check_out_data->whereIn('user_id',$user);
                        }
                        $upto_check_out = $upto_check_out_data->pluck(DB::raw("COUNT(user_id)"),"user_id");

                        $first_time_data = DB::table('user_daily_attendance')->where('company_id',$company_id)->groupBy('work_date','user_id');
                        if(!empty($user))
                        {
                            $first_time_data->whereIn('user_id',$user);
                        }
                        $first_time = $first_time_data->pluck(DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as time"),DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"));

                        $last_time_data = DB::table('check_out')->groupBy('work_date','user_id');
                        if(!empty($user))
                        {
                            $last_time_data->whereIn('user_id',$user);
                        }
                        $last_time = $last_time_data->pluck(DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as time"),DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"));


                        $first_address_data = DB::table('user_daily_attendance')->where('company_id',$company_id)->groupBy('work_date','user_id');
                        if(!empty($user))
                        {
                            $first_time_data->whereIn('user_id',$user);
                        }
                        $first_address = $first_address_data->pluck('track_addrs',DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"));

                        $last_address_data = DB::table('check_out')->groupBy('work_date','user_id');
                        if(!empty($user))
                        {
                            $last_address_data->whereIn('user_id',$user);
                        }
                        $last_address = $last_address_data->pluck('attn_address',DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"));



                        // user_wise and date_wise data  starts here 
                        if($company_id == '44')
                        {
                            $sale_data_part1 = DB::table('user_sales_order_view')
                                    ->join('user_sales_order_details_view','user_sales_order_details_view.order_id','=','user_sales_order_view.order_id')
                                    ->join('product_wise_scheme_plan_details','product_wise_scheme_plan_details.product_id','=','user_sales_order_details_view.product_id')
                                    ->whereRaw("DATE_FORMAT(user_sales_order_view.date,'%Y-%m')='$month'")
                                    ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$find_month' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$find_month'")
                                    ->where('user_sales_order_view.company_id',$company_id)
                                    ->groupBy('user_sales_order_view.user_id','user_sales_order_view.date');
                                    if(!empty($state))
                                    {
                                        $sale_data_part1->whereIn('product_wise_scheme_plan_details.state_id',$state);
                                    }
                                    if(!empty($user))
                                    {
                                        $sale_data_part1->whereIn('user_sales_order_view.user_id',$user);
                                    }
                            $sale_data = $sale_data_part1->pluck(DB::raw("SUM((rate*quantity)-(rate*quantity*(value_amount_percentage/100))) as sale_value"),DB::raw("CONCAT(user_id,user_sales_order_view.date) as d"));
                            // dd($sale_data);
                        }
                        else
                        {
                            $sale_data = DB::table('user_sales_order_view')
                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order_view.order_id')
                                    ->whereRaw("DATE_FORMAT(user_sales_order_view.date,'%Y-%m')='$month'")
                                    ->where('user_sales_order_view.company_id',$company_id)
                                    ->groupBy('user_sales_order_view.user_id','user_sales_order_view.date')
                                    ->pluck(DB::raw("SUM(rate*quantity) as sale_value"),DB::raw("CONCAT(user_id,DATE_FORMAT(user_sales_order_view.date,'%Y-%m-%d')) as d"));
                        }
                        
                     // dd($sale_data);
                        $sale_data_working_town = DB::table('user_sales_order')
                                    ->join('location_7','location_7.id','=','user_sales_order.location_id')
                                    ->join('location_6','location_6.id','=','location_7.location_6_id')
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                    ->where('user_sales_order.company_id',$company_id)
                                    ->groupBy('user_id','date')
                                    ->pluck(DB::raw("group_concat(distinct location_6.name ) as l6"),DB::raw("CONCAT(user_id,DATE_FORMAT(date,'%Y-%m-%d'))"));            

                        $primary_sale_data = array(); 

                         $primary_sale_data_working_town = array();            
                                    // dd($sale_data_working_town);

                        $travelling_expense_data = array();            

                                 // dd($primary_sale_data);             

                        $total_call_data = DB::table('user_sales_order_view')
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                    ->where('user_sales_order_view.company_id',$company_id)
                                    ->where('call_status',1)
                                    ->groupBy('user_id','date')
                                    ->pluck(DB::raw("COUNT(order_id) as sale_value"),DB::raw("CONCAT(user_id,DATE_FORMAT(date,'%Y-%m-%d'))"));

                        $total_t_call_data = DB::table('user_sales_order_view')
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                    ->where('user_sales_order_view.company_id',$company_id)
                                    ->groupBy('user_id','date')
                                    ->pluck(DB::raw("COUNT(order_id) as sale_value"),DB::raw("CONCAT(user_id,DATE_FORMAT(date,'%Y-%m-%d'))"));

                                    // dd($total_t_call_data);
                        // user_wise and date_wise data  ends here 

                        // user_wise total starts here 
                        if($company_id == '44')
                        {
                            $sale_data_grand_data = DB::table('user_sales_order_view')
                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order_view.order_id')
                                    ->join('product_wise_scheme_plan_details','product_wise_scheme_plan_details.product_id','=','user_sales_order_details.product_id')
                                    ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$find_month' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$find_month'")
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                    ->where('user_sales_order_view.company_id',$company_id)
                                    ->groupBy('user_id');
                                    if(!empty($state))
                                    {
                                        $sale_data_grand_data->whereIn('product_wise_scheme_plan_details.state_id',$state);
                                        $sale_data_grand_data->whereIn('l3_id',$state);
                                    }
                                    if(!empty($user))
                                    {
                                        $sale_data_grand_data->whereIn('user_sales_order_view.user_id',$user);
                                    }
                            $sale_data_grand = $sale_data_grand_data->pluck(DB::raw("SUM((rate*quantity)-((rate*quantity)*(value_amount_percentage/100))) as sale_value"),DB::raw("CONCAT(user_id)"));


                        }
                        else
                        {
                            $sale_data_grand = DB::table('user_sales_order_view')
                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order_view.order_id')
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                    ->where('user_sales_order_view.company_id',$company_id)
                                    ->groupBy('user_id')
                                    ->pluck(DB::raw("SUM(rate*quantity) as sale_value"),DB::raw("CONCAT(user_id)"));
                        }
                        
                        // dd($sale_data_grand);


                        $primary_sale_data_grand = array(); 

                        $travelling_expense_data_grand = array();                       

                        $total_call_data_grand_cus = DB::table('user_sales_order_view')
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                    ->where('user_sales_order_view.company_id',$company_id)
                                    ->where('call_status',1)
                                    ->groupBy('user_id');
                                    if(!empty($state))
                                    {
                                        $total_call_data_grand_cus->whereIn('l3_id',$state);
                                    }
                                    if(!empty($user))
                                    {
                                        $total_call_data_grand_cus->whereIn('user_sales_order_view.user_id',$user);
                                    }
                        $total_call_data_grand = $total_call_data_grand_cus->pluck(DB::raw("COUNT(order_id) as sale_value"),DB::raw("CONCAT(user_id)"));

                        $total_call_t_data_grand_cus = DB::table('user_sales_order_view')
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                    ->where('user_sales_order_view.company_id',$company_id)
                                    ->groupBy('user_id');
                                    if(!empty($state))
                                    {
                                        $total_call_t_data_grand_cus->whereIn('l3_id',$state);
                                    }
                                    if(!empty($user))
                                    {
                                        $total_call_t_data_grand_cus->whereIn('user_sales_order_view.user_id',$user);
                                    }

                        $total_call_t_data_grand = $total_call_t_data_grand_cus->pluck(DB::raw("COUNT(order_id) as sale_value"),DB::raw("CONCAT(user_id)"));
                        // user_wise total ends here 

                        // date wise total start here 
                        if($company_id == '44')
                        {
                             $sale_data_grand_date_filt = DB::table('user_sales_order_view')
                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order_view.order_id')
                                    ->join('product_wise_scheme_plan_details','product_wise_scheme_plan_details.product_id','=','user_sales_order_details.product_id')
                                    ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$find_month' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$find_month'")
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                    ->where('user_sales_order_view.company_id',$company_id)
                                    ->groupBy('date');
                                    if(!empty($user))
                                    {
                                        $sale_data_grand_date_filt->whereIn('user_sales_order_view.user_id',$user);
                                    }
                                    if(!empty($state))
                                    {
                                        $sale_data_grand_date_filt->whereIn('l3_id',$state);
                                        $sale_data_grand_date_filt->whereIn('product_wise_scheme_plan_details.state_id',$state);
                                    }
                            $sale_data_grand_date = $sale_data_grand_date_filt->pluck(DB::raw("SUM((rate*quantity)-((rate*quantity)*(value_amount_percentage/100))) as sale_value"),DB::raw("CONCAT(date)"));

                        }
                        else
                        {
                             $sale_data_grand_date_filt = DB::table('user_sales_order_view')
                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order_view.order_id')
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                    ->where('user_sales_order_view.company_id',$company_id)
                                    ->groupBy('date');
                                     if(!empty($user))
                                        {
                                            $sale_data_grand_date_filt->whereIn('user_sales_order_view.user_id',$user);
                                        }
                            $sale_data_grand_date = $sale_data_grand_date_filt->pluck(DB::raw("SUM(rate*quantity) as sale_value"),DB::raw("CONCAT(date)"));

                        }
                       // dd($sale_data_grand_date);

                        // $primary_sale_data_grand_date_filt = DB::table('user_primary_sales_order')
                        //             ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                        //             ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m')='$month'")
                        //             ->where('user_primary_sales_order.company_id',$company_id)
                        //             ->groupBy('sale_date');
                        //              if(!empty($user))
                        //                 {
                        //                     $primary_sale_data_grand_date_filt->whereIn('user_primary_sales_order.created_person_id',$user);
                        //                 }
                        $primary_sale_data_grand_date = array();  

                        // $travelling_expense_data_grand_date_filt = DB::table('travelling_expense_bill')
                        //             ->whereRaw("DATE_FORMAT(travellingDate,'%Y-%m')='$month'")
                        //             ->where('travelling_expense_bill.company_id',$company_id)
                        //             ->groupBy('travellingDate');
                        //              if(!empty($user))
                        //                 {
                        //                     $travelling_expense_data_grand_date_filt->whereIn('travelling_expense_bill.user_id',$user);
                        //                 }
                        $travelling_expense_data_grand_date = array();                       

                        $total_call_data_grand_date_filt = DB::table('user_sales_order_view')
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                    ->where('call_status',1)
                                    ->where('user_sales_order_view.company_id',$company_id)
                                    ->groupBy('date');
                                     if(!empty($user))
                                        {
                                            $total_call_data_grand_date_filt->whereIn('user_sales_order_view.user_id',$user);
                                        }
                                        if(!empty($state))
                                        {
                                            $total_call_data_grand_date_filt->whereIn('l3_id',$state);
                                        }
                        $total_call_data_grand_date =   $total_call_data_grand_date_filt->pluck(DB::raw("COUNT(order_id) as sale_value"),DB::raw("CONCAT(date)"));

                        $total_call_t_data_grand_date_filt = DB::table('user_sales_order_view')
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                    ->where('user_sales_order_view.company_id',$company_id)
                                    ->groupBy('date');
                                 if(!empty($user))
                                    {
                                        $total_call_t_data_grand_date_filt->whereIn('user_sales_order_view.user_id',$user);
                                    }
                                if(!empty($state))
                                {
                                    $total_call_t_data_grand_date_filt->whereIn('l3_id',$state);
                                }
                        $total_call_t_data_grand_date =  $total_call_t_data_grand_date_filt->pluck(DB::raw("COUNT(order_id) as sale_value"),DB::raw("CONCAT(date)"));
                                    // dd($total_call_t_data_grand_date);
                        // date wise total ends here 

                        // grand total starts here 
                        if($company_id == '44')
                        {
                            $grand_sale_data_grand_date_filt = DB::table('user_sales_order_view')
                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order_view.order_id')
                                    ->join('product_wise_scheme_plan_details','product_wise_scheme_plan_details.product_id','=','user_sales_order_details.product_id')
                                    ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$find_month' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$find_month'")
                                    ->where('user_sales_order_view.company_id',$company_id)
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                    ->select(DB::raw("SUM((rate*quantity)-((rate*quantity)*(value_amount_percentage/100))) as sale_value"));
                                    if(!empty($user))
                                        {
                                            $grand_sale_data_grand_date_filt->whereIn('user_sales_order_view.user_id',$user);
                                        }
                                    if(!empty($state))
                                    {
                                        $grand_sale_data_grand_date_filt->whereIn('product_wise_scheme_plan_details.state_id',$state);
                                        $grand_sale_data_grand_date_filt->whereIn('l3_id',$state);
                                    }
                            $grand_sale_data_grand_date = $grand_sale_data_grand_date_filt->first();
                        }
                        else
                        {
                            $grand_sale_data_grand_date_filt = DB::table('user_sales_order_view')
                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order_view.order_id')
                                    ->where('user_sales_order_view.company_id',$company_id)
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                    ->select(DB::raw("SUM(rate*quantity) as sale_value"));
                                    if(!empty($user))
                                        {
                                            $grand_sale_data_grand_date_filt->whereIn('user_sales_order_view.user_id',$user);
                                        }
                            $grand_sale_data_grand_date = $grand_sale_data_grand_date_filt->first();
                        }
                        // dd($grand_sale_data_grand_date);

                        $grand_primary_sale_data_grand_date_filt = DB::table('user_primary_sales_order')
                                    ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                                    ->where('user_primary_sales_order.company_id',$company_id)
                                    ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m')='$month'")
                                    ->select(DB::raw("SUM((rate*cases)+(pcs*pr_rate)) as sale_value"));
                                    if(!empty($user))
                                        {
                                            $grand_primary_sale_data_grand_date_filt->whereIn('user_primary_sales_order.created_person_id',$user);
                                        }
                        $grand_primary_sale_data_grand_date = $grand_primary_sale_data_grand_date_filt->first();  

                        $grand_travelling_expense_data_grand_date_filt = DB::table('travelling_expense_bill')
                                    ->where('travelling_expense_bill.company_id',$company_id)
                                    ->whereRaw("DATE_FORMAT(travellingDate,'%Y-%m')='$month'")
                                    ->select(DB::raw("SUM(total) as sale_value"));
                                    if(!empty($user))
                                        {
                                            $grand_travelling_expense_data_grand_date_filt->whereIn('travelling_expense_bill.user_id',$user);
                                        }
                        $grand_travelling_expense_data_grand_date = $grand_travelling_expense_data_grand_date_filt->first();                      

                        $grand_total_call_data_grand_date_filt = DB::table('user_sales_order_view')
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                    ->where('user_sales_order_view.company_id',$company_id)
                                    ->where('call_status',1)
                                    ->select(DB::raw("COUNT(order_id) as pc"));
                                     if(!empty($user))
                                        {
                                            $grand_total_call_data_grand_date_filt->whereIn('user_sales_order_view.user_id',$user);
                                        }
                                    if(!empty($state))
                                        {
                                            $grand_total_call_data_grand_date_filt->whereIn('l3_id',$state);
                                        }
                                  $grand_total_call_data_grand_date = $grand_total_call_data_grand_date_filt->first();

                        $grand_total_call_t_data_grand_date_filt = DB::table('user_sales_order_view')
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$month'")
                                    ->where('user_sales_order_view.company_id',$company_id)
                                    ->select(DB::raw("COUNT(order_id) as tc"));
                                     if(!empty($user))
                                        {
                                            $grand_total_call_t_data_grand_date_filt->whereIn('user_sales_order_view.user_id',$user);
                                        }
                                    if(!empty($state))
                                        {
                                            $grand_total_call_t_data_grand_date_filt->whereIn('l3_id',$state);
                                        }
                        $grand_total_call_t_data_grand_date = $grand_total_call_t_data_grand_date_filt->first();
                        // grand total ends here 

                        $dealer_count_user_wise_data = DB::table('dealer_location_rate_list')
                                                ->where('company_id',$company_id)
                                                ->groupBy('user_id');
                                                if(!empty($user))
                                                {
                                                    $dealer_count_user_wise_data->whereIn('dealer_location_rate_list.user_id',$user);
                                                }
                        $dealer_count_user_wise = $dealer_count_user_wise_data->pluck(DB::raw("COUNT(DISTINCT dealer_id) as dealer_id"),'user_id');

                        $retailer_count_user_wise_data = DB::table('dealer_location_rate_list')
                                                ->join('retailer','retailer.location_id','=','dealer_location_rate_list.location_id')
                                                ->where('dealer_location_rate_list.company_id',$company_id)
                                                ->groupBy('dealer_location_rate_list.user_id');
                                                if(!empty($user))
                                                {
                                                    $retailer_count_user_wise_data->whereIn('dealer_location_rate_list.user_id',$user);
                                                }
                        $retailer_count_user_wise = $retailer_count_user_wise_data->pluck(DB::raw("COUNT(DISTINCT retailer.id) as dealer_id"),'dealer_location_rate_list.user_id');
                        $date = date('Y-m-d');
                        $retailer_count_added_per_day = DB::table('retailer')
                                                    ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')='$find_month'")
                                                    ->where('company_id',$company_id)
                                                    ->groupBy('created_by_person_id',DB::raw("date_format(created_on,'%Y-%m-%d')"))
                                                    ->pluck(DB::raw("COUNT(DISTINCT retailer.id) as dealer_id"),DB::raw("CONCAT(created_by_person_id,date_format(created_on,'%Y-%m-%d')) as data"));

                        if($company_id == 44)
                        {
                            $retailer_sale_added_per_day_data = DB::table('retailer')
                                                    ->join('user_sales_order_view','user_sales_order_view.retailer_id','=','retailer.id')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order_view.order_id')
                                                    ->join('product_wise_scheme_plan_details','product_wise_scheme_plan_details.product_id','=','user_sales_order_details.product_id')
                                                    ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$find_month' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$find_month'")
                                                    ->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')='$find_month'")
                                                    ->where('retailer.company_id',$company_id)
                                                    ->groupBy('retailer.created_by_person_id',DB::raw("date_format(created_on,'%Y-%m-%d')"));
                                                    if(!empty($state))
                                                    {
                                                        $retailer_sale_added_per_day_data->whereIn('product_wise_scheme_plan_details.state_id',$state);
                                                    }
                                                    if(!empty($user))
                                                    {
                                                        $retailer_count_user_wise_data->whereIn('retailer.created_by_person_id',$user);
                                                    }
                            $retailer_sale_added_per_day = $retailer_sale_added_per_day_data->pluck(DB::raw("SUM((rate*quantity)-((rate*quantity)*(value_amount_percentage/100))) as sale_value"),DB::raw("CONCAT(created_by_person_id,date_format(retailer.created_on,'%Y-%m-%d')) as data"));
                        }
                        else
                        {
                            $retailer_sale_added_per_day = DB::table('retailer')
                                                    ->join('user_sales_order_view','user_sales_order_view.retailer_id','=','retailer.id')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order_view.order_id')
                                                    // ->join('product_wise_scheme_plan_details','product_wise_scheme_plan_details.product_id','=','user_sales_order_details.product_id')
                                                    ->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')='$find_month'")
                                                    ->where('retailer.company_id',$company_id)
                                                    ->groupBy('retailer.created_by_person_id',DB::raw("date_format(created_on,'%Y-%m-%d')"))
                                                    ->pluck(DB::raw("SUM(rate*quantity) as sale_value"),DB::raw("CONCAT(created_by_person_id,date_format(retailer.created_on,'%Y-%m-%d')) as data"));
                        }
                        // dd($retailer_sale_added_per_day);
                        $retailer_count_added_month = DB::table('retailer')
                                                    ->where('company_id',$company_id)
                                                    ->whereRaw("DATE_FORMAT(created_on,'%Y-%m')='$month'")
                                                    ->groupBy('created_by_person_id')
                                                    ->pluck(DB::raw("COUNT(DISTINCT retailer.id) as dealer_id"),'created_by_person_id');
                                                    
                        $distributor_name = DB::table('user_sales_order')
                                            ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
                                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$find_month'")
                                            ->where('user_sales_order.company_id',$company_id)
                                            ->groupBy('user_sales_order.user_id')
                                            ->pluck(DB::raw("group_concat(DISTINCT dealer.name) as dealer_name"),DB::raw("concat(user_sales_order.date,user_sales_order.user_id) as d"));

                        $beat_name = DB::table('user_sales_order')
                                    ->join('location_7','location_7.id','=','user_sales_order.location_id')
                                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$find_month'")
                                    ->where('user_sales_order.company_id',$company_id)
                                    ->groupBy('user_sales_order.user_id')
                                    ->pluck(DB::raw("group_concat(DISTINCT location_7.name) as dealer_name"),DB::raw("concat(user_sales_order.date,user_sales_order.user_id) as b"));

                        $working_status = DB::table('user_daily_attendance')
                                        ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                                        ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')='$find_month'")
                                        ->where('user_daily_attendance.company_id',$company_id)
                                        ->where('_working_status.company_id',$company_id)
                                        ->groupBy('user_daily_attendance.user_id','user_daily_attendance.work_date')
                                        ->pluck('_working_status.name as name',DB::raw("concat(date_format(user_daily_attendance.work_date,'%Y-%m-%d'),user_daily_attendance.user_id) as a"));


                        $working_with = DB::table('user_daily_attendance')
                                        ->join('person','person.id','=','user_daily_attendance.working_with')
                                        ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')='$find_month'")
                                        ->where('user_daily_attendance.company_id',$company_id)
                                        ->where('person.company_id',$company_id)
                                        ->groupBy('user_daily_attendance.user_id','user_daily_attendance.work_date')
                                        ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("concat(date_format(user_daily_attendance.work_date,'%Y-%m-%d'),user_daily_attendance.user_id) as a"));


                        $month_target_data = DB::table('monthly_tour_program')
                                    ->whereRaw("DATE_FORMAT(working_date,'%Y-%m')='$month'")
                                    ->where('monthly_tour_program.company_id',$company_id)
                                    ->groupBy('person_id');
                                    if(!empty($state))
                                    {
                                        $month_target_data->whereIn('l3_id',$state);
                                    }
                                    if(!empty($user))
                                    {
                                        $month_target_data->whereIn('monthly_tour_program.person_id',$user);
                                    }

                        $month_target = $month_target_data->pluck(DB::raw("SUM(rd) as month_target"),DB::raw("CONCAT(person_id)"));

                         $day_target = DB::table('monthly_tour_program')
                                    ->whereRaw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d')='$find_month'")
                                    ->where('monthly_tour_program.company_id',$company_id)
                                    ->groupBy('monthly_tour_program.person_id')
                                    ->pluck(DB::raw("SUM(rd) as day_target"),DB::raw("concat(monthly_tour_program.working_date,monthly_tour_program.person_id) as b"));



                        $mail_id = $sent_mail_email;
                        $mail = Mail::send('reports/time-report-sale/btwSendMail', array(
                                        'records' => $person_query,
                                        'month' => $month,
                                        'datesArr'=>$datesArr,
                                        'location_5'=>$location_5,
                                        'location_6'=>$location_6,
                                        'datesDisplayArr'=>$datesDisplayArr,
                                        'total_days'=>$total_days,
                                        'first_time'=>$first_time,
                                        'last_time'=>$last_time,
                                        'upto_check_in' =>$upto_check_in,
                                        'upto_check_out'=>$upto_check_out,
                                        'count_total_att'=>$count_total_att,
                                        'total_call_data'=> $total_call_data,
                                        'sale_data'=> $sale_data,
                                        'sale_data_working_town'=> $sale_data_working_town,
                                        'primary_sale_data'=> $primary_sale_data,
                                        'primary_sale_data_working_town'=> $primary_sale_data_working_town,
                                        'travelling_expense_data'=> $travelling_expense_data,
                                        'total_call_data_grand'=> $total_call_data_grand,
                                        'sale_data_grand'=> $sale_data_grand,
                                        'primary_sale_data_grand'=> $primary_sale_data_grand,
                                        'travelling_expense_data_grand'=> $travelling_expense_data_grand,
                                        'total_t_call_data'=> $total_t_call_data,
                                        'total_call_t_data_grand'=> $total_call_t_data_grand,
                                        'sale_data_grand_date'=> $sale_data_grand_date,
                                        'primary_sale_data_grand_date'=> $primary_sale_data_grand_date,
                                        'travelling_expense_data_grand_date'=> $travelling_expense_data_grand_date,
                                        'total_call_data_grand_date'=> $total_call_data_grand_date,
                                        'total_call_t_data_grand_date'=> $total_call_t_data_grand_date,
                                        'grand_sale_data_grand_date'=> $grand_sale_data_grand_date,
                                        'grand_primary_sale_data_grand_date'=> $grand_primary_sale_data_grand_date,
                                        'grand_travelling_expense_data_grand_date'=> $grand_travelling_expense_data_grand_date,
                                        'grand_total_call_data_grand_date'=> $grand_total_call_data_grand_date,
                                        'grand_total_call_t_data_grand_date'=> $grand_total_call_t_data_grand_date,
                                        'dealer_count_user_wise'=> $dealer_count_user_wise,
                                        'retailer_count_user_wise'=> $retailer_count_user_wise,
                                        'retailer_count_added_per_day'=> $retailer_count_added_per_day,
                                        // 'retailer_count_added_per_day_on'=> $retailer_count_added_per_day_on,
                                        'retailer_count_added_month'=> $retailer_count_added_month,
                                        'first_address'=> $first_address,
                                        'last_address'=> $last_address,
                                        'distributor_name'=> $distributor_name,
                                        'beat_name'=> $beat_name,
                                        'working_status'=> $working_status,
                                        'working_with'=> $working_with,
                                        'retailer_sale_added_per_day'=> $retailer_sale_added_per_day,
                                        'month_target'=> $month_target,
                                        'day_target'=> $day_target,
                                                    

                                    ) , function($message) use($mail_id,$subject)
                            {
                                $message->from('manacle.php1@gmail.com');

                                $message->to($mail_id)->subject($subject);

                            });
                    }
                        
                    // if(count($fetch_mail_id)>0)
                    // {
                    //     foreach ($fetch_mail_id as $mail_key => $mail_value) 
                    //     {
                            
                }
            }
            

            
       
    
    } 


	
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
    public function download_image(Request $request)
    {
        $filepath = public_path('circular_image/')."20210203115444.png";
        return Response::download($filepath);
    }




    public function hitkaryMailSent(Request $request)
    {
        $subject = 'Daily Report';
        $company_id = '87';
        $role_id = array("534","535","555","525");
        $current_date = date('Y-m-d');
        $yesterday = date('Y-m-d',strtotime("-1 days"));


       $search_all_user = DB::table('send_mail_details')
                        ->where('company_id',$company_id)
                        ->where('status','=','1')
                        ->get();


                        // dd($search_all_user);
        foreach ($search_all_user as $saukey => $sauvalue) {

        $manager_mail = $sauvalue->name;

        $user = array();
        if($sauvalue->user_id != '0')
        {
            Session::forget('juniordata');
            // $login_user=Auth::user()->id;
            $user=self::getJuniorUser($sauvalue->user_id);
            Session::push('juniordata', $sauvalue->user_id);
            $user = $request->session()->get('juniordata');
            if(empty($user))
            {
                $user[]=$sauvalue->user_id;
            }
        }

        // dd($user);

        
                $dailyAttenData = DB::table('user_daily_attendance')
                                ->where('company_id',$company_id)  
                                ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')='$yesterday'")
                                ->groupBy('user_id');
                                if(!empty($user)){
                                    $dailyAttenData->whereIn('user_id',$user);
                                }
                $dailyAtten = $dailyAttenData->pluck('user_id','user_id')->toArray();

                $activeUserData = DB::table('person')
                            ->join('person_login','person_login.person_id','=','person.id')
                            ->where('person_status','=','1')
                            ->where('person.company_id',$company_id)
                            ->groupBy('person.id');
                            if(!empty($user)){
                                $activeUserData->whereIn('person.id',$user);
                            }
                $activeUser = $activeUserData->pluck('person.id','person.id')->toArray();


                $finalUsers = array_merge($dailyAtten,$activeUser);

                $finalUserarray = array_values(array_unique($finalUsers));

                // dd($finalUserarray);








                $userDetails = DB::table('person')
                            ->join('person_login','person_login.person_id','=','person.id')
                            ->join('location_3','location_3.id','=','person.state_id')
                            ->join('_role','_role.role_id','=','person.role_id')
                            ->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'rolename','location_3.name as state_name')
                            // ->where('person_status','=','1')
                            ->where('person.company_id',$company_id)
                            ->whereNotIn('person.role_id',$role_id)
                            ->whereIn('person.id',$finalUserarray)
                            ->groupBy('person.id')
                            ->get();

                            // dd($userDetails);

                $userId = array();
                foreach ($userDetails as $udkey => $udvalue) {
                    $userId[] = $udvalue->user_id;
                }

                // dd($userId);

                $salesDetails = DB::table('user_sales_order')
                                ->join('location_7','location_7.id','=','user_sales_order.location_id')
                                ->join('location_6','location_6.id','=','location_7.location_6_id')
                                ->join('retailer','retailer.location_id','=','location_7.id')
                                ->select(DB::raw("GROUP_CONCAT(DISTINCT location_7.name) as beat_name"),DB::raw("GROUP_CONCAT(DISTINCT location_6.name) as city_name"),DB::raw("COUNT(DISTINCT retailer.id) as beat_outlet"),'user_sales_order.user_id',DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as total_call"),DB::raw("MIN(user_sales_order.time) as first_call"),DB::raw("MAX(user_sales_order.time) as last_call"))
                                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$yesterday'")
                                ->whereIn('user_sales_order.user_id',$userId)
                                ->where('user_sales_order.company_id',$company_id)
                                ->where('retailer.company_id',$company_id)
                                ->where('location_7.company_id',$company_id)
                                ->groupBy('user_sales_order.user_id')
                                ->get();
                 $finalSales = array();
                foreach ($salesDetails as $sdkey => $sdvalue) {
                    $finalSales[$sdvalue->user_id]['beat_name'] = $sdvalue->beat_name;
                    $finalSales[$sdvalue->user_id]['city_name'] = $sdvalue->city_name;
                    $finalSales[$sdvalue->user_id]['beat_outlet'] = $sdvalue->beat_outlet;
                    $finalSales[$sdvalue->user_id]['total_call'] = $sdvalue->total_call;
                    $finalSales[$sdvalue->user_id]['first_call'] = $sdvalue->first_call;
                    $finalSales[$sdvalue->user_id]['last_call'] = $sdvalue->last_call;
                    $finalSales[$sdvalue->user_id]['user_id'] = $sdvalue->user_id;
                }
                // dd($finalSales);



                $productiveDetails = DB::table('user_sales_order')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                ->select(DB::raw("SUM(rate*quantity) as sale"),DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as productive_call"),'user_sales_order.user_id')
                                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$yesterday'")
                                ->whereIn('user_sales_order.user_id',$userId)
                                ->where('user_sales_order.company_id',$company_id)
                                ->where('user_sales_order_details.company_id',$company_id)
                                ->groupBy('user_sales_order.user_id')
                                ->get();
                 $finalSalesDetails = array();
                foreach ($productiveDetails as $sddkey => $sddvalue) {
                    $finalSalesDetails[$sddvalue->user_id]['sale'] = $sddvalue->sale;
                    $finalSalesDetails[$sddvalue->user_id]['productive_call'] = $sddvalue->productive_call;
                    $finalSalesDetails[$sddvalue->user_id]['user_id'] = $sddvalue->user_id;
                }


                $attendanceDetail = DB::table('user_daily_attendance')
                                        ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d')='$yesterday'")
                                        ->whereIn('user_id',$userId)
                                        ->where('company_id',$company_id)
                                        ->groupBy('user_id')
                                        ->pluck(DB::raw("DATE_FORMAT(user_daily_attendance.work_date,'%H:%i:%s') as time"),'user_id')->toArray();


                 $newOutletCount = DB::table('retailer')
                                        ->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')='$yesterday'")
                                        ->whereIn('created_by_person_id',$userId)
                                        ->where('retailer.company_id',$company_id)
                                        ->groupBy('created_by_person_id')
                                        ->pluck(DB::raw("COUNT(DISTINCT retailer.id) as newCount"),'created_by_person_id')->toArray();


                // dd($attendanceDetail);

                $totalOutlet = DB::table('person')
                            ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                            ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
                            ->join('retailer','retailer.location_id','=','location_7.id')
                            ->where('retailer.retailer_status','=','1')
                            ->whereIn('person.id',$userId)
                            ->where('person.company_id',$company_id)
                            ->groupBy('person.id')
                            ->pluck(DB::raw("COUNT(DISTINCT retailer.id) as user_retailer"),'person.id')->toArray();


                $dailyReport = DB::table('daily_reporting')
                                ->select(DB::raw("COUNT(id) as did"),DB::raw("GROUP_CONCAT(DISTINCT daily_reporting.remarks) as remarks"),'daily_reporting.user_id',DB::raw("GROUP_CONCAT( daily_reporting.work_status) as work_status"))
                                ->whereRaw("DATE_FORMAT(daily_reporting.work_date,'%Y-%m-%d')='$yesterday'")
                                ->whereIn('user_id',$userId)
                                ->where('company_id',$company_id)
                                // ->where('daily_reporting.work_status','like','%DISTRIBUTORS VISIT%')
                                // ->where('daily_reporting.work_status','=','CURRENT DISTRIBUTORS VISIT')
                                ->groupBy('user_id')
                                ->get();


                $finalDailyDetails = array();
                foreach ($dailyReport as $drkey => $drvalue) {
                    $finalDailyDetails[$drvalue->user_id]['did'] = $drvalue->did;
                    $finalDailyDetails[$drvalue->user_id]['remarks'] = $drvalue->remarks;
                    $finalDailyDetails[$drvalue->user_id]['work_status'] = $drvalue->work_status;
                    $finalDailyDetails[$drvalue->user_id]['user_id'] = $drvalue->user_id;
                }
                $output= '';

                // attchment code starts here

                // $output .= 'Daily Report'.'('.$yesterday.'),';
                // $output .="\n";




                 $output .= 'S.no,Date,State,User Name,Role,Beat name,City Name,Beat Outlet,New Outlet,Attendance Time,First Call Time,Last Call Time,Total Outlet Of User,Total Call Of The Day ,Productive Call,Non Productive Call,Total Order Value,Daily Reporting,Daily Reporting Status,Daily Reporting Remarks,';
                $output .="\n";


                $beat_outletExcelArray = array();
                $newOutletCArray = array();
                $totalOutletUserExcelArray = array();
                $total_callExcelArray = array();
                $productive_callExcelArray = array();
                $nonProdCallExcelArray = array();
                $saleExcelArray = array();


                 if(!empty($userDetails)){
                    foreach($userDetails as $key=>$data){

                        $user_idExcel = $data->user_id;

                        $beatsExcel = !empty($finalSales[$user_idExcel]['beat_name'])?$finalSales[$user_idExcel]['beat_name']:'-';
                        $cityExcel = !empty($finalSales[$user_idExcel]['city_name'])?$finalSales[$user_idExcel]['city_name']:'-';

                        $beat_outletExcel = !empty($finalSales[$user_idExcel]['beat_outlet'])?$finalSales[$user_idExcel]['beat_outlet']:'0';
                        $beat_outletExcelArray[] = !empty($finalSales[$user_idExcel]['beat_outlet'])?$finalSales[$user_idExcel]['beat_outlet']:'0';


                        $newOutletC = !empty($newOutletCount[$user_idExcel])?$newOutletCount[$user_idExcel]:'0';
                        $newOutletCArray[] = !empty($newOutletCount[$user_idExcel])?$newOutletCount[$user_idExcel]:'0';




                        $att_timeExcel = !empty($attendanceDetail[$user_idExcel])?$attendanceDetail[$user_idExcel]:'-';
                        $first_callExcel = !empty($finalSales[$user_idExcel]['first_call'])?$finalSales[$user_idExcel]['first_call']:'-';
                        $last_callExcel = !empty($finalSales[$user_idExcel]['last_call'])?$finalSales[$user_idExcel]['last_call']:'-';

                        $total_callExcel = !empty($finalSales[$user_idExcel]['total_call'])?$finalSales[$user_idExcel]['total_call']:'0';
                        $total_callExcelArray[] = !empty($finalSales[$user_idExcel]['total_call'])?$finalSales[$user_idExcel]['total_call']:'0';



                        $totalOutletUserExcel = !empty($totalOutlet[$user_idExcel])?$totalOutlet[$user_idExcel]:'0';
                        $totalOutletUserExcelArray[] = !empty($totalOutlet[$user_idExcel])?$totalOutlet[$user_idExcel]:'0';



                        $productive_callExcel = !empty($finalSalesDetails[$user_idExcel]['productive_call'])?$finalSalesDetails[$user_idExcel]['productive_call']:'0';
                        $productive_callExcelArray[] = !empty($finalSalesDetails[$user_idExcel]['productive_call'])?$finalSalesDetails[$user_idExcel]['productive_call']:'0';



                        $saleExcel = !empty($finalSalesDetails[$user_idExcel]['sale'])?$finalSalesDetails[$user_idExcel]['sale']:'0';
                        $saleExcelArray[] = !empty($finalSalesDetails[$user_idExcel]['sale'])?$finalSalesDetails[$user_idExcel]['sale']:'0';


                        $nonProdCallExcel = $total_callExcel-$productive_callExcel;
                        $nonProdCallExcelArray[] = $total_callExcel-$productive_callExcel;



                        $db_visitExcel = !empty($finalDailyDetails[$user_idExcel]['did'])?'YES':'NO';
                        $remarksExcel = !empty($finalDailyDetails[$user_idExcel]['remarks'])?$finalDailyDetails[$user_idExcel]['remarks']:'-';

                        $workStatus = !empty($finalDailyDetails[$user_idExcel]['work_status'])?$finalDailyDetails[$user_idExcel]['work_status']:'-';



                        $output .= ($key+1).',';

                        $output .= $yesterday.',';


                        $output .= str_replace(',','',$data->state_name).',';

                        $output .= str_replace(',','',$data->user_name).',';
                        $output .= str_replace(',','',$data->rolename).',';

                        $output .= str_replace(',','/',$beatsExcel).',';

                        $output .= str_replace(',','/',$cityExcel).',';



                        $output .= $beat_outletExcel.',';


                        $output .= $newOutletC.',';
                        


                        $output .= $att_timeExcel.',';
                        $output .= $first_callExcel.',';
                        $output .= $last_callExcel.',';

                        $output .= $totalOutletUserExcel.',';


                        $output .= $total_callExcel.',';
                        $output .= $productive_callExcel.',';
                        $output .= $nonProdCallExcel.',';

                        $output .= $saleExcel.',';
                        $output .= $db_visitExcel.',';
                        $output .= str_replace(',','/',$workStatus).',';

                        $output .= str_replace(',','/',$remarksExcel).',';

                        $output .="\n";


                    }
                }


                // grand total start
                        $output .= 'Grand Total'.',';
                        $output .= ',';
                        $output .= ',';
                        $output .= ',';
                        $output .= ',';
                        $output .= ',';
                        $output .= ',';
                        $output .= array_sum($beat_outletExcelArray).',';
                        $output .= array_sum($newOutletCArray).',';
                        $output .= ',';
                        $output .= ',';
                        $output .= ',';
                        $output .= array_sum($totalOutletUserExcelArray).',';
                        $output .= array_sum($total_callExcelArray).',';
                        $output .= array_sum($productive_callExcelArray).',';
                        $output .= array_sum($nonProdCallExcelArray).',';
                        $output .= array_sum($saleExcelArray).',';



                // grand total ends


                   $files = touch("mail_excel/summaryHitkary.csv");
                    // dd($files);
                    $file = fopen('mail_excel/summaryHitkary.csv',"wb");
                    fwrite($file,$output);
                    fclose($file);
                    // dd($file);
                    $path = $_SERVER['SERVER_NAME'].'/public/mail_excel/';

                    $pdf_path = public_path().'/mail_excel/summaryHitkary.csv';



                // attchment code ends here 


                            
            


                            $mail = Mail::send('reports/sendMails/sendMailHitkary', array(
                                                    'userDetails' => $userDetails,
                                                    'finalSales' => $finalSales,
                                                    'finalSalesDetails'=>$finalSalesDetails,
                                                    'attendanceDetail'=>$attendanceDetail,
                                                    'totalOutlet'=>$totalOutlet,
                                                    'finalDailyDetails'=>$finalDailyDetails,
                                                    'yesterday'=>$yesterday,

                                    ) , function($message) use($manager_mail,$subject,$pdf_path)
                            {
                                    
                                $message->from('manacle.php1@gmail.com');

                                $cc_mail = "pawan.singh@manacleindia.com";


                                $message->to($manager_mail)->cc($cc_mail)->subject($subject)->attach($pdf_path);

                            });

                            // dd($junior_person_details);
                    

            }               

    } 



}
