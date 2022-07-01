<?php
//this function will give the final view of the data submitted
function final_view($submit,$display,$hidden,$not_to_show)
{
	$i = 0;
	$fname = '';
	$str = '<form method="post" class="iform"><div style="background:#CCC;padding:0 auto;"><center><h1>You Are About To '.ucwords($submit).' This.</h1>';
	foreach($display as $key => $value)
	{
		if($fname == '')
		$fname = $key;
			$str .= '<input type="hidden" name="'.$key.'" value="'.$hidden[$key].'">';
			
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
	$str .= '</center></div><br><table width="100%" style="clear:both;"><tr><th><input autofocus type="submit" name="submit" value="Confirm '.$submit.'"><input type="button" name="submit" value="EDIT" onclick="window.location=\'#workarea\';  "></th></tr></table></form>';
	return $str;
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
function encode_price($salt,$price)
{
	$str = substr($price,1,strlen($price)-1);
	
	return $salt[$price[0]-1].$str;
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

?>
<?php
function db_pulldown($dbc, $name, $q, $arrkey = false, $inioption = true, $jsfunction='',$firstoptiontext = '== Please select ==')
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
					$optarray[$row[0]] = $row[1];	
				}
			}
			else
			{
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[0]] = $row[0];	
					//$optarray[] = $row[1];	
				}
			}
			if($inioption)
				echo'<option value="">'.$firstoptiontext.'</option>';
			foreach($optarray as $key => $value)
			{
				echo'<option value="'.$key.'"'; if(isset($_POST[$name]) && $_POST[$name] == $key) echo'selected="selected"'; echo'>'.$value.'</option>'."\n";	
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

function db_pulldownedit($dbc, $name, $q, $arrkey = false, $inioption = true, $jsfunction='', $array, $firstoptiontext = '== Please select ==')
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
					$optarray[$row[0]] = $row[1];	
				}
			}
			else
			{
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[0]] = $row[0];	
					//$optarray[] = $row[1];	
				}
			}
			if($inioption)
				echo'<option value="">'.$firstoptiontext.'</option>';
			foreach($optarray as $key => $value)
			{
				echo'<option value="'.$key.'"'; if(isset($_POST[$name]) && $_POST[$name] == $key) echo'selected="selected"'; if(isset($array) && is_array($array)){ if(!in_array($key, $array)) echo'disabled="disabled"';} echo'>'.$value.'</option>'."\n";
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

function db_pulldown1($dbc, $name, $q, $arrkey = false, $inioption = true, $jsfunction='', $id,  $firstoptiontext = '== Please select ==')
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
					$optarray[$row[0]] = $row[1];	
				}
			}
			else
			{
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[0]] = $row[0];	
					//$optarray[] = $row[1];	
				}
			}
			if($inioption)
				echo'<option value="">'.$firstoptiontext.'</option>';
			foreach($optarray as $key => $value)
			{
					echo'<option value="'.$key.'"'; if($key == $id) echo'selected="selected"'; echo'>'.$value.'</option>'."\n";
				
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


//This function will create a pull down menu from a list of value specified in array
/*
	arr_pulldown($name, $arritem = array(), $msg='', $usearrkey=false, $ini_option=true, $jsfunction='', $name_array=false )
	
	The paramters of the function are:-
	 (1)  $name = it is the name of the select menu and is compulsory
	 (2)  $arritem = array of the items to be shown in the pulldown
	 (3)  [] $msg = text to be shown if this field is not selected.
	 (4)  [] $usearrkey = whether to use the array key for value attribute of the option element
	 (5)  [] $ini_option = whether to show plese select.. as the first option in the pull down
	 (6)  [] $jsfunction = javascript function to run
	 (7)  [] $name_array = whether to make select name as as an array used with add more system
	
	[] indicates that the field is not mandatory
	
	USAGE:
	 arr_pulldown('itm_cat', array('Raw Material', 'Packing'), $msg='Please select Item category', $usearrkey=false, $ini_option=true)
*/
function arr_pulldown($name, $arritem = array(), $msg='', $usearrkey=false, $ini_option=true, $jsfunction='', $name_array = false, $firstoptiontext = 'Please select...')
{
	if(is_array($arritem))
	{
		if($name_array) $mkarray = '[]'; else $mkarray = '';// whether to make select name as as an array used with add more system
		echo'
		<select name="'.$name.$mkarray.'" ';  if(!empty($jsfunction))echo $jsfunction; echo'>';
		if($ini_option) echo'<option value="">'.$firstoptiontext.'</option>';
		foreach($arritem as $key => $value)
		{
			if(!$usearrkey) // whether to use array keys or not
				$key = $value;
			echo'<option value="'.$key.'"'; if(isset($_POST[$name]) && $_POST[$name] == $key) echo'selected="selected"'; echo'>'.$value.'</option>'."\n";
		}
		echo'</select>';
		if(!empty($msg) && isset($_POST[$name]) && empty($_POST[$name]))
			echo'<span class="error">'.$msg.'</span>';
	}
}

//
function run_query($dbc, $q, $mode='single', $msg='sorry no  record found')
{
	global $local;
	$r = mysqli_query($dbc, $q);
	//$noofrows = mysqli_num_rows($r);
	if($r)
	{
		switch($mode)
		{
			case'single':
			{
				if(mysqli_num_rows($r) == 1)
					return array(true, mysqli_fetch_assoc($r));
				elseif(mysqli_num_rows($r) > 1)
					die('<span class="error">Please change the mode from single to multi</span>');
				elseif(mysqli_num_rows($r) == 0)
					return array(FALSE, '<span class="warn">'.$msg.'</span>');
				break;
			}
			
			case'multi':
			{
				if(mysqli_num_rows($r) > 0)
					return array(true, $r);
				else
					return array(FALSE, '<span class="warn">'.$msg.'</span>');
				break;
			}
			
			default:
			{
				return array(true, mysqli_fetch_assoc($r));
			}
		}
	}
	else
	{
		$mysqlerror = '';
		if(isset($local) && $local) $mysqlerror = '<br/><b>'.mysqli_error($dbc).'</b>';
		return array(FALSE, '<span class="warn">Sorry some error occured in query, please contact admin.'.$mysqlerror.'</span>');
	}
}

// to run the query for insert and update
function run_query_ui($dbc, $q, $mode='single', $msg='sorry no  record found')
{
	global $local;
	if($mode == 'single')
		$r = mysqli_query($dbc, $q);
	else
		$r = mysqli_multi_query($dbc, $q);
	if($r)
	{
		$query = 'update';
		// checking whether the query is of update or insert
		if(stristr($q, 'insert')) $query = 'insert';
		if($query == 'update')
			return array(true, mysqli_affected_rows($dbc));
		else
			return array(true, mysqli_insert_id($dbc));
	}
	else
	{
		$mysqlerror = '';
		if(isset($local) && $local) $mysqlerror = '<br/><b>'.mysqli_error($dbc).'</b>';
		return array(FALSE, '<span class="warn">Sorry some error occured in query, please contact admin.'.$mysqlerror.'</span>');
	}
}

// This script will check whether a given email or username or any other field already present in the database or not and 
// will return either true of false. 
// If true returned it means value found and if false returned value not found
// $field_arry should be in format $field_arry = array(fieldname => value to be matched)
function uniqcheck_msg($dbc,$field_arry,$whichtable, $printmsg = true, $andpart = '')
{
	if(!empty($dbc) && !empty($whichtable) && is_array($field_arry))
	{
		if(!empty($andpart))
			$andpart = 'AND '.$andpart;
		foreach($field_arry as $key => $value)
		{
			if(strpos($value, '<$>'))
			{
				$data = explode('<$>',$value);
				$fvalue = $data[0]; // the value to be searched in database
				$er_label = $data[1]; // label of field to be printed in case of match found
			}
			else
			{
				$fvalue =  $value;
				$er_label = $key;
			}
			
			$qc = "SELECT $key FROM $whichtable WHERE $key = '$fvalue' $andpart  LIMIT 1";
			//echo $qc.'<br/>';
			$rc = mysqli_query($dbc,$qc);
			if(mysqli_num_rows($rc)>0)
			{
				if($printmsg)
				echo'<span class="warn"><b>'.$er_label.'</b> already exists, please provide a different value</a></span>';
				return true;	
			}
		}
		return false;
	}
	else
	die('syntax error occured');
}
// uniqcheck_msg() function ends here

//This function will create a pull down menu from a list of value specified in array
/*
	geteditvalue($dbc, $q='', $ptfields, $mode)
	
	The paramters of the function are:-
	 (1)  $dbc = databse connection
	 (2)  $ptfields = array of the items which will be set like name of post variable to set with corresponding table column name
	 (3)  [] $mode = whether the output of the query will be single or multiple values
	
	[] indicates that the field is not mandatory
	
	USAGE:
	 geteditvalue($dbc, $q='', $ptfields, $mode='single')
*/
function geteditvalue($dbc, $q, $ptfields, $mode='single')
{
	if(is_array($ptfields))
	{
		list($opt, $row) = run_query($dbc, $q, $mode, $msg='sorry no  record found');
		if($opt)
		{
			if($mode == 'single')
			{
				foreach($ptfields as $key=>$value)
				{
					$_POST[$value] = $row[$key];
				}
				return true;
			}
		}
		else
			return false;
	}
}
?><?php
//this function will return the time part as per mysql of provided time format, it takes input as 2:45 pm 
function getmysqltime($time, $seperator = ':')
{
	$time = strtolower($time); // making the complete time to lower
	$period = strtolower(substr($time, -2)); // this will give us either am or pm after strtolower
	$timeonly = rtrim($time, ' '.$period); // this will give us the time part only like 2:45
	$timear = explode(':', $timeonly);
	$secpart = '00';
	if(count($timear) == 3)
		$secpart = $timear[2];
	if($period == 'pm')
		$timear[0] = 12+$timear[0]; // if its pm then add 12 to the hour part
	return $timear[0].$seperator.$timear[1].$seperator.$secpart;
}// getmysqltime function ends here

//function to dd/mm/yyyy to mysqldate if $func = 1 AND mysql date to dd/mm/yyyy
function dateconvert($date,$func)
{
	if ($func == 1)
	{
		 //insert conversion
		$d = explode('/', $date);
		$year = $d[2];
		$month = $d[1];
		$day = $d[0];
		$date = "$year-$month-$day";
		return $date;
	}
	if ($func == 2)
	{
		 //output conversion
		$d = explode('-', $date);
		$year = $d[0];
		$month = $d[1];
		$day = $d[2];
		$date = "$day/$month/$year";
		return $date;
	}
} 
?>
<?php
// This function will stop the user from viewing the page if he is not having the enough permissions
function stop_page_view($value = 0)
{
	if($value != 1)
	{
		echo'<span class="warn">'.MSG_AUTH_VIEW.'</span>';
		include_once ('./include/footer.php');
		exit();
	}
}

//This function will help in storing the last used query in the session so that user can stick to his last search made.
function retainfilter($q, $mode, $formaction, $sesvar = SESS)
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
			
		list($opt, $rs) = run_query($dbc, $_SESSION[$sesvar.'data']['cpage']['q'], $mode='multi', $msg='');
		return array($opt, $rs);
	}
}


//This function will display the msg based on the page being accessed
function user_auth_msg($value = 0, $operation = 'add', $id = '')
{
	$msg  = 'Sorry, <strong>unable to verify the perrmission</strong> for the said operation';
	switch($operation)
	{
		case'add':
		{
			$msg = MSG_AUTH_ADD;
			break;
		}
		case'view':
		{
			$msg = MSG_AUTH_VIEW;
			break;
		}
		case'edit':
		{
			$msg = MSG_AUTH_EDIT;
			break;
		}
		case'del':
		{
			$msg = MSG_AUTH_DEL;
			break;
		}
	}
	if($value == 1) // if user has the said right
	{
		if($operation ==  'edit')
			return checkform($operation, $id);
		elseif($operation ==  'add')
			return checkform();
		else
			return array(true, '');
	}
	else
		return array(false, $msg);
}
// This function check whether a token is valid or not
function valid_token($token)
{
	return ($token == $_SESSION[SESS.'securetoken']) ? 1 : 0;
}

//This function will check whether a email is valid or not
function checkemail($email, $email_pat="#^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$#")
{
	return preg_match($email_pat, $email);	
}

// This function return the  authorization access for a given user and to given view indicated in $inpoption
function user_auth($dbc, $uid, $inpoption = '')
{
	//for user with id =1, there shall be no restriction else the inappropriate updation might lock the panel, so user with id 1
	// will be free from all the restrictions.
	if($_SESSION[SESS.'data']['id'] == 1) return array('add_opt'=>1, 'edit_opt'=>1, 'view_opt'=>1, 'del_opt'=>1, 'sp_opt'=>1, 'superadmin');
	
	if(INDV_MODULE_CONTROL) // checking the indv module control
		$q = "SELECT acr.* FROM admin_account_rights AS acr INNER JOIN admin_modules AS am USING(amId)WHERE acr.id = '$uid' AND am.inpage_option = '$inpoption' LIMIT 1";
	else
		$q = "SELECT act.* FROM admin_account_type AS act INNER JOIN admin AS a ON act.roleId = a.urole WHERE a.id = '$uid' LIMIT 1";
		
	$r = mysqli_query($dbc, $q);
	if($r)
	{
		if(mysqli_num_rows($r)>0)
			return mysqli_fetch_assoc($r);
		else
			die('No authorization credentials found for'.$uid);
	}
	else
		die('user_authorization connection error occured');
}

//The various form buttons in the form
function form_buttons($fieldtofocus = 'rname', $closebutton = '')
{
	//accessing the global paramters
	global $prev; global $next; global $first; global $last; global $open; global $formaction; global $heid;
	if(!empty($closebutton)) $target = $closebutton; else $target = $formaction;
	
	if(JS_OPEN){?><input onclick="formenable('genform');" type="button" value="New" /><?php }?>
    <?php if(isset($heid)) { echo $first."\n".$prev."\n".$next."\n".$last."\n";} // Button for next, prev etc?>
    <input type="submit" name="submit" value="<?php if(isset($heid)) echo'Update'; else echo'Save';?>" />
    <?php if(!isset($heid)) { echo $open;} // Button to open the first record for editing then it disappears?>
    <input onclick="window.document.location = 'index.php?option=<?php echo $target; ?>';" type="button" value="Close" />
	<script type="text/javascript">setfocus('<?php echo $fieldtofocus; ?>');</script>
	<?php if(isset($heid)) echo $heid; //This will give us a hidden field name eid, whose value will be equal to the edit id.
	
	  if(JS_OPEN){ // whether we want to have the js form enable disable or not in our forms
	  if(!isset($_POST['submit']) && !isset($heid))
		echo'<script type="text/javascript">formdisable(\'genform\');</script>';
	  }
}

// This function will clear the span with asm or awm attached with the help of the jquery
function js_span_clear()
{
	echo'<script type="text/javascript">clearspan("asm");</script>';// to clear the span after the javascript msg is displayed
	echo'<script type="text/javascript">clearspan("awm");</script>';// to clear the span after the javascript msg is displayed
}

function exit1($link='', $msg='')
{
	echo $msg;
	if(!empty($link))
	echo'<a class="continue" href="'.$link.'">Click here to continue</a>';
	include_once ('./include/footer.php');
	exit();
}

// This function will convert the date in DD/MM/YYYY to format YYYY-MM-DD, if optional third parameter is passed as true
// current time will also get added in the function
function get_mysql_date($date, $sep='/', $time = false, $mysqlsearch = false)
{
	date_default_timezone_set('Asia/Kolkata');
	$date = trim($date);
	$ndate1 = explode($sep,$date);
	//echo $date.'--->'.count($ndate1);
	if(count($ndate1) != 3) // if after exlosion we do not get the array with three part, then there is some error in the date
		die('The date is not in valid format'.$date);
	// if the month is not numberic, than it could be 3 digit mar or full name like march
	$ndate1[1] = ltrim($ndate1[1], '0');
	if(!is_numeric($ndate1[1]))
	{	
		$ndate1[1] = strtoupper($ndate1[1]); // making the value in uppercase for comparison with the array
		$shortmonth = array('JAN'=>1, 'FEB'=>2, 'MAR'=>3, 'APR'=>4, 'MAY'=>5, 'JUN'=>6, 'JUL'=>7, 'AUG'=>8, 'SEP'=>9, 'OCT'=>10, 'NOV'=>11, 'DEC'=>12);
		$longmonth = array('JANUARY'=>1, 'FEBRUARY'=>2, 'MARCH'=>3, 'APRIL'=>4, 'MAY'=>5, 'JUNE'=>6, 'JUL'=>7, 'AUGUST'=>8, 'SEPTEMBER'=>9, 'OCTOBER'=>10, 'NOVEMBER'=>11, 'DECEMBER'=>12);
		if(strlen($ndate1[1]) == 3)
			$ndate1[1] = $shortmonth[$ndate1[1]];
		else
			$ndate1[1] = $longmonth[$ndate1[1]];
	}
	if(strlen($ndate1[1]) == 1) $ndate1[1] = '0'.$ndate1[1];
	
	$rdate = $ndate1[2].'-'.$ndate1[1].'-'.$ndate1[0];	
	//$rdate = str_replace("-","/",$rdate); // to make the date format consistant
	//adding the time part if  required
	$timepart = '';
	if($time)
	{
		$stamp = time();
		$gd = getdate($stamp);
		$timepart = ' '.$gd['hours'].':'.$gd['minutes'].':'.$gd['seconds'];
	}
	//To return the date if we are using a search query in MYSQL
	return !$mysqlsearch ?	$rdate.$timepart : str_replace('-','',$rdate.$timepart);
}// function get_mysql_date ends here

// This function will convert the date in DD/MM/YYYY to format YYYY-MM-DD, if optional third parameter is passed as true
// current time will also get added in the function
function get_mysql_date1($date, $sep='/', $time = false, $mysqlsearch = false)
{
	date_default_timezone_set('Asia/Kolkata');
	$date = trim($date);
	$ndate1 = explode($sep,$date);
	//echo $date.'--->'.count($ndate1);
	if(count($ndate1) != 3) // if after exlosion we do not get the array with three part, then there is some error in the date
		die('The date is not in valid format'.$date);
	// if the month is not numberic, than it could be 3 digit mar or full name like march
	$ndate1[0] = ltrim($ndate1[0], '0');
	if(!is_numeric($ndate1[0]))
	{	
		$ndate1[0] = strtoupper($ndate1[0]); // making the value in uppercase for comparison with the array
		$shortmonth = array('JAN'=>1, 'FEB'=>2, 'MAR'=>3, 'APR'=>4, 'MAY'=>5, 'JUN'=>6, 'JUL'=>7, 'AUG'=>8, 'SEP'=>9, 'OCT'=>10, 'NOV'=>11, 'DEC'=>12);
		$longmonth = array('JANUARY'=>1, 'FEBRUARY'=>2, 'MARCH'=>3, 'APRIL'=>4, 'MAY'=>5, 'JUNE'=>6, 'JUL'=>7, 'AUGUST'=>8, 'SEPTEMBER'=>9, 'OCTOBER'=>10, 'NOVEMBER'=>11, 'DECEMBER'=>12);
		if(strlen($ndate1[0]) == 3)
			$ndate1[0] = $shortmonth[$ndate1[0]];
		else
			$ndate1[0] = $longmonth[$ndate1[0]];
	}
	
	if(strlen($ndate1[0]) == 1) $ndate1[0] = '0'.$ndate1[1];
	
	$rdate = $ndate1[2].'-'.$ndate1[0].'-'.$ndate1[1];	
	//$rdate = str_replace("-","/",$rdate); // to make the date format consistant
	//adding the time part if  required
	$timepart = '';
	if($time)
	{
		$stamp = time();
		$gd = getdate($stamp);
		$timepart = ' '.$gd['hours'].':'.$gd['minutes'].':'.$gd['seconds'];
	}
	//To return the date if we are using a search query in MYSQL
	return !$mysqlsearch ?	$rdate.$timepart : str_replace('-','',$rdate.$timepart);
}// function get_mysql_date1 ends here

// This function will convert the date from the mysql into masked type date
function convert_mysql_date($date, $sep='/')
{
	$blank = '';
	if($date == '0000-00-00 00:00:00') return $blank;
	if($date == '0000-00-00') return $blank;
	$ndate1 = explode(' ',$date);
	$datepart = explode('-', $ndate1[0]);
	return ("{$datepart[2]}$sep{$datepart[1]}$sep{$datepart[0]}");
}

// This function will return the numeric representation of a month
function get_month_numeric($value)
{
	if(!is_numeric($value))
	{	
		$value = strtoupper($value); // making the value in uppercase for comparison with the array
		$shortmonth = array('JAN'=>1, 'FEB'=>2, 'MAR'=>3, 'APR'=>4, 'MAY'=>5, 'JUN'=>6, 'JUL'=>7, 'AUG'=>8, 'SEP'=>9, 'OCT'=>10, 'NOV'=>11, 'DEC'=>12);
		$longmonth = array('JANUARY'=>1, 'FEBRUARY'=>2, 'MARCH'=>3, 'APRIL'=>4, 'MAY'=>5, 'JUNE'=>6, 'JUL'=>7, 'AUGUST'=>8, 'SEPTEMBER'=>9, 'OCTOBER'=>10, 'NOVEMBER'=>11, 'DECEMBER'=>12);
		if(strlen($value) == 3)
			$value = $shortmonth[$value];
		else
			$value = $longmonth[$value];
	}
	return $value;
}// get_month_numeric ends here

// This function will check whether a date is valid or not 
function icheckdate($date = '', $sep = '/')
{
	if(empty($date))	array(TRUE, $date);
	$ndate = get_mysql_date($date, $sep, $time = false);
	$ldateary = explode('-',$ndate);
	if(count($ldateary) != 3)
		return array(FALSE, 'Invalid Date');
	// checkdate ( int $month , int $day , int $year )
	if(!checkdate($ldateary[1], $ldateary[2], $ldateary[0])) 
		return array(FALSE, 'Invalid Date');
	else
		return array(TRUE, $ndate);
}

function magic_quotes_check($dbc, $check=true)
{
	if(!empty($dbc))
	{
		if($check)
		{
			if(get_magic_quotes_gpc())// checking the status of magic quotes to remove extra slashes
				$removeslash = true;
			else
				$removeslash =  false;
			foreach($_POST as $key=> $value)
			{
				if($removeslash)
				{
					if(!is_array($value))
						$value = stripslashes($value);
					elseif(is_array($value))
					{
						foreach($value as $key1=>$value1)
						{
							$value[$key1] = stripslashes($value1);
						}
					}
				}
				
				if(!is_array($value))
				{
					$_POST[$key] = trim($value);
					$_POST[$key] = mysqli_real_escape_string($dbc, $_POST[$key]);
				}
				elseif(is_array($value))
				{
					foreach($value as $key1=>$value1)
					{
						$_POST[$key][$key1] = trim($value1);
						$_POST[$key][$key1]= mysqli_real_escape_string($dbc, $value1);
					}
				}
					
			}
		}
	}
	else
	die('syntax for magic_quotes_check() not correct');
}

function history_log($dbc, $operation = '', $particulars = '')
{	
	$uid = $_SESSION[SESS.'data']['id'];
	if(!empty($operation) && !empty($particulars))
	$q = "INSERT INTO `history_log` (`hid`, `user_id`, `particulars`, `operation`, `ipaddress`, `dated`) VALUES (NULL, '$uid', '$particulars', '$operation', '$_SERVER[REMOTE_ADDR]', NOW())";
	mysqli_query($dbc, $q);
}

// this will convert the date or datetime from calender to a format that can be used with the mysql date_format 
function getmysql_datetime_format($dtstring)
{
	$monthar = array('JAN'=>'01', 'FEB'=>'02', 'MAR'=>'03', 'APR'=>'04', 'MAY'=>'05', 'JUN'=>'06', 'JUL'=>'07', 'AUG'=>'08', 'SEP'=>'09', 'OCT'=>'10', 'NOV'=>'11', 'DEC'=>'12');
	//spliting the date, if the time part is attached, we need to find that ex : 11-Jan-2012 21:31:15
	$seperate = explode(' ',$dtstring);
	if(count($seperate) == 2)
		$time = $seperate[1];
	elseif(count($seperate) == 1)	// indicate that time is not available.
		$time = '00:00:00';
	$date = explode('-',$seperate[0]); // we are breaking 11-Jan-2012
	$year = $date[2];
	$month = $monthar[strtoupper($date[1])];
	$day = $date[0];
	if(strlen($day) == 1) $day = '0'.$day; // this will make 1 as 01
	$mysqldt = $year.'-'.$month.'-'.$day.' '.$time;  
	return($mysqldt); // output will be 2012-01-11 21:31:15
}

// this will convert the date received from the excel file in a proper format so that it can be used in the mysql storage 
function format_excel_date_time($datep, $timep)
{
	$montharray = array('JAN'=>'01', 'FEB'=>'02', 'MAR'=>'03', 'APR'=>'04', 'MAY'=>'05', 'JUN'=>'06', 'JUL'=>'07', 'AUG'=>'08', 'SEP'=>'09', 'OCT'=>10, 'NOV'=>11, 'DEC'=>12);
	//spliting the date, if the time part is attached, we need to find that ex : 11-Jan-2012 21:31:15
	$seperate = explode('-',$datep);
	if(count($seperate) == 3)
	{
		$day = $seperate[0];
		$emonth = $seperate[1];
		$year = $seperate[2];
	}
	else
	{
		echo $msg = "The date format in the excel file is <strong>not readable</strong>, so entries will not be stored,<br> please <strong>check with the sample format func.</strong>";
		exit();
	}
	if(!is_numeric($emonth))
		$month = $montharray[strtoupper(substr($emonth,0,3))];	
	else
		$month = $emonth;
	if(strlen($day) == 1) $day = '0'.$day; // this will make 1 as 01
	$mysqldt = $year.'-'.$month.'-'.$day.' '.$timep;  
	return($mysqldt); // output will be 2012-01-11 21:31:15
}

//This function will provide the date if the date is read as 11-3-2012 instead of 3-11-2012 the common problems for errors.
// this will convert the date received from the excel file in a proper format so that it can be used in the mysql storage 
function format_excel_date_time2($datep, $timep)
{
	$montharray = array('JAN'=>'01', 'FEB'=>'02', 'MAR'=>'03', 'APR'=>'04', 'MAY'=>'05', 'JUN'=>'06', 'JUL'=>'07', 'AUG'=>'08', 'SEP'=>'09', 'OCT'=>10, 'NOV'=>11, 'DEC'=>12);
	//spliting the date, if the time part is attached, we need to find that ex : 11-Jan-2012 21:31:15
	$seperate = explode('-',$datep);
	if(count($seperate) == 3)
	{
		$day = $seperate[1];
		$emonth = $seperate[0];
		$year = $seperate[2];
	}
	else
	{
		echo $msg = "The date format in the excel file is <strong>not readable</strong>, so entries will not be stored,<br> please <strong>check with the sample format func.</strong>";
		exit();
	}
	if(!is_numeric($emonth))
		$month = $montharray[strtoupper(substr($emonth,0,3))];	
	else
		$month = $emonth;
	if(strlen($day) == 1) $day = '0'.$day; // this will make 1 as 01
	$mysqldt = $year.'-'.$month.'-'.$day.' '.$timep;  
	return($mysqldt); // output will be 2012-01-11 21:31:15
}


// This function will provide the previous and next button functionality starts here
function prev_next($id = '', $table = '', $formaction, $conditions = '')
{
	global $dbc;
	if(empty($id) || empty($table)) die('ID & table are required for this functionality');
	$first = '<input type="submit" name="first" value="< First" />';
	$prev = '<input type="submit" name="prev" value="<< Previous" />';
	$next = '<input type="submit" name="next" value="Next >>" />';
	$last = '<input type="submit" name="last" value="Last >" />';
	$eformaction = ''; // to make the form action change to the id of the row being edited
	$open = '';
	//To give the open button at the start of the form to take the user to the first row
	$wcondition = $acondition = '';
	if(!empty($conditions))
	{
		$wcondition = " WHERE $conditions ";
		$acondition = " AND $conditions ";
	}
	if(!isset($_POST['eid']))
	{
		list($foutput, $fnum) = run_query($dbc, $q="SELECT $id FROM $table $wcondition order by $id ASC LIMIT 1", $mode='single', $msg='sorry no  record found');	
		if($foutput)
		{
		  $iopen = 'window.document.location = \'index.php?option='.$formaction.'&mode=1&id='.$fnum[$id].'\'';
		  $open = '<input type="button" name="open" value = "Open" onclick="'.$iopen.'" />';
		}
	}
	
	// When the first submit button was clicked
	if(isset($_POST['first']))
	{
		list($foutput, $fnum) = run_query($dbc, $q="SELECT $id FROM $table $wcondition order by $id ASC LIMIT 1", $mode='single', $msg='sorry no  record found');	
		if($foutput)
		{
			//Moving the pointer to the first row
			$_GET['id'] = $fnum[$id];
			$prev = '<input type="submit" name="prev" value="<< Previous" disabled="disabled" class="disabled" />';
			$eformaction = 'index.php?option='.$formaction.'&mode=1&id='.$_GET['id'];
		}
		else
			$first = $prev = $next = $last = ''; // becuase if there are no data, then these buttons make no sense
	}
	
	// When the previous submit button was clicked
	if(isset($_POST['prev']))
	{
		list($foutput, $fnum) = run_query($dbc, $q="SELECT $id, (SELECT count($id) FROM $table WHERE $id < $_POST[eid] $acondition ORDER BY $id ASC LIMIT 2) AS balrow FROM $table WHERE $id < $_POST[eid] $acondition ORDER BY $id DESC LIMIT 1", $mode='single', $msg= 'sorry no  record found');
		if($foutput)
		{
			//Moving the pointer to the prev row
			$_GET['id'] = $fnum[$id];
			if($fnum['balrow'] == 1) // because if this is the last row then no need to show the previous button in other case
				$prev = '<input type="submit" name="prev" value="<< Previous" disabled="disabled" class="disabled" />';          // bal row will always be greater than 1
				$eformaction = 'index.php?option='.$formaction.'&mode=1&id='.$_GET['id'];
		}
		else
			$prev = '<input type="submit" name="prev" value="<< Previous" disabled="disabled" class="disabled" />';
	}
	
	// When the next submit button was clicked
	if(isset($_POST['next']))
	{
		//list($foutput, $fnum) = run_query($dbc, $q="SELECT $id FROM $table order by $id ASC LIMIT $_POST[eid], 1", $mode='single', $msg= 'sorry no  record found');
		list($foutput, $fnum) = run_query($dbc, $q="SELECT $id, (SELECT count($id) FROM $table WHERE $id > $_POST[eid] $acondition ORDER BY $id DESC LIMIT 2) AS balrow FROM $table WHERE $id > $_POST[eid] $acondition ORDER BY $id ASC LIMIT 1", $mode='single', $msg= 'sorry no  record found');
		if($foutput)
		{
			//Moving the pointer to the next row
			$_GET['id'] = $fnum[$id];
			if($fnum['balrow'] == 1) // because if this is the last row then no need to show the previous button in other case
				$next = '<input type="submit" name="next" value="Next >>" disabled="disabled" class="disabled" />';     
			$eformaction = 'index.php?option='.$formaction.'&mode=1&id='.$_GET['id'];
		}
		else
			$next = '<input type="submit" name="next" value="Next >>" disabled="disabled" class="disabled" />';
	}
	
	// When the last submit button was clicked
	if(isset($_POST['last']))
	{
		list($foutput, $fnum) = run_query($dbc, $q="SELECT $id FROM $table $wcondition order by $id DESC LIMIT 1", $mode='single', $msg='sorry no  record found');	
		if($foutput)
		{
			//Moving the pointer to the last row
			$_GET['id'] = $fnum[$id];
			$eformaction = 'index.php?option='.$formaction.'&mode=1&id='.$_GET['id'];
			$next = '<input type="submit" name="next" value="Next >>" disabled="disabled" class="disabled" />';
		}
	}	
	return array($open, $first, $prev, $next, $last, $eformaction);
}
// This function will provide the previous and next button functionality ends here

/*       FUNCTION TO SEND EMAIL STARTS HERE       */
function email($mailto,$sub,$msg,$attachment = false,$filename='',$path='')
{
	//mail(
	//echo $msg;
	if(!$attachment)
	{
		if(mail($mailto,$sub,$msg))
		{
			return true.'<$>';
		}
		else
		{
			return false.'<$>Mail Without Attachment Sending Failed';
		}
	}
		$file = $path.$filename;
		$dir = 'mail_dir';
		if(!is_dir($dir))
		{
			mkdir($dir);
		}
		$new_path = date('dmyHis').rand(0,9999).'jpg';
		if(copy($file,$dir.$new_path))
		{
			$file_size = filesize($dir.$new_path);
			$handle = fopen($dir.$new_path, "r");
			$content = fread($handle, $file_size);
			fclose($handle);
			$content = chunk_split(base64_encode($content));
			$uid = md5(uniqid(time()));
			$name = basename($dir.$new_path);
			$header .= "MIME-Version: 1.0\r\n";
			$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
			$header .= "This is a multi-part message in MIME format.\r\n";
			$header .= "--".$uid."\r\n";
			$header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
			$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
			$header .= $msg."\r\n\r\n";
			$header .= "--".$uid."\r\n";
			$header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here
			$header .= "Content-Transfer-Encoding: base64\r\n";
			$header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
			$header .= $content."\r\n\r\n";
			$header .= "--".$uid."--";
			if (mail($mailto, $subject, "", $header)) 
			{
				unlink($dir.$new_path);
				rmdir($dir);
				return true.'<$>';
				//echo "mail send ... OK"; // or use booleans here
			}
			 else 
			 {
					 unlink($dir.$new_path);
					rmdir($dir);
				//echo "mail send ... ERROR!";
				return false.'<$>mail with attachment sending failed';
			 }
		}
		else
		{
			return false.'<$>Problem With the file path';
		}
}
/*         FUNCTION TO SEND EMAIL ENDS HERE            */

// function to send email with phpmailer start here
function mailsend($usmtp = FALSE, $to, $subject, $mailbody, $from, $toname='', $fromname='')
{
	require_once(BASE_URI.'/functions/class.phpmailer.php');
	try {
		  $mail = new PHPMailer(true); //defaults to using php "mail()"; the true param means it will throw exceptions on errors, which we need to catch
		  if($usmtp)
		  {
				$mail->IsSMTP(); 
				//$mail->SMTPDebug  = 2;
				$mail->SMTPAuth   = true; 
				$mail->SMTPSecure = "ssl";
				$mail->Host       = "smtp.gmail.com";
				$mail->Port       = 465;
				$mail->Username   = "test@weboseo.com";
				$mail->Password   = 'TEST$test';
		  }
		  $mail->SMTPKeepAlive = true;                  // SMTP connection will not close after each email sent
		  $mail->AddReplyTo($from, $fromname);
		  $mail->SetFrom($from, $fromname);
		  $mail->Subject = $subject;
		  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
		  //$mail->MsgHTML(file_get_contents('contents.html'));
		  $mail->MsgHTML($mailbody);
		  $mail->AddAddress($to, $toname);
		  if(!$mail->Send())
		  	return array(FALSE, $mail->ErrorInfo);
		  else
		  	return array(TRUE, '');
		 $mail->ClearAddresses();
		} catch (phpmailerException $e) {
		  echo $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
		  echo $e->getMessage(); //Boring error messages from anything else!
		}
}
// function to send email with phpmailer ends here

function pre($value)
{
	if(is_array($value))
	{
		echo'<pre>';
		print_r($value);
		echo'</pre>';
	}
	else
		die('<strong>pre function</strong> takes an <strong>array as argument</strong> the value it got is <b>'.$value.'</b>');
}

// This function takes a file and returns it name and extensinon
function get_extension($file)
{
	$extension = substr($file, strrpos($file, '.') + 1);
    $extension = strtolower($extension);
    $justname = rtrim($file,'.'.$extension);
	return array('ext'=>$extension, 'justname'=>$justname);
}

//This function will return the age of the person as of 27years 1month 23days
function format_age($dob = '26/09/1985')
{
	$vddate = str_replace('/','.', $dob);
	$vddate = str_replace('-','.', $vddate);
	$dobar = explode('.', $vddate);
	$value = $dobar[1];
	//This check is done to incorporate the oct or october instead of numeric representation of the month
	if(!is_numeric($value))
	{	
		$value = strtoupper($value); // making the value in uppercase for comparison with the array

		$shortmonth = array('JAN'=>1, 'FEB'=>2, 'MAR'=>3, 'APR'=>4, 'MAY'=>5, 'JUN'=>6, 'JUL'=>7, 'AUG'=>8, 'SEP'=>9, 'OCT'=>10, 'NOV'=>11, 'DEC'=>12);
		$longmonth = array('JANUARY'=>1, 'FEBRUARY'=>2, 'MARCH'=>3, 'APRIL'=>4, 'MAY'=>5, 'JUNE'=>6, 'JUL'=>7, 'AUGUST'=>8, 'SEPTEMBER'=>9, 'OCTOBER'=>10, 'NOVEMBER'=>11, 'DECEMBER'=>12);
		if(strlen($value) == 3)
			$value = $shortmonth[$value];
		else
			$value = $longmonth[$value];
	}
	$dobstring = (int)$dobar[0].'.'.(int)$value.'.'.(int)$dobar[2];
	$bday = new DateTime($dobstring);
	$today = new DateTime('00:00:00'); // use this for the current date
	$diff = $today->diff($bday);
	return sprintf('%d years, %d months, %d days', $diff->y, $diff->m, $diff->d);
}

function h1($check='testpoint')
{
	echo'<h1><----'.$check.'---></h1>';	
}

//This function will export the data into the excel 
function excel_out($input = array(), $reportname = 'report')
{
	if(empty($input)) return;
	// defining the fields of enclosures, seperators and linebreak starts
	$encloser = '"';
	$seperator = "\t";
	$linebreak = "\n";
	// defining the fields of enclosures, seperators and linebreak ends
	
	$excel_str_header = $excel_str_body = '';
	$headerinfo = array();
	// making the excel outputbody string
	foreach($input as $key=>$value)
	{
		foreach($value as $key1=>$value1)
		{
			$excel_str_body .= $encloser.$value1.$encloser.$seperator;
			$headerinfo[$key1] = $key1;
		}
		$excel_str_body .= $linebreak;
	}	
	
	// making the excel output header string
	foreach($headerinfo as $key=>$value)
		$excel_str_header .= $encloser.$value.$encloser.$seperator;
	$excel_str_header .= $linebreak;
	
	#- code to allow the downloas starts here
	ob_clean();
	echo $excel_str_header.$excel_str_body;
	$filename = "$reportname.xls";
	header("Content-type: application/vnd.ms-excel");	
	header("Content-Length: ".ob_get_length()); // for IE to work
	header('Content-Disposition: inline; filename="'.$filename.'"');
	header('Content-Transfer-Encoding: binary');
	exit();
	#- code to allow the downloas ends here
}
?>