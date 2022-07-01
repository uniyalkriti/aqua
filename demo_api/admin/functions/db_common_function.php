<?php
function db_pulldown($dbc, $name, $q, $arrkey = false, $inioption = true, $jsfunction='',$firstoptiontext = '== Please select ==', $valuetoset='',$all=false)
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
				echo'<option value="" style="background-color:#F5D0A9">'.$firstoptiontext.'</option>';

			if($all)
			{
				$selected = ($setvalue==0)?'selected="selected"':'';
				echo'<option value="0" style="background-color:#F5D0A9" '.$selected.'>ALL</option>';
			}
			
			foreach($optarray as $key => $value)
			{
				echo'<option style="background-color:#F5D0A9" value="'.$key.'"'; if($setvalue == $key) echo'selected="selected"'; echo'>'.$value.'</option>'."\n";	
			}
			echo'</select>';			
		}
		else
		{
			echo'<select name="'.$name.'" '.$jsfunction.'>';
			echo'<option value="" style="background-color:#F5D0A9">N.A.</option>';
			echo'</select>';			
		}
	}
	else
	{
		echo'<span class="error">Sorry, database query failed</span>';
		//exit();
	}
}

function db_pulldown_multi($dbc, $name, $q, $arrkey = false, $inioption = true, $jsfunction='',$firstoptiontext = '== Please select ==', $valuetoset='',$multi="")
{
	$r = mysqli_query($dbc, $q);
	//pre($_POST[$name]);die;
	$setvalue = $valuetoset;
	if(isset($_POST[$name]) && is_array($_POST[$name]))
		$setvalue = $_POST[$name];
		
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<select size="4" name="'.$name.'[]" '.$jsfunction.' '.$multi.' style="height:6em !important;">';
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
				echo'<option value="'.$key.'"'; if(in_array($key,$setvalue)) echo'selected="selected"'; echo'>'.$value.'</option>'."\n";	
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
function db_checkbox_list($dbc, $name='', $q){

	$r = mysqli_query($dbc, $q);
	//pre($_POST[$name]);die;
	$setvalue = $valuetoset;
	if(isset($_POST[$name]) && is_array($_POST[$name]))
		$setvalue = $_POST[$name];
		
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<div name="check_div" style="max-width: 500px; height: 100px; background-color: #F5D0A9; overflow: scroll;">';
			$optarray = array();
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[0]] = $row[1];	
				}
			
			//if($inioption)
				//echo'<option value="">'.$firstoptiontext.'</option>';
			foreach($optarray as $key => $value)
			{
				echo'<input type="checkbox" name="'.$name.'[]" value="'.$key.'"'; if(in_array($key,$setvalue)) echo'checked'; echo' > '.$value.' '."<br>";	
			}
			echo'</div>';			
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
function db_checkbox_all_select($dbc, $name='', $q){

	$r = mysqli_query($dbc, $q);
	//pre($_POST[$name]);die;
	$setvalue = $valuetoset;
	if(isset($_POST[$name]) && is_array($_POST[$name]))
		$setvalue = $_POST[$name];
		
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<div name="check_div" class="check_div" style="max-width: 500px; height: 100px; background-color: #F5D0A9; overflow-y: scroll;">';
			$optarray = array();
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[0]] = $row[1];	
				}
			
			//if($inioption)
				//echo'<option value="">'.$firstoptiontext.'</option>';                                
                       
            echo'<input type="checkbox" class="checkBoxClass" id="ckbCheckAll" onclick="select_all_check()">'. All.' '."<br>";	
			foreach($optarray as $key => $value)
			{
				echo'<input type="checkbox" class="checkBoxClass chstid" name="'.$name.'[]" value="'.$key.'"'; if(in_array($key,$setvalue)) echo'checked'; echo' > '.$value.' '."<br>";	
			}
			echo'</div>';			
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
function db_checkbox_all_select_role($dbc, $name='', $q){

	$r = mysqli_query($dbc, $q);
	//pre($_POST[$name]);die;
	$setvalue = $valuetoset;
	if(isset($_POST[$name]) && is_array($_POST[$name]))
		$setvalue = $_POST[$name];
		
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<div name="check_div_role" style="max-width: 500px; height: 100px; background-color: #F5D0A9; overflow: scroll;">';
			$optarray = array();
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[0]] = $row[1];	
				}
			
			//if($inioption)
				//echo'<option value="">'.$firstoptiontext.'</option>';                                
                       
                        echo'<input type="checkbox" class="checkBoxClassRole" id="ckbCheckAllRole" onclick="select_all_check_role()">'. All.' '."<br>";	
			foreach($optarray as $key => $value)
			{
				echo'<input type="checkbox" class="checkBoxClassRole" name="'.$name.'[]" value="'.$key.'"'; if(in_array($key,$setvalue)) echo'checked'; echo' > '.$value.' '."<br>";	
			}
			echo'</div>';			
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


function db_checkbox_all_select_user($dbc, $name, $q){
    //echo $name;exit;
	$r = mysqli_query($dbc, $q);
        $all='ALL';
       // print_r($_POST[$name]);
	//pre($_POST[$name]);die;
//	if(isset($name) && is_array($name))
//		$setvalue = $name;
//		echo $setvalue;exit;
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<div name="check_div_user" style="max-width: 500px; height: 100px; background-color: #F5D0A9; overflow: scroll;">';
			$optarray = array();
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[0]] = $row[1];	
				}
			
			//if($inioption)
				//echo'<option value="">'.$firstoptiontext.'</option>';                                
                       
                        echo'<input type="checkbox" class="checkBoxClassUser" id="ckbCheckAllUser" onclick="select_all_check_user()">'.$all.' '."<br>";	
			foreach($optarray as $key => $value)
			{
				echo'<input type="checkbox" class="checkBoxClassUser" name="'.$name.'[]" value="'.$key.'"';  echo' > '.$value.' '."<br>";	
			}
			echo'</div>';			
		}
		else
		{
			echo'<select name="'.$name.'" '.$jsfunction.'>';
			echo'<option value="">N.A.</option>';
			echo'</select>';			
		}
	}
//	else
//	{
//		echo'<span class="error">Sorry, database query failed</span>';
//		//exit();
//	}


}
function db_checkbox_all_select_user_set($dbc, $name='', $q){

	$r = mysqli_query($dbc, $q);
	//pre($_POST[$name]);die;
	$setvalue = $valuetoset;
	if(isset($_POST[$name]) && is_array($_POST[$name]))
		$setvalue = $_POST[$name];
		
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<div id="lol" name="check_div_user" style="max-width: 500px; height: 100px; background-color: #F5D0A9; overflow: hidden;">';
			$optarray = array();
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[0]] = $row[1];	
				}
			
			//if($inioption)
				//echo'<option value="">'.$firstoptiontext.'</option>';                                
                       
                        echo'<input type="checkbox" class="checkBoxClassUser" id="ckbCheckAllUser" onclick="select_all_check_user()">'. All.' '."<br>";	
			foreach($optarray as $key => $value)
			{
				echo'<input type="checkbox" class="checkBoxClassUser" name="'.$name.'[]" value="'.$key.'"'; if(in_array($key,$setvalue)) echo'checked'; echo' > '.$value.' '."<br>";	
			}
			echo'</div>';			
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

function db_checkbox_all_select_city($dbc, $name, $q){
   // echo $q;
    //exit;
	$r = mysqli_query($dbc, $q);
        $all='ALL';
       // print_r($_POST[$name]);
	//pre($_POST[$name]);die;
//	if(isset($name) && is_array($name))
//		$setvalue = $name;
//		echo $setvalue;exit;
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<div name="check_div_city" style="max-width: 500px; height: 100px; background-color: #F5D0A9; overflow: scroll;">';
			$optarray = array();
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[0]] = $row[1];	
				}
			
			//if($inioption)
				//echo'<option value="">'.$firstoptiontext.'</option>';                                
                       
                        echo'<input type="checkbox" class="checkBoxClassCity" id="ckbCheckAllCity" onclick="select_all_check_city()">'.$all.' '."<br>";	
			foreach($optarray as $key => $value)
			{
				echo'<input type="checkbox" class="checkBoxClassCity" name="'.$name.'[]" value="'.$key.'"';  echo' > '.$value.' '."<br>";	
			}
			echo'</div>';			
		}
		else
		{
			echo'<select name="'.$name.'" '.$jsfunction.'>';
			echo'<option value="">N.A.</option>';
			echo'</select>';			
		}
	}
//	else
//	{
//		echo'<span class="error">Sorry, database query failed</span>';
//		//exit();
//	}


}
function db_checkbox_all_select_city_set($dbc, $name='', $q){
        //echo $q;
	$r = mysqli_query($dbc, $q);
	//pre($_POST[$name]);die;
	$setvalue = $valuetoset;
	if(isset($_POST[$name]) && is_array($_POST[$name]))
		$setvalue = $_POST[$name];
		
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<div id="lol" name="check_div_city" style="max-width: 500px; height: 100px; background-color: #F5D0A9; overflow: hidden;">';
			$optarray = array();
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[0]] = $row[1];	
				}
			
			//if($inioption)
				//echo'<option value="">'.$firstoptiontext.'</option>';                                
                       
                        echo'<input type="checkbox" class="checkBoxClassCity" id="ckbCheckAllCity" onclick="select_all_check_city()">'. All.' '."<br>";	
			foreach($optarray as $key => $value)
			{
				echo'<input type="checkbox" class="checkBoxClassCity" name="'.$name.'[]" value="'.$key.'"'; if(in_array($key,$setvalue)) echo'checked'; echo' > '.$value.' '."<br>";	
			}
			echo'</div>';			
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
function db_checkbox_all_select_dealer($dbc, $name, $q){
    //echo $name;exit;
	$r = mysqli_query($dbc, $q);
        $all='ALL';
       // print_r($_POST[$name]);
	//pre($_POST[$name]);die;
//	if(isset($name) && is_array($name))
//		$setvalue = $name;
//		echo $setvalue;exit;
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<div name="check_div_dealer" style="max-width: 500px; height: 100px; background-color: #F5D0A9; overflow: scroll;">';
			$optarray = array();
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[0]] = $row[1];	
				}
			
			//if($inioption)
				//echo'<option value="">'.$firstoptiontext.'</option>';                                
                       
                        echo'<input type="checkbox" class="checkBoxClassDealer" id="ckbCheckAllDealer" onclick="select_all_check_dealer()">'.$all.' '."<br>";	
			foreach($optarray as $key => $value)
			{
				echo'<input type="checkbox" class="checkBoxClassDealer" name="'.$name.'[]" value="'.$key.'"';  echo' > '.$value.' '."<br>";	
			}
			echo'</div>';			
		}
		else
		{
			echo'<select name="'.$name.'" '.$jsfunction.'>';
			echo'<option value="">N.A.</option>';
			echo'</select>';			
		}
	}
//	else
//	{
//		echo'<span class="error">Sorry, database query failed</span>';
//		//exit();
//	}


}
function db_checkbox_all_select_dealer_set($dbc, $name='', $q){

	$r = mysqli_query($dbc, $q);
	//pre($_POST[$name]);die;
	$setvalue = $valuetoset;
	if(isset($_POST[$name]) && is_array($_POST[$name]))
		$setvalue = $_POST[$name];
		
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<div name="check_div_dealer" id="lol" style="max-width: 500px; height: 100px; background-color: #F5D0A9; overflow-y: scroll;">';
			$optarray = array();
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[0]] = $row[1];	
				}
			
			//if($inioption)
				//echo'<option value="">'.$firstoptiontext.'</option>';                                
                       
                        echo'<input type="checkbox" class="checkBoxClassDealer" id="ckbCheckAllDealer" onclick="select_all_check_dealer()">'. All.' '."<br>";	
			foreach($optarray as $key => $value)
			{
				echo'<input type="checkbox" class="checkBoxClassDealer" name="'.$name.'[]" value="'.$key.'"'; if(in_array($key,$setvalue)) echo'checked'; echo' > '.$value.' '."<br>";	
			}
			echo'</div>';			
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

function db_checkbox_all_select_csa($dbc, $name, $q){
    //echo $name;exit;
	$r = mysqli_query($dbc, $q);
        $all='ALL';
       // print_r($_POST[$name]);
	//pre($_POST[$name]);die;
//	if(isset($name) && is_array($name))
//		$setvalue = $name;
//		echo $setvalue;exit;
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<div name="check_div_csa" style="max-width: 500px; height: 100px; background-color: #F5D0A9; overflow: scroll;">';
			$optarray = array();
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[0]] = $row[1];	
				}
			
			//if($inioption)
				//echo'<option value="">'.$firstoptiontext.'</option>';                                
                       
                        echo'<input type="checkbox" class="checkBoxClassCsa" id="ckbCheckAllCsa" onclick="select_all_check_csa()">'.$all.' '."<br>";	
			foreach($optarray as $key => $value)
			{
				echo'<input type="checkbox" class="checkBoxClassCsa" name="'.$name.'[]" value="'.$key.'"';  echo' > '.$value.' '."<br>";	
			}
			echo'</div>';			
		}
		else
		{
			echo'<select name="'.$name.'" '.$jsfunction.'>';
			echo'<option value="">N.A.</option>';
			echo'</select>';			
		}
	}
//	else
//	{
//		echo'<span class="error">Sorry, database query failed</span>';
//		//exit();
//	}


}
function db_checkbox_all_select_csa_set($dbc, $name='', $q){

	$r = mysqli_query($dbc, $q);
	//pre($_POST[$name]);die;
	$setvalue = $valuetoset;
	if(isset($_POST[$name]) && is_array($_POST[$name]))
		$setvalue = $_POST[$name];
		
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<div name="check_div_csa" id="lol" style="max-width: 500px; height: 100px; background-color: #F5D0A9; overflow-y: scroll;">';
			$optarray = array();
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[0]] = $row[1];	
				}
			
			//if($inioption)
				//echo'<option value="">'.$firstoptiontext.'</option>';                                
                       
                        echo'<input type="checkbox" class="checkBoxClassCsa" id="ckbCheckAllCsa" onclick="select_all_check_csa()">'. All.' '."<br>";	
			foreach($optarray as $key => $value)
			{
				echo'<input type="checkbox" class="checkBoxClassCsa" name="'.$name.'[]" value="'.$key.'"'; if(in_array($key,$setvalue)) echo'checked'; echo' > '.$value.' '."<br>";	
			}
			echo'</div>';			
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

function db_checkbox_all_select_report($dbc, $name='', $q){
        //echo $q;
	$r = mysqli_query($dbc, $q);
	//pre($_POST[$name]);die;
	$setvalue = $valuetoset;
	if(isset($_POST[$name]) && is_array($_POST[$name]))
		$setvalue = $_POST[$name];
		
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<div name="check_div_report" style="max-width: 500px; height: 100px; background-color: #F5D0A9; overflow: scroll;">';
			$optarray = array();
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[0]] = $row[1];	
				}
			
			//if($inioption)
				//echo'<option value="">'.$firstoptiontext.'</option>';                                
                       
                        echo'<input type="checkbox" class="checkBoxClassReport" id="ckbCheckAllReport" onclick="select_all_check_report()">'. All.' '."<br>";	
			foreach($optarray as $key => $value)
			{
				echo'<input type="checkbox" class="checkBoxClassReport" name="'.$name.'[]" value="'.$key.'"'; if(in_array($key,$setvalue)) echo'checked'; echo' > '.$value.' '."<br>";	
			}
			echo'</div>';			
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
function db_checkbox_all_select_division($dbc, $name='', $q){

	$r = mysqli_query($dbc, $q);
	//pre($_POST[$name]);die;
	$setvalue = $valuetoset;
	if(isset($_POST[$name]) && is_array($_POST[$name]))
		$setvalue = $_POST[$name];
		
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<div name="check_div_division" style="max-width: 500px; height: 100px; background-color: #F5D0A9; overflow: scroll;">';
			$optarray = array();
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[0]] = $row[1];	
				}
			
			//if($inioption)
				//echo'<option value="">'.$firstoptiontext.'</option>';                                
                       
                        echo'<input type="checkbox" class="checkBoxClassDivision" id="ckbCheckAllDivision" onclick="select_all_check_division()">'. All.' '."<br>";	
			foreach($optarray as $key => $value)
			{
				echo'<input type="checkbox" class="checkBoxClassDivision" name="'.$name.'[]" value="'.$key.'"'; if(in_array($key,$setvalue)) echo'checked'; echo' > '.$value.' '."<br>";	
			}
			echo'</div>';			
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
function db_checkbox_all_select_cat($dbc, $name='', $q){

	$r = mysqli_query($dbc, $q);
	//pre($_POST[$name]);die;
	$setvalue = $valuetoset;
	if(isset($_POST[$name]) && is_array($_POST[$name]))
		$setvalue = $_POST[$name];
		
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<div name="check_div_cat" style="max-width: 500px; height: 100px; background-color: #F5D0A9; overflow: scroll;">';
			$optarray = array();
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[0]] = $row[1];	
				}
			
			//if($inioption)
				//echo'<option value="">'.$firstoptiontext.'</option>';                                
                       
                        echo'<input type="checkbox" class="checkBoxClassCat" id="ckbCheckAllCat" onclick="select_all_check_cat()">'. All.' '."<br>";	
			foreach($optarray as $key => $value)
			{
				echo'<input type="checkbox" class="checkBoxClassCat" name="'.$name.'[]" value="'.$key.'"'; if(in_array($key,$setvalue)) echo'checked'; echo' > '.$value.' '."<br>";	
			}
			echo'</div>';			
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
function db_checkbox_all_select_product($dbc, $name='', $q,$all=true){

	$r = mysqli_query($dbc, $q);
	//pre($_POST[$name]);die;
	$setvalue = $valuetoset;
	if(isset($_POST[$name]) && is_array($_POST[$name]))
		$setvalue = $_POST[$name];
		
	if($r)
	{
		if(mysqli_num_rows($r)>0)
		{
			echo'<div name="check_div_product" style="max-width: 500px; height: 100px; background-color: #F5D0A9; overflow-y: scroll;">';
			$optarray = array();
				while($row = mysqli_fetch_row($r))
				{
					$optarray[$row[0]] = $row[1];	
				}
			
			//if($inioption)
				//echo'<option value="">'.$firstoptiontext.'</option>';                                
              // echo $all;         
			if($all)
			{
	            echo'<input type="checkbox" class="checkBoxClassProduct" id="ckbCheckAllProduct" onclick="select_all_check_product()">'. All.' '."<br>";
			}

			foreach($optarray as $key => $value)
			{
				echo'<input type="checkbox" class="checkBoxClassProduct" name="'.$name.'[]" value="'.$key.'"'; if(in_array($key,$setvalue)) echo'checked'; echo' > '.$value.' '."<br>";	
			}
			echo'</div>';			
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
	//h1($q);
	//$noofrows = mysqli_num_rows($r);
        //pre($r);
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
	//h1("SELECT $col FROM $table WHERE $where LIMIT 1");
	global $dbc;
	list($opt, $rs) = run_query($dbc, "SELECT $col FROM $table WHERE $where LIMIT 1");
	return ($opt) ? $rs[$col] : '';
}

function myrowvaljoin($table1, $col,$table2,$on_cond, $where)
{
global $dbc;
//h1("SELECT $col FROM $table1 INNER JOIN $table2 ON $on_cond WHERE $where LIMIT 1");
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

?>