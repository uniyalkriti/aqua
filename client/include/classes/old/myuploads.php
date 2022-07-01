<?php
// This class will handle all the task related to purchase order creation
class myuploads extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}
	
	public function upload_via_pc($filefieldname,$colgid)
	{
		// To upload the image
		$upload_path = MYUPLOADS.MSYM.$colgid;
		if(!is_dir($upload_path)) mkdir($upload_path,0777);
		$upload_path .= MSYM.'pc';
		if(!is_dir($upload_path)) mkdir($upload_path,0777);
		list($uploadstat, $filename) = fileupload($filefieldname, $upload_path, $allowtype =array('image/jpeg','image/png','image/gif'), $maxsize = 52428800, $mandatory = true);
		if($uploadstat) 
		{
			resizeimage($filename, $upload_path, $newwidth=800, $thumbnailwidth=100, MSYM, $thumbnail = true);
			if($this->save_image_to_user_account(1, $filename)){
				return array('status'=>true, 'myreason'=>'Image successfully saved to your account', 'savepath'=>'myuploads/gallery/'.$colgid.'/pc/', 'filename'=>$filename);
			}else{
				$this->unlink_user_files($filename, $upload_path); //Deleting the user uploaded file
				return array('status'=>false, 'myreason'=>'Image could not be uploaded, please try again');
			}
		}
		return array('status'=>false, 'myreason'=>$filename);
	}
	//This function will fetch a remote file and will copy it to the user account
	public function upload_via_url($filefieldname)
	{
		// To upload the image
		$upload_path = MYUPLOADS.MSYM.$_SESSION[FSESS.'id'];
		if(!is_dir($upload_path)) mkdir($upload_path,0777);
		$upload_path .= MSYM.'url';
		if(!is_dir($upload_path)) mkdir($upload_path,0777);
		$url = $_POST[$filefieldname];//'http://'.$id;				
		$filename = basename($url);
		$allowed = array('jpeg','jpg','png','gif');
		$filedetail = get_extension($filename);
		$ext = strtolower($filedetail['ext']);
		$filename = mktime().'.'.$ext;
		if(in_array($ext,$allowed))
		{
			// make sure the remote file is successfully opened before doing anything else
			if ($fp = fopen($url, 'rb')) {
				$newfile = fopen($upload_path.MSYM.$filename, "wb");
				if ($newfile){
					while(!feof($fp))
						fwrite($newfile, fread($fp, 1024 * 8 ), 1024 * 8 );
			  		if ($fp) fclose($fp);
			  		if ($newfile) fclose($newfile);
					resizeimage($filename, $upload_path, $newwidth=800, $thumbnailwidth=100, SYM, $thumbnail = true);
					//$this->save_image_to_user_account(2, $filename);// saving the image database entry
					return array('status'=>true, 'myreason'=>'Image successfully copied to your account', 'savepath'=>'myuploads/gallery/gallery/'.$colgid.'/url/', 'filename'=>$filename);
					
				}//if ($newfile){ ends
			}//if ($fp = fopen($url, 'rb')) { ends
			else
				return array('status'=>false, 'myreason'=>'Unable to open the remote file.');
		}
		else
			return array('status'=>false, 'myreason'=>'Allowed filetype jpg,png,gif only');
	}
	//This function will save the uploaded user image to his/her account
	public function save_image_to_user_account($img_src, $filename, $userid = ''){
		global $dbc;
		if(empty($userid)) $userid = $_SESSION[FSESS.'id'];
		$q = "INSERT INTO my_gallery(mgId, id, img_src, filename) VALUES(NULL, $userid, '$img_src', '$filename')";
		$r = mysqli_query($dbc, $q);
		if($r) return true; else return false;
	}
	//This function will delete the user uploaded files in case of error
	public function unlink_user_files($filename, $path)
	{
		echo $imgpath = $path.MSYM.$filename;
		echo $imgpath_thumb = $path.MSYM.'thumb'.MSYM.$filename;
		if(is_file($imgpath)) unlink($imgpath); // deleting the big image
		if(is_file($imgpath_thumb)) unlink($imgpath_thumb); // deleting the thumbnail file
	}
	
	public function get_category_se_data($mode='add')
	{		
		$d1 = array('fcname'=>$_POST['fcname'], 'tooltip'=>$_POST['tooltip'], 'display_order'=>$_POST['display_order'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);		
		$d1['myreason'] = 'Please fill all the required information';		
		//$d2 = array('itemId'=>$_POST['itemid'], 'rate'=>$_POST['cpc'], 'qty'=>$_POST['qty'], 'desc'=>$_POST['desc']);
		return array(true, $d1);
	}
	
	// This function will save the details of a given
	public function category_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_category_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `furniture_category` (`fcId`, `fcname`, `display_order`, `tooltip`, `activeBit`) VALUES (NULL , '$d1[fcname]', '$d1[display_order]', '$d1[tooltip]', '1')";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'furniture_category table error');}
		$rId = mysqli_insert_id($dbc);		
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'Category added with Id '.$rId. ' '.$d1['fcname']);
		return array('status'=>true, 'myreason'=>'Category successfully Saved', 'rId'=>$rId);
	}
	
	// This function will edit the details of a given
	public function category_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_category_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		$q = "UPDATE furniture_category SET `fcname`='$d1[fcname]', `tooltip`='$d1[tooltip]', `display_order`='$d1[display_order]' WHERE fcId = '$id' LIMIT 1";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'furniture_category table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Category updated with Id '.$id.' '.$d1['fcname']);
		return array('status'=>true, 'myreason'=>'Category successfully updated', 'rId'=>$id);
	}
	
	//This function will return the list of as reflected from function name
	public function get_category_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM furniture_category $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['fcId'];
			$out[$id]['fcId'] = $id; // storing the id
			$out[$id]['fcname'] = $row['fcname'];
			$out[$id]['tooltip'] = $row['tooltip'];
			$out[$id]['display_order'] = $row['display_order'];
		}
		return $out;
	}
	
	//This function can be used when asking for purchase party combobox array
	public function category_name($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();
		$q = "SELECT fcId, fcname FROM furniture_category WHERE activeBit=1 ORDER BY fcname ASC";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
				$out[$row['fcId']] = $row['fcname'];
		}
		return $out;
	}
	
	public function get_subcategory_se_data($mode='add')
	{		
		$d1 = array('fcId'=>$_POST['fcId'], 'fsname'=>$_POST['fsname'], 'price'=>$_POST['price'], 'display_order'=>$_POST['display_order'], 'pg_title'=>$_POST['pg_title'], 'meta_keyword'=>$_POST['meta_keyword'], 'meta_desc'=>$_POST['meta_desc'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);		
		$d1['myreason'] = 'Please fill all the required information';		
		//$d2 = array('itemId'=>$_POST['itemid'], 'rate'=>$_POST['cpc'], 'qty'=>$_POST['qty'], 'desc'=>$_POST['desc']);
		return array(true, $d1);
	}
	
	// This function will save the details of a given
	public function subcategory_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_subcategory_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `furniture_subcat` (`fsId`, `fcId`, `fsname`, `price`, `display_order`, `activeBit`, `pg_title`, `meta_keyword`, `meta_desc`) VALUES (NULL , '$d1[fcId]', '$d1[fsname]', '$d1[price]', '$d1[display_order]',  '1', '$d1[pg_title]', '$d1[meta_keyword]', '$d1[meta_desc]')";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'furniture_subcategory table error');}
		$rId = mysqli_insert_id($dbc);		
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'SubCategory added with Id '.$rId. ' '.$d1['fsname']);
		return array('status'=>true, 'myreason'=>'Subcategory successfully Saved', 'rId'=>$rId);
	}
	
	// This function will edit the details of a given
	public function subcategory_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_subcategory_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		$q = "UPDATE furniture_subcat SET `fcId`='$d1[fcId]', `fsname`='$d1[fsname]', `price`='$d1[price]', `display_order`='$d1[display_order]', `pg_title`='$d1[pg_title]', `meta_keyword`='$d1[meta_keyword]', `meta_desc`='$d1[meta_desc]' WHERE fsId = '$id' LIMIT 1";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'furniture_subcategory table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'SubCategory updated with Id '.$id.' '.$d1['fsname']);
		return array('status'=>true, 'myreason'=>'Subcategory successfully updated', 'rId'=>$id);
	}
	
	public function get_subcategory_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT fs.*, fc.fcname FROM furniture_subcat AS fs INNER JOIN furniture_category AS fc USING(fcId)  $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['fsId'];
			$out[$id]['fsId'] = $id; // storing the id
			$out[$id]['fcId'] = $row['fcId'];
			$out[$id]['fcname'] = $row['fcname'];
			$out[$id]['fsname'] = $row['fsname'];
			$out[$id]['price'] = (int)$row['price'];
			$out[$id]['display_order'] = $row['display_order'];
			$out[$id]['pg_title'] = $row['pg_title'];
			$out[$id]['meta_keyword'] = $row['meta_keyword'];
			$out[$id]['meta_desc'] = $row['meta_desc'];
		}
		return $out;
	}
	
	public function get_subcat_byId($id)
	{
		global $dbc;
		$out = '';			
		$q = "SELECT fsname AS name FROM furniture_subcat WHERE fsId=$id LIMIT 1";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if($opt) $out = $rs['name'];
		return $out;
	}
	//This function will retur the category name based on the subcat id
	public function get_cat_bysubcatId($id)
	{
		global $dbc;
		$out = '';			
		$q = "SELECT fcname AS name FROM furniture_subcat INNER JOIN furniture_category USING(fcId) WHERE fsId=$id LIMIT 1";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if($opt) $out = $rs['name'];
		return $out;
	}
	
	//This will fetch the details of all the furniture questions
	public function get_question_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM questions $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['qId'];
			$out[$id]['qId'] = $id; // storing the id
			$out[$id]['question'] = $row['question'];
			$out[$id]['answer_type'] = $row['answer_type'];
			$out[$id]['tooltip'] = $row['tooltip'];
			$out[$id]['answer_dynamic'] = $row['answer_dynamic'];
			$out[$id]['display_order'] = $row['display_order'];
		}
		return $out;
	}
	
	public function get_quesname_byId($qId, $ofield='')
	{
		global $dbc;
		$out = '';	
		if(!empty($ofield))
			$q = "SELECT $ofield AS name FROM questions WHERE qId=$qId LIMIT 1";	
		else	
			$q = "SELECT question AS name FROM questions WHERE qId=$qId LIMIT 1";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if($opt) $out = $rs['name'];
		return $out;
	}
	
	public function get_answer_se_data($mode='add')
	{		
		$d1 = array('qId'=>$_POST['qId'], 'name'=>$_POST['name'], 'filename'=>$_POST['filename'], 'display_order'=>$_POST['display_order'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);		
		$d1['myreason'] = 'Please fill all the required information';		
		//$d2 = array('itemId'=>$_POST['itemid'], 'rate'=>$_POST['cpc'], 'qty'=>$_POST['qty'], 'desc'=>$_POST['desc']);
		return array(true, $d1);
	}
	
	// This function will save the details of a given
	public function answer_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_answer_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `question_answers` (`qaId`, `qId`, `name`, `filename`, `display_order`, `activeBit`) VALUES (NULL , '$d1[qId]', '$d1[name]', '$d1[filename]', '$d1[display_order]',  '1')";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'answer table error');}
		$rId = mysqli_insert_id($dbc);		
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'Answer added with Id '.$rId. ' '.$d1['name']);
		return array('status'=>true, 'myreason'=>'Answer successfully Saved', 'rId'=>$rId);
	}
	
	// This function will edit the details of a given
	public function answer_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_answer_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		$q = "UPDATE question_answers SET `name`='$d1[name]', `filename`='$d1[filename]', `display_order`='$d1[display_order]' WHERE qaId = '$id' LIMIT 1";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'answer table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Answer updated with Id '.$id.' '.$d1['name']);
		return array('status'=>true, 'myreason'=>'Answer successfully updated', 'rId'=>$id);
	}
	
	public function get_answers_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM question_answers $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['qaId'];
			$out[$id]['qaId'] = $id; // storing the id
			$out[$id]['qId'] = $row['qId'];
			$out[$id]['name'] = $row['name'];
			$out[$id]['filename'] = $row['filename'];
			$out[$id]['activeBit'] = $row['activeBit'];
			$out[$id]['display_order'] = $row['display_order'];
		}
		return $out;
	}
	
	
	public function get_mergeanswer_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();	
		$output = '';	
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM question_answers $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $output; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$out[] = $row['name'];
		}
		$output = implode(', ', $out);
		return $output;
	}
	
	public function get_answer_byId($id)
	{
		global $dbc;
		$out = '';			
		$q = "SELECT name AS name FROM question_answers WHERE qaId=$id LIMIT 1";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if($opt) $out = $rs['name'];
		return $out;
	}
	
	public function get_image_se_data($mode='add')
	{		
		$d1 = array('fsId'=>$_POST['fsId'], 'price'=>$_POST['price'], 'design_code'=>$_POST['design_code'], 'description'=>$_POST['description'], 'filename'=>$_POST['filename'], 'display_order'=>$_POST['display_order'], 'csess'=>$_SESSION[SESS.'csess'], 'uid'=>$_SESSION[SESS.'id']);		
		$d1['myreason'] = 'Please fill all the required information';		
		//$d2 = array('itemId'=>$_POST['itemid'], 'rate'=>$_POST['cpc'], 'qty'=>$_POST['qty'], 'desc'=>$_POST['desc']);
		return array(true, $d1);
	}
	
	// This function will save the details of a given
	public function image_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_image_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `furniture_subcat_images` (`fsimgId`, `fsId`, `price`, `design_code`, `description`, `filename`, `display_order`, `activeBit`) VALUES (NULL , '$d1[fsId]', '$d1[price]', '$d1[design_code]', '$d1[description]',  '$d1[filename]', '$d1[display_order]',  '1')";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'image table error');}
		$rId = mysqli_insert_id($dbc);		
		mysqli_commit($dbc);
		history_log($dbc, 'Add', 'Image added with Id '.$rId. ' '.$d1['filename']);
		return array('status'=>true, 'myreason'=>'Image successfully Saved', 'rId'=>$rId);
	}
	
	// This function will edit the details of a given
	public function image_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_image_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		$q = "UPDATE furniture_subcat_images SET `price`='$d1[price]', `design_code`='$d1[design_code]', `description`='$d1[description]', `filename`='$d1[filename]', `display_order`='$d1[display_order]' WHERE fsimgId = '$id' LIMIT 1";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'image table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'Image updated with Id '.$id.' '.$d1['filename']);
		return array('status'=>true, 'myreason'=>'Image successfully updated', 'rId'=>$id);
	}
	
	//This function will act as a controller for the my gallery view
	public function get_mygallery($mfId)
	{
		global $dbc;
		$out = '';
		$viewtype = $mfId == 'URL' ? 'URL':'D';	// setting the default view type
		if(is_numeric($mfId)) $viewtype = 'NUM';
		switch($viewtype){
			case'D':
			{
				$out = $this->get_firstview(); 
				break;	
			}
			case'URL':
			{
				//getting the details of the url upload images which are not mapped to any album
				$urlimages = $this->get_user_images("img_src = 2 AND mfId = 0 id = ".$uac,  $records = '', $orderby=''); 
				if(empty($urlimages)) $out = 'Sorry no images available';
				break;	
			}
			case'NUM':
			{
				$out[''] = 'Showing the NUM view'; 
				break;	
			}			
		}// switch ends
		return $out;
	}
	
	//This function will give the first view
	public function get_firstview()
	{
		global $dbc;
		$uac = ' id = '.$_SESSION[FSESS.'id']; // The account whose images are to be fetched
		//getting the details of the url upload images which are not mapped to any album
		$urlimages = $this->get_url_images();
		//getting the details of the pc upload images which are not mapped to any album
		$defaultimages = $this->get_default_images();
		//getting the details of the images which are categorised as albums
		$albumimages = $this->get_album_images();		
		$foldernames = array('Default'=>'Default', 'URL'=>'URL', 'ALBUM'=>'ALBUM');
		//If url images are not present then removing the url folder
		if(empty($urlimages)) unset($foldernames['URL']);
		//If default images are not present then removing the default images folder
		if(empty($defaultimages)) unset($foldernames['Default']);
		//If albumimages images are not present then removing the albumimages images folder
		if(empty($albumimages)) unset($foldernames['ALBUM']);
		//This will happen when user is not having any images in his/her account
		if(empty($foldernames))	return 'Sorry no images available.';
		//if user is having some albums in his/her account
		if(isset($foldernames['ALBUM'])){
			$userfolders = $this->get_user_folders($uac);
			if(!empty($userfolders)){
				unset($foldernames['Default']);
				foreach($userfolders as $key=>$value){
					$foldernames[$key] = $value['foldername'];
				}//foreach($userfolders as $key=>$value){ ends					
			}// if(!empty($userfolders)){ ends			
		}
		return 	folder_view_maker($foldernames);	
	}
	
	//This function will make an view of the album
	public function folder_view_maker($foldernames)
	{
		global $dbc;
		$out = '';
		foreach($foldernames as $key=>$value){
			$out .= '<li><a href="javascript:void(0);" onclick="alert(\''.$value.'\');">'.$value.'</a></li>';
		}
		$out = '<ul id="mygallery">'.$out.'</ul>';	
		return $out;	
	}
	
	//This function will make an view of the album
	public function images_view_maker($images)
	{
		global $dbc;
		$out = '';
		$imgpath = '';
		foreach($images as $key=>$value){
			$out .= '<li><a href="javascript:void(0);"><img onclick="load_click_data(\'canvas_load_image\',this.src);" src="'.$imgpath.'" width="50px;"></a></li>';
		}
		$out = '<ul id="mygallery">'.$out.'</ul>';	
		return $out;	
	}
	
	//This function will give the list of the url images
	public function get_url_images($id='')
	{
		global $dbc;
		if(empty($id)) $id = $_SESSION[FSESS.'id'];
		$uac = ' id = '.$id;
		return $this->get_user_images("img_src = 2 AND mfId = 0".$uac,  $records = '', $orderby='');	
	}
	
	//This function will give the list of default images
	public function get_default_images($id='')
	{
		global $dbc;
		if(empty($id)) $id = $_SESSION[FSESS.'id'];
		$uac = ' id = '.$id;
		return $this->get_user_images("img_src = 1 AND mfId = 0".$uac,  $records = '', $orderby='');	
	}
	
	//This function will give the list of album images
	public function get_album_images($id='')
	{
		global $dbc;
		if(empty($id)) $id = $_SESSION[FSESS.'id'];
		$uac = ' id = '.$id;
		return $this->get_user_images("mfId != 0".$uac,  $records = '', $orderby='');
	}
	
	//This function will get the user images based on the filter applied
	public function get_user_images($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM my_gallery $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['mgId'];
			$out[$id]['mgId'] = $id; // storing the id
			$out[$id]['mfId'] = $row['mfId'];
			$out[$id]['id'] = $row['id'];
			$out[$id]['img_src'] = $row['img_src'];
			$out[$id]['filename'] = $row['filename'];
			$out[$id]['name'] = $row['name'];
			$out[$id]['description'] = $row['description'];
		}
		return $out;
	}
	
	//This function will get the user images based on the filter applied
	public function get_user_folders($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM my_folder $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['mfId'];
			$out[$id]['mfId'] = $id; // storing the id
			$out[$id]['foldername'] = $row['foldername'];
		}
		return $out;
	}
	
	//This function will return a folder by name
	public function get_foldername_byId($id)
	{
		global $dbc;
		$out = NULL;			
		$q = "SELECT foldername AS name FROM my_folder WHERE mfId=$id LIMIT 1";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if($opt) $out = $rs['name'];
		return $out;
	}
	//This function can be used to get the value of the column by Id
	public function get_general_byId($table, $fieldname, $pkey, $id)
	{
		global $dbc;
		$out = NULL;			
		$q = "SELECT $fieldname AS name FROM $table WHERE $pkey='$id' LIMIT 1";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if($opt) $out = $rs['name'];
		return $out;
	}
}
?>