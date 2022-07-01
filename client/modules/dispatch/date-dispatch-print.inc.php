<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php
//Reading the company statistics
/*$company = new company();
$cmpId = 1;
$companystat = $company->get_company_list("cmpId=$cmpId");
$companystat = $companystat[$cmpId];
*/
//Fetch the record details
$looper = $myobj->print_looper_date_dispatch($_GET['id']);
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
        Print Dispatch
      </div>
    </td>
  </tr>	
</table>
<div id="searchlistdiv">
   
<?php //pre($looper);
foreach($looper as $key=>$value){ //this loop will help in the printing of the multiple bills
   //print_r($value);
    ?>
<div id="certificate_container" style="padding:0px; font-size:62.5%; font-family:'Times New Roman', Times, serif; page-break-after:auto; margin:0 auto; width:595pt;">
  
  <div id="pritn_what">
    <div class="mytitle" style="font-size:14pt;text-align:center; font-weight:bold; margin-top:0;">
      <span style="text-decoration:underlinel;">Daily Dispatch Summary</span><br />
<!--      <span style="font-size:9pt; font-weight:normal; letter-spacing:-0.5pt;">Invoice for removal of Excisable Goods from a factory or warehouse on payment of Duty (Rule-11)</span>      -->
    </div> 
    <div style="height:20pt;">&nbsp;</div>  
  </div>
  
  <div id="certificate_header">
    <table width="100%" class="header_table" style="margin-top:0pt; border:1pt solid #000; border-collapse:collapse; font-family:'Times New Roman', Times, serif; font-size:10pt;" border="1">
      <tr>
          <td colspan="11" valign="top" style="width:50%;text-align: center;">
          <?php 
            foreach($value['dealer_details'] as $inkey2=>$invalue2)
            {
               //pre($invalue2);
               echo '<strong>'.$invalue2['name'].'</strong><br>';
               echo '(AUTH. DIST. OF DS Spiceco Pvt. Ltd.)<br/>';
               echo $invalue2['address'].' '.$invalue2['pin_no'].'<br>';
               echo '<strong>PH. NO.-:</strong>'.$invalue2['other_numbers'].'<br>';
               echo '<strong>E-Mail.-:</strong>'.$invalue2['email'].'<br>';
               echo '<strong>LANDLINE NO.-:</strong>'.$invalue2['landline'].'<br>';
               echo '<strong>GSTIN. No.-:</strong>'.$invalue2['tin_no'].'<br>';
               echo '<strong>VAN No.-:</strong>'.$value['van'].'<br>';
               
//               echo 'Date :&nbsp;'.$value['dispatch_date'].'<br>';
//               echo 'Van No :&nbsp;'.$value['van_no'];
            }
          ?>
        </td>
      </tr>
     
      <tr style="font-weight:bold;">
        <td style="width:5%">S No.</td>
        <td style="width:5%">Item Code</td>
        <td style="width:27%">Product Description</td>
        <td style="width:7%">M.R.P</td>
         <td style="width:8%">Quantity</td>
      
      </tr>
      <?php 
	  $inc =1; 
	  $amount = array();
	  $mybarcode = '';
          $grandtotal = 0; $grandweight = 0; $granddisc = 0;
          $dispatch_id = $value['dispatch_id'];
          $q = mysqli_query($dbc,"SELECT * FROM daily_dispatch_details INNER JOIN challan_order_details ON 
          challan_order_details.ch_id = daily_dispatch_details.ch_id INNER JOIN catalog_product ON 
                  catalog_product.id = challan_order_details.product_id INNER JOIN challan_order ON challan_order.id=challan_order_details.ch_id WHERE dispatch_id = '$dispatch_id'"); 
          foreach($value['challan_details'] as $inkey3=>$invalue3)
            {
              //pre($invalue3);
	  ?>
       <tr>
        <td style="border-bottom:none;border-top:none;"> <?php echo $inc;?></td>
        <td style="border-bottom:none;border-top:none;"> <?php echo $invalue3['itemcode'];?></td>
        <td style="border-bottom:none;border-top:none;"> <?php echo $invalue3['name'];?></td>
        <td style="border-bottom:none;border-top:none;" align="right"><?php echo my2digit($invalue3['mrp']);?></td>
        <td style="border-bottom:none;border-top:none;" align="right"> <?php echo $invalue3['qty'];?></td>
      </tr>
<?php 
$inc++;
 }?>
         <style>
          tr.title td {
    border-left: 0px solid;
    border-right: 0px solid;
}
          </style>
      <tr>
          <td height="20" colspan="5"></td>          
      </tr>

      <!-- printing the blank td to standarise the height of po starts -->
   
      <tr style="font-weight:bold;">
        <td style="width:5%">S No.</td>
        <td style="width:20%">Invoice No</td>
        <td style="width:27%">Invoice Date</td>
        <td style="width:20%">Retailer Name</td>
         <td style="width:8%">Total Amount</td>
      
      </tr>
     <?php 
	  $inc =1; 
	  $amount = array();
	  $mybarcode = '';
          $grandtotal = 0; $grandweight = 0; $granddisc = 0;
          $dispatch_id = $value['dispatch_id'];
          $q = mysqli_query($dbc,"SELECT * FROM daily_dispatch_details INNER JOIN challan_order_details ON 
          challan_order_details.ch_id = daily_dispatch_details.ch_id INNER JOIN catalog_product ON 
                  catalog_product.id = challan_order_details.product_id INNER JOIN challan_order ON challan_order.id=challan_order_details.ch_id WHERE dispatch_id = '$dispatch_id'"); 
          foreach($value['challan'] as $inkey4=>$invalue4)
            {
              //pre($invalue3);
              $grandtotal=$grandtotal+$invalue4['amount'];
	  ?>
       <tr>
        <td style="border-bottom:none;border-top:none;"> <?php echo $inc;?></td>
        <td style="border-bottom:none;border-top:none;"> <?php echo $invalue4['ch_no'];?></td>
        <td style="border-bottom:none;border-top:none;"> <?php echo $invalue4['ch_date'];?></td>
        <td style="border-bottom:none;border-top:none;" align="right"><?php echo $invalue4['name'];?></td>
        <td style="border-bottom:none;border-top:none;" align="right"> <?php echo my2digit($invalue4['amount']);?></td>
      </tr>
<?php 
$inc++;
 }?>

      <!-- printing the blank td to standarise the height of po ends -->    
     
      <tr><td colspan="3"></td>
      <td><strong>Grand Total</strong></td>
      <td align="right"><strong><?php echo my2digit($grandtotal);?></strong></td>
      </tr>      
    </table>
    <table width="100%" border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
      <tr>
         <!-- <td valign="top" style="border:none;width:70%"><span style="font-size:9pt;"><strong>Damage Replacements.</strong></span><br><br>Product
          </td> -->
          
        <!-- <td align="right" style="border-left:none;">For <?php echo $value['dealer_details'][$_SESSION[SESS.'data']['dealer_id']]['name']; ?><br /><br /><br />Authorised Signatory</td> -->
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
