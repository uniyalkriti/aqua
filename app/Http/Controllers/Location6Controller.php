<?php

namespace App\Http\Controllers;

use App\_module;
use DB;
use Auth;
use App\Location1;
use App\Location2;
use App\Location3;
use App\Location4;
use App\Location5;
use App\Location6;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;
use Illuminate\Http\Request;

class Location6Controller extends Controller
{
    public function __construct()
    {
        $this->menu = _module::orderBy('module_sequence')->get()->load('submenu');
        $this->current = 'Location';
        $this->module = Lang::get('common.location6');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        #Location6 data
        $location6 = array();

        $location2 = $request->location2;
        $location3 = $request->location3;
        $location4 = $request->location4;
        $location5 = $request->location5;
        $location6_filter = $request->location6_filter;
        #Location6 data
        $company_id = Auth::user()->company_id;
        if(!empty($location6_filter) || !empty($location5) || !empty($location4) || !empty($location3) || !empty($location2) || !empty($request->search))
        {

            $query = Location6::join('location_5','location_5.id','=','location_6.location_5_id')
            ->join('location_4','location_4.id','=','location_5.location_4_id')
            ->join('location_3','location_3.id','=','location_4.location_3_id')
            ->join('location_2','location_2.id','=','location_3.location_2_id')
            ->join('location_1','location_1.id','=','location_2.location_1_id')
            ->select('location_6.created_at as created_at','location_6.status as status','location_6.id as id','location_6.name as name','location_1.id as location_1_id','location_2.id as location_2_id','location_3.id as location_3_id','location_4.id as location_4_id','location_5.id as location_5_id','location_5.name as location_5_name','location_4.name as location_4_name','location_3.name as location_3_name','location_2.name as location_2_name','location_1.name as location_1_name')
            ->where('location_1.company_id',$company_id)
            ->where('location_2.company_id',$company_id)
            ->where('location_3.company_id',$company_id)
            ->where('location_4.company_id',$company_id)
            ->where('location_5.company_id',$company_id)
            ->where('location_6.company_id',$company_id)
            ->where('location_6.status', '!=', 9);
            # search functionality
            if (!empty($request->search)) {
                $q = $request->search;

                $query->where(function ($subq) use ($q) {
                    $subq->where('location_6.name', 'LIKE', '%' . $q . '%');
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
            if(!empty($request->location5))
            {
                $query->whereIn('location_5.id',$location5);
            }
            if(!empty($request->location6_filter))
            {
                $query->whereIn('location_6.id',$location6_filter);
            }
            # status filter enable it by setting 'status' named form-element on get request
            if (!empty($request->status) && ($request->status == 1 || $request->status == 0 || $request->status == 2)) {
                $query->where('location_6.status', $request->status);
            }
            # table sorting
            $query->orderBy('location_6.created_at','desc');

            $location6= $query->get();
        }
        $location2 = Location2::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location3 = Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location4 = Location4::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location5 = Location5::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location6_array_filter = Location6::where('status',1)->where('company_id',$company_id)->pluck('name','id');

        return view('location6.index', [
            'location6' => $location6,
            'menu' => $this->menu,
            'location2'=> $location2,
            'location3'=> $location3,
            'location4'=> $location4,
            'location5'=> $location5,
            'location6_array_filter'=> $location6_array_filter,
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
        $location1 = Location1::where('company_id',$company_id)->where('status',1)->get();
        return view('location6.create', [
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
        // print_r($_POST); exit;
        $validatedData = $request->validate([
            'name' => 'required|max:50',
            'location_5' => 'required|max:10',
            'status' => 'required',
        ]);
        DB::beginTransaction();
        /**
         * @des: save data in location_6 table
         */
        $company_id = Auth::user()->company_id;
        $auth_id = Auth::user()->id;

        $check = DB::table('location_6')
                ->where('location_5_id',$request->location_5)
                ->where('name',$request->name)
                ->where('status',$request->status)
                ->first();
                // dd($check);
        if(!empty($check))
        {
            Session::flash('message', "$this->module already exist !!");
            Session::flash('alert-class', 'alert-danger');
        }
        else
        {
            $location6 = Location6::create([
            'name' => trim($request->name),
            'location_5_id' => trim($request->location_5),
            'company_id' => trim($company_id),
            'status' => trim($request->status),
            'created_at'=>date("Y-m-d H:i:s"),
            'created_by'=>$auth_id,

            ]);

            if (!$location6) {
                DB::rollback();
            }
            if ($location6) {
                DB::commit();
                Session::flash('message', "$this->module created successfully");
                Session::flash('alert-class', 'alert-success');
            } else {
                DB::rollback();
                Session::flash('message', 'Something went wrong!');
                Session::flash('alert-class', 'alert-danger');
            }
        }
        

        return redirect('location6');
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
        $location1_info = Location1::where('status',1)->where('company_id',$company_id)->pluck('name', 'id');

        $pin_data = Location6::where('status',1)->where('company_id',$company_id)->findOrFail($encrypt_id);

        #pin
        $pin_code = $pin_data->location_5_id;

        #Town
        $town_data = Location5::where('status',1)->where('company_id',$company_id)->where('id',$pin_code)->first();
        $t_code = $town_data->id;
        $town_code = $town_data->location_4_id;

        #district
        $district_data = Location4::where('status',1)->where('company_id',$company_id)->where('id', $town_code)->first();
        $h_code = $district_data->id;
        $dis_code = $district_data->location_3_id;

        #hq
        $hq_data = Location3::where('status',1)->where('company_id',$company_id)->where('id',$dis_code)->first();

        $s_code = $hq_data->id;
        $hq_code = $hq_data->location_2_id;

        #state
        $state_data = Location2::where('status',1)->where('company_id',$company_id)->where('id', $hq_code)->first();
        $state_code = $state_data->id;
        $c_code = $state_data->location_1_id;

        #country
        $country_data = Location1::where('status',1)->where('company_id',$company_id)->where('id', $c_code)->first();
        $country_code = $country_data->id;

        $location5_info = Location5::where('status',1)->where('company_id',$company_id)->where('location_4_id',$h_code)->pluck('name', 'id');

        $location4_info = Location4::where('status',1)->where('company_id',$company_id)->where('location_3_id',$s_code)->pluck('name', 'id');

        $location3_info = Location3::where('status',1)->where('company_id',$company_id)->where('location_2_id',$state_code)->pluck('name', 'id');

        #location2 To show drop down
        $location2_info = Location2::where('status',1)->where('company_id',$company_id)->where('location_1_id',$country_code)->pluck('name', 'id');
        return view(
            'location6.edit',
            [
                'location1_info'=>$location1_info,
                'location2_info'=>$location2_info,
                'location3_info'=>$location3_info,
                'location4_info'=>$location4_info,
                'location5_info'=>$location5_info,
                't_code'=>$t_code,
                'h_code'=>$h_code,
                's_code'=>$s_code,
                'state_code'=>$state_code,
                'country_code'=>$country_code,
                'pin_data'=>$pin_data,
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
        DB::beginTransaction();
        /**
         * @des: update   array data in location2 Table
         */
        $auth_id = Auth::user()->id;
        $company_id = Auth::user()->company_id;
        $location = [
            'name' => trim($request->name),
            'location_5_id' => trim($request->location_5),
            'updated_at'=>date('Y-m-d H:i:s'),
            'updated_by'=>$auth_id,
            'status' => trim($request->status)
        ];

        $location_6= Location6::where('id', $uid)->update($location);

        if (!$location_6) {
            DB::rollback();
        }

        if (isset($location_6)) {
            DB::commit();
            Session::flash('message', "$this->module successfully updated");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('location6');
    }
}
