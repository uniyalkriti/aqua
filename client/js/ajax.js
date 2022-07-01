// JavaScript Document
//initialize the object
//var ajax = false;
function gethttprequest_object()
{
	//choose object type based upon what is supported
	if(window.XMLHttpRequest)
	{
		// IE7, Mozilla, Safari, firefox, opera, most browsers
		return new XMLHttpRequest();
	}
	else if(window.ActiveXObject)
	{
		//older IE browsers
		// create Msxml2.XMLHTTP, if possible
		try
		{
			return new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(e1)
		{
			//create the older type version
			try
			{
				return new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e2){}		
		}
	}
}