<?php

namespace App\Http\Controllers;


use App\Catalog1;
use App\Catalog2;
use App\CatalogProduct;
use App\User;
use App\Location3;
use App\Division;
use App\Dealer;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class ProductRateListControllerTemplate extends Controller
{
    public function __construct()
    {
        $this->current_menu = 'product_rate_list_template';
        $this->current_dir = 'ProductRateListTemplate';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $template = !empty($request->template)?$request->template:array('0');
        $company_id = Auth::user()->company_id;
        $data_fetch = DB::table('product_rate_list_template')
                    ->join('template_product','template_product.id','=','product_rate_list_template.template_type')
                    ->select('product_rate_list_template.status as status','template_product.name as template_type','product_rate_list_template.template_type as id')
                    ->where('product_rate_list_template.company_id',$company_id)
                    ->whereIn('product_rate_list_template.template_type',$template)
                    ->where('product_rate_list_template.status',1)
                    ->groupBy('template_product.id');
                    // ->get();
        // dd($template);
        // if(!empty($template))
        // {
        //     $data_fetch->where('product_rate_list_template.template_type',$template);
        // }
        $data = $data_fetch->get();
        // dd($data);
        $template_array=DB::table('template_product')
        ->where('status',1)
        ->where('company_id',$company_id)
        // ->orderBy('name','ASC')
        ->pluck("name","id");

        // ->select('product_rate_list_template.mrp as mrp','catalog_product.name as sku_name','itemcode as item_code','catalog_1.name as c1_name','catalog_2.name as c2_name','catalog_0.name as c0_name','mrp as cases_mrp','mrp_pcs as mrp','ss_case_rate as ss_cases_rate','ss_pcs_rate as ss_rate','dealer_rate as dealer_cases_rate','dealer_pcs_rate as dealer_rate','retailer_rate as retailer_cases_rate','retailer_pcs_rate as retailer_rate')
        
        #mrp starts here 

            $mrp_case_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1)
                ->groupBy('product_id');

          
            $mrp_case_rate = $mrp_case_rate_data->pluck('product_rate_list_template.mrp','product_rate_list_template.product_id as id');

            $mrp_pcs_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1)
                ->groupBy('product_id');

            $mrp_pcs_rate = $mrp_pcs_rate_data->pluck('product_rate_list_template.mrp_pcs','product_rate_list_template.product_id as id');
            // dd($mrp_pcs_rate);
        #mrp ends here 

        # Retailer rate starts here 
        $retailer_case_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1);

                if(!empty($template))
                {
                    $retailer_case_rate_data->whereIn('product_rate_list_template.template_type',$template);
                }
        $retailer_case_rate = $retailer_case_rate_data->pluck('retailer_rate',DB::raw("CONCAT(product_rate_list_template.product_id,product_rate_list_template.template_type) as data"));
        // dd($retailer_case_rate);
        $retailer_pcs_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1);

                if(!empty($template))
                {
                    $retailer_pcs_rate_data->whereIn('product_rate_list_template.template_type',$template);
                }
        $retailer_pcs_rate = $retailer_pcs_rate_data->pluck('retailer_pcs_rate',DB::raw("CONCAT(product_rate_list_template.product_id,product_rate_list_template.template_type) as data"));

        $retailer_primary_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1);

                if(!empty($template))
                {
                    $retailer_primary_rate_data->whereIn('product_rate_list_template.template_type',$template);
                }
        $retailer_primary_rate = $retailer_primary_rate_data->pluck('other_retailer_rate',DB::raw("CONCAT(product_rate_list_template.product_id,product_rate_list_template.template_type) as data"));

        # Retailer rate ends here 


        # Distributor rate starts here 
        $distributor_case_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1);

                if(!empty($template))
                {
                    $distributor_case_rate_data->whereIn('product_rate_list_template.template_type',$template);
                }
        $distributor_case_rate = $distributor_case_rate_data->pluck('dealer_rate',DB::raw("CONCAT(product_rate_list_template.product_id,product_rate_list_template.template_type) as data"));

        $distributor_pcs_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1);

                if(!empty($template))
                {
                    $distributor_pcs_rate_data->whereIn('product_rate_list_template.template_type',$template);
                }
        $distributor_pcs_rate = $distributor_pcs_rate_data->pluck('dealer_pcs_rate',DB::raw("CONCAT(product_rate_list_template.product_id,product_rate_list_template.template_type) as data"));

        $distributor_primary_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1);

                if(!empty($template))
                {
                    $distributor_primary_rate_data->whereIn('product_rate_list_template.template_type',$template);
                }
        $distributor_primary_rate = $distributor_primary_rate_data->pluck('other_dealer_rate',DB::raw("CONCAT(product_rate_list_template.product_id,product_rate_list_template.template_type) as data"));

        # Distributor rate ends here 

        # super stockiest rate starts here 
        $csa_case_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1);

                if(!empty($template))
                {
                    $csa_case_rate_data->whereIn('product_rate_list_template.template_type',$template);
                }
        $csa_case_rate = $csa_case_rate_data->pluck('ss_case_rate',DB::raw("CONCAT(product_rate_list_template.product_id,product_rate_list_template.template_type) as data"));

        $csa_pcs_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1);

                if(!empty($template))
                {
                    $csa_pcs_rate_data->whereIn('product_rate_list_template.template_type',$template);
                }
        $csa_pcs_rate = $csa_pcs_rate_data->pluck('ss_pcs_rate',DB::raw("CONCAT(product_rate_list_template.product_id,product_rate_list_template.template_type) as data"));

        $csa_primary_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1);

                if(!empty($template))
                {
                    $csa_primary_rate_data->whereIn('product_rate_list_template.template_type',$template);
                }
        $csa_primary_rate = $csa_primary_rate_data->pluck('other_ss_rate',DB::raw("CONCAT(product_rate_list_template.product_id,product_rate_list_template.template_type) as data"));

        # super stockiest rate ends here 

        $query_fetch = DB::table('catalog_product')
                // ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->select('catalog_product.id as sku_id','catalog_product.name as sku_name','catalog_2.name as c2_name','catalog_1.name as c1_name','catalog_0.name as c0_name')
                ->where('catalog_product.company_id',$company_id)
                ->where('catalog_product.status',1);
              
        $sku_details = $query_fetch->get();
        // foreach ($statequery as $key => $value) 
        // {
        //    $stateList[$value->id]=$value->name;
        // }
        $location3 = DB::table('location_3')
                    ->where('company_id',$company_id)
                    ->where('status',1)
                    ->pluck('name','id');
        $location1 = DB::table('location_1')
                    ->where('company_id',$company_id)
                    ->where('status',1)
                    ->pluck('name','id');
        $distributor = DB::table('dealer')
                    ->where('company_id',$company_id)
                    ->where('dealer_status',1)
                    ->pluck('name','id');
        $csa = DB::table('csa')
                    ->where('company_id',$company_id)
                    ->where('active_status',1)
                    ->pluck('csa_name','c_id');
// dd($data);
        return view($this->current_dir.'.index',
            [
                'records' => $data,
                'sku_details'=> $sku_details,
                'template_array' => $template_array,
                'location3'=> $location3,
                'distributor'=> $distributor,
                'csa'=> $csa,
                'location1'=> $location1,
                'company_id'=> $company_id,

                'csa_case_rate'=> $csa_case_rate,
                'csa_pcs_rate'=> $csa_pcs_rate,
                'csa_primary_rate'=> $csa_primary_rate,

                'distributor_case_rate'=> $distributor_case_rate,
                'distributor_pcs_rate'=> $distributor_pcs_rate,
                'distributor_primary_rate'=> $distributor_primary_rate,

                'retailer_case_rate'=> $retailer_case_rate,
                'retailer_pcs_rate'=> $retailer_pcs_rate,
                'retailer_primary_rate'=> $retailer_primary_rate,

                'mrp_case_rate'=> $mrp_case_rate,
                'mrp_pcs_rate'=> $mrp_pcs_rate,

                'current_menu' => $this->current_menu
            ]);
    }
    public function testIndex(Request $request)
    {
        $template = $request->template;
        $company_id = Auth::user()->company_id;
        $data_fetch = DB::table('product_rate_list_template_test')
                    ->join('template_product','template_product.id','=','product_rate_list_template_test.template_type')
                    ->select('product_rate_list_template_test.status as status','template_product.name as template_type','product_rate_list_template_test.template_type as id')
                    ->where('product_rate_list_template_test.company_id',$company_id)
                    ->where('product_rate_list_template_test.status',1)
                    ->groupBy('template_product.id');
                    // ->get();
        if(!empty($template))
        {
            $data_fetch->where('product_rate_list_template_test.template_type',$template);
        }
        $data = $data_fetch->get();
        // dd($data);
        $template_array=DB::table('template_product')
        ->where('status',1)
        ->where('company_id',$company_id)
        // ->orderBy('name','ASC')
        ->pluck("name","id");

        // ->select('product_rate_list_template_test.mrp as mrp','catalog_product.name as sku_name','itemcode as item_code','catalog_1.name as c1_name','catalog_2.name as c2_name','catalog_0.name as c0_name','mrp as cases_mrp','mrp_pcs as mrp','ss_case_rate as ss_cases_rate','ss_pcs_rate as ss_rate','dealer_rate as dealer_cases_rate','dealer_pcs_rate as dealer_rate','retailer_rate as retailer_cases_rate','retailer_pcs_rate as retailer_rate')
        
        #mrp starts here 

            $mrp_case_rate_data = DB::table('product_rate_list_template_test')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template_test.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template_test.company_id',$company_id)
                ->where('product_rate_list_template_test.status',1)
                ->groupBy('product_id');

          
            $mrp_case_rate = $mrp_case_rate_data->pluck('product_rate_list_template_test.mrp','product_rate_list_template_test.product_id as id');

            $mrp_pcs_rate_data = DB::table('product_rate_list_template_test')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template_test.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template_test.company_id',$company_id)
                ->where('product_rate_list_template_test.status',1)
                ->groupBy('product_id');

            $mrp_pcs_rate = $mrp_pcs_rate_data->pluck('product_rate_list_template_test.mrp_pcs','product_rate_list_template_test.product_id as id');
            // dd($mrp_pcs_rate);
        #mrp ends here 

        # Retailer rate starts here 
        $retailer_case_rate_data = DB::table('product_rate_list_template_test')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template_test.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template_test.company_id',$company_id)
                ->where('product_rate_list_template_test.status',1);

                if(!empty($template))
                {
                    $retailer_case_rate_data->where('product_rate_list_template_test.template_type',$template);
                }
        $retailer_case_rate = $retailer_case_rate_data->pluck('retailer_rate',DB::raw("CONCAT(product_rate_list_template_test.product_id,product_rate_list_template_test.template_type) as data"));

        $retailer_pcs_rate_data = DB::table('product_rate_list_template_test')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template_test.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template_test.company_id',$company_id)
                ->where('product_rate_list_template_test.status',1);

                if(!empty($template))
                {
                    $retailer_pcs_rate_data->where('product_rate_list_template_test.template_type',$template);
                }
        $retailer_pcs_rate = $retailer_pcs_rate_data->pluck('retailer_pcs_rate',DB::raw("CONCAT(product_rate_list_template_test.product_id,product_rate_list_template_test.template_type) as data"));

        $retailer_primary_rate_data = DB::table('product_rate_list_template_test')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template_test.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template_test.company_id',$company_id)
                ->where('product_rate_list_template_test.status',1);

                if(!empty($template))
                {
                    $retailer_primary_rate_data->where('product_rate_list_template_test.template_type',$template);
                }
        $retailer_primary_rate = $retailer_primary_rate_data->pluck('other_retailer_rate',DB::raw("CONCAT(product_rate_list_template_test.product_id,product_rate_list_template_test.template_type) as data"));

        # Retailer rate ends here 


        # Distributor rate starts here 
        $distributor_case_rate_data = DB::table('product_rate_list_template_test')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template_test.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template_test.company_id',$company_id)
                ->where('product_rate_list_template_test.status',1);

                if(!empty($template))
                {
                    $distributor_case_rate_data->where('product_rate_list_template_test.template_type',$template);
                }
        $distributor_case_rate = $distributor_case_rate_data->pluck('dealer_rate',DB::raw("CONCAT(product_rate_list_template_test.product_id,product_rate_list_template_test.template_type) as data"));

        $distributor_pcs_rate_data = DB::table('product_rate_list_template_test')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template_test.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template_test.company_id',$company_id)
                ->where('product_rate_list_template_test.status',1);

                if(!empty($template))
                {
                    $distributor_pcs_rate_data->where('product_rate_list_template_test.template_type',$template);
                }
        $distributor_pcs_rate = $distributor_pcs_rate_data->pluck('dealer_pcs_rate',DB::raw("CONCAT(product_rate_list_template_test.product_id,product_rate_list_template_test.template_type) as data"));

        $distributor_primary_rate_data = DB::table('product_rate_list_template_test')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template_test.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template_test.company_id',$company_id)
                ->where('product_rate_list_template_test.status',1);

                if(!empty($template))
                {
                    $distributor_primary_rate_data->where('product_rate_list_template_test.template_type',$template);
                }
        $distributor_primary_rate = $distributor_primary_rate_data->pluck('other_dealer_rate',DB::raw("CONCAT(product_rate_list_template_test.product_id,product_rate_list_template_test.template_type) as data"));

        # Distributor rate ends here 

        # super stockiest rate starts here 
        $csa_case_rate_data = DB::table('product_rate_list_template_test')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template_test.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template_test.company_id',$company_id)
                ->where('product_rate_list_template_test.status',1);

                if(!empty($template))
                {
                    $csa_case_rate_data->where('product_rate_list_template_test.template_type',$template);
                }
        $csa_case_rate = $csa_case_rate_data->pluck('ss_case_rate',DB::raw("CONCAT(product_rate_list_template_test.product_id,product_rate_list_template_test.template_type) as data"));

        $csa_pcs_rate_data = DB::table('product_rate_list_template_test')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template_test.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template_test.company_id',$company_id)
                ->where('product_rate_list_template_test.status',1);

                if(!empty($template))
                {
                    $csa_pcs_rate_data->where('product_rate_list_template_test.template_type',$template);
                }
        $csa_pcs_rate = $csa_pcs_rate_data->pluck('ss_pcs_rate',DB::raw("CONCAT(product_rate_list_template_test.product_id,product_rate_list_template_test.template_type) as data"));

        $csa_primary_rate_data = DB::table('product_rate_list_template_test')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template_test.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template_test.company_id',$company_id)
                ->where('product_rate_list_template_test.status',1);

                if(!empty($template))
                {
                    $csa_primary_rate_data->where('product_rate_list_template_test.template_type',$template);
                }
        $csa_primary_rate = $csa_primary_rate_data->pluck('other_ss_rate',DB::raw("CONCAT(product_rate_list_template_test.product_id,product_rate_list_template_test.template_type) as data"));

        # super stockiest rate ends here 

        $query_fetch = DB::table('catalog_product')
                // ->join('catalog_product','catalog_product.id','=','product_rate_list_template_test.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->select('catalog_product.id as sku_id','catalog_product.name as sku_name','catalog_2.name as c2_name','catalog_1.name as c1_name','catalog_0.name as c0_name')
                ->where('catalog_product.company_id',$company_id)
                ->where('catalog_product.status',1);
              
        $sku_details = $query_fetch->get();
        // foreach ($statequery as $key => $value) 
        // {
        //    $stateList[$value->id]=$value->name;
        // }
        $location3 = DB::table('location_3')
                    ->where('company_id',$company_id)
                    ->where('status',1)
                    ->pluck('name','id');
        $location1 = DB::table('location_1')
                    ->where('company_id',$company_id)
                    ->where('status',1)
                    ->pluck('name','id');
        $distributor = DB::table('dealer')
                    ->where('company_id',$company_id)
                    ->where('dealer_status',1)
                    ->pluck('name','id');
        $csa = DB::table('csa')
                    ->where('company_id',$company_id)
                    ->where('active_status',1)
                    ->pluck('csa_name','c_id');
// dd($datas);
        return view($this->current_dir.'.testIndex',
            [
                'records' => $data,
                'sku_details'=> $sku_details,
                'template_array' => $template_array,
                'location3'=> $location3,
                'distributor'=> $distributor,
                'csa'=> $csa,
                'location1'=> $location1,
                'company_id'=> $company_id,

                'csa_case_rate'=> $csa_case_rate,
                'csa_pcs_rate'=> $csa_pcs_rate,
                'csa_primary_rate'=> $csa_primary_rate,

                'distributor_case_rate'=> $distributor_case_rate,
                'distributor_pcs_rate'=> $distributor_pcs_rate,
                'distributor_primary_rate'=> $distributor_primary_rate,

                'retailer_case_rate'=> $retailer_case_rate,
                'retailer_pcs_rate'=> $retailer_pcs_rate,
                'retailer_primary_rate'=> $retailer_primary_rate,

                'mrp_case_rate'=> $mrp_case_rate,
                'mrp_pcs_rate'=> $mrp_pcs_rate,

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
        $template_name=$request->template_name;

        $stateList = array();
        $catList = array();

        $other_rate_ist_data = array();
        $other_rate_ist_id_data = array();
        $check_assign = array();

        // if(!empty($cat_id) ){
        $sku_details = CatalogProduct::Join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
        ->Join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
        ->Join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
        ->where('catalog_2.company_id',$company_id)
        ->where('catalog_1.company_id',$company_id)
        ->where('catalog_0.company_id',$company_id)
        ->where('catalog_product.company_id',$company_id)
        ->select('catalog_product.id AS id','catalog_product.name AS name','product_type')
        ->where('catalog_product.status',1);
        if(!empty($cat_id))
        {
            $sku_details->where('catalog_2.id',$cat_id);
        }
        $sku = $sku_details->get();
        
        $product_rate_list_template_fixed_data = array();
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',4)->first();
        if(!empty($check))
        {
            $other_rate_ist_data_data = DB::table('catalog_product')
                            ->join('product_type','product_type.id','=','catalog_product.product_type')
                            ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                            ->where('product_type.name','!=','CASES')
                            ->where('product_type.company_id',$company_id)
                            ->groupBy('catalog_product.id');
                            // ->where('catalog_2.id',$cat_id)
                            if(!empty($cat_id))
                            {
                                $other_rate_ist_data_data->where('catalog_2.id','=',$cat_id);
                            }
                            
            $other_rate_ist_data = $other_rate_ist_data_data->pluck('product_type.name as name','catalog_product.id as id');

            $other_rate_ist_id_data_data = DB::table('catalog_product')
                            ->join('product_type','product_type.id','=','catalog_product.product_type')
                            ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                            ->where('product_type.name','!=','CASES')
                            ->where('product_type.company_id',$company_id)
                            ->groupBy('catalog_product.id');
                            // ->where('catalog_2.id',$cat_id)
                            if(!empty($cat_id))
                            {
                                $other_rate_ist_id_data_data->where('catalog_2.id','=',$cat_id);
                            }
                            

            $other_rate_ist_id_data = $other_rate_ist_id_data_data->pluck('product_type.id as ids','catalog_product.id as id');

            $check_assign = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',4)->get();

            // $product_rate_list_template_fixed_data = DB::table('product_rate_list_template')
            //                                         ->where('template_type',$request->template_name)
            //                                         ->groupBy('product_id')
            //                                         ->pluck(DB::raw("CONCAT(mrp,'|',mrp_pcs,'|',dealer_rate,'|',dealer_pcs_rate,'|',retailer_rate,'|',retailer_pcs_rate,'|',ss_case_rate,'|',ss_pcs_rate,'|',other_dealer_rate,'|',other_ss_rate,'|',other_retailer_rate)"),'product_id');
            // dd($product_rate_list_template_fixed_data);
          }                     //dd($other_rate_ist_data);
          $product_rate_list_template_fixed_data = DB::table('product_rate_list_template')
                                                    ->where('template_type',$request->template_name)
                                                    ->groupBy('product_id')
                                                    ->pluck(DB::raw("CONCAT(mrp,'|',mrp_pcs,'|',dealer_rate,'|',dealer_pcs_rate,'|',retailer_rate,'|',retailer_pcs_rate,'|',ss_case_rate,'|',ss_pcs_rate,'|',other_dealer_rate,'|',other_ss_rate,'|',other_retailer_rate)"),'product_id');
        // }else{
        //     $sku=[];
        // }
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
        $template_arary = DB::table('template_product')->where('status',1)->where('company_id',$company_id)->pluck('name','id');

        return view($this->current_dir.'.create',
            ['current_menu' => $this->current_menu,
            'stateList' => $stateList,
            'sku' => $sku,
            'other_rate_ist_data'=> $other_rate_ist_data,
            'other_rate_ist_id_data'=>$other_rate_ist_id_data,
            'check_assign'=> $check_assign,
            'template_arary'=> $template_arary,
            'product_rate_list_template_fixed_data'=> $product_rate_list_template_fixed_data,
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
        $template_name=$request->template_name;

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
            'template_type' => trim($template_name),
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
            'other_ss_rate'=>!empty($request->other_ss_rate[$key])?$request->other_ss_rate[$key]:'0',

            'company_id' => $company_id,

           
            'status'=> 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];
     $qcheck=DB::table('product_rate_list_template')
            ->where('company_id',$company_id)
            ->where('template_type',$request->template_name)
            ->where('product_id',$value)
            
            ->count();   
            // dd($qcheck);    
      if($qcheck<=0){
        // dd($myArr);
        $product_rate = DB::table('product_rate_list_template')->insert($myArr);
           }else{
        $product_rate = DB::table('product_rate_list_template')
        
        ->where('product_id',$value)
        ->where('template_type',$template_name)
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

        $product_rate_list_template_fetch = DB::table('product_rate_list_template')
                                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                                
                                ->select('product_rate_list_template.id as id','product_id','catalog_product.name as product_name','product_rate_list_template.*')
                                ->where('product_rate_list_template.id',$uid)->first();

        #fetch Project
        $check_assign_edit = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',4)->get();
        if(!empty($check_assign_edit))
        {
            $type_name_details = DB::table('catalog_product')
                            ->join('product_rate_list_template','catalog_product.id','=','product_rate_list_template.product_id')
                            ->join('product_type','product_type.id','=','catalog_product.product_type')
                           
                            ->select('product_type.name as type_name','product_type.id as type_id')
                            ->where('product_type.name','!=','CASES')
                            ->where('product_type.company_id',$company_id)
                            ->where('product_rate_list_template.id',$uid)
                            ->first();
                     
                            

           
        }
        // dd($type_name_details);

        #Catalog 1 data

        return view($this->current_dir.'.edit',
            [
                'encrypt_id' =>$id,
                'product_rate_list_template_fetch'=> $product_rate_list_template_fetch,
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
        $product_rate_list_template = DB::table('product_rate_list_template')->where('id',$id)->where('company_id',$company_id)->update($myArr);
        if($product_rate_list_template)
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
    public function filter_distributor_template(Request $request)
    {
        // dd($request);
        $location1 = $request->location1;
        $location2 = $request->location2;
        $location3 = $request->location3;
        $location4 = $request->location4;
        $location5 = $request->location5;
        $location6 = $request->location6;
        $csa = $request->csa;
        $distributor = $request->distributor;
        $company_id = Auth::user()->company_id;
        $product_rate_list_template_type_distributor = $request->product_rate_list_template_type_distributor;
        // dd($product_rate_list_template_type_distributor);
        $company_id = Auth::user()->company_id;
        $distributor_data = DB::table('dealer')
                            ->join('csa','csa.c_id','=','dealer.csa_id')
                            ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                            ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
                            ->select('dealer.*')
                            ->where('dealer_status',1)
                            ->where('dealer.company_id',$company_id);
                            if(!empty($distributor))
                            {
                                $distributor_data->whereIn('dealer.id',$distributor);
                            }
                            if(!empty($location1))
                            {
                                $distributor_data->whereIn('l1_id',$location1);
                            }
                            if(!empty($location2))
                            {
                                $distributor_data->whereIn('l2_id',$location2);
                            }
                            if(!empty($location3))
                            {
                                $distributor_data->whereIn('l3_id',$location3);
                            }
                            if(!empty($location4))
                            {
                                $distributor_data->whereIn('l4_id',$location4);
                            }
                            if(!empty($location5))
                            {
                                $distributor_data->whereIn('l5_id',$location5);
                            }
                            if(!empty($location6))
                            {
                                $distributor_data->whereIn('l6_id',$location6);
                            }
        $distributor = $distributor_data->groupBy('dealer.id')->get();
        // dd($distributor);
        $dealer_assign = DB::table('dealer')
                    ->join('product_rate_list_template','product_rate_list_template.template_type','=','dealer.template_id')
                    ->where('product_rate_list_template.company_id',$company_id)
                    ->where('product_rate_list_template.template_type',$product_rate_list_template_type_distributor)
                    ->groupBy('dealer.id')
                    ->pluck('dealer.id')->toArray();
        // dd($dealer_assign);
        return view('ajax.distributor_template_rate_list', [
                'rows' => $distributor,
                'product_rate_list_template_type_distributor'=>$product_rate_list_template_type_distributor,
                'dealer_assign'=> $dealer_assign,
                
            ]);

    }
    public function filter_csa_template(Request $request)
    {
        $location1 = $request->location1;
        $location2 = $request->location2;
        $location3 = $request->location3;
        $csa = $request->csa;
        $distributor = $request->distributor;
        $company_id = Auth::user()->company_id;
        $product_rate_list_template_type_csa = $request->product_rate_list_template_type_csa;


        $distributor_data = DB::table('csa')
                            ->join('location_3','location_3.id','=','csa.state_id')
                            ->join('location_2','location_2.id','=','location_3.location_2_id')
                            ->join('location_1','location_1.id','=','location_2.location_1_id')
                            ->select('location_1.name as l1_name','location_2.name as l2_name','location_3.name as l3_name','csa.*')
                            ->where('active_status',1)
                            ->where('csa.company_id',$company_id);
                            if(!empty($csa))
                            {
                                $distributor_data->whereIn('csa.c_id',$csa);
                            }
        $distributor = $distributor_data->get();
        return view('ajax.csa_template_rate_list', [
                'rows' => $distributor,
                'product_rate_list_template_type_csa'=> $product_rate_list_template_type_csa,
                
            ]);

    }
    public function filter_state_template(Request $request)
    {
        $location1 = $request->location1;
        $location2 = $request->location2;
        $location3 = $request->location3;
        $csa = $request->csa;
        $distributor = $request->distributor;
        $company_id = Auth::user()->company_id;
        $product_rate_list_template_type_state = $request->product_rate_list_template_type_state;

        $distributor_data = DB::table('location_3')
                            ->join('location_2','location_2.id','=','location_3.location_2_id')
                            ->join('location_1','location_1.id','=','location_2.location_1_id')
                            ->select('location_1.name as l1_name','location_2.name as l2_name','location_3.name as l3_name','location_3.*')
                            ->where('location_3.status',1)
                            ->where('location_3.company_id',$company_id);
                            if(!empty($state))
                            {
                                $distributor_data->whereIn('location_3.id',$state);
                            }
        $distributor = $distributor_data->get();
        return view('ajax.state_template_rate_list', [
                'rows' => $distributor,
                'product_rate_list_template_type_state'=> $product_rate_list_template_type_state,
                
            ]);

    }
    public function distributor_template_assign(Request $request)
    {
        // dd($request);
        $product_rate_list_template_type_distributor = !empty($request->product_rate_list_template_type_distributor)?$request->product_rate_list_template_type_distributor:'0';
        $dealer_check_test = !empty($request->dealer_check_test)?$request->dealer_check_test:array();
        $dealer_check_old = !empty($request->dealer_check_old)?$request->dealer_check_old:array();
        $company_id = Auth::user()->company_id;
        $insert_query ='';

        $template_type = DB::table('product_rate_list_template')
                        ->where('template_type',$product_rate_list_template_type_distributor)
                        ->get();
        // dd($dealer_check_old);
        if(!empty($dealer_check_test))
        {
           // foreach ($template_type as $t_key => $t_value) 
           //  {


           //      // foreach ($dealer_check_old as $d_key => $d_value) 
           //      // {
           //      //     // $insert_query = DB::table('product_rate_list')
           //      //     //             ->insert([
           //      //     //                 'product_id'=>$t_value->product_id,
           //      //     //                 'mrp'=>$t_value->mrp,
           //      //     //                 'template_id'=>$t_value->template_type,
           //      //     //                 'mrp_pcs'=>$t_value->mrp_pcs,
           //      //     //                 'dealer_rate'=>$t_value->dealer_rate,
           //      //     //                 'dealer_pcs_rate'=>$t_value->dealer_pcs_rate,
           //      //     //                 'retailer_rate'=>$t_value->retailer_rate,
           //      //     //                 'retailer_pcs_rate'=>$t_value->retailer_pcs_rate,
           //      //     //                 'ss_case_rate'=>$t_value->ss_case_rate,
           //      //     //                 'ss_pcs_rate'=>$t_value->ss_pcs_rate,
           //      //     //                 'other_retailer_rate'=>$t_value->other_retailer_rate,
           //      //     //                 'other_dealer_rate'=>$t_value->other_dealer_rate,
           //      //     //                 'other_ss_rate'=>$t_value->other_ss_rate,
           //      //     //                 'product_type_id'=>$t_value->product_type_id,
           //      //     //                 'state_id'=>0,
           //      //     //                 'ss_id'=>0,
           //      //     //                 'distributor_id'=>$d_value,
           //      //     //                 'company_id'=>$company_id,
           //      //     //                 'created_at'=>date('Y-m-d H:i:s'),

           //      //     //             ]);
                    

           //      // }
           //  } 
            // $insert_query_first = DB::table('dealer')->whereIn('id',$dealer_check_old)->update(['template_id'=>$product_rate_list_template_type_distributor]);
            $update_second_query = Dealer::whereIn('id',$dealer_check_test)->update(['template_id'=>$product_rate_list_template_type_distributor]);
            // dd($update_second_query);
            // if($insert_query_first || $update_second_query)
            // {
                // DB::commit();
                $data['code'] = 200;
                $data['result'] = 'Successfully Saved.';
                $data['message'] = 'Successfully Saved.';   
            // }
         

        }
        // elseif (!empty($dealer_check_old)) 
        // {
        //     $delete = DB::table('product_rate_list')->whereIn('distributor_id',$dealer_check_old)->delete();
        //     foreach ($template_type as $t_key => $t_value) 
        //     {
        //         foreach ($dealer_check_old as $d_key => $d_value) 
        //         {

        //             $insert_query = DB::table('product_rate_list')
        //                         ->insert([
        //                             'product_id'=>$t_value->product_id,
        //                             'mrp'=>$t_value->mrp,
        //                             'template_id'=>$t_value->template_type,
        //                             'mrp_pcs'=>$t_value->mrp_pcs,
        //                             'dealer_rate'=>$t_value->dealer_rate,
        //                             'dealer_pcs_rate'=>$t_value->dealer_pcs_rate,
        //                             'retailer_rate'=>$t_value->retailer_rate,
        //                             'retailer_pcs_rate'=>$t_value->retailer_pcs_rate,
        //                             'ss_case_rate'=>$t_value->ss_case_rate,
        //                             'ss_pcs_rate'=>$t_value->ss_pcs_rate,
        //                             'other_retailer_rate'=>$t_value->other_retailer_rate,
        //                             'other_dealer_rate'=>$t_value->other_dealer_rate,
        //                             'other_ss_rate'=>$t_value->other_ss_rate,
        //                             'product_type_id'=>$t_value->product_type_id,
        //                             'state_id'=>0,
        //                             'ss_id'=>0,
        //                             'distributor_id'=>$d_value,
        //                             'company_id'=>$company_id,
        //                             'created_at'=>date('Y-m-d H:i:s'),

        //                         ]);
        //         }
        //     } 
        //     // dd($insert_query);
        //     if($insert_query)
        //     {
        //         DB::commit();
        //         Session::flash('message', Lang::get('common.'.$this->current_menu).' created successfully');
        //         Session::flash('class', 'success');


        //         return redirect()->intended($this->current_menu);
        //     }
        //     else
        //     {
        //         DB::rollback();
        //         return redirect()->intended($this->current_menu);
        //     }
        // }
        else
        {
            $data['code'] = 234;
            $data['result'] = '';
            $data['message'] = 'Select Something.';   

        }
        
        return json_encode($data);

        

    }
    public function csa_template_assign(Request $request)
    {
        $product_rate_list_template_type_csa = $request->product_rate_list_template_type_csa;
        $csa_check = $request->csa_check;
        $company_id = Auth::user()->company_id;

        $template_type = DB::table('product_rate_list_template')
                        ->where('template_type',$product_rate_list_template_type_csa)
                        ->get();

        foreach ($template_type as $t_key => $t_value) 
        {
            foreach ($csa_check as $d_key => $d_value) 
            {
                // $insert_query = DB::table('product_rate_list')
                //             ->insert([
                //                 'product_id'=>$t_value->product_id,
                //                 'mrp'=>$t_value->mrp,
                //                 'template_id'=>$t_value->template_type,
                //                 'mrp_pcs'=>$t_value->mrp_pcs,
                //                 'dealer_rate'=>$t_value->dealer_rate,
                //                 'dealer_pcs_rate'=>$t_value->dealer_pcs_rate,
                //                 'retailer_rate'=>$t_value->retailer_rate,
                //                 'retailer_pcs_rate'=>$t_value->retailer_pcs_rate,
                //                 'ss_case_rate'=>$t_value->ss_case_rate,
                //                 'ss_pcs_rate'=>$t_value->ss_pcs_rate,
                //                 'other_retailer_rate'=>$t_value->other_retailer_rate,
                //                 'other_dealer_rate'=>$t_value->other_dealer_rate,
                //                 'other_ss_rate'=>$t_value->other_ss_rate,
                //                 'product_type_id'=>$t_value->product_type_id,
                //                 'state_id'=>0,
                //                 'ss_id'=>$d_value,
                //                 'distributor_id'=>0,
                //                 'company_id'=>$company_id,
                //                 'created_at'=>date('Y-m-d H:i:s'),

                //             ]);
                $insert_query = DB::table('csa')->where('id',$d_value)->update(['template_id'=>$t_value->template_type]);
            }
        }

        if($insert_query)
        {

            DB::commit();
            Session::flash('message', Lang::get('common.'.$this->current_menu).' created successfully');
            Session::flash('class', 'success');


            return redirect()->intended($this->current_menu);
        }
        else
        {
            DB::rollback();
            return redirect()->intended($this->current_menu);
        }

    }
    public function state_template_assign(Request $request)
    {
        $product_rate_list_template_type_state = $request->product_rate_list_template_type_state;
        // dd($product_rate_list_template_type_state);

        $dealer_check = $request->dealer_check;
        $company_id = Auth::user()->company_id;

        $template_type = DB::table('product_rate_list_template')
                        ->where('template_type',$product_rate_list_template_type_state)
                        ->get();
        // dd($template_type);
        foreach ($template_type as $t_key => $t_value) 
        {
            foreach ($dealer_check as $d_key => $d_value) 
            {
                $insert_query = DB::table('product_rate_list')
                            ->insert([
                                'product_id'=>$t_value->product_id,
                                'mrp'=>$t_value->mrp,
                                'mrp_pcs'=>$t_value->mrp_pcs,
                                'template_id'=>$t_value->template_type,
                                'dealer_rate'=>$t_value->dealer_rate,
                                'dealer_pcs_rate'=>$t_value->dealer_pcs_rate,
                                'retailer_rate'=>$t_value->retailer_rate,
                                'retailer_pcs_rate'=>$t_value->retailer_pcs_rate,
                                'ss_case_rate'=>$t_value->ss_case_rate,
                                'ss_pcs_rate'=>$t_value->ss_pcs_rate,
                                'other_retailer_rate'=>$t_value->other_retailer_rate,
                                'other_dealer_rate'=>$t_value->other_dealer_rate,
                                'other_ss_rate'=>$t_value->other_ss_rate,
                                'product_type_id'=>$t_value->product_type_id,
                                'state_id'=>$d_value,
                                'ss_id'=>0,
                                'distributor_id'=>0,
                                'company_id'=>$company_id,
                                'created_at'=>date('Y-m-d H:i:s'),

                            ]);
            }
        }

        if($insert_query)
        {
            DB::commit();
            Session::flash('message', Lang::get('common.'.$this->current_menu).' created successfully');
            Session::flash('class', 'success');


            // return redirect()->intended($this->current_menu);
        }
        else
        {
            DB::rollback();
            return redirect()->intended($this->current_menu);
        }

    }
    public function get_template_type(Request $request)
    {
        $template_type = $request->template_type;
        // dd($template_type);
        $company_id = Auth::user()->company_id;
        $query = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->select('product_rate_list_template.mrp as mrp','catalog_product.name as sku_name','itemcode as item_code','catalog_1.name as c1_name','catalog_2.name as c2_name','catalog_0.name as c0_name','mrp as cases_mrp','mrp_pcs as mrp','ss_case_rate as ss_cases_rate','ss_pcs_rate as ss_rate','dealer_rate as dealer_cases_rate','dealer_pcs_rate as dealer_rate','retailer_rate as retailer_cases_rate','retailer_pcs_rate as retailer_rate')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1)
                ->where('template_type',$template_type)
                ->get();
        // dd($query);
        if(!empty($query))
        {
            $data['code'] = 200;
            $data['result'] = $query;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    public function UploadTemmplate(Request $request)
    {
        // dd($request);
        $company_id = Auth::user()->company_id;
        if(!empty($request->excelFile)){
            if($request->submit!="productRateListTemplate")
            {

                $csv_file = $request->excelFile;
                if (($getfile = fopen($csv_file, "r")) !== FALSE) 
                {
                    // dd($getfile);
                    $data = fgetcsv($getfile, 3000, ",");
                    $data = fgetcsv($getfile, 3000, ",");
                    $data = fgetcsv($getfile, 3000, ",");
                    // dd($data);
                    $inum=3;
                    $query = " ";
                    $ch_data=array();
                    DB::beginTransaction();
                    while (($data = fgetcsv($getfile, 3000, ",")) !== FALSE) 
                    {
                        // dd($data);
                        $tempArry = [];
                        $curr_date = date('Y-m-d H:m:s');
                        $result = $data;
                         // dd($result);
                        $numOfCols = 9;
                        $rowCount = 0;
                         foreach ($result as $key => $value) 
                         {
                             // dd($result[1]);
                            if($key == 12)
                            {
                                $template_id = DB::table('template_product')
                                        ->where('status',1)
                                        ->where('company_id',$company_id)
                                        ->whereNotIn('id',$tempArry)
                                        ->orderBy('id','ASC')
                                        ->first();
                                $tempArry[] = $template_id->id;

                                $delete_data = DB::table('product_rate_list_template')
                                            // ->where('product_id',$result[1])
                                            ->where('template_type',$template_id->id)
                                            ->delete();
                                $myArr = [
                                    'product_id' => ($result[1]),
                                    'template_type' => $template_id->id,
                                    'mrp' => $result[2],
                                    'mrp_pcs' => $result[3],
                                    'ss_case_rate' => $result[4],
                                    'ss_pcs_rate' => $result[5],
                                    'other_ss_rate'=> $result[6],

                                    'dealer_rate' => $result[7],
                                    'dealer_pcs_rate' => $result[8],
                                    'other_dealer_rate'=> $result[9],

                                    'retailer_rate' => $result[10],
                                    'retailer_pcs_rate' => $result[11],
                                    'other_retailer_rate'=> $result[12],

                                    'company_id' => $company_id,
                                    'status'=> 1,
                                    'created_at' => date('Y-m-d H:i:s'),
                                ];
                                // dd($myArr);
                                    $f_arr[] = $myArr;
                                // $query = DB::table('product_rate_list_template_test')->insert($myArr);
                            }
                            elseif($key > 12)
                            {
                                $rowCount++;
                                if($rowCount % $numOfCols == 0) {
                                    // dd($key);

                                    $template_id = DB::table('template_product')
                                        ->where('status',1)
                                        ->where('company_id',$company_id)
                                        ->whereNotIn('id',$tempArry)
                                        ->orderBy('id','ASC')
                                        ->first();
                                    $tempArry[] = $template_id->id;
                                    $delete_data = DB::table('product_rate_list_template')
                                            // ->where('product_id',$result[1])
                                            ->where('template_type',$template_id->id)
                                            ->delete();
                                    $myArr = [
                                        'product_id' => ($result[1]),
                                        'template_type' => $template_id->id,
                                        'mrp' => $result[2],
                                        'mrp_pcs' => $result[3],
                                        'ss_case_rate' => !empty($result[$key-8])?$result[$key-8]:'0',
                                        'ss_pcs_rate' => !empty($result[$key-7])?$result[$key-7]:'0',
                                        'other_ss_rate' => !empty($result[$key-6])?$result[$key-6]:'0',
                                        
                                        'dealer_rate' => !empty($result[$key-5])?$result[$key-5]:'0',
                                        'dealer_pcs_rate' => !empty($result[$key-4])?$result[$key-4]:'0',
                                        'other_dealer_rate' => !empty($result[$key-3])?$result[$key-3]:'0',
                                        
                                        'retailer_rate' => !empty($result[$key-2])?$result[$key-2]:'0',
                                        'retailer_pcs_rate' => !empty($result[$key-1])?$result[$key-1]:'0',
                                        'other_retailer_rate'=> !empty($result[$key])?$result[$key]:'0',
                                        // 'other_dealer_rate'=> !empty($result[$key-2])?$result[$key-2]:'22',
                                        // 'other_ss_rate'=> !empty($result[$key-1])?$result[$key-1]:'22',
                                        
                                        'company_id' => $company_id,
                                        'status'=> 1,
                                        'created_at' => date('Y-m-d H:i:s'),
                                    ];
                                    $f_arr[] = $myArr;
                                    // $query = DB::table('product_rate_list_template_test')->insert($myArr);

                                };
                            }
                        }
                    }
                // dd($f_arr);
                    $query = DB::table('product_rate_list_template')->insert($f_arr);
                    // dd($query);
                    if ($query) 
                    {
                        DB::commit();
                        Session::flash('message', 'Uploaded Succesfully');
                        Session::flash('alert-class', 'alert-success');
                    }
                    else 
                    {
                        DB::rollback();
                        Session::flash('message', 'Something went wrong!');
                        Session::flash('alert-class', 'alert-danger');
                    }
                }
                return redirect('product_rate_list_template');

            }


        }
    }
    public function productRateListTemplateFormat(Request $request)
    {
       
        $output ='';
        $company_id = Auth::user()->company_id;
        $template_data = DB::table('template_product')
                                    ->where('status',1)
                                    ->where('company_id',$company_id)
                                    ->orderBy('id','ASC')
                                    ->get();
        $output .=",,,,";
        foreach ($template_data as $key => $value) 
        {
            $output .=$value->name.',,,,,,,,,';
        }
        $output .="\n";
        $output .="SKU Name,SKU Id,Mrp.,Mrp. Pcs,";
        foreach ($template_data as $key => $value) 
        {
            $output .="Super Stockist Cases Rate,Super Stockist Pcs Rate ,SuperStockist Primary Rate,Distributor Cases Rate,Distributor Pcs Rate,Distributor Primary Rate,Retailer Cases Rate,Retailer Pcs Rate,Retailer Primary Rate,";
        }
        $output .="\n";

        $template=DB::table('template_product')
        ->where('status',1)
        ->where('company_id',$company_id)
        // ->orderBy('name','ASC')
        ->pluck("id");
        $data_fetch = DB::table('product_rate_list_template')
                    ->join('template_product','template_product.id','=','product_rate_list_template.template_type')
                    ->select('product_rate_list_template.status as status','template_product.name as template_type','product_rate_list_template.template_type as id')
                    ->where('product_rate_list_template.company_id',$company_id)
                    ->where('product_rate_list_template.status',1)
                    ->groupBy('template_product.id');
                    // ->get();
        if(!empty($template))
        {
            $data_fetch->whereIn('product_rate_list_template.template_type',$template);
        }
        $data = $data_fetch->get();
        // dd($data);
        $template_array=DB::table('template_product')
        ->where('status',1)
        ->where('company_id',$company_id)
        // ->orderBy('name','ASC')
        ->pluck("name","id");



        // ->select('product_rate_list_template.mrp as mrp','catalog_product.name as sku_name','itemcode as item_code','catalog_1.name as c1_name','catalog_2.name as c2_name','catalog_0.name as c0_name','mrp as cases_mrp','mrp_pcs as mrp','ss_case_rate as ss_cases_rate','ss_pcs_rate as ss_rate','dealer_rate as dealer_cases_rate','dealer_pcs_rate as dealer_rate','retailer_rate as retailer_cases_rate','retailer_pcs_rate as retailer_rate')
        
        #mrp starts here 

            $mrp_case_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1)
                ->groupBy('product_id');

          
            $mrp_case_rate = $mrp_case_rate_data->pluck('product_rate_list_template.mrp','product_rate_list_template.product_id as id');

            $mrp_pcs_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1)
                ->groupBy('product_id');

            $mrp_pcs_rate = $mrp_pcs_rate_data->pluck('product_rate_list_template.mrp_pcs','product_rate_list_template.product_id as id');
            // dd($mrp_pcs_rate);
        #mrp ends here 

        # Retailer rate starts here 
        $retailer_case_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                // ->where('template_type')
                ->where('product_rate_list_template.status',1);

                if(!empty($template))
                {
                    $retailer_case_rate_data->whereIn('product_rate_list_template.template_type',$template);
                }
        $retailer_case_rate = $retailer_case_rate_data->pluck('retailer_rate',DB::raw("CONCAT(product_rate_list_template.product_id,product_rate_list_template.template_type) as data"));

        $retailer_pcs_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1);

                if(!empty($template))
                {
                    $retailer_pcs_rate_data->whereIn('product_rate_list_template.template_type',$template);
                }
        $retailer_pcs_rate = $retailer_pcs_rate_data->pluck('retailer_pcs_rate',DB::raw("CONCAT(product_rate_list_template.product_id,product_rate_list_template.template_type) as data"));

        $retailer_primary_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1);

                if(!empty($template))
                {
                    $retailer_primary_rate_data->whereIn('product_rate_list_template.template_type',$template);
                }
        $retailer_primary_rate = $retailer_primary_rate_data->pluck('other_retailer_rate',DB::raw("CONCAT(product_rate_list_template.product_id,product_rate_list_template.template_type) as data"));

        # Retailer rate ends here 


        # Distributor rate starts here 
        $distributor_case_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1);

                if(!empty($template))
                {
                    $distributor_case_rate_data->whereIn('product_rate_list_template.template_type',$template);
                }
        $distributor_case_rate = $distributor_case_rate_data->pluck('dealer_rate',DB::raw("CONCAT(product_rate_list_template.product_id,product_rate_list_template.template_type) as data"));

        $distributor_pcs_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1);

                if(!empty($template))
                {
                    $distributor_pcs_rate_data->whereIn('product_rate_list_template.template_type',$template);
                }
        $distributor_pcs_rate = $distributor_pcs_rate_data->pluck('dealer_pcs_rate',DB::raw("CONCAT(product_rate_list_template.product_id,product_rate_list_template.template_type) as data"));

        $distributor_primary_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1);

                if(!empty($template))
                {
                    $distributor_primary_rate_data->whereIn('product_rate_list_template.template_type',$template);
                }
        $distributor_primary_rate = $distributor_primary_rate_data->pluck('other_dealer_rate',DB::raw("CONCAT(product_rate_list_template.product_id,product_rate_list_template.template_type) as data"));

        # Distributor rate ends here 

        # super stockiest rate starts here 
        $csa_case_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1);

                if(!empty($template))
                {
                    $csa_case_rate_data->whereIn('product_rate_list_template.template_type',$template);
                }
        $csa_case_rate = $csa_case_rate_data->pluck('ss_case_rate',DB::raw("CONCAT(product_rate_list_template.product_id,product_rate_list_template.template_type) as data"));

        $csa_pcs_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1);

                if(!empty($template))
                {
                    $csa_pcs_rate_data->whereIn('product_rate_list_template.template_type',$template);
                }
        $csa_pcs_rate = $csa_pcs_rate_data->pluck('ss_pcs_rate',DB::raw("CONCAT(product_rate_list_template.product_id,product_rate_list_template.template_type) as data"));

        $csa_primary_rate_data = DB::table('product_rate_list_template')
                ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->where('product_rate_list_template.company_id',$company_id)
                ->where('product_rate_list_template.status',1);

                if(!empty($template))
                {
                    $csa_primary_rate_data->whereIn('product_rate_list_template.template_type',$template);
                }
        $csa_primary_rate = $csa_primary_rate_data->pluck('other_ss_rate',DB::raw("CONCAT(product_rate_list_template.product_id,product_rate_list_template.template_type) as data"));

        # super stockiest rate ends here 

        $query_fetch = DB::table('catalog_product')
                // ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
                ->select('catalog_product.id as sku_id','catalog_product.name as sku_name','catalog_2.name as c2_name','catalog_1.name as c1_name','catalog_0.name as c0_name')
                ->where('catalog_product.company_id',$company_id)
                ->where('catalog_product.status',1);
              
        $sku_details = $query_fetch->get();
            

                 
        if(!empty($sku_details))
        {
           foreach ($sku_details as $b_key => $b_value) 
            {
                $output .= $b_value->sku_name.',';
                $output .= $b_value->sku_id.',';                
                $output .= !empty($mrp_case_rate[$b_value->sku_id])?$mrp_case_rate[$b_value->sku_id].',':'0'.',';
                $output .= !empty($mrp_pcs_rate[$b_value->sku_id])?$mrp_pcs_rate[$b_value->sku_id].',':'0'.',';
                foreach($data as $b_r__key => $b_r_data)
                {
                    $output .= !empty($csa_case_rate[$b_value->sku_id.$b_r_data->id])?$csa_case_rate[$b_value->sku_id.$b_r_data->id].',':'0'.',';
                    $output .= !empty($csa_pcs_rate[$b_value->sku_id.$b_r_data->id])?$csa_pcs_rate[$b_value->sku_id.$b_r_data->id].',':'0'.',';
                    $output .= !empty($csa_primary_rate[$b_value->sku_id.$b_r_data->id])?$csa_primary_rate[$b_value->sku_id.$b_r_data->id].',':'0'.',';
                    $output .= !empty($distributor_case_rate[$b_value->sku_id.$b_r_data->id])?$distributor_case_rate[$b_value->sku_id.$b_r_data->id].',':'0'.',';
                    $output .= !empty($distributor_pcs_rate[$b_value->sku_id.$b_r_data->id])?$distributor_pcs_rate[$b_value->sku_id.$b_r_data->id].',':'0'.',';
                    $output .= !empty($distributor_primary_rate[$b_value->sku_id.$b_r_data->id])?$distributor_primary_rate[$b_value->sku_id.$b_r_data->id].',':'0'.',';
                    $output .= !empty($retailer_case_rate[$b_value->sku_id.$b_r_data->id])?$retailer_case_rate[$b_value->sku_id.$b_r_data->id].',':'0'.',';
                    $output .= !empty($retailer_pcs_rate[$b_value->sku_id.$b_r_data->id])?$retailer_pcs_rate[$b_value->sku_id.$b_r_data->id].',':'0'.',';
                    $output .= !empty($retailer_primary_rate[$b_value->sku_id.$b_r_data->id])?$retailer_primary_rate[$b_value->sku_id.$b_r_data->id].',':'0'.',';
                }
                
                $output .="\n";

            } 
        }
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=ProductRateListTemplateFormat.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        echo $output;   
            

    }

}
