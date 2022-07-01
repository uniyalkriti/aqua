<?php

namespace App\Http\Controllers;

use App\Person;
use Illuminate\Http\Request;
use App\Dealer;
use App\Retailer;
use App\JuniorData;
use App\Location3;
use DB;
use DateTime;
use Auth;
use Session;
use App\UserSalesOrder;
use App\TableReturn;
use App\SecondarySale;
use Illuminate\Support\Facades\Crypt;
use App\ChallanOrder;

class analyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->menu = DB::table('_modules')->orderBy('module_sequence')->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function retailerAnalytics(Request $request)
    {


        $from_date = !empty($request->from_date)?$request->from_date:date('Y-m-01');
        $to_date = !empty($request->to_date)?$request->to_date:date('Y-m-d');
        $current_menu='DASHBOARD';
        $userLogin=Auth::user();
        $company_id = Auth::user()->company_id;
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
        $startTime = strtotime($from_date);
        $endTime = strtotime($to_date);
        $table_name = TableReturn::table_return($from_date,$to_date);


        // filter query starts
        $outlet_type=DB::table('_retailer_outlet_type')
        ->where('status',1)
        ->where('company_id',$company_id)
        ->orderBy('_retailer_outlet_type.outlet_type','ASC')
        ->pluck('outlet_type','id');
        $retailer_name=DB::table('retailer')
        ->where('company_id',$company_id)
        ->where('retailer_status',1)
        ->orderBy('retailer.name','ASC')
        ->pluck('name','id');

        $dealer_name=DB::table('dealer')
        ->where('company_id',$company_id)
        ->where('dealer_status',1)
        ->orderBy('dealer.name','ASC')
        ->pluck('name','id'); 
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person.company_id',$company_id)
        ->where('person_status',1)
        ->orderBy('person.first_name','ASC')
        ->pluck('name', 'uid');

        $beat = DB::table('location_7')
        ->where('company_id',$company_id)
        ->orderBy('location_7.name','ASC')
        ->pluck('name', 'id');

        $location_6 = DB::table('location_6')
        ->where('company_id',$company_id)
        ->orderBy('location_6.name','ASC')
        ->pluck('name', 'id');

        $location_5 = DB::table('location_5')
        ->where('company_id',$company_id)
        ->orderBy('location_5.name','ASC')
        ->pluck('name', 'id');
        $location_4 = DB::table('location_4')
        ->where('company_id',$company_id)
        ->orderBy('location_4.name','ASC')
        ->pluck('name', 'id');

        $location_3 = DB::table('location_3')
        ->where('company_id',$company_id)
        ->orderBy('location_3.name','ASC')
        ->pluck('name', 'id');

        $class_outlet_category = DB::table('_retailer_outlet_category')
        ->where('company_id',$company_id)
        ->orderBy('_retailer_outlet_category.outlet_category','ASC')
        ->pluck('outlet_category', 'id');
        // filter query ends

        for ($currentDate = $startTime; $currentDate <= $endTime;  $currentDate += (86400))
        {
            $Store = date('Y-m-d', $currentDate);
            $datesArr_new[] = $Store;
            $datesDisplayArr[] = $Store;
        }

        if($userLogin->role_id==1 || $userLogin->is_admin=='1' || $userLogin->role_id==50)
        {
            $junior_data = array();
        }
        else
        {
            Session::forget('juniordata');
            $user_data=JuniorData::getJuniorUser($userLogin->id,$company_id);
            Session::push('juniordata', $userLogin->id);
            $junior_data = Session::get('juniordata');
        }

        // retailer query for filters starts
        $newFilterOutlet = DB::table('retailer')
                        ->join('location_7','location_7.id','=','retailer.location_id')
                        ->join('location_6','location_6.id','=','location_7.location_6_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        ->join('location_3','location_3.id','=','location_4.location_3_id')
                        ->where('retailer.company_id',$company_id)
                        ->where('location_7.company_id',$company_id)
                        ->where('location_6.company_id',$company_id)
                        ->where('location_5.company_id',$company_id)
                        ->where('location_4.company_id',$company_id)
                        ->where('location_3.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(retailer.created_on,'%Y-%m-%d')<='$to_date'");
                            #Retailer type filter
                        if (!empty($request->outlet)) {
                        $outlet_type = $request->outlet;
                        $newFilterOutlet->whereIn('retailer.outlet_type_id', $outlet_type);
                        }
                        if (!empty($request->location_6)) {
                        $newFilterOutlet->whereIn('location_6.id', $request->location_6);
                        }
                        if (!empty($request->location_5)) {
                        $newFilterOutlet->whereIn('location_5.id', $request->location_5);
                        }
                        if (!empty($request->location_4)) {
                        $newFilterOutlet->whereIn('location_4.id', $request->location_4);
                        }
                        if (!empty($request->location_3)) {
                        $newFilterOutlet->whereIn('location_3.id', $request->location_3);
                        }
                        #Distributor filter
                        if (!empty($request->distributor)) {
                        $dealer_name = $request->distributor;
                        $newFilterOutlet->whereIn('retailer.dealer_id', $dealer_name);
                        }

                        #beat filter 
                        if(!empty($beat_id))
                        {
                            $newFilterOutlet->whereIn('retailer.location_id',$beat_id);
                        }
                        #User filter
                        if (!empty($request->user)) {
                        $user_id = $request->user;
                        $newFilterOutlet->whereIn('retailer.created_by_person_id', $user_id);
                        }
        $newFilter = $newFilterOutlet->groupBy('retailer.id')->pluck('retailer.id');
        // retailer query for filters ends



        // dd($newFilter);










        $newOutletCreationQuery = DB::table('retailer')
                                ->where('retailer.company_id',$company_id)
                                ->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(retailer.created_on,'%Y-%m-%d')<='$to_date'");
                                if(!empty($newFilter)){
                                    $newOutletCreationQuery->whereIn('retailer.id',$newFilter);
                                }
        $newOutletCreation = $newOutletCreationQuery
                            ->groupBy(DB::raw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')"))
                            ->pluck(DB::raw("COUNT(DISTINCT retailer.id) as retailerCount"),DB::raw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d') as date"));
        foreach ($datesArr_new as $key_new => $value_new)
        {
            $date_set = date('d-M-y',strtotime($value_new));
            // $date_set = date('Y-m-d',strtotime($value_new));
            $newOutlets =  !empty($newOutletCreation[$value_new])?$newOutletCreation[$value_new]:'0';
            $dataPointsSetBar[] =array("y" => round($newOutlets), "label" => "$date_set");
        }




       
        $outletTypeData = DB::table('_retailer_outlet_type')
                        ->join('retailer','retailer.outlet_type_id','=','_retailer_outlet_type.id')
                        ->select('_retailer_outlet_type.id as outlet_type_id','_retailer_outlet_type.outlet_type as outlet_type_name',DB::raw("COUNT(retailer.id) as retailerCount"),'retailer.outlet_type_id')
                        ->where('retailer.retailer_status','=','1')
                        ->where('_retailer_outlet_type.status','=','1')
                        ->where('_retailer_outlet_type.company_id',$company_id)
                        ->where('retailer.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(retailer.created_on,'%Y-%m-%d')<='$to_date'");
                        if(!empty($newFilter)){
                            $outletTypeData->whereIn('retailer.id',$newFilter);
                        }                      
        $outletType = $outletTypeData->groupBy('_retailer_outlet_type.id')->orderBy('_retailer_outlet_type.outlet_type','ASC')->get()->toArray();


        $databeatPoints=array();
        foreach($outletType as $graphValue)
        {
            $databeatPoints[] =array("y" => round($graphValue->retailerCount,2), "label" => "$graphValue->outlet_type_name","outlet_type_id" => "$graphValue->outlet_type_id",'from_date'=>"$from_date",'to_date'=>"$to_date");
        }


        $userRetailerDetailsQuery = DB::table('retailer')
                            ->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),DB::raw("COUNT(DISTINCT retailer.id) as retailerCount"))
                            ->join('person','person.id','=','retailer.created_by_person_id')
                            ->where('retailer.company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(retailer.created_on,'%Y-%m-%d')<='$to_date'");
                            if(!empty($newFilter)){
                                $userRetailerDetailsQuery->whereIn('retailer.id',$newFilter);
                            }     
        $userRetailerDetails = $userRetailerDetailsQuery
                            ->groupBy('person.id')
                            ->get();

          $dataPoints=array();
        foreach($userRetailerDetails as $beatValue)
        {
            $dataPoints[] = array("y" => round($beatValue->retailerCount,2), "label" => "$beatValue->user_name",'symbol'=>"$beatValue->user_id",'from_date'=>"$from_date",'to_date'=>"$to_date");
        }



        $companyDetails = DB::table('company')
                        ->where('id',$company_id)
                        ->first();

        if(empty($check)){
              $user_perfomance_data = DB::table('retailer')
                                ->join($table_name,$table_name.'.retailer_id','=','retailer.id')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                                 ->select('retailer.name as retailer_name',DB::raw("SUM(rate*quantity) as sale_value"),'retailer.id as retailer_id')
                                ->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(retailer.created_on,'%Y-%m-%d')<='$to_date'")
                                ->where('retailer.company_id',$company_id)
                                ->where($table_name.'.company_id',$company_id)
                                ->where('user_sales_order_details.company_id',$company_id)
                                ->groupBy('retailer.id')
                                 ->orderBy('sale_value','DESC')
                                ->take(5)->get();

        }else{
             $user_perfomance_data = DB::table('retailer')
                                ->join($table_name,$table_name.'.retailer_id','=','retailer.id')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=',$table_name.'.order_id')
                                ->select('retailer.name as retailer_name',DB::raw("SUM(rate*quantity) as sale_value"),'retailer.id as retailer_id')
                                ->whereRaw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(retailer.created_on,'%Y-%m-%d')<='$to_date'")
                                ->where('retailer.company_id',$company_id)
                                 ->where($table_name.'.company_id',$company_id)
                                ->where('user_sales_order_details.company_id',$company_id)
                                ->groupBy('retailer.id')
                                 ->orderBy('sale_value','DESC')
                                ->take(5)->get();

        }
            
         $maxRetailerSales=array();
        foreach($user_perfomance_data as $mrValue)
        {

            $cryptedString = Crypt::encryptString($mrValue->retailer_id);

            $maxRetailerSales[] = array("y" => round($mrValue->sale_value,2), "label" => "$mrValue->retailer_name","retailer_id" => "$mrValue->retailer_id",'from_date'=>"$from_date",'to_date'=>"$to_date",'cryptedString'=>"$cryptedString");
        }

        $price = array_column($maxRetailerSales, 'y');
        array_multisort($price, SORT_ASC, $maxRetailerSales);




        return view('retailerAnalytics',
            [
                'menu' => $this->menu,
                'current_menu' => $current_menu,
                'company_id' => $company_id,
                'outletType' => $outletType,
                'companyDetails' => $companyDetails,
                'maxRetailerSales'=>$maxRetailerSales,
                'datesArr'=>$datesDisplayArr,
                'from_date'=> $from_date,
                'to_date'=> $to_date,
                'dataPointsSetBar'=> $dataPointsSetBar,
                'dataPoints'=> $dataPoints,
                'databeatPoints'=> $databeatPoints,
                 'outlet_type' => $outlet_type,
                'retailer_name' => $retailer_name,
                'dealer_name' => $dealer_name,
                'beat' =>$beat,
                'user'=>$user,
                'location_5'=> $location_5,
                'location_6'=> $location_6,
                'to_date'=> $to_date,
                'location_3'=> $location_3,
                'location_4'=> $location_4,
                'class_outlet_category'=> $class_outlet_category,
                'request'=> $request,
            ]);


    }


      public function getDateFormat(Request $request)
    {
        $getDate = $request->date;

        $sendDate = date('Y-m-d',strtotime($getDate));


        $data['sendDate'] = $sendDate;
        $data['code'] = 200;
        $data['message'] = 'success';



        return json_encode($data);

    } 




      public function getBeatWiseAnalysis(Request $request)
    {
        // dd($request);
        $company_id = Auth::user()->company_id;
        $from_date = !empty($request->from_date)?$request->from_date:date('Y-m-01');
        $to_date = !empty($request->to_date)?$request->to_date:date('Y-m-d');
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
        $table_name = TableReturn::table_return($from_date,$to_date);


        $beatWisePerformanceData = DB::table($table_name)
                                ->join('location_7','location_7.id','=',$table_name.'.location_id')
                                ->join('location_6','location_6.id','=','location_7.location_6_id')
                                ->join('location_5','location_5.id','=','location_6.location_5_id')
                                ->join('location_4','location_4.id','=','location_5.location_4_id')
                                ->join('location_3','location_3.id','=','location_4.location_3_id')
                                // ->join('retailer','retailer.location_id','=',$table_name.'.location_id')
                                ->select(DB::raw("CONCAT_WS(' / ',location_7.name,location_6.name) as beat_name"),'location_7.id as beat_id',DB::raw("COUNT(DISTINCT retailer_id) as total_call"))
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                                ->where('location_7.company_id',$company_id)
                                 ->where($table_name.'.company_id',$company_id)
                                 ->where('call_status','=','1');
                                if (!empty($request->outlet)) {
                                $outlet_type = $request->outlet;
                                $beatWisePerformanceData->whereIn('retailer.outlet_type_id', $outlet_type);
                                }
                                if (!empty($request->location_6)) {
                                $beatWisePerformanceData->whereIn('location_6.id', $request->location_6);
                                }
                                if (!empty($request->location_5)) {
                                $beatWisePerformanceData->whereIn('location_5.id', $request->location_5);
                                }
                                if (!empty($request->location_4)) {
                                $beatWisePerformanceData->whereIn('location_4.id', $request->location_4);
                                }
                                if (!empty($request->location_3)) {
                                $beatWisePerformanceData->whereIn('location_3.id', $request->location_3);
                                }
                                #Distributor filter
                                if (!empty($request->distributor)) {
                                $dealer_name = $request->distributor;
                                $beatWisePerformanceData->whereIn('retailer.dealer_id', $dealer_name);
                                }

                                #beat filter 
                                if(!empty($beat_id))
                                {
                                    $beatWisePerformanceData->whereIn('retailer.location_id',$beat_id);
                                }
                                #User filter
                                if (!empty($request->user)) {
                                $user_id = $request->user;
                                $beatWisePerformanceData->whereIn('retailer.created_by_person_id', $user_id);
                                }
        $beatWisePerformance = $beatWisePerformanceData->groupBy('location_7.id')->orderBy('total_call','DESC')->take(10)->get()->toArray();

        $beatArray = array();
        foreach ($beatWisePerformance as $bkey => $bvalue) {
            $beatArray[] = $bvalue->beat_id;
        }

        $retailerCount = DB::table('retailer')
                        ->where('company_id',$company_id)
                        // ->where('retailer_status','=','1')
                        ->whereIn('location_id',$beatArray)
                        ->groupBy('location_id')
                        ->pluck(DB::raw("COUNT(DISTINCT retailer.id) as retCount"),'location_id');


        $beatWisePerformanceTotal  = DB::table($table_name)
                                ->join('location_7','location_7.id','=',$table_name.'.location_id')
                                ->join('location_6','location_6.id','=','location_7.location_6_id')
                                ->join('location_5','location_5.id','=','location_6.location_5_id')
                                ->join('location_4','location_4.id','=','location_5.location_4_id')
                                ->join('location_3','location_3.id','=','location_4.location_3_id')
                                // ->join('retailer','retailer.location_id','=','location_7.id')
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                                ->where('location_7.company_id',$company_id)
                                 ->where($table_name.'.company_id',$company_id)
                                // ->where('call_status','=','1')
                                ->groupBy($table_name.'.location_id')
                                ->pluck(DB::raw("COUNT(DISTINCT retailer_id) as pcall"),'location_7.id as beat_id');





        $Details = array();
        $finalDetails = array();
        $totalCall = array();
        $productiveCall = array();
        $nonproductiveCall = array();


        $beatWisePerformance = array_reverse($beatWisePerformance);

        // dd($beatWisePerformance);


        foreach ($beatWisePerformance as $key => $value) {

            $beatId = $value->beat_id;

            $retCount = !empty($retailerCount[$beatId])?$retailerCount[$beatId]:'0';

            $beatName = $value->beat_name.' : '.$retCount;

            $Details['beat_id'] = $value->beat_id;
            $Details['beat_name'] = $value->beat_name;
            $Details['total_call'] =  !empty($beatWisePerformanceTotal[$beatId])?$beatWisePerformanceTotal[$beatId]:'0';
            $Details['productive_call'] = $value->total_call;

            $tcall = !empty($beatWisePerformanceTotal[$beatId])?$beatWisePerformanceTotal[$beatId]:'0';

            $productive_call = $value->total_call;

            $npcall = $tcall-$value->total_call;

            

            $totalCall[] =array("label" => "$beatName", "y" => (int)$tcall);
            // $totalCall[] =array("label" => 10, "y" => 30);
            $productiveCall[] =array("label" => "$beatName", "y" => (int)$productive_call);
            $nonproductiveCall[] =array("label" => "$beatName", "y" => (int)$npcall);


            $finalDetails[] = $Details;
        }
        

        if(!empty($finalDetails)){
            $data['totalCall'] = $totalCall;
            $data['productiveCall'] = $productiveCall;
            $data['nonproductiveCall'] = $nonproductiveCall;
            $data['code'] = 200;
            $data['message'] = 'success';
        }else{
            $data['totalCall'] = array();
            $data['productiveCall'] = array();
            $data['nonproductiveCall'] = array();
            $data['code'] = 401;
            $data['message'] = 'Unauthorised Request!!';
        }

        return json_encode($data);

    }    











      public function getBeatWiseAnalysisGraph(Request $request)
    {
        // dd($request);
        $company_id = Auth::user()->company_id;
        $from_date = !empty($request->from_date)?$request->from_date:date('Y-m-01');
        $to_date = !empty($request->to_date)?$request->to_date:date('Y-m-d');
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();
        $table_name = TableReturn::table_return($from_date,$to_date);


        $beatWisePerformanceData = DB::table($table_name)
                                ->join('location_7','location_7.id','=',$table_name.'.location_id')
                                ->join('location_6','location_6.id','=','location_7.location_6_id')
                                ->join('location_5','location_5.id','=','location_6.location_5_id')
                                ->join('location_4','location_4.id','=','location_5.location_4_id')
                                ->join('location_3','location_3.id','=','location_4.location_3_id')
                                // ->join('retailer','retailer.location_id','=',$table_name.'.location_id')
                                ->select(DB::raw("CONCAT_WS(' / ',location_7.name,location_6.name) as beat_name"),'location_7.id as beat_id',DB::raw("COUNT(DISTINCT retailer_id) as total_call"))
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                                ->where('location_7.company_id',$company_id)
                                 ->where($table_name.'.company_id',$company_id)
                                 ->where('call_status','=','1');
                                if (!empty($request->outlet)) {
                                $outlet_type = $request->outlet;
                                $beatWisePerformanceData->whereIn('retailer.outlet_type_id', $outlet_type);
                                }
                                if (!empty($request->location_6)) {
                                $beatWisePerformanceData->whereIn('location_6.id', $request->location_6);
                                }
                                if (!empty($request->location_5)) {
                                $beatWisePerformanceData->whereIn('location_5.id', $request->location_5);
                                }
                                if (!empty($request->location_4)) {
                                $beatWisePerformanceData->whereIn('location_4.id', $request->location_4);
                                }
                                if (!empty($request->location_3)) {
                                $beatWisePerformanceData->whereIn('location_3.id', $request->location_3);
                                }
                                #Distributor filter
                                if (!empty($request->distributor)) {
                                $dealer_name = $request->distributor;
                                $beatWisePerformanceData->whereIn('retailer.dealer_id', $dealer_name);
                                }

                                #beat filter 
                                if(!empty($beat_id))
                                {
                                    $beatWisePerformanceData->whereIn('retailer.location_id',$beat_id);
                                }
                                #User filter
                                if (!empty($request->user)) {
                                $user_id = $request->user;
                                $beatWisePerformanceData->whereIn('retailer.created_by_person_id', $user_id);
                                }
        $beatWisePerformance = $beatWisePerformanceData->groupBy('location_7.id')->orderBy('total_call','DESC')->take(10)->get()->toArray();

        $beatArray = array();
        foreach ($beatWisePerformance as $bkey => $bvalue) {
            $beatArray[] = $bvalue->beat_id;
        }

        $retailerCount = DB::table('retailer')
                        ->where('company_id',$company_id)
                        // ->where('retailer_status','=','1')
                        ->whereIn('location_id',$beatArray)
                        ->groupBy('location_id')
                        ->pluck(DB::raw("COUNT(DISTINCT retailer.id) as retCount"),'location_id');


        $beatWisePerformanceTotal  = DB::table($table_name)
                                ->join('location_7','location_7.id','=',$table_name.'.location_id')
                                ->join('location_6','location_6.id','=','location_7.location_6_id')
                                ->join('location_5','location_5.id','=','location_6.location_5_id')
                                ->join('location_4','location_4.id','=','location_5.location_4_id')
                                ->join('location_3','location_3.id','=','location_4.location_3_id')
                                // ->join('retailer','retailer.location_id','=','location_7.id')
                                ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")
                                ->where('location_7.company_id',$company_id)
                                 ->where($table_name.'.company_id',$company_id)
                                // ->where('call_status','=','1')
                                ->groupBy($table_name.'.location_id')
                                ->pluck(DB::raw("COUNT(DISTINCT retailer_id) as pcall"),'location_7.id as beat_id');





        $Details = array();
        $finalDetails = array();
        $totalCall = array();
        $productiveCall = array();
        $nonproductiveCall = array();





        foreach ($beatWisePerformance as $key => $value) {

            $beatId = $value->beat_id;

            $retCount = !empty($retailerCount[$beatId])?$retailerCount[$beatId]:'0';

            $beatName = $value->beat_name.' : '.$retCount;

            $Details['beat_id'] = $value->beat_id;
            $Details['beat_name'] = $value->beat_name;
            $Details['total_call'] =  !empty($beatWisePerformanceTotal[$beatId])?$beatWisePerformanceTotal[$beatId]:'0';
            $Details['productive_call'] = $value->total_call;

            $tcall = !empty($beatWisePerformanceTotal[$beatId])?$beatWisePerformanceTotal[$beatId]:'0';

            $productive_call = $value->total_call;

            $npcall = $tcall-$value->total_call;

            

            $totalCall[] =array("label" => "$beatName", "y" => (int)$tcall);
            // $totalCall[] =array("label" => 10, "y" => 30);
            $productiveCall[] =array("label" => "$beatName", "y" => (int)$productive_call);
            $nonproductiveCall[] =array("label" => "$beatName", "y" => (int)$npcall);


            $finalDetails[] = $Details;
        }
        

        if(!empty($finalDetails)){
            $data['totalCall'] = $totalCall;
            $data['productiveCall'] = $productiveCall;
            $data['nonproductiveCall'] = $nonproductiveCall;
            $data['code'] = 200;
            $data['message'] = 'success';
        }else{
            $data['totalCall'] = array();
            $data['productiveCall'] = array();
            $data['nonproductiveCall'] = array();
            $data['code'] = 401;
            $data['message'] = 'Unauthorised Request!!';
        }

        return json_encode($data);

    }    

    public function tracking_details_custom_details(Request $request)
    {
        // $company_id = 40;
        // dd('1');
        $date = '2021-08-14';
        $old_date = '2021-08-01';
        // $old_date = date("Y-m-d", strtotime("-3 months"));
        $date_details = DB::table('user_work_tracking_dump')
                        // ->where('company_id',$company_id)
                        ->whereRaw("date_format(track_date,'%Y-%m-%d')>='$old_date' AND date_format(track_date,'%Y-%m-%d')<='$date'")
                        ->get();
        // dd($date_details);
        DB::beginTransaction();
        foreach ($date_details as $key => $value) {
            // code...
            $out = [
                'user_id'=>$value->user_id,
                'track_date'=>$value->track_date,
                'track_time'=>$value->track_time,
                'track_address'=>$value->track_address,
                'status'=>$value->status,
                'server_date_time'=>$value->server_date_time,
                'mnc_mcc_lat_cellid'=>$value->mnc_mcc_lat_cellid,
                'lat_lng'=>$value->lat_lng,
                'gps_status'=>$value->gps_status,
                'company_id'=>$value->company_id,
                'battery_status'=>$value->battery_status,
            ];
            $set_arr[] = $out;
        }
        // dd($set_arr);
    
        $collected = collect($set_arr);
        $chunked_array = $collected->chunk(1000);
        foreach ($chunked_array as  $value) {
            # code...
            $data_ins = DB::table('user_work_tracking')
                                ->insert($value->toArray());

            if($data_ins)
            {
                DB::commit();
                
            }
            else
            {
                DB::rollback();

            }

        }
    }
   
}
