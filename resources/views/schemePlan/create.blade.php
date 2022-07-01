@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.scheme')}} - {{config('app.name')}}</title>
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
                        <a href="{{url('scheme')}}">{{Lang::get('common.scheme')}}</a>
                    </li>

                    <li class="active">Create {{Lang::get('common.scheme')}}</li>
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

                        <form class="form-horizontal" action="{{route('scheme.store')}}" method="POST" id="incentive-form" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}


                            <div class="row">
                                <div class="col-xs-12">

                                    <div class="row">
                                        <div class="col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">Scheme Plan Name</label>

                                                <input type="text" id="p_name" name="p_name"
                                                       value=""
                                                       placeholder="Enter Plan Name"
                                                       class="form-control" required/>
                                            </div>
                                        </div>

                                        <div class="col-lg-2">
                                            <div class="">
                                                <label class="control-label" for="status">Scheme Plan Category</label>
                                                <select name="plan_category_status" id="plan_category_status" class="form-control">
                                                    <option value="">Select</option>
                                                        <option value="1">VPS (Value Sale)</option>
                                                        <option value="2">QPS (Quantity Sale)</option>
                                                        <option value="3">Promotional Scheme</option>
                                                        <option value="4">Gift</option>
                                                </select>
                                            </div>
                                        </div>

                                         <div class="col-lg-2">
                                            <div class="">
                                                <label class="control-label" for="status">Scheme Value Sale</label><br>
                                                <select id="vs_status" class="form-control" name="vs_status">
                                                    
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-2">
                                            <div class="" id="item_status">
                                             
                                            </div>
                                        </div>    


                                        <div class="col-xs-6 col-sm-6 col-lg-2" id="hidden_div">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">SKU Product</label><br>
                                                <select multiple name="product[]" id="product" class="form-control chosen-select">
                                                    <option disabled="disabled" value="">Select</option>
                                                    @if(!empty($product))
                                                        @foreach($product as $k=>$r)
                                                            <option value="{{$k}}">{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                          <div class="col-xs-6 col-sm-6 col-lg-2" id="hidden_div2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">SKU Product</label><br>
                                                <select name="product1[]" id="product1" class="form-control chosen-select">
                                                    <option disabled="disabled" value="">Select</option>
                                                    @if(!empty($product))
                                                        @foreach($product as $k=>$r)
                                                            <option value="{{$k}}">{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                    </div>

                                    <br>

                                     <div class="row">

                                        <table id="dynamic-table"  class="table table-bordered" >
                                            <thead>
                                            <tr>
                                                <th align="center" >Sale unit</th>
                                                <th align="center" >Sale Value</th>
                                                <th align="center" >Scheme Type</th>
                                                <th align="center" >Value</th>
                                            
                                            </tr>
                                            </thead>
                                            <tr id="rows">
                                                <div style="padding-left: 5px">
                                                    <td style="padding:5px;">
                                                        <select name="unit_type[]" id="unit_type" class="form-control chosen-select">
                                                            <option value="">Select</option>
                                                                <option value="1">Weight(litre/kg)</option>
                                                                <option value="2">Cases</option>
                                                                <option value="3">Pcs</option>
                                                        </select>
                                                    </td>
                                                    <td style="padding:5px;">
                                                        <input type="text" id="range" name="range_first[]"
                                                            value=""
                                                            placeholder="Enter Range"
                                                            class="form-control" required/>
                                                                    <h6>--TO--</h6>
                                                        <input type="text" id="range_last" name="range_last[]"
                                                        value=""
                                                        placeholder="Enter Range"
                                                        class="form-control" required/>
                                                    </td>
                                                    <td style="padding:5px;">
                                                        <select name="amt_type[]" id="amt_type" class="form-control chosen-select">
                                                            <option value="">Select</option>
                                                                <option value="1">%</option>
                                                                <option value="2">Amount</option>
                                                                <option value="3">Free Quantity</option>
                                                        </select>
                                                    </td>
                                                    <td style="padding:5px;">
                                                        <input type="text" id="amount" name="amount[]"
                                                            value=""
                                                            placeholder="Enter Amount"
                                                            class="form-control" required/>
                                                    </td>
                                                
                                                </div>
                                            </tr>
                                      </table>

                                                <div id="add_new"><a>Add Row</a></div>


                                     </div>

                                </div>

                            </div>

                            <div class="clearfix form-actions">
                                <div class="col-md-offset-5 col-md-7">
                                    <button class="btn btn-info" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Submit
                                    </button>
                                    <button class="btn" type="button" onclick="document.location.href='{{url('scheme')}}'">
                                        <i class="ace-icon fa fa-close bigger-110"></i>
                                        Cancel
                                    </button>
                                </div>
                            </div>

                        </form>

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
            $('#unit_type').append('<option value="">Select</option> <option value="1">Weight</option>  <option value="3">Pcs</option><option value="2">Cases</option>');
        }
        else
        {
            $('#vs_status').empty();
            $('#vs_status').append('<option value="">Select</option> <option value="2">Item Wise</option>');
            $('#unit_type').empty();
            $('#unit_type').append('<option value="">Select</option> <option value="1">Weight</option>  <option value="3">Pcs</option><option value="2">Cases</option>');
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

            $('#item_status').append('<label class="control-label" for="status">Item Type</label><br> <select name="item_status_id" id="item_status_id" onchange="showDiv(`hidden_div`,`hidden_div2`, this)" class="form-control"><option value="">Select</option> <option value="1">Combo SKU</option>  <option value="2">Single SKU</option></select>');            
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