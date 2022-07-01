<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta charset="utf-8"/>
    @yield('title')
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('/msell/images/logo_msell.png')}}">
    <link rel="icon" type="image/png" href="{{asset('/msell/images/logo_msell.png')}}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{asset('/msell/images/logo_msell.png')}}" sizes="48x48">

    <meta name="description" content="top menu &amp; navigation"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>

    <!-- bootstrap & fontawesome -->
    <link rel="stylesheet" href="{{asset('/msell/css/bootstrap.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('/msell/font-awesome/4.5.0/css/font-awesome.min.css')}}"/>

    <!-- page specific plugin styles -->

    <!-- text fonts -->
    <link rel="stylesheet" href="{{asset('/msell/css/fonts.googleapis.com.css')}}"/>

    <!-- ace styles -->
    <link rel="stylesheet" href="{{asset('/msell/css/ace.min.css')}}" class="ace-main-stylesheet" id="main-ace-style"/>

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="{{asset('/msell/css/ace-part2.min.css')}}" class="ace-main-stylesheet"/>
    <![endif]-->
    <link rel="stylesheet" href="{{asset('/msell/css/ace-skins.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('/msell/css/ace-rtl.min.css')}}"/>

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="{{asset('/msell/css/ace-ie.min.css')}}"/>
    <![endif]-->

    @yield('css')

<!-- ace settings handler -->
    <script src="{{asset('/msell/js/ace-extra.min.js')}}"></script>
    <script>

        if (location.hostname == "localhost") {
            var domain = '{{Request::root()}}';
        }
        else {
            var domain = location.protocol + '//' + '162.213.190.125/gopal-reports';
//            var domain = location.protocol + '//' + location.hostname + (location.port ? ':' + location.port : '');
        }
        //alert(domain);
    </script>

    {{--<!--[if lte IE 8]>--}}
    {{--<script src="msell/js/html5shiv.min.js"></script>--}}
    {{--<script src="msell/js/respond.min.js"></script>--}}
    {{--<![endif]-->--}}
<style>
        @import url(https://fonts.googleapis.com/css?family=Droid+Sans);
        #loader {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url('http://www.downgraf.com/wp-content/uploads/2014/09/01-progress.gif?e44397') 50% 50% no-repeat rgb(249,249,249);
        }

        body{
            font-family: 'Droid Sans', sans-serif;
            background: rgba(170,179,86,1);
            background: -moz-linear-gradient(left, rgba(170,179,86,1) 0%, rgba(134,145,28,0.91) 60%, rgba(124,136,12,0.91) 77%);
            background: -webkit-gradient(left top, right top, color-stop(0%, rgba(170,179,86,1)), color-stop(60%, rgba(134,145,28,0.91)), color-stop(77%, rgba(124,136,12,0.91)));
            background: -webkit-linear-gradient(left, rgba(170,179,86,1) 0%, rgba(134,145,28,0.91) 60%, rgba(124,136,12,0.91) 77%);
            background: -o-linear-gradient(left, rgba(170,179,86,1) 0%, rgba(134,145,28,0.91) 60%, rgba(124,136,12,0.91) 77%);
            background: -ms-linear-gradient(left, rgba(170,179,86,1) 0%, rgba(134,145,28,0.91) 60%, rgba(124,136,12,0.91) 77%);
            background: linear-gradient(to right, rgba(170,179,86,1) 0%, rgba(134,145,28,0.91) 60%, rgba(124,136,12,0.91) 77%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#aab356', endColorstr='#7c880c', GradientType=1 );

        }
        #box{
            width:45%;
            background: rgba(226,226,226,1);
            background: -moz-linear-gradient(left, rgba(226,226,226,1) 0%, rgba(219,219,219,1) 10%, rgba(209,209,209,1) 98%, rgba(254,254,254,1) 100%);
            background: -webkit-gradient(left top, right top, color-stop(0%, rgba(226,226,226,1)), color-stop(10%, rgba(219,219,219,1)), color-stop(98%, rgba(209,209,209,1)), color-stop(100%, rgba(254,254,254,1)));
            background: -webkit-linear-gradient(left, rgba(226,226,226,1) 0%, rgba(219,219,219,1) 10%, rgba(209,209,209,1) 98%, rgba(254,254,254,1) 100%);
            background: -o-linear-gradient(left, rgba(226,226,226,1) 0%, rgba(219,219,219,1) 10%, rgba(209,209,209,1) 98%, rgba(254,254,254,1) 100%);
            background: -ms-linear-gradient(left, rgba(226,226,226,1) 0%, rgba(219,219,219,1) 10%, rgba(209,209,209,1) 98%, rgba(254,254,254,1) 100%);
            background: linear-gradient(to right, rgba(226,226,226,1) 0%, rgba(219,219,219,1) 10%, rgba(209,209,209,1) 98%, rgba(254,254,254,1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e2e2e2', endColorstr='#fefefe', GradientType=1 );

        }
    </style>


</head>

<body class="no-skin">
<div class="loader" id="loader"></div>

<script type="text/javascript">
    $('.navbar-brand').onclick(function() {
        $(".loader").fadeIn("slow");
    });
    </script>
<div id="navbar" class="navbar navbar-default    navbar-collapse       h-navbar ace-save-state">
    <div class="navbar-container ace-save-state" id="navbar-container">
        <div class="navbar-header pull-left">
            <a href="{{url('home')}}" class="navbar-brand">
                <small>
                    <img src="{{asset('msell/images/gopal.jpg')}}" width="40px">
                    Role: {{Auth::user()->id==1?'Admin':'User'}}
                </small>
            </a>

            <button class="pull-right navbar-toggle navbar-toggle-img collapsed" type="button" data-toggle="collapse"
                    data-target=".navbar-buttons,.navbar-menu">
                <span class="sr-only">Toggle user menu</span>

                <img src="{{asset('msell/images/avatars/user.jpg')}}" alt="Jason's Photo"/>
            </button>

            <button class="pull-right navbar-toggle collapsed" type="button" data-toggle="collapse"
                    data-target="#sidebar">
                <span class="sr-only">Toggle sidebar</span>

                <span class="icon-bar"></span>

                <span class="icon-bar"></span>

                <span class="icon-bar"></span>
            </button>
        </div>

        <div class="navbar-buttons navbar-header pull-right  collapse navbar-collapse" role="navigation">
            <ul class="nav ace-nav">

                <li class="light-blue dropdown-modal user-min">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <img class="nav-user-photo" src="{{asset('msell/images/avatars/avatar2.png')}}"
                             alt="Jason's Photo"/>
                        {{strtoupper(Auth::user()->email)}}
                        <span class="user-info">
									<small>Welcome</small>
                            {{--Jason--}}
								</span>

                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>

                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        <li>
                            <a href="#">
                                <i class="ace-icon fa fa-cog"></i>
                                Settings
                            </a>
                        </li>

                        {{--<li>--}}
                        {{--<a href="profile.html">--}}
                        {{--<i class="ace-icon fa fa-user"></i>--}}
                        {{--Profile--}}
                        {{--</a>--}}
                        {{--</li>--}}

                        <li class="divider"></li>

                        <li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                  style="display: none;">{{ csrf_field() }}</form>
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="ace-icon fa fa-power-off"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <nav role="navigation" class="navbar-menu pull-left collapse navbar-collapse">
            {{--<ul class="nav navbar-nav">--}}
            {{--<li>--}}
            {{--<a href="#" class="dropdown-toggle" data-toggle="dropdown">--}}
            {{--Overview--}}
            {{--&nbsp;--}}
            {{--<i class="ace-icon fa fa-angle-down bigger-110"></i>--}}
            {{--</a>--}}

            {{--<ul class="dropdown-menu dropdown-light-blue dropdown-caret">--}}
            {{--<li>--}}
            {{--<a href="#">--}}
            {{--<i class="ace-icon fa fa-eye bigger-110 blue"></i>--}}
            {{--Monthly Visitors--}}
            {{--</a>--}}
            {{--</li>--}}

            {{--<li>--}}
            {{--<a href="#">--}}
            {{--<i class="ace-icon fa fa-user bigger-110 blue"></i>--}}
            {{--Active Users--}}
            {{--</a>--}}
            {{--</li>--}}

            {{--<li>--}}
            {{--<a href="#">--}}
            {{--<i class="ace-icon fa fa-cog bigger-110 blue"></i>--}}
            {{--Settings--}}
            {{--</a>--}}
            {{--</li>--}}
            {{--</ul>--}}
            {{--</li>--}}

            {{--<li>--}}
            {{--<a href="#">--}}
            {{--<i class="ace-icon fa fa-envelope"></i>--}}
            {{--Messages--}}
            {{--<span class="badge badge-warning">5</span>--}}
            {{--</a>--}}
            {{--</li>--}}
            {{--</ul>--}}

            {{--<form class="navbar-form navbar-left form-search" role="search">--}}
            {{--<div class="form-group">--}}
            {{--<input type="text" placeholder="search"/>--}}
            {{--</div>--}}

            {{--<button type="button" class="btn btn-mini btn-info2">--}}
            {{--<i class="ace-icon fa fa-search icon-only bigger-110"></i>--}}
            {{--</button>--}}
            {{--</form>--}}
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
                ace.settings.loadState('sidebar')
            } catch (e) {
            }
        </script>

        <div class="sidebar-shortcuts" id="sidebar-shortcuts">
            <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
                <button class="btn btn-success">
                    <i class="ace-icon fa fa-signal"></i>
                </button>

                <button class="btn btn-info">
                    <i class="ace-icon fa fa-pencil"></i>
                </button>

                <button class="btn btn-warning">
                    <i class="ace-icon fa fa-users"></i>
                </button>

                <button class="btn btn-danger">
                    <i class="ace-icon fa fa-cogs"></i>
                </button>
            </div>

            <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
                <span class="btn btn-success"></span>

                <span class="btn btn-info"></span>

                <span class="btn btn-warning"></span>

                <span class="btn btn-danger"></span>
            </div>
        </div>
        <!-- /.sidebar-shortcuts -->
        <ul class="nav nav-list">
        <li class="hover"><a href="{{url('user')}}"><i class="menu-icon fa fa-users"></i> Users Master</a><b class="arrow"></b></li>
            <li class="hover pull_up">
                <a href="{{url('home')}}" class="dropdown-toggle">
                    <i class="menu-icon fa fa-file"></i>
                    <span class="menu-text">
								Reports
							</span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu ace-scroll can-scroll" style="top: -133px; max-height: 247px;">
                    <li class="hover">
                        <a href="{{url('beat-route')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Beat Route
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('market-beat-plan')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Market Beat Plan
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('daily-attendance')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Daily Attendance
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('daily-performance')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Day Wise Performance
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('payment-details')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Payment Received Details
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('aging')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Aging
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('distributor-stock-status')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Distributor Stock Staus
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('stock-in-hand')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Stock in hand
                        </a>
                        <b class="arrow"></b>
                    </li>
                    {{--<li class="hover">--}}
                        {{--<a href="{{url('rs-wise-secondary-sales')}}">--}}
                            {{--<i class="menu-icon fa fa-caret-right"></i>--}}
                            {{--RS wise Secondary Sales--}}
                        {{--</a>--}}
                        {{--<b class="arrow"></b>--}}
                    {{--</li>--}}
                    <li class="hover">
                        <a href="{{url('outlet-opening-status')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Outlet Opening Status
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('tour-program')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Tour Program
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('isr-so-tgt-month')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            ISR WISE / SO WISE TGT MONTH
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
                <div class="scroll-track scroll-detached no-track scroll-thin scroll-margin scroll-visible scroll-active"
                     style="display: none; height: 246px; top: -132px; left: 185px;">
                    <div class="scroll-bar" style="height: 55px; top: 0px;"></div>
                </div>
            </li>
            <li class="hover pull_up">
                <a href="{{url('home')}}" class="dropdown-toggle">
                    <i class="menu-icon fa fa-area-chart"></i>
                    <span class="menu-text">
								Forms
							</span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu ace-scroll can-scroll" style="top: -133px; max-height: 247px;">
                    <li class="hover">
                        <a href="{{url('new-sd-dist-prospecting')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            NEW DISTRIBUTOR /SUPER DISTRIBUTOR/ SUB DISTRIBUTOR PROSPECTING
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('competitors-new-product')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Competitors New Product
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('product-investigation')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Product Investigation
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('competitive-price-intelligence')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Competitive Price Intelligence
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('feedback')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Feedback
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('pending-claim')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Pending Claim
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('travelling-expenses')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Travelling Expenses Bill
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('complaint-report')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Complaint
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
                <div class="scroll-track scroll-detached no-track scroll-thin scroll-margin scroll-visible scroll-active"
                     style="display: none; height: 246px; top: -132px; left: 185px;">
                    <div class="scroll-bar" style="height: 55px; top: 0px;"></div>
                </div>
            </li>
            <li class="hover pull_up">
                <a href="{{url('home')}}" class="dropdown-toggle">
                    <i class="menu-icon fa fa-bolt"></i>
                    <span class="menu-text">
								Advance Reports
							</span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu ace-scroll can-scroll" style="top: -133px; max-height: 247px;">
                    <li class="hover">
                        <a href="{{url('month_s_primary_and_secondary_sales_plan')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            MONTH PRIMARY AND SECONDARY SALES PLAN
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('ucdp')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            UCDP
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('board-review')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Board Review
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('rsm-asm-so-performance')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            RSM ASM SO Performance Review
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('distributor-performance')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Distributor Performance Review
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('distributer-wise-scondary-sales-trends')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Sales Trends
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('sales-review')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            ASM &amp; above Sales Review
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
                <div class="scroll-track scroll-detached no-track scroll-thin scroll-margin scroll-visible scroll-active"
                     style="display: none; height: 246px; top: -132px; left: 185px;">
                    <div class="scroll-bar" style="height: 55px; top: 0px;"></div>
                </div>
            </li>
            <li class="hover pull_up">
                <a href="{{url('home')}}" class="dropdown-toggle">
                    <i class="menu-icon fa fa-bar-chart "></i>
                    <span class="menu-text">
								Extra Reports
							</span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu ace-scroll can-scroll" style="top: -133px; max-height: 247px;">
                    <li class="hover">
                        <a href="{{url('distributer-stock-report')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Distributor Stock Report
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('user-sales-summary')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            State Wise Secondary Sales
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('state-wise-scondary-sales-trends')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            STATE WISE PRODUCT WISE SECONDARY SALES SUMMARY
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('statewise_user_eatos')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            STATE WISE USER WISE EATOS & AGARBATTI REPORT
                        </a>
                        <b class="arrow"></b>
                    </li>
                     <li class="hover">
                        <a href="{{url('tablet_working_status')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            TABLET WORKING STATUS
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
              
                <div class="scroll-track scroll-detached no-track scroll-thin scroll-margin scroll-visible scroll-active"
                     style="display: none; height: 246px; top: -132px; left: 185px;">
                    <div class="scroll-bar" style="height: 55px; top: 0px;"></div>
                </div>
            </li>
        </ul>

        {{--<ul class="nav nav-list">--}}
        {{--@if(!empty($menu))--}}
        {{--@foreach($menu as $item)--}}
        {{--<li class="hover {{isset($current_menu) && $item->title_name==$current_menu?'active open':''}}">  --}}{{--add class='active open' for active parent--}}
        {{--<a href="{{url($item->path)}}" class="dropdown-toggle">--}}
        {{--<i class="{{$item->image_name}}"></i>--}}
        {{--<span class="menu-text">--}}
        {{--{{$item->title_name}}--}}
        {{--</span>--}}

        {{--<b class="arrow fa fa-angle-down"></b>--}}
        {{--</a>--}}

        {{--<b class="arrow"></b>--}}

        {{--<ul class="submenu">--}}
        {{--@if(!empty($item->submenu ))--}}
        {{--@foreach($item->submenu as $subitem)--}}
        {{--@php--}}
        {{--if($subitem->status!=1){continue;}--}}
        {{--@endphp--}}
        {{--<li class="hover"> --}}{{--add class='active open' for active child--}}
        {{--<a href="{{url($subitem->path)}}">--}}
        {{--<i class="menu-icon fa fa-caret-right"></i>--}}
        {{--{{$subitem->title_name}}--}}
        {{--</a>--}}
        {{--<b class="arrow"></b>--}}
        {{--</li>--}}
        {{--@endforeach--}}
        {{--@endif--}}
        {{--</ul>--}}
        {{--</li>--}}
        {{--@endforeach--}}
        {{--@endif--}}

        {{--</ul>--}}
    </div>

@yield('body')
<!-- /.main-content -->

    <div class="footer">
        <div class="footer-inner">
            <div class="footer-content">
						<span class="bigger-120" style="color:#FF9C03;font-style:italic">
							<span class="" style="cololr:#FF9C03">mSELL,</span>
							developed and powered by Manacle Technologies Pvt. Ltd.
						</span>
            </div>
        </div>
    </div>

    <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
        <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
    </a>
</div><!-- /.main-container -->

<!-- basic scripts -->

<!--[if !IE]> -->
<script src="{{asset('msell/js/jquery-2.1.4.min.js')}}"></script>

<!-- <![endif]-->

<!--[if IE]>
<script src="{{asset('msell/js/jquery-1.11.3.min.js')}}"></script>
<![endif]-->
<script type="text/javascript">
    if ('ontouchstart' in document.documentElement) document.write("<script src='msell/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
</script>
<script src="{{asset('msell/js/bootstrap.min.js')}}"></script>

<!-- page specific plugin scripts -->

<!-- ace scripts -->
<script src="{{asset('msell/js/ace-elements.min.js')}}"></script>
<script src="{{asset('msell/js/ace.min.js')}}"></script>

<!-- inline scripts related to this page -->
<script type="text/javascript">
    jQuery(function ($) {
        var $sidebar = $('.sidebar').eq(0);
        if (!$sidebar.hasClass('h-sidebar')) return;

        $(document).on('settings.ace.top_menu', function (ev, event_name, fixed) {
            if (event_name !== 'sidebar_fixed') return;

            var sidebar = $sidebar.get(0);
            var $window = $(window);

            //return if sidebar is not fixed or in mobile view mode
            var sidebar_vars = $sidebar.ace_sidebar('vars');
            if (!fixed || ( sidebar_vars['mobile_view'] || sidebar_vars['collapsible'] )) {
                $sidebar.removeClass('lower-highlight');
                //restore original, default marginTop
                sidebar.style.marginTop = '';

                $window.off('scroll.ace.top_menu')
                return;
            }


            var done = false;
            $window.on('scroll.ace.top_menu', function (e) {

                var scroll = $window.scrollTop();
                scroll = parseInt(scroll / 4);//move the menu up 1px for every 4px of document scrolling
                if (scroll > 17) scroll = 17;


                if (scroll > 16) {
                    if (!done) {
                        $sidebar.addClass('lower-highlight');
                        done = true;
                    }
                }
                else {
                    if (done) {
                        $sidebar.removeClass('lower-highlight');
                        done = false;
                    }
                }

                sidebar.style['marginTop'] = (17 - scroll) + 'px';
            }).triggerHandler('scroll.ace.top_menu');

        }).triggerHandler('settings.ace.top_menu', ['sidebar_fixed', $sidebar.hasClass('sidebar-fixed')]);

        $(window).on('resize.ace.top_menu', function () {
            $(document).triggerHandler('settings.ace.top_menu', ['sidebar_fixed', $sidebar.hasClass('sidebar-fixed')]);
        });


    });
</script>
<script>
    $(window).load(function(){
        $('#loader').fadeOut();
    });
</script>
@yield('js')

</body>
</html>
