<?php
/*require_once(DB);
require_once(BASE_URI.'functions/db_common_function.php');
require_once(BASE_URI.'functions/fileupload.php');
require_once(BASE_URI.'functions/common_function.php');
require_once(BASE_URI.'functions/mobile.php');
require_once(BASE_URI.'include/settings.php');
require_once(BASE_URI.'printouts-format/indian_currency_format1.php');
require_once(BASE_URI.'printouts-format/indian_currency_format.php');
// Check for a $page_title value:
if(!isset($page_title)) $page_title = 'Panel - Powered by Weboseo (P) Ltd';
function __autoload($class)
{
	require_once(BASE_URI.'include/classes/'.$class .'.php');
}*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $page_title; ?></title>
<style type="text/css">

</style>
<link rel="stylesheet" href="./css/global.css" type="text/css" />
<script src="./js/jquery/jquery-1.8.min.js"></script>
<!--<script src="./js/jquery/jquery-1.9.1.min.js"></script>
<script scr="./js/jquery/jquery-migrate-1.1.1.min.js"></script>-->
<!-- jQuery ui -->
<link rel="stylesheet" href="./widgets/jquery-ui-1.10.1/css/ui-lightness/jquery-ui-1.10.1.custom.min.css" />
<script src="./widgets/jquery-ui-1.10.1/js/jquery-ui-1.10.1.custom.js" type="text/javascript"></script>

<!-- using the jwerty library -->
<script type="text/javascript" src="./widgets/jwerty/jwerty-0.3.js"></script>
<script type="text/javascript" src="./js/keyboard_shortcuts.js"></script>

<!-- choosen autocomplete -->
<link rel="stylesheet" href="./widgets/chosen/chosen.css" />
<script src="./widgets/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> 
$(function(){
	$(".chzn-select").chosen();
	$(".combobox,#combobox").chosen(); 
	$(".chzn-select-deselect").chosen({allow_single_deselect:true}); 
});
</script>

<script type="text/javascript" src="./js/essential.js"></script>
<script type="text/javascript" src="./js/js_form_check.js"></script>

<?php include('./widgets/barcode/bar.php'); ?>

<!-- code to make jQuery Datepicker starts here -->
<script type="text/javascript" src="./widgets/jquery-ui-datepicker/jquery-ui-datepicker.js"></script>

<!-- code to make jQuery combobox starts here -->
<!--<link rel="stylesheet" href="./widgets/jquery-ui-combobox/combobox.css" />
<script type="text/javascript" src="./widgets/jquery-ui-combobox/jquery-ui-combobox.js"></script>-->

<!-- ajax coding to fetch the data from the server starts here  -->
<script type="text/javascript" src="./js/ajax.js"></script>
<script type="text/javascript" src="./js/ajax_pulldown/pulldown_js.js"></script>
<!-- ajax coding to fetch the data from the server ends here  -->

<!-- code to make ajax based deletion starts here -->
<script type="text/javascript" src="./js/ajax_delete/data_delete_js.js"></script>
<!-- code to make ajax based deletion ends here -->

<!-- code to make colorbox starts here -->
<link rel="stylesheet" href="widgets/colorbox/colorbox.css" />
<script type="text/javascript" src="widgets/colorbox/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="widgets/colorbox/mycolorbox.js"></script>

<!-- code to make general ajax queries starts here -->
<script type="text/javascript" src="./js/ajax_general/ajax_general_js.js"></script>
<!-- code to make general ajax queries ends here -->
<!-- code to make general ajax queries for div elements starts here -->
<script type="text/javascript" src="./js/ajax_general/ajax_general_div_js.js"></script>
<!-- code to make general ajax queries for div elements ends here -->

<!--Code For Zoomer Starts Here-->
<script src="widgets/zoomer/zoomple-1.4.js" type="text/javascript"></script>   
<script src="widgets/zoomer/zoomple.js" type="text/javascript"></script>        
<!--Code For Zoomer Ends Here-->

<!--Code For Photo Zoomer Darpan Starts Here-->
<script src="./widgets/photozoom/photoZoom.min.js" type="text/javascript"></script>   
<script type="text/javascript" src="./widgets/photozoom/myphotoZoom.js"></script>
<!--Code For Photo Zoomer Darpan Ends Here-->

<script type="text/javascript">
$(document).ready(function()
{
	$(document).keypress(function(event)
	{
		if(event.keyCode == 27)		//----------     keyCode 27 is for ESC key
		{
			parent.$.fn.colorbox.close();
		}
	});
});
</script>

</head>
<?php if(!isset($_GET['showmode'])){?>
<body  onload="setSize();" onresize="setSize();">
<?php }else{?>
<body>
<?php }?>

  <div id="wrapper">
    <div id="container">  