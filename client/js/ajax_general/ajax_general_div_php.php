<?php

@session_start();
ob_start();
//require_once(BASE_URI_ROOT.ADMINFOLDER.MSYM.'include'.MSYM.'my-ajax.php');
require_once('../../include/config.inc.php');

require_once(BASE_URI_ROOT.ADMINFOLDER.MSYM.'include'.MSYM.'my-functions.php');

// This function will prepare the ajax response text which will be send to ajax call ends here
if(isset($_SESSION[SESS.'user']))
{
	//if at some instance we are making a post request
	if(isset($_POST['wcase'])){$_GET['pid'] = $_POST['pid']; $_GET['wcase'] = $_POST['wcase'];}
	// This function will prepare the ajax response text which will be send to ajax call ends here
	if(isset($_GET['pid']) && !empty($_GET['pid']))
	{
		$id = $_GET['pid'];
		$wcase = $_GET['wcase'];
		switch($wcase)
		{			
			case'input-format': // from the template page
			{
				$filepath = BASE_URI_ROOT.ADMINFOLDER.SYM.'modules'.SYM.'master'.SYM.'template'.SYM.'input-formats'.SYM.'input-format-'.$id.'.php';
				if(is_file($filepath)){
					echo'TRUE<$>';
					require_once($filepath);
				}else
					echo'FALSE<$>Input format not available';
				break;
			};
			case'po_item': // from the gate-entry-po
			{
				$po = new po();
				$poitem = $po->get_po_list("poId = $id");
				if(empty($poitem)){
					echo'FALSE<$>Sorry no such PO available';					
					exit();
				}
				$poitem = $poitem[$id];
				//Getting an array to create the pulldown
				$itemar = array();
				foreach($poitem['po_item'] as $key=>$value) $itemar[$value['itemId']] = $value['itemname'];
				
				//If user is editing a challan, then the qty of current challan will not be counted in the received qty
				echo'TRUE<$>';?>
                <table width="100%" id="mytable">
                  <tr style="font-weight:bold;">
                    <td class="sno">S.No</td>
                    <td>Item Name</td>
                    <td>Order Qty</td>
                    <!--<td>Balance</td>-->
                    <td>Received Qty</td>
                    <td style="width:40px;">&nbsp;</td>
                  </tr>
                  <?php //$inc = 1; foreach($poitem['po_item'] as $key=>$value){?>
                  <tr class="tdata">
                    <td class="myintrow">1</td>
                    <td>
                    <input type="hidden" id="poId" value="<?php echo $id;?>"  />
                     <?php arr_pulldown('itemId[]', $itemar, '', true, true, 'onchange="getajaxdata(\'gate_qty\', \'mytable\',event);"', false, '', '');  ?> 
                    </td>
                    <td>
                     <input type="text" name="poqty[]"  value=""  />
                    </td>
                    <!--<td>
                     <input type="text" readonly="readonly" name="balqty[]"  value=""  />
                    </td>-->
                    <td>
                     <input type="text" name="qty[]" onblur="chk_balance();"  value=""  />
                    </td>
                    <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable', event);"/></td>
                  </tr>
                  <?php // $inc++;} //foreach($wodata_item as $key=>$value){ ends?>
                </table>
                <?php
				break;
			}
                        case 'get-company-import-locality':
                        {
                            $myobj = new company();
                            $rs = $myobj->get_constant_company_data($id);
                            $loc_level = $rs[$id]['location_level'];
                            $str =  '<table width="70%">
                                        <tr>';
                            for($i = 1; $i <= $loc_level; $i++)
                            {
                                  $title = $rs[$id]["location_title_$i"];
                                  $str .= '<td>'.$title.'<input onclick="check_selected_value();" type="checkbox" name="location[]" value="'.$i.'" /></td>';
                           
                            }
                            $str .= '</tr>
                            </table>';
                            echo 'TRUE<$>'.$str;
                            break;
                        }
                         case 'get-company-dynamic-data':
                        {
                            
                            $id = explode('<$>', $id);
                            $location_no = explode(',',$id[0]);
                            $location_table = array();
                            $company_id = $id[1];
                            $myobj = new company();
                            $rs = $myobj->get_constant_company_data($id[1]);
                            
                            $str =  '<table cellpadding="0" cellspacing="0"  width="100%">  <tr valign="top">';       
                            foreach($location_no as $key=>$i)
                            {
                                  if(empty($i)) continue;
                                  $title = $rs[$id[1]]["location_title_$i"];
                                  $str .= '<td><strong>'.$title.'</strong><table>';
                                  $q = "SELECT * FROM location_$i WHERE company_id = '$id[1]'";
                                  list($opt, $rs1) = run_query($dbc, $q,'multi');
                                  if($opt) {
                                      $j = $i - 1;
                                      while($rows = mysqli_fetch_assoc($rs1))
                                      {
                                          $setlabel = '';
                                          if($i == 1) $setlabel = $rows['name'];
                                          if($i > 1){
                                              $name = "location_".$j."_id";
                                              $setlabel = "$rows[name]##$rows[$name]";
                                          }
                                          $str .= '<tr><td><input type="checkbox" name="location_'.$i.'[]" value="'.$setlabel.'">'.$rows['name'].'</td></tr>';
                                      }
                                  }
                                  $str .= '</table></td>';
                            }
                            $str .= '</tr></table>';
                            echo 'TRUE<$>'.$str;
                            break;
                        }
                        //get-company-dynamic-data
			case'wpoId_item': // from the gate-entry-po
			{
				$po = new production();
				$poitem = $po->get_work_po_list("wpoId = $id");
				if(empty($poitem)){
					echo'FALSE<$>Sorry no such Work PO available';					
					exit();
				}
				$poitem = $poitem[$id];
				//Getting an array to create the pulldown
				$itemar = array();
				foreach($poitem['work_po_item'] as $key=>$value) $itemar[$value['itemId']] = $value['itemname'];
				
				//If user is editing a challan, then the qty of current challan will not be counted in the received qty
				echo'TRUE<$>';?>
                <table width="100%" id="mytable">
                  <tr style="font-weight:bold;">
                    <td class="sno">S.No</td>
                    <td>Item Name</td>
                    <td>Quantity</td>
                    <td>LineNo</td>
                    <!--<td>Date.</td>-->
                    <td style="width:40px;">&nbsp;</td>
                  </tr>
                  <?php //$inc = 1; foreach($poitem['po_item'] as $key=>$value){?>
                  <tr class="tdata">
                    <td class="myintrow">1</td>
                    <td>
                     <?php arr_pulldown('itemId[]', $itemar, '', true, true, 'onchange="getajaxdata(\'lineno\', \'mytable\',event);"', false, '', '');  ?> 
                    </td>
                    <td>
                     <input type="text" name="qty[]"  value=""  />
                    </td>
                    <td>
                     <input type="text" name="lineno[]"  value=""  />
                    </td>
                    <!--<td>
                      <input type="text" name="do_item_date[]" class="qdatepicker"  value=""  />
                     </td>-->	
                    <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable', event);"/></td>
                  </tr>
                  <?php // $inc++;} //foreach($wodata_item as $key=>$value){ ends?>
                </table>
                <?php
				break;
			}
                        case'retailer_challan_order_no123': // from the gate-entry-po
			{
				$myobj = new dealer_sale();
                                $company_id = $_SESSION[SESS.'data']['company_id'];
				$challan_no = $myobj->get_retailer_challan_no("ch_retailer_id = '$id' AND company_id = '$company_id'");
				if(empty($challan_no)){
					echo'FALSE<$>Sorry no such challan number available';	
					exit();
				}
                               // echo 'TRUE<$>'.pre($challan_no);
				//$poitem = $poitem[$id];
				//Getting the tax detail as per the vat id found
				//$vatId = $po->get_my_reference_array_direct("SELECT * FROM vat_breakup WHERE vatId = {$poitem['vatId']}", 'taxId');
				//Getting an array to create the pulldown
				$challanr = array();
				foreach($challan_no as $key=>$value) $challanr[$value['ch_no']] = $value['ch_no'];
			//If user is editing a challan, then the qty of current challan will not be counted in the received qty
				
                        echo'TRUE<$>';?>
                            <table width="100%" id="mytable">
                              <tr style="font-weight:bold;">
                                <td class="sno">S.No</td>
                                <td>Challan No</td>                    
                                <td>Total Challan value</td>
                                <td>Payment</td>
                                <td style="width:40px;">&nbsp;</td>
                              </tr>
                              <?php //$inc = 1; foreach($poitem['po_item'] as $key=>$value){?>
                              <tr class="tdata">
                                <td class="myintrow">1</td>
                                <td>
                                 <?php arr_pulldown('challan_no[]', $challanr, '', true, true, 'onchange="getajaxdata(\'get_total_sale_value\', \'mytable\',event);"  onblur="checkuniquearray(\'itemId[]\');"');  ?> 
                                </td>                    
                                <td>
                                 <input type="text" name="total_challan_value[]" id="challan_value" value=""  />
                                </td>
                                <td>
                                 <input type="text" name="pay_amount[]" id="payment" value=""  />
                                </td>
                               
                                <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable', event);"/></td>
                              </tr>
                                  
                            </table>
                            <?php
                            break;
			}  
                        case'retailer_challan_order_no': // from the gate-entry-po
			{
				$myobj = new dealer_sale();
                                $company_id = $_SESSION[SESS.'data']['company_id'];
				$challan_no = $myobj->get_retailer_challan_no("ch_retailer_id = '$id' AND company_id = '$company_id' AND dispatch_status = '1'");
				if(empty($challan_no)){
					echo'FALSE<$>Sorry no such challan number available';	
					exit();
				}
                            //This array is used to create dynamic challan box.
                        echo'TRUE<$>';?>
                            <table width="100%" id="mytable">
                              <?php //$inc = 1; foreach($poitem['po_item'] as $key=>$value){
                              $i = 0;
                              $perrowtd = 4;
                              foreach($challan_no as $inkey=>$invalue)
                              {
                                  if($i == $perrowtd) $i = 0;
                                  if($i == 0) echo '<tr>';
                                  echo '<td><input type="checkbox" onclick="get_total_sale_value();" name="challan_no[]" value="'.$invalue['ch_no'].'">&nbsp;'.$invalue['ch_no'].'</td>';
                                  $i++;
                                  if($i == $perrowtd) echo '</tr>';
                              }
                              if($i < $perrowtd) echo '</td>';
                              ?>
                               <tr>
                                    <td colspan="2">Total Challan Value<br>
                                        <input type="text" class="read" readonly="readonly" name="total_challan_value" id="total_challan_value" value="">
                                   </td>
                                   <td colspan="2">Payment<br>
                                       <input type="text" name="pay_amount" value="">
                                   </td>
                               </tr>
                            </table>
                            <?php
                            break;
			}   
                         case'dealer_challan_order_no': // from the gate-entry-po
			{
				$myobj = new dealer_sale();
                                $company_id = $_SESSION[SESS.'data']['company_id'];
				$challan_no = $myobj->get_retailer_challan_no("ch_dealer_id = '$id' AND company_id = '$company_id' AND dispatch_status = '0'");
                                
				if(empty($challan_no)){
					echo'FALSE<$>Sorry no such challan number available';	
					exit();
				}
                        //This array is used to create dynamic challan box.
                        echo'TRUE<$>';?>
                            <table width="20%" id="mytable">
                              <?php //$inc = 1; foreach($poitem['po_item'] as $key=>$value){
                              $i = 0;
                              $perrowtd = 4;
                              foreach($challan_no as $inkey=>$invalue)
                              {
                                  if($i == $perrowtd) $i = 0;
                                  if($i == 0) echo '<tr>';
                                  echo '<td><input type="checkbox" name="challan_order_id[]" value="'.$invalue['ch_no'].'">&nbsp;'.$invalue['ch_no'].'</td>';
                                  $i++;
                                  if($i == $perrowtd) echo '</tr>';
                              }
                              if($i < $perrowtd) echo '</td>';
                              ?>
                               
                            </table>
                            <?php
                            break;
			}   
			case'wpoId_item_invoice': // from the gate-entry-po
			{
				$po = new production();
				$poitem = $po->get_work_po_list("wpoId = $id");
				if(empty($poitem)){
					echo'FALSE<$>Sorry no such Work PO available';					
					exit();
				}
				$poitem = $poitem[$id];
				//Getting the tax detail as per the vat id found
				$vatId = $po->get_my_reference_array_direct("SELECT * FROM vat_breakup WHERE vatId = {$poitem['vatId']}", 'taxId');
				//Getting an array to create the pulldown
				$itemar = array();
				foreach($poitem['work_po_item'] as $key=>$value) $itemar[$value['itemId']] = $value['itemname'];
				
				//If user is editing a challan, then the qty of current challan will not be counted in the received qty
                        echo'TRUE<$>';?>
                            <table width="100%" id="mytable">
                              <tr style="font-weight:bold;">
                                <td class="sno">S.No</td>
                                <td>Order No</td>                    
                                <td>LineNo</td>
                                <td>No of Packts</td>
                                <td>Quantity</td>
                                <td>Rate</td>
                                <td>Total</td>
                                <td style="width:40px;">&nbsp;</td>
                              </tr>
                              <?php //$inc = 1; foreach($poitem['po_item'] as $key=>$value){?>
                              <tr class="tdata">
                                <td class="myintrow">1</td>
                                <td>
                                 <?php arr_pulldown('itemId[]', $itemar, '', true, true, 'onchange="getajaxdata(\'lineno\', \'mytable\',event);"  onblur="checkuniquearray(\'itemId[]\');"', false, '', '');  ?> 
                                </td>                    
                                <td>
                                 <input type="text" name="lineno[]" id="lineno" value=""  />
                                </td>
                                <td>
                                 <input type="text" name="no_of_pkts[]" id="lineno" value=""  />
                                </td>
                                <td>
                                 <input type="text" name="qty[]" onchange="calculate();"  value=""  />
                                </td>
                                <td>
                                 <input type="text" class="read" readonly="readonly" name="rate[]" onchange="calculate();"  value=""  />
                                </td>
                                <td>
                                 <input type="text" name="total[]"  value=""  />
                                </td>
                                <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable', event);"/></td>
                              </tr>
                              <?php // $inc++;} //foreach($wodata_item as $key=>$value){ ends?>
                              <tr class="tfoot">
                                <td colspan="6" align="right"><strong>Total Amount</strong></td>
                                <td><input type="text" name="totalamount" id="totalamount" value="" /></td>                    
                                <td>&nbsp;</td>
                              <tr class="tfoot">
                                <td colspan="6" align="right">Basic Excise Duty 
                                  <input type="hidden" name="taxname[]" value="<?php echo $vatId['1']['taxname']; ?>"/>
                                  <input type="hidden" name="taxId[]" id="taxid1" value="<?php echo $vatId['1']['taxId']; ?>" style="width:40px;" />
                                  @<input type="text" name="taxvalue[]" id="taxvalue1" value="<?php echo $vatId['1']['taxvalue']; ?>" style="width:40px;"/> %</td>
                                <td><input type="text" name="taxamount[]" id="taxamount1" value="" /></td>                    
                                <td>&nbsp;</td>
                              </tr>
                              <tr class="tfoot">
                                <td colspan="6" align="right">Cess On Bed
                                  <input type="hidden" name="taxname[]" value="<?php echo $vatId['2']['taxname']; ?>"/>
                                  <input type="hidden" name="taxId[]" id="taxid2" value="<?php echo $vatId['2']['taxId']; ?>" style="width:40px;" />
                                  @<input type="text" name="taxvalue[]" id="taxvalue2" value="<?php echo $vatId['2']['taxvalue']; ?>" style="width:40px;"/> %</td>
                                <td><input type="text" name="taxamount[]" id="taxamount2" value="" /></td>                    
                                <td>&nbsp;</td>
                              </tr>
                              <tr class="tfoot">
                                <td colspan="6" align="right">Hr.Sec Cess On Bed
                                  <input type="hidden" name="taxname[]" value="<?php echo $vatId['3']['taxname']; ?>"/>
                                  <input type="hidden" name="taxId[]" id="taxid3" value="<?php echo $vatId['3']['taxId']; ?>" style="width:40px;" />
                                  @<input type="text" name="taxvalue[]" id="taxvalue3" value="<?php echo $vatId['3']['taxvalue']; ?>" style="width:40px;"/> %</td>
                                <td><input type="text" name="taxamount[]" id="taxamount3" value="" /></td>                    
                                <td>&nbsp;</td>
                              </tr>
                              <tr class="tfoot">
                                <td colspan="6" align="right"><strong>Taxable Total</strong></td>
                                <td><input type="text" name="taxabletotal" id="taxabletotal" value="" /></td>                    
                                <td>&nbsp;</td>
                              </tr>
                              <tr class="tfoot">
                                <td colspan="6" align="right">Sales Tax 
                                  <input type="hidden" name="taxname[]" value="<?php echo $vatId['4']['taxname']; ?>"/>
                                  <input type="hidden" name="taxId[]" id="taxid4" value="<?php echo $vatId['4']['taxId']; ?>" style="width:40px;" />
                                  @<input type="text" name="taxvalue[]" id="taxvalue4" value="<?php echo $vatId['4']['taxvalue']; ?>" style="width:40px;"/> %</td>
                                <td><input type="text" name="taxamount[]" id="taxamount4" value="" /></td>                    
                                <td>&nbsp;</td>
                              </tr>
                              <tr class="tfoot">
                                <td colspan="6" align="right">Surcharge on Vat
                                  <input type="hidden" name="taxname[]" value="<?php echo $vatId['5']['taxname']; ?>"/>
                                  <input type="hidden" name="taxId[]" id="taxid5" value="<?php echo $vatId['5']['taxId']; ?>" style="width:40px;" />
                                  @<input type="text" name="taxvalue[]" id="taxvalue5" value="<?php echo $vatId['5']['taxvalue']; ?>" style="width:40px;"/> %</td>
                                <td><input type="text" name="taxamount[]" id="taxamount5" value="" /></td>                    
                                <td>&nbsp;</td>
                              </tr>
                              <tr class="tfoot">
                                <td colspan="6" align="right">
                                  <input type="text" name="taxname[]" id="taxid6" value="" style="width:200px;" placeholder="Misc. Charges" />
                                  <input type="hidden" name="taxId[]" id="taxid6" value="6" style="width:40px;" />
                                  <input type="hidden" name="taxvalue[]" id="taxvalue6" value="" style="width:40px;"/></td>
                                <td><input type="text" name="taxamount[]" onchange="calculate();" id="taxamount6" value="" /></td>                    
                                <td>&nbsp;</td>
                              </tr>
                              <tr class="tfoot">
                                <td colspan="6" align="right">
                                 <strong style="font-size:15px;">Grand total</strong>
                                <td><input type="text" name="grandtotal" id="grandtotal" value="" /></td>                    
                                <td>&nbsp;</td>
                              </tr>
                            </table>
                            <?php
                                            break;
			}
			case'cjoId_item': // from the annexure form to load the job order item details
			{	
				$user = new user();
                                $q = "SELECT role_id FROM _role WHERE role_group_id='$id'";
                                list($opt , $rs) = run_query($dbc , $q,'multi');
                                $str = array();
                                if($opt)
                                {
                                    while($row = mysqli_fetch_assoc($rs))
                                    {
                                        $str[] = $row['role_id'];
                                    }
                                    $str = implode(',' ,$str);
                                }
                                
				$dealer_data = $user->get_user_list("role_id IN ($str)");
				
				if(empty($dealer_data)){
					echo'FALSE<$>Sorry no such Record available';					
					break;
				}
				
				//Getting an array to create the pulldown
				$itemar = array();
				foreach($dealer_data as $key=>$value) $itemar[$value['person_id']] = $value['name'];
				
				//If user is editing a challan, then the qty of current challan will not be counted in the received qty
				echo'TRUE<$>';?>
                <input type="hidden" id="cjoId" name="cjoId" value="<?php echo $id; ?>" />
                <table width="100%" id="mytable">
                  <tr style="font-weight:bold;">
                    <td>S.No</td>
                    <td>Person</td>
                   
                    <!--<td>Date.</td>-->
                    <td>&nbsp;</td>
                  </tr>
                  <?php //$inc = 1; foreach($poitem['po_item'] as $key=>$value){?>
                  <tr class="tdata">
                    <td class="myintrow">1</td>
                    <td>
                     <?php arr_pulldown('person_id[]', $itemar, '', true, true, '', false, '', '');  ?> 
                    </td>
               
                    <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable', event);"/></td>
                  </tr>
                  <?php // $inc++;} //foreach($wodata_item as $key=>$value){ ends?>
                </table>
                <?php
				break;
			}
                  case'dealer_data_details': // from the annexure form to load the job order item details
			{	
				$myobj = new scheme();
                                $rs = $myobj->get_scheme_dealer_list($filter = "location_2_id = '$id'", $records = '', $orderby = '');
                                
				if(empty($rs)){
					echo'FALSE<$>Sorry no such Record available';   					break;
				}
				
				//Getting an array to create the pulldown
				$itemar = array();
				foreach($rs as $key=>$value) $itemar[$value['id']] = $value['name'];
				
				//If user is editing a challan, then the qty of current challan will not be counted in the received qty
				echo'TRUE<$>';?>
                <input type="hidden" id="cjoId" name="cjoId" value="<?php echo $id; ?>" />
                <table width="100%" id="mytable">
                  <tr style="font-weight:bold;">
                    <td>S.No</td>
                    <td>Dealer</td>
                    <td>&nbsp;</td>
                  </tr>
                  <?php //$inc = 1; foreach($poitem['po_item'] as $key=>$value){?>
                  <tr class="tdata">
                    <td class="myintrow">1</td>
                    <td>
                     <?php arr_pulldown('dealer_id[]', $itemar, '', true, true, '', false, '== Please Select ==', '');  ?> 
                    </td>
               
                    <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable', event);"/></td>
                  </tr>
                  <?php // $inc++;} //foreach($wodata_item as $key=>$value){ ends?>
                </table>
                <?php
				break;
			}
			case'rgp_item': // from the annexure form to load the job order item details
			{	
				$rgp = new rgp();
				$rgpitem = $rgp->get_rgp_challan_list("chrgpId = $id");
				if(empty($rgpitem)){
					echo'FALSE<$>Sorry no such Job Order available';					
					break;
				}
				foreach($rgpitem as $key=>$value) $id = $key;
				$rgpitem = $rgpitem[$id];
				//Getting an array to create the pulldown
				$itemar = array();
				foreach($rgpitem['cha_item'] as $key=>$value) $itemar[$value['itemId']] = $value['itemname'];
				
				//If user is editing a challan, then the qty of current challan will not be counted in the received qty
				echo'TRUE<$>';?>
                <input type="hidden" id="chrgpId" name="chrgpId" value="<?php echo $id; ?>" />
                <table width="100%" id="mytable">
                  <tr style="font-weight:bold;">
                    <td>S.No</td>
                    <td>ITEM</td>
                    <td>Job Process</td>
                   <td>Quantity</td>
                    <td>Unit</td>
                    <td>Value</td>
                    <td>Rec. Qty</td>
                    <!--<td>Date.</td>-->
                    <td style="width:40px;">&nbsp;</td>
                  </tr>
                  <?php //$inc = 1; foreach($poitem['po_item'] as $key=>$value){?>
                  <tr class="tdata">
                    <td class="myintrow">1</td>
                    <td>
                     <?php arr_pulldown('itemId[]', $itemar, '', true, true, 'onchange="getajaxdata(\'rgp_item_detail\', \'mytable\',event);"', false, '', '');  ?> 
                    </td>
                    <td><input type="text" class="readonly" readonly="readonly" name="job_process[]"  value=""  /></td>
                   <td><input type="text" class="readonly" readonly="readonly" name="qty[]"  value=""  /></td>
                    <td><input type="text" class="readonly" readonly="readonly" name="unit[]"  value="Nos."  /></td>
                    <td><input type="text" class="readonly" readonly="readonly" name="goodvalue[]"  value=""  /></td>
                    <td><input type="text" name="recqty[]"  value=""  /></td>
                    <!--<td>
                      <input type="text" name="do_item_date[]" class="qdatepicker"  value=""  />
                     </td>-->	
                    <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable', event);"/></td>
                  </tr>
                  <?php // $inc++;} //foreach($wodata_item as $key=>$value){ ends?>
                </table>
                <?php
				break;
			}
			case'ch_annexure_item': // from the annexure form to load the job order item details
			{	
				$po = new annexure();
				$poitem = $po->get_annexure_list("chanum = $id");
				if(empty($poitem)){
					echo'FALSE<$>Sorry no such Job Order available';					
					break;
				}
				foreach($poitem as $key=>$value) $id = $key;
				$poitem = $poitem[$id];
				//Getting an array to create the pulldown
				$itemar = array();
				foreach($poitem['cha_item'] as $key=>$value) $itemar[$value['itemId']] = $value['itemname'];
				
				//If user is editing a challan, then the qty of current challan will not be counted in the received qty
				echo'TRUE<$>';?>
                <input type="hidden" id="cjoId" name="cjoId" value="<?php echo $id; ?>" />
                <table width="100%" id="mytable">
                  <tr style="font-weight:bold;">
                    <td>S.No</td>
                    <td>ITEM</td>
                    <td>Job Process</td>
                    <td>Quantity</td>
                    <td>Unit</td>
                    <td>Value</td>
                    <td>Rec. Qty</td>
                    <!--<td>Date.</td>-->
                    <td style="width:40px;">&nbsp;</td>
                  </tr>
                  <?php //$inc = 1; foreach($poitem['po_item'] as $key=>$value){?>
                  <tr class="tdata">
                    <td class="myintrow">1</td>
                    <td>
                     <?php arr_pulldown('itemId[]', $itemar, '', true, true, 'onchange="getajaxdata(\'get_ch_item\', \'mytable\',event);"', false, '', '');  ?> 
                    </td>
                    <td><input readonly="readonly" class="readonly" type="text" name="job_process[]"  value=""  /></td>
                    <td><input readonly="readonly" class="readonly" type="text" name="qty[]"  value=""  /></td>
                    <td><input readonly="readonly" class="readonly" type="text" name="unit[]"  value=""  /></td>
                    <td><input readonly="readonly" class="readonly" type="text" name="goodvalue[]"  value=""  /></td>
                    <td><input type="text" name="recqty[]"  value=""  /></td>
                    <!--<td>
                      <input type="text" name="do_item_date[]" class="qdatepicker"  value=""  />
                     </td>-->	
                    <td><img  title="more" src="images/more.png" onclick="javascript:addmore_deep('mytable', event);"/></td>
                  </tr>
                  <?php // $inc++;} //foreach($wodata_item as $key=>$value){ ends?>
                </table>
                <?php
				break;
			}
			
			case'usermoduleoptions': // from the user create submodule
			{
				$q = "SELECT acd.add_opt, acd.view_opt, acd.edit_opt, acd.del_opt, acd.sp_opt,  am.* FROM `_modules` AS am  LEFT JOIN  _role_module_rights AS acd ON am.module_id = acd.module_id AND  acd.role_id = '$id' ORDER BY menulinkorder, submenuorder ASC";
                                //echo 'TRUE<$>'.$q;
				$r = mysqli_query($dbc, $q);
				if($r)
				{
					if(mysqli_num_rows($r)>0)
					{
						echo'TRUE<$>';
						
						?>
						<table width="100%" border="0">
						  <tr>
							<th>S.No</th>
							<th>Module</th>
							<th>Module Sub-Part</th>
							<th>Module Sub-Part Brief</th>
							<th colspan="5">User Options</th>
						  </tr>
						<?php
						$i = 0;
						$iadd = $iview = $iedit = $edel = $spopt = array();
						while($row = mysqli_fetch_assoc($r))
						{
							// setting whether the checkboxes will be set or not starts
							if($row['add_opt']) $iadd[] = 'checked="checked"'; else $iadd[] = '';
							if($row['view_opt']) $iview[] = 'checked="checked"'; else $iview[] = '';
							if($row['edit_opt']) $iedit[] = 'checked="checked"'; else $iedit[] = '';
							if($row['del_opt']) $edel[] = 'checked="checked"'; else $edel[] = '';
							if($row['sp_opt']) $spopt[] = 'checked="checked"'; else $spopt[] = '';
							// setting whether the checkboxes will be set or not ends
							echo'
							  <tr style="text-align:center;">
								<td>'.($i+1).'</td>
								<td>'.$row['menulink'].'<input type="hidden" name="linkoption[]" value="'.$row['module_id'].'"/></td>
								<td>'.$row['workdetail'].'</td>
								<td><span class="example" style="font-size:11px;">'.$row['workdesc'].'</span></td>
								<td>Add <input type="checkbox" name="add'.$i.'" '.$iadd[$i].' value="1"></td>
								<td>View <input type="checkbox" name="view'.$i.'" '.$iview[$i].' value="1"></td>
								<td>Edit <input type="checkbox" name="edit'.$i.'" '.$iedit[$i].' value="1"></td>
								<td>Delete <input type="checkbox" name="delete'.$i.'" '.$edel[$i].' value="1"></td>
								<td>Special <input type="checkbox" name="spopt'.$i.'" '.$spopt[$i].' value="1"></td>
							  </tr>';
							  $i++;
						}
						echo'</table>';
					}
					else
						echo'FALSE<$>Sorry no Modules Authorizations Specified';
				}
				break;
			}			
			case'usermoduleoptionsedit': // from the user create submodule
			{
				$q = "SELECT acr.add_opt, acr.view_opt, acr.edit_opt, acr.del_opt, acr.sp_opt,am.* FROM `_modules` AS am LEFT JOIN `person_modules_rights` AS acr ON am.module_id = acr.module_id AND acr.person_id = '$id' ORDER BY menulinkorder, submenuorder ASC";
                                
				$r = mysqli_query($dbc, $q);
				if($r)
				{
					if(mysqli_num_rows($r)>0)
					{
						echo'TRUE<$>';
						
						?>
						<table width="100%" border="0">
						  <tr>
							<th>S.No</th>
							<th>Module</th>
							<th>Module Sub-Part</th>
							<th>Module Sub-Part Brief</th>
							<th colspan="5">User Options</th>
						  </tr>
						<?php
						$i = 0;
						$iadd = $iview = $iedit = $edel = $spopt = array();
						while($row = mysqli_fetch_assoc($r))
						{
							// setting whether the checkboxes will be set or not starts
							if($row['add_opt']) $iadd[] = 'checked="checked"'; else $iadd[] = '';
							if($row['view_opt']) $iview[] = 'checked="checked"'; else $iview[] = '';
							if($row['edit_opt']) $iedit[] = 'checked="checked"'; else $iedit[] = '';
							if($row['del_opt']) $edel[] = 'checked="checked"'; else $edel[] = '';
							if($row['sp_opt']) $spopt[] = 'checked="checked"'; else $spopt[] = '';
							// setting whether the checkboxes will be set or not ends
							echo'
							  <tr style="text-align:center;">
								<td>'.($i+1).'</td>
								<td>'.$row['menulink'].'<input type="hidden" name="linkoption[]" value="'.$row['module_id'].'"/></td>
								<td>'.$row['workdetail'].'</td>
								<td><span class="example" style="font-size:11px;">'.$row['workdesc'].'</span></td>
								<td>Add <input type="checkbox" name="add'.$i.'" '.$iadd[$i].' value="1"></td>
								<td>View <input type="checkbox" name="view'.$i.'" '.$iview[$i].' value="1"></td>
								<td>Edit <input type="checkbox" name="edit'.$i.'" '.$iedit[$i].' value="1"></td>
								<td>Delete <input type="checkbox" name="delete'.$i.'" '.$edel[$i].' value="1"></td>
								<td>Special <input type="checkbox" name="spopt'.$i.'" '.$spopt[$i].' value="1"></td>
							  </tr>';
							  $i++;
						}
						echo'</table>';
					}
					else
						echo'FALSE<$>Sorry no Modules Authorizations Specified';
				}
				break;
			}
			case'emailverify': // from the customer add page
			{
				$q = "SELECT custId FROM customers WHERE email = '$id' LIMIT 1";
				$r = mysqli_query($dbc, $q);
				if($r)
				{
					if(mysqli_num_rows($r)>0)
					{
						echo'FALSE<$>';
						$row = mysqli_fetch_assoc($r);
						echo 'Email Already in use';
					}
					else
						echo'TRUE<$>Email not registered';
				}
				break;
			}
                        case'location-dealer': // from the customer add page
			{
                                 $id = explode('|',$id);
                                 $value = $id[0];
                                 $cur_table = $id[1];
                                 $nexttable = $id[2];
				 $q = "SELECT id, name FROM location_$nexttable WHERE location_".$cur_table."_id = '$value' ORDER BY name ASC";
                                 //echo 'TRUE<$>'.$q;
				$r = mysqli_query($dbc, $q);
				if($r)
				{
					if(mysqli_num_rows($r)>0)
					{
						echo'TRUE<$><table><tr>';
						while($rows = mysqli_fetch_assoc($r))
                                                {
                                                    echo '<td><label><input type="checkbox" name="location_id[]" value="'.$rows['id'].'">'.$rows['name'].'</label></td>';
                                                }
                                                echo '</tr></table>';
					}
					else
						echo'FALSE<$>No location Found';
				}
				break;
			}
                        case'get_catalog_title': // from the customer add page
			{
                           if(!empty($id))
                           {
                                echo'TRUE<$><table width="100%"><tr>';
                                for($i = 1; $i <= $id; $i++)
                                {

                                          echo '<td><span class="star">*</span>Catalog Level&nbsp;'.$i.'<input type="text" name="catalog_title[]" lang="Catalog Level" value=""></td>';
                                }
                           }
                                else
                                        echo'FALSE<$>No Catalog Found';

                            break;
			}
                          case'get_location_title': // from the customer add page
			{
                           if(!empty($id))
                           {
                                echo'TRUE<$><table width="100%"><tr>';
                                for($i = 1; $i <= $id; $i++)
                                {

                                          echo '<td><span class="star">*</span>Location Level&nbsp;'.$i.'<input type="text" name="location_title[]" lang="Location Level" value=""></td>';
                                }
                           }
                                else
                                        echo'FALSE<$>No Location Found';

                            break;
			}
                        case'tracking_time': // from the customer add page
			{
                                
                            if(!empty($id))
                            {
                                    $str = '<table width="100%">
                                             <tr><td><div class="subhead1">Tracking Interval</div></td></tr>';
                                    for($j=1; $j<=$id; $j++)
                                    {
                                        $style = '';
                                        if($j == 1) $style = 'class="read" readonly="readonly"';
                                        $str.= '<tr>
                                                <td>Interval&nbsp;'.$j.'<span class="star">*</span><span class="example">("hh:mm:ss")</span><br><input type="text" '.$style.' name="track[]" value="">
                                                </td>
                                              </tr>';
                                    }
                                $str .= '</table>';
                                 echo'TRUE<$>';
                                 echo $str;
                            }
                            else echo'FALSE<$>Sorry Some error occurred';
                            break;
			}
                         case'get_attendence_details': // from the customer add page
			{
                                
                            if(!empty($id))
                            {
                                    $sesId = $_SESSION[SESS.'data']['id'];
                                    $role_id = $_SESSION[SESS.'data']['urole'];
                                    $id = explode('<$>',$id);
                                    $user_id = $id[0];
                                    $fdate = $id[1];
                                    $edate = $id[2];
                                    $location_3_id = $id[3];
                                    $filter = array();
                                    $attendence = new settings();
                                    if(!empty($fdate))
                                    {
                                        $start = get_mysql_date($fdate,'/',$time = false, $mysqlsearch = true);
                                        $filter[] = "DATE_FORMAT(work_date,'".MYSQL_DATE_SEARCH."') >= '$start'";
                                    }
                                    if(!empty($edate))
                                    {
                                        $end = get_mysql_date($edate,'/',$time = false, $mysqlsearch = true);
                                        $filter[] = "DATE_FORMAT(work_date,'".MYSQL_DATE_SEARCH."') <= '$end'";
                                    }
                                    if(!empty($user_id) && is_numeric($user_id))
                                    {
                                        $filter[] = "user_id= $user_id";
                                    }
                                    if(!empty($location_3_id) && is_numeric($location_3_id))
                                    {
                                        $filter[] = "location_id= $location_3_id";
                                    }
                                    
                                    $attnedance_data = $attendence->get_user_wise_attendence_data($sesId , $role_id);
                                  
                                    $str = '';
                                    if(!empty($attnedance_data))
                                    {
                                         $attendance_data_str = implode(',' ,$attnedance_data);
                                         $filter[] = "user_id IN  ($attendance_data_str)";
                                    }
                                   
                                     //echo 'TRUE<$>'.pre($filter);
                                    $rs = $attendence->get_user_attendence_list($filter,  $records = '', $orderby='');
                                   
                                 
                        ########################## pagination details fetch ends here ###################################
                                if(!empty($rs))
                                {
                                    $str .= '<div class="searchlistdiv" id="searchlistdiv"> 
                                        <div><b>User daily Attendence: <span id="totCounter">'.count($rs).'</span></b>
                                        <br /></div> 
                                          <table width="100%" border="0" class="searchlist" id="searchdata">
                                            <caption><h3>Available User Attendence list</h3></caption>
                                            <tr class="search1tr">
                                              <td class="sno">S.No</td>
                                              <td>Work Date</td>
                                              <td>Work Time</td>
                                              <td>User Name</td>
                                              <td>User Designation</td>
                                              <td>Working Status</td>
                                              <td>User Location</td>
                                              <td>Remark</td>
                                            </tr>';
                                    $bg = TR_ROW_COLOR1;
                                    $inc = 1;
                                    if(isset($_GET['ajaxshow'])) ob_end_clean(); // to help refresh a single row
                                    //<a target="_blank" class="iframef" href="indexpop.php?option=user-location&lat_lng='.$rows['lat_lng'].'"><img src="./images/b_view.png"></a>
                                   foreach($rs as $key=>$rows)
                                    {
                                        $bg=($bg==TR_ROW_COLOR1?TR_ROW_COLOR2:TR_ROW_COLOR1);// to provide different row colors(member_contacted table)
                                        $uid = $rows['id'];
                                        $uidname = $rows['user_id'];

                                      //if($rows['locked'] == 1) $editlink = $deletelink = '';
                                      //if($auth['del_opt'] !=1) $deletelink = '';
                                   $str .='
                                        <tr BGCOLOR="'.$bg.'" id="tr'.$uid.'" class="ihighlight">
                                          <td class="myintrow myresultrow">'.$inc.'</td>
                                          <td>'.$rows['wdate'].'</td>
                                          <td>'.$rows['wtime'].'</td>
                                          <td>'.$rows['name'].'</td>
                                          <td>'.$rows['rolename'].'</td>
                                          <td>'.$rows['working'].'</td>
                                          <td><a onclick="window.open (\'indexpop.php?option=user-location&mcc='.$rows['mnc_mcc_lat_cellid'].'&lat_lng='.$rows['lat_lng'].'\',\'mywindow\',\'menubar=1,resizable=0,width=400,height=400\');"><img src="./images/b_view.png"></a></td> 
                                          <td>'.$rows['remarks'].'</td>
                                        </tr>
                                        ';
                                        $inc++;
                                    }// foreach loop ends here
                                   //if(isset($_GET['ajaxshow'])) exit(); // to help refresh a single row
                                   $str .= '</table>                
                              </div> ';
                                echo'TRUE<$>';
                                echo $str; 
                                } // !empty($rs) end here
                                else echo 'FALSE<$>Sorry No user Attendence found for selected filter';
                               
                            }
                            else echo'FALSE<$>Sorry Some error occurred';
                            break;
			}
                        case'getlocations': // from the customer add page
			{
                            $id = explode('|',$id);
                            $dealer_id = $id[0];
                            $pkeyid = $id[1];
                            $mtype = $_SESSION[SESS.'constant']['dealer_level'];
                            $q = "SELECT id,name FROM dealer_location_rate_list dlrl INNER JOIN location_$mtype AS lm ON lm.id = dlrl.location_id WHERE dealer_id='$dealer_id'";  
                            $r = mysqli_query($dbc,$q);
                            if($r)
                            {
                                    $str = '';
                                    while($rows = mysqli_fetch_assoc($r))
                                    {
                                        $str.= '<div><input type="checkbox"  name="location_id'.$pkeyid.'[]" value="'.$rows['id'].'">'.$rows['name'].'</div>';
                                    }
                               
                                 echo'TRUE<$>';
                                 echo $str;
                            }
                            else echo'FALSE<$>Sorry Some error occurred';
                            break;
			}
                        case'get_retailer_list': // from the customer add page
			{
                                 $id = rtrim($id , ',');
                                 $dealerarray = explode(',',$id);
                                 $locationarry = array();
                                 $retailerarray = array();
                                 $loctype = $_SESSION[SESS.'constant']['location_level'];
                                 
                                 $str = '<table width="100%">
                                                     <tr>
                                                        <td colspan="8"><div class="subhead1">Retailer Details</div></td>
                                                             </tr>';
                                 foreach($dealerarray as $key=>$value)
                                 {
                                     $qd = "SELECT id,name FROM dealer WHERE id = $value";
                                     $rd = mysqli_query($dbc , $qd);
                                     $dealername = mysqli_fetch_assoc($rd);
                                     $qq = "SELECT retailer_id FROM user_dealer_retailer WHERE dealer_id = '$value'";
                                     $rr = mysqli_query($dbc,$qq);
                                     if($rr && mysqli_num_rows($rr) > 0 )
                                     {
                                         while($dd = mysqli_fetch_assoc($rr))
                                         {
                                             $retailerarray[]= $dd['retailer_id'];
                                         }
                                     }
                                    // $q = "SELECT id,name FROM retailer r INNER JOIN dealer_location dl ON dl.location_id = r.location_id INNER JOIN location_$mtypelm ON lm.id = dl.location_id    WHERE dl.dealer_id=  '$value'";
                                     
                                     $q = "SELECT r.id,r.name FROM dealer_location dl INNER JOIN location_$loctype lm ON lm.id = dl.location_id INNER JOIN retailer r ON r.location_id = lm.id WHERE dl.dealer_id = '$value'";
                                   
                                    
                                     $r = mysqli_query($dbc , $q);
                                     if($r)
                                     {
                                         
                                          if(mysqli_num_rows($r) > 0 )
                                             {
                                                 
                                                  $str .= '
                                                          <tr><td>'.$dealername['name'].'
                                                            <table>';
                                              
                                                 while($rows = mysqli_fetch_assoc($r))
                                                 {
                                                 //print_r($rows);
                                                     $name = "retailer_id$dealername[id][]";
                                                     $checked = '';
                                                     if(in_array($rows['id'],$retailerarray)) $checked= 'checked="checked"';
                                                     $str .= '<tr><td>
                                                             <input '.$checked.' type="checkbox" name="'.$name.'" value="'.$rows['id'].'">'.$rows['name'].'</td>
                                                            </tr>';
                                                 }// while loop ends
                                                      $str .= '</table>
                                                             </td></tr>';
                                                     
                                             } //if(mysqli_num_rows($r1) > 0 ) end here
                                             //else echo 'FALSE<$>Sorry No retailer Found';
                                     } // if($r) end here
                                 } //foreach($dealerarray as $key=>$value) end here
                                 $str .= '</table>';
				 echo'TRUE<$>';
                                 echo $str;
				break;
			}
			//nesting_item
			case'nesting_item': // from the customer add page
			{
                                $id = rtrim($id , ',');
                                $dealerarray = explode(',',$id);
                                $locationarry = array();
                                
				$q = "SELECT itemId, itemname, qty FROM nesting_item INNER JOIN item USING(itemId) WHERE nestingId = $id";
				$r = mysqli_query($dbc, $q);
				$str = '';
				if(mysqli_num_rows($r)>0)
				{
					$str = '<table width="100%" cellpadding="2" cellspacing="2">';
					$str .= '<tr style="font-weight:bold;">
								<td>Item</td>
								<td>Quantity</td>
								<td>Actual Qty</td>
							 </tr>';
					while($d = mysqli_fetch_assoc($r))
					{
						$str .=  '<tr>
								<td><input type="text" name="item[]" value="'.$d['itemname'].'" readonly ><input type="hidden" name="hid[]" value="'.$d['itemId'].'"></td>
								<td><input type="text" name="qty[]" value="'.$d['qty'].'" readonly></td>
								<td><input type="text" name="ac_qty[]"  lang="Actual Produced Qty"></td>
								 </tr>';
					}
					$str .= '<tr>
								<td colspan="3"></td>
							</tr>
							<tr style="font-weight:bold;">
								<td>Plate fully Used:<span class="star">*</span></td>
								<td><select  lang="Used Status" name="used_status"><option value="">Please Select..</option><option value="0">No</option><option value="1">Yes</option></select></td>
								<td>&nbsp;</td>
							<tr>
								<td colspan="3" align="center"><input type="submit" name="submit" value="Save" onclick="return js_check_fields(\'pr\')">
								<input type="button" name="exit" onclick="window.document.location = \'index.php?option=work-sf-entry\'" value="Exit"></td>
							</tr>';
					$str .= '</table>';
					echo'TRUE<$>';
					echo $str;
				}
				break;
			}
                  case'get_nesting': // from the customer add page
			{
				$q = "SELECT nestingId, nestingcode FROM nesting WHERE itemId = '$id' ORDER BY nestingcode ASC";
				$r = mysqli_query($dbc, $q);
				$str = '';
				$nId = '';
				if(mysqli_num_rows($r)>0)
				{
					$str = 'Nesting:<br><select name="nesting" onchange="getdata_div(this.value, \'progress_div\', \'nesting_item\',\'nitem\');" style=" background-color:yellow; color:red;font-weight:bold;">';
					$str .= '<option value="">Please Select</option>';
					while($d = mysqli_fetch_assoc($r))
					{
						$str .= '<option value="'.$d['nestingId'].'">'.$d['nestingcode'].'</option>';
						
						//here we get nesting value
						$nId .= '<tr><td style="font-weight:bold;" width="2%"><span style="color:green;"><strong>'.$d['nestingcode'].'</strong></span></td>';
						$q3 = "SELECT itemname, qty, utname FROM nesting_item INNER JOIN item USING(itemId) INNER JOIN item_unit USING(utId) WHERE nestingId = '$d[nestingId]'";
						$r3 = mysqli_query($dbc, $q3);
						$nId .= '<td width="10%">';
						$nId .= '<table>';
						while($d3 = mysqli_fetch_assoc($r3))
						{
							$nId .= '<tr>
										<td>'.$d3['itemname'].'</td>
										<td style="color:red;"><strong>'.$d3['qty'].'</strong> '.$d3['utname'].'</td>
									 </tr>';
						}
						$nId .= '</table>';
						$nId .= '</td></tr>';
						//end here 
					}
					$str .= '</select>';
					
				}
				else
				{
					$str = 'Nesting:<br/><select>';
					$str .= '<option>==No Nesting Found == </option></select>';
				}
				echo 'TRUE<$>';
				echo $str.'<$$>'.$nId;
				break;
			}
                        case'soId_item': // from the annexure form to load the job order item details
			{	
				$so = new sale();
				$soitem = $so->get_sale_order_list("retailer_id = $id", '', '');
				//echo'TRUE<$>'.pre($poitem);
				if(empty($soitem)){
					echo'FALSE<$>Sorry no such Job Order available';					
					break;
				}
				foreach($soitem as $key=>$value) $id = $key;
				$soitem = $soitem[$id];
				//Getting an array to create the pulldown
				$itemar = array();
				foreach($soitem['order_item'] as $key=>$value) $itemar[$value['id']] = $value['name'];
				
				//If user is editing a challan, then the qty of current challan will not be counted in the received qty
				echo'TRUE<$>';?>
                
                <table width="100%" id="mytable">
                  <tr style="font-weight:bold;">
                    <td>S.No</td>
                    <td>ITEM</td>
                    <td>Batch</td>
                    <td>Ord. Qty</td>
                    <td>Rate</td>
                    <td>Value</td>
                    <!--<td>Date.</td>-->
                    <td style="width:40px;">&nbsp;</td>
                  </tr>
                  <?php //$inc = 1; foreach($poitem['po_item'] as $key=>$value){?>
                  <tr class="tdata">
                    <td class="myintrow">1</td>
                    <td>
                     <?php 
                     arr_pulldown('product_id[]', $itemar, $msg='', $usearrkey=true, $ini_option=true, $jsfunction='id="product_id" onchange="custom_function(this.value,this.id);"');
                        ?> 
                    </td>
                    <td><select name="batch[]" id="batch">
                            <option>== Please Select ==</option>
                        </select>
                    </td>
                    <td><input id="qty" type="text" name="qty[]"  value=""  /></td>
                    <td><input id="rate" type="text" name="rate[]"  value="Nos."  /></td>
                    <td><input id="goodvalue" type="text" name="goodvalue[]"  value=""  /></td>
                    <td><img  title="more" src="images/more.png" class="addbutton"/><img  title="less" src="images/less.png" class="removebutton"/></td>
                  </tr>
                  <?php // $inc++;} //foreach($wodata_item as $key=>$value){ ends?>
                </table>
                <?php
				break;
			}
                        case 'person-by-senior': // from the customer add page
			{
                            
				$q = "SELECT id,first_name,middle_name,last_name,mobile,email,rolename from person inner join _role using(role_id) WHERE role_id != 1 AND role_group_id = '11' ORDER BY role_id ASC";
				$r = mysqli_query($dbc, $q);
				$str = '';
				$nId = '';
				if(mysqli_num_rows($r)>0)
				{
                                        $i=1;
                                        $nId .= '<table border=\'1\' width=\'100%\'>';
                                        $nId .= '<tr><th><input type=\'checkbox\' name=\'\' value=\"\"></th>';
                                        $nId .= '<th align=\'center\'>S.No.</th>';
                                        $nId .= '<th align=\'center\'>Person Name</th>';
                                        $nId .= '<th align=\'center\'>Designation.</th>';
                                        $nId .= '<th align=\'center\'>Mobile No.</th>';
                                        $nId .= '<th align=\'center\'>E-Mail</th></tr>';
					while($d = mysqli_fetch_assoc($r))
					{
						//here we get nesting value
                                                $nId .= '<tr>';
                                                $nId .= '<td><input type=\'checkbox\' name=\'personid[]\' value='.$d['id'].'></td>';
						$nId .= '<td>'.$i.'</td>';
                                                $nId .= '<td>'.$d['first_name'].' '.$d['middle_name'].' '.$d['last_name'].'</td>';
                                                $nId .= '<td>'.$d['rolename'].'</td>';
                                                $nId .= '<td>'.$d['mobile'].'</td>';
                                                $nId .= '<td>'.$d['email'].'</td>';
                                                $nId .= '</tr>';
        					//end here 
                                                $i++;
					}
					$nId .= '</table>';					
				}
				else
				{
					$str = 'Nesting:';
				}
				echo 'TRUE<$>'.$nId;
				//echo $str.'<$$>'.$nId;
				break;
			}
                        
		}
	}
	else
	{
		echo'FALSE<$>Please select a value';
	}
}
else
	echo'FALSE<$$>Sorry please login to complete your request';
$output = ob_get_clean();
echo $output = trim($output);		
?>