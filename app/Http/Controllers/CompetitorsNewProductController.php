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


class CompetitorsNewProductController extends Controller
{
	public function show(Request $request,$id)
    {

        $user = Crypt::decryptString($id);
        $from_date = $request->start_date;
        $from_date = $request->end_date;


            $query = DB::table('competitors_launched_product')
                ->where('user_id', $user)
                ->get();

            return view('reports.competitors_new_product', [
                'records' => $query,
                'id'=>$id
            ]);
       
    }


}