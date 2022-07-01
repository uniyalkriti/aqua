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
use App\productPlanDetails;
use App\productPlan;
use App\UserIncentiveSlabs;
use App\UserIncentiveRoleDistribution;
use App\UserIncentive;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;
use Illuminate\Http\Request;

class productSchemeController extends Controller
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
        $company_id = Auth::user()->company_id;
        $query = productPlan::where('status', '=', 1)->where('company_id',$company_id);

        $query->orderBy('created_at','desc');

        $incentive = $query->get();

        return view('productPlan.index', [
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
        $incentive = productPlan::where('company_id',$company_id)->get();

        $dateformat = date('Ymd');
        $company_name = DB::table('company')->where('id',$company_id)->first();
        $scheme_name = $company_name->title.'('.$dateformat.')';

        $state = DB::table('location_3')->where('status','=','1')->where('company_id',$company_id)->pluck('name','id');
        
        $product = DB::table('catalog_product')->where('company_id',$company_id)->where('status',1)->pluck('name','id');
        return view('productPlan.create', [
            'incentive' => $incentive,
            'product' => $product,
            'company_id' => $company_id,
            'scheme_name' => $scheme_name,
            'state' => $state,

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
            'vs_status' => 'required',
            'range_first' => 'required',
            'amount' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'state_id' => 'required',
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
        $incentive = productPlan::create([
            'scheme_name' => trim($request->p_name),
            'scheme_category_status' => trim($request->plan_category_status),
            'vs_status' => trim($request->vs_status),
            'item_status' => trim($request->item_status_id),
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'company_id' => $company_id,

        ]);
    
        foreach($request->unit_type as $s_key => $s_value)
        {

            $checkFrom = DB::table('product_wise_scheme_plan_details')
                    ->whereRaw("DATE_FORMAT(product_wise_scheme_plan_details.valid_from_date, '%Y-%m-%d')<='$request->from_date' AND DATE_FORMAT(product_wise_scheme_plan_details.valid_to_date, '%Y-%m-%d')>='$request->from_date'")
                    ->where('product_id','=',$request->product1[$s_key])
                    ->where('state_id','=',$request->state_id)
                    ->where('company_id',$company_id)
                    ->get()->toArray();

            $checkTo = DB::table('product_wise_scheme_plan_details')
                    ->whereRaw("DATE_FORMAT(product_wise_scheme_plan_details.valid_from_date, '%Y-%m-%d')<='$request->to_date' AND DATE_FORMAT(product_wise_scheme_plan_details.valid_to_date, '%Y-%m-%d')>='$request->to_date'")
                    ->where('product_id','=',$request->product1[$s_key])
                    ->where('state_id','=',$request->state_id)
                    ->where('company_id',$company_id)
                    ->get()->toArray();

                    // print_r($checkFrom);
                    // dd($checkTo);

            if (!empty($checkFrom) && !empty($checkTo)) {
                dd('1');
                DB::rollback();
                Session::flash('message', "Scheme Already Applied");
                Session::flash('alert-class', 'alert-danger');
                return redirect('productScheme');
            }


            $user_incentive_slabs = productPlanDetails::create([
                'scheme_id' => $incentive->id,
                // 'product_id' => $implode,
                'state_id' => $request->state_id,
                'product_id' => $request->product1[$s_key],
                'sale_unit' => $request->unit_type[$s_key],
                'sale_value_range_first' => $request->range_first[$s_key],
                'sale_value_range_last' => $request->range_last[$s_key],
                'incentive_type' => $request->amt_type[$s_key],
                'value_amount_percentage' => $request->amount[$s_key],
                'created_at'=>date('Y-m-d H:i:s'),
                'company_id'=> $company_id,
                'updated_at'=>date('Y-m-d H:i:s'),
                'valid_from_date' => $request->from_date,
                'valid_to_date' => $request->to_date,


            ]);
       
        }

        if (!$incentive) {
            DB::rollback();
        }
        if ($incentive) {
            DB::commit();
            Session::flash('message', "Scheme created successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('productScheme');

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
        $company_id = Auth::user()->company_id;


        // dd($encrypt_id);

        $productDetails = DB::table('product_wise_scheme_plan_details')
                        ->join('location_3','location_3.id','=','product_wise_scheme_plan_details.state_id')
                        ->join('catalog_product','catalog_product.id','=','product_wise_scheme_plan_details.product_id')
                        ->select('catalog_product.name as product_name','location_3.name as state_name','product_wise_scheme_plan_details.*')
                        ->where('scheme_id',$encrypt_id)
                        ->where('product_wise_scheme_plan_details.company_id',$company_id)
                        ->groupBy('product_wise_scheme_plan_details.id')
                        ->get();

        // dd($productDetails);
       
        return view(
            'productPlan.edit',
            [
                'productDetails'=>$productDetails,
                'encrypt_id'=>$encrypt_id,
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

    public function editSchemeDetails(Request $request)
    {
        $string = $request->details;
        $updated_val = $request->updated_val;
        $company_id = Auth::user()->company_id;


        $explode_data = explode('|', $string);

        // dd($updated_val);

        $scheme_id = $explode_data['1'];
        $state_id = $explode_data['2'];
        $sku_id = $explode_data['3'];
        $valid_from = $explode_data['4'];
        $valid_to = $explode_data['5'];

        $updateData = DB::table('product_wise_scheme_plan_details')
                    ->where('company_id',$company_id)
                    ->where('scheme_id',$scheme_id)
                    ->where('state_id',$state_id)
                    ->where('product_id',$sku_id)
                    ->where('valid_from_date',$valid_from)
                    ->where('valid_to_date',$valid_to)
                    ->update([
                        'value_amount_percentage' => $updated_val,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

        $selectData = DB::table('product_wise_scheme_plan_details')
                    ->where('company_id',$company_id)
                    ->where('scheme_id',$scheme_id)
                    ->where('state_id',$state_id)
                    ->where('product_id',$sku_id)
                    ->where('valid_from_date',$valid_from)
                    ->where('valid_to_date',$valid_to)
                    ->first();

        // $selectData = array("value_amount_percentage"=>'50');

        if($selectData){
            $data['code'] = 200;
            $data['string'] = $string;
            $data['result'] = $selectData;
            $data['message'] = 'success';
        }else{
            $data['code'] = 401;
            $data['string'] = '';
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }

        // dd($data);
        return json_encode($data);



    }



}
