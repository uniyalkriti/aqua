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


class PaymentCollectionController extends Controller
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

            $query_data = DB::table('dealer_payments')
                ->select('drawn_from_bank', 'deposited_bank', 'invoice_number', 'person.emp_code', DB::raw('CONCAT(person.first_name," ",person.last_name) as user_name'), 'dealer.name as dealer_name', 'location_view.l1_name as zone', 'location_view.l2_name as region',
                    'location_view.l4_name as town_name', 'dealer_payments.*')
                ->join('person', 'person.id', '=', 'dealer_payments.user_id')
                ->join('person_login','person_login.person_id','=','person.id')
                ->leftJoin('dealer', 'dealer.id', '=', 'dealer_payments.dealer_id')
                ->leftJoin('location_view', 'location_view.l4_id', '=', 'dealer_payments.town')
                ->where('dealer_status',1)
                ->where('person_status',1)
                ->whereRaw("DATE_FORMAT(dealer_payments.payment_recevied_date, '%Y-%m-%d')>='$from_date' and DATE_FORMAT(dealer_payments.payment_recevied_date,'%Y-%m-%d') <='$to_date'")
                ->where('dealer_payments.dealer_id', $distributor);

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
                $query_data->whereIn('dealer_payments.dealer_id', $tr);
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
                $query_data->whereIn('dealer_payments.emp_id', $flag);
            }

            $query = $query_data->groupBy('location_view.l4_id','dealer_payments.drawn_from_bank','dealer_payments.deposited_bank','dealer_payments.invoice_number','person.emp_code','person.first_name','person.last_name','dealer.name','dealer_payments.id')->get();

            return view('reports.distributer-dashboard.payment_collection', [
                'records' => $query,
                'id'=>$id,
            ]);
    }
}
