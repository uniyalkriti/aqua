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
use App\UserIncentiveDetails;
use App\UserIncentiveSlabs;
use App\UserIncentiveRoleDistribution;
use Validator;
use DB;
use Image;

class IncentiveApiController extends Controller
{
    public $successStatus = 200;
    public $response_true = True;
    public $response_false = False;

	public function incentive_calculation(Request $request)
	{
		$validator=Validator::make($request->all(),[
            'user_id'=>'required',
            'company_id'=>'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['response'=>FALSE,'message'=>'Validation Error!','Error'=>$validator->errors()],201);
        }
        $company_id = $request->company_id;
        $user_id = $request->user_id;
		$fetch_plan = DB::table('user_plan_assign')
					->where('status',1)
					->where('plan_user_assigned_date',date('Y-m-d'))
					->where('company_id',$company_id)
					->where('user_id',$request->user_id)
					->first();
        // dd($fetch_plan);
		$check_junior_data = JuniorData::getJuniorUser($request->user_id,$company_id);
		Session::push('juniordata', $request->user_id);
		$junior_data_check = Session::get('juniordata');
		if(!empty($fetch_plan))
		{
            $fectch_incentive_details = UserIncentiveDetails::where('status',1)->where('id',$fetch_plan->plan_id)->first();
            $fectch_incentive_slabs = UserIncentiveSlabs::where('plan_id',$fectch_incentive_details->id)->orderBy('id')->get();
			// dd($fectch_incentive_details);
			if(!empty($fectch_incentive_details))
			{
				$plan_name = $fectch_incentive_details->plan_name; 

				if($fectch_incentive_details->plan_category_status==1) // vps value per sale 
				{
					if($fectch_incentive_details->vs_status==1) // as total sale value vps
					{   
						// run query 
						// $fectch_incentive_slabs = UserIncentiveSlabs::where('plan_id',$fectch_incentive_details->id)->orderBy('id')->get();

                        // dd($fectch_incentive_slabs);
						if(!empty($fectch_incentive_slabs))
						{
							
							foreach ($fectch_incentive_slabs as $slab_key => $slab_value) 
							{
                                // dd($slab_value);
								$sale_value_range_first = $slab_value->sale_value_range_first;
								$sale_value_range_last = $slab_value->sale_value_range_last;

								if($slab_value->sale_unit==4) // as per range
								{
									if($slab_value->incentive_type==1) // 1 as percentage
									{
                                        $level_name = '';
										$person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                        // dd($person_details);

										foreach($person_details as $p_key => $p_value)
										{
                                            $role_id = $p_value->role_id;
											$sale_data_fetch = DB::table('user_sales_order')
															->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
															->select(DB::raw("SUM(rate*quantity) as sale_value"))
                                                            ->where('user_id',$p_value->id)
                                                            ->where('date',date('Y-m-d'))
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

                                                    if($p_value->id == $request->user_id) // if comparsion is true 
                                                    {
                                                        $role_wise_sum_array [] = 0;	
                                                    }
                                                    else
                                                    {
                                                        
                                                        $fetch_role_group = DB::table('_role_group')
                                                                        ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                        ->select('_role_group.id as group_id')
                                                                        ->where('role_id',$role_id)
                                                                        ->where('_role_group.company_id',$company_id)
                                                                        ->where('_role.company_id',$company_id)
                                                                        ->first();

                                                        $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                        $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                    }

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
                                                // $check_comparision = UserIncentiveSlabs::where('plan_isale_value_range_first','>',$sale_data_fetch->sale_value)
                                                //                 ->where('sale_value_range_last','<',$sale_data_fetch->sale_value)
                                                //                 ->first();
                                                
                                                if($sale_value_range_first < $sale_data_fetch->sale_value && $sale_value_range_last > $sale_data_fetch->sale_value)
                                                {
                                                // dd($sale_data_fetch->sale_value);

                                                    $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                    if($p_value->id == $request->user_id) // if comparsion is true 
                                                    {
                                                        $role_wise_sum_array [] = 0;	
                                                    }
                                                    else
                                                    {
                                                        
                                                        $fetch_role_group = DB::table('_role_group')
                                                                        ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                        ->select('_role_group.id as group_id')
                                                                        ->where('role_id',$role_id)
                                                                        ->where('_role_group.company_id',$company_id)
                                                                        ->where('_role.company_id',$company_id)
                                                                        ->first();

                                                        $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                        $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                    }
                                                    $temp_array_for_return = 'TRUE';
                                                        
                                                }
                                                else
                                                {
                                                    $temp_array_for_return = 'FALSE';
                                                    $role_wise_sum_array[] = 0;
                                                }
                                            }

											
										} // for loop ends here 
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

									}
									elseif($slab_value->incentive_type==2) // 2 as amount
									{
                                        $level_name = '';
										$person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                        // dd($person_details);

										foreach($person_details as $p_key => $p_value)
										{
                                            $role_id = $p_value->role_id;
											$sale_data_fetch = DB::table('user_sales_order')
															->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
															->select(DB::raw("SUM(rate*quantity) as sale_value"))
                                                            ->where('user_id',$p_value->id)
                                                            ->where('date',date('Y-m-d'))
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

                                                    if($p_value->id == $request->user_id) // if comparsion is true 
                                                    {
                                                        $role_wise_sum_array [] = 0;	
                                                    }
                                                    else
                                                    {
                                                        
                                                        $fetch_role_group = DB::table('_role_group')
                                                                        ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                        ->select('_role_group.id as group_id')
                                                                        ->where('role_id',$role_id)
                                                                        ->where('_role_group.company_id',$company_id)
                                                                        ->where('_role.company_id',$company_id)
                                                                        ->first();

                                                        $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                        $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                    }

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
                                                // $check_comparision = UserIncentiveSlabs::where('plan_isale_value_range_first','>',$sale_data_fetch->sale_value)
                                                //                 ->where('sale_value_range_last','<',$sale_data_fetch->sale_value)
                                                //                 ->first();
                                                
                                                if($sale_value_range_first < $sale_data_fetch->sale_value && $sale_value_range_last > $sale_data_fetch->sale_value)
                                                {
                                                // dd($sale_data_fetch->sale_value);

                                                    $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                    if($p_value->id == $request->user_id) // if comparsion is true 
                                                    {
                                                        $role_wise_sum_array [] = 0;	
                                                    }
                                                    else
                                                    {
                                                        
                                                        $fetch_role_group = DB::table('_role_group')
                                                                        ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                        ->select('_role_group.id as group_id')
                                                                        ->where('role_id',$role_id)
                                                                        ->where('_role_group.company_id',$company_id)
                                                                        ->where('_role.company_id',$company_id)
                                                                        ->first();

                                                        $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                        $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                    }
                                                    $temp_array_for_return = 'TRUE';
                                                        
                                                }
                                                else
                                                {
                                                    $temp_array_for_return = 'FALSE';
                                                    $role_wise_sum_array[] = 0;
                                                }
                                            }

											
										} // for loop ends here 
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
									elseif($slab_value->incentive_type==3) // 3 as point 
									{
                                        $level_name = '';
										$person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                        // dd($person_details);

										foreach($person_details as $p_key => $p_value)
										{
                                            $role_id = $p_value->role_id;
											$sale_data_fetch = DB::table('user_sales_order')
															->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
															->select(DB::raw("SUM(rate*quantity) as sale_value"))
                                                            ->where('user_id',$p_value->id)
                                                            ->where('date',date('Y-m-d'))
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

                                                    if($p_value->id == $request->user_id) // if comparsion is true 
                                                    {
                                                        $role_wise_sum_array [] = 0;	
                                                    }
                                                    else
                                                    {
                                                        
                                                        $fetch_role_group = DB::table('_role_group')
                                                                        ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                        ->select('_role_group.id as group_id')
                                                                        ->where('role_id',$role_id)
                                                                        ->where('_role_group.company_id',$company_id)
                                                                        ->where('_role.company_id',$company_id)
                                                                        ->first();

                                                        $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                        $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                    }

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
                                                // $check_comparision = UserIncentiveSlabs::where('plan_isale_value_range_first','>',$sale_data_fetch->sale_value)
                                                //                 ->where('sale_value_range_last','<',$sale_data_fetch->sale_value)
                                                //                 ->first();
                                                
                                                if($sale_value_range_first < $sale_data_fetch->sale_value && $sale_value_range_last > $sale_data_fetch->sale_value)
                                                {
                                                // dd($sale_data_fetch->sale_value);

                                                    $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                    if($p_value->id == $request->user_id) // if comparsion is true 
                                                    {
                                                        $role_wise_sum_array [] = 0;	
                                                    }
                                                    else
                                                    {
                                                        
                                                        $fetch_role_group = DB::table('_role_group')
                                                                        ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                        ->select('_role_group.id as group_id')
                                                                        ->where('role_id',$role_id)
                                                                        ->where('_role_group.company_id',$company_id)
                                                                        ->where('_role.company_id',$company_id)
                                                                        ->first();

                                                        $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                        $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                    }
                                                    $temp_array_for_return = 'TRUE';
                                                        
                                                }
                                                else
                                                {
                                                    $temp_array_for_return = 'FALSE';
                                                    $role_wise_sum_array[] = 0;
                                                }
                                            }

											
										} // for loop ends here 
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

                                

                            } // as foreach ends here 
                            
                            return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);

						} // !empty fectch_incentive_slabs if ends here 
						else
						{
							return response()->json([ 'response' =>False,'result'=>[]]);
						}

					} // total sale value ends here 
					elseif($fectch_incentive_details->vs_status==2)  // as item wised vps 
					{
                        // run query 
                        $fectch_incentive_slabs_vps = UserIncentiveSlabs::where('plan_id',$fectch_incentive_details->id)->orderBy('id')->get();
                        foreach ($fectch_incentive_slabs_vps as $i_v_key => $slab_value) 
                        {
                            if($slab_value->sale_unit==4) // as per range
                            {
                            
                                $item_product_details = explode(',',$slab_value->product_id);

                                $sale_value_range_first = $slab_value->sale_value_range_first;
                                $sale_value_range_last = $slab_value->sale_value_range_last;
                                
                                    if($slab_value->incentive_type==1) // 1 as percentage
									{
                                        $level_name = '';
										$person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                        // dd($person_details);

										foreach($person_details as $p_key => $p_value)
										{
                                            $role_id = $p_value->role_id;
											$sale_data_fetch = DB::table('user_sales_order')
															->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
															->select(DB::raw("SUM(rate*quantity) as sale_value"))
                                                            ->where('user_id',$p_value->id)
                                                            ->where('date',date('Y-m-d'))
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
                                                // $check_comparision = UserIncentiveSlabs::where('plan_isale_value_range_first','>',$sale_data_fetch->sale_value)
                                                //                 ->first();
                                                
                                                if($sale_value_range_first < $sale_data_fetch->sale_value)
                                                {
                                                    $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                    if($p_value->id == $request->user_id) // if comparsion is true 
                                                    {
                                                        $role_wise_sum_array [] = 0;	
                                                    }
                                                    else
                                                    {
                                                        
                                                        $fetch_role_group = DB::table('_role_group')
                                                                        ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                        ->select('_role_group.id as group_id')
                                                                        ->where('role_id',$role_id)
                                                                        ->where('_role_group.company_id',$company_id)
                                                                        ->where('_role.company_id',$company_id)
                                                                        ->first();

                                                        $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                        $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                    }

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
                                                // $check_comparision = UserIncentiveSlabs::where('plan_isale_value_range_first','>',$sale_data_fetch->sale_value)
                                                //                 ->where('sale_value_range_last','<',$sale_data_fetch->sale_value)
                                                //                 ->first();
                                                
                                                if($sale_value_range_first < $sale_data_fetch->sale_value && $sale_value_range_last > $sale_data_fetch->sale_value)
                                                {
                                                // dd($sale_data_fetch->sale_value);

                                                    $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                    if($p_value->id == $request->user_id) // if comparsion is true 
                                                    {
                                                        $role_wise_sum_array [] = 0;	
                                                    }
                                                    else
                                                    {
                                                        
                                                        $fetch_role_group = DB::table('_role_group')
                                                                        ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                        ->select('_role_group.id as group_id')
                                                                        ->where('role_id',$role_id)
                                                                        ->where('_role_group.company_id',$company_id)
                                                                        ->where('_role.company_id',$company_id)
                                                                        ->first();

                                                        $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                        $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                    }
                                                    $temp_array_for_return = 'TRUE';
                                                        
                                                }
                                                else
                                                {
                                                    $temp_array_for_return = 'FALSE';
                                                    $role_wise_sum_array[] = 0;
                                                }
                                            }

											
										} // for loop ends here 
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

									}
									elseif($slab_value->incentive_type==2) // 2 as amount
									{
                                        $level_name = '';
										$person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                        // dd($person_details);

										foreach($person_details as $p_key => $p_value)
										{
                                            $role_id = $p_value->role_id;
											$sale_data_fetch = DB::table('user_sales_order')
															->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
															->select(DB::raw("SUM(rate*quantity) as sale_value"))
                                                            ->where('user_id',$p_value->id)
                                                            ->where('date',date('Y-m-d'))
                                                            ->whereIn('product_id',$item_product_details)
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

                                                    if($p_value->id == $request->user_id) // if comparsion is true 
                                                    {
                                                        $role_wise_sum_array [] = 0;	
                                                    }
                                                    else
                                                    {
                                                        
                                                        $fetch_role_group = DB::table('_role_group')
                                                                        ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                        ->select('_role_group.id as group_id')
                                                                        ->where('role_id',$role_id)
                                                                        ->where('_role_group.company_id',$company_id)
                                                                        ->where('_role.company_id',$company_id)
                                                                        ->first();

                                                        $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                        $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                    }

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

                                                    if($p_value->id == $request->user_id) // if comparsion is true 
                                                    {
                                                        $role_wise_sum_array [] = 0;	
                                                    }
                                                    else
                                                    {
                                                        
                                                        $fetch_role_group = DB::table('_role_group')
                                                                        ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                        ->select('_role_group.id as group_id')
                                                                        ->where('role_id',$role_id)
                                                                        ->where('_role_group.company_id',$company_id)
                                                                        ->where('_role.company_id',$company_id)
                                                                        ->first();

                                                        $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                        $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                    }
                                                    $temp_array_for_return = 'TRUE';
                                                        
                                                }
                                                else
                                                {
                                                    $temp_array_for_return = 'FALSE';
                                                    $role_wise_sum_array[] = 0;
                                                }
                                            }

											
										} // for loop ends here 
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
									elseif($slab_value->incentive_type==3) // 3 as point 
									{
                                        $level_name = '';
										$person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                        // dd($person_details);

										foreach($person_details as $p_key => $p_value)
										{
                                            $role_id = $p_value->role_id;
											$sale_data_fetch = DB::table('user_sales_order')
															->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
															->select(DB::raw("SUM(rate*quantity) as sale_value"))
                                                            ->where('user_id',$p_value->id)
                                                            ->where('date',date('Y-m-d'))
                                                            ->whereIn('product_id',$item_product_details)
															->where('user_sales_order.company_id',$company_id)
                                                            ->first();
                                            if(empty($sale_data_fetch))
                                            {
                                                $role_wise_sum_array [] = 0;
                                            }
                                            // comparison parts starts here
                                            if($sale_value_range_last=='~')
                                            {

                                                if($sale_value_range_first < $sale_data_fetch->sale_value)
                                                {
                                                    $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                    if($p_value->id == $request->user_id) // if comparsion is true 
                                                    {
                                                        $role_wise_sum_array [] = 0;	
                                                    }
                                                    else
                                                    {
                                                        
                                                        $fetch_role_group = DB::table('_role_group')
                                                                        ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                        ->select('_role_group.id as group_id')
                                                                        ->where('role_id',$role_id)
                                                                        ->where('_role_group.company_id',$company_id)
                                                                        ->where('_role.company_id',$company_id)
                                                                        ->first();

                                                        $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                        $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                    }

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

                                                    if($p_value->id == $request->user_id) // if comparsion is true 
                                                    {
                                                        $role_wise_sum_array [] = 0;	
                                                    }
                                                    else
                                                    {
                                                        
                                                        $fetch_role_group = DB::table('_role_group')
                                                                        ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                        ->select('_role_group.id as group_id')
                                                                        ->where('role_id',$role_id)
                                                                        ->where('_role_group.company_id',$company_id)
                                                                        ->where('_role.company_id',$company_id)
                                                                        ->first();

                                                        $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                        $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                    }
                                                    $temp_array_for_return = 'TRUE';
                                                        
                                                }
                                                else
                                                {
                                                    $temp_array_for_return = 'FALSE';
                                                    $role_wise_sum_array[] = 0;
                                                }
                                            }

											
										} // for loop ends here 
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
                            
                        }// for each rnds here vps item vised
                        
                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
                    

					} // ends here item wised sales in vps 
					else
					{
						return response()->json([ 'response' =>False,'result'=>[]]);
					}
				}

				elseif($fectch_incentive_details->plan_category_status==2) // qs quantity sale 
				{
					if($fectch_incentive_details->vs_status==2)
					{
						$fectch_incentive_slabs_vps = UserIncentiveSlabs::where('plan_id',$fectch_incentive_details->id)->orderBy('id')->get();
                        foreach ($fectch_incentive_slabs_vps as $i_q_key => $slab_value) 
                        {
                            $item_product_details = explode(',',$slab_value->product_id);
                            $sale_value_range_first = $slab_value->sale_value_range_first;
                            $sale_value_range_last = $slab_value->sale_value_range_last;

                            if($slab_value->sale_unit==1) // weight starts here 
                            {
                                if($slab_value->incentive_type==1) // 1 as percentage
                                {
                                    $level_name = '';
                                    $person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                    // dd($person_details);

                                    foreach($person_details as $p_key => $p_value)
                                    {
                                        $role_id = $p_value->role_id;
                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                                        ->select(DB::raw("SUM((catalog_product.weight*quantity)/1000) as total_weight"))
                                                        ->where('user_id',$p_value->id)
                                                        ->where('date',date('Y-m-d'))
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

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }

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
                                                // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }
                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }

                                        
                                    } // for loop ends here 
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

                                }

                                if($slab_value->incentive_type==2) // 2 as Amount
                                {
                                    $level_name = '';
                                    $person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                    // dd($person_details);

                                    foreach($person_details as $p_key => $p_value)
                                    {
                                        $role_id = $p_value->role_id;
                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                                        ->select(DB::raw("SUM((catalog_product.weight*quantity)/1000) as total_weight"))
                                                        ->where('user_id',$p_value->id)
                                                        ->where('date',date('Y-m-d'))
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

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }

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
                                                // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }
                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }

                                        
                                    } // for loop ends here 
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
                                    $person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                    // dd($person_details);

                                    foreach($person_details as $p_key => $p_value)
                                    {
                                        $role_id = $p_value->role_id;
                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                                        ->select(DB::raw("SUM((catalog_product.weight*quantity)/1000) as total_weight"))
                                                        ->where('user_id',$p_value->id)
                                                        ->where('date',date('Y-m-d'))
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

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }

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
                                                // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }
                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }

                                        
                                    } // for loop ends here 
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

                                }


                            } // weight ends here
                            if($slab_value->sale_unit==2) // cases starts here 
                            {
                                if($slab_value->incentive_type==1) // 1 as percentage
                                {
                                    $level_name = '';
                                    $person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                    // dd($person_details);

                                    foreach($person_details as $p_key => $p_value)
                                    {
                                        $role_id = $p_value->role_id;
                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                                        ->select(DB::raw("SUM(quantity/quantity_per_case) as total_cases"))
                                                        ->where('user_id',$p_value->id)
                                                        ->where('date',date('Y-m-d'))
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

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }

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
                                                // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }
                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }

                                        
                                    } // for loop ends here 
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

                                }

                                if($slab_value->incentive_type==2) // 2 as Amount
                                {
                                    $level_name = '';
                                    $person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                    // dd($person_details);

                                    foreach($person_details as $p_key => $p_value)
                                    {
                                        $role_id = $p_value->role_id;
                                        $sale_data_fetch =  DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                                        ->select(DB::raw("SUM(quantity/quantity_per_case) as total_cases"))
                                                        ->where('user_id',$p_value->id)
                                                        ->where('date',date('Y-m-d'))
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

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }

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
                                                // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }
                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }

                                        
                                    } // for loop ends here 
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
                                    $person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                    // dd($person_details);

                                    foreach($person_details as $p_key => $p_value)
                                    {
                                        $role_id = $p_value->role_id;
                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->join('catalog_product','catalog_product.id','=','user_sales_order_details.product_id')
                                                        ->select(DB::raw("SUM(quantity/quantity_per_case) as total_cases"))
                                                        ->where('user_id',$p_value->id)
                                                        ->where('date',date('Y-m-d'))
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

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }

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
                                                // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }
                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }

                                        
                                    } // for loop ends here 
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

                                }
                            } // cases ends here 
                            if($slab_value->sale_unit==3) // pcs starts here 
                            {
                                if($slab_value->incentive_type==1) // 1 as percentage
                                {
                                    $level_name = '';
                                    $person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                    // dd($person_details);

                                    foreach($person_details as $p_key => $p_value)
                                    {
                                        $role_id = $p_value->role_id;
                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->select(DB::raw("SUM(quantity) as total_quantity"))
                                                        ->where('user_id',$p_value->id)
                                                        ->where('date',date('Y-m-d'))
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

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }

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
                                                // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }
                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }

                                        
                                    } // for loop ends here 
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

                                }

                                if($slab_value->incentive_type==2) // 2 as Amount
                                {
                                    $level_name = '';
                                    $person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                    // dd($person_details);

                                    foreach($person_details as $p_key => $p_value)
                                    {
                                        $role_id = $p_value->role_id;
                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->select(DB::raw("SUM(quantity) as total_quantity"))
                                                        ->where('user_id',$p_value->id)
                                                        ->where('date',date('Y-m-d'))
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

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }

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
                                                // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }
                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }

                                        
                                    } // for loop ends here 
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
                                    $person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                    // dd($person_details);

                                    foreach($person_details as $p_key => $p_value)
                                    {
                                        $role_id = $p_value->role_id;
                                        $sale_data_fetch = DB::table('user_sales_order')
                                                        ->join('user_sales_order_details','user_sales_order_details.order_id','=','user_sales_order.order_id')
                                                        ->select(DB::raw("SUM(quantity) as total_quantity"))
                                                        ->where('user_id',$p_value->id)
                                                        ->where('date',date('Y-m-d'))
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

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }

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
                                                // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }
                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }

                                        
                                    } // for loop ends here 
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

                                }
                            } // pcs end shere 

                        }
                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
					}
					else
					{
						return response()->json([ 'response' =>False,'result'=>[]]);
					}
				}
				elseif($fectch_incentive_details->plan_category_status==3) // as outlet opnening
				{
                    // run query 
                    $fectch_incentive_slabs = UserIncentiveSlabs::where('plan_id',$fectch_incentive_details->id)->orderBy('id')->get();
                        if(!empty($fectch_incentive_slabs))
						{
							
							foreach ($fectch_incentive_slabs as $slab_key => $slab_value) 
							{
                                // dd($slab_value);
								$sale_value_range_first = $slab_value->sale_value_range_first;
								$sale_value_range_last = $slab_value->sale_value_range_last;

								if($slab_value->sale_unit==4) // as per range
								{
									if($slab_value->incentive_type==1) // 1 as percentage
									{
                                        $level_name = '';
										$person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                        // dd($person_details);

										foreach($person_details as $p_key => $p_value)
										{
                                            $role_id = $p_value->role_id;
                                            $cur_date = date('Y-m-d');
                                            $sale_data_fetch = DB::table('retailer')
                                                            ->select(DB::raw("COUNT(id) as retailer_count"))
                                                            ->where('created_by_person_id',$p_value->id)
                                                            ->where('company_id',$company_id)
                                                            ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')='$cur_date'")
                                                            ->first();
                                            if(empty($sale_data_fetch))
                                            {
                                                $role_wise_sum_array [] = 0;
                                            }
                                            // comparison parts starts here
                                            if($sale_value_range_last=='~')
                                            {
                                                
                                                if($sale_value_range_first < $sale_data_fetch->retailer_count)
                                                {
                                                    $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                    if($p_value->id == $request->user_id) // if comparsion is true 
                                                    {
                                                        $role_wise_sum_array [] = 0;	
                                                    }
                                                    else
                                                    {
                                                        
                                                        $fetch_role_group = DB::table('_role_group')
                                                                        ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                        ->select('_role_group.id as group_id')
                                                                        ->where('role_id',$role_id)
                                                                        ->where('_role_group.company_id',$company_id)
                                                                        ->where('_role.company_id',$company_id)
                                                                        ->first();

                                                        $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                        $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                    }

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
                                                
                                                if($sale_value_range_first < $sale_data_fetch->retailer_count && $sale_value_range_last > $sale_data_fetch->retailer_count)
                                                {

                                                    $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                    if($p_value->id == $request->user_id) // if comparsion is true 
                                                    {
                                                        $role_wise_sum_array [] = 0;	
                                                    }
                                                    else
                                                    {
                                                        
                                                        $fetch_role_group = DB::table('_role_group')
                                                                        ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                        ->select('_role_group.id as group_id')
                                                                        ->where('role_id',$role_id)
                                                                        ->where('_role_group.company_id',$company_id)
                                                                        ->where('_role.company_id',$company_id)
                                                                        ->first();

                                                        $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                        $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                    }
                                                    $temp_array_for_return = 'TRUE';
                                                        
                                                }
                                                else
                                                {
                                                    $temp_array_for_return = 'FALSE';
                                                    $role_wise_sum_array[] = 0;
                                                }
                                            }

											
										} // for loop ends here 
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

									}
									elseif($slab_value->incentive_type==2) // 2 as amount
									{
                                        $level_name = '';
										$person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                        // dd($person_details);

										foreach($person_details as $p_key => $p_value)
										{
                                            $role_id = $p_value->role_id;
											$cur_date = date('Y-m-d');
                                            $sale_data_fetch = DB::table('retailer')
                                                            ->select(DB::raw("COUNT(id) as retailer_count"))
                                                            ->where('created_by_person_id',$p_value->id)
                                                            ->where('company_id',$company_id)
                                                            ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')='$cur_date'")
                                                            ->first();
                                                            // dd($sale_data_fetch);
                                            if(empty($sale_data_fetch))
                                            {
                                                $role_wise_sum_array [] = 0;
                                            }
                                            // comparison parts starts here
                                            if($sale_value_range_last=='~')
                                            {
                                                // $check_comparision = UserIncentiveSlabs::where('plan_isale_value_range_first','>',$sale_data_fetch->sale_value)
                                                //                 ->first();
                                                
                                                if($sale_value_range_first < $sale_data_fetch->retailer_count)
                                                {
                                                    $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                    if($p_value->id == $request->user_id) // if comparsion is true 
                                                    {
                                                        $role_wise_sum_array [] = 0;	
                                                    }
                                                    else
                                                    {
                                                        
                                                        $fetch_role_group = DB::table('_role_group')
                                                                        ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                        ->select('_role_group.id as group_id')
                                                                        ->where('role_id',$role_id)
                                                                        ->where('_role_group.company_id',$company_id)
                                                                        ->where('_role.company_id',$company_id)
                                                                        ->first();

                                                        $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                        $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                    }

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
                                                // $check_comparision = UserIncentiveSlabs::where('plan_isale_value_range_first','>',$sale_data_fetch->sale_value)
                                                //                 ->where('sale_value_range_last','<',$sale_data_fetch->sale_value)
                                                //                 ->first();
                                                
                                                if($sale_value_range_first < $sale_data_fetch->retailer_count && $sale_value_range_last > $sale_data_fetch->retailer_count)
                                                {
                                                // dd($sale_data_fetch->sale_value);

                                                    $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                    if($p_value->id == $request->user_id) // if comparsion is true 
                                                    {
                                                        $role_wise_sum_array [] = 0;	
                                                    }
                                                    else
                                                    {
                                                        
                                                        $fetch_role_group = DB::table('_role_group')
                                                                        ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                        ->select('_role_group.id as group_id')
                                                                        ->where('role_id',$role_id)
                                                                        ->where('_role_group.company_id',$company_id)
                                                                        ->where('_role.company_id',$company_id)
                                                                        ->first();

                                                        $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                        $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                    }
                                                    $temp_array_for_return = 'TRUE';
                                                        
                                                }
                                                else
                                                {
                                                    $temp_array_for_return = 'FALSE';
                                                    $role_wise_sum_array[] = 0;
                                                }
                                            }

											
										} // for loop ends here 
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
									elseif($slab_value->incentive_type==3) // 3 as point 
									{
                                        $level_name = '';
										$person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                        // dd($person_details);

										foreach($person_details as $p_key => $p_value)
										{
                                            $role_id = $p_value->role_id;
											$cur_date = date('Y-m-d');
                                            $sale_data_fetch = DB::table('retailer')
                                                            ->select(DB::raw("COUNT(id) as retailer_count"))
                                                            ->where('created_by_person_id',$p_value->id)
                                                            ->where('company_id',$company_id)
                                                            ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')='$cur_date'")
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
                                                
                                                if($sale_value_range_first < $sale_data_fetch->retailer_count)
                                                {
                                                    $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                    if($p_value->id == $request->user_id) // if comparsion is true 
                                                    {
                                                        $role_wise_sum_array [] = 0;	
                                                    }
                                                    else
                                                    {
                                                        
                                                        $fetch_role_group = DB::table('_role_group')
                                                                        ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                        ->select('_role_group.id as group_id')
                                                                        ->where('role_id',$role_id)
                                                                        ->where('_role_group.company_id',$company_id)
                                                                        ->where('_role.company_id',$company_id)
                                                                        ->first();

                                                        $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                        $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                    }

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
                                                
                                                if($sale_value_range_first < $sale_data_fetch->retailer_count && $sale_value_range_last > $sale_data_fetch->retailer_count)
                                                {
                                                // dd($sale_data_fetch->sale_value);

                                                    $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                    if($p_value->id == $request->user_id) // if comparsion is true 
                                                    {
                                                        $role_wise_sum_array [] = 0;	
                                                    }
                                                    else
                                                    {
                                                        
                                                        $fetch_role_group = DB::table('_role_group')
                                                                        ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                        ->select('_role_group.id as group_id')
                                                                        ->where('role_id',$role_id)
                                                                        ->where('_role_group.company_id',$company_id)
                                                                        ->where('_role.company_id',$company_id)
                                                                        ->first();

                                                        $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                        $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                    }
                                                    $temp_array_for_return = 'TRUE';
                                                        
                                                }
                                                else
                                                {
                                                    $temp_array_for_return = 'FALSE';
                                                    $role_wise_sum_array[] = 0;
                                                }
                                            }

											
										} // for loop ends here 
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

                                

                            } // as foreach ends here 
                            
                            return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);

						} // !empty fectch_incentive_slabs if ends here 
						else
						{
							return response()->json([ 'response' =>False,'result'=>[]]);
						}
                    
				}
				elseif($fectch_incentive_details->plan_category_status==4) // as range selling 
				{
                    // run query 
                    $fectch_incentive_slabs = UserIncentiveSlabs::where('plan_id',$fectch_incentive_details->id)->orderBy('id')->get();
                    if(!empty($fectch_incentive_slabs))
                    {
                        
                        foreach ($fectch_incentive_slabs as $slab_key => $slab_value) 
                        {
                            // dd($slab_value);
                            $sale_value_range_first = $slab_value->sale_value_range_first;
                            $sale_value_range_last = $slab_value->sale_value_range_last;

                            if($slab_value->sale_unit==4) // as per range
                            {
                                if($slab_value->incentive_type==1) // 1 as percentage
                                {
                                    $level_name = '';
                                    $person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                    // dd($person_details);

                                    foreach($person_details as $p_key => $p_value)
                                    {
                                        $role_id = $p_value->role_id;
                                        $cur_date = date('Y-m-d');
                                        $sale_data_fetch = DB::table('retailer')
                                                        ->select(DB::raw("COUNT(id) as retailer_count"))
                                                        ->where('created_by_person_id',$p_value->id)
                                                        ->where('company_id',$company_id)
                                                        ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')='$cur_date'")
                                                        ->first();
                                        if(empty($sale_data_fetch))
                                        {
                                            $role_wise_sum_array [] = 0;
                                        }
                                        // comparison parts starts here
                                        if($sale_value_range_last=='~')
                                        {
                                            
                                            if($sale_value_range_first < $sale_data_fetch->retailer_count)
                                            {
                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }

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
                                            
                                            if($sale_value_range_first < $sale_data_fetch->retailer_count && $sale_value_range_last > $sale_data_fetch->retailer_count)
                                            {

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }
                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }

                                        
                                    } // for loop ends here 
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

                                }
                                elseif($slab_value->incentive_type==2) // 2 as amount
                                {
                                    $level_name = '';
                                    $person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                    // dd($person_details);

                                    foreach($person_details as $p_key => $p_value)
                                    {
                                        $role_id = $p_value->role_id;
                                        $cur_date = date('Y-m-d');
                                        $sale_data_fetch = DB::table('retailer')
                                                        ->select(DB::raw("COUNT(id) as retailer_count"))
                                                        ->where('created_by_person_id',$p_value->id)
                                                        ->where('company_id',$company_id)
                                                        ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')='$cur_date'")
                                                        ->first();
                                                        // dd($sale_data_fetch);
                                        if(empty($sale_data_fetch))
                                        {
                                            $role_wise_sum_array [] = 0;
                                        }
                                        // comparison parts starts here
                                        if($sale_value_range_last=='~')
                                        {
                                            // $check_comparision = UserIncentiveSlabs::where('plan_isale_value_range_first','>',$sale_data_fetch->sale_value)
                                            //                 ->first();
                                            
                                            if($sale_value_range_first < $sale_data_fetch->retailer_count)
                                            {
                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }

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
                                            // $check_comparision = UserIncentiveSlabs::where('plan_isale_value_range_first','>',$sale_data_fetch->sale_value)
                                            //                 ->where('sale_value_range_last','<',$sale_data_fetch->sale_value)
                                            //                 ->first();
                                            
                                            if($sale_value_range_first < $sale_data_fetch->retailer_count && $sale_value_range_last > $sale_data_fetch->retailer_count)
                                            {
                                            // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }
                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }

                                        
                                    } // for loop ends here 
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
                                elseif($slab_value->incentive_type==3) // 3 as point 
                                {
                                    $level_name = '';
                                    $person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                    // dd($person_details);

                                    foreach($person_details as $p_key => $p_value)
                                    {
                                        $role_id = $p_value->role_id;
                                        $cur_date = date('Y-m-d');
                                        $sale_data_fetch = DB::table('retailer')
                                                        ->select(DB::raw("COUNT(id) as retailer_count"))
                                                        ->where('created_by_person_id',$p_value->id)
                                                        ->where('company_id',$company_id)
                                                        ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')='$cur_date'")
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
                                            
                                            if($sale_value_range_first < $sale_data_fetch->retailer_count)
                                            {
                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }

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
                                            
                                            if($sale_value_range_first < $sale_data_fetch->retailer_count && $sale_value_range_last > $sale_data_fetch->retailer_count)
                                            {
                                            // dd($sale_data_fetch->sale_value);

                                                $role_wise_sum_array[] = $slab_value->value_amount_percentage;

                                                if($p_value->id == $request->user_id) // if comparsion is true 
                                                {
                                                    $role_wise_sum_array [] = 0;	
                                                }
                                                else
                                                {
                                                    
                                                    $fetch_role_group = DB::table('_role_group')
                                                                    ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                    ->select('_role_group.id as group_id')
                                                                    ->where('role_id',$role_id)
                                                                    ->where('_role_group.company_id',$company_id)
                                                                    ->where('_role.company_id',$company_id)
                                                                    ->first();

                                                    $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                    $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                }
                                                $temp_array_for_return = 'TRUE';
                                                    
                                            }
                                            else
                                            {
                                                $temp_array_for_return = 'FALSE';
                                                $role_wise_sum_array[] = 0;
                                            }
                                        }

                                        
                                    } // for loop ends here 
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

                            

                        } // as foreach ends here 
                        
                        return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);

                    } // !empty fectch_incentive_slabs if ends here 
                    else
                    {
                        return response()->json([ 'response' =>False,'result'=>[]]);
                    }
                   
                    
				}
				elseif($fectch_incentive_details->plan_category_status==5) // as productivity % 
				{
					 // run query 
                    $fectch_incentive_slabs = UserIncentiveSlabs::where('plan_id',$fectch_incentive_details->id)->orderBy('id')->get();
                    if(!empty($fectch_incentive_slabs))
                    {
                         
                         foreach ($fectch_incentive_slabs as $slab_key => $slab_value) 
                         {
                             // dd($slab_value);
                             $sale_value_range_first = $slab_value->sale_value_range_first;
                             $sale_value_range_last = $slab_value->sale_value_range_last;
 
                             if($slab_value->sale_unit==4) // as per range
                             {
                                 if($slab_value->incentive_type==1) // 1 as percentage
                                 {
                                     $level_name = '';
                                     $person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                     // dd($person_details);
 
                                     foreach($person_details as $p_key => $p_value)
                                     {
                                         $role_id = $p_value->role_id;
                                         $cur_date = date('Y-m-d');
                                         $sale_data_fetch = DB::table('retailer')
                                                         ->select(DB::raw("COUNT(id) as retailer_count"))
                                                         ->where('created_by_person_id',$p_value->id)
                                                         ->where('company_id',$company_id)
                                                         ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')='$cur_date'")
                                                         ->first();
                                         if(empty($sale_data_fetch))
                                         {
                                             $role_wise_sum_array [] = 0;
                                         }
                                         // comparison parts starts here
                                         if($sale_value_range_last=='~')
                                         {
                                             
                                             if($sale_value_range_first < $sale_data_fetch->retailer_count)
                                             {
                                                 $role_wise_sum_array[] = $slab_value->value_amount_percentage;
 
                                                 if($p_value->id == $request->user_id) // if comparsion is true 
                                                 {
                                                     $role_wise_sum_array [] = 0;	
                                                 }
                                                 else
                                                 {
                                                     
                                                     $fetch_role_group = DB::table('_role_group')
                                                                     ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                     ->select('_role_group.id as group_id')
                                                                     ->where('role_id',$role_id)
                                                                     ->where('_role_group.company_id',$company_id)
                                                                     ->where('_role.company_id',$company_id)
                                                                     ->first();
 
                                                     $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                     $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                 }
 
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
                                             
                                             if($sale_value_range_first < $sale_data_fetch->retailer_count && $sale_value_range_last > $sale_data_fetch->retailer_count)
                                             {
 
                                                 $role_wise_sum_array[] = $slab_value->value_amount_percentage;
 
                                                 if($p_value->id == $request->user_id) // if comparsion is true 
                                                 {
                                                     $role_wise_sum_array [] = 0;	
                                                 }
                                                 else
                                                 {
                                                     
                                                     $fetch_role_group = DB::table('_role_group')
                                                                     ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                     ->select('_role_group.id as group_id')
                                                                     ->where('role_id',$role_id)
                                                                     ->where('_role_group.company_id',$company_id)
                                                                     ->where('_role.company_id',$company_id)
                                                                     ->first();
 
                                                     $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                     $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                 }
                                                 $temp_array_for_return = 'TRUE';
                                                     
                                             }
                                             else
                                             {
                                                 $temp_array_for_return = 'FALSE';
                                                 $role_wise_sum_array[] = 0;
                                             }
                                         }
 
                                         
                                     } // for loop ends here 
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
 
                                 }
                                 elseif($slab_value->incentive_type==2) // 2 as amount
                                 {
                                     $level_name = '';
                                     $person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                     // dd($person_details);
 
                                     foreach($person_details as $p_key => $p_value)
                                     {
                                         $role_id = $p_value->role_id;
                                         $cur_date = date('Y-m-d');
                                         $sale_data_fetch = DB::table('retailer')
                                                         ->select(DB::raw("COUNT(id) as retailer_count"))
                                                         ->where('created_by_person_id',$p_value->id)
                                                         ->where('company_id',$company_id)
                                                         ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')='$cur_date'")
                                                         ->first();
                                                         // dd($sale_data_fetch);
                                         if(empty($sale_data_fetch))
                                         {
                                             $role_wise_sum_array [] = 0;
                                         }
                                         // comparison parts starts here
                                         if($sale_value_range_last=='~')
                                         {
                                             // $check_comparision = UserIncentiveSlabs::where('plan_isale_value_range_first','>',$sale_data_fetch->sale_value)
                                             //                 ->first();
                                             
                                             if($sale_value_range_first < $sale_data_fetch->retailer_count)
                                             {
                                                 $role_wise_sum_array[] = $slab_value->value_amount_percentage;
 
                                                 if($p_value->id == $request->user_id) // if comparsion is true 
                                                 {
                                                     $role_wise_sum_array [] = 0;	
                                                 }
                                                 else
                                                 {
                                                     
                                                     $fetch_role_group = DB::table('_role_group')
                                                                     ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                     ->select('_role_group.id as group_id')
                                                                     ->where('role_id',$role_id)
                                                                     ->where('_role_group.company_id',$company_id)
                                                                     ->where('_role.company_id',$company_id)
                                                                     ->first();
 
                                                     $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                     $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                 }
 
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
                                             // $check_comparision = UserIncentiveSlabs::where('plan_isale_value_range_first','>',$sale_data_fetch->sale_value)
                                             //                 ->where('sale_value_range_last','<',$sale_data_fetch->sale_value)
                                             //                 ->first();
                                             
                                             if($sale_value_range_first < $sale_data_fetch->retailer_count && $sale_value_range_last > $sale_data_fetch->retailer_count)
                                             {
                                             // dd($sale_data_fetch->sale_value);
 
                                                 $role_wise_sum_array[] = $slab_value->value_amount_percentage;
 
                                                 if($p_value->id == $request->user_id) // if comparsion is true 
                                                 {
                                                     $role_wise_sum_array [] = 0;	
                                                 }
                                                 else
                                                 {
                                                     
                                                     $fetch_role_group = DB::table('_role_group')
                                                                     ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                     ->select('_role_group.id as group_id')
                                                                     ->where('role_id',$role_id)
                                                                     ->where('_role_group.company_id',$company_id)
                                                                     ->where('_role.company_id',$company_id)
                                                                     ->first();
 
                                                     $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                     $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                 }
                                                 $temp_array_for_return = 'TRUE';
                                                     
                                             }
                                             else
                                             {
                                                 $temp_array_for_return = 'FALSE';
                                                 $role_wise_sum_array[] = 0;
                                             }
                                         }
 
                                         
                                     } // for loop ends here 
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
                                 elseif($slab_value->incentive_type==3) // 3 as point 
                                 {
                                     $level_name = '';
                                     $person_details = DB::table('person')->whereIn('id',$junior_data_check)->where('company_id',$company_id)->get();
                                     // dd($person_details);
 
                                     foreach($person_details as $p_key => $p_value)
                                     {
                                         $role_id = $p_value->role_id;
                                         $cur_date = date('Y-m-d');
                                         $sale_data_fetch = DB::table('retailer')
                                                         ->select(DB::raw("COUNT(id) as retailer_count"))
                                                         ->where('created_by_person_id',$p_value->id)
                                                         ->where('company_id',$company_id)
                                                         ->whereRaw("DATE_FORMAT(created_on,'%Y-%m-%d')='$cur_date'")
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
                                             
                                             if($sale_value_range_first < $sale_data_fetch->retailer_count)
                                             {
                                                 $role_wise_sum_array[] = $slab_value->value_amount_percentage;
 
                                                 if($p_value->id == $request->user_id) // if comparsion is true 
                                                 {
                                                     $role_wise_sum_array [] = 0;	
                                                 }
                                                 else
                                                 {
                                                     
                                                     $fetch_role_group = DB::table('_role_group')
                                                                     ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                     ->select('_role_group.id as group_id')
                                                                     ->where('role_id',$role_id)
                                                                     ->where('_role_group.company_id',$company_id)
                                                                     ->where('_role.company_id',$company_id)
                                                                     ->first();
 
                                                     $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                     $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                 }
 
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
                                             
                                             if($sale_value_range_first < $sale_data_fetch->retailer_count && $sale_value_range_last > $sale_data_fetch->retailer_count)
                                             {
                                             // dd($sale_data_fetch->sale_value);
 
                                                 $role_wise_sum_array[] = $slab_value->value_amount_percentage;
 
                                                 if($p_value->id == $request->user_id) // if comparsion is true 
                                                 {
                                                     $role_wise_sum_array [] = 0;	
                                                 }
                                                 else
                                                 {
                                                     
                                                     $fetch_role_group = DB::table('_role_group')
                                                                     ->join('_role','_role.role_group_id','=','_role_group.id')
                                                                     ->select('_role_group.id as group_id')
                                                                     ->where('role_id',$role_id)
                                                                     ->where('_role_group.company_id',$company_id)
                                                                     ->where('_role.company_id',$company_id)
                                                                     ->first();
 
                                                     $fetch_role_wise_data = UserIncentiveRoleDistribution::where('role_group',$fetch_role_group->group_id)->first();
                                                     $role_wise_sum_array[] = $fetch_role_wise_data->amount;
                                                 }
                                                 $temp_array_for_return = 'TRUE';
                                                     
                                             }
                                             else
                                             {
                                                 $temp_array_for_return = 'FALSE';
                                                 $role_wise_sum_array[] = 0;
                                             }
                                         }
 
                                         
                                     } // for loop ends here 
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
 
                             
 
                         } // as foreach ends here 
                         
                         return response()->json([ 'response' =>True,'level_name'=> $level_name ,'level_result'=>$final_sum_role_wise ]);
 
                     } // !empty fectch_incentive_slabs if ends here 
                     else
                     {
                         return response()->json([ 'response' =>False,'result'=>[]]);
                     }
				}
				elseif($fectch_incentive_details->plan_category_status==6) // other 
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
			$fectch_incentive_details = UserIncentiveDetails::where('status',1)->where('id',$fetch_plan->plan_id)->first();
			
			if(!empty($fectch_incentive_details))
			{
				$plan_name = $fectch_incentive_details->plan_name; 

				if($fectch_incentive_details->plan_category_status==1) // vps value per sale 
				{
					if($fectch_incentive_details->vs_status==1) // as total sale value vps
					{
						// run query 
						$fectch_incentive_slabs = UserIncentiveSlabs::where('plan_id',$fectch_incentive_details->id)->first();
						$sale_value_range_first = $fectch_incentive_slabs->sale_value_range_first;
						$sale_value_range_last = $fectch_incentive_slabs->sale_value_range_last;
						if(!empty($fectch_incentive_slabs))
						{
							if($fectch_incentive_slabs->sale_unit==1) // as per weight
							{
								if($fectch_incentive_slabs->incentive_type==1) // 1 as percentage
								{

								}
								elseif($fectch_incentive_slabs->incentive_type==2) // 2 as amount
								{

								}
								elseif($fectch_incentive_slabs->incentive_type==3) // 3 as point 
								{
									
								}
								else
								{
									return response()->json([ 'response' =>False,'result'=>[]]);
								}
							}
							elseif($fectch_incentive_slabs->sale_unit==2) // as per cases	
							{

							}
							elseif($fectch_incentive_slabs->sale_unit==3) // as per pcs
							{

							}
							elseif($fectch_incentive_slabs->sale_unit==4) // as per range
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
					elseif($fectch_incentive_details->vs_status==2)  // as item wised vps 
					{
						if($fectch_incentive_details->item_status==1) // as combo wise
						{
							// run query 

						}
						elseif($fectch_incentive_details->item_status==2) // as single sku wise
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

				elseif($fectch_incentive_details->plan_category_status==2) // qs quantity sale 
				{
					if($fectch_incentive_details->vs_status==2)
					{
						if($fectch_incentive_details->item_status==1) // as combo wise
						{
							// run query 

						}
						elseif($fectch_incentive_details->item_status==2) // as single sku wise
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
				elseif($fectch_incentive_details->plan_category_status==3) // as outlet opnening
				{
                    // run query 
                    
                    
				}
				elseif($fectch_incentive_details->plan_category_status==4) // as range selling 
				{
					// run query 
				}
				elseif($fectch_incentive_details->plan_category_status==5) // as productivity % 
				{
					// run query 
				}
				elseif($fectch_incentive_details->plan_category_status==6) // other 
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
