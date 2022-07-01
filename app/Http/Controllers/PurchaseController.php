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


class PurchaseController extends Controller
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
        $q = DB::table('purchase_order')
         ->where('purchase_order.dealer_id', $uid)
         ->whereRaw("DATE_FORMAT(order_date, '%Y-%m-%d') >= '$from_date'")
            ->whereRaw("DATE_FORMAT(order_date, '%Y-%m-%d') <= '$to_date'")
        ->select('order_id',DB::raw("DATE_FORMAT(receive_date,'%d-%m-%Y') AS receive_date"),DB::raw("DATE_FORMAT(order_date,'%d-%m-%Y') AS order_date"));
        $data=$q->get();
        //dd($data);
        $out=array();
       if (!empty($data)) {
            foreach ($data as $k => $d) {
                $uid=$d->order_id;
                $out[$uid] = DB::table('purchase_order_details')
                ->join('catalog_view','catalog_view.product_id','=','purchase_order_details.product_id')
                    ->where('order_id', $uid)
                    ->select('purchase_order_details.*','product_name')
                    ->get();
            }
        }

      // dd($out);
        return view('reports.purchase.purchase',
            [
                'purchase' => $data,
                'details' => $out,
                 'id' => $id
            ]);
    }
}
