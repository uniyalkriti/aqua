
<form method="post" action="<?php if(isset($eformaction)) echo  $eformaction; ?>" class="iform" name="genform" onsubmit="return checkForm_alert('genform');">
      <fieldset>
        <legend class="legend" style=""><?php echo $forma; ?></legend>
        <input type="hidden" name="hf" value="<?php echo $securetoken;?>" />
        <input type="hidden" name="dealer_id" value="<?php echo $_SESSION[SESS.'data']['dealer_id']; ?>">
        <table width="100%" border="0" cellspacing="2" cellpadding="2" class="tableform">
         <tr>
             <td>Dispatch No<br>
             <input type="text"  name="dispatch_no"  value="<?php if(isset($_POST['dispatch_no'])) echo $_POST['dispatch_no']; else echo $dis_num; ?>"/>
             </td>
            <td><span class="star">*</span>Dispatch Date<br />
             <input type="text" id="invdate" name="dispatch_date"  value="<?php if(isset($_POST['dispatch_date'])) echo $_POST['dispatch_date']; else echo date('d/m/Y');?>" class="qdatepicker" lang="Dispatch Date" />
            </td>
            <td>Van Number<br />
                <input type="text" class="van" name="van_no" value="<?php if(isset($_POST['van_no'])) echo $_POST['van_no']; ?>">
                <input type="hidden" id="vanId" name="vanId" value="<?php if(isset($_POST['vanId'])) echo $_POST['vanId']; ?>">
            </td>
         </tr>
         <tr>
           <td colspan="6"><div class="subhead1">Search Details</div></td>
         </tr>
         <tr>
            <tr>
                <td><strong>Name</strong><br />
                   <!-- <div style="float:left; "><input type="radio" style="float:left" name="search" checked="checked"  onclick="return search_user(1)" /> -->
                 
                        
                        <?php db_pulldown($dbc, 'retailer_id', "SELECT id AS retailer_id, name AS retailer_name FROM `retailer` WHERE `dealer_id` = '$dea_id'", true, true, ''); ?> 
                    </div>
                </td>
                
                 <td><strong>Beat</strong><br />
                <!-- <div style="float:left; "> <input type="radio" style="float:left" name="search"  onclick="return search_user(2)" />  -->
                 
                      <?php db_pulldown($dbc, 'location_id', "SELECT location_id as beat_id,(select name from location_5 where id=dealer_location_rate_list.location_id) as beat_name FROM `dealer_location_rate_list` WHERE `dealer_id` = '$dea_id'", true, true, ''); ?> 
            </div>
                </td>
                
            <td><strong>From Date</strong><br />
                <div style="float:left; "> 
                    <input type="text" id="from_date" name="from_date"  value="<?php if(isset($_POST['from_date'])) echo $_POST['from_date']; else echo date('d/m/Y');?>" class="qdatepicker" lang="Form Date" />
                </div>
            </td>
            
            <td><strong>To Date</strong><br />
                <div style="float:left; "> 
                    <input type="text" id="to_date" name="to_date"  value="<?php if(isset($_POST['to_date'])) echo $_POST['to_date']; else echo date('d/m/Y');?>" class="qdatepicker" lang="To Date" />
                </div>
            </td>
            
            
            
            
            
            <!--<td colspan="2"><strong>Bill No</strong><br />
                <div style="float:left; "> <input type="radio" style="float:left" name="search"  onclick="return search_user(3)" /><input  style="float:left; width:130px;" type="text" id="search3" placeholder="Dispatch Bill From" class="billno" name="bill_from" value="<?php if(isset($_POST['bill_from'])) echo $_POST['bill_from']; ?>" /><input  style="float:left; width:130px;" class="billno" type="text" id="search4" placeholder="Dispatch Bill To" name="bill_to" value="<?php if(isset($_POST['bill_to'])) echo $_POST['bill_to']; ?>" /></div>
             </td>-->
             <td colspan="2">
              <input id="mysave" type="submit" name="submit" value="<?php if(isset($heid)) echo'Update'; else echo'Search';?>" />
              <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />
             </td>
            </tr>
         <?php 
        // pre($rs1);
         if(!empty($rs1)) { ?>
            <tr>
                <td colspan="6">
                    <table width="100%" border="0" class="searchlist" id="searchdata">
                    <tr class="search1tr">
                        <td class="sno">S.No<input onclick="selectCheckBoxes('checkall', 'chk[]');" type="checkbox" id="checkall"></td>
                      <td>Invoice No</td>
                      
                      <td>Invoice Date</td>
                      <td>Retailer Name</td>
                    </tr>
                     <?php
                        $inc = 1;
                      foreach($rs1 as $key=>$value)
                      {
                          echo'
                      <tr>
                        <td>'.$inc.'<input type="checkbox" name="chk[]" value="'.$value['id'].'"></td>
                        <td><strong>'.$value['ch_no'].'</strong></td>
                        
                        <td>'.$value['ch_date'].'</td>
                        <td>'.$value['retailer_name'].'</td>
                       </tr>';
                        $inc++;
                      }
                     ?>
                    <tr>
                        <th colspan="4" align="center">
                          <input id="mysave" type="submit" name="submit" value="<?php if(isset($heid)) echo'Update'; else echo'Save';?>" />
                        <input onclick="window.document.location = 'index.php?option=<?php echo $formaction; ?>';" type="button" value="Close" />
                        </th>
                    </tr>
                    </table>
                </td>
            </tr>
         <?php } ?>
        </table>
      </fieldset>
    </form>