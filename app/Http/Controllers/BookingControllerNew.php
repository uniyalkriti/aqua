<?php
namespace App\Http\Controllers;
use App\UserDetail;
use Illuminate\Http\Request;
use Auth;
use DB; 
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class BookingControllerNew extends Controller
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

       // $from_date = date('Y-m-d');
      //  $to_date = date('Y-m-d');


        // $sale = DB::table('user_sales_order_view')
        //     ->join('person', 'person.id', '=', 'user_sales_order_view.user_id', 'inner')
        //     ->where('user_id', $uid)
        //     ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date'")
        //     ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")
        //     ->select('user_sales_order_view.*', 'person.first_name', 'person.middle_name', 'person.last_name',DB::raw("DATE_FORMAT(date,'%d-%m-%Y') AS dates"))->orderBy('order_id','ASC')
        //     ->get();
        // $sale = DB::table('user_sales_order_view')
        //     ->leftJoin('_no_sale_reason','_no_sale_reason.id','=','user_sales_order_view.non_productive_reason_id')
        //     ->join('person', 'person.id', '=', 'user_sales_order_view.user_id', 'inner')
        //     ->join('retailer','retailer.id','=','user_sales_order_view.retailer_id')
        //     ->where('user_id', $uid)
        //     ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date'")
        //     ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")
        //     ->select('_no_sale_reason.name as non_productive_reason','retailer.lat_long as retialer_lat_lng','user_sales_order_view.*', 'person.first_name', 'person.middle_name', 'person.last_name',DB::raw("DATE_FORMAT(date,'%d-%m-%Y') AS dates"))->orderBy('order_id','ASC')
        //     ->get();

        if(empty($check)){
        $sale = DB::table('user_sales_order')
            ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
            ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
            ->leftJoin('_no_sale_reason','_no_sale_reason.id','=','user_sales_order.reason')
            ->join('person', 'person.id', '=', 'user_sales_order.user_id')
            ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
            // ->join('location_view','location_view.l7_id','=','retailer.location_id')
            ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
             ->select(DB::raw("SUM(rate*quantity) as total_sale_value_real"),'retailer.name as retailer_name','l5_name','dealer.name as dealer_name','_no_sale_reason.name as non_productive_reason','retailer.lat_long as retialer_lat_lng','user_sales_order.*', 'l3_name','person.first_name', 'person.middle_name', 'person.last_name',DB::raw("DATE_FORMAT(user_sales_order.date,'%d-%m-%Y') AS dates"))
            ->where('user_id', $uid)
            ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date'")
            ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
            ->orderBy('user_sales_order.order_id','ASC')
            ->groupBy('order_id')
            ->get();
        }else{
            $sale = DB::table('user_sales_order')
            ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
            ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
            ->leftJoin('_no_sale_reason','_no_sale_reason.id','=','user_sales_order.reason')
            ->join('person', 'person.id', '=', 'user_sales_order.user_id')
            ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
            // ->join('location_view','location_view.l7_id','=','retailer.location_id')
            ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
             ->select(DB::raw("round(SUM(final_secondary_rate*final_secondary_qty),2) as total_sale_value_real"),'retailer.name as retailer_name','l5_name','dealer.name as dealer_name','_no_sale_reason.name as non_productive_reason','retailer.lat_long as retialer_lat_lng','user_sales_order.*', 'l3_name','person.first_name', 'person.middle_name', 'person.last_name',DB::raw("DATE_FORMAT(user_sales_order.date,'%d-%m-%Y') AS dates"))
            ->where('user_id', $uid)
            ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date'")
            ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
            ->orderBy('user_sales_order.order_id','ASC')
            ->groupBy('order_id')
            ->get();
        }



        $diffrence = DB::table('user_sales_order_view')
        ->join('person', 'person.id', '=', 'user_sales_order_view.user_id', 'inner')
        ->where('user_id', $uid)
        ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date'")
        ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")
        ->select('order_id',DB::raw("CONCAT_ws(' ',date,time) as time"))->orderBy('order_id','ASC')
        ->get()->toArray();
            // dd($sale);
        if (!empty($sale)) {
            foreach ($sale as $k => $d)
             {
                $uid3=$d->order_id;
                if(empty($check)){
                $arr[$uid3] = DB::table('user_sales_order_details')
                    ->join('catalog_product','user_sales_order_details.product_id','=','catalog_product.id')
                    ->select('user_sales_order_details.rate as rate','user_sales_order_details.quantity as quantity','catalog_product.name as product_name')
                    ->where('order_id', $uid3)
                    ->get();
                }else{
                    $arr[$uid3] = DB::table('user_sales_order_details')
                    ->join('catalog_product','user_sales_order_details.product_id','=','catalog_product.id')
                    ->select('user_sales_order_details.final_secondary_rate as rate','user_sales_order_details.final_secondary_qty as quantity','catalog_product.name as product_name')
                    ->where('order_id', $uid3)
                    ->get();
                }
            }
        }

      // dd($sale);

        return view('reports.booking', [
            'details' => $arr,
            'sale_data' => $sale,
            'diffrence' => $diffrence,

            'from_date' => $from_date,
            'to_date' => $to_date,
            'id' => $id
        ]);
    }

    public function dailyAttendanceReport(Request $request)
    {
        if ($request->ajax() && !empty($request->from_date) && !empty($request->to_date)) {

            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $data1 = UserDetail::join('person_login', 'person_login.person_id', '=', 'person.id')
                ->select('person.id as person_id', 'person.emp_code as emp_code', DB::raw("CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as uname"), 'location_view.l1_name as zone', 'location_view.l2_name as region', '_role.rolename as role')
                ->distinct('person.id')
                ->where('person.id', '>', 1)
                ->where('person_login.person_status', '=', 1)
                ->leftJoin('location_view', 'location_view.l3_id', '=', 'person.state_id')
                ->leftJoin('_role', '_role.role_id', '=', 'person.role_id');
            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data1->whereIn('location_view.l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $data1->whereIn('location_view.l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data1->whereIn('person.id', $user);
            }
            $user_record = $data1->get();

            $query = DB::table('user_att_sale_view')
                ->whereRaw("DATE_FORMAT(user_att_sale_view.work_date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(user_att_sale_view.work_date,'%Y-%m-%d') <='$to_date'")
                ->groupBy('work_date', 'user_id', 'track_addrs', 'work', 'check_out_date')
                ->get();
            $arr = [];
            foreach ($query as $k => $q) {
                $date = !empty($q->work_date) ? date('Y-m-d', strtotime($q->work_date)) : 0;
                $arr[$date][$q->user_id] = $q;
            }

            return view('reports.daily-attendance.ajax', [
                'records' => $arr,
                'users' => $user_record,
                'from_date' => $from_date,
                'to_date' => $to_date
            ]);

        }
    }
}
