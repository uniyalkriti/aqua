<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\UserTodaysAttendanceEnabledLog;
use App\GeofenceAttendanceLog;
use App\UserDailyAttandence;
use App\Checkout;
// use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Image;

class AttendanceApiController extends Controller
{

    public function __construct()
    {
        $this->current_menu = 'GeofenceAttandence';
    }
    #.....................function is for finding in / out status as per geofence................................# 
    public function GeofenceAttandence(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'working_date'=>'required',
            'lat' => 'required',
            'lng' => 'required',
            'time' =>'required',
        ]); // required fields.
        $lng=$request->lng;    // request for longitude which comes from decvice.........
        $lat=$request->lat;  // request for latitude  which comes from decvice.........
        $user_id =$request->user_id; // request for user_id  which comes from decvice..........
        // dd($user_id);       
        $working_date = $request->working_date; // request for working_date  which comes from decvice...
        $time = $request->time; // request for time  which comes from decvice...
        $data_id=date('ymdHis').$user_id; // for tracking purpose only
        $town_id = DB::table('monthly_tour_program')->select('town','working_status_id','_working_status.name as work_status')->join('_working_status','_working_status.id','=','monthly_tour_program.working_status_id')->where('working_date',$working_date)->where('person_id',$user_id)->first();// query to find MTP town id.......
        $town_id_att = !empty($town_id->town)?$town_id->town:'0'; // return town_id if query is empty then return 0
        $work_status = !empty($town_id->work_status)?$town_id->work_status:'N/A'; // return work_sttaus 
        $work_status_id = !empty($town_id->working_status_id)?$town_id->working_status_id:'N/A'; // return work_sttaus 
        
        $town_data = DB::table('location_6')->where('id',$town_id_att)->select('name')->first(); // query for town_name 
        $town_name = !empty($town_data->name)?$town_data->name:'NA'; // return town_name if empty the return NA.
        $reasonQuery = DB::table('_attendance_reason')->select('name','id')->get(); // return all reason for dropdown.
        $statusQuery = UserTodaysAttendanceEnabledLog::select('is_enabled','id')->whereRaw("DATE_FORMAT(created_at,'%Y-%m-%d') = '$working_date'")->where('user_id',$user_id)->orderBy('id','DESC')->first(); // query for find the status
        $status = !empty($statusQuery->is_enabled)?$statusQuery->is_enabled:0;// return status 1:enable 2:disable 0:disable
        #.............................response key discription..................................................#
           #1. response = always return true or false;
           #2. in_out_status = return 3 types 0: if lat/lng return 0, 1: return 'in', 2: return 'out', datatype:"varchar"; 
           #3. message =  return user freindly message;
           #4. town_name = return town_name from db as per town_id , table:'location_4', datatype:'varchar';
           #5. town_id = return if mtp is filled from db as per user_id , table:'monthly_tour_program', datatype:'varchar';
           #6. enable_status = return 2 type of condition from db as per user_id and date table:'user_todays_attendance_enabled_log',Types: 1:enable,2:disable,if doesnot return 1 or 2 then we also consider 0 as disable, datatype:'integer';
           #7. contact_no = return number of Administartor;
           #8. reason = return predefine reasons from db,table:'_attendance_reason';
        #...............................response key discription ends here .......................................#
        if($work_status_id == 13 || $work_status_id == 17 || $work_status_id == 12)
        {
            $my_Arr=
            [
                'user_id'=>$user_id,
                'enabled_status'=>$status,
                'town_name' =>$town_name,
                'lat' => $lat,
                'lng' =>$lng,
                'data_id'=>$data_id,
                'message' =>"As per MTP User Marked Holiday or Leave", 
                'in_out_status' => $work_status,
                'town_id' => $town_id_att,
                'created_at' => $working_date.' '.$time,
            ];
            $insertQuery=GeofenceAttendanceLog::create($my_Arr); 
            return response()->json([
                'response' => true,'in_out_status'=>$work_status, 'message' => "As per MTP User Marked Holiday or Leave", 'town_name'=>$town_name,'town_id' => $town_id_att,'enable_status'=>$status,'contact_no'=>'9015255305','reason' =>$reasonQuery
                        ]);
        }
        if(!empty($town_id))
        {
                $pathlat= DB::table('geofence')->where('location_id',$town_id_att)->pluck('lat');
                $pathlng= DB::table('geofence')->where('location_id',$town_id_att)->pluck('lng');

                if(count($pathlat)>0 && count($pathlng)>0)
                {           
                    $vertices_x = $pathlat; // plot on x-axis.........
                    $vertices_y = $pathlng; // plot on y-axis.........  
                    $points_polygon = count($vertices_x); // number vertices.........
                    $latitude_x = $lat;  // x-coordinate of the point to test.......
                    $longitude_y = $lng;  // y-coordinate of the point to test........
                  //  dd($latitude_x);
                    if ($this->is_in_polygon($points_polygon-1, $vertices_x, $vertices_y, $latitude_x, $longitude_y))
                    {
                      $ret = 'IN'; // latitude and longitude given by device are in proper geofence.
                      $my_Arr=[
                            'user_id'=>$user_id,
                            'enabled_status'=>$status,
                            'town_name' =>$town_name,
                            'lat' => $lat,
                            'lng' =>$lng,
                            'data_id'=>$data_id,
                            'message' =>"User marked attandence as per mtp", 
                            'in_out_status' => $ret,
                            'town_id' => $town_id_att,
                            'created_at' => $working_date.' '.$time,
                        ];
                        $insertQuery=GeofenceAttendanceLog::create($my_Arr); // insert all data for track each activity
                        return response()->json([
                         'response' => true,'in_out_status'=>$ret, 'message' => "User marked attandence as per mtp", 'town_name'=>$town_name,'town_id' => $town_id_att,'enable_status'=>$status,'contact_no'=>'9015255305','reason' =>$reasonQuery
                        ]);
                    } // condition ends here if in_out_status return in.
                    else
                    {
                       $ret = 'OUT'; #..latitude and longitude given by device are not in proper geofence.
                       if($status == 2)
                       {    
                            $myArr=[
                            'user_id'=>$user_id,
                            'enabled_status'=>$status,
                            'town_name' =>$town_name,
                            'lat' => $lat,
                            'lng' =>$lng,
                            'message' =>"As per MTP you are not in right place Please contact to Administartor", 
                            'in_out_status' => $ret,
                            'town_id' => $town_id_att,
                            'data_id'=>$data_id,
                            'created_at' => $working_date.' '.$time,
                        ];
                        $insertQuery=GeofenceAttendanceLog::create($myArr);
                            return response()->json([
                            'response' => false,'in_out_status'=>$ret, 'message' => "As per MTP you are not in right place Please contact to Administartor",'town_name'=>$town_name,'town_id' => $town_id_att,'enable_status'=>$status,'contact_no'=>'9015255305','reason' =>$reasonQuery,
                        ]);
                       } // condition ends here where status is 2
                       elseif($status==1)
                       {
                            $myArr1=[
                                'user_id'=>$user_id,
                                'enabled_status'=>$status,
                                'town_name' =>$town_name,
                                'lat' => $lat,
                                'lng' =>$lng,
                                'message' =>"Please select the reason", 
                                'in_out_status' => $ret,
                                'town_id' => $town_id_att,
                                'data_id'=>$data_id,
                                'created_at' => $working_date.' '.$time,
                            ];
                            $insertQuery=GeofenceAttendanceLog::create($myArr1);
                            return response()->json([
                            'response' => true, 'in_out_status'=>$ret,'message' => "Please select the reason",'town_name'=>$town_name,'town_id' => $town_id_att,'enable_status'=>$status,'contact_no'=>'9015255305','reason' =>$reasonQuery
                            ]);
                        } // condition ends here where status is 1 
                       else
                       {
                            $myArr2=[
                            'user_id'=>$user_id,
                            'enabled_status'=>$status,
                            'town_name' =>$town_name,
                            'lat' => $lat,
                            'lng' =>$lng,
                            'message' =>"As per MTP you are not in right place Please contact to Administartor", 
                            'in_out_status' => $ret,
                            'town_id' => $town_id_att,
                            'data_id'=>$data_id,
                            'created_at' => $working_date.' '.$time,
                                ];
                            $insertQuery=GeofenceAttendanceLog::create($myArr2);
                            return response()->json([
                            'response' => false,'in_out_status'=>$ret, 'message' => "As per MTP you are not in right place Please contact to Administartor",'town_name'=>$town_name,'town_id' => $town_id_att,'enable_status'=>$status,'contact_no'=>'9015255305','reason' =>$reasonQuery,
                            ]);
                        } // condition ends here where status is 0
                    } // condition ends here if in_out_status return out.
                    return $ret;
                } // path_lat / path_lng if condition ends here
                else
                {   
                    $myArr3=[
                            'user_id'=>$user_id,
                            'enabled_status'=>$status,
                            'town_name' =>$town_name,
                            'lat' => $lat,
                            'lng' =>$lng,
                            'message' =>"No records found", 
                            'in_out_status' => 'OUT',
                            'town_id' => $town_id_att,
                            'data_id'=>$data_id,
                            'created_at' => $working_date.' '.$time,
                        ];
                        $insertQuery=GeofenceAttendanceLog::create($myArr3);
                        return response()->json([
                            'response' => false,'in_out_status'=>'OUT', 'message' => "No records found",'town_name'=>$town_name,'town_id' => $town_id_att,'enable_status'=>$status,'contact_no'=>'9015255305','reason' =>$reasonQuery,
                        ]);
                }  // path_lat / path_lng else condition ends here
        } // town_id if condition ends here 
        else
        {   
            $myArr4=
                [
                    'user_id'=>$user_id,
                    'enabled_status'=>$status,
                    'town_name' =>NULL,
                    'lat' => $lat,
                    'lng' =>$lng,
                    'message' =>"town id not found !!please check you filled your mtp or not!!", 
                    'in_out_status' => 'OUT',
                    'town_id' => $town_id_att,
                    'data_id'=>$data_id,
                    'created_at' => $working_date.' '.$time,
                ];
                    $insertQuery=GeofenceAttendanceLog::create($myArr4);
                    return response()->json([
                    'response' =>false ,'in_out_status'=>'OUT', 'message' => "town id not found !!please check you filled your mtp or not!!",'town_name'=>'','town_id' => $town_id_att,'enable_status' =>$status,'contact_no'=>'9015255305','reason' =>$reasonQuery ]);
        }  // town_id else conditiion ends here   
    } // function ends here.
    #.....................function is for finding in / out status as per geofence ends here.............................#

    #.....................function is for calculate the lat/lng lies(x-axis & y-axis) on plan or not....................# 
    public function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $latitude_x, $longitude_y)
    {

        $i = $j = $c = 0;
        for ($i = 0, $j = $points_polygon ; $i <= $points_polygon; $j = $i++) {

        // ($vertices_y[$i]  >  $longitude_y != ($vertices_y[$j] > $longitude_y)));
        //echo $first = (77.32289  >  76.74073 != (76.29679 > 76.74073));
        //($latitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($longitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i])
        //echo $second =  (28.68125 < (28.87993 - 28.90398) * (76.74073 - 77.32289) / (76.29679 - 77.32289) + 28.90398);
        if ( (($vertices_y[$i]  >  $longitude_y != ($vertices_y[$j] > $longitude_y)) &&
            ($latitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($longitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i]) ) )
            {
             $c = !$c;
            }
        }
         return $c;
    }
    #..........function is for calculate the lat/lng lies(x-axis & y-axis) on plan or not ends here ...................# 

    #.....................submit attendence data in daily attendance table ................................# 
    public function attendanceSubmit(Request $request)
    {
       $validator=Validator::make($request->all(),[
            'user_id' => 'required',
            'working_date' => 'required',
            'work_status' => 'required',
            'mnc_mcc_lat_cellid' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'track_address' => 'required',
            'remarks' => 'required',
            'in_out_status' => 'required',
            'reason_id' => 'required',
            'mtp_town_id' => 'required',
            'company_id'=>'required',
            // 'from_leave'=>'required',
            // 'to_leave'=>'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $user_id = $request->user_id;
        $order_id = date('ymdHis').$user_id;
        $working_date = $request->working_date;
        $working_date_custom = date('Y-m-d',strtotime($request->working_date));
        $working_with = !empty($request->working_with)?$request->working_with:0;
        $server_date = date('Y-m-d H:i:s');
        $work_status = $request->work_status;
        $mnc_mcc_lat_cellid = $request->mnc_mcc_lat_cellid;
        $lat = $request->lat;
        $lng = $request->lng;
        $lat_lng = $lat.','.$lng;
        $track_address = $request->track_address;
        $remarks = $request->remarks;
        $in_out_status = $request->in_out_status;
        $reason_id = $request->reason_id;
        $mtp_town_id = $request->mtp_town_id;
        $company_id = $request->company_id;
        $from_leave = $request->from_leave;
        $to_leave = $request->to_leave;
        $battery_status = !empty($request->battery_status)?$request->battery_status:'0';
        $gps_status = !empty($request->gps_status)?$request->gps_status:'0';
        $imageName = null;
        if ($request->hasFile('image_source')) 
        {
            $files = $request->file('image_source');
            $inc = 0;
            foreach($files as $file)
            {
                $name_random = date('YmdHis').$inc;
                $str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
                $random_no = substr($str, 0,2);  // return always a new string 
                $custom_image_name = date('YmdHis').$random_no.$user_id;
                $imageName = $custom_image_name . '.' . $file->getClientOriginalExtension();
                $file_name[] = $imageName;
                $destinationPath = public_path('/attendance_images/');
                $file->move($destinationPath , $imageName);
                $inc++;
                
            }
            // $image = $request->file('image_source');
            // $imageName = date('YmdHis') . '.' . $image->getClientOriginalExtension();
            // $destinationPath = public_path('/attendance_images/' . $imageName);
            // Image::make($image)->save($destinationPath);
        }
        // dd($working_date_custom);
            // return response()->json([ 'response' =>TRUE,'data'=>$_POST ]);
        $check = UserDailyAttandence::where('user_id',$user_id)->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d') = '$working_date_custom'")->where('company_id',$company_id)->get();
        // dd($check);
        if(COUNT($check)>0)
        {
            return response()->json([ 'response' =>TRUE,'message'=>'Attendance Already Marked!!','code'=>'102' ]);
        }
        $myArr = 
            [
                'user_id' => $user_id,
                'order_id' => $order_id,
                'work_date' => $working_date,
                'working_with' => $working_with,
                'server_date' => $server_date,
                'work_status' => $work_status,
                'mnc_mcc_lat_cellid' => $mnc_mcc_lat_cellid,
                'lat_lng' => $lat_lng,
                'track_addrs' => $track_address,
                'remarks' => $remarks,
                'in_out_status' => $in_out_status,
                'reason_id' => $reason_id,
                'mtp_town_id' => $mtp_town_id,
                'company_id' => $company_id,
                'image_name' => $imageName,
                'leave_from_date' => $from_leave,
                'leave_to_date' => $to_leave,
            ];
        $myArr2 = 
            [
                'user_id' => $user_id,
               
                'track_date' => $working_date,
                'track_time' => date('H:i:s'),
               
                'server_date_time' => $server_date,
                'mnc_mcc_lat_cellid' => $mnc_mcc_lat_cellid,
                'lat_lng' => $lat_lng,
                'track_address' => $track_address,
                
                'status' => 'Attendance',
                
                'battery_status' => $battery_status,
                'gps_status' => $gps_status,
                'company_id' => $company_id,
                
               
            ];
        if(!empty($myArr))
        {
            $insertQuery=UserDailyAttandence::create($myArr);
            $insertQuery2=DB::table('user_work_tracking')->insert($myArr2);
            return response()->json([ 'response' =>TRUE,'message'=>'!!Successfully Attendance Marked!!','code'=>'201' ]);
        }
        else
        {
            return response()->json([ 'response' =>FALSE,'message'=>'!!Something Went Wrong Due To Some Parameters!!' ]);
        }

    }

    public function attendanceSubmitNew(Request $request)
    {
       $validator=Validator::make($request->all(),[
            'user_id' => 'required',
            'working_date' => 'required',
            'work_status' => 'required',
            'mnc_mcc_lat_cellid' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'track_address' => 'required',
            'remarks' => 'required',
            'in_out_status' => 'required',
            'reason_id' => 'required',
            'mtp_town_id' => 'required',
            'company_id'=>'required',
            // 'from_leave'=>'required',
            // 'to_leave'=>'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $user_id = $request->user_id;
        $order_id = date('ymdHis').$user_id;
        $working_date = $request->working_date;
        $working_date_custom = date('Y-m-d',strtotime($request->working_date));
        $working_with = !empty($request->working_with)?$request->working_with:0;
        $server_date = date('Y-m-d H:i:s');
        $work_status = $request->work_status;
        $mnc_mcc_lat_cellid = $request->mnc_mcc_lat_cellid;
        $lat = $request->lat;
        $lng = $request->lng;
        $lat_lng = $lat.','.$lng;
        $track_address = $request->track_address;
        $remarks = $request->remarks;
        $in_out_status = $request->in_out_status;
        $reason_id = $request->reason_id;
        $mtp_town_id = $request->mtp_town_id;
        $company_id = $request->company_id;
        $from_leave = $request->from_leave;
        $to_leave = $request->to_leave;
        $battery_status = !empty($request->battery_status)?$request->battery_status:'0';
        $gps_status = !empty($request->gps_status)?$request->gps_status:'0';
        $imageName = null;
        if ($request->hasFile('image_source')) 
        {
            $files = $request->file('image_source');
            $inc = 0;
            foreach($files as $file)
            {
                $name_random = date('YmdHis').$inc;
                $str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
                $random_no = substr($str, 0,2);  // return always a new string 
                $custom_image_name = date('YmdHis').$random_no.$user_id;
                $imageName = $custom_image_name . '.' . $file->getClientOriginalExtension();
                $file_name[] = $imageName;
                $destinationPath = public_path('/attendance_images/');
                $file->move($destinationPath , $imageName);
                $inc++;
                
            }
            // $image = $request->file('image_source');
            // $imageName = date('YmdHis') . '.' . $image->getClientOriginalExtension();
            // $destinationPath = public_path('/attendance_images/' . $imageName);
            // Image::make($image)->save($destinationPath);
        }
        // dd($working_date_custom);
            // return response()->json([ 'response' =>TRUE,'data'=>$_POST ]);
        $check = UserDailyAttandence::where('user_id',$user_id)->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d') = '$working_date_custom'")->where('company_id',$company_id)->get();
        // dd($check);
        if(COUNT($check)>0)
        {
            return response()->json([ 'response' =>FALSE,'message'=>'Attendance Already Marked!!','code'=>'102' ]);
        }
        $myArr = 
            [
                'user_id' => $user_id,
                'order_id' => $order_id,
                'work_date' => $working_date,
                'working_with' => $working_with,
                'server_date' => $server_date,
                'work_status' => $work_status,
                'mnc_mcc_lat_cellid' => $mnc_mcc_lat_cellid,
                'lat_lng' => $lat_lng,
                'track_addrs' => $track_address,
                'remarks' => $remarks,
                'in_out_status' => $in_out_status,
                'reason_id' => $reason_id,
                'mtp_town_id' => $mtp_town_id,
                'company_id' => $company_id,
                'image_name' => $imageName,
                'leave_from_date' => $from_leave,
                'leave_to_date' => $to_leave,
            ];
        $myArr2 = 
            [
                'user_id' => $user_id,
               
                'track_date' => $working_date,
                'track_time' => date('H:i:s'),
               
                'server_date_time' => $server_date,
                'mnc_mcc_lat_cellid' => $mnc_mcc_lat_cellid,
                'lat_lng' => $lat_lng,
                'track_address' => $track_address,
                
                'status' => 'Attendance',
                
                'battery_status' => $battery_status,
                'gps_status' => $gps_status,
                'company_id' => $company_id,
                
               
            ];
        if(!empty($myArr))
        {
            $insertQuery=UserDailyAttandence::create($myArr);
            $insertQuery2=DB::table('user_work_tracking')->insert($myArr2);
            return response()->json([ 'response' =>TRUE,'message'=>'!!Successfully Attendance Marked!!','code'=>'201' ]);
        }
        else
        {
            return response()->json([ 'response' =>FALSE,'message'=>'!!Something Went Wrong Due To Some Parameters!!' ]);
        }

    }
    #.....................submit attendence data in daily attendance table Ends here................................# 

    public function checkout_submit(Request $request)
    {
       $validator=Validator::make($request->all(),[
            'user_id' => 'required',
            'date_time' => 'required',
            // 'work_status' => 'required',
            'mnc_mcc_lat_cellid' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'track_address' => 'required',
            'remark' => 'required',
            'company_id'=>'required',
            'battery_status'=>'required',
            'gps_status'=>'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $user_id = $request->user_id;
        $order_id = date('ymdHis').$user_id;
        $working_date = date('Y-m-d',strtotime($request->date_time));
        $working_time = date('H:i:s',strtotime($request->date_time));
        $working_with = !empty($request->working_with)?$request->working_with:0;
        $server_date = date('Y-m-d H:i:s');
        $work_status = !empty($request->work_status)?$request->work_status:'1';
        $mnc_mcc_lat_cellid = !empty($request->mnc_mcc_lat_cellid)?$request->mnc_mcc_lat_cellid:'0:0:0:0';
        $lat = $request->lat;
        $lng = $request->lng;
        $lat_lng = $lat.','.$lng;
        $track_address = $request->track_address;
        $remarks = $request->remark;
        $total_call = !empty($request->total_call)?$request->total_call:'0';
        $total_pc = !empty($request->total_pc)?$request->total_pc:'0';
        $total_sale_value = !empty($request->total_sale_value)?$request->total_sale_value:'0';
        $battery_status = !empty($request->battery_status)?$request->battery_status:'0';
        $gps_status = !empty($request->gps_status)?$request->gps_status:'0';
        $company_id = $request->company_id;
        
        $imageName = ' ';
        if ($request->hasFile('image_source')) 
        {
            $image = $request->file('image_source');
            $imageName = date('YmdHis') . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/attendance_images/' . $imageName);
            Image::make($image)->save($destinationPath);
        }
        $check = Checkout::where('user_id',$user_id)->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d') = '$working_date'")->get();
        if(COUNT($check)>0)
        {
            return response()->json([ 'response' =>TRUE,'message'=>'Already Checkout Marked !!' ]);
        }
        $myArr = 
            [
                'user_id' => $user_id,
                'order_id' => $order_id,
                'work_date' => $request->date_time,
                'server_date_time' => $server_date,
                'work_status' => $work_status,
                'mnc_mcc_lat_cellid' => $mnc_mcc_lat_cellid,
                'lat_lng' => $lat_lng,
                'attn_address' => $track_address,
                'remarks' => $remarks,
                'total_call' => $total_call,
                'total_pc' => $total_pc,
                'total_sale_value' => $total_sale_value,
                'battery_status' => $battery_status,
                'gps_status' => $gps_status,
                'company_id' => $company_id,
                'image_name' => $imageName,
               
            ];

         $myArr2 = 
            [
                'user_id' => $user_id,
               
                'track_date' => $working_date,
                'track_time' => $working_time,
               
                'server_date_time' => $server_date,
                'mnc_mcc_lat_cellid' => $mnc_mcc_lat_cellid,
                'lat_lng' => $lat_lng,
                'track_address' => $track_address,
                
                'status' => 'CheckOut',
                
                'battery_status' => $battery_status,
                'gps_status' => $gps_status,
                'company_id' => $company_id,
                
               
            ];
        if(!empty($myArr))
        {
            $insertQuery=Checkout::create($myArr);
            $insertQuery2=DB::table('user_work_tracking')->insert($myArr2);
            return response()->json([ 'response' =>TRUE,'message'=>'!! Successfully Checkout Marked !!' ]);
        }
        else
        {
            return response()->json([ 'response' =>FALSE,'message'=>'!!Something Went Wrong Due To Some Parameters!!' ]);
        }

    }
    public function tracking_mints(Request $request)
    {
        $user_id = !empty($request->user_id)?$request->user_id:'0';
        $order_id = date('ymdHis').$user_id;
        $working_date = date('Y-m-d');
        $working_time = date('H:i:s');
        $working_with = !empty($request->working_with)?$request->working_with:0;
        $server_date = date('Y-m-d H:i:s');
        $work_status = !empty($request->work_status)?$request->work_status:'1';
        $mnc_mcc_lat_cellid = !empty($request->mnc_mcc_lat_cellid)?$request->mnc_mcc_lat_cellid:'0:0:0:0';
        $lat = $request->lat;
        $lng = $request->lng;
        $lat_lng = $lat.','.$lng;
        $track_address = $request->track_address;
        $remarks = $request->remark;
        $total_call = !empty($request->total_call)?$request->total_call:'0';
        $total_pc = !empty($request->total_pc)?$request->total_pc:'0';
        $total_sale_value = !empty($request->total_sale_value)?$request->total_sale_value:'0';
        $battery_status = !empty($request->battery_status)?$request->battery_status:'0';
        $gps_status = !empty($request->gps_status)?$request->gps_status:'0';
        $company_id = !empty($request->company_id)?$request->company_id:'90';
        $myArr2 = 
            [
                'user_id' => $user_id,
               
                'track_date' => $working_date,
                'track_time' => $working_time,
               
                'server_date_time' => $server_date,
                'mnc_mcc_lat_cellid' => $mnc_mcc_lat_cellid,
                'lat_lng' => $lat_lng,
                'track_address' => $track_address,
                
                'status' => 'tracking',
                
                'battery_status' => $battery_status,
                'gps_status' => $gps_status,
                'company_id' => $company_id,
                
               
            ];
        if(!empty($myArr2))
        {
            // $insertQuery=Checkout::create($myArr);
            $insertQuery2=DB::table('user_work_tracking_test')->insert($myArr2);
            return response()->json([ 'response' =>TRUE,'message'=>'!!Successfully Checkout Marked!!' ]);
        }
        else
        {
            return response()->json([ 'response' =>FALSE,'message'=>'!!Something Went Wrong Due To Some Parameters!!' ]);
        }
    }
    public function tracking_submit(Request $request)
    {
        $user_id = !empty($request->user_id)?$request->user_id:'0';
        $order_id = date('ymdHis').$user_id;
        $working_date = date('Y-m-d');
        $working_time = date('H:i:s');
        $working_with = !empty($request->working_with)?$request->working_with:0;
        $server_date = date('Y-m-d H:i:s');
        $work_status = !empty($request->work_status)?$request->work_status:'1';
        $mnc_mcc_lat_cellid = !empty($request->mnc_mcc_lat_cellid)?$request->mnc_mcc_lat_cellid:'0:0:0:0';
        $lat = $request->lat;
        $lng = $request->lng;
        $lat_lng = $lat.','.$lng;
        $track_address = $request->track_address;
        $remarks = $request->remark;
        $total_call = !empty($request->total_call)?$request->total_call:'0';
        $total_pc = !empty($request->total_pc)?$request->total_pc:'0';
        $total_sale_value = !empty($request->total_sale_value)?$request->total_sale_value:'0';
        $battery_status = !empty($request->battery_status)?$request->battery_status:'0';
        $gps_status = !empty($request->gps_status)?$request->gps_status:'0';
        $company_id = !empty($request->company_id)?$request->company_id:'90';
        $myArr2 = 
            [
                'user_id' => $user_id,
               
                'track_date' => $working_date,
                'track_time' => $working_time,
               
                'server_date_time' => $server_date,
                'mnc_mcc_lat_cellid' => $mnc_mcc_lat_cellid,
                'lat_lng' => $lat_lng,
                'track_address' => $track_address,
                
                'status' => 'tracking',
                
                'battery_status' => $battery_status,
                'gps_status' => $gps_status,
                'company_id' => $company_id,
                
               
            ];
        if(!empty($myArr2))
        {
            // $insertQuery=Checkout::create($myArr);
            $insertQuery2=DB::table('user_work_tracking')->insert($myArr2);
            return response()->json([ 'response' =>TRUE,'message'=>'!!Successfully Tracking Marked!!' ]);
        }
        else
        {
            return response()->json([ 'response' =>FALSE,'message'=>'!!Something Went Wrong Due To Some Parameters!!' ]);
        }
    }


     public function live_tracking_api(Request $request)
    {
        $user_id = !empty($request->user_id)?$request->user_id:'0';
        $order_id = date('ymdHis').$user_id;
        $working_date = date('Y-m-d');
        $working_time = date('H:i:s');
        $working_with = !empty($request->working_with)?$request->working_with:0;
        $server_date = date('Y-m-d H:i:s');
        $work_status = !empty($request->work_status)?$request->work_status:'1';
        $mnc_mcc_lat_cellid = !empty($request->mnc_mcc_lat_cellid)?$request->mnc_mcc_lat_cellid:'0:0:0:0';
        $lat = $request->lat;
        $lng = $request->lng;
        $lat_lng = $lat.','.$lng;
        $track_address = $request->track_address;
        $remarks = $request->remark;
        $total_call = !empty($request->total_call)?$request->total_call:'0';
        $total_pc = !empty($request->total_pc)?$request->total_pc:'0';
        $total_sale_value = !empty($request->total_sale_value)?$request->total_sale_value:'0';
        $battery_status = !empty($request->battery_status)?$request->battery_status:'0';
        $gps_status = !empty($request->gps_status)?$request->gps_status:'0';
        $company_id = !empty($request->company_id)?$request->company_id:'90';
        $myArr2 = 
            [
                'user_id' => $user_id,
               
                'track_date' => $working_date,
                'track_time' => $working_time,
               
                'server_date_time' => $server_date,
                'mnc_mcc_lat_cellid' => $mnc_mcc_lat_cellid,
                'lat_lng' => $lat_lng,
                'track_address' => $track_address,
                
                'status' => 'Live Tracking',
                
                'battery_status' => $battery_status,
                'gps_status' => $gps_status,
                'company_id' => $company_id,
                
               
            ];
        if(!empty($myArr2))
        {
            // $insertQuery=Checkout::create($myArr);
            $insertQuery2=DB::table('user_work_tracking')->insert($myArr2);
            return response()->json([ 'response' =>TRUE,'message'=>'!!Successfully Tracking Marked!!' ]);
        }
        else
        {
            return response()->json([ 'response' =>FALSE,'message'=>'!!Something Went Wrong Due To Some Parameters!!' ]);
        }
    }

    
}