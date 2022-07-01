// this function will calculate the inner width of wrapper div
function setSize() 
{	
// code from where i took it http://www.howtocreate.co.uk/tutorials/javascript/browserwindow
  var myWidth = 0, myHeight = 0;
  if( typeof( window.innerWidth ) == 'number' ) 
  {
    //Non-IE
    myWidth = window.innerWidth;
    myHeight = window.innerHeight;
  } 
  else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) 
  {
    //IE 6+ in 'standards compliant mode'
    myWidth = document.documentElement.clientWidth;
    myHeight = document.documentElement.clientHeight;
  } 
  else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) 
  {
    //IE 4 compatible
    myWidth = document.body.clientWidth;
    myHeight = document.body.clientHeight;
  }
  document.getElementById('wrapper').style.height = (myHeight-100)+'px';  //original 136
}// function setsize ends here

// The code in this includes the basic js functions which will be used in most of the pages and is thus included at the start
//<!-- This script will open up the pop up window -->
function PopupCenter(pageURL, title,w,h) {
var left = (screen.width/2)-(w/2);
var top = (screen.height/2)-(h/2);
var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
}


//<!-- This script will open up the pop up window scrolling yes option -->
function PopupCenter1(pageURL, title,w,h) {
var left = (screen.width/2)-(w/2);
var top = (screen.height/2)-(h/2);
var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
}

//<!-- This script will open up the pop up window scrolling yes option and in full screen -->
function PopupCenterF(pageURL, title) {
//var left = (screen.width/2)-(w/2);
//var top = (screen.height/2)-(h/2);
var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no');
}



//<!-- this script will control the text areas word limit -->
function textCounter(field,cntfield,maxlimit)
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
   return string.replace( /(^|\s)([a-z])/g , function(m,p1,p2){ return p1+p2.toUpperCase(); } );
}

//triming the white spaces in javascript
function trim(stringToTrim)
{
	return stringToTrim.replace(/^\s+|\s+$/g,"");
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
   if(typeof evt.target != 'undefined') // for firefox and other browsers
		var mtarget = evt.target;
	else if(evt.srcElement) // indicating it is Internet Explorer family
		var mtarget = evt.srcElement;
   var pos = mtarget.value.indexOf(".");
   if(pos != -1) 
   {
   		var found = true; 
   }
   else
   	var found = false;
   if(found)
   {
		if (charCode > 31 && (charCode < 48 || charCode > 57))
     		 return false;
   }
   else
   {
	   if(charCode == 46 ) return true;
	   if (charCode > 31 && (charCode < 48 || charCode > 57))
     		 return false;
   }   	
   return true;
}

function printing1(printarea)
{
	var DocumentContainer = document.getElementById(printarea);
    var WindowObject = window.open('', 'PrintWindow', 'width=850, height=700, top=50, left=50, toolbars=no, scrollbars=yes, status=no, resizable=yes');	
	var textprint='<center><h3><strong><style type="text/css">table{font-size:9px; font-family:Verdana, Geneva, sans-serif;} .options{display:none;}</style></strong></h3><br/></center>';
	textprint += DocumentContainer.innerHTML;
   //WindowObject.document.writeln('<html><head><link rel="stylesheet" type="text/css" href="css/print.css" /></head><body style="font-size:12px;">'+textprint+'</body></html>');
   WindowObject.document.writeln(textprint);
   WindowObject.document.close();
   WindowObject.focus();
   WindowObject.print();
   WindowObject.close();
}

function printing(printarea)
{
	var DocumentContainer = document.getElementById(printarea);
	var textprint = DocumentContainer.innerHTML;
	//To allow the printing of the current view pane or all pages ends
	if(document.getElementById('cpage')){
		var myprintpage = document.getElementById('cpage').value*1;
		DocumentContainer = document.getElementById('mypages'+myprintpage);
		if(confirm('Print current page only')){
			textprint = DocumentContainer.innerHTML;			
		}else{			
			var printheader = $('#mypages1 div').eq(1);
			var headtr = $('#mypages1 table.searchlist tr.search1tr:first');
			var datatr = $("table.searchlist").find("> tbody > tr:not(.search1tr)");
			
			textprint = printheader.html();
			textprint += '<table width="100%">'+headtr.html();
			
			datatr.each(function( index ) {textprint += $(this).clone().wrap('<table>').parent().html();});
			textprint +='</table>';
		}
	}
	//To allow the printing of the current view pane or all pages ends
		
    var WindowObject = window.open('', 'PrintWindow', 'width=850,height=700,top=50,left=50,toolbars=no,scrollbars=yes,status=no,resizable=yes');	
	
   WindowObject.document.writeln('<html><head><link media="print" rel="stylesheet" type="text/css" href="./css/print.css" /></head><body style="font-size:12px;" id="myprintarea">'+textprint+'</body></html>');
   WindowObject.document.close();
   WindowObject.focus();
   WindowObject.print();
   WindowObject.close();
}

function pdf(printarea)
{
	var DocumentContainer = document.getElementById(printarea);
    var WindowObject = window.open('./printpdf.php', 'PrintWindow', 'width=100,height=20,top=50,left=50,toolbars=no,scrollbars=yes,status=no,resizable=yes');	
	var textprint = DocumentContainer.innerHTML;
   WindowObject.document.writeln('<html><head><link rel="stylesheet" type="text/css" href="css/print.css" /></head><body style="font-size:12px; text-align:center;" onblur="window.close();"><form name="testform" method="post" action="printpdf.php"> <textarea name="printcontent" style="display:none;">'+textprint+'</textarea><input name="thesubmit" type="submit" value="create pdf"></form></body></html>');
   //WindowObject.document.close();
   WindowObject.focus();
  // WindowObject.close();
}

function formenable(formname)
{
	var formToProcess = window.document.forms[formname];
	for(var i = 0; i< formToProcess.length; i++)
	{
		if(formToProcess.elements[i].type == 'text' || formToProcess.elements[i].type == 'submit' || formToProcess.elements[i].type == 'password' || formToProcess.elements[i].type == 'checkbox' || formToProcess.elements[i].type == 'radio' || formToProcess.elements[i].type == 'file' || formToProcess.elements[i].type == 'select-one' || formToProcess.elements[i].type == 'textarea')
		{
			formToProcess.elements[i].disabled = false;
		}
	}
}

function formdisable(formname)
{
	var formToProcess = window.document.forms[formname];
	for(var i = 0; i< formToProcess.length; i++)
	{
		if(formToProcess.elements[i].type == 'text' || formToProcess.elements[i].type == 'submit' || formToProcess.elements[i].type == 'password' || formToProcess.elements[i].type == 'checkbox' || formToProcess.elements[i].type == 'radio' || formToProcess.elements[i].type == 'file' || formToProcess.elements[i].type == 'select-one' || formToProcess.elements[i].type == 'textarea')
		{
			formToProcess.elements[i].disabled = true;
		}
	}
}

function clearspan(wclass)
{
  setTimeout(function(){
    $("span."+wclass).fadeOut("slow", function () {
    $("span."+wclass).remove();
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
	if(OpullId.options[OpullId.selectedIndex].value != '')
		document.getElementById(fieldId).value = OpullId.options[OpullId.selectedIndex].text;
	else
		document.getElementById(fieldId).value = '';
}

function dropdownSelectedValue(drId)
{
	var dropDown = document.getElementById(drId);
	if(dropDown.options[dropDown.selectedIndex].value != '')
		return dropDown.options[dropDown.selectedIndex].value;
	else
		return false;
}

function dropdownSelectedText(drId)
{
	var dropDown = document.getElementById(drId);
	if(dropDown.options[dropDown.selectedIndex].value != '')
		return dropDown.options[dropDown.selectedIndex].text;
	else
		return false;
}

//This function will give focus to the field of our choice when the page loads
function setfocus(fid)
{
	if(document.getElementById(fid))
		document.getElementById(fid).focus();
}

//This function will clear all the option of a pull down
function eclearOption(id)
{
	document.getElementById(id).options.length = 0;
}

function checkemail(id)
{
	if(document.getElementById(id))
	{
		var email = document.getElementById(id);
		if(email.value != '')
		{
			var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			var output = filter.test(email.value);
			if(!output)
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
function selectCheckBoxes(id, value)
{
	var chkbox = document.getElementsByName(value);
	for(var i=0; i<chkbox.length; i++)
	{
		if(document.getElementById(id).checked)
			chkbox[i].checked = true;
		else
			chkbox[i].checked = false;
	}
}

//This function will help in the closing of the CSS POPUP
function cssPopClose(closeAction)
{
	switch(closeAction)
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


//code for dymamic add and delete of rows with the use of ajax starts here.........
function addmore_deep(tableId, event, func_after_add, wcase)
{
	var mtable = document.getElementById(tableId);
	var trows = mtable.rows.length;
	
	if(typeof event.target != 'undefined') // for firefox and other browsers
		var mtarget = event.target;
	else if(event.srcElement) // indicating it is Internet Explorer family
		var mtarget = event.srcElement;
	else 
		return;
		
		//alert(mtarget.type);
	
	var totdatarow = $('#'+tableId+' tr.tdata').size();
	
	if(mtarget.title == 'more' || mtarget.type == 'text')
	{
		var currentRow = mtarget.parentNode.parentNode;
		var newRow = mtable.insertRow(currentRow.rowIndex +1); // insert new row
		newRow = $(newRow).addClass('tdata');
		newRow = newRow[0];
		newRow.innerHTML = currentRow.innerHTML;
		
		$(newRow).find("input").each(function(){
			if($(this).hasClass("hasDatepicker")){ // if the current input has the hasDatpicker class
			
				var this_id = $(this).attr("id"); // current inputs id
				var new_id = this_id +1; // a new id
				$(this).attr("id", new_id); // change to new id
			
				$(this).removeClass('hasDatepicker'); // remove hasDatepicker class
				 // re-init datepicker
				$(this).qdatepicker({
				  numberOfMonths: 1,
				  showButtonPanel: false,
				  changeYear : false,
				  yearRange : "-1:+1",
				  dateFormat : "dd/m/yy",
				  minDate: -0
			  });
			}
		});
		
		newRow.cells[newRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmore_deep(\''+tableId+'\', event, \''+func_after_add+'\', \''+wcase+'\');"/><img  title="less" src="images/less.png" onclick="javascript:addmore_deep(\''+tableId+'\', event, \''+func_after_add+'\', \''+wcase+'\');"/>';
	}
	else if(mtarget.title == 'less')
	{
		var currentRow = mtarget.parentNode.parentNode;
			mtable.deleteRow(currentRow.rowIndex); // delete current row
			
			
	}
	setsno_deep(tableId);
	if(jQuery.isFunction(func_after_add)) 
		func_after_add(mtarget, currentRow, newRow, wcase);
}

function setsno_deep(tableId)
{
	$('#'+tableId+' tr.tdata').each(function(i){
		$(this).find('td.myintrow:first').html((i+1)*1);
	});
}


function addmore(tableId, event, func_after_add)
{
	var mtable = document.getElementById(tableId);
	var trows = mtable.rows.length;
	if(typeof event.target != 'undefined') // for firefox and other browsers
		var mtarget = event.target;
	else if(event.srcElement) // indicating it is Internet Explorer family
		var mtarget = event.srcElement;
	else 
		return;
		
	if(mtarget.title == 'more')
	{
		var currentRow = mtarget.parentNode.parentNode;
		var newRow = mtable.insertRow(currentRow.rowIndex +1); // insert new row
		newRow.innerHTML = currentRow.innerHTML;
		//alert(newRow.cells[newRow.cells.length - 1].innerHTML);
		newRow.cells[newRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmore(\''+tableId+'\', event);"/><img  title="less" src="images/less.png" onclick="javascript:addmore(\''+tableId+'\', event);"/>';
		//to replace the plus sign with - also for the first tr also
		if(currentRow.rowIndex == 1)
		{
			currentRow.cells[currentRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmore(\''+tableId+'\', event);"/><img  title="less" src="images/less.png" onclick="javascript:addmore(\''+tableId+'\', event);"/>';
		}
	}
	else if(mtarget.title == 'less')
	{
		var currentRow = mtarget.parentNode.parentNode;
		if(trows == 3 && currentRow.rowIndex == 1) // checking if this is the last row to be edited, then only + sign should stay.
		{
			currentRow.cells[currentRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmore(\''+tableId+'\', event);"/>';
		}
		else
		{
			mtable.deleteRow(currentRow.rowIndex); // delete current row
			// call the desired calculation function
			//getvaluetotal();
		}
	}
	setsno(tableId);
	if(jQuery.isFunction(func_after_add)) 
		func_after_add(mtarget, currentRow, newRow);
}

//This will not give the addmore sign
function addmore_2(tableId, event, func_after_add)
{
	var mtable = document.getElementById(tableId);
	var trows = mtable.rows.length;
	if(typeof event.target != 'undefined') // for firefox and other browsers
		var mtarget = event.target;
	else if(event.srcElement) // indicating it is Internet Explorer family
		var mtarget = event.srcElement;
	else 
		return;
		
	if(mtarget.title == 'more')
	{
		var currentRow = mtarget.parentNode.parentNode;
		var newRow = mtable.insertRow(currentRow.rowIndex +1); // insert new row
		newRow.innerHTML = currentRow.innerHTML;
		//alert(newRow.cells[newRow.cells.length - 1].innerHTML);
		newRow.cells[newRow.cells.length - 1].innerHTML = '<img  title="less" src="images/less.png" onclick="javascript:addmore(\''+tableId+'\', event);"/>';
		//to replace the plus sign with - also for the first tr also
		if(currentRow.rowIndex == 1)
		{
			currentRow.cells[currentRow.cells.length - 1].innerHTML = '<img  title="less" src="images/less.png" onclick="javascript:addmore(\''+tableId+'\', event);"/>';
		}
	}
	else if(mtarget.title == 'less')
	{
		var currentRow = mtarget.parentNode.parentNode;
		if(trows == 3 && currentRow.rowIndex == 1) // checking if this is the last row to be edited, then only + sign should stay.
		{
			currentRow.cells[currentRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmore(\''+tableId+'\', event);"/>';
		}
		else
		{
			mtable.deleteRow(currentRow.rowIndex); // delete current row
			
		}
		
	}
	setsno(tableId);
	if(jQuery.isFunction(func_after_add)) 
		func_after_add(mtarget, currentRow, newRow);
}

//function for dynamically deleting rows (but with without implimentation of ajax )
function deleteRow(tableID, func_after_del) 
{
	try 
	{
		var table = document.getElementById(tableID);
		var rowCount = table.rows.length;
		for(var i=0; i<rowCount; i++)
		{
			var row = table.rows[i];
			var chkbox = row.cells[0].childNodes[0];
			if(null != chkbox && true == chkbox.checked)
			{
				if(rowCount <= 1) 
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
	catch(e)
	{
		alert(e);
	}
	if($.isFunction(func_after_del)) 
		func_after_del();
}
//code ends here for dymamic add and delete of rows

// to set the numbers in the sno column and it is optional
function setsno(tableId)
{
	var mtable = document.getElementById(tableId);
	//alert(mtable.rows.length);
	for(var i = 1; i<mtable.rows.length; i++)
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
	for(var i = 0; i<srate.length; i++)
	{
		
		if(srate[i].value == '')
			var rate = 0;
		else
			var rate = srate[i].value;
		if(qty[i].value == '')
			var quantity = 0;
		else
			var quantity = qty[i].value;
		var res = quantity * rate;
		samount[i].value = res.toFixed(2);
		gtotal += samount[i].value*1;// multiply by so that total[i].value is treated as numeric
	}
	document.getElementById('totalamount').value = gtotal.toFixed(2);
	var totalamount = document.getElementById('totalamount').value;
	var basicexduty = totalamount * 10/100 ;
	document.getElementById('taxamount1').value = basicexduty.toFixed(2);
	var csessonbed = basicexduty * 2/100;
	document.getElementById('taxamount2').value = csessonbed.toFixed(2);
	
	var Hrsescess = csessonbed * 1/100;
	document.getElementById('taxamount3').value = Hrsescess.toFixed(2);
	var taxable_tot = totalamount *1 + basicexduty *1 + csessonbed *1 + Hrsescess *1 ;
	document.getElementById('taxabletotal').value = taxable_tot.toFixed(2);
	var sale_tax = taxable_tot * 4/100;
	document.getElementById('taxamount4').value = sale_tax.toFixed(2);
	var Surcharge = sale_tax * 5/100;
	document.getElementById('taxamount5').value = sale_tax.toFixed(2);
	var misc_charge = document.getElementById('taxamount6').value;
	var grandtotal = taxable_tot * 1 + sale_tax *1 + Surcharge *1 + misc_charge *1;
	document.getElementById('grandtotal').value = grandtotal.toFixed(2);
	
}
function product_calculate()
{
	
	var qty = document.getElementsByName('quantity[]');
	var bp = document.getElementsByName('base_price[]');
        var pv = document.getElementsByName('prodvalue[]');
        //prodvalue
	//alert('ANKUSH');
	for(var i = 0; i<qty.length; i++)
	{
                var res =  qty[i].value * bp[i].value;   
		pv[i].value = res.toFixed(2);
      	}
        

}
function getajaxdata(wcase, tableId, event)
{
	
	var mtable = document.getElementById(tableId);
	var trows = mtable.rows.length;
	if(typeof event.target != 'undefined') // for firefox and other browsers
		var mtarget = event.target;
	else if(event.srcElement) // indicating it is Internet Explorer family
		var mtarget = event.srcElement;
	else 
		return;
	var currentRow = mtarget.parentNode.parentNode;
	switch(wcase)
	{
		case 'rate':
		{
			var ratevalue = document.getElementsByName('rate[]');
			var updatetextid = new Array(ratevalue[currentRow.rowIndex - 1]);
			getdataObject(mtarget.value, 'progdiv', 'get_rate', updatetextid);
			break;			
		}
		case 'lineno':
		{
			
			var lineno = document.getElementsByName('lineno[]');
			var rate = document.getElementsByName('rate[]');
			var wpoId = document.getElementById('wpoId').value;
			var updatetextid = new Array(lineno[currentRow.rowIndex - 1],rate[currentRow.rowIndex - 1]);
			var pulldata = mtarget.value+'<$>'+wpoId;
			getdataObject(pulldata, 'progdiv', 'get_lineno', updatetextid);
			break;			
		}
                case 'get_product_details':
		{
			
			//var quantity = document.getElementsByName('quantity[]');
                        var base_price = document.getElementsByName('base_price[]');
			//var prodvalue = document.getElementsByName('prodvalue[]');
			var updatetextid = new Array(base_price[currentRow.rowIndex - 1]);
			var pulldata = mtarget.value;
			getdataObject(pulldata, 'progdiv', 'get_product_details', updatetextid);
			break;			
		}                
		case 'rgp_item_detail':
		{
			var job_process = document.getElementsByName('job_process[]');
			var qty = document.getElementsByName('qty[]');
			var un = document.getElementsByName('unit[]');
			var goodvalue = document.getElementsByName('goodvalue[]');
			var chrgpId = document.getElementById('chrgpId').value;
			
			var updatetextid = new Array(job_process[currentRow.rowIndex - 1],un[currentRow.rowIndex - 1],qty[currentRow.rowIndex - 1],goodvalue[currentRow.rowIndex - 1]);
			var pulldata = mtarget.value+'<$>'+chrgpId;
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
			getdataObject(mtarget.value+'-'+poId, 'progdiv', 'gate_qty', updatetextid);
			break;			
		}
		case 'stock':
		{
			var stock = document.getElementsByName('stockbalance[]');
			var updatetextid = new Array(stock[currentRow.rowIndex - 1]);
			getdataObject(mtarget.value, 'progdiv', 'stock_issue', updatetextid);
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
			var updatetextid = new Array(ratevalue[currentRow.rowIndex - 1],prqty[currentRow.rowIndex - 1]);
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
			var updatetextid = new Array(job_process[currentRow.rowIndex - 1],qty[currentRow.rowIndex - 1],un[currentRow.rowIndex - 1], goodvalue[currentRow.rowIndex - 1]);
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
				if(ajax)
				{
					// call the php script. use the get method. Pass the username in the url
					ajax.open('get','js/ajax_mobile/ajax_mobile_php.php?pid='+ encodeURIComponent(val)+'&wcase='+wcase);
					
					//Function that handles the response
					ajax.onreadystatechange =function () { 
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
				if((ajax.readyState == 4) && (ajax.status == 200))
				{
					//alert(ajax.responseText);
					var datafetch = ajax.responseText.split('<$>');
					if(datafetch[0] == 'TRUE' )
					{	
						$(progress_div).html(datafetch[1]);
						comboo();
					}
					else
					{
						$(progress_div).html('<span style="color:red;">'+datafetch[1]+'</span>');
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

