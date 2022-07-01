<?php

namespace App\Http\Controllers;

use App\retailer;
use App\retailerLocation;
use App\retailerPersonLogin;
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

class RetailerSaleOrderController extends Controller
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
        $dms_status_order_query = array();
        
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
                        ->join('retailer','retailer.id','=','purchase_order.retailer_id')
                        // ->join('_dms_reason','_dms_reason.id','=','purchase_order.dms_order_reason_id')
                        ->select('dms_order_reason_id','purchase_order.order_id as order_id','purchase_order.retailer_id as retailer_id','retailer.other_numbers as mobile_number',DB::raw("SUM(cases) as cases"),DB::raw("SUM(pcs) as pcs"),DB::raw("SUM((pr_rate*cases)+(rate*pcs)) as total_vale"),'retailer.name as retailer_name','app_flag','sale_date')
                        ->where('purchase_order.company_id',$company_id)
                        ->where('retailer.company_id',$company_id)
                        ->whereRaw("DATE_FORMAT(sale_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(sale_date,'%Y-%m-%d')<='$to_date'")
                        ->groupBy('purchase_order.order_id');
                        // ->get();

           if(!empty($retailer_id))
            {   
                $query_data->whereIn('purchase_order.retailer_id',$retailer_id);
            }
           
          
         


            $query=$query_data->orderBy('purchase_order.order_id','ASC')->get();
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
        

       
     


        return view($this->current_menu.'.retailer', [
            'records' => $query,
            'details' => $out,
            'region' => $state,
            'user' => $user_name,
            'role' => $role,
            'payment_modes'=> $payment_modes,
            'reject_reason_dms'=>$reject_reason_dms,
            'dms_status_order_query'=> $dms_status_order_query,
            'current_menu'=>$this->current_menu
        ]);

    }


    // public function submit_order_edit_form(Request $request)
    // {
    //     $product_id = $request->product_id;
    //     $rate = $request->rate;
    //     $old_qty = $request->old_qty;
    //     $new_qty = $request->qty;
    //     $total_value = $request->total;
    //     $order_id = $request->order_id;
    //     $user_id = $request->user_id;
    //     $retailer_id = $request->retailer_id;
    //     $order_date = $request->order_date;
    //     $product_name = $request->product_name;
    //     $retailer_name = $request->retailer_name;
    //     $remarks = $request->remarks;
    //     $status = $request->status;


    //     $order_id_new = array_unique($order_id);
    //     $retailer_id_new = array_unique($retailer_id);
    //     $user_id_new = array_unique($user_id);
    //     $retailer_name_new = array_unique($retailer_name);
    //     $order_date_new = array_unique($order_date);
    //     $total_sale_value_new = array_sum($total_value);

    //     foreach ($product_id as $key => $value) 
    //     {
    //         $myArr = [
    //             'order_id' => $order_id[$key],
    //             'product_id' => $value,
    //             'product_name' => $product_name[$key],
    //             'product_qty' => $old_qty[$key],
    //             'product_rate' => $rate[$key],
    //             'product_value' => $total_value[$key],
    //             'product_fullfiment_qty' => $new_qty[$key],
    //             'created_at' => date('Y-m-d H:i:s'),
    //         ];

    //         $fullfillment_insert_query = DB::table('fullfillment_order_details')->insert($myArr);
    //     }

    //     $myArrOrder = [
    //             'order_id'=> $order_id_new[0],
    //             'retailer_id'=> $retailer_id_new[0],
    //             'user_id'=> $user_id_new[0],
    //             'retailer_name'=> $retailer_name_new[0],
    //             'order_date'=> $order_date_new[0],
    //             'fullfillment_value' => $total_sale_value_new,
    //             'date'=>date('Y-m-d'),
    //             'time'=>date('H:i:s'),
    //             'remarks'=>$remarks,
    //             'invoice_number'=>date('YmdHis'),
    //             'server_date'=>date('Y-m-d H:i:s'),
    //     ];
    //     // dd($myArrOrder);
    //     $fullfillment_order_insert_query = DB::table('fullfillment_order')->insert($myArrOrder);

    //     $user_sale_order_update_query = DB::table('user_sales_order')->where('order_id',array_unique($order_id))->update(['updated_at'=>date('Y-m-d H:i:s'),'status'=>$status]);

    //     if(!empty($fullfillment_order_insert_query) && !empty($fullfillment_insert_query) && !empty($user_sale_order_update_query))
    //     {
    //         DB::commit();
    //         Session::flash('message', Lang::get('common.'.$this->current_menu).' updated successfully');
    //         Session::flash('class', 'success');

    //     }
    //     else
    //     {
    //         DB::rollback();
    //         Session::flash('message', 'Something went wrong!');
    //         Session::flash('class', 'danger');
    //     }
    //     return redirect()->intended($this->current_menu);

    // }




    // public function edit_order_details(Request $request)
    // {

    //     $order_id = $request->order_id;

    //     $check = DB::table('user_sales_order')->where('order_id',$order_id)->first();
    //     // dd($check);
    //     $remarks_status = $check->remarks;         
    //     if($check->status==1)
    //     {
    //         $query_data =DB::table('user_sales_order_view')
    //         ->leftJoin('fullfillment_order','fullfillment_order.order_id','=','user_sales_order_view.order_id')
    //         ->select('gst_no','fullfillment_value','status_approval','user_sales_order_view.retailer_id as retailer_id','user_sales_order_view.user_id as user_id','user_sales_order_view.retailer_id as retailer_id','user_name AS user_name','retailer_name',DB::raw("DATE_FORMAT(user_sales_order_view.date,'%d-%m-%Y') AS date"),'user_sales_order_view.order_id','call_status','l4_name','user_sales_order_view.retailer_name','user_sales_order_view.time','mobile','track_address','total_sale_value')
    //         ->where('user_sales_order_view.order_id',$order_id)
    //         ->orderBy('user_sales_order_view.order_id','ASC')
    //         ->get();

    //         $edit_query = DB::table('user_sales_order_details_view')
    //             ->join('fullfillment_order_details','fullfillment_order_details.order_id','=','user_sales_order_details_view.order_id')
    //             ->join('user_sales_order_view','user_sales_order_view.order_id','user_sales_order_details_view.order_id')
    //             ->where('user_sales_order_details_view.order_id', $order_id)
    //             ->select('remarks',DB::raw("CONCAT(date,' ',time) as order_date"),'itemcode','product_fullfiment_qty','product_value','retailer_name','user_name','user_id','retailer_id','quantity','rate','user_sales_order_details_view.product_name as product_name','user_sales_order_details_view.product_id as product_id','scheme_qty as weight','user_sales_order_details_view.order_id as order_id')
    //             ->groupBy('user_sales_order_details_view.product_id')
    //             ->get();
    //             // dd($edit_query);
    //     }
    //     else
    //     {
    //         $query_data =DB::table('user_sales_order_view')
    //         ->leftJoin('fullfillment_order','fullfillment_order.order_id','=','user_sales_order_view.order_id')
    //         ->select('gst_no','fullfillment_value','status_approval','user_sales_order_view.retailer_id as retailer_id','user_sales_order_view.user_id as user_id','user_sales_order_view.retailer_id as retailer_id','user_name AS user_name','retailer_name',DB::raw("DATE_FORMAT(user_sales_order_view.date,'%d-%m-%Y') AS date"),'user_sales_order_view.order_id','call_status','l4_name','user_sales_order_view.retailer_name','user_sales_order_view.time','mobile','track_address','total_sale_value')
    //         ->where('user_sales_order_view.order_id',$order_id)
    //         ->orderBy('user_sales_order_view.order_id','ASC')
    //         ->get();

    //         $edit_query = DB::table('user_sales_order_details_view')
    //             ->join('user_sales_order_view','user_sales_order_view.order_id','user_sales_order_details_view.order_id')
    //             ->where('user_sales_order_details_view.order_id', $order_id)
    //             ->select(DB::raw("CONCAT(date,' ',time) as order_date"),'itemcode','retailer_name','user_name','user_id','retailer_id','quantity','rate','product_name','product_id','scheme_qty as weight','user_sales_order_details_view.order_id as order_id')
    //             ->get();
    //     }
        

    //    if($edit_query)
    //     {
    //         $data['code'] = 200;
    //         $data['result'] = $edit_query;
    //         $data['result_top'] = $query_data;
    //         $data['remarks'] = $remarks_status;
    //         $data['message'] = 'success';
    //     } 
    //     else 
    //     {
    //         $data['code'] = 401;
    //         $data['result'] = '';
    //         $data['message'] = 'unauthorized request';
    //     }
    //     return json_encode($data);
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     #decrypt id
    //     $uid = Crypt::decryptString($id);
    //     $product_id = $request->product_id;
    //     $rate = $request->rate;
    //     $qty = $request->qty;

    //     foreach ($product_id as $key => $value) 
    //     {
    //         $total_sale_value_array[] = ($rate[$key]*$qty[$key]);
    //         $update_query = DB::table('user_sales_order_details')
    //                 ->where('order_id',$uid)
    //                 ->where('product_id',$value)
    //                 ->update(['quantity'=>$qty[$key],'updated_at'=>date('Y-m-d H:i:s')]);
    //     }
    //     $total_sale_value = array_sum($total_sale_value_array);
    //     $update_user_sales_order = DB::table('user_sales_order')->where('order_id',$uid)->update(['total_sale_value'=>$total_sale_value,'updated_at'=>date('Y-m-d H:i:s')]);

        
    //     if ($update_query) 
    //     {
    //         $update_user_sales_order = DB::table('user_sales_order')->where('order_id',$uid)->update(['total_sale_value'=>$total_sale_value,'updated_at'=>date('Y-m-d H:i:s')]);
            
    //         if($update_user_sales_order)
    //         {
    //             DB::commit();
    //             Session::flash('message', Lang::get('common.'.$this->current_menu).' updated successfully');
    //             Session::flash('class', 'success');
    //         }
    //         else
    //         {
    //              DB::rollback();
    //             Session::flash('message', 'Something went wrong!');
    //             Session::flash('class', 'danger');
    //         }
            
    //     } else {
    //         DB::rollback();
    //         Session::flash('message', 'Something went wrong!');
    //         Session::flash('class', 'danger');
    //     }

    //     return redirect()->intended($this->current_menu);
    // }
    public function retailer_dms_get_order_details(Request $request)
    {
        $order_id = $request->order_id;
        $company_id = Auth::user()->company_id;

        $data_query = DB::table('purchase_order')
            ->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
            ->join('retailer','retailer.id','=','purchase_order.retailer_id')
            // ->join('_dms_reason','_dms_reason.id','=','purchase_order.dms_order_reason_id')
            ->select('retailer.id as retailer_id','retailer.name as retailer_name','purchase_order.order_id as order_id','retailer.email as retailer_email','retailer.address as retailer_address','retailer.other_numbers as retailer_mobile','sale_date','retailer.tin_no as retailer_gst_no')
            ->where('purchase_order.order_id',$order_id)
            ->where('purchase_order.company_id',$company_id)
            ->first();

        $product_details = DB::table('purchase_order')
            ->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
            ->join('catalog_product','catalog_product.id','=','purchase_order_details.product_id')
            ->join('retailer','retailer.id','=','purchase_order.retailer_id')
            // ->join('_dms_reason','_dms_reason.id','=','purchase_order.dms_order_reason_id')
            ->select('catalog_product.id as product_id','catalog_product.name as product_name',DB::raw("SUM(purchase_order_details.cases) as cases"),DB::raw("SUM(purchase_order_details.pcs) as pcs"),DB::raw("SUM(purchase_order_details.scheme_qty) as scheme_qty"),'pr_rate as cases_rate','rate as pcs_rate')
            ->where('purchase_order.order_id',$order_id)
            ->where('purchase_order.company_id',$company_id)
            ->groupBy('product_id')
            ->get();

        $payemt_checked = DB::table('payment_collect_retailer')
                        // ->where('retailer_id',$retailer_id)
                        ->where('company_id',$company_id)
                        ->where('order_id',$order_id)
                        // ->whereRaw("DATE_FORMAT")
                        ->count();

        $dispatch_checked = DB::table('fullfillment_order')
                        ->join('fullfillment_order_details','fullfillment_order_details.order_id','=','fullfillment_order.order_id')
                        // ->where('retailer_id',$retailer_id)
                        ->where('fullfillment_order.company_id',$company_id)
                        ->where('fullfillment_order.order_id',$order_id)
                        // ->whereRaw("DATE_FORMAT")
                        ->count();

        $recject_checked = DB::table('purchase_order')
                        ->join('purchase_order_details','purchase_order_details.order_id','=','purchase_order.order_id')
                        // ->where('retailer_id',$retailer_id)
                        ->where('purchase_order.company_id',$company_id)
                        ->where('purchase_order.order_id',$order_id)
                        ->where('dms_order_reason_id',7)
                        // ->whereRaw("DATE_FORMAT")
                        ->count();

         $cancel_checked = DB::table('purchase_order')
                ->where('company_id',$company_id)
                ->where('order_id',$order_id)
                ->where('dms_order_reason_id',5)
                ->count();
            
        if($data_query)
        {
            $data['code'] = 200;
            $data['result_down'] = $product_details;
            $data['result_top'] = $data_query;
            $data['dispatch_date'] = date('Y-m-d');
            $data['payemt_status'] = (($payemt_checked)>0)?1:0;
            $data['dispatch_status'] = (($dispatch_checked)>0)?1:0;
            $data['recject_status'] = (($recject_checked)>0)?1:0;
            $data['cacncel_status'] = (($cancel_checked)>0)?1:0;
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
    public function submit_retailer_dms_order_details(Request $request)
    {
        // dd($request);
        $dispatch_date = $request->dispatch_date;
        $order_id = $request->order_id;
        $retailer_id = $request->retailer_id;
        // dd($retailer_id);
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
            'retailer_id' => $retailer_id,
            'order_id'=> $order_id,
            'payment_mode' => $payment_mode,
            'amount'  => $amount,
            'bank_branch' => $bank_branch,
            'cheque_no' => $cheque_no,
            'cheque_date' => $cheque_date,
            'trans_no' => $trans_no,
            'payment_time' => date('H:i:s'),
            'payment_date' => $payment_date,
            'user_id'=> 0,
            'company_id'=> $company_id,

        ];
        $insert_query = DB::table('payment_collect_retailer')->insert($myArr);
        if($insert_query)
        {
            $update_query = DB::table('purchase_order')->where('order_id',$order_id)->update(['dispatch_date'=>$dispatch_date,'dms_order_reason_id'=>'1']);
            $insert_data_log = DB::table('dms_order_reason_log')->insert(['order_id'=>$order_id,'company_id'=>$company_id,'dms_reason_id'=>'1','date'=>date("Y-m-d"),'time'=>date('H:i:s')]);
            // $fcm_token_query = DB::table('retailer_person_login')->where('retailer_id',$retailer_id)->first();
            // $msg = array(
            //     'body' => $order_id,
            //     'title'=> 'Payment Collection',
            //     'icon' => 'https://demo.msell.in/public/sku_images/20200416125417.jpg',
            //     'sound' => 'mySound'/*Default sound*/
            // );
            // $sent_status = self::CivicSendFcmNotification($fcm_token_query->fcm_token, $msg);

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
        return json_encode($data);
        
    }
    public function SendFcmNotification($fcm_token, $msg)
    {
        // echo $fcm_token;die;
        $url = "https://fcm.googleapis.com/fcm/send";

        $fields = array(
            'to' => $fcm_token,
            'notification' => $msg
        );

        $headers = array(
            'Authorization: key=' . config('app.FCM_API_ACCESS_KEY_CIVIC'),
            'Content-Type: application/json'
        );
        // print_r($headers);exit;
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
        return $result;
    }
    public function submit_retailer_dms_order_dispatch(Request $request)
    {
        $order_id = $request->order_id;
        $retailer_id = $request->retailer_id;
        $fullfillment_cases = $request->fullfillment_cases;
        $fullfillment_pcs = $request->fullfillment_pcs;
        $fullfillment_scheme_qty = $request->fullfillment_scheme_qty;
        $sale_date = $request->order_date;
        $product_id = $request->product_id;
        $product_name = $request->product_name;
        $product_cases = $request->product_cases;
        $product_qty = $request->product_qty;
        $company_id = Auth::user()->company_id;
        // dd($company_id);
        // dd($request);
        $retailer_data = DB::table('retailer')->where('id',$retailer_id)->first();
        $masrterArr = [
            'retailer_id'=> $retailer_id,
            'dealer_id'=>0,
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
        if($insert_fullfillment)
        {
            foreach ($product_id as $key => $value) 
            {
                $myArr = [
                    'product_id'=>$value,
                    'product_name'=>$product_name[$key],
                    'company_id' => $company_id,
                    'order_id'=>$order_id,
                    'product_fullfiment_qty'=>!empty($fullfillment_pcs[$key])?$fullfillment_pcs[$key]:'0',
                    'product_fullfiment_cases'=>!empty($fullfillment_cases[$key])?$fullfillment_cases[$key]:'0',
                    'product_fullfiment_scheme_qty'=>!empty($fullfillment_scheme_qty[$key])?$fullfillment_scheme_qty[$key]:'0',
                    'product_qty'=>!empty($product_qty[$key])?$product_qty[$key]:'0',
                    'product_qty_cases'=>!empty($product_cases[$key])?$product_cases[$key]:'0',
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                $insert_details = DB::table('fullfillment_order_details')->insert($myArr);


                $check2 = DB::table('retailer_stock')
                    ->join('retailer_stock_details','retailer_stock_details.order_id','=','retailer_stock.order_id')
                    ->where('product_id',$value)
                    // ->where('company_id',$company_id)
                    ->where('retailer_id',$retailer_id)
                    ->first();
                if(COUNT($check2)>0)
                {
                    $update_stock2= DB::table('retailer_stock_details')
                                    ->join('retailer_stock','retailer_stock_details.order_id','=','retailer_stock.order_id')
                                    ->where('retailer_id',$retailer_id)
                                    ->where('product_id',$value)
                                    // ->where('company_id',$company_id)
                                    ->update([
                                        'quantity'=>!empty($fullfillment_pcs[$key])?$check2->quantity+$fullfillment_pcs[$key]:$check2->quantity,
                                        'cases'=>!empty($fullfillment_cases[$key])?$check2->cases+$fullfillment_cases[$key]:$check2->cases,
                                        // 'server_date_time'=>date('Y-m-d H:i:s'),
                                    ]);
                }
                else
                {
                    $check3 = DB::table('retailer_stock')->where('order_id',$order_id)->count();
                    if($check3<=0)
                    {
                        $insert_stock2 = DB::table('retailer_stock')->insert([
                                    'order_id'=>$order_id,
                                    'dealer_id' => 0,                                   
                                    'user_id' => 0,                                   
                                    'location_id' => 0,                                   
                                    'retailer_id'=> $retailer_id,
                                   
                                    'date'=>date('Y-m-d'),
                                    'company_id'=>$company_id,
                                ]);

                        $insert_details_stock = DB::table('retailer_stock_details')->insert([
                                                'order_id'=>$order_id,
                                                'quantity'=>!empty($fullfillment_pcs[$key])?$fullfillment_pcs[$key]:'0',
                                                'cases'=>!empty($fullfillment_cases[$key])?$fullfillment_cases[$key]:'0',
                                                'product_id'=> $value,
                                                ]);
                    }
                    else
                    {

                        $insert_details_stock = DB::table('retailer_stock_details')->insert([
                                                'order_id'=>$order_id,
                                                'quantity'=>!empty($fullfillment_pcs[$key])?$fullfillment_pcs[$key]:'0',
                                                'cases'=>!empty($fullfillment_cases[$key])?$fullfillment_cases[$key]:'0',
                                                'product_id'=> $value,
                                                ]);
                    }
                   

                }


            }
            // $fcm_token_query = DB::table('retailer_person_login')->where('retailer_id',$retailer_id)->first();

            // $msg = array(
            //     'body' => $order_id,
            //     'title'=> 'Order Dispatch',
            //     'icon' => 'https://demo.msell.in/public/sku_images/20200416125417.jpg',
            //     'sound' => 'mySound'/*Default sound*/
            // );
            // $sent_status = self::CivicSendFcmNotification($fcm_token_query->fcm_token, $msg);
            $update_query = DB::table('purchase_order')->where('order_id',$order_id)->update(['dispatch_date'=>date('Y-m-d H:i:s'),'dms_order_reason_id'=>'3']);
            $insert_data_log = DB::table('dms_order_reason_log')->insert(['order_id'=>$order_id,'company_id'=>$company_id,'dms_reason_id'=>'3','date'=>date("Y-m-d"),'time'=>date('H:i:s')]);

           

            DB::commit();
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
    public function submit_retailer_dms_reject_order(Request $request)
    {
        $order_id = $request->dms_reject_order;
        // dd($order_id);  
        // $retailer_id = $request->retailer_id;
        $reason_order = $request->reason_order;
        $company_id = Auth::user()->company_id;

        $update = DB::table('purchase_order')
                ->where('order_id',$order_id)
                // ->where('retailer_id',$retailer_id)
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
    public function dms_retailer_order_confirm_submit(Request $request)
    {
        $order_id = $request->order_id;
        $company_id = Auth::user()->company_id;

        $data_query = DB::table('purchase_order')
                ->where('company_id',$company_id)
                ->where('order_id',$order_id)
                ->where('dms_order_reason_id',2)
                ->count();
        if($data_query>0)
        {
            $data['code'] = 200;
            $data['result'] = '1';
        }
        
        else
        {
            $data_query2 = DB::table('purchase_order')
                ->where('company_id',$company_id)
                ->where('order_id',$order_id)
                ->where('dms_order_reason_id',5)
                ->count();
            if($data_query2>0)
            {
                $data['code'] = 200;
                $data['result'] = '4';
            }
            else
            {
                $update = DB::table('purchase_order')
                ->where('order_id',$order_id)
                // ->where('retailer_id',$retailer_id)
                ->update(['dms_order_reason_id'=>2]);

                if($update)
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
                        $data['code'] = 200;
                        $data['result'] = '2';
                }
                else
                {
                    DB::rollback();
                    $data['code'] = 401;
                    $data['result'] = '3';
                }
            }
            
        }

        
        return json_encode($data);
    }
}
