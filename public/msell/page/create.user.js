
$(".chosen-select").chosen();
$('button').click(function(){
    $(".chosen-select").trigger("chosen:updated");
});

$(document).on('change', '#state', function () {
    _current_val = $(this).val();

    getregions(_current_val);
});

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
            url: domain + '/get_location4',
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


    });

$(".chosen-select").chosen();
$('button').click(function () {
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

    function stripslashes(str) {
        str = str.replace(/\\'/g, '\'');
        str = str.replace(/\\"/g, '"');
        str = str.replace(/\\0/g, '\0');
        str = str.replace(/\\\\/g, '\\');
        return str;
    }

//            on region change fill city drop down
    $(document).on('change', '#region', function () {
        _current_val = $(this).val();

        getcities_location4(_current_val);
    });

    $(document).on('change', '#city', function () {
        _current_val = $(this).val();

        getdistributor(_current_val);
    });

    $(document).on('change', '#seniorrole', function () {
        _current_val = $(this).val();

        getseniorname(_current_val);
    });

    //            Ajax request for Senior Name

    function getseniorname(seniorrole) {
        //alert("jjjk");
        _seniorname = $('#seniorname');
        if (seniorrole != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/senior_name',
                dataType: 'json',
                data: "id=" + seniorrole,
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
                        _seniorname.empty();
                        _seniorname.append(template);
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

    //            Ajax request for Distributor

    function getdistributor(city) {

        _distributor = $('#distributor');
        if (city != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/city_wise_distributor',
                dataType: 'json',
                data: "id=" + city,
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



$(".chosen-select").chosen();
$('button').click(function () {
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
