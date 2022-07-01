<!DOCTYPE html>
<html lang="en">

<head>
    <!--title and description start-->
    @yield('header_css')
    <!--title and description end-->

    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge text/html; charset=utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--icon start-->
    <link rel="icon" href="https://www.talktoastro.com/public/img/fv.png?v=1.1" type="image/png" sizes="16x16">
    <!--icon end-->

    <!--canonical code start-->
    <link rel="canonical" href="https://www.talktoastro.com">
    <!--canonical code end-->

    <!-- base url start-->
    <meta name="base-url" content="https://www.talktoastro.com">
    <!-- base url end-->

    <!--google site veification start-->
    <meta name="google-site-verification" content="uj-VIh9rssFlFbCcBHUS2ljGnot5qG8WAIsV2e_X09A" />
    <!--google site veification end-->

    <link rel="stylesheet" href="{{asset('/assets/bootstrap.css')}}">

    <meta name="csrf-token" content="gpmYprZ3cacR6Z9oH05v3y6pCe7kaWU94AVbhoUl">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" crossorigin="anonymous" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js" integrity="sha512-Eak/29OTpb36LLo2r47IpVzPBLXnAMPAVypbSZiZ4Qkf8p/7S/XRG5xp7OKWPPYfJT6metI+IORkR5G8F900+g==" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="{{asset('/assets/style.css')}}">

    {{-- <link rel="stylesheet" type="text/css" href="{{asset('/css/fontawesome.min.css')}}"> --}}

    <style>
      .navbar-collapse {
        position: fixed;
        top: 50px;
        left: 0;
        padding-left: 15px;
        padding-right: 15px;
        padding-bottom: 15px;
        width: 60%;
        height: 100%;
    }

    .navbar-collapse.collapsing {
        left: -75%;
        transition: height 0s ease;
    }

    .navbar-collapse.show {
        left: 0;
        transition: left 300ms ease-in-out;
    }

    .navbar-toggler.collapsed ~ .navbar-collapse {
        transition: left 500ms ease-in-out;
    }
    .navbar-collapse ul li:hover {
    background: #FF8C00;
    padding-left: 26px;
}
.navbar-collapse ul li {
    padding-left: 26px;
    border-bottom: 1px solid #f3f3f3;
    margin-bottom: 0px;
}
.navbar-collapse ul li a {
    font-size: 16px;
}
.navbar-collapse ul li a:hover {
    color: white !important;
}
[data-toggle="collapse"] .fa:before {  
  content: "\f00d";
}

[data-toggle="collapse"].collapsed .fa:before {
  content: "\f0c9";
}
@media screen and (min-width: 1200px){
  .navbar-expand-lg .navbar-collapse {
    display: none !important;
  }
}
    </style>

    <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>

    <!--Start of Tawk.to Script start-->
    <script type="text/javascript">
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/5ec9e2ee8ee2956d73a3fd95/default';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>
    <!--End of Tawk.to Script end-->

    <!-- Google Anlytics (sur@j) start-->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-130258295-1"></script>
    <!-- Google Anlytics (sur@j) end-->

    <!-- Global site tag (gtag.js) - Google Analytics  start-->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-134601282-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());



        gtag('config', 'UA-134601282-1');
    </script>
    <!-- Global site tag (gtag.js) - Google Analytics  end-->

    <!--facebook pixel code start-->
    <script>
        ! function(f, b, e, v, n, t, s)

        {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?

                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };

            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';

            n.queue = [];
            t = b.createElement(e);
            t.async = !0;

            t.src = v;
            s = b.getElementsByTagName(e)[0];

            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',

            'https://connect.facebook.net/en_US/fbevents.js');

        fbq('init', '1181942968625344');

        fbq('track', 'PageView');
    </script>
    <!--facebook pixel code end-->

    <!-- DO NOT MODIFY -->
    <!-- Quora Pixel Code (JS Helper) -->
    <script>
        ! function(q, e, v, n, t, s) {
            if (q.qp) return;
            n = q.qp = function() {
                n.qp ? n.qp.apply(n, arguments) : n.queue.push(arguments);
            };
            n.queue = [];
            t = document.createElement(e);
            t.async = !0;
            t.src = v;
            s = document.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s);
        }(window, 'script', 'https://a.quora.com/qevents.js');
        qp('init', 'b109169b7c72465f8067daf473812674');
        qp('track', 'ViewContent');
    </script>


    <!-- End of Quora Pixel Code -->
    <noscript><img height="1" width="1" style="display:none" src="https://q.quora.com/_/ad/b109169b7c72465f8067daf473812674/pixel?tag=ViewContent&noscript=1" /></noscript>
    <script>
        qp('track', 'Purchase');
    </script>

    <!-- Add event to the button's click handler -->
    <script type="text/javascript">
        qp('track', 'Purchase'); // Call this function when inline action happens
    </script>

    
     @yield('head')
    
</head>


<body onload="generateCaptcha()" >

    <!-- =======  Header Start  ======= -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-light fixed-top index-control" style="padding: 9px 15px 8px 0;">
            <div class="container">
                <button class="navbar-toggler collapsed an" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="fa"></span>
                  </button>
                  <a class="navbar-brand" href="{{url('/')}}"><img class="img-fluid logo-img" src="{{asset('/assets/astrologo.png')}}" alt="TalktoAstro logo"></a>
                      
                  <div class="collapse an navbar-collapse nav-side" id="navbarToggler" style="background: white;
margin-top: -1px;">
                            <ul class="navbar-nav" style="margin-left: -15px;margin-right: -15px; height: 400px;overflow-x: hidden;overflow-y: scroll;">
                            <li class="nav-item ">
                              <a class="nav-link" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Contact Us <i class="fas fa-caret-down ml-3"></i></a>
                              <ul  class="collapse" id="collapseExample" class="navbar-nav">
                              <li style="border-top: 1px solid rgb(243, 243, 243);margin-left: -65px;">
                                <a href="tel:8860095202" class="nav-link"> 91-8860095202
                                   </a>
                              </li>
                              <li style="border-top: 1px solid rgb(243, 243, 243);margin-left: -65px;"> 
                               <a href="mailto:support@talktoastro.com" class="nav-link">support@talktoastro.com</a></li>
                            </ul>
                            <li class="nav-item">
                              <a class="nav-link" href="#"><i class="fas fa-user-friends mr-1"></i>Talk to Astrologer</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" href="#"><i class="fas fa-comments mr-1"></i>Chat with Astrologer</a>
                            </li>
                             <li class="nav-item">
                              <a class="nav-link" href="#"><i class="fas fa-file-alt mr-1"></i>Order Report</a>
                            </li>
                             <li class="nav-item">
                              <a class="nav-link" href="#"><i class="fas fa-book mr-1"></i>Rashifal 2021</a>
                            </li>
                             <li class="nav-item">
                              <a class="nav-link" href="#"><i class="fas fa-file mr-1"></i>Astrology Articles</a>
                            </li>
                             <li class="nav-item">
                              <a class="nav-link" href="#"><i class="fas fa-compass mr-1"></i>Free Kundali</a>
                            </li>
                             <li class="nav-item">
                              <a class="nav-link" href="#"><i class="fas fa-calculator mr-1"></i>Free Astrology Services</a>
                            </li>
                             <li class="nav-item">
                              <a class="nav-link" href="#"> <i class="fas fa-clone mr-1"></i>Tarot Card Readings</a>
                            </li>
                             <li class="nav-item">
                              <a class="nav-link" href="#"><i class="fas fa-question-circle mr-1"></i>Ask Free Questions</a>
                            </li>
                            <li class="nav-item">
                              <li>
                              <a href="https://play.google.com/store/apps/details?id=tta.destinigo.talktoastro" target="_blank" class="mt-3"><img src="https://www.talktoastro.com/public/img/playStore.png" style="margin-top:10px;" alt="TalktoAstro Android App" width="130"></a>
                            </li>
                            </li>

                          </ul> 
                    <!-- <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                      <li class="nav-item dropdown contactus-btn" id="contact1">
                          <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Contact Us
                          </a>
                          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                             <a class="nav-link" href="https://api.whatsapp.com/send?phone=918860095202"><i class="fa fa-whatsapp fa-1.5x"></i> <strong>91-8860095202</strong></a>
                               <a class="nav-link" href="https://api.whatsapp.com/send?phone=918860095202"><i class="fa fa-envelope fa-1.5x"></i> <strong> support@talktoastro.com</strong></a>
                          </div>
                      </li>
                    </ul> -->
                  </div>
          
                  <div class="nav-side">
                      <span>
                          <li class="nav-item dropdown contactus-btn" id="contact2">
                              <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Contact Us
                              </a>
                              <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                              <a class="nav-link" href="https://api.whatsapp.com/send?phone=918860095202"><i class="fa fa-whatsapp fa-1.5x"></i> <strong>91-8860095202</strong></a>
                               <a class="nav-link" href="https://api.whatsapp.com/send?phone=918860095202"><i class="fa fa-envelope fa-1.5x"></i> <strong> support@talktoastro.com</strong></a>
                              
                              </div>
                          </li>
                      </span>
                      <span class="navbar-brand-small"><a href="{{ url('/') }}"><img class="img-fluid logo-img" src="{{asset('/assets/astrologo.png')}}" alt="TalktoAstro logo"></a></span>
                    
                       @if(Auth::check())
                        <?php $balance = Auth::user()->balance; ?>
                            @if(Auth::user()->phonecode ==91)
                      <img class="purse" src="{{asset('/assets/noun_Purse_3362985.png')}}" alt="">
                      <span class="amount"> ₹ {{ $balance }} </span>
                           @else
                       <img class="purse" src="{{asset('/assets/noun_Purse_3362985.png')}}" alt="">
                      <span class="amount"> $ {{ $balance }} </span>
                            @endif
                       @endif
                      
                      @if(Route::has('login'))
                       @auth
                                  <span>
                                      <li class="nav-item dropdown contactus-btn">
                                          <a class="nav-link dropdown-toggle text-dark p-0" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                              <span class="head-text"><b> {{ucfirst(Auth::user()->first_name)}} </b><i class="fa fa-user" aria-hidden="true"></i></span>
                                          </a>
                                          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                            <a class="dropdown-item" href="#"><span><img src="{{asset('/assets/logged-in/user-head/Icon material-payment.svg')}}" class="pr-2" width="30px" alt=""></span> Recharge History</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#"><span><img src="{{asset('/assets/logged-in/user-head/Group 427.svg')}}" class="pr-2" width="30px" alt=""></span> Call History</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#"><span><img src="{{asset('/assets/logged-in/user-head/Icon metro-history.svg')}}" class="pr-2" width="30px" alt=""></span> Report History</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#"><span><img src="{{asset('/assets/logged-in/user-head/Icon ionic-ios-chatbubbles.svg')}}" class="pr-2" width="30px" alt=""></span> Chat History</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#"><span><img src="{{asset('/assets/logged-in/user-head/Icon material-account-circle.svg')}}" class="pr-2" width="30px" alt=""></span> Profile History</a>
                                            <div class="dropdown-divider"></div>
                                             <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();  document.getElementById('logout-form').submit();"><span><img src="/assets/logged-in/user-head/Icon open-account-logout.svg" class="pr-2" width="30px" alt=""></span>Logout</a>
                                              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                             </form>
                                          </div>
                                      </li>
                                      <i class="fa fa-bell pl-1" aria-hidden="true"></i>
                                  </span>
                      
                       @else
                            <span>
                                <a class="btn head-btn raise" data-toggle="modal" data-target="#login">Login</a>
                                <a class="btn head-btn raise" data-toggle="modal" data-target="#signup">Sign Up</a>
                           </span>
                       @endauth
                      @endif
                      
                  </div>
            </div>
        </nav> 
    </header>
    <!-- =======  Header End  ======= -->


    <div class="modal fade" id="signup">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">            
            <div class="modal-body">
                <div class="row column-reverse">
                    <div class="col-lg-6 col-md-12 ">
                        <div class="modal-left text-center">
                            <img src="{{asset('/assets/astrologo.png')}}" alt="">
                            <p class="sign-up-head">Sign Up</p>
                        </div>

                          @if(Session::has('message'))
                <p class="alert alert-danger">{{ Session::get('message') }}</p>
                @endif

                @foreach (['danger', 'warning', 'success', 'info'] as $key)
                @if(Session::has($key))
                <p class="alert alert-{{ $key }}">{{ Session::get($key) }}</p>
                @endif
                @endforeach

                @if (count($errors) > 0)
                <div class="row" style="background-color:#ff5959; padding:20px;">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li style="font-color:#bc0000;">{{ $error }}</li><br>
                        @endforeach
                    </ul>
                </div><br>
                @endif
                        <form action="{{url('/register')}}" class="register pl-4 pr-4" onsubmit="return CheckValidCaptcha()" method="post">
                             {{ csrf_field() }}
                          
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="fname" class="has-float-label">
                                            <input type="text" class="form-control form-control-sm" placeholder="First Name" id="fname" name="first_name" required>
                                            <span>First Name</span>
                                        </label>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="lname" class="has-float-label">
                                            <input type="text" class="form-control form-control-sm" placeholder="Last Name" id="lname" name="last_name" required>
                                            <span>Last Name</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="email" class="has-float-label">
                                            <input type="email" class="form-control form-control-sm" placeholder="E-mail" id="email" name="email" required>
                                            <span>E-mail</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                         <?php $countries=DB::table('countries')->orderBy('name')->get();?>
                                        <select id="inputState" class="form-control form-control-sm select-option" name="phonecode">
                                             <option selected>IN (+91)</option>
                                            @foreach($countries as $country)
                                            <option value="{{$country->phonecode}}">{{$country->name}} (+{{$country->phonecode}})</option>
                                             @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label for="pnum" class="has-float-label">
                                            <input type="text" class="form-control form-control-sm" placeholder="Phone Number" id="pnum" name="mobile" required>
                                            <span>Phone Number</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="pass1" class="has-float-label">
                                            <input type="password" class="form-control form-control-sm show_hide_password" placeholder="Enter Password" id="pass1" name="password" required>
                                            <span>Enter Password</span>
                                            <i class="fa fa-eye float-right mt-n4 mr-2" id="togglePassword1"></i>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="pass2" class="has-float-label">
                                            <input type="password" class="form-control form-control-sm show_hide_password" placeholder="Confirm Password" id="pass2"  name="password_confirmation" required>
                                            <span>Confirm Password</span>
                                            <i class="fa fa-eye float-right mt-n4 mr-2" id="togglePassword2"></i>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <div class="input-group">
                                              <!--<input type="text" id="mainCaptcha" readonly="readonly" class="form-control"  name="maincaptcha" />-->
                                            <input type="text" class="form-control form-control-sm mt-1" id="mainCaptcha"  readonly="readonly" >
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="validationTooltipUsernamePrepend1"><a href="#"><i type="button" id="refresh" value="" onclick="generateCaptcha();" class='fa fa-refresh' aria-hidden='true' ></i></a></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label for="captcha" class="has-float-label">
                                            <input type="text" name="verifycaptcha" placeholder="Verify Captcha" id="captcha" class="form-control form-control-sm" required  />
                                            <span>Verify Captcha</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="text-center pt-2">
                                    <button type="submit" class="btn submit-report">Sign Up</button>
                                    <p class="pt-3 have-acc">Already have an account ? <a href="#" class="sign-link">Sign In</a></p>
                                </div>
                          </form>
                    </div>
                    <div class="col-lg-6 col-md-12 m-right pt-2 order-first order-lg-last">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div class="modal-right">
                            <p class="head-one"> The </p>
                            <p class="head-two"> Tarot </p>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
    </div>

    <div class="modal fade" id="login">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">            
            <div class="modal-body">
                <div class="row column-reverse">
                    <div class="col-lg-6 col-md-12 ">
                        <div class="modal-left text-center pt-5">
                            <img src="{{asset('/assets/astrologo.png')}}" alt="">
                            <p class="pt-2" class="login-now-btn">Login Now</p>
                        </div>

                        <form class="register pl-4 pr-4 pt-3"  method="POST" action="{{url('login_user')}}">
                             {{ csrf_field() }}
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <select id="inputState2" class="form-control form-control-sm select-option" name="phonecode" >
                                    <?php $countries=DB::table('countries')->orderBy('name')->get();?>
                                      <option selected>IN (+91)</option>
                                      @foreach($countries as $country)
                                      <option value="{{$country->phonecode}}" @if($country->phonecode=='91') selected @endif >{{$country->iso}} (+{{$country->phonecode}})</option>
                                      @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-8">
                                  <label for="pnum2" class="has-float-label">
                                      <input type="text" class="form-control form-control-sm" placeholder="Phone Number" id="pnum2" name="mobile" required>
                                      <span>Phone Number</span>
                                  </label>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="pass3" class="has-float-label">
                                        <input type="password" class="form-control form-control-sm show_hide_password" placeholder="Enter Password" id="pass3" name="password" required>
                                        <span>Enter Password</span>
                                        <i class="fa fa-eye float-right mt-n4 mr-2" id="togglePassword3"></i>
                                    </label>
                                </div>
                            </div>
                            
                       
                            <div class="text-center pt-2">
                                <button type="submit" class="btn submit-report">Login</button>
                                <p class="text-secondary p-1">or</p>
                                <a type="submit" class="btn submit-report mt-n3" data-toggle="modal" data-target="#loginotp"  data-dismiss="modal" onclick="otp_verify();">Login with OTP</a>
                                <p class="pt-3 text-secondary">Don't have an account ? <a href="#" class="sign-link">Sign Up</a></p>
                            </div>
                        </form>

                    </div>
                    <div class="col-lg-6 col-md-12 m-right pt-2 order-first order-lg-last">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div class="modal-right">
                            <p class="head-one"> The </p>
                            <p class="head-two"> Tarot </p>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
    </div>

    <div class="modal fade" id="loginotp">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">            
            <div class="modal-body">
                <div class="row column-reverse">
                    <div class="col-lg-6 col-md-12 pb-5">
                        <div class="modal-left text-center pt-5">
                            <img src="/assets/astrologo.png" alt="">
                            <p class="pt-2 verify-otp" >Verify OTP</p>
                        </div>

                        <form class="register pl-5 pr-5 pt-3">
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                  <label for="otp" class="has-float-label">
                                      <input type="text" class="form-control form-control-sm" placeholder="Enter Your 4 Digit OTP Here" id="otp">
                                      <span>Enter OTP</span>
                                  </label>
                                </div>
                            </div>
                       
                            <div class="text-center pt-2">
                                <a type="submit" class="btn submit-report">Submit</a><br>
                                <a type="submit" class="btn submit-report mt-3">Resend OTP</a>
                            </div>
                        </form>

                    </div>
                    <div class="col-lg-6 col-md-12 m-right pt-2 order-first order-lg-last">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div class="modal-right">
                            <p class="head-one"> The </p>
                            <p class="head-two"> Tarot </p>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
    </div>


        <!------ Main Content ------------->





        @yield('content')







        <!------end  Main Content ------------->




    <!-- =======  Footer Start  ======= -->
    <footer class="bg-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6 p-0">
                    <ul class="list-unstyled footer-link mt-4">
                        <li><a href="{{ url('/') }}" class="link-bold"><img class="pb-2 foot-logo" src="{{url('/assets/astrologo.png')}}" alt=""></a></li>
                        <div class="desk-foot">
                            <li> <a href="" class="link-bold"><div class="iconic-div"><img class="iconic" src="{{asset('/assets/Icon awesome-money-bill-alt.png')}}" alt=""></div><span class="ss-pp"> Money Back Guarantee</span> </a></li>
                            <li> <a href="" class="link-bold"><div class="iconic-div"><img class="iconic" src="{{asset('/assets/Icon material-verified-user.png')}}" alt=""></div><span class="ss-pp"> Verified Experts Astrologers </span> </a></li>
                            <li> <a href="" class="link-bold"><div class="iconic-div"><img class="iconic" src="{{asset('/assets/Icon material-security.png')}}" alt=""></div><span class="ss-pp"> 100% Secure Payment </span> </a></li>
                            <li><a href="{{ url('/join-as-astrologer') }}" class="foot-btn-vg mt-4" >Join as astrologer</a></li>
                        </div>
                        <div class="mob-foot">
                            <li><a href="{{ url('/talk-to-astrologer') }}">Indian Astrologers/Jyotish</a></li>
                            <li><a href="{{ url('/talk-to-astrologer') }}">Best Astrology Services</a></li>
                            <li><a href="{{ url('/talk-to-astrologer') }}">Astrologer Consultation</a></li>
                            <li><a href="{{ url('/talk-to-astrologer') }}">Astrology reading</a></li>
                            <li><a href="{{ url('/talk-to-astrologer') }}">Astrologer online</a></li>
                            <li><a href="{{ url('/articles') }}">Moon Signs /Ascendants</a></li>
                            <li><a href="{{ url('/articles') }}">Nakshatras</a></li>
                            <li><a href="{{ url('/articles') }}">Panchang Kaal Sarpyog</a></li>
                            <li><a href="{{ url('/articles') }}">Shani Sade Sati</a></li>
                        </div>
                    </ul>
                </div>

                <div class="col-md-3 col-6 p-0">
                    <ul class="list-unstyled footer-link mt-4">
                    <div class="mob-foot">
                        <li><a href="index.html" class="link-bold"><img class="pb-2 foot-logo invisible" src="/assets/astrologo.png" alt=""></a></li>
                        <li> <a href="" class="link-bold"><div class="iconic-div"><img class="iconic" src="/assets/Icon awesome-money-bill-alt.png" alt=""></div><span class="ss-pp"> Money Back Guarantee</span> </a></li>
                        <li> <a href="" class="link-bold"><div class="iconic-div"><img class="iconic" src="/assets/Icon material-verified-user.png" alt=""></div><span class="ss-pp"> Verified Experts Astrologers </span> </a></li>
                        <li> <a href="" class="link-bold"><div class="iconic-div"><img class="iconic" src="/assets/Icon material-security.png" alt=""></div><span class="ss-pp"> 100% Secure Payment </span> </a></li> 
                        <li><button class="foot-btn-vg mt-3" data-toggle="modal" data-target="#signup">Join as astrologer</button></li>
                        <li class="list-inline-item"><a href=""><img class="foot-icon" src="/assets/gplay.png" alt=""></a></li>
                        <li class="list-inline-item"><a href=""><img class="foot-icon" src="/assets/app-store.png" alt=""></a></li>
                    </div>
                    <div class="desk-foot">
                            <li><a href="{{ url('/talk-to-astrologer') }}">Indian Astrologers/Jyotish</a></li>
                            <li><a href="{{ url('/talk-to-astrologer') }}">Best Astrology Services</a></li>
                            <li><a href="{{ url('/talk-to-astrologer') }}">Astrologer Consultation</a></li>
                            <li><a href="{{ url('/talk-to-astrologer') }}">Astrology reading</a></li>
                            <li><a href="{{ url('/talk-to-astrologer') }}">Astrologer online</a></li>
                            <li><a href="">Moon Signs /Ascendants</a></li>
                            <li><a href="">Nakshatras</a></li>
                            <li><a href="">Panchang Kaal Sarpyog</a></li>
                            <li><a href="">Shani Sade Sati</a></li>
                    </div>
                    </ul>
                </div>
                <div class="col-md-3 col-6 p-0">
                     <ul class="list-unstyled footer-link mt-4">
                        <!-- <li><a href="" class="link-bold"><img class="pb-2 foot-logo mob-foot" style="visibility: hidden;" src="/assets/astrologo.png" alt=""></a></li> -->
                        <li><a href="{{ url('/talk-to-astrologer') }}">Numerology Experts</a></li>
                        <li><a href="{{ url('/talk-to-astrologer') }}">Numerology Services</a></li>
                        <li><a href="{{ url('/talk-to-astrologer') }}">Tarot Experts</a></li>
                        <li><a href="{{ url('/talk-to-astrologer') }}">Tarot Services</a></li>
                        <li><a href="{{ url('/talk-to-astrologer') }}">PrashnaKundli</a></li>
                        <li><a href="{{ url('/talk-to-astrologer') }}">Vastu Experts</a></li>
                        <li><a href="{{ url('/talk-to-astrologer') }}">Vastu Services</a></li>
                        <li><a href="{{ url('/talk-to-astrologer') }}">Best Psychic readings</a></li>
                        <li><a href="{{ url('/talk-to-astrologer') }}">Kundli Matching/Gun Milan</a></li>
                        <li><a href="{{ url('/talk-to-astrologer') }}">Manglik Dosh</a></li>
                    </ul>
                </div>
                <div class="col-md-3 col-6 p-0">
                    <ul class="list-unstyled footer-link mt-4">
                        <!-- <li><a href="" class="link-bold"><img class="pb-2 foot-logo mob-foot" style="visibility: hidden;" src="/assets/astrologo.png" alt=""></a></li> -->
                        <li><p class="pb-1 pt-2 c-link"><b>Contact Us</b></p></li>
                        <li><a href="whatsapp://send?text=Tell%20us%20Whats%20Your%20Query!"><p class="contact-info"><img class="pr-1 c-icon" src="{{asset('/assets/Group 422.png')}}" alt="">  <span class="ss-pp"> +91-8860095202 </span></p></a></li>
                        <li><a href="mailto:support@talktoastro.com"><p class="contact-info"><img class="pr-1 c-icon" src="{{asset('/assets/Group 423.png')}}" alt=""> <span class="ss-pp">  support@talktoastro.com </span> </p> </a> </li>
                        <li><p class="contact-info"> <span class="ss-pp time-lim">  (Monday to Saturday 10:00 AM to 7:00 PM)</span> </p></li>
                        <li><p class="pt-3 c-link">  <b>Get In Touch</b> </p></li>
                    </ul>
                    <ul class="list-inline">
                        <li class="list-inline-item"><a href="#"><img class="social" src="{{asset('/assets/Icon awesome-facebook-f.png')}}" alt=""></a></li>
                        <li class="list-inline-item"><a href="#"><img class="social" src="{{asset('/assets/Icon awesome-linkedin.png')}}" alt=""></a></li>
                        <li class="list-inline-item"><a href="#"><img class="social" src="{{asset('/assets/Icon awesome-twitter.png')}}" alt=""></a></li>
                        <li class="list-inline-item"><a href="#"><img class="social" src="{{asset('/assets/Icon ionic-logo-whatsapp.png')}}" alt=""></a></li>
                    </ul>
                    <!-- <ul class="list-unstyled footer-link">
                    </ul> -->
                    <div class="desk-foot">
                        <ul class="list-inline">
                            <li class="list-inline-item"><span class="footer-policy"><a href="">Privacy Policy</a> | <a href="">Terms & Conditions</a></span></li>
                            <li class="list-inline-item"><a href=""><img class="foot-icon" src="{{asset('/assets/gplay.png')}}" alt=""></a></li>
                            <li class="list-inline-item"><a href=""><img class="foot-icon" src="{{asset('/assets/app-store.png')}}" alt=""></a></li>
                        </ul>
                    </div>
                    <div class="mob-foot">
                        <ul class="list-inline">
                            <li class="list-inline-item"><span class="footer-policy"><a href="">Privacy Policy</a> | <a href="">Terms & Conditions</a></span></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <div class="text-center">
            <p class="footer-alt mb-0 f-14">2021 © VG, All Rights Reserved</p>
        </div>
    </footer>
    <!-- =======  Footer End  ======= -->

    
    
    <!--   Facebook pixel purcase code (sur@j) -->
    <script>
        fbq('track', 'Purchase', {

            value: 200.00,

        });
    </script>
    
    <!-- Google Anlytics (sur@j) -->
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', 'UA-130258295-1');
    </script>
    
    
    
    <script>
	 tinymce.init({
        selector:'textarea#comment',
        width: 1100,
        height: 100
    });
    
    tinymce.init({
        selector:'textarea#exampleFormControlTextarea1',
        width: 900,
        height: 100
    });
    
	</script>
<script type="text/javascript">
    function generateCaptcha() {
        var alpha = new Array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
        var i;
        for (i = 0; i < 4; i++) {
            var a = alpha[Math.floor(Math.random() * alpha.length)];
            var b = alpha[Math.floor(Math.random() * alpha.length)];
            var c = alpha[Math.floor(Math.random() * alpha.length)];
            var d = alpha[Math.floor(Math.random() * alpha.length)];
        }
        var code = a + '' + b + '' + '' + c + '' + d;
        document.getElementById("mainCaptcha").value = code;
    }

    

    function CheckValidCaptcha() {
        var string1 = removeSpaces(document.getElementById('mainCaptcha').value);
        var string2 = removeSpaces(document.getElementById('txtInput').value);
        if (string1 == string2) {
            document.getElementById('success').innerHTML = "Form is validated Successfully";
            alert("Form is validated Successfully");
            return true;
        } else {
            document.getElementById('error').innerHTML = "Please enter a valid captcha.";
            alert("Please enter a valid captcha.");
            return false;

        }
    }

    function removeSpaces(string) {
        return string.split(' ').join('');
    }



</script>

    
    @if(@auth()->user()->user_type == 'astrologer')
<script>
    @if(request()->route()->getName() != "chat")
        function playSound() {
            APP_URL = {!! json_encode(url('/')) !!}
            var mp3Source = '';
            $.ajax({
                    type:'GET',
                    url:"{{url('getChatNotification')}}",
                    success:function(data) {
                            
                            if(data.status == true){
                                $('.chatNotificationMsg').html('');
                                $.each(data.data, function(key, value) {
                                    if(value.user_id != "{{ request()->user_id }}"){
                                        var mp3Source = '<source src="' +APP_URL+'/phone-ringing-sound-effect.mp3'+'" type="audio/mpeg">';
                                        document.getElementById("sound").innerHTML='<audio autoplay="autoplay">' + mp3Source + '</audio>';


                                        $('.chatNotificationMsg').append(`<div class="toast mb-2 border-warning text-center" role="alert" aria-live="assertive" aria-atomic="true">
                                            
                                            <div class="toast-body">
                                                Incoming Chat Request ...
                                            </div>
                                            <div class="toast-footer mb-2">
                                            <a class="btn btn-success consultNowBtn text-white mr-2" href="`+APP_URL+'/chat/'+value.id+`">Join</a>
                                            <a class="btn consultNowBtn btn-danger text-white" href="`+APP_URL+'/chat_remove?chatid='+value.id+`">Cancel</a>
                                            </div>
                                            </div>`);
                                    }
                                });

                            if(data.data.length < 1){
                                console.log("We are in else");
                                mutePage();
                            }
                            }else{
                                console.log('not login');
                            }                  
                    }
                    });

                setTimeout(playSound, interval);
            }
        setTimeout(playSound, interval);
        var interval = 3000;  // 1000 = 1 second, 3000 = 3 seconds
    @endif

    function muteMe(elem) {
        elem.muted = true;
        elem.pause();
    }
    function mutePage() {
        var elems = document.querySelectorAll("video, audio");
        [].forEach.call(elems, function(elem) { muteMe(elem); });
    }
    </script>

@endif


    @yield('script')
    
    
    <!--New HTML TEMPLTES CODE-->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" crossorigin="anonymous"></script>
    
    <script>
        //carousels-script
        $('#testimonials').owlCarousel({
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        loop: true,
        responsiveClass: true,
        nav: false,
        loop: true,
        stagePadding: 20,
        responsive: {
            300: {
            items: 1,
            margin: 50
            },
            400: {
            items: 1.5,
            margin: 50
            },
            600: {
            items: 1.5,
            margin: 50
            },
            1000: {
            items: 3,
            stagePadding: 0,
            loop:false,
            nav:true
            }
        }
        });

        var owl = $('#articles');
        owl.owlCarousel({
        loop:true,
        margin:10,
        autoplay:true,
        autoplayTimeout:5000,
        autoplayHoverPause:true,
        responsiveClass:true,
        nav: true,
        responsive:{
            500:{
                items:1,
                nav:true,
                stagePadding: 80,
            },
            900:{
                items:3,
                nav:true,
                loop:false
            },
            1000:{
                items:4,
                nav:false,
                loop:false
            }
        }
        });

        $('#astrotv').owlCarousel({
        loop:true,
        margin:10,
        responsiveClass:true,
        autoplay: 5000,
        nav:true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:2
            },
            1300:{
                items:4
            }
        }
    });

    </script>
    
    <script>
        //tab-control-script
        function openCity(evt, Name) {
          var i, tabcontent, tablinks;
          tabcontent = document.getElementsByClassName("tabcontent");
          for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
          }
          tablinks = document.getElementsByClassName("tablinks");
          for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
          }
          document.getElementById(Name).style.display = "block";
          evt.currentTarget.className += " active";
        }
        
        // Get the element with id="defaultOpen" and click on it
        document.getElementById("defaultOpen").click();
    </script>

    <script>
        //container-change-script
        jQuery(document).ready(function($) {
        var alterClass = function() {
            var ww = document.body.clientWidth;
            if (ww < 600) {
            $('.section').removeClass('container');
            } else if (ww >= 601) {
            $('.section').addClass('container');
            };
        };
        $(window).resize(function(){
            alterClass();
        });
        //Fire it when the page first loads:
        alterClass();
        });
    </script>

    <script>
        //bootstrap-modal
        $(document).ready(function(){
        function alignModal(){
            var modalDialog = $(this).find(".modal-dialog");
            
            // Applying the top margin on modal to align it vertically center
            modalDialog.css("margin-top", Math.max(0, ($(window).height() - modalDialog.height()) / 2));
        }
        // Align modal when it is displayed
        $(".modal").on("shown.bs.modal", alignModal);
        
        // Align modal when user resize the window
        $(window).on("resize", function(){
            $(".modal:visible").each(alignModal);
        });   
        });
    </script>

    <script>
        //Show-hide-password

        const togglePassword1 = document.querySelector('#togglePassword1');
        const pass1 = document.querySelector('#pass1');
        togglePassword1.addEventListener('click', function (e) {
        // toggle the type attribute
        const type = pass1.getAttribute('type') === 'password' ? 'text' : 'password';
        pass1.setAttribute('type', type);
        // toggle the eye slash icon
        this.classList.toggle('fa-eye-slash');
        });

        const togglePassword2 = document.querySelector('#togglePassword2');
        const pass2 = document.querySelector('#pass2');
        togglePassword2.addEventListener('click', function (e) {
        // toggle the type attribute
        const type = pass2.getAttribute('type') === 'password' ? 'text' : 'password';
        pass2.setAttribute('type', type);
        // toggle the eye slash icon
        this.classList.toggle('fa-eye-slash');
        });

        const togglePassword3 = document.querySelector('#togglePassword3');
        const pass3 = document.querySelector('#pass3');
        togglePassword3.addEventListener('click', function (e) {
        // toggle the type attribute
        const type = pass3.getAttribute('type') === 'password' ? 'text' : 'password';
        pass3.setAttribute('type', type);
        // toggle the eye slash icon
        this.classList.toggle('fa-eye-slash');
        });

    </script>

    <script>
        function moreServices() {
            var x = document.getElementsByClassName("more");
            var i;
            for (i = 0; i < x.length; i++) {
            x[i].style.display = "block";
            }
            document.getElementById("more-content").style.display = "none";
            document.getElementById("less-content").style.display = "block";
            document.getElementById("more-content-web").style.display = "none";
            document.getElementById("less-content-web").style.display = "block";
            
        }
        function lessServices() {
            var x = document.getElementsByClassName("more");
            var i;
            for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";
            }
            document.getElementById("more-content").style.display = "block";
            document.getElementById("less-content").style.display = "none";
            document.getElementById("more-content-web").style.display = "block";
            document.getElementById("less-content-web").style.display = "none";
        }
    </script>

    <!-- readmore-js -->
    <script src="{{ asset('/assets/js/cbpTooltipMenu.js') }}"></script>
    <script src="{{ asset('/assets/js/modernizr.custom.js') }}"></script>
    <script>
    var menu = new cbpTooltipMenu( document.getElementById( 'cbp-tm-menu' ) );
    </script>
    
     @yield('script')
   
</body>

</html>