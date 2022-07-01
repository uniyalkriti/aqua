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
        <td colspan="14"><h3>Order Fulfillment List Report</h3></td>
    </tr>
    <tr>
        <th >S.No.</th>
    
        <th >Date</th>
        <th >Time</th>
        <th >Order No</th>
        <th >{{Lang::get('common.location2')}}</th>
        <th >{{Lang::get('common.location3')}}</th>
        <th >User Name</th>
        <th >Retailer Name</th>
        <th >Retailer No</th>
        <th >Details</th>
    </tr>
  
    <tbody>
    <?php $gtotal=0; $gqty=0; $gpieceqty=0; $gweight=0; $i=1; $count_call_status_1=array(); $count_call_0=array();?>

    @if(!empty($main_query_data))
    
    @foreach($main_query_data as $k=> $r)
    @if(!empty($r->order_id))
   
        <?php 
        $user_id = Crypt::encryptString($r->user_id); 
        $retailer_id = Crypt::encryptString($r->retailer_id); 
        // $dealer_id = Crypt::encryptString($r->dealer_id); 
        ?>
        <tr>
            <td>{{$i}}</td>
            <td>{{$r->date}}</td>
            <td>{{$r->time}}</td>
            <td>{{$r->order_id}}</td>
            <td>{{$r->l2_name}}</td>
            <td>{{$r->l3_name}}</td>
            <td><a href="{{url('user/'.$user_id)}}">{{$r->user_name}}</a></td>

            <td><a href="{{url('retailer/'.$retailer_id)}}">{{$r->retailer_name}}</a></td>
            <td>{{!empty($r->other_numbers)?$r->other_numbers:$r->landline}}    </td>
  
            <td>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product Name</th> 
                            <th>Case Rate</th>
                            <th>Piece Rate</th>
                            <th>Scheme Qty <br>(CASES)</th>
                            <th>Cases</th>
                            <th>Pieces</th>
                            <th>Value</th>
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
            <th colspan="9"><strong>Grand Total</strong></th>
            <!-- <td></td> -->
           
            <td>
                <table class="table">
                    <tr>
                        <th>Total Cases</th>
                        <th>Total Pieces</th>
                        <th>Total Value</th></tr>
                    <tr>
                    <td>{{$gqty}}</td>
                    <td>{{$gpieceqty}}</td>
                    <td>{{$gtotal}}</td></tr>
                </table>
            </td>
            </tr>  
            @else
               <tr>
            <td colspan="9">
                <p class="alert alert-danger">No data found</p>
            </td>
        </tr>          
        @endif
    </tbody>
</table>



