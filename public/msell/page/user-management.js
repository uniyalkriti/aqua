

$('.user-modal').click(function () {
    $('#result').html('');
    $('#uuid').val($(this).attr('userid'));
});

//form submit to search distributor
$("#filter_distributor").submit(function(e) {


    var form = $(this);
    var url = form.attr('action');
    var target=$('#result');

    $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(), // serializes the form's elements.
        success: function(data)
        {
            target.html(data); // show response from the php script.
        }
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});

$(document).on('submit',"#distributor-beat", function(e) {
    $('#result2').html('');
    $('#result3').html('');
    var form = $(this);
    var url = form.attr('action');
    var target=$('#result2');

    $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(), // serializes the form's elements.
        success: function(data)
        {
            target.html(data); // show response from the php script.
        }
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});
$(document).on('submit',"#assign-beat", function(e) {
    $('#result3').html('');
    var form = $(this);
    var url = form.attr('action');
    var target=$('#result3');

    $.ajax({
        type: "POST",
        url: url,
        dataType: 'json',
        data: form.serialize(), // serializes the form's elements.
        success: function(data)
        {
            if (data.code == 200) {
                toastr.success(data.message);
            }
            else{
                toastr.error(data.message);
            }
            // setTimeout("window.location.reload(1)",3000);
            $(function () {
               $('#myModal2').modal('toggle');
               $('#myModal').modal('toggle');
            });
        }
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});
$(document).on('submit',"#retailer-assign", function(e) {
    $('#result4').html('');
    var form = $(this);
    var url = form.attr('action');
    var target=$('#result4');

    $.ajax({
        type: "POST",
        url: url,
        dataType: 'json',
        data: form.serialize(), // serializes the form's elements.
        success: function(data)
        {
            if (data.code == 200) {
                toastr.success(data.message);
            }
            else{
                toastr.error(data.message);
            }
            setTimeout("window.location.reload(1)",3000);
        }
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});

$(document).on('change', '#state', function () {
    _current_val = $(this).val();

    getcities(_current_val);
});
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
    location_data(_current_val,4);
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

function location_data(val,level) {
    _append_box=$('#location_'+level);
    if (val != '') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: domain + '/getLocationForAssign',
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
$(document).on('change', '#location3', function () {
        _current_val = $(this).val();
        custom_user_Data(_current_val);
    });

    $(document).on('change', '#location4', function () {
        _current_val = $(this).val();
        custom_location_data(_current_val,5);
    });
    $(document).on('change', '#location5', function () {
        _current_val = $(this).val();
        custom_location_data(_current_val,6);
    });
   
    function custom_user_Data(val) {
        _append_box2=$('#user');
        if (val != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/statndard_filter_onchange_for_user',
                dataType: 'json',
                data: "id=" + val,
                success: function (data2) {
                    if (data2.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data2.code == 200) {
                        var level=4;
                        $.ajax({
                            type: "POST",
                            url: domain + '/statndard_filter_onchange',
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
                                    $('#location4').empty();
                                    $('#location4').append(template).trigger('chosen:updated');

                                }

                            },
                            complete: function () {
                                // $('#loading-image').hide();
                            },
                            error: function () {
                            }
                        });

                        //Location 3
                        template2 = '<option value="" >Select</option>';
                        $.each(data2.result, function (key2, value2) {
                            if (value2.name != '') {
                                template2 += '<option value="' + key2 + '" >' + stripslashes(value2) + '</option>';
                            }
                        });
                        _append_box2.empty();
                        _append_box2.append(template2).trigger('chosen:updated');

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

    function custom_location_data(val,level) {
        _append_box=$('#location'+level);
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
                        _append_box.append(template).trigger('chosen:updated');

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
// function getcities(state) {

//     _city = $('#city');
//     if (state != '') {
//         $.ajaxSetup({
//             headers: {
//                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//             }
//         });
//         $.ajax({
//             type: "POST",
//             url: domain + '/cities',
//             dataType: 'json',
//             data: "id=" + state,
//             success: function (data) {
//                 // console.log(data);
//                 if (data.code == 401) {
//                     //  $('#loading-image').hide();
//                 }
//                 else if(data.code == 200) {
//                     template = '';

//                     $.each(data.result, function (key, value) {
//                         if (value.subsectionname != '') {
//                             template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
//                         }
//                     });
//                     _city.empty().chosen();
//                     _city.append(template).trigger("chosen:updated");
//                 }

//             },
//             complete: function () {
//                 // $('#loading-image').hide();
//             },
//             error: function () {
//             }
//         });
//     }

// }

function stripslashes(str) {
    str = str.replace(/\\'/g, '\'');
    str = str.replace(/\\"/g, '"');
    str = str.replace(/\\0/g, '\0');
    str = str.replace(/\\\\/g, '\\');
    return str;
}

function search() {

    if ($('#search').val()) {
        document.getElementById('filterForm').submit();
    }
}

function searchReset() {
    $('#search').val('');
    $('#filterForm').submit();
}

function formReset() {
    $('#search').val('');
    $('#city').val('');
    $('#state').val('');
    $('#gender').val('');
    $('#filterForm').submit();
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