<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Person;
use App\UserDetail;
use App\Company;
use App\JuniorData;
use App\Circular;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use App\UserIncentiveDetails;
use App\UserIncentiveSlabs;
use App\UserIncentiveRoleDistribution;
use Validator;
use DB;
use Image;

class CommonTrackingController extends Controller
{
    public $successStatus = 401;
    public $response_true = True;
    public $response_false = False;

    

    public function change_lat_lng_mnc_mcc_lat_cellid(Request $request)
    {
    	$company_id = array('69,37');
    	$date = !empty($request->track_date)?$request->track_date:date('Y-m-d');
    	$data_set = DB::table('user_daily_tracking')
    				->where('track_date',$date)
					->where('lat_lng','=','0.0,0.0')
					// ->where('lat_lng','=','0,0')
					// ->where('lat_lng','=','0')
					// ->where('user_id','5010')
    				->whereIn('company_id',$company_id)
    				->orderBy('id','desc')
    				->get();
    	$count_key[] = '0';
		foreach($data_set as $key => $value)
		{
			$explode_data = explode(':',$value->mnc_mcc_lat_cellid);
			$first = !empty($explode_data[0])?$explode_data[0]:'0';
			$second = !empty($explode_data[1])?$explode_data[1]:'0';
			$third = !empty($explode_data[2])?$explode_data[2]:'0';
			$forth = !empty($explode_data[3])?$explode_data[3]:'0';
    		$str = "https://opencellid.org/ajax/searchCell.php?mcc=".$first."&mnc=".$second."&lac=".$third."&cell_id=".$forth;
    		// dd($str);

    		$ch = curl_init($str);
	        // curl_setopt($ch, CURLOPT_POST, true);
	        // curl_setopt($ch, CURLOPT_POSTFIELDS, $sending_array);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        $response = curl_exec($ch);
	        $return_decode = json_decode($response);
	        // dd($return_decode);
	        if($return_decode != false)
	        {
	        	$count_key[] = $key;
	        	$lat = !empty($return_decode->lat)?$return_decode->lat:'0.0';
	        	$lon = !empty($return_decode->lon)?$return_decode->lon:'0.0';
	        	$update_query = DB::table('user_daily_tracking')
							->where('user_id',$value->user_id)
    						->where('id',$value->id)
    						->update([
    							'lat_lng'=>$lat.','.$lon,
    						]);
	        }
	        // dd('1');
    		

		}
		$msg = array_sum($count_key).' Count Updated Successfully';
        return response()->json([ 'response' =>True,'message'=>$msg]);


    }
    public function change_addr_mnc_mcc_lat_cellid(Request $request)
    {
    	$company_id = array('69,37');
    	$date = !empty($request->track_date)?$request->track_date:date('Y-m-d');
    	$data_set = DB::table('user_daily_tracking')
    				->where('track_date',$date)
					->where('lat_lng','!=','0.0,0.0')
					->Where('track_address','NA')
					// ->orWhere('track_address','')
					// ->orWhere('track_address',NULL)
					// ->where('lat_lng','=','0,0')
					// ->where('lat_lng','=','0')
					->where('user_id','5010')
    				->whereIn('company_id',$company_id)
    				->orderBy('id','desc')
    				->get();
		// dd($data_set);
		foreach($data_set as $key => $value)
		{
			$explode_data = explode(':',$value->mnc_mcc_lat_cellid);
			$first = $explode_data[0];
			$second = $explode_data[1];
			$third = $explode_data[2];
			$forth = $explode_data[3];
    		$str = "https://www.latlong.net/Show-Latitude-Longitude.html";

    		$sending_array = [

    			'latitude'=>"10.934727",
    			'latitude'=>"78.429596",
    		];

    		$ch = curl_init($str);
	        curl_setopt($ch, CURLOPT_POST, true);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $sending_array);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        $response = curl_exec($ch);
	        $return_decode = json_decode($response);
	        dd($return_decode);
	        if($return_decode != false)
	        {
	        	$update_query = DB::table('user_daily_tracking')
							->where('user_id',$value->user_id)
    						->where('id',$value->id)
    						->update([
    							'lat_lng'=>$return_decode->lat.','.$return_decode->lon,
    						]);
	        }
	        // dd('1');
    		

		}
        return response()->json([ 'response' =>True,'message'=>'Success']);

        
    }

}
