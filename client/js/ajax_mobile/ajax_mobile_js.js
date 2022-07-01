// JavaScript Document
//function that start the Ajax process
function getdataObject_div(mtarget, progress_div, wcase, updatetextid)
{
	ajax = gethttprequest_object(); // making an ajax object this will help make multiple asychronous call on one page
	//confirm that the object is usable
	//alert(updatetextid);
	//alert(wcase);
	//alert(mtarget);
	//alert(progress_div);
	if(ajax)
	{
		// call the php script. use the get method. Pass the username in the url
		ajax.open('get','js/ajax_general/ajax_general_div_php.php?pid='+ encodeURIComponent(mtarget)+'&wcase='+wcase);
		
		//Function that handles the response
		//ajax.onreadystatechange = handle_check; // when you dont want to pass argument to call back function
		ajax.onreadystatechange =function () { 
                                    resp_getdataObject_div(progress_div, updatetextid);
                                    }
		//send the request
		ajax.send(null);
	}
	else
	{
		//cant use ajax!
		document.getElementById(progress_div).innerHTML = 'The availability of location will be confirmed upon form submission.';
	}
}

function resp_getdataObject_div(progress_div, updatetextid)
{
	//if everything's OK
	if((ajax.readyState == 4) && (ajax.status == 200))
	{
		//alert(ajax.responseText);
		var datafetch = ajax.responseText.split('<$>');
		if(datafetch[0] == 'TRUE' )
		{	
			var plelement = datafetch[1].split('<$$>');
			//alert(updatetextid);
			for(var i = 0; i<updatetextid.length; i++)
			{
				updatetextid[i].innerHTML = plelement[i];
			}			
		}
		else
		{
			alert(datafetch[1]);
		}
		document.getElementById(progress_div).style.display = 'none';
	}
	else
	{
		document.getElementById(progress_div).style.display = 'block';
		document.getElementById(progress_div).innerHTML = '<img src="images/loader.gif" />fetching data ...';
	}
}// End of handle_check() function


function getdata_div(selectvalue, progress_div, wcase, updatetextid)
{
	ajax = gethttprequest_object(); // making an ajax object this will help make multiple asychronous call on one page
	//confirm that the object is usable
	//alert(selectvalue);
//	alert(progress_div);
//	alert(wcase);
//	alert(updatetextid);
	if(ajax)
	{
		// call the php script. use the get method. Pass the username in the url
		ajax.open('get','js/ajax_general/ajax_general_div_php.php?pid='+ encodeURIComponent(selectvalue)+'&wcase='+wcase);
		
		//Function that handles the response
		//ajax.onreadystatechange = handle_check; // when you dont want to pass argument to call back function
		ajax.onreadystatechange =function () { 
                                    resp_getdata_div(progress_div, updatetextid);
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
function resp_getdata_div(progress_div, updatetextid)
{
	//if everything's OK
	if((ajax.readyState == 4) && (ajax.status == 200))
	{
		//alert(ajax.responseText);
		var datafetch = ajax.responseText.split('<$>');
		if(datafetch[0] == 'TRUE' )
		{
			var plelement = datafetch[1].split('<$$>');	
			var ids = updatetextid.split('<$>');
			for(var i = 0; i<ids.length; i++)
			{
				document.getElementById(ids[i]).innerHTML = plelement[i];
			}			
		}
		else
		{
			alert(datafetch[1]);
			var ids = updatetextid.split('<$>');
			for(var i = 0; i<ids.length; i++)
			{
				document.getElementById(ids[i]).innerHTML = '';
			}			
		}
		document.getElementById(progress_div).style.display = 'none';
	}
	else
	{
		document.getElementById(progress_div).style.display = 'block';
		document.getElementById(progress_div).innerHTML = '<img src="images/ajax-loader.gif" />fetching data ...';
	}
}// End of handle_check() function

function ajax_verify(selectvalue, progress_div, wcase, updatetextid)
{
	ajax = gethttprequest_object(); // making an ajax object this will help make multiple asychronous call on one page
	//confirm that the object is usable
	if(ajax)
	{
		// call the php script. use the get method. Pass the username in the url
		ajax.open('get','js/ajax_general/ajax_general_div_php.php?pid='+ encodeURIComponent(selectvalue)+'&wcase='+wcase);
		
		//Function that handles the response
		//ajax.onreadystatechange = handle_check; // when you dont want to pass argument to call back function
		ajax.onreadystatechange =function () { 
                                    resp_ajax_verify(progress_div, updatetextid);
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
function resp_ajax_verify(progress_div, updatetextid)
{
	//if everything's OK
	if((ajax.readyState == 4) && (ajax.status == 200))
	{
		//alert(ajax.responseText);
		var datafetch = ajax.responseText.split('<$>');
		if(datafetch[0] == 'TRUE' )
		{			
			document.getElementById(progress_div).style.color = 'green';
			document.getElementById(progress_div).innerHTML = 	datafetch[1];
			
		}
		else
		{
			//alert(datafetch[1]);			
			//document.getElementById(updatetextid).value = '';
			document.getElementById(progress_div).style.color = 'red';
			document.getElementById(progress_div).innerHTML = 	datafetch[1];
		}
	}
	else
	{
		document.getElementById(progress_div).style.display = 'block';
		document.getElementById(progress_div).style.color = 'red';
		document.getElementById(progress_div).innerHTML = '<img src="images/ajax-loader.gif" />checking ...';
	}
}// End of handle_check() function