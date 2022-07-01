<?php

namespace App\Http\Controllers;

use App\_outletType;
use App\Area;
use App\Catalog1;
use App\Claim;
use App\competitorBrand;
use App\CompetitorsPriceLog;
use App\competitorsProduct;
use App\DailyStock;
use App\Dealer;
use App\DealerLocation; 
use App\DealerRetailer;
use App\DistributorTarget;
use App\Feedback;
use App\Location1;
use App\Location2; 
use App\Location6;
use App\Location7;
use App\Location3;
use App\Location4;
use App\Location5;
use App\MonthlyTourProgram;
use App\ReceiveOrder;
use App\Retailer;
use App\User;
use App\UserDealerRetailer;
use App\UserDetail;
use App\UserExpenseReport;
use App\UserSalesOrder;
use App\Vendor;
use App\CatalogProduct;
use App\UsersDetail;
use Illuminate\Http\Request;
use DB;
use DateTime;

class TallyController extends Controller
{
     ##..............................Tally Report SS billing...........................................##
    public function tallySsBillingReport(Request $request)
    {
        $state = $request->area;
        $stockist = $request->stockist;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        // dd($stockist);
        $tally_query =  DB::table('tally_demo')->join('dealer','dealer.id','=','tally_demo.dealer_id')->join('csa','csa.c_id','=','tally_demo.csa_id')->join('location_view','location_view.l3_id','=','csa.state_id')->select('location_view.l3_name as state_name','tally_demo.id','ch_no','first_ch_no','ch_date','vocher_id','dealer.dealer_code','ss_voucher_id','tally_demo.csa_id as csa_id','csa.csa_code as csa_code','csa.csa_name as csa_name', 'dealer_id','dealer.name AS dname','invoice_type','location_view.l4_name as town_name')->whereRaw("DATE_FORMAT(tally_demo.ch_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(tally_demo.ch_date,'%Y-%m-%d')<='$to_date'")->whereRaw("(dealer.dealer_code != 'Dealer Code' OR dealer.dealer_code !='')")->groupBy('tally_demo.id')->orderBy('tally_demo.ch_no','DESC');

        if(!empty($state))
        {
            $tally_query->whereIn('location_view.l3_id',$state);
        }
        if(!empty($stockist))
        {
            $tally_query->whereIn('csa.c_id',$stockist);
        }
        $tally_query_data = $tally_query->get();
        // dd($tally_query_data);
        
        $out=[];

        if(!empty($tally_query_data))
        {   
            foreach($tally_query_data as $Tkey => $Tvalue)
            {
                $tally_id= $Tvalue->id;
                $out[$tally_id]=DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->select('tally_demo_details.id AS id', 'td_id', 'tally_demo_details.product_id as tall_demo_product_id', 'qty', 'rate', 'gst', 'gst_amt', 'dis_per', 'dis_amt', 'taxable_amt', 'item_for', 'tally_catalog_product.name AS pname')->where('td_id',$tally_id)->get();
            }

        }
        // dd($out);
        return view('reports.tally_ss_billing_report.ajax', [
            'records' => $tally_query_data,
            'details' => $out   
        ]);
    }
    
    ##..............................Tally Report SS billing Ends here..................................##
    ##..............................Tally Report SS Closing Report.....................................##
    public function tallClosingReport(Request $request)
    {
        $state_id = $request->area;
        $start = $request->from_date;
        $end = $request->to_date;
        $to_date = date('Y-m-d', strtotime($start .' -1 day'));
        $from_date = "2010-04-01";
        $opening  = '';
        $opening_pcs = '';
        $opurchase1  = '';
        $opurchase1_pcs  = '';
        $opurchase2  = '';
        $opurchase2_pcs  = '';
        $opurchase3  = '';
        $opurchase3_pcs  = '';
        $obilled1  = '';
        $obilled1_pcs  = '';
        $obilled2  = '';
        $obilled2_pcs  = '';
        $obilled3  = '';
        $obilled3_pcs  = '';
        $inward1  = '';
        $inward1_pcs  = '';
        $inward2  = '';
        $inward2_pcs  = '';
        $inward3  = '';
        $inward3_pcs  = '';
        $outward1  = '';
        $outward1_pcs  = '';
        $outward2  = '';
        $outward2_pcs  = '';
        $outward3  = '';
        $outward3_pcs  = '';
        $rate  = '';
        $rate_pcs  = '';
        $gst_rate  = '';
        $unit  = '';
        $out = [];
        $closing_Query_data = DB::table('tally_opening_stock')->join('csa','csa.c_id','=','tally_opening_stock.csa_id')->join('tally_catalog_product','tally_catalog_product.id','=','tally_opening_stock.product_id')->join('location_view','location_view.l3_id','=','csa.state_id')->select('tally_opening_stock.opening AS apr_open','tally_opening_stock.id','csa_id','csa.csa_name as csa_name','csa.csa_code as csa_code','tally_opening_stock.product_id','tally_catalog_product.name as pname','from_date','to_date','server_date_time','location_view.l3_name','location_view.l3_id as cstate_id','tally_catalog_product.itemcode as itemcode')->groupBy('tally_opening_stock.product_id')->orderBy('csa_name','pname');
        if(!empty($state_id))
        {
            $closing_Query_data->whereIn('csa.state_id',$state_id);           
        }
        $closing_Query = $closing_Query_data->get();
        foreach ($closing_Query as $key => $value) {
            $product_id = $value->product_id;
            $id = $value->id;
            $csa_id = $value->csa_id;
            $cstate_id = $value->cstate_id;
            $unit = DB::table('tally_catalog_product')->select('quantity_per_case')->where('id',$product_id)->first();

            $opening_data = DB::table('tally_opening_stock')->select(DB::raw('round(sum(opening)) as case_qty'))->where('tally_opening_stock.csa_id',$csa_id)->where('tally_opening_stock.product_id',$product_id)->where('unit','CASE')->first(); 

            $opening_pcs_data = DB::table('tally_opening_stock')->select(DB::raw('round(sum(opening)) as case_qty'))->where('tally_opening_stock.csa_id',$csa_id)->where('tally_opening_stock.product_id',$product_id)->where('unit','PCS')->first(); 

            $cqty = !empty($opening_data->case_qty)?$opening_data->case_qty:0;
            $pcs = !empty($opening_pcs_data->case_qty)?$opening_pcs_data->case_qty:0;

            if($pcs==0 || $unit->quantity_per_case==0 || $cqty==0)
            {
                $case_qty=0;
                $opening_pcs=$pcs;
            }
            else
            {
                $case_qty=ROUND(($pcs)/($unit->quantity_per_case));
                $opening_pcs=($pcs)%($unit->quantity_per_case);
            }
            $opening = $cqty+$case_qty;


            $opurchase1_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$to_date'")->where('tally_demo.drcr','CR')->whereIn('tally_demo.invoice_type',[4,5,6,7])->where('unit','CASE')->first();

            $opurchase1_pcs_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$to_date'")->where('tally_demo.drcr','CR')->whereIn('tally_demo.invoice_type',[4,5,6,7])->where('unit','PCS')->first();

                    $cqty = !empty($opurchase1_data->case_qty)?$opurchase1_data->case_qty:0;
                    $pcs = !empty($opurchase1_pcs_data->case_qty)?$opurchase1_pcs_data->case_qty:0;

                    if($pcs==0 || $unit->quantity_per_case==0 || $cqty==0)
                    {
                        $case_qty=0;
                        $opurchase1_pcs=$pcs;
                    }
                    else
                    {
                        $case_qty=ROUND(($pcs)/($unit->quantity_per_case));

                        $opurchase1_pcs=($pcs)%($unit->quantity_per_case);
                    }

                    $opurchase1 = $cqty+$case_qty;


            $opurchase2_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$to_date'")->where('tally_demo.drcr','DR')->whereIn('tally_demo.invoice_type',[4,5,6,7])->where('unit','CASE')->first();

            $opurchase2_pcs_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$to_date'")->where('tally_demo.drcr','DR')->whereIn('tally_demo.invoice_type',[4,5,6,7])->where('unit','PCS')->first();

                $cqty = !empty($opurchase2_data->case_qty)?$opurchase2_data->case_qty:0;
                $pcs = !empty($opurchase2_pcs_data->case_qty)?$opurchase2_pcs_data->case_qty:0;

                    if($pcs==0 || $unit->quantity_per_case==0 || $cqty==0){
                    $case_qty=0;
                    $opurchase2_pcs=$pcs;
                    }else{
                        $case_qty=ROUND($pcs/$unit->quantity_per_case);
                        $opurchase2_pcs=($pcs)%($unit->quantity_per_case);
                    }

                    $opurchase2 = ($cqty)+($case_qty);

            $opurchase3_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$to_date'")->where('tally_demo_details.cr_dr','CR')->whereIn('tally_demo.invoice_type',[8,9])->where('unit','CASE')->first();

            $opurchase3_pcs_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$to_date'")->where('tally_demo_details.cr_dr','CR')->whereIn('tally_demo.invoice_type',[8,9])->where('unit','PCS')->first();

                $cqty = !empty($opurchase3_data->case_qty)?$opurchase3_data->case_qty:0;
                $pcs = !empty($opurchase3_pcs_data->case_qty)?$opurchase3_pcs_data->case_qty:0;

                    if($pcs==0 || $unit->quantity_per_case==0 || $cqty==0)
                    {
                        $case_qty=0;
                        $opurchase3_pcs=$pcs;
                    }
                    else
                    {
                        $case_qty=ROUND($pcs/($unit->quantity_per_case));

                        $opurchase3_pcs=($pcs)%($unit->quantity_per_case);
                    }

                    $opurchase3 = ($cqty)+($case_qty);


                $opurchase=$opurchase1-$opurchase2+$opurchase3;
                // print_r($opurchase);
                $opurchase_pcs=($opurchase1_pcs)-($opurchase2_pcs)+($opurchase3_pcs);

            $obilled1_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$to_date'")->where('tally_demo.drcr','CR')->whereIn('tally_demo.invoice_type',[0,1,2,3])->where('unit','CASE')->first();

            $obilled1_pcs_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$to_date'")->where('tally_demo.drcr','CR')->whereIn('tally_demo.invoice_type',[0,1,2,3])->where('unit','PCS')->first();

                    $cqty = !empty($obilled1_data->case_qty)?$obilled1_data->case_qty:0;
                    $pcs = !empty($obilled1_pcs_data->case_qty)?$obilled1_pcs_data->case_qty:0;

                    if($pcs==0 || $unit->quantity_per_case==0 || $cqty==0)
                    {
                        $case_qty=0;
                        $obilled1_pcs=$pcs;
                    }
                    else
                    {
                        $case_qty=ROUND(($pcs)/($unit->quantity_per_case));

                        $obilled1_pcs=($pcs)%($unit->quantity_per_case);
                    }

                    $obilled1 = ($cqty)+($case_qty);


            $obilled2_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$to_date'")->where('tally_demo.drcr','DR')->whereIn('tally_demo.invoice_type',[0,1,2,3])->where('unit','CASE')->first();

            $obilled2_pcs_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$to_date'")->where('tally_demo.drcr','DR')->whereIn('tally_demo.invoice_type',[0,1,2,3])->where('unit','PCS')->first();

                $cqty = !empty($obilled2_data->case_qty)?$obilled2_data->case_qty:0;
                $pcs = !empty($obilled2_pcs_data->case_qty)?$obilled2_pcs_data->case_qty:0;

                    if($pcs==0 || $unit->quantity_per_case==0 || $cqty==0)
                    {
                        $case_qty=0;
                        $obilled2_pcs=$pcs;
                    }
                    else
                    {
                        $case_qty=ROUND(($pcs)/($unit->quantity_per_case));

                        $obilled2_pcs=($pcs)%($unit->quantity_per_case);
                    }

                    $obilled2 = ($cqty)+($case_qty);

            $obilled3_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$to_date'")->where('tally_demo_details.cr_dr','DR')->whereIn('tally_demo.invoice_type',[8,9])->where('unit','CASE')->first();

            $obilled3_pcs_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$from_date' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$to_date'")->where('tally_demo_details.cr_dr','DR')->whereIn('tally_demo.invoice_type',[8,9])->where('unit','PCS')->first();


                $cqty = !empty($obilled3_data->case_qty)?$obilled3_data->case_qty:0;
                $pcs = !empty($obilled3_pcs_data->case_qty)?$obilled3_pcs_data->case_qty:0;

                    if($pcs==0 || $unit->quantity_per_case==0 || $cqty==0)
                    {
                        $case_qty=0;
                        $obilled3_pcs=$pcs;
                    }
                    else
                    {
                        $case_qty=ROUND(($pcs)/($unit->quantity_per_case));

                        $obilled3_pcs=($pcs)%($unit->quantity_per_case);
                    }

                    $obilled3 = $cqty+$case_qty;


                $obilled=$obilled2-$obilled1+$obilled3;
                $obilled_pcs=($obilled2_pcs)-($obilled1_pcs)+($obilled3_pcs);

                $opening_stock=($opening+$opurchase)-$obilled;
                $opening_stock_pcs=($opening_pcs+$opurchase_pcs)-$obilled_pcs;


            $inward1_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$start' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$end'")->where('tally_demo.drcr','DR')->whereIn('tally_demo.invoice_type',[4,5,6,7])->where('unit','CASE')->first();

            $inward1_pcs_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$start' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$end'")->where('tally_demo.drcr','DR')->whereIn('tally_demo.invoice_type',[4,5,6,7])->where('unit','PCS')->first();


                $cqty = !empty($inward1_data->case_qty)?$inward1_data->case_qty:0;
                $pcs = !empty($inward1_pcs_data->case_qty)?$inward1_pcs_data->case_qty:0;

                    if($pcs==0 || $unit->quantity_per_case==0 || $cqty==0)
                    {
                        $case_qty=0;
                        $inward1_pcs=$pcs;
                    }
                    else
                    {
                        $case_qty=ROUND(($pcs)/($unit->quantity_per_case));

                        $inward1_pcs=($pcs)%($unit->quantity_per_case);
                    }
                     $inward1 = $cqty+$case_qty;

            $inward2_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$start' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$end'")->where('tally_demo.drcr','CR')->whereIn('tally_demo.invoice_type',[4,5,6,7])->where('unit','CASE')->first();

            $inward2_pcs_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$start' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$end'")->where('tally_demo.drcr','CR')->whereIn('tally_demo.invoice_type',[4,5,6,7])->where('unit','PCS')->first();

                $cqty = !empty($inward2_data->case_qty)?$inward2_data->case_qty:0;
                $pcs = !empty($inward2_pcs_data->case_qty)?$inward2_pcs_data->case_qty:0;

                    if($pcs==0 || $unit->quantity_per_case==0 || $cqty==0)
                    {
                        $case_qty=0;
                        $inward2_pcs=$pcs;
                    }
                    else
                    {
                        $case_qty=ROUND($pcs/($unit->quantity_per_case));

                        $inward2_pcs=($pcs)%($unit->quantity_per_case);
                    }

                    $inward2 = $cqty+$case_qty;

            $inward3_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$start' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$end'")->where('tally_demo_details.cr_dr','CR')->whereIn('tally_demo.invoice_type',[8,9])->where('unit','CASE')->first();

            $inward3_pcs_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$start' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$end'")->where('tally_demo_details.cr_dr','CR')->whereIn('tally_demo.invoice_type',[8,9])->where('unit','PCS')->first();

                $cqty = !empty($inward3_data->case_qty)?$inward3_data->case_qty:0;
                $pcs = !empty($inward3_pcs_data->case_qty)?$inward3_pcs_data->case_qty:0;

                    if($pcs==0 || $unit->quantity_per_case==0 || $cqty==0)
                    {
                        $case_qty=0;
                        $inward3_pcs=$pcs;
                    }
                    else
                    {
                        $case_qty=ROUND(($pcs)/($unit->quantity_per_case));

                        $inward3_pcs=($pcs)%($unit->quantity_per_case);
                    }

                    $inward3 = $cqty+$case_qty;


                $inward=$inward2-$inward1+$inward3;
                // dd($inward);
                if(empty($inward)){
                    $inward=0;
                }
                $inward_pcs=($inward2_pcs)-($inward1_pcs)+($inward3_pcs);
                // dd($inward_pcs);
                if(empty($inward_pcs)){
                    $inward_pcs=0;
                }

            $outward1_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$start' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$end'")->where('tally_demo.drcr','CR')->whereIn('tally_demo.invoice_type',[0,1,2,3])->where('unit','CASE')->first();

            $outward1_pcs_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$start' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$end'")->where('tally_demo.drcr','CR')->whereIn('tally_demo.invoice_type',[0,1,2,3])->where('unit','PCS')->first();

                $cqty = !empty($outward1_data->case_qty)?$outward1_data->case_qty:0;
                $pcs = !empty($outward1_pcs_data->case_qty)?$outward1_pcs_data->case_qty:0;

                    if($pcs==0 || $unit->quantity_per_case==0 || $cqty==0)
                    {
                        $case_qty=0;
                        $outward1_pcs=$pcs;
                    }
                    else
                    {
                        $case_qty=ROUND(($pcs)/($unit->quantity_per_case));

                        $outward1_pcs=($pcs)%($unit->quantity_per_case);
                    }

                    $outward1 = $cqty+$case_qty;

            $outward2_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$start' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$end'")->where('tally_demo.drcr','DR')->whereIn('tally_demo.invoice_type',[0,1,2,3])->where('unit','CASE')->first();

            $outward2_pcs_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$start' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$end'")->where('tally_demo.drcr','DR')->whereIn('tally_demo.invoice_type',[0,1,2,3])->where('unit','PCS')->first();


                $cqty = !empty($outward2_data->case_qty)?$outward2_data->case_qty:0;
                $pcs = !empty($outward2_pcs_data->case_qty)?$outward2_pcs_data->case_qty:0;

                    if($pcs==0 || $unit->quantity_per_case==0 || $cqty==0)
                    {
                        $case_qty=0;
                        $outward2_pcs=$pcs;
                    }
                    else
                    {
                        $case_qty=ROUND(($pcs)/($unit->quantity_per_case));

                        $outward2_pcs=($pcs)%($unit->quantity_per_case);
                    }

                    $outward2 = $cqty+$case_qty;

            $outward3_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$start' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$end'")->where('tally_demo_details.cr_dr','DR')->whereIn('tally_demo.invoice_type',[8,9])->where('unit','CASE')->first();

            $outward3_pcs_data = DB::table('tally_demo_details')->join('tally_catalog_product','tally_catalog_product.id','=','tally_demo_details.product_id')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->select(DB::raw('sum(qty) as case_qty'))->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$start' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$end'")->where('tally_demo_details.cr_dr','DR')->whereIn('tally_demo.invoice_type',[8,9])->where('unit','PCS')->first();


                $cqty = !empty($outward3_data->case_qty)?$outward3_data->case_qty:0;
                $pcs = !empty($outward3_pcs_data->case_qty)?$outward3_pcs_data->case_qty:0;

                    if($pcs==0 || $unit->quantity_per_case==0 || $cqty==0)
                    {
                        $case_qty=0;
                        $outward3_pcs=$pcs;
                    }
                    else
                    {
                        $case_qty=ROUND(($pcs)/($unit->quantity_per_case));

                        $outward3_pcs=($pcs)%($unit->quantity_per_case);
                    }

                    $outward3 = $cqty+$case_qty;

                $outward=($outward2)-($outward1)+($outward3);
                if(empty($outward)){
                    $outward=0;
                }
                $outward_pcs=($outward2_pcs)-($outward1_pcs)+($outward3_pcs);
                if(empty($outward_pcs)){
                    $outward_pcs=0;
                }                  
        
                $closing=$opening_stock+$inward-$outward;
                $closing_pcs=($opening_stock_pcs)+($inward_pcs)-($outward_pcs);

            $rate = DB::table("tally_product_rate_list")->select('dealer_rate')->where('product_id',$product_id)->where('state_id',$cstate_id)->first();

            $rate_pcs = DB::table("tally_product_rate_list")->select('dealer_pcs_rate')->where('product_id',$product_id)->where('state_id',$cstate_id)->first();
            $gst_rate = DB::table('tally_demo_details')->select('gst')->join('tally_demo','tally_demo.id','=','tally_demo_details.td_id')->where('tally_demo.csa_id',$csa_id)->where('tally_demo_details.product_id',$product_id)->whereRaw("DATE_FORMAT(ch_date,'%Y-%m-%d')>='$start' AND DATE_FORMAT(ch_date,'%Y-%m-%d')<='$end'")->first();

                $out[$id]['opening'] = $opening_stock;
                $out[$id]['inward'] = $inward;
                $out[$id]['outward'] = $outward;
                $out[$id]['closing'] = $closing;
                $out[$id]['rate'] = !empty($rate->dealer_rate)?$rate->dealer_rate:0;
                $out[$id]['opening_pcs'] = $opening_stock_pcs;
                $out[$id]['inward_pcs'] = $inward_pcs;
                $out[$id]['outward_pcs'] = $outward_pcs;
                $out[$id]['closing_pcs'] = $closing_pcs;
                $out[$id]['rate_pcs'] = !empty($rate_pcs->dealer_pcs_rate)?$rate_pcs->dealer_pcs_rate:0;
                $out[$id]['gst_rate'] = !empty($gst_rate->gst)?$gst_rate->gst:0;

        }
    return view('reports.tally_ss_closing_report.ajax', [
        'records' => $closing_Query,
        'details' => $out   
    ]);
    }
    ##..............................Tally Report SS Closing Report Ends here ..........................##
    ##..............................Tally Report SS Stock Report ..........................##
    public function tallySsStockReport(Request $request)
    {
        $state_id = $request->area;
        $stockist = $request->stockist;
        $from_date = $request->from_date;

        $stock_Query = DB::table('tally_stock')->join('csa','csa.c_id','=','tally_stock.csa_id')->join('tally_catalog_product','tally_catalog_product.id','=','tally_stock.product_id')->join('location_3','location_3.id','=','csa.state_id')->select('tally_stock.id AS id','csa_id','csa.csa_name as csa_name','csa.csa_code','tally_stock.product_id as product_id','tally_catalog_product.name AS pname','from_date','to_date','opening','inward','outward','closing', 'server_date_time','location_3.name as l3_name')->whereRaw("DATE_FORMAT(from_date,'%Y-%m-%d')='$from_date'")->groupBy('id');
        if($state_id)
        {
            $stock_Query->whereIn('location_3.id',$state_id);
        }
        if(!empty($stockist))
        {
            $stock_Query->whereIn('csa.c_id',$stockist);
        }
        $stock_data = $stock_Query->get();
        return view('reports.tally_ss_stock_report.ajax', [
            'records' => $stock_data,
            // 'details' => $out   
        ]);
    }

    // ##...........................On change function ...................................#
        public function getStockist(Request $request)
        {
            if ($request->ajax() && !empty($request->id)) 
            {   
                $id = explode(',',$request->id);
                $data = DB::table('csa')->whereIn('state_id',$id)->pluck('csa_name','c_id');
                // dd($stockist_Query);
                $data['code'] = 200;
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
    // ##...........................On change function Ends Here..........................#

}


