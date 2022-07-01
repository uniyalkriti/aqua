<!DOCTYPE html>
  <?php
  error_reporting(0);
  require_once('../function/graph_reports.php');
  require_once('../../admin/include/conectdb.php');
  //$myobjsale  = new sale_dashboard();
  $id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id'])));
  $role_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['role_id'])));
  $data = get_total_user($id,$role_id);
  
  
 
  ?>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>msell-gopal</title>

    <!-- Bootstrap -->
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="../vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- bootstrap-progressbar -->
    <link href="../vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
    <!-- JQVMap -->
    <link href="../vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet"/>
    <!-- bootstrap-daterangepicker -->
    <link href="../vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="index.php" class="site_title"></i> <span>msell-gopal</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_pic">
                <img src="images/plogo.png" alt="..." class="img-circle profile_img">
              </div>
              <div class="profile_info">
                <span>Welcome,</span><br/>
                <h2>Mr. <?php //echo $full_name?></h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

           <!--sidebar menu--> 
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>General</h3>
                <ul class="nav side-menu">
                  <li><a href="../../index.php"><i class="fa fa-home"></i>Home</a>                  
              </div>

            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="Settings">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Lock">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Logout">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div>
            <!-- /menu footer buttons -->
          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav hidden-xs">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>

              <ul class="nav navbar-nav navbar-right">
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <img src="images/plogo.png" alt="">Mr. <?=$full_name?>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
        
        <!-- /top navigation -->

        <!-- page content -->
     <div class="right_col" role="main">
      <form action="" method="post">
      <div class="title_right">      
       <div class="row">
       <div class="col-md-2 col-xs-2 col-sm-2">
       </div>
        <div class="col-md-6 col-xs-6 col-sm-6"   style=" top:2px;">
         
              <input id="reportrange" class="pull-right"  style="background: #fff; cursor: pointer; padding: 5px 10px;  border: 1px solid #ccc" name="date_range">
              </div> 
          <div class="col-md-4 col-xs-4 col-sm-4" style="top:2px;">
          <input type="submit" class="btn btn-primary" value="Filter"> 
          
</div>
        </div>
               
          <!-- top tiles -->
          <div class="row tile_count">
          <div class="row">
            <div class="col-sm-6 tile_stats_count" style="width:50%" align="center" >
              <span class="count_top"><i class="fa fa-user"></i> Total Sales Team </span>
               <div class="count green"><?=  $data->total_sales_team; ?></div> 
              </div>
            <div class="col-sm-6 tile_stats_count" style="width:50%" align="center">
              <span class="count_top"><i class="fa fa-user"></i> Total Attendance marked</span>
             <div class="count green"><?= $data->total_users_working; ?></div>
            </div>
          </div>  
          <div class="row">
            <div class="col-sm-6 tile_stats_count" style="width:50%" align="center">
              <span class="count_top"><i class="fa fa-user"></i> Frontliner Sales Team</span>
             <div class="count green"><?= ($data->total_sales_team_working); ?></div>
            </div>
  
             <div class="col-sm-6 tile_stats_count" style="width:50%" align="center">
              <span class="count_top"><i class="fa fa-user"></i> Daily Reporting Done By</span>
              <div class="count green"><?= $data->total_user_reporting; ?></div>
            </div>
          </div>
            <div class="col-sm-6 tile_stats_count" style="width:50%" align="center">
              <span class="count_top"><i class="fa fa-user"></i> Total Order Value</span>
              <div class="count green"><?= round($data->total_sale_value,2); ?></div>
             </div>
          
           <div class="col-sm-6 tile_stats_count" style="width:50%" align="center">
              <span class="count_top"><i class="fa fa-user"></i>Order Taken sales Team </span>
              <div class="count green"><?= $data->totaluser_sales_count; ?></div>            
            </div>           
          </div>
          <!-- /top tiles -->

          <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
              <div class="x_panel tile fixed_height_420">
                <div class="x_title">
                  <h2>Order Across State</h2>
                  <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                  </ul>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">  
                  <?php               
                  foreach ($data->city_wise_secondry_sale as $key => $value) {
                    if(!empty($value)) {
                        $parcentage = ($value / $data->total_sale_value) * 100;
                        $val = $value;
                    }else{
                        $parcentage=0;
                        $val = 0;
                    } 
                    
                    
                    
                    
                    ?>
                  <div class="widget_summary">
                    <div class="w_left w_25">
                        <?php if(empty($location_3)){ ?>
                      <font size="3"><?= $key; ?>( <?= round($parcentage,2); ?>% )</font>
                        <?php }else{ ?>
                      <font size="3">Zone: <?= $key; ?>( <?= round($parcentage,2); ?>% )</font>
                        <?php }?>
                    </div>
                    <div class="w_center w_55">
                      <div class="progress">
                        <div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?= $parcentage; ?>%;">
                          <span class="sr-only"><?= round($parcentage,2); ?>% Complete</span>
                        </div>
                      </div>
                    </div>
                    <div class="w_right w_20">
                      <font size="2"><b><?= $val; ?></b></font>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <?php } ?>
                </div>
              </div>
            </div>

            
            <div class="col-md-6 col-sm-6 col-xs-12">
              <div class="x_panel tile fixed_height_420">
                <div class="x_title">
                  <h2>Order Across Country wise</h2>
                  <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                  </ul>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">  
                  <?php
                  $location_1 = '';
                  if(empty($location_1)) {
                      arsort($data->state_wise_secondry_sale);
                  }else{
                    ksort($data->state_wise_secondry_sale);
                  }
                 
                  foreach ($data->state_wise_secondry_sale as $key => $value) {
                   // print_r($key);
                    if(!empty($value)) {
                        $parcentage = ($value / $data->total_sale_value) * 100;
                        $val = $value;
                    }else{
                        $parcentage=0;
                        $val = 0;
                    } ?>
                  <div class="widget_summary">
                    <div class="w_left w_25">
                        <?php if(empty($location_1)){ ?>
                      <font size="3"><?= $key; ?>( <?= round($parcentage,2); ?>% )</font>
                        <?php }else{ ?>
                      <font size="3">Zone: <?= $key; ?>( <?= round($parcentage,2); ?>% )</font>
                        <?php }?>
                    </div>
           
              <div class="w_center w_55">
                      <div class="progress">
                        <div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?= $parcentage; ?>%;">
                          <span class="sr-only"><?= round($parcentage,2); ?>% Complete</span>
                        </div>
                      </div>
                    </div> 

                    <div class="w_right w_20">
                      <font size="2"><b><?= $val; ?></b></font>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <?php } ?>
                </div>
              </div>
            </div>

                   <!--     NEW   DASHBOPARD      -->

             
              <div class="col-md-12 col-sm-12">
                  <div class="x_panel tile fixed_height_100">
                      <div class="x_title">
                          <h2>Order Classification<h align="justify"> P C </h> </h2>
                          <ul class="nav navbar-right panel_toolbox">
                              <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                              </li>
                          </ul>
                          <div class="clearfix"></div>
                      </div>
                      <div class="x_content">

                        <?php
                        foreach($data->classification_sale as $key2=>$value2){
                    
                        if(!empty($value2['total_sale'])) {
                            $parcentage3 = ($value2['total_sale'] / $data->total_sale_value) * 100;
                    
                        }else{ $parcentage3=0;} 
                        
                        if(isset($_POST['date_range'])) {
                            $dt = $_POST['date_range'];
                        }else{
                            $dt1 = date('m/d/Y');
                         // $dt1 = '10/03/2018';
                          $dt = $dt1.'-'.$dt1;
                        }
                        
                        $categories_url      = "sales_c1_details.php?user_id=$id&date=$dt&catalog_id=$value2[product_id]";
                        ?>

                              <div class="widget_summary">                           
                                  <div class="w_left w_25">
                                      <font size="2"><b><a href="<?= $categories_url; ?>"><?= $key2;?></a></b>(<?=round($parcentage3,2)?> %)</font>
                                  </div>
                                  <div class="w_center w_55">
                                      <div class="progress">
                                          <div class="progress progress" style="width: 100%;">
                                              <div class="progress-bar bg-green"  role="progressbar" data-transitiongoal="<?=$parcentage3?>"></div>
                                         
                                          </div>
                                      </div>
                                  </div>
<!--                                   <div class="w_right w_20">
                                  <font size="3"><?= $value2['piece_quantity']; ?></font>
                                  </div>-->
                                   <div class="w_right w_20">
                                   <font size="3"><?= $value2['case_quantity']; ?></font>
                                  </div>
                                  <div class="w_right w_20">
                                  <font size="4"><b><?= $value2['total_sale']; ?></b></font>
                                  </div>
                                  <div class="clearfix"></div>
                              </div>

                            <?php }
                           
                            ?>
                      </div>
                  </div>
              </div>

          </div>
<!-- ###################### -->

            <div class="col-md-12 col-sm-12" style=" display:none">
              <div class="x_panel tile fixed_height_320 overflow_hidden">
                <div class="x_title">
                  <h2>Order Across Division</h2>
                  <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                    
                  </ul>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <table class="" style="width:100%">
                    <tr>
                      <th style="width:37%;">
                        <p>Top 3 Division</p>
                      </th>
                      <th>
                        <div class="col-lg-7 col-md-7 col-sm-6 col-xs-6">
                          <p class="">Division</p>
                        </div>
                        
                      </th>
                    </tr>
                    <tr>
                      <td>
                      <canvas id="canvas1" height="190" width="190" style="margin: 15px 10px 10px 0"></canvas>
                     
                         </td>
                      <td>
                        <table class="tile_info">
                        <?php 
                        $color=array('A'=>'blue','B'=>'green','R'=>'red');
                        foreach($data->division_wise_sale as $key1=>$value1){
                        if(!empty($value1)) {
                            $parcentage1 = ($value1 / $data->total_sale_value) * 100;
                        } ?>
                          <tr>
                            <td>
                              <i class="fa fa-square <?= $color[$key1]?>"></i>
                            </td>
                            <td><font size="6" color="<?= $color[$key1]?>"><?=$key1?>:</font>&nbsp; </td>
                            <td><font size="5"><?=round($parcentage1,2); ?>%</font></td>
                          </tr>
                          <?php } ?>
                        </table>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div> 
  
              <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12 widget_tally_box">
                  <div class="x_panel">
                      <div class="x_title">
                          <h2>Attendance Across Day's</h2>
                          <ul class="nav navbar-right panel_toolbox">
                              <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                              </li>

                              <li><a class="close-link"><i class="fa fa-close"></i></a>
                              </li>
                          </ul>
                          <div class="clearfix"></div>
                      </div>
                      <div class="x_content">
                          <div id="graph_bar" style="width:100%; height:300px;"></div>
                      </div>
                  </div>
              </div>
            </div>
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            <!-- <b>  © 2017 Manacle Technologies Pvt. Ltd All rights reserved.</b> -->
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
    
    </body>  

    <!-- jQuery -->
    <script src="../vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="../vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="../vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="../vendors/nprogress/nprogress.js"></script>
    <!-- Chart.js -->
    <script src="../vendors/Chart.js/dist/Chart.min.js"></script>
    <!-- gauge.js -->
    <script src="../vendors/gauge.js/dist/gauge.min.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="../vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="../vendors/iCheck/icheck.min.js"></script>
    <!-- Skycons -->
    <script src="../vendors/raphael/raphael.min.js"></script>
    <script src="../vendors/morris.js/morris.min.js"></script>
    <script src="../vendors/skycons/skycons.js"></script>
    <!-- Flot -->
    <script src="../vendors/Flot/jquery.flot.js"></script>
    <script src="../vendors/Flot/jquery.flot.pie.js"></script>
    <script src="../vendors/Flot/jquery.flot.time.js"></script>
    <script src="../vendors/Flot/jquery.flot.stack.js"></script>
    <script src="../vendors/Flot/jquery.flot.resize.js"></script>
    <!-- Flot plugins -->
    <script src="../vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
    <script src="../vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
    <script src="../vendors/flot.curvedlines/curvedLines.js"></script>
    <!-- DateJS -->
    <script src="../vendors/DateJS/build/date.js"></script>
    <!-- JQVMap -->
    <script src="../vendors/jqvmap/dist/jquery.vmap.js"></script>
    <script src="../vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
    <script src="../vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="../vendors/moment/min/moment.min.js"></script>
    <script src="../vendors/bootstrap-daterangepicker/daterangepicker.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="../build/js/custom.min.js"></script>

    <script>
      $(document).ready(function(){
        var options = {
          legend: false,
          responsive: false
        };
        var data_array=new Array();
        var lavel_array=new Array();
        <?php
       foreach($data->division_wise_sale as $key1=>$value1){
        ?>
        lavel_array.push('<?php echo $key1; ?>');
        data_array.push('<?php echo $value1; ?>');
        <?php }?>
        new Chart(document.getElementById("canvas1"), {
          type: 'doughnut',
          tooltipFillColor: "rgba(51, 51, 51, 0.55)",
          data: {
            labels: lavel_array,
            datasets: [{
              data: data_array,
              backgroundColor: [
                "blue",
                "green",
                "red"
              ],
              hoverBackgroundColor: [
                "blue",
                "green",
                "red"
              ]
            }]
          },
          options: options
        });
      });
    </script>
         <!--        //*******************Attendance Graph*****************-->
        <?php
        foreach($data->attendance as $k1=>$val1){
            $data1[]=array('period'=>$k1,'Marked'=>$val1);
        }
        $attend_data = json_encode($data1);?>
        <script>
            $(document).ready(function() {
                Morris.Bar({
                    element: 'graph_bar',
                    data:<?=$attend_data?>,
                    xkey: 'period',
                    hideHover: 'auto',
                    barColors: ['#26B99A', '#34495E', '#ACADAC', '#3498DB'],
                    ykeys: ['Marked', 'sorned'],
                    labels: ['Marked', ''],
                    xLabelAngle: 60,
                    resize: true
                });
                $MENU_TOGGLE.on('click', function() {
                    $(window).resize();
                });
            });
        </script>
        
        
        <script>
      $(document).ready(function() {
        var cb = function(start, end, label) {
          console.log(start.toISOString(), end.toISOString(), label);
          $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        };
          <?php if(isset($_POST['date_range'])) {
             $date_range=  explode('-', $_POST['date_range']);
             $from_range = date("m-d-Y", strtotime($date_range[0]));
             $to_range = date("m-d-Y", strtotime($date_range[1]));
            
             ?>  
            var from_date='<?=$from_range?>';
            var to_date='<?=$to_range?>'; 
            //alert(to_date);
          <?php }else{ ?>
              var from_date=moment().format('MM/DD/YYYY');
              var to_date=moment().format('MM/DD/YYYY');

               //alert('llol');
         <?php }?>
        var optionSet1 = {
          startDate: from_date,
          endDate: to_date,
//          minDate: '01/01/2012',
//          maxDate: '12/31/2020',
          dateLimit: {
            days: 30
          },
          showDropdowns: true,
          showWeekNumbers: true,
          timePicker: false,
          timePickerIncrement: 1,
          timePicker12Hour: true,
          ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          },
          opens: 'left',
          buttonClasses: ['btn btn-default'],
          applyClass: 'btn-small btn-primary',
          cancelClass: 'btn-small',
          format: 'DD/MM/YYYY',
          separator: ' to ',
          locale: {
            applyLabel: 'Submit',
            cancelLabel: 'Clear',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            firstDay: 1
          }
        };
        $('#reportrange span').html(moment().subtract(29, 'days').format('DD/MM/YYYY') + ' - ' + moment().format('DD/MM/YYYY'));
        $('#reportrange').daterangepicker(optionSet1, cb);
        $('#reportrange').on('show.daterangepicker', function() {
          console.log("show event fired");
        });
        $('#reportrange').on('hide.daterangepicker', function() {
          console.log("hide event fired");
        });
        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
          console.log("apply event fired, start/end dates are " + picker.startDate.format('DD/MM/YYYY') + " to " + picker.endDate.format('DD/MM/YYYY'));
        });
        $('#reportrange').on('cancel.daterangepicker', function(ev, picker) {
          console.log("cancel event fired");
        });
        $('#options1').click(function() {
          $('#reportrange').data('daterangepicker').setOptions(optionSet1, cb);
        });
        $('#options2').click(function() {
          $('#reportrange').data('daterangepicker').setOptions(optionSet2, cb);
        });
        $('#destroy').click(function() {
          $('#reportrange').data('daterangepicker').remove();
        });
      });
    </script>


</html>
