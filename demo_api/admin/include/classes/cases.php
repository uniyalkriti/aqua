<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class cases extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	
	 public function get_cases_se_data()
	{  
		$d1 = array();
		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		//$d1['csess'] = $_SESSION[SESS.'csess'];	
		$d1['myreason'] = 'Please fill all the required information';
		$title = "cases".$d1;
		$d1['what'] = $_SESSION[SESS.'constant'][$title];
		return array(true,$d1);
	}
	
        ######################################## catalog save code  start here ######################################################
	public function cases_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_cases_se_data();
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
               // pre($d1);
	        $q = "INSERT INTO cases (`product_id`,`piece`,`cases`) VALUES ('$d1[product_id]','$d1[piece]','$d1[case]')";
                      
                $r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$d1['what'].' table error');}
		$rId = $id;	
		mysqli_commit($dbc);
		//Logging the history		
		//history_log($dbc, 'Add', 'Catalog <b>'.$d1['name'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
        public function cases_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_cases_se_data();
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
		$q="UPDATE cases SET `cases` = '$d1[case]',`piece` = '$d1[piece]'WHERE id='$id'";
             
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
        public function get_cases_list($filter='',  $records = '', $orderby='')
	{
		
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM cases  $filterstr ";
               // h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['id'];
                        $row['name'] = $this->getProduct($row['product_id']);
			$out[$id] = $row; // storing the item id
		}
             //   pre($out);
		return $out;
	}
          public function getProduct($product_id)
        {
            global $dbc;
           //$out = array();	
            $q = "SELECT * FROM `catalog_product` where id=$product_id";
             $rs = mysqli_query($dbc,$q);
           while($row = mysqli_fetch_assoc($rs))
                {
                    $name = $row['name'];
                }
                return $name ;
            
        }
}// class end here
?>