<?php

namespace App\Http\Controllers;

use App\Person;
use App\UserDetail;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class UserAttendanceController extends Controller
{
    public function __construct()
    {
        $this->current_menu = 'user_attendance';
    }



    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        #decrypt id
        $uid = Crypt::decryptString($id);

        $attendance_record=[];
        if (!empty($request->month))
        {
            $date=date('Y-m-d',strtotime($request->month));            
        }else{
            $date=date('Y-m-d');
        }
        $company_id = Auth::user()->company_id;
        $attendance_record=DB::table('user_daily_attendance')
                ->select(DB::raw("DATE_FORMAT(work_date,'%d-%m-%Y') AS work_date"),DB::raw("DATE_FORMAT(work_date,'%H:%i:%s') AS work_time"),'_working_status.name AS work_status','track_addrs','image_name')
                ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
                ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') ='$date' ")
                ->where('user_daily_attendance.user_id',$uid)
                ->first();

        $month=!empty($request->month)?date('Y-m',strtotime($request->month)):'';
        $from_date = date('Y').'-01-01';
        $to_date = date('Y-m-d');

        $person=Person::join('person_login','person_login.person_id','=','person.id','inner')
            ->join('_role','_role.role_id','=','person.role_id','inner')
            ->select('person.*','person_login.person_username','_role.rolename',DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as uname"))
            ->where('person.id',$uid)
            ->first();

        $data1 = UserDetail::join('person_login','person_login.person_id','=','person.id')
            ->select('person.id as person_id', 'person.emp_code as emp_code', DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as uname"), 'location_view.l1_name as zone', 'location_view.l2_name as region', '_role.rolename as role')
            ->distinct('person.id')
            ->where('person.id', '>', 1)
            ->where('person_login.person_status','=', 1)
            ->leftJoin('location_view', 'location_view.l3_id', '=', 'person.state_id')
            ->leftJoin('_role', '_role.role_id', '=', 'person.role_id');
        #User filter
        if (!empty($uid)) {
            $data1->where('person.id', $uid);
        }
        $user_record = $data1->get();


        $a = DB::table('daily_attendance_view')
            ->where('user_id',$uid);
        if (!empty($month))
        {
            $a->whereRaw("DATE_FORMAT(daily_attendance_view.work_date,'%Y-%m') ='$month' ");
        }
        else{
            $a->whereRaw("DATE_FORMAT(daily_attendance_view.work_date,'%Y-%m-%d') <='$to_date'");
        }
            $a->groupBy('work_date','user_id','track_addrs','work','check_out_date','color_status');
        $query=$a->get();
        $arr=[];
        $status=[];
        if (!empty($query))
        {
            foreach ($query as $k=>$q)
            {
                $date=!empty($q->work_date)?date('Y-m-d',strtotime($q->work_date)):0;
                $arr[$date]=$q;

                $status[$q->work][]=1;
            }
        }
//        dd($status);
        $ws=DB::table('_working_status')->where('status',1)->where('company_id',$company_id)->groupBy('id')->pluck('name','id');

       // dd($ws);

        return view('reports.userAttendance',[
            'records' => $arr,
            'working_status' => $ws,
            'users' => $user_record,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'status' => $status,
            'id' => $id,
            'person' => $person,
            'attendance_record' => $attendance_record
        ]);
    }

    public function dailyAttendanceReport(Request $request)
    {
        if ($request->ajax() && !empty($request->from_date) && !empty($request->to_date)) {

            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $data1 = UserDetail::join('person_login','person_login.person_id','=','person.id')
                ->select('person.id as person_id', 'person.emp_code as emp_code', DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as uname"), 'location_view.l1_name as zone', 'location_view.l2_name as region', '_role.rolename as role')
                ->distinct('person.id')
                ->where('person.id', '>', 1)
                ->where('person_login.person_status','=', 1)
                ->leftJoin('location_view', 'location_view.l3_id', '=', 'person.state_id')
                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id');
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data1->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $data1->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data1->whereIn('person.id', $user);
            }
            $user_record = $data1->get();

            $query = DB::table('user_att_sale_view')
                ->whereRaw("DATE_FORMAT(user_att_sale_view.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_att_sale_view.work_date,'%Y-%m-%d') <='$to_date'")
                ->groupBy('work_date','user_id','track_addrs','work','check_out_date')
                ->get();
            $arr=[];
            foreach ($query as $k=>$q)
            {
                $date=!empty($q->work_date)?date('Y-m-d',strtotime($q->work_date)):0;
                $arr[$date][$q->user_id]=$q;
            }

            return view('reports.daily-attendance.ajax', [
                'records' => $arr,
                'users' => $user_record,
                'from_date' => $from_date,
                'to_date' => $to_date
            ]);

        }
    }
}
