<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Person;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Session;
use DateTime;

class FulfillmentController extends Controller
{
    public $successStatus = 200;

    public function dealer_fulfillment(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'dealer_id' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'company_id'=> 'required',
        ]);
		if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
    	$dealer_id = $request->dealer_id;
    	$from_date = $request->from_date;
    	$to_date = $request->to_date;
    	$company_id = $request->company_id;
    	$overall_data_query = DB::table('user_sales_order')
							->join('retailer','retailer.id','=','user_sales_order.retailer_id')
							->select('user_sales_order.order_id as order_id','user_sales_order.date as date','user_sales_order.retailer_id as retailer_id','retailer.name as retailer_name')
							->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')<='$to_date'")
							->where('user_sales_order.dealer_id',$dealer_id)
							->where('flag_fullfillment',1)
							->where('user_sales_order.company_id',$company_id)
							->where('retailer.company_id',$company_id)
							->groupBy('user_sales_order.retailer_id','user_sales_order.date')
							->get();
		// dd($overall_data_query);
		foreach ($overall_data_query as $key => $value) 
		{
			$retailer_id = $value->retailer_id;
			$date = $value->date;

			$data = DB::table('user_sales_order')
				->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
				->join('catalog_product','catalog_product.id','user_sales_order_details.product_id')
				->join('retailer','retailer.id','=','user_sales_order.retailer_id')
				->select('product_id','user_sales_order.date as date',DB::raw("SUM(rate*quantity) as total_sale_value"),"quantity","rate",'catalog_product.name as product_name','user_sales_order_details.order_id as order_id')
				->whereRaw("DATE_FORMAT(user_sales_order.date,'%Y-%m-%d')='$date'")
				->where('user_sales_order.retailer_id',$retailer_id)
				->where('user_sales_order.company_id',$company_id)
				->where('retailer.company_id',$company_id)
				->groupBy('product_id','user_sales_order.date')
				->get();
			$out[] = array(
				'retailer_name' => $value->retailer_name,
				'retailer_id' => $value->retailer_id,
				'order_id' => $value->order_id,
				'date' => $value->date,
				'details' => $data
				);

		}
		if(!empty($out))
		{
			return response()->json(['response' => TRUE,'date' =>$out, 'message' => 'Successfully found']);

		}
		else
		{
			return response()->json(['response' => FALSE,'date' =>'', 'message'=>'No record found!!']);

		}


    }
}