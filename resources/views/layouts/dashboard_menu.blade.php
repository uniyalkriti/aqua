<div id="sidebar" class="sidebar responsive ace-save-state">
    <ul class="nav nav-list">
       

        <li class="active">
            <a href="{{url('user/'.$id)}}">
                <i class="menu-icon fa fa-signal"></i>
                <span class="menu-text"> User Dashboard </span>
            </a>

            <b class="arrow"></b>
        </li>

        <li class="">
            <a href="{{url('user_attendance/'.$id)}}">
                <i class="menu-icon fa fa-map-marker"></i>
                <span class="menu-text"> User Attendance </span>
            </a>

            <b class="arrow"></b>
        </li>


        <li class="">
            <a href="{{url('user_tracking/'.$id)}}">
                <i class="menu-icon fa fa-map-o"></i>
                <span class="menu-text">  Tracking</span>
            </a>

            <b class="arrow"></b>
        </li>


        <li class="">
            <a href="{{url('booking/'.$id)}}">
                <i class="menu-icon fa fa-cart-arrow-down"></i>
                <span class="menu-text"> Secondary Sale </span>
            </a>

            <b class="arrow"></b> 
        </li>
        <li class="">
            <a href="{{url('primaryBooking/'.$id)}}">
                <i class="menu-icon fa fa-cart-arrow-down"></i>
                <span class="menu-text"> Primary Sale </span>
            </a>

            <b class="arrow"></b> 
        </li>
        <li class="">
                <a href="{{url('reporting/'.$id)}}">
                    <i class="menu-icon fa fa-calendar-check-o"></i>
                    <span class="menu-text"> Daily Reporting </span>
                </a>
    
                <b class="arrow"></b>
            </li>
    
            <li class="">
                <a href="{{url('mtp/'.$id)}}">
                    <i class="menu-icon fa fa-motorcycle"></i>
                    <span class="menu-text">MonthlyTourProgram</span>
                </a>
    
                <b class="arrow"></b>
            </li>
             <li class="">
                <a href="{{url('expense/'.$id)}}">
                    <i class="menu-icon fa fa-cart-arrow-down"></i>
                    <span class="menu-text">Travelling Expense</span>
                </a>
    
                <b class="arrow"></b>
            </li>
            
            <!-- forms parts starts here  -->
                    <li class="">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-pencil-square-o"></i>
                            <span class="menu-text"> Analysis Reporting </span>

                            <b class="arrow fa fa-angle-down"></b>
                        </a>

                        <b class="arrow"></b>

                        <ul class="submenu">
                            <li class="">
                                <a href="{{url('product_investigation/'.$id)}}">
                                    <i class="menu-icon fa fa-tablet"></i>
                                    <span class="menu-text">Product Investigation</span>
                                </a>
                    
                                <b class="arrow"></b>
                            </li>
                            <li class="">
                                <a href="{{url('Competitors_Product/'.$id)}}">
                                    <i class="menu-icon fa fa-tablet"></i>
                                    <span class="menu-text">Competetior Product</span>
                                </a>
                    
                                <b class="arrow"></b>
                            </li>
                            <li class="">
                                <a href="{{url('daily_prospecting/'.$id)}}">
                                    <i class="menu-icon fa fa-user"></i>
                                    <span class="menu-text">Daily Prospecting</span>
                                </a>
                    
                                <b class="arrow"></b>
                            </li>
                             <li class="">
                                <a href="{{url('competitive_price_intelligence/'.$id)}}">
                                    <i class="menu-icon fa fa-tablet"></i>
                                    <span class="menu-text">Competitive Price</span>
                                </a>
                    
                                <b class="arrow"></b>
                            </li>
                            <li class="">
                                <a href="{{url('feedackDashbord/'.$id)}}">
                                    <i class="menu-icon fa fa-pencil"></i>
                                    <span class="menu-text">Feedback</span>
                                </a>
                    
                                <b class="arrow"></b>
                            </li>
                            <li class="">
                                <a href="{{url('pending_claim/'.$id)}}">
                                    <i class="menu-icon fa fa-rupee"></i>
                                    <span class="menu-text">Pending Claim</span>
                                </a>
                    
                                <b class="arrow"></b>
                            </li>
                        
                        </ul>
                    </li>
                    <!-- forms parts ends here  -->

    </ul><!-- /.nav-list -->

    <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
        <i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
    </div>
</div>

