<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php
$dealer_id = $_SESSION[SESS.'data']['dealer_id'];
$company_id = $_SESSION[SESS.'data']['company_id'];
//pre($_SESSION);
//echo $dealer_id;
//echo $company_id;
$id =  $_GET['id'];
$myobj = new purchase_order();
$rs = $myobj->get_purchase_order_list($filter="id=$id",  $records = '', $orderby ="ORDER BY $myorderby");
/*pre($rs);
*///echo $rs['company_id'];
//

$myobj1 = new dealer();
$dealer_data = $myobj1->get_dealer_list($filter = "id = '$dealer_id'", $records, $orderby);
$dealer_data = $dealer_data[$dealer_id];
//print_r($dealer_data);
//pre($dealer_data);
//pre($dealer_data);
$companyname = $dealer_data['name'];
$company_adr = $dealer_data['address'];
$phone = " +91 $dealer_data[other_numbers], E-MAIL : $dealer_data[email]";

//h1($_GET['id']);
//$looper = $myobj->print_looper_challan($_GET['id']);

?>
<style type="text/css" media="all">
div#certificate_container{font-family:"kitfont", Times, serif; font-size:12px; color:#000;}
table.certificate_detail td.col1{ text-align:left;}
table.certificate_detail td.col2{ text-align:center;}
table.certificate_detail td.col3{ text-align:right;}

div.mytitle{ font-size:18px; margin-bottom:5px; text-align:center; font-weight:bold; margin-top:10px;}

div.calib_result table{border-collapse:collapse; border:1px solid;}
div.calib_result table tr td{ padding-left:5px;}
</style>

<table width="100%">
  <tr>
    <td>
      <div class="subhead1"><!-- this portion indicate the print options -->
        <a href="javascript:printing('searchlistdiv');" title="take a printout" style="margin:0 10px;"><img src="./icons/print.png" /></a>
        Print Purchase Order
      </div>
    </td>
  </tr>	
</table>
<div id="searchlistdiv" style="page-break-inside: avoid;">
<?php foreach($rs as $key=>$value){ //this loop will help in the printing of the multiple bills
    $company_id = $value['company_id'];
    $company_name = myrowval('company','name','id='.$company_id.'');
    //echo $company_name;
    ?>
<div id="certificate_container" style="padding:0px; font-size:62.5%; font-family:'Times New Roman', Times, serif; page-break-after:auto; margin:0 auto; width:595pt;">
  
  <div id="pritn_what">
    <div class="mytitle" style="font-size:14pt;text-align:center; font-weight:bold; margin-top:0;">
      <span style="text-decoration:underline;">
        <?php if($value['ch_date']!='1970-01-01'){ ?>
        GOOD RECEIVED
        <?php }else{ ?>
        PURCHASE ORDER
        <?php } ?>
        </span><br />
<!--      <span style="font-size:9pt; font-weight:normal; letter-spacing:-0.5pt;">Invoice for removal of Excisable Goods from a factory or warehouse on payment of Duty (Rule-11)</span>      -->
    </div> 
  </div>
  
  <div id="certificate_header">
    <table width="100%" class="header_table" style="margin-top:0pt; border:1pt solid #000; border-collapse:collapse; font-family:'Times New Roman', Times, serif; font-size:10pt;" border="1">
      <tr>
        <td valign="top" colspan="3" style="width:50%;">
          <!-- table to tin no detail starts here -->
          <table border="0" style="font-family:kitfont; font-size:8pt;">
            <tr>
                <td colspan="3"><strong><?=$_SESSION[SESS.'data']['dealer_name']?></strong></td>
            </tr>
            <tr>
                <td colspan="3">(AUTH. DIST. OF <?php echo $company_name; ?>)</td> <td></td>
            </tr>
            <tr>
              <td colspan="7"><strong>Order No.: </strong><?php echo $value['order_id']; ?> <br><strong>Order Date : </strong><?php echo date('d-m-Y',strtotime($value['order_date'])); ?> </td>
              <td></td>
            </tr>
           
          </table>
          <!-- table to tin no detail ends here -->
        </td>
        <td valign="top" colspan="3">
          <!-- table to Tarrif heading detail starts here -->
          <table border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
            <tr>
              <td><strong>From : </strong><?= $value['csa_name']?> </td>
	      <td align="right"><strong>Authorised Signatory</strong> </td>
              </tr>
              <tr>
                <td colspan="3"> <br><strong>Receiving Date : </strong><?php echo date('d-m-Y',strtotime($value['ch_date'])); ?>
                </td>
             </tr>
             <tr>
                 <td colspan="4"><strong>Challan No.-:</strong> <?php  echo $value['challan_no']; ?></td>	
            </tr>
          </table>
          <!-- table to Tarrif heading detail ends here -->
        </td>  
      </tr>
     
      <tr style="font-weight:bold;">
        <td style="width:3%">S No.</td>
        <td style="width:30%">Item Name</td>
        <td style="width:10%" align="center">Quantity (In Case)</td>
        <td style="width:10%"align="center">Qty (In Piece)</td>
        <?php /*<td style="width:16%"align="center">M.R.P</td>*/?>
        <td style="width:16%"align="center">Landing Price</td>
        <td style="width:23%" align="center">Total Amt.</td>
      </tr>
      <?php 
	  $inc =1; 
          $i=1;
	  $amount = array();
          $qty1 = 0;
           $qty2 = 0;
          $rate1 =0; $rate2 = 0;
          $mybarcode = '';
          $grandtotal = 0;
          $total_sale_value=0;
          $total_amt=0;
         // $surcharge=  myrowval('catalog_product_rate_list', 'surcharge', 'stateid='.$_SESSION[SESS.'data']['state_id']);
         // print_r($surcharge);
         // print_r($value);
	  foreach($value['order_item'] as $key1=>$value1){ 
	//pre($value1);
              $total_sale_value=$invalue['rate']*$invalue['quantity'];
	  ?>
       <tr>
        <td style="border-bottom:none;border-top:none;height:36px;"> <?php echo $i;?></td>
        <td style="border-bottom:none;border-top:none;height:36px;"> <?php echo $value1['name'];?></td>
        <td style="border-bottom:none;border-top:none;height:10px;"align="center"> <?php echo $value1['cases'];?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="center"> <?php echo $value1['quantity'];?></td>
       <?php /* <td style="border-bottom:none;border-top:none;height:36px;" align="center"> <?php echo $value1['mrp'];?></td> */?>
        <td style="border-bottom:none;border-top:none;height:36px;" align="center"> <?php echo $value1['rate'];?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="center"><?php echo my2digit($value1['quantity']*$value1['rate']); ?></td>
      
      </tr>
      
	  <?php
          $total_amt = $total_amt+($value1['quantity']*$value1['rate']);
         $rate1 = $rate1+$value1['mrp'];
         $rate2 = $rate2+$value1['rate'];
         $qty1 = $qty1+$value1['quantity'];
         $qty2 = $qty2+$value1['cases'];
         //$total_sales_value_array[]= $total_sale_value;
        // $grand_total_value[] =  $total_sale_value;
        // $g=my2digit(array_sum($total_sales_value_array));
          $inc++; 
          $i++;
          if($inc>10){
              $inc=1;
              echo '</table><div style="page-break-after:always;page-break-inside:avoid;width: 90%;">
                    </div>
                    <table width="100%" class="header_table" style="margin-top:0pt; border:1pt solid #000; border-collapse:collapse; font-family:\'Times New Roman\', Times, serif; font-size:10pt;" border="1">
                    <tr style="font-weight:bold;">
                         <td style="width:3%">S No.</td>
        <td style="width:30%">Item Name</td>
        <td style="width:10%" align="center">Quantity (In Case)</td>
        <td style="width:10%" align="center">Qty (In Piece)</td>
        <td style="width:16%" align="center">Landing Price</td>
        <td style="width:23%" align="center">Total Amt.</td>
                      </tr>
                    ';
          }          
          
             }// foreach($value['po_item'] as $key1=>$value1){ ends
    //   for($k=$inc;$k<8;$k++){
   ?>
    
      <?php// } ?>
      <tr style="font-size:11pt;">
        
        <td colspan="2" align="center"><strong>GRAND TOTAL</strong></td>
       
        <td colspan="1" align="center"><strong><?php echo $qty2; ?><!--106425.00--></strong></td>
        <td colspan="1" align="center"><strong><?php echo $qty1; ?><!--106425.00--></strong></td>
        <td colspan="1" align="center"><strong><?php //echo my2digit($rate1); ?><!--106425.00--></strong></td>
        
       <?php /* <td align="center">&nbsp;</td>*/ ?>
        <td colspan="" align="center"><strong><?php echo my2digit($total_amt); ?><!--106425.00--></strong></td>
         </tr> 
      <tr style="font-size:11pt;">
         <td colspan="6">Amount of Invoice (in Words) Rupees. <b><?php
         $grandtotal = my2digit($total_amt);
         echo strtoupper(price_to_words($grandtotal,2));?></b><!--ONE LAC SIX THOUSAND FOUR HUNDRED TWENTY FIVE ONLY.--></td>
        </tr>

        <tr>
           <td colspan="3" style="border-right:none;">
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
                echo "<td>".$val['surcharge']."</td>";
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
                echo "<td>".$surcharge1."</td>";
                echo "<td>".$total1."</td>";
                echo "</tr>";
           ?>
               </table>
           </td>
           <td colspan="3" align="right" style="border-left:none;"><b>For : <?=$_SESSION[SESS.'data']['dealer_name']?> </b><br /><br /><br />Authorised Signatory</td>
        </tr>
    </table>
    <table width="100%" border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
      <tr>
        <td valign="top" style="border:none;width:70%"><span style="font-size:9pt;">E & O E Please Check the stock at delivery All disputes are subject to Jursdiction only. It is computer generated invoice does not need signature.</span></td>
      </tr>
       <tr>
        <td valign="top" style="border:none;width:70%"> <br><hr/><br></td>
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