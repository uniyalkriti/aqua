<?php if (!defined('BASE_URL')) die('direct script access not allowed'); ?>
<?php
//echo $_GET['order_id'];die;

$forma = 'Make Payment'; // to indicate what type of form this is
$formaction = $p;
$myobj = new dealer_sale();
$myobj_pay = new payment();
$cls_func_str = 'dealer_sale'; //The name of the function in the class that will do the job
$myorderby = 'user_sales_order.id DESC'; // The orderby clause for fetching of the data
$myfilter = 'user_sales_order.order_id ='; //the main key against which we will fetch data in the get_item_category_function
$loc_level = $_SESSION[SESS . 'constant']['location_level'];
//Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS . 'data']['id'], $formaction);
$dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
?>
<div id="breadcumb"><a href="#">Invoice</a> &raquo; <a></a>  &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma; ?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
    <?php 

    if(isset($_POST['Submit2']) && $_POST['Submit2']=='Save Payment'){
    
        $return = $myobj_pay->save_payment();

        echo "<script>alert('$return[myreason]');</script>";


    }
    ?>  
</div>

<?php
if (isset($_GET['mode']) && $_GET['mode'] == 1){
    if (isset($_GET['total_val'])) {
        $total_val = $_GET['total_val'];
        $retailer_id = $_GET['retailer_id'];
        $order_id= $_GET['order_id'];
       
        
        
    }
}

 // to show the form when and only when needed
?>
     <form method="post" action="" class="iform" name="genform" onsubmit="return checkForm_alert('genform');">
      <fieldset>
        <legend class="legend" style=""><?php echo $forma; ?></legend>
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
        <input type="hidden" name="company_id" value="<?php echo $_SESSION[SESS.'data']['company_id']; ?>">
        <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
         <tr>
            
                      
            <td>
            <span class="star"></span>Payment Amount<br />
            <input type="text" name="total_amount_view" value="<?php echo $total_val; ?>" disabled>
            <input type="hidden" name="total_amount" value="<?php echo $total_val; ?>">
            <input type="hidden" name="retailer_id" value="<?php echo $retailer_id; ?>">
            <input type="hidden" name="challan_id" value="<?php echo $order_id; ?>">
            </td>            
           <td><span class="star">*</span>Payment Type<br />
               <select onchange="hideDiv(this.value)" name="pay_mode">
                   <option value="">== Please Select ==</option>
                   <option value="0" selected="selected">By Cash</option>
                   <option value="1">By Cheque</option>
               </select>
            </td>  
            </tr>
            <tr> 
                <!--  <td id="td4" style="visibility: hidden">Amount<br />
                <input type="text"  name="amount" id="amount"  value="<?php if(isset($_POST['amount'])) echo $_POST['amount']; ?>"  />
            </td> --> 
             <td id="td4" style="">Remark<br />
                    <input   name="Remark" id="Remark" value="" >            </td> 
                <td id="td1" style="visibility: hidden">Bank Name<br />
                    <input   name="bank_name" id="bank_name" value="<?php if(isset($_POST['bank_name'])) echo $_POST['bank_name']; ?>" >            </td> 
            <td id="td2" style="visibility: hidden">Cheque No<br />
                <input  type="text" id="chq_no"  name="chq_no"  value="<?php if(isset($_POST['chq_no'])) echo $_POST['chq_no']; ?>"  />            </td> 
             <td id="td3" style="visibility: hidden">Cheque Date<br />
                 <input  type="text"  class="qdatepicker" id="chq_date"  name="chq_date"  value="<?php if(isset($_POST['cheque_date'])) echo $_POST['cheque_no']; ?>"  />            </td>

          
             
            
         </tr>                 
         <tr>
             <td colspan="5" align="center">
            <?php //form_buttons(); // All the form control button, defined in common_function?>
            <input id="mysave" type="submit" name="Submit2" value="<?php if(isset($heid)) echo'Update'; else echo'Save Payment';?>" />
            <?php if(isset($heid)){ echo $heid; //A hidden field name eid, whose value will be equal to the edit id.?>
            <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>&showmode=1';" type="button" value="New" title="add new <?php echo $forma;?>" />  
            <input onclick="parent.$.fn.colorbox.close();" type="button" value="Exit" /> <br />
            <?php edit_links_via_js($id, $jsclose=true, $options=array());?>            
            <?php }else{?>
            <input onclick="parent.$.fn.colorbox.close();" type="button" value="Close" />
            <?php }?>
            </td>
          </tr>
        </table>
      </fieldset>
    </form>
 <script>
 function hideDiv(value){
    if(value == 0) {
       document.getElementById('td4').style.visibility='visible';
       document.getElementById('td1').style.visibility='hidden';
        document.getElementById('td2').style.visibility='hidden';
         document.getElementById('td3').style.visibility='hidden';
    }
     if(value == 1) {
       document.getElementById('td4').style.visibility='visible';
       document.getElementById('td1').style.visibility='visible';
        document.getElementById('td2').style.visibility='visible';
         document.getElementById('td3').style.visibility='visible';
       
       
    }
}
 </script>