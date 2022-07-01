<?php
//Check for a $page_title value:
//parent.$.fn.colorbox.close();
if(!isset($page_title)) $page_title = 'Panel - Powered by Manacle technologies pvt. ltd';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--<!doctype html>-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!--<meta name="viewport" content="width=device-width, initial-scale=1">-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $page_title; ?></title>
<!-- my global css files starts here -->
<link rel="stylesheet" href="./css/global.css" type="text/css" />
<link rel="stylesheet" href="./css/pagination.css" type="text/css" />
<style type="text/css">
  /*select{ background-color: #F5D0A9  }*/
</style>

<?php
//Will include the jquery and other third party widgets
require_once(BASE_URI_ROOT.ADMINFOLDER.'/include/my-jquery.php');
$loadjquery = isset($myjquery) ? $myjquery : $DEFAULT_JQUERY;
myjquery($loadjquery);
//Will include my javascript files customly written by me
require_once(BASE_URI_ROOT.ADMINFOLDER.'/include/my-js.php');
//include('./widgets/jquery-ui-timepicker-0.3.1/jquery-ui-timepicker.php'); 
//This code include the ajax files code written by me
(!isset($NO_AJAX)) ? require_once(BASE_URI_ROOT.ADMINFOLDER.'/include/my-ajax.php'):'';
//This code include the YUI MENU styles, Js files
(!isset($NO_YUI)) ? require_once(BASE_URI_ROOT.ADMINFOLDER.'/include/my-yui-menu.php'):'';

?>
<?php
if(isset($_SESSION[SESS.'user']))
{
    $mycompany = new company();
    $company_id = $_SESSION[SESS.'data']['company_id'];
    $company_data = $mycompany->get_company_list($filter='',  $records = '', $orderby='');
    $company_data = $company_data[$company_id]['name'];
   
}
?>
</head>
<?php if(!isset($_GET['showmode'])){
    //style="background-image: url('images/backgd.jpeg')"
    ?>
<body  onload="setSize();" onresize="setSize();" class="yui3-skin-sam">
<?php }else{?>
<body  class="yui3-skin-sam">
<?php }?>
  <div id="myheader" style="padding:1px; background-color:#FF9933; overflow:auto">
    <div class="top-line" style="float:left; color:#FFFFFF; font-weight:bold; margin-left:5px; font-size:18px;text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;"><?php echo PANEL_NAME;?></div>
    <?php if(isset($_SESSION[SESS.'user'])){?>
    
    <div style="float: right;color:#FFFFFF; font-weight:bold;font-size:14px;text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;">  &nbsp;&nbsp; Company : <?php echo $company_data;?> </div>
    <div style="float: right;color:#FFFFFF; font-weight:bold;font-size:14px;text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;"> User Name : <?php echo $_SESSION[SESS.'data']['first_name'].' '.$_SESSION[SESS.'data']['middle_name'].' '.$_SESSION[SESS.'data']['last_name']?> </div>
   
    <div style="float:right; color:#FFFFFF; font-weight:bold; margin-left:5px; font-size:14px;text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;" id="sesperiod"><?php  //echo send_session_period($_SESSION[SESS.'csess']); ?></div>
    <?php }?>
  </div>
  <?php if(isset($_SESSION[SESS.'user'])){require_once('./include/menu.inc.php');/*include('./include/menutop.php');*/}?>
  <div id="wrapper" >
      <div id="container" > 
