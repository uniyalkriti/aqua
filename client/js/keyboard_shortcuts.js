jwerty.key('f1', f1select);
jwerty.key('f2', f2select);
jwerty.key('f3', f3select);

jwerty.key('alt+ctrl+S', alt_ctrl_s_select);
jwerty.key('alt+ctrl+n', alt_ctrl_n_select);

function f1select()
{
	$('#myhome').focus();
	$('#myhome').select();
	return false;
}
function f2select()
{
	if($('#mynew').length > 0)
	{
		$(window).attr('location',$('#mynew').attr('href'));
		return false;
	}
	$('form.iform:eq(0)').find('select,input[type=text]').not('[readonly="readonly"]').eq(0).focus();
	return false;
}
function f3select()
{
	if($('#mylist').length > 0)
	{
		$(window).attr('location',$('#mylist').attr('href'));
		return false;
	}
	if(document.getElementById('searchfilter'))
	{
	  $('#searchfilter').find('select,input[type=text]').not('[readonly="readonly"]').eq(0).focus();
	  return false;
	}
	else return true;
}

function alt_ctrl_s_select()
{
	if(document.getElementById('mysave'))
	{
	  $('#mysave').eq(0).click();
	  return false;
	}
	else return true;
}
function alt_ctrl_n_select()
{
	if(document.getElementById('addnew'))
	{
		$('#addnew').eq(0).click();
		return false;
	}
	else  return true;
}
/*$(document).ready(function(){
	$(document).keypress(function (evt){
		if (evt.keyCode == 112) //F1 key
		{
			$('#myhome').focus();
			$('#myhome').select();
			evt.preventDefault();
		}
		if (evt.keyCode == 113)//f2 key
		{
			$('form.iform:eq(0)').find('select,input[type=text]').not('[readonly="readonly"]').eq(0).focus();
			evt.preventDefault();	
		}
		if (evt.keyCode == 114)//f3 key
		{	
			$('input[value="Filter"]').focus();
			evt.preventDefault();
		}
		if (evt.keyCode == 115)//f4 key
		{	
			$('#itemsearch').click();
			evt.preventDefault();
		}
			
	   });
});*/