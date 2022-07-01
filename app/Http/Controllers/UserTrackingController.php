<?php

namespace App\Http\Controllers;


use App\User;
use App\UserDetail;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class UserTrackingController extends Controller
{
    public function __construct()
    {
        $this->current_menu = 'user_tracking';
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $route=[];
        #decrypt id
        $uid = Crypt::decryptString($id);
        // dd($uid);
        $user=UserDetail::find($uid);
        // dd($user);
        $date=!empty($request->date)?$request->date:date('Y-m-d');
        // dd($date);
        $company_id = Auth::user()->company_id; 
        $order_id = $request->order_id;
        $arr=[];

        if(!empty($order_id))
        {
            #############order booking###########
            $sales_data = DB::table('user_sales_order')
            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$date'")
            ->where('user_id',$uid)
            ->where('company_id',$company_id)
            ->whereRaw("lat_lng!='0.0 0.0' AND lat_lng!='0.0,0.0' AND lat_lng!=' ' AND lat_lng!=''" );
            if(!empty($order_id))
            {
                $sales_data->where('order_id',$order_id);
            }
            $sales = $sales_data->get();
            if (!empty($sales))
            {
                foreach ($sales as $datas)
                {
                    
                    $route[]=$datas->track_address;
                    $totaData[] = $datas;
                    $latlng=str_replace(' ',',',$datas->lat_lng);
                     $arr[]=$latlng.",".$datas->date." ".$datas->time.",Order".",".$datas->battery_status.",".$datas->time.",".$datas->gps_status;

                }
            }
            $retailer_data = DB::table('user_sales_order')
            ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
            ->select('retailer.track_address as track_address','lat_long','retailer.battery_status as battery_status','retailer.gps_status as gps_status','created_on',DB::raw("DATE_FORMAT(created_on,'%H:%i:%s') as time"))
            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$date'")
            ->where('user_id',$uid)
            ->where('user_sales_order.company_id',$company_id)
            ->where('retailer.company_id',$company_id)
            ->whereRaw("lat_long!='0.0 0.0' AND lat_long!='0.0,0.0' AND lat_long!=' ' AND lat_long!=''" );
            if(!empty($order_id))
            {
                $retailer_data->where('order_id',$order_id);
            }
            $retailer_data_d = $retailer_data->get();
            if (!empty($retailer_data_d))
            {
                foreach ($retailer_data_d as $datas)
                {
                    
                    $route[]=$datas->track_address;
                    $totaData[] = $datas;
                    $latlng=str_replace(' ',',',$datas->lat_long);
                    $lat_lng1 = explode(',', $datas->lat_long);
                    $lat = $lat_lng1[0];
                    $lng = $lat_lng1[1];
                    if( $lng!=1 && $lng!=0 && $lng!=0.0)
                    {
                        // dd($lng);
                        $arr[]=$lat.",".$lng.",".$datas->created_on.",Outlet".",".$datas->battery_status.",".$datas->time.",".$datas->gps_status;

                    }

                }
            }
            $way= !empty($arr)?json_encode($arr):'';
            // dd($way);
            return view('reports.userTracking',[
                'attendance' => '',
                'checkout' => '',
                'checkout2' => '',
                'start' => '',
                'last' => '',
                'way' => $way,
                'records' => '',
                'track' => '',
                'sales' => $sales,
                'outlet' => '',
                'user' => '',
                'id' => $id,
                'totaData'=>'',
                'routes' => '',
                'daily_reporting'=>'',
                'flag'=>'1', // only for hide rest of the count block on panel only show toatl distance count bloack

            ]);

        }
        else
        {
            #attendance for starting point
            $totaData = [];
            $track=0;
            $attendance=DB::table('user_daily_attendance')
                ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') ='$date'")
                ->where('user_id',$uid)
                ->where('company_id',$company_id)
                ->take(1)->get()->toArray();
                $start=0;
            if (!empty($attendance->track_addrs))
            {
                $route[]=$attendance->track_addrs;
                $totaData[] = $attendance;

                $start=1;
            }

            $query = DB::table('user_daily_tracking')
                ->whereRaw("DATE_FORMAT(user_daily_tracking.track_date,'%Y-%m-%d') ='$date'")
                ->where('user_id',$uid)
                ->where('company_id',$company_id)
                ->get();
            
            
            // if (!empty($query))
            // {
            //     foreach ($query as $data)
            //     {
            //         if ($data->lat_lng!='0.0,0.0')
            //         {
            //             $arr[]=['location'=>$data->lat_lng];
            //            $arr[]=$data->lat_lng.",".$data->track_date." ".$data->track_time.",tracking_module".$data->battery_status.",".$data->track_time.",".$data->gps_status;
            //             $totaData[] = $data;

            //             $route[]=$data->track_address;
            //             $track++;
            //         }
            //     }

            // }

             $checkout2 = DB::table('user_daily_tracking')
                ->whereRaw("DATE_FORMAT(user_daily_tracking.track_date,'%Y-%m-%d') ='$date'")
                ->whereRaw("lat_lng!='0.0 0.0' AND lat_lng!='0.0,0.0' AND lat_lng!=' ' AND lat_lng!=''" )
                ->where('company_id',$company_id)
                ->where('user_id',$uid)
                ->orderBy('id','DESC')
                ->take(1)->get()->toArray();

               
               
                #############order booking###########
                $sales_data = DB::table('user_sales_order')
                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$date'")
                ->where('company_id',$company_id)
                ->where('user_id',$uid)
                ->whereRaw("lat_lng!='0.0 0.0' AND lat_lng!='0.0,0.0' AND lat_lng!=' ' AND lat_lng!=''" );
                if(!empty($order_id))
                {
                 
                    $sales_data->where('order_id',$order_id);
                }
                $sales = $sales_data->get();
                // dd($sales);
                if (!empty($sales))
                {
                    foreach ($sales as $datas)
                    {
                        
                    $route[]=$datas->track_address;
                      $totaData[] = $datas;
                      $latlng=str_replace('_',',',$datas->lat_lng);
                   $arr[$datas->date." ".$datas->time]=$latlng.",".$datas->date." ".$datas->time.",Order".",".$datas->battery_status.",".$datas->time.",".$datas->gps_status;


                }
            }
      
            #############outlet creation#############
            
            $outlet = DB::table('retailer')
            ->select('retailer.track_address as track_address','lat_long','retailer.battery_status as battery_status','retailer.gps_status as gps_status','created_on',DB::raw("DATE_FORMAT(created_on,'%H:%i:%s') as time"))
            ->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')='$date'")
            ->where('company_id',$company_id)
            ->where('created_by_person_id',$uid)
            ->whereRaw("lat_long!='0.0 0.0' AND lat_long!='0.0,0.0' AND lat_long!=' ' AND lat_long!='' AND lat_long!='0.0,1' AND lat_long!=',%0%'" )
            ->get()->take(18);

            if (!empty($outlet))
            {
                foreach ($outlet as $creation)
                {
                   
                    $route[]=$creation->track_address;
                    $totaData[] = $creation;
                    $latlng=str_replace(' ',',',$creation->lat_long);
                    $lat_lng1 = explode(',', $creation->lat_long);
                    $lat = $lat_lng1[0];
                    $lng = $lat_lng1[1];
                    if( $lng!=1 && $lng!=0 && $lng!=0.0)
                    {
                        // dd($lng);
                        $arr[$creation->created_on]=$lat.",".$lng.",".$creation->created_on.",Outlet".",".$creation->battery_status.",".$creation->time.",".$creation->gps_status;
                    }
                    
                    
                }
            }
            $daily_reporting = DB::table('daily_reporting')
            ->select('daily_reporting.attn_address as track_address','lat_lng','work_date',DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') as time"))
            ->whereRaw("DATE_FORMAT(daily_reporting.work_date,'%Y-%m-%d')='$date'")
            ->where('company_id',$company_id)
            ->where('user_id',$uid)
            ->whereRaw("lat_lng!='0.0 0.0' AND lat_lng!='0.0,0.0' AND lat_lng!=' ' AND lat_lng!='' AND lat_lng!='0.0,1' AND lat_lng!=',%0%'" )
            ->get();

            if (!empty($daily_reporting))
            {
                foreach ($daily_reporting as $creation)
                {
                   
                    $route[]=$creation->track_address;
                    $totaData[] = $creation;
                    $latlng=str_replace(' ',',',$creation->lat_lng);
                    $lat_lng1 = explode(',', $creation->lat_lng);
                    $lat = $lat_lng1[0];
                    $lng = $lat_lng1[1];
                    if( $lng!=1 && $lng!=0 && $lng!=0.0)
                    {
                        // dd($lng);
                        $arr[$creation->work_date]=$lat.",".$lng.",".$creation->work_date.",Daily Reporting".",".'0'.",".$creation->time.",".'0';
                    }
                    
                    
                }
            }
            // $outlet = [];

            #checkout for ending point
            $checkout=DB::table('check_out')
                ->where('company_id',$company_id)
                ->whereRaw("DATE_FORMAT(check_out.work_date,'%Y-%m-%d') ='$date'")
                ->whereRaw("lat_lng!='0.0 0.0' AND lat_lng!='0.0,0.0' AND lat_lng!=' ' AND lat_lng!=''" )
                ->where('user_id',$uid)
                ->take(1)->get()->toArray();
                // ->first();
            if (!empty($checkout->attn_address))
            {
                $route[]=$checkout->attn_address;
                $totaData[] = $checkout;

               // $arr[]=$checkout->lat_lng.",".$checkout->work_date.",5";
                $last=1;
            }else{
                if (!empty($checkout2->track_address))
            {
                $route[]=$checkout2->track_address;
            }
                $last=0;
            }

             #Work Track
             $user_work_tracking=DB::table('user_work_tracking')
             ->whereRaw("DATE_FORMAT(user_work_tracking.track_date,'%Y-%m-%d') ='$date'")
             ->whereRaw("lat_lng!='0.0 0.0' AND lat_lng!='0.0,0.0' AND lat_lng!=' ' AND lat_lng!=''" )
             ->where('user_id',$uid)
             ->where('company_id',$company_id)
             ->where('status','!=','Daily Reporting')
             ->where('status','!=','Daily Reporting')
             ->where('status','!=','Primary Booking')
             ->where('status','!=','Counter Sale')
             ->where('status','!=','Dealer Balance Stock')
             ->where('status','!=','Retailer Creation')
             ->where('status','!=','Tracking')
             ->get();
            if (!empty($user_work_tracking))
            {
                foreach ($user_work_tracking as $dataRow)
                {
                    $latlng=str_replace(' ',',',$dataRow->lat_lng);
                    $stime = $dataRow->track_date." ".$dataRow->track_time;
                    $arr[$dataRow->track_date." ".$dataRow->track_time]=$latlng.",".$stime.",".$dataRow->status.",".$dataRow->battery_status.",".$dataRow->track_time.",".$dataRow->gps_status;

                }
            }
            ksort($arr);
            // $temp
            // dd($arr);
            $temp = array();      
            foreach ($arr as $key => $value) 
            {
                $temp[] = $value; 
            }


            $user_work_tracking_array = array();
            foreach ($temp as $key => $value) 
            {
                $ex = explode(',',$value);
                $unix_timew = strtotime($ex[5]);
                $startTimew = $unix_timew;

                if($key == 0){
                $user_work_tracking_array[] = $value;
                $plusthirtytimew = strtotime(date('H:i:s',strtotime('+45 minutes',$unix_timew)));
                }
                
                if($plusthirtytimew <= $startTimew){
                $user_work_tracking_array[] = $value;
                $plusthirtytimew = strtotime(date('H:i:s',strtotime('+45 minutes',$unix_timew)));
                }


            }
            // $tempArr[] = $user_work_tracking_array; 


            // dd($tempArr);
            // $way= !empty($temp)?json_encode($temp):'';
            $way= !empty($user_work_tracking_array)?json_encode($user_work_tracking_array):'';

            // dd($way);
            // dd($attendance);

            return view('reports.userTracking',[
                'attendance' => $attendance,
                'checkout' => $checkout,
                'checkout2' => $checkout2,
                'start' => $start,
                'last' => $last,
                'way' => $way,
                'records' => $query,
                'track' => $track,
                'sales' => $sales,
                'outlet' => $outlet,
                'user' => $user,
                'id' => $id,
                'totaData'=>$totaData,
                'routes' => $route,
                'daily_reporting'=> $daily_reporting,
                'flag'=>'0',//user for show all count block on tracking module panel

            ]);

        }
        
    }
}
