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


class ProductInvestigationController extends Controller
{
	public function show(Request $request ,$id)
    {
        $user_id = Crypt::decryptString($id);
     	
        $from_date = $request->start_date;
        $to_date = $request->end_date;

            $query = DB::table('product_investigation_report')
                ->where('user_id', $user_id)
                ->whereRaw("date_format(date_time,'%Y-%m-%d')>='$from_date' AND date_format(date_time,'%Y-%m-%d')<='$to_date' ");

            $query_data = $query->orderBy('date_time', 'DESC')
                ->get();
            //  print_r($query_data );die;
            return view('product_investigation', [
                'records' => $query_data,
                'id'=>$id
            ]);
      


    }
}