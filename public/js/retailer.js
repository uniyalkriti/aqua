$(".chosen-select").chosen();
$('button').click(function () {
    $(".chosen-select").trigger("chosen:updated");
});

$(function () {
    $('#retailer-form').validate({
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
                required: true
            },
            location_6: {
                required: true
            },
            location_7: {
                required: true
            },
            distributor: {
                required: true
            },
            retailer_name: {
                required: true,
                maxlength:30
            },
            landline: {
                maxlength: 20
            },
            mobile: {
                maxlength: 10,
                minlength: 10,
                number: true
            },
            email: {
                maxlength: 50
            },
            outlet_type: {
                required: true
            },
            pin_no: {
                maxlength: 20
            },
            tin_no: {
                required: true,
                maxlength: 20
            },
            avg_per_month: {
                maxlength: 11
            }

        },

        messages: {},
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
});

function stripslashes(str) {
    str = str.replace(/\\'/g, '\'');
    str = str.replace(/\\"/g, '"');
    str = str.replace(/\\0/g, '\0');
    str = str.replace(/\\\\/g, '\\');
    return str;
}

$('.user-modal').click(function () {
    // $('#result').html('');
    $('#uuid').val($(this).attr('userid'));
});
$('.user-modal2').click(function () {
    // $('#result').html('');
    var dealer=$(this).attr('userid')
    $('#uuid2').val(dealer);

    //Ajax for edit modal pop-up
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: "POST",
        url: domain + '/getDealerPerson',
        dataType: 'json',
        data: "id=" + dealer,
        success: function (data) {
            if (data.code == 401) {

            }
            else if (data.code == 200) {
                console.log(data.result.person_name);
                $('#person_name2').val(data.result.person_name);
                $('#username2').val(data.result.uname);
                $('#phone2').val(data.result.phone);
                $('#email2').val(data.result.email);
                $('#role_name2').val(data.result.role_id);
                $('#state2').val(data.result.state_id);
                $('#pass1').val(data.result.pass1);
            }

        },
        complete: function () {
            // $('#loading-image').hide();
        },
        error: function () {
        }
    });
});

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

$(document).on('change', '#location_1', function () {
    _current_val = $(this).val();
    location_data(_current_val,2);
});

$(document).on('change', '#location_2', function () {
    _current_val = $(this).val();
    location_data(_current_val,3);
});

$(document).on('change', '#location_3', function () {
    _current_val = $(this).val();
    csa(_current_val);
    // location_data(_current_val,4);
});

$(document).on('change', '#location_4', function () {
    _current_val = $(this).val();
    location_data(_current_val,5);
});

$(document).on('change', '#location_5', function () {
    _current_val = $(this).val();
    location_data(_current_val,6);
});

$(document).on('change', '#location_6', function () {
    _current_val = $(this).val();
    location_data(_current_val,7);
});

$(document).on('change', '#location_7', function () {
    _current_val = $(this).val();
    dealer_data(_current_val);
});

function dealer_data(val) {
    _append_box=$('#distributor');
    if (val != '') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: domain + '/getBeatDealer',
            dataType: 'json',
            data: "id=" + val,
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

function location_data(val,level) {
    _append_box=$('#location_'+level);
    // console.log(_append_box);
    if (val != '') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: domain + '/getLocation',
            dataType: 'json',
            data: "id=" + val+"&type="+level,
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

function csa(val) {
    _append_box=$('#csa');
    if (val != '') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: domain + '/getCSA',
            dataType: 'json',
            data: "id=" + val,
            success: function (data) {
                if (data.code == 401) {
                    //  $('#loading-image').hide();
                }
                else if (data.code == 200) {
                    var level=4;
                    $.ajax({
                        type: "POST",
                        url: domain + '/getLocation',
                        dataType: 'json',
                        data: "id=" + val+"&type="+level,
                        success: function (data2) {
                            if (data2.code == 401) {
                                //  $('#loading-image').hide();
                            }
                            else if (data2.code == 200) {

                                //Location 3
                                template2 = '<option value="" >Select</option>';
                                $.each(data2.result, function (key2, value2) {
                                    if (value2.name != '') {
                                        template2 += '<option value="' + key2 + '" >' + stripslashes(value2) + '</option>';
                                    }
                                });
                                $('#location_4').empty();
                                $('#location_4').append(template2);

                            }

                        },
                        complete: function () {
                            // $('#loading-image').hide();
                        },
                        error: function () {
                        }
                    });


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

function fnExcelReport() {
    var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
    var textRange;
    var j = 0;
    tab = document.getElementById('dynamic-table'); // id of table

    for (j = 0; j < tab.rows.length; j++) {
        tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
        //tab_text=tab_text+"</tr>";
    }

    tab_text = tab_text + "</table>";
    tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
    tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
    tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
    {
        txtArea1.document.open("txt/html", "replace");
        txtArea1.document.write(tab_text);
        txtArea1.document.close();
        txtArea1.focus();
        sa = txtArea1.document.execCommand("SaveAs", true, "file.xlxs");
    }
    else                 //other browser not tested on IE 11
        sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

    return (sa);
}