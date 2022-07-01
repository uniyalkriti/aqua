<?php
namespace App\Http\Controllers;
use App\UserDetail;
use Illuminate\Http\Request;
use Auth;
use DB; 
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class RetailerStockController extends Controller
{

	public function show(Request $request,$id)
    {
            // dd('f');\
            $zone = $request->region;
            $region = $request->area;
            $distributor = $request->distributor;
            $user = $request->user;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $retailer = Crypt::decryptString($id);
            $company_id = Auth::user()->company_id;
            
            $data1=DB::table('retailer_stock')
              ->join('retailer','retailer.id','retailer_stock.retailer_id')
            
              ->join('person','person.id','retailer_stock.user_id')
              ->join('dealer','dealer.id','retailer_stock.dealer_id')    
              ->join('_role','_role.role_id','person.role_id')
              ->leftjoin('location_view','location_view.l7_id','retailer_stock.location_id')
              ->select('person.id as user_id','dealer.id as dealer_id','retailer.id as retailer_id','retailer_stock.order_id','retailer.name as rname','retailer_stock.id as id','retailer_stock.user_id as user_id','retailer_stock.dealer_id as dealer_id', 'l3_name as state', 'l6_name as town',DB::raw("DATE_FORMAT(retailer_stock.date,'%d-%m-%Y') AS stock_date"),'person.mobile as mobile',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name"), 'person.role_id', '_role.rolename as role_name','dealer.name as dealer_name','person_id_senior')
             ->whereRaw("DATE_FORMAT(retailer_stock.date,'%Y-%m-%d') >='$from_date' and DATE_FORMAT(retailer_stock.date,'%Y-%m-%d') <='$to_date'")
             ->where('retailer.id',$retailer)
             ->where('retailer.company_id',$company_id)
             ->where('retailer_stock.company_id',$company_id)
             ->groupBy('retailer_stock.order_id') ;


            #Region filter
            if (!empty($request->region)) {
                $region = $request->region;
                $data1->whereIn('l2_id', $region);
            }
            #State filter
            if (!empty($request->area)) {
                $area = $request->area;
                $data1->whereIn('l3_id', $area);
            }
            #User filter
            if (!empty($request->user)) {
                $user = $request->user;
                $data1->whereIn('user_id', $user);
            }
            $user_record = $data1->get();
            // dd($data1);
            $dsr=[];
            foreach ($user_record as $key => $value) {
             $id = $value->id;
             $orderid = $value->order_id;
                $dsr[$orderid]['date']=$value->stock_date;                                
                $dsr[$orderid]['rname']=$value->rname;                                
                $dsr[$orderid]['state']=$value->state;
                $dsr[$orderid]['town']=$value->town;
                $dsr[$orderid]['user_name']=$value->user_name;
                $dsr[$orderid]['role_name']=$value->role_name;
                $dsr[$orderid]['mobile']=$value->mobile;
                $dsr[$orderid]['person_id_senior']=$value->person_id_senior;
                $dsr[$orderid]['seniorname']=DB::table('person')->select(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) AS seniorname"))->where('id',$value->person_id_senior)->first();
                $dsr[$orderid]['dealer_name']=$value->dealer_name;
      
                $proout = DB::table('retailer_stock_details')
                    ->leftJoin('catalog_product','catalog_product.id','=','retailer_stock_details.product_id')
                    ->where('order_id', $orderid)
                    ->where('retailer_stock_details.company_id',$company_id)

                    ->select('retailer_stock_details.quantity as pieces','catalog_product.name as product_name','catalog_product.base_price_per as mrp','catalog_product.base_price as base_price',DB::raw("(catalog_product.base_price_per*retailer_stock_details.quantity) as total_sale_value"));

                  $dsr[$orderid]=$proout->get(); 

            }

          //  dd($dsr);
            return view('reports.retailer_dashboard.retailer_stock', [
                'records' => $user_record,
                'dsr' => $dsr,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'id'=>$id,
            ]);


    }
}