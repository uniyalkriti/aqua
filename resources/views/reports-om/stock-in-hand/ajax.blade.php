<?php
//dd($cal[0]);
?>
@if(!empty($records))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o "></i> Export Excel</a>
@endif
<style>
    #simple-table table {
        border-collapse: collapse !important;
    }

    #simple-table table, #simple-table th, #simple-table td {
        border: 1px solid black !important;
    }
    #simple-table th{
        /*background-color: #438EB9 !important;*/
        background-color: #7BB0FF !important;
        color: black;
    }
</style>
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
    <thead>
    <tr>
        <th rowspan="2">BRAND</th>
        <th rowspan="2">SKUs</th>
        <th colspan="4">LANDING COST TO SUPER / DISTRIBUTOR / SUB DISTRIBUTOR</th>
        <th rowspan="2">COMM. {{$m1}}</th>
        <th rowspan="2">% SEC ACH AGAINST COMM.</th>
        <th colspan="3">STOCK IN HAND AS ON {{'1-'.$m1}}</th>
        <th rowspan="2">No. of days as on {{'1-'.$m1}}</th>
        <th rowspan="2">SECONDARY PLAN {{'1-'.$m1}}</th>
        <th rowspan="2">PRIMARY PLAN {{'1-'.$m1}}</th>
        <th rowspan="2">ESTIMATED STOCK IN HAND</th>
        <th rowspan="2">No. of days as on {{'1-'.$m1}}</th>
    </tr>
    <tr>
        <th>{{$m3}}</th>
        <th>{{$m2}}</th>
        <th>{{$m1}}</th>
        <th>AVERAGE</th>
        <th>DISTRIBUTORS</th>
        <th>SUPER DISTRIBUTOR</th>
        <th>TOTAL STOCK</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($records) && count($records)>0)
    @foreach($records as $k=>$d)
        <tr>
            <td>{{$d->catalog_name}}</td>
            <td>{{$d->sku}}</td>
            <td>{{$a=!empty($cal[$k]->m3)?$cal[$k]->m3:0}}</td>
            <td>{{$b=!empty($cal[$k]->m2)?$cal[$k]->m2:0}}</td>
            <td>{{$c=!empty($cal[$k]->m1)?$cal[$k]->m1:0}}</td>
            <td>{{$d=round(($a+$b+$c)/3,2)}}</td>
            <td>{{$e=round(($a+$b+$c),2)}}</td>
            <td>{{$e>0?round(($c/$e)*100,2).'%':0}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    @endforeach
    {{--<tr>--}}
        {{--<td>FCT</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td rowspan="3">FCT Rs. VALUE (LACS)</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>FPC</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td rowspan="2">FPC Rs.VALUE (LACS)</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>MATCH BOX</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td colspan="3">MATCH BOX Rs.VALUE (LACS)</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>ELAICHI</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td rowspan="2">ELAICHI Rs.VALUE (LACS)</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>B-KOOL</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td rowspan="3">B-KOOL Rs.VALUE (LACS)</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>PALLETS</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td rowspan="3">PALLETS Rs.VALUE (LACS)</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>AGARBATTI</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td rowspan="3">AGARBATTI Rs.VALUE (LACS)</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>SILVER LEAVES</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td rowspan="3">SLIVER LEAVES Rs.VALUE (LACS)</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>WAFFERS Rs.5/-</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td rowspan="3">WAFFERS Rs.5/- Rs.VALUE (LACS)</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>WAFFERS Rs.10/-</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td rowspan="3">WAFFERS Rs.10/- Rs.VALUE (LACS)</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td rowspan="11">RINGS & PUFFS</td>--}}
        {{--<td>RINGS TOMATO Rs.5/-</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>RINGS TOMATO Rs.10/-</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>RINGS MASALA Rs.5/-</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>RINGS MASALA Rs.10/-</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>RINGS PUDINA Rs.5/-</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>RINGS PUDINA Rs.10/-</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>RINGS TOMATO Rs.5/-</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>RINGS TOMATO Rs.10/-</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>RINGS MASALA Rs.5/-</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>RINGS MASALA Rs.10/-</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>RINGS PUDINA Rs.5/-</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>RINGS PUDINA Rs.10/-</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td colspan="3">RINGS & PUFFS Rs.VALUE (LACS)</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>TEDA MEDA</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td colspan="3">TEDA MEDA Rs.VALUE (LACS)</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>NAMKEEN</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td colspan="2">NAMKEEN Rs.VALUE (LACS)</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td colspan="2">TOTAL Rs.VALUE (LACS)</td>--}}
    {{--</tr>--}}
    @else
        <tr>
            <td colspan="16">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>