@extends('layouts.master')

@section('body')


    <div class="page-wrapper">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
    @endif
    </div>
<div class="main-content" >
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #4287BA;">
            <ul class="breadcrumb">
                <li style="color: white">
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a style="color: white" href="{{url('home')}}">Home</a>
                </li>

                <li class="active" style="color: white">Leave Management</li>
            </ul><!-- /.breadcrumb -->
            <!-- /.nav-search -->
        </div>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <div class="page-content">


            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-10">
                        <div class="card">
                            
                            
                              
                                <div class="card-body">
                                    <h4 class="card-title">Total</h4>
                                    <table d="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>S.N.</th>
                                                @if(!empty($data))
                                                    @foreach($data as $key => $value)
                                                        <th>{{$value->name}}</th>
                                                    @endforeach
                                                @endif
                                                <th>Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id='hide'>
                                            <td>1</td>
                                            <?php 
                                                $arr = [];
                                            ?>
                                            @if(!empty($data))
                                                @foreach($data as $key => $value)
                                                    <?php
                                                        $balance = !empty($leaves_type_data[$value->id])?$leaves_type_data[$value->id]:'0'; 
                                                        $count = $value->count-$balance;

                                                    ?>
                                                    <td class="setall" id="{{'leaver_id'.$value->id}}">{{$arr[] = $count}}</td>
                                                @endforeach
                                            @endif
                                            <td id="balance">{{array_sum($arr)}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                </div>
                                
                            
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10">
                        <div class="card">

                            <form action="{{route('leave.store')}}" method="post" class="form-horizontal">
                                @csrf
                                <div class="card-body">
                                    <h4 class="card-title">Apply Leave</h4>
                                    <div class="form-group row">
                                        <label for="fname" class="col-sm-3 text-right control-label col-form-label">Leave type</label>
                                        <div class="col-sm-9">
                                            <select  name="leave_type" class="form-control" id="fname" placeholder="Leave type">
                                                @if(!empty($data))
                                                    @foreach($data as $key => $value)
                                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                            
                                    </div>
                                    <div class="form-group row">
                                        <label for="lname" class="col-sm-3 text-right control-label col-form-label">Date from</label>
                                        <div class="col-sm-4">
                                            <input type="date"  name="date_from" class="form-control" id="FromDate">
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="date" name="date_to" class="form-control" id="ToDate">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="fname" class="col-sm-3 text-right control-label col-form-label">Days</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="days" class="form-control" id="TotalDays" placeholder="Number of leave days">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="fname" class="col-sm-3 text-right control-label col-form-label">Reason</label>
                                        <div class="col-sm-9">
                                            <textarea type="text" name="reason" class="form-control" placeholder="Reason">
                                            </textarea></div>
                                    </div>
                                </div>
                                <div class="border-top">
                                    <div class="card-body">
                                            <button type="submit" class="form-control btn btn-primary ">Apply</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
       


@endsection

@section('js')
    <script>
        $("#ToDate").change(function () {
            var start = new Date($('#FromDate').val());
            var end = new Date($('#ToDate').val());

            var diff = new Date(end - start);
            var days=1;
            days = diff / 1000 / 60 / 60 / 24;

            // $('#TotalDays').val(days);
            if (days == NaN) {
                $('#TotalDays').val(0);
            } else {
                $('#TotalDays').val(days+1);
            }
        })

        $("#FromDate").change(function () {
            var start = new Date($('#FromDate').val());
            var end = new Date($('#ToDate').val());

            var diff = new Date(end - start);
            var days=1;
            days = diff / 1000 / 60 / 60 / 24;

            // $('#TotalDays').val(days);
            if (days == NaN) {
                $('#TotalDays').val(0);
            } else {
                $('#TotalDays').val(days+1);
            }
        })
        $(document).on('change', '#ToDate', function () {
            _current_val = $('#TotalDays').val();
            _current_val_from_date = $('#ToDate').val();
            _current_val_to_date = $('#FromDate').val();
            leaver_type = $('#fname').val();
            // alert(_current_val);
            location_data(_current_val,_current_val_from_date,_current_val_to_date,leaver_type);

            
        });

        function location_data(val,_current_val_from_date,_current_val_to_date,leaver_type) {
            
            if (val != '') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url:  '{{url("getDays")}}',
                    dataType: 'json',
                    data: "val=" + val+"&from_date="+_current_val_from_date+"&to_date="+_current_val_to_date+"&leaver_type="+leaver_type,
                    success: function (data) {
                        if (data.code == 401) {
                            alert(data.message);
                            var final_leave = $('#leaver_id'+leaver_type).html();
                            var blan = $('#balance').html();
                             $('#balance').empty();
                            // alert(final_leave);
                            $('#leaver_id'+leaver_type).empty();
                            var set_append = final_leave-val
                            var set_append_new = blan-val
                            $('#leaver_id'+leaver_type).append(set_append);
                            $('#balance').append(set_append_new);

                        }
                        else if (data.code == 200) {

                            alert(data.message);
                            var final_leave = $('#leaver_id'+leaver_type).html();
                            var blan = $('#balance').html();
                             $('#balance').empty();
                            // alert(final_leave);
                            $('#leaver_id'+leaver_type).empty();
                            var set_append = final_leave-val
                            var set_append_new = blan-val
                            $('#leaver_id'+leaver_type).append(set_append);
                            $('#balance').append(set_append_new);
                            //Location 3
                            
                            

                        }
                        else if (data.code == 801) {

                            alert(data.message);
                            
                            $('#hide').empty();
                            $('#balance').empty();
                            //Location 3
                            
                            

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
        }
    </script>
    @endsection