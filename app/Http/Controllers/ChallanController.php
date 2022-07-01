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


class ChallanController extends Controller
{
    public function __construct()
    {
        $this->current = 'stock';
    }

public function show(Request $request,$id)
    {
        $data = [];
        #decrypt id
        $uid = Crypt::decryptString($id);
        $from_date=!empty($request->start_date)?$request->start_date:date('Y-m-d');
        $to_date=!empty($request->end_date)?$request->end_date:date('Y-m-d');
        $q = DB::table('challan_order')
         ->where('challan_order.ch_dealer_id', $uid)
         ->whereRaw("DATE_FORMAT(ch_date, '%Y-%m-%d') >= '$from_date'")
            ->whereRaw("DATE_FORMAT(ch_date, '%Y-%m-%d') <= '$to_date'")
        ->select('id','ch_no',DB::raw("DATE_FORMAT(ch_date,'%d-%m-%Y') AS ch_date"));
        $data=$q->get();
        //dd($data);
        $out=array();
       if (!empty($data)) {
            foreach ($data as $k => $d) {
                $uid=$d->id;
                $out[$uid] = DB::table('challan_order_details')
                ->join('catalog_view','catalog_view.product_id','=','challan_order_details.product_id')
                    ->where('ch_id', $uid)
                    ->select('challan_order_details.*','product_name')
                    ->get();
            }
        }

      // dd($out);
        return view('reports.challan.challan',
            [
                'challan' => $data,
                'details' => $out,
                 'id' => $id
            ]);
    }
}
