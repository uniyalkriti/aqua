<?php

namespace App\Http\Controllers;

use App\Dealer;
use App\DealerLocation;
use App\DealerPersonLogin;
use App\Location1; 
use App\Location2;
use App\Location3;
use App\Location4;
use App\Location5;
use App\SS;
use DB;
use App\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use PDF;

class SaleOrderController extends Controller
{
    public function __construct()
    {
        $this->current_menu='saleorder';

        $this->status_table='saleorder';
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd($request);
        $region = $request->region;
        $user_id = $request->user_id;
        $product=$request->product;
        $status=$request->status;
        $role_id=$request->role_id;
        $retailer_type=$request->retailer_type;
        $company_id = Auth::user()->company_id;
        $data_master = Auth::user();
        $dms_status_order_query = array();
        // $view_action_icon = 'True';
        $permissions = DB::table('company_sub_web_module_permission')
                    ->where('company_id',$company_id)
                    ->where('role_id',Auth::user()->role_id)
                    ->where('sub_module_id',72)
                    ->first();
        
        if ($data_master->role_id == 239) // for support
        {
            $status = 0;
        }
        elseif ($data_master->role_id == 238) // for payment 
        {
            $status = 2;
        }
        elseif ($data_master->role_id == 237) // for logistic 
        {
            $status = 1;
        }
        elseif ($data_master->role_id == 299) // for invoice 
        {
            $status = 3;
        }
        else
        {
            $status = 9;
        }
     

        $flag = 1;
        $query = [];
        $state = DB::table('location_3')->where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $role = DB::table('_role')->where('status',1)->where('company_id',$company_id)->pluck('rolename','role_id');
        $reject_reason_dms = DB::table('dms_order_cancel_reason')->where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $payment_modes = DB::table('_payment_modes')->where('status',1)->pluck('mode','id');
        $user_name = DB::table('person')->join('person_login','person_login.person_id','=','person.id')
                    ->where('person_status',1)
                    ->where('person.company_id',$company_id)
                    ->pluck(DB::raw("CONCAT_WS(' ',first_name,middle_name,last_name) as user_name"),'id');
        // $status = 
        if($flag == 1)
        {
            if(!empty($request->date_range_picker))
            {
                $explodeDate = explode(" -", $request->date_range_picker);
                $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
                $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            }
            else
            {
                $from_date = date('Y-m-d');
                $to_date = date('Y-m-d');
            }
           
            // dd($status);
            $query_data = DB::table('purchase_order')
                        ->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
                        ->join('dealer','dealer.id','=','purchase_order.dealer_id')
                        // ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                        ->join('location_3','location_3.id','=','dealer.state_id')
                        // ->join('_dms_reason','_dms_reason.id','=','purchase_order.dms_order_reason_id')
                        ->select('location_3.name as l3_name','pdf_name2','pdf_name','dms_order_reason_id','purchase_order.order_id as order_id','purchase_order.dealer_id as dealer_id','dealer.other_numbers as mobile_number',DB::raw("SUM(purchase_order_details.cases) as cases"),'cases as 1pert',DB::raw("SUM(pcs) as pcs"),DB::raw("SUM((pr_rate*cases)+(rate*pcs)) as total_vale"),'dealer.name as dealer_name','app_flag','sale_date','dealer.email as d_email')
                        ->where('purchase_order.company_id',$company_id)
                        ->where('purchase_order_details.company_id',$company_id)
                        ->where('dealer.company_id',$company_id)
                        ->where('purchase_order_details.app_flag','!=',1)
                        ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(sale_date,'%Y-%m-%d')<='$to_date'")
                        ->groupBy('purchase_order_details.order_id');
                        // ->get();

           if(!empty($dealer_id))
            {   
                $query_data->whereIn('purchase_order.dealer_id',$dealer_id);
            }
            if(!empty($status) || $status == 0)
            {   
                if($status == 9)
                {
                    $no_use = 'only for handling';
                }
                else
                {
                    $query_data->where('purchase_order.dms_order_reason_id',$status);
                }
            }
           
          
         


            $query=$query_data->get();
            // dd($query);
            $location_data = DB::table('dealer_location_rate_list')
                            ->join('location_view','location_view.l7_id','=','dealer_location_rate_list.location_id')
                            ->where('company_id',$company_id)
                            ->groupBy('dealer_id')
                            ->pluck('l6_name','dealer_id');
            $out=array();
            $proout=array();

            if (!empty($query)) 
            {
                foreach ($query as $k => $d) 
                {
                    $uid=$d->order_id;
                     $proout = DB::table('purchase_order_details')
                    ->join('catalog_product','catalog_product.id','=','purchase_order_details.product_id')
                    ->where('order_id', $uid)
                    ->select('product_id','cases','pcs as quantity','pr_rate as cases_rate','rate','catalog_product.name as product_name','scheme_qty as weight');
                    
                   if(!empty($product))
                   {
                        $proout->whereIn('product_id',$product);
                   }
                    $out[$uid]=$proout->get(); 
                }
            }
            // dd($query);
            $dms_status_order_query = DB::table('_dms_reason')->where('status',1)->pluck('name','id');
        }   
        
        $travell_mode = DB::table('_vehicle_details')->where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $dms_plant_master = DB::table('_dms_plant_master')->where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $product_filter_array = DB::table('catalog_product')->where('status',1)->where('company_id',$company_id)->pluck('name','id');

       
     


        return view($this->current_menu.'.index', [
            'records' => $query,
            'details' => $out,
            'region' => $state,
            'user' => $user_name,
            'role' => $role,
            'payment_modes'=> $payment_modes,
            'product_filter_array'=> $product_filter_array,
            'reject_reason_dms'=>$reject_reason_dms,
            'dms_status_order_query'=> $dms_status_order_query,
            'dms_plant_master'=> $dms_plant_master,
            'travell_mode'=>$travell_mode,
            'permissions'=> $permissions,
            'location_data'=> $location_data,
            'is_admin' => $data_master->is_admin,
            'current_menu'=>$this->current_menu
        ]);

    }


    public function submit_order_edit_form(Request $request)
    {
        $product_id = $request->product_id;
        $rate = $request->rate;
        $old_qty = $request->old_qty;
        $new_qty = $request->qty;
        $total_value = $request->total;
        $order_id = $request->order_id;
        $user_id = $request->user_id;
        $retailer_id = $request->retailer_id;
        $order_date = $request->order_date;
        $product_name = $request->product_name;
        $retailer_name = $request->retailer_name;
        $remarks = $request->remarks;
        $status = $request->status;


        $order_id_new = array_unique($order_id);
        $retailer_id_new = array_unique($retailer_id);
        $user_id_new = array_unique($user_id);
        $retailer_name_new = array_unique($retailer_name);
        $order_date_new = array_unique($order_date);
        $total_sale_value_new = array_sum($total_value);

        foreach ($product_id as $key => $value) 
        {
            $myArr = [
                'order_id' => $order_id[$key],
                'product_id' => $value,
                'product_name' => $product_name[$key],
                'product_qty' => $old_qty[$key],
                'product_rate' => $rate[$key],
                'product_value' => $total_value[$key],
                'product_fullfiment_qty' => $new_qty[$key],
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $fullfillment_insert_query = DB::table('fullfillment_order_details')->insert($myArr);
        }

        $myArrOrder = [
                'order_id'=> $order_id_new[0],
                'retailer_id'=> $retailer_id_new[0],
                'user_id'=> $user_id_new[0],
                'retailer_name'=> $retailer_name_new[0],
                'order_date'=> $order_date_new[0],
                'fullfillment_value' => $total_sale_value_new,
                'date'=>date('Y-m-d'),
                'time'=>date('H:i:s'),
                'remarks'=>$remarks,
                'invoice_number'=>date('YmdHis'),
                'server_date'=>date('Y-m-d H:i:s'),
        ];
        // dd($myArrOrder);
        $fullfillment_order_insert_query = DB::table('fullfillment_order')->insert($myArrOrder);

        $user_sale_order_update_query = DB::table('user_sales_order')->where('order_id',array_unique($order_id))->update(['updated_at'=>date('Y-m-d H:i:s'),'status'=>$status]);

        if(!empty($fullfillment_order_insert_query) && !empty($fullfillment_insert_query) && !empty($user_sale_order_update_query))
        {
            DB::commit();
            Session::flash('message', Lang::get('common.'.$this->current_menu).' updated successfully');
            Session::flash('class', 'success');

        }
        else
        {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
        }
        return redirect()->intended($this->current_menu);

    }




    public function edit_order_details(Request $request)
    {

        $order_id = $request->order_id;

        $check = DB::table('user_sales_order')->where('order_id',$order_id)->first();
        // dd($check);
        $remarks_status = $check->remarks;         
        if($check->status==1)
        {
            $query_data =DB::table('user_sales_order_view')
            ->leftJoin('fullfillment_order','fullfillment_order.order_id','=','user_sales_order_view.order_id')
            ->select('gst_no','fullfillment_value','status_approval','user_sales_order_view.dealer_id as dealer_id','user_sales_order_view.user_id as user_id','user_sales_order_view.retailer_id as retailer_id','user_name AS user_name','dealer_name',DB::raw("DATE_FORMAT(user_sales_order_view.date,'%d-%m-%Y') AS date"),'user_sales_order_view.order_id','call_status','l4_name','user_sales_order_view.retailer_name','user_sales_order_view.time','mobile','track_address','total_sale_value')
            ->where('user_sales_order_view.order_id',$order_id)
            ->orderBy('user_sales_order_view.order_id','ASC')
            ->get();

            $edit_query = DB::table('user_sales_order_details_view')
                ->join('fullfillment_order_details','fullfillment_order_details.order_id','=','user_sales_order_details_view.order_id')
                ->join('user_sales_order_view','user_sales_order_view.order_id','user_sales_order_details_view.order_id')
                ->where('user_sales_order_details_view.order_id', $order_id)
                ->select('remarks',DB::raw("CONCAT(date,' ',time) as order_date"),'itemcode','product_fullfiment_qty','product_value','retailer_name','user_name','user_id','retailer_id','quantity','rate','user_sales_order_details_view.product_name as product_name','user_sales_order_details_view.product_id as product_id','scheme_qty as weight','user_sales_order_details_view.order_id as order_id')
                ->groupBy('user_sales_order_details_view.product_id')
                ->get();
                // dd($edit_query);
        }
        else
        {
            $query_data =DB::table('user_sales_order_view')
            ->leftJoin('fullfillment_order','fullfillment_order.order_id','=','user_sales_order_view.order_id')
            ->select('gst_no','fullfillment_value','status_approval','user_sales_order_view.dealer_id as dealer_id','user_sales_order_view.user_id as user_id','user_sales_order_view.retailer_id as retailer_id','user_name AS user_name','dealer_name',DB::raw("DATE_FORMAT(user_sales_order_view.date,'%d-%m-%Y') AS date"),'user_sales_order_view.order_id','call_status','l4_name','user_sales_order_view.retailer_name','user_sales_order_view.time','mobile','track_address','total_sale_value')
            ->where('user_sales_order_view.order_id',$order_id)
            ->orderBy('user_sales_order_view.order_id','ASC')
            ->get();

            $edit_query = DB::table('user_sales_order_details_view')
                ->join('user_sales_order_view','user_sales_order_view.order_id','user_sales_order_details_view.order_id')
                ->where('user_sales_order_details_view.order_id', $order_id)
                ->select(DB::raw("CONCAT(date,' ',time) as order_date"),'itemcode','retailer_name','user_name','user_id','retailer_id','quantity','rate','product_name','product_id','scheme_qty as weight','user_sales_order_details_view.order_id as order_id')
                ->get();
        }
        

       if($edit_query)
        {
            $data['code'] = 200;
            $data['result'] = $edit_query;
            $data['result_top'] = $query_data;
            $data['remarks'] = $remarks_status;
            $data['message'] = 'success';
        } 
        else 
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        #decrypt id
        $uid = Crypt::decryptString($id);
        $product_id = $request->product_id;
        $rate = $request->rate;
        $qty = $request->qty;

        foreach ($product_id as $key => $value) 
        {
            $total_sale_value_array[] = ($rate[$key]*$qty[$key]);
            $update_query = DB::table('user_sales_order_details')
                    ->where('order_id',$uid)
                    ->where('product_id',$value)
                    ->update(['quantity'=>$qty[$key],'updated_at'=>date('Y-m-d H:i:s')]);
        }
        $total_sale_value = array_sum($total_sale_value_array);
        $update_user_sales_order = DB::table('user_sales_order')->where('order_id',$uid)->update(['total_sale_value'=>$total_sale_value,'updated_at'=>date('Y-m-d H:i:s')]);

        
        if ($update_query) 
        {
            $update_user_sales_order = DB::table('user_sales_order')->where('order_id',$uid)->update(['total_sale_value'=>$total_sale_value,'updated_at'=>date('Y-m-d H:i:s')]);
            
            if($update_user_sales_order)
            {
                DB::commit();
                Session::flash('message', Lang::get('common.'.$this->current_menu).' updated successfully');
                Session::flash('class', 'success');
            }
            else
            {
                 DB::rollback();
                Session::flash('message', 'Something went wrong!');
                Session::flash('class', 'danger');
            }
            
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
        }

        return redirect()->intended($this->current_menu);
    }
    public function dms_get_order_details(Request $request)
    {
        $order_id = $request->order_id;
        $company_id = Auth::user()->company_id;

        $role_id = Auth::user()->role_id;
        $is_admin = Auth::user()->is_admin;

        if($company_id == 52)
        {
            $address_invoice = 'Patanjali Peya Pvt. Ltd.<br>
                LG-01, Aggarwal Cyber Plaza 1, Plot no. C 4,5 & 6,<br>
                District Center, Netaji Subhash Place, Wazirpur, Delhi, 110034';
        }
        elseif($company_id == 55)
        {
            $address_invoice = 'Piranha Communication <br>
                2204, 22ND FLOOR G-SQUARE BUSINESS PARK,<br>
                PLOT NO. 25 & 26 , Maharashtra, Code 27';
        }
        else
        {
            $address_invoice = 'mSELL';
        }
        
        $data_query = DB::table('purchase_order')
            ->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
            ->join('dealer','dealer.id','=','purchase_order.dealer_id')
            ->join('_vehicle_details','_vehicle_details.id','=','purchase_order.vehicle_id')
            // ->join('_dms_reason','_dms_reason.id','=','purchase_order.dms_order_reason_id')
            ->select('_vehicle_details.name as vehicle_name','dealer.id as dealer_id','dealer.name as dealer_name','purchase_order.order_id as order_id','dealer.email as dealer_email','dealer.address as dealer_address','dealer.other_numbers as dealer_mobile','sale_date','dealer.tin_no as dealer_gst_no')
            ->where('purchase_order.order_id',$order_id)
            ->where('purchase_order.company_id',$company_id)
            ->first();

        $invoice_data = DB::table('fullfillment_order')
            ->join('fullfillment_order_details','fullfillment_order_details.order_id','=','fullfillment_order.order_id')
            ->join('catalog_product','catalog_product.id','=','fullfillment_order_details.product_id')
            ->join('dealer','dealer.id','=','fullfillment_order.dealer_id')
            // ->join('_dms_reason','_dms_reason.id','=','purchase_order.dms_order_reason_id')
            ->select('weight','mfg_date','batch_no','catalog_product.id as product_id','catalog_product.name as product_name',DB::raw("SUM(fullfillment_order_details.product_fullfiment_cases) as cases"),DB::raw("SUM(fullfillment_order_details.product_fullfiment_scheme_qty) as pcs"),DB::raw("SUM(fullfillment_order_details.product_fullfiment_scheme_qty) as scheme_qty"),'product_case_rate as cases_rate')
            ->where('fullfillment_order.order_id',$order_id)
            ->where('fullfillment_order.company_id',$company_id)
            ->groupBy('fullfillment_order_details.id')
            ->get();

        $transport_details_data = DB::table('dms_transport_details')
            ->join('_dms_plant_master','_dms_plant_master.id','=','dms_transport_details.plant_id')
            ->join('_vehicle_details','_vehicle_details.id','=','dms_transport_details.travelling_id')
            ->select('dms_transport_details.*','_vehicle_details.name as tavel_mode_name','_dms_plant_master.name as plant_name')
            ->where('dms_transport_details.company_id',$company_id);
            if(!empty($order_id)){

                $transport_details_data->where('dms_transport_details.order_id',$order_id);
            }
            
        $transport_details = $transport_details_data->first();

        $product_details = DB::table('purchase_order')
            ->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
            ->join('catalog_product','catalog_product.id','=','purchase_order_details.product_id')
            ->join('dealer','dealer.id','=','purchase_order.dealer_id')
            // ->join('_dms_reason','_dms_reason.id','=','purchase_order.dms_order_reason_id')
            ->select('catalog_product.weight as weight','catalog_product.id as product_id','catalog_product.name as product_name',DB::raw("SUM(purchase_order_details.cases) as cases"),DB::raw("SUM(purchase_order_details.pcs) as pcs"),DB::raw("SUM(purchase_order_details.scheme_qty) as scheme_qty"),'pr_rate as cases_rate','rate as pcs_rate')
            ->where('purchase_order.order_id',$order_id)
            ->where('purchase_order.company_id',$company_id)
            ->groupBy('product_id')
            ->get();

        $payemt_details = DB::table('payment_collect_dealer')
                // ->where('dealer_id',$dealer_id)
                ->where('company_id',$company_id)
                ->where('order_id',$order_id)
                // ->whereRaw("DATE_FORMAT")
                ->first();
        $payemt_checked = DB::table('dms_order_reason_log')
                ->where('company_id',$company_id)
                ->where('order_id',$order_id)
                ->where('dms_reason_id',1)
                ->count();

        $payemt_recieved = DB::table('payment_collect_dealer')
                        // ->where('dealer_id',$dealer_id)
                        ->where('company_id',$company_id)
                        ->where('order_id',$order_id)
                        // ->whereRaw("DATE_FORMAT")
                        ->sum('amount_by_sfa');

        $payment_remarks = DB::table('payment_collect_dealer')
                        // ->where('dealer_id',$dealer_id)
                        ->where('company_id',$company_id)
                        ->where('order_id',$order_id)
                        // ->whereRaw("DATE_FORMAT")
                        ->first();

        $dispatch_payment_remarks = DB::table('fullfillment_order')
                        // ->where('dealer_id',$dealer_id)
                        ->where('company_id',$company_id)
                        ->where('order_id',$order_id)
                        // ->whereRaw("DATE_FORMAT")
                        ->first();
        // dd($payemt_recieved);


        $dispatch_checked = DB::table('dms_order_reason_log')
                ->where('company_id',$company_id)
                ->where('order_id',$order_id)
                ->where('dms_reason_id',3)
                ->count();


        $recject_checked =  DB::table('dms_order_reason_log')
                ->where('company_id',$company_id)
                ->where('order_id',$order_id)
                ->where('dms_reason_id',7)
                ->count();

        $cancel_checked = DB::table('dms_order_reason_log')
                ->where('company_id',$company_id)
                ->where('order_id',$order_id)
                ->where('dms_reason_id',5)
                ->count();

        $order_confirm_checked = DB::table('dms_order_reason_log')
                ->where('company_id',$company_id)
                ->where('order_id',$order_id)
                ->where('dms_reason_id',2)
                ->count();

        $invoice_generate_checked = DB::table('dms_order_reason_log')
                ->where('company_id',$company_id)
                ->where('order_id',$order_id)
                ->where('dms_reason_id',4)
                ->count();

        $catalog_product_filter = DB::table('catalog_product')
                        ->where('company_id',$company_id)
                        ->where('status',1)
                        ->pluck('name','id');

         $range_first = DB::table('scheme_plan_details')
                        ->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
                        // ->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id')
                        ->where('scheme_plan_details.company_id',$company_id)
                        ->where('sale_unit',2)
                        // ->where('product_id',$product_id)
                        ->where('incentive_type',3)
                        ->groupBy('product_id')
                        ->pluck('sale_value_range_first','product_id');
        $range_second = DB::table('scheme_plan_details')
                        ->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
                        // ->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id')
                        ->where('scheme_plan_details.company_id',$company_id)
                        ->where('sale_unit',2)
                        // ->where('product_id',$product_id)
                        ->where('incentive_type',3)
                        ->groupBy('product_id')
                        ->pluck('sale_value_range_last','product_id');


        $free_qty = DB::table('scheme_plan_details')
                        ->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
                        // ->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id')
                        ->where('scheme_plan_details.company_id',$company_id)
                        ->where('sale_unit',2)
                        // ->where('product_id',$product_id)
                        ->where('incentive_type',3)
                        ->groupBy('product_id')
                        ->pluck('value_amount_percentage','product_id');
            
        if($data_query)
        {
            $data['code'] = 200;
            $data['result_down'] = $product_details;
            $data['count_toat_order'] = COUNT($product_details);
            $data['result_top'] = $data_query;
            $data['transport_details'] = !empty($transport_details)?$transport_details:array();
            $data['dispatch_date'] = date('Y-m-d');
            $data['payemt_status'] = (($payemt_checked)>0)?1:0;
            $data['order_confirm_status'] = (($order_confirm_checked)>0)?1:0;
            $data['dispatch_status'] = (($dispatch_checked)>0)?1:0;
            $data['recject_status'] = (($recject_checked)>0)?1:0;
            $data['cacncel_status'] = (($cancel_checked)>0)?1:0;
            $data['invoice_generate_status'] = (($invoice_generate_checked)>0)?1:0;
            $data['payemt_recieved'] = $payemt_recieved;
            $data['payment_remarks'] = !empty($payment_remarks->remarks)?$payment_remarks->remarks:'NA';
            $data['dispatch_payment_remarks'] = !empty($dispatch_payment_remarks->dispatch_remarks)?$dispatch_payment_remarks->dispatch_remarks:'NA';
            $data['payemt_details'] = !empty($payemt_details->amount)?$payemt_details->amount:'0';
            $data['payment_remarks_app'] = !empty($payemt_details->remarks_app)?$payemt_details->remarks_app:'0';
            $data['invoice_data'] = $invoice_data;
            $data['catalog_product_filter'] = $catalog_product_filter;
            $data['range_first'] = $range_first;
            $data['range_second'] = $range_second;
            $data['free_qty'] = $free_qty;
            $data['role_id'] = $role_id;
            $data['is_admin'] = $is_admin;
            $data['address_invoice'] = $address_invoice;

            // $data['order_remarks'] = $payemt_recieved;

            $data['message'] = 'success';
        } 
        else 
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    public function submit_dms_order_details(Request $request)
    {
        // dd($request);
        $dispatch_date = $request->dispatch_date;
        $order_id = $request->order_id;
        $dealer_id = $request->dealer_id;
        // dd($dealer_id);
        $amount = $request->amount;
        $remarks = $request->remarks;
        $payment_mode = $request->payment_mode;
        $cheque_no = !empty($request->cheque_no)?$request->cheque_no:'0';
        $bank_branch = !empty($request->bank_branch)?$request->bank_branch:'NA';
        $cheque_date = !empty($request->cheque_date)?$request->cheque_date:'0000-00-00';
        $trans_no = !empty($request->trans_no)?$request->trans_no:'0';
        $payment_date = $request->payment_date;
        $company_id = Auth::user()->company_id;
        $myArr = [
            'dealer_id' => $dealer_id,
            'order_id'=> $order_id,
            'payment_mode' => $payment_mode,
            'amount_by_sfa'  => $amount,
            'bank_branch' => $bank_branch,
            'cheque_no' => $cheque_no,
            'cheque_date' => $cheque_date,
            'trans_no' => $trans_no,
            'payment_time' => date('H:i:s'),
            'payment_date' => $payment_date,
            'company_id'=> $company_id,

        ];
        $check = DB::table('payment_collect_dealer')->where('order_id',$order_id)->where('dealer_id',$dealer_id)->COUNT();
        // dd($check);
        if($check<=0)
        {
            $insert_query = DB::table('payment_collect_dealer')->insert($myArr);
            if($insert_query)
            {
                $update_query = DB::table('purchase_order')->where('order_id',$order_id)->update(['dispatch_date'=>$dispatch_date,'dms_order_reason_id'=>'1']);
                $insert_data_log = DB::table('dms_order_reason_log')->insert(['order_id'=>$order_id,'company_id'=>$company_id,'dms_reason_id'=>'1','date'=>date("Y-m-d"),'time'=>date('H:i:s')]);
               

                    DB::commit();
                    $data['code'] = 200;
                    $data['result'] = '';

            }   
            else
            {
                    // dd('qwe1234');

                DB::rollback();
                $data['code'] = 401;
                $data['result'] = '';
                

            }
        }
        else
        {
            $myArr2 = [
                
                'payment_mode' => $payment_mode,
                'amount_by_sfa'  => $amount,
                'bank_branch' => $bank_branch,
                'cheque_no' => $cheque_no,
                'cheque_date' => $cheque_date,
                'trans_no' => $trans_no,
                'payment_time' => date('H:i:s'),
                'payment_date' => $payment_date,
                'company_id'=> $company_id,

            ];
            // $update_query4 = DB::table('purchase_order')->where('order_id',$order_id)->update(['dispatch_date'=>$dispatch_date,'dms_order_reason_id'=>'1']);
            $update_query2 = DB::table('payment_collect_dealer')->where('order_id',$order_id)->where('dealer_id',$dealer_id)->update($myArr2);
            if($update_query2)
            {
                $update_query = DB::table('purchase_order')->where('order_id',$order_id)->update(['dispatch_date'=>$dispatch_date,'dms_order_reason_id'=>'1']);
                $insert_data_log = DB::table('dms_order_reason_log')->insert(['order_id'=>$order_id,'company_id'=>$company_id,'dms_reason_id'=>'1','date'=>date("Y-m-d"),'time'=>date('H:i:s')]);
               

                    DB::commit();
                    $data['code'] = 200;
                    $data['result'] = '';

            }   
            else
            {
                    // dd('qwe1234');

                DB::rollback();
                $data['code'] = 401;
                $data['result'] = '';
                

            }

        }
        $user_data  = DB::table('dealer_location_rate_list')
                        // ->join('dealer_person_login','dealer_person_login.dealer_id','=','dealer_location_rate_list.dealer_id')
                        ->join('person','person.id','=','dealer_location_rate_list.user_id')
                        ->join('users','users.id','=','dealer_location_rate_list.user_id')
                        ->select(DB::raw("concat_ws(' ',first_name,middle_name,last_name) as user_name"),'person.dms_token','person.id as user_id')
                        ->where('is_admin','!=',1)
                        ->where('dealer_location_rate_list.company_id','=',$company_id)
                        ->where('dealer_location_rate_list.dealer_id',$dealer_id)
                        ->get();
        
        $data_notifi = DB::table('_dms_reason')->where('id',1)->first();
        $msg = !empty($data_notifi->noti_msg)?$data_notifi->noti_msg.$order_id:''.$order_id;

        foreach ($user_data as $key => $value) 
        {
            $fcm_token = !empty($value->dms_token)?$value->dms_token:'0';
            // $msg = 'Your orderid is confirmed and order id is :- '.$primary_sale_summary[0]->order_id;
            $data = [
                        'msg' => $msg,
                        'body' => $msg,
                        'title' => $data_notifi->name,
                        'flag' => '1',
                        'flag_means' => trim($data_notifi->name),
                        'sound' => 'mySound'/*Default sound*/

                ];
            
            $notification = self::sendNotification($fcm_token, $data);
            $notification_return_details = json_decode($notification);
            // dd($notification_return_details->success);
            if($notification_return_details->success == 1)
            {
                $insert_data = DB::table('dms_notification_details')
                        ->insert([
                            'user_id'=>$value->user_id,
                            'dealer_id'=>0,
                            'title'=>$data_notifi->name,
                            'msg' => $msg,
                            'body' => $msg,
                            'flag' => '1',
                            'flag_means' => trim($data_notifi->name),
                            'order_id'=> $order_id,
                            'company_id'=>$company_id,
                            'notification_status' => 1, 
                            'created_at'=>date('Y-m-d H:i:s'),
                        ]);
            }
            
        }

        
        $token_data = DB::table('dealer_person_login')->where('dealer_id',$dealer_id)->first();
        $fcm_token = !empty($token_data->dms_token)?$token_data->dms_token:'0';
        $data = [
                    'msg' => $msg,
                    'body' => $msg,
                    'title' => $data_notifi->name,
                    'flag' => '1',
                    'flag_means' => trim($data_notifi->name),
                    'sound' => 'mySound'/*Default sound*/

                ];
        
        $notification = self::sendNotification($fcm_token, $data);
        $notification_return_details = json_decode($notification);
        if($notification_return_details->success == 1)
        {
            $insert_data = DB::table('dms_notification_details')
                    ->insert([
                        'user_id'=>0,
                        'dealer_id'=>$dealer_id,
                        'title'=>$data_notifi->name,
                        'msg' => $msg,
                        'body' => $msg,
                        'flag' => '1',
                        'flag_means' => trim($data_notifi->name),
                        'company_id'=>$company_id,
                        'order_id'=> $order_id,
                        'notification_status' => 1, 
                        'created_at'=>date('Y-m-d H:i:s'),
                    ]);
        }
        // dd()
        
        return json_encode($data);
        
    }
    public function sendNotification($fcm_token, $data)
    {
        $url = "https://fcm.googleapis.com/fcm/send";
        $fields = array(
            'to' => $fcm_token,
            'notification' => $data,
            'data' => ['complaint_id' =>  'Test', 'notify_type' => 1], #1 for complaint notification
           

        );
        // dd(json_encode($fields));
        $headers = array(
            'Authorization: key=' . config('app.FCM_API_ACCESS_KEY'),
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result === FALSE) {
            die('Curl Failed: ' . curl_error($ch));
        }
        // dd($result);
        return $result;
    }
    public function submit_dms_order_dispatch(Request $request)
    {
        // dd($request);
        $order_id = $request->order_id;
        $dealer_id = $request->dealer_id;
        $fullfillment_cases = !empty($request->fullfillment_cases)?$request->fullfillment_cases:array();
        $fullfillment_pcs = !empty($request->fullfillment_pcs)?$request->fullfillment_pcs:array();
        $fullfillment_scheme_qty = !empty($request->fullfillment_scheme_qty)?$request->fullfillment_scheme_qty:array();
        $sale_date = $request->order_date;
        $product_id = $request->product_id;
        $product_name = $request->product_name;
        $product_cases = !empty($request->product_cases)?$request->product_cases:array();
        $product_qty = !empty($request->product_qty)?$request->product_qty:array();
        $product_rate_cases = !empty($request->product_rate_cases)?$request->product_rate_cases:array();
        $mgf_date = !empty($request->mgf_date)?$request->mgf_date:array();
        $batch_no = !empty($request->batch_no)?$request->batch_no:array();
        $company_id = Auth::user()->company_id;
        // dd($company_id);
        // dd($request);
        $dealer_data = DB::table('dealer')->where('id',$dealer_id)->first();
        $catalog_product_name = DB::table('catalog_product')->where('company_id',$company_id)->where('status',1)->pluck('name','id');

        // if(!empty($request->excelFile)){
        //     $csv_file = $request->excelFile;
        //     if (($getfile = fopen($csv_file, "r")) !== FALSE) {
        //             $data = fgetcsv($getfile, 1000, ",");
        //             $inum=2;
        //             $query = " ";
        //             $ch_data=array();
        //             // DB::beginTransaction();
        //             while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
        //                     $result = $data;

        //                      $result1  = str_replace(",","",$result);  
        //                      $str = implode(",", $result1);
        //                      $slice = explode(",", $str);

        //                      dd($slice);
        //                      $distributor_code = $slice[0];
        //                      $distributor_name = $slice[1];
        //                      $distributor_city = $slice[2];
        //                      $bill_no = $slice[3];
        //                      $bill_date = $slice[4];
        //                      $stockist_code = $slice[5];
        //                      $stockist_name = $slice[6];
        //                      $hq_code = $slice[7];
        //                      $hq = $slice[8];
        //                      $mr_code = $slice[9];
        //                      $mr_name = $slice[10];
        //                      $am_code = $slice[11];
        //                      $am_name = $slice[12];
        //                      $am_hq_code = $slice[13];
        //                      $am_hq_code = $slice[14];
        //                      $rsm_code = $slice[15];
        //                      $rsm_name = $slice[16];
        //                      $cust_category_code = $slice[17];
        //                      $cust_category_name = $slice[18];
        //                      $sku_code = $slice[19];
        //                      $sku_name = $slice[20];
        //                      $product_code = $slice[21];
        //                      $product_name = $slice[22];
        //                      $pack_code = $slice[23];
        //                      $pack_name = $slice[24];
        //                      $batch_no = $slice[25];
        //                      $expiry_date = $slice[26];
        //                      $div_code = $slice[27];
        //                      $division_name = $slice[28];
        //                      $quantity = $slice[29];
        //                      $free_qty = $slice[30];
        //                      $repl_qty = $slice[31];
        //                      $mrp = $slice[32];
        //                      $sale_rate = $slice[33];
        //                      $amount = $slice[34];
        //                      $discount = $slice[35];
        //                      $cgst_sgst_percent = $slice[36];
        //                      $cgst_sgst_amount = $slice[37];
        //                      $igst_percent = $slice[38];
        //                      $igst_amount = $slice[39];
        //                      $cess_atax_percent = $slice[40];
        //                      $cess_atax_amount = $slice[41];
        //                      $other_amount = $slice[42];
        //                      $net_amount = $slice[43];
        //                      $free_amount = $slice[44];
        //                      $repl_amount = $slice[45];
        //                      $tax_type_code = $slice[46];
        //                      $tax_type_name = $slice[47];
        //                      $class_code = $slice[48];
        //                      $class_name = $slice[49];
        //                      $type_code = $slice[50];
        //                      $type_name = $slice[51];
        //                      $group_code = $slice[52];
        //                      $group_name = $slice[53];
        //                      $category_code = $slice[54];
        //                      $category_name = $slice[55];
        //                      $therapy_code = $slice[56];
        //                      $therapy_name = $slice[57];
        //                      $brand_code = $slice[58];
        //                      $brand_name = $slice[59];
        //                      $dosage_from_code = $slice[60];
        //                      $dosage_from_name = $slice[61];
        //                      $dpco = $slice[62];
        //                      $schedule_h = $slice[63];
        //                      $schedule_h_one = $slice[64];
        //                      $own_trading = $slice[65];
        //                      $source_flag = $slice[66];
        //                      $check = $slice[67];
        //                      $month = $slice[68];


        //             }


        //     }


        // }



        $travell_details = [
            'order_id' => $order_id,
            'dealer_id' => $dealer_id,
            'company_id' => $company_id,
            'plant_id' => $request->plant_id,
            'travelling_id' => $request->travelling_id,
            'transport_name' => $request->transport_name,
            'gr_no' => !empty($request->gr_no)?$request->gr_no:'0',
            'freight' => !empty($request->freight)?$request->freight:'0',
            'driver_name' => $request->driver_name,
            'driver_number' => $request->driver_number,
            'vehical_number' => $request->vehical_number,
            'carrying_capacity' => !empty($request->carrying_capacity)?$request->carrying_capacity:'0',
            'payment_recieved' => $request->payment_recieved,
            'server_date_time' => date("Y-m-d H:i:s"),

        ];
       
        // $masrterArr = [
        //     'dealer_id'=> $dealer_id,
        //     'retailer_id'=>0,
        //     'user_id'=>0,
        //     'order_id'=> $order_id,
        //     'company_id'=> $company_id,
        //     // 'fullfillment_type'=>0,
        //     'fullfilment_type'=>0,
        //     'date'=>date('Y-m-d'),
        //     'time'=>date('H:i:s'),
        //     'order_date'=>$sale_date,
        //     'invoice_number'=>0,
        //     'server_date'=>date('Y-m-d H:i:s'),
        //     'created_by'=>Auth::user()->id,
        // ];
        $delete_details = DB::table('fullfillment_order_details')
                        ->where('order_id',$order_id)
                        ->where('company_id',$company_id)
                        ->delete();
        if($delete_details)
        {
            $update = DB::table('fullfillment_order')->where('order_id',$order_id)->where('company_id',$company_id)->update(['dispatch_remarks'=>$request->dispatch_remarks]);            
            foreach ($product_id as $key => $value) 
            {
                $myArr = [
                    'product_id'=>$value,
                    'product_name'=>!empty($catalog_product_name[$value])?$catalog_product_name[$value]:'NA',
                    'company_id' => $company_id,
                    'order_id'=>$order_id,
                    // 'product_fullfiment_qty'=>!empty($fullfillment_pcs[$key])?$fullfillment_pcs[$key]:'0',
                    'product_fullfiment_cases'=>!empty($fullfillment_cases[$key])?$fullfillment_cases[$key]:'0',
                    'product_fullfiment_scheme_qty'=>!empty($fullfillment_scheme_qty[$key])?$fullfillment_scheme_qty[$key]:'0',
                    'product_case_rate'=>!empty($product_rate_cases[$key])?$product_rate_cases[$key]:'0',
                    'mfg_date'=>!empty($mgf_date[$key])?$mgf_date[$key]:NULL,
                    'batch_no'=>!empty($batch_no[$key])?$batch_no[$key]:'0',
                    // 'product_qty_cases'=>!empty($product_cases[$key])?$product_cases[$key]:'0',
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                $insert_details = DB::table('fullfillment_order_details')->insert($myArr);

                // $check = DB::table('stock')
                //     ->where('dealer_id',$dealer_id)
                //     ->where('product_id',$value)
                //     // ->where('company_id',Auth::user()->company_id)
                //     ->first();
                //     // dd($check);
                // $product_details = DB::table('product_rate_list')->where('state_id',$dealer_data->state_id)->where('company_id',$company_id)->first();
                // if(COUNT($check)>0)
                // {
                //     $update_stock = DB::table('stock')
                //                 ->where('dealer_id',$dealer_id)
                //                 // ->where('company_id',$company_id)
                //                 ->where('product_id',$value)
                //                 ->update(['qty'=>!empty($fullfillment_cases[$key])?$check->qty+$fullfillment_cases[$key]:$check->qty]);
                // }
                // else
                // {
                //     $insert_stock = DB::table('stock')->insert([
                //                     'qty'=>!empty($fullfillment_cases[$key])?$fullfillment_cases[$key]:'0',
                //                     'product_id'=> $value,
                //                     'dealer_id'=> $dealer_id,
                //                     'mrp' => $product_details->mrp,
                //                     'dealer_rate' => $product_details->dealer_rate,
                //                     'person_id' => $dealer_id,
                //                     'csa_id'=> $dealer_data->csa_id,
                //                     'date'=>date('Y-m-d H:i:s'),
                //                     // 'update_date_time '=>date('Y-m-d H:i:s'),
                //                     'company_id'=>$company_id,
                //                 ]);

                // }

                // $check2 = DB::table('dealer_balance_stock')
                //     ->where('product_id',$value)
                //     // ->where('company_id',$company_id)
                //     ->where('dealer_id',$dealer_id)
                //     ->first();
                // if(COUNT($check2)>0)
                // {
                //     $update_stock2= DB::table('dealer_balance_stock')
                //                     ->where('dealer_id',$dealer_id)
                //                     ->where('product_id',$value)
                //                     // ->where('company_id',$company_id)
                //                     ->update([
                //                         'stock_qty'=>!empty($fullfillment_pcs[$key])?$check2->stock_qty+$fullfillment_pcs[$key]:$check2->stock_qty,
                //                         'stock_case'=>!empty($fullfillment_cases[$key])?$check2->stock_case+$fullfillment_cases[$key]:$check2->stock_case,
                //                         'server_date_time'=>date('Y-m-d H:i:s'),
                //                     ]);
                // }
                // else
                // {
                    
                //     $insert_stock2 = DB::table('dealer_balance_stock')->insert([
                //                     'order_id'=>$order_id,
                //                     'stock_qty'=>!empty($fullfillment_pcs[$key])?$fullfillment_pcs[$key]:'0',
                //                     'stock_case'=>!empty($fullfillment_cases[$key])?$fullfillment_cases[$key]:'0',
                //                     'product_id'=> $value,
                //                     'dealer_id'=> $dealer_id,
                //                     'mrp' => $product_details->mrp,
                //                     'pcs_mrp' => $product_details->mrp_pcs,
                //                     'submit_date_time'=>date('Y-m-d H:i:s'),
                //                     'server_date_time'=>date('Y-m-d H:i:s'),
                //                     'sstatus'=>1,
                //                     'company_id'=>$company_id,
                //                 ]);

                // }


            }
            // $fcm_token_query = DB::table('dealer_person_login')->where('dealer_id',$dealer_id)->first();

            // $msg = array(
            //     'body' => $order_id,
            //     'title'=> 'Order Dispatch',
            //     'icon' => 'https://demo.msell.in/public/sku_images/20200416125417.jpg',
            //     'sound' => 'mySound'/*Default sound*/
            // );
            // $sent_status = self::CivicSendFcmNotification($fcm_token_query->fcm_token, $msg);
            $travell_details = DB::table('dms_transport_details')->insert($travell_details);
            $update_query = DB::table('purchase_order')->where('order_id',$order_id)->update(['dispatch_date'=>date('Y-m-d H:i:s'),'dms_order_reason_id'=>'3']);
            $insert_data_log = DB::table('dms_order_reason_log')->insert(['order_id'=>$order_id,'company_id'=>$company_id,'dms_reason_id'=>'3','date'=>date("Y-m-d"),'time'=>date('H:i:s')]);

            if($travell_details && $update_query && $insert_data_log)
            {

                DB::commit();
                $user_data  = DB::table('dealer_location_rate_list')
                        // ->join('dealer_person_login','dealer_person_login.dealer_id','=','dealer_location_rate_list.dealer_id')
                        ->join('person','person.id','=','dealer_location_rate_list.user_id')
                        ->join('users','users.id','=','dealer_location_rate_list.user_id')
                        ->select(DB::raw("concat_ws(' ',first_name,middle_name,last_name) as user_name"),'person.dms_token','person.id as user_id')
                        ->where('is_admin','!=',1)
                        ->where('dealer_location_rate_list.company_id','=',$company_id)
                        ->where('dealer_location_rate_list.dealer_id',$dealer_id)
                        ->get();
        
                $data_notifi = DB::table('_dms_reason')->where('id',3)->first();
                $msg = !empty($data_notifi->noti_msg)?$data_notifi->noti_msg.$order_id:''.$order_id;

                foreach ($user_data as $key => $value) 
                {
                    $fcm_token = !empty($value->dms_token)?$value->dms_token:'0';
                    // $msg = 'Your orderid is confirmed and order id is :- '.$primary_sale_summary[0]->order_id;
                    $data = [
                                'msg' => $msg,
                                'body' => $msg,
                                'title' => $data_notifi->name,
                                'flag' => '1',
                                'flag_means' => trim($data_notifi->name),
                                'sound' => 'mySound'/*Default sound*/

                        ];
                    
                    $notification = self::sendNotification($fcm_token, $data);
                    $notification_return_details = json_decode($notification);
                    // dd($notification_return_details->success);
                    if($notification_return_details->success == 1)
                    {
                        $insert_data = DB::table('dms_notification_details')
                                ->insert([
                                    'user_id'=>$value->user_id,
                                    'dealer_id'=>0,
                                    'title'=>$data_notifi->name,
                                    'msg' => $msg,
                                    'body' => $msg,
                                    'flag' => '1',
                                    'flag_means' => trim($data_notifi->name),
                                    'order_id'=> $order_id,
                                    'company_id'=>$company_id,
                                    'notification_status' => 1, 
                                    'created_at'=>date('Y-m-d H:i:s'),
                                ]);
                    }
                    
                }

                
                $token_data = DB::table('dealer_person_login')->where('dealer_id',$dealer_id)->first();
                $fcm_token = !empty($token_data->dms_token)?$token_data->dms_token:'0';
                $data = [
                            'msg' => $msg,
                            'body' => $msg,
                            'title' => $data_notifi->name,
                            'flag' => '1',
                            'flag_means' => trim($data_notifi->name),
                            'sound' => 'mySound'/*Default sound*/

                        ];
                
                $notification = self::sendNotification($fcm_token, $data);
                $notification_return_details = json_decode($notification);
                if($notification_return_details->success == 1)
                {
                    $insert_data = DB::table('dms_notification_details')
                            ->insert([
                                'user_id'=>0,
                                'dealer_id'=>$dealer_id,
                                'title'=>$data_notifi->name,
                                'msg' => $msg,
                                'body' => $msg,
                                'flag' => '1',
                                'flag_means' => trim($data_notifi->name),
                                'company_id'=>$company_id,
                                'order_id'=> $order_id,
                                'notification_status' => 1, 
                                'created_at'=>date('Y-m-d H:i:s'),
                            ]);
                }
                $data['code'] = 200;
                $data['result'] = '';

            }
            else
            {
                DB::rollback();
                $data['code'] = 401;
                $data['result'] = '';
            }

        }
        else
        {
            DB::rollback();
            $data['code'] = 401;
            $data['result'] = '';
        }
        
        return json_encode($data);


       
    }
    public function submit_dms_reject_order(Request $request)
    {
        $order_id = $request->dms_reject_order;
        // dd($order_id);  
        // $dealer_id = $request->dealer_id;
        $reason_order = $request->reason_order;
        $company_id = Auth::user()->company_id;

        $update = DB::table('purchase_order')
                ->where('order_id',$order_id)
                // ->where('dealer_id',$dealer_id)
                ->update(['dms_order_reason_id'=>7,'cancel_order_reason_id'=>$reason_order]);

        if($update)
        {
            $insert_q = DB::table('dms_order_reason_log')->insert([
                            'order_id'=> $order_id,
                            'company_id'=>$company_id,
                            'date'=>date('Y-m-d'),
                            'time'=>date('H:i:s'),
                            'dms_reason_id'=>7,
                            'server_date_time'=>date('Y-m-d H:i:s'),
                        ]);
        
                DB::commit();
                $dealer_data = DB::table('purchase_order')->where('order_id',$order_id)->first();
                $user_data  = DB::table('dealer_location_rate_list')
                        // ->join('dealer_person_login','dealer_person_login.dealer_id','=','dealer_location_rate_list.dealer_id')
                        ->join('person','person.id','=','dealer_location_rate_list.user_id')
                        ->join('users','users.id','=','dealer_location_rate_list.user_id')
                        ->select(DB::raw("concat_ws(' ',first_name,middle_name,last_name) as user_name"),'person.dms_token','person.id as user_id')
                        ->where('is_admin','!=',1)
                        ->where('dealer_location_rate_list.company_id','=',$company_id)
                        ->where('dealer_location_rate_list.dealer_id',$dealer_data->dealer_id)
                        ->get();
        
                $data_notifi = DB::table('_dms_reason')->where('id',7)->first();
                $msg = !empty($data_notifi->noti_msg)?$data_notifi->noti_msg.$order_id:''.$order_id;

                foreach ($user_data as $key => $value) 
                {
                    $fcm_token = !empty($value->dms_token)?$value->dms_token:'0';
                    // $msg = 'Your orderid is confirmed and order id is :- '.$primary_sale_summary[0]->order_id;
                    $data = [
                                'msg' => $msg,
                                'body' => $msg,
                                'title' => $data_notifi->name,
                                'flag' => '1',
                                'flag_means' => trim($data_notifi->name),
                                'sound' => 'mySound'/*Default sound*/

                        ];
                    
                    $notification = self::sendNotification($fcm_token, $data);
                    $notification_return_details = json_decode($notification);
                    // dd($notification_return_details->success);
                    if($notification_return_details->success == 1)
                    {
                        $insert_data = DB::table('dms_notification_details')
                                ->insert([
                                    'user_id'=>$value->user_id,
                                    'dealer_id'=>0,
                                    'title'=>$data_notifi->name,
                                    'msg' => $msg,
                                    'body' => $msg,
                                    'flag' => '1',
                                    'flag_means' => trim($data_notifi->name),
                                    'company_id'=>$company_id,
                                    'order_id'=> $order_id,
                                    'notification_status' => 1, 
                                    'created_at'=>date('Y-m-d H:i:s'),
                                ]);
                    }
                    
                }

                
                $token_data = DB::table('dealer_person_login')->where('dealer_id',$dealer_data->dealer_id)->first();
                $fcm_token = !empty($token_data->dms_token)?$token_data->dms_token:'0';
                $data = [
                            'msg' => $msg,
                            'body' => $msg,
                            'title' => $data_notifi->name,
                            'flag' => '1',
                            'flag_means' => trim($data_notifi->name),
                            'sound' => 'mySound'/*Default sound*/

                        ];
                
                $notification = self::sendNotification($fcm_token, $data);
                $notification_return_details = json_decode($notification);
                if($notification_return_details->success == 1)
                {
                    $insert_data = DB::table('dms_notification_details')
                            ->insert([
                                'user_id'=>0,
                                'dealer_id'=>$dealer_data->dealer_id,
                                'title'=>$data_notifi->name,
                                'msg' => $msg,
                                'body' => $msg,
                                'flag' => '1',
                                'flag_means' => trim($data_notifi->name),
                                'company_id'=>$company_id,
                                'order_id'=> $order_id,
                                'notification_status' => 1, 
                                'created_at'=>date('Y-m-d H:i:s'),
                            ]);
                }
                $data['code'] = 200;
                $data['result'] = '';
        }
        else
        {
            DB::rollback();
            $data['code'] = 401;
            $data['result'] = '';
        }
        return json_encode($data);

    }
    public function dms_order_confirm_submit(Request $request)
    {
        // $order_id = $request->order_id;
        $remarks = $request->remarks;
        $company_id = Auth::user()->company_id;

        // dd($request);
        

        $order_id = $request->order_id;
        $dealer_id = $request->dealer_id;
        $fullfillment_cases = !empty($request->fullfillment_cases)?$request->fullfillment_cases:array();
        $fullfillment_pcs = !empty($request->fullfillment_pcs)?$request->fullfillment_pcs:array();
        $fullfillment_scheme_qty = !empty($request->fullfillment_scheme_qty)?$request->fullfillment_scheme_qty:array();
        $sale_date = $request->order_date;
        $product_id = $request->product_id;
        $product_name = $request->product_name;
        $product_cases = !empty($request->product_cases)?$request->product_cases:array();
        $product_rate_cases = !empty($request->product_rate_cases)?$request->product_rate_cases:array();
        $product_qty = !empty($request->product_qty)?$request->product_qty:array();
        $company_id = Auth::user()->company_id;
        
        $masrterArr = [
            'dealer_id'=> $dealer_id,
            'retailer_id'=>0,
            'user_id'=>0,
            'order_id'=> $order_id,
            'company_id'=> $company_id,
            // 'fullfillment_type'=>0,
            'fullfilment_type'=>0,
            'date'=>date('Y-m-d'),
            'time'=>date('H:i:s'),
            'order_date'=>$sale_date,
            'invoice_number'=>0,
            'server_date'=>date('Y-m-d H:i:s'),
            'created_by'=>Auth::user()->id,
        ];
        $insert_fullfillment = DB::table('fullfillment_order')->insert($masrterArr);
        if(!empty($product_id))
        {

            foreach ($product_id as $key => $value) 
            {
                $myArr = [
                    'product_id'=>$value,
                    'product_name'=>!empty($catalog_product_name[$value])?$catalog_product_name[$value]:'NA',
                    'company_id' => $company_id,
                    'order_id'=>$order_id,
                    'product_fullfiment_qty'=>!empty($fullfillment_pcs[$key])?$fullfillment_pcs[$key]:'0',
                    'product_fullfiment_cases'=>!empty($fullfillment_cases[$key])?$fullfillment_cases[$key]:'0',
                    'product_fullfiment_scheme_qty'=>!empty($fullfillment_scheme_qty[$key])?$fullfillment_scheme_qty[$key]:'0',
                    'product_case_rate'=>!empty($product_rate_cases[$key])?$product_rate_cases[$key]:'0',
                    'product_rate'=>!empty($product_rate_qty[$key])?$product_rate_qty[$key]:'0',
                    'product_qty'=>!empty($product_qty[$key])?$product_qty[$key]:'0',
                    'product_qty_cases'=>!empty($product_cases[$key])?$product_cases[$key]:'0',
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                $insert_details = DB::table('fullfillment_order_details')->insert($myArr);
            }
        }


        $update = DB::table('purchase_order')
        ->where('order_id',$order_id)
        // ->where('dealer_id',$dealer_id)
        ->update(['dms_order_reason_id'=>2,'dms_order_confirm_remaks'=>$remarks]);

        if($update && $insert_fullfillment && $insert_details)
        {
            $insert_q = DB::table('dms_order_reason_log')->insert([
                            'order_id'=> $order_id,
                            'company_id'=>$company_id,
                            'date'=>date('Y-m-d'),
                            'time'=>date('H:i:s'),
                            'dms_reason_id'=>2,
                            'server_date_time'=>date('Y-m-d H:i:s'),
                        ]);
        
            DB::commit();
        }

        $data['code'] = 200;
            // $data['result'] = '';
        $user_data  = DB::table('dealer_location_rate_list')
                        // ->join('dealer_person_login','dealer_person_login.dealer_id','=','dealer_location_rate_list.dealer_id')
                        ->join('person','person.id','=','dealer_location_rate_list.user_id')
                        ->join('users','users.id','=','dealer_location_rate_list.user_id')
                        ->select(DB::raw("concat_ws(' ',first_name,middle_name,last_name) as user_name"),'person.dms_token','person.id as user_id')
                        ->where('is_admin','!=',1)
                        ->where('dealer_location_rate_list.company_id','=',$company_id)
                        ->where('dealer_location_rate_list.dealer_id',$dealer_id)
                        ->get();
        
        $data_notifi = DB::table('_dms_reason')->where('id',2)->first();
        $msg = !empty($data_notifi->noti_msg)?$data_notifi->noti_msg.$order_id:''.$order_id;

        foreach ($user_data as $key => $value) 
        {
            $fcm_token = !empty($value->dms_token)?$value->dms_token:'0';
            // $msg = 'Your orderid is confirmed and order id is :- '.$primary_sale_summary[0]->order_id;
            $data = [
                        'msg' => $msg,
                        'body' => $msg,
                        'title' => $data_notifi->name,
                        'flag' => '1',
                        'flag_means' => trim($data_notifi->name),
                        'sound' => 'mySound'/*Default sound*/

                ];
            
            $notification = self::sendNotification($fcm_token, $data);
            $notification_return_details = json_decode($notification);
            // dd($notification_return_details->success);
            if($notification_return_details->success == 1)
            {
                $insert_data = DB::table('dms_notification_details')
                        ->insert([
                            'user_id'=>$value->user_id,
                            'dealer_id'=>0,
                            'title'=>$data_notifi->name,
                            'msg' => $msg,
                            'body' => $msg,
                            'flag' => '1',
                            'flag_means' => trim($data_notifi->name),
                            'company_id'=>$company_id,
                            'order_id'=> $order_id,
                            'notification_status' => 1, 
                            'created_at'=>date('Y-m-d H:i:s'),
                        ]);
            }
            
        }


        $token_data = DB::table('dealer_person_login')->where('dealer_id',$dealer_id)->first();
        $fcm_token = !empty($token_data->dms_token)?$token_data->dms_token:'0';
        $data = [
                    'msg' => $msg,
                    'body' => $msg,
                    'title' => $data_notifi->name,
                    'flag' => '1',
                    'flag_means' => trim($data_notifi->name),
                    'sound' => 'mySound'/*Default sound*/

                ];

        $notification = self::sendNotification($fcm_token, $data);
        $notification_return_details = json_decode($notification);
        if($notification_return_details->success == 1)
        {
            $insert_data = DB::table('dms_notification_details')
                    ->insert([
                        'user_id'=>0,
                        'dealer_id'=>$dealer_id,
                        'title'=>$data_notifi->name,
                        'msg' => $msg,
                        'body' => $msg,
                        'flag' => '1',
                        'flag_means' => trim($data_notifi->name),
                        'company_id'=>$company_id,
                        'order_id'=> $order_id,
                        'notification_status' => 1, 
                        'created_at'=>date('Y-m-d H:i:s'),
                    ]);
        }
        return json_encode($data);
    }

    public function submit_dms_invoice_genrate(Request $request)
    {
        // dd($request);
        $order_id = $request->dms_invoice_action_id;
        $company_id = Auth::user()->company_id;
        $mailId = explode(',',$request->email_sent);
        // $mailId = $request->email_sent;

        // dd($request->file());
        // $my_pdf_path_for_example = 'my/really/cool/path/' . str_random(25) . '.pdf';
        // PDF::loadHTML('<h1>Test</h1> ')->save(public_path('pdf/'.$request->file_name) );
        // dd($request);
        // if ($request->hasFile('file_name')) {
            // dd($request);

        // $address_invoice
        if($company_id == 52)
        {
            $address_invoice = 'Patanjali Peya Pvt. Ltd.<br>
                LG-01, Aggarwal Cyber Plaza 1, Plot no. C 4,5 & 6,<br>
                District Center, Netaji Subhash Place, Wazirpur, Delhi, 110034';
        }
        elseif($company_id == 55)
        {
            $address_invoice = 'Piranha Communication <br>
                2204, 22ND FLOOR G-SQUARE BUSINESS PARK,<br>
                PLOT NO. 25 & 26 , Maharashtra, Code 27';
        }
        else
        {
            $address_invoice = 'mSELL';
        }
        $name_invoice = 'NA';
        if($request->file('imageFile')->isValid()) {
            try {
                $file = $request->file('imageFile');
                $name_invoice = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                # save to DB
                // $personImage = PersonLogin::where('person_id',$person->id)->update(['person_image' => 'users-profile/'.$name]);

                $request->file('imageFile')->move("pdf", $name_invoice);
            } catch (Illuminate\Filesystem\FileNotFoundException $e) {

            }
        }
        
        $update = DB::table('purchase_order')
                ->where('order_id',$order_id)
                // ->where('dealer_id',$dealer_id)
                ->update(['dms_order_reason_id'=>4,'pdf_name2'=>$name_invoice,'pdf_name'=>$order_id.'.pdf','invoice_no_p'=> $request->invoice_no,'invoice_date'=>$request->invoice_date]);

      
        $insert_q = DB::table('dms_order_reason_log')->insert([
                        'order_id'=> $order_id,
                        'company_id'=>$company_id,
                        'date'=>date('Y-m-d'),
                        'time'=>date('H:i:s'),
                        'dms_reason_id'=>4,
                        'server_date_time'=>date('Y-m-d H:i:s'),
                    ]);
    
           
       

        $data_query = DB::table('purchase_order')
            ->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
            ->join('dealer','dealer.id','=','purchase_order.dealer_id')
            ->join('fullfillment_order','fullfillment_order.order_id','=','purchase_order.order_id')
            // ->join('_dms_reason','_dms_reason.id','=','purchase_order.dms_order_reason_id')
            ->select('dispatch_remarks','purchase_order.dms_order_confirm_remaks as remarks','dealer.id as dealer_id','dealer.name as dealer_name','purchase_order.order_id as order_id','dealer.email as dealer_email','dealer.address as dealer_address','dealer.other_numbers as dealer_mobile','sale_date','dealer.tin_no as dealer_gst_no')
            ->where('purchase_order.order_id',$order_id)
            ->where('purchase_order.company_id',$company_id)
            ->first();

        $invoice_data = DB::table('fullfillment_order')
            ->join('fullfillment_order_details','fullfillment_order_details.order_id','=','fullfillment_order.order_id')
            ->join('catalog_product','catalog_product.id','=','fullfillment_order_details.product_id')
            ->join('dealer','dealer.id','=','fullfillment_order.dealer_id')
            // ->join('_dms_reason','_dms_reason.id','=','purchase_order.dms_order_reason_id')
            ->select('batch_no','mfg_date','weight','catalog_product.id as product_id','catalog_product.name as product_name',DB::raw("SUM(fullfillment_order_details.product_fullfiment_cases) as cases"),DB::raw("SUM(fullfillment_order_details.product_fullfiment_scheme_qty) as pcs"),DB::raw("SUM(fullfillment_order_details.product_fullfiment_scheme_qty) as scheme_qty"),'product_case_rate as cases_rate')
            ->where('fullfillment_order.order_id',$order_id)
            ->where('fullfillment_order.company_id',$company_id)
            ->groupBy('fullfillment_order_details.id')
            ->get();

        $transport_details = DB::table('dms_transport_details')
            ->join('_dms_plant_master','_dms_plant_master.id','=','dms_transport_details.plant_id')
            ->join('_vehicle_details','_vehicle_details.id','=','dms_transport_details.travelling_id')
            ->select('dms_transport_details.*','_vehicle_details.name as tavel_mode_name','_dms_plant_master.name as plant_name')
            ->where('dms_transport_details.order_id',$order_id)
            ->where('dms_transport_details.company_id',$company_id)
            ->first();

        $customPaper = array(0, 0, 1240, 1748);
        $pdf_name = $order_id.'.pdf';
        // dd($pdf_name);
        $pdf = PDF::loadView('reports/pdf', ['transport_details' => $transport_details,'invoice_data' => $invoice_data,'data_query'=>$data_query,'address_invoice'=>$address_invoice]);
        $pdf->setPaper($customPaper);

        $pdf->save(public_path('pdf/'.$pdf_name));
            // return $pdf->download('some-filename.pdf');
        
        $pdf_path = public_path() . '/pdf/' .$name_invoice;
        // $mails=json_decode($mailId);

        
        $mailMsg="Please find the attached Invoice Form $order_id";
      
        $send=Mail::raw($mailMsg, function ($message) use($mailId,$pdf_path,$mailMsg,$order_id)
        {
          foreach ($mailId as $mkey => $mail) 
          {
            $message->to($mail,$mail);
          }  
         
          $message->subject("Invoice No: $order_id || Please do not reply")
            ->attach($pdf_path);
        });

        if($insert_q && $update)
        {
            DB::commit();

            $user_data  = DB::table('dealer_location_rate_list')
                        // ->join('dealer_person_login','dealer_person_login.dealer_id','=','dealer_location_rate_list.dealer_id')
                        ->join('person','person.id','=','dealer_location_rate_list.user_id')
                        ->join('users','users.id','=','dealer_location_rate_list.user_id')
                        ->select(DB::raw("concat_ws(' ',first_name,middle_name,last_name) as user_name"),'person.dms_token','person.id as user_id')
                        ->where('is_admin','!=',1)
                        ->where('dealer_location_rate_list.company_id','=',$company_id)
                        ->where('dealer_location_rate_list.dealer_id',$data_query->dealer_id)
                        ->get();
        
            $data_notifi = DB::table('_dms_reason')->where('id',4)->first();
            $msg = !empty($data_notifi->noti_msg)?$data_notifi->noti_msg.$order_id:''.$order_id;

            foreach ($user_data as $key => $value) 
            {
                $fcm_token = !empty($value->dms_token)?$value->dms_token:'0';
                // $msg = 'Your orderid is confirmed and order id is :- '.$primary_sale_summary[0]->order_id;
                $data = [
                            'msg' => $msg,
                            'body' => $msg,
                            'title' => $data_notifi->name,
                            'flag' => '1',
                            'flag_means' => trim($data_notifi->name),
                            'sound' => 'mySound'/*Default sound*/

                    ];
                
                $notification = self::sendNotification($fcm_token, $data);
                $notification_return_details = json_decode($notification);
                // dd($notification_return_details->success);
                if($notification_return_details->success == 1)
                {
                    $insert_data = DB::table('dms_notification_details')
                            ->insert([
                                'user_id'=>$value->user_id,
                                'dealer_id'=>0,
                                'title'=>$data_notifi->name,
                                'msg' => $msg,
                                'body' => $msg,
                                'flag' => '1',
                                'flag_means' => trim($data_notifi->name),
                                'company_id'=>$company_id,
                                'order_id'=> $order_id,
                                'notification_status' => 1, 
                                'created_at'=>date('Y-m-d H:i:s'),
                            ]);
                }
                
            }

            
            $token_data = DB::table('dealer_person_login')->where('dealer_id',$data_query->dealer_id)->first();
            $fcm_token = !empty($token_data->dms_token)?$token_data->dms_token:'0';
            $data = [
                        'msg' => $msg,
                        'body' => $msg,
                        'title' => $data_notifi->name,
                        'flag' => '1',
                        'flag_means' => trim($data_notifi->name),
                        'sound' => 'mySound'/*Default sound*/

                    ];
            
            $notification = self::sendNotification($fcm_token, $data);
            $notification_return_details = json_decode($notification);
            if($notification_return_details->success == 1)
            {
                $insert_data = DB::table('dms_notification_details')
                        ->insert([
                            'user_id'=>0,
                            'dealer_id'=>$data_query->dealer_id,
                            'title'=>$data_notifi->name,
                            'msg' => $msg,
                            'body' => $msg,
                            'flag' => '1',
                            'flag_means' => trim($data_notifi->name),
                            'company_id'=>$company_id,
                            'order_id'=> $order_id,
                            'notification_status' => 1, 
                            'created_at'=>date('Y-m-d H:i:s'),
                        ]);
            }
            $data['code'] = 200;

        }
        //         $data['result'] = '';
        return json_encode($data);

    }
    public function dms_rate_bhelaf_product_id(Request $request)
    {
        // dd($request);
        $dealer_id = $request->dealer_id;
        $product_id = $request->product_id;

        $company_id = Auth::user()->company_id;
        $date = date('Y-m-d');
        $state_data = DB::table('dealer')->where('id',$dealer_id)->first();
        $state_id = $state_data->state_id;

        $product_details = DB::table('product_rate_list_template')
                        ->join('dealer','dealer.template_id','=','product_rate_list_template.template_type')
                        ->join('catalog_product','catalog_product.id','=','product_rate_list_template.product_id')
                        ->select('dealer_rate','weight')
                        ->where('dealer.id',$dealer_id)
                        ->where('product_rate_list_template.product_id',$product_id)
                        ->first();

        
        // $scheme_details = DB::table('scheme_plan_details')
        //                 ->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
        //                 ->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id')
        //                 ->where('scheme_plan_details.company_id',$company_id)
        //                 ->where('sale_unit',2)
        //                 ->where('product_id',$product_id)
        //                 ->where('incentive_type',3)
        //                 ->first();
        $scheme_details = DB::table('scheme_plan_details')
                        ->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
                        ->join('scheme_assign_dealer','scheme_assign_dealer.plan_id','=','scheme_plan.id')
                        ->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id')
                        ->where('scheme_plan_details.company_id',$company_id)
                        ->where('scheme_plan_details.product_id',$product_id)
                        ->where('scheme_assign_dealer.dealer_id',$dealer_id)
                        ->whereRaw("date_format(plan_assigned_from_date,'%Y-%m-%d')<='$date' AND date_format(plan_assigned_to_date,'%Y-%m-%d')>='$date'")
                        ->where('sale_unit',2)
                        ->where('incentive_type',3)
                        ->orderBy('scheme_assign_dealer.id','DESC')
                        ->first();
        if(!empty($product_details))
        {
            $data['code'] = 200;
            $data['product_details'] = $product_details;
            $data['scheme_details'] = $scheme_details;

        }
        else
        {
            $product_details = DB::table('product_rate_list')
                        ->join('catalog_product','catalog_product.id','=','product_rate_list.product_id')
                        ->select('dealer_rate','weight')
                        ->where('state_id',$state_id)
                        ->where('product_id',$product_id)
                        ->first();

            // $scheme_details = DB::table('scheme_plan_details')
            //             ->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
            //             ->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id')
            //             ->where('scheme_plan_details.company_id',$company_id)
            //             ->where('sale_unit',2)
            //             ->where('product_id',$product_id)
            //             ->where('incentive_type',3)
            //             ->first();

            $scheme_details = DB::table('scheme_plan_details')
                        ->join('scheme_plan','scheme_plan.id','=','scheme_plan_details.scheme_id')
                        ->join('scheme_assign_dealer','scheme_assign_dealer.plan_id','=','scheme_plan.id')
                        ->select('sale_value_range_last as range_second','sale_value_range_first as range_first','value_amount_percentage as free_qty','scheme_id as plan_id','product_id')
                        ->where('scheme_plan_details.company_id',$company_id)
                        ->where('scheme_plan_details.product_id',$product_id)
                        ->where('scheme_assign_dealer.dealer_id',$dealer_id)
                        ->whereRaw("date_format(plan_assigned_from_date,'%Y-%m-%d')<='$date' AND date_format(plan_assigned_to_date,'%Y-%m-%d')>='$date'")
                        ->where('sale_unit',2)
                        ->where('incentive_type',3)
                        ->orderBy('scheme_assign_dealer.id','DESC')
                        ->first();

            $data['code'] = 200;
            $data['product_details'] = $product_details;
            $data['scheme_details'] = $scheme_details;


        }

        return json_encode($data);


    }

    public function dms_payement_recieved_details(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $distributor = DB::table('dealer')->where('company_id',$company_id)->where('dealer_status',1)->pluck('name','id');
        $location3 = DB::table('location_3')->where('company_id',$company_id)->where('status',1)->pluck('name','id');

        return view($this->current_menu.'.paymentIndex', [
           'distributor'=>$distributor,
           'location3'=>$location3,
        ]);
    }
    public function dms_payement_recieved_details_report(Request $request)
    {
        $dealer = $request->dealer;
        $location3 = $request->location_3;
        $company_id = Auth::user()->company_id;
        $explodeDate = explode(" -", $request->date_range_picker);
        $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
        $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));

        $data = DB::table('purchase_order')
                ->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
                ->join('fullfillment_order','fullfillment_order.order_id','=','purchase_order.order_id')
                ->join('fullfillment_order_details','fullfillment_order_details.order_id','=','fullfillment_order.order_id')
                ->join('payment_collect_dealer','payment_collect_dealer.order_id','=','purchase_order.order_id')
                ->join('_payment_modes','_payment_modes.id','=','payment_collect_dealer.payment_mode')
                ->join('dealer','dealer.id','=','purchase_order.dealer_id')
                ->select('claim_adjustment_amount','claim_adjustment','purchase_order.order_id','purchase_order.dealer_id','_payment_modes.mode as payment_mode','dealer.name as dealer_name','dealer.dealer_code as dealer_code','payment_collect_dealer.amount_by_sfa as amount','invoice_no_p as invoice_no','invoice_date',DB::raw("SUM(product_fullfiment_cases*product_case_rate) as order_value"))
                ->whereRaw("date_format(dispatch_date,'%Y-%m-%d')>='$from_date' AND date_format(dispatch_date,'%Y-%m-%d')<='$to_date'")
                ->where('purchase_order.company_id',$company_id)
                ->where('claim_adjustment_status',2)
                ->where('purchase_order.dealer_id',$dealer)
                ->where('dms_order_reason_id',4)
                ->groupBy('fullfillment_order_details.order_id');

                if(!empty($location3))
                {
                    $data->where('dealer.state_id',$location3);
                }

        $fetch_data = $data->get();
        $order_value = DB::table('fullfillment_order')
                    ->join('fullfillment_order_details','fullfillment_order_details.order_id','=','fullfillment_order.order_id')
                    ->where('fullfillment_order.company_id',$company_id)
                    ->groupBy('fullfillment_order.order_id')
                    ->pluck(DB::raw("SUM(product_fullfiment_cases*product_case_rate) as order_value"),'fullfillment_order.order_id'); 
        // $payment_collector = DB::table('payment_collect_dealer')
        // dd($fetch_data)
        $mode = DB::table('_payment_modes')->where('company_id',$company_id)->where('status',1)->pluck('mode','id');
        return view($this->current_menu.'.paymentAjax', [
           'records'=>$fetch_data,
           'order_value'=> $order_value,
           'mode'=> $mode,

        ]);


    }
    public function submit_payment_adjusment(Request $request)
    {
        // dd($request);
        $order_id = $request->order_id;
        $dealer_id = $request->dealer_id;
        $claim_adjustment = $request->claim_adjustment;
        $claim_adjustment_amount = $request->claim_adjustment_amount;
        $claim_adjustment_mode = $request->claim_adjustment_mode;
        $claim_adjustment_date = $request->claim_adjustment_date;
        $update_query = '';
        foreach ($order_id as $key => $value) 
        {
            if($claim_adjustment[$key] != 0)
            {
                $update_query = DB::table('payment_collect_dealer')
                            ->where('dealer_id',$dealer_id[$key])
                            ->where('order_id',$value)
                            ->update([
                                'claim_adjustment'=>!empty($claim_adjustment[$key])?$claim_adjustment[$key]:'0',
                                'claim_adjustment_amount'=>!empty($claim_adjustment_amount[$key])?$claim_adjustment_amount[$key]:'0',
                                'claim_adjustment_mode'=>!empty($claim_adjustment_mode[$key])?$claim_adjustment_mode[$key]:'0',
                                'claim_adjustment_date'=>!empty($claim_adjustment_date[$key])?$claim_adjustment_date[$key]:'0',
                                'claim_adjustment_status'=>1,
                            ]);
                $order_id_array[] = $value;
                
            }
         
        }
        if($update_query)
        {
            $data['code'] = 200;
            $data['order_id'] = $order_id_array;
            $data['message'] = 'success';
        }
        else
        {
            $data['code'] = 401;
              
            $data['message'] = 'success';
        }
        return json_encode($data);
        // return json_encode($data);

    }

    public function dms_srn_function(Request $request)
    {
        $state_id = $request->location3;
        $dealer_id = $request->dealer;
        $company_id = Auth::user()->company_id;
        $flag = 1;
        $query = [];
        $state = DB::table('location_3')->where('status',1)->where('company_id',$company_id)->pluck('name','id');
        $distributor = DB::table('dealer')->where('dealer_status',1)->where('company_id',$company_id)->pluck('name','id');
        if($flag == 1)
        {
            if(!empty($request->date_range_picker))
            {
                $explodeDate = explode(" -", $request->date_range_picker);
                $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
                $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
            }
            else
            {
                $from_date = date('Y-m-d');
                $to_date = date('Y-m-d');
            }
            $query_data = DB::table('purchase_order')
                        ->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
                        ->join('fullfillment_order','fullfillment_order.order_id','=','purchase_order.order_id')
                        ->join('fullfillment_order_details','fullfillment_order_details.order_id','=','fullfillment_order.order_id')
                        ->join('dealer','dealer.id','=','purchase_order.dealer_id')
                        ->select('invoice_date','invoice_no_p as invoice_no','pdf_name2','pdf_name','dms_order_reason_id','purchase_order.order_id as order_id','purchase_order.dealer_id as dealer_id','dealer.other_numbers as mobile_number',DB::raw("SUM(product_fullfiment_cases) as cases"),DB::raw("SUM(product_fullfiment_scheme_qty) as scheme_qty"),DB::raw("SUM(product_fullfiment_cases*product_case_rate) as order_value"),'dealer.name as dealer_name','app_flag','sale_date')
                        ->where('purchase_order.company_id',$company_id)
                        ->where('dealer.company_id',$company_id)
                        ->where('dms_order_reason_id',4)
                        ->where('app_flag',2)
                        ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(sale_date,'%Y-%m-%d')<='$to_date'")
                        ->groupBy('purchase_order.order_id');
            if(!empty($dealer_id))
            {   
                $query_data->where('purchase_order.dealer_id',$dealer_id);
            }
            if(!empty($state_id))
            {   
                $query_data->where('dealer.state_id',$state_id);
            }
            $query=$query_data->orderBy('purchase_order.order_id','ASC')->get();
            $dms_status_order_query = DB::table('_dms_reason')->where('status',1)->pluck('name','id');

            $out=array();
            $proout=array();
        }   
        
        return view($this->current_menu.'.srn', [
            'records' => $query,
            'location3' => $state,
            'dms_status_order_query'=> $dms_status_order_query,
            'distributor' => $distributor,
            'current_menu'=>$this->current_menu
        ]);

    }
    public function dms_get_srn_details(Request $request)
    {
        $order_id = $request->order_id;
        $company_id = Auth::user()->company_id;
        $data_query = DB::table('purchase_order')
                ->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
                ->join('fullfillment_order','fullfillment_order.order_id','=','purchase_order.order_id')
                ->join('fullfillment_order_details','fullfillment_order_details.order_id','=','fullfillment_order.order_id')
                ->join('dealer','dealer.id','=','purchase_order.dealer_id')
                ->select('product_case_rate','dealer_code','invoice_date','invoice_no_p as invoice_no','pdf_name2','pdf_name','dms_order_reason_id','purchase_order.order_id as order_id','purchase_order.dealer_id as dealer_id','dealer.other_numbers as mobile_number',DB::raw("SUM(product_fullfiment_cases) as cases"),DB::raw("SUM(product_fullfiment_scheme_qty) as scheme_qty"),DB::raw("SUM(product_fullfiment_cases*product_case_rate) as order_value"),'dealer.name as dealer_name','app_flag','sale_date')
                ->where('purchase_order.company_id',$company_id)
                ->where('dealer.company_id',$company_id)
                ->where('fullfillment_order.order_id',$order_id)
                ->where('dms_order_reason_id',4)
                ->where('app_flag',2)
                ->groupBy('fullfillment_order.order_id')
                ->get()->toArray();

        if(COUNT($data_query)>0)
        {
            $check_sale_return = DB::table('dms_dealer_sales_return')->where('order_id',$order_id)->count();
            $check_damge = DB::table('dms_dealer_damage')->where('order_id',$order_id)->count();
            $data['code'] = 200;
            $data['result_down'] = $data_query;
            $data['dealer_id'] = $data_query[0]->dealer_id;
            $data['order_id'] = $order_id;
            $data['sale_return_status'] = $check_sale_return>0?1:'0';
            $data['damage_status'] = $check_damge>0?1:'0';
            $data['message'] = 'success';
        } 
        else 
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);
    }
    public function submit_sale_return(Request $request)
    {
        $order_id = $request->order_id;
        $dealer_id = $request->dealer_id;
        $cases_return = $request->cases_return;
        $scheme_return = $request->scheme_return;
        $remarks_return = $request->remarks_return;
        $cases_rate = $request->case_rate;
        $company_id = Auth::user()->company_id;
        foreach ($cases_return as $key => $value) 
        {
            $insert_query = DB::table('dms_dealer_sales_return')->insert([
                                    'order_id'=>$order_id,
                                    'dealer_id'=>$dealer_id,
                                    'company_id'=> $company_id,
                                    'cases'=>$cases_return[$key],
                                    'cases_rate'=>$cases_rate[$key],
                                    'scheme_qty'=>$scheme_return[$key],
                                    'remarks'=>$remarks_return[$key],
                                    'date'=>date('Y-m-d'),
                                    'time'=>date('H:i:s'),
                                    'server_date_time'=>date('Y-m-d H:i:s'),
                            ]);
        }
        if($insert_query)
        {
            $data['code'] = 200;

        }
        else
        {
            $data['code'] = 401;

        }
        return json_encode($data);

    }
    public function submit_dms_damge(Request $request)
    {
        $order_id = $request->order_id;
        $dealer_id = $request->dealer_id;
        $cases_return = $request->cases_return;
        $remarks_return = $request->remarks_return;
        $cases_rate = $request->case_rate;
        $company_id = Auth::user()->company_id;
        foreach ($cases_return as $key => $value) 
        {
            $insert_query = DB::table('dms_dealer_damage')->insert([
                                    'order_id'=>$order_id,
                                    'dealer_id'=>$dealer_id,
                                    'company_id'=> $company_id,
                                    'cases'=>$cases_return[$key],
                                    'cases_rate'=>$cases_rate[$key],
                                    'remarks'=>$remarks_return[$key],
                                    'date'=>date('Y-m-d'),
                                    'time'=>date('H:i:s'),
                                    'server_date_time'=>date('Y-m-d H:i:s'),
                            ]);
        }
        if($insert_query)
        {
            $data['code'] = 200;

        }
        else
        {
            $data['code'] = 401;

        }
        return json_encode($data);

    }
    public function dms_status_logs(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $order_id = $request->order_id;
        $data_return = DB::table('dms_order_reason_log')
                    ->join('_dms_reason','_dms_reason.id','=','dms_order_reason_log.dms_reason_id')
                    ->select('_dms_reason.name as tiltle','dms_order_reason_log.date as date','dms_order_reason_log.time as time')
                    ->where('dms_order_reason_log.order_id',$order_id)
                    ->where('dms_order_reason_log.company_id',$company_id)
                    ->orderBy('dms_order_reason_log.id','ASC')
                    ->get();

        if($data_return)
        {
            $data['code'] = 200;
            $data['data_return'] = $data_return;

        }
        else
        {
            $data['code'] = 401;
            $data['data_return'] = array();


        }
        return json_encode($data);

    }
}
