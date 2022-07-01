<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\UserDetail;
use App\Person;
use App\Location5;
use App\Location4;
use App\Location3;
use App\Location6;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Session;
use DateTime;

class RetailerDmsControllers extends Controller
{


	public function retailer_dealer_stock(Request $request)
	{
		$validator=Validator::make($request->all(),[
			"dealer_id"=>'required',
			"company_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
        }
        $company_id = $request->company_id;
        $retailer_id = $request->dealer_id;

        $details_array = DB::table('catalog_product')
    			->select('id as product_id','name as product_name')
    			->where('catalog_product.company_id',$company_id)
    			->get();

		$stock_cases = DB::table('retailer_stock')
					->join('retailer_stock_details','retailer_stock_details.order_id','=','retailer_stock.order_id')
					->where('retailer_id',$retailer_id)
					->where('retailer_stock.company_id',$company_id)
					->groupBy('product_id')
					->pluck(DB::raw("SUM(cases)"),'product_id');
		$stock_qty = DB::table('retailer_stock')
					->join('retailer_stock_details','retailer_stock_details.order_id','=','retailer_stock.order_id')
					->where('retailer_id',$retailer_id)
					->where('retailer_stock.company_id',$company_id)
					->groupBy('product_id')
					->pluck(DB::raw("SUM(quantity)"),'product_id');
		$finalArr = [];
		foreach ($details_array as $key => $value) 
		{
			$details['product_id'] = $value->product_id;
			$details['product_name'] = $value->product_name;
			$details['stock_qty'] = !empty($stock_qty[$value->product_id])?$stock_qty[$value->product_id]:'0';
			$details['stock_case'] = !empty($stock_cases[$value->product_id])?$stock_cases[$value->product_id]:'0';
			$details['dealer_id'] = $retailer_id;
			$finalArr[]	= $details;
		}

		return response()->json(['response' =>True,'message'=>'NotFound!!','data'=>$finalArr]);    

	}
	public function retailer_purchase_order_submit(Request $request)
	{
		$validator=Validator::make($request->all(),[
            "order_id"=>'required',
			"retailer_id"=>'required',
			"sale_date"=>'required',
			"created_date"=>'required',
			"date_time"=>'required',
			"battery_status"=>'required',
			"gps_status"=>'required',
			"lat"=>'required',
			"lng"=>'required',
			"address"=>'required',
			"mcc_mnc_lac_cellid"=>'required',
			"user_id"=>'required',
			"company_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
        }

		$order_id = $request->order_id;
		$retailer_id = $request->retailer_id;
		$sale_date = $request->sale_date;
		$created_date = $request->created_date;
		$date_time = $request->date_time;
		$battery_status = $request->battery_status;
		$gps_status = $request->gps_status;
		$lat = $request->lat;
		$lng = $request->lng;
		$address = $request->address;
		$mcc_mnc_lac_cellid = $request->mcc_mnc_lac_cellid;
		$user_id = $request->user_id;
		$company_id = $request->company_id;
		$primary_sale_summary = json_decode($request->primary_sale_summary);
		$insertDetailsArr = array();
		DB::beginTransaction();
		foreach ($primary_sale_summary as $key => $value) 
		{
			$check = DB::table('purchase_order')
					->where('order_id',$value->existing_order)
					->count();
					// ->update(['order_id'=>$value->order_id]);
			if($check>0)
			{
				// $delete 
				$delete_order = DB::table('purchase_order')->where('order_id',$value->order_id)->delete();

				$check = DB::table('purchase_order')
					->where('order_id',$value->existing_order)
					// ->count();
					->update(['order_id'=>$value->order_id]);

				$delete_details = DB::table('purchase_order_details')->where('order_id',$value->existing_order)->delete();

					$detailsArr = [
						'order_id' => $value->order_id,
						'id' => $value->order_id,
						'product_id' => $value->product_id,
						'rate' => $value->rate,
						'quantity' => $value->quantity,
						'barcode' => $value->Barcode,
						'scheme_qty' => $value->scheme_qty,
						'cases' => $value->case,
						'pcs' => $value->pcs,
						'total_value' => $value->value,
						'pr_rate' => $value->case_rate,
						'company_id'=>$company_id,
						'app_flag' => $value->app_flag,
					];
					$insertDetailsArr[] = $detailsArr;
			}
			else
			{
				$check = DB::table('purchase_order')
					->where('order_id',$value->order_id)
					->count();
					// ->update(['order_id'=>$value->order_id]);
				if($check>0)
				{
					$detailsArr = [
						'order_id' => $value->order_id,
						'id' => $value->order_id,
						'product_id' => $value->product_id,
						'rate' => $value->rate,
						'quantity' => $value->quantity,
						'barcode' => $value->Barcode,
						'scheme_qty' => $value->scheme_qty,
						'cases' => $value->case,
						'pcs' => $value->pcs,
						'total_value' => $value->value,
						'pr_rate' => $value->case_rate,
						'company_id'=>$company_id,
						'app_flag' => $value->app_flag,
					];
					$insertDetailsArr[] = $detailsArr;
				}
				else
				{
					$myArr = [
						'order_id'=>$value->order_id,
						'id' => $value->order_id,
						'retailer_id'=>$retailer_id,
						'created_person_id'=>0,
						'dealer_id'=>0,
						'sale_date'=>$sale_date,
						'created_date'=>$created_date,
						'receive_date'=>$created_date,
						'dispatch_date'=>$created_date,
						'date_time'=>$date_time,
						'battery_status'=>$battery_status,
						'gps_status'=>$gps_status,
						'lat'=>$lat,
						'lng'=>$lng,
						'address'=>$address,
						'mcc_mnc_lac_cellid'=>$mcc_mnc_lac_cellid,
						'company_id'=>$company_id,
					];
					$insert_order = DB::table('purchase_order')->insert($myArr);

					$detailsArr = [
						'order_id' => $value->order_id,
						'id' => $value->order_id,
						'product_id' => $value->product_id,
						'rate' => $value->rate,
						'quantity' => $value->quantity,
						'barcode' => $value->Barcode,
						'scheme_qty' => $value->scheme_qty,
						'cases' => $value->case,
						'pcs' => $value->pcs,
						'total_value' => $value->value,
						'pr_rate' => $value->case_rate,
						'company_id'=>$company_id,
						'app_flag' => $value->app_flag,
					];
					$insertDetailsArr[] = $detailsArr;
				}

			}

			
		}
		$insert_details = DB::table('purchase_order_details')->insert($insertDetailsArr);

		if($insert_details )
		{
			DB::commit();
        	return response()->json(['response' =>True,'message'=>'Successfully Submitted!!']);        

		}
		else
		{
			DB::rollback();
        	return response()->json(['response' =>False,'message'=>'Not Submitted!!']);        

		}
	}
	public function retailer_product_details(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'state_id' => 'required',
            'company_id'=>'required',
            'retailer_id'=>'required',
            'status'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
        }
        $state_id = $request->state_id;
        $company_id = $request->company_id;
        $retailer_id = $request->retailer_id;

        $product_array = DB::table('catalog_product')
                        ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                        ->select('catalog_2.*')
                        ->where('catalog_1.status',1)
                        ->where('catalog_2.status',1)
                        ->where('catalog_product.status',1)
                        ->where('state_id',$state_id)
                        ->where('catalog_product.company_id',$company_id)
                        ->groupBy('catalog_2.id')
                        ->get()->toArray();

        // $dms_status
        $final_array_details = [];
        foreach ($product_array as $key => $value) 
        {
        	$first_layer['id']=$value->id;
        	$first_layer['name']=$value->name;
        	$first_layer['catalog_product_details']=DB::table('catalog_product')
						                        ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
						                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
						                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
						                        ->select('catalog_product.image_name as image_name','product_id','state_id','ss_id','distributor_id','product_type_primary','other_retailer_rate','other_dealer_rate','quantiy_per_other_type','product_type_id','retailer_rate as retailer_case_rate','catalog_product.product_type as product_type','catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','catalog_product.quantity_per_case as quantity_per_case','unit','catalog_product.name as product_name','product_rate_list.retailer_pcs_rate as base_price','product_rate_list.mrp_pcs as mrp', 'product_rate_list.mrp_pcs','hsn_code','catalog_2.name as category_name', 'product_rate_list.dealer_rate as dealer_rate','product_rate_list.dealer_pcs_rate as dealer_pcs_rate')
						                        ->where('catalog_1.status',1)
						                        ->where('catalog_2.status',1)
						                        ->where('catalog_product.status',1)
						                        ->where('state_id',$state_id)
						                        ->where('catalog_id',$value->id)
						                        ->where('catalog_product.company_id',$company_id)
						                        ->groupBy('product_id')
						                        ->get()->toArray();
            $final_array_details[] = $first_layer;
    	}
    	$further_details = array();
    	// $scheme_details = DB::table('scheme_plan_details')
     //    				->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
     //    				->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id')
     //    				->where('scheme_plan_details.company_id',$company_id)
     //    				->where('sale_unit',2)
     //    				->where('incentive_type',3)
     //    				->get();
    	if($request->status == 1) // purchase order
    	{


    		$further_details = DB::table('purchase_order')
						->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
						->select('cases as cases','product_id','scheme_qty','purchase_order.order_id as order_id','quantity')
						->where('purchase_order.retailer_id',$retailer_id)
						->where('app_flag',1)
						->where('purchase_order.company_id',$company_id)
						->get();
		}
		elseif ($request->status == 2) 
		{
			$further_details = DB::table('counter_sale_summary')
						->join('counter_sale_details','counter_sale_details.order_id','=','counter_sale_summary.order_id')
						->select('cases','case_rate','counter_sale_summary.order_id','product_id','pcs as quantity')
						->where('app_flag',1)
						->where('counter_sale_summary.retailer_id',$retailer_id)
						->where('counter_sale_summary.company_id',$company_id)
						->get();
		}

        return response()->json(['response' =>True,'message'=>'!!Found!!','product_details'=>$final_array_details,'further_details'=>$further_details]);        

	}
	public function retailer_purchase_order_report_btw(Request $request)
	{
		$validator=Validator::make($request->all(),[
            "state_id"=>'required',
			"retailer_id"=>'required',
			"from_date"=>'required',
			"to_date"=>'required',
			// "company_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
        }

        $state_id = $request->state_id;
        $retailer_id = $request->retailer_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        // $company_id = $request->company_id;

       $data = DB::table('purchase_order')
       		->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
   			->join('retailer','retailer.id','=','purchase_order.retailer_id')
   			->select('purchase_order.order_id','sale_date','dms_order_reason_id','date_time')
   			->whereRaw("date_format(sale_date,'%Y-%m-%d')>='$from_date' AND date_format(sale_date,'%Y-%m-%d')<='$to_date' ")
   			// ->where('purchase_order.company_id',$company_id)
   			->where('purchase_order.retailer_id',$retailer_id)
   			->where('app_flag',2)
   			// ->where('purchase_order.order_id',$order_id)
   			->groupBy('purchase_order.order_id')
   			->get();
		if(!empty($data))
		{
			$dms_reason_data = DB::table('_dms_reason')->where('status',1)->pluck('name','id');
			$dms_reason_data_status_time = DB::table('_dms_reason')->where('status',1)->pluck('created_at','id');
			$finalArr = [];
			foreach ($data as $key => $value) 
			{
				$first_layer['order_id'] = $value->order_id;
				$first_layer['sale_date'] = $value->sale_date;
				$first_layer['supplier_name'] = 'BTW Pvt Lmt.';
				$first_layer['current_reason_status_id'] = $value->dms_order_reason_id;
				$first_layer['current_reason_status'] = !empty($dms_reason_data[$value->dms_order_reason_id])?$dms_reason_data[$value->dms_order_reason_id]:'Order Placed';
				$first_layer['reason_status_time'] = !empty($dms_reason_data[$value->dms_order_reason_id])?$dms_reason_data[$value->dms_order_reason_id]:$value->date_time;
				$first_layer['details'] = DB::table('purchase_order')
							       		->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
							       		->join ('catalog_product','catalog_product.id','=','purchase_order_details.product_id')
							   			->join('retailer','retailer.id','=','purchase_order.retailer_id')
							   			->select('catalog_product.image_name','product_id','catalog_product.name as product_name','rate','quantity','cases','pcs','pr_rate as case_rate')
							   			->whereRaw("date_format(sale_date,'%Y-%m-%d')>='$from_date' AND date_format(sale_date,'%Y-%m-%d')<='$to_date'")
							   			// ->where('purchase_order.company_id',$company_id)
							   			->where('purchase_order.retailer_id',$retailer_id)
							   			->where('purchase_order.order_id',$value->order_id)
							   			->groupBy('purchase_order_details.id','product_id')
							   			->get();

	   			$first_layer['reason_log'] = DB::table('dms_order_reason_log')
	   										->join('_dms_reason','_dms_reason.id','=','dms_order_reason_log.dms_reason_id')
	   										->select('_dms_reason.name as status_name','order_id','dms_order_reason_log.id')
	   										->where('order_id',$value->order_id)
	   										// ->where('dms_order_reason_log.company_id',$company_id)
	   										->get();
				$finalArr[] = $first_layer; 
			}	



    		return response()->json(['response' =>True,'message'=>'Found!!','data'=>$finalArr]);        
    	}
    	else
    	{
    		return response()->json(['response' =>False,'message'=>'NotFound!!','data'=>array()]);        
    	}
	}
	public function retailer_order_to_dealer_btw(Request $request)
	{
		$validator=Validator::make($request->all(),[
            // "state_id"=>'required',
			"dealer_id"=>'required',
			"from_date"=>'required',
			"to_date"=>'required',
			"company_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
        }

        // $state_id = $request->state_id;
        $dealer_id = $request->dealer_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $company_id = $request->company_id;

       $data = DB::table('purchase_order')
       		->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
   			->join('retailer','retailer.id','=','purchase_order.retailer_id')
   			// ->join('dealer','dealer.id','=','retailer.dealer_id')
   			->select('retailer.other_numbers','retailer.address','retailer.name','purchase_order.order_id','sale_date','dms_order_reason_id','date_time')
   			->whereRaw("date_format(sale_date,'%Y-%m-%d')>='$from_date' AND date_format(sale_date,'%Y-%m-%d')<='$to_date' ")
   			->where('purchase_order.company_id',$company_id)
   			->where('retailer.dealer_id',$dealer_id)
   			->where('app_flag',2)
   			// ->where('purchase_order.order_id',$order_id)
   			->groupBy('purchase_order.order_id')
   			->get();
		if(!empty($data))
		{
			$dms_reason_data = DB::table('_dms_reason')->where('status',1)->pluck('name','id');
			$dms_reason_data_status_time = DB::table('_dms_reason')->where('status',1)->pluck('created_at','id');
			$finalArr = [];
			foreach ($data as $key => $value) 
			{
				$first_layer['order_id'] = $value->order_id;
				$first_layer['sale_date'] = $value->sale_date;
				$first_layer['supplier_name'] = 'BTW Pvt Lmt.';
				$first_layer['retailer_name'] = $value->name;
				$first_layer['retailer_address'] = $value->address;
				$first_layer['retailer_number'] = $value->other_numbers;
				$first_layer['current_reason_status_id'] = $value->dms_order_reason_id;
				$first_layer['current_reason_status'] = !empty($dms_reason_data[$value->dms_order_reason_id])?$dms_reason_data[$value->dms_order_reason_id]:'Order Placed';
				$first_layer['reason_status_time'] = !empty($dms_reason_data[$value->dms_order_reason_id])?$dms_reason_data[$value->dms_order_reason_id]:$value->date_time;
				$first_layer['details'] = DB::table('purchase_order')
							       		->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
							       		->join ('catalog_product','catalog_product.id','=','purchase_order_details.product_id')
							   			->join('retailer','retailer.id','=','purchase_order.retailer_id')
   										// ->join('dealer','dealer.id','=','retailer.dealer_id')
							   			->select('catalog_product.image_name','product_id','catalog_product.name as product_name','rate','quantity','cases','pcs','pr_rate as case_rate')
							   			->whereRaw("date_format(sale_date,'%Y-%m-%d')>='$from_date' AND date_format(sale_date,'%Y-%m-%d')<='$to_date'")
							   			->where('purchase_order.company_id',$company_id)
							   			->where('retailer.dealer_id',$dealer_id)
							   			->where('purchase_order.order_id',$value->order_id)
							   			->groupBy('purchase_order_details.id','product_id')
							   			->get();

	   			$first_layer['reason_log'] = DB::table('dms_order_reason_log')
	   										->join('_dms_reason','_dms_reason.id','=','dms_order_reason_log.dms_reason_id')
	   										->select('_dms_reason.name as status_name','order_id','dms_order_reason_log.id')
	   										->where('order_id',$value->order_id)
	   										->where('dms_order_reason_log.company_id',$company_id)
	   										->get();
				$finalArr[] = $first_layer; 
			}	



    		return response()->json(['response' =>True,'message'=>'Found!!','data'=>$finalArr]);        
    	}
    	else
    	{
    		return response()->json(['response' =>False,'message'=>'NotFound!!','data'=>array()]);        
    	}
	}
	public function retailer_counter_sale_submit(Request $request)
	{
		$validator=Validator::make($request->all(),[
            "order_id"=>'required',
			"retailer_id"=>'required',
			"sale_date"=>'required',
			"created_date"=>'required',
			"date_time"=>'required',
			"battery_status"=>'required',
			"gps_status"=>'required',
			"lat"=>'required',
			"lng"=>'required',
			"address"=>'required',
			"mcc_mnc_lac_cellid"=>'required',
			"user_id"=>'required',
			"company_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
        }

		$order_id = $request->order_id;
		$retailer_id = $request->retailer_id;
		$sale_date = $request->sale_date;
		$created_date = $request->created_date;
		$date_time = $request->date_time;
		$battery_status = $request->battery_status;
		$gps_status = $request->gps_status;
		$lat = $request->lat;
		$lng = $request->lng;
		$address = $request->address;
		$mcc_mnc_lac_cellid = $request->mcc_mnc_lac_cellid;
		$user_id = $request->user_id;
		$company_id = $request->company_id;
		$primary_sale_summary = json_decode($request->primary_sale_summary);
		DB::beginTransaction();
		$myArr = [
				'order_id'=>$order_id,
				'dealer_id'=>0,
				'created_by_person'=>0,
				'retailer_id'=>$retailer_id,
				'sale_date'=>$sale_date,
				'created_date'=>$created_date,
				'date_time'=>$date_time,
				'battery_status'=>$battery_status,
				'gps_status'=>$gps_status,
				'lat'=>$lat,
				'lng'=>$lng,
				'address'=>$address,
				'mcc_mnc_lac_cellid'=>$mcc_mnc_lac_cellid,
				'company_id'=>$company_id,
				'server_date'=>date('Y-m-d H:i:s'),

			];

		foreach ($primary_sale_summary as $key => $value) {

				$detailsArr = [
					'order_id' => $value->order_id,
					'product_id' => $value->product_id,
					'rate' => $value->rate,
					'pcs_rate' => $value->rate,
					'quantity' => $value->quantity,
					'barcode' => $value->Barcode,
					'scheme_qty' => $value->scheme_qty,
					'cases' => $value->case,
					'pcs' => $value->pcs,
					'value' => $value->value,
					'case_rate' => $value->case_rate,
					'company_id'=>$company_id,
					'app_flag' => $value->app_flag,
					'created_by'=>0,
					'server_date_time'=>date('Y-m-d H:i:s'),
				];
				$insertDetailsArr[] = $detailsArr;
		}
		$insert_order = DB::table('counter_sale_summary')->insert($myArr);
		$insert_order_details = DB::table('counter_sale_details')->insert($insertDetailsArr);
		if($insert_order && $insert_order_details)
		{
			DB::commit();
			return response()->json(['response' =>True,'message'=>'Submitted!!']);        


		}
		else
		{
			DB::rollback();
			return response()->json(['response' =>False,'message'=>'Not Submitted!!']);        

		}
	}
	public function retailer_ecart_product_details(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'state_id' => 'required',
            'company_id'=>'required',
            'retailer_id'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
        }
        $state_id = $request->state_id;
        $company_id = $request->company_id;
        $retailer_id = $request->retailer_id;	

        $product_arary = DB::table('catalog_product')
                        ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
                        ->join('purchase_order_details','purchase_order_details.product_id','=','catalog_product.id')
                        ->join('purchase_order','purchase_order_details.order_id','=','purchase_order.order_id')
                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                        ->select('purchase_order_details.scheme_qty as primary_scheme','purchase_order_details.pcs as primary_pcs','purchase_order_details.cases as primary_cases','purchase_order.order_id','catalog_product.image_name as image_name','product_rate_list.product_id as product_id','state_id','ss_id','distributor_id','product_type_primary','other_retailer_rate','other_dealer_rate','quantiy_per_other_type','product_type_id','retailer_rate as retailer_case_rate','catalog_product.product_type as product_type','catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','catalog_product.quantity_per_case as quantity_per_case','unit','catalog_product.name','product_rate_list.retailer_pcs_rate as base_price','product_rate_list.mrp_pcs as mrp', 'product_rate_list.mrp_pcs','hsn_code','catalog_2.name as cname', 'product_rate_list.dealer_rate as dealer_rate','product_rate_list.dealer_pcs_rate as dealer_pcs_rate')
                        ->where('catalog_1.status',1)
                        ->where('catalog_2.status',1)
                        ->where('catalog_product.status',1)
                        ->where('purchase_order.retailer_id',$retailer_id)
                        ->where('state_id',$state_id)
                        ->where('app_flag','=',1)
                        // ->where('catalog_id',$value->id)
                        ->where('catalog_product.company_id',$company_id)
                        ->groupBy('product_id')
                        ->get()->toArray();
        $product_type_new = DB::table('product_type')
                                    ->where('status',1)
                                    ->where('company_id',$company_id)
                                    ->groupBy('id')
                                    ->pluck('name','id');
        $final_catalog_product_details = array();
        foreach ($product_arary as $key => $value) 
        {
        	$productArray['id'] = "$value->id";
            $productArray['dealer_id'] = "$value->distributor_id";
            $productArray['ss_id'] = "$value->ss_id";
            $productArray['state_id'] = "$value->state_id";
            $productArray['classification_id'] = "$value->classification_id";
            $productArray['classification_name'] = $value->classification_name;
            $productArray['category'] = "$value->catalog_id";
            $productArray['hsn_code'] = $value->hsn_code;
            $productArray['category_name'] = $value->cname;
            $productArray['name'] = $value->name;
            $productArray['product_name'] = $value->name;
            $productArray['weight'] = $value->weight;
            $productArray['base_price'] = $value->base_price;
            $productArray['case_base_price'] = $value->retailer_case_rate;
            $productArray['dealer_rate'] = $value->dealer_rate;
            $productArray['dealer_pcs_rate'] = $value->dealer_pcs_rate;
            $productArray['mrp'] = $value->mrp;
            $productArray['pcs_mrp'] = $value->mrp_pcs;
            $productArray['unit'] = !empty($value->unit)?$value->unit:'';
            $productArray['quantity_per_case'] = !empty($value->quantity_per_case)?$value->quantity_per_case:'';
            $productArray['quantiy_per_other_type'] = !empty($value->quantiy_per_other_type)?$value->quantiy_per_other_type:'';
            $productArray['sku_product_type_id_primary'] = !empty($value->product_type_primary)?$value->product_type_primary:'';
            $productArray['sku_product_type_name_primary'] = !empty($product_type_new[$value->product_type_primary])?$product_type_new[$value->product_type_primary]:'';
            $productArray['sku_product_type_id'] = !empty($value->product_type)?$value->product_type:'';
            $productArray['sku_product_type_name'] = !empty($product_type_new[$value->product_type])?$product_type_new[$value->product_type]:'';
            $productArray['product_type_id_rate_list'] = !empty($value->product_type_id)?$value->product_type_id:'';
            $productArray['product_type_name_rate_list'] = !empty($product_type_new[$value->product_type_id])?$product_type_new[$value->product_type_id]:'';
            $productArray['other_retailer_rate'] = !empty($value->other_retailer_rate)?$value->other_retailer_rate:'';
            $productArray['other_dealer_rate'] = !empty($value->other_dealer_rate)?$value->other_dealer_rate:'';
            $productArray['image_name'] = !empty($value->image_name)?$value->image_name:'';
            $productArray['primary_cases'] = !empty($value->primary_cases)?$value->primary_cases:'';
            $productArray['primary_pcs'] = !empty($value->primary_pcs)?$value->primary_pcs:'';
            $productArray['scheme_qty'] = !empty($value->primary_scheme)?$value->primary_scheme:'';
            $productArray['dms_order_id'] = !empty($value->order_id)?$value->order_id:'';
            $final_catalog_product_details[] = $productArray;
        }

        $scheme_details = DB::table('scheme_plan_details')
        				->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
        				->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id')
        				->where('scheme_plan_details.company_id',$company_id)
        				->where('sale_unit',2)
        				->where('incentive_type',3)
        				->get();
		$mode_details = DB::table('_payment_modes')->select('mode as name','id')->where('status',1)->get();
        return response()->json(['response' =>True,'message'=>'!!Found!!','product_details'=>$final_catalog_product_details,'scheme_details'=>$scheme_details,'payment_mode'=>$mode_details]);        

	}

	public function retailer_counter_draft_product_details(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'state_id' => 'required',
            'company_id'=>'required',
            'retailer_id'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
        }
        $state_id = $request->state_id;
        $company_id = $request->company_id;
        $retailer_id = $request->retailer_id;	

        $product_arary = DB::table('catalog_product')
                        ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
                        ->join('counter_sale_details','counter_sale_details.product_id','=','catalog_product.id')
                        ->join('counter_sale_summary','counter_sale_summary.order_id','=','counter_sale_details.order_id')
                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
                        ->select('counter_sale_details.scheme_qty as primary_scheme','counter_sale_details.pcs as primary_pcs','counter_sale_details.cases as primary_cases','counter_sale_details.order_id','catalog_product.image_name as image_name','product_rate_list.product_id as product_id','state_id','ss_id','distributor_id','product_type_primary','other_retailer_rate','other_dealer_rate','quantiy_per_other_type','product_type_id','retailer_rate as retailer_case_rate','catalog_product.product_type as product_type','catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','catalog_product.quantity_per_case as quantity_per_case','unit','catalog_product.name','product_rate_list.retailer_pcs_rate as base_price','product_rate_list.mrp_pcs as mrp', 'product_rate_list.mrp_pcs','hsn_code','catalog_2.name as cname', 'product_rate_list.dealer_rate as dealer_rate','product_rate_list.dealer_pcs_rate as dealer_pcs_rate')
                        ->where('catalog_1.status',1)
                        ->where('catalog_2.status',1)
                        ->where('catalog_product.status',1)
                        ->where('retailer_id',$retailer_id)
                        ->where('state_id',$state_id)
                        ->where('app_flag','=',1)
                        // ->where('catalog_id',$value->id)
                        ->where('catalog_product.company_id',$company_id)
                        ->groupBy('product_id','order_id')
                        ->get()->toArray();
        $product_type_new = DB::table('product_type')
                                    ->where('status',1)
                                    ->where('company_id',$company_id)
                                    ->groupBy('id')
                                    ->pluck('name','id');
        $final_catalog_product_details = array();
        foreach ($product_arary as $key => $value) 
        {
        	$productArray['id'] = "$value->id";
            $productArray['dealer_id'] = "$value->distributor_id";
            $productArray['ss_id'] = "$value->ss_id";
            $productArray['state_id'] = "$value->state_id";
            $productArray['classification_id'] = "$value->classification_id";
            $productArray['classification_name'] = $value->classification_name;
            $productArray['category'] = "$value->catalog_id";
            $productArray['hsn_code'] = $value->hsn_code;
            $productArray['category_name'] = $value->cname;
            $productArray['name'] = $value->name;
            $productArray['product_name'] = $value->name;
            $productArray['weight'] = $value->weight;
            $productArray['base_price'] = $value->base_price;
            $productArray['case_base_price'] = $value->retailer_case_rate;
            $productArray['dealer_rate'] = $value->dealer_rate;
            $productArray['dealer_pcs_rate'] = $value->dealer_pcs_rate;
            $productArray['mrp'] = $value->mrp;
            $productArray['pcs_mrp'] = $value->mrp_pcs;
            $productArray['unit'] = !empty($value->unit)?$value->unit:'';
            $productArray['quantity_per_case'] = !empty($value->quantity_per_case)?$value->quantity_per_case:'';
            $productArray['quantiy_per_other_type'] = !empty($value->quantiy_per_other_type)?$value->quantiy_per_other_type:'';
            $productArray['sku_product_type_id_primary'] = !empty($value->product_type_primary)?$value->product_type_primary:'';
            $productArray['sku_product_type_name_primary'] = !empty($product_type_new[$value->product_type_primary])?$product_type_new[$value->product_type_primary]:'';
            $productArray['sku_product_type_id'] = !empty($value->product_type)?$value->product_type:'';
            $productArray['sku_product_type_name'] = !empty($product_type_new[$value->product_type])?$product_type_new[$value->product_type]:'';
            $productArray['product_type_id_rate_list'] = !empty($value->product_type_id)?$value->product_type_id:'';
            $productArray['product_type_name_rate_list'] = !empty($product_type_new[$value->product_type_id])?$product_type_new[$value->product_type_id]:'';
            $productArray['other_retailer_rate'] = !empty($value->other_retailer_rate)?$value->other_retailer_rate:'';
            $productArray['other_dealer_rate'] = !empty($value->other_dealer_rate)?$value->other_dealer_rate:'';
            $productArray['image_name'] = !empty($value->image_name)?$value->image_name:'';
            $productArray['primary_cases'] = !empty($value->primary_cases)?$value->primary_cases:'';
            $productArray['primary_pcs'] = !empty($value->primary_pcs)?$value->primary_pcs:'';
            $productArray['scheme_qty'] = !empty($value->primary_scheme)?$value->primary_scheme:'';
            $productArray['dms_order_id'] = !empty($value->order_id)?$value->order_id:'';
            $final_catalog_product_details[] = $productArray;
        }

        $scheme_details = DB::table('scheme_plan_details')
        				->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
        				->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id')
        				->where('scheme_plan_details.company_id',$company_id)
        				->where('sale_unit',2)
        				->where('incentive_type',3)
        				->get();
		$mode_details = DB::table('_payment_modes')->select('mode as name','id')->where('status',1)->get();
        return response()->json(['response' =>True,'message'=>'!!Found!!','product_details'=>$final_catalog_product_details]);        

	}
	public function retailer_total_ecart(Request $request)
	{
		$validator=Validator::make($request->all(),[
            "state_id"=>'required',
			"retailer_id"=>'required',
			"company_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
        }
        $company_id = $request->company_id;
        $state_id = $request->state_id;
        $retailer_id = $request->retailer_id;
        $data = DB::table('purchase_order')
    		->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
			->where('app_flag',1)
			// ->where('purchase_order.state_id',$state_id)
			->where('purchase_order.retailer_id',$retailer_id)
			->where('purchase_order.company_id',$company_id)
			->COUNT();
    	return response()->json(['response' =>True,'message'=>'Found!!','data'=>$data]); 
	}
	public function retailer_action_list_btw(Request $request)
	{
		$validator=Validator::make($request->all(),[
			"order_id"=>'required',
			"company_id"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
        }

        $order_id = $request->order_id;
        $company_id = $request->company_id;

        $data = DB::table('_dms_reason')
    			->select('id','name')
    			->where('status',1)
    			->get();
		$dms_log = DB::table('dms_order_reason_log')
				->where('order_id',$order_id)
				->groupBy('dms_reason_id')
				->pluck('dms_reason_id','dms_reason_id');
		foreach ($data as $key => $value) 
		{
			$out['id'] = $value->id;
			$out['name'] = $value->name;
			$out['current_reason_status_id'] = !empty($dms_log[$value->id])?$dms_log[$value->id]:'';
			// $out['current_reason_status_name'] = '';
			$finalArr[] = $out;
		}
    	return response()->json(['response' =>True,'message'=>'Found!!','data'=>$finalArr]); 

	}
	public function sumbit_status_action_retailer_btw(Request $request)
	{
		$validator=Validator::make($request->all(),[
			"order_id"=>'required',
			"dms_reason_id"=>'required',
			"company_id"=>'required',
			"date"=>'required',
			"time"=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
        }

        $order_id = $request->order_id;
        $dms_reason_id = $request->dms_reason_id;
        $company_id = $request->company_id;
        $date = $request->date;
        $time = $request->time;

        $check = DB::table('purchase_order')
        		->where('order_id',$order_id)
        		->count();
		if($check>0)
		{
			$check2 = DB::table('dms_order_reason_log')
					->where('order_id',$order_id)
					->where('company_id',$company_id)
					->where('dms_reason_id',$dms_reason_id)
					->count();
			if($check2>0)
			{
				$insert_query = DB::table('dms_order_reason_log')->insert([
						'order_id'=>$order_id,
						'company_id'=>$company_id,
						'date'=>$date,
						'time'=>$time,
						'dms_reason_id'=>$dms_reason_id,
						'server_date_time'=>date('Y-m-d H:i:s'),

				]);
				$update_query = DB::table('purchase_order')
							->where('order_id',$order_id)
							->where('company_id',$company_id)
							->update(['dms_order_reason_id'=>$dms_reason_id]);
    			return response()->json(['response' =>True,'message'=>'Submit Successfully!!']); 

			}
			else
			{
    			return response()->json(['response' =>True,'message'=>'Duplicate!!']); 

			}
		}
		else
		{
			return response()->json(['response' =>True,'message'=>'Duplicate!!']); 

		}

	}
	// public function dms_login(Request $request)
	// {
	//  	$validator=Validator::make($request->all(),[
 //            'uname' => 'required',
 //            'pass'=>'required',
 //        ]);
 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
 //        }
	// 	$uname = $request->uname;
	// 	$pass = $request->pass;

	// 	$query_login = DB::table('dealer_person_login')
	// 				->join('_role','_role.role_id','=','dealer_person_login.role_id')
	// 				->join('location_3','location_3.id','=','dealer_person_login.state_id')
	// 				->select('rolename','dealer_id','email','state_id','dpId','_role.role_id','person_name','dealer_person_login.company_id','phone','location_3.name as l3_name')
	// 				->where('uname',$uname)
	// 				// ->where('person_password',DB::raw("AES_ENCRYPT('".trim($pass)."', '".Lang::get('common.db_salt')."')"))
	// 				->whereRaw("AES_DECRYPT(pass, 'demo') = '$pass'")
	// 				->where('activestatus',1)
	// 				->get();
	// 	// dd($query_login);
	// 	$dms_login_array = array();
	// 	if(count($query_login)<=0)
	// 	{
	// 		$query_login_second = DB::table('retailer')
	// 				// ->join('_role','_role.role_id','=','dealer_person_login.role_id')
	// 				->select('dealer_id','email','location_id','id','name','company_id','landline','other_numbers','username as user_name','state_id_retailer')
	// 				->where('username',$uname)
	// 				// ->where('person_password',DB::raw("AES_ENCRYPT('".trim($pass)."', '".Lang::get('common.db_salt')."')"))
	// 				->whereRaw("AES_DECRYPT(password, 'demo') = '$pass'")
	// 				->where('retailer_status',1)
	// 				->get();
	// 		$dms_login_array = array();
	// 		if(count($query_login_second)>0)
	// 		{
	// 			$company_id = 0;
	// 			foreach ($query_login_second as $key => $value) 
	// 			{
	// 			    // $dms_primary_id = $value->dpId;
	// 				// $person_fullname = $value->person_name
	// 				// $mobile = $value->phone;
	// 				// $email = $value->email;
	// 				// $state_id = $value->state_id;
					
	// 				$company_id = $value->company_id;
	// 				// $location_id = $value->location_id;
	// 				if($value->location_id == 0)
	// 				{
	// 					$location_details_arra = DB::table('location_view')->where('l3_id',$value->state_id_retailer)->where('l4_company_id',$value->company_id)->first();

	// 				}
	// 				else
	// 				{
	// 					$location_details_arra = DB::table('location_view')->where('l7_id',$value->location_id)->where('l7_company_id',$value->company_id)->first();

	// 				}
	// 				$dms_login_array['retailer_primary_id'] = $value->id;
	// 				$dms_login_array['name'] = $value->name;
	// 				$dms_login_array['person_fullname'] = $value->name;
	// 				$dms_login_array['mobile'] = !empty($value->landline)?$value->landline:$value->other_numbers;
	// 				$dms_login_array['email'] = !empty($value->email)?$value->email:'';
	// 				$dms_login_array['location_id'] = $value->location_id;
	// 				$dms_login_array['state_id'] = !empty($location_details_arra->l3_id)?$location_details_arra->l3_id:'0';
	// 				$dms_login_array['person_role_id'] = 0;
	// 				$dms_login_array['person_role_name'] = 'Retailer';
	// 				$dms_login_array['state_name'] = !empty($location_details_arra->l3_name)?$location_details_arra->l3_name:'NA';
	// 				// $dms_login_array['person_role_id'] = $value->role_id;
	// 				// $dms_login_array['person_role_name'] = $value->rolename;
	// 				$dms_login_array['dealer_id'] = $value->dealer_id;
	// 				$dms_login_array['company_id'] = $value->company_id;
	// 				$dms_login_array['user_type'] = '1';
	// 			}
	// 			$check_role_id_data = DB::table('_role')->where('company_id',$company_id)->where('rolename','Retailer')->first();
	// 			if(empty($check_role_id_data))
	// 			{
 //            		return response()->json(['response'=>False,'message'=>'Make role first']);

	// 			}
	// 			$check_role_id = $check_role_id_data->role_id;
	// 		 	$check_role_wise_assing_module = DB::table('role_app_module')
	//                                             ->join('master_list_module','master_list_module.id','=','role_app_module.module_id')
	//                                             ->select('master_list_module.icon_image as module_icon_image','master_list_module.id as module_id','role_app_module.title_name as module_name','master_list_module.url as module_url')
	//                                             ->where('role_app_module.company_id',$company_id)
	//                                             ->where('role_app_module.status',1)
	//                                             ->where('role_app_module.status',1)
	//                                             ->where('role_app_module.role_id',$check_role_id)
	//                                             ->orderBy('role_app_module.module_sequence','ASC')
	//                                             ->get();
	//                     // dd($check_role_wise_assing_module);
	                   
	//             if(COUNT($check_role_wise_assing_module)>0)
	//             {
	//                 $module = array();
	//                 foreach ($check_role_wise_assing_module as $key => $value)
	//                 {
	//                     $module[$key]['module_icon_image'] = !empty($value->module_icon_image)?$value->module_icon_image:'';
	//                     $module[$key]['module_id'] = "$value->module_id";
	//                     $module[$key]['module_name'] = !empty($value->module_name)?$value->module_name:'';
	//                     $module[$key]['module_url'] = !empty($value->module_url)?$value->module_url:'';
	//                 }
	//                 $role_sub_module = DB::table('role_sub_modules')
	//                             ->join('master_list_sub_module','master_list_sub_module.id','=','role_sub_modules.sub_module_id')
	//                             ->select('master_list_sub_module.module_id as module_id','role_sub_modules.sub_module_name as sub_module_name','master_list_sub_module.id as sub_module_id','master_list_sub_module.path as sub_module_url','master_list_sub_module.image_name as sub_module_icon_image')
	//                             ->where('role_sub_modules.company_id',$company_id)
	//                             ->where('role_sub_modules.status',1)
	//                             ->where('master_list_sub_module.status',1)
	//                             ->where('role_sub_modules.role_id',$check_role_id)
	//                             ->orderBy('role_sub_modules.module_sequence','ASC')
	//                             ->get();
	//                 $sub_module_arr = array();
	//                 foreach ($role_sub_module as $key => $value)
	//                 {
	//                     $sub_module_arr[$key]['sub_module_icon_image'] = !empty($value->sub_module_icon_image)?$value->sub_module_icon_image:'';
	//                     $sub_module_arr[$key]['module_id'] = "$value->module_id";
	//                     $sub_module_arr[$key]['sub_module_id'] = "$value->sub_module_id";
	//                     $sub_module_arr[$key]['sub_module_name'] = !empty($value->sub_module_name)?$value->sub_module_name:'';
	//                     $sub_module_arr[$key]['sub_module_url'] = !empty($value->sub_module_url)?$value->sub_module_url:'';
	//                 }

	//                 $other_module = DB::table('role_app_other_module_assign')
	//                         ->join('master_other_app_module','master_other_app_module.id','=','role_app_other_module_assign.module_id')
	//                         ->select('master_other_app_module.image_name as other_module_icon_image','role_app_other_module_assign.title_name as other_module_name','role_app_other_module_assign.module_id as other_module_id')
	//                         ->where('role_app_other_module_assign.status',1)
	//                         ->where('role_app_other_module_assign.company_id',$company_id)
	//                         ->where('master_other_app_module.id','!=',2) // beacaue this id belongs to retailer creation with otp so this condition true above the role wise module condition starts
	//                         ->where('master_other_app_module.status',1)
	//                         ->where('role_app_other_module_assign.role_id',$check_role_id)
	//                         ->orderBy('role_app_other_module_assign.module_sequence','ASC')
	//                         ->get();
	//                 $other_module_arr = array();
	//                 foreach ($other_module as $key => $value)
	//                 {
	//                     $other_module_arr[$key]['other_module_icon_image'] = !empty($value->other_module_icon_image)?$value->other_module_icon_image:'';
	//                     $other_module_arr[$key]['other_module_id'] = "$value->other_module_id";
	//                     $other_module_arr[$key]['other_module_name'] = !empty($value->other_module_name)?$value->other_module_name:'';
	//                 }
	//                 // dd($other_module_arr);

	//             }
	//             else
	//             {
	//                 $app_module = DB::table('app_module')
	//                         ->join('master_list_module','master_list_module.id','=','app_module.module_id')
	//                         ->select('master_list_module.icon_image as module_icon_image','master_list_module.id as module_id','app_module.title_name as module_name','master_list_module.url as module_url')
	//                         ->where('app_module.company_id',$company_id)
	//                         ->where('app_module.status',1)
	//                         ->where('master_list_module.status',1)
	//                         ->orderBy('app_module.module_sequence','ASC')
	//                         ->get();
	//                 $module = array();
	//                 foreach ($app_module as $key => $value)
	//                 {
	//                     $module[$key]['module_icon_image'] = !empty($value->module_icon_image)?$value->module_icon_image:'';
	//                     $module[$key]['module_id'] = "$value->module_id";
	//                     $module[$key]['module_name'] = !empty($value->module_name)?$value->module_name:'';
	//                     $module[$key]['module_url'] = !empty($value->module_url)?$value->module_url:'';
	//                 }
	//                 $sub_module = DB::table('_sub_modules')
	//                             ->join('master_list_sub_module','master_list_sub_module.id','=','_sub_modules.sub_module_id')
	//                             ->select('master_list_sub_module.module_id as module_id','_sub_modules.sub_module_name as sub_module_name','master_list_sub_module.id as sub_module_id','master_list_sub_module.path as sub_module_url','master_list_sub_module.image_name as sub_module_icon_image')
	//                             ->where('_sub_modules.company_id',$company_id)
	//                             ->where('_sub_modules.status',1)
	//                             ->where('master_list_sub_module.status',1)
	//                             ->orderBy('_sub_modules.module_sequence','ASC')
	//                             ->get();
	//                 $sub_module_arr = array();
	//                 foreach ($sub_module as $key => $value)
	//                 {
	//                     $sub_module_arr[$key]['sub_module_icon_image'] = !empty($value->sub_module_icon_image)?$value->sub_module_icon_image:'';
	//                     $sub_module_arr[$key]['module_id'] = "$value->module_id";
	//                     $sub_module_arr[$key]['sub_module_id'] = "$value->sub_module_id";
	//                     $sub_module_arr[$key]['sub_module_name'] = !empty($value->sub_module_name)?$value->sub_module_name:'';
	//                     $sub_module_arr[$key]['sub_module_url'] = !empty($value->sub_module_url)?$value->sub_module_url:'';
	//                 }
	//                 $other_module = DB::table('app_other_module_assign')
	//                         ->join('master_other_app_module','master_other_app_module.id','=','app_other_module_assign.module_id')
	//                         ->select('master_other_app_module.image_name as other_module_icon_image','app_other_module_assign.title_name as other_module_name','app_other_module_assign.module_id as other_module_id')
	//                         ->where('app_other_module_assign.status',1)
	//                         ->where('app_other_module_assign.company_id',$company_id)
	//                         // ->where('master_other_app_module.id','!=',2) // beacaue this id belongs to retailer creation with otp so this condition true above the role wise module condition starts
	//                         ->where('master_other_app_module.status',1)
	//                         ->orderBy('app_other_module_assign.module_sequence','ASC')
	//                         ->get();
	//                 $other_module_arr = array();
	//                 foreach ($other_module as $key => $value)
	//                 {
	//                     $other_module_arr[$key]['other_module_icon_image'] = !empty($value->other_module_icon_image)?$value->other_module_icon_image:'';
	//                     $other_module_arr[$key]['other_module_id'] = "$value->other_module_id";
	//                     $other_module_arr[$key]['other_module_name'] = !empty($value->other_module_name)?$value->other_module_name:'';
	//                 }
	//                 // dd($other_module_arr);
	//             }
	// 			#......................................reponse parameters starts here ..................................................##
	//                     return response()->json([
	//                         'response' =>True,
	//                         'details'=>$dms_login_array,
	//               		 	'app_module'=> $module,
	//                         'sub_module'=> $sub_module_arr,
	//                         'message'=>'Success!!']);
	//                 #......................................reponse parameters ends here ..................................................##
	// 		}
	// 		else
	// 		{
	//             return response()->json([ 'response' =>False,'message'=>'!!Credentials not match w1ith our records!!']);        

	// 		}
	// 	}
	// 	if(COUNT($query_login)>0)
	// 	{
	// 		$company_id = 0;
	// 		foreach ($query_login as $key => $value) 
	// 		{
	// 		 // 	$dms_primary_id = $value->dpId;
	// 			// $person_fullname = $value->person_name
	// 			// $mobile = $value->phone;
	// 			// $email = $value->email;
	// 			// $state_id = $value->state_id;
	// 			$check_role_id = $value->role_id;
	// 			$company_id = $value->company_id;

	// 			$dms_login_array['dms_primary_id'] = $value->dpId;
	// 			$dms_login_array['person_fullname'] = $value->person_name;
	// 			$dms_login_array['mobile'] = !empty($value->phone)?$value->phone:'0';
	// 			$dms_login_array['email'] = !empty($value->email)?$value->email:'';
	// 			$dms_login_array['state_id'] = $value->state_id;
	// 			$dms_login_array['person_role_id'] = $value->role_id;
	// 			$dms_login_array['person_role_name'] = $value->rolename;
	// 			$dms_login_array['dealer_id'] = $value->dealer_id;
	// 			$dms_login_array['company_id'] = $value->company_id;
	// 			$dms_login_array['state_name'] = $value->l3_name;
	// 			$dms_login_array['user_type'] = '2';

	// 		}

	// 	 	$check_role_wise_assing_module = DB::table('role_app_module')
	//                                             ->join('master_list_module','master_list_module.id','=','role_app_module.module_id')
	//                                             ->select('master_list_module.icon_image as module_icon_image','master_list_module.id as module_id','role_app_module.title_name as module_name','master_list_module.url as module_url')
	//                                             ->where('role_app_module.company_id',$company_id)
	//                                             ->where('role_app_module.status',1)
	//                                             ->where('role_app_module.status',1)
	//                                             ->where('role_app_module.role_id',$check_role_id)
	//                                             ->orderBy('role_app_module.module_sequence','ASC')
	//                                             ->get();
	//                     // dd($check_role_wise_assing_module);
	                   
	//             if(COUNT($check_role_wise_assing_module)>0)
	//             {
	//                 $module = array();
	//                 foreach ($check_role_wise_assing_module as $key => $value)
	//                 {
	//                     $module[$key]['module_icon_image'] = !empty($value->module_icon_image)?$value->module_icon_image:'';
	//                     $module[$key]['module_id'] = "$value->module_id";
	//                     $module[$key]['module_name'] = !empty($value->module_name)?$value->module_name:'';
	//                     $module[$key]['module_url'] = !empty($value->module_url)?$value->module_url:'';
	//                 }
	//                 $role_sub_module = DB::table('role_sub_modules')
	//                             ->join('master_list_sub_module','master_list_sub_module.id','=','role_sub_modules.sub_module_id')
	//                             ->select('master_list_sub_module.module_id as module_id','role_sub_modules.sub_module_name as sub_module_name','master_list_sub_module.id as sub_module_id','master_list_sub_module.path as sub_module_url','master_list_sub_module.image_name as sub_module_icon_image')
	//                             ->where('role_sub_modules.company_id',$company_id)
	//                             ->where('role_sub_modules.status',1)
	//                             ->where('master_list_sub_module.status',1)
	//                             ->where('role_sub_modules.role_id',$check_role_id)
	//                             ->orderBy('role_sub_modules.module_sequence','ASC')
	//                             ->get();
	//                 $sub_module_arr = array();
	//                 foreach ($role_sub_module as $key => $value)
	//                 {
	//                     $sub_module_arr[$key]['sub_module_icon_image'] = !empty($value->sub_module_icon_image)?$value->sub_module_icon_image:'';
	//                     $sub_module_arr[$key]['module_id'] = "$value->module_id";
	//                     $sub_module_arr[$key]['sub_module_id'] = "$value->sub_module_id";
	//                     $sub_module_arr[$key]['sub_module_name'] = !empty($value->sub_module_name)?$value->sub_module_name:'';
	//                     $sub_module_arr[$key]['sub_module_url'] = !empty($value->sub_module_url)?$value->sub_module_url:'';
	//                 }

	//                 $other_module = DB::table('role_app_other_module_assign')
	//                         ->join('master_other_app_module','master_other_app_module.id','=','role_app_other_module_assign.module_id')
	//                         ->select('master_other_app_module.image_name as other_module_icon_image','role_app_other_module_assign.title_name as other_module_name','role_app_other_module_assign.module_id as other_module_id')
	//                         ->where('role_app_other_module_assign.status',1)
	//                         ->where('role_app_other_module_assign.company_id',$company_id)
	//                         ->where('master_other_app_module.id','!=',2) // beacaue this id belongs to retailer creation with otp so this condition true above the role wise module condition starts
	//                         ->where('master_other_app_module.status',1)
	//                         ->where('role_app_other_module_assign.role_id',$check_role_id)
	//                         ->orderBy('role_app_other_module_assign.module_sequence','ASC')
	//                         ->get();
	//                 $other_module_arr = array();
	//                 foreach ($other_module as $key => $value)
	//                 {
	//                     $other_module_arr[$key]['other_module_icon_image'] = !empty($value->other_module_icon_image)?$value->other_module_icon_image:'';
	//                     $other_module_arr[$key]['other_module_id'] = "$value->other_module_id";
	//                     $other_module_arr[$key]['other_module_name'] = !empty($value->other_module_name)?$value->other_module_name:'';
	//                 }
	//                 // dd($other_module_arr);

	//             }
	//             else
	//             {
	//                 $app_module = DB::table('app_module')
	//                         ->join('master_list_module','master_list_module.id','=','app_module.module_id')
	//                         ->select('master_list_module.icon_image as module_icon_image','master_list_module.id as module_id','app_module.title_name as module_name','master_list_module.url as module_url')
	//                         ->where('app_module.company_id',$company_id)
	//                         ->where('app_module.status',1)
	//                         ->where('master_list_module.status',1)
	//                         ->orderBy('app_module.module_sequence','ASC')
	//                         ->get();
	//                 $module = array();
	//                 foreach ($app_module as $key => $value)
	//                 {
	//                     $module[$key]['module_icon_image'] = !empty($value->module_icon_image)?$value->module_icon_image:'';
	//                     $module[$key]['module_id'] = "$value->module_id";
	//                     $module[$key]['module_name'] = !empty($value->module_name)?$value->module_name:'';
	//                     $module[$key]['module_url'] = !empty($value->module_url)?$value->module_url:'';
	//                 }
	//                 $sub_module = DB::table('_sub_modules')
	//                             ->join('master_list_sub_module','master_list_sub_module.id','=','_sub_modules.sub_module_id')
	//                             ->select('master_list_sub_module.module_id as module_id','_sub_modules.sub_module_name as sub_module_name','master_list_sub_module.id as sub_module_id','master_list_sub_module.path as sub_module_url','master_list_sub_module.image_name as sub_module_icon_image')
	//                             ->where('_sub_modules.company_id',$company_id)
	//                             ->where('_sub_modules.status',1)
	//                             ->where('master_list_sub_module.status',1)
	//                             ->orderBy('_sub_modules.module_sequence','ASC')
	//                             ->get();
	//                 $sub_module_arr = array();
	//                 foreach ($sub_module as $key => $value)
	//                 {
	//                     $sub_module_arr[$key]['sub_module_icon_image'] = !empty($value->sub_module_icon_image)?$value->sub_module_icon_image:'';
	//                     $sub_module_arr[$key]['module_id'] = "$value->module_id";
	//                     $sub_module_arr[$key]['sub_module_id'] = "$value->sub_module_id";
	//                     $sub_module_arr[$key]['sub_module_name'] = !empty($value->sub_module_name)?$value->sub_module_name:'';
	//                     $sub_module_arr[$key]['sub_module_url'] = !empty($value->sub_module_url)?$value->sub_module_url:'';
	//                 }
	//                 $other_module = DB::table('app_other_module_assign')
	//                         ->join('master_other_app_module','master_other_app_module.id','=','app_other_module_assign.module_id')
	//                         ->select('master_other_app_module.image_name as other_module_icon_image','app_other_module_assign.title_name as other_module_name','app_other_module_assign.module_id as other_module_id')
	//                         ->where('app_other_module_assign.status',1)
	//                         ->where('app_other_module_assign.company_id',$company_id)
	//                         // ->where('master_other_app_module.id','!=',2) // beacaue this id belongs to retailer creation with otp so this condition true above the role wise module condition starts
	//                         ->where('master_other_app_module.status',1)
	//                         ->orderBy('app_other_module_assign.module_sequence','ASC')
	//                         ->get();
	//                 $other_module_arr = array();
	//                 foreach ($other_module as $key => $value)
	//                 {
	//                     $other_module_arr[$key]['other_module_icon_image'] = !empty($value->other_module_icon_image)?$value->other_module_icon_image:'';
	//                     $other_module_arr[$key]['other_module_id'] = "$value->other_module_id";
	//                     $other_module_arr[$key]['other_module_name'] = !empty($value->other_module_name)?$value->other_module_name:'';
	//                 }
	//                 // dd($other_module_arr);
	//             }
	// 			#......................................reponse parameters starts here ..................................................##
	//                     return response()->json([
	//                         'response' =>True,
	//                         'details'=>$dms_login_array,
	//               		 	'app_module'=> $module,
	//                         'sub_module'=> $sub_module_arr,
	//                         'message'=>'Success!!']);
	//                 #......................................reponse parameters ends here ..................................................##
			
	// 	}

	// 	else
	// 	{
 //            return response()->json([ 'response' =>False,'message'=>'!!Credentials not match w1ith our records!!']);        

	// 	}
	// }

	// public function dms_product_details(Request $request)
	// {
	// 	$validator=Validator::make($request->all(),[
 //            'state_id' => 'required',
 //            'company_id'=>'required',
 //            'dealer_id'=>'required',
 //        ]);
 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
 //        }
 //        $state_id = $request->state_id;
 //        $company_id = $request->company_id;
 //        $dealer_id = $request->dealer_id;

 //        $product_array = DB::table('catalog_product')
 //                        ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
 //                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
 //                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
 //                        ->select('catalog_2.*')
 //                        ->where('catalog_1.status',1)
 //                        ->where('catalog_2.status',1)
 //                        ->where('catalog_product.status',1)
 //                        ->where('state_id',$state_id)
 //                        ->where('catalog_product.company_id',$company_id)
 //                        ->groupBy('catalog_2.id')
 //                        ->get()->toArray();

 //        // $dms_status
 //        $final_array_details = [];
 //        foreach ($product_array as $key => $value) 
 //        {
 //        	$first_layer['id']=$value->id;
 //        	$first_layer['name']=$value->name;
 //        	$first_layer['catalog_product_details']=DB::table('catalog_product')
	// 					                        ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
	// 					                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
	// 					                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
	// 					                        ->select('catalog_product.image_name as image_name','product_id','state_id','ss_id','distributor_id','product_type_primary','other_retailer_rate','other_dealer_rate','quantiy_per_other_type','product_type_id','retailer_rate as retailer_case_rate','catalog_product.product_type as product_type','catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','catalog_product.quantity_per_case as quantity_per_case','unit','catalog_product.name as product_name','product_rate_list.retailer_pcs_rate as base_price','product_rate_list.mrp_pcs as mrp', 'product_rate_list.mrp_pcs','hsn_code','catalog_2.name as category_name', 'product_rate_list.dealer_rate as dealer_rate','product_rate_list.dealer_pcs_rate as dealer_pcs_rate')
	// 					                        ->where('catalog_1.status',1)
	// 					                        ->where('catalog_2.status',1)
	// 					                        ->where('catalog_product.status',1)
	// 					                        ->where('state_id',$state_id)
	// 					                        ->where('catalog_id',$value->id)
	// 					                        ->where('catalog_product.company_id',$company_id)
	// 					                        ->groupBy('product_id')
	// 					                        ->get()->toArray();
 //            $final_array_details[] = $first_layer;
 //    	}
 //    	$further_details = array();
 //    	$scheme_details = DB::table('scheme_plan_details')
 //        				->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
 //        				->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id')
 //        				->where('scheme_plan_details.company_id',$company_id)
 //        				->where('sale_unit',2)
 //        				->where('incentive_type',3)
 //        				->get();
 //    	if($request->status == 1) // purchase order
 //    	{


 //    		$further_details = DB::table('purchase_order')
	// 					->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
	// 					->select('cases as cases','product_id','scheme_qty','purchase_order.order_id as order_id','quantity')
	// 					->where('purchase_order.dealer_id',$dealer_id)
	// 					->where('app_flag',1)
	// 					->where('purchase_order.company_id',$company_id)
	// 					->get();
	// 	}
	// 	elseif ($request->status == 2) 
	// 	{
	// 		$further_details = DB::table('counter_sale_summary')
	// 					->join('counter_sale_details','counter_sale_details.order_id','=','counter_sale_summary.order_id')
	// 					->select('cases','case_rate','counter_sale_summary.order_id','product_id','pcs as quantity')
	// 					->where('app_flag',1)
	// 					->where('counter_sale_summary.dealer_id',$dealer_id)
	// 					->where('counter_sale_summary.company_id',$company_id)
	// 					->get();
	// 	}

 //        return response()->json(['response' =>True,'message'=>'!!Found!!','product_details'=>$final_array_details,'further_details'=>$further_details,'scheme_details'=>$scheme_details]);        

	// }
	// public function dms_ecart_product_details(Request $request)
	// {
	// 	$validator=Validator::make($request->all(),[
 //            'state_id' => 'required',
 //            'company_id'=>'required',
 //            'dealer_id'=>'required',
 //        ]);
 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
 //        }
 //        $state_id = $request->state_id;
 //        $company_id = $request->company_id;
 //        $dealer_id = $request->dealer_id;	

 //        $product_arary = DB::table('catalog_product')
 //                        ->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
 //                        ->join('purchase_order_details','purchase_order_details.product_id','=','catalog_product.id')
 //                        ->join('purchase_order','purchase_order_details.order_id','=','purchase_order.order_id')
 //                        ->join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
 //                        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
 //                        ->select('purchase_order_details.scheme_qty as primary_scheme','purchase_order_details.pcs as primary_pcs','purchase_order_details.cases as primary_cases','purchase_order.order_id','catalog_product.image_name as image_name','product_rate_list.product_id as product_id','state_id','ss_id','distributor_id','product_type_primary','other_retailer_rate','other_dealer_rate','quantiy_per_other_type','product_type_id','retailer_rate as retailer_case_rate','catalog_product.product_type as product_type','catalog_product.id as id','catalog_product.weight as weight','catalog_1.id as classification_id','catalog_1.name as classification_name','catalog_id','catalog_product.quantity_per_case as quantity_per_case','unit','catalog_product.name','product_rate_list.retailer_pcs_rate as base_price','product_rate_list.mrp_pcs as mrp', 'product_rate_list.mrp_pcs','hsn_code','catalog_2.name as cname', 'product_rate_list.dealer_rate as dealer_rate','product_rate_list.dealer_pcs_rate as dealer_pcs_rate')
 //                        ->where('catalog_1.status',1)
 //                        ->where('catalog_2.status',1)
 //                        ->where('catalog_product.status',1)
 //                        ->where('state_id',$state_id)
 //                        ->where('app_flag','=',1)
 //                        // ->where('catalog_id',$value->id)
 //                        ->where('catalog_product.company_id',$company_id)
 //                        ->groupBy('product_id')
 //                        ->get()->toArray();
 //        $product_type_new = DB::table('product_type')
 //                                    ->where('status',1)
 //                                    ->where('company_id',$company_id)
 //                                    ->groupBy('id')
 //                                    ->pluck('name','id');
 //        $final_catalog_product_details = array();
 //        foreach ($product_arary as $key => $value) 
 //        {
 //        	$productArray['id'] = "$value->id";
 //            $productArray['dealer_id'] = "$value->distributor_id";
 //            $productArray['ss_id'] = "$value->ss_id";
 //            $productArray['state_id'] = "$value->state_id";
 //            $productArray['classification_id'] = "$value->classification_id";
 //            $productArray['classification_name'] = $value->classification_name;
 //            $productArray['category'] = "$value->catalog_id";
 //            $productArray['hsn_code'] = $value->hsn_code;
 //            $productArray['category_name'] = $value->cname;
 //            $productArray['name'] = $value->name;
 //            $productArray['product_name'] = $value->name;
 //            $productArray['weight'] = $value->weight;
 //            $productArray['base_price'] = $value->base_price;
 //            $productArray['case_base_price'] = $value->retailer_case_rate;
 //            $productArray['dealer_rate'] = $value->dealer_rate;
 //            $productArray['dealer_pcs_rate'] = $value->dealer_pcs_rate;
 //            $productArray['mrp'] = $value->mrp;
 //            $productArray['pcs_mrp'] = $value->mrp_pcs;
 //            $productArray['unit'] = !empty($value->unit)?$value->unit:'';
 //            $productArray['quantity_per_case'] = !empty($value->quantity_per_case)?$value->quantity_per_case:'';
 //            $productArray['quantiy_per_other_type'] = !empty($value->quantiy_per_other_type)?$value->quantiy_per_other_type:'';
 //            $productArray['sku_product_type_id_primary'] = !empty($value->product_type_primary)?$value->product_type_primary:'';
 //            $productArray['sku_product_type_name_primary'] = !empty($product_type_new[$value->product_type_primary])?$product_type_new[$value->product_type_primary]:'';
 //            $productArray['sku_product_type_id'] = !empty($value->product_type)?$value->product_type:'';
 //            $productArray['sku_product_type_name'] = !empty($product_type_new[$value->product_type])?$product_type_new[$value->product_type]:'';
 //            $productArray['product_type_id_rate_list'] = !empty($value->product_type_id)?$value->product_type_id:'';
 //            $productArray['product_type_name_rate_list'] = !empty($product_type_new[$value->product_type_id])?$product_type_new[$value->product_type_id]:'';
 //            $productArray['other_retailer_rate'] = !empty($value->other_retailer_rate)?$value->other_retailer_rate:'';
 //            $productArray['other_dealer_rate'] = !empty($value->other_dealer_rate)?$value->other_dealer_rate:'';
 //            $productArray['image_name'] = !empty($value->image_name)?$value->image_name:'';
 //            $productArray['primary_cases'] = !empty($value->primary_cases)?$value->primary_cases:'';
 //            $productArray['primary_pcs'] = !empty($value->primary_pcs)?$value->primary_pcs:'';
 //            $productArray['scheme_qty'] = !empty($value->primary_scheme)?$value->primary_scheme:'';
 //            $productArray['dms_order_id'] = !empty($value->order_id)?$value->order_id:'';
 //            $final_catalog_product_details[] = $productArray;
 //        }

 //        $scheme_details = DB::table('scheme_plan_details')
 //        				->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
 //        				->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id')
 //        				->where('scheme_plan_details.company_id',$company_id)
 //        				->where('sale_unit',2)
 //        				->where('incentive_type',3)
 //        				->get();
	// 	$mode_details = DB::table('_payment_modes')->select('mode as name','id')->where('status',1)->get();
 //        return response()->json(['response' =>True,'message'=>'!!Found!!','product_details'=>$final_catalog_product_details,'scheme_details'=>$scheme_details,'payment_mode'=>$mode_details]);        




	// }

	// public function dms_primary_sale_submit(Request $request)
	// {
	// 	$validator=Validator::make($request->all(),[
 //            "order_id"=>'required',
	// 		"dealer_id"=>'required',
	// 		"sale_date"=>'required',
	// 		"created_date"=>'required',
	// 		"date_time"=>'required',
	// 		"battery_status"=>'required',
	// 		"gps_status"=>'required',
	// 		"lat"=>'required',
	// 		"lng"=>'required',
	// 		"address"=>'required',
	// 		"mcc_mnc_lac_cellid"=>'required',
	// 		"user_id"=>'required',
	// 		"company_id"=>'required',
 //        ]);
 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
 //        }

	// 	$order_id = $request->order_id;
	// 	$dealer_id = $request->dealer_id;
	// 	$sale_date = $request->sale_date;
	// 	$created_date = $request->created_date;
	// 	$date_time = $request->date_time;
	// 	$battery_status = $request->battery_status;
	// 	$gps_status = $request->gps_status;
	// 	$lat = $request->lat;
	// 	$lng = $request->lng;
	// 	$address = $request->address;
	// 	$mcc_mnc_lac_cellid = $request->mcc_mnc_lac_cellid;
	// 	$user_id = $request->user_id;
	// 	$company_id = $request->company_id;
	// 	$primary_sale_summary = json_decode($request->primary_sale_summary);
	// 	$insertDetailsArr = array();
	// 	DB::beginTransaction();
	// 	foreach ($primary_sale_summary as $key => $value) 
	// 	{
	// 		$check = DB::table('purchase_order')
	// 				->where('order_id',$value->existing_order)
	// 				->count();
	// 				// ->update(['order_id'=>$value->order_id]);
	// 		if($check>0)
	// 		{
	// 			// $delete 
	// 			$delete_order = DB::table('purchase_order')->where('order_id',$value->order_id)->delete();

	// 			$check = DB::table('purchase_order')
	// 				->where('order_id',$value->existing_order)
	// 				// ->count();
	// 				->update(['order_id'=>$value->order_id]);

	// 			$delete_details = DB::table('purchase_order_details')->where('order_id',$value->existing_order)->delete();

	// 				$detailsArr = [
	// 					'order_id' => $value->order_id,
	// 					'id' => $value->order_id,
	// 					'product_id' => $value->product_id,
	// 					'rate' => $value->rate,
	// 					'quantity' => $value->quantity,
	// 					'barcode' => $value->Barcode,
	// 					'scheme_qty' => $value->scheme_qty,
	// 					'cases' => $value->case,
	// 					'pcs' => $value->pcs,
	// 					'total_value' => $value->value,
	// 					'pr_rate' => $value->case_rate,
	// 					'company_id'=>$company_id,
	// 					'app_flag' => $value->app_flag,
	// 				];
	// 				$insertDetailsArr[] = $detailsArr;
	// 		}
	// 		else
	// 		{
	// 			$check = DB::table('purchase_order')
	// 				->where('order_id',$value->order_id)
	// 				->count();
	// 				// ->update(['order_id'=>$value->order_id]);
	// 			if($check>0)
	// 			{
	// 				$detailsArr = [
	// 					'order_id' => $value->order_id,
	// 					'id' => $value->order_id,
	// 					'product_id' => $value->product_id,
	// 					'rate' => $value->rate,
	// 					'quantity' => $value->quantity,
	// 					'barcode' => $value->Barcode,
	// 					'scheme_qty' => $value->scheme_qty,
	// 					'cases' => $value->case,
	// 					'pcs' => $value->pcs,
	// 					'total_value' => $value->value,
	// 					'pr_rate' => $value->case_rate,
	// 					'company_id'=>$company_id,
	// 					'app_flag' => $value->app_flag,
	// 				];
	// 				$insertDetailsArr[] = $detailsArr;
	// 			}
	// 			else
	// 			{
	// 				$myArr = [
	// 					'order_id'=>$value->order_id,
	// 					'id' => $value->order_id,
	// 					'dealer_id'=>$dealer_id,
	// 					'created_person_id'=>0,
	// 					'retailer_id'=>0,
	// 					'sale_date'=>$sale_date,
	// 					'created_date'=>$created_date,
	// 					'receive_date'=>$created_date,
	// 					'dispatch_date'=>$created_date,
	// 					'date_time'=>$date_time,
	// 					'battery_status'=>$battery_status,
	// 					'gps_status'=>$gps_status,
	// 					'lat'=>$lat,
	// 					'lng'=>$lng,
	// 					'address'=>$address,
	// 					'mcc_mnc_lac_cellid'=>$mcc_mnc_lac_cellid,
	// 					'company_id'=>$company_id,
	// 				];
	// 				$insert_order = DB::table('purchase_order')->insert($myArr);

	// 				$detailsArr = [
	// 					'order_id' => $value->order_id,
	// 					'id' => $value->order_id,
	// 					'product_id' => $value->product_id,
	// 					'rate' => $value->rate,
	// 					'quantity' => $value->quantity,
	// 					'barcode' => $value->Barcode,
	// 					'scheme_qty' => $value->scheme_qty,
	// 					'cases' => $value->case,
	// 					'pcs' => $value->pcs,
	// 					'total_value' => $value->value,
	// 					'pr_rate' => $value->case_rate,
	// 					'company_id'=>$company_id,
	// 					'app_flag' => $value->app_flag,
	// 				];
	// 				$insertDetailsArr[] = $detailsArr;
	// 			}

	// 		}

			
	// 	}
	// 	$insert_details = DB::table('purchase_order_details')->insert($insertDetailsArr);

	// 	if($insert_details )
	// 	{
	// 		DB::commit();
 //        	return response()->json(['response' =>True,'message'=>'Successfully Submitted!!']);        

	// 	}
	// 	else
	// 	{
	// 		DB::rollback();
 //        	return response()->json(['response' =>False,'message'=>'Not Submitted!!']);        

	// 	}

	// }
	// public function dms_banner_images(Request $request)
	// {
	// 	$validator=Validator::make($request->all(),[
            
	// 		"company_id"=>'required',
 //        ]);
 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
 //        }
 //        $company_id = $request->company_id;
 //        $data_omage = DB::table('banner_images')
 //        			->select('image_name','id')
 //        			->where('company_id',$company_id)
 //        			->where('status',1)
 //        			->get();
 //    	return response()->json(['response' =>True,'message'=>'Found!!','data'=>$data_omage]);        


	// }
	// public function dms_total_ecart(Request $request)
	// {
	// 	$validator=Validator::make($request->all(),[
 //            "state_id"=>'required',
	// 		"dealer_id"=>'required',
	// 		"company_id"=>'required',
 //        ]);
 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
 //        }
 //        $company_id = $request->company_id;
 //        $state_id = $request->state_id;
 //        $dealer_id = $request->dealer_id;
 //        $data = DB::table('purchase_order')
 //    		->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
	// 		->where('app_flag',1)
	// 		// ->where('purchase_order.state_id',$state_id)
	// 		->where('purchase_order.dealer_id',$dealer_id)
	// 		->where('purchase_order.company_id',$company_id)
	// 		->COUNT();
 //    	return response()->json(['response' =>True,'message'=>'Found!!','data'=>$data]);        

	// }
	// public function dms_primary_sale_report(Request $request)
	// {
	// 	$validator=Validator::make($request->all(),[
 //            "state_id"=>'required',
	// 		"dealer_id"=>'required',
	// 		"from_date"=>'required',
	// 		"to_date"=>'required',
	// 		"company_id"=>'required',
 //        ]);
 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
 //        }

 //        $state_id = $request->state_id;
 //        $dealer_id = $request->dealer_id;
 //        $from_date = $request->from_date;
 //        $to_date = $request->to_date;
 //        $company_id = $request->company_id;

 //       $data = DB::table('purchase_order')
 //       		->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
 //   			->join('dealer','dealer.id','=','purchase_order.dealer_id')
 //   			->select('purchase_order.order_id','sale_date','dms_order_reason_id','date_time')
 //   			->whereRaw("date_format(sale_date,'%Y-%m-%d')>='$from_date' AND date_format(sale_date,'%Y-%m-%d')<='$to_date' ")
 //   			->where('purchase_order.company_id',$company_id)
 //   			->where('purchase_order.dealer_id',$dealer_id)
 //   			->where('app_flag',2)
 //   			// ->where('purchase_order.order_id',$order_id)
 //   			->groupBy('purchase_order.order_id')
 //   			->get();
	// 	if(!empty($data))
	// 	{
	// 		$dms_reason_data = DB::table('_dms_reason')->where('status',1)->pluck('name','id');
	// 		$dms_reason_data_status_time = DB::table('_dms_reason')->where('status',1)->pluck('created_at','id');
	// 		$finalArr = [];
	// 		foreach ($data as $key => $value) 
	// 		{
	// 			$first_layer['order_id'] = $value->order_id;
	// 			$first_layer['sale_date'] = $value->sale_date;
	// 			$first_layer['supplier_name'] = 'Patanjali';
	// 			$first_layer['current_reason_status_id'] = $value->dms_order_reason_id;
	// 			$first_layer['current_reason_status'] = !empty($dms_reason_data[$value->dms_order_reason_id])?$dms_reason_data[$value->dms_order_reason_id]:'Order Placed';
	// 			$first_layer['reason_status_time'] = !empty($dms_reason_data[$value->dms_order_reason_id])?$dms_reason_data[$value->dms_order_reason_id]:$value->date_time;
	// 			$first_layer['details'] = DB::table('purchase_order')
	// 						       		->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
	// 						       		->join ('catalog_product','catalog_product.id','=','purchase_order_details.product_id')
	// 						   			->join('dealer','dealer.id','=','purchase_order.dealer_id')
	// 						   			->select('image_name','product_id','catalog_product.name as product_name','rate','quantity','cases','pcs','pr_rate as case_rate','image_name')
	// 						   			->whereRaw("date_format(sale_date,'%Y-%m-%d')>='$from_date' AND date_format(sale_date,'%Y-%m-%d')<='$to_date'")
	// 						   			->where('purchase_order.company_id',$company_id)
	// 						   			->where('purchase_order.dealer_id',$dealer_id)
	// 						   			->where('purchase_order.order_id',$value->order_id)
	// 						   			->groupBy('purchase_order_details.id','product_id')
	// 						   			->get();

	//    			$first_layer['reason_log'] = DB::table('dms_order_reason_log')
	//    										->join('_dms_reason','_dms_reason.id','=','dms_order_reason_log.dms_reason_id')
	//    										->select('_dms_reason.name as status_name','order_id','dms_order_reason_log.id')
	//    										->where('order_id',$value->order_id)
	//    										->where('dms_order_reason_log.company_id',$company_id)
	//    										->get();
	// 			$finalArr[] = $first_layer; 
	// 		}	



 //    		return response()->json(['response' =>True,'message'=>'Found!!','data'=>$finalArr]);        
 //    	}
 //    	else
 //    	{
 //    		return response()->json(['response' =>False,'message'=>'NotFound!!','data'=>array()]);        
 //    	}
	// }

	// public function dms_dealer_stock(Request $request)
	// {
	// 	$validator=Validator::make($request->all(),[
	// 		"dealer_id"=>'required',
	// 		"company_id"=>'required',
 //        ]);
 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
 //        }
 //        $company_id = $request->company_id;
 //        $dealer_id = $request->dealer_id;

 //        $details_array = DB::table('catalog_product')
 //    			->select('id as product_id','name as product_name')
 //    			->where('catalog_product.company_id',$company_id)
 //    			->get();

	// 	$stock_cases = DB::table('dealer_balance_stock')
	// 				->where('dealer_id',$dealer_id)
	// 				->where('company_id',$company_id)
	// 				->groupBy('product_id')
	// 				->pluck(DB::raw("SUM(stock_case)"),'product_id');
	// 	$stock_qty = DB::table('dealer_balance_stock')
	// 				->where('dealer_id',$dealer_id)
	// 				->where('company_id',$company_id)
	// 				->groupBy('product_id')
	// 				->pluck(DB::raw("SUM(stock_qty)"),'product_id');
	// 	foreach ($details_array as $key => $value) 
	// 	{
	// 		$details['product_id'] = $value->product_id;
	// 		$details['product_name'] = $value->product_name;
	// 		$details['stock_qty'] = !empty($stock_qty[$value->product_id])?$stock_qty[$value->product_id]:'0';
	// 		$details['stock_case'] = !empty($stock_cases[$value->product_id])?$stock_cases[$value->product_id]:'0';
	// 		$details['dealer_id'] = $dealer_id;
	// 		$finalArr[]	= $details;
	// 	}

	// 	return response()->json(['response' =>True,'message'=>'NotFound!!','data'=>$finalArr]);        

	// }

	// public function dms_cart_product_update_patanjali(Request $request)
	// {
	// 	$validator=Validator::make($request->all(),[
	// 		"dealer_id"=>'required',
	// 		"company_id"=>'required',
	// 		"payment_mode_id"=>'required',
	// 		"amount"=>'required',
	// 		"remarks"=>'required',
	// 		"date"=>'required',
	// 		"time"=>'required',
	// 		"primary_sale_summary"=>'required',
 //        ]);
 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
 //        }
 //        $primary_sale_summary = json_decode($request->primary_sale_summary);

 //        if(!empty($primary_sale_summary))
 //        {
 //        	foreach ($primary_sale_summary as $key => $value) 
	//         {
	//         	$myArr = [
	//         		'rate' => $value->rate,
	//         		'cases' => $value->case,
	//         		'pcs' => $value->pcs,
	//         		'quantity' => $value->pcs,
	//         		'pr_rate'=>$value->case_rate,
	//         		'scheme_qty'=> $value->sch_qty,
	//         		'total_value'=> $value->value,
	//         		'app_flag'=> $value->app_flag,

	//         	];
	//         	$update_data = DB::table('purchase_order_details')
	//     					->where('order_id',$value->order_id)
	//     					->where('product_id',$value->product_id)
	//     					->update($myArr);
	        
	// 	        $paymentArr = [
	// 	        	'dealer_id'=> $request->dealer_id,
	// 	        	'company_id'=> $request->company_id,
	// 	        	'order_id'=>$value->order_id,
	// 	        	'payment_mode'=> $request->payment_mode_id,
	// 	        	'amount'=> $request->amount,
	// 	        	'remarks'=> $request->remarks,
	// 	        	'payment_date'=> $request->date,
	// 	        	'payment_time'=> $request->time,
	// 	        	'bank_branch'=> 'NA',
	// 	        	'cheque_no'=> '0',
	// 	        	'cheque_date'=> '0000-00-00',
	// 	        	'trans_no'=> '0',
	// 	        	'trans_date'=> $request->date,
	// 	        	'remarks'=>'NA',
	// 	        	'user_id'=>'0',

	// 	        ];
	//     	}
	//         $insert_payment = DB::table('payment_collect_dealer')->insert($paymentArr);
	// 		return response()->json(['response' =>True,'message'=>'Submitted Successfully!!']);        

 //        }
	// 	return response()->json(['response' =>False,'message'=>'JSON Blank!!']);        

	// }

	// public function dms_send_empty_jar_data(Request $request)
	// {
	// 	$validator=Validator::make($request->all(),[
	// 		"jar_quantity"=>'required',
	// 		"company_id"=>'required',
	// 		"dealer_id"=>'required',
	// 		"date"=>'required',
	// 		"time"=>'required',
	// 		'order_id'=>'required',
		
 //        ]);
 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
 //        }

 //        $myArr = [
 //        	'jar_quantity' =>$value->jar_quantity,
 //        	'dealer_id' =>$value->dealer_id,
 //        	'date' =>$value->date,
 //        	'time' =>$value->time,
 //        	'company_id' =>$value->company_id,
 //        	'order_id' =>$value->order_id,
 //        	'server_date_time' =>date("Y-m-d H:i:s"),

 //        ];

 //        $insert = DB::table('dms_dealer_return_quantity')->insert($myArr);
	// 	return response()->json(['response' =>True,'message'=>'Submitted Successfully!!']);        


	// }
	// public function dms_receive_product_update(Request $request)
	// {
	// 	$validator=Validator::make($request->all(),[
	// 		'primary_sale_summary'=>'required',
 //        ]);
 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
 //        }
 //        $primary_sale_summary = json_decode($request->primary_sale_summary);
 //        foreach ($primary_sale_summary as $key => $value) {
 //        	# code...
        
	//         $myArr = [
	//         	'order_id' =>$value->order_id,
	//         	'dealer_id' =>$value->dealer_id,
	//         	'company_id' =>$value->company_id,
	//         	'product_id' =>$value->product_id,
	//         	'case_rate' =>$value->case_rate,
	//         	'case_fullfillment_qty' =>$value->case_fullfillment_qty,
	//         	'remarks' =>!empty($value->remarks)?$value->remarks:'NA',
	//         	'damage_qty' =>!empty($value->damage_qty)?$value->damage_qty:'NA',
	//         	'server_date_time' =>date('Y-m-d H:i:s'),
	//         	'status' =>1,

	//         ];
	//         $finalArr[] = $myArr;
	//     }
 //        $insert = DB::table('dms_order_recieved_dealer')->insert($finalArr);
	// 	return response()->json(['response' =>True,'message'=>'Submitted Successfully!!']);        
        

	// }
	// public function complaint_feedback_array(Request $request)
	// {
	// 	$validator=Validator::make($request->all(),[
	// 		'company_id'=>'required',
 //        ]);
 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
 //        }

 //        $data = DB::table('_complaint_type')
 //        		->select('id','name')
 //        		// ->where('status',1)
 //        		->get();

	// 	return response()->json(['response' =>True,'data'=>$data,'message'=>'Found!!']);        



	// }

	// public function complaint_feedback_submit(Request $request)
	// {
	// 	$validator=Validator::make($request->all(),[
	// 		'company_id'=>'required',
	// 		'order_id'=>'required',
	// 		'dealer_id'=>'required',
	// 		'feedback_complaint_id'=>'required',
	// 		'remarks'=>'required',
	// 		'date'=>'required',
	// 		'time'=>'required',
 //        ]);
 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
 //        }

 //        $insert_data = DB::table('complaint_feedback_data')->insert([

 //        	'company_id'=>$request->company_id,
 //        	'order_id'=> $request->order_id,
 //        	'dealer_id'=>$request->dealer_id,
 //        	'retailer_id'=>0,
 //        	'date'=>$request->date,
 //        	'time'=>$request->time,
 //        	'remarks'=>$request->remarks,
 //        	'complaint_feedback_id'=>$request->feedback_complaint_id,
 //        	'server_date_time'=>date('Y-m-d H:i:s'),

 //        ]);
	// 	return response()->json(['response' =>True,'data'=>$data,'message'=>'Submitted Successfully!!']);        


	// }

	// public function dms_order_cancel_reason(Request $request)
	// {
	// 	$validator=Validator::make($request->all(),[
	// 		'company_id'=>'required',
 //        ]);
 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
 //        }

 //        $data = DB::table('dms_order_cancel_reason')
 //        		->select('id','name as reason')
 //        		->where('status',1)
 //        		->where('company_id',$request->company_id)
 //        		->get();

	// 	return response()->json(['response' =>True,'data'=>$data,'message'=>'Found!!']);        



	// }

	// public function dms_cancel_order_update(Request $request)
	// {
	// 	$validator=Validator::make($request->all(),[
	// 		'company_id'=>'required',
	// 		'order_id'=>'required',
	// 		'status_id'=>'required',
	// 		'order_cancel_reason_id'=>'required',
	// 		'remarks'=>'required',
	// 		'date_time'=>'required',
 //        ]);
 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
 //        }

 //        $fisrt_insert = DB::table('dms_order_reason_log')->insert([
 //        				'order_id' =>$request->order_id,
 //        				'dms_reason_id' =>$request->dms_reason_id,
 //        				'date' =>date('Y-m-d'),
 //        				'time' =>date('H:i:s'),
 //        				'company_id' =>$request->company_id,
 //        				'server_date_time' =>date('Y-m-d H:i:s'),
 //        		]);
 //    	$layer_updte = DB::table('purchase_order')
 //    				->where('order_id',$request->order_id)
 //    				->update(['dms_order_reason_id'=>$request->status_id,'cancel_order_reason_id'=>$request->order_cancel_reason_id,'remarks'=>$request->remarks,'date_time'=>$request->date_time]);

	// 	if($layer_updte && $fisrt_insert)
	// 	{
	// 		return response()->json(['response' =>True,'message'=>'Submitted!!']);        

	// 	}
	// 	else
	// 	{
	// 		return response()->json(['response' =>False,'message'=>'Not Submitted!!']);        

	// 	}
	// }

	// public function dms_counter_sale_submit(Request $request)
	// {
	// 	$validator=Validator::make($request->all(),[
 //            "order_id"=>'required',
	// 		"dealer_id"=>'required',
	// 		"sale_date"=>'required',
	// 		"created_date"=>'required',
	// 		"date_time"=>'required',
	// 		"battery_status"=>'required',
	// 		"gps_status"=>'required',
	// 		"lat"=>'required',
	// 		"lng"=>'required',
	// 		"address"=>'required',
	// 		"mcc_mnc_lac_cellid"=>'required',
	// 		"user_id"=>'required',
	// 		"company_id"=>'required',
 //        ]);
 //        if($validator->fails())
 //        {
 //            return response()->json(['response'=>False,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
 //        }

	// 	$order_id = $request->order_id;
	// 	$dealer_id = $request->dealer_id;
	// 	$sale_date = $request->sale_date;
	// 	$created_date = $request->created_date;
	// 	$date_time = $request->date_time;
	// 	$battery_status = $request->battery_status;
	// 	$gps_status = $request->gps_status;
	// 	$lat = $request->lat;
	// 	$lng = $request->lng;
	// 	$address = $request->address;
	// 	$mcc_mnc_lac_cellid = $request->mcc_mnc_lac_cellid;
	// 	$user_id = $request->user_id;
	// 	$company_id = $request->company_id;
	// 	$primary_sale_summary = json_decode($request->primary_sale_summary);
	// 	DB::beginTransaction();
	// 	$myArr = [
	// 			'order_id'=>$order_id,
	// 			'dealer_id'=>$dealer_id,
	// 			'created_by_person'=>0,
	// 			'retailer_id'=>0,
	// 			'sale_date'=>$sale_date,
	// 			'created_date'=>$created_date,
	// 			'date_time'=>$date_time,
	// 			'battery_status'=>$battery_status,
	// 			'gps_status'=>$gps_status,
	// 			'lat'=>$lat,
	// 			'lng'=>$lng,
	// 			'address'=>$address,
	// 			'mcc_mnc_lac_cellid'=>$mcc_mnc_lac_cellid,
	// 			'company_id'=>$company_id,
	// 			'server_date'=>date('Y-m-d H:i:s'),

	// 		];

	// 	foreach ($primary_sale_summary as $key => $value) {

	// 			$detailsArr = [
	// 				'order_id' => $value->order_id,
	// 				'product_id' => $value->product_id,
	// 				'rate' => $value->rate,
	// 				'pcs_rate' => $value->rate,
	// 				'quantity' => $value->quantity,
	// 				'barcode' => $value->Barcode,
	// 				'scheme_qty' => $value->scheme_qty,
	// 				'cases' => $value->case,
	// 				'pcs' => $value->pcs,
	// 				'value' => $value->value,
	// 				'case_rate' => $value->case_rate,
	// 				'company_id'=>$company_id,
	// 				'app_flag' => $value->app_flag,
	// 				'created_by'=>0,
	// 				'server_date_time'=>date('Y-m-d H:i:s'),
	// 			];
	// 			$insertDetailsArr[] = $detailsArr;
	// 	}
	// 	$insert_order = DB::table('counter_sale_summary')->insert($myArr);
	// 	$insert_order_details = DB::table('counter_sale_details')->insert($detailsArr);
	// 	if($insert_order && $insert_order_details)
	// 	{
	// 		DB::commit();
	// 		return response()->json(['response' =>True,'message'=>'Submitted!!']);        


	// 	}
	// 	else
	// 	{
	// 		DB::rollback();
	// 		return response()->json(['response' =>False,'message'=>'Not Submitted!!']);        

	// 	}


	// }
	
}