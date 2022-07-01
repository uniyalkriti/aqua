<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php
//Reading the company statistics
$company = new company();
$cmpId = 1;
$companystat = $company->get_company_list("cmpId=$cmpId");
$companystat = $companystat[$cmpId];

$companyname = $companystat['cmp_name'];
$company_adr = 'Plot No. 94/5 West, Saroorpur Indl. Area, Sohna Road, Faridabad-121 004';
$phone = 'Tel. : +91 9250015515, 9212511336, E-MAIL : info@digimettechnologies.com';

//Fetch the record details
$looper = $myobj->print_looper($_GET['id']);
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
<table width="100%">
  <tr>
    <td>
      <div class="subhead1"><!-- this portion indicate the print options -->
        <a href="javascript:printing('searchlistdiv');" title="take a printout" style="margin:0 10px;"><img src="../icon-system/i16X16/printo.png" /></a>
        Print Order
      </div>
    </td>
  </tr>	
</table>
<div id="searchlistdiv">
<?php foreach($looper as $key=>$value){ //this loop will help in the printing of the multiple bills?>
<div id="certificate_container" style="padding:2px; font-size:14px; font-family:'Times New Roman', Times, serif; page-break-after:always;">
  <div class="mytitle" style="font-size:24px; margin-bottom:5px; text-align:center; margin-top:5px;"><strong style="text-decoration:underline">CALIBRATION SCHEDULE</strong></div>				  
  
  <div id="certificate_detail">
    <table width="100%" class="certificate_detail">
      <tr class="row1">
        <td width="30%">&nbsp;</td>
        <td width="40%">&nbsp; <?php //echo $certstat['lab_code']; ?></td>
        <td width="30%" >&nbsp;</td>
      </tr>
      <tr class="row1">
        <td colspan="2">  
          <strong>Customer Name : </strong>  M/s <?php echo $value['partyId_val']; ?><br />
          <strong>Address : </strong>  <?php echo $value['cust_adr']; ?><br /><br />
          <strong>Contact Person :  </strong> <?php //echo $value['contact_person']; ?><br />
          <strong>Contact Details : </strong>  <?php //echo $value['contact_detail']; ?>        
        </td>
        <td>
          <strong><?php echo $value['challan_or_po_based']; ?> Date : </strong>  <?php echo $value['po_challan_date']; ?><br />
          <strong><?php echo $value['challan_or_po_based']; ?> No. : </strong>  <?php echo $value['po_challan_no']; ?>
        </td>
      </tr>
    </table>
  </div>  
  
  <div id="calib_result">
    <div class="mytitle" style="font-size:15px; margin-bottom:5px; text-align:center; font-weight:bold; margin-top:10px;">Detail of Equipments :</div>
    <?php include('data/srf-items-schedule.inc.php');?>    
  </div>
</div><!-- #certificate_conatiner ends -->
<?php }// foreach($looper as $key=>$value){ ends?>
</div>