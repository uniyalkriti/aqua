@extends('layouts.master') 
 
@section('title')
    <title>{{Lang::get('common.'.$current_menu)}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
    <style>
    #simple-table table {
        border-collapse: collapse !important;
    }

    #simple-table table, #simple-table th, #simple-table td {
        border: 1px solid black !important;
    }

    #simple-table th {
        /*background-color: #438EB9 !important;*/
        background-color: #7BB0FF !important;
        color: black;
    }
</style>
@endsection

@section('body')
    
<!-- ......................table contents........................................... -->
<div class="main-container ace-save-state" id="main-container">
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
             <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->

                        <form class="form-horizontal open collapse in" action="" method="GET" id="sale-order" role="form"
                              enctype="multipart/form-data">
                            {!! csrf_field() !!}
                                        <div class="col-xs-6 col-sm-6 col-lg-2">
                                             <div class="">
                                                <label class="control-label no-padding-right"
                                                       for="name">From</label>
                                                <input autocomplete="off" type="text" name="month" id="month" class="form-control" placeholder="Month" required>
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-lg-1">
                                            <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10"
                                                    style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                                Find
                                            </button>
                                        </div>
                            </form>
                     </div>
             </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="clearfix">
                            <div class="pull-right tableTools-container"></div>
                        </div>
                       <!--  <div class="table-header">
                            Assign Plan
                            <a href="{{url($current_menu.'/create')}}" class="btn btn-sm btn-success pull-right"><i
                            class="fa fa-plus mg-r-10"></i> Add {{Lang::get('common.'.$current_menu)}}</a>
                        </div> -->
                        <!-- div.table-responsive -->
                        <!-- div.dataTables_borderWrap -->
                        <div>
        @if(!empty($records))
        <div>
            <form  method="get" action="assignPlans"  enctype="multipart/form-data">
                 {!! csrf_field() !!}
                            <table id="simple-table" class="table table-bordered" style="font-size: 13px;border: 1px black;">
                                <thead>
                                    <th class="center">
                                        S.No.
                                    </th>
                                    <th>User Name</th>
                                    <th>Designation</th>
                                    @if(!empty($datesDisplayArr))
                                        @foreach($datesDisplayArr as $keyd=>$datad)
                                            <th>{{$datad}}</th>
                                        @endforeach
                                    @endif
                                </thead>

                                <tbody>
                              @if(!empty($records)) 

                                @foreach($records as $key=>$data)
                                    <tr>
                                        <td class="center">
                                            {{  $key+1 }}
                                        </td>
                                        <td>
                                                {{$data->user_name}}
                                        </td>

                                          <td>
                                                {{$data->role_name}}
                                        </td>
                                        
                                        @foreach($datesArr as $keydd=>$datadd)
                                        <td style="background-color:white">
                                             <select multiple name="plan[]" id="plan" class="form-control chosen-select">
                                                <option value="">select</option>
                                                @if(!empty($plan))
                                                    @foreach($plan as $k=>$r)
                                                        <option value="{{$data->user_id.'|'.$datadd.'|'.$k}}">{{$r}}</option>
                                                    @endforeach
                                                @endif
                                            </select>    
                                        </td>
                                        @endforeach
                                       
                                    </tr>
                                @endforeach
                              @endif
                                </tbody>
                            </table>
                             <div class="row">
                        <div class="col-md-3" align="center"><br>
                           <input class="form-control btn btn-primary" type="submit" name="submit" value="Submit">
                         </div>
                 </div>
             </form>
         </div>
         @endif
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </div>
</div>

    <!-- ......................table ends contents...........................................  -->
    
@endsection

@section('js')
<script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery-additional-methods.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/page/report5.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
    <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
    <script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>
    
    <script>
    function confirmAction(heading, name, action_id, tab, act) {
            $.confirm({
                title: heading,
                content: 'Are you sure want to ' + act + ' ' + name + '?',
                buttons: {
                    confirm: function () {
                        takeAction(name, action_id, tab, act);
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

        function takeAction(module, action_id, tab, act) {
            if (action_id != '') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/takeAction',
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
        function searchReset() {
            $('#search').val('');
            $('#user-search').submit();
        }
        function search() {
            if($('#search').val()!='')
            {
                $('#user-search').submit();
            }
        }
    </script>
    <script src="{{asset('nice/js/toastr.min.js')}}"></script>
    @if(Session::has('message'))
    <script>
        toastr.{{ Session::get('class', 'info') }}("{{ Session::get('message') }}");
    </script>
    @endif

    <script type="text/javascript">
        $("#month").datetimepicker  ( {
            clear: "Clear",
            format: 'YYYY-MM'
        });
    </script>


@endsection