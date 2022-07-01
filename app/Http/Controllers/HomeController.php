<?php

namespace App\Http\Controllers;

use App\_module;
use App\_subModule;
use App\Person;
use Illuminate\Http\Request;
use App\Dealer;
use App\Retailer;
use DB;
use DateTime;
use Auth;
use Session;
use App\UserSalesOrder;
use App\SecondarySale;
use App\ChallanOrder;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->menu = _module::orderBy('module_sequence')->get()->load('submenu');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $check_dashboard = Session::get('dashboard');// this variable get the session values 
        // dd($check_dashboard[0]['is_set']);
        $current_menu='DASHBOARD ';
        $user=Auth::user();

        if($user->role_id==1 || $user->role_id==50)
        {
            if($check_dashboard[0]['is_set'] && empty($request->year))  //This condition is check if the data were in session or not
            {
                $dashboard = $check_dashboard[0];
                return view('home',
                [
                    'menu' => $this->menu,
                    'current_menu' => $current_menu,
                    'totalSalesTeam' => $dashboard['totalSalesTeam'],
                    'totalDistributor'=>$dashboard['totalDistributor'],
                    'totalOutlet'=>$dashboard['totalOutlet'],
                    'roleWiseTeam'=>$dashboard['roleWiseTeam'],
                    'catalog1Sale'=>$dashboard['catalog1Sale'],
                    'datesArr'=>$dashboard['datesArr'],
                    'totalOrderValue'=>$dashboard['totalOrderValue'],
                    'totalChallanValue'=>$dashboard['totalChallanValue'],
                    'totalAttd'=>$dashboard['totalAttd'],
                    'totalOrder'=>$dashboard['totalOrder'],
                    'totalPrimaryOrder'=>$dashboard['totalPrimaryOrder'],
                    'mdate' =>$dashboard['mdate'],
                    'location_5'=>$dashboard['location_5'],
                    'totalDistributorSale'=>$dashboard['totalDistributorSale'],
                    'totalOutletSale' =>$dashboard['totalOutletSale'],
                    'totalBeatSale' => $dashboard['totalBeatSale'],
                    'totalCall' => $dashboard['totalCall'],
                    'productiveCall' => $dashboard['productiveCall']
                ]);
            }
             // seesion check end here     
            $cdate=date('Y-m-d');
            if(isset($request->year))
            {
                $mdate = $request->year;
            }
            else
            {
                $mdate=date('Y-m');
            }
        
        // USER DETAILS
        $totalSalesTeam=Person::join('person_login','person_login.person_id','=','person.id')->where('person_status',1)->count();
        $totalAttd=DB::table('user_daily_attendance')->whereRaw("DATE_FORMAT(work_date,'%Y-%m-%d')='$cdate'")->count();
        if(empty($totalAttd))
        $totalAttd=0;
        // END OF USER DETAILE
        // SS DETAILS
        // END OF SS DETAILS
        // DISTRIBUTOR DETAILS
        $totalDistributor=Dealer::where('dealer_status',1)->count();
        $totalDistributorSale=DB::table('user_sales_order')
        ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m')='$mdate'")
        ->select(DB::raw('count(DISTINCT dealer_id) as dealersale'))->first();
        // END OF DISTRIBUTOR DETAILS
        // OUTLET DETAILS
        $totalOutlet=Retailer::where('retailer_status',1)->count();
        $totalOutletSale=DB::table('user_sales_order')
        ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m')='$mdate'")
        ->select(DB::raw('count(DISTINCT retailer_id) as outletsale'))->first();
        // END OF OUTLET DETAILS
        // BEAT DETAILS
        $location_5 = DB::table('location_5')->where('status',1)->count();
        $totalBeatSale=DB::table('user_sales_order')
        ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m')='$mdate'")
        ->select(DB::raw('count(DISTINCT location_id) as beatsale'))->first();
       // END OF BEAT DETAILS
        // CALL
        $totalCall=DB::table('user_sales_order')
        ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m')='$mdate'")
        ->select(DB::raw('count(call_status) as total_call'))->first();
        $productiveCall=DB::table('user_sales_order')
        ->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m')='$mdate'")
        ->select(DB::raw('count(order_id) as productive_call'))->where('call_status',1)->first();
        // END CALL
        // SALE START
        $totalOrder=DB::table('user_sales_order')->whereRaw("DATE_FORMAT(date,'%Y-%m')='$mdate'")->select(DB::raw("sum(total_sale_value) AS total_sale_value"))->first();
        if(empty($totalOrder))
            $totalOrder=0;
        $totalPrimaryOrder=DB::table('primary_sale_view')->whereRaw("DATE_FORMAT(sale_date,'%Y-%m')='$mdate'")->select(DB::raw("sum((rate*pcs)+(cases*pr_rate)) AS total_sale_value"))->first();
        if(empty($totalPrimaryOrder))
            $totalPrimaryOrder=0; 
        // SALE END
        $roleWiseTeam=Person::join('person_login','person_login.person_id','=','person.id','inner')
        ->join('_role','_role.role_id','=','person.role_id','inner')
        ->select('person.role_id','_role.rolename',DB::raw("count(person.id) as count"))
        ->where('person_status',1)->groupBy('person.role_id')->orderBY('rolename','ASC')->get();
       
        // $catalog1Sale=UserSalesOrder::join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id','inner')
        // ->join('catalog_view','catalog_view.product_id','=','user_sales_order_details.product_id','inner')
        // ->select('c1_id','c1_name',DB::raw("SUM(user_sales_order_details.rate*quantity) as sale"))
        // ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$mdate'")
        // ->groupBy('c1_id')->orderBY('c1_name','ASC')->get();

        $catalog1Sale = SecondarySale::select('color_code','c1_name',DB::raw("SUM(rate*quantity) as sale"))
        ->whereRaw("DATE_FORMAT(date,'%Y-%m')='$mdate'")
        ->groupBy('c1_id')->orderBY('c1_name','ASC')->get();
       
        // $catalog1challan=ChallanOrder::join('challan_order_details','challan_order_details.ch_id','=','challan_order.id','inner')
        // ->join('catalog_view','catalog_view.product_id','=','challan_order_details.product_id','inner')
        // ->select('c1_id','c1_name',DB::raw("SUM(challan_order_details.taxable_amt) as challan_value"))
        // ->groupBy('c1_id')->orderBY('c1_name','ASC')->get();
        $month=substr($mdate,5);
        $year=substr($mdate,0,4);
        $monthNum  = $month;
        $dateObj   = DateTime::createFromFormat('!m', $monthNum);
        $monthName = $dateObj->format('F'); 
        for($i = 1; $i <=  date('t'); $i++)
        {
            // add the date to the dates array
            $datesArr[] = $year . "-" . $month . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
            $datesDisplayArr[] =  $monthName . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        $saleOrderValue = DB::table('user_sales_order')->groupBy('date')->pluck(DB::raw("SUM(total_sale_value)"),'date');
        foreach($datesArr as $dkey=>$dateVal)
        {
            // $totalChallanValue[]=ChallanOrder::where(DB::raw("(DATE_FORMAT(ch_date,'%Y-%m-%d'))"), "=", ''.$dateVal.'')->sum('amount');
            $totalChallanValue[]=0;

            $totalOrderValue1[]=!empty($saleOrderValue[$dateVal])?$saleOrderValue[$dateVal]:'';
            
        }
        $totalOrderValue  = array();
        $totalOrderValue = array_map('round',$totalOrderValue1);

        // $dashboard_arr this array push the dashboard values in session
        $dashboard_arr=array('is_set'=>1,
        'totalSalesTeam' => $totalSalesTeam,
        'totalDistributor'=>$totalDistributor,
        'totalOutlet'=>$totalOutlet,
        'roleWiseTeam'=>$roleWiseTeam,
        'catalog1Sale'=>$catalog1Sale,
        'datesArr'=>$datesDisplayArr,
        'totalOrderValue'=>$totalOrderValue,
        'totalChallanValue'=>$totalChallanValue,
        'totalAttd'=>$totalAttd,
        'totalOrder'=>$totalOrder,
        'totalPrimaryOrder'=>$totalPrimaryOrder,
        'mdate' =>$mdate,
        'location_5'=>$location_5,
        'totalDistributorSale'=>$totalDistributorSale,
        'totalOutletSale' =>$totalOutletSale,
        'totalBeatSale' => $totalBeatSale,
        'totalCall' => $totalCall,
        'productiveCall' => $productiveCall);
        Session::push('dashboard', $dashboard_arr);
        // $dashboard_arr array ends here

        return view('home',
            [
                'menu' => $this->menu,
                'current_menu' => $current_menu,
                'totalSalesTeam' => $totalSalesTeam,
                'totalDistributor'=>$totalDistributor,
                'totalOutlet'=>$totalOutlet,
                'roleWiseTeam'=>$roleWiseTeam,
                'catalog1Sale'=>$catalog1Sale,
                // 'catalog1challan'=>$catalog1challan,
                'datesArr'=>$datesDisplayArr,
                'totalOrderValue'=>$totalOrderValue,
                'totalChallanValue'=>$totalChallanValue,
                'totalAttd'=>$totalAttd,
                'totalOrder'=>$totalOrder,
                'totalPrimaryOrder'=>$totalPrimaryOrder,
                'mdate' =>$mdate,
                'location_5'=>$location_5,
                'totalDistributorSale'=>$totalDistributorSale,
                'totalOutletSale' =>$totalOutletSale,
                'totalBeatSale' => $totalBeatSale,
                'totalCall' => $totalCall,
                'productiveCall' => $productiveCall
            ]);
  

        }else
        {
            return view('senior_home',[
                        'menu' => $this->menu,
                        'user'=>$user,
                        'current_menu' => $current_menu,
                        
            ]);
        }

    }
}
