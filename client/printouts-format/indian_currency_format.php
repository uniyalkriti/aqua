<?php
/***********************************************
* Script Name : NumToWords *
* Scripted By : Deepak Tokas *
* Email : deepak@weboseo.com *

This file contains four functions 

1. convert_number($num)
	- This function will convert the given number in words (In million format)
	For Ex: 458625 can be Four Hundred and Fifty-Eight Thousand Six Hundred and Twenty-Five Only
	
2. numtowords($num)
	- function similar to convert_number() above one but not tested
	
3. formatInIndianStyle($num) 
	- This function will format the given number in Indian currency format
	For Ex: 458625 will become 4,58,625
	
3. formatindianstyle($num) 
	- function similar to formatInIndianStyle() above one but with less lines of code
	For Ex: 458625 will become 4,58,625
	
formatindianstyle
***********************************************/
function convert_number($number) 
{ 
    if (($number < 0) || ($number > 999999999)) 
    { 
    throw new Exception("Number is out of range");
    } 

    $Gn = floor($number / 1000000);  /* Millions (giga) */ 
    $number -= $Gn * 1000000; 
    $kn = floor($number / 1000);     /* Thousands (kilo) */ 
    $number -= $kn * 1000; 
    $Hn = floor($number / 100);      /* Hundreds (hecto) */ 
    $number -= $Hn * 100; 
    $Dn = floor($number / 10);       /* Tens (deca) */ 
    $n = $number % 10;               /* Ones */ 

    $res = ""; 

    if ($Gn) 
    { 
        $res .= convert_number($Gn) . " Million"; 
    } 

    if ($kn) 
    { 
        $res .= (empty($res) ? "" : " ") . 
            convert_number($kn) . " Thousand"; 
    } 

    if ($Hn) 
    { 
        $res .= (empty($res) ? "" : " ") . 
            convert_number($Hn) . " Hundred"; 
    } 

    $ones = array("", "One", "Two", "Three", "Four", "Five", "Six", 
        "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", 
        "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", 
        "Nineteen"); 
    $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", 
        "Seventy", "Eigthy", "Ninety"); 

    if ($Dn || $n) 
    { 
        if (!empty($res)) 
        { 
            $res .= " and "; 
        } 

        if ($Dn < 2) 
        { 
            $res .= $ones[$Dn * 10 + $n]; 
        } 
        else 
        { 
            $res .= $tens[$Dn]; 

            if ($n) 
            { 
                $res .= "-" . $ones[$n]; 
            } 
        } 
    } 

    if (empty($res)) 
    { 
        $res = "zero"; 
    } 

    return $res; 
} 

/*//usage of the function
$cheque_amt = 7431 ; 
try
    {
    echo convert_number($cheque_amt). ' Only';
    }
catch(Exception $e)
    {
    echo $e->getMessage();
    }
*/
##################################### function similar to convert_number() above one but not tested #######
function numtowords($num){
$ones = array(
1 => "one",
2 => "two",
3 => "three",
4 => "four",
5 => "five",
6 => "six",
7 => "seven",
8 => "eight",
9 => "nine",
10 => "ten",
11 => "eleven",
12 => "twelve",
13 => "thirteen",
14 => "fourteen",
15 => "fifteen",
16 => "sixteen",
17 => "seventeen",
18 => "eighteen",
19 => "nineteen"
);
$tens = array(
2 => "twenty",
3 => "thirty",
4 => "forty",
5 => "fifty",
6 => "sixty",
7 => "seventy",
8 => "eighty",
9 => "ninety"
);
$hundreds = array(
"hundred",
"thousand",
"million",
"billion",
"trillion",
"quadrillion"
); //limit t quadrillion
$num = number_format($num,2,".",",");
$num_arr = explode(".",$num);
$wholenum = $num_arr[0];
$decnum = $num_arr[1];
$whole_arr = array_reverse(explode(",",$wholenum));
krsort($whole_arr);
$rettxt = "";
foreach($whole_arr as $key => $i){
if($i < 20){
$rettxt .= $ones[$i];
}elseif($i < 100){
$rettxt .= $tens[substr($i,0,1)];
$rettxt .= " ".$ones[substr($i,1,1)];
}else{
$rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0];
$rettxt .= " ".$tens[substr($i,1,1)];
$rettxt .= " ".$ones[substr($i,2,1)];
}
if($key > 0){
$rettxt .= " ".$hundreds[$key]." ";
}
}
if($decnum > 0){
$rettxt .= " and ";
if($decnum < 20){
$rettxt .= $ones[$decnum];
}elseif($decnum < 100){
$rettxt .= $tens[substr($decnum,0,1)];
$rettxt .= " ".$ones[substr($decnum,1,1)];
}
}
return $rettxt;
}


#################################### function to make a number format in indian style #######################
// This function will format the given number in Indian currency format
// For Ex;- 546898 will be given as 5,46,898
function formatInIndianStyle($num){
 $pos = strpos((string)$num, ".");
 if ($pos === false) {
 $decimalpart="00";
 }
 if (!($pos === false)) {
 $decimalpart= substr($num, $pos+1, 2); $num = substr($num,0,$pos);
 }

 if(strlen($num)>3 & strlen($num) <= 12){
 $last3digits = substr($num, -3 );
 $numexceptlastdigits = substr($num, 0, -3 );
 $formatted = makeComma($numexceptlastdigits);
 $stringtoreturn = $formatted.",".$last3digits.".".$decimalpart ;
 }elseif(strlen($num)<=3){
 $stringtoreturn = $num.".".$decimalpart ;
 }elseif(strlen($num)>12){
 $stringtoreturn = number_format($num, 2);
 }

 if(substr($stringtoreturn,0,2)=="-,"){
 $stringtoreturn = "-".substr($stringtoreturn,2 );
 }

 return $stringtoreturn;
 }

 function makeComma($input){
 // This function is written by some anonymous person - I got it from Google
 if(strlen($input)<=2)
 { return $input; }
 $length=substr($input,0,strlen($input)-2);
 $formatted_input = makeComma($length).",".substr($input,-2);
 return $formatted_input;
 }
 
################################### another function for formating currency in indian style ##############
function formatindianstyle($num)
{
	//$n = "1234567.89";
	$n = $num;
	list($n,$dec) = explode('.',$n);//echo "<br />";
	$len = strlen($n); //lenght of the no
	$num = substr($n,$len-3,3); //get the last 3 digits
	$n = floor($n/1000); //omit the last 3 digits already stored in $num
	while($n > 0) //loop the process - further get digits 2 by 2
	{
		$len = strlen($n);
		$num = substr($n,$len-2,2).",".$num;
		$n = floor($n/100);
	}
	$formatedOutput = $num.".".$dec;
	return $formatedOutput;
}
?> 