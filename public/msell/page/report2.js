$(".chosen-select").chosen();
$('button').click(function () {
    $(".chosen-select").trigger("chosen:updated");
});

$(function () {
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

    function stripslashes(str) {
        str = str.replace(/\\'/g, '\'');
        str = str.replace(/\\"/g, '"');
        str = str.replace(/\\0/g, '\0');
        str = str.replace(/\\\\/g, '\\');
        return str;
    }

    $('#market-beat-plan').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        ignore: "",
        rules: {
            month: {
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
            $('#dis-err').hide();
            _distributor_data=$('#distributor').val();
            if(_distributor_data!='') {
                $('#ajax-table').html('');
                $('#ajax-table').html('<i class="fa fa-spinner fa-spin" id="m-spinner" style="font-size:42px;margin: 10px 50%;"></i>');
                $.ajax({
                    type: "GET",
                    url: domain + '/market-beat-plan-report',
                    // dataType: 'json',
                    data: $('form').serialize(),
                    success: function (data) {
                        $('#ajax-table').html(data);
                        $('#market-beat-plan').collapse('hide');
                    },
                    complete: function () {
                        $('#m-spinner').remove();
                    },
                    error: function () {
                        $('#m-spinner').remove();
                    }
                });
            }
            else{
                $('#dis-err').show();
            }
        },
        invalidHandler: function (form) {
        }
    });

    $(document).on('change', '#distributor', function () {
        _current_val = $(this).val();
        distributorBeat(_current_val);
    });

    $(document).on('change', '#belt', function () {
        _current_val = $(this).val();
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
});

$('#from_date').datetimepicker({
    format: 'YYYY-MM-DD'
}).on('dp.change', function (e) {
    var incrementDay = moment(new Date(e.date));
    incrementDay.add(1, 'days');
    $('#to_date').data('DateTimePicker').minDate(incrementDay);
    $(this).data("DateTimePicker").hide();
});

$('#to_date').datetimepicker({
    format: 'YYYY-MM-DD'
}).on('dp.change', function (e) {
    var decrementDay = moment(new Date(e.date));
    decrementDay.subtract(1, 'days');
    $('#from_date').data('DateTimePicker').maxDate(decrementDay);
    $(this).data("DateTimePicker").hide();
});

$("#month").datetimepicker  ( {
    clear: "Clear",
    format: 'YYYY-MM'
});