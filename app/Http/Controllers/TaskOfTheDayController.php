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
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class TaskOfTheDayController extends Controller
{
    public function __construct()
    {
        $this->current = 'TaskOfTheDay';
        $this->module=Lang::get('common.task_of_the_day');
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

       
        $query = DB::table('_task_of_the_day')
                ->select('id','task as name','status','sequence')
                ->where('status', '!=', 9)
                ->where('company_id',$company_id);


        # search functionality
        // if (!empty($request->search)) {
        //     $q = $request->search;

        //     $query->where(function ($subq) use ($q) {
        //         $subq->where('name', 'LIKE', '%' . $q . '%');
        //     });
        // }
        # status filter enable it by setting 'status' named form-element on get request
        // if (!empty($request->status) && ($request->status == 1 || $request->status == 0 || $request->status == 2)) {
        //     $query->where('status', $request->status);
        // }
        # table sorting
        $query->orderBy('id','desc');

        $working_type = $query->get();
        // dd($working_type);
        return view('TaskOfTheDay.index', [
            'working_type' => $working_type,
            'current_menu' => $this->current
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company_id = Auth::user()->company_id;
        $working_type = DB::table('_task_of_the_day')->where('company_id',$company_id)->orderBy('_task_of_the_day.sequence','desc')->first();
        // dd($working_type);
        return view('TaskOfTheDay.create',[
            'working_type'=> $working_type,
            'current_menu' => $this->current
        ]);
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
        $validatedData = $request->validate([
            'workType' => 'required|max:50',
            'status' => 'required',
        ]);

        $company_id = Auth::user()->company_id;
        // $working_type = DB::table('_working_status')->where('company_id',$company_id)->orderBy('_working_status.sequence','desc')->first();
        // $sequence = $working_type->sequence+1;
        // $color = $working_type->color_status;

        DB::beginTransaction();
        /**
         * @des: save data in Super_Stockist table
         */
        $work_type = DB::table('_task_of_the_day')->insert([
            'task' => trim(strtoupper($request->workType)),
            'sequence' => $request->sequence,
            'company_id' => $company_id,
            'status' => trim($request->status),
            'created_at'=> date('Y-m-d H:i:s'),
            'created_by'=>Auth::user()->id,
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

        return redirect('TaskOfTheDay');
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
        $workType_info = DB::table('_task_of_the_day')->where('company_id',$company_id)->pluck('task as name', 'id');
        $workType_data = DB::table('_task_of_the_day')->where('company_id',$company_id)->where('id',$encrypt_id)->first();
        // dd($loc_data);
        return view(
            'TaskOfTheDay.edit',
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
            'task' => trim(strtoupper($request->workType)),
            'status' => trim($request->status),
            'sequence' => $request->sequence,
            'updated_at'=>date('Y-m-d H:i:s'),
            'updated_by'=>Auth::user()->id,
        ];


        $work_type_data= DB::table('_task_of_the_day')->where('id', $uid)->where('company_id',$company_id)->update($work_type);

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

        return redirect('TaskOfTheDay');
    }
}
