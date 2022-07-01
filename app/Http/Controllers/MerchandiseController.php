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


class MerchandiseController extends Controller
{
    public function __construct()
    {
        $this->current_menu = 'mtp';
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

        $zone = $request->region;
        $region = $request->area;
        $distributor = $request->distributor;
        $user = $request->user;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $data1 = DB::table('merchandise')
        ->join('person','person.id','merchandise.user_id')
        ->join('_role','_role.role_id','person.role_id')
        ->leftJoin('_retailer_mkt_gift','_retailer_mkt_gift.id','=','merchandise.merchandise_id')
        ->leftJoin('retailer','retailer.id','=','merchandise.retailer_id')
        ->join('dealer','dealer.id','retailer.dealer_id')
        ->leftJoin('location_view','location_view.l5_id','=','retailer.location_id')
        ->whereRaw("DATE_FORMAT(merchandise.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(merchandise.date,'%Y-%m-%d') <='$to_date'  ")
        ->where('retailer.id',$retailer)
        ->select('dealer.id as dealer_id','retailer.id as retailer_id','user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'l2_name','l3_name','l4_name','head_quar','rolename','mobile','emp_code','retailer.name as retailername','merchandise.date as date','merchandise_name','qty','dealer.name as dealername','person_id_senior','merchandise.time as time','merchandise.lat as lat','merchandise.lng as lng',DB::raw("(SELECT CONCAT_WS(' ',first_name,middle_name,last_name) from person as p WHERE p.id=person.person_id_senior) AS seniorname"))->groupBy('retailer_id');
       //seniorname
       
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
            $data1->whereIn('user_id', $user);
        }
        $user_record = $data1->get();

       // dd($query);
        return view('reports.retailer_dashboard.merchandise', [
            'records' => $user_record,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'id'=>$id,
        ]);
    }
}
