<div id="nav" class="yui3-menu yui3-menu-horizontal" role="menubar"><!-- Bounding box -->
  <div class="yui3-menu-content" ><!-- Content box -->
     <ul>
      <li id="_Alt_i"><a class="yui3-menu-label" href="index.php" id="myhome">Home</a></li>
      <!-- MASTER part start here -->
      <?php $rol_id = $_SESSION[SESS.'data']['role_id']; 
      if($rol_id == 1){
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
                  if(!empty($_SESSION[SESS.'constant']['catalog_level'])) { 
                    for($i = 1; $i <= $_SESSION[SESS.'constant']['catalog_level'];$i++)
                    {
                       if($i >= 2 ) $option = 2;
                      
                    ?>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=catalog_<?php if(isset($option)) echo $option; else echo $i; ?>&mtype=<?php echo $i; ?>"><?php echo ucwords($_SESSION[SESS.'constant']["catalog_title_$i"]); ?></a></li>
                    <?php
                    }
                  } // if(!empty($_SESSION[SESS.'catlevel'])) end here
                    ?>
                  <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=catalog-product">Catalog Product</a></li> 
                  <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=catalog-rate-list">Catalog Rate List</a></li>
<!--                  <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=pack-name">Pack Name</a></li>
                  <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=product-company">Product Company</a></li>-->
                   <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=focus-product">Focus Product</a></li> 
                    </ul> 
                  </div>
                </div> 
              </li>  
              <li class="yui3-menuitem">
                <a class="yui3-menu-label" href="#">Location</a>
                <div id="about" class="yui3-menu">
                  <div class="yui3-menu-content">
                    <ul>
                    <?php
                      //if(!empty($_SESSION[SESS.'constant']['location_level'])) { ?>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=location"><?php echo ucwords($_SESSION[SESS.'constant']["location_title_1"]); ?></a></li>
                      <?php
                       
                        for($i = 2;$i<=$_SESSION[SESS.'constant']["location_level"];$i++)
                        {
                      ?>
                        <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=location-category&mtype=<?php echo $i; ?>"><?php echo ucwords($_SESSION[SESS.'constant']["location_title_$i"]);  ?></a></li>
                     <?php
                        }
                    //} //!empty($_SESSION[SESS.'constant']['location_level']) end here
                        ?>
                      <!--<li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=process-plan">Process Plan</a></li>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=nesting">Nesting</a></li>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=plate-planner">Plate Planner</a></li> -->
                    </ul> 
                  </div>
                </div> 
              </li>           
<!--          <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=session-year">Session</a></li>-->
             <!--<li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=scheme">Scheme</a></li>-->
            <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=case">Cases</a></li>
            <!-------------------------SCHEME----------------------------------------------->
            <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=dealer_target">Dealer Target</a></li>
            
              <li class="yui3-menuitem">
                <a class="yui3-menu-label" href="#">Scheme</a>
                <div id="about" class="yui3-menu">
                  <div class="yui3-menu-content">
                    <ul>                        
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=scheme">Inbuilt Scheme</a></li>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=scheme-value">Q.P.S/V.P.S.</a></li>
                       </ul> 
                  </div>
                </div> 
              </li>  
            
            
            
            <!------------------------------------------------------------------------------>
             <li class="yui3-menuitem">
                <a class="yui3-menu-label" href="#">Users</a>
                <div id="about" class="yui3-menu">
                  <div class="yui3-menu-content">
                    <ul>                        
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=add-user">User details</a></li>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=dealer">Dealer details</a></li>
                       <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=all_dealer_person">All Dealer Login Details</a></li>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=retailer">Retailer details</a></li>
                      <!-- <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=plumber">Add Plumber</a></li>-->
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=set-default-rights">Set Default Rights</a></li>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=dealer-scheme">Dealer Scheme</a></li>
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
      <li>
        <a class="yui3-menu-label" href="#">Settings</a>
        <div id="academics" class="yui3-menu">
          <div class="yui3-menu-content">
            <ul> 
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=dealer-ownership">Dealer Ownership Type</a></li>
<!--              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=field-experience">Field Experience</a></li>-->
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=retailer-mkt-gift">Retailer Market Gift</a></li>
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=outlet-type">Outlet Type</a></li>
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=working-status">Working Status</a></li>
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=travelling-mode">Travelling Mode</a></li>
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=tracking-time">Tracking Time</a></li>
<!--                <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=tax-type">Tax Type</a></li>-->
               
          </ul>
          </div>
        </div>
      </li>
      <!-- Inventory part ends here -->
      <!-- Challan part start here -->
<!--<li>
        <a class="yui3-menu-label" href="#">Job Order</a>
        <div id="academics" class="yui3-menu">
          <div class="yui3-menu-content">
            <ul> 
              <li class="yui3-menuitem">
                <a class="yui3-menu-label" href="#">Annexure</a>
                <div id="about" class="yui3-menu">
                  <div class="yui3-menu-content">
                    <ul>                        
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=job-order">Job Order</a></li>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=annexure">Annexure</a></li>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=annexure-receive">Annexure Receive</a></li>
                    </ul> 
                  </div>
                </div> 
              </li>
              <li class="yui3-menuitem">
                <a class="yui3-menu-label" href="#">RGP</a>
                <div id="about" class="yui3-menu">
                  <div class="yui3-menu-content">
                    <ul>                        
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=rgp-challan">RGP Challan</a></li>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=rgp-receiving">RGP Receive</a></li>
                    </ul> 
                  </div>
                </div> 
              </li>-->
             <!-- <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=rgp-challan">RGP Challan</a></li>
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=rgp-receiving">RGP Receiving</a></li>-->
              <!--<li class="yui3-menuitem">
                <a class="yui3-menu-label" href="#">Party</a>
                <div id="about" class="yui3-menu">
                  <div class="yui3-menu-content">
                    <ul>                        
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=party-vendor">Vendor</a></li>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=party-customer">Customer</a></li>
                    </ul> 
                  </div>
                </div> 
              </li> -->
<!--            </ul>
          </div>
        </div>
      </li>-->
      <!-- Challan part ends here -->
      
      <!-- Production part start here -->
      <?php }

      if($rol_id != 1){
      ?>

      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=add-user">User details</a></li>
      <?php }?>
    <li>
        <a class="yui3-menu-label" href="#">Sale Order</a>
        <div id="academics" class="yui3-menu">
          <div class="yui3-menu-content">
            <ul> 
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=order-details">Order Details</a></li>
              <?php if($rol_id == 1){ ?>
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=mtp">Monthly Tour Plan</a></li>
              <?php } ?>
            </ul>
          </div>
        </div>
      </li>
       <?php if($rol_id == 1){ ?>
       <li>
        <a class="yui3-menu-label" href="index.php?option=primary-sale-details">Primary Sale Order</a>
      </li>
      <?php } ?>
       <?php if($rol_id == 1){ ?>
       <li>
        <a class="yui3-menu-label" href="index.php?option=complaint">Complaint</a>
      </li>
      <?php } ?>
      <!-- Production part ends here -->
      
      <!-- Sales part start here -->
<!--      <li>
        <a class="yui3-menu-label" href="#">Sales</a>
        <div id="academics" class="yui3-menu">
          <div class="yui3-menu-content">
            <ul> 
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=make-invoice">Make-invoice</a></li>-->
              <!--<li class="yui3-menuitem">
                <a class="yui3-menu-label" href="#">Party</a>
                <div id="about" class="yui3-menu">
                  <div class="yui3-menu-content">
                    <ul>                        
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=party-vendor">Vendor</a></li>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=party-customer">Customer</a></li>
                    </ul> 
                  </div>
                </div> 
              </li> -->
<!--            </ul>
          </div>
        </div>
      </li>-->
      <!-- Sales part ends here -->
    
      <!---------------------------NEW REPORT ----------------------------------------->
      <!-------------------------------------__CLAIM REPORT_------------------------------------->
     
     <li>
        <a class="yui3-menu-label" href="#">Claim Report</a>
        <div id="academics" class="yui3-menu">
          <div class="yui3-menu-content">
            <ul> 
             
          <li><a class="yui3-menuitem-content" href="index.php?option=user-claim-report">Distributor Claim</a></li>
          <li><a class="yui3-menuitem-content" href="index.php?option=claim-retailer-report">Retailer Claim</a></li>
          
            </ul>
          </div>
        </div>
      </li>
     
     <!------------------------------------------------------------------------------------------->
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
                        <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=user-attendence">User Daily Attendance</a></li>
                        <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=user-expanse-report">User Expense Report</a></li>
                     <!--   <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=user_daily_reporting">User Daily Report</a></li> daily_reporting table doesn't exit-->
                     <!--    <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=sales-team-dsr">Sales Team DSR</a></li> daily_reporting table doesn't exit-->
                        <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=sales-team-attendance">Sales Team/Man Attn.</a></li>
                        <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=attendance-summary">Attendance Summary</a></li>
                        <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=user-tracking-report">User Tracking Report</a></li>
                        <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=user-tracking-distance">User Tracking Distance</a></li>               
                        <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=sale-time-report">First Call Time Report</a></li>
                        <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=time-report">Time Report</a></li> 
                        <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=user-complaint">User Complaint</a></li>                      
                        <!--<li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=phone-status">Mobile On/Off Report</a></li> phone_status_details doesn't exit-->
                        <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=notification-non-contacted">Non Contacted Notification</a></li>
                    </ul> 
                     </div>
                </div> 
                </li> 
                <li class="yui3-menuitem">
                <a class="yui3-menu-label" href="#">Sale Reports</a>
                <div id="about" class="yui3-menu">
                <div class="yui3-menu-content">
                    <ul>
                    <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=user-daily-sales-report">User Daily Sales</a></li>
                    <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=user-sales-report">Sku Wise Report</a></li>
                    <!--<li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=division-sale-summary">Div.Sales Summary</a></li> Division not in ds group-->
                    <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=sales-man-secondary">Sales Man Secondary</a></li>                   
                    <!--<li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=depot-ss-report">Depot wise SS Sale</a></li> Depot table doen't exit -->
                    <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=advance-summary-report">Advance Summary</a></li>
                    <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=rds-wise">RDS Wise Sale</a></li> 
                    <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=dsr-monthly">DSR monthly</a></li>
                    <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=classification-wise-sales">Classification Wise</a></li>
                    <!--<li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=dealer_counter_sale">Dealer Counter Sale</a></li> dealer_counter_sale table doesn't exit-->
                    </ul> 
                  </div>
                </div> 
              </li>  
                <li class="yui3-menuitem">
                <a class="yui3-menu-label" href="#">Other Reports</a>
                <div id="about" class="yui3-menu">
                <div class="yui3-menu-content">
                    
                    <ul>
                        <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=monthly-tour-plan">Monthly Tour Plan</a></li>
                        <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=detail-report-advanced">Detail Report Advanced</a></li>  
                        <!--<li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=dealer-balance-stock">Dist. Balance Stock</a></li> dealer_bal_stock table doesn't exit-->
                        <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=damage-replace">Damage Report</a></li>
                        <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=no-attendance">No Attendance</a></li>
                        <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=no-sale">No Booking</a></li>
                    </ul> 
                  </div>
                </div> 
              </li> 
            </ul>
                        </div>
                    </div>
        </li>
           
      <!-- Report part ends here -->
      
      
      
      <!----------------------------------------------------------------------------------->
      <!-- MISCELLANEOUS part start here -->
      <li>
        <a class="yui3-menu-label" href="#">Miscellaneous</a>
        <div id="academics" class="yui3-menu">
          <div class="yui3-menu-content">
            <ul> 
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=change-password">Change Password</a></li>
              <?php if($rol_id == 1){ ?>
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=circular">Circular Detail</a></li>
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=circular-report">Circular Report</a></li>
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
