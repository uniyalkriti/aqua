<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php
//Reading the company statistics
/*$company = new company();
$cmpId = 1;
$companystat = $company->get_company_list("cmpId=$cmpId");
$companystat = $companystat[$cmpId];
*/


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
$phone = "Tel. : +91 $dealer_data[other_numbers], $dealer_data[landline], E-MAIL : $dealer_data[email]";

//Fetch the record details
$looper = $myobj->print_looper_challan($_GET['id']);
//pre($looper);
?>
<style type="text/css" media="all">
div#certificate_container{font-family:"Times New Roman", Times, serif; font-size:14px; color:#000;}
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
<div id="searchlistdiv">
<?php foreach($looper as $key=>$value){// pre($value); //this loop will help in the printing of the multiple bills?>
<div id="certificate_container" style="padding:0px; font-size:62.5%; font-family:'Times New Roman', Times, serif; page-break-after:auto; margin:0 auto; width:595pt;">
  
  <div id="pritn_what">
    <div class="mytitle" style="font-size:14pt;text-align:center; font-weight:bold; margin-top:0;">
      <span style="text-decoration:underlinel;"><?php if($value['adr']['tin_no']!='') echo"TAX "; else echo "RETAIL "  ?>INVOICE</span><br />
<!--      <span style="font-size:9pt; font-weight:normal; letter-spacing:-0.5pt;">Invoice for removal of Excisable Goods from a factory or warehouse on payment of Duty (Rule-11)</span>      -->
    </div> 
    <div style="height:20pt;">&nbsp;</div>  
  </div>
  
  <div id="certificate_header">
    <table width="100%" class="header_table" style="margin-top:0pt; border:1pt solid #000; border-collapse:collapse; font-family:'Times New Roman', Times, serif; font-size:10pt;" border="1">
      <tr>
        <td valign="top" colspan="3" style="width:50%;">
          <!-- table to tin no detail starts here -->
          <table border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
            <tr>
                <td colspan="3"><strong><?php echo $companyname; ?></strong></td>
            </tr>
            <tr>
                <td colspan="3">(AUTH. DIST. OF <?php echo $company_data['name']; ?>)</td>
            </tr>
            <tr>
                <td colspan="3" style="">
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
        <td valign="top" colspan="8">
          <!-- table to Tarrif heading detail starts here -->
          <table border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
            <tr>
              <td><strong>TO</strong> </td>
              <td valign="top"></td>
            </tr>
            <tr>
              <td><strong>INV. No.: <?php echo $value['ch_no']; ?> Date : <?php echo $value['ch_date']; ?></strong> </td>
              <td></td>
            </tr>
            <tr>
                <td colspan="2"><?php echo $value['retailer_name']; ?><br>
                <?php echo $value['adr']['address']; ?>
                </td>
            </tr>
             <tr>
                 <td colspan="2">TIN.- : <?php  echo $value['adr']['tin_no']; ?></td>
            </tr>
          </table>
          <!-- table to Tarrif heading detail ends here -->
        </td>  
      </tr>
     
      <tr style="font-weight:bold;">
        <td style="width:5%">S No.</td>
        <td style="width:27%">Item Name</td>
        <td style="width:5%" align="right">Qty</td>
        <td style="width:8%" align="right">Sch. Qty</td>
        <td style="width:7%">M.R.P</td>
        <td style="width:8%">Rate</td>
        <td style="width:8%">Sch.</td>
	<td style="width:7%" align="right">C.D.Amt</td>
        <td style="width:5%" align="right">Vat%</td>
        <td style="width:8%" align="right">VAT Amt</td>
        <td style="width:15%" align="right">Amount</td>
      </tr>
      <?php 
	  $inc =1; 
	  $amount = array();
	  $mybarcode = '';
          $grandtotal = 0;
	  foreach($value['challan_item'] as $key1=>$value1){ 
	  	if(!empty($value1['lineno'])) $mybarcode .= "\t".$value1['lineno']."\t".$value1['qty'];
                
               // if(empty($value1['taxId']))
                //    $taxvalue = 0; 
                //else 
                //  $taxvalue = $value1['taxId'];
                
               // $netAmt = my2digit($value1['ch_qty']*$value1['product_rate'] + $value1['ch_qty']*$value1['product_rate'] * $taxvalue/ 100 );
               
	  ?>
       <tr>
        <td style="border-bottom:none;border-top:none;"> <?php echo $inc;?></td>
        <td style="border-bottom:none;border-top:none;"> <?php echo $value1['name'];?></td>
         <td style="border-bottom:none;border-top:none;" align="right"> <?php echo $value1['qty'];?></td>
         <td style="border-bottom:none;border-top:none;" align="right"> <?php echo $value1['free_qty'];?></td>
        <td style="border-bottom:none;border-top:none;" align="right"><?php echo my2digit($value1['mrp']);?></td>
        <td style="border-bottom:none;border-top:none;" align="right"> <?php echo my2digit($value1['product_rate']);?></td>
       <td style="border-bottom:none;border-top:none;" align="right"> <?php echo my2digit("0.00");?></td>
	<?php  
        if($value1['cd_type']==1){
        $cd = (($value1['qty'] * $value1['product_rate'])*$value1['cd'])/100;        
        }
elseif ($value1['cd_type']==2) {
 $cd = ($value1['cd']);        
}
            //$vat_amt = ((($value1['qty'] * $value1['product_rate'])-$cd)*$value1['tax'])/100;
                  $vat_amt = $value1['vat_amt']; 
                  $amt=$value1['taxable_amt']; 
                  $cd_amt=$value1['cd_amt'];
                  $td_amt=$value1['dis_amt'];
                  $net_amt=$amt; 
            //$net_amt = ($value1['qty'] * $value1['product_rate'])-$cd +$vat_amt;
        ?>
       <td style="border-bottom:none;border-top:none;" align="right"> <?php echo my2digit($cd);?></td>
        <td style="border-bottom:none;border-top:none;" align="right"> <?php echo my2digit($value1['tax']).'';?></td>
        <td style="border-bottom:none;border-top:none;" align="right"> <?php echo my2digit($vat_amt) ;?></td>
        <td style="border-bottom:none;border-top:none;" align="right"> <?php echo my2digit($net_amt) ;?></td>
      </tr>
	  <?php 
          $grandcd += $cd;
          $grandtotal += $net_amt;
          $grandvat += $vat_amt;
          $inc++; }// foreach($value['po_item'] as $key1=>$value1){ ends?>
      <!-- printing the blank td to standarise the height of po starts -->
      <?php for($i = $inc; $i<20; $i++){ ?>
       <tr>
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>  
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>  
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>
	<td style="border-bottom:none;border-top:none;">&nbsp;</td>
      </tr>
	  <?php }// foreach($i= $inc; $i<10; $i++;){ ends?>
      <tr>
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>
        <td style="border-bottom:none;border-top:none;"><div id="bcContent"></div></td>
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>  
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>
        <td style="border-bottom:none;border-top:none;">&nbsp;</td> 
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>
	<td style="border-bottom:none;border-top:none;">&nbsp;</td>
      </tr>
      <!-- printing the blank td to standarise the height of po ends -->    
        <tr style="font-size:11pt;">
        
        <td colspan="7"><strong>GRAND TOTAL</strong></td>
       
        <td colspan="1" align="right"><strong><?php echo price_to_words(my2digit($grandcd)); ?><!--106425.00--></strong></td>
     
        <td colspan="2" align="right"><strong><?php echo price_to_words(my2digit($grandvat)); ?><!--106425.00--></strong></td>
        <td colspan="1" align="right"><strong><?php echo price_to_words(round(my2digit($grandtotal,2))); ?><!--106425.00--></strong></td>
      </tr> 
      <tr style="font-size:11pt;">
         <td colspan="11">Amount of Invoice (in Words) Rupees. <b><?php echo strtoupper(price_to_words($grandtotal,2));?></b><!--ONE LAC SIX THOUSAND FOUR HUNDRED TWENTY FIVE ONLY.--></td>
        </tr>      
    </table>
    <table width="100%" border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
      <tr>
        <td valign="top" style="border:none;width:70%"><span style="font-size:9pt;">E & O E Please Check the stock at delivery All disputes are subject to delhi Jursdiction only. It is computer generated invoice does not need signature.</span></td>
        <td align="right" style="border-left:none;">For <?php echo $dealer_data['name']; ?><br /><br /><br />Authorised Signatory</td>
      </tr>
    </table>  
  </div>
</div><!-- #certificate_conatiner ends -->

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
