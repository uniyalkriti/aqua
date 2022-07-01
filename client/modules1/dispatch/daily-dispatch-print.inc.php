<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php
//Reading the company statistics
/*$company = new company();
$cmpId = 1;
$companystat = $company->get_company_list("cmpId=$cmpId");
$companystat = $companystat[$cmpId];
*/
//Fetch the record details
$looper = $myobj->print_looper_daily_dispatch($_GET['id']);
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
<?php foreach($looper as $key=>$value){ //this loop will help in the printing of the multiple bills
   
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
          <td colspan="11" valign="top" style="width:50%;">
          <?php 
            foreach($value['dealer_details'] as $inkey2=>$invalue2)
            {
               //pre($invalue2);
               echo '<strong>'.$invalue2['name'].'</strong><br>';
               echo $invalue2['address'].'<br>';
               echo 'Pin No&nbsp;'.$invalue2['pin_no'].'<br>';
               echo '<h2>Daily Dispatch Summary</h2>';
               echo 'Date :&nbsp;'.$value['dispatch_date'].'<br>';
               echo 'Van No :&nbsp;'.$value['van_no'];
            }
          ?>
        </td>
      </tr>
     
      <tr style="font-weight:bold;">
        <td style="width:5%">S No.</td>
        <td style="width:27%">Product Description</td>
        <td style="width:7%">M.R.P</td>
        <td style="width:10%">Basic Price.</td>
         <td style="width:8%" align="right">Quantity</td>
        <td style="width:7%" align="right">C.D.Amt</td>
        <td style="width:7%" align="right">T.D.Amt</td>
        <td style="width:5%" align="right">Vat%</td>
        <td style="width:8%" align="right">VAT Amt</td>
        
        <td style="width:12%" align="right">Weight</td>
        <td style="width:12%" align="right">Amount</td>
      
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
          while($value1 = mysqli_fetch_assoc($q))
          {
              //pre($value1);
              $chid = $value1['ch_id'];
          $query1 = "select qty,free_qty,weight from `challan_order_details` inner join catalog_product on 
                           challan_order_details.product_id=catalog_product.id
                           WHERE `ch_id` = '$chid'"; 
                   // h1($query1);
                       $quan = array();
                       $result1 = mysqli_query($dbc,$query1);
                     while($row1 = mysqli_fetch_assoc($result1)){
                          $qtyy = $row1['qty'];
                     $fqtyy = $row1['free_qty'];
                     $weight = $row1['weight'];
                     $quan[] = ($qtyy+$fqtyy)*$weight;
                     }
                    $quant = array_sum($quan);
                    
                    $quanti = $quant/1000;
                     
        //  }
          //foreach($value['challan_dispatch'] as $key1=>$value1){ 
	  //pre($value1);
                if(empty($value1['tvalue']))
                    $taxvalue = 0; 
                else 
                  $taxvalue = $value1['tvalue'];
                
                  $netAmt = my2digit($value1['qty']*$value1['product_rate']);
               
	  ?>
       <tr>
        <td style="border-bottom:none;border-top:none;"> <?php echo $inc;?></td>
        <td style="border-bottom:none;border-top:none;"> <?php echo $value1['name'].' [ '.$value1['batch_no']." ]";?></td>
        <td style="border-bottom:none;border-top:none;" align="right"><?php echo my2digit($value1['mrp']);?></td>
        <td style="border-bottom:none;border-top:none;" align="right"> <?php echo my2digit($value1['product_rate']);?></td>
        <td style="border-bottom:none;border-top:none;" align="right"> <?php echo $value1['qty'];?></td>
        <?php  
           //$cd = (($value1['qty'] * $value1['product_rate'])*$value1['cd'])/100;
            //$vat_amt = ((($value1['qty'] * $value1['product_rate'])-$cd)*$value1['tax'])/100;
           $vat_amt = $value1['vat_amt'];
            //$net_amt = ($value1['qty'] * $value1['product_rate'])-$cd +$vat_amt;
           $amount=$value1['amount'];
           $dis_amt=$value1['discount_amt'];
           $cd=$value1['cd_amt'];
           $td=$value1['dis_amt'];
           
            $net_amt = $amount+$dis_amt;
		$qunt = round($quanti,3);
        ?>
        <td style="border-bottom:none;border-top:none;" align="right"> <?php echo $cd;?></td>
        <td style="border-bottom:none;border-top:none;" align="right"> <?php echo $td;?></td>
        <td style="border-bottom:none;border-top:none;" align="right"> <?php  echo $value1['tax'];?></td>
        <td style="border-bottom:none;border-top:none;" align="right"> <?php  echo my2digit($vat_amt);?></td>
        
        <td style="border-bottom:none;border-top:none;" align="right"> <?php echo $qunt."Kg."?></td>
         <td style="border-bottom:none;border-top:none;" align="right"> <?php echo my2digit($net_amt);?></td>
      </tr>
	  <?php 
          $grandtotal += $net_amt;
          $granddisc += $dis_amt;
	  $grandweight += $qunt;
          $inc++; }// foreach($value['po_item'] as $key1=>$value1){ ends?>
      <!-- printing the blank td to standarise the height of po starts -->
      <?php for($i = $inc; $i<11; $i++){ ?>
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
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>
<!--        <td style="border-bottom:none;border-top:none;">&nbsp;</td>  
        <td style="border-bottom:none;border-top:none;">&nbsp;</td>-->
      </tr>
	  <?php }// foreach($i= $inc; $i<10; $i++;){ ends?>
      
      <!-- printing the blank td to standarise the height of po ends -->    
     
      <tr style="font-size:11pt;">
<!--         <td colspan="3">Amount of Invoice (in Words) Rupees. <?php echo strtoupper(price_to_words($grandtotal,2));?></td>-->
<!--          <td colspan="3">Total Bills : <?php echo $value['total_bills']; ?>&nbsp;&nbsp;-->
             <?php //echo $value['total_product']; ?>
         </td>
         <td colspan="10"><strong>DISCOUNT</strong></td>
         <td align="right"><strong><?php echo price_to_words(my2digit($granddisc,2)); ?></td>
         <tr>
              <td colspan="10"><strong>TOTAL</strong></td>
         <td align="right"><strong><?php echo price_to_words(my2digit($grandtotal,2)); ?></td>
         </tr>
         <?php $grandtotal1=$grandtotal-$granddisc?>
         <tr>     
        <td colspan="9"><strong>GRAND TOTAL</strong></td>
	<td align="right"><strong><?php echo price_to_words(round($grandweight,3))."Kg."; ?>
                <td align="right"><strong><?php echo price_to_words(round(my2digit($grandtotal1,2))); ?><!--106425.00--></strong></td></tr>
      </tr>      
    </table>
    <table width="100%" border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
      <tr>
<!--          <td valign="top" style="border:none;width:70%"><span style="font-size:9pt;"><strong>Damage Replacements.</strong></span><br><br>Product
          </td>-->
          
        <td align="right" style="border-left:none;">For <?php echo $value['dealer_details'][$_SESSION[SESS.'data']['dealer_id']]['name']; ?><br /><br /><br />Authorised Signatory</td>
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
