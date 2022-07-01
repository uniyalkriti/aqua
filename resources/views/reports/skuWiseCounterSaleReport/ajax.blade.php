@if(!empty($CatalogProduct))
    <a onclick="fnExcelReport()" href="javascript:void(0)" class="nav-link">
        <i class="fa fa-file-excel-o"></i> Export Excel</a>
@endif
<style>
    #simple-table table {
        border-collapse: collapse !important;
    }

    #simple-table table, #simple-table th, #simple-table td {
        border: 1px solid black !important;
    }

    #simple-table th {
        background-color: #7BB0FF !important;
        color: black;
    }
</style>
<table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black">
    <tr>
        <td colspan="100"><h3>{{Lang::get('common.skuWiseCounterSaleReport')}}</h3></td>
    </tr>
    <tr>
        <th rowspan="2">{{Lang::get('common.s_no')}}</th>
       
        <th rowspan="2">{{Lang::get('common.location3')}}</th>
        <th rowspan="2">{{Lang::get('common.location4')}}</th>
        <th rowspan="2">{{Lang::get('common.location5')}}</th>
        <th rowspan="2">{{Lang::get('common.location6')}}</th>
        <th rowspan="2">{{Lang::get('common.emp_code')}}</th>
        <th rowspan="2">{{Lang::get('common.username')}}</th>
        <th rowspan="2">{{Lang::get('common.role_key')}}</th>
        <th rowspan="2">{{Lang::get('common.user_contact')}}</th>
        <th rowspan="2">{{Lang::get('common.senior_name')}}</th>

        <th rowspan="2">{{Lang::get('common.retailer')}} Name</th>

        @foreach($CatalogProduct as $ck => $cv)
            @foreach($monthArray as $mk => $mv)
            <th colspan="7">{{$mv}}</th>
            @endforeach
        @endforeach    

        <th rowspan="2">Grand Total</th>

    </tr>

    <tr>
    @foreach($CatalogProduct as $ck => $cv)
        @foreach($monthArray as $mk => $mv)
                <th>SKU Name</th>
            @foreach($finalProductTypeOut as $fpk => $fpv)
                <th>{{$fpv}}</th>
            @endforeach
                <th>Total Sale Value (with Scheme)</th>
        @endforeach
    @endforeach
    </tr>
    

    <tbody>
         
    <?php  
    $i = 1;
    ?>
    @foreach($userDetail as $udk=> $udr)
   
        <?php 
        $user_id = Crypt::encryptString($udr->user_id); 
        $person_id_senior = Crypt::encryptString($udr->person_id_senior); 

        $assignRetailerArray = !empty($userWiseRetailer[$udr->user_id])?$userWiseRetailer[$udr->user_id]:array();
        ?>


            @if(!empty($assignRetailerArray))
                @foreach($assignRetailerArray as $ark => $arv)

                    <?php
                    $retailer_id = Crypt::encryptString($arv->retailer_id); 

                    ?>

                    <tr>
                        <td>{{$i}}</td>
                   
                        <td>{{$udr->state_name}}</td>
                        <td>{{$udr->l4_name}}</td>
                        <td>{{$udr->l5_name}}</td>
                        <td>{{$udr->l6_name}}</td>
                        <td>{{$udr->emp_code}}</td>
                        <td><a href="{{url('user/'.$user_id)}}"> {{$udr->user_name}}</a></td>
                        <td>{{$udr->rolename}}</td>
                        <td>{{$udr->mobile}}</td>
                        <td><a href="{{url('user/'.$person_id_senior)}}">{{!empty($senior_name[$udr->person_id_senior])?$senior_name[$udr->person_id_senior]:''}}</a></td>

                        <td><a href="{{url('retailer/'.$retailer_id)}}"> {{$arv->retailer_name}}</a></td>
                        <?php $finalSum = array(); ?>
                        @foreach($CatalogProduct as $ck => $cv)
                             @foreach($monthArray as $mk => $mv)
                                    <td>{{$cv}}</td>

                                    <?php  
                                        $finalSaleVal = !empty($finalOutDsrSale[$udr->user_id.$arv->retailer_id.$mv.$ck])?$finalOutDsrSale[$udr->user_id.$arv->retailer_id.$mv.$ck]:'0';

                                         $finalSum[] = !empty($finalOutDsrSale[$udr->user_id.$arv->retailer_id.$mv.$ck])?$finalOutDsrSale[$udr->user_id.$arv->retailer_id.$mv.$ck]:'0';
                                     ?>

                                @foreach($finalProductTypeOut as $fk => $fv)
                                    <?php 
                                    // dd($udr->user_id.$arv->retailer_id.$mv.$ck.$fk);
                                    $finalVal = !empty($finalOutDsr[$udr->user_id.$arv->retailer_id.$mv.$ck.$fk]['quantity'])?$finalOutDsr[$udr->user_id.$arv->retailer_id.$mv.$ck.$fk]['quantity']:'-';
                                    
                                   

                                    ?>

                                    <td>{{$finalVal}}</td>

                                @endforeach
                                <td>{{$finalSaleVal}}</td>

                            @endforeach
                        @endforeach
                  
                        <td>{{array_sum($finalSum)}}</td>

                    </tr>
                    <?php $i++; ?>
                @endforeach    
            @endif
    @endforeach  
            
    </tbody>
</table>

<script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>


