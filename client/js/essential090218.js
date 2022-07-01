// this function will calculate the inner width of wrapper div
function setSize()
{
// code from where i took it http://www.howtocreate.co.uk/tutorials/javascript/browserwindow
    var myWidth = 0, myHeight = 0;
    if (typeof (window.innerWidth) == 'number')
    {
        //Non-IE
        myWidth = window.innerWidth;
        myHeight = window.innerHeight;
    }
    else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight))
    {
        //IE 6+ in 'standards compliant mode'
        myWidth = document.documentElement.clientWidth;
        myHeight = document.documentElement.clientHeight;
    }
    else if (document.body && (document.body.clientWidth || document.body.clientHeight))
    {
        //IE 4 compatible
        myWidth = document.body.clientWidth;
        myHeight = document.body.clientHeight;
    }
    document.getElementById('wrapper').style.height = (myHeight - 100) + 'px';  //original 136
}// function setsize ends here

// The code in this includes the basic js functions which will be used in most of the pages and is thus included at the start
//<!-- This script will open up the pop up window -->
function PopupCenter(pageURL, title, w, h) {
    var left = (screen.width / 2) - (w / 2);
    var top = (screen.height / 2) - (h / 2);
    var targetWin = window.open(pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
}


//<!-- This script will open up the pop up window scrolling yes option -->
function PopupCenter1(pageURL, title, w, h) {
    var left = (screen.width / 2) - (w / 2);
    var top = (screen.height / 2) - (h / 2);
    var targetWin = window.open(pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
}
function challan_calculate()
{
    var qty = document.getElementsByName('qty[]');
    var rate = document.getElementsByName('rate[]');
    var samount = document.getElementsByName('total[]');
    //prodvalue
   //  alert(qty.length);
    for (var i = 0; i < qty.length; i++)
    {
        var res = qty[i].value * rate[i].value;
        samount[i].value = res.toFixed(2);
    }

}

//<!-- This script will open up the pop up window scrolling yes option and in full screen -->
function PopupCenterF(pageURL, title) {
//var left = (screen.width/2)-(w/2);
//var top = (screen.height/2)-(h/2);
    var targetWin = window.open(pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no');
}



//<!-- this script will control the text areas word limit -->
function textCounter(field, cntfield, maxlimit)
{
    if (field.value.length > maxlimit) // if too long...trim it!
        field.value = field.value.substring(0, maxlimit);
    // otherwise, update 'characters left' counter
    else
        cntfield.value = maxlimit - field.value.length;
}

//<!-- This function will make the string Capitalise only initial letter single word only -->
function ucfirst(string)
{
    return string.charAt(0).toUpperCase() + string.slice(1);
}

//<!-- This function will make the string Capitalise only initial letter of complete string passed to it -->
function ucwords(string)
{
    return string.replace(/(^|\s)([a-z])/g, function(m, p1, p2) {
        return p1 + p2.toUpperCase();
    });
}

//triming the white spaces in javascript
function trim(stringToTrim)
{
    return stringToTrim.replace(/^\s+|\s+$/g, "");
}

//triming the white spaces in javascript
function strtoupper(string)
{
    return string.toUpperCase();
}

//making the text boxes to accept only numeric values
function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;

    return true;
}

//making the text boxes to accept only numeric values or a floating values only
function isNumberKeyOrFloat(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;
    //searching for the occurence of the . in the text box
    if (typeof evt.target != 'undefined') // for firefox and other browsers
        var mtarget = evt.target;
    else if (evt.srcElement) // indicating it is Internet Explorer family
        var mtarget = evt.srcElement;
    var pos = mtarget.value.indexOf(".");
    if (pos != -1)
    {
        var found = true;
    }
    else
        var found = false;
    if (found)
    {
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
    }
    else
    {
        if (charCode == 46)
            return true;
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
    }
    return true;
}

function printing1(printarea)
{
    var DocumentContainer = document.getElementById(printarea);
    var WindowObject = window.open('', 'PrintWindow', 'width=850, height=700, top=50, left=50, toolbars=no, scrollbars=yes, status=no, resizable=yes');
    var textprint = '<center><h3><strong><style type="text/css">table{font-size:9px; font-family:Verdana, Geneva, sans-serif;} .options{display:none;}</style></strong></h3><br/></center>';
    textprint += DocumentContainer.innerHTML;
    //WindowObject.document.writeln('<html><head><link rel="stylesheet" type="text/css" href="css/print.css" /></head><body style="font-size:12px;">'+textprint+'</body></html>');
    WindowObject.document.writeln(textprint);
    WindowObject.document.close();
    WindowObject.focus();
    WindowObject.print();
    WindowObject.close();
}

//function printing(printarea)
//{
//    var DocumentContainer = document.getElementById(printarea);
//    var textprint = DocumentContainer.innerHTML;
//    //To allow the printing of the current view pane or all pages ends
//    if (document.getElementById('cpage')) {
//        var myprintpage = document.getElementById('cpage').value * 1;
//        DocumentContainer = document.getElementById('mypages' + myprintpage);
//        if (confirm('Print current page only')) {
//            textprint = DocumentContainer.innerHTML;
//        } else {
//            var printheader = $('#mypages1 div').eq(1);
//            var headtr = $('#mypages1 table.searchlist tr.search1tr:first');
//            var datatr = $("table.searchlist").find("> tbody > tr:not(.search1tr)");
//
//            textprint = printheader.html();
//            textprint += '<table width="100%">' + headtr.html();
//
//            datatr.each(function(index) {
//                textprint += $(this).clone().wrap('<table>').parent().html();
//            });
//            textprint += '</table>';
//        }
//    }
//    //To allow the printing of the current view pane or all pages ends
//
//    var WindowObject = window.open('', 'PrintWindow', 'width=850,height=700,top=50,left=50,toolbars=no,scrollbars=yes,status=no,resizable=yes');
//
//    WindowObject.document.writeln('<html><head><link  rel="stylesheet" type="text/css" href="./css/print.css" /></head><body style="font-size:12px;" id="myprintarea">' + textprint + '</body></html>');
//    WindowObject.document.close();
//    WindowObject.focus();
//    WindowObject.print();
//    WindowObject.close();
//}
function printing(divID){

//Get the HTML of div
var divElements = document.getElementById(divID).innerHTML;
//Get the HTML of whole page
var oldPage = document.body.innerHTML;


//Reset the page's HTML with div's HTML only
document.body.innerHTML =
"<html><head><title></title></head><body>" +
divElements + "</body></html>";

//Print Page
window.print();

//Restore orignal HTML
document.body.innerHTML = oldPage;
}
function pdf(printarea)
{
    var DocumentContainer = document.getElementById(printarea);
    var WindowObject = window.open('./printpdf.php', 'PrintWindow', 'width=100,height=20,top=50,left=50,toolbars=no,scrollbars=yes,status=no,resizable=yes');
    var textprint = DocumentContainer.innerHTML;

    WindowObject.document.writeln('<html><head><link rel="stylesheet" type="text/css" href="./css/print.css" /></head><body style="font-size:12px; text-align:center;" onblur="window.close();"><form name="testform" method="post" action="printpdf.php"> <textarea name="printcontent" style="display:none;">' + textprint + '</textarea><input name="thesubmit" type="submit" value="create pdf"></form></body></html>');
    //WindowObject.document.close();
    WindowObject.focus();
    // WindowObject.close();
}

function formenable(formname)
{
    var formToProcess = window.document.forms[formname];
    for (var i = 0; i < formToProcess.length; i++)
    {
        if (formToProcess.elements[i].type == 'text' || formToProcess.elements[i].type == 'submit' || formToProcess.elements[i].type == 'password' || formToProcess.elements[i].type == 'checkbox' || formToProcess.elements[i].type == 'radio' || formToProcess.elements[i].type == 'file' || formToProcess.elements[i].type == 'select-one' || formToProcess.elements[i].type == 'textarea')
        {
            formToProcess.elements[i].disabled = false;
        }
    }
}

function formdisable(formname)
{
    var formToProcess = window.document.forms[formname];
    for (var i = 0; i < formToProcess.length; i++)
    {
        if (formToProcess.elements[i].type == 'text' || formToProcess.elements[i].type == 'submit' || formToProcess.elements[i].type == 'password' || formToProcess.elements[i].type == 'checkbox' || formToProcess.elements[i].type == 'radio' || formToProcess.elements[i].type == 'file' || formToProcess.elements[i].type == 'select-one' || formToProcess.elements[i].type == 'textarea')
        {
            formToProcess.elements[i].disabled = true;
        }
    }
}

function clearspan(wclass)
{
    setTimeout(function() {
        $("span." + wclass).fadeOut("slow", function() {
            $("span." + wclass).remove();
        });
    }, 9000);
}

function initialoption(selectId, otext, ovalue)
{
    var option = new Option(otext, ovalue);
    var tselectId = document.getElementById(selectId);
    tselectId.options[0] = option;
}
//This function will set the Text of the selected pulldown in a hidden field
function setTextFromPullDown(pulId, fieldId)
{
    var OpullId = document.getElementById(pulId);
    if (OpullId.options[OpullId.selectedIndex].value != '')
        document.getElementById(fieldId).value = OpullId.options[OpullId.selectedIndex].text;
    else
        document.getElementById(fieldId).value = '';
}

function dropdownSelectedValue(drId)
{
    var dropDown = document.getElementById(drId);
    if (dropDown.options[dropDown.selectedIndex].value != '')
        return dropDown.options[dropDown.selectedIndex].value;
    else
        return false;
}

function dropdownSelectedText(drId)
{
    var dropDown = document.getElementById(drId);
    if (dropDown.options[dropDown.selectedIndex].value != '')
        return dropDown.options[dropDown.selectedIndex].text;
    else
        return false;
}

//This function will give focus to the field of our choice when the page loads
function setfocus(fid)
{
    if (document.getElementById(fid))
        document.getElementById(fid).focus();
}

//This function will clear all the option of a pull down
function eclearOption(id)
{
    document.getElementById(id).options.length = 0;
}

function checkemail(id)
{
    if (document.getElementById(id))
    {
        var email = document.getElementById(id);
        if (email.value != '')
        {
            var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            var output = filter.test(email.value);
            if (!output)
            {
                alert('Please enter a valid email address');
                email.value = '';
                email.focus();
            }
        }
    }
}

//This function will select all the checkboxes in one go
// here id will be the id of the checkbox on whose selection other checkbox will get selected and value will be like iname[]
function selectCheckBoxes(id, value) //selectCheckBoxes('mukesh', 'suraj[]')
{
    var chkbox = document.getElementsByName(value);
    for (var i = 0; i < chkbox.length; i++)
    {
        if (document.getElementById(id).checked)
            chkbox[i].checked = true;
        else
            chkbox[i].checked = false;
    }
}

//This function will help in the closing of the CSS POPUP
function cssPopClose(closeAction)
{
    switch (closeAction)
    {
        case'R':
            {
                parent.$.fn.colorbox.close();
                window.document.location.reload;
                break;
            }
        default:
            {
                parent.$.fn.colorbox.close();
            }
    }
}

function addmore_deep(tableId, event, func_after_add, wcase)
{
    var mtable = document.getElementById(tableId);
    var trows = mtable.rows.length;

    if (typeof event.target != 'undefined') // for firefox and other browsers
        var mtarget = event.target;
    else if (event.srcElement) // indicating it is Internet Explorer family
        var mtarget = event.srcElement;
    else
        return;

    //alert(mtarget.title);

    var totdatarow = $('#' + tableId + ' tr.tdata').size();

    if (mtarget.title == 'more' || mtarget.type == 'text')
    {
        var currentRow = mtarget.parentNode.parentNode;
        var newRow = mtable.insertRow(currentRow.rowIndex + 1); // insert new row
        newRow = $(newRow).addClass('tdata');
        newRow = newRow[0];

        newRow.innerHTML = currentRow.innerHTML;
        //alert(currentRow.innerHTML);
        $(newRow).find("input").each(function() {
            if ($(this).hasClass("hasDatepicker")) { // if the current input has the hasDatpicker class

                var this_id = $(this).attr("id"); // current inputs id
                var new_id = this_id + 1; // a new id
                $(this).attr("id", new_id); // change to new id

                $(this).removeClass('hasDatepicker'); // remove hasDatepicker class
                //re-init datepicker
                $(this).qdatepicker({
                    numberOfMonths: 1,
                    showButtonPanel: false,
                    changeYear: false,
                    yearRange: "-1:+1",
                    dateFormat: "dd/m/yy",
                    minDate: -0
                });
            }
        });

        newRow.cells[newRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmore_deep(\'' + tableId + '\', event, \'' + func_after_add + '\', \'' + wcase + '\');"/><img  title="less" src="images/less.png" onclick="javascript:addmore_deep(\'' + tableId + '\', event, \'' + func_after_add + '\', \'' + wcase + '\');"/>';
    }
    else if (mtarget.title == 'less')
    {
        var currentRow = mtarget.parentNode.parentNode;
        mtable.deleteRow(currentRow.rowIndex); // delete current row


    }
    setsno_deep(tableId);
    if (jQuery.isFunction(func_after_add))
        func_after_add(mtarget, currentRow, newRow, wcase);
}

function setsno_deep(tableId)
{

    $('#' + tableId + ' tr.tdata').each(function(i) {
        $(this).find('td.myintrow:first').html((i + 1) * 1);
    });
}

// This function is used to set dynamic calander on with new new as blank elements
function addmore22_2(tableId, event, wcase)
{
    if (wcase == null)
        wcase = '';
    var mtable = document.getElementById(tableId);
    var trows = mtable.rows.length;
    if (typeof event.target != 'undefined') // for firefox and other browsers
        var mtarget = event.target;
    else if (event.srcElement) // indicating it is Internet Explorer family
        var mtarget = event.srcElement;
    else
        return;

    if (mtarget.title == 'more')
    {   
        var currentRow = mtarget.parentNode.parentNode;
        var newRow = mtable.insertRow(currentRow.rowIndex + 1); // insert new row
         newRow.innerHTML = currentRow.innerHTML;
        /*alert(currentRow.rowIndex + 1);*/
        $(newRow).find('.nkget_dealer_rate').val('');
        $(newRow).find('.nkget_dealer_mrp_rate').val('');
        $(newRow).find('.nkset_dealer_rate').val('');

        $(newRow).find('.nkset_qty').val('');
        $(newRow).find('.nkset_batch_no').val('');
       
    /*newRow.children[1].children[0].setAttribute('value','');    
    newRow.children[2].children[0].setAttribute('value','');
    newRow.children[3].children[0].setAttribute('value','');
    newRow.children[4].children[0].setAttribute('value','');
    newRow.children[5].children[0].setAttribute('value','');*/

        //alert(newRow.cells[newRow.cells.length - 1].innerHTML);

        $(newRow).find("input").each(function() {
            if ($(this).hasClass("hasDatepicker")) { // if the current input has the hasDatpicker class

                var this_id = $(this).attr("id"); // current inputs id
                var new_id = this_id + 1; // a new id
                $(this).attr("id", new_id); // change to new id

                $(this).removeClass('hasDatepicker'); // remove hasDatepicker class
                // re-init datepicker
                $(this).datepicker({
                    numberOfMonths: 1,
                    showButtonPanel: false,
                    changeYear: true,
                    yearRange: "-30:+1",
                    dateFormat: "dd/m/yy"
                });
            }
        });

        if (wcase == 'distributor')
        {
            newRow.cells[1].innerHTML = '<img src="images/loader.gif">Fetching Items..';
            //alert(newRow.cells[1]);
            get_select(newRow.cells[1], 'distributor');
            //newRow.cells[2].innerHTML = get_select();
            //setTimeout(function(){ comboo();},500);
            // comboo();
        }
        if (wcase == 'combo')
        {

            newRow.cells[1].innerHTML = '<img src="images/loader.gif">Fetching Items..';
            //alert(newRow.cells[1]);
            get_select(newRow.cells[1], 'combo');
            //newRow.cells[2].innerHTML = get_select();
            //setTimeout(function(){ comboo();},500);
            // comboo();
        }
        /*if(wcase == 'brand')
         {
         $(function() {
         $("#mytable1").on("mouseover", 'input[class="brandname"]', function(event){
         $(".brandname").autocomplete({
         source: "index.php?option=myajax&suboption=drug-search&searchdomain=brandname"
         });
         });
         });
         }*/
        newRow.cells[newRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmore22_2(\'' + tableId + '\',event,\'' + wcase + '\');"/><img  title="less" src="images/less.png" onclick="javascript:addmore22_2(\'' + tableId + '\', event);"/>';
        //to replace the plus sign with - also for the first tr also
        if (currentRow.rowIndex == 1)
        {
            currentRow.cells[currentRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmore22_2(\'' + tableId + '\', event,\'' + wcase + '\');"/><img  title="less" src="images/less.png" onclick="javascript:addmore22_2(\'' + tableId + '\', event);"/>';
        }
    }
    else if (mtarget.title == 'less')
    {
        var currentRow = mtarget.parentNode.parentNode;
        if (trows == 3 && currentRow.rowIndex == 1) // checking if this is the last row to be edited, then only + sign should stay.
        {
            currentRow.cells[currentRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmore22_2(\'' + tableId + '\', event);"/>';
        }
        else
        {
            mtable.deleteRow(currentRow.rowIndex); // delete current row
            // call the desired calculation function
            //getvaluetotal();
        }
    }
    setsno1(tableId);
}
// This function is used to set dynamic calander on
function addmore22(tableId, event, wcase)
{
	if (wcase == null)
        wcase = '';
    var mtable = document.getElementById(tableId);
    var trows = mtable.rows.length;
    if (typeof event.target != 'undefined') // for firefox and other browsers
        var mtarget = event.target;
    else if (event.srcElement) // indicating it is Internet Explorer family
        var mtarget = event.srcElement;
    else
        return;

    if (mtarget.title == 'more')
    {
        var currentRow = mtarget.parentNode.parentNode;
        var newRow = mtable.insertRow(currentRow.rowIndex + 1); // insert new row
        newRow.innerHTML = currentRow.innerHTML;

        //alert(newRow.cells[newRow.cells.length - 1].innerHTML);
		//newRow[0].classList.add("tdata");
        $(newRow).find("input").each(function() {
            if ($(this).hasClass("hasDatepicker")) { // if the current input has the hasDatpicker class

                var this_id = $(this).attr("id"); // current inputs id
                var new_id = this_id + 1; // a new id
                $(this).attr("id", new_id); // change to new id

                $(this).removeClass('hasDatepicker'); // remove hasDatepicker class
                // re-init datepicker
                $(this).datepicker({
                    numberOfMonths: 1,
                    showButtonPanel: false,
                    changeYear: true,
                    yearRange: "-30:+1",
                    dateFormat: "dd/m/yy"
                });
            }
        });

        if (wcase == 'distributor')
        {
            newRow.cells[1].innerHTML = '<img src="images/loader.gif">Fetching Items..';
            //alert(newRow.cells[1]);
            get_select(newRow.cells[1], 'distributor');
            //newRow.cells[2].innerHTML = get_select();
            //setTimeout(function(){ comboo();},500);
            // comboo();
        }
        if (wcase == 'combo')
        {

            newRow.cells[1].innerHTML = '<img src="images/loader.gif">Fetching Items..';
            //alert(newRow.cells[1]);
            get_select(newRow.cells[1], 'combo');
            //newRow.cells[2].innerHTML = get_select();
            //setTimeout(function(){ comboo();},500);
            // comboo();
        }
        /*if(wcase == 'brand')
         {
         $(function() {
         $("#mytable1").on("mouseover", 'input[class="brandname"]', function(event){
         $(".brandname").autocomplete({
         source: "index.php?option=myajax&suboption=drug-search&searchdomain=brandname"
         });
         });
         });
         }*/
        newRow.cells[newRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmore22(\'' + tableId + '\',event,\'' + wcase + '\');"/><img  title="less" src="images/less.png" onclick="javascript:addmore22(\'' + tableId + '\', event);"/>';
        //to replace the plus sign with - also for the first tr also
        if (currentRow.rowIndex == 1)
        {
            currentRow.cells[currentRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmore22(\'' + tableId + '\', event,\'' + wcase + '\');"/><img  title="less" src="images/less.png" onclick="javascript:addmore22(\'' + tableId + '\', event);"/>';
        }
    }
    else if (mtarget.title == 'less')
    {
        var currentRow = mtarget.parentNode.parentNode;
        if (trows == 3 && currentRow.rowIndex == 1) // checking if this is the last row to be edited, then only + sign should stay.
        {
            currentRow.cells[currentRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmore22(\'' + tableId + '\', event);"/>';
        }
        else
        {
            mtable.deleteRow(currentRow.rowIndex); // delete current row
            // call the desired calculation function
            //getvaluetotal();
        }
    }
    setsno1(tableId);
}
// to set the numbers in the sno column and it is optional
function setsno1(tableId)
{
    var mtable = document.getElementById(tableId);
    for (var i = 1; i < mtable.rows.length - 1; i++)
    {
        mtable.rows[i].cells[0].innerHTML = i;
    }
}
function addmore(tableId, event, func_after_add)
{
    var mtable = document.getElementById(tableId);
    var trows = mtable.rows.length;
    if (typeof event.target != 'undefined') // for firefox and other browsers
        var mtarget = event.target;
    else if (event.srcElement) // indicating it is Internet Explorer family
        var mtarget = event.srcElement;
    else
        return;

    if (mtarget.title == 'more')
    {
        var currentRow = mtarget.parentNode.parentNode;
        var newRow = mtable.insertRow(currentRow.rowIndex + 1); // insert new row
        newRow.innerHTML = currentRow.innerHTML;
        //alert(newRow.cells[newRow.cells.length - 1].innerHTML);
        newRow.cells[newRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmore(\'' + tableId + '\', event);"/><img  title="less" src="images/less.png" onclick="javascript:addmore(\'' + tableId + '\', event);"/>';
        //to replace the plus sign with - also for the first tr also
        if (currentRow.rowIndex == 1)
        {
            currentRow.cells[currentRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmore(\'' + tableId + '\', event);"/><img  title="less" src="images/less.png" onclick="javascript:addmore(\'' + tableId + '\', event);"/>';
        }
    }
    else if (mtarget.title == 'less')
    {
        var currentRow = mtarget.parentNode.parentNode;
        if (trows == 3 && currentRow.rowIndex == 1) // checking if this is the last row to be edited, then only + sign should stay.
        {
            currentRow.cells[currentRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmore(\'' + tableId + '\', event);"/>';
        }
        else
        {
            mtable.deleteRow(currentRow.rowIndex); // delete current row
            //call the desired calculation function
            //getvaluetotal();
        }
    }
    setsno(tableId);
    if (jQuery.isFunction(func_after_add))
        func_after_add(mtarget, currentRow, newRow);
}

//This will not give the addmore sign
function addmore_2(tableId, event, func_after_add)
{
    var mtable = document.getElementById(tableId);
    var trows = mtable.rows.length;
    if (typeof event.target != 'undefined') // for firefox and other browsers
        var mtarget = event.target;
    else if (event.srcElement) // indicating it is Internet Explorer family
        var mtarget = event.srcElement;
    else
        return;

    if (mtarget.title == 'more')
    {
        var currentRow = mtarget.parentNode.parentNode;
        var newRow = mtable.insertRow(currentRow.rowIndex + 1); // insert new row
        newRow.innerHTML = currentRow.innerHTML;
        //alert(newRow.cells[newRow.cells.length - 1].innerHTML);
        newRow.cells[newRow.cells.length - 1].innerHTML = '<img  title="less" src="images/less.png" onclick="javascript:addmore(\'' + tableId + '\', event);"/>';
        //to replace the plus sign with - also for the first tr also
        if (currentRow.rowIndex == 1)
        {
            currentRow.cells[currentRow.cells.length - 1].innerHTML = '<img  title="less" src="images/less.png" onclick="javascript:addmore(\'' + tableId + '\', event);"/>';
        }
    }
    else if (mtarget.title == 'less')
    {
        var currentRow = mtarget.parentNode.parentNode;
        if (trows == 3 && currentRow.rowIndex == 1) // checking if this is the last row to be edited, then only + sign should stay.
        {
            currentRow.cells[currentRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmore(\'' + tableId + '\', event);"/>';
        }
        else
        {
            mtable.deleteRow(currentRow.rowIndex); // delete current row

        }

    }
    setsno(tableId);
    if (jQuery.isFunction(func_after_add))
        func_after_add(mtarget, currentRow, newRow);
}

//function for dynamically deleting rows (but with without implimentation of ajax )
function deleteRow(tableID, func_after_del)
{
    try
    {
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        for (var i = 0; i < rowCount; i++)
        {
            var row = table.rows[i];
            var chkbox = row.cells[0].childNodes[0];
            if (null != chkbox && true == chkbox.checked)
            {
                if (rowCount <= 1)
                {
                    alert("Cannot delete all the rows.");
                    break;
                }
                table.deleteRow(i);
                rowCount--;
                i--;
            }
        }
    }
    catch (e)
    {
        alert(e);
    }
    if ($.isFunction(func_after_del))
        func_after_del();
}
//code ends here for dymamic add and delete of rows

// to set the numbers in the sno column and it is optional
function setsno(tableId)
{
    var mtable = document.getElementById(tableId);
    //alert(mtable.rows.length);
    for (var i = 1; i < mtable.rows.length; i++)
    {
        mtable.rows[i].cells[0].innerHTML = i;
    }
}
function calculate()
{
    var srate = document.getElementsByName('rate[]');
    var qty = document.getElementsByName('qty[]');
    var samount = document.getElementsByName('total[]');
    var gtotal = 0;
    for (var i = 0; i < srate.length; i++)
    {

        if (srate[i].value == '')
            var rate = 0;
        else
            var rate = srate[i].value;
        if (qty[i].value == '')
            var quantity = 0;
        else
            var quantity = qty[i].value;
        var res = quantity * rate;
        samount[i].value = res.toFixed(2);
        gtotal += samount[i].value * 1;// multiply by so that total[i].value is treated as numeric
    }
    document.getElementById('totalamount').value = gtotal.toFixed(2);
    var totalamount = document.getElementById('totalamount').value;
    var basicexduty = totalamount * 10 / 100;
    document.getElementById('taxamount1').value = basicexduty.toFixed(2);
    var csessonbed = basicexduty * 2 / 100;
    document.getElementById('taxamount2').value = csessonbed.toFixed(2);

    var Hrsescess = csessonbed * 1 / 100;
    document.getElementById('taxamount3').value = Hrsescess.toFixed(2);
    var taxable_tot = totalamount * 1 + basicexduty * 1 + csessonbed * 1 + Hrsescess * 1;
    document.getElementById('taxabletotal').value = taxable_tot.toFixed(2);
    var sale_tax = taxable_tot * 4 / 100;
    document.getElementById('taxamount4').value = sale_tax.toFixed(2);
    var Surcharge = sale_tax * 5 / 100;
    document.getElementById('taxamount5').value = sale_tax.toFixed(2);
    var misc_charge = document.getElementById('taxamount6').value;
    var grandtotal = taxable_tot * 1 + sale_tax * 1 + Surcharge * 1 + misc_charge * 1;
    document.getElementById('grandtotal').value = grandtotal.toFixed(2);

}
function product_calculate()
{   
    var surcharge = document.getElementsByName('surcharge[]');
    var state = document.getElementsByName('state[]');
    //var surcharge = document.getElementsByName('surcharge[]');
    var qty = document.getElementsByName('quantity[]');
    //var scheme = document.getElementsByName('scheme[]');
    var rate = document.getElementsByName('rate[]');
    var amount = document.getElementsByName('amount[]');
    var cd = document.getElementsByName('cd[]');
    var cd_type = document.getElementsByName('cd_type[]');
    var cd_amt = document.getElementsByName('cd_amt[]');
    var vat = document.getElementsByName('vat[]');
    var va = document.getElementsByName('vat_amt[]');
    var tds_amt = document.getElementsByName('trade_disc_amt[]');
    var taxable_amt = document.getElementsByName('taxable_amt[]');
    var ttl_amt = document.getElementsByName('ttl_amt[]');
    var total = document.getElementsByName('total');
    //prodvalue
     
    // alert(surcharge[0].value);
    trade_disc_calculate();
    // alert(qty.length);
    for (var i = 0; i < qty.length; i++)
    {
        /*1 = (%) percentage*/
        if(cd_type[i].value==1){
            var cd_dis = (cd[i].value * ttl_amt[i].value) / 100;
            cd_amt[i].value = cd_dis.toFixed(2);
            var taxablamt = ttl_amt[i].value - cd_dis;
            taxable_amt[i].value = taxablamt.toFixed(2);
            va[i].value = taxable_amt[i].value * vat[i].value;
        }

        /*2 = amount*/
        if(cd_type[i].value==2){
            cd_dis = cd[i].value;
            cd_amt[i].value = cd_dis; 

            var taxablamt = ttl_amt[i].value - cd_dis;
            taxable_amt[i].value = taxablamt.toFixed(3);
            va[i].value = taxable_amt[i].value * vat[i].value;     
        }
        
       //if(state[i].value==28){
           // vat[i].value = 0.0;   
        //}else{
          //  vat[i].value=vat[i].value;
       // }
        var v_amt
        if(vat[i].value==0){
           v_amt = 0;
        }
        else
        {
            v_amt= taxable_amt[i].value*(vat[i].value/100);
        }

        va[i].value = v_amt.toFixed(3);
        var surcharge_amt = v_amt*(surcharge[i].value)/100;
        //v_amt = v_amt+(v_amt*(surcharge[i].value)/100);
        //  alert(v_amt);
        var taxableamt = taxable_amt[i].value*1;
        var gamt = v_amt + surcharge_amt + taxableamt;

        if(gamt!=0){
            amount[i].value = gamt.toFixed(3);
        }else{
            amount[i].value = 0;
        }




    
//			var qty_val = qty[i].value;
//			if(qty_val.length>=1){
//			var d = qty_val.length-1;
//				if(qty_val>=10){
//					var sch_val = qty_val.substring(0, d);
//					scheme[i].value = sch_val;
//				}else{
//					//console.log(qty_val);
//					scheme[i].value=0;
//				}
//			}
		
    }

    var discount_val = document.getElementById('dis').value;
    getTotal(discount_val);
    
}
  

function damage_product_calculate()
{   
    //alert('murari');
    var surcharge = document.getElementsByName('surcharge[]');
    var state = document.getElementsByName('state[]');
    var surcharge = document.getElementsByName('surcharge[]');
    var qty = document.getElementsByName('quantity[]');
    var scheme = document.getElementsByName('scheme[]');
    var rate = document.getElementsByName('rate[]');
    var amount = document.getElementsByName('amount[]');
    var cd = document.getElementsByName('cd[]');
    var cd_type = document.getElementsByName('cd_type[]');
    var cd_amt = document.getElementsByName('cd_amt[]');
    var vat = document.getElementsByName('vat[]');
    var va = document.getElementsByName('vat_amt[]');
    var tds_amt = document.getElementsByName('trade_disc_amt[]');
    var taxable_amt = document.getElementsByName('taxable_amt[]');
    var ttl_amt = document.getElementsByName('ttl_amt[]');
    var actual_amount = document.getElementsByName('actual_amount[]');
    //prodvalue
    // alert(qty.length);
    //alert(surcharge[0].value);
   // trade_disc_calculate();
    
     for (var i = 0; i < qty.length; i++)
    {
      // alert(tds_amt);
        //alert(i);
        var res = (qty[i].value * rate[i].value);
        amount[i].value = res;
        var resper = (res*35)/100;
        var aa = res-resper;
        actual_amount[i].value = aa.toFixed(2);
    }
}

function damage_product_calculate_replace()
{   
    //alert('murari');
    var surcharge = document.getElementsByName('surcharge[]');
    var state = document.getElementsByName('state[]');
    var surcharge = document.getElementsByName('surcharge[]');
    var qty = document.getElementsByName('replace_quantity[]');
    var scheme = document.getElementsByName('scheme[]');
    var rate = document.getElementsByName('replace_rate[]');
    var amount = document.getElementsByName('replace_amount[]');
    var cd = document.getElementsByName('replace_cd[]');
    var cd_type = document.getElementsByName('replace_cd_type[]');
    var cd_amt = document.getElementsByName('cd_amt[]');
    var vat = document.getElementsByName('vat[]');
    var va = document.getElementsByName('vat_amt[]');
    var tds_amt = document.getElementsByName('trade_disc_amt[]');
    var taxable_amt = document.getElementsByName('taxable_amt[]');
    var ttl_amt = document.getElementsByName('ttl_amt[]');
    //prodvalue
    // alert(qty.length);
    //alert(surcharge[0].value);
   // trade_disc_calculate();
    
     for (var i = 0; i < qty.length; i++)
    {
      // alert(tds_amt);
        //alert(i);
        var res = (qty[i].value * rate[i].value);
        amount[i].value = res.toFixed(2);

    }
}

function product_calculate_new()
{
var cd_type = document.getElementsByName('cd_type[]');
var rate = document.getElementsByName('rate[]');
var tds_type = document.getElementsByName('cd[]');
var tds_val = document.getElementsByName('cd_amt[]');


//prodvalue
//alert(cd.length);
//alert(surcharge[0].value);
// trade_disc_calculate();
// alert(tds_type.length);
for(var i = 0; i<tds_type.length; i++)
{
// alert(cd_type[i].value);
if(cd_type[i].value == '' || cd_type[i].value == null){

tds_val[i].value = '';
}else if(cd_type[i].value == 1){

var res = (rate[i].value) * (tds_type[i].value/100);
tds_val[i].value = res.toFixed(2);
}else{
var res = tds_type[i].value;
tds_val[i].value = res;
}
}
}
function product_calculate_new_temp()
{   
    var cd_type = document.getElementsByName('cd_type[]');     
    var rate = document.getElementsByName('rate[]');
    var tds_type = document.getElementsByName('cd[]');
    var tds_val = document.getElementsByName('cd_amt[]');
  
    
    //prodvalue
   //alert(cd.length);
    //alert(surcharge[0].value);
   // trade_disc_calculate();
   //  alert(tds_type.length);
    for(var i = 0; i<tds_type.length; i++)
	{           
        
            if(cd_type[i].value == 1){
               // alert("manisha");
               //  alert(rate[i].value);
               //   alert(tds_type[i].value);
                
                var res = (rate[i].value) * (tds_type[i].value/100);
               // alert(res);
		tds_val[i].value = res.toFixed(2);
               // tds_val[i].value = ( r[i].value - tds_val[i].value ) * qty[i].value;
            }else{
              var res = tds_type[i].value;
		  tds_val[i].value = res;
              //  tds_val[i].value = ( r[i].value - tds_val[i].value ) * qty[i].value;
            }
      	}
}

function product_calculate_extra()
{
    var qty = document.getElementsByName('quantity_ex[]');
    var rate = document.getElementsByName('rate_ex[]');
    var amt = document.getElementsByName('amount_ex[]');
     var scheme = document.getElementsByName('scheme_ex[]');
    var cd = document.getElementsByName('cd_ex[]');
    var cd_type = document.getElementsByName('cd_type_ex[]');
    var cd_amt = document.getElementsByName('cd_amt_ex[]');
    var vat = document.getElementsByName('vat_ex[]');
    var va = document.getElementsByName('vat_amt_ex[]');
    //prodvalue
    // alert(qty.length);
    for (var i = 0; i < qty.length; i++)
    {
        var res = qty[i].value * rate[i].value;
         if(cd_type[i].value==1){
        var cd_dis = (cd[i].value * res) / 100;
        cd_amt[i].value = cd_dis.toFixed(2);
    }
     if(cd_type[i].value==2){         
        var cd_dis = cd[i].value;
        cd_amt[i].value = cd_dis; 
    }
       
        act_val = res - cd_dis;
        var v_amt = (vat[i].value * act_val) / 100;
        va[i].value = v_amt.toFixed(2);
        gamt = act_val ;
        amt[i].value = gamt.toFixed(2);
         qty_val = qty[i].value;   
        if(qty_val.length>1){
            var d = qty_val.length-1;
        sch_val = qty_val.substring(0, d);
      //  sch_val = qty_val.toString()['0'];
        scheme[i].value = sch_val;
    }
    }

}



///**********************For Edit******************************
function product_calculate_edit(id)
{   
    var surcharge = document.getElementById('surcharge'+id);
    var state = document.getElementById('state'+id);
    var surcharge = document.getElementById('surcharge'+id);
    var qty = document.getElementById('quantity'+id).value;
  
    var scheme = document.getElementById('scheme'+id);
    var rate = document.getElementById('rate'+id).value;
    var cd = document.getElementById('cd'+id);
    var cd_type = document.getElementById('cd_type'+id);
    var cd_amt = document.getElementById('cd_amt'+id);
    var vat = document.getElementById('vat'+id);
    var va = document.getElementById('vat_amt'+id);
    var tds_amt = document.getElementById('trade_disc_amt'+id).value;
    var taxable_amt = document.getElementById('taxable_amt'+id);
    var ttl_amt = document.getElementById('ttl_amt'+id);
    //prodvalue

  

    
   
        var res = (qty * (rate-tds_amt) );
        var amount = res;
        alert(amount);
        if(cd_type[i].value==1){
            var cd_dis = (cd[i].value * res) / 100;
            cd_amt[i].value = cd_dis.toFixed(2);
            var taxablamt = ttl_amt[i].value - cd_dis;
            taxable_amt[i].value = taxablamt.toFixed(2);
            va[i].value = taxable_amt[i].value * vat[i].value/100;
        }
        if(cd_type[i].value==2){
            cd_dis = cd[i].value;
            cd_amt[i].value = cd_dis;      
        }
        
       if(state[i].value==28){
            vat[i].value = 0.0;   
        }else{
            vat[i].value=vat[i].value;
        }
        var v_amt
        if(vat[i].value==0){
           v_amt = 0;
        }
        else
        {
            v_amt= taxable_amt[i].value*(vat[i].value/100);
        }
        va[i].value = v_amt.toFixed(2);
        var surcharge_amt = v_amt*(surcharge[i].value)/100;
        var taxableamt = taxable_amt[i].value*1;

        var gamt = v_amt + surcharge_amt + taxableamt;
        if(gamt!=0){
            amount[i].value = gamt.toFixed(2);
            alert(amount[i].value);
        }
    
        var qty_val = qty[i].value;
        if(qty_val.length>1){
        var d = qty_val.length-1;
        var sch_val = qty_val.substring(0, d);
        scheme[i].value = sch_val;
        }
    
  
}


function challan_calculate()
{

    var qty = document.getElementsByName('qty[]');
    var rate = document.getElementsByName('rate[]');
    var samount = document.getElementsByName('total[]');
    //prodvalue
    // alert(qty.length);
    for (var i = 0; i < qty.length; i++)
    {
        var res = qty[i].value * rate[i].value;
        samount[i].value = res.toFixed(2);
    }

}
function getdynamicdata(wcase, fieldid)
{
    switch (wcase)
    {
        case 'get_batch_rate':
            {
                var prodvalue = document.getElementById('p' + fieldid).value; // this is product value
                var product_details = document.getElementById(fieldid).value; //product details id
                var updatetextid = 'r' + fieldid + '<$>ostock' + fieldid + '<$>batch_no' + fieldid;
                //alert(updatetextid);
                var pulldata = prodvalue + '<$>' + product_details;
                getdata(pulldata, 'progdiv', 'get_batch_rate', updatetextid);
                break;
            }
    }
}
function getajaxdata(wcase, tableId, event,data)
{   
  // alert("manisha");getajaxdata(\'get_mrp_product\',\'mytable\',event)
    var mtable = document.getElementById(tableId);
    var trows = mtable.rows.length;
    if (typeof event.target != 'undefined') // for firefox and other browsers
        var mtarget = event.target;
    else if (event.srcElement) // indicating it is Internet Explorer family
        var mtarget = event.srcElement;
    else
        return;
    var currentRow = mtarget.parentNode.parentNode;
    switch (wcase)
    {
        case 'rate':
            {
                var ratevalue = document.getElementsByName('rate[]');
                var updatetextid = new Array(ratevalue[currentRow.rowIndex - 1]);
                getdataObject(mtarget.value, 'progdiv', 'get_rate', updatetextid);
                break;
            }
            case 'get_product_gst':
            {
                var ratevalue = document.getElementsByName('vat[]');
                var updatetextid = new Array(ratevalue[currentRow.rowIndex - 1]);
                getdataObject(mtarget.value, 'progdiv', 'get_product_gst', updatetextid);
                break;
            }
            
            case 'get_retailer_rate':
            {
                var replace_rate = document.getElementsByName('replace_rate[]');
                var updatetextid = new Array(replace_rate[currentRow.rowIndex - 1]);
                getdataObject(mtarget.value, 'progdiv', 'get_retailer_rate', updatetextid);
               // alert(pulldata);
                break;
            }
//             case 'get_product_hsn':
//            {
//                var hsn_code = document.getElementsByName('hsn_code[]');
//                var updatetextid = new Array(hsn_code[currentRow.rowIndex - 1]);
//                getdataObject(mtarget.value, 'progdiv', 'get_product_hsn', updatetextid);
//               // alert(pulldata);
//                break;
//            }
        case 'lineno':
            {
                var lineno = document.getElementsByName('lineno[]');
                var rate = document.getElementsByName('rate[]');
                var wpoId = document.getElementById('wpoId').value;
                var updatetextid = new Array(lineno[currentRow.rowIndex - 1], rate[currentRow.rowIndex - 1]);
                var pulldata = mtarget.value + '<$>' + wpoId;
                getdataObject(pulldata, 'progdiv', 'get_lineno', updatetextid);
                break;
            }

            case 'get-retailer-rate':
            {
                var rate = document.getElementsByName('rate[]');
                var updatetextid = new Array(rate[currentRow.rowIndex - 1]);
                getdataObject(data, 'progdiv', 'get_retailer_rate', updatetextid);                
                break;
            }
                case 'get-retailer-rate-edit':
            {
                var rate = document.getElementsByName('rate[]');
                var aval_stock = document.getElementsByName('aval_stock[]');

                var updatetextid = new Array(rate[currentRow.rowIndex - 1],aval_stock[currentRow.rowIndex - 1]);
                getdataObject(data+'|'+data, 'progdiv', 'get_retailer_rate_edit', updatetextid);
                // console.log(data+'|'+data);
                break;
            }

        case 'get_product_details':
            {
                //var quantity = document.getElementsByName('quantity[]');
                var base_price = document.getElementsByName('base_price[]');
                var updatetextid = new Array(base_price[currentRow.rowIndex - 1]);
                //alert(updatetextid);
                var dealerId = document.getElementById('dealer_id').value;
                var pulldata = mtarget.value + '<$>' + dealerId;
                //alert(pulldata);
                getdataObject(pulldata, 'progdiv', 'get_product_details', updatetextid);
                break;
            }
            case 'get-calculate-rate':
            {
                //var quantity = document.getElementsByName('quantity[]');
                var base_price = document.getElementsByName('base_price[]');
                var updatetextid = new Array(base_price[currentRow.rowIndex - 1]);
                //alert(updatetextid);
                var dealerId = document.getElementById('product_id').value;
                var pulldata = mtarget.value + '<$>' + dealerId;
                //alert(pulldata);
                getdataObject(pulldata, 'progdiv', 'get_calculate_rate', updatetextid);
                break;
            }
            case 'get-calculate-rate-extra':
            {
                //var quantity = document.getElementsByName('quantity[]');
                var base_price = document.getElementsByName('rate_ex[]');
                var updatetextid = new Array(base_price[currentRow.rowIndex - 1]);
                //alert(updatetextid);
                var dealerId = document.getElementById('product_id_ex').value;
                var pulldata = mtarget.value + '<$>' + dealerId;
                //alert(pulldata);
                getdataObject(pulldata, 'progdiv', 'get_calculate_rate', updatetextid);
                break;
            }
             case 'get_mrp_product':
            {
                //var quantity = document.getElementsByName('quantity[]');
                var base_price = document.getElementsByName('base_price[]');
                var updatetextid = new Array(base_price[currentRow.rowIndex - 1]);
                //alert(updatetextid);
                var dealerId = document.getElementById('product_id').value;
                var pulldata = mtarget.value + '<$>' + dealerId;
                //alert(pulldata);
                getdataObject(pulldata, 'progdiv', 'get-mrp-product', updatetextid);
                break;
            }
             case 'vat':
            { 
                var state = document.getElementsByName('state[]');
                var base_price = document.getElementsByName('vat[]');
                var updatetextid = new Array(base_price[currentRow.rowIndex - 1]);
                //alert(updatetextid);
                var dealerId = document.getElementById('dealer_id').value;
                var pulldata = mtarget.value + '<$>' + dealerId;
                //alert(pulldata);
                getdataObject(pulldata, 'progdiv', 'vat', updatetextid);
                break;
            }
            case 'get_product_mrp':
            {
                var avbl_quantity = document.getElementsByName('avlb_quantity[]');
                var base_price = document.getElementsByName('mrp[]');
                var retailer_rate = document.getElementsByName('rate[]');

                var updatetextid = new Array(base_price[currentRow.rowIndex - 1],
                    avbl_quantity[currentRow.rowIndex - 1],
                    retailer_rate[currentRow.rowIndex - 1]);
                //alert(updatetextid);
                var dealerId = document.getElementById('dealer_id').value;                
                var pulldata = mtarget.value + '<$>' + dealerId;
                //alert(pulldata);
                
                getdataObject(pulldata, 'progdiv', 'get_product_mrp', updatetextid);
                break;
            }
            case 'get_product_mrp_refund':
            {
                var base_price = document.getElementsByName('mrp[]');
                var retailer_rate = document.getElementsByName('rate[]');

                var updatetextid = new Array(base_price[currentRow.rowIndex - 1],
                    retailer_rate[currentRow.rowIndex - 1]);
                //alert(updatetextid);
                var dealerId = document.getElementById('dealer_id').value;                
                var pulldata = mtarget.value + '<$>' + dealerId;
                //alert(pulldata);
                
                getdataObject(pulldata, 'progdiv', 'get_product_mrp_refund', updatetextid);
                break;
            }
            case 'get_product_mrp_replace':
            {
                //var quantity = document.getElementsByName('quantity[]');
                var base_price = document.getElementsByName('replace_mrp[]');
                var updatetextid = new Array(base_price[currentRow.rowIndex - 1]);
               // alert(updatetextid);
                var dealerId = document.getElementById('dealer_id').value;                
                var pulldata = mtarget.value + '<$>' + dealerId;
                //alert(pulldata);
                
                getdataObject(pulldata, 'progdiv', 'get_product_mrp_replace', updatetextid);
                break;
            }
        case 'get_product_vat':
            { 
                var state = document.getElementsByName('state[]');
                var base_price = document.getElementsByName('vat[]');
                var updatetextid = new Array(base_price[currentRow.rowIndex - 1]);
                //alert(updatetextid);
                var dealerId = document.getElementById('dealer_id').value;
                var pulldata = mtarget.value + '<$>' + dealerId;
                //alert(pulldata);
                getdataObject(pulldata, 'progdiv', 'get_product_vat', updatetextid);
                break;
            }
            
            case 'get_comunity_code':
            { 
                var state = document.getElementsByName('state[]');
                var comunity_code = document.getElementsByName('comunity_code[]');
                var updatetextid = new Array(comunity_code[currentRow.rowIndex - 1]);
                //alert('comunity_code');
                var dealerId = document.getElementById('dealer_id').value;
                var pulldata = mtarget.value + '<$>' + dealerId;
                //alert(pulldata);
                getdataObject(pulldata, 'progdiv', 'get_comunity_code', updatetextid);
                break;
            }
        case 'get_mrp_vat':
            {
                //var quantity = document.getElementsByName('quantity[]');
                var ord_qty = document.getElementsByName('mrp_ex[]');
                var uid = document.getElementsByName('vat_ex[]');
                var updatetextid = new Array(ord_qty[currentRow.rowIndex - 1], uid[currentRow.rowIndex - 1]);
                //var dealerId = document.getElementById('dealer_id').value;
                var pulldata = mtarget.value;
                getdataObject(pulldata, 'progdiv', 'get_mrp_vat', updatetextid);
                break;
            }

        case 'get_product_qty':get_mrp_vat
            {
                //var quantity = document.getElementsByName('quantity[]');
                var ord_qty = document.getElementsByName('qty[]');
                var uid = document.getElementsByName('user_id[]');
                var updatetextid = new Array(ord_qty[currentRow.rowIndex - 1], uid[currentRow.rowIndex - 1]);
                //var dealerId = document.getElementById('dealer_id').value;
                var pulldata = mtarget.value;
                //alert(updatetextid);
                getdataObject(pulldata, 'progdiv', 'get_product_qty', updatetextid);
                break;
            }
        case 'get_product_tax':
            {
                //var quantity = document.getElementsByName('quantity[]');
                var ord_qty = document.getElementsByName('taxId[]');
                var uid = document.getElementsByName('user_id[]');
                var updatetextid = new Array(ord_qty[currentRow.rowIndex - 1], uid[currentRow.rowIndex - 1]);
                //var dealerId = document.getElementById('dealer_id').value;
                var pulldata = mtarget.value;
                //alert(updatetextid);
                getdataObject(pulldata, 'progdiv', 'get_product_tax', updatetextid);
                break;
            }
        case 'get_total_sale_value':
            {

                //var quantity = document.getElementsByName('quantity[]');
                var challan_value = document.getElementsByName('total_challan_value[]');
                var updatetextid = new Array(challan_value[currentRow.rowIndex - 1]);
                var pulldata = mtarget.value;
                //alert(pulldata);
                getdataObject(pulldata, 'progdiv', 'get_total_sale_value', updatetextid);
                break;
            }

        case 'get_product_test':
            {

                //var quantity = document.getElementsByName('quantity[]');
                var batch_no = document.getElementsByName('batch_no[]');
                var updatetextid = new Array(batch_no[currentRow.rowIndex - 1]);
                var pulldata = mtarget.value;
                fetch_location(pulldata, 'progdiv', updatetextid, 'get_batch_no');
                //alert(pulldata);
                //getdataObject(pulldata, 'progdiv', 'get_product_details', updatetextid);
                break;
            }

        case 'get-product-rate':
            {

                //var quantity = document.getElementsByName('quantity[]');

                var product = document.getElementsByName('product_id[]');
                var base_price = document.getElementsByName('base_price[]');
              //  alert(base_price);
                var batch_no = document.getElementsByName('batch_no[]');
                var updatetextid = new Array(base_price[currentRow.rowIndex - 1], batch_no[currentRow.rowIndex - 1], product[currentRow.rowIndex - 1]);
                var pulldata = mtarget.value;
                getdataObject(pulldata, 'progdiv', 'get-product-rate', updatetextid);
                break;
            }
            //get-product-rate
        case 'get_batch_rate':
            {
                var rate = document.getElementsByName('rate[]');
                var updatetextid = new Array(batch_no[currentRow.rowIndex - 1]);
                var pulldata = mtarget.value;
                getdataObject(pulldata, 'progdiv', 'get_batch_rate', updatetextid);
                break;
            }
           

        case 'rgp_item_detail':
            {
                var job_process = document.getElementsByName('job_process[]');
                var qty = document.getElementsByName('qty[]');
                var un = document.getElementsByName('unit[]');
                var goodvalue = document.getElementsByName('goodvalue[]');
                var chrgpId = document.getElementById('chrgpId').value;

                var updatetextid = new Array(job_process[currentRow.rowIndex - 1], un[currentRow.rowIndex - 1], qty[currentRow.rowIndex - 1], goodvalue[currentRow.rowIndex - 1]);
                var pulldata = mtarget.value + '<$>' + chrgpId;
                //alert(pulldata);
                getdataObject(pulldata, 'progdiv', 'rgp_item_detail', updatetextid);
                break;
            }
            //rgp_item_detail
        case 'gate_qty':
            {
                var qty = document.getElementsByName('poqty[]');
                var poId = document.getElementById('poId').value;
                var updatetextid = new Array(qty[currentRow.rowIndex - 1]);
                getdataObject(mtarget.value + '-' + poId, 'progdiv', 'gate_qty', updatetextid);
                break;
            }
        case 'get-stock':
            {
                var aval_stock = document.getElementsByName('aval_stock[]');
                var updatetextid = new Array(aval_stock[currentRow.rowIndex - 1]);
                getdataObject(mtarget.value+'|'+data, 'progdiv', 'get_stock', updatetextid);
                // console.log(mtarget.value+'|'+data);
                break;
            }
            case 'get-stock-extra':
            { 
                var stock = document.getElementsByName('aval_stock_ex[]');
                var updatetextid = new Array(stock[currentRow.rowIndex - 1]);
                getdataObject(mtarget.value, 'progdiv', 'get_stock', updatetextid);
                break;
            }
        case 'stock_return':
            {
                var stock = document.getElementsByName('stockbalance[]');
                var updatetextid = new Array(stock[currentRow.rowIndex - 1]);
                getdataObject(mtarget.value, 'progdiv', 'stock_return', updatetextid);
                break;
            }
        case 'rate_qty':
            {
                var ratevalue = document.getElementsByName('rate[]');
                var prqty = document.getElementsByName('prqty[]');
                var updatetextid = new Array(ratevalue[currentRow.rowIndex - 1], prqty[currentRow.rowIndex - 1]);
                //alert(mtarget.value);
                getdataObject(mtarget.value, 'progdiv', 'get_rate_qty', updatetextid);
                break;
            }
        case 'get_ch_item':
            {
                var job_process = document.getElementsByName('job_process[]');
                var qty = document.getElementsByName('qty[]');
                var un = document.getElementsByName('unit[]');
                var goodvalue = document.getElementsByName('goodvalue[]');
                var updatetextid = new Array(job_process[currentRow.rowIndex - 1], qty[currentRow.rowIndex - 1], un[currentRow.rowIndex - 1], goodvalue[currentRow.rowIndex - 1]);
                //alert(mtarget.value);
                getdataObject(mtarget.value, 'progdiv', 'get_ch_item', updatetextid);
                break;
            }

        case 'raw_taken':
            {
                var raw = document.getElementsByName('aqty[]');
                var updatetextid = new Array(raw[currentRow.rowIndex - 1]);
                getdataObject(mtarget, 'progdiv', 'raw_taken', updatetextid);
                break;
            }

        case 'do_refresh':
            {
                var ch = currentRow.cells[2];
                function salehandler_ajax(val, progress_div, wcase)
                {
                    //alert('salehandler_ajax called');
                    ajax = gethttprequest_object(); // making an ajax object this will help make multiple asychronous call on one page
                    if (ajax)
                    {
                        // call the php script. use the get method. Pass the username in the url
                        ajax.open('get', 'js/ajax_mobile/ajax_mobile_php.php?pid=' + encodeURIComponent(val) + '&wcase=' + wcase);

                        //Function that handles the response
                        ajax.onreadystatechange = function() {
                            resp_salehandler_ajax(progress_div);
                        }
                        //send the request
                        ajax.send(null);
                    }
                    return;
                }
                // This function is called from salehandler_ajax() func above to handle the ajax response
                function resp_salehandler_ajax(progress_div)
                {
                    //if everything's OK
                    if ((ajax.readyState == 4) && (ajax.status == 200))
                    {
                        //alert(ajax.responseText);
                        var datafetch = ajax.responseText.split('<$>');
                        if (datafetch[0] == 'TRUE')
                        {
                            $(progress_div).html(datafetch[1]);
                            comboo();
                        }
                        else
                        {
                            $(progress_div).html('<span style="color:red;">' + datafetch[1] + '</span>');
                        }
                    }
                    else
                    {
                        //$(progress_div).style.display = 'inline';
                        $(progress_div).html('<img src="images/loader.gif" />fetching items ...');
                    }
                }
                salehandler_ajax('do_refresh', ch, 'do_refresh');
                return;
            }
    }
}
// Finding item here
function getitem(iid, id, batch, qty)
{
//alert(iid+' '+id+' '+batch+' '+qty);
//alert(iid);
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function()
    {
        resp_getitem(iid);
    }
    xmlhttp.open("GET", "js/ajax_general/get_item.php?option=" + id, true);
    xmlhttp.send();
}
function resp_getitem(iid)
{
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
    {

        var len1 = document.getElementById(iid).options.length;
        //alert(iid+ '--'+len1);
        //alert(len1);
        for (j = len1; j >= 0; j--)
        {
            document.getElementById(iid).remove(j);
        }
        var r1 = xmlhttp.responseText;
        //alert(r1);
        var a = r1.split('<$>');
        var len = a.length;
        var optn = new Array();
        optn[0] = document.createElement("OPTION");
        optn[0].text = 'Please Select...';
        optn[0].value = '';
        document.getElementById(iid).options.add(optn[0]);
        for (i = 0; i < len; i++)
        {
            optn[i + 1] = document.createElement("OPTION");
            var b = a[i].split('@');
            optn[i + 1].text = b[1];
            optn[i + 1].value = b[0];
            document.getElementById(iid).options.add(optn[i + 1]);
        }
        //get_size(size);
        //cal(qt,iid,am);	 
    }
}
function get_scheme()
{
   // var srate = document.getElementsByName('product_id[]');
    var qty = document.getElementsByName('quantity[]');
    var sch = document.getElementsByName('scheme[]');
    for (var i = 0; i < qty.length; i++)
    {
        qty_val = qty[i].value;   
        if(qty_val.length>1){
         var d = qty_val.length-1;
        sch_val = qty_val.substring(0, d);
        sch[i].value = sch_val;
    }

    }
}
function get_scheme_extra()
{
    //var srate = document.getElementsByName('product_id_ex[]');
    var qty = document.getElementsByName('quantity_ex[]');
    var sch = document.getElementsByName('scheme_ex[]');
    for (var i = 0; i < qty.length; i++)
    {
        qty_val = qty[i].value;   
        if(qty_val.length>1){
          var d = qty_val.length-1;
        sch_val = qty_val.substring(0, d);
        sch[i].value = sch_val;
    }

    }
}
/*        function get_saleable_non_saleable(pullId,wcase)
{
   // alert(11111111111111111111111);
    ajax = gethttprequest_object(); // making an ajax object this will help make multiple asychronous call on one page
    if (ajax)
    {
        //alert(wcase);
        // call the php script. use the get method. Pass the username in the url
        ajax.open('get', 'ajax_pulldown/pulldown_php.php?pid=' + pullId+ '&wcase=' + wcase);
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
}*/
function get_ajax_scheme(quantity, product, sch)
{
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function()
    {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
        {
            sch.value = xmlhttp.responseText;
        }
    }
    //alert('js/get_scheme.php?quantity='+quantity+'&product='+product);
    xmlhttp.open("GET", 'js/get_scheme.php?quantity=' + quantity + '&product=' + product, true);
    xmlhttp.send();
}


$('#mytable').on('change','.quantitycl',function()
 {
    var qty = parseInt($(this).val());
    var avbl_qty = parseInt($(this).closest("tr").find('.avlb_quantity').val());
    
    if(avbl_qty<qty)
    {
        alert('Available stock must be greater then input quantity');
        $(this).val('0');
        return false;
    }
 })

/*<!-- Get all details of a item for DirectChallan (PUNEET)-->*/
$(function(){

    /*******************************************************************
    ****** get Dealer Rate on taxable product or non-taxable product *****
    ********************************************************************/

     $('#mytable').on('change','.nkget_dealer_rate',function(){
    var ths = $(this);
    var product_id = ths.val();
   /* alert(product_id);*/ 
    $.ajax({
        type:'POST',
        url:'js/ajax_general/ajax_general_php.php',
        data:{pid:product_id,wcase:'getDealerRate'},
        success: function (data) { /*alert(data);*/
          var response = $.parseJSON(data);
          /*alert(response.data.mrp);*/
          if(response.exception)
          {
            alert(response.data);
          }else{
            var row = ths.parent().parent();
            var mrp = row.find('.nkget_dealer_mrp_rate');
            var dealer_mrp = row.find('.nkset_dealer_rate');
            mrp.val(response.data.mrp);
            dealer_mrp.val(response.data.dealer_rate);

            /*mrp.html('<option value="" selected>== Please Select ==</option>');
            $.each(response.data.mrp, function(k, v) {
               mrp.append('<option value="'+v+'">'+v+'</option>');
            });

            row.find('.rate').val(response.data.retailer_rate);
            row.find('.vat').val(parseFloat(response.data.gst).toFixed(2));  */           
          }
        }
    })
  })
  $('#mytable').on('change','.item_details',function(){
    var ths = $(this);
    var product_id = ths.val();

    var table  = $(this).closest('table');
    if(table.attr('class')!='')
    {
        var order_id = table.find('.oid').val();
        if(table.attr('class').indexOf("table") >= 0)
        {
            var pkigst = 'pkigst'+order_id;
            var rid = $('#retailer_id'+order_id+'').val();
            var rid = (rid) ? rid : 0;
        }
    }else{
        var pkigst = 'pkigst';
        var rid = ($('.retailer').val()) ? $('.retailer').val() : 0;
    }
    
    if(!parseInt(rid))
    {
        alert('Please select retailer.');
        return false;
    }
    
    $.ajax({
        type:'POST',
        url:'js/ajax_general/ajax_general_php.php',
        data:{pid:product_id,r_id:rid,wcase:'getItemDetails'},
        success: function (data) {
          var response = $.parseJSON(data);
          if(response.exception)
          {
            alert(response.data);
          }else{
            var row = ths.parent().parent();
            var mrp = row.find('.mrp');

            mrp.html('');
            if(response.data.mrp.length>1)
            {
                mrp.html('<option value="" selected>== Please Select ==</option>');                
            }
            
            $.each(response.data.mrp, function(k, v) {
               mrp.append('<option value="'+k+'">'+v+'</option>');
            });
            ths.closest('tr').find('.mrp').trigger("change");
            row.find('.rate').val(response.data.retailer_rate);


            if(response.data.st)
            {
                $('.'+pkigst+'').val(1);
                row.find('.sgst').val(parseFloat(response.data.sgst).toFixed(2));
                row.find('.cgst').val(parseFloat(response.data.cgst).toFixed(2));
                row.find('.vat').val();
                row.find('.vat_amt').val();
            }else{
                $('.'+pkigst+'').val(0);
                row.find('.vat').val(parseFloat(response.data.gst).toFixed(2));
                row.find('.sgst').val();
                row.find('.sgst_amt').val();
                row.find('.cgst').val();
                row.find('.cgst_amt').val();
            }
          }
        }
    })
  })

  $('#mytable').on('change','.mrp_dd',function(){
     var id = $(this).closest('tr').find('.item_details').val();
     var avlb_quantity = $(this).closest('tr').find('.avlb_quantity');
     var qty = $(this).closest('tr').find('.quantitycl');
     var rate = $(this).closest('tr').find('.rate');
     var m  = $(this).val();

     $.ajax({
        type:'POST',
        url:'js/ajax_general/ajax_general_php.php',
        data:{pid:id,mrp:m,wcase:'rateNstock'},
        success:function(data){
          var resp = $.parseJSON(data);
          qty.val('');
          avlb_quantity.val(resp.data.qty);
          rate.val(resp.data.rate);
        }
     })      
  })

})


function tableToExcel(table, name)
{
        // document.getElementById('img').src = "#";
        var tableToExcel = (function() { 
        var uri = 'data:application/vnd.ms-excel;base64,'
          , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
          , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
          , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
        
          if (!table.nodeType) table = document.getElementById(table)
          var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML.replace(/<img[^>]*>/g,"")}
          // ctx = ctx.replace(/<img[^>]*>/g,"");
          // content.replace(/<img[^>"']*((("[^"]*")|('[^']*'))[^"'>]*)*>/g,"");
          window.location.href = uri + base64(format(template, ctx))
      })()
  }