<?php

namespace App\Http\Controllers;

use App\_module;
use App\Location1;
use App\Location2;
use App\Dealerowner;
use App\Outlettype;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class OutletTypeController extends Controller
{
    public function __construct()
    {
        $this->current = 'outletType';
        $this->module=Lang::get('common.outlet_type');
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

       
        $query = DB::table('_retailer_outlet_type')
                ->select('id','outlet_type','status')
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

        $outlet_type = $query->get();
        // dd($outlet_type);
        return view('outletType.index', [
            'outlet_type' => $outlet_type,
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
        $outlet_type = DB::table('_retailer_outlet_type')->where('company_id',$company_id)->get();
        return view('outletType.create',[
            'outlet_type'=> $outlet_type,
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
            'outletType' => 'required|max:50',
            'status' => 'required',
        ]);

        $company_id = Auth::user()->company_id;

        DB::beginTransaction();
        /**
         * @des: save data in Super_Stockist table
         */
        $outlet_type = DB::table('_retailer_outlet_type')->insert([
            'outlet_type' => trim($request->outletType),
            'company_id' => $company_id,
            'status' => trim($request->status),
            'created_at'=> date('Y-m-d H:i:s'),
            'created_by'=>Auth::user()->id,
        ]);


        if (!$outlet_type) {
            DB::rollback();
        }
        if ($outlet_type) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('outletType');
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
        $outletType_info = DB::table('_retailer_outlet_type')->where('company_id',$company_id)->pluck('outlet_type', 'id');
        $outletType_data = Outlettype::where('company_id',$company_id)->findOrFail($encrypt_id);
        // dd($loc_data);
        return view(
            'outletType.edit',
            [
                'outletType_info'=>$outletType_info,
                'outletType_data'=>$outletType_data,
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
            'outlet_type' => 'required|max:50',
            'status' => 'required',
        ]);

        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        DB::beginTransaction();
        /**
         * @des: update   array data in location2 Table
         */
        $outlet_type= [
            'outlet_type' => trim($request->outlet_type),
            'status' => trim($request->status),
            'updated_at'=>date('Y-m-d H:i:s'),
            'updated_by'=>Auth::user()->id,
        ];


        $outlet_type_data= Outlettype::where('id', $uid)->where('company_id',$company_id)->update($outlet_type);

        if (!$outlet_type_data) {
            DB::rollback();
        }

        if ($outlet_type_data) {
            DB::commit();
            Session::flash('message', "$this->module successfully updated");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('outletType ');
    }
}
