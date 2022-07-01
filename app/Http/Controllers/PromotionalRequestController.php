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


class PromotionalRequestController extends Controller
{
    public function __construct()
    {
        $this->current_menu = 'promotional_request';
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
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $promotional_request_query = DB::table('merchandise_requirement')
                                    ->join('retailer','retailer.id','=','merchandise_requirement.retailer_id')
                                    ->join('person','person.id','=','merchandise_requirement.user_id')
                                    ->join('person_login','person_login.person_id','=','person.id')
                                    ->join('_role','_role.role_id','=','person.role_id')
                                    ->join('location_3','location_3.id','=','person.state_id')
                                    ->select(DB::raw("CONCAT_WS(first_name,middle_name,last_name) as user_name"), 'location_3.name as state','_role.rolename as designation','retailer.name as retailer_name','merchandise_requirement.*')
                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                                    ->where('person_status',1)
                                    ->where('retailer_status',1)
                                    ->where('retailer_id',$retailer)
                                    ->get();
                                    // dd($promotional_request_query);
        return view('reports.retailer_dashboard.promotional_request',[
                    'promotional_request_query' => $promotional_request_query,
                    'id' => $id,

            ]);
    }
}
