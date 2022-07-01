<?php
//this function will give the final view of the data submitted
function thumb_of($filename)
{
	$extension = substr($filename, strrpos($filename, '.') + 1);
	$extension = strtolower($extension);
	$justimagename = rtrim($filename,'.'.$extension);
	$thumbname = $justimagename.'_thumb.'.$extension;
	return $thumbname;
}
function final_view($submit,$display,$hidden,$not_to_show,$action='')
{
	$i = 0;
	$fname = '';
	//if(!empty($action))
	//$action = ' action="index.php?option="'.$action.' ';
	$str = '<form method="post" '.$action.' class="iform" id="preview_form"><div style="background:#CCC;padding:0 auto;"><center><h1>You Are About To '.ucwords($submit).' This.</h1>';
	$names = '';
	foreach($display as $key => $value)
	{
		$names .= $key.'<@@>'.$hidden[$key].'<$preview$>';
		
		if($fname == '')
		$fname = $key;
			$str .= '<input type="hidden" name="'.$key.'" value="'.$hidden[$key].' - '.$key.'">';
			
			if(!in_array($key,$not_to_show))
			{
				$style = 'width:19%; float:left; margin:2px 0 0 2px; color:#333; border:solid 1px #000; vertical-align:top; height:35px;';
				if($i % 2 == 1)
				$style = 'width:19%; float:left; margin:2px 0 0 2px; color:#333; border:solid 1px #000; vertical-align:top; height:35px; background:#DDD;';
				
				$str .= '<div style="'.$style.'" ><table width="100%"><tr><td width="40%"><u>'.ucwords($key).'</u> </td><td><strong>'.$value.'</strong></td></tr></table></div>';
				$i++;
				if($i == 5)
				$i = 0;
		}
	}
	$str .= '<input type="hidden" id="preview_info" value="'.base64_encode($names).'"></center></div><br><br><br><div id="preview_buttons"><table width="100%" style="clear:both;"><tr><th><input autofocus type="button" id="preview_submit" name="submit" value="Confirm '.$submit.'"><input type="button" name="submit" value="EDIT" onclick="parent.$.fn.colorbox.close();"></th></tr></table></div></form><script type="text/javascript">document.getElementById("preview_submit").focus();</script>';
	return $str;
}
//This function will help in storing the last used query in the session so that user can stick to his last search made.
function retainquery($q, $mode, $formaction, $sesvar = SESS)
{
	global $dbc;
	if($mode == 'store')
	{
		$_SESSION[$sesvar.'data']['cpage']['formaction'] = $formaction;
		$_SESSION[$sesvar.'data']['cpage']['q'] = $q;
	}
	elseif($mode == 'fetch')
	{
		if(!isset($_SESSION[$sesvar.'data']['cpage']['formaction']) || !isset($_SESSION[$sesvar.'data']['cpage']['q']))
			return array(false,'sorry query not save in the retainfilter() function');
		elseif($_SESSION[$sesvar.'data']['cpage']['formaction'] != $formaction)// we are viewing some other page now
		{
			unset($_SESSION[$sesvar.'data']['cpage']);
			return array(false,'sorry query not matching in the retainfilter() function');
		}
			
		return array(true,$_SESSION[$sesvar.'data']['cpage']['q']);
	}
}

//this function will return the details about the location, operatior of the mobile number passed to it
function db_pulldown_group($dbc, $name, $q, $arrkey = false, $inioption = true, $jsfunction='',$id = '',$firstoptiontext = '== Please select ==')
{
	$r = mysqli_query($dbc, $q);
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<select name="'.$name.'" '.$jsfunction.'>';
			$optarray = array();
			if($arrkey)
			{
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[2]][$row[0]] = $row[1];	
				}
			}
			else
			{
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[1]][$row[0]] = $row[0];	
					//$optarray[] = $row[1];	
				}
			}
			if($inioption)
				echo'<option value="">'.$firstoptiontext.'</option>';
			foreach($optarray as $key => $value)
			{
					echo '<optgroup label="'.$key.'">';
				foreach($optarray[$key] as $k => $v)
				{
					echo'<option value="'.$k.'"'; if((isset($_POST[$name]) && $_POST[$name] == $k)||($k == $id)) echo'selected="selected"'; echo'>'.$v.'</option>'."\n";
					
				}
					echo '</optgroup>';
			}
			echo'</select>';			
		}
		else
		{
			echo'<select name="'.$name.'" '.$jsfunction.'>';
			echo'<option value="">N.A.</option>';
			echo'</select>';			
		}
	}
	else
	{
		echo'<span class="error">Sorry, database query failed</span>';
		//exit();
	}
}
function encode_priceg($salt,$price)
{
	$str = substr($price,1,strlen($price)-1);
	
	return $salt[$price[0]-1].$str;
}
function encode_price($salt,$price)
{
	$price = (int)$price;
	$str = substr($price,1);
	$first = substr($price,0,1);	
	return $salt[$first-1].$str;
}
function getmobilelocation($dbc, $mobilenumber)
{
	$first4digits = substr($mobilenumber,0,4);
	if(strlen($mobilenumber) == 10 && is_numeric($first4digits))
	{		
		list($optloc, $rloc) = run_query($dbc, $q="SELECT mto.ocode, mto.oname, mto.ocorporation, mtc.code, mtc.name  FROM m_mobile_circle_operator AS mmco INNER JOIN m_telecom_operators AS mto USING(toId) INNER JOIN m_telecom_circles AS mtc USING(tcId) WHERE mmco.series='$first4digits'", $mode='single', $msg='sorry no  record found');
		if($optloc)
		{
			// determining whether the cal made was local or it was an std call							
			$shortloc = explode('Circle', $rloc['name']); 
			$strloc = $rloc['oname'].'<br/><b>'.$shortloc[0].'</b>';							
		}
		else
			$strloc = 'N.A';
	}
	else
	{
		$strloc = 'N.A';  // if its not a mobile then we can not find the location
	}
	return $strloc;
}

//To get the total communication for a number in case of tower dump single
//this function will return the details about the location, operatior of the mobile number passed to it
function getcommunication_singletower($dbc, $mobilenumber, $searchcond)
{
	$tota = $totb = 0;
	//communication sideA
	$qa = "SELECT count(numberA) as frequency, numberA FROM searchtower_detail WHERE $searchcond AND numberA='$mobilenumber' group by numberA order by frequency DESC";
	
	list($optloc, $rloc) = run_query($dbc, $qa, $mode='single', $msg='sorry no  record found');
	if($optloc) $tota = $rloc['frequency'];
	
	//communication sideB
	$qb = "SELECT count(numberB) as frequency, numberB FROM searchtower_detail WHERE $searchcond AND numberB='$mobilenumber' group by numberB order by frequency DESC";
	
	list($optloc, $rloc) = run_query($dbc, $qb, $mode='single', $msg='sorry no  record found');
	if($optloc) $totb = $rloc['frequency'];
	
	return($tota + $totb);
}

//function to find the diff of two array start
function arr_diff($first , $second)
{
	$arraydiff = array();
	foreach($first as $k=>$v)
	{
		$a = array_diff($first[$k],$second[$k]);
		$noofelm = count($a);
		if($noofelm == 0) continue;
		else
		{
			$arraydiff[$k] = $a;
		}
	}
	return $arraydiff;
}
//function to find the diff of two array end

function by_admin($dbc,$tablename,$primarykeyId,$keyvalue,$sess_id)	//----------  Function to check whether different elements are created by ADMIN or NOT..!!
{
	$result = false;
	$q0 = "SELECT rolename FROM admin_account_type AS aat,admin AS ad WHERE aat.roleid = ad.urole AND id = '$sess_id' ";
	$r0 = mysqli_query($dbc,$q0);
	if($r0)
	{
		$d0 = mysqli_fetch_assoc($r0);
		if(strtolower($d0['rolename'])=='super admin')
		{
			$result = true;
			return $result;
		}
	}
	$q = "SELECT crby FROM {$tablename} WHERE {$primarykeyId} = '$keyvalue' LIMIT 1";
	$r = mysqli_query($dbc,$q);
	if($r)
	{
		$d = mysqli_fetch_assoc($r);
		$crby = $d['crby'];
		if($crby == $sess_id)
		{
			$result = true;
			return $result;
		}
	}
	return $result;
}
function available_stock($dbc , $itemId)
{
	$qty = 0.00;
	$q = "SELECT current_stock AS ostock FROM items WHERE itemId = '$itemId'";
	$r = mysqli_query($dbc, $q);
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			$row = mysqli_fetch_assoc($r);
			$qty += $row['ostock'];
			
			$qq = "SELECT SUM(qty) AS quality_qty FROM quality_var INNER JOIN quality_items USING(qiId) WHERE itemId = '$itemId'";
			$rr = mysqli_query($dbc,$qq);
			if($rr)
			{
				$dd = mysqli_fetch_assoc($rr);
				$qty += $dd['quality_qty'];
			}
			/*$qq = "SELECT SUM(issued_qty) AS store_qty FROM store_items WHERE item_id = '$id'";
			$rr = mysqli_query($dbc,$qq);
			if($rr)
			{
				$dd = mysqli_fetch_assoc($rr);
				$qty -= $dd['store_qty'];
			}*/
			return $qty;
		}
		else
			return '0';
	}
	else
	return false;
}
function variation_stock($dbc , $itemId, $voId)
{
	$qty = 0.00;
	$q = "SELECT vcstock FROM variation_combination WHERE itemId = '$itemId' AND vcoptionids = '$voId'";
	$r = mysqli_query($dbc, $q);
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			$row = mysqli_fetch_assoc($r);
			$qty += $row['vcstock'];
			
			$qq = "SELECT SUM(acc_qty) AS quality_qty FROM qc_var INNER JOIN qc_qty USING(qciId) WHERE itemId = '$itemId' AND voId = '$voId'";
			$rr = mysqli_query($dbc,$qq);
			if($rr)
			{
				$dd = mysqli_fetch_assoc($rr);
				$qty += $dd['quality_qty'];
			}
			/*$qq = "SELECT SUM(issued_qty) AS store_qty FROM store_items WHERE item_id = '$id'";
			$rr = mysqli_query($dbc,$qq);
			if($rr)
			{
				$dd = mysqli_fetch_assoc($rr);
				$qty -= $dd['store_qty'];
			}*/
			return $qty;
		}
		else
			return '0';
	}
	else
	return false;
}
function get_mail($dbc)
{
	$q = "SELECT stvalue FROM settings WHERE stId = '1' OR stname = 'email' LIMIT 1";
	$r = mysqli_query($dbc, $q);
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			$d = mysqli_fetch_assoc($r);
			return $d['stvalue'];
		}
	}
	return 'test@weboseo.com';
}
function create_md5($to_chk_with = '',$arrkey=array(),$def_key=true)   //---Function to create a unique md5 for the post variables starts here ---//
{
	// -- $arrkey is an array which contains the keys of the post variables that are to be used for creating unique md5
	// -- $def_key is an extra parameter which decides whether to process the keys in $arrkey or to exclude these keys from the process
	// -- Both the parameters are optional
	// -- if function is called without any parameter then all the post variables will be processed to create md5
	
	// -- if $def_key is true then md5 will be created only of those post variables whose key is present in $arrkey
	// -- if $def_key is false then md5 will be created only of those post variables whose key is not present in $arrkey
	$str = '';
	$len = count($arrkey);
	if($len == 0)
	{
		foreach($_POST as $key => $value)
		{
			$str .= $value;
		}
	}
	else
	{
		if($def_key)
		{
			foreach($_POST as $key => $value)
			{
				if(in_array($key,$arrkey))
				{
					$str .= $value;
				}
			}
		}
		else
		{
			foreach($_POST as $key => $value)
			{
				if(!in_array($key,$arrkey))
				{
					$str .= $value;
				}
			}
		}
	}
	$unique = md5($str);
	
	if($unique == $to_chk_with)
	{
		return array(true, $unique);
	}
	else
	{
		return array(FALSE, $unique);
	}
}			 //---Function to create a unique md5 for the post variables ends here ---//
//Function to send sms to user starts
function send_sms($mobile, $message)
{
	return true;
	$username="civic";
	$api_password="civic654321";
	$sender="CivicE";
	$domain="sms.weboseosms.com";
	$priority="2";// 1-Normal,2-Priority,3-Marketing
	$method="POST";
	$mobile=$mobile;
	$message=$message;
	$username=urlencode($username);
	$password=urlencode($api_password);
	$sender=urlencode($sender);
	$message=urlencode($message);
	
	$parameters="user=$username&password=$api_password&mobiles=$mobile&message=$message&sender=$sender&priority=4";
	if($method=="POST")
	{
		$opts = array(
		  'http'=>array(
			'method'=>"$method",
			'content' => "$parameters",
			'header'=>"Accept-language: en\r\n".
    				 "Content-type: application/x-www-form-urlencoded\r\n"
		  )
		);
		
		$context = stream_context_create($opts);
		$url = "http://$domain/sendhttp.php?$parameters";
		$fp = fopen($url, "r", false, $context);
	}
	else
	{
		$fp = fopen("http://$domain/sendhttp.php?$parameters", "r");
	}
	
	$response = stream_get_contents($fp);
	fpassthru($fp);
	fclose($fp);

	if($response=="")
	{
		//echo "Process Failed, Please check domain, username and password.";
		return false;
	}
	else
	{
		//echo "$response";
		return true;
	}
}
//function to send sms starts ends

// function used for saving stock item
function save_central_stock($transtype, $transdate, $itemId, $qty){
	return array ('status'=>true, 'myreason'=>'') ;	
	global $dbc;
	$date = !empty($transdate) ? get_mysql_date($transdate) : '';
	$str = array();
	$sessId = $_SESSION[SESS.'csess'];	
	foreach($itemId as $key=>$value){
		switch($transtype){
			// transtype 1 for, after check quality
			case 1 : {
				$qty1 = $qty[$key];
				break;
			}
			// 2 for stock issue
			case 2 : {
				$qty1 = '-'.$qty[$key];
				$date = date('Y-m-d');
				break;
			}
			// 3 for stock return 
			case 3 : {
				$qty1 = $qty[$key];
				break;
			}
			// 4 for SF Entry
			case 4 : {
				$qty1 = $qty[$key];
				$date = date('Y-m-d');
				break;
			}
			// 5 for SF Partial
			case 5 : {
				$qty1 = $qty[$key];
				$date = date('Y-m-d');
				break;
			}
			// After complete the job process then entry in stock item
			case 6 : {
				$qty1 = $qty[$key];
				$date = date('Y-m-d');
				break;
			}
			// item assign, when job batch will be start
			case 7 : {
				
				$qty1 = '-'.$qty[$key];
				break;
			}
		}
		
		$str[] = "(NULL, $transtype, '{$itemId[$key]}', '$qty1', $sessId, '$date', NOW())";	
	}
	$str = implode(', ', $str);
	$q = "INSERT INTO `stock_item` (`transId`, `transtype`, `itemId`, `qty`, `sesId`, `trans_date`, `created`) VALUES $str";
	$r = mysqli_query($dbc, $q);
	if(!$r) return array ('status'=>false, 'myreason'=>'item_stock Table error') ;	
	return array ('status'=>true, 'myreason'=>'') ;	
}

function get_central_stock($itemId)
{
	global $dbc;
	$out = array();
	$table = array();
	$sum = 0;
	$q = "SELECT itemId, opening_stock FROM item WHERE itemId = $itemId LIMIT 1";
	list($opt, $rs) = run_query($dbc, $q, $mode='single');
	if(!$opt) return $sum;
	$sum = $rs['opening_stock'];
	
	//When item is received in the inventory via quality
	$table[1]['q'] = "SELECT SUM(qty) as tot FROM quality_item WHERE itemId = $itemId";
	$table[1]['plus_minus'] = "+";
	//When item is received in the inventory via quality
	$table[2]['q'] = "SELECT SUM(qty) as tot FROM stock_issue_item WHERE itemId = $itemId";
	$table[2]['plus_minus'] = "-";
	//When item will be return
	$table[3]['q'] = "SELECT SUM(qty) as tot FROM stock_return_item WHERE itemId = $itemId";
	$table[3]['plus_minus'] = "+";
	//When SF Entry
	$table[4]['q'] = "SELECT SUM(qty) as tot FROM sf_item WHERE itemId = $itemId";
	$table[4]['plus_minus'] = "+";
	//When entry in SF partial
	/*$table[5]['q'] = "SELECT SUM(qty) as tot FROM sf_item WHERE itemId = $itemId";
	$table[5]['plus_minus'] = "+";*/
	//After complete the job process
	//$q = "SELECT ";
	$table[5]['q'] = "SELECT SUM(batch_output_qty) as tot FROM job_route_batch WHERE fgId = $itemId";
	$table[5]['plus_minus'] = "+";
	
	// When job batch will be start, item assign to process
	$table[6]['q'] = "SELECT SUM(qty) as tot FROM job_route_batch_item WHERE itemId = $itemId";
	$table[6]['plus_minus'] = "-";
	// When item out for RGP
	$table[7]['q'] = "SELECT SUM(qty) as tot FROM ch_rgp_item WHERE itemId = $itemId";
	$table[7]['plus_minus'] = "-";
	// When item in of RGP
	$table[8]['q'] = "SELECT SUM(qty) as tot FROM ch_rgp_receive_item WHERE itemId = $itemId";
	$table[8]['plus_minus'] = "+";
	
	// When item out annexure 
	$table[9]['q'] = "SELECT SUM(qty) as tot FROM ch_annexure_item WHERE itemId = $itemId";
	$table[9]['plus_minus'] = "-";
	// When item in of annexure
	$table[10]['q'] = "SELECT SUM(qty) as tot FROM ch_annexure_receive_item WHERE itemId = $itemId";
	$table[10]['plus_minus'] = "+";
	
	foreach($table as $key=>$value){
		list($opt, $rs) = run_query($dbc, $value['q']);
		$sum = $value['plus_minus'] == '+' ? $sum+$rs['tot'] : $sum-$rs['tot'];		
	}
	return $sum;
	
	
	/*$q = "SELECT SUM(qty) as qty FROM stock_item WHERE itemId = $itemId";
	list($opt, $rs) = run_query($dbc, $q, $mode='single');
	if($rs['qty']==0) 
		return 0;
	else 
		return $rs['qty'];*/
}
?>
