<?php
 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location1;
use App\Location2;
use App\Location3;
use App\Location4;
use App\Location5;
use App\Person;
use App\Dealer;
use App\Retailer;
use App\SecondarySale;
use App\UserSalesOrder;
use DateTime;
use Illuminate\Support\Facades\Session;
use DB;
use Auth;
use Crypt;


class CatalogDashboardController extends Controller
{
    public function __construct()
    {
        $this->current_menu = 'catalogdashboard';
        $this->current_dir  = 'catalogdashboard';
     }
    // #....................main function for dropdown and for table  ........................

    public function index(Request $request)
    {   
        $company_id = Auth::user()->company_id;
            if(isset($request->date_range_picker))
            {
                $ReturnDate = $request->date_range_picker;
                $explodeDate = explode(" -", $request->date_range_picker);
                $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
                $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            }
            else
            {
                $from_date = date('Y-m-d');
                $to_date = date('Y-m-d');
                $ReturnDate = $from_date.' - '.$to_date;
            }
//  dd($ReturnDate);
            $CatalogName = array();
            $totalOrderValue = array();
            $final_array = array();

               $color_code = DB::table('catalog_2')
                    ->where('company_id',$company_id)
                     ->pluck('color_code','id');


        $catalog1Sale = DB::table('user_sales_order_view')
                        ->join('user_sales_order_details_view','user_sales_order_details_view.order_id','=','user_sales_order_view.order_id')
                        ->select('c2_name as label',DB::raw("SUM(rate*quantity) as data"),'c2_id')
                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(date,'%Y-%m-%d') <='$to_date'")
                        ->where('user_sales_order_view.company_id',$company_id)
                        ->where('c1_id','!=',0)
                        ->groupBy('c2_id')
                        ->get();

         // $catalog1Sale = SecondarySale::select('c2_name as label',DB::raw("SUM(rate*quantity) as data"),'c1_color_code as color')
         // ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(date,'%Y-%m-%d') <='$to_date'")
         // ->where('company_id',$company_id)
         // ->where('c1_id','!=',0)
         // ->groupBy('c2_id')->get();

         // $catalog1SaleLegend = SecondarySale::select('c2_name as label',DB::raw("SUM(rate*quantity) as data"))
         // ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(date,'%Y-%m-%d') <='$to_date'")
         // ->where('company_id',$company_id)
         // ->where('c1_id','!=',0)
         // ->groupBy('c2_id')->get();

        //  $out[] = 'Product';
        //  $out[] = 'Sales Figure';

        // $final_array[] = $out;


        //  foreach($catalog1SaleLegend as $ckey=>$cVal)
        // {
        //     $out = array();

        //     $out[] = $cVal->label;
        //     $out[] = (int)$cVal->data;

        // $final_array[] = $out;


        // }


        // dd(json_encode($final_array));


         

foreach($catalog1Sale as $ckey=>$cVal)
{
    $totalOrderValue[]=$cVal->data;
    $CatalogName[]=$cVal->label;

    $out = array();
    $out[] = $cVal->label;
    $out[] = (int)$cVal->data;
    $final_array[] = $out;
}

        return view($this->current_dir.'.index',
            [
                'menu' => $this->current_dir,
                'catalog1Sale'=>$catalog1Sale,
                'CatalogName'=>$CatalogName,
                'totalOrderValue' => $totalOrderValue,
                'ReturnDate' => $ReturnDate,
                'from_date' => $from_date,
                'catalog1SaleLegend' => $final_array,
                 'to_date' => $to_date,
                'color_code' => $color_code,
  
            ]);
  
    }


}
 