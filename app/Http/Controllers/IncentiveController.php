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
use App\UserIncentiveDetails;
use App\UserIncentiveSlabs;
use App\UserIncentiveRoleDistribution;
use App\UserIncentive;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;
use Illuminate\Http\Request;

class IncentiveController extends Controller
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

        $company_id = Auth::user()->company_id;
        #Location5 data
        $query = UserIncentiveDetails::where('status', '=', 1)->where('company_id',$company_id);

        $query->orderBy('created_at','desc');

        $incentive = $query->get();

        return view('incentive.index', [
            'incentive' => $incentive,
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
        $incentive = UserIncentiveDetails::where('company_id',$company_id)->get();
        $role_group = DB::table('_role_group')->where('status',1)->where('company_id',$company_id)->get();
        $product = DB::table('catalog_product')->where('company_id',$company_id)->where('status',1)->pluck('name','id');
        return view('incentive.create', [
            'incentive' => $incentive,
            'product' => $product,
            'role_group'=>$role_group,
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

        // dd($request);
        $company_id = Auth::user()->company_id;
        
        $validatedData = $request->validate([
            'p_name' => 'required|max:50',
            'plan_category_status' => 'required',

            'range_first' => 'required',
            'amount' => 'required',
        ]);
        if($request->item_status_id == 1)
        {
            $product = $request->product;
        }
        elseif($request->item_status_id == 2)
        {
            $product = $request->product1;
        }

        $implode = !empty($product)?implode(',',$product):'0';


        DB::beginTransaction();
        $temp = 0;
        $count_role_group =  DB::table('_role_group')->where('status',1)->where('company_id',$company_id)->COUNT();
        $incentive = UserIncentiveDetails::create([
            'plan_name' => trim($request->p_name),
            'plan_category_status' => trim($request->plan_category_status),
            'vs_status' => trim(!empty($request->vs_status)?$request->vs_status:'0'),
            'item_status' => trim($request->item_status_id),
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'company_id' => $company_id,

        ]);
    
        foreach($request->unit_type as $s_key => $s_value)
        {
            $user_incentive_slabs = UserIncentiveSlabs::create([
                'plan_id' => $incentive->id,
                'product_id' => $implode,
                'sale_unit' => $request->unit_type[$s_key],
                'sale_value_range_first' => $request->range_first[$s_key],
                'sale_value_range_last' => $request->range_last[$s_key],
                'incentive_type' => $request->amt_type[$s_key],
                'value_amount_percentage' => $request->amount[$s_key],
                'created_at'=>date('Y-m-d H:i:s'),
                'company_id'=> $company_id,
                'updated_at'=>date('Y-m-d H:i:s'),

            ]);
            for ($i=$temp; $i < $count_role_group ; $i++) 
            { 
                $role_distribution = UserIncentiveRoleDistribution::create([
                    'slab_id' => $user_incentive_slabs->id,
                    'role_group' => $request->role_name_id[$i],
                    'amount' => $request->role_wise_amount[$i],
                    'created_at' => date("Y-m-d H:i:s"),
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'company_id'=> $company_id,
                 ]);
            }
        }

        if (!$incentive) {
            DB::rollback();
        }
        if ($incentive) {
            DB::commit();
            Session::flash('message', "$this->module created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('incentive');

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
        #location1 To show drop down
        $location1_info = Location1::pluck('name', 'id');

        $town_data = Location5::findOrFail($encrypt_id);

        #town
        $town_name = $town_data->name;
        $town_code = $town_data->location_4_id;


        #District code
        $district_data = Location4::where('id',$town_code)->first();


        $h_code = $district_data->id;
        $district_code = $district_data->location_3_id;

        #hq
        $hq_data = Location3::where('id', $district_code)->first();
        $s_code = $hq_data->id;
        $hq_code = $hq_data->location_2_id;

        #state
        $state_data = Location2::where('id',$hq_code)->first();
        $c_code = $state_data->id;
        $state_code = $state_data->location_1_id;

        #Country
        $country_data = Location1::where('id', $state_code)->first();
        $c_id = $country_data->id;
        $country_code = $country_data->id;

        #location2 To show drop down
        $location2_info = Location2::where('location_1_id',$c_id)->pluck('name', 'id');

        $location3_info = Location3::where('location_2_id',$c_code)->pluck('name', 'id');
        $location4_info = Location4::where('location_3_id',$s_code)->pluck('name', 'id');
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
        DB::beginTransaction();
        /**
         * @des: update   array data in location_5 Table
         */
        $location = [
            'name' => trim($request->town),
            'location_4_id' => trim($request->location_4),
            'company_id' => 1,
            'status' => trim($request->status)
        ];

        $l2_data= Location5::where('id', $uid)->update($location);

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
