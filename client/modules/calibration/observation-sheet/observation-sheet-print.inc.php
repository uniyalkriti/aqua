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

$observation = new observation();
//Fetch the record details
$looper = $observation->print_looper($_GET['id']);
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
        <a href="javascript:printing('searchlistdiv');" title="take a printout" style="margin:0 10px;"><img src="./icons/print.png" /></a>
        Print SRF
      </div>
    </td>
  </tr>	
</table>
<div id="searchlistdiv">
<?php foreach($looper as $key=>$value){ //this loop will help in the printing of the multiple bills?>
<div id="certificate_container" style="padding:2px; font-size:14px; font-family:'Times New Roman', Times, serif; page-break-after:always;">
  <div id="certificate_header">
    <table width="100%" class="header_table" style="margin-top:5px;">
      <tr>
        <td class="logo" valign="top"><img src="../icon-project/default/digimet.jpg" align="left" height="50px;"></td>
        <td align="center" valign="top">
          <div style="text-align:center;">
              <div id="companyname" style="font-size:24px;"><B><?php echo $companyname; ?></B></div>
              <div id="company_adr"><?php echo $company_adr; ?></div>
              <div id="phone"><?php echo $phone; ?></div>
           </div>          
        </td>
        <td valign="top">
          <div style="border:1px solid #000; float:right; font-size:10px;">
          Form No. F01-(5.4/01)<br />
		  Date of Issue<br />
		  Rev.No./Dt. 01/01-03-2011
          </div>
        </td>  
      </tr>
    </table>
  </div>
  
  <div class="mytitle" style="font-size:17px; margin-bottom:5px; text-align:center; margin-top:5px;"><strong>OBSERVATION SHEET (JOB NO. ____________________)</strong></div>				  
  
  <div id="certificate_detail">
    <table width="100%" class="certificate_detail">
      <tr class="row1">
        <td width="30%">Services Request No. : <?php echo $value['srfcode']; ?></td>
        <td width="40%">Lab Code No. : <?php echo $value['lab_code']; ?></td>
        <td width="30%" >Service Request Date : <?php echo $value['srfdate']; ?></td>
      </tr>
      <tr class="row1">
        <td>Date of Calibration : </td>
        <td>&nbsp;</td>
        <td>Calibration Due Date : <?php if(!empty($value['calibration_frequency'])) echo 'After '.$value['calibration_frequency'].' months'; ?></td>
      </tr>
      <tr class="row1">
        <td>Instruement Details :</td>
        <td>&nbsp;</td>
        <td>Certification No : </td>
      </tr>
      <tr class="row1">
        <td>Name : <?php echo $value['itemdesc']; ?></td>
        <td>Range/Size : <?php echo $value['range_size']; ?></td>
        <td>Visual Inspection : <?php echo $value['visual_inspection']; ?></td>
      </tr>
      <tr class="row1">
        <td>Make : <?php echo $value['make']; ?></td>
        <td>Least Count : <?php echo $value['least_count']; ?></td>
        <td>Repeatability : <?php echo $value['repeatibility']; ?></td>
      </tr>
      <tr class="row1">
        <td>Sr. No : <?php echo $value['serial_no']; ?></td>
        <td>Zero Error : </td>
        <td>Flatness : </td>
      </tr>
      <tr class="row1">
        <td>ID No : <?php echo $value['equipno']; ?></td>
        <td>Ref. Std./Cal.Procedure :  <?php echo $value['ref_standard']; ?> / <?php echo $value['cal_procedure']; ?></td>
        <td>Parallelism : </td>
      </tr>
      <tr class="row1">
        <td colspan="2">Location : <?php echo $value['location']; ?></td>
        <td>Calibration Performed :  <?php echo $value['cal_performed_at']; ?></td>
      </tr>
      <tr class="row1">
        <td colspan="2"><strong>Standard Equipments used for Calibration :</strong></td>
        <td><strong>Working Range</strong> (As Per Customer Requirement)</td>
      </tr>
      <tr class="row1">
        <td colspan="3"><?php if(!empty($value['tmpmasterId'])) echo $observation->observation_equipment('save', $value['tmpmasterId'], $outmode='html'); else echo'<table width="100%" border="1" style="border-collapse:collapse;" cellpadding="20px;"><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></table>'; ?></td>
      </tr>
      <tr class="row1">
        <td colspan="3" align="center">Reading observed at Ambient Temp : _____________
          <span style="margin-left:40px;">Relative Humidity :</span> _____________
        </td>
      </tr>
    </table>
  </div>  
  
  <div id="calib_result">
    <div class="mytitle" style="font-size:15px; margin-bottom:5px; text-align:center; font-weight:bold; margin-top:10px;">OBSERVATION READINGS</div>
    <?php include('observation/input-format-1.php');?>    
  </div>
  
  <div id="footremark" style="margin-top:20px;">
    <table width="100%" class="footremark">
      <tr class="remark">
        <td colspan="2"><strong><strong>Note:</strong></strong> 
          <ol style="margin:0px;">
            <li>* UUC - Unit Under Calibration</li>
            <li>NO OVERWRITING IS PERMISSIBLE IN THIS SHEET. IN CASE OF MISTAKE THE WRONG READING SHOULD BE CROSSED & CORRECT READING SHOULD BE WRITTEN SEPERATELY DULY SIGNED.</li>
          </ol>
           
        </td>
      </tr>
      <tr class="rowgen">
        <td colspan="2" align="left" style="padding-top:10px;">
          <table width="100%">
            <tr>	
              <td style="width:10%"><strong>Remarks :</strong></td>
        	  <td class="col2" align="right" style="padding-top:10px; border-bottom:1px solid #000;">&nbsp;</td>
            </tr>
          </table>
        </td>      
      </tr>
      <tr class="rowgen">
        <td class="col1" align="left" style="padding-top:25px;">(SIGNATURE OF CALIBRATION ENGINEER)</td>
        <td class="col2" align="right" style="padding-top:25px;">(SIGNATURE OF TECHNICAL MANAGER)</td>
      </tr>
    </table>
  </div>
</div><!-- #certificate_conatiner ends -->
<?php }// foreach($looper as $key=>$value){ ends?>
</div>