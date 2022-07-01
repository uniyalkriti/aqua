<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class catalog extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	
	######################################## catalog start here ######################################################		
	public function get_catalog_se_data()
	{  
		$d1 = array();
		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		//$d1['csess'] = $_SESSION[SESS.'csess'];	
		
		$d1['myreason'] = 'Please fill all the required information';
		$title = "catalog_title_".$d1[mtype];
		$d1['what'] = $_SESSION[SESS.'constant'][$title];
		return array(true,$d1);
	}
	######################################## catalog save code  start here ######################################################
	public function catalog_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_catalog_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
                $id = $d1['uid'].date('Ymdhis');
		$q = "INSERT INTO `catalog_1` (`id`, `name`, `company_id`) VALUES ('$id', '$d1[name]', '$d1[company_id]')";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$d1['what'].' table error');}
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'Catalog <b>'.$d1['name'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	######################################## catalog code edit start here ######################################################
	public function catalog_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_catalog_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		$originaldata = $this->get_catalog_list("id = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE catalog_1 SET `name` = '$d1[name]', company_id = '$d1[company_id]' WHERE id='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$d1['what'].' Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		$rId = $id;
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'item <strong>'.$d1['itemname'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated', 'rId'=>$rId);
	}	
	######################################## catalog list code  start here ######################################################
	public function get_catalog_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM catalog_1  $filterstr ";
               // h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['id'];
			$out[$id] = $row; // storing the item id
		}
		return $out;
	}
   
	######################################## catalog code delete start here ######################################################
	public function get_catalog_deletion_list($filter='',  $records = '', $orderby='',$mtype)
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM catalog_$mtype  $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['id'];
			$out[$id] = $row; // storing the item id
		}
		return $out;
	}
        // This function is used to delte catalog product
        public function category_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
                $catalog_level = $_SESSION[SESS.'constant']['catalog_level'];
                $id = explode('<$>' , $id);
                $catalog_id = $id[0];
                $mtype = $id[1];
                $next_catalog = $mtype + 1;
		if(empty($filter)) $filter = "id = $catalog_id";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_catalog_deletion_list($filter, $records, $orderby,$mtype);
              
		if(empty($deleteRecord)){ $out['myreason'] = 'Catalog not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		//Checking whether the category is deletable or not
		$q['catalog'] = "SELECT id FROM catalog_$next_catalog WHERE catalog_".$mtype."_id = ";
                if($mtype == $catalog_level)
		$q['catalog_product'] = "SELECT catalog_id FROM catalog_product INNER JOIN catalog_$mtype ON catalog_$mtype.id = catalog_product.catalog_id  WHERE catalog_id = ";
                
		$found = false;
		foreach($q as $key=>$value)
		{
			$q1 = "$value $catalog_id LIMIT 1";
			list($opt1, $rs1) = run_query($dbc, $q1, $mode='single', $msg='');	
			if($opt1) {$found = true; $found_case = $key; break; }
		}
		// If this category has been found in any one of the above query we can not delete it.		
		if($found) {$out['myreason'] = 'Catalog  entered in <b>'.$found_case.'</b> so could not be deleted.'; return $out;}
		
		//Running the deletion queries
		$delquery = array();
		$delquery['location'] = "DELETE FROM catalog_$mtype  WHERE id = $catalog_id LIMIT 1";
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>"$key query failed");
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Catalog successfully deleted');
	}
        public function get_catalog_category_se_data()
	{  
		$d1 = array();
		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		//$d1['csess'] = $_SESSION[SESS.'csess'];	
		
		$d1['myreason'] = 'Please fill all the required information';
		$title = "catalog_title_".$d1[mtype];
		$d1['what'] = $_SESSION[SESS.'constant'][$title];
		return array(true,$d1);
	}
	
        ######################################## catalog save code  start here ######################################################
	public function catalog_category_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_catalog_category_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
                $mtype = $d1['mtype']; 
                $loop = $mtype - 1;
                mysqli_query($dbc, "START TRANSACTION");
                $catalogloopid = "catalog_".$loop."_id";
                $catname = "name$mtype";
                $company_id = $_SESSION[SESS.'data']['company_id'];
                $id = $d1['uid'].date('Ymdhis');
		// query to save
	        $q = "INSERT INTO catalog_$mtype (`id`, `name`,`company_id`) VALUES ('$id', '$d1[$catname]', '$company_id')";
                h1($q);
              
                    $r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$d1['what'].' table error');}
		$rId = $id;	
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'Catalog <b>'.$d1['name'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
        public function catalog_category_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_catalog_category_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		
                $mtype = $d1['mtype'];
                $loop = $mtype - 1;
                $catalogloopid = "catalog_".$loop."_id";
                $catname = "name$mtype";
                $company_id = $_SESSION[SESS.'data']['company_id'];
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE catalog_$mtype SET `name` = '$d1[$catname]', company_id = '$company_id' WHERE id='$id'";
             
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$d1['what'].' Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		$rId = $id;
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'item <strong>'.$d1['itemname'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated', 'rId'=>$rId);
	}	
        public function get_catalog_category_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
                if(isset($_POST['mtype'])) $mtype = $_POST['mtype'];
                if(isset($_GET['mtype']))  $mtype = $_GET['mtype'];
                $loop = $mtype - 1;
                $str = '';
//                for($k = $mtype; $k>=1; $k--)
//               {
//                   $str .= ",catalog_$k.name AS name$k,catalog_$k.id AS catalog_".$k."_id ";
//               }
                $q = "SELECT * FROM catalog_2 $filterstr";
                //h1($q);
//                for($i = $mtype; $i>1;$i--)
//                {
//                    $j = $i - 1; 
//                    $q .= "INNER JOIN catalog_$j ON catalog_$i.catalog_".$j."_id = catalog_$j.id ";
//                }
//               $q .= "$filterstr";
                //h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row["id"];
			$out[$id] = $row; // storing the item id
                           
		}
		return $out;
	}
        public function get_catalog_rate_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
                    $q = "select DISTINCT(state.stateid),statename from state INNER JOIN catalog_product_rate_list ON state.stateid = catalog_product_rate_list.stateId $filterstr ";             
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row["stateid"];
			$out[$id] = $row;
                               $q=" SELECT cprl.*,CONCAT_WS(' ',c2.name,cp.name,cp.unit)as name from catalog_product_rate_list cprl INNER JOIN catalog_product cp ON cp.id = cprl.catalog_product_id INNER JOIN catalog_2 c2 ON c2.id = cp.catalog_id  where stateId = '$id' ";
                                $out[$id]['rate_list'] = $this->get_rate_list($q);                           
		}               
		return $out;
	}
        public  function get_rate_list($q){
            global $dbc;
            $res = mysqli_query($dbc, $q);
            while($row = mysqli_fetch_array($res)){
               $id = $row['catalog_product_id'].$row['stateId'];
                $out[$id] = $row;
            }
            return $out;
        }
        
        public function get_catalog_rate_se_data()
	{  
		$d1 = array();
		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		//$d1['csess'] = $_SESSION[SESS.'csess'];	
		
		$d1['myreason'] = 'Please fill all the required information';	
		$d1['what'] = "Catalog Rate List" ;
		return array(true,$d1);
	}
	
        ######################################## catalog save code  start here ######################################################
	public function catalog_rate_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_catalog_rate_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
	
                mysqli_query($dbc, "START TRANSACTION");               
                $company_id = $_SESSION[SESS.'data']['company_id'];
                //pre($d1); exit;
                foreach($d1['name'] as $key => $value){
                    $str .= "('{$d1[product_id][$key]}','{$d1[state_id]}','{$d1[rate][$key]}','{$d1[tax][$key]}','1'),";
                }
                $str = rtrim($str,',');
               
	       $q = "INSERT INTO catalog_product_rate_list (`catalog_product_id`, `stateId`, `rate`, `taxId`,`company_id`) VALUES $str ";
                    $r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$d1['what'].' table error');}
		$rId = $id;	
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'Catalog <b>'.$d1['name'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
        public function catalog_rate_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_catalog_rate_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
	
                mysqli_query($dbc, "START TRANSACTION");      
             $q = "delete from catalog_product_rate_list where stateId ='$d1[state_id]' ";
             $r  = mysqli_query($dbc, $q);
                $company_id = $_SESSION[SESS.'data']['company_id'];
                //pre($d1); exit;
                foreach($d1['name'] as $key => $value){
                    $str .= "('{$d1[product_id][$key]}','{$d1[state_id]}','{$d1[rate][$key]}','{$d1[tax][$key]}','1'),";
                }
                $str = rtrim($str,',');
               
	      $q = "INSERT INTO catalog_product_rate_list (`catalog_product_id`, `stateId`, `rate`, `taxId`,`company_id`) VALUES $str ";  
                    $r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$d1['what'].' table error');}
		$rId = $id;	
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'Catalog <b>'.$d1['name'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}	
	
        
}// class end here
?>