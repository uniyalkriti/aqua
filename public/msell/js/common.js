function confirmAction(heading, name, action_id, tab, act) {
    $.confirm({
        title: heading,
        content: 'Are you sure want to ' + act + ' ' + name + '?',
        buttons: {
            confirm: function () {
                takeAction(name, action_id, tab, act);
                $.alert('Done!');
                window.setTimeout(function () {
                    location.reload()
                }, 3000);
            },
            cancel: function () {
                $.alert('Canceled!');
            }
//                    somethingElse: {
//                        text: 'Something else',
//                        btnClass: 'btn-blue',
//                        keys: ['enter', 'shift'],
//                        action: function(){
//                            $.alert('Something else?');
//                        }
//                    }
        }
    });
}

function takeAction(module, action_id, tab, act) {


    if (action_id != '') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: domain + '/takeAction',
            dataType: 'json',
            data: {'module': module, 'action_id': action_id, 'tab': tab, 'act': act},
            success: function (data) {
                // console.log(data);
                if (data.code == 401) {
                    //  $('#loading-image').hide();
                }
                else if (data.code == 200) {

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

$(document).ready(function () {
//        it will hide flash message after timeout
    var timeout = 3000;
    $(".hidemsg").delay(timeout).fadeOut(300);
});

function search() {

    if ($('#search').val()) {
        document.getElementById('filterForm').submit();
    }
}
