
    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/toastr.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/colorbox.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/chosen.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}" />
<style>
table, th, td {
 
}
.textAlignRight{
    text-align: right;
}
td.rowspan{
  vertical-align: top;
  text-align: left;
}
table th#title {
    font-size: 15px;
    padding-bottom: 5px;
   
}
table.invoice-print tr, thead th, td 
{
    border: medium none !important;
    padding: 1px;
    vertical-align: top;
}
table.invoice-print {
   /* border: 1px solid #000;*/
    margin: 0 auto;
    width: 100%;
}

table.invoice-print td.header {
    border-top: 1px solid #000 !important;
    padding-left: 381px;
}

table tr.header1 > th , .header1{
    border-bottom: 1px solid #000  !important;
    border-top: 1px solid #000  !important;
    border-left: 1px solid #000  !important;
    padding: 2px 3px;
    text-align:center;
}
tbody td.border-left {
    border-left: 1px solid #000  !important;
    padding-left: 5px;
}
tbody td.border-left1 {
    border-left: 1px solid #000  !important;
    padding-left: 0px !important;
    
}
table tr.gsts > th, .gsts1 {
    border-bottom: 1px solid #000 !important;
    border-right: 1px solid #000;
    text-align: center;
}
table tr.gsts > th, .gsts3 {
    border-bottom: 1px solid #000 !important;
    border-right: 1px solid #000;
}
table tr.gsts > th, .gsts2 {
    border-bottom: 0px solid #000 !important;
    border-right: 1px solid #000;
    text-align: center;
}
tbody td.border-left2 {
    padding-left: 0px !important;
    padding-right: 0px !important;
}

table th {
    font-weight:Normal;
}
td.display-label {
    font-weight:130%;
    text-align: right;
}
tr.display-border td
{
    border-bottom: 1px solid #000 !important;
    border-top: 1px solid #000 !important;
}
.border-right{
        border-right: 1px solid !important;
}
.border-left{
        border-left: 1px solid !important;
}
tr.invoice-title-row td{
    border-left: solid 1px !important;
    border-right: solid 1px !important;
    border-top: solid 1px !important;
}
table.no-border td {
    border: none !important;
}
tr.invoice-title-row td.no-border{
    border: none !important;
}
.border-top-none{
        border-top: medium none !important;
}
.border-left-none{
        border-left: medium none !important;
}
h1, h2, p {
    margin: 0 auto;
}
h1 {
    color: #000000;
    font-size: 20px;
    line-height: 20px;
    margin: 0;
    padding: 0;
    text-align: center;
}

table tr td.borderTop,th.border-top 
{
    border-top: 1px solid #000 !important;
}
table tr th.content-data,td.content-data
{
    border-bottom: 1px solid #000 !important;
    text-align: left;
}
table tr th.textAlignRight,td.textAlignRight
{
     padding-right: 5px;
     text-align: right;
}

.note {
    text-align: justify;
}
.text_bold {
    font-weight:bold;
}
table.invoice-print tbody td:nth-child(3) {
    white-space: nowrap;
}
table.invoice-print tbody td:nth-child(4) {
    white-space: nowrap;
}
table.invoice-print tbody td:nth-child(13) {
    white-space: nowrap;
}
.title-left  {
    white-space: nowrap;
    display: inline-block;
    width: 85px;
}
.title-right  {
    display: inline-block;
}
.title-middle  {
    display: inline-block;
    width: 10px;
}
.nowrap {
    white-space: nowrap;
}
table.extra-information td
{
    text-align:center;
}
table {
    border-collapse: collapse;
}

/Custom GST Summary CSS Start/
table tr.gsts > th, .gsts {
    border-bottom: 1px solid #000 !important;
    border-right: 1px solid #000;
    padding: 2px 3px;
    text-align: center;
}
.rebook .gsts,.rebook .gsts2 {
    text-align: left;
}
table tr.border-none > th, .border-none {
    border-bottom:0 !important;
    border-left:0 !important;
    border-top:0 !important;
    border-right: none !important;
}
table.gstr td{
    border-right: 1px solid #000 !important;
}
table.gstr td.border-none,.border-none{
    border-right: 0px solid #000 !important;
}
table.gstr{
    border-bottom: 1px solid #000 !important;
    border-left: 1px solid #000 !important;
    border-top: 1px solid #000 !important;
    border-right: 1px solid #000;
}
tr.sumtotal{
    border-top: 1px solid #000 !important;  
}
.flash{
display:none;
}
table.no-border td.border-left-right {
    border-right: solid 1px !important;
    border-bottom: solid 1px !important;
}
table.no-border td.border-bottom{
    border-bottom: solid 1px !important;
}
</style>

<div class="main-content" style="   ">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #90d781; color: black;">
            
        </div>

        <div class="page-content"  style=" font-family: 'Times New Roman', Times, serif; ">
            <br>
            <div class="row container-fluid" >
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-xs-12" >
                            <table class=" " cellspacing="0" cellpadding="0" style="width:100%;">
                                <colgroup>
                                    <col width="2%">
                                    <col width="14%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="6%">
                                    <col width="6%">
                                    <col width="6%">
                                    <col width="6%">
                                    <col width="5%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th colspan="18" style="background-color: white; color: black; text-align: center; font-size: 40px; font-weight:100px; ">
                                            <b style="background-color: ; color: black; font-family: 'Times New Roman', Times, serif;">TAX INVOICE</b>
                                        </th>
                                    </tr>

                                    <tr class="invoice-title-row">
                                        <td style="background-color: white; text-align: left;" colspan="5">
                                            <div><b>Shree Baidyanath Ayurved Bhawan Pvt. Ltd.</b></div>
                                            <div>172, GUSAIN PURA, JHANSI <br/><br/></div>
                                            <div>CITY: JHANSI, DISTRICT: JHANSI</div>
                                            <div>State: UTTAR PRADESH (Code-09) PIN: 284002</div>
                                            <div>GSTIN:09AAECS5408D1ZI</div>
                                            <div>PAN Number: AAECS5408D</div>
                                            <div>Phone No.:(0510)-2333871,72,73</div>
                                            <div>Email: contact@baidyanath.co.in</div>
                                            <div>Ayurvedic Drug Mfg. License No. A-1800/89</div>
                                            <div>REGISTERED OFFICE : 1, GUPTA LANE,KOLKATA 700 006<br/>CIN:-U24233WB1947PTC015374</div>
                                        </td>
                                        <td style="background-color: white; text-align: left;"  colspan="6">
                                            <table style="width:100%;" class="no-border">
                                                <tbody>
                                                    <tr style="text-align: left;">
                                                        <td style="text-align: left;" class="border-left-right">Invoice No.</td>
                                                        @php
                                                            $vrno_details = explode('Y-',$invoice_details->VRNO);
                                                            $date = date('m-d');
                                                            if($date >= '04-01')
                                                            {
                                                                $date_cur = date('y')+1;
                                                            }
                                                            else
                                                            {
                                                                $date_cur = date('y');
                                                            }
                                                        @endphp
                                                        <td style="text-align: left;" class="border-left-right">{{$vrno_details[0].'-'.$date_cur.'Y-'.$vrno_details[1]}}</td>
                                                        <td style="text-align: left;" class="border-left-right">G.R. No.</td>
                                                        <td style="text-align: left;" class="border-bottom">{{$invoice_details->LRNO}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: left;" class="border-left-right">Invoice Date</td>
                                                        <td style="text-align: left;" class="border-left-right">{{$invoice_details->VRDATE}}</td>
                                                        <td style="text-align: left;" class="border-left-right">G.R. Date</td>
                                                        <td style="text-align: left;" class="border-bottom">{{$invoice_details->LRDATE}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: left;" class="border-left-right">Order No.</td>
                                                        <td style="text-align: left;" class="border-left-right">{{$invoice_details->ORDER_VRNO.'-'.$invoice_details->DO_VRNO}}</td>
                                                        <td style="text-align: left;" class="border-left-right">Gate Pass No.</td>
                                                        <td style="text-align: left;" class="border-bottom">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: left;" class="border-left-right">Order Date</td>
                                                        <td style="text-align: left;" class="border-left-right">{{$invoice_details->VRDATE}}</td>
                                                        <td style="text-align: left;" class="border-left-right">G. Pass Date</td>
                                                        <td style="text-align: left;" class="border-bottom">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: left;" class="border-left-right">Marka No.</td>
                                                        <td style="text-align: left;" class="border-left-right">{{$invoice_details->IRFIELD4}}</td>
                                                        <td style="text-align: left;" class="border-left-right">Total Carton(s)</td>
                                                        <td style="text-align: left;" class="border-bottom">{{$invoice_details->IRFIELD3}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: left;" class="border-left-right">Transport</td>
                                                        <td colspan="3" style="text-align: left;" class="border-bottom">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: left;" class="border-left-right">Vehicle No.</td>
                                                        <td style="text-align: left;" colspan="3" class="border-bottom">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: left;" class="border-left-right">Remark</td>
                                                        <td style="text-align: left;" colspan="3">&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td style="background-color: white; text-align: left;"  colspan="7">
                                            <div style="border-bottom: solid 1px;">
                                                <b>Details of Receiver (Bill To) </b>
                                            </div>
                                            {{$dealer_details->STNO}}<br>
                                            {{$dealer_details->ACC_NAME}} (Code:{{$dealer_details->ACC_CODE}})<br/>
                                            {{$dealer_details->ADD1}},{{$dealer_details->ADD2}} CITY: {{$dealer_details->CITY_CODE}}  DISTRICT: {{$dealer_details->DISTRICT_CODE}} STATE: {{$dealer_details->STATE_CODE}} 
                                            <div style="border-bottom: solid 1px;border-top: solid 1px;">
                                                <b>Details of Consignee (Shipped To)</b>
                                            </div>
                                            {{$dealer_details->STNO}}<br>
                                            {{$dealer_details->ACC_NAME}} (Code:{{$dealer_details->ACC_CODE}})<br/>
                                            {{$dealer_details->ADD1}},{{$dealer_details->ADD2}} CITY: {{$dealer_details->CITY_CODE}}  DISTRICT: {{$dealer_details->DISTRICT_CODE}} STATE: {{$dealer_details->STATE_CODE}}  </td>
                                    </tr>

                                </thead>

                            </table>

                            <table class="invoice-print " cellspacing="0" cellpadding="0">
                                <colgroup>
                                    <col width="2%">
                                    <col width="12%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="7%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="7%">
                                    <col width="7%">
                                    <col width="5%">
                                </colgroup>
                                <thead>
                                <tr class="header1">        
                                         <th rowspan="2">Slno</th>
                                         <th rowspan="2">Item Details</th>
                                         <th rowspan="2">SIZE</th>
                                         <th rowspan="2">HSN CODE</th>
                                         <th rowspan="2" class="textAlignRight">M.R.P</th>
                                         <th rowspan="2" class="textAlignRight">RATE</th>
                                         <th colspan="2" class="textAlignRight">QTY IN PIECE(S)</th>
                                         <th rowspan="2" class="textAlignRight nowrap">VALUE</th>
                                         <th rowspan="2" class="textAlignRight nowrap">Trade <br>Disc.</th>
                                         <th rowspan="2" class="textAlignRight nowrap">Scheme <br>Disc.</th>
                                         <th rowspan="2" class="textAlignRight nowrap">Special <br>Disc.</th>
                                         <th rowspan="2" class="textAlignRight nowrap">Cash <br>Disc.</th>
                                         <th rowspan="2" class="textAlignRight nowrap">Addl Trade <br>Disc.</th>
                                         <th rowspan="2" class="textAlignRight nowrap">TAXABLE <br>AMT</th>
                                         <th rowspan="2" class="textAlignRight nowrap">CGST</th>
                                         <th rowspan="2" class="textAlignRight nowrap">SGST</th>
                                         <th rowspan="2" class="textAlignRight nowrap border-right">IGST</th>
                                     </tr>
                                     <tr class="header1">       
                                         <th class="textAlignRight">SOLD</th>
                                         <th class="textAlignRight">Disc.</th>
                                     </tr>
                                </thead>
                                <tbody>
                                <?php 
                                    $array_value = array();
                                    $array_value = array();
                                    $a1trade_arr = array();
                                    $scheme_arr = array();
                                    $special_arr = array();
                                    $cash_arr = array();
                                    $trade_arr = array();
                                    $taxable_arr = array();
                                    $cgst_arr = array();
                                    $sgst_arr = array();
                                    $igst_arr = array();
                                    $grand_total_value = 0;
                                ?>
                                @if(!empty($order_details_body))
                                    @foreach($order_details_body as $key=>$value)
                                        <tr style="height:4.5mm;">
                                            <td class="border-left gsts3">{{$key+1}}</td>
                                            @php 
                                                $final_item = explode(',',$value->ITEM_NAME);
                                            @endphp
                                            <td class="border-left gsts3" style="text-align: left;">{{$value->ITEM_CODE}} : {{$final_item[0]}}<br> Batch No.: {{$value->BATCHNO}}, Mfg Dt.:<span class="nowrap">{{$value->MFG_DATE}}</span></td>
                                            <td class="textAlignRight border-left gsts1">{{$value->NGPSIZE.' '.$value->SIZE_UM}}</td>
                                            <td class="textAlignRight border-left gsts1">{{$value->HSN_CODE}}</td>
                                            <td class="textAlignRight border-left gsts1">{{$value->MRP}}</td>
                                            <td class="textAlignRight border-left gsts1">{{$value->RATE}}</td>
                                            <?php 
                                                $qty_free_cus = !empty($value->QTYFREE)?$value->QTYFREE:'0';
                                                $billed_qty = $value->QTYISSUED-$qty_free_cus;
                                                $total_rs = $billed_qty*$value->RATE;
                                                $array_value[] = $total_rs;
                                                $scheme_arr[] = '0.00';
                                                $cgst_arr[] = $value->CGST_AMOUNT;
                                                $sgst_arr[] = $value->SGST_AMOUNT;
                                                $igst_arr[] = $value->IGST_AMOUNT;
                                                $a1trade_arr[] = $value->AFIELD6;
                                                $cash_arr[] = $value->AFIELD5;
                                                $special_arr[] = $value->AFIELD4;
                                                $taxable_arr[] = $value->VALISSUED;
                                                $trade_arr[] = $value->AFIELD2;

                                                
                                                // $grand_total = 
                                            ?>
                                            <td class="textAlignRight border-left gsts1">{{$billed_qty}}</td>
                                            <td class="textAlignRight border-left gsts1">{{$value->QTYFREE}}</td>
                                            <td class="textAlignRight border-left gsts1">{{$total_rs}}</td>
                                            <td class="textAlignRight border-left gsts1">{{$value->AFIELD2.'  ('.$value->TI_RATE.'%)'}}</td>
                                            <td class="textAlignRight border-left gsts1">{{$value->AFIELD3}}</td>
                                            <td class="textAlignRight border-left gsts1">{{$value->AFIELD4}}</td>
                                            <td class="textAlignRight border-left gsts1">{{$value->AFIELD5}}</td>
                                            <td class="textAlignRight border-left gsts1">{{$value->AFIELD6}}</td>
                                            <td class="textAlignRight border-left gsts1">{{$value->VALISSUED}}</td>
                                            <td class="textAlignRight border-left gsts1">{{$value->CGST_AMOUNT.' ('.$value->CGST_PERCENTAGE.'%)'}} </td>
                                            <td class="textAlignRight border-left gsts1">{{$value->SGST_AMOUNT.' ('.$value->SGST_PERCENTAGE.'%)'}} </td>
                                            <td class="textAlignRight border-left border-right gsts1">{{!empty($value->IGST_AMOUNT)?$value->IGST_AMOUNT.' ('.$value->GST_PERCENTAGE.'%)':'0'}}</td>
                                        </tr><!--cahnges-->
                                    @endforeach
                                    <tr class="display-border">
                                        <?php 
                                            $array_value_bottom = array_sum($array_value);     
                                            $trade_arr_bottom = array_sum($trade_arr);     
                                            $scheme_arr_bottom = array_sum($scheme_arr);     
                                            $special_arr_bottom = array_sum($special_arr);     
                                            $cash_arr_bottom = array_sum($cash_arr);     
                                            $a1trade_arr_bottom = array_sum($a1trade_arr);     
                                            $taxable_arr_bottom = array_sum($taxable_arr);     
                                            $cgst_arr_bottom = array_sum($cgst_arr);     
                                            $sgst_arr_bottom = array_sum($sgst_arr);     
                                            $igst_arr_bottom = array_sum($igst_arr);   
                                            $grand_total_value = $array_value_bottom+$trade_arr_bottom+$scheme_arr_bottom+$special_arr_bottom+$cash_arr_bottom+$a1trade_arr_bottom+$cgst_arr_bottom+$sgst_arr_bottom+$igst_arr_bottom;  
                                            $out['gst_perc'] = $order_details_body[0]->GST_PERCENTAGE;
                                            $out['taxable_amt'] = $taxable_arr_bottom;
                                            $out['cgst_amt'] = $cgst_arr_bottom;
                                            $out['sgst_amt'] = $sgst_arr_bottom;
                                            $out['igst_amt'] = $igst_arr_bottom;
                                            $out['hsn_code'] = $order_details_body[0]->HSN_CODE;
                                            $final_out[] = $out;
                                        ?>
                                        <td class="display-label border-left" colspan ="8" style="text-align:center;"><b>Total</b></td>
                                        <td colspan ="1" class="border-left border-right textAlignRight">{{array_sum($array_value)}}</td>
                                        <td colspan ="1" class="border-right textAlignRight">{{array_sum($trade_arr)}}</td>
                                        <td colspan ="1" class="border-right textAlignRight">{{array_sum($scheme_arr)}}</td>
                                        <td colspan ="1" class="border-right textAlignRight">{{array_sum($special_arr)}}</td>
                                        <td colspan ="1" class="border-right textAlignRight">{{array_sum($cash_arr)}}</td>
                                        <td colspan ="1" class="border-right textAlignRight">{{array_sum($a1trade_arr)}}</td>
                                        <td colspan ="1" class="border-right textAlignRight">{{array_sum($taxable_arr)}}</td>
                                        <td colspan ="1" class="border-right textAlignRight">{{array_sum($cgst_arr)}}</td>
                                        <td colspan ="1" class="border-right textAlignRight">{{array_sum($sgst_arr)}}</td>
                                        <td colspan ="1" class="border-right textAlignRight">{{array_sum($igst_arr)}}</td>
               
                                    </tr>
                                    <tr >
                                        <td colspan="2" class="text_bold pay border-right border-left border-top gsts1" style="size:25px;">Net Payable Amount</td>
                                        <td colspan="10" class="text_bold pay border-right border-left border-top gsts1"><b id="words">Seven Thousand Eight Hundred Seventy-nine Only</b></td>
                                        <td colspan="3" class="text_bold pay border-right border-left border-top gsts1"><b>Grand Total</b></td>
                                        <td colspan="3" class="text_bold pay border-right border-left border-top gsts1 border-bottom" id="number">{{round($grand_total_value)}}</td>
                                    </tr>
                                
                                
                                    
                                    
                                <!--changes-->
                                    
                                    <!--GST Summary Start--->
                                    <tr>
                                        <td colspan="10" class="border-left1 border-right border-top gsts1">
                                            <table style="width:100%;" >
                                                <tr class="gsts1">
                                                    <th colspan="7" class="gsts1"><b>Tax Details</b></th>
                                                </tr>
                                                <tr class="gsts1">       
                                                     <th class="gsts1">GST Slab</th>
                                                     <th class="gsts1">HSN Code</th>           
                                                     <th class="gsts1">Taxable Amount</th>
                                                     <th class="gsts1">GST Amount</th>
                                                     <th class="gsts1">CGST Amount</th>
                                                     <th class="gsts1">SGST Amount</th>
                                                     <th class=" gsts1">IGST Amount</th>                            
                                                </tr>
                                                @foreach($final_out as $gst_key => $gst_value)
                                                    @if($gst_key == 0)
                                                    <tr class="border-right gsts1">
                                                        <td class="border-right textAlignRight gst1">{{$gst_value['gst_perc']}}</td>
                                                        <td class="border-right textAlignRight gst1">{{$gst_value['hsn_code']}}</td>
                                                        <td class="border-right textAlignRight gst1">{{$gst_value['taxable_amt']}}</td>
                                                        <td class="border-right textAlignRight gst1">{{$gst_value['cgst_amt']+$gst_value['sgst_amt']+$gst_value['igst_amt']}}</td>
                                     
                                                        <td class="border-right textAlignRight gst1">{{$gst_value['cgst_amt']}}</td>
                                                        <td class="border-right textAlignRight gst1">{{$gst_value['sgst_amt']}}</td>
                                                        <td class="border-right textAlignRight gst1">{{$gst_value['igst_amt']}}</td>
                                                    </tr>
                                                    <?php

                                                        $taxable_amt_arr[] = !empty($gst_value['taxable_amt'])?$gst_value['taxable_amt']:'0';
                                                        $cgst_amt_arr[] = !empty($gst_value['cgst_amt'])?$gst_value['cgst_amt']:'0';
                                                        $sgst_amt_arr[] = !empty($gst_value['sgst_amt'])?$gst_value['sgst_amt']:'0';
                                                        $igst_amt_arr[] = !empty($gst_value['igst_amt'])?$gst_value['igst_amt']:'0';
                                                        $gst_amount_array[] = !empty($gst_value['cgst_amt'])?$gst_value['cgst_amt']+$gst_value['sgst_amt']+$gst_value['igst_amt']:'0';
                                                    ?>
                                                    @endif
                                                @endforeach
                                                <tr class="border-right sumtotal">
                                                    <td class="borderTop textAlignCenter border-none"></td>
                                                    <td class="border-right borderTop textAlignCenter"><b>Total</b></td>
                                                    <td class="border-right borderTop textAlignRight"><b>{{array_sum($taxable_amt_arr)}}</b></td>
                                                    <td class="border-right borderTop textAlignRight"><b>{{array_sum($gst_amount_array)}}</b></td>
                                                    <td class="border-right borderTop textAlignRight"><b>{{array_sum($cgst_amt_arr)}}</b></td>
                                                    <td class="border-right borderTop textAlignRight"><b>{{array_sum($sgst_amt_arr)}}</b></td>
                                                    <td class="border-right borderTop textAlignRight"><b>{{array_sum($igst_amt_arr)}}</b></td>
                                                   
                                                </tr>   
                                            </table>
                                        </td>
                                        <td colspan="8" class="border-right border-top gsts1">
                                            <b>For Shree Baidyanath Ayurved Bhawan Pvt. Ltd.</b>
                                        </td>   
                                    </tr>
                                @endif
                                    @if(!empty($invoice_rebooked_details))
                                    <tr>
                                        <td colspan="18" class="border-left1 border-right border-top gsts1">
                                            <table style="width:100%;" class="rebook">
                                                <thead>
                                                    <tr>
                                                        <td colspan="5" class="border-top gsts"><h4>Details of un-executed Items Rebooked/Cancelled</h4>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <thead>
                                                    <tr>
                                                        <th class="border-right  border-top gsts1" style="text-align: left;"><h4>Item Code</h4></th>
                                                        <th class="border-right border-top gsts1" style="text-align: left;"><h4>Item Name</h4></th>
                                                        <th class="border-right border-top gsts1" style="text-align: left;"><h4>Quantity</h4></th>
                                                        <th class="border-right border-top gsts1" style="text-align: left;"><h4>Status</h4></th>
                                                        <th class="border-top gsts1" style="text-align: left;"><h4>Re-Book VRNO</h4></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($invoice_rebooked_details as $r_key => $r_value)
                                                        <tr class="gsts1">
                                                            <td class="border-right border-top gsts1" style="text-align: left;">{{!empty($r_value->ITEM_CODE)?$r_value->ITEM_CODE:''}}</td>
                                                            <td class="border-right border-top gsts1" style="text-align: left;">{{!empty($r_value->ITEM_NAME)?$r_value->ITEM_NAME:''}}</td>
                                                            <td class="border-right border-top gsts1" style="text-align: left;">{{!empty($r_value->REBOOKED_QTY)?$r_value->REBOOKED_QTY:''}}</td>
                                                            <td class="border-right border-top gsts1" style="text-align: left;">RE-BOOKED</td>
                                                            <td class="border-top gsts1" style="text-align: left;">{{$r_value->VRNO}}</td>
                                                        </tr>
                                                    @endforeach
                                                   

                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    
                                    @endif
                                    <tr class="footer">
                                        <td colspan="18" align="left">
                                            <div style="text-align: left;">
                                                <b>Terms & conditions</b>
                                            </div>
                                            <div style="text-align: left;">
                                                1. Subject to exclusive jurisdiction of JHANSI (U.P.) courts and our business terms agreed and accepted by you.
                                            </div>
                                            <div style="text-align: left;">
                                                2. Interest will be charged @18% PA from the date of invoice, If payment received by us after due date.
                                            </div>
                                            <div style="text-align: left;">
                                                3. The risk & rewards associated with goods shall be deemed to be transferred to the customer with the dispatch of goods.
                                            </div>
                                            <div style="text-align: left;">
                                                4. You hereby accept GST charged & deposited by company herein. It is your liability if variation in GST charged/deposited or excess GST input credit claimed by you.
                                            </div>
                                            <div style="text-align: left;">
                                                5. By accepting this invoice, you also acknowledge & accept our standard business terms & conditions.
                                            </div>
                                        </td>
                                    </tr>
                                        <!--GST Summary End--->
                                <!--gstdetails-->
                                           
                                          <!--   </tfoot> -->
                                          
                                </tbody> <!-- parent tbody ends here  --> 
                                         
                            </table><!-- parent table ends here  --> 

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>







<script type="text/javascript">
    var a = ['','One ','Two ','Three ','Four ', 'Five ','Six ','Seven ','Eight ','Nine ','Ten ','Eleven ','Twelve ','Thirteen ','Fourteen ','Fifteen ','Sixteen ','Seventeen ','Eighteen ','Nineteen '];
    var b = ['', '', 'Twenty','Thirty','Forty','Fifty', 'Sixty','Seventy','Eighty','Ninety'];

function inWords (num) {
    if ((num = num.toString()).length > 9) return 'overflow';
    n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
    if (!n) return; var str = '';
    str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + ' ' + a[n[1][1]]) + 'Crore ' : '';
    str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + ' ' + a[n[2][1]]) + 'Lakh ' : '';
    str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + ' ' + a[n[3][1]]) + 'Thousand ' : '';
    str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + ' ' + a[n[4][1]]) + 'Hundred ' : '';
    str += (n[5] != 0) ? ((str != '') ? 'and ' : '') + (a[Number(n[5])] || b[n[5][0]] + ' ' + a[n[5][1]]) + 'only ' : '';
    return str;
}

$(document).ready(function() { /* code here */ 
    var old_v  = document.getElementById('number').innerHTML;

    document.getElementById('words').innerHTML = inWords(document.getElementById('number').innerHTML);

    document.getElementById('number').innerHTML = '';
    document.getElementById('number').innerHTML = old_v+'.00';

});

</script>
