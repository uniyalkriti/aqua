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
    <tr><td colspan="20"><h3>{{Lang::get('common.beat_route')}}</h3></td></tr>
    <tr>
        <td colspan="2" class="bg-primary">
            {{Lang::get('common.retailer')}} CATEGORISATION OF THIS BEAT ROUTE
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
            {{Lang::get('common.retailer')}} CLASSIFICATION ON THE BASIS OF MONTHLY PURCHASE
        </td>
        <td>
            PLATINUM {{Lang::get('common.retailer')}}: <b>@if(!empty($platinum->count)){{$platinum->count}} @else 0 @endif</b>
        </td>
        <td>
            DIAMOND {{Lang::get('common.retailer')}}: <b>@if(!empty($diamond->count)){{$diamond->count}} @else 0 @endif</b>
        </td>
        <td>
            GOLD {{Lang::get('common.retailer')}}: <b>@if(!empty($gold->count)){{$gold->count}} @else 0 @endif</b>
        </td>
        <td>
            SILVER {{Lang::get('common.retailer')}}: <b>@if(!empty($silver->count)){{$silver->count}} @else 0 @endif</b>
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
        <td>{{Lang::get('common.s_no')}}</td>
      <!--   <td>{{Lang::get('common.location1')}}</td>
        <td>{{Lang::get('common.location2')}}</td> -->
        <td>{{Lang::get('common.location3')}}</td>
        <td>{{Lang::get('common.location4')}}</td>
        <td>{{Lang::get('common.location5')}}</td>
        <td>{{Lang::get('common.location6')}}</td>
        <td>{{Lang::get('common.retailer')}} {{Lang::get('common.created_date')}}</td>
        <td>{{Lang::get('common.retailer')}} ID</td>


        <td>{{Lang::get('common.distributor')}}</td>
        <td>{{Lang::get('common.retailer')}} NAME</td>
        <td>EXISTING {{Lang::get('common.location7')}}</td>
        <td>PROPOSED {{Lang::get('common.location7')}}</td>
        <td>{{Lang::get('common.retailer')}} CATEGORY</td>
        <td>{{Lang::get('common.retailer')}} CLASS</td>
    </tr>

        @if(!empty($rows))
            @php
                $class_arr=[0=>'none',1=>'Platinum',2=>'Diamond',3=>'Gold',4=>'Silver'];
            @endphp
            @foreach($rows as $key=>$row)
            <?php
              $retailer_id = Crypt::encryptString($row->id); 
              $dealer_id = Crypt::encryptString($row->dealer_id); 
            ?>
            <tr>
                <td>{{$key+1}}</td>
              <!--   <td>{{$row->zone}}</td>
                <td>{{$row->region}}</td> -->
                <td>{{$row->state}}</td>
                <td>{{$row->l4_name}}</td>
                <td>{{$row->l5_name}}</td>
                <td>{{$row->town}}</td>
                <td>{{!empty($row->created_on)?date("d-M-Y",strtotime($row->created_on)):$row->created_on}}</td>
                <td>{{!empty($row->id)?'#'.$row->id:'N/A'}}</td>
                

                <td><a href="{{url('distributor/'.$dealer_id)}}">{{$row->dealer_name}}</a></td>
                <td><a href="{{url('retailer/'.$retailer_id)}}">{{$row->outlet_name}}</a></td>
                <td>{{$row->beat}}</td>
                <td>{{$row->beat}}</td>
                <td>{{$row->outlet_category}}</td>
                <td>{{isset($class_arr[$row->class])?$class_arr[$row->class]:''}}</td>
            </tr>
            @endforeach
        @endif
</table>