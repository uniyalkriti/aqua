<?php
namespace App\Http\Controllers;
use App\UserDetail;
use Illuminate\Http\Request;
use Auth;
use DB; 
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class DailyProspectingController extends Controller
{

	 public function show(Request $request,$id)
    {
       // dd('f');\
        $zone = $request->zone; 
        $region = $request->region;
        $state = $request->state;
        $user_id = Crypt::decryptString($id);
        // dd($user_id);
        $from = $request->from_date; 
        $to = $request->to_date;
        // dd($from);

        $query = DB::table('daily_prospecting_working')
                ->join('person','person.id','=','daily_prospecting_working.user_id')
                ->whereRaw("DATE_FORMAT(daily_prospecting_working.cur_date_time,'%Y-%m-%d') >='$from' and DATE_FORMAT(daily_prospecting_working.cur_date_time,'%Y-%m-%d') <='$to'");

        if (!empty($user_id)) {
            $query->where('user_id', $user_id);
        }
       

        $query_data = $query->orderBy('cur_date_time', 'DESC')
            ->get();
            // dd($query_data);
        return view('reports.daily_prospecting', [
            'records' => $query_data,
            'id'=>$id,
        ]);

    }
}