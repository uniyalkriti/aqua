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
            // var domain = location.protocol + '//' + '162.213.190.125/gopal-reports';
           // var domain = location.protocol + '//' + location.hostname + (location.port ? ':' + location.port : '') + '/public';
           var domain = '{{Request::root()}}';

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
<!-- for dyanmic module starts here  -->

<!-- for dyanmic module ends here  -->
<body class="no-skin">
<div class="loader" id="loader"></div>

<script type="text/javascript">
    $('.navbar-brand').onclick(function() {
        $(".loader").fadeIn("slow");
    });
    </script>

@php

     $image = DB::table('company')
            ->select('company_image')
            ->where('id',$company_id)
            ->first();
           
@endphp
<div id="navbar" class="navbar navbar-default    navbar-collapse       h-navbar ace-save-state">
    <div class="navbar-container ace-save-state" id="navbar-container">
        <div class="navbar-header pull-left">
            <!-- <a href="{{url('home')}}" class="navbar-brand"> -->
            <a class="navbar-brand">
                {{-- @if(!empty($image->company_image))
                <small>
                    <img src="{{asset($image->company_image)}}" width="40px">
                </small>
                @else
                <small>
                    <img src="{{asset('msell/images/gopal.jpg')}}" width="40px">
                </small>
                @endif  --}}
            </a>

            <!-- <button class="pull-right navbar-toggle navbar-toggle-img collapsed" type="button" data-toggle="collapse"
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
            </button> -->
        </div>



        <nav role="navigation" class="navbar-menu pull-left collapse navbar-collapse">

        </nav>
    </div><!-- /.navbar-container -->






@yield('body')
<!-- /.main-content -->

    <div class="footer">
        <div class="footer-inner">
            <div class="footer-content">
                        <span class="bigger-120" style="color:#FF9C03;font-style:italic">
                            <span class="" style="cololr:#FF9C03"></span>
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
