@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.productScheme')}} - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}" />
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}" />

      <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <!-- <link rel="stylesheet" href="{{asset('css/common.css')}}"/> -->
    <link rel="stylesheet" href="{{asset('nice/css/colorbox.min.css')}}"/>
    <style type="text/css">
        #hidden_div {
            display: none;
        }
        #hidden_div2 {
            display: none;
        }
    </style>
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
@endsection

@section('body')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="{{url('home')}}">Dashboard</a>
                    </li>
                    <li>
                        <a href="{{url('productScheme')}}">{{Lang::get('common.productScheme')}}</a>
                    </li>

                    <li class="active">Edit {{Lang::get('common.productScheme')}}</li>
                </ul><!-- /.breadcrumb -->
                <!-- /.nav-search -->
            </div>

            <div class="page-content">
                @include('layouts.settings')
                @if(count($errors)>0)
                    @foreach ($errors->all() as $error)
                        <div class="help-block">{{ $error }}</div>
                    @endforeach
                @endif

                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->

                        {!! Form::open(array('route'=>['scheme.update',$encrypt_id] , 'method'=>'PUT','id'=>'scheme-form','role'=>'form','enctype'=>'multipart/form-data' ))!!}



                            <div class="row">
                                <div class="col-xs-12">

                                    <div class="row">
                                        <div class="col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">Product Scheme Plan Name</label>

                                                <input type="text" id="p_name" name="p_name"
                                                       value="{{$scheme_plan_data->scheme_name}}"
                                                       placeholder="Enter Plan Name"
                                                       class="form-control" required/>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="">
                                                <label class="control-label" for="status">Product Scheme Plan Category</label>
                                                <select name="plan_category_status" id="plan_category_status" class="form-control">
                                                    <option value="">Select</option>
                                                         <option value="1">VPS (Value Sale)</option>
                                                        {{--<option value="3">Promotional Scheme</option>--}}
                                                        <option {{ $scheme_category_status==2 ? 'selected':'' }} value="2">QPS (Quantity Sale)</option>
                                                </select>
                                            </div>
                                        </div>

                                         <div class="col-lg-2">
                                            <div class="">
                                                <label class="control-label" for="status">Product Scheme Value Sale</label><br>
                                                <select id="vs_status" class="form-control" name="vs_status">

                                                <option value="">Select</option>
                                                 <option {{ $vs_status==2 ? 'selected':'' }} value="2">Item Wise</option>
                                                    
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-2">
                                            <div class="" id="item_status">


                                            <label class="control-label" for="status">Item Type</label><br> 
                                            <select name="item_status_id" id="item_status_id" onchange="showDiv(`hidden_div`,`hidden_div2`, this)" class="form-control">
                                            <option value="">Select</option> 
                                             <option {{ $item_status==2 ? 'selected':'' }} value="2">Single SKU</option></select>
                                             
                                            </div>
                                        </div>                                        

                                    </div>

                                    <br>

                                     <div class="row">

                                        <table id="dynamic-table"  class="table table-bordered" >
                                            <thead>
                                            <tr>
                                                <th align="center" >SKU</th>
                                                <th align="center" >Sale unit</th>
                                                <th align="center" >Sale Value Range</th>
                                                <th align="center" >Scheme Type</th>
                                                <th align="center" >Value</th>
                                            
                                            </tr>
                                            </thead>
                                            @foreach($scheme_plan_details_data as $spdkey => $spddata)

                                            <input type="hidden" name="details_id[]" value="{{$spddata['id']}}"> 
                                            <tr id="rows">
                                                <div style="padding-left: 5px">

                                                    <td>
                                                        <select name="product1[]" id="product" class="form-control chosen-select">
                                                            <option  value="">Select</option>
                                                            <!-- <option disabled="disabled" value="">Select</option> -->
                                                            @if(!empty($product))
                                                                @foreach($product as $k=>$r)
                                                                    <option {{ $spddata['product_id']==$k ? 'selected':'' }} value="{{$k}}">{{$r}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </td>
                                        
                                    
                                                    <td style="padding:5px;">
                                                        <select name="unit_type[]" id="unit_type" class="form-control chosen-select">
                                                            <option value="">Select</option>
                                                                {{-- <option value="1">Weight(litre/kg)</option> --}}
                                                                <option {{ $spddata['sale_unit']==2 ? 'selected':'' }} value="2">Cases</option> 
                                                                 <option {{ $spddata['sale_unit']==3 ? 'selected':'' }} value="3">Pcs</option> 
                                                        </select>
                                                    </td>
                                                    <td style="padding:5px;">
                                                        <input type="text" id="range" name="range_first[]"
                                                            value="{{ $spddata['sale_value_range_first']}}"
                                                            placeholder="Enter Range"
                                                            class="form-control" required/>
                                                                    <h6>--TO--</h6>
                                                        <input type="text" id="range_last" name="range_last[]"
                                                        value="{{ $spddata['sale_value_range_last']}}"
                                                        placeholder="Enter Range"
                                                        class="form-control" required/>
                                                    </td>
                                                    <td style="padding:5px;">
                                                        <select name="amt_type[]" id="amt_type" class="form-control chosen-select">
                                                            <option value="">Select</option>
                                                                <option {{ $spddata['incentive_type']==1 ? 'selected':'' }} value="1">%</option>
                                                                <option {{ $spddata['incentive_type']==2 ? 'selected':'' }} value="2">Amount</option>
                                                                <option {{ $spddata['incentive_type']==3 ? 'selected':'' }} value="3">Free Quantity</option>
                                                                <option {{ $spddata['incentive_type']==4 ? 'selected':'' }} value="4">GIFT</option>

                                                        </select>

                                                        <div id="imageForGift" class="imageModule">
                                                            <input type="file" name="imageForGift[]" accept="image/png, image/jpeg"> 
                                                        </div>


                                                    </td>
                                                    <td style="padding:5px;">
                                                        <input type="text" id="amount" name="amount[]"
                                                            value=" {{ $spddata['value_amount_percentage']}} "
                                                            placeholder="Enter Amount"
                                                            class="form-control" required/>
                                                    </td>
                                                
                                                </div>
                                            </tr>
                                            @endforeach
                                      </table>

                                                <div id="add_new"><a>Add Row</a></div>


                                     </div>

                                </div>

                            </div>

                            <div class="clearfix form-actions">
                                <div class="col-md-offset-5 col-md-7">
                                    <button class="btn btn-info" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Update
                                    </button>
                                    <button class="btn" type="button" onclick="document.location.href='{{url('scheme')}}'">
                                        <i class="ace-icon fa fa-close bigger-110"></i>
                                        Cancel
                                    </button>
                                </div>
                            </div>

                            {!! Form::close() !!}

                        <div class="hr hr-18 dotted hr-double"></div>

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection

@section('js')
     <script type="text/javascript">
        $(".chosen-select").chosen();
        $('button').click(function () {
            $(".chosen-select").trigger("chosen:updated");
        });
    </script>
    <script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('msell/page/incentive.js')}}"></script>
    <script src="{{asset('js/incentive.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>

     <script type="text/javascript">
        // $( document ).ready(function() {
        //     $('.imageModule').hide();
        // });

     $(document).on('change', '#amt_type', function () {
        forGift = $(this).val();

        if(forGift == '4'){
            // alert(forGift);
            $(this).siblings(".imageModule").show();
        }else{
            $(this).siblings(".imageModule").hide();
        }
    });


       $(document).on('ready', '#amt_type', function () {
        forGift = $(this).val();

        if(forGift == '4'){
            // alert(forGift);
            $(this).siblings(".imageModule").show();
        }else{
            $(this).siblings(".imageModule").hide();
        }
    });


    </script>
    


     <script>
     $(document).on('change', '#plan_category_status', function () {
        val = $(this).val();
        
       if(val==1)
        {
            $('#vs_status').empty();
            $('#vs_status').append('<option value="">Select</option> <option value="1">Total Sale Wise</option>  <option value="2">Item Wise</option> ');
            $('#unit_type').empty();
            $('#unit_type').append('<option value="4">Range</option>');
        }
        else if(val == 3)
        {
            $('#vs_status').empty();
            $('#vs_status').append('<option value="">Select</option> <option value="1">Total Sale Wise</option>  <option value="2">Item Wise</option> ');
            $('#unit_type').empty();
            $('#unit_type').append('<option value="">Select</option> <option value="2">Cases</option> <option value="3">pcs</option>');
        }
        else
        {
            $('#vs_status').empty();
            $('#vs_status').append('<option value="">Select</option> <option value="2">Item Wise</option>');
            $('#unit_type').empty();
            $('#unit_type').append('<option value="">Select</option>  <option value="2">Cases</option>  <option value="3">Pcs</option>');
        }
        
    });
    </script>


     <script>
     $(document).on('change', '#vs_status', function () {
        vsval = $(this).val();
        val = $('#plan_category_status').val();
       if(vsval==2)
        {
            $('#item_status').empty();
            // <option value="1">Combo SKU</option> 
            $('#item_status').append('<label class="control-label" for="status">Item Type</label><br> <select name="item_status_id" id="item_status_id" onchange="showDiv(`hidden_div`,`hidden_div2`, this)" class="form-control"><option value="">Select</option>  <option value="2">Single SKU</option></select>');            
            $('#product').show();
            $('#product1').show();
            
        }
        else if(val==1 || val==2)
        {

         $('#item_status').empty();
         $('#product').hide();
        $('#product1').hide();
      
            

        }

        else{

         $('#item_status').empty();
        $('#product').hide();
        $('#product1').hide();

        }
        
    });
    </script>

    <script type="text/javascript">
        
        function showDiv(divId,divId2, element)
        {
            $('#hidden_div').hide();
            $('#hidden_div2').hide();
            val_vs_status = $('#vs_status').val();

           
            if(element.value == 1)
            {
            
               document.getElementById(divId).style.display = element.value == 1 ? 'block' : 'none';

                
            }
            else if(element.value == 2)
            {

                document.getElementById(divId2).style.display = element.value == 2 ? 'block' : 'none';

                
            }
            else if(val_vs_status== 1 || val_vs_status== 2)
            {

             $('#hidden_div').hide();
             $('#hidden_div2').hide();
                

            }
            else
            {
                $('#hidden_div').hide();
                $('#hidden_div2').hide();
            }

        }
    </script>


   <!--  <script>
     $(document).on('change', '#item_status', function () {
        itval = $(this).val();
        
       if(itval==1)
        {
         $('#product_status').empty();

            $('#product_status').show();            

            
        }
        else if(val==1 || val==2)
        {

         $('#product_status').empty();
            

        }
        else{

         $('#product_status').empty();

        }
        
    });
    </script> -->

    <script type="text/javascript">
        $("#add_new").click(function () { 

    $("#dynamic-table").each(function () {
       
        var tds = '<tr>';
        jQuery.each($('tr:last td', this), function () {
            tds += '<td>' + $(this).html() + '</td>';
        });
        tds += '</tr>';
        if ($('tbody', this).length > 0) {
            $('tbody', this).append(tds);
        } else {
            $(this).append(tds);
        }
    });
});
    </script>
   
@endsection