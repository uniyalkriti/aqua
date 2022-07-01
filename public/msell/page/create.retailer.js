$(".chosen-select").chosen();
$('button').click(function(){
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

$(function () {

    $('#create-retailer-form').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        ignore: "",
        rules: {
            name: {
                required: true,
                letterswithbasicpunc: true,
                maxlength: 70,
                minlength: 2
            },
            retail_code: {
                required: true,
                maxlength: 25
            },
            sap_retailer_code: {
                maxlength: 50
            },
            email: {
                required: true,
                email: true
            },
            state: {
                required: true
            },
            region: {
                required: true
            },
            city: {
                required: true
            },
            distributor: {
                required: true
            },         
            address1: {
                required: true,
                maxlength: 200
            },
            address3: {
                required: true,
                minlength: 6,
                maxlength: 6,
                number: true,
                min: 1
            },
            contact_person_name: {
                required: true,
                letterswithbasicpunc: true,
                maxlength: 70,
                minlength: 2
            },
            oulet:{
                required: true
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
            $('#create-retailer-form').submit();
        },
        invalidHandler: function (form) {
        }
    });


    function stripslashes(str) {
        str = str.replace(/\\'/g, '\'');
        str = str.replace(/\\"/g, '"');
        str = str.replace(/\\0/g, '\0');
        str = str.replace(/\\\\/g, '\\');
        return str;
    }

    $(document).on('change', '#state', function () {
        _current_val = $(this).val();

        getregions(_current_val);
    });
    //            on region change fill city drop down
    $(document).on('change', '#region', function () {
        _current_val = $(this).val();

        getcities_location4(_current_val);
    });

    $(document).on('change', '#city', function () {
        _current_val = $(this).val();

        getbeat(_current_val);
    });

    $(document).on('change', '#beat', function () {
        _current_val = $(this).val();

        getbeatdistributor(_current_val);
    });

    // Ajax request for location7
    function getbeat(val2) {

        //alert(val2);
        _loc5 = $('#beat');

        if (val2 != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/location7',
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
                        _loc5.append(template);

                    }

                },
                complete: function () {

                },
                error: function () {
                }
            });
        }

    }



    function getbeatdistributor(beat) {

        _distributor = $('#distributor');
        if (beat != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/beat_wise_distributor',
                dataType: 'json',
                data: "id=" + beat,
                success: function (data) {
                    // console.log(data);
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data.code == 200) {
                        template = '<option value="" >Select</option>';
                        template = '<option value="" >Select</option>';

                        $.each(data.result, function (key, value) {
                            if (value.subsectionname != '') {
                                template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                            }
                        });
                        _distributor.empty();
                        _distributor.append(template).trigger("chosen:updated");
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
    //            Ajax request for region
    function getregions(state) {

        _region = $('#region');
        if (state != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/cities',
                dataType: 'json',
                data: "id=" + state,
                success: function (data) {
                    // console.log(data);
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data.code == 200) {
                        template = '<option value="" >Select</option>';

                        $.each(data.result, function (key, value) {
                            if (value.subsectionname != '') {
                                template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                            }
                        });
                        _region.empty();
                        _region.append(template);
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

    //            Ajax request for city
    function getcities_location4(region) {

        _city = $('#city');
        if (region != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/cities_location4',
                dataType: 'json',
                data: "id=" + region,
                success: function (data) {
                    // console.log(data);
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data.code == 200) {
                        template = '<option value="" >Select</option>';
                        template = '<option value="" >Select</option>';

                        $.each(data.result, function (key, value) {
                            if (value.subsectionname != '') {
                                template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                            }
                        });
                        _city.empty();
                        _city.append(template);
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

});