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
use App\UserDetail;
use App\UserExpenseReport;
use App\UserSalesOrder;
use App\Vendor;
use App\CatalogProduct;
use App\UsersDetail;
use Illuminate\Http\Request;
use DB;
use Auth;
use App\MyConfiguration;
use DateTime;
use Session;
use PDF;

class AjaxDailyTeamController extends Controller
{

    public function dtrpanel1(Request $request)
    {
        $region = $request->region;
        $user = $request->user;
        $date = $request->date;

        $month = date('Y-m', strtotime($date));
        $startDate = $month . "-01";
        $days = date('d', strtotime($date));
        $start = new DateTime($startDate);
        $sundays = floor($days / 7) + ($start->format('N') + $days % 7 >= 7);
        $totalOff = $sundays + 1;
        $workingDays = $days - $totalOff;
        $lastmonthDate = date('Y-m-d', strtotime('-1 month', strtotime($date)));
        $lastMonth = date('Y-m', strtotime($lastmonthDate));
        $startlastDate = $lastMonth . "-01";
        $_SESSION['juniordata'] = array();

        DB::delete('delete from users_junior_hierarchy where senior_id = ?', [$user]);
        $details = DB::table('person')
            ->where('id', $user)->where('status', '1')
            ->select('id', DB::raw("CONCAT(person.first_name,' ',person.last_name) as uname"),
                'role_id')->first();
        DB::table('users_junior_hierarchy')->insert(
            ['senior_id' => $user,
                'junior_id' => $user,
                'junior_name' => $details->uname,
                'role_id' => $details->role_id
                , 'created_at' => now()
                , 'updated_at' => now()]
        );
        $myobj = new MyConfiguration();
        $myobj->getJuniorPersonWithSenior($user, $user);

        $junior = $_SESSION['juniordata'];
        //  print_r($_SESSION['juniordata']); exit;
        $userName = DB::table('person')->where('id', $user)->select(DB::raw("CONCAT(person.first_name,' ',person.last_name) as uname"))->first();


        $manpowerstatus = DB::table('man_power_status')->where(['man_power_status.user_id' => $user])
            ->get();

        $retailing = DB::table('user_sales_order')
            ->whereIn('user_id', $junior)
            ->whereRaw("DATE_FORMAT(user_sales_order.date, '%Y-%m') = '$month'")
            ->select('role_id', DB::raw('count(order_id) as total'))
            ->join('person', 'person.id', '=', 'user_sales_order.user_id')
            ->groupBy('role_id')
            ->pluck('total', 'role_id');
        //dd($month);
        // RD STATUS //
        $rdStatusQuery = DB::table('users_junior_hierarchy')
            ->select('junior_id as user_id', 'junior_name as uname',
                'target', 'achievement', 'month', '_role.rolename as rolename')
            ->leftJoin('user_sale_target', function ($join) use ($month) {
                $join->on('user_sale_target.user_id', '=', 'users_junior_hierarchy.junior_id');
                $join->on(DB::raw("DATE_FORMAT(user_sale_target.month,'%Y-%m')"), '=', DB::raw("'$month'"));

            })
            ->join('_role', '_role.role_id', '=', 'users_junior_hierarchy.role_id', 'INNER')
            ->where('users_junior_hierarchy.senior_id', $user)
            ->orderBy('users_junior_hierarchy.id')
            ->get();
        $rdStatus = array();
        $ird = 0;
        foreach ($rdStatusQuery as $keyrd => $valuerd) {
            $rdStatus[$ird]['user_id'] = $valuerd->user_id;
            $rdStatus[$ird]['uname'] = $valuerd->uname;
            $rdStatus[$ird]['target'] = $valuerd->target;
            $rdStatus[$ird]['achievement'] = $valuerd->achievement;
            $rdStatus[$ird]['month'] = $valuerd->month;
            $rdStatus[$ird]['rolename'] = $valuerd->rolename;
            $thisKg = DB::table('sale_view')
                ->select(DB::raw("SUM(quantity*weight) as weight"))
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $date)
                ->where('user_id', '=', $valuerd->user_id)
                ->first();
            $lastKg = DB::table('sale_view')
                ->select(DB::raw("SUM(quantity*weight) as weight"))
                ->where('date', '>=', $startlastDate)
                ->where('date', '<=', $lastmonthDate)
                ->where('user_id', '=', $valuerd->user_id)
                ->first();
            $rdStatus[$ird]['thisKg'] = $thisKg->weight / 1000;
            $rdStatus[$ird]['lastKg'] = $lastKg->weight / 1000;
            $ird++;
        }

// OUTLET STATUS //
        $outletStatusquery = DB::table('users_junior_hierarchy')
            ->select('junior_id as user_id', 'junior_name as uname',
                'target', 'achievement', 'month', '_role.rolename as rolename')
            ->leftJoin('user_retailer_target', function ($joino) use ($month) {
                $joino->on('user_retailer_target.user_id', '=', 'users_junior_hierarchy.junior_id');
                $joino->on(DB::raw("DATE_FORMAT(user_retailer_target.month,'%Y-%m')"), '=', DB::raw("'$month'"));
            })
            ->join('_role', '_role.role_id', '=', 'users_junior_hierarchy.role_id', 'INNER')
            ->where('users_junior_hierarchy.senior_id', $user)
            ->orderBy('users_junior_hierarchy.id')
            ->get();
        $outletStatus = array();
        $iout = 0;
        foreach ($outletStatusquery as $keyOutlet => $valueOutlet) {
            $outletStatus[$iout]['user_id'] = $valueOutlet->user_id;
            $outletStatus[$iout]['uname'] = $valueOutlet->uname;
            $outletStatus[$iout]['target'] = $valueOutlet->target;
            $outletStatus[$iout]['achievement'] = $valueOutlet->achievement;
            $outletStatus[$iout]['rolename'] = $valueOutlet->rolename;
            $visit = DB::table('user_sales_order')
                ->select(DB::raw("count(DISTINCT retailer_id) as retailer"))
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $date)
                ->where('user_id', '=', $valueOutlet->user_id)
                ->first();
            $visitActive = DB::table('user_sales_order')
                ->select(DB::raw("count(DISTINCT retailer_id) as retailer"))
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $date)
                ->where('user_id', '=', $valueOutlet->user_id)
                ->where('call_status', '=', '1')
                ->first();
            $visitActiveLast = DB::table('user_sales_order')
                ->select(DB::raw("count(DISTINCT retailer_id) as retailer"))
                ->where('date', '>=', $startlastDate)
                ->where('date', '<=', $lastmonthDate)
                ->where('user_id', '=', $valueOutlet->user_id)
                ->where('call_status', '=', '1')
                ->first();
            $perolKg = DB::table('sale_view')
                ->select(DB::raw("SUM(quantity*weight) as weight"))
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $date)
                ->where('user_id', '=', $valueOutlet->user_id)
                ->first();
            $perolKgLast = DB::table('sale_view')
                ->select(DB::raw("SUM(quantity*weight) as weight"))
                ->where('date', '>=', $startlastDate)
                ->where('date', '<=', $lastmonthDate)
                ->where('user_id', '=', $valueOutlet->user_id)
                ->first();

            $todayBeat = DB::table('user_sales_order')
                ->select('location_id')
                ->where('date', '=', $date)
                ->where('user_id', '=', $valueOutlet->user_id)
                ->first();

            if (!empty($todayBeat)) {
                $todaySaleBeat = DB::table('sale_view')
                    ->select(DB::raw("SUM(rate*quantity) as todaysale"))
                    ->where('date', '=', $date)
                    ->where('location_id', '=', $todayBeat->location_id)
                    ->where('user_id', '=', $valueOutlet->user_id)
                    ->first();
                $lastSaleBeat = DB::table('sale_view')
                    ->select(DB::raw("SUM(rate*quantity) as todaysale"))
                    ->where('date', '<', $date)
                    ->where('location_id', '=', $todayBeat->location_id)
                    ->where('user_id', '=', $valueOutlet->user_id)
                    ->first();

                $todayRetailerBeat = DB::table('user_sales_order')
                    ->select(DB::raw("count(DISTINCT retailer_id) as retailer"))
                    ->where('date', '=', $date)
                    ->where('location_id', '=', $todayBeat->location_id)
                    ->where('user_id', '=', $valueOutlet->user_id)
                    ->where('call_status', '=', '1')
                    ->first();
                $lastRetailerBeat = DB::table('user_sales_order')
                    ->select(DB::raw("count(DISTINCT retailer_id) as retailer"))
                    ->where('date', '<', $date)
                    ->where('location_id', '=', $todayBeat->location_id)
                    ->where('user_id', '=', $valueOutlet->user_id)
                    ->where('call_status', '=', '1')
                    ->first();

                $todaySaleBeatData = $todaySaleBeat->todaysale;
                $lastSaleBeatData = $lastSaleBeat->todaysale;
                $todayRetailerBeatData = $todayRetailerBeat->retailer;
                $lastRetailerBeatData = $lastRetailerBeat->retailer;
            } else {
                $todaySaleBeatData = 0;
                $lastSaleBeatData = 0;
                $todayRetailerBeatData = 0;
                $lastRetailerBeatData = 0;
            }


            $outletStatus[$iout]['visit'] = $visit->retailer;
            $outletStatus[$iout]['active'] = $visitActive->retailer;
            $outletStatus[$iout]['activeLast'] = $visitActiveLast->retailer;
            $outletStatus[$iout]['perolKg'] = $perolKg->weight;
            $outletStatus[$iout]['perolKgLast'] = $perolKgLast->weight;
            $outletStatus[$iout]['todaySaleBeat'] = $todaySaleBeatData;
            $outletStatus[$iout]['lastSaleBeat'] = $lastSaleBeatData;
            $outletStatus[$iout]['todayRetailerBeat'] = $todayRetailerBeatData;
            $outletStatus[$iout]['lastRetailerBeat'] = $lastRetailerBeatData;

            $iout++;

        }
//  dd($userName);
        return view('reports.daily-item-report.panel1',
            ['manpowerstatus' => $manpowerstatus
                , 'retailing' => $retailing
                , 'rdStatus' => $rdStatus
                , 'workingDays' => $workingDays
                , 'days' => $days
                , 'userName' => $userName
                , 'outletStatus' => $outletStatus

            ]);
    }

// FOR RD SALE
    public function dtrpanel6(Request $request)
    {
        $region = $request->region;
        $user = $request->user;
        $date = $request->date;

        $month = date('Y-m', strtotime($date));
        $startDate = $month . "-01";
        $days = date('d', strtotime($date));
        $start = new DateTime($startDate);
        $sundays = floor($days / 7) + ($start->format('N') + $days % 7 >= 7);
        $totalOff = $sundays + 1;
        $workingDays = $days - $totalOff;
        $lastmonthDate = date('Y-m-d', strtotime('-1 month', strtotime($date)));
        $lastMonth = date('Y-m', strtotime($lastmonthDate));
        $startlastDate = $lastMonth . "-01";
        $_SESSION['juniordata'] = array();

        DB::delete('delete from users_junior_hierarchy where senior_id = ?', [$user]);
        $details = DB::table('person')
            ->where('id', $user)->where('status', '1')
            ->select('id', DB::raw("CONCAT(person.first_name,' ',person.last_name) as uname"),
                'role_id')->first();
        DB::table('users_junior_hierarchy')->insert(
            ['senior_id' => $user,
                'junior_id' => $user,
                'junior_name' => $details->uname,
                'role_id' => $details->role_id
                , 'created_at' => now()
                , 'updated_at' => now()]
        );
        $myobj = new MyConfiguration();
        $myobj->getJuniorPersonWithSenior($user, $user);
        $junior = $_SESSION['juniordata'];
//  print_r($_SESSION['juniordata']); exit;
        $userName = DB::table('person')->where('id', $user)->select(DB::raw("CONCAT(person.first_name,' ',person.last_name) as uname"))->first();
        $manpowerstatus = DB::table('man_power_status')->where(['man_power_status.user_id' => $user])
            ->get();

        $retailing = DB::table('purchase_order')
            ->whereIn('created_person_id', $junior)
            ->whereRaw("DATE_FORMAT(purchase_order.ch_date, '%Y-%m') = '$month'")
            ->select('role_id', DB::raw('count(order_id) as total'))
            ->join('person', 'person.id', '=', 'purchase_order.created_person_id')
            ->groupBy('role_id')
            ->pluck('total', 'role_id');
        //dd($month);
        // RD STATUS //
        $rdStatusQuery = DB::table('users_junior_hierarchy')
            ->select('junior_id as user_id', 'junior_name as uname',
                'target', 'achievement', 'month', '_role.rolename as rolename', 'dealer.name as dealer_name', 'user_purchase_target.dealer_id as dealerId')
            ->leftJoin('user_purchase_target', function ($join) use ($month) {
                $join->on('user_purchase_target.user_id', '=', 'users_junior_hierarchy.junior_id');
                $join->on(DB::raw("DATE_FORMAT(user_purchase_target.month,'%Y-%m')"), '=', DB::raw("'$month'"));

            })
            ->join('_role', '_role.role_id', '=', 'users_junior_hierarchy.role_id', 'INNER')
            ->join('dealer', 'dealer.id', '=', 'user_purchase_target.dealer_id', 'INNER')
            ->where('users_junior_hierarchy.senior_id', $user)
            ->orderBy('users_junior_hierarchy.id')
            ->get();
        $rdStatus = array();
        $ird = 0;
        foreach ($rdStatusQuery as $keyrd => $valuerd) {
            $rdStatus[$ird]['user_id'] = $valuerd->user_id;
            $rdStatus[$ird]['uname'] = $valuerd->uname;
            $rdStatus[$ird]['target'] = $valuerd->target;
            $rdStatus[$ird]['achievement'] = $valuerd->achievement;
            $rdStatus[$ird]['month'] = $valuerd->month;
            $rdStatus[$ird]['rolename'] = $valuerd->rolename;
            $rdStatus[$ird]['dealerId'] = $valuerd->dealerId;
            $rdStatus[$ird]['dealer_name'] = $valuerd->dealer_name;
            $thisKg = DB::table('purchase_view')
                ->select(DB::raw("SUM(quantity*weight) as weight"))
                ->where('ch_date', '>=', $startDate)
                ->where('ch_date', '<=', $date)
                ->where('user_id', '=', $valuerd->user_id)
                ->where('dealer_id', '=', $valuerd->dealerId)
                ->first();
            $lastKg = DB::table('purchase_view')
                ->select(DB::raw("SUM(quantity*weight) as weight"))
                ->where('ch_date', '>=', $startlastDate)
                ->where('ch_date', '<=', $lastmonthDate)
                ->where('user_id', '=', $valuerd->user_id)
                ->first();
            $rdStatus[$ird]['thisKg'] = $thisKg->weight / 1000;
            $rdStatus[$ird]['lastKg'] = $lastKg->weight / 1000;
            $ird++;
        }

// OUTLET STATUS //
        $outletStatusquery = DB::table('users_junior_hierarchy')
            ->select('junior_id as user_id', 'junior_name as uname',
                'target', 'achievement', 'month', '_role.rolename as rolename')
            ->leftJoin('user_retailer_target', function ($joino) use ($month) {
                $joino->on('user_retailer_target.user_id', '=', 'users_junior_hierarchy.junior_id');
                $joino->on(DB::raw("DATE_FORMAT(user_retailer_target.month,'%Y-%m')"), '=', DB::raw("'$month'"));
            })
            ->join('_role', '_role.role_id', '=', 'users_junior_hierarchy.role_id', 'INNER')
            ->where('users_junior_hierarchy.senior_id', $user)
            ->orderBy('users_junior_hierarchy.id')
            ->get();
        $outletStatus = array();
        $iout = 0;
        foreach ($outletStatusquery as $keyOutlet => $valueOutlet) {
            $outletStatus[$iout]['user_id'] = $valueOutlet->user_id;
            $outletStatus[$iout]['uname'] = $valueOutlet->uname;
            $outletStatus[$iout]['target'] = $valueOutlet->target;
            $outletStatus[$iout]['achievement'] = $valueOutlet->achievement;
            $outletStatus[$iout]['rolename'] = $valueOutlet->rolename;
            $visit = DB::table('user_sales_order')
                ->select(DB::raw("count(DISTINCT retailer_id) as retailer"))
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $date)
                ->where('user_id', '=', $valueOutlet->user_id)
                ->first();
            $visitActive = DB::table('user_sales_order')
                ->select(DB::raw("count(DISTINCT retailer_id) as retailer"))
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $date)
                ->where('user_id', '=', $valueOutlet->user_id)
                ->where('call_status', '=', '1')
                ->first();
            $visitActiveLast = DB::table('user_sales_order')
                ->select(DB::raw("count(DISTINCT retailer_id) as retailer"))
                ->where('date', '>=', $startlastDate)
                ->where('date', '<=', $lastmonthDate)
                ->where('user_id', '=', $valueOutlet->user_id)
                ->where('call_status', '=', '1')
                ->first();
            $perolKg = DB::table('sale_view')
                ->select(DB::raw("SUM(quantity*weight) as weight"))
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $date)
                ->where('user_id', '=', $valueOutlet->user_id)
                ->first();
            $perolKgLast = DB::table('sale_view')
                ->select(DB::raw("SUM(quantity*weight) as weight"))
                ->where('date', '>=', $startlastDate)
                ->where('date', '<=', $lastmonthDate)
                ->where('user_id', '=', $valueOutlet->user_id)
                ->first();

            $todayBeat = DB::table('user_sales_order')
                ->select('location_id')
                ->where('date', '=', $date)
                ->where('user_id', '=', $valueOutlet->user_id)
                ->first();

            if (!empty($todayBeat)) {
                $todaySaleBeat = DB::table('sale_view')
                    ->select(DB::raw("SUM(rate*quantity) as todaysale"))
                    ->where('date', '=', $date)
                    ->where('location_id', '=', $todayBeat->location_id)
                    ->where('user_id', '=', $valueOutlet->user_id)
                    ->first();
                $lastSaleBeat = DB::table('sale_view')
                    ->select(DB::raw("SUM(rate*quantity) as todaysale"))
                    ->where('date', '<', $date)
                    ->where('location_id', '=', $todayBeat->location_id)
                    ->where('user_id', '=', $valueOutlet->user_id)
                    ->first();

                $todayRetailerBeat = DB::table('user_sales_order')
                    ->select(DB::raw("count(DISTINCT retailer_id) as retailer"))
                    ->where('date', '=', $date)
                    ->where('location_id', '=', $todayBeat->location_id)
                    ->where('user_id', '=', $valueOutlet->user_id)
                    ->where('call_status', '=', '1')
                    ->first();
                $lastRetailerBeat = DB::table('user_sales_order')
                    ->select(DB::raw("count(DISTINCT retailer_id) as retailer"))
                    ->where('date', '<', $date)
                    ->where('location_id', '=', $todayBeat->location_id)
                    ->where('user_id', '=', $valueOutlet->user_id)
                    ->where('call_status', '=', '1')
                    ->first();

                $todaySaleBeatData = $todaySaleBeat->todaysale;
                $lastSaleBeatData = $lastSaleBeat->todaysale;
                $todayRetailerBeatData = $todayRetailerBeat->retailer;
                $lastRetailerBeatData = $lastRetailerBeat->retailer;
            } else {
                $todaySaleBeatData = 0;
                $lastSaleBeatData = 0;
                $todayRetailerBeatData = 0;
                $lastRetailerBeatData = 0;
            }


            $outletStatus[$iout]['visit'] = $visit->retailer;
            $outletStatus[$iout]['active'] = $visitActive->retailer;
            $outletStatus[$iout]['activeLast'] = $visitActiveLast->retailer;
            $outletStatus[$iout]['perolKg'] = $perolKg->weight;
            $outletStatus[$iout]['perolKgLast'] = $perolKgLast->weight;
            $outletStatus[$iout]['todaySaleBeat'] = $todaySaleBeatData;
            $outletStatus[$iout]['lastSaleBeat'] = $lastSaleBeatData;
            $outletStatus[$iout]['todayRetailerBeat'] = $todayRetailerBeatData;
            $outletStatus[$iout]['lastRetailerBeat'] = $lastRetailerBeatData;

            $iout++;

        }
        return view('reports.daily-item-report.panel6',
            ['manpowerstatus' => $manpowerstatus
                , 'retailing' => $retailing
                , 'rdStatus' => $rdStatus
                , 'workingDays' => $workingDays
                , 'days' => $days
                , 'userName' => $userName
                , 'outletStatus' => $outletStatus

            ]);
    }

    public function dtrpanel2(Request $request)
    {
        $region = $request->region;
        $user = $request->user;
        $date = $request->date;

        $month = date('Y-m', strtotime($date));
        $startDate = $month . "-01";
        $days = date('d', strtotime($date));
        $start = new DateTime($startDate);
        $sundays = floor($days / 7) + ($start->format('N') + $days % 7 >= 7);
        $totalOff = $sundays + 1;
        $workingDays = $days - $totalOff;
        // 1 MONTH AGO
        $Date30 = date('Y-m-d', strtotime('-30 Day', strtotime($date)));
        // 45 DAYS AGO
        $Date45 = date('Y-m-d', strtotime('-45 Day', strtotime($date)));
        // 60 DAYS AGO
        $Date60 = date('Y-m-d', strtotime('-60 Day', strtotime($date)));
        // 90 DAYS AGO
        $Date90 = date('Y-m-d', strtotime('-90 Day', strtotime($date)));
// exit;
        $_SESSION['juniordata'] = array();

        DB::delete('delete from users_junior_hierarchy where senior_id = ?', [$user]);
        $details = DB::table('person')
            ->where('id', $user)->where('status', '1')
            ->select('id', DB::raw("CONCAT(person.first_name,' ',person.last_name) as uname"),
                'role_id')->first();
        DB::table('users_junior_hierarchy')->insert(
            ['senior_id' => $user,
                'junior_id' => $user,
                'junior_name' => $details->uname,
                'role_id' => $details->role_id
                , 'created_at' => now()
                , 'updated_at' => now()]
        );
        $myobj = new MyConfiguration();
        $myobj->getJuniorPersonWithSenior($user, $user);
        $junior = $_SESSION['juniordata'];
        //  print_r($_SESSION['juniordata']); exit;
        $userName = DB::table('person')->where('id', $user)->select(DB::raw("CONCAT(person.first_name,' ',person.last_name) as uname"))->first();
        // DATA

        $paymentUser = DB::table('users_junior_hierarchy')
            ->select('junior_id as user_id', 'junior_name as uname', '_role.rolename as rolename')
            ->join('_role', '_role.role_id', '=', 'users_junior_hierarchy.role_id', 'INNER')
            ->where('users_junior_hierarchy.senior_id', $user)
            ->orderBy('users_junior_hierarchy.id')
            ->get();
        $payment30 = array();
        $payment45 = array();
        $payment60 = array();
        $payment90 = array();
        $ip = 0;
        $ip30 = 0;
        $ip45 = 0;
        $ip60 = 0;
        foreach ($paymentUser as $keyPay => $valuePay) {
            $ip30++;
            // FOR 30 DAYS //
            $payment30[$ip30]['user_id'] = $valuePay->user_id;
            $payment30[$ip30]['uname'] = $valuePay->uname;
            $payment30[$ip30]['rolename'] = $valuePay->rolename;
            $payrs30User = DB::table('challan_order')->select(DB::raw("SUM(remaining) as remain"))
                ->join('dealer', 'dealer.id', '=', 'challan_order.ch_dealer_id')
                ->where(DB::raw("DATE_FORMAT(ch_date,'%Y-%m-%d')"), '<', DB::raw("'$Date30'"))
                ->where(DB::raw("DATE_FORMAT(ch_date,'%Y-%m-%d')"), '>=', DB::raw("'$Date45'"))
                ->where('ch_user_id', $valuePay->user_id)->first();
            $payment30[$ip30]['remain'] = $payrs30User->remain;
            $payment30[$ip30]['color'] = "yellow";

            // ch_user_id

            $payrs30 = DB::table('challan_order')->select(DB::raw("SUM(remaining) as remain"), 'ch_dealer_id', 'dealer.name as uname')
                ->join('dealer', 'dealer.id', '=', 'challan_order.ch_dealer_id')
                ->where(DB::raw("DATE_FORMAT(ch_date,'%Y-%m-%d')"), '<', DB::raw("'$Date30'"))
                ->where(DB::raw("DATE_FORMAT(ch_date,'%Y-%m-%d')"), '>=', DB::raw("'$Date45'"))
                ->where('ch_user_id', $valuePay->user_id)->groupBy('ch_dealer_id')->get();
            if (!empty($payrs30)) {
                foreach ($payrs30 as $key30 => $value30) {
                    $ip30++;
                    $payment30[$ip30]['user_id'] = $value30->ch_dealer_id;
                    $payment30[$ip30]['uname'] = $value30->uname;
                    $payment30[$ip30]['rolename'] = "Distributor";
                    $payment30[$ip30]['remain'] = $value30->remain;
                    $payment30[$ip30]['color'] = "#fff";
                }
            } else {
                $ip30++;
            }


// END OF 30 DAYS //
// START OF 45 DAYS //
            $ip45++;
            $payment45[$ip45]['user_id'] = $valuePay->user_id;
            $payment45[$ip45]['uname'] = $valuePay->uname;
            $payment45[$ip45]['rolename'] = $valuePay->rolename;

            $payrs451 = DB::table('challan_order')->select(DB::raw("SUM(remaining) as remain"))
                ->where(DB::raw("DATE_FORMAT(ch_date,'%Y-%m-%d')"), '<', DB::raw("'$Date45'"))
                ->where(DB::raw("DATE_FORMAT(ch_date,'%Y-%m-%d')"), '>=', DB::raw("'$Date60'"))
                ->where('ch_user_id', $valuePay->user_id)->first();
            $payment45[$ip45]['remain'] = $payrs451->remain;

            $payment45[$ip45]['color'] = "yellow";

            // ch_user_id

            $payrs45 = DB::table('challan_order')->select(DB::raw("SUM(remaining) as remain"), 'ch_dealer_id', 'dealer.name as uname')
                ->join('dealer', 'dealer.id', '=', 'challan_order.ch_dealer_id')
                ->where(DB::raw("DATE_FORMAT(ch_date,'%Y-%m-%d')"), '<', DB::raw("'$Date45'"))
                ->where(DB::raw("DATE_FORMAT(ch_date,'%Y-%m-%d')"), '>=', DB::raw("'$Date60'"))
                ->where('ch_user_id', $valuePay->user_id)->groupBy('ch_dealer_id')->get();
            if (!empty($payrs45)) {
                foreach ($payrs45 as $key45 => $value45) {
                    $ip45++;
                    $payment45[$ip45]['user_id'] = $value45->ch_dealer_id;
                    $payment45[$ip45]['uname'] = $value45->uname;
                    $payment45[$ip45]['rolename'] = "Distributor";
                    $payment45[$ip45]['remain'] = $value45->remain;
                    $payment45[$ip45]['color'] = "#fff";
                }
            } else {
                $ip45++;
            }


// END OF 45 DAYS //
// START OF 60 DAYS //
            $ip60++;
            $payment60[$ip60]['user_id'] = $valuePay->user_id;
            $payment60[$ip60]['uname'] = $valuePay->uname;
            $payment60[$ip60]['rolename'] = $valuePay->rolename;

            $payrs601 = DB::table('challan_order')->select(DB::raw("SUM(remaining) as remain"))
                ->where(DB::raw("DATE_FORMAT(ch_date,'%Y-%m-%d')"), '<', DB::raw("'$Date60'"))
                ->where(DB::raw("DATE_FORMAT(ch_date,'%Y-%m-%d')"), '>=', DB::raw("'$Date90'"))
                ->where('ch_user_id', $valuePay->user_id)->first();
            $payment60[$ip60]['remain'] = $payrs601->remain;

            $payment60[$ip60]['color'] = "yellow";

            // ch_user_id

            $payrs60 = DB::table('challan_order')->select(DB::raw("SUM(remaining) as remain"), 'ch_dealer_id', 'dealer.name as uname')
                ->join('dealer', 'dealer.id', '=', 'challan_order.ch_dealer_id')
                ->where(DB::raw("DATE_FORMAT(ch_date,'%Y-%m-%d')"), '<', DB::raw("'$Date60'"))
                ->where(DB::raw("DATE_FORMAT(ch_date,'%Y-%m-%d')"), '>=', DB::raw("'$Date90'"))
                ->where('ch_user_id', $valuePay->user_id)->groupBy('ch_dealer_id')->get();
            if (!empty($payrs60)) {
                foreach ($payrs60 as $key60 => $value60) {
                    $ip60++;
                    $payment60[$ip60]['user_id'] = $value60->ch_dealer_id;
                    $payment60[$ip60]['uname'] = $value60->uname;
                    $payment60[$ip60]['rolename'] = "Distributor";
                    $payment60[$ip60]['remain'] = $value60->remain;
                    $payment60[$ip60]['color'] = "#fff";
                }
            } else {
                $ip60++;
            }
// END OF 60 DAYS //

// START OF 90 DAYs //
            $ip++;
            $payment90[$ip]['user_id'] = $valuePay->user_id;
            $payment90[$ip]['uname'] = $valuePay->uname;
            $payment90[$ip]['rolename'] = $valuePay->rolename;

            $payrs901 = DB::table('challan_order')->select(DB::raw("SUM(remaining) as remain"))
                ->where(DB::raw("DATE_FORMAT(ch_date,'%Y-%m-%d')"), '<', DB::raw("'$Date90'"))
                //  ->where(DB::raw("DATE_FORMAT(ch_date,'%Y-%m-%d')"), '>=',DB::raw("'$Date45'"))
                ->where('ch_user_id', $valuePay->user_id)->first();
            $payment90[$ip]['remain'] = $payrs901->remain;

            $payment90[$ip]['color'] = "yellow";

            // ch_user_id

            $payrs90 = DB::table('challan_order')->select(DB::raw("SUM(remaining) as remain"), 'ch_dealer_id', 'dealer.name as uname')
                ->join('dealer', 'dealer.id', '=', 'challan_order.ch_dealer_id')
                ->where(DB::raw("DATE_FORMAT(ch_date,'%Y-%m-%d')"), '<', DB::raw("'$Date60'"))
                ->where(DB::raw("DATE_FORMAT(ch_date,'%Y-%m-%d')"), '>=', DB::raw("'$Date90'"))
                ->where('ch_user_id', $valuePay->user_id)->groupBy('ch_dealer_id')->get();
            if (!empty($payrs90)) {
                foreach ($payrs90 as $key90 => $value90) {
                    $ip++;
                    $payment90[$ip]['user_id'] = $value90->ch_dealer_id;
                    $payment90[$ip]['uname'] = $value90->uname;
                    $payment90[$ip]['rolename'] = "Distributor";
                    $payment90[$ip]['remain'] = $value90->remain;
                    $payment90[$ip]['color'] = "#fff";
                }
            } else {
                $ip++;
            }


            // $ip++;
        }

        return view('reports.daily-item-report.panel2',
            [
                'workingDays' => $workingDays
                , 'days' => $days
                , 'userName' => $userName
                , 'payment30' => $payment30
                , 'payment45' => $payment45
                , 'payment60' => $payment60
                , 'payment90' => $payment90

            ]);

    }

    public function dtrpanel3(Request $request)
    {
        $region = $request->region;
        $user = $request->user;
        $date = $request->date;

        $month = date('Y-m', strtotime($date));
        $startDate = $month . "-01";
        $days = date('d', strtotime($date));
        $start = new DateTime($startDate);
        $sundays = floor($days / 7) + ($start->format('N') + $days % 7 >= 7);
        $totalOff = $sundays + 1;
        $workingDays = $days - $totalOff;
        $lastmonthDate = date('Y-m-d', strtotime('-1 month', strtotime($date)));
        $lastMonth = date('Y-m', strtotime($lastmonthDate));
        $startlastDate = $lastMonth . "-01";
        $_SESSION['juniordata'] = array();

        DB::delete('delete from users_junior_hierarchy where senior_id = ?', [$user]);
        $details = DB::table('person')
            ->where('id', $user)->where('status', '1')
            ->select('id', DB::raw("CONCAT(person.first_name,' ',person.last_name) as uname"),
                'role_id')->first();
        DB::table('users_junior_hierarchy')->insert(
            ['senior_id' => $user,
                'junior_id' => $user,
                'junior_name' => $details->uname,
                'role_id' => $details->role_id
                , 'created_at' => now()
                , 'updated_at' => now()]
        );
        $myobj = new MyConfiguration();
        $myobj->getJuniorPersonWithSenior($user, $user);
        $junior = $_SESSION['juniordata'];
        //  print_r($_SESSION['juniordata']); exit;
        $userName = DB::table('person')->where('id', $user)->select(DB::raw("CONCAT(person.first_name,' ',person.last_name) as uname"))->first();
        $pwRdUser = DB::table('users_junior_hierarchy')
            ->select('junior_id as user_id', 'junior_name as uname', '_role.rolename as rolename')
            ->join('_role', '_role.role_id', '=', 'users_junior_hierarchy.role_id', 'INNER')
            ->where('users_junior_hierarchy.senior_id', $user)
            ->orderBy('users_junior_hierarchy.id')
            ->get();
        $ip = 0;
        $catSale = array();
        foreach ($pwRdUser as $keypwRd => $valuepwRd) {
            $catSale[$ip]['user_id'] = $valuepwRd->user_id;
            $catSale[$ip]['uname'] = $valuepwRd->uname;
            $catSale[$ip]['rolename'] = $valuepwRd->rolename;
            // ch_user_id
            $cate1 = DB::table('sale_view')->select(DB::raw("SUM(rate*quantity) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->where('catalog_view.c1_id', '120150507011140')
                ->first();

            $catSale[$ip]['sale1'] = $cate1->sale;

            // ch_user_id
            $cate2 = DB::table('sale_view')->select(DB::raw("SUM(rate*quantity) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->where('catalog_view.c1_id', '120150507011152')
                ->first();

            $catSale[$ip]['sale2'] = $cate2->sale;

            // ch_user_id
            $cate3 = DB::table('sale_view')->select(DB::raw("SUM(rate*quantity) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->where('catalog_view.c1_id', '120150507011211')
                ->first();

            $catSale[$ip]['sale3'] = $cate3->sale;
            // ch_user_id
            $cate4 = DB::table('sale_view')->select(DB::raw("SUM(rate*quantity) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->where('catalog_view.c1_id', '120150507011222')
                ->first();

            $catSale[$ip]['sale4'] = $cate4->sale;

            $cate5 = DB::table('sale_view')->select(DB::raw("SUM(rate*quantity) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->where('catalog_view.c1_id', '120150507011301')
                ->first();

            $catSale[$ip]['sale5'] = $cate5->sale;

            $cate6 = DB::table('sale_view')->select(DB::raw("SUM(rate*quantity) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->where('catalog_view.c1_id', '120150622100550')
                ->first();

            $catSale[$ip]['sale6'] = $cate6->sale;

            $cate7 = DB::table('sale_view')->select(DB::raw("SUM(rate*quantity) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->where('catalog_view.c1_id', '120170323095853')
                ->first();

            $catSale[$ip]['sale7'] = $cate7->sale;

            $cate8 = DB::table('sale_view')->select(DB::raw("SUM(rate*quantity) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->where('catalog_view.c1_id', '120171219112227')
                ->first();

            $catSale[$ip]['sale8'] = $cate8->sale;

            $cate9 = DB::table('sale_view')->select(DB::raw("SUM(rate*quantity) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->where('catalog_view.c1_id', '120171219112228')
                ->first();

            $catSale[$ip]['sale9'] = $cate9->sale;

            $ip++;
        }
        return view('reports.daily-item-report.panel3',
            [
                'workingDays' => $workingDays
                , 'days' => $days
                , 'userName' => $userName
                , 'catSale' => $catSale
            ]);
    }

    public function dtrpanel4(Request $request)
    {
        $region = $request->region;
        $user = $request->user;
        $date = $request->date;

        $month = date('Y-m', strtotime($date));
        $startDate = $month . "-01";
        $days = date('d', strtotime($date));
        $start = new DateTime($startDate);
        $sundays = floor($days / 7) + ($start->format('N') + $days % 7 >= 7);
        $totalOff = $sundays + 1;
        $workingDays = $days - $totalOff;
        $lastmonthDate = date('Y-m-d', strtotime('-1 month', strtotime($date)));
        $lastMonth = date('Y-m', strtotime($lastmonthDate));
        $startlastDate = $lastMonth . "-01";
        $_SESSION['juniordata'] = array();

        DB::delete('delete from users_junior_hierarchy where senior_id = ?', [$user]);
        $details = DB::table('person')
            ->where('id', $user)->where('status', '1')
            ->select('id', DB::raw("CONCAT(person.first_name,' ',person.last_name) as uname"),
                'role_id')->first();
        DB::table('users_junior_hierarchy')->insert(
            ['senior_id' => $user,
                'junior_id' => $user,
                'junior_name' => $details->uname,
                'role_id' => $details->role_id
                , 'created_at' => now()
                , 'updated_at' => now()]
        );
        $myobj = new MyConfiguration();
        $myobj->getJuniorPersonWithSenior($user, $user);
        $junior = $_SESSION['juniordata'];
        //  print_r($_SESSION['juniordata']); exit;
        $userName = DB::table('person')->where('id', $user)->select(DB::raw("CONCAT(person.first_name,' ',person.last_name) as uname"))->first();
        $pwRdUser = DB::table('users_junior_hierarchy')
            ->select('junior_id as user_id', 'junior_name as uname', '_role.rolename as rolename')
            ->join('_role', '_role.role_id', '=', 'users_junior_hierarchy.role_id', 'INNER')
            ->where('users_junior_hierarchy.senior_id', $user)
            ->orderBy('users_junior_hierarchy.id')
            ->get();
        $ip = 0;
        $catSale = array();
        foreach ($pwRdUser as $keypwRd => $valuepwRd) {
            $catSale[$ip]['user_id'] = $valuepwRd->user_id;
            $catSale[$ip]['uname'] = $valuepwRd->uname;
            $catSale[$ip]['rolename'] = $valuepwRd->rolename;
            // ch_user_id

            $catet = DB::table('sale_view')->select(DB::raw("count(DISTINCT order_id) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->first();

            $catSale[$ip]['salet'] = $catet->sale;

            $cate1 = DB::table('sale_view')->select(DB::raw("count(DISTINCT order_id) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->where('catalog_view.c1_id', '120150507011140')
                ->first();

            $catSale[$ip]['sale1'] = $cate1->sale;

            // ch_user_id
            $cate2 = DB::table('sale_view')->select(DB::raw("count(DISTINCT order_id) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->where('catalog_view.c1_id', '120150507011152')
                ->first();

            $catSale[$ip]['sale2'] = $cate2->sale;

            // ch_user_id
            $cate3 = DB::table('sale_view')->select(DB::raw("count(DISTINCT order_id) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->where('catalog_view.c1_id', '120150507011211')
                ->first();

            $catSale[$ip]['sale3'] = $cate3->sale;
            // ch_user_id
            $cate4 = DB::table('sale_view')->select(DB::raw("count(DISTINCT order_id) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->where('catalog_view.c1_id', '120150507011222')
                ->first();

            $catSale[$ip]['sale4'] = $cate4->sale;

            $cate5 = DB::table('sale_view')->select(DB::raw("count(DISTINCT order_id) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->where('catalog_view.c1_id', '120150507011301')
                ->first();

            $catSale[$ip]['sale5'] = $cate5->sale;

            $cate6 = DB::table('sale_view')->select(DB::raw("count(DISTINCT order_id) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->where('catalog_view.c1_id', '120150622100550')
                ->first();

            $catSale[$ip]['sale6'] = $cate6->sale;

            $cate7 = DB::table('sale_view')->select(DB::raw("count(DISTINCT order_id) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->where('catalog_view.c1_id', '120170323095853')
                ->first();

            $catSale[$ip]['sale7'] = $cate7->sale;

            $cate8 = DB::table('sale_view')->select(DB::raw("count(DISTINCT order_id) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->where('catalog_view.c1_id', '120171219112227')
                ->first();

            $catSale[$ip]['sale8'] = $cate8->sale;

            $cate9 = DB::table('sale_view')->select(DB::raw("count(DISTINCT order_id) as sale"))
                ->join('catalog_view', 'catalog_view.product_id', '=', 'sale_view.product_id', 'INNER')
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '>=', DB::raw("'$startDate'"))
                ->where(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"), '<=', DB::raw("'$date'"))
                ->where('user_id', $valuepwRd->user_id)
                ->where('catalog_view.c1_id', '120171219112228')
                ->first();

            $catSale[$ip]['sale9'] = $cate9->sale;

            $ip++;
        }
        return view('reports.daily-item-report.panel4',
            [
                'workingDays' => $workingDays
                , 'days' => $days
                , 'userName' => $userName
                , 'catSale' => $catSale
            ]);
    }

    public function dtrpanel5(Request $request)
    {
        $region = $request->region;
        $user = $request->user;
        $date = $request->date;

        $month = date('Y-m', strtotime($date));
        $startDate = $month . "-01";
        $days = date('d', strtotime($date));
        $start = new DateTime($startDate);
        $sundays = floor($days / 7) + ($start->format('N') + $days % 7 >= 7);
        $totalOff = $sundays + 1;
        $workingDays = $days - $totalOff;
        $lastmonthDate = date('Y-m-d', strtotime('-1 month', strtotime($date)));
        $lastMonth = date('Y-m', strtotime($lastmonthDate));
        $startlastDate = $lastMonth . "-01";
        $_SESSION['juniordata'] = array();

        DB::delete('delete from users_junior_hierarchy where senior_id = ?', [$user]);
        $details = DB::table('person')
            ->where('id', $user)->where('status', '1')
            ->select('id', DB::raw("CONCAT(person.first_name,' ',person.last_name) as uname"),
                'role_id')->first();
        DB::table('users_junior_hierarchy')->insert(
            ['senior_id' => $user,
                'junior_id' => $user,
                'junior_name' => $details->uname,
                'role_id' => $details->role_id
                , 'created_at' => now()
                , 'updated_at' => now()]
        );
        $myobj = new MyConfiguration();
        $myobj->getJuniorPersonWithSenior($user, $user);
        $junior = $_SESSION['juniordata'];
        //  print_r($_SESSION['juniordata']); exit;
        $userName = DB::table('person')->where('id', $user)->select(DB::raw("CONCAT(person.first_name,' ',person.last_name) as uname"))->first();
        // Data //
        $outLets = DB::table('users_junior_hierarchy')
            ->select('junior_id as user_id', 'junior_name as uname',
                'target', 'achievement', 'month', '_role.rolename as rolename')
            ->leftJoin('user_retailer_create_target', function ($joino) use ($month) {
                $joino->on('user_retailer_create_target.user_id', '=', 'users_junior_hierarchy.junior_id');
                $joino->on(DB::raw("DATE_FORMAT(user_retailer_create_target.month,'%Y-%m')"), '=', DB::raw("'$month'"));
            })
            ->join('_role', '_role.role_id', '=', 'users_junior_hierarchy.role_id', 'INNER')
            ->where('users_junior_hierarchy.senior_id', $user)
            ->orderBy('users_junior_hierarchy.id')
            ->get();
        $outLet = array();
        $i = 0;
        foreach ($outLets as $key => $value) {
            $outLet[$i]['user_id'] = $value->user_id;
            $outLet[$i]['uname'] = $value->uname;
            $outLet[$i]['target'] = $value->target;
            $outLet[$i]['rolename'] = $value->rolename;
            $newOutlet = DB::table('retailer')
                ->select(DB::raw("count(DISTINCT retailer.id) as retailerCount"))
                ->where(DB::raw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')"), '>=', $startDate)
                ->where(DB::raw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')"), '<=', $date)
                ->where('created_by_person_id', '=', $value->user_id)
                ->first();

            $retailerActive = DB::table('retailer')
                ->select(DB::raw("count(DISTINCT retailer.id) as retailer"))
                ->where(DB::raw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')"), '=', $startDate)
                ->where('created_by_person_id', '=', $value->user_id)
                ->first();

            $retailerActiveLast = DB::table('retailer')
                ->select(DB::raw("count(DISTINCT retailer.id) as retailer"))
                ->where(DB::raw("DATE_FORMAT(retailer.created_on,'%Y-%m-%d')"), '=', $lastmonthDate)
                ->where('created_by_person_id', '=', $value->user_id)
                ->first();
            $outLet[$i]['newOutlet'] = $newOutlet->retailerCount;
            $outLet[$i]['retailerActive'] = $retailerActive->retailer;
            $outLet[$i]['retailerActiveLast'] = $retailerActiveLast->retailer;

            $i++;
        }
//  dd($userName);
        return view('reports.daily-item-report.panel5',
            ['workingDays' => $workingDays
                , 'days' => $days
                , 'userName' => $userName
                , 'outLet' => $outLet

            ]);


    }

//Ganesh Add function for
    public function distributerStock(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax()) {

            $explodeDate = explode(" -", $request->date_range_picker);
            $region = $request->region;
            $state = $request->area;
            $town = $request->territory;
            $distributor = $request->distributor;
            $beat = $request->belt;
            $role = $request->role;
            $user = $request->user;
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

            $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50)
            {
            $datasenior='';
            $login_user=Auth::user()->id;
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                
                $datasenior_call=self::getJuniorUser($login_user);
                Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }

            $query_data = DB::table('dealer_balance_stock')
                ->select('l3_name','l4_name','l5_name','l6_name','rolename','person.mobile','person.person_id_senior','person.emp_code','pcs_mrp', DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'person.id as uid', 'dealer.name as dealer_name','dealer.id as did', 'dealer_balance_stock.*', 'catalog_product.name as product_name')
                ->join('person', 'person.id', '=', 'dealer_balance_stock.user_id')
                ->join('_role', '_role.role_id', '=', 'person.role_id')
                ->join('catalog_product', 'catalog_product.id', '=', 'dealer_balance_stock.product_id')
                ->join('dealer', 'dealer.id', '=', 'dealer_balance_stock.dealer_id')
                ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
                ->groupBy('dealer_id','user_id','product_id')
                ->where('dealer_balance_stock.company_id',$company_id);

            $tmp = array();
            // $dealer_beat = DB::table('location_view')->where('l7_company_id',$company_id);

            // if (!empty($beat)) {
            //     $dealer_beat->whereIn('l7_id', $beat)->pluck('l7_id');
            // } //State Data
            // elseif (!empty($town)) {

            //     $dealer_beat->whereIn('l6_id', $town)->pluck('l7_id');
            // } //Town Data
            // elseif (!empty($state)) {

            //     $dealer_beat->whereIn('l3_id', $state)->pluck('l7_id');

            // } //Beat Data
             if (!empty($datasenior)) 
            {
                $query_data->whereIn('person.id', $datasenior);
            }
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $query_data->whereIn('l3_id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $query_data->whereIn('l4_id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $query_data->whereIn('l5_id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $query_data->whereIn('l6_id', $location_6);
            }
            if (!empty($request->dealer)) 
            {
                $dealer = $request->dealer;
                $query_data->whereIn('dealer_id', $dealer);
            }
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $query_data->whereIn('person.id', $user);
            }
            if (!empty($request->role)) 
            {
                $role = $request->role;
                $query_data->whereIn('person.role_id', $role);
            }
            // elseif (!empty($region)) {
            //     $tr = [];
            //     $tr = $dealer_beat->whereIn('l1_id', $region)->pluck('l7_id');

            // }


            // if (!empty($request->distributor)) {

            //     $tr = [];
            //     $tr = $request->distributor;
            // }

            // if (!empty($tr)) {
            //     $query_data->whereIn('dealer_balance_stock.dealer_id', $tr);
            // }

// To find No of user under certain role
            // $flag = [];
            // if (!empty($request->role)) {
            //     $roleArr = $request->role;
            //     if (!empty($roleArr)) {
            //         $flag = DB::table('person')->where('person.company_id',$company_id)->whereIn('role_id', $roleArr)->pluck('id');
            //     }
            // }

            // if (!empty($user)) {

            //     $flag = [];
            //     $flag = $request->user;

            // }

            // if (!empty($flag)) {
            //     $query_data->whereIn('dealer_balance_stock.user_id', $flag);
            // }

            if (!empty($from_date)) {
              
                $query_data->whereRaw("DATE_FORMAT(dealer_balance_stock.submit_date_time, '%Y-%m-%d') >= '$from_date'");

                $query_data->whereRaw("DATE_FORMAT(dealer_balance_stock.submit_date_time, '%Y-%m-%d') <= '$to_date'");
            }

            $query = $query_data->get(); 
            // dd($query);
            return view('reports.distributer-stock-report.panel1', [
                'records' => $query,
                'company_id' => $company_id,
            ]);
        } else {
            echo '<p class="alert-danger">Data Not Found</p>';
        }
    }

    public function dealerPrimarySale(Request $request)
    {
         $company_id = Auth::user()->company_id;
        if ($request->ajax()) {
            $explodeDate = explode(" -", $request->date_range_picker);
            $user_id = $request->user;
            $region = $request->region;
            $state = $request->area;
            $town = $request->territory;
            $distributor = $request->distributor;
            $beat = $request->belt;
            $role = $request->role;
            $user = $request->user;
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

            $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50)
            {
            $datasenior='';
            $login_user=Auth::user()->id;
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                
                $datasenior_call=self::getJuniorUser($login_user);
                Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }

            $sale_amount = DB::table('user_primary_sales_order')
                           ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id') 
                           ->whereRaw("DATE_FORMAT(user_primary_sales_order.created_date, '%Y-%m-%d')>='$from_date' and DATE_FORMAT(user_primary_sales_order.created_date,'%Y-%m-%d') <='$to_date'")
                           ->where('user_primary_sales_order.company_id',$company_id)
                           ->groupBy('user_primary_sales_order.order_id')
                           ->pluck(DB::raw("ROUND(SUM(final_secondary_qty*final_secondary_rate),2) as amount"),'user_primary_sales_order.order_id');


            $query_data = DB::table('user_primary_sales_order')
                ->leftJoin('person', 'person.id', '=', 'user_primary_sales_order.created_person_id')
                ->join('dealer', 'dealer.id', '=', 'user_primary_sales_order.dealer_id')
                ->join('csa','csa.c_id','=','dealer.csa_id')
                ->join('dealer_location_rate_list', 'dealer_location_rate_list.dealer_id', '=', 'dealer.id')
                ->join('location_view', 'dealer_location_rate_list.location_id', '=', 'location_view.l7_id')
                ->select('l3_id','csa_name','csa_code','dealer_code','location_view.l4_name', DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'person.id as uid', 'dealer.name as dealer_name','dealer.id as did', 'user_primary_sales_order.*')
                ->where('user_primary_sales_order.company_id',$company_id);


            $tmp = array();
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $query_data->whereIn('l3_id', $location_3);
            }
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $query_data->whereIn('l4_id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $query_data->whereIn('l5_id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $query_data->whereIn('l6_id', $location_6);
            }
            if (!empty($request->dealer)) 
            {
                $dealer = $request->dealer;
                $query_data->whereIn('dealer_id', $dealer);
            }
            if (!empty($request->user)) 
            {
                $user = $request->user;
                $query_data->whereIn('person.id', $user);
            }
             if (!empty($datasenior)) 
            {
                $query_data->whereIn('person.id', $datasenior);
            }
            // if (!empty($request->role)) 
            // {
            //     $role = $request->role;
            //     $query_data->whereIn('person.role_id', $role);
            // }


            // To find No of user under certain role 
            $flag = [];
            if (!empty($request->role)) {
                $roleArr = $request->role;
                if (!empty($roleArr)) {
                    $flag = DB::table('person')->whereIn('role_id', $roleArr)->pluck('id');
                }
            }

            if (!empty($user)) {
                $flag = [];
                $flag = $request->user;

            }

            if (!empty($flag)) {
                $query_data->whereIn('user_primary_sales_order.created_person_id', $flag);
            }

            if (!empty($from_date)) {

                $query_data->whereRaw("DATE_FORMAT(user_primary_sales_order.created_date, '%Y-%m-%d')>='$from_date' and DATE_FORMAT(user_primary_sales_order.created_date,'%Y-%m-%d') <='$to_date'");
            }

            $mid_query = $query_data
//                ->select('location_view.l4_name','user_name','dealer_name','id','order_id','dealer_id','created_date','created_person_id','sale_date','receive_date','date_time','company_id','ch_date','challan_no','csa_id','action','is_claim','sync_status')
                ->groupBy('user_primary_sales_order.order_id')->orderBy('sale_date');
            $d = $mid_query->get();
            $idArr = $mid_query->pluck('order_id')->toArray();
            $orderArr = !empty($idArr) ? array_unique($idArr) : [];

            if($company_id == 50){
                $order_details = DB::table('user_primary_sales_order_details')
                                ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                                ->select('catalog_product.name','catalog_product.weight','user_primary_sales_order_details.rate','user_primary_sales_order_details.final_secondary_qty as cases','user_primary_sales_order_details.final_secondary_qty as cases','user_primary_sales_order_details.pcs as pcs','user_primary_sales_order_details.final_secondary_rate as pr_rate','user_primary_sales_order_details.order_id')
                                ->where('user_primary_sales_order_details.company_id',$company_id)
                                ->whereIn('order_id', $orderArr)
                                ->get();

                $orderDetailArr = [];
                foreach ($order_details as $od) {
                    $orderDetailArr[$od->order_id][] = $od;
                    // $orderDetailArr[$od->order_id]['weight'] = $od->weight;
                    // $orderDetailArr[$od->order_id]['rate'] = $od->rate;
                    // $orderDetailArr[$od->order_id]['quantity'] = $od->quantity;
                    // $orderDetailArr[$od->order_id]['cases'] = $od->cases;
                    // $orderDetailArr[$od->order_id]['pcs'] = $od->pcs;
                    // $orderDetailArr[$od->order_id]['pr_rate'] = $od->pr_rate;
                    // $orderDetailArr[$od->order_id]['order_id'] = $od->order_id;
                }
    //            dd($orderDetailArr);
                return view('reports.distributer-stock-report.panelJanak', [
                    'records' => $d,
                    'sale_amount' => $sale_amount,
                    'order_detial_arr' => $orderDetailArr
                ]);

            }
            elseif($company_id == 61){
                $order_details = DB::table('user_primary_sales_order_details')
                                ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                               ->select('catalog_product.id as product_id','itemcode','catalog_product.name','catalog_product.weight','user_primary_sales_order_details.rate','user_primary_sales_order_details.cases','user_primary_sales_order_details.cases','user_primary_sales_order_details.pcs as pcs','user_primary_sales_order_details.pr_rate','user_primary_sales_order_details.order_id','catalog_product.quantity_per_case')
                                ->where('user_primary_sales_order_details.company_id',$company_id)
                                ->whereIn('order_id', $orderArr)
                                ->get();

                $orderDetailArr = [];
                foreach ($order_details as $od) {
                    $orderDetailArr[$od->order_id][] = $od;
                    // $orderDetailArr[$od->order_id]['weight'] = $od->weight;
                    // $orderDetailArr[$od->order_id]['rate'] = $od->rate;
                    // $orderDetailArr[$od->order_id]['quantity'] = $od->quantity;
                    // $orderDetailArr[$od->order_id]['cases'] = $od->cases;
                    // $orderDetailArr[$od->order_id]['pcs'] = $od->pcs;
                    // $orderDetailArr[$od->order_id]['pr_rate'] = $od->pr_rate;
                    // $orderDetailArr[$od->order_id]['order_id'] = $od->order_id;
                }
                $product_rate_details = DB::table('product_rate_list')
                                        ->join('dealer','dealer.state_id','=','product_rate_list.state_id')
                                        ->join('catalog_product','catalog_product.id','=','product_rate_list.product_id')
                                        ->groupBy('product_rate_list.state_id','product_id')
                                        ->pluck('mrp',DB::raw("CONCAT(product_rate_list.product_id,product_rate_list.state_id) as id"));
    //            dd($orderDetailArr);
                return view('reports.distributer-stock-report.panelGuruji', [
                    'records' => $d,
                    'order_detial_arr' => $orderDetailArr,
                    'product_rate_details'=> $product_rate_details,
                ]);

            }
            else{
                $order_details = DB::table('user_primary_sales_order_details')
                                ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                                ->select('catalog_product.name','catalog_product.weight','user_primary_sales_order_details.rate','user_primary_sales_order_details.cases','user_primary_sales_order_details.cases','user_primary_sales_order_details.pcs as pcs','user_primary_sales_order_details.pr_rate','user_primary_sales_order_details.order_id')
                                ->where('user_primary_sales_order_details.company_id',$company_id)
                                ->whereIn('order_id', $orderArr)
                                ->groupBy('order_id','product_id')
                                ->get();

            }

           
            $orderDetailArr = [];
            foreach ($order_details as $od) {
                $orderDetailArr[$od->order_id][] = $od;
                // $orderDetailArr[$od->order_id]['weight'] = $od->weight;
                // $orderDetailArr[$od->order_id]['rate'] = $od->rate;
                // $orderDetailArr[$od->order_id]['quantity'] = $od->quantity;
                // $orderDetailArr[$od->order_id]['cases'] = $od->cases;
                // $orderDetailArr[$od->order_id]['pcs'] = $od->pcs;
                // $orderDetailArr[$od->order_id]['pr_rate'] = $od->pr_rate;
                // $orderDetailArr[$od->order_id]['order_id'] = $od->order_id;
            }
           // dd($d);
            return view('reports.distributer-stock-report.panel6', [
                'records' => $d,
                'order_detial_arr' => $orderDetailArr
            ]);
        } else {
            echo '<p class="alert-danger">Data Not Found</p>';
        }
    }


    public function distributerReportReturn(Request $request)
    {
         $company_id = Auth::user()->company_id;
        if ($request->ajax()) {

            $explodeDate = explode(" -", $request->date_range_picker);
            $user_id = $request->user;
            $region = $request->region;
            $state = $request->area;
            $town = $request->territory;
            $distributor = $request->distributor;
            $beat = $request->belt;
            $role = $request->role;
            $user = $request->user;
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

             $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50)
            {
            $datasenior='';
            $login_user=Auth::user()->id;
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                
                $datasenior_call=self::getJuniorUser($login_user);
                Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }

            $query_data = DB::table('damage_replace')
                ->select('l3_name','l4_name','l5_name','l6_name','retailer.name as rname', 'catalog_product.name as product_name', DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'),'person.id as uid','dealer.id as did', 'dealer.name as dealer_name', 'damage_replace.*','person.mobile','person.emp_code','person.person_id_senior','rolename')
                ->join('person', 'person.id', '=', 'damage_replace.user_id')
                ->join('_role','_role.role_id','=','person.role_id')
                ->join('dealer', 'dealer.id', '=', 'damage_replace.dis_code')
                ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
                ->join('catalog_product', 'catalog_product.id', 'damage_replace.prod_code')
                ->leftJoin('retailer', 'retailer.id', 'damage_replace.ret_code')
                ->where('damage_replace.company_id',$company_id);

                if (!empty($datasenior)) {
                $query_data->whereIn('person.id', $datasenior);
            }

            // $dealer_beat = DB::table('location_view')->where('l7_company_id.company_id',$company_id);

            // if (!empty($beat)) {

            //     $dealer_beat->whereIn('l5_id', $beat)->pluck('l5_id');
            // } //State Data
            // elseif (!empty($town)) {

            //     $dealer_beat->whereIn('l4_id', $town)->pluck('l5_id');
            // } //Town Data
            // elseif (!empty($state)) {

            //     $dealer_beat->whereIn('l3_id', $state)->pluck('l5_id');

            // } //Beat Data
            // elseif (!empty($region)) {
            //     $tr = [];
            //     $tr = $dealer_beat->whereIn('l1_id', $region)->pluck('l5_id');

            // }


            if (!empty($request->distributor)) {

                $tr = [];
                $tr = $request->distributor;
            }

            if (!empty($tr)) {
                $query_data->whereIn('damage_replace.dis_code', $tr);
            }

            // To find No of user under certain role 
            $flag = [];
            if (!empty($request->role)) {
                $roleArr = $request->role;
                if (!empty($roleArr)) {
                    $flag = DB::table('person')->where('person.company_id',$company_id)->whereIn('role_id', $roleArr)->pluck('id');
                }
            }

            if (!empty($user)) {

                $flag = [];
                $flag = $request->user;

            }

            if (!empty($flag)) {
                $query_data->whereIn('damage_replace.user_id', $flag);
            }

            if (!empty($from_date)) {
               
                $query_data->whereRaw("DATE_FORMAT(damage_replace.date_time, '%Y-%m-%d')>='$from_date' and DATE_FORMAT(damage_replace.date_time,'%Y-%m-%d') <='$to_date'");
            }

            $query = $query_data->get();

            return view('reports.distributer-stock-report.panel2', [
                'records' => $query
            ]);
        } else {
            echo '<p class="alert-danger">Data Not Found</p>';
        }
    } 

    public function paymemtCollectionReport(Request $request)
    {
        $company_id = Auth::user()->company_id;
        if ($request->ajax()) {
            $explodeDate = explode(" -", $request->date_range_picker);
            $user_id = $request->user;
            $region = $request->region;
            $state = $request->area;
            $town = $request->territory;
            $distributor = $request->distributor;
            $beat = $request->belt;
            $role = $request->role;
            $user = $request->user;
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

             $role_id=Auth::user()->is_admin;
            if($role_id==1 || $role_id==50)
            {
            $datasenior='';
            $login_user=Auth::user()->id;
            }else
            { 
                
                Session::forget('juniordata');
                $login_user=Auth::user()->id;
                
                $datasenior_call=self::getJuniorUser($login_user);
                Session::push('juniordata', $login_user);
                $datasenior = $request->session()->get('juniordata');
                if(empty($datasenior)){
                    $datasenior[]=$login_user;
                            }
            }

            $query_data = DB::table('dealer_payments')
                ->select('l3_name','l4_name','l5_name','l6_name','drawn_from_bank', 'deposited_bank', 'invoice_number', 'person.person_id_senior as person_id_senior','person.emp_code','person.mobile', DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as user_name'), 'person.id as uid','dealer.id as did','dealer.name as dealer_name', 'location_view.l1_name as zone', 'location_view.l2_name as region',
                    'location_view.l6_name as town_name', 'dealer_payments.*')
                ->join('person', 'person.id', '=', 'dealer_payments.user_id')
                ->join('dealer', 'dealer.id', '=', 'dealer_payments.dealer_id')
                ->join('location_view', 'location_view.l6_id', '=', 'dealer_payments.town')
                ->where('dealer_payments.company_id',$company_id);

             if (!empty($datasenior)) 
            {
                $query_data->whereIn('person.id', $datasenior);
            }

            $dealer_beat = DB::table('location_view');

            if (!empty($beat)) {

                $dealer_beat->whereIn('l5_id', $beat)->pluck('l5_id');
            } //State Data
            elseif (!empty($town)) {

                $dealer_beat->whereIn('l4_id', $town)->pluck('l5_id');
            } //Town Data
            elseif (!empty($state)) {

                $dealer_beat->whereIn('l3_id', $state)->pluck('l5_id');

            } //Beat Data
            elseif (!empty($region)) {
                $tr = [];
                $tr = $dealer_beat->whereIn('l1_id', $region)->pluck('l5_id');

            }
            if (!empty($request->location_3)) 
            {
                $location_3 = $request->location_3;
                $seniorInfo->whereIn('l3_id', $location_3);
            }
            
            if (!empty($request->location_4)) 
            {
                $location_4 = $request->location_4;
                $seniorInfo->whereIn('l4_id', $location_4);
            }
            if (!empty($request->location_5)) 
            {
                $location_5 = $request->location_5;
                $seniorInfo->whereIn('l5_id', $location_5);
            }
            if (!empty($request->location_6)) 
            {
                $location_6 = $request->location_6;
                $seniorInfo->whereIn('l6_id', $location_6);
            }


            if (!empty($request->distributor)) {

                $tr = [];
                $tr = $request->distributor;
            }

            if (!empty($tr)) {
                $query_data->whereIn('dealer_payments.dealer_id', $tr);
            }

            // To find No of user under certain role 
            $flag = [];
            if (!empty($request->role)) {
                $roleArr = $request->role;
                if (!empty($roleArr)) {
                    $flag = DB::table('person')->where('company_id',$company_id)->whereIn('role_id', $roleArr)->pluck('id');
                }
            }

            if (!empty($user)) {

                $flag = [];
                $flag = $request->user;

            }

            if (!empty($flag)) {
                $query_data->whereIn('dealer_payments.emp_id', $flag);
            }

            if (!empty($from_date)) {
               
                $query_data->whereRaw("DATE_FORMAT(dealer_payments.cur_datetime, '%Y-%m-%d')>='$from_date' and DATE_FORMAT(dealer_payments.cur_datetime,'%Y-%m-%d') <='$to_date'");
            }

            $query = $query_data->groupBy('location_view.l6_id','dealer_payments.drawn_from_bank','dealer_payments.deposited_bank','dealer_payments.invoice_number','person.emp_code','person.first_name','person.last_name','dealer.name','dealer_payments.id')->get();

            return view('reports.distributer-stock-report.panel3', [
                'records' => $query
            ]);
        } else {
            echo '<p class="alert-danger">Data Not Found</p>';
        }
    }
    public function order_wise_pdf_format_primary(Request $request)
    {
        $order_id = $request->order_id;
        $company_id = Auth::user()->company_id;
        $quer_data = DB::table('user_primary_sales_order')
                ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                ->join('product_type','product_type.id','=','catalog_product.product_type')
                ->join('dealer','dealer.id','=','user_primary_sales_order.dealer_id')
                ->select('catalog_product.name as product_name','final_secondary_qty','final_secondary_rate','dealer.name as retailer_name','user_primary_sales_order.order_id as order_id','user_primary_sales_order.sale_date as sale_date','dealer.other_numbers as retailer_no','product_type.name as primary_unit','discount_value as discount','user_primary_sales_order.comment as remarks','user_primary_sales_order.dispatch_through','user_primary_sales_order.destination')
                ->where('user_primary_sales_order.company_id',$company_id)
                ->where('user_primary_sales_order.order_id',$order_id)
                ->groupBy('user_primary_sales_order.order_id','product_id')
                ->get();

        $coampany_details = DB::table('company')->where('id',$company_id)->first();

        $customPaper = array(0, 0, 1240, 1748);
        $pdf_name = $order_id.'.pdf';
        // dd($pdf_name);
        $pdf = PDF::loadView('pdf/pdf', ['coampany_details' => $coampany_details,'data_query'=>$quer_data]);
        $pdf->setPaper($customPaper);

        $pdf->save(public_path('pdf/'.$pdf_name));
            // return $pdf->download('some-filename.pdf');
        
        $pdf_path = public_path() . '/pdf/' .$pdf_name;

        if(!empty($quer_data))
        {
            $data['code'] = 200;
            $data['pdf_name'] = $pdf_name;
            $data['message'] = 'success';
        }
            
        else {
            $data['code'] = 401;
            $data['pdf_name'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }




     public function makeOpeningStock(Request $request)
    {
            $company_id = Auth::user()->company_id;
            $login_id = Auth::user()->id;
            $explodeDate = explode(" -", $request->date_range_picker);
            $region = $request->region;
            $state = $request->area;
            $town = $request->territory;
            $distributor = $request->distributor;
            $beat = $request->belt;
            $role = $request->role;
            $user = $request->user;
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));


            $query_data = DB::table('dealer_balance_stock')
                ->select('dealer.id as dealer_id','product_id as product_id',DB::raw("SUM((stock_qty)+(cases*catalog_product.quantity_per_case)) as quantity"),'pcs_mrp as rate')
                ->leftJoin('person', 'person.id', '=', 'dealer_balance_stock.user_id')
                ->join('catalog_product', 'catalog_product.id', '=', 'dealer_balance_stock.product_id')
                ->join('dealer', 'dealer.id', '=', 'dealer_balance_stock.dealer_id')
                ->where('dealer_balance_stock.company_id',$company_id);

            $tmp = array();
            $dealer_beat = DB::table('location_view')->where('l7_company_id',$company_id);

            if (!empty($beat)) {
                $dealer_beat->whereIn('l7_id', $beat)->pluck('l7_id');
            } //State Data
            elseif (!empty($town)) {

                $dealer_beat->whereIn('l6_id', $town)->pluck('l7_id');
            } //Town Data
            elseif (!empty($state)) {

                $dealer_beat->whereIn('l3_id', $state)->pluck('l7_id');

            } //Beat Data
            elseif (!empty($region)) {
                $tr = [];
                $tr = $dealer_beat->whereIn('l1_id', $region)->pluck('l7_id');

            }


            if (!empty($request->distributor)) {

                $tr = [];
                $tr = $request->distributor;
            }

            if (!empty($tr)) {
                $query_data->whereIn('dealer_balance_stock.dealer_id', $tr);
            }

// To find No of user under certain role
            $flag = [];
            if (!empty($request->role)) {
                $roleArr = $request->role;
                if (!empty($roleArr)) {
                    $flag = DB::table('person')->where('person.company_id',$company_id)->whereIn('role_id', $roleArr)->pluck('id');
                }
            }

            if (!empty($user)) {

                $flag = [];
                $flag = $request->user;

            }

            if (!empty($flag)) {
                $query_data->whereIn('dealer_balance_stock.user_id', $flag);
            }

            if (!empty($from_date)) {
              
                $query_data->whereRaw("DATE_FORMAT(dealer_balance_stock.submit_date_time, '%Y-%m-%d') >= '$from_date'");

                $query_data->whereRaw("DATE_FORMAT(dealer_balance_stock.submit_date_time, '%Y-%m-%d') <= '$to_date'");
            }

            $query = $query_data->groupBy('dealer_id','product_id')->get(); 


            foreach($query as $query_key => $query_value){
                $dealer_id = $query_value->dealer_id;
                $product_id = $query_value->product_id;
                $quantity = $query_value->quantity;
                $rate = $query_value->rate;

                $order_id = date('YmdHis').$login_id;

                $curr = date('Y-m-d H:i:s');


                $insertDealerBalanceStockArray = [
                    'company_id' => $company_id,
                    'order_id' => $order_id,
                    'dealer_id' => $dealer_id,
                    'user_id' => $login_id,
                    'product_id' => $product_id,
                    'pcs_mrp' => $rate,
                    'stock_qty' => $quantity,
                    'submit_date_time' => $curr,
                    ];

            $insert_dealer_balance_stock = DB::table('dealer_balance_stock')->insert($insertDealerBalanceStockArray);



                $flush_stock = DB::table('stock')
                            ->where('dealer_id',$dealer_id)
                            ->where('product_id',$product_id)
                            ->where('company_id',$company_id)
                            ->delete();


                $insertArr = [
                    'product_id' => $product_id,
                    'rate' => $rate,
                    'dealer_id' => $dealer_id,
                    'qty' => $quantity,
                    'pr_rate' => $rate,
                    'company_id' => $company_id,
                    ];

                $insert_stock = DB::table('stock')->insert($insertArr);

            }

    return redirect('distributer-stock-report ');

    }



     public function distributerSecondarySale(Request $request)
     {
         $company_id = Auth::user()->company_id;

         if ($request->ajax()) {

         $state = $request->state; 
         $location5 = $request->location5; 
         $location6 = $request->location6; 
         $location7 = $request->location7; 
         $role = $request->role; 
         $dealer = $request->dealer; 
         $retailer = $request->retailer; 
         $user = $request->user; 
         $explodeDate = explode(" -", $request->date_range_picker);
         $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
         $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
 
         $main_query = DB::table('user_sales_order')->join('person','person.id','=','user_sales_order.user_id')
                     ->join('_role','_role.role_id','=','person.role_id')
                     ->join('dealer','dealer.id','=','user_sales_order.dealer_id')
                     ->join('retailer','retailer.id','=','user_sales_order.retailer_id')
                     ->join('location_view','location_view.l7_id','=','user_sales_order.location_id')
                     ->select('person.mobile as mobile_no','rolename','l7_name','l5_name','user_sales_order.date as date',DB::raw("DATE_FORMAT(user_sales_order.date,'%d-%m-%Y') AS show_date"),'user_sales_order.id AS uniq','location_view.l3_name as state','location_view.l6_name as city',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as person_name"),'user_sales_order.user_id as user_id','dealer.name as dealer_name','person.person_id_senior as person_id_senior','retailer.name as retailer_name','user_sales_order.location_id as usolid','user_sales_order.dealer_id AS did','user_sales_order.retailer_id as retailer_id')
                     ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
                     ->where('user_sales_order.company_id',$company_id)
                     ->groupBy('date','user_id','retailer_id');
                     if(!empty($state))
                     {
                         $main_query->whereIn('location_view.l3_id',$state);
                     }
                     if(!empty($location5))
                     {
                         $main_query->whereIn('l5_id',$location5);
                     }
                     if(!empty($location6))
                     {
                         $main_query->whereIn('l6_id',$location6);
                     }
                     if(!empty($location7))
                     {
                         $main_query->whereIn('l7_id',$location7);
                     }
                     if(!empty($role))
                     {
                         $main_query->whereIn('person.role_id',$role);
                     }
                     if(!empty($dealer))
                     {
                         $main_query->whereIn('dealer.id',$dealer);
                     }
                     if(!empty($retailer))
                     {
                         $main_query->whereIn('retailer.id',$retailer);
                     }
                     if(!empty($user))
                     {
                         $main_query->whereIn('person.id',$user);
                     }
                     $main_query_data = $main_query->get();
 
                     $secondry_sale_data = DB::table('user_sales_order_details')->join('user_sales_order','user_sales_order.order_id','=','user_sales_order_details.order_id')->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_date'")->where('user_sales_order.company_id',$company_id)->groupBy('user_id','date','dealer_id','retailer_id')->pluck(DB::raw("SUM(rate*quantity) as total_sale_value"),DB::raw("CONCAT(user_id,date,dealer_id,retailer_id) as total"));
 
                     $senior_name_data = DB::table('person')->where('person.company_id',$company_id)->groupBy('id')->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as senior_person_name"),'id');
 
                     // dd($secondry_sale_data);
                     $secondry_sale = array();
                     $senior_name = array();
 
                     foreach ($main_query_data as $key => $value) 
                     {
                         $user_id = $value->user_id;
                         $retailer_id = $value->retailer_id;
                         $did = $value->did;
                         $date = $value->date;
                         $person_id_senior = $value->person_id_senior;
 
                         $senior_name[$user_id][$date] = $senior_name_data[$person_id_senior];
 
                         $secondry_sale[$user_id][$date][$did][$retailer_id] = !empty($secondry_sale_data[$user_id.$date.$did.$retailer_id])?$secondry_sale_data[$user_id.$date.$did.$retailer_id]:'0';      
                         // dd($user_id.$date.$did.$retailer_id);               
                     }
                     // dd($secondry_sale);
 
                     return view('reports.distributer-stock-report.panel7', [
                                'secondry_sale'=>$secondry_sale,
                                'senior_name'=>$senior_name,
                                'main_query_data'=>$main_query_data,
 
                             ]);
                     }else {
                        echo '<p class="alert-danger">Data Not Found</p>';
                    }
 
 
     }


    public function distributorClosingStock(Request $request)
    {
         $company_id = Auth::user()->company_id;

         if ($request->ajax()) {

         $state = $request->state; 
         $location5 = $request->location5; 
         $location6 = $request->location6; 
         $location7 = $request->location7; 
         $role = $request->role; 
         $dealer = $request->dealer; 
         $retailer = $request->retailer; 
         $user = $request->user; 
         $explodeDate = explode(" -", $request->date_range_picker);
         $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
         $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));


        $openingStockValue = DB::table('dealer_balance_stock')
                            ->where('company_id',$company_id)
                            ->groupBy('dealer_id','submit_date_time')
                            ->pluck(DB::raw("SUM((stock_case*mrp)+(stock_qty*pcs_mrp)) as stock_value"),DB::raw("CONCAT(dealer_id,submit_date_time) as data"));

        $openingStockCase = DB::table('dealer_balance_stock')
                            ->join('catalog_product','catalog_product.id','=','dealer_balance_stock.product_id')
                            ->select(DB::raw("ROUND(SUM((stock_case)+(stock_qty/catalog_product.quantity_per_case)),2) as stock_value"),'dealer_id','submit_date_time')
                            ->where('dealer_balance_stock.company_id',$company_id)
                            ->groupBy('dealer_id','submit_date_time','product_id')
                            ->get();
        $caseOpening = array();
        foreach ($openingStockCase as $okey => $ovalue) {
            $caseOpening[$ovalue->dealer_id.$ovalue->submit_date_time][] = $ovalue->stock_value;
        }




        $optimiseOpeningStock = DB::table('dealer_balance_stock')
                            ->select('dealer_id',DB::raw("MIN(submit_date_time) as submit_date_time"))
                            ->where('company_id',$company_id)
                            ->groupBy('dealer_id')
                            ->get();

        $finalOpening = array();
        foreach ($optimiseOpeningStock as $key => $value) {
            $opening_dealer_id = $value->dealer_id;
            $opening_submit_date = $value->submit_date_time;

             $finalOpening[$opening_dealer_id]['opening_stock_date'] = !empty($opening_submit_date)?$opening_submit_date:'NA';

            $finalOpening[$opening_dealer_id]['opening_stock_value'] = !empty($openingStockValue[$opening_dealer_id.$opening_submit_date])?$openingStockValue[$opening_dealer_id.$opening_submit_date]:'0';


             $finalOpening[$opening_dealer_id]['opening_stock_case'] = !empty($caseOpening[$opening_dealer_id.$opening_submit_date])?array_sum($caseOpening[$opening_dealer_id.$opening_submit_date]):'0';


        }

        // dd($finalOpening);


        $dealerPrimarySale = DB::table('user_primary_sales_order')
                            ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                            ->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d') <= '$to_date'")
                            ->where('user_primary_sales_order.company_id',$company_id)
                            ->where('user_primary_sales_order_details.company_id',$company_id)
                            ->groupBy('dealer_id')
                            ->pluck(DB::raw("SUM((cases*pr_rate)+(rate*pcs)) as primarySaleValue"),'dealer_id');


        $dealerPrimarySaleCases = DB::table('user_primary_sales_order')
                            ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                            ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                            ->select(DB::raw("ROUND(SUM((cases)+(pcs/catalog_product.quantity_per_case)),2) as stock_cases"),'dealer_id','product_id')
                            ->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d') <= '$to_date'")
                            ->where('user_primary_sales_order.company_id',$company_id)
                            ->where('user_primary_sales_order_details.company_id',$company_id)
                            ->groupBy('dealer_id','user_primary_sales_order_details.product_id')
                            ->get();

        $primaryCase = array();
        foreach ($dealerPrimarySaleCases as $dpkey => $dpvalue) {
            $primaryCase[$dpvalue->dealer_id][] = $dpvalue->stock_cases;
        }



        $dealerSecondarySale = DB::table('user_sales_order')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') <= '$to_date'")
                                ->where('user_sales_order.company_id',$company_id)
                                ->where('user_sales_order_details.company_id',$company_id)
                                ->groupBy('dealer_id')
                                ->pluck(DB::raw("SUM(rate*quantity) as total_sale_value"),'dealer_id');

        $dealerSecondarySaleCases = DB::table('user_sales_order')
                                ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                ->select(DB::raw("ROUND(SUM(quantity)/quantity_per_case,2) as stock_cases"),'dealer_id','product_id')
                                ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') >= '$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d') <= '$to_date'")
                                ->where('user_sales_order.company_id',$company_id)
                                ->where('user_sales_order_details.company_id',$company_id)
                                ->groupBy('dealer_id','product_id')
                                ->get();

         $secondaryCase = array();
        foreach ($dealerSecondarySaleCases as $sckey => $scvalue) {
            $secondaryCase[$scvalue->dealer_id][] = $scvalue->stock_cases;
        }


        $main_query = DB::table('dealer')
                        ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                        ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
                        ->select('dealer.other_numbers as mobile_no','l7_name','l5_name','location_view.l3_name as state','location_view.l6_name as city','dealer.name as dealer_name','dealer.id as did')
                        ->where('dealer.company_id',$company_id)
                        ->where('dealer_location_rate_list.company_id',$company_id)
                        ->where('dealer.dealer_status','=','1')
                        ->groupBy('dealer.id');

                         if(!empty($state))
                     {
                         $main_query->whereIn('location_view.l3_id',$state);
                     }
                     if(!empty($location5))
                     {
                         $main_query->whereIn('l5_id',$location5);
                     }
                     if(!empty($location6))
                     {
                         $main_query->whereIn('l6_id',$location6);
                     }
                     if(!empty($location7))
                     {
                         $main_query->whereIn('l7_id',$location7);
                     }
                     if(!empty($role))
                     {
                         $main_query->join('person','person.id','=','dealer_location_rate_list.user_id')->whereIn('person.role_id',$role);
                     }
                     if(!empty($dealer))
                     {
                         $main_query->whereIn('dealer.id',$dealer);
                     }
                     if(!empty($user))
                     {
                         $main_query->join('person','person.id','=','dealer_location_rate_list.user_id')->whereIn('person.id',$user);
                     }
                       if(!empty($user) && !empty($role))
                     {
                         $main_query->join('person','person.id','=','dealer_location_rate_list.user_id')->whereIn('person.id',$user)->whereIn('person.role_id',$role);
                     }

        $main_query_data = $main_query->get();

        // dd($main_query_data);
        $finalArray = array();
         foreach ($main_query_data as $key => $value) 
         {
                $did = $value->did;

                $out[$did]['did'] = $value->did;
                $out[$did]['mobile_no'] = $value->mobile_no;
                $out[$did]['l7_name'] = $value->l7_name;
                $out[$did]['l5_name'] = $value->l5_name;
                $out[$did]['state'] = $value->state;
                $out[$did]['city'] = $value->city;
                $out[$did]['dealer_name'] = $value->dealer_name;


                $out[$did]['openingStock'] = !empty($finalOpening[$did]['opening_stock_value'])?$finalOpening[$did]['opening_stock_value']:'0';

                $out[$did]['openingStockCase'] = !empty($finalOpening[$did]['opening_stock_case'])?$finalOpening[$did]['opening_stock_case']:'0';


                $out[$did]['primarySale'] = !empty($dealerPrimarySale[$did])?$dealerPrimarySale[$did]:'0';

                $out[$did]['primarySaleCases'] = !empty($primaryCase[$did])?array_sum($primaryCase[$did]):'0';



                $out[$did]['secondarySale'] = !empty($dealerSecondarySale[$did])?$dealerSecondarySale[$did]:'0';

                $out[$did]['secondarySaleCases'] = !empty($secondaryCase[$did])?array_sum($secondaryCase[$did]):'0';



                $openingStock = !empty($finalOpening[$did]['opening_stock_value'])?$finalOpening[$did]['opening_stock_value']:'0';
                $primarySale = !empty($dealerPrimarySale[$did])?$dealerPrimarySale[$did]:'0';
                $secondarySale =  !empty($dealerSecondarySale[$did])?$dealerSecondarySale[$did]:'0';


                 $openingStockCases = !empty($finalOpening[$did]['opening_stock_case'])?$finalOpening[$did]['opening_stock_case']:'0';
                $primarySaleCases = !empty($primaryCase[$did])?array_sum($primaryCase[$did]):'0';
                $secondarySaleCases =  !empty($secondaryCase[$did])?array_sum($secondaryCase[$did]):'0';


                $out[$did]['closingStock'] = ($openingStock+$primarySale-$secondarySale);

                $out[$did]['closingStockCases'] = ($openingStockCases+$primarySaleCases-$secondarySaleCases);



                $finalArray = $out;


         }


         // dd($finalArray);
   

 
                     return view('reports.distributer-stock-report.panel8', [
                                'finalArray'=>$finalArray,
                             ]);
                     }else {
                        echo '<p class="alert-danger">Data Not Found</p>';
                    }
    }




     public function getJuniorUser($code)
    {
        $res1="";
        $res2="";
        $details = UserDetail::where('person_id_senior',$code)
            ->select('id as user_id')->get();
            $num = count($details);  
            if($num>0)
            {
                foreach($details as $key=>$res2)
                {
                    if($res2->user_id!="")
                    {
                        //$product = collect([1,2,3,4]);
                        Session::push('juniordata', $res2->user_id);
                       // $_SESSION['juniordata'][]=$res2->user_id;
                        $this->getJuniorUser($res2->user_id);
                    }
                }
                
            }
            else
            {
                foreach($details as $key1=>$res1)
                {
                    if($res1->user_id!="")
                    {
                        Session::push('juniordata', $res1->user_id);
                        // $_SESSION['juniordata'][]= $res1->user_id;
                    }
                }

                
            }
            return 1;
    } 

}