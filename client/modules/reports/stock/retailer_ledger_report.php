<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
$forma = 'RETAILER LEDGER REPORT'; // to indicate what type of form this is
$formaction = $p;
$myobj = new sale();
$cls_func_str = 'retailer_ledger'; //The name of the function in the class that will do the job
$myorderby = ''; // The orderby clause for fetching of the data
$myfilter = 'id ='; //the main key against which we will fetch data in the get_item_category_function
// Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS.'data']['id'], $formaction);
//pre($_SESSION[SESS.'constant']);
$location_level = $_SESSION[SESS.'constant']['dealer_level'];
$dealer_level = $_SESSION[SESS.'constant']['dealer_level'];
$retailer_level = $_SESSION[SESS.'constant']['retailer_level'];
$dealer_id = $_SESSION[SESS . 'data']['dealer_id'];

$sesId = $_SESSION[SESS.'data']['id'];
$role_id = $_SESSION[SESS.'data']['urole'];
$editlink="";
$personlink="";
$deletelink="";
?>
<?php
//stop_page_view($auth['view_opt']); // checking the user current page view

############################# code for checking of submitted form data starts here data starts here ########################
function checkform($mode='add', $id='')
{ 
	global $dbc;

	if($mode == 'filter') return array(TRUE, '');
	$field_arry = array('firm_name' => $_POST['firm_name']);// checking for  duplicate Unit Name
	
	if($mode == 'add'){
		if(uniqcheck_msg($dbc,$field_arry,'retailer', false, ""))
			return array(FALSE, '<b>Retailer Name</b> already exists, please provide a different value.');
	}elseif($mode == 'edit'){
		if(uniqcheck_msg($dbc,$field_arry,'retailer', false," id != '$_GET[id]'"))
			return array(FALSE, '<b>Retailer Name</b> already exists, please provide a different value.');
	}
	return array(TRUE, '');
}

############################# Code to handle the user search starts here ###############################
$rs = array();
$filterused = '';
$filterexcel = '';
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
      
      if (!empty($_POST['from_date'])) {
        $start = get_mysql_date($_POST['from_date'], '/', $time = false, $mysqlsearch = true);
        $filter[] = "DATE_FORMAT(`ch_date`,'" . MYSQL_DATE_SEARCH . "') >= '$start'";
        $filterstr[] = '<b>Start : </b>' . $_POST['from_date'];
      }
      if (!empty($_POST['to_date'])) {
        $end = get_mysql_date($_POST['to_date'], '/', $time = false, $mysqlsearch = true);
        $filter[] = "DATE_FORMAT(`ch_date`,'" . MYSQL_DATE_SEARCH . "') <= '$end'";
        $filterstr[] = '<b>End : </b>' . $_POST['to_date'];
      }

      if (!empty($_POST['retailer_id'])) {
       $filter[] = "ch_retailer_id = '$_POST[retailer_id]'";
       $filterstr[] = '<b>retailer_id : </b>' . $_POST['retailer_id'];
             } // print_r($filter);

             $filter[] = "ch_dealer_id = '$dealer_id'";
             $filterused = implode($filterstr, '<span style="margin: 0 10px;">|</span>');			 
            
             $rs = $myobj->get_retailer_ledger_list($filter,  $records = '', $orderby ="");  
               
                if(empty($rs))
                  echo '<span class="awm">Sorry, <strong>No record</strong> found.</span>';
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
       
//dynamic_js_enhancement();

       ?>
       <script type="text/javascript">
        $(function() {
         $(".retailer").autocomplete({
          source: "./modules/ajax-autocomplete/user/ajax-retailer-name.php"
        });

       });
        function get_user_wise_dealer(user_id)
        {
          var city = document.getElementById('get_city_value').value;
          var city_id = document.getElementById('location_'+city).value;
          var pulldata = user_id+'|'+city_id+'|'+city;

          if(pulldata == '') return;
          fetch_location(pulldata, 'progress_div', 'dealer_id', 'get_dealer_id');
        }
      </script>
      <br>
      <div id="workarea">
       <form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform1" onsubmit="return checkForm('genform1');">
         <table width="100%" border="0" cellspacing="2" cellpadding="2">
           <tr id="mysearchfilter">
             <td>
               <!-- this table will contain our form filter code starts -->
               <fieldset>

                 <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
                 <table>

                   <div class="col-xs-2">From Date<br />
                     <input type="text" name="from_date" id="fdate" class="datepicker" value="<?php if (isset($_POST['from_date'])) echo $_POST['from_date'];
                     else echo date('d/M/Y'); ?>" />
                   </div>
                   <div class="col-xs-2">To Date<br />
                    <input type="text" id="tdate" name="to_date" class="datepicker" value="<?php if (isset($_POST['to_date'])) echo $_POST['to_date'];
                    else echo date('d/M/Y'); ?>" />
                  </div>
                  <div class="col-xs-2">Retailer Name<span style="color:RED">*</span><br />       
                    <?php
                    $qrt = "SELECT retailer.id as id, CONCAT(retailer.name,' [',location_5.name,'] ')as name FROM retailer INNER JOIN location_5 ON retailer.location_id = location_5.id where retailer.dealer_id ='" . $_SESSION[SESS . 'data']['dealer_id'] . "' AND retailer_status='1' group by retailer.id ORDER BY retailer.name ASC  ";
                    db_pulldown($dbc, 'retailer_id', $qrt, true, true, 'id="retailer" lang="Retailer" onchange="getdata(this.value, \'progress_div\', \'get-retailer-location\', \'location_id\');"','=Please Select=',$_POST['retailer_id']);
                    ?>
                  </div>       <div class="col-xs-6"> <br>
                   <input class="btn btn-sm btn-primary" id="mysave" type="submit" name="filter" value="Filter" />

                 </div>
               </tr>
             </table>
           </fieldset>
           <!-- this table will contain our form filter code ends -->           
         </td>
       </tr>
     </table>
     <?php

	if(isset($_GET['ajaxshowblank'])) ob_end_clean(); // to show the first row when parent table not avialable
    //if(!empty($rs))
        { //if no content available present no need to show the bottom part
          ?>

          <div class="row">
            <div class="col-xs-12">
             <div class="row">
              <div class="col-xs-12">

                <br>
                
                <div class="table-header">
                  <?=$forma?>  <strong><span style="margin-left:20%">
                    Period: <?php 

                    echo $_POST['from_date']; ?> 
                    To <?php 

                    echo $_POST['to_date']; ?></span>
                  </strong><div class="pull-right tableTools-container">
                    <input type="button" onclick="tableToExcel('dynamic-table', 'W3C Example Table')" value="Export to Excel" class="btn btn-success" style="padding: 4px;">
                  </div>
                </div>

                <?php $total1=0;?>                
                <div>
                 <style> th {
                  background-color: #C7CDC8;
                  color:#000;
                }</style>

                <?php                    

                    $ret_data=0;
                    $dq = mysqli_query($dbc,"SELECT name,address,landline,tin_no  FROM `dealer` WHERE `id` = $dealer_id");
                    $dealer_data = mysqli_fetch_assoc($dq);

                    if(isset($_POST['retailer_id']))
                    {
                      $retailer_id = $_POST['retailer_id'];
                      //h1("SELECT name,address FROM `retailer` WHERE `dealer_id` = $dealer_id AND id = $retailer_id");
                      $rq = mysqli_query($dbc,"SELECT name,address FROM `retailer` WHERE `dealer_id` = $dealer_id AND id = $retailer_id");
                      $ret_data = mysqli_fetch_assoc($rq);                     
                    }                   

                 ?>

                <table class="table table-striped table-bordered table-hover">
                  
                </table>                
                <table id="dynamic-table" class="table table-striped table-bordered table-hover">

                  <tr>
                    <td colspan="3">
                      <table>
                        <tr>
                          <td colspan="2"><b><?php echo $dealer_data['name'] ?></b></td>
                        </tr>
                        <tr>
                          <td>GSTIN No.-</td>  
                          <td><?php echo $dealer_data['tin_no'] ?></td>
                        </tr>
                        <tr>
                          <td>Address-</td>  
                          <td><?php echo $dealer_data['address'] ?></td>
                        </tr>
                        <tr>
                          <td>Phone No.-</td>  
                          <td><?php echo $dealer_data['landline'] ?></td>
                        </tr>
                      </table>
                    </td>
                    <td colspan="3">
                      <?php 

                        if($ret_data)
                        { ?>
                          <table>
                            <tr>
                              <td>Retailer Name-</td>
                              <td><?php echo $ret_data['name'] ?></td>
                            </tr>
                            <tr>
                              <td>Address</td>
                              <td><?php echo $ret_data['address'] ?></td>
                            </tr>
                          </table>
                      <?php  }  ?>
                      
                    </td>
                  </tr>
                    <tr class="search1tr">
                      <th style="color:#000;">Date</th>
                      <th style="color:#000;">Voucher No</th>
                      <th style="color:#000;">Narration</th>
                      <th style="color:#000;">Payment</th>
                      <th style="color:#000;">Receipt</th>
                      <th style="color:#000;">Balance</th>
                    </tr>                    
                 
                 
                  <?php 
                  $bg = TR_ROW_COLOR1;
                  $inc = 1; 
                  $total_payment = 0;
                  $total_balance = 0;
                  $new_bal = 0;

                  
                  /* Fetching date of very first challan of the retailer. */

                  $cond = "ch_dealer_id='$dealer_id'";
                  if(isset($_POST['retailer_id']))
                  {
                    $retailer_id = $_POST['retailer_id'];
                    $cond .= " AND ch_retailer_id='$retailer_id'";
                  }

                  $first_date = myrowval('challan_order','MIN(auto)',$cond);

                  foreach($rs as $key=>$rows)
                  {
                    $ch_date = $rows['ch_date'];
                    $real_date = $rows['auto'];
                    $amount = $rows['remaining'];

                    $new_bal += $amount;

                    $narration = ($real_date==$first_date)?"Opening balance":"Sale";

                    $total_payment += $amount;
                    $total_balance += $amount;

                    echo'
                    <tr BGCOLOR="'.$bg.'" class="ihighlight">
                    <td>'.$ch_date.'</td>
                    <td>'.$rows['ch_no'].'</td>
                    <td>'.$narration.'</td>
                    <td>'.round($amount).'</td>
                    <td></td>
                    <td>'.round($new_bal).'</td>
                    </tr>';
                    $inc++;
                  }// foreach loop ends here
                   // to help refresh a single row
                  ?>

                </div> 
                <?php echo'</div>';} // foreach($pgoutput['temp_result'] as $key=>$value){ ends?>           
                <tr>
                  <td colspan="3"><b>Grand Total</b></td>
                  <td><b><?php echo round($total_payment) ?></b></td>
                  <td></td>
                  <td><b><?php echo round($total_balance) ?></b></td>
                </tr>
              
            </table>
          </div>
        </div>
      </div>


      <!-- PAGE CONTENT ENDS -->

    </div><!-- /.row -->
  </div><!-- /.page-content -->  
  <?php if(isset($_GET['ajaxshowblank'])) exit(); // to show the first row when parent table not avialable ?>

  <?php if(isset($pgoutput)) echo $pgoutput['pglinks']; // showing the paginataion links to the user?>
</fieldset>
</form>

</div><!-- workarea div ends here -->

<script src="assets/js/jquery-2.1.4.min.js"></script>
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
                                          {"bSortable": false}, {"bSortable": false},
                                          {"bSortable": false}, {"bSortable": false},
                                          {"bSortable": false}, {"bSortable": false},
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
//                                            "text": "<i class='fa fa-file-excel-o bigger-110 green'>Excel</i> <span class='hidden'>Export to Excel</span>",
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
