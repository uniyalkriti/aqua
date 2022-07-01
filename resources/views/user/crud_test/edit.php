<?php
    $_POST['module_select'] = 'edit';
    $_POST['id'] = $_GET['id'];
    include_once('crud_query.php');
    
    $first_name =  $data_set->first_name;
    $middle_name =  $data_set->middle_name;
    $last_name =  $data_set->last_name;
    $address =  $data_set->address;
    $age =  $data_set->age;
    $mobile_no =  $data_set->mobile_no;

?>

    <title>Add User</title>
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="row">
                    <div class="col-xs-12">
                       <input type="hidden" name="id" id="id" value="<?php echo $_GET['id']; ?>">
                        <div class="col-xs-2">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="module"> First Name <b style="color: red;">*</b></label>
                                <input placeholder="First Name" required="required" type="text" id="first_name" name="first_name"
                                       class="form-control input-sm" value="<?php echo $first_name;?>" />
                            </div>
                        </div>
                        <div class="col-xs-2">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="module"> Middle Name</label>
                                <input placeholder="Middle Name" type="text" id="middle_name" name="middle_name"
                                       class="form-control input-sm" value="<?php echo $middle_name;?>"/>
                            </div>
                        </div>
                        <div class="col-xs-2">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="module"> Last Name <b style="color: red;">*</b></label>
                                <input placeholder="Last Name" required="required" type="text" id="last_name" name="last_name"
                                       class="form-control input-sm" value="<?php echo $last_name;?>"/>
                            </div>
                        </div>
                        
                        <div class="col-xs-2">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="mobile_no"> Mobile No. <b style="color: red;">*</b></label>
                                <input type="text" id="mobile_no" name="mobile_no"
                                       placeholder="Enter Your Mobile Number" class="vnumerror" maxlength="10" minlength="10" value="<?php echo $mobile_no;?>"/>
                            </div>
                        </div>
                        <div class="col-xs-2">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="Address"> Address <b style="color: red;">*</b></label>
                                <input type="text" id="address" name="address" placeholder="Enter Address" value="<?php echo $address;?>"/>
                            </div>
                        </div>
                        <div class="col-xs-2">
                            <div class="">
                                <label class="control-label no-padding-right"
                                       for="Address"> Age <b style="color: red;">*</b></label>
                                <input type="text" id="age" name="age" placeholder="Enter Age" class="vnumerror" maxlength="3" minlength="1" value="<?php echo $age;?>"/>
                            </div>
                        </div>
                                
                            
                        <div class="hr hr-18 dotted hr-double"></div>
                        <div class="clearfix form-actions">
                            <div class="col-md-offset-5 col-md-7">
                                <button class="btn btn-info btn-sm" type="button" onclick="submit_function();">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    Update
                                </button>
                                
                            </div>
                        </div>

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    <script src="jquery.js"></script>
    <script>
        $('.vnumerror').keyup(function()
        {
            var yourInput = $(this).val();
            re = /[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
            var isSplChar = re.test(yourInput);
            if(isSplChar)
            {
                var no_spl_char = yourInput.replace(/[a-zA-Z`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
                $(this).val(no_spl_char);
            }
        });
    </script>
     <script>

        function submit_function() {

            let user_name1 = $('#first_name').val();
            let user_name2 = $('#middle_name').val();
            let user_name3 = $('#last_name').val();
            let user_name = user_name1+' '+user_name2+' '+user_name3;
            let mobile_no = $('#mobile_no').val();
            let address = $('#address').val();
            let age = $('#age').val();
            let id = $('#id').val();

            
                $.ajax({
                    type: "POST",
                    url:  'crud_query.php',
                    dataType: 'json',
                    data: "id=" + id+"&user_name=" + user_name+"&first_name=" + user_name1+"&middle_name=" + user_name2+"&last_name=" + user_name3+"&mobile_no="+mobile_no+"&address="+address+"&age="+age+"&module=update",
                    success: function (data) {
                        
                            // setTimeout("window.parent.location = 'index.php'", 1);

                    },
                    complete: function () {
                        // $('#loading-image').hide();
                            setTimeout("window.parent.location = 'index.php'", 1);

                    },
                    error: function () {
                    }
                });
            }
            

    
    </script>
