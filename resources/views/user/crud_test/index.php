<?php 
    include_once('conn.php');
    include_once('crud_query.php');
?>
       
<div class="row">
    <div class="col-xs-12" >
        <div class="table-header center" style="text-align: center;">
            User Details
          
        </div>
        <div class="col-lg-2">
            <a href="create.php" class=" input-sm form-control btn  btn-info" style="margin-top: 27px; text-align: right;">
                <i class="fa fa-plus mg-r-10"></i> Add User
            </a>
        </div>
        <table id="dynamic-table" border="1" class="" style="width: 100%;">
            <thead>
            <tr>
                <th class="center">
                    Sr.no
                </th>
                <th>User Name</th>
                <th>Mobile No</th>
                <th>Address</th>
                <th>Age</th>
                <th>Created at</th>
                <th>Updated at</th>
                <th colspan="2">Action</th>
                
                
             
            </tr>
            </thead>
            <tbody>
                <?php
                    $key = 1;
                    while ($row = mysqli_fetch_assoc($select_data))
                    {
                ?>
                        <tr>
                            <td><?php echo $key; ?></td>
                            <td><?php echo $row['user_name']; ?></td>
                            <td><?php echo $row['mobile_no']; ?></td>
                            <td><?php echo $row['address']; ?></td>
                            <td><?php echo $row['age']; ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td><?php echo $row['updated_at']; ?></td>
                            <td>
                                    <a title="Edit" class="btn btn-xs btn-primary" href="edit.php?id=<?php echo $row['id']; ?>" >
                                        <i class="ace-icon fa fa-pencil bigger-120"></i>Edit
                                    </a>
                            </td>
                            <td>
                                <a title="Edit" class="btn btn-xs btn-primary" href="delete.php?id=<?php echo $row['id']; ?>" >
                                        <i class="ace-icon fa fa-pencil bigger-120"></i>Delete
                                    </a>
                            </td>
                        </tr>
                <?php
                        $key++;
                    }
                ?>

            </tbody>
        </table>
    </div><!-- /.span -->
</div><!-- /.row -->

    <script src="jquery.js"></script>


   