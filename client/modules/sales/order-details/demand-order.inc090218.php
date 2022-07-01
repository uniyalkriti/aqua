<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
//print_r($_SESSION);
$forma = 'Purchase Stock Details'; // to indicate what type of form this is
$formaction = $p;
$myobj = new demand_order();
$primary_sale = new dealer_sale();
$cls_func_str = 'demand_order'; //The name of the function in the class that will do the job
$myorderby = 'demand_order.created_date DESC'; // The orderby clause for fetching of the data
$myfilter = 'demand_order.id ='; //the main key against which we will fetch data in the get_item_category_function
$loc_level = $_SESSION[SESS.'constant']['location_level'];
//Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
$userid = $_SESSION[SESS.'data']['id'];
$sesId =  $_SESSION[SESS.'sess']['sesId'];
$role_id = $_SESSION[SESS.'data']['urole'];
$dealer_id =  $_SESSION[SESS.'data']['dealer_id'];
$stateid = $_SESSION[SESS.'data']['state_id'];
//Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
$user_data = $primary_sale->get_dsp_wise_user_data($dealer_id);

function moneyFormatIndia($num1) {
	//$num1='';
    $explrestunits = "" ;
	$number = explode('.',$num1);
	$num = $number[0];
	//print_r($num1);
    if(strlen($num)>3) {
        $lastthree = substr($num, strlen($num)-3, strlen($num));
        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for($i=0; $i<sizeof($expunit); $i++) {
            // creates each of the 2's group and adds a comma to the end
            if($i==0) {
                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
            } else {
                $explrestunits .= $expunit[$i].",";
            }
        }
        if($number[1]!= ''){
        $thecash = $explrestunits.$lastthree.".".$number[1];
        }
        else{
            $thecash = $explrestunits.$lastthree;
        }
    } else {
        $thecash = $num;
    }
    return $thecash; // writes the final format where $currency is the currency symbol.
}

?>
<script>   
    $(document).ready(function () {
           $('#btn').click(function () {
               window.opener.location.reload(true);
               window.close();
           });
       });
</script>
<script>
function showamt(str) {
   // alert(str);
  if (str=="") {
    document.getElementById("base").value="0";
    return;
  }
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else {  // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
      document.getElementById("base").value=this.value;
    }
  }
  //var a = "../client/sales/order-details/get_amount.php?q="+str;
 var a = "index.php?option=get_amount.php&q="+str;
  //alert(a);
  xmlhttp.open("GET",a,true);
  xmlhttp.send();
}
</script>

  <div id="breadcumb"><a href="#">Primary Stock Details</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma;?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
<?php //if(!(isset($_GET['showmode']) && $_GET['showmode'] == 1)) require_once('breadcum/party.php');  ?>
 </div>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view
// include "table.php";
############################# code for checking of submitted form data starts here data starts here ########################
function checkform($mode='add', $id='')
{
  global $dbc;
  if($mode == 'filter') 
  return array(TRUE, '');
  /*$field_arry = array('partyname' => $_POST['partyname']);// checking for  duplicate Unit Name
  if($mode == 'add')
  {
    if(uniqcheck_msg($dbc,$field_arry,'party', false, " ptype=1"))
      return array(FALSE, '<b>Vendor</b> already exists, please provide a different value.');
  }
  elseif($mode == 'edit')
  {
    if(uniqcheck_msg($dbc,$field_arry,'party', false," partyId != '$_GET[id]' AND ptype = 1"))
      return array(FALSE, '<b>Vendor</b> already exists, please provide a different value.');
  }*/
  return array(TRUE, '');
}

############################# code for SAVING data starts here ########################
if(isset($_POST['submit']) && $_POST['submit'] == 'Save')
{
  if(valid_token($_POST['hf'])) // checking if post value is same as timestamp stored in session during form load
  {
    //calculating the user authorisastion for the operation performed, function is defined in common_function
    list($checkpass, $fmsg) = user_auth_msg($auth['add_opt'], $operation = 'add', $id = '');    
    if($checkpass)
    {
      // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
      magic_quotes_check($dbc, $check=true);
      $funcname = $cls_func_str.'_save'; 
                    
      $action_status =  $myobj->$funcname(); // $myobj->item_category_save()
      if($action_status['status'])
      {
        echo '<span class="asm">'.$action_status['myreason'].'</span>';
        //show_row_change(BASE_URL_A.'?option='.$formaction, $action_status['rId']);
        unset($_POST);
	?> <script>
                    setTimeout("window.parent.location = 'index.php?option=demand-order'", 500);
                    //window.open("index.php?option=dsp-challan-list&showmode=1&id=<?php echo $_SESSION['chalan_id']; ?>&dealer_id=<?php echo $_SESSION['chalan_dealer_id'] ?>&actiontype=print","_blank");
                </script>
                <?php
        /*echo'<script type="text/javascript">ajax_refresher(\'vendorId\', \'getvendor\', \'\');</script>';*/
        //unset($_SESSION[SESS.'securetoken']);     
      }
      else
        echo '<span class="awm">'.$action_status['myreason'].'</span>';
    }
    else
      echo'<span class="awm">'.$fmsg.'</span>';
  }
  else
    echo'<span class="awm">Please do not try to hack the system.</span>';
}

############################# code for editing starts here ########################
if(isset($_POST['submit']) && $_POST['submit'] == 'Update')
{
    //echo "manisha";
  if(valid_token($_POST['hf'])) // checking if post value is same as timestamp stored in session during form load
  {
    //calculating the user authorisastion for the operation performed, function is defined in common_function
    list($checkpass, $fmsg) = user_auth_msg($auth['add_opt'], $operation = 'edit', $id = $_POST['eid']);    
    if($checkpass)
    {
      // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
      magic_quotes_check($dbc, $check=true);
      $funcname = $cls_func_str.'_edit';
      $action_status = $myobj->$funcname($_POST['eid']); // $myobj->item_category_edit()
      if($action_status['status'])
      {
        echo '<span class="asm">'.$action_status['myreason'].'</span>';       
        //unset($_SESSION[SESS.'securetoken']); 
        //show_row_change(BASE_URL_A.'?option='.$formaction, $_POST['eid']);
        unset($_POST);
      ?> <script>
                    setTimeout("window.parent.location = 'index.php?option=demand-order'", 500);
                    //window.open("index.php?option=dsp-challan-list&showmode=1&id=<?php echo $_SESSION['chalan_id']; ?>&dealer_id=<?php echo $_SESSION['chalan_dealer_id'] ?>&actiontype=print","_blank");
                </script>
      <?php
        }
    else
        echo '<span class="awm">'.$action_status['myreason'].'</span>';
    }
    else
      echo'<span class="awm">'.$fmsg.'</span>';
  }
  else
    echo'<span class="awm">Please do not try to hack the system.</span>';
}

############################# code to get the stored info for editing starts here ########################
if(isset($_GET['mode']) && $_GET['mode'] == 1)
{
  if(isset($_GET['id']) && is_numeric($_GET['id']))
  {
    $id = $_GET['id'];
    //This will containt the pr no, pr date and other values
    $funcname = 'get_'.$cls_func_str.'_list';
    $mystat = $myobj->$funcname($filter="$myfilter'$id'",  $records = '', $orderby=''); // $myobj->get_item_category_list()
                //pre($mystat);
    if(!empty($mystat))
    {
      //geteditvalue_class($eid=$id, $in = $mystat, $labelchange=array(), $options)
      geteditvalue_class($eid=$id, $in = $mystat);
      //This will create the post multidimensional array
      //create_multi_post($mystat[$id]['pr_item'], array('itemId'=>'itemId', 'qty'=>'qty'));
      $heid = '<input type="hidden" name="eid" value="'.$id.'" />';
    }
    else
      echo '<span class="awm">Sorry, no such '.$forma.' found.</span>';
  }                  
}
############################# Code to handle the user search starts here ###############################
$rs = array();
$filterused = '';
$funcname = 'get_'.$cls_func_str.'_list';
$mymatch['datepref'] = array('podate'=>'PO Date', 'created'=>'Created');
if(isset($_POST['filter']) && $_POST['filter'] == 'Filter')
{
  if(valid_token($_POST['hf'])) // checking if post value is same as timestamp stored in session during form load
  {
    //calculating the user authorisastion for the operation performed, function is defined in common_function
    list($checkpass, $fmsg) = checkform('filter');  
    if($checkpass)
    {
      // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
      magic_quotes_check($dbc, $check=true);
      $filter = array();
      $filterstr = array();
      if(!empty($_POST['from_date'])){
        $start = get_mysql_date($_POST['from_date'],'/',$time = false, $mysqlsearch = true);
        $filter[] = "DATE_FORMAT(`order_date`,'".MYSQL_DATE_SEARCH."') >= '$start'";
        $filterstr[] = '<b>Start : </b>'.$_POST['from_date'];
      }
      if(!empty($_POST['to_date'])){
        $end = get_mysql_date($_POST['to_date'],'/',$time = false, $mysqlsearch = true);
        $filter[] = "DATE_FORMAT(`order_date`,'".MYSQL_DATE_SEARCH."') <= '$end'";
        $filterstr[] = '<b>End : </b>'.$_POST['to_date'];
                                 
      }
                        if(!empty($_POST['order_no'])){
        $filter[] = "order_id = '$_POST[order_no]'";
        $filterstr[] = '<b>Order No  : </b>'.$_POST['order_no'];
      }
                       
                         //pre($user_data);
//                         if(!empty($user_data)){
//                            $user_data_str = implode(',' , $user_data);
//                            $filter[] = "created_person_id IN ($user_data_str)";
//                            
//                        }
                        $filter[] = "dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
      $filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');      
        $rs = $myobj->$funcname($filter,  $records = '', $orderby ="ORDER BY $myorderby"); // $myobj->get_item_category_list()
      if(empty($rs))
        echo '<span class="awm">Sorry, <strong>no record</strong> found.</span>';
    }
    else
      echo'<span class="awm">'.$fmsg.'</span>';
  }
  else
    echo'<span class="awm">Please do not try to hack the system.</span>';
}elseif(isset($_GET['ajaxshow']) || isset($_GET['ajaxshowblank'])){
  $ajaxshowid = isset($_GET['ajaxshow']) ? $_GET['ajaxshow'] : $_GET['ajaxshowblank'];
  $rs = $myobj->$funcname($filter="$myfilter'$ajaxshowid'",  $records = '', $orderby='');
}
else {
           // $user_data_str = implode(',' , $user_data);
            $filter = "dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
            
            $rs = $myobj->$funcname($filter,  $records = '', $orderby ="ORDER BY $myorderby");
}
dynamic_js_enhancement();
?>

<div id="workarea">
    <?php
//This block of code will help in the print work
if (isset($_GET['actiontype'])) {
    switch ($_GET['actiontype']) {
        case'print':
           // echo "hiii"; die;
            require_once('demand_order_print.php');
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
 <?php if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ // to show the form when and only when needed?>
 <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>"  name="genform" onsubmit="return checkForm_alert('genform');">
      <fieldset>
        <legend class="legend" style="background-color: #438eb9;font-size: 100%;font-family: Arial, Georgia, Serif; color:white;"><?php echo $forma; ?></legend>
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />  
        <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
            <tr>
                <td><strong>Focus Product</strong><br/>
       <img src="./images/green.jpeg" style="width:25px; height: 25px"/>
           
                    <input type="hidden" name="company_id" value="1">
                </td>

              
                <td><strong>Order date</strong><br>
                    <input style="width:200px;height:25px;" type="text" id="podate" name="receive_date"  value="<?php if(isset($_POST['order_date'])) echo $_POST['order_date']; else echo date('d/m/Y'); ?>"  class="qdatepicker" />
                </td>

                 <td><strong>Depot/C&F Name</strong><br>
                     <?php
                   $q = 'SELECT c_id, csa_name FROM csa where c_id IN('.$_SESSION[SESS.'data']['csa_id'].')';
        db_pulldownstart($dbc, 'csa_id', $q, TRUE, TRUE,'style="width:200px;height:25px;"','','');
        ?>
                  </td>
				  <td>
					<strong>Total Amount</strong><br>
					<input type="text" name="tot_amt" readonly="" class="tot_amt">
					</td>
                <div class="col-xs-2">
                    <input type="hidden" class="order_id"  name="order_no" class="order" value="<?php if(isset($_POST['order_id'])) echo $_POST['order_id'];?>" /> 
               </div>
            <td>
                <?php
                $date = date('Y-m-d');
        $q11 = "SELECT svp.id,svp.value,svp.value_to,svp.scheme_gift,svpd.start_date,svpd.end_date from scheme_value_product_details svp INNER JOIN scheme_value svpd ON svpd.scheme_id = svp.scheme_id where '$date' BETWEEN svpd.start_date AND svpd.end_date AND user = 2 AND state_id = $stateid";
       // echo $q11;
        $r11 = mysqli_query($dbc, $q11);
        while ($row11 = mysqli_fetch_array($r11)) {
          $start = $row11['start_date'];
            $end = $row11['end_date'];
        }
        $dealerId = $_SESSION[SESS.'data']['dealer_id'];
        $q= "SELECT SUM(rate*(quantity)) as achieved FROM `user_primary_sales_order` upso INNER JOIN user_primary_sales_order_details upsod ON upsod.order_id = upso.order_id where is_claim=0 AND action=1 AND ch_date >='$start' AND ch_date<='$end' AND dealer_id = $dealerId";
       // h1($q);
        $r = mysqli_query($dbc,$q);
        $row = mysqli_fetch_assoc($r);
        $unit = $row['achieved'];
       // $u = 212134.56;
//       setlocale(LC_MONETARY, 'en_IN');
//      $price = money_format("%.2n", $unit);
        //$price = IND_money_format($unit);
       //    $price = number_format($unit, 2, '.', ',');
        $amount = moneyFormatIndia($unit);
//    setlocale(LC_MONETARY, 'en_IN.UTF-8');
//echo "<br/>".money_format('%n', 211234.56);
       // echo "<strong>Achieved On Purchase : <br><span style='background-color:#F89406; font-size:20px;'>₹ ".$amount."</span></strong>";
                ?>
                </td></tr>
        <tr><td> </td></tr>
            
<tr>
     <td colspan="5"><div style="background-color: #438eb9;font-size: 10%;font-family: Arial, Georgia, Serif;"><br/></div></td>
         </tr>
         <tr>
           <td colspan="5">
             <!-- table to capture the address field starts -->
             <div id="progdiv"></div>
             <div id="ss">
            
             <table width="100%"  id="mytable">
               <tr class="thead" style="font-weight:bold;">
                 <td>S.No</td>
                 <td>&nbsp;&nbsp;Product</td>
                
<!--                 <td>Batch No</td>-->
                 <td> &nbsp;MRP</td>
<!--                 <td>Sale Rate</td>-->
                 <td> &nbsp;Rate</td>
					<td>Qty.(in Pieces)</td>
                <!-- <td> &nbsp;Select Type</td>-->
                  <?php if(!isset($heid)){ ?>
                  <td> &nbsp;Stock In Hand</td><?php } ?>
<!--                 <td> &nbsp;Mfg Date.<br><span class="example">(dd/mm/yyyy)</span></td>
                 <td> &nbsp;Expire Date<br><span class="example">(dd/mm/yyyy)</span></td>-->
                 <td style="width:40px;">&nbsp;</td>
               </tr>
          <?php if(!isset($heid)){ ?>
                 <!------------------------------------FOCUS PRODUCT----------------------------------->
                <?php 
               // pre($_SESSION);
               
                $count1 = 0; /*$dealer_landing_price = 0;*/
                 $query = "SELECT focus.product_id,name,cprl.mrp as rate,cprl.product_id as pid FROM `focus` 
                     INNER JOIN catalog_product ON catalog_product.id = focus.product_id
                     INNER JOIN product_rate_list cprl ON cprl.product_id = focus.product_id 
                     WHERE state_id ='$stateid' GROUP BY focus.product_id";
                // h1($query);
                 $rr = mysqli_query($dbc,$query);
                 while($row1 = mysqli_fetch_assoc($rr))
                 {
                     $count1 = $count1+1;
               //     pre($row);$primary_sale->get_dealer_landing_rate($row1['pid']);
                  /*$dealer_landing_price = $primary_sale->get_dealer_landing_rate($row1['pid']);*/
                    
                    $product_id1 = $row1['product_id'];
                    $product_name = $row1['name'];
                    
                    $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
                    $reobj = new report();
                   // $getstock = $reobj->get_available_stock($product_id1,$dealer_id);
                    $where = "dealer_id=$dealer_id AND product_id=$product_id1 AND mrp=$row1[rate]";
                    $getstock = myrowval('stock','qty',$where);
                  // h1($getstock);
                    
                    ?>
                 
                   <tr class="tdata" bgcolor="#32c403">
                 <td class="myintrow"><?=$count1?></td>
                 <td>
  <input type="hidden" name="product_id[]" id="product_id" value="<?=$product_id1?>">
  <strong><?=$product_name?></strong>
                     <?php 
                     //$cp = mysqli_query($dbc, "SELECT id,name FROM catalog_product WHERE id='$product_id1'");
                     
   //db_pulldown($dbc , 'product_id[]', "SELECT id,name FROM catalog_product ",TRUE,TRUE,' id ="product_id" onchange=getajaxdata(\'get_mrp_product\',\'mytable\',event);', '', $row1['product_id']); 
   
  ?>
               
                 </td>
                  
                <!--                 <td>
                     <input type="text" name="batch_no[]"  value=""  />
                 </td>-->
                
                  <td>
                      <input type="text" style="width:100px" placeholder="MRP" name="base_price[]"  value="<?=$row1['rate']?>"  />
                 </td>
           
                 <td>
                     <?php
                      /*$pod  = mysqli_query($dbc,"SELECT avg(cases) as cases FROM  `purchase_order_details` INNER JOIN purchase_order USING(order_id) WHERE product_id='$product_id1' AND dealer_id='$dealer_id' ");

           
                     
                     while ($rowp = mysqli_fetch_assoc($pod)) {
                      // $ip = $ip+1;
                       $cases = round($rowp['cases']);
                     //  h1($cases);
                      // $case = $cases/$ip;
                     }*/
                     ?>
                     <input type="text" class="nkset_dealer_rate" style="width:100px" placeholder="Landing Price" name="dealer_rate[]"  
                     value="<?php echo $primary_sale->get_dealer_landing_rate($row1['pid']); ?>" readonly/>
                 </td>
                  <td>
                      <input type="text" style="width:120px" placeholder="Quantity" name="quantity[]"  placeholder="In Pieces" class="qty"  value=""/>
                  </td>
 <!--                  <td>  
                <input type="text" name="cases[]"  value=""  />
                      <select name="cp[]" class="form-control">
                          <option value="2">Cases</option> 
                      </select>
                 </td>-->
                  <td>
                     <center> <strong><?=$getstock                                                  ?></strong></center>
                 <input type="hidden" style="width:100px" placeholder="Batch" name="purchase_inv[]"  value=""  />
                 </td>
<!--                  <td>
                      <input type="text" style="width:100px" class="datepicker"  name="mfg_date[]"  value=""  />
                 </td>
                  <td>
                      <input type="text" style="width:100px" class="datepicker"  name="expiry_date[]"  value=""  />
                 </td>-->
                 <td><img title="less" src="images/less.png" onclick="javascript:addmore22('mytable', event);"></td>
	
               </tr>    
                <?php 
                 }
                 ?>               
               
                <?php   
                 // $query = "SELECT sum(remaining) as remain,product_id,rate FROM `stock` group BY product_id";
                 // $rr = mysqli_query($dbc,$query);
                 // while($row = mysqli_fetch_assoc($rr))
                 // {
                     
                 //    $product_id = $row['product_id'];
                 //    $qs = "SELECT `qty` FROM `threshold` where product_id=$product_id AND dealer_id = $dealer_id";
                 
                 //    $rss = mysqli_query($dbc,$qs);
                 //   $rows = mysqli_fetch_assoc($rss);
                 //   $qtyy = $rows['qty'];
                   
                 //   if($row['remain'] <=$qtyy)
                 //   {
                 //       $count1 = $count1+1;
                     ?>
                 
                   <!-- <tr class="tdata">
                 <td class="myintrow"><?=$count1?></td>
                 <td> -->
  <?php 
   //db_pulldown($dbc , 'product_id[]', "SELECT id,name FROM catalog_product ",TRUE,TRUE,' id ="product_id" onchange=getajaxdata(\'get_mrp_product\',\'mytable\',event);', '', $row['product_id']); 
   ?>
               
                 <!-- </td>                
                  <td>
                      <input type="text" style="width:100px" placeholder="MRP" name="base_price[]"  value="<?=$row['rate']?>"  />
                 </td>
           
                 <td>
                     <input type="text" style="width:120px" placeholder="Quantity" name="quantity[]"  placeholder="In Pieces" onblur="qty_scheme_calculate();" value=""  />
                 </td>
<td>  </td>
                  <td>
                      <select name="cp[]" class="form-control">
                         Select Type	 Stock In Hand
                           <option value="2">Cases</option> 
                      </select>
                 </td>
                  <td>
                 <input type="hidden" style="width:100px" placeholder="Batch" name="purchase_inv[]"  value=""  />
                 </td>
                 <td><img title="less" src="images/less.png" onclick="javascript:addmore22('mytable', event);"></td>
	
               </tr>  -->   
                <?php //}  }  ?>
             <tr class="tdata">
                 <td class="myintrow"><?=$count1 = $count1+1;?></td>
                 <td>
               
 <?php 
                db_pulldown($dbc , 'product_id[]', "SELECT catalog_product.id,catalog_product.name FROM catalog_product INNER JOIN catalog_2 ON catalog_product.catalog_id = catalog_2.id WHERE catalog_product.company_id = 1 order by name",TRUE,TRUE,' id="pproduct_id" class="nkget_dealer_rate"');
                      ?>
                
                 </td>
                <!--                 <td>
                     <input type="text" name="batch_no[]"  value=""  />
                 </td>-->
                  <td>
                      <input type="text" class="nkget_dealer_mrp_rate" style="width:100px" placeholder="MRP" name="base_price[]"  value=""  readonly/>
                 </td>          
                 
                 
                   <td>
                    <input type="text" class="nkset_dealer_rate" style="width:100px" placeholder="Landing Price" name="dealer_rate[]"  value="" readonly/>

                    <?php /* <input type="hidden" style="width:120px" id="scheme_quantity" placeholder="scheme quantity" name="scheme_quantity[]"  placeholder="In Scheme Pieces"  value=""  />*/?>
                 </td>
                 <td>
                     <input type="text" style="width:120px" id="quantity" placeholder="Quantity" name="quantity[]" onkeypress="javascript:return isNumber(event)" class="qty" placeholder="In Pieces"  value=""  />
                 </td>

                 <!--<td>
                     <input type="text" style="width:100px" name="quantity[]" lang="quantity"  value="<?php echo $invalue['quantity']; ?>"  />
                 </td>-->
  <!--                 <td>
                <input type="text" name="cases[]"  value=""  />
                       <select name="cp[]" class="form-control" >
                          
                           <option value="2">Cases</option> 
                      </select>
                 </td>-->
                  <td>
                 <input type="hidden" style="width:100px" placeholder="Batch" name="purchase_inv[]"  value=""  />
                 </td>
<!--                  <td>
                      <input type="text" style="width:100px" class="datepicker"  name="mfg_date[]"  value=""  />
                 </td>
                  <td>
                      <input type="text" style="width:100px" class="datepicker"  name="expiry_date[]"  value=""  />
                 </td>-->
                 <td class="lasttd"><img title="more" src="images/more.png" onclick="javascript:addmore22('mytable', event,'');"/>
				 <img title="less" src="images/less.png" onclick="javascript:addmore22('mytable', event);"></td>
               </tr>
          <?php }else{ 
              $inc2 = 1;
              //pre($_POST['order_item']);
              foreach ($_POST['order_item'] as $inkey=>$invalue) { 
          //        print_r($invalue);
                  ?>
               
               
                <tr class="tdata row1">
                 <td class="myintrow"><?=$inc2?></td>
                 <td>
                   <?php 
                      db_pulldown($dbc , 'product_id[]', "SELECT id,name FROM catalog_product ",TRUE,TRUE,' id ="product_id" class="nkget_dealer_rate"', '', $invalue['product_id']); ?>
                 </td>

              <td>
                 <input type="text" style="width:100px" placeholder="MRP" class="nkget_dealer_mrp_rate" name="base_price[]"  value="<?php echo $invalue['mrp']; ?>"  />
                 </td>
              <td>
                  <input type="text" class="nkset_dealer_rate" style="width:100px" placeholder="Landing Price" name="dealer_rate[]"  
                   value="<?php echo $invalue['rate']; ?>" readonly/>
              </td>   
              <td>
                     <input type="text" style="width:120px" class="nkset_qty" id="quantity" placeholder="Quantity" name="quantity[]" onblur="qty_scheme_calculate();" placeholder="In Pieces"  value="<?php echo $invalue['cases']; ?>"  />
                 </td>
                 
                 <?php /*  <td>
                     <input type="hidden" style="width:120px" id="scheme_quantity" placeholder="scheme quantity" name="scheme_quantity[]"  placeholder="In Scheme Pieces"  value=""  />
                 </td>*/?>

                 <!--<td>
                     <input type="text" style="width:100px" name="quantity[]" lang="quantity"  value="<?php echo $invalue['quantity']; ?>"  />
                 </td>
                  <td>
                  <select name="cp[]" class="form-control" >
                          
                           <option value="2">Cases</option> 
                      </select>
                 </td>-->
                  <td>
                     <input type="hidden" style="width:100px" name="purchase_inv[]"  value="<?php echo $invalue['batch_no']; ?>"  />
                 </td>
                  <td class="lasttd"><img title="more" src="images/more.png" onclick="javascript:addmore22_2('mytable', event,'');"/>
				 <img title="less" src="images/less.png" onclick="javascript:addmore22('mytable', event);"></td>
                <!-- <td><img  title="more" src="images/more.png" onclick="javascript:addmore22_2('mytable', event,'');"/> <img title="less" src="images/less.png" onclick="javascript:addmore22_2('mytable', event);"></td>-->
               </tr>
               
          <?php $inc2++; } 
          } ?>
               <tr><td colspan="9">&nbsp;</td></tr>
             </table>
             </div>
             <!-- table to capture the address field ends -->
           </td>
         </tr>
         <tr>
             <td colspan="4" align="center">
                  <?php //form_buttons(); // All the form control button, defined in common_function?>
            <input style="width:100px;height:30px;background-color: #438eb9" class="btn btn-sm btn-primary" id="mysave" type="submit" name="submit" value="<?php if(isset($heid)) echo'Update'; else echo'Save';?>" />
      <?php if(isset($heid)){ echo $heid; //A hidden field name eid, whose value will be equal to the edit id.?>
            <input style="width:100px;height:30px; background-color: #87B87F"  onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>&showmode=1';" type="button" value="New" title="add new <?php echo $forma;?>" />  
            <input style="width:100px;height:30px;" onclick="parent.$.fn.colorbox.close();" type="button" value="Exit" /> <br />
            <?php edit_links_via_js($id, $jsclose=true, $options=array());?>            
      <?php }else{?>
            <input style="width:100px;height:30px; background-color:red;" onclick="parent.$.fn.colorbox.close();" type="button" value="Close" />
            <?php }?>
             </td>
         </tr>
        </table>
      </fieldset>
 </form>
 <?php }
  else{//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here ?>
  <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" name="genform1" onsubmit="return checkForm('genform1');">
       <table width="100%" border="0" cellspacing="2" cellpadding="2">
         <tr id="mysearchfilter">
           <td>
             <!-- this table will contain our form filter code starts -->
        <fieldset>
<!--               <legend class="legend">Search <?php echo $forma;?></legend>-->
               <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
             <table>
               <tr>
               <div class="col-xs-2">Order No<br />
                    <input type="text"  class="order_id"  name="order_no" class="order" value="<?php if(isset($_POST['order_id'])) echo $_POST['order_id'];?>" /> 
               </div>
               <div class="col-xs-2">From Date<br />
                    <input type="text" name="from_date" id="fdate" class="datepicker" value="<?php if(isset($_POST['from_date'])) echo $_POST['from_date']; else  echo date('d/M/Y');?>" />
                </div>
               <div class="col-xs-2">To Date<br />
                     <input type="text" id="tdate" name="to_date" class="datepicker" value="<?php if(isset($_POST['to_date'])) echo $_POST['to_date']; else echo date('d/M/Y');?>" />
                </div>                
                 <div class="col-xs-6">
                     <br/>
                  <input id="mysave" class="btn btn-sm btn-primary" type="submit" name="filter" value="Filter" />
                <!--  <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />-->
<!--                  <input class="btn btn-success"  onclick="$.colorbox({href:'index.php?option=<?php echo $formaction; ?>&showmode=1', iframe:true, width:'95%', height:'95%'});" type="button" value="New" title="add new <?php echo $formaction; ?>" />-->
               <a class="iframef" target="_blanck" href="index.php?option=<?php echo $formaction; ?>&showmode=1&mode=1"> <input class="btn btn-sm btn-success" type="button" value="New" title="add new <?php echo $formaction; ?>" />           
                  
           </div>
              </tr>
             </table>
             </fieldset>
            <!-- this table will contain our form filter code ends -->           
           </td>
         </tr>
  <?php
    if(isset($_GET['ajaxshowblank'])) ob_end_clean(); // to show the first row when parent table not avialable
    if(!empty($rs)){ //if no content available present no need to show the bottom part
       
    ?>
        </table>
       
      </fieldset>
      </form>
       
   <script type="text/javascript">
      function do_delete()
      {
            if (confirm("Delete Account?"))
                 location.href='linktoaccountdeletion';
      }
  
    //do_delete(\'Sale Delete\', \''.$uid.'\',\'Sale Order\',\''.addslashes($uidname).'\')
    </script> 

<div class="row">
    <div class="col-xs-12">
        <!-- PAGE CONTENT BEGINS -->

        <div class="row">
            <div class="col-xs-12">
              
                <br>
                                   
                <div class="table-header">
                  Purchase Order Details<div class="pull-right tableTools-container"></div> 
                   
                </div>

                <!-- div.table-responsive -->
<?php
//pre($rs);
?>
                <!-- div.dataTables_borderWrap -->
                <div>
                    <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                        <thead>
                   <tr>
                      <th style="background-color:#C7CDC8; color:#000;" rowspan="2" class="sno">S.No</th>
                      <th style="background-color:#C7CDC8; color:#000;" rowspan="2">Date</th>
                   <!-- <th  style="background-color:#C7CDC8; color:#000;" rowspan="2">Purchase Id</th> -->
                      <th style="background-color:#C7CDC8; color:#000;" rowspan="2" >Depot/C&F Name</th>
                      <th style="background-color:#C7CDC8; color:#000; text-align:center; width: 65%;" colspan="5">Order Details</th>
                      <th style="background-color:#C7CDC8; color:#000;" rowspan="2" width="75px">Options</th>
                    </tr>
                     <tr class="search1tr">
                        <th style="background-color:#307ECC; color:#fff; width:150px; text-align:center;"  >Product Name</th>
                        <th style="background-color:#307ECC; color:#fff; width: 120px; text-align:center;">Rate</th>
                        <th style="background-color:#307ECC; color:#fff; width: 120px; text-align:center;">Quantity(In Pcs.)</th>
<!--                         <th style="background-color:#307ECC; color:#fff; width: 120px; text-align:center;">Sch.Qty. (In Pcs.)</th>-->
                  <!--      <th style="background-color:#307ECC; color:#fff; width: 120px; text-align:center;">Quantity (In Case)</th>-->
                        <th style="background-color:#307ECC; color:#fff; width: 150px; text-align:center;">Total Purchase Value</th>
                        
                    </tr>
                    </thead>
                    <tbody>
                     <style>tr.bordered {border-bottom: 1px solid #000; width:65%; text-align:center;}</style>
                  <?php 
                 $bg = TR_ROW_COLOR1;
                  $inc = 1;
                  //pre($rs);20
                  if(isset($_GET['ajaxshow'])) ob_end_clean(); // to help refresh a single row
                  foreach($rs as $key=>$rows)
                  {
                      $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $rows['id'];
                      $uidname = $rows['order_id'];
                      $rorder = $rows['ch_date'];
                      if($rorder=='1970-01-01' || empty($rorder)){
$editlink = '<a class="iframef" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'"><img src="./images/b_edit.png"></a>&nbsp;&nbsp;';
$receive = '<a class="iframef" href="index.php?option=received-order&showmode=1&mode=1&id='.$uid.'"><button class="btn btn-sm btn-primary">Receive</button></a>&nbsp;&nbsp;';
                      }
                      else {
                          $receive ='<button class="btn btn-sm btn-default">Received</button>';
                          $editlink = '<img src="./images/b_edit.png">';

                      }
$printlink = '<span class="seperator">|</span> <a class="iframef" title="print Invoice ' . $uidname . '" href="index.php?option=' . $formaction . '&showmode=1&mode=1&id=' . $uid . '&actiontype=print"><img src="../icon-system/i16X16/print.png"></a>'; //$rows['postat'] == 1 ? '<span class="seperator">|</span> <a class="iframef" title="print PO '.$uidname.'" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'&actiontype=print"><img src="../icon-system/i16X16/print.png"></a>' : '';
                       echo'
                      <tr  BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
                        <td style="display: table-cell; vertical-align: top; width:10px" class="myintrow myresultrow">'.$inc.'.&nbsp;&nbsp;'.'</td>
                        <td style=" display: table-cell; vertical-align: top;" >'.$rows['sale_date'].'</td>
                   
                       <!-- <td  style=" display: table-cell; vertical-align: top;">'.$rows['id'].'</td> -->
                            <td style=" display: table-cell; vertical-align: top;">'.$rows['csa_name'].'</td>
                        <td  colspan="5">';
                        echo'
                        <table style="width:100%;">';
                   //pre($rows['order_item']);   
                    $count_value = count($rows['order_item']);
                  
                    $total_sale_value = 0;
                    if(!empty($rows['order_item']))
                     {
                        $total_sales_value_array = array(0);
                        foreach($rows['order_item'] as $inkey=>$invalue){
                         //   pre($rows['order_item']);
                            //$total_sale_value = $myobj->get_sale_value($invalue['catalog_1_id'],$invalue['metric_ton']);
                            $total_sale_value=$invalue['rate']*$invalue['quantity'];
                        echo'
                            <style>tr.bordered {border-bottom: 1px solid #000;}</style>
                        <tr class="bordered">
                          <td style="border:none; width:150px;" >'.$invalue['name'].' '.$invalue['batch_no'].'</td>
                          <td style="border:none;width:120px;text-align:center" >'.$invalue['rate'].'</td>
                          <td style="border:none;width:120px;text-align:center">'.$invalue['quantity'].'</td>
<!--                              <td style="border:none;width:120px;text-align:center">'.$invalue['scheme_qty'].'</td>-->
             <!--            <td style="border:none;width:120px;text-align:center">'.$invalue['cases'].'</td>-->
                          <td style="border:none;width:120px;text-align:center">₹ '.my2digit($total_sale_value).'</td>
                         
                        </tr>'; 
                       $total_sales_value_array[]= $total_sale_value;

                        $grand_total_value[] =  $total_sale_value;  
                             }
                     }
                    else {
                        echo'
                        <tr>
                          <td style="border:none; width:180px;">-</td>
                          <td style="border:none;width:150px;">-</td>
                       <!--   <td style="border:none;width:150px;">-</td>
                          <td style="border:none;width:150px;">-</td>-->
                          <td style="border:none;width:150px;">-</td>
                          <td style="border:none;width:150px;">-</td>
                        </tr>'; 
                    }
                    echo'
                        <tr bgcolor="#307ECC">
                          <td style="border:none; width:120px;color:#fff;text-align:center;"><b>Total</b></td>
                          <td style="border:none;width:120px;">-</td>
                         <!-- <td style="border:none;width:120px;">-</td>
                          <td style="border:none;width:120px;">-</td>-->
                          <td style="border:none;width:120px;">-</td>
                          <td style="border:none;width:120px;color:#fff;text-align:center;"><b>'.my2digit(array_sum($total_sales_value_array)).'</b></td>
                        </tr>';

                 echo'</table>';
                     echo'</td>
                       <td style=" display: table-cell; vertical-align: top;" class="options">'.$editlink.$printlink.'</td>
                      </tr>
                      ';
                      $inc++;
                  }// foreach loop ends here
                    if(isset($_GET['ajaxshow'])) exit(); // to help refresh a single row

//                    echo '<tr class="ihighlight" class="green">
//                        <td style=" display: table-cell; vertical-align: top;" class="myintrow myresultrow"></td>
//                        <td style=" display: table-cell; vertical-align: top;"></td>
//                        <td style=" display: table-cell; vertical-align: top;"></td>
//                        <td style=" display: table-cell; vertical-align: top;"></td>
//                        <td style=" display: table-cell; vertical-align: top;"><b>Grand Total</b></td>
//                        <td colspan="5">
//                        <table>
//                        <tbody>
//                        <tr class="blue">
//                          <td style="border:none; width:150px;"><b></b></td>
//                        
//                          <td style="border:none;width:120px;">-</td>
//                            <td style="border:none;width:120px;">-</td>
//                          <td style="border:none;width:120px;">-</td>
//                          <td style="border:none;width:120px;"><b>'.my2digit(array_sum($grand_total_value)).'</b></td>
//                        </tr></tbody></table></td>
//                       <td style=" display: table-cell; vertical-align: top;" class="options"></td>
//                      </tr>';
                  ?>

                 </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- PAGE CONTENT ENDS -->

    </div><!-- /.row -->
</div><!-- /.page-content -->
              <?php echo'</div>';} // foreach($pgoutput['temp_result'] as $key=>$value){ ends?>           
                   <?php } //if(!empty($rs)){?>
          <?php if(isset($_GET['ajaxshowblank'])) exit(); // to show the first row when parent table not avialable
         
          ?>
       
      </div><!-- workarea div ends here -->
     <?php  
 function qtyscheme($qty,$product){
        global $dbc;
      //  $dbc11 = @mysqli_connect('localhost','root','root','msell-dsgroup-dms') OR die ('could not connect:' .mysqli_connect_error());

        $q3 =mysqli_query($dbc,"select `scheme_quantity` from `scheme_product_details` where product_id='$product'");                                
       // $q3 = mysqli_query($dbc11,$query3);
        //echo $q3;
       // $co=  mysqli_num_rows($q3);
      //  echo $co;
        $row3 = mysqli_fetch_assoc($q3); 
        
        $scheme = $row3['scheme_quantity'];
       // echo $scheme.$query3;
        return $scheme;                                                  
                               
}
                
                          ?>

<script>   
           function qty_scheme_calculate()
            { 
                
                
                var qty = document.getElementsByName('quantity[]');
                var scheme_quantity = document.getElementsByName('scheme_quantity[]');
                var product_id = document.getElementsByName('product_id[]');
                //alert(qty.length);
                 for (var i = 0; i < qty.length; i++)
                {
                    
                       var qty_val = qty[i].value;
                       var product = product_id[i].value;                      
			if(qty_val.length>=1){
			var d = qty_val.length-1; 
                       <?php 
                       $qtyphp='"+qty_val+"';
                       $product_id='"+product+"';
                       ?>
                             var qtysch= "<?php qtyscheme($qtyphp,$product_id)?>";
                       // alert(qtysch);
				if(qty_val>=qtysch){ 
                                    
					var sch_val = qty_val.substring(0, d);
                                   
					scheme_quantity[i].value = sch_val;
				}else{
					//console.log(qty_val);
					scheme_quantity[i].value=0;
				}
			}


                }    
                
               

            }
        </script>
		<script>
		$(document).ready(function()
		{
			
		function addnewrow1()
{
var table = $('#mytable');
var tb = table.find('input[name="base_price[]"]').length;
var i = tb+1;

var $c_row = $('.row1').clone();
$c_row.removeClass('row1');
$c_row.find('.myintrow').html(i);
$c_row.find('input').val('');
$c_row.find('.lasttd').append('<img title="less" src="images/less.png" class="removenewrow">');

$c_row.find('.nkget_dealer_rate').removeClass('chzn-done').css({'display':'block'}).removeAttr('id').val('').next('div').remove();
$c_row.find('.nkget_dealer_rate').attr('id','pproduct_id'+i);

table.find('.addhere').before($c_row);
$('#pproduct_id'+i+'').chosen();
}

$('#mytable').on('click','.removenewrow',function(){

var table = $(this).closest('table');
var i = table.find('.item_details').length;

if(i==1)
{
return false;
}

$(this).closest('tr').remove();
})
		
		$('#mytable').on('keyup','.qty',function(){

var tot_amt = 0;
$('#mytable .tdata').each(function(){
var l_price = $(this).find('.nkset_dealer_rate').val();
var qty = $(this).find('.qty').val();

if(l_price>0 && qty>0)
{
tot_amt = parseFloat(tot_amt)+parseFloat(l_price*qty);
}
})
$('.tot_amt').val(tot_amt.toFixed(2));

})	
		})
		
		</script>
<!--  <script src="assets/js/jquery-2.1.4.min.js"></script>-->
 <script src="assets/js/jquery.dataTables.min.js"></script>
        <script src="assets/js/jquery.dataTables.bootstrap.min.js"></script>
        <script src="assets/js/dataTables.buttons.min.js"></script>
        <script src="assets/js/buttons.flash.min.js"></script>
        <script src="assets/js/buttons.html5.min.js"></script>
        <script src="assets/js/buttons.print.min.js"></script>
        <script src="assets/js/buttons.colVis.min.js"></script>
        <script src="assets/js/dataTables.select.min.js"></script>

        <!-- ace scripts -->
        <script src="assets/js/ace-elements.min.js"></script>
        <script src="assets/js/ace.min.js"></script>
		
        <!-- inline scripts related to this page -->
        <script type="text/javascript">
                            jQuery(function ($) {
                                //initiate dataTables plugin
                                var myTable =
                                        $('#dynamic-table')
                                        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                                        .DataTable({
                                            bAutoWidth: false,
                                            "aoColumns": [
                                                //{"bSortable": false},
                                                null, null, null,null, 
                                                {"bSortable": false}
                                            ],
                                            "aaSorting": [],

                                           select: {
                                                style: 'multi'
                                            }
                                        });



                                $.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-overlap btn-group btn-overlap';

                                new $.fn.dataTable.Buttons(myTable, {
                                    buttons: [
                                        {
                                            "extend": "colvis",
                                            
                                            "text": "<i class='fa fa-search bigger-110 blue' ></i> <span class='hidden'>Show/hide columns</span>",
                                            "className": "btn btn-white btn-primary btn-bold",
                                            columns: ':not(:first):not(:last)'
                                        },
                                        {
                                            "extend": "copy",
                                            "text": "<i class='fa fa-copy bigger-110 pink'>Copy</i> <span class='hidden'>Copy to clipboard</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
                                        {
                                            "extend": "csv",
                                            "text": "<i class='fa fa-file-excel-o bigger-110 green'>Excel</i> <span class='hidden'>Export to CSV</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
//                                        {
//                                            "extend": "excel",
//                                            "text": "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Export to Excel</span>",
//                                            "className": "btn btn-white btn-primary btn-bold"
//                                        },
//                                        {
//                                            "extend": "pdf",
//                                            "text": "<i class='fa fa-file-pdf-o bigger-110 red'></i> <span class='hidden'>Export to PDF</span>",
//                                            "className": "btn btn-white btn-primary btn-bold"
//                                        },
                                        {
                                            "extend": "print",
                                            "text": "<i class='fa fa-print bigger-110 grey'></i> <span class='hidden'>Print</span>",
                                            "className": "btn btn-white btn-primary btn-bold",
                                            autoPrint: false,
                                            message: 'This print was produced using the Print button for DataTables'
                                        }
                                    ]
                                });
                                myTable.buttons().container().appendTo($('.tableTools-container'));

                                //style the message box
                                var defaultCopyAction = myTable.button(1).action();
                                myTable.button(1).action(function (e, dt, button, config) {
                                    defaultCopyAction(e, dt, button, config);
                                    $('.dt-button-info').addClass('gritter-item-wrapper gritter-info gritter-center white');
                                });


                                var defaultColvisAction = myTable.button(0).action();
                                myTable.button(0).action(function (e, dt, button, config) {

                                    defaultColvisAction(e, dt, button, config);


                                    if ($('.dt-button-collection > .dropdown-menu').length == 0) {
                                        $('.dt-button-collection')
                                                .wrapInner('<ul class="dropdown-menu dropdown-light dropdown-caret dropdown-caret" />')
                                                .find('a').attr('href', '#').wrap("<li />")
                                    }
                                    $('.dt-button-collection').appendTo('.tableTools-container .dt-buttons')
                                });

                                ////

                                setTimeout(function () {
                                    $($('.tableTools-container')).find('a.dt-button').each(function () {
                                        var div = $(this).find(' > div').first();
                                        if (div.length == 1)
                                            div.tooltip({container: 'body', title: div.parent().text()});
                                        else
                                            $(this).tooltip({container: 'body', title: $(this).text()});
                                    });
                                }, 500);





                                myTable.on('select', function (e, dt, type, index) {
                                    if (type === 'row') {
                                        $(myTable.row(index).node()).find('input:checkbox').prop('checked', true);
                                    }
                                });
                                myTable.on('deselect', function (e, dt, type, index) {
                                    if (type === 'row') {
                                        $(myTable.row(index).node()).find('input:checkbox').prop('checked', false);
                                    }
                                });




                                /////////////////////////////////
                                //table checkboxes
                                $('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);

                                //select/deselect all rows according to table header checkbox
                                $('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function () {
                                    var th_checked = this.checked;//checkbox inside "TH" table header

                                    $('#dynamic-table').find('tbody > tr').each(function () {
                                        var row = this;
                                        if (th_checked)
                                            myTable.row(row).select();
                                        else
                                            myTable.row(row).deselect();
                                    });
                                });

                                //select/deselect a row when the checkbox is checked/unchecked
                                $('#dynamic-table').on('click', 'td input[type=checkbox]', function () {
                                    var row = $(this).closest('tr').get(0);
                                    if (this.checked)
                                        myTable.row(row).deselect();
                                    else
                                        myTable.row(row).select();
                                });



                                $(document).on('click', '#dynamic-table .dropdown-toggle', function (e) {
                                    e.stopImmediatePropagation();
                                    e.stopPropagation();
                                    e.preventDefault();
                                });



                                //And for the first simple table, which doesn't have TableTools or dataTables
                                //select/deselect all rows according to table header checkbox
                                var active_class = 'active';
                                $('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function () {
                                    var th_checked = this.checked;//checkbox inside "TH" table header

                                    $(this).closest('table').find('tbody > tr').each(function () {
                                        var row = this;
                                        if (th_checked)
                                            $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
                                        else
                                            $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
                                    });
                                });

                                //select/deselect a row when the checkbox is checked/unchecked
                                $('#simple-table').on('click', 'td input[type=checkbox]', function () {
                                    var $row = $(this).closest('tr');
                                    if ($row.is('.detail-row '))
                                        return;
                                    if (this.checked)
                                        $row.addClass(active_class);
                                    else
                                        $row.removeClass(active_class);
                                });



                                /********************************/
                                //add tooltip for small view action buttons in dropdown menu
                                $('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});

                                //tooltip placement on right or left
                                function tooltip_placement(context, source) {
                                    var $source = $(source);
                                    var $parent = $source.closest('table')
                                    var off1 = $parent.offset();
                                    var w1 = $parent.width();

                                    var off2 = $source.offset();
                                    //var w2 = $source.width();

                                    if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2))
                                        return 'right';
                                    return 'left';
                                }




                                /***************/
                                $('.show-details-btn').on('click', function (e) {
                                    e.preventDefault();
                                    $(this).closest('tr').next().toggleClass('open');
                                    $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
                                });
                               


                            })
        </script>
<script>
    // WRITE THE VALIDATION SCRIPT IN THE HEAD TAG.
    function isNumber(evt) {
        var iKeyCode = (evt.which) ? evt.which : evt.keyCode
        if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
            return false;

        return true;
    }    
</script>