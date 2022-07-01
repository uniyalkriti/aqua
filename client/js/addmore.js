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

function addmoreadvance(tableId, event, wcase)
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
		newRow.cells[newRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmoreadvance(\''+tableId+'\', event, \''+wcase+'\');"/><img  title="less" src="images/less.png" onclick="javascript:addmoreadvance(\''+tableId+'\', event, \''+wcase+'\');"/>';
		//to replace the plus sign with - also for the first tr also
		if(currentRow.rowIndex == 1)
		{
			currentRow.cells[currentRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmoreadvance(\''+tableId+'\', event, \''+wcase+'\');"/><img  title="less" src="images/less.png" onclick="javascript:addmoreadvance(\''+tableId+'\', event, \''+wcase+'\');"/>';
		}
		//alert(currentRow.cells.length);
		/*for(var ir = 1; ir<currentRow.cells.length; ir++)
		{
			if(newRow.cells[ir].childNodes[0].type == 'text')
			{
				newRow.cells[ir].childNodes[0].value = '';
			}
		}*/
	}
	else if(mtarget.title == 'less')
	{
		var currentRow = mtarget.parentNode.parentNode;
		if(trows == 3 && currentRow.rowIndex == 1) // checking if this is the last row to be edited, then only + sign should stay.
		{
			currentRow.cells[currentRow.cells.length - 1].innerHTML = '<img title="more" src="images/more.png" onclick="javascript:addmoreadvance(\''+tableId+'\', event, \''+wcase+'\');"/>';
		}
		else
		{
			mtable.deleteRow(currentRow.rowIndex); // delete current row
			// call the desired calculation function
			calculate();
		}
	}
	setsnoadvance(tableId);
	customAlter(mtable, currentRow, newRow, wcase);
}


function customAlter(mtable, currentRow, newRow, wcase)
{
	switch(wcase)
	{
		case'RETAIN':
		{
			if(newRow.cells[3].childNodes[0].type == 'text' || newRow.cells[3].childNodes[0].type == 'textarea')
			{
				newRow.cells[3].childNodes[0].value = '';
			}
			if(newRow.cells[4].childNodes[0].type == 'text' || newRow.cells[4].childNodes[0].type == 'textarea')
			{
				newRow.cells[4].childNodes[0].value = '';
			}
			/*if(newRow.cells[ir].childNodes[0].type == 'select-one')
			{
				newRow.cells[ir].childNodes[0].selectedIndex = currentRow.cells[ir].childNodes[0].selectedIndex;
			//}*/
			break;
		}
		case'SPECIAL':
		{
			for(var ir = 1; ir<currentRow.cells.length; ir++)
			{
				if(newRow.cells[ir].childNodes[0].type == 'text' || newRow.cells[ir].childNodes[0].type == 'textarea')
				{
					newRow.cells[ir].childNodes[0].value = '';
				}
				if(newRow.cells[ir].childNodes[0].type == 'select-one')
				{
					if(currentRow.cells[ir].childNodes[0].selectedIndex != 0) // to not remove please select option
						newRow.cells[ir].childNodes[0].remove(currentRow.cells[ir].childNodes[0].selectedIndex);
					if(newRow.cells[ir].childNodes[0].length == 1)
					{
						mtable.deleteRow(newRow.rowIndex);
						alert('No More Data To Select');
					}
						
					//alert(mtable.rows[mtable.rows.length-3].cells[2].childNodes[0].length);
					
					if(mtable.rows.length-2 ==  mtable.rows[1].cells[2].childNodes[0].length)
					{
						alert('No More Data To Select');
						//if(currentRow.cells[ir].childNodes[0].options[0].value == '')
						mtable.deleteRow(newRow.rowIndex); 
					}
				}
			}
			break;
		}
		case'SPECIAL1':
		{
			for(var ir = 1; ir<currentRow.cells.length; ir++)
			{
				if(newRow.cells[ir].childNodes[0].type == 'text' || newRow.cells[ir].childNodes[0].type == 'textarea')
				{
					newRow.cells[ir].childNodes[0].value = '';
				}
				if(newRow.cells[ir].childNodes[0].type == 'select-one')
				{
					if(currentRow.cells[ir].childNodes[0].selectedIndex != 0) // to not remove please select option
						newRow.cells[ir].childNodes[0].remove(currentRow.cells[ir].childNodes[0].selectedIndex);
					if(newRow.cells[ir].childNodes[0].length == 1)
					{
						mtable.deleteRow(newRow.rowIndex);
						alert('No More Data To Select');
						//alert('1');
					}
						
					//alert(mtable.rows[mtable.rows.length-3].cells[2].childNodes[0].length);
					//alert(mtable.rows.length-2);
					//alert(mtable.rows[1].cells[1].childNodes[0].length);
					if(mtable.rows.length-2 ==  mtable.rows[1].cells[1].childNodes[0].length)
					{
						//if(currentRow.cells[ir].childNodes[0].options[0].value == '')
						mtable.deleteRow(newRow.rowIndex);
						alert('No More Data To Select');
						//alert('2');
					}
				}
			}
			break;
		}
		case'PR':
		{
			for(var ir = 1; ir<currentRow.cells.length; ir++)
			{
				if(newRow.cells[ir].childNodes[0].type == 'text' || newRow.cells[ir].childNodes[0].type == 'textarea')
				{
					newRow.cells[ir].childNodes[0].value = '';
				}
				if(newRow.cells[ir].childNodes[0] == '[object HTMLDivElement]')
				newRow.cells[ir].childNodes[0].style.display = 'none';
				
				if(newRow.cells[ir].childNodes[0].type == 'select-one')
				{
					if(currentRow.cells[ir].childNodes[0].selectedIndex != 0) // to not remove please select option
						newRow.cells[ir].childNodes[0].remove(currentRow.cells[ir].childNodes[0].selectedIndex);
					if(newRow.cells[ir].childNodes[0].length == 1)
					{
						mtable.deleteRow(newRow.rowIndex);
						alert('No More Data To Select');
					}
					//alert(mtable.rows[mtable.rows.length-3].cells[2].childNodes[0].length);
					//alert(mtable.rows.length-2);
					//alert(mtable.rows[1].cells[1].childNodes[0].length);
					if(mtable.rows.length-2 ==  mtable.rows[1].cells[1].childNodes[0].length)
					{
						//if(currentRow.cells[ir].childNodes[0].options[0].value == '')
						mtable.deleteRow(newRow.rowIndex);
						alert('No More Data To Select');
					}
				}
			}
			break;
		}
	}
}

// to set the numbers in the sno column and it is optional
function setsnoadvance(tableId)
{
	var mtable = document.getElementById(tableId);
	for(var i = 1; i<mtable.rows.length-1; i++)
	{
		mtable.rows[i].cells[0].innerHTML = i;
	}
}

function getajaxdataadvance(wcase, tableId, event)
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
		case 'getitemdetails':
		{
			var stock = document.getElementsByName('stock[]');
			var rrate = document.getElementsByName('rrate[]');
			var srate = document.getElementsByName('srate[]');
			var updatetextid = new Array(rrate[currentRow.rowIndex - 1], srate[currentRow.rowIndex - 1], stock[currentRow.rowIndex - 1]);
			getdataObject(mtarget, 'progdiv', 'getitemdetails', updatetextid);
			break;			
		}
	}
}


