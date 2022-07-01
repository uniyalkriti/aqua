 // JavaScript Document
 // this function will take four parameter, what we are deleting its id, its name and the progress div
function do_close(doption,deleteId, delWhat, name)
{
	if(confirm('Are you sure want to close '+ delWhat +'\n '+ name))
	{
		ajax_delete(doption, deleteId, 'delDiv'+deleteId);
	}
}// delete function ends here
function do_delete(doption,deleteId, delWhat, name)
{
	//alert(deleteId);
	if(confirm('Are you sure want to delete '+ delWhat +'\n '+ name))
	{
		ajax_delete(doption, deleteId, 'delDiv'+deleteId);
	}
}// delete function ends here
function do_delete_special(doption,deleteId, delWhat, name, special_parameter)
{
	//alert(deleteId);
	if(confirm('Are you sure want to delete '+ delWhat +'\n '+ name))
	{
		ajax_delete_special(doption, deleteId, 'delDiv'+deleteId, special_parameter);
	}
}// delete function ends here

function ajax_delete_special(pageId, deleteId, progress_div,special_parameter)
{
	ajax = gethttprequest_object(); // making an ajax object this will help make multiple asychronous call on one page
	//confirm that the object is usable
	if(ajax)
	{
		// call the php script. use the get method. Pass the username in the url
		ajax.open('get','js/ajax_delete/data_delete_php1.php?pageId='+ encodeURIComponent(pageId)+'&deleteId='+ encodeURIComponent(deleteId)+'&levelType='+ encodeURIComponent(special_parameter));
		
		//Function that handles the response
		//ajax.onreadystatechange = handle_check; // when you dont want to pass argument to call back function
		ajax.onreadystatechange =function () { 
                                    ajax_delete_response(progress_div, deleteId);
                                    }
		//send the request
		ajax.send(null);
	}
	else
	{
		//cant use ajax!
		document.getElementById(progress_div).innerHTML = 'The availability of deletion will be confirmed upon form submission.';
	}
}


//This code will delete the data based on the deletion id starts here
// this will need the pageId, which will tell what we are deleting
function ajax_delete(pageId, deleteId, progress_div)
{
	ajax = gethttprequest_object(); // making an ajax object this will help make multiple asychronous call on one page
	//confirm that the object is usable
	if(ajax)
	{
		// call the php script. use the get method. Pass the username in the url
		ajax.open('get','js/ajax_delete/data_delete_php.php?pageId='+ encodeURIComponent(pageId)+'&deleteId='+ encodeURIComponent(deleteId));
		
		//Function that handles the response
		//ajax.onreadystatechange = handle_check; // when you dont want to pass argument to call back function
		ajax.onreadystatechange =function () { 
                                    ajax_delete_response(progress_div, deleteId);
                                    }
		//send the request
		ajax.send(null);
	}
	else
	{
		//cant use ajax!
		document.getElementById(progress_div).innerHTML = 'The availability of deletion will be confirmed upon form submission.';
	}
}

function ajax_delete_response(progress_div, deleteId)
{
	//if everything's OK
	if((ajax.readyState == 4) && (ajax.status == 200))
	{
		//assign the returned value to a document element
		if(ajax.responseText=='stock')
		{
			alert('Stock successfully deleted');
			window.location = "index.php?option=balance-stock";
			return false;
		}
		
	    alert(ajax.responseText);
		var serverdatac = ajax.responseText.split('<$$>');// <$$> is used as first level of seperator
		//document.getElementById(progress_div).style.display = 'none';
		if(serverdatac[0] == 'TRUE')
		{
			document.getElementById(progress_div).style.color = 'green';
			document.getElementById(progress_div).innerHTML = serverdatac[1];
			alert(serverdatac[1]);
			// deleting the row from the table starts here
			var el = document.getElementById('tr'+deleteId);
			var totcounter = document.getElementById('totCounter').innerHTML*1 - 1; //decrementing the total entry
			document.getElementById('totCounter').innerHTML = totcounter;// decrease the total
			el.parentNode.removeChild(el);// removing the tr after sometime
			// deleting the row from the table ends here
			
			// Rechanging the background color of the table with id searchdata starts here
			var bg ='#efede8';
			var trs = document.getElementById('searchdata').getElementsByTagName('tr');
			for(var i=1; i<document.getElementById('searchdata').rows.length; i++)
			{
				bg=(bg=='#efede8'?'#ffffff':'#efede8');// to provide different row colors(member_contacted table)
				trs[i].style.backgroundColor = bg;
			}
			// Rechanging the background color of the table with id searchdata ends here
		}
		else
		{
			//alert('Sorry no data found. ' + ajax.responseText);
			//alert(serverdatac[1]);
			document.getElementById(progress_div).style.color = 'red';
			document.getElementById(progress_div).innerHTML = serverdatac[1];
			setTimeout("document.getElementById('"+progress_div+"').style.display = 'none';",3000);
			//document.getElementById(progress_div).style.display = 'none';
		}
	}
	else
	{
		document.getElementById(progress_div).style.display = 'block';
		document.getElementById(progress_div).innerHTML = '<img src="images/ajax-loader.gif" /><span style="margin-left:15px; color:red;">deletion in progress ...</span>';
	}
}
//confirming the presence of barcode in the database before making further queries ends here

function close_po(pageId, deleteId, progress_div)
{
	var ans = confirm('Are you sure to close Purchase Order' + deleteId);
	if(ans)
	{
		code_for_close_po(pageId, deleteId, progress_div);
	}
}
function code_for_close_po(pageId, deleteId, progress_div)
{
	if (window.XMLHttpRequest)
  	{// code for IE7+, Firefox, Chrome, Opera, Safari
 		 xmlhttp=new XMLHttpRequest();
 	}
	else
  	{// code for IE6, IE5
  		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  	}
	xmlhttp.onreadystatechange=function()
  	{
  		if (xmlhttp.readyState==4 && xmlhttp.status==200)
   		 {
    		var r = xmlhttp.responseText;
			//alert(r);
			var a = r.split('<$$>');
			if(a[0] == 'TRUE')
			alert(a[1]);
			document.getElementById(progress_div).innerHTML = '<a href="#">print</a>';
			location.reload(true);
			//alert(r);
    	 }
  	}
	xmlhttp.open('get','js/ajax_delete/data_delete_php.php?pageId='+ encodeURIComponent(pageId)+'&deleteId='+ encodeURIComponent(deleteId));
	xmlhttp.send();
}
/////////////////////////////////////////////////////////////////
function sh_lock(pageId, deleteId, progress_div)
{
	var ans = confirm('Are you sure to Lock schedule' + deleteId + '\nAfter lock Schedule cannot be changed');
	if(ans)
	{
		code_for_sh_lock(pageId, deleteId, progress_div);
	}
}
function code_for_sh_lock(pageId, deleteId, progress_div)
{
	if (window.XMLHttpRequest)
  	{// code for IE7+, Firefox, Chrome, Opera, Safari
 		 xmlhttp=new XMLHttpRequest();
 	}
	else
  	{// code for IE6, IE5
  		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  	}
	xmlhttp.onreadystatechange=function()
  	{
  		if (xmlhttp.readyState==4 && xmlhttp.status==200)
   		 {
    		var r = xmlhttp.responseText;
			//alert(r);
			var a = r.split('<$$>');
			if(a[0] == 'TRUE')
			alert(a[1]);
			location.reload(true);
			//document.getElementById(progress_div).innerHTML = '<a href="#">print</a>';
			//alert(r);*/
    	 }
  	}
	xmlhttp.open('get','js/ajax_delete/data_delete_php.php?pageId='+ encodeURIComponent(pageId)+'&deleteId='+ encodeURIComponent(deleteId));
	xmlhttp.send();
}

/////////////////////////////////////////////////////////////

function qc_lock(pageId, deleteId, progress_div)
{
	var list = (deleteId);
	listArray = list.split('$');
	var rej = listArray[2];
	if(rej == 'N.A' || rej == '')
	{
		var ans = confirm('Are you sure to Lock Quality Check of process' + listArray[0] + '\nAfter Lock, Quality Check of this process cannot be changed');
	}
	else
	{
		var ans = confirm('There is Rejection of ' + rej +' Pcs.\nAre you sure to Lock Quality Check of process' + listArray[0] + '\nAfter Lock, Quality Check of this process cannot be changed');
	}
	if(ans)
	{
		code_for_qc_lock(pageId, deleteId, progress_div);
	}
}
function code_for_qc_lock(pageId, deleteId, progress_div)
{
	if (window.XMLHttpRequest)
  	{// code for IE7+, Firefox, Chrome, Opera, Safari
 		 xmlhttp=new XMLHttpRequest();
 	}
	else
  	{// code for IE6, IE5
  		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  	}
	xmlhttp.onreadystatechange=function()
  	{
  		if (xmlhttp.readyState==4 && xmlhttp.status==200)
   		 {
    		var r = xmlhttp.responseText;
			//alert(r);
			var a = r.split('<$$>');
			if(a[0] == 'TRUE')
			alert(a[1]);
			location.reload(true);
			//document.getElementById(progress_div).innerHTML = '<a href="#">print</a>';
			//alert(r);*/
    	 }
  	}
	xmlhttp.open('get','js/ajax_delete/data_delete_php.php?pageId='+ encodeURIComponent(pageId)+'&deleteId='+ encodeURIComponent(deleteId));
	xmlhttp.send();
}

/////////////////////////////////////////////////////////////
function close_pr(pageId, deleteId, progress_div)
{
	var ans = confirm('After closing, this PR is not available for Purchase Order.\nAre you sure to close Purchase Request ' + deleteId);
	if(ans)
	{
		var reply = prompt("Please Enter reason for PR Close!" , "")
		deleteId = deleteId + '$' + reply;
		code_for_close_pr(pageId, deleteId, progress_div);
	}
}

function code_for_close_pr(pageId, deleteId, progress_div)
{
	if (window.XMLHttpRequest)
  	{// code for IE7+, Firefox, Chrome, Opera, Safari
 		 xmlhttp=new XMLHttpRequest();
 	}
	else
  	{// code for IE6, IE5
  		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  	}
	xmlhttp.onreadystatechange=function()
  	{
  		if (xmlhttp.readyState==4 && xmlhttp.status==200)
   		 {
    		var r = xmlhttp.responseText;
			//alert(r);
			var a = r.split('<$$>');
			if(a[0] == 'TRUE')
			alert(a[1]);
			location.reload(true);
    	 }
  	}
	xmlhttp.open('get','js/ajax_delete/data_delete_php.php?pageId='+ encodeURIComponent(pageId)+'&deleteId='+ encodeURIComponent(deleteId));
	xmlhttp.send();
}

function cancel_po(pageId, deleteId, progress_div)
{
	//var list = (deleteId);
	//listArray = list.split('$');
	var ans = confirm('Are you sure to Cancel Purchase Order ' + deleteId);
	if(ans)
	{
		code_for_cancel_po(pageId, deleteId, progress_div);
	}
}

function code_for_cancel_po(pageId, deleteId, progress_div)
{
	if (window.XMLHttpRequest)
  	{// code for IE7+, Firefox, Chrome, Opera, Safari
 		 xmlhttp=new XMLHttpRequest();
 	}
	else
  	{// code for IE6, IE5
  		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  	}
	xmlhttp.onreadystatechange=function()
  	{
  		if (xmlhttp.readyState==4 && xmlhttp.status==200)
   		 {
    		var r = xmlhttp.responseText;
			//alert(r);
			var a = r.split('<$$>');
			if(a[0] == 'TRUE')
			alert(a[1]);
			location.reload(true);
			//document.getElementById(progress_div).innerHTML = '<a href="#">print</a>';
			//alert(r);
    	 }
  	}
	xmlhttp.open('get','js/ajax_delete/data_delete_php.php?pageId='+ encodeURIComponent(pageId)+'&deleteId='+ encodeURIComponent(deleteId));
	xmlhttp.send();
}

//code for pr lock which is less then minimum limit start here
function lockpr(pageId, deleteId, progress_div)
{
	var ans = confirm('Are you sure to lock this purchase request' + deleteId);
	if(ans)
	{
		code_for_lockpr(pageId, deleteId, progress_div);
	}
}
function code_for_lockpr(pageId, deleteId, progress_div)
{
	if (window.XMLHttpRequest)
  	{// code for IE7+, Firefox, Chrome, Opera, Safari
 		 xmlhttp=new XMLHttpRequest();
 	}
	else
  	{// code for IE6, IE5
  		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  	}
	xmlhttp.onreadystatechange=function()
  	{
  		if (xmlhttp.readyState==4 && xmlhttp.status==200)
   		 {
    		var r = xmlhttp.responseText;
			//alert(r);
			var a = r.split('<$$>');
			if(a[0] == 'TRUE')
			alert(a[1]);
			//document.getElementById(progress_div).innerHTML = '<a href="#">print</a>';
			location.reload(true);
			//alert(r);
    	 }
  	}
	xmlhttp.open('get','js/ajax_delete/data_delete_php.php?pageId='+ encodeURIComponent(pageId)+'&deleteId='+ encodeURIComponent(deleteId));
	xmlhttp.send();
}
//code for pr lock which is less then minimum limit ends here
 
//code for payment start here
function payment(pageId, deleteId, progress_div)
{
	var ans = confirm('Are you sure to Recieve Payment for Bill No' + deleteId);
	if(ans)
	{
		code_for_payment(pageId, deleteId, progress_div);
	}
}
function code_for_payment(pageId, deleteId, progress_div)
{
	if (window.XMLHttpRequest)
  	{// code for IE7+, Firefox, Chrome, Opera, Safari
 		 xmlhttp=new XMLHttpRequest();
 	}
	else
  	{// code for IE6, IE5
  		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  	}
	xmlhttp.onreadystatechange=function()
  	{
  		if (xmlhttp.readyState==4 && xmlhttp.status==200)
   		 {
    		var r = xmlhttp.responseText;
			//alert(r);
			var a = r.split('<$$>');
			if(a[0] == 'TRUE')
			alert(a[1]);
			location.reload(true);
			//alert(r);
    	 }
  	}
	xmlhttp.open('get','js/ajax_delete/data_delete_php.php?pageId='+ encodeURIComponent(pageId)+'&deleteId='+ encodeURIComponent(deleteId));
	xmlhttp.send();
}

//code for payment ends here
// code for challan deletion start here
function challan_delete(doption,deleteId, delWhat, name, special_parameter)
{
	//alert(deleteId);
	if(confirm('Are you sure want to delete '+ delWhat +'\n '+ name))
	{
		challan_delete_special(doption, deleteId, 'delDiv'+deleteId, special_parameter);
	}
}// delete function ends here
function challan_delete_special(pageId, deleteId, progress_div,special_parameter)
{
	ajax = gethttprequest_object(); // making an ajax object this will help make multiple asychronous call on one page
	//confirm that the object is usable
	if(ajax)
	{
		// call the php script. use the get method. Pass the username in the url
		ajax.open('get','js/ajax_delete/data_delete_php1.php?pageId='+ encodeURIComponent(pageId)+'&deleteId='+ encodeURIComponent(deleteId)+'&levelType='+ encodeURIComponent(special_parameter));
		
		//Function that handles the response
		//ajax.onreadystatechange = handle_check; // when you dont want to pass argument to call back function
		ajax.onreadystatechange =function () { 
                                    challan_delete_response(progress_div, deleteId);
                                    }
		//send the request
		ajax.send(null);
	}
	else
	{
		//cant use ajax!
		document.getElementById(progress_div).innerHTML = 'The availability of deletion will be confirmed upon form submission.';
	}
}
function challan_delete_response(progress_div, deleteId)
{
	//if everything's OK
	if((ajax.readyState == 4) && (ajax.status == 200))
	{
		//assign the returned value to a document element
	        //alert(ajax.responseText);
		var serverdatac = ajax.responseText.split('<$$>');// <$$> is used as first level of seperator
		//document.getElementById(progress_div).style.display = 'none';
		if(serverdatac[0] == 'TRUE')
		{
			document.getElementById(progress_div).style.color = 'green';
			document.getElementById(progress_div).innerHTML = serverdatac[1];
			alert(serverdatac[1]);
			// deleting the row from the table starts here
			var el = document.getElementById('tr'+deleteId);
			//var totcounter = document.getElementById('totCounter').innerHTML*1 - 1; //decrementing the total entry
			//document.getElementById('totCounter').innerHTML = totcounter;// decrease the total
			el.parentNode.removeChild(el);// removing the tr after sometime
			
		}
		else
		{
			//alert('Sorry no data found. ' + ajax.responseText);
			//alert(serverdatac[1]);
			document.getElementById(progress_div).style.color = 'red';
			document.getElementById(progress_div).innerHTML = serverdatac[1];
			setTimeout("document.getElementById('"+progress_div+"').style.display = 'none';",3000);
			//document.getElementById(progress_div).style.display = 'none';
		}
	}
	else
	{
		document.getElementById(progress_div).style.display = 'block';
		document.getElementById(progress_div).innerHTML = '<img src="images/ajax-loader.gif" /><span style="margin-left:15px; color:red;">deletion in progress ...</span>';
	}
}