<?php
function fileupload($name, $uploadpath, $allowtype ='', $maxsize = 52428800, $mandatory = false)
{
	//global SYM;
	$file_success = false;
	// code to make the final upload folder and path to it, which is stored in $path variable
	$path = $uploadpath; // where the uploaded file will be uploaded
	if(!is_dir($path)) mkdir($path,0777);
	$path .= SYM;
	// code to make the final upload folder and path to it, which is stored in $path variable ends here

	date_default_timezone_set('Asia/Kolkata'); // to make timestamp appended to the image
	$fileName = $_FILES[$name]['name'];
	$ext = substr($fileName, strrpos($fileName, '.') + 1); // getting the info about the image to get its extension
	//$file = $path.mktime().'_'.$memname.'.'.$ext;
	$file = $path.date('YmdHis').'.'.$ext;
       
	$max = number_format($maxsize/1024/1024,1).'MB';
       
	//$permitted = array('image/jpeg','image/png','image/gif');
	$sizeOK = false;
	$typeOK = false;
	if($_FILES[$name]['size'] > 0 && $_FILES[$name]['size'] <= $maxsize)
		$sizeOK = true;
	if(!empty($allowtype))
	{
		$permitted = $allowtype;
		foreach($permitted as $type)
		{
			if($type == $_FILES[$name]['type'])
			{
				$typeOK = true;
				break;
			}
		}
	}
	else
		$typeOK = true;
		
	if($sizeOK && $typeOK)
	{
               
		switch($_FILES[$name]['error'])
		{
			case 0:
			{
				$showform = false;
				$success = move_uploaded_file($_FILES[$name]['tmp_name'],$file);
				if($success)
				{
					//include('include/conectdb.php');
					$pdflink = basename($file); // the name of file which will be stored in the database
                                       
					$file_success = true;
				}
				break;
			}
			case 3:
			{
				$pdflink = '<h3>Error uploading '.$_FILES[$name]['name'].'. Please try again.</h3>';
				break;
			}
			case 1:
			{
				$pdflink = '<h3>Uploading failed as POST MAX SIZE limit exceeded.</h3>';
				break;
			}
			default:
			{
				$pdflink = '<h3>System error uploading '.$_FILES[$name]['name'].'. Contact webmaster.</h3>';
			}
		}
	}
	else if($_FILES[$name]['error'] == 4)
	{
		
		if($mandatory) 
			$pdflink = '<span class="warn">No file selected</span>';
		else
		{
			$pdflink = '';
			$file_success = true;
		}
	}
	else
	{
		$pdflink = '<span class="warn"><b>'.$_FILES[$name]['name'].'</b> cannot be uploaded.<br/> Maximum size allowed: '.$max.' | Accetable file types: JPEG, PNG, GIF only.</span>';
	}
	return array($file_success, $pdflink);
}

function fileuploadmulti($name, $number, $uploadpath, $allowtype ='', $maxsize = 52428800, $mandatory = false)
{
	//global SYM;
	$file_success = false;
	// code to make the final upload folder and path to it, which is stored in $path variable
	$path = $uploadpath; // where the uploaded file will be uploaded
	if(!is_dir($path)) mkdir($path,0777);
	$path .= SYM;
	// code to make the final upload folder and path to it, which is stored in $path variable ends here

	date_default_timezone_set('Asia/Kolkata'); // to make timestamp appended to the image
	$fileName = $_FILES[$name]['name'][$number];
	$ext = substr($fileName, strrpos($fileName, '.') + 1); // getting the info about the image to get its extension
	//$file = $path.mktime().'_'.$memname.'.'.$ext;
	$file = $path.mktime().'.'.$ext;
	$max = number_format($maxsize/1024/1024,1).'MB';
	//$permitted = array('image/jpeg','image/png','image/gif');
	$sizeOK = false;
	$typeOK = false;
	if($_FILES[$name]['size'][$number] > 0 && $_FILES[$name]['size'][$number] <= $maxsize)
		$sizeOK = true;
	if(!empty($allowtype))
	{
		foreach($permitted as $type)
		{
			if($type == $_FILES[$name]['type'][$number])
			{
				$typeOK = true;
				break;
			}
		}
	}
	else
		$typeOK = true;
		
	if($sizeOK && $typeOK)
	{
		switch($_FILES[$name]['error'][$number])
		{
			case 0:
			{
				$showform = false;
				$success = move_uploaded_file($_FILES[$name]['tmp_name'][$number],$file);
				if($success)
				{
					//include('include/conectdb.php');
					$pdflink = basename($file); // the name of file which will be stored in the database
					$file_success = true;
				}
				break;
			}
			case 3:
			{
				$pdflink = '<h3>Error uploading '.$_FILES[$name]['name'][$number].'. Please try again.</h3>';
				break;
			}
			case 1:
			{
				$pdflink = '<h3>Uploading failed as POST MAX SIZE limit exceeded.</h3>';
				break;
			}
			default:
			{
				$pdflink = '<h3>System error uploading '.$_FILES[$name]['name'][$number].'. Contact webmaster.</h3>';
			}
		}
	}
	else if($_FILES[$name]['error'][$number] == 4)
	{
		
		if($mandatory) 
			$pdflink = '<span class="warn">No file selected</span>';
		else
		{
			$pdflink = '';
			$file_success = true;
		}
	}
	else
	{
		$pdflink = '<span class="warn"><b>'.$_FILES[$name]['name'][$number].'</b> cannot be uploaded.<br/> Maximum size allowed: '.$max.' | Accetable file types: JPEG, PNG, GIF only.</span>';
	}
	return array($file_success, $pdflink);
}

function resizeimage($imgname, $path, $newwidth, $thumbnailwidth, $sym, $thumbnail = false)
{
	$image = $imgname;
	$uploadedfile = $path.$sym.$imgname;
	if(!is_dir($path)) mkdir($path,0777); // to setup the directory to store the resized images
	if ($image) 
	{
		$filename = stripslashes($imgname);
		$extension = substr($filename, strrpos($filename, '.') + 1);
		$extension = strtolower($extension);
		if($extension=="jpg" || $extension=="jpeg" )
		{
			$src = imagecreatefromjpeg($uploadedfile);
		}
		else if($extension=="png")
		{
			$src = imagecreatefrompng($uploadedfile);
		}
		else 
		{
			$src = imagecreatefromgif($uploadedfile);
		}
		list($width,$height) = getimagesize($uploadedfile);
		
		//if our file width is less than the  size we need, then we do not need to resize the image
		if($width > $newwidth)
		{
			$newheight=($height/$width)*$newwidth;
			//if($newheight < 500)$newheight = 600;
			$tmp=imagecreatetruecolor($newwidth,$newheight);
			imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
			$filename = $path.SYM.$imgname;
			imagejpeg($tmp,$filename,100);			
			imagedestroy($tmp);
		}
		// for the thumbnail image
		if($thumbnail)
		{
			$newwidth1 = $thumbnailwidth; // for the thumbnail image
			$newheight1 = ($height/$width)*$newwidth1;
			//in case our height is not what we required than we will alter the image
			//if($newheight1 > 50) list($newwidth1, $newheight1) = heightProportion($width, $height, $thumbnailwidth, 50);
			$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
			imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1, $width,$height);
			$justimagename = rtrim($imgname,'.'.$extension);
			if(!is_dir($path.SYM.'thumbnail')) mkdir($path.SYM.'thumbnail',0777);
			$filename1 = $path.SYM.'thumbnail'.SYM.$justimagename.'.'.$extension;
			imagejpeg($tmp1,$filename1,90);		
			imagedestroy($tmp1);
		}	
		imagedestroy($src);		
	}
}
// this function will return the height what we need actually
function heightProportion($width, $height, $rwidth, $rheight)
{	
	$nwidth = $rwidth;
	$nheight = ($height/$width)*$rwidth;
	if($nheight>$rheight)// if new height is grtr than what we need we will do calculation height wise.
	{
		$nheight = $rheight;
		$nwidth = ($width/$height)*$rheight;
	}
	return array($nwidth, $nheight);
}
?>