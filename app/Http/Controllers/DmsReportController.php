<?php

namespace App\Http\Controllers;

use App\_outletType;
use App\Area;
use App\Catalog1;
use App\Claim;
use App\competitorBrand;
use App\CompetitorsPriceLog;
use App\competitorsProduct;
use App\DailyStock;
use App\Dealer;
use App\DealerLocation;
use App\DealerRetailer;
use App\DistributorTarget;
use App\Feedback;
use App\Location1;
use App\Location2;
use App\Location6;
use App\Location7;
use App\Location3;
use App\Location4;
use App\Location5; 
use App\MonthlyTourProgram;
use App\ReceiveOrder;
use App\Retailer;
use App\User;
use App\SS;
use App\Person;
use App\UserDealerRetailer;
use App\UserDetail;
use App\UserExpenseReport;
use App\UserSalesOrder;
use App\Vendor;
use App\CatalogProduct;
use App\UsersDetail;
use Illuminate\Http\Request;
use DB;
use Auth;
use Session;
use DateTime;
use PDF;


class DmsReportController extends Controller
{
  
   public function dms_sale_order_report(Request $request)
   {
   		$company_id = Auth::user()->company_id;
   		$dealer = Dealer::where('dealer_status',1)->where('company_id',$company_id)->pluck('name','id');
   		$product_filter = CatalogProduct::where('status',1)->where('company_id',$company_id)->pluck('name','id');
   		$l3_filter = Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        return view('reports.dms_reports.index', [
	            'dealer' => $dealer,
	            'product_filter' => $product_filter,
	            'l3_filter' => $l3_filter,
        	]);

   }
   public function dms_order_booking_report_final(Request $request)
   {
   		$company_id = Auth::user()->company_id;
   		$dealer_id = $request->dealer_id;
   		$state_id = $request->state_id;
   		$product_id = $request->product_id;
   		$status = $request->status;
   		$explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

   		$query_data = DB::table('purchase_order')
                        ->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
                    	->join('catalog_product','catalog_product.id','=','purchase_order_details.product_id')
                        ->join('dealer','dealer.id','=','purchase_order.dealer_id')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                        ->join('person','person.id','=','dealer_location_rate_list.user_id')
                        ->join('users','users.id','=','person.id')
                        ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
                        // ->join('_dms_reason','_dms_reason.id','=','purchase_order.dms_order_reason_id')
                        ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'l3_name','l6_name','pdf_name2','pdf_name','dms_order_reason_id','purchase_order.order_id as order_id','purchase_order.dealer_id as dealer_id','dealer.other_numbers as mobile_number','dealer.name as dealer_name','sale_date','dealer.email as d_email','product_id','cases','pcs as quantity','pr_rate as cases_rate','rate','catalog_product.name as product_name','scheme_qty as scheme_qty','invoice_date','invoice_no_p')
                        ->where('purchase_order.company_id',$company_id)
                        ->where('purchase_order_details.company_id',$company_id)
                        ->where('dealer.company_id',$company_id)
                        ->where('is_admin','!=',1)
                        ->where('purchase_order_details.app_flag','!=',1)
                        ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(sale_date,'%Y-%m-%d')<='$to_date'")
                        ->groupBy('purchase_order_details.order_id','product_id');
                        // ->get();

           	if(!empty($dealer_id))
            {   
                $query_data->whereIn('purchase_order.dealer_id',$dealer_id);
            }
            if(!empty($state_id))
            {   
                $query_data->whereIn('location_3.id',$state_id);
            }
            if(!empty($product_id))
            {   
                $query_data->whereIn('catalog_product.id',$product_id);
            }
            
        $query=$query_data->get();
        // dd($query);
        return view('reports.dms_reports.ajax', [
	            'records' => $query,
        	]);

   	}
    public function dms_sale_order_report_final(Request $request)
   {
        $company_id = Auth::user()->company_id;
        $dealer_id = $request->dealer_id;
        $state_id = $request->state_id;
        $product_id = $request->product_id;
        $status = $request->status;
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

        $query_data = DB::table('fullfillment_order')
                        ->join('fullfillment_order_details','fullfillment_order_details.order_id','=','fullfillment_order.order_id')
                        ->join('catalog_product','catalog_product.id','=','fullfillment_order_details.product_id')
                        ->join('dealer','dealer.id','=','fullfillment_order.dealer_id')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                        ->join('person','person.id','=','dealer_location_rate_list.user_id')
                        ->join('users','users.id','=','person.id')
                        ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
                        ->join('purchase_order','purchase_order.order_id','=','fullfillment_order.order_id')
                        ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'l3_name','l6_name','fullfillment_order.order_id as order_id','fullfillment_order.dealer_id as dealer_id','dealer.other_numbers as mobile_number','dealer.name as dealer_name','order_date as sale_date','dealer.email as d_email','product_id','product_fullfiment_cases as cases','product_fullfiment_qty as quantity','product_case_rate as cases_rate','product_qty_cases as rate','catalog_product.name as product_name','product_fullfiment_scheme_qty as scheme_qty','invoice_date','invoice_no_p')
                        ->where('fullfillment_order.company_id',$company_id)
                        ->where('fullfillment_order_details.company_id',$company_id)
                        ->where('dealer.company_id',$company_id)
                        ->where('is_admin','!=',1)
                        // ->where('fullfillment_order_details.app_flag','!=',1)
                        ->whereRaw("DATE_FORMAT(fullfillment_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(fullfillment_order.date,'%Y-%m-%d')<='$to_date'")
                        ->groupBy('fullfillment_order_details.order_id','product_id');
                        // ->get();

            if(!empty($dealer_id))
            {   
                $query_data->whereIn('purchase_order.dealer_id',$dealer_id);
            }
            if(!empty($state_id))
            {   
                $query_data->whereIn('location_3.id',$state_id);
            }
            if(!empty($product_id))
            {   
                $query_data->whereIn('catalog_product.id',$product_id);
            }
            
        $query=$query_data->get();
        // dd($query);
        return view('reports.dms_reports.saleAjax', [
                'records' => $query,
            ]);

    }
   public function dms_payment_report(Request $request)
   {
   		$company_id = Auth::user()->company_id;
   		$dealer = Dealer::where('dealer_status',1)->where('company_id',$company_id)->pluck('name','id');
   		$product_filter = CatalogProduct::where('status',1)->where('company_id',$company_id)->pluck('name','id');
   		$l3_filter = Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        return view('reports.dms_reports.paymentIdex', [
	            'dealer' => $dealer,
	            'product_filter' => $product_filter,
	            'l3_filter' => $l3_filter,
        	]);
   }
   public function dms_payment_report_final(Request $request)
   {
   		$company_id = Auth::user()->company_id;
   		$company_id = Auth::user()->company_id;
   		$dealer_id = $request->dealer_id;
   		$state_id = $request->state_id;
   		$product_id = $request->product_id;
   		$status = $request->status;
   		$explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

   		$query_data = DB::table('payment_collect_dealer')
   					->join('dealer','dealer.id','=','payment_collect_dealer.dealer_id')
   					->join('location_3','location_3.id','=','dealer.state_id')
   					->join('purchase_order','purchase_order.order_id','=','payment_collect_dealer.order_id')
   					->join('_payment_modes','_payment_modes.id','=','payment_collect_dealer.payment_mode')
   					->select('location_3.name as l3_name','invoice_date','invoice_no_p','dealer_code','dealer.name as dealer_name','payment_date','_payment_modes.mode as payment_mode','trans_no','cheque_no','amount_by_sfa','claim_adjustment','amount as amount')
   					->groupBy('invoice_date','payment_collect_dealer.dealer_id');
                        // ->get();

           	if(!empty($dealer_id))
            {   
                $query_data->whereIn('payment_collect_dealer.dealer_id',$dealer_id);
            }
            if(!empty($state_id))
            {   
                $query_data->whereIn('dealer.state_id',$state_id);
            }
           
        $query=$query_data->get();

        return view('reports.dms_reports.paymentAjax', [
	            'records' => $query ,
	           
        	]);
   }
   public function dms_sale_payment_report(Request $request)
   {
   		$company_id = Auth::user()->company_id;
   		$dealer = Dealer::where('dealer_status',1)->where('company_id',$company_id)->pluck('name','id');
   		$product_filter = CatalogProduct::where('status',1)->where('company_id',$company_id)->pluck('name','id');
   		$l3_filter = Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        return view('reports.dms_reports.salePaymentIndex', [
	            'dealer' => $dealer,
	            'product_filter' => $product_filter,
	            'l3_filter' => $l3_filter,
        	]);
   }
   public function dms_sale_payment_report_final(Request $request)
   {
   		$company_id = Auth::user()->company_id;
   		$dealer_id = $request->dealer_id;
   		$state_id = $request->state_id;
   		$product_id = $request->product_id;
   		$status = $request->status;
   		$explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

   		$query_data = DB::table('purchase_order')
                        ->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
   						->join('payment_collect_dealer','purchase_order.order_id','=','payment_collect_dealer.order_id')
                    	// ->join('catalog_product','catalog_product.id','=','purchase_order_details.product_id')
                        ->join('dealer','dealer.id','=','purchase_order.dealer_id')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                        ->join('person','person.id','=','dealer_location_rate_list.user_id')
                        ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
                        // ->join('_dms_reason','_dms_reason.id','=','purchase_order.dms_order_reason_id')
                        ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'l3_name','l6_name','pdf_name2','pdf_name','dms_order_reason_id','purchase_order.order_id as order_id','purchase_order.dealer_id as dealer_id','dealer.other_numbers as mobile_number','dealer.name as dealer_name','sale_date','dealer.email as d_email',DB::raw("SUM(purchase_order_details.cases) as cases"),DB::raw("SUM(pcs) as pcs"),DB::raw("SUM(scheme_qty) as scheme_qty"),DB::raw("SUM((pr_rate*cases)+(rate*pcs)) as total_vale"),'invoice_date','invoice_no_p',DB::raw('payment_collect_dealer.amount_by_sfa as payment'),'claim_adjustment')
                        ->where('purchase_order.company_id',$company_id)
                        ->where('purchase_order_details.company_id',$company_id)
                        ->where('dealer.company_id',$company_id)
                        ->where('purchase_order_details.app_flag','!=',1)
                        ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(sale_date,'%Y-%m-%d')<='$to_date'")
                        ->groupBy('purchase_order_details.order_id');
                        // ->get();

           	if(!empty($dealer_id))
            {   
                $query_data->whereIn('purchase_order.dealer_id',$dealer_id);
            }
            if(!empty($state_id))
            {   
                $query_data->whereIn('location_3.id',$state_id);
            }
            if(!empty($product_id))
            {   
                $query_data->whereIn('catalog_product.id',$product_id);
            }
            
        $query=$query_data->get();
        // dd($query);
        return view('reports.dms_reports.salePaymentAjax', [
	            'records' => $query,
        	]);

   	}

}

