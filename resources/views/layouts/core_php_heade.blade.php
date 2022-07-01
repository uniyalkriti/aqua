    <?php error_reporting(0);
      $above_url =  'http://'.$_SERVER[HTTP_HOST].'/client'; 
      // die;

     ?>
    <script src="{{asset('msell/js/jquery-2.1.4.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
    <html lang="en">
    <head>

      <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
      <meta charset="utf-8" />
      <title>DISTRIBUTOR PANEL</title>

      <meta name="description" content="top menu &amp; navigation" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

      <!-- bootstrap & fontawesome -->
      <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />
      <link rel="stylesheet" href="{{asset('assets/font-awesome/4.5.0/css/font-awesome.min.css')}}" />

      <!-- page specific plugin styles -->

      <!-- text fonts -->
      <link rel="stylesheet" href="{{asset('assets/css/fonts.googleapis.com.css')}}" />

      <!-- ace styles -->
      <link rel="stylesheet" href="{{asset('assets/css/ace.min.css')}}" class="ace-main-stylesheet" id="main-ace-style" />

        <!--[if lte IE 9]>
                <link rel="stylesheet" href="assets/css/ace-part2.min.css" class="ace-main-stylesheet" />
              <![endif]-->
              <link rel="stylesheet" href="{{asset('assets/css/ace-skins.min.css')}}" />
              <link rel="stylesheet" href="{{asset('assets/css/ace-rtl.min.css')}}" />

        <!--[if lte IE 9]>
          <link rel="stylesheet" href="assets/css/ace-ie.min.css" />
        <![endif]-->

        <!-- inline styles related to this page -->

        <!-- ace settings handler -->
        <script src="{{asset('assets/js/ace-extra.min.js')}}"></script>

        <!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

        <!--[if lte IE 8]>
        <script src="assets/js/html5shiv.min.js"></script>
        <script src="assets/js/respond.min.js"></script>
      <![endif]-->
    </head>

    <body class="no-skin" >
      <div id="navbar" class="navbar navbar-default    navbar-collapse       h-navbar ace-save-state" style="height: 0px; background-color: #90d781; color:black;" > 
        <div class="navbar-container ace-save-state" id="navbar-container">
          <div class="navbar-header pull-left" >

            <?php
            // dd($_SESSION);
      // pre($_SESSION);
            $role_id = !empty($_SESSION['iclientdigimetdata']['urole'])?$_SESSION['iclientdigimetdata']['urole']:'0';;
            ?>


            <!-- <a href="<?= $above_url.'/index.php'?>" class="navbar-brand" style="color:black; font-weight: bolder;"> -->
            <a href="#" class="navbar-brand" style="color:black; font-weight: bolder;">
              <small>
                <?php
                    session_start();
                    // Sess
                    // dd($_SESSION);   
                ?>
                <!--                            <i class="fa fa-leaf"></i>
                <img src="./images/baidyanath.gif" style="width:28px; height: 26px"/>-->
                DISTRIBUTOR: <?php echo $_SESSION['iclientdigimetdata']['dealer_name']; ?>
              </small> 
            </a>

            <button class="pull-right navbar-toggle navbar-toggle-img collapsed" type="button" data-toggle="collapse" data-target=".navbar-buttons,.navbar-menu">
              <span class="sr-only">Toggle user menu</span> -->

              <!-- <img src="assets/images/avatars/user.jpg" alt="Jason's Photo" /> -->
            </button>
                    <!-- <button class="pull-right navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#sidebar">
                        <span class="sr-only">DMS</span>

                        <span class="icon-bar"></span>

                        <span class="icon-bar"></span>

                        <span class="icon-bar"></span>
                      </button> -->
                    </div>
                    <!-- ANKUSH -->

                    <div class="navbar-buttons navbar-header pull-right  collapse navbar-collapse" role="navigation">
                      <ul class="nav ace-nav">
                        <li>

                         <a href="<?=$above_url.'/index.php?option=logout'?>">
                          <i class="ace-icon fa fa-power-off"></i>
                          Logout
                        </a>
                      </li>
                    </ul>
                  </div>

                  <!-- <div class="pull-right" role="navigation" style="color: black;margin-right: 30px;margin-top: 5px;">
                    <span>
                      <a style="color: black;" href="mailto:support@manacleindia.com">Email ID :- support@manacleindia.com</a>
                      <br/>                                  
                      Contact No. :- +91- 8010819820, <i class="fa fa-whatsapp" aria-hidden="true" style="color: #86ff86;font-size: 17px;"></i>
                      - +91- 9810299704
                    </span>
                  </div> -->

                  <nav role="navigation" class="navbar-menu pull-left collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                     <?php 
                     ini_set('max_execution_time','-1');
                     ini_set('memory_limit','-1');
                     $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];

                     $i = 0;
                     $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
                     $query = "SELECT sum(qty) as remain,product_id,rate FROM `stock` WHERE `dealer_id`='$dealer_id' group BY product_id";
    //h1($query);
                     $rr = mysqli_query($dbc,$query);
                     while($row = mysqli_fetch_assoc($rr))
                     {
                     // pre($row);
                       $product_id = $row['product_id'];
                       $remain = $row['remain'];
                       $qs = "SELECT `qty` FROM `threshold` where product_id='$product_id' AND dealer_id = '$dealer_id'";
      //h1($qs);
                       $rss = mysqli_query($dbc,$qs);
                       $rows = mysqli_fetch_assoc($rss);
                       $qtyy = $rows['qty'];
                       if($remain<=$qtyy)
                       {
                         $i = $i+1;
                       }
                     }

                     
                      // session_start();
                      $dealer_id = !empty($_SESSION['iclientdigimetdata']['dealer_id'])?$_SESSION['iclientdigimetdata']['dealer_id']:'0';
                      $role_id = !empty($_SESSION['iclientdigimetdata']['urole'])?$_SESSION['iclientdigimetdata']['urole']:'0';
?>
                    @if($role_id == '37')
                      @else
                      <li class="transparent dropdown-modal">
                        <a title="LOW BALANCE STOCK OF (<?=$i?>) PRODUCTS" class="iframef" href="<?= $above_url.'/index.php?option=demand-order&showmode=1&mode=1'?>" style="color: black;">
                          <i class="ace-icon fa fa-bell icon-animated-bell"></i>
                          Low Stock
                          <span class="badge badge-warning"><?=$i?></span>
                        </a>

                      </li>
                    @endif
                     
                    <li class="transparent dropdown-modal">
                      <a title="Updtae Profile"  href="<?= $above_url.'/index.php?option=update-profile'?>" style="color: black;">
                        <i class="ace-icon fa fa-user"></i>
                        Update Profile
                        <span class="badge badge-warning"></span>
                      </a>

                    </li>
                  </ul>

                </nav>
              </div><!-- /.navbar-container -->
            </div>

            <div class="main-container ace-save-state" id="main-container">
              <script type="text/javascript">
                try {
                  ace.settings.loadState('main-container')
                } catch (e) {
                }
              </script>

              <div id="sidebar" class="sidebar      h-sidebar                navbar-collapse collapse          ace-save-state">
                <script type="text/javascript">
                  try {
                    ace.settings.loadState('sidebar')} catch (e) {
                    }
                  </script>

                  <div class="sidebar-shortcuts" id="sidebar-shortcuts">
                   <!-- <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large" style="width:60px;">
                        

<!--                <a href="index.php?option=bar_pie">   
                      <button class="btn btn-info">
                            <i class="ace-icon fa fa-area-chart"></i>
                      </button>
                </a>
                 <a href="index.php?option=bar_pie">   
                      <button class="btn btn-warning">
                            <i class="ace-icon fa fa-area-chart"></i>
                      </button>
                    </a>        -->


<!--                        <button class="btn btn-danger">
                            <i class="ace-icon fa fa-cogs"></i>
                          </button>-->
                          <!--</div>-->

                          <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">

                      <!--  <span class="btn btn-success"></span>

                        <span class="btn btn-info"></span>

                        <span class="btn btn-warning"></span>

                        <span class="btn btn-danger"></span>-->
<!--<a href="../dashboard/production/" title="INSIGHT">   
                      <button class="btn btn-success">
                            <i class="ace-icon fa fa-signal"></i>
                      </button>
                    </a>-->
                  </div>
                </div><!-- /.sidebar-shortcuts -->
                @if($role_id == 1)
                <ul class="nav nav-list">
                  <li class="hover">
                      <a href="{{url('web/dms_dealer')}}" >
                          <i class="menu-icon fa fa-user"></i>
                           Distributor
                      </a>

                      <b class="arrow"></b>
                  </li>
                  <li class="hover">
                      <a href="{{url('web/Scheme-details/create')}}" >
                          <i class="menu-icon fa fa-user"></i>
                           Scheme
                      </a>

                      <b class="arrow"></b>
                  </li>
                  <li class="hover">
                      <a href="{{url('web/Scheme-details')}}" >
                          <i class="menu-icon fa fa-user"></i>
                           Scheme Details
                      </a>

                      <b class="arrow"></b>
                  </li>
                  <li class="hover">
                      <a href="{{url('web/admin_scheme_details')}}" >
                          <i class="menu-icon fa fa-user"></i>
                           Scheme Details Distributor Wise
                      </a>

                      <b class="arrow"></b>
                  </li>
                  <li class="hover">
                      <a href="{{url('Order-details')}}" >
                          <i class="menu-icon fa fa-user"></i>
                           Order Details
                      </a>

                      <b class="arrow"></b>
                  </li>
                  <li class="hover">
                      <a href="{{url('dms-payment-details')}}" >
                          <i class="menu-icon fa fa-user"></i>
                           D Payment details
                      </a>

                      <b class="arrow"></b>
                  </li>
                  <li class="hover">
                      <a href="{{url('short_item_list_report')}}" >
                          <i class="menu-icon fa fa-user"></i>
                           Order Life Cycle Report
                      </a>

                      <b class="arrow"></b>
                  </li>
                  <li class=" hover">
                      <a href="{{url('credit_debit_notes')}}" >
                          <i class="menu-icon fa fa-caret-right"></i>
                          Credit Debit Notes
                      </a>
                      <b class="arrow"></b>
                  </li>

                  <li class="hover">
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon fa fa-file"></i>
                     Reports
                    <b class="arrow fa fa-angle-right"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">

                     
                     <li class="hover">
                        <a href="{{url('saleStatement')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                           Sale Statement Mainline
                        </a>
                        <b class="arrow"></b>
                     </li>

                      <li class="hover">
                        <a href="{{url('saleStatementEthical')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                           Sale Statement Ethical
                        </a>
                        <b class="arrow"></b>
                     </li>
                </ul>
            </li>
            
                </ul>
                @endif
                @if($role_id == 41)
                <ul class="nav nav-list">
                  <li class="hover">
                      <a href="{{url('report_welcome')}}" >
                          <i class="menu-icon fa fa-user"></i>
                           Report
                      </a>

                      <b class="arrow"></b>
                  </li>
                  <li class="hover">
                      <a href="{{url('short_item_list_report')}}" >
                          <i class="menu-icon fa fa-user"></i>
                           Order Life Cycle Report
                      </a>

                      <b class="arrow"></b>
                  </li>

                  <li class="hover">
                      <a href="{{url('admin_scheme_details')}}" >
                          <i class="menu-icon fa fa-user"></i>
                           Scheme Details Distributor Wise
                      </a>

                      <b class="arrow"></b>
                  </li>

                  <li class="hover">
                      <a href="{{url('Order-details')}}" >
                          <i class="menu-icon fa fa-user"></i>
                           Order Details
                      </a>

                      <b class="arrow"></b>
                  </li>

                  <li class="hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-file"></i>
                         Reports
                        <b class="arrow fa fa-angle-right"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">

                         
                         <li class="hover">
                            <a href="{{url('saleStatement')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               Sale Statement Mainline
                            </a>
                            <b class="arrow"></b>
                         </li>

                          <li class="hover">
                            <a href="{{url('saleStatementEthical')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               Sale Statement Ethical
                            </a>
                            <b class="arrow"></b>
                         </li>
                    </ul>
                </li>

                  
                </ul>
                @endif
                @if(($role_id != 37 && $role_id != 38 && $role_id != 1 && $role_id != 41 && $role_id !=  '823') || $dealer_id == '1170')

                <?php $menu = $_GET['option']; ?>
                <?php
                $index = '/index.php';
                $url = base64_encode($index);
                ?>
                <ul class="nav nav-list">
                 <?php if($menu=='')
                 echo'<li class=" open hover">';    
                 else
                   echo'<li class="open hover">'; ?> 
                    <a href="<?=$above_url.'/'.$index?>" id="myhome">
                      <i class="menu-icon fa fa-tachometer"></i>
                      <span class="menu-text">Home</span>
                    </a>
                    <b class="arrow"></b>
                  </li>

                  <?php if($menu=='catalog-product' || $menu=='add-user' || $menu=='retailer-add')
                  echo'<li class="active open hover">';    
                  else
                   echo'<li class="open hover">'; ?>  
                    <a href="#" class="dropdown-toggle">
                      <i class="menu-icon fa fa-desktop"></i>
                      <span class="menu-text">
                        Master
                      </span>

                      <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                      <li class="hover">
                        <a href="<?=$above_url.'/index.php?option=add-van'?>">
                          <i class="menu-icon fa fa-caret-right"></i>
                          Supply Vehical
                        </a>
                        <b class="arrow"></b>
                      </li>
                      <li class="hover">
                        <a href="<?=$above_url.'/index.php?option=add-threshold'?>">
                          <i class="menu-icon fa fa-caret-right"></i>
                          Stock Threshold
                        </a>

                        <b class="arrow"></b>
                      </li>
                      <li class="hover">
                        <a href="<?=$above_url.'/index.php?option=catalog-product'?>">
                          <i class="menu-icon fa fa-caret-right"></i>
                          Item List
                        </a>

                        <b class="arrow"></b>
                      </li>

                      <li class="hover">
                        <a href="<?=$above_url.'/index.php?option=add-user'?>">
                          <i class="menu-icon fa fa-caret-right"></i>
                          User
                        </a>

                        <b class="arrow"></b>
                      </li>

                      <li class="hover">
                        <a href="<?=$above_url.'/index.php?option=retailer-add'?>">
                          <i class="menu-icon fa fa-caret-right"></i>
                          Retailer
                        </a>

                        <b class="arrow"></b>
                      </li> 

                      <li class="hover">
                        <a href="<?=$above_url.'/index.php?option=location-category&mtype=5'?>">
                          <i class="menu-icon fa fa-caret-right"></i>
                          Beat
                        </a>
                        <b class="arrow"></b>
                      </li>

                      <li class="hover">
                        <a href="<?=$above_url.'/index.php?option=company'?>">
                          <i class="menu-icon fa fa-caret-right"></i>
                          Brands
                        </a>
                        <b class="arrow"></b>
                      </li>

                      <li class="hover">
                        <a href="<?=$above_url.'/index.php?option=brand-product'?>">
                          <i class="menu-icon fa fa-caret-right"></i>
                         Other Brand Product Master
                        </a>
                        <b class="arrow"></b>
                      </li>
                      <li class="open hover">
                        <a href="<?= $above_url.'/index.php?option=retailer-claim'?>">
                           <i class="menu-icon fa fa-gift"></i>
                                   
                            <span class="menu-text">  Retailer Claim </span>
                        </a>
                        <b class="arrow"></b>
                    </li> 

<!--                            <li class="hover">
                                <a href="/index.php?option=dispatch-beat">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                   Dispatch Route
                                </a>

                                <b class="arrow"></b>
                              </li>-->

                              <!--<li class="open hover">-->
<!--                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Users
                                    <b class="arrow fa fa-angle-down"></b>
                                  </a>-->

                                  <b class="arrow"></b>

                                  <ul class="submenu">


                                    <li class="hover">
                                      <a href="<?=$above_url.'/index.php?option=add-user'?>">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        Field Staff
                                      </a>

                                      <b class="arrow"></b>
                                    </li>

                                    <li class="hover">
                                      <a href="<?= $above_url.'/index.php?option=retailer-add'?>">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        Retailer
                                      </a>

                                      <b class="arrow"></b>
                                    </li> 
                                    
                                    <li class="hover">
                                      <a href="<?= $above_url.'/index.php?option=location-category&mtype=5'?>">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        Beat
                                      </a>

                                      <b class="arrow"></b>
                                    </li>

                                    <li class="hover">
                                      <a href="<?= $above_url.'/index.php?option=dispatch-beat'?>">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        Dispatch Route
                                      </a>

                                      <b class="arrow"></b>
                                    </li>
                                  </ul>
                                </li> 
<!--                            <li class="hover">
                                <a href="index.php?option=location-category&mtype=5">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Beat
                                </a>

                                <b class="arrow"></b>
                              </li>-->
                            </ul>
                          </li>

                         <?php 
                         // die();
                         ?>
            <!--    <?php if($menu=='import_invoice')
                          echo'<li class="active open hover">';    
                          else
                           echo'<li class="open hover">'; ?> 
                            <a href="index.php?option=import_invoice">
                              <i class="menu-icon fa fa-cart-arrow-down"></i>
                              <span class="menu-text"> Purchase Order </span>
                            </a>
                            <b class="arrow"></b>
                          </li>-->
              <?php if($menu=='balance-stock'||$menu=='purchase-order'||$menu=='import_invoice')
                      echo'<li class="active open hover">';    
                      else
                       echo'<li class="open hover">'; ?> 
                        <a href="#" class="dropdown-toggle">
                          <i class="menu-icon fa fa-database"></i>
                          <span class="menu-text">
                            Stock
                          </span>

                          <b class="arrow fa fa-angle-down"></b>
                        </a>

                        <b class="arrow"></b>

                        <ul class="submenu">
                          <li class="hover">
                            <a href="<?= $above_url.'/index.php?option=balance-stock'?>">
                              <i class="menu-icon fa fa-caret-right"></i>
                              Current Stock
                            </a>
                            <b class="arrow"></b>
                          </li>
                                  <li class="active open hover">
                                    <a href="#" class="dropdown-toggle">
                                      <i class="menu-icon fa fa-caret-right"></i>
                                      Opening Stock
                                      <b class="arrow fa fa-angle-right"></b>
                                    </a>

                                    <b class="arrow"></b>

                                    <ul class="submenu"> 
                            <li class="hover">
                            <a href="<?= $above_url.'/index.php?option=opening_stock'?>">
                              <i class="menu-icon fa fa-caret-right"></i>
                              Opening Stock
                            </a>
                            <b class="arrow"></b>
                          </li>
                           <li class="hover">
                        <a href="<?= $above_url.'/index.php?option=import_opening_stock'?>">
                              <i class="menu-icon fa fa-caret-right"></i>
                             Import Opening Stock
                            </a>
                            <b class="arrow"></b>
                          </li>
                                  </ul>
                                </li>

                          <li class="hover">
                            <a href="<?= $above_url.'/index.php?option=purchase-order'?>">
                              <i class="menu-icon fa fa-caret-right"></i>
                              Recieve Stock
                            </a>
                            <b class="arrow"></b>
                          </li> 
  <!--            <li class="hover">
                            <a href="index.php?option=import_invoice">
                              <i class="menu-icon fa fa-caret-right"></i>
                              Import Invoice
                            </a>
                            <b class="arrow"></b>
                          </li>  -->
                        </ul>
                      </li> 


<!--                    <?php if($menu=='primary-sale-details'||$menu=='Opening-stock')
                         echo'<li class="active open hover">';    
                          else
                         echo'<li class="open hover">'; ?> 
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-database"></i>
                            <span class="menu-text">
                             &nbsp;&nbsp;&nbsp;  Stock &nbsp;&nbsp;&nbsp;
                            </span>

                            <b class="arrow fa fa-angle-down"></b>
                        </a>

                        <b class="arrow"></b>

                        <ul class="submenu">
                            <li class="hover">
                                    <a href="index.php?option=opening-stock">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                   Opening Stock
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="hover">
                                    <a href="index.php?option=primary-sale-details">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Primary Stock
                                </a>
                                <b class="arrow"></b>
                            </li>                                            
                        </ul>
                      </li> -->

                 <!--     <?php if($menu=='balance-stock')
                      echo'<li class="active open hover">';    
                      else
                       echo'<li class="open hover">'; ?> 
                        <a href="index.php?option=balance-stock">
                          <i class="menu-icon fa fa-database"></i>
                          <span class="menu-text"> Stock </span>
                        </a>
                        <b class="arrow"></b>
                      </li> -->


                  <!---------------------------------------------------------RETAILER CLAIM------------------------------->

    <!-----------------------------
                      <?php if($menu=='claim-order')
                         echo'<li class="active open hover">';    
                          else
                         echo'<li class="open hover">'; ?> 
                    
                     <a href="#" class="dropdown-toggle">
                           <i class="menu-icon fa fa-gift"></i>
                            <span class="menu-text">
                                Claim 
                            </span>

                            <b class="arrow fa fa-angle-down"></b>
                        </a>

                        <b class="arrow"></b>
                    <ul class="submenu">
                        <li class="hover">
                                <a href="index.php?option=retailer-claim">
                                    <i class="menu-icon fa fa-gift"></i>
                                    Retailer Claim
                                </a>
                                <b class="arrow"></b>
                            </li>
                        

                    </ul>    
                  </li>------------------------------>
                  <?php if($menu=='direct-challan'||$menu=='make-challan'||$menu=='Invoice-Summary'||$menu=='sale-order-detailes')
                  echo'<li class="active open hover">';    
                  else
                   echo'<li class="open hover">'; ?> 
                    <a href="#" class="dropdown-toggle">
                      <i class="menu-icon fa fa-list-alt"></i>
                      <span class="menu-text">
                        Invoice/Billing
                      </span>

                      <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                      <li class="hover">
                        <a href="<?= $above_url.'/index.php?option=direct-challan'?>">
                          <i class="menu-icon fa fa-caret-right"></i>
                          Generate Invoice
                        </a>
                        <b class="arrow"></b>
                      </li>
            <li class="hover">
                        <a href="<?= $above_url.'/index.php?option=direct-challan-retail'?>">
                          <i class="menu-icon fa fa-caret-right"></i>
                          Generate Retail Invoice
                        </a>
                        <b class="arrow"></b>
                      </li>
                      <li class="hover">
                        <a href="<?= $above_url.'/index.php?option=sale-order-detailes'?>">
                          <i class="menu-icon fa fa-caret-right"></i>
                          Invoice Through SFA
                        </a>
                        <b class="arrow"></b>
                      </li>
                      <li class="hover">
                        <a href="<?= $above_url.'/index.php?option=make-challan'?>">
                          <i class="menu-icon fa fa-caret-right"></i>
                          Invoice Details
                        </a>
                        <b class="arrow"></b>
                      </li>
                      <li class="hover">
                        <a href="<?= $above_url.'/index.php?option=return-challan'?>">
                          <i class="menu-icon fa fa-caret-right"></i>
                          Edit Invoice
                        </a>
                        <b class="arrow"></b>
                      </li>
                      <li class="hover">
                        <a href="<?= $above_url.'/index.php?option=Invoice-Summary'?>">
                          <i class="menu-icon fa fa-caret-right"></i>
                          Invoice Summary
                        </a>
                        <b class="arrow"></b>
                      </li>
                    </ul>
                  </li>  


                  <!--- DAILY DISPATCH -->
                  <?php if($menu=='daily-dispatch-details'||$menu=='daily-dispatch-report')
                  echo'<li class="active open hover">';    
                  else
                   echo'<li class="open hover">'; ?> 
                    <a href="<?= $above_url.'/index.php?option=daily-dispatch-details'?>">
                      <i class="menu-icon fa fa-share-square-o"></i>
                      <span class="menu-text">
                        Daily Dispatch
                      </span>

                      <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

<!--                         <ul class="submenu">
                            <li class="hover">
                               <a href="index.php?option=daily-dispatch-details">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Dispatch 
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="hover">
                                <a href="index.php?option=daily-dispatch-report">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Dispatch Details
                                </a>
                                <b class="arrow"></b>
                            </li>                                            
                          </ul> -->
                        </li>

                    <!--    <?php //if($menu=='payment-collection')
                  if($menu=='payment')
                       echo'<li class="active open hover">';    
                     else
                       echo'<li class="open hover">'; ?> 
                        <a href="index.php?option=payment">
                          <i class="menu-icon fa fa-inr"></i>
                          <span class="menu-text"> Payment Collection </span>
                        </a>
                        <b class="arrow"></b>
                      </li> -->
            <?php if($menu=='payment'||$menu=='old_payment')
                      echo'<li class="active open hover">';    
                      else
                       echo'<li class="open hover">'; ?> 
                        <a href="#" class="dropdown-toggle">
                          <i class="menu-icon fa fa-share-square-o"></i>
                          <span class="menu-text">
                            Other Modules
                          </span>

                          <b class="arrow fa fa-angle-down"></b>
                        </a>

                        <b class="arrow"></b>

                        <ul class="submenu">
                          <li class="hover">
                            <a href="<?= $above_url.'/index.php?option=old_payment'?>">
                              <i class="menu-icon fa fa-caret-right"></i>
                              Old Dues
                            </a>
                            <b class="arrow"></b>
                          </li>
                          <li class="hover">
                            <a href="<?= $above_url.'/index.php?option=payment-new'?>">
                              <i class="menu-icon fa fa-caret-right"></i>
                              Payment Collection
                            </a>
                            <b class="arrow"></b>
                          </li> 
                          <li class="hover">
                            <a href="<?= $above_url.'/index.php?option=direct-challan-damage'?>">
                              <i class="menu-icon fa fa-caret-right"></i>
                            Retailer  Damage/Replace Details
                            </a>
                            <b class="arrow"></b>
                          </li>
                          <!-- new menu added -->
                          <li class="hover">
                            <a href="<?= $above_url.'/index.php?option=direct-challan-damage-depot'?>">
                              <i class="menu-icon fa fa-caret-right"></i>
                            Depot  Damage/Replace Details
                            </a>
                            <b class="arrow"></b>
                          </li>
                          <li class="hover">
                            <a href="<?= $above_url.'/index.php?option=make-challan-damage'?>">
                              <i class="menu-icon fa fa-caret-right"></i>
                            Retailer  Damage/Replace List
                            </a>
                            <b class="arrow"></b>
                          </li> 
                          <li class="hover">
                            <a href="<?= $above_url.'/index.php?option=make-challan-damage-depot'?>">
                              <i class="menu-icon fa fa-caret-right"></i>
                           Depot  Damage/Replace List
                            </a>
                            <b class="arrow"></b>
                          </li>   
                       <!--    <li class="hover">
                            <a href="index.php?option=payment">
                              <i class="menu-icon fa fa-caret-right"></i>
                              Payment Collection
                            </a>
                            <b class="arrow"></b>
                          </li> --> 
                        </ul>
                      </li> 

                    


                      <?php if($menu=='primary-sale-details'||$menu=='intransit-dispatch-report' ||$menu=='stock-age-report' ||$menu=='product_wise_summary_report' ||$menu=='bill_summary_report' ||$menu=='retailerwise_bill_summary_report' ||$menu=='sku-wise-report'||$menu=='intransit-dispatch-report'||$menu=='sale-report'||$menu=='aging-report'||$menu=='user-aging-report'||$menu=='challan-report-details'||$menu=='tax_inv_sale_report'||$menu=='tax_register_report'||$menu=='daily-dispatch-report' || $menu=='payment-collection-report'||$menu=='catalog_1&mtype=1'||$menu=='catalog_2&mtype=2' ||$menu=='catalog-rate-list' || $menu=='focus-product' ||$menu=='user-sale-performance' || $menu=='retailer-sale-performance'||$menu=='productwise-dispatch-report' ||$menu=='datewise-dispatch-report' || $menu=='user-bill-performance' || $menu=='division_summary_report' || $menu=='retailer-dues' || $menu=='stock-ledger' || $menu=='stock_summary' || $menu=='stock-ledger-tranc' || $menu=='retailer-ledger-tranc' ||$menu=='bill-purchase-performance' ||$menu=='gst_wise_summary' ||$menu=='retailer_payment_collection_report')
                      echo'<li class="active open hover">';    
                      else
                       echo'<li class="open hover">'; ?> 
                         <a href="#" class="dropdown-toggle">
                          <i class="menu-icon fa fa-desktop"></i>
                          <span class="menu-text">
                            Reports
                          </span>

                          <b class="arrow fa fa-angle-down"></b>
                        </a>

                        <b class="arrow"></b>

                        <ul class="submenu">

                         <li class="active open hover">
                          <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Sale Reports
                            <b class="arrow fa fa-angle-right"></b>
                          </a>

                          <b class="arrow"></b>

                          <ul class="submenu">
                          <li class="active open hover">
                          <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-caret-right"></i>
                            MKT Reports
                            <b class="arrow fa fa-angle-right"></b>
                          </a>

                          <b class="arrow"></b>
                          <ul class="submenu">
                          <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=dealer-sale-performance'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          MKT Wise Sale Vs Purchase
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=retailer-wise-sale-performance'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          MKT Wise Retailer Sale
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=product-wise-sale-performance'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Item Wise Retailer Sale
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=order-sale-performance'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          MKT Wise Sale Vs Order
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=retailer-wise-sale-order-performance'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          MKT Wise Retailer Sale Vs Order
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                             </ul>
                             </li>         

                                   <!-- <li class="hover">
                                        <a href="index.php?option=retailer-invoice">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Retailer Invoice
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                     <li class="hover">
                                        <a href="index.php?option=retailer-wise-ledger">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Retailer Wise Ledger
                                        </a>
                                        <b class="arrow"></b>
                                      </li> -->
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=challan-report-details'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Challan Vs Sale
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=bill-purchase-performance'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Monthly Challan Vs Purchase
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=tax_inv_sale_report'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Tax Invoice Sale Report
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <!-- <li class="hover">
                                        <a href="index.php?option=gst_return_summary">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          GST Return Summary
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                        <a href="index.php?option=purchase_register">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          CGST/SGST Purchase Register
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                        <a href="index.php?option=GSTR_report">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          GSTR 3B Report
                                          </a>
                                        <b class="arrow"></b>
                                      </li> -->
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=sale-report'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          DSR Salesman wise Report
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                    <!--   <li class="hover">
                                        <a href="index.php?option=sku-wise-report">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          SKU Wise Report
                                        </a>
                                        <b class="arrow"></b>
                                      </li> 
                                      <li class="hover">
                                        <a href="index.php?option=user-sale-performance">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Users Sale Performance
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                        <a href="index.php?option=user-bill-performance">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Users Bill Performance
                                        </a>
                                        <b class="arrow"></b>
                                      </li>-->
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=division_summary_report'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Division Wise Sale Report
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=retailer-sale-performance'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Retailers Sale Performance
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <!-- <li class="hover">
                                        <a href="index.php?option=retailer-ledger">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Retailers Ledger
                                        </a>
                                        <b class="arrow"></b>
                                      </li> -->
                                     
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=division-wise'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Division Wise Invoice Details
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      
                                    </ul>
                                  </li>

                    <li class="active open hover">
                          <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-caret-right"></i>
                            GST Reports
                            <b class="arrow fa fa-angle-right"></b>
                          </a>

                          <b class="arrow"></b>

                          <ul class="submenu">

                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=gst_return_summary'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          GSTR1 Report
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=purchase_register'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          GSTR2 Report
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=GSTR_report'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          GSTR 3B Report
                                          </a>
                                        <b class="arrow"></b>
                                </li> 
                                </ul> 
                                 </li>


                                  <li class="active open hover">
                                    <a href="#" class="dropdown-toggle">
                                      <i class="menu-icon fa fa-caret-right"></i>
                                      Stock Reports
                                      <b class="arrow fa fa-angle-right"></b>
                                    </a>

                                    <b class="arrow"></b>

                                    <ul class="submenu">

                                      <!-- <li class="hover">
                                        <a href="index.php?option=primary-sale-details">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Stock Received
                                        </a>
                                        <b class="arrow"></b>
                                      </li> -->
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=stock_summary'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Stock Summary
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <!--<li class="hover">
                                        <a href="index.php?option=stock-age-report">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Stock Age
                                        </a>
                                        <b class="arrow"></b>
                                      </li>-->
                                    <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=current-stock'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Current Stock
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=stock-details'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Stock Details
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=stock-ledger-tranc'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Stock Ledger
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                  </ul>
                                </li>
                                <li class="active open hover">
                                    <a href="#" class="dropdown-toggle">
                                      <i class="menu-icon fa fa-caret-right"></i>
                                      Invoice Reports
                                      <b class="arrow fa fa-angle-right"></b>
                                    </a>

                                    <b class="arrow"></b>

                                    <ul class="submenu">
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=stock-ledger'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Product Wise Invoice List
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=product_wise_summary_report'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Product Wise Summary Report
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=gst_wise_summary'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          GST Wise Invoice List
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=bill_summary_report'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Bill Wise Summary Report
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=retailerwise_bill_summary_report'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Retailer Wise Bill Summary Report
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                        <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=retailerwise_summary_report'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Retailer Wise Summary Report
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=purchase-report'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Purchase Report
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                       <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=purchase_report_details'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Purchase Report Details
                                        </a>
                                        <b class="arrow"></b>
                                      </li>

                                      <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=intransit-details'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          In Transit Purchase Order
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                  </ul>
                                </li>
                                <li class="active open hover">
                                    <a href="#" class="dropdown-toggle">
                                      <i class="menu-icon fa fa-caret-right"></i>
                                      Dispatch Reports
                                      <b class="arrow fa fa-angle-right"></b>
                                    </a>

                                    <b class="arrow"></b>

                                    <ul class="submenu"> 
                                    <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=daily-dispatch-report'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Daily Dispatch Report
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                      <li class="hover">
                                       <a href="<?= $above_url.'/index.php?option=productwise-dispatch-report'?>">
                                         <i class="menu-icon fa fa-caret-right"></i>
                                         Product Wise Dispatch Report
                                       </a>
                                       <b class="arrow"></b>
                                     </li>
                                     <li class="hover">
                                       <a href="<?= $above_url.'/index.php?option=datewise-dispatch-report'?>">
                                         <i class="menu-icon fa fa-caret-right"></i>
                                         Invoice Wise Dispatch Report
                                       </a>
                                       <b class="arrow"></b>
                                     </li>
                                    <!--  <li class="hover">
                                      <a href="index.php?option=intransit-dispatch-report">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        Intransit Dispatch Report
                                      </a>
                                      <b class="arrow"></b>
                                    </li> -->
                                  </ul>
                                </li>
                                <li class="active open hover">
                                    <a href="#" class="dropdown-toggle">
                                      <i class="menu-icon fa fa-caret-right"></i>
                                      Payment Collection
                                      <b class="arrow fa fa-angle-right"></b>
                                    </a>

                                    <b class="arrow"></b>

                                    <ul class="submenu"> 
                               <!--   <li class="hover">
                                      <a href="index.php?option=payment-collection-report">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        Payment Collection Report
                                      </a>
                                      <b class="arrow"></b>
                                    </li> --> 
                                    <li class="hover">
                                      <a href="<?= $above_url.'/index.php?option=retailer_payment_collection_report'?>">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        Payment Collection Report
                                      </a>
                                      <b class="arrow"></b>
                                    </li> 
                                     <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=retailer-ledger-tranc'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Retailer Payment Ledger
                                        </a>
                                        <b class="arrow"></b>
                                      </li> 
                                    <li class="hover">
                                        <a href="<?= $above_url.'/index.php?option=retailer-dues'?>">
                                          <i class="menu-icon fa fa-caret-right"></i>
                                          Retailer 90 Days Dues
                                        </a>
                                        <b class="arrow"></b>
                                      </li>
                                  </ul>
                                </li>
                                <li class="active  hover">
                                    <a href="{{url('credit_debit_notes')}}" >
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        Credit Debit Notes
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            <!--    <li class="active open hover">
                                    <a href="#" class="dropdown-toggle">
                                      <i class="menu-icon fa fa-caret-right"></i>
                                      Other Reports
                                      <b class="arrow fa fa-angle-right"></b>
                                    </a>

                                    <b class="arrow"></b>

                                    <ul class="submenu"> 
                                    <li class="hover">
                                      <a href="index.php?option=claim-report">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        Claim Report
                                      </a>
                                      <b class="arrow"></b>
                                    </li>
                                  </ul>
                                </li>-->

                              </ul>
                            </li> 

                            <?php if($menu=='sync'||$menu=='change-password' ||$menu=='backup-db' ||$menu=='customer-feedback' ||$menu=='help')
                            echo'<li class="active open hover">';    
                            else
                             echo'<li class="open hover">'; ?> 
                              <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-server"></i>
                                <span class="menu-text">
                                  Miscellaneous
                                </span>

                                <b class="arrow fa fa-angle-down"></b>
                              </a>

                              <b class="arrow"></b>

                              <ul class="submenu">
                                <li class="hover">
                                  <a href="<?= $above_url.'/index.php?option=help'?>">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Help Desk
                                  </a>
                                  <b class="arrow"></b>
                                </li>
                                <li class="hover">
                                  <a href="<?= $above_url.'/index.php?option=customer-feedback'?>">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Feedback
                                  </a>
                                  <b class="arrow"></b>
                                </li>
                                <li class="hover">
                                  <a href="<?= $above_url.'/index.php?option=change-password'?>">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Change password
                                  </a>
                                  <b class="arrow"></b>
                                </li>



                                <?php  if($_SERVER['HTTP_HOST'] == 'localhost')
                                {?>

                                  <li class="hover">
                                    <a href="index.php?option=backup-db">
                                      <i class="menu-icon fa fa-caret-right"></i>
                                      BackUp Database
                                    </a>
                                    <b class="arrow"></b>
                                  </li>

                                  <li class="hover">
                                    <!--<a href="../webservices/client_return_data.php">-->
                                      <a href="index.php?option=sync-data&step=1">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        Sync to Server
                                      </a>
                                      <b class="arrow"></b>
                                    </li>  
                                    <?php       }?>

                                  </ul>
                                </li> 

                                

                                <?php
                                  if($menu=='Order-details' || $menu=='testing_dms'|| $menu=='test')
                                echo'<li class=" open hover">'; 
                                    else
                                echo'<li class="  open hover">'; ?> 
                                    <a  href="#" >
                                      <i class="menu-icon fa fa-cart-arrow-down"></i>
                                      <span class="menu-text"> Dealer Details </span>
                                    </a>
                                    <b class="arrow"></b>

                                    <ul class="submenu">
                                        <li class="hover">
                                          <a href="{{url('Order-details/create')}}">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Quick Order
                                          </a>
                                          <b class="arrow"></b>
                                        </li>
                                        <li class="hover">
                                          <a href="{{url('Order-details')}}">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Order History
                                          </a>
                                          <b class="arrow"></b>
                                        </li>
                                        <li class="hover">
                                          <a href="{{url('short_item_list_report')}}">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Order Status
                                          </a>
                                          <b class="arrow"></b>
                                        </li>
                                        
                                       
                                        <li class="hover">
                                          <a href="{{url('invoice-details')}}">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Invoice-Details
                                          </a>
                                          <b class="arrow"></b>
                                        </li>
                                        <li class="hover">
                                          <a href="{{url('dms-payment-details/create')}}">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Make a Payment
                                          </a>
                                          <b class="arrow"></b>
                                        </li>
                                        <li class="hover">
                                          <a href="{{url('dms-payment-details')}}">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Payment History
                                          </a>
                                          <b class="arrow"></b>
                                        </li>
                                        
                                        <li class="hover">
                                          <a href="{{url('dealer_scheme')}}">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Dealer Target Scheme
                                          </a>
                                          <b class="arrow"></b>
                                        </li>
                                       
                                    </ul>
                                </li> 
                                <?php
                                  if($menu=='Order-details' || $menu=='testing_dms'|| $menu=='test')
                                    echo'<li class=" open hover">'; 
                                        else
                                    echo'<li class="  open hover">'; ?> 
                                        <a  href="{{url('credit_debit_notes')}}" >
                                          <i class="menu-icon fa fa-rupee"></i>
                                          <span class="menu-text"> Credit Debit Notes </span>
                                        </a>
                                        <b class="arrow"></b>
                                    </li> 

                                    <li class="hover">
                                      <a href="{{url('dms_dealer_dashboard')}}">
                                        <i class="menu-icon fa fa-server"></i>
                                        Dealer Dashboard
                                      </a>
                                      <b class="arrow"></b>
                                    </li>


                              </ul>
                            </li>
                          </ul>
                        </li>
                        @elseif($role_id == '38' || $role_id == '823')
                        <ul class="nav nav-list">

                            <li class="  hover">
                                    <a  href="#" >
                                      <i class="menu-icon fa fa-cart-arrow-down"></i>
                                      <span class="menu-text"> Dealer Details </span>
                                    </a>
                                    <b class="arrow"></b>

                                    <ul class="submenu">
                                        
                                        <li class="hover">
                                          <a href="{{url('Order-details')}}">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Order History
                                          </a>
                                          <b class="arrow"></b>
                                        </li>
                                        
                                       
                                        <li class="hover">
                                          <a href="{{url('invoice-details')}}">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Invoice-Details
                                          </a>
                                          <b class="arrow"></b>
                                        </li>
                                        <li class="hover">
                                          <a href="{{url('dms-payment-details/create')}}">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Make a Payment
                                          </a>
                                          <b class="arrow"></b>
                                        </li>
                                        <li class="hover">
                                          <a href="{{url('dms-payment-details')}}">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Payment History
                                          </a>
                                          <b class="arrow"></b>
                                        </li>

                                        

                                       
                                    </ul>
                                </li> 
                                <li class="  open hover">
                                    <a  href="{{url('credit_debit_notes')}}" >
                                      <i class="menu-icon fa fa-rupee"></i>
                                      <span class="menu-text"> Credit Debit Notes </span>
                                    </a>
                                    <b class="arrow"></b>
                                </li> 

                                
                            </ul>
                        @else
                          
                          @if($role_id != 1 && $role_id != 41)
                            <ul class="nav nav-list">

                            <li class="  hover">
                                    <a  href="#" >
                                      <i class="menu-icon fa fa-cart-arrow-down"></i>
                                      <span class="menu-text"> Dealer Details </span>
                                    </a>
                                    <b class="arrow"></b>

                                    <ul class="submenu">
                                        <li class="hover">
                                          <a href="{{url('Order-details/create')}}">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Quick Order
                                          </a>
                                          <b class="arrow"></b>
                                        </li>
                                        <li class="hover">
                                          <a href="{{url('Order-details')}}">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Order History
                                          </a>
                                          <b class="arrow"></b>
                                        </li>
                                        
                                       
                                        <li class="hover">
                                          <a href="{{url('invoice-details')}}">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Invoice-Details
                                          </a>
                                          <b class="arrow"></b>
                                        </li>
                                        <li class="hover">
                                          <a href="{{url('dms-payment-details/create')}}">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Make a Payment
                                          </a>
                                          <b class="arrow"></b>
                                        </li>
                                        <li class="hover">
                                          <a href="{{url('dms-payment-details')}}">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Payment History
                                          </a>
                                          <b class="arrow"></b>
                                        </li>
                                        
                                        

                                       
                                    </ul>
                                </li> 
                                <li class="  open hover">
                                    <a  href="{{url('credit_debit_notes')}}" >
                                      <i class="menu-icon fa fa-rupee"></i>
                                      <span class="menu-text"> Credit Debit Notes </span>
                                    </a>
                                    <b class="arrow"></b>
                                </li> 

                                
                            </ul>
                            @endif
                        
                        @endif




                      </div>


                      <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                  </div><!-- /.row -->
                </div><!-- /.page-content -->
              </div>
            </div><!-- /.main-content -->


            <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
              <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
            </a>
 

    @yield('dms_body')
