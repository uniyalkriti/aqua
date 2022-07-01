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


class UserMtpController extends Controller
{
    public function __construct()
    {
        $this->current_menu = 'mtp';
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

        $mtp_record=[];
        if (!empty($request->month))
        {
            $date=date('Y-m-d',strtotime($request->month));            
        }else{
            $date=date('Y-m-d');
        }
        $mtp_record=DB::table('monthly_tour_program')
              ->leftJoin('dealer','dealer.id','=','monthly_tour_program.dealer_id')
              ->leftJoin('location_5','monthly_tour_program.locations','=','location_5.id')
              ->join('_task_of_the_day','monthly_tour_program.working_status_id','=','_task_of_the_day.id')
             ->select('monthly_tour_program.*','dealer.name AS dealer_name','location_5.name AS beat','_task_of_the_day.task AS working_status')
                ->whereRaw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d') ='$date' ")
                ->where('monthly_tour_program.person_id',$uid)
                ->first();

        $month=!empty($request->month)?date('Y-m',strtotime($request->month)):'';
        $prev_year=date('Y')-1;
        $from_date = $prev_year.'-01-01';
        $to_date = date('Y-m').'-31';

        $person=Person::join('person_login','person_login.person_id','=','person.id','inner')
            ->join('_role','_role.role_id','=','person.role_id','inner')
            ->select('person.*','person_login.person_username','person_login.person_image','_role.rolename')
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


        $a = DB::table('monthly_tour_program')
              ->leftJoin('dealer','dealer.id','=','monthly_tour_program.dealer_id')
              ->leftJoin('location_5','monthly_tour_program.locations','=','location_5.id')
              ->join('_task_of_the_day','monthly_tour_program.working_status_id','=','_task_of_the_day.id')
             ->select('monthly_tour_program.*','dealer.name AS dealer_name','location_5.name AS beat','_task_of_the_day.task AS working_status')
            ->where('person_id',$uid);
        if (!empty($month))
        {
            $a->whereRaw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m') ='$month' ");
        }
        else{
            $a->whereRaw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d') <='$to_date'");
        }
            //$a->groupBy('work_date','user_id','track_addrs','work','check_out_date','color_status');
        $query=$a->get();
        $arr=[];
        $status=[];
        if (!empty($query))
        {
            foreach ($query as $k=>$q)
            {
                $date=!empty($q->working_date)?date('Y-m-d',strtotime($q->working_date)):0;
                $arr[$date]=$q;

                $status[$q->working_status_id][]=1;
            }
        }
//        dd($status);
        $ws=DB::table('_working_status')->where('id','!=',9)->pluck('name','color_status');

       //dd($arr);

        return view('reports.userMtp',[
            'records' => $arr,
            'working_status' => $ws,
            'users' => $user_record,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'status' => $status,
            'id' => $id,
            'person' => $person,
            'mtp_record' => $mtp_record
        ]);
    }
}
