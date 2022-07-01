<?php

namespace App\Http\Controllers;


use App\UserDetail;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use App\Stock;
use App\Helpers\LocationArray;


class DealerPrimarySaleController extends Controller
{ 
    public function __construct()
    {
        $this->current = 'stock';
    }

public function show(Request $request,$id)
    {
        $arr = [];
        #decrypt id
        $distributor = Crypt::decryptString($id);

        $explodeDate = explode(" -", $request->date_range_picker);
            $user_id = $request->user;
            $region = $request->region;
            $state = $request->area;
            $town = $request->territory;
            $beat = $request->belt;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
           $company_id = Auth::user()->company_id;
           $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();




            $query_data = DB::table('user_primary_sales_order')
                ->select('location_view.l6_name as l4_name', DB::raw('CONCAT(person.first_name," ",person.last_name) as user_name'), 'dealer.name as dealer_name', 'user_primary_sales_order.*')
                ->leftJoin('person', 'person.id', '=', 'user_primary_sales_order.created_person_id')
                ->join('dealer', 'dealer.id', '=', 'user_primary_sales_order.dealer_id')
                ->leftJoin('dealer_location_rate_list', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                ->leftJoin('location_view', 'dealer_location_rate_list.location_id', '=', 'location_view.l7_id')
                ->whereRaw("DATE_FORMAT(user_primary_sales_order.created_date, '%Y-%m-%d')>='$from_date' and DATE_FORMAT(user_primary_sales_order.created_date,'%Y-%m-%d') <='$to_date'")
                ->where('dealer.id',$distributor);


            $tmp = array();
            // To find No of user under certain role 
            $flag = [];
            if (!empty($request->role)) {
                $roleArr = $request->role;
                if (!empty($roleArr)) {
                    $flag = DB::table('person')->whereIn('role_id', $roleArr)->pluck('id');
                }
            }

            if (!empty($user)) {
                $flag = [];
                $flag = $request->user;

            }

            if (!empty($flag)) {
                $query_data->whereIn('user_primary_sales_order.created_person_id', $flag);
            }

        

            $mid_query = $query_data
                ->groupBy('l6_id', 'user_name', 'dealer_name', 'id', 'order_id', 'dealer_id', 'created_date', 'created_person_id', 'sale_date', 'receive_date', 'date_time', 'company_id', 'ch_date', 'challan_no', 'csa_id', 'action', 'is_claim', 'sync_status')->orderBy('sale_date');
            $d = $mid_query->get();
            $idArr = $mid_query->pluck('order_id')->toArray();
            $orderArr = !empty($idArr) ? array_unique($idArr) : [];

            if(empty($check)){
            $order_details = DB::table('user_primary_sales_order_details')
                ->leftJoin('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                ->whereIn('order_id', $orderArr)
                ->select('catalog_product.name','catalog_product.weight','user_primary_sales_order_details.rate','user_primary_sales_order_details.cases','user_primary_sales_order_details.cases','user_primary_sales_order_details.pcs','user_primary_sales_order_details.pr_rate','user_primary_sales_order_details.order_id',DB::raw("((user_primary_sales_order_details.rate*user_primary_sales_order_details.pcs)+(user_primary_sales_order_details.cases*user_primary_sales_order_details.pr_rate)) as sale_value"))
                ->get();
            }else{
                $order_details = DB::table('user_primary_sales_order_details')
                ->leftJoin('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                ->whereIn('order_id', $orderArr)
                ->select('catalog_product.name','catalog_product.weight','user_primary_sales_order_details.rate','user_primary_sales_order_details.cases','user_primary_sales_order_details.cases','user_primary_sales_order_details.pcs','user_primary_sales_order_details.pr_rate','user_primary_sales_order_details.order_id',DB::raw("((user_primary_sales_order_details.final_secondary_rate*user_primary_sales_order_details.final_secondary_qty)) as sale_value"))
                ->get();
            }
            $orderDetailArr = [];
            foreach ($order_details as $od) {
                $orderDetailArr[$od->order_id][] = $od;

            }
            return view('reports.distributer-dashboard.primary_sale', [
                'records' => $d,
                'order_detial_arr' => $orderDetailArr,
                'id'=>$id
            ]);
        
    }
}
