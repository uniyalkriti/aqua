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
use Illuminate\Support\Facades\Session;



class ManualTourPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->current_menu = 'manualTourPlan';
        $this->current_dir  = 'manualTourPlan';
     }

    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;
    //    echo"Aao haveli pr";die;
        $state = Location3::where('company_id',$company_id)->where('status', 1)->pluck('name', 'id');
        $role  = _role::where('company_id',$company_id)->pluck('rolename','role_id');
        $user  = PersonLogin::join('person','person.id','=','person_login.person_id')->where('person.company_id',$company_id)->where('person_login.company_id',$company_id)->where('person_status','1')->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as person_fullname"),'person_id')->pluck('person_fullname','person_id');
       
        $reasonQuery = DB::table('_attendance_reason')->where('_attendance_reason.company_id',$company_id)->where('status', 1)->pluck('name', 'id');
        $user_id=$request->user;
        $date=$request->date;
        $flag=$request->flag;
        $query=  DB::table('monthly_tour_program')->where('person_id',$user_id)->where('company_id',$company_id)->whereRaw("DATE_FORMAT(monthly_tour_program.working_date, '%Y-%m-%d')='$date'")->first();
        $work=DB::table('_working_status')->where('company_id',$company_id)->pluck('name','id');

        $task_of_the_day = DB::table('_task_of_the_day')->where('company_id',$company_id)->where('status',1)->pluck('task','id');

        $town = DB::table('dealer_location_rate_list')
                ->join('location_7','location_7.id','=','dealer_location_rate_list.location_id')
                ->join('location_6','location_6.id','=','location_7.location_6_id')
                ->where('dealer_location_rate_list.company_id',$company_id)
                ->where('dealer_location_rate_list.user_id',$user_id)
                ->groupBy('location_6.id')
                ->pluck('location_6.name','location_6.id');

        if(!empty($query)){
            // Session::flash('message', 'MTP Already Filled');
            // Session::flash('class', 'success');
            //  return redirect()->intended('manualTourPlan');
            // $custom_dealer = DB::table('dealer')
            //             ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
            //             ->where('id',$query->town)
            //             ->pluck('name','id');

        }        
                

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
                'town' => $town, 
                'task_of_the_day' => $task_of_the_day, 
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

    public function submitManualTourPlan(Request $request)
    {
        $login_user=Auth::user()->id;


        $user_id = $request->user_id;
        $date = $request->date;
        $day = date('l', strtotime($date));
        $category = $request->category;
        $town = !empty($request->town)?$request->town:" ";
        $distributor = !empty($request->distributor)?$request->distributor:0;
        $beat = !empty($request->beat)?$request->beat:" ";
        $productive_call = !empty($request->productive_call)?$request->productive_call:0;
        $secondary_sales = !empty($request->secondary_sales)?$request->secondary_sales:0;
        $collection = !empty($request->collection)?$request->collection:0;
        $primary_order = !empty($request->primary_order)?$request->primary_order:0;
        $new_outlet = !empty($request->new_outlet)?$request->new_outlet:0;
        $remark = $request->remark;
        $company_id = Auth::user()->company_id;


        $insert =
            [
                'company_id'=> $company_id,
                'person_id'=>$user_id,
                'working_date'=>$date,
                'dayname'=>$day,
                'working_status_id'=>$category,
                'dealer_id'=>$distributor,
                'town'=>$town,
                'locations'=>$beat,
                'total_calls'=>0,
                'total_sales'=>0.00,
                'ss_id'=>0,
                'travel_mode' => 0,
                'mobile_save_date_time' => $date,
                'upload_date_time' => date('Y-m-d H:i:s'),
                'pc' => $productive_call,
                'rd' => $secondary_sales,
                'collection' => $collection,
                'primary_ord' => $primary_order,
                'new_outlet' => $new_outlet,
                'any_other_task' => $remark,
                'submit_from' => 2,
                'submit_by' => $login_user,
            ];
        if(!empty($insert))
        {
            $data = DB::table('monthly_tour_program')->insert($insert);
            Session::flash('message', 'MTP Successfully Saved!');
            Session::flash('class', 'success');
        }
        else{
                Session::flash('message', 'Something went wrong!');
                Session::flash('class', 'danger');
        }
        return redirect()->intended('manualTourPlan');


    }
    public function updateManualTourPlan(Request $request)
    {
        $login_user=Auth::user()->id;


        $user_id = $request->user_id;
        $date = $request->date;
        $day = date('l', strtotime($date));
        $category = $request->category;
        $town = !empty($request->town)?$request->town:" ";
        $distributor = !empty($request->distributor)?$request->distributor:0;
        $beat = !empty($request->beat)?$request->beat:" ";
        $productive_call = !empty($request->productive_call)?$request->productive_call:0;
        $secondary_sales = !empty($request->secondary_sales)?$request->secondary_sales:0;
        $collection = !empty($request->collection)?$request->collection:0;
        $primary_order = !empty($request->primary_order)?$request->primary_order:0;
        $new_outlet = !empty($request->new_outlet)?$request->new_outlet:0;
        $remark = $request->remark;
        $company_id = Auth::user()->company_id;


        $insert =
            [
                'working_status_id'=>$category,
                'dealer_id'=>$distributor,
                'town'=>$town,
                'locations'=>$beat,
                'upload_date_time' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'pc' => $productive_call,
                'rd' => $secondary_sales,
                'collection' => $collection,
                'primary_ord' => $primary_order,
                'new_outlet' => $new_outlet,
                'any_other_task' => $remark,
                'submit_from' => 2,
                'updated_by' => $login_user,
                'admin_approved'=>$request->admin_approved,
            ];
        if(!empty($insert))
        {
            $data = DB::table('monthly_tour_program')->where('id',$request->primary_id)->update($insert);
            Session::flash('message', 'MTP Update Successfully!');
            Session::flash('class', 'success');
        }
        else{
                Session::flash('message', 'Something went wrong!');
                Session::flash('class', 'danger');
        }
        return redirect()->intended('manualTourPlan');
    }
}
