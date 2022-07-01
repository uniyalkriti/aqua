<?php

namespace App\Http\Controllers;

use App\_module;
use App\Location1;
use App\Location2;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class Location2Controller extends Controller
{
    public function __construct()
    {
        $this->menu = _module::orderBy('module_sequence')->get()->load('submenu');
        $this->current = 'Location';
        $this->module=Lang::get('common.location1');
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

        #super stock data
        $query = Location2::join('location_1','location_1.id','=','location_2.location_1_id')
                ->select('location_1.name as location_1_name','location_2.name as location_2_name','location_2.id as location_2_id','location_1_id','location_2.status as status','location_2.created_at as created_at')
                ->where('location_2.status', '!=', 9)
                ->where('location_1.company_id',$company_id)
                ->where('location_2.company_id',$company_id);


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
        $query->orderBy('location_2.created_at','desc');

        $location1 = $query->get();
        // dd($location1);
        return view('location2.index', [
            'location1' => $location1,
            'menu' => $this->menu,
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
        $location1 = Location1::where('company_id',$company_id)->get();
        return view('location2.create',[
            'location1'=> $location1,
            'menu' => $this->menu,
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
            'state' => 'required|max:50',
            'status' => 'required',
            'zone' => 'required',
        ]);

        $company_id = Auth::user()->company_id;

        DB::beginTransaction();
        /**
         * @des: save data in Super_Stockist table
         */
        $location2 = Location2::insert([
            'location_1_id' => trim($request->zone),
            'name' => trim($request->state),
            'company_id' => $company_id,
            'status' => trim($request->status),
            'updated_at' => date('Y-m-d H:i:s',strtotime('now'))
        ]);


        if (!$location2) {
            DB::rollback();
        }
        if ($location2) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('location2');
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
        $location1_info = Location1::where('company_id',$company_id)->pluck('name', 'id');
        $loc_info = Location2::where('company_id',$company_id)->pluck('name', 'id');
        $loc_data = Location2::where('company_id',$company_id)->findOrFail($encrypt_id);
        //dd($loc_data);
        return view(
            'location2.edit',
            [
                'location1_info'=>$location1_info,
                'loc_info' => $loc_info,
                'loc_data'=>$loc_data,
                'encrypt_id' => $id,
                'menu' => $this->menu,
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
            'state' => 'required|max:50',
            'status' => 'required',
            'location1' => 'required'
        ]);

        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        DB::beginTransaction();
        /**
         * @des: update   array data in location2 Table
         */
        $location= [
            'location_1_id' => trim($request->location1),
            'name' => trim($request->state),
            'status' => trim($request->status)
        ];


        $l2_data= Location2::where('id', $uid)->where('company_id',$company_id)->update($location);

        if (!$l2_data) {
            DB::rollback();
        }

        if ($l2_data) {
            DB::commit();
            Session::flash('message', "$this->module successfully updated");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('location2 ');
    }
}
