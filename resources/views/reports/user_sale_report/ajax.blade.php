@if(!empty($records))
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
        <td colspan="21"><h3>{{Lang::get('common.sale_order_report')}}</h3></td>
    </tr>
    <tr>
        <th>{{Lang::get('common.s_no')}}</th>
        <th>{{Lang::get('common.image')}}</th>
        <th>{{Lang::get('common.date')}}</th>
        <th>{{Lang::get('common.order_id')}}</th>
        <th>{{Lang::get('common.location3')}}</th>
        <th>{{Lang::get('common.location4')}}</th>
        <th>{{Lang::get('common.location5')}}</th>
        <th>{{Lang::get('common.location6')}}</th>
        <th>{{Lang::get('common.emp_code')}}</th>
        <th>{{Lang::get('common.username')}}</th>
        <th>{{Lang::get('common.role_key')}}</th>
        <th>{{Lang::get('common.user_contact')}}</th>
        <th>{{Lang::get('common.senior_name')}}</th>
        <th>{{Lang::get('common.distributor')}} Name</th>
        <th>{{Lang::get('common.location7')}} Name</th>
        <th>{{Lang::get('common.retailer')}} Name</th>
        <th>{{Lang::get('common.retailer')}} Number</th>
        <th>Sale {{Lang::get('common.remarks')}}</th>
        <th>Call {{Lang::get('common.status')}}</th>
        <th>No Productive Reason</th>
       
        <th>{{Lang::get('common.details')}}</th>

        @if($company_id == 44 || $company_id == 52)
        <th>Actions</th>
        @endif


    </tr>
    <tbody>
    <?php
     $gtotal=0; $gqty=0; $gweight=0; $i=1; $count_call_status_1=[]; $count_call_0=[];
     $productiveRetailerDateWise = array();
     $NonproductiveRetailerDateWise = array();
     $senior_name = App\CommonFilter::senior_name('person');
     ?>

    @if(!empty($records) && count(array($records))>0)
    
    @foreach($records as $k=> $r)
    @if(count(array($r->order_id))>0)
   
        <?php 
        $user_id = Crypt::encryptString($r->user_id); 
        $senior_id = Crypt::encryptString($r->senior_id); 
        $retailer_id = Crypt::encryptString($r->retailer_id); 
        $dealer_id = Crypt::encryptString($r->dealer_id); 
        ?>
        <tr>
            <td>{{$i}}</td>
            <td>
                  <img id="user_image" style="height: 60px;width: 60px;" class="nav-user-photo"  
                            src="{{asset('order_booking_image/')}}{{!empty($r->image_name)?'/'.$r->image_name:'N/A'}}" alt= " "/>
            </td>
            <td>{{$r->date.' '.$r->time}}</td>
            @if($r->call_status==0 || Auth::user()->company_id !== 50)
                <td><a href="#">{{$r->order_id}}</a></td>
            @else
                <td><a title="PDF Generation" order_id="{{$r->order_id}}" class="myModal2" id="order_id" data-toggle="modal" data-target="#myModal2">{{$r->order_id}}</a></td>
            @endif
            <td>{{$r->l3_name}}</td>
            <td>{{$r->l4_name}}</td>
            <td>{{$r->l5_name}}</td>
            <td>{{$r->l6_name}}</td>
            <td>{{$r->emp_code}}</td>
            <td><a href="{{url('user/'.$user_id)}}"> {{$r->user_name}}</a></td>
            <td>{{$r->role_name}}</td>
            <td>{{$r->mobile}}</td>
            <td><a href="{{url('user/'.$senior_id)}}">{{!empty($senior_name[$r->senior_id])?$senior_name[$r->senior_id]:''}}</a></td>
            <td><a href="{{url('distributor/'.$dealer_id)}}">{{$r->dealer_name}}</a></td>
            <td>{{$r->l7_name}}</td>

            <td><a href="{{url('retailer/'.$retailer_id)}}">{{$r->retailer_name}}</a></td>
            <td>{{!empty($r->retailer_other_number)?$r->retailer_other_number:$r->retailer_landline}}    </td>
            <td>{{!empty($r->remarks)?$r->remarks:'NA'}}    </td>
            @php
                if($r->call_status==1)
                {
                    $count_call_status_1[] = '1';
                    $productiveRetailerDateWise[$r->retailer_id.$r->date] = 'Productive';
                }
            @endphp

            @php
                if($r->call_status==1)
                {
                    $count_call_status_1[] = '1';
                }
                else
                {
                    $count_call_0[] = '1';
                    $NonproductiveRetailerDateWise[$r->retailer_id.$r->date] = 'NonProductive';

                }
            @endphp
                @if($r->call_status=='1')
                    <td style="color:green"><b>Productive</b></td>
                @else
                    <td style="color:red"><b>Non Productive</b></td>
                @endif
            @if($r->non_productive_reason_id == 0)
                <td>{{''}}</td>
                @else
                    <td>{{!empty($non_productive_reason_name[$r->non_productive_reason_id])?$non_productive_reason_name[$r->non_productive_reason_id]:''}}</td>
            @endif
          
            <td>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product Name</th> 
                            <th>Quantity</th>
                            <th>Weight(In KG)</th>
                            <th>Scheme Qty</th>
                            <th>Rate</th>
                            <th>Value</th>
                            <th>Total amount (without the scheme)</th>
                        </tr>
                    </thead>
                    @if(!empty($r))
                        <?php  $i++; $total=0;$ttwn=0;
                        $totalqty=0;
                        $totalweight=0; 
                        // dd($productWeight);
                        ?>

                        @foreach($details[$r->order_id] as $k1=>$data1)
                        <?php $value = 0; 
                        $volume = !empty($productWeight[$data1->product_id])?(int) $productWeight[$data1->product_id]:'0';
                        $quantity_cus = !empty($data1->quantity)?$data1->quantity:'0';
                        // dd($quantity_cus);
                        $finalWeight = (($volume*$quantity_cus)/1000);

                        ?>
                            <tr>
                                <td>{{$data1->product_name}}</td>
                                <td>{{$data1->quantity}}</td>
                                <td>{{$finalWeight}}</td>
                                <td>{{!empty($product_percentage[$data1->product_id.$r->l3_id.$r->date_cus])?$product_percentage[$data1->product_id.$r->l3_id.$r->date_cus].'%':''}}</td>
                                <td>{{$data1->rate}}</td>
                                @if(!empty($product_percentage[$data1->product_id.$r->l3_id.$r->date_cus]))
                                   <?php  $value = ($data1->rate*$data1->quantity)*$product_percentage[$data1->product_id.$r->l3_id.$r->date_cus]/100; ?>

                                @else
                                    <?php $value = 0; ?>
                                @endif
                                <td>{{($data1->rate*$data1->quantity - $value)}}</td> 
                                <td>{{($data1->rate*$data1->quantity)}}</td> 
                            </tr>
                             <?php 
                             $total+=$data1->rate*$data1->quantity - $value; 
                             $totalqty+=$data1->quantity;
                             $totalweight+=$finalWeight;

                             $gtotal+=$data1->rate*$data1->quantity - $value; 
                             $gqty+=$data1->quantity;
                             $gweight+=$finalWeight;

                             $ttwn+=$data1->rate*$data1->quantity;
                             ?>
                        @endforeach
                    @else
                        <tr>
                            <td>-</td>
                            <td>-</td>
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
                            
                            <th>{{$totalqty}}</th>
                            <th>{{$totalweight}}</th>
                            <th>{{$totalweight}}</th>
                            <th></th>
                            <th>{{$total}}</th>
                            <th>{{$ttwn}}</th>
                        </tr>
                    </tfoot>
                </table>
            </td>

            @if($company_id == 44 || $company_id == 52)
            <td>
              <a href="#" orderid="{{ $r->order_id }}" data-toggle="modal"  data-target="#logs_modal" class="logs_modal" title="Update">
                    <div><button class="btn btn-xs btn-info"><i class="ace-icon fa fa-edit bigger-95"></i></button></div>
                </a>
            </td>
            @endif


        </tr>
            @endif
            @endforeach  

            <?php
            $finalNpd = array();
            foreach ($NonproductiveRetailerDateWise as $npkey => $npvalue) {
                if(!empty($productiveRetailerDateWise[$npkey])){
                }else{
                    $finalNpd[$npkey] = 'NonProductive';
                }
            }



            $finalArrayMerge = array_merge($productiveRetailerDateWise,$finalNpd);
            $prd = array();
            $nprd = array();
            foreach ($finalArrayMerge as $fakey => $favalue) {
                
                if($favalue == 'Productive'){
                    $prd[] = '1';
                }else{
                    $nprd[] = '1';
                }


            }


            ?>



            <tr>
                <th colspan="18"><strong>Grand Total</strong></th>
                <td>
                    <b style="color:red">Non Productive Call : </b> <b style="color:red">{{array_sum($nprd)}}</b><br>
                    <b style="color:green">Productive Call : </b><b style="color:green">{{array_sum($prd)}}</b><br>
                </td>
                <td></td>
                <td>
                    <table class="table">
                        <tr>
                            <th>Total Qty</th>
                            <th>Total Weight(in KG)</th>
                            <th>Total Value</th>
                        </tr>
                        <tr>
                            <td>{{$gqty}}</td>
                            <td>{{$gweight}}</td>
                            <td>{{$gtotal}}</td>
                        </tr>
                    </table>
                </td>

                @if($company_id == 44 || $company_id == 52)
                <td>
                </td>
                @endif

            </tr>  
            @else
            <tr>
                <td colspan="20">
                    <p class="alert alert-danger">No data found</p>
                </td>
            </tr>          
        @endif
    </tbody>
</table>

<script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>


<!-- details modal starts here  -->

<div class="modal fade" id="logs_modal" role="dialog">
    <div class="modal-dialog modal-lg">
    
        <!-- Modal content-->
        <div class="modal-content" style="width:1300px;">
            <div class="modal-header widget-header widget-header-small">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title smaller" >  <a onclick="fnExcelReportDetails()" href="javascript:void(0)" class="nav-link">Order Details Export  </a></h4>
            </div>
            <div class="modal-body ui-dialog-content ui-widget-content">
                <form method="get" id="filter_distributor" action="orderDetailsUpdate" enctype="multipart/form-data">

                 
                    <div class="table-header center">
                        <span>Order Details </span>
                    </div>
                  

                        <table id="simple-table-details" class="table table-bordered">
                        
                            <thead>
                                <th>Sr.No</th>
                                <th>Order No.</th>
                                <th>Product Name</th>
                              
                                <th>Quantity</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                
                            </thead>
                          
                            <tbody class="tbody_logs">
                            
                            </tbody>
                    
                        </table>

                        <div class="col-lg-4">
                            <div class="">
                                <button type="submit" class="form-control btn btn-xs btn-purple mt-5"
                                        style="margin-top: 25px">Update
                                </button>
                            </div>
                        </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-right"  data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<script>
        function confirmAction(heading, name, action_id, tab, act) {
            $.confirm({
                title: heading,
                content: 'Are you sure want to ' + act + ' ' + name + '?',
                buttons: {
                    confirm: function () {
                        takeActionForOrder(name, action_id, tab, act);
                        $.alert({
                            title: 'Alert!',
                            content: 'Done!',
                            buttons: {
                                ok: function () {
                                    setTimeout("window.parent.location = ''", 50);
                                }
                            }
                        });
                    },
                    cancel: function () {
                        $.alert('Canceled!');
                    }
                }
            });
        }

        function takeActionForOrder(module, action_id, tab, act) {
            if (action_id != '') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/takeActionForOrder',
                    dataType: 'json',
                    data: {'module': module, 'action_id': action_id, 'tab': tab, 'act': act},
                    success: function (data) {
                        // console.log(data);
                        if (data.code == 401) {
                            //  $('#loading-image').hide();
                        }
                        else if (data.code == 200) {

                        }

                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            }

        }

    
    </script>


<script type="text/javascript">
          $('#simple-table-details').on('keyup','.qty_val',function(){
              var tr=$(this).closest('tr');
        var rateTx   =tr.find('.rate_val').val();
        var stk_val  =tr.find('.amt_val');
        var pieces=$(this).val();

        // var case_qty_val   =tr.find('.case_qty_val').val();
        // var case_rate_val   =tr.find('.case_rate_val').val();

        // var tval=((rateTx*pieces)+(case_rate_val*case_qty_val)).toFixed(2);
        var tval=((rateTx*pieces)).toFixed(2);
        stk_val.val(tval);
  })
  </script>



  <script type="text/javascript">
          $('#simple-table-details').on('keyup','.case_qty_val',function(){
              var tr=$(this).closest('tr');
        var rateTx   =tr.find('.rate_val').val();
        var stk_val  =tr.find('.amt_val');
        var pieces=$(this).val();

        var case_qty_val   =tr.find('.case_qty_val').val();
        var case_rate_val   =tr.find('.case_rate_val').val();

        var tval=((rateTx*pieces)+(case_rate_val*case_qty_val)).toFixed(2);
        stk_val.val(tval);
  })
  </script>


<!-- <script type="text/javascript">
          $('#simple-table-details').on('keyup','.rate_val',function(){
              var tr=$(this).closest('tr');
        var rateTx   =tr.find('.qty_val').val();
        var stk_val  =tr.find('.amt_val');
        var pieces=$(this).val();
        var tval=(rateTx*pieces).toFixed(2);
        stk_val.val(tval);
  })
  </script> -->


<script>
    $('.logs_modal').click(function() {
            var orderid = $(this).attr('orderid');
            if (orderid != '') 
            {
                $('.tbody_logs').html('');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/orderDetails',
                    dataType: 'json',
                    data: "orderid=" + orderid,
                    success: function (data) 
                    {
                        if (data.code == 401) 
                        {
                           
                        }
                        else if (data.code == 200) 
                        {
                            var template = '';
                            var Sno = 1;
                            $.each(data.data_return, function (u_key, u_value) {
                               
                                // template += ('<tr class="odd" role="row"><input type="hidden" name="product_id[]" value='+u_value.product_id+'><input type="hidden" name="order_id[]" value='+u_value.order_id+'><td>'+Sno+'</td><td>'+u_value.order_id+'</td><td>'+u_value.product_name+'</td><td><input type="text" class="case_qty_val" required="required" name="case_qty[]" value='+u_value.case_qty+'></td><td><input type="text" class="case_rate_val" required="required" readonly="readonly" name="case_rate[]" value='+u_value.case_rate+'></td><td><input type="text" class="qty_val" required="required" name="quantity[]" value='+u_value.quantity+'></td><td><input readonly type="text" class="rate_val" required="required" name="rate[]" value='+u_value.rate+'></td><td><input type="text" class="amt_val" readonly="readonly" name="amount[]" value='+u_value.amount+'></td><tr>');


                                 template += ('<tr class="odd" role="row"><input type="hidden" name="product_id[]" value='+u_value.product_id+'><input type="hidden" name="order_id[]" value='+u_value.order_id+'><td>'+Sno+'</td><td>'+u_value.order_id+'</td><td>'+u_value.product_name+'</td><td><input type="text" class="qty_val" required="required" name="quantity[]" value='+u_value.quantity+'></td><td><input readonly type="text" class="rate_val" required="required" name="rate[]" value='+u_value.rate+'></td><td><input type="text" class="amt_val" readonly="readonly" name="amount[]" value='+u_value.amount+'></td><tr>');


                                Sno++;
                            });   
                            $('.tbody_logs').append(template);

                            
                        }
                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            }       
        });
        
    </script>










<script type="text/javascript">
    $('.myModal2').click(function() {
        var order_id = $(this).attr('order_id');
        if (order_id != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/order_wise_pdf_format',
                dataType: 'json',
                data: "order_id=" + order_id,
                success: function (data) {
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data.code == 200) {

                        var save = document.createElement('a');
                        save.href = domain+'/pdf/'+data.pdf_name;
                        save.target = '_blank';
                        save.download = 'order.pdf' || 'unknown';

                        var evt = new MouseEvent('click', {
                            'view': window,
                            'bubbles': true,
                            'cancelable': false
                        });
                        save.dispatchEvent(evt);


                    }

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
        }
        else{
            _append_box.empty();
        }
    });
</script>