<?php

namespace App\Http\Controllers;

use App\_module;
use App\Location1;
use App\Location2;
use App\Location3;
use DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;
use Auth;
use Illuminate\Http\Request;

class Location3Controller extends Controller
{
    public function __construct()
    {
        $this->menu = _module::orderBy('module_sequence')->get()->load('submenu');
        $this->current = 'Location';
        $this->module = Lang::get('common.location3');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Location3
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        $location3 = array();
        $location2 = $request->location2;
        $location3_filter = $request->location3_filter;
        // $location5 = $request->location5;
        // $location6 = $request->location6;

        $company_id = Auth::user()->company_id;
        #Location5 data
        if( !empty($location3_filter) || !empty($location4) || !empty($location3) || !empty($location2) || !empty($request->search))
        {
            $query = Location3::join('location_2','location_2.id','=','location_3.location_2_id')
                    ->join('location_1','location_1.id','=','location_2.location_1_id')
                    ->select('location_1.name as location_1_name','location_3.id as id','location_1.id as location_1_id','location_2.id as location_2_id','location_3.name as name','location_2.name as location_2_name','location_3.created_at as created_at','location_3.status as status')
                    ->where('location_3.status', '!=', 9)
                    ->where('location_1.company_id',$company_id)
                    ->where('location_3.company_id',$company_id)
                    ->where('location_2.company_id',$company_id);


           if (!empty($request->search)) {
                $q = $request->search;

                $query->where(function ($subq) use ($q) {
                    $subq->where('location_3.name', 'LIKE', '%' . $q . '%');
                });
            }
            if(!empty($request->location2))
            {
                $query->whereIn('location_2.id',$location2);
            }
            if(!empty($request->location3_filter))
            {
                $query->whereIn('location_3.id',$location3_filter);
            }
          
            # status filter enable it by setting 'status' named form-element on get request
            if (!empty($request->status) && ($request->status == 1 || $request->status == 0 || $request->status == 2)) {
                $query->where('location_3.status', $request->status);
            }
            # table sorting
            $query->orderBy('location_3.created_at','desc');

            $location3 = $query->get();
        }
        $location2 = Location2::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location3_filter_array = Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');

        return view('location3.index', [
            'menu' => $this->menu,
            'location2'=> $location2,
            'location3'=> $location3,
            'location3_filter_array'=> $location3_filter_array,
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
        return view('location3.create', [
            'location1' => $location1,
            'menu' => $this->menu,
            'current_menu' => $this->current
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'location_1' => 'required',
            'location_2' => 'required',
            'name' => 'required|max:50',
            'status' => 'required',
        ]);

        $company_id = Auth::user()->company_id;
        DB::beginTransaction();
        /**
         * @des: save data in Super_Stockist table
         */
        $location3 = Location3::insert([

            'location_2_id' => trim($request->location_2),
            'name' => trim($request->name),
            'company_id' => $company_id,
            'status' => trim($request->status),
            'updated_at' => date('Y-m-d H:i:s',strtotime('now'))
        ]);


        if (!$location3) {
            DB::rollback();
        }
        if ($location3) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('location3');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $encrypt_id = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        #location1 To show drop down
        $location1_info = Location1::where('company_id',$company_id)->pluck('name', 'id');
        #location2 To show drop down
        $location2_info = Location2::where('company_id',$company_id)->pluck('name', 'id');

        $loc_data = Location3::where('company_id',$company_id)->findOrFail($encrypt_id);

        #state Name
        $l3_name = $loc_data->name;
        #state Code
        $l3_code = $loc_data->location_2_id;
        // dd($l3_code);
        #To Get l1_id
        $loc1_code = Location2::where('company_id',$company_id)->where('id', $l3_code)->first();
        // dd($loc1_code);

        #State Name
        $l2_code = $loc1_code->id;
        # State Code
        $l3_code = $loc1_code->location_1_id;

        $loc1 = Location1::where('company_id',$company_id)->where('id', $l3_code)->first();
        # Country Code
        $l1_code = $loc1->id;

        return view(
            'location3.edit',
            [
                'location1_info' => $location1_info,
                'location2_info' => $location2_info,
                'loc_data' => $loc_data,
                'l1_code' => $l1_code,
                'l2_code' => $l2_code,
                'l3_name' => $l3_name,
                'encrypt_id' => $id,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        DB::beginTransaction();
        /**
         * @des: update   array data in location2 Table
         */
        $location = [
            'location_2_id' => trim($request->location_2),
            'name' => trim($request->name),
            'status' => trim($request->status)
        ];

        // echo "<pre>";print_r($location);die;
        $l2_data = Location3::where('company_id',$company_id)->where('id', $uid)->update($location);

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

        return redirect('location3 ');
    }
}
