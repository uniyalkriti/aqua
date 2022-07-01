<?php
function inputform($mtype)
{
    
    if($mtype == 2)
    {
        $mtype = $mtype - 1;
        for($i = 1; $i<=$mtype; $i++)
        {
            echo '<td>';
             db_pulldown($dbc, "catalog_$i", "SELECT id, name FROM catalog_$i", true, true, 'onchange="get_cjoId_item(this.value);"');
            echo'</td>';
        }
        echo '<td><input type="text" name="name" value=""></td>';
    }
}
?>

