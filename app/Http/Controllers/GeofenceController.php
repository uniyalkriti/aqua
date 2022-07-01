<?php
 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location1;
use App\Location2;
use App\Location3;
use App\Location4;
use App\Location5;
use App\Geofence;
use Illuminate\Support\Facades\Session;
use DB;
use Auth;
use Crypt;


class GeofenceController extends Controller
{
    public function __construct()
    {
        $this->current_menu = 'geofence';
        $this->current_dir  = 'geofence';
     }
    // #....................main function for dropdown and for table  ........................

    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('company_id',$company_id)->where('status', 1)->select('name', 'id')->get();
        $state = Location3::where('company_id',$company_id)->where('status', 1)->pluck('name', 'id');
        $town = Location4::where('company_id',$company_id)->where('status', 1)->pluck('name', 'id');
        $location_2_id=$request->region;
        $location_3_id=$request->area;
        $location_4_id=$request->territory;
        $geofenceData = Geofence::select(DB::raw('DISTINCT geofence.location_id as id'),'location_4.name as town_name','location_3.name as state_name','location_2.name as region_name')
                        ->join('location_4','location_4.id','=','geofence.location_id')
                        ->join('location_3','location_3.id','=','location_4.location_3_id')
                        ->join('location_2','location_2.id','=','location_3.location_2_id')
                        ->where('geofence.company_id',$company_id)
                        ->where('location_4.company_id',$company_id)
                        ->where('location_3.company_id',$company_id)
                        ->where('location_2.company_id',$company_id);
        if(!empty($location_2_id))
        {
            $geofenceData->whereIn('location_2.id',$location_2_id);
        }
         if(!empty($location_3_id))
        {
            $geofenceData->whereIn('location_3.id',$location_3_id);
        } 
        if(!empty($location_4_id))
        {
            $geofenceData->whereIn('location_4.id',$location_4_id);
        }
        $query=$geofenceData->get();
        return view($this->current_dir.'.index',
            [
                'region' => $region,
                'state' => $state,
                'town' => $town,
                'geofenceData' => $query,
                'current_menu' => $this->current_menu,
            ]);
    }

    // #...................main function for dropdown and for table Ends here........................

    // #....................Show Geofence function for particuler town ........................

    public function show($id)
     {
        $company_id = Auth::user()->company_id;
        $town_id = Crypt::decryptString($id);
        $geofenceData= Geofence::join('location_4','location_4.id','=','geofence.location_id')
                               ->where('location_id','=',$town_id)
                               ->where('geofence.company_id',$company_id)
                               ->where('location_4.company_id',$company_id)
                               ->get();
        foreach ($geofenceData as $datas)
        {
          $totaData[] = $datas;
          $latitude=str_replace(' ',',',$datas->lat);
          $longitude=str_replace(' ',',',$datas->lng);
          $arr_lat[]=$latitude;
          $arr_lng[] =$longitude;
        }
        $lat= !empty($arr_lat)?json_encode($arr_lat):'';
        $lng= !empty($arr_lng)?json_encode($arr_lng):'';

        return view($this->current_dir.'.showFence',
            ['geofenceData'=>$geofenceData,
             'lat'=>$lat,
             'lng'=>$lng
            ]);
    }

    // #....................Show Geofence function for particuler town ends here ........................

    // #....................edit function for particuler town ........................#

     public function edit($id)
     { 
        $town_id = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id; 
        $geofenceData= Geofence::join('location_4','location_4.id','=','geofence.location_id')
                                ->where('location_id','=',$town_id)
                                ->where('geofence.company_id',$company_id)
                                ->where('location_4.company_id',$company_id)
                                ->get();
        return view($this->current_dir.'.editFence',[
            'geofenceData'=>$geofenceData,
            'town_id' => $town_id,
           
            ]);
     }

    // #....................edit function for particuler town ends here........................#

    // #......submit geofence after delete existing geofence against particular town id.....................#

     public function update(Request $request, $id)
     { 
        $request->validate([
            'polylat'=>'required',
            'polylng'=>'required'
            ]);
        $town_id = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        $lat=$request->polylat;
        $lng=$request->polylng;
        $latArr=explode(',',$lat);
        $lngArr=explode(',',$lng);
        $arr=[];
        $insert=[];

        DB::beginTransaction();
        if(!empty($lat) && ($lng))
        {
            $query=DB::table('geofence')->where('company_id',$company_id)->where('location_id','=',$town_id)->delete();
        }
        else
        {   
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
            return redirect()->intended('geofence');
        }
        foreach ($latArr as $k=>$d)
        {
            $arr['location_id']=$town_id;
            $arr['lng']=$lngArr[$k];
            $arr['lat']=$d;
            $arr['company_id']=$company_id;
            $arr['status']=1;

            $insert[]=$arr;
        }
         if (!empty($insert))
        {
            $check=Geofence::insert($insert);
        }
        if (!empty($check)) 
            {
                DB::commit();
                Session::flash('message', 'Success!');
                Session::flash('class', 'success');
            } 
            else 
            {   
                DB::rollback();
                Session::flash('message', 'Something went wrong!');
                Session::flash('class', 'danger');
            }
     return redirect()->intended('geofence');
     }

    // #......submit geofence after delete existing geofence against particular town id ends here.....................#

    // #................ function for draw a geo fence .....................
    
    public function createGeofence()
    {
        $company_id = Auth::user()->company_id;
        $town = Location4::where('location_4.status', 1)->select('location_4.id',DB::raw("CONCAT(location_4.name,' ','-','(',location_3.name,')') AS town_name"))->join('location_3','location_4.location_3_id','=','location_3.id')
            ->where('location_4.company_id',$company_id)
            ->where('location_3.company_id',$company_id)
            ->get();

        return view('geofence.geofence',[
                    'town' =>$town,
            ]);
    }
    
    // #................ function for draw a geo fence ends here .....................

    // #.... function submit the geo fence  lat lng along with town id................

    public function geofenceSubmit(Request $request)
    {
         $request->validate([
            'territory' => 'required',
            'polylat'=>'required',
            'polylng'=>'required'
        ]);

        $town=$request->territory;
        $lat=$request->polylat;
        $lng=$request->polylng;
        $company_id = Auth::user()->company_id;
        
        $latArr=explode(',',$lat);
        $lngArr=explode(',',$lng);
        $arr=[];
        $insert=[];
        foreach ($latArr as $k=>$d)
        {
            $arr['location_id']=$town;
            $arr['lng']=$lngArr[$k];
            $arr['lat']=$d;
            $arr['company_id']=$company_id;
            $arr['status']=1;

            $insert[]=$arr;
        }
            DB::beginTransaction();
        if (!empty($insert))
        {
            $check=Geofence::insert($insert);
        }
         if (!empty($check)) 
            {
                DB::commit();
                Session::flash('message', 'Success!');
                Session::flash('class', 'success');
            } 
            else 
            {
                DB::rollback();
                Session::flash('message', 'Something went wrong!');
                Session::flash('class', 'danger');
            }
        return redirect()->intended('geofence');
    }

    // #................ function for geofence submit  ends here .....................

}
 