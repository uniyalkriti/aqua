<!DOCTYPE html>
<html lang="en">
<head>
    <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/> -->
    <meta charset="utf-8"/>
    <meta http-equiv="Conten-Security-Policy" content="upgrade-insecure-requests">
    
    @yield('title')
    <title>Aqualabindia || Manacle Technologies</title>
    <?php
    $userdataNew=Auth::user();


    $imageNew = DB::table('company')
            ->select('company_image','footer_message','footer_link')
            ->where('id',$userdataNew->company_id)
            ->first();


    ?>
<link href="https://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" rel="Stylesheet"></link>
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset($imageNew->company_image)}}">
    <link rel="icon" type="image/png" href="{{asset($imageNew->company_image)}}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{asset($imageNew->company_image)}}" sizes="48x48">

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
            // var domain = location.protocol + '//' + '162.213.190.125/gopal-reports';
           // var domain = location.protocol + '//' + location.hostname + (location.port ? ':' + location.port : '') + '/public';
           // var domain = '{{Request::root()}}';
           var domain = '{{Request::root()}}'.replace("https", "https");

        }
        // alert(domain);
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
            background: url('https://demo.msell.in/public/loader.svg') 50% 50% no-repeat rgb(249,249,249);
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
<!-- for dyanmic module starts here  -->
 @php
    $userdata=Auth::user();
    

    $data = DB::table('web_module')
            ->join('modules_bucket','modules_bucket.id','=','web_module.module_id')
            ->join('company_web_module_permission','company_web_module_permission.module_id','=','modules_bucket.id')
            ->select('modules_bucket.id as id','modules_bucket.title','company_web_module_permission.title as name','modules_bucket.icon')
            ->where('web_module.status',1)
            ->where('modules_bucket.status',1)
            ->where('web_module.company_id',$userdata->company_id)
            ->where('company_web_module_permission.role_id',$userdata->role_id)
            ->orderBy('web_module.sequence','ASC')
            ->get()->toArray();
    if(empty($data) && Auth::user()->is_admin==1)
    {
        $data = DB::table('web_module')
            ->join('modules_bucket','modules_bucket.id','=','web_module.module_id')
            ->select('modules_bucket.id as id','modules_bucket.title','web_module.title as name','modules_bucket.icon')
            ->where('web_module.status',1)
            ->where('modules_bucket.status',1)
            ->where('web_module.company_id',$userdata->company_id)
            ->orderBy('web_module.sequence','ASC')
            ->get()->toArray();

    }
    $custom_neha_herbals = DB::table('web_module')
                        ->join('modules_bucket','modules_bucket.id','=','web_module.module_id')
                        ->select('modules_bucket.id as id','modules_bucket.title','web_module.title as name','modules_bucket.icon')
                        ->where('web_module.status',1)
                        ->where('modules_bucket.status',1)
                        ->whereIn('modules_bucket.id',[33,35,8])
                        ->where('web_module.company_id',$userdata->company_id)
                        ->orderBy('web_module.sequence','ASC')
                        ->get();

     $image = DB::table('company')
            ->select('company_image','footer_message','footer_link')
            ->where('id',$userdata->company_id)
            ->first();
           
@endphp
<!-- for dyanmic module ends here  -->
<body class="no-skin">
<div class="loader" id="loader"></div>

<script type="text/javascript">
    $('.navbar-brand').onclick(function() {
        $(".loader").fadeIn("slow");
    });
    </script>

    
<?php 
if($userdata->role_id=='59') // only for manacle adminsitrateor check then remove it
{ 
    ?>
<div id="navbar" class="navbar navbar-default    navbar-collapse       h-navbar ace-save-state">
    <div class="navbar-container ace-save-state" id="navbar-container">
        <div class="navbar-header pull-left">
            <a href="{{url('home')}}" class="navbar-brand">
                @if(Auth::user()->company_id == '37')
                    @if(!empty($image->company_image))
                    <small>
                        <img src="{{asset($image->company_image)}}" width="160px">
                        Role: {{Auth::user()->is_admin==1?'Admin':'User'}}
                    </small>
                    @else
                    <small>
                        <img src="{{asset('msell/images/gopal.jpg')}}" width="160px">
                        Role: {{Auth::user()->is_admin==1?'Admin':'User'}}
                    </small>
                    @endif
                @else
                    @if(!empty($image->company_image))
                    <small>
                         <img src="{{asset($image->company_image)}}" width="100px"> 
                        Role: {{Auth::user()->is_admin==1?'Admin':'User'}}
                    </small>
                    @else
                    <small>
                        <img src="{{asset('msell/images/gopal.jpg')}}" width="40px">
                        Role: {{Auth::user()->is_admin==1?'Admin':'User'}}
                    </small>
                    @endif
                @endif
            </a>

            <button class="pull-right navbar-toggle navbar-toggle-img collapsed" type="button" data-toggle="collapse"
                    data-target=".navbar-buttons,.navbar-menu">
                <span class="sr-only">Toggle user menu</span>

                <img src="{{asset('msell/images/avatars/user.jpg')}}" alt=""/>
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
                             alt=""/>
                        {{strtoupper(Auth::user()->email)}}
                        <span class="user-info">
                                    <small>Welcome</small>
                            {{--Jason--}}
                                </span>

                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>

                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
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
                <a href="{{url('/home')}}">
                    <button class="btn btn-info" title="Home Dashboard">
                        <i class="ace-icon fa fa-home"></i>
                    </button>
                </a>
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
            <li class="hover">
                <a href="{{url('company')}}">
                    <i class="menu-icon fa fa-building-o"></i>
                    Company
                </a>
                <b class="arrow"></b>
            </li>
            
            @if($userdata->id != '5767')
                <li class="hover">
                    <a href="{{url('url')}}">
                        <i class="menu-icon fa fa-building-o"></i>
                        Interface URL
                    </a>
                    <b class="arrow"></b>
                </li>
                <li class=" open hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-map"></i>
                            Web Bucket Master
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu ace-scroll scroll-disabled" style="">
                        <li class="hover">
                            <a href="{{url('MainMenu')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Web Module Bucket
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="hover">
                            <a href="{{url('Menu')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Web Sub Module Bucket
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="hover">
                            <a href="{{url('SubMenus')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Web Sub Sub Module Bucket
                            </a>

                            <b class="arrow"></b>
                        </li>

                        
                    </ul><div class="scroll-track scroll-detached no-track scroll-thin scroll-margin scroll-visible" style="display: none; height: 199px; top: 1px; left: 368px;"><div class="scroll-bar" style="height: 182px; top: 0px;"></div></div>
                </li>
                <li class=" open hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-map"></i>
                            App Bucket Master
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu ace-scroll scroll-disabled" style="">
                        <li class="hover">
                            <a href="{{url('appModule')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                App Module Bucket
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="hover">
                            <a href="{{url('appSubModule')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                App Sub Module Bucket
                            </a>

                            <b class="arrow"></b>
                        </li>
                        

                        
                    </ul><div class="scroll-track scroll-detached no-track scroll-thin scroll-margin scroll-visible" style="display: none; height: 199px; top: 1px; left: 368px;"><div class="scroll-bar" style="height: 182px; top: 0px;"></div></div>
                </li>
                 <li class=" open hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-map"></i>
                            Assigned App Modification Master
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu ace-scroll scroll-disabled" style="">
                        <li class="hover">
                            <a href="{{url('editAppModule')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Assigned App Module 
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="hover">
                            <a href="{{url('editAppSubModule')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               Assigned App Sub Module 
                            </a>

                            <b class="arrow"></b>
                        </li>
                        

                        
                    </ul><div class="scroll-track scroll-detached no-track scroll-thin scroll-margin scroll-visible" style="display: none; height: 199px; top: 1px; left: 368px;"><div class="scroll-bar" style="height: 182px; top: 0px;"></div></div>
                </li>
            @endif
            <li class="hover">
                <a href="{{url('webAssigning')}}">
                    <i class="menu-icon fa fa-tasks "></i>
                    Web Assigning
                </a>
                <b class="arrow"></b>
            </li>
            <li class="hover">
                <a href="{{url('Modules')}}">
                    <i class="menu-icon fa fa-tasks "></i>
                    App Assigning
                </a>
                <b class="arrow"></b>
            </li>
            @if($userdata->id != '5767')
                <li class="hover">
                    <a href="{{url('urllist')}}">
                        <i class="menu-icon fa fa-tasks "></i>
                        Url Bucket List
                    </a>
                    <b class="arrow"></b>
                </li>
            @endif
            <li class="hover">
                <a href="{{url('urlassign')}}">
                    <i class="menu-icon fa fa-tasks "></i>
                    Url Assign
                </a>
                <b class="arrow"></b>
            </li>
              
            @if($userdata->id != '5767')
                <li class="hover">
                    <a href="{{url('version')}}">
                        <i class="menu-icon fa fa-tasks "></i>
                        Version
                    </a>
                    <b class="arrow"></b>
                </li>
                <li class="hover">
                    <a href="{{url('app_link_master')}}">
                        <i class="menu-icon fa fa-tasks "></i>
                        App Link
                    </a>
                    <b class="arrow"></b>
                </li>
            @endif

        </ul>
            
</div>
</div>


<?php } 


##############################for neha herbals
else if($userdata->role_id=='1' || $userdata->is_admin=='1' || $userdata->role_id=='50' || $userdata->is_admin=='0'){


    ?>
    <div id="navbar" class="navbar navbar-default    navbar-collapse       h-navbar ace-save-state">
    <div class="navbar-container ace-save-state" id="navbar-container">

        <div class="navbar-header pull-left">
            <a href="{{url('home')}}" class="navbar-brand">
                @if(Auth::user()->company_id == '37')
                    @if(!empty($image->company_image))
                    <small>
                        <img src="{{asset($image->company_image)}}" width="160px">
                        Role: {{Auth::user()->is_admin==1?'Admin':'User'}}
                    </small>
                    @else
                    <small>
                        <img src="{{asset('msell/images/gopal.jpg')}}" width="160px">
                        Role: {{Auth::user()->is_admin==1?'Admin':'User'}}
                    </small>
                    @endif
                @else
                    @if(!empty($image->company_image))
                    <small>
                         <img src="{{asset($image->company_image)}}" width="100px"> 
                        Role: {{Auth::user()->is_admin==1?'Admin':'User'}}
                    </small>
                    @else
                    <small>
                        <img src="{{asset('msell/images/gopal.jpg')}}" width="40px">
                        Role: {{Auth::user()->is_admin==1?'Admin':'User'}}
                    </small>
                    @endif
                @endif
            </a>

            <button class="pull-right navbar-toggle navbar-toggle-img collapsed" type="button" data-toggle="collapse"
                    data-target=".navbar-buttons,.navbar-menu">
                <span class="sr-only">Toggle user menu</span>

                <img src="{{asset('msell/images/avatars/user.jpg')}}" alt=""/>
            </button>

            <button class="pull-right navbar-toggle collapsed" type="button" data-toggle="collapse"
                    data-target="#sidebar">
                <span class="sr-only">Toggle sidebar</span>

                <span class="icon-bar"></span>

                <span class="icon-bar"></span>

                <span class="icon-bar"></span>
            </button>
        </div>
                <!-- <input type="text" placeholder="Search.." name="search" id="serch_url" style="width:290px;"> -->
                <!-- <meta name="csrf-token" content="{{ csrf_token() }}"> -->
                <!-- <button type="button" id="click_button" ><i class="fa fa-search"></i></button> -->
        <div class="navbar-buttons navbar-header pull-right  collapse navbar-collapse" role="navigation">
            <ul class="nav ace-nav">
                <li class="light-blue dropdown-modal user-min">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <img class="nav-user-photo" src="{{asset('msell/images/avatars/avatar2.png')}}"
                             alt=""/>
                        {{strtoupper(Auth::user()->email)}}
                        <span class="user-info">
                                    <small>Welcome</small>
                            {{--Jason--}}
                                </span>

                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>

                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
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
                @if(Auth::user()->is_admin==1)
                <a href="{{url('/home')}}">
                    <button class="btn btn-info" title="Home Dashboard">
                        <i class="ace-icon fa fa-home"></i>
                    </button>
                </a>
                <a href="{{url('/performanceDashboad')}}">
                    <button class="btn btn-danger" title="Performance Dashboard">
                        <i class="ace-icon fa fa-user"></i>
                    </button>
                </a>
                <a href="{{url('/catalogdashboard')}}">
                    <button class="btn btn-success" title="Product Dashboard">
                        <i class="ace-icon fa fa-signal"></i>
                    </button>
                </a>
                <a href="{{url('/TraningModule')}}">
                    <button class="btn btn-warning" title="Traning Module">
                        <i class="ace-icon fa fa-user"></i>
                    </button>
                </a>
                @endif

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
        <!-- for master module  -->
            
            <!-- master module starts here -->
            <?php $sub_module_data = array(); $i=0; ?>
            @foreach($data as $key => $value)
                @php
                $sub_module_data = DB::table('sub_web_module')
                        ->join('sub_web_module_bucket','sub_web_module_bucket.id','=','sub_web_module.sub_module_id')
                        ->join('company_sub_web_module_permission','company_sub_web_module_permission.sub_module_id','=','sub_web_module_bucket.id')
                        ->select('sub_web_module_bucket.module_id as module_id','sub_web_module_bucket.id as id','company_sub_web_module_permission.title as sub_module_name','sub_web_module_bucket.title as title')
                        ->where('sub_web_module.status',1)
                        ->where('sub_web_module.company_id',$userdata->company_id)
                        ->where('company_sub_web_module_permission.company_id',$userdata->company_id)
                        ->where('company_sub_web_module_permission.role_id',$userdata->role_id)
                        ->get()->toArray();

                if(empty($sub_module_data))
                {
                    $sub_module_data = DB::table('sub_web_module')
                                    ->join('sub_web_module_bucket','sub_web_module_bucket.id','=','sub_web_module.sub_module_id')
                                    ->select('sub_web_module_bucket.module_id as module_id','sub_web_module_bucket.id as id','sub_web_module.title as sub_module_name','sub_web_module_bucket.title as title')
                                    ->where('sub_web_module.status',1)
                                    ->where('sub_web_module.company_id',$userdata->company_id)
                                    ->where('sub_web_module_bucket.module_id',$value->id)
                                    ->get()->toArray();
                }
                else
                {
                    $sub_module_data = DB::table('sub_web_module')
                                ->join('sub_web_module_bucket','sub_web_module_bucket.id','=','sub_web_module.sub_module_id')
                                ->join('company_sub_web_module_permission','company_sub_web_module_permission.sub_module_id','=','sub_web_module_bucket.id')
                                ->select('sub_web_module_bucket.module_id as module_id','sub_web_module_bucket.id as id','company_sub_web_module_permission.title as sub_module_name','sub_web_module_bucket.title as title')
                                ->where('sub_web_module.status',1)
                                ->where('sub_web_module.company_id',$userdata->company_id)
                                ->where('company_sub_web_module_permission.company_id',$userdata->company_id)
                                ->where('company_sub_web_module_permission.role_id',$userdata->role_id)
                                ->where('sub_web_module_bucket.module_id',$value->id)
                                ->get()->toArray();
                }

                
                @endphp

                <li class="open hover">
                    @if($value->title=='home')
                    <a href="#">
                    <i class="{{$value->icon}}"></i>

                        {{$value->name}}
                    </a>
                    @else
                    <a href="{{url($value->title)}}">
                    <i class="{{$value->icon}}"></i>

                        {{$value->name}}
                    </a>
                    @endif
                        

                    <b class="arrow"></b>
                 
                    @if(!empty($sub_module_data))
                        <ul class="submenu ace-scroll scroll-disabled" style="">
                            @foreach($sub_module_data as $sub_key => $value_sub)

                                @php

                                    $sub_sub_module_data = DB::table('sub_sub_web_module')
                                            ->join('sub_sub_web_module_bucket','sub_sub_web_module_bucket.id','=','sub_sub_web_module.sub_sub_module_id')
                                            ->join('company_sub_sub_web_module_permission','company_sub_sub_web_module_permission.sub_sub_module_id','=','sub_sub_web_module_bucket.id')
                                            ->select('sub_sub_web_module_bucket.sub_web_module_id as sub_web_module_id','sub_sub_web_module_bucket.id as id','company_sub_sub_web_module_permission.title as sub_module_name','sub_sub_web_module_bucket.title as title')
                                            ->where('sub_sub_web_module.status',1)
                                            ->where('sub_sub_web_module.company_id',$userdata->company_id)
                                            ->where('company_sub_sub_web_module_permission.company_id',$userdata->company_id)
                                            ->where('company_sub_sub_web_module_permission.role_id',$userdata->role_id)
                                            ->get()->toArray();

                                    if(empty($sub_sub_module_data))
                                    {
                                        $sub_sub_module_data = DB::table('sub_sub_web_module')
                                                    ->join('sub_sub_web_module_bucket','sub_sub_web_module_bucket.id','=','sub_sub_web_module.sub_sub_module_id')
                                                    ->select('sub_sub_web_module_bucket.sub_web_module_id as sub_web_module_id','sub_sub_web_module_bucket.id as id','sub_sub_web_module.title as sub_module_name','sub_sub_web_module_bucket.title as title')
                                                    ->where('sub_sub_web_module.status',1)
                                                    ->where('sub_sub_web_module.company_id',$userdata->company_id)
                                                    ->where('sub_sub_web_module_bucket.sub_web_module_id',$value_sub->id)
                                                    ->get()->toArray();
                                    }
                                    else
                                    {
                                        $sub_sub_module_data = DB::table('sub_sub_web_module')
                                                ->join('sub_sub_web_module_bucket','sub_sub_web_module_bucket.id','=','sub_sub_web_module.sub_sub_module_id')
                                                ->join('company_sub_sub_web_module_permission','company_sub_sub_web_module_permission.sub_sub_module_id','=','sub_sub_web_module_bucket.id')
                                                ->select('sub_sub_web_module_bucket.sub_web_module_id as sub_web_module_id','sub_sub_web_module_bucket.id as id','company_sub_sub_web_module_permission.title as sub_module_name','sub_sub_web_module_bucket.title as title')
                                                ->where('sub_sub_web_module.status',1)
                                                ->where('sub_sub_web_module.company_id',$userdata->company_id)
                                                ->where('company_sub_sub_web_module_permission.company_id',$userdata->company_id)
                                                ->where('company_sub_sub_web_module_permission.role_id',$userdata->role_id)
                                                ->where('sub_sub_web_module_bucket.sub_web_module_id',$value_sub->id)
                                                ->get()->toArray();
                                    }

                                @endphp
                                

                                @if(!empty($sub_sub_module_data))
                                <li class="hover" class="dropdown-toggle">
                                    <a href="#" >
                                            {{$value_sub->sub_module_name}}
                                    </a>
                                    <b class="arrow"></b>
                                    <ul class="submenu">
                                        @foreach($sub_sub_module_data as $ss_key => $ss_value)
                                            <li class="hover">
                                                <a href="{{url($ss_value->title)}}">
                                                    {{$ss_value->sub_module_name}}
                                                </a>
                                                <b class="arrow"></b>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>

                                @else
                                <li class="hover">
                                    <a href="{{url($value_sub->title)}}" >
                                        {{$value_sub->sub_module_name}}
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                                @endif
                            @endforeach
                        </ul>
                        @else
                    @endif
                </li>
               <?php $i++; ?>
            @endforeach
            <!-- master module ends here -->

            <!-- reports module starts here -->

            <!-- Parent Ul Ends here -->
    </div>
</div>
<?php 
}
elseif($userdata->company_id=='25')
{
    ?>
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

                <img src="{{asset('msell/images/avatars/user.jpg')}}" alt=""/>
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
                             alt=""/>
                        {{strtoupper(Auth::user()->email)}}
                        <span class="user-info">
                                    <small>Welcome</small>
                            {{--Jason--}}
                                </span>

                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>

                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        <!-- <li>
                            <a href="#">
                                <i class="ace-icon fa fa-cog"></i>
                                Settings
                            </a>
                        </li> -->

                        
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
                
                <button class="btn btn-info">
                    <i class="ace-icon fa fa-home"></i>
                </button>

                <button class="btn btn-success">
                    <i class="ace-icon fa fa-signal"></i>
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
                        <a href="{{url('daily-attendance')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Daily Attendance
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="hover">
                        <a href="{{url('product_tracker')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Product Tracker
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
    </div> 
    <?php
} // aeris inventory company static code ends here 


    
else   // only for sub user in a company 
{
?>
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

                <img src="{{asset('msell/images/avatars/user.jpg')}}" alt=""/>
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
                             alt=""/>
                        {{strtoupper(Auth::user()->email)}}
                        <span class="user-info">
                                    <small>Welcome</small>
                            {{--Jason--}}
                                </span>

                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>

                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        <!-- <li>
                            <a href="#">
                                <i class="ace-icon fa fa-cog"></i>
                                Settings
                            </a>
                        </li> -->

                        
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
                
                <button class="btn btn-info">
                    <i class="ace-icon fa fa-home"></i>
                </button>

                <button class="btn btn-success">
                    <i class="ace-icon fa fa-signal"></i>
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
        @if(Auth::user()->id == 2216 || Auth::user()->id == 2215)
            <ul class="nav nav-list">
                <li class="hover pull_up">
                    <a href="{{url('#')}}" class="dropdown-toggle">
                        <i class="menu-icon fa fa-book  "></i>
                        <span class="menu-text">
                                    Entry Mode
                                </span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu ace-scroll can-scroll" style="top: -133px; max-height: 247px;">
                      
                        <li class="hover">
                            <a href="{{url('saleorder')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Purchae Order
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li class="hover">
                            <a href="{{url('Payment-Recieved-Details')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               Payment
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li class="hover">
                            <a href="{{url('SRN-Details')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               SRN
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li class="hover">
                            <a href="{{url('fullifillment_order')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               LR Receipt
                            </a>
                            <b class="arrow"></b>
                        </li>
                      
                     
                       
                    </ul>
                  
                
                </li>
            </ul>
            @elseif(Auth::user()->id == 2214)
            <ul class="nav nav-list">
                <li class="hover pull_up">
                    <a href="{{url('#')}}" class="dropdown-toggle">
                        <i class="menu-icon fa fa-headphones  "></i>
                        <span class="menu-text">
                                    Support
                                </span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu ace-scroll can-scroll" style="top: -133px; max-height: 247px;">
                      
                        <li class="hover">
                            <a href="{{url('dms_new_calling')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                DMS New Calling
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li class="hover">
                            <a href="{{url('dms_order_enquiry')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               DMS Order Enquiry
                            </a>
                            <b class="arrow"></b>
                        </li>
                       
                    </ul>
                  
                
                </li>
            </ul>

            @else
            <ul class="nav nav-list">
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
                            <a href="{{url('daily-attendance')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Daily Attendance
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li class="hover">
                            <a href="{{url('dailyTracking')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Daily Tracking
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
                            <a href="{{url('tour-program')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Tour Program
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="hover">
                            <a href="{{url('user-Meeting-Order')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                User Meeting Order Report
                            </a>
                            <b class="arrow"></b>
                        </li>
                     
                    </ul>
                    <div class="scroll-track scroll-detached no-track scroll-thin scroll-margin scroll-visible scroll-active"
                         style="display: none; height: 246px; top: -132px; left: 185px;">
                        <div class="scroll-bar" style="height: 55px; top: 0px;"></div>
                    </div>
                </li>
               
            {{-- <li class="hover pull_up">
                <a href="{{url('home')}}" class="dropdown-toggle">
                    <i class="menu-icon fa fa-bolt"></i>
                    <span class="menu-text">
                                Advance Reports
                            </span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

             
                <div class="scroll-track scroll-detached no-track scroll-thin scroll-margin scroll-visible scroll-active"
                     style="display: none; height: 246px; top: -132px; left: 185px;">
                    <div class="scroll-bar" style="height: 55px; top: 0px;"></div>
                </div>
            </li> --}}
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
                        <a href="{{url('user-sales-summary')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            State Wise Secondary Sales
                        </a>
                        <b class="arrow"></b>
                    </li>
                  
                 
                   
                </ul>
              
            
            </li>
        </ul>
        @endif
        

        
    </div>
    

    <?php
}

?>


@yield('body')
<!-- /.main-content -->

    <div class="footer">
        <div class="footer-inner">
            <div class="footer-content">
                        <span class="bigger-120" style="color:#FF9C03;font-style:italic">
                                
                                    <span class="" style="cololr:#FF9C03"><a href="{{$image->footer_link}}">{{$image->footer_message}}</a></span>
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
    $('#click_button').click(function() {
            var url_name = $("#serch_url").val();
            
            if (url_name != '') 
            {
                // alert(url_name);
                $.ajaxSetup({
                  headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
                });
                $.ajax({
                    type: "POST",
                    url: domain + '/search_url_details',
                    dataType: 'json',
                    data: "url_name=" + url_name,
                    success: function (data) 
                    {
                        if (data.code == 401) 
                        {
                           alert('Enter Module Name Doesnt Exist');
                        }
                        else if (data.code == 200) 
                        {
                            window.location = domain+'/'+ data.data_return;
                            
                        }
                    },
                    complete: function () {
                        // $('#loading-image').hide();
                    },
                    error: function () {
                    }
                });
            }       
        });
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

<script src="https://code.jquery.com/ui/1.10.2/jquery-ui.js" ></script>

<script>
    $(document).ready(function () {
        var result;
        var url_name;
        src = "{{ route('autocomplete_search_url') }}";
        $("#serch_url").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: src,
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function (data) {
                        result = data;
                        // console.log(result);
                        // url_name = result[0].title;
                        response(data);
                    }
                });
            },
            select: function (e, ui) {

//                            alert(result.email);
            },

            change: function (e, ui) {

//                            alert("changed!");
            },
            minLength: 2,

        }).on('autocompleteselect', function (e, ui) {
            // console.log(ui.item.label);
            var t = $(this),
            url_name = ( e.type == 'autocompleteresponse' ? ui.content[0].label : ui.item.label ),
            url_name = ( e.type == 'autocompleteresponse' ? ui.content[0].title : ui.item.title );
            // t.val(value);
            // var url_name = ui.item.label;
            $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });
            $.ajax({
                type: "POST",
                url: domain + '/search_url_details',
                dataType: 'json',
                data: "url_name=" + url_name,
                success: function (data) 
                {
                    if (data.code == 401) 
                    {
                       alert('Enter Module Name Doesnt Exist');
                    }
                    else if (data.code == 200) 
                    {
                        window.location = domain+'/'+ data.data_return;
                        
                    }
                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });

            return false;
        });
    });
</script>
<script>
    $(window).load(function(){
        $('#loader').fadeOut();
    });
</script>
@yield('js')
<script type="text/javascript" src="{{asset('/js/permission.js')}}"></script>

</body>
</html>
