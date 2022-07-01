 $(".chosen-select").chosen();
$('button').click(function () {
    $(".chosen-select").trigger("chosen:updated");
});

// distributor change
function distributor(val) {
    _town = $('#territory');
    _distributor = $('#distributor');
    _beat = $('#belt');
    _user = $('#user');
    if (val != '') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: domain + '/get_distributor',
            dataType: 'json',
            data: "id=" + val,
            success: function (data) {
                // console.log(data);
                if (data.code == 401) {
                    //  $('#loading-image').hide();
                }
                else if (data.code == 200) {
                    template4 = '<option disabled="disabled" value="" >Select</option>';
                    $.each(data.user_data, function (key4, value4) {
                        if (value4.name != '') {
                            template4 += '<option value="' + key4 + '" >' + stripslashes(value4) + '</option>';
                        }
                    });
                    _user.empty();
                    _user.append(template4).trigger("chosen:updated");
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
function stripslashes(str) {
    str = str.replace(/\\'/g, '\'');
    str = str.replace(/\\"/g, '"');
    str = str.replace(/\\0/g, '\0');
    str = str.replace(/\\\\/g, '\\');
    return str;
}

$(function () {

    $('#sale-order').validate({
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
        messages: {
            from_date:"Enter date",
            to_date:"Enter date"
        },
        highlight: function (e) {
            $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
        },

        success: function (e) {
            $(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
            $(e).remove();
        },
        submitHandler: function (form) {
            $('#ajax-table').html('');
            $('#ajax-table').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 10px 50%;"></i>');
            $.ajax({
                type: "GET",
                url: domain + '/dsrMonthlyReport',
                // dataType: 'json',
                data: $('form').serialize(),
                success: function (data) {
                    $('#ajax-table').html(data);
                    $('#sale-order').collapse('hide');
                },
                complete: function () {
                    $('#m-spinner').remove();
                },
                error: function () {
                    $('#m-spinner').remove();
                }
            });
        },
        invalidHandler: function (form) {
        }
    });

    $(document).on('change', '#region', function () {
        _current_val = $(this).val();
        get_district(_current_val);
    });

    $(document).on('change', '#distributor', function () {
        _current_val = $(this).val();
        distributor(_current_val);
    });

    $(document).on('change', '#area', function () {
        _current_val = $(this).val();
        get_pincode(_current_val);
    });

    $(document).on('change', '#belt', function () {
        _current_val = $(this).val();
        users(_current_val);
    });

    $(document).on('change', '#territory', function () {
        _current_val = $(this).val();
        beat(_current_val);
    });

    function get_district(val) {
        _dist = $('#area');
        _town= $('#territory');
        _beat= $('#belt');
        _user= $('#user');
        _distributor=$('#distributor');
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
                        //Users
                        template4 = '<option disabled="disabled" value="" >Select</option>';
                        $.each(data.user, function (key4, value4) {
                            if (value4.name != '') {
                                template4 += '<option value="' + key4 + '" >' + stripslashes(value4) + '</option>';
                            }
                        });
                        _user.empty();
                        _user.append(template4).trigger("chosen:updated");


                        //Location 3
                        template = '<option disabled="disabled" value="" >Select</option>';
                        $.each(data.result, function (key, value) {
                            if (value.name != '') {
                                template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                            }
                        });
                        _dist.empty();
                        _dist.append(template).trigger("chosen:updated");

                        //Location4
                        // template2 = '<option value="" >Select</option>';
                        // $.each(data.result2, function (key2, value2) {
                        //     if (value2.name != '') {
                        //         template2 += '<option value="' + key2 + '" >' + stripslashes(value2) + '</option>';
                        //     }
                        // });
                        // _town.empty();
                        // _town.append(template2).trigger("chosen:updated");

                        //Location5
                        // template3 = '<option value="" >Select</option>';
                        // $.each(data.result3, function (key3, value3) {
                        //     if (value3.name != '') {
                        //         template3 += '<option value="' + key3 + '" >' + stripslashes(value3) + '</option>';
                        //     }
                        // });
                        // _beat.empty();
                        // _beat.append(template3).trigger("chosen:updated");


                        //Dealer
                        // template5 = '<option value="" >Select</option>';
                        // $.each(data.dealers, function (key5, value5) {
                        //     if (value5.name != '') {
                        //         template5 += '<option value="' + key5 + '" >' + stripslashes(value5) + '</option>';
                        //     }
                        // });
                        // _distributor.empty();
                        // _distributor.append(template5).trigger("chosen:updated");
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

    function get_pincode(val) {
        _town = $('#territory');
        _user= $('#user');
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

                        template4 = '<option disabled="disabled" value="" >Select</option>';
                        $.each(data.user_data, function (key4, value4) {
                            if (value4.name != '') {
                                template4 += '<option value="' + key4 + '" >' + stripslashes(value4) + '</option>';
                            }
                        });
                        _user.empty();
                        _user.append(template4).trigger("chosen:updated");
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

    function beat(val) {
        _pincode = $('#belt');
        if (val != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/get_beat',
                dataType: 'json',
                data: "id=" + val,
                success: function (data) {
                    // console.log(data);
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
                        _pincode.empty();
                        _pincode.append(template).trigger("chosen:updated");;
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

    function users(val) {
        _user = $('#user');
        if (val != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/get_belt_users',
                dataType: 'json',
                data: "id=" + val,
                success: function (data) {
                    // console.log(data);
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
                        _user.empty();
                        _user.append(template).trigger("chosen:updated");;
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

function fnExcelReport() {
    var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
    var textRange;
    var j = 0;
    tab = document.getElementById('simple-table'); // id of table

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

// $(function () {
//     $('#from_date,#to_date').datetimepicker({
//         viewMode: 'days',
//         format: 'YYYY-MM-DD',
//         // useCurrent: true,
//         // maxDate: moment()
//     });
//
// });