<?php

namespace App\Http\Controllers;

use App\_module;
use App\_role;
use App\Competitor;
use App\Dealer;
use App\Location1;
use App\Location2;
use App\Catalog2;
use App\CatalogProduct;
use App\Location3;
use App\Location4;
use App\Location5;
use App\UserDetail;
use App\Location6;
use App\Location7;
use Illuminate\Http\Request;
use DB;
use Auth;
use Session;
use App\Helpers\LocationArray;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->menu = _module::orderBy('module_sequence')->get()->load('submenu');
        $this->current = 'Report';
    }

    /**
     * @function: beatRoute
     * @desc: index page of beat route
     */
    public function beatRoute(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $locationArr = LocationArray::locationArr();
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $region = Location2::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $state = Location3::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status','=', '1')->where('company_id',$company_id)->groupBy('dealer.id')->orderBy('name', 'asc')->pluck('name', 'id');
        // dd($distributor);
        $outlet = DB::table('_retailer_outlet_type')->where('status', 1)->where('company_id',$company_id)->orderBy('outlet_type', 'asc')->pluck('outlet_type', 'id');

        return view('reports.beat-route', [
            'zone' => $zone,
            'region' => $region,
            'state' => $state,
            'belt' => $town,
            'beat' => $beat,
            'area'=>$area,
            'head_quater'=>$head_quater,
            'menu' => $this->menu,
            'distributor' => $distributor,
            'outlet' => $outlet,
            'current_menu' => $this->current
        ]);
    }

    /**
     * @function: marketBeatPlan
     * @desc: index page of Market Beat Plan
     */
    public function marketBeatPlan()
    {
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $region = Location2::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $belt = Location3::where('status', 1)->orderBy('name', 'asc')->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $town = Location6::where('status', 1)->orderBy('name', 'asc')->pluck('name', 'id');
        $beat = Location7::where('status', 1)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->pluck('name', 'id');
        return view('reports.market-beat-plan.market-beat-plan',
            [
                'belt' => $belt,
                'beat' => $beat,
                'town' => $town,
                'head_quater'=> $head_quater,
                'zone'=>$zone,
                'region'=>$region,
                'area'=> $area,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function tourProgram()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $role = DB::table('_role')->where('company_id',$company_id)->pluck('rolename', 'role_id');
        $user = DB::table('person')->join('person_login','person_login.person_id','=','person.id')->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')->where('person.company_id',$company_id)->where('person_status',1)->pluck('name', 'uid');
        return view('reports.tour-program.index',
            [
                'region' => $region,
                'state' => $state,
                'town' => $town,
                'beat' => $beat,
                'zone'=> $zone,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'distributor' => $distributor,
                'role' => $role,
                'users' => $user,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    /**
     * @function: dailyAttendance
     * @desc: daily attendance report
     */
    public function dailyAttendance()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')->where('company_id',$company_id)->pluck('name', 'uid');
        
        return view('reports.daily-attendance.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'distributor' => $distributor,
                'company_id' => $company_id,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function dailyPerformance()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $role = DB::table('_role')->where('company_id',$company_id)->where('status',1)->pluck('rolename', 'role_id');
        $user = DB::table('person')->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')->where('company_id',$company_id)->pluck('name', 'uid');
        return view('reports.daily-performance.index',
            [
                'region' => $region,
                'state' => $state,
                'town' => $town,
                'beat' => $beat,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'zone'=> $zone,
                'distributor' => $distributor,
                'role' => $role,
                'users' => $user,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function productInvestigation()
    {
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')->join('person_login','person_login.person_id','=','person.id')->where('company_id',$company_id)->where('person_status',1)->where('id','!=',1);
        $user_data = $user->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
            ->pluck('name', 'uid');

        return view('reports.product-investigation.index',
            [
                'region' => $region,
                'zone' => $zone,
                'state' => $state,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'user' => $user_data,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function competitorsNewProduct()
    {
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')->join('person_login','person_login.person_id','=','person.id')->where('company_id',$company_id)->where('person_status',1)->where('id','!=',1);
        $user_data = $user->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
            ->pluck('name', 'uid');
        $position = DB::table('position_master')->pluck('name', 'id');
        return view('reports.competitors-new-product.index',
            [
                'zone' => $zone,
                'region' => $region,
                'state' => $state,
                'user' => $user_data,
                'position' => $position,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function complaintReport()
    {
        $company_id = Auth::user()->company_id;
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')->join('person_login','person_login.person_id','=','person.id')->where('company_id',$company_id)->where('person_status',1)->where('id','!=',1);
        $user_data = $user->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
            ->pluck('name', 'uid');
        return view('reports.complaint-report.index',
            [
                'region' => $region,
                'zone' => $zone,

                'state' => $state,
                'user' => $user_data,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function travellingExpenses()
    {
        $company_id = Auth::user()->company_id;

        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')->join('person_login','person_login.person_id','=','person.id')->where('person.company_id',$company_id)->where('person_status',1)->where('id','!=',1);
        $user_data = $user->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
            ->pluck('name', 'uid');
        return view('reports.travelling-expenses.index',
            [
                'region' => $region,
                'zone' => $zone,
                'area'=> $area,
                'town'=> $town,
                'beat'=> $beat,
                'state' => $state,
                'user' => $user_data,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function pendingClaim()
    {
        $zone = Location1::where('status', 1)->pluck('name', 'id');
        $region = Location2::where('status', 1)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->pluck('name', 'id');
        $user = DB::table('person')->where('id','!=',1);
        $user_data = $user->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
            ->pluck('name', 'uid');
        $position = DB::table('position_master')->pluck('name', 'id');
        return view('reports.pending-claim.index',
            [
                'zone' => $zone,
                'region' => $region,
                'state' => $state,
                'user' => $user_data,
                'position' => $position,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function competitivePriceIntelligence()
    {
        $zone = Location1::where('status', 1)->pluck('name', 'id');
        $region = Location2::where('status', 1)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->pluck('name', 'id');
        // 1 Passed For Not showing Admin in user Listing
        $user = DB::table('person')->whereNotIn('id',[1]);
        $user_data = $user->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
            ->pluck('name', 'uid');
        //$query
        $position = DB::table('position_master')->pluck('name', 'id');
        return view('reports.competitive-price-intelligence.index',
            [
                'zone' => $zone,
                'region' => $region,
                'state' => $state,
                'user' => $user_data,
                'position' => $position,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function newSdDistProspecting()
    {
        $zone = Location1::where('status', 1)->pluck('name', 'id');
        $region = Location2::where('status', 1)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->pluck('name', 'id');
        // 1 Passed for Not showing Admin In User listing
        $user = DB::table('person')->whereNotIn('id',[1]);
        $user_data = $user->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
            ->pluck('name', 'uid');
        return view('reports.new-sd-dist-prospecting.index',
            [
                'region' => $region,
                'zone' => $zone,
                'state' => $state,
                'user' => $user_data,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function aging()
    {
        $company_id = Auth::user()->company_id;
        $region = Location3::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        return view('reports.aging.index',
            [
                'region' => $region,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function distributorStockStatus()
    {
        $dealer = Dealer::where('dealer_status', 1)->pluck('name', 'id');
        return view('reports.distributor-stock-status.index',
            [
                'dealer' => $dealer,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function stockInHand()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        return view('reports.stock-in-hand.index',
            [
                'region' => $region,
                'state' => $state,
                'town' => $town,
                'beat' => $beat,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function monthSPrimaryAndSecondarySalesPlan()
    {

        $locationArr = LocationArray::locationArr();
        $town = Location4::where('status', 1)->orderBy('name', 'asc')->pluck('name', 'id');
        $beat = Location5::where('status', 1)->orderBy('name', 'asc')->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->orderBy('name', 'asc')->pluck('name', 'id');

        $outlet = DB::table('_retailer_outlet_type')->where('status', 1)->orderBy('outlet_type', 'asc')->pluck('outlet_type', 'id');

        return view('reports.month-s-primary-and-secondary-sales-plan.index', [
            'belt' => $town,
            'beat' => $beat,
            'menu' => $this->menu,
            'distributor' => $distributor,
            'outlet' => $outlet,
            'current_menu' => $this->current
        ]);
    }

    public function ucdp()
    {

        $region = Location2::where('status', 1)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->pluck('name', 'id');
        $town = Location4::where('status', 1)->pluck('name', 'id');
        $beat = Location5::where('status', 1)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->pluck('name', 'id');

        $user = DB::table('person')->where('id','!=',1)->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')->pluck('name', 'uid');

        return view('reports.ucdp.index',
            [
                'region' => $region,
                'state' => $state,
                'town' => $town,
                'beat' => $beat,
                'distributor' => $distributor,
                'users' => $user,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function boardReview()
    {

        $region = Location2::where('status', 1)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->pluck('name', 'id');
        $town = Location4::where('status', 1)->pluck('name', 'id');
        $beat = Location5::where('status', 1)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->pluck('name', 'id');
        $user = DB::table('person')->where('id','!=',1)->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')->pluck('name', 'uid');

        return view('reports.board_review.index',
            [
                'region' => $region,
                'state' => $state,
                'town' => $town,
                'beat' => $beat,
                'distributor' => $distributor,
                'users' => $user,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);


    }

    public function rsWiseSecondarySales()
    {
        $query = Location3::where('status', 1)->pluck('name', 'id');
        return view('reports.rs-wise-secondary-sales.index',
            [
                'region' => $query,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function rsmAsmSoPerformance()
    {

        $region = Location2::where('status', 1)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->pluck('name', 'id');
        $town = Location4::where('status', 1)->pluck('name', 'id');
        $beat = Location5::where('status', 1)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->pluck('name', 'id');

        $user = DB::table('person')->where('id','!=',1)->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')->pluck('name', 'uid');

        return view('reports.rsm_asm_so_performance.index',
            [
                'region' => $region,
                'state' => $state,
                'town' => $town,
                'beat' => $beat,
                'distributor' => $distributor,
                'users' => $user,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    
    public function distributorPerformance()
    {

        $region = Location2::where('status', 1)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->pluck('name', 'id');
        $town = Location4::where('status', 1)->pluck('name', 'id');
        $beat = Location5::where('status', 1)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->pluck('name', 'id');

        $user = DB::table('person')->where('id','!=',1)->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')->pluck('name', 'uid');

        return view('reports.distributor-performance.index',
            [
                'region' => $region,
                'state' => $state,
                'town' => $town,
                'beat' => $beat,
                'distributor' => $distributor,
                'users' => $user,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function salesTrends()
    {
        $region = Location2::where('status', 1)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->pluck('name', 'id');
        $town = Location4::where('status', 1)->pluck('name', 'id');
        $beat = Location5::where('status', 1)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->pluck('name', 'id');

        return view('reports.sales-trends.index',
            [
                'region' => $region,
                'state' => $state,
                'town' => $town,
                'beat' => $beat,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function outletOpeningStatus()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        return view('reports.outlet-opening-status.index',
            [
                'region' => $region,
                'state'=> $state,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'zone'=> $zone,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function salesReview()
    {
        $region = Location2::where('status', 1)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->pluck('name', 'id');
        $town = Location4::where('status', 1)->pluck('name', 'id');
        $beat = Location5::where('status', 1)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->pluck('name', 'id');

        $user = DB::table('person')->where('id','!=',1)->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')->pluck('name', 'uid');

        return view('reports.distributor-performance.index',
            [
                'region' => $region,
                'state' => $state,
                'town' => $town,
                'beat' => $beat,
                'distributor' => $distributor,
                'users' => $user,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function feedback()
    {
        $zone = Location1::where('status', 1)->pluck('name', 'id');
        $region = Location2::where('status', 1)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->pluck('name', 'id');
        $user = DB::table('person')->where('id','!=',1);
        $user_data = $user->select(DB::raw('CONCAT_WS(person.first_name," ",person.last_name) as name'), 'person.id as uid')
            ->pluck('name', 'uid');

        return view('reports.feedback.index',
            [
                'region' => $region,
                'zone' => $zone,

                'state' => $state,
                'user' => $user_data,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function paymentDetails()
    {
        $company_id = Auth::user()->company_id;
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $region = Location2::where('status', 1)->select('name', 'id')->where('company_id',$company_id)->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
//        $role=DB::table('_role')->pluck('rolename','role_id');
        $user = DB::table('person')->where('id','!=',1)->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')->where('person.company_id',$company_id)->pluck('name', 'uid');

        return view('reports.payment-details.index',
            [
                'region' => $region,
                'zone'=> $zone,
                'state' => $state,
                'town' => $town,
                'beat' => $beat,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'distributor' => $distributor,
                'users' => $user,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function isrSoTgtMonth()
    {
        $state = Location3::where('status', 1)->pluck('name', 'id');
        $town = Location4::where('status', 1)->pluck('name', 'id');

        $isr_so_role = [];
        $isr_so_role = DB::table('_role')
            ->where('filter', 1)
            ->pluck('role_id');
        $user = DB::table('person')
            ->whereIn('role_id', $isr_so_role)
            ->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')
            ->where('person.first_name', '!=', '')
            ->pluck('name', 'uid');

        return view('reports.isr-so-tgt-month.index',
            [
                'state' => $state,
                'town' => $town,
                'user' => $user,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function distributerStockReport()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $role = DB::table('_role')->where('status',1)->where('company_id',$company_id)->pluck('rolename', 'role_id');
        $user = DB::table('person')->join('person_login','person_login.person_id','=','person.id')->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')->where('person_status',1)->where('person.company_id',$company_id)->pluck('name', 'uid');
        return view('reports.distributer-stock-report.index',
            [
                'region' => $region,
                'state' => $state,
                'town' => $town,
                'beat' => $beat,
                'head_quater'=> $head_quater,
                'area'=> $area,
                'distributor' => $distributor,
                'role' => $role,
                'users' => $user,
                'company_id' => $company_id,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);

    }

    public function distributerWiseSecondarySalesTrends()
    {
        $company_id = Auth::user()->company_id;
        $dealer= Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        return view('reports.distributer-wise-sales-trends.index',
            [
                'dealer_data' => $dealer,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function userSalesSummary()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')->where('company_id',$company_id)->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')->pluck('name', 'uid');
        return view('reports.user_sales_summary.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function stateWiseSecondarySalesTrends()
    {
        $company_id = Auth::user()->company_id;
       $state = DB::table('location_3')->where('status', 1)->where('company_id',$company_id)
                ->pluck('name','id');
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $region = Location2::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
       //dd($dealer);
       return view('reports.state-wise-sales-trends.index',
            [
                'state' => $state,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'zone'=> $zone,
                'region'=> $region,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

     public function statewise_user_eatos()
    {
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')->pluck('name', 'uid');
       //dd($dealer);
       return view('reports.statewise_user_eatos.index',
            [
                'state' => $state,
                'user' => $user,
                'town'=> $town,
                'beat'=> $beat,
                'zone'=> $zone,
                'region'=> $region,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }
 ####################
       public function budget_target()
    {
        $region = Location2::where('status', 1)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->pluck('name', 'id');
        $user = DB::table('person')->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')->pluck('name', 'uid');
       //dd($dealer);
       return view('reports.budget_target_status.index',
            [
                'state' => $state,
                'user' => $user,
                'region' => $region,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    #..........................monthly progressive function starts here ..................................#
    public function monthlyProgressive()
     {
        $company_id = Auth::user()->company_id;
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $region = Location2::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
      
        return view('reports.monthly-progressive.index',
            [
                
                'state' => $state,
                'zone'=>$zone,
                'region'=>$region,
                'area'=>$area,
                'head_quater'=>$head_quater,
                'town'=>$town,
                'beat'=>$beat,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }
    #..........................monthly progressive function ends here ..................................#

    #..........................Tall Report ss billing function starts here ...............................#
    public function tallySsBilling(Request $request)
    {
        $state = Location3::where('status', 1)->pluck('name', 'id');
        $super_stockist_name = DB::table('csa')->select('csa_name','c_id')->get();
        return view('reports.tally_ss_billing_report.index',
            [
                'state' => $state,
                'super_stockist_name' => $super_stockist_name,
            ]);

    }
    #..........................Tall Report ss billing function Ends here ...............................#
    #..........................Tall Report ss stock function starts here ...............................#
    public function tallySsStock(Request $request)
    {
        $state = Location3::where('status', 1)->pluck('name', 'id');
        $super_stockist_name = DB::table('csa')->select('csa_name','c_id')->get();
        return view('reports.tally_ss_stock_report.index',
            [
                'state' => $state,
                'super_stockist_name' => $super_stockist_name,
            ]);

    }
    #..........................Tall Report ss stock function Ends here ...............................#
        #..........................Tall Report ss stock function starts here ...............................#
    public function tallySsClosingStock(Request $request)
    {
        $state = Location3::pluck('name', 'id');
        $super_stockist_name = DB::table('csa')->select('csa_name','c_id')->get();
        return view('reports.tally_ss_closing_report.index',
            [
                'state' => $state,
                'super_stockist_name' => $super_stockist_name,
            ]);

    }
    #..........................Tall Report ss stock function Ends here ...............................#

    
    #...........................date_product_wise_sale_report starts here.............................#
    public function date_wise_product_wise(Request $request)
    {
        $state = Location3::pluck('name', 'id');
        $zone = Location2::pluck('name', 'id');
        $user = DB::table('person')->join('person_login','person_login.person_id','=','person.id')->where('person_status',1)->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id');

        return view('reports.product_wise_sale_report.index',
            [
                'state' => $state,
                'zone' => $zone,
                'user' => $user,
            ]);

    }
    #...........................date_product_wise_sale_report ends here...............................#

    #...........................dailyReporting starts here.............................#
    public function dailyReporting()
    {
        $company_id = Auth::user()->company_id;
        $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.user-daily-reporting.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=>$area,
                'head_quater'=>$head_quater,
                'town'=>$town,
                'beat'=>$beat,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }
    #...........................dailyReporting Ends here.............................#


      #...........................mobileOnOff starts here.............................#
      public function mobileOnOff()
      {
          $company_id = Auth::user()->company_id;
          $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
          $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
          $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
          $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
          $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
          $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
          $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
          $user = DB::table('person')
          ->join('person_login','person_login.person_id','=','person.id')
          ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
          ->where('person_login.person_status','=','1')
          ->where('person.company_id',$company_id)
          ->pluck('name', 'uid');
          return view('reports.mobile-on-off.index',
              [
                  'region' => $region,
                  'state' => $state,
                  'users' => $user,
                  'area'=>$area,
                  'head_quater'=>$head_quater,
                  'town'=>$town,
                  'beat'=>$beat,
                  'distributor' => $distributor,
                  'menu' => $this->menu,
                  'current_menu' => $this->current
              ]);
      }
      #...........................mobileOnOff Ends here.............................#


         #...........................userComplaint starts here.............................#
         public function userComplaint()
         {
             $company_id = Auth::user()->company_id;
             $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
             $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
             $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
             $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
             $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
             $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
             $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
             $user = DB::table('person')
             ->join('person_login','person_login.person_id','=','person.id')
             ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
             ->where('person_login.person_status','=','1')
             ->where('person.company_id',$company_id)
             ->pluck('name', 'uid');
             return view('reports.user-complaint.index',
                 [
                     'region' => $region,
                     'state' => $state,
                     'users' => $user,
                     'area'=>$area,
                     'head_quater'=>$head_quater,
                     'town'=>$town,
                     'beat'=>$beat,
                     'distributor' => $distributor,
                     'menu' => $this->menu,
                     'current_menu' => $this->current
                 ]);
         }
         #...........................userComplaint Ends here.............................#

      #...........................notificationNonContacted starts here.............................#
      public function notificationNonContacted()
      {
          $company_id = Auth::user()->company_id;
          $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
          $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
          $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
          $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
          $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
          $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
          $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
          $user = DB::table('person')
          ->join('person_login','person_login.person_id','=','person.id')
          ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
          ->where('person_login.person_status','=','1')
          ->where('person.company_id',$company_id)
          ->pluck('name', 'uid');
          return view('reports.notification-non-contacted.index',
              [
                  'region' => $region,
                  'state' => $state,
                  'users' => $user,
                  'area'=>$area,
                  'head_quater'=>$head_quater,
                  'town'=>$town,
                  'beat'=>$beat,
                  'distributor' => $distributor,
                  'menu' => $this->menu,
                  'current_menu' => $this->current
              ]);
      }
      #...........................notificationNonContacted Ends here.............................#





       #...........................attendance summary starts here.............................#
       public function attendanceSummary()
       {
           $company_id = Auth::user()->company_id;
           $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
           $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
           $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
           $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
           $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
           $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
           $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
           $user = DB::table('person')
           ->join('person_login','person_login.person_id','=','person.id')
           ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
           ->where('person_login.person_status','=','1')
           ->where('person.company_id',$company_id)
           ->pluck('name', 'uid');
           return view('reports.user-attendance-summary.index',
               [
                   'region' => $region,
                   'state' => $state,
                   'users' => $user,
                   'area'=>$area,
                   'head_quater'=>$head_quater,
                   'town'=>$town,
                   'beat'=>$beat,
                   'distributor' => $distributor,
                   'menu' => $this->menu,
                   'current_menu' => $this->current
               ]);
       }
       #...........................attendance summary Ends here.............................#

              #...........................callTimeSummary starts here.............................#
              public function callTimeSummary()
              {
                  $company_id = Auth::user()->company_id;
                  $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
                  $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
                  $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
                  $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
                  $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
                  $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
                  $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
                  $user = DB::table('person')
                  ->join('person_login','person_login.person_id','=','person.id')
                  ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                  ->where('person_login.person_status','=','1')
                  ->where('person.company_id',$company_id)
                  ->pluck('name', 'uid');
                  return view('reports.call-time-summary.index',
                      [
                          'region' => $region,
                          'state' => $state,
                          'users' => $user,
                          'area'=>$area,
                          'head_quater'=>$head_quater,
                          'town'=>$town,
                          'beat'=>$beat,
                          'distributor' => $distributor,
                          'menu' => $this->menu,
                          'current_menu' => $this->current
                      ]);
              }
              #...........................callTimeSummary Ends here.............................#





        #...........................sales team attendance starts here.............................#
        public function salesTeamAttendanceSummary()
        {
            $company_id = Auth::user()->company_id;
            $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
            $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            $user = DB::table('person')
            ->join('person_login','person_login.person_id','=','person.id')
            ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
            ->where('person_login.person_status','=','1')
            ->where('person.company_id',$company_id)
            ->pluck('name', 'uid');
            return view('reports.sales-team-attendance-summary.index',
                [
                    'region' => $region,
                    'state' => $state,
                    'users' => $user,
                    'area'=>$area,
                    'head_quater'=>$head_quater,
                    'town'=>$town,
                    'beat'=>$beat,
                    'distributor' => $distributor,
                    'menu' => $this->menu,
                    'current_menu' => $this->current
                ]);
        }
        #..........................sales team attendance Ends here.............................#

    #...........................circularReport starts here.............................#
    public function circularReport()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')->where('company_id',$company_id)->pluck('name', 'uid');
        return view('reports.circular-report.index',
            [
                'region' => $region,
                'state' => $state,
                'area'=>$area,
                'head_quater'=>$head_quater,
                'town'=>$town,
                'beat'=>$beat,
                'users' => $user,
            ]);
    }
    #...........................circularReport ends here.............................#

    #...........................timeAttd starts here.............................#
    public function timeAttd()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')->where('company_id',$company_id)->pluck('name', 'uid');
        return view('reports.time-report.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=>$area,
                'head_quater'=>$head_quater,
                'town'=>$town,
                'beat'=>$beat,
                'distributor' => $distributor,
                'company_id' => $company_id,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }
    #...........................timeAttd ends here.............................#


      #...........................timeAttd for btw starts here.............................#
    public function timeAttdBtw()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')->where('company_id',$company_id)->pluck('name', 'uid');
        return view('reports.time-report.btwindex',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=>$area,
                'head_quater'=>$head_quater,
                'town'=>$town,
                'beat'=>$beat,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }
    #...........................timeAttd forbtw ends here.............................#

    #...........................time sale report starts here.............................#
    public function time_report()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $role = _role::where('status', 1)->where('company_id',$company_id)->pluck('rolename', 'role_id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')
                ->join('users','users.id','=','person.id')
                ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                ->where('person.company_id',$company_id)
                ->where('is_admin','!=','1')
                ->pluck('name', 'uid');
        return view('reports.time-report-sale.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=>$area,
                'head_quater'=>$head_quater,
                'town'=>$town,
                'beat'=>$beat,
                'role'=>$role,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }
    #...........................time sale report ends here.............................#

    #...........................dailyReporting starts here.............................#
    public function expense_report()
    {
        $region = Location1::where('status', 1)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->pluck('name', 'id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->pluck('name', 'uid');
        return view('reports.user-expense-report.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }
    #...........................dailyReporting Ends here.............................#

    #...........................userSales starts here.............................#
    public function userSales()
    {
        $company_id = Auth::user()->company_id;
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person.company_id',$company_id)
        ->where('person_login.person_status','=','1')
        ->pluck('name', 'uid');
        
        $region = DB::table('location_3')->where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = DB::table('location_6')->where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = DB::table('location_3')->where('company_id',$company_id)->pluck('name', 'id');

        $beat = DB::table('location_7')->where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');


        $distributor = DB::table('dealer')->where('company_id',$company_id)->pluck('name', 'id');

        $product= DB::table('catalog_product')
             ->where('status',1)
             ->where('company_id',$company_id)
             ->groupBy('id')
             ->pluck('name', 'id');
        // dd($details);
        return view('reports.user_sale_report.index',
            [
                'region' => $region,
                'area' => $area,
                'user' => $user,
                'product'=>$product,
                'town'=>$town,
                'distributor'=>$distributor,
                'beat'=>$beat,
                'menu' => $this->menu,
                'current_menu' => $this->current,
               
            ]);
    }
    #...........................userSales ends here.............................#
    #...........................userSales starts here.............................#
    public function sales_man_secondary()
    {
        $company_id = Auth::user()->company_id;
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        
        
        $role = DB::table('_role')->where('status',1)->where('company_id',$company_id)->pluck('rolename','role_id');
        $zone =Location1::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $region =Location2::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $state =Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        // dd($details);
        return view('reports.sales-man-secondary.index',
            [
                'state' => $state,
                'role' => $role,
                'user' => $user,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'zone'=> $zone,
                'region'=> $region,
            ]);
    }

    public function skuSales()
    {
        $company_id = Auth::user()->company_id;
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        
        
        $role = DB::table('_role')->where('status',1)->where('company_id',$company_id)->pluck('rolename','role_id');
        $zone =Location1::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $region =Location2::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $state =Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $retailer = DB::table('retailer')->where('company_id',$company_id)->pluck('name', 'id');
        $product = DB::table('catalog_product')->where('company_id',$company_id)->pluck('name', 'id');
        // dd($details);
        return view('reports.skuSales.index',
            [
                'state' => $state,
                'role' => $role,
                'user' => $user,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'zone'=> $zone,
                'region'=> $region,
                'retailer'=> $retailer,
                'product'=> $product,
            ]);
    }

    public function skuSalesPrimary()
    {
        $company_id = Auth::user()->company_id;
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        
        
        $role = DB::table('_role')->where('status',1)->where('company_id',$company_id)->pluck('rolename','role_id');
        $zone =Location1::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $region =Location2::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $state =Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $retailer = DB::table('dealer')->where('company_id',$company_id)->pluck('name', 'id');
        $product = DB::table('catalog_product')->where('company_id',$company_id)->pluck('name', 'id');
        // dd($details);
        return view('reports.skuSalesPrimary.index',
            [
                'state' => $state,
                'role' => $role,
                'user' => $user,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'zone'=> $zone,
                'region'=> $region,
                'retailer'=> $retailer,
                'product'=> $product,
            ]);
    }
    #...........................userSales ends here.............................#

    #...........................score card starts here.............................#
    public function score_card()
    {
        $company_id = Auth::user()->company_id;
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        
        
        $role = DB::table('_role')->where('status',1)->where('company_id',$company_id)->pluck('rolename','role_id');
        $zone =Location1::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $region =Location2::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $state =Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        // dd($details);
        return view('reports.score-card.index',
            [
                'state' => $state,
                'role' => $role,
                'user' => $user,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'zone'=> $zone,
                'region'=> $region,
            ]);
    }
    #...........................score card ends here.............................#

     #...........................score card starts here.............................#
    public function score_card_new()
    {
        $company_id = Auth::user()->company_id;
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        
        
        $role = DB::table('_role')->where('status',1)->where('company_id',$company_id)->pluck('rolename','role_id');
        $zone =Location1::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $region =Location2::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $state =Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        // dd($details);
        return view('reports.score-card-new.index',
            [
                'state' => $state,
                'role' => $role,
                'user' => $user,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'zone'=> $zone,
                'region'=> $region,
            ]);
    }
    #...........................score card ends here.............................#

    #...........................user_monthy starts here.............................#
    public function user_monthy()
    {
        $company_id = Auth::user()->company_id;
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        
        $role = DB::table('_role')->where('status',1)->where('company_id',$company_id)->pluck('rolename','role_id');
      
        $zone =Location1::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $region =Location2::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $state =Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        // dd($details);
        return view('reports.user-monthly-report.index',
            [
                'state' => $state,
                'role' => $role,
                'user' => $user,
                'town'=> $town,
                'beat'=> $beat,
                'zone'=> $zone,
                'region'=> $region,
                'area'=> $area,
                'head_quater'=> $head_quater,
            ]);
    }
    #...........................user_monthy ends here.............................#

    public function advance_summary()
    {
        $company_id = Auth::user()->company_id;
        $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.advance_summary_report.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=> $area,
                'town'=> $town,
                'beat'=> $beat,
                'head_quater'=> $head_quater,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    #dsr monthly starts here 

    public function dsrMonthly()
    {
        $company_id = Auth::user()->company_id;
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name','id');
        $region = Location2::where('status', 1)->where('company_id',$company_id)->pluck('name','id');
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name','id');

        $catalog_2 = DB::table('catalog_2')->where('status','=','1')->where('company_id',$company_id)->pluck('name','id');



        
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $role = _role::where('company_id',$company_id)->pluck('rolename', 'role_id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.dsr-monthly.index',
            [
                'role' => $role,
                'state' => $state,
                'users' => $user,
                'zone'=>$zone,
                'region'=>$region,
                'area'=>$area,
                'catalog_2'=>$catalog_2,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }
    public function dsrMonthlyCases()
    {
        $company_id = Auth::user()->company_id;
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name','id');
        $region = Location2::where('status', 1)->where('company_id',$company_id)->pluck('name','id');
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name','id');

        $catalog_2 = DB::table('catalog_2')->where('status','=','1')->where('company_id',$company_id)->pluck('name','id');



        
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $role = _role::where('company_id',$company_id)->pluck('rolename', 'role_id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.dsr-monthly.CaseIndex',
            [
                'role' => $role,
                'state' => $state,
                'users' => $user,
                'zone'=>$zone,
                'region'=>$region,
                'area'=>$area,
                'catalog_2'=>$catalog_2,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    #dsr monthly ends here 

    #rds_wise_sale starts here 

    public function rds_wise_sale()
    {
        $company_id = Auth::user()->company_id;
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name','id');
        $region = Location2::where('status', 1)->where('company_id',$company_id)->pluck('name','id');
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name','id');

        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $role = _role::where('company_id',$company_id)->pluck('rolename', 'role_id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.rds_wise_sale.index',
            [
                'role' => $role,
                'state' => $state,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'users' => $user,
                'zone'=> $zone,
                'region'=> $region,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }
    #rds_wise_sale ends here 

    ###############Merchandise##########
    public function merchandiseOrder()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')->where('company_id',$company_id)->pluck('name', 'uid');
        return view('reports.merchandise_sale_order.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function retailerStock()
    {
        $company_id = Auth::user()->company_id;
       $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
       $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
     //   $town = Location4::where('status', 1)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

       $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
       $user = DB::table('person')->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')->where('person.company_id',$company_id)->pluck('name', 'uid');
       return view('reports.retailerStockReport.index',
           [
               'region' => $region,
               'state' => $state,
               'town' => $town,
               'users' => $user,
               'distributor' => $distributor,
               'menu' => $this->menu,
               'area'=> $area,
               'head_quater'=> $head_quater,
               'beat'=> $beat,
               'current_menu' => $this->current
           ]);
    }

     public function userMeetingOrder()
    {
        $company_id = Auth::user()->company_id;
       $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
       $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
     //   $town = Location4::where('status', 1)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

       $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
       $user = DB::table('person')->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')->where('person.company_id',$company_id)->pluck('name', 'uid');
       return view('reports.userMeetingOrderReport.index',
           [
               'region' => $region,
               'state' => $state,
               'town' => $town,
               'users' => $user,
               'distributor' => $distributor,
               'menu' => $this->menu,
               'area'=> $area,
               'head_quater'=> $head_quater,
               'beat'=> $beat,
               'current_menu' => $this->current
           ]);
    }
    #..............................................Daily Tracking Report Starts here .......................#
    public function dailyTracking()
    {
        $company_id = Auth::user()->company_id;
        Session::forget('juniordata');
        $login_user=Auth::user()->id;
        $role_id=Auth::user()->role_id;
        $is_admin=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50 || $is_admin = 1)
        {
            $datasenior='';
        }
        else
        { 
            $datasenior_call=self::getJuniorUser($login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior))
            {
                $datasenior[]=$login_user;
            }
        }   
        // dd($company_id);
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $city = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user_data = DB::table('person')->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                ->where('company_id',$company_id);
                if(!empty($datasenior))
                {
                    $user_data->whereIn('person.id',$datasenior);
                }
        $user = $user_data->pluck('name', 'uid');
        return view('reports.daily-tracking.index',
            [
                'city' => $city,
                'state' => $state,
                'users' => $user,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'beat'=> $beat,
                'region'=> $region,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }
    public function dailyTrackingTest()
    {
        $company_id = Auth::user()->company_id;
        Session::forget('juniordata');
        $login_user=Auth::user()->id;
        $role_id=Auth::user()->role_id;
        $is_admin=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50 || $is_admin = 1)
        {
            $datasenior='';
        }
        else
        { 
            $datasenior_call=self::getJuniorUser($login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior))
            {
                $datasenior[]=$login_user;
            }
        }   
        // dd($company_id);
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $city = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user_data = DB::table('person')->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                ->where('company_id',$company_id);
                if(!empty($datasenior))
                {
                    $user_data->whereIn('person.id',$datasenior);
                }
        $user = $user_data->pluck('name', 'uid');
        return view('reports.daily-tracking.kbzIndex',
            [
                'city' => $city,
                'state' => $state,
                'users' => $user,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'beat'=> $beat,
                'region'=> $region,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    #..............................................Daily Tracking Report Ends here .......................#
    #....................................................no attendance reports starts here ...............................................................##
    public function noAttendance()
    {   
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->where('person_login.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.no-attendance.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=>$area,
                'head_quater'=>$head_quater,
                'town'=>$town,
                'beat'=>$beat,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }
    #...................................................................no booking starts here .......................................................##
    public function noBooking()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->where('person_login.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.no-booking.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=>$area,
                'head_quater'=>$head_quater,
                'town'=>$town,
                'beat'=>$beat,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }
    #......................................................................user dealer info starts here..........................................................................##
    public function userDealerInfo()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $city = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->where('person_login.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.user_dealer_info.index',
            [
                'region' => $region,
                'city' => $city,
                'area'=>$area,
                'head_quater'=>$head_quater,
                'beat'=>$beat,
                'state' => $state,
                'users' => $user,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }
    #......................................................................senior herirchacy function starts here .............................................................##

    public function seniorInfo()
    {
        $company_id = Auth::user()->company_id;
        $state = Location3::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $region = Location2::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $role = _role::where('status', 1)->where('company_id',$company_id)->pluck('rolename', 'role_id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->where('person_login.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.senior_info.index',
            [
                'role' => $role,
                'state' => $state,
                'users' => $user,
                'zone'=> $zone,
                'region'=> $region,
                'town'=> $town,
                'beat'=> $beat,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }
    # ................................................................................get junior for particular user starts here ............................##
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
    #....................................................junior function ends here ...........................................................>###
    public function product_tracker(Request $request)
    {
        $company_id = Auth::user()->company_id;

        $role_id = Auth::user()->role_id;
        $is_admin = Auth::user()->is_admin;
        if($role_id==1 || $role_id==50 || $is_admin == 1 )
        {
            $datasenior='';
        }
        else
        { 
            
            Session::forget('juniordata');
            $login_user=Auth::user()->id;
            $datasenior_call=self::getJuniorUser($login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior))
            {
                $datasenior[]=$login_user;
            }
        }
        $dealer_id_array = array();
        // dd($datasenior);    
        if(!empty($datasenior))
        {
            $dealer_id_array = DB::table('dealer_location_rate_list')
                        ->whereIn('user_id',$datasenior)
                        ->groupBy('dealer_id')
                        ->pluck('dealer_id');            
        }

        $dealer_query = DB::table('dealer')
                ->where('dealer_status',1)
                ->where('company_id',$company_id);
                if(!empty($dealer_id_array))
                {
                    $dealer_query->whereIn('id',$dealer_id_array);
                }
        $dealer = $dealer_query->pluck('name','id');

        $product = DB::table('catalog_0')
                ->where('status',1)
                ->where('company_id',$company_id)
                ->pluck('name','id');
        
        return view('reports.product_tracker.index',
        [
            'product' => $product,
            'dealer' => $dealer,
           
        ]);

    }


     #..............................................Modern Report start here ...............................#
      public function userSalesModern()
    {
        $company_id = Auth::user()->company_id;
        Session::forget('juniordata');
        $login_user=Auth::user()->id;
        $role_id=Auth::user()->role_id;
        $is_admin=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50 || $is_admin = 1)
        {
            $datasenior='';
        }
        else
        { 
            $datasenior_call=self::getJuniorUser($login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior))
            {
                $datasenior[]=$login_user;
            }
        }   


        $user_data =  DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"),'person.id AS user_id')
        ->where('person.company_id',$company_id);
         if(!empty($datasenior))
                {
                    $user_data->whereIn('person.id',$datasenior);
                }
        $user =   $user_data->pluck('user_name', 'user_id');

        $region = DB::table('location_view')->where('l2_company_id',$company_id)->pluck('l2_name', 'l2_id');
        $area = DB::table('location_view')->where('l3_company_id',$company_id)->pluck('l3_name', 'l3_id');
        $product= DB::table('catalog_view')
             ->where('c0_company_id',$company_id)
             ->pluck('product_name', 'product_id');
        // dd($details);
        return view('reports.user_sale_report_modern.index',
            [
                'region' => $region,
                'area' => $area,
                'user' => $user,
                'product'=>$product,
                'menu' => $this->menu,
                'current_menu' => $this->current,
               
            ]);
    }

     public function merchandiseVisit()
    {

        $company_id = Auth::user()->company_id;
        Session::forget('juniordata');
        $login_user=Auth::user()->id;
        $role_id=Auth::user()->role_id;
        $is_admin=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50 || $is_admin = 1)
        {
            $datasenior='';
        }
        else
        { 
            $datasenior_call=self::getJuniorUser($login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior))
            {
                $datasenior[]=$login_user;
            }
        }   


        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user_data = DB::table('person')
                ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                ->where('person.company_id',$company_id);
             if(!empty($datasenior))
                    {
                        $user_data->whereIn('person.id',$datasenior);
                    }
        $user =  $user_data->pluck('name', 'uid');
        return view('reports.merchandise-visit.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

     public function supervisorVisit()
    {
         $company_id = Auth::user()->company_id;
        Session::forget('juniordata');
        $login_user=Auth::user()->id;
        $role_id=Auth::user()->role_id;
        $is_admin=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50 || $is_admin = 1)
        {
            $datasenior='';
        }
        else
        { 
            $datasenior_call=self::getJuniorUser($login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior))
            {
                $datasenior[]=$login_user;
            }
        }   


        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user_data = DB::table('person')
                ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                ->where('person.company_id',$company_id);
             if(!empty($datasenior))
                    {
                        $user_data->whereIn('person.id',$datasenior);
                    }
        $user = $user_data->pluck('name', 'uid');
        return view('reports.supervisor-visit.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function retailerCaptureImages()
    {
         $company_id = Auth::user()->company_id;
        Session::forget('juniordata');
        $login_user=Auth::user()->id;
        $role_id=Auth::user()->role_id;
        $is_admin=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50 || $is_admin = 1)
        {
            $datasenior='';
        }
        else
        { 
            $datasenior_call=self::getJuniorUser($login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior))
            {
                $datasenior[]=$login_user;
            }
        }   

        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user_data = DB::table('person')
                ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                ->where('person.company_id',$company_id);
             if(!empty($datasenior))
                    {
                        $user_data->whereIn('person.id',$datasenior);
                    }
        $user = $user_data->pluck('name', 'uid');
        return view('reports.retailer_capture_images.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function coverageCaptureImages()
    {
         $company_id = Auth::user()->company_id;
        Session::forget('juniordata');
        $login_user=Auth::user()->id;
        $role_id=Auth::user()->role_id;
        $is_admin=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50 || $is_admin = 1)
        {
            $datasenior='';
        }
        else
        { 
            $datasenior_call=self::getJuniorUser($login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior))
            {
                $datasenior[]=$login_user;
            }
        }   

        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user_data = DB::table('person')->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                ->join('person_login','person_login.person_id','=','person.id')
                ->where('person_status',1)
                ->where('person.company_id',$company_id);
                  if(!empty($datasenior))
                    {
                        $user_data->whereIn('person.id',$datasenior);
                    }
        $user = $user_data->pluck('name', 'uid');
        return view('reports.coverage_capture_images.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }
    // public function 
    #..............................................Modern Report ends here ...............................#
    public function dealer_counter_sale()
    {
        $company_id = Auth::user()->company_id;
        $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $location2 = Location2::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.dealer_counter_sale_report.index',
            [
                'region' => $region,
                'state' => $state,
                'location2' => $location2,
                'users' => $user,
                'area'=> $area,
                'town'=> $town,
                'beat'=> $beat,
                'head_quater'=> $head_quater,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function dms_complaint_details()
    {
        $company_id = Auth::user()->company_id;
        $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $location2 = Location2::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.dms_dealer_complaint_report.index',
            [
                'region' => $region,
                'state' => $state,
                'location2' => $location2,
                'users' => $user,
                'area'=> $area,
                'town'=> $town,
                'beat'=> $beat,
                'head_quater'=> $head_quater,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function fullifillment_order(Request $request)
    {
        $company_id = Auth::user()->company_id;
        // $division = Division::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_7 = Location7::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_6 = Location6::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_5 = Location5::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_4 = Location4::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_3 = Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $zone = Location2::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $dealer = DB::table('dealer')->where('company_id',$company_id)->where('dealer_status',1)->pluck('name','id');
        $retailer = DB::table('retailer')->where('company_id',$company_id)->where('retailer_status',1)->pluck('name','id');
        $user = DB::table('person')->join('person_login','person_login.person_id','=','person.id')
                ->where('person_status',1)
                ->where('person.company_id',$company_id)
                ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id');

        return view('reports.fullifillment_order.index',
        [
            'location_3'=> $location_3,
            'location_4'=> $location_4,
            'location_5'=> $location_5,
            'location_6'=> $location_6,
            'location_7'=> $location_7,
            'retailer' => $retailer,
            'dealer' => $dealer,
            'zone' => $zone,
            'user'=> $user,
           
        ]);

    }

    public function common_fullifillment_order(Request $request)
    {
        $company_id = Auth::user()->company_id;
        // $division = Division::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_7 = Location7::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_6 = Location6::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_5 = Location5::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_4 = Location4::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_3 = Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $zone = Location2::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $dealer = DB::table('dealer')->where('company_id',$company_id)->where('dealer_status',1)->pluck('name','id');
        $retailer = DB::table('retailer')->where('company_id',$company_id)->where('retailer_status',1)->pluck('name','id');
        $user = DB::table('person')->join('person_login','person_login.person_id','=','person.id')
                ->where('person_status',1)
                ->where('person.company_id',$company_id)
                ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id');

        return view('reports.fullifillment_order.commonindex',
        [
            'location_3'=> $location_3,
            'location_4'=> $location_4,
            'location_5'=> $location_5,
            'location_6'=> $location_6,
            'location_7'=> $location_7,
            'retailer' => $retailer,
            'dealer' => $dealer,
            'zone' => $zone,
            'user'=> $user,
           
        ]);

    }

    public function salesManSecondarySales()
    {
        $company_id = Auth::user()->company_id;
        $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.salesManSecondarySalesReport.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=>$area,
                'head_quater'=>$head_quater,
                'town'=>$town,
                'beat'=>$beat,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }


    public function dealerWiseSS()
    {
        $company_id = Auth::user()->company_id;
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        
        
        $role = DB::table('_role')->where('status',1)->where('company_id',$company_id)->pluck('rolename','role_id');
        $zone =Location1::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $region =Location2::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $state =Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $dealer = DB::table('dealer')->where('company_id',$company_id)->where('dealer_status',1)->pluck('name','id');
        $csa = DB::table('csa')->where('company_id',$company_id)->where('active_status',1)->pluck('csa_name','c_id');

        // dd($details);
        return view('reports.dealerWiseSSReport.index',
            [
                'state' => $state,
                'role' => $role,
                'user' => $user,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'zone'=> $zone,
                'region'=> $region,
                'dealer'=> $dealer,
                'csa'=> $csa,
            ]);
    }


    public function userSalesBtw()
    {
        $company_id = Auth::user()->company_id;
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person.company_id',$company_id)
        ->where('person_login.person_status','=','1')
        ->pluck('name', 'uid');
        
        $region = DB::table('location_3')->where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = DB::table('user_sales_order_view')->where('company_id',$company_id)->pluck('l3_name', 'l3_id');
        $product= DB::table('catalog_product')
             ->where('status',1)
             ->where('company_id',$company_id)
             ->groupBy('id')
             ->pluck('name', 'id');
        // dd($details);
        return view('reports.user_sale_report.Btwindex',
            [
                'region' => $region,
                'area' => $area,
                'user' => $user,
                'product'=>$product,
                'menu' => $this->menu,
                'current_menu' => $this->current,
               
            ]);
    }

    public function dailyAttendanceBtw()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')->where('company_id',$company_id)->pluck('name', 'uid');
        
        return view('reports.daily-attendance.btwindex',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }


    ############################################## for target Reports #############################################################
    public function superStockistSkuMonthlyTargetDetails()
    {
        $company_id = Auth::user()->company_id;
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person.company_id',$company_id)
        ->where('person_login.person_status','=','1')
        ->pluck('name', 'uid');
        
        $region = DB::table('location_3')->where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = DB::table('user_sales_order_view')->where('company_id',$company_id)->pluck('l3_name', 'l3_id');
        $product= DB::table('catalog_product')
            ->where('company_id',$company_id)
             ->groupBy('id')->pluck('name', 'id');
        // dd($details);
        return view('reports.super_stockist_sku_month_target_report_details.index',
            [
                'region' => $region,
                'area' => $area,
                'user' => $user,
                'product'=>$product,
                'menu' => $this->menu,
                'current_menu' => $this->current,
               
            ]);
    }


       public function distributorSkuMonthlyTargetDetails()
    {
        $company_id = Auth::user()->company_id;
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person.company_id',$company_id)
        ->where('person_login.person_status','=','1')
        ->pluck('name', 'uid');
        
        $region = DB::table('location_3')->where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = DB::table('user_sales_order_view')->where('company_id',$company_id)->pluck('l3_name', 'l3_id');
        $product= DB::table('catalog_product')
            ->where('company_id',$company_id)
             ->groupBy('id')->pluck('name', 'id');
        // dd($details);
        return view('reports.distributor_sku_month_target_report_details.index',
            [
                'region' => $region,
                'area' => $area,
                'user' => $user,
                'product'=>$product,
                'menu' => $this->menu,
                'current_menu' => $this->current,
               
            ]);
    }

    public function ssMonthly()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')->where('company_id',$company_id)->pluck('name', 'uid');
        return view('reports.ss-monthly.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=>$area,
                'head_quater'=>$head_quater,
                'town'=>$town,
                'beat'=>$beat,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }


     public function distributorMonthly()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')->where('company_id',$company_id)->pluck('name', 'uid');
        return view('reports.distributor-monthly.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=>$area,
                'head_quater'=>$head_quater,
                'town'=>$town,
                'beat'=>$beat,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function target_ss()
    {
        $company_id = Auth::user()->company_id;
        $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $location2 = Location2::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.target_ss_report.index',
            [
                'region' => $region,
                'state' => $state,
                'location2' => $location2,
                'users' => $user,
                'area'=> $area,
                'town'=> $town,
                'beat'=> $beat,
                'head_quater'=> $head_quater,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

    public function target_db()
    {
        $company_id = Auth::user()->company_id;
        $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $location2 = Location2::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.target_db_report.index',
            [
                'region' => $region,
                'state' => $state,
                'location2' => $location2,
                'users' => $user,
                'area'=> $area,
                'town'=> $town,
                'beat'=> $beat,
                'head_quater'=> $head_quater,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }
    public function distributorTargetReport()
    {
        $company_id = Auth::user()->company_id;
        // $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        // $location2 = Location2::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $location3 = Location3::where('status', 1)->where('company_id',$company_id)->orderBy('name','ASC')->pluck('name', 'id');
        $location4 = Location4::where('status', 1)->where('company_id',$company_id)->orderBy('name','ASC')->pluck('name', 'id');
        $location5 = Location5::where('status', 1)->where('company_id',$company_id)->orderBy('name','ASC')->pluck('name', 'id');
        $location6 = Location6::where('status', 1)->where('company_id',$company_id)->orderBy('name','ASC')->pluck('name', 'id');
        // $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $dealer = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->orderBy('name','ASC')->pluck('name', 'id');
        $catalog_2 = Catalog2::where('status', 1)->where('company_id',$company_id)->orderBy('name','ASC')->pluck('name', 'id');
        $catalog_product = CatalogProduct::where('status', 1)->where('company_id',$company_id)->orderBy('name','ASC')->pluck('name', 'id');
        // $user = DB::table('person')
        // ->join('person_login','person_login.person_id','=','person.id')
        // ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        // ->where('person_login.person_status','=','1')
        // ->where('person.company_id',$company_id)
        // ->pluck('name', 'uid');
        return view('reports.target_db_report.gurujiIndex',
            [
                'location3' => $location3,
                'location4' => $location4,
                'location5' => $location5,
                'location6' => $location6,
                'dealer' => $dealer,
                'catalog_2' => $catalog_2,
                'catalog_product'=> $catalog_product,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }
    ############################################## for target Reports ends #############################################################

    public function dms_new_calling(Request $request)
    {
        $company_id = Auth::user()->company_id;
        // $division = Division::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_7 = Location7::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_6 = Location6::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_5 = Location5::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_4 = Location4::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_3 = Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $zone = Location2::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $dealer = DB::table('dealer')->where('company_id',$company_id)->where('dealer_status',1)->pluck('name','id');
        $retailer = DB::table('retailer')->where('company_id',$company_id)->where('retailer_status',1)->pluck('name','id');
        $user = DB::table('person')->join('person_login','person_login.person_id','=','person.id')
                ->where('person_status',1)
                ->where('person.company_id',$company_id)
                ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id');

        return view('reports.dms_new_calling.index',
        [
            'location_3'=> $location_3,
            'location_4'=> $location_4,
            'location_5'=> $location_5,
            'location_6'=> $location_6,
            'location_7'=> $location_7,
            'retailer' => $retailer,
            'dealer' => $dealer,
            'zone' => $zone,
            'user'=> $user,
           
        ]);

    }


    public function dms_order_enquiry(Request $request)
    {
        $company_id = Auth::user()->company_id;
        // $division = Division::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_7 = Location7::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_6 = Location6::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_5 = Location5::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_4 = Location4::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_3 = Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $zone = Location2::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $dealer = DB::table('dealer')->where('company_id',$company_id)->where('dealer_status',1)->pluck('name','id');
        $retailer = DB::table('retailer')->where('company_id',$company_id)->where('retailer_status',1)->pluck('name','id');
        $user = DB::table('person')->join('person_login','person_login.person_id','=','person.id')
                ->where('person_status',1)
                ->where('person.company_id',$company_id)
                ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id');

        return view('reports.dms_order_enquiry.index',
        [
            'location_3'=> $location_3,
            'location_4'=> $location_4,
            'location_5'=> $location_5,
            'location_6'=> $location_6,
            'location_7'=> $location_7,
            'retailer' => $retailer,
            'dealer' => $dealer,
            'zone' => $zone,
            'user'=> $user,
           
        ]);

    }
    public function dms_dealer_details_for_document(Request $request)
    {
        $company_id = Auth::user()->company_id;
        // $division = Division::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_7 = Location7::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_6 = Location6::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_5 = Location5::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_4 = Location4::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $location_3 = Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $zone = Location2::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $dealer = DB::table('dealer')->where('company_id',$company_id)->where('dealer_status',1)->pluck('name','id');
        $retailer = DB::table('retailer')->where('company_id',$company_id)->where('retailer_status',1)->pluck('name','id');
        $user = DB::table('person')->join('person_login','person_login.person_id','=','person.id')
                ->where('person_status',1)
                ->where('person.company_id',$company_id)
                ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id');

        return view('DmsDealerDocument.index',
        [
            'location_3'=> $location_3,
            'location_4'=> $location_4,
            'location_5'=> $location_5,
            'location_6'=> $location_6,
            'location_7'=> $location_7,
            'retailer' => $retailer,
            'dealer' => $dealer,
            'zone' => $zone,
            'user'=> $user,
           
        ]);

    }

    public function dailyAttendanceOyster()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')->where('company_id',$company_id)->pluck('name', 'uid');
        
        return view('reports.daily-attendance.oysterindex',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }

     public function finalStock()
    {
        $company_id = Auth::user()->company_id;
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        
        
        $role = DB::table('_role')->where('status',1)->where('company_id',$company_id)->pluck('rolename','role_id');
        $zone =Location1::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $region =Location2::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $state =Location3::where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $dealer = DB::table('dealer')->where('company_id',$company_id)->where('dealer_status',1)->pluck('name','id');
        $csa = DB::table('csa')->where('company_id',$company_id)->where('active_status',1)->pluck('csa_name','c_id');

        // dd($details);
        return view('reports.finalStockReport.index',
            [
                'state' => $state,
                'role' => $role,
                'user' => $user,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'zone'=> $zone,
                'region'=> $region,
                'dealer'=> $dealer,
                'csa'=> $csa,
            ]);
    }


     public function dailyAttendancePatanjali()
    {
        $company_id = Auth::user()->company_id;
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->orderBy('name', 'asc')->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')->select(DB::raw('CONCAT_WS(" ",person.first_name,person.middle_name,person.last_name) as name'), 'person.id as uid')->where('company_id',$company_id)->pluck('name', 'uid');
        
        return view('reports.dailyAttendancePatanjaliReport.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }


       public function salesTeamAttendanceSummaryPatanajali()
        {
            $company_id = Auth::user()->company_id;
            $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
            $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            $user = DB::table('person')
            ->join('person_login','person_login.person_id','=','person.id')
            ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
            ->where('person_login.person_status','=','1')
            ->where('person.company_id',$company_id)
            ->pluck('name', 'uid');
            return view('reports.salesTeamAttendanceSummaryPatanajaliReport.index',
                [
                    'region' => $region,
                    'state' => $state,
                    'users' => $user,
                    'area'=>$area,
                    'head_quater'=>$head_quater,
                    'town'=>$town,
                    'beat'=>$beat,
                    'distributor' => $distributor,
                    'menu' => $this->menu,
                    'current_menu' => $this->current
                ]);
        }


           public function userAttendanceTimePatanajali()
        {
            $company_id = Auth::user()->company_id;
            $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
            $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
            $user = DB::table('person')
            ->join('person_login','person_login.person_id','=','person.id')
            ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
            ->where('person_login.person_status','=','1')
            ->where('person.company_id',$company_id)
            ->pluck('name', 'uid');
            return view('reports.userAttendanceTimePatanajaliReport.index',
                [
                    'region' => $region,
                    'state' => $state,
                    'users' => $user,
                    'area'=>$area,
                    'head_quater'=>$head_quater,
                    'town'=>$town,
                    'beat'=>$beat,
                    'distributor' => $distributor,
                    'menu' => $this->menu,
                    'current_menu' => $this->current
                ]);
        }


        public function dailyAttendanceEdit()
    {
        $company_id = Auth::user()->company_id;
        $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.dailyAttendanceEditReport.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=>$area,
                'head_quater'=>$head_quater,
                'town'=>$town,
                'beat'=>$beat,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }


    public function dsrMonthlyNeha()
    {
        $company_id = Auth::user()->company_id;
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name','id');
        $region = Location2::where('status', 1)->where('company_id',$company_id)->pluck('name','id');
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name','id');

        $catalog_2 = DB::table('catalog_2')->where('status','=','1')->where('company_id',$company_id)->pluck('name','id');



        
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $role = _role::where('company_id',$company_id)->pluck('rolename', 'role_id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.dsrMonthlyNeha.index',
            [
                'role' => $role,
                'state' => $state,
                'users' => $user,
                'zone'=>$zone,
                'region'=>$region,
                'area'=>$area,
                'catalog_2'=>$catalog_2,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }


    public function dailyTrackingNeha()
    {
        $company_id = Auth::user()->company_id;
        Session::forget('juniordata');
        $login_user=Auth::user()->id;
        $role_id=Auth::user()->role_id;
        $is_admin=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50 || $is_admin = 1)
        {
            $datasenior='';
        }
        else
        { 
            $datasenior_call=self::getJuniorUser($login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior))
            {
                $datasenior[]=$login_user;
            }
        }   
        // dd($company_id);
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $city = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user_data = DB::table('person')->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                ->where('company_id',$company_id);
                if(!empty($datasenior))
                {
                    $user_data->whereIn('person.id',$datasenior);
                }
        $user = $user_data->pluck('name', 'uid');
        return view('reports.daily-tracking-neha.index',
            [
                'city' => $city,
                'state' => $state,
                'users' => $user,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'beat'=> $beat,
                'region'=> $region,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }



      public function dailyTrackingLog()
    {
        $company_id = Auth::user()->company_id;
        Session::forget('juniordata');
        $login_user=Auth::user()->id;
        $role_id=Auth::user()->role_id;
        $is_admin=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50 || $is_admin = 1)
        {
            $datasenior='';
        }
        else
        { 
            $datasenior_call=self::getJuniorUser($login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior))
            {
                $datasenior[]=$login_user;
            }
        }   
        // dd($company_id);
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $city = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user_data = DB::table('person')->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                ->where('company_id',$company_id);
                if(!empty($datasenior))
                {
                    $user_data->whereIn('person.id',$datasenior);
                }
        $user = $user_data->pluck('name', 'uid');
        return view('reports.daily-tracking.indexlog',
            [
                'city' => $city,
                'state' => $state,
                'users' => $user,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'beat'=> $beat,
                'region'=> $region,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }



      public function userSalesSummaryRajdhani()
    {
        $company_id = Auth::user()->company_id;
        $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.userSalesSummaryRajdhani.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=>$area,
                'head_quater'=>$head_quater,
                'town'=>$town,
                'beat'=>$beat,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }




    public function dsrMonthlyForNeha()
    {
        $company_id = Auth::user()->company_id;
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name','id');
        $region = Location2::where('status', 1)->where('company_id',$company_id)->pluck('name','id');
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name','id');

        $catalog_2 = DB::table('catalog_2')->where('status','=','1')->where('company_id',$company_id)->pluck('name','id');



        
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $role = _role::where('company_id',$company_id)->pluck('rolename', 'role_id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.dsrMonthlyForNehaReport.index',
            [
                'role' => $role,
                'state' => $state,
                'users' => $user,
                'zone'=>$zone,
                'region'=>$region,
                'area'=>$area,
                'catalog_2'=>$catalog_2,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }




      public function unbilledOutlet()
    {
        $company_id = Auth::user()->company_id;
        $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.unbilledOutletReport.index',
            [
                'region' => $region,
                'state' => $state,
                'users' => $user,
                'area'=>$area,
                'head_quater'=>$head_quater,
                'town'=>$town,
                'beat'=>$beat,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }




     public function userPrimarySales(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $region = $request->region;
        $town = $request->town;
        $distributor = $request->distributor;
        $beat = $request->beat;
        $user_id = $request->user_id;
        $product=$request->product;
        $call_status = $request->call_status;
        // $explodeDate = explode(" -", $request->date_range_picker);
        // $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        // $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        $from_date = !empty($request->from_date)?$request->from_date:'';
        $to_date = !empty($request->to_date)?$request->to_date:'';
        $arr = [];
        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',6)->first();

        if(empty($from_date) && empty($to_date)){
            $status = '1';
        }else{
            $status = '0';
        }


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
                       ->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date, '%Y-%m-%d')>='$from_date' and DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d') <='$to_date'")
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
        if(!empty($datasenior))
        {
            $query_data->whereIn('user_id',$datasenior);
        }
        if(!empty($user_id))
        {   
            $query_data->whereIn('user_id',$user_id);
        }
        if(!empty($region))
        {
            $query_data->whereIn('l3_id',$region);
        }
        if(!empty($town))
        {
            $query_data->whereIn('l6_id',$town);
        }
        if(!empty($distributor))
        {
            $query_data->whereIn('dealer_id',$distributor);
        }
        if(!empty($beat))
        {
            $query_data->whereIn('location_id',$beat);
        }
        if(!empty($call_status))
        {
            $query_data->whereIn('call_status',$call_status);
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
            $query_data->whereIn('user_sales_order.dealer_id', $dealer);
        }
        if (!empty($request->user)) 
        {
            $user = $request->user;
            $query_data->whereIn('user_id', $user);
        }
        if (!empty($request->role)) 
        {
            $role = $request->role;
            $query_data->whereIn('person.role_id', $role);
        }
        if($status == '1'){
        $query_data->groupBy('user_primary_sales_order.order_id')
        ->orderBy('user_primary_sales_order.janak_order_sequence','DESC')
        ->take('50');
        }else{
        $query_data->whereRaw("DATE_FORMAT(user_primary_sales_order.sale_date, '%Y-%m-%d')>='$from_date' and DATE_FORMAT(user_primary_sales_order.sale_date,'%Y-%m-%d') <='$to_date'")
        ->groupBy('user_primary_sales_order.order_id')
        ->orderBy('user_primary_sales_order.janak_order_sequence','DESC');
        }
        $query = $query_data->get();
        $current_datre = date('Y-m-d');
        $datearray = array();
        $startTime = strtotime($from_date);
        $endTime = strtotime($to_date);
        for ($currentDate = $startTime; $currentDate <= $endTime; $currentDate += (86400)) 
        {                                       
            $Store = date('Y-m-d', $currentDate); 
            $datearray[] = $Store; 
        }
        $product_percentage = array();
        foreach ($datearray as $key => $value) 
        {
            $product_percentage_data = DB::table('product_wise_scheme_plan_details')
                            ->where('incentive_type',1)
                            ->where('company_id',$company_id)
                            ->whereRaw("DATE_FORMAT(valid_from_date,'%Y-%m-%d' )<= '$value' AND DATE_FORMAT(valid_to_date,'%Y-%m-%d')>='$value'")
                            // ->select()
                            ->get();

            foreach ($product_percentage_data as $keyi => $valuei) 
            {
                $ans = $valuei->product_id.$valuei->state_id.$value;
                $product_percentage[$ans] = $valuei->value_amount_percentage;
            }
        }
        $non_productive_reason_name = DB::table("_no_sale_reason")->where('company_id',$company_id)->where('status',1)->groupBy('id')->pluck('name','id');
        $out=array();
        $proout=array();
       if (!empty($query)) 
       {
            foreach ($query as $k => $d) 
            {
                $uid=$d->order_id;
           
                $proout = DB::table('user_primary_sales_order_details')
                            ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                            ->select('catalog_product.name','catalog_product.weight','user_primary_sales_order_details.rate','user_primary_sales_order_details.final_secondary_qty as cases','user_primary_sales_order_details.final_secondary_qty as cases','user_primary_sales_order_details.pcs as pcs','user_primary_sales_order_details.final_secondary_rate as pr_rate','user_primary_sales_order_details.order_id')
                            ->where('user_primary_sales_order_details.company_id',$company_id)
                            ->where('order_id', $uid);

                
               if(!empty($product))
               {
                    $proout->whereIn('product_id',$product);
               }
                $out[$uid]=$proout->groupBy('user_primary_sales_order_details.id')->groupBy('order_id','product_id')->get(); 
            }
        }
        $productWeight = DB::table('catalog_product')
                    ->where('company_id',$company_id)
                    ->pluck('weight','id');



        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person.company_id',$company_id)
        ->where('person_login.person_status','=','1')
        ->pluck('name', 'uid');
        
        $region = DB::table('location_3')->where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = DB::table('location_6')->where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = DB::table('location_3')->where('company_id',$company_id)->pluck('name', 'id');

        $beat = DB::table('location_7')->where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');


        $distributor = DB::table('dealer')->where('company_id',$company_id)->pluck('name', 'id');

        $product= DB::table('catalog_product')
             ->where('status',1)
             ->where('company_id',$company_id)
             ->groupBy('id')
             ->pluck('name', 'id');
        // dd($query);
        return view('reports.userPrimarySalesReport.index',
            [
                'region' => $region,
                'area' => $area,
                'user' => $user,
                'product'=>$product,
                'town'=>$town,
                'distributor'=>$distributor,
                'beat'=>$beat,
                'menu' => $this->menu,
                'current_menu' => $this->current,
                'records' => $query,
                'order_detial_arr' => $out,
                'non_productive_reason_name'=>$non_productive_reason_name,
                'product_percentage'=> $product_percentage,
                'productWeight'=> $productWeight,
                'company_id'=> $company_id,
               
            ]);
    }
    

    public function beatWiseSale()
    {
        $company_id = Auth::user()->company_id;
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name','id');
        $region = Location2::where('status', 1)->where('company_id',$company_id)->pluck('name','id');
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name','id');

        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $role = _role::where('company_id',$company_id)->pluck('rolename', 'role_id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.beatWiseSaleReport.index',
            [
                'role' => $role,
                'state' => $state,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'users' => $user,
                'zone'=> $zone,
                'region'=> $region,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }


     public function hitkaryBeatWiseSale()
    {
        $company_id = Auth::user()->company_id;
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name','id');
        $region = Location2::where('status', 1)->where('company_id',$company_id)->pluck('name','id');
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name','id');

        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $role = _role::where('company_id',$company_id)->pluck('rolename', 'role_id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.hitkaryBeatWiseSaleReport.index',
            [
                'role' => $role,
                'state' => $state,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'users' => $user,
                'zone'=> $zone,
                'region'=> $region,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }



     public function hitkaryRetailerWiseSale()
    {
        $company_id = Auth::user()->company_id;
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name','id');
        $region = Location2::where('status', 1)->where('company_id',$company_id)->pluck('name','id');
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name','id');

        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $role = _role::where('company_id',$company_id)->pluck('rolename', 'role_id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.hitkaryRetailerWiseSaleReport.index',
            [
                'role' => $role,
                'state' => $state,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'users' => $user,
                'zone'=> $zone,
                'region'=> $region,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }


     public function hitkaryUserWiseSale()
    {
        $company_id = Auth::user()->company_id;
        $zone = Location1::where('status', 1)->where('company_id',$company_id)->pluck('name','id');
        $region = Location2::where('status', 1)->where('company_id',$company_id)->pluck('name','id');
        $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name','id');

        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $role = _role::where('company_id',$company_id)->pluck('rolename', 'role_id');
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person_login.person_status','=','1')
        ->where('person.company_id',$company_id)
        ->pluck('name', 'uid');
        return view('reports.hitkaryUserWiseSaleReport.index',
            [
                'role' => $role,
                'state' => $state,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'town'=> $town,
                'beat'=> $beat,
                'users' => $user,
                'zone'=> $zone,
                'region'=> $region,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }


       public function distributorAssign()
              {
                  $company_id = Auth::user()->company_id;
                  $region = Location1::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
                  $state = Location3::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
                  $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
                  $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
                  $town = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
                  $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
                  $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
                  $user = DB::table('person')
                  ->join('person_login','person_login.person_id','=','person.id')
                  ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                  ->where('person_login.person_status','=','1')
                  ->where('person.company_id',$company_id)
                  ->pluck('name', 'uid');
                  return view('reports.distributorAssignReport.index',
                      [
                          'region' => $region,
                          'state' => $state,
                          'users' => $user,
                          'area'=>$area,
                          'head_quater'=>$head_quater,
                          'town'=>$town,
                          'beat'=>$beat,
                          'distributor' => $distributor,
                          'menu' => $this->menu,
                          'current_menu' => $this->current
                      ]);
              }



    public function skuWiseCounterSale()
    {
        $company_id = Auth::user()->company_id;
        $user = DB::table('person')
        ->join('person_login','person_login.person_id','=','person.id')
        ->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
        ->where('person.company_id',$company_id)
        ->where('person_login.person_status','=','1')
        ->pluck('name', 'uid');
        
        $region = DB::table('location_3')->where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $town = DB::table('location_6')->where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $area = DB::table('location_3')->where('company_id',$company_id)->pluck('name', 'id');

        $beat = DB::table('location_7')->where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');


        $distributor = DB::table('dealer')->where('company_id',$company_id)->pluck('name', 'id');

        $product= DB::table('catalog_product')
             ->where('status',1)
             ->where('company_id',$company_id)
             ->groupBy('id')
             ->pluck('name', 'id');
        // dd($details);
        return view('reports.skuWiseCounterSaleReport.index',
            [
                'region' => $region,
                'area' => $area,
                'user' => $user,
                'product'=>$product,
                'town'=>$town,
                'distributor'=>$distributor,
                'beat'=>$beat,
                'menu' => $this->menu,
                'current_menu' => $this->current,
               
            ]);
    }

     public function dailyTrackingKoyas()
    {
        $company_id = Auth::user()->company_id;
        Session::forget('juniordata');
        $login_user=Auth::user()->id;
        $role_id=Auth::user()->role_id;
        $is_admin=Auth::user()->is_admin;
        if($role_id==1 || $role_id==50 || $is_admin = 1)
        {
            $datasenior='';
        }
        else
        { 
            $datasenior_call=self::getJuniorUser($login_user);
            $datasenior = $request->session()->get('juniordata');
            if(empty($datasenior))
            {
                $datasenior[]=$login_user;
            }
        }   
        // dd($company_id);
        $region = Location2::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $state = Location3::where('status', 1)->where('company_id',$company_id)->select('name', 'id')->get();
        $area = Location4::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $head_quater = Location5::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $city = Location6::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $beat = Location7::where('status', 1)->where('company_id',$company_id)->pluck('name', 'id');

        $distributor = Dealer::where('dealer_status', 1)->where('company_id',$company_id)->pluck('name', 'id');
        $user_data = DB::table('person')->select(DB::raw('CONCAT(person.first_name," ",person.last_name) as name'), 'person.id as uid')
                ->where('company_id',$company_id);
                if(!empty($datasenior))
                {
                    $user_data->whereIn('person.id',$datasenior);
                }
        $user = $user_data->pluck('name', 'uid');
        return view('reports.daily-tracking-koyas.index',
            [
                'city' => $city,
                'state' => $state,
                'users' => $user,
                'area'=> $area,
                'head_quater'=> $head_quater,
                'beat'=> $beat,
                'region'=> $region,
                'distributor' => $distributor,
                'menu' => $this->menu,
                'current_menu' => $this->current
            ]);
    }


}
