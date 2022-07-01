<?php
function db_pulldownsmall($dbc, $name, $q, $arrkey = false, $inioption = true, $jsfunction='',$firstoptiontext = '== Please select ==', $valuetoset='')
{
	$r = mysqli_query($dbc, $q);
	
	$setvalue = $valuetoset;
	if(isset($_POST[$name]) && !is_array($_POST[$name]))
		$setvalue = $_POST[$name];
		
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<select name="'.$name.'" '.$jsfunction.' style="width: 120px">';
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
				echo'<option value="'.$key.'"'; if(trim($setvalue) === trim($key)) echo'selected="selected"'; echo'>'.$value.'</option>'."\n";	
			}
			echo'</select>';			
		}
		else
		{
			echo'<select name="'.$name.'" '.$jsfunction.' style="width: 150px">';
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
function db_pulldownstart($dbc, $name, $q, $arrkey = false, $inioption = true, $jsfunction='',$firstoptiontext = '== Please select ==', $valuetoset='')
{
	$r = mysqli_query($dbc, $q);
	
	$setvalue = $valuetoset;
	if(isset($_POST[$name]) && !is_array($_POST[$name]))
		$setvalue = $_POST[$name];
		
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
			//if($inioption)
			//	echo'<option value="">'.$firstoptiontext.'</option>';
			foreach($optarray as $key => $value)
			{
				echo'<option value="'.$key.'"'; if(trim($setvalue) === trim($key)) echo'selected="selected"'; echo'>'.$value.'</option>'."\n";	
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
function db_pulldown170($dbc, $name, $q, $arrkey = false, $inioption = true, $jsfunction='',$firstoptiontext = '== Please select ==', $valuetoset='')
{
	$r = mysqli_query($dbc, $q);
	
	$setvalue = $valuetoset;
	if(isset($_POST[$name]) && !is_array($_POST[$name]))
		$setvalue = $_POST[$name];
		
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<select name="'.$name.'" '.$jsfunction.' style="width: 180px">';
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
				echo'<option value="'.$key.'"'; if(trim($setvalue) === trim($key)) echo'selected="selected"'; echo'>'.$value.'</option>'."\n";	
			}
			echo'</select>';			
		}
		else
		{
			echo'<select name="'.$name.'" '.$jsfunction.' style="width: 180px">';
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

function db_pulldown($dbc, $name, $q, $arrkey = false, $inioption = true, $jsfunction='',$firstoptiontext = '== Please select ==', $valuetoset='')
{
	$r = mysqli_query($dbc, $q);
	
	$setvalue = $valuetoset;
	if(isset($_POST[$name]) && !is_array($_POST[$name]))
		$setvalue = $_POST[$name];
		
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
				echo'<option value="'.$key.'"'; if(trim($setvalue) === trim($key)) echo'selected="selected"'; echo'>'.$value.'</option>'."\n";	
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
function db_pulldownall($dbc, $name, $q, $arrkey = false, $inioption = true, $jsfunction='',$firstoptiontext = '== Please select ==', $valuetoset='')
{
	$r = mysqli_query($dbc, $q);

	$setvalue = $valuetoset;
	if(isset($_POST[$name]) && !is_array($_POST[$name]))
		$setvalue = $_POST[$name];
		
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
				echo'<option value="'.$firstoptiontext.'">'.$firstoptiontext.'</option>';
			foreach($optarray as $key => $value)
			{
                            echo'<option value="'.$key.'"'; if($setvalue == $key) echo'selected="selected"'; echo'>'.$value.'</option>'."\n";	
			}
			echo'</select>';			
		}
		else
		{
                        
			echo'<select name="'.$name.'" '.$jsfunction.'>';
			echo'<option value="All">All</option>';
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
function arr_pulldown($name, $arritem = array(), $msg='', $usearrkey=false, $ini_option=true, $jsfunction='', $name_array = false, $firstoptiontext = 'Please select...', $valuetoset='')
{
	if(is_array($arritem))
	{
		$setvalue = $valuetoset;
	if(isset($_POST[$name]) && !is_array($_POST[$name]))
		$setvalue = $_POST[$name];
		
		if($name_array) $mkarray = '[]'; else $mkarray = '';// whether to make select name as as an array used with add more system
		echo'
		<select name="'.$name.$mkarray.'" ';  if(!empty($jsfunction))echo $jsfunction; echo'>';
		if($ini_option) echo'<option value="">'.$firstoptiontext.'</option>';
		foreach($arritem as $key => $value)
		{
			if(!$usearrkey) // whether to use array keys or not
				$key = $value;
			echo'<option value="'.$key.'"'; if($setvalue == $key) echo 'selected="selected"'; echo'>'.$value.'</option>'."\n";
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
                        //h1($qc);
		
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

//This function will give the edit value for class
function geteditvalue_class($eid=0, $in = NULL, $labelchange=array(), $options=NULL)
{
   // pre($in);
	if(is_array($in))
	{
		foreach($in[$eid] as $key=>$value)
		{
			if(isset($labelchange[$key])) 
                           $_POST[$labelchange[$key]] = $value;
			else
                        $_POST[$key] = $value;
		}
		//To change the name of some of the variable if needed
		/*foreach($labelchange as $key1=>$value1)
		{
			if(isset($_POST[$key1]))
			{
				$_POST[$value1] = $_POST[$key1];
				unset($_POST[$key1]);
			}
		}*/
	}
}


function check_post_data($d1,$labelchange=array() ,$data=array(), $options=NULL)
{

    if(is_array($d1))
	{
		foreach($labelchange as $key=>$value)
		{
			if(isset($d1[$key]))
				$data[$key] = $d1[$key];
			else
				$data[$key] = "";
		}
        return $data;        
	}
        
}
//This function will find the value of a col based on the primary key
function myrowval($table, $col, $where)
{
	global $dbc;
	list($opt, $rs) = run_query($dbc, "SELECT $col FROM $table WHERE $where LIMIT 1");
	return ($opt) ? $rs[$col] : '';
}
function myrowvaljoin($table1, $col,$table2,$on_cond, $where)
{
global $dbc;
list($opt, $rs) = run_query($dbc, "SELECT $col FROM $table1 INNER JOIN $table2 ON $on_cond WHERE $where LIMIT 1");
return ($opt) ? $rs[$col] : '';
}

function myrowvaladvance($table, $col, $alias, $where)
{
	global $dbc;
	list($opt, $rs) = run_query($dbc, "SELECT $col AS $alias FROM $table WHERE $where LIMIT 1");
	return ($opt) ? $rs[$alias] : '';
}

function for_recursive($id,$catalog_level,$incre=1){
    $q="";
    for($i=$incre;$i<=$catalog_level;$i++){
        $j=$i+1;
        $q.="select id from catalog_".$j." where catalog_".$i."_id=".$id."<br/>";
        $qry=mysqli_query($db,"select id from catalog_".$j." where catalog_".$i."_id=".$id);
        $num= 2;// mysql_num_rows($qry);
        while($res=  mysqli_fetch_assoc($qry)){
            $id=$res['id'];
            for_recursive($id,$num,$i);
        }
    }
    return $q;
}

function while_recursive($id,$catalog_level){
    for($i=1;$i<=$catalog_level;$i++){
        $j=$i+1;
        $q="select id from catalog_".$j." where catalog_".$i."_id=".$id."<br/>";
        $qry=mysqli_query($dbc,"select id from catalog_".$j." where catalog_".$i."_id=".$id);
        while($res=  mysqli_fetch_assoc($qry)){
            $q="select id from catalog_".$j." where catalog_".$i."_id=".$res['id']."<br/>";
        }
    }
}

function write_query($q){
    $file_name = 'sync_file/'.$_SESSION[SESS.'data']['dealer_id'].'_'.date('Ymd').$_SESSION[SESS.'file_id'].'.txt';
    $oldmask = umask(0);
     $file =  fopen($file_name,'a+');
      umask($oldmask);
     $content = $q.'##|##';
     fwrite($file,$content);
     fclose($file);
}


function new_rate_calc($igst,$product_id,$mrp)
{
	if($igst>0)
	{
	  $with_gst = array(3,4,5,6,8,12,13,14,15,16);

	  if(in_array($product_id,$with_gst))
	  {
	    $act_rate = ($mrp*82/105);
	    $rates = number_format($act_rate,2);
	  }else{
	    $act_rate = ($mrp*75/105);
	    $rates = number_format($act_rate,2);
	  }
	}else{
	  $act_rate = ($mrp*82/100);
	  $rates = number_format($act_rate,2);
	}


	return array('act_rate'=>$act_rate,'rate'=>$rates);
}

function new_rate_calc2($igst,$product_id,$mrp,$o_date)
{
	// h1($o_date);
	if($igst>0)
	{
	  $with_gst = array(3,4,5,6,8,12,13,14,15,16);

	  if(in_array($product_id,$with_gst))
	  {
	    $act_rate = ($mrp*82/105);
	    $rates = number_format($act_rate,2);
	  }else{
	    $act_rate = ($mrp*75/105);
	    $rates = number_format($act_rate,2);
	  }
	}else{
	  $act_rate = ($mrp*82/100);
	  $rates = number_format($act_rate,2);
	}

	if($o_date=='09/12/2017' && $product_id==122)
	{
		$rates = $rates-2;
	}
	// if($o_date=='15/12/2017' && $product_id==5)
	// {
	// 	$rates = $rates-2;
	// }
	// if($o_date=='15/12/2017' && $product_id==169)
	// {
	// 	$rates = $rates-1;
	// }
	// if($o_date=='15/12/2017' && $product_id==78)
	// {
	// 	$rates = $rates-1;
	// }


	return array('act_rate'=>$act_rate,'rate'=>$rates);
}

function new_dealer_rate($r_rate,$division)
{
	$state_id =  $_SESSION[SESS.'data']['state_id'];
	$stateids = array(120150112064905,120160523054911,120161129120907,120170403073142,120170403073414,120170403074238);

	/* If states IN ( 'Rajasthan', 'Himanchal Pradesh', 'Bihar', 'Chhattisgarh', 'Madhya Pradesh', 'Chandigarh' ) */
	if(in_array($state_id,$stateids))
	{
	  $d_rate = $r_rate-($r_rate*9/100);
	}else{

	    switch($division)
	    {
	      case 1:
	      $d_rate = $r_rate-($r_rate*9/100);
	      break;

	      case 2:
	      $d_rate = $r_rate-($r_rate*7/100);
	      break;

	      default:
	      break;
	    }
	}

	return $d_rate;
}

function get_product_multiple_mrp($dbc,$pid,$did,$type=false)
{
	// echo "SELECT mrp FROM stock WHERE product_id=$pid AND dealer_id = $did";
	$mrps = mysqli_query($dbc,"SELECT mrp FROM stock WHERE product_id=$pid AND dealer_id = $did ORDER BY product_id,mrp");
	$mrp_cnt = mysqli_num_rows($mrps);
	$out = '';

	if($mrp_cnt>1 && !$type)
	{
		$out .= '<option value="">== Please Select ==</option>';
	}

	while($mrp_row = mysqli_fetch_assoc($mrps))
	{ 
		$selected = ($invalue['mrp']==$mrp_row['mrp']) ? "selected":"";

		if($type)
		{
			$out[] = $mrp_row['mrp'];
		}else{
			$out .= "<option value=".$mrp_row['mrp']." $selected>".$mrp_row['mrp']."</option>";
		}
	}

	return $out;
}

?>
