<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php
$dealer_id = $_SESSION[SESS.'data']['dealer_id'];
$company_id = $_SESSION[SESS.'data']['company_id'];
$state_session = $_SESSION[SESS.'data']['state_id'];
//$state_igst = '500';
//pre($_SESSION);
//echo $dealer_id;
//echo $company_id;
$id =  $_GET['id'];
$myobj = new receive_order();
$rs = $myobj->get_receive_order_list_on_purchase($filter="id=$id",  $records = '', $orderby ="ORDER BY $myorderby");
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
// echo 1;die;
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
<?php
//pre($rs);
foreach($rs as $key=>$value){ 
//this loop will help in the printing of the multiple bills
    $company_id = $value['company_id'];
   // $state = $value['state_id'];
    if($state_session == $state_session)
    {
        $col = 17;
        $headcol = 12;
    }
    else
    {
        $col = 15;
         $headcol = 10;
    }
    $company_name = myrowval('company','name','id='.$company_id.'');
    //echo $company_name;
    ?>
<div id="certificate_container" style="padding:0px; font-size:62.5%; font-family:'Times New Roman', Times, serif; page-break-after:auto; margin:0 auto; width:750pt;">
  
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
        <td valign="top" colspan="7" style="width:50%;">
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
        <td valign="top" colspan="<?=$headcol?>">
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
                 <td colspan="4"><strong>Bill No.-:</strong> <?php  echo $value['challan_no']; ?> 
                 <strong>Bill Date.-:</strong> <?php  echo $value['created_dates']; ?></td> 
            </tr>
          </table>
          <!-- table to Tarrif heading detail ends here -->
        </td>  
      </tr>
     
      <tr style="font-weight:bold;">
        <td style="width:3%">S No.</td>
        <td style="width:30%">Item Name</td>
        <td style="width:10%">HSN CODE</td>
<!--        <td style="width:10%" align="center">Quantity (In Case)</td>-->
        <td style="width:10%"; align="center">Rate (Per Unit)</td>
        <td style="width:10%"; align="center">Qty (In Piece)</td>
        <td style="width:10%"; align="center">Free Qty(In Piece)</td>
        <?php /*<td style="width:16%"align="center">M.R.P</td>*/?>
<!--        <td style="width:16%"align="center">Landing Price</td>-->
        <td style="width:23%"; align="center">Gross Amt.</td>
        <td style="width:23%"; align="center">TD Amt.</td>
        <td style="width:23%"; align="center">SCH Amt.</td>
        <td style="width:23%"; align="center">SPL Amt.</td>
        <td style="width:23%"; align="center">CD Amt.</td>
        <td style="width:23%"; align="center">ATD Amt.</td>
        <?php if($state_session ==$state_session)
        {?>
        <td style="width:23%"; align="center">CGST %</td>
        <td style="width:23%"; align="center">CGST Amt.</td>
        <td style="width:23%"; align="center">SGST %</td>
        <td style="width:23%"; align="center">SGST Amt.</td>
        <?php }else{ ?>
        <td style="width:23%"; align="center">IGST %</td>
        <td style="width:23%"; align="center">IGST Amt.</td>
        <?php }?>
         <td style="width:23%"; align="center">Cr Note</td>
          <td style="width:23%"; align="center">Dr Note</td>
        <td style="width:23%"; align="center">Total Amt.</td>
      </tr>
      <?php 
    $inc =1; 
          $i=1;
    $amount = array();
          $qty1 = 0;
           $qty2 = 0;
          $rate1 =0; 
          $rate2 = 0;
          $mybarcode = '';
          $grandtotal = 0;
          $total_sale_value=0;
          $total_td_value=0;
          $total_atd_value=0;
          $total_amt=0;
         $taxs1 = 0;
         $taxs2 = 0;
         $taxs = 0;
          $aftergst = 0;
           $total_cr_value=0;
            $total_dr_value=0;
         $total_sch_value=0;
         $total_spl_value=0;
         $total_cd_value=0;
         // $surcharge=  myrowval('catalog_product_rate_list', 'surcharge', 'stateid='.$_SESSION[SESS.'data']['state_id']);
         // print_r($surcharge);
         // print_r($value); die;
    foreach($value['order_item'] as $key1=>$value1){ 
  //pre($value1);
//               $total_sale_value=$value1['pr_rate']*$value1['quantity'];
                //$total_sale_value=$value1['pr_rate']*$value1['quantity'];
                $total_sale_value=$value1['gross_amt'];
                $total_td_value+=$value1['td_amount'];
                $total_sch_value+=$value1['sch_amt'];
                $total_spl_value+=$value1['spl_amt'];
                $total_cd_value+=$value1['cd_amount'];
                $total_atd_value+=$value1['atd_amt'];
                 $total_cr_value+=$value1['cr_note'];
                  $total_dr_value+=$value1['dr_note'];
                $gst1=myrowval('_gst','igst','hsn_code='.$value1['hsn_code']);
               if($state_session ==$state_session)
               {
               //$gst = $value1['gst_percentage']/2;
               
               $gst =$gst1/2;
               $tax1 = $gst*$total_sale_value/100;
               $total1 = $total_sale_value+$tax1;  
               $tax2 = $gst*$total_sale_value/100;
               $total2 = $total_sale_value+$tax2; 
              // $total = $total_sale_value+$tax2+$tax1;
               //$total=$total_sale_value+$value1['cgst_amount']+$value1['sgst_amount'];
               $total=$value1['total_amount'];
               //$taxs1 = $taxs1+ $tax1;
               $taxs1 = $taxs1+ $value1['cgst_amount'];
               //$taxs2 = $taxs2+ $tax2;
               $taxs2 = $taxs2+ $value1['sgst_amount'];
               }
               else{
               $tax = $value1['gst']*$total_sale_value/100;
               $tax=$value1['cgst_amount']+ $value1['sgst_amount'];
               //$total = $total_sale_value+$tax;
               //$total=$total_sale_value+$value1['cgst_amount']+$value1['sgst_amount'];
               $total=$value1['total_amount'];
               $taxs = $taxs+ $tax;
               }
               
               $case = $value1['quantity']/$value1['cases'];
               $caserate = $case*$value1['rate'];
//            pre($value1);  
    ?>
       <tr>
        <td style="border-bottom:none;border-top:none;height:36px;"> <?php echo $i;?></td>
        <td style="border-bottom:none;border-top:none;height:36px;"> <?php echo $value1['name'];?></td>
        <td style="border-bottom:none;border-top:none;height:36px;"> <?php echo $value1['hsn_code'];?></td>
<!--        <td style="border-bottom:none;border-top:none;height:10px;"align="center"> <?php echo $value1['cases'];?></td>-->
        <td style="border-bottom:none;border-top:none;height:10px;"align="center"> <?php echo my2digit($value1['pr_rate']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="center"> <?php echo $value1['quantity'];?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="center"> <?php echo $value1['scheme_qty'];?></td>
       <?php /* <td style="border-bottom:none;border-top:none;height:36px;" align="center"> <?php echo $value1['mrp'];?></td> */?>
        <!--<td style="border-bottom:none;border-top:none;height:36px;" align="center"> <?php// echo my2digit($value1['rate']);?></td>-->
        <td style="border-bottom:none;border-top:none;height:36px;" align="center"><?php echo my2digit($value1['gross_amt']); ?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="center"><?php echo my2digit($value1['td_amount']); ?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="center"><?php echo my2digit($value1['sch_amt']); ?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="center"><?php echo my2digit($value1['spl_amt']); ?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="center"><?php echo my2digit($value1['cd_amount']); ?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="center"><?php echo my2digit($value1['atd_amt']); ?></td>
     <?php if($state_session == $state_session)
        {?>
      <td style="border-bottom:none;border-top:none;height:36px;" align="center"><?=$gst ?></td>
      <td style="border-bottom:none;border-top:none;height:36px;" align="center"><?php echo $value1['cgst_amount'];?></td>
      <td style="border-bottom:none;border-top:none;height:36px;" align="center"><?=$gst ?></td>
     <td style="border-bottom:none;border-top:none;height:36px;" align="center"><?php echo $value1['sgst_amount'];?></td>
       <?php }else{ ?>
      <td style="border-bottom:none;border-top:none;height:36px;" align="center"><?=$value1['gst'] ?></td>
      <td style="border-bottom:none;border-top:none;height:36px;" align="center"><?php echo my2digit($tax); ?></td>
       
       <?php } ?>
       <td style="border-bottom:none;border-top:none;height:36px;" align="center"><?php echo my2digit($value1['cr_note']); ?></td>
       <td style="border-bottom:none;border-top:none;height:36px;" align="center"><?php echo my2digit($value1['dr_note']); ?></td>
      <td style="border-bottom:none;border-top:none;height:36px;" align="center"><?php echo my2digit($total); ?></td>
      </tr>
      
    <?php
         // $total_amt = $total_amt+($value1['cases']*$value1['pr_rate']);
          $total_amt = $total_amt+$value1['gross_amt'];
          
         $rate1 = $rate1+$value1['mrp'];
         $rate2 = $rate2+$value1['rate'];
         $qty1 = $qty1+$value1['quantity'];
         $qty2 = $qty2+$value1['scheme_qty'];
         $aftergst = $total+$aftergst;
         
         
         //$total_sales_value_array[]= $total_sale_value;
        // $grand_total_value[] =  $total_sale_value;
        // $g=my2digit(array_sum($total_sales_value_array));
          $inc++; 
          $i++;
          if($inc>30){
              $inc=1;
              echo '</table><div style="page-break-after:always;page-break-inside:avoid;width: 90%;">
                    </div>
                    <table width="100%" class="header_table" style="margin-top:0pt; border:1pt solid #000; border-collapse:collapse; font-family:\'Times New Roman\', Times, serif; font-size:10pt;" border="1">
                    <tr style="font-weight:bold;">
        <td style="width:3%">S No.</td>
        <td style="width:30%">Item Name</td>
        <td style="width:10%">HSN CODE</td>
   <!--     <td style="width:10%" align="center">Quantity (In Case)</td>-->
        <td style="width:10%" align="center">Rate (Per Unit)</td>
        <td style="width:10%"align="center">Qty (In Piece)</td>
        <td style="width:10%"align="center">Free Qty(In Piece)</td>
        <td style="width:23%" align="center">Taxable Amt.</td>
        <td style="width:23%" align="center">TD Amt.</td>
        <td style="width:23%"; align="center">SCH Amt.</td>
        <td style="width:23%"; align="center">SPL Amt.</td>
        <td style="width:23%"; align="center">CD Amt.</td>
        <td style="width:23%"; align="center">ATD Amt.</td>
        
        ';
         if($state_session==$state_session)
        {
        echo'<td style="width:23%" align="center">CGST %</td>
        <td style="width:23%" align="center">CGST Amt.</td>
        <td style="width:23%" align="center">SGST %</td>
        <td style="width:23%" align="center">SGST Amt.</td>';
        }else{ 
        echo'<td style="width:23%" align="center">IGST %</td>
        <td style="width:23%" align="center">IGST Amt.</td>';
        }
        echo'
         <td style="width:23%"; align="center">Cr Note</td>
          <td style="width:23%"; align="center">Dr Note</td>
        <td style="width:23%" align="center">Total Amt.</td>
      </tr>
                    ';
          }          
          
             }// foreach($value['po_item'] as $key1=>$value1){ ends
    //   for($k=$inc;$k<8;$k++){
   ?>
    
      <?php// } ?>
      <tr style="font-size:11pt;">
        
        <td colspan="3" align="center"><strong>GRAND TOTAL</strong></td>
        <td colspan="1" align="center"><strong>-</strong></td>
        <td colspan="1" align="center"><strong><?php echo $qty1; ?><!--106425.00--></strong></td>
         
          
        <td colspan="1" align="center"><strong><?php echo $qty2; ?><!--106425.00--></strong></td>
<!--         <td colspan="1" align="center"><strong>-</strong></td>-->
         
       <?php /* <td align="center">&nbsp;</td>*/ ?>
       <td colspan="" align="center"><strong><?php echo my2digit($total_amt); ?><!--106425.00--></strong></td>
       <td colspan="" align="center"><strong><?php echo my2digit($total_td_value); ?><!--106425.00--></strong></td>
       <td colspan="" align="center"><strong><?php echo my2digit($total_sch_value); ?><!--106425.00--></strong></td>
       <td colspan="" align="center"><strong><?php echo my2digit($total_spl_value); ?><!--106425.00--></strong></td>
       <td colspan="" align="center"><strong><?php echo my2digit($total_cd_value); ?><!--106425.00--></strong></td>
        <td colspan="" align="center"><strong><?php echo my2digit($total_atd_value); ?><!--106425.00--></strong></td>
        
        
       <?php if($state_session==$state_session)
        {?>
        <td colspan="1" align="center"><strong>-</strong></td>
        <td colspan="1" align="center"><strong><?=my2digit($taxs1)?></strong></td>
         <td colspan="1" align="center"><strong>-</strong></td>
        <td colspan="1" align="center"><strong><?=my2digit($taxs2)?></strong></td>
        <?php }else{ ?> 
         <td colspan="1" align="center"><strong>-</strong></td>
        <td colspan="1" align="center"><strong><?=my2digit($taxs)?></strong></td>
        
        <?php } ?>
         <td colspan="" align="center"><strong><?php echo my2digit($total_cr_value); ?><!--106425.00--></strong></td>
          <td colspan="" align="center"><strong><?php echo my2digit($total_dr_value); ?><!--106425.00--></strong></td>
         <td colspan="1" align="center"><strong><?=my2digit($aftergst)?></strong></td>
      
      </tr> 
      <tr style="font-size:12pt;">
         <td colspan="<?=$col?>">Amount of Invoice (in Words) Rupees. <b><?php
         $grandtotal = my2digit($aftergst);
         $roun_off_value = round($grandtotal).'.00';
         echo strtoupper(price_to_words($grandtotal,2));?></b><!--ONE LAC SIX THOUSAND FOUR HUNDRED TWENTY FIVE ONLY.--></td>
         <td colspan="2" style="text-align: right;">Grand Total <br><span style="display: flex; justify-content: center;"><b style="text-align: center;"><?=$roun_off_value?></b></span></td>
        </tr>

        <tr>
<!--           <td colspan="<?=$headcol?>" style="border-right:none;">
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
              //  echo "<td>".$val['surcharge']."</td>";
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
               // echo "<td>".$surcharge1."</td>";
                echo "<td>".$total1."</td>";
                echo "</tr>";
           ?>
               </table>
           </td>-->
            <td colspan="<?=$headcol?>" style="border-right:none;"></td>
           <td colspan="7" align="right" style="border-left:none;"><b>For : <?=$_SESSION[SESS.'data']['dealer_name']?> </b><br /><br /><br />Authorised Signatory</td>
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
