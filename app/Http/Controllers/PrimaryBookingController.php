<?php
namespace App\Http\Controllers;
use App\UserDetail;
use Illuminate\Http\Request;
use Auth;
use DB; 
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class PrimaryBookingController extends Controller
{
    public function __construct()
    {
        $this->current_menu = 'booking';
    }

    public function show(Request $request,$id)
    {
        $arr = [];
        #decrypt id
        $uid = Crypt::decryptString($id);
        // dd($uid);
        $from_date=!empty($request->start_date)?$request->start_date:date('Y-m-d');
        $to_date=!empty($request->end_date)?$request->end_date:date('Y-m-d');
        $company_id = Auth::user()->company_id;
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

        if(empty($check)){
        $sale = DB::table('user_primary_sales_order')
                ->join('dealer', 'dealer.id', '=', 'user_primary_sales_order.dealer_id')
                ->join('csa','csa.c_id','=','dealer.csa_id')
                ->join('dealer_location_rate_list', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                ->join('person', 'person.id', '=', 'dealer_location_rate_list.user_id')
                ->join('location_view', 'dealer_location_rate_list.location_id', '=', 'location_view.l7_id')
                ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                ->select('dealer_location_rate_list.user_id','l3_id','csa_name','csa_code','dealer_code','location_view.l4_name', DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'person.id as uid', 'dealer.name as dealer_name','dealer.id as did', 'user_primary_sales_order.*','catalog_product.name','catalog_product.weight','user_primary_sales_order_details.rate','user_primary_sales_order_details.cases','user_primary_sales_order_details.cases','user_primary_sales_order_details.pcs as pcs','user_primary_sales_order_details.pr_rate','user_primary_sales_order_details.order_id',DB::raw("((user_primary_sales_order_details.rate*user_primary_sales_order_details.pcs)+(user_primary_sales_order_details.cases*user_primary_sales_order_details.pr_rate)) as total_sale"))
                ->whereRaw("DATE_FORMAT(user_primary_sales_order.created_date, '%Y-%m-%d')>='$from_date' and DATE_FORMAT(user_primary_sales_order.created_date,'%Y-%m-%d') <='$to_date'")
                ->where('user_primary_sales_order.company_id',$company_id)
                ->where('dealer_location_rate_list.user_id',$uid)
                ->groupBy('user_primary_sales_order_details.order_id','product_id','user_primary_sales_order.created_date','dealer_id','dealer_location_rate_list.user_id')
                ->get();
        }else{
            $sale = DB::table('user_primary_sales_order')
                ->join('dealer', 'dealer.id', '=', 'user_primary_sales_order.dealer_id')
                ->join('csa','csa.c_id','=','dealer.csa_id')
                ->join('dealer_location_rate_list', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                ->join('person', 'person.id', '=', 'dealer_location_rate_list.user_id')
                ->join('location_view', 'dealer_location_rate_list.location_id', '=', 'location_view.l7_id')
                ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                ->select('dealer_location_rate_list.user_id','l3_id','csa_name','csa_code','dealer_code','location_view.l4_name', DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'person.id as uid', 'dealer.name as dealer_name','dealer.id as did', 'user_primary_sales_order.*','catalog_product.name','catalog_product.weight','user_primary_sales_order_details.rate','user_primary_sales_order_details.cases','user_primary_sales_order_details.cases','user_primary_sales_order_details.pcs as pcs','user_primary_sales_order_details.pr_rate','user_primary_sales_order_details.order_id',DB::raw("((user_primary_sales_order_details.final_secondary_rate*user_primary_sales_order_details.final_secondary_qty)) as total_sale"))
                ->whereRaw("DATE_FORMAT(user_primary_sales_order.created_date, '%Y-%m-%d')>='$from_date' and DATE_FORMAT(user_primary_sales_order.created_date,'%Y-%m-%d') <='$to_date'")
                ->where('user_primary_sales_order.company_id',$company_id)
                ->where('dealer_location_rate_list.user_id',$uid)
                ->groupBy('user_primary_sales_order_details.order_id','product_id','user_primary_sales_order.created_date','dealer_id','dealer_location_rate_list.user_id')
                ->get();
        }



        return view('userDashboard.primaryBooking', [
            'sale_data' => $sale,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'id' => $id
        ]);
    }

    
}
