<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>

<?php

$dealer_id = $_SESSION[SESS.'data']['dealer_id'];
$company_id = $_SESSION[SESS.'data']['company_id'];
$objcomp = new company();
$company_data = $objcomp->get_company_list($filter="company.id='$company_id'",  $records = '', $orderby='');
$myobjd = new dealer_sale();
$company_data = $company_data[$company_id];

$myobj1 = new dealer();
$dealer_data = $myobj1->get_dealer_list($filter = "id = '$dealer_id'", $records, $orderby);
$dealer_data = $dealer_data[$dealer_id];
$dealer_state_id = $dealer_data['dealer_state_id'];
//pre($dealer_data);
$companyname = $dealer_data['name'];
$company_adr = $dealer_data['address'];
$phone = " +91 $dealer_data[other_numbers],</br> <strong>E-MAIL : </strong>$dealer_data[email]";
//h1($_GET['id']);
$looper = $myobjd->print_looper_challan($_GET['id']);

//echo "<pre>";
//pre($looper); exit;
?>

<script type="text/javascript">
    function ShowHideDiv() {
        var ddlPassport = document.getElementById("ddlPassport");
        var normal = document.getElementById("normal");
        normal.style.display = ddlPassport.value == "Y" ? "block" : "none";
        var ank = document.getElementById("ank");
        ank.style.display = ddlPassport.value == "N" ? "block" : "none";
        var a412 = document.getElementById("a412");
        a412.style.display = ddlPassport.value == "12" ? "block" : "none";
        var a48 = document.getElementById("a48");
        a48.style.display = ddlPassport.value == "8" ? "block" : "none";
    }
</script>
<span>Do you want to change page type ?</span>
    <select id = "ddlPassport" onchange = "ShowHideDiv()">
        <option value="Y">Normal</option>
         <?php if($dealer_id==2000){ ?>   
             <option value="8">8 ON A4 Sheet</option> 
              <option value="N">10 ON A4 Sheet</option>    
              <option value="12">12 ON A4 Sheet</option>
          <?php } ?>
       
    </select>

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
<!-- NORMAL PAGE -->
<div id="normal" >
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
<?php 
foreach($looper as $key=>$value){ 
  $retailer_state_id = $value['adr']['state_id'];
  $user_id = $value['ch_user_id'];
  
  if($dealer_state_id == $retailer_state_id){
      $gst = 'CGST';
      $gst1 = 'SGST';
  }else{
      $gst = 'IGST';
  }
//this loop will help in the printing of the multiple bills?>
<div id="certificate_container" style="padding:0px; font-size:62.5%; font-family:'Times New Roman', Times, serif; page-break-after:auto; margin:0 auto; width:970pt;">
  <div id="pritn_what">
    <div class="mytitle" style="font-size:14pt;text-align:center; font-weight:bold; margin-top:0;">
      <span style="text-decoration:underlinel;">
      <?php 
      if($value['crdr_status']==1){
        echo "CREDIT NOTE";
      }elseif($value['crdr_status']==2){
        echo "DEBIT NOTE";
      }else{
      echo ($value['adr']['tin_no']!='') ? "TAX INVOICE":"TAX INVOICE";
        }
      ?>
    
      </span><br />
<!--      <span style="font-size:9pt; font-weight:normal; letter-spacing:-0.5pt;">Invoice for removal of Excisable Goods from a factory or warehouse on payment of Duty (Rule-11)</span>      -->
    </div> 
  </div>
  
  <div id="certificate_header">
  <table width="100%" class="header_table" style="margin-top:0pt;border-collapse:collapse; font-family:'Times New Roman', Times, serif; font-size:10pt;" border="0">
      <tr>
	  <?php if($value['invoice_type']==4){ ?>
        <td style="width:97px" colspan="4" align="left">Patient's Name:-</td>
		<td style="width:97px" colspan="4" align="center">Doctor's Name:-</td>
<?php }?>	
        <td align="right">Original/Duplicate/Triplicate</td>
      </tr>
    </table>
    <table width="100%" class="header_table" style="margin-top:0pt; border:1pt solid #000; border-collapse:collapse; font-family:'Times New Roman', Times, serif; font-size:10pt;" border="1">
      <tr>
        <td valign="top" colspan="7" style="width:50%;">
          <!-- table to tin no detail starts here -->
          <table border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
            <tr>
                <td colspan="3"><strong><?php echo $companyname; ?></strong></td>
            </tr>
            <tr>
                <td colspan="3">(AUTH. DIST. OF <?php echo 'Baidyanath Jhansi Pvt. Ltd.'; ?>)</td>
            </tr>
            <tr>
                <td colspan="3" style="">
                   <?php echo $company_adr; ?> <br>
                   <strong> PHONE NO.-: </strong><?php echo $phone; ?>
                </td>
            </tr>
			<tr>
              <td><strong>GSTIN. No.: </strong><?php echo $dealer_data['tin_no']; ?></td>
              </tr>
			  <tr>
              <td><strong>Drug License No.: </strong><?php echo $dealer_data['drug_lic_no']; ?></td>
              </tr>
			  <tr>
              <td><strong>PAN No.: </strong><?php echo $dealer_data['pan_no']; ?></td>
              </tr>
              <tr><td></td>
              <!--<td><strong>LANDLINE NO.:- </strong><?php echo $dealer_data['landline']; ?></td>-->
              </tr>
            
          </table>
          <!-- table to tin no detail ends here -->
        </td>
        <td valign="top" colspan="8">
          <!-- table to Tarrif heading detail starts here -->
          <table border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
            <tr>
              <td><strong>BILL TO</strong> </td>
<!--        <td align="right"><strong>Authorised Signatory</strong> </td>-->
              <td valign="top"></td>
            </tr>
            <tr>
              <td colspan="8"><strong>INV. No.: </strong><?php echo $value['ch_no']; ?> <br> <strong>Date : </strong><?php echo date('d-m-Y',strtotime($value['ch_date'])); ?> </td>
              <td></td>
            </tr>
			<tr>
              <td colspan="8"><strong>E-WAY Bill No.: </strong><br> <strong>Date : </strong> </td>
              <td></td>
            </tr>
            <tr>
                <td colspan="2"><strong> Retailer: <?php echo $value['retailer_id']; ?></strong><br/><strong>Address: </strong>
                <?php echo $value['adr']['address']; ?> <?php echo $value['adr']['pin_no']; ?>
                </td>
            </tr>
            
            <tr>
                <td colspan="2"><strong> PH No: </strong><?php echo $value['adr']['landline'];?> / <?php echo $value['adr']['other_numbers'];?>
                </td>
            </tr>
             <tr>
                 <td colspan="2"><strong>GSTIN.-:</strong> <?php  echo strtoupper($value['adr']['tin_no']); ?></td> 
            </tr>
			<tr>
                <td colspan="2"><strong> Salesman: </strong>
                <?php
                    $u_q = "SELECT CONCAT_WS(' ',p.first_name,p.last_name, '[',r.rolename,']-MOB:',p.mobile) as uname FROM person p INNER JOIN _role r ON p.role_id=r.role_id  WHERE p.id = '" . $value[ch_user_id]. "'";
                     //h1($u_q);
                    $user = mysqli_query($dbc,$u_q);
                    $user = mysqli_fetch_assoc($user);

                    echo ($user['uname']!='')?$user['uname']:''; ?>
                </td>
            </tr>
          </table>
          <!-- table to Tarrif heading detail ends here -->
        </td>
<td valign="top" colspan="8">
          <!-- table to Tarrif heading detail starts here -->
          <table border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
            <tr>
              <td><strong>SHIP TO</strong> </td>
<!--        <td align="right"><strong>Authorised Signatory</strong> </td>-->
              <td valign="top"></td>
            </tr>
            <tr>
              <td colspan="8"><strong>INV. No.: </strong><?php echo $value['ch_no']; ?> <br> <strong>Date : </strong><?php echo date('d-m-Y',strtotime($value['ch_date'])); ?> </td>
              <td></td>
            </tr>
			<tr>
              <td colspan="8"><strong>E-WAY Bill No.: </strong><br> <strong>Date : </strong> </td>
              <td></td>
            </tr>
            <tr>
                <td colspan="2"><strong> Retailer: <?php echo $value['retailer_id']; ?></strong><br/><strong>Address: </strong>
                <?php echo $value['adr']['address']; ?> <?php echo $value['adr']['pin_no']; ?>
                </td>
            </tr>
            
            <tr>
                <td colspan="2"><strong> PH No: </strong><?php echo $value['adr']['landline'];?> / <?php echo $value['adr']['other_numbers'];?>
                </td>
            </tr>
             <tr>
                 <td colspan="2"><strong>GSTIN.-:</strong> <?php  echo strtoupper($value['adr']['tin_no']); ?></td> 
            </tr>
			<tr>
            </tr>
          </table>
          <!-- table to Tarrif heading detail ends here -->
        </td>  		
      </tr>     
      <tr style="font-weight:bold;">
        <td rowspan="2" style="width:3%" align="center">S No.</td>
        <td rowspan="2" style="width:15%" align="center">Item Name</td>
        <td rowspan="2" style="width:5%" align="center">HSN Code</td>
        <td rowspan="2" style="width:5%" align="center">Batch No.</td>
        <td rowspan="2" style="width:5%" align="center">Mfg.Date</td>
        <td rowspan="2" style="width:4%" align="center">Qty</td>
		<td rowspan="2" style="width:4%" align="center">Free</td>
        <td rowspan="2" style="width:5%" align="center">M.R.P</td>
        <td rowspan="2" style="width:5%" align="center">Rate</td>
        <td rowspan="2" style="width:4%" align="center">Trade/ Sch. Disc.%</td>
        <td rowspan="2" style="width:4%" align="center">Trade/ Sch. Disc. Amt.</td>

        <td rowspan="2" style="width:4%" align="center">CD Disc.%</td>
        <td rowspan="2" style="width:4%" align="center">CD Disc. Amt.</td>
        <td rowspan="2" style="width:4%" align="center">Special Disc. Amt.</td>

        <td rowspan="2" style="width:8%" align="center">ATD Disc. %</td>
        <td rowspan="2" style="width:8%" align="center">ATD Disc. Amt.</td>
        <td rowspan="2" style="width:5%" align="center">Tax - able Amt.</td>
        <td rowspan="2" style="width:7%" align="center">GST %</td>
        <td colspan="2" style="width:5%" align="center"><?php echo $gst; ?></td>       
       <?php  if($dealer_state_id == $retailer_state_id){ ?>
        <td colspan="2" style="width:5%" align="center"><?php echo $gst1; ?></td>    
       <?php } ?>

        <td rowspan="2" style="width:15%" align="center">Total Amt.</td>
      </tr>      
      <tr style="font-weight:bold;">   
         <td style="width:5%" align="center">%</td>
        <td style="width:8%" align="center">Amt</td>
       <?php  if($dealer_state_id == $retailer_state_id){ ?>
        <td style="width:5%" align="center">%</td>
        <td style="width:8%" align="center">Amt</td>
       <?php } ?>     
      </tr>

      <?php 
          $inc =1; 
          $i=1;
          $amount = array();
          $mybarcode = '';
          $grandtotal = 0;
          $break_from = 24;

          /* Taxable amount for tax summary block */
          $taxable_amt_summery = 0;
          $taxable_tax_summery = array();

         // $surcharge=  myrowval('catalog_product_rate_list', 'surcharge', 'stateid='.$_SESSION[SESS.'data']['state_id']);
          // pre($value['challan_item']);
    foreach($value['challan_item'] as $key1=>$value1){ 
             // pre($value1);
      if(!empty($value1['lineno'])) $mybarcode .= "\t".$value1['lineno']."\t".$value1['qty'];
      if(empty($value1['dis_amt'])){
          $value1['dis_amt']=0;
      }
      if(empty($value1['cd_amt'])){
          $value1['cd_amt']=0;
      }

      $t_amt = ($value1['product_rate']*$value1['qty'])-$value1['dis_amt']-$value1['cd_amt'];
      //$t_amt= $value1['taxable_amt'];
      $item['hsn_code'] = $value1['hsn_code'];
      $item['taxable_amt'] = $value1['product_rate']*$value1['qty'];
      $item['tax'] = $value1['tax'];
      $item['tax_amt'] = $value1['vat_amt'];

      $taxable_amt_summery += $t_amt;

      if($value1['tax']>0)
      {
        $taxable_tax_summery[] = ($t_amt*$value1['tax'])/100;
      }
      
      $invoice_summary[] = $item;
       
      if($value1['cd_type']==1){
        $cd = (($value1['qty'] * $value1['product_rate'])*$value1['cd'])/100;        
        }
            elseif ($value1['cd_type']==2) {
             $cd = ($value1['cd']);        
            }    
            $taxable=$value1['product_rate']*$value1['qty'];
           //  $amt = $taxable;
             if($value1['tax']==0){$vat_amt = 0; }
             else{
           //  $vat_amt = (($amt)*$value1['tax'])/100;}
            //  h1($vat_amt);
             //$vat_amt = (($amt-$cd)*$value1['tax'])/100;   
          //   $vat_amt1 = $vat_amt - ($vat_amt*($surcharge/100));
           //  $surcharge_amt= $vat_amt - $vat_amt1;  
            // h1( $surcharge_amt);
            //h1($vat_amt1);
             }
           //$amt = $taxable -($value1['dis_amt']+$value1['cd_amt']+$value1['cash_amt']);
           $amt=$value1['taxable_amt']-$value1['vat_amt'];
           $vat_amt= $value1['vat_amt']; ?>
       <?php 

               $x = mysqli_query($dbc,"SELECT batch_no,DATE_FORMAT(`mfg`,'%b-%Y') AS mfg FROM stock WHERE dealer_id=$dealer_id AND product_id=".$value1['product_id']." AND MRP='".$value1['mrp']."' LIMIT 1");        
               $more_info = mysqli_fetch_assoc($x);


               if(($header_changer++ == $break_from)){
                 $header_changer = 0; 
                 $break_from = 28;
                 ?>
              <tr style="font-weight:bold;">
                <td rowspan="2" style="width:3%" align="center">S No.</td>
                <td rowspan="2" style="width:40%" align="center">Item Name</td>
                <td rowspan="2" style="width:5%" align="center">HSN Code</td>
				<td rowspan="2" style="width:5%" align="center">Batch No.</td>
        <td rowspan="2" style="width:5%" align="center">Mfg.Date</td>
                <td rowspan="2" style="width:5%" align="center">Qty</td>
				<td rowspan="2" style="width:5%" align="center">Free</td>
                <td rowspan="2" style="width:7%" align="center">M.R.P</td>
                <td rowspan="2" style="width:8%" align="center">Rate</td>
                <td rowspan="2" style="width:4%" align="center">Trade/ Sch. Disc.%</td>
                <td rowspan="2" style="width:4%" align="center">Trade/ Sch. Disc. Amt.</td>
               <td rowspan="2" style="width:4%" align="center">CD Disc.%</td>
        <td rowspan="2" style="width:4%" align="center">CD Disc. Amt.</td>
        <td rowspan="2" style="width:4%" align="center">Special Disc. Amt.</td>

        <td rowspan="2" style="width:8%" align="center">ATD Disc. %</td>
        <td rowspan="2" style="width:8%" align="center">ATD Disc. Amt.</td>
        <td rowspan="2" style="width:5%" align="center">Tax - able Amt.</td>
        <td rowspan="2" style="width:7%" align="center">GST %</td>
                <td colspan="2" style="width:5%" align="center"><?php echo $gst; ?></td>       
               <?php  if($dealer_state_id == $retailer_state_id){ ?>
                <td colspan="2" style="width:5%" align="center"><?php echo $gst1; ?></td>    
               <?php } ?>

                <td rowspan="2" style="width:15%" align="center">Total Amt.</td>
              </tr>
              <tr style="font-weight:bold;">   
                 <td style="width:5%" align="center">%</td>
                <td style="width:8%" align="center">Amt</td>
               <?php  if($dealer_state_id == $retailer_state_id){ ?>
                <td style="width:5%" align="center">%</td>
                <td style="width:8%" align="center">Amt</td>
               <?php } ?>     
              </tr>
              <?php } ?>
       <tr>
        <td style="border-bottom:none;border-top:none;height:35px;"><?php echo $i;?></td>
<!--        <td style="border-bottom:none;border-top:none;height:35px;"> <?php echo $value1['comunity_code'];?></td>-->
        <td style="border-bottom:none;border-top:none;height:35px;"> <?php echo $value1['name'];?></td>
        <td style="border-bottom:none;border-top:none;height:35px;"> <?php echo $value1['hsn_code'];?></td>
        <td style="border-bottom:none;border-top:none;height:35px;"><?php echo $more_info['batch_no'] ?></td>
        <td style="border-bottom:none;border-top:none;height:35px;"><?php echo $more_info['mfg'] ?></td>
        <td style="border-bottom:none;border-top:none;height:35px;" align="center"> <?php echo $value1['qty'];?></td>
       <td style="border-bottom:none;border-top:none;height:35px;" align="center"> <?php echo $value1['free_qty'];?></td>
        <td style="border-bottom:none;border-top:none;height:35px;" align="right"><?php echo my2digit($value1['mrp']);?></td>
        <td style="border-bottom:none;border-top:none;height:35px;" align="right"> <?php echo my2digit($value1['product_rate']);?></td>
        <td style="border-bottom:none;border-top:none;height:35px;" align="right"> <?php echo my2digit($value1['dis_percent']);?></td>
        <td style="border-bottom:none;border-top:none;height:35px;" align="right"> <?php echo my2digit($value1['dis_amt']);?></td>

        <td style="border-bottom:none;border-top:none;height:35px;" align="right"> <?php echo my2digit($value1['spl_disc_val']);?></td>
        <td style="border-bottom:none;border-top:none;height:35px;" align="right"> <?php echo my2digit($value1['spl_disc_amt']);?></td>
        <td style="border-bottom:none;border-top:none;height:35px;" align="right"> <?php echo my2digit($value1['cash_amt']);?></td>

        <td style="border-bottom:none;border-top:none;height:35px;" align="right"> <?php echo my2digit($value1['cd']);?></td>
        <td style="border-bottom:none;border-top:none;height:35px;" align="right"> <?php echo my2digit($value1['cd_amt']);?></td>
        <td style="border-bottom:none;border-top:none;height:35px;" align="right"> <?php echo my2digit($amt);?></td>
        <td style="border-bottom:none;border-top:none;height:35px;" align="right"> <?php echo my2digit($value1['tax']).'%';?></td>        
       <?php  if($dealer_state_id == $retailer_state_id){ ?>
        <td style="border-bottom:none;border-top:none;height:35px;" align="right"> <?php echo ($value1['tax']/2).'%' ;?></td>
        <td style="border-bottom:none;border-top:none;height:35px;" align="right"> <?php echo my2digit($vat_amt/2) ;?></td>          
        <td style="border-bottom:none;border-top:none;height:35px;" align="right"> <?php echo ($value1['tax']/2).'%' ;?></td>
        <td style="border-bottom:none;border-top:none;height:35px;" align="right"> <?php echo my2digit($vat_amt/2) ;?></td>
        <?php }else{ ?>
        <td style="border-bottom:none;border-top:none;height:35px;" align="right"> <?php echo ($value1['tax']) ;?></td>
        <td style="border-bottom:none;border-top:none;height:35px;" align="right"> <?php echo my2digit($vat_amt) ;?></td>
        <?php } ?>
<!--        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($surcharge);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($surcharge);?></td>-->
  <?php $net_amt = $value1['taxable_amt']; ?>
        <td style="border-bottom:none;border-top:none;" align="right"> <?php echo " ".my2digit($net_amt) ;?></td>
      </tr>
      
    <?php
          $srchrg = (trim($value1['tax'])==0)?'0':$surcharge;
          $summary_vat[$value1['tax']."+".$srchrg]['amount']+=my2digit($taxable);
          $summary_vat[$value1['tax']."+".$srchrg]['tax']+=my2digit($vat_amt);
          $summary_vat[$value1['tax']."+".$srchrg]['surcharge']+=my2digit($surcharge_amt);
          $summary_vat[$value1['tax']."+".$srchrg]['cd_amt']+=my2digit($value1['cd_amt']);
        //  $summary_vat[$value1['tax']]['total']+=$summary_vat[$value1['tax']]['amount']+$summary_vat[$value1['tax']]['tax']+$summary_vat[$value1['tax']]['surcharge'];
          $dis_amt += $value1['dis_amt'];
          $spl_amt += $value1['spl_disc_amt'];
          $gst_per += $value1['tax'];
          $cd_per += $value1['cd'];
          $cd_amt+=$value1['cd_amt'];
          $cash_amt+=$value1['cash_amt'];
          $taxable_amt+=$amt;
          $ttl_surcharge_amt+= $surcharge_amt;
          $ttl_vat_amt+=$vat_amt;
          //$grandcd += $cd;
          $grandtotal += $net_amt;
          $ttcgst+=$value1['vat_amt']/2;
          $ttsgst+=$value1['vat_amt']/2;
          $ttigst+=$value1['vat_amt'];
          $tnet_amt+=$net_amt;
          //$grandvat += $vat_amt ;
         // h1($grandtotal);
          $inc++; 
          $i++;
         
          
             }// foreach($value['po_item'] as $key1=>$value1){ ends?>
       <?php if($dealer_state_id == $retailer_state_id){ $col = '2'; $col1 = '2'; $col2 = '20'; $col3 = '3'; }else{
          $col = '1'; $col1 = '1'; $col2 = '15'; $col3 = '3'; 
      } ?>
            
        <tr>
          <th></th>
          <th>Total</th>
          <th colspan="8"></th>
          <th><?php echo my2digit($dis_amt); ?></th>
          <th></th>
          <th><?php echo my2digit($spl_amt); ?></th>
          <th><?php echo my2digit($cash_amt); ?></th>
          <th></th>
          <th><?php echo my2digit($cd_amt); ?></th>
          <th><?php echo my2digit($taxable_amt); ?></th>
          <th colspan="2"></th>
             <?php  
        if($dealer_state_id == $retailer_state_id){?>
        <th> <?php echo my2digit($ttcgst) ;?></th>          
        <th></th>
        <th> <?php echo my2digit($ttsgst) ;?></th>
        <?php }else{ ?>
        <th></th>
        <th> <?php echo my2digit($ttigst) ;?></th>
        <?php } ?>
          <th><?php echo my2digit($tnet_amt); ?></th>
        </tr>      
      <tr style="border-left:0px;">
          <td colspan="18" align="Left" style="border-right:0px;">
		  <table width="100%" border="0">
		  <tr>
		  <td>
		  <tr>
		  <td colspan="3"><b>GST Summary:</b><td>
		  </tr>
		  <tr>
		  <td><b>GST%</b></td>
		   <td>0%</td>
       <td>3%</td>
		   <td>5%</td>
		   <td>12%</td>
		   <td>18%</td>
		   <td>28%</td>
		   <td>Total</td>
		  </tr>
      <?php 
      $tgst=0;
      $ttm=0;
        $gstq0="SELECT sum(vat_amt) AS tax_sum,sum(taxable_amt-vat_amt) AS taxable_amt FROM challan_order_details WHERE ch_id='$_GET[id]' AND tax=0.00";
        $rgst0=mysqli_query($dbc,$gstq0);
        $rungst0=mysqli_fetch_assoc($rgst0);
        if(!empty($rungst0[tax_sum])){
          $gst_amt_sum0=$rungst0[tax_sum];
        }else{
          $gst_amt_sum0=0.00;
        }

        $tgst=$tgst+$gst_amt_sum0;
        if(!empty($rungst0[taxable_amt])){
          $tax_amt_sum0=$rungst0[taxable_amt];
        }else{
          $tax_amt_sum0=0.00;
        }
        $ttm=$ttm+$tax_amt_sum0;
        ?>
        <?php 
        $gstq3="SELECT sum(vat_amt) AS tax_sum,sum(taxable_amt-vat_amt) AS taxable_amt FROM challan_order_details  WHERE ch_id='$_GET[id]' AND tax=3.00";
        $rgst3=mysqli_query($dbc,$gstq3);
        $rungst3=mysqli_fetch_assoc($rgst3);
        if(!empty($rungst3[tax_sum])){
          $gst_amt_sum3=$rungst3[tax_sum];
        }else{
          $gst_amt_sum3=0.00;
        }
        $tgst=$tgst+$gst_amt_sum3;
        if(!empty($rungst3[taxable_amt])){
          $tax_amt_sum3=$rungst3[taxable_amt];
        }else{
          $tax_amt_sum3=0.00;
        }
        $ttm=$ttm+$tax_amt_sum3;
        ?>
        <?php 
        $gstq5="SELECT sum(vat_amt) AS tax_sum,sum(taxable_amt-vat_amt) AS taxable_amt FROM challan_order_details  WHERE ch_id='$_GET[id]' AND tax=5.00";
        $rgst5=mysqli_query($dbc,$gstq5);
        $rungst5=mysqli_fetch_assoc($rgst5);
        if(!empty($rungst5[tax_sum])){
          $gst_amt_sum5=$rungst5[tax_sum];
        }else{
          $gst_amt_sum5=0.00;
        }
        $tgst=$tgst+$gst_amt_sum5;
        if(!empty($rungst5[taxable_amt])){
          $tax_amt_sum5=$rungst5[taxable_amt];
        }else{
          $tax_amt_sum5=0.00;
        }
        $ttm=$ttm+$tax_amt_sum5;
        ?>
        <?php 
        $gstq12="SELECT sum(vat_amt) AS tax_sum,sum(taxable_amt-vat_amt) AS taxable_amt FROM challan_order_details  WHERE ch_id='$_GET[id]' AND tax=12.00";
        $rgst12=mysqli_query($dbc,$gstq12);
        $rungst12=mysqli_fetch_assoc($rgst12);
        if(!empty($rungst12[tax_sum])){
          $gst_amt_sum12=$rungst12[tax_sum];
        }else{
          $gst_amt_sum12=0.00;
        }
        $tgst=$tgst+$gst_amt_sum12;
        if(!empty($rungst12[taxable_amt])){
          $tax_amt_sum12=$rungst12[taxable_amt];
        }else{
          $tax_amt_sum12=0.00;
        }
        $ttm=$ttm+$tax_amt_sum12;
        ?>
        <?php 
        $gstq18="SELECT sum(vat_amt) AS tax_sum,sum(taxable_amt-vat_amt) AS taxable_amt FROM challan_order_details  WHERE ch_id='$_GET[id]' AND tax=18.00";
        $rgst18=mysqli_query($dbc,$gstq18);
        $rungst18=mysqli_fetch_assoc($rgst18);
        if(!empty($rungst18[tax_sum])){
          $gst_amt_sum18=$rungst18[tax_sum];
        }else{
          $gst_amt_sum18=0.00;
        }
        $tgst=$tgst+$gst_amt_sum18;
        if(!empty($rungst18[taxable_amt])){
          $tax_amt_sum18=$rungst18[taxable_amt];
        }else{
          $tax_amt_sum18=0.00;
        }
        $ttm=$ttm+$tax_amt_sum18;
        ?>
        <?php 
        $gstq28="SELECT sum(vat_amt) AS tax_sum,sum(taxable_amt-vat_amt) AS taxable_amt FROM challan_order_details  WHERE ch_id='$_GET[id]' AND tax=28.00";
        $rgst28=mysqli_query($dbc,$gstq28);
        $rungst28=mysqli_fetch_assoc($rgst28);
        if(!empty($rungst28[tax_sum])){
          $gst_amt_sum28=$rungst28[tax_sum];
        }else{
          $gst_amt_sum28=0.00;
        }
        $tgst=$tgst+$gst_amt_sum28;
        if(!empty($rungst28[taxable_amt])){
          $tax_amt_sum28=$rungst28[taxable_amt];
        }else{
          $tax_amt_sum28=0.00;
        }
        $ttm=$ttm+$tax_amt_sum28;
        ?>
        <tr>
      <td><b>Taxable Amt.</b></td>
        <td><?=my2digit($tax_amt_sum0,2);?></td>
        <td><?=my2digit($tax_amt_sum3,2);?></td>
        <td><?=my2digit($tax_amt_sum5,2);?></td>
        <td><?=my2digit($tax_amt_sum12,2);?></td>
        <td><?=my2digit($tax_amt_sum18,2);?></td>
        <td><?=my2digit($tax_amt_sum28,2);?></td>
        <td><?=my2digit($ttm,2);?></td>
      </tr>
		  <tr>
		  <td><b>GST Amt.</b></td>
        <td><?=my2digit($gst_amt_sum0,2);?></td>
        <td><?=my2digit($gst_amt_sum3,2);?></td>
        <td><?=my2digit($gst_amt_sum5,2);?></td>
        <td><?=my2digit($gst_amt_sum12,2);?></td>
        <td><?=my2digit($gst_amt_sum18,2);?></td>
				<td><?=my2digit($gst_amt_sum28,2);?></td>
				<td><?=my2digit($tgst,2);?></td>
		  </tr>
		  </td>
		  </tr>   
		</table>		  
          </td> 
<td colspan="5">
<!--           <td colspan="5">
               <table width="100%" border="0">
                     PUNEET -->

                  <?php 
                    $gst_rate = '0.0%';                    
                    $gst_val = '0.00';
                    $gst_total_val = 0;
                    if(count($taxable_tax_summery)>0)
                    {
                        $gst_rate = '2.5%';
                        $gst_total_val = array_sum($taxable_tax_summery);
                        $gst_val = $gst_total_val/2;
                    }
                  ?>

              <!--      <tr>
                      <th>SUB TOTAL</th>
                      <th align="left"><?php echo $taxable_amt_summery ?></th>
                    </tr>
                    <tr>
                      <td align="center">SGST <?php echo $gst_rate ?></td>
                      <td><?php echo my2digit($gst_val) ?></td>
                    </tr>
                    <tr>
                      <td align="center">CGST <?php echo $gst_rate ?></td>
                      <td><?php echo my2digit($gst_val) ?></td>
                    </tr>     -->              

                    <?php 

                       if(count($invoice_summary))
                       {
                         $taxableamt = 0;
                         foreach($invoice_summary as $itm)
                         { 
                           $taxableamt += $itm['taxable_amt'];
                           ?>
                            
                   <?php } ?>
                            
                   <?php }  ?>

                <!--    /*PUNEET*/ -->
                   
                   <?php                     
                   
                   foreach($value['challan_hsn_dtl'] as $key1=>$value2){  ?>
                    <tr>                       
                       <td colspan="2" align="center"><?php echo $value2['hsn_code']; ?></td>
                       <td colspan="2" align="center"><?php echo $value2['taxable_amt']; ?></td>
                       <?php  if($dealer_state_id == $retailer_state_id){ ?>
                       <td colspan="2" align="center"><?php echo ($value2['gst_tax']/2); ?></td>
                       <td colspan="2" align="center"><?php echo my2digit($value2['gst_amt']/2); ?></td> 
                       <td colspan="2" align="center"><?php echo ($value2['gst_tax']/2); ?></td>
                       <td colspan="2" align="center"><?php echo my2digit($value2['gst_amt']/2); ?></td> 
                       <?php } else { ?>
                        <td colspan="2" align="center"><?php echo ($value2['gst_tax']); ?></td>
                       <td colspan="2" align="center"><?php echo my2digit($value2['gst_amt']); ?></td> 
                       <?php } ?>
                   </tr> 
                   <?php 
                    $taxableamt += ($value2['taxable_amt']);
                    if($dealer_state_id == $retailer_state_id){ 
                     $gstper += ($value2['gst_tax']/2); 
                     $vatamt += ($value2['gst_amt']/2);
                    }else{
                        $gstper += ($value2['gst_tax']); 
                        $vatamt += ($value2['gst_amt']);
                    }
                   
                   } ?>
                   
                <!--   <tr>
                     <th align="center">TOTAL</th>
                     <th align="left">-->
                       <?php 
                       $total = round($gst_total_val+$taxable_amt_summery,2);
                   //  echo $total?>
                <!-- </th>  </tr>
                   
               </table>-->

           </td>
         
        </tr>
        <tr style="font-size:11pt;">
          <td colspan="<?php echo $col2; ?>" style="border-right:0px;"><b> TOTAL AMOUNT </b><!--ONE LAC SIX THOUSAND FOUR HUNDRED TWENTY FIVE ONLY.--></td>
          <td colspan="<?php echo $col3; ?>" style="border-left:0px;"> <span style="float:right"><b><?php echo price_to_words(round($grandtotal,2));?></b></span></td>

        </tr>
        <tr style="font-size:11pt;">
          <td colspan="<?php echo $col2; ?>" style="border-right:0px;"><b>DISCOUNT &nbsp; <?=$value['discount_per']?>% &nbsp; ON TOTAL AMOUNT </b><!--ONE LAC SIX THOUSAND FOUR HUNDRED TWENTY FIVE ONLY.--></td>
          <td colspan="<?php echo $col3; ?>" style="border-left:0px;"> <span style="float:right"><b><?php echo price_to_words(round($value['discount_amt']));?></b></span></td>

        </tr>
        <tr style="font-size:11pt;">
         <td colspan="<?php echo $col2; ?>" style="border-right:0px;"><b>ROUND OFF AMOUNT</b><!--ONE LAC SIX THOUSAND FOUR HUNDRED TWENTY FIVE ONLY.--></td>
         <?php 
         $v1 = round($value['amount']);
         $v2 = $value['amount'];
         $v3 = $v1-$v2;
         $v4 = round($v3,2);
         $v5 = abs($v4);

         ?>
         <td colspan="<?php echo $col3; ?>" style="border-left:0px;"> <span style="float:right"><b><?php echo $v5;?></b></span></td>
       </tr>

        <tr style="font-size:11pt;">
         <td colspan="<?php echo $col2; ?>" style="border-right:0px;"><b>GRAND TOTAL </b><!--ONE LAC SIX THOUSAND FOUR HUNDRED TWENTY FIVE ONLY.--></td>
         <td colspan="<?php echo $col3; ?>" style="border-left:0px;"> <span style="float:right"><b><?php echo price_to_words(round($value['amount']));?></b></span></td>

       </tr>  
        
        <tr style="font-size:11pt;">
         <td colspan="23">Amount of Invoice (in Words) Rupees. <b><?php echo strtoupper(price_to_words($value['amount'],2));?></b><!--ONE LAC SIX THOUSAND FOUR HUNDRED TWENTY FIVE ONLY.--></td>
       </tr>
       <tr>
         <td valign="top" colspan="13" style="border:none;">
           <span style="font-size:9pt;">
                        <strong>Terms & Conditions:</strong><br>
                    <?php if(!empty($dealer_data['terms'])) echo "<pre>".$dealer_data[terms]."</pre>"; else echo "";?></span>
         </td>
         <td colspan="10" align="right" style="border-left:none;">
           <b>For : <?php echo $dealer_data['name']; ?></b><br /><br /><br />Authorised Signatory
         </td>
       </tr>
    </table>
  </div>
</div><!-- #certificate_conatiner ends -->
<div style="page-break-after:always;page-break-inside:avoid;width: 90%;"></div>
<?php }// foreach($looper as $key=>$value){ ends?>
</div>  
    
</div>
<!-- END NORMAL PAGE -->


































<!------------------------------START A4 8 SHEET PAGE------------------------------------------->
<div id="a48" style="display: none">
<table width="100%">
  <tr>
    <td>
      <div class="subhead1"><!-- this portion indicate the print options -->
        <a href="javascript:printing('searchlistdiv1');" title="take a printout" style="margin:0 10px;"><img src="./icons/print.png" /></a>
        Print Invoice 1
      </div>
    </td>
  </tr> 
</table>
<div id="searchlistdiv1" style="page-break-inside: avoid;">
<?php foreach($looper as $key=>$value){
    $retailer_state_id = $value['adr']['state_id'];
    $user_id = $value['ch_user_id'];
  
 if($dealer_state_id == $retailer_state_id){
      $gst = 'CGST';
      $gst1 = 'SGST';
  }else{
      $gst = 'IGST';
  }
   $ch_no = $value['ch_no'];
//   h1($ch_no);
//this loop will help in the printing of the multiple bills?>
<div id="certificate_container" style="padding:0px; font-size:62.5%; font-family:'Times New Roman', Times, serif; page-break-after:auto; margin:0 auto; width:595pt;">
  
  <div id="pritn_what">
    <div class="mytitle" style="font-size:14pt;text-align:center; font-weight:bold; margin-top:0;">
      <span style="text-decoration:underlinel;"><?php echo ($value['adr']['tin_no']!='') ? "TAX ":"TAX ";?>INVOICE</span><br />
<!--      <span style="font-size:9pt; font-weight:normal; letter-spacing:-0.5pt;">Invoice for removal of Excisable Goods from a factory or warehouse on payment of Duty (Rule-11)</span>      -->
    </div> 
  </div>
  
  <div id="certificate_header">
    <table width="100%" class="header_table" style="margin-top:0pt; border:1pt solid #000; border-collapse:collapse; font-family:'Times New Roman', Times, serif; font-size:10pt;" border="1">
      <tr>
        <td valign="top" colspan="8" style="width:50%;">
          <!-- table to tin no detail starts here -->
          <table border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
            <tr>
                <td colspan="3"><strong><?php echo $companyname; ?></strong></td>
            </tr>
            <tr>
                <td colspan="3">(AUTH. DIST. OF <?php echo 'Baidyanath Jhansi Pvt. Ltd.'; ?>)</td>
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
        <td valign="top" colspan="9">
          <!-- table to Tarrif heading detail starts here -->
          <table border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
            <tr>
              <td><strong>TO</strong> </td>
<!--        <td align="right"><strong>Authorised Signatory</strong> </td>-->
              <td valign="top"></td>
            </tr>
            <tr>
              <td colspan="8"><strong>INV. No.: </strong><?php echo $value['ch_no']; ?> <br> Date : <?php echo date('d-m-Y',strtotime($value['ch_date'])); ?> </td>
              <td></td>
            </tr>
            <tr>
                <td colspan="2"><strong> Retailer: </strong><?php echo $value['retailer_id']; ?><br/><strong>Address: </strong>
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
        <td rowspan="2" style="width:3%" align="center">S No.</td>
        <td rowspan="2" style="width:40%" align="center">Item Name</td>
        <td rowspan="2" style="width:5%" align="center">HSN Code</td>
        <td rowspan="2" style="width:5%" align="center">Qty</td>  
        <td rowspan="2" style="width:7%" align="center">M.R.P</td>
        <td rowspan="2" style="width:8%" align="center">Rate</td>
        <td rowspan="2" style="width:4%" align="center">Trade/ Sch. Disc.%</td>
        <td rowspan="2" style="width:4%" align="center">Trade/ Sch. Disc. Amt.</td>
        <td rowspan="2" style="width:8%" align="center">Other Disc. %</td>
        <td rowspan="2" style="width:8%" align="center">Other Disc. Amt.</td>
        <td rowspan="2" style="width:5%" align="center">Tax - able Amt.</td>
        <td rowspan="2" style="width:7%" align="center">GST %</td>
        <td colspan="2" style="width:5%" align="center"><?php echo $gst; ?></td>       
       <?php  if($dealer_state_id == $retailer_state_id){ ?>
        <td colspan="2" style="width:5%" align="center"><?php echo $gst1; ?></td>      
       <?php } ?>
        <td rowspan="2" style="width:15%" align="center">Total Amt.</td>
      </tr>
      
      <tr style="font-weight:bold;">   
         <td style="width:5%" align="center">%</td>
        <td style="width:8%" align="center">Amt</td>
       <?php  if($dealer_state_id == $retailer_state_id){ ?>
        <td style="width:5%" align="center">%</td>
        <td style="width:8%" align="center">Amt</td>
       <?php } ?>     
      </tr>
      <?php 
    $inc1 =1; 
          $i1=1;
    $amount1 = array();
    $mybarcode1 = '';
          $grandtotal1 = 0;
          $surcharge1=  myrowval('catalog_product_rate_list', 'surcharge', 'stateid='.$_SESSION[SESS.'data']['state_id']);
         // print_r($surcharge);
    foreach($value['challan_item'] as $key1=>$value1){ 
      if(!empty($value1['lineno'])) $mybarcode1 .= "\t".$value1['lineno']."\t".$value1['qty'];
                
                 if($value1['cd_type']==1){
        $cd1 = (($value1['qty'] * $value1['product_rate'])*$value1['cd'])/100;        
        }
elseif ($value1['cd_type']==2) {
 $cd1 = ($value1['cd']);        
}    
            $taxable1=$value1['product_rate']*$value1['qty'];
           //  $amt1 = $taxable1;
             if($value1['tax']==0){$vat_amt1 = 0; }
             else{
            // $vat_amt1 = (($amt1)*$value1['tax'])/100;}
            //  h1($vat_amt);
             //$vat_amt = (($amt-$cd)*$value1['tax'])/100;   
            // $vat_amt1 = $vat_amt1 - ($vat_amt1*($surcharge1/100));
            // $surcharge_amt1= $vat_amt1 - $vat_amt1;  
            // h1( $surcharge_amt);
            //h1($vat_amt1);
               $amt1 = $taxable1 -($value1['dis_amt']+$value1['cd_amt']);
              $vat_amt1 = $value1['vat_amt'];
             }
    ?>
       <tr>
        <td style="border-bottom:none;border-top:none;height:36px;"> <?php echo $i1;?></td>
        <!--<td style="border-bottom:none;border-top:none;height:36px;"> <?php echo $value1['comunity_code'];?></td>-->
        <td style="border-bottom:none;border-top:none;height:10px;"> <?php echo $value1['name'];?></td>
         <td style="border-bottom:none;border-top:none;height:10px;"> <?php echo $value1['hsn_code'];?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo $value1['qty'];?></td>
<!--        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo $value1['free_qty'];?></td>-->
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"><?php echo my2digit($value1['mrp']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['product_rate']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['dis_percent']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['dis_amt']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['cd']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['cd_amt']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($amt1);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['tax']).'';?></td>
        <?php  if($dealer_state_id == $retailer_state_id){ ?>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo ($value1['tax']/2) ;?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($vat_amt/2) ;?></td>
         
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo ($value1['tax']/2) ;?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($vat_amt/2) ;?></td>
       <?php }else{ ?>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo ($value1['tax']) ;?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($vat_amt) ;?></td>
        <?php } ?>
<!--        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($surcharge1);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($surcharge_amt1);?></td>-->
  <?php  
        
          
            
           
            $net_amt1 = $value1['taxable_amt'];
        ?>
        <td style="border-bottom:none;border-top:none;" align="right"> <?php echo my2digit($net_amt1) ;?></td>
      </tr>
    <?php
          $srchrg1 = (trim($value1['tax'])==0)?'0':$surcharge1;
          $summary_vat1[$value1['tax']."+".$srchrg1]['amount']+=my2digit($taxable1);
          $summary_vat1[$value1['tax']."+".$srchrg1]['tax']+=my2digit($vat_amt1);
          $summary_vat1[$value1['tax']."+".$srchrg1]['surcharge']+=my2digit($surcharge_amt1);
          $summary_vat1[$value1['tax']."+".$srchrg1]['cd_amt']+=my2digit($value1['cd_amt']);
        //  $summary_vat[$value1['tax']]['total']+=$summary_vat[$value1['tax']]['amount']+$summary_vat[$value1['tax']]['tax']+$summary_vat[$value1['tax']]['surcharge'];
          $dis_amt1 += $value1['dis_amt'];
          $gst_per += $value1['tax'];
          $cd_amt1 +=$value1['cd_amt'];
          $taxable_amt1 +=$amt1;
          $ttl_surcharge_amt1 += $surcharge_amt1;
          $ttl_vat_amt1 +=$vat_amt1;
          //$grandcd += $cd;
          $grandtotal1 += $net_amt1;
          //$grandvat += $vat_amt ;
         // h1($grandtotal);
          $inc1++; 
          $i1++;
                   
             }// foreach($value['po_item'] as $key1=>$value1){ ends
       for($k=$inc;$k<8;$k++){
   ?>
         
      <?php }  if($dealer_state_id == $retailer_state_id){ $col = '2'; $col1 = '2'; $col2 = '15'; $col3 = '2'; }else{
          $col = '1'; $col1 = '1'; $col2 = '13'; $col3 = '2'; 
      } ?>
      <tr style="font-size:11pt;">
        
        <td colspan="7"><strong>TOTAL AMOUNT</strong></td>
       
        <td colspan="1" align="right"><strong><?php echo my2digit($dis_amt1); ?><!--106425.00--></strong></td>
        <td colspan="2" align="right"><strong><?php echo my2digit($cd_amt1); ?><!--106425.00--></strong></td>
        <td colspan="1" align="right"><strong><?php echo my2digit($taxable_amt1); ?><!--106425.00--></strong></td>
          <td colspan="1" align="right"><strong><?php// echo my2digit($gst_per); ?><!--106425.00--></strong></td>
       
        <td colspan="<?php echo $col; ?>" align="right"><strong><?php echo my2digit($ttl_vat_amt1/2); ?><!--106425.00--></strong></td>
        <td colspan="<?php echo $col1; ?>" align="right"><strong><?php echo my2digit($ttl_vat_amt1/2); ?><!--106425.00--></strong></td>
        

        
        <td colspan="1" align="right"><strong><?php echo $grandtotal1; ?><!--106425.00--></strong></td>
      </tr>
     
        <tr style="font-size:11pt;">
            <td colspan="<?php echo $col2; ?>"><b>DISCOUNT &nbsp; <?=$value['discount_per']?>% &nbsp; ON TOTAL AMOUNT </b><!--ONE LAC SIX THOUSAND FOUR HUNDRED TWENTY FIVE ONLY.--></td>
            <td colspan="<?php echo $col3; ?>"> <span style="float:right"><b><?php echo price_to_words(round($value['discount_amt']));?></b></span></td>
            
        </tr>
        <tr style="font-size:11pt;">
           <td colspan="<?php echo $col2; ?>"><b>GRAND TOTAL </b><!--ONE LAC SIX THOUSAND FOUR HUNDRED TWENTY FIVE ONLY.--></td>
        <td colspan="<?php echo $col3; ?>"> <span style="float:right"><b><?php echo price_to_words(round($value['amount']));?></b></span></td>
           
       </tr>
 
      <tr style="font-size:11pt;">
         <td colspan="17">Amount of Invoice (in Words) Rupees. <b><?php echo strtoupper(price_to_words($value['amount'],2));?></b><!--ONE LAC SIX THOUSAND FOUR HUNDRED TWENTY FIVE ONLY.--></td>
        </tr>

       <tr>
            
           <td colspan="6" >
               <table width="100%" border="0">
                   <tr>
                       <th colspan="2">HSN Code</th>
                       <th colspan="2">Taxable Amt.</th>
                       <th colspan="2"><?php echo $gst; ?>  %</th>
                       <th colspan="2"><?php echo $gst; ?>  Amt.</th>
                        <?php  if($dealer_state_id == $retailer_state_id){ ?>
                        <th colspan="2"><?php echo $gst1; ?> %</th>
                       <th colspan="2"><?php echo $gst1; ?> Amt.</th>
                         <?php } ?>
                   </tr>
                   <?php                     
                  // pre($value);
                   
                   foreach($value['challan_hsn_dtl'] as $key1=>$value2){  //pre($value2); ?>
                   <tr>                       
                       <td colspan="2" align="center"><?php echo $value2['hsn_code']; ?></td>
                       <td colspan="2" align="center"><?php echo $value2['taxable_amt']; ?></td>
                       <?php  if($dealer_state_id == $retailer_state_id){ ?>
                       <td colspan="2" align="center"><?php echo ($value2['gst_tax']/2); ?></td>
                       <td colspan="2" align="center"><?php echo my2digit($value2['gst_amt']/2); ?></td> 
                       <td colspan="2" align="center"><?php echo ($value2['gst_tax']/2); ?></td>
                       <td colspan="2" align="center"><?php echo my2digit($value2['gst_amt']/2); ?></td> 
                       <?php } else { ?>
                        <td colspan="2" align="center"><?php echo ($value2['gst_tax']); ?></td>
                       <td colspan="2" align="center"><?php echo my2digit($value2['gst_amt']); ?></td> 
                       <?php } ?>
                   </tr>
                   <?php 
                    $taxableamt += ($value2['taxable_amt']);
                    if($dealer_state_id == $retailer_state_id){ 
                     $gstper += ($value2['gst_tax']/2); 
                     $vatamt += ($value2['gst_amt']/2);
                    }else{
                        $gstper += ($value2['gst_tax']); 
                        $vatamt += ($value2['gst_amt']);
                    }
                   
                   } ?>
                   <tr>                       
                       <td colspan="2" align="center"><b>Total</b></td>
                       <td colspan="2" align="center"><?php echo $taxableamt; ?></td>
                       <td colspan="2" align="center"><?php echo $gstper; ?></td>
                       <td colspan="2" align="center"><?php echo my2digit($vatamt); ?></td> 
                        <?php  if($dealer_state_id == $retailer_state_id){ ?>
                       <td colspan="2" align="center"><?php echo $gstper; ?></td>
                       <td colspan="2" align="center"><?php echo my2digit($vatamt); ?></td> 
                        <?php } ?>
                   </tr>
                   
               </table>

           </td>
       
           <td colspan="9" align="right" style="border-left:none;"><b>For : <?php echo $dealer_data['name']; ?></b><br /><br /><br />Authorised Signatory</td>
        </tr>
    </table>
    <table width="100%" border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
      <tr>
         <td valign="top" style="border:none;width:70%"><span style="font-size:9pt;"><?php if(!empty($dealer_data['terms'])) echo "$dealer_data[terms]"; else echo "E & O E Please Check the stock at delivery All disputes are subject to Jursdiction only. It is computer generated invoice does not need signature.";?></span></td>
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
    
    
</div>
<!-------------------------------END 44 8 SHEET PAGE--------------------------------------------->
<!------------------------------START A4 10 SHEET PAGE------------------------------------------->
<div id="ank" style="display: none">
<table width="100%">
  <tr>
    <td>
      <div class="subhead1"><!-- this portion indicate the print options -->
        <a href="javascript:printing('searchlistdiv1');" title="take a printout" style="margin:0 10px;"><img src="./icons/print.png" /></a>
        Print Invoice 1
      </div>
    </td>
  </tr> 
</table>
<div id="searchlistdiv1" style="page-break-inside: avoid;">
<?php foreach($looper as $key=>$value){
	//print_r($value);
    $retailer_state_id = $value['adr']['state_id'];
  
if($dealer_state_id == $retailer_state_id){
      $gst = 'CGST';
      $gst1 = 'SGST';
  }else{
      $gst = 'IGST';
  }
    $ch_no = $value['ch_no'];
//this loop will help in the printing of the multiple bills?>
<div id="certificate_container" style="padding:0px; font-size:62.5%; font-family:'Times New Roman', Times, serif; page-break-after:auto; margin:0 auto; width:595pt;">
  
  <div id="pritn_what">
    <div class="mytitle" style="font-size:14pt;text-align:center; font-weight:bold; margin-top:0;">
      <span style="text-decoration:underlinel;"><?php if($value['adr']['tin_no']!='') echo"TAX "; else echo "TAX "  ?>INVOICE</span><br />
<!--      <span style="font-size:9pt; font-weight:normal; letter-spacing:-0.5pt;">Invoice for removal of Excisable Goods from a factory or warehouse on payment of Duty (Rule-11)</span>      -->
    </div> 
  </div>
  
  <div id="certificate_header">
    <table width="100%" class="header_table" style="margin-top:0pt; border:1pt solid #000; border-collapse:collapse; font-family:'Times New Roman', Times, serif; font-size:10pt;" border="1">
      <tr>
        <td valign="top" colspan="8" style="width:50%;">
          <!-- table to tin no detail starts here -->
          <table border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
            <tr>
                <td colspan="3"><strong><?php echo $companyname; ?></strong></td>
            </tr>
            <tr>
                <td colspan="3">(AUTH. DIST. OF <?php echo 'Baidyanath Jhansi Pvt. Ltd.'; ?>)</td>
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
        <td valign="top" colspan="9">
          <!-- table to Tarrif heading detail starts here -->
          <table border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
            <tr>
              <td><strong>TO</strong> </td>
<!--        <td align="right"><strong>Authorised Signatory</strong> </td>-->
              <td valign="top"></td>
            </tr>
            <tr>
              <td colspan="8"><strong>INV. No.: </strong><?php echo $value['ch_no']; ?> <br> Date : <?php echo $value['ch_date']; ?> </td>
              <td></td>
            </tr>
            <tr>
                <td colspan="2"><strong> Retailer: </strong><?php echo $value['retailer_id']; ?><br/><strong>Address: </strong>
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
        <td rowspan="2" style="width:3%" align="center">S No.</td>
        <td rowspan="2" style="width:40%" align="center">Item Name</td>
        <td rowspan="2" style="width:5%" align="center">HSN Code</td>
        <td rowspan="2" style="width:5%" align="center">Qty</td>  
        <td rowspan="2" style="width:7%" align="center">M.R.P</td>
        <td rowspan="2" style="width:8%" align="center">Rate</td>
        <td rowspan="2" style="width:4%" align="center">Trade/ Sch. Disc.%</td>
        <td rowspan="2" style="width:4%" align="center">Trade/ Sch. Disc. Amt.</td>
        <td rowspan="2" style="width:8%" align="center">Other Disc. %</td>
        <td rowspan="2" style="width:8%" align="center">Other Disc. Amt.</td>
        <td rowspan="2" style="width:5%" align="center">Tax - able Amt.</td>
        <td rowspan="2" style="width:7%" align="center">GST %</td>
        <td colspan="2" style="width:5%" align="center"><?php echo $gst; ?></td>       
       <?php  if($dealer_state_id == $retailer_state_id){ ?>
        <td colspan="2" style="width:5%" align="center"><?php echo $gst1; ?></td>      
       <?php } ?>
        <td rowspan="2" style="width:15%" align="center">Total Amt.</td>
      </tr>
      
      <tr style="font-weight:bold;">   
         <td style="width:5%" align="center">%</td>
        <td style="width:8%" align="center">Amt</td>
       <?php  if($dealer_state_id == $retailer_state_id){ ?>
        <td style="width:5%" align="center">%</td>
        <td style="width:8%" align="center">Amt</td>
       <?php } ?>     
      </tr>
      <?php 
    $inc2 =1; 
          $i2=1;
    $amount2 = array();
    $mybarcode2 = '';
          $grandtotal2 = 0;
          $surcharge2=  myrowval('catalog_product_rate_list', 'surcharge', 'stateid='.$_SESSION[SESS.'data']['state_id']);
         // print_r($surcharge);
    foreach($value['challan_item'] as $key1=>$value1){ 
      if(!empty($value1['lineno'])) $mybarcode2 .= "\t".$value1['lineno']."\t".$value1['qty'];
                
                 if($value1['cd_type']==1){
        $cd2 = (($value1['qty'] * $value1['product_rate'])*$value1['cd'])/100;        
        }
elseif ($value1['cd_type']==2) {
 $cd2 = ($value1['cd']);        
}    
            $taxable2 =$value1['product_rate']*$value1['qty'];
             //$amt2 = $taxable2;
             if($value1['tax']==0){$vat_amt2 = 0; }
             else{
           //  $vat_amt2 = (($amt2)*$value1['tax'])/100;}
            //  h1($vat_amt);
             //$vat_amt = (($amt-$cd)*$value1['tax'])/100;   
           //  $vat_amt12 = $vat_amt2 - ($vat_amt2*($surcharge2/100));
           //  $surcharge_amt2= $vat_amt2 - $vat_amt12;  
            // h1( $surcharge_amt);
            //h1($vat_amt1);
                 $amt2 = $taxable2 -($value1['dis_amt']+$value1['cd_amt']);
                 $vat_amt2 = $value1['vat_amt'];
             }
    ?>
       <tr>
        <td style="border-bottom:none;border-top:none;height:36px;"> <?php echo $i2;?></td>
        <!--<td style="border-bottom:none;border-top:none;height:36px;"> <?php echo $value1['comunity_code'];?></td>-->
        <td style="border-bottom:none;border-top:none;height:10px;"> <?php echo $value1['name'];?></td>
         <td style="border-bottom:none;border-top:none;height:10px;"> <?php echo $value1['hsn_code'];?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo $value1['qty'];?></td>
<!--        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo $value1['free_qty'];?></td>-->
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"><?php echo my2digit($value1['mrp']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['product_rate']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['dis_percent']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['dis_amt']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['cd']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['cd_amt']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($amt2);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['tax']).'';?></td>
       <?php  if($dealer_state_id == $retailer_state_id){ ?>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo ($value1['tax']/2) ;?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($vat_amt/2) ;?></td>
          
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo ($value1['tax']/2) ;?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($vat_amt/2) ;?></td>
         <?php }else{ ?>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo ($value1['tax']) ;?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($vat_amt) ;?></td>
        <?php } ?>
<!--        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($surcharge);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($surcharge_amt);?></td>-->
  <?php  
        
          
            
           
            $net_amt2 = $value1['taxable_amt'];
        ?>
        <td style="border-bottom:none;border-top:none;" align="right"> <?php echo my2digit($net_amt2) ;?></td>
      </tr>
    <?php
          $srchrg2 = (trim($value1['tax'])==0)?'0':$surcharge2;
          $summary_vat2[$value1['tax']."+".$srchrg2]['amount']+=my2digit($taxable2);
          $summary_vat2[$value1['tax']."+".$srchrg2]['tax']+=my2digit($vat_amt2);
          $summary_vat2[$value1['tax']."+".$srchrg2]['surcharge']+=my2digit($surcharge_amt2);
          $summary_vat2[$value1['tax']."+".$srchrg2]['cd_amt']+=my2digit($value1['cd_amt']);
        //  $summary_vat[$value1['tax']]['total']+=$summary_vat[$value1['tax']]['amount']+$summary_vat[$value1['tax']]['tax']+$summary_vat[$value1['tax']]['surcharge'];
          $dis_amt2 += $value1['dis_amt'];
          $gst_per += $value1['tax'];
          $cd_amt2+=$value1['cd_amt'];
          $taxable_amt2+=$amt2;
          $ttl_surcharge_amt2 += $surcharge_amt2;
          $ttl_vat_amt2 +=$vat_amt2;
          //$grandcd += $cd;
          $grandtotal2 += $net_amt2;
          //$grandvat += $vat_amt ;
         // h1($grandtotal);
          $inc2++; 
          $i2++;
                   
             }// foreach($value['po_item'] as $key1=>$value1){ ends
       for($k=$inc;$k<8;$k++){
   ?>
         
      <?php }  if($dealer_state_id == $retailer_state_id){ $col = '2'; $col1 = '2'; $col2 = '15'; $col3 = '2'; }else{
          $col = '1'; $col1 = '1'; $col2 = '13'; $col3 = '2'; 
      }?>
      <tr style="font-size:11pt;">
        
        <td colspan="7"><strong>TOTAL AMOUNT</strong></td>
       
        <td colspan="1" align="right"><strong><?php echo my2digit($dis_amt2); ?><!--106425.00--></strong></td>
        <td colspan="2" align="right"><strong><?php echo my2digit($cd_amt2); ?><!--106425.00--></strong></td>
        <td colspan="1" align="right"><strong><?php echo my2digit($taxable_amt2); ?><!--106425.00--></strong></td>
        <td colspan="1" align="right"><strong><?php// echo my2digit($gst_per); ?><!--106425.00--></strong></td>
       
        <td colspan="<?php echo $col; ?>" align="right"><strong><?php echo my2digit($ttl_vat_amt2/2); ?><!--106425.00--></strong></td>
        <td colspan="<?php echo $col1; ?>" align="right"><strong><?php echo my2digit($ttl_vat_amt2/2); ?><!--106425.00--></strong></td>
        <td colspan="1" align="right"><strong><?php echo $grandtotal2; ?><!--106425.00--></strong></td>
      </tr> 
     <tr style="font-size:11pt;">
            <td colspan="<?php echo $col2; ?>"><b>DISCOUNT &nbsp; <?=$value['discount_per']?>% &nbsp; ON TOTAL AMOUNT </b><!--ONE LAC SIX THOUSAND FOUR HUNDRED TWENTY FIVE ONLY.--></td>
            <td colspan="<?php echo $col3; ?>"> <span style="float:right"><b><?php echo price_to_words(round($value['discount_amt']));?></b></span></td>
            
        </tr>
        <tr style="font-size:11pt;">
           <td colspan="<?php echo $col2; ?>"><b>GRAND TOTAL </b><!--ONE LAC SIX THOUSAND FOUR HUNDRED TWENTY FIVE ONLY.--></td>
        <td colspan="<?php echo $col3; ?>"> <span style="float:right"><b><?php echo price_to_words(round($value['amount']));?></b></span></td>
           
       </tr>
 
      <tr style="font-size:11pt;">
         <td colspan="17">Amount of Invoice (in Words) Rupees. <b><?php echo strtoupper(price_to_words($value['amount'],2));?></b><!--ONE LAC SIX THOUSAND FOUR HUNDRED TWENTY FIVE ONLY.--></td>
        </tr>
       <tr>
            
           <td colspan="6" >
               <table width="100%" border="0">
                   <tr>
                       <th colspan="2">HSN Code</th>
                       <th colspan="2">Taxable Amt.</th>
                       <th colspan="2"><?php echo $gst; ?>  %</th>
                       <th colspan="2"><?php echo $gst; ?>  Amt.</th>
                        <?php  if($dealer_state_id == $retailer_state_id){ ?>
                        <th colspan="2"><?php echo $gst1; ?> %</th>
                       <th colspan="2"><?php echo $gst1; ?> Amt.</th>
                         <?php } ?>
                   </tr>
                   <?php                     
                  // pre($value);
                   
                   foreach($value['challan_hsn_dtl'] as $key1=>$value2){  //pre($value2); ?>
                   <tr>                       
                       <td colspan="2" align="center"><?php echo $value2['hsn_code']; ?></td>
                       <td colspan="2" align="center"><?php echo $value2['taxable_amt']; ?></td>
                       <?php  if($dealer_state_id == $retailer_state_id){ ?>
                       <td colspan="2" align="center"><?php echo ($value2['gst_tax']/2); ?></td>
                       <td colspan="2" align="center"><?php echo my2digit($value2['gst_amt']/2); ?></td> 
                       <td colspan="2" align="center"><?php echo ($value2['gst_tax']/2); ?></td>
                       <td colspan="2" align="center"><?php echo my2digit($value2['gst_amt']/2); ?></td> 
                       <?php } else { ?>
                        <td colspan="2" align="center"><?php echo ($value2['gst_tax']); ?></td>
                       <td colspan="2" align="center"><?php echo my2digit($value2['gst_amt']); ?></td> 
                       <?php } ?>
                   </tr>
                   <?php 
                    $taxableamt += ($value2['taxable_amt']);
                    if($dealer_state_id == $retailer_state_id){ 
                     $gstper += ($value2['gst_tax']/2); 
                     $vatamt += ($value2['gst_amt']/2);
                    }else{
                        $gstper += ($value2['gst_tax']); 
                        $vatamt += ($value2['gst_amt']);
                    }
                   
                   } ?>
                   <tr>                       
                       <td colspan="2" align="center"><b>Total</b></td>
                       <td colspan="2" align="center"><?php echo $taxableamt; ?></td>
                       <td colspan="2" align="center"><?php echo $gstper; ?></td>
                       <td colspan="2" align="center"><?php echo my2digit($vatamt); ?></td> 
                        <?php  if($dealer_state_id == $retailer_state_id){ ?>
                       <td colspan="2" align="center"><?php echo $gstper; ?></td>
                       <td colspan="2" align="center"><?php echo my2digit($vatamt); ?></td> 
                        <?php } ?>
                   </tr>
                   
               </table>

           </td>
           <td colspan="2" style="border-right:none;">
           </td>
           <td colspan="9" align="right" style="border-left:none;"><b>For : <?php echo $dealer_data['name']; ?></b><br /><br /><br />Authorised Signatory</td>
        </tr>
    </table>
    <table width="100%" border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
      <tr>
         <td valign="top" style="border:none;width:70%"><span style="font-size:9pt;"><?php if(!empty($dealer_data['terms'])) echo "$dealer_data[terms]"; else echo "E & O E Please Check the stock at delivery All disputes are subject to Jursdiction only. It is computer generated invoice does not need signature.";?></span></td>
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
    
    
</div>
<!-------------------------------END 44 10 SHEET PAGE--------------------------------------------->
<!------------------------------START A4 12 SHEET PAGE------------------------------------------->
<div id="a412" style="display: none">
<table width="100%">
  <tr>
    <td>
      <div class="subhead1"><!-- this portion indicate the print options -->
        <a href="javascript:printing('searchlistdiv1');" title="take a printout" style="margin:0 10px;"><img src="./icons/print.png" /></a>
        Print Invoice 
      </div>
    </td>
  </tr> 
</table>
<div id="searchlistdiv1" style="page-break-inside: avoid;">
<?php 

foreach($looper as $key=>$value){  
    $retailer_state_id = $value['adr']['state_id'];
  
 if($dealer_state_id == $retailer_state_id){
      $gst = 'CGST';
      $gst1 = 'SGST';
  }else{
      $gst = 'IGST';
  }
  
 $ch_no = $value['ch_no'];
 //h1($ch_no);
//this loop will help in the printing of the multiple bills?>
<div id="certificate_container" style="padding:0px; font-size:62.5%; font-family:'Times New Roman', Times, serif; page-break-after:auto; margin:0 auto; width:595pt;">
  
  <div id="pritn_what">
    <div class="mytitle" style="font-size:14pt;text-align:center; font-weight:bold; margin-top:0;">
      <span style="text-decoration:underlinel;"><?php if($value['adr']['tin_no']!='') echo"TAX "; else echo "TAX "  ?>INVOICE</span><br />
<!--      <span style="font-size:9pt; font-weight:normal; letter-spacing:-0.5pt;">Invoice for removal of Excisable Goods from a factory or warehouse on payment of Duty (Rule-11)</span>      -->
    </div> 
  </div>
  
  <div id="certificate_header">
    <table width="100%" class="header_table" style="margin-top:0pt; border:1pt solid #000; border-collapse:collapse; font-family:'Times New Roman', Times, serif; font-size:10pt;" border="1">
      <tr>
        <td valign="top" colspan="8" style="width:50%;">
          <!-- table to tin no detail starts here -->
          <table border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
            <tr>
                <td colspan="3"><strong><?php echo $companyname; ?></strong></td>
            </tr>
            <tr>
                <td colspan="3">(AUTH. DIST. OF <?php echo 'DS Spiceco Pvt. Ltd.'; ?>)</td>
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
        <td valign="top" colspan="9">
          <!-- table to Tarrif heading detail starts here -->
          <table border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
            <tr>
              <td><strong>TO</strong> </td>
<!--        <td align="right"><strong>Authorised Signatory</strong> </td>-->
              <td valign="top"></td>
            </tr>
            <tr>
              <td colspan="8"><strong>INV. No.: </strong><?php echo $value['ch_no']; ?> <br> Date : <?php echo $value['ch_date']; ?> </td>
              <td></td>
            </tr>
            <tr>
                <td colspan="2"><strong> Retailer: </strong><?php echo $value['retailer_id']; ?><br/><strong>Address: </strong>
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
        <td rowspan="2" style="width:3%" align="center">S No.</td>
        <td rowspan="2" style="width:40%" align="center">Item Name</td>
        <td rowspan="2" style="width:5%" align="center">HSN Code</td>
        <td rowspan="2" style="width:5%" align="center">Qty</td>  
        <td rowspan="2" style="width:7%" align="center">M.R.P</td>
        <td rowspan="2" style="width:8%" align="center">Rate</td>
        <td rowspan="2" style="width:4%" align="center">Trade/ Sch. Disc.%</td>
        <td rowspan="2" style="width:4%" align="center">Trade/ Sch. Disc. Amt.</td>
        <td rowspan="2" style="width:8%" align="center">Other Disc. %</td>
        <td rowspan="2" style="width:8%" align="center">Other Disc. Amt.</td>
        <td rowspan="2" style="width:5%" align="center">Tax - able Amt.</td>
        <td rowspan="2" style="width:7%" align="center">GST %</td>
        <td colspan="2" style="width:5%" align="center"><?php echo $gst; ?></td>       
       <?php  if($dealer_state_id == $retailer_state_id){ ?>
        <td colspan="2" style="width:5%" align="center"><?php echo $gst1; ?></td>      
       <?php } ?>
        <td rowspan="2" style="width:15%" align="center">Total Amt.</td>
      </tr>
      
      <tr style="font-weight:bold;">   
         <td style="width:5%" align="center">%</td>
        <td style="width:8%" align="center">Amt</td>
       <?php  if($dealer_state_id == $retailer_state_id){ ?>
        <td style="width:5%" align="center">%</td>
        <td style="width:8%" align="center">Amt</td>
       <?php } ?>     
      </tr>
      <?php 
    $inc3 =1; 
          $i3=1;
    $amount3 = array();
    $mybarcode3 = '';
          $grandtotal3 = 0;
          $surcharge3=  myrowval('catalog_product_rate_list', 'surcharge', 'stateid='.$_SESSION[SESS.'data']['state_id']);
         // print_r($surcharge);
    foreach($value['challan_item'] as $key1=>$value1){ 
      if(!empty($value1['lineno'])) $mybarcode3 .= "\t".$value1['lineno']."\t".$value1['qty'];
                
                 if($value1['cd_type']==1){
        $cd3 = (($value1['qty'] * $value1['product_rate'])*$value1['cd'])/100;        
        }
        elseif ($value1['cd_type']==2) {
         $cd3 = ($value1['cd']);        
        }    
            $taxable3=$value1['product_rate']*$value1['qty'];
             //$amt3 = $taxable3;
             if($value1['tax']==0){$vat_amt3 = 0; }
             else{
            // $vat_amt3 = (($amt3)*$value1['tax'])/100;}
            //  h1($vat_amt);
             //$vat_amt = (($amt-$cd)*$value1['tax'])/100;   
           //  $vat_amt13 = $vat_amt3 - ($vat_amt3*($surcharge3/100));
           //  $surcharge_amt3= $vat_amt3 - $vat_amt13;  
            // h1( $surcharge_amt);
            //h1($vat_amt1);
                 $amt3 = $taxable3 -($value1['dis_amt']+$value1['cd_amt']);
                 $vat_amt3 = $value1['vat_amt'];
             }
    ?>
       <tr>
        <td style="border-bottom:none;border-top:none;height:36px;"> <?php echo $i3;?></td>
        <!--<td style="border-bottom:none;border-top:none;height:36px;"> <?php echo $value1['comunity_code'];?></td>-->
        <td style="border-bottom:none;border-top:none;height:10px;"> <?php echo $value1['name'];?></td>
         <td style="border-bottom:none;border-top:none;height:10px;"> <?php echo $value1['hsn_code'];?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo $value1['qty'];?></td>
<!--        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo $value1['free_qty'];?></td>-->
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"><?php echo my2digit($value1['mrp']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['product_rate']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['dis_percent']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['dis_amt']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['cd']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['cd_amt']);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($amt3);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($value1['tax']).'';?></td>
      <?php  if($dealer_state_id == $retailer_state_id){ ?>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo ($value1['tax']/2) ;?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($vat_amt/2) ;?></td>
          
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo ($value1['tax']/2) ;?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($vat_amt/2) ;?></td>
        <?php } ?>
<!--        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($surcharge3);?></td>
        <td style="border-bottom:none;border-top:none;height:36px;" align="right"> <?php echo my2digit($surcharge_amt3);?></td>-->
  <?php  
        
          
            
           
            $net_amt3 = $value1['taxable_amt'];
        ?>
        <td style="border-bottom:none;border-top:none;" align="right"> <?php echo my2digit($net_amt3) ;?></td>
      </tr>
    <?php
          $srchrg3 = (trim($value1['tax'])==0)?'0':$surcharge3;
          $summary_vat3[$value1['tax']."+".$srchrg3]['amount']+=my2digit($taxable3);
          $summary_vat3[$value1['tax']."+".$srchrg3]['tax']+=my2digit($vat_amt3);
          $summary_vat3[$value1['tax']."+".$srchrg3]['surcharge']+=my2digit($surcharge_amt3);
          $summary_vat3[$value1['tax']."+".$srchrg3]['cd_amt']+=my2digit($value1['cd_amt']);
        //  $summary_vat[$value1['tax']]['total']+=$summary_vat[$value1['tax']]['amount']+$summary_vat[$value1['tax']]['tax']+$summary_vat[$value1['tax']]['surcharge'];
          $dis_amt3 += $value1['dis_amt'];
          $gst_per += $value1['tax'];
          $cd_amt3+=$value1['cd_amt'];
          $taxable_amt3 +=$amt3;
          $ttl_surcharge_amt3 += $surcharge_amt3;
          $ttl_vat_amt3 +=$vat_amt3;
          //$grandcd += $cd;
          $grandtotal3 += $net_amt3;
          //$grandvat += $vat_amt ;
         // h1($grandtotal);
          $inc3++; 
          $i3++;
          
          
             }// foreach($value['po_item'] as $key1=>$value1){ ends
       for($k=$inc3;$k<8;$k++){
   ?>
         
      <?php }if($dealer_state_id == $retailer_state_id){ $col = '2'; $col1 = '2'; $col2 = '15'; $col3 = '2'; }else{
          $col = '1'; $col1 = '1'; $col2 = '13'; $col3 = '2'; 
      } ?>
      <tr style="font-size:11pt;">
        
        <td colspan="7"><strong>TOTAL AMOUNT</strong></td>
       
        <td colspan="1" align="right"><strong><?php echo my2digit($dis_amt3); ?><!--106425.00--></strong></td>
        <td colspan="2" align="right"><strong><?php echo my2digit($cd_amt3); ?><!--106425.00--></strong></td>
        <td colspan="1" align="right"><strong><?php echo my2digit($taxable_amt3); ?><!--106425.00--></strong></td>
        <td colspan="1" align="right"><strong><?php// echo my2digit($gst_per); ?><!--106425.00--></strong></td>
       
        <td colspan="<?php echo $col; ?>" align="right"><strong><?php echo my2digit($ttl_vat_amt3/2); ?><!--106425.00--></strong></td>
        <td colspan="<?php echo $col1; ?>" align="right"><strong><?php echo my2digit($ttl_vat_amt3/2); ?><!--106425.00--></strong></td>

        <td colspan="1" align="right"><strong><?php echo $grandtotal3; ?><!--106425.00--></strong></td>
      </tr> 
     <tr style="font-size:11pt;">
            <td colspan="<?php echo $col2; ?>"><b>DISCOUNT &nbsp; <?=$value['discount_per']?>% &nbsp; ON TOTAL AMOUNT </b><!--ONE LAC SIX THOUSAND FOUR HUNDRED TWENTY FIVE ONLY.--></td>
            <td colspan="<?php echo $col3; ?>"> <span style="float:right"><b><?php echo price_to_words(round($value['discount_amt']));?></b></span></td>
            
        </tr>
       <tr style="font-size:11pt;">
           <td colspan="<?php echo $col2; ?>"><b>GRAND TOTAL </b><!--ONE LAC SIX THOUSAND FOUR HUNDRED TWENTY FIVE ONLY.--></td>
        <td colspan="<?php echo $col3; ?>"> <span style="float:right"><b><?php echo price_to_words(round($value['amount']));?></b></span></td>
           
       </tr>
 
      <tr style="font-size:11pt;">
         <td colspan="17">Amount of Invoice (in Words) Rupees. <b><?php echo strtoupper(price_to_words($value['amount'],2));?></b><!--ONE LAC SIX THOUSAND FOUR HUNDRED TWENTY FIVE ONLY.--></td>
        </tr>
        <tr>
            
           <td colspan="6" >
               <table width="100%" border="0">
                   <tr>
                       <th colspan="2">HSN Code</th>
                       <th colspan="2">Taxable Amt.</th>
                       <th colspan="2"><?php echo $gst; ?>  %</th>
                       <th colspan="2"><?php echo $gst; ?>  Amt.</th>
                        <?php  if($dealer_state_id == $retailer_state_id){ ?>
                        <th colspan="2"><?php echo $gst1; ?> %</th>
                       <th colspan="2"><?php echo $gst1; ?> Amt.</th>
                         <?php } ?>
                   </tr>
                   <?php                     
                  // pre($value);
                   
                   foreach($value['challan_hsn_dtl'] as $key1=>$value2){  //pre($value2); ?>
                   <tr>                       
                       <td colspan="2" align="center"><?php echo $value2['hsn_code']; ?></td>
                       <td colspan="2" align="center"><?php echo $value2['taxable_amt']; ?></td>
                       <?php  if($dealer_state_id == $retailer_state_id){ ?>
                       <td colspan="2" align="center"><?php echo ($value2['gst_tax']/2); ?></td>
                       <td colspan="2" align="center"><?php echo my2digit($value2['gst_amt']/2); ?></td> 
                       <td colspan="2" align="center"><?php echo ($value2['gst_tax']/2); ?></td>
                       <td colspan="2" align="center"><?php echo my2digit($value2['gst_amt']/2); ?></td> 
                       <?php } else { ?>
                        <td colspan="2" align="center"><?php echo ($value2['gst_tax']); ?></td>
                       <td colspan="2" align="center"><?php echo my2digit($value2['gst_amt']); ?></td> 
                       <?php } ?>
                   </tr>
                   <?php 
                    $taxableamt += ($value2['taxable_amt']);
                    if($dealer_state_id == $retailer_state_id){ 
                     $gstper += ($value2['gst_tax']/2); 
                     $vatamt += ($value2['gst_amt']/2);
                    }else{
                        $gstper += ($value2['gst_tax']); 
                        $vatamt += ($value2['gst_amt']);
                    }
                   
                   } ?>
                   <tr>                       
                       <td colspan="2" align="center"><b>Total</b></td>
                       <td colspan="2" align="center"><?php echo $taxableamt; ?></td>
                       <td colspan="2" align="center"><?php echo $gstper; ?></td>
                       <td colspan="2" align="center"><?php echo my2digit($vatamt); ?></td> 
                        <?php  if($dealer_state_id == $retailer_state_id){ ?>
                       <td colspan="2" align="center"><?php echo $gstper; ?></td>
                       <td colspan="2" align="center"><?php echo my2digit($vatamt); ?></td> 
                        <?php } ?>
                   </tr>
                   
               </table>

           </td>
           <td colspan="2" style="border-right:none;">
           </td>
           <td colspan="9" align="right" style="border-left:none;"><b>For : <?php echo $dealer_data['name']; ?></b><br /><br /><br />Authorised Signatory</td>
        </tr>
    </table>
    <table width="100%" border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
      <tr>
        <td valign="top" style="border:none;width:70%"><span style="font-size:9pt;"><?php if(!empty($dealer_data['terms'])) echo "$dealer_data[terms]"; else echo "E & O E Please Check the stock at delivery All disputes are subject to Jursdiction only. It is computer generated invoice does not need signature.";?></span></td>
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
    
    
</div>
<!-------------------------------END 44 12 SHEET PAGE--------------------------------------------->
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
