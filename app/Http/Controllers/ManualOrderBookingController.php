<?php

namespace App\Http\Controllers;

use App\_module;
use App\Location1;
use App\Location2;
use App\Location3;
use App\Location4;
use App\Location5;
use App\Location6;
use App\Location7;
use App\Retailer;
use App\Dealer;
use App\DealerLocationRetailer;

use DB;
use Illuminate\Support\Facades\Crypt;
use Session;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use PDF;


class ManualOrderBookingController extends Controller
{
	public function __construct()
    {
        $this->current = 'manual_order_booking';
        $this->module=Lang::get('common.manual_order_booking');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $dealer = Dealer::where('dealer_status',1)->where('company_id',$company_id)->pluck('name','id'); 
        // $retailer = Retailer::where('retailer_status',1)->where('company_id',$company_id)->pluck('name','id'); 

        $stateData = DB::table('location_3')->where('company_id',$company_id)->where('status','1')->pluck('name','id');

        return view('manualOrderBooking.index', [
            'dealer' => $dealer,
            // 'retailer' => $retailer,
            'stateData' => $stateData,
            'current_menu' => $this->current
            ]);
    }

   	public function manual_order_booking_form(Request $request)
   	{
   		// dd($request);
   		$dealer_id = $request->dealer;
   		$retailer_id = $request->retailer;
   		$details = array();	
  		$name_title = '';
  		$company_details = array();
  		$company_id = Auth::user()->company_id;
  		$auth_id = Auth::user()->id;
  		$party_id = '';

   		if(!empty($dealer_id))
   		{
   			if(!empty($retailer_id))
   			{
   				$details = Retailer::where('id',$retailer_id)
							->where('retailer_status',1)
							->where('company_id',$company_id)
							->first();

				$name_title = 'Retailer';
				$party_id = $request->retailer;

   			}
   			else
   			{
   				$details = Dealer::where('id',$dealer_id)
							->where('company_id',$company_id)
							->where('dealer_status',1)
							->first();
				$party_id = $request->dealer;
				$name_title = 'Distributor';
   		
   			}
   		}
        elseif(!empty($retailer_id))
        {
            $details = Retailer::where('id',$retailer_id)
                        ->where('retailer_status',1)
                        ->where('company_id',$company_id)
                        ->first();

            $name_title = 'Retailer';
            $party_id = $request->retailer;

        }
   		$company_details = DB::table('company')
   						->where('id',$company_id)
   						->first();
		$product_details = DB::table('catalog_product')
						->where('status',1)
						->where('company_id',$company_id)
						->pluck('name','id');
		$date = date('d-M-Y');
		$str = str_shuffle("12345678900987654321345678908765431234567809876543");
        $random_id = substr($str, 0,2);  // return always a new string 
		$order_no = date('YmdHis').$auth_id.$random_id;
   		// dd($retailer_details);
   		return view('manualOrderBooking.ajax', [
            'details' => $details,
            'name_title' => $name_title,
            'company_details' => $company_details,
            'product_details'=> $product_details,
            'date'=> $date,
            'order_no'=>$order_no,
            'party_id'=> $party_id,
            'current_menu' => $this->current
            ]); 

   	}
   	public  function manual_orderproduct_details(Request $request)
   	{
   		$party_id = $request->party_id;
   		$product_id = $request->product_id;
   		$party_name = $request->party_name;
   		$product_details = array();
   		$company_id = Auth::user()->company_id;
   		// dd($request);
   		if($party_name == 'Distributor')
   		{
   			$dealer_data = Dealer::where('id',$party_id)->where('dealer_status',1)->where('company_id',$company_id)->first();
   			$product_details = DB::table('catalog_product')
   							->join('product_type','product_type.id','=','catalog_product.product_type')
   							->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
   							->select('other_dealer_rate as rate','weight','product_type.name as type_name')
   							->where('catalog_product.id',$product_id)
                            ->where('state_id',$dealer_data->state_id)
   							->where('catalog_product.company_id',$company_id)
   							->first();
			// dd($product_details);
			if(empty($product_details))
			{
				$product_details = DB::table('catalog_product')
   							->join('product_type','product_type.id','=','catalog_product.product_type')
   							->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
   							->select('other_dealer_rate as rate','weight','product_type.name as type_name')
   							->where('distributor_id',$party_id)
   							->where('catalog_product.id',$product_id)
                            ->where('catalog_product.company_id',$company_id)
   							->first();
				// dd($product_details);
			}
			$data['code'] = 200;
            $data['product_details'] = !empty($product_details)?$product_details:array();

   		}
   		elseif($party_name == 'Retailer')
   		{
   			$retailer_data = Retailer::join('location_view','location_view.l7_id','retailer.location_id')
   						->select('dealer_id','l3_id')
						->where('retailer.id',$party_id)
   						->where('retailer_status',1)
   						->where('retailer.company_id',$company_id)
   						->first();
   			$product_details = DB::table('catalog_product')
   							->join('product_type','product_type.id','=','catalog_product.product_type')
   							->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
   							->select('other_retailer_rate as rate','weight','product_type.name as type_name')
   							->where('state_id',$retailer_data->l3_id)
   							->where('catalog_product.id',$product_id)
                            ->where('catalog_product.company_id',$company_id)
   							->first();
			if(empty($product_details))
			{
				$product_details = DB::table('catalog_product')
   							->join('product_type','product_type.id','=','catalog_product.product_type')
   							->join('product_rate_list','product_rate_list.product_id','=','catalog_product.id')
   							->select('other_retailer_rate as rate','weight','product_type.name as type_name')
   							->where('distributor_id',$retailer_data->dealer_id)
   							->where('catalog_product.id',$product_id)
                            ->where('catalog_product.company_id',$company_id)
   							->first();
			}

            $data['code'] = 200;
            $data['product_details'] = !empty($product_details)?$product_details:array();
   		}
   		else
   		{
		 	$data['code'] = 401;
            $data['product_details'] = !empty($product_details)?$product_details:array();
   		}
        return json_encode($data);

   	}

    public function submit_manual_order_booking(Request $request)
    {
        // dd($request);
        $party_id = $request->party_id;
        $name_title = $request->party_name;
        $product_id = $request->product_id;
        $order_id = $request->order_no;
        $rate = $request->rate;
        $qty = $request->qty;
        $scheme = $request->scheme;
        $remarks = $request->remarks;
        $dispatch = $request->dispatch;
        $destination = $request->destination;
        $primary_unit = $request->primary_unit;
        $discount_type = !empty($request->discount_type)?$request->discount_type:'0';
        $discount_value = $request->discount_value;
        $company_id = Auth::user()->company_id;
        $auth_id = Auth::user()->id;
        $mailId = explode(',',!empty($request->email_sent)?$request->email_sent:'karan12@manacleindia.com');
        $sum_total_amount = array_sum($request->total_amt);


        $discval = ROUND($sum_total_amount*$discount_value/100,2);

        // dd($discval);
        if($discount_type == 1){
        $discount_value = $discval;
        }elseif($discount_type == 2){
        $discount_value = $request->discount_value;
        }


        $quantityPerOtherType = DB::table('catalog_product')
                                ->where('company_id',$company_id)
                                ->pluck('quantiy_per_other_type','id');

        // DB::beginTransaction();
        if($name_title == 'Retailer')
        {

        }
        elseif($name_title == 'Distributor')
        {

              if($company_id == 50){
                $chk_uso = DB::table('user_primary_sales_order')->select('janak_order_sequence')->where('company_id',$company_id)->orderBy('janak_order_sequence','DESC')->first();

                    if(empty($chk_uso->janak_order_sequence)){
                        $sequence = '1';
                    }else{
                        $sequence = $chk_uso->janak_order_sequence+1;
                    }

                }else{
                        $sequence = '';
                }




            // dd($request);
         
            foreach ($product_id as $key => $value) 
            {

                ///////////// final  qty for janak
                // $quantity_per_other_type = !empty($quantityPerOtherType[$value])?$quantityPerOtherType[$value]:'0';
                // $final_piece_qty = $qty[$key];
                // if($quantity_per_other_type == 0){
                // $calculated_secondary_qty = 0;
                // }
                // else{
                // $calculated_secondary_qty = ($final_piece_qty/$quantity_per_other_type);
                // }
                // $final_secondary_quantity = ROUND(($calculated_secondary_qty),3);
                // //////////// final rate for janak
                // $pcs_secondary_sale  = ($rate[$key]*$qty[$key]);
                // if($final_secondary_quantity == 0){
                // $final_secondary_rate = 0;
                // }
                // else{
                // $final_secondary_rate = ROUND(($pcs_secondary_sale)/($final_secondary_quantity),3);
                // }
                // dd($final_secondary_quantity);

                //////////




                $myArrDetails = [
                    'product_id'=> $value,
                    'primary_unit'=> $primary_unit[$key],
                    'rate'=> '0',
                    'quantity'=> '0',
                    'scheme_qty'=> $scheme[$key],
                    'order_id'=> $order_id,
                    'id'=> $order_id,
                    'secondary_rate'=> $rate[$key],
                    'secondary_qty'=> $qty[$key],
                    'final_secondary_rate'=> "$rate[$key]",
                    'final_secondary_qty'=> $qty[$key],
                    'company_id'=> $company_id,
                    'server_date_time'=>date('Y-m-d H:i:s'),

                ];

                // dd($myArrDetails);

                // $primary_order_details_insert = DB::table('user_primary_sales_order_details')->insert($myArrDetails);

                $finalArr[] = $myArrDetails;

                $amountBeforeDisc[] = $rate[$key]*$qty[$key];
            }





               $myArr = [
                'order_id' => $order_id,
                'id' => $order_id,
                'created_date' => date('Y-m-d'),
                'created_person_id' => $auth_id,
                'sale_date' => date('Y-m-d'),
                'receive_date' => date('Y-m-d'),
                'dispatch_date' => date('Y-m-d'),
                'date_time' => date('Y-m-d H:i:s'),
                'company_id'=> $company_id,
                'dealer_id'=> $party_id,
                'discount_type'=>$discount_type,
                'discount_value'=>$discount_value,
                'remarks'=> $remarks,
                'comment'=> $remarks,
                'dispatch_through'=> $dispatch,
                'destination'=> $destination,
                'order_from'=> "2",
                'pdf_name'=>$order_id.'.pdf',
                'janak_order_sequence'=> $sequence,
                'amount_before_discount'=> array_sum($amountBeforeDisc),
                'amount_after_discount'=> $request->input_grand_final_amount,
                

            ];







            $primary_order_insert = DB::table('user_primary_sales_order')->insert($myArr);
            // dd($finalArr);
            $primary_order_details_insert = DB::table('user_primary_sales_order_details')->insert($finalArr);


            if($primary_order_insert && $primary_order_details_insert)
            {

                // DB::commit();

                $upper_data = DB::table('user_primary_sales_order')
                        ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                        ->join('dealer','dealer.id','=','user_primary_sales_order.dealer_id')
                        ->join('location_3','location_3.id','=','dealer.state_id')
                        ->select('user_primary_sales_order.order_id','dealer.name as dealer_name','location_3.name as l3_name','dealer.*','user_primary_sales_order.dispatch_through','user_primary_sales_order.destination','user_primary_sales_order.remarks as remarks_c','user_primary_sales_order.sale_date as sale_date')
                        ->where('user_primary_sales_order.order_id',$order_id)
                        ->groupBy('user_primary_sales_order.order_id')
                        ->first();
                $company_details = DB::table('company')
                        ->where('id',$company_id)
                        ->first();
                $lower_data = DB::table('user_primary_sales_order')
                        ->join('user_primary_sales_order_details','user_primary_sales_order_details.order_id','=','user_primary_sales_order.order_id')
                        ->join('catalog_product','catalog_product.id','=','user_primary_sales_order_details.product_id')
                        ->select('catalog_product.name as product_name','user_primary_sales_order_details.primary_unit as primary_unit','user_primary_sales_order_details.rate as rate','catalog_product.weight as weight',DB::raw("SUM(user_primary_sales_order_details.quantity) as quantity"),DB::raw("SUM(rate*quantity) as total_amt"),DB::raw("SUM(user_primary_sales_order_details.scheme_qty) as scheme_qty"),DB::raw("SUM(scheme_qty+quantity) as dispatch_qty"),'discount_type','discount_value')
                        ->where('user_primary_sales_order.order_id',$order_id)
                        ->groupBy('user_primary_sales_order_details.order_id','user_primary_sales_order_details.product_id')
                        ->get();
                $customPaper = array(0, 0, 1240, 1748);
                $pdf_name = $order_id.'.pdf';
                // dd($pdf_name);
                $pdf = PDF::loadView('reports/pdfManualOrderBooking', ['upper_data' => $upper_data,'lower_data' => $lower_data,'company_details'=> $company_details,'name_title'=> $name_title]);
                $pdf->setPaper($customPaper);

                $pdf->save(public_path('pdf/'.$pdf_name));
                    // return $pdf->download('some-filename.pdf');
                
                $pdf_path = public_path() . '/pdf/' .$order_id. '.pdf';

                $mailMsg="Please find the attached Invoice Form $order_id";
      
                if(!empty($mailId))
                {
                    $send=Mail::raw($mailMsg, function ($message) use($mailId,$pdf_path,$mailMsg,$order_id)
                        {
                          foreach ($mailId as $mkey => $mail) 
                          {
                            $message->to($mail,$mail);
                          }  
                         
                          $message->subject("Invoice No: $order_id || Please do not reply")
                            ->attach($pdf_path);
                        });
                }

                $data['code'] = 200;
            }
            else
            {
                DB::rollback();
                $data['code'] = 401;
            }
            return json_encode($data);

        }
    }
}
