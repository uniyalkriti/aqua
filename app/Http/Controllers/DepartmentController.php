<?php

namespace App\Http\Controllers;

use App\_department;
use App\_module;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->menu = _module::orderBy('module_sequence')->get()->load('submenu');
        $this->current = 'MASTERS';
        $this->module=Lang::get('common.department');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        $cities = $request->city;
        $state = $request->state;


        #Department data
        $query = _department::where('status', '!=', 2);


        # search functionality
        if (!empty($request->search)) {
            $q = $request->search;

            $query->where(function ($subq) use ($q) {
                $subq->where('name', 'LIKE', '%' . $q . '%');
            });
        }
        # status filter enable it by setting 'status' named form-element on get request
        if (!empty($request->status) && ($request->status == 1 || $request->status == 0 || $request->status == 2)) {
            $query->where('status', $request->status);
        }
        # table sorting
        if (($request->otype == 'asc' || $request->otype == 'desc') && ($request->oby == 'name' || $request->oby == 'department' || $request->oby == 'role' || $request->oby == 'status')) {
            $order = $request->oby;

        }
        $query->orderBy('created_at','desc');

        $department = $query->paginate($pagination);

        return view('department.index', [
            'department' => $department,
            'menu' =>$this->menu,
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

        # department
        $department = _department::All();

        return view('department.create',
            [
                'department' => $department,
                'menu' =>$this->menu,
                'current_menu' => $this->current
            ]
        );
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
            'name' => 'name|max:255'
        ]);
        DB::beginTransaction();
        /**
         * @des: save data in department  table
         */
        $department = _department::create([
            'name' => trim($request->de_name),
            'icon_code' => 001,
            'status' => 1
        ]);
        if (!$department) {
            DB::rollback();
        }
        if ($department) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect()->intended('/department');
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

        $department_data = _department::findOrFail($encrypt_id);
        return view(
            'department.edit',
            [
                'department_data'=>$department_data,
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
        DB::beginTransaction();
        /**
         * @des: update   array data in vendor Table
         */
        $dept = [
            'name' => trim($request->de_name),
            'icon_code' => 001,
            'status' => 1
        ];


        $vendor_data= _department::where('id', $uid)->update($dept);

        if (!$vendor_data) {
            DB::rollback();
        }

        if ($vendor_data) {
            DB::commit();
            Session::flash('message', "$this->module successfully updated");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect()->intended('department');
    }

}
