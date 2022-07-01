
    <script type="text/javascript">
        $(".chosen-select").chosen();
        $('#retailerAnalytics').collapse();
    </script>
    
    <script type="text/javascript">
   
    // $('.date-picker').datetimepicker({
    //     viewMode: 'days',
    //     format: 'DD-MM-YYYY',
    //     useCurrent: true,
    //     maxDate: moment()
    // });

    $('#from_date').datetimepicker({
        format: 'YYYY-MM-DD'
    }).on('dp.change', function (e) {
        var incrementDay = moment(new Date(e.date));
        incrementDay.add(0, 'days');
        $('#to_date').data('DateTimePicker').minDate(incrementDay);
        $(this).data("DateTimePicker").hide();
    });

    $('#to_date').datetimepicker({
        format: 'YYYY-MM-DD'
    }).on('dp.change', function (e) {
        var decrementDay = moment(new Date(e.date));
        decrementDay.subtract(0, 'days');
        $('#from_date').data('DateTimePicker').maxDate(decrementDay);
        $(this).data("DateTimePicker").hide();
    });



    </script>


    <script>
    

        $( document ).ready(function() {
                 var chart = new CanvasJS.Chart("BarChart_on_load", {
                                animationEnabled: true,
                                theme:"light2",
                                title: {
                                    text: "",
                                    horizontalAlign: "left"
                                },
                                
                                axisY: {
                                    title: "Retailer Stats"
                                },
                            data: [{
                                    type: "column",
                                    // legendMarkerColor: "grey",
                                    // showInLegend: true,
                                    // legendText: "Sale Value Current Month",
                                    click: onClickRetailerCreationStats,
                                    dataPoints: <?=json_encode($dataPointsSetBar)?>
                                },


                            ],

                    });
                    chart.render();  

        });
       
    </script>
    <script type="text/javascript">
        $( document ).ready(function() {
     

                    var chart = new CanvasJS.Chart("showGraph", {
                        animationEnabled: true,
                        theme: "light2",
                        title: {
                            text: ""
                        },
                        axisY: {
                            title: "Retailer Stats"
                        },
                      axisX:{
                        gridThickness: 0,
                        tickLength: 0,
                        lineThickness: 0,
                        labelFormatter: function(){
                          return " ";
                        }
                      },
                        data: [{
                            type: "splineArea",
                            click: onClickUserRetailerCreation,
                            dataPoints: <?=json_encode($dataPoints)?>
                        }]
                    });
                    chart.render();  



                    var chart = new CanvasJS.Chart("maxRetailerSales", {
                        animationEnabled: true,
                        theme: "light2",
                        title: {
                            text: ""
                        },
                        axisY2:{
                            interlacedColor: "rgba(1,77,101,.2)",
                            gridColor: "rgba(1,77,101,.1)",
                            title: "Sale Values"
                        },
                      axisX:{
                        interval: 1,
                        gridThickness: 0,
                        tickLength: 0,
                        lineThickness: 0,
                        labelFormatter: function(){
                          return " ";
                        }
                      },
                        data: [{
                            type: "bar",
                            name: "companies",
                            axisYType: "secondary",
                            color: "#014D65",
                            click: onClickMaxRetailerSales,
                            dataPoints: <?=json_encode($maxRetailerSales)?>
                        }]
                    });
                    chart.render();  
                
          
            
        });
    </script>
    <script type="text/javascript">
        $( document ).ready(function() {
                    var chart = new CanvasJS.Chart("chartContainer", {
                        animationEnabled: true,
                        title: {
                            text: "",
                            horizontalAlign: "left"
                        },
                        
                        axisY: {
                            title: "Retailer Stats"
                        },
                        data: [{
                            type: "doughnut",
                            indexLabel: "{label}",
                            showInLegend: true,
                            legendText: "{label} : {y}",
                            click: onClickRetailerCategory,
                            dataPoints: <?=json_encode($databeatPoints)?>
                        }],

                    });
                    chart.render();  
        });
    </script>

     <script>
   function onClickRetailerCreationStats(e) {
           var label = e.dataPoint.label;
           $.ajax({
                type: "get",
                url: domain + '/getDateFormat',
                dataType: 'json',
                data: "date=" + label,
                success: function (data) {
                    // alert(data.sendDate);
                 window.open('/public/retailer?from_date='+data.sendDate+'&to_date='+data.sendDate, '_blank');
                },
                complete: function () {
                 window.open('/public/retailer?from_date='+data.sendDate+'&to_date='+data.sendDate, '_blank');
                },
                error: function () {
                }
            });
        }


        function onClickUserRetailerCreation(e) {
           var userid = e.dataPoint.symbol;
           var from_date = e.dataPoint.from_date;
           var to_date = e.dataPoint.to_date;
            window.open('/public/retailer?from_date='+from_date+'&to_date='+to_date+'&user[]='+userid, '_blank');
        }


        function onClickMaxRetailerSales(e) {
           var retailerid = e.dataPoint.retailer_id;
           var from_date = e.dataPoint.from_date;
           var to_date = e.dataPoint.to_date;
           var cryptedString = e.dataPoint.cryptedString;

            // window.open('/public/retailer?from_date='+from_date+'&to_date='+to_date+'&retailer_id='+retailerid, '_blank');
            window.open('/public/retailer_order_booking/'+cryptedString+'?start_date='+from_date+'&end_date='+to_date, '_blank');
        }


         function onClickRetailerCategory(e) {
           var outlet_type_id = e.dataPoint.outlet_type_id;
           var from_date = e.dataPoint.from_date;
           var to_date = e.dataPoint.to_date;
            window.open('/public/retailer?from_date='+from_date+'&to_date='+to_date+'&outlet[]='+outlet_type_id, '_blank');
        }
    </script>


  