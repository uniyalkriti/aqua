
<style>
    #tg6 table {
        border-collapse: collapse !important;
    }

    #tg6 table, #tg6 th, #tg6 td {
        border: 1px solid black !important;
    }

    #tg6 th {
        background-color: #7BB0FF !important;
        color: black;
    }
</style>
<div class="row">
    
    <div class="col-md-12" >
        <a onclick="fnExcelReport('tg6')" href="javascript:void(0)" class="nav-link">
            <button class="btn btn-primary"><i class="fa fa-file-excel-o "></i> Export Excel </button></a>
 <table class="tg6" id="tg6" width="100%" style="font-size: 13px;border: 1px black">
    <tr bgcolor="#438EB9">
      <th colspan="6" bgcolor="#438EB9">NEW O/L STATUS</th>
    </tr>
<tr>
  <th>User<br></th>
  <th>Designation<br></th>
  <th>NO OF NEW O/L OPENED TILL<BR> DATE THIS MONTH</th>
  <th>TGT FOR THE<br>    MONTH</th>
  <th>%TGT ACH <BR> OF MONTHS TOTAL</th>
  <th bgcolor="yellow" >%NEW O/L <br>GROWTH<br>FORM DAY<br>PREVIOUS MONTH</th>
</tr>
@foreach ($outLet as $keyoutLet=>$valueoutLet)
<?php
if(!empty($valueoutLet['target']))
{
  $tgtPer = round($valueoutLet['newOutlet']*100/$valueoutLet['target'],2);
} 
else
{
  $tgtPer = 0;
}   
if(!empty($valueoutLet['retailerActiveLast']))
{
  $growthR = round(($valueoutLet['retailerActive']-$valueoutLet['retailerActiveLast'])*100/$valueoutLet['retailerActiveLast'],2);
} 
else
{
  $growthR = ($valueoutLet['retailerActive']-$valueoutLet['retailerActiveLast'])*100;
} 
?>

<tr>
    <td class="tg6-0lax-left-aligned"><?=$valueoutLet['uname']?></td>
    <td class="tg6-0lax-left-aligned"><?=$valueoutLet['rolename']?></td>
    <td><?=$valueoutLet['newOutlet']?></td>
    <td><?=$valueoutLet['target']?></td>
    <td><?=$tgtPer?></td>
      <td bgcolor="yellow"><?=$growthR?></td>
  </tr>
  
@endforeach

<tr>
  <td class="tg6-0lax-left-aligned"><strong><?=$userName->uname?></strong></td>
  <td class="tg6-0lax-left-aligned"><strong>ASM</strong></td>
  <td class="tg6-0lax"><strong></strong></td>
  <td class="tg6-0lax"><strong></strong></td>
  <td class="tg6-0lax"><strong></strong></td>
  <td bgcolor="yellow"><strong>-<strong></td>
</tr>
</table>  

</div>
 </div>