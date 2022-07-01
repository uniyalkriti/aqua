// JavaScript Document
/*
* Name:jsfunctions.js
* Author:Kumar Chetan
* Description:Contains various javascript functions.
*/
var debugme = 0;
/*
* Error handling
*/
function handleError(str,page,line,chr) {
	if(debugme==1)alert('OUCH!!\n Error Occured!\nPage: '+page +'\n Line no: '+line+'\n Character: ' +chr);
	return true;
}

window.onerror = handleError

/*
* Simply Checks the boxes
*/
function CheckAll(form2, boxname)
{
	l = eval('document.'+form2+'.elements.length;');
	for (var i=0;i<l;i++)
	{
		var e = eval('document.'+form2+'.elements['+i+'];');
		if ((e.name == boxname ) && (e.type=='checkbox'))e.checked = true;
	}
}

/*
* Simply UnChecks the boxes
*/

function UnCheckAll(form2, boxname)
{
	l = eval('document.'+form2+'.elements.length;');
	for (var i=0;i<l;i++)
	{
		var e = eval('document.'+form2+'.elements['+i+'];');
		if ((e.name == boxname ) && (e.type=='checkbox'))e.checked = false;
	}
}


function isdate(date,seperator)
{
	var flag = true;
	if(seperator == '')
	separator = '/';
	var data = date.split(separator);
	
}

function istime(time,seperator)
{
	var flag = true;
	if(seperator == '')
	separator = ':';
	var data = date.split(separator);
	if(data.length < 2)
	{
		flag = false;
	}
	else
	{
		if(data[0] > 23 || data[0] < 00)
		{
			flag = false;
		}
		if(data[1] > 59 || data[1]< 00)
		{
			flag = false;
		}
		if(data[2] && data[2] > 59 || data[2]< 00)
		{
			flag = false;
		}
	}
	return flag;
}


/*
* Simple. It will send u to a 'url' on event.
*/
function go2url(url)
{
	window.location = url;
}



/*
* Will check for null Values in the array of textboxes.
*/
function chkfrm(form2, boxname)
{
	var flag = 0;
	var l = eval('document.'+form2+'.elements.length;');
	for (var i=0;i<l;i++)
	{
		var e = eval('document.'+form2+'.elements['+i+'];');
		if ((e.name == boxname ) && (e.value=='')){alert('Null value not allowed!');e.focus(); flag = 1;};
		if (flag==1) {return (false); break;}
	}
	if(flag==0){ return (true);	}
}


var fieldcounter = 0;

function moreFields(whichfield,wheretoadd)
{
	fieldcounter++;
	newFields = document.getElementById(whichfield).cloneNode(true);
	newFields.id = '';
	newFields.style.display = 'block';
	var newField = newFields.childNodes;
	for (var i=0;i<newField.length;i++)
	{
		var theName = newField[i].name
		if (theName)
			newField[i].name = theName + fieldcounter;
	}
	var insertHere = document.getElementById(wheretoadd);
	insertHere.parentNode.insertBefore(newFields,insertHere);
}


/*
* Populates another Select box. U can say chained selects.
*/
function replace_values(frm, triggefield, triggerval, fieldname,newstrs, newvalues)
{
  v = eval('document.'+frm+'.'+triggefield+'.options[document.'+frm+'.'+triggefield+'.selectedIndex].value');
  if (v!='' && v == triggerval)
  {
    var new_arr_length = eval(newstrs+'['+triggerval+'].length');
    ex_len = eval('document.'+frm+'.'+fieldname+'.length');
    for(k=(ex_len - 1); k > 0; k--)
    {
      eval('document.'+frm+'.'+fieldname+'.options['+k+'] = null;');
    }
    for(i=0;i<new_arr_length;i++)
    {
      eval('option0 = new Option('+newvalues+'['+triggerval+'][i], '+newstrs+'['+triggerval+'][i]);');
      eval('my_var = document.'+frm+'.'+fieldname);
      my_var.options[my_var.length] = option0;
    }
  }
}


/*
* This function will check for valid email
*/
function isValidEmail(field){
	var re = /^[a-z0-9]([a-z0-9_\-\.]*)@([a-z0-9_\-\.]*)(\.[a-z]{2,4}(\.[a-z]{2}){0,2})$/i;
	return re.test(field.value);
}

/*
* This function will check for albhabets only
*/
function isAlphabet(field) {
	var alph_exp = "([a-zA-Z])$";
	var valid_alpha = new RegExp(alph_exp);
	if(!valid_alpha.test(field.value)) {
		return true;
	}
	return false;
}

/*
* This function will check for numbers only
*/
function isNumber(field) {
	var num_exp = "([0-9])$";
	var valid_num = new RegExp(num_exp);
	if(!valid_num.test(field.value)) {
		return true;
	}
	return false;
}

/*
* This function will check for empty input boxes
*/
function isEmpty(val){
	var re = /^\s*$/;
	return re.test(val.value);
}

function dontHaveRights() {
	alert("You dont have permissions to do this action.\n Contact to administrator for permissions.");
	return false;
}

