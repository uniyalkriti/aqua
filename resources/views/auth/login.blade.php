@extends('layouts.loginmaster')

@section('title')

<?php $current_url_new = $_SERVER['HTTP_HOST'];
    $explode_array = explode('.',$current_url_new);
    // print_r ($explode_array);
  ?>
    <!-- <title>{{ config('app.name', '') }}</title> -->
    <title>Aqualabindia || Manacle Technologies Pvt Ltd.</title>
@endsection

@section('css')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<!-- <link href="https://fonts.googleapis.com/css2?family=Zen+Old+Mincho:wght@900&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="msell/css/common.css" />
     <style>

      section#formHolder {
    height: 100vh !important;
    display: flex;
    flex-direction: column;
    justify-content: center;
    }
      p.q img {
    width: 50px;
    margin-top: -20px;
}
        body {
  /* font-family: "Montserrat", sans-serif; */
  background: #f7edd5;
}

.container {
  max-width: 900px;
}

a {
  display: inline-block;
  text-decoration: none;
}

input {
  outline: none !important;
}

h1 {
  text-align: center;
  text-transform: uppercase;
  margin-bottom: 40px;
  font-weight: 700;
}

section#formHolder {
  /* padding: 14vh 0; */
}

.brand {
  padding: 20px;
  background: url(https://goo.gl/A0ynht);
  background-size: cover;
  background-position: center center;
  color: #fff;
  min-height: 540px;
  position: relative;
  box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.3);
  transition: all 0.6s cubic-bezier(1, -0.375, 0.285, 0.995);
  z-index: 9999;
}
.brand.active {
  width: 100%;
}
.brand::before {
  content: "";
  display: block;
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
  background: teal;
  z-index: -1;
}
.brand a.logo {
  color: #f95959;
  font-size: 20px;
  font-weight: 700;
  text-decoration: none;
  line-height: 1em;
}
.brand a.logo span {
  font-size: 30px;
  color: #fff;
  transform: translateX(-5px);
  display: inline-block;
}
.brand .heading {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
  transition: all 0.6s;
  width: 90%;
}
.brand .heading.active {
  top: 100px;
  left: 100px;
  transform: translate(0);
}
.brand .heading h2 {
  font-size: 3em;
  font-weight: 700;
  text-transform: uppercase;
  margin-bottom: 0;
}
.brand .heading p {
  font-size: 15px;
  font-weight: 300;
  text-transform: uppercase;
  letter-spacing: 2px;
  white-space: 4px;
  font-family: "Raleway", sans-serif;
}
.brand .success-msg {
  width: 100%;
  text-align: center;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  margin-top: 60px;
}
.brand .success-msg p {
  font-size: 25px;
  font-weight: 400;
  font-family: "Raleway", sans-serif;
}
.brand .success-msg a {
  font-size: 12px;
  text-transform: uppercase;
  padding: 8px 30px;
  background: #f95959;
  text-decoration: none;
  color: #fff;
  border-radius: 30px;
}
.brand .success-msg p, .brand .success-msg a {
  transition: all 0.9s;
  transform: translateY(20px);
  opacity: 0;
}
.brand .success-msg p.active, .brand .success-msg a.active {
  transform: translateY(0);
  opacity: 1;
}

.form {
  position: relative;
}
.form .form-peice {
  background: #fff;
  min-height: 480px;
  margin-top: 30px;
  box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.2);
  color: #bbbbbb;
  padding: 30px 0 60px;
  transition: all 0.9s cubic-bezier(1, -0.375, 0.285, 0.995);
  position: absolute;
  top: 0;
  left: -30%;
  width: 130%;
  overflow: hidden;
}
.form .form-peice.switched {
  transform: translateX(-100%);
  width: 100%;
  left: 0;
}
.form form {
  padding: 0 40px;
  margin: 0;
  width: 70%;
  position: absolute;
  top: 50%;
  left: 60%;
  transform: translate(-50%, -50%);
}
.form form .form-group {
  margin-bottom: 5px;
  position: relative;
}
.form form .form-group.hasError input {
  border-color: #f95959 !important;
}
.form form .form-group.hasError label {
  color: #f95959 !important;
}
.form form label {
  font-size: 12px;
  font-weight: 400;
  text-transform: uppercase;
  font-family: "Montserrat", sans-serif;
  transform: translateY(40px);
  transition: all 0.4s;
  cursor: text;
  z-index: -1;
}
.form form label.active {
  transform: translateY(10px);
  font-size: 10px;
}
.form form label.fontSwitch {
  font-family: "Raleway", sans-serif !important;
  font-weight: 600;
}
.form form input:not([type=submit]) {
  background: none;
  outline: none;
  border: none;
  display: block;
  padding: 10px 0;
  width: 100%;
  border-bottom: 1px solid #eee;
  color: #444;
  font-size: 15px;
  font-family: "Montserrat", sans-serif;
  z-index: 1;
}
.form form input:not([type=submit]).hasError {
  border-color: #f95959;
}
.form form span.error {
  color: #f95959;
  font-family: "Montserrat", sans-serif;
  font-size: 12px;
  position: absolute;
  bottom: -20px;
  right: 0;
  display: none;
}
.form form input[type=password] {
  color: #f95959;
}
.form form .CTA {
  margin-top: 30px;
}
.form form .CTA input {
  font-size: 12px;
  text-transform: uppercase;
  padding: 5px 30px;
  background: #f95959;
  color: #fff;
  border-radius: 30px;
  margin-right: 20px;
  border: none;
  font-family: "Montserrat", sans-serif;
}
.form form .CTA a.switch {
  font-size: 13px;
  font-weight: 400;
  font-family: "Montserrat", sans-serif;
  color: #bbbbbb;
  text-decoration: underline;
  transition: all 0.3s;
}
.form form .CTA a.switch:hover {
  color: #f95959;
}

footer {
  text-align: center;
}
footer p {
  color: #777;
}
footer p a, footer p a:focus {
  color: #b8b09f;
  transition: all 0.3s;
  text-decoration: none !important;
}
footer p a:hover, footer p a:focus:hover {
  color: #f95959;
}

@media (max-width: 768px) {
     section#formHolder {
    height: auto !important;

    }
  .container {
    overflow: hidden;
  }
  .brand .heading p{
    font-size:14px;
  }
  .form form{
    top:25%;
  }
  a.logo {
    display: flex;
    justify-content: center;
  }
  .heading h2 {
    font-size: 30px !important;
  }

  section#formHolder {
    padding: 0;
  }
  section#formHolder div.brand {
    min-height: 500px !important;
  }
  section#formHolder div.brand.active {
    min-height: 100vh !important;
  }
  section#formHolder div.brand .heading.active {
    top: 200px;
    left: 50%;
    transform: translate(-50%, -50%);
  }
  section#formHolder div.brand .success-msg p {
    font-size: 16px;
  }
  section#formHolder div.brand .success-msg a {
    padding: 5px 30px;
    font-size: 10px;
  }
  section#formHolder .form {
    width: 80vw;
    min-height: 500px;
    margin-left: 10vw;
  }
  section#formHolder .form .form-peice {
    margin: 0;
    top: 0;
    left: 0;
    width: 100% !important;
    transition: all 0.5s ease-in-out;
  }
  section#formHolder .form .form-peice.switched {
    transform: translateY(-100%);
    width: 100%;
    left: 0;
  }
  section#formHolder .form .form-peice > form {
    width: 100% !important;
    padding: 60px;
    left: 50%;
  }
}
@media (max-width: 480px) {
  section#formHolder .form {
    width: 100vw;
    margin-left: 0;
  }

  h2 {
    font-size: 50px !important;
  }
}

 #grv_hide_send, #grv_hide_send1{
     display:none;
 }
 .scan_img img {
    width: 70px !important;
    position: absolute;
    bottom: 20px;
    right: 20px;
}
    </style>
@endsection

@section('body')
<?php $current_url = $_SERVER['HTTP_HOST'];
    // echo $current_url;
  ?>
<div class="container">
   <section id="formHolder">

      <div class="row">

         <!-- Brand Box -->
         <div class="col-sm-6 brand">
            <a href="#" class="logo"><img src="http://demo.msell.in/public/msell/images/logo.png" width="100px" alt=""></a>

            <div class="heading">
               <h2 style="font-family: 'Zen Old Mincho', serif; letter-spacing:2px; word-spacing:4px; font-weight:600 !important; ">Sales Force Automation</h2>
               <p style="margin-top:40px;" class="q">
               <img src="http://demo.msell.in/public/company-profile/comma.png">
               Use This Portal To Manage Your Sales, Updates,
Announcements, New Product Launch, Notification, Tour Entries, Attendance, Personal Details ,Maintain Distributors, Outlets....etc.</p>
            </div>
            <div class="scan_img">
                <img src="http://demo.msell.in/public/msell/images/msell_qr_code.png" alt="">
            </div>

            <div class="success-msg">
               <p>Great! You are one of our members now</p>
               <a href="#" class="profile">Your Profile</a>
            </div>
         </div>


         <!-- Form Box -->
         <div class="col-sm-6 form">

            <!-- Login Form -->
            <div class="login form-peice">
               <form id="validation-form" method="post" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                                            {!! csrf_field() !!}
                  <div class="form-group">
                     <label for="loginemail">Email Adderss</label>
                     <input type="email" name="email" id="loginemail" required>
                  </div>

                  <div class="form-group">
                     <label for="loginPassword">Password</label>
                     <input type="password" name="password" id="loginPassword" required>
                  </div>

                  <div class="CTA">
                     <input type="submit" value="Login">
                     <a href="#" class="switch">Forget Password</a>
                  </div>
               </form>
            </div><!-- End Login Form -->


            <!-- Signup Form -->
            <div class="signup form-peice switched">
               <form class="signup-form" action="#" method="post">

                  <!-- <div class="form-group">
                     <label for="name">Full Name</label>
                     <input type="text" name="username" id="name" class="name">
                     <span class="error"></span>
                  </div> -->

                  <div class="form-group">
                     <label for="email">Email Adderss</label>
                     <input type="email" name="emailAdress" id="email" class="email">
                     <span class="error"></span>
                  </div>

                  <!-- <div class="form-group">
                     <label for="phone">Phone Number - <small>Optional</small></label>
                     <input type="text" name="phone" id="phone">
                  </div> -->

                  <!-- <div class="form-group">
                     <label for="password">Password</label>
                     <input type="password" name="password" id="password" class="pass">
                     <span class="error"></span>
                  </div> -->

                  <!-- <div class="form-group">
                     <label for="passwordCon">Confirm Password</label>
                     <input type="password" name="passwordCon" id="passwordCon" class="passConfirm">
                     <span class="error"></span>
                  </div> -->

                  <div class="CTA">
                     <input type="submit" value="Send OTP" id="submit2" onclick="funSend()">
                     <a href="#" class="switch">Go Back</a>
                  </div>

                  <div id="grv_hide_send">
                        <div class="form-group">
                            <label for="password">Enter OTP</label>
                            <input type="password" name="password" id="password" class="pass">
                            <span class="error"></span>
                        </div>

                        <div class="CTA">
                            <input type="submit" value="Submit" id="submit3" onclick="funSend1()">

                        </div>
                  </div>
                  <div id="grv_hide_send1">
                      <h5>Please check your E-mail for temporary password.</h5>
                  </div>


               </form>
            </div><!-- End Signup Form -->
         </div>
      </div>

   </section>


   

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
       <script>

        /global $, document, window, setTimeout, navigator, console, location/
$(document).ready(function () {

'use strict';

var usernameError = true,
    emailError    = true,
    passwordError = true,
    passConfirm   = true;

// Detect browser for css purpose
if (navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
    $('.form form label').addClass('fontSwitch');
}

// Label effect
$('input').focus(function () {

    $(this).siblings('label').addClass('active');
});

// Form validation
$('input').blur(function () {

    // User Name
    if ($(this).hasClass('name')) {
        if ($(this).val().length === 0) {
            $(this).siblings('span.error').text('Please type your full name').fadeIn().parent('.form-group').addClass('hasError');
            usernameError = true;
        } else if ($(this).val().length > 1 && $(this).val().length <= 6) {
            $(this).siblings('span.error').text('Please type at least 6 characters').fadeIn().parent('.form-group').addClass('hasError');
            usernameError = true;
        } else {
            $(this).siblings('.error').text('').fadeOut().parent('.form-group').removeClass('hasError');
            usernameError = false;
        }
    }
    // Email
    if ($(this).hasClass('email')) {
        if ($(this).val().length == '') {
            $(this).siblings('span.error').text('Please type your email address').fadeIn().parent('.form-group').addClass('hasError');
            emailError = true;
        } else {
            $(this).siblings('.error').text('').fadeOut().parent('.form-group').removeClass('hasError');
            emailError = false;
        }
    }

    // PassWord
    if ($(this).hasClass('pass')) {
        if ($(this).val().length < 8) {
            $(this).siblings('span.error').text('Please type at least 8 charcters').fadeIn().parent('.form-group').addClass('hasError');
            passwordError = true;
        } else {
            $(this).siblings('.error').text('').fadeOut().parent('.form-group').removeClass('hasError');
            passwordError = false;
        }
    }

    // PassWord confirmation
    if ($('.pass').val() !== $('.passConfirm').val()) {
        $('.passConfirm').siblings('.error').text('Passwords don\'t match').fadeIn().parent('.form-group').addClass('hasError');
        passConfirm = false;
    } else {
        $('.passConfirm').siblings('.error').text('').fadeOut().parent('.form-group').removeClass('hasError');
        passConfirm = false;
    }

    // label effect
    if ($(this).val().length > 0) {
        $(this).siblings('label').addClass('active');
    } else {
        $(this).siblings('label').removeClass('active');
    }
});


// form switch
$('a.switch').click(function (e) {
    $(this).toggleClass('active');
    e.preventDefault();

    if ($('a.switch').hasClass('active')) {
        $(this).parents('.form-peice').addClass('switched').siblings('.form-peice').removeClass('switched');
    } else {
        $(this).parents('.form-peice').removeClass('switched').siblings('.form-peice').addClass('switched');
    }
});


// Form submit
$('form.signup-form').submit(function (event) {
    event.preventDefault();

    if (usernameError == true || emailError == true || passwordError == true || passConfirm == true) {
        $('.name, .email, .pass, .passConfirm').blur();
    } else {
        $('.signup, .login').addClass('switched');

        setTimeout(function () { $('.signup, .login').hide(); }, 700);
        setTimeout(function () { $('.brand').addClass('active'); }, 300);
        setTimeout(function () { $('.heading').addClass('active'); }, 600);
        setTimeout(function () { $('.success-msg p').addClass('active'); }, 900);
        setTimeout(function () { $('.success-msg a').addClass('active'); }, 1050);
        setTimeout(function () { $('.form').hide(); }, 700);
    }
});

// Reload page
$('a.profile').on('click', function () {
    location.reload(true);
});


});
    </script>

    <script>
        function funSend(){
            var grv_resend = "Resend OTP";
            var hideshow = document.getElementById("grv_hide_send");
            document.getElementById("submit2").value = grv_resend;
            hideshow.style.display = "block";
            
            
        }

        function funSend1(){

            var hideshow1 = document.getElementById("grv_hide_send1");
            hideshow1.style.display = "block";


        }

    </script>
@endsection