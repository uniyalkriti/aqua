<div class="searchlistdiv watermark" id="searchlistdiv"     style="border: solid 1px #000;"> 
<form action="submit_manual_order_booking" method="post" id="submit_manual_order_booking_id">
    <p align="center" style="background-color: #438eb9; color: white;" ><strong> <a style="height:70px; color:white;">Order Booking</a> <br>
        <a style="height:70px; color:white;"> {{$company_details->title}}</a><br>
        {{$company_details->address}}</strong>
    </p>
    <table border="1" cellspacing="0" cellpadding="0" height="30px" width="100%">
        <tr>
            <th style="text-align: left; background-color: #438eb9; color: white;"><i class="ace-icon fa fa-map-pin"></i>&nbsp&nbsp{{$name_title}} Information</th>
        </tr>
    </table>
    <table border="0" cellspacing="0" cellpadding="0" width="100%">

        <tr>
            <input type="hidden" name="party_id" id='party_id' value="{{$party_id}}">
            <input type="hidden" name="party_name" id='party_name' value="{{$name_title}}">
            <td style="text-align: left;">&nbsp;&nbsp;{{$name_title}} Name:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$details->name}}</a></td>
            <td style="text-align: left;">&nbsp;&nbsp;{{Lang::get('common.location3')}} </td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$details->tin_no}}</a></td>
        </tr>
      
        <tr>
            <td style="text-align: left;">&nbsp;&nbsp;GST No</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$details->tin_no}}</a></td>
             <td style="text-align: left;">&nbsp;&nbsp;Contact No.</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$details->other_numbers}}</a></td> 
            
        </tr>
        <tr>
            <td style="text-align: left;">&nbsp;&nbsp;Date:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$date}}</a></td>
            <td style="text-align: left;">&nbsp;&nbsp;Email ID:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$details->email}}</a></td>

        </tr>
        <tr>
        
            <td style="text-align: left;">&nbsp;&nbsp;Order No:</td>
            <input type="hidden" name="order_no" value="{{$order_no}}">
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$order_no}}</a></td>
            <td style="text-align: left;">&nbsp;&nbsp;Order Booked By:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="">Super Admin</a></td>

        </tr>
      
     
    </table>
    <table border="1" cellspacing="0" cellpadding="0" height="30px" width="100%">
        <tr>
            <th style="text-align: left; background-color: #438eb9; color: white;"><i class="ace-icon fa fa-map-pin"></i>&nbsp&nbspExtra Information</th>
        </tr>
    </table>
    <table border="0" cellspacing="0" cellpadding="0" width="100%">

        <tr>
            <td style="text-align: left;">&nbsp;Dispatch Trhough: </td>
            <td style="text-align: left;">&nbsp;<input  type="text" required placeholder="Enter Dispatch" name="dispatch" id=""></td>
            <td style="text-align: left;">&nbsp;Destination: </td>
            <td style="text-align: left;">&nbsp;<input  type="text" required placeholder="Enter Destination" name="destination" id=""></td>
            <td style="text-align: left;">&nbsp;Remarks: </td>
            <td style="text-align: left;">&nbsp;<input  type="text" required placeholder="Enter Remarks" name="remarks" id=""></td>
        </tr>
     
    </table>
    <table border="1" cellspacing="0" cellpadding="0" height="30px" width="100%">

        <tr>
            <th style="text-align: left; background-color: #438eb9; color: white;"><i class="ace-icon fa fa-map-pin"></i>&nbsp&nbsp{{Lang::get('common.catalog_product')}} Detail</th>
        </tr>
    </table>

    <table id="mytable" width="100%"  class="table table-bordered table-hover" style="overflow-x: scroll;">

        <th>S.No</th>
        <th>Product Name</th>
        <th>Primary Unit</th>
        <th>Weight<br>(Gram)</th> 
        <th>Rate</th>   
        <th>Qty </th>
        <th>Scheme </th>
        <th>Dispatch Qty </th>
        <th>Total Amt <br>(₹)</th>


       
        <tbody class="order_body">
            <tr>
            <td>1</td>            
            <td>
                <select  required="required" onchange="return product_function(this.id)"  name="product_id[]"
                        id="product_id1" class="form-control chosen-select-new">
                    <option value="">==Select Product==</option>
                    @if(!empty($product_details))
                        @foreach($product_details as $p_key=>$p_value)
                            <option value="{{$p_key}}">{{$p_value}}</option>
                        @endforeach
                    @endif
                </select>
            </td>
            <td>
                <input style="width: 75px;" type="text"  readonly="readonly" name="primary_unit[]" id="primary_unit1">
            </td>            
            <td>
                <input style="width: 75px;" type="text"value="0"  readonly="readonly" name="weight[]" id="weight1">
            </td>            
            <td>
                <input style="width: 75px;" type="text" required="required" name="rate[]" id="rate1">
            </td>            
            <td>
                <input style="width: 75px;" type="text" required="required" value="0"  name="qty[]" id="qty1" onkeyup="return mulfunc(this.id)">
            </td>
            <td>
                <input style="width: 75px;" type="number" value="0"  readonly name="scheme[]" id="scheme1" >
            </td>
            <td>
                <input style="width: 75px;" type="number"value="0"  readonly="readonly" name="dispatch_qty[]" id="dispatch_qty1">
            </td>

            <td>
                <input style="width: 75px;" type="text" value="0" readonly="readonly" name="total_amt[]" id="total_amt1">
            </td>
            <td width="70px" ><i  title="more" id="sr_no1" class="fa fa-plus" onclick="return addfunction()" ></i></td>  
            </tr>                      
        </tbody>
        <tfoot class="order_foot">
            <tr>
                <td rowspan="4" colspan="3">Grand Total</td>
                <td rowspan="4" id="grand_weight"></td>
                <td rowspan="4" ></td>
                <td rowspan="4" id="grand_qty"></td>
                <td  id="grand_scheme"></td>
                <td id="grand_dispatch_qty"></td>
                <td id="grand_total_amt"></td>
            </tr>
            <tr>
                <td>Discount Details : </td>
                <td>
                    <select     name="discount_type"
                        id="discount_type" class="chosen-select-new">
                        <option value="">==Select Discount Type==</option>
                        <option value="1">%</option>
                        <option value="2">₹</option>
                    </select>
                </td>
                <td>
                    <input  value="0" type="text"  name="discount_value" onkeyup="return mulfuncDiscount()" id="discount_value">
                </td>
            </tr>
            <tr>
                <td>Final Amount : </td>
                <input type="hidden" name="input_grand_final_amount" id="input_grand_final_amount" value="">
                <td colspan="2" id="grand_final_amount"></td>
            </tr>
        </tfoot>
    </table>
    @if($name_title == 'Distributor') 
    <table border="1" cellspacing="0" cellpadding="0" height="30px" width="100%">

        <tr>
            <th style="text-align: center;"><button type="submit" class="btn btn-primary" id="submit_form"> Submit </button></th>
            <th style="text-align: center;">Email :  </th>
            <th ><input type="text" placeholder="Enter Multiple E-mail" name="email_sent"> </th>
        </tr>
    </table>    
    @endif
</form>
</div>
    <script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script type="text/javascript">
         $(".chosen-select-new").chosen();
        $('button').click(function () {
            $(".chosen-select-new").trigger("chosen:updated");
        });
    </script>

<script>

    function product_function(str)
    {
        var d=str.substr(10,3);
        var product_id= document.getElementById("product_id"+d).value;
        var party_id = $('#party_id').val();
        var party_name = $('#party_name').val();
        // alert(product_id);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "post",
            url: domain + '/manual_orderproduct_details',
            dataType: 'json',
            data: {'product_id': product_id, 'party_id': party_id,'party_name': party_name},
            success: function (data) 
            {
                if(data.code == 401)
                {
                    $.alert('Something Went Wrong Contact To Development Team !!');
                }
                else if(data.code == 200)
                {   
                    // con
                    $('#weight'+d).val(data.product_details.weight);
                    $('#rate'+d).val(data.product_details.rate);
                    $('#primary_unit'+d).val(data.product_details.type_name);
                }
            }
        });

    }
        var cust_id = 2;
        // console.log(cust_id);
        function addfunction()
        {
            // console.log(sr_no);
            var product_filter = `<select  required="required" onchange="return product_function(this.id)"  name="product_id[]"
                                        id="product_id${cust_id}" class="chosen-select-new">
                                    <option value="">==Select Product==</option>
                                    @if(!empty($product_details))
                                        @foreach($product_details as $p_key=>$p_value)
                                            <option value="{{$p_key}}">{{$p_value}}</option>
                                        @endforeach
                                    @endif
                                </select>`;
            var primary_unit = `<input style="width: 75px;" type="text" readonly="readonly" name="primary_unit[]" id="primary_unit${cust_id}">`;

            var weight = `<input style="width: 75px;" value="0" type="text" readonly="readonly" name="weight[]" id="weight${cust_id}">`;

            var rate = `<input style="width: 75px;" type="text" required name="rate[]" id="rate${cust_id}">`;

            var qty = `<input style="width: 75px;" value="0" type="text"  name="qty[]" id="qty${cust_id}" onkeyup="return mulfunc(this.id)">`;

            var scheme = `<input style="width: 75px;"  value="0"type="number" readonly name="scheme[]"  id="scheme${cust_id}">`;
            var dispatch_qty = `<input style="width: 75px;" value="0" type="number" readonly  name="dispatch_qty[]" id="dispatch_qty${cust_id}">`;

            var total_amt = `<input style="width: 75px;" value="0" type="text" readonly="readonly" name="total_amt[]" id="total_amt${cust_id}">`;
            var discount_value = `<input  value="0" type="text" onkeyup="return mulfuncDiscount()" name="discount_value" id="discount_value">`;
            var discount_filter = `<select     name="discount_type"
                                        id="discount_type" class="chosen-select-new">
                                        <option value="">==Select Discount Type==</option>
                                        <option value="1">%</option>
                                        <option value="2">₹</option>
                                    </select>`;
                
            var final_amount =`<td colspan="2" id="grand_final_amount"></td>`; 

         

            var template = ('<tr><td>'+cust_id+'</td><td>'+product_filter+'</td><td>'+primary_unit+'</td><td>'+weight+'</td><td>'+rate+'</td><td>'+qty+'</td><td>'+scheme+'</td><td>'+dispatch_qty+'</td><td>'+total_amt+'</td><td width="70px" ><i id=sr_no'+cust_id+' title="more" class="fa fa-plus" aria-hidden="true" onclick="return addfunction()"></i>&nbsp&nbsp<i  title="Less"  class="removenewrow fa fa-minus"/></i></td></tr>');
            $('.order_body').append(template);

            $('.order_foot').html('');
            var template_foot =  ("<tr><td rowspan='4' colspan = '3'>Grand Total</td><td rowspan='4' id='grand_weight'></td><td rowspan='4'></td><td rowspan='4' id='grand_qty'></td><td id='grand_scheme'></td><td id='grand_dispatch_qty'></td><td id='grand_total_amt'></td></tr><tr><td>Discount Details : </td><td>"+discount_filter+"</td><td>"+discount_value+"</td></tr><tr><td>Final Amount : </td<td>"+final_amount+"</td>></tr>");
            $('.order_foot').append(template_foot);

            
            
           cust_id++;

            var grand_weight = 0;
            var grand_qty = 0;
            var grand_scheme = 0;
            var grand_dispatch_qty = 0;
            var grand_total_amt = 0;
           
            
            var g_weight = document.getElementsByName('weight[]');
            var g_qty = document.getElementsByName('qty[]');
            var g_scheme = document.getElementsByName('scheme[]');
            var g_dispatch_qty = document.getElementsByName('dispatch_qty[]');
            var g_total_amt = document.getElementsByName('total_amt[]');

            for (var po = 0; po < g_weight.length; po++)
            {
                // var gtweight = Weight[po].value;
                grand_weight += parseInt(g_weight[po].value);
                grand_qty += parseInt(g_qty[po].value);
                grand_scheme += parseInt(g_scheme[po].value);
                grand_dispatch_qty += parseInt(g_dispatch_qty[po].value);
                grand_total_amt += parseInt(g_total_amt[po].value);
            }
            // console.log(grand_fullfilmentschemqty_total);
            document.getElementById('grand_weight').innerHTML=grand_weight; 
            document.getElementById('grand_qty').innerHTML=grand_qty; 
            document.getElementById('grand_scheme').innerHTML=grand_scheme; 
            document.getElementById('grand_dispatch_qty').innerHTML=grand_dispatch_qty; 
            document.getElementById('grand_total_amt').innerHTML=grand_total_amt; 
            document.getElementById('grand_final_amount').innerHTML=grand_total_amt; 

            document.getElementById("input_grand_final_amount").value = grand_total_amt;


            var x = document.getElementById('grand_total_amt').innerHTML; 
            var y = document.getElementById('discount_type').value;
            // alert(y);
            var d = document.getElementById('discount_value').value;
            if(y == 1) // for percentage 
            {
                var final_discount = (x*d)/100;
                document.getElementById('grand_final_amount').innerHTML = x-final_discount;
                document.getElementById('input_grand_final_amount').value = x-final_discount;

            }
            else if (y == 2) // for amount  
            {
                document.getElementById('grand_final_amount').innerHTML = x-d;
                document.getElementById('input_grand_final_amount').value = x-d;

            }

        }
        $('#mytable').on('click','.removenewrow',function(){

              var table = $(this).closest('table');
              var i = table.find('.mytbody_dispatch7').length;                 

              if(i==1)
              {
                 return false;
              }

             $(this).closest('tr').remove();
            var grand_weight = 0;
            var grand_qty = 0;
            var grand_scheme = 0;
            var grand_dispatch_qty = 0;
            var grand_total_amt = 0;
           
            
            var g_weight = document.getElementsByName('weight[]');
            var g_qty = document.getElementsByName('qty[]');
            var g_scheme = document.getElementsByName('scheme[]');
            var g_dispatch_qty = document.getElementsByName('dispatch_qty[]');
            var g_total_amt = document.getElementsByName('total_amt[]');

            for (var po = 0; po < g_weight.length; po++)
            {
                // var gtweight = Weight[po].value;
                grand_weight += parseInt(g_weight[po].value);
                grand_qty += parseInt(g_qty[po].value);
                grand_scheme += parseInt(g_scheme[po].value);
                grand_dispatch_qty += parseInt(g_dispatch_qty[po].value);
                grand_total_amt += parseInt(g_total_amt[po].value);
            }
            // console.log(grand_fullfilmentschemqty_total);
            document.getElementById('grand_weight').innerHTML=grand_weight; 
            document.getElementById('grand_qty').innerHTML=grand_qty; 
            document.getElementById('grand_scheme').innerHTML=grand_scheme; 
            document.getElementById('grand_dispatch_qty').innerHTML=grand_dispatch_qty; 
            document.getElementById('grand_total_amt').innerHTML=grand_total_amt; 
            document.getElementById('grand_final_amount').innerHTML=grand_total_amt; 

            document.getElementById("input_grand_final_amount").value = grand_total_amt;


            
        });

        function mulfunc(str2)
        {
            var d=str2.substr(3,3);
            var x= document.getElementById("qty"+d).value;
            var y= document.getElementById("rate"+d).value;
            var z= document.getElementById("scheme"+d).value;
            var rate_check= document.getElementById("rate"+d).value;
            if(rate_check == 0)
            {
                document.getElementById("rate"+d).value='';

            }
            else if (rate_check == 0.00)
            {
                document.getElementById("rate"+d).value='';

            }
            var total_amount = x*y;
            var toatl_dispacth_qty = parseInt(x)+parseInt(z);
            document.getElementById("dispatch_qty"+d).value= toatl_dispacth_qty;
            document.getElementById("total_amt"+d).value= total_amount.toFixed(3);
            var grand_weight = 0;
            var grand_qty = 0;
            var grand_scheme = 0;
            var grand_dispatch_qty = 0;
            var grand_total_amt = 0;
           
            
            var g_weight = document.getElementsByName('weight[]');
            var g_qty = document.getElementsByName('qty[]');
            var g_scheme = document.getElementsByName('scheme[]');
            var g_dispatch_qty = document.getElementsByName('dispatch_qty[]');
            var g_total_amt = document.getElementsByName('total_amt[]');

            for (var po = 0; po < g_weight.length; po++)
            {
                // var gtweight = Weight[po].value;
                grand_weight += parseInt(g_weight[po].value);
                grand_qty += parseInt(g_qty[po].value);
                grand_scheme += parseInt(g_scheme[po].value);
                grand_dispatch_qty += parseInt(g_dispatch_qty[po].value);
                grand_total_amt += parseInt(g_total_amt[po].value);
            }
            // console.log(grand_fullfilmentschemqty_total);
            document.getElementById('grand_weight').innerHTML=grand_weight; 
            document.getElementById('grand_qty').innerHTML=grand_qty; 
            document.getElementById('grand_scheme').innerHTML=grand_scheme; 
            document.getElementById('grand_dispatch_qty').innerHTML=grand_dispatch_qty; 
            document.getElementById('grand_total_amt').innerHTML=grand_total_amt; 
            document.getElementById('grand_final_amount').innerHTML=grand_total_amt; 

            document.getElementById("input_grand_final_amount").value = grand_total_amt;

            
        }

        function mulfuncDiscount()
        {
             
            var x = document.getElementById('grand_total_amt').innerHTML; 
            var y = document.getElementById('discount_type').value;
            // alert(y);
            var d = document.getElementById('discount_value').value;
            if(y == 1) // for percentage 
            {
                var final_discount = (x*d)/100;
                document.getElementById('grand_final_amount').innerHTML = x-final_discount;
                document.getElementById('input_grand_final_amount').value = x-final_discount;


            }
            else if (y == 2) // for amount  
            {
                document.getElementById('grand_final_amount').innerHTML = x-d;
                document.getElementById('input_grand_final_amount').value = x-d;

            } 
        }

        $("#submit_manual_order_booking_id").submit(function(e) {
            var form = $(this);
            var url = form.attr('action');
            // var target=$('#result_state');
            $('#submit_form').html('');
            $('#submit_form').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 10px 50%;"></i>');
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    $.alert('Order Placed SuccessFully !!');
                    setTimeout("window.parent.location = ''", 50);

                },
                complete: function () {
                    $('#m-spinner').remove();
                },
                error: function () {
                    $.alert('Something Went Wrong !!');
                    $('#m-spinner').remove();
                }
            });

            e.preventDefault(); // avoid to execute the actual submit of the form.
        });
</script>


 <script>
     $(document).on('change', '#discount_type', function () {
        val = $(this).val();
        // alert(val);
       if (val == "") // for amount  
        {   
            document.getElementById('discount_value').value = '0';
            document.getElementById('discount_value').readOnly = true;
        } else{
            document.getElementById('discount_value').readOnly = false;
        }
        
    });
    </script>