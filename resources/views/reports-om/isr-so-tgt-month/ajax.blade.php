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
        <th rowspan="2">S.No</th>
        <th rowspan="2">CODE</th>
        <th rowspan="2">STATE</th>
        <th rowspan="2">NAME OF ISR/SO</th>
        <th rowspan="2">DESIG.</th>
        <th colspan="6">TGT VS. ACH <span class="badge badge-dark">{{strtoupper($last_month_from_filter)}}</span> & GROWTH TREND</th>
        <th colspan="6">TGT VS. ACH <span class="badge badge-dark">{{strtoupper($first_month_from_filter)}}</span> TO <span class="badge badge-dark">{{strtoupper($last_month_from_filter)}}</span> & GROWTH TREND</th>
        <th colspan="3">ACTION PLAN____________</th>
        <th colspan="7">E TO S</th>
    </tr>
    <tr>
        <th>TGT.</th>
        <th>ACH</th>
        <th>% ACH</th>
        <th>GAP( +/-)</th>
        <th>ACH LAST YEAR SAME MONTH</th>
        <th>% GR</th>
        <th>TGT.</th>
        <th>ACH</th>
        <th>% ACH</th>
        <th>GAP( +/-)</th>
        <th>ACH LAST YEAR SAME PERIOD</th>
        <th>% GR</th>
        <th>LAST YEAR SAME MONTH</th>
        <th>TGT THIS MONTH</th>
        <th>POA THIS MONTH</th>
        <th>ISR COST( RS.)</th>
        <th>TGT ( PER KG)</th>
        <th>ACTUAL COST PER KG</th>
        <th>VARIANCE</th>
        <th>TGT %</th>
        <th>ACTUAL %</th>
        <th>VARIANCE</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($records) && count($records)>0)
    @foreach($records as $key=>$data)
        <tr>
            <td>{{$key+1}}</td>
            <td>{{$data->position_name}}</td>
            <td>{{$data->state}}</td>
            <td>{{!empty($data->middle_name)?$data->first_name.' '.$data->middle_name.' '.$data->last_name:$data->first_name.' '.$data->last_name}}</td>
            <td>{{$data->rolename}}</td>
            {{--<td>{{$f=$data->total_rd}}</td>--}}
            <td>{{$f=!empty($last_month_user[$data->pid]->total_rd)?$last_month_user[$data->pid]->total_rd:0}}</td>
            <td>{{$g=!empty($last_month_user[$data->pid]->total_arch)?$last_month_user[$data->pid]->total_arch:0}}</td>
            <td>{{!empty($f) && $f?round($g/$f,2):0}}</td>
            <td>{{$g-$f}}</td>
            <td>{{$j=!empty($last_month_user[$data->pid]->lysm)?$last_month_user[$data->pid]->lysm:0}}</td>
            <td>{{!empty($j) && $j>0?$g/$j*100-100:0}}</td>
            <td>{{$l=$f*8}}</td>
            <td>{{$m=$data->total_arch}}</td>
            <td>{{!empty($l) && $l>0?round($m/$l,2):0}}</td>
            <td>{{$m-$l}}</td>
            <td>{{$p=!empty($data->lysm)?$data->lysm:0}}</td>
            <td>{{!empty($p) && $p>0?$m/$p*100-100:0}}</td>
            <td></td>
            <td></td>
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
    @else
        <tr>
            <td colspan="52">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>