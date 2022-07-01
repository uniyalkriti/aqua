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

    #simple-table th {
        /*background-color: #438EB9 !important;*/
        background-color: #7BB0FF !important;
        color: black;
    }
</style>
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
<?php
 $countp=count($product);
 $supercol = $countp+20;
 ?>
    <tr>
    <tr>
    <td colspan="<?=$supercol?>"><h3>STATE WISE ,USER WISE EATOS & AGARBATTI REPORT FROM {{date('d-m-Y',strtotime($start_date))}} TO {{date('d-m-Y',strtotime($end_date))}}</h3>
    </td>
    </tr>
    <tr>
    <th rowspan="2">Sr.NO</th>
    <th rowspan="2">ZONE</th>
    <th rowspan="2">STATE</th>
    <th rowspan="2">TOWN</th>
    <th rowspan="2"> Distributor </th>
    <th rowspan="2">USER NAME</th>
    <th rowspan="2">DESIGNATION</th>
    <th rowspan="2">WORKING WITH</th>
    <th rowspan="2">PRODUCTIVE CALLS ( EATOS )</th>
    <th rowspan="2">PRODUCTIVE CALLS( AGARBATTIS )</th>
    @if(!empty($catalog_2))
    <?php
    $count=count($catalog_2);
    foreach($catalog_2 as $key2=>$data2) {
    echo "<th colspan=".$data2->numproduct.">".$data2->name."</th>";
    }
    ?>
    @endif
   
</tr>
<tr>

@if(!empty($product))
    <?php
   
    foreach($product as $key1=>$data1) {
    echo "<th >".$data1->name."</th>";
    }
    ?>
    @endif

</tr>

        
    <tbody>
    <?php
     $i = 1;
     $total_productive_calls = array();
     $total_aggarbati = array();
     $total_rv = array();
    ?>
    @if(!empty($details))
        @foreach($details as $key=>$data)

       <?php
        $concat_data = $data['date'].$data['user_id'];
        //dd($concat_data);
        $total_productive_calls[] = !empty($eatos_data[$concat_data])?$eatos_data[$concat_data]:0;

        $total_aggarbati[] = !empty($aggarbati_data[$concat_data])?$aggarbati_data[$concat_data]:0;
        
       ?>
            <tr>
              
                <td>{{$i}}</td>
                <td>{{$data["l1_name"]}}</td>
                <td>{{$data["l3_name"]}}</td>
                <td>{{$data["l4_name"]}}</td>
                
               
                <td>{{$data["dealer_name"]}}</td>
                <td>{{$data["user_name"]}}</td>
                <td>{{$data["role_name"]}}</td>

                
                <td>{{!empty($working_with[$concat_data])?$working_with[$concat_data]:'N.A'}}</td>
                <td>{{ !empty($eatos_data[$concat_data])?$eatos_data[$concat_data]:0 }}</td>
                <td>{{ !empty($aggarbati_data[$concat_data])?$aggarbati_data[$concat_data]:0 }}</td>
                @if(!empty($product))
                    @foreach($product as $key1=>$data1)  
                    <?php 
                    $total_rv[$data1->id][] = !empty($total_sale_value[$concat_data.$data1->id])?$total_sale_value[$concat_data.$data1->id]:0; 
                    ?>    

                    <td>{{ !empty($total_sale_value[$concat_data.$data1->id])?$total_sale_value[$concat_data.$data1->id]:0 }}</td>

                    @endforeach
                @endif      
            </tr>
            <?php $i++;?>
        @endforeach

        <tr>
            <th colspan="8">TOTAL</th>
            <th>{{ array_sum($total_productive_calls) }}</th>
            <th>{{ array_sum($total_aggarbati) }}</th>
            @if(!empty($product))
        @foreach($product as $key1=>$data1)      
        <th>{{ array_sum($total_rv[$data1->id]) }}</th>
        @endforeach
    @endif      
        </tr>
    @endif
    </tbody>
</table>