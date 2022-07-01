<?php
$id = $_GET['q'];
//echo $id;
  $q = " CALL `product`($id])";
          
                    $r = mysqli_query($dbc, $q);
               
                            $row = mysqli_fetch_assoc($r);
          
                            if($row['rate']>0)
                            {
                            echo $row['rate'] ;
                        } 
                        else
                             echo'0'; 

?>