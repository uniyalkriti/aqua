
<?php 
error_reporting(0);
require_once('../function/report_details.php');
require_once('../../admin/include/conectdb.php');
$forma = 'Sale Details';
$user_id = $_GET['user_id'];
$date = $_GET['date'];
$catalog_id = $_GET['catalog_id'];

$data = get_catalog1_details($user_id,$date,$catalog_id);

//print_r($data); exit;

?>

    <div id="workarea">     
<!--       <table width="100%" border="0" cellspacing="2" cellpadding="2">-->
    <?php      
        if(!empty($data)){ //if no content available present no need to show the bottom part
    ?>
                     
              <div class="table-responsive">                
                  <table width="100%" border="0" class="searchlist" id="searchdata">
                    <caption><h3>Available <?php echo $forma; ?> list</h3></caption>
                     <thead>
                    <tr class="search1tr">
                     <td class="sno">S.No</td>
                      <td>User Name</td>
                      <td>Dealer Name</td>
                      <td>Retailer Name</td>
                      <td>Product Name</td>
                      <td>Quantity</td>
                      <td>Total Value</td>                                           
                    </tr>
                     </thead>
                      <tbody>
                  <?php               
                  
                
                   $count1 = count($data);
                   $inc1 =1; 
                    $inc = 1;     
                  while($inc1 <= $count1)
                  {  
                             
                     ?>  
                      <tr>
			<td><?php echo $inc; ?></td>
                        <td><?php echo $data[$inc1]->full_name; ?></td>
                        <td><?php echo $data[$inc1]->dealer_name; ?></td>
                        <td><?php echo $data[$inc1]->retailer_name; ?></td>
                        <td><?php echo $data[$inc1]->cp_name; ?></td>  
                        <td><?php echo $data[$inc1]->quantity; ?></td> 
                        <td><?php echo $data[$inc1]->total_value; ?></td> 
                
                      </tr>
                  <?php   
                    $inc1++; 
                      $inc++;                 
                  }
                  ?> 
                       </tbody>
                </table>
            </div>
          
          <?php }else{ ?>
           <div style="text-align:center;margin-top: 30;font-size: 18;"><?php echo 'Data Not Found'; ?></div>
          
          <?php } ?>
<!--        </table>-->
      </div>
   