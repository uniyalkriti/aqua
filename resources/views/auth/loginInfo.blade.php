@extends('layouts.loginmaster')

@section('title')
    <title>{{ config('app.name', '') }}</title>
@endsection

@section('css')
    <link rel="stylesheet" href="msell/css/common.css" />
@endsection

@section('body')
<?php $current_url = $_SERVER['HTTP_HOST'];
    // echo $current_url;
  ?>
  <style type="text/css">
      .employeeSelfServiceRow {
            margin-top: 7rem;
        }
        .ess {
            font-weight: 600;
            font-size: 8rem;
            line-height: 9rem;
            font-family: 'lato';
        }
        
      
  </style>
    <div class="container-fluid "  style="margin-top: 9rem;" >
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-4 col-lg-offset-4" >
                    <div class=" container-fluid" >
                        <div class="center">

                            {{--<h4 class="blue" id="id-company-text">&copy; Manacle</h4>--}}
                        </div>

                        <div class="space-6"></div>

                        <div class="position">
                            <div id="login-box" class="login-box visible widget-box no-border" style="background-color: white;">
                                <div class="widget-body">
                                    <div class="widget-main" style="background-color: white;">
                                        <h1 style="text-align: center;">
                                            {{--<i class="ace-icon fa fa-leaf green"></i>--}}
                                            <img src="{{asset('company-profile/20210428112847.png')}}" width="300px">
                                            <br>
                                            <br>
                                            <br>
                                            {{--<span class="red">{{ config('app.name', '') }}</span>--}}
                                            {{--<span class="white" id="id-text2">Sell</span>--}}
                                        </h1>
                                        <h4 class="header blue  bigger">
                                            <i class="ace-icon fa fa-coffee green"></i>
                                            Please Enter Your Information
                                        </h4>

                                        <div class="space-6"></div>

                                        <form id="validation-form" method="post" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                                            {!! csrf_field() !!}
                                            <fieldset>
                                                <label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="text" name="email" class="form-control"
                                                                   placeholder="Email"/>
															<i class="ace-icon fa fa-user"></i>
														</span>
                                                </label>

                                                <label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="password" name="password" class="form-control"
                                                                   placeholder="Password"/>
															<i class="ace-icon fa fa-lock"></i>
														</span>
                                                </label>

                                                <div class="space"></div>
                                                @if (count($errors))
                                                        @foreach($errors->all() as $error)
                                                        <label class="error">{{ $error }}</label>

                                                        @endforeach
                                                @endif
                                                
                                                <div class="clearfix">
                                                    <label class="inline">
                                                        <input type="checkbox" class="ace"/>
                                                        <span class="lbl"> Remember Me</span>
                                                    </label>

                                                    <button type="submit"
                                                            class="width-35 pull-right  btn-sm " style="background-color:#00334e; color: white;">
                                                        <i class="ace-icon fa fa-key"></i>
                                                        <span class="bigger-110">Login</span>
                                                    </button>
                                                </div>

                                                <div class="space-4"></div>
                                            </fieldset>
                                            @if($current_url == 'piranha.msell.in')
                                                <div class="">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <strong style="color:black;">Scan For Download APP</strong> 
                                                            <img src="{{asset('msell/images/piranha_qr_code.png')}}" width="100px">  
                                                        </div>
                                                        <div class="col-md-6" style="text-align: right;">
                                                            <strong style="color:black;" ><span >Click For Download APP</span></strong>
                                                            <h1 style="text-align: right;">

                                                                <a href="https://drive.google.com/file/d/1W4EbjGfGYhQZF-LWQbDODD_dBkMwIinp/view?usp=sharing" class="btn-sm width-155 " style="background-color:#00334e; color: white;"><i class="ace-icon fa fa-download"></i><span class="bigger-110">Download</span></a>
                                                            </h1>
                                                        </div>
                                                        
                                                    </div>
                                                         
                                                     
                                                </div>

                                                @else

                                                <div class="">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <strong style="color:black;">Scan For Download APP</strong> 
                                                            <img src="{{asset('msell/images/msell_qr_code.png')}}" width="100px">  
                                                        </div>
                                                        <div class="col-md-6" style="text-align: right;">
                                                            <strong style="color:black;" ><span >Click For Download APP</span></strong>
                                                            <h1 style="text-align: right;">

                                                                <a href="https://drive.google.com/file/d/1RZIuct4K2LI1h4vBnq27wvvfUCKdB6V6/view?usp=sharing" class="btn-sm width-155 " style="background-color:#00334e; color: white;"><i class="ace-icon fa fa-download"></i><span class="bigger-110">Download</span></a>
                                                            </h1>
                                                        </div>
                                                        
                                                    </div>
                                                         
                                                     
                                                </div>

                                                
                                            @endif
                                        </form>
                                    </div><!-- /.widget-main -->
                                    

                                    <div class="toolbar clearfix">
                                        <div>
                                            <a href="#" data-target="#forgot-box" class="forgot-password-link">
                                                <i class="ace-icon fa fa-arrow-left"></i>
                                                I forgot my password
                                            </a>
                                        </div>
                                    </div>
                                </div><!-- /.widget-body -->
                            </div><!-- /.login-box -->

                            <div id="forgot-box" class="forgot-box widget-box no-border">
                                <div class="widget-body">
                                    <div class="widget-main">
                                        <h4 class="header red  bigger">
                                            <i class="ace-icon fa fa-key"></i>
                                            Retrieve Password
                                        </h4>

                                        <div class="space-6"></div>
                                        <p>
                                            Enter your email and to receive instructions
                                        </p>

                                        <form>
                                            <fieldset>
                                                <label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="email" name="email" class="form-control"
                                                                   placeholder="Email"/>
															<i class="ace-icon fa fa-envelope"></i>
														</span>
                                                </label>

                                                <div class="clearfix">
                                                    <button type="button"
                                                            class="width-35 pull-right btn btn-sm btn-danger">
                                                        <i class="ace-icon fa fa-lightbulb-o"></i>
                                                        <span class="bigger-110">Send Me!</span>
                                                    </button>
                                                </div>
                                            </fieldset>
                                        </form>
                                    </div><!-- /.widget-main -->

                                    <div class="toolbar center">
                                        <a href="#" data-target="#login-box" class="back-to-login-link">
                                            Back to login
                                            <i class="ace-icon fa fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div><!-- /.widget-body -->
                            </div><!-- /.forgot-box -->


                        </div>
                    </div>
                </div><!-- /.col -->
               
            </div><!-- /.row -->
        </div><!-- /.main-content -->
    </div>

@endsection

@section('js')
    

    <script src="msell/js/common.js"></script>
    <script src="msell/js/jquery.validate.min.js"></script>
    <script src="msell/js/jquery-additional-methods.min.js"></script>
    <script>
        $('#validation-form').validate({
            rules: {
                email: {
                    required: true
//                    email: true
                },
                password: {
                    required: true
                }
            }
        });
    </script>
@endsection

