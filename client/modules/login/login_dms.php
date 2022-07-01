<?php 
if (!defined('BASE_URL'))
	require_once('../../page_not_direct_allow.php');
?>
<script type="text/javascript">
$('document').ready(function() {
  setTimeout(function(){
    $("span.warn").fadeOut("slow", function () {
    $("span.warn").remove();
      });
  }, 6000);
});
</script>
<style type="text/css">
    .blue{color:#ef1828 !important;}.green{color:#ef1828 !important;}
</style>
<?php
	if(!empty($_POST['uname']) && !empty($_POST['pass']))
	{
        $q = "SELECT d.edit_stock as edit_stock, dpId AS id,d.csa_id as csa_id, role_id AS urole,dealer_person_login.state_id,d.name as dealer_name, person_name AS name, reason, bitactive, ipaddress, activestatus, uname,role_group_id,dealer_id, DATE_FORMAT(lastvisit,'%e %M %Y at %r IST') AS lastlogin,state_id FROM dealer_person_login"
        . " INNER JOIN _role USING(role_id) INNER JOIN dealer d on dealer_person_login.dealer_id=d.id "
        . "WHERE uname = '$_POST[uname]' && AES_DECRYPT(dealer_person_login.pass, '".EDSALT."') = '$_POST[pass]' AND bitactive = 1 LIMIT 1";
                  //echo $q; exit;

	 	$_SESSION[SESS.'worktoken'] = date('YmdHis');
		list($output, $data) = run_query($dbc, $q, $mode='single', $msg='Sorry username or password do not match.');
		if($output)
		{					
			if($data['activestatus'] == '1')
			{
				$q_s = "SELECT * FROM session_year ORDER BY sesId DESC LIMIT 1";
				$r_s = mysqli_query($dbc,$q_s);
				$d_s = mysqli_fetch_assoc($r_s);
                                
				$_SESSION[SESS.'user'] = true;
				$_SESSION[SESS.'data'] = $data;
				$_SESSION[SESS.'id'] = $data['id'];	
                $_SESSION[SESS.'file_id'] = date('Hi');	
				$_SESSION[SESS.'sess'] = $d_s;
				$_SESSION[SESS.'csess'] = $d_s['sesId'];
                $_SESSION[SESS . 'data']['state_id'] = $data['state_id'];
                $_SESSION[SESS . 'data']['csa_margin'] = myrowval_assoc('ss_margin', 'margin,division','division','ss_id='.$data['csa_id']);
                $_SESSION[SESS.'data']['company_id'] = 0;
                $q_s = "SELECT * FROM _constant LIMIT 1";
				$r_s = mysqli_query($dbc,$q_s);
				$d_s = mysqli_fetch_assoc($r_s);				
                $_SESSION[SESS.'constant'] = $d_s;
				// updating user login time, ip address, login status into users table starts	
				$q = "UPDATE dealer_person_login SET lastvisit = NOW(), ipaddress = '$_SERVER[REMOTE_ADDR]' WHERE uname = '$_POST[uname]'  LIMIT 1";
				$r = mysqli_query($dbc,$q);
				// Redirect to the index page:
                $case = $_POST['case'];
				$url = BASE_URL . 'index.php?option='.$case;
				header ("Location: $url");
				exit();
			}elseif($data['activestatus'] == 'hold')
				echo'<span class="awm">Your <strong>A/c is on Hold</strong>, for the reason below:<br>'.$data['reason'].'</span>';
		}
		else
			echo'<span class="awm">Sorry username or password do not match.</span>';
	}
	else
		echo'<span class="awm">Please fill all the required fields.</span>';

?> 
<body class="login-layout" style="background:url(./images/maxresdefault.png) no-repeat center center fixed; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;">
    <div class="main-container">
        <div class="main-content">
            <div class="row">
                <div class="col-sm-10 col-sm-offset-1">
                    <div class="login-container">
                        <div class="center">
                            <h1 style="margin:20px 0 0 20px; border-bottom: 0px; ">
                               <!-- <img src="./images/logo.png" style="size:50px"/>
                                <span class="red">DISTRIBUTOR</span>
                                <span class="white" id="id-text2">PANEL</span> -->
                            </h1>
<!--                            <h4 class="blue" id="id-company-text">&copy; MANACLE INDIA Pvt.Ltd.</h4>-->
                        </div>

                        <div class="space-6"></div>

                        <div class="position-relative">
                            <div id="login-box" class="login-box visible widget-box no-border">
                                <div class="widget-body">
                                    <div class="widget-main">
                                        <h4 class="header blue lighter bigger" style="border-bottom-color:#ef1828;">
                                            <i class="ace-icon fa fa-coffee green"></i>
                                           Please Login to Continue
                                        </h4>
                                       <form name="login" autocomplete="off" method="post" action="index.php" onsubmit="return checkForm('login');">
    
                                            <fieldset>
                                                <label class="block clearfix">
                                                    <span class="block input-icon input-icon-right">
                                                        <input type="text" name="uname" id="rname" lang="Username" class="form-control" placeholder="Username" />
                                                        <i class="ace-icon fa fa-user"></i>
                                                    </span>
                                                </label>

                                                <label class="block clearfix">
                                                    <span class="block input-icon input-icon-right">
                                                        <input style="height:35px; width: 100%;"  name="pass" lang="Password"   type="password" class="form-control" placeholder="Password" />
                                                        <i class="ace-icon fa fa-lock"></i>
                                                    </span>
                                                </label>

                                                <div class="space"></div>

                                                <div class="clearfix">
                                                    <!-- <label class="inline">
                                                        <input type="checkbox" class="ace" />
                                                        <span class="lbl"> Remember Me</span>
                                                    </label> -->
                                                        <input type="hidden" name="submitted" value="true" />
                                                    <button type="submit" name="submit" class="width-35 pull-right btn btn-sm btn-primary" style="background-color: #ef1828! important; border-color: #ef1828 !important;">
                                                        <i class="ace-icon fa fa-key"></i>
                                                        <span class="bigger-110">Login</span>
                                                    </button>
                                                </div>

                                                <div class="space-4"></div>
                                            </fieldset>
                                        </form>

                                        <div class="space-6"></div>

                                     
                                    </div><!-- /.widget-main -->

                                   
                                </div><!-- /.widget-body -->
                            </div><!-- /.login-box -->

                        </div><!-- /.position-relative -->

                    </div>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.main-content -->
    </div><!-- /.main-container -->
    <div class="footer">
                    <div class="footer-inner">
                        <!-- #section:basics/footer -->
                        <div class="footer-content">
                            <span class="bigger-120">                            
                                 © Copyright 2017-2018, Design & Developed by <a href="http://manacleindia.com/" style="text-decoration:underline;color:#ffffff;">Manacle Technologies Pvt. Ltd.</a> 
                            </span>                    
                            
                        </div>

                        <!-- /section:basics/footer -->
                    </div>
                </div>
</body>

<!--	<div id="showlogin" style="border:1px solid #787474; margin:10% auto; padding:5px; background-color:#FFF; width:400px;">
      <p style="border-bottom:2px solid #787474; text-align:center; padding-bottom:5px; font-size:1.1em; font-weight:bold;">Please login to continue</p>
      <form name="login" autocomplete="off" method="post" action="index.php" class="lform" onsubmit="return checkForm('login');">
      <table width="100%" border="0" cellspacing="5" cellpadding="5">
        <tr>
          <td class="txtright">Username <span class="star">*</span> :</td>
          <td><input type="text" name="uname" id="rname" lang="Username" /></td>
        </tr>
        <tr>
          <td class="txtright">Password <span class="star">*</span> :</td>
          <td><input type="password" name="pass" lang="Password"  /></td>
        </tr>
        <tr>
          <td colspan="2" align="center"><input type="submit" name="submit" value="Log In" /><input type="hidden" name="submitted" value="true" /></td>
        </tr>
      </table>
      </form>
    </div>-->
    <script type="text/javascript">
    /*jwerty.key('alt+ctrl+l', showlogin);
	function showlogin()
	{
		$('#showlogin').toggle();
		setfocus('rname');
		return false
	}*/
    </script>
