<?php #  login.php
if(!defined('BASE_URL')) die('direct script access not allowed');

//echo $_SERVER['DOCUMENT_ROOT']."dfsfsdfsdf";
$myobj = new scheme();
$dboard = new dashboard();
$obj = new dealer_sale();
$cls_func_str = 'profile_dtl';
$rs11 = array();
$dealerId = $_SESSION[SESS.'data']['dealer_id'];
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
$rs11 = $myobj->get_dealer_scheme_list($filter="dealer_id = '$dealerId'",  $records = '', $orderby='');
$db_rs = $dboard->available_stock($filter="dealer_id = '$dealerId'",  $records = '', $orderby='');
$exp_bat = $dboard->expiry_batch($filter="dealer_id = '$dealerId'",  $records = '', $orderby='');
$d_profile = $dboard->dealer_profile($dealerId);
$curr_scheme = $dboard->current_scheme();
$focus_target = $dboard->target_focus();
$prev_challan = $dboard->get_challan_prev();
$prev_challan_count = $dboard->get_challan_count();
$retailer_count = $dboard->total_retailer();
$complaint  = $dboard->complaint_list();
$receive_amount = $dboard->receive_amount();
$pending_invoice = $dboard->pending_invoice();
$invoice_amt = $dboard->invoice_amount();
$focus_product = $dboard->focus_product();
//$focus_product = $dboard->mtd_totalinvoice();
//print_r($receive_amount);
//pre($complaint);
//print_r($d_profile);
foreach($d_profile as $kk => $row)
{
  //  print_r($row);
    
if(isset($_POST['submit']) && $_POST['submit'] == 'Update')
{
    //echo "manisha";
  if(valid_token($_POST['hf'])) // checking if post value is same as timestamp stored in session during form load
  {
     
    //calculating the user authorisastion for the operation performed, function is defined in common_function
   // list($checkpass, $fmsg) = user_auth_msg($auth['add_opt'], $operation = 'edit', $id = $_POST['eid']);    
   // pre($fmsg);
   // if($checkpass)
  //  {
        // echo "manisha"; 
      // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
      //magic_quotes_check($dbc, $check=true);
      $funcname = $cls_func_str.'_edit';
      $action_status = $dboard->$funcname($_POST['dealer_id']); // $myobj->item_category_edit()
      if($action_status['status'])
      {
        echo '<span class="asm">'.$action_status['myreason'].'</span>';       
        //unset($_SESSION[SESS.'securetoken']); 
        //show_row_change(BASE_URL_A.'?option='.$formaction, $_POST['eid']);
        unset($_POST);
      }
      else
        echo '<span class="awm">'.$action_status['myreason'].'</span>';
     
    }
    else
      echo'<span class="awm">'.$fmsg.'</span>';
 // }
 // else
 //   echo'<span class="awm">Please do not try to hack the system.</span>';
}


}
$rs = array();
$filter = array();
$filterused = '';
$funcname = 'get_mtd_list';
if(isset($_POST['submit']) && $_POST['submit'] == 'Search')
{
  
    magic_quotes_check($dbc, $check=true);
    // pre($_POST);
      $filterstr = array();
      
        $start =$_POST['date'];
        //echo $start;
        $filter[] = $start;
        //$filterstr[] = '<b>Start : </b>'.$_POST['from_date'];
     
      $filter[] = $_SESSION[SESS.'data']['dealer_id'];
    //  pre($filter);
      //$filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');      
        $rs = $dboard->$funcname($filter,  $records = '', $orderby =''); // $myobj->get_item_category_list()
      
}
else {

     $filter[] = date("Y-m");
     $filter[] = $_SESSION[SESS.'data']['dealer_id'];
    
     $rs = $dboard->$funcname($filter,  $records = '', $orderby ="");
}
?>
<?php
if(isset($_POST['submit']) && $_POST['submit'] == 'Claim')
{

			$action_status =  $obj->claim_desk_save(); // $myobj->item_category_save()
			if($action_status['status'])
			{
				echo '<span class="asm">'.$action_status['myreason'].'</span>';
				//show_row_change(BASE_URL_A.'?option='.$formaction, $action_status['rId']);
				unset($_POST);
				 		
			}
			else
				echo '<span class="awm">'.$action_status['myreason'].'</span>';
		
	
}

?>
 <?php if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ // to show the form when and only when needed?>

 <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>"  name="genform" onsubmit="return checkForm_alert('genform');" enctype="multipart/form-data">
      <fieldset>
        <legend class="legend" style="background-color: #438eb9;font-size: 100%;font-family: Arial, Georgia, Serif; color:white;"><?php echo 'Distributer Profile Details'; ?></legend>
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" /> 
        <input type="hidden" id="dealer_id" name="dealer_id"  value="<?php echo $row['dealer_id']; ?>"  />
       <br/>
             
                 <div class="col-xs-2" ><strong>Full Name</strong> 
               <input type="text" id="full_name" name="full_name"  value="<?php echo $row['person_name']; ?>"  />
                 </div>
    
           
              <br/>
              
          
                <div><strong>User Name</strong> 
                <input type="text" class="user_name"  name="user_name" value="<?php echo $row['uname'];?>" readonly/> 
               </div>
              
            <br/>
         
                <div class="col-xs-2"><strong>Email Id</strong>
                    <input type="text" class="email_id"  name="email_id" value="<?php echo $row['email'];?>" /> 
               </div>
            <br/>            
        
                <div class="col-xs-2"><strong>Phone</strong>
                    <input type="text" class="phone"  name="phone" value="<?php echo $row['phone'];?>" /> 
               </div>
            <br/>
           
                <!--<div class="col-xs-2"><strong>Images</strong><br/>
                    <input type="file"   name="image" accept="image/*" onchange="loadFile1(event)" /> 
              <img id="output1" src="../myuploads/profile_images/<?=$_POST['image']?>" style="width:200px;"/>
                </div>-->
            <?php 
            //echo $row['profile_pic'];
            if($row['profile_pic'] != ''){
                   $img =  $row['profile_pic'];
                 }else{
                    $img =  'images/logo.png';
                 }
            
            ?>
              <div class="col-xs-2" ><strong>Profile Pic</strong><br/>              
                 <input type="file" name="image" id="image" accept="image/*" onchange="loadFile1(event)">
                <!-- <img id="output1" src="../client/myuploads/profile_images/<?php echo $img; ?>" style="width:200px;"/>-->
                 
                   <?php if($row['profile_pic'] != ''){  ?>
                     <img id="output1" src="../client/myuploads/profile_images/<?php echo $img; ?>" style="width:200px;"/>
                    <?php }else{ ?>
                      <img id="output1" src="../client/myuploads/profile_images/user.png" style="width:200px;"/>                    
                    <?php  } ?>
             
             </div>
            <br/>
            <script>
                   var loadFile1 = function (event) {
                    var output1 = document.getElementById('output1');
                    output1.src = URL.createObjectURL(event.target.files[0]);
                   };
                </script>
       
             <div colspan="4" align="center">                 
                <input style="background-color: #438eb9" id="mysave" type="submit" name="submit" value="<?php echo'Update';?>" />     
                <input onclick="parent.$.fn.colorbox.close();" type="button" value="Close" />          
             </div>
       
      </fieldset>
 </form>
   <?php
//This block of code will help in the print work
if (isset($_GET['actiontype'])) {
    switch ($_GET['actiontype']) {
        case'print':
       // echo 'hiiiiiiiiiiiiii';die;
            require_once('challan-print.inc.php');
            exit();
            break;
        case'claim_print':
       // echo 'hiiiiiiiiiiiiii';die;
            require_once('claim_report.inc.php');
            exit();
            break;
        default:
            $filepath = BASE_URI_ROOT . ADMINFOLDER . SYM . 'modules' . SYM . 'sales' . SYM . 'invoice' . SYM . 'invoice-print.inc.php';
            if (is_file($filepath))
                require_once($filepath);
            exit();
            break;
    }//switch($_GET['actiontype']){ ends
}
//This block of code will help in the print work ens
?>                                                     
 <?php }else{  ?>
 <?php 
            //echo $row['profile_pic'];
            if($row['profile_pic'] != ''){
                   $img =  $row['profile_pic'];
                 }else{
                    $img =  'images/logo.png';
                 }
            
            ?>

<div class="hr dotted"></div>

<div>
    <div id="user-profile-1" class="user-profile row">


<style type="text/css">
    .row{margin-left: 0px !important; margin-right: 0px !important;}
    .container-div-first{
        border:1px solid #cccccc;margin:10px; width:280px; float: left;
    }
    .container-div-multiple{
        border:1px solid #cccccc;margin:10px 10px 10px 0px;width:290px; float: left;
    }
    p.title{
        border-bottom:2px solid #cccccc; text-align:center; padding-bottom:5px; font-size:1.1em; font-weight:bold;
    }
    .lform tabel,td,th{
        border: solid 1px #cccccc;
    }
</style>
<div class="row">
 
<style>
    hover img {
    width: 250px;
    height: 375px;
    padding: 0px 5px 0px 5px;
    
}
    </style>
    
<!--------------------------------------------------------GRAPH----------------------------------------------------------->
<div class="row">
        <div class="space-12"></div>

        <div class="col-sm-12 infobox-container">
                <div class="infobox infobox-green">
                        <div class="infobox-icon">
                                <i class="ace-icon fa fa-list-alt"></i>
                        </div>

                        <div class="infobox-data">
                                <span class="infobox-data-number"><?=$prev_challan_count['today']?></span>
                                <div class="infobox-content"><strong>Today's Number of Bill</strong></div>
                        </div>
                      <?php if($prev_challan_count['today']>=$prev_challan_count['prev']){
                        echo'<div class="stat stat-success"><span title="'.($prev_challan_count['today']-$prev_challan_count['prev']).' Grater than to Yesterday">+'.($prev_challan_count['today']-$prev_challan_count['prev']).'</span></div>';
                        }else
                        {
                         echo'<div class="stat stat-important"><span title="'.($prev_challan_count['prev']-$prev_challan_count['today']).' Less than to Yesterday">-'.($prev_challan_count['prev']-$prev_challan_count['today']).'</span></div>';
                        }
                        ?>
                </div>

                <div class="infobox infobox-blue">
                        <div class="infobox-icon">
                                <i class="ace-icon fa fa-user"></i>
                        </div>

                        <div class="infobox-data">
                                <span class="infobox-data-number"><?=$retailer_count['total_retailer']?></span>
                                <div class="infobox-content"><strong>Total Retailers</strong></div>
                        </div>

                        <div class="badge badge-success" >
                        <?php  
                       echo'+'.$retailer_count['month_ret'].' <i class="ace-icon fa fa-arrow-up"></i> This Month';
                        ?>
                            
                               
                        </div>
                </div>

                <div class="infobox infobox-pink">
                        <div class="infobox-icon">
                                <i class="ace-icon fa fa-inr"></i>
                        </div>

                        <div class="infobox-data">
                           
                                <span class="infobox-data-number">₹ <?=$receive_amount['this']?></span>
                                <div class="infobox-content"><strong>Purchase Value of
 <?php echo date("F", strtotime(date('Y-m-d')));?></strong></div>
                        </div>
                        <!--<div class="stat stat-important"><span title='Less than from last month'>4%</span></div>-->
                </div>

                <div class="infobox infobox-orange">
                        <div class="infobox-icon">
                       <i class="ace-icon fa fa-file-text-o"></i>
                        </div>

                        <div class="infobox-data">
                                <span class="infobox-data-number"><a href="index.php?option=sale-order-detailes" style="color:#E8B110">
 <?=$pending_invoice?></a></span>
                                <div class="infobox-content"><strong>Pending Invoice</strong></div>
                        </div>
                </div>
               <div class="infobox infobox-brown">
<!--                        <div class="infobox-icon">
                                <i class="ace-icon fa fa-cart-plus"></i>
                        </div>-->

                        <div class="infobox-data">
                            
            <span class="infobox-data-number"><span title='Total Billing'>₹<?=$invoice_amt['amount']?></span>
       <span title='Total Outstanding'>/  ₹<?=$invoice_amt['remaining']?></span></span>
            <div class="infobox-content"><strong>Total Billing Value / Outstanding of
 <?php echo date("F", strtotime(date('Y-m-d')));?></strong></div>
                        </div>
                </div>

               

                <div class="space-6"></div>

        </div>

        <div class="vspace-12-sm"></div>
      
</div>
<script>
$(document).ready(function(){

$(".monthPicker").datepicker({ 
dateFormat: 'mm-yy',
changeMonth: true,
changeYear: true,
showButtonPanel: true,

onClose: function(dateText, inst) { 
var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val(); 
var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val(); 
$(this).val($.datepicker.formatDate('yy-mm', new Date(year, month, 1)));
}
});

$(".monthPicker").focus(function () {
$(".ui-datepicker-calendar").hide();
$("#ui-datepicker-div").position({
my: "center top",
at: "center bottom",
of: $(this)
}); 
});

});
</script>


<hr><div class="row"><div class="col-md-4"></div>

<div class="col-md-4">
<form class="form-horizontal" method="post" action="<?php if (isset($eformaction)) echo $eformaction; ?>"  name="genform" onsubmit="return checkuniquearray('genform');" enctype="multipart/form-data">
   <input type="text" id="month" name="date" class="monthPicker" value="<?php if(isset($_POST['date'])) echo $_POST['date']; else echo date('Y-m'); ?>" />           
    <input id="mysave" class="btn btn-sm btn-info" type="submit" name="submit" value="Search" />    
</form>
</div><div class="col-md-4"></div>
    </div>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
<!--<script type="text/javascript" src="graph.js"></script>-->
<!------------------------------------------MTD REPORT------------------------------------------------>
<div class="row">

<div class="col-xs-6 col-sm-3 pricing-box">
        <div class="widget-box widget-color-red">
                <div class="widget-header">
                        <h5 class="widget-title bigger lighter"><strong>Total Billing Value of
 <?php if(isset($_POST['date'])){echo date("F-Y", strtotime($_POST['date']));} else {echo date("F-Y");}?> </strong></h5>
                </div>

                <div class="widget-body">
                        <div class="widget-main">
                                <ul class="list-unstyled spaced2">
                                        <li>
                                                <i class="ace-icon fa fa-check green"></i>
    <strong>Bill Of <?php echo date("F-Y", strtotime($rs['totalinvoice']['month']));?>
        -: <span style="float:right">₹ <?=$rs['totalinvoice']['amount']?> </span></strong>
                                        </li>

                                        <li>
                                              <i class="ace-icon fa fa-check green"></i>
    <strong>Bill Of <?php echo date("F-Y", strtotime($rs['totalinvoice']['pmonth']));?> 
        -: <span style="float:right">  ₹ <?=$rs['totalinvoice']['amountp']?> </span></strong>
                                        </li>
 <li>&nbsp; </li>  
                                        <li> <center>
                                                <canvas id="piechart" width="150" height="150"></canvas>
  
  <script type="text/javascript">
    // Get the context of the canvas element we want to select
    var ctx = document.getElementById("piechart").getContext("2d");
    var amt = "<?=$rs['totalinvoice']['amtgraph']?>";
    var amtp = "<?=$rs['totalinvoice']['amtpgraph']?>";
   /// alert(amt);
   // alert(amtp);
    var data = [{
        value: amt,
        color:"#F54242",
        highlight: "#FF5A5E",
        label: "Curr.Month"
    },
    {
        value: amtp,
        color: "#46BFBD",
        highlight: "#5AD3D1",
        label: "Prev.Month"
    }
   ];
    
    var options = {
      animateScale: true
    };

    var myNewChart = new Chart(ctx).Pie(data,options);

  </script> </center>
                                        </li>

                                           
                                </ul>

                                                               
                        </div>
<!--
                        <div>
                                <a href="#" class="btn btn-block btn-inverse">
                                        <i class="ace-icon fa fa-shopping-cart bigger-110"></i>
                                        <span>Buy</span>
                                </a>
                        </div>-->
                </div>
        </div>
</div>

<div class="col-xs-6 col-sm-3 pricing-box">
        <div class="widget-box widget-color-orange">
                <div class="widget-header">
                        <h5 class="widget-title bigger lighter"><strong>Retailers Reach of <?php if(isset($_POST['date'])){echo date("F-Y", strtotime($_POST['date']));} else {echo date("F-Y");}?> </strong></h5>
               </strong></h5>
                </div>

                <div class="widget-body">
                        <div class="widget-main">
                                <ul class="list-unstyled spaced2">
                                        <li>
                                                <i class="ace-icon fa fa-check green"></i>
                                               <strong>Total Call <?php echo date("F-Y", strtotime($rs['totalinvoice']['month']));?> 
        -: <span style="float:right"> <?=$rs['retailerreach']['totalretailer']?> </span></strong>  
                                        </li>

                                        <li>
                                                <i class="ace-icon fa fa-check green"></i>
                                          <strong>Total Productive <?php echo date("F-Y", strtotime($rs['totalinvoice']['month']));?> 
        -: <span style="float:right"> <?=$rs['retailerreach']['totalp']?> </span></strong> 
                                        </li>

                                        <li>
                                                <i class="ace-icon fa fa-check green"></i>
                                               <strong>Total Non-Productive <?php echo date("F-Y", strtotime($rs['totalinvoice']['month']));?> 
        -: <span style="float:right"> <?=$rs['retailerreach']['totalnp']?> </span></strong> 
                                        </li>
<li>
                                        <center>
                                                <canvas id="piechart1" width="150" height="150"></canvas>
  
  <script type="text/javascript">
    // Get the context of the canvas element we want to select
    var ctx = document.getElementById("piechart1").getContext("2d");
    var tc = "<?=$rs['retailerreach']['totalretailer']?>";
    var tp = "<?=$rs['retailerreach']['totalp']?>";
    var tnp = "<?=$rs['retailerreach']['totalnp']?>";
   /// alert(amt);
   // alert(amtp);
    var data = [
        {
        value: tc,
        color: "#FDB45C",
        highlight: "#FFC870",
        label: "Total Call"
    },
        {
        value: tp,
        color:"#B00101",
        highlight: "#B53A3A",
        label: "Productive Call"
    },
    {
        value: tnp,
        color: "#46BFBD",
        highlight: "#5AD3D1",
        label: "Non-Productive"
    }
   ];
    
    var options = {
      animateScale: true
    };

    var myNewChart = new Chart(ctx).Pie(data,options);

  </script> </center></li>
                                </ul>

                               
                        </div>

<!--                        <div>
                                <a href="#" class="btn btn-block btn-warning">
                                        <i class="ace-icon fa fa-shopping-cart bigger-110"></i>
                                        <span>Buy</span>
                                </a>
                        </div>-->
                </div>
        </div>
</div>

<div class="col-xs-6 col-sm-3 pricing-box">
        <div class="widget-box widget-color-blue">
                <div class="widget-header">
                        <h5 class="widget-title bigger lighter"><strong>Total Received Value of
 <?php if(isset($_POST['date'])){echo date("F-Y", strtotime($_POST['date']));} else {echo date("F-Y");}?> </strong></h5>
                </div>

                <div class="widget-body">
                        <div class="widget-main">
                                <ul class="list-unstyled spaced2">
                                        <li>
                                                <i class="ace-icon fa fa-check green"></i>
    <strong>Bill Of <?php echo date("F-Y", strtotime($rs['totalinvoice']['month']));?>
        -: <span style="float:right">₹ <?=$rs['received']['ramount']?> </span></strong>
                                        </li>

                                        <li>
                                              <i class="ace-icon fa fa-check green"></i>
    <strong>Bill Of <?php echo date("F-Y", strtotime($rs['totalinvoice']['pmonth']));?> 
        -: <span style="float:right">  ₹ <?=$rs['received']['ramountp']?> </span></strong>
                                        </li>
 <li>&nbsp; </li>  
                                        <li> <center>
                                                <canvas id="piechart2" width="150" height="150"></canvas>
  
  <script type="text/javascript">
    // Get the context of the canvas element we want to select
    var ctx = document.getElementById("piechart2").getContext("2d");
    var ramt = "<?=$rs['received']['ramtgraph']?>";
    var ramtp = "<?=$rs['received']['ramtpgraph']?>";
   /// alert(amt);
   // alert(amtp);
    var data = [{
        value: ramt,
        color:"#055AE6",
        highlight: "#3C83F7",
        label: "Curr.Month"
    },
    {
        value: ramtp,
        color: "#D6A004",
        highlight: "#DAAF32",
        label: "Prev.Month"
    }
   ];
    
    var options = {
      animateScale: true
    };

    var myNewChart = new Chart(ctx).Pie(data,options);

  </script> </center>
                                        </li>

                                           
                                </ul>

                                                               
                        </div>
<!--
                        <div>
                                <a href="#" class="btn btn-block btn-inverse">
                                        <i class="ace-icon fa fa-shopping-cart bigger-110"></i>
                                        <span>Buy</span>
                                </a>
                        </div>-->
                </div>
        </div>
</div>

<div class="col-xs-6 col-sm-3 pricing-box">
        <div class="widget-box widget-color-green">
                <div class="widget-header">
                        <h5 class="widget-title bigger lighter">Payment Collection</h5>
                </div>

                <div class="widget-body">
                        <div class="widget-main">
                                <ul class="list-unstyled spaced2">
                                        <li>
                                                <i class="ace-icon fa fa-check green"></i>
    <strong>Bill Of <?php echo date("F-Y", strtotime($rs['totalinvoice']['month']));?>
        -: <span style="float:right">₹ <?=$rs['payment']['pamount']?> </span></strong>
                                        </li>

                                        <li>
                                              <i class="ace-icon fa fa-check green"></i>
    <strong>Bill Of <?php echo date("F-Y", strtotime($rs['totalinvoice']['pmonth']));?> 
        -: <span style="float:right">  ₹ <?=$rs['payment']['pamountp']?> </span></strong>
                                        </li>
 <li>&nbsp; </li>  
                                        <li> <center>
                                                <canvas id="piechart4" width="150" height="150"></canvas>
  
  <script type="text/javascript">
    // Get the context of the canvas element we want to select
    var ctx = document.getElementById("piechart4").getContext("2d");
    var ramt = "<?=$rs['payment']['pamtgraph']?>";
    var ramtp = "<?=$rs['payment']['pamtpgraph']?>";
   /// alert(amt);
   // alert(amtp);
    var data = [{
        value: ramt,
         color:"#048F10",
        highlight: "#0BAD19",
        label: "Curr.Month"
    },
    {
        value: ramtp,
        color: "#C88002",
        highlight: "#F7B541",
        label: "Prev.Month"
    }
   ];
    
    var options = {
      animateScale: true
    };

    var myNewChart = new Chart(ctx).Pie(data,options);

  </script> </center>
                                        </li>

                                           
                                </ul>
                        </div>

<!--                        <div>
                                <a href="#" class="btn btn-block btn-success">
                                        <i class="ace-icon fa fa-shopping-cart bigger-110"></i>
                                        <span>Buy</span>
                                </a>
                        </div>-->
                </div>
        </div>
</div>
</div>

<hr>
<div class="row">
<?php
$data = array();
$q1="SELECT sum((balance_stock-salable_stock)*dealer_available_stock.rate) AS amount1, 
    SUM(stock_manual.qty*stock_manual.rate) as amount2,catalog_view.c1_name as c1_name,catalog_view.c1_id as cat FROM `dealer_available_stock` 
    INNER JOIN catalog_view ON catalog_view.product_id=dealer_available_stock.pid 
    LEFT JOIN `stock_manual` ON `stock_manual`.`product_id`= `dealer_available_stock`.`pid`
    WHERE dealer_available_stock.dealer_id = '$dealer_id' GROUP BY catalog_view.c1_id";
    $r1=  mysqli_query($dbc, $q1);
    while($result = mysqli_fetch_assoc($r1))
{
  
  $id = $result['cat'];
             //echo "ANKUSH";
$stock = $result['amount1']+$result['amount2'];
$data[$id]['name'] = $result['c1_name'];
$data[$id]['stock'] = $stock;
  
}

?>
<div class="col-xs-6 col-sm-6 widget-container-col ui-sortable" id="widget-container-col-6" style="min-height: 100px;">
		<div class="panel-group">
			<div class="panel panel-primary">
			  <div class="panel-heading"><i class="fa fa-laptop"> <strong>PRODUCT STOCK VALUE CATEGORYWISE</strong></i></div>			  
				  <div class="panel-body">
					<div class="ui-sortable-handle" id="widget-box-5">
                                             <span style="float:left">
<i class="ace-icon fa fa-bookmark" style="color:#ad0606"></i><strong> Blends : ₹ <?=number_format($data['120150507011222']['stock'], 2, '.', ',');?></strong><br/>
<i class="ace-icon fa fa-bookmark" style="color:#f67c0f"></i><strong> Sprinkler : ₹ <?=number_format($data['120150507011140']['stock'], 2, '.', ',');?></strong><br/>                                               
<i class="ace-icon fa fa-bookmark" style="color:#43114a"></i><strong> Straight Premium : ₹ <?=number_format($data['120150507011152']['stock'], 2, '.', ',');?></strong><br/>                                               
<i class="ace-icon fa fa-bookmark" style="color:#0a87b2"></i><strong> specialties Spices : ₹ <?=number_format($data['120150507011211']['stock'], 2, '.', ',');?></strong><br/>                                               
<i class="ace-icon fa fa-bookmark" style="color:#d5e007"></i><strong> Hing : ₹ <?=number_format($data['120150507011301']['stock'], 2, '.', ',');?></strong><br/>                                               
<i class="ace-icon fa fa-bookmark" style="color:#4d59f0"></i><strong> Whole : ₹ <?=number_format($data['120150622100550']['stock'], 2, '.', ',');?></strong><br/>                                               

                                            </span>
                                            <span style="float:right">
    
   <a href="index.php?option=balance-stock"><canvas id="piechartp" width="250" height="250"></canvas></a>
  
  <script type="text/javascript">
    // Get the context of the canvas element we want to select
     var ctx = document.getElementById("piechartp").getContext("2d");
    var blends = "<?=$data['120150507011222']['stock']?>";
    var sprinkler = "<?=$data['120150507011140']['stock']?>";
    var straight = "<?=$data['120150507011152']['stock']?>";
    var spices = "<?=$data['120150507011211']['stock']?>";
    var hing = "<?=$data['120150507011301']['stock']?>";
    var whole = "<?=$data['120150622100550']['stock']?>";
   /// alert(amt);
   // alert(amtp);
    var data = [{
        value: blends,
         color:"#ad0606",
        highlight: "#cc1212",
        label: "Blends"
    },
    {
        value: sprinkler,
         color:"#f67c0f",
        highlight: "#f5953f",
        label: "Sprinkler"
    },
    {
        value: straight,
         color:"#43114a",
        highlight: "#a274a9",
        label: "Straight Premium"
    },
    {
        value: spices,
         color:"#0a87b2",
        highlight: "#5da3bb",
        label: "Specialities Spices"
    },
    {
        value: hing,
        color: "#d5e007",
        highlight: "#e7ec7e",
        label: "Hing"
    },
    {
        value: whole,
        color: "#4d59f0",
        highlight: "#7b84ec",
        label: "Whole"
    }
   ];
    
    var options = {
      animateScale: true
    };

    var myNewChart = new Chart(ctx).Pie(data,options);

  </script> </span>

</div></div></div></div></div>

<!-----------------------------------------------SECOND GRAPH------------------------------------------------------>
<?php
$data = array();
$date = date("Y-m-d");
$prev_date = date('Y-m-d', strtotime($date .' -1 day'));
$q1="SELECT SUM(amount) AS amount1 FROM challan_order  WHERE ch_dealer_id = '$dealer_id'
    AND DATE_FORMAT(`ch_date`,'%Y-%m-%d') = '$date' ";
//echo $q1;
    $r1=  mysqli_query($dbc, $q1);
    $result = mysqli_fetch_assoc($r1);
    //pre($result);
   //echo $result['amount1'];
$q2="SELECT SUM(amount) AS amount2 FROM challan_order  WHERE ch_dealer_id = '$dealer_id'
    AND DATE_FORMAT(`ch_date`,'%Y-%m-%d') = '$prev_date' ";
    $r2=  mysqli_query($dbc, $q2);
    $result2 = mysqli_fetch_assoc($r2);

?>
<div class="col-xs-6 col-sm-6 widget-container-col ui-sortable" id="widget-container-col-6" style="min-height: 100px;">
		<div class="panel-group">
			<div class="panel panel-primary">
			  <div class="panel-heading"><i class="fa fa-laptop"> <strong>INVOICE DETAILS</strong></i></div>			  
				  <div class="panel-body">
					<div class="ui-sortable-handle" id="widget-box-5">
                                             <span style="float:left">
<i class="ace-icon fa fa-bookmark" style="color:#C88103"></i><strong> Today's Invoice : ₹ <?=number_format($result['amount1'], 2, '.', ',');?></strong><br/>
<i class="ace-icon fa fa-bookmark" style="color:#0354C8"></i><strong> Yesterday's Invoice : ₹ <?=number_format($result2['amount2'], 2, '.', ',');?></strong><br/>                                               

                                            </span>
                                            <span style="float:right">
    
   <a href="index.php?option=balance-stock"><canvas id="piechartpr1" width="250" height="250"></canvas></a>
  
  <script type="text/javascript">
    // Get the context of the canvas element we want to select
     var ctx = document.getElementById("piechartpr1").getContext("2d");
    var at = "<?=$result['amount1']?>";
    var ay = "<?=$result2['amount2']?>";
   
    var data = [{
        value: at,
         color:"#C88103",
        highlight: "#D99F38",
        label: "Today"
    },
    {
        value: ay,
        color: "#0354C8",
        highlight: "#5A87C8",
        label: "Yesterday"
    }
   ];
    
    var options = {
      animateScale: true
    };

    var myNewChart = new Chart(ctx).Pie(data,options);

  </script> </span>

</div></div></div></div></div></div>
</div>
<!-------------------------------------------___CATALOG WISE GRAPH--------------------------------------------->
<!--<a target="_blank" class="iframef" href="index.php?option=graph-product&showmode=1&mode=1&page=Edit Invoice/Challan&id='. $uid . '"><img src="./images/b_edit.png"></a>-->
	
<hr>
<!----------------------------------------------------Target Vs Achievemnt ON PURCHSE------------------------------------------------------->
    <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable" id="widget-container-col-12" style="min-height: 100px;">
	
	<div class="panel-group">
		<div class="panel panel-primary">
		  <div class="panel-heading"><i class="fa fa-bookmark"> PROGRESS SCHEME</i></div>
			  <div class="panel-body">
				<div class="ui-sortable-handle" id="widget-box-5">
		<!--Table Start End-->
                <table width="100%" border="0" cellspacing="2" cellpadding="3" >
				  <tr>
					<th style="width:50px;">&nbsp; Value</th>
					<th style="width:50px;">&nbsp; Scheme</th>    
					<th style="width:50px;">&nbsp; Start Date</th>
					<th style="width:50px;">&nbsp; End Date</th>  
					<th style="width:50px;">&nbsp; Achieved Value</th> 
				  </tr>
				 <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>"  name="genform" onsubmit="return checkForm_alert('genform');" enctype="multipart/form-data">
			 
				  <?php
				  //pre($curr_scheme);
				  if(!empty($curr_scheme)){
				  foreach($curr_scheme as $cs => $curr){ ?>
				  <tr>
					<td>&nbsp;<?php echo $curr['value'].' - '.$curr['value_to']; ?></td>
					<td >&nbsp; <?php echo $curr['scheme_gift']; ?></td>  
					<td>&nbsp;<?php echo $curr['start_date']; ?></td>
					<td >&nbsp; <?php echo $curr['end_date']; ?></td>
					<td style='width:30%' >&nbsp; <?php
					if($curr['achieved']>=$curr['value'] && $curr['achieved']<=$curr['value_to'])  
					{
					$gift = $curr['scheme_gift'];
					$g = strtolower($gift);
					$ge = str_replace(' ','_', $g);
					echo $curr['achieved']; 
					if(strpos($gift, '%' ) !== true) 
					{
					echo'<img src="gift/'.$ge.'.png" width="20%">';
					}
					echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="hidden" value="'.$curr['start_date'].'" name="start">
						<input type="hidden" value="'.$curr['end_date'].'" name="end">
						<input type="hidden" value="'.$curr['achieved'].'" name="achieved">
						<input type="hidden" value="'.$curr['scheme_gift'].'" name="scheme_gift">
						<a class="iframef" title="Claim Achieved" id="print_all" href="index.php?option=claim-pop&showmode=1&mode=1&actiontype=print&start='.$curr[start_date].'&end='.$curr[end_date].'&achieved='.$curr[achieved].'&scheme_gift='.$curr[scheme_gift].'">
						<input type="submit" class="btn btn-xs btn-success" name="submit" value="Claim"></a>';
					}
					else  if($curr['achieved'] > $curr['value'])  
					{
								   echo '<i style="color:#1d9d74" class="glyphicon glyphicon-ok">'; 
				   
					}
					?></td>
				  </tr>
				  <?php }} ?>
				 </form>
				</table>
            <!--Table End-->
        
    </div>
			  </div>
		</div>    
    </div>
	
    
  </div>

<!----------------------------------------------------END Target Vs Achievemnt ON PURCHSE------------------------------------------------------->
<div class="row">
    <div class="col-md-12"> &nbsp; </div>
</div>
<!----------------------------------------------------Target Vs Achievemnt ON FOCUS PRODUCT------------------------------------------------------->
    <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable" id="widget-container-col-12" style="min-height: 100px;">
		<div class="panel-group">
			<div class="panel panel-primary">
			  <div class="panel-heading"><i class="fa fa-laptop"> SECONDARY SCHEME</i></div>			  
				  <div class="panel-body">
					<div class="ui-sortable-handle" id="widget-box-5">
						<table width="100%" border="0" cellspacing="2" cellpadding="3" >
						  <tr>
							<th style="width:10px;">&nbsp; Sno.</th>
							<th style="width:80px;">&nbsp; Product Name</th>    
							<th style="width:50px;">&nbsp; Target Value</th>
							<th style="width:50px;">&nbsp; Achieved Value</th> 
							<th style="width:50px;">&nbsp; Time Period</th>
						   
						  </tr>
						 <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>"  name="genform" onsubmit="return checkForm_alert('genform');" enctype="multipart/form-data">
					 
						  <?php
						  //pre($curr_scheme);
						  if(!empty($focus_target)){
							  $finc = 1;
						  foreach($focus_target as $fs => $focus){
							  $target = $focus['target'];
							 // setlocale(LC_MONETARY, 'en_IN');
							 // $target = money_format('%!i', $target);
							  $target = number_format($focus['target'], 2, '.', ',');
							  $achieved = $focus['achieved'];
							  $achieved = money_format('%!i', $achieved);
							  
							  ?>
						  <tr>
							<td>&nbsp;<?php echo $finc; ?></td>
							<td >&nbsp; <?php echo $focus['product_name']; ?></td>
							<td >&nbsp; <?php echo "₹ ".$target; ?></td>
							<?php 
							if($focus['target']<=$focus['achieved'])
							{
							?>
							<td style="background-color:#049305; color:white;"><strong>&nbsp; <?php echo "₹ ".$achieved; ?></strong></td>
							<?php
							}
							else {?>
							<td >&nbsp; <?php echo "₹ ".$achieved; ?></td>
							<?php } ?>
							<td >&nbsp; <?php echo $focus['start_date']."&nbsp; To &nbsp;".$focus['end_date']; ?></td>
						  </tr>
						  <?php 
						  $finc++;
						  }} ?>
						 </form>
						</table>
				  </div>
			</div>    
		</div>
    
        <!--<div class="widget-header">
            <center><h4 class="widget-title smaller" style="background:#3A87AD"><span class="label label-warning arrowed arrowed-right" style="color:black" ><strong>SECONDARY SCHEME</strong></span></h4></center>
<u><img src="./images/green.jpeg" style="width:18px; height: 18px"> : ACHIEVED </u>
                    </div>-->

        
        
    </div>
  </div>
<!----------------------------------------------------END Target Vs Achievemnt ON PURCHSE------------------------------------------------------->
<!----------------------------------------------------END Target Vs Achievemnt ON PURCHSE------------------------------------------------------->
<div class="row">
    <div class="col-md-12"> &nbsp; </div>
</div>
<!----------------------------------------------------Target Vs Achievemnt ON FOCUS PRODUCT------------------------------------------------------->
    <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable" id="widget-container-col-12" style="min-height: 100px;">
		<div class="panel-group">
			<div class="panel panel-primary">
			  <div class="panel-heading"><i class="fa fa-laptop"> <strong>FOCUS PRODUCT SALE OF YESTERDAY</strong></i></div>			  
				  <div class="panel-body">
					<div class="ui-sortable-handle" id="widget-box-5">
						 <table width="100%" border="0" cellspacing="2" cellpadding="3" >
          <tr>
            <th style="width:10px;">&nbsp; Sno.</th>
            <th style="width:80px;">&nbsp; Product Name</th>    
            <th style="width:50px;">&nbsp; Quantity</th>
            <th style="width:50px;">&nbsp; Rate</th> 
            <th style="width:50px;">&nbsp; Value</th>
           
          </tr>
         <?php
          //pre($curr_scheme);
          if(!empty($focus_product)){
              $rinc = 1;
          foreach($focus_product as $fkey => $fvalue){
              $total = $fvalue['qty']*$fvalue['rate'];
              
             //  setlocale(LC_MONETARY, 'en_IN');
           //   $amt = money_format('%!i', $total);
            $amt =  number_format($total, 2, '.', ',');
              ?>
          <tr><td>&nbsp;<?php echo $rinc; ?></td>
                <td>&nbsp;<?php echo $fvalue['product_name']; ?></td>
            <td>&nbsp;<?php echo $fvalue['qty']; ?></td>
            <td >&nbsp; <?php echo $fvalue['rate']; ?></td>
            <td >&nbsp; <?php echo "₹ ".$amt; ?></td>
           
          </tr>
          <?php 
          $rinc++;
          }} ?>
         </form>
        </table>
				  </div>
			</div>    
		</div>
    
        <!--<div class="widget-header">
            <center><h4 class="widget-title smaller" style="background:#3A87AD"><span class="label label-warning arrowed arrowed-right" style="color:black" ><strong>SECONDARY SCHEME</strong></span></h4></center>
<u><img src="./images/green.jpeg" style="width:18px; height: 18px"> : ACHIEVED </u>
                    </div>-->

        
        
    </div>
  </div>
<!----------------------------------------------------END Target Vs Achievemnt ON PURCHSE------------------------------------------------------->


<div class="row">
    <div class="col-md-12"> &nbsp; </div>
</div>
 <!----------------------------------------------------CHALLAN PAY FOR PREV DAY------------------------------------------------------->
    <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable" id="widget-container-col-12" style="min-height: 100px;">
		<div class="panel-group">
			<div class="panel panel-primary">
			  <div class="panel-heading"><i class="fa fa-list-alt"> <strong>INVOICE/BILL OF YESTERDAY</strong></i></div>			  
				  <div class="panel-body">
					<div class="ui-sortable-handle" id="widget-box-5">
                    <table width="100%" border="0" cellspacing="2" cellpadding="3" >
          <tr>
            <th style="width:10px;">&nbsp; Sno.</th>
            <th style="width:50px;">&nbsp; Invoice Date</th>    
            <th style="width:80px;">&nbsp; Invoice Number</th>
            <th style="width:50px;">&nbsp; Payment Status</th> 
            <th style="width:50px;">&nbsp; Retailer Name</th>
           <th style="width:50px;">&nbsp; Total Amount</th>
          </tr>
         <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>"  name="genform" onsubmit="return checkForm_alert('genform');" enctype="multipart/form-data">
     
          <?php
          //pre($curr_scheme);
          if(!empty($prev_challan)){
              $pinc = 1;
          foreach($prev_challan as $ps => $pay){
               //pre($pay);
              $amt = 0;
              foreach ($pay['challan_item'] as $inkey => $invalue) {
                
                    $amt = $amt+$invalue['taxable_amt'];
              }
            //  setlocale(LC_MONETARY, 'en_IN');
            //  $amount = money_format('%!i', $amt);
             $amount = number_format($amt, 2, '.', ',');
             ?>
          <tr>
            <td>&nbsp;<?php echo $pinc; ?></td>
             <td >&nbsp; <?php echo $pay['ch_date']; ?></td>
            <td >&nbsp; <?php echo $pay['ch_no']; ?></td>
            <td >&nbsp; <?php echo $pay['payment']; ?></td>
            <td >&nbsp; <?php echo $pay['retailer_name']; ?></td>
            <td >&nbsp; <?php echo "₹ ".$amount; ?></td>
          </tr>
          <?php 
          $pinc++;
          }} ?>
         </form>
        </table></div>
            </div>
        </div>
    </div>
  </div>

<!----------------------------------------------------END Target Vs Achievemnt ON PURCHSE------------------------------------------------------->
  <div class="row">
    <div class="col-md-12"> &nbsp; </div>
</div>

<!----------------------------------------------------COMPLAINT OF DISTRIBUTOR------------------------------------------------------->
       <div class="col-xs-12 col-sm-12 widget-container-col ui-sortable" id="widget-container-col-12" style="min-height: 100px;">
		<div class="panel-group">
			<div class="panel panel-primary">
			  <div class="panel-heading"><i class="fa fa-laptop"> <strong>COMPLAINT OF YESTERDAY</strong></i></div>			  
				  <div class="panel-body">
					<div class="ui-sortable-handle" id="widget-box-5">
                    <table width="100%" border="0" cellspacing="2" cellpadding="3" >
          <tr>
            <th style="width:10px;">&nbsp; Sno.</th>
            <th style="width:50px;">&nbsp; Date</th>    
            <th style="width:80px;">&nbsp; Complaint Type</th>
            <th style="width:50px;">&nbsp; Person Name</th> 
            <th style="width:50px;">&nbsp; Complaint</th> 
            <th style="width:50px;">&nbsp; Status</th> 
            <!--<th style="width:50px;">&nbsp; Action</th>--> 
           </tr>
                  
         <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>"  name="genform" onsubmit="return checkForm_alert('genform');" enctype="multipart/form-data">
     
          <?php
          //pre($curr_scheme);
          if(!empty($complaint)){
              $pinc = 1;
          foreach($complaint as $ck => $comp){
               //pre($comp);
           $complaint_id = $comp['complaint_id'];
             ?>
          <tr>
            <td>&nbsp;<?php echo $pinc; ?></td>
            <td >&nbsp; <?php echo $comp['date']; ?></td>
            <td >&nbsp; <?php echo $comp['type']; ?></td>
            <td >&nbsp; <?php echo $comp['user_name']; ?></td>
            <td >&nbsp; <?php echo $comp['complaint']; ?></td>
            <td >&nbsp; <?php 
             if($comp['action']==0)
            {
                echo 'INITIATE';
            }
            else  if($comp['action']==1)
            {
                echo 'ON PROGRESS';
            }
            else  if($comp['action']==2)
            {
                echo 'COMPLETED';
            }
            
           ?></td>
<!--            <td> &nbsp;&nbsp;&nbsp;&nbsp;
                <?php //echo'<a class="iframef" title="Complaint" id="print_all" href="index.php?option=complaint-report&complaint_id='.$complaint_id.'&showmode=1&mode=1&actiontype=claim_print">
              //  <i class="glyphicon glyphicon-eye-open"></i></a>'; ?>
                </td>-->
          </tr>
          <?php 
          $pinc++;
          }} ?>
         </form>
        </table></div>
            </div>
        </div>
    </div>
  </div>
<!----------------------------------------------------END CMPLAINT------------------------------------------------------->
  <div class="row">
    <div class="col-md-12"> &nbsp; </div>
</div>
</div> 
</div>
    </div>

<?php } //if(!empty($rs)){?>
