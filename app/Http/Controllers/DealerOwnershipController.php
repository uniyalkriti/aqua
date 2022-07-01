<?php

namespace App\Http\Controllers;

use App\_module;
use App\Location1;
use App\Location2;
use App\Dealerowner;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class DealerOwnershipController extends Controller
{
    public function __construct()
    {
        $this->current = 'dealerOwnership';
        $this->module=Lang::get('common.dealer_ownership');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        $cities = $request->city;
        $state = $request->state;
        $company_id = Auth::user()->company_id;

       
        $query = DB::table('_dealer_ownership_type')
                ->select('id','ownership_type','status')
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

        $dealer_owner = $query->get();
        // dd($dealer_owner);
        return view('dealerOwnership.index', [
            'dealer_owner' => $dealer_owner,
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
        $dealer_owner = DB::table('_dealer_ownership_type')->where('company_id',$company_id)->get();
        return view('dealerOwnership.create',[
            'dealer_owner'=> $dealer_owner,
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
            'dealerOwner' => 'required|max:50',
            'status' => 'required',
        ]);

        $company_id = Auth::user()->company_id;

        DB::beginTransaction();
        /**
         * @des: save data in Super_Stockist table
         */
        $dealer_owner = DB::table('_dealer_ownership_type')->insert([
            'ownership_type' => trim($request->dealerOwner),
            'company_id' => $company_id,
            'status' => trim($request->status)
        ]);


        if (!$dealer_owner) {
            DB::rollback();
        }
        if ($dealer_owner) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('dealerOwnership');
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
        $dealerOwner_info = DB::table('_dealer_ownership_type')->where('company_id',$company_id)->pluck('ownership_type', 'id');
        $dealerOwner_data = Dealerowner::where('company_id',$company_id)->findOrFail($encrypt_id);
        //dd($loc_data);
        return view(
            'dealerOwnership.edit',
            [
                'dealerOwner_info'=>$dealerOwner_info,
                'dealerOwner_data'=>$dealerOwner_data,
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
            'ownership_type' => 'required|max:50',
            'status' => 'required',
        ]);

        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        DB::beginTransaction();
        /**
         * @des: update   array data in location2 Table
         */
        $dealer_owner= [
            'ownership_type' => trim($request->ownership_type),
            'status' => trim($request->status)
        ];


        $dealer_owner_data= Dealerowner::where('id', $uid)->where('company_id',$company_id)->update($dealer_owner);

        if (!$dealer_owner_data) {
            DB::rollback();
        }

        if ($dealer_owner_data) {
            DB::commit();
            Session::flash('message', "$this->module successfully updated");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('dealerOwnership ');
    }
}
