<?php
namespace App\Http\Controllers;


use DB;
use Image;
use App\Person;
use App\_role;
use App\Location3;
use App\Circular;
use App\SendSms;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Class CircularController extends Controller
{
	public function __construct()
	{
        $this->current_menu='circular';

	}
	public function index(Request $request)
	{
		$request_state = $request->area;
		$request_role = $request->role;
		$flag = $request->flag;
		$user_data = [];
		$company_id = Auth::user()->company_id;
		$state = Location3::where('company_id',$company_id)->where('status',1)->pluck('name','id');
		$designation = _role::where('company_id',$company_id)->where('status',1)->pluck('rolename','role_id');
		if($flag==1)
		{	
			$user_details = Person::join('person_login','person_login.person_id','=','person.id')
						->join('_role','_role.role_id','=','person.role_id')
						->join('location_6','location_6.id','=','person.town_id')
			            ->join('location_5','location_5.id','=','person.head_quater_id')
			            ->join('location_4','location_4.id','=','location_5.location_4_id')
			            ->join('location_3','person.state_id','=','location_3.id')
						->select('location_6.name as l6_name','location_5.name as l5_name','location_4.name as l4_name','location_3.name as l3_name',DB::raw("CONCAT(first_name,middle_name,last_name) as fullname"),'mobile','email','_role.rolename as designation','person.id as user_id')
						->where('person.company_id',$company_id)
						->where('person_login.person_status','=',1)
						->where('_role.role_id','!=',1);
						if(!empty($request_state))
						{
							$user_details->whereIn('person.state_id',$request_state);
						}
						
			            if(!empty($request->location_3))
				        {
				            $user_details->whereIn('location_3.id',$request->location_3);
				        }
			            if(!empty($request->location_4))
				        {
				            $user_details->whereIn('location_4.id',$request->location_4);
				        }
				        if(!empty($request->location_5))
				        {
				            $user_details->whereIn('location_5.id',$request->location_5);
				        }
			            if(!empty($request->location_6))
				        {
				            $user_details->whereIn('location_6.id',$request->location_6);
				        }
						if(!empty($request_role))
						{
							$user_details->whereIn('person.role_id',$request_role);
						}
						$user_data = $user_details->get();
		}
		return view('circular.index',[
			'role'=>$designation,
			'state'=>$state,
			'user_data'=>$user_data,
			'current_menu'=>$this->current_menu,
			]);
	} 
	public function send_sms_notification(Request $request)
	{
		$category = $request->category;
		$sms = $request->sms;
		$email = $request->email;
		$notifitext = $request->notifitext;
		$subject = $request->subject;
		$person_id = $request->person_id;
		$issued_by_person_id = Auth::user()->id;
		$company_id = Auth::user()->company_id;
		// dd($request);
		$arr = [];
		$insert_circular_data =[];
		if($category=='sms')
		{
			$title = $subject;
			$content = $sms;
		}
		elseif ($category=='email') 
		{
			$title = $subject;
			$content = $email;
		}
		else
		{
			$title = $subject;
			$content = $notifitext;
			if(!empty($request->file('notifiimage')))
			{
				$image = $request->file('notifiimage');
				$imageName = date('YmdHis') . '.' . $image->getClientOriginalExtension();
				$destinationPath = public_path('/circular_image/' . $imageName);
				Image::make($image)->save($destinationPath);
			}
			else
			{
				$imageName=NULL;
			}
			
		}
        DB::beginTransaction();
        if(!empty($person_id))
        {
        	foreach ($person_id as $key => $value) 
			{
				

				if($company_id == 52)
				{
					$arr['circular_type'] = $category;
					$arr['title'] = $title;
					$arr['content'] = $content;
					$arr['issued_by_person_id'] = $issued_by_person_id;
					$arr['company_id'] = $company_id;
					$arr['issued_time'] = date('Y-m-d H:i:s');
					$arr['circular_for_persons'] = $value;
					$arr['image'] = !empty($imageName)?$imageName:'';
					$arr['status'] = 'Publish';
					$circular_insert = Circular::create($arr);
				}
				else
				{
					$arr['circular_type'] = $category;
					$arr['title'] = $title;
					$arr['content'] = $content;
					$arr['issued_by_person_id'] = $issued_by_person_id;
					$arr['company_id'] = $company_id;
					$arr['issued_time'] = date('Y-m-d H:i:s');
					$arr['circular_for_persons'] = $value;
					$arr['image'] = !empty($imageName)?$imageName:'';
					$circular_insert = Circular::create($arr);

				}
				if($category=='email')
				{
					$email_id = DB::table('person')->where('id',$value)->first();

					$subject = $title;
					$msg = $content;
					$mailId = !empty($email_id->email)?$email_id->email:'manaclemsell1@gmail.com';
					$send_email = SendSms::sendEMAIL($msg, $mailId,$subject);

				}
				elseif($category=='notifi')
				{
					if($company_id == 52)
					{
						$user_dms_token = DB::table('person')->where('id',$value)->first();
						$fcm_token = $user_dms_token->dms_token;
		                $msg = $content;
		                $title = $subject;
		                $data = [
		                            'msg' => $msg,
		                            'body' => $msg,
		                            'title' => $title,
		                            'flag' => '3',
		                            'flag_means' => 'circular_notification',
		                            'sound' => 'mySound'/*Default sound*/

		                        ];
		                        // dd($fcm_token);
		                
		                $notification = self::sendNotificationMsell($fcm_token, $data);
		                $notification_return_details = json_decode($notification);
		                // dd($notification_return_details);
		                if($notification_return_details->success == 1)
		                {
		                    $insert_data = DB::table('dms_notification_details')
		                            ->insert([
		                                'user_id'=>$value,
		                                'dealer_id'=>0,
		                                'title'=>$title,
		                                'msg' => $msg,
		                                'body' => $msg,
		                                'flag' => '3',
		                                'flag_means' => 'circular_notification',
		                                'order_id'=> 0,
		                                'company_id'=>$company_id,
		                                'notification_status' => 1, 
		                                'created_at'=>date('Y-m-d H:i:s'),
		                            ]);
		                }
            			$dealer_dms_data  = DB::table('dealer_location_rate_list')
			                        ->join('dealer_person_login','dealer_person_login.dealer_id','=','dealer_location_rate_list.dealer_id')
			                        ->join('dealer','dealer.id','=','dealer_location_rate_list.dealer_id')
			                        ->join('person','person.id','=','dealer_location_rate_list.user_id')
			                        ->join('users','users.id','=','dealer_location_rate_list.user_id')
			                        ->select('dealer.name as dealer_name','dealer_person_login.dms_token','dealer.id as dealer_id')
			                        ->where('is_admin','!=',1)
			                        ->where('dealer_location_rate_list.company_id','=',$company_id)
			                        ->where('dealer_location_rate_list.user_id',$value)
			                        ->get();

                        foreach ($dealer_dms_data as $d_key => $d_value) 
			            {
			                $fcm_token = $d_value->dms_token;
			                $msg = $content;
			                $title = $subject;
			                $data = [
			                            'msg' => $msg,
			                            'body' => $msg,
			                            'title' => $title,
			                            'flag' => '3',
			                            'flag_means' => 'circular_notification',
			                            'sound' => 'mySound'/*Default sound*/

			                        ];
			                
			                $notification = self::sendNotificationMsell($fcm_token, $data);
			                $notification_return_details = json_decode($notification);
			                // dd($notification_return_details->success);
			                if($notification_return_details->success == 1)
			                {
			                    $insert_data = DB::table('dms_notification_details')
			                            ->insert([
			                                'user_id'=>0,
			                                'dealer_id'=>$d_value->dealer_id,
			                                'title'=>$title,
			                                'msg' => $msg,
			                                'body' => $msg,
			                                'flag' => '3',
			                                'flag_means' => 'circular_notification',
			                                'order_id'=> 0,
			                                'company_id'=>$company_id,
			                                'notification_status' => 1, 
			                                'created_at'=>date('Y-m-d H:i:s'),
			                            ]);
			                }
			            }

					}
					else // for other company
					{
						
				        $userDetails = DB::table('person')
				                        ->join('person_login','person_login.person_id','=','person.id')
				                        ->select('fcm_token',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.id as user_id','role_id')
				                        ->where('person.id',$value)
				                        ->where('person_status','=','1')
				                        ->where('person.company_id',$company_id)
				                        ->where('fcm_token','!=',NULL)
				                        // ->whereIn('role_id',$selectedRole)
				                        ->get()->toArray();

				                        // dd($userDetails);


				        foreach ($userDetails as $udkey => $udvalue) {

				            $fcm_token = $udvalue->fcm_token;

			                $data = [
			                            'msg' => $content,
			                            'body' => $content,
			                            // 'click_action' => 'fmcg.msales.MainActivity',
			                            // 'click_action' => 'sfa.solution.NewMainActivity',
			                            'title' => $subject,
			                    ];
			                $notification = $this->sendNotificationMsell($fcm_token, $data); 

			                // dd($notification);
				            
				        }
					}
				}
				// dd($circular_insert);
				if(!$circular_insert)
				{
	                DB::rollback();
	                Session::flash('message', 'Something went wrong!');
	                Session::flash('class', 'danger');
	        		return redirect()->intended($this->current_menu);
				}
				$update_person_data = DB::table('person_login')
									->where('person_id',$value)
									->update([
										'circular_id'=>$circular_insert->id,
									]);
				if(!$update_person_data)
				{
					DB::rollback();
					Session::flash('message', 'Something went wrong!');
	                Session::flash('class', 'danger');
	        		return redirect()->intended($this->current_menu);
				}
			}
			DB::commit();
			Session::flash('message', 'Success!');
	        Session::flash('class', 'success');
        }
		
        
        return redirect()->intended($this->current_menu);

	}

	#circular report starts here
    public function user_circular_report(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_id = $request->user;
        $state = $request->area;
        $region = $request->region;
        $category_type = $request->category_type;
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

        $circular_data_query = DB::table('circular')
        				->join('person','person.id','=','circular.circular_for_persons')
        				->join('_role','_role.role_id','=','person.role_id')
        				->join('location_6','location_6.id','=','person.town_id')
			            ->join('location_5','location_5.id','=','person.head_quater_id')
			            ->join('location_4','location_4.id','=','location_5.location_4_id')
			            ->join('location_3','person.state_id','=','location_3.id')
        				->select('location_6.name as l6_name','location_5.name as l5_name','location_4.name as l4_name','location_3.name as l3_name',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'circular_type','title','content','issued_by_person_id',DB::raw("DATE_FORMAT(issued_time,'%d-%m-%Y %H:%i:%s') as cdate"),'circular_for_persons','circular.status as status','image','person.person_id_senior','person.mobile','rolename')
        				->whereRaw("DATE_FORMAT(issued_time,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(issued_time,'%Y-%m-%d')<='$to_date'")
        				->where('circular.company_id',$company_id)
        				->orderBy('circular.id','DESC')
        				->groupBy('circular_for_persons','circular_type','circular.id');

		if(!empty($request->location_3))
        {
            $circular_data_query->whereIn('location_3.id',$request->location_3);
        }
        if(!empty($request->location_4))
        {
            $circular_data_query->whereIn('location_4.id',$request->location_4);
        }
        if(!empty($request->location_5))
        {
            $circular_data_query->whereIn('location_5.id',$request->location_5);
        }
        if(!empty($request->location_6))
        {
            $circular_data_query->whereIn('location_6.id',$request->location_6);
        }
        if(!empty($state))
        {
            $circular_data_query->whereIn('l3_id',$state);
        }
        if(!empty($category_type))
        {
            $circular_data_query->whereIn('circular_type',$category_type);
        }
        if(!empty($user))
        {
            $circular_data_query->whereIn('person.id',$user);
        }
        $circular_data_query_data = $circular_data_query->get();
        // dd($circular_data_query_data);
        return view('reports.circular-report.ajax', [
                "records"=>$circular_data_query_data,
                ]);

    } 
    #circular report ends  here 
    public function sendNotification($fcm_token, $data)
    {
        $url = "https://fcm.googleapis.com/fcm/send";
        $fields = array(
            'to' => $fcm_token,
            'notification' => $data,
            'data' => ['complaint_id' =>  'Test', 'notify_type' => 1], #1 for complaint notification
           

        );
        // dd(json_encode($fields));
        $headers = array(
            'Authorization: key=' . config('app.FCM_API_ACCESS_KEY'),
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
        // dd($result);
        return $result;
    }
    public function sendNotificationMsell($fcm_token,$data)
    {
      
            $url = "https://fcm.googleapis.com/fcm/send";
       //  $fields = array(
       //      'to' => $fcm_token,
       //      // 'notification' => $data,
       //      'notification' => [
	      //                       'msg' => 'msd',
	      //                       'body' => 'content',
	      //                       // 'click_action' => 'fmcg.msales.MainActivity',
	      //                       'title' => 'subject',
	      //                       "click_action" => 'sfa.solution',
	      //               	],
       // //  	'data' => [
	      // //                       'msg' => 'msd',
	      // //                       'body' => 'content',
	      // //                       // 'click_action' => 'fmcg.msales.MainActivity',
	      // //                       'title' => 'subject',
	      // //                       'click_action' => 'sfa.solution',
	      // //               	],

       // //  	'messages'=>[
    			// // 'notification' => [
	      // //                       'msg' => 'msd',
	      // //                       'body' => 'content',
	      // //                       // 'click_action' => 'fmcg.msales.MainActivity',
	      // //                       'title' => 'subject',
	      // //                       'click_action' => 'sfa.solution',
	      // //               	],
       // //  	],
       // //  	"android"=>[
		     // //   "notification"=>[
		     // //     // "icon":"stock_ticker_update",
		     // //     // "color":"#7e55c3"
	      // //                       'click_action' => 'sfa.solution',
		     // //   ]
		     // // ],
		     // // "app"=>[
		     // //   "notification"=>[
		     // //     // "icon":"stock_ticker_update",
		     // //     // "color":"#7e55c3"
	      // //                       'click_action' => 'sfa.solution',
		     // //   ]
		     // // ],

       // //      'click_action' => 'sfa.solution',
   			


       //  );

        // $data = array("to" => $fcm_token,
        //       "notification" => array( "title" => "Shareurcodes.com", "body" => "A Code Sharing Blog!","icon" => "icon.png", "click_action" => "sfa.solution"));                         				


     	// $notification = [
      //       'title' =>'title karan',
      //       'body' => 'body of message. karanm',
      //       // 'icon' =>'myIcon', 
      //       // 'sound' => 'mySound'
      //   ];

        // $extraNotificationData = ["message" => $data,"click_action" =>'sfa.solution.NotificationLayoutActivity'];
        $extraNotificationData = ["message" => $data,"click_action" =>'manacle.aqualabdms'];
        

      	$fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to'        => $fcm_token, //single token
            'notification' => $data,
            'data' => $extraNotificationData
        ];


		$data_string = json_encode($fcmNotification); 
        //   $headers = array(
        //     'Authorization: key=AAAAxjJqtKA:APA91bGHNnQHaNzwdPzOSV-G0EhtRb-AfdbfoYJVGNFG8vQyn2HLFjKUd9f34LfrYt9KeAR5L9FMK1tzNcOtbPUzTLbMuawzQLHAV_us3AOtJIxE21WBmc-qTETSdq-yUSpRu1nOs4sV',
        //     'Content-Type: application/json'
        // );


       	// $headers = array(
        //     'Authorization: key=AAAAcmM-bF0:APA91bEmNq9OEWySv8I8IfdTyeJZ8w18jWEiN5MyY2LDlKICOwy52kh921S6wNTZ9jGgSWTmtOySOPF_SyoJ0vkocJG24trvb-Fv2BtNhZO15MoRxymueZSKPcnYeMXBZdVVxQseDdiR',
        //     'Content-Type: application/json'
        // );
    	// $headers = array(
     //        'Authorization: key=AAAAObdV-Mg:APA91bHpFdfIBFjTsm5Py-AFdJ3nFsxlzcgI2wwHTjXXITPNef7u25eY7-aZrELovu_8L77hPlGhZ-uJRMjXvjWiCo9V0X0kLqLqIkBAGNQu6fiEjioM-dVh6wpWIh6AxAN1cGAvoMCe',
     //        'Content-Type: application/json'
     //    );


		$headers = array(
            'Authorization: key=AAAAdL--YQk:APA91bFf4mUXuYoM---AgjHFhebm3-Vb5Dq3B8rXHO2236YYrDosi7pNagYPncfP0EckU9nu1iXvdYydi_etlMUYv4QelWhxj9D6bi19O9FtXkv-fCnFpVwFx8LpK9dvYQefl5CEdngD',
            'Content-Type: application/json'
        );




        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $result = curl_exec($ch);
        // dd(json_encode($fields),$result);
        curl_close($ch);
        if ($result === FALSE) {
            die('Curl Failed: ' . curl_error($ch));
        }
        // echo $result;die;
        return $result;

           
    }
}