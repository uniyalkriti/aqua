function stripslashes(str) {
    str = str.replace(/\\'/g, '\'');
    str = str.replace(/\\"/g, '"');
    str = str.replace(/\\0/g, '\0');
    str = str.replace(/\\\\/g, '\\');
    return str;
}

//Ajax request for location3
function get_city(val) {

    _hq = $('#location3');
    if (val != '') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: domain + '/get_location4',
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
                    _hq.empty();
                    _hq.append(template);
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

//Ajax request for locatin4
function get_district(val) {
    _dist = $('#location4');
    if (val != '') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: domain + '/get_dist',
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
                    _dist.empty();
                    _dist.append(template);
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

//Ajax request for location5
function get_pincode(val) {
    _town = $('#location5');
    if (val != '') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: domain + '/get_pin',
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
                    _town.empty();
                    _town.append(template);
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

$(document).on('change', '#location2', function () {
    _current_val = $(this).val();
    get_city(_current_val);
});

$(document).on('change', '#location3', function () {
    _current_val = $(this).val();
    get_district(_current_val);
});

$(document).on('change', '#location4', function () {
    _current_val = $(this).val();
    get_pincode(_current_val);
});