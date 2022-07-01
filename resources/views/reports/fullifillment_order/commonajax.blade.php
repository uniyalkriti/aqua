@if(!empty($main_query_data))
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
        <td colspan="16"><h3>{{Lang::get('common.fulfillment_order')}}</h3></td>
    </tr>
    <tr>
        <th >{{Lang::get('common.s_no')}}</th>
    
        <th >{{Lang::get('common.date')}}</th>
        <th >{{Lang::get('common.time')}}</th>
        <th >{{Lang::get('common.order_id')}}</th>
        <th >{{Lang::get('common.location3')}}</th>
        <th >{{Lang::get('common.location4')}}</th>
        <th >{{Lang::get('common.location5')}}</th>
        <th >{{Lang::get('common.location6')}}</th>
        <th >{{Lang::get('common.emp_code')}}</th>
        <th >{{Lang::get('common.username')}}</th>
        <th >{{Lang::get('common.role_key')}}</th>
        <th >{{Lang::get('common.user_contact')}}</th>
        <th >{{Lang::get('common.senior_name')}}</th>
        <th >{{Lang::get('common.retailer')}} Name</th>
        <th >{{Lang::get('common.retailer')}} No</th>
        <th >{{Lang::get('common.details')}}</th>
    </tr>
  
    <tbody>
    <?php 
    $gtotal=0; $gqty=0; $gpieceqty=0; $gweight=0; $i=1; $count_call_status_1=array(); $count_call_0=array();
    $senior_name = App\CommonFilter::senior_name('person');
    ?>

    @if(!empty($main_query_data))
    
    @foreach($main_query_data as $k=> $r)
    @if(!empty($r->order_id))
   
        <?php 
        $user_id = Crypt::encryptString($r->user_id); 
        $senior_user_id = Crypt::encryptString($r->person_id_senior); 
        $retailer_id = Crypt::encryptString($r->retailer_id); 
        // $dealer_id = Crypt::encryptString($r->dealer_id); 
        ?>
        <tr>
            <td>{{$i}}</td>
            <td>{{$r->date}}</td>
            <td>{{$r->time}}</td>
            <td>{{$r->order_id}}</td>
            <td>{{$r->l3_name}}</td>
            <td>{{$r->l4_name}}</td>
            <td>{{$r->l5_name}}</td>
            <td>{{$r->l6_name}}</td>
            <td>{{$r->emp_code}}</td>
            <td><a href="{{url('user/'.$user_id)}}">{{$r->user_name}}</a></td>
            <td>{{$r->rolename}}</td>
            <td>{{$r->mobile}}</td>
            <td><a href="{{url('user/'.$senior_user_id)}}">{{!empty($senior_name[$r->person_id_senior])?$senior_name[$r->person_id_senior]:''}}</a></td>

            <td><a href="{{url('retailer/'.$retailer_id)}}">{{$r->retailer_name}}</a></td>
            <td>{{!empty($r->other_numbers)?$r->other_numbers:$r->landline}}    </td>
  
            <td>
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{Lang::get('common.catalog_4')}}</th> 
                            <th>{{Lang::get('common.case')}} {{Lang::get('common.rate')}}</th>
                            <th>{{Lang::get('common.piece')}} {{Lang::get('common.rate')}}</th>
                            <th>Scheme Qty <br>({{Lang::get('common.case')}})</th>
                            <th>{{Lang::get('common.case')}}</th>
                            <th>{{Lang::get('common.piece')}}</th>
                            <th>{{Lang::get('common.secondary_sale')}}</th>
                        </tr>
                    </thead>
                @if(!empty($r))
                    <?php  $i++; $total=0;
                    $totalqty=0;
                    $totalpieceqty=0;
                    $totalweight=0; ?>
                    @foreach($sub_query_data[$r->order_id] as $k1=>$data1)
                         <tr><td>{{$data1->product_name}}</td>
                         <!-- <td></td> -->
                         <td>{{$data1->product_case_rate}}</td>
                         <td>{{$data1->piece_rate}}</td>
                         <td>{{$data1->product_fullfiment_scheme_qty}}</td>
                         <td>{{$data1->product_fullfiment_cases}}</td>
                         <td>{{$data1->product_fullfiment_qty}}</td>
                         <td>{{($data1->sale_value)}}</td> </tr>
                         <?php 
                         $total+=$data1->sale_value; 
                         $totalqty+=$data1->product_fullfiment_cases;
                         $totalpieceqty+=$data1->product_fullfiment_qty;
                         

                         $gtotal+=$data1->sale_value; 
                         $gqty+=$data1->product_fullfiment_cases;
                         $gpieceqty+=$data1->product_fullfiment_qty;
                         
                         
                         ?>
                    @endforeach
                @else
                    <tr>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td> 
                    </tr>
               @endif
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>{{$totalqty}}</th>
                        <th>{{$totalpieceqty}}</th>
                        <th>{{$total}}</th>
                    </tr>
                </tfoot>
                </table>
            </td>
        </tr>
            @endif
            @endforeach  

            <tr>
            <th colspan="15"><strong>{{Lang::get('common.grand')}} {{Lang::get('common.total')}}</strong></th>
            <!-- <td></td> -->
           
            <td>
                <table class="table">
                    <tr>
                        <th>{{Lang::get('common.total')}} {{Lang::get('common.case')}}</th>
                        <th>{{Lang::get('common.total')}} {{Lang::get('common.piece')}}</th>
                        <th>{{Lang::get('common.total')}} {{Lang::get('common.secondary_sale')}}</th></tr>
                    <tr>
                    <td>{{$gqty}}</td>
                    <td>{{$gpieceqty}}</td>
                    <td>{{$gtotal}}</td></tr>
                </table>
            </td>
            </tr>  
            @else
               <tr>
            <td colspan="14">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>          
        @endif
    </tbody>
</table>



