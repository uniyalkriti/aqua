<?php

namespace App\Http\Controllers;


use App\User;
use App\UserDetail;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class RDSController extends Controller
{
    public function __construct()
    {
        $this->current_menu = 'retailer_comment';
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        #decrypt id
        $retailer = Crypt::decryptString($id);
        $state = $request->state; 
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $secondry_sale = array();
        $senior_name = array();

        $main_query = DB::table('user_sales_order')->join('person','person.id','=','user_sales_order.user_id')
                    ->join('_role','_role.role_id','=','person.role_id')
                    ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
                    ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                    ->join('location_view','location_view.l5_id','=','user_sales_order.location_id')
                    ->select('user_sales_order.retailer_id','user_sales_order.dealer_id','user_sales_order.user_id','user_sales_order.date as date',DB::raw("DATE_FORMAT(user_sales_order.date,'%d-%m-%Y') AS show_date"),'user_sales_order.id AS uniq','location_view.l3_name as state','location_view.l4_name as city',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as person_name"),'user_sales_order.user_id as user_id','dealer.name as dealer_name','person.person_id_senior as person_id_senior','retailer.name as retailer_name','user_sales_order.location_id as usolid','user_sales_order.dealer_id AS did','user_sales_order.retailer_id as retailer_id')
                    ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                    ->where('retailer_id',$retailer)
                    ->groupBy('date','user_id','retailer_id');
                    if(!empty($state))
                    {
                        $main_query->whereIn('location_view.l3_id',$state);
                    }
                    $main_query_data = $main_query->get();

                    $secondry_sale_data = DB::table('user_sales_order_details')->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")->groupBy('user_id','date','dealer_id','retailer_id')->pluck(DB::raw("SUM(rate*quantity) as total_sale_value"),DB::raw("CONCAT(user_id,date,dealer_id,retailer_id) as total"));

                    $senior_name_data = DB::table('person')->join('user_sales_order','user_sales_order.user_id','=','person.id')->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")->groupBy('person_id_senior')->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as senior_person_name"),'person_id_senior');

                    // dd($main_query_data);
                    foreach ($main_query_data as $key => $value) 
                    {
                        $user_id = $value->user_id;
                        $retailer_id = $value->retailer_id;
                        $did = $value->did;
                        $date = $value->date;
                        $person_id_senior_id = $value->person_id_senior;
                        // dd($date);
                        $senior_name[$user_id][$date] = !empty($senior_name_data[$person_id_senior_id])?$senior_name_data[$person_id_senior_id]:'';
                        // dd($retailer_id);
                        $secondry_sale[$user_id][$date][$did][$retailer_id] = !empty($secondry_sale_data[$user_id.$date.$did.$retailer_id])?$secondry_sale_data[$user_id.$date.$did.$retailer_id]:'0';      
                        // dd($user_id.$date.$did.$retailer_id);               
                    }
                    // dd($secondry_sale);
                    return view('reports.retailer_dashboard.rds', [
                               'secondry_sale'=>$secondry_sale,
                               'senior_name'=>$senior_name,
                               'main_query_data'=>$main_query_data,
                               'id'=>$id,

                            ]);

    }
}
