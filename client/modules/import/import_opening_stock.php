<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
$forma = 'Import Opening Stock'; // to indicate what type of form this is
$formaction = $p;
$myobj = new import_os();
$cls_func_str = 'import_opening_stock'; //The name of the function in the class that will do the job
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
$dealer_id=$_SESSION[SESS.'data']['dealer_id'];

stop_page_view($auth['view_opt']); // checking the user current page view
############################# code for checking of submitted form data starts here data starts here ########################

############################# code for SAVING data starts here ########################
if(isset($_POST['submit']) && $_POST['submit'] == 'Upload')
{
	if(valid_token($_POST['hf'])) // checking if post value is same as timestamp stored in session during form load
	{
            $funcname = $cls_func_str.'_save'; 
            $action_status =  $myobj->$funcname();
            if($action_status['status']=='false'){
            echo'<div class="alert alert-danger fade in">
                <a href="index.php?option=import_invoice" class="close" data-dismiss="alert">&times;</a>
                <strong>Error!</strong> There was a problem with your uploded sheet. 
                Please fix below listed issues,and try again.
                <br/>--------------------- Error Logs------------------------<br/>
                <strong>'.$action_status[myreason].'.
                </strong></div>';
            }else{
                
               // echo key($action_status['ch_no']);
                echo'<div class="alert alert-success fade in"> <a href="index.php?option=import_invoice" class="close" data-dismiss="alert">&times;</a>
                <strong>Success!</strong> Following Opening Stock uploaded.Thank you.<br> ';
                foreach(array_keys($action_status['ItemCode']) as $chno)
                echo "Item Code : <strong>".$chno . "<br>";
                echo '</div>'; 
            }
          //  print_r($action_status);exit;

        }
}

?>
<script type="text/javascript">

</script>
    <div id="workarea">
      <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform1" onsubmit="return checkForm('genform1');" enctype="multipart/form-data">
       <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
              <div class="searchlistdiv" id="searchlistdiv"> 
                  <div class="row">
                    <div class="col-xs-12">
                       <div class="box">
                        <div class="box-header">
                            <div class="row">
                            <div class="table-header">
                            <h3 class="widget-title lighter"><?=$forma;?> In CSV File format </h3>
                             </div>

                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="form-group">
                                <div class="col-xs-3">   

                                <a href="modules/import/opening _stock_format.csv" class="btn btn-success" role="button">
                                    Download Format &nbsp; <span class="glyphicon glyphicon-download-alt"></span></a>
                                 </div>


                                     <div class="col-xs-3">   

                                <a href="modules/import/export_previous_opening_stock.php?dealerId=<?=$dealer_id?>" class="btn btn-success" role="button">
                                    Download Previous Stock Format &nbsp; <span class="glyphicon glyphicon-download-alt"></span></a>
                                 </div>



                                <div class="col-xs-3">
                                <input class="btn btn-primary" type="file" id="file" name="excelFile" multiple="multiple" />
                                </div>
                                 <div class="col-xs-2">   
                                <input type="submit" name="submit" style="height:47px" value="Upload" class="btn btn-primary form-control"/><span class="glyphicon glyphicon-upload-alt"></span>
                                 </div>
                                
                            </div>
                            
                        </div>
                        <!-- /.box-body -->
                      
                      </div>
                      <!-- /.box -->
                    </div>
                    <!-- /.col -->
                  </div>
                  <hr></hr>
                    <div class="row">
                          <div class="table-header"><h3 class="widget-title lighter">Important Points To Remember:-</h3></div>
                    </div>
                    <div class="box-body">
                    <font size="6">
                      1. Batch No. is compulsory if you don't have fill <font color="red">BD123</font></br>
                      2. Mfg Date is compulsory if you don't have fill any date in <font color="red">dd/mm/yyyy format</font>.</br>
                      3. If you have same product with <font color="red"> different Batch No and MFG Date</font>,Please copy same product line with <font color="red">item code and paste below it </font>and fill different Batch No and Mfg Date.
                    </font>
                    </div>
              </div> 
      </form>
      </div><!-- workarea div ends here -->
