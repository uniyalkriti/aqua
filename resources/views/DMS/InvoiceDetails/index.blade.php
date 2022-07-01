@extends('layouts.core_php_heade')

@section('dms_body')

    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/toastr.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/colorbox.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/chosen.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}" />
<style>
table, th, td {
 
}
</style>

    <div class="main-content" style="overflow-x: scroll;">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #90d781; color: black;">
                <ul class="breadcrumb">
                    <li style="color: black;">
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a style="color: black;" href="#">Reports</a>
                    </li>
                    <li class="active" style="color: black;">Invoice Details</li>
                </ul><!-- /.breadcrumb -->
                <!-- /.nav-search -->
            </div>

            <div class="page-content"  style=" font-family: 'Times New Roman', Times, serif; ">
                <form class="form-horizontal open collapse in" action="" method="get" id="sale-order" role="form"
                                          enctype="multipart/form-data">

                    <input type="hidden" name="submit_url" id="submit_url" value="invoice_details_ajax">
                    {!! csrf_field() !!}
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-lg-4 ">
                                <label class="control-label no-padding-right" for="name">{{Lang::get('common.seacrh_by_ref_no')}}</label>
                                <div class="input-group" style="cursor: pointer;">
                                    @if(empty(Request::get('order_no')))
                                     <input type="text" placeholder="Search by Order No" id="search"
                                               name="order_no" value="{{ Request::get('order_no') }}"
                                             class="form-control input-sm"/>
                                       <span onclick="search()" class="input-group-addon cursor">
                                           <i class="fa fa-search"></i>
                                     </span>
                                   @else
                                     <input type="text" readonly="readonly" placeholder="Search by Order No"
                                             id="search" name="order_no" value="{{ Request::get('order_no') }}"
                                               class="form-control input-sm"/>
                                      <span onclick="searchReset();" class="input-group-addon cursor">
                                           <i class="fa fa-times"></i>
                                       </span>
                                    @endif
                                </div>
                                {{-- <input type="text" id="myInput" class = "form-control" onkeyup="myFunction()" placeholder="Search for anything.." title="Type in a name"> --}}
                            </div>
                            <div class="col-lg-4">
                                <label class="control-label no-padding-right" for="id-date-range-picker-1">{{Lang::get('common.date_range')}}</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar bigger-110"></i>
                                    </span>

                                    <input class="form-control" type="text" name="date_range_picker" id="id-date-range-picker-1" readonly />
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <button type="submit" class="  btn btn-sm btn-primary btn-block mg-b-10"
                                        style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                    {{Lang::get('common.find')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <br>

                <div class="row">
                    <div class="col-xs-12" id="ajax-table" >


                    </div>
                </div>
                
            </div>
        </div>
    </div>
<!--  -->
    </div>
</div>
    
</body>
<script src="{{asset('msell/js/jquery-2.1.4.min.js')}}"></script>
<script src="{{asset('msell/js/moment.min.js')}}"></script>
<script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
<script src="{{asset('msell/js/common.js')}}"></script>
<script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
<script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
<script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>
<script src="{{asset('msell/page/dynamic_page.js')}}"></script>
<script src="{{asset('msell/js/moment.min.js')}}"></script>
<script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
<script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>




<script type="text/javascript">
    function searchReset() {
            $('#search').val('');
            $('#user-search').submit();
        }

        
</script>
<script type="text/javascript">
    $(".chosen-select").chosen();
        $('button').click(function () {
            $(".chosen-select").trigger("chosen:updated");
        });
        $('.date-picker').datepicker({
            autoclose: true,
            todayHighlight: true
        })
        //show datepicker when clicking on the icon
        .next().on(ace.click_event, function(){
            $(this).prev().focus();
        });

        //or change it into a date range picker
        $('.input-daterange').datepicker({autoclose:true});
        //to translate the daterange picker, please copy the "examples/daterange-fr.js" contents here before initialization
        $('input[name=date_range_picker]').daterangepicker({
            'applyClass' : 'btn-sm btn-success',
            'cancelClass' : 'btn-sm btn-default',
                showDropdowns: true,
            // showWeekNumbers: true,             
            minDate: '2017-01-01',
            maxDate: moment().add(2, 'years').format('YYYY-01-01'),
            locale: {
                format: 'YYYY/MM/DD',
                applyLabel: 'Apply',
                cancelLabel: 'Cancel',
            },
            ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    dateLimit: {
                                        "month": 1
                                    },

        })
        .prev().on(ace.click_event, function()
        {
            $(this).next().focus();
        }); 
    </script>
</script>
@endsection