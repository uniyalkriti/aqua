<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class location extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	
	######################################## location start here ######################################################		
	public function get_location_se_data()
	{  
		$d1 = array();
		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'data']['id'];
		
		$d1['myreason'] = 'Please fill all the required information';
                $title = "location_title_".$d1['mtype'];
		$d1['what'] = $_SESSION[SESS.'constant'][$title];
		return array(true,$d1);
	}
	######################################## location save code  start here ######################################################
	public function location_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_location_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
                $id = $d1['uid'].date('Ymdhis');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `location_1` (`id`, `name`, `company_id`) VALUES ('$id', '$d1[name]', '$d1[company_id]')";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$d1['what'].' table error');}
		$rId = $id;	
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'Catalog <b>'.$d1['name'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	######################################## location code edit start here ######################################################
	public function location_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_location_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		$originaldata = $this->get_location_list("id = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE location_1 SET `name` = '$d1[name]', company_id = '$d1[company_id]' WHERE id='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$d1['what'].' Table error') ;} 
		
		mysqli_commit($dbc);
		$rId = $id;
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'item <strong>'.$d1['itemname'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated', 'rId'=>$rId);
	}	
	######################################## location list code  start here ######################################################
	public function get_location_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM location_1  $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['id'];
			$out[$id] = $row; // storing the item id
		}
		return $out;
	}
   
	######################################## location code delete start here ######################################################
	public function get_location_deletion_list($filter='',  $records = '', $orderby='',$mtype)
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM location_$mtype  $filterstr ";
                //h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['id'];
			$out[$id] = $row; // storing the item id
		}
		return $out;
	}
   
	######################################## location code delete start here ######################################################
	public function location_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
                $dealer_level = $_SESSION[SESS.'constant']['dealer_level'];
                $retailer_level = $_SESSION[SESS.'constant']['retailer_level'];
                $id = explode('<$>' , $id);
                $loc_id = $id[0];
                $mtype = $id[1];
                $next_location = $mtype + 1;
		if(empty($filter)) $filter = "id = $loc_id";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_location_deletion_list($filter, $records, $orderby,$mtype);
               
		if(empty($deleteRecord)){ $out['myreason'] = 'Location not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
                
                
                /////////////// Forcefully delete beat without checking /////////////////////
//                $deldlrlq['location']="DELETE FROM `dealer_location_rate_list` WHERE `location_id`= $loc_id";
//                foreach($deldlrlq as $key1=>$value1){
//                    
//			if(!mysqli_query($dbc, $value1)){
//				mysqli_rollback($dbc);
//				return array('status'=>false, 'myreason'=>"$key1 query failed");
//			}
//		}
//                
//                 $delretq['location']="DELETE FROM `retailer` WHERE `location_id`= $loc_id";
//                foreach($delretq as $key2=>$value2){
//                    
//			if(!mysqli_query($dbc, $value2)){
//				mysqli_rollback($dbc);
//				return array('status'=>false, 'myreason'=>"$key1 query failed");
//			}
//		}
//                
                
		//Checking whether the location is deletable or not
		$q['LOCATION'] = "SELECT id FROM location_$next_location WHERE location_".$mtype."_id = ";
                if($mtype == $dealer_level)
		$q['dealer'] = "SELECT dealer_id FROM dealer_location_rate_list INNER JOIN location_$mtype ON location_$mtype.id = dealer_location_rate_list.location_id INNER JOIN dealer ON dealer.id = dealer_location_rate_list.dealer_id WHERE location_id = ";
                
                if($mtype == $retailer_level)
                $q['retailer'] = "SELECT r.id FROM location_$mtype INNER JOIN retailer r ON r.location_id = location_$mtype.id WHERE location_id = ";
		$found = false;
		foreach($q as $key=>$value)
		{
			$q1 = "$value $loc_id LIMIT 1";
			list($opt1, $rs1) = run_query($dbc, $q1, $mode='single', $msg='');	
			if($opt1) {$found = true; $found_case = $key; break; }
		}
		// If this location has been found in any one of the above query we can not delete it.		      
		if($found) {$out['myreason'] = 'Location  entered in <b>'.$found_case.'</b> so could not be deleted.'; return $out;}
		
		//Running the deletion queries
		$delquery = array();
		$delquery['location'] = "DELETE FROM  location_$mtype  WHERE id = '$loc_id' LIMIT 1";
                
                
		foreach($delquery as $key=>$value){
                    
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>"$key query failed");
			}
		}
                
		//After successfull deletion
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Location successfully deleted');
	}
        ////////////////////////////////////////DISPATCH BEAT SAVE///////////////////////////////////
      
        public function get_dispatch_se_data()
	{  
		$d1 = array();
		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		//$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$title = "location_title_".$d1['mtype'];
		$d1['what'] = $_SESSION[SESS.'constant'][$title];
		return array(true,$d1);
	}
	
	public function dispatch_beat_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_dispatch_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);	
		//start the transaction
              //  pre($d1); //exit;
               $dealer_id =  $_SESSION[SESS.'data']['dealer_id'];
               $rout_id = $dealer_id.date('Ymdhis');
               	mysqli_query($dbc, "START TRANSACTION");
                
		// query to save
	        $q = "INSERT INTO `dispatch_beat`(`route_id`, `route_name`,`dealer_id`) VALUES
                    ('$rout_id','$d1[dispatch_name]'$dealer_id)";
               // echo $q;
                $r = mysqli_query($dbc,$q) ;
                $str = array();
                foreach($d1['location'] as $key=>$value)
                {
                    $str[] =  "('$dealer_id',$rout_id,'$value')";
                }
                 $str = implode(', ', $str);
               // pre($str); exit;
                $q11="INSERT INTO `dispatch_beat_details`(`dealer_id`, `route_id`, `beat`) VALUES $str";
                $r11 = mysqli_query($dbc,$q11) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$d1['what']. 'table error');}
		$rId = $id;	
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'Catalog <b>'.$d1['name'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
	return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
         public function get_dispatch_beat_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		//if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
               
                $q = "SELECT dispatch_beat_details.id as dbid,route_name, l5_name FROM dispatch_beat  INNER JOIN dispatch_beat_details ON dispatch_beat.route_id
                    = dispatch_beat_details.route_id INNER JOIN location_view ON beat = l5_id $filterstr";               

               list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row["dbid"];
			$out[$id] = $row; // storing the item id
                           
		}
            //   pre($out);
		return $out;
	}
      
       ######################################## catalog save code  start here ######################################################
        public function get_location_category_se_data()
	{  
		$d1 = array();
		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		//$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$title = "location_title_".$d1['mtype'];
		$d1['what'] = $_SESSION[SESS.'constant'][$title];
		return array(true,$d1);
	}
	
	public function beat_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_location_category_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);	
		//start the transaction
                $mtype = $d1['mtype']; 
                $dealer_id =  $_SESSION[SESS.'data']['dealer_id'];
                $loop = $mtype - 1;
		mysqli_query($dbc, "START TRANSACTION");
                $catalogloopid = "location_".$loop."_id";
                $catname = "name$mtype";
                $id = $dealer_id.date('Ymdhis');
		// query to save
	        $q = "INSERT INTO location_$mtype (`id`, `name`, `location_".$loop."_id`, `company_id`) VALUES ('$id', '$d1[$catname]','$d1[$catalogloopid]', '$d1[company_id]')";
               // echo $q;
                $r = mysqli_query($dbc,$q) ;
                $q11="INSERT INTO `dealer_location_rate_list`(`id`, `dealer_id`, `location_id`, `rate_list_id`, `company_id`) VALUES ('NULL','$dealer_id','$id','1','1')";
                $r11 = mysqli_query($dbc,$q11) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$d1['what']. 'table error');}
		$rId = $id;	
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'Catalog <b>'.$d1['name'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
	return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
        public function beat_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_location_category_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		
                $mtype = $d1['mtype'];
                $loop = $mtype - 1;
                $catalogloopid = "location_".$loop."_id";
                $catname = "name$mtype";
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
                //h1($d1['uid'].date('Ymdhis'));
		// query to update 
		$q="UPDATE location_$mtype SET `name` = '$d1[$catname]',`location_".$loop."_id` = '$d1[$catalogloopid]', `company_id` = '$d1[company_id]' WHERE id='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$d1['what'].'location_$mtype Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		$rId = $id;
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'item <strong>'.$d1['itemname'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated', 'rId'=>$rId);
	}	
        public function get_location_category_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		//if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
                if(isset($_POST['mtype'])) $mtype = $_POST['mtype'];
                if(isset($_GET['mtype']))  $mtype = $_GET['mtype'];
                $loop = $mtype - 1;
                $str = '';
                for($k = $mtype; $k>=1; $k--)
               {
                   $str .= ",location_$k.name AS name$k,location_$k.id AS location_".$k."_id ";
               }
                $q = "SELECT * $str, location_$mtype.company_id FROM location_$mtype ";
                for($i = $mtype; $i>1;$i--)
                {
                    $j = $i - 1; 
                    $q .= "INNER JOIN location_$j ON location_$i.location_".$j."_id = location_$j.id ";
                }
               $q .= "$filterstr";
                h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row["location_".$mtype."_id"];
			$out[$id] = $row; // storing the item id
                           
		}
		return $out;
	}
        
         public function get_beat_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		//if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
                if(isset($_POST['mtype'])) $mtype = $_POST['mtype'];
                if(isset($_GET['mtype']))  $mtype = $_GET['mtype'];
                $q = "SELECT *,location_5.name AS name5,location_5.id AS id from location_5 
INNER JOIN location_4 ON location_4.id=location_5.location_4_id 
INNER JOIN location_3 ON location_3.id=location_4.location_3_id 
INNER JOIN location_2 ON location_2.id=location_3.location_2_id 
INNER JOIN dealer_location_rate_list  ON dealer_location_rate_list.location_id=location_5.id 
INNER JOIN location_view ON location_view.l5_id=location_5.id 
                        $filterstr";               

               // h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row["id"];
			$out[$id] = $row; // storing the item id
                           
		}
               // pre($out);
		return $out;
	}
        
        public function get_beat1_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		//if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
                if(isset($_POST['mtype'])) $mtype = $_POST['mtype'];
                if(isset($_GET['mtype']))  $mtype = $_GET['mtype'];
                $q = "SELECT *,location_view.l5_name AS name5,location_5.id AS id FROM dealer_location_rate_list INNER JOIN location_view ON 
                    location_view.l5_id = dealer_location_rate_list.location_id 
                    INNER JOIN location_5 ON location_5.id=location_view.l5_id 
                        $filterstr";               

               // h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row["id"];
			$out[$id] = $row; // storing the item id
                           
		}
               // pre($out);
		return $out;
	}
}// class end here
?>