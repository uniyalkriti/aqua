<?php

namespace App\Http\Controllers;


use App\Catalog1;
use App\Catalog2;
use App\CatalogProduct;
use App\User;
use App\Location3;
use App\Division;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class ProductRateListController extends Controller
{
    public function __construct()
    {
        $this->current_menu = 'product_rate_list';
        $this->current_dir = 'ProductRateList';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd('123');
       // $pagination = !empty($request->perpage) ? $request->perpage : 10;
        $states = $request->state_id;
        $template_array = array();
        // $division = $request->division;
        $company_id = Auth::user()->company_id;
        $template_type_id = $request->template_type_id;
        $stateList = array();
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',4)->first();
        if(!empty($check))
        {
             $template_array=DB::table('template_product')
            ->where('status',1)
            ->where('company_id',$company_id)
            // ->orderBy('name','ASC')
            ->pluck("name","id");
        }
        // dd($states);
        if(!empty($states) )
        {
            $data_fetch = DB::table('product_rate_list')
            ->Join('catalog_product','catalog_product.id','=','product_rate_list.product_id')
            // ->join('_division','_division.id','=','catalog_product.division')
            ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
            ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
            ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
            // ->join('location_3','location_3.id','=','product_rate_list.state_id')
            ->select('other_retailer_rate','other_dealer_rate','catalog_product.itemcode as item_code','product_rate_list.*','catalog_2.name as cat3','catalog_1.name as cat2','catalog_0.name as cat1','catalog_product.name AS name','product_rate_list.state_id AS stateid','product_rate_list.status as rate_list_status')
            ->where('catalog_0.company_id',$company_id)
            ->where('catalog_1.company_id',$company_id)
            ->where('catalog_2.company_id',$company_id)
            ->where('catalog_product.company_id',$company_id)
            ->where('product_rate_list.company_id',$company_id)
            ->where('catalog_product.status',1)
            ->groupBy('product_rate_list.id')
            ->orderBy('name', 'ASC');
         
            if(!empty($states))
            {
                $data_fetch->where('product_rate_list.state_id',$states);
            }
            if(!empty($template_type_id))
            {
                $data_fetch->where('product_rate_list.template_id',$template_type_id);
            }
            $data = $data_fetch->get();

            $state_name = DB::table('location_3')
            			->where('company_id',$company_id)
                        ->groupBy('id')
            			->pluck('location_3.name','id');
			$distribuor_name = DB::table('dealer')
            			->join('product_rate_list','product_rate_list.distributor_id','=','dealer.id')
            			->groupBy('product_rate_list.product_id')
            			->where('product_rate_list.company_id',$company_id)
            			->pluck('dealer.name','product_id');

			$csa_name = DB::table('csa')
            			->join('product_rate_list','product_rate_list.ss_id','=','csa.c_id')
            			->groupBy('product_rate_list.product_id')
            			->where('product_rate_list.company_id',$company_id)
            			->pluck('csa.csa_name','product_id');
            // dd($data);
            $datas=[];
            if(!empty($data))
            {
                foreach ($data as $key => $value) 
                {
                    $iid=$value->stateid.$value->id;
                    $datas[$iid]['id']=$value->id;
                    $datas[$iid]['cat1']=$value->cat1;
                    $datas[$iid]['cat2']=$value->cat2;
                    $datas[$iid]['cat3']=$value->cat3;
                    $datas[$iid]['name']=$value->name;
                    $datas[$iid]['mrp']=$value->mrp;
                    $datas[$iid]['mrp_pcs']=$value->mrp_pcs;
                    $datas[$iid]['dealer_rate']=$value->dealer_rate;
                    $datas[$iid]['dealer_pcs_rate']=$value->dealer_pcs_rate;
                    $datas[$iid]['ss_case_rate']=$value->ss_case_rate;
                    $datas[$iid]['ss_pcs_rate']=$value->ss_pcs_rate;
                    $datas[$iid]['retailer_rate']=$value->retailer_rate;
                    $datas[$iid]['retailer_pcs_rate']=$value->retailer_pcs_rate;
                    $datas[$iid]['status']="1";
                    $datas[$iid]['rate_list_status']=$value->rate_list_status;
                    $datas[$iid]['product_id']=$value->product_id;
                    $datas[$iid]['state_id']=$value->state_id;
                    // $datas[$iid]['state']=$value->state;
                    $datas[$iid]['item_code']=$value->item_code;
                    $datas[$iid]['other_retailer_rate'] = !empty($value->other_retailer_rate)?$value->other_retailer_rate:'0';
                    $datas[$iid]['other_dealer_rate'] = !empty($value->other_dealer_rate)?$value->other_dealer_rate:'0';
       
                }
            }
            // dd($datas);

        }

        else
        {

            if(!empty($check))
            {
                if(!empty($states) || !empty($template_type_id))
                {
                    $data_fetch = DB::table('product_rate_list')
                    ->join('catalog_product','catalog_product.id','=','product_rate_list.product_id')
                    ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                    ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                    ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                    // ->join('location_3','location_3.id','=','product_rate_list.state_id')
                    ->select('other_retailer_rate','other_dealer_rate','catalog_product.itemcode as item_code','product_rate_list.*','catalog_2.name as cat3','catalog_1.name as cat2','catalog_0.name as cat1','catalog_product.name AS name','product_rate_list.state_id AS stateid','product_rate_list.status as rate_list_status')
                    ->where('catalog_0.company_id',$company_id)
                    ->where('catalog_1.company_id',$company_id)
                    ->where('catalog_2.company_id',$company_id)
                    ->where('catalog_product.company_id',$company_id)
                    ->where('product_rate_list.company_id',$company_id)
                     ->where('catalog_product.status',1)
                    ->groupBy('product_rate_list.id')
                    ->orderBy('name', 'ASC');
                    if(!empty($template_type_id))
                    {
                        $data_fetch->where('product_rate_list.template_id',$template_type_id);
                    }
                    $data = $data_fetch->get();
                }
            }
            else
            {
                $data_fetch = DB::table('product_rate_list')
                ->join('catalog_product','catalog_product.id','=','product_rate_list.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                // ->join('location_3','location_3.id','=','product_rate_list.state_id')
                ->select('other_retailer_rate','other_dealer_rate','catalog_product.itemcode as item_code','product_rate_list.*','catalog_2.name as cat3','catalog_1.name as cat2','catalog_0.name as cat1','catalog_product.name AS name','product_rate_list.state_id AS stateid','product_rate_list.status as rate_list_status')
                ->where('catalog_0.company_id',$company_id)
                ->where('catalog_1.company_id',$company_id)
                ->where('catalog_2.company_id',$company_id)
                ->where('catalog_product.company_id',$company_id)
                ->where('product_rate_list.company_id',$company_id)
                 ->where('catalog_product.status',1)
                ->groupBy('product_rate_list.id')
                ->orderBy('name', 'ASC');
                if(!empty($template_type_id))
                {
                    $data_fetch->where('product_rate_list.template_id',$template_type_id);
                }
                $data = $data_fetch->get();
            }


          


            $state_name = DB::table('location_3')
                        ->where('company_id',$company_id)
                        ->groupBy('id')
                        ->pluck('location_3.name','id');
			$distribuor_name = DB::table('dealer')
            			->join('product_rate_list','product_rate_list.distributor_id','=','dealer.id')
            			->where('product_rate_list.company_id',$company_id)
                        ->groupBy('product_rate_list.product_id','dealer.id')
            			->pluck('dealer.name','product_id');

			$csa_name = DB::table('csa')
            			->join('product_rate_list','product_rate_list.ss_id','=','csa.c_id')
            			->groupBy('product_rate_list.product_id')
            			->where('product_rate_list.company_id',$company_id)
            			->pluck('csa.csa_name','product_id');
            // dd($data);
            $datas=[];
            if(!empty($data))
            {
                foreach ($data as $key => $value) 
                {
                    $iid=$value->stateid.$value->id;
                    $datas[$iid]['id']=$value->id;
                    $datas[$iid]['cat1']=$value->cat1;
                    $datas[$iid]['cat2']=$value->cat2;
                    $datas[$iid]['cat3']=$value->cat3;
                    $datas[$iid]['name']=$value->name;
                    $datas[$iid]['mrp']=$value->mrp;
                    $datas[$iid]['mrp_pcs']=$value->mrp_pcs;
                    $datas[$iid]['ss_case_rate']=$value->ss_case_rate;
                    $datas[$iid]['ss_pcs_rate']=$value->ss_pcs_rate;
                    $datas[$iid]['dealer_rate']=$value->dealer_rate;
                    $datas[$iid]['dealer_pcs_rate']=$value->dealer_pcs_rate;
                    $datas[$iid]['retailer_rate']=$value->retailer_rate;
                    $datas[$iid]['retailer_pcs_rate']=$value->retailer_pcs_rate;
                    $datas[$iid]['status']="1";
                    $datas[$iid]['rate_list_status']=$value->rate_list_status;
                    
                    $datas[$iid]['state_id']=$value->state_id;
                    $datas[$iid]['product_id']=$value->product_id;
                    $datas[$iid]['item_code']=$value->item_code;

                    $datas[$iid]['other_retailer_rate'] = !empty($value->other_retailer_rate)?$value->other_retailer_rate:'0';
                    $datas[$iid]['other_dealer_rate'] = !empty($value->other_dealer_rate)?$value->other_dealer_rate:'0';
                    // $datas[$iid]['state']=$value->state;
             
                }
            } 
        }
        $statequery=DB::table('location_3')
        ->select('id','name')
        ->where('status',1)
        ->where('company_id',$company_id)
        ->orderBy('name','ASC')
        ->get();
        foreach ($statequery as $key => $value) 
        {
           $stateList[$value->id]=$value->name;
        }
       
// dd($datas);
        $status_table = "product_rate_list";
        return view($this->current_dir.'.index',
            [
                'records' => $datas,
                'stateList' => $stateList,  
                'template_array'=> $template_array,

                'state_name'=> $state_name,
				'distribuor_name'=> $distribuor_name,
                'csa_name'=> $csa_name,
				'status_table'=> $status_table,

                'current_menu' => $this->current_menu
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //dd($request);
        #Catalog data
        $company_id = Auth::user()->company_id;
        $state_id=$request->state_id;
        $cat_id=$request->cat_id;

        $stateList = array();
        $catList = array();

        $other_rate_ist_data = array();
        $other_rate_ist_id_data = array();
        $check_assign = array();

        if(!empty($state_id) && !empty($cat_id) ){
        $sku = CatalogProduct::where('catalog_product.status', '=', '1')
        ->Join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
        ->Join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
        ->Join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
        ->where('catalog_2.id','=',$cat_id)
        ->where('catalog_2.company_id',$company_id)
        ->where('catalog_1.company_id',$company_id)
        ->where('catalog_0.company_id',$company_id)
        ->where('catalog_product.company_id',$company_id)
        ->select('catalog_product.id AS id','catalog_product.name AS name','product_type')
        ->where('catalog_product.status',1)
        ->get();
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',4)->first();
        if(!empty($check))
        {
        $other_rate_ist_data = DB::table('catalog_product')
                            ->join('product_type','product_type.id','=','catalog_product.product_type')
                            ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                            ->where('product_type.name','!=','CASES')
                            ->where('product_type.company_id',$company_id)
                            ->where('catalog_2.id',$cat_id)
                            ->groupBy('catalog_product.id')
                            ->pluck('product_type.name as name','catalog_product.id as id');

        $other_rate_ist_id_data = DB::table('catalog_product')
                            ->join('product_type','product_type.id','=','catalog_product.product_type')
                            ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                            ->where('product_type.name','!=','CASES')
                            ->where('product_type.company_id',$company_id)
                            ->where('catalog_2.id',$cat_id)
                            ->groupBy('catalog_product.id')

                            ->pluck('product_type.id as ids','catalog_product.id as id');

        $check_assign = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',4)->get();


          }                     //dd($other_rate_ist_data);
        }else{
            $sku=[];
        }
        $statequery=DB::table('location_3')
        ->select('id','name')
        ->where('status',1)
        ->where('company_id',$company_id)
        ->orderBy('name','ASC')
        ->get();
        foreach ($statequery as $key => $value) {
           $stateList[$value->id]=$value->name;
        }

       
        $catquery=DB::table('catalog_2')
        ->select('id','name')
        ->where('status',1)
        ->where('company_id',$company_id)
        ->orderBy('name','ASC')
        ->get();
        foreach ($catquery as $key => $value) {
           $catList[$value->id]=$value->name;
        }

        return view($this->current_dir.'.create',
            ['current_menu' => $this->current_menu,
            'stateList' => $stateList,
            'sku' => $sku,
            'other_rate_ist_data'=> $other_rate_ist_data,
            'other_rate_ist_id_data'=>$other_rate_ist_id_data,
            'check_assign'=> $check_assign,

            'catList' => $catList,

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

            DB::beginTransaction();
        /**
         * @des: save data in Project table
         */
        //dd($request);
        $product_id=$request->product_id;
        $company_id = Auth::user()->company_id;
        
        $myArr=[];
        $state_id=$request->state_id;

        $mrp_pcs = $request->mrp_pcs_rate;
        $mrp_cases = $request->mrp_case_rate;
        $ss_case_rate = $request->ss_case_rate;
        $ss_pcs_rate = $request->ss_pcs_rate;
        $dealer_case_rate = $request->dealer_case_rate;
        $dealer_pcs_rate = $request->dealer_pcs_rate;
        $retailer_cases_rate = $request->retailer_cases_rate;
        $retailer_pcs_rate = $request->retailer_pcs_rate;
        // dd($product_id);
        foreach ($product_id as $key => $value) {   

        if(!empty($mrp_pcs[$key]) && !empty($dealer_pcs_rate[$key]) && !empty($retailer_pcs_rate[$key])) {
            // dd($product_id);    
            // dd($request);
        $myArr=[];     
         $myArr = [
            'product_id' => trim($value),
            'mrp' => trim($mrp_cases[$key]),
            'mrp_pcs' => trim($mrp_pcs[$key]),
            'ss_case_rate' => trim($ss_case_rate[$key]),
            'ss_pcs_rate' => trim($ss_pcs_rate[$key]),
            'dealer_rate' => trim($dealer_case_rate[$key]),
            'dealer_pcs_rate' => trim($dealer_pcs_rate[$key]),
            'retailer_rate' => trim($retailer_cases_rate[$key]),
            'retailer_pcs_rate' => trim($retailer_pcs_rate[$key]),

            'product_type_id'=>!empty($request->product_type_id[$key])?$request->product_type_id[$key]:'0',
            'other_retailer_rate'=>!empty($request->other_retailer_rate_type[$key])?$request->other_retailer_rate_type[$key]:'0',
            'other_dealer_rate'=>!empty($request->other_dealer_rate_type[$key])?$request->other_dealer_rate_type[$key]:'0',

            'company_id' => $company_id,

            'ss_id' => 0,
            'is_temp' => 0,
            'state_id' =>trim($request->state_id),
            'created_at' => date('Y-m-d H:i:s'),
        ];
     $qcheck=DB::table('product_rate_list')
            ->where('company_id',$company_id)
            ->where('product_id',$value)
            ->where('state_id',$state_id)
            ->count();   
            // dd($qcheck);    
      if($qcheck<=0){
        // dd($myArr);
        $product_rate = DB::table('product_rate_list')->insert($myArr);
           }else{
        $product_rate = DB::table('product_rate_list')
        ->where('state_id',$state_id)
        ->where('product_id',$value)
        ->where('company_id',$company_id)
        ->update($myArr);
           }
        // if ($product_rate) {
        //     DB::commit();
        //     Session::flash('message', Lang::get('common.'.$this->current_menu).' created successfully');
        //     Session::flash('class', 'success');
        // } else {
        //     DB::rollback();
        //     Session::flash('message', 'Something went wrong!');
        //     Session::flash('class', 'danger');
        // }
        DB::commit();
        Session::flash('message', Lang::get('common.'.$this->current_menu).' created successfully');
        Session::flash('class', 'success');
                }
            }

        return redirect()->intended($this->current_menu);

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        #decrypt id
        $company_id = Auth::user()->company_id;
        $uid = Crypt::decryptString($id);
        $check_assign_edit = array();
        $type_name_details = array();
        // dd($uid);    

        $product_rate_list_fetch = DB::table('product_rate_list')
                                ->join('catalog_product','catalog_product.id','=','product_rate_list.product_id')
                                
                                ->select('product_rate_list.id as id','product_id','catalog_product.name as product_name','product_rate_list.*')
                                ->where('product_rate_list.id',$uid)->first();

        #fetch Project
        $check_assign_edit = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',4)->get();
        if(!empty($check_assign_edit))
        {
            $type_name_details = DB::table('catalog_product')
                            ->join('product_rate_list','catalog_product.id','=','product_rate_list.product_id')
                            ->join('product_type','product_type.id','=','catalog_product.product_type')
                           
                            ->select('product_type.name as type_name','product_type.id as type_id')
                            ->where('product_type.name','!=','CASES')
                            ->where('product_type.company_id',$company_id)
                            ->where('product_rate_list.id',$uid)
                            ->first();
                     
                            

           
        }
        // dd($type_name_details);

        #Catalog 1 data

        return view($this->current_dir.'.edit',
            [
                'encrypt_id' =>$id,
                'product_rate_list_fetch'=> $product_rate_list_fetch,
                'check_assign_edit'=> $check_assign_edit,
                'type_name_details'=> $type_name_details,
                'type_name_details'=> $type_name_details,
                'current_menu'=>$this->current_menu
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

        DB::beginTransaction();
        $id = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        
        $myArr = [
            'mrp' => trim($request->mrp_case_rate),
            'mrp_pcs' => trim($request->mrp_pcs_rate),
            'ss_case_rate' => trim($request->ss_case_rate),
            'ss_pcs_rate' => trim($request->ss_pcs_rate),
            'dealer_rate' => trim($request->dealer_case_rate),
            'dealer_pcs_rate' => trim($request->dealer_pcs_rate),
            'product_type_id'=> $request->product_type_id,
            'other_retailer_rate'=> $request->other_retailer_rate,
            'other_dealer_rate'=> $request->other_dealer_rate,
            'retailer_rate' => trim($request->retailer_cases_rate),
            'retailer_pcs_rate' => trim($request->retailer_pcs_rate),
            'updated_at'=>date('Y-m-d'),
        ];
        $product_rate_list = DB::table('product_rate_list')->where('id',$id)->where('company_id',$company_id)->update($myArr);
        if($product_rate_list)
        {
            DB::commit();
            Session::flash('message', Lang::get('common.'.$this->current_menu).' updated successfully');
            Session::flash('class', 'success');
        }
        else
        {
            DB::rollback();
            Session::flash('message', Lang::get('common.'.$this->current_menu).'Please try again!!');
            Session::flash('class', 'danger');
        }
        return redirect()->intended($this->current_menu);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
