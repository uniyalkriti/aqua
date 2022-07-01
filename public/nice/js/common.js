$(document).on('change','#current_project',function() {
    var x = document.cookie;
    // alert('current_cookie'+x);
    _current=$(this).val();
    // alert(_current);
    document.cookie = "current_project="+_current;
    location.reload();
});