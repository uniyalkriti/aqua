<?php

namespace App\Http\Controllers;

use App\_module;
use DB;
use App\Location1;
use App\Location2;
use App\Location3;
use App\Location4;
use App\Location5;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;
use Auth;
use Illuminate\Http\Request;

class Location5Controller extends Controller
{
    public function __construct()
    {
        $this->menu = _module::orderBy('module_sequence')->get()->load('submenu');
        $this->current = 'Location';
        $this->module = Lang::get('common.location5');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        #Location5 data
        $location5 = array();
        $location2 = $request->location2;
        $location3 = $request->location3;
        $location4 = $request->location4;
        $location5_filter = $request->location5_filter;
        // $location5 = $request->location5;
        // $location6 = $request->location6;

        $company_id = Auth::user()->company_id;
        #Location5 data
        if( !empty($location5_filter) || !empty($location4) || !empty($location3) || !empty($location2) || !empty($request->search))
        {
            $query = Location5::join('location_4','location_4.id','=','location_5.location_4_id')
                    ->join('location_3','location_3.id','=','location_4.location_3_id')
                    ->join('location_2','location_2.id','=','location_3.location_2_id')
                    ->join('location_1','location_1.id','=','location_2.location_1_id')
                    ->select('location_5.created_at as created_at','location_5.status as status','location_5.id as id','location_5.name as name','location_4.name as location_4_name','location_3.name as location_3_name','location_2.name as location_2_name','location_1.name as location_1_name')
                    ->where('location_1.company_id',$company_id)
                    ->where('location_2.company_id',$company_id)
                    ->where('location_3.company_id',$company_id)
                    ->where('location_4.company_id',$company_id)
                    ->where('location_5.company_id',$company_id)
                    ->where('location_5.status', '!=', 9);


            # search functionality
            if (!empty($request->search)) {
                $q = $request->search;

                $query->where(function ($subq) use ($q) {
                    $subq->where('location_5.name', 'LIKE', '%' . $q . '%');
                });
            }
            if(!empty($request->location2))
            {
                $query->whereIn('location_2.id',$location2);
            }
            if(!empty($request->location3))
            {
                $query->whereIn('location_3.id',$location3);
            }
            if(!empty($request->location4))
            {
                $query->whereIn('location_4.id',$location4);
            }
            if(!empty($request->location5_filter))
            {
                $query->whereIn('location_5.id',$location5_filter);
            }
            # status filter enable it by setting 'status' named form-element on get request
            if (!empty($request->status) && ($request->status == 1 || $request->status == 0 || $request->status == 2)) {
                $query->where('location_5.status', $request->status);
            }
            # table sorting
            $query->orderBy('location_5.created_at','desc');

            $location5 = $query->get();
        }
        $location2 = Location2::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location3 = Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location4 = Location4::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location5_filter_array = Location5::where('status',1)->where('company_id',$company_id)->pluck('name','id');

        return view('location5.index', [
            'location5' => $location5,
            'menu' => $this->menu,
            'location2'=> $location2,
            'location3'=> $location3,
            'location4'=> $location4,
            'location5_filter_array'=> $location5_filter_array,
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
        return view('location5.create', [
            'location1' => $location1,
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

        $validatedData = $request->validate([
            'town' => 'required|max:50',
            'status' => 'required',
            'location_4' => 'required',
            'location_3' => 'required',
            'location_2' => 'required',
            'location_1' => 'required'
        ]);

        $company_id = Auth::user()->company_id;

        DB::beginTransaction();
        /**
         * @des: save data in Location5 table
         */
        $check_l7 = DB::table('location_5')
                    ->where('name',$request->name)
                    ->where('location_5.status','!=',9)
                    ->where('location_4_id',$request->location_4)
                    ->where('company_id',$request->company_id)
                    ->count();
        if($check_l7>0)
        {
            Session::flash('message', 'Duplicate Entry Please Check');
            Session::flash('alert-class', 'alert-danger');
            return redirect('location5 ');
        }
        $location5 = Location5::create([
            'name' => trim($request->town),
            'location_4_id' => trim($request->location_4),
            'company_id' => $company_id,
            'status' => trim($request->status)

        ]);

        if (!$location5) {
            DB::rollback();
        }
        if ($location5) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('location5');

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
        #location1 To show drop down
        $location1_info = Location1::where('company_id',$company_id)->pluck('name', 'id');

        $town_data = Location5::where('company_id',$company_id)->findOrFail($encrypt_id);

        #town
        $town_name = $town_data->name;
        $town_code = $town_data->location_4_id;


        #District code
        $district_data = Location4::where('id',$town_code)->where('company_id',$company_id)->first();


        $h_code = $district_data->id;
        $district_code = $district_data->location_3_id;

        #hq
        $hq_data = Location3::where('id', $district_code)->where('company_id',$company_id)->first();
        $s_code = $hq_data->id;
        $hq_code = $hq_data->location_2_id;

        #state
        $state_data = Location2::where('id',$hq_code)->where('company_id',$company_id)->first();
        $c_code = $state_data->id;
        $state_code = $state_data->location_1_id;

        #Country
        $country_data = Location1::where('id', $state_code)->where('company_id',$company_id)->first();
        $c_id = $country_data->id;
        $country_code = $country_data->id;

        #location2 To show drop down
        $location2_info = Location2::where('location_1_id',$c_id)->where('company_id',$company_id)->pluck('name', 'id');

        $location3_info = Location3::where('location_2_id',$c_code)->where('company_id',$company_id)->pluck('name', 'id');
        $location4_info = Location4::where('location_3_id',$s_code)->where('company_id',$company_id)->pluck('name', 'id');
        return view(
            'location5.edit',
            [
                'location1_info'=>$location1_info,
                'location2_info'=>$location2_info,
                'location3_info'=>$location3_info,
                'location4_info'=>$location4_info,
                'h_code'=>$h_code,
                's_code'=>$s_code,
                'c_code'=>$c_code,
                'town_data'=>$town_data,
                'id'=>$c_id,
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
        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        DB::beginTransaction();
        /**
         * @des: update   array data in location_5 Table
         */
        $location = [
            'name' => trim($request->town),
            'location_4_id' => trim($request->location_4),
            'status' => trim($request->status)
        ];

        $l2_data= Location5::where('company_id',$company_id)->where('id', $uid)->update($location);

        if (!$l2_data) {
            DB::rollback();
        }

        if (isset($l2_data)) {
            DB::commit();
            Session::flash('message', "$this->module successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('location5');
    }
}
