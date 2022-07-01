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
use App\Location7;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;
use Illuminate\Http\Request;

class Location7Controller extends Controller
{
    public function __construct()
    {
        $this->menu = _module::orderBy('module_sequence')->get()->load('submenu');
        $this->current = 'Location';
        $this->module = Lang::get('common.location7');
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

        #Location7 data
        $location2 = $request->location2;
        $location3 = $request->location3;
        $location4 = $request->location4;
        $location5 = $request->location5;
        $location6 = $request->location6;
        $location_beat = $request->location7;
        $location7 = array();
        if(!empty($location_beat)|| !empty($location6) || !empty($location5) || !empty($location4) || !empty($location3) || !empty($location2) || !empty($request->search))
        {
            $query = Location7::join('location_6','location_6.id','=','location_7.location_6_id')
            ->join('location_5','location_5.id','=','location_6.location_5_id')
            ->join('location_4','location_4.id','=','location_5.location_4_id')
            ->join('location_3','location_3.id','=','location_4.location_3_id')
            ->join('location_2','location_2.id','=','location_3.location_2_id')
            ->join('location_1','location_1.id','=','location_2.location_1_id')
            ->select('location_7.created_at as created_at','location_7.status as status','location_7.id as id','location_7.name as name','location_1.id as location_1_id','location_2.id as location_2_id','location_3.id as location_3_id','location_4.id as location_4_id','location_5.id as location_5_id','location_6.id as location_6_id','location_6.name as location_6_name','location_5.name as location_5_name','location_4.name as location_4_name','location_3.name as location_3_name','location_2.name as location_2_name','location_1.name as location_1_name','location_7.beat_no')
            ->where('location_1.company_id',$company_id)
            ->where('location_2.company_id',$company_id)
            ->where('location_3.company_id',$company_id)
            ->where('location_4.company_id',$company_id)
            ->where('location_5.company_id',$company_id)
            ->where('location_6.company_id',$company_id)
            ->where('location_7.company_id',$company_id)
            ->where('location_7.status', '=', 1);


            # search functionality
            if (!empty($request->search)) {
                $q = $request->search;

                $query->where(function ($subq) use ($q) {
                    $subq->where('location_7.name', 'LIKE', '%' . $q . '%');
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
            if(!empty($request->location6))
            {
                $query->whereIn('location_6.id',$location6);
            }
            if(!empty($request->location7))
            {
                $query->whereIn('location_7.id',$location_beat);
            }
            # status filter enable it by setting 'status' named form-element on get request
            if (!empty($request->status) && ($request->status == 1 || $request->status == 0 || $request->status == 2)) {
                $query->where('location_7.status', $request->status);
            }
            # table sorting
            $query->orderBy('location_7.created_at', 'desc');

            $location7 = $query->get();
        }
        $location2 = Location2::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location3 = Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location4 = Location4::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location5 = Location5::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location6 = Location6::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_beat_array = Location7::where('status',1)->where('company_id',$company_id)->pluck('name','id');

        return view('location7.index', [
            'location7' => $location7,
            'location2'=> $location2,
            'location3'=> $location3,
            'location4'=> $location4,
            'location6' => $location6,
            'location5'=> $location5,
            'location_beat_array'=> $location_beat_array,
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
        //
        $company_id = Auth::user()->company_id;
        $location1 = Location1::where('company_id',$company_id)->where('status',1)->get();
        return view('location7.create', [
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
            'beat_name' => 'required',
            'location_6' => 'required|max:9',
            'status' => 'required',
        ]);



        DB::beginTransaction();
        /**
         * @des: save data in Super_Stockist table
         */
        $company_id = Auth::user()->company_id;
        $auth_id = Auth::user()->id;

        $check = DB::table('location_7')
                ->where('location_6_id',$request->location_6)
                ->where('name',$request->beat_name)
                ->where('status',$request->status)
                ->count();
                // dd($check);
        if(($check)>0)
        {
            Session::flash('message', "$this->module already exist !!");
            Session::flash('alert-class', 'alert-danger');
            return redirect('location7');

        }
        else
        {
            $location7 = Location7::create([
            'name' => trim($request->beat_name),
            'location_6_id' => trim($request->location_6),
            'company_id' => $company_id,
            'created_at'=>date('Y-m-d H:i:s'),
            'created_by'=>$auth_id,
            'status' => trim($request->status),
            'beat_no' => $request->beat_no,


            ]);

            if (!$location7) {
                DB::rollback();
            }
            if ($location7) {
                DB::commit();
                Session::flash('message', "$this->module created successfully");
                Session::flash('alert-class', 'alert-success');
            } else {
                DB::rollback();
                Session::flash('message', 'Something went wrong!');
                Session::flash('alert-class', 'alert-danger');
            }
        }
        

        return redirect('location7');
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

        $beat_data = Location7::where('status',1)->where('company_id',$company_id)->findOrFail($encrypt_id);
        $beat_code = $beat_data->location_6_id;
        // dd($beat_code);
        #pin Data
        $pincode_data = Location6::where('status',1)->where('company_id',$company_id)->where('id',$beat_code)->first();

        $p_code = $pincode_data->id;
        $t_code = $pincode_data->location_5_id;

        #town
        $pin_data = Location5::where('status',1)->where('company_id',$company_id)->where('id',$t_code)->first();

        $town_code = $pin_data->id;
        $h_code = $pin_data->location_4_id;


        #district
        $district_data = Location4::where('status',1)->where('company_id',$company_id)->where('id', $h_code)->first();
        $district_code = $district_data->id;
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
        $code = $country_data->id;

        $location6_info = Location6::where('status',1)->where('company_id',$company_id)->where('location_5_id',$t_code)->pluck('name', 'id');

        $location5_info = Location5::where('status',1)->where('company_id',$company_id)->where('location_4_id',$h_code)->pluck('name', 'id');

        $location4_info = Location4::where('status',1)->where('company_id',$company_id)->where('location_3_id',$dis_code)->pluck('name', 'id');

        $location3_info = Location3::where('status',1)->where('company_id',$company_id)->where('location_2_id',$hq_code)->pluck('name', 'id');

        #location2 To show drop down
        $location2_info = Location2::where('status',1)->where('company_id',$company_id)->where('location_1_id',$c_code)->pluck('name', 'id');

        return view(
            'location7.edit',
            [
                'location1_info'=>$location1_info,
                'location2_info'=>$location2_info,
                'location3_info'=>$location3_info,
                'location4_info'=>$location4_info,
                'location5_info'=>$location5_info,
                'location6_info'=>$location6_info,
                'p_code'=>$p_code,
                'town_code'=>$town_code,
                'district_code'=>$district_code,
                's_code'=>$s_code,
                'state_code'=>$state_code,
                'code'=>$code,
                'beat_data'=>$beat_data,
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
        $company_id = Auth::user()->company_id;
        $auth_id = Auth::user()->id;
        $location = [
            'name' => trim($request->beat_name),
            'location_6_id' => trim($request->location_6),
            'company_id' => $company_id,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $auth_id,
            'status' => trim($request->status),
            'beat_no' => $request->beat_no,
            
        ];

        // echo "<pre>";print_r($location);die;
        $location_6= Location7::where('id', $uid)->update($location);

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

        return redirect('location7');
    }
}
