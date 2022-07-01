<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php
        global $dbc;
        $d1 = $_GET;
        $d1['uid'] = $_SESSION[SESS . 'data']['id'];
        $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
        $out = array('status' => 'false', 'myreason' => '');
        
     //   print_r($_GET);
       // pre($d1); exit;
      // echo "ABC"; exit;
            $amt = $d1['achieved'];
            $gift = $d1['scheme_gift'];
        if(strpos($gift, '%' ) !== false)
        {
            $g1 = explode("%", $gift);
           // echo $g1[0]; 
            $amt = ($amt*$g1[0])/100;
        }
    else {
            $amt = '0';
         }
          
            $fdate = $d1['start'];
            $tdate = $d1['end'];
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        $q = "INSERT INTO `claim_challan`(`dealer_id`, `from_date`, `to_date`, `claim_amount`, `claim`,`total_amt`) VALUES 
           ('$dealer_id','$fdate','$tdate','$d1[achieved]','$d1[scheme_gift]','$amt')";
//h1($q);
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Sales Table error');
        }
      
       $q = "UPDATE user_primary_sales_order SET  is_claim = '1' WHERE dealer_id = '$dealer_id' AND DATE_FORMAT(ch_date,'%Y-%m-%d') BETWEEN '$fdate' AND '$tdate'";
      //  h1($q);
       $r = mysqli_query($dbc, $q);
        mysqli_commit($dbc);
        //Final success 
     
$dealer_id = $_SESSION[SESS.'data']['dealer_id'];
$company_id = $_SESSION[SESS.'data']['company_id'];
//$objcomp = new company();
//$company_data = $objcomp->get_company_list($filter="company.id='$company_id'",  $records = '', $orderby='');
//$company_data = $company_data[$company_id];
$myobj1 = new dealer();
$dealer_data = $myobj1->get_dealer_list($filter = "id = '$dealer_id'", $records, $orderby);
//$dealer_data = $dealer_data[$dealer_id];
//pre($dealer_data);
//pre($company_data);
$companyname = $dealer_data['name'];
$company_adr = $dealer_data['address'];
$phone = " +91 $dealer_data[other_numbers], E-MAIL : $dealer_data[email]";
//echo"fdjkgd";
//h1($_GET['id']);
//$looper = $myobj->print_looper_challan($_GET['id']);
//echo "<pre>";
//pre($dealer_data);die;
//echo"<br> ABC";
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
<!--------------------------------NORMAL PAGE----------------------------------------------->

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
<?php { //this loop will help in the printing of the multiple bills?>
<div id="certificate_container" style="padding:0px; font-size:62.5%; font-family:'Times New Roman', Times, serif; page-break-after:auto; margin:0 auto; width:595pt;">
  
  <div id="pritn_what">
    <div class="mytitle" style="font-size:14pt;text-align:center; font-weight:bold; margin-top:0;">
      <span style="text-decoration:underlinel;"><?php if($dealer_data[$dealer_id]['tin_no']!='') echo"CLAIM"; else echo "CLAIM "  ?>INVOICE</span><br />
<!--      <span style="font-size:9pt; font-weight:normal; letter-spacing:-0.5pt;">Invoice for removal of Excisable Goods from a factory or warehouse on payment of Duty (Rule-11)</span>      -->
    </div> 
  </div>
  
  <div id="certificate_header">
    <table width="100%" class="header_table" style="margin-top:0pt; border:1pt solid #000; border-collapse:collapse; font-family:'Times New Roman', Times, serif; font-size:10pt;" border="1">
      <tr>
        <td valign="top" colspan="2" style="width:50%;">
          <!-- table to tin no detail starts here -->
          <table border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
            <tr>
                <td colspan="3"><strong><?php echo $dealer_data[$dealer_id]['name']; ?></strong></td>
            </tr>
            <tr>
                <td colspan="3">(AUTH. DIST. OF DS GROUP) <br> <?=$dealer_data[$dealer_id]['address']?></td>
            </tr>
            <tr>
                <td colspan="3" style="">
                    CONTACT.-: <?php echo $dealer_data[$dealer_id]['other_number'];; ?>
                </td>
            </tr>
            <tr>
              <td>TIN. No.: <?php echo $dealer_data[$dealer_id]['tin_no']; ?></td>
            </tr>
          </table>
          <!-- table to tin no detail ends here -->
        </td>
        <td valign="top" colspan="2">
          <!-- table to Tarrif heading detail starts here -->
          <table border="0" style="font-family:'Times New Roman', Times, serif; font-size:10pt;">
            <tr>
              <td><strong>TO : </strong> DS GROUP <br> B-25 Noida sector-3</td>
	     
              <td valign="top"></td>
            </tr>
            <tr>
              <td><strong>Claim Date: </strong> <?=date("d-m-Y")?></td>
	     
              <td valign="top"></td>
            </tr>
         
          </table>
          <!-- table to Tarrif heading detail ends here -->
        </td>  
      </tr>
     
      <tr style="font-weight:bold;">
        <td style="width:10%" align="center">S No.</td>
<!--        <td style="width:40%">Com Code</td>-->
        <td style="width:40%" align="center">Achieved Amount</td>
        <td style="width:20%" align="center">Claimed Amount/Gift</td>
        <td style="width:30%" align="center">Scheme Date</td>
     </tr>
      <?php 
	  $inc =1; 
          $i=1;
	  $amount = array();
	  $mybarcode = '';
          $grandtotal = 0;
         echo'<td style="width:10%" align="center">'.$inc.'</td>
         <td style="width:40%" align="center">'.$_GET['achieved'].'</td>
        <td style="width:20%" align="center">';
         if(strpos($gift, '%' ) !== false)        
         echo $amt;
         else
             echo $_GET['scheme_gift'];
                         echo'</td>
        <td style="width:30%" align="center">'.$_GET['start'].' - '.$_GET['end'].'</td>';
	 ?>
<!--               </table>
           </td>-->
           <tr>
           <td colspan="2" align="left" style="border:0;"><b><?php 
           if(strpos($gift, '%' ) !== false)
           echo strtoupper(price_to_words($amt,2));
           else
           {
          $gift = $_GET['scheme_gift'];
            $g = strtolower($gift);
            $ge = str_replace(' ','_', $g);
           echo'<img src="./gift/'.$ge.'.png" width="20%">';
           }
           ?></td>
          <td colspan="2" align="right" style="border-left:0;"><b>For : <?php echo $dealer_data[$dealer_id]['name']; ?></b><br /><br /><br />Authorised Signatory</td>
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

<!------------------------------END NORMAL PAGE----------------------------------------------->

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