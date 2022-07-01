<?php #  leads.inc.php

/*
* This is the main content module.
* This page is included by index.php.
*/
if (!defined('BASE_URL')) require_once('../../page_not_direct_allow.php');
?>
<?php 

$forma = 'SYNC DATA'; // to indicate what type of form this is
$formaction = $p;
// Getting the user credentials for this page access
//$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
//$dealer_id=$_SESSION[SESS.'data']['dealer_id'];
$url="dealer_id=$dealer_id";
$url_data= base64_encode($url);
$step=$_GET['step'];

if(!isset($step) || $step==1){
    $button_value="SYNC";
    $action="../webservices/client_return_data.php";
}elseif ($step==2) {
    $button_value="FINISH";
    $action="../webservices/client_recieve_data.php?data=$url_data";   
}

    $connected = @fsockopen("dsdsr.com", 80);                                
    if ($connected){
        $is_conn = "Online"; //action when connected
        fclose($connected);
    }else{
        $is_conn ="Offline";
        echo'<div class="alert alert-danger" style="text-align: center;">
                        <strong>Unable to Sync!</strong> Please Connect to the Internet !!!!!
                        </div>';exit;
        //action in connection failure
    }
    //echo $is_conn;
?>

	 <!-- <h1 style=""><?php echo $forma;?></h1>-->
<!--      <div id="breadcumb">
          <a href="#">Miscellaneous</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;">Sync Data</a> 
          <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
      </div>-->


     
         
      
      <div class="row">
          
      </div>
        <div class="row" style="align-content:center">
            <form action="<?=$action?>" method="POST">
                <div class="col-md-3"></div>
            <div class="col-md-6">
            
            <div class="panel panel-warning">

                <div class="panel-heading" style="text-align: center;">
                    
                    <h2 class="panel-title" style="padding-bottom: 15px; font-size: 20px;">SYNC TO SERVER</h2>
                    <p style="color:red;"><strong>Please follow all steps to sync data on Server</strong> </p>
                   <?php 
                   $dealer_id =  $_SESSION[SESS.'data']['dealer_id'];
                   $q_max_date="SELECT MAX(DATE_FORMAT(date_time,'%Y-%m-%d')) AS last_sync_date FROM sync_status WHERE dealer_id='$dealer_id' LIMIT 1";
                   $run_max_date= mysqli_query($dbc, $q_max_date);
                   $result_max_date= mysqli_fetch_assoc($run_max_date);
                   $last_sync_date1=$result_max_date['last_sync_date'];
                   $last_sync_date = strtotime($last_sync_date1);
                   $curr_date1=date('Y-m-d');
                   $curr_date = strtotime($curr_date1);
                   $days_between = ceil(abs($curr_date - $last_sync_date) / 86400);
                   ?>
                    <p style="color:red;"><strong>You are not Sync to Server from the last <?=$days_between?> day(s)</strong> </p>
                    <?php if($step==1 || empty($step)){
                        echo '<h2 class="panel-title" style="font-size: 20px; color:#0073e6;">STEP 1 <span class="glyphicon glyphicon-refresh"></span></h2>';
                       // echo'<h2 class="panel-title" style="font-size: 20px; color:#0073e6;">STEP 2 <span class="glyphicon glyphicon-refresh"></span></h2>';
                    }elseif($step==2){
                          echo '<h2 class="panel-title" style="font-size: 20px; color:green;">STEP 1 <span class="glyphicon glyphicon-ok"></span></h2>';
                        echo'<h2 class="panel-title" style="font-size: 20px; color:#0073e6;">STEP 2 <span class="glyphicon glyphicon-refresh"></span></h2>';
                    }else{
                       echo '<h2 class="panel-title" style="font-size: 20px; color:green;">STEP 1 <span class="glyphicon glyphicon-ok"></span></h2>';
                        echo'<h2 class="panel-title" style="font-size: 20px; color:green;">STEP 2 <span class="glyphicon glyphicon-ok"></span></h2>';  
                    } 
                    ?>
                    
                    

                </div>
                <div></div>
                <div></div>
                <div></div>
                
            </div>
                <?php if(!isset($step) || $step<=2){?>
                <center> 
                    <div class="col-xs-12">
                        <input class="btn btn-primary" type="submit" name="submit" value="<?=$button_value?>" />
                    </div>
                </center>
                <?php }elseif($step=='404'){
                     echo'<div class="alert alert-danger" style="text-align: center;">
                        <strong>Failed!</strong> Data not Sync on server please contact to support team
                        </div>';
                }elseif($step=='143'){
                     echo'<div class="alert alert-danger" style="text-align: center;">
                        <strong>Failed!</strong> Nothing to Sync from the Server
                        </div>';
                }else{
                    echo'<div class="alert alert-success" style="text-align: center;">
                        <strong>Success!</strong> All Data Sync on Server.
                        </div>';
                            }?>
            </div>
            </form>
                
            
        </div>
        
         
       <hr/>

      
     