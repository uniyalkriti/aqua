<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
$forma = 'Feedback'; // to indicate what type of form this is
$formaction = $p;
$myobj = new feedback();
$cls_func_str = 'feedback'; //The name of the function in the class that will do the job
$myorderby = 'feedback_no ASC'; // The orderby clause for fetching of the data
$myfilter = 'feedback.id='; //the main key against which we will fetch data in the get_item_category_function
//Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
$category = $_SESSION[SESS.'constant']['catalog_title_1'];
$mtype = isset($_GET['mtype']) ? $_GET['mtype'] : 1;

?>
  <div id="breadcumb"><a href="#">Misc</a> &raquo; <a href="index.php?option=<?php echo $formaction; ?>" style="color:#2dcf5f;"><?php echo $forma;?></a> <span id="ajaxloader"><img src="images/ajaxloader.gif" /> Loading...</span>
      <?php //if(!(isset($_GET['showmode']) && $_GET['showmode'] == 1)) require_once('breadcum/catalog_bread.php'); 
?>
 </div>
<?php
stop_page_view($auth['view_opt']); // checking the user current page view
############################# code for checking of submitted form data starts here data starts here ########################
function checkform($mode='add', $id='')
{
	global $dbc;
	if($mode == 'filter') return array(TRUE, '');
	$field_arry = array('van_no' => $_POST['van_no']);// checking for  duplicate Unit Name
	if($mode == 'add'){
		if(uniqcheck_msg($dbc,$field_arry,'feedback', false, ""))
			return array(FALSE, '<b>Feedback No</b> already exists, please provide a different value.');
	}elseif($mode == 'edit'){
		if(uniqcheck_msg($dbc,$field_arry,'feedback', false," vanId != '$_GET[id]'"))
			return array(FALSE, '<b>Feedback No</b> already exists, please provide a different value.');
	}
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
				show_row_change(BASE_URL_A.'?option='.$formaction, $action_status['rId']);
				unset($_POST);
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
	if(valid_token($_POST['hf'])) // checking if post value is same as timestamp stored in session during form load
	{
		//calculating the user authorisastion for the operation performed, function is defined in common_function
		list($checkpass, $fmsg) = user_auth_msg($auth['edit_opt'], $operation = 'edit', $id = $_POST['eid']);		
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
				show_row_change(BASE_URL_A.'?option='.$formaction, $_POST['eid']);
				unset($_POST);
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
		if(!empty($mystat))
		{
			//geteditvalue_class($eid=$id, $in = $mystat, $labelchange=array(), $options)
			geteditvalue_class($eid=$id, $in = $mystat);
			$heid = '<input type="hidden" name="eid" value="'.$id.'" />';
		}
		else
			echo '<span class="awm">Sorry, no such '.$forma.' found.</span>';
	}									 
}
############################# Code to handle the user search starts here ##############################
$rs = array();
$filterused = '';
$funcname = 'get_'.$cls_func_str.'_list';
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
			if(!empty($_POST['feedback_no'])){
				$filter[] = "feedback_no LIKE '%$_POST[feedback_no]%'";
				$filterstr[] = '<b>Feedback No  : </b>'.$_POST['feedback_no'];
			}
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
	$filter = "dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}'";
$rs = $myobj->$funcname($filter,  $records = '', $orderby ="ORDER BY $myorderby");
}
dynamic_js_enhancement();
?>
<script type="text/javascript">
$(function() {
	$("#itemcode").autocomplete({
		source: "./modules/ajax-autocomplete/item/ajax-itemcode.php"
	});
});
</script>
    <div id="workarea">
      <?php if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ // to show the form when and only when needed?>
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" name="genform" onsubmit="return checkForm_alert('genform');">
      <fieldset>
<!--        <legend class="legend" style=""><?php echo $forma; ?></legend>-->
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
        <input type="hidden" name="company_id" value="<?php echo $_SESSION[SESS.'data']['company_id']; ?>">
        <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
         <tr>    
            <td colspan="5" align="center"><span class="star">*</span><strong><font size="3">Title</font></strong><br />
            <?php
                  $qf = "SELECT id,name FROM _feedback_type";
                db_pulldown($dbc, "feedback_type", $qf, true, true, 'id="feedback_type" lang="feedback type"',"==PLease Select==","$_POST[feedback_type]");               
                ?>
            </td>
            </tr>
            <tr>
            <td colspan="5" align="center"><strong><font size="3">Feedback</font></strong><br />
             <textarea name="feedback" cols="100" rows="8" required><?php if(isset($_POST['feedback'])) echo $_POST['feedback']; ?></textarea>
            </td>               
         </tr> 
         <tr>
             <td colspan="5" align="center">
            <?php //form_buttons(); // All the form control button, defined in common_function?>
                 <input  id="mysave" style="background-color: #006dcc" type="submit" name="submit" value="<?php if(isset($heid)) echo'Update'; else echo'Save';?>" />
			<?php if(isset($heid)){ echo $heid; //A hidden field name eid, whose value will be equal to the edit id.?>
            <input style="background-color: green" onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>&showmode=1';" type="button" value="New" title="add new <?php echo $forma;?>" />  
            <input class="btn btn-sm btn-danger" onclick="parent.$.fn.colorbox.close();" type="button" value="Exit" /> <br />
            <?php edit_links_via_js($id, $jsclose=true, $options=array());?>            
			<?php }else{?>
            <input class="btn btn-sm btn-danger" onclick="parent.$.fn.colorbox.close();" type="button" value="Close" />
            <?php }?>
            </td>
          </tr>
        </table>
      </fieldset>
    </form>
    <?php }else{//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here?>
    
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>"  name="genform1" onsubmit="return checkForm('genform1');">
       <table width="100%" border="0" cellspacing="2" cellpadding="2">
         <tr id="mysearchfilter">
           <td>
             <!-- this table will contain our form filter code starts -->
            <fieldset>
               
               <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
             <table>
               <tr>
                <td>Feedback Number<br />
                <input type="text" name="feedback_no"  value="<?php if(isset($_POST['feedback_no'])) echo $_POST['feedback_no'];?>"/> 
                </td>
                <td> &nbsp;&nbsp;&nbsp;&nbsp;
                </td>
                <td> &nbsp;&nbsp;&nbsp;&nbsp;
                </td>
                <td><br>
                    <input class="btn btn-primary" id="mysave" type="submit" name="filter" value="Filter" />
                    <!--<input class="btn btn-danger" onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />-->
                  <input class="btn btn-success" onclick="$.colorbox({href:'index.php?option=<?php echo $formaction; ?>&showmode=1', iframe:true, width:'95%', height:'95%'});" type="button" value="New" title="add new <?php echo $formaction; ?>" />
                </td>
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
    	  <tr>
            <td>
                <br>
                                   
                <div class="table-header">
                  Feedback &nbsp;&nbsp;
                  <a href="javascript:printing('searchlistdiv');" title="take a printout" style="margin:0 10px;"><img src="./icons/print.png" /></a>
                
                <div class="pull-right tableTools-container"></div> 
                   
                </div>
               
              </div>
            </td>
          </tr>	
          <tr>
            <td>            
              <?php
			  ########################## pagination details fetch starts here ###################################
              $pgoutput = get_pagination_details($rs);
			 
              echo $pgoutput['loader'];
              foreach($pgoutput['temp_result'] as $key=>$value){
                 $rs = $value; 	 
                 echo'<div class="mypages" id="mypages'.$key.'" style="display:none;">';
			  ########################## pagination details fetch ends here ###################################
			  $inc = 1+($key-1)*PGDISPLAY;
			  $lastinc = (($inc+PGDISPLAY-1) > $pgoutput['totrecords']) ? $pgoutput['totrecords'] : ($inc+PGDISPLAY-1);
			  ?>	 
              
              <div class="searchlistdiv" id="searchlistdiv"> 
                  <style> th {
    background-color: #C7CDC8;
    color:#000;
}</style>
                 <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                        <thead> <tr class="search1tr">
                      <th class="sno">S.No</th>
                      <th>Feedback No</th>
                      <th>Feedback Type</th>
                      <th>Feedback</th>
                       <!-- <th>Status</th> -->
                      <th class="options">Options</td>
                    </tr>
                    </thead>
                  <?php 
                  $bg = TR_ROW_COLOR1;
                  //$inc = 1;
                 if(isset($_GET['ajaxshow'])) ob_end_clean(); // to help refresh a single row
                  foreach($rs as $key=>$rows)
                  {
                      $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $rows['id'];
                      $uidname = $rows['feedback_type_name'];		  
                      $editlink = '<a class="iframef" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'"><img src="./images/b_edit.png"></a>';
                      $deletelink = '<span class="seperator">|</span> <a href="javascript:void(0);" onclick="do_delete_special(\'Category Delete\', \''.$uid.'\',\'Category\',\''.addslashes($uidname).'\',\''.$mtype.'\');"><img src="./images/b_drop.png"></a>';
                      $deletelink = '';
                      if($rows['status']==1){
                          $vstatus="Active";
                      }elseif($rows['status']==0){
                          $vstatus="Deactive";
                      }
                      // if($rows['locked'] == 1) $editlink = $deletelink = '';
                    if($auth['del_opt'] !=1) $deletelink = '';
                     echo'
                      <tr BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
                        <td class="myintrow myresultrow">'.$inc.'</td>
                        <td>'.$rows['feedback_no'].'</td>
                        <td><strong>'.$uidname.'</strong><div style="display:none" id="delDiv'.$uid.'"></div></td>
                        <td style="text-transform: uppercase;">'.$rows['feedback'].'</td>
                        <!--<td>'.$vstatus.'</td>-->
                        <td class="options">'.$editlink.$deletelink.'</td>
                      </tr>
                      ';
                      $inc++;
                  }// foreach loop ends here
                if(isset($_GET['ajaxshow'])) exit(); // to help refresh a single row
                  ?>
                </table>                
            </div> 
              <?php echo'</div>';} // foreach($pgoutput['temp_result'] as $key=>$value){ ends?>           
            </td>
          </tr>
          <?php } //if(!empty($rs)){?>
          <?php if(isset($_GET['ajaxshowblank'])) exit(); // to show the first row when parent table not avialable ?>
        </table>
        <?php if(isset($pgoutput)) echo $pgoutput['pglinks']; // showing the paginataion links to the user?>
      </fieldset>
      </form>
      <?php }//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here?>
      </div><!-- workarea div ends here -->
      <script type="text/javascript">setfocus('partycode');</script>
      <?php if(isset($pgoutput)) pagination_js($pgoutput);?>
      <!--       <script src="assets/js/jquery-2.1.4.min.js"></script>-->
 <script src="assets/js/jquery.dataTables.min.js"></script>
        <script src="assets/js/jquery.dataTables.bootstrap.min.js"></script>
        <script src="assets/js/dataTables.buttons.min.js"></script>
        <script src="assets/js/buttons.flash.min.js"></script>
        <script src="assets/js/buttons.html5.min.js"></script>
        <script src="assets/js/buttons.print.min.js"></script>
        <script src="assets/js/buttons.colVis.min.js"></script>
        <script src="assets/js/dataTables.select.min.js"></script>
        <script src="assets/js/jszip.min.js"></script>
        <script src="assets/js/pdfmake.min.js"></script>
        <script src="assets/js/vfs_fonts.js"></script>
        <!-- ace scripts -->
        <script src="assets/js/ace-elements.min.js"></script>
        <script src="assets/js/ace.min.js"></script>

        <script type="text/javascript">
                  $(function(){
                      if(!ace.vars['touch']) {
                           $('.chosen-select').chosen({allow_single_deselect:true});
                      }
                  })
        </script>
           

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
                                                {"bSortable": false},
                                                null, null, null,
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
                                            "text": "<i class='fa fa-search bigger-110 blue'></i> <span class='hidden'>Show/hide columns</span>",
                                            "className": "btn btn-white btn-primary btn-bold",
                                            columns: ':not(:first):not(:last)'
                                        },
                                        {
                                            "extend": "copy",
                                            "text": "<i class='fa fa-copy bigger-110 pink'></i> <span class='hidden'>Copy to clipboard</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
                                        {
                                            "extend": "csv",
                                            "text": "<i class='fa fa-database bigger-110 orange'></i> <span class='hidden'>Export to CSV</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
                                        {
                                            "extend": "excel",
                                            "text": "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Export to Excel</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
                                        {
                                            "extend": "pdf",
                                            "text": "<i class='fa fa-file-pdf-o bigger-110 red'></i> <span class='hidden'>Export to PDF</span>",
                                            "className": "btn btn-white btn-primary btn-bold"
                                        },
                                        {
                                            "extend": "print",
                                            "text": "<i class='fa fa-print bigger-110 grey'></i> <span class='hidden'>Print</span>",
                                            "className": "btn btn-white btn-primary btn-bold",
                                            autoPrint: true,
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


//
//
//
//                                myTable.on('select', function (e, dt, type, index) {
//                                    if (type === 'row') {
//                                        $(myTable.row(index).node()).find('input:checkbox').prop('checked', true);
//                                    }
//                                });
//                                myTable.on('deselect', function (e, dt, type, index) {
//                                    if (type === 'row') {
//                                        $(myTable.row(index).node()).find('input:checkbox').prop('checked', false);
//                                    }
//                                });
//
//
//
//
//                                /////////////////////////////////
//                                //table checkboxes
//                                $('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);
//
//                                //select/deselect all rows according to table header checkbox
//                                $('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function () {
//                                    var th_checked = this.checked;//checkbox inside "TH" table header
//
//                                    $('#dynamic-table').find('tbody > tr').each(function () {
//                                        var row = this;
//                                        if (th_checked)
//                                            myTable.row(row).select();
//                                        else
//                                            myTable.row(row).deselect();
//                                    });
//                                });

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
                                /***************/





                               


                            })
        </script>

