<!DOCTYPE html>
<html lang="en">

<div class="searchlistdiv watermark" id="searchlistdiv"     style="border: solid 1px #000;"> 
    <p align="center" style="background-color: #438eb9; color: white;" ><strong> Order Booking <br>
        {{$company_details->title}}<br>
        {{$company_details->address}}</strong>
    </p>
    <table border="1" cellspacing="0" cellpadding="0" height="30px" width="100%">
        <tr>
            <th style="text-align: left; background-color: #438eb9; color: white;"><i class="ace-icon fa fa-map-pin"></i>&nbsp;&nbsp;{{$name_title}} Information</th>
        </tr>
    </table>
    <table border="0" cellspacing="0" cellpadding="0" width="100%">

        <tr>
            <td style="text-align: left;">&nbsp;&nbsp;{{$name_title}} Name:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$upper_data->name}}</a></td>
            <td style="text-align: left;">&nbsp;&nbsp;{{Lang::get('common.location3')}} </td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$upper_data->l3_name}}</a></td>
        </tr>
      
        <tr>
            <td style="text-align: left;">&nbsp;&nbsp;GST No</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$upper_data->tin_no}}</a></td>
             <td style="text-align: left;">&nbsp;&nbsp;Contact No.</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$upper_data->other_numbers}}</a></td> 
            
        </tr>
        <tr>
            <td style="text-align: left;">&nbsp;&nbsp;Date:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$upper_data->sale_date}}</a></td>
            <td style="text-align: left;">&nbsp;&nbsp;Email ID:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$upper_data->email}}</a></td>

        </tr>
        <tr>
        
            <td style="text-align: left;">&nbsp;&nbsp;Order No:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$upper_data->order_id}}</a></td>
            <td style="text-align: left;">&nbsp;&nbsp;Order Booked By:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="">Super Admin</a></td>

        </tr>
      
     
    </table>
    <table border="1" cellspacing="0" cellpadding="0" height="30px" width="100%">
        <tr>
            <th style="text-align: left; background-color: #438eb9; color: white;"><i class="ace-icon fa fa-map-pin"></i>&nbsp;&nbsp;Extra Information</th>
        </tr>
    </table>
    <table border="0" cellspacing="0" cellpadding="0" width="100%">

        <tr>
            <td style="text-align: left;">Dispatch Trhough: </td>
            <td style="text-align: left;">{{$upper_data->dispatch_through}}</td>
            <td style="text-align: left;">Destination: </td>
            <td style="text-align: left;">{{$upper_data->destination}}</td>
            <td style="text-align: left;">Remarks: </td>
            <td style="text-align: left;">{{$upper_data->remarks_c}}</td>
        </tr>
     
    </table>
    <table border="1" cellspacing="0" cellpadding="0" height="30px" width="100%">

        <tr>
            <th style="text-align: left; background-color: #438eb9; color: white;"><i class="ace-icon fa fa-map-pin"></i>&nbsp;&nbsp;{{Lang::get('common.catalog_product')}} Detail</th>
        </tr>
    </table>

    <table id="mytable" width="100%"  class="table table-bordered table-hover" style="overflow-x: scroll;">
        <tr>
            <th>S.No</th>
            <th>Product Name</th>
            <th>Primary Unit</th>
            <th>Weight<br>(Gram)</th> 
            <th>Rate</th>   
            <th>Qty </th>
            <th>Scheme </th>
            <th>Dispatch Qty </th>
            <th>Total Amt <br>(₹)</th>
        </tr>

       
        <tbody class="order_body">
                <?php
                    $grand_weight = 0;
                    $grand_qty = 0;
                    $grand_scheme = 0;
                    $grand_dispatch_qty = 0;
                    $grand_total_amt = 0;
                    $discount_type = 0;
                    $discount_value = 0;
                ?>
            @foreach($lower_data as $key => $value)
            <tr>

                <td>{{$key+1}}</td>            
                <td>{{$value->product_name}}</td>
                <td>{{$value->primary_unit}}</td>
                <td>{{$value->weight}}</td>
                <td>{{$value->rate}}</td>
                <td>{{$value->quantity}}</td>
                <td>{{$value->scheme_qty}}</td>
                <td>{{$value->dispatch_qty}}</td>
                <td>{{$value->total_amt}}</td>
                <?php 
                    $grand_weight += $value->weight;
                    $grand_qty += $value->quantity;
                    $grand_scheme += $value->scheme_qty;
                    $grand_dispatch_qty += $value->dispatch_qty;
                    $grand_total_amt += $value->total_amt;
                    $discount_type = $value->discount_type;
                    $discount_value = $value->discount_value;
                ?>
            </tr>                      
            
            @endforeach
        </tbody>
        <tfoot class="order_foot">
            <tr>
                <td  colspan="3">Grand Total</td>
                <td  id="grand_weight">{{$grand_weight}}</td>
                <td  ></td>
                <td  id="grand_qty">{{$grand_qty}}</td>
                <td  id="grand_scheme">{{$grand_scheme}}</td>
                <td  id="grand_dispatch_qty">{{$grand_dispatch_qty}}</td>
                <td  id="grand_total_amt">{{$grand_total_amt}}</td>
            </tr>
            <tr>
                <td rowspan="4"></td>
                <td rowspan="4"></td>
                <td rowspan="4"></td>
                <td rowspan="4"></td>
                <td rowspan="4" ></td>
                <td rowspan="4" ></td>
                <td rowspan="4" ></td>
                <td>Discount Details : </td>
                <td>{{($discount_type == 1?$discount_value.'%':$discount_value.'₹')}}</td>
            </tr>
            <tr>
                <td>Final Amount : </td>
                <?php 
                    if($discount_type == 1)
                    {
                        $final_amt = ($grand_total_amt*$discount_value)/100;
                    }
                    else
                    {
                        $final_amt = ($grand_total_amt-$discount_value);
                    }
                ?>
                <td colspan="2" id="grand_final_amount">{{$final_amt}}</td>
            </tr>
        </tfoot>
    </table>


</div>
