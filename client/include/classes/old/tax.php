<?php 

class tax extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}	
	
     public function get_tax_se_data()
		{
		    $d1 = array('taxname'=>$_POST['taxname'], 'taxtype'=>$_POST['taxtype'],'taxvalue'=>$_POST['taxvalue'],'taxcategory'=>$_POST['taxcategory'], 'locked'=>0,'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'sess']['ses_id']);
			$d1['myreason'] = 'Please fill all the required information';
			$d1['what'] = 'Tax'; //whether to do history log or not
			return array(true,$d1);	
		}
     public function tax_save()
       { 
			global $dbc;
            $out= array('status'=>'false','myreason'=>'');
            list($status,$d1)= $this ->get_tax_se_data();  
		 if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
			mysqli_query($dbc, "START TRANSACTION");
            $q = "INSERT INTO `tax` (`taxId`, `taxname`, `taxtype`,`taxvalue`,`taxcategory`,`locked`,`created`) VALUES (NULL , '$d1[taxname]','$d1[taxtype]', '$d1[taxvalue]','$d1[taxcategory]','$d1[locked]',NOW())";
			$r=mysqli_query($dbc, $q);
             if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreasdon'=>'Tax Table error') ;} 
			$rId = mysqli_insert_id($dbc);	
			mysqli_commit($dbc);
			return array ('status'=>true,'myreason'=>'Successfully Saved');
	   }
     public function tax_edit($id)
		 {
			 global $dbc;
			 $out = array('status'=>'false','myreason'=>'');
			 list($status,$d1)=$this->get_tax_se_data();
			 if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
			 mysqli_query($dbc, "START TRANSACTION");
             $q="UPDATE tax SET taxname='$d1[taxname]',taxtype='$d1[taxtype]', taxvalue='$d1[taxvalue]',taxcategory='$d1[taxcategory]',modified=NOW() WHERE taxId='$id'";
			 $r=mysqli_query($dbc, $q);
			  if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Tax Table error') ;} 
			$rId = mysqli_insert_id($dbc);	
			mysqli_commit($dbc);
			return array ('status'=>true,'myreason'=>'Successfully Update');

		 }
	      public function get_tax_list($filter='', $records='', $orderby='')
	   {
		   global $dbc;
		   $out = array();
           $filterstr=$this->oo_filter($filter, $records, $orderby);
		   $q="SELECT * FROM tax $filterstr";
		   list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		   if(!$opt) return $out; 
		   while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['taxId'];
			$out[$id] = $row; // storing the item id
			//$out[$id]['istatus_val'] = $GLOBALS['istatus'][$row['istatus']];
		}
		return $out;
		   
		   
	   }
}

  ?>