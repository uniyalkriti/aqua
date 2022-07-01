<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Person;
use App\UserDetail;
use App\Company;
use App\JuniorData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use App\SchemePlanDetails;
use App\SchemePlan;
use App\UserIncentiveDetails;
use App\UserIncentiveSlabs;
use App\UserIncentiveRoleDistribution;
use Validator;
use DB;
use Image;

class SchemeApiController extends Controller
{
    public $successStatus = 200;
    public $response_true = True;
    public $response_false = False;

    ##................................retailer scheme starts here .....................###
	public function scheme_retailer_calculation(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'retailer_id'=>'required',
            'company_id'=>'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],201);
        }
        $company_id = $request->company_id;
        $retailer_id = $request->retailer_id;
		$fetch_plan = DB::table('scheme_assign_retailer')
					->where('status',1)
					->where('company_id',$company_id)
                    ->where('retailer_id',$retailer_id)
                    ->orderBy('id','DESC')
					->first();
        // dd($fetch_plan);
		
		if(!empty($fetch_plan))
		{
            $plan_assigned_from_date = $fetch_plan->plan_assigned_from_date;
		    $plan_assigned_to_date = $fetch_plan->plan_assigned_to_date;
            $fetch_scheme_details = SchemePlan::where('status',1)->where('id',$fetch_plan->plan_id)->first();
            $fetch_scheme_slabs = SchemePlanDetails::where('scheme_id',$fetch_scheme_details->id)->orderBy('id')->get();
			// dd($fetch_scheme_details);
			if(!empty($fetch_scheme_details))
			{
				$plan_name = $fetch_scheme_details->scheme_name; 

				if($fetch_scheme_details->scheme_category_status==1) // vps value per sale 
				{
					if($fetch_scheme_details->vs_status==1) // as total sale value vps
					{   

                        // dd($fetch_scheme_slabs);
						if(!empty($fetch_scheme_slabs))
						{
							
							foreach ($fetch_scheme_slabs as $slab_key => $slab_value) 
							{
                                // dd($slab_value);
								$sale_value_range_first = $slab_value->sale_value_range_first;
								$sale_value_range_last = $slab_value->sale_value_range_last;

								if($slab_value->sale_unit==4) // as per range
								{
									if($slab_value->incentive_type==1) // 1 as percentage
									{
                                        $level_name = '';

                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->select(DB::raw("SUM(rate*quantity) as sale_value"))
                                                        ->where('retailer_id',$retailer_id)
                                                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                        ->where('user_sales_order.company_id',$company_id)
                                                        ->first();

                                        if(empty($sale_data_fetch))
                                        {
                                            $role_wise_sum_array [] = 0;
                                        }
                                        // comparison parts starts here
                                        if($sale_value_range_last=='~')
                                        {
                                            // $check_comparision = UserIncentiveSlabs::where('plan_isale_value_range_first','>',$sale_data_fetch->sale_value)
                                            //                 ->first();
                                            
                                            if($sale_value_range_first < $sale_data_fetch->sale_value)
                                            {
                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                $temp_array_for_return = 'TRUE';

                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }
                                        else
                                        {

                                            if($sale_value_range_first < $sale_data_fetch->sale_value && $sale_value_range_last > $sale_data_fetch->sale_value)
                                            {
                                            // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }

                                        if($temp_array_for_return == 'TRUE')
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                            $level_name = 'Reward Percentage';

                                            return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                        }
                                        else
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array);
                                            $level_name = 'Reward Percentage';
                                        }

									} // percentage ends here 
									elseif($slab_value->incentive_type==2) // 2 as amount
									{
                                        $level_name = '';

                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->select(DB::raw("SUM(rate*quantity) as sale_value"))
                                                        ->where('retailer_id',$retailer_id)
                                                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                        ->where('user_sales_order.company_id',$company_id)
                                                        ->first();

                                        if(empty($sale_data_fetch))
                                        {
                                            $role_wise_sum_array [] = 0;
                                        }
                                        // comparison parts starts here
                                        if($sale_value_range_last=='~')
                                        {
                                            // $check_comparision = UserIncentiveSlabs::where('plan_isale_value_range_first','>',$sale_data_fetch->sale_value)
                                            //                 ->first();
                                            
                                            if($sale_value_range_first < $sale_data_fetch->sale_value)
                                            {
                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                $temp_array_for_return = 'TRUE';

                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }
                                        else
                                        {

                                            if($sale_value_range_first < $sale_data_fetch->sale_value && $sale_value_range_last > $sale_data_fetch->sale_value)
                                            {
                                            // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }

                                        if($temp_array_for_return == 'TRUE')
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                            $level_name = 'Reward Amount';

                                            return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                        }
                                        else
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array);
                                            $level_name = 'Reward Amount';
                                        }
									} // amount ends here 
									elseif($slab_value->incentive_type==3) // 3 as point 
									{
                                        $level_name = '';

                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->select(DB::raw("SUM(rate*quantity) as sale_value"))
                                                        ->where('retailer_id',$retailer_id)
                                                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                        ->where('user_sales_order.company_id',$company_id)
                                                        ->first();

                                        if(empty($sale_data_fetch))
                                        {
                                            $role_wise_sum_array [] = 0;
                                        }
                                        // comparison parts starts here
                                        if($sale_value_range_last=='~')
                                        {
                                            // $check_comparision = UserIncentiveSlabs::where('plan_isale_value_range_first','>',$sale_data_fetch->sale_value)
                                            //                 ->first();
                                            
                                            if($sale_value_range_first < $sale_data_fetch->sale_value)
                                            {
                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                $temp_array_for_return = 'TRUE';

                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }
                                        else
                                        {

                                            if($sale_value_range_first < $sale_data_fetch->sale_value && $sale_value_range_last > $sale_data_fetch->sale_value)
                                            {
                                            // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }

                                        if($temp_array_for_return == 'TRUE')
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                            $level_name = 'Reward Point';

                                            return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                        }
                                        else
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array);
                                            $level_name = 'Reward Point';
                                        }
									} // as point 
									else
									{
										return response()->json([ 'response' =>False,'result'=>[]]);
									}
								}
								else
								{
									return response()->json([ 'response' =>False,'result'=>[]]);
								}

                                

                            } // as foreach ends here 
                            
                            return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);

						} // !empty fectch_incentive_slabs if ends here 
						else
						{
							return response()->json([ 'response' =>False,'result'=>[]]);
						}

					} // total sale value ends here 
					elseif($fetch_scheme_details->vs_status==2)  // as item wised vps 
					{
                        // run query 
                        
                        foreach ($fetch_scheme_slabs as $i_v_key => $slab_value) 
                        {
                            if($slab_value->sale_unit==4) // as per range
                            {
                            
                                $item_product_details = explode(',',$slab_value->product_id);

                                $sale_value_range_first = $slab_value->sale_value_range_first;
                                $sale_value_range_last = $slab_value->sale_value_range_last;
                                
                                    if($slab_value->incentive_type==1) // 1 as percentage
									{
                                        $level_name = '';
                                    
                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->select(DB::raw("SUM(rate*quantity) as sale_value"))
                                                        ->where('retailer_id',$retailer_id)
                                                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                        ->whereIn('product_id',$item_product_details)
                                                        ->where('user_sales_order.company_id',$company_id)
                                                        ->first();
                                        if(empty($sale_data_fetch))
                                        {
                                            $role_wise_sum_array [] = 0;
                                            $temp_array_for_return = 'FALSE';
                                        }
                                        
                                        // comparison parts starts here
                                        if($sale_value_range_last=='~')
                                        {
                                            
                                            if($sale_value_range_first < $sale_data_fetch->sale_value)
                                            {
                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                $temp_array_for_return = 'TRUE';

                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }
                                        else
                                        {
                                            
                                            if($sale_value_range_first < $sale_data_fetch->sale_value && $sale_value_range_last > $sale_data_fetch->sale_value)
                                            {
                                            // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }
										
                                        if($temp_array_for_return == 'TRUE')
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                            $level_name = 'Reward Percentage';

                                            return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                        }
                                        else
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array);
                                            $level_name = 'Reward Percentage';
                                        }

									} // percentage ends here
									elseif($slab_value->incentive_type==2) // 2 as amount
									{
                                        $level_name = '';
                                    
                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->select(DB::raw("SUM(rate*quantity) as sale_value"))
                                                        ->where('retailer_id',$retailer_id)
                                                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                        ->whereIn('product_id',$item_product_details)
                                                        ->where('user_sales_order.company_id',$company_id)
                                                        ->first();
                                        if(empty($sale_data_fetch))
                                        {
                                            $role_wise_sum_array [] = 0;
                                            $temp_array_for_return = 'FALSE';
                                        }
                                        
                                        // comparison parts starts here
                                        if($sale_value_range_last=='~')
                                        {
                                            
                                            if($sale_value_range_first < $sale_data_fetch->sale_value)
                                            {
                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                $temp_array_for_return = 'TRUE';

                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }
                                        else
                                        {
                                            
                                            if($sale_value_range_first < $sale_data_fetch->sale_value && $sale_value_range_last > $sale_data_fetch->sale_value)
                                            {
                                            // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }
										
                                        if($temp_array_for_return == 'TRUE')
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                            $level_name = 'Reward Amount';

                                            return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                        }
                                        else
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array);
                                            $level_name = 'Reward Amount';
                                        }

									} // Amount ends here 
									elseif($slab_value->incentive_type==3) // 3 as point 
									{
                                        $level_name = '';
                                    
                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->select(DB::raw("SUM(rate*quantity) as sale_value"))
                                                        ->where('retailer_id',$retailer_id)
                                                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                        ->whereIn('product_id',$item_product_details)
                                                        ->where('user_sales_order.company_id',$company_id)
                                                        ->first();
                                        if(empty($sale_data_fetch))
                                        {
                                            $role_wise_sum_array [] = 0;
                                            $temp_array_for_return = 'FALSE';
                                        }
                                        
                                        // comparison parts starts here
                                        if($sale_value_range_last=='~')
                                        {
                                            
                                            if($sale_value_range_first < $sale_data_fetch->sale_value)
                                            {
                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                $temp_array_for_return = 'TRUE';

                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }
                                        else
                                        {
                                            
                                            if($sale_value_range_first < $sale_data_fetch->sale_value && $sale_value_range_last > $sale_data_fetch->sale_value)
                                            {
                                            // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }
										
                                        if($temp_array_for_return == 'TRUE')
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                            $level_name = 'Reward Point';

                                            return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                        }
                                        else
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array);
                                            $level_name = 'Reward Point';
                                        }
									} // point ends here 
									else
									{
										return response()->json([ 'response' =>False,'result'=>[]]);
									}
                            }
                            else
                            {
                                return response()->json([ 'response' =>False,'result'=>[]]);
                            }
                            
                        }// for each rnds here vps item vised
                        
                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                    

					} // ends here item wised sales in vps 
					else
					{
						return response()->json([ 'response' =>False,'result'=>[]]);
					}
				}

				elseif($fetch_scheme_details->scheme_category_status==2) // qs quantity sale 
				{
					if($fetch_scheme_details->vs_status==2)
					{
                        foreach ($fetch_scheme_slabs as $i_q_key => $slab_value) 
                        {
                            $item_product_details = explode(',',$slab_value->product_id);
                            $sale_value_range_first = $slab_value->sale_value_range_first;
                            $sale_value_range_last = $slab_value->sale_value_range_last;

                            if($slab_value->sale_unit==1) // weight starts here 
                            {
                                if($slab_value->incentive_type==1) // 1 as percentage
                                {
                                    $level_name = '';
                                    $sale_data_fetch = DB::table('user_sales_order')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                                    ->select(DB::raw("SUM((catalog_product.weight*quantity)/1000) as total_weight"))
                                                    ->where('retailer_id',$retailer_id)
                                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                    ->whereIn('product_id',$item_product_details)
                                                    ->where('user_sales_order.company_id',$company_id)
                                                    ->first();
                                    if(empty($sale_data_fetch))
                                    {
                                        $role_wise_sum_array [] = 0;
                                        $temp_array_for_return = 'FALSE';
                                    }
                                        
                                    // comparison parts starts here
                                    if($sale_value_range_last=='~')
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_weight)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    else
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_weight && $sale_value_range_last > $sale_data_fetch->total_weight)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                      
                                    if($temp_array_for_return == 'TRUE')
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                        $level_name = 'Reward Percentage';

                                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                    }
                                    else
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array);
                                        $level_name = 'Reward Percentage';
                                    }

                                } // as percentage ends here 

                                if($slab_value->incentive_type==2) // 2 as Amount
                                {
                                    $level_name = '';
                                    $sale_data_fetch = DB::table('user_sales_order')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                                    ->select(DB::raw("SUM((catalog_product.weight*quantity)/1000) as total_weight"))
                                                    ->where('retailer_id',$retailer_id)
                                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                    ->whereIn('product_id',$item_product_details)
                                                    ->where('user_sales_order.company_id',$company_id)
                                                    ->first();
                                    if(empty($sale_data_fetch))
                                    {
                                        $role_wise_sum_array [] = 0;
                                        $temp_array_for_return = 'FALSE';
                                    }
                                        
                                    // comparison parts starts here
                                    if($sale_value_range_last=='~')
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_weight)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    else
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_weight && $sale_value_range_last > $sale_data_fetch->total_weight)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                      
                                    if($temp_array_for_return == 'TRUE')
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                        $level_name = 'Reward Amount';

                                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                    }
                                    else
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array);
                                        $level_name = 'Reward Amount';
                                    }
                                } // Amount ends here 

                                if($slab_value->incentive_type==3) // 3 as point
                                {
                                    $level_name = '';
                                    $sale_data_fetch = DB::table('user_sales_order')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                                    ->select(DB::raw("SUM((catalog_product.weight*quantity)/1000) as total_weight"))
                                                    ->where('retailer_id',$retailer_id)
                                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                    ->whereIn('product_id',$item_product_details)
                                                    ->where('user_sales_order.company_id',$company_id)
                                                    ->first();
                                    if(empty($sale_data_fetch))
                                    {
                                        $role_wise_sum_array [] = 0;
                                        $temp_array_for_return = 'FALSE';
                                    }
                                        
                                    // comparison parts starts here
                                    if($sale_value_range_last=='~')
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_weight)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    else
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_weight && $sale_value_range_last > $sale_data_fetch->total_weight)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                      
                                    if($temp_array_for_return == 'TRUE')
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                        $level_name = 'Reward Amount';

                                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                    }
                                    else
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array);
                                        $level_name = 'Reward Amount';
                                    }
                                } // points ends here 
                            } // weight ends here
                            if($slab_value->sale_unit==2) // cases starts here 
                            {
                                if($slab_value->incentive_type==1) // 1 as percentage
                                {
                                    $level_name = '';
                                    $sale_data_fetch = DB::table('user_sales_order')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                                    ->select(DB::raw("SUM(quantity/quantity_per_case) as total_cases"))
                                                    ->where('retailer_id',$retailer_id)
                                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                    ->whereIn('product_id',$item_product_details)
                                                    ->where('user_sales_order.company_id',$company_id)
                                                    ->first();
                                    if(empty($sale_data_fetch))
                                    {
                                        $role_wise_sum_array [] = 0;
                                        $temp_array_for_return = 'FALSE';
                                    }
                                        
                                    // comparison parts starts here
                                    if($sale_value_range_last=='~')
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_cases)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    else
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_cases && $sale_value_range_last > $sale_data_fetch->total_cases)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                      
                                    if($temp_array_for_return == 'TRUE')
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                        $level_name = 'Reward Percentage';

                                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                    }
                                    else
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array);
                                        $level_name = 'Reward Percentage';
                                    }

                                } // as percentage ends here 

                                if($slab_value->incentive_type==2) // 2 as Amount
                                {
                                    $level_name = '';
                                    $sale_data_fetch =DB::table('user_sales_order')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                                    ->select(DB::raw("SUM(quantity/quantity_per_case) as total_cases"))
                                                    ->where('retailer_id',$retailer_id)
                                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                    ->whereIn('product_id',$item_product_details)
                                                    ->where('user_sales_order.company_id',$company_id)
                                                    ->first();
                                    if(empty($sale_data_fetch))
                                    {
                                        $role_wise_sum_array [] = 0;
                                        $temp_array_for_return = 'FALSE';
                                    }
                                        
                                    // comparison parts starts here
                                    if($sale_value_range_last=='~')
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_cases)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    else
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_cases && $sale_value_range_last > $sale_data_fetch->total_cases)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                      
                                    if($temp_array_for_return == 'TRUE')
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                        $level_name = 'Reward Amount';

                                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                    }
                                    else
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array);
                                        $level_name = 'Reward Amount';
                                    }
                                } // Amount ends here 

                                if($slab_value->incentive_type==3) // 3 as point
                                {
                                    $level_name = '';
                                    $sale_data_fetch = DB::table('user_sales_order')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                                    ->select(DB::raw("SUM(quantity/quantity_per_case) as total_cases"))
                                                    ->where('retailer_id',$retailer_id)
                                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                    ->whereIn('product_id',$item_product_details)
                                                    ->where('user_sales_order.company_id',$company_id)
                                                    ->first();
                                    if(empty($sale_data_fetch))
                                    {
                                        $role_wise_sum_array [] = 0;
                                        $temp_array_for_return = 'FALSE';
                                    }
                                        
                                    // comparison parts starts here
                                    if($sale_value_range_last=='~')
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_cases)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    else
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_cases && $sale_value_range_last > $sale_data_fetch->total_cases)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                      
                                    if($temp_array_for_return == 'TRUE')
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                        $level_name = 'Reward Amount';

                                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                    }
                                    else
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array);
                                        $level_name = 'Reward Amount';
                                    }
                                } // points ends here 
                            } // cases ends here 
                            if($slab_value->sale_unit==3) // pcs starts here 
                            {
                                if($slab_value->incentive_type==1) // 1 as percentage
                                {
                                    $level_name = '';
                                    $sale_data_fetch = DB::table('user_sales_order')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                    ->select(DB::raw("SUM(quantity) as total_quantity"))
                                                    ->where('retailer_id',$retailer_id)
                                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                    ->whereIn('product_id',$item_product_details)
                                                    ->where('user_sales_order.company_id',$company_id)
                                                    ->first();
                                    if(empty($sale_data_fetch))
                                    {
                                        $role_wise_sum_array [] = 0;
                                        $temp_array_for_return = 'FALSE';
                                    }
                                    // comparison parts starts here
                                    if($sale_value_range_last=='~')
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_quantity)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    else
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_quantity && $sale_value_range_last > $sale_data_fetch->total_quantity)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';  
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    if($temp_array_for_return == 'TRUE')
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                        $level_name = 'Reward Percentage';

                                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                    }
                                    else
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array);
                                        $level_name = 'Reward Percentage';
                                    }

                                } // percentage ends here 

                                if($slab_value->incentive_type==2) // 2 as Amount
                                {
                                    $level_name = '';
                                    $sale_data_fetch = DB::table('user_sales_order')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                    ->select(DB::raw("SUM(quantity) as total_quantity"))
                                                    ->where('retailer_id',$retailer_id)
                                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                    ->whereIn('product_id',$item_product_details)
                                                    ->where('user_sales_order.company_id',$company_id)
                                                    ->first();
                                    if(empty($sale_data_fetch))
                                    {
                                        $role_wise_sum_array [] = 0;
                                        $temp_array_for_return = 'FALSE';
                                    }
                                    // comparison parts starts here
                                    if($sale_value_range_last=='~')
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_quantity)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    else
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_quantity && $sale_value_range_last > $sale_data_fetch->total_quantity)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';  
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    if($temp_array_for_return == 'TRUE')
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                        $level_name = 'Reward Amount';

                                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                    }
                                    else
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array);
                                        $level_name = 'Reward Amount';
                                    }
                                }
                                if($slab_value->incentive_type==3) // 3 as point
                                {
                                    $level_name = '';
                                    $sale_data_fetch = DB::table('user_sales_order')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                    ->select(DB::raw("SUM(quantity) as total_quantity"))
                                                    ->where('retailer_id',$retailer_id)
                                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                    ->whereIn('product_id',$item_product_details)
                                                    ->where('user_sales_order.company_id',$company_id)
                                                    ->first();
                                    if(empty($sale_data_fetch))
                                    {
                                        $role_wise_sum_array [] = 0;
                                        $temp_array_for_return = 'FALSE';
                                    }
                                    // comparison parts starts here
                                    if($sale_value_range_last=='~')
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_quantity)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    else
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_quantity && $sale_value_range_last > $sale_data_fetch->total_quantity)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';  
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    if($temp_array_for_return == 'TRUE')
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                        $level_name = 'Reward Amount';

                                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                    }
                                    else
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array);
                                        $level_name = 'Reward Amount';
                                    }
                                } // point ends here 
                            } // pcs end shere 

                        }
                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
					}
					else
					{
						return response()->json([ 'response' =>False,'result'=>[]]);
					}
				}
				elseif($fetch_scheme_details->plan_category_status==6) // other 
				{
					// depend on condition 
				}
				else
				{
					return response()->json([ 'response' =>False,'result'=>[]]);
				}
			} // !empty fectch_incentive_details if ends here 
			else
			{
				return response()->json([ 'response' =>False,'result'=>[]]);
			}
		} // !empty	fetch_plan if enda here 
		else
		{
			return response()->json([ 'response' =>False,'result'=>[]]);

		}		
    } // incentive part function ends here

    ##.........................retailer scheme ends here ........................###
    
    ##................dealer scheme starts here .................................###
    public function scheme_dealer_calculation(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'dealer_id'=>'required',
            'company_id'=>'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],201);
        }
        $company_id = $request->company_id;
        $dealer_id = $request->dealer_id;
		$fetch_plan = DB::table('scheme_assign_retailer')
					->where('status',1)
					->where('company_id',$company_id)
                    ->where('dealer_id',$dealer_id)
                    ->orderBy('id','DESC')
					->first();
        // dd($fetch_plan);
		
		if(!empty($fetch_plan))
		{
            $plan_assigned_from_date = $fetch_plan->plan_assigned_from_date;
		    $plan_assigned_to_date = $fetch_plan->plan_assigned_to_date;
            $fetch_scheme_details = SchemePlan::where('status',1)->where('id',$fetch_plan->plan_id)->first();
            $fetch_scheme_slabs = SchemePlanDetails::where('scheme_id',$fetch_scheme_details->id)->orderBy('id')->get();
			// dd($fetch_scheme_details);
			if(!empty($fetch_scheme_details))
			{
				$plan_name = $fetch_scheme_details->scheme_name; 

				if($fetch_scheme_details->scheme_category_status==1) // vps value per sale 
				{
					if($fetch_scheme_details->vs_status==1) // as total sale value vps
					{   

                        // dd($fetch_scheme_slabs);
						if(!empty($fetch_scheme_slabs))
						{
							
							foreach ($fetch_scheme_slabs as $slab_key => $slab_value) 
							{
                                // dd($slab_value);
								$sale_value_range_first = $slab_value->sale_value_range_first;
								$sale_value_range_last = $slab_value->sale_value_range_last;

								if($slab_value->sale_unit==4) // as per range
								{
									if($slab_value->incentive_type==1) // 1 as percentage
									{
                                        $level_name = '';

                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->select(DB::raw("SUM(rate*quantity) as sale_value"))
                                                        ->where('dealer_id',$dealer_id)
                                                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                        ->where('user_sales_order.company_id',$company_id)
                                                        ->first();

                                        if(empty($sale_data_fetch))
                                        {
                                            $role_wise_sum_array [] = 0;
                                        }
                                        // comparison parts starts here
                                        if($sale_value_range_last=='~')
                                        {
                                            // $check_comparision = UserIncentiveSlabs::where('plan_isale_value_range_first','>',$sale_data_fetch->sale_value)
                                            //                 ->first();
                                            
                                            if($sale_value_range_first < $sale_data_fetch->sale_value)
                                            {
                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                $temp_array_for_return = 'TRUE';

                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }
                                        else
                                        {

                                            if($sale_value_range_first < $sale_data_fetch->sale_value && $sale_value_range_last > $sale_data_fetch->sale_value)
                                            {
                                            // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }

                                        if($temp_array_for_return == 'TRUE')
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                            $level_name = 'Reward Percentage';

                                            return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                        }
                                        else
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array);
                                            $level_name = 'Reward Percentage';
                                        }

									} // percentage ends here 
									elseif($slab_value->incentive_type==2) // 2 as amount
									{
                                        $level_name = '';

                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->select(DB::raw("SUM(rate*quantity) as sale_value"))
                                                        ->where('dealer_id',$dealer_id)
                                                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                        ->where('user_sales_order.company_id',$company_id)
                                                        ->first();

                                        if(empty($sale_data_fetch))
                                        {
                                            $role_wise_sum_array [] = 0;
                                        }
                                        // comparison parts starts here
                                        if($sale_value_range_last=='~')
                                        {
                                            // $check_comparision = UserIncentiveSlabs::where('plan_isale_value_range_first','>',$sale_data_fetch->sale_value)
                                            //                 ->first();
                                            
                                            if($sale_value_range_first < $sale_data_fetch->sale_value)
                                            {
                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                $temp_array_for_return = 'TRUE';

                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }
                                        else
                                        {

                                            if($sale_value_range_first < $sale_data_fetch->sale_value && $sale_value_range_last > $sale_data_fetch->sale_value)
                                            {
                                            // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }

                                        if($temp_array_for_return == 'TRUE')
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                            $level_name = 'Reward Amount';

                                            return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                        }
                                        else
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array);
                                            $level_name = 'Reward Amount';
                                        }
									} // amount ends here 
									elseif($slab_value->incentive_type==3) // 3 as point 
									{
                                        $level_name = '';

                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->select(DB::raw("SUM(rate*quantity) as sale_value"))
                                                        ->where('dealer_id',$dealer_id)
                                                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                        ->where('user_sales_order.company_id',$company_id)
                                                        ->first();

                                        if(empty($sale_data_fetch))
                                        {
                                            $role_wise_sum_array [] = 0;
                                        }
                                        // comparison parts starts here
                                        if($sale_value_range_last=='~')
                                        {
                                            // $check_comparision = UserIncentiveSlabs::where('plan_isale_value_range_first','>',$sale_data_fetch->sale_value)
                                            //                 ->first();
                                            
                                            if($sale_value_range_first < $sale_data_fetch->sale_value)
                                            {
                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                $temp_array_for_return = 'TRUE';

                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }
                                        else
                                        {

                                            if($sale_value_range_first < $sale_data_fetch->sale_value && $sale_value_range_last > $sale_data_fetch->sale_value)
                                            {
                                            // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }

                                        if($temp_array_for_return == 'TRUE')
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                            $level_name = 'Reward Point';

                                            return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                        }
                                        else
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array);
                                            $level_name = 'Reward Point';
                                        }
									} // as point 
									else
									{
										return response()->json([ 'response' =>False,'result'=>[]]);
									}
								}
								else
								{
									return response()->json([ 'response' =>False,'result'=>[]]);
								}

                                

                            } // as foreach ends here 
                            
                            return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);

						} // !empty fectch_incentive_slabs if ends here 
						else
						{
							return response()->json([ 'response' =>False,'result'=>[]]);
						}

					} // total sale value ends here 
					elseif($fetch_scheme_details->vs_status==2)  // as item wised vps 
					{
                        // run query 
                        
                        foreach ($fetch_scheme_slabs as $i_v_key => $slab_value) 
                        {
                            if($slab_value->sale_unit==4) // as per range
                            {
                            
                                $item_product_details = explode(',',$slab_value->product_id);

                                $sale_value_range_first = $slab_value->sale_value_range_first;
                                $sale_value_range_last = $slab_value->sale_value_range_last;
                                
                                    if($slab_value->incentive_type==1) // 1 as percentage
									{
                                        $level_name = '';
                                    
                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->select(DB::raw("SUM(rate*quantity) as sale_value"))
                                                        ->where('dealer_id',$dealer_id)
                                                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                        ->whereIn('product_id',$item_product_details)
                                                        ->where('user_sales_order.company_id',$company_id)
                                                        ->first();
                                        if(empty($sale_data_fetch))
                                        {
                                            $role_wise_sum_array [] = 0;
                                            $temp_array_for_return = 'FALSE';
                                        }
                                        
                                        // comparison parts starts here
                                        if($sale_value_range_last=='~')
                                        {
                                            
                                            if($sale_value_range_first < $sale_data_fetch->sale_value)
                                            {
                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                $temp_array_for_return = 'TRUE';

                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }
                                        else
                                        {
                                            
                                            if($sale_value_range_first < $sale_data_fetch->sale_value && $sale_value_range_last > $sale_data_fetch->sale_value)
                                            {
                                            // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }
										
                                        if($temp_array_for_return == 'TRUE')
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                            $level_name = 'Reward Percentage';

                                            return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                        }
                                        else
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array);
                                            $level_name = 'Reward Percentage';
                                        }

									} // percentage ends here
									elseif($slab_value->incentive_type==2) // 2 as amount
									{
                                        $level_name = '';
                                    
                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->select(DB::raw("SUM(rate*quantity) as sale_value"))
                                                        ->where('dealer_id',$dealer_id)
                                                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                        ->whereIn('product_id',$item_product_details)
                                                        ->where('user_sales_order.company_id',$company_id)
                                                        ->first();
                                        if(empty($sale_data_fetch))
                                        {
                                            $role_wise_sum_array [] = 0;
                                            $temp_array_for_return = 'FALSE';
                                        }
                                        
                                        // comparison parts starts here
                                        if($sale_value_range_last=='~')
                                        {
                                            
                                            if($sale_value_range_first < $sale_data_fetch->sale_value)
                                            {
                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                $temp_array_for_return = 'TRUE';

                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }
                                        else
                                        {
                                            
                                            if($sale_value_range_first < $sale_data_fetch->sale_value && $sale_value_range_last > $sale_data_fetch->sale_value)
                                            {
                                            // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }
										
                                        if($temp_array_for_return == 'TRUE')
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                            $level_name = 'Reward Amount';

                                            return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                        }
                                        else
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array);
                                            $level_name = 'Reward Amount';
                                        }

									} // Amount ends here 
									elseif($slab_value->incentive_type==3) // 3 as point 
									{
                                        $level_name = '';
                                    
                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->select(DB::raw("SUM(rate*quantity) as sale_value"))
                                                        ->where('dealer_id',$dealer_id)
                                                        ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                        ->whereIn('product_id',$item_product_details)
                                                        ->where('user_sales_order.company_id',$company_id)
                                                        ->first();
                                        if(empty($sale_data_fetch))
                                        {
                                            $role_wise_sum_array [] = 0;
                                            $temp_array_for_return = 'FALSE';
                                        }
                                        
                                        // comparison parts starts here
                                        if($sale_value_range_last=='~')
                                        {
                                            
                                            if($sale_value_range_first < $sale_data_fetch->sale_value)
                                            {
                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                $temp_array_for_return = 'TRUE';

                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }
                                        else
                                        {
                                            
                                            if($sale_value_range_first < $sale_data_fetch->sale_value && $sale_value_range_last > $sale_data_fetch->sale_value)
                                            {
                                            // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }
										
                                        if($temp_array_for_return == 'TRUE')
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                            $level_name = 'Reward Point';

                                            return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                        }
                                        else
                                        {
                                            $final_sum_role_wise = array_sum($role_wise_sum_array);
                                            $level_name = 'Reward Point';
                                        }
									} // point ends here 
									else
									{
										return response()->json([ 'response' =>False,'result'=>[]]);
									}
                            }
                            else
                            {
                                return response()->json([ 'response' =>False,'result'=>[]]);
                            }
                            
                        }// for each rnds here vps item vised
                        
                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                    

					} // ends here item wised sales in vps 
					else
					{
						return response()->json([ 'response' =>False,'result'=>[]]);
					}
				}

				elseif($fetch_scheme_details->scheme_category_status==2) // qs quantity sale 
				{
					if($fetch_scheme_details->vs_status==2)
					{
                        foreach ($fetch_scheme_slabs as $i_q_key => $slab_value) 
                        {
                            $item_product_details = explode(',',$slab_value->product_id);
                            $sale_value_range_first = $slab_value->sale_value_range_first;
                            $sale_value_range_last = $slab_value->sale_value_range_last;

                            if($slab_value->sale_unit==1) // weight starts here 
                            {
                                if($slab_value->incentive_type==1) // 1 as percentage
                                {
                                    $level_name = '';
                                    $sale_data_fetch = DB::table('user_sales_order')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                                    ->select(DB::raw("SUM((catalog_product.weight*quantity)/1000) as total_weight"))
                                                    ->where('dealer_id',$dealer_id)
                                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                    ->whereIn('product_id',$item_product_details)
                                                    ->where('user_sales_order.company_id',$company_id)
                                                    ->first();
                                    if(empty($sale_data_fetch))
                                    {
                                        $role_wise_sum_array [] = 0;
                                        $temp_array_for_return = 'FALSE';
                                    }
                                        
                                    // comparison parts starts here
                                    if($sale_value_range_last=='~')
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_weight)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    else
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_weight && $sale_value_range_last > $sale_data_fetch->total_weight)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                      
                                    if($temp_array_for_return == 'TRUE')
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                        $level_name = 'Reward Percentage';

                                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                    }
                                    else
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array);
                                        $level_name = 'Reward Percentage';
                                    }

                                } // as percentage ends here 

                                if($slab_value->incentive_type==2) // 2 as Amount
                                {
                                    $level_name = '';
                                    $sale_data_fetch = DB::table('user_sales_order')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                                    ->select(DB::raw("SUM((catalog_product.weight*quantity)/1000) as total_weight"))
                                                    ->where('dealer_id',$dealer_id)
                                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                    ->whereIn('product_id',$item_product_details)
                                                    ->where('user_sales_order.company_id',$company_id)
                                                    ->first();
                                    if(empty($sale_data_fetch))
                                    {
                                        $role_wise_sum_array [] = 0;
                                        $temp_array_for_return = 'FALSE';
                                    }
                                        
                                    // comparison parts starts here
                                    if($sale_value_range_last=='~')
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_weight)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    else
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_weight && $sale_value_range_last > $sale_data_fetch->total_weight)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                      
                                    if($temp_array_for_return == 'TRUE')
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                        $level_name = 'Reward Amount';

                                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                    }
                                    else
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array);
                                        $level_name = 'Reward Amount';
                                    }
                                } // Amount ends here 

                                if($slab_value->incentive_type==3) // 3 as point
                                {
                                    $level_name = '';
                                    $sale_data_fetch = DB::table('user_sales_order')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                                    ->select(DB::raw("SUM((catalog_product.weight*quantity)/1000) as total_weight"))
                                                    ->where('dealer_id',$dealer_id)
                                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                    ->whereIn('product_id',$item_product_details)
                                                    ->where('user_sales_order.company_id',$company_id)
                                                    ->first();
                                    if(empty($sale_data_fetch))
                                    {
                                        $role_wise_sum_array [] = 0;
                                        $temp_array_for_return = 'FALSE';
                                    }
                                        
                                    // comparison parts starts here
                                    if($sale_value_range_last=='~')
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_weight)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    else
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_weight && $sale_value_range_last > $sale_data_fetch->total_weight)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                      
                                    if($temp_array_for_return == 'TRUE')
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                        $level_name = 'Reward Amount';

                                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                    }
                                    else
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array);
                                        $level_name = 'Reward Amount';
                                    }
                                } // points ends here 
                            } // weight ends here
                            if($slab_value->sale_unit==2) // cases starts here 
                            {
                                if($slab_value->incentive_type==1) // 1 as percentage
                                {
                                    $level_name = '';
                                    $sale_data_fetch = DB::table('user_sales_order')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                                    ->select(DB::raw("SUM(quantity/quantity_per_case) as total_cases"))
                                                    ->where('dealer_id',$dealer_id)
                                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                    ->whereIn('product_id',$item_product_details)
                                                    ->where('user_sales_order.company_id',$company_id)
                                                    ->first();
                                    if(empty($sale_data_fetch))
                                    {
                                        $role_wise_sum_array [] = 0;
                                        $temp_array_for_return = 'FALSE';
                                    }
                                        
                                    // comparison parts starts here
                                    if($sale_value_range_last=='~')
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_cases)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    else
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_cases && $sale_value_range_last > $sale_data_fetch->total_cases)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                      
                                    if($temp_array_for_return == 'TRUE')
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                        $level_name = 'Reward Percentage';

                                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                    }
                                    else
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array);
                                        $level_name = 'Reward Percentage';
                                    }

                                } // as percentage ends here 

                                if($slab_value->incentive_type==2) // 2 as Amount
                                {
                                    $level_name = '';
                                    $sale_data_fetch =DB::table('user_sales_order')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                                    ->select(DB::raw("SUM(quantity/quantity_per_case) as total_cases"))
                                                    ->where('dealer_id',$dealer_id)
                                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                    ->whereIn('product_id',$item_product_details)
                                                    ->where('user_sales_order.company_id',$company_id)
                                                    ->first();
                                    if(empty($sale_data_fetch))
                                    {
                                        $role_wise_sum_array [] = 0;
                                        $temp_array_for_return = 'FALSE';
                                    }
                                        
                                    // comparison parts starts here
                                    if($sale_value_range_last=='~')
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_cases)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    else
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_cases && $sale_value_range_last > $sale_data_fetch->total_cases)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                      
                                    if($temp_array_for_return == 'TRUE')
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                        $level_name = 'Reward Amount';

                                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                    }
                                    else
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array);
                                        $level_name = 'Reward Amount';
                                    }
                                } // Amount ends here 

                                if($slab_value->incentive_type==3) // 3 as point
                                {
                                    $level_name = '';
                                    $sale_data_fetch = DB::table('user_sales_order')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                    ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                                    ->select(DB::raw("SUM(quantity/quantity_per_case) as total_cases"))
                                                    ->where('dealer_id',$dealer_id)
                                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                    ->whereIn('product_id',$item_product_details)
                                                    ->where('user_sales_order.company_id',$company_id)
                                                    ->first();
                                    if(empty($sale_data_fetch))
                                    {
                                        $role_wise_sum_array [] = 0;
                                        $temp_array_for_return = 'FALSE';
                                    }
                                        
                                    // comparison parts starts here
                                    if($sale_value_range_last=='~')
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_cases)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    else
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_cases && $sale_value_range_last > $sale_data_fetch->total_cases)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                      
                                    if($temp_array_for_return == 'TRUE')
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                        $level_name = 'Reward Amount';

                                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                    }
                                    else
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array);
                                        $level_name = 'Reward Amount';
                                    }
                                } // points ends here 
                            } // cases ends here 
                            if($slab_value->sale_unit==3) // pcs starts here 
                            {
                                if($slab_value->incentive_type==1) // 1 as percentage
                                {
                                    $level_name = '';
                                    $sale_data_fetch = DB::table('user_sales_order')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                    ->select(DB::raw("SUM(quantity) as total_quantity"))
                                                    ->where('dealer_id',$dealer_id)
                                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                    ->whereIn('product_id',$item_product_details)
                                                    ->where('user_sales_order.company_id',$company_id)
                                                    ->first();
                                    if(empty($sale_data_fetch))
                                    {
                                        $role_wise_sum_array [] = 0;
                                        $temp_array_for_return = 'FALSE';
                                    }
                                    // comparison parts starts here
                                    if($sale_value_range_last=='~')
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_quantity)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    else
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_quantity && $sale_value_range_last > $sale_data_fetch->total_quantity)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';  
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    if($temp_array_for_return == 'TRUE')
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                        $level_name = 'Reward Percentage';

                                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                    }
                                    else
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array);
                                        $level_name = 'Reward Percentage';
                                    }

                                } // percentage ends here 

                                if($slab_value->incentive_type==2) // 2 as Amount
                                {
                                    $level_name = '';
                                    $sale_data_fetch = DB::table('user_sales_order')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                    ->select(DB::raw("SUM(quantity) as total_quantity"))
                                                    ->where('dealer_id',$dealer_id)
                                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                    ->whereIn('product_id',$item_product_details)
                                                    ->where('user_sales_order.company_id',$company_id)
                                                    ->first();
                                    if(empty($sale_data_fetch))
                                    {
                                        $role_wise_sum_array [] = 0;
                                        $temp_array_for_return = 'FALSE';
                                    }
                                    // comparison parts starts here
                                    if($sale_value_range_last=='~')
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_quantity)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    else
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_quantity && $sale_value_range_last > $sale_data_fetch->total_quantity)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';  
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    if($temp_array_for_return == 'TRUE')
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                        $level_name = 'Reward Amount';

                                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                    }
                                    else
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array);
                                        $level_name = 'Reward Amount';
                                    }
                                }
                                if($slab_value->incentive_type==3) // 3 as point
                                {
                                    $level_name = '';
                                    $sale_data_fetch = DB::table('user_sales_order')
                                                    ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                    ->select(DB::raw("SUM(quantity) as total_quantity"))
                                                    ->where('dealer_id',$dealer_id)
                                                    ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d')>='$plan_assigned_from_date' AND DATE_FORMAT(date,'%Y-%m-%d')<='$plan_assigned_to_date'")
                                                    ->whereIn('product_id',$item_product_details)
                                                    ->where('user_sales_order.company_id',$company_id)
                                                    ->first();
                                    if(empty($sale_data_fetch))
                                    {
                                        $role_wise_sum_array [] = 0;
                                        $temp_array_for_return = 'FALSE';
                                    }
                                    // comparison parts starts here
                                    if($sale_value_range_last=='~')
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_quantity)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    else
                                    {
                                        if($sale_value_range_first < $sale_data_fetch->total_quantity && $sale_value_range_last > $sale_data_fetch->total_quantity)
                                        {
                                            $role_wise_sum_array[] = $slab_value->value_amount_percentage;
                                            $temp_array_for_return = 'TRUE';  
                                        }
                                        else
                                        {
                                            $temp_array_for_return = 'FALSE';
                                            $role_wise_sum_array[] = 0;
                                        }
                                    }
                                    if($temp_array_for_return == 'TRUE')
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array); 
                                        $level_name = 'Reward Amount';

                                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                                    }
                                    else
                                    {
                                        $final_sum_role_wise = array_sum($role_wise_sum_array);
                                        $level_name = 'Reward Amount';
                                    }
                                } // point ends here 
                            } // pcs end shere 

                        }
                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
					}
					else
					{
						return response()->json([ 'response' =>False,'result'=>[]]);
					}
				}
				elseif($fetch_scheme_details->plan_category_status==6) // other 
				{
					// depend on condition 
				}
				else
				{
					return response()->json([ 'response' =>False,'result'=>[]]);
				}
			} // !empty fectch_incentive_details if ends here 
			else
			{
				return response()->json([ 'response' =>False,'result'=>[]]);
			}
		} // !empty	fetch_plan if enda here 
		else
		{
			return response()->json([ 'response' =>False,'result'=>[]]);

		}		
    } // incentive part function ends here
    

    ##...............................dealer scheme ends here ..............................###
##################################################################################################################################################################################################
	public function scheme_calculation(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'company_id'=>'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],200);
		}
		$fetch_plan = DB::table('user_plan_assign')
					->where('status',1)
					->where('plan_user_assigned_date',date('Y-m-d'))
					->where('company_id',$company_id)
					->where('user_id',$request->user_id)
					->first();
		if(!empty($fetch_plan))
		{
			$fetch_scheme_details = UserIncentiveDetails::where('status',1)->where('id',$fetch_plan->plan_id)->first();
			
			if(!empty($fetch_scheme_details))
			{
				$plan_name = $fetch_scheme_details->plan_name; 

				if($fetch_scheme_details->plan_category_status==1) // vps value per sale 
				{
					if($fetch_scheme_details->vs_status==1) // as total sale value vps
					{
						// run query 
						$fetch_scheme_slabs = UserIncentiveSlabs::where('plan_id',$fetch_scheme_details->id)->first();
						$sale_value_range_first = $fetch_scheme_slabs->sale_value_range_first;
						$sale_value_range_last = $fetch_scheme_slabs->sale_value_range_last;
						if(!empty($fetch_scheme_slabs))
						{
							if($fetch_scheme_slabs->sale_unit==1) // as per weight
							{
								if($fetch_scheme_slabs->incentive_type==1) // 1 as percentage
								{

								}
								elseif($fetch_scheme_slabs->incentive_type==2) // 2 as amount
								{

								}
								elseif($fetch_scheme_slabs->incentive_type==3) // 3 as point 
								{
									
								}
								else
								{
									return response()->json([ 'response' =>False,'result'=>[]]);
								}
							}
							elseif($fetch_scheme_slabs->sale_unit==2) // as per cases	
							{

							}
							elseif($fetch_scheme_slabs->sale_unit==3) // as per pcs
							{

							}
							elseif($fetch_scheme_slabs->sale_unit==4) // as per range
							{

							}
							else
							{
								return response()->json([ 'response' =>False,'result'=>[]]);
							}

						} // !empty fectch_incentive_slabs if ends here 
						else
						{
							return response()->json([ 'response' =>False,'result'=>[]]);
						}

					}
					elseif($fetch_scheme_details->vs_status==2)  // as item wised vps 
					{
						if($fetch_scheme_details->item_status==1) // as combo wise
						{
							// run query 

						}
						elseif($fetch_scheme_details->item_status==2) // as single sku wise
						{
							// run query 

						}
						else
						{
							return response()->json([ 'response' =>False,'result'=>[]]);
						}
					}
					else
					{
						return response()->json([ 'response' =>False,'result'=>[]]);
					}
				}

				elseif($fetch_scheme_details->plan_category_status==2) // qs quantity sale 
				{
					if($fetch_scheme_details->vs_status==2)
					{
						if($fetch_scheme_details->item_status==1) // as combo wise
						{
							// run query 

						}
						elseif($fetch_scheme_details->item_status==2) // as single sku wise
						{
							// run query 

						}
						else
						{
							return response()->json([ 'response' =>False,'result'=>[]]);
						}
					}
					else
					{
						return response()->json([ 'response' =>False,'result'=>[]]);
					}
				}
				elseif($fetch_scheme_details->plan_category_status==3) // as outlet opnening
				{
                    // run query 
                    
                    
				}
				elseif($fetch_scheme_details->plan_category_status==4) // as range selling 
				{
					// run query 
				}
				elseif($fetch_scheme_details->plan_category_status==5) // as productivity % 
				{
					// run query 
				}
				elseif($fetch_scheme_details->plan_category_status==6) // other 
				{
					// depend on condition 
				}
				else
				{
					return response()->json([ 'response' =>False,'result'=>[]]);
				}
			} // !empty fectch_incentive_details if ends here 
			else
			{
				return response()->json([ 'response' =>False,'result'=>[]]);
			}
		} // !empty	fetch_plan if enda here 
		else
		{
			return response()->json([ 'response' =>False,'result'=>[]]);

		}		
	}
}
