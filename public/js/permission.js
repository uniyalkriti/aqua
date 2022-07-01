
$( document ).ready(function() {
	var pathname = window.location.pathname;
    permission_check(pathname,2);    
});

function permission_check(val,level) {
	// alert(1);
    if (val != '') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: domain + '/check_read_write_permission',
            dataType: 'json',
            data: "title=" + val+"&type="+level,
            success: function (data) {
                if (data.code == 401) {
                    //  $('#loading-image').hide();
                }
                else if (data.code == 200) {
                	if(data.edit_permission != 1){
                		$('.ActionData').hide();
                		// console.log(1);
                	}
                    if(data.create_permission != 1){
						$('.ActionCreate').hide();
                	}
                    if(data.delete_permission != 1){
						$('.ActionDelete').hide();
					}
                }

            },
            complete: function () {               
            },
            error: function () {
            }
        });
    }
    else{
       
    }
}
