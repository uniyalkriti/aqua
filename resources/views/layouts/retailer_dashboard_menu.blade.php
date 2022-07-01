<div id="sidebar" class="sidebar responsive ace-save-state">
    <ul class="nav nav-list">
        <li class="active">
            <a href="{{url('retailer/'.$id)}}">
                <i class="menu-icon fa fa-signal"></i>
                <span class="menu-text">  Dashboard </span>
            </a>

            <b class="arrow"></b>
        </li>

        <li class="active1">
            <a href="{{url('retailer_stock_dashboard/'.$id)}}">
                <i class="menu-icon fa fa-tablet"></i>
                <span class="menu-text">  Stock  </span>
            </a>
        </li>
        <li class="active1">
            <a href="{{url('merchandise_dashboard/'.$id)}}">
                <i class="menu-icon fa fa-gift"></i>
                <span class="menu-text">  Merchandise </span>
            </a>
        </li>
        <li class="active1">
            <a href="{{url('retailer_order_booking/'.$id)}}">
                <i class="menu-icon fa fa-rupee"></i>
                <span class="menu-text">  Order Booking </span>
            </a>
        </li>
        <li class="active1">
            <a href="{{url('retailer_payment_collection/'.$id)}}">
                <i class="menu-icon fa fa-rupee"></i>
                <span class="menu-text">  Payment Collection </span>
            </a>
        </li>
        <li class="active1">
            <a href="{{url('promotional_request/'.$id)}}">
                <i class="menu-icon fa fa-paper-plane "></i>
                <span class="menu-text"> Promotional Request </span>
            </a>
        </li>
        <li class="active1">
            <a href="{{url('retailer_comment/'.$id)}}">
                <i class="menu-icon fa fa-comments-o "></i>
                <span class="menu-text"> Retailer Comment </span>
            </a>
        </li>
        <li class="active1">
            <a href="{{url('rds_dashboard/'.$id)}}">
                <i class="menu-icon fa fa-shopping-cart "></i>
                <span class="menu-text"> RDS Wise Sale </span>
            </a>
        </li>


    </ul><!-- /.nav-list -->

    <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
        <i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
    </div>
</div>
