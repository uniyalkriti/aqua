@extends('layouts.master')

@section('title')
    <title>Scheme Plan - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
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
                        <a href="{{url('leave_type')}}">Scheme Plan</a>
                    </li>

                    <li class="active">Edit Scheme Plan</li>
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
                        {!! Form::open(array('route'=>['leave_type.update',$encrypt_id] , 'method'=>'PUT','id'=>'workType-form','role'=>'form' ))!!}

                        <div class="row" style="overflow-x: scroll;">

                            <table id="simple-table" align="center" class="table table-bordered table-hover">
                                <thead><tr><td colspan="13"><h3>Edit Scheme Plan {{Lang::get('common.details')}}</h3></td></tr></thead>
                                <tr>
                                    <th>{{Lang::get('common.s_no')}}</th>
                                    
                                    <th>{{Lang::get('common.location3')}} ID</th>


                                    <th>{{Lang::get('common.location3')}}</th>

                                    <th>SKU ID</th>
                                    <th>SKU Name</th>

                                    <th>Value Amount</th>

                                    <th>Valid From</th>
                                    <th>Valid To</th>

                                    <th>Action</th>



                                
                                </tr>
                                <tbody>
                                  
                                    <?php $inc=1; ?>
                                     @if(!empty($productDetails))
                                     @foreach($productDetails as $pkey => $pvalue)

                                     <?php 
                                     $dynamic_id = $pvalue->scheme_id.'|'.$pvalue->state_id.'|'.$pvalue->product_id.'|'.$pvalue->valid_from_date.'|'.$pvalue->valid_to_date
                                      ?>
                                   
                                    <tr>


                                        <td>{{$inc}}</td>

                                        <td>{{$pvalue->state_id}}</td>

                                        <td>{{$pvalue->state_name}}</td>

                                        <td>{{$pvalue->product_id}}</td>

                                        <td>{{$pvalue->product_name}}</td>

                                      
                                        <td><input type="text" id="amount|{{$dynamic_id}}" required='required' value="{{$pvalue->value_amount_percentage}}"  name="value_amount_percentage" placeholder="Value Amount" readonly></td>
                                        <td><input type="text" required='required' value="{{$pvalue->valid_from_date}}" name="valid_from_date" placeholder="Valid From" readonly></td>
                                        <td><input type="text" required='required' value="{{$pvalue->valid_to_date}}"  name="valid_to_date" placeholder="Valid To"readonly></td>
                                        
                                        <td>

                                        <span id="reverse|{{$dynamic_id}}">

                                            <a id="view|{{$dynamic_id}}" onclick="hideReadonly(this.id)" class="sign">
                                                <i class="fa fa-eye" style="font-size:24px"></i>
                                            </a>

                                        </span>


                                        <span id="showPencil|{{$dynamic_id}}">
                                        
                                        </span>

                                        </td>


                                    </tr>
                                     <?php $inc++;?>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                            
                        </div>

                        {{--<div class="clearfix form-actions">
                            <div class="col-md-offset-5 col-md-9">
                                <button class="btn btn-info" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    Update
                                </button>
                                <button class="btn" type="button" onclick="document.location.href='{{url('leave_type')}}'">
                                    <i class="ace-icon fa fa-close bigger-110"></i>
                                    Cancel
                                </button>
                            </div>
                        </div> --}}

                        {!! Form::close() !!}

                        <div class="hr hr-18 dotted hr-double"></div>

                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection

@section('js')
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('msell/page/location2.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>

    <script type="text/javascript">
        function hideReadonly(e){
            // alert(e);

            var myArr = e.split("|");
            var initial = myArr[0];
            var scheme_id = myArr[1];
            var state_id = myArr[2];
            var sku_id = myArr[3];
            var valid_from = myArr[4];
            var valid_to = myArr[5];

            var data = 'showPencil|'+scheme_id+'|'+state_id+'|'+sku_id+'|'+valid_from+'|'+valid_to;

            var dynamic_id = 'edit|'+scheme_id+'|'+state_id+'|'+sku_id+'|'+valid_from+'|'+valid_to;

            var remove_input = 'amount|'+scheme_id+'|'+state_id+'|'+sku_id+'|'+valid_from+'|'+valid_to;

            document.getElementById(remove_input).removeAttribute('readonly');

            document.getElementById(e).style.display = 'none';

            // alert(data);

            $(document.getElementById(data)).html('');

            $(document.getElementById(data)).append("<a onclick='editDetails(this.id)' id='"+dynamic_id+"'> <i class='fa fa-pencil' style='font-size:24px'></i></a>");

        }



        function editDetails(e){
            // alert(e);

            var myArr = e.split("|");
            var initial = myArr[0];
            var scheme_id = myArr[1];
            var state_id = myArr[2];
            var sku_id = myArr[3];
            var valid_from = myArr[4];
            var valid_to = myArr[5];

            var amount_id = 'amount|'+scheme_id+'|'+state_id+'|'+sku_id+'|'+valid_from+'|'+valid_to;


            var updated_value = $(document.getElementById(amount_id)).val();

            // alert(updated_value);

            $.ajax({
                type: "get",
                url: domain + '/editSchemeDetails',
                dataType: 'json',
                data: "details=" + e+"&updated_val=" + updated_value,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                        var myArrString = data.string.split("|");
                        var upinitial = myArr[0];
                        var upscheme_id = myArr[1];
                        var upstate_id = myArr[2];
                        var upsku_id = myArr[3];
                        var upvalid_from = myArr[4];
                        var upvalid_to = myArr[5];

                        var reverse = 'reverse|'+upscheme_id+'|'+upstate_id+'|'+upsku_id+'|'+upvalid_from+'|'+upvalid_to;

                       


                        var up_value = data.result.value_amount_percentage;

                        var amountInput = 'amount|'+upscheme_id+'|'+upstate_id+'|'+upsku_id+'|'+upvalid_from+'|'+upvalid_to;

                        $(document.getElementById(amountInput)).val(up_value);

                        document.getElementById(amountInput).readOnly = true;;
                        
                        var dynamic_view_id = 'view|'+scheme_id+'|'+state_id+'|'+sku_id+'|'+valid_from+'|'+valid_to;

                        var data_penc = 'showPencil|'+scheme_id+'|'+state_id+'|'+sku_id+'|'+valid_from+'|'+valid_to;
                         $(document.getElementById(data_penc)).html('');

                         $(document.getElementById(reverse)).html('');

                        $(document.getElementById(reverse)).append("<a onclick='hideReadonly(this.id)' id='"+dynamic_view_id+"'> <i class='fa fa-eye' style='font-size:24px'></i></a>");
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });


        }

    </script>
    
@endsection