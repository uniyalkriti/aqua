<?php
// Check for a $page_title value:
//parent.$.fn.colorbox.close();
if(!isset($page_title)) $page_title = 'Panel - Powered by Manacle Technologies  (P) Ltd';
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
<script type="text/javascript" src="./hmenu/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="hmenu1/pro_drop_1.css" />
<script src="hmenu1/stuHover.js" type="text/javascript"></script>    
<?php
//Will include the jquery and other third party widgets
require_once(BASE_URI_ROOT.ADMINFOLDER.'/include/my-jquery.php');
$loadjquery = isset($myjquery) ? $myjquery : $DEFAULT_JQUERY;
myjquery($loadjquery);
//Will include my javascript files customly written by me
require_once(BASE_URI_ROOT.ADMINFOLDER.'/include/my-js.php');
//This code include the ajax files code written by me
(!isset($NO_AJAX)) ? require_once(BASE_URI_ROOT.ADMINFOLDER.'/include/my-ajax.php'):'';
//This code include the YUI MENU styles, Js files
//(!isset($NO_YUI)) ? require_once(BASE_URI_ROOT.ADMINFOLDER.'/include/my-yui-menu.php'):'';

?>
<script src="<?php echo BASE_URL_A;?>widgets/yahoomenu/yui-min.js" type="text/javascript"></script>
</head>
<?php if(!isset($_GET['showmode'])){?>
<body onload="setSize();" onresize="setSize();">
<?php }else{?>
<body>
<?php }?>
  <div  id="myheader" style=" padding:1px; background-color: #B84D4D; overflow:auto">
    <div style="float:left; color:#CCC; font-weight:bold; margin-left:5px; font-size:20px"><?php echo PANEL_NAME;?></div>
   
    <?php if(isset($_SESSION[SESS.'user'])){?>
    <div style="float:right; color:#CCC; font-weight:bold; margin-left:5px; font-size:14px" id="sesperiod"> &nbsp;&nbsp;SESSION : <?php  echo send_session_period($_SESSION[SESS.'csess']); ?></div>
    <div style="float: right;color:#CCC; font-weight:bold;font-size:14px">  User  : <?php echo $_SESSION[SESS.'data']['name']; ?> </div>
    <div style="float: right;color:#CCC; font-weight:bold;font-size:14px">  Distributor  : <?php echo $_SESSION[SESS.'data']['dealer_name']; ?> &nbsp; </div>
    <?php }?>
  </div>
  <?php if(isset($_SESSION[SESS.'user'])){require_once('./include/menu.inc.php');/*include('./include/menutop.php');*/}?>
  <div id="wrapper">
    <div id="container"> 