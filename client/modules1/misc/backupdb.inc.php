<?php #  leads.inc.php

/*
* This is the main content module.
* This page is included by index.php.
*/
if (!defined('BASE_URL')) require_once('../../page_not_direct_allow.php');
?>
<?php 
include'../client/modules/table.php';
$forma = 'BackUp DB'; // to indicate what type of form this is
$formaction = $p;
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
?>
	 <!-- <h1 style=""><?php echo $forma;?></h1>-->
      <div id="breadcumb"><a href="#">Miscellaneous</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;">Export</a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span></div>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view
# -------------------------------- code for handling of the previous, first and next button starts here 
//list($open, $first, $prev, $next, $last, $eformaction) = prev_next($id = 'itemId', $table = 'item', $formaction);	
# -------------------------------- code for handling of the previous, first and next button ends here



// code to edit region starts here
//if(isset($_POST['submit']) && $_POST['submit'] == 'Update')
//{
//	if(valid_token($_POST['hf'])) // checking if post value is same as timestamp stored in session during form load
//	{
//		$id = $_SESSION[SESS.'data']['id'];
//		//calculating the user authorisastion for the operation performed, function is defined in common_function
//		list($checkpass, $fmsg) = user_auth_msg($auth['edit_opt'], $operation = 'edit', $id);
//		if($checkpass)
//		{
//			// triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
//			magic_quotes_check($dbc, $check=true);
//			
//			if(mysqli_query($dbc, "UPDATE dealer_person_login SET `pass` = AES_ENCRYPT('$_POST[npass]', '".EDSALT."') WHERE dpId ='$id' LIMIT 1"))
//			{
//				$msg = '<span class="asm"><b>Password</b> successfull <b>'.$_POST['submit'].'d</b>.</span>';				
//				//updation of user activity in history log table starts here
//				$particular = 'Password update for user </b> '.$_SESSION[SESS.'data']['uname'].' <b></b> in system';
//				history_log($dbc, 'Update', $particular);
//				//updation of user activity in history log table ends here
//				unset($_POST);
//				echo $msg;
//			}
//			else
//				echo'<span class="awm">Sorry,<b>'.$forma.'</b> could not be <b>'.$_POST['submit'].'d</b>, some error occured.</span>';
//		}
//		else
//			echo'<span class="awm">'.$fmsg.'</span>';
//	}
//	else
//		echo'<span class="awm">Please do not try to hack the system.</span>';
//}
//?>

      <div id="row">
<?php

/* backup the db OR just a table */

$host = 'localhost';
$user = 'root';
$pass = 'root';
$name = 'msell-dsgroup-dms';
$tables = "*";
$link = mysql_connect($host,$user,$pass);
mysql_select_db($name,$link);

//get all of the tables
if($tables == "*")
{
$tables = array();
$result = mysql_query("SHOW TABLES");
while($row = mysql_fetch_row($result))
{
$tables[] = $row[0];
}
}
else
{
$tables = is_array($tables) ? $tables : explode(",",$tables);
}

//cycle through
foreach($tables as $table)
{
  //  echo $table."<br>";
$result = mysql_query("SELECT * FROM ".$table);
$num_fields = mysql_num_fields($result);

$return.= "DROP TABLE ".$table.";";
$row2 = mysql_fetch_row(mysql_query("SHOW CREATE TABLE ".$table));
$return.= "\n\n".$row2[1].";\n\n";

for ($i = 0; $i < $num_fields; $i++)
{
    
while($row = mysql_fetch_row($result))
{
    
$return.= "INSERT INTO ".$table." VALUES(";
for($j=0; $j<$num_fields; $j++)
{
  
$row[$j] = addslashes($row[$j]);
$row[$j] = ereg_replace("\n","\\n",$row[$j]);
if (isset($row[$j]))
    { $return.= ".$row[$j]." ; } 
    else { $return.= ""; }
if ($j<($num_fields-1)) { $return.= ","; }
}
$return.= ");\n";
}
}
$return.="\n\n\n";
}

$prefix="db_";
$tmpDir="../../dbbackup/";

$sqlFile = $tmpDir.$prefix.date("Y_m_d").".sql";
//echo $sqlFile;
//save file
$handle = fopen($sqlFile,"w+");
fwrite($handle,$return);
fclose($handle);

//}
  
?>
       <span class="asm">DATABASE HAS BEEN EXPORTED</span>
      </div><!-- workarea div ends here -->