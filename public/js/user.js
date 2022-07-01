$(".chosen-select").chosen();
$('button').click(function () {
    $(".chosen-select").trigger("chosen:updated");
});

function stripslashes(str) {
    str = str.replace(/\\'/g, '\'');
    str = str.replace(/\\"/g, '"');
    str = str.replace(/\\0/g, '\0');
    str = str.replace(/\\\\/g, '\\');
    return str;
}

function renderimage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#render').attr('src', e.target.result);
//                        $('#render_value').attr('value', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$(document).on('change', '#senior', function () {
    _current_val = $(this).val();
    user_data(_current_val);
});

function user_data(val) {
    _append_box=$('#senior_person');
    if (val != '') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: domain + '/getUser',
            dataType: 'json',
            data: "role="+val,
            success: function (data) {
                if (data.code == 401) {
                    //  $('#loading-image').hide();
                }
                else if (data.code == 200) {

                    //Location 3
                    template = '<option value="" >Select</option>';
                    $.each(data.result, function (key, value) {
                        if (value.name != '') {
                            template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                        }
                    });
                    _append_box.empty();
                    _append_box.append(template).trigger("chosen:updated");

                }

            },
            complete: function () {
                // $('#loading-image').hide();
            },
            error: function () {
            }
        });
    }
    else{
        _append_box.empty();
    }
}


$(document).on('change', '#designation1', function () {
    _current_val = $(this).val();
    senior_data(_current_val);
});

function senior_data(val) {
_append_box=$('#senior');
if (val != '') {
$.ajaxSetup({
headers: {
'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});
$.ajax({
type: "POST",
url: domain + '/getSenior',
dataType: 'json',
data: "senior_id="+val,
success: function (data) {
if (data.code == 401) {
// $('#loading-image').hide();
}
else if (data.code == 200) {

//Location 3
template = '<option value="" >Select</option>';
$.each(data.result, function (key, value) {
if (value.name != '') {
template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
}
});
_append_box.empty();
_append_box.append(template);

}

},
complete: function () {
// $('#loading-image').hide();
},
error: function () {
}
});
}
else{
_append_box.empty();
}
}


$(function () {

    $('#user-form').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        ignore: "",
        rules: {
            first_name: {
                required: true,
                maxlength: 50,
                minlength: 2
            },
            last_name: {
                maxlength: 50
            },
            last_name:{
                maxlength: 50
            },
            joining_date:{
                date:true
            },
            head_quarter: {
                maxlength: 100
            },
            email:{
                required: true,
                maxlength: 40
            },
            password:{
                required: true,
                maxlength: 40
            },
            email_o:{
                required: true,
                maxlength: 100
            },
            mobile:{
                maxlength: 10,
                minlength: 10,
                number:true,
                min:0
            },
            imei_number:{
                required:true,
                number:true,
                min:0,
            },

        },

        messages: {

        },
        highlight: function (e) {
            $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
        },

        success: function (e) {
            $(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
            $(e).remove();
        },

        errorPlacement: function (error, element) {
            if (element.is('input[type=checkbox]') || element.is('input[type=radio]')) {
                var controls = element.closest('div[class*="col-"]');
                if (controls.find(':checkbox,:radio').length > 1) controls.append(error);
                else error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
            }
            else if (element.is('.select2')) {
                error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
            }
            else if (element.is('.chosen-select')) {
                error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
            }
            else error.insertAfter(element.parent());
        },

        submitHandler: function (form) {
            // $('#create-user-form').submit();
            form.submit();
        },
        invalidHandler: function (form) {
        }
    });
    $('#location-form2').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        ignore: "",
        rules: {
            location_1: {
                required: true
            },
            location_2: {
                required: true,
                maxlength:50
            }
        },

        messages: {

        },
        highlight: function (e) {
            $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
        },

        success: function (e) {
            $(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
            $(e).remove();
        },

        errorPlacement: function (error, element) {
            if (element.is('input[type=checkbox]') || element.is('input[type=radio]')) {
                var controls = element.closest('div[class*="col-"]');
                if (controls.find(':checkbox,:radio').length > 1) controls.append(error);
                else error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
            }
            else if (element.is('.select2')) {
                error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
            }
            else if (element.is('.chosen-select')) {
                error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
            }
            else error.insertAfter(element.parent());
        },

        submitHandler: function (form) {
            // $('#create-user-form').submit();
            form.submit();
        },
        invalidHandler: function (form) {
        }
    });
    $('#location-form3').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        ignore: "",
        rules: {
            location_1: {
                required: true
            },
            location_2: {
                required: true
            },
            location_3: {
                required: true,
                maxlength:50
            }
        },

        messages: {

        },
        highlight: function (e) {
            $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
        },

        success: function (e) {
            $(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
            $(e).remove();
        },

        errorPlacement: function (error, element) {
            if (element.is('input[type=checkbox]') || element.is('input[type=radio]')) {
                var controls = element.closest('div[class*="col-"]');
                if (controls.find(':checkbox,:radio').length > 1) controls.append(error);
                else error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
            }
            else if (element.is('.select2')) {
                error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
            }
            else if (element.is('.chosen-select')) {
                error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
            }
            else error.insertAfter(element.parent());
        },

        submitHandler: function (form) {
            // $('#create-user-form').submit();
            form.submit();
        },
        invalidHandler: function (form) {
        }
    });
    $('#location-form4').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        ignore: "",
        rules: {
            location_1: {
                required: true
            },
            location_2: {
                required: true
            },
            location_3: {
                required: true
            },
            location_4: {
                required: true,
                maxlength:50
            }
        },

        messages: {

        },
        highlight: function (e) {
            $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
        },

        success: function (e) {
            $(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
            $(e).remove();
        },

        errorPlacement: function (error, element) {
            if (element.is('input[type=checkbox]') || element.is('input[type=radio]')) {
                var controls = element.closest('div[class*="col-"]');
                if (controls.find(':checkbox,:radio').length > 1) controls.append(error);
                else error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
            }
            else if (element.is('.select2')) {
                error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
            }
            else if (element.is('.chosen-select')) {
                error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
            }
            else error.insertAfter(element.parent());
        },

        submitHandler: function (form) {
            // $('#create-user-form').submit();
            form.submit();
        },
        invalidHandler: function (form) {
        }
    });
    $('#location-form5').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        ignore: "",
        rules: {
            location_1: {
                required: true
            },
            location_2: {
                required: true
            },
            location_3: {
                required: true
            },
            location_4: {
                required: true
            },
            location_5: {
                required: true,
                maxlength:50
            }
        },

        messages: {

        },
        highlight: function (e) {
            $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
        },

        success: function (e) {
            $(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
            $(e).remove();
        },

        errorPlacement: function (error, element) {
            if (element.is('input[type=checkbox]') || element.is('input[type=radio]')) {
                var controls = element.closest('div[class*="col-"]');
                if (controls.find(':checkbox,:radio').length > 1) controls.append(error);
                else error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
            }
            else if (element.is('.select2')) {
                error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
            }
            else if (element.is('.chosen-select')) {
                error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
            }
            else error.insertAfter(element.parent());
        },

        submitHandler: function (form) {
            // $('#create-user-form').submit();
            form.submit();
        },
        invalidHandler: function (form) {
        }
    });


    //datepicker plugin
    //link
    $('.date-picker').datetimepicker({
        viewMode: 'days',
        format: 'YYYY-MM-DD',
        useCurrent: true,
        maxDate: moment()
    });


});
