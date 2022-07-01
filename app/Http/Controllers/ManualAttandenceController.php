<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use DB;
use Auth;
use App\Location1;
use App\Location2;
use App\Location3;
use App\Location4;
use App\Location5;
use App\_role; 
use App\PersonLogin;
use App\UserDailyAttandence;
use App\JuniorData;
use Illuminate\Support\Facades\Session;



class ManualAttandenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->current_menu = 'manualAttandence';
        $this->current_dir  = 'manualAttandence';
     }

    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;
       //echo"Aao haveli pr";die;
        $state = Location3::where('company_id',$company_id)->where('status', 1)->pluck('name', 'id');
        $role  = _role::where('company_id',$company_id)->pluck('rolename','role_id');
        $user  = PersonLogin::join('person','person.id','=','person_login.person_id')->where('person.company_id',$company_id)->where('person_login.company_id',$company_id)->where('person_status','1')->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as person_fullname"),'person_id')->pluck('person_fullname','person_id');
        $town = Location4::where('location_4.status', 1)->select('location_4.id',DB::raw("CONCAT(location_4.name,' ','-','(',location_3.name,')') AS town_name"))->join('location_3','location_4.location_3_id','=','location_3.id')->where('location_4.company_id',$company_id)->where('location_3.company_id',$company_id)->get();
        $reasonQuery = DB::table('_attendance_reason')->where('_attendance_reason.company_id',$company_id)->where('status', 1)->pluck('name', 'id');
        $user_id=$request->user;
        $date=$request->date;
        $flag=$request->flag;
        $query=  UserDailyAttandence::where('user_id',$user_id)->where('company_id',$company_id)->whereRaw("DATE_FORMAT(user_daily_attendance.work_date, '%Y-%m-%d')='$date'")->first();
        $work=DB::table('_working_status')->where('company_id',$company_id)->pluck('name','id');


         Session::forget('juniordata');
        $user_data=JuniorData::getJuniorUser($user_id,$company_id);
        $junior_data = Session::get('juniordata');

          Session::forget('seniorData');
        $user_data=JuniorData::getSeniorUser($user_id,$company_id);
        $senior_data = Session::get('seniorData');

        if(!empty($senior_data) && !empty($junior_data)){
            $senior_junior = array_merge($senior_data,$junior_data);
        }elseif(!empty($senior_data) && empty($junior_data)){
            $senior_junior = $senior_data;
        }elseif(empty($senior_data) && !empty($junior_data)){
            $senior_junior = $junior_data;
        }else{
            $senior_junior = array();
        }


        $workingWithUser = DB::table('person')
                            ->join('users','users.id','=','person.id')
                            ->whereIn('person.id',$senior_junior)
                            ->where('is_admin','!=','1')
                            ->pluck(DB::raw('CONCAT_WS(" ",first_name,middle_name,last_name) as name'), 'person.id')->toArray();

        // dd($workingWithUser);
        // $checkouttime = DB::table('check_out')
        //             ->select("DATE_FORMAT(work_date, '%H:%i:%s') as ctime")
        //             ->where('user_id',$user_id)
        //             ->where('company_id',$company_id)
        //             ->whereRaw("DATE_FORMAT(check_out.work_date, '%Y-%m-%d')='$date'")
        //             ->first();

        return view($this->current_dir.'.index',
            [
                'role' => $role,
                'state' => $state,
                'user' => $user,
                'user_id'=> $user_id,
                'date' => $date,
                'work' => $work,
                'flag' => $flag,
                'query' => $query,
                'reason_display' => $reasonQuery,
                // 'checkouttime' => $checkouttime,
                'town_id' => $town, 
                'workingWithUser' => $workingWithUser, 
                'current_menu' => $this->current_menu,
            ]); 
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function submitManualAttandence(Request $request)
    {
        $work_status=$request->work;
        $work_with=$request->work_with;
        $remarks=$request->remarks;
        $att_status=2;
        $user_id=$request->user_id;
        $work_date=$request->date;
        $time=$request->time;
        $order_id = date('ymdHis').$user_id;
        // $town_id = $request->town_id;
        $town_id = !empty($request->town_id)?$mtp_town_id:'0';
        
        $reason_id = $request->reason;
        $in_out_status = "OUT";
        $company_id = Auth::user()->company_id;
        $insert =
            [
                'order_id'=> $order_id,
                'work_status'=>$work_status,
                'working_with'=>$work_with,
                'remarks'=>$remarks,
                'att_status'=>$att_status,
                'server_date'=>date('Y-m-d H:i:s'),
                'user_id'=>$user_id,
                'work_date'=>$work_date.' '.$time,
                'mnc_mcc_lat_cellid'=>0,
                'lat_lng'=>0,
                'track_addrs'=>0,
                'company_id'=>$company_id,
                'mtp_town_id' => $town_id,
                'reason_id' => $reason_id,
                'in_out_status' => $in_out_status,
            ];
        if(!empty($insert))
        {
            $dealer = UserDailyAttandence::create($insert);
            Session::flash('message', 'Successfully Attandence Marked!');
            Session::flash('class', 'success');
        }
        else{
                Session::flash('message', 'Something went wrong!');
                Session::flash('class', 'danger');
        }
        return redirect()->intended('manualAttandence');


    }
    public function updateManualAttandence(Request $request)
    {
        $work_status = $request->work;
        $remarks = $request->remarks;
        $att_status = 2;
        $user_id = $request->user_id;
        $work_date = $request->date;
        $town_id = !empty($request->town_id)?$mtp_town_id:'0';
        $reason_id = $request->reason;
        $in_out_status = "OUT";
        $company_id = Auth::user()->company_id;
        $work_with=$request->work_with;


        $checkouttime = $request->checkouttime;


        $update =
            [
                'work_status' => $work_status,
                'working_with'=>$work_with,
                'remarks' => $remarks,
                'mtp_town_id' => $town_id,
                'reason_id' => $reason_id,
                'in_out_status' => $in_out_status,
                'server_date' => date('Y-m-d H:i:s'),
            ];
        if(!empty($update))
        {
            $dealer = UserDailyAttandence::where('user_id',$user_id)->where('company_id',$company_id)->whereRaw("DATE_FORMAT(user_daily_attendance.work_date, '%Y-%m-%d')='$work_date'")->update($update);
            Session::flash('message', 'Successfully Updated!');
            Session::flash('class', 'success');
        }
        else{
                Session::flash('message', 'Something went wrong try again!');
                Session::flash('class', 'danger');
        }


        // for check out starts
        $checkCheckOut = DB::table('check_out')
                        ->where('user_id',$user_id)
                        ->where('company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(check_out.work_date, '%Y-%m-%d')='$work_date'")
                        ->count();

        if($checkCheckOut > 0 ){ // update check out time

            $updateCheckOutArray = [
                                'work_date' => $work_date.' '.$checkouttime,
                                'reason_id' => $reason_id,
                                'remarks' => $remarks,
                                'check_out_status' => '2',
                                ];
            $dealer = DB::table('check_out')->where('user_id',$user_id)->where('company_id',$company_id)->whereRaw("DATE_FORMAT(check_out.work_date, '%Y-%m-%d')='$work_date'")->update($updateCheckOutArray);
            Session::flash('message', 'Successfully Updated!');
            Session::flash('class', 'success');


        }else{ // insert check out

               $insertCheckOutArray = [
                                'company_id' => $company_id,
                                'user_id' => $user_id,
                                'work_date' => $work_date.' '.$checkouttime,
                                'server_date_time' => date('Y-m-d H:i:s'),
                                'work_status' => $work_status,
                                'user_location' => 'NULL',
                                'mnc_mcc_lat_cellid' => '0',
                                'lat_lng' => '0,0',
                                'remarks' => $remarks,
                                'attn_address' => 'NA',
                                'image_name' => '',
                                'order_id' => date('YmdHis').$user_id,
                                'total_call' => '0',
                                'total_pc' => '0',
                                'total_sale_value' => '0',
                                'total_secondary_target' => '0', 
                                'battery_status' => '0',
                                'gps_status' => '0',
                                'reason_id' => $reason_id,
                                'check_out_status' => '2'
                                ];

                $dealer = DB::table('check_out')->insert($insertCheckOutArray);

        }


        // for check out ends





        return redirect()->intended('manualAttandence');
    }
}
