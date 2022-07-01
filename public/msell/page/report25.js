$(".chosen-select").chosen();
$('button').click(function () {
    $(".chosen-select").trigger("chosen:updated");
});
$(function () {
    function stripslashes(str) {
        str = str.replace(/\\'/g, '\'');
        str = str.replace(/\\"/g, '"');
        str = str.replace(/\\0/g, '\0');
        str = str.replace(/\\\\/g, '\\');
        return str;
    }

    $('#product-investigation').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        ignore: "",
        rules: {
           
            user: {
                required: true
            }
        },
        messages:{
            user: "Please select user"
            
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
                url: domain + '/product-investigation-report',
                // dataType: 'json',
                data: $('form').serialize(),
                success: function (data) {
                  //  alert(data);
                    $('#ajax-table').html(data);
                    $('#product-investigation').collapse('hide');
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

    $(document).on('change', '#zone', function () {
        _current_val = $(this).val();
        get_zone_user(_current_val);
    });
    $(document).on('change', '#region', function () {
        _current_val = $(this).val();
        get_region_user(_current_val);
    });

    $(document).on('change', '#state', function () {
        _current_val = $(this).val();
        get_state_user(_current_val);
    });

    function get_region_user(val) {
        _region=$('#region');
        _state=$('#state');
        _position=$('#position');
        _user=$('#user');
        if (val != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/get_region_user',
                dataType: 'json',
                data: "id=" + val,
                success: function (data) {
                    // console.log(data);
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data.code == 200) {

                        //state data
                        template2 = '<option disabled="disabled" value="" >Select</option>';
                        $.each(data.state, function (key2, value2) {
                            if (value2.name != '') {
                                template2 += '<option value="' + key2 + '" >' + stripslashes(value2) + '</option>';
                            }
                        });
                        _state.empty();
                        _state.append(template2).trigger("chosen:updated");
                        //user data
                        template3 = '<option disabled="disabled" value="" >Select</option>';
                        $.each(data.user, function (key3, value3) {
                            if (value3.name != '') {
                                template3 += '<option value="' + key3 + '" >' + stripslashes(value3) + '</option>';
                            }
                        });
                        _user.empty();
                        _user.append(template3).trigger("chosen:updated");

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

    function get_state_user(val) {
        _user=$('#user');
        if (val != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/get_state_user',
                dataType: 'json',
                data: "id=" + val,
                success: function (data) {
                    // console.log(data);
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data.code == 200) {
                        template = '<option value="" >Select</option>';

                        $.each(data.user, function (key, value) {
                            if (value.name != '') {
                                template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                            }
                        });
                        _user.empty();
                        _user.append(template).trigger("chosen:updated");
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

    function get_zone_user(val) {
        _region=$('#region');
        _state=$('#state');
        _position=$('#position');
        _user=$('#user');
        if (val != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/get_zone_user',
                dataType: 'json',
                data: "id=" + val,
                success: function (data) {
                    // console.log(data);
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data.code == 200) {
                        //region data
                        template = '<option disabled="disabled" value="" >Select</option>';
                        $.each(data.region, function (key, value) {
                            if (value.name != '') {
                                template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                            }
                        });
                        _region.empty();
                        _region.append(template).trigger("chosen:updated");

                        //state data
                        template2 = '<option disabled="disabled" value="" >Select</option>';
                        $.each(data.state, function (key2, value2) {
                            if (value2.name != '') {
                                template2 += '<option value="' + key2 + '" >' + stripslashes(value2) + '</option>';
                            }
                        });
                        _state.empty();
                        _state.append(template2).trigger("chosen:updated");
                        //user data
                        template3 = '<option disabled="disabled" value="" >Select</option>';
                        $.each(data.user, function (key3, value3) {
                            if (value3.name != '') {
                                template3 += '<option value="' + key3 + '" >' + stripslashes(value3) + '</option>';
                            }
                        });
                        _user.empty();
                        _user.append(template3).trigger("chosen:updated");
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