<a onclick="fnExcelReport()" href="javascript:void(0)"
   class="nav-link"><i class="fa fa-file-excel-o "></i> Export Excel</a>
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
    <tr>
        <td colspan="2" class="bg-primary">
            OUTLET CATEGORISATION OF THIS BEAT ROUTE
        </td>
        @if(!empty($outlet_categories))
            @foreach($outlet_categories as $outlet)
                <td>
                    {{$outlet->outlet_name.': '}} <b>{{$outlet->total}}</b>
                </td>
            @endforeach
        @endif
    </tr>
    <tr>
        <td colspan="2" class="bg-primary">
            OUTLET CLASSIFICATION ON THE BASIS OF MONTHLY PURCHASE
        </td>
        <td>
            PLATINUM OUTLET: <b>@if(!empty($platinum->count)){{$platinum->count}} @else 0 @endif</b>
        </td>
        <td>
            DIAMOND OUTLET: <b>@if(!empty($diamond->count)){{$diamond->count}} @else 0 @endif</b>
        </td>
        <td>
            GOLD OUTLET: <b>@if(!empty($gold->count)){{$gold->count}} @else 0 @endif</b>
        </td>
        <td>
            SILVER OUTLET: <b>@if(!empty($silver->count)){{$silver->count}} @else 0 @endif</b>
        </td>
        <td>
            SEMI WHOLE SELLER: <b>@if(!empty($semi_wholeseller->count)){{$semi_wholeseller->count}} @else 0 @endif</b>
        </td>
        <td>
            WHOLE SELLER: <b>@if(!empty($wholeseller->count)){{$wholeseller->count}} @else 0 @endif</b>
        </td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr class="bg-info">
        <td>S.No</td>
        <td>ZONE</td>
        <td>STATE</td>
        <td>TOWN</td>
        <td>DISTRIBUTOR</td>
        <td>OUTLET ID</td>
        <td>OUTLET NAME</td>
        <td>EXISTING BEAT</td>
        <td>PROPOSED BEAT</td>
        <td>OUTLET CATEGORY</td>
        <td>OUTLET CLASS</td>
    </tr>

        @if(!empty($rows))
            @php
                $class_arr=[0=>'none',1=>'Platinum',2=>'Diamond',3=>'Gold',4=>'Silver'];
            @endphp
            @foreach($rows as $key=>$row)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$row->zone}}</td>
                <td>{{$row->state}}</td>
                <td>{{$row->town}}</td>
                <td>{{$row->dealer_name}}</td>
                <td>{{!empty($row->id)?'#'.$row->id:'N/A'}}</td>
                <td>{{$row->outlet_name}}</td>
                <td>{{$row->beat}}</td>
                <td>{{$row->beat}}</td>
                <td>{{$row->outlet_category}}</td>
                <td>{{isset($class_arr[$row->class])?$class_arr[$row->class]:''}}</td>
            </tr>
            @endforeach
        @endif
</table>