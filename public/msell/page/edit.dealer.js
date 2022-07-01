$(".chosen-select").chosen();
$('button').click(function () {
    $(".chosen-select").trigger("chosen:updated");
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

// Chosen validation
$('.chosen-select').chosen();
$.validator.setDefaults({ ignore: ":hidden:not(select)" });

// validation of chosen on change
if ($("select.chosen-select").length > 0) {
    $("select.chosen-select").each(function() {
        if ($(this).attr('required') !== undefined) {
            $(this).on("change", function() {
                $(this).valid();
            });
        }
    });
}

$('#edit-dealer-form').validate({
    errorElement: 'div',
    errorClass: 'help-block',
    focusInvalid: false,
    ignore: [],
    rules: {
        name: {
            required: true,
            // letterswithbasicpunc: true,
            maxlength: 50,
            minlength: 2
        },
        dealer_code: {
            required: true,
            minlength: 2,
            maxlength: 25
        },
        contact_person: {
            required: true,
            minlength: 2,
            maxlength: 100
        },
        email: {
            required: true,
            email: true
        },
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
        address: {
            required: true
        },
        owner: {
            required: true
        },
        superstockist: {
            required: true
        },
        landline: {
            required: true,
            maxlength: 20,
            minlength: 6
        },
        other_number: {
            required: true,
            maxlength: 20,
            minlength: 6
        }
    },

    messages: {
        first_name: {
            letterswithbasicpunc: "Please enter validate first name."
        },
        first_name: {
            letterswithbasicpunc: "Please enter validate first name."
        },
        mobile: {
            min: "Please enter valid mobile number"
        }
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
        form.submit();
    },
    invalidHandler: function (form) {
    }
});

$(function () {


    function stripslashes(str) {
        str = str.replace(/\\'/g, '\'');
        str = str.replace(/\\"/g, '"');
        str = str.replace(/\\0/g, '\0');
        str = str.replace(/\\\\/g, '\\');
        return str;
    }

    // on location 2 change
    $(document).on('change', '#location_2', function () {
        _current_val = $(this).val();

        location3(_current_val);
    });

    // on state change fill region drop down
    $(document).on('change', '#location_3', function () {
        _current_val = $(this).val();

        location4(_current_val);
    });
    // on region change fill city drop down
    $(document).on('change', '#location_4', function () {
        _current_val = $(this).val();
        //alert('location7');
        location7(_current_val);
    });

    // Ajax request for location3
    function location3(val) {

        _loc3 = $('#location_3');
        if (val != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/cities',
                dataType: 'json',
                data: "id=" + val,
                success: function (data) {
                    // console.log(data);
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data.code == 200) {
                        template = '<option value="" >Select</option>';

                        $.each(data.result, function (key, value) {
                            if (value.name != '') {
                                template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                            }
                        });
                        _loc3.empty();
                        _loc3.append(template).trigger("chosen:updated");
                    }

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
        }

    }

    // Ajax request for location4
    function location4(val) {

        _region = $('#location_4');

        if (val != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/cities_location4',
                dataType: 'json',
                data: "id=" + val,
                success: function (data) {
                    // console.log(data);
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data.code == 200) {
                        template = '<option value="" >Select</option>';

                        $.each(data.result, function (key, value) {
                            if (value.name != '') {
                                template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                            }
                        });
                        _region.empty();
                        _region.append(template).trigger("chosen:updated");
                    }

                },
                complete: function () {
                    // $('#loading-image').hide();
                },
                error: function () {
                }
            });
        }

    }

    // Ajax request for location5
    function location5(val2) {

        _loc5 = $('#location_5');

        if (val2 != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/location5',
                dataType: 'json',
                data: "id=" + val2,
                success: function (data) {
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data.code == 200) {
                        template = '<option value="" >Select</option>';

                        $.each(data.result, function (key, value) {
                            if (value.name != '') {
                                template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                            }
                        });
                        _loc5.empty();
                        _loc5.append(template).trigger("chosen:updated");

                    }

                },
                complete: function () {

                },
                error: function () {
                }
            });
        }

    }
    // Ajax request for location7
    function location7(val2) {

        //alert(val2);
        _loc5 = $('#location_7');

        if (val2 != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/location7_dist',
                dataType: 'json',
                data: "id=" + val2,
                success: function (data) {
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data.code == 200) {
                        template = '<option value="" >Select</option>';

                        $.each(data.result, function (key, value) {
                            if (value.name != '') {
                                template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                            }
                        });
                        _loc5.empty();
                        _loc5.append(template).trigger("chosen:updated");

                    }

                },
                complete: function () {

                },
                error: function () {
                }
            });
        }

    }

});