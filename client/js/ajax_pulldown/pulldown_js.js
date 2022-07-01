// JavaScript Document
//function that start the Ajax process
function fetch_location(pullId, progress_div, nextplId, wcase, func_after_add)
{
	//alert(pullId);
        //alert(wcase);
	ajax = gethttprequest_object(); // making an ajax object this will help make multiple asychronous call on one page
	//confirm that the object is usable
	if(ajax)
	{
            //alert('js/ajax_pulldown/pulldown_php.php?pid='+ encodeURIComponent(pullId)+'&wcase='+wcase);
		// call the php script. use the get method. Pass the username in the url
		ajax.open('get','js/ajax_pulldown/pulldown_php.php?pid='+ encodeURIComponent(pullId)+'&wcase='+wcase);
		
		//Function that handles the response
		//ajax.onreadystatechange = handle_check; // when you dont want to pass argument to call back function
		ajax.onreadystatechange =function () { 
                resp_fetch_location(progress_div, nextplId, pullId, func_after_add);
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
function resp_fetch_location(progress_div, nextplId, pullId, func_after_add)
{
        //alert(nextplId);
	//document.write('abcdeefdf');
	//if everything's OK
	if((ajax.readyState == 4) && (ajax.status == 200))
	{
		//alert(ajax.responseText);
		var datafetch = ajax.responseText.split('<$>');
		if(datafetch[0] == 'TRUE' )
		{			
			var plelement = datafetch[1].split('<$$>');			
			var text = new Array();
			var value = new Array();
			//getting the pulldown values and text in an array
			for(var i = 0; i<plelement.length; i++)
			{
				var values = plelement[i].split('<$$$>');
				text[i] = values[1];
				value[i] = values[0];
                                
			}
                       
			addOption(nextplId, text, value);
		}
		else
		{
			alert(datafetch[1]);
			clearOption(nextplId);
		}
		if(document.getElementById(progress_div))
			document.getElementById(progress_div).style.display = 'none';
		//calling the func to be called after this
		if(jQuery.isFunction(func_after_add)) 
			func_after_add(pullId, datafetch);
	}
	else
	{
		if(document.getElementById(progress_div)){
			document.getElementById(progress_div).style.display = 'block';
			document.getElementById(progress_div).innerHTML = '<img src="images/loader.gif" />fetching data ...';
		}
	}
}// End of handle_check() function
function fetch_location_all(pullId, progress_div, nextplId, wcase, func_after_add)
{
        //alert(pullId);
	ajax = gethttprequest_object(); // making an ajax object this will help make multiple asychronous call on one page
	//confirm that the object is usable
	if(ajax)
	{
            //alert('js/ajax_pulldown/pulldown_php.php?pid='+ encodeURIComponent(pullId)+'&wcase='+wcase);
		// call the php script. use the get method. Pass the username in the url
		ajax.open('get','js/ajax_pulldown/pulldown_php.php?pid='+ encodeURIComponent(pullId)+'&wcase='+wcase);

		//Function that handles the response
		//ajax.onreadystatechange = handle_check; // when you dont want to pass argument to call back function
		ajax.onreadystatechange =function () { 
                                    resp_fetch_location(progress_div, nextplId, pullId, func_after_add);
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
function addOption(sboxId,textopt,valueopt)
{
       
	clearOption(sboxId);
	for(var i = 0; i<textopt.length; i++)
	{
		var optn = document.createElement("OPTION");
		optn.text = textopt[i];
		optn.value = valueopt[i];
                //alert(jQuery.type(sboxId));
		if(jQuery.type(sboxId) === 'object')
			sboxId.options.add(optn);
		else if(jQuery.type(sboxId) === 'string')
			document.getElementById(sboxId).options.add(optn);
//                else if(jQuery.type(sboxId) === 'array')
//                   document.getElementById(sboxId).options.add(optn);
	}
}

function clearOption(id)
{
	if(jQuery.type(id) === 'object')
		id.options.length = 0;
	else if(jQuery.type(id) === 'string')
		document.getElementById(id).options.length = 0;
}
//This function will refresh an pulldown via ajax method, so that option in pulldown can be added dynamically from any page
function ajax_refresher(opullId, wcase, dependency)
{
	
	ajax = gethttprequest_object(); // making an ajax object this will help make multiple asychronous call on one page
	//confirm that the object is usable
	if(ajax)
	{
		// call the php script. use the get method. Pass the username in the url
		var pullId = '1';
		if(dependency != '') // indicates that the pulldown depends upon the parent reference value
		{
			if(parent.document.getElementById(dependency)){
				var dpull = parent.document.getElementById(dependency);				
				pullId = dpull.options[dpull.selectedIndex].value;
				//alert(pullId);
				if(pullId == '') return;
			}
			else{
					//alert('Dependency value missing');
					return;
			}				
		}
		ajax.open('get','js/ajax_pulldown/pulldown_php.php?pid='+ encodeURIComponent(pullId)+'&wcase='+wcase);
		
		//Function that handles the response
		//ajax.onreadystatechange = handle_check; // when you dont want to pass argument to call back function
		ajax.onreadystatechange =function () { 
                                    resp_ajax_refresher(opullId);
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

function resp_ajax_refresher(opullId)
{
	//document.write('abcdeefdf');
	//if everything's OK
	if((ajax.readyState == 4) && (ajax.status == 200))
	{
		//alert(ajax.responseText);
		
		var datafetch = ajax.responseText.split('<$>');
		if(datafetch[0] == 'TRUE' )
		{			
			parent.document.getElementById(opullId).options.length = 0;
			var plelement = datafetch[1].split('<$$>');			
			var text = new Array();
			var value = new Array();
			//getting the pulldown values and text in an array
			for(var i = 0; i<plelement.length; i++)
			{
				var values = plelement[i].split('<$$$>');
				text[i] = values[1];
				value[i] = values[0];
			}
			addOption(parent.document.getElementById(opullId), text, value);
			//parent.$.fn.colorbox.close();
		}
	}
}// End of handle_check() function// end of check_username() function