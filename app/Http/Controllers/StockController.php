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


class StockController extends Controller
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
        // dd($distributor);
        $region = $request->region;
            $state = $request->area;
            $town = $request->territory;
            $from_date = !empty($request->from_date)?$request->from_date:date('Y-m-d');
            $to_date = !empty($request->to_date)?$request->to_date:date('Y-m-d');
            $user = $request->user;
            // $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            // $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));


            $query_data = DB::table('dealer_balance_stock')
                ->select('pcs_mrp', DB::raw('CONCAT(person.first_name," ",person.last_name) as user_name'), 'dealer.name as dealer_name', 'dealer_balance_stock.*', 'catalog_product.name as product_name')
                ->leftJoin('person', 'person.id', '=', 'dealer_balance_stock.user_id')
                ->leftJoin('person_login','person_login.person_id','=','person.id')
                ->join('catalog_product', 'catalog_product.id', '=', 'dealer_balance_stock.product_id')
                ->join('dealer', 'dealer.id', '=', 'dealer_balance_stock.dealer_id')
                // ->where('person_status',1)
                // ->whereRaw("DATE_FORMAT(dealer_balance_stock.submit_date_time, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(dealer_balance_stock.submit_date_time, '%Y-%m-%d') <= '$to_date'")
                ->where('dealer_balance_stock.dealer_id', $distributor);

            $tmp = array();
            $dealer_beat = DB::table('location_view');

            if (!empty($beat)) {
                $dealer_beat->whereIn('l5_id', $beat)->pluck('l5_id');
            } //State Data
            elseif (!empty($town)) {

                $dealer_beat->whereIn('l4_id', $town)->pluck('l5_id');
            } //Town Data
            elseif (!empty($state)) {

                $dealer_beat->whereIn('l3_id', $state)->pluck('l5_id');

            } //Beat Data
            elseif (!empty($region)) {
                $tr = [];
                $tr = $dealer_beat->whereIn('l1_id', $region)->pluck('l5_id');

            }


            if (!empty($request->distributor)) {

                $tr = [];
                $tr = $request->distributor;
            }

            if (!empty($tr)) {
                $query_data->whereIn('dealer_balance_stock.dealer_id', $tr);
            }

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
                // $query_data->whereIn('dealer_balance_stock.user_id', $flag);
            }

           

            $query = $query_data->groupBy('submit_date_time','product_id')->get();

            return view('reports.distributor-stock.stock', [
                'records' => $query,
                'id'=>$id
            ]);
        
    }
}
