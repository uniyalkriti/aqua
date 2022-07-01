<!DOCTYPE html>
<html lang="en">
<div class="modal-body" id="qwerty">
        <input type="hidden" id="dms_invoice" name="dms_invoice_action_id" value="">
        <input type="hidden" id="dealer_id_invoice" name="dealer_id" value="">
        
        <div class="searchlistdiv watermark" id="searchlistdiv"     style="border: solid 1px #000;"> 
            <p align="center"><strong>{!! $address_invoice !!}</strong>
            </p>
            <table border="1" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <th style="text-align: left;">Party Information</th>
                </tr>
            </table>
            <table border="0" cellspacing="0" cellpadding="0" width="100%">

                <tr>
                    <td style="text-align: left;">&nbsp;&nbsp;Party Name:</td>
                    <td style="text-align: left;">&nbsp;&nbsp;<a id="dealer_name_invoice"></a>{{$data_query->dealer_name}}</td>
                </tr>
              
                <tr>
                    <td style="text-align: left;">&nbsp;&nbsp;GST No</td>
                    <td style="text-align: left;">&nbsp;&nbsp;<a id="gst_no_invoice"></a>{{$data_query->dealer_gst_no}}</td>
                    <td style="text-align: left;">&nbsp;&nbsp;State: </td>
                    <td style="text-align: left;">&nbsp;&nbsp;<a id="state_name_invoice"></a>{{$data_query->dealer_address}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">&nbsp;&nbsp;Contact No.</td>
                    <td style="text-align: left;">&nbsp;&nbsp;<a id="dms_mobile_invoice"></a>{{$data_query->dealer_mobile}}</td> 
                    <td style="text-align: left;">&nbsp;&nbsp;Order Date:</td>
                    <td style="text-align: left;">&nbsp;&nbsp;<a id="order_date_invoice"></a>{{$data_query->sale_date}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">&nbsp;&nbsp;Email ID:</td>
                    <td style="text-align: left;">&nbsp;&nbsp;<a id="email_id_invoice"></a>{{$data_query->dealer_email}}</td>
                
                    <td style="text-align: left;">&nbsp;&nbsp;Order No:</td>
                    <td style="text-align: left;">&nbsp;&nbsp;<a id="order_no_invoice"></a>{{$data_query->order_id}}</td>
                </tr>
              
                <tr>
                    <td style="text-align: left;">&nbsp;&nbsp;Party Type: </td>
                    <td style="text-align: left;">&nbsp;&nbsp;Distributor</td>
                    <td style="text-align: left;">&nbsp;&nbsp;Order Booked By:</td>
                    <td style="text-align: left;">&nbsp;&nbsp;<a id="fullname_invoice"></a>{{$data_query->dealer_name}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">&nbsp;&nbsp;Remaks</td>
                    <td style="text-align: left;">&nbsp;&nbsp;<a id="remarks_invoice"></a>{{$data_query->dispatch_remarks}}</td>
                </tr>
            </table>
            <table border="1" cellspacing="0" cellpadding="0" width="100%">

                <tr>
                    <th style="text-align: left;">Transport Detail</th>
                </tr>
            </table>
            <table border="0" cellspacing="0" cellpadding="0" width="100%">

                <tr>
                    <td style="text-align: left;">&nbsp;&nbsp;Plant Name:</td>
                    <td style="text-align: left;">&nbsp;&nbsp;<a id='plant_name_invoice'></a>{{!empty($transport_details->plant_name)?$transport_details->plant_name:''}}</td>
                    <td style="text-align: left;">&nbsp;&nbsp;GR No.:</td>
                    <td style="text-align: left;">&nbsp;&nbsp;<a id='gr_no_invoice'></a>{{!empty($transport_details->gr_no)?$transport_details->gr_no:''}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">&nbsp;&nbsp;Transport Type:  </td>
                    <td style="text-align: left;">&nbsp;&nbsp;<a id='travell_mode'></a>{{!empty($transport_details->tavel_mode_name)?$transport_details->tavel_mode_name:''}}</td>
                    <td style="text-align: left;">&nbsp;&nbsp;Driver Name:</td>
                    <td style="text-align: left;">&nbsp;&nbsp;<a id='driver_name_invoice'></a>{{!empty($transport_details->driver_name)?$transport_details->driver_name:''}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">&nbsp;&nbsp;Transport Name:</td>
                    <td style="text-align: left;">&nbsp;&nbsp;<a id='transport_name_invoice'></a>{{!empty($transport_details->transport_name)?$transport_details->transport_name:''}}</td>
                    <td style="text-align: left;">&nbsp;&nbsp;Driver Contact No.</td>
                    <td style="text-align: left;">&nbsp;&nbsp;<a id='driver_number_invoice'></a>{{!empty($transport_details->driver_number)?$transport_details->driver_number:''}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">&nbsp;&nbsp;Vehicle No.</td>
                    <td style="text-align: left;">&nbsp;&nbsp;<a id='vehical_number_invoice'></a>{{!empty($transport_details->vehical_number)?$transport_details->vehical_number:''}}</td>
                    <td style="text-align: left;">&nbsp;&nbsp;Freight: Rs.</td>
                    <td style="text-align: left;">&nbsp;&nbsp;<a id='freight_invoice'></a>{{!empty($transport_details->freight)?$transport_details->freight:''}}</td>
                </tr>
            </table>
            <table id="mytable" width="100%" border="1" cellspacing="2" cellpadding="2" class="tableform">
                <tr>
                <th>S.No</th>
                <th>Description</th>
                <th>Rate</th>   
                <th>Qty <br>(Cases)</th>
                <th>Scheme <br>(Cases)</th>
                <th>Total Amt <br>(₹)</th>
                
                <th>Weight<br>(Kg)</th> 
                <th>MFG Date</th> 
                <th>Batch No</th>
                </tr>

               
                <tbody class="dms_invoice_body">
                    @if(!empty($invoice_data))
                    @foreach($invoice_data as $k=> $r)
                        <tr>
                            <td>{{$k+1}}</td>
                            <td>{{$r->product_name}}</td>
                            <td>{{$r->cases_rate}}</td>
                            <td>{{$r->cases}}</td>
                            <td>{{$r->scheme_qty}}</td>
                            <td>{{$r->cases_rate*$r->cases}}</td>
                            <?php
                             $weight = !empty($r->weight)?intval($r->weight):'0';
                            ?>
                            <td>{{ ($weight/1000)*($r->cases+$r->scheme_qty) }}</td>
                            <td>{{!empty($r->mfg_date)?$r->mfg_date:''}}</td>
                            <td>{{$r->batch_no}}</td>
                            
                        </tr>
                    @endforeach
                    @else

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                    @endif
                </tbody>
            </table>     

        </div>

</div>
