<div id="sidebar" class="sidebar responsive ace-save-state">
    <ul class="nav nav-list">
        <li class="active">
            <a href="{{url('distributor/'.$id)}}">
                <i class="menu-icon fa fa-signal"></i>
                <span class="menu-text">  Dashboard </span>
            </a>

            <b class="arrow"></b>
        </li>

        <li class="">
            <a href="{{url('stock/'.$id)}}">
                <i class="menu-icon fa fa-list-alt"></i>
                <span class="menu-text"> Current Stock </span>
            </a>

            <b class="arrow"></b>
        </li>

        <!-- <li class="">
            <a href="{{url('purchase/'.$id)}}">
                <i class="menu-icon fa fa-list"></i>
                <span class="menu-text"> Purchase</span>
            </a>

            <b class="arrow"></b>
        </li> -->

       <!--  <li class="">
            <a href="{{url('challan/'.$id)}}">
                <i class="menu-icon fa fa-cart-arrow-down"></i>
                <span class="menu-text"> Sale</span>
            </a>

            <b class="arrow"></b>
        </li> -->
        <li class="">
            <a href="{{url('primarSaleDashboard/'.$id)}}">
                <i class="menu-icon fa fa-rupee"></i>
                <span class="menu-text">Primary Sale</span>
            </a>

            <b class="arrow"></b>
        </li>

        <!-- <li class="">
            <a href="{{url('threshold/'.$id)}}">
                <i class="menu-icon fa fa-opencart"></i>
                <span class="menu-text"> Threshold</span>
            </a>

            <b class="arrow"></b>
        </li>

        <li class="">
            <a href="{{url('paymentLedger/'.$id)}}">
                <i class="menu-icon fa fa-pencil-square-o"></i>
                <span class="menu-text">Payment Ledger</span>
            </a>

            <b class="arrow"></b>
        </li> -->
        <li class="">
            <a href="{{url('saleTrend/'.$id)}}">
                <i class="menu-icon fa fa-map-o"></i>
                <span class="menu-text"> Sale Trend</span>
            </a>

            <b class="arrow"></b>
        </li>

        <li class="">
            <a href="{{url('payment_collection/'.$id)}}">
                <i class="menu-icon fa fa-rupee"></i>
                <span class="menu-text"> Payment Collection</span>
            </a>

            <b class="arrow"></b>
        </li>

        <li class="">
            <a href="{{url('return_dashboard/'.$id)}}">
                <i class="menu-icon fa fa-rupee"></i>
                <span class="menu-text">Return</span>
            </a>

            <b class="arrow"></b>
        </li>


       <!--  <li class="">
            <a href="{{url('booking/'.$id)}}">
                <i class="menu-icon fa fa-calendar-check-o"></i>
                <span class="menu-text"> Order Booking </span>
            </a>

            <b class="arrow"></b>
        </li> -->


    </ul><!-- /.nav-list -->

    <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
        <i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
    </div>
</div>
