    <script src="assets/js/bootstrap.min.js"></script>
<html lang="en">
    <head>
     
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta charset="utf-8" />
        <title>DISTRIBUTOR PANEL</title>

        <meta name="description" content="top menu &amp; navigation" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

        <!-- bootstrap & fontawesome -->
        <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
        <link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css" />

        <!-- page specific plugin styles -->

        <!-- text fonts -->
        <link rel="stylesheet" href="assets/css/fonts.googleapis.com.css" />

        <!-- ace styles -->
        <link rel="stylesheet" href="assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />

        <!--[if lte IE 9]>
                <link rel="stylesheet" href="assets/css/ace-part2.min.css" class="ace-main-stylesheet" />
        <![endif]-->
        <link rel="stylesheet" href="assets/css/ace-skins.min.css" />
        <link rel="stylesheet" href="assets/css/ace-rtl.min.css" />

        <!--[if lte IE 9]>
          <link rel="stylesheet" href="assets/css/ace-ie.min.css" />
        <![endif]-->

        <!-- inline styles related to this page -->

        <!-- ace settings handler -->
        <script src="assets/js/ace-extra.min.js"></script>

        <!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

        <!--[if lte IE 8]>
        <script src="assets/js/html5shiv.min.js"></script>
        <script src="assets/js/respond.min.js"></script>
        <![endif]-->
    </head>

    <body class="no-skin">
        <div id="navbar" class="navbar navbar-default    navbar-collapse       h-navbar ace-save-state">
            <div class="navbar-container ace-save-state" id="navbar-container">
                <div class="navbar-header pull-left">
                    
                    <?php
			//pre($_SESSION);
			?>
                    
                    
                    <a href="index.php" class="navbar-brand">
                        <small>
<!--                            <i class="fa fa-leaf"></i>-->
                            <img src="./images/logo.png" style="width:28px; height: 26px"/>
                            DISTRIBUTOR: <?php echo $_SESSION[SESS.'data']['dealer_name']; ?>
                        </small> 
                    </a>

                    <button class="pull-right navbar-toggle navbar-toggle-img collapsed" type="button" data-toggle="collapse" data-target=".navbar-buttons,.navbar-menu">
                        <span class="sr-only">Toggle user menu</span>

                        <img src="assets/images/avatars/user.jpg" alt="Jason's Photo" />
                    </button>

                    <button class="pull-right navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#sidebar">
                        <span class="sr-only">DMS</span>

                        <span class="icon-bar"></span>

                        <span class="icon-bar"></span>

                        <span class="icon-bar"></span>
                    </button>
                </div>
                <!-- ANKUSH -->
             
                <div class="navbar-buttons navbar-header pull-right  collapse navbar-collapse" role="navigation">
                  <ul class="nav ace-nav">
                    <li>
                     <a href="index.php?option=logout">
                      <i class="ace-icon fa fa-power-off"></i>
                      Logout
                    </a>
                  </li>
                </ul>
              </div>
                
                <div class="pull-right" role="navigation" style="color: #FFF;margin-right: 30px;margin-top: 5px;">
                    <span>
                        <a style="color: #FFF;" href="mailto:dshelpdesk@manacleindia.com">Email ID :- dshelpdesk@manacleindia.com</a>
                        <br/>                                  
                        Contact No. :- +91- 8010819820</span>
                </div>

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
                     $qs = "SELECT `qty` FROM `threshold` where product_id=$product_id AND dealer_id = $dealer_id";
			//h1($qs);
                     $rss = mysqli_query($dbc,$qs);
                     $rows = mysqli_fetch_assoc($rss);
                     $qtyy = $rows['qty'];
                     if($remain<=$qtyy)
                     {
                       $i = $i+1;
                     }
                   }
                   
                   ?>

                   <li class="transparent dropdown-modal">
                    <a title="LOW BALANCE STOCK OF (<?=$i?>) PRODUCTS" class="iframef" href="index.php?option=purchase-order&showmode=1&mode=1">
                      <i class="ace-icon fa fa-bell icon-animated-bell"></i>
                      Low Stock
                      <span class="badge badge-warning"><?=$i?></span>
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

<?php $menu = $_GET['option']; ?>
<?php
$index = 'index.php';
$url = base64_encode ($index.php);
?>
                <ul class="nav nav-list">
                           <?php if($menu=='')
                         echo'<li class="active open hover">';    
                          else
                         echo'<li class="open hover">'; ?> 
                            <a href="<?=$index?>" id="myhome">
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
                                    <a href="index.php?option=add-van">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                   VAN
                                </a>
                                <b class="arrow"></b>
                            </li>
                             <li class="hover">
                                <a href="index.php?option=add-threshold">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Stock Threshold
                                </a>

                                <b class="arrow"></b>
                            </li>
                            <li class="hover">
                                <a href="index.php?option=catalog-product">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Product Master
                                </a>

                                <b class="arrow"></b>
                            </li>
                            
                             <li class="hover">
                                        <a href="index.php?option=add-user">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            User
                                        </a>

                                        <b class="arrow"></b>
                              </li>
                              
                              <li class="hover">
                                        <a href="index.php?option=retailer-add">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Retailer
                                        </a>

                                        <b class="arrow"></b>
                              </li> 
                              
                              <li class="hover">
                                <a href="index.php?option=location-category&mtype=5">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Beat
                                </a>

                                <b class="arrow"></b>
                            </li>
<!--                            <li class="hover">
                                <a href="index.php?option=dispatch-beat">
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
                                        <a href="index.php?option=add-user">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            User
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="hover">
                                        <a href="index.php?option=retailer-add">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Retailer
                                        </a>

                                        <b class="arrow"></b>
                                    </li> 
                                    
                                    <li class="hover">
                                <a href="index.php?option=location-category&mtype=5">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Beat
                                </a>

                                <b class="arrow"></b>
                            </li>
                            
                              <li class="hover">
                                <a href="index.php?option=dispatch-beat">
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
                    <?php if($menu=='purchase-order')
                         echo'<li class="active open hover">';    
                          else
                         echo'<li class="open hover">'; ?> 
                        <a href="index.php?option=purchase-order">
                            <i class="menu-icon fa fa-cart-arrow-down"></i>
                            <span class="menu-text"> Purchase Order </span>
                        </a>
                        <b class="arrow"></b>
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
 
                    <?php if($menu=='balance-stock')
                         echo'<li class="active open hover">';    
                          else
                         echo'<li class="open hover">'; ?> 
                        <a href="index.php?option=balance-stock">
                            <i class="menu-icon fa fa-database"></i>
                            <span class="menu-text"> Stock </span>
                        </a>
                        <b class="arrow"></b>
                    </li> 
                 
                    
                  <!---------------------------------------------------------RETAILER CLAIM------------------------------->
			 <?php if($menu=='retailer-claim')
                         echo'<li class="active open hover">';    
                          else
                         echo'<li class="open hover">'; ?> 
                        <a href="index.php?option=retailer-claim">
                           <i class="menu-icon fa fa-gift"></i>
                                   
                            <span class="menu-text">  Retailer Claim </span>
                        </a>
                        <b class="arrow"></b>
                    </li> 
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
                                <a href="index.php?option=direct-challan">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Direct Invoice
                                </a>
                                <b class="arrow"></b>
                            </li>
                             <li class="hover">
                                <a href="index.php?option=sale-order-detailes">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Billing Through SFA
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="hover">
                                <a href="index.php?option=make-challan">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Invoice Details
                                </a>
                                <b class="arrow"></b>
                            </li>
                             <li class="hover">
                                <a href="index.php?option=return-challan">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Edit Bill
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="hover">
                                <a href="index.php?option=Invoice-Summary">
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
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-share-square-o"></i>
                            <span class="menu-text">
                                Daily Dispatch
                            </span>

                            <b class="arrow fa fa-angle-down"></b>
                        </a>

                        <b class="arrow"></b>

                        <ul class="submenu">
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
                        </ul>
                    </li>
                    
                     <?php //if($menu=='payment-collection')
                     if($menu=='payment')
                         echo'<li class="active open hover">';    
                          else
                         echo'<li class="open hover">'; ?> 
                        <a href="index.php?option=payment">
                            <i class="menu-icon fa fa-inr"></i>
                            <span class="menu-text"> Payment Collection </span>
                        </a>
                        <b class="arrow"></b>
                    </li> 

                    <?php if($menu=='damage-details'||$menu=='damage-challan')
                         echo'<li class="active open hover">';    
                          else
                         echo'<li class="open hover">'; ?> 
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-share-square-o"></i>
                            <span class="menu-text">
                                Damage & Replace
                            </span>

                            <b class="arrow fa fa-angle-down"></b>
                        </a>

                        <b class="arrow"></b>

                        <ul class="submenu">
                            <li class="hover">
                                <a href="index.php?option=damage-details">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Damage/Replace Details
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="hover">
                                <a href="index.php?option=damage-challan">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Damage/Replace List
                                </a>
                                <b class="arrow"></b>
                            </li>                                            
                        </ul>
                    </li> 


                    <?php if($menu=='primary-sale-details'||$menu=='intransit-dispatch-report' ||$menu=='stock-age-report' ||$menu=='sku-wise-report'||$menu=='intransit-dispatch-report'||$menu=='sale-report'||$menu=='aging-report'||$menu=='user-aging-report'||$menu=='challan-report-details'||$menu=='tax_inv_sale_report'||$menu=='tax_register_report'||$menu=='daily-dispatch-report' || $menu=='payment-collection-report'||$menu=='catalog_1&mtype=1'||$menu=='catalog_2&mtype=2' ||$menu=='catalog-rate-list' || $menu=='focus-product' ||$menu=='user-sale-performance' || $menu=='retailer-sale-performance')
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
                                        <a href="index.php?option=challan-report-details">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Challan Vs Sale
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                    <li class="hover">
                                        <a href="index.php?option=tax_inv_sale_report">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Tax Invoice Sale Report
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                    <li class="hover">
                                        <a href="index.php?option=sale-report">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            DSR Salesman wise Report
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                    <li class="hover">
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
                                        <a href="index.php?option=retailer-sale-performance">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                                Retailers Sale Performance
                                                </a>
                                        <b class="arrow"></b>
                                    </li>
                                </ul>
                            </li>
                            
                             <li class="active open hover">
                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Other Reports
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                <b class="arrow"></b>

                                <ul class="submenu">
                                    
                                    <li class="hover">
                                        <a href="index.php?option=primary-sale-details">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Stock Received
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                    <li class="hover">
                                        <a href="index.php?option=stock-age-report">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Stock Age
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                    <li class="hover">
                                        <a href="index.php?option=purchase-report">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Purchase Report
                                        </a>
                                        <b class="arrow"></b>
                                    </li>

                                    <li class="hover">
                                        <a href="index.php?option=intransit-details">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            In Transit Purchase Order
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                    <li class="hover">
                                        <a href="index.php?option=intransit-dispatch-report">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            In Transit Dispatch
                                        </a>
                                        <b class="arrow"></b>
                                    </li>


                                    <li class="hover">
                                        <a href="index.php?option=daily-dispatch-report">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Daily Dispatch Report
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                    <li class="hover">
                                        <a href="index.php?option=intransit-dispatch-report">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Intransit Dispatch Report
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                    <li class="hover">
                                        <a href="index.php?option=payment-collection-report">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Payment Collection Report
                                        </a>
                                        <b class="arrow"></b>
                                    </li>  

                                    <li class="hover">
                                        <a href="index.php?option=claim-report">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Claim Report
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li> 
                  <?php if($menu=='sync'||$menu=='change-password' ||$menu=='backup-db')
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
                                <a href="index.php?option=help">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Help Desk
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="hover">
                                <a href="index.php?option=change-password">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Change password
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="hover">
                                <a href="index.php?option=backup-db">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    BackUp Database
                                </a>
                                <b class="arrow"></b>
                            </li>
                          <?php       if($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'domain' || $_SERVER['HTTP_HOST'] == 'localhost:8080' || $_SERVER['HTTP_HOST'] == 'domain:8080' || substr($_SERVER['HTTP_HOST'], 0, 7) == '192.168' || substr($_SERVER['HTTP_HOST'], 0, 7) == '172.168')
                              {?>
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


                </ul>
                </li>
                </ul>
                </li>




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

<div class="main-content">
    <div class="main-content-inner">
