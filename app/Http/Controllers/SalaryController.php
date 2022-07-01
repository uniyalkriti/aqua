<?php

namespace App\Http\Controllers;

use App\_module;
use App\Location1;
use App\Location2;
use App\Dealerowner;
use App\Outlettype;
use App\Weighttype;
use App\Travellingtype;
use App\Workingtype;
use App\Person;
use App\Salary;
use App\UserDetail;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use PDF;


class SalaryController extends Controller
{
    public function __construct()
    {
        $this->current = 'salary';
        $this->module=Lang::get('common.salary');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        $company_id = Auth::user()->company_id;
        $is_admin = Auth::user()->is_admin;
        $auth_id = Auth::user()->id;

        $month = date("Y-m",strtotime("-1 month"));

        $salary_management_data = DB::table('salary_management_log')->groupBy('user_id')->orderBy('id','DESC')->pluck('pdf_name','user_id');

        // dd($salary_management_data);
        

        $query = Salary::join('person','person.id','=','salary_management.user_id')->select('month','created_at',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'pdf_name','person.id as user_id')->where('salary_management.company_id',$company_id);



        # table sorting
        $query->orderBy('salary_management.id','desc');

        $working_type = $query->get();
        // dd($working_type);
        if($is_admin == '1')
        {
            return view('salary.index', [
                'working_type' => $working_type,
                'salary_management_data' => $salary_management_data,
                'current_menu' => $this->current
            ]);
        }
        else
        {


            $company_details = DB::table('company')->where('id',$company_id)->first();
            $user_details = Person::join('location_3','location_3.id','=','person.state_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'email','rolename','location_3.name as l3_name','emp_code','joining_date')
                        ->where('person.company_id',$company_id)
                        ->where('person.id',$auth_id)
                        ->first();
            $salary_details = Salary::where('user_id',$auth_id)->where('company_id',$company_id)->first();
            

            return view('salary.salaryView', ['salary_details' => $salary_details,'user_details' => $user_details,'company_details'=> $company_details]);
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company_id = Auth::user()->company_id;
        $user = Person::join('users','users.id','=','person.id')
            ->join('person_login','person_login.person_id','=','person.id')
            ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
            ->where('person.company_id',$company_id)
            ->where('is_admin','!=',1)
            ->where('person_status',1)
            ->pluck('name', 'uid');
        // dd($working_type);`
        return view('salary.create',[
            'user'=> $user,
            'current_menu' => $this->current
        ]);
    }

    public function salary_generation_ajax(Request $request)
    {
        $company_id = Auth::user()->company_id;
        // $curr_month = date('d',strtotime(date('Y-m-t')));
        // $days_in_month = $curr_month;
        
        $month = !empty($request->month)?$request->month:date('Y-m');

        $days_in_month = date('t',strtotime($month));

        $salary_management_data = DB::table('salary_management_log')->where('month',$month)->groupBy('user_id')->pluck('pdf_name','user_id');
        // dd($salary_management_data);

        $present_days = DB::table('user_daily_attendance')
                ->join('salary_management','salary_management.user_id','=','user_daily_attendance.user_id')
                // ->select('user_daily_attendance.*')
                ->where('user_daily_attendance.company_id',$company_id)
                ->where('salary_management.company_id',$company_id)
                ->where('user_daily_attendance.work_status','!=','48')
                ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m')='$month'")
                ->groupBy('user_daily_attendance.user_id')
                ->pluck(DB::raw("COUNT(user_daily_attendance.id) as count"),'user_daily_attendance.user_id as user_id');

        $leave_marked = DB::table('user_daily_attendance')
                ->where('company_id',$company_id)
                ->where('work_status','48')
                ->whereRaw("DATE_FORMAT(work_date,'%Y-%m')='$month'")
                ->groupBy('user_id')
                ->pluck(DB::raw("COUNT(user_daily_attendance.id) as count"),'user_id');


        $user_details = UserDetail::user_details_master($company_id);

        return view('salary.salary_generation',[
            'user_details'=> $user_details,
            'leave_marked'=>$leave_marked,
            'present_days'=>$present_days,
            'days_in_month'=>$days_in_month,
            'salary_management_data'=>$salary_management_data,
            'month'=>$month,
            'current_menu' => $this->current
        ]);
        // dd($user_details);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // save in location2
        // $validatedData = $request->validate([
        //     'workType' => 'required|max:50',
        //     'status' => 'required',
        //     'sequence' => 'required',
        // ]);
        // dd($request);
        $company_id = Auth::user()->company_id;
        $login_id = Auth::user()->id;
        $month = date('Y-m');


        DB::beginTransaction();
        /**
         * @des: save data in Super_Stockist table
         */

        $curr_month = date('d',strtotime(date('Y-m-t')));
        // dd($curr_month);

        // $explodeDate = explode(" -", $request->date_range_picker);
        // $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        // $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

        $work_type = Salary::create([
            'user_id'=>$request->user_id,
            'company_id'=> $company_id,
            'bank_name'=>$request->bank_name,
            'account_no'=>$request->account_no,
            'pan_no'=>!empty($request->pan_no)?$request->pan_no:'-',
            'uan_no'=> !empty($request->uan_no)?$request->uan_no:'-',
            'pf_no'=> !empty($request->pf_no)?$request->pf_no:'-',
            'esic_no'=> $request->esic_no,
            'from_date'=>date('Y-m-d'),
            'to_date'=>date('Y-m-d'),
            'basic_salary'=>!empty($request->basic_salary)?$request->basic_salary:'0',
            'hra_amount'=>!empty($request->hra_amount)?$request->hra_amount:'0',
            'ta'=>!empty($request->ta)?$request->ta:'0',
            'special_amount'=>!empty($request->special_amount)?$request->special_amount:'0',
            'pf_amount'=>!empty($request->pf_amount)?$request->pf_amount:'0',
            'pf_amount_employee'=>!empty($request->pf_amount_employee)?$request->pf_amount_employee:'0',
            'esic_amount'=>!empty($request->esic_amount)?$request->esic_amount:'0',
            'esic_amount_employee'=>!empty($request->esic_amount_employee)?$request->esic_amount_employee:'0',
            'month'=>$month,
            'created_by'=>$login_id,
            'created_at'=>date('Y-m-d H:i:s'),
        ]);

        

        if (!$work_type) {
            DB::rollback();
        }
        if ($work_type) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('salary');
    }

    public function process_salry_bulk(Request $request)
    {

        $user_id = $request->user_id;
        $month = $request->month;

        $days_in_month = $request->days_in_month;
        $paid_days = $request->paid_days;
        $leave_days = $request->leave_days;
        $absent_days = $request->absent_days;
        $company_id = Auth::user()->company_id;
        $login_id = Auth::user()->id;

        if(empty($user_id))
        {
             return redirect('salary');
        }
        foreach ($user_id as $key => $value) {
            # code...


            $days_in_month = $days_in_month;
            $paid_days = !empty($paid_days[$key])?$paid_days[$key]:'0';
            $leave_days = !empty($leave_days[$key])?$leave_days[$key]:'0';
            $absent_days = !empty($absent_days[$key])?$absent_days[$key]:'0';
            $t_days = $absent_days+$leave_days;

            $salary_details = Salary::where('user_id',$value)->where('company_id',$company_id)->first();


            $work_type = DB::table('salary_management_log')->insert([
                'user_id'=>$salary_details->user_id,
                'company_id'=> $company_id,
                'bank_name'=>$salary_details->bank_name,
                'account_no'=>$salary_details->account_no,
                'pan_no'=>$salary_details->pan_no,
                'uan_no'=> $salary_details->uan_no,
                'pf_no'=> $salary_details->pf_no,
                'esic_no'=> $salary_details->esic_no,
                'from_date'=>date('Y-m-d'),
                'to_date'=>date('Y-m-d'),
                'basic_salary'=>$salary_details->basic_salary,
                'hra_amount'=>$salary_details->hra_amount,
                'ta'=>$salary_details->ta,
                'special_amount'=>$salary_details->special_amount,
                'pf_amount'=>$salary_details->pf_amount,
                'pf_amount_employee'=>$salary_details->pf_amount_employee,
                'esic_amount'=>$salary_details->esic_amount,
                'esic_amount_employee'=>$salary_details->esic_amount_employee,
                'month'=>$month,
                'created_by'=>$login_id,
                'paid_days'=>!empty($paid_days)?$paid_days:'0',
                'leave_days'=>!empty($leave_days)?$leave_days:'0',
                'absent_days'=>!empty($absent_days)?$absent_days:'0',
                'created_at'=>date('Y-m-d H:i:s'),
            ]);
            $user_details = Person::join('location_3','location_3.id','=','person.state_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'email','rolename','location_3.name as l3_name','emp_code','joining_date')
                        ->where('person.company_id',$company_id)
                        ->where('person.id',$value)
                        ->first();
            // $salary_details = Salary::where('user_id',$value)->where('company_id',$company_id)->first();
            $company_details = DB::table('company')->where('id',$company_id)->first();

            $order_id = date('YmdHis').$value;
            $customPaper = array(0, 0, 1240, 1748);
            $pdf_name = $order_id.'.pdf';            

            $another_deduction = ($salary_details->basic_salary/$days_in_month)*$t_days;

            $pdf = PDF::loadView('reports/salaryPdf', ['absent_days'=>$absent_days,'another_deduction'=>$another_deduction,'salary_details' => $salary_details,'leave_days'=>$leave_days,'user_details' => $user_details,'company_details'=> $company_details,'paid_days'=>$paid_days,'days_in_month'=>$days_in_month,'month'=> $month]);
            // $pdf->setPaper($customPaper);

            $pdf->save(public_path('pdf/'.$pdf_name));
            
            $pdf_path = public_path() . '/pdf/' .$pdf_name;

            $update_first = Salary::where('user_id',$value)->where('company_id',$company_id)->where('month',$month)->update(['pdf_name'=>$pdf_name,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>$login_id]);
            $update_first = DB::table('salary_management_log')->where('user_id',$value)->where('company_id',$company_id)->where('month',$month)->update(['pdf_name'=>$pdf_name,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>$login_id]);
            $mailfirst = $user_details->email;
            // $mailId = array('karan@manacleindia.com');
            $mailId = array('hr@manacleindia.com',$mailfirst,'bhoopendranath@manacleindia.com');
            $mailMsg="Please find the attached Invoice Form $order_id";
          
            $send=Mail::raw($mailMsg, function ($message) use($mailId,$pdf_path,$mailMsg,$order_id)
            {
              foreach ($mailId as $mkey => $mail) 
              {
                $message->to($mail,$mail);
              }  
             
              $message->subject("Invoice No: $order_id || Please do not reply")
                ->attach($pdf_path);
            });
        }
             return redirect('salary');
        // return redirect('salary_generation');

    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $encrypt_id = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        $workType_info = Salary::where('company_id',$company_id)->pluck('name', 'id');
        $workType_data = Salary::where('company_id',$company_id)->where('id',$encrypt_id)->first();
        // dd($workType_data);
        return view('salary.edit',
            [
                'workType_info'=>$workType_info,
                'workType_data'=>$workType_data,
                'encrypt_id' => $id,
                'current_menu' => $this->current
            ]
        );
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
        $validatedData = $request->validate([
            'workType' => 'required|max:50',
            'status' => 'required',
            'sequence'=>'required'
        ]);

        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        DB::beginTransaction();
        /**
         * @des: update   array data in location2 Table
         */
        $work_type= [
            'name' => trim(strtoupper($request->workType)),
            'status' => trim($request->status),
            'sequence' => $request->sequence,
            'updated_at'=>date('Y-m-d H:i:s'),
            'updated_by'=>Auth::user()->id,
        ];


        $work_type_data= Salary::where('id', $uid)->where('company_id',$company_id)->update($work_type);

        if (!$work_type_data) {
            DB::rollback();
        }

        if ($work_type_data) {
            DB::commit();
            Session::flash('message', "$this->module successfully updated");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('salary');
    }
}
