@extends('layouts.master')

@section('title')
    <title>{{Lang::get('common.'.$current_menu)}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('nice/css/chosen.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/common.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/jquery-confirm.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('nice/css/toastr.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
@endsection

@section('body')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #4287BA;">
                <li style="color: white">
                    <ul class="breadcrumb">
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a style="color: white" href="#">Retailer</a>
                    </li>

                    <li class="active" style="color: white">@Lang('common.'.$current_menu)</li>
                </ul><!-- /.breadcrumb -->


                <!-- /.nav-search -->
            </div>

            <div class="page-content">
            @if(empty($records))
                   <div class="br-pagebody">
                    @if (session()->has('flash_notification.message'))
                        <div class="container">
                            <div class="hidemsg alert alert-{{ session()->get('flash_notification.level') }}">
                                {!! session()->get('flash_notification.message') !!}
                            </div>
                        </div>
                    @endif
                @endif 
                <div class="row">
                    <div class="col-xs-12">
                            <form class="form-horizontal open collapse in" action="" method="GET" id="sale-order" role="form"
                            enctype="multipart/form-data"> 
                            <div class="col-xs-2">
                                <label class="control-label no-padding-right"
                                for="name">State</label>
                                <select name="region" id="region" class="form-control chosen-select">
                                    <option value="">please select</option>
                                    @if(!empty($region_fltr))
                                    @foreach($region_fltr as $k=>$r)
                                
                                        <option value="{{$k}}"  {{Request::get('status')==$k?'selected':''}}> {{$r}} 
                                        </option>
                                    @endforeach
                                    @endif

                                </select>
                            </div>
                            <div class="col-xs-2">
                                <label class="control-label no-padding-right"
                                for="name">User</label>
                                <select multiple name="user[]" id="user" class="form-control chosen-select">
                                    <option disabled="disabled" value="">Select</option>
                                    @if(!empty($user_fltr))
                                    @foreach($user_fltr as $k=>$r)
                                    <option value="{{$k}}">{{$r}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-xs-2">
                                <label class="control-label no-padding-right"
                                for="name">Status</label>
                                <select  name="status" id="status" class="form-control chosen-select">
                                    <option  value="">Select</option>
                                    <option  {{Request::get('status')==1?'selected':''}} value="1">Productive</option>
                                    <option  {{Request::get('status')==2?'selected':''}} value="2">Non Productive</option>
                                </select>
                            </div>
                            <div class="col-xs-2">
                                <div class="">
                                    <label class="control-label no-padding-right"
                                           for="name">Month</label>
                                    <input autocomplete="off" type="text" name="month" id="month" class="form-control" placeholder="Month">
                                </div>
                            </div>
                            <div class="col-xs-1">
                                <button type="submit" class="btn btn-xs btn-primary btn-block mg-b-10"
                                style="margin-top: 25px;"><i class="fa fa-search mg-r-10"></i>
                                Find
                                </button><br>
                            </div>
                            <div class="col-xs-2">
                                <b style="color: green;">Count Productive : {{COUNT($count_productive)}} </b><br>
                                <b style="color: red;">Count Non Productive :  {{COUNT($count_non_productive)}} </b>
                            </div>
                           <br>

                            <div id="g_map" style="width:100%;height:450px;"></div>
                        <div class="hr hr-18 dotted hr-double"></div>

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div>
            <!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->

@endsection

@section('js')
<script>
    var array = <?php echo $records ?>;
    // console.log(array);
    var position = array[0].split(',');
   function initMap()
    {
        var iconnumber = 0;
        var sale_value = 0;
        var last_sale_date_time = '';
        var mapr={lat:Number(position[0]), lng:Number(position[1])};
        // console.log(mapr); 
        
        var mapg= new google.maps.Map(document.getElementById("g_map"),{
            zoom:10,
            center :mapr
        });
        setMarkers(mapg,iconnumber);
    }
    function setMarkers(mapg,iconnumber)
    {
        for(var i=0; i<array.length; i++)
        {
            var color = 'ff0000';
            iconnumber = iconnumber+1;
            var ar = array[i].split(',');

            var status = (ar[3]);
            sale_value = (ar[4]);
            last_sale_date_time = (ar[5]);
            // console.log(status);

            if(status == 1)
            {
                color = '00ff55';
            }
            else if(status == 2)
            {
                color = 'ff0000';
            }
            var marker=new google.maps.Marker({
                position: {lat:parseFloat(ar[0]) , lng:parseFloat(ar[1])},
                map: mapg,
                title:"LAT :"+parseFloat(ar[0])+" Lng:"+parseFloat(ar[1]),
                icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld='+iconnumber+'|'+color+'|000000',
                animation: google.maps.Animation.CUSTOM_FADE
                // icon: iconBase
            });
            
            marker.setPosition(mapg);
            var infowindow=new google.maps.InfoWindow();
            (function(marker,ar){
                google.maps.event.addListener(marker,'click',function(e)
                {
                            
                    var latlng = new google.maps.LatLng(Number(ar[0]),Number(ar[1]));
                    var geocoder= new google.maps.Geocoder();
                geocoder.geocode({'location': latlng}, function(results, status) 
                {
                  if (status === 'OK') 
                  {
                    if (results[0]) 
                    {
                        var contents="<div style='color:blue; width:200px;' ><strong>"+ar[2]+"</strong><br>"+results[0].formatted_address+"<br><strong>Sale Value : </strong>"+ar[4]+"<br><strong>Last Sale Date Time : </strong>"+ar[5]+" </div>";
                        infowindow.setContent(contents);
                        infowindow.open(mapg,marker);
                    } else {
                      window.alert('No results found');
                    }
                  } else {
                    window.alert('Geocoder failed due to: ' + status);
                  }
                });
                //    var latLng = "LAT : "+ar[0]+" Lng:"+ar[1]+"<br>"+ar[2];
                   
                });
            })(marker,ar);
         

        }


    }       
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD96suZt39wVDLJ_D1xRmDQ2JA3I5m4Xwg&callback=initMap">
    </script>
    {{-- END FOR MAP --}}
    <script src="{{asset('nice/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('nice/js/jquery.dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('nice/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('nice/js/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('nice/js/dataTables.select.min.js')}}"></script>
    <script src="{{asset('js/retailer.js')}}"></script>
    <script src="{{asset('nice/js/jquery-confirm.min.js')}}"></script>
    <script src="{{asset('msell/js/moment.min.js')}}"></script>     
    <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
    <script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>        
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>

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
            if ($('#search').val() != '') {
                $('#user-search').submit();
            }
        }
    </script>
    <script>
        $(document).ready(function () {

            $('#openBtn').click(function () {
                $('#myModal').modal({
                    show: true
                })
            });

            $(document).on('show.bs.modal', '.modal', function (event) {
                var zIndex = 1040 + (10 * $('.modal:visible').length);
                $(this).css('z-index', zIndex);
                setTimeout(function () {
                    $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
                }, 0);
            });


        });
    </script>
    <script src="{{asset('nice/js/toastr.min.js')}}"></script>
    @if(Session::has('message'))
        <script>
            toastr.{{ Session::get('class', 'info') }}("{{ Session::get('message') }}");
        </script>
    @endif
        <script>
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#dynamic-table tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
</script>

    <script type="text/javascript">
        jQuery(function ($) {
            //initiate dataTables plugin
            var myTable =
                $('#dynamic-table1')
                //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                    .DataTable({
                        bAutoWidth: false,
                        "aoColumns": [
                            {"bSortable": false},
                            null, null, null, null, null, null, null, null,
                            {"bSortable": false}
                        ],
                        "aaSorting": [],


                        //"bProcessing": true,
                        //"bServerSide": true,
                        //"sAjaxSource": "http://127.0.0.1/table.php"   ,

                        //,
                        //"sScrollY": "200px",
                        //"bPaginate": false,

                        //"sScrollX": "100%",
                        //"sScrollXInner": "120%",
                        //"bScrollCollapse": true,
                        //Note: if you are applying horizontal scrolling (sScrollX) on a ".table-bordered"
                        //you may want to wrap the table inside a "div.dataTables_borderWrap" element

                        //"iDisplayLength": 50


                        select: {
                            style: 'multi'
                        }
                    });


            $.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-overlap btn-group btn-overlap';

            new $.fn.dataTable.Buttons(myTable, {
                buttons: [
                    {
                        "extend": "colvis",
                        "text": "<i class='fa fa-search bigger-110 blue'></i> <span class='hidden'>Show/hide columns</span>",
                        "className": "btn btn-white btn-primary btn-bold",
                        columns: ':not(:first):not(:last)'
                    },
                    {
                        "extend": "copy",
                        "text": "<i class='fa fa-copy bigger-110 pink'></i> <span class='hidden'>Copy to clipboard</span>",
                        "className": "btn btn-white btn-primary btn-bold"
                    },
                    {
                        "extend": "csv",
                        "text": "<i class='fa fa-database bigger-110 orange'></i> <span class='hidden'>Export to CSV</span>",
                        "className": "btn btn-white btn-primary btn-bold"
                    },
                    {
                        "extend": "excel",
                        "text": "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Export to Excel</span>",
                        "className": "btn btn-white btn-primary btn-bold"
                    },
                    {
                        "extend": "pdf",
                        "text": "<i class='fa fa-file-pdf-o bigger-110 red'></i> <span class='hidden'>Export to PDF</span>",
                        "className": "btn btn-white btn-primary btn-bold"
                    },
                    {
                        "extend": "print",
                        "text": "<i class='fa fa-print bigger-110 grey'></i> <span class='hidden'>Print</span>",
                        "className": "btn btn-white btn-primary btn-bold",
                        autoPrint: false,
                        message: 'This print was produced using the Print button for DataTables'
                    }
                ]
            });
            myTable.buttons().container().appendTo($('.tableTools-container'));

            //style the message box
            var defaultCopyAction = myTable.button(1).action();
            myTable.button(1).action(function (e, dt, button, config) {
                defaultCopyAction(e, dt, button, config);
                $('.dt-button-info').addClass('gritter-item-wrapper gritter-info gritter-center white');
            });


            var defaultColvisAction = myTable.button(0).action();
            myTable.button(0).action(function (e, dt, button, config) {

                defaultColvisAction(e, dt, button, config);


                if ($('.dt-button-collection > .dropdown-menu').length == 0) {
                    $('.dt-button-collection')
                        .wrapInner('<ul class="dropdown-menu dropdown-light dropdown-caret dropdown-caret" />')
                        .find('a').attr('href', '#').wrap("<li />")
                }
                $('.dt-button-collection').appendTo('.tableTools-container .dt-buttons')
            });

            ////

            setTimeout(function () {
                $($('.tableTools-container')).find('a.dt-button').each(function () {
                    var div = $(this).find(' > div').first();
                    if (div.length == 1) div.tooltip({container: 'body', title: div.parent().text()});
                    else $(this).tooltip({container: 'body', title: $(this).text()});
                });
            }, 500);


            myTable.on('select', function (e, dt, type, index) {
                if (type === 'row') {
                    $(myTable.row(index).node()).find('input:checkbox').prop('checked', true);
                }
            });
            myTable.on('deselect', function (e, dt, type, index) {
                if (type === 'row') {
                    $(myTable.row(index).node()).find('input:checkbox').prop('checked', false);
                }
            });


            /////////////////////////////////
            //table checkboxes
            $('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);

            //select/deselect all rows according to table header checkbox
            $('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function () {
                var th_checked = this.checked;//checkbox inside "TH" table header

                $('#dynamic-table').find('tbody > tr').each(function () {
                    var row = this;
                    if (th_checked) myTable.row(row).select();
                    else myTable.row(row).deselect();
                });
            });

            //select/deselect a row when the checkbox is checked/unchecked
            $('#dynamic-table').on('click', 'td input[type=checkbox]', function () {
                var row = $(this).closest('tr').get(0);
                if (this.checked) myTable.row(row).deselect();
                else myTable.row(row).select();
            });


            $(document).on('click', '#dynamic-table .dropdown-toggle', function (e) {
                e.stopImmediatePropagation();
                e.stopPropagation();
                e.preventDefault();
            });


            //And for the first simple table, which doesn't have TableTools or dataTables
            //select/deselect all rows according to table header checkbox
            var active_class = 'active';
            $('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function () {
                var th_checked = this.checked;//checkbox inside "TH" table header

                $(this).closest('table').find('tbody > tr').each(function () {
                    var row = this;
                    if (th_checked) $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
                    else $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
                });
            });

            //select/deselect a row when the checkbox is checked/unchecked
            $('#simple-table').on('click', 'td input[type=checkbox]', function () {
                var $row = $(this).closest('tr');
                if ($row.is('.detail-row ')) return;
                if (this.checked) $row.addClass(active_class);
                else $row.removeClass(active_class);
            });


            /********************************/
            //add tooltip for small view action buttons in dropdown menu
            $('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});

            //tooltip placement on right or left
            function tooltip_placement(context, source) {
                var $source = $(source);
                var $parent = $source.closest('table')
                var off1 = $parent.offset();
                var w1 = $parent.width();

                var off2 = $source.offset();
                //var w2 = $source.width();

                if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2)) return 'right';
                return 'left';
            }


            /***************/
            $('.show-details-btn').on('click', function (e) {
                e.preventDefault();
                $(this).closest('tr').next().toggleClass('open');
                $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
            });
            /***************/


            /**
             //add horizontal scrollbars to a simple table
             $('#simple-table').css({'width':'2000px', 'max-width': 'none'}).wrap('<div style="width: 1000px;" />').parent().ace_scroll(
             {
               horizontal: true,
               styleClass: 'scroll-top scroll-dark scroll-visible',//show the scrollbars on top(default is bottom)
               size: 2000,
               mouseWheelLock: true
             }
             ).css('padding-top', '12px');
             */


        })

    </script>
    <!-- code for filter dependency in retailer_map -->
    <script>
     $(document).on('change', '#region', function () {
        val = $(this).val();
        _hq = $('#user');
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
                url: domain + '/get_statewise_user',
                dataType: 'json',
                data: "id=" + val,
                success: function (data) {
              
                  
                        template = '<option value="" >Select</option>';

                        $.each(data.result, function (key, value) {
                            console.log(value);
                            if (value.name != '') {
                                template += '<option value="' + value.user_id + '" >' + value.user_name + '</option>';
                            }
                        });
                        console.log(template);
                       // alert(_hq.val());
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
    <!-- code end for filter dependency in retailer_map -->
    <script>
        $("#month").datetimepicker  ( {
            clear: "Clear",
            format: 'YYYY-MM'
        });
                
    </script>

@endsection