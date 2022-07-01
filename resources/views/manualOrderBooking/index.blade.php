@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.manual_order_booking')}} - {{config('app.name')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
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
                    <li class="active">{{Lang::get('common.manual_order_booking')}}</li>
                </ul><!-- /.breadcrumb -->
                <p class="bs-component pull-right">
                    <a href="#" data-toggle="collapse" data-target="#manual_order_booking_form" class="btn btn-sm btn-default"><i class="fa fa-navicon mg-r-10"></i> Filter</a>
                </p>
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

                        <form class="form-horizontal open collapse in" action="manual_order_booking_form" method="POST" id="manual_order_booking_form" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">


                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">{{Lang::get('common.location3')}}</label>
                                                <select  name="state" id="state" class="form-control chosen-select">
                                                    <option value="">select</option>
                                                    @if(!empty($stateData))
                                                        @foreach($stateData as $sk=>$sr)
                                                            <option value="{{$sk}}">{{$sr}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>



                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">{{Lang::get('common.distributor')}}</label>
                                                <select  name="dealer" id="distributor" class="form-control chosen-select" required>
                                                    <option value="">select</option>
                                                    @if(!empty($dealer))
                                                        @foreach($dealer as $k=>$r)
                                                            <option value="{{$k}}">{{$r}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>


                                 

                                       
                                        
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                            <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10" style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                                Find
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <br>
                        <div class="row">
                            <div class="col-xs-12">
                                <!-- PAGE CONTENT BEGINS -->
                                @if(Session::has('message'))
                                    <div class="alert alert-block {{ Session::get('alert-class', 'alert-info') }}">
                                        <button type="button" class="close" data-dismiss="alert">
                                            <i class="ace-icon fa fa-times"></i>
                                        </button>
                                        <i class="ace-icon fa fa-check green"></i>
                                        {{ Session::get('message') }}
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-xs-12" id="ajax-table" style="overflow-x: scroll;">


                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="hr hr-18 dotted hr-double"></div>

                        <!-- PAGE CONTENT ENDS -->
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
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
    <script>
        $(".chosen-select").chosen();
        $('button').click(function () {
            $(".chosen-select").trigger("chosen:updated");
        });
     $("#manual_order_booking_form").submit(function(e) {
            var form = $(this);
            var url = form.attr('action');
            // var target=$('#result_state');
            $('#ajax-table').html('');
            $('#ajax-table').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 10px 50%;"></i>');
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    $('#ajax-table').html(data);
                    $('#manual_order_booking_form').collapse('hide');
                   
                },
                complete: function () {
                    $('#m-spinner').remove();
                },
                error: function () {
                    $('#m-spinner').remove();
                }
            });

            e.preventDefault(); // avoid to execute the actual submit of the form.
        });
    </script>



<script>
$(document).on('change', '#state', function () {
   val = $(this).val();
   _hq = $('#distributor');
   //alert(_current_val);
   if(val != '')
  {
   $.ajaxSetup({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
       });
       $.ajax({
           type: "GET",
           url: domain + '/get_distributor_from_state',
           dataType: 'json',
           data: "id=" + val,
           success: function (data) {
               
             
                   template = '<option value="" >Select</option>';

                   $.each(data.result, function (key, value) {
                     
                       console.log(value);
                       if (value.name != '') {
                          
                           template += '<option value="' + key + '" >' + value + '</option>';
                           
                       }
                   });
                   console.log(template);
                 //  alert(_hq.val());
                   _hq.empty();
                   _hq.append(template).trigger("chosen:updated");
          

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


@endsection