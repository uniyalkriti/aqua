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

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="{{asset('nice/css/ace-ie.min.css')}}" />
    <![endif]-->

    <!-- inline styles related to this page -->

    <!-- ace settings handler -->
    <script src="{{asset('nice/js/ace-extra.min.js')}}"></script>
    <script>

        if (location.hostname == "localhost") {
            var domain = '{{Request::root()}}';
        }
        else {
            var domain = location.protocol + '//' + location.hostname + (location.port ? ':' + location.port : '');
        }
    </script>
</head>
 @php
    $userdata=Auth::user();
    $data = DB::table('web_module')
            ->join('modules_bucket','modules_bucket.id','=','web_module.module_id')
            ->select('modules_bucket.id as id','modules_bucket.title','web_module.title as name','modules_bucket.icon')
            ->where('web_module.status',1)
            ->where('modules_bucket.status',1)
            ->where('web_module.company_id',$userdata->company_id)
            ->orderBy('web_module.sequence','ASC')
            ->get();

     $image = DB::table('company')
            ->select('company_image','footer_message','footer_link')
            ->where('id',$userdata->company_id)
            ->first();
           
@endphp
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
            <a href="{{url('home')}}" class="navbar-brand">
                @if(!empty($image->company_image))
                <small>
                    <img src="{{asset($image->company_image)}}" width="40px">
                    Role: {{Auth::user()->is_admin==1?'Admin':'User'}}
                </small>
                @else
                <small>
                    <img src="{{asset('msell/images/gopal.jpg')}}" width="40px">
                    Role: {{Auth::user()->is_admin==1?'Admin':'User'}}
                </small>
                @endif
            </a>
        </div>
        {{--<div class="pull-left ">--}}
            {{--<select id="current_project" class="form-control input-sm" style="margin-top: 7px">--}}
                {{--<option value="">All Project</option>--}}
                {{--@if(!empty($project_listing))--}}
                    {{--@foreach($project_listing as $key=>$data)--}}
                        {{--<option {{isset($_COOKIE['current_project']) && $_COOKIE['current_project']==$data->project_code?'selected':''}} value="{{$data->project_code}}">{{ucwords(strtolower($data->name))}}</option>--}}
                        {{--@endforeach--}}
                    {{--@endif--}}
            {{--</select>--}}
        {{--</div>--}}

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

    @include('layouts.dashboard_menu')
    @yield('body')
    <!-- /.main-content -->

    <div class="footer">
        <div class="footer-inner">
            <div class="footer-content">
						<span class="bigger-100">
                                <!-- <img class="nav-user-photo" src="{{asset('msell/images/logo_msell.png')}}" style="height:40px; width:40px;" alt="mSELL"> -->
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
