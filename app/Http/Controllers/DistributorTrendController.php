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


class DistributorTrendController extends Controller
{
    public function __construct()
    {
        $this->current = 'threshold';
        $this->current_menu='threshold';
    }

public function show(Request $request,$id)
    {
     //   dd('hh');
        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();


        $dealer = $uid;
        if(!empty($request->year)) {
        $crmonth=$request->year;
        }else{
            $crmonth=date('Y-m');
        }
        if (!empty($crmonth)) 
        {
            //$dealer = $uid;
            $month_with_year = explode('-', $crmonth);
            $year = $month_with_year[0];

            $month = $month_with_year[1];

            $one_year_back = $year - 1;

            $two_year_back = $year - 2;
           // dd($year);

            $month_arr = ['04', '05', '06', '07', '08', '09', '10', '11', '12', '01', '02', '03'];
            //dd($month_arr);
            #Dealer
            $query_data = DB::table('dealer_location_l4')
            ->where('id',$uid);
            $records = $query_data->get();
            $final_data = [];
            if(empty($check)){
            $final_data  = DB::table('user_sales_order_view')->groupBy('dealer_id',DB::raw("(DATE_FORMAT(date,'%Y-%m'))"))->pluck(DB::raw("SUM(total_sale_value)"),DB::raw("CONCAT(dealer_id,DATE_FORMAT(date,'%Y-%m'))"));
            }else{
                $final_data = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id') 
                        ->where('user_sales_order.company_id',$company_id)
                        ->groupBy('dealer_id',DB::raw("(DATE_FORMAT(date,'%Y-%m'))"))
                        ->pluck(DB::raw("SUM(final_secondary_rate*final_secondary_qty) as data"),DB::raw("CONCAT(dealer_id,DATE_FORMAT(date,'%Y-%m'))"));
            }

           


        //    dd($final_data);
            $arr = [];
            // foreach ($records as $k => $d) {
            //     foreach ($month_arr as $mk => $md) {
            //         if ($md < 4) {
            //             $year = $year + 1;
            //         }
            //         $query = DB::table('challan_order')->where('ch_dealer_id', $d->id);
            //         $data = $query;

            //         $d1 = $year . '-' . $md;
            //         $d2 = ($year - 1) . '-' . $md;
            //         $d3 = ($year - 2) . '-' . $md;

            //         $arr[$d->id]['f1'][$md] = $data->whereRaw("DATE_FORMAT(ch_date, '%Y-%m') = '$d1'")->select(DB::raw("SUM(amount) as sv"))->first();

            //         $arr[$d->id]['f2'][$md] = $data->whereRaw("DATE_FORMAT(ch_date, '%Y-%m') = '$d2'")->select(DB::raw("SUM(amount) as sv"))->first();

            //         $arr[$d->id]['f3'][$md] = $data->whereRaw("DATE_FORMAT(ch_date, '%Y-%m') = '$d3'")->select(DB::raw("SUM(amount) as sv"))->first();
            //         if ($md < 4) {
            //             $year = $year - 1;
            //         }
            //     }
            // }
            foreach ($records as $k => $d) 
            {
                foreach ($month_arr as $mk => $md) 
                {
                   

                    if ($md < 4) {
                        $year = $year + 1;
                    }
                    // $query = DB::table('user_sales_order_view')->where('dealer_id', $d->id);
                    // $data = $query;
                    $d1 = $year . '-' . $md;
                    $d2 = ($year - 1) . '-' . $md;
                    $d3 = ($year - 2) . '-' . $md;
                   // echo $d->id.$d1;
                   // echo "<br>";
                    $arr[$d->id]['f1'][$md] = !empty($final_data[$d->id.$d1])?$final_data[$d->id.$d1]:'0';
                    $arr[$d->id]['f2'][$md] = !empty($final_data[$d->id.$d2])?$final_data[$d->id.$d2]:'0';
                    $arr[$d->id]['f3'][$md] = !empty($final_data[$d->id.$d3])?$final_data[$d->id.$d3]:'0';
                    // $arr[$d->id]['f1'][$md] = $data->whereRaw("DATE_FORMAT(date, '%Y-%m') = '$d1'")->select(DB::raw("SUM(total_sale_value) as sv"))->first();
                    // $arr[$d->id]['f2'][$md] = $data->whereRaw("DATE_FORMAT(date, '%Y-%m') = '$d2'")->select(DB::raw("SUM(total_sale_value) as sv"))->first();
                    // $arr[$d->id]['f3'][$md] = $data->whereRaw("DATE_FORMAT(date, '%Y-%m') = '$d3'")->select(DB::raw("SUM(total_sale_value) as sv"))->first();
                    if ($md < 4) {
                        $year = $year - 1;
                    }
                }

            }

           // dd($arr);

            return view('reports.distributer-wise-sales-trends.saleTrend', [
                'records' => $records,
                'arr' => $arr,
                'crmonth'=>$crmonth,
                'y1' => $year,
                'y2' => $one_year_back,
                'y3' => $two_year_back,
                'month' => $month,
                'monthArr' => $month_arr,
                'id' => $id
            ]);
        }
    }
}
