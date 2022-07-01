<?php

namespace App\Http\Controllers;

use App\_module;
use App\Location1;
use App\Location2;
use App\Dealerowner;
use App\Outlettype;
use App\Gsttype;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class GstTypeController extends Controller
{
    public function __construct()
    {
        $this->current = 'gstType';
        $this->module=Lang::get('common.gst_type');
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
        // dd($company_id);
       
        $query = DB::table('_gst')
                ->select('id','company_id','hsn_code','sgst','cgst','igst','status')
                ->where('status', '!=', 9)
                ->where('company_id',$company_id);


        # search functionality
        if (!empty($request->search)) {
            $q = $request->search;

            $query->where(function ($subq) use ($q) {
                $subq->where('hsn_code', 'LIKE', '%' . $q . '%');
            });
        }
        # status filter enable it by setting 'status' named form-element on get request
        if (!empty($request->status) && ($request->status == 1 || $request->status == 0 || $request->status == 2)) {
            $query->where('status', $request->status);
        }
        # table sorting
        $query->orderBy('id','desc');

        $gst_type = $query->get();
        // dd($gst_type);
        return view('gstType.index', [
            'gst_type' => $gst_type,
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
        $gst_type = DB::table('_gst')->where('company_id',$company_id)->get();
        return view('gstType.create',[
            'gst_type'=> $gst_type,
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
            'HSN' => 'required|max:50',
            'SGST' => 'required|max:50',
            'CGST' => 'required|max:50',
            'IGST' => 'required|max:50',
            'status' => 'required',
        ]);

        $company_id = Auth::user()->company_id;

        DB::beginTransaction();
        /**
         * @des: save data in Super_Stockist table
         */
        $gst_type = DB::table('_gst')->insert([
            'hsn_code' => trim($request->HSN),
            'sgst' => trim($request->SGST),
            'cgst' => trim($request->CGST),
            'igst' => trim($request->IGST),
            'company_id' => $company_id,
            'status' => trim($request->status)
        ]);


        if (!$gst_type) {
            DB::rollback();
        }
        if ($gst_type) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('gstType');
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
        $gstType_info = DB::table('_gst')->where('company_id',$company_id)->pluck('hsn_code', 'id');
        $gstType_data = Gsttype::where('company_id',$company_id)->findOrFail($encrypt_id);
        // dd($loc_data);
        return view(
            'gstType.edit',
            [
                'gstType_info'=>$gstType_info,
                'gstType_data'=>$gstType_data,
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
            'HSN' => 'required|max:50',
            'SGST' => 'required|max:50',
            'CGST' => 'required|max:50',
            'IGST' => 'required|max:50',
            'status' => 'required',
        ]);

        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        DB::beginTransaction();
        /**
         * @des: update   array data in location2 Table
         */
        $gst_type= [
            'hsn_code' => trim($request->HSN),
            'sgst' => trim($request->SGST),
            'cgst' => trim($request->CGST),
            'igst' => trim($request->IGST),
            'status' => trim($request->status)
        ];


        $gst_type_data= Gsttype::where('id', $uid)->where('company_id',$company_id)->update($gst_type);

        if (!$gst_type_data) {
            DB::rollback();
        }

        if ($gst_type_data) {
            DB::commit();
            Session::flash('message', "$this->module successfully updated");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('gstType ');
    }
}
