<?php
//include'table.php';
if (!defined('BASE_URL'))
    require_once('../../../page_not_direct_allow-depth.php');
?>
<?php
$forma = 'Dealer User'; // to indicate what type of form this is
$formaction = $p;
$myobj = new dealer_user();
$cls_func_str = 'dsp'; //The name of the function in the class that will do the job
$myorderby = 'person.id DESC'; // The orderby clause for fetching of the data
$myfilter = 'id = '; //the main key against which we will fetch data in the get_item_category_function
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS . 'data']['id'], $formaction);
//pre($_SESSION[SESS . 'data']);
$js_disable = "";
?> 
<?php
stop_page_view($auth['view_opt']); // checking the user current page view
# -------------------------------- code for handling of the previous, first and next button starts here 
list($open, $first, $prev, $next, $last, $eformaction) = prev_next($id = 'id', $table = 'person', $formaction);
# -------------------------------- code for handling of the previous, first and next button ends here
//$ss =$myobj->get_parent_role(4);

function checkform($mode = 'add', $id = '') {
    global $dbc;
    // checking whether the Category name is left empty or not
    //checking whether the same name exists or not

    if ($mode == 'filter')  return array(TRUE, '');
       
    $field_arry = array('mobile' => $_POST['mobile'] . '<$>Mobile'); // checking for  duplicate region name
    if ($mode == 'add') {
        if (uniqcheck_msg($dbc, $field_arry, 'person', false))
            return array(FALSE, '<b>Mobile</b> already exists, please provide a different value.');
    }
    elseif ($mode == 'edit') {
        if (uniqcheck_msg($dbc, $field_arry, 'person', false, "id != '$_POST[eid]'"))
            return array(FALSE, '<b>Mobile</b> already exists, please provide a different value.');
    }
    return array(TRUE, '');
}

// code to add new region starts here
if (isset($_POST['submit']) && $_POST['submit'] == 'Save') {
    if (valid_token($_POST['hf'])) { // checking if post value is same as timestamp stored in session during form load
        //calculating the user authorisastion for the operation performed, function is defined in common_function
        list($checkpass, $fmsg) = user_auth_msg($auth['add_opt'], $operation = 'add', $id = '');
        if ($checkpass) {
            // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
            magic_quotes_check($dbc, $check = true);
            $funcname = $cls_func_str . '_save';
            $action_status = $myobj->$funcname(); // $myobj->item_category_save()
            if ($action_status['status']) {
                echo '<span class="asm">' . $action_status['myreason'] . '</span>';
                //show_row_change(BASE_URL_A.'?option='.$formaction, $action_status['rId']);
                unset($_POST);
                /* echo'<script type="text/javascript">ajax_refresher(\'vendorId\', \'getvendor\', \'\');</script>'; */
                //unset($_SESSION[SESS.'securetoken']); 		
            } else
                echo '<span class="awm">' . $action_status['myreason'] . '</span>';
        } else
            echo'<span class="awm">' . $fmsg . '</span>';
    } else
        echo'<span class="awm">Please do not try to hack the system.</span>';
}
// code to edit region starts here
if (isset($_POST['submit']) && $_POST['submit'] == 'Update') {
    if (valid_token($_POST['hf'])) { // checking if post value is same as timestamp stored in session during form load
        //calculating the user authorisastion for the operation performed, function is defined in common_function
        list($checkpass, $fmsg) = user_auth_msg($auth['edit_opt'], $operation = 'edit', $id = $_POST['eid']);
        if ($checkpass) {
            // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
            magic_quotes_check($dbc, $check = true);
            $funcname = $cls_func_str . '_edit';
            $action_status = $myobj->$funcname($_POST['eid']); // $myobj->item_category_edit()
            if ($action_status['status']) {
                echo '<span class="asm">' . $action_status['myreason'] . '</span>';
                //unset($_SESSION[SESS.'securetoken']); 
                //show_row_change(BASE_URL_A.'?option='.$formaction, $_POST['eid']);
                unset($_POST);
            } else
                echo '<span class="awm">' . $action_status['myreason'] . '</span>';
        } else
            echo'<span class="awm">' . $fmsg . '</span>';
    } else
        echo'<span class="awm">Please do not try to hack the system.</span>';
}

//code to get the previous entry details if user is editing  starts here
if (isset($_GET['mode']) && $_GET['mode'] == 1) {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];

        //This will containt the pr no, pr date and other values
        $funcname = 'get_' . $cls_func_str . '_list';
        $mystat = $myobj->$funcname($filter = "$myfilter'$id'", $records = '', $orderby = ''); // $myobj->get_item_category_list()
      
        if (!empty($mystat)) {
            //geteditvalue_class($eid=$id, $in = $mystat, $labelchange=array(), $options)
            geteditvalue_class($eid = $id, $in = $mystat);
            //This will create the post multidimensional array
            $heid = '<input type="hidden" name="eid" value="' . $id . '" />';
        } else
            echo '<span class="awm">Sorry, no such ' . $forma . ' found.</span>';
    }
}

//code to get the previous entry details if user is editin ends here
############################# Code to handle the user search starts here ###############################
$rs = array();
$filterused = '';
$funcname = 'get_' . $cls_func_str . '_list';
if (isset($_POST['filter']) && $_POST['filter'] == 'Filter') {
    if (valid_token($_POST['hf'])) { // checking if post value is same as timestamp stored in session during form load
        //calculating the user authorisastion for the operation performed, function is defined in common_function
        list($checkpass, $fmsg) = checkform('filter');
        if ($checkpass) {
            // triming and stripslashing post data if required and also passing it through mysqli_real_escape_string
            magic_quotes_check($dbc, $check = true);
            $filter = array();
            $filterstr = array();
            if (!empty($_POST['name'])) {
                $filter[] = "CONCAT_WS(' ',first_name,middle_name,last_name)  LIKE '%$_POST[name]%'";
                $filterstr[] = '<b>Name  : </b>' . $_POST['name'];
            }
            if (!empty($_POST['mobile'])) {
                $filter[] = "mobile = '$_POST[mobile]'";
                $filterstr[] = '<b>Mobile  : </b>' . $_POST['mobile'];
            }
            
            $filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');
            $rs = $myobj->$funcname($filter, $records = '', $orderby = "ORDER BY $myorderby"); // $myobj->get_item_category_list()
            if (empty($rs))
                echo '<span class="awm">Sorry, <strong>no record</strong> found.</span>';
        } else
            echo'<span class="awm">' . $fmsg . '</span>';
    } else
        echo'<span class="awm">Please do not try to hack the system.</span>';
}elseif (isset($_GET['ajaxshow']) || isset($_GET['ajaxshowblank'])) {
    $ajaxshowid = isset($_GET['ajaxshow']) ? $_GET['ajaxshow'] : $_GET['ajaxshowblank'];
    $rs = $myobj->$funcname($filter = "$myfilter'$ajaxshowid'", $records = '', $orderby = '');
} else {  
    $filter[] = " (user_dealer_retailer.dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}' OR dealer_person.dealer_id = '{$_SESSION[SESS.'data']['dealer_id']}') AND person_login.person_status !=9";
            
    $rs = $myobj->$funcname($filter, $records = '', $orderby = " ORDER BY $myorderby");
   // pre($rs);
}
?>
<?php if (isset($_GET['showmode']) && $_GET['showmode'] == 1) { // to show the form when and only when needed?>
        <form method="post" action="<?php if (isset($eformaction)) echo $eformaction; ?>" name="genform" onsubmit="return checkForm('genform');">
            <fieldset>
                <legend class="legend" style="background-color: #CDEAF3;font-size: 150%;font-family: Arial, Georgia, Serif;"> <?php echo $forma; ?> </legend>
                <input type="hidden" name="hf" value="<?php echo $securetoken; ?>">
                <?php if (isset($heid)) echo $heid; ?>
                <table width="100%" border="0" cellspacing="5" cellpadding="5">
                    <tr>
                        <td colspan="4"><div style="background-color: #CDEAF3;font-size: 150%;font-family: Arial, Georgia, Serif;">DSP Details</div></td>
                    </tr>
                    <tr>
                        <td><span class="star">*</span>Company<br> 
    <?php
    $js_attr = ' lang="company_id" ';
    if (!isset($_POST['company_id'])) {
        $q = 'SELECT id, name from company';
        db_pulldown($dbc, 'company_id', $q, TRUE, TRUE, $jsattr);
    } else {
        ?>
        <select name="company_id" id="state_id" lang="company">
            <option value="">== Please Select ==</option>
        <?php
        $q = 'select id,name FROM company  ';
        $st_res = mysqli_query($dbc, $q);
        while ($row = mysqli_fetch_array($st_res)) {
            ?>
                                        <option value="<?php echo $row['id']; ?>" <?php if ($_POST['company_id'] == $row['id']) echo 'selected = "selected"' ?>><?php echo $row['name'] ?></option>
            <?php
        }
        ?>
                                </select>
                            <?php } //echo $q;
                            ?>
                        </td>
                        <td><span class="star">*</span>First Name <br>
                            <input onChange="this.value = ucwords(trim(this.value));" type="text" id="rname" lang="First Name" name="first_name" value="<?php if (isset($_POST['first_name'])) echo $_POST['first_name']; ?>">
                        </td>
                        <td>Middle Name <br>
                            <input onChange="this.value = ucwords(trim(this.value));" type="text" name="middle_name" value="<?php if (isset($_POST['middle_name'])) echo $_POST['middle_name']; ?>">
                        </td>
                        <td><span class="star">*</span>Last Name <br>
                            <input onChange="this.value = ucwords(trim(this.value));" lang="last_name" type="text" name="last_name" value="<?php if (isset($_POST['last_name'])) echo $_POST['last_name']; ?>"
                        </td>
                        
                        
                      <?php if(isset($_SESSION[SESS.'data']['company_id'])) echo '<input type="hidden" name="company_id" value="'.$_SESSION[SESS.'data']['company_id'].'">'; ?>
                    </tr>
                    <tr> 
                        <td>
                            <span class="star">*</span> User Designation <Br/>
                            <?php
                               db_pulldown($dbc, 'role_id', "SELECT role_id, rolename FROM `_role` WHERE role_group_id = '22' ORDER BY rolename ASC", true, true, $jsfunction='', '== Plese Select ==');
                            ?>
                        </td>
                        
                        <td>
                            <span class="star">*</span> Account Active <Br/>
                            <?php
                            if (!isset($heid))
                                $_POST['person_status'] = 1;
                            arr_pulldown('person_status', array('1' => 'Active', '0' => 'Deactive'), $msg = '', $usearrkey = true, $ini_option = false, $jsfunction = 'lang="Select Account Active"', false);
                            ?>

                        </td>
                        <td>
                            Email<Br/>
                            <input type="text" onchange="checkemail('email');" id="email" name="email" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" maxlength="60" />
                        </td>
                         <td>
                            <span class="star">*</span>
                            Mobile<Br/>
                            <input type="text" lang="mobile" onkeypress="return isNumberKey(event);" name="mobile" value="<?php if (isset($_POST['mobile'])) echo $_POST['mobile']; ?>" maxlength="10" />
                        </td>          
                    </tr>
                  
                    <tr>
                        <td colspan="4"><div style="background-color: #CDEAF3;font-size: 150%;font-family: Arial, Georgia, Serif;">DSP Address Details</div></td>
                    </tr>
                    <tr>
                    <td>
                        Address<textarea name="address"><?php if (isset($_POST['address'])) echo $_POST['address']; ?></textarea></td>
                    <td><span class="star">*</span>Gender<br> 
                        <label> <input type="radio" <?php
                            if (isset($_POST['gender']) && $_POST['gender'] == 'M')
                                echo 'checked="checked"';
                            else
                                echo 'checked="checked"';
                            ?> name="gender" value="M">Male </label> 
                        <label><input type="radio" <?php if (isset($_POST['gender']) && $_POST['gender'] == 'F') echo 'checked="checked"'; ?> name="gender" value="F"> Female</label></td>
                    <td>Dob<span class="example"> (<span class="star">*</span>dd/mm/YYYY)</span> <input type="text" name="dob" class="datepicker" id="dob" value="<?php if (isset($_POST['dob'])) echo $_POST['dob']; ?>"></td>
                    <td>Alternate Number <input type="text" name="alternate_number" id="alternate_number" value="<?php if (isset($_POST['alternate_number'])) echo $_POST['alternate_number']; ?>"></td>
                       
                    </tr>
           
                    <tr>

                        <td align="center" colspan="4">
                            <?php //form_buttons(); // All the form control button, defined in common_function   ?>
                            <input style="background-color: #428bca;" id="mysave" type="submit" name="submit" value="<?php if (isset($heid)) echo'Update';
                            else echo'Save'; ?>" />
                            <?php
                            if (isset($heid)) {
                                echo $heid; //A hidden field name eid, whose value will be equal to the edit id. 
                                ?>
                            <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Exit" /><br />  
<!--                                <input onclick="parent.$.fn.colorbox.close();" type="button" value="Exit" /> <br />-->
                                <?php edit_links_via_js($id, $jsclose = true, $options = array()); ?>            
                            <?php } else { ?>
                              <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />
                            <?php } ?>
                        </td>

                    </tr>
                </table>
            </fieldset>
        </form>
<?php } else {?>
                            <?php
                            //pre($rs);
                            ########################## pagination details fetch starts here ###################################
                         ?>
                    <div class="row">
    <div class="col-xs-12">
        <!-- PAGE CONTENT BEGINS -->

        <div class="row">
            <div class="col-xs-12">
              
                <br>
                                   
                <div class="table-header">
                   User List <div class="pull-right tableTools-container"></div> 
                   
                </div>

                <!-- div.table-responsive -->
<?php
//pre($rs);
?>
                <!-- div.dataTables_borderWrap -->
                <div>
                    <style> th {
    background-color: #C7CDC8;
    color:#000;
}</style>
                    <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
<!--                                <th  class="center">
                                    <label class="pos-rel">
                                        <input type="checkbox" class="ace" />
                                        <span class="lbl"></span>
                                    </label>
                                </th>-->
                                <th >S.No</th>
                                <th >Name</th>
								<th >Mobile</th>
                                <th >Email</th>
                                <th >Designation</th>
								<th >Division</th>
                                <th >A/c Status</th>
                                <th  colspan="2" class="hidden"><center>Options</center></th>
                               </tr>
                        </thead>

                        <tbody>
                            <?php
                            $inc = 1;
                            foreach($rs as $key=>$value)
                            {$uid = $value['id'];
                            $uidname = $value['name'];
							if($value['division']==1){
								$division='Mainline';
							}elseif($value['division']==2){
								$division='Ethical';
							}
                             $editlink = '<a class="iframeFix" target="popup" href="index.php?option='.$formaction.'&showmode=1&mode=1&id='.$uid.'"><img src="./images/b_edit.png"></a>&nbsp;&nbsp;';

//                              $editlink = '<a class="iframef" href="index.php?option=' . $formaction . '&showmode=1&mode=1&id=' . $uid . '"><img src="./images/b_edit.png"></a>';
                              $dealer_person = '<a title="Add Dealer Person" class="iframef" href="indexpop.php?option=person-dealer&mode=1&id=' . $uid . '"><img src="./images/user.png"></a>';
                              $deletelink = '<a href="javascript:void(0);" onclick="do_delete(\'Person Delete\', \'' . $uid . '\',\'Person\',\'' . addslashes($uidname) . '\');"><img src="./images/b_drop.png"></a>';
                                           
                                ?>
                                <tr>
<!--                                <td class="center">
                                    <label class="pos-rel">
                                        <input type="checkbox" class="ace" />
                                        <span class="lbl"></span>
                                    </label>
                                </td>-->
                                <td>
                                    <?=$inc?>
                                </td>
                                <td>
                                   <?=strtoupper($value['name'])?>
                                </td>
								<td><?=$value['mobile']?></td>
                                <td><?=$value['email']?></td>
                                <td><?=$value['rolename']?></td>
								<td><?=$division?></td>
                                <td><?=($value['person_status'] == 0 ? 'Deactive' : 'Active')?></td>
                                 <td class="hidden"><?=$editlink?></td>
                                <td class="hidden"><?=$deletelink?></td>
                            </tr>

                          <?php $inc++;
                            }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- PAGE CONTENT ENDS -->

    </div><!-- /.row -->
</div><!-- /.page-content -->
            <?php
            echo'</div>';
}     
        ?>           
        <?php if (isset($_GET['ajaxshowblank'])) exit(); // to show the first row when parent table not avialable  ?>
          
   
  
</div><!-- workarea div ends here -->
<script type="text/javascript">setfocus('partycode');</script>
      <script src="assets/js/jquery-2.1.4.min.js"></script>
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
                                                null, null, null, null, null,null,null,
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
                                /***************/



                            })
        </script>
