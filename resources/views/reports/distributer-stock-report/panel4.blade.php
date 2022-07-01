
<div class="row">

    <div class="col-md-12">
        <a onclick="fnExcelReport('range')" href="javascript:void(0)" class="nav-link">
            <button class="btn btn-primary"><i class="fa fa-file-excel-o "></i> Export Excel </button></a>
        <style>
            #range table {
                border-collapse: collapse !important;
            }

            #range table, #range th, #range td {
                border: 1px solid black !important;
            }

            #range th {
                background-color: #7BB0FF !important;
                color: black;
            }
        </style>
<table class="table table-bordered" id="range" width="100%" style="font-size: 13px;border: 1px black">
  <tr>
    <th colspan="20" bgcolor="#438EB9">RANGE SELLING LINES PER CALL(MIN 3 LINES PER CALL)</th>
  </tr>
    <tr height="1px">
      <th  rowspan="2"> TEAM </th>
      <th  rowspan="2"> TOTAL PC TILL DATE </th>
      <th colspan="2"> FDP </th>
      <th colspan="2"> FDC </th>
      <th colspan="2"> MAHA BAR </th>
      <th colspan="2"> ACTIVE WHITE </th>
      <th colspan="2"> NIP-POWDER </th>
      <th colspan="2"> NIP-BAR </th>
      <th colspan="2"> IDP </th>
      <th colspan="2"> FAMUS </th>
      <th colspan="2"> COP </th>
      
    </tr>
    <tr height="1px">
        <th > TOTAL PC TILL DATE </th>
        <th > AVG PC PER DAY PER SR </th>
        <th > TOTAL PC TILL DATE </th>
        <th > AVG PC PER DAY PER SR </th>
        <th > TOTAL PC TILL DATE </th>
        <th > AVG PC PER DAY PER SR </th>
        <th > TOTAL PC TILL DATE </th>
        <th > AVG PC PER DAY PER SR </th>
        <th > TOTAL PC TILL DATE </th>
        <th > AVG PC PER DAY PER SR </th>
        <th > TOTAL PC TILL DATE </th>
        <th > AVG PC PER DAY PER SR </th>
        <th > TOTAL PC TILL DATE </th>
        <th > AVG PC PER DAY PER SR </th>
        <th > TOTAL PC TILL DATE </th>
        <th > AVG PC PER DAY PER SR </th>
        <th > TOTAL PC TILL DATE </th>
        <th > AVG PC PER DAY PER SR </th> 
    </tr>
    <?php
    $salet=array();
    $sale1=array();
    $sale2=array();
    $sale3=array();
    $sale4=array();
    $sale5=array();
    $sale6=array();
    $sale7=array();
    $sale8=array();
    $sale9=array();
    $salet1=array();
    $sale11=array();
    $sale21=array();
    $sale31=array();
    $sale41=array();
    $sale51=array();
    $sale61=array();
    $sale71=array();
    $sale81=array();
    $sale91=array();
    ?>
   @foreach ($catSale as $catKey=>$catVal)
        <?php 
        $salet[]=$catVal['salet'];
        $sale1[]=$catVal['sale1'];
        $sale2[]=$catVal['sale2'];
        $sale3[]=$catVal['sale3'];
        $sale4[]=$catVal['sale4'];
        $sale5[]=$catVal['sale5'];
        $sale6[]=$catVal['sale6'];
        $sale7[]=$catVal['sale7'];
        $sale8[]=$catVal['sale8'];
        $sale9[]=$catVal['sale8'];
        
        $sale11[]=round($catVal['sale2']/$workingDays);
        $sale21[]=round($catVal['sale3']/$workingDays);
        $sale31[]=round($catVal['sale4']/$workingDays);
        $sale41[]=round($catVal['sale5']/$workingDays);
        $sale51[]=round($catVal['sale6']/$workingDays);
        $sale61[]=round($catVal['sale7']/$workingDays);
        $sale71[]=round($catVal['sale8']/$workingDays);
        $sale81[]=round($catVal['sale9']/$workingDays);
        $sale91[]=round($catVal['sale1']/$workingDays);
        ?>
    <tr>
      <td><?=$catVal['uname']?></br><?=$catVal['rolename']?> </td>
      <td><?=$catVal['salet']?> </td>
      <td><?=$catVal['sale1']?> </td>
      <td><?=round($catVal['sale1']/$workingDays)?> </td>
      <td><?=$catVal['sale2']?> </td>
      <td><?=round($catVal['sale2']/$workingDays)?> </td>
      <td><?=$catVal['sale3']?> </td>
      <td><?=round($catVal['sale3']/$workingDays)?> </td>
      <td><?=$catVal['sale4']?> </td>
      <td><?=round($catVal['sale4']/$workingDays)?> </td>
      <td><?=$catVal['sale5']?> </td>
      <td><?=round($catVal['sale5']/$workingDays)?> </td>
      <td><?=$catVal['sale6']?> </td>
      <td><?=round($catVal['sale6']/$workingDays)?> </td>
      <td><?=$catVal['sale7']?> </td>
      <td><?=round($catVal['sale7']/$workingDays)?> </td>
      <td><?=$catVal['sale8']?> </td>
      <td><?=round($catVal['sale8']/$workingDays)?> </td>
      <td><?=$catVal['sale9']?> </td>
      <td><?=round($catVal['sale9']/$workingDays)?> </td>
    <tr>
        @endforeach
    <tr>
        <td><strong><?=$userName->uname?></br>(ASM)</strong></td>
        <td><strong><?=array_sum($salet)?></strong> </td>
        <td><strong><?=array_sum($sale1)?></strong> </td>
        <td><strong><?=array_sum($sale11)?></strong> </td>
        <td><strong><?=array_sum($sale2)?></strong> </td>
        <td><strong><?=array_sum($sale21)?></strong> </td>
        <td><strong><?=array_sum($sale3)?></strong> </td>
        <td><strong><?=array_sum($sale31)?></strong> </td>
        <td><strong><?=array_sum($sale4)?></strong></td>
        <td><strong><?=array_sum($sale41)?></strong> </td>
        <td><strong><?=array_sum($sale5)?></strong> </td>
        <td><strong><?=array_sum($sale51)?></strong> </td>
        <td><strong><?=array_sum($sale6)?></strong> </td>
        <td><strong><?=array_sum($sale61)?></strong> </td>
        <td><strong><?=array_sum($sale7)?></strong> </td>
        <td><strong><?=array_sum($sale71)?></strong> </td>
        <td><strong><?=array_sum($sale8)?></strong> </td>
        <td><strong><?=array_sum($sale81)?></strong> </td>
        <td><strong><?=array_sum($sale9)?></strong> </td>
        <td><strong><?=array_sum($sale91)?></strong> </td>
      <tr>
    </table>
    </div>
</div>