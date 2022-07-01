
<?php
//Reading the company statistics
$company = new company();
$cmpId = 1;
$companystat = $company->get_company_list("cmpId=$cmpId");
$companystat = $companystat[$cmpId];

$certstat = array('companyname'=>$companystat['cmp_name'],
				  'company_adr'=>'94/5,WEST SAROORPUR INDL. AREA,<br>SOHNA ROAD,BALLABHGARH <br>FARIDABAD',
				  'phone'=>'M : '.$companystat['phone'].', '.$companystat['mobile'],
				  'website'=>$companystat['website'],
				  'email'=>$companystat['cmp_email'],
				  'format_no'=>'- FO1 (5.10/01) / REV.NO. 02',
				  'certificate_no'=>$_POST['certificate_no'],
				  'srfcode'=>$_POST['srfcode'],
				  'srfdate'=>$_POST['srfdate'],
				  'itemdesc'=>$_POST['itemdesc'],
				  'calibration_date'=>$_POST['calibration_date'],
				  'cal_due_date'=>$_POST['cal_due_date'],
				  'ref_standard'=>$_POST['ref_standard'],
				  'cal_procedure'=>$_POST['cal_procedure'],
				  'custname'=>strtoupper($_POST['partyId_val']),
				  'custadr'=>$party->get_party_adr($_POST['partyId']),
				  'equipno'=>$_POST['equipno'],
				  'make'=>$_POST['make'],
				  'serial_no'=>address_presenter(array('equipno'=>$_POST['equipno'], 'serial_no'=>$_POST['serial_no']), array('equipno', 'serial_no'), '/'),
				  'range_size'=>$_POST['range_size'],
				  'least_count'=>$_POST['least_count'],
				  'location'=>$_POST['location'],
				  'temperature'=>$_POST['temperature'],
				  'humidity'=>$_POST['humidity'],
				  'cal_performed_at'=>$GLOBALS['job_type'][$_POST['cal_performed_at']],
				  'equipment_master'=>'',
				  'calibration_result'=>'',
				  'uncertainity'=>$_POST['uncertainity'],
				  'technical_manager'=>$_POST['technical_manager']
				  );
				  $website = !empty($companystat['website']) ? 'Website : '.$companystat['website'] : '';
				  $website .= !empty($companystat['cmp_email']) ? ' Email : '.$companystat['cmp_email'] : '';

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
        Print Certificate <span class="seperator"> | </span> <a href="javascript:void(0);" onclick="hidemyheader();" style="margin:0 10px;">Manage Header</a>
      </div>
    </td>
  </tr>	
</table>
<div id="searchlistdiv">
<div id="certificate_container" style="padding:10px; font-size:14px; font-family:'Times New Roman', Times, serif;">
  
  <div id="certificate_header_no" style="height:145px; display:none;" class="showno">
  </div>
  
  <div id="certificate_header" class="showyes">
    <table width="100%" class="header_table">
      <tr>
        <td class="logo" width="30%"><img src="../icon-project/default/digimet.jpg" align="left" width="120px" height="70px;"></td>
        <td align="center">
          <div id="companyname"><B><?php echo $certstat['companyname']; ?></B></div>
          <div id="company_adr"><?php echo $certstat['company_adr']; ?></div>
          <div id="phone"><?php echo $certstat['phone']; ?></div>
          <div id="website_email"><?php echo $website; ?></div>
        </td> 
        <td width="35%">&nbsp; </td>    
      </tr>
    </table>
  </div>
  
  <div class="mytitle" style="font-size:18px; margin-bottom:5px; text-align:center; font-style:italic; margin-top:10px;">CALIBRATION CERTIFICATE</div>				  
  
  <div id="certificate_detail">
    <table width="100%" class="certificate_detail" cellpadding="0" cellspacing="0">
      <tr class="row1">
        <td width="30%">&nbsp;</td>
        <td width="35%">&nbsp;</td>
        <td width="35%" align="right" ><span class="bold">FORMAT NO.</span> <?php echo $certstat['format_no']; ?></td>
      </tr>
      <tr class="row2" >
        <td class="col1" align="left" valign="top" style="border-top:1px solid; border-bottom:1px solid; padding:5px 0;"><b>Certificate No.</b> <br /> <?php echo $certstat['certificate_no']; ?></td>
        <td class="col2" align="center" valign="top" style="border-top:1px solid; border-bottom:1px solid; padding:5px 0;" ><b>SRF. No. / Date</b> <br /><?php echo $certstat['srfcode']; ?> / <?php echo $certstat['srfdate']; ?></td>
        <td class="col3" align="right" valign="top" style="border-top:1px solid; border-bottom:1px solid; padding:5px 0;"><b>Calibration Certificate of</b> <br /> <?php echo $certstat['itemdesc']; ?></td>
      </tr>
      <tr class="row3">
        <td  class="col1" align="left" valign="top" style="padding:5px 0;"><b>Date of Calibration</b> <br /> <?php echo $certstat['calibration_date']; ?></td>
        <td class="col2" align="center" valign="top" style="padding:5px 0;"><b>Sugg. Due Date of Calibration</b> <br /> <?php echo $certstat['cal_due_date']; ?></td>
        <td class="col3" align="right" valign="top" style="padding:5px 0;"><b>Standard Followed/W.I. No.</b> <br /> <?php echo $certstat['ref_standard']; ?> / <?php echo $certstat['cal_procedure']; ?></td>
      </tr>
    </table>
  </div>
  
  <div id="customer_detail">
    <table width="100%" class="customer_detail" style="border:1px solid; border-collapse:collapse;">
      <tr class="row1">
        <td width="40%" class="col1" valign="top" style="border-right:1px solid; padding-left:5px;">CUSTOMER NAME & ADDRESS</td>
        <td class="col2" align="left" style="padding-left:5px;"><b>M/s <?php echo $certstat['custname']; ?></b><Br /><?php echo $certstat['custadr']; ?></td>
      </tr>
    </table>
  </div>	  
  
  <div id="item_detail">
    <table width="100%" class="item_detail">
      <tr class="rowgen">
        <td class="col1" width="40%" style="padding-left:10px;">MAKE</td>
        <td class="col2" align="left"><span style="margin-right:10px;">:</span> <?php echo $certstat['make']; ?></td>
      </tr>
      <tr class="rowgen">
        <td class="col1" style="padding-left:10px;">ID. NO./SR. NO.</td>
        <td class="col2" align="left"><span style="margin-right:10px;">:</span> <?php echo $certstat['serial_no']; ?></td>
      </tr>
      <tr class="rowgen">
        <td class="col1" style="padding-left:10px;">RANGE/SIZE</td>
        <td class="col2" align="left"><span style="margin-right:10px;">:</span> <?php echo $certstat['range_size']; ?></td>
      </tr>
      <tr class="rowgen">
        <td class="col1" style="padding-left:10px;">LEAST COUNT</td>
        <td class="col2" align="left"><span style="margin-right:10px;">:</span> <?php echo $certstat['least_count']; ?></td>
      </tr>
      <tr class="rowgen">
        <td class="col1" style="padding-left:10px;">LOCATION</td>
        <td class="col2" align="left"><span style="margin-right:10px;">:</span> <?php echo $certstat['location']; ?></td>
      </tr>
      <tr class="rowgen">
        <td class="col1" style="padding-left:10px;">TEMPERATURE/HUMIDITY</td>
        <td class="col2" align="left"><span style="margin-right:10px;">:</span> <?php echo $certstat['temperature']; ?>/<?php echo $certstat['humidity']; ?></td>
      </tr>
      <tr class="rowgen">
        <td class="col1" style="padding-left:10px;">CALIBRATION PERFORMED AT</td>
        <td class="col2" align="left"><span style="margin-right:10px;">:</span> <?php echo $certstat['cal_performed_at']; ?></td>
      </tr>
    </table>
  </div>
  
  <div id="master_equipment">
    <div class="mytitle" style="font-size:15px; margin-bottom:5px; text-align:center; font-weight:bold; margin-top:10px;">EQUIPMENT & MASTER USED FOR CALIBRATION</div>
    <?php echo $myobj->observation_equipment('edit', $_GET['id'], $outmode='html'); ?>
  </div>
  
  <div id="calib_result">
    <div class="mytitle" style="font-size:15px; margin-bottom:5px; text-align:center; font-weight:bold; margin-top:10px;">CALIBRATION RESULTS</div>
    <?php echo $myobj->calibration_result_html($_GET['id'], $_POST['rifId']);?>
    <div style="margin-top:10px;">Expanded uncertainty of Measurement is <?php echo $certstat['uncertainity']; ?> at coverage factor k=2.</div>
  </div>
  
  <div id="footremark">
    <table width="100%" class="footremark">
      <tr class="remark">
        <td colspan="2"><strong>Note:</strong> 
          <ol style="margin:0px;">
            <li>The results are valid only for this calibrated item.</li>
            <li>The calibration report shall not be reproduced except in full, without the written approval of the laboratory.</li>
            <li>Results Reported are valid at the time of and under the stated condition of measurement.</li>
          </ol>
        </td>
      </tr>
      <tr class="rowgen">
        <td class="col1" align="left" style="padding-top:25px;">Calibrated By:</td>
        <td class="col2" align="right" style="padding-top:25px;">Approved By:</td>
      </tr>
      <tr class="rowgen">
        <td >
          <div id="signare1a" style="margin-top:30px;">
          (<?php echo $_POST['cal_engineer']; ?>)<br />
          (Calibration Engineer)
          </div>
        </td>
        <td align="right" >
          <div id="signarea" style="margin-top:30px;">
          (<?php echo $certstat['technical_manager']; ?>)<br />
          (Quality Manager)
          </div>
        </td>
      </tr>
    </table>
  </div>
</div><!-- #certificate_conatiner ends -->
</div>