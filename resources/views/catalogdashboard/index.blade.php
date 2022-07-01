@extends('layouts.master')

@section('title')
    <title>{{ config('app.name', '') }}</title>
@endsection
@section('css')
       <link rel="stylesheet" href="{{asset('msell/css/common.css')}}"/>
        <link rel="stylesheet" href="{{asset('msell/css/chosen.min.css')}}" />
        <link rel="stylesheet" href="{{asset('msell/css/daterangepicker.min.css')}}" />
        <link rel="stylesheet" href="{{asset('msell/css/bootstrap-datetimepicker.min.css')}}" />
        <style>
        div.google-visualization-tooltip {
            white-space: nowrap;
            height: 10px;
            border-radius: 10px;
            border: 1px solid #435279;
            padding: 10px;
            color: #3d4f61;
            }
        </style>
@endsection
@section('body')

    <div class="main-content">
        <div class="main-content-inner">
            

            <div class="page-content">
                <div class="ace-settings-container" id="ace-settings-container">
                    <div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
                        <i class="ace-icon fa fa-cog bigger-130"></i>
                    </div>

                    <div class="ace-settings-box clearfix" id="ace-settings-box">
                        <div class="pull-left width-50">
                            <div class="ace-settings-item">
                                <div class="pull-left">
                                    <select id="skin-colorpicker" class="hide">
                                        <option data-skin="no-skin" value="#438EB9">#438EB9</option>
                                        <option data-skin="skin-1" value="#222A2D">#222A2D</option>
                                        <option data-skin="skin-2" value="#C6487E">#C6487E</option>
                                        <option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>
                                    </select>
                                </div>
                                <span>&nbsp; Choose Skin</span>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-navbar" autocomplete="off" />
                                <label class="lbl" for="ace-settings-navbar"> Fixed Navbar</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-sidebar" autocomplete="off" />
                                <label class="lbl" for="ace-settings-sidebar"> Fixed Sidebar</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-breadcrumbs" autocomplete="off" />
                                <label class="lbl" for="ace-settings-breadcrumbs"> Fixed Breadcrumbs</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl" autocomplete="off" />
                                <label class="lbl" for="ace-settings-rtl"> Right To Left (rtl)</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-add-container" autocomplete="off" />
                                <label class="lbl" for="ace-settings-add-container">
                                    Inside
                                    <b>.container</b>
                                </label>
                            </div>
                        </div><!-- /.pull-left -->

                        <div class="pull-left width-50">
                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-hover" autocomplete="off" />
                                <label class="lbl" for="ace-settings-hover"> Submenu on Hover</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-compact" autocomplete="off" />
                                <label class="lbl" for="ace-settings-compact"> Compact Sidebar</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-highlight" autocomplete="off" />
                                <label class="lbl" for="ace-settings-highlight"> Alt. Active Item</label>
                            </div>
                        </div><!-- /.pull-left -->
                    </div><!-- /.ace-settings-box -->
                </div><!-- /.ace-settings-container -->

                <div class="page-header">
                    <h1>
                        Catalog Dashboard
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            overview &amp; stats
                        </small>
                    </h1>
                </div>
                <!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12">
                  
                            <?php
                            if(isset($from_date))
                            {
                                $year = date('M-Y',strtotime($from_date));
                                
                            }
                            else {
                                $year = date('Y-m');
                            }
                            $from_date = $year."-01";
                            $to_date = date('Y-m-d');
                            $yearMonth = date('M-Y',strtotime($year));
                            ?>
                        <!-- PAGE CONTENT BEGINS -->
                     
                        
                        <div class="row">
                           <div class="col-md-12" style="text-align: center">
                                <span class="label label-success arrowed-in arrowed-in-right" style="width:100px">{{ $yearMonth }}</span>
                           </div>
                                <form class="form-horizontal open collapse in" action="catalogdashboard" method="GET" role="form"
                                enctype="multipart/form-data">
                              {!! csrf_field() !!}
                                
                                <div class="col-xs-6 col-sm-6 col-lg-2">
                                            
                                        <div class="">
                                            <label class="control-label no-padding-right"
                                                   for="name">Date Range</label>
                                                   <input value="{{$ReturnDate}}" class="form-control" type="text" name="date_range_picker" id="id-date-range-picker-1" readonly />
                                        </div>
                                    
                                </div> 
                            
                                <div class="col-xs-6 col-sm-6 col-lg-1">
                                        <button type="submit" class="btn btn-sm btn-primary btn-block mg-b-10 input-sm"
                                                style="margin-top: 28px;"><i class="fa fa-search mg-r-10"></i>
                                            Find
                                        </button>
                                    </div>

                                </form>
                        </div>
                        <div class="row">
                              <div class="space-6"></div>

                                  <div class="col-sm-12 infobox-container">
                                     @if(!empty($catalog1Sale))
                                                @foreach($catalog1Sale as $c1key=>$cval)
                                                   @php
                                                    $color = !empty($color_code[$cval->c2_id])?$color_code[$cval->c2_id]:'';
                                                    @endphp
                                                   <div style="background-color:{{$color}}" class="infobox infobox-large infobox-dark">
                                                        <div class="infobox-icon">
                                                            <i class="ace-icon fa fa-users"></i>
                                                        </div>
                    
                                                        <div class="infobox-data">
                                                            <div class="infobox-data-number">{{$cval->data}}</div>
                                                            <div class="infobox-content">{{$cval->label}}</div>
                                                            
                                                        </div>
                                                     </div>
                                                    
                                                @endforeach 
                                            @endif

   
                                     </div>
    

                                        <div class="vspace-12-sm"></div>

                                       
                                        </div><!-- /.row -->
                                        <div class="row">
                                              <div class="col-md-2"></div>
                                             
                                             <div class="col-md-12">


                                             <div class="widget-box">
                                                <div class="widget-header widget-header-flat">
                                                        <h4 class="widget-title lighter">
                                                            <i class="ace-icon fa fa-signal"></i>
                                                            Catalog Stats
                                                        </h4>
                                                <div class="widget-toolbar ">
                                                        <a href="#" data-action="collapse">
                                                            <i class="ace-icon fa fa-chevron-up"></i>
                                                        </a>
                                                    </div>
                                                    <h5 class="widget-title lighter pull-right"> &nbsp; || &nbsp;
                                                            <i class="ace-icon fa fa-circle green"></i>
                                                            Catalog Value
                                                    </h5> 
                                                   </div>
    
                                                    <div class="widget-body">
                                                        <div class="widget-main padding-4">
                                                                <canvas id="BarChart" height="100" class="img-responsive" ></canvas>
                                                        </div><!-- /.widget-main -->
                                                    </div><!-- /.widget-body -->
                                             </div><!-- /.widget-box -->
                                            </div><!-- /.col -->
                                         </div><!-- /.row -->

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="widget-box" style="overflow: auto;">
                                                    <div class="widget-header widget-header-flat widget-header-small">
                                                        <h5 class="widget-title">
                                                            <i class="ace-icon fa fa-signal"></i>
                                                            Traffic Sources
                                                        </h5>

                                                    </div>

                                                    <div class="widget-body">
                                                        <div class="widget-main">
                                                            <!-- <div id="piechart-placeholder"></div> -->

                                                            <div id="piechart2"></div>

                                                            <!-- <div id="chart" style="width:900px; height:500px;"></div>  -->

                                                            <div class="hr hr8 hr-double"></div>

                                                        
                                                        </div><!-- /.widget-main -->
                                                    </div><!-- /.widget-body -->
                                                </div><!-- /.widget-box -->
                                            </div><!-- /.col -->
                                            <div class="col-sm-6">
                                                <div class="widget-box">
                                                    <div class="widget-header widget-header-flat widget-header-small">
                                                        <h5 class="widget-title">
                                                            <i class="ace-icon fa fa-signal"></i>
                                                            Category Wise Order Booking ({{ $from_date .' To '. $to_date }})
                                                        </h5>
                
                                                        
                                                    </div>
                
                                                    <div class="widget-body">
                                                        <div class="widget-main">
                                                            <div class="clearfix">
                                                                <ul id="tasks" class="item-list">
                                                                    @foreach($catalog1Sale as $c1key=>$cval)
                                                                    @if($cval->data >0)
                                                                         @php
                                                                          $color_new = !empty($color_code[$cval->c2_id])?$color_code[$cval->c2_id]:'';
                                                                          @endphp
                                                                    <li class="item-orange clearfix">
                                                                        <label class="inline">
                                                                        <span class="lbl">{{$cval->label}}</span>
                                                                        </label>
                                                                        <div class="pull-right " data-size="30" data-color="">
                                                                                <i class="ace-icon fa fa-rupee"></i>
                                                                            <span class="percent" style="color:{{$color_new}}">{{$cval->data}}</span>
                                                                        </div>
                                                                    </li>
                                                                    @endif
                                                                    @endforeach
                                                                </ul>
                
                                                                
                                                            </div>
                                                        </div><!-- /.widget-main -->
                                                    </div><!-- /.widget-body -->
                                                </div><!-- /.widget-box -->
                                            </div>
                                        </div>    

                                       </div>
                                    </div><!-- /.page-content -->
                                </div>
                            </div>
                            
                        </div>

                           

@endsection
@section('js')

     <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('msell/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('msell/js/common.js')}}"></script>
    <script src="{{asset('msell/js/daterangepicker.min.js')}}"></script>    
    <script src="{{asset('msell/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.easypiechart.min.js')}}"></script>    
    <script src="{{asset('msell/js/jquery.flot.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.flot.pie.min.js')}}"></script>
    <script src="{{asset('msell/js/jquery.flot.resize.min.js')}}"></script>
    <script src="https://www.gstatic.com/charts/loader.js"></script>

    <!-- <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script>
    google.charts.load("visualization", "1", { packages: ["corechart"] });
    google.charts.setOnLoadCallback(DrawPieChart);

    function DrawPieChart() {
        // DEFINE AN ARRAY OF DATA.
        var arrSales = <?=json_encode($catalog1SaleLegend)?> 

        // SET CHART OPTIONS.
        var options = {
            title: 'Monthly Sales',
            is3D: true,
            pieSliceText: 'value-and-percentage'
        };

        var figures = google.visualization.arrayToDataTable(arrSales);

        // WHERE TO SHOW THE CHART (DIV ELEMENT).
        var chart = new google.visualization.PieChart(document.getElementById('chart'));

        // DRAW THE CHART.
        chart.draw(figures, options);
    }
</script> -->


    <script>
            function drawChart() {
        var dataArray = <?=json_encode($catalog1SaleLegend)?> 


        var total = getTotal(dataArray);


            // Adding tooltip column  
        for (var i = 0; i < dataArray.length; i++) {
        dataArray[i].push(customTooltip(dataArray[i][0], dataArray[i][1], total));
        }

        // Changing legend  
        for (var i = 0; i < dataArray.length; i++) {
        dataArray[i][0] = dataArray[i][0] + " " +"[" +
                    dataArray[i][1]  +"]"+ "{"+((dataArray[i][1] / total) * 100).toFixed(1) + "%"+"}"; 
        }

        // Column names
        dataArray.unshift(['Goal Name', 'No. of times Requested', 'Tooltip']);

        var data = google.visualization.arrayToDataTable(dataArray);

        // Setting role tooltip
        data.setColumnProperty(2, 'role', 'tooltip');
        data.setColumnProperty(2, 'html', true);

        var options = {
            title: '',
            width: 900,
            height: 400,
            tooltip: { isHtml: true }
        };
            
        var chart = new google.visualization.PieChart(document.getElementById('piechart2'));
        chart.draw(data, options);
        }

        function customTooltip(name, value, total) {
        return name + '<br/><b>' + value + ' (' + ((value/total) * 100).toFixed(1) + '%)</b>';
        }

        function getTotal(dataArray) {
        var total = 0;
        for (var i = 0; i < dataArray.length; i++) {
        total += dataArray[i][1];
        }
        return total;
        }


        google.load('visualization', '1', {packages:['corechart'], callback: drawChart});
    </script>
    
    <!-- ############### Date Range Picker Script starts Here ################### -->
<script>
            //datepicker plugin
                //link
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
    <!-- ############### Date Range Picker Script Ends Here ################### -->



    <!-- ############### PIE Chart Script Starts Here ################### -->

<!-- inline scripts related to this page -->
<script type="text/javascript">
            // jQuery(function($) {
            //  $('.easy-pie-chart.percentage').each(function(){
            //      var $box = $(this).closest('.infobox');
            //      var barColor = $(this).data('color') || (!$box.hasClass('infobox-dark') ? $box.css('color') : 'rgba(255,255,255,0.95)');
            //      var trackColor = barColor == 'rgba(255,255,255,0.95)' ? 'rgba(255,255,255,0.25)' : '#E2E2E2';
            //      var size = parseInt($(this).data('size')) || 50;
            //      $(this).easyPieChart({
            //          barColor: barColor,
            //          trackColor: trackColor,
            //          scaleColor: false,
            //          lineCap: 'butt',
            //          lineWidth: parseInt(size/10),
            //          animate: ace.vars['old_ie'] ? false : 1000,
            //          size: size
            //      });
            //  })
            
            //  $('.sparkline').each(function(){
            //      var $box = $(this).closest('.infobox');
            //      var barColor = !$box.hasClass('infobox-dark') ? $box.css('color') : '#FFF';
            //      $(this).sparkline('html',
            //                       {
            //                          tagValuesAttribute:'data-values',
            //                          type: 'bar',
            //                          barColor: barColor ,
            //                          chartRangeMin:$(this).data('min') || 0
            //                       });
            //  });
            
            
            
            //   $.resize.throttleWindow = false;
            
            //   var placeholder = $('#piechart-placeholder').css({'width':'90%' , 'min-height':'250px'});
            //   var data = <?=json_encode($catalog1Sale)?> 
            //   function drawPieChart(placeholder, data, position) {
            //        $.plot(placeholder, data, {
            //      series: {
            //          pie: {
            //              show: true,
            //              tilt:0.8,
            //              highlight: {
            //                  opacity: 0.25
            //              },
            //              stroke: {
            //                  color: '#fff',
            //                  width: 2
            //              },
            //              startAngle: 2
            //          }
            //      },
            //      legend: {
            //          show: true,
            //          position: position || "ne", 
            //          labelBoxBorderColor: null,
            //          margin:[-30,15]
            //      }
            //      ,
            //      grid: {
            //          hoverable: true,
            //          clickable: true
            //      }
            //   })
            //  }
            //  drawPieChart(placeholder, data);
            
        
            //  placeholder.data('chart', data);
            //  placeholder.data('draw', drawPieChart);
            
            
            
            //   var $tooltip = $("<div class='tooltip top in'><div class='tooltip-inner'></div></div>").hide().appendTo('body');
            //   var previousPoint = null;
            
            //   placeholder.on('plothover', function (event, pos, item) {
            //  if(item) {
            //      if (previousPoint != item.seriesIndex) {
            //          previousPoint = item.seriesIndex;
            //          var tip = item.series['label'] + " : " + Math.round(item.series['percent'])+'%';
            //          $tooltip.show().children(0).text(tip);
            //      }
            //      $tooltip.css({top:pos.pageY + 10, left:pos.pageX + 10});
            //  } else {
            //      $tooltip.hide();
            //      previousPoint = null;
            //  }
                
            //  });
            
                
            
            // })
        </script>
    <!-- ############### PIE Chart Script Ends Here ################### -->
        

    <!-- ############### Graph Script Starts Here ################### -->

        <script src="{{asset('nice/js/BarChart.js')}}"></script>
<script>
        var barChartData = {
            
            labels:<?=json_encode($CatalogName)?> ,
            datasets: [
                {
                    //SET COLORS BELOW
                    fillColor: "rgba(76,194,88,0.5)",
                    strokeColor: "rgba(76,194,88,0.8)",
                    data:<?=json_encode($totalOrderValue)?> 
                    // data: [15, 55, 40, 80, 50, 180,35, 45, 90, 100, 150, 160] // SET YOUR DATA POINTS HERE
                }
            ]

        }



        window.onload = function () {
            var ctx = document.getElementById("BarChart").getContext("2d");
            window.myLine = new Chart(ctx).Bar(barChartData, {
                responsive: true,
                showTooltips: false,
                onAnimationComplete: function () {

                                        var ctx = this.chart.ctx;
                                        ctx.font = this.scale.font;
                                        ctx.fillStyle = this.scale.textColor
                                        ctx.textAlign = "center";
                                        ctx.textBaseline = "left";

                                        this.datasets.forEach(function (dataset) {
                                            dataset.bars.forEach(function (bar) {
                                                ctx.fillText(bar.value, bar.x, bar.y - 7);
                                    });
                                })
                          }
            });
            
        };
    </script>
    <!-- ############### Graph Script Ends Here ################### -->

@endsection

