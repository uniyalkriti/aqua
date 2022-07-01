$(function () {
    function stripslashes(str) {
        str = str.replace(/\\'/g, '\'');
        str = str.replace(/\\"/g, '"');
        str = str.replace(/\\0/g, '\0');
        str = str.replace(/\\\\/g, '\\');
        return str;
    }

    $('#daily-attendance').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        ignore: "",
        rules: {
            region: {
                required: true
            },
            area: {
                required: true
            },
            territory: {
                required: true
            },
            belt: {
                required: true
            }

        },
        highlight: function (e) {
            $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
        },

        success: function (e) {
            $(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
            $(e).remove();
        },
        submitHandler: function (form) {
            $.ajax({
                type: "GET",
                url: domain + '/rs-wise-secondary-sales-report',
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
        },
        invalidHandler: function (form) {
        }
    });

    $(document).on('change', '#region', function () {
        _current_val = $(this).val();
        get_district(_current_val);
    });

    $(document).on('change', '#area', function () {
        _current_val = $(this).val();
        get_pincode(_current_val);
    });


    $(document).on('change', '#territory', function () {
        _current_val = $(this).val();
        beat(_current_val);
    });

    function get_district(val) {
        _dist = $('#area');
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

    function get_pincode(val) {
        _town = $('#territory');
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
                        template = '<option value="" >Select</option>';

                        $.each(data.result, function (key, value) {
                            if (value.name != '') {
                                template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                            }
                        });
                        _pincode.empty();
                        _pincode.append(template);
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

$('#date').datetimepicker({
    format: 'YYYY-MM-DD',
    maxDate: $.now()
});