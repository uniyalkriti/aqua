<script>
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
        custom_user_Data(_current_val,4);
    });
    $(document).on('change', '#dealer', function () {
        _current_val = $(this).val();
        custom_dealer_Data(_current_val,4);
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
   
    function custom_user_Data(val) {
        _append_box2=$('#user');
        _append_box3=$('#dealer');
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
                                    $('#location_4').empty();
                                    $('#location_4').append(template).trigger('chosen:updated');

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
                                template2 += '<option value="' + value2.user_id + '" >' + stripslashes(value2.user_name) + '</option>';
                            }
                        });
                        _append_box2.empty();
                        _append_box2.append(template2).trigger('chosen:updated');

                        template3 = '<option value="" >Select</option>';
                        $.each(data2.query_dealer, function (key2, value2) {
                            if (value2.name != '') {
                                template3 += '<option value="' + key2 + '" >' + stripslashes(value2) + '</option>';
                            }
                        });
                        _append_box3.empty();
                        _append_box3.append(template3).trigger('chosen:updated');

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
            _append_box3.empty();
            _append_box2.empty();
        }
    }
    function custom_dealer_Data(val) {
        _append_box2=$('#location_7');
        if (val != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: domain + '/statndard_filter_onchange_for_dealer',
                dataType: 'json',
                data: "id=" + val,
                success: function (data2) {
                    if (data2.code == 401) {
                        //  $('#loading-image').hide();
                    }
                    else if (data2.code == 200) {
                        var level=4;

                        //Location 3
                        template2 = '<option value="" >Select</option>';
                        $.each(data2.query_dealer, function (key2, value2) {
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
            _append_box2.empty();
        }
    }
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
                url: domain + '/getLocationForStandaradFilter',
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
                        if(data.dealer_flag == 1)
                        {
                            dealer_template = '<option value="" >Select</option>';
                            $.each(data.dealer, function (key, value) {
                                if (value.name != '') {
                                    dealer_template += '<option value="' + key + '" >' + stripslashes(value) + '</option>';
                                }
                            });
                            $('#dealer').empty();
                            $('#dealer').append(dealer_template).trigger("chosen:updated");

                        }
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

    function stripslashes(str) {
        str = str.replace(/\\'/g, '\'');
        str = str.replace(/\\"/g, '"');
        str = str.replace(/\\0/g, '\0');
        str = str.replace(/\\\\/g, '\\');
        return str;
    }
</script>