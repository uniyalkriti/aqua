<?php

namespace App\Http\Controllers;

use App\_outletType;
use App\Area;
use App\Catalog1;
use App\Claim;
use App\competitorBrand;
use App\CompetitorsPriceLog;
use App\competitorsProduct;
use App\DailyStock;
use App\Dealer;
use App\DealerLocation;
use App\DealerRetailer;
use App\DistributorTarget;
use App\Feedback;
use App\Location1;
use App\Location2;
use App\Location6;
use App\Location7;
use App\Location3;
use App\Location4;
use App\Location5; 
use App\MonthlyTourProgram;
use App\ReceiveOrder;
use App\Retailer;
use App\User;
use App\SS;
use App\Person;
use App\UserDealerRetailer;
use App\UserDetail;
use App\UserExpenseReport;
use App\UserSalesOrder;
use App\Vendor;
use App\CatalogProduct;
use App\UsersDetail;
use Illuminate\Http\Request;
use DB;
use Auth;
use Session;
use DateTime;
use PDF;


class NewReportController extends Controller
{
	public function dsrMonthlyReportNew(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->state; 
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();


        $catalog_product_data = DB::table('catalog_product')
                            ->where('status',1)
                            ->where('company_id',$company_id)
                            ->groupBy('id')
                            ->orderBy('id','asc');
                            if(!empty($request->product))
                            {
                                $catalog_product_data->whereIn('id',$request->product);
                            }
                              if(!empty($request->catalog_2))
                            {
                                $catalog_product_data->whereIn('catalog_id',$request->catalog_2);
                            }
        $catalog_product = $catalog_product_data->get();
        
        $person_details = Person::join('person_login','person_login.person_id','=','person.id')
                        ->join('user_sales_order','user_sales_order.user_id','=','person.id')
                        ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->select('rolename','person.mobile as mobile','user_sales_order.date as date','l3_name','l4_name','l5_name','l6_name',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as person_fullname"),'person.id as user_id','person.state_id as state_id','person.emp_code','person.person_id_senior')
                        ->where('person.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
                        ->groupBy('user_id','date')
                        ->orderBy('person.state_id','ASC')
                        ->orderBy('rolename','ASC');

        if(!empty($state))
        {
            $person_details->where('person.state_id',$state);
        }
        if (!empty($request->location_3)) 
        {
            $location_3 = $request->location_3;
            $person_details->whereIn('l3_id', $location_3);
        }
        if (!empty($request->location_4)) 
        {
            $location_4 = $request->location_4;
            $person_details->whereIn('l4_id', $location_4);
        }
        if (!empty($request->location_5)) 
        {
            $location_5 = $request->location_5;
            $person_details->whereIn('l5_id', $location_5);
        }
        if (!empty($request->location_6)) 
        {
            $location_6 = $request->location_6;
            $person_details->whereIn('l6_id', $location_6);
        }
        if (!empty($request->dealer)) 
        {
            $dealer = $request->dealer;
            $person_details->whereIn('dealer_id', $dealer);
        }
        if (!empty($request->user)) 
        {
            $user = $request->user;
            $person_details->whereIn('user_id', $user);
        }
        if (!empty($request->role)) 
        {
            $role = $request->role;
            $person_details->whereIn('person.role_id', $role);
        }

        $person = $person_details->get();
        // dd($person);
        if(empty($check)){
        $dsr =  DB::table('user_sales_order')->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")->groupBy('product_id','user_id','date')->where('user_sales_order.company_id',$company_id)->pluck(DB::raw("SUM(user_sales_order_details.quantity) as product_quantity"),DB::raw("CONCAT(user_id,product_id,date)"));
        }
        else{
        $dsr =  DB::table('user_sales_order')->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")->groupBy('product_id','user_id','date')->where('user_sales_order.company_id',$company_id)->pluck(DB::raw("SUM(user_sales_order_details.final_secondary_qty) as product_quantity"),DB::raw("CONCAT(user_id,product_id,date)"));
        }
        // dd($dsr);
        $market_data  = DB::table('user_sales_order as uso')->join('location_7','location_7.id','=','uso.location_id')->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")->groupBy('user_id','date')->where('uso.company_id',$company_id)->pluck('location_7.name as market',DB::raw("CONCAT(user_id,date)"));

        $total_call_data = DB::table('user_sales_order')->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")->groupBy('user_id','date')->where('user_sales_order.company_id',$company_id)->pluck(DB::raw('count(order_id) as total_call'),DB::raw("CONCAT(user_id,date)"));

        $productive_call_data = DB::table('user_sales_order')->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")->where('call_status',1)->groupBy('user_id','date')->where('user_sales_order.company_id',$company_id)->pluck(DB::raw('count(call_status) as total_call'),DB::raw("CONCAT(user_id,date)"));

        if(empty($check)){
        $product_amount_data_data = DB::table('user_sales_order')
                            ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                            ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")
                            ->where('user_sales_order.company_id',$company_id)
                            ->groupBy('user_id','date');
                            if(!empty($request->product))
                            {
                                $product_amount_data_data->whereIn('product_id',$request->product);
                            }
        $product_amount_data = $product_amount_data_data->pluck(DB::raw("SUM(user_sales_order_details.quantity*user_sales_order_details.rate) as product_quantity_amount"),DB::raw("CONCAT(user_id,date)"));
        }else{
        $product_amount_data_data = DB::table('user_sales_order')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")
                                ->where('user_sales_order.company_id',$company_id)
                                ->groupBy('user_id','date');
                                if(!empty($request->product))
                                {
                                    $product_amount_data_data->whereIn('product_id',$request->product);
                                }
        $product_amount_data = $product_amount_data_data->pluck(DB::raw("SUM(final_secondary_qty*final_secondary_rate) as product_quantity_amount"),DB::raw("CONCAT(user_id,date)"));
        }

        $out=array();
        if (!empty($person)) {
                foreach ($person as $k => $d) {
                    $uid=$d->user_id;
                    $date= $d->date;
                    $out[$uid][$date]['user'] = $uid;
                    $out[$uid][$date]['date'] = $date;
                    $out[$uid][$date]['market'] = !empty($market_data[$uid.$date])?$market_data[$uid.$date]:'0';

                    $out[$uid][$date]['total_call'] = !empty($total_call_data[$uid.$date])?$total_call_data[$uid.$date]:'0';

                    $out[$uid][$date]['productive_call'] = !empty($productive_call_data[$uid.$date])?$productive_call_data[$uid.$date]:'0';

                    $out[$uid][$date]['product_amount'] = !empty($product_amount_data[$uid.$date])?$product_amount_data[$uid.$date]:'0';
                }
            }
             // dd($out);
            return view('reports.dsr-monthly.ajax', [
            'person' => $person,
            'productData' => $out,
            'dsr'=>$dsr,
            'catalog_product' => $catalog_product,

        ]);

    }


    public function tourProgramReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        // $explodeDate = explode("-", $request->month);
        $from_date = date('Y-m-01',strtotime(trim($request->month)));
        $to_date = date('Y-m-t',strtotime(trim($request->month)));

        
        $start = strtotime($from_date);
        $end = strtotime($to_date);


        $datearray = array();
        $datediff =  ($end - $start)/60/60/24;
        $datearray[] = $from_date;

        for($i=0 ; $i<$datediff;$i++)
        {
            $datearray[] = date('Y-m-d', strtotime($datearray[$i] .' +1 day'));
        }



    $person_details = DB::table('person')
             ->join('person_login','person_login.person_id','=','person.id')
             ->join('_role','_role.role_id','=','person.role_id')
             ->join('users','users.id','=','person.id')
             ->select('person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'person.mobile as mobile','rolename','head_quater_id','town_id')
             ->where('person_status',1)
             ->where('is_admin','!=',1)
             ->where('person.company_id',$company_id)
             ->where('_role.company_id',$company_id)
             ->where('person_login.company_id',$company_id)
             ->where('users.company_id',$company_id)
             ->groupBy('person.id');

              if (!empty($request->location_3)) 
        {
            $location_3 = $request->location_3;
            $person_details->whereIn('person.state_id', $location_3);
        }
        // if (!empty($request->location_4)) 
        // {
        //     $location_4 = $request->location_4;
        //     $person_details->whereIn('l4_id', $location_4);
        // }
        if (!empty($request->location_5)) 
        {
            $location_5 = $request->location_5;
            $person_details->whereIn('person.head_quater_id', $location_5);
        }
        if (!empty($request->location_6)) 
        {
            $location_6 = $request->location_6;
            $person_details->whereIn('person.town_id', $location_6);
        }
        if (!empty($request->user)) 
        {
            $user = $request->user;
            $person_details->whereIn('person.id', $user);
        }
        if (!empty($request->role)) 
        {
            $role = $request->role;
            $person_details->whereIn('person.role_id', $role);
        }

    $userData =  $person_details->get();

    $headQuarter = DB::table('location_6')
                ->where('company_id',$company_id)
                ->pluck('name','id');

    $townData = DB::table('location_6')
                ->where('company_id',$company_id)
                ->pluck('name','id');


    $dealerData = DB::table('dealer')
                ->where('company_id',$company_id)
                ->pluck('name','id');

    $dealerContactData = DB::table('dealer')
                ->where('company_id',$company_id)
                ->pluck('other_numbers','id');


    $beatData = DB::table('location_7')
                ->where('company_id',$company_id)
                ->pluck('name','id');



    $mtpData = DB::table('monthly_tour_program')
            ->select('town','dealer_id','locations','working_date','person_id')
            ->where('monthly_tour_program.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d')<='$to_date' ")
            ->groupBy('working_date','person_id')
            ->get();

    $finalOut = array();
    foreach ($mtpData as $mtpDataKey => $mtpDataValue) {
        $mtpDate = $mtpDataValue->working_date;
        $mtpUserId = $mtpDataValue->person_id;
        $mtpTownId = $mtpDataValue->town;
        $mtpDealerId = $mtpDataValue->dealer_id;
        $mtpBeatId = $mtpDataValue->locations;

        $explodeTown = explode(",",$mtpTownId);
        $explodeTownArray = array();
        foreach ($explodeTown as $explodeTownKey => $explodeTownValue) {
                $explodeTownArray[] = !empty($townData[$explodeTownValue])?$townData[$explodeTownValue]:'';
        }
        $implodedTown = implode(",",$explodeTownArray);

        $explodeDealer = explode(",",$mtpDealerId);
        $explodeDealerArray = array();
        foreach ($explodeDealer as $explodeDealerKey => $explodeDealerValue) {
                $explodeDealerArray[] = !empty($dealerData[$explodeDealerValue])?$dealerData[$explodeDealerValue]:'';
        }
        $implodedDealer = implode(",",$explodeDealerArray);

        $explodeDealerContact = explode(",",$mtpDealerId);
        $explodeDealerContactArray = array();
        foreach ($explodeDealerContact as $explodeDealerContactKey => $explodeDealerContactValue) {
                $explodeDealerContactArray[] = !empty($dealerContactData[$explodeDealerContactValue])?$dealerContactData[$explodeDealerContactValue]:'';
        }
        $implodedDealerContact = implode(",",$explodeDealerContactArray);


        $explodeBeat = explode(",",$mtpBeatId);
        $explodeBeatArray = array();
        foreach ($explodeBeat as $explodeBeatKey => $explodeBeatValue) {
                $explodeBeatArray[] = !empty($beatData[$explodeBeatValue])?$beatData[$explodeBeatValue]:'';
        }
        $implodedBeat = implode(",",$explodeBeatArray);

        $out['mtpDate'] = $mtpDate;
        $out['mtpUserId'] = $mtpUserId;
        $out['mtpTown'] = $implodedTown;
        $out['mtpDealer'] = $implodedDealer;
        $out['mtpDealerContact'] = $implodedDealerContact;
        $out['mtpBeat'] = $implodedBeat;


        $finalOut[$mtpDate.$mtpUserId] = $out;
    }

            // dd($finalOut);
    $dynamicCount = COUNT($userData);


    $actualSaleData = DB::table('user_sales_order')
            ->select(DB::raw("GROUP_CONCAT(DISTINCT dealer_id) as dealer_id"),DB::raw("GROUP_CONCAT(DISTINCT location_id) as locations"),'date as working_date','user_id as person_id')
            ->where('user_sales_order.company_id',$company_id)
            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND  DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date' ")
            ->groupBy('date','user_id')
            ->get();

    $finalSaleOut = array();
    foreach ($actualSaleData as $saleDataKey => $saleDataValue) {
        $saleDate = $saleDataValue->working_date;
        $saleUserId = $saleDataValue->person_id;
        $saleDealerId = $saleDataValue->dealer_id;
        $saleBeatId = $saleDataValue->locations;

   
        $explodeDealer = explode(",",$saleDealerId);
        $explodeDealerArray = array();
        foreach ($explodeDealer as $explodeDealerKey => $explodeDealerValue) {
                $explodeDealerArray[] = !empty($dealerData[$explodeDealerValue])?$dealerData[$explodeDealerValue]:'';
        }
        $implodedDealer = implode(",",$explodeDealerArray);

    


        $explodeBeat = explode(",",$saleBeatId);
        $explodeBeatArray = array();
        foreach ($explodeBeat as $explodeBeatKey => $explodeBeatValue) {
                $explodeBeatArray[] = !empty($beatData[$explodeBeatValue])?$beatData[$explodeBeatValue]:'';
        }
        $implodedBeat = implode(",",$explodeBeatArray);

        $outSale['saleDate'] = $saleDate;
        $outSale['saleUserId'] = $saleUserId;
        $outSale['saleDealer'] = $implodedDealer;
        $outSale['saleBeat'] = $implodedBeat;


        $finalSaleOut[$saleDate.$saleUserId] = $outSale;
    }






        return view('reports.tourProgramReport.ajax', [
                'finalOut' => $finalOut,
                'finalSaleOut' => $finalSaleOut,
                'datearray' => $datearray,
                'userData' => $userData,
                'headQuarter' => $headQuarter,
                'dynamicCount' => $dynamicCount,
            ]);
    }



     public function dsrMonthlyNehaReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $state = $request->state; 
        // $explodeDate = explode(" -", $request->date_range_picker);
        // $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        // $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

          $from_date = $request->from_date;
        $to_date = $request->from_date;

        $location_5 = DB::table('location_5')->where('company_id',$company_id)->where('status','=','1')->pluck('id');


        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();





          $catalogTwoDataData = DB::table('catalog_product')
                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                        ->select(DB::raw("COUNT(catalog_product.id) as dynamicData"),'catalog_2.id as categoryId','catalog_2.name as categoryName')
                        ->where('catalog_product.company_id',$company_id)
                        ->where('catalog_2.company_id',$company_id)
                        ->where('catalog_2.status',1)
                        ->where('catalog_product.status',1)
                        ->groupBy('catalog_2.id')
                        ->orderBy('catalog_2.id','ASC');
                         if(!empty($request->product))
                            {
                                $catalogTwoDataData->whereIn('catalog_product.id',$request->product);
                            }
                              if(!empty($request->catalog_2))
                            {
                                $catalogTwoDataData->whereIn('catalog_2.id',$request->catalog_2);
                            }
        $catalogTwoData = $catalogTwoDataData->get();


        $catalog_product_data = DB::table('catalog_product')
                            ->where('status',1)
                            ->where('company_id',$company_id)
                            ->groupBy('id')
                            ->orderBy('catalog_id','asc');
                            if(!empty($request->product))
                            {
                                $catalog_product_data->whereIn('id',$request->product);
                            }
                              if(!empty($request->catalog_2))
                            {
                                $catalog_product_data->whereIn('catalog_id',$request->catalog_2);
                            }
        $catalog_product = $catalog_product_data->get();
        
        $person_details = Person::join('person_login','person_login.person_id','=','person.id')
                        ->join('location_3','location_3.id','=','person.state_id')
                        ->join('location_6','location_6.id','=','person.town_id')
                        ->join('location_5','location_5.id','=','location_6.location_5_id')
                        ->join('location_4','location_4.id','=','location_5.location_4_id')
                        // ->join('user_sales_order','user_sales_order.user_id','=','person.id')
                        // ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                        ->join('_role','_role.role_id','=','person.role_id')
                        ->select('rolename','person.mobile as mobile','location_3.name as l3_name','location_3.id as l3_id','location_4.name as l4_name','location_5.name as l5_name','location_5.id as l5_id','location_6.name as l6_name',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as person_fullname"),'person.id as user_id','person.state_id as state_id','person.emp_code','person.person_id_senior')
                        ->where('person.company_id',$company_id)
                        ->where('location_3.company_id',$company_id)
                        ->where('location_4.company_id',$company_id)
                        ->where('location_5.company_id',$company_id)
                        ->where('location_6.company_id',$company_id)
                        ->where('person_login.person_status','=','1')
                        // ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
                        ->groupBy('user_id')->orderBy('l3_name','ASC')->orderBy('l5_name','ASC');

        if(!empty($state))
        {
            $person_details->where('person.state_id',$state);
        }
        if (!empty($request->location_3)) 
        {
            $location_3 = $request->location_3;
            $person_details->whereIn('location_3.id', $location_3);
        }
        if (!empty($request->location_4)) 
        {
            $location_4 = $request->location_4;
            $person_details->whereIn('location_4.id', $location_4);
        }
        if (!empty($request->location_5)) 
        {
            $location_5 = $request->location_5;
            $person_details->whereIn('location_5.id', $location_5);
        }
        if (!empty($request->location_6)) 
        {
            $location_6 = $request->location_6;
            $person_details->whereIn('location_6.id', $location_6);
        }
        if (!empty($request->dealer)) 
        {
            $dealer = $request->dealer;
            $person_details->whereIn('dealer_id', $dealer);
        }
        if (!empty($request->user)) 
        {
            $user = $request->user;
            $person_details->whereIn('user_id', $user);
        }
        if (!empty($request->role)) 
        {
            $role = $request->role;
            $person_details->whereIn('person.role_id', $role);
        }

        $person = $person_details->get();
        // dd($person);


        // for calc neha


        $ProductType = DB::table('product_type')->where('status','1')->where('company_id',$company_id)->pluck('name','id')->toArray(); 

        $finalProductTypeOut = array();

        foreach ($ProductType as $key => $value) {
           $finalProductTypeOut[$key] = $value;
       }   
       $finalProductTypeOut['0'] = "Pieces";

       // dd($finalProductTypeOut);



       $finalCatalogProduct = DB::table('product_type')
                                ->where('product_type.company_id',$company_id)
                                ->groupBy('product_type.id')
                                ->pluck('flag_neha','product_type.id')->toArray();


        // for calc neha ends




        if(empty($check)){

            if($company_id != '44'){

                $dsr =  DB::table('user_sales_order')
                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
                ->groupBy('product_id','user_id','date')->where('user_sales_order.company_id',$company_id)
                ->pluck(DB::raw("SUM(user_sales_order_details.quantity) as product_quantity"),DB::raw("CONCAT(user_id,product_id,date)"));

            }else{

                //     $dsr =  DB::table('user_sales_order')
                // ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                // ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
                // ->groupBy('product_id','user_id','date')->where('user_sales_order.company_id',$company_id)
                // ->pluck(DB::raw("SUM(user_sales_order_details.quantity) as product_quantity"),DB::raw("CONCAT(user_id,product_id,date)"));


                $finalOutDsr =  DB::table('user_sales_order')
                ->select(DB::raw("SUM(user_sales_order_details.quantity) as product_quantity"),DB::raw("CONCAT(user_sales_order.user_id,user_sales_order_details.product_id,user_sales_order.date) as concat"),'user_sales_order.user_id','user_sales_order_details.product_id','user_sales_order.date','catalog_product.final_product_type','catalog_product.quantity_per_case as quantity_per_case','catalog_product.quantiy_per_other_type as quantiy_per_other_type')
                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')

                ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")
                ->groupBy('user_sales_order_details.product_id','user_sales_order.user_id','user_sales_order.date')
                ->where('user_sales_order.company_id',$company_id)
                ->get();


                foreach($finalOutDsr as $dskey => $dsval){
                    $product_quantity = $dsval->product_quantity;
                    $concat = $dsval->concat;
                    $user_id = $dsval->user_id;
                    $product_id = $dsval->product_id;
                    $date = $dsval->date;
                    $final_product_type = $dsval->final_product_type;

                    $flagNeha = !empty($finalCatalogProduct[$final_product_type])?$finalCatalogProduct[$final_product_type]:'0';



                    // $dsr[$concat]['concat'] = $concat;
                    // $dsr[$concat]['user_id'] = $user_id;
                    // $dsr[$concat]['product_id'] = $product_id;
                    // $dsr[$concat]['date'] = $date;

                    if($flagNeha == '0'){
                     $dsr[$concat] =  $dsval->product_quantity;
                    }else{


                        if($flagNeha == '1'){
                            $dsr[$concat] =  ROUND(($dsval->product_quantity/$dsval->quantity_per_case),2);
                        }elseif($flagNeha == '2'){
                            $dsr[$concat] =  ROUND(($dsval->product_quantity/$dsval->quantiy_per_other_type),2);
                        }else{
                            $dsr[$concat] =  '0';
                        }
                    }
                }
            }


        }
        else{
        $dsr =  DB::table('user_sales_order')->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date, '%Y-%m-%d') <= '$to_date'")->groupBy('product_id','user_id','date')->where('user_sales_order.company_id',$company_id)->pluck(DB::raw("SUM(user_sales_order_details.final_secondary_qty) as product_quantity"),DB::raw("CONCAT(user_id,product_id,date)"));
        }
        // dd($dsr);
        // $market_data  = DB::table('user_sales_order as uso')
        //                 ->join('location_7','location_7.id','=','uso.location_id')
        //                 ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")
        //                 ->groupBy('user_id','date')->where('uso.company_id',$company_id)
        //                 ->pluck('location_7.name as market',DB::raw("CONCAT(user_id,date)"));



        $market_data = DB::table('monthly_tour_program')
                        ->join('location_7','location_7.id','=','monthly_tour_program.locations')
                         ->whereRaw("DATE_FORMAT(working_date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(working_date, '%Y-%m-%d') <= '$to_date'")
                        ->groupBy('person_id','working_date')->where('monthly_tour_program.company_id',$company_id)
                        ->pluck('location_7.name as market',DB::raw("CONCAT(person_id,working_date)"));




        $total_call_data = DB::table('user_sales_order')->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")->groupBy('user_id','date')->where('user_sales_order.company_id',$company_id)->pluck(DB::raw('count(order_id) as total_call'),DB::raw("CONCAT(user_id,date)"));

        $productive_call_data = DB::table('user_sales_order')->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")->where('call_status',1)->groupBy('user_id','date')->where('user_sales_order.company_id',$company_id)->pluck(DB::raw('count(call_status) as total_call'),DB::raw("CONCAT(user_id,date)"));

        if(empty($check)){
        $product_amount_data_data = DB::table('user_sales_order')
                            ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                            ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")
                            ->where('user_sales_order.company_id',$company_id)
                            ->groupBy('user_id','date');
                            if(!empty($request->product))
                            {
                                $product_amount_data_data->whereIn('product_id',$request->product);
                            }
        $product_amount_data = $product_amount_data_data->pluck(DB::raw("SUM(user_sales_order_details.quantity*user_sales_order_details.rate) as product_quantity_amount"),DB::raw("CONCAT(user_id,date)"));
        }else{
        $product_amount_data_data = DB::table('user_sales_order')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")
                                ->where('user_sales_order.company_id',$company_id)
                                ->groupBy('user_id','date');
                                if(!empty($request->product))
                                {
                                    $product_amount_data_data->whereIn('product_id',$request->product);
                                }
        $product_amount_data = $product_amount_data_data->pluck(DB::raw("SUM(final_secondary_qty*final_secondary_rate) as product_quantity_amount"),DB::raw("CONCAT(user_id,date)"));
        }


        $uniqueSkuSold = DB::table('user_sales_order')
                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                        ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date'")
                        ->where('user_sales_order.company_id',$company_id)
                        ->where('user_sales_order_details.company_id',$company_id)
                        ->where('call_status','=','1')
                        ->groupBy('user_id','date')
                        ->pluck(DB::raw("COUNT(DISTINCT user_sales_order_details.id) as uniqueSKU"),DB::raw("CONCAT(user_id,date)"));


        $totalSecondaryCasesSold = DB::table('user_sales_order')
                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                    ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$to_date' ")
                                    ->where('user_sales_order.company_id',$company_id)
                                    ->where('user_sales_order_details.company_id',$company_id)
                                    ->where('call_status','=','1')
                                    ->groupBy('user_id','date')
                                    ->pluck(DB::raw("ROUND(SUM(user_sales_order_details.quantity/catalog_product.quantity_per_case)) as CasesSold"),DB::raw("CONCAT(user_id,date)"));



        $attendanceRemarks = DB::table('user_daily_attendance')
                            ->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(work_date,'%Y-%m-%d') <= '$to_date' ")
                            ->where('user_daily_attendance.company_id',$company_id)
                            ->groupBy('user_id','work_date')
                            ->pluck('remarks',DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"));


        // $attendanceWorkStatus = DB::table('user_daily_attendance')
        //                     ->join('_working_status','_working_status.id','=','user_daily_attendance.work_status')
        //                     ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') <= '$to_date' ")
        //                     ->where('user_daily_attendance.company_id',$company_id)
        //                     ->groupBy('user_daily_attendance.user_id','user_daily_attendance.work_date')
        //                     ->pluck('_working_status.name',DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"));


        $attendanceWorkStatus = DB::table('monthly_tour_program')
                            ->join('_task_of_the_day','_task_of_the_day.id','=','monthly_tour_program.working_status_id')
                            ->whereRaw("DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(monthly_tour_program.working_date,'%Y-%m-%d') <= '$to_date' ")
                            ->where('monthly_tour_program.company_id',$company_id)
                            ->groupBy('monthly_tour_program.person_id','monthly_tour_program.working_date')
                            ->pluck('monthly_tour_program.working_status_id',DB::raw("CONCAT(person_id,DATE_FORMAT(working_date,'%Y-%m-%d'))"));

        $taskOfTheDay = DB::table('_task_of_the_day')->where('company_id',$company_id)->pluck('task','id');


        $attendanceWorkWith = DB::table('user_daily_attendance')
                            ->join('person','person.id','=','user_daily_attendance.working_with')
                            ->whereRaw("DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') <= '$to_date' ")
                            ->where('user_daily_attendance.company_id',$company_id)
                            ->groupBy('user_daily_attendance.user_id','user_daily_attendance.work_date')
                            ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as person_fullname"),DB::raw("CONCAT(user_id,DATE_FORMAT(work_date,'%Y-%m-%d'))"));

                            // dd($attendanceWorkWith);

        $out=array();
        if (!empty($person)) {
                foreach ($person as $k => $d) {
                    $uid=$d->user_id;
                    $date = $from_date;
                    $out[$uid][$date]['user'] = $uid;
                    $out[$uid][$date]['date'] = $date;
                    $out[$uid][$date]['market'] = !empty($market_data[$uid.$date])?$market_data[$uid.$date]:'';

                    $out[$uid][$date]['total_call'] = !empty($total_call_data[$uid.$date])?$total_call_data[$uid.$date]:'0';

                    $out[$uid][$date]['productive_call'] = !empty($productive_call_data[$uid.$date])?$productive_call_data[$uid.$date]:'0';

                    $out[$uid][$date]['product_amount'] = !empty($product_amount_data[$uid.$date])?$product_amount_data[$uid.$date]:'0';

                    $out[$uid][$date]['linesSold'] = !empty($uniqueSkuSold[$uid.$date])?$uniqueSkuSold[$uid.$date]:'0';

                    $out[$uid][$date]['secondaryCasesSold'] = !empty($totalSecondaryCasesSold[$uid.$date])?$totalSecondaryCasesSold[$uid.$date]:'0';


                }
            }
             // dd($out);

            $scheme_amount = DB::table('user_sales_order')
                            ->where('company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') <= '$to_date' ")
                            ->groupBy('user_id')
                            ->groupBy('date')
                            ->pluck(DB::raw("SUM(total_sale_value) as sale"),DB::raw("CONCAT(user_id,date)"));



        


       // dd($finalCatalogProduct);




            return view('reports.dsrMonthlyNeha.ajax', [
            'person' => $person,
            'productData' => $out,
            'dsr'=>$dsr,
            'catalog_product' => $catalog_product,
            'catalogTwoData' => $catalogTwoData,
            'location_5' => $location_5,
            'attendanceRemarks' => $attendanceRemarks,
            'attendanceWorkStatus' => $attendanceWorkStatus,
            'attendanceWorkWith' => $attendanceWorkWith,
            'scheme_amount' => $scheme_amount,
            'from_date' => $from_date,
            'flagNeha' => $flagNeha,
            'finalCatalogProduct' => $finalCatalogProduct,
            'finalProductTypeOut' => $finalProductTypeOut,
            'taskOfTheDay' => $taskOfTheDay,


        ]);

    }

}