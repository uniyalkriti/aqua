<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
$forma = 'Catalog Product'; // to indicate what type of form this is
$formaction = $p;
$myobj = new catalog_product();
$cls_func_str = 'catalog_product'; //The name of the function in the class that will do the job
$myorderby = 'catalog_id DESC'; // The orderby clause for fetching of the data
$myfilter = ' catalog_product.id='; //the main key against which we will fetch data in the get_item_category_function
//Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);

?>

<?php
stop_page_view($auth['view_opt']); // checking the user current page view
############################# code for checking of submitted form data starts here data starts here ########################
function checkform($mode='add', $id='')
{
	global $dbc;
	if($mode == 'filter') return array(TRUE, '');
	$field_arry = array('name' => $_POST['name']);// checking for  duplicate Unit Name
	
	if($mode == 'add'){
		if(uniqcheck_msg($dbc,$field_arry,'catalog_product', false, "unit='$_POST[unit]'"))
			return array(FALSE, '<b>Product</b> already exists, please provide a different value.');
	}elseif($mode == 'edit'){
		if(uniqcheck_msg($dbc,$field_arry,'catalog_product', false," id != '$_GET[id]' AND unit='$_POST[unit]'"))
			return array(FALSE, '<b>Product</b> already exists, please provide a different value.');
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
    $catlog_id = $mystat[$id]['catalog_id'];
    $mystat1 = $myobj->get_product_catalog_list($filter="catalog_id = $catlog_id",  $records = '', $orderby='');


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

############################# Code to handle the user search starts here ###############################
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
			if(!empty($_POST['item_name'])){
				$filter[] = "item_name LIKE '%$_POST[item_name]%'";
				$filterstr[] = '<b>Item Name  : </b>'.$_POST['item_name'];
			}
			if(!empty($_POST['item_code'])){
				$filter[] = "item_code LIKE '%$_POST[item_code]%'";
				$filterstr[] = '<b>Item Code  : </b>'.$_POST['item_code'];
			}
			//$filter[] = "catalog_product.company_id = '{$_SESSION[SESS.'data']['company_id']}'";
			$filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');
                        $rs = $myobj->$funcname($filter,  $records = '', $orderby =""); // $myobj->get_item_category_list()
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
        $rs = $myobj->$funcname($filter= "",  $records = '', $orderby="");
        //$funcname = get_catalog_product_list;
}

dynamic_js_enhancement();

?>
<script type="text/javascript">
$(function() {
	$(".product").autocomplete({
		source: "./modules/ajax-autocomplete/product/ajax-product-name.php"
	});
	$("#itemname").autocomplete({
		source: "./modules/ajax-autocomplete/item/ajax-itemname.php"
	});
});
</script>
    <div id="workarea">
      <?php if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ // to show the form when and only when needed?>
        <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform" onsubmit="return checkForm_alert('genform');" enctype="multipart/form-data">
      <fieldset>
        <legend class="legend" style=""><?php echo $forma; ?></legend>
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
        <input type="hidden" name="company_id" value="<?php echo $_SESSION[SESS.'data']['company_id']; ?>">
        <input type="hidden" name="old_file" value="<?php if(isset($_POST['image_name'])) echo $_POST['image_name']; ?>">
        <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
         <tr>
             <td><span class="star">*</span>Item Code <br>
            <input onChange="this.value = ucwords(trim(this.value));" type="text"  lang="Name" name="item_code" value="<?php if(isset($_POST['itemcode'])) echo $_POST['itemcode']; ?>">
          </td>
          <td><span class="star">*</span>Item Name <br>
            <input onChange="this.value = ucwords(trim(this.value));" type="text"  lang="Name" name="item_name" value="<?php if(isset($_POST['name'])) echo $_POST['name']; ?>">
          </td>
           <td>
              Image <?php if(isset($heid) && !empty($_POST['image_name'])) { echo '<img height="20" width="20" src="../myuploads/product/'.$_POST['image_name'].'" height="30" width="30" >'; } ?><Br/>
              
              <input type="file" name="image_name" value="">
           </td>
           <td>Weight in Grams<br>
                <input type="number" name="grams" value="<?php if(isset($_POST['grams'])) echo $_POST['grams']; ?>">
            </td>

          <!--  <td>Pack Name<br>
                 <?php  
                 db_pulldown($dbc, "packname", "SELECT id, name FROM _packname", true, true);?>
              </td>-->
            <td colspan="2">&nbsp;</td>
          </tr>

          <tr>
              <td colspan="7"><div class="subhead1">Product Category Details</div></td>
          </tr>
          <tr>
          <?php
        $loop = $_SESSION[SESS.'constant']['catalog_level'];
        for($i = 1; $i<=$loop; $i++)
        {
            $j = $i+1; //here we getnextplid
            if(!empty($mystat1))
            {
                foreach($mystat1 as $key=>$value)
                {
                    $_POST["catalog_".$i."_id"] = $value["catalog_".$i."_id"]; 
            ?>
             <td>
                <?php echo $_SESSION[SESS.'constant']["catalog_title_$i"]; ?><br>
                <?php
                    $js_attr = 'onchange="fetch_location(this.value+\'|\'+\''.$i.'\'+\'|\'+\''.$j.'\', \'progress_div\', \'catalog_'.$j.'\', \'catalog-subcategory\');" id="catalog_'.$i.'"';
                   if($i == $loop) $js_attr = 'id="catalog_'.$i.'"';
                   db_pulldown($dbc, "catalog_".$i."_id", "SELECT id, name FROM catalog_$i WHERE company_id = '{$_SESSION[SESS.'data']['company_id']}'", true, true);
                 ?>
            </td>
            
        <?php } // foreach end here
            } // if(!empty($mystat1)) end here
            else
            {
            ?>
            <td>
            <?php echo $_SESSION[SESS.'constant']["catalog_title_$i"]; ?><br>
                <?php
                    $js_attr = 'onchange="fetch_location(this.value+\'|\'+\''.$i.'\'+\'|\'+\''.$j.'\', \'progress_div\', \'catalog_'.$j.'\', \'catalog-subcategory\');" id="catalog_'.$i.'"';

                   if($i == $loop) $js_attr = 'id="catalog_'.$i.'"';

                   db_pulldown($dbc, "catalog_".$i."_id", "SELECT id, name FROM catalog_$i WHERE company_id = '{$_SESSION[SESS.'data']['company_id']}'", true, true);
                 ?>
            </td>
            <?php
            } // else part end here 
        }
       ?>
<!--            <td>Company Name<br>
                 <?php  
                 db_pulldown($dbc, "company_id", "SELECT id, comp_name FROM _product_company", true, true);?>
              </td>-->
           </tr>
         <tr>
           <td align="center" colspan="7">
            <?php //form_buttons(); // All the form control button, defined in common_function?>
            <input id="mysave" type="submit" name="submit" value="<?php if(isset($heid)) echo'Update'; else echo'Save';?>" />
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
    <?php }else{//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here?>
    
     <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform1" onsubmit="return checkForm('genform1');">
       <table width="100%" border="0" cellspacing="2" cellpadding="2">
         <tr id="mysearchfilter">
          
         </tr>
	<?php
	if(isset($_GET['ajaxshowblank'])) ob_end_clean(); // to show the first row when parent table not avialable
    if(!empty($rs)){ //if no content available present no need to show the bottom part
    ?>
    	 
          <tr>
            <td> 
                <div class="row">
    <div class="col-xs-12">
         <div class="row">
            <div class="col-xs-12">
              
                <br>
              
                <div class="table-header">
                Catalog Product <div class="pull-right tableTools-container"></div>
                </div>
                </div>
             <div class="row">
                        <div class="col-md-2">
                            
                        </div>
                         <div class="col-md-1" style="background-color:#AD0606; color:#fff;">
                            <b>Blends</b>
                        </div>
                         <div class="col-md-1" style="background-color:#F67C0F; color:#000;">
                            <b>Sprinkler</b>
                        </div>
                        
                         <div class="col-md-2" style="background-color:#46154D; color:#fff;">
                             <b>Straight Premium</b>
                        </div>
                         <div class="col-md-2" style="background-color:#0A87B2; color:#fff;">
                            <b>Specialities Spices</b>
                        </div>
                        
                         <div class="col-md-1" style="background-color:#D2DC13; color:#000;">
                        <b>Hing</b>
                        </div>
                         <div class="col-md-1" style="background-color:#4D59F0; color:#fff;">
                           <b> Whole</b>
                        </div>
                        <div class="col-md-1" style="background-color:#4D051D; color:#fff;">
                           <b> Mini Master</b>
                        </div>
                         <div class="col-md-2">
                        </div>
                        
                    </div>      
             
              <?php
			  ########################## pagination details fetch starts here ###################################
             $inc=1;
			  ?>	 
              
<!--              <div class="searchlistdiv" id="searchlistdiv"> 
                <div class="myprintheader"><b><?php echo $forma; ?> : <span id="totCounter"><?php echo $pgoutput['totrecords']; ?></span></b>
                <span class="example">(Showing result : <strong><?php echo $inc;?> to <?php echo $lastinc;?></strong> out of <strong><?php echo $pgoutput['totrecords']; ?></strong>)</span>
                <br /><?php echo $filterused; ?></div> -->
                 <div>
                     <style> th {
    background-color: #C7CDC8;
    color:#000;
}</style>
                    <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                        
                        
                        <thead>  
                    <tr class="search1tr">
<!--                           <th></th>-->
                      <th class="sno">S.No</th>
                      <th>Item Code</th>
                      <th>Item Name</th>
                      <th>Item Weight(in gm(s))</th>
                      <th>Unit(in pieces)</th>                     
                      <th>MRP(in ₹)</th>                     
                      <th>Rate(in ₹)</th>                     
                      <th>Product Category</th>
                      <th>Packing Type</th>
                      <th class="hidden">Options</th>
                    </tr>
                        </thead>
                  <?php 
                  $bg = TR_ROW_COLOR1;
                  //$inc = 1;
                  if(isset($_GET['ajaxshow'])) ob_end_clean(); // to help refresh a single row

                  // pre($rs);
                  foreach($rs as $key=>$rows)
                  {     //pre($rows);
                       if($rows['c1']=='Sprinklers')
                              {
                                 $color = "#F67C0F";
                                 $font = "#000";
                              }
                              else if($rows['c1']=='Straight Premium')
                              {
                                 $color = "#46154D";
                                 $font = "#FFF";
                              }
                              else if($rows['c1']=='Specialities Spices')
                              {
                                 $color = "#0A87B2";
                                 $font = "#FFF";
                              }
                              else if($rows['c1']=='Blends')
                              {
                                 $color = "#AD0606";
                                 $font = "#fff";
                              }
                              else if($rows['c1']=='Hing')
                              {
                                 $color = "#D2DC13";
                                 $font = "#000";
                              }
                              else if($rows['c1']=='Whole')
                              {
                                 $color = "#4D59F0";
                                 $font = "#fff";
                              }
                              else if($rows['c1']=='Focus')
                              {
                                 $color = ""; 
                                 $font = "#000";
                              } 
                                
                      
                      
                      $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                      $uid = $rows['id'];
                      $uidname = $rows['item_name'];
				  
                      $editlink = '<a class="iframef" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'"><img src="./images/b_edit.png"></a>';
                      $deletelink = '<span class="seperator">|</span> <a href="javascript:void(0);" onclick="do_delete(\'Catalog Product Delete\', \''.$uid.'\',\'Product\',\''.addslashes($uidname).'\');"><img src="./images/b_drop.png"></a>';
                      
                       //$product = '<span class="seperator">|</span> <a class="iframef" href="indexpop.php?option=product-details&mode=1&id='.$uid.'"><img src="./images/goods.png"></a>';
                      
                       //$cname=$company_name[$rows['company_id']];
                      //if($rows['locked'] == 1) $editlink = $deletelink = '';
                      $image = ($rows['image_name'] == '') ? '' : '<a href="../myuploads/product/'.$rows['image_name'].'" class="youtube cboxElement"><img src="../myuploads/product/'.$rows['image_name'].'" height="20" width="20"></a>';
                      $taxable = ($rows['taxable'] == '0') ? 'No' : 'Yes';
                      if($auth['del_opt'] !=1) $deletelink = '';
                     ?>
                          <tr style="background-color:<?=$color?>; color:<?=$font?>">
<!--                                <td class="center">
                                    <label class="pos-rel">
                                        <input type="checkbox" class="ace" />
                                        <span class="lbl"></span>
                                    </label>
                                </td>-->
                                <td>
                                  <?=$inc?>
                                </td>
                                <td><?=$rows['itemcode']?></td>
                                <td><?=$rows['name']?><?php          echo' <span style="float:right;"> <image src="images/3D packs/'.$rows['id'].'.png" height="42" width="42"  /></span>'; ?></td>
                                <td><?=$rows['weight']?></td>
                                <td><?=$rows['piece']?></td>
                                <td><?=$rows['mrp']?></td>
                                <td><?=$rows['dealer_rate']?></td>
                                <td><?=$rows['c1']?></td>
                                <td><?=$rows['packing_type']?></td>
                                <td class="hidden"><?=$rows['weight']?></td>
                        <?php
//                      echo'
//                          
//                      <tr style="BGCOLOR:'.$color.';" id="tr'.$uid.'" class="ihighlight">
//                        <td class="myintrow myresultrow">'.$inc.'</td>
//                        <td>'.$rows['itemcode'].'</td>
//                        <td>'.$rows['name'].'</td>  
//                        <td>'.$rows['weight'].'</td>  
//                        <td><strong>'.$rows['unit'].'</strong><div style="display:none" id="delDiv'.$uid.'"></div></td>  
//                        <td >'.$rows['rate'].'</td>                        
//                        <td>'.$rows['c1'].'</td>
//                        <td>'.$rows['packing_type'].'</td>
//                        <td class="hidden">'.$editlink.$product.$deletelink.'</td>
//                      </tr>
//                      ';
                      
                      
                      
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
      </fieldset>
      </form>
      <?php //}//if(isset($_GET['showmode']) && $_GET['showmode'] == 1){ ends here?>
         </div> </div> </div> </div>
     </div><!-- workarea div ends here -->
      <script type="text/javascript">setfocus('name');</script>
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
                                                null,null,null,null,null,null,null,null,
                                                 
                                                {"bSortable": false}
                                            ],
                                            "aaSorting": [],

                                            //"bProcessing": true,
                                            //"bServerSide": true,
                                            //"sAjaxSource": "http://127.0.0.1/table.php"	,

                                            //,
                                            //"sScrollY": "200px",
                                            //"bPaginate": false,

                                            //"sScrollX": "100%",
                                            //"sScrollXInner": "120%",
                                            //"bScrollCollapse": true,
                                            //Note: if you are applying horizontal scrolling (sScrollX) on a ".table-bordered"
                                            //you may want to wrap the table inside a "div.dataTables_borderWrap" element

                                            //"iDisplayLength": 50


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





                                /**
                                 //add horizontal scrollbars to a simple table
                                 $('#simple-table').css({'width':'2000px', 'max-width': 'none'}).wrap('<div style="width: 1000px;" />').parent().ace_scroll(
                                 {
                                 horizontal: true,
                                 styleClass: 'scroll-top scroll-dark scroll-visible',//show the scrollbars on top(default is bottom)
                                 size: 2000,
                                 mouseWheelLock: true
                                 }
                                 ).css('padding-top', '12px');
                                 */


                            })
        </script>
     
    
