<?php

namespace App\Http\Controllers;

use App\User;
use App\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LeaveRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // dd('1');
        $user = Auth::user();
        $company_id = Auth::user()->company_id;

        if(Auth::user()->is_admin=='1') {
            $leaves = Leave::join('person','person.id','=','leaves.employee_id')
                        ->join('leaver_master','leaver_master.id','=','leaves.leave_type')
                        ->select('leaves.*',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'leaver_master.name as leave_type_name')
                        ->where('leaves.company_id',$company_id)
                        ->orderBy('leaves.id','DESC')
                        ->paginate(10);
        }else{
            $leaves =  Leave::join('person','person.id','=','leaves.employee_id')
                        ->join('leaver_master','leaver_master.id','=','leaves.leave_type')
                        ->select('leaves.*',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'leaver_master.name as leave_type_name')
                        ->where('employee_id',$user->id)
                        ->orderBy('leaves.id','DESC')
                        ->paginate(10);

           
            // dd($leaves_type_data);


            $data = DB::table('leaver_master')
                ->join('leave_master_details','leave_master_details.leave_id','=','leaver_master.id')
                ->select('leaver_master.*','days_count as count')
                ->where('leaver_master.company_id',$company_id)
                ->where('year',date('Y'))
                ->where('year_month_date',date('Y-m'))
                ->where('leaver_master.status',1)
                ->groupBy('leaver_master.id')
                ->orderBy('leaver_master.id','DESC')
                ->get();

            // dd($data);
        }
//        $user = Auth::user();
        $data = array();
         $leaves_type_data = DB::table('leaves')->where('employee_id',$user->id)->groupBy('leave_type')
                                ->orderBy('leaves.id','DESC')->pluck(DB::raw('SUM(days) as days_count'),'leave_type');
                                
        return view('admin.leave.index',compact('leaves','user','data','leaves_type_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company_id = Auth::user()->company_id;
        $user = Auth::user();
        $leaves_type_data = DB::table('leaves')->where('employee_id',$user->id)->groupBy('leave_type')
                                ->pluck(DB::raw('SUM(days) as days_count'),'leave_type');
        // $data = DB::table('leaver_master')
        //         ->where('company_id',$company_id)
        //         ->where('year',date('Y'))
        //         ->where('status',1)
        //         ->groupBy('id')
        //         ->get();

            $data = DB::table('leaver_master')
                ->join('leave_master_details','leave_master_details.leave_id','=','leaver_master.id')
                ->select('leaver_master.*','days_count as count')
                ->where('leaver_master.company_id',$company_id)
                ->where('year',date('Y'))
                ->where('year_month_date',date('Y-m'))
                ->where('leaver_master.status',1)
                ->groupBy('leaver_master.id')
                ->get();
        return view('admin.leave.create',compact('data','leaves_type_data'));
    }

    public function getdays(Request $request)
    {
        $from_date =  $request->from_date;
        $to_date = $request->to_date; 
        $totalCountDays = $request->val; 
        $leaver_type = $request->leaver_type; 

        $from_date = date('Y-m',strtotime($from_date));
        $to_date = date('Y-m',strtotime($to_date));
        $year = date('Y',strtotime($from_date));
        $query_data = DB::table('leave_master_details')
                    ->select(DB::raw("SUM(days_count) as count_days"))
                    ->whereRaw("year_month_date<='$from_date'")
                    ->whereRaw("year_month_date<='$to_date'")
                    ->where('year_month_date', 'like', '%' . $year . '%')
                    ->where('status',1)
                    ->where('leave_id',$leaver_type)
                    ->first();
        $count_days = $query_data->count_days;
        // dd($count_days);
        if(!empty($count_days))
        {
            if($count_days >= $totalCountDays)
            {
                $final_count_days = $count_days - $totalCountDays ;
                $data['code'] = 200;
                $data['result'] = true;
                $data['days'] = $final_count_days;
                $data['message'] = 'success';
            }
            else
            {

                
                $final_count_days = $totalCountDays - $count_days;
                $data['code'] = 401;
                $data['result'] = '';
                // $data['days'] = $count_days;
                $data['days'] = $final_count_days;
                $data['message'] = "You can Only take ".$count_days." days leave in a month \nIn this secenario " .$count_days." days will deducted from your EL and ".$final_count_days." days salary will be deducted\nDo You want to proceed.";
                // $data['again_popup'] = 'You can Only take'.$count_days.' days';
            }
        }
        else
        {
            $data['code'] = 801;
            $data['message'] = "You Don't Have any leave in your bucket yet, So In This Secenario ".$totalCountDays." days salary will be deducted \nDo you Want to Proceed ";
            // $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LeaveRequest $request)
    {
        Leave::create([
            'employee_id'   => Auth::id(),
            'leave_type'    => $request->leave_type,
            'date_from'     => $request->date_from,
            'date_to'       => $request->date_to,
            'days'          => $request->days,
            'reason'        => $request->reason,
        ]);

        // Toastr::success('Leave successfully requested to HR!','Success');

        return redirect()->route('leave');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
//        dd($request->all());
           // $leave = $request -> get('search');
            $leaves =Leave::join('person','person.id','=','leaves.employee_id')->where('leave_type', 'LIKE',"%{$request->search}%")->paginate();
            return view('admin.leave.index',compact('leaves'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function edit(Leave $leave)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Leave $leave)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */

    public function approve(Request $request,$id)
    {

      //  dd($request->all());
        $leave = Leave::find($id);
//        dd($leave);
       if($leave){
           $leave->is_approved = $request -> approve;
           $leave->save();
           return redirect()->back();
       }
    }

    public function paid(Request $request,$id)
    {
        $leave = Leave::find($id);
        if($leave){
            $leave->leave_type_offer = $request -> paid;
            $leave->save();
            return redirect()->back();
        }
    }
}
