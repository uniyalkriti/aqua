<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php
        global $dbc;
        $d1 = $_GET;
        $d1['uid'] = $_SESSION[SESS . 'data']['id'];
        $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
        $compid = $_GET['complaint_id'];
 $myobj = new dashboard();
 $chl = $myobj->complaint_history_list($compid);
 pre($chl);
?>
<table width="100%">
  <tr>
    <td>
      <div class="subhead1"><!-- this portion indicate the print options -->
        <a href="javascript:printing('searchlistdiv');" title="take a printout" style="margin:0 10px;"><img src="./icons/print.png" /></a>
        Print Complaint
      </div>
    </td>
  </tr>	
</table>
<div id="searchlistdiv" style="page-break-inside: avoid;">
 <table width="100%" class="header_table" style="margin-top:0pt; border:1pt solid #000; border-collapse:collapse; font-family:'Times New Roman', Times, serif; font-size:10pt;" border="1">
      <tr>
     
      
      </tr>
     
  </table>
</div>
<!--------------------------------NORMAL PAGE----------------------------------------------->

