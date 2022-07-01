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
use App\SchemePlanDetails;
use App\SchemePlan;
use App\UserIncentiveSlabs;
use App\UserIncentiveRoleDistribution;
use App\UserIncentive;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;
use Illuminate\Http\Request;

class SchemeController extends Controller
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
        $query = SchemePlan::where('status', '=', 1)->where('company_id',$company_id);

        $query->orderBy('created_at','desc');

        $incentive = $query->get();

        return view('schemePlan.index', [
            'incentive' => $incentive,
            'company_id' => $company_id,
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
        $incentive = SchemePlan::where('company_id',$company_id)->get();
        
        $product = DB::table('catalog_product')->where('company_id',$company_id)->where('status',1)->pluck('name','id');

        if($company_id == 52){
            return view('schemePlan.patanjali_create', [
                'incentive' => $incentive,
                'product' => $product,
    
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);

        }
        else{
        return view('schemePlan.create', [
            'incentive' => $incentive,
            'product' => $product,

            'menu' => $this->menu,
            'current_menu' => $this->current
        ]);
        }
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
                if($company_id == 52){
                    $incentive = SchemePlan::create([
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
                        $file = $request->imageForGift[$s_key];
                         $name_random = date('YmdHis');
                        $str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
                        $random_no = substr($str, 0,2);  // return always a new string 
                        $custom_image_name = date('YmdHis').$random_no;
                        $imageName = $custom_image_name . '.' . $file->getClientOriginalExtension();
                        $destinationPath = public_path('/schemeplandetails/');
                        $file->move($destinationPath , $imageName);





                        $user_incentive_slabs = SchemePlanDetails::create([
                            'scheme_id' => $incentive->id,
                            // 'product_id' => $implode,
                            'product_id' => !empty($request->product1[$s_key])?$request->product1[$s_key]:'0',
                            'sale_unit' => $request->unit_type[$s_key],
                            'sale_value_range_first' => $request->range_first[$s_key],
                            'sale_value_range_last' => $request->range_last[$s_key],
                            'incentive_type' => $request->amt_type[$s_key],
                            'value_amount_percentage' => $request->amount[$s_key],
                            'created_at'=>date('Y-m-d H:i:s'),
                            'company_id'=> $company_id,
                            'image' => $imageName,
                            'updated_at'=>date('Y-m-d H:i:s'),
            
                        ]);
                
                    }

                }
                else{
                $incentive = SchemePlan::create([
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
                    $user_incentive_slabs = SchemePlanDetails::create([
                        'scheme_id' => $incentive->id,
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

        return redirect('scheme');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company_id = Auth::user()->company_id;
        $encrypt_id = Crypt::decryptString($id);
        #location1 To show drop down
        $location1_info = Location1::pluck('name', 'id');

        $scheme_plan_data = SchemePlan::findOrFail($encrypt_id);

        $scheme_plan_details_data = SchemePlanDetails::where('scheme_id',$encrypt_id)->where('company_id',$company_id)->get()->toArray();

        // dd($scheme_plan_details_data);

        $product = DB::table('catalog_product')->where('company_id',$company_id)->where('status',1)->pluck('name','id');


        $scheme_category_status = $scheme_plan_data->scheme_category_status;
        $vs_status = $scheme_plan_data->vs_status;
        $item_status = $scheme_plan_data->item_status;


        if($company_id == 52){

            return view(
                'schemePlan.patanjali_edit',
                [
                    'location1_info'=>$location1_info,
                    'scheme_plan_data'=>$scheme_plan_data,
                    'product'=>$product,
                    'encrypt_id' => $id,
                    'menu' => $this->menu,
                    'current_menu' => $this->current,
                    'scheme_category_status' => $scheme_category_status,
                    'vs_status' => $vs_status,
                    'item_status' => $item_status,
                    'scheme_plan_details_data' => $scheme_plan_details_data,

                ]
            );

        }
        else{
     
        return view(
            'schemePlan.edit',
            [
                'location1_info'=>$location1_info,
                'scheme_plan_data'=>$scheme_plan_data,
                'product'=>$product,
                'encrypt_id' => $id,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]
        );
      }

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
    	// dd($request);

        $company_id = Auth::user()->company_id;
        $uid = Crypt::decryptString($id);
        DB::beginTransaction();

        if($company_id == 52){
      
        $incentive = SchemePlan::where('id', $uid)->update([
            'scheme_name' => trim($request->p_name),
            'scheme_category_status' => trim($request->plan_category_status),
            'vs_status' => trim($request->vs_status),
            'item_status' => trim($request->item_status_id),
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'company_id' => $company_id,

        ]);
    	$delte_data = SchemePlanDetails::where('scheme_id',$uid)->where('company_id',$company_id)->delete();
        foreach($request->unit_type as $s_key => $s_value)
        {
            if(!empty($request->imageForGift[$s_key])){
            $file = $request->imageForGift[$s_key];
            $name_random = date('YmdHis');
            $str = str_shuffle("1234567891234567891232345678904567891234567891234567890098765432125646456765456");
            $random_no = substr($str, 0,5);  // return always a new string 
            $custom_image_name = date('YmdHis').$random_no;
            $imageName = $custom_image_name . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('/schemeplandetails/');
            $file->move($destinationPath , $imageName);
            }else{
            $imageName = '';
            }

            if(!empty($request->amt_type[$s_key]) && $request->amt_type[$s_key] == '4'){
                $user_incentive_slabs = SchemePlanDetails::insert([
                    'scheme_id' => $uid,
                    // 'product_id' => $implode,
                    'product_id' => !empty($request->product1[$s_key])?$request->product1[$s_key]:'0',
                    'sale_unit' => '4',
                    'sale_value_range_first' => $request->range_first[$s_key],
                    'sale_value_range_last' => $request->range_last[$s_key],
                    'incentive_type' => '4',
                    'value_amount_percentage' => $request->amount[$s_key],
                    'created_at'=>date('Y-m-d H:i:s'),
                    'company_id'=> $company_id,
                    'image' => $imageName,
                    'updated_at'=>date('Y-m-d H:i:s'),

                ]);
            }else{
                $user_incentive_slabs = SchemePlanDetails::insert([
                    'scheme_id' => $uid,
                    // 'product_id' => $implode,
                    'product_id' => !empty($request->product1[$s_key])?$request->product1[$s_key]:'0',
                    'sale_unit' => $s_value,
                    'sale_value_range_first' => $request->range_first[$s_key],
                    'sale_value_range_last' => $request->range_last[$s_key],
                    'incentive_type' => $request->amt_type[$s_key],
                    'value_amount_percentage' => $request->amount[$s_key],
                    'created_at'=>date('Y-m-d H:i:s'),
                    'company_id'=> $company_id,
                    'image' => $imageName,
                    'updated_at'=>date('Y-m-d H:i:s'),

                ]);
            }

            if (!$user_incentive_slabs) {
                DB::rollback();
                Session::flash('message', 'Something went wrong!');
                Session::flash('alert-class', 'alert-danger');
            }
    
        }
    }
    else{

    }




        if (isset($incentive)) {
            DB::commit();
            Session::flash('message', "$this->module successfully");
            Session::flash('alert-class', 'alert-success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('scheme');
    }

    public function show($id)
    {
        $company_id = Auth::user()->company_id;
        $encrypt_id = Crypt::decryptString($id);
        #location1 To show drop down
        $location1_info = Location1::pluck('name', 'id');

        $scheme_plan_data = SchemePlan::findOrFail($encrypt_id);

        $scheme_plan_details_data = SchemePlanDetails::where('scheme_id',$encrypt_id)->where('company_id',$company_id)->get()->toArray();

        // dd($scheme_plan_details_data);

        $product = DB::table('catalog_product')->where('company_id',$company_id)->where('status',1)->pluck('name','id');


        $scheme_category_status = $scheme_plan_data->scheme_category_status;
        $vs_status = $scheme_plan_data->vs_status;
        $item_status = $scheme_plan_data->item_status;


        if($company_id == 52){

            return view(
                'schemePlan.patanjali_show',
                [
                    'location1_info'=>$location1_info,
                    'scheme_plan_data'=>$scheme_plan_data,
                    'product'=>$product,
                    'encrypt_id' => $id,
                    'menu' => $this->menu,
                    'current_menu' => $this->current,
                    'scheme_category_status' => $scheme_category_status,
                    'vs_status' => $vs_status,
                    'item_status' => $item_status,
                    'scheme_plan_details_data' => $scheme_plan_details_data,

                ]
            );

        }
        else{
     
        return view(
            'schemePlan.show',
            [
                'location1_info'=>$location1_info,
                'scheme_plan_data'=>$scheme_plan_data,
                'product'=>$product,
                'encrypt_id' => $id,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]
        );
      }

    }





}
