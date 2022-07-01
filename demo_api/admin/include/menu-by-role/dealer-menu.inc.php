<?php
//set_default_session_time();
?>
<div id="nav" class="yui3-menu yui3-menu-horizontal" role="menubar"><!-- Bounding box -->
  <div class="yui3-menu-content" ><!-- Content box -->
    <ul>
      <li id="_Alt_i"><a class="yui3-menu-label" href="index.php" id="myhome">Home</a></li>
      <li><a class="yui3-menu-label" href="index.php?option=company-add">Company</a></li>
      <!-- MASTER part start here -->
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
                    for($i = 1;$i<=$_SESSION[SESS.'catlevel'];$i++)
                    {
                       if($i >= 2 ) $option = 2;
                      // h1($_SESSION[SESS.'constant']["catalog_title_$i"]);
                    ?>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=catalog_<?php if(isset($option)) echo $option; else echo $i; ?>&mtype=<?php echo $i; ?>"><?php echo ucwords($_SESSION[SESS.'constant']["catalog_title_$i"]); ?></a></li>
                    <?php
                    }
                  } // if(!empty($_SESSION[SESS.'catlevel'])) end here
                    ?>
                  <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=catalog-product">Catalog Product</a></li> 
                  <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=primary-sale-details">Catalog Product Batch</a></li>
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
                    </ul> 
                  </div>
                </div> 
              </li>  
               <li class="yui3-menuitem">
                <a class="yui3-menu-label" href="#">Users</a>
                <div id="about" class="yui3-menu">
                  <div class="yui3-menu-content">
                    <ul>                        
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=add-dealer-user">User</a></li>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=dealer-multi-wise-location">Dealer Location Assign</a></li>
                       <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=retailer-add">Retailer</a></li>
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
     
      <!-- Inventory part ends here -->
      
      <!-- Challan part start here -->
<!--      <li>
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
    <li>
        <a class="yui3-menu-label" href="#">Sale Order</a>
        <div id="academics" class="yui3-menu">
          <div class="yui3-menu-content">
            <ul> 
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=sale-order-detailes">Sale Details</a></li>
               <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=dsp-challan-list">DSP/USER CHALLAN LIST</a></li>
<!--             <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="indexpop.php?option=direct-challan">Direct Challan</a></li>-->

            </ul>
          </div>
        </div>
      </li>
       <li>
        <a class="yui3-menu-label" href="#">Daily Dispatch</a>
        <div id="academics" class="yui3-menu">
          <div class="yui3-menu-content">
            <ul> 
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=daily-dispatch-details">Daily Dispatch Details</a></li>
            </ul>
          </div>
        </div>
      </li>
        <li>
        <a class="yui3-menu-label" href="#">Payment Collection</a>
        <div id="academics" class="yui3-menu">
          <div class="yui3-menu-content">
            <ul> 
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=payment-collection">Payment Collection</a></li>
            </ul>
          </div>
        </div>
      </li>
      
      <li>
        <a class="yui3-menu-label" href="#">Switch Company</a>
        <div id="academics" class="yui3-menu">
          <div class="yui3-menu-content">
            <ul> 
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=switch-company">Switch Company</a></li>
            </ul>
          </div>
        </div>
      </li>
      
        <li>
        <a class="yui3-menu-label" href="#">Reports</a>
        <div id="academics" class="yui3-menu">
          <div class="yui3-menu-content">
            <ul> 
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=balance-stock">Balance Stock</a></li>
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=party-wise-ledger-report">Party Wise Ledger Report</a></li>
            </ul>
          </div>
        </div>
      </li>
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
      
      <!-- Report part start here -->
<!--      <li>
        <a class="yui3-menu-label" href="#">Report</a>
        <div id="academics" class="yui3-menu">
          <div class="yui3-menu-content">
            <ul> 
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=user-attendence">User Daily Attendance</a></li>
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=monthly-tour-plan">Monthly Tour Plan</a></li>
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=user-sales-report">Sales Report</a></li>
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=user-expanse-report">User Expense Report</a></li>
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=user-tracking-report">User Tracking Report</a></li>-->
              <!--<li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=branch-staff-details">Branch Staff Details</a></li>-->
              <!--<li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=sale-order-details">Sale Order Details Report</a></li>-->
              <!--<li class="yui3-menuitem">
                <a class="yui3-menu-label" href="#">Stock</a>
                <div id="about" class="yui3-menu">
                  <div class="yui3-menu-content">
                    <ul>                        
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=stock-ledger">Stock Ledger</a></li>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=raw-material-stock">Raw Material Stock</a></li>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=raw-plate-stock">Raw Plates Stock</a></li>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=semi-finished-item">Semi-Finished Item</a></li>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=semi-finished-import">Semi-Finished Import Item</a></li>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=finished-good">Finish Good</a></li>
                    </ul>
                     
                  </div>
                </div> 
              </li>-->
              <!--<li class="yui3-menuitem">
                <a class="yui3-menu-label" href="#">Billing</a>
                <div id="about" class="yui3-menu">
                  <div class="yui3-menu-content">
                    <ul>
                      <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=itemwise-report">Item Wise Report</a></li>
              		  <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=powise-report">PO Wise Report</a></li>
                    </ul>                     
                  </div>
                </div> 
              </li>
              
              <li class="yui3-menuitem">
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
<!--           </ul>
          </div>
        </div>
      </li>-->
      <!-- Report part ends here -->
      
      <!-- MISCELLANEOUS part start here -->
      <li>
        <a class="yui3-menu-label" href="#">Miscellaneous</a>
        <div id="academics" class="yui3-menu">
          <div class="yui3-menu-content">
            <ul> 
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=change-password">Change Password</a></li>
<!--          <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=file-import">Import</a></li>-->
<!--              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=circular">Circular Detail</a></li>
              <li class="yui3-menuitem"><a class="yui3-menuitem-content" href="index.php?option=circular-report">Circular Report</a></li>-->
    
            </ul>
          </div>
        </div>
      </li>
      <!-- MISCELLANEOUS part ends here -->
            
      <li><a class="yui3-menu-label" href="index.php?option=logout">Log Out</a></li>                 
	</ul>	
  </div>
</div>	
