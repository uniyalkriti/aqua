<?php
//This function will load the necessary jQueyr files specific to the project
function myjquery($loadjquery = array(), $num=1.8, $status = false)
{
	$defaultload = array('jQuery'=>1,'jQueryUI'=>1,'jwerty'=>1, 'datepicker'=>1, 'chosen'=>1, 'maskedinput'=>1, 'colorbox'=>1,'zoomer'=>1,'phtozoom'=>1);
	if(!empty($loadjquery)){
		foreach($loadjquery as $key=>$value){
			if(isset($defaultload[$key])){
				if($value == 0) unset($defaultload[$key]);
			}//if ends
		}//foreachends
	}//if(!empty($loadjquery)) ends
	$myjquery = '';
	//loading the jquery
	if(isset($defaultload['jQuery'])){
		$myjquery .= '<!-- jQuery library -->'."\n";
		$myjquery .= '<script src="'.BASE_URL_A.'js/jquery/jquery-'.$num.'.min.js"></script>'."\n";
		if($num === '1.9.1')
			$myjquery .= '<script src="'.BASE_URL_A.'js/jquery/jquery-migrate-1.1.1.min.js"></script>'."\n";
	}			
	//loading the jquery
	if(isset($defaultload['jQueryUI'])){
		$myjquery .= '<!-- jQuery ui -->'."\n";
		$myjquery .= '<link rel="stylesheet" href="'.BASE_URL_A.'widgets/jquery-ui-1.10.1/css/ui-lightness/jquery-ui-1.10.1.custom.min.css" />'."\n";
		$myjquery .= '<script src="'.BASE_URL_A.'widgets/jquery-ui-1.10.1/js/jquery-ui-1.10.1.custom.js"></script>'."\n";
	}
	//loading the jwerty library
	if(isset($defaultload['jwerty'])){
		$myjquery .= '<!-- using the jwerty library -->'."\n";
		$myjquery .= '<script type="text/javascript" src="'.BASE_URL_A.'widgets/jwerty/jwerty-0.3.js"></script>'."\n";
		$myjquery .= '<script src="'.BASE_URL_A.'js/keyboard_shortcuts.js"></script>'."\n";
	}
	
	//loading the jquery datepicker library
	if(isset($defaultload['datepicker'])){
		$myjquery .= '<!-- using the datepicker library -->'."\n";
		$myjquery .= '<script type="text/javascript" src="./widgets/jquery-ui-datepicker/jquery-ui-datepicker.js"></script>'."\n";
	}
    
	//loading the chosen library
	if(isset($defaultload['chosen'])){
		$myjquery .= '<!-- using the mychosen search to make pulldown combobox -->'."\n";
		$myjquery .= '<link rel="stylesheet" href="'.BASE_URL_A.'widgets/chosen/chosen.css" />'."\n";
		$myjquery .= '<script src="'.BASE_URL_A.'widgets/chosen/chosen.jquery.js" type="text/javascript"></script>'."\n";
		$myjquery .= '<script src="'.BASE_URL_A.'widgets/chosen/mychosen.js" type="text/javascript"></script>'."\n";
	}
	//loading the maskedinput library
	if(isset($defaultload['maskedinput'])){
		$myjquery .= '<!-- using the maskedinput to capture formatted data -->'."\n";
		$myjquery .= '<script src="'.BASE_URL_A.'widgets/maskedinput/maskedinput-1.3.js" type="text/javascript"></script>'."\n";
		$myjquery .= '<script src="'.BASE_URL_A.'widgets/maskedinput/mymaskedinput.js" type="text/javascript"></script>'."\n";
	}
	//code to make colorbox starts here
	if(isset($defaultload['colorbox'])){
		$myjquery .= '<!-- code to make colorbox starts here -->'."\n";
		$myjquery .= '<link rel="stylesheet" href="'.BASE_URL_A.'widgets/colorbox/colorbox.css" />'."\n";
		$myjquery .= '<script type="text/javascript" src="'.BASE_URL_A.'widgets/colorbox/jquery.colorbox-min.js"></script>'."\n";
		$myjquery .= '<script type="text/javascript" src="'.BASE_URL_A.'widgets/colorbox/mycolorbox.js"></script>'."\n";
	}
	//code to make colorbox starts here
	if(isset($defaultload['zoomer'])){
		$myjquery .= '<!-- Code For Zoomer Starts Here -->'."\n";
		$myjquery .= '<script src="'.BASE_URL_A.'widgets/zoomer/zoomple-1.4.js" type="text/javascript"></script>'."\n";
		$myjquery .= '<script src="'.BASE_URL_A.'widgets/zoomer/zoomple.js" type="text/javascript"></script>'."\n";
	}
	//code to make colorbox starts here
	if(isset($defaultload['phtozoom'])){
		$myjquery .= '<!-- Code For Photo Zoomer Darpan Starts Here -->'."\n";
		$myjquery .= '<script src="'.BASE_URL_A.'widgets/photozoom/photoZoom.min.js" type="text/javascript"></script>'."\n";
		$myjquery .= '<script type="text/javascript" src="'.BASE_URL_A.'widgets/photozoom/myphotoZoom.js"></script>'."\n";
	}
	if($status) return $myjquery;// if user wants to return the code instead of displaying them.
	echo $myjquery;
}
?>