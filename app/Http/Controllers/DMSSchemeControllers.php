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
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;
use PDF;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class DMSSchemeControllers extends Controller
{
    public function __construct()
    {
        session_start();
        // dd($_SESSION);
        $this->signup_status = !empty($_SESSION['iclientdigimetsignup_status'])?$_SESSION['iclientdigimetsignup_status']:'0';
        // dd($this->signup_status);
        if($this->signup_status == 0 || $this->signup_status == '0')
        {
            header('Location: http://demo.msell.in/public/Signup');
            dd('1');
        }
        $auth_id = !empty($_SESSION['iclientdigimetid'])?$_SESSION['iclientdigimetid']:'0';
        $this->auth_id = $auth_id; 
        $this->dealer_id = !empty($_SESSION['iclientdigimetdata']['dealer_id'])?$_SESSION['iclientdigimetdata']['dealer_id']:'0';
        $this->csa_id = !empty($_SESSION['iclientdigimetdata']['csa_id'])?$_SESSION['iclientdigimetdata']['csa_id']:'0';
        $this->dealer_code = !empty($_SESSION['iclientdigimetdata']['dealer_code'])?$_SESSION['iclientdigimetdata']['dealer_code']:'0';
        $this->role_id = !empty($_SESSION['iclientdigimetdata']['urole'])?$_SESSION['iclientdigimetdata']['urole']:'0';
        // $this->dealer_code = '20602';
        $this->current_menu = 'Scheme-details'; 
        $this->date = '2021-01-01';
        if($auth_id != '0' )
        {
            // dd('1');
            $auth_id = $auth_id;

        }
        else {
            # code...
            // dd('11');
            header('Location: http://demo.msell.in/client');
            dd('1');
        }
        // dd($auth_id);   

        // if()
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_scheme_details(Request $request)
    {

        if($this->role_id == 41){

        $dealer_report_section_data_data = DB::table('dealer_report_section_data')
                                ->where('dealer_id',$this->dealer_id)
                                ->where('location_2','!=','0')
                                ->groupBy('location_2');
        $dealer_report_section_data = $dealer_report_section_data_data->take(1)->pluck('location_2')->toArray();
        $dealer_id_data = DealerLocation::select(DB::raw("CONCAT_WS('-',dealer.dealer_code,dealer.name) as dealer_name"),'dealer.dealer_code')
                            ->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
                            ->join('dealer','dealer_location_rate_list.dealer_id','=','dealer.id')
                            ->where('dealer_code','!=','0');
                            if(!empty($dealer_report_section_data))
                            {
                                $dealer_id_data->whereIn('l2_id',$dealer_report_section_data);
                            }
        $dealer = $dealer_id_data->where('dealer.dealer_status','=','1')->groupBy('dealer.id')->pluck('dealer_name','dealer_code')->toArray();

        // dd($dealer);

        }else{
        $dealerDetails = DB::table('dealer')
        ->where('dealer_status',1);
        $dealer = $dealerDetails->pluck(DB::raw("CONCAT(dealer_code,' - ',name) as name"),'dealer_code');
        }


        return view('DMS/Scheme.dealer_wise_view_index',[
                'dealer_filter'=>$dealer,
        ]);
    }
    
    public function index(Request $request)
    {
        $records = array();
        $order_details = array();
        // $curr_date = date('Y-m-d');
       
            $scheme_catg_title_data = DB::table('scheme_domain_man')
                    ->join('scheme_master_man','scheme_master_man.scheme_no','=','scheme_domain_man.scheme_no')
                    ->join('circular_master_man','circular_master_man.circular_no','=','scheme_domain_man.circular_no')
                    ->where('scheme_domain_man.status',1)
                    // ->where('dealer_code',$this->dealer_code)
                    // ->where('dealer_code',0)
                    // ->whereIn('scheme_domain_man.scheme_no',$scheme_details_no)
                    // ->whereRaw("date_format(scheme_from_date,'%Y-%m-%d')<= '$curr_date' AND date_format(scheme_to_date,'%Y-%m-%d')>= '$curr_date'")
                    ->groupBy('scheme_domain_man.scheme_no')
                    ->get();
        
        return view('DMS/Scheme.index',[
                    'order_details'=> $order_details,
                    'role_id'=> $this->role_id,
                    'current_menu'=>$this->current_menu,
                    'scheme_catg_title_data'=> $scheme_catg_title_data,
                    'records'=> $records

                ]);
    }
    public function create()
    {
        $records = array();
        $order_details = array();
        $mktg_drop_down_data = DB::table('MKTG_CATG_MAST')->get();
        $prod_drop_down_data = DB::table('PROD_CATG_MAST')->orderBy('PROD_CATG_NAME','ASC')->get();
        return view('DMS/Scheme.create',[
                    'order_details'=> $order_details,
                    'role_id'=> $this->role_id,
                    'current_menu'=>$this->current_menu,
                    'mktg_drop_down_data'=> $mktg_drop_down_data,
                    'prod_drop_down_data'=> $prod_drop_down_data,
                    'records'=> $records

                ]);
    }
    public function store(Request $request)
    {

        // dd($request);
        DB::beginTransaction();
        // begin::transaction();
        $circular_no = $request->circular_no;
        $circular_date = $request->circular_date;
        $created_at = date('Y-m-d H:i:s');
        $status = '1';
        // insertion circula
        $check = DB::table('circular_master_man')
                ->where('circular_no',$circular_no)
                ->first();
        if(empty($check))
        {
            $insert_circular_data = DB::table('circular_master_man')
                                ->insert([
                                    'circular_no' => $circular_no, 
                                    'circular_date' => $circular_date, 
                                    'created_at' => $created_at, 
                                    'status' => $status, 
                                ]);
        }
        else
        {
            $circular_no = $circular_no;
        }

        

        $scheme_no = $request->scheme_no;
        $scheme_from_date = $request->scheme_from_date;
        $scheme_to_date = $request->scheme_to_date;
        $scheme_type = $request->scheme_type;
        $scheme_benific = $request->scheme_benefically;
        $scheme_des = $request->scheme_description;
        $sale_type = $request->sale_type;

        // scheme insertion
        $insert_scheme_mast_data = DB::table('scheme_master_man')
                                ->insert([
                                    'scheme_no' => $scheme_no,
                                    'scheme_from_date' => $scheme_from_date,
                                    'scheme_to_date' => $scheme_to_date,
                                    'scheme_type' => $scheme_type,
                                    'scheme_benific' => $scheme_benific,
                                    'scheme_des' => $scheme_des,
                                    'created_at' => $created_at, 
                                    'status' => $status, 
                                ]);


        // $asav_all = $request->asav_all;
        $mktg_all = $request->mktg_all;
        $mktg_selected = $request->mktg_selected;

        $mainline_all = $request->mainline_all;
        $mainline_selected = $request->mainline_selected;

        $prod_group_all = $request->prod_group_all;
        $prod_group_selected = $request->prod_group_selected;

        $prod_selected = $request->prod_selected;
        $prod_catg_all = $request->prod_catg_all;

        $dealer_all = $request->dealer_all;

        if(!empty($mktg_all))
        {
            // dd('1');
            $item_code_return = DB::table('MKTG_CATG_MAST')
                                ->get();
            $insert_domain = [];
            foreach ($item_code_return as $key => $value) {
                # code...
                // DD($value);s
                $insert_domain[] = [
                        'item_code'=>0,
                        'mktg_catg'=>$value->MKTG_CATG,
                        'circular_no'=>$circular_no,
                        'scheme_no'=>$scheme_no,
                        'created_at'=>$created_at,
                        'sale_type'=> $sale_type,
                        'status'=>$status,
                ];
            }
            $insert_scheme_domain_1 = DB::table('scheme_domain_man')
                                ->insert($insert_domain);
        }
        if(!empty($prod_catg_all))
        {
            $item_code_return = DB::table('PROD_CATG_MAST')
                                ->get();
            $insert_domain = [];
            foreach ($item_code_return as $key => $value) {
                # code...
                // DD($value);s
                $insert_domain[] = [
                        'item_code'=>0,
                        'mktg_catg'=>0,
                        'prod_catg'=>$value->PROD_CATG,
                        'circular_no'=>$circular_no,
                        'scheme_no'=>$scheme_no,
                        'created_at'=>$created_at,
                        'sale_type'=> $sale_type,
                        'status'=>$status,
                ];
            }
            $insert_scheme_domain_1 = DB::table('scheme_domain_man')
                                ->insert($insert_domain);
        }
        if(!empty($mktg_selected))
        {
            $mktg_catg_dropdown = $request->mktg_catg_dropdown;
            // $item_code_return = DB::table('MKTG_CATG_MAST')
            //                     ->get();
            // dd($mktg_catg_dropdown);
            $insert_domain = [];
            foreach ($mktg_catg_dropdown as $key => $value) {
                # code...
                // DD($value);s
                $insert_domain[] = [
                        'item_code'=>0,
                        'mktg_catg'=>$value,
                        'circular_no'=>$circular_no,
                        'scheme_no'=>$scheme_no,
                        'created_at'=>$created_at,
                        'sale_type'=> $sale_type,
                        'status'=>$status,
                ];
            }
            $insert_scheme_domain_1 = DB::table('scheme_domain_man')
                                ->insert($insert_domain);
        }
        if(!empty($prod_selected))
        {
            $prod_catg_dropdown = $request->prod_catg_dropdown;
            // $item_code_return = DB::table('MKTG_CATG_MAST')
            //                     ->get();
            // dd($mktg_catg_dropdown);
            $insert_domain = [];
            foreach ($prod_catg_dropdown as $key => $value) {
                # code...
                // DD($value);s
                $insert_domain[] = [
                        'item_code'=>0,
                        'mktg_catg'=>0,
                        'prod_catg'=>$value,
                        'circular_no'=>$circular_no,
                        'scheme_no'=>$scheme_no,
                        'created_at'=>$created_at,
                        'sale_type'=> $sale_type,
                        'status'=>$status,
                ];
            }
            $insert_scheme_domain_1 = DB::table('scheme_domain_man')
                                ->insert($insert_domain);
        }
        

        if(!empty($mainline_all))
        {   
            // $array_mainline = array('CLA','GLD','GEN');
            // $item_code_return = DB::table('WWW_WEB_ITEM_DISPLAY')
            //                     ->whereIn('MKTG_CATG',$array_mainline)
            //                     ->get();
            $insert_domain = [];
            // foreach ($item_code_return as $key => $value) {
                # code...
                $insert_domain = [
                        'item_code'=>0,
                        'mktg_catg'=>'mainline',
                        'circular_no'=>$circular_no,
                        'scheme_no'=>$scheme_no,
                        'created_at'=>$created_at,
                        'status'=>$status,
                        'sale_type'=> $sale_type,
                ];
            // }
            $insert_scheme_domain_1 = DB::table('scheme_domain_man')
                                ->insert($insert_domain);
        }
    

        if(!empty($dealer_all))
        {
            $out = [];
            $insert_domain = [];
            $dealer_selection = DB::table('ACC_MAST')
                            ->join('dealer','dealer.dealer_code','=','ACC_MAST.ACC_CODE')
                            ->where('ACC_STATUS','!=','C')
                            ->where('dealer_status','1')
                            ->groupBy('dealer_code')
                            ->get();
            foreach ($dealer_selection as $d_key => $d_value) {
                # code...
                $insert_domain[] = [
                        'dealer_code'=>$d_value->ACC_CODE,
                        'circular_no'=>$circular_no,
                        'scheme_no'=>$scheme_no,
                        'created_at'=>$created_at,
                        'status'=>$status,
                ];
            }
            $insert_scheme_domain_2 = DB::table('scheme_domain_man')
                                    ->insert($insert_domain);
        }

        $target_sale_from_date = $request->target_sale_from_date; 
        $target_sale_to_date = $request->target_sale_to_date; 
        $incentive_sale_from_date = $request->incentive_sale_from_date; 
        $incentive_sale_to_date = $request->incentive_sale_to_date; 
        $base_from = $request->base_from; 
        $base_to = $request->base_to; 
        $free_final_rate = $request->free_final_rate; 
        if(!empty($target_sale_from_date) && !empty($target_sale_to_date) && !empty($incentive_sale_from_date) && !empty($incentive_sale_to_date) && !empty($base_from) && !empty($base_to) && !empty($free_final_rate))
        {
            foreach ($target_sale_from_date as $key => $value) {
                # code...
                if(!empty($target_sale_from_date[$key]) && !empty($target_sale_to_date[$key]) && !empty($incentive_sale_from_date[$key]) && !empty($incentive_sale_to_date[$key]) && !empty($base_from[$key])  && !empty($free_final_rate[$key]))
                {
                    $slab_insertion[] = [
                            'target_sale_from_date' => $value, 
                            'target_sale_to_date' => $target_sale_to_date[$key], 
                            'incentive_sale_from_date' => $incentive_sale_from_date[$key], 
                            'incentive_sale_to_date' => $incentive_sale_to_date[$key], 
                            'base_from' => $base_from[$key], 
                            'base_to' => !empty($base_to[$key])?$base_to[$key]:'Above', 
                            'free_final_rate' => $free_final_rate[$key], 
                            'scheme_no'=>$scheme_no,
                            'circular_no'=>$circular_no,
                            'created_at'=>$created_at,
                            'status'=>$status,
                    ]; 
                }
            }

            $inserltion_slab = DB::table('scheme_slab_man')
                            ->insert($slab_insertion);
        }

        if($insert_scheme_mast_data&&$insert_scheme_domain_1&&$insert_scheme_domain_2&&$inserltion_slab)
        {
            DB::commit();
        return redirect()->guest(url($this->current_menu));
            // return redirect()->intended($this->current_menu);

        }
        else
        {
            DB::rollback();
        return redirect()->guest(url($this->current_menu));
            // return redirect()->intended($this->current_menu);
        }

    }
    public function dealer_scheme_old(Request $request)
    {
        $mktg_target_out = array();
        $records = array();
        $order_details = array();
        $out = array();
        $mktg_catg_wise_sales = array();
        $prod_catg_wise_sales = array();
        $prod_out = array();
        $prod_target_out = array();
        // $prod_scheme_catg_title_data = array();
        $curr_date = date('Y-m-d');
        $scheme_catg_title_data = array();
        $scheme_slab_part = array();
        $prod_scheme_catg_title_data = array();


        $scheme_details_no = DB::table('scheme_domain_man')
                    ->join('scheme_master_man','scheme_master_man.scheme_no','=','scheme_domain_man.scheme_no')
                    ->join('circular_master_man','circular_master_man.circular_no','=','scheme_domain_man.circular_no')
                    ->where('scheme_domain_man.status',1)
                    ->where('dealer_code',$this->dealer_code)
                    ->whereRaw("date_format(scheme_from_date,'%Y-%m-%d')<= '$curr_date' AND date_format(scheme_to_date,'%Y-%m-%d')>= '$curr_date'")
                    ->pluck('scheme_domain_man.scheme_no')->toArray();
        // dd($scheme_details);
        if(!empty($scheme_details_no))
        {
            $scheme_catg_title_data = DB::table('scheme_domain_man')
                    ->join('scheme_master_man','scheme_master_man.scheme_no','=','scheme_domain_man.scheme_no')
                    ->join('circular_master_man','circular_master_man.circular_no','=','scheme_domain_man.circular_no')
                    ->where('scheme_domain_man.status',1)
                    ->where('dealer_code',0)
                    ->whereIn('scheme_domain_man.scheme_no',$scheme_details_no)
                    ->whereRaw("date_format(scheme_from_date,'%Y-%m-%d')<= '$curr_date' AND date_format(scheme_to_date,'%Y-%m-%d')>= '$curr_date'")
                    ->groupBy('mktg_catg')
                    ->get();
        

            foreach ($scheme_catg_title_data as $key => $value) {
                # code...
                $out[$value->mktg_catg] = DB::table('scheme_slab_man')
                                    ->join('scheme_domain_man','scheme_slab_man.scheme_no','=','scheme_domain_man.scheme_no')
                                    ->join('scheme_master_man','scheme_master_man.scheme_no','=','scheme_domain_man.scheme_no')
                                    ->join('circular_master_man','circular_master_man.circular_no','=','scheme_domain_man.circular_no')
                                    ->where('scheme_domain_man.status',1)
                                    ->where('dealer_code',0)
                                    ->whereIn('scheme_master_man.scheme_no',$scheme_details_no)
                                    ->where('mktg_catg',$value->mktg_catg)
                                    ->whereRaw("date_format(scheme_from_date,'%Y-%m-%d')<= '$curr_date' AND date_format(scheme_to_date,'%Y-%m-%d')>= '$curr_date'")
                                    ->groupBy('scheme_slab_man.id')
                                    ->get();
                
            }
            // dd($out);   
            
        }
        // dd($out);
        foreach ($scheme_catg_title_data as $scheme_key => $scheme_value) {

            foreach ($out[$scheme_value->mktg_catg] as $sale_key => $sale_value) {
                # code...
                // $sale_out['']
                // dd($sale_value);
                if($scheme_value->mktg_catg == 'ASV')
                {
                    $mktg_data_obj = DB::table('ITEMTRAN_BODY')
                        ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
                        ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')
                        ->select(DB::raw('SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED'))
                        ->where('MKTG_CATG_MAST.MKTG_CATG', $scheme_value->mktg_catg)
                        // ->where('MKTG_CATG_MAST.MKTG_CATG', 'ASV')
                        ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$sale_value->target_sale_from_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$sale_value->target_sale_to_date'") 
                        ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
                        ->where('ITEMTRAN_HEAD.ACC_CODE', $this->dealer_code)
                        ->first();
                    $mktg_catg_wise_sales[$scheme_value->mktg_catg] = !empty($mktg_data_obj->QTYISSUED)?$mktg_data_obj->QTYISSUED:'0';
                }
                elseif($scheme_value->mktg_catg == 'ETHICAL')
                {
                    $mktg_data_obj = DB::table('ITEMTRAN_BODY')
                                                    ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
                                                    ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                                                    ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                                                    ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                                                    ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_MAST.MKTG_CATG')
                                                    ->select(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'))
                                                    ->where('MKTG_CATG_MAST.MKTG_CATG', 'JPS')
                                                    ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$sale_value->target_sale_from_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$sale_value->target_sale_to_date'") 
                                                    ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
                                                    ->where('ITEMTRAN_HEAD.ACC_CODE', $this->dealer_code)
                                                    ->first();
                    $mktg_catg_wise_sales[$scheme_value->mktg_catg] = !empty($mktg_data_obj->VALISSUED)?$mktg_data_obj->VALISSUED:'0';
                }
                elseif($scheme_value->mktg_catg == 'FMCG')
                {
                    $mktg_data_obj = DB::table('ITEMTRAN_BODY')
                                                    ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
                                                    ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                                                    ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                                                    ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                                                    ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_MAST.MKTG_CATG')
                                                    ->select(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'))
                                                    ->where('MKTG_CATG_MAST.MKTG_CATG', 'FMC')
                                                    ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$sale_value->target_sale_from_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$sale_value->target_sale_to_date'") 
                                                    ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
                                                    ->where('ITEMTRAN_HEAD.ACC_CODE', $this->dealer_code)
                                                    ->first();
                    $mktg_catg_wise_sales[$scheme_value->mktg_catg] = !empty($mktg_data_obj->VALISSUED)?$mktg_data_obj->VALISSUED:'0';
                }
                elseif( $scheme_value->mktg_catg == 'mainline')
                {
                    $mainline_array_mktg = array('GEN','GLD','CLA');
                    $mainline_array_obj = DB::table('ITEMTRAN_BODY')
                                        ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
                                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                                        ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                                        ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_MAST.MKTG_CATG')
                                        ->select(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'))
                                        ->whereIn('MKTG_CATG_MAST.MKTG_CATG', $mainline_array_mktg)
                                        ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$sale_value->target_sale_from_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$sale_value->target_sale_to_date'") 
                                        ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
                                        ->where('ITEMTRAN_HEAD.ACC_CODE', $this->dealer_code)
                                        ->first();
                    // $mainline_array[] = 
                    $mktg_catg_wise_sales[$scheme_value->mktg_catg] =  !empty($mainline_array_obj)?$mainline_array_obj->VALISSUED:'0';
                }
                else
                {
                    $mktg_data_obj = DB::table('ITEMTRAN_BODY')
                                    ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
                                    ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                                    ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                                    ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                                    ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_MAST.MKTG_CATG')
                                    ->select(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'))
                                    ->where('MKTG_CATG_MAST.MKTG_CATG', $scheme_value->mktg_catg)
                                    ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$sale_value->target_sale_from_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$sale_value->target_sale_to_date'") 
                                    ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
                                    ->where('ITEMTRAN_HEAD.ACC_CODE', $this->dealer_code)
                                    ->first();
                    $mktg_catg_wise_sales[$scheme_value->mktg_catg] = !empty($mktg_data_obj->VALISSUED)?$mktg_data_obj->VALISSUED:'0';
                }
                
            }
        }
        // dd($mktg_catg_wise_sales);
        // $from_target_date = "01-APR-" .(date('y')-1);
        // $to_target_date = "31-MAR-" .(date('y'));
        // $from_sale_date = (date('Y')-1)."-04-01";
        // $to_sale_date = date('Y')."-03-31";
        $from_target_date = "01-APR-" .(date('y'));
        $to_target_date = "31-MAR-" .(date('y')+1);
        $from_sale_date = (date('Y'))."-04-01";
        $to_sale_date = (date('Y')+1)."-03-31";


        $mktg_catg_array = array("ASV", "CLA", "GEN", "GLD", "OTC", "OT2", "JPS", "FMC");
        for ($i=0; $i <= 7; $i++) { 
            # code...
            $mktg_catg_wise_target = DB::table('DEALER_TARGET_MAST')
                ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','DEALER_TARGET_MAST.MKTG_CATG')  
                ->where('DEALER_TARGET_MAST.MKTG_CATG',$mktg_catg_array[$i])
                ->where('DEALER_TARGET_MAST.PROD_CATG','')
                ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
                ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
                ->where('DEALER_TARGET_MAST.ACC_CODE', $this->dealer_code);
                $mktg_catg_wise_targets[$mktg_catg_array[$i]] = !empty($mktg_catg_wise_target->first()->TARGET_AMT)?$mktg_catg_wise_target->first()->TARGET_AMT:0;
        }
        // dd($mktg_catg_wise_targets);
        $asv_wise_target_data = DB::table('DEALER_TARGET_MAST')
                ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','DEALER_TARGET_MAST.MKTG_CATG')  
                ->where('MKTG_CATG_MAST.MKTG_CATG','ASV')
                ->where('DEALER_TARGET_MAST.PROD_CATG','ASV')
                ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
                ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
                ->where('DEALER_TARGET_MAST.ACC_CODE', $this->dealer_code);
                // ->where('PROD_CATG','!=','')
        $mktg_catg_wise_targets['ASV'] = !empty($asv_wise_target_data->first()->TARGET_QTY)?$asv_wise_target_data->first()->TARGET_QTY:0;
        // dd($mktg_catg_wise_targets);

        foreach ($mktg_catg_wise_targets as $key => $value) {
            # code...
            // $out[]
            // dd($value);
            if($key == 'CLA' || $key == 'GEN' || $key == 'GLD')
            {
                $set_out[] = $value;
                $mktg_target_out['mainline'] = array_sum($set_out);

            }
            elseif($key == 'JPS')
            {

                $mktg_target_out['ETHICAL'] = $value;
            }
            elseif($key == 'FMC')
            {

                $mktg_target_out['FMCG'] = $value;
            }
            else
            {
                $mktg_target_out[$key] = $value;
            }

        }
        // dd($mktg_target_out);

        return view('DMS/Scheme.dealerScheme',[
                    'order_details'=> $order_details,
                    'role_id'=> $this->role_id,
                    'current_menu'=>$this->current_menu,
                    'records'=> $records,
                    'out'=>$out,
                    'mktg_target_out'=> $mktg_target_out,
                    'mktg_catg_wise_sales'=> $mktg_catg_wise_sales,
                    'scheme_catg_title_data'=> $scheme_catg_title_data,

                ]);
    }
    public function dealer_scheme(Request $request)
    {
        $mktg_target_out = array();
        $records = array();
        $order_details = array();
        $out = array();
        $mktg_catg_wise_sales = array();
        // $mktg_catg_wise_sales = array();
        $curr_date = date('Y-m-d');
        $scheme_catg_title_data = array();
        $scheme_slab_part = array();
        $admin_status = $request->admin_status;

        // $from_target_date = "01-APR-" .(date('y')-1);
        // $to_target_date = "31-MAR-" .(date('y'));
        // $from_sale_date = (date('Y')-1)."-04-01";
        // $to_sale_date = date('Y')."-03-31";

        

        $from_target_date = "01-APR-" .(date('y'));
        $to_target_date = "31-MAR-" .(date('y')+1);
        $from_sale_date = (date('Y'))."-04-01";
        $to_sale_date = (date('Y')+1)."-03-31";
        $prod_target_out = array();

        $prod_scheme_catg_title_data = array();
        $mktg_catg_wise_targets = array();
        $prod_catg_wise_sales = array();
        $prod_out = array();
        if($admin_status == 1){
            $dealer_code = $request->dealer_code;
            if(empty($dealer_code))
            {
                return redirect()->guest(url($this->current_menu));
                // return redirect()->intended($this->current_menu);
            }
        }
        else{
            $dealer_code = $this->dealer_code;
        }

        $div_code_entry = DB::table('ACC_MAST')->where('ACC_CODE',$dealer_code)->first();

        $deiv_code_login = DB::table('dealer_person_login')
                            ->select('div_code_main')
                            ->join('dealer','dealer.id','=','dealer_person_login.dealer_id')
                            ->where('dealer.dealer_code',$dealer_code)->first(); 

        $div_code_else = !empty($deiv_code_login->div_code_main)?$deiv_code_login->div_code_main:'0';
        $div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:$div_code_else;

        $scheme_details_no = DB::table('scheme_domain_man')
                    ->join('scheme_master_man','scheme_master_man.scheme_no','=','scheme_domain_man.scheme_no')
                    ->join('circular_master_man','circular_master_man.circular_no','=','scheme_domain_man.circular_no')
                    ->where('scheme_domain_man.status',1)
                    ->where('dealer_code',$dealer_code)
                    ->whereRaw("date_format(scheme_from_date,'%Y-%m-%d')<= '$curr_date' AND date_format(scheme_to_date,'%Y-%m-%d')>= '$curr_date'")
                    ->pluck('scheme_domain_man.scheme_no')->toArray();
        // dd($scheme_details_no);
        if(!empty($scheme_details_no))
        {
            $scheme_catg_title_data = DB::table('scheme_domain_man')
                    ->join('scheme_master_man','scheme_master_man.scheme_no','=','scheme_domain_man.scheme_no')
                    ->join('circular_master_man','circular_master_man.circular_no','=','scheme_domain_man.circular_no')
                    ->where('scheme_domain_man.status',1)
                    ->where('dealer_code',0)
                    ->where('prod_catg','0')
                    ->whereIn('scheme_domain_man.scheme_no',$scheme_details_no)
                    ->whereRaw("date_format(scheme_from_date,'%Y-%m-%d')<= '$curr_date' AND date_format(scheme_to_date,'%Y-%m-%d')>= '$curr_date'")
                    ->groupBy('mktg_catg')
                    ->get();
            // dd($scheme_catg_title_data);

            foreach ($scheme_catg_title_data as $key => $value) {
                # code...
                $out[$value->mktg_catg] = DB::table('scheme_slab_man')
                                    ->join('scheme_domain_man','scheme_slab_man.scheme_no','=','scheme_domain_man.scheme_no')
                                    ->join('scheme_master_man','scheme_master_man.scheme_no','=','scheme_domain_man.scheme_no')
                                    ->join('circular_master_man','circular_master_man.circular_no','=','scheme_domain_man.circular_no')
                                    ->select('scheme_slab_man.*','scheme_domain_man.mktg_catg as mktg_catg')
                                    ->where('scheme_domain_man.status',1)
                                    ->where('dealer_code',0)
                                    ->where('prod_catg',0)
                                    ->whereIn('scheme_master_man.scheme_no',$scheme_details_no)
                                    ->where('mktg_catg',$value->mktg_catg)
                                    ->whereRaw("date_format(scheme_from_date,'%Y-%m-%d')<= '$curr_date' AND date_format(scheme_to_date,'%Y-%m-%d')>= '$curr_date'")
                                    ->groupBy('scheme_slab_man.id')
                                    ->orderBy('scheme_slab_man.id','DESC')
                                    ->get();
                
            }

            $prod_scheme_catg_title_data = DB::table('scheme_domain_man')
                    ->join('scheme_master_man','scheme_master_man.scheme_no','=','scheme_domain_man.scheme_no')
                    ->join('circular_master_man','circular_master_man.circular_no','=','scheme_domain_man.circular_no')
                    ->where('scheme_domain_man.status',1)
                    ->where('dealer_code',0)
                    ->where('mktg_catg','0')
                    ->whereIn('scheme_domain_man.scheme_no',$scheme_details_no)
                    ->whereRaw("date_format(scheme_from_date,'%Y-%m-%d')<= '$curr_date' AND date_format(scheme_to_date,'%Y-%m-%d')>= '$curr_date'")
                    ->groupBy('prod_catg')
                    ->get();
        
            // dd($prod_scheme_catg_title_data);
            foreach ($prod_scheme_catg_title_data as $key => $value) {
                # code...
                $prod_out[$value->prod_catg] = DB::table('scheme_slab_man')
                                    ->join('scheme_domain_man','scheme_slab_man.scheme_no','=','scheme_domain_man.scheme_no')
                                    ->join('scheme_master_man','scheme_master_man.scheme_no','=','scheme_domain_man.scheme_no')
                                    ->join('circular_master_man','circular_master_man.circular_no','=','scheme_domain_man.circular_no')
                                    ->select('scheme_slab_man.*','scheme_domain_man.prod_catg as prod_catg')
                                    ->where('scheme_domain_man.status',1)
                                    ->where('dealer_code',0)
                                    ->where('mktg_catg',0)
                                    ->whereIn('scheme_master_man.scheme_no',$scheme_details_no)
                                    ->where('prod_catg',$value->prod_catg)
                                    ->whereRaw("date_format(scheme_from_date,'%Y-%m-%d')<= '$curr_date' AND date_format(scheme_to_date,'%Y-%m-%d')>= '$curr_date'")
                                    ->groupBy('scheme_slab_man.id')
                                    ->orderBy('id','DESC')
                                    ->get();
                
            }
            // dd($out);   
            
        }

        // dd($prod_out);
        // $mktg_catg_return = DB::table('MKTG_CATG_MAST')
        //                         ->get();
        $mktg_catg_return_array = DB::table('MKTG_CATG_MAST')
                                ->pluck('MKTG_CATG_NAME','MKTG_CATG');


        $mktg_group_mast_return = DB::table('mktg_group_mast')
                                ->pluck('mktg_group_code')->toArray();



        $prod_catg_return_array = DB::table('PROD_CATG_MAST')
                                ->pluck('PROD_CATG_NAME','PROD_CATG');

        
        $prod_group_mast_return = DB::table('prod_group_mast')
                                ->pluck('prod_group_code')->toArray();

        // dd()
        foreach ($scheme_catg_title_data as $scheme_key => $scheme_value) {
            // dd($out[$scheme_value->mktg_catg]);
            foreach ($out[$scheme_value->mktg_catg] as $sale_key => $sale_value) {
                # code...
                // $sale_out['']
                // dd($sale_value);
                if($scheme_value->sale_type == 'BOX' || $scheme_value->sale_type == 'PCS')
                {
                    $mktg_data_obj = DB::table('ITEMTRAN_BODY')
                        ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
                        ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')
                        ->select(DB::raw('SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED'))
                        ->where('MKTG_CATG_MAST.MKTG_CATG', $scheme_value->mktg_catg)
                        // ->where('MKTG_CATG_MAST.MKTG_CATG', 'ASV')
                        ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)
                        ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
                        ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$sale_value->target_sale_from_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$sale_value->target_sale_to_date'") 
                        ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
                        ->where('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
                        ->first();
                    $mktg_catg_wise_sales[$scheme_value->mktg_catg.$sale_value->id] = !empty($mktg_data_obj->QTYISSUED)?$mktg_data_obj->QTYISSUED:'0';

                    $asv_wise_target_data = DB::table('DEALER_TARGET_MAST')
                                        ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','DEALER_TARGET_MAST.MKTG_CATG')  
                                        ->where('MKTG_CATG_MAST.MKTG_CATG',$scheme_value->mktg_catg)
                                        ->where('DEALER_TARGET_MAST.PROD_CATG','!=','')
                                        ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
                                        ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
                                        ->where('DEALER_TARGET_MAST.ACC_CODE', $dealer_code);
                    $mktg_catg_wise_targets[$scheme_value->mktg_catg] = !empty($asv_wise_target_data->first()->TARGET_QTY)?$asv_wise_target_data->first()->TARGET_QTY:0;
                }
                elseif($scheme_value->mktg_catg == 'mainline' )
                {

                    $mktg_data_obj = DB::table('ITEMTRAN_BODY')
                        ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
                        ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')
                        //->select(DB::raw('SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED'))
                        ->select(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'))
                        ->whereIn('MKTG_CATG_MAST.MKTG_CATG', $mktg_group_mast_return)
                        // ->where('MKTG_CATG_MAST.MKTG_CATG', 'ASV')
                        ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)
                        ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
                        ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$sale_value->target_sale_from_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$sale_value->target_sale_to_date'") 
                        ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
                        ->where('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
                        ->first();
                    // $mktg_catg_wise_sales[$scheme_value->mktg_catg.$sale_value->id] = !empty($mktg_data_obj->QTYISSUED)?$mktg_data_obj->QTYISSUED:'0';
                    $mktg_catg_wise_sales[$scheme_value->mktg_catg.$sale_value->id] = !empty($mktg_data_obj->VALISSUED)?$mktg_data_obj->VALISSUED:'0';

                    $mktg_catg_wise_target = DB::table('DEALER_TARGET_MAST')
                                        ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','DEALER_TARGET_MAST.MKTG_CATG')  
                                        ->whereIn('DEALER_TARGET_MAST.MKTG_CATG',$mktg_group_mast_return)
                                        ->where('DEALER_TARGET_MAST.PROD_CATG','')
                                        ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
                                        ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
                                        ->where('DEALER_TARGET_MAST.ACC_CODE', $dealer_code);
                    $mktg_catg_wise_targets[$scheme_value->mktg_catg] = !empty($mktg_catg_wise_target->first()->TARGET_AMT)?$mktg_catg_wise_target->first()->TARGET_AMT:0;
                }
                else
                {
                    $mktg_data_obj = DB::table('ITEMTRAN_BODY')
                                    ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
                                    ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                                    ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                                    ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                                    ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_MAST.MKTG_CATG')
                                    ->select(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'))
                                    ->where('MKTG_CATG_MAST.MKTG_CATG', $scheme_value->mktg_catg)
                                    ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)
                                    ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
                                    ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$sale_value->target_sale_from_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$sale_value->target_sale_to_date'") 
                                    ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
                                    ->where('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
                                    ->first();
                    $mktg_catg_wise_sales[$scheme_value->mktg_catg.$sale_value->id] = !empty($mktg_data_obj->VALISSUED)?$mktg_data_obj->VALISSUED:'0';

                    $mktg_catg_wise_target = DB::table('DEALER_TARGET_MAST')
                                        ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','DEALER_TARGET_MAST.MKTG_CATG')  
                                        ->where('DEALER_TARGET_MAST.MKTG_CATG',$scheme_value->mktg_catg)
                                        ->where('DEALER_TARGET_MAST.PROD_CATG','')
                                        ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
                                        ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
                                        ->where('DEALER_TARGET_MAST.ACC_CODE', $dealer_code);
                    $mktg_catg_wise_targets[$scheme_value->mktg_catg] = !empty($mktg_catg_wise_target->first()->TARGET_AMT)?$mktg_catg_wise_target->first()->TARGET_AMT:0;
                }
                
            }
        }



        foreach ($prod_scheme_catg_title_data as $prod_scheme_key => $prod_scheme_value) {

            foreach ($prod_out[$prod_scheme_value->prod_catg] as $prod_sale_key => $prod_sale_value) {
                # code...
                // $sale_out['']
                // dd($sale_value);
                if($prod_scheme_value->sale_type == 'BOX' || $prod_scheme_value->sale_type == 'PCS')
                {
                    $mktg_data_obj = DB::table('ITEMTRAN_BODY')
                        ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
                        ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')
                        ->select(DB::raw('SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED'))
                        ->where('PROD_CATG_MAST.PROD_CATG', $prod_scheme_value->prod_catg)
                        // ->where('MKTG_CATG_MAST.MKTG_CATG', 'ASV')
                        ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)
                        ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
                        ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$prod_sale_value->target_sale_from_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$prod_sale_value->target_sale_to_date'") 
                        ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
                        ->where('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
                        ->first();
                    $prod_catg_wise_sales[$prod_scheme_value->prod_catg.$prod_sale_value->id] = !empty($mktg_data_obj->QTYISSUED)?$mktg_data_obj->QTYISSUED:'0';

                    $prod_asv_wise_target_data = DB::table('DEALER_TARGET_MAST')
                                        ->where('DEALER_TARGET_MAST.PROD_CATG',$prod_scheme_value->prod_catg)
                                        ->where('DEALER_TARGET_MAST.MKTG_CATG','!=','')
                                        ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
                                        ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
                                        ->where('DEALER_TARGET_MAST.ACC_CODE', $dealer_code);
                    $prod_target_out[$prod_scheme_value->prod_catg] = !empty($prod_asv_wise_target_data->first()->TARGET_QTY)?$prod_asv_wise_target_data->first()->TARGET_QTY:0;
                }
                elseif($prod_scheme_value->prod_catg == 'prod_group' )
                {

                    $mktg_data_obj = DB::table('ITEMTRAN_BODY')
                        ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
                        ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')
                        ->select(DB::raw('SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED'))
                        ->whereIn('PROD_CATG_MAST.PROD_CATG', $prod_group_mast_return)
                        // ->where('MKTG_CATG_MAST.MKTG_CATG', 'ASV')
                        ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)
                        ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
                        ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$prod_sale_value->target_sale_from_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$prod_sale_value->target_sale_to_date'") 
                        ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
                        ->where('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
                        ->first();
                    $prod_catg_wise_sales[$prod_scheme_value->prod_catg.$prod_sale_value->id] = !empty($mktg_data_obj->QTYISSUED)?$mktg_data_obj->QTYISSUED:'0';
                    $prod_catg_wise_target = DB::table('DEALER_TARGET_MAST')
                                        ->whereIn('DEALER_TARGET_MAST.PROD_CATG',$prod_group_mast_return)
                                        ->where('DEALER_TARGET_MAST.MKTG_CATG','!=','')
                                        ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
                                        ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
                                        ->where('DEALER_TARGET_MAST.ACC_CODE', $dealer_code);
                    $prod_target_out[$prod_scheme_value->prod_catg] = !empty($prod_catg_wise_target->first()->TARGET_AMT)?$prod_catg_wise_target->first()->TARGET_AMT:0;
                }
                else
                {
                    $mktg_data_obj = DB::table('ITEMTRAN_BODY')
                                    ->join('ITEMTRAN_HEAD','ITEMTRAN_HEAD.VRNO','=','ITEMTRAN_BODY.VRNO')
                                    ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                                    ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                                    ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                                    ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','ITEM_MAST.PROD_CATG')
                                    ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_MAST.MKTG_CATG')
                                    ->select(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'))
                                    ->where('PROD_MAST.PROD_CATG', $prod_scheme_value->prod_catg)
                                    ->whereRaw("date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')>='$prod_sale_value->target_sale_from_date' and date_format(ITEMTRAN_BODY.VRDATE_FILTER,'%Y-%m-%d')<='$prod_sale_value->target_sale_to_date'") 
                                    ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)
                                    ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
                                    ->groupBy('MKTG_CATG_MAST.MKTG_CATG')
                                    ->where('ITEMTRAN_HEAD.ACC_CODE', $dealer_code)
                                    ->first();
                    $prod_catg_wise_sales[$prod_scheme_value->prod_catg.$prod_sale_value->id] = !empty($mktg_data_obj->VALISSUED)?$mktg_data_obj->VALISSUED:'0';

                    $prod_catg_wise_target = DB::table('DEALER_TARGET_MAST')
                                        ->where('DEALER_TARGET_MAST.PROD_CATG',$prod_scheme_value->prod_catg)
                                        ->where('DEALER_TARGET_MAST.MKTG_CATG','!=','')
                                        ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
                                        ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
                                        ->where('DEALER_TARGET_MAST.ACC_CODE', $dealer_code);
                    $prod_target_out[$prod_scheme_value->prod_catg] = !empty($prod_catg_wise_target->first()->TARGET_AMT)?$prod_catg_wise_target->first()->TARGET_AMT:0;
                }
                
            }
        }

        // dd($mktg_target_out);
        if($admin_status == 1){
            
            return view('DMS/Scheme.admin_scheme_dealer_wise_scheme_ajax',[
                'order_details'=> $order_details,
                'role_id'=> $this->role_id,
                'current_menu'=>$this->current_menu,
                'records'=> $records,
                'out'=>$out,
                'mktg_catg_return_array'=> $mktg_catg_return_array,
                'mktg_target_out'=> $mktg_catg_wise_targets,
                'mktg_catg_wise_sales'=> $mktg_catg_wise_sales,
                'scheme_catg_title_data'=> $scheme_catg_title_data,

                'prod_catg_return_array'=>$prod_catg_return_array,
                'prod_target_out'=> $prod_target_out,
                'prod_catg_wise_sales'=> $prod_catg_wise_sales,
                'prod_scheme_catg_title_data'=> $prod_scheme_catg_title_data,
                'prod_out'=> $prod_out,

            ]);
        }
        else
        {
            return view('DMS/Scheme.dealerScheme',[
                'order_details'=> $order_details,
                'role_id'=> $this->role_id,
                'current_menu'=>$this->current_menu,
                'records'=> $records,
                'out'=>$out,
                'mktg_catg_return_array'=> $mktg_catg_return_array,
                'mktg_target_out'=> $mktg_catg_wise_targets,
                'mktg_catg_wise_sales'=> $mktg_catg_wise_sales,
                'scheme_catg_title_data'=> $scheme_catg_title_data,

                'prod_catg_return_array'=>$prod_catg_return_array,
                'prod_target_out'=> $prod_target_out,
                'prod_catg_wise_sales'=> $prod_catg_wise_sales,
                'prod_scheme_catg_title_data'=> $prod_scheme_catg_title_data,
                'prod_out'=> $prod_out,

            ]);

        }
                    
    }
}
