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


class ReturnController extends Controller
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
            $distributor = Crypt::decryptString($id);

            $from_date = $request->from_date;
            $to_date = $request->to_date;

            $query_data = DB::table('damage_replace')
                ->select('retailer.name as rname','retailer.id as retailer_id', 'catalog_product.name as product_name', DB::raw('CONCAT(person.first_name," ",person.last_name) as user_name'), 'dealer.name as dealer_name','dealer.id as dealer_id', 'damage_replace.*')
                ->join('person', 'person.id', '=', 'damage_replace.user_id')
                ->join('person_login','person_login.person_id','=','person.id')
                ->join('dealer', 'dealer.id', '=', 'damage_replace.dis_code')
                ->leftJoin('catalog_product', 'catalog_product.id', 'damage_replace.prod_code')
                ->leftJoin('retailer', 'retailer.id', 'damage_replace.ret_code')
                ->where('person_status',1)
                ->where('dealer_status',1)
                ->whereRaw("DATE_FORMAT(damage_replace.date_time, '%Y-%m-%d')>='$from_date' and DATE_FORMAT(damage_replace.date_time,'%Y-%m-%d') <='$to_date'")
                ->where('damage_replace.dis_code', $distributor);


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
                $query_data->whereIn('damage_replace.dis_code', $tr);
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
                $query_data->whereIn('damage_replace.user_id', $flag);
            }

            

            $query = $query_data->get();
            // dd($query);
            return view('reports.distributer-dashboard.return', [
                'records' => $query,
                'id'=>$id,
            ]);
    }
}
