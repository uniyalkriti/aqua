  <script>
    $(".chosen-select").chosen();

        $( document ).ready(function() {
            // console.log( "after_click" );
            // document.getElementById('after_click').hide;
      

      //       var barChartData = {
            
      //           labels:<?=json_encode($datesArr)?> ,
      //           datasets: [
      //               {
      //                   //SET COLORS BELOW
      //                   fillColor: "rgba(3, 111, 231,0.6)",
      //                   strokeColor: "rgb(3, 111, 231)",
      //                   data:<?=json_encode($totalOrderValue)?>
      //                   // data: [15, 55, 40, 80, 50, 180,35, 45, 90, 100, 150, 160] // SET YOUR DATA POINTS HERE
      //               }
      //           ]

      //       }



      //       // window.onload = function () {
      //       var ctx = document.getElementById("BarChart_on_load").getContext("2d");
      //       window.myLine = new Chart(ctx).Bar(barChartData, {
      //           responsive: true,
      //           hover: {
      //   animationDuration: 0
      // },
      //   animation: {
      //     duration: 1,
      //       onComplete: function() {
      //         let chartInstance = this.chart,
      //             ctx = chartInstance.ctx;

      //         ctx.textAlign = 'center';
      //         ctx.textBaseline = 'bottom';

      //         this.data.datasets.forEach(function(dataset, i)
      //                                    {
      //           let meta = chartInstance.controller.getDatasetMeta(i);
      //           meta.data.forEach(function(bar, index) {
      //             let data = dataset.data[index];
      //             ctx.fillText(data, bar._model.x + 15 , bar._model.y + 5);
      //           });
      //         });
      //       }
      //   }

      //       });
                   var chart = new CanvasJS.Chart("BarChart_on_load", {
                                animationEnabled: true,
                                theme:"light2",
                                title: {
                                    text: "",
                                    horizontalAlign: "left"
                                },
                                
                                axisY: {
                                    title: "Sale Stats"
                                },
                            data: [{
                                    type: "column",
                                    // legendMarkerColor: "grey",
                                    // showInLegend: true,
                                    // legendText: "Sale Value Current Month",
                                    click: onClickBar,
                                    dataPoints: <?=json_encode($dataPointsSetBar)?>
                                },


                            ],

                    });
                    chart.render();  
        // };
        });
       
    </script>
    <script type="text/javascript">
        $( document ).ready(function() {
        // window.onload = function()  {  
            // console.log(dataPoints);         
            $('#showGraph').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 150px 50%; color:blue;"></i>');
            var state_id = $(this).attr('state_id'); 
            var from_date = $(this).attr('from_date'); 
            var to_date = $(this).attr('to_date'); 
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "post",
                url: domain + '/get_year_wise_data',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    var chart = new CanvasJS.Chart("showGraph", {
                        animationEnabled: true,
                        theme: "light2",
                        title: {
                            text: ""
                        },
                        axisY: {
                            title: "Sale Stats"
                        },
                        data: [{
                            type: "line",
                            dataPoints: data.dataPoints
                        }]
                    });

                    chart.render();  
                }
            });
            
        });
    </script>
    <script type="text/javascript">
        $( document ).ready(function() {
        // window.onload = function()  {  
            // console.log(dataPoints);    
            $('#chartContainer').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 150px 50%; color:blue;"></i>');

            var state_id = $(this).attr('state_id'); 
            var from_date = $(this).attr('from_date'); 
            var to_date = $(this).attr('to_date'); 

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "post",
                url: domain + '/get_year_wise_data_product',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    var chart = new CanvasJS.Chart("chartContainer", {
                        animationEnabled: true,
                        title: {
                            text: "",
                            horizontalAlign: "left"
                        },
                        
                        axisY: {
                            title: "Sale Stats"
                        },
                        data: [{
                            type: "doughnut",
                            indexLabel: "{label}",
                            showInLegend: true,
                            legendText: "{label} : {y}",
                            click: onClick,
                            dataPoints: data.dataPoints
                        },


                        ],

                    });
                    chart.render();  


                }
            });
            
        });
        // dynamic modal piechart starts here
        function onClick(e) {

            var cat_id = e.dataPoint.symbol;
            var label = e.dataPoint.label;
            // alert(label);
            var chart_type = e.dataPoint.type;
            var x_value = e.dataPoint.x;
            var y_value = e.dataPoint.y;

            var state_id = $(this).attr('state_id'); 
            var from_date = $(this).attr('from_date'); 
            var to_date = $(this).attr('to_date'); 

            $('#modal_pie_chart').modal('show');
            $("#title_pi_chart").html(label+" WISE SKU DETAILS");
            $('#modalChartContainer').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 150px 50%; color:blue;"></i>');
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "post",
                url: domain + '/get_year_wise_data_product_cat_wise',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date+"&cat_id="+cat_id,
                success: function (data) {

                    
                    var chart = new CanvasJS.Chart("modalChartContainer", {
                        animationEnabled: true,
                        title: {
                            text: "",
                            horizontalAlign: "left"
                        },
                        
                        axisY: {
                            title: "Sale Stats"
                        },
                        data: [{
                            type: "doughnut",
                            indexLabel: "{label}",
                            showInLegend: true,
                            legendText: "{label} : {y}",
                            dataPoints: data.dataPoints
                        },


                        ],

                    });
                    chart.render();  


                }
            });
        }

        function onClickBar(e) {

            var cat_id = e.dataPoint.symbol;
            var label = e.dataPoint.label;
            // alert(label);
            var chart_type = e.dataPoint.type;
            var x_value = e.dataPoint.x;
            var y_value = e.dataPoint.y;

            var state_id = $(this).attr('state_id'); 
            var from_date = $(this).attr('from_date'); 
            var to_date = $(this).attr('to_date'); 

            $('#BarChartContainerModal').modal('show');
            $("#title_bar_chart").html(label+" Sale Stats");
            $('#BarChartContainer').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 150px 50%; color:blue;"></i>');
            $('.mytbodyBar').html('');
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "post",
                url: domain + '/get_month_wise_data_user_wise',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date+"&label="+label,
                success: function (data) {

                    var Sno = 1;
                    $('#m-spinner').remove();
            $('#BarChartContainer').html('');
                    
                    $.each(data.grapData, function (key, value){
                        // console.log(value);
                        $('.mytbodyBar').append("<tr><td>"+Sno+"</td><td style='text-align:left;'>"+value.user_name+"</td><td style='text-align:left;'>"+value.rolename+"</td><td style='text-align:right;'>"+value.mobile+"</td><td style='text-align:right;'>"+Math.round(value.sale_value)+"</td></tr>");
                        Sno++;
                    });
                },
                complete: function () {
                    $('#m-spinner').remove();
                },
                error: function () {
                    $('#m-spinner').remove();
                }
            });
        }
    </script>
    <script src="{{asset('nice/js/canvasjs.min.js')}}"></script>
    <script>
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
    <script>
        // var barChartData = {
            
        //     labels:<?=json_encode($datesArr)?> ,
        //     datasets: [
        //         {
        //             //SET COLORS BELOW
        //             fillColor: "rgba(76,194,88,0.5)",
        //             strokeColor: "rgba(76,194,88,0.8)",
        //             data:<?=json_encode($totalOrderValue)?>
        //             // data: [15, 55, 40, 80, 50, 180,35, 45, 90, 100, 150, 160] // SET YOUR DATA POINTS HERE
        //         }
        //     ]

        // }



        // window.onload = function () {
        //     var ctx = document.getElementById("BarChart").getContext("2d");
        //     window.myLine = new Chart(ctx).Bar(barChartData, {
        //         responsive: true,
        //         showTooltips: false,
        //         onAnimationComplete: function () {

        //                                 var ctx = this.chart.ctx;
        //                                 ctx.font = this.scale.font;
        //                                 ctx.fillStyle = this.scale.textColor
        //                                 ctx.textAlign = "center";
        //                                 ctx.textBaseline = "bottom";

        //                                 this.datasets.forEach(function (dataset) {
        //                                     dataset.bars.forEach(function (bar) {
        //                                         ctx.fillText(bar.value, bar.x, bar.y - 7);
        //                             });
        //                         })
        //                   }
        //     });
            
        // };
    </script>
    <script src="{{asset('msell/js/moment.min.js')}}"></script>
    <script src="{{asset('msell/js/bootstrap-datetimepicker.min.js')}}"></script>


   <script>
        $('#to_date').datetimepicker
        ({
        format: 'YYYY-MM-DD'
        }).on('dp.change', function (e) {
            var decrementDay = moment(new Date(e.date));
            decrementDay.subtract(0, 'days');
            $('#from_date').data('DateTimePicker').maxDate(decrementDay);
            $(this).data("DateTimePicker").hide();
        });
        $("#year").datetimepicker  ( {

            format: 'YYYY-MM'
        });
    </script>
       <!-- inline scripts related to this page -->
    <script type="text/javascript">
        jQuery(function($) {
        $('.stateWiseDetails').click(function() {

        var state_id = $(this).attr('state_id'); 
        var from_date = $(this).attr('from_date'); 
        var to_date = $(this).attr('to_date'); 
        $('#piechart-placeholder').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 150px 50%; color:blue;"></i>');

     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_state_wise_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) 
                    {
                        // console.log(data.beat_query);
                          var data_det = data.beat_query;
                            var chart = new CanvasJS.Chart("piechart-placeholder", {
                            animationEnabled: true,
                            theme: "light2",
                            title: {
                                text: ""
                            },
                            axisY: {
                                title: "Sale Stats"
                            },
                            data: [{
                                type: "pyramid",
                                indexLabel: "{label}",
                                showInLegend: true,
                                legendText: "{label} : {y}",
                                dataPoints: data.dataPoints
                            }]
                        });

                        chart.render();  
                       
                    }
                }
            });
                
        });
        
            
        
        })
    </script>

    <script type="text/javascript">
        jQuery(function($) {
        $('.primaryStateWiseDetails').click(function() {

        var state_id = $(this).attr('state_id'); 
        var from_date = $(this).attr('from_date'); 
        var to_date = $(this).attr('to_date'); 
        $('#piechart-placeholder-primary').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 150px 50%; color:blue;"></i>');

     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_state_wise_primary_booking_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) 
                    {
                        // console.log(data.beat_query);
                         var data_det = data.beat_query;
                            var chart = new CanvasJS.Chart("piechart-placeholder-primary", {
                            animationEnabled: true,
                            theme: "light2",
                            title: {
                                text: ""
                            },
                            axisY: {
                                title: "Sale Stats"
                            },
                            data: [{
                                type: "pyramid",
                                indexLabel: "{label}",
                                showInLegend: true,
                                legendText: "{label} : {y}",
                                dataPoints: data.dataPoints
                            }]
                        });

                        chart.render();  
                    }
                }
            });
                
        });
        
            
        
        })
    </script>

    <script>

    $('.sluggish_retailer').click(function() {
          var state_id = $(this).attr('state_id_data'); 
          var from_date = $(this).attr('from_date'); 
          var flag = $(this).attr('flag'); 

        $('.mytbody').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/sluggish_retailer_list',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&flag=" + flag,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.retailer_result, function (key, value){
                                // console.log(value);
                                $('.mytbody').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.dealer_name+"</td><td>"+value.l7_name+"</td><td>"+value.retailer_name+"</td><td>"+value.contact_per_name+"</td><td>"+(value.landline)+"</td></tr>");
                                Sno++;
                            });
                       

                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });

    $('.totalSalesTeam').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.mytbody_totalsalesteam').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/getTotalSalesTeamHome',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.user_details, function (key, value){
                                // console.log(value);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.mytbody_totalsalesteam').append("<tr><td>"+Sno+"</td><td><a href=user/"+value.user_n+">"+value.user_name+"</a></td><td>"+value.mobile+"</td><td>"+value.role+"</td><td>"+value.state+"</td><td>"+value.status+"</td><td>"+value.deleted_deactivated_on+"</td><td>"+value.att_count+"</td></tr>");
                                Sno++;
                            });
                   

                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });
    $('.totalBeatCoverage').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_totalbeat_coverage').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/total_beat_coverage_details',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.beat_coverage_details, function (key, value){
                                // console.log(value);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.tbody_totalbeat_coverage').append("<tr><td>"+Sno+"</td><td>"+value.beat+"</td><td>"+value.total_sale_value+"</td></tr>");
                                Sno++;
                            });
                   

                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });

    $('.totalProductiveCoverage').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.mytbody_productive_details').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/total_productive_coverage_details',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.productve_call_details, function (key, value){
                                // console.log(value);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.mytbody_productive_details').append("<tr><td>"+Sno+"</td><td><a href=user/"+value.user_n+">"+value.user_name+"</a></td><td>"+value.mobile+"</td><td>"+value.call_status_count+"</td><td>"+value.total_sale_value+"</td></tr>");
                                Sno++;
                            });
                   

                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });
    $('.user_details_on_role').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          var work_status = $(this).attr('work_status'); 
          var role_id = $(this).attr('role_id'); 
          

        $('.tbody_user_on_role').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/user_details_on_roles',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date+"&work_status=" + work_status+ "&role_id=" + role_id,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.attendance_details, function (key, value){
                                // console.log(value);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.tbody_user_on_role').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td><a href=user/"+value.user_n+">"+value.user_name+"</a></td><td>"+value.rolename+"</td><td>"+value.mobile+"</td><td>"+value.work_date+"</td></tr>");
                                Sno++;
                            });
                   
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });

       $('.dealerDetailsHomeCommon').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 

         
          

        $('.tbody_distributor_details_home_common').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_distributor_details_home_common',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
                               // var total_sale = 0;
          
                            $.each(data.dealer_details, function (key, value){


                                var url = "<a title='Distributor List' from_date='"+from_date+"' to_date='"+to_date+"' state_id='"+state_id+"' data-toggle='modal' data-target='#distributor-modal' class='distributor-modal dealerDetailsHome"+value.dealer_status+"'>";

                                 $('.tbody_distributor_details_home_common').append("<tr><td>"+Sno+"</td><td>"+value.status+"</td><td>"+url+value.count+"</a></td></tr>");
                                Sno++;

                            ////////////////////////////////////  for another modal ///////////////////////////////

                            $('.dealerDetailsHome'+value.dealer_status).click(function() {
                                // alert('1');
                                  var state_id = $(this).attr('state_id'); 
                                  var from_date = $(this).attr('from_date'); 
                                  var to_date = $(this).attr('to_date'); 
                                  var status = value.dealer_status;
                                  

                                $('.tbody_dealer_details_home').html('');
                             
                                    $.ajaxSetup({
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        }
                                    });
                                    $.ajax({
                                        type: "get",
                                        url: domain + '/get_dealer_details_home',
                                        dataType: 'json',
                                        data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date+ "&status=" + status,
                                        success: function (data) {

                                            if (data.code == 401) {

                                            }
                                            else if (data.code == 200) {

                                                       var Sno = 1;
                                  
                                                    $.each(data.dealer_details, function (key, value){
                                                        // console.log(value);
                                                        // user_n = `Crypt::encryptString(${value.user_id})` ;
                                                        $('.tbody_dealer_details_home').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td><a href=distributor/"+value.dealer_n+">"+value.dealer_name+"</a></td><td>"+value.mobile+"</td><td>"+value.dealer_beat_count+"</td><td>"+value.dealer_retailer_count+"</td><td>"+value.dealer_user_name+"</td><td>"+value.dealer_sale+"</td></tr>");
                                                        Sno++;
                                                    });
                                           

                                                   
                                               
                                             }      

                                        },
                                        complete: function () {
                                            // $('#loading-image').hide();
                                        },
                                        error: function () {
                                        }
                                    });
                            });

                            ////////////////////////////////////for another modal ends ///////////////////////////





                            });

                         
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });



    // $('.dealerDetailsHome').click(function() {
    //       var state_id = $(this).attr('state_id'); 
    //       var from_date = $(this).attr('from_date'); 
    //       var to_date = $(this).attr('to_date'); 
          

    //     $('.tbody_dealer_details_home').html('');
     
    //         $.ajaxSetup({
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             }
    //         });
    //         $.ajax({
    //             type: "get",
    //             url: domain + '/get_dealer_details_home',
    //             dataType: 'json',
    //             data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
    //             success: function (data) {

    //                 if (data.code == 401) {

    //                 }
    //                 else if (data.code == 200) {

    //                            var Sno = 1;
          
    //                         $.each(data.dealer_details, function (key, value){
    //                             // console.log(value);
    //                             // user_n = `Crypt::encryptString(${value.user_id})` ;
    //                             $('.tbody_dealer_details_home').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td><a href=distributor/"+value.dealer_n+">"+value.dealer_name+"</a></td><td>"+value.mobile+"</td><td>"+value.dealer_beat_count+"</td><td>"+value.dealer_retailer_count+"</td><td>"+value.dealer_user_name+"</td><td>"+value.dealer_sale+"</td></tr>");
    //                             Sno++;
    //                         });
                   

                           
                       
    //                  }      

    //             },
    //             complete: function () {
    //                 // $('#loading-image').hide();
    //             },
    //             error: function () {
    //             }
    //         });
    // });
    $('.dealerCoverageDetailsHome').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_dealer_coverage_details_home').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_dealer_coverage_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
          
                            $.each(data.dealer_details, function (key, value){
                                // console.log(value);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.tbody_dealer_coverage_details_home').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td><a href=distributor/"+value.dealer_n+">"+value.dealer_name+"</a></td><td>"+value.mobile+"</td><td>"+value.dealer_beat_count+"</td><td>"+value.dealer_retailer_count+"</td><td>"+value.dealer_user_name+"</td><td>"+value.dealer_sale+"</td></tr>");
                                Sno++;
                            });
                   

                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });
    $('.retailerDetailsHome').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_retailer_details_home').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_retailer_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
                               var total_sale = 0;
          
                            $.each(data.dealer_details, function (key, value){
                                // console.log(value);
                                total_sale +=  parseInt(value.retailer_sale);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.tbody_retailer_details_home').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td>"+value.l7_name+"</td><td><a href=distributor/"+value.dealer_n+">"+value.dealer_name+"</a></td><td><a href=retailer/"+value.retailer_n+">"+value.retailer_name+"</a></td><td>"+value.mobile+"</td><td>"+value.retailer_user_name+"</td><td>"+value.added_by_user+"</td><td>"+value.retailer_date+"</td><td>"+value.retailer_sale+"</td></tr>");
                                Sno++;
                            });
                            console.log(total_sale);

                   
  
                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });

    // for retailer of neha
    $('.retailerNehaDetailsHome').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          var state_string = "";
          

        $('.tbody_retailer_details_neha_home').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_retailer_details_neha_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
                               // var total_sale = 0;
          
                            $.each(data.dealer_details, function (key, value){


                                if (typeof value.state_id !== 'undefined' && value.state_id.length > 0) {
                                  $.each(value.state_id, function (skey, svalue){
                                     state_string +=  "&location_3[]="+svalue+"";
                                });
                              }

                                // console.log(state_string);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                // $('.tbody_retailer_details_neha_home').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td>"+value.l7_name+"</td><td><a href=distributor/"+value.dealer_n+">"+value.dealer_name+"</a></td><td><a href=retailer/"+value.retailer_n+">"+value.retailer_name+"</a></td><td>"+value.mobile+"</td><td>"+value.retailer_user_name+"</td><td>"+value.added_by_user+"</td><td>"+value.retailer_date+"</td><td>"+value.retailer_sale+"</td></tr>");


                               

                                var url = "<a href=retailer?status="+value.retailer_status+state_string+">";



                                 $('.tbody_retailer_details_neha_home').append("<tr><td>"+Sno+"</td><td>"+value.status+"</td><td>"+url+value.count+"</a></td></tr>");
                                Sno++;
                            });
                            // console.log(total_sale);

                   
  
                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });
    // for retailer of neha 
    $('.retailerCovergaeDetailsHome').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_retailer_coverage_details_home').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_retailer_coverage_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                               var Sno = 1;
                               var total_sale = 0;
          
                            $.each(data.dealer_details, function (key, value){
                                // console.log(value);
                                total_sale +=  parseInt(value.retailer_sale);
                                // user_n = `Crypt::encryptString(${value.user_id})` ;
                                $('.tbody_retailer_coverage_details_home').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td>"+value.l7_name+"</td><td><a href=distributor/"+value.dealer_n+">"+value.dealer_name+"</a></td><td><a href=retailer/"+value.retailer_n+">"+value.retailer_name+"</a></td><td>"+value.mobile+"</td><td>"+value.retailer_user_name+"</td><td>"+value.added_by_user+"</td><td>"+value.retailer_date+"</td><td>"+value.retailer_sale+"</td></tr>");
                                Sno++;
                            });
                            console.log(total_sale);

                   
  
                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });

    $('.beatDetailsHome').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_beat_details').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_beat_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                            var Sno = 1;
                            var total_sale = 0;
                            $.each(data.beat_details, function (key, value){
                                // console.log(value);
                                total_sale +=  parseInt(value.sale_value);
                                $('.tbody_beat_details').append("<tr><td>"+Sno+"</td><td>"+value.l3_name+"</td><td>"+value.l6_name+"</td><td>"+value.l7_name+"</td><td>"+value.dealer_count+"</td><td>"+value.retailer_count+"</td><td>"+value.sale_value+"</td></tr>");
                                Sno++;
                            });
                            console.log(total_sale);
                            
                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });
    $('.totalCallDetails').click(function() {
          var state_id = $(this).attr('state_id'); 
          var from_date = $(this).attr('from_date'); 
          var to_date = $(this).attr('to_date'); 
          

        $('.tbody_total_calls_details').html('');
     
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/get_total_call_details_home',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                    if (data.code == 401) {

                    }
                    else if (data.code == 200) {

                            var Sno = 1;
                            var total_sale = 0;
                            $.each(data.total_call_details, function (key, value){
                                // console.log(value);
                                // total_sale +=  parseInt(value.sale_value);
                                $('.tbody_total_calls_details').append("<tr><td>"+Sno+"</td><td>"+value.date+"</td><td>"+value.retailer_count+"</td><td>"+value.productive_count+"</td><td>"+value.sale_value+"</td></tr>");
                                Sno++;
                            });
                            // console.log(total_sale);
                            
                           
                       
                     }      

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
    });


 

     

    </script>


    
<script type="text/javascript">
        $( document ).ready(function() {
        // window.onload = function()  {  
            // console.log(dataPoints);         
            $('#showGraphPrimary').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 150px 50%; color:blue;"></i>');
            var state_id = '<?php echo $location_3_filter; ?>';
            var from_date = '<?php echo $from_date; ?>';
            var to_date = '<?php echo $to_date; ?>';
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/getDayWisePrimary',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {




                var chart = new CanvasJS.Chart("showGraphPrimary", {
                            animationEnabled: true,
                            title:{
                                // text: "Primary Sales Stats"
                            },
                            axisY:[{
                                title: "Sale",
                                lineColor: "#C24642",
                                tickColor: "#C24642",
                                labelFontColor: "#C24642",
                                titleFontColor: "#C24642",
                                includeZero: true,
                                // suffix: "k"
                            }],
                            axisY2: {
                                title: "Cases",
                                lineColor: "#7F6084",
                                tickColor: "#7F6084",
                                labelFontColor: "#7F6084",
                                titleFontColor: "#7F6084",
                                includeZero: true,
                                // prefix: "$",
                                // suffix: "k"
                            },
                            toolTip: {
                                shared: true
                            },
                            legend: {
                                cursor: "pointer",
                                itemclick: toggleDataSeries
                            },
                            data: [{
                                type: "line",
                                name: "Sale",
                                color: "#C24642",
                                axisYIndex: 0,
                                showInLegend: true,
                                dataPoints: data.dataPoints
                            },
                            {
                                type: "line",
                                name: "Cases",
                                color: "#7F6084",
                                axisYType: "secondary",
                                showInLegend: true,
                                dataPoints: data.dataPointsCases
                            }]
                        });
                        chart.render();

                        function toggleDataSeries(e) {
                            if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                                e.dataSeries.visible = false;
                            } else {
                                e.dataSeries.visible = true;
                            }
                            e.chart.render();
                        }











                // var chart = new CanvasJS.Chart("showGraphPrimary", {
                //     animationEnabled: true,
                //     title:{
                //         // text: "Speed And Distance Time Graph"
                //     },
                //     toolTip: {
                //         shared: true
                //     },
                //     axisX: {
                //         title: "Month",
                //         // suffix : " s"
                //     },
                //     axisY: {
                //         title: "Sale Value",
                //         titleFontColor: "#4F81BC",
                //         // suffix : " m/s",
                //         lineColor: "#4F81BC",
                //         tickColor: "#4F81BC"
                //     },
                //     axisY2: {
                //         title: "Cases",
                //         titleFontColor: "#C0504E",
                //         // suffix : " m",
                //         lineColor: "#C0504E",
                //         tickColor: "#C0504E"
                //     },
                //     data: [{
                //         type: "line",
                //         name: "Sale Value",
                //         // xValueFormatString: "#### sec",
                //         // yValueFormatString: "#,##0.00 m/s",
                //         dataPoints:  data.dataPoints
                //     },
                //     {
                //         type: "line",  
                //         axisYType: "secondary",
                //         name: "Cases",
                //         // yValueFormatString: "#,##0.# m",
                //         dataPoints: data.dataPointsCases
                //     }]
                // });
                // chart.render();




                }
            });
            
        });
    </script>




<script type="text/javascript">
        $( document ).ready(function() {
        // window.onload = function()  {  
            // console.log(dataPoints);         
            $('#showGraphPrimaryProgress').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 150px 50%; color:blue;"></i>');
            var state_id = '<?php echo $location_3_filter; ?>';
            var from_date = '<?php echo $from_date; ?>';
            var to_date = '<?php echo $to_date; ?>';
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "get",
                url: domain + '/getMonthWisePrimary',
                dataType: 'json',
                data: "state_id=" + state_id+"&from_date=" + from_date+ "&to_date=" + to_date,
                success: function (data) {

                var chart = new CanvasJS.Chart("showGraphPrimaryProgress", {
                            animationEnabled: true,
                            title:{
                                // text: "Primary Sales Monthly Progress"
                            },
                            axisY:[{
                                title: "Sale",
                                lineColor: "#C24642",
                                tickColor: "#C24642",
                                labelFontColor: "#C24642",
                                titleFontColor: "#C24642",
                                includeZero: true,
                                // suffix: "k"
                            }],
                            axisY2: {
                                title: "Cases",
                                lineColor: "#7F6084",
                                tickColor: "#7F6084",
                                labelFontColor: "#7F6084",
                                titleFontColor: "#7F6084",
                                includeZero: true,
                                // prefix: "$",
                                // suffix: "k"
                            },
                            toolTip: {
                                shared: true
                            },
                            legend: {
                                cursor: "pointer",
                                itemclick: toggleDataSeries
                            },
                            data: [{
                                type: "stepLine",
                                name: "Sale",
                                color: "#C24642",
                                axisYIndex: 0,
                                showInLegend: true,
                                dataPoints: data.dataPoints
                            },
                            {
                                type: "stepLine",
                                name: "Cases",
                                color: "#7F6084",
                                axisYType: "secondary",
                                showInLegend: true,
                                dataPoints: data.dataPointsCases
                            }]
                        });
                        chart.render();

                        function toggleDataSeries(e) {
                            if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                                e.dataSeries.visible = false;
                            } else {
                                e.dataSeries.visible = true;
                            }
                            e.chart.render();
                        }

                }
            });
            
        });
    </script>

  <script type="text/javascript">
        // $( document ).ready(function() {

        //     var request =  window.location.href;


        //    $.ajax({
        //         type: "get",
        //         url: domain + '/getBeatWiseAnalysis',
        //         dataType: 'json',
        //         data: "data=" + request,
        //         success: function (data) {
        //             var jsonString = [];
        //             var jsonString2 = [];
        //             var jsonString3 = [];
        //              jsonString= JSON.stringify(data.totalCall);
        //              jsonString2= JSON.stringify(data.productiveCall);
        //              jsonString3= JSON.stringify(data.nonproductiveCall);
           
        //              console.log(jsonString);

        //         var chart = new CanvasJS.Chart("beatWiseAnalysis", {
        //             animationEnabled: true,
        //             title:{
        //                 text: ""
        //             },
        //             axisX: {
        //                 valueFormatString: ""
        //             },
        //             axisY: {
        //                 prefix: ""
        //             },
        //             toolTip: {
        //                 shared: true
        //             },
        //             legend:{
        //                 cursor: "pointer",
        //                 itemclick: toggleDataSeries
        //             },
        //             data: [{
        //                 type: "stackedBar",
        //                 name: "Unique Outlets Visited",
        //                 showInLegend: "true",
        //                 xValueFormatString: "#,##0.00",
        //                 yValueFormatString: "#,##0.00",
        //                 dataPoints: JSON.parse(jsonString)
        //             },
                    
        //             {
        //                 type: "stackedBar",
        //                 name: "Unique Outlets Billed",
        //                 showInLegend: "true",
        //                 xValueFormatString: "#,##0.00",
        //                 yValueFormatString: "#,##0.00",
        //                 dataPoints: JSON.parse(jsonString3)
        //             },
        //             {
        //                 type: "stackedBar",
        //                 name: "Unique Outlets Billed",
        //                 showInLegend: "true",
        //                 xValueFormatString: "#,##0.00",
        //                 yValueFormatString: "#,##0.00",
        //                 dataPoints: JSON.parse(jsonString2)
        //             }]
        //         });
        //         chart.render();


        //         function toggleDataSeries(e) {
        //             if(typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
        //                 e.dataSeries.visible = false;
        //             }
        //             else {
        //                 e.dataSeries.visible = true;
        //             }
        //             chart.render();
        //         }




                   
        //         },
        //         complete: function () {
        //         },
        //         error: function () {
        //         }
        //     });


          
            
        // });

    </script>

    


  <script type="text/javascript">
        $( document ).ready(function() {

            var request =  window.location.href;


           $.ajax({
                type: "get",
                url: domain + '/getBeatWiseAnalysisGraph',
                dataType: 'json',
                data: "data=" + request,
                success: function (data) {
                    var jsonString = [];
                    var jsonString2 = [];
                    var jsonString3 = [];
                     jsonString= JSON.stringify(data.totalCall);
                     jsonString2= JSON.stringify(data.productiveCall);
                     jsonString3= JSON.stringify(data.nonproductiveCall);
           
                     console.log(jsonString);

                    var chart = new CanvasJS.Chart("beatWiseAnalysisGraph", {
                    animationEnabled: true,
                    theme: "light2",
                    title: {
                        text: ""
                    },
                    axisX: {
                        valueFormatString: "MMM"
                    },
                    axisY: {
                        prefix: "",
                        labelFormatter: addSymbols
                    },
                    toolTip: {
                        shared: true
                    },
                    legend: {
                        cursor: "pointer",
                        itemclick: toggleDataSeries
                    },
                    data: [
                    {
                        type: "column",
                        name: "Unique Outlets Visited",
                        showInLegend: true,
                        xValueFormatString: "#,##0.00",
                        yValueFormatString: "#,##0.00",
                        dataPoints: JSON.parse(jsonString)
                    }, 
                    {
                        type: "line",
                        name: "Unique Outlets UnBilled",
                        showInLegend: true,
                        xValueFormatString: "#,##0.00",
                        yValueFormatString: "#,##0.00",
                        dataPoints: JSON.parse(jsonString3)
                    },
                    {
                        type: "area",
                        name: "Unique Outlets Billed",
                        markerBorderColor: "white",
                        markerBorderThickness: 2,
                        showInLegend: true,
                        xValueFormatString: "#,##0.00",
                        yValueFormatString: "#,##0.00",
                        dataPoints: JSON.parse(jsonString2)
                    }]
                });
                chart.render();

                function addSymbols(e) {
                    var suffixes = ["", "K", "M", "B"];
                    var order = Math.max(Math.floor(Math.log(e.value) / Math.log(1000)), 0);

                    if(order > suffixes.length - 1)                 
                        order = suffixes.length - 1;

                    var suffix = suffixes[order];      
                    return CanvasJS.formatNumber(e.value / Math.pow(1000, order)) + suffix;
                }

                function toggleDataSeries(e) {
                    if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                        e.dataSeries.visible = false;
                    } else {
                        e.dataSeries.visible = true;
                    }
                    e.chart.render();
                }




                   
                },
                complete: function () {
                },
                error: function () {
                }
            });


          
            
        });

    </script>
    
