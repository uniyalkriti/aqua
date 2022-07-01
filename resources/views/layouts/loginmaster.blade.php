<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />

    @yield('title')
    <title>Aqualabindia || Manacle Technologies</title>
    <meta name="description" content="User login page" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

    <!-- bootstrap & fontawesome -->
    <link rel="stylesheet" href="{{asset('msell/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{asset('msell/font-awesome/4.5.0/css/font-awesome.min.css')}}" />

    <!-- text fonts -->
    <link rel="stylesheet" href="{{asset('msell/css/fonts.googleapis.com.css')}}" />

    <!-- ace styles -->
    <link rel="stylesheet" href="{{asset('msell/css/ace.min.css')}}" />

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="{{asset('msell/css/ace-part2.min.css')}}" />
    <![endif]-->
    <link rel="stylesheet" href="{{asset('msell/css/ace-rtl.min.css')}}" />

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="{{asset('msell/css/ace-ie.min.css')}}" />
    <![endif]-->

    @yield('css')
    <!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

    <!--[if lte IE 8]>
    <script src="{{asset('msell/js/html5shiv.min.js')}}"></script>
    <script src="{{asset('msell/js/respond.min.js')}}"></script>
    <![endif]-->
</head>

<body class="login-layout light-login" style="background-image: url('company-profile/background_login.png'); ">

@yield('body')

<!--[if !IE]> -->
<script src="{{asset('msell/js/jquery-2.1.4.min.js')}}"></script>

<!-- <![endif]-->

<!--[if IE]>
<script src="{{asset('msell/js/jquery-1.11.3.min.js')}}"></script>
<![endif]-->
<script type="text/javascript">
    if('ontouchstart' in document.documentElement) document.write("<script src='public/msell/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
</script>

<!-- inline scripts related to this page -->

@yield('js')
</body>
</html>