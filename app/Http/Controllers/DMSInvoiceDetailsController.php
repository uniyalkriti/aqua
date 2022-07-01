<?php

namespace App\Http\Controllers;

use App\_module;
use DB;
use App\Location1;
use App\Location2;
use App\Location3;
use App\Location4;
use App\Location5;
use App\Location6;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Session;
use Illuminate\Http\Request;
use PDF;

class DMSInvoiceDetailsController extends Controller
{
    public function __construct()
    {
        session_start();
        // dd($_SESSION);
        $this->signup_status = !empty($_SESSION['iclientdigimetsignup_status'])?$_SESSION['iclientdigimetsignup_status']:'0';
        // dd($this->signup_status);
        if($this->signup_status == 0 || $this->signup_status == '0')
        {
            // $data_check_again = DB::table('')
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
        $this->current_menu = 'Order-details'; 
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
    public function invoice_details_ajax(Request $request)
    {
        // dd('1');


        $div_code_entry = DB::table('ACC_MAST')->where('ACC_CODE',$this->dealer_code)->first();

        $deiv_code_login = DB::table('dealer_person_login')->where('dealer_id',$this->dealer_id)->first(); 


        $div_code_1 = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:'0';

        $div_code_else = !empty($deiv_code_login->div_code_main)?$deiv_code_login->div_code_main:'0';

        $div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:$div_code_else;
        // $div_code = !empty($div_code_1)?$div_code_1:$deiv_code_login; 
        $status = 0;
        if(empty($request->date_range_picker))
        {
            if(!empty($request->range))
            {
                $from_date = date('Y-m-d',strtotime('-'.$request->range.' day',strtotime($request->from_date)));
                $to_date = date('Y-m-d');
                $status = '1';
            }
            else
            {
                $from_date = date('Y-m-d');
                $to_date = date('Y-m-d');
            }
            
        }
        else
        {
            $explodeDate = explode(" -", $request->date_range_picker);
            $from_date = date('Y-m-d',strtotime(trim($explodeDate[0])));
            $to_date = date('Y-m-d',strtotime(trim($explodeDate[1])));
        }
        // $from_date  = '2020-01-01';
        //     $to_date    = '2021-09-30';
        // dd($from_date);
        if($from_date <'2021-04-01')
        {
            $from_date = '2021-04-01';
        }
        if($this->role_id == '37')
        {
            $div_code = 'JH';
            $invoice_details = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY',function($join){
                            $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                            $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                        })
                        ->select('ITEMTRAN_HEAD.*')
                        ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$from_date' AND date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$to_date'")
                        ->where('ACC_CODE',$this->dealer_code)
                        // ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
                        // ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)
                        ->groupBy('ITEMTRAN_HEAD.VRNO')
                        ->orderBy('VRDATE_FILTER','DESC')
                        ->orderBy('VRNO','ASC')
                        ->get(); 
        }
        else
        {
            $invoice_details = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY',function($join){
                            $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                            $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                        })
                        ->select('ITEMTRAN_HEAD.*')
                        ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$from_date' AND date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$to_date'")
                        ->where('ACC_CODE',$this->dealer_code)
                        // ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
                        // ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)
                        ->groupBy('ITEMTRAN_HEAD.VRNO')
                        ->orderBy('VRDATE_FILTER','DESC')
                        ->orderBy('VRNO','ASC')
                        ->get(); 
        }
        
// dd($invoice_details);
         return view('DMS/InvoiceDetails.ajax',[
            'current_menu'=>$this->current_menu,
            'status'=> $status,
            'invoice_details' => $invoice_details
        ]);
    }
    public function return_details_invoice_order_id(Request $request)
    {
        // dd('1');
        $dealer_code = $request->dealer_code;
        if(!empty($dealer_code))
        {
            $dealer_code = $dealer_code;
            $div_code = $request->div_code;
        }
        else
        {
            $dealer_code = $this->dealer_code;
            $div_code_entry = DB::table('ACC_MAST')->where('ACC_CODE',$dealer_code)->first();

            $deiv_code_login = DB::table('dealer_person_login')->where('dealer_id',$this->dealer_id)->first(); 
            $div_code_1 = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:'0';

            $div_code_else = !empty($deiv_code_login->div_code_main)?$deiv_code_login->div_code_main:'0';

            $div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:$div_code_else;

            if($this->role_id == '37')
            {
                $div_code = 'JH';
            }
            else
            {
                $div_code = $div_code;

            }
        }
        if(!empty($request->div_code))
        {
            $div_code = $request->div_code;
        }
        $order_id = $request->order_id;
        // $vrdate = $request->vrdate;

        
        
        $invoice_details = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY','ITEMTRAN_BODY.VRNO','=','ITEMTRAN_HEAD.VRNO')
                        ->select('ITEMTRAN_HEAD.*')
                        // ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$from_date' AND date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$to_date'")
                        ->where('ITEMTRAN_HEAD.VRNO',$order_id)
                        ->where('ITEMTRAN_BODY.VRNO',$order_id)

                        ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
                        ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)

                        ->groupBy('ITEMTRAN_HEAD.VRNO')
                        ->orderBy('VRDATE_FILTER','DESC')
                        ->orderBy('VRNO','ASC')
                        ->first();
// dd($invoice_details);
        $order_details_body = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY','ITEMTRAN_BODY.VRNO','=','ITEMTRAN_HEAD.VRNO')
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->select('ITEM_MAST.ITEM_CODE as ITEM_CODE','ITEM_NAME','BATCHNO','MFG_DATE','SIZE_UM','NGPSIZE','HSN_CODE','ITEMTRAN_BODY.MRP','ITEMTRAN_BODY.RATE','ITEMTRAN_BODY.QTYISSUED','QTYFREE','CGST_AMOUNT','SGST_AMOUNT','IGST_AMOUNT','ITEMTRAN_BODY.AFIELD6','ITEMTRAN_BODY.AFIELD5','ITEMTRAN_BODY.AFIELD3','ITEMTRAN_BODY.AFIELD4','ITEMTRAN_BODY.VALISSUED','ITEMTRAN_BODY.AFIELD2','ITEMTRAN_BODY.GST_PERCENTAGE','ITEMTRAN_BODY.TI_RATE','CGST_PERCENTAGE','SGST_PERCENTAGE')
                        // ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$from_date' AND date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$to_date'")
                        ->where('ITEMTRAN_BODY.VRNO',$order_id)
                        ->where('ITEMTRAN_HEAD.VRNO',$order_id)

                        ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
                        ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)


                        ->groupBy('ITEMTRAN_BODY.id')
                        ->orderBy('ITEMTRAN_BODY.VRDATE_FILTER','DESC')
                        ->orderBy('ITEMTRAN_BODY.VRNO','ASC')
                        ->orderBy('ITEMTRAN_BODY.ITEM_CODE','ASC')
                        ->get(); 

        $dealer_details = DB::table('ACC_MAST')
                        ->where('ACC_CODE',$invoice_details->ACC_CODE)
                        ->first();
        // dd($invoice_details);
// dd($invoice_details)
         return view('DMS/InvoiceDetails.pdf',[
            'current_menu'=>$this->current_menu,
            'invoice_details' => $invoice_details,
            'order_details_body' => $order_details_body,
            'dealer_details' => $dealer_details
        ]);
    }
    public function dms_invoice_export_csv(Request $request)
    {

        $div_code_entry = DB::table('ACC_MAST')->where('ACC_CODE',$this->dealer_code)->first();

        $deiv_code_login = DB::table('dealer_person_login')->where('dealer_id',$this->dealer_id)->first(); 
        $div_code_1 = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:'0';

        $div_code_else = !empty($deiv_code_login->div_code_main)?$deiv_code_login->div_code_main:'0';

        $div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:$div_code_else;

        $output ='';
        $order_id = $request->order_id;

        if($this->role_id == '37')
        {
            $div_code = 'JH';
        }
        else
        {
            $div_code = $div_code;

        }
        if(!empty($request->div_code))
        {
            $div_code = $request->div_code;
        }
        $invoice_details = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY','ITEMTRAN_BODY.VRNO','=','ITEMTRAN_HEAD.VRNO')
                        ->select('ITEMTRAN_HEAD.*')
                        // ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$from_date' AND date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$to_date'")
                        ->where('ITEMTRAN_HEAD.VRNO',$order_id)
                        ->where('ITEMTRAN_BODY.VRNO',$order_id)

                        ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
                        ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)


                        ->groupBy('ITEMTRAN_HEAD.VRNO')
                        ->orderBy('VRDATE_FILTER','DESC')
                        ->orderBy('VRNO','ASC')
                        ->first();
// dd($invoice_details);
        $order_details_body = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY','ITEMTRAN_BODY.VRNO','=','ITEMTRAN_HEAD.VRNO')
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->select('ITEM_MAST.ITEM_CODE as ITEM_CODE','ITEM_NAME','BATCHNO','MFG_DATE','SIZE_UM','NGPSIZE','HSN_CODE','ITEMTRAN_BODY.MRP','ITEMTRAN_BODY.RATE','ITEMTRAN_BODY.QTYISSUED','QTYFREE','CGST_AMOUNT','SGST_AMOUNT','IGST_AMOUNT','ITEMTRAN_BODY.AFIELD6','ITEMTRAN_BODY.AFIELD5','ITEMTRAN_BODY.AFIELD3','ITEMTRAN_BODY.AFIELD4','ITEMTRAN_BODY.VALISSUED','ITEMTRAN_BODY.AFIELD2','ITEMTRAN_BODY.GST_PERCENTAGE','ITEMTRAN_BODY.TI_RATE','CGST_PERCENTAGE','SGST_PERCENTAGE','ITEMTRAN_BODY.AFIELD1','ITEMTRAN_BODY.GST_AMT','ITEMTRAN_BODY.DRAMT')
                        // ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$from_date' AND date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$to_date'")
                        ->where('ITEMTRAN_BODY.VRNO',$order_id)
                        ->where('ITEMTRAN_HEAD.VRNO',$order_id)

                        ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
                        ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)


                        ->groupBy('ITEMTRAN_BODY.id')
                        ->orderBy('ITEMTRAN_BODY.VRDATE_FILTER','DESC')
                        ->orderBy('ITEMTRAN_BODY.VRNO','ASC')
                        ->get(); 

        $dealer_details = DB::table('ACC_MAST')
                        ->where('ACC_CODE',$invoice_details->ACC_CODE)
                        ->first();

        // $Dealer_Query = $Dealer_Query_data->get()->toarray();
                        // dd($invoice_details->VRDATE);
            $qwerty = "SHREE BAIDYANATH AYURVED BHAWAN PVT.LTD JHANSI ";
            $aacc_name = preg_replace('/[^A-Za-z0-9\-]/', ' ', $dealer_details->ACC_NAME);            
            $output .=$qwerty." , GSTIN:09AAECS5408D1ZI,Invoice Date,".$invoice_details->VRDATE.",Invoice For,".$aacc_name.",".$dealer_details->STNO.",Total Amount,".$invoice_details->DRAMT;
            $output .="\n";
            $output .="INVOICENO,VRDATE,ACC_CODE,DEPOT_CODE,ITEM_CODE,PROD_NAME,ITEM_SIZE,HSN_CODE,BATCHNO,ORDERNO,ORDERDATE,MFG_DATE,QTYISSUED,FREEQTY,MRP,RATE,Gross Amount,Trade Percentage (%),Trade Discount,Scheme Discount,Special Discount,Cash Discount,ATD,Taxable Amount,GST Percentage (%),GST,CGST Percentage (%),CGST/IGST,SGST Percentage (%),SGST,Net Amount,";
            $output .="\n";
            $i=1;

            foreach ($order_details_body as $key => $value) 
            {

                $ITEM_NAME =  preg_replace('/[^A-Za-z0-9\-.)()]/', ' ', $value->ITEM_NAME);
                $qty_free_cus = !empty($value->QTYFREE)?$value->QTYFREE:'0';
                $qty_issued_cus = !empty($value->QTYISSUED)?$value->QTYISSUED:'0';


                    $output .=$invoice_details->VRNO.',';
                    $output .=$invoice_details->VRDATE.',';
                    $output .=$dealer_details->ACC_CODE.',';
                    $output .=$dealer_details->ACC_CODE.',';
                    $output .=$value->ITEM_CODE.',';
                    $output .=$ITEM_NAME.',';
                    $output .=$value->NGPSIZE.''.$value->SIZE_UM.',';
                    $output .=$value->HSN_CODE.',';
                    $output .=$value->BATCHNO.',';
                    $output .=$invoice_details->ORDER_VRNO.'-'.$invoice_details->DO_VRNO.',';
                    $output .=$value->MFG_DATE.',';
                    $output .=$value->MFG_DATE.',';
                    $output .=$qty_issued_cus-$qty_free_cus.',';
                    $output .=$value->QTYFREE.',';
                    $output .=$value->MRP.',';
                    $output .=$value->RATE.',';
                    $output .=$value->AFIELD1.',';
                    $output .=$value->TI_RATE.',';
                    $output .=$value->AFIELD2.',';
                    $output .=$value->AFIELD3.',';
                    $output .=$value->AFIELD4.',';
                    $output .=$value->AFIELD5.',';
                    $output .=$value->AFIELD6.',';
                    $output .=$value->VALISSUED.',';
                    $output .=$value->GST_PERCENTAGE.',';
                    $output .=$value->GST_AMT.',';
                    $output .=$value->CGST_PERCENTAGE.',';
                    $output .=$value->CGST_AMOUNT.',';
                    $output .=$value->SGST_PERCENTAGE.',';
                    $output .=$value->SGST_AMOUNT.',';
                    $output .=$value->DRAMT.',';


                    
                
                    $output .="\n";
                    $i++;
         
                    $dataCount=1;
            }
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=InvoiceDetails.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            echo $output;
    }

    public function export_invoice_details_pdf(Request $request)
    {


        $div_code_entry = DB::table('ACC_MAST')->where('ACC_CODE',$this->dealer_code)->first();

        $deiv_code_login = DB::table('dealer_person_login')->where('dealer_id',$this->dealer_id)->first(); 

        $div_code_1 = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:'0';

        $div_code_else = !empty($deiv_code_login->div_code_main)?$deiv_code_login->div_code_main:'0';

        $div_code = !empty($div_code_entry->DIV_CODE)?$div_code_entry->DIV_CODE:$div_code_else;

        $order_id = $request->order_id;
        // $vrdate = $request->vrdate;

        
        if($this->role_id == '37')
        {
            $div_code = 'JH';
        }
        else
        {
            $div_code = $div_code;

        }
        if(!empty($request->div_code))
        {
            $div_code = $request->div_code;
        }
        $invoice_details = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY','ITEMTRAN_BODY.VRNO','=','ITEMTRAN_HEAD.VRNO')
                        ->select('ITEMTRAN_HEAD.*')
                        // ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$from_date' AND date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$to_date'")
                        ->where('ITEMTRAN_HEAD.VRNO',$order_id)
                        ->where('ITEMTRAN_BODY.VRNO',$order_id)

                        ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
                        ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)


                        ->groupBy('ITEMTRAN_HEAD.VRNO')
                        ->orderBy('VRDATE_FILTER','DESC')
                        ->orderBy('VRNO','ASC')
                        ->first();
// dd($invoice_details);
        $order_details_body = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY','ITEMTRAN_BODY.VRNO','=','ITEMTRAN_HEAD.VRNO')
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->select('ITEM_MAST.ITEM_CODE as ITEM_CODE','ITEM_NAME','BATCHNO','MFG_DATE','SIZE_UM','NGPSIZE','HSN_CODE','ITEMTRAN_BODY.MRP','ITEMTRAN_BODY.RATE','ITEMTRAN_BODY.QTYISSUED','QTYFREE','CGST_AMOUNT','SGST_AMOUNT','IGST_AMOUNT','ITEMTRAN_BODY.AFIELD6','ITEMTRAN_BODY.AFIELD5','ITEMTRAN_BODY.AFIELD3','ITEMTRAN_BODY.AFIELD4','ITEMTRAN_BODY.VALISSUED','ITEMTRAN_BODY.AFIELD2','ITEMTRAN_BODY.GST_PERCENTAGE','ITEMTRAN_BODY.TI_RATE','CGST_PERCENTAGE','SGST_PERCENTAGE')
                        // ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$from_date' AND date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$to_date'")
                        ->where('ITEMTRAN_BODY.VRNO',$order_id)
                        ->where('ITEMTRAN_HEAD.VRNO',$order_id)

                        ->where('ITEMTRAN_HEAD.DIV_CODE',$div_code)
                        ->where('ITEMTRAN_BODY.DIV_CODE',$div_code)

                        
                        ->groupBy('ITEMTRAN_BODY.id')
                        ->orderBy('ITEMTRAN_BODY.VRDATE_FILTER','DESC')
                        ->orderBy('ITEMTRAN_BODY.VRNO','ASC')
                        ->get(); 

        $dealer_details = DB::table('ACC_MAST')
                        ->where('ACC_CODE',$invoice_details->ACC_CODE)
                        ->first();
        $customPaper = array(0, 0, 1240, 1748);

        $pdf_name = $order_id.'.pdf';

        $pdf = PDF::loadView('DMS/InvoiceDetails.pdf_2',[
                'current_menu'=>$this->current_menu,
                'invoice_details' => $invoice_details,
                'order_details_body' => $order_details_body,
                'dealer_details' => $dealer_details
            ]);
        $pdf->setPaper($customPaper);
        $pdf->save(public_path('pdf/'.$pdf_name));
            return $pdf->download('pdf/'.$pdf_name);




    }



    public function saleStatementAjax(Request $request)
    {
        // dd('1');

        $month = $request->month;
        $terr_id = $request->terr_id;
        $fromDate = date('Y-m-01',strtotime($month));

        // dd($fromDate);

        $calcMonth = date('m');
        // $month = "04";

        if($calcMonth == "01" || $calcMonth == "02" || $calcMonth == "03"){
            $currYear = date('Y');
            $lastYear = $currYear-1;

            $fromCummDate = date($lastYear.'-04'.'-01');

        }else{
            $fromCummDate = date('Y'.'-04'.'-01');
        }





        $toDate = date('Y-m-t',strtotime($month));
        $from_target_date = "01-APR-" .(date('y'));
        $to_target_date = "31-MAR-" .(date('y')+1);
        // dd($toDate);


         $dealerDetailsMast = DB::table('ACC_MAST')
                    ->select('ACC_MAST.ACC_NAME','ACC_MAST.ACC_CODE')
                    ->join('dealer','dealer.dealer_code','=','ACC_MAST.ACC_CODE')
                    ->join('dealer_person_login','dealer_person_login.dealer_id','=','dealer.id')
                    ->where('dealer_person_login.role_id','=','5')
                    ->where('dealer.dealer_status','=','1')
                    ->groupBy('ACC_MAST.ACC_CODE');
                    if(!empty($terr_id)){
                        $dealerDetailsMast->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                                          ->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
                                          ->whereIn('l2_id',$terr_id);
                    }

        $dealerDetails = $dealerDetailsMast->get();

        // $arrayPeriod = array("20-21","TARGET","MONTH","C.PRD");

        $arrayPeriod = array("1"=>"TARGET","2"=>"MONTH","3"=>"C.PRD");


      




        // $finalProducts = DB::table('SALE_MIS_CATG_MAST')
        //                 // ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','SALE_MIS_CATG_MAST.PROD_CATG')
        //                 ->where('SALE_MIS_CATG_MAST.PROD_CATG','!=',NULL)
        //                 ->where('SALE_MIS_CATG_MAST.PROD_CATG','!=','')
        //                 ->where('SALE_MIS_CATG_MAST.PROD_CATG','!=','ASV')
        //                 ->orderBy('SALE_MIS_CATG_MAST.USER_SEQ','ASC')
        //                 ->pluck('SALE_MIS_CATG_MAST.PROD_CATG','SALE_MIS_CATG_MAST.PROD_CATG');


        $mLineArray = array('OTC','OT2');

        $finalProducts = DB::table('PROD_CATG_MAST')
                        // ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','SALE_MIS_CATG_MAST.PROD_CATG')
                        ->whereIn('PROD_CATG_MAST.MKTG_CATG',$mLineArray)
                        ->orderBy('PROD_CATG_MAST.USER_SEQ','ASC')
                        ->pluck('PROD_CATG_MAST.PROD_CATG','PROD_CATG_MAST.PROD_CATG');





        // //////////////////////////// for ASAV Queries starts ////////////////////////////////////////////

        // asav target
        $finalASAVTargetDataCase = DB::table('DEALER_TARGET_MAST')
                    ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','DEALER_TARGET_MAST.MKTG_CATG')  
                    ->where('MKTG_CATG_MAST.MKTG_CATG','ASV')
                    ->where('DEALER_TARGET_MAST.PROD_CATG','ASV')
                    ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
                    ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
                    ->groupBy('DEALER_TARGET_MAST.ACC_CODE')
                    ->pluck(DB::raw('SUM(DEALER_TARGET_MAST.TARGET_QTY) as TARGET_QTY'),DB::raw("CONCAT(DEALER_TARGET_MAST.ACC_CODE,'1') AS ITEM_NAME"));

        $finalASAVTargetDataValue = DB::table('DEALER_TARGET_MAST')
                    ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','DEALER_TARGET_MAST.MKTG_CATG')  
                    ->where('MKTG_CATG_MAST.MKTG_CATG','ASV')
                    ->where('DEALER_TARGET_MAST.PROD_CATG','ASV')
                    ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
                    ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
                    ->groupBy('DEALER_TARGET_MAST.ACC_CODE')
                    ->pluck(DB::raw('SUM(DEALER_TARGET_MAST.TARGET_AMT) as TARGET_AMT'),DB::raw("CONCAT(DEALER_TARGET_MAST.ACC_CODE,'1') AS ITEM_NAME"));


        //asav month sale 
        $finalASAVMonthSaleCases = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY',function($join){
                            $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                            $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                        })
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','PROD_MAST.PROD_CATG')
                        ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')

                        ->where('MKTG_CATG_MAST.MKTG_CATG','=','ASV')
                        ->select(DB::raw("SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED"),DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'),'ITEMTRAN_HEAD.ACC_CODE')
                        ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m')='$month'")
                        ->groupBy('ITEMTRAN_HEAD.ACC_CODE')
                        ->orderBy('ITEMTRAN_HEAD.VRDATE_FILTER','DESC')
                        ->get();
        $finalASAVMonthSaleOut = array();
        foreach ($finalASAVMonthSaleCases as $ASAVkey => $ASAVvalue) {

            $ASAVACC_CODE = $ASAVvalue->ACC_CODE;

            $MonthSaleKey = $ASAVACC_CODE.'2';

            $finalASAVMonthSaleOut[$MonthSaleKey]['QTYISSUED'] = ROUND($ASAVvalue->QTYISSUED,2);
            $finalASAVMonthSaleOut[$MonthSaleKey]['VALISSUED'] = ROUND($ASAVvalue->VALISSUED,2);
            
        }


        // asav comm sale

         $finalASAVCummMonthSaleCases = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY',function($join){
                            $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                            $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                        })
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','PROD_MAST.PROD_CATG')
                        ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')

                        ->where('MKTG_CATG_MAST.MKTG_CATG','=','ASV')
                        ->select(DB::raw("SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED"),DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'),'ITEMTRAN_HEAD.ACC_CODE')
                        ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$fromCummDate' AND date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$toDate'")
                        ->groupBy('ITEMTRAN_HEAD.ACC_CODE')
                        ->orderBy('ITEMTRAN_HEAD.VRDATE_FILTER','DESC')
                        ->get();



        $finalASAVCummMonthSaleOut = array();
        foreach ($finalASAVCummMonthSaleCases as $fscASAVkey => $fscASAVvalue) {

            $ASAV_ACC_CODE = $fscASAVvalue->ACC_CODE.'3';


            $finalASAVCummMonthSaleOut[$ASAV_ACC_CODE]['QTYISSUED'] = ROUND($fscASAVvalue->QTYISSUED,2);
            $finalASAVCummMonthSaleOut[$ASAV_ACC_CODE]['VALISSUED'] = ROUND($fscASAVvalue->VALISSUED,2);
            
        }

        // dd($finalASAVCummMonthSaleOut);

        // //////////////////////////// for ASAV Queries ends ////////////////////////////////////////////



         // //////////////////////////// for MAINLINE Queries starts ////////////////////////////////////////////

        // mainline target


      

        
        $mainline_array = array('GEN','CLA','GLD');

        $finalMainlineTargetDataValue = DB::table('DEALER_TARGET_MAST')
                    ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','DEALER_TARGET_MAST.MKTG_CATG')  
                    ->whereIn('MKTG_CATG_MAST.MKTG_CATG',$mainline_array)
                    ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
                    ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
                    ->groupBy('DEALER_TARGET_MAST.ACC_CODE')
                    ->pluck(DB::raw('SUM(DEALER_TARGET_MAST.TARGET_AMT) as TARGET_AMT'),DB::raw("CONCAT(DEALER_TARGET_MAST.ACC_CODE,'1') AS ITEM_NAME"));


        // dd($finalMainlineTargetDataValue);

        //asav month sale 
        $finalMainlineMonthSaleCases = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY',function($join){
                            $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                            $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                        })
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        // ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                        // ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','PROD_MAST.PROD_CATG')
                        // ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')

                        // ->whereIn('MKTG_CATG_MAST.MKTG_CATG',$mainline_array)
                        ->whereIn('PROD_MAST.MKTG_CATG',$mainline_array)
                        // ->select(DB::raw("SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED"),DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'),'ITEMTRAN_HEAD.ACC_CODE')
                        ->select(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'),'ITEMTRAN_HEAD.ACC_CODE')
                        ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m')='$month'")
                        ->groupBy('ITEMTRAN_HEAD.ACC_CODE')
                        ->orderBy('ITEMTRAN_HEAD.VRDATE_FILTER','DESC')
                        ->get();

        $finalMainlineMonthSaleOut = array();
        foreach ($finalMainlineMonthSaleCases as $Mainlinekey => $Mainlinevalue) {

            $MainlineACC_CODE = $Mainlinevalue->ACC_CODE;

            $MonthSaleKey = $MainlineACC_CODE.'2';

            // $finalMainlineMonthSaleOut[$MonthSaleKey]['QTYISSUED'] = ROUND($Mainlinevalue->QTYISSUED,2);
            $finalMainlineMonthSaleOut[$MonthSaleKey]['VALISSUED'] = ROUND($Mainlinevalue->VALISSUED,2);
            
        }

        // Mainline comm sale

         $finalMainlineCummMonthSaleCases = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY',function($join){
                            $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                            $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                        })
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        // ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                        // ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','PROD_MAST.PROD_CATG')
                        //  ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')

                        // ->whereIn('MKTG_CATG_MAST.MKTG_CATG',$mainline_array)
                        ->whereIn('PROD_MAST.MKTG_CATG',$mainline_array)
                        // ->select(DB::raw("SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED"),DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'),'ITEMTRAN_HEAD.ACC_CODE')
                         ->select(DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'),'ITEMTRAN_HEAD.ACC_CODE')
                        ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$fromCummDate' AND date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$toDate'")
                        ->groupBy('ITEMTRAN_HEAD.ACC_CODE')
                        ->orderBy('ITEMTRAN_HEAD.VRDATE_FILTER','DESC')
                        ->get();



        $finalMainlineCummMonthSaleOut = array();
        foreach ($finalMainlineCummMonthSaleCases as $fscMainlinekey => $fscMainlinevalue) {

            $Mainline_ACC_CODE = $fscMainlinevalue->ACC_CODE.'3';


            // $finalMainlineCummMonthSaleOut[$Mainline_ACC_CODE]['QTYISSUED'] = ROUND($fscMainlinevalue->QTYISSUED,2);
            $finalMainlineCummMonthSaleOut[$Mainline_ACC_CODE]['VALISSUED'] = ROUND($fscMainlinevalue->VALISSUED,2);
            
        }


        // //////////////////////////// for MAINLINE Queries ends ////////////////////////////////////////////





        // //////////////////////////// for otcOne Queries starts ////////////////////////////////////////////

        // mainline target


      

        
        $otcOne_array = array('OTC');

        $finalotcOneTargetDataValue = DB::table('DEALER_TARGET_MAST')
                    ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','DEALER_TARGET_MAST.MKTG_CATG')  
                    ->whereIn('MKTG_CATG_MAST.MKTG_CATG',$otcOne_array)
                    ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
                    ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
                    ->groupBy('DEALER_TARGET_MAST.ACC_CODE')
                    ->pluck(DB::raw('SUM(DEALER_TARGET_MAST.TARGET_AMT) as TARGET_AMT'),DB::raw("CONCAT(DEALER_TARGET_MAST.ACC_CODE,'1') AS ITEM_NAME"));


        // dd($finalotcOneTargetDataValue);

        //asav month sale 
        $finalotcOneMonthSaleCases = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY',function($join){
                            $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                            $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                        })
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','PROD_MAST.PROD_CATG')
                        ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')

                        ->whereIn('MKTG_CATG_MAST.MKTG_CATG',$otcOne_array)
                        ->select(DB::raw("SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED"),DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'),'ITEMTRAN_HEAD.ACC_CODE')
                        ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m')='$month'")
                        ->groupBy('ITEMTRAN_HEAD.ACC_CODE')
                        ->orderBy('ITEMTRAN_HEAD.VRDATE_FILTER','DESC')
                        ->get();

        $finalotcOneMonthSaleOut = array();
        foreach ($finalotcOneMonthSaleCases as $otcOnekey => $otcOnevalue) {

            $otcOneACC_CODE = $otcOnevalue->ACC_CODE;

            $MonthSaleKey = $otcOneACC_CODE.'2';

            $finalotcOneMonthSaleOut[$MonthSaleKey]['QTYISSUED'] = ROUND($otcOnevalue->QTYISSUED,2);
            $finalotcOneMonthSaleOut[$MonthSaleKey]['VALISSUED'] = ROUND($otcOnevalue->VALISSUED,2);
            
        }

        // otcOne comm sale

         $finalotcOneCummMonthSaleCases = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY',function($join){
                            $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                            $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                        })
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','PROD_MAST.PROD_CATG')
                         ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')

                        ->whereIn('MKTG_CATG_MAST.MKTG_CATG',$otcOne_array)
                        ->select(DB::raw("SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED"),DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'),'ITEMTRAN_HEAD.ACC_CODE')
                        ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$fromCummDate' AND date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$toDate'")
                        ->groupBy('ITEMTRAN_HEAD.ACC_CODE')
                        ->orderBy('ITEMTRAN_HEAD.VRDATE_FILTER','DESC')
                        ->get();



        $finalotcOneCummMonthSaleOut = array();
        foreach ($finalotcOneCummMonthSaleCases as $fscotcOnekey => $fscotcOnevalue) {

            $otcOne_ACC_CODE = $fscotcOnevalue->ACC_CODE.'3';


            $finalotcOneCummMonthSaleOut[$otcOne_ACC_CODE]['QTYISSUED'] = ROUND($fscotcOnevalue->QTYISSUED,2);
            $finalotcOneCummMonthSaleOut[$otcOne_ACC_CODE]['VALISSUED'] = ROUND($fscotcOnevalue->VALISSUED,2);
            
        }


        // //////////////////////////// for otcOne Queries ends ////////////////////////////////////////////






            // //////////////////////////// for otcTwo Queries starts ////////////////////////////////////////////

        // mainline target


      

        
        $otcTwo_array = array('OT2');

        $finalotcTwoTargetDataValue = DB::table('DEALER_TARGET_MAST')
                    ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','DEALER_TARGET_MAST.MKTG_CATG')  
                    ->whereIn('MKTG_CATG_MAST.MKTG_CATG',$otcTwo_array)
                    ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
                    ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
                    ->groupBy('DEALER_TARGET_MAST.ACC_CODE')
                    ->pluck(DB::raw('SUM(DEALER_TARGET_MAST.TARGET_AMT) as TARGET_AMT'),DB::raw("CONCAT(DEALER_TARGET_MAST.ACC_CODE,'1') AS ITEM_NAME"));


        // dd($finalotcTwoTargetDataValue);

        //asav month sale 
        $finalotcTwoMonthSaleCases = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY',function($join){
                            $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                            $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                        })
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','PROD_MAST.PROD_CATG')
                        ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')

                        ->whereIn('MKTG_CATG_MAST.MKTG_CATG',$otcTwo_array)
                        ->select(DB::raw("SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED"),DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'),'ITEMTRAN_HEAD.ACC_CODE')
                        ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m')='$month'")
                        ->groupBy('ITEMTRAN_HEAD.ACC_CODE')
                        ->orderBy('ITEMTRAN_HEAD.VRDATE_FILTER','DESC')
                        ->get();

        $finalotcTwoMonthSaleOut = array();
        foreach ($finalotcTwoMonthSaleCases as $otcTwokey => $otcTwovalue) {

            $otcTwoACC_CODE = $otcTwovalue->ACC_CODE;

            $MonthSaleKey = $otcTwoACC_CODE.'2';

            $finalotcTwoMonthSaleOut[$MonthSaleKey]['QTYISSUED'] = ROUND($otcTwovalue->QTYISSUED,2);
            $finalotcTwoMonthSaleOut[$MonthSaleKey]['VALISSUED'] = ROUND($otcTwovalue->VALISSUED,2);
            
        }

        // otcTwo comm sale

         $finalotcTwoCummMonthSaleCases = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY',function($join){
                            $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                            $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                        })
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','PROD_MAST.PROD_CATG')
                         ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')

                        ->whereIn('MKTG_CATG_MAST.MKTG_CATG',$otcTwo_array)
                        ->select(DB::raw("SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED"),DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'),'ITEMTRAN_HEAD.ACC_CODE')
                        ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$fromCummDate' AND date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$toDate'")
                        ->groupBy('ITEMTRAN_HEAD.ACC_CODE')
                        ->orderBy('ITEMTRAN_HEAD.VRDATE_FILTER','DESC')
                        ->get();



        $finalotcTwoCummMonthSaleOut = array();
        foreach ($finalotcTwoCummMonthSaleCases as $fscotcTwokey => $fscotcTwovalue) {

            $otcTwo_ACC_CODE = $fscotcTwovalue->ACC_CODE.'3';


            $finalotcTwoCummMonthSaleOut[$otcTwo_ACC_CODE]['QTYISSUED'] = ROUND($fscotcTwovalue->QTYISSUED,2);
            $finalotcTwoCummMonthSaleOut[$otcTwo_ACC_CODE]['VALISSUED'] = ROUND($fscotcTwovalue->VALISSUED,2);
            
        }


        // //////////////////////////// for otcTwo Queries ends ////////////////////////////////////////////



        $finalTargetData = DB::table('DEALER_TARGET_MAST')
                    ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','DEALER_TARGET_MAST.PROD_CATG')
                    ->whereIn('PROD_CATG_MAST.PROD_CATG',$finalProducts)
                    ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
                    ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
                    ->groupBy('PROD_CATG_MAST.PROD_CATG','DEALER_TARGET_MAST.ACC_CODE')
                    ->pluck(DB::raw('SUM(DEALER_TARGET_MAST.TARGET_QTY) as TARGET_AMT'),DB::raw("CONCAT(DEALER_TARGET_MAST.ACC_CODE,'1',PROD_CATG_MAST.PROD_CATG) AS ITEM_NAME"));




         $finalMonthSaleCases = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY',function($join){
                            $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                            $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                        })
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','PROD_MAST.PROD_CATG')
                        ->whereIn('PROD_CATG_MAST.PROD_CATG',$finalProducts)
                        ->select(DB::raw("SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED"),DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'),'PROD_CATG_MAST.PROD_CATG','ITEMTRAN_HEAD.ACC_CODE')
                        ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m')='$month'")
                        ->groupBy('PROD_CATG_MAST.PROD_CATG','ITEMTRAN_HEAD.ACC_CODE')
                        ->orderBy('ITEMTRAN_HEAD.VRDATE_FILTER','DESC')
                        ->get();


        $finalMonthSaleOut = array();
        foreach ($finalMonthSaleCases as $fsckey => $fscvalue) {

            $arrayPeriodKey = '2';
            $ACC_CODE = $fscvalue->ACC_CODE;
            $PROD_CATG = $fscvalue->PROD_CATG;

            $MonthSaleKey = $ACC_CODE.$arrayPeriodKey.$PROD_CATG;

            $finalMonthSaleOut[$MonthSaleKey]['QTYISSUED'] = ROUND($fscvalue->QTYISSUED,2);
            $finalMonthSaleOut[$MonthSaleKey]['VALISSUED'] = ROUND($fscvalue->VALISSUED,2);
            
        }


                        // dd($finalMonthSaleOut);



        $finalCummMonthSaleCases = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY',function($join){
                            $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                            $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                        })
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','PROD_MAST.PROD_CATG')
                        ->whereIn('PROD_CATG_MAST.PROD_CATG',$finalProducts)
                        ->select(DB::raw("SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED"),DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'),'PROD_CATG_MAST.PROD_CATG','ITEMTRAN_HEAD.ACC_CODE')
                        ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$fromCummDate' AND date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$toDate'")
                        ->groupBy('PROD_CATG_MAST.PROD_CATG','ITEMTRAN_HEAD.ACC_CODE')
                        ->orderBy('ITEMTRAN_HEAD.VRDATE_FILTER','DESC')
                        ->get();



        $finalCummMonthSaleOut = array();
        foreach ($finalCummMonthSaleCases as $fscckey => $fsccvalue) {

            $arrayPeriodKey = '3';
            $ACC_CODE = $fsccvalue->ACC_CODE;
            $PROD_CATG = $fsccvalue->PROD_CATG;

            $MonthSaleKey = $ACC_CODE.$arrayPeriodKey.$PROD_CATG;

            $finalCummMonthSaleOut[$MonthSaleKey]['QTYISSUED'] = ROUND($fsccvalue->QTYISSUED,2);
            $finalCummMonthSaleOut[$MonthSaleKey]['VALISSUED'] = ROUND($fsccvalue->VALISSUED,2);
            
        }

        // dd($finalCummMonthSaleOut);

       $dealerDetailsCity = DB::table('ACC_MAST')
                ->join('dealer','dealer.dealer_code','=','ACC_MAST.ACC_CODE')
                ->join('dealer_person_login','dealer_person_login.dealer_id','=','dealer.id')
                ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                ->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
                ->where('dealer_person_login.role_id','=','5')
                ->where('dealer.dealer_status','=','1')
                ->groupBy('ACC_MAST.ACC_CODE')
                ->pluck('l4_name','ACC_CODE');



        $out = array();
        foreach($dealerDetails as $fdkey => $fdval){
            $ACC_NAME = $fdval->ACC_NAME;
            $ACC_CODE = $fdval->ACC_CODE;

            $out['ACC_NAME'] = $ACC_NAME;
            $out['ACC_CODE'] = $ACC_CODE;
            $out['DEALER_CITY'] = !empty($dealerDetailsCity[$fdval->ACC_CODE])?$dealerDetailsCity[$fdval->ACC_CODE]:'';

            $finalOut[] = (object)$out;

        }

        $price = array_column($finalOut, 'DEALER_CITY');

        array_multisort($price, SORT_ASC, $finalOut);

        // dd($finalOut);

        $dealerDetails = $finalOut;

        // dd($dealerDetails);

        // $finalTargetData = array();
        // $finalMonthSaleOut = array();
        // $finalCummMonthSaleOut = array();
       
         return view('DMS/saleStatement.ajax',[
            'current_menu'=>$this->current_menu,
            'dealerDetails'=> $dealerDetails,
            'arrayPeriod'=> $arrayPeriod,
            'finalProducts'=> $finalProducts,
            'dealerDetailsCity'=> $dealerDetailsCity,

            'finalTargetData'=> $finalTargetData,
            'finalMonthSaleOut'=> $finalMonthSaleOut,
            'finalCummMonthSaleOut'=> $finalCummMonthSaleOut,

            // ASAV array
            'finalASAVTargetDataCase'=> $finalASAVTargetDataCase,
            'finalASAVTargetDataValue'=> $finalASAVTargetDataValue,
            'finalASAVMonthSaleOut'=> $finalASAVMonthSaleOut,
            'finalASAVCummMonthSaleOut'=> $finalASAVCummMonthSaleOut,

             // mianline array
            'finalMainlineTargetDataValue'=> $finalMainlineTargetDataValue,
            'finalMainlineMonthSaleOut'=> $finalMainlineMonthSaleOut,
            'finalMainlineCummMonthSaleOut'=> $finalMainlineCummMonthSaleOut,


              // otcOne array
            'finalotcOneTargetDataValue'=> $finalotcOneTargetDataValue,
            'finalotcOneMonthSaleOut'=> $finalotcOneMonthSaleOut,
            'finalotcOneCummMonthSaleOut'=> $finalotcOneCummMonthSaleOut,



              // otcTwo array
            'finalotcTwoTargetDataValue'=> $finalotcTwoTargetDataValue,
            'finalotcTwoMonthSaleOut'=> $finalotcTwoMonthSaleOut,
            'finalotcTwoCummMonthSaleOut'=> $finalotcTwoCummMonthSaleOut,
            
        ]);
    }






     public function saleStatementEthicalAjax(Request $request)
    {
        // dd('1');

        $month = $request->month;
        $terr_id = $request->terr_id;
        $fromDate = date('Y-m-01',strtotime($month));

        // dd($fromDate);

        $toDate = date('Y-m-t',strtotime($month));
        $from_target_date = "01-APR-" .(date('y'));
        $to_target_date = "31-MAR-" .(date('y')+1);
        // dd($toDate);

         $calcMonth = date('m');
        // $month = "04";

        if($calcMonth == "01" || $calcMonth == "02" || $calcMonth == "03"){
            $currYear = date('Y');
            $lastYear = $currYear-1;

            $fromCummDate = date($lastYear.'-04'.'-01');

        }else{
            $fromCummDate = date('Y'.'-04'.'-01');
        }



         $dealerDetailsMast = DB::table('ACC_MAST')
                    ->select('ACC_MAST.ACC_NAME','ACC_MAST.ACC_CODE')
                    ->join('dealer','dealer.dealer_code','=','ACC_MAST.ACC_CODE')
                    ->join('dealer_person_login','dealer_person_login.dealer_id','=','dealer.id')
                    ->where('dealer_person_login.role_id','=','5')
                    ->where('dealer.dealer_status','=','1')
                    ->groupBy('ACC_MAST.ACC_CODE');
                    if(!empty($terr_id)){
                        $dealerDetailsMast->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                                          ->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
                                          ->whereIn('l2_id',$terr_id);
                    }

        $dealerDetails = $dealerDetailsMast->get();

        // $arrayPeriod = array("20-21","TARGET","MONTH","C.PRD");


        $arrayPeriod = array("1"=>"TARGET","2"=>"MONTH","3"=>"C.PRD");


      




        // $finalProducts = DB::table('SALE_MIS_CATG_MAST')
        //                 // ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','SALE_MIS_CATG_MAST.PROD_CATG')
        //                 ->where('SALE_MIS_CATG_MAST.PROD_CATG','!=',NULL)
        //                 ->where('SALE_MIS_CATG_MAST.PROD_CATG','!=','')
        //                 ->where('SALE_MIS_CATG_MAST.PROD_CATG','!=','ASV')
        //                 ->orderBy('SALE_MIS_CATG_MAST.USER_SEQ','ASC')
        //                 ->pluck('SALE_MIS_CATG_MAST.PROD_CATG','SALE_MIS_CATG_MAST.PROD_CATG');


        $ethicalArray = array('JPS','JP2');

        $finalProducts = DB::table('PROD_CATG_MAST')
                        // ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','SALE_MIS_CATG_MAST.PROD_CATG')
                        ->whereIn('PROD_CATG_MAST.MKTG_CATG',$ethicalArray)
                        ->orderBy('PROD_CATG_MAST.USER_SEQ','ASC')
                        ->pluck('PROD_CATG_MAST.PROD_CATG','PROD_CATG_MAST.PROD_CATG');





      





        // //////////////////////////// for otcOne Queries starts ////////////////////////////////////////////

        // mainline target


      

        
        $jps_array = array('JPS');

        $finalotcOneTargetDataValue = DB::table('DEALER_TARGET_MAST')
                    ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','DEALER_TARGET_MAST.MKTG_CATG')  
                    ->whereIn('MKTG_CATG_MAST.MKTG_CATG',$jps_array)
                    ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
                    ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
                    ->groupBy('DEALER_TARGET_MAST.ACC_CODE')
                    ->pluck(DB::raw('SUM(DEALER_TARGET_MAST.TARGET_AMT) as TARGET_AMT'),DB::raw("CONCAT(DEALER_TARGET_MAST.ACC_CODE,'1') AS ITEM_NAME"));


        // dd($finalotcOneTargetDataValue);

        //asav month sale 
        $finalotcOneMonthSaleCases = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY',function($join){
                            $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                            $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                        })
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','PROD_MAST.PROD_CATG')
                        ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')

                        ->whereIn('MKTG_CATG_MAST.MKTG_CATG',$jps_array)
                        ->select(DB::raw("SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED"),DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'),'ITEMTRAN_HEAD.ACC_CODE')
                        ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m')='$month'")
                        ->groupBy('ITEMTRAN_HEAD.ACC_CODE')
                        ->orderBy('ITEMTRAN_HEAD.VRDATE_FILTER','DESC')
                        ->get();

        $finalotcOneMonthSaleOut = array();
        foreach ($finalotcOneMonthSaleCases as $otcOnekey => $otcOnevalue) {

            $otcOneACC_CODE = $otcOnevalue->ACC_CODE;

            $MonthSaleKey = $otcOneACC_CODE.'2';

            $finalotcOneMonthSaleOut[$MonthSaleKey]['QTYISSUED'] = ROUND($otcOnevalue->QTYISSUED,2);
            $finalotcOneMonthSaleOut[$MonthSaleKey]['VALISSUED'] = ROUND($otcOnevalue->VALISSUED,2);
            
        }

        // otcOne comm sale

         $finalotcOneCummMonthSaleCases = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY',function($join){
                            $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                            $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                        })
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','PROD_MAST.PROD_CATG')
                         ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')

                        ->whereIn('MKTG_CATG_MAST.MKTG_CATG',$jps_array)
                        ->select(DB::raw("SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED"),DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'),'ITEMTRAN_HEAD.ACC_CODE')
                        ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$fromCummDate' AND date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$toDate'")
                        ->groupBy('ITEMTRAN_HEAD.ACC_CODE')
                        ->orderBy('ITEMTRAN_HEAD.VRDATE_FILTER','DESC')
                        ->get();



        $finalotcOneCummMonthSaleOut = array();
        foreach ($finalotcOneCummMonthSaleCases as $fscotcOnekey => $fscotcOnevalue) {

            $otcOne_ACC_CODE = $fscotcOnevalue->ACC_CODE.'3';


            $finalotcOneCummMonthSaleOut[$otcOne_ACC_CODE]['QTYISSUED'] = ROUND($fscotcOnevalue->QTYISSUED,2);
            $finalotcOneCummMonthSaleOut[$otcOne_ACC_CODE]['VALISSUED'] = ROUND($fscotcOnevalue->VALISSUED,2);
            
        }


        // //////////////////////////// for otcOne Queries ends ////////////////////////////////////////////






            // //////////////////////////// for otcTwo Queries starts ////////////////////////////////////////////

        // mainline target


      

        
        $jpsTwo_array = array('OT2');

        $finalotcTwoTargetDataValue = DB::table('DEALER_TARGET_MAST')
                    ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','DEALER_TARGET_MAST.MKTG_CATG')  
                    ->whereIn('MKTG_CATG_MAST.MKTG_CATG',$jpsTwo_array)
                    ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
                    ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
                    ->groupBy('DEALER_TARGET_MAST.ACC_CODE')
                    ->pluck(DB::raw('SUM(DEALER_TARGET_MAST.TARGET_AMT) as TARGET_AMT'),DB::raw("CONCAT(DEALER_TARGET_MAST.ACC_CODE,'1') AS ITEM_NAME"));


        // dd($finalotcTwoTargetDataValue);

        //asav month sale 
        $finalotcTwoMonthSaleCases = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY',function($join){
                            $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                            $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                        })
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','PROD_MAST.PROD_CATG')
                        ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')

                        ->whereIn('MKTG_CATG_MAST.MKTG_CATG',$jpsTwo_array)
                        ->select(DB::raw("SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED"),DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'),'ITEMTRAN_HEAD.ACC_CODE')
                        ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m')='$month'")
                        ->groupBy('ITEMTRAN_HEAD.ACC_CODE')
                        ->orderBy('ITEMTRAN_HEAD.VRDATE_FILTER','DESC')
                        ->get();

        $finalotcTwoMonthSaleOut = array();
        foreach ($finalotcTwoMonthSaleCases as $otcTwokey => $otcTwovalue) {

            $otcTwoACC_CODE = $otcTwovalue->ACC_CODE;

            $MonthSaleKey = $otcTwoACC_CODE.'2';

            $finalotcTwoMonthSaleOut[$MonthSaleKey]['QTYISSUED'] = ROUND($otcTwovalue->QTYISSUED,2);
            $finalotcTwoMonthSaleOut[$MonthSaleKey]['VALISSUED'] = ROUND($otcTwovalue->VALISSUED,2);
            
        }

        // otcTwo comm sale

         $finalotcTwoCummMonthSaleCases = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY',function($join){
                            $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                            $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                        })
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','PROD_MAST.PROD_CATG')
                         ->join('MKTG_CATG_MAST','MKTG_CATG_MAST.MKTG_CATG','=','PROD_CATG_MAST.MKTG_CATG')

                        ->whereIn('MKTG_CATG_MAST.MKTG_CATG',$jpsTwo_array)
                        ->select(DB::raw("SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED"),DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'),'ITEMTRAN_HEAD.ACC_CODE')
                        ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$fromCummDate' AND date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$toDate'")
                        ->groupBy('ITEMTRAN_HEAD.ACC_CODE')
                        ->orderBy('ITEMTRAN_HEAD.VRDATE_FILTER','DESC')
                        ->get();



        $finalotcTwoCummMonthSaleOut = array();
        foreach ($finalotcTwoCummMonthSaleCases as $fscotcTwokey => $fscotcTwovalue) {

            $otcTwo_ACC_CODE = $fscotcTwovalue->ACC_CODE.'3';


            $finalotcTwoCummMonthSaleOut[$otcTwo_ACC_CODE]['QTYISSUED'] = ROUND($fscotcTwovalue->QTYISSUED,2);
            $finalotcTwoCummMonthSaleOut[$otcTwo_ACC_CODE]['VALISSUED'] = ROUND($fscotcTwovalue->VALISSUED,2);
            
        }


        // //////////////////////////// for otcTwo Queries ends ////////////////////////////////////////////



        $finalTargetData = DB::table('DEALER_TARGET_MAST')
                    ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','DEALER_TARGET_MAST.PROD_CATG')
                    ->whereIn('PROD_CATG_MAST.PROD_CATG',$finalProducts)
                    ->where('DEALER_TARGET_MAST.FROM_DATE', '=', $from_target_date)
                    ->where('DEALER_TARGET_MAST.TO_DATE', '=', $to_target_date)
                    ->groupBy('PROD_CATG_MAST.PROD_CATG','DEALER_TARGET_MAST.ACC_CODE')
                    ->pluck(DB::raw('SUM(DEALER_TARGET_MAST.TARGET_QTY) as TARGET_AMT'),DB::raw("CONCAT(DEALER_TARGET_MAST.ACC_CODE,'1',PROD_CATG_MAST.PROD_CATG) AS ITEM_NAME"));




         $finalMonthSaleCases = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY',function($join){
                            $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                            $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                        })
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','PROD_MAST.PROD_CATG')
                        ->whereIn('PROD_CATG_MAST.PROD_CATG',$finalProducts)
                        ->select(DB::raw("SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED"),DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'),'PROD_CATG_MAST.PROD_CATG','ITEMTRAN_HEAD.ACC_CODE')
                        ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m')='$month'")
                        ->groupBy('PROD_CATG_MAST.PROD_CATG','ITEMTRAN_HEAD.ACC_CODE')
                        ->orderBy('ITEMTRAN_HEAD.VRDATE_FILTER','DESC')
                        ->get();


        $finalMonthSaleOut = array();
        foreach ($finalMonthSaleCases as $fsckey => $fscvalue) {

            $arrayPeriodKey = '2';
            $ACC_CODE = $fscvalue->ACC_CODE;
            $PROD_CATG = $fscvalue->PROD_CATG;

            $MonthSaleKey = $ACC_CODE.$arrayPeriodKey.$PROD_CATG;

            $finalMonthSaleOut[$MonthSaleKey]['QTYISSUED'] = ROUND($fscvalue->QTYISSUED,2);
            $finalMonthSaleOut[$MonthSaleKey]['VALISSUED'] = ROUND($fscvalue->VALISSUED,2);
            
        }


                        // dd($finalMonthSaleOut);



        $finalCummMonthSaleCases = DB::table('ITEMTRAN_HEAD')
                        ->join('ITEMTRAN_BODY',function($join){
                            $join->on('ITEMTRAN_BODY.VRNO', '=', 'ITEMTRAN_HEAD.VRNO');
                            $join->on('ITEMTRAN_BODY.DIV_CODE', '=', 'ITEMTRAN_HEAD.DIV_CODE');

                        })
                        ->join('ITEM_MAST','ITEM_MAST.ITEM_CODE','=','ITEMTRAN_BODY.ITEM_CODE')
                        ->join('ITEM_AUM_MAST','ITEM_AUM_MAST.ITEM_CODE','=','ITEM_MAST.ITEM_CODE')
                        ->join('PROD_MAST','PROD_MAST.PROD_CODE','=','ITEM_MAST.PROD_CODE')
                        ->join('PROD_CATG_MAST','PROD_CATG_MAST.PROD_CATG','=','PROD_MAST.PROD_CATG')
                        ->whereIn('PROD_CATG_MAST.PROD_CATG',$finalProducts)
                        ->select(DB::raw("SUM(ITEMTRAN_BODY.QTYISSUED/ITEM_AUM_MAST.UMTOAUM) as QTYISSUED"),DB::raw('SUM(ITEMTRAN_BODY.VALISSUED) as VALISSUED'),'PROD_CATG_MAST.PROD_CATG','ITEMTRAN_HEAD.ACC_CODE')
                        ->whereRaw("date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')>='$fromCummDate' AND date_format(ITEMTRAN_HEAD.VRDATE_FILTER,'%Y-%m-%d')<='$toDate'")
                        ->groupBy('PROD_CATG_MAST.PROD_CATG','ITEMTRAN_HEAD.ACC_CODE')
                        ->orderBy('ITEMTRAN_HEAD.VRDATE_FILTER','DESC')
                        ->get();



        $finalCummMonthSaleOut = array();
        foreach ($finalCummMonthSaleCases as $fscckey => $fsccvalue) {

            $arrayPeriodKey = '3';
            $ACC_CODE = $fsccvalue->ACC_CODE;
            $PROD_CATG = $fsccvalue->PROD_CATG;

            $MonthSaleKey = $ACC_CODE.$arrayPeriodKey.$PROD_CATG;

            $finalCummMonthSaleOut[$MonthSaleKey]['QTYISSUED'] = ROUND($fsccvalue->QTYISSUED,2);
            $finalCummMonthSaleOut[$MonthSaleKey]['VALISSUED'] = ROUND($fsccvalue->VALISSUED,2);
            
        }

        // dd($finalCummMonthSaleOut);


         $dealerDetailsCity = DB::table('ACC_MAST')
                    ->join('dealer','dealer.dealer_code','=','ACC_MAST.ACC_CODE')
                    ->join('dealer_person_login','dealer_person_login.dealer_id','=','dealer.id')
                    ->join('dealer_location_rate_list','dealer_location_rate_list.dealer_id','=','dealer.id')
                    ->join('location_view','location_view.l5_id','=','dealer_location_rate_list.location_id')
                    ->where('dealer_person_login.role_id','=','5')
                    ->where('dealer.dealer_status','=','1')
                    ->groupBy('ACC_MAST.ACC_CODE')
                    ->pluck('l4_name','ACC_CODE');

                    // dd($dealerDetailsCity);

        $out = array();
        foreach($dealerDetails as $fdkey => $fdval){
            $ACC_NAME = $fdval->ACC_NAME;
            $ACC_CODE = $fdval->ACC_CODE;

            $out['ACC_NAME'] = $ACC_NAME;
            $out['ACC_CODE'] = $ACC_CODE;
            $out['DEALER_CITY'] = !empty($dealerDetailsCity[$fdval->ACC_CODE])?$dealerDetailsCity[$fdval->ACC_CODE]:'';

            $finalOut[] = (object)$out;

        }

        $price = array_column($finalOut, 'DEALER_CITY');

        array_multisort($price, SORT_ASC, $finalOut);

        // dd($finalOut);

        $dealerDetails = $finalOut;

        // $finalTargetData = array();
        // $finalMonthSaleOut = array();
        // $finalCummMonthSaleOut = array();
       
         return view('DMS/saleStatementEthical.ajax',[
            'current_menu'=>$this->current_menu,
            'dealerDetails'=> $dealerDetails,
            'arrayPeriod'=> $arrayPeriod,
            'finalProducts'=> $finalProducts,
            'dealerDetailsCity'=> $dealerDetailsCity,

            'finalTargetData'=> $finalTargetData,
            'finalMonthSaleOut'=> $finalMonthSaleOut,
            'finalCummMonthSaleOut'=> $finalCummMonthSaleOut,



              // otcOne array
            'finalotcOneTargetDataValue'=> $finalotcOneTargetDataValue,
            'finalotcOneMonthSaleOut'=> $finalotcOneMonthSaleOut,
            'finalotcOneCummMonthSaleOut'=> $finalotcOneCummMonthSaleOut,



              // otcTwo array
            'finalotcTwoTargetDataValue'=> $finalotcTwoTargetDataValue,
            'finalotcTwoMonthSaleOut'=> $finalotcTwoMonthSaleOut,
            'finalotcTwoCummMonthSaleOut'=> $finalotcTwoCummMonthSaleOut,
            
        ]);
    }



    
    
}
