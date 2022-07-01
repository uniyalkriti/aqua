<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
     @yield('title')
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('msell-favicon/favicon.ico')}}"> 
    <link rel="icon" type="image/png" href="{{asset('msell-favicon/favicon-32x32.png')}}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{asset('msell-favicon/android-icon-48x48.png')}}" sizes="48x48"> 

    <meta name="description" content="top menu &amp; navigation" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

    <!-- bootstrap & fontawesome -->
    <link rel="stylesheet" href="{{asset('nice/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{asset('nice/font-awesome/4.5.0/css/font-awesome.min.css')}}" />

    <!-- page specific plugin styles -->

    <!-- text fonts -->
    <link rel="stylesheet" href="{{asset('nice/css/fonts.googleapis.com.css')}}" />

    <!-- ace styles -->
    <link rel="stylesheet" href="{{asset('nice/css/ace.min.css')}}" class="ace-main-stylesheet" id="main-ace-style" />

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="{{asset('nice/css/ace-part2.min.css')}}" class="ace-main-stylesheet" />
    <![endif]-->
    <link rel="stylesheet" href="{{asset('nice/css/ace-skins.min.css')}}" />
    <link rel="stylesheet" href="{{asset('nice/css/ace-rtl.min.css')}}" />

    @yield('css')

    <script src="{{asset('nice/js/ace-extra.min.js')}}"></script>
    <script>

        if (location.hostname == "localhost" || location.hostname == "162.213.190.125") {
            var domain = '{{Request::root()}}';
        }
        else {
            var domain = location.protocol + '//' + location.hostname + (location.port ? ':' + location.port : '');
        }
    </script>
</head>

<body class="no-skin">
<div id="navbar" class="navbar navbar-default          ace-save-state">
    <div class="navbar-container ace-save-state" id="navbar-container">
        <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
            <span class="sr-only">Toggle sidebar</span>

            <span class="icon-bar"></span>

            <span class="icon-bar"></span>

            <span class="icon-bar"></span>
        </button>

        <div class="navbar-header pull-left">
            <a href="#" class="navbar-brand">
                <small>
                    <img class="nav-user-photo" src="{{asset('msell/images/logo.jpg')}}" style="height:30px; width:80px;" alt="mSELL">
                    {{Lang::get('common.project-name')}}
                </small>
            </a>
        </div>

        <div class="navbar-buttons navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav">

                <li class="light-blue dropdown-modal">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <img class="nav-user-photo" src="{{asset('msell/images/avatars/avatar2.png')}}"
                             alt="Jason's Photo"/>
                        <span class="user-info">
									{{--<small>Welcome</small>--}}
                            {{--Jason--}}
								</span>

                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>

                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                       <!--  <li>
                            <a href="#">
                                <i class="ace-icon fa fa-cog"></i>
                                Settings
                            </a>
                        </li>

                        <li>
                            <a href="profile.html">
                                <i class="ace-icon fa fa-user"></i>
                                Profile
                            </a>
                        </li> -->

                        <!-- <li class="divider"></li> -->

                        <li>
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                <i class="ace-icon fa fa-power-off"></i>
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div><!-- /.navbar-container -->
</div>

<div class="main-container ace-save-state" id="main-container">
    <script type="text/javascript">
        try{ace.settings.loadState('main-container')}catch(e){}
    </script>

    <div id="sidebar" class="sidebar h-sidebar navbar-collapse collapse ace-save-state">
        <script type="text/javascript">
            try{ace.settings.loadState('sidebar')}catch(e){}
        </script>

   
        <!-- /.sidebar-shortcuts -->

       
    </div>

    @yield('body')
    <!-- /.main-content -->

    <div class="footer">
        <div class="footer-inner">
            <div class="footer-content">
						<span class="bigger-100">
                                <img class="nav-user-photo" src="{{asset('msell/images/logo_msell.png')}}" style="height:40px; width:40px;" alt="mSELL">
							<span class="blue bolder">mSELL,</span>
							Developed & Powered by Manacle Technologies Pvt. Ltd.
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
<script src="{{asset('nice/js/jquery-2.1.4.min.js')}}"></script>

<!-- <![endif]-->

<!--[if IE]>
<script src="{{asset('nice/js/jquery-1.11.3.min.js')}}"></script>
<![endif]-->
<script type="text/javascript">
    if('ontouchstart' in document.documentElement) document.write("<script src='{{asset('nice/js/jquery.mobile.custom.min.js')}}'>"+"<"+"/script>");
</script>
<script src="{{asset('nice/js/bootstrap.min.js')}}"></script>


<!-- scripts -->
<script src="{{asset('nice/js/ace-elements.min.js')}}"></script>
<script src="{{asset('nice/js/ace.min.js')}}"></script>
<script src="{{asset('nice/js/common.js')}}"></script>

<!-- inline scripts related to this page -->
@yield('js')

</body>
</html>
