<?php 
/*$dbc = mysqli_connect('localhost','root','','medgyan');
// This class will handle all the task related to purchase order creation
include_once('myfilter.php');*/
class customers extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	// function will be contain the address of customer 
	public function get_customer_adr($id)
	{	
	    global $dbc;
		$out=array();
		$q="SELECT * FROM customer_address where custId='$id'";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['adr_type'];
			//$out[$id]['custId'] = $row['custId'];
			$out[$id]['adr_line1'] = $row['adr_line1'];
			$out[$id]['adr_line2'] = $row['adr_line2'];
			$out[$id]['locality'] = $row['locality'];
			$out[$id]['landmark'] = $row['landmark'];
			$out[$id]['city_district']= $row['city_district'];
			$out[$id]['state']= $row['state'];
			$out[$id]['pincode']= $row['pincode'];
			$out[$id]['country']= $row['country'];
		}
    return $out;
	 }
	 //function will be sget the data of front end customer post value 
	public function get_se_data($saver=1)
	{		
		if(isset($_POST['saver'])) $saver = $_POST['saver']; //indicate whether creating a/c via front or back end
		$name = $_POST['title'].' '.$_POST['firstname'].' '.$_POST['lastname'];
		$d1 = array('username'=>$_POST['username'], 'email'=>$_POST['email'], 'pass'=>$_POST['pass'], 'gender'=>$_POST['gender'], 'title'=>$_POST['title'], 'firstname'=>$_POST['firstname'], 'lastname'=>$_POST['lastname'], 'name'=>$name, 'filename'=>'', 'mobile'=>$_POST['mobile']);
		$d1['mobile_verified'] = isset($_POST['mobile_verified']) ? $_POST['mobile_verified'] : 0;
		$d1['dob'] = isset($_POST['dob']) ? $_POST['dob'] : '';
		$d1['anv'] = isset($_POST['anv']) ? $_POST['anv'] : '';
		$d1['actcode'] = $saver == 1 ? md5(uniqid(rand(), true)) : '';		
		$d1['blacklist'] = isset($_POST['blacklist']) ? $_POST['blacklist'] : 0;
		$d1['registered_via'] = isset($_POST['registered_via']) ? $_POST['registered_via'] : 1;
		$d1['blacklist_reason'] = isset($_POST['blacklist_reason']) ? $_POST['blacklist_reason'] : '';
		$d1['myreason'] = 'Please fill all the required information';		
		//$d2 = array('itemId'=>$_POST['itemid'], 'rate'=>$_POST['cpc'], 'qty'=>$_POST['qty'], 'desc'=>$_POST['desc']);
		return array(true, $d1);
	}
	// This function will save the customer front end details
	public function customer_save($saver='')
	{
		global $dbc;
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		$dob  = !empty($d1['dob']) ? get_mysql_date($d1['dob']) : '';
		$anv  = !empty($d1['anv']) ? get_mysql_date($d1['anv']) : '';
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		//$actcode = md5(uniqid(rand(), true));
		 $q = "INSERT INTO `customers` (`custId`, `present_ac_status`, `username`, `email`, `pass`, `gender`, `title`, `firstname`, `lastname`, `name`, `filename`, `mobile`, `mobile_verified`, `dob`, `anv`, `actcode`, `passwordtoken`, `lastlogin`, `lastlogout`, `ipaddress`, `blacklist`, `blacklist_reason`, `registered_via`, `created`, `modified`, `activation_date`, `activeBit`) VALUES (NULL, '1', '$d1[username]', '$d1[email]', AES_ENCRYPT('$d1[pass]', '".EDSALT."'), '$d1[gender]', '$d1[title]', '$d1[firstname]', '$d1[lastname]', '$d1[name]', '$d1[filename]', '$d1[mobile]', '$d1[mobile_verified]', '$dob', '$anv', '$d1[actcode]', NULL, NULL, NULL, NULL, '$d1[blacklist]', '$d1[blacklist_reason]', '$d1[registered_via]', NOW(), NULL, NULL, '1');";
		//$q = "INSERT INTO `customers` (`custId`,`reg_category`,`reg_category_verified`,`present_ac_status`, `username`,`email`,`pass`,`gender`,`title`,`firstname`,`lastname`, `mobile`, `actcode`,`ipaddress`,`registered_via`,`created`,`activeBit`) VALUES (NULL , '$d1[reg_category]',0,1, '$d1[username]',  '$d1[email]',,'$d1[gender]','$d1[title]','$d1[firstname]','$d1[lastname]', '$d1[mobile]', '$actcode','$_SERVER[REMOTE_ADDR]',1,NOW(),1)";		
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'customer table error'); }
		$rId = mysqli_insert_id($dbc);		
		//$extrawork = $this->customer_save_extrawork($rId);
		//if(!$extrawork)  return array('status'=>false, 'myreason'=>'Customer extended setting failed.');
		mysqli_commit($dbc);
		//history_log($dbc, 'Add', 'Party added with Id '.$rId. ' '.$d1['name']);
		//send a message on user mobile
		//sendToMobile('Thanks for registering with medgyan.com. Ur A/c details are sent at '.$d1['email'].'. For any queries pl login or email at www.medgyan.com',$d1['mobile']);		
		//if($d1['registered_via'] == 1) $this->user_send_welcome_email($rId);
		return array('status'=>true, 'myreason'=>'Customer successfully Saved', 'rId'=>$rId);
	}
	//This function will do all the extra work when we save new customer from the front end
	public function customer_save_extrawork($custId)
	{
		global $dbc;
		$out = false;
		// saving the customer profile
		$r = mysqli_query($dbc, $q="INSERT INTO `customer_profile` (custId) VALUES($custId)");
		if(!$r) return $out;
		// saving the customer address
		$reskey = $custId.'1'; $ofckey = $custId.'2';
		$r = mysqli_query($dbc, $q="INSERT INTO `customer_address` (pkey, custId, adr_type) VALUES('$reskey', $custId, 1), ('$ofckey', $custId, 2)");
		if(!$r) return $out;
		// saving the customer group
		list($opt, $rs) = run_query($dbc, $q="SELECT gId FROM group_name WHERE gname='General' LIMIT 1", $mode='single', $msg='');
		if(!$opt){
				$r = mysqli_query($dbc, $q="INSERT INTO `group_name` (gId, gname) VALUES(NULL, 'General')");
				if(!$r) return $out;
				$gId = mysqli_insert_id($dbc);				
		}
		else $gId = $rs['gId'];
		$r = mysqli_query($dbc, $q="UPDATE `customers` SET gId = $gId WHERE custId = $custId LIMIT 1");
		if(!$r) return $out;
		// saving the customer package
		$package = new package();
		$package->set_customer_package_f_or_n($custId, 'New User');
		return true;
	}
	// This function will save the details of a given party via open login
	public function customer_openlogin_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
	//	$q = "INSERT INTO `customers` (`custId`, `custname`, `email`, `pass`, `mobile`, `actcode`, `created`, `registered_via`, `activeBit`) VALUES (NULL , '$d1[custname]', '$d1[email]',  '$d1[pass]', '$d1[mobile]','$di[actcode', NOW(),'$d1[registered_via]', 1)";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'customer table error'); }
		$rId = mysqli_insert_id($dbc);		
		mysqli_commit($dbc);
		$this->customer_send_welcome_email_openlogin($rId);
		return array('status'=>true, 'myreason'=>'Customer successfully Saved', 'rId'=>$rId);
	}
	// function will be send email to user email id
	public function user_send_welcome_email($custId)
	{
		global $dbc;	
		$q = "SELECT username,firstname, email, actcode,  AES_DECRYPT(pass, '".EDSALT."') as pass FROM customers WHERE custId = $custId";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if($opt)
		{
			$username=$rs['username'];
			$email = $rs['email'];
			$pass = $rs['pass'];
			$randcode = $rs['actcode'];
			$link = '<a href="'.BASE_URL.'index.php?option=activation&username='.$username.'&code='.$randcode.'">'.BASE_URL.'index.php?option=activation&username='.$username.'&code='.$randcode.'</a>';
			$mailbody = "Dear <b>{$rs['firstname']}</b>, <br/><br/> Welcome to medgyan.com.<br/> <br/> Your medgyan account has been successfully created. <br/><br/> <b>To activate your account please click on the link below:</b> <br/><br/> $link <br/><br/>  Before you can login, you need to verify your account by clicking the link above.<br/><br/> <b>Your LoginId is : <span style=\"color:#5e9a43;\">$username</span> </b><br/><b>Your account password is : <span style=\"color:#5e9a43;\">$pass</span> </b><br/><br/> Regards,<br/> <b>Team Medgyan.com</b> <br/> <a href=\"http://www.medgyan.com\">medgyan.com</a>";
			
			$subject = 'Welcome to Medgyan';
			mailsend(LOCAL_HOST_MAIL, $to=$rs['email'], $subject, $mailbody, $from='info@medgyan.com', $toname=$rs['username'], $fromname='Medgyan');	
		}
	}
	
	//This function will send a mail to the customer registered via open-login
	public function customer_send_welcome_email_openlogin($custId)
	{
		global $dbc;	
		$q = "SELECT custname,firstname, email, pass FROM customers WHERE custId = $custId";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if($opt)
		{
			$username=$rs['username'];
			$email = $rs['email'];
			$pass = $rs['pass'];
			$mailbody = "Dear <b>{$rs['firstname']}</b>, <br/><br/> Welcome to Uandifurniture.com.<br/> <br/> Your Uandifurniture account has been successfully created. <br/><br/> <b>Your A/c detail are:</b> <br/><br/> <b>Your LoginId is : <span style=\"color:#5e9a43;\">$username</span> </b><br/><b>Your account password is : <span style=\"color:#5e9a43;\">$pass</span> </b><br/><br/> Regards,<br/> <b>Team Uandifurniture</b> <br/> <a href=\"http://www.uandifurniture.com\">Uandifurniture.com</a>";
			$subject = 'Welcome to Uandifurniture';
			mailsend(LOCAL_HOST_MAIL, $to=$rs['email'], $subject, $mailbody, $from='info@uandifurniture.com', $toname=$rs['custname'], $fromname='Uandifurniture');	
		}
	}
	
	public function customer_login($email, $pass, $logincase='NORMAL')
	{
		global $dbc;
		//Check the case via which the user is trying to login in the system.
		if($logincase == 'NORMAL')// if user is logining via our website form
		{
			// if user loging via the master account
			/*if($email == 'medgyan' && $pass == 'services'){
				$_SESSION[FSESS.'master'] = 0;
				$_SESSION[FSESS.'masterlogin'] = true;				
				$rml = mysqli_query($dbc,"INSERT INTO masterlogin (mloginId, lastlogin, ipaddress) VALUES(NULL, NOW(), '{$_SERVER['REMOTE_ADDR']}')");
				$_SESSION[FSESS.'mloginId'] = mysqli_insert_id($dbc);
				return array('status'=>true, 'myreason'=>'');
			}*/
			$q = "SELECT *,DATE_FORMAT(lastlogin,'%e %M %Y at %r IST') AS lastlogin FROM customers WHERE username = '$email' AND AES_DECRYPT(pass, '".EDSALT."') = '$pass' AND activeBit = 1 LIMIT 1";
		
		}
		elseif($logincase == 'OPENLOGIN')// if user is logining facebook, gmail or some other service
			$q = "SELECT *,DATE_FORMAT(lastlogin,'%e %M %Y at %r IST') AS lastlogin FROM customers WHERE username = '$email' AND activeBit = 1 LIMIT 1";
		elseif($logincase == 'Masterlogin')// if user is logining facebook, gmail or some other service
		{
			$q = "SELECT *,DATE_FORMAT(lastlogin,'%e %M %Y at %r IST') AS lastlogin FROM customers WHERE custId = '$email' AND activeBit = 1 LIMIT 1";	
			//unset($_SESSION[FSESS.'masterlogin']); // so that user can not change the multiple account in single login
		}
		else // this case of login is not allowed
			return array('status'=>false, 'myreason'=>'Login not allowed');
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if(!$opt) return array('status'=>false, 'myreason'=>'Username or  password do not match');
		if($opt)
		{					
			if($rs['actcode'] != '') return array('status'=>false, 'myreason'=>'Please activate your account');
			if($rs['blacklist'] == 1) return array('status'=>false, 'myreason'=>'Your account is blacklisted for reason below : <hr>'.$rs['blacklist_reason']);
			$_SESSION[FSESS.'uname'] = $rs['username'];
			$_SESSION[FSESS.'filename'] = $rs['filename'];
			$_SESSION[FSESS.'data'] = $rs;
		echo	$_SESSION[FSESS.'id'] = $rs['custId'];
			// updating user login time, ip address, login status into users table starts	
		/*	$q = "UPDATE customers SET lastlogin = NOW(), ipaddress = '$_SERVER[REMOTE_ADDR]' WHERE username = '$email'  LIMIT 1";
			$r = mysqli_query($dbc,$q);*/
		//	$this ->chk_exp();
		//	return array('status'=>true, 'myreason'=>'');
		}
	}
	
	//This function will return the customer name and email for a supplied id
	public function activateaccount($username='', $actcode='')
	{
		global $dbc;
		if(empty($username)) return array(false,'Required parameters Email missing');
		if(empty($actcode) || strlen($actcode) != 32) return array(false,'Required parameters Activation Code missing/invalid');
		list($opt, $rs) = run_query($dbc, $q="SELECT actcode FROM customers  WHERE username = '$username' LIMIT 1", $mode='single', $msg='sorry no  record found');
		if($opt) 
		{
			if($rs['actcode'] == '')
			{
				return array(true,'Your account is already active');
			}
			elseif($rs['actcode'] != $actcode)
				return array(false,'Activation code is invalid');
			else
			{
				if(mysqli_query($dbc,"UPDATE customers SET actcode='',acc_active_date=NOW() WHERE username='$username' LIMIT 1"))
					return array(true,'Account successfully <strong>activated</strong>.');
				else
					return array(false,'Some error occured, account could not be activated<br/>try again after sometime');
			}
		}
		else
			return array(false,'Sorry user account do not exists.');
	}
	
	// When we want to add/modify the customer details from the backend
	public function get_bse_data($mode='add')
	{		
		$d1 = array('custname'=>$_POST['custname'], 'email'=>$_POST['email'], 'pass'=>$_POST['pass'], 'mobile'=>$_POST['mobile'], 'adr'=>$_POST['adr'], 'actcode'=>$_POST['actcode'], 'newsletter'=>$_POST['newsletter'], 'ac_blacklist'=>$_POST['ac_blacklist'], 'blacklist_reason'=>$_POST['blacklist_reason'], 'registered_via'=>'Back End');
		
		$d1['myreason'] = 'Please fill all the required information';		
		//$d2 = array('itemId'=>$_POST['itemid'], 'rate'=>$_POST['cpc'], 'qty'=>$_POST['qty'], 'desc'=>$_POST['desc']);
		return array(true, $d1);
	}
	
	// This function will save the details of a given party
	public function customerbackend_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$actcode = md5(uniqid(rand(), true));
		$q = "INSERT INTO `customers` (`custId`, `custname`, `email`, `pass`, `mobile`, `adr`, `actcode`, `newsletter`, `ac_blacklist`, `blacklist_reason`, `created`, `registered_via`, `activeBit`) VALUES (NULL , '$d1[custname]', '$d1[email]',  '$d1[pass]', '$d1[mobile]',  '$d1[adr]',  '$d1[actcode]',  '$d1[newsletter]', '$d1[ac_blacklist]', '$d1[blacklist_reason]', NOW(), '$d1[registered_via]', 1)";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'customer table error'); }
		$rId = mysqli_insert_id($dbc);	
		history_log($dbc, 'Added', 'Customer added with Id '.$rId.' '.$d1['custname']);
		return array('status'=>true, 'myreason'=>'Customer successfully Saved', 'rId'=>$rId);
	}
	
	// This function will edit cousomer details
	public function customerbackend_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		$dob  = !empty($d1['dob']) ? get_mysql_date($d1['dob']) : '';
		mysqli_query($dbc, "START TRANSACTION");
		 $q = "UPDATE customers SET `username`='$d1[username]', `email`='$d1[email]', `pass`=AES_ENCRYPT('$d1[pass]', '".EDSALT."'), `mobile`='$d1[mobile]', `title`='$d1[title]', `firstname`='$d1[firstname]', `lastname`='$d1[lastname]', `name`='$d1[name]', `dob`='$dob', `gender`='$d1[gender]' WHERE custId = '$id' LIMIT 1";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Customer table error');}
		mysqli_commit($dbc);
		//history_log($dbc, 'Edit', 'Customer updated with Id '.$id.' '.$d1['name']);
		return array('status'=>true, 'myreason'=>'Customer successfully updated', 'custId'=>$id);
	}
	//This function will return the list of as reflected from function name
	public function get_customer_list($filter='',  $records = '', $orderby ='')
	{
		global $dbc;
		$out = array();	
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
	
	
	   $q = "SELECT *,firstname,lastname,title,email,AES_DECRYPT(pass, '".EDSALT."') as pass,mobile,DATE_FORMAT(dob,'%d/%m/%Y') AS dob ,DATE_FORMAT(created,'%d/%m/%Y') AS created FROM customers ".$filterstr."";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['custId'];
			$out[$id]['custId'] = $id;
			$out[$id]['username'] = $row['username'];
			$out[$id]['email'] = $row['email'];
			$out[$id]['pass'] = $row['pass'];
			$out[$id]['gender'] = $row['gender'];
			$out[$id]['title'] = $row['title'];
			$out[$id]['firstname'] = $row['firstname'];
			$out[$id]['lastname'] = $row['lastname'];
			$out[$id]['name'] = $row['name'];
			$out[$id]['filename'] = $row['filename'];
			$out[$id]['mobile'] = $row['mobile'];        
			
			$out[$id]['dob'] = $row['dob'];
			$out[$id]['created'] = $row['created'];
			
			
		}
		return $out;
	}
	
	public function forgot_password($username)
	{
	//	echo $email;
   //		return array(TRUE,'hello');
	//	exit();
		global $dbc;
		$out = array('status'=>false, 'myreason'=>'');	
		$q = "SELECT username,email,AES_DECRYPT(pass, '".EDSALT."') as pass FROM `customers` WHERE username = '$username'";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if($opt)
		{
			/*$username=$rs['username'];
			$email = $rs['email'];
			$pass = $rs['pass'];*/
	
		$mailbody = "Dear <b>{$rs['username']}</b>, <br/><br/> Welcome to Medgyan.com.<br/> <br/> 
		<br/><b>Your account password is : <span style=\"color:#5e9a43;\">{$rs['pass']}</span> </b><br/><br/> Regards,<br/> <b>Team Medgyan</b> <br/> <a href=\"http://www.medgyan.com\">medgyan.com</a>";
		$subject = 'Medgyan A/c password';
		mailsend(LOCAL_HOST_MAIL, $to=$rs['email'], $subject, $mailbody, $from='info@medgyan.com', $toname=$rs['username'], $fromname='Medgyan');	
		return array('status'=>true, 'Password sent to your email address.');
		}
	}
	public function account_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		$q = "UPDATE party SET `pname`='$d1[name]', `commission`='$d1[commission]', `contact_person`='$d1[contact_person]', `refper`='$d1[refper]', `bankdetail`='$d1[bankdetail]',`extra-detail`='$d1[extra]', `email`='$d1[email]', `mobile`='$d1[mobile]', `phone`='$d1[phone]', `address`='$d1[adr]', `locality`='$d1[locality]', `city`='$d1[city]', `pincode`='$d1[pincode]', `state`='$d1[state]', modified=NOW(), `moId`='$d1[uid]' WHERE pId = '$id' LIMIT 1";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Party table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Party updated with Id '.$id.' '.$d1['name']);
		return array('status'=>true, 'myreason'=>'Party successfully updated', 'rId'=>$id);
	}
	// function define foe customer profile get data
	public function get_profile_data()
	{		
		$d1 = array('title'=>$_POST['title'],'firstname'=>$_POST['firstname'],'lastname'=>$_POST['lastname'],'mobile'=>$_POST['mobile'],'gender'=>$_POST['gender']);
		$d1['myreason'] = 'Please fill all the required information';		
		return array(true, $d1);
	}
	public function customer_profile_update($id)
	{
		global $dbc;
		$out = array('status'=>false, 'myreason'=>'');
		$date  = !empty($_POST['dob']) ? get_mysql_date($_POST['dob']) : '';
		list($status, $d1) = $this->get_profile_data();
	
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		mysqli_query($dbc, "START TRANSACTION");
		// query to update
		$q = "UPDATE `customers` SET `title`='$d1[title]', `firstname`='$d1[firstname]', `lastname`='$d1[lastname]', `mobile`='$d1[mobile]',`gender`='$d1[gender]' where `custId`='$id'";
		
		
		list($opt, $rs) = run_query($dbc,"SELECT * FROM family_members WHERE custId = $id AND name = 'Myself'", 'single');
		if(!$opt) $this->make_me_member($id);
		
		$q5 ="UPDATE `family_members` SET  `gender`='$d1[gender]' WHERE custId = '$id'";
		$rs5=mysqli_query($dbc,$q5);
		/*$qf="SELECT fmId FROM `family_members` WHERE custId='$id'";
		$rsf=mysqli_query($dbc,$qf);
		$fd = mysqli_fetch_assoc($rsf);
		$fmId = $fd['fmId'];*/
		$q1="SELECT custId FROM `customer_profile` WHERE custId='$id'";
		$rs=mysqli_query($dbc,$q1);
		
		$count=mysqli_num_rows($rs);
		if($count>0)
		{
			$td= date('Y-m-d');
			if($_POST['db']==1)
			{
				$age=$this->daysDifference($td,$date);
				$qry = "UPDATE `customer_profile` SET dob='$date' WHERE custId = '$id'";
				$r1 = mysqli_query($dbc,$qry);
				$qry1 = "UPDATE `family_members` SET dob_type = '$_POST[db]',dob='$date',age = '$age' WHERE custId = '$id'";
				$r2 = mysqli_query($dbc,$qry1);
			}
		}
		else
		{
			$qry="REPLACE INTO `customer_profile`(custId,dob) VALUES('$id','$date')";
			$r1 = mysqli_query($dbc,$qry);
		}
		if($_POST['db']==2)
		{
			$q2 = "UPDATE `family_members` SET age='$_POST[age]',dob_type = '$_POST[db]', dob=''  WHERE custId = '$id'";
			$r3 = mysqli_query($dbc,$q2);
			$qry = "UPDATE `customer_profile` SET dob='' WHERE custId = '$id'";
			$r1 = mysqli_query($dbc,$qry);
		}
		//$qry = "UPDATE `customer_profile` SET dob='$date' WHERE custId = '$id'";
		//$q = "INSERT INTO `customer_address` (`custId`,`dob`,`anv_date`,`ac_secret`) VALUES (NULL , '$d1[dob]', '$d1[anv_date]',  '$d1[ac_secret]')";
		$r = mysqli_query($dbc,$q);
		
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'customer profile error'); }
		//if(!$r1){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'customer table error'); }		
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Customer successfully Saved');
	}
	public function customer_change_password($id)
	{
			global $dbc;
			$out = array('status'=>false, 'myreason'=>'');
			$_POST['npass']; 
			if($_POST['npass']!=$_POST['vpass']) return array('status'=>false, 'myreason'=>'Password Not Match Please Try Again');
			$q="UPDATE `customers` SET pass=AES_ENCRYPT('$_POST[npass]', '".EDSALT."') WHERE custId = '$id'";
			$r = mysqli_query($dbc,$q);
			if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'customer table error'); }		
		    mysqli_commit($dbc);
		    return array('status'=>true, 'myreason'=>'Password successfully Updated');
			//list($status,$d1) = $this->get_customer_list();
	}
	public function get_customer_account_data()
	{
		$d1 = array('username'=>$_POST['username'],'ac_secret'=>$_POST['ac_secret']);
		$d1['myreason'] = 'Please fill all the required information';		
		return array(true, $d1);
		
	}
	public function customer_account_update($id)
	{
		global $dbc;
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_customer_account_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		mysqli_query($dbc, "START TRANSACTION");
		// query to update
		$q = "UPDATE `customers` SET `username`='$d1[username]' WHERE`custId`='$id'";
		$q1="SELECT custId FROM `customer_profile` WHERE custId='$id'";
		$rs=mysqli_query($dbc,$q1);
		$count=mysqli_num_rows($rs);
		if($count>0)
		{
			$qry="UPDATE`customer_profile` SET `ac_secret` = '$d1[ac_secret]' WHERE`custId`='$id'";	
		}
		else
		{
			$qry="REPLACE INTO `customer_profile`(custId,ac_secret) VALUES('$id','$d1[ac_secret]')";
			
		}
		//$qry="UPDATE`customer_profile` SET `ac_secret` = '$d1[ac_secret]' WHERE`custId`='$id'";
		$r = mysqli_query($dbc,$q);
		$r1 = mysqli_query($dbc,$qry);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'customer profile error'); }
		if(!$r1){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'customer table error'); }		
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Customer successfully Saved');
		//pre($d1);
	}
	
	// To upload family member images
	public function get_mh_upload_path($mhtype,$status=2)
	{
		if($status == 1) { $outpath = BASE_URL_ROOT.'myuploads'.MSYM.'users'; }
		else {  $outpath = BASE_URI_ROOT.'myuploads'.MSYM.'users';}
		if(!is_dir($outpath)) mkdir($outpath,0777); // create the users folder
		$outpath .= MSYM.$_SESSION[FSESS.'id'];
		if(!is_dir($outpath)) mkdir($outpath,0777); // create a seperate folder per user
		switch($mhtype)
		{
			case'profilepic'	:
			{
				$outpath .= MSYM.'profile-pic';
				if(!is_dir($outpath)) mkdir($outpath,0777); // create a seperate medical-test folder
				break;	
			}
			case'medical-test'	:
			{
				$outpath .= MSYM.'medical-test';
				if(!is_dir($outpath)) mkdir($outpath,0777); // create a seperate medical-test folder
				break;	
			}
			default:
			{
				$outpath .= MSYM.'extras';
				if(!is_dir($outpath)) mkdir($outpath,0777); // create a seperate extras folder
				break;	
			}
		}
		return $outpath;
	}
	public function get_family_member_self()
	{
		$d1 = array('mobile'=>$_POST['mobile'],'gender'=>$_POST['gender'], 'name'=>ucwords($_POST['name']), 'dob'=>$_POST['dob'],'dob_type'=>$_POST['db'],'age'=>$_POST['age']);
		$d1['myreason'] = 'Please fill all the required information';		
		return array(true, $d1);
	}
	
	public function get_family_member_data()
	{
		$d1 = array('bgId'=>$_POST['bgId'], 'relationId'=>$_POST['relationId'], 'mobile'=>$_POST['mobile'],'gender'=>$_POST['gender'], 'name'=>ucwords($_POST['name']), 'dob'=>$_POST['dob'], 'age'=>$_POST['age']);
		$d1['myreason'] = 'Please fill all the required information';		
		return array(true, $d1);
	}
	// function used to save the family member
	public function family_member_save($custId)
	{
		
		global $dbc;
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_family_member_data();
		$family_member_upload_path = $this->get_mh_upload_path('profilepic','');
		//h1($family_member_upload_path);
		list($file_success, $pdflink1) = fileupload('filename',$family_member_upload_path, $allowtype ='', $maxsize = 52428800, $mandatory = false);
        if(!$file_success) $pdflink1 = ''; else resizeimage($pdflink1,$family_member_upload_path, 300, 120, MSYM, true); // to resize the image
		$date= date('Y-m-d');
		$date_dob = !empty($_POST['dob']) ? get_mysql_date($_POST['dob']) : '';
	      $db=$_POST['db'];
		if($db==1)
		{
			$dob=$date_dob;
			$age=$this->daysDifference($date,$date_dob);
		}
	   if($db==2)
		{
			$dob=date('Y-m-d');
			$age=$_POST['age'];
		}
		if(empty($_POST['bgId'])) $d1['bgId']=1;
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		$name = ucwords($d1['name']);
        $q = "INSERT INTO `family_members` (`fmId`,`memcode`,`bgId`,`relationId`, `mobile`,`name`,`dob`,`age`,`dob_type`,`filename`,`gender`,`custId`) VALUES (NULL , 'X','$d1[bgId]',  '$d1[relationId]',  '$d1[mobile]','$name','$dob','$age','$db',' $pdflink1','$d1[gender]','$custId')";		
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'customer table error'); }
		$rId = mysqli_insert_id($dbc);		
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Family Member successfully Saved', 'rId'=>$rId);
	}

	function daysDifference($endDate, $beginDate)
   {

	   $date_parts1 = explode("-", $beginDate);
	   $date_parts2 = explode("-", $endDate);
	   $start_date = gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
	   $end_date = gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
	   $diff = abs($end_date - $start_date);
	   $years = floor($diff / 365.25);
	   return $years;
   }
   
	public function family_member_update($id)
	{
		global $dbc;
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_family_member_data();
		$date = !empty($_POST['dob']) ? get_mysql_date($_POST['dob']) : '';
		$family_member_upload_path = $this->get_mh_upload_path('profilepic');
		list($file_success, $pdflink1) = fileupload('filename',$family_member_upload_path, $allowtype ='', $maxsize = 52428800, $mandatory = false);
   		if(!$file_success) $pdflink1 = ''; else resizeimage($pdflink1,$family_member_upload_path, 400, 120, MSYM, true); // to resize the image
        if(empty($_POST['bgId'])) $d1['bgId']=1;
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		mysqli_query($dbc, "START TRANSACTION");
		$td= date('Y-m-d');
		$fileupate = "";
		//if adding a new file need to delete the old one
		if(!empty($pdflink1))
		{
			$fileupate = ",filename='$pdflink1'";
			list($opt,$rs) = run_query($dbc, "SELECT filename FROM family_members WHERE fmId = $id" ,'single', '');
			$oimg = $family_member_upload_path.MSYM.$rs['filename'];
			$thumbnail = $family_member_upload_path.MSYM.'thumbnail'.MSYM.$rs['filename'];
			if(is_file($oimg)) unlink($oimg);
			if(is_file($thumbnail)) unlink($thumbnail);
		}
		// query to update
		$q = "UPDATE `family_members` SET `bgId`='$d1[bgId]', `relationId`='$d1[relationId]', `mobile`='$d1[mobile]', `name`='$d1[name]',`gender`='$d1[gender]' $fileupate WHERE `fmId`='$id'";
		$custId = $_SESSION[FSESS.'id'];
		$q6 = "UPDATE `customers` SET `gender`='$d1[gender]' where `custId`='$custId'";
		$r6 = mysqli_query($dbc,$q6);		
		if($_POST['db']==1)
			{
				$age=$this->daysDifference($td,$date);
				$qry = "UPDATE `customer_profile` SET dob='$date' WHERE custId = '$id'";
				$r1 = mysqli_query($dbc,$qry);
				$qry1 = "UPDATE `family_members` SET dob_type = '$_POST[db]',dob='$date',age = '$age' WHERE fmId = '$id'";
				$r2 = mysqli_query($dbc,$qry1);
			}
		if($_POST['db']==2)
		{
			$q2 = "UPDATE `family_members` SET age='$_POST[age]',dob_type = '$_POST[db]', dob=''  WHERE fmId = '$id'";
			$r3 = mysqli_query($dbc,$q2);
			$qry = "UPDATE `customer_profile` SET dob='' WHERE custId = '$id'";
			$r1 = mysqli_query($dbc,$qry);
		}
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'customer profile error'); }
		mysqli_commit($dbc);
	//	echo "Succesfully Updated";
		return array('status'=>true, 'myreason'=>'Customer successfully Update');
	}
	public function family_member_self_update($id)
	{
		global $dbc;
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_family_member_self();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		$date = !empty($_POST['dob']) ? get_mysql_date($_POST['dob']) : '';
		mysqli_query($dbc, "START TRANSACTION");
		
		// query to update
		$q = "UPDATE `family_members` SET `dob_type`='$d1[dob_type]',`mobile`='$d1[mobile]', `name`='$d1[name]', `dob`='$date', `age`='$d1[age]',`gender`='$d1[gender]'  WHERE `fmId`='$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'customer profile error'); }
		mysqli_commit($dbc);
	//	echo "Succesfully Updated";
		return array('status'=>true, 'myreason'=>'Customer successfully Update');
	}
	public function get_family_self_data()
	{
		$d1 = array('fmId'=>$_POST['fmId'], 'testname'=>$_POST['testname'],'test_report'=>$_POST['test_report'], 'lab_name'=>$_POST['lab_name'],'lab_contact_no'=>$_POST['lab_contact_no'],'lab_adr'=>$_POST['lab_adr']);
		$d1['myreason'] = 'Please fill all the required information';
		return array(true, $d1);
	}
	public function self_family_test_save($custId)
	{
		
		global $dbc;
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_family_self_data();
		$self_test_upload_path = $this->get_mh_upload_path('medical-test','');
		list($file_success, $pdflink1) = fileupload('filename',$self_test_upload_path, $allowtype ='', $maxsize = 52428800, $mandatory = false);
        if(!$file_success) $pdflink1 = ''; else resizeimage($pdflink1,$self_test_upload_path, 300, 120, MSYM, true); // to resize the image
		
		if(!empty($_POST['test_date'])) $test_date = get_mysql_date($_POST['test_date']); else $test_date = '0000-00-00';
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		$testname = ucwords($d1['testname']);
        $q = "INSERT INTO `family_ilns_test` (`filnstId`,`filnsdId`,`ifmId`,`testname`,`test_date`,`test_report`, `test_report_scan`,`lab_name`,`lab_contact_no`,`lab_adr`) VALUES (NULL ,0,'$d1[fmId]',  '$testname','$test_date','$d1[test_report]','$pdflink1','$d1[lab_name]','$d1[lab_contact_no]','$d1[lab_adr]')";		
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'customer table error'); }
		$rId = mysqli_insert_id($dbc);		
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Self medical test successfully Saved', 'rId'=>$rId);
	}
	public function get_family_self_test_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		//$q = "SELECT fst.*,fm.name,fm.age,bg_name,DATE_FORMAT(fm.dob,'%d/%m/%Y') AS dob,DATE_FORMAT(fst.test_date,'%d/%m/%Y') AS test_date FROM `family_self_test` as fst INNER JOIN `family_members` as fm USING(fmId) INNER JOIN `ref_blood` USING(bgId) $filterstr";
		//h1($q);
		$q = "SELECT *,DATE_FORMAT(test_date,'%d/%m/%Y') AS test_date,DATE_FORMAT(dob,'%d/%m/%Y') AS dob FROM family_ilns_test INNER JOIN family_members USING(fmId)  $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['filnstId'];
			$out[$id]['filnstId'] = $row['filnstId'];
			$out[$id]['fmId'] = $row['fmId'];
			$out[$id]['name'] = ucwords($row['name']);
			$out[$id]['testname'] = ucwords($row['testname']);
			$out[$id]['test_report_scan'] = $row['test_report_scan'];
			$out[$id]['age'] = $row['age'];
			$out[$id]['dob'] = $row['dob']=='00/00/0000'?'': $row['dob'];
			$out[$id]['test_date'] = $row['test_date'];
			$out[$id]['test_report'] = $row['test_report'];
			$out[$id]['lab_name'] = $row['lab_adr'];
		}
		return $out;
	}
	public function self_family_test_update($id)
	{
		
		global $dbc;
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_family_self_data();
		$self_test_upload_path = $this->get_mh_upload_path('medical-test');
		list($file_success, $pdflink1) = fileupload('filename',$self_test_upload_path, $allowtype ='', $maxsize = 52428800, $mandatory = false);
   		if(!$file_success) $pdflink1 = ''; else resizeimage($pdflink1,$self_test_upload_path, 400, 120, MSYM, true); // to resize the image
		
		$fileupate = "";
		//if adding a new file need to delete the old one
		if(!empty($pdflink1))
		{
			$fileupate = ",test_report_scan='$pdflink1'";
			list($opt,$rs) = run_query($dbc, "SELECT test_report_scan FROM family_ilns_test WHERE filnstId = $id" ,'single', '');
			$oimg = $self_test_upload_path.MSYM.$rs['test_report_scan'];
			$thumbnail = $self_test_upload_path.MSYM.'thumbnail'.MSYM.$rs['test_report_scan'];
			if(is_file($oimg)) unlink($oimg);
			if(is_file($thumbnail)) unlink($thumbnail);
		}
		
		if(!empty($_POST['test_date'])) $test_date = get_mysql_date($_POST['test_date']); else $test_date = '0000-00-00';
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
        $q = "UPDATE  `family_ilns_test` SET `ifmId` = '$d1[fmId]',`testname` = '$d1[testname]',`test_date` = '$test_date', `test_report` = '$d1[test_report]' , `lab_name` = '$d1[lab_name]', `lab_contact_no` = '$d1[lab_contact_no]', `lab_adr` = '$d1[lab_adr]' $fileupate WHERE filnstId  = '$id'";	
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Self test table error'); }
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Self medical test successfully Saved');
	}
	public function search_self_family_test($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$out = array();
		$flter = array();		
		$sdate = $_POST['sdate'];
		$edate = $_POST['edate'];
		$start = get_mysql_date($sdate);
		$end = get_mysql_date($edate);
		$start = explode('-',$start);
		$start = implode('',$start);
		$end = explode('-',$end);
		$end = implode('',$end);
		$filter[] = "DATE_FORMAT(test_date,'%Y%m%d') BETWEEN $start AND $end";
		if(!empty($id))  $filter[] = "custId = '$id'";
		 
		if(count($filter)>0) $filterstr = 'WHERE '.implode(' AND ', $filter);
	 //   $q="SELECT *,DATE_FORMAT(test_date,'%r') AS stime,DATE_FORMAT(test_date,'%d/%m/%Y') as fdated FROM family_self_test $filterstr";
		$q = "SELECT *,DATE_FORMAT(test_date,'%d/%m/%Y') AS test_date,DATE_FORMAT(dob,'%d/%m/%Y') AS dob FROM family_self_test INNER JOIN family_members USING(fmId)  INNER JOIN `ref_blood` USING(bgId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		$inc=1;
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['filns_stId'];
			$out[$id]['filns_stId'] = $row['filns_stId'];
			$out[$id]['name'] = ucwords($row['name']);
			$out[$id]['testname'] = ucwords($row['testname']);
			$out[$id]['filename'] = $row['test_report_scan'];
			$out[$id]['age'] = $row['age'];
			$out[$id]['dob'] = $row['dob']=='00/00/0000'?'': $row['dob'];
			$out[$id]['test_date'] = $row['test_date'];
			$out[$id]['test_report'] = $row['test_report'];
			$out[$id]['lab_name'] = $row['lab_detail'];
			
		}
		return $out;
	}

	// This function is used to calculate the approx and exact date of family members
	public function cal_date($fmId,$dob)
	{
		$curdate = date('Y-m-d');
		$diff = abs(strtotime($curdate) - strtotime($dob));
		$years = floor($diff / (365*60*60*24));
		return $years;
	}
	// This function will full details of member
	public function get_member_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *,relation,bg_name,DATE_FORMAT(dob,'%d/%m/%Y') AS dob FROM `family_members` LEFT JOIN `ref_relation` USING(relationId) LEFT JOIN `ref_blood` USING(bgId) $filterstr";
		//h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['fmId'];
			$out[$id]['fmId'] = $row['fmId'];
			$out[$id]['bgId'] = $row['bgId'];
			$out[$id]['relationId'] = $row['relationId'];
			$out[$id]['blood'] = $row['bg_name'];
			$out[$id]['gender'] = $row['gender'];
			$out[$id]['relation'] = $row['relation'];
			$out[$id]['name'] = ucwords($row['name']);
			$out[$id]['age'] = $row['age'];
			$out[$id]['mobile'] = $row['mobile'];
			$out[$id]['dob_type'] = $row['dob_type'];
			$out[$id]['dob'] = $row['dob']=='00/00/0000'?'': $row['dob'];
			$out[$id]['filename'] = $row['filename'];
			$out[$id]['custId'] = $row['custId'];
		}
		return $out;
	}
	public function make_me_member($custId)
	{
		
		global $dbc;
		mysqli_query($dbc, "START TRANSACTION");
		$qry = "SELECT * FROM customers WHERE custId = $custId LIMIT 1";
		list($opt, $rs) = run_query($dbc, $qry, $mode='single', $msg='');
        $q = "INSERT INTO `family_members` (`fmId`,`memcode`,`bgId`,`relationId`, `name`,`gender`,`custId`) VALUES (NULL , 'X',1,  1,'Myself','$rs[gender]','$custId')";		
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'customer table error'); }
		$rId = mysqli_insert_id($dbc);
		return $rId;		
	}
	public function my_search_history($id,$sdate,$edate)
	{
		global $dbc;
		$out = array();		
		$start = get_mysql_date($sdate);
		$end = get_mysql_date($edate);
		$start = explode('-',$start);
		$start = implode('',$start);
		$end = explode('-',$end);
		$end = implode('',$end);
		$filter[] = "DATE_FORMAT(search_date,'%Y%m%d') BETWEEN $start AND $end";
		if(!empty($id))  $filter[] = "custId = '$id'";
		 
		if(count($filter)>0) $filterstr = 'WHERE '.implode(' AND ', $filter);
	    $q="SELECT *,DATE_FORMAT(search_date,'%r') AS stime,DATE_FORMAT(search_date,'%d/%m/%Y') as fdated FROM customer_search_history $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		$inc=1;
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id =$inc;
			$out[$id]['custId'] = $row['custId'];
			$out[$id]['fdated'] = $row['fdated'];
			$out[$id]['stime'] = $row['stime'];
			$out[$id]['searchtxt'] = $row['searchtxt'];
			$out[$id]['search_output'] = $row['search_output'];
			$out[$id]['search_section'] = $row['search_section'];
			$out[$id]['search_domain'] = $row['search_domain'];
			$inc++;
			
		}
		return $out;
	}
	public function new_to_free_customer($filter, $records, $orderby)
	{
		global $dbc;
		$out = array();	
		$filter = $this->oo_filter($filter, $records, $orderby);
	    $q = "SELECT expired,cpId,DATE_FORMAT(created,'%Y-%m-%d') AS created FROM customer_package_history INNER JOIN  customers USING(custId)  $filter  AND cpId='1' AND expired = '1'";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		//if(!$opt) return array('status'=>false, 'myreason'=>'Username or  password do not match');
		mysqli_query($dbc, "START TRANSACTION");
	    $ddate = $rs['created'];
	    $ddate = strtotime(date("Y-m-d", strtotime($ddate)) . " +3 day");
 	    $ddate = date('Y-m-d', $ddate);
		$ddate = explode('-',$ddate);
	    $ddate = implode('',$ddate);
		$curdate = date('Y-m-d');
		$curdate = explode('-',$curdate);
	    $curdate = implode('',$curdate);
		if($ddate <= $curdate)
		{
			$q = "UPDATE  customer_package_history SET cpId = '2' $filter AND expired = 1";	
			$r = mysqli_query($dbc,$q);
			if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'customer table error'); }		
		    mysqli_commit($dbc);
			//echo '<span style="margin-top:25px;" class="asm">Your Account Change to New User to free user</span>';
		}
	   return array('status'=>true, 'myreason'=>'Your Account Change to New User to free user');
	
	}
	public function calculate_date()
	{
		global $dbc;
	    $q ="SELECT cpdId,CONCAT_WS(' ',duration,ref_duration) AS cpdId FROM customer_package_duration INNER JOIN ref_package_duration USING(cpv_type) INNER JOIN customer_package USING(cpId) WHERE cpId='1'";
		
		$rs = mysqli_query($dbc,$q);
		$row = mysqli_fetch_assoc($rs);
		$dd = '+'. $row['cpdId'];
		$ddate = date('Y-m-d');
	    $ddate = strtotime(date("Y-m-d", strtotime($ddate)) . " $dd");
 	    $ddate = date('Y-m-d', $ddate);
		return $ddate;
	}
	public function calcuate_exp_date($filter, $records, $orderby)
	{
		global $dbc;
		$out = array();	
		$filter = $this->oo_filter($filter, $records, $orderby);
	    $q = "SELECT expired,cpId,DATE_FORMAT(created,'%Y-%m-%d') AS created FROM customer_package_history INNER JOIN  customers USING(custId)  $filter  AND cpId='1' AND expired = '1'";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		//if(!$opt) return array('status'=>false, 'myreason'=>'Username or  password do not match');
		mysqli_query($dbc, "START TRANSACTION");
	    $ddate = $rs['created'];
	    $ddate = strtotime(date("Y-m-d", strtotime($ddate)) . " +3 day");
 	    $ddate = date('Y-m-d', $ddate);
		$ddate = explode('-',$ddate);
	    $ddate = implode('',$ddate);
		$curdate = date('Y-m-d');
		$curdate = explode('-',$curdate);
	    $curdate = implode('',$curdate);
		if($ddate <= $curdate)
		{
			$q = "UPDATE  customer_package_history SET cpId = '2' $filter AND expired = 1";	
			$r = mysqli_query($dbc,$q);
			if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'customer table error'); }		
		    mysqli_commit($dbc);
			//echo '<span style="margin-top:25px;" class="asm">Your Account Change to New User to free user</span>';
		}
	   return array('status'=>true, 'myreason'=>'Your Account Change to New User to free user');
	}
	
	/**	This function will convert the new user to free user after 3 days of a/c creation
		It will also convert the paid members A/c to free user if their validity expires
	   	This function will be run from admin panel welcome.php page at the top.
	*/
	public function chk_exp()
	{
		global $dbc;
		$out = array();
		$cur_date = date('Y-m-d');
	    $q = "SELECT * FROM customer_package_history WHERE DATE_FORMAT(validity,'%Y%m%d') < DATE_FORMAT(NOW(),'%Y%m%d') AND expired = 1";
		list($opt, $r) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; 
	    $q ="SELECT cpId,cpname,per_day_count,total_count,per_month_count,CONCAT_WS(' ',duration,ref_duration) AS cpdId FROM customer_package_duration INNER JOIN ref_package_duration USING(cpv_type) INNER JOIN customer_package USING(cpId) WHERE cpId='2'";

	    list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if(!$opt) return $out; 
		$dd = '+'. $rs['cpdId'];
	    $ddate = strtotime(date("Y-m-d", strtotime($cur_date)) . " $dd");
 	    $ddate = date('Y-m-d', $ddate);
		mysqli_query($dbc, "START TRANSACTION");
		while($row = mysqli_fetch_assoc($r))
		{
			$id = $row['custId'];
			// making current active package unactive
		    $qry = "UPDATE customer_package_history SET  expired  = '0' WHERE cphId = $row[cphId]";
			$r1 = mysqli_query($dbc,$qry);
			if(!$r1){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Customer package history update table error');}
			//Inserting the free user package
			$r3 = mysqli_query($dbc, $q="INSERT INTO `customer_package_history` (cphId, custId, cpId, package_name, validity, total_count, per_month_count, per_day_count, expired, price, purchase_date) VALUES(NULL, '$id', $rs[cpId], 'Free User', '$ddate', '$rs[total_count]', '$rs[per_month_count]', '$rs[per_day_count]', 1, 0, NOW())");
			if(!$r3){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Customer package history table error');}
			//Updating the present ac status of the customer
			$qry1 = "UPDATE customers SET present_ac_status = '2' WHERE custId ='$id'";
			$r2 = mysqli_query($dbc,$qry1);
			if(!$r2){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Customer update  table error');}
			
		}
		mysqli_commit($dbc);
	}
	// This function used to make the mearge array for medical test and self test.. 
	public function merge_test_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out =array();
		$out1 =array();
		$filter = $this->oo_filter($filter, $records, $orderby);
		$q1 = "SELECT *,DATE_FORMAT(test_date,'%e/%b/%Y') AS testDate FROM `family_ilns_test` INNER JOIN `family_ilns_doctor` USING(filnsdId) INNER JOIN family_illness USING(ilnsId) INNER JOIN family_members USING(fmId) $filter";
		//list($opt, $rs1) = run_query($dbc, $q1, $mode='multi', $msg='');
		$rs1 = mysqli_query($dbc,$q1);
		//if(!$opt) return $out; 
		$q2 = "SELECT *,DATE_FORMAT(test_date,'%d/%m/%Y') AS test_date,DATE_FORMAT(dob,'%d/%m/%Y') AS dob FROM family_self_test INNER JOIN family_members USING(fmId)  INNER JOIN `ref_blood` USING(bgId) $filter";
		//list($opt1, $rs2) = run_query($dbc, $q2, $mode='multi', $msg='');
		//if(!$opt) return $out; 
		$rs2 = mysqli_query($dbc,$q2);
		if(mysqli_num_rows($rs1)>0)
		{
			while($row =mysqli_fetch_assoc($rs1))
			{
				$id = $row['filnstId'];	
				$out[$id]['testname'] = $row['testname'];	
			}
		}
		if(mysqli_num_rows($rs2)>0)
		{

			while($rows =mysqli_fetch_assoc($rs2))
			{
				$id = $rows['filns_stId'];	
				$out1[$id]['testname'] = $rows['testname'];	
			}
		}
		$out2 = array_merge((array)$out, (array)$out1);
		return $out2;
		//return array('1'=>$out,'2'=>$out1);
	}
	// this function used to get the patient list record
	public function get_patient_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		//doctor_visit_date
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *,DATE_FORMAT(doctor_visit_date,'%d/%m/%Y') AS vdate FROM family_ilns_doctor INNER JOIN family_illness USING(ilnsId) INNER JOIN family_members USING(fmId) $filterstr";
		//$q = "SELECT fd.*,fm.name,fm.age,fm.gender  FROM family_ilns_doctor as fd INNER JOIN family_members as fm USING(fmId) $filterstr";
		//$q = "SELECT * FROM `family_members` INNER JOIN family_illness USING(fmId) INNER JOIN family_ilns_doctor USING(ilnsId) $filterstr";
		//h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['fmId'];
			$out[$id]['fmId'] = $row['fmId'];
			$out[$id]['ilnsId'] = $row['ilnsId'];
			$out[$id]['gender'] = $row['gender'];
			$out[$id]['title'] = $row['title'];
			$out[$id]['name'] = ucwords($row['name']);
			$out[$id]['age'] = $row['age'];
			$out[$id]['mobile'] = $row['mobile'];
			$out[$id]['dob_type'] = $row['dob_type'];
			$out[$id]['vdate'] = $row['vdate'];
		}
		return $out;
	}
	// this function  used to get the docId
	public function get_docId($id)
	{
		global $dbc;
		$q = "SELECT docId FROM customers WHERE custId = '$id'";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return ; // if no docId found return blank array
		$row = mysqli_fetch_assoc($rs);
		return $row['docId'];
	}
	//// customer list////////////////////////////////
	
}	

?>