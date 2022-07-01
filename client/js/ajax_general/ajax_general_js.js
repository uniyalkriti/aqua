function getdataObject(mtarget, progress_div, wcase, updatetextid)
{
    //alert(updatetextid);
    ajax = gethttprequest_object(); // making an ajax object this will help make multiple asychronous call on one page
    if (ajax)
    {
       //alert(mtarget);
        // call the php script. use the get method. Pass the username in the url
        ajax.open('get', 'js/ajax_general/ajax_general_php.php?pid=' + encodeURIComponent(mtarget) + '&wcase=' + wcase);
    //alert(wcase);
        //Function that handles the response
        //ajax.onreadystatechange = handle_check; // when you dont want to pass argument to call back function
        ajax.onreadystatechange = function() {
            resp_getdataObject(progress_div, updatetextid);
        }
        //send the request
        ajax.send(null);
    }
    else
    {
        //cant use ajax!
        document.getElementById(progress_div).innerHTML = 'The availability of location will be confirmed upon form submission.';
    }
} // end of check_username() function

//Function that handles the response from the php script
function resp_getdataObject(progress_div, updatetextid)
{
    //if everything's OK
    // alert(ajax.readyState);
    if ((ajax.readyState == 4) && (ajax.status == 200))
    {
        // alert(ajax.responseText);
        var datafetch = ajax.responseText.split('<$>');
        if (datafetch[0] == 'TRUE')
        {

            var plelement = datafetch[1].split('<$$>');
           
            for (var i = 0; i < updatetextid.length; i++)
            {
                if (updatetextid[i].type == 'select-one')
                {
                    var selectval = plelement[i].split('|');
                    var text = new Array();
                    var value = new Array();
                    for (var j = 0; j < selectval.length; j++)
                    {
                        var values = selectval[j].split('<$$$>');
                        text[j] = values[1];
                        value[j] = values[0];
                    }
                    addOption(updatetextid[i], text, value);
                }
                else if (updatetextid[i].type == 'text' || updatetextid[i].type == 'hidden')
                {                    
                    updatetextid[i].value = plelement[i];
                }
            }
        }
        else
        {
            // alert(datafetch[1]);
        }
        //document.getElementById(progress_div).style.display = 'none';
    }
    else
    {
        //document.getElementById(progress_div).style.display = 'block';
        //document.getElementById(progress_div).innerHTML = '<img src="images/loader.gif" />fetching data ...';
    }
}// End of handle_check() function

function getdata(pullId, progress_div, wcase, updatetextid)
{
    ajax = gethttprequest_object(); // making an ajax object this will help make multiple asychronous call on one page
    //confirm that the object is usable
    //alert(pullId);
    //alert(wcase);
    if (ajax)
    {
        // call the php script. use the get method. Pass the username in the url
        ajax.open('get', 'js/ajax_general/ajax_general_php.php?pid=' + encodeURIComponent(pullId) + '&wcase=' + wcase);

        //Function that handles the response
        //ajax.onreadystatechange = handle_check; // when you dont want to pass argument to call back function
        ajax.onreadystatechange = function() {
            resp_getdata(progress_div, updatetextid);
        }
        //send the request
        ajax.send(null);
    }
    else
    {
        //cant use ajax!
        document.getElementById(progress_div).innerHTML = 'The availability of location will be confirmed upon form submission.';
    }
} // end of check_username() function

//Function that handles the response from the php script
function resp_getdata(progress_div, updatetextid)
{
    //if everything's OK
    if ((ajax.readyState == 4) && (ajax.status == 200))
    {
        //alert(ajax.responseText);
        var datafetch = ajax.responseText.split('<$>');
        if (datafetch[0] == 'TRUE')
        {
            var plelement = datafetch[1].split('<$$>');
            //alert(updatetextid);
            var ids = updatetextid.split('<$>');
            for (var i = 0; i < ids.length; i++)
            {
                document.getElementById(ids[i]).value = plelement[i];
            }
        }
        else
        {
            alert(datafetch[1]);
        }
        if (document.getElementById(progress_div))
            document.getElementById(progress_div).style.display = 'none';
    }
    else
    {
        if (document.getElementById(progress_div)) {
            document.getElementById(progress_div).style.display = 'block';
            document.getElementById(progress_div).innerHTML = '<img src="images/loader.gif" />fetching data ...';
        }
    }
}// End of handle_check() function
