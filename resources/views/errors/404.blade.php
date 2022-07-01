<!DOCTYPE html>
<html lang="en" class="pos-relative">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> {{ config('app.name', '') }}</title>

    <!-- vendor css -->
    <link rel="stylesheet" href="{{asset('/msell/css/bootstrap.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('/msell/font-awesome/4.5.0/css/font-awesome.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('/msell/css/ace.min.css')}}" class="ace-main-stylesheet" id="main-ace-style"/>
  </head>

  <body class="clearfix center">

    <div class="ht-100v d-flex align-items-center justify-content-center">
      <div class="wd-lg-70p wd-xl-50p tx-center pd-x-40">
        <h1 class="tx-100 tx-xs-140 tx-normal tx-inverse tx-roboto mg-b-0">404!</h1>
        <h5 class="tx-xs-24 tx-normal tx-info mg-b-30 lh-5">The page your are looking for has not been found.</h5>
        <p class="tx-16 mg-b-30">The page you are looking for might have been removed, had its name changed.</p>

        <div class="d-flex justify-content-center">
          <div class="input-group wd-xs-300">
            
            <div class="input-group-btn">
                <a href="{{ url('home') }}" class="btn btn-info"> Go to home </a>
            </div><!-- input-group-btn -->
          </div><!-- input-group -->
        </div><!-- d-flex -->
      </div>
    </div><!-- ht-100v -->

    <script src="{{asset('msell/js/jquery-2.1.4.min.js')}}"></script>

     <script src="{{asset('msell/js/bootstrap.min.js')}}"></script>

    <!-- page specific plugin scripts -->

    <!-- ace scripts -->
    <script src="{{asset('msell/js/ace-elements.min.js')}}"></script>
    <script src="{{asset('msell/js/ace.min.js')}}"></script>
    

  </body>
</html>
