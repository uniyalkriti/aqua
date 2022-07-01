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



class DistanceCalculateController extends Controller
{
	public $successStatus=200;
	public $salt="Rajdhani";
	public $otpString = '0123456789';

	

	public function btwDistanceCalculate(Request $request)
	{
        $company_id = array("43");
    
        $date = date('Y-m-d');


        // $user_id = '1619';
        $user_id = $_GET['user_id'];





        $checkKmIfExist = DB::table('user_distance_tracking')
                        ->whereIn('company_id',$company_id)
                        ->where('user_id',$user_id)
                        ->whereRaw("DATE_FORMAT(user_distance_tracking.track_date,'%Y-%m-%d')='$date'")
                        ->select('user_distance_tracking.*')
                        ->groupBy('user_id')
                        ->get();

        $finalDateTracking = array();
        foreach ($checkKmIfExist as $checkKmIfExistKey => $checkKmIfExistValue) {

            $id = $checkKmIfExistValue->user_id;
            $finalDateTracking[$id] = $checkKmIfExistValue;             
        }                

        // dd($finalDateTracking);

        $selectAllUsers = DB::table('person')
                        ->join('person_login','person_login.person_id','=','person.id')
                        ->whereIn('person.company_id',$company_id)
                        ->where('person_status','=','1')
                        ->where('person.id',$user_id)
                        ->groupBy('person.id')
                        ->pluck('person.id');

        $fromAttendance = DB::table('user_work_tracking')
                        ->select('user_work_tracking.id')
                        ->whereIn('company_id',$company_id)
                        ->where('user_id',$user_id)
                        ->where('status','=','Attendance')
                        ->whereRaw("DATE_FORMAT(user_work_tracking.track_date,'%Y-%m-%d')='$date'")
                        ->first();


        if(!empty($fromAttendance)){
        $finalMerge = DB::table('user_work_tracking')
                        ->select('user_work_tracking.*',DB::raw("CONCAT(track_date,' ',track_time) as submit_date_time"))
                        ->whereIn('company_id',$company_id)
                        ->where('user_id',$user_id)
                        ->whereRaw("user_work_tracking.id >= '$fromAttendance->id'")
                        ->where('status','!=','Tracking')
                        ->whereRaw("DATE_FORMAT(user_work_tracking.track_date,'%Y-%m-%d')='$date'")
                        ->groupBy('id')
                        ->orderBy('id','ASC')
                        ->get()->toArray();
        }else{
          $finalMerge = array();
        }


        // dd($finalMerge);

        foreach ($selectAllUsers as $key => $value) {
            $activeUsersId = $value;

            $latest_submit_date_time = "0000-00-00 00:00:00";
            $checkIfWeProceedFurther = !empty($finalDateTracking[$activeUsersId])?$finalDateTracking[$activeUsersId]:'';

            // dd($checkIfWeProceedFurther);

             if(!empty($checkIfWeProceedFurther)){
             

                        $latestSubmittedDateTime = strtotime($checkIfWeProceedFurther->latest_entry_submit_date_time);

                        $updatedFinalMerge=array();
                        foreach($finalMerge as $row)
                        {
                            if(strtotime($row->submit_date_time) > $latestSubmittedDateTime){
                                $updatedFinalMerge[]=$row;
                              }
                        }

                         $count = count($updatedFinalMerge);  
                          $km = '0';   

                        if(!empty($updatedFinalMerge)){
                          foreach ($updatedFinalMerge as $finalMergeKey => $finalMergeValue) {

                                                $array_submit_date_time = DB::table('user_distance_tracking')
                                                                        ->whereIn('company_id',$company_id)
                                                                        ->where('user_id',$user_id)
                                                                        ->whereRaw("DATE_FORMAT(user_distance_tracking.track_date,'%Y-%m-%d')='$date'")
                                                                        ->select('latest_entry_submit_date_time')
                                                                        ->first();


                                                 $lastArrayid = array_search($array_submit_date_time->latest_entry_submit_date_time, array_column($finalMerge, 'submit_date_time'));




                                                $lastArrayValue = $finalMerge[$lastArrayid];


                                                $lat1 = $lastArrayValue->lat_lng;

                                                $lat2 = $finalMergeValue->lat_lng;

                                                 $url1="https://maps.googleapis.com/maps/api/distancematrix/json?key=AIzaSyAteDiZiAurFBJQ0l9UrM87cz_w_z1XOBQ&origins=$lat1&destinations=$lat2&mode=car&language=fr-FR"; 

                                                 $url = preg_replace("/ /", "%20", $url1);

                                                $array=file_get_contents($url);
                                                $json=json_decode($array);
                                                $data=!empty($json->rows[0]->elements[0]->distance->value)?$json->rows[0]->elements[0]->distance->value:0;
                                                $km = $data/1000;

                                              // $latest_submit_date_time = $updatedFinalMerge[$count-1]['submit_date_time'];

                                                $latest_submit_date_time = $updatedFinalMerge[$finalMergeKey]->submit_date_time;


                                                $serverDateTime = date('Y-m-d H:i:s');

                                                $updatedKm = DB::table('user_distance_tracking')
                                                              ->whereIn('company_id',$company_id)
                                                              ->where('user_id',$activeUsersId)
                                                              ->whereRaw("DATE_FORMAT(user_distance_tracking.track_date,'%Y-%m-%d')='$date'")
                                                              ->select('total_km')
                                                              ->first();

                                                $finalKm = ROUND($updatedKm->total_km+$km,3);


                                                 $updateDistanceTracking = DB::table('user_distance_tracking')
                                                                            ->whereIn('company_id',$company_id)
                                                                            ->where('user_id',$activeUsersId)
                                                                            ->whereRaw("DATE_FORMAT(user_distance_tracking.track_date,'%Y-%m-%d')='$date'")
                                                                            ->update([
                                                                               'total_km' => $finalKm,
                                                                               'server_date_time' => $serverDateTime,
                                                                               'latest_entry_submit_date_time' => $latest_submit_date_time,
                                                                           ]);




                            }


                             // $sum =   number_format((float)$km, 3, '.', '');

                           
                        }





             }else{



                             $count = count($finalMerge);  
                  $km = '0';   
                // $km = array();      
                      if(!empty($finalMerge)){
                          foreach ($finalMerge as $finalMergeKey => $finalMergeValue) {
                                  if($finalMergeKey < $count-1){
                                              $lat1 = $finalMergeValue->lat_lng;
                                              $next_key = $finalMergeKey+1;
                                              $lat2 = $finalMerge[$next_key]->lat_lng;

                                               $url1="https://maps.googleapis.com/maps/api/distancematrix/json?key=AIzaSyAteDiZiAurFBJQ0l9UrM87cz_w_z1XOBQ&origins=$lat1&destinations=$lat2&mode=car&language=fr-FR"; 

                                               $url = preg_replace("/ /", "%20", $url1);

                                              $array=file_get_contents($url);
                                              $json=json_decode($array);
                                              $data=!empty($json->rows[0]->elements[0]->distance->value)?$json->rows[0]->elements[0]->distance->value:0;
                                              $km += $data/1000;
                                              // $km[] = $data/1000;

                                  $latest_submit_date_time = $finalMerge[$count-1]->submit_date_time;

                                  }


                          }
                        // $sum = array_sum($km);
                        $sum =   number_format((float)$km, 3, '.', '');
                        // pre($sum);die;

                        $insertArray = [
                            'user_id' => $activeUsersId,
                            'track_date' => $date,
                            'total_km' => $sum,
                            'latest_entry_submit_date_time' => $latest_submit_date_time,
                            'company_id' => 43,
                        ];

                        $insertDistanceTracking = DB::table('user_distance_tracking')
                                                    ->insert($insertArray);



                      }


             } 



        }




           

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



      public function btwManualDistanceCalculatenew(Request $request)
  {
        $company_id = array("43");
    
        $date =  $_GET['date'];

                      // dd($date);

        




       $selectAllUsers = DB::table('user_work_tracking')
                        ->whereIn('user_work_tracking.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(user_work_tracking.track_date,'%Y-%m-%d')='$date'")
                        // ->where('user_id','=','1624')
                        ->groupBy('user_id')
                        ->pluck('user_id');


        // dd($selectAllUsers);

        foreach ($selectAllUsers as $key => $value) {
            $activeUsersId = $value;

            $latest_submit_date_time = "0000-00-00 00:00:00";


         $fromAttendance = DB::table('user_work_tracking')
                    ->select('user_work_tracking.id')
                    ->whereIn('company_id',$company_id)
                    ->where('user_id',$activeUsersId)
                    ->where('status','=','Attendance')
                    ->whereRaw("DATE_FORMAT(user_work_tracking.track_date,'%Y-%m-%d')='$date'")
                    ->first();





          if(!empty($fromAttendance)){
          $finalMerge = DB::table('user_work_tracking')
                          ->select('user_work_tracking.*',DB::raw("CONCAT(track_date,' ',track_time) as submit_date_time"))
                          ->whereIn('company_id',$company_id)
                          ->where('user_id',$activeUsersId)
                          ->whereRaw("user_work_tracking.id >= '$fromAttendance->id'")
                          ->where('status','!=','Tracking')
                          ->whereRaw("DATE_FORMAT(user_work_tracking.track_date,'%Y-%m-%d')='$date'")
                          ->groupBy('id')
                          ->orderBy('id','ASC')
                          ->get()->toArray();
          }else{
            $finalMerge = array();
          }

            // dd($finalMerge);



          
                  $count = count($finalMerge);  
                  $km = '0';   
                  $sum = '0';   
                // $km = array();      
                      if(!empty($finalMerge)){
                          foreach ($finalMerge as $finalMergeKey => $finalMergeValue) {
                                  if($finalMergeKey < $count-1){
                                              $lat1 = $finalMergeValue->lat_lng;
                                              $next_key = $finalMergeKey+1;
                                              $lat2 = $finalMerge[$next_key]->lat_lng;

                                               $url1="https://maps.googleapis.com/maps/api/distancematrix/json?key=AIzaSyBJHHqymxlSqRqSNm1z6_vWkQ0YuKx3pS8&origins=$lat1&destinations=$lat2&mode=car&language=fr-FR"; 

                                               // echo $url1;die;

                                               $url = preg_replace("/ /", "%20", $url1);

                                              $array=file_get_contents($url);
                                              $json=json_decode($array);
                                              $data=!empty($json->rows[0]->elements[0]->distance->value)?$json->rows[0]->elements[0]->distance->value:0;
                                              $km += $data/1000;
                                              // $km[] = $data/1000;

                                  $latest_submit_date_time = $finalMerge[$count-1]->submit_date_time;

                                  }


                          }
                        // $sum = array_sum($km);
                        $sum =   number_format((float)$km, 3, '.', '');
                        // echo($sum);die;

                        $insertArray = [
                            'user_id' => $activeUsersId,
                            'track_date' => $date,
                            'total_km' => $sum,
                            'latest_entry_submit_date_time' => $latest_submit_date_time,
                            'company_id' => 43,
                        ];

                        $insertDistanceTracking = DB::table('user_distance_tracking')
                                                    ->insert($insertArray);

                      }



             
        }   


        echo "success";

  }




}
