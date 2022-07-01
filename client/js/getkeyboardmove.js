// This function will give the keyboard moves in a form on upkey, down, left, right, pagup, pagedown keys
/* 	Author - Deepak Tokas
   	Dated - 7-Aug-2012
	This function need the 4 parameteres
	1. event - The default event keyword
	2. row - The current row number of the input box with id like grade1, here 1 is our required number
	3. totrow - The total number of rows in our form in which we are required to make the moves
	4. idarray - An array containing the id of the fields in order in which we need to give the keyboard moves
*/

function getkeyboardmove(event, row, totrow, idarray)
{
	//alert('hi'); return;
	var charCode = (event.which) ? event.which : event.keyCode
	// assigning the array starts here
	//var inpid = new Array("emp_code","account","pf", "esi", "basic", "grade", "da", "hra", "con", "sa", "cca", "pa", "aa", "ai", "pf_per", "vpf_per", "esi_per");
	var inpid = new Array();
	inpid = idarray;
	// assigning the array ends here
	
	var curid = event.target.id; // The id of the textbox on which the key was pressed	
	var cleanid = curid.replace(row,''); // The clean id of the textbox after the removal of the number from it.
	//alert(charCode);
	var finalMoveId = '';
	// for the Home key with keycode 36
	if(charCode == 36)
	{
		if(document.getElementById(inpid[0]+1)) 
		{
			$("html, body").animate({ scrollTop: 0 }, "slow"); // to move the scrollbar to top
			document.getElementById(inpid[0]+1).focus(); // take the focus to the first text box
			finalMoveId = inpid[0]+1;
		}
	}
	// for the End key with keycode 36
	if(charCode == 35)
	{
		if(document.getElementById(inpid[(inpid.length - 1)]+totrow)) 
		{
			window.scrollTo(0, document.body.scrollHeight);
			document.getElementById(inpid[(inpid.length - 1)]+totrow).focus(); // take the focus to the first text box
			finalMoveId = inpid[(inpid.length - 1)]+totrow;
		}
	}
	// for the pageup key with keycode 33
	if(charCode == 33)
	{
		var maxmove = row - 1;
		if(maxmove > 10)
		{
			if(document.getElementById(cleanid+(row*1-10)))
			{
				document.getElementById(cleanid+(row*1-10)).focus();
				finalMoveId = cleanid+(row*1-10);
			}
		}
		else
		{
			$("html, body").animate({ scrollTop: 0 }, "slow"); // to move the scrollbar to top
			document.getElementById(cleanid+1).focus();
			finalMoveId = cleanid+1;
		}
	}
	// for the pagedown key with keycode 34
	if(charCode == 34)
	{
		var maxmove = totrow - row;
		if(maxmove > 10)
		{
			if(document.getElementById(cleanid+(row*1+10)))
			{
				document.getElementById(cleanid+(row*1+10)).focus();
				finalMoveId = cleanid+(row*1+10);
			}
		}
		else
		{
			//to move the scrollbar to bottom
			window.scrollTo(0, document.body.scrollHeight);
			document.getElementById(cleanid+totrow).focus();
			finalMoveId = cleanid+totrow;
		}
	}
	// for the Enter key with keycode 13
	if(charCode == 13) // when the enter key is pressed on the text box not on the submit button
	{
		if(document.getElementById(cleanid+(row*1+1)))
		{
			document.getElementById(cleanid+(row*1+1)).focus();
			finalMoveId = cleanid+(row*1+1);
		}
	}
	// for the Down key with keycode 40
	if(charCode == 40)
	{
		if(document.getElementById(cleanid+(row*1+1)))
		{
			document.getElementById(cleanid+(row*1+1)).focus();
			finalMoveId = cleanid+(row*1+1);
		}
		else if(document.getElementById(cleanid+1))
		{
			document.getElementById(cleanid+1).focus();
			finalMoveId = cleanid+1;
		}
	}
	// for the up key with keycode 38
	if(charCode == 38)
	{
		if(document.getElementById(cleanid+(row*1-1)))
		{
			if(row < 10) // to move the scroller to the top
				$("html, body").animate({ scrollTop: 0 }, "slow"); // to move the scrollbar to top
			document.getElementById(cleanid+(row*1-1)).focus();
			finalMoveId = cleanid+(row*1-1);
			
		}
		else if(document.getElementById(cleanid+totrow))
		{
			document.getElementById(cleanid+totrow).focus();
			finalMoveId = cleanid+totrow;
		}
	}
	// for the right key with keycode 39
	if(charCode == 39)
	{
		for(var i=0; i<inpid.length; i++) 
		{
        	if (inpid[i] == cleanid) break;
		}
		if(i == 0 || i < (inpid.length-1)) // if we are at the first text box then we should move to the next text box
		{
			if(document.getElementById(inpid[i+1]+(row*1))) 
			{
				document.getElementById(inpid[i+1]+(row*1)).focus();
				finalMoveId = inpid[i+1]+(row*1);
			}
		}
		else
		{
			if(row == totrow) // if we are the last textbox,move the user again back to the first text box
			{
				if(document.getElementById(inpid[0]+1)) 
				{
					document.getElementById(inpid[0]+1).focus();
					finalMoveId = inpid[0]+1;
				}
			}
			else
			{
				if(document.getElementById(inpid[0]+(row*1+1)))
				{
					document.getElementById(inpid[0]+(row*1+1)).focus(); // move the user to the first textbox of next row
					finalMoveId = inpid[0]+(row*1+1);
				}
			}
		}
    }
	// for the left key with keycode 37
	if(charCode == 37)
	{
		for(var i=0; i<inpid.length; i++) 
		{
        	if (inpid[i] == cleanid) break;
		}
		if((i >= 1 || i <= (inpid.length-1)) && i !=0) // if we are at the last text box then we should move to the prev text box
		{
			if(document.getElementById(inpid[i-1]+(row*1))) 
			{
				document.getElementById(inpid[i-1]+(row*1)).focus();	
				finalMoveId = inpid[i-1]+(row*1);
			}
		}
		else
		{
			if(i == 0) // if we are the first textbox,move the user again back to the last text box of prev row
			{
				
				if(document.getElementById(inpid[inpid.length-1]+(row*1 - 1))) // if there is no prev row
				{
					document.getElementById(inpid[inpid.length-1]+(row*1 - 1)).focus();
					finalMoveId = inpid[inpid.length-1]+(row*1 - 1);
				}
				else if(document.getElementById(inpid[inpid.length-1]+totrow))  // move the cursor to last box of last row
				{
					document.getElementById(inpid[inpid.length-1]+totrow).focus();
					finalMoveId = inpid[inpid.length-1]+totrow;
				}
			}
		}
    }
}