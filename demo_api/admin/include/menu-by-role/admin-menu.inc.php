<div id="nav" class="yui3-menu yui3-menu-horizontal" role="menubar"><!-- Bounding box -->
    <div class="yui3-menu-content"><!-- Content box -->
        <ul>
            <li id="_Alt_i"><a class="yui3-menu-label" href="index.php" id="myhome">Home</a></li>
            <!-- MASTER part start here -->
            <?php $rol_id = $_SESSION[SESS . 'data']['role_id'];
            $product_division = $_SESSION[SESS . 'data']['product_division'];
            if ($rol_id == 1 ||$rol_id == 50) {
                ?>
                <li>
                    <a class="yui3-menu-label" href="#">Master</a>
                    <div id="academics" class="yui3-menu">
                        <div class="yui3-menu-content">
                            <ul>
                                <li class="yui3-menuitem">
                                    <a class="yui3-menu-label" href="#">Catalog</a>
                                    <div id="about" class="yui3-menu">
                                        <div class="yui3-menu-content">
                                            <ul>
                                                <?php
                                                if (!empty($_SESSION[SESS . 'constant']['catalog_level'])) {
                                                    for ($i = 1; $i <= $_SESSION[SESS . 'constant']['catalog_level']; $i++) {
                                                        if ($i >= 2) $option = 2;

                                                        ?>
                                                        <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                                     href="index.php?option=catalog_<?php if (isset($option)) echo $option; else echo $i; ?>&mtype=<?php echo $i; ?>"><?php echo ucwords($_SESSION[SESS . 'constant']["catalog_title_$i"]); ?></a>
                                                        </li>
                                                        <?php
                                                    }
                                                } // if(!empty($_SESSION[SESS.'catlevel'])) end here
                                                ?>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=catalog-product">Catalog
                                                        Product</a>
                                                </li>
                                                        <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                        href="index.php?option=catalog-rate-list">Catalog Rate List</a>
                                                          </li>

                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=focus-product">Focus
                                                        Product</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                                <li class="yui3-menuitem">
                                    <a class="yui3-menu-label" href="#">Location</a>
                                    <div id="about" class="yui3-menu">
                                        <div class="yui3-menu-content">
                                            <ul>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=location"><?php echo ucwords($_SESSION[SESS . 'constant']["location_title_1"]); ?></a>
                                                </li>
                                                <?php

                                                for ($i = 2; $i <= $_SESSION[SESS . 'constant']["location_level"]; $i++) {
                                                    ?>
                                                    <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                                 href="index.php?option=location-category&mtype=<?php echo $i; ?>"><?php echo ucwords($_SESSION[SESS . 'constant']["location_title_$i"]); ?></a>
                                                    </li>
                                                    <?php
                                                }
                                                //} //!empty($_SESSION[SESS.'constant']['location_level']) end here
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </li>

                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                             href="index.php?option=position-master">Position Master</a>
                                </li>

                                <!--                                <li class="yui3-menuitem">-->
                                <!--                                    <a class="yui3-menu-label" href="#">Location wise Position</a>-->
                                <!--                                    <div id="about" class="yui3-menu">-->
                                <!--                                        <div class="yui3-menu-content">-->
                                <!--                                            <ul>-->
                                <!---->
                                <!--                                                    <li class="yui3-menuitem">-->
                                <!--                                                        <a class="yui3-menuitem-content" href="index.php?option=location-position&mtype=1">Zone</a>-->
                                <!--                                                        <a class="yui3-menuitem-content" href="index.php?option=location-position&mtype=2">Zone 1</a>-->
                                <!--                                                        <a class="yui3-menuitem-content" href="index.php?option=location-position&mtype=3">Region</a>-->
                                <!--                                                        <a class="yui3-menuitem-content" href="index.php?option=location-position&mtype=4">State</a>-->
                                <!--                                                        <a class="yui3-menuitem-content" href="index.php?option=location-position&mtype=5">Area</a>-->
                                <!--                                                        <a class="yui3-menuitem-content" href="index.php?option=location-position&mtype=6">Territory</a>-->
                                <!--                                                        <a class="yui3-menuitem-content" href="index.php?option=location-position&mtype=7">Belt</a>-->
                                <!--                                                    </li>-->
                                <!--                                            </ul>-->
                                <!--                                        </div>-->
                                <!--                                    </div>-->
                                <!--                                </li>-->

                                <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=case">Cases</a>
                                </li>

                                <li class="yui3-menuitem">
                                    <a class="yui3-menu-label" href="#">Users</a>
                                    <div id="about" class="yui3-menu">
                                        <div class="yui3-menu-content">
                                            <ul>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=add-user">User
                                                        details</a></li>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=dealer">Dealer
                                                        details</a></li>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=csa">CSA details</a>
                                                </li>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=all_dealer_person">All
                                                        Dealer Login Details</a></li>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=retailer">Retailer
                                                        details</a></li>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=retailer-move">Retailers
                                                        Move/Copy</a></li>
                                                <!-- <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=plumber">Add Plumber</a></li>-->
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=set-default-rights">Set
                                                        Default Rights</a></li>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=dealer-scheme">Dealer
                                                        Scheme</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
                <!-- MASTER part ends here -->
                <!-- Inventory part start here -->
           <?php        if ($rol_id != 50) {
                ?>
                <li>
                    <a class="yui3-menu-label" href="#">Settings</a>
                    <div id="academics" class="yui3-menu">
                        <div class="yui3-menu-content">
                            <ul>
                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                             href="index.php?option=dealer-ownership">Dealer Ownership
                                        Type</a></li>
                                <!--              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=field-experience">Field Experience</a></li>-->
                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                             href="index.php?option=retailer-mkt-gift">Retailer Market
                                        Gift</a></li>
                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                             href="index.php?option=outlet-type">Outlet Type</a></li>
                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                             href="index.php?option=working-status">Working Status</a>
                                </li>
                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                             href="index.php?option=travelling-mode">Travelling Mode</a>
                                </li>
                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                             href="index.php?option=tracking-time">Tracking Time</a>
                                </li>
                                <!--                <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=tax-type">Tax Type</a></li>-->

                            </ul>
                        </div>
                    </div>
                </li>
            <?php } }

            if ($rol_id != 1 && $rol_id != 50) {
                ?>

                <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=add-user">User
                        details</a></li>
                <li>
                    <a class="yui3-menu-label" href="index.php?option=primary-sale-details">Primary Sale Order</a>
                </li>
                <li>
                    <a class="yui3-menu-label" href="index.php?option=complaint">Complaint</a>
                </li>
            <?php } ?>
            <li>
                <a class="yui3-menu-label" href="#">Sale Order</a>
                <div id="academics" class="yui3-menu">
                    <div class="yui3-menu-content">
                        <ul>
                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                         href="index.php?option=order-details">Order Details</a></li>
                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                         href="index.php?option=Dealer-Stock">Dealer Stock</a></li>
                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                         href="index.php?option=retailer-stock">Retailer Stock</a></li>
                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                         href="index.php?option=daily-activity-reports">User Daily
                                    Activity</a></li>
                            <?php if ($rol_id == 1 || $rol_id == 50) { ?>
                                <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=mtp">Monthly
                                        Tour Plan</a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </li>
            <?php if ($rol_id == 1 || $rol_id == 50) { ?>
                <li>
                    <a class="yui3-menu-label" href="index.php?option=primary-sale-details">Primary Sale Order</a>
                </li>
            <?php } ?>
            <?php if ($rol_id == 1 || $rol_id == 50) { ?>
                <li>
                    <a class="yui3-menu-label" href="index.php?option=complaint">Complaint</a>
                </li>
            <?php } ?>


            <!--  -------------------------NEW REPORT ---------------------------------------  -->

            <?php if ($rol_id == 1 ||$rol_id == 50) { ?>
                <li>
                    <a class="yui3-menu-label" href="#">Claim Report</a>
                    <div id="academics" class="yui3-menu">
                        <div class="yui3-menu-content">
                            <ul>

                                <li><a class="yui3-menuitem-content" href="index.php?option=user-claim-report">Distributor
                                        Claim</a></li>
                                <li><a class="yui3-menuitem-content" href="index.php?option=claim-retailer-report">Retailer
                                        Claim</a></li>

                            </ul>
                        </div>
                    </div>
                </li>

            <?php } ?>
            <!-- Report part start here -->
            <li>
                <a class="yui3-menu-label" href="#">Report</a>

                <div id="academics" class="yui3-menu">
                    <div class="yui3-menu-content">


                        <ul>
                            <li class="yui3-menuitem">
                                <a class="yui3-menu-label" href="#">User Reports</a>
                                <div id="about" class="yui3-menu">
                                    <div class="yui3-menu-content">
                                        <ul>

                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=user-attendence">User
                                                    Daily Attendance</a></li>

                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=time-report-attd">Attendance
                                                    Report</a></li>

                                            <!-- <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=user-sale-details">User
                                                    Details Whatsapp Format</a></li> -->

                                            <!-- <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=user-product-sale-details">Product
                                                    Details Whatsapp Format</a></li> -->

                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=user-expanse-report">User
                                                    Expense Report</a></li>

                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=sale-month-report">Sale
                                                    Monthly Report</a></li>

                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=sale-month-report-summary">Sale
                                                    Monthly Report Summary</a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=attendance-summary">Attendance
                                                    Summary</a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=user-tracking-report">User
                                                    Tracking Report</a></li>
                                                    <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=user-tracking-report-new">User New
                                                    Tracking Report</a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=user-tracking-distance">User
                                                    Tracking Distance</a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=sale-time-report">First
                                                    Call Time Report</a></li>

                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=time-report">Time
                                                    Report</a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=user-complaint">User
                                                    Complaint</a></li>
                                            <!--<li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=phone-status">Mobile On/Off Report</a></li> phone_status_details doesn't exit-->

                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=notification-non-contacted">Non
                                                    Contacted Notification</a></li>

                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=user-daily-report">User
                                                    Daliy Report</a></li>
                                            <?php if ($rol_id == 1) { ?>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=merchandise-report">Merchendise
                                                        Report</a></li>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=user-sale-yearly-report">User
                                                        Sales Summary Report</a></li>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=sale-month-report-npc">Day
                                                        wise Call Report</a></li>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=dealer-user-info">User
                                                        Dealer Info </a></li>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=person-senior-details">User
                                                        Senior Info </a></li>

                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=sale-summary-report">Sale
                                                        Summary Report </a></li>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=user-retailer-report">User
                                                        Dealer Retailer Details </a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            <li class="yui3-menuitem">
                                <a class="yui3-menu-label" href="#">Sale Reports</a>
                                <div id="about" class="yui3-menu">
                                    <div class="yui3-menu-content">
                                        <ul>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=sale-order-row">Sale
                                                    Order Report</a></li>

                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=sales-man-secondary">Sales
                                                    Man Secondary</a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=user-monthly-reports">User
                                                    Wise MTP</a></li>

                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=advance-summary-report">Advance
                                                    Summary</a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=rds-wise">RDS Wise
                                                    Sale</a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=dsr-monthly">DSR
                                                    monthly</a></li>

                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=dealer-sale-details">Dealer
                                                    Sale Reports</a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=challan-sale">Challan vs
                                                    Sale Reports</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            <li class="yui3-menuitem">
                                <a class="yui3-menu-label" href="#">Tally Reports</a>
                                <div id="about" class="yui3-menu">
                                    <div class="yui3-menu-content">

                                        <ul>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=ss-stock">SS
                                                    Stock Report</a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=ss-billing">SS
                                                    Billing Report</a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=ss-closing-stock">SS
                                                    Closing Stock Report</a></li>
                                                    <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=ss-closing-stock-pcs">SS
                                                    Closing Pcs Stock Report</a></li>
                                        </ul>
                                    </div>
                                </div>

                            </li>
                            <li class="yui3-menuitem">
                                <a class="yui3-menu-label" href="#">Busy Reports</a>
                                <div id="about" class="yui3-menu">
                                    <div class="yui3-menu-content">

                                        <ul>
                                           <!--  <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=busy-ss-stock">SS
                                                    Stock Report</a></li> -->
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=busy-ss-billing">SS
                                                    Billing Report</a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=busy-ss-closing-stock">SS
                                                    Closing Stock Report</a></li> 
                                        </ul>
                                    </div>
                                </div>

                            </li>
                            <li class="yui3-menuitem">
                                <a class="yui3-menu-label" href="#">Other Reports</a>
                                <div id="about" class="yui3-menu">
                                    <div class="yui3-menu-content">

                                        <ul>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=monthly-tour-plan-new">Monthly
                                                    Tour Plan</a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=user-retailer-report">User
                                                    Dealer Retailer Details </a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=detail-report-advanced">Detail
                                                    Report Advanced</a></li>
                                            <!--<li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=dealer-balance-stock">Dist. Balance Stock</a></li> dealer_bal_stock table doesn't exit-->
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=damage-replace">Damage
                                                    Distributor Report</a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=damage-replace-retailer">Damage
                                                    Reatailer Report</a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=no-attendance">No
                                                    Attendance</a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=no-sale">No Booking</a>
                                            </li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=utilization">Utilization
                                                    Report</a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=used-function-report">App
                                                    Used Report</a></li>

                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=retailer-payment-reports">Retailer
                                                    Payment Report</a></li>
                                            <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                         href="index.php?option=dealer-payment-reports">Dealer
                                                    Payment Report</a></li>
                                            <?php if ($rol_id == 1) { ?>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=ret-new-rep">Retailer
                                                        Billing Report</a></li>
                                            <?php } ?>
                                            <li class="yui3-menuitem">
                                                <a class="yui3-menuitem-content" href="index.php?option=user-leave-request-reports">User Leave Request</a>
                                            </li>
                                            <li class="yui3-menuitem">
                                                <a class="yui3-menuitem-content" href="index.php?option=tour-and-advance-expense-reports">Tour And Advance Expense Report</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                            </li>
                            <?php if ($product_division != 3) { ?>
                                <li class="yui3-menuitem">
                                    <a class="yui3-menu-label" href="#">Secondary Reports</a>
                                    <div id="about" class="yui3-menu">
                                        <div class="yui3-menu-content">
                                            <ul>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=manpower-performance">Manpower
                                                        Performance</a></li>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=customer-performance">Customer
                                                        Performance</a></li>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=rds-vs-billing">RDS
                                                        Retail Booking VS Billing</a></li>
                                                <!-- <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=classification-wise-sales">Classification
                                                        Wise</a></li> -->
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=category-performance">Category
                                                        Performance</a></li>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=state-level-performance">State
                                                        Level Performance</a></li>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=dropped-outlet-report">Dropped
                                                        Outlet Report</a></li>
                                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=dropped-sku-report">Dropped
                                                        SKU Report</a></li>
                                                <!-- <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                                             href="index.php?option=productive-outlets-tracking">Productive
                                                        Outlets Tracking</a></li> -->
                                                <!--<li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=no-sale">No Booking</a></li>-->
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            <?php } ?>
                            <li>
                                <a class="yui3-menu-label" href="index.php?option=all_sales_reports">Daily Sales Summary
                                    Reports</a>
                            </li>

                            <li>
                                <a class="yui3-menu-label" href="index.php?option=dealer-report">Distributor Report</a>
                            </li>
                            <!-- <?php if ($rol_id == 1){ ?> -->
                            <!-- <li>
                                <a class="yui3-menu-label" href="index.php?option=winback">Retailer Winback Report</a>
                            </li> -->
                            <!-- <?php } ?> -->
                        </ul>

                    </div>
                </div>
            </li>

            <!-- Report part ends here -->


            <!-- --------------------------------------------------------------------------------->
            <!-- MISCELLANEOUS part start here -->
            <li>
                <a class="yui3-menu-label" href="#">Miscellaneous</a>
                <div id="academics" class="yui3-menu">
                    <div class="yui3-menu-content">
                        <ul>
                            <!--   <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=change-password">Change Password</a></li> -->
                            <?php if ($rol_id == 1|| $rol_id == 50) { ?>
                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                             href="index.php?option=circular">Circular Detail</a></li>
                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                             href="index.php?option=circular-report">Circular Report</a>
                                </li>


                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                             href="index.php?option=upload-pdf">Upload PDF</a>
                                </li>
                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                             href="http://162.213.190.125/msell-gopal/webservices/retailer_download.php">Retailer Export</a>
                                </li>
                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                             href="index.php?option=sale-summary-export">Sale Summary Export</a>
                                </li>
                                <li class="yui3-menuitem"><a class="yui3-menuitem-content"
                                                             href="index.php?option=dealer-beat-export">Distributor Export</a>
                                </li>
                                

                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </li>
            <!-- MISCELLANEOUS part ends here -->
            <li><a class="yui3-menu-label" href="index.php?option=logout">Log Out</a></li>
        </ul>
    </div>
</div>  
