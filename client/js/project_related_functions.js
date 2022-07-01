// JavaScript Document
function get_variation_details(varId)
{
		if(window.XMLHttpRequest)
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
				 //alert('hi');
				 var r1 = xmlhttp.responseText;
				// alert(r1);
				 document.getElementById('var_further').innerHTML = r1;
			 }
		}
		xmlhttp.open("GET",'js/ajax_general/ajax_general_php.php?pid='+ encodeURIComponent(varId)+'&wcase=var_details',true);
		xmlhttp.send();	
}
// JavaScript Document
function checkqty(id)
{
	//alert(id);
	var pqty = document.getElementsByName('p_qty[]');
	var aqty = document.getElementsByName('a_qty[]');
	var act = document.getElementById('app'+id);
	var rej = document.getElementById('rej'+id);
	var rate = document.getElementsByName('rate[]');
	var amount = document.getElementsByName('amt[]');
	if(act.checked || rej.checked)
	{
		var pvalue = pqty[id].value;
		var avalue = aqty[id].value;
		var rvalue = rate[id].value;
		var amtvalue = amount[id].value;
		if(pvalue*1 < avalue*1)
		{
			alert('cannot be greater');
			aqty[id].value = '';
		}
		else if((avalue == '' || avalue == 0) && (act.checked))
		{
			alert('cannot be blank or 0');
			aqty[id].value = '';
		}
		var a = parseFloat(avalue*rvalue);
		amount[id].value = a.toFixed(2);
		var total = document.getElementById('tamt').value;
		var b = parseFloat((total*1 - amtvalue*1 + a));
		document.getElementById('tamt').value = b.toFixed(2);
	}
}

function pocheckqty(id)
{
	//alert(id);
	var pqty = document.getElementsByName('p_qty[]');
	var poqty = document.getElementsByName('po_qty[]');
	var aqty = document.getElementsByName('a_qty[]');
	var pvalue = pqty[id].value;
	var povalue = poqty[id].value;
	if(povalue == '') povalue = 0;
	var avalue = aqty[id].value;
	//alert(pvalue);
	if(pvalue*1 < (avalue*1 + povalue*1))
	{
		alert('cannot be greater');
		aqty[id].value = '';
	}
	else if(avalue == '' || avalue == 0)
	{
		alert('cannot be blank or 0');
		aqty[id].value = '';
	}
}

function conform()
{
	var a = document.getElementById('bop').value;
	//alert(a);
	var answer = confirm('The obtainable bussiness price is:' + a);
	if (answer)
	{
		document.getElementById('hbop').value = a;
	}
	else
	{
		var reply = prompt("Please Enter the price for bussiness!" , "")
		alert(reply);
		if(reply)
		{
			document.getElementById('hbop').value = reply;
		}
		else
		{
			document.getElementById('hbop').value = '';
		}
	}
}

function calacceptedqty(counter)
{
	var g = document.getElementById('gqty'+counter).value;
	var rej = document.getElementById('rqty'+counter).value;
	document.getElementById('accqty'+counter).value = g*1 - rej*1;
}

function display(id)
{
	//alert(id);
	var a = document.getElementById(id).value;
	if(a == '1')
	{
		document.getElementById('show').style.display = 'block';
		document.getElementById('bill_no').setAttribute('lang','BILL NO');
	}
	else
	{
		document.getElementById('show').style.display = 'none';
		document.getElementById('bill_no').removeAttribute('lang');
	}
}

function alert_for_max_limit()
{
	var maxlimit = document .getElementById('maxlimit').value;
	var totamt = document .getElementById('tamt').value;
	if(maxlimit*1 > totamt*1)
	{
		alert('Total Amount of this PR is less than Minimum limit '+ maxlimit);
	}
}