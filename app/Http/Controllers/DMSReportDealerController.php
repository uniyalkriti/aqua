<?php

namespace App\Http\Controllers;

use App\_module;
use DB;
use App\Location1;
use App\Location2;
use App\Location3;
use App\Location4;
use App\Location5;
use App\DemandOrderCart;
use App\DealerLocation;
use App\Dealer;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;
use PDF;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class DMSReportDealerController extends Controller
{
    public function __construct()
    {
        // clearstatcache();
        session_start();
        // dd($_SESSION);
        $this->signup_status = !empty($_SESSION['iclientdigimetsignup_status'])?$_SESSION['iclientdigimetsignup_status']:'0';
        // dd($this->signup_status);
        if($this->signup_status == 0 || $this->signup_status == '0')
        {
            header('Location: https://demo.msell.in/public/Signup');
            dd('1');
        }
        $auth_id = !empty($_SESSION['iclientdigimetid'])?$_SESSION['iclientdigimetid']:'0';
        $this->auth_id = $auth_id; 
        $this->dealer_id = !empty($_SESSION['iclientdigimetdata']['dealer_id'])?$_SESSION['iclientdigimetdata']['dealer_id']:'0';
        $this->csa_id = !empty($_SESSION['iclientdigimetdata']['csa_id'])?$_SESSION['iclientdigimetdata']['csa_id']:'0';
        $this->dealer_code = !empty($_SESSION['iclientdigimetdata']['dealer_code'])?$_SESSION['iclientdigimetdata']['dealer_code']:'0';
        $this->role_id = !empty($_SESSION['iclientdigimetdata']['urole'])?$_SESSION['iclientdigimetdata']['urole']:'0';
        // $this->dealer_code = '20602';
        $this->current_menu = 'Order-details'; 
        $this->date = date('Y-m-d');
        if($auth_id != '0' )
        {
            // dd('1');
            $auth_id = $auth_id;

        }
        else {
            # code...
            // dd('11');
            header('Location: https://demo.msell.in/client');
            dd('1');
        }
        // dd($auth_id);   

        // if()
    }

    public function short_item_list_report(Request $request)
    {

        $depo_filter = DB::table('dealer')
                        ->join('dealer_person_login','dealer_person_login.dealer_id','=','dealer.id')
                        ->where('dealer_status',1)
                        ->where('role_id',37)
                        ->pluck(DB::raw("CONCAT(dealer_code,' - ',name) as dealer_name"),'div_code_main');

        if($this->role_id == '41')
        {
            $terr_id = '';
            $dealer_id_cus = '';
            
            $dealer_report_section_data_data = DB::table('dealer_report_section_data')
                                    ->where('dealer_id',$this->dealer_id)
                                    ->where('location_2','!=','0')
                                    ->groupBy('location_2');
                                    if(!empty($terr_id))
                                    {
                                        // dd($this->dealer_id);
                                        $dealer_report_section_data_data->whereIn('location_2',$terr_id);
                                    }
            $dealer_report_section_data = $dealer_report_section_data_data->take(1)->pluck('location_2')->toArray();

            // dd($dealer_report_section_data);
            $dealer_id_data = DealerLocation::join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
                        ->join('dealer','dealer_location_rate_list.dealer_id','=','dealer.id');
                        if(!empty($dealer_id_cus))
                        {
                            $dealer_id_data->whereIn('dealer_id',$dealer_id_cus);
                        }
                        if(!empty($dealer_code))
                        {
                            $dealer_id_data->whereIn('dealer_code',$dealer_code);
                        }
                        if(!empty($terr_id))
                        {
                            $dealer_id_data->whereIn('l2_id',$terr_id);
                        }
                        else
                        {
                            $dealer_id_data->whereIn('l2_id',$dealer_report_section_data);
                        }
            $dealer_id = $dealer_id_data->groupBy('dealer_id')->pluck('dealer_id')->toArray();
            // dd($dealer_id);

            $location_2_arr_cus = Location2::where('status',1)
                                ->join('dealer_report_section_data','dealer_report_section_data.location_2','=','location_2.id')
                                // ->where('location_2.id',$dealer_report_section_data)
                                ->where('dealer_id',$this->dealer_id)
                                ->groupBy('location_2.id')
                                ->pluck('location_2.name as name','location_2.id as id'); 
            // dd($location_2_arr_cus);
            $dealer_arr_cus = Dealer::where('dealer_status',1)
                            ->join('dealer_location_rate_list','dealer.id','=','dealer_location_rate_list.dealer_id')
                            ->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
                            ->whereIn('l2_id',$dealer_report_section_data)
                            // ->whereIn('id',$dealer_id)
                            ->groupBy('dealer.id')
                            ->pluck('dealer.name','dealer.id'); 

            $dealer_code_filter_cus = DB::table('dealer')->where('dealer_status','1')->pluck('dealer_code','id'); 


            // $deiv_code_login = DB::table('dealer_person_login')->whereIn('dealer_id',$dealer_id)->pluck('div_code_main')->toArray(); 
            $dealer_code = DB::table('dealer')->whereIn('id',$dealer_id)->pluck('dealer_code')->toArray(); 
            // dd($div_code_entry);
           
            // $div_code = $deiv_code_login;
        }

        $dealer_name_data=DB::table('dealer')
                    ->where('dealer_status',1);
                    if(!empty($dealer_code))
                    {
                        $dealer_name_data->whereIn('dealer_code',$dealer_code);
                    }
        $dealer_name = $dealer_name_data->pluck(DB::raw("CONCAT(dealer_code,' - ',name) as name"),'dealer_code');

        return view('DMS/ShortItemReport.index',[
                    'dealer_name'=> $dealer_name,
                    'depo_filter'=>$depo_filter,
                    'role_id'=>$this->role_id,
                    'current_menu'=>$this->current_menu,

                ]);
    }


    public function short_item_list_report_ajax(Request $request)
    {
        if(empty($request->date_range_picker))
        {
            $from_date = date('Y-m').'-01';
            $to_date = date('Y-m-d');
        }
        else
        {
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        }
        if($this->role_id == 1)
        {

            $dealer_code = $request->dealer_code;
            $dealer_id = $request->dealer_id;
        }
        else
        {
            if($this->role_id == '41')
            {
                $terr_id = '';
                $dealer_id_cus = '';
                $dealer_code = $request->dealer_code;
                $dealer_report_section_data_data = DB::table('dealer_report_section_data')
                                        ->where('dealer_id',$this->dealer_id)
                                        ->where('location_2','!=','0')
                                        ->groupBy('location_2');
                                        if(!empty($terr_id))
                                        {
                                            // dd($this->dealer_id);
                                            $dealer_report_section_data_data->whereIn('location_2',$terr_id);
                                        }
                $dealer_report_section_data = $dealer_report_section_data_data->take(1)->pluck('location_2')->toArray();

                // dd($dealer_report_section_data);
                $dealer_id_data = DealerLocation::join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
                            ->join('dealer','dealer_location_rate_list.dealer_id','=','dealer.id');
                            if(!empty($dealer_id_cus))
                            {
                                $dealer_id_data->whereIn('dealer_id',$dealer_id_cus);
                            }
                            if(!empty($dealer_code))
                            {
                                $dealer_id_data->whereIn('dealer_code',$dealer_code);
                            }
                            if(!empty($terr_id))
                            {
                                $dealer_id_data->whereIn('l2_id',$terr_id);
                            }
                            else
                            {
                                $dealer_id_data->whereIn('l2_id',$dealer_report_section_data);
                            }
                $dealer_id = $dealer_id_data->groupBy('dealer_id')->pluck('dealer_id')->toArray();
                // dd($dealer_id);

                $location_2_arr_cus = Location2::where('status',1)
                                    ->join('dealer_report_section_data','dealer_report_section_data.location_2','=','location_2.id')
                                    // ->where('location_2.id',$dealer_report_section_data)
                                    ->where('dealer_id',$this->dealer_id)
                                    ->groupBy('location_2.id')
                                    ->pluck('location_2.name as name','location_2.id as id'); 
                // dd($location_2_arr_cus);
                $dealer_arr_cus = Dealer::where('dealer_status',1)
                                ->join('dealer_location_rate_list','dealer.id','=','dealer_location_rate_list.dealer_id')
                                ->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
                                ->whereIn('l2_id',$dealer_report_section_data)
                                // ->whereIn('id',$dealer_id)
                                ->groupBy('dealer.id')
                                ->pluck('dealer.name','dealer.id'); 

                $dealer_code_filter_cus = DB::table('dealer')->where('dealer_status','1')->pluck('dealer_code','id'); 


                // $deiv_code_login = DB::table('dealer_person_login')->whereIn('dealer_id',$dealer_id)->pluck('div_code_main')->toArray(); 
                // $dealer_code = DB::table('dealer')->whereIn('id',$dealer_id)->pluck('dealer_code')->toArray(); 
                $dealer_code_data = DB::table('dealer');
                            if(!empty($dealer_code))
                            {
                                $dealer_code_data->whereIn('dealer_code',$dealer_code);
                            }
                            else
                            {
                                $dealer_code_data->whereIn('id',$dealer_id);
                            }
                $dealer_code = $dealer_code_data->pluck('dealer_code')->toArray();
                // dd($div_code_entry);
               
                // $div_code = $deiv_code_login;
            }
            else
            {
                $dealer_code = $this->dealer_code;
                $dealer_id = $this->dealer_id;
                $dealer_code = array($dealer_code);
            }
            
        }
        
        // dd($dealer_code);
        // $div_code_entry = DB::table('ACC_MAST')
        //                 ->where('ACC_CODE',$dealer_code)
        //                 ->first();
        $data_div_code_data = DB::table('dealer_person_login')
                            ->join('dealer','dealer.id','=','dealer_person_login.dealer_id')
                            ->where('dealer_status',1)
                            ->groupBy('dealer.id');
                            if(!empty($dealer_code))
                            {
                                $data_div_code_data->whereIn('dealer_code',$dealer_code);
                            }
        $data_div_code = $data_div_code_data->pluck('div_code_main','dealer_code');

        $dealer_order_summary_data = DB::table('ORDER_HEAD')
                        ->join('ORDER_BODY',function($join){
                           $join->on('ORDER_BODY.VRNO', '=', 'ORDER_HEAD.VRNO');
                           $join->on('ORDER_BODY.DIV_CODE', '=', 'ORDER_HEAD.DIV_CODE');

                         })
                        ->join('ACC_MAST','ACC_MAST.ACC_CODE','=','ORDER_HEAD.ACC_CODE')
                        ->select('ACC_MAST.ACC_CODE as dealer_code','ORDER_HEAD.USER_CODE as order_type',DB::raw("SUM(ORDER_BODY.AFIELD1) as order_amt"),DB::raw("SUM(QTYCANCELLED) as qty_cancel"),'ORDER_HEAD.VRNO as order_no','ORDER_BODY.DO_VRNO as sl_no','ORDER_HEAD.VRDATE as order_date','ORDER_HEAD.VRDATE_FILTER as order_date_filter','ACC_MAST.DIV_CODE as div_code','ORDER_BODY.DIV_CODE as in_div_code','ACC_MAST.ACC_NAME as dealer_name','ORDER_HEAD.APPROVEDDATE as appv_date')
                        ->whereRaw("date_format(ORDER_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$from_date' and date_format(ORDER_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$to_date'") 
                        ->groupBy('ORDER_BODY.VRNO','ORDER_BODY.DO_VRNO','ORDER_HEAD.ACC_CODE')
                        ->orderBy("ORDER_HEAD.VRDATE_FILTER",'DESC')
                        ->orderBy("ORDER_BODY.VRNO",'DESC')
                        ->orderBy("ORDER_BODY.DO_VRNO",'ASC');
                        if(!empty($dealer_code))
                        {
                            // ->where('VRDATE_FILTER')
                            $dealer_order_summary_data->whereIn('ORDER_HEAD.ACC_CODE',$dealer_code);
                        }
        $dealer_order_summary = $dealer_order_summary_data->get();


        // $pluck_dealer_order_summary_data = DB::table('ORDER_HEAD')
        //                 ->join('ORDER_BODY','ORDER_BODY.VRNO','=','ORDER_HEAD.VRNO')
        //                 ->join('ACC_MAST','ACC_MAST.ACC_CODE','=','ORDER_HEAD.ACC_CODE')
        //                 // ->select('ACC_MAST.ACC_CODE as dealer_code',DB::raw("SUM(QTYCANCELLED) as qty_cancel"),'ORDER_HEAD.USER_CODE as order_type',DB::raw("SUM(ORDER_BODY.AFIELD1) as order_amt"),DB::raw("SUM(QTYCANCELLED) as qty_cancel"),'ORDER_HEAD.VRNO as order_no','ORDER_BODY.DO_VRNO as sl_no','ORDER_HEAD.VRDATE as order_date','ORDER_HEAD.VRDATE_FILTER as order_date_filter','ACC_MAST.DIV_CODE as div_code','ORDER_BODY.DIV_CODE as in_div_code','ACC_MAST.ACC_NAME as dealer_name','ORDER_HEAD.APPROVEDDATE as appv_date')
        //                 ->whereRaw("date_format(ORDER_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$from_date' and date_format(ORDER_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$to_date'") 
        //                 ->groupBy('ORDER_BODY.VRNO','ORDER_BODY.DO_VRNO','ORDER_HEAD.ACC_CODE','ORDER_BODY.DIV_CODE')
        //                 ->orderBy("ORDER_HEAD.VRDATE_FILTER",'DESC')
        //                 ->orderBy("ORDER_BODY.VRNO",'DESC')
        //                 ->orderBy("ORDER_BODY.DO_VRNO",'ASC');
        //                 if(!empty($dealer_code))
        //                 {
        //                     // ->where('VRDATE_FILTER')
        //                     $pluck_dealer_order_summary_data->whereIn('ORDER_HEAD.ACC_CODE',$dealer_code);
        //                 }
        // $pluck_dealer_order_summary = $pluck_dealer_order_summary_data->pluck(DB::raw("CONCAT(SUM(ORDER_BODY.AFIELD1),'||',SUM(QTYCANCELLED)) as value"),DB::raw("CONCAT(ORDER_BODY.VRNO,ORDER_BODY.DO_VRNO,ORDER_HEAD.ACC_CODE,ORDER_BODY.DIV_CODE) as data"));
        // dd($dealer_order_summary);
        $invoice_summary_data = DB::table('ITEMTRAN_HEAD')
                        // ->join('ITEMTRAN_BODY','ITEMTRAN_BODY.VRNO','=','ITEMTRAN_HEAD.VRNO')
                        ->join('ITEMTRAN_BODY',function($join){
                           $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                           $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                         })
                        ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_date'") 
                        ->groupBy('ITEMTRAN_BODY.ORDER_VRNO','ITEMTRAN_BODY.DO_VRNO','ITEMTRAN_HEAD.ACC_CODE');
                        if(!empty($dealer_code))
                        {
                            // ->where('VRDATE_FILTER')
                            $invoice_summary_data->whereIn('ITEMTRAN_HEAD.ACC_CODE',$dealer_code);
                        }
        $invoice_summary = $invoice_summary_data->pluck(DB::raw("CONCAT(ITEMTRAN_BODY.VRNO,'||',SUM(ITEMTRAN_BODY.VALISSUED),'||',SUM(ITEMTRAN_BODY.DRAMT),'||',ITEMTRAN_HEAD.ACC_CODE,'||',ITEMTRAN_HEAD.VRDATE) as value"),DB::raw("CONCAT(ITEMTRAN_BODY.ORDER_VRNO,ITEMTRAN_BODY.DO_VRNO,ITEMTRAN_HEAD.ACC_CODE) as key_t"));
        // dd($invoice_summary);
        return view('DMS/ShortItemReport.ajax',[
                    'dealer_order_summary'=> $dealer_order_summary,
                    'data_div_code'=>$data_div_code,
                    'invoice_summary'=>$invoice_summary,
                    'role_id'=>$this->role_id,
                    // 'pluck_dealer_order_summary'=>$pluck_dealer_order_summary,
                    'current_menu'=>$this->current_menu,

        ]);
        
    }
    public function item_detailer_short_item(Request $request)
    {
        $order_no = $request->order_no;
        $sl_no = $request->sl_no;
        $order_date = $request->order_date;
        // $div_code = $request->div_code;

        $data_return = DB::table('ORDER_HEAD')
                        ->join('ORDER_BODY',function($join){
                           $join->on('ORDER_BODY.VRNO', '=', 'ORDER_HEAD.VRNO');
                           $join->on('ORDER_BODY.DIV_CODE', '=', 'ORDER_HEAD.DIV_CODE');

                         })
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ORDER_BODY.ITEM_CODE')
                        ->select('ITEM_MAST.ITEM_NAME',DB::raw("SUM(QTYCANCELLED) as qty_cancel"),DB::raw("SUM(ORDER_BODY.AFIELD1) as order_amt"),'REBOOK_VRNO as r_vrno','ITEM_MAST.ITEM_CODE','QTYCANCELLED_FLAG')
                        ->whereRaw("date_format(ORDER_HEAD.VRDATE_FILTER,'%Y-%m-%d')='$order_date'") 
                        ->where('ORDER_BODY.VRNO',$order_no)
                        ->where('ORDER_BODY.DO_VRNO',$sl_no)
                        // ->where('ORDER_BODY.DIV_CODE',$div_code)
                        ->groupBy('ORDER_BODY.ITEM_CODE')
                        ->get();
        // dd($data_return);

        $data['code'] = 200;
        $data['data_return'] = $data_return;
        return json_encode($data);
    }
    public function change_date()
    {
        $data = DB::table('DEALER_TARGET_MAST')
                ->where('id','>','0')
                // ->where('id','<=','/**/50000')
                ->get();
        // dd($data);

        foreach ($data as $key => $value) {
            # code...
            $updatre = DB::table('DEALER_TARGET_MAST')
                    ->where('id',$value->id)
                    ->update([
                        'FROM_DATE_FILTER' => date('Y-m-d',strtotime($value->FROM_DATE)),
                        'TO_DATE_FILTER' => date('Y-m-d',strtotime($value->TO_DATE)),
                    ]);
        }
    }
    public function dealer_sale_details_report(Request $request)
    {

        $depo_filter = DB::table('dealer')
                        ->join('dealer_person_login','dealer_person_login.dealer_id','=','dealer.id')
                        ->where('dealer_status',1)
                        ->where('role_id',37)
                        ->pluck(DB::raw("CONCAT(dealer_code,' - ',name) as dealer_name"),'div_code_main');

        if($this->role_id == '41')
        {
            $terr_id = '';
            $dealer_id_cus = '';
            
            $dealer_report_section_data_data = DB::table('dealer_report_section_data')
                                    ->where('dealer_id',$this->dealer_id)
                                    ->where('location_2','!=','0')
                                    ->groupBy('location_2');
                                    if(!empty($terr_id))
                                    {
                                        // dd($this->dealer_id);
                                        $dealer_report_section_data_data->whereIn('location_2',$terr_id);
                                    }
            $dealer_report_section_data = $dealer_report_section_data_data->take(1)->pluck('location_2')->toArray();

            // dd($dealer_report_section_data);
            $dealer_id_data = DealerLocation::join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
                        ->join('dealer','dealer_location_rate_list.dealer_id','=','dealer.id');
                        if(!empty($dealer_id_cus))
                        {
                            $dealer_id_data->whereIn('dealer_id',$dealer_id_cus);
                        }
                        if(!empty($dealer_code))
                        {
                            $dealer_id_data->whereIn('dealer_code',$dealer_code);
                        }
                        if(!empty($terr_id))
                        {
                            $dealer_id_data->whereIn('l2_id',$terr_id);
                        }
                        else
                        {
                            $dealer_id_data->whereIn('l2_id',$dealer_report_section_data);
                        }
            $dealer_id = $dealer_id_data->groupBy('dealer_id')->pluck('dealer_id')->toArray();
            // dd($dealer_id);

            $location_2_arr_cus = Location2::where('status',1)
                                ->join('dealer_report_section_data','dealer_report_section_data.location_2','=','location_2.id')
                                // ->where('location_2.id',$dealer_report_section_data)
                                ->where('dealer_id',$this->dealer_id)
                                ->groupBy('location_2.id')
                                ->pluck('location_2.name as name','location_2.id as id'); 
            // dd($location_2_arr_cus);
            $dealer_arr_cus = Dealer::where('dealer_status',1)
                            ->join('dealer_location_rate_list','dealer.id','=','dealer_location_rate_list.dealer_id')
                            ->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
                            ->whereIn('l2_id',$dealer_report_section_data)
                            // ->whereIn('id',$dealer_id)
                            ->groupBy('dealer.id')
                            ->pluck('dealer.name','dealer.id'); 

            $dealer_code_filter_cus = DB::table('dealer')->where('dealer_status','1')->pluck('dealer_code','id'); 


            // $deiv_code_login = DB::table('dealer_person_login')->whereIn('dealer_id',$dealer_id)->pluck('div_code_main')->toArray(); 
            $dealer_code = DB::table('dealer')->whereIn('id',$dealer_id)->pluck('dealer_code')->toArray(); 
            // dd($div_code_entry);
           
            // $div_code = $deiv_code_login;
        }

        $dealer_name_data=DB::table('dealer')
                    ->where('dealer_status',1);
                    if(!empty($dealer_code))
                    {
                        $dealer_name_data->whereIn('dealer_code',$dealer_code);
                    }
        $dealer_name = $dealer_name_data->pluck(DB::raw("CONCAT(dealer_code,' - ',name) as name"),'dealer_code');

        return view('DMS/saleReportProd.index',[
                    'dealer_name'=> $dealer_name,
                    'depo_filter'=>$depo_filter,
                    'role_id'=>$this->role_id,
                    'current_menu'=>$this->current_menu,

                ]);
    }
    public function dealer_sale_details_report_ajax(Request $request)
    {

        if(empty($request->date_range_picker))
        {
            $from_date = date('Y-m').'-01';
            $to_date = date('Y-m-d');
        }
        else
        {
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        }
        if($this->role_id == 1)
        {

            $dealer_code = $request->dealer_code;
            $dealer_id = $request->dealer_id;
        }
        else
        {
            if($this->role_id == '41')
            {
                $terr_id = '';
                $dealer_id_cus = '';
                $dealer_code = $request->dealer_code;
                $dealer_report_section_data_data = DB::table('dealer_report_section_data')
                                        ->where('dealer_id',$this->dealer_id)
                                        ->where('location_2','!=','0')
                                        ->groupBy('location_2');
                                        if(!empty($terr_id))
                                        {
                                            // dd($this->dealer_id);
                                            $dealer_report_section_data_data->whereIn('location_2',$terr_id);
                                        }
                $dealer_report_section_data = $dealer_report_section_data_data->take(1)->pluck('location_2')->toArray();

                // dd($dealer_report_section_data);
                $dealer_id_data = DealerLocation::join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
                            ->join('dealer','dealer_location_rate_list.dealer_id','=','dealer.id');
                            if(!empty($dealer_id_cus))
                            {
                                $dealer_id_data->whereIn('dealer_id',$dealer_id_cus);
                            }
                            if(!empty($dealer_code))
                            {
                                $dealer_id_data->whereIn('dealer_code',$dealer_code);
                            }
                            if(!empty($terr_id))
                            {
                                $dealer_id_data->whereIn('l2_id',$terr_id);
                            }
                            else
                            {
                                $dealer_id_data->whereIn('l2_id',$dealer_report_section_data);
                            }
                $dealer_id = $dealer_id_data->groupBy('dealer_id')->pluck('dealer_id')->toArray();
                // dd($dealer_id);

                $location_2_arr_cus = Location2::where('status',1)
                                    ->join('dealer_report_section_data','dealer_report_section_data.location_2','=','location_2.id')
                                    // ->where('location_2.id',$dealer_report_section_data)
                                    ->where('dealer_id',$this->dealer_id)
                                    ->groupBy('location_2.id')
                                    ->pluck('location_2.name as name','location_2.id as id'); 
                // dd($location_2_arr_cus);
                $dealer_arr_cus = Dealer::where('dealer_status',1)
                                ->join('dealer_location_rate_list','dealer.id','=','dealer_location_rate_list.dealer_id')
                                ->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
                                ->whereIn('l2_id',$dealer_report_section_data)
                                // ->whereIn('id',$dealer_id)
                                ->groupBy('dealer.id')
                                ->pluck('dealer.name','dealer.id'); 

                $dealer_code_filter_cus = DB::table('dealer')->where('dealer_status','1')->pluck('dealer_code','id'); 


                // $deiv_code_login = DB::table('dealer_person_login')->whereIn('dealer_id',$dealer_id)->pluck('div_code_main')->toArray(); 
                // $dealer_code = DB::table('dealer')->whereIn('id',$dealer_id)->pluck('dealer_code')->toArray(); 
                $dealer_code_data = DB::table('dealer');
                            if(!empty($dealer_code))
                            {
                                $dealer_code_data->whereIn('dealer_code',$dealer_code);
                            }
                            else
                            {
                                $dealer_code_data->whereIn('id',$dealer_id);
                            }
                $dealer_code = $dealer_code_data->pluck('dealer_code')->toArray();
                // dd($div_code_entry);
               
                // $div_code = $deiv_code_login;
            }
            else
            {
                $dealer_code = $this->dealer_code;
                $dealer_id = $this->dealer_id;
                $dealer_code = array($dealer_code);
            }
            
        }   
        $mktg_catg = DB::table('MKTG_CATG_MAST')
                    ->join('PROD_CATG_MAST','PROD_CATG_MAST.MKTG_CATG','=','MKTG_CATG_MAST.MKTG_CATG')
                    ->select('MKTG_CATG_NAME as name',DB::raw("COUNT(PROD_CATG_MAST.id) as count_prod"))
                    ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
                    ->orderBy('MKTG_CATG_MAST.id','ASC')
                    ->get();

        $prod_catg = DB::table('PROD_CATG_MAST')
                    ->join('MKTG_CATG_MAST','PROD_CATG_MAST.MKTG_CATG','=','MKTG_CATG_MAST.MKTG_CATG')
                    ->groupBy('PROD_CATG_MAST.PROD_CATG')
                    ->orderBy('PROD_CATG_MAST.USER_SEQ','ASC')
                    ->pluck('PROD_CATG_MAST.PROD_CATG_NAME','PROD_CATG_MAST.PROD_CATG');

        // $qty_prod_catg = DB::table('ITEMTRAN_HEAD')
        //             ->join('ITEMTRAN_BODY',function($join){
        //                 $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
        //                 $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

        //             })
        //             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
        //             ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
        //             ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
        //             ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_MAST.MKTG_CATG')
        //             ->select(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'))
        //             // ->whereIn('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
        //             // ->where('MKTG_CATG_MAST.MKTG_CATG', $mktg_catg_array[$i])
        //             // ->whereIn('ITEMTRAN_BODY.DIV_CODE',$div_code)
        //             // ->whereIn('ITEMTRAN_HEAD.DIV_CODE',$div_code)
        //             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_date'") 
        //             ->groupBy('MKTG_CATG_MAST.MKTG_CATG','MKTG_CATG_MAST.MKTG_CATG')
        //             ->first();


        return view('DMS/saleReportProd.ajax',[
                    'mktg_catg'=> $mktg_catg,
                    'prod_catg'=>$prod_catg,
                    // 'invoice_summary'=>$invoice_summary,
                    // 'pluck_dealer_order_summary'=>$pluck_dealer_order_summary,
                    'current_menu'=>$this->current_menu,

        ]);
        
    }
//     /**
//      * Display a listing of the resource.
//      *
//      * @return \Illuminate\Http\Response
//      */
//     public function index(Request $request)
//     {
        
//         if(empty($request->date_range_picker))
//         {
//             $from_date = date('Y-m').'-01';
//             $to_date = date('Y-m-d');
//         }
//         else
//         {
//             $explodeDate = explode(" -", $request->date_range_picker);
//             $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
//             $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
//         }
//         $order_no = $request->order_no;
        
//         $div_code_entry = DB::table('ACC_MAST')->where('ACC_CODE',$this->dealer_code)->first();

//         $deiv_code_login = DB::table('dealer_person_login')->where('dealer_id',$this->dealer_id)->first(); 
//         // dd($div_code_entry);
//         if($this->dealer_code == '00001')
//         {
//             $div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:'JH';

//         }
//         else
//         {
//             $div_code_else = !empty($deiv_code_login->div_code_main)?$deiv_code_login->div_code_main:'0';
//             $div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:$div_code_else;
//         }
//         // dd($div_code);
//         $paginate = !empty($request->perpage)?$request->perpage:'25';
//         $order_details = 'test';
//         $records_data = DB::table('demand_order')
//                 ->join('demand_order_details','demand_order_details.order_id','=','demand_order.order_id')  
//                 ->whereRaw("date_format(demand_order.order_date,'%Y-%m-%d')>='$from_date' and date_format(demand_order.order_date,'%Y-%m-%d')<='$to_date'") 
//                 ->groupBy('demand_order.id')
//                 ->orderBy('demand_order.id','DESC');
//                 if(!empty($order_no))
//                 {
//                     $records_data->where('demand_order.order_id','LIKE','%'.$order_no.'%')->orWhere('erp_order_no','LIKE','%'.$order_no.'%');
//                 }
//                 if($this->role_id == 37)
//                 {

//                     if($this->dealer_id == '1170')
//                     {

//                         $records_data->select('demand_order.*','demand_order.id as order_id',DB::raw("(SUM((total_rs-t1_rate-atd_rate)+atd_rate)) as total_value"))->where('demand_order.dealer_id',$this->dealer_id);
//                     }
//                     else
//                     {
//                         $records_data->select('demand_order.*','demand_order.id as order_id','ACC_MAST.*',DB::raw("(SUM((total_rs-t1_rate-atd_rate)+atd_rate)) as total_value"))->join('dealer','dealer.id','=','demand_order.dealer_id')
//                                 ->join('ACC_MAST','ACC_MAST.ACC_CODE','=','dealer.dealer_code')
//                                 // ->where('ACC_MAST.ACC_CODE',,'!=',$this->dealer_code)
//                                 // ->where('demand_order.id','38')
//                                 ->where('sale_div_code',$div_code);
//                     }
                    
//                 }
//                 else
//                 {
//                     $records_data->select('demand_order.*','demand_order.id as order_id',DB::raw("(SUM((total_rs-t1_rate-atd_rate)+atd_rate)) as total_value"))->where('demand_order.dealer_id',$this->dealer_id);
//                 }
//         $records = $records_data->get()->toArray();

//         if($this->dealer_id != '1170')
//         {
//             if($this->role_id == '37')
//             {


//                 $records_data = DB::table('demand_order')
//                         ->join('demand_order_details','demand_order_details.order_id','=','demand_order.order_id')  
//                         ->whereRaw("date_format(demand_order.order_date,'%Y-%m-%d')>='$from_date' and date_format(demand_order.order_date,'%Y-%m-%d')<='$to_date'") 
//                         ->groupBy('demand_order.id')
//                         ->orderBy('demand_order.id','DESC');
//                         if(!empty($order_no))
//                         {
//                             $records_data->where('demand_order.order_id','LIKE','%'.$order_no.'%')->orWhere('erp_order_no','LIKE','%'.$order_no.'%');
//                         }
//                         if($this->role_id == 37)
//                         {
                            
//                                 $records_data->select('demand_order.*','demand_order.id as order_id','ACC_MAST.*',DB::raw("(SUM((total_rs-t1_rate-atd_rate)+atd_rate)) as total_value"))->join('dealer','dealer.id','=','demand_order.dealer_id')
//                                         ->join('ACC_MAST','ACC_MAST.ACC_CODE','=','dealer.dealer_code')
//                                         // ->where('ACC_MAST.ACC_CODE',,'!=',$this->dealer_code)
//                                         ->where('dealer_id',$this->dealer_id);
                            
                            
//                         }
//                         else
//                         {
//                             $records_data->select('demand_order.*','demand_order.id as order_id',DB::raw("(SUM((total_rs-t1_rate-atd_rate)+atd_rate)) as total_value"))->where('demand_order.dealer_id',$this->dealer_id);
//                         }
//                 $records_depo = $records_data->get()->toArray();

//                 $records = array_merge($records_depo,$records);
//             }
//         }
//         // dd($records);
//         if($this->dealer_id == '1170')
//         {
//             return view('DMS/ShortItemReport.index',[
//                     'order_details'=> $order_details,
//                     'dealer_code_cus'=>$this->dealer_code,
//                     'dealer_id_cus'=>$this->dealer_id,
//                     'role_id'=> 5,
//                     'current_menu'=>$this->current_menu,
//                     'records'=> $records

//                 ]);
//         }
//         else
//         {
//             $depo_filter = '';
//             $dealer_name = '';
//             if($this->role_id == '1')
//             {
//                 $records_data = DB::table('demand_order')
//                         ->join('demand_order_details','demand_order_details.order_id','=','demand_order.order_id')  
//                         ->whereRaw("date_format(demand_order.order_date,'%Y-%m-%d')>='$from_date' and date_format(demand_order.order_date,'%Y-%m-%d')<='$to_date'") 
//                         ->groupBy('demand_order.id')
//                         ->orderBy('demand_order.id','DESC');
//                         $records_data->select('demand_order.*','demand_order.id as order_id','ACC_MAST.*',DB::raw("(SUM((total_rs-t1_rate-atd_rate)+atd_rate)) as total_value"))->join('dealer','dealer.id','=','demand_order.dealer_id')
//                         ->join('ACC_MAST','ACC_MAST.ACC_CODE','=','dealer.dealer_code');
//                         // ->where('sale_div_code',$div_code);

//                         if(!empty($order_no))
//                         {
//                             $records_data->where('demand_order.order_id','LIKE','%'.$order_no.'%')->orWhere('erp_order_no','LIKE','%'.$order_no.'%');
//                         }
//                         if(!empty($request->depo_filter))
//                         {
//                             $records_data->whereIn('demand_order.sale_div_code',$request->depo_filter);
//                         }
//                         if(!empty($request->distributor))
//                         {
//                             $records_data->whereIn('demand_order.dealer_id',$request->distributor);
//                         }
                        
                       
//                 $records = $records_data->get()->toArray();

//                 $depo_filter = DB::table('dealer')
//                         ->join('dealer_person_login','dealer_person_login.dealer_id','=','dealer.id')
//                         ->where('dealer_status',1)
//                         ->where('role_id',37)
//                         ->pluck(DB::raw("CONCAT(dealer_code,' - ',name) as dealer_name"),'div_code_main');
//                 $dealer_name=DB::table('dealer')
//                             ->where('dealer_status',1)
//                             ->pluck(DB::raw("CONCAT(dealer_code,' - ',name) as name"),'id');

//                 return view('DMS/QuickOrder.adminIndex',[
//                     'order_details'=> $order_details,
//                     'dealer_code_cus'=>$this->dealer_code,
//                     'dealer_id_cus'=>$this->dealer_id,
//                     'role_id'=> $this->role_id,
//                     'current_menu'=>$this->current_menu,
//                     'depo_filter'=>$depo_filter,
//                     'dealer_name'=>$dealer_name,
//                     'records'=> $records

//                 ]);
//             }
//             return view('DMS/QuickOrder.index',[
//                     'order_details'=> $order_details,
//                     'dealer_code_cus'=>$this->dealer_code,
//                     'dealer_id_cus'=>$this->dealer_id,
//                     'role_id'=> $this->role_id,
//                     'current_menu'=>$this->current_menu,
//                     'depo_filter'=>$depo_filter,
//                     'dealer_name'=>$dealer_name,
//                     'records'=> $records

//                 ]);
            
//         }
        

//         // return 
        
//         // dd(Session::get('iclientdigimetdata'));
//         // dd($_SESSION['iclientdigimet']);
//         // $userid = $_SESSION['iclientdigimet.data']['id'];
//         // dd($userid);
//     }

//     public function create()
//     {
//         $dealer_code = $this->dealer_code;

//         $div_code_entry = DB::table('ACC_MAST')->where('ACC_CODE',$this->dealer_code)->first();

//         $deiv_code_login = DB::table('dealer_person_login')->where('dealer_id',$this->dealer_id)->first(); 
//         // dd($div_code_entry);
       
//         $div_code_else = !empty($deiv_code_login->div_code_main)?$deiv_code_login->div_code_main:'0';
//         $div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:$div_code_else;
        

//         // $dealer_code = '20602';
//         $image_dyn = DB::table('image_mast')->where('img_locn','Order')->get();
//         $div_code_master = DB::table('_div_code_master')->where('status','1')->pluck('name','id');

//         $from_target_date = "01-APR-" .(date('y'));
//         $to_target_date = "31-MAR-" .(date('y')+1);
//         $from_sale_date = (date('Y'))."-04-01";
//         $to_sale_date = (date('Y')+1)."-03-31";

//         $from_date_month_sale = date('Y-m').'-01';
//         $to_date_month_sale = date('Y-m-t');




//         $record_product_id = DB::table('demand_order_cart')
//                     ->join('demand_order_details_cart','demand_order_details_cart.order_id','=','demand_order_cart.order_id')  
//                     ->join('WWW_WEB_ITEM_DISPLAY','WWW_WEB_ITEM_DISPLAY.ITEM_CODE','=','demand_order_details_cart.product_id')  
//                     // ->select()
//                     ->where('demand_order_cart.dealer_id',$this->dealer_id) 
//                     ->groupBy('demand_order_details_cart.product_id')
//                     // ->get()
//                     // ->toArray();
//                     ->pluck('product_id')->toArray();

//         // dd($to_target_date);
//         // $from_date = strtoupper(date('d-M-y'));
//         // $to_date = strtoupper(date('d-M-y'));
//         $paginate = !empty($request->perpage)?$request->perpage:'10';
//         $order_details = 'test';
//         // $records = DB::table('demand_order')->where('dealer_id',$this->dealer_id)->paginate($paginate);

//         $cart_details = DB::table('demand_order_details_cart')
//                     ->join('demand_order_cart','demand_order_cart.order_id','=','demand_order_details_cart.order_id')
//                     ->select(DB::raw("COUNT(demand_order_details_cart.product_id) as count"),DB::raw("SUM(total_rs-t1_rate-atd_rate+atd_rate) as sale_vale"))
//                     ->where('demand_order_cart.dealer_id',$this->dealer_id)
//                     // ->groupBy('')
//                     ->first();

//         $converstion_unit_item_code = DB::table('ITEM_AUM_MAST')->pluck('UMTOAUM','ITEM_CODE');

//         $catalog_product_details = DB::table('WWW_WEB_ITEM_DISPLAY')->select(DB::raw("CONCAT(ITEM_NAME,' (',ITEM_CODE,')') AS ITEM_NAME"))->whereNotIn('ITEM_CODE',$record_product_id)->where('ITEM_STATUS','!=','C')->orderBy('ITEM_CODE','ASC')->get();
//         $mktg_cat_array = DB::table('MKTG_CATG_MAST')->orderBy('MKTG_CATG','ASC')->get();

//         $asv_wise_target_data = DB::table('DEALER_TARGET_MAST')
//                 ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','DEALER_TARGET_MAST.MKTG_CATG')  
//                 ->where('MKTG_CATG_MAST.MKTG_CATG','ASV')
//                 ->where('DEALER_TARGET_MAST.PROD_CATG','ASV')
//                 ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
//                 ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
//                 ->where('DEALER_TARGET_MAST.ACC_CODE', $this->dealer_code);
//                 // ->where('PROD_CATG','!=','')
//                 $asv_wise_target = !empty($asv_wise_target_data->first()->TARGET_QTY)?$asv_wise_target_data->first()->TARGET_QTY:0;
//         // dd($asv_wise_target);
//         $asv_wise_sale_data = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')
//             ->select(DB::raw('SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED'))
//             ->where('MKTG_CATG_MAST.MKTG_CATG', 'ASV')
//             ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_sale_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_sale_date'") 
//             ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
//             ->where('ITEMTRAN_HEAD.ACC_CODE', $this->dealer_code)
//             ->first();

//         $montha_sv_wise_sale_data = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')
//             ->select(DB::raw('SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED'))
//             ->where('MKTG_CATG_MAST.MKTG_CATG', 'ASV')
//             ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_date_month_sale' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_date_month_sale'") 
//             ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
//             ->where('ITEMTRAN_HEAD.ACC_CODE', $this->dealer_code)
//             ->first();
      

//         $mktg_catg_array = array("ASV", "CLA", "GEN", "GLD", "OTC", "OT2", "JPS", "FMC");
//         for ($i=0; $i <= 7; $i++) { 
//             # code...
//             $mktg_catg_wise_target = DB::table('DEALER_TARGET_MAST')
//                 ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','DEALER_TARGET_MAST.MKTG_CATG')  
//                 ->where('DEALER_TARGET_MAST.MKTG_CATG',$mktg_catg_array[$i])
//                 ->where('DEALER_TARGET_MAST.PROD_CATG','')
//                 ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
//                 ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
//                 ->where('DEALER_TARGET_MAST.ACC_CODE', $this->dealer_code);
//                 $mktg_catg_wise_targets[$mktg_catg_array[$i]] = !empty($mktg_catg_wise_target->first()->TARGET_AMT)?$mktg_catg_wise_target->first()->TARGET_AMT:0;
//         }
//         // dd($mktg_catg_wise_targets);
//         for ($i=0; $i <= 7; $i++) { 
//             # code...
//             $mktg_catg_wise_sales[$mktg_catg_array[$i]] = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//             ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
//             ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_MAST.MKTG_CATG')
//             ->select(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'))
//             ->where('ITEMTRAN_HEAD.ACC_CODE', $this->dealer_code)
//             ->where('MKTG_CATG_MAST.MKTG_CATG', $mktg_catg_array[$i])
//             ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_sale_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_sale_date'") 
//             ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
//             ->first();
//             // dd($mktg_catg_wise_sales);
//         }
//         for ($i=0; $i <= 7; $i++) { 
//             # code...
//             $month_mktg_catg_wise_sales[$mktg_catg_array[$i]] = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//             ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
//             ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_MAST.MKTG_CATG')
//             ->select(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'))
//             ->where('ITEMTRAN_HEAD.ACC_CODE', $this->dealer_code)
//             ->where('MKTG_CATG_MAST.MKTG_CATG', $mktg_catg_array[$i])
//             ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_date_month_sale' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_date_month_sale'") 
//             ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
//             ->first();
//             // dd($mktg_catg_wise_sales);
//         }
//         // dd($mktg_catg_wise_sales);


//         return view('DMS/QuickOrder.create',[
//                     'order_details'=> $order_details,
//                     'catalog_product_details'=> $catalog_product_details,
//                     'current_menu'=>$this->current_menu,
//                     // 'records'=> $records,
//                     'mktg_cat_array'=> $mktg_cat_array,
//                     'auth_id'=>$this->dealer_id,
//                     'role_id_cus'=>$this->role_id,
//                     'converstion_unit_item_code'=> $converstion_unit_item_code,

//                     'asv_wise_target' => $asv_wise_target,
//                     'image_dyn'=> $image_dyn,
//                     'sale_value_cart'=> !empty($cart_details->sale_vale)?$cart_details->sale_vale:'0',
//                     'count_prod_cart'=> !empty($cart_details->count)?$cart_details->count:'0',
//                     // 'cla_wise_target' => $cla_wise_target,
//                     // 'gen_wise_target' => $gen_wise_target,
//                     'div_code_master'=> $div_code_master,
//                     // 'gld_wise_target' => $gld_wise_target,
//                     // 'ot2_wise_target' => $ot2_wise_target,

//                     'montha_sv_wise_sale_data'=>$montha_sv_wise_sale_data,
//                     'month_mktg_catg_wise_sales'=>$month_mktg_catg_wise_sales,

//                     'asv_wise_sale_data'=>$asv_wise_sale_data,
//                     'mktg_catg_wise_sales'=>$mktg_catg_wise_sales,
//                     'mktg_catg_wise_targets'=>$mktg_catg_wise_targets, 

//                 ]);
//     }

//     // public function create()
//     // {

//     //     $paginate = !empty($request->perpage)?$request->perpage:'10';
//     //     $order_details = 'test';
//     //     $records = DB::table('demand_order')->where('dealer_id',$this->dealer_id)->paginate($paginate);

//     //     $catalog_product_details = DB::table('WWW_WEB_ITEM_DISPLAY')->where('ITEM_STATUS','!=','C')->orderBy('ITEM_CODE','ASC')->get();
//     //     $mktg_cat_array = DB::table('MKTG_CATG_MAST')->orderBy('MKTG_CATG','ASC')->get();


        
//     //     $converstion_unit_item_code = DB::table('ITEM_AUM_MAST')->pluck('UMTOAUM','ITEM_CODE');


//     //     return view('DMS/QuickOrder.create',[
//     //                 'order_details'=> $order_details,
//     //                 'catalog_product_details'=> $catalog_product_details,
//     //                 'current_menu'=>$this->current_menu,
//     //                 'records'=> $records,
//     //                 'converstion_unit_item_code'=> $converstion_unit_item_code,
//     //                 'mktg_cat_array'=> $mktg_cat_array,
//     //                 'auth_id'=>$this->dealer_id

//     //             ]);
//     // }

//     public function store(Request $request)
//     {
//         DB::beginTransaction();
//         $order_date = '';
//         $myArrDetails = array();
//         $item_code = $request->item_code;
//         $product_rate = $request->product_rate;
//         $unit_configuration = $request->unit_configuration;
//         $qty = $request->qty;
//         $free_qty = $request->free_qty;
//         $total_rs = $request->total_rs;
//         $scheme_qty_with_free_qty = $request->scheme_qty_with_free_qty;
//         $remarks = $request->remarks;
//         $final_remarks = $request->final_remarks;
//         $converstion_unit_item_code = DB::table('ITEM_AUM_MAST')->pluck('UMTOAUM','ITEM_CODE');

//         // $dealer_details = 
//         $update_qty_status = $request->update_qty_status;
//         // dd($update_qty_status);
//         if($update_qty_status == '1')
//         {
//             // dd($request);

//             $update_details_cart = [
//                     'quantity'=> $request->update_qty,
//                     'free_qty'=> $request->update_free_qty,
//                     'total_rs'=> $request->update_total_rs
//             ];
//             $update_detailes_cary = DB::table('demand_order_details_cart')
//                                     ->where('product_id',$request->update_product_id)
//                                     ->where('order_id',$request->update_order_id_c)
//                                     ->update($update_details_cart);
//             if($update_detailes_cary)
//             {
//                 DB::commit();
//             }
//         }

//         // else
//         // {

//         // }
//         if($this->role_id == '37'){
//             $depo_dealer_status = 'DO';
//             $sale_div_code = !empty($request->sale_div_code)?$request->sale_div_code:'JH';
//         }
//         elseif($this->role_id == '1')
//         {
//             return redirect()->guest(url($this->current_menu));
//             // return redirect()->intended($this->current_menu);
//         }
//         else
//         {
//             $depo_dealer_status = 'SO';
//             $div_code_entry = DB::table('dealer_person_login')->select('div_code_main as DIV_CODE')->where('dealer_id',$this->dealer_id)->first();
//             // dd($div_code_entry);
//             if(empty($div_code_entry->DIV_CODE))
//             {
//                 $div_code_entry = DB::table('ACC_MAST')->where('ACC_CODE',$this->dealer_code)->first();
//             }
//             $sale_div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:'JH';
           
//         }

//         $order_id = date('YmdHis').$this->auth_id; 

//         $check = DemandOrderCart::where('dealer_id',$this->dealer_id)->first();
//         if(!empty($check) && isset($check))
//         {
//             $order_id = $check->order_id;
//             $uid = $check->id;
//             $order_date = $check->order_date;
//         }
//         else
//         {
//             $myorderArr = [
//                 'order_id' => $order_id, 
//                 'dealer_id' => $this->dealer_id, 
//                 'created_date' => date('Y-m-d H:i:s'), 
//                 'order_date' => date('Y-m-d'), 
//                 'created_person_id' => $this->auth_id, 
//                 'date_time' => date('Y-m-d H:i:s'), 
//                 'company_id' => '1', 
//                 // 'ch_date' => $ch_date, /
//                 // 'challan_no' => '', 
//                 'sale_div_code'=>$sale_div_code,
//                 'csa_id' => $this->csa_id, 
//                 'order_remark' => $final_remarks, 
//                 'total_value' => array_sum($total_rs),
//                 'vr_no_define'=>$depo_dealer_status
//                 // 'status' => $status, 
//                 // 'sync_status' => $sync_status


//             ];
//             $insert_demand_order = DemandOrderCart::create($myorderArr);
//         }
        
//         // dd($insert_demand_order);

//         $uid = !empty($insert_demand_order->id)?$insert_demand_order->id:$uid;
//         if(isset($item_code))
//         {
//             // dd($total_rs);
//             if(!empty($total_rs) && !empty($qty) && isset($total_rs) && isset($qty)  && count($total_rs)>0 &&  count($qty)>0 && count($total_rs)>0 && count($qty)>0  )
//             {
//                 // dd('1');
//                 foreach ($item_code as $key => $value) {
//                     # code...
//                     if(!empty($total_rs[$key]) && !empty($qty[$key]) && isset($total_rs[$key]) && isset($qty[$key])  && isset($total_rs[$key])>0 &&  isset($qty[$key])>0 && isset($total_rs[$key])>0 && isset($qty[$key])>0  )
//                     {
//                         if(!empty($value))
//                         {
//                             $mrp_details = DB::table('ITEM_RATE_MAST')
//                                             ->where('ITEM_CODE',$value)
//                                             ->whereRaw("TO_DATE is NULL")
//                                             ->first();
//                                             // dd($mrp_details);
//                             if($unit_configuration[$key] == 'PCS')
//                             {
//                                 $order_converstion_unit = !empty($qty[$key])?$qty[$key]:'0';
//                             }
//                             else
//                             {
//                                 $converstion_data = DB::table('ITEM_AUM_MAST')
//                                                     ->where('ITEM_CODE',$value)
//                                                     ->first();
//                                 $qty_custom = !empty($qty[$key])?$qty[$key]:'0';
//                                 $uam_toaum = !empty($converstion_data->UMTOAUM)?$converstion_data->UMTOAUM:'0';
//                                 $order_converstion_unit = ($uam_toaum*$qty_custom);
//                             }
//                             $discount = self::common_discount_function($this->dealer_id, $value, $total_rs[$key]);

//                             $myArrDetails[] = [
//                                     'order_id'=>$order_id,
//                                     'product_id' => $value,
//                                     'rate' => !empty($product_rate[$key])?$product_rate[$key]:'',
//                                     'mrp' => !empty($mrp_details->MRP)?$mrp_details->MRP:'0',
//                                     'quantity' => !empty($qty[$key])?(int)$qty[$key]:'0',
//                                     'scheme_qty' => 0,
//                                     'free_qty' => !empty($free_qty[$key])?$free_qty[$key]:'',
//                                     'order_unit' => !empty($unit_configuration[$key])?$unit_configuration[$key]:'', 
//                                     'order_converstion_unit'=>$order_converstion_unit,
//                                     'purchase_inv' => '',
//                                     'mfg_date' => '0000-00-00',
//                                     'expiry_date' => '0000-00-00',
//                                     'receive_date' => '0000-00-00',
//                                     'batch_no' => '',
//                                     'pr_rate' => '',
//                                     'gst' => '',
//                                     'cases' => '',
//                                     'total_rs' => !empty($total_rs[$key])?$total_rs[$key]:'0', 
//                                     'remarks' => !empty($remarks[$key])?$remarks[$key]:'', 
//                                     'scheme_qty_with_free_qty' => !empty($scheme_qty_with_free_qty[$key])?$scheme_qty_with_free_qty[$key]:'0',
//                                     't1_rate' => !empty($discount[1])?$discount[1]:'0',
//                                     'atd_rate' => !empty($discount[0])?$discount[0]:'0',
//                                     't1_rate_perc' => !empty($discount[2])?$discount[2]:'0'

//                             ];
//                         }
//                     }
//                 }   
//             }
            
//         }
        

//         if(count($myArrDetails)>0)
//         {
//             $data_store_details = DB::table('demand_order_details_cart')
//                             ->insert($myArrDetails);

//             if($data_store_details)
//             {
//                 DB::commit();
                

//             }
//             else
//             {
//                 DB::rollback();
//                 return redirect()->guest(url($this->current_menu));
//                 // return redirect()->intended($this->current_menu);
//             }
//         }

        

//         $mktg_cat_array = DB::table('MKTG_CATG_MAST')
//                             ->orderBy('MKTG_CATG','ASC')
//                             ->pluck('MKTG_CATG_NAME as v', 'MKTG_CATG as k');

//         $order_details = DB::table('demand_order_cart')
//                         ->join('dealer','dealer.id','=','demand_order_cart.dealer_id')
//                         ->select('dealer.*')
//                         ->where('demand_order_cart.id',$uid)->first();
//         $dealer_details = DB::table('ACC_MAST')->where('ACC_CODE',$order_details->dealer_code)->first();
//         $final_out = array();

//         foreach ($mktg_cat_array as $key => $value) {
//             $record = DB::table('demand_order_cart')
//                     ->join('demand_order_details_cart','demand_order_details_cart.order_id','=','demand_order_cart.order_id')  
//                     ->join('WWW_WEB_ITEM_DISPLAY','WWW_WEB_ITEM_DISPLAY.ITEM_CODE','=','demand_order_details_cart.product_id')  
//                     ->select('demand_order_cart.*','demand_order_details_cart.*','demand_order_details_cart.id as pid','WWW_WEB_ITEM_DISPLAY.*')
//                     ->where('WWW_WEB_ITEM_DISPLAY.MKTG_CATG', $key)
//                     ->where('demand_order_cart.dealer_id',$this->dealer_id) 
//                     ->where('demand_order_cart.id',$uid) 
//                     ->groupBy('demand_order_details_cart.id')
//                     ->get()
//                     ->toArray();
//             // dd($key);
//             $out_1[$key] = $record;
//             $out_2 = [];
//             foreach ($record as $k => $v) {
//                 //$discount = self::common_discount_function($v->dealer_id, $v->product_id, $v->total_rs);
//                 // dd($discount);
//                 $order_date = $v->order_date;
//                 $out_4s['total_rs'] = $v->total_rs;
//                 $out_4s['pid'] = $v->pid;
//                 $out_4s['order_id'] = $v->order_id;
//                 $out_4s['order_converstion_unit'] = $v->order_converstion_unit;
//                 $out_4s['ITEM_CODE'] = $v->ITEM_CODE;
//                 $out_4s['ITEM_NAME'] = $v->ITEM_NAME;
//                 $out_4s['rate'] = $v->rate;
//                 $out_4s['order_unit'] = $v->order_unit;
//                 $out_4s['quantity'] = $v->quantity;
//                 $out_4s['free_qty'] = $v->free_qty;
//                 $out_4s['remarks'] = $v->remarks;
//                 $out_4s['t1_rate'] = $v->t1_rate;
//                 $out_4s['atd_rate'] = $v->atd_rate;
//                 $out_2[$key][] = $out_4s;
//             }
//             $final_out[] = $out_2;
//         // dd($final_out);
        
//         }

//         $item_aum_mast_array = DB::table('ITEM_AUM_MAST')
//                         ->pluck('UMTOAUM as v', 'ITEM_CODE as k');

//         // dd($request->revert_to_order);
//         // dd($request);

//         if($request->revert_to_order == 1)
//         {
//             return redirect()->guest(url('Order-details/create'));
//             // return redirect()->guest(url($this->current_menu));

//         }
//         // dd($request);

//         return view('DMS/QuickOrder.edit',[
//             'current_menu'=>$this->current_menu,
//             'auth_id'=>$this->dealer_id,
//             'party_name'=> !empty($dealer_details->ACC_NAME)?$dealer_details->ACC_NAME:'',
//             'order_id'=> 'B-'.$uid,
//             'order_id_for_use'=>$uid,
//             'order_date'=> $order_date,
//             'mktg_cat_array'=>$mktg_cat_array,
//             'converstion_unit_item_code'=>$converstion_unit_item_code,
//             'final_out'=>$final_out,
//             'item_aum_mast_array' => $item_aum_mast_array,
//         ]);
//         // return redirect()->intended($this->current_menu);
        
//         // return redirect()->intended($this->current_menu);

        
//     }

//     public function return_cart_data_for_modal(Request $request)
//     {
//     	$mktg_cat_array = DB::table('MKTG_CATG_MAST')
//                             ->orderBy('MKTG_CATG','ASC')
//                             ->pluck('MKTG_CATG_NAME as v', 'MKTG_CATG as k');
//         $converstion_unit_item_code = DB::table('ITEM_AUM_MAST')->pluck('UMTOAUM','ITEM_CODE');

//         $order_details = DB::table('demand_order_cart')
//                         ->join('dealer','dealer.id','=','demand_order_cart.dealer_id')
//                         ->select('dealer.*','demand_order_cart.id as ppid')
//                     	->where('demand_order_cart.dealer_id',$this->dealer_id) 
//                         ->first();
//         // dd($order_details);
//         // if($order_details)
//         $uid = !empty($order_details->ppid)?$order_details->ppid:'';
//         $order_date = '';
//         $final_out = array();
//         if(!empty($uid))
//         {
//         	$dealer_details = DB::table('ACC_MAST')->where('ACC_CODE',$order_details->dealer_code)->first();

//         	foreach ($mktg_cat_array as $key => $value) {
// 	            $record = DB::table('demand_order_cart')
// 	                    ->join('demand_order_details_cart','demand_order_details_cart.order_id','=','demand_order_cart.order_id')  
// 	                    ->join('WWW_WEB_ITEM_DISPLAY','WWW_WEB_ITEM_DISPLAY.ITEM_CODE','=','demand_order_details_cart.product_id')  
// 	                    ->select('demand_order_cart.*','demand_order_details_cart.*','demand_order_details_cart.id as pid','WWW_WEB_ITEM_DISPLAY.*')
// 	                    ->where('WWW_WEB_ITEM_DISPLAY.MKTG_CATG', $key)
// 	                    ->where('demand_order_cart.dealer_id',$this->dealer_id) 
// 	                    // ->where('demand_order_cart.id',$uid) 
// 	                    ->groupBy('demand_order_details_cart.id')
// 	                    ->get()
// 	                    ->toArray();
// 	            // dd($key);
// 	            $out_1[$key] = $record;
// 	            $out_2 = [];
// 	            foreach ($record as $k => $v) {
// 	                //$discount = self::common_discount_function($v->dealer_id, $v->product_id, $v->total_rs);
// 	                // dd($discount);
// 	                $order_date = $v->order_date;
// 	                $out_4s['total_rs'] = $v->total_rs;
// 	                $out_4s['pid'] = $v->pid;
// 	                $out_4s['order_id'] = $v->order_id;
// 	                $out_4s['order_converstion_unit'] = $v->order_converstion_unit;
// 	                $out_4s['ITEM_CODE'] = $v->ITEM_CODE;
// 	                $out_4s['ITEM_NAME'] = $v->ITEM_NAME;
// 	                $out_4s['rate'] = $v->rate;
// 	                $out_4s['order_unit'] = $v->order_unit;
// 	                $out_4s['quantity'] = $v->quantity;
// 	                $out_4s['free_qty'] = $v->free_qty;
// 	                $out_4s['remarks'] = $v->remarks;
// 	                $out_4s['t1_rate'] = $v->t1_rate;
// 	                $out_4s['atd_rate'] = $v->atd_rate;
// 	                $out_2[$key][] = $out_4s;
// 	            }
// 	            $final_out[] = $out_2;
// 	        // dd($final_out);
	        
// 	        }
//         }
        
//         $item_aum_mast_array = DB::table('ITEM_AUM_MAST')
//                         ->pluck('UMTOAUM as v', 'ITEM_CODE as k');

//         // dd($request->revert_to_order);
//         if($request->revert_to_order == 1)
//         {
//             return redirect()->guest(url('Order-details/create'));
//             // return redirect()->intended('Order-details/create');

//         }
//         return view('DMS/QuickOrder.cartcommonmodal',[
//             'current_menu'=>$this->current_menu,
//             'auth_id'=>$this->dealer_id,
//             'party_name'=> !empty($dealer_details->ACC_NAME)?$dealer_details->ACC_NAME:'',
//             'order_id'=> 'B-'.$uid,
//             'order_id_for_use'=>$uid,
//             'order_date'=> $order_date,
//             'mktg_cat_array'=>$mktg_cat_array,
//             'converstion_unit_item_code'=>$converstion_unit_item_code,
//             'final_out'=>$final_out,
//             'item_aum_mast_array' => $item_aum_mast_array,
//         ]);
//     }
//     public function store_final(Request $request)
//     {
//         // dd($request);
//         $order_id_for_use = $request->order_id_for_use;


//         $data_use_step1 = DB::table('demand_order_cart')
//                         ->select('demand_order_cart.*')
//                         ->where('id',$order_id_for_use)
//                         ->first();

//         $data_use_step2 = DB::table('demand_order_details_cart')
//                         ->join('demand_order_cart','demand_order_cart.order_id','=','demand_order_details_cart.order_id')
//                         ->select('demand_order_details_cart.*')
//                         ->where('demand_order_cart.id',$order_id_for_use)
//                         ->groupBy('demand_order_details_cart.id')
//                         ->get();

//         $myArrDetails = array();
//         // $item_code = $data_use_step1->item_code;
//         // $product_rate = $data_use_step1->product_rate;
//         // $unit_configuration = $data_use_step1->unit_configuration;
//         // $qty = $data_use_step1->qty;
//         // $free_qty = $data_use_step1->free_qty;
//         // $total_rs = $data_use_step1->total_rs;
//         // $scheme_qty_with_free_qty = $data_use_step1->scheme_qty_with_free_qty;
//         // $remarks = $data_use_step1->remarks;
//         $final_remarks = !empty($data_use_step1->order_remark)?$data_use_step1->order_remark:'';
//         $sale_div_code = !empty($data_use_step1->sale_div_code)?$data_use_step1->sale_div_code:'';

//         // $dealer_details = 
//         if($this->role_id == '37'){
//             $depo_dealer_status = 'DO';
//             // $sale_div_code = 'JH';
//         }
//         elseif($this->role_id == '1')
//         {
//             return redirect()->guest(url($this->current_menu));
//             // return redirect()->intended($this->current_menu);
//         }
//         else
//         {
//             $depo_dealer_status = 'SO';
//             // $div_code_entry = DB::table('ACC_MAST')->where('ACC_CODE',$this->dealer_code)->first();
//             // $sale_div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:'JH';

//         }
//         $order_id = date('YmdHis').$this->auth_id; 
        

//         $total_rs_arr = [];
//         // dd('1');
//         foreach ($data_use_step2 as $key => $value) {
//             # code...
//             // $discount = self::common_discount_function($this->dealer_id, $value, $total_rs[$key]);
//             $total_rs_arr[]  = $value->total_rs;
//             $myArrDetails[] = [
//                     'order_id'=>$order_id,
//                     'product_id' => $value->product_id,
//                     'rate' => $value->rate, 
//                     'mrp' => $value->mrp, 
//                     'quantity' => $value->quantity, 
//                     'scheme_qty' => $value->scheme_qty, 
//                     'free_qty' => $value->free_qty, 
//                     'order_unit' => $value->order_unit, 
//                     'order_converstion_unit'=>$value->order_converstion_unit,
//                     'purchase_inv' => $value->purchase_inv, 
//                     'mfg_date' => $value->mfg_date, 
//                     'expiry_date' => $value->expiry_date, 
//                     'receive_date' => $value->receive_date, 
//                     'batch_no' => $value->batch_no, 
//                     'pr_rate' => $value->pr_rate, 
//                     'gst' => $value->gst, 
//                     'cases' => $value->cases, 
//                     'total_rs' => $value->total_rs, 
//                     'remarks' => $value->remarks, 
//                     'scheme_qty_with_free_qty' => $value->scheme_qty_with_free_qty, 
//                     't1_rate' => $value->t1_rate, 
//                     'atd_rate' => $value->atd_rate, 
//                     't1_rate_perc' => $value->t1_rate_perc

//             ];
              
//         }   
           
        

//         if(count($myArrDetails)>0)
//         {
//         	DB::beginTransaction();

//             $data_store_details = DB::table('demand_order_details')
//                             ->insert($myArrDetails);

//             if($data_store_details)
//             {

//                 $myorderArr = [
//                     'order_id' => $order_id, 
//                     'dealer_id' => $this->dealer_id, 
//                     'created_date' => date('Y-m-d H:i:s'), 
//                     'order_date' => date('Y-m-d'), 
//                     'created_person_id' => $this->auth_id, 
//                     'date_time' => date('Y-m-d H:i:s'), 
//                     'company_id' => '1', 
//                     // 'ch_date' => $ch_date, /
//                     // 'challan_no' => '', 
//                     'sale_div_code'=>$sale_div_code,
//                     'csa_id' => $this->csa_id, 
//                     'order_remark' => $final_remarks, 
//                     'total_value' => array_sum($total_rs_arr),
//                     'vr_no_define'=>$depo_dealer_status
//                     // 'status' => $status, 
//                     // 'sync_status' => $sync_status


//                 ];
//                 $insert_demand_order = DB::table('demand_order')
//                                     ->insert($myorderArr);


//                 if($insert_demand_order)
//                 {
//                     $delete_2 = DB::table('demand_order_details_cart')
//                             ->join('demand_order_cart','demand_order_cart.order_id','=','demand_order_details_cart.order_id')
//                             ->where('demand_order_cart.id',$order_id_for_use)
//                             ->delete();

//                     $delete_1 = DB::table('demand_order_cart')
//                         ->where('id',$order_id_for_use)
//                         ->delete();

                    
//                     DB::commit();
//                     $pdf = self::dmsforpdf2($order_id);
//                     return redirect()->guest(url($this->current_menu));
// 	            	// return redirect()->intended($this->current_menu);

//                 }
//                 else
//                 {
//                     DB::rollback();
// 			        // return redirect()->intended($this->current_menu);

//                     return redirect()->guest(url($this->current_menu));
// 	            	// return redirect()->intended($this->current_menu);

//                 }
//                     return redirect()->guest(url($this->current_menu));
//                 // return redirect()->intended($this->current_menu);

//             }
//                     return redirect()->guest(url($this->current_menu));
//         	// return redirect()->intended($this->current_menu);

//         }
//                     return redirect()->guest(url($this->current_menu));
//         // return redirect()->intended($this->current_menu);
        

        
//     }

//     // public function edit($id)
//     // {
//     //     $uid = Crypt::decryptString($id);

     

//     //     $mktg_cat_array = DB::table('MKTG_CATG_MAST')
//     //                         ->orderBy('MKTG_CATG','ASC')
//     //                         ->pluck('MKTG_CATG_NAME as val', 'MKTG_CATG as id');
//     //     $final_out = array();
//     //     foreach ($mktg_cat_array as $key => $value) {
//     //         $record = DB::table('demand_order')
//     //                 ->join('demand_order_details','demand_order_details.order_id','=','demand_order.order_id')  
//     //                 ->join('WWW_WEB_ITEM_DISPLAY','WWW_WEB_ITEM_DISPLAY.ITEM_CODE','=','demand_order_details.product_id')  
//     //                 ->where('WWW_WEB_ITEM_DISPLAY.MKTG_CATG', $key)
//     //                 ->where('demand_order.dealer_id',$this->dealer_id) 
//     //                 ->where('demand_order.order_id',$uid) 
//     //                 ->groupBy('demand_order_details.product_id')
//     //                 ->get()
//     //                 ->toArray();
//     //         $out_1[$key] = $record;
//     //     }
//     //     $final_out[] = $out_1;
//     //     return view('DMS/QuickOrder.edit',[
//     //         'current_menu'=>$this->current_menu,
//     //         'auth_id'=>$this->dealer_id,
//     //         'mktg_cat_array'=>$mktg_cat_array,
//     //         'final_out'=>$final_out
//     //     ]); 
//     // }
//     // public function edit($id)
//     // {
//     //     $uid = Crypt::decryptString($id);

//     //     $mktg_cat_array = DB::table('MKTG_CATG_MAST')
//     //                         ->orderBy('MKTG_CATG','ASC')
//     //                         ->pluck('MKTG_CATG_NAME as v', 'MKTG_CATG as k');
//     //     $final_out = array();
//     //     foreach ($mktg_cat_array as $key => $value) {
//     //         $record = DB::table('demand_order')
//     //                 ->join('demand_order_details','demand_order_details.order_id','=','demand_order.order_id')  
//     //                 ->join('WWW_WEB_ITEM_DISPLAY','WWW_WEB_ITEM_DISPLAY.ITEM_CODE','=','demand_order_details.product_id')  
//     //                 ->where('WWW_WEB_ITEM_DISPLAY.MKTG_CATG', $key)
//     //                 ->where('demand_order.dealer_id',$this->dealer_id) 
//     //                 ->where('demand_order.order_id',$uid) 
//     //                 ->groupBy('demand_order_details.product_id')
//     //                 ->get()
//     //                 ->toArray();
//     //         $out_1[$key] = $record;
//     //     }
//     //     $final_out[] = $out_1;
//     //     $item_aum_mast_array = DB::table('ITEM_AUM_MAST')
//     //                         ->pluck('UMTOAUM as v', 'ITEM_CODE as k');
//     //     return view('DMS/QuickOrder.edit',[
//     //         'current_menu'=>$this->current_menu,
//     //         'auth_id'=>$this->dealer_id,
//     //         'mktg_cat_array'=>$mktg_cat_array,
//     //         'final_out'=>$final_out,
//     //         'item_aum_mast_array' => $item_aum_mast_array
//     //     ]);
//     // }
//     public function edit($id)
//     {
//         $uid = Crypt::decryptString($id);
//         $order_date= '';
//         $converstion_unit_item_code = DB::table('ITEM_AUM_MAST')->groupBy('ITEM_CODE')->pluck('UMTOAUM','ITEM_CODE');

//         $mktg_cat_array = DB::table('MKTG_CATG_MAST')
//                             ->orderBy('MKTG_CATG','ASC')
//                             ->pluck('MKTG_CATG_NAME as v', 'MKTG_CATG as k');

//         $order_details = DB::table('demand_order')
//                         ->join('dealer','dealer.id','=','demand_order.dealer_id')
//                         ->select('dealer.*','demand_order.order_remark as order_remark')
//                         ->where('demand_order.id',$uid)->first();
//         $dealer_details = DB::table('ACC_MAST')->where('ACC_CODE',$order_details->dealer_code)->first();

//         $div_code_entry = DB::table('ACC_MAST')->where('ACC_CODE',$this->dealer_code)->first();
//         // dd($div_code_entry);
//         // $div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:'0';
//         if($this->dealer_code == '00001')
//         {
//             $div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:'JH';

//         }
//         else
//         {
//             $div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:'0';
//         }

//         $final_out = array();
//         foreach ($mktg_cat_array as $key => $value) {
//             $record_data = DB::table('demand_order')
//                     ->join('demand_order_details','demand_order_details.order_id','=','demand_order.order_id')  
//                     ->join('WWW_WEB_ITEM_DISPLAY','WWW_WEB_ITEM_DISPLAY.ITEM_CODE','=','demand_order_details.product_id')  
//                     ->where('WWW_WEB_ITEM_DISPLAY.MKTG_CATG', $key)
//                     // ->where('demand_order.dealer_id',$this->dealer_id) 
//                     ->where('demand_order.id',$uid) 
//                     ->groupBy('demand_order_details.id');
//                  //    if($this->role_id == 37)
// 	                // {

// 	                //     $record_data->join('dealer','dealer.id','=','demand_order.dealer_id')
// 	                //                 ->join('ACC_MAST','ACC_MAST.ACC_CODE','=','dealer.dealer_code')
// 	                //                 ->where('sale_div_code',$div_code);
// 	                // }
// 	                // else
// 	                // {
// 	                //     $record_data->where('demand_order.dealer_id',$this->dealer_id);
// 	                // }
//             $record = $record_data->get()->toArray();
//             // dd($key);
//             $out_1[$key] = $record;
//             $out_2 = [];
//             foreach ($record as $k => $v) {
//                 //$discount = self::common_discount_function($v->dealer_id, $v->product_id, $v->total_rs);
//                 // dd($discount);
//                 $order_date = $v->order_date;
//                 $out_4s['total_rs'] = $v->total_rs;
//                 $out_4s['order_converstion_unit'] = $v->order_converstion_unit;
//                 $out_4s['ITEM_CODE'] = $v->ITEM_CODE;
//                 $out_4s['ITEM_NAME'] = $v->ITEM_NAME;
//                 $out_4s['rate'] = $v->rate;
//                 $out_4s['order_unit'] = $v->order_unit;
//                 $out_4s['quantity'] = $v->quantity;
//                 $out_4s['free_qty'] = $v->free_qty;
//                 $out_4s['remarks'] = $v->remarks;
//                 $out_4s['t1_rate'] = $v->t1_rate;
//                 $out_4s['atd_rate'] = $v->atd_rate;
//                 $out_2[$key][] = $out_4s;
//             }
//             $final_out[] = $out_2;
//         // dd($final_out);
        
//         }
//         $item_aum_mast_array = DB::table('ITEM_AUM_MAST')
//                         ->pluck('UMTOAUM as v', 'ITEM_CODE as k');
//         return view('DMS/QuickOrder.perfoma',[
//             'current_menu'=>$this->current_menu,
//             'auth_id'=>$this->dealer_id,
//             'party_name'=> !empty($dealer_details->ACC_NAME)?$dealer_details->ACC_NAME:'',
//             'order_remark'=> !empty($order_details->order_remark)?$order_details->order_remark:'',
//             'order_id'=> 'B-'.$uid,
//             'order_date'=> $order_date,
//             'mktg_cat_array'=>$mktg_cat_array,
//             'final_out'=>$final_out,
//             'converstion_unit_item_code'=> $converstion_unit_item_code,
//             'item_aum_mast_array' => $item_aum_mast_array,
//         ]);
//     }

//     public function dmsforpdf(Request $request)
//     {
//     	$uid = $request->order_id;
//     	$pdf = self::dmsforpdf2($uid);
//     	// dd($pdf);
//     }
//     // public function dmsforpdf($id)
//     public function dmsforpdf2($uid)
//     {
//         // $uid = Crypt::decryptString($id);
//         $uid = $uid;
//         $order_date= '';
//         $converstion_unit_item_code = DB::table('ITEM_AUM_MAST')->pluck('UMTOAUM','ITEM_CODE');

//         $mktg_cat_array = DB::table('MKTG_CATG_MAST')
//                             ->orderBy('MKTG_CATG','ASC')
//                             ->pluck('MKTG_CATG_NAME as v', 'MKTG_CATG as k');

//         $order_details = DB::table('demand_order')
//                         ->join('dealer','dealer.id','=','demand_order.dealer_id')
//                         ->select('dealer.*','demand_order.order_remark as order_remark','demand_order.id as pid')
//                         ->where('demand_order.order_id',$uid)->first();
//         $dealer_details = DB::table('ACC_MAST')->where('ACC_CODE',$order_details->dealer_code)->first();
//         $dealer_details_for_maail = DB::table('dealer')->where('dealer_code',$order_details->dealer_code)->first();
//         $dealer_details_for_maail_f = !empty($dealer_details_for_maail->email)?$dealer_details_for_maail->email:'';
//         $div_code_entry = DB::table('ACC_MAST')->where('ACC_CODE',$this->dealer_code)->first();
//         // dd($div_code_entry);
//         $div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:'0';

//         $final_out = array();
//         foreach ($mktg_cat_array as $key => $value) {
//             $record_data = DB::table('demand_order')
//                     ->join('demand_order_details','demand_order_details.order_id','=','demand_order.order_id')  
//                     ->join('WWW_WEB_ITEM_DISPLAY','WWW_WEB_ITEM_DISPLAY.ITEM_CODE','=','demand_order_details.product_id')  
//                     ->where('WWW_WEB_ITEM_DISPLAY.MKTG_CATG', $key)
//                     // ->where('demand_order.dealer_id',$this->dealer_id) 
//                     ->where('demand_order.order_id',$uid) 
//                     ->groupBy('demand_order_details.id');
//                  //    if($this->role_id == 37)
// 	                // {

// 	                //     $record_data->join('dealer','dealer.id','=','demand_order.dealer_id')
// 	                //                 ->join('ACC_MAST','ACC_MAST.ACC_CODE','=','dealer.dealer_code')
// 	                //                 ->where('sale_div_code',$div_code);
// 	                // }
// 	                // else
// 	                // {
// 	                //     $record_data->where('demand_order.dealer_id',$this->dealer_id);
// 	                // }
//             $record = $record_data->get()->toArray();
//             // dd($key);
//             $out_1[$key] = $record;
//             $out_2 = [];
//             foreach ($record as $k => $v) {
//                 //$discount = self::common_discount_function($v->dealer_id, $v->product_id, $v->total_rs);
//                 // dd($discount);
//                 $order_date = $v->order_date;
//                 $out_4s['total_rs'] = $v->total_rs;
//                 $out_4s['order_converstion_unit'] = $v->order_converstion_unit;
//                 $out_4s['ITEM_CODE'] = $v->ITEM_CODE;
//                 $out_4s['ITEM_NAME'] = $v->ITEM_NAME;
//                 $out_4s['rate'] = $v->rate;
//                 $out_4s['order_unit'] = $v->order_unit;
//                 $out_4s['quantity'] = $v->quantity;
//                 $out_4s['free_qty'] = $v->free_qty;
//                 $out_4s['remarks'] = $v->remarks;
//                 $out_4s['t1_rate'] = $v->t1_rate;
//                 $out_4s['atd_rate'] = $v->atd_rate;
//                 $out_2[$key][] = $out_4s;
//             }
//             $final_out[] = $out_2;
//         // dd($final_out);
        
//         }
//         $item_aum_mast_array = DB::table('ITEM_AUM_MAST')
//                         ->pluck('UMTOAUM as v', 'ITEM_CODE as k');
//         // return view('DMS/QuickOrder.perfoma',[
//         //     'current_menu'=>$this->current_menu,
//         //     'auth_id'=>$this->dealer_id,
//         //     'party_name'=> !empty($dealer_details->ACC_NAME)?$dealer_details->ACC_NAME:'',
//         //     'order_remark'=> !empty($order_details->order_remark)?$order_details->order_remark:'',
//         //     'order_id'=> 'B-'.$uid,
//         //     'order_date'=> $order_date,
//         //     'mktg_cat_array'=>$mktg_cat_array,
//         //     'final_out'=>$final_out,
//         //     'converstion_unit_item_code'=> $converstion_unit_item_code,
//         //     'item_aum_mast_array' => $item_aum_mast_array,
//         // ]);
//         $subject = !empty($request->subject)?$request->subject:'Report';
//         // dd();
//         $acc_dealer_details_EMAIL = !empty($dealer_details->EMAIL)?$dealer_details->EMAIL:'';
//         $mail_id = !empty($dealer_details_for_maail_f)?$dealer_details_for_maail_f:$acc_dealer_details_EMAIL;
//         // $mail_id = 'karan@manacleindia.com';    
//         // dd($mail_id);    
//         $mail = Mail::send('DMS/QuickOrder/perfoma_pdf', array(
//             'current_menu'=>$this->current_menu,
//             'auth_id'=>$this->dealer_id,
//             'party_name'=> !empty($dealer_details->ACC_NAME)?$dealer_details->ACC_NAME:'',
//             'order_remark'=> !empty($order_details->order_remark)?$order_details->order_remark:'',
//             'order_id'=> 'B-'.$order_details->pid,
//             'order_date'=> $order_date,
//             'mktg_cat_array'=>$mktg_cat_array,
//             'final_out'=>$final_out,
//             'converstion_unit_item_code'=> $converstion_unit_item_code,
//             'item_aum_mast_array' => $item_aum_mast_array,
//         ) , function($message) use($mail_id,$subject)
//         {
                                    
//             $message->from('manacle.php1@gmail.com');

//             $message->to($mail_id)->subject($subject);

//         });

//         // $customPaper = array(0, 0, 900, 1000);

//         // $order_id = 'B-'.$uid;
//         // $pdf_name = $order_id.'.pdf';
//         // $pdf = PDF::loadView('DMS/QuickOrder/perfoma_pdf',[
//         //  	'current_menu'=>$this->current_menu,
//         //     'auth_id'=>$this->dealer_id,
//         //     'party_name'=> !empty($dealer_details->ACC_NAME)?$dealer_details->ACC_NAME:'',
//         //     'order_remark'=> !empty($order_details->order_remark)?$order_details->order_remark:'',
//         //     'order_id'=> 'B-'.$uid,
//         //     'order_date'=> $order_date,
//         //     'mktg_cat_array'=>$mktg_cat_array,
//         //     'final_out'=>$final_out,
//         //     'converstion_unit_item_code'=> $converstion_unit_item_code,
//         //     'item_aum_mast_array' => $item_aum_mast_array,
//         // ]);
//         // $pdf->setPaper($customPaper);
//         // $pdf->save(public_path('pdf/'.$pdf_name));
//         // $pdf_path = public_path() . '/pdf/' .$pdf_name;
//         return true;

//     }

//     public function common_discount_function($dealer_id, $product_id,$total_rs){
//             // dd('yo');
//             $dealer_data = DB::table('dealer')->where('id',$dealer_id)->first();
//             $dealer_code = !empty($dealer_data->dealer_code)?$dealer_data->dealer_code:'0';
//             // $dealer_code = '20602';
//             $dealer_data_DETAILS = DB::table('ACC_MAST')->where('ACC_CODE',$dealer_code)->first();
//             // $div_code = ''
//             // dd($dealer_data_DETAILS);
//             if(empty($dealer_data_DETAILS) || empty($dealer_code))
//             {
//                 return 0;
//                 // die;
//             }
//             if(empty($dealer_code) && empty($dealer_data_DETAILS))
//             {
//                 return 0;
//             }
//             $product_id = $product_id;
//             $total_rs = $total_rs;

//             $final_tr_rate = "0";
//             // for state wise tax code starts here 
//                 $stax_rate_discount = DB::table('PROD_TAX_MAST')
//                                     ->where('STATE_CODE',$dealer_data_DETAILS->STATE_CODE)
//                                     ->orderBy('PROD_TAX_MAST.id','DESC')
//                                     ->get();
//                 // dd($stax_rate_discount);
//                 if(!empty($stax_rate_discount) && COUNT($stax_rate_discount)>0)
//                 {
//                     foreach ($stax_rate_discount as $s_key => $s_value) {
//                         # code...
//                         // dd($value);
//                         if($s_value->TO_DATE == '' || $s_value->TO_DATE == ' ' || $s_value->TO_DATE == 'NULL' || $s_value->TO_DATE == NULL)
//                         {
//                             // dd('q1')
//                             $prod_code_stax[] = $s_value->PROD_CODE;
//                             $prod_catg_stax[] = $s_value->PROD_CATG;
//                             $price_list_catg_stax[] = $s_value->PRICE_LIST_CATG;
//                             if($s_value->PRICE_LIST_CATG == ' ' && $s_value->PROD_CATG == ' ' && $s_value->PROD_CODE == ' ' )
//                             {
//                                 // dd()
//                                 if($s_value->PRICE_LIST_CATG == '' && $s_value->PROD_CATG == '' && $s_value->PROD_CODE == '')
//                                 {
//                                     $final_stax_discount = $s_value->STAX_RATE;
//                                 }
//                                 else
//                                 {
//                                     $final_stax_discount = $s_value->STAX_RATE;
//                                 }
//                             }
//                             else
//                             {
//                                 if($s_value->PRICE_LIST_CATG == '' && $s_value->PROD_CATG == '' && $s_value->PROD_CODE == '')
//                                 {
//                                     $final_stax_discount = $s_value->STAX_RATE;
//                                 }
//                             }
//                         }
//                     }
//                     // dd($final_stax_discount);
//                     $details_of_stax_discount = DB::table('PROD_TAX_MAST')
//                                                 ->join('ITEM_MAST','ITEM_MAST.PROD_CODE','=','PROD_TAX_MAST.PROD_CODE')
//                                                 ->where('ITEM_MAST.ITEM_CODE',$product_id)
//                                                 ->where('STATE_CODE',$dealer_data_DETAILS->STATE_CODE)
//                                                 ->whereIn('PROD_TAX_MAST.PROD_CODE',$prod_code_stax)
//                                                 ->first();
//                         if(!empty($details_of_stax_discount))
//                         {
//                             $final_stax_discount = $details_of_stax_discount->STAX_RATE;

//                         }
//                         else
//                         {
//                             $details_of_stax_discount = DB::table('PROD_TAX_MAST')
//                                                 ->join('ITEM_MAST','ITEM_MAST.PROD_CATG','=','PROD_TAX_MAST.PROD_CATG')
//                                                 ->where('ITEM_MAST.ITEM_CODE',$product_id)
//                                                 ->where('STATE_CODE',$dealer_data_DETAILS->STATE_CODE)
//                                                 ->whereIn('PROD_TAX_MAST.PROD_CODE',$prod_catg_stax)
//                                                 ->first();
//                                 if(!empty($details_of_stax_discount))
//                                 {
//                                     $final_stax_discount = $details_of_stax_discount->STAX_RATE;
//                                 }
//                                 else
//                                 {
//                                     $details_of_stax_discount = DB::table('PROD_TAX_MAST')
//                                                 ->join('PROD_MAST','PROD_MAST.PRICE_LIST_CATG','=','PROD_TAX_MAST.PRICE_LIST_CATG')
//                                                 ->join('ITEM_MAST','ITEM_MAST.PROD_CODE','=','PROD_MAST.PROD_CODE')
//                                                 ->where('ITEM_MAST.ITEM_CODE',$product_id)
//                                                 ->where('STATE_CODE',$dealer_data_DETAILS->STATE_CODE)
//                                                 ->whereIn('PROD_TAX_MAST.PROD_CODE',$price_list_catg_stax)
//                                                 ->first();
//                                         if(!empty($details_of_stax_discount))
//                                         {
//                                             $final_stax_discount = $details_of_stax_discount->STAX_RATE;
//                                         }
//                                         else
//                                         {
//                                             $final_stax_discount = !empty($final_stax_discount)?$final_stax_discount:'0';
//                                         }
//                                 }
//                         }

//                 }
//                 else
//                 {
//                     $final_stax_discount = 0;
//                 }
//             // stax code ends here 


//             // for dealer bill tax code starts here 
//                 $tr_rate_discount = DB::table('DEALER_BILLTAX_MAST')
//                                     ->where('ACC_CODE',$dealer_code)
//                                     ->orderBy('DEALER_BILLTAX_MAST.id','DESC')
//                                     ->get();

//                 // if($tr_rate_discount)
//                 if(!empty($tr_rate_discount) && COUNT($tr_rate_discount)>0)
//                 {
//                     foreach ($tr_rate_discount as $d_key => $d_value) {
//                         # code...
//                         // dd($d_value);
//                         if($d_value->TO_DATE == '' || $d_value->TO_DATE == ' ' || $d_value->TO_DATE == 'NULL' || $d_value->TO_DATE == NULL)
//                         {
//                             $prod_code[] = $d_value->PROD_CODE;
//                             $prod_catg[] = $d_value->PROD_CATG;
//                             $price_list_catg[] = $d_value->PRICE_LIST_CATG;
//                             if($d_value->PRICE_LIST_CATG == ' ' && $d_value->PROD_CATG == ' ' && $d_value->PROD_CODE == ' ' )
//                             {
//                                 if($d_value->PRICE_LIST_CATG == '' && $d_value->PROD_CATG == '' && $d_value->PROD_CODE == '')
//                                 {
//                                     $final_tr_rate = $d_value->TI_RATE;

//                                 }   
//                                 else
//                                 {
//                                     $final_tr_rate = $d_value->TI_RATE;

//                                 }
//                             }
//                             else
//                             {
//                                 if($d_value->PRICE_LIST_CATG == '' && $d_value->PROD_CATG == '' && $d_value->PROD_CODE == '')
//                                 {
//                                     $final_tr_rate = $d_value->TI_RATE;

//                                 }
//                             }
//                         }
//                     }
//                     // dd($value);
//                             $tr_rate_discount_inn = DB::table('DEALER_BILLTAX_MAST')
//                                     // ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','DEALER_BILLTAX_MAST.PROD_CODE')
//                                     ->join('ITEM_MAST','ITEM_MAST.PROD_CODE','=','DEALER_BILLTAX_MAST.PROD_CODE')
//                                     ->where('ITEM_MAST.ITEM_CODE',$product_id)
//                                     ->where('ACC_CODE',$dealer_code)
//                                     ->whereIn('DEALER_BILLTAX_MAST.PROD_CODE',$prod_code)
//                                     ->orderBy('DEALER_BILLTAX_MAST.id','DESC')
//                                     ->first();
//                             if(!empty($tr_rate_discount_inn))
//                             {
//                                 $final_tr_rate = $tr_rate_discount_inn->TI_RATE;
//                             }
//                             else
//                             {
//                                 $tr_rate_discount_inn = DB::table('DEALER_BILLTAX_MAST')
//                                     // ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','DEALER_BILLTAX_MAST.PROD_CODE')
//                                     ->join('ITEM_MAST','ITEM_MAST.PROD_CATG','=','DEALER_BILLTAX_MAST.PROD_CATG')
//                                     ->where('ITEM_MAST.ITEM_CODE',$product_id)
//                                     ->whereIn('DEALER_BILLTAX_MAST.PROD_CATG',$prod_catg)
//                                     ->where('ACC_CODE',$dealer_code)
//                                     ->orderBy('DEALER_BILLTAX_MAST.id','DESC')
//                                     ->first();
//                                 if(!empty($tr_rate_discount_inn))
//                                 {
//                                     $final_tr_rate = $tr_rate_discount_inn->TI_RATE;
//                                     // dd('n');
//                                 }
//                                 else
//                                 {
//                                     $tr_rate_discount_inn = DB::table('DEALER_BILLTAX_MAST')
//                                                     ->join('PROD_MAST','PROD_MAST.PRICE_LIST_CATG','=','DEALER_BILLTAX_MAST.PRICE_LIST_CATG')
//                                                     ->join('ITEM_MAST','ITEM_MAST.PROD_CODE','=','PROD_MAST.PROD_CODE')
//                                                     ->where('ITEM_MAST.ITEM_CODE',$product_id)
//                                                     ->whereIn('DEALER_BILLTAX_MAST.PRICE_LIST_CATG',$price_list_catg)
//                                                     ->where('ACC_CODE',$dealer_code)
//                                                     ->orderBy('DEALER_BILLTAX_MAST.id','DESC')
//                                                     ->first();
//                                     if(!empty($tr_rate_discount_inn))
//                                     {
//                                         $final_tr_rate = $tr_rate_discount_inn->TI_RATE;
//                                         // dd('1');
//                                     }
//                                     // else
//                                     // {
//                                     //     $final_tr_rate = $d_value->TI_RATE;

//                                     // }
//                                 }
//                             }
//                     //     }

//                     // }
//                 }
//                 else
//                 {
//                     $tr_rate_discount = DB::table('DEALER_BILLTAX_MAST')
//                                     // ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','DEALER_BILLTAX_MAST.PROD_CODE')
//                                     ->join('ITEM_MAST','ITEM_MAST.PROD_CODE','=','DEALER_BILLTAX_MAST.PROD_CODE')
//                                     ->where('ITEM_MAST.ITEM_CODE',$product_id)
//                                     ->where('ACC_CODE','')
//                                     ->orderBy('DEALER_BILLTAX_MAST.id','ASC')
//                                     ->get();

//                     if(!empty($tr_rate_discount) && COUNT($tr_rate_discount)>0)
//                     {
//                         foreach ($tr_rate_discount as $d_key => $d_value) {
//                             if($d_value->TO_DATE == '' || $d_value->TO_DATE == ' ' || $d_value->TO_DATE == 'NULL' || $d_value->TO_DATE == NULL)
//                             {
//                                 $final_tr_rate = $d_value->TI_RATE;       
//                             }
//                         }
//                         // $final_tr_rate = array_sum($final_tr_rate_code);

//                     }
//                     else
//                     {
//                         $tr_rate_discount = DB::table('DEALER_BILLTAX_MAST')
//                                     // ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','DEALER_BILLTAX_MAST.PROD_CODE')
//                                     ->join('ITEM_MAST','ITEM_MAST.PROD_CODE','=','DEALER_BILLTAX_MAST.PROD_CATG')
//                                     ->where('ITEM_MAST.ITEM_CODE',$product_id)
//                                     ->where('ACC_CODE','')
//                                     ->orderBy('DEALER_BILLTAX_MAST.id','ASC')
//                                     ->get();

//                         if(!empty($tr_rate_discount) && COUNT($tr_rate_discount)>0)
//                         {
//                             foreach ($tr_rate_discount as $d_key => $d_value) {
//                                 if($d_value->TO_DATE == '' || $d_value->TO_DATE == ' ' || $d_value->TO_DATE == 'NULL' || $d_value->TO_DATE == NULL)
//                                 {
//                                     $final_tr_rate = $d_value->TI_RATE;       
//                                 }
//                             }

//                         }
//                         else
//                         {
//                             $tr_rate_discount = DB::table('DEALER_BILLTAX_MAST')
//                                         ->join('PROD_MAST','PROD_MAST.PRICE_LIST_CATG','=','DEALER_BILLTAX_MAST.PRICE_LIST_CATG')
//                                         ->join('ITEM_MAST','ITEM_MAST.PROD_CODE','=','PROD_MAST.PROD_CODE')
//                                         ->where('ITEM_MAST.ITEM_CODE',$product_id)
//                                         ->where('ACC_CODE','')
//                                         ->orderBy('DEALER_BILLTAX_MAST.id','ASC')
//                                         ->get();

//                             if(!empty($tr_rate_discount) && COUNT($tr_rate_discount)>0)
//                             {
//                                 foreach ($tr_rate_discount as $d_key => $d_value) {
//                                     if($d_value->TO_DATE == '' || $d_value->TO_DATE == ' ' || $d_value->TO_DATE == 'NULL' || $d_value->TO_DATE == NULL)
//                                     {
//                                         $final_tr_rate = $d_value->TI_RATE;       
//                                     }
//                                 }

//                             }
//                         }
//                     }
//                 }

//             //dealer tax ends here 

//             // dd($value->total_rs);
//             $out = [];

//             $gst_rate = !empty($final_stax_discount)?$final_stax_discount:'0';
//             $atd_rate = (($gst_rate*100)/($gst_rate+100)); // atd discound
//             // dd($final_tr_rate);
//             $final_tr_rate = !empty($final_tr_rate)?$final_tr_rate:'0';
//             $step_1 = ($total_rs*$final_tr_rate);

//             $td_discount = ((($step_1)/100)); // 244
//             $atd_discount = round((((($total_rs-$td_discount)*$atd_rate)/100)),2); // 435

// // dd($td_discount);
// // 
//             // $td_discount = $final_tr_rate;

//             // dd($td_discount);
//             $out[] = $atd_discount;
//             $out[] = $td_discount;
//             $out[] = $final_tr_rate;
//             return $out;

//             // dd($atd_rate);

//     }
    
//     public function dms_return_rate_on_the_behalf_of_product(Request $request)
//     {
//         $item_code = $request->item_code;
//         $dealer_id = $request->auth_id;
//         $catg = $request->catg;
//         $item_codes_str = explode(',', $request->item_codes_str);
//         $check_product_not_allow = DemandOrderCart::join('demand_order_details_cart','demand_order_details_cart.order_id','=','demand_order_cart.order_id')
//                 ->where('dealer_id',$this->dealer_id)->groupBy('product_id')->pluck('product_id');

//         $return_rate_details = DB::table('ITEM_RATE_MAST')
//                             ->where('ITEM_CODE',$item_code)
//                             ->whereRaw("TO_DATE is NULL")
//                             ->first();

//         if(empty($return_rate_details))
//         {
//             $return_rate_details = DB::table('ITEM_RATE_MAST')
//                             ->where('ITEM_CODE',$item_code)
//                             ->where("TO_DATE",'')
//                             ->first();
//         }

//         $return_size_um = DB::table('WWW_WEB_ITEM_DISPLAY')
//                             ->where('ITEM_CODE',$item_code)
//                             // ->whereRaw("TO_DATE is NULL")
//                             ->first();

//         $item_aum_mast_array_data = DB::table('WWW_WEB_ITEM_DISPLAY')
//                         ->whereNotIn('ITEM_CODE', $item_codes_str);
//                         if(!empty($catg))
//                         {
//                             $item_aum_mast_array_data->where('MKTG_CATG',$catg);
//                         }
//                         if(!empty($check_product_not_allow))
//                         {
//                             $item_aum_mast_array_data->whereNotIn('ITEM_CODE',$check_product_not_allow);
//                         }
                        
//         $item_aum_mast_array = $item_aum_mast_array_data->groupBy('ITEM_CODE')->orderBy('ITEM_CODE','ASC')->pluck(DB::raw("CONCAT(ITEM_CODE,' - ',ITEM_NAME) AS ITEM_NAME"), 'ITEM_CODE as k');
//         // dd($item_aum_mast_array);
//         $item_mast_name = DB::table('WWW_WEB_ITEM_DISPLAY')
//                         ->where('ITEM_CODE', $item_code)
//                         ->orderBy('ITEM_CODE','ASC')
//                         ->first();

//         if (!empty($return_rate_details)) {
//             $data['code'] = 200;
//             $data['result'] = $return_rate_details->WP;
//             $data['item_mast_name'] = $item_mast_name->ITEM_NAME;
//             $data['item_aum_mast_array'] = $item_aum_mast_array;
//             if($return_size_um->MIN_QTY == 'P' )
//             {
//                 $data['selection_size_um'] = 'PCS';
//             }
//             elseif($return_size_um->MIN_QTY == 'B')
//             {
//                 $data['selection_size_um'] = 'BOX';
//             }
//             $data['message'] = 'success';

//         } else {
//             $data['code'] = 401;
//             $data['result'] = '';
//             $data['selection_size_um'] = '';
//             $data['item_aum_mast_array'] = $item_aum_mast_array;
//             $data['message'] = 'unauthorized request';
//         }
//         return json_encode($data);

//     }

//     public function send_order_to_erp(Request $request)
//     {

//     	// dd($request->order_no);
//         $order_no = $request->order_no;
//         if(!empty($order_no))
//         {
//             $update_order = DB::table('demand_order')
//                         ->whereIn('id',$order_no)
//                         ->update([
//                             'send_order_erp_status'=>1, 
//                             'send_order_erp_date_time'=>date('Y-m-d H:i:s'), 
//                         ]);
//         }
//         // else
//         // {
//         //     Session::get('alert-class danger','message Already Submitted')
//         // }
        

//                     return redirect()->guest(url($this->current_menu));
//         // return redirect()->intended($this->current_menu);
//     }

//     public function check_scheme1(Request $request)
//     {

//         // $date = date('Y-m-d');
//         // $date = $this->date;
//         // $dealer_code = !empty($request->dealer_code)?$request->dealer_code:$this->dealer_code;
//         // $dealer_code = '20602';
//         // $qty = !empty($request->qty)?$request->qty:'21';
//         // $scheme_details = DB::table('CIRCULAR_MAST')
//         //                 ->join('SCHEME_MAST','SCHEME_MAST.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//         //                 ->join('SCHEME_SLAB','SCHEME_SLAB.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//         //                 ->join('SCHEME_DOMAIN','SCHEME_DOMAIN.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//         //                 ->whereRaw("DATE_FORMAT(CIRCULAR_MAST.from_date_condition ,'%Y-%m-%d')<='$date' AND DATE_FORMAT(CIRCULAR_MAST.to_date_condition,'%Y-%m-%d') >= '$date'")
//         //                 // ->whereRaw("CIRCULAR_MAST.from_date_condition >= '$date' and CIRCULAR_MAST.to_date_condition <= '$date' ")
//         //                 // ->orWhere('CANCELLEDBY','')
//         //                 // ->orWhere('CANCELLEDBY',' ')
//         //                 // ->orWhereRaw("CANCELLEDBY is NULL")
//         //                 ->where('SCHEME_DOMAIN.ACC_CODE',$dealer_code)
//         //                 ->get();
//         // // dd($scheme_details);
//         // $item_code = !empty($request->item_code)?$request->item_code:'30218';

//         // foreach ($scheme_details as $key => $value) {
//         //     # code...
//         //     if($value->CANCELLEDBY == '' || $value->CANCELLEDBY == ' ' || $value->CANCELLEDBY == NULL || $value->CANCELLEDBY == 'NULL'  )
//         //     {
//         //         // dd($value);
//         //         $scheme_no_step1[] = $value->SCHEME_NO;
//         //     }
//         // }
//         // // dd($scheme_no_step1);
//         // $srtring = implode(',', $scheme_no_step1);
//         // // dd($srtring);
//         // $scheme_details_1_step = DB::table('CIRCULAR_MAST')
//         //         ->join('SCHEME_MAST','SCHEME_MAST.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//         //         ->join('SCHEME_SLAB','SCHEME_SLAB.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//         //         ->join('SCHEME_DOMAIN','SCHEME_DOMAIN.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//         //         ->whereRaw("DATE_FORMAT(CIRCULAR_MAST.from_date_condition ,'%Y-%m-%d')<='$date' AND DATE_FORMAT(CIRCULAR_MAST.to_date_condition,'%Y-%m-%d') >= '$date'")
//         //         // ->whereRaw("CIRCULAR_MAST.from_date_condition >= '$date' and CIRCULAR_MAST.to_date_condition <= '$date' ")
//         //         // ->orWhere('CANCELLEDBY','')
//         //         // ->orWhere('CANCELLEDBY',' ')
//         //         // ->orWhereRaw("CANCELLEDBY is NULL")
//         //         ->whereIn('SCHEME_DOMAIN.SCHEME_NO',$scheme_no_step1)
//         //         // ->where('SCHEME_DOMAIN.ACC_CODE',$dealer_code)
//         //         ->where('SCHEME_DOMAIN.ITEM_CODE',$item_code)
//         //         ->orderBy('SCHEME_DOMAIN.id','DESC')
//         //         ->first();
//         // // dd($scheme_details_1_step);
//         // if(!empty($scheme_details_1_step) && COUNT($scheme_details_1_step)>0)
//         // {
//         //     $final_scheme_details_1_step_array = DB::table('SCHEME_SLAB')
//         //                         ->where('SCHEME_SLAB.SCHEME_NO',$scheme_details_1_step->SCHEME_NO)
//         //                         ->get();
//         //     // dd($scheme_details_1_step_array);
//         // }
//         // else
//         // {
//         //     $item_code_details = DB::table('ITEM_MAST')->where('ITEM_CODE',$item_code)->pluck('PROD_CODE');
//         //     // dd($item_code_details);
//         //     $scheme_details_1_step = DB::table('CIRCULAR_MAST')
//         //         ->join('SCHEME_MAST','SCHEME_MAST.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//         //         ->join('SCHEME_SLAB','SCHEME_SLAB.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//         //         ->join('SCHEME_DOMAIN','SCHEME_DOMAIN.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//         //         ->whereRaw("DATE_FORMAT(CIRCULAR_MAST.from_date_condition ,'%Y-%m-%d')<='$date' AND DATE_FORMAT(CIRCULAR_MAST.to_date_condition,'%Y-%m-%d') >= '$date'")
//         //         // ->whereRaw("CIRCULAR_MAST.from_date_condition >= '$date' and CIRCULAR_MAST.to_date_condition <= '$date' ")
//         //         // ->orWhere('CANCELLEDBY','')
//         //         // ->orWhere('CANCELLEDBY',' ')
//         //         // ->orWhereRaw("CANCELLEDBY is NULL")
//         //         ->whereIn('SCHEME_DOMAIN.SCHEME_NO',$scheme_no_step1)
//         //         // ->where('SCHEME_DOMAIN.ACC_CODE',$dealer_code)
//         //         ->whereIn('SCHEME_DOMAIN.PROD_CODE',$item_code_details)
//         //         ->orderBy('SCHEME_DOMAIN.id','DESC')
//         //         ->first();
//         //     // dd($scheme_details_1_step);

//         //     if(!empty($scheme_details_1_step) && COUNT($scheme_details_1_step)>0)
//         //     {
//         //         $final_scheme_details_1_step_array = DB::table('SCHEME_SLAB')
//         //                         ->where('SCHEME_SLAB.SCHEME_NO',$scheme_details_1_step->SCHEME_NO)
//         //                         ->orderBy('SCHEME_SLAB.id','DESC')
//         //                         ->get();
//         //         // dd($scheme_details_1_step_array);
//         //     }

//         // }
//         // // dd($final_scheme_details_1_step_array);
//         // $slabs = array();
//         // if(!empty($final_scheme_details_1_step_array))
//         // {
//         //     foreach ($final_scheme_details_1_step_array as $key => $value) {
//         //         # code...
//         //         // dd($value);
//         //         $sum_value = $value->BASE_FROM+$value->FREE_ITEM1_QTY1;
//         //         $out[] = $sum_value.'('.$value->BASE_FROM.'+'.$value->FREE_ITEM1_QTY1.'= '.$sum_value.')'; 
//         //         $slabs[] = $sum_value.'('.$value->BASE_FROM.'+'.$value->FREE_ITEM1_QTY1.'= '.$sum_value.')'; 
//         //     }
//         // }
//         // else
//         // {
//         //     $out[] = $qty.'('.$qty.'+ 0 = '.$qty.')'; 

//         // }

//         // if(!empty($out) && COUNT($out)>0)
//         // {
//         //     $data['code'] = 200;
//         //     $data['result'] = $out;
//         //     $data['slabs'] = $slabs;
//         //     $data['message'] = 'Found';
//         // }
//         // else
//         // {
//         //     $data['code'] = 401;
//         //     $data['result'] = array();
//         //     $data['slabs'] = array();
//         //     $data['message'] = 'unauthorized request';
//         // }
       
//         // return json_encode($data);

//         $qty = $request->get('term', '');
//         // dd($url_name);
//         $out = array();
//         $date = $this->date;
//         $dealer_code = !empty($request->dealer_code)?$request->dealer_code:$this->dealer_code;
//         // $dealer_code = '20602';
//         // $qty = !empty($request->qty)?$request->qty:'21';
//         $unit_conf = !empty($request->unit_conf)?$request->unit_conf:'';
//         $scheme_details = DB::table('CIRCULAR_MAST')
//                         ->join('SCHEME_MAST','SCHEME_MAST.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                         ->join('SCHEME_SLAB','SCHEME_SLAB.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                         ->join('SCHEME_DOMAIN','SCHEME_DOMAIN.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                         ->whereRaw("DATE_FORMAT(CIRCULAR_MAST.from_date_condition ,'%Y-%m-%d')<='$date' AND DATE_FORMAT(CIRCULAR_MAST.to_date_condition,'%Y-%m-%d') >= '$date'")
//                         // ->whereRaw("CIRCULAR_MAST.from_date_condition >= '$date' and CIRCULAR_MAST.to_date_condition <= '$date' ")
//                         // ->orWhere('CANCELLEDBY','')
//                         // ->orWhere('CANCELLEDBY',' ')
//                         // ->orWhereRaw("CANCELLEDBY is NULL")
//                         ->where('SCHEME_DOMAIN.ACC_CODE',$dealer_code)
//                         ->groupBy('SCHEME_DOMAIN.SCHEME_NO') // ac
//                         ->get();
//         // dd($scheme_details);
//         $item_code = !empty($request->item_code)?$request->item_code:'0';

//         foreach ($scheme_details as $key => $value) {
//             # code...
//             if($value->CANCELLEDBY == '' || $value->CANCELLEDBY == ' ' || $value->CANCELLEDBY == NULL || $value->CANCELLEDBY == 'NULL'  )
//             {
//                 // dd($value);
//                 $scheme_no_step1[] = $value->SCHEME_NO;
//             }
//         }
//         // dd($scheme_no_step1);
//         $srtring = implode(',', $scheme_no_step1);
//         // dd($srtring);
//         $scheme_details_1_step = DB::table('CIRCULAR_MAST')
//                 ->join('SCHEME_MAST','SCHEME_MAST.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                 ->join('SCHEME_SLAB','SCHEME_SLAB.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                 ->join('SCHEME_DOMAIN','SCHEME_DOMAIN.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                 ->whereRaw("DATE_FORMAT(CIRCULAR_MAST.from_date_condition ,'%Y-%m-%d')<='$date' AND DATE_FORMAT(CIRCULAR_MAST.to_date_condition,'%Y-%m-%d') >= '$date'")
//                 // ->whereRaw("CIRCULAR_MAST.from_date_condition >= '$date' and CIRCULAR_MAST.to_date_condition <= '$date' ")
//                 // ->orWhere('CANCELLEDBY','')
//                 // ->orWhere('CANCELLEDBY',' ')
//                 // ->orWhereRaw("CANCELLEDBY is NULL")
//                 ->whereIn('SCHEME_DOMAIN.SCHEME_NO',$scheme_no_step1)
//                 // ->where('SCHEME_DOMAIN.ACC_CODE',$dealer_code)
//                 ->where('SCHEME_DOMAIN.ITEM_CODE',$item_code)
//                 ->groupBy('SCHEME_DOMAIN.SCHEME_NO')
//                 ->orderBy('SCHEME_DOMAIN.id','DESC')
//                 ->first();
//         // dd($scheme_details_1_step);
//         if(!empty($scheme_details_1_step) && COUNT($scheme_details_1_step)>0)
//         {
//             $final_scheme_details_1_step_array = DB::table('SCHEME_SLAB')
//                                 ->where('SCHEME_SLAB.SCHEME_NO',$scheme_details_1_step->SCHEME_NO)
//                                 ->get();
//             // dd($scheme_details_1_step_array);
//         }
//         else
//         {
//             $item_code_details = DB::table('ITEM_MAST')->where('ITEM_CODE',$item_code)->pluck('PROD_CODE');
//             // dd($item_code_details);
//             $item_code_details_size = DB::table('ITEM_MAST')->where('ITEM_CODE',$item_code)->first();
//             $scheme_details_1_step = DB::table('CIRCULAR_MAST')
//                 ->join('SCHEME_MAST','SCHEME_MAST.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                 ->join('SCHEME_SLAB','SCHEME_SLAB.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                 ->join('SCHEME_DOMAIN','SCHEME_DOMAIN.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                 ->join('ITEM_MAST','ITEM_MAST.PROD_CODE','=','SCHEME_DOMAIN.PROD_CODE')
//                 ->select('ITEM_MAST.ITEM_SIZE as ITEM_SIZE','SCHEME_DOMAIN.SCHEME_NO as SCHEME_NO')
//                 ->whereRaw("DATE_FORMAT(CIRCULAR_MAST.from_date_condition ,'%Y-%m-%d')<='$date' AND DATE_FORMAT(CIRCULAR_MAST.to_date_condition,'%Y-%m-%d') >= '$date'")
//                 // ->whereRaw("CIRCULAR_MAST.from_date_condition >= '$date' and CIRCULAR_MAST.to_date_condition <= '$date' ")
//                 // ->orWhere('CANCELLEDBY','')
//                 // ->orWhere('CANCELLEDBY',' ')
//                 // ->orWhereRaw("CANCELLEDBY is NULL")
//                 ->whereIn('SCHEME_DOMAIN.SCHEME_NO',$scheme_no_step1)
//                 // ->where('SCHEME_DOMAIN.ACC_CODE',$dealer_code)
//                 ->whereIn('SCHEME_DOMAIN.PROD_CODE',$item_code_details)
//                 ->groupBy('SCHEME_DOMAIN.SCHEME_NO')
//                 ->orderBy('SCHEME_DOMAIN.id','DESC')
//                 ->first();
//             // dd($scheme_details_1_step);

//             if(!empty($scheme_details_1_step) && COUNT($scheme_details_1_step)>0)
//             {
//                 $final_scheme_details_1_step_array = DB::table('SCHEME_SLAB')
//                                 ->where('SCHEME_SLAB.SCHEME_NO',$scheme_details_1_step->SCHEME_NO)
//                                 ->where('ITEM_SIZE',$item_code_details_size->ITEM_SIZE)
//                                 ->orderBy('SCHEME_SLAB.id','DESC')
//                                 ->get();
//                 if(!empty($final_scheme_details_1_step_array) && COUNT($final_scheme_details_1_step_array)>0)
//                 {
//                     $final_scheme_details_1_step_array = DB::table('SCHEME_SLAB')
//                                 ->where('SCHEME_SLAB.SCHEME_NO',$scheme_details_1_step->SCHEME_NO)
//                                 ->where('ITEM_SIZE',$item_code_details_size->ITEM_SIZE)
//                                 ->orderBy('SCHEME_SLAB.id','DESC')
//                                 ->get();
//                 }
//                 else
//                 {
//                     $final_scheme_details_1_step_array = DB::table('SCHEME_SLAB')
//                                 ->where('SCHEME_SLAB.SCHEME_NO',$scheme_details_1_step->SCHEME_NO)
//                                 // ->where('ITEM_SIZE',$item_code_details_size->ITEM_SIZE)
//                                 ->orderBy('SCHEME_SLAB.id','DESC')
//                                 ->get();
//                 }
//                 // dd($final_scheme_details_1_step_array);
//             }


//         }
//         // dd($item_code);
//         $converstion_unit_item_code = DB::table('ITEM_AUM_MAST')->where('ITEM_CODE',$item_code)->first();
//         $converstion_unit = $converstion_unit_item_code->UMTOAUM;
//         // dd($converstion_unit);

//         $slabs = array();
//         $out_value_outer = '';
//         if(!empty($final_scheme_details_1_step_array) && COUNT($final_scheme_details_1_step_array)>0)
//         {
//             // dd('1');
//             $out = [];
//             $slabs = [];
//             $sum_value = '';
//             $sum_value_inner = '';
//             $free_inner = '';
//             $inner_sum_value = '';
//             $out_value = '';
//             foreach ($final_scheme_details_1_step_array as $s_key => $s_value) {
//                 # code...
//                 // dd($value);

                
//                 // $inner_sum_value = $sum_value_inner+$free_inner;
//                 if($unit_conf == 'BOX')
//                 {
//                     // dd($item_code);
//                     // dd($converstion_unit_item_code);
                    
//                     $sum_value = $s_value->BASE_FROM+$s_value->FREE_ITEM1_QTY1;
//                     $sum_value_inner = (($s_value->BASE_FROM)*$converstion_unit);
//                     $free_inner = (($s_value->FREE_ITEM1_QTY1)*$converstion_unit);
//                     $inner_sum_value = ($free_inner+$sum_value_inner);
//                     // $sum_value = $value->BASE_FROM
//                     $out_value = $sum_value.'('.$sum_value_inner.'+'.$free_inner.'= '.$inner_sum_value.')';

//                 // dd($unit_conf);

//                 }
//                 else
//                 {
//                     $sum_value = $s_value->BASE_FROM+$s_value->FREE_ITEM1_QTY1;
//                     $free_inner = $s_value->FREE_ITEM1_QTY1;
//                     $sum_value_inner = $s_value->BASE_FROM;

//                     $out_value = $sum_value.'('.$s_value->BASE_FROM.'+'.$s_value->FREE_ITEM1_QTY1.'= '.$sum_value.')';
//                 }
//                 // dd('1');
                

//                 $slabs_testing =$sum_value.'|'.$s_value->BASE_FROM.'|'.$s_value->FREE_ITEM1_QTY1;
//                 $slabs[] =' [ '. $sum_value.' ('.$s_value->BASE_FROM.'+'.$s_value->FREE_ITEM1_QTY1.'= '.$sum_value.' ) ] ';

//                 $out[] = array('value'=>$out_value, 'title' => $sum_value,'free_qty'=>$free_inner,'slabs_testing'=>$slabs_testing); 
                 
//             }
//         }
//         else
//         {
//             $out = [];
//             $slabs_testing =$qty.'|'.$qty.'|'.'0';
//             if($unit_conf == 'BOX')
//             {
//                 $out_value_outer = $qty.'('.$qty*$converstion_unit.'+ 0 = '.$qty*$converstion_unit.')'; 
//                 $out[] = array('value'=>$out_value_outer, 'title' => $qty,'free_qty'=>"0",'slabs_testing'=>$slabs_testing);
//             }
//             else
//             {
//                 $out_value_outer = $qty.'('.$qty.'+ 0 = '.$qty.')'; 
//                 $out[] = array('value'=>$out_value_outer, 'title' => $qty,'free_qty'=>"0",'slabs_testing'=>$slabs_testing);
//             }
            

//         }

//         $break_string_into_array_test = explode('|', $out[0]['slabs_testing']);
//         $tested_qty = $break_string_into_array_test[0];
//         $free_qty_one_time = $break_string_into_array_test[2];
//         $free_qty_one_time_prefix = 0;
//         $old_value_diff = 1;
//         // dd($qty);
//         if($unit_conf == 'BOX')
//         {
//             if($qty < $tested_qty)
//             {
//                 for ($i=1; $i < $tested_qty; $i++) { 
//                     # code...
//                     // dd($free_qty_one_time);
//                     $step1 = $qty; // 40
//                     $step2 = $free_qty_one_time_prefix+$free_qty_one_time/$converstion_unit;
//                     // dd($step2);
//                     $step3 = round($step2*$converstion_unit); // 5

//                     $step4 = $qty*$converstion_unit;
//                     $step5 = $step4 - $step3;



//                     $display_dropdown = $step1.'('.$step5.'+'.$step3.'='.$step4.')';

//                     $title = $step1;
//                     $free_qty = $step3;
//                     $free_qty_one_time_prefix = $step2;
//                     $qty = $step1+1;

//                     $finally_scheme_done_array[] = array('value'=>$display_dropdown,'title'=>$title,'free_qty'=>$free_qty);
//                 }
//             }
            
//         }
//         // dd($finally_scheme_done_array);
//         foreach ($out as $key => $value) {
//             # code...
//             // dd($value['slabs_testing']);
//             $break_string_into_array = explode('|', $value['slabs_testing']);
//             if(COUNT($break_string_into_array)>2)
//             {
//                 $total_qty = $break_string_into_array[0];
//                 $billed_qty = $break_string_into_array[1];
//                 $free_qty = $break_string_into_array[2];

//                 // dd($free_qty);
//                 // $loop_step1 = 


//                 if(empty($out[$key+1]))
//                 {
//                     $display_dropdown_f =  $value['value'];
//                     $title_f =  $value['title'];
//                     $free_qty_f =  $value['free_qty'];
//                     // dd($free_qty);
//                     $finally_scheme_done_array[] = array('value'=>$display_dropdown_f,'title'=>$title_f,'free_qty'=>$free_qty_f);
//                     $final_stop_condition = 10;
//                     for ($i=0; $i < $final_stop_condition; $i++) { 
//                         # code...

//                         if($unit_conf == 'BOX')
//                         {
//                             $step_multiply = 2+$i;
//                             $step1 = $total_qty+$tested_qty; // 40
//                             $step2 = $free_qty+$free_qty_one_time; // 5
//                             $step3 = $step1 - $step2; // 35

//                             $step_con1 = $step1*$converstion_unit;
//                             $step_con2 = $step2*$converstion_unit;
//                             $step_con3 = $step3*$converstion_unit;

//                             $display_dropdown = $step1.'('.$step_con3.'+'.$step_con2.'='.$step_con1.')';

//                             $title = $step1;
//                             $free_qty = $step2;
//                             $total_qty = $step1;
//                         }
//                         else
//                         {
//                             $step_multiply = 2+$i;
//                             $step1 = $total_qty+$tested_qty; // 40
//                             $step2 = $free_qty+$free_qty_one_time; // 5
//                             $step3 = $step1 - $step2; // 35
//                             $display_dropdown = $step1.'('.$step3.'+'.$step2.'='.$step1.')';
//                             $step_con2 = $step2;
//                             $title = $step1;
//                             $free_qty = $step2;
//                             $total_qty = $step1;

//                             // dd('1');
//                         }
//                         $finally_scheme_done_array[] = array('value'=>$display_dropdown,'title'=>$title,'free_qty'=>$step_con2);

//                     }
//                 }
//                 else
//                 {
//                     $break_string_into_array_2_arr = explode('|', $out[$key+1]['slabs_testing']);

//                     $fixed_slab = $break_string_into_array_2_arr[0];
//                     $stop_condition = $fixed_slab/$tested_qty;
//                     $final_stop_condition = $stop_condition-$old_value_diff;
//                     $old_value_diff = $stop_condition;

//                     $finally_scheme_done_array[] = array('value'=>$value['value'],'title'=>$value['title'],'free_qty'=>$value['free_qty']);
//                     // dd($fixed_slab);
//                     for ($i=0; $i < $final_stop_condition-1; $i++) { 
//                         # code...

//                         if($unit_conf == 'BOX')
//                         {
//                             $step_multiply = 2+$i;
//                             $step1 = $total_qty+$tested_qty; // 40
//                             $step2 = $free_qty+$free_qty_one_time; // 5
//                             $step3 = $step1 - $step2; // 35

//                             $step_con1 = $step1*$converstion_unit;
//                             $step_con2 = $step2*$converstion_unit;
//                             $step_con3 = $step3*$converstion_unit;

//                             $display_dropdown = $step1.'('.$step_con3.'+'.$step_con2.'='.$step_con1.')';

//                             $title = $step1;
//                             $free_qty = $step_con2;
//                             $total_qty = $step1;
//                         }
//                         else
//                         {
//                             $step_multiply = 2+$i;
//                             $step1 = $total_qty+$tested_qty; // 40
//                             $step2 = $free_qty+$free_qty_one_time; // 5
//                             $step3 = $step1 - $step2; // 35
//                             $display_dropdown = $step1.'('.$step3.'+'.$step2.'='.$step1.')';

//                             $title = $step1;
//                             $free_qty = $step2;
//                             $total_qty = $step1;
//                             // dd('1');
//                         }
//                         $finally_scheme_done_array[] = array('value'=>$display_dropdown,'title'=>$title,'free_qty'=>$free_qty);

//                     }
//                 }
                
//                 // dd($finally_scheme_done_array);


                

                



//                 // dd($finally_scheme_done_array);


//             }
//             else
//             {
//                 $display_dropdown =  $value['value'];
//                 $title =  $value['title'];
//                 $free_qty =  $value['free_qty'];
//                 $finally_scheme_done_array[] = array('value'=>$display_dropdown,'title'=>$title,'free_qty'=>$free_qty);
//             }
            
//         }
//         // dd($finally_scheme_done_array);

//         if(!empty($out) && COUNT($out)>0)
//         {
//             $data['code'] = 200;
//             $data['result'] =  $finally_scheme_done_array;
//             $data['size'] =  $scheme_details_1_step->ITEM_SIZE;
//             $data['prod'] = $item_code_details;
//             $data['slabs'] = implode(',',$slabs);
//             $data['message'] = 'Found';
//         }
//         else
//         {
//             $data['code'] = 401;
//             $data['result'] = array();
//             $data['slabs'] = array();
//             $data['message'] = 'unauthorized request';
//         }
       
//         return ($data);
        

//     }

//     public function autocomplete_search_url(Request $request)
//     {
//         // $qty = $request->get('term', '');
//         // // dd($url_name);
//         // $out = array();
//         // $date = $this->date;
//         // $item_code_details = array();
//         // $dealer_code = !empty($request->dealer_code)?$request->dealer_code:$this->dealer_code;
//         // // $dealer_code = '20602';
//         // // $qty = !empty($request->qty)?$request->qty:'21';
//         // $scheme_no_step1 = array();
//         // $unit_conf = !empty($request->unit_conf)?$request->unit_conf:'';
//         // $scheme_details = DB::table('CIRCULAR_MAST')
//         //                 ->join('SCHEME_MAST','SCHEME_MAST.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//         //                 ->join('SCHEME_SLAB','SCHEME_SLAB.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//         //                 ->join('SCHEME_DOMAIN','SCHEME_DOMAIN.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//         //                 ->whereRaw("DATE_FORMAT(CIRCULAR_MAST.from_date_condition ,'%Y-%m-%d')<='$date' AND DATE_FORMAT(CIRCULAR_MAST.to_date_condition,'%Y-%m-%d') >= '$date'")
//         //                 // ->whereRaw("CIRCULAR_MAST.from_date_condition >= '$date' and CIRCULAR_MAST.to_date_condition <= '$date' ")
//         //                 // ->orWhere('CANCELLEDBY','')
//         //                 // ->orWhere('CANCELLEDBY',' ')
//         //                 // ->orWhereRaw("CANCELLEDBY is NULL")
//         //                 ->where('SCHEME_DOMAIN.ACC_CODE',$dealer_code)
//         //                 ->groupBy('SCHEME_DOMAIN.SCHEME_NO') // ac
//         //                 ->get();
//         // // dd($scheme_details);
//         // $item_code = !empty($request->item_code)?$request->item_code:'0';

//         // foreach ($scheme_details as $key => $value) {
//         //     # code...
//         //     if($value->CANCELLEDBY == '' || $value->CANCELLEDBY == ' ' || $value->CANCELLEDBY == NULL || $value->CANCELLEDBY == 'NULL'  )
//         //     {
//         //         // dd($value);
//         //         $scheme_no_step1[] = $value->SCHEME_NO;
//         //     }
//         // }
//         // // dd($scheme_no_step1);
//         // $srtring = implode(',', $scheme_no_step1);
//         // // dd($srtring);
//         // $scheme_details_1_step = DB::table('CIRCULAR_MAST')
//         //         ->join('SCHEME_MAST','SCHEME_MAST.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//         //         ->join('SCHEME_SLAB','SCHEME_SLAB.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//         //         ->join('SCHEME_DOMAIN','SCHEME_DOMAIN.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//         //         ->whereRaw("DATE_FORMAT(CIRCULAR_MAST.from_date_condition ,'%Y-%m-%d')<='$date' AND DATE_FORMAT(CIRCULAR_MAST.to_date_condition,'%Y-%m-%d') >= '$date'")
//         //         // ->whereRaw("CIRCULAR_MAST.from_date_condition >= '$date' and CIRCULAR_MAST.to_date_condition <= '$date' ")
//         //         // ->orWhere('CANCELLEDBY','')
//         //         // ->orWhere('CANCELLEDBY',' ')
//         //         // ->orWhereRaw("CANCELLEDBY is NULL")
//         //         ->whereIn('SCHEME_DOMAIN.SCHEME_NO',$scheme_no_step1)
//         //         // ->where('SCHEME_DOMAIN.ACC_CODE',$dealer_code)
//         //         ->where('SCHEME_DOMAIN.ITEM_CODE',$item_code)
//         //         ->groupBy('SCHEME_DOMAIN.SCHEME_NO')
//         //         ->orderBy('SCHEME_DOMAIN.id','DESC')
//         //         ->first();
//         // // dd($scheme_details_1_step);
//         // if(!empty($scheme_details_1_step) && COUNT($scheme_details_1_step)>0)
//         // {
//         //     $final_scheme_details_1_step_array = DB::table('SCHEME_SLAB')
//         //                         ->where('SCHEME_SLAB.SCHEME_NO',$scheme_details_1_step->SCHEME_NO)
//         //                         ->get();
//         //     // dd($scheme_details_1_step_array);
//         // }
//         // else
//         // {
//         //     $item_code_details = DB::table('ITEM_MAST')->where('ITEM_CODE',$item_code)->pluck('PROD_CODE');
//         //     // dd($item_code_details);
//         //     $item_code_details_size = DB::table('ITEM_MAST')->where('ITEM_CODE',$item_code)->first();
//         //     $scheme_details_1_step = DB::table('CIRCULAR_MAST')
//         //         ->join('SCHEME_MAST','SCHEME_MAST.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//         //         ->join('SCHEME_SLAB','SCHEME_SLAB.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//         //         ->join('SCHEME_DOMAIN','SCHEME_DOMAIN.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//         //         ->join('ITEM_MAST','ITEM_MAST.PROD_CODE','=','SCHEME_DOMAIN.PROD_CODE')
//         //         ->select('ITEM_MAST.ITEM_SIZE as ITEM_SIZE','SCHEME_DOMAIN.SCHEME_NO as SCHEME_NO')
//         //         ->whereRaw("DATE_FORMAT(CIRCULAR_MAST.from_date_condition ,'%Y-%m-%d')<='$date' AND DATE_FORMAT(CIRCULAR_MAST.to_date_condition,'%Y-%m-%d') >= '$date'")
//         //         // ->whereRaw("CIRCULAR_MAST.from_date_condition >= '$date' and CIRCULAR_MAST.to_date_condition <= '$date' ")
//         //         // ->orWhere('CANCELLEDBY','')
//         //         // ->orWhere('CANCELLEDBY',' ')
//         //         // ->orWhereRaw("CANCELLEDBY is NULL")
//         //         ->whereIn('SCHEME_DOMAIN.SCHEME_NO',$scheme_no_step1)
//         //         // ->where('SCHEME_DOMAIN.ACC_CODE',$dealer_code)
//         //         ->whereIn('SCHEME_DOMAIN.PROD_CODE',$item_code_details)
//         //         ->groupBy('SCHEME_DOMAIN.SCHEME_NO')
//         //         ->orderBy('SCHEME_DOMAIN.id','DESC')
//         //         ->first();
//         //     // dd($scheme_details_1_step);

//         //     if(!empty($scheme_details_1_step) && COUNT($scheme_details_1_step)>0)
//         //     {
//         //         $final_scheme_details_1_step_array = DB::table('SCHEME_SLAB')
//         //                         ->where('SCHEME_SLAB.SCHEME_NO',$scheme_details_1_step->SCHEME_NO)
//         //                         ->where('ITEM_SIZE',$item_code_details_size->ITEM_SIZE)
//         //                         ->orderBy('SCHEME_SLAB.id','DESC')
//         //                         ->get();
//         //         if(!empty($final_scheme_details_1_step_array) && COUNT($final_scheme_details_1_step_array)>0)
//         //         {
//         //             $final_scheme_details_1_step_array = DB::table('SCHEME_SLAB')
//         //                         ->where('SCHEME_SLAB.SCHEME_NO',$scheme_details_1_step->SCHEME_NO)
//         //                         ->where('ITEM_SIZE',$item_code_details_size->ITEM_SIZE)
//         //                         ->orderBy('SCHEME_SLAB.id','DESC')
//         //                         ->get();
//         //         }
//         //         else
//         //         {
//         //             $final_scheme_details_1_step_array = DB::table('SCHEME_SLAB')
//         //                         ->where('SCHEME_SLAB.SCHEME_NO',$scheme_details_1_step->SCHEME_NO)
//         //                         // ->where('ITEM_SIZE',$item_code_details_size->ITEM_SIZE)
//         //                         ->orderBy('SCHEME_SLAB.id','DESC')
//         //                         ->get();
//         //         }
//         //         // dd($final_scheme_details_1_step_array);
//         //     }


//         // }
//         // // dd($item_code);
//         // $converstion_unit_item_code = DB::table('ITEM_AUM_MAST')->where('ITEM_CODE',$item_code)->first();
//         // $converstion_unit = !empty($converstion_unit_item_code->UMTOAUM)?$converstion_unit_item_code->UMTOAUM:'0';
//         // // dd($final_scheme_details_1_step_array);

//         // $slabs = array();
//         // $out_value_outer = '';
//         // if(!empty($final_scheme_details_1_step_array) && COUNT($final_scheme_details_1_step_array)>0)
//         // {
//         //     // dd('1');
//         //     $out = [];
//         //     $slabs = [];
//         //     $sum_value = '';
//         //     $sum_value_inner = '';
//         //     $free_inner = '';
//         //     $inner_sum_value = '';
//         //     $out_value = '';
//         //     foreach ($final_scheme_details_1_step_array as $s_key => $s_value) {
//         //         # code...
//         //         // dd($value);
//         //         if($s_value->FREE_ITEM1_QTY1 != '' && $s_value->BASE_FROM != '' ){
//         //             $FREE_ITEM1_QTY1 = !empty($s_value->FREE_ITEM1_QTY1)?$s_value->FREE_ITEM1_QTY1:'0';
//         //             $BASE_FROM = !empty($s_value->BASE_FROM)?$s_value->BASE_FROM:'0';
//         //             // $inner_sum_value = $sum_value_inner+$free_inner;
//         //             if($unit_conf == 'BOX1')
//         //             {
//         //                 // dd($item_code);
//         //                 // dd($converstion_unit_item_code);
                        
//         //                 $sum_value = $BASE_FROM+$FREE_ITEM1_QTY1;
//         //                 $sum_value_inner = (($BASE_FROM)*$converstion_unit);
//         //                 $free_inner = (($FREE_ITEM1_QTY1)*$converstion_unit);
//         //                 $inner_sum_value = ($free_inner+$sum_value_inner);
//         //                 // $sum_value = $value->BASE_FROM
//         //                 $out_value = $sum_value.'('.$sum_value_inner.'+'.$free_inner.'= '.$inner_sum_value.')';

//         //             // dd($unit_conf);

//         //             }
//         //             else
//         //             {
//         //                 $sum_value = $BASE_FROM+$FREE_ITEM1_QTY1;
//         //                 $free_inner = $FREE_ITEM1_QTY1;
//         //                 $sum_value_inner = $BASE_FROM;

//         //                 $out_value = $sum_value.'('.$BASE_FROM.'+'.$FREE_ITEM1_QTY1.'= '.$sum_value.')';
//         //             }
//         //             // dd('1');
                    

//         //             $slabs_testing =$sum_value.'|'.$BASE_FROM.'|'.$FREE_ITEM1_QTY1;
//         //             $slabs[] =''. $sum_value.' ('.$BASE_FROM.'+'.$FREE_ITEM1_QTY1.'= '.$sum_value.' )';

//         //             $out[] = array('value'=>$out_value, 'title' => $sum_value,'free_qty'=>$free_inner,'slabs_testing'=>$slabs_testing); 
//         //          }
//         //     }
//         // }
//         // else
//         // {
//         //     $out = [];
//         //     $slabs_testing =$qty.'|'.$qty.'|'.'0';
//         //     if($unit_conf == 'BOX')
//         //     {
//         //         $out_value_outer = $qty.' ('.$qty*$converstion_unit.'+ 0 = '.$qty*$converstion_unit.')'; 
//         //         $out[] = array('value'=>$out_value_outer, 'title' => $qty,'free_qty'=>"0",'slabs_testing'=>$slabs_testing);
//         //     }
//         //     else
//         //     {
//         //         $out_value_outer = $qty.' ('.$qty.'+ 0 = '.$qty.')'; 
//         //         $out[] = array('value'=>$out_value_outer, 'title' => $qty,'free_qty'=>"0",'slabs_testing'=>$slabs_testing);
//         //     }
            

//         // }

//         // // dd(($out));
//         // $set_value = 1;
//         // if($unit_conf == 'BOX')
//         // {
//         //     $qty = $qty*$converstion_unit;
//         // }

//         // foreach ($out as $key => $value) {
//         //     # code...
//         //     $set_value = 0;
//         //     // dd($value);

//         //     if(!empty($out[$key+1]))
//         //     {

//         //         if($qty > $value['title'] && $qty < $out[$key+1]['title'])
//         //         {
//         //             // dd($value['title']);
//         //             $title = $value['title'];
//         //             $free_qty = $value['free_qty'];
//         //             $final_value_billed = $title-$free_qty;

//         //             if(!empty($out[$key+1]))
//         //             {
//         //                 $another_slab_title = $out[$key+1]['title'];
//         //             }
//         //             else
//         //             {
//         //                 $another_slab_title = 0;
//         //             }
//         //             break;


//         //         }

//         //         elseif($value['title']>=$qty  )
//         //         {
//         //             $title = $value['title'];
//         //             $free_qty = $value['free_qty'];
//         //             $final_value_billed = $title-$free_qty;

//         //             if(!empty($out[$key+1]))
//         //             {
//         //                 $another_slab_title = $out[$key+1]['title'];
//         //             }
//         //             else
//         //             {
//         //                 $another_slab_title = 0;
//         //             }
//         //             // dd($value);
//         //             break;
//         //         }
//         //     }
//         //     else
//         //     {
//         //         if($qty > $value['title'] )
//         //         {
//         //             // dd($value['title']);
//         //             $title = $value['title'];
//         //             $free_qty = $value['free_qty'];
//         //             $final_value_billed = $title-$free_qty;

//         //             if(!empty($out[$key+1]))
//         //             {
//         //                 $another_slab_title = $out[$key+1]['title'];
//         //             }
//         //             else
//         //             {
//         //                 $another_slab_title = 0;
//         //             }


//         //         }

//         //         elseif($value['title']>=$qty  )
//         //         {
//         //             $title = $value['title'];
//         //             $free_qty = $value['free_qty'];
//         //             $final_value_billed = $title-$free_qty;

//         //             if(!empty($out[$key+1]))
//         //             {
//         //                 $another_slab_title = $out[$key+1]['title'];
//         //             }
//         //             else
//         //             {
//         //                 $another_slab_title = 0;
//         //             }
//         //             // dd($value);
//         //             break;
//         //         }
//         //     }
//         // }
//         // // dd($out,$another_slab_title);
//         // if($set_value == 0)
//         // {
//         //     $free_qty = $free_qty;
//         //     $title = $title;
//         //     $final_value_billed = $final_value_billed;
//         //     $divide_perc = ($free_qty/$final_value_billed)*100;

//         //     $given_qty = $qty;
//         //     // dd($given_qty);
//         //     $step1_perc_value = round($given_qty/$title);
//         //     for ($i=0; $i < 10; $i++) { 
//         //         # code...
//         //         if($step1_perc_value == 0)
//         //         {
//         //             $step1_perc_value = 1;
//         //         }
//         //         // elseif($step1_perc_value == 1)
//         //         // {
//         //         //     $step1_perc_value = 2;
//         //         // }
//         //         // if()
//         //         $step2_free_qty = $free_qty*$step1_perc_value;
//         //         $step3_billed_qty = $final_value_billed*$step1_perc_value;
//         //         if($unit_conf == 'BOX')
//         //         {
//         //             $step4_title = $step3_billed_qty+$step2_free_qty;
//         //             $step4_title_front = ($step3_billed_qty+$step2_free_qty)/$converstion_unit;
//         //         }
//         //         else
//         //         {
//         //             $step4_title = $step3_billed_qty+$step2_free_qty;
//         //             $step4_title_front = $step3_billed_qty+$step2_free_qty;
//         //         }
//         //         if($another_slab_title != 0 && $step4_title <= $another_slab_title)
//         //         {
//         //             $final_deploy['value'] = $step4_title_front.' ('.$step3_billed_qty.'+'.$step2_free_qty.'= '.$step4_title.')';
//         //             $final_deploy['title'] = $step4_title_front;
//         //             $final_deploy['free_qty'] = $step2_free_qty;
//         //             // $final_deploy[] = 
//         //             // $final_deploy[] = value
//         //             $final_deploy_out[] = $final_deploy;

//         //         }
//         //         elseif($another_slab_title == 0)
//         //         {
//         //             $final_deploy['value'] = $step4_title_front.' ('.$step3_billed_qty.'+'.$step2_free_qty.'= '.$step4_title.')';
//         //             $final_deploy['title'] = $step4_title_front;
//         //             $final_deploy['free_qty'] = $step2_free_qty;
//         //         // dd($final_deploy,$step1_perc_value);free_qty
//         //             $final_deploy_out[] = $final_deploy;

//         //         }
//         //         $step1_perc_value = $step1_perc_value+1;
//         //         $step1_perc_value1[] = $step1_perc_value;
//         //     }
//         //     $count_details = COUNT($final_deploy_out);

//         //     // if($)
            
//         //     $last_key = $final_deploy_out[$count_details-1]['title'];
//         //     foreach ($out as $key => $value) {
//         //         # code...
//         //         // dd($value);
//         //         if($last_key != $value['title'] && $last_key <= $value['title'])
//         //         {
//         //             $final_deploy['value'] = $value['value'];
//         //             $final_deploy['title'] = $value['title'];
//         //             $final_deploy['free_qty'] = $value['free_qty'];
//         //             $final_deploy_out[] = $final_deploy;

//         //         }
//         //     }
//         //         // dd($final_deploy_out,$count_details);

//         // }
//         // else // use when no scheme is there 
//         // {
//         //     $puda_size_col = DB::table('WWW_WEB_ITEM_DISPLAY')
//         //                     ->where('ITEM_CODE',$item_code)
//         //                     ->first();
//         //     $puda_size = $puda_size_col->PUDA_SIZE;
//         //     // dd($puda_size);
//         //     $out = [];
//         //     $slabs_testing =$qty.'|'.$qty.'|'.'0';
//         //     if($unit_conf == 'BOX')
//         //     {
//         //         $out_value_outer = $qty.' ('.$qty*$converstion_unit.'+ 0 = '.$qty*$converstion_unit.')'; 
//         //         $out[] = array('value'=>$out_value_outer, 'title' => $qty,'free_qty'=>"0",'slabs_testing'=>$slabs_testing);
//         //     }
//         //     else
//         //     {
//         //         $out_value_outer = $qty.' ('.$qty.'+ 0 = '.$qty.')'; 
//         //         $out[] = array('value'=>$out_value_outer, 'title' => $qty,'free_qty'=>"0",'slabs_testing'=>$slabs_testing);
//         //     }
//         //     // dd($out);
//         //     $step1_perc_value = $puda_size;
//         //     $final_value_billed = $qty;
//         //     $free_qty = 0;
//         //     for ($i=0; $i < 10; $i++) { 
//         //         # code...
//         //         if($step1_perc_value == 0)
//         //         {
//         //             $step1_perc_value = 1;
//         //         }
//         //         // elseif($step1_perc_value == 1)
//         //         // {
//         //         //     $step1_perc_value = 2;
//         //         // }
//         //         // if()
//         //         $step2_free_qty = $free_qty*$step1_perc_value;
//         //         $step3_billed_qty = $final_value_billed*$step1_perc_value;
//         //         $step4_title = $step3_billed_qty+$step2_free_qty;
               
//         //         $final_deploy['value'] = $step4_title.' ('.$step3_billed_qty.'+'.$step2_free_qty.'='.$step4_title.')';
//         //         $final_deploy['title'] = $step4_title;
//         //         $final_deploy['free_qty'] = $step2_free_qty;
//         //     // dd($final_deploy,$step1_perc_value);free_qty
//         //         $final_deploy_out[] = $final_deploy;

               
//         //         $step1_perc_value = $step1_perc_value+1;
//         //         $step1_perc_value1[] = $step1_perc_value;
//         //     }
//         // }
//         // // dd($final_deploy_out,$step1_perc_value1);





        
            
        
//         // if(!empty($out) && COUNT($out)>0)
//         // {
//         //     $data['code'] = 200;
//         //     $data['result'] =  $final_deploy_out;
//         //     $data['size'] =  !empty($scheme_details_1_step->ITEM_SIZE)?$scheme_details_1_step->ITEM_SIZE:'';
//         //     $data['prod'] = $item_code_details;
//         //     $data['slabs'] = implode(',',$slabs);
//         //     $data['message'] = 'Found';
//         // }
//         // else
//         // {
//         //     $data['code'] = 401;
//         //     $data['result'] = array();
//         //     $data['slabs'] = array();
//         //     $data['message'] = 'unauthorized request';
//         // }
       
//         // return ($data);
//         $qty = $request->get('term', '');
//         $qty = (int)$qty;

//         // dd($url_name);
//         $out = array();
//         $date = $this->date;
//         $dealer_code = !empty($request->dealer_code)?$request->dealer_code:$this->dealer_code;
//         // $dealer_code = '20602';
//         // $qty = !empty($request->qty)?$request->qty:'21';
//         $scheme_no_step1= array();
//         $unit_conf = !empty($request->unit_conf)?$request->unit_conf:'';
//         $scheme_details = DB::table('CIRCULAR_MAST')
//                         ->join('SCHEME_MAST','SCHEME_MAST.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                         ->join('SCHEME_SLAB','SCHEME_SLAB.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                         ->join('SCHEME_DOMAIN','SCHEME_DOMAIN.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                         ->whereRaw("DATE_FORMAT(CIRCULAR_MAST.from_date_condition ,'%Y-%m-%d')<='$date' AND DATE_FORMAT(CIRCULAR_MAST.to_date_condition,'%Y-%m-%d') >= '$date'")
//                         // ->whereRaw("CIRCULAR_MAST.from_date_condition >= '$date' and CIRCULAR_MAST.to_date_condition <= '$date' ")
//                         // ->orWhere('CANCELLEDBY','')
//                         // ->orWhere('CANCELLEDBY',' ')
//                         // ->orWhereRaw("CANCELLEDBY is NULL")
//                         ->where('SCHEME_DOMAIN.ACC_CODE',$dealer_code)
//                         ->groupBy('SCHEME_DOMAIN.SCHEME_NO') // ac
//                         ->get();
//         // dd($scheme_details);
//         $item_code = !empty($request->item_code)?$request->item_code:'0';

//         foreach ($scheme_details as $key => $value) {
//             # code...
//             if($value->CANCELLEDBY == '' || $value->CANCELLEDBY == ' ' || $value->CANCELLEDBY == NULL || $value->CANCELLEDBY == 'NULL'  )
//             {
//                 // dd($value);
//                 $scheme_no_step1[] = $value->SCHEME_NO;
//             }
//         }
//         // dd($scheme_no_step1);
//         $srtring = implode(',', $scheme_no_step1);
//         // dd($srtring);
//         $scheme_details_1_step = DB::table('CIRCULAR_MAST')
//                 ->join('SCHEME_MAST','SCHEME_MAST.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                 ->join('SCHEME_SLAB','SCHEME_SLAB.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                 ->join('SCHEME_DOMAIN','SCHEME_DOMAIN.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                 ->whereRaw("DATE_FORMAT(CIRCULAR_MAST.from_date_condition ,'%Y-%m-%d')<='$date' AND DATE_FORMAT(CIRCULAR_MAST.to_date_condition,'%Y-%m-%d') >= '$date'")
//                 // ->whereRaw("CIRCULAR_MAST.from_date_condition >= '$date' and CIRCULAR_MAST.to_date_condition <= '$date' ")
//                 // ->orWhere('CANCELLEDBY','')
//                 // ->orWhere('CANCELLEDBY',' ')
//                 // ->orWhereRaw("CANCELLEDBY is NULL")
//                 ->whereIn('SCHEME_DOMAIN.SCHEME_NO',$scheme_no_step1)
//                 // ->where('SCHEME_DOMAIN.ACC_CODE',$dealer_code)
//                 ->where('SCHEME_DOMAIN.ITEM_CODE',$item_code)
//                 ->groupBy('SCHEME_DOMAIN.SCHEME_NO')
//                 ->orderBy('SCHEME_DOMAIN.id','DESC')
//                 ->first();
//         // dd($scheme_details_1_step);
//         if(!empty($scheme_details_1_step) && COUNT($scheme_details_1_step)>0)
//         {
//             $final_scheme_details_1_step_array = DB::table('SCHEME_SLAB')
//                                 ->where('SCHEME_SLAB.SCHEME_NO',$scheme_details_1_step->SCHEME_NO)
//                                 ->get();
//             // dd($scheme_details_1_step_array);
//         }
//         else
//         {
//             $item_code_details = DB::table('ITEM_MAST')->where('ITEM_CODE',$item_code)->pluck('PROD_CODE');
//             // dd($item_code_details);
//             $item_code_details_size = DB::table('ITEM_MAST')->where('ITEM_CODE',$item_code)->first();
//             $scheme_details_1_step = DB::table('CIRCULAR_MAST')
//                 ->join('SCHEME_MAST','SCHEME_MAST.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                 ->join('SCHEME_SLAB','SCHEME_SLAB.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                 ->join('SCHEME_DOMAIN','SCHEME_DOMAIN.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                 ->join('ITEM_MAST','ITEM_MAST.PROD_CODE','=','SCHEME_DOMAIN.PROD_CODE')
//                 ->select('ITEM_MAST.ITEM_SIZE as ITEM_SIZE','SCHEME_DOMAIN.SCHEME_NO as SCHEME_NO')
//                 ->whereRaw("DATE_FORMAT(CIRCULAR_MAST.from_date_condition ,'%Y-%m-%d')<='$date' AND DATE_FORMAT(CIRCULAR_MAST.to_date_condition,'%Y-%m-%d') >= '$date'")
//                 // ->whereRaw("CIRCULAR_MAST.from_date_condition >= '$date' and CIRCULAR_MAST.to_date_condition <= '$date' ")
//                 // ->orWhere('CANCELLEDBY','')
//                 // ->orWhere('CANCELLEDBY',' ')
//                 // ->orWhereRaw("CANCELLEDBY is NULL")
//                 ->whereIn('SCHEME_DOMAIN.SCHEME_NO',$scheme_no_step1)
//                 // ->where('SCHEME_DOMAIN.ACC_CODE',$dealer_code)
//                 ->whereIn('SCHEME_DOMAIN.PROD_CODE',$item_code_details)
//                 ->groupBy('SCHEME_DOMAIN.SCHEME_NO')
//                 ->orderBy('SCHEME_DOMAIN.id','DESC')
//                 ->first();
//             // dd($scheme_details_1_step);

//             if(!empty($scheme_details_1_step) && COUNT($scheme_details_1_step)>0)
//             {
//                 $final_scheme_details_1_step_array = DB::table('SCHEME_SLAB')
//                                 ->where('SCHEME_SLAB.SCHEME_NO',$scheme_details_1_step->SCHEME_NO)
//                                 ->where('ITEM_SIZE',$item_code_details_size->ITEM_SIZE)
//                                 ->orderBy('SCHEME_SLAB.id','DESC')
//                                 ->get();
//                 if(!empty($final_scheme_details_1_step_array) && COUNT($final_scheme_details_1_step_array)>0)
//                 {
//                     $final_scheme_details_1_step_array = DB::table('SCHEME_SLAB')
//                                 ->where('SCHEME_SLAB.SCHEME_NO',$scheme_details_1_step->SCHEME_NO)
//                                 ->where('ITEM_SIZE',$item_code_details_size->ITEM_SIZE)
//                                 ->orderBy('SCHEME_SLAB.id','DESC')
//                                 ->get();
//                 }
//                 else
//                 {
//                     $final_scheme_details_1_step_array = DB::table('SCHEME_SLAB')
//                                 ->where('SCHEME_SLAB.SCHEME_NO',$scheme_details_1_step->SCHEME_NO)
//                                 // ->where('ITEM_SIZE',$item_code_details_size->ITEM_SIZE)
//                                 ->orderBy('SCHEME_SLAB.id','DESC')
//                                 ->get();
//                 }
//                 // dd($final_scheme_details_1_step_array);
//             }


//         }
//         // dd($item_code);
//         $converstion_unit_item_code = DB::table('ITEM_AUM_MAST')->where('ITEM_CODE',$item_code)->first();
//         $converstion_unit = !empty($converstion_unit_item_code->UMTOAUM)?$converstion_unit_item_code->UMTOAUM:'0';
//         // dd($final_scheme_details_1_step_array);
//         $qty = !empty($qty)?$qty:'0';
//         if($unit_conf == 'BOX')
//         {
//             $qty = $qty*$converstion_unit;
//         }
//         else{
//             $qty = $qty;
//         }   
//         $slabs = array();
//         $out_value_outer = '';
//         if(!empty($final_scheme_details_1_step_array) && COUNT($final_scheme_details_1_step_array)>0)
//         {
//             // dd('1');
//             $out = [];
//             $slabs = [];
//             $sum_value = '';
//             $sum_value_inner = '';
//             $free_inner = '';
//             $inner_sum_value = '';
//             $out_value = '';
//             foreach ($final_scheme_details_1_step_array as $s_key => $s_value) {
//                 # code...
//                 // dd($value);
//                 if($s_value->FREE_ITEM1_QTY1 != '' && $s_value->BASE_FROM != '' ){
//                     $FREE_ITEM1_QTY1 = !empty($s_value->FREE_ITEM1_QTY1)?$s_value->FREE_ITEM1_QTY1:'0';
//                     $BASE_FROM = !empty($s_value->BASE_FROM)?$s_value->BASE_FROM:'0';
//                     // $inner_sum_value = $sum_value_inner+$free_inner;
//                     if($unit_conf == 'BOX1')
//                     {
//                         // dd($item_code);
//                         // dd($converstion_unit_item_code);
                        
//                         $sum_value = $BASE_FROM+$FREE_ITEM1_QTY1;
//                         $sum_value_inner = (($BASE_FROM)*$converstion_unit);
//                         $free_inner = (($FREE_ITEM1_QTY1)*$converstion_unit);
//                         $inner_sum_value = ($free_inner+$sum_value_inner);
//                         // $sum_value = $value->BASE_FROM
//                         // $out_value = $sum_value.'('.$sum_value_inner.'+'.$free_inner.'= '.$inner_sum_value.')';
//                         $out_value = $sum_value.'('.$sum_value_inner.'+'.$free_inner.')';

//                     // dd($unit_conf);

//                     }
//                     else
//                     {
//                         $sum_value = $BASE_FROM+$FREE_ITEM1_QTY1;
//                         $free_inner = $FREE_ITEM1_QTY1;
//                         $sum_value_inner = $BASE_FROM;

//                         // $out_value = $sum_value.'('.$BASE_FROM.'+'.$FREE_ITEM1_QTY1.'= '.$sum_value.')';
//                         $out_value = $sum_value.'('.$BASE_FROM.'+'.$FREE_ITEM1_QTY1.')';
//                     }
//                     // dd('1');
                    

//                     $slabs_testing =$sum_value.'|'.$BASE_FROM.'|'.$FREE_ITEM1_QTY1;
//                     // $slabs[] ='  '. $sum_value.' ('.$BASE_FROM.'+'.$FREE_ITEM1_QTY1.'= '.$sum_value.' ) ';
//                     $slabs[] ='  '. $sum_value.' ('.$BASE_FROM.'+'.$FREE_ITEM1_QTY1.'= '.$sum_value.' ) ';

//                     $out[] = array('value'=>$out_value, 'title' => $sum_value,'free_qty'=>$free_inner,'slabs_testing'=>$slabs_testing); 
//                  }
//             }
//         }
//         else
//         {
//             $out = [];
//             $slabs_testing =$qty.'|'.$qty.'|'.'0';
//             if($unit_conf == 'BOX')
//             {
//                 $out_value_outer = $qty.'('.$qty*$converstion_unit.'+ 0 = '.$qty*$converstion_unit.')'; 
//                 $out[] = array('value'=>$out_value_outer, 'title' => $qty,'free_qty'=>"0",'slabs_testing'=>$slabs_testing);
//             }
//             else
//             {
//                 $out_value_outer = $qty.'('.$qty.'+ 0 = '.$qty.')'; 
//                 $out[] = array('value'=>$out_value_outer, 'title' => $qty,'free_qty'=>"0",'slabs_testing'=>$slabs_testing);
//             }
            

//         }

//         // dd(($out));
//         $set_value = 1;
//         if($unit_conf == 'BOX1')
//         {
//             $qty = $qty*$converstion_unit;
//         }

//         foreach ($out as $key => $value) {
//             # code...
//             $set_value = 0;
//             // dd($value);
//             // dd($value);
//             if(!empty($out[$key+1]))
//             {

//                 if($qty > $value['title'] && $qty < $out[$key+1]['title'])
//                 {
//                     // dd($value['title']);
//                     $title = $value['title'];
//                     $free_qty = $value['free_qty'];
//                     $final_value_billed = $title-$free_qty;

//                     if(!empty($out[$key+1]))
//                     {
//                         $another_slab_title = $out[$key+1]['title'];
//                     }
//                     else
//                     {
//                         $another_slab_title = 0;
//                     }
//                     break;


//                 }

//                 elseif($value['title']>=$qty  )
//                 {
//                     $title = $value['title'];
//                     $free_qty = $value['free_qty'];
//                     $final_value_billed = $title-$free_qty;

//                     if(!empty($out[$key+1]))
//                     {
//                         $another_slab_title = $out[$key+1]['title'];
//                     }
//                     else
//                     {
//                         $another_slab_title = 0;
//                     }
//                     // dd($value);
//                     break;
//                 }
//             }
//             else
//             {
//                 // dd($qty);
//                 if($qty > $value['title'] )
//                 {
//                     // dd($value['title']);
//                     $title = $value['title'];
//                     $free_qty = $value['free_qty'];
//                     $final_value_billed = $title-$free_qty;

//                     if(!empty($out[$key+1]))
//                     {
//                         $another_slab_title = $out[$key+1]['title'];
//                     }
//                     else
//                     {
//                         $another_slab_title = 0;
//                     }


//                 }

//                 elseif($value['title']>=$qty  )
//                 {
//                     $title = $value['title'];
//                     $free_qty = $value['free_qty'];
//                     $final_value_billed = $title-$free_qty;

//                     if(!empty($out[$key+1]))
//                     {
//                         $another_slab_title = $out[$key+1]['title'];
//                     }
//                     else
//                     {
//                         $another_slab_title = 0;
//                     }
//                     // dd($value);
//                     break;
//                 }
//             }
//         }
//         // dd($out,$another_slab_title);
//         $final_deploy_out = [];
//         if($set_value == 0)
//         {
//             $free_qty = $free_qty;
//             $title = !empty($title)?$title:'0';
//             $final_value_billed = $final_value_billed;
//             if($final_value_billed == '0' || $final_value_billed == 0)
//             {
//                 $divide_perc = 0;
//             }
//             else{
//                 $divide_perc = ($free_qty/$final_value_billed)*100;
//             }

//             $given_qty = !empty($qty)?$qty:'0';
//             // dd($given_qty);
//             if($title == '0')
//             {
//                 $step1_perc_value = 0;

//             }
//             else
//             {
//                 $step1_perc_value = round($given_qty/$title);

//             }
//             for ($i=0; $i < 10; $i++) { 
//                 # code...
//                 if($step1_perc_value == 0)
//                 {
//                     $step1_perc_value = 1;
//                 }
//                 // elseif($step1_perc_value == 1)
//                 // {
//                 //     $step1_perc_value = 2;
//                 // }
//                 // if()
//                 $step2_free_qty = $free_qty*$step1_perc_value;
//                 $step3_billed_qty = $final_value_billed*$step1_perc_value;
//                 $step4_title = $step3_billed_qty+$step2_free_qty;
//                 if($another_slab_title != 0 && $step4_title <= $another_slab_title)
//                 {
//                     $final_deploy['value'] = $step4_title.' ('.$step3_billed_qty.'+'.$step2_free_qty.')';
//                     $final_deploy['title'] = $step4_title;
//                     $final_deploy['free_qty'] = $step2_free_qty;
//                     // $final_deploy[] = 
//                     // $final_deploy[] = value
//                     $final_deploy_out[] = $final_deploy;

//                 }
//                 elseif($another_slab_title == 0)
//                 {
//                     $final_deploy['value'] = $step4_title.' ('.$step3_billed_qty.'+'.$step2_free_qty.')';
//                     $final_deploy['title'] = $step4_title;
//                     $final_deploy['free_qty'] = $step2_free_qty;
//                 // dd($final_deploy,$step1_perc_value);free_qty
//                     $final_deploy_out[] = $final_deploy;

//                 }
//                 $step1_perc_value = $step1_perc_value+1;
//                 $step1_perc_value1[] = $step1_perc_value;
//             }
//             $count_details = COUNT($final_deploy_out);

//             // if($)
//             if(empty($final_deploy_out))
//             {
//                 $last_key = 0;

//             }
//             else
//             {
//                 $last_key = $final_deploy_out[$count_details-1]['title'];
//             }
//             foreach ($out as $key => $value) {
//                 # code...
//                 // dd($value);
//                 if($last_key != $value['title'] && $last_key <= $value['title'])
//                 {
//                     $final_deploy['value'] = $value['value'];
//                     $final_deploy['title'] = $value['title'];
//                     $final_deploy['free_qty'] = $value['free_qty'];
//                     $final_deploy_out[] = $final_deploy;

//                 }
//             }
//                 // dd($final_deploy_out,$count_details);

//         }
//         else // use when no scheme is there 
//         {
//             $puda_size_col = DB::table('WWW_WEB_ITEM_DISPLAY')
//                             ->where('ITEM_CODE',$item_code)
//                             ->first();
//             $puda_size = $puda_size_col->PUDA_SIZE;
//             // dd($puda_size);
//             $out = [];
//             $slabs_testing =$qty.'|'.$qty.'|'.'0';
//             if($unit_conf == 'BOX')
//             {
//                 $out_value_outer = $qty.'('.$qty*$converstion_unit.'+ 0 = '.$qty*$converstion_unit.')'; 
//                 $out[] = array('value'=>$out_value_outer, 'title' => $qty,'free_qty'=>"0",'slabs_testing'=>$slabs_testing);
//             }
//             else
//             {
//                 $out_value_outer = $qty.'('.$qty.'+ 0 = '.$qty.')'; 
//                 $out[] = array('value'=>$out_value_outer, 'title' => $qty,'free_qty'=>"0",'slabs_testing'=>$slabs_testing);
//             }
//             // dd($out);
//             $step1_perc_value = $puda_size;
//             $final_value_billed = $qty;
//             $free_qty = 0;
//             for ($i=0; $i < 10; $i++) { 
//                 # code...
//                 if($step1_perc_value == 0)
//                 {
//                     $step1_perc_value = 1;
//                 }
//                 // elseif($step1_perc_value == 1)
//                 // {
//                 //     $step1_perc_value = 2;
//                 // }
//                 // if()
//                 $step2_free_qty = $free_qty*$step1_perc_value;
//                 $step3_billed_qty = $final_value_billed*$step1_perc_value;
//                 $step4_title = $step3_billed_qty+$step2_free_qty;
               
//                 $final_deploy['value'] = $step4_title.' ('.$step3_billed_qty.'+'.$step2_free_qty.')';
//                 $final_deploy['title'] = $step4_title;
//                 $final_deploy['free_qty'] = $step2_free_qty;
//             // dd($final_deploy,$step1_perc_value);free_qty
//                 $final_deploy_out[] = $final_deploy;

               
//                 $step1_perc_value = $step1_perc_value+1;
//                 $step1_perc_value1[] = $step1_perc_value;
//             }
//         }
//         // dd($final_deploy_out,$step1_perc_value1);
//         $puda_size_col = DB::table('WWW_WEB_ITEM_DISPLAY')
//                         ->where('ITEM_CODE',$item_code)
//                         ->first();
//         $puda_size = !empty($puda_size_col->PUDA_SIZE)?$puda_size_col->PUDA_SIZE:'0';
//         $forbreaking_loop = $final_deploy_out;
//         foreach ($out as $key => $value) {
//             # code...
//             // dd($value);
//             $title = $value['title'];
//             $free_qty = $value['free_qty'];
//             $value_ins = $value['value'];
//             if($title == '0')
//             {
//                 $perc_matching = 0;
//             }
//             else
//             {
//                 $perc_matching = $free_qty/$title;
//             }
//             $for_per_check1 = $title-$free_qty;
//             // dd($free_qty);if
//             if($free_qty == 0)
//             {
//                 // dd(1);
//                 break;
//             }
//             $final_per_check_for_comp = $for_per_check1/$free_qty;
//             // dd($final_per_check_for_comp);
//             $loop_cond1 = $title/$puda_size;
//             // $loop_cond2 = $loop_cond1*8;
//             // $loop_cond3 = $loop_cond2+$title;
//             // dd($loop_cond3);
//             // for ($i=0; $i < ; $i++) { 
//             //     # code...
//             // }
//                 # code...
//             if(!empty($forbreaking_loop[$key+1]))
//             {
//                 for ($i=0; $i <9 ; $i++) { 
//                     // code...
//                     // dd($final_deploy_out[$key+1]['title']);

//                     if($title == $forbreaking_loop[$key+1]['title'])
//                     {
//                         // dd($title);
//                         break;
//                     }
//                     else
//                     {
//                         $step1 = $title+$puda_size;
//                         $step2 = (int)($step1*$perc_matching);
//                         $title = $step1;
//                         $free_qty = $step2;
//                         $bill_qty = $title-$free_qty;

//                         $final_deploy['value'] = $title.' ('.$bill_qty.'+'.$free_qty.')';
//                         $final_deploy['title'] = $title;
//                         $final_deploy['free_qty'] = $free_qty;
//                         $perc_check = $bill_qty/$free_qty;
//                         // dd($perc_check);
//                         if($perc_check == $final_per_check_for_comp)
//                         {
//                             // dd('1');
//                             if($title != $forbreaking_loop[$key+1]['title'])
//                             {
//                                 $final_deploy_out[] = $final_deploy;
//                             }
//                         }
//                     }
                    
                    

                    
//                     // if()
//                 }
//                 asort($final_deploy_out);
//                 $final_deploy_out_c = array_column($final_deploy_out, 'title');

//                 array_multisort($final_deploy_out_c, SORT_ASC, $final_deploy_out);
//                 // dd($final_deploy_out,$perc_check);

//             }
//             else
//             {
//                 for ($i=0; $i <10 ; $i++) { 
//                     // code...
//                     // dd($final_deploy_out[$key+1]['title']);

//                         $step1 = $title+$puda_size;
//                         $step2 = (int)($step1*$perc_matching);
//                         $title = $step1;
//                         $free_qty = $step2;
//                         $bill_qty = $title-$free_qty;

//                         $final_deploy['value'] = $title.' ('.$bill_qty.'+'.$free_qty.')';
//                         $final_deploy['title'] = $title;
//                         $final_deploy['free_qty'] = $free_qty;
//                         $perc_check = $bill_qty/$free_qty;
//                         // dd($perc_check);
//                         if($perc_check == $final_per_check_for_comp)
//                         {
//                             // dd('1');
                            
//                                 $final_deploy_out[] = $final_deploy;
//                         }

//                 }
//                 // asort($final_deploy_out);
//                 $final_deploy_out_c = array_column($final_deploy_out, 'title');

//                 array_multisort($final_deploy_out_c, SORT_ASC, $final_deploy_out);
//             }

           
            
//         }
//         // dd($final_deploy_out);


//         // ($final_deploy_out);
//         $sd = array();
//         $final_deploy_out_f = array();
//         foreach ($final_deploy_out as $key => $value) {
//         	# code...
//         	// dd($value);
//         	$sd = array();
//             // if($unit_conf == 'BOX')
//             // {
//             //     $match_val = (int)($value['title']*$converstion_unit);
//             // }
//             // else{
//             //     $match_val = (int)($value['title']);
//             // }   
        	
//             $match_val = (int)($value['title']);
//         	$qty = (int)($qty);
//         	// dD($match_val);
//         	if($qty <= $match_val)
//         	{
//         		$fq='';
// 	        	$fq = $value['title'];
//                 // $fqq[$fq]['value'] = str_replace(')', '',$value['value']).' = '.$fq.' )';
//                 $bill_qty_cus = $value['title']-$value['free_qty'];
//                 if($unit_conf == 'BOX')
//                 {
//                     $fqq[$fq]['value'] = $value['title']/$converstion_unit.'('.$bill_qty_cus.'+'.$value['free_qty'].'='.$value['title'].')';
//                     $fqq[$fq]['title'] = $value['title'];
//                     $fqq[$fq]['free_qty'] = $value['free_qty'];
//                 }
//                 else
//                 {
//                     $fqq[$fq]['value'] = $value['title'].'('.$bill_qty_cus.'+'.$value['free_qty'].'='.$value['title'].')';
//                     $fqq[$fq]['title'] = $value['title'];
//                     $fqq[$fq]['free_qty'] = $value['free_qty'];
//                 }
	        	
// 	        	// dd($fqq);
// 	        	$sd[] = $fqq;
//         	}
//         }
//         foreach ($sd as $key => $value) {
//         	# code...
//         	// dd($value);
//         	foreach ($value as $skey => $svalue) {
//         		# code...
//         		// dd($svalue);
//         		$final_deploy_out_f[] = $svalue;
//         	}
//         }

        
		
        
//         if(!empty($out) && COUNT($out)>0)
//         {
//             $data['code'] = 200;
//             $data['result'] =  $final_deploy_out_f;
//             $data['size'] =  !empty($scheme_details_1_step->ITEM_SIZE)?$scheme_details_1_step->ITEM_SIZE:'';
//             $data['prod'] = !empty($item_code_details)?$item_code_details:array();
//             $data['slabs'] = implode(',',$slabs);
//             $data['message'] = 'Found';
//         }
//         else
//         {
//             $data['code'] = 401;
//             $data['result'] = array();
//             $data['slabs'] = array();
//             $data['message'] = 'unauthorized request';
//         }
       
//         return ($data);
        
//     }
//     public function otc(Request $request){
//         // $from_target_date = "01-APR-" .(date('y')-1);
//         // $to_target_date = "31-MAR-" .(date('y'));
//         // $from_sale_date = (date('Y')-1)."-04-01";
//         // $to_sale_date = date('Y')."-03-31";
//         $from_target_date = "01-APR-" .(date('y'));
//         $to_target_date = "31-MAR-" .(date('y')+1);
//         $from_sale_date = (date('Y'))."-04-01";
//         $to_sale_date = (date('Y')+1)."-03-31";

//         $from_date_month_sale = date('Y-m').'-01';
//         $to_date_month_sale = date('Y-m-t');

//         if($request->check_status == '1')
//         {
//             $terr_id = explode(',',$request->terr_id);
//             $dealer_id_cus = explode(',',$request->dealer_id_cus);
//             $dealer_report_section_data_data = DB::table('dealer_report_section_data')
//                                     ->where('dealer_id',$this->dealer_id)
//                                     ->where('location_2','!=','0')
//                                     ->groupBy('location_2');
//                                     if(!empty($terr_id))
//                                     {
//                                         // dd($this->dealer_id);
//                                         $dealer_report_section_data_data->whereIn('location_2',$terr_id);
//                                     }
//             $dealer_report_section_data = $dealer_report_section_data_data->take(1)->pluck('location_2')->toArray();

//             // dd($dealer_report_section_data);
//             $dealer_id_data = DealerLocation::join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
//                         ->join('dealer','dealer_location_rate_list.dealer_id','=','dealer.id');
//                         if(!empty($dealer_id_cus))
//                         {
//                             $dealer_id_data->whereIn('dealer_id',$dealer_id_cus);
//                         }
//                         if(!empty($terr_id))
//                         {
//                             $dealer_id_data->whereIn('l2_id',$terr_id);
//                         }
//                         else
//                         {
//                             $dealer_id_data->whereIn('l2_id',$dealer_report_section_data);
//                         }
//             $dealer_id = $dealer_id_data->groupBy('dealer_id')->pluck('dealer_id')->toArray();
//             // dd($dealer_id);

//             $location_2_arr_cus = Location2::where('status',1)
//                                 ->join('dealer_report_section_data','dealer_report_section_data.location_2','=','location_2.id')
//                                 // ->where('location_2.id',$dealer_report_section_data)
//                                 ->where('dealer_id',$this->dealer_id)
//                                 ->groupBy('location_2.id')
//                                 ->pluck('location_2.name as name','location_2.id as id'); 
//             // dd($location_2_arr_cus);
//             $dealer_arr_cus = Dealer::where('dealer_status',1)
//                             ->join('dealer_location_rate_list','dealer.id','=','dealer_location_rate_list.dealer_id')
//                             ->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
//                             ->whereIn('l2_id',$dealer_report_section_data)
//                             // ->whereIn('id',$dealer_id)
//                             ->groupBy('dealer.id')
//                             ->pluck('dealer.name','dealer.id'); 

//             $dealer_code_filter_cus = DB::table('dealer')->where('dealer_status','1')->pluck('dealer_code','id'); 


//             $deiv_code_login = DB::table('dealer_person_login')->whereIn('dealer_id',$dealer_id)->pluck('div_code_main')->toArray(); 
//             $dealer_code = DB::table('dealer')->whereIn('id',$dealer_id)->pluck('dealer_code')->toArray(); 
//             // dd($div_code_entry);
           
//             $div_code = $deiv_code_login;
//         }
//         else
//         {
//             $dealer_code = $this->dealer_code;
//             $div_code_entry = DB::table('ACC_MAST')->where('ACC_CODE',$dealer_code)->first();

//             $deiv_code_login = DB::table('dealer_person_login')->where('dealer_id',$this->dealer_id)->first(); 
//             // dd($div_code_entry);
           
//             $div_code_else = !empty($deiv_code_login->div_code_main)?$deiv_code_login->div_code_main:'0';
//             $div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:$div_code_else;


//             $div_code =  array($div_code); 
//             $dealer_code =  array($dealer_code); 
//         }
        

        


//         $prod_catg_mast_array = DB::table('PROD_CATG_MAST')  
//                 ->where('PROD_CATG_MAST.MKTG_CATG','OTC')
//                 ->orderBy('PROD_CATG_MAST.PROD_CATG_NAME', 'ASC')
//                 ->get();

//         $um_array = DB::table('PROD_CATG_MAST')  
//                 ->join('ITEM_MAST','ITEM_MAST.PROD_CATG','=','PROD_CATG_MAST.PROD_CATG')
//                 ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//                 ->pluck('ITEM_AUM_MAST.AUM', 'PROD_CATG_MAST.PROD_CATG');

//         $prod_wise_target_mast_data = DB::table('DEALER_TARGET_MAST')
//                 ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','DEALER_TARGET_MAST.PROD_CATG')
//                 ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
//                 ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
//                 ->groupBy('DEALER_TARGET_MAST.PROD_CATG')
//                 ->orderBy('DEALER_TARGET_MAST.PROD_CATG', 'ASC')
//                 ->whereIn('DEALER_TARGET_MAST.ACC_CODE', $dealer_code)
//                 ->pluck(DB::raw('SUM(DEALER_TARGET_MAST.TARGET_QTY) as TARGET_QTY'), 'DEALER_TARGET_MAST.PROD_CATG as PROD_CATG');
        
//         $prod_wise_sales_mast_data_qty = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_sale_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_sale_date'") 
//             ->groupBy('PROD_CATG_MAST.PROD_CATG')
//             ->orderBy('PROD_CATG_MAST.PROD_CATG', 'ASC')
//             ->whereIn('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
//             ->pluck(DB::raw('SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED'), 'PROD_CATG_MAST.PROD_CATG as PROD_CATG');
//         // dd($prod_wise_sales_mast_data_qty);
//         $prod_wise_sales_mast_data_val = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_sale_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_sale_date'") 
//             ->groupBy('PROD_CATG_MAST.PROD_CATG')
//             ->orderBy('PROD_CATG_MAST.PROD_CATG', 'ASC')
//             ->whereIn('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
//             ->pluck(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'), 'PROD_CATG_MAST.PROD_CATG as PROD_CATG');



//         $month_prod_wise_sales_mast_data_qty = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_date_month_sale' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_date_month_sale'") 
//             ->groupBy('PROD_CATG_MAST.PROD_CATG')
//             ->orderBy('PROD_CATG_MAST.PROD_CATG', 'ASC')
//             ->whereIn('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
//             ->pluck(DB::raw('SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED'), 'PROD_CATG_MAST.PROD_CATG as PROD_CATG');
//         // dd($prod_wise_sales_mast_data_qty);
//         $month_prod_wise_sales_mast_data_val = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_date_month_sale' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_date_month_sale'") 
//             ->groupBy('PROD_CATG_MAST.PROD_CATG')
//             ->orderBy('PROD_CATG_MAST.PROD_CATG', 'ASC')
//             ->whereIn('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
//             ->pluck(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'), 'PROD_CATG_MAST.PROD_CATG as PROD_CATG');
//         // dd($prod_wise_sales_mast_data_val);
//             return view('DMS/QuickOrder.otc',[
//             'current_menu'=>$this->current_menu,
//             'prod_catg_mast_array' => $prod_catg_mast_array,
//             'prod_wise_target_mast'=>$prod_wise_target_mast_data,
//             'um_array'=>$um_array,
//             'prod_wise_sales_mast_data_qty' => $prod_wise_sales_mast_data_qty,
//             'prod_wise_sales_mast_data_val' => $prod_wise_sales_mast_data_val,
//             'month_prod_wise_sales_mast_data_qty'=>$month_prod_wise_sales_mast_data_qty,
//             'month_prod_wise_sales_mast_data_val'=>$month_prod_wise_sales_mast_data_val
//         ]);
//     }

//     public function ot2(Request $request){
//         $from_target_date = "01-APR-" .(date('y'));
//         $to_target_date = "31-MAR-" .(date('y')+1);
//         $from_sale_date = (date('Y'))."-04-01";
//         $to_sale_date = (date('Y')+1)."-03-31";

//         $from_date_month_sale = date('Y-m').'-01';
//         $to_date_month_sale = date('Y-m-t');

//         if($request->check_status == '1')
//         {
//             $terr_id = explode(',',$request->terr_id);
//             $dealer_id_cus = explode(',',$request->dealer_id_cus);
//             $dealer_report_section_data_data = DB::table('dealer_report_section_data')
//                                     ->where('dealer_id',$this->dealer_id)
//                                     ->where('location_2','!=','0')
//                                     ->groupBy('location_2');
//                                     if(!empty($terr_id))
//                                     {
//                                         // dd($this->dealer_id);
//                                         $dealer_report_section_data_data->whereIn('location_2',$terr_id);
//                                     }
//             $dealer_report_section_data = $dealer_report_section_data_data->take(1)->pluck('location_2')->toArray();

//             // dd($dealer_report_section_data);
//             $dealer_id_data = DealerLocation::join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
//                         ->join('dealer','dealer_location_rate_list.dealer_id','=','dealer.id');
//                         if(!empty($dealer_id_cus))
//                         {
//                             $dealer_id_data->whereIn('dealer_id',$dealer_id_cus);
//                         }
//                         if(!empty($terr_id))
//                         {
//                             $dealer_id_data->whereIn('l2_id',$terr_id);
//                         }
//                         else
//                         {
//                             $dealer_id_data->whereIn('l2_id',$dealer_report_section_data);
//                         }
//             $dealer_id = $dealer_id_data->groupBy('dealer_id')->pluck('dealer_id')->toArray();
//             // dd($dealer_id);

//             $location_2_arr_cus = Location2::where('status',1)
//                                 ->join('dealer_report_section_data','dealer_report_section_data.location_2','=','location_2.id')
//                                 // ->where('location_2.id',$dealer_report_section_data)
//                                 ->where('dealer_id',$this->dealer_id)
//                                 ->groupBy('location_2.id')
//                                 ->pluck('location_2.name as name','location_2.id as id'); 
//             // dd($location_2_arr_cus);
//             $dealer_arr_cus = Dealer::where('dealer_status',1)
//                             ->join('dealer_location_rate_list','dealer.id','=','dealer_location_rate_list.dealer_id')
//                             ->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
//                             ->whereIn('l2_id',$dealer_report_section_data)
//                             // ->whereIn('id',$dealer_id)
//                             ->groupBy('dealer.id')
//                             ->pluck('dealer.name','dealer.id'); 

//             $dealer_code_filter_cus = DB::table('dealer')->where('dealer_status','1')->pluck('dealer_code','id'); 


//             $deiv_code_login = DB::table('dealer_person_login')->whereIn('dealer_id',$dealer_id)->pluck('div_code_main')->toArray(); 
//             $dealer_code = DB::table('dealer')->whereIn('id',$dealer_id)->pluck('dealer_code')->toArray(); 
//             // dd($div_code_entry);
           
//             $div_code = $deiv_code_login;
//         }
//         else
//         {
//             $dealer_code = $this->dealer_code;
//             $div_code_entry = DB::table('ACC_MAST')->where('ACC_CODE',$this->dealer_code)->first();

//             $deiv_code_login = DB::table('dealer_person_login')->where('dealer_id',$this->dealer_id)->first(); 
//             // dd($div_code_entry);

//             $div_code_else = !empty($deiv_code_login->div_code_main)?$deiv_code_login->div_code_main:'0';
//             $div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:$div_code_else;

//             $div_code =  array($div_code); 
//             $dealer_code =  array($dealer_code); 
//         }
        


//         $prod_catg_mast_array = DB::table('PROD_CATG_MAST')  
//                 ->where('PROD_CATG_MAST.MKTG_CATG','OT2')
//                 ->orderBy('PROD_CATG_MAST.PROD_CATG_NAME', 'ASC')
//                 ->get();
//         // dd($prod_catg_mast_array);
//         $um_array = DB::table('PROD_CATG_MAST')  
//                 ->join('ITEM_MAST','ITEM_MAST.PROD_CATG','=','PROD_CATG_MAST.PROD_CATG')
//                 ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//                 ->pluck('ITEM_AUM_MAST.AUM', 'PROD_CATG_MAST.PROD_CATG');
//         // dd($um_array);
//         $prod_wise_target_mast_data = DB::table('DEALER_TARGET_MAST')
//                 ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','DEALER_TARGET_MAST.PROD_CATG')
//                 ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
//                 ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
//                 ->groupBy('DEALER_TARGET_MAST.PROD_CATG')
//                 ->orderBy('DEALER_TARGET_MAST.PROD_CATG', 'ASC')
//                 ->whereIn('DEALER_TARGET_MAST.ACC_CODE', $dealer_code)
//                 ->pluck(DB::raw('SUM(DEALER_TARGET_MAST.TARGET_QTY) as TARGET_QTY'), 'DEALER_TARGET_MAST.PROD_CATG as PROD_CATG');
//         // dd($prod_wise_target_mast_data);
         
//         $ot2_wise_target_data_1 = DB::table('DEALER_TARGET_MAST')
//             ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','DEALER_TARGET_MAST.MKTG_CATG')  
//             ->where('DEALER_TARGET_MAST.MKTG_CATG','OT2')
//             ->where('DEALER_TARGET_MAST.PROD_CATG','')
//             ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
//             ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
//             ->whereIn('DEALER_TARGET_MAST.ACC_CODE', $dealer_code)
//             ->first();
//         $ot2_wise_target_data = !empty($ot2_wise_target_data_1->TARGET_AMT)?$ot2_wise_target_data_1->TARGET_AMT:0;
//         // dd($ot2_wise_target_data);
//         $prod_wise_sales_mast_data_qty = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_sale_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_sale_date'") 
//             ->whereIn('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
//             ->groupBy('PROD_CATG_MAST.PROD_CATG')
//             ->orderBy('PROD_CATG_MAST.PROD_CATG', 'ASC')
//             ->pluck(DB::raw('SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED'), 'PROD_CATG_MAST.PROD_CATG as PROD_CATG');
//         // dd($prod_wise_sales_mast_data_qty);
//         $prod_wise_sales_mast_data_val = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_sale_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_sale_date'") 
//             ->whereIn('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
//             ->groupBy('PROD_CATG_MAST.PROD_CATG')
//             ->orderBy('PROD_CATG_MAST.PROD_CATG', 'ASC')
//             ->pluck(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'), 'PROD_CATG_MAST.PROD_CATG as PROD_CATG');


//         $month_prod_wise_sales_mast_data_qty = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_date_month_sale' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_date_month_sale'") 
//             ->whereIn('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
//             ->groupBy('PROD_CATG_MAST.PROD_CATG')
//             ->orderBy('PROD_CATG_MAST.PROD_CATG', 'ASC')
//             ->pluck(DB::raw('SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED'), 'PROD_CATG_MAST.PROD_CATG as PROD_CATG');
//         // dd($prod_wise_sales_mast_data_qty);
//         $month_prod_wise_sales_mast_data_val = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_date_month_sale' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_date_month_sale'") 
//             ->whereIn('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
//             ->groupBy('PROD_CATG_MAST.PROD_CATG')
//             ->orderBy('PROD_CATG_MAST.PROD_CATG', 'ASC')
//             ->pluck(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'), 'PROD_CATG_MAST.PROD_CATG as PROD_CATG');

//         return view('DMS/QuickOrder.ot2',[
//             'current_menu'=>$this->current_menu,
//             'prod_catg_mast_array' => $prod_catg_mast_array,
//             'prod_wise_target_mast'=>$prod_wise_target_mast_data,
//             'um_array'=>$um_array,
//             'ot2_wise_target_data'=>$ot2_wise_target_data,
//             'prod_wise_sales_mast_data_qty' => $prod_wise_sales_mast_data_qty,
//             'prod_wise_sales_mast_data_val' => $prod_wise_sales_mast_data_val,

//             'month_prod_wise_sales_mast_data_qty'=>$month_prod_wise_sales_mast_data_qty,
//             'month_prod_wise_sales_mast_data_val'=>$month_prod_wise_sales_mast_data_val
//         ]);
//     }
//     public function ethical(Request $request){
//         $from_target_date = "01-APR-" .(date('y'));
//         $to_target_date = "31-MAR-" .(date('y')+1);
//         $from_sale_date = (date('Y'))."-04-01";
//         $to_sale_date = (date('Y')+1)."-03-31";

//         $from_date_month_sale = date('Y-m').'-01';
//         $to_date_month_sale = date('Y-m-t');

//         if($request->check_status == '1')
//         {
//             $terr_id = explode(',',$request->terr_id);
//             $dealer_id_cus = explode(',',$request->dealer_id_cus);
//             $dealer_report_section_data_data = DB::table('dealer_report_section_data')
//                                     ->where('dealer_id',$this->dealer_id)
//                                     ->where('location_2','!=','0')
//                                     ->groupBy('location_2');
//                                     if(!empty($terr_id))
//                                     {
//                                         // dd($this->dealer_id);
//                                         $dealer_report_section_data_data->whereIn('location_2',$terr_id);
//                                     }
//             $dealer_report_section_data = $dealer_report_section_data_data->take(1)->pluck('location_2')->toArray();

//             // dd($dealer_report_section_data);
//             $dealer_id_data = DealerLocation::join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
//                         ->join('dealer','dealer_location_rate_list.dealer_id','=','dealer.id');
//                         if(!empty($dealer_id_cus))
//                         {
//                             $dealer_id_data->whereIn('dealer_id',$dealer_id_cus);
//                         }
//                         if(!empty($terr_id))
//                         {
//                             $dealer_id_data->whereIn('l2_id',$terr_id);
//                         }
//                         else
//                         {
//                             $dealer_id_data->whereIn('l2_id',$dealer_report_section_data);
//                         }
//             $dealer_id = $dealer_id_data->groupBy('dealer_id')->pluck('dealer_id')->toArray();
//             // dd($dealer_id);

//             $location_2_arr_cus = Location2::where('status',1)
//                                 ->join('dealer_report_section_data','dealer_report_section_data.location_2','=','location_2.id')
//                                 // ->where('location_2.id',$dealer_report_section_data)
//                                 ->where('dealer_id',$this->dealer_id)
//                                 ->groupBy('location_2.id')
//                                 ->pluck('location_2.name as name','location_2.id as id'); 
//             // dd($location_2_arr_cus);
//             $dealer_arr_cus = Dealer::where('dealer_status',1)
//                             ->join('dealer_location_rate_list','dealer.id','=','dealer_location_rate_list.dealer_id')
//                             ->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
//                             ->whereIn('l2_id',$dealer_report_section_data)
//                             // ->whereIn('id',$dealer_id)
//                             ->groupBy('dealer.id')
//                             ->pluck('dealer.name','dealer.id'); 

//             $dealer_code_filter_cus = DB::table('dealer')->where('dealer_status','1')->pluck('dealer_code','id'); 


//             $deiv_code_login = DB::table('dealer_person_login')->whereIn('dealer_id',$dealer_id)->pluck('div_code_main')->toArray(); 
//             $dealer_code = DB::table('dealer')->whereIn('id',$dealer_id)->pluck('dealer_code')->toArray(); 
//             // dd($div_code_entry);
           
//             $div_code = $deiv_code_login;
//         }
//         else
//         {
//             $dealer_code = $this->dealer_code;
//             $div_code_entry = DB::table('ACC_MAST')->where('ACC_CODE',$this->dealer_code)->first();

//             $deiv_code_login = DB::table('dealer_person_login')->where('dealer_id',$this->dealer_id)->first(); 
//             // dd($div_code_entry);

//             $div_code_else = !empty($deiv_code_login->div_code_main)?$deiv_code_login->div_code_main:'0';
//             $div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:$div_code_else;

//             $div_code =  array($div_code); 
//             $dealer_code =  array($dealer_code); 
//         }

        

//         $prod_catg_mast_array = DB::table('PROD_CATG_MAST')  
//                 ->where('PROD_CATG_MAST.MKTG_CATG','JPS')
//                 ->orderBy('PROD_CATG_MAST.PROD_CATG_NAME', 'ASC')
//                 ->get();
//         // dd($prod_catg_mast_array);
//         $um_array = DB::table('PROD_CATG_MAST')  
//                 ->join('ITEM_MAST','ITEM_MAST.PROD_CATG','=','PROD_CATG_MAST.PROD_CATG')
//                 ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//                 ->pluck('ITEM_AUM_MAST.AUM', 'PROD_CATG_MAST.PROD_CATG');
//         // dd($um_array);
//         $prod_wise_target_mast_data = DB::table('DEALER_TARGET_MAST')
//                 ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','DEALER_TARGET_MAST.PROD_CATG')
//                 ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
//                 ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
//                 ->whereIn('DEALER_TARGET_MAST.ACC_CODE', $dealer_code)
//                 ->groupBy('DEALER_TARGET_MAST.PROD_CATG')
//                 ->orderBy('DEALER_TARGET_MAST.PROD_CATG', 'ASC')
//                 ->pluck(DB::raw('SUM(DEALER_TARGET_MAST.TARGET_AMT) as TARGET_QTY'), 'DEALER_TARGET_MAST.PROD_CATG as PROD_CATG');
//         // dd($prod_wise_target_mast_data);
//         $prod_wise_sales_mast_data_qty = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_sale_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_sale_date'") 
//             ->groupBy('PROD_CATG_MAST.PROD_CATG')
//             ->orderBy('PROD_CATG_MAST.PROD_CATG', 'ASC')
//             ->whereIn('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
//             ->pluck(DB::raw('SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED'), 'PROD_CATG_MAST.PROD_CATG as PROD_CATG');
//         // dd($prod_wise_sales_mast_data_qty);
//         $prod_wise_sales_mast_data_val = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_sale_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_sale_date'") 
//             ->groupBy('PROD_CATG_MAST.PROD_CATG')
//             ->orderBy('PROD_CATG_MAST.PROD_CATG', 'ASC')
//             ->whereIn('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
//             ->pluck(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'), 'PROD_CATG_MAST.PROD_CATG as PROD_CATG');

//         $month_prod_wise_sales_mast_data_qty = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_date_month_sale' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_date_month_sale'") 
//             ->groupBy('PROD_CATG_MAST.PROD_CATG')
//             ->orderBy('PROD_CATG_MAST.PROD_CATG', 'ASC')
//             ->whereIn('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
//             ->pluck(DB::raw('SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED'), 'PROD_CATG_MAST.PROD_CATG as PROD_CATG');
//         // dd($prod_wise_sales_mast_data_qty);
//         $month_prod_wise_sales_mast_data_val = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_date_month_sale' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_date_month_sale'") 
//             ->groupBy('PROD_CATG_MAST.PROD_CATG')
//             ->orderBy('PROD_CATG_MAST.PROD_CATG', 'ASC')
//             ->whereIn('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
//             ->pluck(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'), 'PROD_CATG_MAST.PROD_CATG as PROD_CATG');

//             return view('DMS/QuickOrder.ethical',[
//             'current_menu'=>$this->current_menu,
//             'prod_catg_mast_array' => $prod_catg_mast_array,
//             'prod_wise_target_mast'=>$prod_wise_target_mast_data,
//             'um_array'=>$um_array,
//             'prod_wise_sales_mast_data_qty' => $prod_wise_sales_mast_data_qty,
//             'prod_wise_sales_mast_data_val' => $prod_wise_sales_mast_data_val,

//             'month_prod_wise_sales_mast_data_qty'=>$month_prod_wise_sales_mast_data_qty,
//             'month_prod_wise_sales_mast_data_val'=>$month_prod_wise_sales_mast_data_val,
//         ]);
//     }
//     public function fmcg(Request $request){
//         $from_target_date = "01-APR-" .(date('y'));
//         $to_target_date = "31-MAR-" .(date('y')+1);
//         $from_sale_date = (date('Y'))."-04-01";
//         $to_sale_date = (date('Y')+1)."-03-31";

//         $from_date_month_sale = date('Y-m').'-01';
//         $to_date_month_sale = date('Y-m-t');


//         if($request->check_status == '1')
//         {
//             $terr_id = explode(',',$request->terr_id);
//             $dealer_id_cus = explode(',',$request->dealer_id_cus);
//             $dealer_report_section_data_data = DB::table('dealer_report_section_data')
//                                     ->where('dealer_id',$this->dealer_id)
//                                     ->where('location_2','!=','0')
//                                     ->groupBy('location_2');
//                                     if(!empty($terr_id))
//                                     {
//                                         // dd($this->dealer_id);
//                                         $dealer_report_section_data_data->whereIn('location_2',$terr_id);
//                                     }
//             $dealer_report_section_data = $dealer_report_section_data_data->take(1)->pluck('location_2')->toArray();

//             // dd($dealer_report_section_data);
//             $dealer_id_data = DealerLocation::join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
//                         ->join('dealer','dealer_location_rate_list.dealer_id','=','dealer.id');
//                         if(!empty($dealer_id_cus))
//                         {
//                             $dealer_id_data->whereIn('dealer_id',$dealer_id_cus);
//                         }
//                         if(!empty($terr_id))
//                         {
//                             $dealer_id_data->whereIn('l2_id',$terr_id);
//                         }
//                         else
//                         {
//                             $dealer_id_data->whereIn('l2_id',$dealer_report_section_data);
//                         }
//             $dealer_id = $dealer_id_data->groupBy('dealer_id')->pluck('dealer_id')->toArray();
//             // dd($dealer_id);

//             $location_2_arr_cus = Location2::where('status',1)
//                                 ->join('dealer_report_section_data','dealer_report_section_data.location_2','=','location_2.id')
//                                 // ->where('location_2.id',$dealer_report_section_data)
//                                 ->where('dealer_id',$this->dealer_id)
//                                 ->groupBy('location_2.id')
//                                 ->pluck('location_2.name as name','location_2.id as id'); 
//             // dd($location_2_arr_cus);
//             $dealer_arr_cus = Dealer::where('dealer_status',1)
//                             ->join('dealer_location_rate_list','dealer.id','=','dealer_location_rate_list.dealer_id')
//                             ->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
//                             ->whereIn('l2_id',$dealer_report_section_data)
//                             // ->whereIn('id',$dealer_id)
//                             ->groupBy('dealer.id')
//                             ->pluck('dealer.name','dealer.id'); 

//             $dealer_code_filter_cus = DB::table('dealer')->where('dealer_status','1')->pluck('dealer_code','id'); 


//             $deiv_code_login = DB::table('dealer_person_login')->whereIn('dealer_id',$dealer_id)->pluck('div_code_main')->toArray(); 
//             $dealer_code = DB::table('dealer')->whereIn('id',$dealer_id)->pluck('dealer_code')->toArray(); 
//             // dd($div_code_entry);
           
//             $div_code = $deiv_code_login;
//         }
//         else
//         {
//             $dealer_code = $this->dealer_code;
//             $div_code_entry = DB::table('ACC_MAST')->where('ACC_CODE',$this->dealer_code)->first();

//             $deiv_code_login = DB::table('dealer_person_login')->where('dealer_id',$this->dealer_id)->first(); 
//             // dd($div_code_entry);

//             $div_code_else = !empty($deiv_code_login->div_code_main)?$deiv_code_login->div_code_main:'0';
//             $div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:$div_code_else;
//             $div_code =  array($div_code); 
//             $dealer_code =  array($dealer_code); 

//         }
        

//         $prod_catg_mast_array = DB::table('PROD_CATG_MAST')  
//                 ->where('PROD_CATG_MAST.MKTG_CATG','FMC')
//                 ->orderBy('PROD_CATG_MAST.PROD_CATG_NAME', 'ASC')
//                 ->get();
//         // dd($prod_catg_mast_array);
//         $um_array = DB::table('PROD_CATG_MAST')  
//                 ->join('ITEM_MAST','ITEM_MAST.PROD_CATG','=','PROD_CATG_MAST.PROD_CATG')
//                 ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//                 ->pluck('ITEM_AUM_MAST.AUM', 'PROD_CATG_MAST.PROD_CATG');
//         // dd($um_array);
//         $prod_wise_target_mast_data = DB::table('DEALER_TARGET_MAST')
//                 ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','DEALER_TARGET_MAST.PROD_CATG')
//                 ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
//                 ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
//                 ->groupBy('DEALER_TARGET_MAST.PROD_CATG')
//                 ->orderBy('DEALER_TARGET_MAST.PROD_CATG', 'ASC')
//                 ->whereIn('DEALER_TARGET_MAST.ACC_CODE', $dealer_code)
//                 ->pluck(DB::raw('SUM(DEALER_TARGET_MAST.TARGET_QTY) as TARGET_QTY'), 'DEALER_TARGET_MAST.PROD_CATG as PROD_CATG');
//         // dd($prod_wise_target_mast_data);
//         $prod_wise_sales_mast_data_qty = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_sale_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_sale_date'") 
//             ->groupBy('PROD_CATG_MAST.PROD_CATG')
//             ->orderBy('PROD_CATG_MAST.PROD_CATG', 'ASC')
//             ->whereIn('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
//             ->pluck(DB::raw('SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED'), 'PROD_CATG_MAST.PROD_CATG as PROD_CATG');
//         // dd($prod_wise_sales_mast_data_qty);
//         $prod_wise_sales_mast_data_val = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_sale_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_sale_date'") 
//             ->groupBy('PROD_CATG_MAST.PROD_CATG')
//             ->orderBy('PROD_CATG_MAST.PROD_CATG', 'ASC')
//             ->whereIn('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
//             ->pluck(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'), 'PROD_CATG_MAST.PROD_CATG as PROD_CATG');


//         $month_prod_wise_sales_mast_data_qty = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_date_month_sale' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_date_month_sale'") 
//             ->groupBy('PROD_CATG_MAST.PROD_CATG')
//             ->orderBy('PROD_CATG_MAST.PROD_CATG', 'ASC')
//             ->whereIn('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
//             ->pluck(DB::raw('SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED'), 'PROD_CATG_MAST.PROD_CATG as PROD_CATG');
//         // dd($prod_wise_sales_mast_data_qty);
//         $month_prod_wise_sales_mast_data_val = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_date_month_sale' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_date_month_sale'") 
//             ->groupBy('PROD_CATG_MAST.PROD_CATG')
//             ->orderBy('PROD_CATG_MAST.PROD_CATG', 'ASC')
//             ->whereIn('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->whereIn('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
//             ->pluck(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'), 'PROD_CATG_MAST.PROD_CATG as PROD_CATG');
//         // dd($prod_wise_sales_mast_data);
//         return view('DMS/QuickOrder.fmcg',[
//             'current_menu'=>$this->current_menu,
//             'prod_catg_mast_array' => $prod_catg_mast_array,
//             'prod_wise_target_mast'=>$prod_wise_target_mast_data,
//             'um_array'=>$um_array,
//             'prod_wise_sales_mast_data_qty' => $prod_wise_sales_mast_data_qty,
//             'prod_wise_sales_mast_data_val' => $prod_wise_sales_mast_data_val,
//             'month_prod_wise_sales_mast_data_qty'=>$month_prod_wise_sales_mast_data_qty,

//             'month_prod_wise_sales_mast_data_val'=>$month_prod_wise_sales_mast_data_val,

//         ]);
//     }

//     public function credit_debit_notes_ajax(Request $request){
        
//         $order_no = $request->order_no;
//         $status = 0;
//         if(empty($request->date_range_picker))  
//         {
//             if(!empty($request->range))
//             {
//                 $from_date = date('Y-m-d',strtotime('-'.$request->range.' day',strtotime($request->from_date)));
//                 $to_date = date('Y-m-d');
//                 $status = '1';
//             }
//             else
//             {
//                 $from_date = date('Y-m-d');
//                 $to_date = date('Y-m-d');
//             }
            
//         }
//         else
//         {
//             $explodeDate = explode(" -", $request->date_range_picker);
//             $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
//             $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
//         }
//         if($from_date <'2021-04-01')
//         {
//             $from_date = '2021-04-01';
//         }
//         $accnote_tran_data_DATA = DB::table('ACCNOTE_TRAN')
//                 ->whereRaw("date_format(ACCNOTE_TRAN.VRDATE_FILTER,'%Y-%m-%d')>='$from_date' and date_format(ACCNOTE_TRAN.VRDATE_FILTER,'%Y-%m-%d')<='$to_date'")
//                 ->where('ACCNOTE_TRAN.ACC_CODE', $this->dealer_code);
//                 if(!empty($order_no))
//                 {
//                     $accnote_tran_data_DATA->where('ACCNOTE_TRAN.VRNO','LIKE','%'.$order_no.'%');
//                 }
//         $accnote_tran_data = $accnote_tran_data_DATA->orderBy('VRDATE_FILTER','DESC')->get();
//         // dd($accnote_tran_data);
//         // $accnote_tran_data = [];
//         return view('DMS/CreditDebitNote.credit_debit_note_ajax',[
//             'current_menu'=>$this->current_menu,
//             'status'=> $status,
//             'accnote_tran_data' => $accnote_tran_data,
//         ]);
//     }

//     public function credit_debit_notes(Request $request)
//     {
//         return view('DMS/CreditDebitNote.credit_debit_notes');

//     }
//     public function invoice_details(Request $request)
//     {
//         // dd('1');
//         return view('DMS/InvoiceDetails.index');
        
//     }
//     public function dms_dealer_dashboard(Request $request)
//     {
// 		$dealer_code = $this->dealer_code;
//         // $dealer_code = '20602';

//         // $from_target_date = "01-APR-" .(date('y')-1);
//         // $to_target_date = "31-MAR-" .(date('y'));
//         // $from_sale_date = (date('Y')-1)."-04-01";
//         // $to_sale_date = date('Y')."-03-31";


//         $div_code_entry = DB::table('ACC_MAST')->where('ACC_CODE',$this->dealer_code)->first();

//         $deiv_code_login = DB::table('dealer_person_login')->where('dealer_id',$this->dealer_id)->first(); 
//         // dd($div_code_entry);
       
//         $div_code_else = !empty($deiv_code_login->div_code_main)?$deiv_code_login->div_code_main:'0';
//         $div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:$div_code_else;

//         $from_target_date = "01-APR-" .(date('y'));
//         $to_target_date = "31-MAR-" .(date('y')+1);
//         $from_sale_date = (date('Y'))."-04-01";
//         $to_sale_date = (date('Y')+1)."-03-31";

//         $from_date_month_sale = date('Y-m').'-01';
//         $to_date_month_sale = date('Y-m-t');

//         // $from_date = strtoupper(date('d-M-y'));
//         // $to_date = strtoupper(date('d-M-y'));
//         $paginate = !empty($request->perpage)?$request->perpage:'10';
//         $order_details = 'test';
//         $records = DB::table('demand_order')->where('dealer_id',$this->dealer_id)->paginate($paginate);
//         $converstion_unit_item_code = DB::table('ITEM_AUM_MAST')->pluck('UMTOAUM','ITEM_CODE');

//         $catalog_product_details = DB::table('WWW_WEB_ITEM_DISPLAY')->select(DB::raw("CONCAT(ITEM_NAME,' (',ITEM_CODE,')') AS ITEM_NAME"))->where('ITEM_STATUS','!=','C')->orderBy('ITEM_CODE','ASC')->get();
//         $mktg_cat_array = DB::table('MKTG_CATG_MAST')->orderBy('MKTG_CATG','ASC')->get();

//         $asv_wise_target_data = DB::table('DEALER_TARGET_MAST')
//                 ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','DEALER_TARGET_MAST.MKTG_CATG')  
//                 ->where('MKTG_CATG_MAST.MKTG_CATG','ASV')
//                 ->where('DEALER_TARGET_MAST.PROD_CATG','ASV')
//                 ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
//                 ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
//                 ->where('DEALER_TARGET_MAST.ACC_CODE', $this->dealer_code);
//                 // ->where('PROD_CATG','!=','')
//                 $asv_wise_target = !empty($asv_wise_target_data->first()->TARGET_QTY)?$asv_wise_target_data->first()->TARGET_QTY:0;
//         // dd($asv_wise_target);
//         $asv_wise_sale_data = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')
//             ->select(DB::raw('SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED'))
//             ->where('MKTG_CATG_MAST.MKTG_CATG', 'ASV')
//             ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_sale_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_sale_date'") 
//             ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
//             ->where('ITEMTRAN_HEAD.ACC_CODE', $this->dealer_code)
//             ->first();
        
//         $montha_sv_wise_sale_data = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//             ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
//             ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')
//             ->select(DB::raw('SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED'))
//             ->where('MKTG_CATG_MAST.MKTG_CATG', 'ASV')
//             ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_date_month_sale' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_date_month_sale'") 
//             ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
//             ->where('ITEMTRAN_HEAD.ACC_CODE', $this->dealer_code)
//             ->first();


//         $mktg_catg_array = array("ASV", "CLA", "GEN", "GLD", "OTC", "OT2", "JPS", "FMC");
//         for ($i=0; $i <= 7; $i++) { 
//             # code...
//             $mktg_catg_wise_target = DB::table('DEALER_TARGET_MAST')
//                 ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','DEALER_TARGET_MAST.MKTG_CATG')  
//                 ->where('DEALER_TARGET_MAST.MKTG_CATG',$mktg_catg_array[$i])
//                 ->where('DEALER_TARGET_MAST.PROD_CATG','')
//                 ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
//                 ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
//                 ->where('DEALER_TARGET_MAST.ACC_CODE', $this->dealer_code);
//                 $mktg_catg_wise_targets[$mktg_catg_array[$i]] = !empty($mktg_catg_wise_target->first()->TARGET_AMT)?$mktg_catg_wise_target->first()->TARGET_AMT:0;
//         }
//         // dd($mktg_catg_wise_targets);
//         for ($i=0; $i <= 7; $i++) { 
//             # code...
//             $mktg_catg_wise_sales[$mktg_catg_array[$i]] = DB::table('ITEMTRAN_BODY')
//             ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//             ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//             ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//             ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
//             ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_MAST.MKTG_CATG')
//             ->select(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'))
//             ->where('MKTG_CATG_MAST.MKTG_CATG', $mktg_catg_array[$i])
//             ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)
//             ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//             ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_sale_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_sale_date'") 
//             ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
//             ->where('ITEMTRAN_HEAD.ACC_CODE', $this->dealer_code)
//             ->first();
//         }

//         for ($i=0; $i <= 7; $i++) { 
//               # code...
//             $month_mktg_catg_wise_sales[$mktg_catg_array[$i]] = DB::table('ITEMTRAN_BODY')
//               ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
//               ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
//               ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
//               ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
//               ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_MAST.MKTG_CATG')
//               ->select(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'))
//               ->where('ITEMTRAN_HEAD.ACC_CODE', $this->dealer_code)
//               ->where('MKTG_CATG_MAST.MKTG_CATG', $mktg_catg_array[$i])
//               ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)
//               ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
//               ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$from_date_month_sale' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$to_date_month_sale'") 
//               ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
//               ->first();
//               // dd($mktg_catg_wise_sales);
//         }
//         // dd($mktg_catg_wise_sales);

//         $image_dyn = DB::table('image_mast')->where('img_locn','Dashboard')->get();

//         return view('DMS.dmsDealerDashboard',[
//                     'order_details'=> $order_details,
//                     'catalog_product_details'=> $catalog_product_details,
//                     'current_menu'=>$this->current_menu,
//                     'records'=> $records,
//                     'mktg_cat_array'=> $mktg_cat_array,
//                     'auth_id'=>$this->dealer_id,
//                     'converstion_unit_item_code'=> $converstion_unit_item_code,
//                     'image_dyn'=>$image_dyn,
//                     'asv_wise_target' => $asv_wise_target,
//                     // 'cla_wise_target' => $cla_wise_target,
//                     // 'gen_wise_target' => $gen_wise_target,
//                     // 'gld_wise_target' => $gld_wise_target,
//                     // 'ot2_wise_target' => $ot2_wise_target,

//                     'montha_sv_wise_sale_data'=>$montha_sv_wise_sale_data,
//                     'month_mktg_catg_wise_sales'=>$month_mktg_catg_wise_sales,
//                     'asv_wise_sale_data'=>$asv_wise_sale_data,
//                     'mktg_catg_wise_sales'=>$mktg_catg_wise_sales,
//                     'mktg_catg_wise_targets'=>$mktg_catg_wise_targets, 

//                 ]);
//         // return view('DMS.',[
//         //     // 'current_menu'=>$this->current_menu,
//         //     // 'accnote_tran_data' => $accnote_tran_data,
//         // ]);
//         // return view('DMS.');
        
//     }

//     public function check_scheme(Request $request)
//     {
//         $qty = $request->get('term', '');
//         // dd($url_name);
//         $out = array();
//         $date = $this->date;
//         $dealer_code = !empty($request->dealer_code)?$request->dealer_code:$this->dealer_code;
//         // $dealer_code = '20602';
//         // $qty = !empty($request->qty)?$request->qty:'21';
//         $unit_conf = !empty($request->unit_conf)?$request->unit_conf:'';
//         $scheme_details = DB::table('CIRCULAR_MAST')
//                         ->join('SCHEME_MAST','SCHEME_MAST.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                         ->join('SCHEME_SLAB','SCHEME_SLAB.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                         ->join('SCHEME_DOMAIN','SCHEME_DOMAIN.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                         ->whereRaw("DATE_FORMAT(CIRCULAR_MAST.from_date_condition ,'%Y-%m-%d')<='$date' AND DATE_FORMAT(CIRCULAR_MAST.to_date_condition,'%Y-%m-%d') >= '$date'")
//                         // ->whereRaw("CIRCULAR_MAST.from_date_condition >= '$date' and CIRCULAR_MAST.to_date_condition <= '$date' ")
//                         // ->orWhere('CANCELLEDBY','')
//                         // ->orWhere('CANCELLEDBY',' ')
//                         // ->orWhereRaw("CANCELLEDBY is NULL")
//                         ->where('SCHEME_DOMAIN.ACC_CODE',$dealer_code)
//                         ->groupBy('SCHEME_DOMAIN.SCHEME_NO') // ac
//                         ->get();
//         // dd($scheme_details);
//         $item_code = !empty($request->item_code)?$request->item_code:'0';

//         foreach ($scheme_details as $key => $value) {
//             # code...
//             if($value->CANCELLEDBY == '' || $value->CANCELLEDBY == ' ' || $value->CANCELLEDBY == NULL || $value->CANCELLEDBY == 'NULL'  )
//             {
//                 // dd($value);
//                 $scheme_no_step1[] = $value->SCHEME_NO;
//             }
//         }
//         // dd($scheme_no_step1);
//         $srtring = implode(',', $scheme_no_step1);
//         // dd($srtring);
//         $scheme_details_1_step = DB::table('CIRCULAR_MAST')
//                 ->join('SCHEME_MAST','SCHEME_MAST.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                 ->join('SCHEME_SLAB','SCHEME_SLAB.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                 ->join('SCHEME_DOMAIN','SCHEME_DOMAIN.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                 ->whereRaw("DATE_FORMAT(CIRCULAR_MAST.from_date_condition ,'%Y-%m-%d')<='$date' AND DATE_FORMAT(CIRCULAR_MAST.to_date_condition,'%Y-%m-%d') >= '$date'")
//                 // ->whereRaw("CIRCULAR_MAST.from_date_condition >= '$date' and CIRCULAR_MAST.to_date_condition <= '$date' ")
//                 // ->orWhere('CANCELLEDBY','')
//                 // ->orWhere('CANCELLEDBY',' ')
//                 // ->orWhereRaw("CANCELLEDBY is NULL")
//                 ->whereIn('SCHEME_DOMAIN.SCHEME_NO',$scheme_no_step1)
//                 // ->where('SCHEME_DOMAIN.ACC_CODE',$dealer_code)
//                 ->where('SCHEME_DOMAIN.ITEM_CODE',$item_code)
//                 ->groupBy('SCHEME_DOMAIN.SCHEME_NO')
//                 ->orderBy('SCHEME_DOMAIN.id','DESC')
//                 ->first();
//         // dd($scheme_details_1_step);
//         if(!empty($scheme_details_1_step) && COUNT($scheme_details_1_step)>0)
//         {
//             $final_scheme_details_1_step_array = DB::table('SCHEME_SLAB')
//                                 ->where('SCHEME_SLAB.SCHEME_NO',$scheme_details_1_step->SCHEME_NO)
//                                 ->get();
//             // dd($scheme_details_1_step_array);
//         }
//         else
//         {
//             $item_code_details = DB::table('ITEM_MAST')->where('ITEM_CODE',$item_code)->pluck('PROD_CODE');
//             // dd($item_code_details);
//             $item_code_details_size = DB::table('ITEM_MAST')->where('ITEM_CODE',$item_code)->first();
//             $scheme_details_1_step = DB::table('CIRCULAR_MAST')
//                 ->join('SCHEME_MAST','SCHEME_MAST.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                 ->join('SCHEME_SLAB','SCHEME_SLAB.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                 ->join('SCHEME_DOMAIN','SCHEME_DOMAIN.SCHEME_NO','=','CIRCULAR_MAST.SCHEME_NO')
//                 ->join('ITEM_MAST','ITEM_MAST.PROD_CODE','=','SCHEME_DOMAIN.PROD_CODE')
//                 ->select('ITEM_MAST.ITEM_SIZE as ITEM_SIZE','SCHEME_DOMAIN.SCHEME_NO as SCHEME_NO')
//                 ->whereRaw("DATE_FORMAT(CIRCULAR_MAST.from_date_condition ,'%Y-%m-%d')<='$date' AND DATE_FORMAT(CIRCULAR_MAST.to_date_condition,'%Y-%m-%d') >= '$date'")
//                 // ->whereRaw("CIRCULAR_MAST.from_date_condition >= '$date' and CIRCULAR_MAST.to_date_condition <= '$date' ")
//                 // ->orWhere('CANCELLEDBY','')
//                 // ->orWhere('CANCELLEDBY',' ')
//                 // ->orWhereRaw("CANCELLEDBY is NULL")
//                 ->whereIn('SCHEME_DOMAIN.SCHEME_NO',$scheme_no_step1)
//                 // ->where('SCHEME_DOMAIN.ACC_CODE',$dealer_code)
//                 ->whereIn('SCHEME_DOMAIN.PROD_CODE',$item_code_details)
//                 ->groupBy('SCHEME_DOMAIN.SCHEME_NO')
//                 ->orderBy('SCHEME_DOMAIN.id','DESC')
//                 ->first();
//             // dd($scheme_details_1_step);

//             if(!empty($scheme_details_1_step) && COUNT($scheme_details_1_step)>0)
//             {
//                 $final_scheme_details_1_step_array = DB::table('SCHEME_SLAB')
//                                 ->where('SCHEME_SLAB.SCHEME_NO',$scheme_details_1_step->SCHEME_NO)
//                                 ->where('ITEM_SIZE',$item_code_details_size->ITEM_SIZE)
//                                 ->orderBy('SCHEME_SLAB.id','DESC')
//                                 ->get();
//                 if(!empty($final_scheme_details_1_step_array) && COUNT($final_scheme_details_1_step_array)>0)
//                 {
//                     $final_scheme_details_1_step_array = DB::table('SCHEME_SLAB')
//                                 ->where('SCHEME_SLAB.SCHEME_NO',$scheme_details_1_step->SCHEME_NO)
//                                 ->where('ITEM_SIZE',$item_code_details_size->ITEM_SIZE)
//                                 ->orderBy('SCHEME_SLAB.id','DESC')
//                                 ->get();
//                 }
//                 else
//                 {
//                     $final_scheme_details_1_step_array = DB::table('SCHEME_SLAB')
//                                 ->where('SCHEME_SLAB.SCHEME_NO',$scheme_details_1_step->SCHEME_NO)
//                                 // ->where('ITEM_SIZE',$item_code_details_size->ITEM_SIZE)
//                                 ->orderBy('SCHEME_SLAB.id','DESC')
//                                 ->get();
//                 }
//                 // dd($final_scheme_details_1_step_array);
//             }


//         }
//         // dd($item_code);
//         $converstion_unit_item_code = DB::table('ITEM_AUM_MAST')->where('ITEM_CODE',$item_code)->first();
//         $converstion_unit = !empty($converstion_unit_item_code->UMTOAUM)?$converstion_unit_item_code->UMTOAUM:'0';
//         // dd($final_scheme_details_1_step_array);

//         $slabs = array();
//         $out_value_outer = '';
//         if(!empty($final_scheme_details_1_step_array) && COUNT($final_scheme_details_1_step_array)>0)
//         {
//             // dd('1');
//             $out = [];
//             $slabs = [];
//             $sum_value = '';
//             $sum_value_inner = '';
//             $free_inner = '';
//             $inner_sum_value = '';
//             $out_value = '';
//             foreach ($final_scheme_details_1_step_array as $s_key => $s_value) {
//                 # code...
//                 // dd($value);
//                 if($s_value->FREE_ITEM1_QTY1 != '' && $s_value->BASE_FROM != '' ){
//                     $FREE_ITEM1_QTY1 = !empty($s_value->FREE_ITEM1_QTY1)?$s_value->FREE_ITEM1_QTY1:'0';
//                     $BASE_FROM = !empty($s_value->BASE_FROM)?$s_value->BASE_FROM:'0';
//                     // $inner_sum_value = $sum_value_inner+$free_inner;
//                     if($unit_conf == 'BOX1')
//                     {
//                         // dd($item_code);
//                         // dd($converstion_unit_item_code);
                        
//                         $sum_value = $BASE_FROM+$FREE_ITEM1_QTY1;
//                         $sum_value_inner = (($BASE_FROM)*$converstion_unit);
//                         $free_inner = (($FREE_ITEM1_QTY1)*$converstion_unit);
//                         $inner_sum_value = ($free_inner+$sum_value_inner);
//                         // $sum_value = $value->BASE_FROM
//                         // $out_value = $sum_value.'('.$sum_value_inner.'+'.$free_inner.'= '.$inner_sum_value.')';
//                         $out_value = $sum_value.'('.$sum_value_inner.'+'.$free_inner.')';

//                     // dd($unit_conf);

//                     }
//                     else
//                     {
//                         $sum_value = $BASE_FROM+$FREE_ITEM1_QTY1;
//                         $free_inner = $FREE_ITEM1_QTY1;
//                         $sum_value_inner = $BASE_FROM;

//                         // $out_value = $sum_value.'('.$BASE_FROM.'+'.$FREE_ITEM1_QTY1.'= '.$sum_value.')';
//                         $out_value = $sum_value.'('.$BASE_FROM.'+'.$FREE_ITEM1_QTY1.')';
//                     }
//                     // dd('1');
                    

//                     $slabs_testing =$sum_value.'|'.$BASE_FROM.'|'.$FREE_ITEM1_QTY1;
//                     // $slabs[] ='  '. $sum_value.' ('.$BASE_FROM.'+'.$FREE_ITEM1_QTY1.'= '.$sum_value.' ) ';
//                     $slabs[] ='  '. $sum_value.' ('.$BASE_FROM.'+'.$FREE_ITEM1_QTY1.'= '.$sum_value.' ) ';

//                     $out[] = array('value'=>$out_value, 'title' => $sum_value,'free_qty'=>$free_inner,'slabs_testing'=>$slabs_testing); 
//                  }
//             }
//         }
//         else
//         {
//             $out = [];
//             $slabs_testing =$qty.'|'.$qty.'|'.'0';
//             if($unit_conf == 'BOX')
//             {
//                 $out_value_outer = $qty.'('.$qty*$converstion_unit.'+ 0 = '.$qty*$converstion_unit.')'; 
//                 $out[] = array('value'=>$out_value_outer, 'title' => $qty,'free_qty'=>"0",'slabs_testing'=>$slabs_testing);
//             }
//             else
//             {
//                 $out_value_outer = $qty.'('.$qty.'+ 0 = '.$qty.')'; 
//                 $out[] = array('value'=>$out_value_outer, 'title' => $qty,'free_qty'=>"0",'slabs_testing'=>$slabs_testing);
//             }
            

//         }

//         // dd(($out));
//         $set_value = 1;
//         if($unit_conf == 'BOX1')
//         {
//             $qty = $qty*$converstion_unit;
//         }

//         foreach ($out as $key => $value) {
//             # code...
//             $set_value = 0;
//             // dd($value);
//             // dd($value);
//             if(!empty($out[$key+1]))
//             {

//                 if($qty > $value['title'] && $qty < $out[$key+1]['title'])
//                 {
//                     // dd($value['title']);
//                     $title = $value['title'];
//                     $free_qty = $value['free_qty'];
//                     $final_value_billed = $title-$free_qty;

//                     if(!empty($out[$key+1]))
//                     {
//                         $another_slab_title = $out[$key+1]['title'];
//                     }
//                     else
//                     {
//                         $another_slab_title = 0;
//                     }
//                     break;


//                 }

//                 elseif($value['title']>=$qty  )
//                 {
//                     $title = $value['title'];
//                     $free_qty = $value['free_qty'];
//                     $final_value_billed = $title-$free_qty;

//                     if(!empty($out[$key+1]))
//                     {
//                         $another_slab_title = $out[$key+1]['title'];
//                     }
//                     else
//                     {
//                         $another_slab_title = 0;
//                     }
//                     // dd($value);
//                     break;
//                 }
//             }
//             else
//             {
//                 // dd($qty);
//                 if($qty > $value['title'] )
//                 {
//                     // dd($value['title']);
//                     $title = $value['title'];
//                     $free_qty = $value['free_qty'];
//                     $final_value_billed = $title-$free_qty;

//                     if(!empty($out[$key+1]))
//                     {
//                         $another_slab_title = $out[$key+1]['title'];
//                     }
//                     else
//                     {
//                         $another_slab_title = 0;
//                     }


//                 }

//                 elseif($value['title']>=$qty  )
//                 {
//                     $title = $value['title'];
//                     $free_qty = $value['free_qty'];
//                     $final_value_billed = $title-$free_qty;

//                     if(!empty($out[$key+1]))
//                     {
//                         $another_slab_title = $out[$key+1]['title'];
//                     }
//                     else
//                     {
//                         $another_slab_title = 0;
//                     }
//                     // dd($value);
//                     break;
//                 }
//             }
//         }
//         // dd($out,$another_slab_title);
//         $final_deploy_out = [];
//         if($set_value == 0)
//         {
//             $free_qty = $free_qty;
//             $title = $title;
//             $final_value_billed = $final_value_billed;
//             $divide_perc = ($free_qty/$final_value_billed)*100;

//             $given_qty = $qty;
//             // dd($given_qty);
//             $step1_perc_value = round($given_qty/$title);
//             for ($i=0; $i < 10; $i++) { 
//                 # code...
//                 if($step1_perc_value == 0)
//                 {
//                     $step1_perc_value = 1;
//                 }
//                 // elseif($step1_perc_value == 1)
//                 // {
//                 //     $step1_perc_value = 2;
//                 // }
//                 // if()
//                 $step2_free_qty = $free_qty*$step1_perc_value;
//                 $step3_billed_qty = $final_value_billed*$step1_perc_value;
//                 $step4_title = $step3_billed_qty+$step2_free_qty;
//                 if($another_slab_title != 0 && $step4_title <= $another_slab_title)
//                 {
//                     $final_deploy['value'] = $step4_title.' ('.$step3_billed_qty.'+'.$step2_free_qty.')';
//                     $final_deploy['title'] = $step4_title;
//                     $final_deploy['free_qty'] = $step2_free_qty;
//                     // $final_deploy[] = 
//                     // $final_deploy[] = value
//                     $final_deploy_out[] = $final_deploy;

//                 }
//                 elseif($another_slab_title == 0)
//                 {
//                     $final_deploy['value'] = $step4_title.' ('.$step3_billed_qty.'+'.$step2_free_qty.')';
//                     $final_deploy['title'] = $step4_title;
//                     $final_deploy['free_qty'] = $step2_free_qty;
//                 // dd($final_deploy,$step1_perc_value);free_qty
//                     $final_deploy_out[] = $final_deploy;

//                 }
//                 $step1_perc_value = $step1_perc_value+1;
//                 $step1_perc_value1[] = $step1_perc_value;
//             }
//             $count_details = COUNT($final_deploy_out);

//             // if($)
//             if(empty($final_deploy_out))
//             {
//                 $last_key = 0;

//             }
//             else
//             {
//                 $last_key = $final_deploy_out[$count_details-1]['title'];
//             }
//             foreach ($out as $key => $value) {
//                 # code...
//                 // dd($value);
//                 if($last_key != $value['title'] && $last_key <= $value['title'])
//                 {
//                     $final_deploy['value'] = $value['value'];
//                     $final_deploy['title'] = $value['title'];
//                     $final_deploy['free_qty'] = $value['free_qty'];
//                     $final_deploy_out[] = $final_deploy;

//                 }
//             }
//                 // dd($final_deploy_out,$count_details);

//         }
//         else // use when no scheme is there 
//         {
//             $puda_size_col = DB::table('WWW_WEB_ITEM_DISPLAY')
//                             ->where('ITEM_CODE',$item_code)
//                             ->first();
//             $puda_size = $puda_size_col->PUDA_SIZE;
//             // dd($puda_size);
//             $out = [];
//             $slabs_testing =$qty.'|'.$qty.'|'.'0';
//             if($unit_conf == 'BOX')
//             {
//                 $out_value_outer = $qty.'('.$qty*$converstion_unit.'+ 0 = '.$qty*$converstion_unit.')'; 
//                 $out[] = array('value'=>$out_value_outer, 'title' => $qty,'free_qty'=>"0",'slabs_testing'=>$slabs_testing);
//             }
//             else
//             {
//                 $out_value_outer = $qty.'('.$qty.'+ 0 = '.$qty.')'; 
//                 $out[] = array('value'=>$out_value_outer, 'title' => $qty,'free_qty'=>"0",'slabs_testing'=>$slabs_testing);
//             }
//             // dd($out);
//             $step1_perc_value = $puda_size;
//             $final_value_billed = $qty;
//             $free_qty = 0;
//             for ($i=0; $i < 10; $i++) { 
//                 # code...
//                 if($step1_perc_value == 0)
//                 {
//                     $step1_perc_value = 1;
//                 }
//                 // elseif($step1_perc_value == 1)
//                 // {
//                 //     $step1_perc_value = 2;
//                 // }
//                 // if()
//                 $step2_free_qty = $free_qty*$step1_perc_value;
//                 $step3_billed_qty = $final_value_billed*$step1_perc_value;
//                 $step4_title = $step3_billed_qty+$step2_free_qty;
               
//                 $final_deploy['value'] = $step4_title.' ('.$step3_billed_qty.'+'.$step2_free_qty.')';
//                 $final_deploy['title'] = $step4_title;
//                 $final_deploy['free_qty'] = $step2_free_qty;
//             // dd($final_deploy,$step1_perc_value);free_qty
//                 $final_deploy_out[] = $final_deploy;

               
//                 $step1_perc_value = $step1_perc_value+1;
//                 $step1_perc_value1[] = $step1_perc_value;
//             }
//         }
//         // dd($final_deploy_out,$step1_perc_value1);
//         $puda_size_col = DB::table('WWW_WEB_ITEM_DISPLAY')
//                         ->where('ITEM_CODE',$item_code)
//                         ->first();
//         $puda_size = $puda_size_col->PUDA_SIZE;
//         $forbreaking_loop = $final_deploy_out;
//         foreach ($out as $key => $value) {
//             # code...
//             // dd($value);

//             $title = $value['title'];
//             $free_qty = $value['free_qty'];
//             $value_ins = $value['value'];
//             $perc_matching = $free_qty/$title;
//             $for_per_check1 = $title-$free_qty;
//             $final_per_check_for_comp = $for_per_check1/$free_qty;
//             // dd($final_per_check_for_comp);
//             $loop_cond1 = $title/$puda_size;
//             // $loop_cond2 = $loop_cond1*8;
//             // $loop_cond3 = $loop_cond2+$title;
//             // dd($loop_cond3);
//             // for ($i=0; $i < ; $i++) { 
//             //     # code...
//             // }
//                 # code...

//             if(!empty($forbreaking_loop[$key+1]))
//             {
//                 for ($i=0; $i <9 ; $i++) { 
//                     // code...
//                     // dd($final_deploy_out[$key+1]['title']);

//                     if($title == $forbreaking_loop[$key+1]['title'])
//                     {
//                         // dd($title);
//                         break;
//                     }
//                     else
//                     {
//                         $step1 = $title+$puda_size;
//                         $step2 = (int)($step1*$perc_matching);
//                         $title = $step1;
//                         $free_qty = $step2;
//                         $bill_qty = $title-$free_qty;

//                         $final_deploy['value'] = $title.' ('.$bill_qty.'+'.$free_qty.')';
//                         $final_deploy['title'] = $title;
//                         $final_deploy['free_qty'] = $free_qty;
//                         $perc_check = $bill_qty/$free_qty;
//                         // dd($perc_check);
//                         if($perc_check == $final_per_check_for_comp)
//                         {
//                             // dd('1');
//                             if($title != $forbreaking_loop[$key+1]['title'])
//                             {
//                                 $final_deploy_out[] = $final_deploy;
//                             }
//                         }
//                     }
                    
                    

                    
//                     // if()
//                 }
//                 asort($final_deploy_out);
//                 $final_deploy_out_c = array_column($final_deploy_out, 'title');

//                 array_multisort($final_deploy_out_c, SORT_ASC, $final_deploy_out);
//                 // dd($final_deploy_out,$perc_check);

//             }
//             else
//             {
//                 for ($i=0; $i <10 ; $i++) { 
//                     // code...
//                     // dd($final_deploy_out[$key+1]['title']);

//                         $step1 = $title+$puda_size;
//                         $step2 = (int)($step1*$perc_matching);
//                         $title = $step1;
//                         $free_qty = $step2;
//                         $bill_qty = $title-$free_qty;

//                         $final_deploy['value'] = $title.' ('.$bill_qty.'+'.$free_qty.')';
//                         $final_deploy['title'] = $title;
//                         $final_deploy['free_qty'] = $free_qty;
//                         $perc_check = $bill_qty/$free_qty;
//                         // dd($perc_check);
//                         if($perc_check == $final_per_check_for_comp)
//                         {
//                             // dd('1');
                            
//                                 $final_deploy_out[] = $final_deploy;
//                         }

//                 }
//                 // asort($final_deploy_out);
//                 $final_deploy_out_c = array_column($final_deploy_out, 'title');

//                 array_multisort($final_deploy_out_c, SORT_ASC, $final_deploy_out);
//             }

           
            
//         }
//         // dd($final_deploy_out);
//         $sd = array();
//         $final_deploy_out_f = array();
//         foreach ($final_deploy_out as $key => $value) {
//         	# code...
//         	// dd($value);
//         	// dd($qty);
//         	$sd = array();
//         	$match_val = (int)($value['title']);
//         	$qty = (int)($qty);
//         	// dD($match_val);
//         	if($qty <= $match_val)
//         	{
//         		$fq='';
// 	        	$fq = $value['title'];
// 	        	$fqq[$fq]['value'] = $value['value'];
// 	        	$fqq[$fq]['title'] = $value['title'];
// 	        	$fqq[$fq]['free_qty'] = $value['free_qty'];
// 	        	// dd($fqq);
// 	        	$sd[] = $fqq;
//         	}
//         	// else
//         	// {
//         	// 	$match_val1[] = $match_val;
//         	// }
        	
//         }
//         // dd($sd);
//         foreach ($sd as $key => $value) {
//         	# code...
//         	// dd($value);
//         	foreach ($value as $skey => $svalue) {
//         		# code...
//         		// dd($svalue);
//         		$final_deploy_out_f[] = $svalue;
//         	}
//         }
//         // dd($final_deploy_out_f,$final_deploy_out);


//         // $input = array_map("unserialize", array_unique(array_map("serialize", $final_deploy_out)));
//         // dd($input);
            
        
//         if(!empty($out) && COUNT($out)>0)
//         {
//             $data['code'] = 200;
//             $data['result'] =  $final_deploy_out_f;
//             $data['size'] =  !empty($scheme_details_1_step->ITEM_SIZE)?$scheme_details_1_step->ITEM_SIZE:'';
//             $data['prod'] = $item_code_details;
//             $data['slabs'] = implode(',',$slabs);
//             $data['message'] = 'Found';
//         }
//         else
//         {
//             $data['code'] = 401;
//             $data['result'] = array();
//             $data['slabs'] = array();
//             $data['message'] = 'unauthorized request';
//         }
       
//         return ($data);
//     }
//     public function dms_order_delete_function(Request $request)
//     {
//         // dd($request);
//         $pid = $request->id;
//         $uid = $request->order_id;
//         $product_id = $request->product_id;
//         $delete_quer_pasr = DB::table('demand_order_details_cart')
//                             ->where('product_id',$product_id)
//                             ->where('id',$pid)->delete();

//         $converstion_unit_item_code = DB::table('ITEM_AUM_MAST')->groupBy('ITEM_CODE')->pluck('UMTOAUM','ITEM_CODE');

//         $mktg_cat_array = DB::table('MKTG_CATG_MAST')
//                             ->orderBy('MKTG_CATG','ASC')
//                             ->pluck('MKTG_CATG_NAME as v', 'MKTG_CATG as k');

//         $order_details = DB::table('demand_order_cart')
//                         ->join('dealer','dealer.id','=','demand_order_cart.dealer_id')
//                         ->select('dealer.*')
//                         ->where('demand_order_cart.order_id',$uid)->first();
//         // dd($order_details);
//         $dealer_details = DB::table('ACC_MAST')->where('ACC_CODE',$order_details->dealer_code)->first();
//         $final_out = array();

//         foreach ($mktg_cat_array as $key => $value) {
//             $record = DB::table('demand_order_cart')
//                     ->join('demand_order_details_cart','demand_order_details_cart.order_id','=','demand_order_cart.order_id')  
//                     ->join('WWW_WEB_ITEM_DISPLAY','WWW_WEB_ITEM_DISPLAY.ITEM_CODE','=','demand_order_details_cart.product_id')  
//                     ->select('demand_order_cart.*','demand_order_details_cart.*','demand_order_details_cart.id as pid','WWW_WEB_ITEM_DISPLAY.*')
//                     ->where('WWW_WEB_ITEM_DISPLAY.MKTG_CATG', $key)
//                     ->where('demand_order_cart.dealer_id',$this->dealer_id) 
//                     ->where('demand_order_cart.order_id',$uid) 
//                     ->groupBy('demand_order_details_cart.id')
//                     ->get()
//                     ->toArray();
//             // dd($key);
//             $out_1[$key] = $record;
//             $out_2 = [];
//             foreach ($record as $k => $v) {
//                 //$discount = self::common_discount_function($v->dealer_id, $v->product_id, $v->total_rs);
//                 // dd($discount);
//                 $order_date = $v->order_date;
//                 $out_4s['total_rs'] = $v->total_rs;
//                 $out_4s['pid'] = $v->pid;
//                 $out_4s['order_id'] = $v->order_id;
//                 $out_4s['order_converstion_unit'] = $v->order_converstion_unit;
//                 $out_4s['ITEM_CODE'] = $v->ITEM_CODE;
//                 $out_4s['ITEM_NAME'] = $v->ITEM_NAME;
//                 $out_4s['rate'] = $v->rate;
//                 $out_4s['order_unit'] = $v->order_unit;
//                 $out_4s['quantity'] = $v->quantity;
//                 $out_4s['free_qty'] = $v->free_qty;
//                 $out_4s['remarks'] = $v->remarks;
//                 $out_4s['t1_rate'] = $v->t1_rate;
//                 $out_4s['atd_rate'] = $v->atd_rate;
//                 $out_2[$key][] = $out_4s;
//             }
//             $final_out[] = $out_2;
//         // dd($final_out);
        
//         }
//         $item_aum_mast_array = DB::table('ITEM_AUM_MAST')
//                         ->pluck('UMTOAUM as v', 'ITEM_CODE as k');
//         return view('DMS/QuickOrder.edit',[
//             'current_menu'=>$this->current_menu,
//             'auth_id'=>$this->dealer_id,
//             'party_name'=> !empty($dealer_details->ACC_NAME)?$dealer_details->ACC_NAME:'',
//             'order_id'=> 'B-'.$uid,
//             'order_id_for_use'=>$uid,
//             'order_date'=> !empty($order_date)?$order_date:'',
//             'converstion_unit_item_code'=>$converstion_unit_item_code,
//             'mktg_cat_array'=>$mktg_cat_array,
//             'final_out'=>$final_out,
//             'item_aum_mast_array' => $item_aum_mast_array,
//         ]);
//     }
}
