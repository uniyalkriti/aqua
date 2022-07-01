<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
$forma = 'Import Purchase Invoices'; // to indicate what type of form this is
$formaction = $p;
$myobj = new import();
$cls_func_str = 'import_invoice'; //The name of the function in the class that will do the job
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
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
                <strong>Success!</strong> Following Invoices uploaded.Thank you.<br> ';
                foreach(array_keys($action_status['ch_no']) as $chno)
                echo "Invoice : <strong>".$chno . "<br>";
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
                            <h3 class="widget-title lighter">Import Purchase Invoices In CSV File format </h3>
                             </div>

                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="form-group">
                                <div class="col-xs-4">   

                                <a href="modules/import/purchase_invoice_import_format.csv" class="btn btn-success" role="button">
                                    Download Format &nbsp; <span class="glyphicon glyphicon-download-alt"></span></a>
                                 </div>
                                <div class="col-xs-4">
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
              </div> 
      </form>
      </div><!-- workarea div ends here -->
