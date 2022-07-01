<?php
// This class will handle all the task related to purchase order creation
class myalbum extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}
	
	public function get_se_data($mode='add')
	{
		$d1 = array('caname'=>$_POST['caname'],'album_type'=>$_POST['album_type'],'colgId'=>$_POST['colgId'] );	
		return array(true, $d1);
	}
	
	// This function will save the details of a given party
	public function createalbum()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_se_data();	
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		
		$q = "INSERT INTO `college_album` (`caId`, `colgId`, `album_type`, `caname` ) VALUES (NULL , '$d1[colgId]', '$d1[album_type]', '$d1[caname]')";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Billing table error');}
		$rId = mysqli_insert_id($dbc);		
		mysqli_commit($dbc);
		//history_log($dbc, 'Add', 'Bill added with Id '.$rId. ' '.$d1['invoice_no']);
		// in case the user is interested for doing the settlement
		//if($d1['agr'] == 1) $this->gr_updater_onsave($rId, $d1['p_party'], $d1['s_party']);//
		return array('status'=>true, 'myreason'=>'Album successfully Saved');
	}
	public function update_album($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->get_se_data();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		$q = "UPDATE `college_album` SET `caname`='$d1[caname]' WHERE  caId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'Entrance table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'College Updated With ');		return array('status'=>true, 'reason'=>'Albun  successfully updated');
	}
	
	//This function will return the list of as reflected from function name
	public function get_album_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM  college_album  $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['caId'];
			$out[$id]['caId'] = $id;
			$out[$id]['colgId'] = $row['colgId'];
			$out[$id]['album_type'] = $row['album_type'];
			$out[$id]['caname'] = $row['caname'];
		}
		return $out;
	}
	public function getdata($mode='add')
	{
		$d1 = array('caId'=>$_POST['caId'],'colgId'=>$_POST['colgId']);
		return array(true, $d1);
	}
	
	public function save_college_image()
	{
		global $dbc;
		$out = array('status'=>false, 'myreason'=>'');
		// To upload the image
		$upload_path = MYUPLOADS.MSYM.'gallery';
		if(!is_dir($upload_path)) mkdir($upload_path,0777);
		list($status, $d1) = $this->getdata();
		$colgid = $d1['colgId'];	
		//$filefieldname = $_FILES['filename']['name'];
		$upload_path .= MSYM.$colgid;
		if(!is_dir($upload_path)) mkdir($upload_path,0777);
		list($uploadstat, $filename) = fileupload('filename', $upload_path, $allowtype =array('image/jpeg','image/png','image/gif'), $maxsize = 52428800, $mandatory = true);
		if($uploadstat) 
		{
			resizeimage($filename, $upload_path, $newwidth=600, $thumbnailwidth=100, MSYM, $thumbnail = true);
			
			$q = "INSERT INTO `college_album_pics` (`capId`, `caId`, `filename`,`created`) VALUES (NULL , '$d1[caId]', '$filename', NOW())";
			if(mysqli_query($dbc,$q)){
				return array('status'=>true, 'myreason'=>'Image successfully saved.', 'savepath'=>'myuploads/gallery/'.$colgid.'/', 'filename'=>$filename);
			}else{
				$this->unlink_user_files($filename, $upload_path); //Deleting the user uploaded file
				return array('status'=>false, 'myreason'=>'Image could not be uploaded, please try again');
			}
		}
		return array('status'=>true, 'myreason'=>'Album successfully Saved');
	}
	public function edit_college_image()
	{
		global $dbc;
		$out = array('status'=>false, 'myreason'=>'');
		// To upload the image
		$upload_path = MYUPLOADS.MSYM.'gallery';
		if(!is_dir($upload_path)) mkdir($upload_path,0777);
		list($status, $d1) = $this->getdata();
		$colgid = $d1['colgId'];	
		//$filefieldname = $_FILES['filename']['name'];
		$upload_path .= MSYM.$colgid;
		if(!is_dir($upload_path)) mkdir($upload_path,0777);
		list($uploadstat, $filename) = fileupload('filename', $upload_path, $allowtype =array('image/jpeg','image/png','image/gif'), $maxsize = 52428800, $mandatory = true);
		if(!empty($filename)){
		if($uploadstat) 
		{
			resizeimage($filename, $upload_path, $newwidth=600, $thumbnailwidth=100, MSYM, $thumbnail = true);
			
			$q = "INSERT INTO `college_album_pics` (`capId`, `caId`, `filename`,`created`) VALUES (NULL , '$d1[caId]', '$filename', NOW())";
			if(mysqli_query($dbc,$q)){
				return array('status'=>true, 'myreason'=>'Image successfully update.', 'savepath'=>'myuploads/gallery/'.$colgid.'/', 'filename'=>$filename);
			}else{
				$this->unlink_user_files($filename, $upload_path); //Deleting the user uploaded file
				return array('status'=>false, 'myreason'=>'Image could not be uploaded, please try again');
			}
		}}
		return array('status'=>true, 'myreason'=>'Album successfully update');
	}
	
	public function college_imagelist_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
	 $q = "SELECT *,count(filename) as img FROM college_album_pics inner join college_album using(caId)   $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['capId'];
			$out[$id]['capId'] = $id;
			$out[$id]['caId'] = $row['caId'];
			$out[$id]['created'] = $row['colgId'];
			$out[$id]['album_type'] = $row['album_type'];
			$out[$id]['videolink'] = $row['videolink'];
			$out[$id]['caname'] = $row['caname'];
			$out[$id]['colgId'] = $row['colgId'];
			$out[$id]['filename'] = $row['filename'];
			$out[$id]['img'] = $row['img'];
		}
		return $out;
	}
	
	public function getimg($id)
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		//$filterstr = $this->oo_filter($filter, $records, $orderby);
		
	 $q = "SELECT * FROM college_album_pics WHERE caId='$id'";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['capId'];
			$out[$id]['capId'] = $id;
			$out[$id]['caId'] = $row['caId'];
			$out[$id]['filename'] = $row['filename'];
			
		}
		return $out;
	}
	
	//This function will remove the uploaded image
	public function unlink_user_files($filename, $path)
	{
		echo $imgpath = $path.MSYM.$filename;
		echo $imgpath_thumb = $path.MSYM.'thumb'.MSYM.$filename;
		if(is_file($imgpath)) unlink($imgpath); // deleting the big image
		if(is_file($imgpath_thumb)) unlink($imgpath_thumb); // deleting the thumbnail file
	}
	
	
	
	public function getdata_link($mode='add')
	{
		$d1 = array('caId'=>$_POST['caId'],'colgId'=>$_POST['colgId'],'videolink'=>$_POST['videolink']);
		return array(true, $d1);
	}
	public function svaevideolink()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->getdata_link();	
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		
		$q = "INSERT INTO `college_album_pics` (`capId`, `caId`, `videolink`, `created` ) VALUES (NULL , '$d1[caId]', '$d1[videolink]', now())";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Billing table error');}
		$rId = mysqli_insert_id($dbc);		
		mysqli_commit($dbc);
		//history_log($dbc, 'Add', 'Bill added with Id '.$rId. ' '.$d1['invoice_no']);
		// in case the user is interested for doing the settlement
		//if($d1['agr'] == 1) $this->gr_updater_onsave($rId, $d1['p_party'], $d1['s_party']);//
		return array('status'=>true, 'myreason'=>'Album successfully Saved');
	}
	public function updatevideolink($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'reason'=>'');
		list($status, $d1) = $this->getdata_link();
		if(!$status) return array('status'=>false, 'reason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update the qc
		 $q = "UPDATE `college_album_pics` SET `videolink`='$d1[videolink]' WHERE  capId = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'reason'=>'Entrance table error');}
		mysqli_commit($dbc);
		history_log($dbc, 'Edit', 'College Updated With ');		return array('status'=>true, 'reason'=>'Albun  successfully updated');
	}
	public function get_videolik_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM college_album_pics inner join college_album using(caId)  $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['capId'];
			$out[$id]['capId'] = $id;
			$out[$id]['caId'] = $row['caId'];
			$out[$id]['created'] = $row['colgId'];
			$out[$id]['album_type'] = $row['album_type'];
			$out[$id]['videolink'] = $row['videolink'];
			$out[$id]['caname'] = $row['caname'];
			$out[$id]['colgId'] = $row['colgId'];
			
		}
		return $out;
	}
	public function get_college_image($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM college_album_pics INNER JOIN college_album USING(caId) INNER JOIN college USING(colgId) $filterstr";
		list($opt,$rs) = run_query($dbc, $q, $mode='multi',$msg = '');
		if(!$opt) return $out;
		while($row = mysqli_fetch_assoc($rs))
		{
			$id= $row['caId'];
			$id1= $row['capId'];
			$out[$id]['capId'] = $id;
			$out[$id]['caId'] = $row['caId']; 	
			$out[$id]['caname'] = $row['caname']; 	
			$out[$id]['filename'] = $row['filename'];
			$out[$id]['title'] = $row['title'];
			$out[$id]['videolink'] = $row['videolink'];
			$out[$id]['logo'] = $row['logo'];
			$out[$id]['colg_name'] = $row['colg_name'];
		//	$out[$id]['images'][$id1] = $row['title'];
		}
		return $out;
	}
	public function get_album_images($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM college_album_pics INNER JOIN college_album USING(caId) $filterstr";
		list($opt,$rs) = run_query($dbc, $q, $mode='multi',$msg = '');
		if(!$opt) return $out;
		while($row = mysqli_fetch_assoc($rs))
		{
			//$id= $row['caId'];
			$id= $row['capId'];
			$out[$id]['capId'] = $id;
			$out[$id]['caId'] = $row['caId']; 	
			$out[$id]['caname'] = $row['caname']; 	
			$out[$id]['filename'] = $row['filename'];
			$out[$id]['title'] = $row['title'];
			$out[$id]['videolink'] = $row['videolink'];
			
		}
		return $out;
		
	}
	/*public function chk($id)
	{
		global $dbc;
		$out = array();		
		
		
	  $q = "SELECT * FROM  college_album_pics WHERE caId = '$id'";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			   $id = $row['capId'];
			  $out[$id]['capId'] = $id;
			  $out[$id]['filename'] = $row['filename'];
			
		}
		return $out;
	}*/
	
}
?>