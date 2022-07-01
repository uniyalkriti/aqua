<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php
$dealer_id = $_SESSION[SESS.'data']['dealer_id'];
$company_id = $_SESSION[SESS.'data']['company_id'];
$objcomp = new company();
$company_data = $objcomp->get_company_list($filter="company.id='$company_id'",  $records = '', $orderby='');

$company_data = $company_data[$company_id];

$myobj1 = new dealer();
$dealer_data = $myobj1->get_dealer_list($filter = "id = '$dealer_id'", $records, $orderby);
$dealer_data = $dealer_data[$dealer_id];
//pre($dealer_data);
//pre($dealer_data);
$companyname = $dealer_data['name'];
$company_adr = $dealer_data['address'];
$phone = " +91 $dealer_data[other_numbers], E-MAIL : $dealer_data[email]";

//Fetch the record details
$looper = $myobj->print_looper_challan($_GET['id']);
//pre($looper);
?>
<style type="text/css" media="all">
div#certificate_container{font-family:"Times New Roman", Times, serif; font-size:12px; color:#000;}
table.certificate_detail td.col1{ text-align:left;}
table.certificate_detail td.col2{ text-align:center;}
table.certificate_detail td.col3{ text-align:right;}

div.mytitle{ font-size:18px; margin-bottom:5px; text-align:center; font-weight:bold; margin-top:10px;}

div.calib_result table{border-collapse:collapse; border:1px solid;}
div.calib_result table tr td{ padding-left:5px;}
</style>
<!--<script src="<?php echo BASE_URL_A;?>widgets/barcode/jquery-barcode-2.0.1.js"></script>-->
<script type="text/xml">
<!--
<oa:widgets>
  <oa:widget wid="2489022" binding="#bcContent" />
</oa:widgets>
-->
</script>
<table width="100%">
  <tr>
    <td>
      <div class="subhead1"><!-- this portion indicate the print options -->
        <a href="javascript:printing('searchlistdiv');" title="take a printout" style="margin:0 10px;"><img src="./icons/print.png" /></a>
        Print Invoice
      </div>
    </td>
  </tr>	
</table>
<div id="searchlistdiv" style="page-break-inside: avoid;">
<?php foreach($looper as $key=>$value){ //this loop will help in the printing of the multiple bills?>
<div id="certificate_container" style="padding:0px; font-size:62.5%; font-family:'Times New Roman', Times, serif; page-break-after:auto; margin:0 auto; width:595pt;">
  
  <div id="pritn_what">
    <div class="mytitle" style="font-size:14pt;text-align:center; font-weight:bold; margin-top:0;">
      <span style="text-decoration:underlinel;"><?php if($value['adr']['tin_no']!='') echo"TAX "; else echo "RETAIL "  ?>INVOICE</span><br />
<!--      <span style="font-size:9pt; font-weight:normal; letter-spacing:-0.5pt;">Invoice for removal of Excisable Goods from a factory or warehouse on payment of Duty (Rule-11)</span>      -->
    </div> 
  </div>
  
  <div id="certificate_header">
    <table width="100%" class="header_table" style="margin-top:0pt; border:1pt solid #000; border-collapse:collapse; font-family:'Times New Roman', Times, serif; font-size:10pt;" border="1">
      <tr>
        <td valign="top" colspan="5" style="width:50%;">
          <!-- table to tin no detail starts here -->
          <table border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
            <tr>
                <td colspan="1"><strong><?php echo $companyname; ?></strong></td>
            </tr>
            <tr>
                <td colspan="2">(AUTH. DIST. OF <?php echo $company_data['name']; ?>)</td>
            </tr>
            <tr>
                <td colspan="2" style="">
                   <?php echo $company_adr; ?> <br>
                    PH. NO.-: <?php echo $phone; ?>
                </td>
            </tr>
            <tr>
              <td>TIN. No.: <?php echo $dealer_data['tin_no']; ?></td>
              <td>PH NO.:- <?php echo $dealer_data['landline']; ?></td>
            </tr>
          </table>
          <!-- table to tin no detail ends here -->
        </td>
        <td valign="top" colspan="5">
          <!-- table to Tarrif heading detail starts here -->
          <table border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
            <tr>
              <td><strong>TO</strong> </td>
	      <td align="right"><strong>Authorised Signatory</strong> </td>
              <td valign="top"></td>
            </tr>
            <tr>
              <td colspan="1"><strong>INV. No.: </strong><?php echo $value['ch_no']; ?> Date : <?php echo $value['ch_date']; ?> </td>
              <td></td>
            </tr>
            <tr>
                <td colspan="2"><strong> Retailer: </strong><?php echo $value['retailer_name']; ?><br/><strong>Address: </strong>
                <?php echo $value['adr']['address']; ?>
                </td>
            </tr>
             <tr>
                 <td colspan="2"><strong>TIN.-:</strong> <?php  echo $value['adr']['tin_no']; ?></td>	
            </tr>
          </table>
          <!-- table to Tarrif heading detail ends here -->
        </td>  
      </tr>
     
      <tr style="font-weight:bold;">
        <td style="width:3%">S No.</td>
        <td style="width:40%">Item Name</td>
        <td style="width:20%">Complain Type</td>
        <td style="width:5%" align="right">Qty</td>
        <td style="width:8%">MRP</td>
        <td style="width:8%">Rate</td>
        <td style="width:5%" align="right">Vat (%).</td>
        <td style="width:5%" align="right">Vat Amt.</td>
        <td style="width:5%" align="right">Amt.</td>
        <td style="width:15%" align="right">Act. Amt.</td>
      </tr>
      <?php 
	  $inc =1; 
          $i=1;
	  $amount = array();
	  $mybarcode = '';
          $grandtotal = 0;
          $surcharge=  myrowval('catalog_product_rate_list', 'surcharge', 'stateid='.$_SESSION[SESS.'data']['state_id']);
         // print_r($surcharge);
	  foreach($value['challan_item'] as $key1=>$value1){ 
             // pre($value['challan_item']);
	  	if(!empty($value1['lineno'])) $mybarcode .= "\t".$value1['lineno']."\t".$value1['qty'];
                
               // if(empty($value1['taxId']))
                //    $taxvalue = 0; 
                //else 
                //  $taxvalue = $value1['taxId'];
                
               // $netAmt = my2digit($value1['ch_qty']*$value1['product_rate'] + $value1['ch_qty']*$value1['product_rate'] * $taxvalue/ 100 );
             if($value1['cd_type']==1){
        $cd = (($value1['qty'] * $value1['product_rate'])*$value1['cd'])/100;        
        }
elseif ($value1['cd_type']==2) {
 $cd = ($value1['cd']);        
}    
            $taxable=$value1['product_rate']*$value1['qty'];
             $amt = $taxable;
             if($value1['tax']==0){$vat_amt = 0; }
             else{
             $vat_amt = (($amt)*$value1['tax'])/100;}
            //  h1($vat_amt);
             //$vat_amt = (($amt-$cd)*$value1['tax'])/100;   
             $vat_amt1 = $vat_amt - ($vat_amt*($surcharge/100));
             $surcharge_amt= $vat_amt - $vat_amt1;  
            // h1( $surcharge_amt);
            //h1($vat_amt1);
            // pre($value1);
	  ?>
       <tr>
        <td style="border-bottom:none;border-top:none;height:36px;"> <?php echo $i;?></td>
        <td style="border-bottom:none;border-top:none;height:10px;"> <?php echo $value1['name'];?></td>
        <td style="border-bottom:none;border-top:none;height:10px;"> <?php echo $value['complaint_name'];?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo $value1['qty'];?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['mrp']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['product_rate']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($vat_amt1);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($vat_amt1);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['product_rate']*$value1['qty']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['actual_amount']);?></td>
      </tr>
	  <?php
          $srchrg = (trim($value1['tax'])==0)?'0':$surcharge;
          $summary_vat[$value1['tax']."+".$srchrg]['amount']+=my2digit($taxable);
          $summary_vat[$value1['tax']."+".$srchrg]['tax']+=my2digit($vat_amt);
          $summary_vat[$value1['tax']."+".$srchrg]['surcharge']+=my2digit($surcharge_amt);
          $summary_vat[$value1['tax']."+".$srchrg]['cd_amt']+=my2digit($value1['cd_amt']);
        //  $summary_vat[$value1['tax']]['total']+=$summary_vat[$value1['tax']]['amount']+$summary_vat[$value1['tax']]['tax']+$summary_vat[$value1['tax']]['surcharge'];
          $dis_amt += $value1['dis_amt'];
          $cd_amt+=$value1['cd_amt'];
          $taxable_amt+=$taxable;
          $ttl_surcharge_amt+= $surcharge_amt;
          $ttl_vat_amt+=$vat_amt;
          $total_qty+=$value1['qty'];
          $grandtotal += $net_amt-$value1['cd_amt'];
          //$grandvat += $vat_amt ;
         // h1($grandtotal);
          $inc++; 
          $i++;

             }// foreach($value['po_item'] as $key1=>$value1){ ends
       for($k=$inc;$k<8;$k++){
   ?> 
      <?php } ?>
      <tr style="font-size:11pt;">
        
        <td colspan="3"><strong>GRAND TOTAL</strong></td>
        <td align="right"><strong><?php echo $total_qty; ?></strong></td>
        <td colspan="4" align="right"><strong><?php echo my2digit($ttl_vat_amt); ?></strong></td>
        <td colspan="1" align="right"><strong><?php echo my2digit($taxable_amt); ?></strong></td>
        <td colspan="1" align="right"><strong><?php echo price_to_words(round($grandtotal)); ?></strong></td>
      </tr> 
      <tr style="font-size:11pt;">
         <td colspan="10">Amount of Invoice (in Words) Rupees. <b><?php echo strtoupper(price_to_words($taxable_amt,2));?></b><!--ONE LAC SIX THOUSAND FOUR HUNDRED TWENTY FIVE ONLY.--></td>
      </tr>

        <tr>
           <td colspan="5" style="border-right:none;">
               <table border="0">
                   <tr>
                       <td colspan="5"><b>Tax Summary</b></td>
                   </tr>
                   <tr>
                       <td><b>Percent(%)</b></td>
                       <td><b>Amount</b></td>
                       <td><b>Tax</b></td>
                       <td><b>Surcharge</b></td>
                       <td><b>Total</b></td>
                   </tr>
               <?php
             //  pre($summary_vat);
               foreach($summary_vat as $key => $val){
                   //pre($summary_vat);
                echo "<tr>";
                echo "<td>".$key."%</td>";
                echo "<td>".$val['amount']."</td>";
                echo "<td>".$val['tax']."</td>";
                $ttl1[$key] =  $val['amount']+$val['tax']+$val['surcharge']-$val['cd_amt'];
                echo "<td>".$ttl1[$key]."</td>";
                echo "</tr>";
                
                $amt1+=$val['amount'];
                $tax1+=$val['tax'];
                $surcharge1+=$val['surcharge'];
                $total1+=$ttl1[$key];
           }
                echo "<tr>";
                echo "<td>Total</td>";
                echo "<td>".$amt1."</td>";
                echo "<td>".$tax1."</td>";
                echo "<td>".$total1."</td>";
                echo "</tr>";
           ?>
               </table>
           </td>
           <td colspan="5" align="right" style="border-left:none;">For <?php echo $dealer_data['name']; ?><br /><br /><br />Authorised Signatory</td>
        </tr>
    </table>
    <table width="100%" border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
      <tr>
        <td valign="top" style="border:none;width:70%"><span style="font-size:9pt;">E & O E Please Check the stock at delivery All disputes are subject to Jursdiction only. It is computer generated invoice does not need signature.</span></td>
      </tr>
    </table>  
  </div>
</div><!-- #certificate_conatiner ends -->
<div style="page-break-after:always;page-break-inside:avoid;width: 90%;"></div>
<?php }// foreach($looper as $key=>$value){ ends?>
</div>
<input type="hidden" id="bb" name="bb" value="<?php echo $mybarcode;?>">
<?php if(true || $showbarcode) { // deciding whether to show the barcode or not starts here?>
<script type="text/javascript">
var b = document.getElementById('bb').value;
//alert(b);
			$("#bcContent").barcode(b, "datamatrix",{
		    	barWidth: 1,
    			barHeight: 30,
    			moduleSize: 2,
				showHRI: true,
				addQuietZone: true,
				marginHRI: 5,
				bgColor: "#FFFFFF",
				color: "#000000",
				fontSize: 0,
				output: "css",
				posX: 0,
				posY: 0
			});    
		
// EndOAWidget_Instance_2489022
</script>
<?php } //deciding whether to show the barcode or not ends here?>
