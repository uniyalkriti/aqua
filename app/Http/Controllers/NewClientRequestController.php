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
use Excel;



class NewClientRequestController extends Controller
{
	


	public function newClientRequest(Request $request)
	{
		  
          dd($request);

        $txtname = $request->txtname;
        $txtdeg = $request->txtdeg;
        $industry = $request->industry;
        $otxtog = $request->otxtog;
        $txtmail = $request->txtmail;
        $users = $request->users;
        $txtcontact = $request->txtcontact;
        $enquiry = $request->enquiry;


        $insertArray = [
            'client_full_name' => $txtname,
            'designation' => $txtdeg,
            'industry_type' => $industry,
            'other_industry' => $otxtog,
            'approx_user' => $users,
            'client_mail_id' => $txtmail,
            'client_contact' => $txtcontact,
            'message_from_client' => $enquiry,
        ];


        $insertQuery = DB::table('new_client_request')->insert($insertArray);

        
      

	
    } 





     public function btwManagerReportToEveryOneForTest(Request $request)
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




                                             $mail = Mail::send('reports/sendMailsTest/btwManagerUserDailySale', array(
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

                                            // $cc_mail = array("hr3@btwindia.com");
                                            // $bcc_mail = array("chiefsales@btwindia.com");

                                            // $bcc_mail = array("rohit.k@manacleindia.com");
                                            // $cc_mail = array("rohit.k@manacleindia.com");


                                              


                                            $message->to($manager_mail)->subject($subject);

                                        });

                        
                    }

        ///////////////////////////////////// mails send to all asm/rsm/dgm sales ends //////////////////////////////////////



                    

            }
    

  
}
