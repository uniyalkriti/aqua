<?php

namespace App\Http\Controllers;

use App\_module;
use App\Location1;
use App\Location2;
use App\Dealerowner;
use App\Outlettype;
use App\Weighttype;
use App\Travellingtype;
use App\Merchandisetype;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class MerchandiseTypeController extends Controller
{
    public function __construct()
    {
        $this->current = 'merchandiseType';
        $this->module=Lang::get('common.merchandise_type');
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

       
        $query = DB::table('_retailer_mkt_gift')
                ->select('id','gift_name','status')
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

        $travel_type = $query->get();
        // dd($travel_type);
        return view('merchandiseType.index', [
            'travel_type' => $travel_type,
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
        $travel_type = DB::table('_retailer_mkt_gift')->where('company_id',$company_id)->get();
        return view('merchandiseType.create',[
            'travel_type'=> $travel_type,
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
            'merchandiseType' => 'required|max:50',
            'status' => 'required',
        ]);

        $company_id = Auth::user()->company_id;

        DB::beginTransaction();
        /**
         * @des: save data in Super_Stockist table
         */
        $travel_type = DB::table('_retailer_mkt_gift')->insert([
            'gift_name' => trim($request->merchandiseType),
            'company_id' => $company_id,
            'status' => trim($request->status)
        ]);


        if (!$travel_type) {
            DB::rollback();
        }
        if ($travel_type) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('merchandiseType');
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
        $travelType_info = DB::table('_retailer_mkt_gift')->where('company_id',$company_id)->pluck('gift_name', 'id');
        $travelType_data = Merchandisetype::where('company_id',$company_id)->findOrFail($encrypt_id);
        // dd($loc_data);
        return view(
            'merchandiseType.edit',
            [
                'travelType_info'=>$travelType_info,
                'travelType_data'=>$travelType_data,
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
            'merchandiseType' => 'required|max:50',
            'status' => 'required',
        ]);

        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        DB::beginTransaction();
        /**
         * @des: update   array data in location2 Table
         */
        $travel_type= [
            'gift_name' => trim($request->merchandiseType),
            'status' => trim($request->status)
        ];


        $travel_type_data= Merchandisetype::where('id', $uid)->where('company_id',$company_id)->update($travel_type);

        if (!$travel_type_data) {
            DB::rollback();
        }

        if ($travel_type_data) {
            DB::commit();
            Session::flash('message', "$this->module successfully updated");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('merchandiseType ');
    }
}
