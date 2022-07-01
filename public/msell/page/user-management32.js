
$(".chosen-select").chosen();
$('button').click(function(){
    $(".chosen-select").trigger("chosen:updated");
});

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
        data: form.serialize(), // serializes the form's elements.
        success: function(data)
        {
            target.html(data); // show response from the php script.
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
                    _append_box.append(template);

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

function getcities(state) {

    _city = $('#city');
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
                else if(data.code == 200) {
                    template = '';

                    $.each(data.result, function (key, value) {
                        if (value.subsectionname != '') {
                            template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                        }
                    });
                    _city.empty().chosen();
                    _city.append(template).trigger("chosen:updated");
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

jQuery(function ($) {

    //load city data on page refersh

    getcities($('#state').val());

    ////

    setTimeout(function () {
        $($('.tableTools-container')).find('a.dt-button').each(function () {
            var div = $(this).find(' > div').first();
            if (div.length == 1) div.tooltip({container: 'body', title: div.parent().text()});
            else $(this).tooltip({container: 'body', title: $(this).text()});
        });
    }, 500);


    /////////////////////////////////
    //table checkboxes
    $('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);

    //select/deselect all rows according to table header checkbox
    $('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function () {
        var th_checked = this.checked;//checkbox inside "TH" table header

        $('#dynamic-table').find('tbody > tr').each(function () {
            var row = this;
            if (th_checked) myTable.row(row).select();
            else myTable.row(row).deselect();
        });
    });

    //select/deselect a row when the checkbox is checked/unchecked
    $('#dynamic-table').on('click', 'td input[type=checkbox]', function () {
        var row = $(this).closest('tr').get(0);
        if (this.checked) myTable.row(row).deselect();
        else myTable.row(row).select();
    });


    $(document).on('click', '#dynamic-table .dropdown-toggle', function (e) {
        e.stopImmediatePropagation();
        e.stopPropagation();
        e.preventDefault();
    });


    //And for the first simple table, which doesn't have TableTools or dataTables
    //select/deselect all rows according to table header checkbox
    var active_class = 'active';
    $('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function () {
        var th_checked = this.checked;//checkbox inside "TH" table header

        $(this).closest('table').find('tbody > tr').each(function () {
            var row = this;
            if (th_checked) $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
            else $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
        });
    });

    //select/deselect a row when the checkbox is checked/unchecked
    $('#simple-table').on('click', 'td input[type=checkbox]', function () {
        var $row = $(this).closest('tr');
        if ($row.is('.detail-row ')) return;
        if (this.checked) $row.addClass(active_class);
        else $row.removeClass(active_class);
    });


    /********************************/
    //add tooltip for small view action buttons in dropdown menu
    $('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});

    //tooltip placement on right or left
    function tooltip_placement(context, source) {
        var $source = $(source);
        var $parent = $source.closest('table')
        var off1 = $parent.offset();
        var w1 = $parent.width();

        var off2 = $source.offset();
        //var w2 = $source.width();

        if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2)) return 'right';
        return 'left';
    }


    /***************/
    $('.show-details-btn').on('click', function (e) {
        e.preventDefault();
        $(this).closest('tr').next().toggleClass('open');
        $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
    });
    /***************/


})

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