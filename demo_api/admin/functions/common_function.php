<?php
// This function check whether interner connection is on or off
function is_connected()
{
    $connected = @fsockopen("www.google.com", 80); //website, port  (try 80 or 443)
    if ($connected){
        $is_conn = true; //action when connected
        fclose($connected);
    }else{
        $is_conn = false; //action in connection failure
    }
    return $is_conn;

}
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

function dealer_view_page_auth()
{
	
        echo'<span class="warn">'.MSG_AUTH_DEALER.'</span>';
        include_once ('./include/footer.php');
        exit();
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
			return checkform($operation, $id);
		elseif($operation ==  'del')
			return checkform($operation, $id);
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

function valid_token_f($token)
{
	return ($token == $_SESSION[FSESS.'securetoken']) ? 1 : 0;
}

//This function will check whether a email is valid or not
function checkemail($email, $email_pat="#^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$#")
{
	return preg_match($email_pat, $email);	
}

function security_check($checkpnt = '')
{
	if(empty($checkpnt)) return;
	$cur_checkpnt = (int)date('Ymd');
	if($cur_checkpnt >= $checkpnt)
	{
		echo'<span class="warn" style="margin-top:10%;">Some of the required system files, needs update. Please contact the administrator.<Br/> <strong>Error Code : PNRAD</strong></span>';
		include_once ('./include/footer.php');
		exit();
	}
}

// This function return the  authorization access for a given user and to given view indicated in $inpoption
function user_auth($dbc, $uid, $inpoption = '')
{
	//for user with id =1, there shall be no restriction else the inappropriate updation might lock the panel, so user with id 1
	// will be free from all the restrictions.
        return array('add_opt'=>1, 'edit_opt'=>1, 'view_opt'=>1, 'del_opt'=>1, 'sp_opt'=>1, 'superadmin');
	if($_SESSION[SESS.'data']['urole'] == 1) return array('add_opt'=>1, 'edit_opt'=>1, 'view_opt'=>1, 'del_opt'=>1, 'sp_opt'=>1, 'superadmin');
	
	if(INDV_MODULE_CONTROL) // checking the indv module control
		 $q = "SELECT acr.* FROM person_modules_rights AS acr INNER JOIN _modules AS am USING(module_id) WHERE acr.person_id = '$uid' AND am.inpage_option = '$inpoption' LIMIT 1";
        
	else
		$q = "SELECT act.* FROM _role AS act INNER JOIN person AS a ON act.role_id = a.role_id WHERE a.id = '$uid' LIMIT 1";
	
        //echo $q;exit();
        
	$r = mysqli_query($dbc, $q);
	if($r)
	{
		if(mysqli_num_rows($r)>0)
			return mysqli_fetch_assoc($r);
		else
		{
			echo'<span class="warn">'.MSG_AUTH_NO.'</span>';
			include_once ('./include/footer.php');
			exit();
		}
	}
	else
		die('<span class="warn">User authorization connection error occured</span>');
}

//The various form buttons in the form
function form_buttons($fieldtofocus = 'rname', $closebutton = '')
{
	global $showmode;
	//accessing the global paramters
	global $prev; global $next; global $first; global $last; global $open; global $formaction; global $heid;
	if(!empty($closebutton)) $target = $closebutton; else $target = $formaction;
	
	if(JS_OPEN){?><input onclick="formenable('genform');" type="button" value="New" /><?php }?>
    <?php if(isset($heid)) { echo $first."\n".$prev."\n".$next."\n".$last."\n";} // Button for next, prev etc?>
    <input type="submit" id="mysave" name="submit" value="<?php if(isset($heid)) echo'Update'; else echo'Save';?>" />
    <?php if(!isset($heid)) { echo $open;} // Button to open the first record for editing then it disappears?>
    <?php 
	if(isset($heid) && $showmode == 2) // we are editing in popup
		$location = "parent.$.fn.colorbox.close();";
	else 
		$location = "window.document.location = 'index.php?option=$target';";
	?>
    <input onclick="<?php echo $location;?>" type="button" value="Close" />
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

function exit1($link='', $msg='', $class = 'warn')
{
	echo $msg;
	if(!empty($link))
	echo'<a class="continue" href="'.$link.'">Click here to continue</a>';
	include_once ('./include/footer.php');
	exit();
}

function myquit($option = array('msg'=>''), $qtype='w')
{
	$msg = is_array($option) ? $option['msg']:$option;
	switch($qtype)
	{
		case's':
			$qtype = 'successmssg';
			break;
		case'ex':
			$qtype = 'exclaim';
			break;
		default:
			$qtype = 'warn';
			break;	
	}
	if(!empty($msg))
		echo'<span class="'.$qtype.'">'.$msg.'</span>';
	include_once ('./include/footer.php');
	exit();
}

function squit($option)
{
	$msg = is_array($option) ? $option['msg']:$option;
	echo'<span class="warn">'.$msg.'</span>';
	include_once ('./include/footer.php');
	exit();
}

//this function will return the time part as per mysql of provided time format, it takes input as 2:45 pm 
function getmysqltime($time, $seperator = ':')
{
	if(empty($time)) return '';
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

// This function will convert the date in DD/MM/YYYY to format YYYY-MM-DD, if optional third parameter is passed as true
// current time will also get added in the function
function get_mysql_date($date, $sep='/', $time = false, $mysqlsearch = false)
{
	if(empty($date)) return '';
	date_default_timezone_set('Asia/Kolkata');
	$date = trim($date);
	$ndate1 = explode($sep,$date);
	//echo $date.'--->'.count($ndate1);
	if(count($ndate1) != 3) // if after exlosion we do not get the array with three part, then there is some error in the date
		return '';//die('The date is not in valid format'.$date);
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

function history_log($dbc, $operation = '', $particulars = '', $identifier = '')
{	
	$uid = $_SESSION[SESS.'data']['id'];
	if(!empty($operation) && !empty($particulars))
	$q = "INSERT INTO `history_log` (`hid`, `user_id`, `particulars`, `operation`, `ipaddress`, `dated`, `identifier`) VALUES (NULL, '$uid', '$particulars', '$operation', '$_SERVER[REMOTE_ADDR]', NOW(), '$identifier')";
	if(mysqli_query($dbc, $q)) return mysqli_insert_id($dbc); else return NULL;
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

//This function will do the header call
function iheader($location = '', $msg='')
{
	if(!empty($location))
	{
		$location = BASE_URL.$location;
		ob_clean();
		header("Location:$location");
		exit();
	}
}

function sendToMobile($message,$mobiles)
{
	//echo'<h3>'.$message.'</h3>';
	//pre($mobiles);
	//return;
	if(is_array($mobiles)) $mobile = implode(',',$mobiles);else	$mobile = $mobiles;
	if(true || !empty(self::$receiverid)) //self::$receiverid will created by originalMessage($msgdetails)
	{
		$username="20130189";
		$api_password="Ravimittal$1";
		$sender="Uandif";
		$domain="103.247.98.91";
		$priority="1";// 1-Normal,2-Priority,3-Marketing,4-Transactional
		$method="POST";
		$mobile=$mobile;
		$message=$message;
		$username=urlencode($username);
		$password=urlencode($api_password);
		$sender=urlencode($sender);
		$message=urlencode($message);
		$schtm = urlencode('2013-05-28 21:02');
		
		
		$parameters="uname=$username&pass=$api_password&dest=$mobile&msg=$message&send=$sender&priority=$priority&schtm=$schtm";
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
			$url = "http://$domain/PRPAPI/PrpApiSendMsg.aspx?$parameters";
			$fp = fopen($url, "r", false, $context);
		}
		else
		{
			$fp = fopen("http://$domain/sendhttp.php?$parameters", "r");
		}
		
		$response = stream_get_contents($fp);
		fpassthru($fp);
		fclose($fp);
	
		/*if($response=="")
		echo "Process Failed, Please check domain, username and password.";
		else
		echo "$response";*/

	}
}

function formatmycurrency($amount='', $echo = true, $precission=2, $formatstyle='INDIAN')
{
	if(empty($amount)) return;
	$out = '';
	switch($formatstyle)
	{
		case'INDIAN':
		{
			$amount = (int) $amount;
			$out = '<img src="./images/rupee.png" />'. formatInIndianStyle($amount);
			break;
		}
		case'US':
		{
			$out = '<img src="./images/rupee.png" />'. number_format($amount,$precission);
			break;
		}
	}
	//whether user want to print or get it as string.
	if($echo) 
		echo $out;
	else
		return ($out);
}
//This function will create a breadcum 
function breadcum($menu, $options=array())
{
	if(!is_array($menu)) return'';
	$h1 = isset($options['h1']) ? '<h1>'.$options['h1'].'</h1>':'';
	$out = $h1.'<div id="breadcumb">';
	$inc = count($menu);
	foreach($menu as $key=>$value)	
	{	
		$key = is_numeric($key) ? '#':$key;
		if($inc == 1)
			$out .= '<a style="color:#2dcf5f;" href="'.$key.'">'.$value.'</a>';
		else
			$out .= '<a href="'.$key.'">'.$value.'</a> &raquo; ';
		$inc--;
	}
	$out .= '<span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>';
	if(isset($options['bmenu'])) $out .= breadcumMenu($options['bmenu']);
	return $out.'</div>';
	
	/*<div id="breadcumb"><a href="#">Master</a> &raquo; <a href="#">Items</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;">Catalogue</a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span></div>*/
}

//This function will create a breadcum menu on the breadcum
function breadcumMenu($in, $str = '')
{
	
	$str = empty($str) ? 'index.php' : 'indexpop.php';
	if(!is_array($in)) return;
	//choosing which label to be higlighted
	$setlabel = '';
	if(isset($in['setlabel'])){
		$setlabel = $in['setlabel'];
		unset($in['setlabel']);
	}	
	$length = strlen($setlabel);
	$out = '';
	$match = false;
	foreach($in as $key=>$value){
		if(!$match){
			if($key == $setlabel) $match = true;
			if(!$match){if(substr($key,0, $length) == $setlabel && $length == strlen($key)) $match = true;}
			if($match)	
				$out .= '<li><a style="background-color:#FF9933;" href="'.$str.'?option='.$key.'">'.$value.'</a></li>';
			else
				$out .= '<li><a href="'.$str.'?option='.$key.'">'.$value.'</a></li>';
		}
		else
			$out .= '<li><a href="'.$str.'?option='.$key.'">'.$value.'</a></li>';
	}
	$out = '<div style="float:right; margin-top:-7px; margin-right:30px;" id="msgmenu"><ul class="msgmenu">'.$out.'</ul></div>';
	return $out;
}

// This function takes a file and returns it name and extensinon
function get_file_icon($file, $folderpath = '')
{
	$extension = substr($file, strrpos($file, '.') + 1);
    $extension = strtolower($extension);
    $justname = rtrim($file,'.'.$extension);
	$iconname = 'unknown.png';
	if($extension == 'rar') $iconname = 'rar.png';
	elseif($extension == 'doc' || $extension == 'docx') $iconname = 'doc.png';
	elseif($extension == 'pdf') $iconname = 'pdf.png';
	elseif($extension == 'xls' || $extension == 'xlsx') $iconname = 'xls.png';
	elseif($extension == 'zip' || $extension == '7z') $iconname = 'zip.png';
	elseif($extension == 'ppt') $iconname = 'ppt.png';	
	return $folderpath.$iconname;
}

function _prepare_url_text($string, $seperator='-')
{
	$not_accept = '#[^-a-zA-Z0-9_ ]#';	
	$string = preg_replace($not_accept,'', $string);	
	$string = trim($string);
	$string = preg_replace('#[-_ ]+#', $seperator, $string);	
	return $string;
}

//This function will provide the navigation links in the editing mode
function edit_links_via_js($curId=NULL, $jsclose=true, $options=array())
{
	?>
    <script type="text/javascript">
    function get_navigation()
	{
		var $matchtr = window.parent.$('#searchdata tr.ihighlight') || $('#searchdata tr.ihighlight');
		var total = $matchtr.size();
		var curid = <?php echo $curId;?>;
		//if we have 0 or more elements then hide the navigation
		if(total <= 1)	return;
		if(total > 0)
		{
			var myfirst = myprev = mynext = mylast =  atindex = '';						
			$($matchtr).each(function(index) {
				temp = (this.id).replace(/tr/, '');
				if(temp == curid)
				{
					atindex = index;
					return;
				}
			});
			
			myfirst = (($matchtr.get(0)).id).replace(/tr/, '');
			
			//if we click the first element then first and last would be the same
			if(atindex == 0)
				myprev = (($matchtr.get(0)).id).replace(/tr/, '');
			else
				myprev = (($matchtr.get(atindex-1)).id).replace(/tr/, '');
			//if we click the last element then last and next would be the same
			if(atindex == (total-1))
				mynext = (($matchtr.get(total-1)).id).replace(/tr/, '');
			else
				mynext = (($matchtr.get(atindex+1)).id).replace(/tr/, '');
			
			mylast = (($matchtr.get(total-1)).id).replace(/tr/, '');
			//alert('index-->'+atindex+'myfirst-->'+myfirst+'myprev-->'+myprev+'mynext-->'+mynext+'mylast-->'+mylast);
		}
		var curhref = $(location).attr('href');
		$('#my_navigation').show();
		//if first and prev are same then disable the prev button
		$('#myfirst').bind('click',function(){$(location).attr('href', curhref.replace("id="+curid,"id="+myfirst));});	
		if(myfirst == myprev && (atindex == 0))
			$('#myprev').addClass('disabled').prop('disabled',true);
		else			
			$('#myprev').bind('click',function(){$(location).attr('href', curhref.replace("id="+curid,"id="+myprev));});
				
		//if last and next are same then disable the next button
		$('#mylast').bind('click',function(){$(location).attr('href', curhref.replace("id="+curid,"id="+mylast));});	
		if(mylast == mynext && (atindex == (total-1))) 
			$('#mynext').addClass('disabled').prop('disabled',true);
		else			
			$('#mynext').bind('click',function(){$(location).attr('href', curhref.replace("id="+curid,"id="+mynext));});
		//Changing the value of myinfotext button
		$('#myinfotext').val('showing '+(atindex+1)+' of '+total);
	}
	//This function will upgrade the parent tr with the new value, so user can see the effect immediately of his/her updates
	function my_parent_update(eid, uoptions, finalfunc)
	{
		var $matchtr = window.parent.$('#tr'+eid) || $('#tr'+eid);
		var currentRow = $matchtr.get(0);
		var curcel = ''; // the current cell in action
		$.each(uoptions, function(index, value) {
			curcel = currentRow.cells[index];
			if(curcel)
			{
				//if this value is changed then only change the cell background color
				if(curcel.innerHTML != value)$(curcel).css('background-color','#f1ffb8')
				//change the current cell value to the supplied value
				 curcel.innerHTML = value; 
			}
		});
		//if we have any function to run after update, it can be set here
		if(jQuery.isFunction(finalfunc)) finalfunc(eid, currentRow);
	}
	//To make the get_navigation function active
	$(function(){
		get_navigation();
	});
    </script>
    <?php
	echo'<div id="my_navigation" style="display:none; margin-top:20px;">';
	echo '<input type="button" disabled="disabled" class="disabled" id="myinfotext" name="myinfotext" value="" />';
	echo $first = '<input type="button" id="myfirst" name="first" value="< First" />';
	echo $prev = '<input type="button" id="myprev" name="prev" value="<< Previous" />';
	echo $next = '<input type="button" id="mynext" name="next" value="Next >>" />';
	echo $last = '<input type="button" id="mylast" name="last" value="Last >" />';	
	echo'</div>';
}

//This function will pass a php file
function my_file_parser($filepath, $data=array(), $send = FALSE)
{
	return file_get_contents($filepath);
	extract($data, EXTR_OVERWRITE);
	if(!is_file($filepath)) die("Invalid file path specified<br><strong>$filepath</strong>");
	ob_start();
	include($filepath);
	$html = ob_get_contents();
	ob_end_clean(); # end buffer
	ob_start();
	if($send) return $html;
	echo $html;
}

// This function will help in setting the default value of inbox element
function do_pagination($totlink, $filterstr, $pagename='', $curpage=0, $display=5)
{
	$filterstr = trim($filterstr);
	$filterstr = base64_encode(ltrim($filterstr,'WHERE '));
	$records = $totlink;
	$pages = $records > $display ?	$pages = ceil($records/$display) : $pages = 1;
	$start = $curpage;
	if($pages > 1)
	{
		echo'<br /><div class="myplinks">'; //add some spacing
		$current_page = floor(($start/$display)) + 1; //determine which page the script is on
		// If it's not the first page, make a previous button
		echo'<span class="totlabel">Showing page '.$current_page.' of '.$pages.'</span>';
		if($current_page != 1)
		{
			//echo'<span class="totlabel">Showing page '.$current_page.' of '.$pages.'</span>';
			if($current_page >=6)
			echo'<a title="First page" href="'.$pagename.'&s=0&p='.$pages.'&pgc='.$filterstr.'">First</a>';
			echo'<a title="Previous page" href="'.$pagename.'&s='.($start - $display).'&p='.$pages.'&pgc='.$filterstr.'"><</a>';
		}
		//Make all numbered pages
		if($pages<=5)
		{
			$st = 1;
			$ll = $pages;
		}
		else if($pages>5 && $current_page<=4)
		{
			$st = 1;
			$ll = 5;
		}
		else 
		{
			$st = $current_page-2;
			$ll = $current_page + 3;
			if($ll>=$pages)
			$ll = $pages;
			$x = $ll-$st;
			if($x<6)
			$st = $current_page-4;
		}
		for($i = $st; $i <= $ll; $i++)
		{
			if($i != $current_page)
			{
				echo'<a href="'.$pagename.'&s='.($display*($i-1)).'&p='.$pages.'&pgc='.$filterstr.'">'.$i.'</a>';
			}
			else
			{
				echo '<span class="uhlmargin">'.$i.'</span>';
			}
		}// end of for loop
		// If it's not the last page, make a next button
		if($current_page != $pages)
		{
			echo'<a title="Next page" href="'.$pagename.'&s='.($start + $display).'&p='.$pages.'&pgc='.$filterstr.'">></a>';
			//if($current_page >=5 && (($pages-$current_page)>3))
			if($pages >=5 && (($pages-$current_page)>3))
			echo'<a title="Last page" href="'.$pagename.'&s='.($display*($pages-1)).'&p='.$pages.'&pgc='.$filterstr.'">Last</a>';
			//option to remove the pagination
			echo'<a title="List ALL" href="'.$pagename.'&s='.($display*($pages-1)).'&p='.$pages.'&pgsdisplay=true&pgc='.$filterstr.'">List ALL</a>';
		}
		echo'</div>';// close the paragraph
	}// end of links section
}

//This function will work for company account login
function worktoken()
{
	if($_SESSION[SESS.'worktoken'] === 786) return true; else return false;
}

function stopper($msg)
{
	echo'<div id="workarea">'.$msg.'</div>';
	require_once('include/footer.php');
	exit();
}

function add_refresher($link, $type='CSSPOP'){
	echo'<a style="float:left;" href="'.$link.'" class="iframef">'.ADD_REFRESHER.'</a>';
}

//Pagination functions
function get_pagination_details($result=NULL, $display=PGDISPLAY)
{	
	?>
	<style type="text/css">
	.pagination {
    background: #f2f2f2;
    padding: 5px;
	margin:0 auto;
	text-align:center;
	position:absolute;
	bottom: 20px;
	}
	.page {
		display: inline-block;
		padding: 5px 5px 5px 5px;
		margin-right: 4px;
		border-radius: 3px;
		border: solid 1px #c0c0c0;
		background: #f4f3f3;
		box-shadow: inset 0px 1px 0px rgba(4,97,60, .1), 0px 1px 3px rgba(0,0,0, .1);
		font-size: .875em;
		font-weight: bold;
		text-decoration: none;
		color: #990f05;
		text-shadow: 0px 1px 0px rgba(255,255,255, 1);
	}
	.page:hover, .page.gradient:hover {
		background: #fefefe;
		background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#FEFEFE), to(#f0f0f0));
		background: -moz-linear-gradient(0% 0% 270deg,#FEFEFE, #f0f0f0);
	}
	.page.active {
		border: none;
		background: #990f05;
		box-shadow: inset 0px 0px 8px rgba(0,0,0, .5), 0px 1px 0px rgba(255,255,255, .8);
		color: #f0f0f0;
		text-shadow: 0px 0px 3px rgba(0,0,0, .5);
	}
    </style>
	<?php
	$loader = '<div id="myloader" style="color:green; font-size:12px; font-weight:bold;"><img src="./icon-system/loader/ajax-loader.gif"> Loading ...</div>';
	
	$out = array('totpage'=>1, 'totrecords'=>0, 'temp_result'=>array(),'loader'=>$loader, 'pglinks'=>'');
	if(empty($result) || is_null($result)) return $out;
	//$display = 3;
	$totpage = ceil(count($result)/$display); 
	$totrecords = count($result);
	$i = $inc = 1;
	$temp_result = array();
	foreach($result as $key=>$value)
	{
	  $temp_result[$i][$key] = $value;	
	  if($inc == $display){$inc = 0; $i++;}
	  $inc++;
	}		
	$pglinks = '
	<table width="100%" border="0" cellspacing="5" cellpadding="5">
	  <tr>
		<td>
		  <div class="pagination pk">
			<a href="javascript:void(0);" class="page" title="First Page" onclick="show_pagination(\'first\');">First</a>
			<a href="javascript:void(0);" onclick="show_pagination(\'prev\');" class="page" title="Previous Page"><<</a>
			Showing page <input style="width:35px; background-color:#CCC;" maxlength="4" onkeypress="return isNumberKey(event);" type="text" name="cpage" id="cpage" value="1" onkeyup="show_pagination(\'current\');"/> of '.$totpage.'
			<a href="javascript:void(0);" onclick="show_pagination(\'next\');" class="page" title="Next Page">>></a>
			<a href="javascript:void(0);" class="page" title="Last Page" onclick="show_pagination(\'last\');">Last</a>
		  </div>
		</td>
	  </tr>
	</table>';
	if($totpage == 1) $pglinks = '';// if single page no need to show the pagination links
	$out = array('totpage'=>$totpage, 'totrecords'=>$totrecords, 'temp_result'=>$temp_result, 'loader'=>$loader, 'pglinks'=>$pglinks);
	return $out;
}

//This function will provide the necessary js for the pagination
function pagination_js($pgoutput)
{
	?>
    <script type="text/javascript">
	$(function(){
		$('div.mypages').hide();
		$('#mypages1').show();
		$('#myloader').hide();	
	})
	function show_pagination(wcase)
	{
		var maxpage = <?php echo $pgoutput['totpage']?>;	
		var num = $('#cpage').val()*1;
		if(!(num >=1  && num <= maxpage)) {alert('Please enter a proper page number'); $('#cpage').select(); return;}
		switch(wcase)
		{
			case'first':
			{
				num = 1;
				break;
			}
			case'last':
			{
				num = maxpage;
				break;
			}
			case'prev':
			{
				num = num-1;
				if(num == 0) num = 1;	
				break;
			}
			case'next':
			{
				num = num+1;
				if(num > maxpage) num = maxpage;
				break;
			}
		}
		$('#cpage').val(num);
		$('div.mypages').hide();
		$('#mypages'+num).show();
	}
	</script>
    <?php	
}

// This function will build the reference array for single column
function get_my_reference_array($tablename, $primarykey, $column, $orderby= '', $outtype = 'single')
{
	global $dbc;
	$out = array();
	list($opt, $rs) = run_query($dbc, "SELECT * FROM $tablename $orderby", 'multi');
	if(!$opt) return $out;
	while($row = mysqli_fetch_assoc($rs)){
		$id = $row[$primarykey];
		if($outtype == 'multi')
			$out[$id][$column] = $row[$column];
		else
			$out[$id] = $row[$column];
	}
	return $out;
}
// This function will build the reference array for multi column
function get_my_reference_array_direct($q, $primarykey)
{
	global $dbc;
	$out = array();
	list($opt, $rs) = run_query($dbc, $q, 'multi');
	if(!$opt) return $out;
	while($row = mysqli_fetch_assoc($rs)){
		$id = $row[$primarykey];
		$out[$id] = $row;
	}
	return $out;
}
//This function will give an alphabet corresponding to a month
function month_to_alphabet($month)
{
	if(empty($month)) return '';
	// if the month is not numberic, than it could be 3 digit mar or full name like march
	$month = ltrim($month, '0');
	if(!is_numeric($month))
	{	
		$month = strtoupper($month); // making the value in uppercase for comparison with the array
		$shortmonth = array('JAN'=>1, 'FEB'=>2, 'MAR'=>3, 'APR'=>4, 'MAY'=>5, 'JUN'=>6, 'JUL'=>7, 'AUG'=>8, 'SEP'=>9, 'OCT'=>10, 'NOV'=>11, 'DEC'=>12);
		$longmonth = array('JANUARY'=>1, 'FEBRUARY'=>2, 'MARCH'=>3, 'APRIL'=>4, 'MAY'=>5, 'JUNE'=>6, 'JUL'=>7, 'AUGUST'=>8, 'SEPTEMBER'=>9, 'OCTOBER'=>10, 'NOVEMBER'=>11, 'DECEMBER'=>12);
		if(strlen($month) == 3)
			$month = $shortmonth[$month];
		else
			$month = $longmonth[$month];
	}
	$alphabet = range('A','Z');
	//To return the date if we are using a search query in MYSQL
	return $alphabet[$month-1];
}

//This function will show the session period for the given session id
function send_session_period($sesId)
{
	global $dbc;
	$out = '';	
	$q = "SELECT period FROM session_year WHERE sesId = $sesId";
	list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
	if($opt) $out = $rs['period'];
	return $out;
}

//This function will help in the key preservation like to preserve some of the POST array key
function key_value_saver($heap=array(), $keytobesaved=array())
{
	$saved = array();
	foreach($keytobesaved as $key=>$value){
		if(isset($heap[$value]))
			$saved[$value] = $heap[$value];
	}
	return $saved;
}

//This function will bulid the pulldown from our query
function option_builder($dbc, $q, $selected ='')
{
	$out = '';
	list($opt, $rs) = run_query($dbc, $q, 'multi');
	if($opt){
		while($row = mysqli_fetch_row($rs)){
			$out .= '<option value="'.$row[0].'" '; 
			if($selected == $row[0]) $out .= 'selected="selected"'; 
			$out .='>'.$row[1].'</option>'."\n";
		}
	}
	return $out;
}

function show_row_change($url,$id)
{
	?>
    <script type="text/javascript">
	var url = '<?php echo $url;?>'+'&ajaxshow=<?php echo $id;?>';
	//if user has not maded any search, we need to show the initial table also, it can be achieved by change of url
	if(window.parent.$('td.myresultrow').length == 0) url = '<?php echo $url;?>'+'&ajaxshowblank=<?php echo $id;?>';
	//alert(url);
	$.get( url, function(data){
		 if(window.parent.$("tr#tr<?php echo $id;?>").length > 0){ // when we are updating a record
		 	window.parent.$("tr#tr<?php echo $id;?>").replaceWith( data );
		  	//alert('data replaced');
			do_final_touchup();
		 }else if(window.parent.$('td.myresultrow').length > 0){ // when we are inserting a new record
			// $('table.myfirst1 > tbody > tr').eq(0).after(data);
			  window.parent.$('td.myresultrow').eq(0).parent().before(data);
			 //alert('data inserted'+data);
			 do_final_touchup();
		 }else{// alert('table not available '+url);
		 	window.parent.$('tr#mysearchfilter').after(data); // when we do not have our search table available
			window.parent.$('#myloader').hide();
			window.parent.$('#mypages1').show();
			do_final_touchup();
		 }	 
	  });
	  	  
	  function do_final_touchup(){
		window.parent.$('#totCounter').html(window.parent.$('td.myresultrow').length);
		window.parent.$('td.myintrow').each(function(i){
			$(this).html((i+1)*1);
		});
		// Rechanging the background color of the table with id searchdata starts here
		var bg ='#efede8';
		window.parent.$('td.myresultrow').each(function(i){
			bg=(bg=='#efede8'?'#ffffff':'#efede8');
			$(this).parent().css('background-color',bg);
		});
	  }
	</script>
    <?php 
}
function show_row_changer_dynamic($url,$id)
{
	?>
    <script type="text/javascript">
	var url = '<?php echo $url;?>'+'&ajaxshow=<?php echo $id;?>';
	//if user has not maded any search, we need to show the initial table also, it can be achieved by change of url
        
	if(window.parent.$('td.myresultrow').length == 0) url = '<?php echo $url;?>'+'&ajaxshowblank=<?php echo $id; ?>';
	//alert(url);
	$.get( url, function(data){
		 if(window.parent.$("tr#tr<?php echo $id;?>").length > 0){ // when we are updating a record
		 	window.parent.$("tr#tr<?php echo $id;?>").replaceWith( data );
		  	//alert('data replaced');
			do_final_touchup();
		 }else if(window.parent.$('td.myresultrow').length > 0){ // when we are inserting a new record
			// $('table.myfirst1 > tbody > tr').eq(0).after(data);
			  window.parent.$('td.myresultrow').eq(0).parent().before(data);
			 //alert('data inserted'+data);
			 do_final_touchup();
		 }else{// alert('table not available '+url);
		 	window.parent.$('tr#mysearchfilter').after(data); // when we do not have our search table available
			window.parent.$('#myloader').hide();
			window.parent.$('#mypages1').show();
			do_final_touchup();
		 }	 
	  });
	  	  
	  function do_final_touchup(){
		window.parent.$('#totCounter').html(window.parent.$('td.myresultrow').length);
                alert(window.parent.$('td.myintrow').length);
		window.parent.$('td.myintrow').each(function(i){
			$(this).html((i+1)*1);
                       
		});
		// Rechanging the background color of the table with id searchdata starts here
		var bg ='#efede8';
		window.parent.$('td.myresultrow').each(function(i){
			bg=(bg=='#efede8'?'#ffffff':'#efede8');
			$(this).parent().css('background-color',bg);
		});
	  }
	</script>
    <?php 
}
function dynamic_js_enhancement()
{
	?>
    <script type="text/javascript">
    $(document).ready(function(){
		$( "table" ).on( "hover", 'a.iframef', function() {
			$(".iframef").colorbox({iframe:true, width:"95%", height:"100%"});
		});
	});
	</script>
    <?php	
}

function price_to_words($amount, $format=1)
{
	if($format == 1) return formatInIndianStyle($amount);
	$currency_object = new Currency();
	$inwords = $currency_object->get_bd_amount_in_text(round($amount));
	$inwords = str_replace('Taka & Zero Paisa Only', ' Only', $inwords);// removal of words after decimal points
	return $inwords;		
}

function address_presenter($input, $breakup=array(), $seperator=', ')
{
	if(empty($breakup)) $breakup = array('address','locality','city','pincode'=>'pincode', 'state'=>'state','country');
	$address = array();
        
	foreach($breakup as $key=>$value){
		//if($value == 'pincode' && in_array('state', $breakup)) continue;
		if(isset($input[$value]) && !empty($input[$value])) $address[$key] = $input[$value];			
	}
	//Comibining the pincode and state together and removing the pincode key	
	if(array_key_exists('state', $address) && array_key_exists('pincode', $address)) {
		$address['state'] = $address['state'].'-'.$address['pincode'];
		unset($address['pincode']);
	}
	return implode($seperator, $address);
}

function address_representer($input, $breakup=array(), $seperator=', ')
{
	if(empty($breakup)) $breakup = array('address','locality','city','pincode'=>'pincode', 'state'=>'state','country');
	$address = array();
       
	foreach($breakup as $key=>$value){
		//if($value == 'pincode' && in_array('state', $breakup)) continue;
		if(isset($input[$value]) && !empty($input[$value])) $address[$value] = $input[$value];			
	}
      
	//Comibining the pincode and state together	and removing the pincode key	
	if(array_key_exists('state', $address) && array_key_exists('pincode', $address)) {
		$address['state'] = $address['state'].'-'.$address['pincode'];
		unset($address['pincode']);
	}
	return $address;
}
function my_ajax_pagination($option=array())
{
	switch($option['showpart'])
	{
		case'js':
		{
			$pageurl = !isset($option['pageurl']) ? './js/ajax_general/loadData.php' : $option['pageurl'];
			$serverdata = !isset($option['serverdata']) ? '' : $option['serverdata'];
			$extraserverdata = !isset($option['extraserverdata']) ? '' : $option['extraserverdata'];
			$class_selector = !isset($option['class_selector']) ? '.myajaxinput' : $option['class_selector'];
			$container = !isset($option['container']) ? 'container' : $option['container'];
			?>
            <script type="text/javascript">
			  $(document).ready(function(){		             
				  function loadData(page){
					  //creating the data to be sent to the server automatically starts
					  var serverdata = $( "<?php echo $class_selector;?>" ).serialize();
					  /*$( "<?php echo $class_selector;?>" ).each(function() {
						serverdata += $( this ).attr( "name") + '=' + $( this ).val()+'&';
					  });*/
					  //alert(serverdata);
					  //creating the data to be sent to the server automatically end
					  $.ajax
					  ({
						  type: "POST",
						  url: "<?php echo $pageurl;?>",
						  data: "page="+page+"&"+serverdata+"<?php echo $extraserverdata;?>",
						  success: function(msg)
						  {
							  $("#<?php echo $container;?>").ajaxComplete(function(event, request, settings)
							  {						
								  $("#<?php echo $container;?>").html(msg);
							  });
						  }
					  });
				  }
				  loadData(1);  // For first time page load default results
				  $('#<?php echo $container;?> .pagination li.active').live('click',function(){
					  var page = $(this).attr('p');
					  loadData(page);					  
				  }); 
			  });
		    </script>			
            <?php
			break;	
		}
		case'divblock':
		{
	         $container = !isset($option['container']) ? 'container' : $option['container'];?>
                    <div class="textwidget" id="<?php echo $container;?>">
                      <div class="data"></div>
                      <div class="pagination"></div>
                      <!-- here we get all the data from ajax -->
                     </div>
                    <?php
                    break;	
		}		
	}	
}

function my2digit($num)
{
	return sprintf ("%.2f", $num);
}

/* This function will create post variable from input variable
** usage 
	$input = array(1=>array('name'=>'deepak', 'sex'=>'M'));
	$output = array('name'=>'out_post_indexname', 'sex'=>'out_post_indexname1',);
*/	
function create_multi_post($input, $output)
{
	foreach($input as $key=>$value){
		foreach($output as $key1=>$value1){
			$_POST[$value1][] = $value[$key1];
		}		
	}
}
function AddPlayTime ($oldPlayTime, $PlayTimeToAdd) {

    $pieces = split(':', $oldPlayTime);
    $hours=$pieces[0];
   
    $hours=str_replace("00","12",$hours);
   
    $minutes=$pieces[1];
    $seconds=$pieces[2];
    $oldPlayTime=$hours.":".$minutes.":".$seconds;

    $pieces = split(':', $PlayTimeToAdd);
    $hours=$pieces[0];
    $hours=str_replace("00","12",$hours);
    $minutes=$pieces[1];
    $seconds=$pieces[2];
   
    $str = $str.$minutes." minute ".$seconds." second" ;
    $str = "01/01/2000 ".$oldPlayTime." am + ".$hours." hour ".$minutes." minute ".$seconds." second" ;
   
    // Avant PHP 5.1.0, vous devez comparer avec  -1, au lieu de false
    if (($timestamp = strtotime($str)) === false) {
        return false;
    } else {
        $sum=date('h:i:s', $timestamp);
        $pieces = split(':', $sum);
        $hours=$pieces[0];
        $hours=str_replace("12","00",$hours);
        $minutes=$pieces[1];
        $seconds=$pieces[2];
        $sum=$hours.":".$minutes.":".$seconds;
       
        return $sum;
       
    }
    
}
function getTimeDiff($dtime,$atime)
{
    $nextDay=$dtime>$atime?1:0;
    $dep=explode(':',$dtime);
    $arr=explode(':',$atime);
    $diff=abs(mktime($dep[0],$dep[1],0,date('n'),date('j'),date('y'))-mktime($arr[0],$arr[1],0,date('n'),date('j')+$nextDay,date('y')));

    //Hour
    $hours=floor($diff/(60*60));

    //Minute 
    $mins=floor(($diff-($hours*60*60))/(60));

    //Second
    $secs=floor(($diff-(($hours*60*60)+($mins*60))));

    if(strlen($hours)<2)
    {
        $hours="0".$hours;
    }

    if(strlen($mins)<2)
    {
        $mins="0".$mins;
    }

    if(strlen($secs)<2)
    {
        $secs="0".$secs;
    }

    return $hours.':'.$mins.':'.$secs;

}
//This function Will return parent child name
function recursiveallparent($parent){
	global $dbc;
        $q = "SELECT t2.id,CONCAT_WS('=>',t1.name,t2.name) AS name FROM `_working_status` t1,`_working_status` t2 where t1.id = t2.parent_id AND  t2.id = $parent";
	$r = mysqli_query($dbc,$q);
        $str = '';
	if($r)
        {
             $row = mysqli_fetch_assoc($r);
             return $row['name'];
        }
        
}
//This function is used to convert integer to roman no
function integerToRoman($integer)
{
 // Convert the integer into an integer (just to make sure)
 $integer = intval($integer);
 $result = '';
 
 // Create a lookup array that contains all of the Roman numerals.
 $lookup = array('M' => 1000,'CM' => 900,'D' => 500,'CD' => 400,'C' => 100,'XC' => 90,'L' => 50,'XL' => 40,'X' => 10,'IX' => 9,'V' => 5,'IV' => 4,'I' => 1);
 
 foreach($lookup as $roman => $value){
  // Determine the number of matches
  $matches = intval($integer/$value);
  // Add the same number of characters to the string
  $result .= str_repeat($roman,$matches);
  // Set the integer to be the remainder of the integer and the value
  $integer = $integer % $value;
 }
 // The Roman numeral should be built, return it
 return $result;
}
function set_default_session_time()
{
   $timeout = TIMEOUT; // Number of seconds until it times out.
   $url = BASE_URL . 'index.php';
 // Check if the timeout field exists.
   if(isset($_SESSION['timeout'])) {
        // See if the number of seconds since the last visit is larger than the timeout period.
        $duration = time() - (int) $_SESSION['timeout'];
        if($duration > $timeout) {
            // Destroy the session and restart it.
            session_destroy();
            session_start();
            header ("Location: $url");
            exit();
        }
    }
    $_SESSION['timeout'] = time();
     
}

// code to get address according to latitude , longitude
function getLocationByLatLng($lat,$lng){
    if(($lat!='0') && ($lng!='0')){
//http://maps.googleapis.com/maps/api/geocode/json?latlng=28.4023003,77.3229817&sensor=true 28.531368627418,77.2377156544599
	$data = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&sensor=true');
	$data1=json_decode($data, true);
	$address=$data1['results'][0]['address_components'][1]['long_name'].", ";
	$address.=$data1['results'][0]['address_components'][2]['long_name'].", ";
	$address.=$data1['results'][0]['address_components'][4]['long_name'].", ";
	$address.=$data1['results'][0]['address_components'][6]['long_name'];
	return $address;
    }else{
        $address="";
        return $address;
    }
}

function getlatlongbymccmnclaccid($mcc,$mnc,$lac,$cid){
    
    if(($mcc!='0') && ($mnc!='0')){
$data = 
"\x00\x0e". 
"\x00\x00\x00\x00\x00\x00\x00\x00". 
"\x00\x00". 
"\x00\x00". 
"\x00\x00". 
"\x1b". 
"\x00\x00\x00\x00". 
"\x00\x00\x00\x00". 
"\x00\x00\x00\x03".
"\x00\x00".
"\x00\x00\x00\x00". 
"\x00\x00\x00\x00". 
"\x00\x00\x00\x00". 
"\x00\x00\x00\x00". 
"\xff\xff\xff\xff". 
"\x00\x00\x00\x00"  
;
  $mcc = substr("00000000".dechex($mcc),-8);
  $mnc = substr("00000000".dechex($mnc),-8);
  $lac = substr("00000000".dechex($lac),-8);
  $cid = substr("00000000".dechex($cid),-8);


$init_pos = strlen($data);
$data[$init_pos - 38]= pack("H*",substr($mnc,0,2));
$data[$init_pos - 37]= pack("H*",substr($mnc,2,2));
$data[$init_pos - 36]= pack("H*",substr($mnc,4,2));
$data[$init_pos - 35]= pack("H*",substr($mnc,6,2));
$data[$init_pos - 34]= pack("H*",substr($mcc,0,2));
$data[$init_pos - 33]= pack("H*",substr($mcc,2,2));
$data[$init_pos - 32]= pack("H*",substr($mcc,4,2));
$data[$init_pos - 31]= pack("H*",substr($mcc,6,2));
$data[$init_pos - 24]= pack("H*",substr($cid,0,2));
$data[$init_pos - 23]= pack("H*",substr($cid,2,2));
$data[$init_pos - 22]= pack("H*",substr($cid,4,2));
$data[$init_pos - 21]= pack("H*",substr($cid,6,2));
$data[$init_pos - 20]= pack("H*",substr($lac,0,2));
$data[$init_pos - 19]= pack("H*",substr($lac,2,2));
$data[$init_pos - 18]= pack("H*",substr($lac,4,2));
$data[$init_pos - 17]= pack("H*",substr($lac,6,2));
$data[$init_pos - 16]= pack("H*",substr($mnc,0,2));
$data[$init_pos - 15]= pack("H*",substr($mnc,2,2));
$data[$init_pos - 14]= pack("H*",substr($mnc,4,2));
$data[$init_pos - 13]= pack("H*",substr($mnc,6,2));
$data[$init_pos - 12]= pack("H*",substr($mcc,0,2));
$data[$init_pos - 11]= pack("H*",substr($mcc,2,2));
$data[$init_pos - 10]= pack("H*",substr($mcc,4,2));
$data[$init_pos - 9]= pack("H*",substr($mcc,6,2));

if ((hexdec($cid) > 0xffff) && ($mcc != "00000000") && ($mnc != "00000000")) {
  $data[$init_pos - 27] = chr(5);
} else {
  $data[$init_pos - 24]= chr(0);
  $data[$init_pos - 23]= chr(0);
}

$context = array (
        'http' => array (
            'method' => 'POST',
            'header'=> "Content-type: application/binary\r\n"
                . "Content-Length: " . strlen($data) . "\r\n",
            'content' => $data
            )
        );

$xcontext = stream_context_create($context);
$str=file_get_contents("http://www.google.com/glm/mmap",FALSE,$xcontext);
if (strlen($str) > 10) {
  $lat_tmp = unpack("l",$str[10].$str[9].$str[8].$str[7]);
  $lon_tmp = unpack("l",$str[14].$str[13].$str[12].$str[11]);
  $lon = $lon_tmp[1]/1000000;
  $lat = $lat_tmp[1]/1000000;
  $ll=$lat.",".$lon;
return $ll;

  }
   else{
	return $ll="0,0";   	
  	}
//http://yourwebhost.com/locate.php?mcc=XXX&mnc=XXX&lac=XXXXX&cid=XXXXXXXX

//http://manacledemo.in/test5.php?mcc=405&mnc=5&lac=5081&cid=10912
    }else{
        return $ll="0,0";   	
    }
}

function month_wise_pulldown($name,$inioption = true, $jsfunction='',$firstoptiontext = '== Please select ==', $valuetoset='')
{
    global $dbc;
    $shortmonth = array();
    $date = date('Y');
    $datenext = $date+1;
    $dateprev = $date-1;
    $str = '';
    if((date('n')>3) && (date('n')<=12)) {
       
         $shortmonth["04$date"] = "Apr-$date";
         $shortmonth["05$date"] = "May-$date";
         $shortmonth["06$date"] = "Jun-$date";
         $shortmonth["07$date"] = "Jul-$date";
         $shortmonth["08$date"] = "Aug-$date";
         $shortmonth["09$date"] = "Sep-$date";
         $shortmonth["10$date"] = "Oct-$date";
         $shortmonth["11$date"] = "Nov-$date";
         $shortmonth["12$date"] = "Dec-$date";
         $shortmonth["01$datenext"] = "Jan-$datenext";
         $shortmonth["02$datenext"] = "Feb-$datenext";
         $shortmonth["03$datenext"] = "Mar-$datenext";
    }
     if((date('n')<=3)){
         $shortmonth["04$dateprev"] = "Apr-$dateprev";
         $shortmonth["05$dateprev"] = "May-$dateprev";
         $shortmonth["06$dateprev"] = "Jun-$dateprev";
         $shortmonth["07$dateprev"] = "Jul-$dateprev";
         $shortmonth["08$dateprev"] = "Aug-$dateprev";
         $shortmonth["09$dateprev"] = "Sep-$dateprev";
         $shortmonth["10$dateprev"] = "Oct-$dateprev";
         $shortmonth["11$dateprev"] = "Nov-$dateprev";
         $shortmonth["12$dateprev"] = "Dec-$dateprev";
         $shortmonth["01$date"] = "Jan-$date";
         $shortmonth["02$date"] = "Feb-$date";
         $shortmonth["03$date"] = "Mar-$date";
     }
     $setvalue = $valuetoset;
 if(isset($_POST[$name]) && !is_array($_POST[$name]))
  $setvalue = $_POST[$name];
        
        echo'<select name="'.$name.'" '.$jsfunction.'>';
   if($inioption)
    echo'<option value="">'.$firstoptiontext.'</option>';
   foreach($shortmonth as $key => $value)
   {
    echo'<option value="'.$key.'"'; if($setvalue == $key) echo'selected="selected"'; echo'>'.$value.'</option>'."\n"; 
   }
   echo'</select>';
    
}


function month_wise_pulldown_new($name,$inioption = true, $jsfunction='',$firstoptiontext = '== Please select ==', $valuetoset='')
{
    global $dbc;
    $shortmonth = array();
    $date = date('Y');
    $datenext = $date+1;
    $dateprev = $date-1;
    $str = '';
    if((date('n')>3) && (date('n')<=12)) {
 
       
         $shortmonth["$date-04"] = "Apr-$date";
         $shortmonth["$date-05"] = "May-$date";
         $shortmonth["$date-06"] = "Jun-$date";
         $shortmonth["$date-07"] = "Jul-$date";
         $shortmonth["$date-08"] = "Aug-$date";
         $shortmonth["$date-09"] = "Sep-$date";
         $shortmonth["$date-10"] = "Oct-$date";
         $shortmonth["$date-11"] = "Nov-$date";
         $shortmonth["$date-12"] = "Dec-$date";
         $shortmonth["$datenext-01"] = "Jan-$datenext";
         $shortmonth["$datenext-02"] = "Feb-$datenext";
         $shortmonth["$datenext-03"] = "Mar-$datenext";
    }
     if((date('n')<=3)){
         $shortmonth["$dateprev-04"] = "Apr-$dateprev";
         $shortmonth["$dateprev-05"] = "May-$dateprev";
         $shortmonth["$dateprev-06"] = "Jun-$dateprev";
         $shortmonth["$dateprev-07"] = "Jul-$dateprev";
         $shortmonth["$dateprev-08"] = "Aug-$dateprev";
         $shortmonth["$dateprev-09"] = "Sep-$dateprev";
         $shortmonth["$dateprev-10"] = "Oct-$dateprev";
         $shortmonth["$dateprev-11"] = "Nov-$dateprev";
         $shortmonth["$dateprev-12"] = "Dec-$dateprev";
         $shortmonth["$date-01"] = "Jan-$date";
         $shortmonth["$date-02"] = "Feb-$date";
         $shortmonth["$date-03"] = "Mar-$date";
     }
     $setvalue = $valuetoset;
 if(isset($_POST[$name]) && !is_array($_POST[$name]))
  $setvalue = $_POST[$name];
        
        echo'<select name="'.$name.'" '.$jsfunction.'>';
   if($inioption)
    echo'<option value="">'.$firstoptiontext.'</option>';
   foreach($shortmonth as $key => $value)
   {
    echo'<option value="'.$key.'"'; if($setvalue == $key) echo'selected="selected"'; echo'>'.$value.'</option>'."\n";
   }
   echo'</select>';
    
}
// This function is used to done php and mysql pagination
function pagination($table, $per_page = 10, $page = 1, $url = '?', $page_filter,$icon) {
    global $dbc;
   // h1($table);
    $out = array('totrecords' => '', 'pagination_link' => '');
    $query = "SELECT COUNT($icon) as `num` FROM {$table} {$page_filter}";
  // h1($query);
    $row = mysqli_fetch_assoc(mysqli_query($dbc, $query));
    $total = $row['num'];

    $adjacents = "2";
    $page = ($page == 0 ? 1 : $page);  // if page shoul be zero then $page = 1;
    $start = ($page - 1) * $per_page;
    $prev = $page - 1;
    $next = $page + 1;
    $lastpage = ceil($total / $per_page);
    $lpm1 = $lastpage - 1;

    $pagination = "";
    if ($lastpage > 1) {
        $pagination .= "<table  width='100%' cellpadding='0' cellspacing='0'>"
                . "<tr><td><div style='width:100%;position:relative;'><ul class='pagination'>";
        $pagination .= "<li class='details'>Page $page of $lastpage</li>";
        if ($lastpage < 7 + ($adjacents * 2)) {
            for ($counter = 1; $counter <= $lastpage; $counter++) {
                if ($counter == $page)
                    $pagination.= "<li><a class='current'>$counter</a></li>";
                else
                    $pagination.= "<li><a href='{$url}page=$counter'>$counter</a></li>";
            }
        }
        elseif ($lastpage > 5 + ($adjacents * 2)) {
            if ($page < 1 + ($adjacents * 2)) {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                    if ($counter == $page)
                        $pagination.= "<li><a class='current'>$counter</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}page=$counter'>$counter</a></li>";
                }
                $pagination.= "<li class='dot'>...</li>";
                $pagination.= "<li><a href='{$url}page=$lpm1'>$lpm1</a></li>";
                $pagination.= "<li><a href='{$url}page=$lastpage'>$lastpage</a></li>";
            }
            elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                $pagination.= "<li><a href='{$url}page=1'>1</a></li>";
                $pagination.= "<li><a href='{$url}page=2'>2</a></li>";
                $pagination.= "<li class='dot'>...</li>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<li><a class='current'>$counter</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}page=$counter'>$counter</a></li>";
                }
                $pagination.= "<li class='dot'>..</li>";
                $pagination.= "<li><a href='{$url}page=$lpm1'>$lpm1</a></li>";
                $pagination.= "<li><a href='{$url}page=$lastpage'>$lastpage</a></li>";
            }
            else {
                $pagination.= "<li><a href='{$url}page=1'>1</a></li>";
                $pagination.= "<li><a href='{$url}page=2'>2</a></li>";
                $pagination.= "<li class='dot'>..</li>";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<li><a class='current'>$counter</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}page=$counter'>$counter</a></li>";
                }
            }
        }

        if ($page < $counter - 1) {
            $pagination.= "<li><a href='{$url}page=$next'>Next</a></li>";
            $pagination.= "<li><a href='{$url}page=$lastpage'>Last</a></li>";
        } else {
            $pagination.= "<li><a class='current'>Next</a></li>";
            $pagination.= "<li><a class='current'>Last</a></li>";
        }
        $pagination.= "</ul></div></td></tr></table>\n";
    }

    return array('totrecords' => $total, 'pagination_link' => $pagination);
    //return $pagination;
} 
function getDistanceBetweentwopoint($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Mi') {
    
     $theta = $longitude1 - $longitude2;
     $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
     $distance = acos($distance);
     $distance = rad2deg($distance);
     $distance = $distance * 60 * 1.1515; switch($unit) {
          case 'Mi': break; case 'Km' : $distance = $distance * 1.609344;
     }
     return (round($distance,2));
}
function distance($lat1, $lon1, $lat2, $lon2, $unit) {
	 
	  $theta = $lon1 - $lon2;
	  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	  $dist = acos($dist);
	  $dist = rad2deg($dist);
	  $miles = $dist * 60 * 1.1515;
	  $unit = strtoupper($unit);
	 
	  if ($unit == "K") {
	    return ($miles * 1.609344);
	  } else if ($unit == "N") {
	      return ($miles * 0.8684);
	    } else {
	        return $miles;
	      }
	}
        
        function get_juniour_id($person_id)
{
        global $dbc;
        $sesId = $_SESSION[SESS.'data']['id'];
        $qq = "SELECT id FROM person WHERE person_id_senior IN ($person_id)";
        $rr = mysqli_query($dbc,$qq);
        if(mysqli_num_rows($rr) > 0  )
         { 
            $num = mysqli_num_rows($rr);
            $inc = 1;
            $final_data = array();
           
            while($d = mysqli_fetch_assoc($rr)) {
                static $pid = '';
                $final_data[$d['id']] = $d['id'];
                if($inc == $num) { 
                    $id_str = implode(',', $final_data); 
                    $pid .= ','.$id_str;
                    get_juniour_id($id_str); 
                }
                $inc++; 
            } 
        }
        
    return $pid = trim($pid, ',');
            
}
/// RECURSIVE WITH NAME FOR JUNIOR ANK

function recursivejuniorsName($code){
    global $dbc;
    $qry="";
	$res1="";
	$res2="";
	 $qry=mysqli_query($dbc,"select person.id,CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as name from person INNER JOIN person_login pl ON pl.person_id=person.id where person_id_senior=trim('".$code."')");
	 
	 $num=mysqli_num_rows($qry);

		while($res2=mysqli_fetch_assoc($qry)){
			if($res2['id']!="" && $res2['id']!=0){
				$result=['id'=>$res2['id'],'name'=>$res2['name']];
				$_SESSION['resursivedata'][]=$result;
				recursivejuniorsName($res2['id']);
			}
		}
	
}


///  RECURSIVE FUNCTION WITH SENIOR ANKUSH
function recursiveseniorName($code){
    global $dbc;
    $qry="";
	$res1="";
	$res2="";
	if($code != 0 && $code !=1)
	{
		$qry=mysqli_query($dbc,"select person.id,person_id_senior,CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name) as name from person INNER JOIN person_login pl ON pl.person_id=person.id where person.id=trim('".$code."')");
	 
		$num=mysqli_num_rows($qry);
   
		   while($res2=mysqli_fetch_assoc($qry)){
			   if($res2['id']!="" && $res2['id']!=0){
				   $result=['id'=>$res2['id'],'name'=>$res2['name']];
				   $_SESSION['resursiveseniordata'][]=$result;
				   recursiveseniorName($res2['person_id_senior']);
			   }
		   }
	}
	
	
}


/// END OF RECURSIVE FUNCTION
function recursivejuniors($code){
    global $dbc;
    $qry="";
	$res1="";
	$res2="";
	 $qry=mysqli_query($dbc,"select person.id from person INNER JOIN person_login pl ON pl.person_id=person.id where person_id_senior=trim('".$code."')");
	 /*$qry1="select person.id from person INNER JOIN person_login pl ON pl.person_id=person.id where person_id_senior=trim('".$code."')";
	 h1($qry1);
	 die;*/
        $num=mysqli_num_rows($qry);
          
	if($num<1){
		$res1=mysqli_fetch_assoc($qry);
		if($res1['id']!="" && $res1['id']!=0){
		 	$_SESSION['resursivedata'][]=$res1['id'];
		}
	}
	else
	{
		while($res2=mysqli_fetch_assoc($qry)){
			if($res2['id']!="" && $res2['id']!=0){
				$_SESSION['resursivedata'][]=$res2['id'];
				recursivejuniors($res2['id']);
			}
		}
	}
}

function recursivejuniors_new($code){
    global $dbc;
    $qry="";
	$res1="";
	$res2="";
	 $qry=mysqli_query($dbc,"select person.id from person INNER JOIN person_login pl ON pl.person_id=person.id where person_id_senior=trim('".$code."')");
/*	 $qry1="select person.id from person INNER JOIN person_login pl ON pl.person_id=person.id where person_id_senior=trim('".$code."')";
	 h1($qry1);
	 die;*/
        $num=mysqli_num_rows($qry);
          
	if($num<1){
		$res1=mysqli_fetch_assoc($qry);
		if($res1['id']!="" && $res1['id']!=0){
		 	$_SESSION['resursivedata'][]=$res1['id'];
		}
	}
	else
	{
		while($res2=mysqli_fetch_assoc($qry)){
			if($res2['id']!="" && $res2['id']!=0){
				$_SESSION['resursivedata'][]=$res2['id'];
				//recursivejuniors($res2['id']);
			}
		}
	}
}
?>