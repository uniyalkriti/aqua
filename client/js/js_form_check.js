// JavaScript Document
// this function will chech whether the field having an alt attribute set in the form have been filled or not, just a basic js check
// this function just need the name of the form to check
function checkForm(formname)
{
	formToProcess = window.document.forms[formname];
	var str = '';
	for(var i = 0; i< formToProcess.length; i++)
	{
		if(formToProcess.elements[i].lang)
		{
			if(str == '')
				var focusIndex = i; // to set focus to first unfilled element
			if(formToProcess.elements[i].value == '')
			{
				str += formToProcess.elements[i].getAttribute('lang')+'\n';
			}
		}
	}
	if(str != '')
	{
		//alert('Please fill the following fields : \n'+str);
		formToProcess.elements[focusIndex].focus();
		return false;
	}
	else
	{
		return true;	
	}
}

// this will be used if the text box already have a default value
function checkFormD(formname)
{
	formToProcess = window.document.forms[formname];
	var str = '';
	for(var i = 0; i< formToProcess.length; i++)
	{
		if(formToProcess.elements[i].lang)
		{
			if(str == '')
				var focusIndex = i; // to set focus to first unfilled element
			if(formToProcess.elements[i].type == 'select-one') // in case of pull down  we go for diff approach
			{
				if(formToProcess.elements[i].value == '')
					str += formToProcess.elements[i].getAttribute('lang')+'\n';
			}
			else if(formToProcess.elements[i].value == formToProcess.elements[i].defaultValue)
			{
				str += formToProcess.elements[i].getAttribute('lang')+'\n';
			}
		}
	}
	if(str != '')
	{
		//alert('Please fill the following fields : \n'+str);
		formToProcess.elements[focusIndex].focus();
		return false;
	}
	else
	{
		return true;	
	}
}

function checkForm_alert(formname)
{
	formToProcess = window.document.forms[formname];
	var str = '';
	for(var i = 0; i< formToProcess.length; i++)
	{
		if(formToProcess.elements[i].lang)
		{
			if(str == '')
				var focusIndex = i; // to set focus to first unfilled element
			if(formToProcess.elements[i].value == '')
			{
				str += formToProcess.elements[i].getAttribute('lang')+'\n';
			}
		}
	}
	if(str != '')
	{
		alert('Please fill the following fields : \n'+str);
		formToProcess.elements[focusIndex].focus();
		return false;
	}
	else
	{
		return true;	
	}
}

// this will be used if the text box already have a default value
function checkFormD_alert(formname)
{
	formToProcess = window.document.forms[formname];
	var str = '';
	for(var i = 0; i< formToProcess.length; i++)
	{
		if(formToProcess.elements[i].lang)
		{
			if(str == '')
				var focusIndex = i; // to set focus to first unfilled element
			if(formToProcess.elements[i].type == 'select-one') // in case of pull down  we go for diff approach
			{
				if(formToProcess.elements[i].value == '')
					str += formToProcess.elements[i].getAttribute('lang')+'\n';
			}
			else if(formToProcess.elements[i].value == formToProcess.elements[i].defaultValue)
			{
				str += formToProcess.elements[i].getAttribute('lang')+'\n';
			}
		}
	}
	if(str != '')
	{
		alert('Please fill the following fields : \n'+str);
		formToProcess.elements[focusIndex].focus();
		return false;
	}
	else
	{
		return true;	
	}
}