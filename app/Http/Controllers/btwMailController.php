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
use App\MonthlyTourProgram;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;



class btwMailController extends Controller
{
	public $successStatus=200;
	public $salt="Rajdhani";
	public $otpString = '0123456789';

	

	public function btwMailSent(Request $request)
	{
		$subject = 'Sale Report';
        $company_id = array("43");
        $role_id = array("149","167","148","146","145","155","154","149","167","168","301");
        $current_date = date('Y-m-d');
        $yesterday = date('Y-m-d',strtotime("-1 days"));


        // dd($role_id);
   		// $mail_id = 'bhoopendranath@manacleindia.com';

   		$search_all_user = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.email')
                        ->where('person_status',1)
                        ->whereIn('person.company_id',$company_id)
                        ->whereIn('person.role_id',$role_id)
                        ->groupBy('person.id')
                        ->get();

                        // dd($search_all_user);



         foreach ($search_all_user as $saukey => $sauvalue) {

                $manager_name = $sauvalue->user_name;
                $manager_mail = $sauvalue->email;
                // $manager_mail = "pooja@manacleindia.com";
                            
                   if($sauvalue->user_id != '0')
                    {
                        Session::forget('juniordata');
                        $juniors_array=self::getJuniorUser($sauvalue->user_id);
                        Session::push('juniordata', $sauvalue->user_id);
                        // Session::push('juniordata', $sauvalue->user_id);
                        $juniors_array = $request->session()->get('juniordata');
                        if(empty($juniors_array))
                        {
                            $juniors_array = array();
                        }
                    }
                    // dd($juniors_array);
                    $junior_count = count($juniors_array);

                    if($junior_count > 1){         // mail send to those whose have juniors

                            $sale_details = DB::table('user_sales_order')
                                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                // ->join('location_7','location_7.id','=','user_sales_order.location_id')
                                                // ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
                                                ->select('user_id',DB::raw("SUM(rate*quantity) as total_sale"),DB::raw("COUNT(DISTINCT user_sales_order.order_id) as productive_calls"),DB::raw("GROUP_CONCAT(DISTINCT user_sales_order.remarks) as concatinated_remarks"))
                                                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$yesterday'")
                                                ->whereIn('user_id',$juniors_array)
                                                ->where('call_status',1)
                                                ->groupBy('user_id')
                                                ->get();

                            $final_sale_details = array();
                            foreach ($sale_details as $sdkey => $sdvalue) {
                                $sale_user_id = $sdvalue->user_id;

                                $final_sale[$sale_user_id]['user_id'] = $sdvalue->user_id;
                                // $final_sale[$sale_user_id]['concatinated_beat'] = $sdvalue->concatinated_beat;
                                // $final_sale[$sale_user_id]['concatinated_dealer'] = $sdvalue->concatinated_dealer;
                                $final_sale[$sale_user_id]['total_sale'] = $sdvalue->total_sale;
                                $final_sale[$sale_user_id]['productive_calls'] = $sdvalue->productive_calls;
                                $final_sale[$sale_user_id]['concatinated_remarks'] = $sdvalue->concatinated_remarks;

                                $final_sale_details = $final_sale;
                            }

                            $total_calls = DB::table('user_sales_order')
                                        ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$yesterday'")
                                        ->whereIn('user_id',$juniors_array)
                                        ->groupBy('user_id')
                                        ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.order_id) as total_calls"),'user_id')->toArray();


                            $concatinated_beat = DB::table('user_sales_order')
                                        ->join('location_7','location_7.id','=','user_sales_order.location_id')
                                        ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$yesterday'")
                                        ->whereIn('user_id',$juniors_array)
                                        ->groupBy('user_id')
                                        ->pluck(DB::raw("GROUP_CONCAT(DISTINCT location_7.name) as concatinated_beat"),'user_id')->toArray();


                            $concatinated_dealer = DB::table('user_sales_order')
                                        ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
                                        ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$yesterday'")
                                        ->whereIn('user_id',$juniors_array)
                                        ->groupBy('user_id')
                                        ->pluck(DB::raw("GROUP_CONCAT(DISTINCT dealer.name) as concatinated_dealer"),'user_id')->toArray();

                            $new_retailer = DB::table('retailer')
                                        ->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')='$yesterday'")
                                        ->whereIn('created_by_person_id',$juniors_array)
                                        ->groupBy('created_by_person_id')
                                        ->pluck(DB::raw("COUNT(retailer.id) as new_outlet"),'created_by_person_id')->toArray();
                                                // dd($new_retailer);

                            $distance = DB::table('travelling_expense_bill')
                                        ->whereRaw("DATE_FORMAT(travelling_expense_bill.travellingDate,'%Y-%m-%d')='$yesterday'")
                                        ->whereIn('user_id',$juniors_array)
                                        ->groupBy('user_id')
                                        ->pluck('distance','user_id')->toArray();

                                        // dd($distance);



                            $junior_person_details = DB::table('person')
                                                    ->join('person_login','person_login.person_id','=','person.id')
                                                    ->join('location_3','location_3.id','=','person.state_id')
                                                    ->join('_role','_role.role_id','=','person.role_id')
                                                    ->select('person.id as junior_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as junior_name"),'location_3.name as junior_state','rolename')
                                                    ->where('person_status',1)
                                                    ->whereIn('person.id',$juniors_array)
                                                    ->groupBy('person.id')
                                                    ->get();

                            $mail = Mail::send('reports/sendMails/sendMail', array(
                                                    'junior_person_details' => $junior_person_details,
                                                    'new_retailer' => $new_retailer,
                                                    'total_calls'=>$total_calls,
                                                    'final_sale_details'=>$final_sale_details,
                                                    'manager_name'=>$manager_name,
                                                    'distance'=>$distance,
                                                    'concatinated_beat'=>$concatinated_beat,
                                                    'concatinated_dealer'=>$concatinated_dealer,

                                    ) , function($message) use($manager_mail,$subject)
                            {
                                    
                                $message->from('manacle.php1@gmail.com');

                                $cc_mail = "hr3@btwindia.com";


                                $message->to($manager_mail)->cc($cc_mail)->subject($subject);

                            });

                            // dd($junior_person_details);
                    }

            }               

	} 


    public function btwTourProgramMailSent(Request $request)
    {
        $subject = 'Tour Program Report';
        $company_id = array("43");
        $role_id = array("149","167","148","146","145","155","154","149","167","168","301");        
        // $current_date = date('Y-m-d');
        // $from_date = date('Y-m-d',strtotime("-6 days"));

        $current_date = date('Y-m-d',strtotime("+6 days"));
        $from_date =  date('Y-m-d');

        // $manager_mail = "chiefsales@btwindia.com";
        // $manager_mail = "rohit.k@manacleindia.com";

        $search_all_user = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.email')
                        ->where('person_status',1)
                        ->whereIn('person.company_id',$company_id)
                        ->whereIn('person.role_id',$role_id)
                        ->groupBy('person.id')
                        ->get();

                        // dd($search_all_user);



        foreach ($search_all_user as $saukey => $sauvalue) {

           
            $manager_mail = $sauvalue->email;
        // $manager_mail = "rohit.k@manacleindia.com";

            // $manager_mail = "pooja@manacleindia.com";
                        
               if($sauvalue->user_id != '0')
                {
                    Session::forget('juniordata');
                    $juniors_array=self::getJuniorUser($sauvalue->user_id);
                    Session::push('juniordata', $sauvalue->user_id);
                    // Session::push('juniordata', $sauvalue->user_id);
                    $juniors_array = $request->session()->get('juniordata');
                    if(empty($juniors_array))
                    {
                        $juniors_array = array();
                    }
                }
                // dd($juniors_array);
                $junior_count = count($juniors_array);

            if($junior_count > 1){         // mail send to those whose have juniors



                $plans = MonthlyTourProgram::join('person', 'monthly_tour_program.person_id', 'person.id')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('_role', '_role.role_id', '=', 'person.role_id')
                                ->join('location_3', 'location_3.id', '=', 'person.state_id')
                                ->join('location_2','location_2.id','=','location_3.location_2_id')
                                ->join('location_1','location_1.id','=','location_2.location_1_id')
                                ->join('location_6', 'location_6.id', '=', 'person.town_id')
                                ->join('location_5','location_5.id','=','location_6.location_5_id')
                                ->join('location_4','location_4.id','=','location_5.location_4_id')
                                ->join('person as p', 'p.id', '=', 'person.person_id_senior')
                                ->select('monthly_tour_program.town','person_login.person_status as status',DB::raw('CONCAT_WS(" ",p.first_name,p.middle_name,p.last_name) as senior'),'person.person_id_senior as senior_id', '_role.rolename as role', 'monthly_tour_program.admin_approved', 'monthly_tour_program.id as mid', DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as user_id','person.state_id as person_state', 'person.emp_code', 'person.head_quar', 'monthly_tour_program.working_date', 'location_1.name as l1_name', 'location_2.name as l2_name', 'location_3.name as l3_name','location_6.name as l6_name','location_5.name as l5_name','location_4.name as l4_name',  'monthly_tour_program.working_status_id','monthly_tour_program.dealer_id','locations', 'monthly_tour_program.pc', 'monthly_tour_program.rd', 'monthly_tour_program.arch', 'monthly_tour_program.collection', 'monthly_tour_program.primary_ord', 'monthly_tour_program.any_other_task', 'monthly_tour_program.new_outlet','person.mobile')
                                ->whereRaw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d') <='$current_date'")
                                ->where('person_status','=','1')
                                ->whereIn('person.company_id',$company_id)
                                ->whereIn('person.id',$juniors_array)
                                ->groupBy('working_date', 'monthly_tour_program.person_id')
                                ->get();

                                // dd($plans);

                    $location_6 = DB::table('location_6')->whereIn('company_id',$company_id)->pluck('name','id')->toArray();
                    $location_7 = DB::table('location_7')->whereIn('company_id',$company_id)->pluck('name','id')->toArray();

                    $dealer_name_array = DB::table('dealer')
                                        ->whereIn('company_id',$company_id)
                                        ->pluck('name','id')->toArray();

                    $work_status = DB::table('_task_of_the_day')->whereIn('company_id',$company_id)->pluck('task', 'id');



                        $mail = Mail::send('reports/sendMails/sendTourProgramMail', array(
                                                'plans' => $plans,
                                                'work_status' => $work_status,
                                                // 'month' => $month,
                                                'dealer_name_array'=>$dealer_name_array,
                                                'from_date'=> $from_date,
                                                'to_date'=> $current_date,
                                                'location_6'=> $location_6,
                                                'location_7'=> $location_7,

                                ) , function($message) use($manager_mail,$subject)
                        {
                                
                            $message->from('manacle.php1@gmail.com');

                            $cc_mail = "hr3@btwindia.com";
                            // $cc_mail = "rohit.k@manacleindia.com";


                            $message->to($manager_mail)->cc($cc_mail)->subject($subject);

                        });
            }
        }

    }


    public function btwDailySalesReport(Request $request)
    {

        $subject = 'Daily Sale Report';
        $company_id = array("43");
        $person_id = array("2092","3664");
        // $person_id = array("3040");
        $role_id = array("148","146","145","155","154");        

        $current_date = date('Y-m-d');
        $yesterday = date('Y-m-d',strtotime("-1 days"));

        $locationFour = DB::table('location_4')
                        ->where('status',1)
                        ->whereIn('company_id',$company_id)
                        ->groupBy('id')
                        ->pluck('name','id');


        $sumOfNewRetailer = DB::table('retailer')
                            ->join('location_7','location_7.id','=','retailer.location_id')
                            ->join('location_6','location_6.id','=','location_7.location_6_id')
                            ->join('location_5','location_5.id','=','location_6.location_5_id')
                            ->join('location_4','location_4.id','=','location_5.location_4_id')
                            ->where('retailer_status','=','1')
                            ->where('location_4.status','=','1')
                            ->whereIn('retailer.company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(retailer.created_on, '%Y-%m-%d') = '$yesterday'")
                            ->groupBy('location_4.id')
                            ->pluck(DB::raw("COUNT(retailer.id) as newRetailer"),'location_4.id');


        $sumOfTotalRetailer = DB::table('retailer')
                            ->join('location_7','location_7.id','=','retailer.location_id')
                            ->join('location_6','location_6.id','=','location_7.location_6_id')
                            ->join('location_5','location_5.id','=','location_6.location_5_id')
                            ->join('location_4','location_4.id','=','location_5.location_4_id')
                            ->where('retailer_status','=','1')
                            ->where('location_4.status','=','1')
                            ->whereIn('retailer.company_id',$company_id)
                            ->groupBy('location_4.id')
                            ->pluck(DB::raw("COUNT(retailer.id) as totalRetailer"),'location_4.id');

        $sumOfTotalCall = DB::table('user_sales_order')
                        ->join('location_7','location_7.id','=','user_sales_order.location_id')
                        ->join('location_6','location_6.id','=','location_7.location_6_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                        ->where('location_4.status','=','1')
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('location_4.id')
                        ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalCall"),'location_4.id');


        $sumOfProductiveCall = DB::table('user_sales_order')
                        ->join('location_7','location_7.id','=','user_sales_order.location_id')
                        ->join('location_6','location_6.id','=','location_7.location_6_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->where('user_sales_order.call_status','=','1')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                        ->where('location_4.status','=','1')
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('location_4.id')
                        ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalProductiveCall"),'location_4.id');


        $sumOfSales = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('location_7','location_7.id','=','user_sales_order.location_id')
                        ->join('location_6','location_6.id','=','location_7.location_6_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->where('user_sales_order.call_status','=','1')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                        ->where('location_4.status','=','1')
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('location_4.id')
                        ->pluck(DB::raw("ROUND(SUM((user_sales_order_details.rate*user_sales_order_details.quantity)+(user_sales_order_details.case_rate*user_sales_order_details.case_qty)),2) as totalSales"),'location_4.id');


        $sumOfRetailingEmployee = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('user_daily_attendance','user_daily_attendance.user_id','=','person.id')
                                ->join('location_6','location_6.id','=','person.town_id')
                                ->join('location_5','location_5.id','=','location_6.location_5_id')
                                ->join('location_4','location_4.id','=','location_5.location_4_id')
                                ->where('person_status','=','1')
                                ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date, '%Y-%m-%d') = '$yesterday'")
                                ->where('location_4.status','=','1')
                                ->whereIn('user_daily_attendance.company_id',$company_id)
                                ->groupBy('location_4.id')
                                ->pluck(DB::raw("COUNT(user_id) as totalRetailingEmployee"),'location_4.id');



        $countOfTotalEmployee = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('location_6','location_6.id','=','person.town_id')
                                ->join('location_5','location_5.id','=','location_6.location_5_id')
                                ->join('location_4','location_4.id','=','location_5.location_4_id')
                                ->where('person_status','=','1')
                                ->where('location_4.status','=','1')
                                ->whereIn('person.company_id',$company_id)
                                ->groupBy('location_4.id')
                                ->pluck(DB::raw("COUNT(person.id) as totalEmployee"),'location_4.id');


        $managerEmployee = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('location_6','location_6.id','=','person.town_id')
                                ->join('location_5','location_5.id','=','location_6.location_5_id')
                                ->join('location_4','location_4.id','=','location_5.location_4_id')
                                ->where('person_status','=','1')
                                ->where('location_4.status','=','1')
                                ->whereIn('person.role_id',$role_id)
                                ->whereIn('person.company_id',$company_id)
                                ->groupBy('location_4.id')
                                ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'location_4.id');



      
        $search_all_user = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.email')
                        ->where('person_status',1)
                        ->whereIn('person.company_id',$company_id)
                        ->whereIn('person.id',$person_id)
                        ->groupBy('person.id')
                        ->get();


         foreach ($search_all_user as $saukey => $sauvalue) {

                $manager_name = $sauvalue->user_name;
                $manager_mail = $sauvalue->email;
                // $manager_mail = "pooja@manacleindia.com";

                            $mail = Mail::send('reports/sendMails/sendDailySalesReportMail', array(
                                                    'sumOfNewRetailer' => $sumOfNewRetailer,
                                                    'sumOfTotalRetailer' => $sumOfTotalRetailer,
                                                    'sumOfTotalCall'=>$sumOfTotalCall,
                                                    'sumOfProductiveCall'=>$sumOfProductiveCall,
                                                    'sumOfSales'=>$sumOfSales,
                                                    'sumOfRetailingEmployee'=>$sumOfRetailingEmployee,
                                                    'countOfTotalEmployee'=>$countOfTotalEmployee,
                                                    'managerEmployee'=>$managerEmployee,
                                                    'locationFour'=>$locationFour,
                                                    'yesterday'=>$yesterday,

                                    ) , function($message) use($manager_mail,$subject)
                            {
                                    
                                $message->from('manacle.php1@gmail.com');

                                // $cc_mail = "hr3@btwindia.com";
                                // $cc_mail = "rohit.k@manacleindia.com";


                                // $message->to($manager_mail)->cc($cc_mail)->subject($subject);
                                $message->to($manager_mail)->subject($subject);

                            });

                            // dd($junior_person_details);
                    

            }               

    } 



     public function btwManagerWiseSale(Request $request)
    {

        $subject = 'Daily Sale Report';
        $company_id = array("43");
        // $person_id = array("2092","3664");
        // $person_id = array("3040");
        $person_role_id = array("148","146","145","155","154","200","301");  
        $role_id = array("148","146","145","155","154");        

        $current_date = date('Y-m-d');
        $yesterday = date('Y-m-d',strtotime("-1 days"));
        // $yesterday = date('2020-12-26');


        $locationFour = DB::table('location_4')
                        ->where('status',1)
                        ->whereIn('company_id',$company_id)
                        ->groupBy('id')
                        ->pluck('name','id');

        $todayTask = DB::table('user_daily_attendance')
                    ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                    ->whereIn('user_daily_attendance.company_id',$company_id)
                    ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date, '%Y-%m-%d') = '$yesterday'")
                    ->groupBy('user_id')
                    ->pluck('_working_status.name','user_id');


        $sumOfNewRetailer = DB::table('retailer')
                    ->whereIn('retailer.company_id',$company_id)
                    ->whereRaw("DATE_FORMAT(retailer.created_on, '%Y-%m-%d') = '$yesterday'")
                    ->where('retailer_status',1)
                    ->groupBy('created_by_person_id')
                    ->pluck(DB::raw("COUNT(retailer.id) as retailerCount"),'created_by_person_id');

        $sumOfTotalCall =  DB::table('user_sales_order')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('user_id')
                        ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalCall"),'user_id'); 


        $sumOfProductiveCall =  DB::table('user_sales_order')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->where('call_status','=','1')
                        ->groupBy('user_id')
                        ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalCall"),'user_id'); 

        $sumOfSaleAmount =  DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->where('call_status','=','1')
                        ->groupBy('user_id')
                        ->pluck(DB::raw("ROUND(SUM((user_sales_order_details.rate*user_sales_order_details.quantity)+(user_sales_order_details.case_rate*user_sales_order_details.case_qty)),2) as totalSales"),'user_id'); 


        $retailingEmployee = DB::table('user_daily_attendance')
                    ->whereIn('user_daily_attendance.company_id',$company_id)
                    ->where('work_status','=','89')
                    ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date, '%Y-%m-%d') = '$yesterday'")
                    ->groupBy('user_id')
                    ->pluck('user_daily_attendance.work_status','user_id');

        $totalRetailer = DB::table('dealer_location_rate_list')
                        ->join('retailer','retailer.location_id','=','dealer_location_rate_list.location_id')
                        ->where('dealer_location_rate_list.company_id',$company_id)
                        ->where('retailer.company_id',$company_id)
                        ->where('retailer.retailer_status','=','1')
                        ->groupBy('user_id')
                        ->pluck(DB::raw("COUNT(DISTINCT retailer.id) as totalRetailer"),'user_id');



                        // dd($totalRetailer);




         $managerEmployee = DB::table('person')
                ->join('person_login','person_login.person_id','=','person.id')
                ->join('location_6','location_6.id','=','person.town_id')
                ->join('location_5','location_5.id','=','location_6.location_5_id')
                ->join('location_4','location_4.id','=','location_5.location_4_id')
                ->select('person.id as managerId',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as managerName"),'location_4.id as managerLocationFour')
                ->where('person_status','=','1')
                ->where('location_4.status','=','1')
                ->whereIn('person.role_id',$role_id)
                ->whereIn('person.company_id',$company_id)
                ->groupBy('location_4.id')
                ->get();



        $finalOut = array();                
        foreach ($managerEmployee as $managerEmployeeKey => $managerEmployeeValue) {

                if($managerEmployeeValue->managerId != '0')
                    {
                        Session::forget('juniordata');
                        $juniors_array=self::getJuniorUser($managerEmployeeValue->managerId);
                        Session::push('juniordata', $managerEmployeeValue->managerId);
                        // Session::push('juniordata', $managerEmployeeValue->managerId);
                        $juniors_array = $request->session()->get('juniordata');
                        if(empty($juniors_array))
                        {
                            $juniors_array = array();
                        }
                    }

                    // dd($juniors_array);



            
            $location4 = $managerEmployeeValue->managerLocationFour;

            $out['managerId'] = $managerEmployeeValue->managerId; 
            $out['managerName'] = $managerEmployeeValue->managerName; 
            $out['juniorCount'] = COUNT($juniors_array); 
                    $juniorDetails =  DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('_role','_role.role_id','=','person.role_id')
                                ->join('location_5','location_5.id','=','person.head_quater_id')
                                ->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'rolename','location_5.name as head_quarter_name')
                                ->whereIn('person.company_id',$company_id)
                                ->whereIn('location_5.company_id',$company_id)
                                ->whereIn('person.id',$juniors_array)
                                ->where('person_login.person_status','=','1')
                                ->groupBy('person.id')
                                ->get()
                                ->toArray();
                    $finalJunior = array();
                    foreach ($juniorDetails as $juniorDetailskey => $juniorDetailsvalue) {



                        $juniorOut['user_id'] = $juniorDetailsvalue->user_id;
                        $juniorOut['user_name'] = $juniorDetailsvalue->user_name;
                        $juniorOut['rolename'] = $juniorDetailsvalue->rolename;
                        $juniorOut['head_quarter_name'] = $juniorDetailsvalue->head_quarter_name;
                        $juniorOut['today_task'] = !empty($todayTask[$juniorDetailsvalue->user_id])?$todayTask[$juniorDetailsvalue->user_id]:'N/A';
                        $juniorOut['totalRetailer'] = !empty($totalRetailer[$juniorDetailsvalue->user_id])?$totalRetailer[$juniorDetailsvalue->user_id]:'0';

                        $juniorOut['newRetailer'] = !empty($sumOfNewRetailer[$juniorDetailsvalue->user_id])?$sumOfNewRetailer[$juniorDetailsvalue->user_id]:'0';
                        $juniorOut['totalCall'] = !empty($sumOfTotalCall[$juniorDetailsvalue->user_id])?$sumOfTotalCall[$juniorDetailsvalue->user_id]:'0';
                        $juniorOut['productiveCall'] = !empty($sumOfProductiveCall[$juniorDetailsvalue->user_id])?$sumOfProductiveCall[$juniorDetailsvalue->user_id]:'0';
                        $juniorOut['saleAmount'] = !empty($sumOfSaleAmount[$juniorDetailsvalue->user_id])?$sumOfSaleAmount[$juniorDetailsvalue->user_id]:'0';
                        $juniorOut['retailingEmployee'] = !empty($retailingEmployee[$juniorDetailsvalue->user_id])?'1':'0';
                        $juniorOut['totalManPower'] = '1';

                        $finalJunior[] = $juniorOut;
                                }   
            $out['juniorDetails'] = $finalJunior; 

          

            $finalOut[$location4][] = $out;
        }

        // dd($finalOut);


  



      
        $search_all_user = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.email')
                        ->where('person_status',1)
                        ->whereIn('person.company_id',$company_id)
                        // ->whereIn('person.id',$person_id)
                        ->whereIn('person.role_id',$person_role_id)
                        ->groupBy('person.id')
                        ->get();


         foreach ($search_all_user as $saukey => $sauvalue) {

                $manager_name = $sauvalue->user_name;
                $manager_mail = $sauvalue->email;
                // $manager_mail = "pooja@manacleindia.com";

                            $mail = Mail::send('reports/sendMails/sendDailyManagerSale', array(
                                                    'finalOut'=>$finalOut,
                                                    'locationFour'=>$locationFour,
                                                    'yesterday'=>$yesterday,

                                    ) , function($message) use($manager_mail,$subject)
                            {
                                    
                                $message->from('manacle.php1@gmail.com');

                                // $cc_mail = "hr3@btwindia.com";
                                // $cc_mail = "rohit.k@manacleindia.com";


                                // $message->to($manager_mail)->cc($cc_mail)->subject($subject);
                                $message->to($manager_mail)->subject($subject);

                            });

                            // dd($junior_person_details);
                    

            }               

    } 



    public function btwSubStateDSR(Request $request)
    {

        $subject = 'Daily Sale Report';
        $company_id = array("43");
        $person_id = array("2092","3664","3040");
        // $person_id = array("3040");
        $role_id = array("148","145","149","167","168");        

        $current_date = date('Y-m-d');
        // $startDate = date('Y-m-01');
        $yesterday = date('Y-m-d',strtotime("-1 days"));
        // $yesterday = date('2021-01-31');

        $startDate = date('Y-m-01', strtotime($yesterday));

        $location_5_id = ['356','354','355','357','358','7517','7518','7519','7520','7521','7523','8061','366','6848','365','6383','5481','8063','6310','364','6402','405'];

        // $locationFive = DB::table('location_5')
        //                 ->where('status',1)
        //                 ->whereIn('company_id',$company_id)
        //                 ->whereIn('id',$location_5_id)
        //                 ->groupBy('id')
        //                 ->pluck('name','id');


                        // $locationFive = DB::table('location_5')
                        //         ->join('person','person.head_quater_id','=','location_5.id')
                        //         ->join('person_login','person_login.person_id','=','person.id')
                        //         ->where('person_login.person_status',1)
                        //            ->whereIn('location_5.company_id',$company_id)
                        //            ->whereIn('person.company_id',$company_id)
                        //             ->groupBy('location_5.id')
                        //             ->pluck('location_5.name','location_5.id');
                    // dd($locationFive);



        $locationFive = DB::table('person') 
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                    ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
                    ->join('location_6','location_6.id','=','location_7.location_6_id')
                    ->join('location_5','location_5.id','=','location_6.location_5_id')
                    ->select('location_5.id','location_5.name','person.first_name',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"))
                    ->where('person_status','=','1')
                    ->whereIn('person.role_id',$role_id)        
                    ->whereIn('location_5.id',$location_5_id)
                    ->groupBy('location_5.id')
                    ->orderBy('person.first_name','ASC')
                    ->get();
                    // ->pluck('location_5.name','location_5.id');

                    // dd($locationFive);


      



        $sumOfComTotalCall = DB::table('user_sales_order')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->join('location_5','location_5.id','=','person.head_quater_id')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$yesterday'")
                        ->where('location_5.status','=','1')
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('location_5.id')
                        ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id,date) as totalCall"),'location_5.id');
                        // count distinct id of user sales order by performance

        $sumOfComProductiveCall = DB::table('user_sales_order')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->join('location_5','location_5.id','=','person.head_quater_id')
                        ->where('user_sales_order.call_status','=','1')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$yesterday'")
                        ->where('location_5.status','=','1')
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('location_5.id')
                        ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id,date) as totalProductiveCall"),'location_5.id');


        $sumOfComSales = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->join('location_5','location_5.id','=','person.head_quater_id')
                        ->where('user_sales_order.call_status','=','1')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$yesterday'")
                        ->where('location_5.status','=','1')
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('location_5.id')
                        ->pluck(DB::raw("ROUND(SUM((user_sales_order_details.rate*user_sales_order_details.quantity)+(user_sales_order_details.case_rate*user_sales_order_details.case_qty)),2) as totalSales"),'location_5.id');


        $sumOfComNewProductiveRetailer = DB::table('retailer')
                            ->join('user_sales_order','user_sales_order.retailer_id','=','retailer.id')
                            ->join('person','person.id','=','retailer.created_by_person_id')
                            ->join('location_5','location_5.id','=','person.head_quater_id')
                            ->where('retailer_status','=','1')
                            ->where('call_status','=','1')
                            ->where('location_5.status','=','1')
                            ->whereIn('retailer.company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(retailer.created_on, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(retailer.created_on, '%Y-%m-%d') <= '$yesterday'")
                            ->groupBy('location_5.id')
                            ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as newProductiveRetailer"),'location_5.id');



        // $location5 = DB::table('person') 
        //             ->join('person_login','person_login.person_id','=','person.id')
        //             ->join('_role','_role.role_id','=','person.role_id')
        //             ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
        //             ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
        //             ->join('location_6','location_6.id','=','location_7.location_6_id')
        //             ->join('location_5','location_5.id','=','location_6.location_5_id')
        //             ->where('person_status','=','1')
        //             ->whereIn('person.role_id',$role_id)               
        //             ->groupBy('person.id','location_5.id')
        //             ->pluck(DB::raw("CONCAT(person.first_name,location_5.name) as concat"));


         // $location5 = DB::table('person') 
         //            ->join('person_login','person_login.person_id','=','person.id')
         //            ->join('_role','_role.role_id','=','person.role_id')
         //            ->join('location_5','location_5.id','=','person.head_quater_id')
         //            ->where('person_status','=','1')
         //            ->whereIn('person.role_id',$role_id)               
         //            ->groupBy('person.id','location_5.id')
         //            ->pluck(DB::raw("CONCAT(person.first_name,location_5.name) as concat"));

                    // dd($location5);


        // $sumOfNewRetailer = DB::table('retailer')
        //                     ->join('person','person.id','=','retailer.created_by_person_id')
        //                     ->join('location_5','location_5.id','=','person.head_quater_id')
        //                     ->where('retailer_status','=','1')
        //                     ->where('location_5.status','=','1')
        //                     ->whereIn('retailer.company_id',$company_id)
        //                     ->whereRaw("DATE_FORMAT(retailer.created_on, '%Y-%m-%d') = '$yesterday'")
        //                     ->groupBy('location_5.id')
        //                     ->pluck(DB::raw("COUNT(retailer.id) as newRetailer"),'location_5.id');



        // $sumOfNewRetailer = DB::table('user_sales_order')
        //                     ->join('location_7','location_7.id','=','user_sales_order.location_id')
        //                     ->join('location_6','location_6.id','=','location_7.location_6_id')
        //                     ->join('location_5','location_5.id','=','location_6.location_5_id')
        //                     ->join('retailer','retailer.location_id','=','location_7.id')

        //                     // ->join('person','person.id','=','retailer.created_by_person_id')
        //                     // ->join('location_5','location_5.id','=','person.head_quater_id')
        //                     // ->where('retailer_status','=','1')
        //                     ->where('call_status','=','1')
        //                     ->where('location_5.status','=','1')
        //                     ->whereIn('user_sales_order.company_id',$company_id)
        //                     ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$yesterday'")
        //                     ->groupBy('location_5.id')
        //                     ->pluck(DB::raw("COUNT(DISTINCT retailer.id) as newProductiveRetailer"),'location_5.id');





        // $sumOfNewRetailer = DB::table('person')
        //                         ->join('person_login','person_login.person_id','=','person.id')
        //                         ->join('user_daily_attendance','user_daily_attendance.user_id','=','person.id')
        //                         ->join('location_5','location_5.id','=','person.head_quater_id')
        //                         ->join('user_sales_order','user_sales_order.user_id','=','person.id')
        //                         ->join('retailer','retailer.location_id','=','user_sales_order.location_id')
        //                         ->where('person_status','=','1')
        //                         ->where('user_sales_order.call_status','=','1')
        //                         ->where('work_status',89)
        //                         ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date, '%Y-%m-%d') = '$yesterday'")
        //                         ->where('location_5.status','=','1')
        //                         ->whereIn('user_daily_attendance.company_id',$company_id)
        //                         ->groupBy('location_5.id')
        //                         ->pluck(DB::raw("COUNT(DISTINCT retailer.id) as newProductiveRetailer"),'location_5.id');



        $sumOfNewRetailerQuery = DB::table('user_sales_order')
                                ->join('person','person.id','=','user_sales_order.user_id')
                                ->join('user_daily_attendance','user_daily_attendance.user_id','=','person.id')
                                ->join('location_5','location_5.id','=','person.head_quater_id')
                                ->where('user_sales_order.call_status','=','1')
                                ->where('work_status',89)
                                ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                                ->where('location_5.status','=','1')
                                ->whereIn('user_daily_attendance.company_id',$company_id)
                                ->whereIn('user_sales_order.company_id',$company_id)
                                ->groupBy('location_5.id')
                                ->pluck(DB::raw("GROUP_CONCAT(DISTINCT location_id) as newProductiveRetailer"),'location_5.id');

        $sumOfNewRetailer = array();
        $sumOfTotalCall = array();
        foreach ($sumOfNewRetailerQuery as $sonkey => $sonvalue) {
            
            $explode_location = explode(',',$sonvalue);

            // dd($explode_location);

            $retailer_count = DB::table('user_sales_order')
                            ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                            ->whereIn('retailer.location_id',$explode_location)
                            ->whereIn('retailer.company_id',$company_id)
                            ->where('call_status','=','1')
                            ->select(DB::raw('COUNT(DISTINCT retailer.id) as count_retailer'))
                            ->first();


            $sumOfNewRetailer[$sonkey] = !empty($retailer_count->count_retailer)?$retailer_count->count_retailer:'0';

            // $total_retailer_count = DB::table('retailer')
            //                     ->whereIn('location_id',$explode_location)
            //                     ->whereIn('company_id',$company_id)
            //                     ->select(DB::raw('COUNT(retailer.id) as count_retailer'))
            //                     ->first();

            $total_retailer_count =  DB::table('user_sales_order')
                                ->whereIn('location_id',$explode_location)
                                ->whereIn('company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                                ->select(DB::raw('COUNT(DISTINCT retailer_id) as count_retailer'))
                                ->first();

            $sumOfTotalCall[$sonkey] = !empty($total_retailer_count->count_retailer)?$total_retailer_count->count_retailer:'0';





        }






        $sumOfNewProductiveRetailer = DB::table('retailer')
                            ->join('user_sales_order','user_sales_order.retailer_id','=','retailer.id')
                            // ->join('location_7','location_7.id','=','retailer.location_id')
                            // ->join('location_6','location_6.id','=','location_7.location_6_id')
                            // ->join('location_5','location_5.id','=','location_6.location_5_id')
                            ->join('person','person.id','=','retailer.created_by_person_id')
                            ->join('location_5','location_5.id','=','person.head_quater_id')
                            ->where('retailer_status','=','1')
                            ->where('call_status','=','1')
                            ->where('location_5.status','=','1')
                            ->whereIn('retailer.company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(retailer.created_on, '%Y-%m-%d') = '$yesterday'")
                            ->groupBy('location_5.id')
                            ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as newProductiveRetailer"),'location_5.id');




        $sumOfTotalRetailer = DB::table('retailer')
                            ->join('person','person.id','=','retailer.created_by_person_id')
                            ->join('location_5','location_5.id','=','person.head_quater_id')
                            ->where('retailer_status','=','1')
                            ->where('location_5.status','=','1')
                            ->whereIn('retailer.company_id',$company_id)
                            ->groupBy('location_5.id')
                            ->pluck(DB::raw("COUNT(retailer.id) as totalRetailer"),'location_5.id');

        // $sumOfTotalCall = DB::table('user_sales_order')
        //                 ->join('person','person.id','=','user_sales_order.user_id')
        //                 ->join('location_5','location_5.id','=','person.head_quater_id')
        //                 ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
        //                 ->where('location_5.status','=','1')
        //                 ->whereIn('user_sales_order.company_id',$company_id)
        //                 ->groupBy('location_5.id')
        //                 ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalCall"),'location_5.id');


        $sumOfProductiveCall = DB::table('user_sales_order')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->join('location_5','location_5.id','=','person.head_quater_id')
                        ->where('user_sales_order.call_status','=','1')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                        ->where('location_5.status','=','1')
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('location_5.id')
                        ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalProductiveCall"),'location_5.id');


        $sumOfSales = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->join('location_5','location_5.id','=','person.head_quater_id')
                        ->where('user_sales_order.call_status','=','1')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                        ->where('location_5.status','=','1')
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('location_5.id')
                        ->pluck(DB::raw("ROUND(SUM((user_sales_order_details.rate*user_sales_order_details.quantity)+(user_sales_order_details.case_rate*user_sales_order_details.case_qty)),2) as totalSales"),'location_5.id');


        $sumOfRetailingEmployee = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('user_daily_attendance','user_daily_attendance.user_id','=','person.id')
                                ->join('location_5','location_5.id','=','person.head_quater_id')
                                ->where('person_status','=','1')
                                ->where('work_status',89)
                                ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date, '%Y-%m-%d') = '$yesterday'")
                                ->where('location_5.status','=','1')
                                ->whereIn('user_daily_attendance.company_id',$company_id)
                                ->whereIn('person.role_id',['144','171','172','173','150','151'])
                                ->groupBy('location_5.id')
                                ->pluck(DB::raw("COUNT(user_id) as totalRetailingEmployee"),'location_5.id');



        $countOfTotalEmployee = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('location_5','location_5.id','=','person.head_quater_id')
                                ->where('person_status','=','1')
                                ->where('location_5.status','=','1')
                                ->whereIn('person.company_id',$company_id)
                                ->whereIn('person.role_id',['144','171','172','173','150','151'])
                                ->groupBy('location_5.id')
                                ->pluck(DB::raw("COUNT(person.id) as totalEmployee"),'location_5.id');


        // $managerEmployee = DB::table('person')
        //                         ->join('person_login','person_login.person_id','=','person.id')
        //                         ->join('location_5','location_5.id','=','person.head_quater_id')
        //                         ->where('person_status','=','1')
        //                         ->where('location_5.status','=','1')
        //                         ->whereIn('person.role_id',$role_id)
        //                         ->whereIn('person.company_id',$company_id)
        //                         ->groupBy('location_5.id')
        //                         ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'location_5.id');


        $managerEmployee = DB::table('person') 
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                    ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
                    ->join('location_6','location_6.id','=','location_7.location_6_id')
                    ->join('location_5','location_5.id','=','location_6.location_5_id')
                    ->where('person_status','=','1')
                    ->whereIn('person.role_id',$role_id)               
                    ->groupBy('person.id','location_5.id')
                    ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'location_5.id');


                    // dd($managerEmployee);



      
        $search_all_user = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.email')
                        ->where('person_status',1)
                        ->whereIn('person.company_id',$company_id)
                        ->whereIn('person.id',$person_id)
                        ->groupBy('person.id')
                        ->get();

                    // dd($search_all_user);



         foreach ($search_all_user as $saukey => $sauvalue) {

                $manager_name = $sauvalue->user_name;
                $manager_mail = $sauvalue->email;

                // $manager_mail = "pooja@manacleindia.com";

                            $mail = Mail::send('reports/sendMails/btwSubStateDSR', array(
                                                    'sumOfNewRetailer' => $sumOfNewRetailer,
                                                    'sumOfNewProductiveRetailer' => $sumOfNewProductiveRetailer,
                                                    'sumOfTotalRetailer' => $sumOfTotalRetailer,
                                                    'sumOfTotalCall'=>$sumOfTotalCall,
                                                    'sumOfProductiveCall'=>$sumOfProductiveCall,
                                                    'sumOfSales'=>$sumOfSales,
                                                    'sumOfRetailingEmployee'=>$sumOfRetailingEmployee,
                                                    'countOfTotalEmployee'=>$countOfTotalEmployee,
                                                    'managerEmployee'=>$managerEmployee,
                                                    'locationFive'=>$locationFive,
                                                    'yesterday'=>$yesterday,
                                                    'sumOfComTotalCall'=>$sumOfComTotalCall,
                                                    'sumOfComProductiveCall'=>$sumOfComProductiveCall,
                                                    'sumOfComSales'=>$sumOfComSales,
                                                    'startDate'=>$startDate,
                                                    'sumOfComNewProductiveRetailer'=>$sumOfComNewProductiveRetailer,

                                    ) , function($message) use($manager_mail,$subject)
                            {
                                    
                                $message->from('manacle.php1@gmail.com');

                                // $cc_mail = "hr3@btwindia.com";
                                // $cc_mail = "rohit.k@manacleindia.com";


                                // $message->to($manager_mail)->cc($cc_mail)->subject($subject);
                                $message->to($manager_mail)->subject($subject);

                            });

                            // dd($junior_person_details);
                    

            }               

    } 


     public function btwManagerDailyReporting(Request $request)
    {

        $subject = 'Managers Daily Report';
        $company_id = array("43");
        $person_id = array("2092","3664","3040");
        // $person_id = array("3040");
        $role_id = array("148","145","168");        

        $current_date = date('Y-m-d');
        $yesterday = date('Y-m-d',strtotime("-1 days"));


        $managerDailyReporting = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('daily_reporting','daily_reporting.user_id','=','person.id')
                                ->select(DB::raw("COUNT(DISTINCT order_id) as count_order"),DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'daily_reporting.work_status','daily_reporting.remarks','daily_reporting.attn_address',DB::raw("DATE_FORMAT(daily_reporting.work_date, '%H:%i:%s') as time"),'person.id as user_id','primary_target','secondary_target')
                                ->whereRaw("DATE_FORMAT(daily_reporting.work_date, '%Y-%m-%d') = '$yesterday'")
                                ->where('person_status','=','1')
                                ->whereIn('person.role_id',$role_id)
                                ->whereIn('person.company_id',$company_id)
                                ->groupBy('person.id','daily_reporting.order_id')
                                ->orderBy('user_name','ASC')
                                ->orderBy('time','ASC')
                                ->get();




        // $managerEmployee = DB::table('person')
        //                         ->join('person_login','person_login.person_id','=','person.id')
        //                         ->join('daily_reporting','daily_reporting.user_id','=','person.id')
        //                         // ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id as user_id',DB::raw("COUNT(DISTINCT order_id) as count_order"))
        //                         ->whereRaw("DATE_FORMAT(daily_reporting.work_date, '%Y-%m-%d') = '$yesterday'")
        //                         ->where('person_status','=','1')
        //                         ->whereIn('person.role_id',$role_id)
        //                         ->whereIn('person.company_id',$company_id)
        //                         ->groupBy('person.id')
        //                         ->pluck(DB::raw("COUNT(DISTINCT order_id) as count_order"),'person.id');

                                // dd($managerEmployee);

      
        $search_all_user = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.email')
                        ->where('person_status',1)
                        ->whereIn('person.company_id',$company_id)
                        ->whereIn('person.id',$person_id)
                        ->groupBy('person.id')
                        ->get();

                    // dd($search_all_user);



         foreach ($search_all_user as $saukey => $sauvalue) {

                $manager_name = $sauvalue->user_name;
                $manager_mail = $sauvalue->email;

                // $manager_mail = "pooja@manacleindia.com";

                            $mail = Mail::send('reports/sendMails/btwManagerDailyReporting', array(
                                                   
                                                    // 'managerEmployee'=>$managerEmployee,
                                                    'managerDailyReporting'=>$managerDailyReporting,
                                                    'yesterday'=>$yesterday,
                                                 

                                    ) , function($message) use($manager_mail,$subject)
                            {
                                    
                                $message->from('manacle.php1@gmail.com');

                                // $cc_mail = "hr3@btwindia.com";
                                // $cc_mail = "rohit.k@manacleindia.com";


                                // $message->to($manager_mail)->cc($cc_mail)->subject($subject);
                                $message->to($manager_mail)->subject($subject);

                            });

                            // dd($junior_person_details);
                    

            }               

    } 


    ////////////////////////////////////////////////////// btw managers user daily reporting starts //////////////////////////////////////////////////


     public function btwManagerUserDailyReport(Request $request)
    {

        $subject = 'Manager Users Daily Sale Report';
        $company_id = array("43");
        // $person_id = array("2092","3664");
        $person_id = array("3040");
        $role_id = array("148","145","168");        

        $current_date = date('Y-m-d');
        // $startDate = date('Y-m-01');
        $yesterday = date('Y-m-d',strtotime("-1 days"));

        $startDate = date('Y-m-01', strtotime($yesterday));



        $todayTask = DB::table('user_daily_attendance')
                    ->join('person','person.id','=','user_daily_attendance.user_id')
                    ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                    ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date, '%Y-%m-%d') = '$yesterday'")
                    ->whereIn('user_daily_attendance.company_id',$company_id)
                    ->groupBy('user_daily_attendance.user_id')
                    ->pluck('_working_status.name','person.id');

        $beatName =  DB::table('user_sales_order')
                ->join('person','person.id','=','user_sales_order.user_id')
                ->join('location_7','location_7.id','=','user_sales_order.location_id')
                ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                ->whereIn('user_sales_order.company_id',$company_id)
                ->groupBy('person.id')
                ->pluck(DB::raw("GROUP_CONCAT(DISTINCT location_7.name) as beats"),'person.id');


        $beatNumber =  DB::table('user_sales_order')
                ->join('person','person.id','=','user_sales_order.user_id')
                ->join('location_7','location_7.id','=','user_sales_order.location_id')
                ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                ->whereIn('user_sales_order.company_id',$company_id)
                ->groupBy('person.id')
                ->pluck(DB::raw("GROUP_CONCAT(DISTINCT location_7.beat_no) as beats"),'person.id');



        $sumOfComTotalCall = DB::table('user_sales_order')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$yesterday'")
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('person.id')
                        ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalCall"),'person.id');
                        // count distinct id of user sales order by performance

        $sumOfComProductiveCall = DB::table('user_sales_order')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->where('user_sales_order.call_status','=','1')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$yesterday'")
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('person.id')
                        ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalProductiveCall"),'person.id');


        $sumOfComSales = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->where('user_sales_order.call_status','=','1')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$yesterday'")
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('person.id')
                        ->pluck(DB::raw("ROUND(SUM((user_sales_order_details.rate*user_sales_order_details.quantity)+(user_sales_order_details.case_rate*user_sales_order_details.case_qty)),2) as totalSales"),'person.id');


        $sumOfComNewProductiveRetailer = DB::table('retailer')
                            ->join('user_sales_order','user_sales_order.retailer_id','=','retailer.id')
                            ->join('person','person.id','=','retailer.created_by_person_id')
                            ->where('retailer_status','=','1')
                            ->where('call_status','=','1')
                            ->whereIn('retailer.company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(retailer.created_on, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(retailer.created_on, '%Y-%m-%d') <= '$yesterday'")
                            ->groupBy('person.id')
                            ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as newProductiveRetailer"),'person.id');




                            ############################################################################################################
                        $sumOfNewRetailerQuery = DB::table('user_sales_order')
                            ->join('person','person.id','=','user_sales_order.user_id')
                            ->join('user_daily_attendance','user_daily_attendance.user_id','=','person.id')
                            // ->join('location_5','location_5.id','=','person.head_quater_id')
                            ->where('user_sales_order.call_status','=','1')
                            ->where('work_status',89)
                            ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                            // ->where('location_5.status','=','1')
                            ->whereIn('user_daily_attendance.company_id',$company_id)
                            ->whereIn('user_sales_order.company_id',$company_id)
                            ->groupBy('person.id')
                            ->pluck(DB::raw("GROUP_CONCAT(DISTINCT location_id) as newProductiveRetailer"),'person.id');

                        $sumOfNewRetailer = array();
                        $sumOfTotalCall = array();
                        foreach ($sumOfNewRetailerQuery as $sonkey => $sonvalue) {
                            
                            $explode_location = explode(',',$sonvalue);

                            // dd($explode_location);

                            $retailer_count = DB::table('user_sales_order')
                                            ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                                            ->whereIn('retailer.location_id',$explode_location)
                                            ->whereIn('retailer.company_id',$company_id)
                                            ->where('call_status','=','1')
                                            ->select(DB::raw('COUNT(DISTINCT retailer.id) as count_retailer'))
                                            ->first();


                            $sumOfNewRetailer[$sonkey] = !empty($retailer_count->count_retailer)?$retailer_count->count_retailer:'0';

                            // $total_retailer_count = DB::table('retailer')
                            //                     ->whereIn('location_id',$explode_location)
                            //                     ->whereIn('company_id',$company_id)
                            //                     ->select(DB::raw('COUNT(retailer.id) as count_retailer'))
                            //                     ->first();

                            $total_retailer_count =  DB::table('user_sales_order')
                                                ->whereIn('location_id',$explode_location)
                                                ->whereIn('company_id',$company_id)
                                                ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                                                ->select(DB::raw('COUNT(DISTINCT retailer_id) as count_retailer'))
                                                ->first();

                            $sumOfTotalCall[$sonkey] = !empty($total_retailer_count->count_retailer)?$total_retailer_count->count_retailer:'0';





                        }
                            ############################################################################################################


        // $sumOfNewRetailer = DB::table('retailer')
        //                     ->join('person','person.id','=','retailer.created_by_person_id')
        //                     ->where('retailer_status','=','1')
        //                     ->whereIn('retailer.company_id',$company_id)
        //                     ->whereRaw("DATE_FORMAT(retailer.created_on, '%Y-%m-%d') = '$yesterday'")
        //                     ->groupBy('person.id')
        //                     ->pluck(DB::raw("COUNT(retailer.id) as newRetailer"),'person.id');


        // $sumOfNewProductiveRetailer = DB::table('retailer')
        //                     ->join('user_sales_order','user_sales_order.retailer_id','=','retailer.id')
        //                     ->join('person','person.id','=','retailer.created_by_person_id')
        //                     ->where('retailer_status','=','1')
        //                     ->where('call_status','=','1')
        //                     ->whereIn('retailer.company_id',$company_id)
        //                     ->whereRaw("DATE_FORMAT(retailer.created_on, '%Y-%m-%d') = '$yesterday'")
        //                     ->groupBy('person.id')
        //                     ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as newProductiveRetailer"),'person.id');



        $sumOfNewProductiveRetailer = DB::table('retailer')
                            ->join('user_sales_order','user_sales_order.retailer_id','=','retailer.id')
                            // ->join('location_7','location_7.id','=','retailer.location_id')
                            // ->join('location_6','location_6.id','=','location_7.location_6_id')
                            // ->join('location_5','location_5.id','=','location_6.location_5_id')
                            ->join('person','person.id','=','retailer.created_by_person_id')
                            // ->join('location_5','location_5.id','=','person.head_quater_id')
                            ->where('retailer_status','=','1')
                            ->where('call_status','=','1')
                            // ->where('location_5.status','=','1')
                            ->whereIn('retailer.company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(retailer.created_on, '%Y-%m-%d') = '$yesterday'")
                            ->groupBy('person.id')
                            ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as newProductiveRetailer"),'person.id');



        $sumOfTotalRetailer = DB::table('retailer')
                            ->join('person','person.id','=','retailer.created_by_person_id')
                            ->where('retailer_status','=','1')
                            ->whereIn('retailer.company_id',$company_id)
                            ->groupBy('person.id')
                            ->pluck(DB::raw("COUNT(retailer.id) as totalRetailer"),'person.id');

        // $sumOfTotalCall = DB::table('user_sales_order')
        //                 ->join('person','person.id','=','user_sales_order.user_id')
        //                 ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
        //                 ->whereIn('user_sales_order.company_id',$company_id)
        //                 ->groupBy('person.id')
        //                 ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalCall"),'person.id');

        $sumOfProductiveCall = DB::table('user_sales_order')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->where('user_sales_order.call_status','=','1')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('person.id')
                        ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalProductiveCall"),'person.id');

        $sumOfSales = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->where('user_sales_order.call_status','=','1')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('person.id')
                        ->pluck(DB::raw("ROUND(SUM((user_sales_order_details.rate*user_sales_order_details.quantity)+(user_sales_order_details.case_rate*user_sales_order_details.case_qty)),2) as totalSales"),'person.id');

        $sumOfRetailingEmployee = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('user_daily_attendance','user_daily_attendance.user_id','=','person.id')
                                ->where('person_status','=','1')
                                ->where('work_status',89)
                                ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date, '%Y-%m-%d') = '$yesterday'")
                                ->whereIn('user_daily_attendance.company_id',$company_id)
                                ->groupBy('person.id')
                                ->pluck(DB::raw("COUNT(user_id) as totalRetailingEmployee"),'person.id');

        $countOfTotalEmployee = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->where('person_status','=','1')
                                ->whereIn('person.company_id',$company_id)
                                ->whereIn('person.role_id',['144','171','172','173','150','151'])
                                ->groupBy('person.id')
                                ->pluck(DB::raw("COUNT(person.id) as totalEmployee"),'person.id');


    
        ///////////////////////////////////// mails send to all asm/rsm/dgm sales starts //////////////////////////////////////


        // $managerEmployee = DB::table('person') 
        //             ->join('person_login','person_login.person_id','=','person.id')
        //             ->join('_role','_role.role_id','=','person.role_id')
        //             ->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.email')
        //             ->where('person_status','=','1')
        //             ->whereIn('person.role_id',$role_id)               
        //             ->groupBy('person.id')
        //             ->get();

        // foreach ($managerEmployee as $meKey => $meValue) {

        //     $manager_name = $meValue->user_name;
        //     $manager_mail = $meValue->email;

        //         Session::forget('juniordata');
        //         $juniors_array=self::getJuniorUser($meValue->user_id);
        //         $juniors_array = $request->session()->get('juniordata');
        //         if(empty($juniors_array))
        //         {
        //             $juniors_array = array();
        //         }


        //         $juniorDetails = DB::table('person')
        //                         ->join('person_login','person_login.person_id','=','person.id')
        //                         ->where('person_status','=','1')
        //                         ->whereIn('person.company_id',$company_id)
        //                         ->whereIn('person.id',$juniors_array)
        //                         ->whereIn('person.role_id',['144','171','172','173','150','151'])
        //                         ->groupBy('person.id')
        //                         ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id');


        //         $juniorRoleDetails = DB::table('person')
        //                         ->join('person_login','person_login.person_id','=','person.id')
        //                         ->join('_role','_role.role_id','=','person.role_id')
        //                         ->where('person_status','=','1')
        //                         ->whereIn('person.company_id',$company_id)
        //                         ->whereIn('person.id',$juniors_array)
        //                         ->whereIn('person.role_id',['144','171','172','173','150','151'])
        //                         ->groupBy('person.id')
        //                         ->pluck('rolename','person.id');

        //         $juniorHeadQuarterDetails = DB::table('person')
        //                         ->join('person_login','person_login.person_id','=','person.id')
        //                         ->join('_role','_role.role_id','=','person.role_id')
        //                         ->join('location_5','location_5.id','=','person.head_quater_id')
        //                         ->where('person_status','=','1')
        //                         ->whereIn('person.company_id',$company_id)
        //                         ->whereIn('person.id',$juniors_array)
        //                         ->whereIn('person.role_id',['144','171','172','173','150','151'])
        //                         ->groupBy('person.id')
        //                         ->pluck('location_5.name','person.id');




        //                          $mail = Mail::send('reports/sendMails/btwManagerUserDailySale', array(
        //                                             'sumOfNewRetailer' => $sumOfNewRetailer,
        //                                             'sumOfNewProductiveRetailer' => $sumOfNewProductiveRetailer,
        //                                             'sumOfTotalRetailer' => $sumOfTotalRetailer,
        //                                             'sumOfTotalCall'=>$sumOfTotalCall,
        //                                             'sumOfProductiveCall'=>$sumOfProductiveCall,
        //                                             'sumOfSales'=>$sumOfSales,
        //                                             'sumOfRetailingEmployee'=>$sumOfRetailingEmployee,
        //                                             'countOfTotalEmployee'=>$countOfTotalEmployee,
        //                                             'juniorDetails'=>$juniorDetails,
        //                                             'yesterday'=>$yesterday,
        //                                             'sumOfComTotalCall'=>$sumOfComTotalCall,
        //                                             'sumOfComProductiveCall'=>$sumOfComProductiveCall,
        //                                             'sumOfComSales'=>$sumOfComSales,
        //                                             'startDate'=>$startDate,
        //                                             'sumOfComNewProductiveRetailer'=>$sumOfComNewProductiveRetailer,
        //                                             'manager_name'=>$manager_name,
        //                                             'juniorRoleDetails'=>$juniorRoleDetails,
        //                                             'juniorHeadQuarterDetails'=>$juniorHeadQuarterDetails,
        //                                             'todayTask'=>$todayTask,
        //                                             'beatName'=>$beatName,
        //                                             'beatNumber'=>$beatNumber,
        //                                             'status'=>'1',

        //                             ) , function($message) use($manager_mail,$subject)
        //                     {
                                    
        //                         $message->from('manacle.php1@gmail.com');
        //                         $message->to($manager_mail)->subject($subject);

        //                     });

            
        // }

        ///////////////////////////////////// mails send to all asm/rsm/dgm sales ends //////////////////////////////////////





        ///////////////////////////////////// mails send to hr and chief sales starts //////////////////////////////////////
      
        $search_all_user = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.email')
                        ->where('person_status',1)
                        ->whereIn('person.company_id',$company_id)
                        ->whereIn('person.id',$person_id)
                        ->groupBy('person.id')
                        ->get();

                    // dd($search_all_user);



         foreach ($search_all_user as $saukey => $sauvalue) {

                $hrandsalesname = $sauvalue->user_name;
                $hrandsalesmail = $sauvalue->email;

                $manager_name = DB::table('person')
                            ->join('person_login','person_login.person_id','=','person.id')
                            ->where('person_status','=','1')
                            ->whereIn('person.company_id',$company_id)
                            ->whereIn('person.role_id',$role_id)
                            ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id');

                foreach ($manager_name as $mkey => $mvalue) {
                    Session::forget('juniordata');
                    $juniors_array=self::getJuniorUser($mkey);
                    $juniors_array = $request->session()->get('juniordata');

                    $finalmanagers[$mkey] = $juniors_array;
                }

                // dd($finalmanagers);

                $juniorDetails = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->where('person_status','=','1')
                                ->whereIn('person.company_id',$company_id)
                                ->whereIn('person.role_id',['144','171','172','173','150','151'])
                                ->groupBy('person.id')
                                ->orderBy('first_name','ASC')
                                ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id');

                $juniorSenior = array();
                foreach ($juniorDetails as $jdkey => $jdvalue) {
                    foreach ($finalmanagers as $fmkey => $fmvalue) {
                        foreach ((array)$fmvalue as $fkey => $fvalue) {
                                if($jdkey == $fvalue){
                                    $juniorSenior[$jdkey] = !empty($manager_name[$fmkey])?$manager_name[$fmkey]:'NA';
                                }
                        }
                    }
                }

                // dd($juniorSenior);



                $juniorRoleDetails = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('_role','_role.role_id','=','person.role_id')
                                ->where('person_status','=','1')
                                ->whereIn('person.company_id',$company_id)
                                ->whereIn('person.role_id',['144','171','172','173','150','151'])
                                ->groupBy('person.id')
                                ->pluck('rolename','person.id');

                $juniorHeadQuarterDetails = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('_role','_role.role_id','=','person.role_id')
                                ->join('location_5','location_5.id','=','person.head_quater_id')
                                ->where('person_status','=','1')
                                ->whereIn('person.company_id',$company_id)
                                ->whereIn('person.role_id',['144','171','172','173','150','151'])
                                ->groupBy('person.id')
                                ->pluck('location_5.name','person.id');

                                 $mail = Mail::send('reports/sendMails/btwManagerUserDailySale', array(
                                                    'sumOfNewRetailer' => $sumOfNewRetailer,
                                                    'sumOfNewProductiveRetailer' => $sumOfNewProductiveRetailer,
                                                    'sumOfTotalRetailer' => $sumOfTotalRetailer,
                                                    'sumOfTotalCall'=>$sumOfTotalCall,
                                                    'sumOfProductiveCall'=>$sumOfProductiveCall,
                                                    'sumOfSales'=>$sumOfSales,
                                                    'sumOfRetailingEmployee'=>$sumOfRetailingEmployee,
                                                    'countOfTotalEmployee'=>$countOfTotalEmployee,
                                                    'juniorDetails'=>$juniorDetails,
                                                    'yesterday'=>$yesterday,
                                                    'sumOfComTotalCall'=>$sumOfComTotalCall,
                                                    'sumOfComProductiveCall'=>$sumOfComProductiveCall,
                                                    'sumOfComSales'=>$sumOfComSales,
                                                    'startDate'=>$startDate,
                                                    'sumOfComNewProductiveRetailer'=>$sumOfComNewProductiveRetailer,
                                                    'manager_name'=>$manager_name,
                                                    'finalmanagers'=>$finalmanagers,
                                                    'juniorRoleDetails'=>$juniorRoleDetails,
                                                    'juniorHeadQuarterDetails'=>$juniorHeadQuarterDetails,
                                                    'todayTask'=>$todayTask,
                                                    'beatName'=>$beatName,
                                                    'beatNumber'=>$beatNumber,
                                                    'juniorSenior'=>$juniorSenior,
                                                    'status'=>'2',

                                    ) , function($message) use($hrandsalesmail,$subject)
                            {
                                    
                                $message->from('manacle.php1@gmail.com');
                                $message->to($hrandsalesmail)->subject($subject);

                            });
                    

            }

        ///////////////////////////////////// mails send to hr and chief sales ends //////////////////////////////////////


    } 



    ///////////////////////////////////////////////////// btw managers user daily reporting ends /////////////////////////////////////////////////////











     public function btwManagerUserDailyReportTest(Request $request)
    {

        $subject = 'Manager Users Daily Sale Report';
        $company_id = array("43");
        // $person_id = array("2092","3664");
        $person_id = array("3040");
        // $role_id = array("148","145","168");        
        $role_id = array("148","145","149","167");        

        $current_date = date('Y-m-d');
        // $startDate = date('Y-m-01');
        $yesterday = date('Y-m-d',strtotime("-1 days"));

        $startDate = date('Y-m-01', strtotime($yesterday));



        $todayTask = DB::table('user_daily_attendance')
                    ->join('person','person.id','=','user_daily_attendance.user_id')
                    ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                    ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date, '%Y-%m-%d') = '$yesterday'")
                    ->whereIn('user_daily_attendance.company_id',$company_id)
                    ->groupBy('user_daily_attendance.user_id')
                    ->pluck('_working_status.name','person.id');

        $beatName =  DB::table('user_sales_order')
                ->join('person','person.id','=','user_sales_order.user_id')
                ->join('location_7','location_7.id','=','user_sales_order.location_id')
                ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                ->whereIn('user_sales_order.company_id',$company_id)
                ->groupBy('person.id')
                ->pluck(DB::raw("GROUP_CONCAT(DISTINCT location_7.name) as beats"),'person.id');


        $beatNumber =  DB::table('user_sales_order')
                ->join('person','person.id','=','user_sales_order.user_id')
                ->join('location_7','location_7.id','=','user_sales_order.location_id')
                ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                ->whereIn('user_sales_order.company_id',$company_id)
                ->groupBy('person.id')
                ->pluck(DB::raw("GROUP_CONCAT(DISTINCT location_7.beat_no) as beats"),'person.id');



        $sumOfComTotalCall = DB::table('user_sales_order')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$yesterday'")
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('person.id')
                        ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalCall"),'person.id');
                        // count distinct id of user sales order by performance

        $sumOfComProductiveCall = DB::table('user_sales_order')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->where('user_sales_order.call_status','=','1')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$yesterday'")
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('person.id')
                        ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalProductiveCall"),'person.id');


        $sumOfComSales = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->where('user_sales_order.call_status','=','1')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$yesterday'")
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('person.id')
                        ->pluck(DB::raw("ROUND(SUM((user_sales_order_details.rate*user_sales_order_details.quantity)+(user_sales_order_details.case_rate*user_sales_order_details.case_qty)),2) as totalSales"),'person.id');


        $sumOfComNewProductiveRetailer = DB::table('retailer')
                            ->join('user_sales_order','user_sales_order.retailer_id','=','retailer.id')
                            ->join('person','person.id','=','retailer.created_by_person_id')
                            ->where('retailer_status','=','1')
                            ->where('call_status','=','1')
                            ->whereIn('retailer.company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(retailer.created_on, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(retailer.created_on, '%Y-%m-%d') <= '$yesterday'")
                            ->groupBy('person.id')
                            ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as newProductiveRetailer"),'person.id');




                            ############################################################################################################
                        $sumOfNewRetailerQuery = DB::table('user_sales_order')
                            ->join('person','person.id','=','user_sales_order.user_id')
                            ->join('user_daily_attendance','user_daily_attendance.user_id','=','person.id')
                            // ->join('location_5','location_5.id','=','person.head_quater_id')
                            ->where('user_sales_order.call_status','=','1')
                            ->where('work_status',89)
                            ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                            // ->where('location_5.status','=','1')
                            ->whereIn('user_daily_attendance.company_id',$company_id)
                            ->whereIn('user_sales_order.company_id',$company_id)
                            ->groupBy('person.id')
                            ->pluck(DB::raw("GROUP_CONCAT(DISTINCT location_id) as newProductiveRetailer"),'person.id');

                        $sumOfNewRetailer = array();
                        $sumOfTotalCall = array();
                        foreach ($sumOfNewRetailerQuery as $sonkey => $sonvalue) {
                            
                            $explode_location = explode(',',$sonvalue);

                            // dd($explode_location);

                            $retailer_count = DB::table('user_sales_order')
                                            ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                                            ->whereIn('retailer.location_id',$explode_location)
                                            ->whereIn('retailer.company_id',$company_id)
                                            ->where('call_status','=','1')
                                            ->select(DB::raw('COUNT(DISTINCT retailer.id) as count_retailer'))
                                            ->first();


                            $sumOfNewRetailer[$sonkey] = !empty($retailer_count->count_retailer)?$retailer_count->count_retailer:'0';

                            // $total_retailer_count = DB::table('retailer')
                            //                     ->whereIn('location_id',$explode_location)
                            //                     ->whereIn('company_id',$company_id)
                            //                     ->select(DB::raw('COUNT(retailer.id) as count_retailer'))
                            //                     ->first();

                            $total_retailer_count =  DB::table('user_sales_order')
                                                ->whereIn('location_id',$explode_location)
                                                ->whereIn('company_id',$company_id)
                                                ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                                                ->select(DB::raw('COUNT(DISTINCT retailer_id) as count_retailer'))
                                                ->first();

                            $sumOfTotalCall[$sonkey] = !empty($total_retailer_count->count_retailer)?$total_retailer_count->count_retailer:'0';





                        }
                            ############################################################################################################


        // $sumOfNewRetailer = DB::table('retailer')
        //                     ->join('person','person.id','=','retailer.created_by_person_id')
        //                     ->where('retailer_status','=','1')
        //                     ->whereIn('retailer.company_id',$company_id)
        //                     ->whereRaw("DATE_FORMAT(retailer.created_on, '%Y-%m-%d') = '$yesterday'")
        //                     ->groupBy('person.id')
        //                     ->pluck(DB::raw("COUNT(retailer.id) as newRetailer"),'person.id');


        // $sumOfNewProductiveRetailer = DB::table('retailer')
        //                     ->join('user_sales_order','user_sales_order.retailer_id','=','retailer.id')
        //                     ->join('person','person.id','=','retailer.created_by_person_id')
        //                     ->where('retailer_status','=','1')
        //                     ->where('call_status','=','1')
        //                     ->whereIn('retailer.company_id',$company_id)
        //                     ->whereRaw("DATE_FORMAT(retailer.created_on, '%Y-%m-%d') = '$yesterday'")
        //                     ->groupBy('person.id')
        //                     ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as newProductiveRetailer"),'person.id');



        $sumOfNewProductiveRetailer = DB::table('retailer')
                            ->join('user_sales_order','user_sales_order.retailer_id','=','retailer.id')
                            // ->join('location_7','location_7.id','=','retailer.location_id')
                            // ->join('location_6','location_6.id','=','location_7.location_6_id')
                            // ->join('location_5','location_5.id','=','location_6.location_5_id')
                            ->join('person','person.id','=','retailer.created_by_person_id')
                            // ->join('location_5','location_5.id','=','person.head_quater_id')
                            ->where('retailer_status','=','1')
                            ->where('call_status','=','1')
                            // ->where('location_5.status','=','1')
                            ->whereIn('retailer.company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(retailer.created_on, '%Y-%m-%d') = '$yesterday'")
                            ->groupBy('person.id')
                            ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as newProductiveRetailer"),'person.id');



        $sumOfTotalRetailer = DB::table('retailer')
                            ->join('person','person.id','=','retailer.created_by_person_id')
                            ->where('retailer_status','=','1')
                            ->whereIn('retailer.company_id',$company_id)
                            ->groupBy('person.id')
                            ->pluck(DB::raw("COUNT(retailer.id) as totalRetailer"),'person.id');

        // $sumOfTotalCall = DB::table('user_sales_order')
        //                 ->join('person','person.id','=','user_sales_order.user_id')
        //                 ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
        //                 ->whereIn('user_sales_order.company_id',$company_id)
        //                 ->groupBy('person.id')
        //                 ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalCall"),'person.id');

        $sumOfProductiveCall = DB::table('user_sales_order')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->where('user_sales_order.call_status','=','1')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('person.id')
                        ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalProductiveCall"),'person.id');

        $sumOfSales = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->where('user_sales_order.call_status','=','1')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('person.id')
                        ->pluck(DB::raw("ROUND(SUM((user_sales_order_details.rate*user_sales_order_details.quantity)+(user_sales_order_details.case_rate*user_sales_order_details.case_qty)),2) as totalSales"),'person.id');

        $sumOfRetailingEmployee = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('user_daily_attendance','user_daily_attendance.user_id','=','person.id')
                                ->where('person_status','=','1')
                                ->where('work_status',89)
                                ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date, '%Y-%m-%d') = '$yesterday'")
                                ->whereIn('user_daily_attendance.company_id',$company_id)
                                ->groupBy('person.id')
                                ->pluck(DB::raw("COUNT(user_id) as totalRetailingEmployee"),'person.id');

        $countOfTotalEmployee = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->where('person_status','=','1')
                                ->whereIn('person.company_id',$company_id)
                                ->whereIn('person.role_id',['144','171','172','173','150','151'])
                                ->groupBy('person.id')
                                ->pluck(DB::raw("COUNT(person.id) as totalEmployee"),'person.id');


    
        ///////////////////////////////////// mails send to all asm/rsm/dgm sales starts //////////////////////////////////////


        // $managerEmployee = DB::table('person') 
        //             ->join('person_login','person_login.person_id','=','person.id')
        //             ->join('_role','_role.role_id','=','person.role_id')
        //             ->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.email')
        //             ->where('person_status','=','1')
        //             ->whereIn('person.role_id',$role_id)               
        //             ->groupBy('person.id')
        //             ->get();

        // foreach ($managerEmployee as $meKey => $meValue) {

        //     $manager_name = $meValue->user_name;
        //     $manager_mail = $meValue->email;

        //         Session::forget('juniordata');
        //         $juniors_array=self::getJuniorUser($meValue->user_id);
        //         $juniors_array = $request->session()->get('juniordata');
        //         if(empty($juniors_array))
        //         {
        //             $juniors_array = array();
        //         }


        //         $juniorDetails = DB::table('person')
        //                         ->join('person_login','person_login.person_id','=','person.id')
        //                         ->where('person_status','=','1')
        //                         ->whereIn('person.company_id',$company_id)
        //                         ->whereIn('person.id',$juniors_array)
        //                         ->whereIn('person.role_id',['144','171','172','173','150','151'])
        //                         ->groupBy('person.id')
        //                         ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id');


        //         $juniorRoleDetails = DB::table('person')
        //                         ->join('person_login','person_login.person_id','=','person.id')
        //                         ->join('_role','_role.role_id','=','person.role_id')
        //                         ->where('person_status','=','1')
        //                         ->whereIn('person.company_id',$company_id)
        //                         ->whereIn('person.id',$juniors_array)
        //                         ->whereIn('person.role_id',['144','171','172','173','150','151'])
        //                         ->groupBy('person.id')
        //                         ->pluck('rolename','person.id');

        //         $juniorHeadQuarterDetails = DB::table('person')
        //                         ->join('person_login','person_login.person_id','=','person.id')
        //                         ->join('_role','_role.role_id','=','person.role_id')
        //                         ->join('location_5','location_5.id','=','person.head_quater_id')
        //                         ->where('person_status','=','1')
        //                         ->whereIn('person.company_id',$company_id)
        //                         ->whereIn('person.id',$juniors_array)
        //                         ->whereIn('person.role_id',['144','171','172','173','150','151'])
        //                         ->groupBy('person.id')
        //                         ->pluck('location_5.name','person.id');




        //                          $mail = Mail::send('reports/sendMails/btwManagerUserDailySale', array(
        //                                             'sumOfNewRetailer' => $sumOfNewRetailer,
        //                                             'sumOfNewProductiveRetailer' => $sumOfNewProductiveRetailer,
        //                                             'sumOfTotalRetailer' => $sumOfTotalRetailer,
        //                                             'sumOfTotalCall'=>$sumOfTotalCall,
        //                                             'sumOfProductiveCall'=>$sumOfProductiveCall,
        //                                             'sumOfSales'=>$sumOfSales,
        //                                             'sumOfRetailingEmployee'=>$sumOfRetailingEmployee,
        //                                             'countOfTotalEmployee'=>$countOfTotalEmployee,
        //                                             'juniorDetails'=>$juniorDetails,
        //                                             'yesterday'=>$yesterday,
        //                                             'sumOfComTotalCall'=>$sumOfComTotalCall,
        //                                             'sumOfComProductiveCall'=>$sumOfComProductiveCall,
        //                                             'sumOfComSales'=>$sumOfComSales,
        //                                             'startDate'=>$startDate,
        //                                             'sumOfComNewProductiveRetailer'=>$sumOfComNewProductiveRetailer,
        //                                             'manager_name'=>$manager_name,
        //                                             'juniorRoleDetails'=>$juniorRoleDetails,
        //                                             'juniorHeadQuarterDetails'=>$juniorHeadQuarterDetails,
        //                                             'todayTask'=>$todayTask,
        //                                             'beatName'=>$beatName,
        //                                             'beatNumber'=>$beatNumber,
        //                                             'status'=>'1',

        //                             ) , function($message) use($manager_mail,$subject)
        //                     {
                                    
        //                         $message->from('manacle.php1@gmail.com');
        //                         $message->to($manager_mail)->subject($subject);

        //                     });

            
        // }

        ///////////////////////////////////// mails send to all asm/rsm/dgm sales ends //////////////////////////////////////





        ///////////////////////////////////// mails send to hr and chief sales starts //////////////////////////////////////
      
        $search_all_user = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.email')
                        ->where('person_status',1)
                        ->whereIn('person.company_id',$company_id)
                        ->whereIn('person.id',$person_id)
                        ->groupBy('person.id')
                        ->get();

                    // dd($search_all_user);



         foreach ($search_all_user as $saukey => $sauvalue) {

                $hrandsalesname = $sauvalue->user_name;
                $hrandsalesmail = $sauvalue->email;

                $manager_name = DB::table('person')
                            ->join('person_login','person_login.person_id','=','person.id')
                            ->where('person_status','=','1')
                            ->whereIn('person.company_id',$company_id)
                            ->whereIn('person.role_id',$role_id)
                            ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id');

                foreach ($manager_name as $mkey => $mvalue) {
                    Session::forget('juniordata');
                    $juniors_array=self::getJuniorUser($mkey);
                    $juniors_array = $request->session()->get('juniordata');

                    $finalmanagers[$mkey] = $juniors_array;
                }

                $juniorRoleDetails = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('_role','_role.role_id','=','person.role_id')
                                ->where('person_status','=','1')
                                ->whereIn('person.company_id',$company_id)
                                ->whereIn('person.role_id',['144','171','172','173','150','151'])
                                ->groupBy('person.id')
                                ->pluck('rolename','person.id');

                $juniorHeadQuarterDetails = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('_role','_role.role_id','=','person.role_id')
                                ->join('location_5','location_5.id','=','person.head_quater_id')
                                ->where('person_status','=','1')
                                ->whereIn('person.company_id',$company_id)
                                ->whereIn('person.role_id',['144','171','172','173','150','151'])
                                ->groupBy('person.id')
                                ->pluck('location_5.name','person.id');
                                

                $juniorHeadQuarterDetailsId = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->where('person_status','=','1')
                                ->whereIn('person.company_id',$company_id)
                                ->whereIn('person.role_id',['144','171','172','173','150','151'])
                                ->groupBy('person.id')
                                ->pluck('person.head_quater_id','person.id');

                // dd($juniorHeadQuarterDetailsId);


                $juniorDetails = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->where('person_status','=','1')
                                ->whereIn('person.company_id',$company_id)
                                ->whereIn('person.role_id',['144','171','172','173','150','151'])
                                ->groupBy('person.id')
                                ->orderBy('first_name','ASC')
                                ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id');

                $juniorSenior = array();
                foreach ($juniorDetails as $jdkey => $jdvalue) {
                    foreach ($finalmanagers as $fmkey => $fmvalue) {
                        foreach ((array)$fmvalue as $fkey => $fvalue) {
                                if($jdkey == $fvalue){
                                    $juniorSenior[$jdkey] = !empty($manager_name[$fmkey])?$manager_name[$fmkey]:'NA';
                                }
                        }
                    }
                }

                 $managerEmployeeRecent = DB::table('person') 
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->join('dealer_location_rate_list','dealer_location_rate_list.user_id','=','person.id')
                    ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
                    ->join('location_6','location_6.id','=','location_7.location_6_id')
                    ->join('location_5','location_5.id','=','location_6.location_5_id')
                    ->where('person_status','=','1')
                    ->whereIn('person.role_id',$role_id)               
                    ->groupBy('person.id','location_5.id')
                    ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'location_5.id');



                    // dd($juniorDetails);
                $juniorDetailsLoop = $juniorDetails;

                foreach ($juniorDetailsLoop as $key => $data) {

                      // $mngrs = !empty($juniorSenior[$key])?$juniorSenior[$key]:'';

                    $today_task = !empty($todayTask[$key])?$todayTask[$key]:'NA';
                    $beat_name = !empty($beatName[$key])?$beatName[$key]:'NA';

                    $beat_number = !empty($beatNumber[$key])?$beatNumber[$key]:'';



                    $role = !empty($juniorRoleDetails[$key])?$juniorRoleDetails[$key]:'NA';
                    $head_quarter = !empty($juniorHeadQuarterDetails[$key])?$juniorHeadQuarterDetails[$key]:'NA';

                    $head_quarter_id = !empty($juniorHeadQuarterDetailsId[$key])?$juniorHeadQuarterDetailsId[$key]:'NA';

                    $mngrs = !empty($managerEmployeeRecent[$head_quarter_id])?$managerEmployeeRecent[$head_quarter_id]:'';



                    // $manager = !empty($managerEmployee[$key])?$managerEmployee[$key]:'NA';
                    $NewRetailer = !empty($sumOfNewRetailer[$key])?$sumOfNewRetailer[$key]:'0';
                    $NewProductiveRetailer = !empty($sumOfNewProductiveRetailer[$key])?$sumOfNewProductiveRetailer[$key]:'0';
                    // $TotalRetailer = !empty($sumOfTotalRetailer[$key])?$sumOfTotalRetailer[$key]:'0';
                    $TotalCall = !empty($sumOfTotalCall[$key])?$sumOfTotalCall[$key]:'0';
                    $ProductiveCall = !empty($sumOfProductiveCall[$key])?$sumOfProductiveCall[$key]:'0';
                    $Sales = !empty($sumOfSales[$key])?$sumOfSales[$key]:'0';
                    $RetailingEmployee = !empty($sumOfRetailingEmployee[$key])?$sumOfRetailingEmployee[$key]:'0';
                    $TotalEmployee = !empty($countOfTotalEmployee[$key])?$countOfTotalEmployee[$key]:'0';

                    // $secondaryTarget = 12500*$RetailingEmployee;
                    $secondaryTarget = 12500*$TotalEmployee;


                    $TotalComCall = !empty($sumOfComTotalCall[$key])?$sumOfComTotalCall[$key]:'0';
                    $ProductiveComCall = !empty($sumOfComProductiveCall[$key])?$sumOfComProductiveCall[$key]:'0';
                    $SalesCom = !empty($sumOfComSales[$key])?$sumOfComSales[$key]:'0';

                    $NewComProductiveRetailer = !empty($sumOfComNewProductiveRetailer[$key])?$sumOfComNewProductiveRetailer[$key]:'0';


                     if($secondaryTarget != 0){
                    $achievePer = round(($Sales/$secondaryTarget)*100);
                    }else{
                    $achievePer = '0';
                    }




                    if($RetailingEmployee != 0){
                    $avgNewRetailer = round($NewProductiveRetailer/$RetailingEmployee);
                    }else{
                    $avgNewRetailer = '0';
                    }
                    
                    if($RetailingEmployee != 0){
                    $avgTC = round($TotalCall/$RetailingEmployee);
                    }else{
                    $avgTC = '0';
                    }

                    if($RetailingEmployee != 0){
                    $avgPC = round($ProductiveCall/$RetailingEmployee);
                    }else{
                    $avgPC = '0';
                    }   

                    if($RetailingEmployee != 0){
                    $avgSales = round($Sales/$RetailingEmployee);
                    }else{
                    $avgSales = '0';
                    }



                    $finalJuniorDetails['manager_name'] = $mngrs;
                    $finalJuniorDetails['head_quarter'] = $head_quarter;
                    $finalJuniorDetails['data'] = $data;
                    $finalJuniorDetails['role'] = $role;
                    // $finalJuniorDetails['head_quarter'] = $head_quarter;
                    $finalJuniorDetails['today_task'] = $today_task;
                    $finalJuniorDetails['beat_number'] = $beat_number;
                    $finalJuniorDetails['beat_name'] = $beat_name;
                    $finalJuniorDetails['NewRetailer'] = $NewRetailer;
                    $finalJuniorDetails['NewProductiveRetailer'] = $NewProductiveRetailer;
                    $finalJuniorDetails['TotalCall'] = $TotalCall;
                    $finalJuniorDetails['ProductiveCall'] = $ProductiveCall;
                    $finalJuniorDetails['Sales'] = $Sales;
                    $finalJuniorDetails['secondaryTarget'] = $secondaryTarget;
                    $finalJuniorDetails['achievePer'] = $achievePer;
                    $finalJuniorDetails['RetailingEmployee'] = $RetailingEmployee;
                    $finalJuniorDetails['TotalEmployee'] = $TotalEmployee;
                    $finalJuniorDetails['avgNewRetailer'] = $avgNewRetailer;
                    $finalJuniorDetails['avgTC'] = $avgTC;
                    $finalJuniorDetails['avgPC'] = $avgPC;
                    $finalJuniorDetails['avgSales'] = $avgSales;
                    $finalJuniorDetails['NewComProductiveRetailer'] = $NewComProductiveRetailer;
                    $finalJuniorDetails['TotalComCall'] = $TotalComCall;
                    $finalJuniorDetails['ProductiveComCall'] = $ProductiveComCall;
                    $finalJuniorDetails['SalesCom'] = $SalesCom;

                    $juniorDetailsArray[] = $finalJuniorDetails;


                }

                // dd($juniorDetailsArray);


                $price = array_column($juniorDetailsArray, 'manager_name');

                array_multisort($price, SORT_ASC, $juniorDetailsArray);

                // dd($juniorDetailsArray);




                

                                 $mail = Mail::send('reports/sendMails/btwManagerUserDailySaleTest', array(
                                                    // 'sumOfNewRetailer' => $sumOfNewRetailer,
                                                    // 'sumOfNewProductiveRetailer' => $sumOfNewProductiveRetailer,
                                                    // 'sumOfTotalRetailer' => $sumOfTotalRetailer,
                                                    // 'sumOfTotalCall'=>$sumOfTotalCall,
                                                    // 'sumOfProductiveCall'=>$sumOfProductiveCall,
                                                    // 'sumOfSales'=>$sumOfSales,
                                                    // 'sumOfRetailingEmployee'=>$sumOfRetailingEmployee,
                                                    // 'countOfTotalEmployee'=>$countOfTotalEmployee,
                                                    'juniorDetails'=>$juniorDetailsArray,
                                                    'yesterday'=>$yesterday,
                                                    // 'sumOfComTotalCall'=>$sumOfComTotalCall,
                                                    // 'sumOfComProductiveCall'=>$sumOfComProductiveCall,
                                                    // 'sumOfComSales'=>$sumOfComSales,
                                                    'startDate'=>$startDate,
                                                    // 'sumOfComNewProductiveRetailer'=>$sumOfComNewProductiveRetailer,
                                                    // 'manager_name'=>$manager_name,
                                                    // 'finalmanagers'=>$finalmanagers,
                                                    // 'juniorRoleDetails'=>$juniorRoleDetails,
                                                    // 'juniorHeadQuarterDetails'=>$juniorHeadQuarterDetails,
                                                    // 'todayTask'=>$todayTask,
                                                    // 'beatName'=>$beatName,
                                                    // 'beatNumber'=>$beatNumber,
                                                    // 'juniorSenior'=>$juniorSenior,
                                                    'status'=>'2',

                                    ) , function($message) use($hrandsalesmail,$subject)
                            {
                                    
                                $message->from('manacle.php1@gmail.com');
                                $message->to($hrandsalesmail)->subject($subject);

                            });
                    

            }

        ///////////////////////////////////// mails send to hr and chief sales ends //////////////////////////////////////


    } 




    ////////////////////////////////////////////////////// for managers testing ////////////////////////////////////////


     public function btwManagerReportToEveryOne(Request $request)
    {

        $subject = 'Manager Users Daily Sale Report';
        $company_id = array("43");
        // $person_id = array("2092","3664");
        // $person_id = array("3040");
        // $role_id = array("148","145","168");        
        $role_id = array("148","145","168");        

        $current_date = date('Y-m-d');
        // $startDate = date('Y-m-01');
        $yesterday = date('Y-m-d',strtotime("-1 days"));

        $startDate = date('Y-m-01', strtotime($yesterday));



        $todayTask = DB::table('user_daily_attendance')
                    ->join('person','person.id','=','user_daily_attendance.user_id')
                    ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                    ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date, '%Y-%m-%d') = '$yesterday'")
                    ->whereIn('user_daily_attendance.company_id',$company_id)
                    ->groupBy('user_daily_attendance.user_id')
                    ->pluck('_working_status.name','person.id');

        $beatName =  DB::table('user_sales_order')
                ->join('person','person.id','=','user_sales_order.user_id')
                ->join('location_7','location_7.id','=','user_sales_order.location_id')
                ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                ->whereIn('user_sales_order.company_id',$company_id)
                ->groupBy('person.id')
                ->pluck(DB::raw("GROUP_CONCAT(DISTINCT location_7.name) as beats"),'person.id');


        $beatNumber =  DB::table('user_sales_order')
                ->join('person','person.id','=','user_sales_order.user_id')
                ->join('location_7','location_7.id','=','user_sales_order.location_id')
                ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                ->whereIn('user_sales_order.company_id',$company_id)
                ->groupBy('person.id')
                ->pluck(DB::raw("GROUP_CONCAT(DISTINCT location_7.beat_no) as beats"),'person.id');



        $sumOfComTotalCall = DB::table('user_sales_order')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$yesterday'")
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('person.id')
                        ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalCall"),'person.id');
                        // count distinct id of user sales order by performance

        $sumOfComProductiveCall = DB::table('user_sales_order')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->where('user_sales_order.call_status','=','1')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$yesterday'")
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('person.id')
                        ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalProductiveCall"),'person.id');


        $sumOfComSales = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->where('user_sales_order.call_status','=','1')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$yesterday'")
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('person.id')
                        ->pluck(DB::raw("ROUND(SUM((user_sales_order_details.rate*user_sales_order_details.quantity)+(user_sales_order_details.case_rate*user_sales_order_details.case_qty)),2) as totalSales"),'person.id');


        $sumOfComNewProductiveRetailer = DB::table('retailer')
                            ->join('user_sales_order','user_sales_order.retailer_id','=','retailer.id')
                            ->join('person','person.id','=','retailer.created_by_person_id')
                            ->where('retailer_status','=','1')
                            ->where('call_status','=','1')
                            ->whereIn('retailer.company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(retailer.created_on, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(retailer.created_on, '%Y-%m-%d') <= '$yesterday'")
                            ->groupBy('person.id')
                            ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as newProductiveRetailer"),'person.id');




                            ############################################################################################################
                        $sumOfNewRetailerQuery = DB::table('user_sales_order')
                            ->join('person','person.id','=','user_sales_order.user_id')
                            ->join('user_daily_attendance','user_daily_attendance.user_id','=','person.id')
                            // ->join('location_5','location_5.id','=','person.head_quater_id')
                            ->where('user_sales_order.call_status','=','1')
                            ->where('work_status',89)
                            ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                            // ->where('location_5.status','=','1')
                            ->whereIn('user_daily_attendance.company_id',$company_id)
                            ->whereIn('user_sales_order.company_id',$company_id)
                            ->groupBy('person.id')
                            ->pluck(DB::raw("GROUP_CONCAT(DISTINCT location_id) as newProductiveRetailer"),'person.id');

                        $sumOfNewRetailer = array();
                        $sumOfTotalCall = array();
                        foreach ($sumOfNewRetailerQuery as $sonkey => $sonvalue) {
                            
                            $explode_location = explode(',',$sonvalue);

                            // dd($explode_location);

                            $retailer_count = DB::table('user_sales_order')
                                            ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                                            ->whereIn('retailer.location_id',$explode_location)
                                            ->whereIn('retailer.company_id',$company_id)
                                            ->where('call_status','=','1')
                                            ->select(DB::raw('COUNT(DISTINCT retailer.id) as count_retailer'))
                                            ->first();


                            $sumOfNewRetailer[$sonkey] = !empty($retailer_count->count_retailer)?$retailer_count->count_retailer:'0';

                            // $total_retailer_count = DB::table('retailer')
                            //                     ->whereIn('location_id',$explode_location)
                            //                     ->whereIn('company_id',$company_id)
                            //                     ->select(DB::raw('COUNT(retailer.id) as count_retailer'))
                            //                     ->first();

                            $total_retailer_count =  DB::table('user_sales_order')
                                                ->whereIn('location_id',$explode_location)
                                                ->whereIn('company_id',$company_id)
                                                ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                                                ->select(DB::raw('COUNT(DISTINCT retailer_id) as count_retailer'))
                                                ->first();

                            $sumOfTotalCall[$sonkey] = !empty($total_retailer_count->count_retailer)?$total_retailer_count->count_retailer:'0';





                        }
                            ############################################################################################################


        // $sumOfNewRetailer = DB::table('retailer')
        //                     ->join('person','person.id','=','retailer.created_by_person_id')
        //                     ->where('retailer_status','=','1')
        //                     ->whereIn('retailer.company_id',$company_id)
        //                     ->whereRaw("DATE_FORMAT(retailer.created_on, '%Y-%m-%d') = '$yesterday'")
        //                     ->groupBy('person.id')
        //                     ->pluck(DB::raw("COUNT(retailer.id) as newRetailer"),'person.id');


        // $sumOfNewProductiveRetailer = DB::table('retailer')
        //                     ->join('user_sales_order','user_sales_order.retailer_id','=','retailer.id')
        //                     ->join('person','person.id','=','retailer.created_by_person_id')
        //                     ->where('retailer_status','=','1')
        //                     ->where('call_status','=','1')
        //                     ->whereIn('retailer.company_id',$company_id)
        //                     ->whereRaw("DATE_FORMAT(retailer.created_on, '%Y-%m-%d') = '$yesterday'")
        //                     ->groupBy('person.id')
        //                     ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as newProductiveRetailer"),'person.id');



        $sumOfNewProductiveRetailer = DB::table('retailer')
                            ->join('user_sales_order','user_sales_order.retailer_id','=','retailer.id')
                            // ->join('location_7','location_7.id','=','retailer.location_id')
                            // ->join('location_6','location_6.id','=','location_7.location_6_id')
                            // ->join('location_5','location_5.id','=','location_6.location_5_id')
                            ->join('person','person.id','=','retailer.created_by_person_id')
                            // ->join('location_5','location_5.id','=','person.head_quater_id')
                            ->where('retailer_status','=','1')
                            ->where('call_status','=','1')
                            // ->where('location_5.status','=','1')
                            ->whereIn('retailer.company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(retailer.created_on, '%Y-%m-%d') = '$yesterday'")
                            ->groupBy('person.id')
                            ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as newProductiveRetailer"),'person.id');



        $sumOfTotalRetailer = DB::table('retailer')
                            ->join('person','person.id','=','retailer.created_by_person_id')
                            ->where('retailer_status','=','1')
                            ->whereIn('retailer.company_id',$company_id)
                            ->groupBy('person.id')
                            ->pluck(DB::raw("COUNT(retailer.id) as totalRetailer"),'person.id');

        // $sumOfTotalCall = DB::table('user_sales_order')
        //                 ->join('person','person.id','=','user_sales_order.user_id')
        //                 ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
        //                 ->whereIn('user_sales_order.company_id',$company_id)
        //                 ->groupBy('person.id')
        //                 ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalCall"),'person.id');

        $sumOfProductiveCall = DB::table('user_sales_order')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->where('user_sales_order.call_status','=','1')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('person.id')
                        ->pluck(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalProductiveCall"),'person.id');

        $sumOfSales = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->join('person','person.id','=','user_sales_order.user_id')
                        ->where('user_sales_order.call_status','=','1')
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$yesterday'")
                        ->whereIn('user_sales_order.company_id',$company_id)
                        ->groupBy('person.id')
                        ->pluck(DB::raw("ROUND(SUM((user_sales_order_details.rate*user_sales_order_details.quantity)+(user_sales_order_details.case_rate*user_sales_order_details.case_qty)),2) as totalSales"),'person.id');

        $sumOfRetailingEmployee = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('user_daily_attendance','user_daily_attendance.user_id','=','person.id')
                                ->where('person_status','=','1')
                                ->where('work_status',89)
                                ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date, '%Y-%m-%d') = '$yesterday'")
                                ->whereIn('user_daily_attendance.company_id',$company_id)
                                ->groupBy('person.id')
                                ->pluck(DB::raw("COUNT(user_id) as totalRetailingEmployee"),'person.id');

        $countOfTotalEmployee = DB::table('person')
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->where('person_status','=','1')
                                ->whereIn('person.company_id',$company_id)
                                ->whereIn('person.role_id',['144','171','172','173','150','151'])
                                ->groupBy('person.id')
                                ->pluck(DB::raw("COUNT(person.id) as totalEmployee"),'person.id');


    
        ///////////////////////////////////// mails send to all asm/rsm/dgm sales starts //////////////////////////////////////


                    $managerEmployee = DB::table('person') 
                                ->join('person_login','person_login.person_id','=','person.id')
                                ->join('_role','_role.role_id','=','person.role_id')
                                ->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.email')
                                ->where('person_status','=','1')
                                ->whereIn('person.role_id',$role_id)               
                                // ->whereIn('person.id',$person_id)               
                                ->groupBy('person.id')
                                ->get();

                    foreach ($managerEmployee as $meKey => $meValue) {

                        $manager_name = $meValue->user_name;
                        $manager_mail = $meValue->email;

                            Session::forget('juniordata');
                            $juniors_array=self::getJuniorUser($meValue->user_id);
                            $juniors_array = $request->session()->get('juniordata');
                            if(empty($juniors_array))
                            {
                                $juniors_array = array();
                            }


                            $juniorDetails = DB::table('person')
                                            ->join('person_login','person_login.person_id','=','person.id')
                                            ->where('person_status','=','1')
                                            ->whereIn('person.company_id',$company_id)
                                            ->whereIn('person.id',$juniors_array)
                                            ->whereIn('person.role_id',['144','171','172','173','150','151'])
                                            ->groupBy('person.id')
                                            ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id');


                            $juniorRoleDetails = DB::table('person')
                                            ->join('person_login','person_login.person_id','=','person.id')
                                            ->join('_role','_role.role_id','=','person.role_id')
                                            ->where('person_status','=','1')
                                            ->whereIn('person.company_id',$company_id)
                                            ->whereIn('person.id',$juniors_array)
                                            ->whereIn('person.role_id',['144','171','172','173','150','151'])
                                            ->groupBy('person.id')
                                            ->pluck('rolename','person.id');

                            $juniorHeadQuarterDetails = DB::table('person')
                                            ->join('person_login','person_login.person_id','=','person.id')
                                            ->join('_role','_role.role_id','=','person.role_id')
                                            ->join('location_5','location_5.id','=','person.head_quater_id')
                                            ->where('person_status','=','1')
                                            ->whereIn('person.company_id',$company_id)
                                            ->whereIn('person.id',$juniors_array)
                                            ->whereIn('person.role_id',['144','171','172','173','150','151'])
                                            ->groupBy('person.id')
                                            ->pluck('location_5.name','person.id');



                                            ///////////////// for test starts  //////////////////



                // $juniorDetailsLoop = $juniorDetails;

                // foreach ($juniorDetailsLoop as $key => $data) {

                //       // $mngrs = !empty($juniorSenior[$key])?$juniorSenior[$key]:'';

                //     $today_task = !empty($todayTask[$key])?$todayTask[$key]:'NA';
                //     $beat_name = !empty($beatName[$key])?$beatName[$key]:'NA';

                //     $beat_number = !empty($beatNumber[$key])?$beatNumber[$key]:'';



                //     $role = !empty($juniorRoleDetails[$key])?$juniorRoleDetails[$key]:'NA';
                //     $head_quarter = !empty($juniorHeadQuarterDetails[$key])?$juniorHeadQuarterDetails[$key]:'NA';

                 

                //     $mngrs = $manager_name;



                //     // $manager = !empty($managerEmployee[$key])?$managerEmployee[$key]:'NA';
                //     $NewRetailer = !empty($sumOfNewRetailer[$key])?$sumOfNewRetailer[$key]:'0';
                //     $NewProductiveRetailer = !empty($sumOfNewProductiveRetailer[$key])?$sumOfNewProductiveRetailer[$key]:'0';
                //     // $TotalRetailer = !empty($sumOfTotalRetailer[$key])?$sumOfTotalRetailer[$key]:'0';
                //     $TotalCall = !empty($sumOfTotalCall[$key])?$sumOfTotalCall[$key]:'0';
                //     $ProductiveCall = !empty($sumOfProductiveCall[$key])?$sumOfProductiveCall[$key]:'0';
                //     $Sales = !empty($sumOfSales[$key])?$sumOfSales[$key]:'0';
                //     $RetailingEmployee = !empty($sumOfRetailingEmployee[$key])?$sumOfRetailingEmployee[$key]:'0';
                //     $TotalEmployee = !empty($countOfTotalEmployee[$key])?$countOfTotalEmployee[$key]:'0';

                //     // $secondaryTarget = 12500*$RetailingEmployee;
                //     $secondaryTarget = 12500*$TotalEmployee;


                //     $TotalComCall = !empty($sumOfComTotalCall[$key])?$sumOfComTotalCall[$key]:'0';
                //     $ProductiveComCall = !empty($sumOfComProductiveCall[$key])?$sumOfComProductiveCall[$key]:'0';
                //     $SalesCom = !empty($sumOfComSales[$key])?$sumOfComSales[$key]:'0';

                //     $NewComProductiveRetailer = !empty($sumOfComNewProductiveRetailer[$key])?$sumOfComNewProductiveRetailer[$key]:'0';


                //      if($secondaryTarget != 0){
                //     $achievePer = round(($Sales/$secondaryTarget)*100);
                //     }else{
                //     $achievePer = '0';
                //     }




                //     if($RetailingEmployee != 0){
                //     $avgNewRetailer = round($NewProductiveRetailer/$RetailingEmployee);
                //     }else{
                //     $avgNewRetailer = '0';
                //     }
                    
                //     if($RetailingEmployee != 0){
                //     $avgTC = round($TotalCall/$RetailingEmployee);
                //     }else{
                //     $avgTC = '0';
                //     }

                //     if($RetailingEmployee != 0){
                //     $avgPC = round($ProductiveCall/$RetailingEmployee);
                //     }else{
                //     $avgPC = '0';
                //     }   

                //     if($RetailingEmployee != 0){
                //     $avgSales = round($Sales/$RetailingEmployee);
                //     }else{
                //     $avgSales = '0';
                //     }



                //     $finalJuniorDetails['manager_name'] = $mngrs;
                //     $finalJuniorDetails['head_quarter'] = $head_quarter;
                //     $finalJuniorDetails['data'] = $data;
                //     $finalJuniorDetails['role'] = $role;
                //     // $finalJuniorDetails['head_quarter'] = $head_quarter;
                //     $finalJuniorDetails['today_task'] = $today_task;
                //     $finalJuniorDetails['beat_number'] = $beat_number;
                //     $finalJuniorDetails['beat_name'] = $beat_name;
                //     $finalJuniorDetails['NewRetailer'] = $NewRetailer;
                //     $finalJuniorDetails['NewProductiveRetailer'] = $NewProductiveRetailer;
                //     $finalJuniorDetails['TotalCall'] = $TotalCall;
                //     $finalJuniorDetails['ProductiveCall'] = $ProductiveCall;
                //     $finalJuniorDetails['Sales'] = $Sales;
                //     $finalJuniorDetails['secondaryTarget'] = $secondaryTarget;
                //     $finalJuniorDetails['achievePer'] = $achievePer;
                //     $finalJuniorDetails['RetailingEmployee'] = $RetailingEmployee;
                //     $finalJuniorDetails['TotalEmployee'] = $TotalEmployee;
                //     $finalJuniorDetails['avgNewRetailer'] = $avgNewRetailer;
                //     $finalJuniorDetails['avgTC'] = $avgTC;
                //     $finalJuniorDetails['avgPC'] = $avgPC;
                //     $finalJuniorDetails['avgSales'] = $avgSales;
                //     $finalJuniorDetails['NewComProductiveRetailer'] = $NewComProductiveRetailer;
                //     $finalJuniorDetails['TotalComCall'] = $TotalComCall;
                //     $finalJuniorDetails['ProductiveComCall'] = $ProductiveComCall;
                //     $finalJuniorDetails['SalesCom'] = $SalesCom;

                //     $juniorDetailsArray[] = $finalJuniorDetails;


                // }

                // // dd($juniorDetailsArray);


                // $price = array_column($juniorDetailsArray, 'head_quarter');

                // array_multisort($price, SORT_ASC, $juniorDetailsArray);


                                            /////////////// for test ends /////////////////////////




                                             $mail = Mail::send('reports/sendMails/btwManagerUserDailySale', array(
                                                                'sumOfNewRetailer' => $sumOfNewRetailer,
                                                                'sumOfNewProductiveRetailer' => $sumOfNewProductiveRetailer,
                                                                'sumOfTotalRetailer' => $sumOfTotalRetailer,
                                                                'sumOfTotalCall'=>$sumOfTotalCall,
                                                                'sumOfProductiveCall'=>$sumOfProductiveCall,
                                                                'sumOfSales'=>$sumOfSales,
                                                                'sumOfRetailingEmployee'=>$sumOfRetailingEmployee,
                                                                'countOfTotalEmployee'=>$countOfTotalEmployee,
                                                                'juniorDetails'=>$juniorDetails,
                                                                'yesterday'=>$yesterday,
                                                                'sumOfComTotalCall'=>$sumOfComTotalCall,
                                                                'sumOfComProductiveCall'=>$sumOfComProductiveCall,
                                                                'sumOfComSales'=>$sumOfComSales,
                                                                'startDate'=>$startDate,
                                                                'sumOfComNewProductiveRetailer'=>$sumOfComNewProductiveRetailer,
                                                                'manager_name'=>$manager_name,
                                                                'juniorRoleDetails'=>$juniorRoleDetails,
                                                                'juniorHeadQuarterDetails'=>$juniorHeadQuarterDetails,
                                                                'todayTask'=>$todayTask,
                                                                'beatName'=>$beatName,
                                                                'beatNumber'=>$beatNumber,
                                                                'status'=>'1',

                                                ) , function($message) use($manager_mail,$subject)
                                        {
                                                
                                            $message->from('manacle.php1@gmail.com');

                                            $cc_mail = array("hr3@btwindia.com");
                                            $bcc_mail = array("chiefsales@btwindia.com");

                                            // $bcc_mail = array("rohit.k@manacleindia.com");
                                            // $cc_mail = array("rohit.k@manacleindia.com");


                                              


                                            $message->to($manager_mail)->cc($cc_mail)->bcc($bcc_mail)->subject($subject);

                                        });

                        
                    }

        ///////////////////////////////////// mails send to all asm/rsm/dgm sales ends //////////////////////////////////////



                    

            }



    

/////////////////////////////////////////// managers testing ends ////////////////////////////////////////




	
    public function getJuniorUser($code)
    {
        $res1="";
        $res2="";
        $details = UserDetail::join("person_login","person_login.person_id","=","person.id")->where('person_login.person_status',1)->where('person_id_senior',$code)
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



    // public function getSeniorUserAsmRsmDgm($code)
    // {
    //     $res1="";
    //     $res2="";
    //     // dd($code);
    //     $role = array(145,148,168);

    //     $details = DB::table('person')
    //         ->join('users','users.id','=','person.id')
    //         ->join('person_login','person_login.person_id','=','person.id')
    //         ->select('person_id_senior as user_id')
    //         ->where('person.id',$code)
    //         ->where('person_id_senior','!=',0)
    //         ->where('is_admin','!=',1)
    //         ->where('person_status',1)
    //         ->whereIn('person.role_id',$role)
    //         ->get();
    //         $num = count($details);  
    //         if($num>0)
    //         {

    //             foreach($details as $key=>$res2)
    //             {
    //                 if($res2->user_id!="" || $res2->user_id!="0")
    //                 {
    //                     // dd($res2);
    //                     //$product = collect([1,2,3,4]);
    //                     Session::push('seniorData', $res2->user_id);
    //                    // $_SESSION['juniordata'][]=$res2->user_id;
    //                     Self::getSeniorUser($res2->user_id);
    //                 }
    //             }
                
    //         }
    //         else
    //         {
    //             foreach($details as $key1=>$res1)
    //             {
    //                 if($res1->user_id!="" || $res2->user_id!="0")
    //                 {
    //                     Session::push('seniorData', $res1->user_id);
    //                     // $_SESSION['juniordata'][]= $res1->user_id;
    //                 }
    //             }

                
    //         }
    //         // dd(Session::get('juniordata'))
    //         return 1;
    //     }


}
