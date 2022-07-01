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
use App\Circular;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Excel;



class NotificationController extends Controller
{
	


	public function checkOutNotification(Request $request)
	{
		  
        $company_id = '90';
        $selectedRole = array(386);
        $date = date('Y-m-d');

        $checkOutData = DB::table('check_out')
                        ->where('company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(check_out.work_date, '%Y-%m-%d') = '$date'")
                        ->groupBy('user_id')
                        ->pluck('user_id','user_id');


        $userDetails = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->select('fcm_token',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id as user_id','role_id')
                        ->where('person_status','=','1')
                        ->where('person.company_id',$company_id)
                        ->where('fcm_token','!=',NULL)
                        // ->where('id','7514')
                        // ->whereIn('role_id',$selectedRole)
                        ->get()->toArray();



        foreach ($userDetails as $udkey => $udvalue) {

            $fcm_token = $udvalue->fcm_token;

            $checkCheckOut = !empty($checkOutData[$udvalue->user_id])?$checkOutData[$udvalue->user_id]:'';

            if(empty($checkCheckOut)){

                $msg = 'You Have Not Checked Out Yet!!';
                $title = 'Notification';
                $data = [
                            'msg' => $msg,
                            'body' => $msg,
                            'title' => $title,
                    ];
                $notification = $this->sendNotification($fcm_token, $data); 

                

            }
            
        }

	
    } 
    

    public function oysterFollowUpNotification(Request $request)
	{
		  
        $company_id = '49';
        $date = date('Y-m-d');

        $tommorowDate = date('Y-m-d', strtotime($date . ' +1 day'));

    


        $userDetails = DB::table('meeting_order_booking')
                        ->join('person','person.id','=','meeting_order_booking.user_id')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->select('fcm_token',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id as user_id','role_id','meeting_order_booking.meet_name','meeting_order_booking.contact_no','followup_date')
                        ->where('person_status','=','1')
                        ->where('person.company_id',$company_id)
                        ->where('meeting_order_booking.company_id',$company_id)
                        ->where('person_login.company_id',$company_id)
                        // ->where('meeting_order_booking.meeting_with','=','Meeting with Dealer')
                        ->where('fcm_token','!=',NULL)
                        ->whereRaw("DATE_FORMAT(meeting_order_booking.followup_date, '%Y-%m-%d') = '$tommorowDate'")
                        ->groupBy('person.id')
                        ->get()->toArray();

                        // dd($userDetails);


        foreach ($userDetails as $udkey => $udvalue) {

            $fcm_token = $udvalue->fcm_token;

            $isAdmin = DB::table('users')->select('id as isAdmin')->where('company_id',$company_id)->where('is_admin','=','1')->first();
            $isAdminId = !empty($isAdmin->isAdmin)?$isAdmin->isAdmin:'0';


            $meet_name = $udvalue->meet_name;
            $contact_no = $udvalue->contact_no;
            $followup_date = $udvalue->followup_date."\n";

            $txt = "You Have A Meeting On ".$followup_date."With ".$meet_name;
            
          

                // $msg = 'You Have a Meeting Tommorow!!';
                $msg = $txt;


                $title = 'Notification';
                $data = [
                            'msg' => $msg,
                            'body' => $msg,
                            'title' => $title,
                    ];
                $notification = $this->sendNotification($fcm_token, $data); 



            $category = 'notifi';
            $title = 'Notification From mSELL';



            $arr['circular_type'] = $category;
            $arr['title'] = $title;
            $arr['content'] = $msg;
            $arr['issued_by_person_id'] = $isAdminId;
            $arr['company_id'] = $company_id;
            $arr['issued_time'] = date('Y-m-d H:i:s');
            $arr['circular_for_persons'] = $udvalue->user_id;
            $arr['image'] = '';
            $circular_insert = Circular::create($arr);


            $data = [
                        'msg' => $msg,
                        'body' => $msg,
                        'title' => $title,
                ];
            $notification = $this->sendNotification($fcm_token, $data); 



            
        }

	
	}



     public function commonNotification(Request $request)
    {
          
        $company_id = '40';
        $selectedRole = array(113,114,115,116);
        $date = date('Y-m-d');

        $dailyReportingData = DB::table('daily_reporting')
                        ->where('company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(daily_reporting.work_date, '%Y-%m-%d') = '$date'")
                        ->groupBy('user_id')
                        ->pluck(DB::raw("COUNT(id) as count"),'user_id');



        $marketReportOneData = DB::table('market_report_1')
                        ->where('company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(market_report_1.date, '%Y-%m-%d') = '$date'")
                        ->groupBy('created_by')
                        ->pluck(DB::raw("COUNT(id) as count"),'created_by');


        $marketReportTwoData = DB::table('market_report_2')
                        ->where('company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(market_report_2.date, '%Y-%m-%d') = '$date'")
                        ->groupBy('created_by')
                        ->pluck(DB::raw("COUNT(id) as count"),'created_by');



        $userDetails = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->select('fcm_token',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id as user_id','role_id')
                        ->where('person_status','=','1')
                        ->where('person.company_id',$company_id)
                        ->where('fcm_token','!=',NULL)
                        ->whereIn('role_id',$selectedRole)
                        ->get()->toArray();

                        // dd($userDetails);


        foreach ($userDetails as $udkey => $udvalue) {

            $fcm_token = $udvalue->fcm_token;

            $checkDailyReporting = !empty($dailyReportingData[$udvalue->user_id])?$dailyReportingData[$udvalue->user_id]:'';

            $checkMarketReportOne = !empty($marketReportOneData[$udvalue->user_id])?$marketReportOneData[$udvalue->user_id]:'';

            $checkMarketReportTwo = !empty($marketReportTwoData[$udvalue->user_id])?$marketReportTwoData[$udvalue->user_id]:'';



            if(empty($checkDailyReporting)){

                $msg = 'Please Fill Your Daily Reporting!!';
                $title = 'Notification';
                $data = [
                            'msg' => $msg,
                            'body' => $msg,
                            'title' => $title,
                    ];
                $notification = $this->sendNotification($fcm_token, $data); 

            }

             if(empty($checkMarketReportOne)){

                $msg = 'Please Fill Your Market Report One!!';
                $title = 'Notification';
                $data = [
                            'msg' => $msg,
                            'body' => $msg,
                            'title' => $title,
                    ];
                $notification = $this->sendNotification($fcm_token, $data); 

            }

               if(empty($checkMarketReportTwo)){

                $msg = 'Please Fill Your Market Report Two!!';
                $title = 'Notification';
                $data = [
                            'msg' => $msg,
                            'body' => $msg,
                            'title' => $title,
                    ];
                $notification = $this->sendNotification($fcm_token, $data); 

            }

            
        }

    
    } 


############################################## manager Notification on mSELL starts here ##############################################################


     public function managerNotification(Request $request)
    {
          
        $todaydate = date('Y-m-d');
        $date = date('Y-m-d', strtotime($todaydate . ' -1 day'));

        $getASM = DB::table('_role')
                            ->where('_role.rolename','=','ASM')
                            ->pluck('role_id')->toArray();

        $getASE = DB::table('_role')
                            ->where('_role.rolename','=','ASE')
                            ->pluck('role_id')->toArray();


        $getSO = DB::table('_role')
                            ->where('_role.rolename','=','SO')
                            ->pluck('role_id')->toArray();


        $getTSM = DB::table('_role')
                            ->where('_role.rolename','=','TSM')
                            ->pluck('role_id')->toArray();


        $finalRoleArray = array_merge($getASM,$getASE,$getSO,$getTSM);



        $userQuery = DB::table('person')
                    ->join('person_login','person_login.person_id','=','person.id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->select('person.id as manager_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as manager_name"),'person.role_id as manager_role_id','rolename as manager_role_name','person.company_id','fcm_token')
                    ->where('person_login.person_status','=','1')
                    ->where('fcm_token','!=',NULL)
                    ->whereIn('person.role_id',$finalRoleArray)
                  // ->where('person.id','=','4539') //rohit
                  // ->where('person.id','=','4537') //bhoop sir
                 //  ->where('person.id','=','2091') //dheeru
                    ->get()->toArray();




        foreach ($userQuery as $udkey => $udvalue) {

            $manager_id = $udvalue->manager_id;
            $manager_name = $udvalue->manager_name;
            $manager_role_id = $udvalue->manager_role_id;
            $manager_role_name = $udvalue->manager_role_name;
            $company_id = $udvalue->company_id;
            $fcm_token = $udvalue->fcm_token;

            $isAdmin = DB::table('users')->select('id as isAdmin')->where('company_id',$company_id)->where('is_admin','=','1')->first();
            $isAdminId = !empty($isAdmin->isAdmin)?$isAdmin->isAdmin:'0';

             if($manager_id != '0')
            {
                Session::forget('juniordata');
                $juniors_array=self::getJuniorUser($manager_id);
                Session::push('juniordata', $manager_id);
                $juniors_array = $request->session()->get('juniordata');
                if(empty($juniors_array))
                {
                    $juniors_array = array();
                }
            }
            $junior_count = count($juniors_array);

            // dd($juniors_array);

            $hlarray = array('Holiday','Leave');

            $holidayLeaveArray = DB::table('_working_status')->where('company_id',$company_id)->whereIn('name',$hlarray)->pluck('id')->toArray();



            $attendenceMarkedAsPresent = DB::table('user_daily_attendance')
                                        ->select(DB::raw("COUNT(DISTINCT user_id) as presentUser"))
                                        ->where('company_id',$company_id)
                                        ->whereIn('user_id',$juniors_array)
                                        ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date, '%Y-%m-%d') = '$date'")
                                        ->whereNotIn('user_daily_attendance.work_status', $holidayLeaveArray)
                                        ->first();



            $secondaryOrderBookedBy = DB::table('user_sales_order')
                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                        ->select(DB::raw("COUNT(DISTINCT user_sales_order.user_id) as orderBookedUser"))
                                        ->where('user_sales_order.company_id',$company_id)
                                        ->where('user_sales_order_details.company_id',$company_id)
                                        ->whereIn('user_sales_order.user_id',$juniors_array)
                                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$date'")
                                        ->first();





            $totalSecondaryCall = DB::table('user_sales_order')
                                        ->select(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalSecondaryCall"))
                                        ->where('user_sales_order.company_id',$company_id)
                                        ->whereIn('user_sales_order.user_id',$juniors_array)
                                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$date'")
                                        ->first();


            $totalSecondaryProductiveCall = DB::table('user_sales_order')
                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                        ->select(DB::raw("COUNT(DISTINCT user_sales_order.retailer_id) as totalSecondaryProductiveCall"))
                                        ->where('user_sales_order.company_id',$company_id)
                                        ->whereIn('user_sales_order.user_id',$juniors_array)
                                        ->where('call_status','=','1')
                                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$date'")
                                        ->first();



            $totalSecondarySales = DB::table('user_sales_order')
                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                        ->select(DB::raw("SUM((user_sales_order_details.rate*user_sales_order_details.quantity)+(user_sales_order_details.case_rate*user_sales_order_details.case_quantity)) as totalSecondarySales"))
                                        ->where('user_sales_order.company_id',$company_id)
                                        ->whereIn('user_sales_order.user_id',$juniors_array)
                                        ->where('call_status','=','1')
                                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$date'")
                                        ->first();


            $secondaryOrderBookedUserId = DB::table('user_sales_order')
                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                        ->where('user_sales_order.company_id',$company_id)
                                        ->where('user_sales_order_details.company_id',$company_id)
                                        ->whereIn('user_sales_order.user_id',$juniors_array)
                                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') = '$date'")
                                        ->groupBy('user_sales_order.user_id')
                                        ->pluck('user_sales_order.user_id')->toArray();

            $noBoookingUser = array_diff($juniors_array,$secondaryOrderBookedUserId);

            $noBookingUserDetails = DB::table('person')
                                    ->join('_role','_role.role_id','=','person.role_id')
                                    ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as noBookingName"),'rolename as noBookingRole','mobile as noBookMobile')
                                    ->where('person.company_id',$company_id)
                                    ->whereIn('person.id',$noBoookingUser)
                                    ->get()->toArray();


            
            $finalAttMarked = !empty($attendenceMarkedAsPresent->presentUser)?$attendenceMarkedAsPresent->presentUser:'0';
            $finalOrdBookBy = !empty($secondaryOrderBookedBy->orderBookedUser)?$secondaryOrderBookedBy->orderBookedUser:'0';
            $finalTotSecCall = !empty($totalSecondaryCall->totalSecondaryCall)?$totalSecondaryCall->totalSecondaryCall:'0';
            $finalTotSecPrCall = !empty($totalSecondaryProductiveCall->totalSecondaryProductiveCall)?$totalSecondaryProductiveCall->totalSecondaryProductiveCall:'0';
            $finalTotSecSales = !empty($totalSecondarySales->totalSecondarySales)?ROUND($totalSecondarySales->totalSecondarySales,2):'0';


            $greeting = "Good Morning, ".$manager_name."\n";
            $msg_date = "Date :".$date."\n";
            $teamTxt = "Total Team Member :".$junior_count."\n";
            $attMarTxt = "Attendance Marked As Present :".$finalAttMarked."\n";
            $secOrdBookTxt = "Secondary Order Punched By :".$finalOrdBookBy."\n";
            $secTotCallTxt = "Total Secondary Call :".$finalTotSecCall."\n";
            $secTotPrCallTxt = "Total Productive Call :".$finalTotSecPrCall."\n";
            $secSaleTxt = "Total Secondary Sale :".$finalTotSecSales."\n\n";

            $header = "Team Members Who Didn`t Booked Any Order"."\n\n";
            $output = '';
            $i = '1';

            foreach ($noBookingUserDetails as $nbkey => $nbvalue) {
                    $output .="S.No.  ".$i."\n";
                    $output .="User Name : ".$nbvalue->noBookingName."\n";
                    $output .="Designation : ".$nbvalue->noBookingRole."\n";
                    $output .="Mobile : ".$nbvalue->noBookMobile."\n";
                    $output .="\n";


                    $i++;
            }   



            $msg = $greeting.$msg_date.$teamTxt.$attMarTxt.$secOrdBookTxt.$secTotCallTxt.$secTotPrCallTxt.$secSaleTxt.$header.$output;
            $category = 'notifi';
            $title = 'Notification From mSELL';



            $arr['circular_type'] = $category;
            $arr['title'] = $title;
            $arr['content'] = $msg;
            $arr['issued_by_person_id'] = $isAdminId;
            $arr['company_id'] = $company_id;
            $arr['issued_time'] = date('Y-m-d H:i:s');
            $arr['circular_for_persons'] = $manager_id;
            $arr['image'] = '';
            $circular_insert = Circular::create($arr);


                $data = [
                            'msg' => $msg,
                            'body' => $msg,
                            'title' => $title,
                    ];
                $notification = $this->sendNotification($fcm_token, $data); 

            
        }

    
    }
   
############################################## manager Notification on mSELL ends here ##############################################################


     public function sendNotification($fcm_token,$data)
    {
      
            $url = "https://fcm.googleapis.com/fcm/send";
        $fields = array(
            'to' => $fcm_token,
            'notification' => $data,
            'data' => ['complaint_id' =>  'Test', 'notify_type' => 1], #1 for complaint notification
           

        );

        //   $headers = array(
        //     'Authorization: key=AAAAxjJqtKA:APA91bGHNnQHaNzwdPzOSV-G0EhtRb-AfdbfoYJVGNFG8vQyn2HLFjKUd9f34LfrYt9KeAR5L9FMK1tzNcOtbPUzTLbMuawzQLHAV_us3AOtJIxE21WBmc-qTETSdq-yUSpRu1nOs4sV',
        //     'Content-Type: application/json'
        // );


           $headers = array(
            'Authorization: key=AAAAObdV-Mg:APA91bHpFdfIBFjTsm5Py-AFdJ3nFsxlzcgI2wwHTjXXITPNef7u25eY7-aZrELovu_8L77hPlGhZ-uJRMjXvjWiCo9V0X0kLqLqIkBAGNQu6fiEjioM-dVh6wpWIh6AxAN1cGAvoMCe',
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
        if ($result === FALSE) {
            die('Curl Failed: ' . curl_error($ch));
        }
        // echo $result;die;
        return $result;

           
    } 
      


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
	
    // public function getJuniorUser($code)
    // {
    //     $res1="";
    //     $res2="";
    //     $details = UserDetail::where('person_id_senior',$code)
    //         ->select('id as user_id')->get();
    //         $num = count($details);  
    //         if($num>0)
    //         {
    //             foreach($details as $key=>$res2)
    //             {
    //                 if($res2->user_id!="")
    //                 {
    //                     //$product = collect([1,2,3,4]);
    //                     Session::push('juniordata', $res2->user_id);
    //                    // $_SESSION['juniordata'][]=$res2->user_id;
    //                     $this->getJuniorUser($res2->user_id);
    //                 }
    //             }
                
    //         }
    //         else
    //         {
    //             foreach($details as $key1=>$res1)
    //             {
    //                 if($res1->user_id!="")
    //                 {
    //                     Session::push('juniordata', $res1->user_id);
    //                     // $_SESSION['juniordata'][]= $res1->user_id;
    //                 }
    //             }

                
    //         }
    //         return 1;
    // } 
}
