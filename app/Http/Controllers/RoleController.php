<?php

namespace App\Http\Controllers;

use App\_module;
use App\_role;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;
use Auth;


class RoleController extends Controller
{
    public function __construct()
    {
        $this->menu = _module::orderBy('module_sequence')->get()->load('submenu');
        $this->current = 'role';
        $this->module=Lang::get('common.super_stockist');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 50;
        $cities = $request->city;
        $state = $request->state;
        $company_id = Auth::user()->company_id;


        #super stock data
        $query = _role::where('status', '!=', 2)->where('company_id',$company_id);


        # search functionality
        // if (!empty($request->search)) {
        //     $q = $request->search;

        //     $query->where(function ($subq) use ($q) {
        //         $subq->where('name', 'LIKE', '%' . $q . '%');
        //     });
        // }
        // # status filter enable it by setting 'status' named form-element on get request
        // if (!empty($request->status) && ($request->status == 1 || $request->status == 0 || $request->status == 2)) {
        //     $query->where('status', $request->status);
        // }
        $query->orderBy('role_sequence','ASC');

        $role = $query->paginate($pagination);

        $senior_name = _role::where('status','!=',2)->pluck('rolename','role_id');
        $class_type = DB::table('class_type')->where('status',1)->where('company_id',$company_id)->orderBy('sequence','ASC')->pluck('name','id');

        $class_type_check = DB::table('class_type_details')->where('status',1)->where('company_id',$company_id)->orderBy('sequence','ASC')->pluck('name','class_id')->toArray();


        $class_type_details = DB::table('class_type_details')->where('status',1)->where('company_id',$company_id)->orderBy('sequence','ASC')->pluck('name','id');

        // dd($class_type_check);
        // details



        return view('role.index', [
            'role' => $role,
            'senior_name' => $senior_name,
            'company_id' => $company_id,
            'menu' =>$this->menu,
            'class_type'=> $class_type,
            'class_type_check'=> $class_type_check,
            'class_type_details'=> $class_type_details,
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
        //
        $company_id = Auth::user()->company_id;
        $role = _role::where('company_id',$company_id)->pluck('rolename', 'role_id');
        $role_group = DB::table('_role_group')->where('company_id',$company_id)->pluck('group_name', 'id');
        // dd($role_group);
        return view('role.create',[
            'role'=> $role,
            'role_group'=> $role_group,
            'menu' =>$this->menu,
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

        $validatedData = $request->validate([
            'name' => 'required|max:255',
        ]);

        $company_id = Auth::user()->company_id;

        DB::beginTransaction();
        /**
         * @des: save data in role  table
         */
        $role= _role::create([
            'company_id' => $company_id,
            'rolename' => trim($request->name),
            'role_group_id' => 11,
            'senior_role_id' => trim($request->s_id),
            'role_sequence' => 1,
            'filter' => 0,
            'udr_flag' => 0,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);


        if (!$role) {
            DB::rollback();
        }
        if ($role) {
            DB::commit();
            Session::flash('message', "$this->module successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect()->intended('/role');
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
        $role_info = _role::where('company_id',$company_id)->pluck('rolename', 'role_id');
        // $role_data = _role::findOrFail('role_id',$encrypt_id);
        $role_data = _role::where('role_id',$encrypt_id)->where('company_id',$company_id)->first();
        // dd($role_data);

        $role_group = DB::table('_role_group')->pluck('group_name', 'id');

        return view(
            'role.edit',
            [
                'role_data' => $role_data,
                'role_info'=>$role_info,
                'role_group'=>$role_group,
                'encrypt_id' => $id,
                'menu' =>$this->menu,
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

        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        DB::beginTransaction();
        /**
         * @des: update   array data in vendor Table
         */
        $vendor = [
            'rolename' => trim($request->name),
            'role_group_id' => 11,
            'senior_role_id' => trim($request->s_id),
            'role_sequence' => 1,
            'filter' => 0,
            'udr_flag' => 0,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $role_data= _role::where('role_id', $uid)->where('company_id',$company_id)->update($vendor);

        if (!$role_data) {
            DB::rollback();
        }
        if ($role_data) {
            DB::commit();
            Session::flash('message', "$this->module updated successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect()->intended('role');
    }




    public function role_wise_assign(Request $request)
    {

        // $task_of_the_day = $request->task_of_the_day;
        // $working_with_type = $request->working_with_type;
        // $travel_mode = $request->travel_mode;
        // $class_type_id = $request->class_type_id;
        $ta = $request->ta;
        $da = $request->da;
        $te = $request->te;
        $role_id = $request->role_id;
        $class_type_id = $request->class_type_id;
        $da_for_class_type = $request->da_for_class_type;

        $class_type_details_id = $request->class_type_details_id;
        $da_for_class_type_details = $request->da_for_class_type_details;

        // $class_type_id_for_details_array = $request->class_type_id_for_details_array;
        $company_id = Auth::user()->company_id;

        // dd($ta);
    //     if(!empty($task_of_the_day)){
    //     foreach ($task_of_the_day as $key => $value) 
    //     {
    //         $insert_query = DB::table('role_wise_assign')
    //                     ->insert(['role_id'=>$role_id,
    //                                 'flag_status'=> 1,
    //                                 'task_of_the_day_id'=>$value,
    //                                 'working_with_type_id'=> 0,
    //                                 'travel_mode_id'=> 0,
    //                                 'class_type_id'=> 0,
    //                                 'da_for_class_type'=> 0,
    //                                 'date'=> date('Y-m-d'),
    //                                 'time'=> date('H:i:s'),
    //                                 'created_at'=>date('Y-m-d H:i:s'),
    //                                 'company_id'=> $company_id,
    //                             ]);
    //     }
    // }
    // if(!empty($working_with_type)){
    //     foreach ($working_with_type as $key => $value) 
    //     {
    //         $insert_query_2 = DB::table('role_wise_assign')
    //                     ->insert(['role_id'=>$role_id,
    //                                 'flag_status'=> 2,
    //                                 'task_of_the_day_id'=>0,
    //                                 'travel_mode_id'=> 0,
    //                                 'working_with_type_id'=>$value,
    //                                 'class_type_id'=> 0,
    //                                 'da_for_class_type'=> 0,
    //                                 'date'=> date('Y-m-d'),
    //                                 'time'=> date('H:i:s'),
    //                                 'created_at'=>date('Y-m-d H:i:s'),
    //                                 'company_id'=> $company_id,
    //                             ]);
    //     }
    // }
    // if(!empty($travel_mode)){
    //     $delete = DB::table('role_wise_assign')->where('role_id',$role_id)->where('flag_status','=',3)->delete();

    //     foreach ($travel_mode as $key => $value) 
    //     {
    //         $insert_query_3 = DB::table('role_wise_assign')
    //                     ->insert(['role_id'=>$role_id,
    //                                 'flag_status'=> 3,
    //                                 'task_of_the_day_id'=>0,
    //                                 'travel_mode_id'=> $value,
    //                                 'working_with_type_id'=>0,
    //                                 'class_type_id'=> 0,
    //                                 'da_for_class_type'=> 0,
    //                                 'date'=> date('Y-m-d'),
    //                                 'time'=> date('H:i:s'),
    //                                 'created_at'=>date('Y-m-d H:i:s'),
    //                                 'company_id'=> $company_id,
    //                             ]);
    //     }
    // }

    if(!empty($ta) || !empty($te)){
        $delete = DB::table('role_wise_assign')->where('role_id',$role_id)->where('flag_status','=',4)->delete();

     
            $insert_query_3 = DB::table('role_wise_assign')
                        ->insert(['role_id'=>$role_id,
                                    'flag_status'=> 4,
                                    'task_of_the_day_id'=>0,
                                    'travel_mode_id'=> 0,
                                    'working_with_type_id'=>0,
                                    'TA'=> $ta,
                                    'telephone_expense'=> $te,
                                    'date'=> date('Y-m-d'),
                                    'time'=> date('H:i:s'),
                                    'created_at'=>date('Y-m-d H:i:s'),
                                    'company_id'=> $company_id,
                                ]);
        
    }

    if(!empty($class_type_id)){
        $delete_class_type = DB::table('role_wise_assign')->where('role_id',$role_id)->where('flag_status','=',5)->delete();

        foreach ($class_type_id as $key => $value) 
        {
            $insert_query_3 = DB::table('role_wise_assign')
                        ->insert(['role_id'=>$role_id,
                                    'flag_status'=> 5,
                                    'task_of_the_day_id'=>0,
                                    'travel_mode_id'=> 0,
                                    'working_with_type_id'=>0,
                                    'class_type_id'=> $value,
                                    'da_for_class'=> $da_for_class_type[$key],
                                    'date'=> date('Y-m-d'),
                                    'time'=> date('H:i:s'),
                                    'created_at'=>date('Y-m-d H:i:s'),
                                    'company_id'=> $company_id,
                                ]);
        }
    }


    if(!empty($class_type_details_id)){
        $delete_class_type_details = DB::table('role_wise_assign')->where('role_id',$role_id)->where('flag_status','=',6)->delete();

        foreach ($class_type_details_id as $keyd => $valued) 
        {
            $explode = explode('|',$valued);
            $class_id = $explode[0];
            $class_details_id = $explode[1];
            $insert_query_4 = DB::table('role_wise_assign')
                        ->insert(['role_id'=>$role_id,
                                    'flag_status'=> 6,
                                    'task_of_the_day_id'=>0,
                                    'travel_mode_id'=> 0,
                                    'working_with_type_id'=>0,
                                    'class_type_id'=> $class_id,
                                    'class_type_details_id'=> $class_details_id,
                                    'da_for_class'=> $da_for_class_type_details[$keyd],
                                    'date'=> date('Y-m-d'),
                                    'time'=> date('H:i:s'),
                                    'created_at'=>date('Y-m-d H:i:s'),
                                    'company_id'=> $company_id,
                                ]);
        }
    }

        if($class_type_id || $class_type_details_id)
        {
            DB::commit();
            Session::flash('message', "$this->module Assign successfully");
            Session::flash('alert-class', 'alert-success');

        }
        else
        {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }
        return redirect()->intended('role');

    }




}
