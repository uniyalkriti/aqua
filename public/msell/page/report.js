$(".chosen-select").chosen();
$('button').click(function () {
    $(".chosen-select").trigger("chosen:updated");
});

// $.validator.setDefaults({ ignore: ":hidden:not(.chosen-select)" });

function stripslashes(str) {
    str = str.replace(/\\'/g, '\'');
    str = str.replace(/\\"/g, '"');
    str = str.replace(/\\0/g, '\0');
    str = str.replace(/\\\\/g, '\\');
    return str;
}


// $(document).on('change', '#beat', function () {
//     _current_val = $(this).val();
//     outlet(_current_val);
// });
$(document).on('change', '#distributor', function () {
    _current_val = $(this).val();
  //  alert(_current_val);
    distributorBeat(_current_val);
});

$(document).on('change', '#belt', function () {
    _current_val = $(this).val();
  //  alert(_current_val);
    distributor(_current_val);
});
//Distributor beat
function distributorBeat(val2) {
    _loc5 = $('#beat');

    if (val2 != '') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: domain + '/distributors_beat',
            dataType: 'json',
            data: "id=" + val2,
            success: function (data) {
                if (data.code == 401) {
                    //  $('#loading-image').hide();
                }
                else if (data.code == 200) {
                    template = '<option disabled="disabled" value="" >Select</option>';
                    $.each(data.result, function (key, value) {
                        if (value.name != '') {
                            template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                        }
                    });
                    _loc5.empty();
                    // _loc5.append(template).trigger("chosen:updated");
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
function distributor(val2) {
    _loc5 = $('#beat');
    _d=$('.distributor');

    if (val2 != '') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: domain + '/location7_id',
            dataType: 'json',
            data: "id=" + val2,
            success: function (data) {
                if (data.code == 401) {
                    //  $('#loading-image').hide();
                }
                else if (data.code == 200) {
                    //1
                    template = '<option disabled="disabled" value="" >Select</option>';
                    $.each(data.result, function (key, value) {
                        if (value.name != '') {
                            template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                        }
                    });
                    _loc5.empty();
                    _loc5.append(template).trigger("chosen:updated");
                    //2

                    template2 = '<option disabled="disabled" value="" >Select</option>';
                    $.each(data.result2, function (key2, value2) {
                        if (value2.name != '') {
                            template2 += '<option value="' + key2 + '" >' + stripslashes(value2) + '</option>';
                        }
                    });
                    _d.empty();
                    _d.append(template2).trigger("chosen:updated");
                }
            },
            complete: function () {
            },
            error: function () {
            }
        });
    }

}

function outlet(val2) {
    _dis = $('#distributor');

    if (val2 != '') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: domain + '/outlet_data',
            dataType: 'json',
            data: "id=" + val2,
            success: function (data) {
                if (data.code == 401) {
                    //  $('#loading-image').hide();
                }
                else if (data.code == 200) {
                    template = '<option disabled="disabled" value="" >Select</option>';
                    $.each(data.result, function (key, value) {
                        if (value.name != '') {
                            template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                        }
                    });
                    _dis.empty();
                    _dis.append(template).trigger("chosen:updated");
                }
            },
            complete: function () {
            },
            error: function () {
            }
        });
    }

}
$('#daily-attendance').validate({
    errorElement: 'div',
    errorClass: 'help-block',
    focusInvalid: false,
    ignore: "",
    rules: {
        from_date: {
            required: true
        },
        to_date: {
            required: true
        }
    },

    messages: {},
    errorElement : 'div',
    errorLabelContainer: '.errorTxt',
    highlight: function (e) {
        $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
    },
    success: function (e) {
        $(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
        $(e).remove();
        // $('#edit-retailer-form').submit();
    },

    submitHandler: function (form) {
        $('#distributor-err').hide();
        _distributor=$('#distributor').val();
        if(_distributor!=null) {
            $.ajax({
                type: "GET",
                url: domain + '/month_s_primary_and_secondary_sales_plan-report',
                // dataType: 'json',
                data: $('form').serialize(),
                success: function (data) {
                    $('#ajax-table').html(data);
                    $('#daily-attendance').collapse('hide');
                },
                complete: function () {
                },
                error: function () {
                }
            });
        }
        else{
            $('#distributor-err').show();
        }
    },
    invalidHandler: function (form) {
    }
});

$('#from_date').datetimepicker({
    format: 'YYYY-MM-DD'
}).on('dp.change', function (e) {
    var incrementDay = moment(new Date(e.date));
    incrementDay.add(0, 'days');
    $('#to_date').data('DateTimePicker').minDate(incrementDay);
    $(this).data("DateTimePicker").hide();
});

$('#to_date').datetimepicker({
    format: 'YYYY-MM-DD'
}).on('dp.change', function (e) {
    var decrementDay = moment(new Date(e.date));
    decrementDay.subtract(0, 'days');
    $('#from_date').data('DateTimePicker').maxDate(decrementDay);
    $(this).data("DateTimePicker").hide();
});