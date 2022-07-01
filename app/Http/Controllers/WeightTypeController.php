<?php

namespace App\Http\Controllers;

use App\_module;
use App\Location1;
use App\Location2;
use App\Dealerowner;
use App\Outlettype;
use App\Weighttype;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class WeightTypeController extends Controller
{
    public function __construct()
    {
        $this->current = 'weightType';
        $this->module=Lang::get('common.weight_type');
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

       
        $query = DB::table('weight_type')
                ->select('id','type','status','value')
                ->where('status', '!=', 9)
                ->where('company_id',$company_id);


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
        $query->orderBy('id','desc');

        $weight_type = $query->get();
        // dd($weight_type);
        return view('weightType.index', [
            'weight_type' => $weight_type,
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
        $weight_type = DB::table('weight_type')->where('company_id',$company_id)->get();
        return view('weightType.create',[
            'weight_type'=> $weight_type,
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
            'weightType' => 'required|max:50',
            'weightValue' => 'required|max:50',
            'status' => 'required',
        ]);

        $company_id = Auth::user()->company_id;

        DB::beginTransaction();
        /**
         * @des: save data in Super_Stockist table
         */
        $weight_type = DB::table('weight_type')->insert([
            'type' => trim($request->weightType),
            'value' => trim($request->weightValue),
            'company_id' => $company_id,
            'status' => trim($request->status)
        ]);


        if (!$weight_type) {
            DB::rollback();
        }
        if ($weight_type) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('weightType');
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
        $weightType_info = DB::table('weight_type')->where('company_id',$company_id)->pluck('type', 'id');
        $weightType_data = Weighttype::where('company_id',$company_id)->findOrFail($encrypt_id);
        // dd($loc_data);
        return view(
            'weightType.edit',
            [
                'weightType_info'=>$weightType_info,
                'weightType_data'=>$weightType_data,
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
            'weightType' => 'required|max:50',
            'weightValue' => 'required|max:50',
            'status' => 'required',
        ]);

        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        DB::beginTransaction();
        /**
         * @des: update   array data in location2 Table
         */
        $weight_type= [
            'type' => trim($request->weightType),
            'value' => trim($request->weightValue),
            'status' => trim($request->status)
        ];


        $weight_type_data= Weighttype::where('id', $uid)->where('company_id',$company_id)->update($weight_type);

        if (!$weight_type_data) {
            DB::rollback();
        }

        if ($weight_type_data) {
            DB::commit();
            Session::flash('message', "$this->module successfully updated");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('weightType ');
    }
}
