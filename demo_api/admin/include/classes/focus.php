<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class focus extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	

	######################################## catalog code delete start here ######################################################
	public function get_catalog_deletion_list($filter='',  $records = '', $orderby='',$mtype)
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM focus  $filterstr ";
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
        public function focus_delete($id, $filter='', $records='', $orderby='')
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
		$q['catalog_product'] = "SELECT id FROM focus";
                
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
		$delquery['location'] = "DELETE FROM focus  WHERE id = $catalog_id LIMIT 1";
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
        public function get_focus_se_data()
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
	public function focus_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_focus_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		$loop = $mtype - 1;
                mysqli_query($dbc, "START TRANSACTION");
            
	        $q = "INSERT INTO focus (`product_id`) VALUES ('$d1[catalog_product]')";
               // h1($q);
              
                    $r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$d1['what'].' table error');}
		$rId = $id;	
		mysqli_commit($dbc);
		
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
       
        public function get_focus_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
                if(isset($_POST['mtype'])) $mtype = $_POST['mtype'];
                if(isset($_GET['mtype']))  $mtype = $_GET['mtype'];
                $loop = $mtype - 1;
                $str = '';
//             
                $q = "SELECT * FROM focus $filterstr";
            
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row["id"];
			$out[$id] = $row; // storing the item id
                           
		}
		return $out;
	}
     
       
        
}// class end here
?>