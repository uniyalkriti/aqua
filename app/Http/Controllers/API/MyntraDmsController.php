<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Person;
use App\Company;
use App\Location7;
use App\JuniorData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Validator;
use DB;
use Image;

class MyntraDmsController extends Controller
{
	

	public function submitOpeningStock(Request $request)
	{
		$validator=Validator::make($request->all(),[
          'user_id' => 'required',
          'company_id' => 'required',
          'dealer_id'=> 'required',
          'date'=> 'required',
          'details'=> 'required',
       ]);
       // return response()->json(['response'=>True,'Post'=>$_POST,'message'=>'POST DATA'],200);
       if($validator->fails()){
        return response()->json(['response'=>FALSE,'Error'=>$validator->errors(),'message'=>'Validation Error! Please try again..'],200);
       }

       $user_id = $request->user_id;
       $company_id = $request->company_id;
       $dealer_id = $request->dealer_id;
       $date = $request->date;
       $details = $request->details;


       foreach ($details as $key => $value) {
       	$product_id = $value->product_id;
       	$rate = $value->rate;
       	$quantity = $value->quantity;
       	$mfg = $value->mfg;


       	$openingInsertArray = [
		       		'person_id' => $user_id,
		       		'dealer_id' => $dealer_id,
		       		'company_id' => $company_id,
		       		'product_id' => $product_id,
		       		'rate' => $rate,
		       		'qty' => $quantity,
		       		'mfg' => $mfg,
		       		'date' => $date,
		       	];

	    $insertOpening = DB::table('opening_stocks')->insert($openingInsertArray);


       	$check_stock = DB::table('stock')
       					->where('product_id',$product_id)
       					->where('dealer_id',$dealer_id)
       					->first();

	       	if(COUNT($check)<=0)
			{
					$stockInsertArray = [
			       		'person_id' => $user_id,
			       		'dealer_id' => $dealer_id,
			       		'company_id' => $company_id,
			       		'product_id' => $product_id,
			       		'rate' => $rate,
			       		'qty' => $quantity,
			       		'mfg' => $mfg,
			       		'date' => $date,
			       	];

		   		$insertStock = DB::table('stock')->insert($stockInsertArray);  	
			}
			else{
				$updation_qty = $quantity+$check_stock->quantity;
				$stockUpdateArray = [	
			       		'qty' => $updation_qty,
			       	];

		   		$updateStock = DB::table('stock')->insert($stockUpdateArray);  
			}

       }

        if($insertOpening)
		{
			return response()->json([ 'response' =>True,'message'=>'SuccessFully Inserted']);		
		}
		else
		{
			return response()->json([ 'response' =>False,'message'=>'Not Inserted']);		
		}


       
	}




	public function myntraCounterSale(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'company_id'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
          
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],401);
        }
        $user_id = $request->user_id;
        $company_id = $request->company_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        Session::forget('juniordata');      
        $check_junior_data=JuniorData::getJuniorUser($user_id,$company_id);
        Session::push('juniordata', $user_id);

        $junior_data_check = Session::get('juniordata');



        $saleData = DB::table('counter_sale_summary')
        			->join('counter_sale_details','counter_sale_details.order_id','=','counter_sale_summary.order_id')
        			->join('catalog_product','catalog_product.id','=','counter_sale_details.product_id')
        			->select('counter_sale_details.order_id','counter_sale_details.product_id','catalog_product.name as product_name','counter_sale_details.pcs as quantity','counter_sale_details.pcs_rate as rate','counter_sale_details.pcs as quantity1','catalog_product.weight as weight',DB::raw("pcs_rate*pcs as total_sale_value"))
        			->whereRaw("DATE_FORMAT(counter_sale_summary.sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(counter_sale_summary.sale_date,'%Y-%m-%d')<='$to_date'")
                	// ->where('counter_sale_summary.created_by_person',$junior_data_check)
                	->where('counter_sale_summary.company_id',$company_id)
                	->where('counter_sale_details.company_id',$company_id)
                	->groupBy('counter_sale_details.order_id','counter_sale_details.product_id')
                	->get()->toArray();



         $outArray = array();
		foreach($saleData as $sdkey => $sdval){

			$uid = $sdval->order_id;

			$outArray[$uid]['prodVal'][] = $sdval;

			$outArray[$uid]['finalValue'][] = $sdval->quantity*$sdval->rate;


		}

                	// dd($outArray);

        			



        $counterSale = DB::table('counter_sale_summary')
        				->join('retailer','retailer.id','=','counter_sale_summary.retailer_id')
        				->join('person','person.id','=','counter_sale_summary.created_by_person')
        				->select('retailer.id as retailer_id','retailer.name as retailer_name','person.id as user_id',DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'counter_sale_summary.order_id','counter_sale_summary.sale_date as date')
                    	->whereRaw("DATE_FORMAT(counter_sale_summary.sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(counter_sale_summary.sale_date,'%Y-%m-%d')<='$to_date'")
                    	->where('counter_sale_summary.created_by_person',$junior_data_check)
                    	->where('counter_sale_summary.company_id',$company_id)
                    	->where('retailer.company_id',$company_id)
                    	->where('person.company_id',$company_id)
                    	->groupBy('counter_sale_summary.order_id')
                    	->get()->toArray();

        // dd($counterSale);

        $final_array = array();
        foreach ($counterSale as $jtdkey => $jtdvalue) {

            $userId = !empty($jtdvalue->user_id)?$jtdvalue->user_id:'';
            $user_name = !empty($jtdvalue->user_name)?$jtdvalue->user_name:'';
            $retailer_id = !empty($jtdvalue->retailer_id)?$jtdvalue->retailer_id:'';
            $retailer_name = !empty($jtdvalue->retailer_name)?$jtdvalue->retailer_name:'';
            $order_id = !empty($jtdvalue->order_id)?$jtdvalue->order_id:'';
            $date = !empty($jtdvalue->date)?$jtdvalue->date:'';


          


            $out['user_id'] = $userId;
            $out['user_name'] = $user_name;
            $out['retailer_id'] = $retailer_id;
            $out['dealer_name'] = $retailer_name;
            $out['date'] = $date;
            $out['order_id'] = $order_id;
            $out['total_sale_value'] = !empty($outArray[$order_id]['finalValue'])?array_sum($outArray[$order_id]['finalValue']):array();

           

            $out['product_details'] = !empty($outArray[$order_id]['prodVal'])?$outArray[$order_id]['prodVal']:array();



            $final_array[] = $out;
            
        }


		// dd($final_array);


        if(!empty($final_array)){
            return response()->json([ 'response' =>TRUE,'result'=>$final_array]);

        }else{
            return response()->json([ 'response' =>FALSE,'result'=>array()]);

        }

    }

}