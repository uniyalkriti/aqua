<?php

namespace App\Http\Controllers;

use App\_module;
use App\Location1;
use App\Location2;
use App\Location3;
use App\Location4;
use App\locationFourTown;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class LocationFourTownController extends Controller
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

    private $title = "locationFourTown";
    public function index(Request $request)
    {
        $pagination = !empty($request->perpage) ? $request->perpage : 10;
        $location4 = array();
        $location2 = $request->location2;
        $location3 = $request->location3;
        $location4_filter = $request->location4_filter;
        // $location5 = $request->location5;
        // $location6 = $request->location6;

        $company_id = Auth::user()->company_id;
        #Location5 data
        if( !empty($location4_filter) || !empty($location4) || !empty($location3) || !empty($location2) || !empty($request->search))
        {
            $query = locationFourTown::join('location_3','location_3.id','=','location_4_townexp.location_3_id')
                    ->join('location_2','location_2.id','=','location_3.location_2_id')
                    ->join('location_1','location_1.id','=','location_2.location_1_id')
                    ->select('location_4_townexp.status as status','location_4_townexp.name as name','location_4_townexp.id as id','location_3.id as location_3_id','location_2.id as location_2_id','location_1.id as location_1_id','location_4_townexp.created_at as created_at','location_3.name as location_3_name','location_2.name as location_2_name','location_1.name as location_1_name')
                    ->where('location_4_townexp.status', '!=', 9)
                    ->where('location_4_townexp.company_id',$company_id)
                    ->where('location_3.company_id',$company_id)
                    ->where('location_1.company_id',$company_id)
                    ->where('location_2.company_id',$company_id);


             # search functionality
            if (!empty($request->search)) {
                $q = $request->search;

                $query->where(function ($subq) use ($q) {
                    $subq->where('location_4_townexp.name', 'LIKE', '%' . $q . '%');
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
            if(!empty($request->location4_filter))
            {
                $query->whereIn('location_4_townexp.id',$location4_filter);
            }
          
            # status filter enable it by setting 'status' named form-element on get request
            if (!empty($request->status) && ($request->status == 1 || $request->status == 0 || $request->status == 2)) {
                $query->where('location_4_townexp.status', $request->status);
            }
            # table sorting
            $query->orderBy('location_4_townexp.created_at','desc');

            $location4 = $query->get();
        }
        $location2 = Location2::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location3 = Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location4_filter_array = locationFourTown::where('status',1)->where('company_id',$company_id)->pluck('name','id');

        return view('locationFourTown.index', [
            'menu' => $this->menu,
            'location2'=> $location2,
            'location3'=> $location3,
            'location4'=> $location4,
            'location4_filter_array'=> $location4_filter_array,
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
        return view('locationFourTown.create', [
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
        $company_id = Auth::user()->company_id;
        $createdBy = Auth::user()->id;
        DB::beginTransaction();
        /**
         * @des: save data in Super_Stockist table
         */
        $check = DB::table('location_4_townexp')
                ->where('location_3_id',$request->location_3)
                ->where('name',$request->name)
                ->where('status',$request->status)
                ->count();
                // dd($check);
        if(($check)>0)
        {
            Session::flash('message', "$this->module already exist !!");
            Session::flash('alert-class', 'alert-danger');
            return redirect('locationFourTown');

        }

        $location3 = locationFourTown::create([
            'name' => trim($request->name),
            'location_3_id' => trim($request->location_3),
            'company_id' => $company_id,
            'status' => trim($request->status),
            'created_by' => $createdBy,


        ]);

        if (!$location3) {
            DB::rollback();
        }
        if ($location3) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        }
        else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('locationFourTown');

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
        $loc_data = locationFourTown::where('company_id',$company_id)->findOrFail($encrypt_id);

        #location1 To show drop down
        $location1_info = Location1::where('company_id',$company_id)->pluck('name', 'id');
        #location2 To show drop down
        $location2_info = Location2::where('company_id',$company_id)->pluck('name', 'id');

        $location3_info = Location3::where('company_id',$company_id)->pluck('name', 'id');

        #state Name
        $l4_name = $loc_data->name;

        #state Code
        $l4_code = $loc_data->id;
        $l4_3_code = $loc_data->location_3_id;
        #To Get l2_id
        $l3_code = Location3::where('company_id',$company_id)->where('id', $l4_3_code)->first();

        #Hq Name
        $location3_code = $l3_code->id;
        #hq Code
        $l3_code = $l3_code->location_2_id;


        $l2_code = Location2::where('company_id',$company_id)->where('id', $l3_code)->first();


        #State Name
        $loc2_name = $l2_code->name;

        # State Code
        $loc2_code = $l2_code->location_1_id;


        $loc1 = Location1::where('company_id',$company_id)->where('id', $loc2_code)->first();
        # Country Code
        $l1_code = $loc1->id;

        return view(
            'locationFourTown.edit',
            [
                'location1_info'=>$location1_info,
                'location2_info'=>$location2_info,
                'location3_info'=>$location3_info,
                'loc_data'=>$loc_data,
                'l1_code'=>$l1_code,
                'loc2_code'=>$loc2_code,
                'l3_code'=>$l3_code,
                'loc2_code'=>$loc2_code,
                'l4_name'=>$l4_name,
                'l4_code'=>$l4_code,
                'location3_code'=>$location3_code,
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
         * @des: update   array data in location2 Table
         */
        $location = [
            'name' => trim($request->name),
            'location_3_id' => trim($request->location_3),
            'status' => trim($request->status)
        ];

        $l2_data= locationFourTown::where('company_id',$company_id)->where('id', $uid)->update($location);

        if (!$l2_data) {
            DB::rollback();
        }

        if (isset($l2_data)) {
            DB::commit();
            Session::flash('message', "$this->module successfully updated");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('locationFourTown');
    }
}
