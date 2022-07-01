<?php
namespace App\Http\Controllers;
use App\UserDetail;
use Illuminate\Http\Request;
use Auth;
use DB; 
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class CompetitivePriceIntelligenceController extends Controller
{

	 public function show(Request $request,$id)
    {
            $zone = $request->zone;
            $region = $request->region;
            $state = $request->state;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $position = $request->position;
            $temp = [];
            $l_ids = [];
            $user_id = Crypt::decryptString($id);

            #location based user
            if (!empty($zone) || !empty($region) || !empty($state)) {
                $location = DB::table('location_view');
                if (!empty($zone)) {
                    $location->whereIn('l1_id', $zone);
                }
                if (!empty($region)) {
                    $location->whereIn('l2_id', $region);
                }
                if (!empty($state)) {
                    $location->whereIn('l3_id', $state);
                }
                $loc_ids = $location->pluck('l3_id');

                if (!empty($loc_ids)) {
                    $l_ids = DB::table('person')->whereIn('state_id', $loc_ids)->pluck('id');
                }
            }

            $query = DB::table('competitive_price_intelligence')->join('person','person.id','=','competitive_price_intelligence.user_id');
            if (!empty($unique_ids)) {
                $query->whereIn('user_id', $unique_ids);
            }
            if (!empty($request->user)) {
                $u = $request->user;
                $query->whereIn('user_id', $u);
            }
            if (!empty($l_ids)) {
                $u = $request->user;
                $query->whereIn('user_id', $l_ids);
            }

            $query_data = $query->whereRaw("DATE_FORMAT(competitive_price_intelligence.cur_date_time,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(competitive_price_intelligence.cur_date_time,'%Y-%m-%d') <='$to_date'")
                ->where('user_id',$user_id)
                ->select('competitive_price_intelligence.*',DB::raw("CONCAT_WS(' ','first_name,middle_name,last_name') as username"))
                ->orderBy('cur_date_time', 'DESC')
                ->get();
            return view('reports.competitive_price_intelligence', [
                'records' => $query_data,
                'id'=>$id,
            ]);

    }
}