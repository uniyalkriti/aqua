@extends('layouts.common_dashboard')

@section('title')
    <title>{{Lang::get('common.user_detail')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    {{--<link rel="stylesheet" href="{{asset('css/chosen.min.css')}}"/>--}}
    {{--    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>--}}
    <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
    {{--    <link rel="stylesheet" href="{{asset('nice/css/bootstrap-datetimepicker.min.css')}}"/>--}}
    <link rel="stylesheet" href="{{asset('nice/css/jquery-ui.custom.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/fullcalendar.min.css')}}"/>
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
                        <a href="{{url('home')}}">{{Lang::get('common.dashboard')}}</a>
                    </li>

                    <li>
                        <a href="{{url('user')}}">{{Lang::get('common.user_detail')}}</a>
                    </li>
                    <li class="active">{{Lang::get('common.user')}} {{Lang::get('common.attendance')}}</li>
                </ul><!-- /.breadcrumb -->
            </div>
            <div class="page-content">
                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->
                        <div class="row">
                            <div class="col-sm-2">
                                <div>
                                <?php 
                                    if(!empty($attendance_record->image_name)){
                                        $img_name=$attendance_record->image_name;
                                    }else{
                                        $img_name="";
                                    }
                                ?>
												<span class="profile-picture">
                                                  <!--   <img id="user_image" style="height: 80px;" class="editable img-responsive"  src="http://162.213.190.125/rajdhani-besan-api/webservices/mobile_images/Attendance/{{$img_name}}" onerror="this.onerror=null;this.src='{{asset('msell/images/avatars/profile-pic.jpg')}}';" /> -->

                                                    <img id="user_image" style="height: 80px;" class="editable img-responsive"  src="http://bambinoagro.msell.in/bambinoagro_api/webservices/mobile_images/Attendance/{{$img_name}}" onerror="this.onerror=null;this.src='{{asset('msell/images/avatars/profile-pic.jpg')}}';" />
												</span>
                                </div>
                            </div>
                            <div class="col-sm-10">
                            <div class="col-md-6">
                                <div class="profile-user-info profile-user-info-striped">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"><strong>{{Lang::get('common.user')}}</strong></div>

                                        <div class="profile-info-name" style="text-align: left"><strong>Info</strong></div>
                                    </div>
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> {{Lang::get('common.username')}}</div>

                                        <div class="profile-info-value">
                                            <span class="editable" id="username">{{$person->uname}}</span>
                                        </div>
                                    </div>

                                   <div class="profile-info-row">
                                   <div class="profile-info-name"> {{Lang::get('common.role_key')}} </div>

                                   <div class="profile-info-value">
                                    <i class="fa fa-star light-orange bigger-110"></i>
                                   <span class="editable" id="city">{{$person->rolename}}</span>
                                    </div>
                                   </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> {{Lang::get('common.email')}}</div>

                                        <div class="profile-info-value">
                                            <span class="editable"
                                                  id="age">{{!empty($person->email)?$person->email:'N/A'}}</span>
                                        </div>
                                    </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> {{Lang::get('common.user_contact')}}</div>

                                        <div class="profile-info-value">
                                            <span class="editable" id="signup">{{$person->mobile}}</span>
                                        </div>
                                    </div>
                                  
                                </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="profile-user-info profile-user-info-striped">
                                    <div class="profile-info-row">
                                       <div class="profile-info-name"><strong>{{Lang::get('common.attendance')}}</strong></div>
                                    <div class="profile-info-name" style="text-align: left"><strong>Info</strong></div>
                                    </div>
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> {{Lang::get('common.date')}}</div>

                                        <div class="profile-info-value">
                                            <span class="editable" id="username">{{!empty($attendance_record->work_date)?$attendance_record->work_date:'N/A'}}</span>
                                        </div>
                                    </div>

                                   <div class="profile-info-row">
                                   <div class="profile-info-name"> {{Lang::get('common.time')}} </div>

                                   <div class="profile-info-value">
                                    <i class="fa fa-star light-orange bigger-110"></i>
                                   <span class="editable" id="city">{{!empty($attendance_record->work_time)?$attendance_record->work_time:'N/A'}}</span>
                                    </div>
                                   </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> {{Lang::get('common.status')}}</div>

                                        <div class="profile-info-value">
                                            <span class="editable"
                                                  id="age">{{!empty($attendance_record->work_status)?$attendance_record->work_status:'N/A'}}</span>
                                        </div>
                                    </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Geo {{Lang::get('common.location')}}</div>

                                        <div class="profile-info-value">
                                            <span class="editable" id="signup">{{!empty($attendance_record->track_addrs)?$attendance_record->track_addrs:'N/A'}}</span>
                                        </div>
                                    </div>
                                  
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="center">
                                    <form id="month_form" method="get">
                                        <input value="{{ !empty(Request::get('month'))?date('Y-m',strtotime(Request::get('month'))):'' }}"
                                               data-date-format="YYYY-MM"
                                               type="hidden" class="form-control input-sm" name="month" id="month">
                                    </form>
                                    {{--@if(!empty($working_status))--}}
                                    {{--@foreach($working_status as $key=>$data)--}}
                                    {{--<span class="label label-lg arrowed-in arrowed-in-right" style="background-color: {{$key}}">{{$data}}</span>--}}
                                    {{----}}
                                    {{--@endforeach--}}
                                    {{--@endif--}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-9">
                                <div class="space"></div>

                                <div id="calendar"></div>
                            </div>

                            <div class="col-sm-3">
                                <div class="widget-box transparent">
                                    <div class="widget-header">
                                        <h4>{{Lang::get('common.action')}}</h4>
                                    </div>

                                    <div class="widget-body">
                                        <div class="widget-main no-padding">
                                            <div id="external-events">
                                                @if(!empty($working_status))
                                                    @foreach($working_status as $key=>$data)
                                                        <div class="external-event" style="background-color: #C39BD3;">
                                                            <i class="ace-icon fa fa-angle-double-right"></i>
                                                            {{$data}} <span
                                                                    class="badge badge-pink">{{!empty($status[$data])?count($status[$data]):0}}</span>
                                                        </div>
                                                    @endforeach
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div>
        </div>
    </div>
@endsection

@section('js')

    <script src="{{asset('nice/js/moment.min.js')}}"></script>
    {{--    <script src="{{asset('nice/js/bootstrap-datetimepicker.min.js')}}"></script>--}}
    {{--    <script src="{{asset('nice/js/jquery.validate.min.js')}}"></script>--}}
    {{--    <script src="{{asset('nice/js/jquery-additional-methods.min.js')}}"></script>--}}
    {{--    <script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>--}}
    {{--<script src="{{asset('js/user.js')}}"></script>--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
    <script src="{{asset('nice/js/jquery-ui.custom.min.js')}}"></script>
    <script src="{{asset('nice/js/fullcalendar.min.js')}}"></script>
    <script type="text/javascript">
        jQuery(function ($) {

            /* initialize the external events
                -----------------------------------------------------------------*/

            $('#external-events div.external-event').each(function () {

                // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
                // it doesn't need to have a start or end
                var eventObject = {
                    title: $.trim($(this).text()) // use the element's text as the event title
                };

                // store the Event Object in the DOM element so we can get to it later
                $(this).data('eventObject', eventObject);

                // make the event draggable using jQuery UI
                $(this).draggable({
                    zIndex: 999,
                    revert: true,      // will cause the event to go back to its
                    revertDuration: 0  //  original position after the drag
                });

            });


            /* initialize the calendar
            -----------------------------------------------------------------*/

            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();


            var calendar = $('#calendar').fullCalendar({
                //isRTL: true,
                //firstDay: 1,// >> change first day of week

                buttonHtml: {
                    prev: '<i class="ace-icon fa fa-chevron-left"></i>',
                    next: '<i class="ace-icon fa fa-chevron-right"></i>'
                },

                header: {
                    @if(!empty( Request::get('month') ))
                    left: '',
                    @else
                    left: 'prev,next today',

                    @endif
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                events: [
                        @if(!empty($records))
                        @foreach($records as $k=>$d)
                    {
                        title: '{{$d->work}}',
                        start: '{{$k}}',
                        className: 'label-important',
                        url: '{{url()->current().'?month='.$k}}',
                        backgroundColor: '{{$d->color_status}}'
                    },
                    @endforeach
                    @endif
                ]
                ,

                /**eventResize: function(event, delta, revertFunc) {

			alert(event.title + " end is now " + event.end.format());

			if (!confirm("is this okay?")) {
				revertFunc();
			}

		},*/

                editable: false,
                droppable: false, // this allows things to be dropped onto the calendar !!!
                drop: function (date) { // this function is called when something is dropped

                    // retrieve the dropped element's stored Event Object
                    var originalEventObject = $(this).data('eventObject');
                    var $extraEventClass = $(this).attr('data-class');


                    // we need to copy it, so that multiple events don't have a reference to the same object
                    var copiedEventObject = $.extend({}, originalEventObject);

                    // assign it the date that was reported
                    copiedEventObject.start = date;
                    copiedEventObject.allDay = false;
                    if ($extraEventClass) copiedEventObject['className'] = [$extraEventClass];

                    // render the event on the calendar
                    // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
                    $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);

                    // is the "remove after drop" checkbox checked?
                    if ($('#drop-remove').is(':checked')) {
                        // if so, remove the element from the "Draggable Events" list
                        $(this).remove();
                    }

                }
                ,
                selectable: true,
                selectHelper: true,
                select: function (start, end, allDay) {

                    bootbox.prompt("New Event Title:", function (title) {
                        if (title !== null) {
                            calendar.fullCalendar('renderEvent',
                                {
                                    title: title,
                                    start: start,
                                    end: end,
                                    allDay: allDay,
                                    className: 'label-info'
                                },
                                true // make the event "stick"
                            );
                        }
                    });


                    calendar.fullCalendar('unselect');
                }
                ,

            });
            @if(!empty( Request::get('month') ))
            $('#calendar').fullCalendar('gotoDate', '{{Request::get('month')}}');
            @endif


        })

        $('#month').change(function () {
            $('#month_form').submit();
        });
    </script>
@endsection
