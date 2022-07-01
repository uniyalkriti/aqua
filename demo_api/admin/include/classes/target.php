<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class target extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	
	 public function get_target_se_data()
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
	public function target_save()
	{
         global $dbc;  
    $file = $_FILES['upload']['tmp_name'];
    $handle = fopen($file, "r");
    if ($file == NULL) {
      
      error(_('Please select a file to import'));
     // redirect(page_link_to('admin_export'));
    }
    else {
       
        $i=0;
       while(($filesop = fgetcsv($handle, 1000, ",")) !== false)
        {
           if($i>=1)
           {
          $dealer_id = $filesop[0];
          $april = $filesop[1];
          $may = $filesop[2];
          $june = $filesop[3];
          $july = $filesop[4];
          $aug = $filesop[5];
          $sept = $filesop[6];
          $oct = $filesop[7];
          $nov = $filesop[8];
          $dec = $filesop[9];
          $jan = $filesop[10];
          $feb = $filesop[11];
          $march = $filesop[12];

       $sql = "INSERT INTO `dealer_target`(`dealer_id`, `april`,`may`, `june`, `july`, 
           `august`, `september`, `october`, `november`, `december`, `january`, `february`, `march`, `action`) VALUES
       ('$dealer_id','$april','$may','$june','$july','$aug','$sept','$oct','$nov','$dec'
          ,'$jan','$feb','$march','1')";
      // h1($sql);
       $q = mysqli_query($dbc,$sql);
           }
           
           $i++;
        
        }
    }          
return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
        public function target_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_target_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		//pre($d1);
                $mtype = $d1['mtype'];
                $loop = $mtype - 1;
                $catalogloopid = "catalog_".$loop."_id";
                $catname = "name$mtype";
                $company_id = $_SESSION[SESS.'data']['company_id'];
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE `dealer_target` SET `april`='$d1[april]',
                    `may`='$d1[may]',`june`='$d1[june]',`july`='$d1[july]',`august`='$d1[aug]',
                        `september`='$d1[sept]',
                    `october`='$d1[oct]',`november`='$d1[nov]',`december`='$d1[dec]',`january`='$d1[jan]',
                        `february`='$d1[feb]',`march`='$d1[march]' WHERE id='$id'";
             
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
        public function get_target_list($filter='',  $records = '', $orderby='')
	{
		
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM dealer_target  $filterstr ";
               // h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['id'];
                        $row['name'] = $this->getDealer($row['dealer_id']);
			$out[$id] = $row; // storing the item id
		}
             //   pre($out);
		return $out;
	}
          public function getDealer($did)
        {
            global $dbc;
           //$out = array();	
           $q = "SELECT * FROM `dealer` where id=$did";
           $rs = mysqli_query($dbc,$q);
           while($row = mysqli_fetch_assoc($rs))
                {
                    $name = $row['name'];
                }
                return $name ;
            
        }
}// class end here
?>