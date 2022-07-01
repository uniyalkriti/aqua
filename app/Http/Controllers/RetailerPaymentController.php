<?php

namespace App\Http\Controllers;

use App\Person;
use App\UserDetail;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class RetailerPaymentController extends Controller
{


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

            $from_date = $request->from_date;
            $to_date = $request->to_date;

            $query_data = DB::table('payment_collect_retailer')
                ->select('dealer.id as dealer_id','_role.rolename as user_designation','payment_collect_retailer.*', 'person.emp_code', DB::raw('CONCAT(person.first_name," ",person.last_name) as user_name'), 'dealer.name as dealer_name', 'location_view.l1_name as zone', 'location_view.l2_name as region',
                    'location_view.l4_name as town_name')
                ->join('person', 'person.id', '=', 'payment_collect_retailer.user_id')
                ->join('_role','_role.role_id','=','person.role_id')
                ->join('person_login','person_login.person_id','=','person.id')
                ->leftJoin('dealer', 'dealer.id', '=', 'payment_collect_retailer.dealer_id')
                ->join('retailer','retailer.id','=','payment_collect_retailer.tr_code')
                ->leftJoin('location_view', 'location_view.l5_id', '=', 'retailer.location_id')
                ->where('dealer_status',1)
                ->where('person_status',1)
                ->whereRaw("DATE_FORMAT(payment_date, '%Y-%m-%d')>='$from_date' and DATE_FORMAT(payment_date,'%Y-%m-%d') <='$to_date'")
                ->where('tr_code', $retailer);


            

            $query = $query_data->get();

            return view('reports.retailer_dashboard.retailer_payment_collection', [
                'records' => $query,
                'id'=>$id,
            ]);
    }
}
