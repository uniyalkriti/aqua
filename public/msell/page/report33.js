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
 
    $('#manualAttandence').validate({

        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        ignore: "",
        rules: {
            user: {
                required: true
            },
            date: {
                required: true
            }

        },
       
    });

    $(document).on('change', '#region', function () {
        _current_val = $(this).val();
        get_district(_current_val);
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
                        template2 = '<option disabled="disabled" value="" >Select</option>';
                        $.each(data.result2, function (key2, value2) {
                            if (value2.name != '') {
                                template2 += '<option value="' + key2 + '" >' + stripslashes(value2) + '</option>';
                            }
                        });
                        _town.empty();
                        _town.append(template2).trigger("chosen:updated");

                        //Location5
                        template3 = '<option disabled="disabled" value="" >Select</option>';
                        $.each(data.result3, function (key3, value3) {
                            if (value3.name != '') {
                                template3 += '<option value="' + key3 + '" >' + stripslashes(value3) + '</option>';
                            }
                        });
                        _beat.empty();
                        _beat.append(template3).trigger("chosen:updated");


                        //Dealer
                        template5 = '<option disabled="disabled" value="" >Select</option>';
                        $.each(data.dealers, function (key5, value5) {
                            if (value5.name != '') {
                                template5 += '<option value="' + key5 + '" >' + stripslashes(value5) + '</option>';
                            }
                        });
                        _distributor.empty();
                        _distributor.append(template5).trigger("chosen:updated");
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
                url: domain + '/get_pin',
                dataType: 'json',
                data: "id=" + val,
                success: function (data) {
                    // console.log(data);
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data.code == 200) {
                        //town data
                        template = '<option disabled="disabled" value="" >Select</option>';
                        $.each(data.towns, function (key, value) {
                            if (value.name != '') {
                                template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                            }
                        });
                        _town.empty();
                        _town.append(template).trigger("chosen:updated");
                        //dealers data
                        template2 = '<option disabled="disabled" value="" >Select</option>';
                        $.each(data.dealers, function (key2, value2) {
                            if (value2.name != '') {
                                template2 += '<option value="' + key2 + '" >' + stripslashes(value2) + '</option>';
                            }
                        });
                        _distributor.empty();
                        _distributor.append(template2).trigger("chosen:updated");
                        //beat data
                        template3 = '<option disabled="disabled" value="" >Select</option>';
                        $.each(data.beats, function (key3, value3) {
                            if (value3.name != '') {
                                template3 += '<option value="' + key3 + '" >' + stripslashes(value3) + '</option>';
                            }
                        });
                        _beat.empty();
                        _beat.append(template3).trigger("chosen:updated");
                        //user data
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
                url: domain + '/get_beat',
                dataType: 'json',
                data: "id=" + val,
                success: function (data) {
                    // console.log(data);
                    if (data.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data.code == 200) {
                        //dealers data
                        template2 = '<option disabled="disabled" value="" >Select</option>';
                        $.each(data.dealers, function (key2, value2) {
                            if (value2.name != '') {
                                template2 += '<option value="' + key2 + '" >' + stripslashes(value2) + '</option>';
                            }
                        });
                        _distributor.empty();
                        _distributor.append(template2).trigger("chosen:updated");
                        //beat data
                        template3 = '<option disabled="disabled" value="" >Select</option>';
                        $.each(data.beats, function (key3, value3) {
                            if (value3.name != '') {
                                template3 += '<option value="' + key3 + '" >' + stripslashes(value3) + '</option>';
                            }
                        });
                        _beat.empty();
                        _beat.append(template3).trigger("chosen:updated");
                        //user data
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
                url: domain + '/get_beat_users',
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
});

$("#month").datetimepicker  ( {
    clear: "Clear",
    format: 'YYYY-MM'
});