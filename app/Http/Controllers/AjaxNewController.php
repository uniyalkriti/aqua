<?php

namespace App\Http\Controllers;

use App\_outletType;
use App\Catalog1;
use App\Location1;
use App\Location2;
use App\Location6;
use App\Location7;
use App\Location3;
use App\Location4;
use App\Location5;
use App\ReceiveOrder;
use App\Retailer;
use App\User;
use App\UserDetail;
use App\UserSalesOrder;
use App\CatalogProduct;
use Illuminate\Http\Request;
use DB;

class AjaxNewController extends Controller
{
    # OUT LET STATUS REPORTS
    public function outletOpeningStatusReport(Request $request)
    {
        $year=date('Y');
        if ($request->ajax() && !empty($request->state_id)) {
            $state_id = $request->state_id;
        }
       
        $monthArr=array('04'=>'APRIL','05'=>'MAY','06'=>'JUNE',
                        '07'=>'JULY','08'=>'AUGUST','09'=>'SEPTEMBER',
                        '10'=>'OCTOBER','11'=>'NOVEMBER','12'=>'DECEMBER',
                        '01'=>'JANURAY','02'=>'FEBRUARY','03'=>'MARCH');

        $monthDateArr=array($year.'-04-01'=>'APRIL',$year.'-05-01'=>'MAY',$year.'-06-01'=>'JUNE',
        $year.'-07-01'=>'JULY',$year.'-08-01'=>'AUGUST',$year.'-09-01'=>'SEPTEMBER',
        $year.'-10-01'=>'OCTOBER',$year.'-11-01'=>'NOVEMBER',$year.'-12-01'=>'DECEMBER',
        $year.'-01-01'=>'JANURAY',$year.'-02-01'=>'FEBRUARY',$year.'-03-01'=>'MARCH');
        // print_r($monthDateArr);die;

        $query=_outletType::where('status',1)->orderBy('outlet_type', 'ASC')->get();

        $statedata = Location3::where('status', 1);
        if(!empty($state_id)){
            $statedata->whereIn('id',$state_id);
        }
        $state=$statedata->get();

        $outletData=DB::table('outlet_opening_status')
        ->where('year',$year)
        ->get();
        
        $aprDate='2018-04-01';
        $outletAsOnFirstMonth=Retailer::join('location_view', 'location_view.l5_id', '=', 'retailer.location_id')
        ->whereRaw("DATE_FORMAT(created_on, '%Y-%m-%d') = '$aprDate'")
        ->select('outlet_type_id','l3_id',DB::raw('COUNT(retailer.id) as tot_ret'))
        ->groupBy('l3_id')
        ->groupBy('outlet_type_id')
        ->get();
       

        $activeOutlet=UserSalesOrder::join('retailer', 'retailer.id', '=', 'user_sales_order.retailer_id')
        ->join('location_view', 'location_view.l5_id', '=', 'retailer.location_id')
        ->whereRaw("DATE_FORMAT(date, '%Y') = '$year'")
        ->select('outlet_type_id','l3_id',DB::raw('COUNT(DISTINCT retailer_id) as tot_act_ret'),
          DB::raw('DATE_FORMAT(date,"%m") as month'))
        ->groupBy('l3_id')
        ->groupBy('outlet_type_id')
        ->groupBy('month')
        ->get();

        // -----For UNIQUE INACTIVE OUTLET MORE THAN 2 MONTH------------//
        $uniqueInActOutlet=array();
        foreach ($monthDateArr as $mkey => $mvalue)
        {
            $created_on = strtotime ( '-2 month' , strtotime ($mkey) ) ;
            $from_date= date ('m-Y' , $created_on );
            $to_date=  date('m-Y', strtotime($mkey));
            $month=  date('m', strtotime($mkey));

            $uniqInActiceOutlet=Retailer::join('location_view', 'location_view.l5_id', '=', 'retailer.location_id')
            ->whereNotIn('retailer.id', DB::table('user_sales_order')
            ->whereRaw("DATE_FORMAT(date, '%m-%Y') >= '$from_date'")
            ->whereRaw("DATE_FORMAT(date, '%m-%Y') <= '$to_date'")
            ->select('retailer_id')
            )
            ->select('l3_id', 'outlet_type_id', DB::raw('COUNT(DISTINCT retailer.id) as tot_in_act_ret'),
            DB::raw("$month as month"))
            ->groupBy('l3_id', 'outlet_type_id')
            ->get();
            foreach($uniqInActiceOutlet as $inActVal)
            {
                $state_id=  $inActVal->l3_id;
                $outlet_type=  $inActVal->outlet_type_id;
                $mk=  $inActVal->month;
                $uniqueInActOutlet[$state_id][$mk][$outlet_type]=$inActVal->tot_in_act_ret;
            }
    
        }
        // echo"<pre>";
        // print_r($uniqueInActOutlet);die;

        // #For Total Outlets During the months  
        $outData=array();
        foreach($outletData as $outVal)
        {
          $state_id=  $outVal->state_id;
          $month=  $outVal->month;
          $outlet_type=  $outVal->outlet_type_id;
          $outData[$state_id][$month][$outlet_type]=$outVal->total_outlet;
        }
        // #For Active Outlets
        $activeOutletArr=array();
        foreach($activeOutlet as $actVal)
        {
          $l3_id=  $actVal->l3_id;
          $month=  $actVal->month;
          $outlet_type_id=  $actVal->outlet_type_id;
          $activeOutletArr[$l3_id][$month][$outlet_type_id]=$actVal->tot_act_ret;
        }

        // -----For April Month Only------------//
        $firstOutlet=array();
        foreach($outletAsOnFirstMonth as $fMVal)
        {
          $l3_id=  $fMVal->l3_id;
          $outlet_type_id=  $fMVal->outlet_type_id;
          $firstOutlet[$l3_id][04][$outlet_type_id]=$fMVal->tot_ret;
        }

        
        
        //   print_r($outletData);die;

        if ($query) {
            return view('reports.outlet-opening-status.ajax', [
                'monthArr' => $monthArr,
                'state' => $state,
                'outlet_type' => $query,
                'outData' => $outData,
                'firstOutlet'=>$firstOutlet,
                'activeOutletArr'=>$activeOutletArr,
                'uniqueInActOutlet'=>$uniqueInActOutlet

            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system</p>';
        }
    }

    // public function outletAddedDuringMonth($month)
    // {
    //    return $month;
    // }

    public function complaintReport(Request $request)
    {

        if ($request->ajax() && !empty($request->user)) {
            $zone = $request->zone;
            $region = $request->region;
            $state = $request->state;
            $user_id = $request->user;


            $query = DB::table('Complaint_report');

            $query_data = $query->orderBy('created_at', 'DESC')
                ->get();
            // print_r($query_data );die;
            return view('reports.complaint-report.ajax', [
                'records' => $query_data
            ]);
        } else {
            echo '<p class="alert-danger">Do not hack the system123</p>';
        }


    }

}
