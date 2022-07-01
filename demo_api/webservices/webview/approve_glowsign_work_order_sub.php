<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<?php 
include('script.php');
?>
<!-- LOADER -->
<style>
.loader {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url('loading.gif') 50% 50% no-repeat rgb(249,249,249);
    opacity: .8;
}
</style>
<div class="loader" ></div>

<script type="text/javascript">
$(window).load(function() {
    $(".loader").fadeOut("slow");
});
</script>
<script type="text/javascript">

    function checkAll(checkId){
        var inputs = document.getElementsByTagName("input");
        for (var i = 0; i < inputs.length; i++) { 
            if (inputs[i].type == "radio" && inputs[i].id == checkId) { 
                if(inputs[i].checked == true) {
                    inputs[i].checked = false ;
                } else if (inputs[i].checked == false ) {
                    inputs[i].checked = true ;
                }
            }  
        }  
 //       alert(i);
    }
   
</script>
    <script>
  $( function() {
    $( ".datepicker" ).datepicker();
  } );
  </script>
  <style>
.checkboxes label {
	
	font-size: 12px;
	color: #666;
	border-radius: 20px 20px 20px 20px;
	background: #f0f0f0;
	padding: 3px 10px;
	text-align: left;
}
input[type=checkbox]:checked + label {
	color: white;
	background: red;
}

</style>
  <style>
     html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow:hidden;
      }
  .dataTables_length,.dataTables_info,.dataTables_paginate 
  {
  display : none;
  }
  .stuck {
    position: fixed;
    top: 10px;
    bottom: 10px;
    overflow-y: scroll;
}
</style>
<style>
     body{
     overflow:hidden;
     } 
      tbody {
      position: fixed;
    height: 400px;
    width: 90%;
    overflow: auto;
    overflow-x: hidden;
    display: block;
}

tbody tr{
display: table;
width:100%;
table-layout:fixed}
</style>
</head>
<body background="bg.png">
<?php
include ('connectdb.php');
include('Approval.php');
include('NewSurvey.php');
$fromDate = $_GET['fromDate'];
$toDate = $_GET['toDate'];
$orderId = $_GET['orderId'];
$userId = $_GET['userId'];
$flag= $_GET['flag'];

$obj = new Approval();
$newObj = new NewSurvey();
$data = $obj->get_gsb($fromDate,$toDate,$orderId,$userId,$flag);
$monthly = $obj->get_monthly($fromDate,$toDate,$orderId,$userId);
//print_r($monthly); exit;
 if(isset($_POST['submit']) && $_POST['submit'] == 'Submit')
      {
     // echo "<pre>";
    // print_r($_POST); exit;
      $check = $_POST['check'];
      $userid = $_POST['user_id'];
      $seniorUser = $_POST['user_id'];
    $curdate = date('Y-m-d H:i:s');
      foreach($check as $keycheck=> $valuecheck)
      {
    
      $checkbox = $valuecheck;
       $amount = $_POST['amount'][$keycheck];
       $orderId = $_POST['order_id'][$keycheck];
       $material_code = $_POST['material_code'][$keycheck];
       $glow_sign_type_code = $_POST['glow_sign_type_code'][$keycheck];
       $rate_per_sf = $_POST['rate_per_sf'][$keycheck];
       $size_code = $_POST['size_code'][$keycheck];
       $qty = $_POST['qty'][$keycheck];
       $height = $_POST['height'][$keycheck];
       
       $width = $_POST['width'][$keycheck];
       $area = $_POST['area'][$keycheck];
       $remark = $_POST['remark'][$keycheck];
 $seniorUser = $newObj->get_senior($_POST['user_id']);
      $uq = "UPDATE `purchase_order_details` SET  status = '$checkbox' ,`approved_by`='$userId',`approved_date`='$curdate',`approved_for`='$seniorUser',`updated_at`='$curdate' WHERE unique_id = '$keycheck'";  
     $ruq =   mysqli_query($dbc,$uq);
       
       if($ruq)
       {
      $iq = "INSERT INTO `purchase_order_approval`( `purchase_order_id`, `unique_id`, `material_code`, `glow_sign_type`, `rate_per_sf`, `size`, `qty`, `height`, `width`, `area`, `amount`, `remark`, `status`, `approved_by`, `approved_date`, `created_at`, `updated_at`) values ('$orderId','$keycheck','$material_code','$glow_sign_type_code','$rate_per_sf','$size_code','$qty','$height','$width','$area','$amount','$remark','$checkbox','$userid','$curdate','$curdate','$curdate')";
        
  $rq = mysqli_query($dbc,$iq);
  if($checkbox == 1)
  {
  $monthQ = "UPDATE   monthly_budget set approval_amount=approval_amount+$amount  WHERE employee_code='$userid' AND month = DATE_FORMAT('$curdate','%Y-%m')";
 $runMonth = mysqli_query($dbc,$monthQ);
  }
     ?> <script>
     // alert("DONE");
                    setTimeout("window.parent.location = ''", 100);
                     </script>
                <?php 
  }
  
      }
    
      }
?>
<div class="container-fluid">
<form action="" method="POST">

  <div class="row" style="position:fixed; width:100%; top:0px;">
    <div class="col-sm-12" style="background-color:#64a082;">Monthly Budget:&nbsp;&nbsp;<?=$monthly['amount']?></div>
    <div class="col-sm-12" style="background-color:#64a082;">Monthly Spent:&nbsp;&nbsp;&nbsp;&nbsp;<?=$monthly['approval_amount']?>
    
    </div>
    </div>
    <div class="row" style="position:fixed; width:100%; top:40px;">
    <?php if ($flag==0)
    {
    ?>
  <input type="submit"  value="Submit" name="submit"  style="width:100%; background-color:#dd1526;color:#fff;" class=" form-control" >
    <?php } ?>
    
    </div>
  
    <div style="margin-top:26%">
    <input type="hidden" name="user_id" value="<?=$userId?>">
           <table  id="example"   style="width:100%">
      <thead class="hidden-xs">
      <tr>
      <th>
      Data
        </th></tr>
  </thead>
  
  <tbody>
    <?php
    $i = 0;
    foreach($data as $key=>$value){   
      ?>
 
  
  <tr><td>
  
    <div  class="row" data-toggle="collapse" style="background-color:#e0e0e8; border-top:1px solid #000; border-bottom:1px solid #000;"  data-target="#demo<?php echo $i;?>">
    <div class="col-sm-6">Dealer Name: <?=$value['retailerName']?></div>
    <div class="col-sm-6">Vendor Name: <?=$value['vendor_name']?></div>
    <div class="col-sm-12">
   <span style="float:left"><strong> Qty : <?=$value['qty']?> </strong></span>
    <span style="float:right"><strong> Amount : ₹ <?=$value['amt']?> </strong> </span>
   </div>
  </div>
  <div class="row" style="background-color:#e0e0e8;  border-bottom:1px solid #000;" >

  <div class="col-md-12 col-sm-12 col-lg-12">
   <?php if ($flag==0)
    {
    ?>
   <input type="checkbox" id="option<?=$value['purchase_order_id']?>1"  name="check_box[<?=$value['purchase_order_id']?>]" value="1"  onclick="checkAll('option<?=$value[purchase_order_id]?>1');"/>Approve&nbsp;
    <input type="checkbox"  id="option<?=$value['purchase_order_id']?>2"  name="check_box[<?=$value['purchase_order_id']?>]" value="2" onclick="checkAll('option<?=$value[purchase_order_id]?>2');"/> Reject&nbsp;
    <input type="checkbox" id="option<?=$value['purchase_order_id']?>3"  name="check_box[<?=$value['purchase_order_id']?>]" value="3" onclick="checkAll('option<?=$value[purchase_order_id]?>3');"/>Return&nbsp;</br>
    <?php } ?>
</div>
</div>
    <div class="collapse" id="demo<?php echo $i;?>">
  <?php 
  $j=0;
   foreach($value['details'] as $keydetail=>$valuedetail){ 

  $j++;
  ?>
      <input type="hidden" name="order_id[<?=$valuedetail['unique_id']?>]" value="<?=$valuedetail['purchase_order_id']?>">
      <input type="hidden" name="material_code[<?=$valuedetail['unique_id']?>]" value="<?=$valuedetail['material_code']?>">
       <input type="hidden" name="glow_sign_type_code[<?=$valuedetail['unique_id']?>]" value="<?=$valuedetail['glow_sign_type_code']?>">
        <input type="hidden" name="rate_per_sf[<?=$valuedetail['unique_id']?>]" value="<?=$valuedetail['rate_per_sf']?>">
         <input type="hidden" name="size_code[<?=$valuedetail['unique_id']?>]" value="<?=$valuedetail['size_code']?>">
         
           <input type="hidden" name="qty[<?=$valuedetail['unique_id']?>]" value="<?=$valuedetail['qty']?>">
       <input type="hidden" name="height[<?=$valuedetail['unique_id']?>]" value="<?=$valuedetail['height']?>">
        <input type="hidden" name="width[<?=$valuedetail['unique_id']?>]" value="<?=$valuedetail['width']?>">
         <input type="hidden" name="area[<?=$valuedetail['unique_id']?>]" value="<?=$valuedetail['area']?>">
          <?php if ($flag==0)
    {
    ?>
  <!--<input type="checkbox" checked="true"   name="fooby[ <?=$valuedetail['unique_id']?>" value="0"/>-->
   <input type="radio" id="option<?=$value['purchase_order_id']?>1"  name="check[<?=$valuedetail['unique_id']?>]" value="1"/> Approve &nbsp;
    <input type="radio"  id="option<?=$value['purchase_order_id']?>2"  name="check[<?=$valuedetail['unique_id']?>]" value="2"/> Reject &nbsp;
    <input type="radio" id="option<?=$value['purchase_order_id']?>3"  name="check[<?=$valuedetail['unique_id']?>]" value="3"/> Return &nbsp;</br>
    <?php 
    }
    ?>
    <b>OrderId:</b> <?=$valuedetail['unique_id']?> </br>
    <b>Created at:</b> <?=$valuedetail['created_at']?></br>
    <b>Remark:</b> <?=$valuedetail['remark']?> </br>
    <b>GlowSign Type:</b> <?=$valuedetail['glow_sign_type']?> </br>
    <b>Rate per ₹:</b> <?=$valuedetail['rate_per_sf']?> </br>
      <b> Width: </b><?=$valuedetail['width']?>  <?=$valuedetail['size']?> </br>
       <b>Height: </b><?=$valuedetail['height']?>  <?=$valuedetail['size']?> </br>
    <b>Amount ₹:</b> <?=$valuedetail['amount']?>
     <?php if ($flag==0)
    {
    ?></br>
    <b>Amount</b><input type="text" name="amount[<?=$valuedetail['unique_id']?>]" style="width:100%" value="<?=$valuedetail['amount']?>"/><br/>
     <b>Remark</b><input type="text" name="remark[<?=$valuedetail['unique_id']?>]" style="width:100%" />
<?php }
?>
 <hr>
  <?php } ?>
  </div>
</td>
  </tr>
    <?php 
  $i++;
  }?>

  </tbody>
<tfoot>
    <tr>
<td>
<hr>

</td>
  </tr>
</tfoot>
  </table>

    </div>  
    
  </div>
</div>

</body>
</html>

<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#example').DataTable();
} );
</script>
<script type="text/javascript">
  $("input:checkbox").on('click', function() {
  // in the handler, 'this' refers to the box clicked on
  var $box = $(this);
  if ($box.is(":checked")) {
    // the name of the box is retrieved using the .attr() method
    // as it is assumed and expected to be immutable
    var group = "input:checkbox[name='" + $box.attr("name") + "']";
    // the checked state of the group/box on the other hand will change
    // and the current value is retrieved using .prop() method
    $(group).prop("checked", false);
    $box.prop("checked", true);
  } else {
    $box.prop("checked", false);
  }
});
</script>
