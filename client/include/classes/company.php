<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class company extends myfilter
{
	public $poid = NULL;
	public function __construct()
	{
		parent::__construct();
	}

	############################## Department Starts here ##############################	
	public function get_company_se_data()
	{  
		$d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS.'data']['id'];
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = ' Company'; //whether to do history log or not
		return array(true,$d1);
	}
	public function company_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_company_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		$compId = $_SESSION[SESS.'data']['dealer_id'].date('Ymdhis');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		$q = "INSERT INTO `company` (`id`, `name`, `address`, `email`, `landline`, `other_numbers`, `location_id`, `dealer_id`,`website`) VALUES ('$compId' , '$d1[name]', '$d1[address]', '$d1[email]', '$d1[landline]', '$d1[other_numbers]', '1', '{$_SESSION[SESS.'data']['dealer_id']}','$d1[website]')";
                //h1($q);
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Company could not be saved, some error occurred');}
		$rId = $compId;
                
                if($d1['cname'])
                {
                    foreach($d1['cname'] as $keys=>$values) {
                       $str[] = '(NULL, \''.$d1['cdesignation'][$keys].'\', \''.$values.'\', \''.$d1['cmobile'][$keys].'\',\''.$d1['cemail'][$keys].'\',\''.$d1['cphone'][$keys].'\',\''.$d1['cremark'][$keys].'\',\''.$rId.'\')';
                    }
                }
               
                $str=implode(',',$str);
               
             $q1="INSERT INTO `company_contact_persons` (`id`,`cdesignation`,`cname`,`cmobile`,`cemail`,`cphone`,`cremark`,`company_id`) VALUES $str";
                
                $r = mysqli_query($dbc,$q1) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Contact Persons could not be saved, some error occurred');}
               
//                $q = "INSERT INTO `_constant` SET catalog_level = '$d1[catalog_level]', location_level = '$d1[location_level]', dealer_level = '$d1[dealer_level]', retailer_level = '$d1[retailer_level]', company_id = '$rId', constant_status = '1', location_status = '$d1[location_status]', catalog_status = '$d1[catalog_status]'";
//              
//                if(!empty($d1['catalog_title']))
//                {
//                    $i = 1;
//                    foreach($d1['catalog_title'] as $key=>$value){
//                        $q .= ", catalog_title_$i = '$value'";
//                        $i++;
//                    }
//                }
//              
//                if(!empty($d1['location_title']))
//                {
//                    $i = 1;
//                    foreach($d1['location_title'] as $key1=>$value1){
//                        $q .= ", location_title_$i = '$value1'";
//                        $i++;
//                    }
//                }
//                
//                $r = mysqli_query($dbc,$q) ;
//		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Company could not be saved, some error occurred');}
		mysqli_commit($dbc);
		//Logging the history		
		history_log($dbc, 'Add', 'Company <b>'.$d1['name'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function company_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_company_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE `company` SET `name` = '$d1[name]', `address` = '$d1[address]', `email` = '$d1[email]', `landline` = '$d1[landline]', `other_numbers` = '$d1[other_numbers]',`website` = '$d1[website]'  WHERE id = '$id'";
		$r = mysqli_query($dbc,$q);
            	
                if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Company could not be updated some error occurred.');}
                $rId=$id;
                
//                $q= "DELETE FROM _constant WHERE company_id ='$rId' ";
//                $r=  mysqli_query($dbc, $q);
                
                if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'constant table error');}
                 
//                $q= "DELETE FROM company_contact_persons WHERE company_id ='$rId' ";
//                $r=  mysqli_query($dbc, $q);
                
                if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Contact Persons table error');}
                 if($d1['cname'])
                {
                    foreach($d1['cname'] as $keys=>$values) {
                        $str[] = '(NULL, \''.$d1['cdesignation'][$keys].'\', \''.$values.'\', \''.$d1['cmobile'][$keys].'\',\''.$d1['cemail'][$keys].'\',\''.$d1['cphone'][$keys].'\',\''.$d1['cremark'][$keys].'\',\''.$rId.'\')';
                    }
                }
               
                $str=implode(',',$str);
               
                $q1="INSERT INTO `company_contact_persons` (`id`,`cdesignation`,`cname`,`cmobile`,`cemail`,`cphone`,`cremark`,`company_id`) VALUES $str";
              //h1($q1);
                $r = mysqli_query($dbc,$q1) ;
		
                $q = "INSERT INTO `_constant` SET catalog_level = '$d1[catalog_level]', location_level = '$d1[location_level]', dealer_level = '$d1[dealer_level]', retailer_level = '$d1[retailer_level]', company_id = '$rId', constant_status = '1', location_status = '$d1[location_status]', catalog_status = '$d1[catalog_status]'";
              
                if(!empty($d1['catalog_title']))
                {
                    $i = 1;
                    foreach($d1['catalog_title'] as $key=>$value){
                        $q .= ", catalog_title_$i = '$value'";
                        $i++;
                    }
                }
              
                if(!empty($d1['location_title']))
                {
                    $i = 1;
                    foreach($d1['location_title'] as $key1=>$value1){
                        $q .= ", location_title_$i = '$value1'";
                        $i++;
                    }
                }
                
                $r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Company could not be saved, some error occurred');}
		
                mysqli_commit($dbc);
		$rId = $id;
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'department <strong>'.$d1['deptcode'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated', 'rId'=>$rId);
	}	
	
	public function get_company_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
                //h1($filterstr);
		 $q = "SELECT *,csa.c_id as id FROM csa where c_id IN(".$_SESSION[SESS.'data']['csa_id'].")   $filterstr ";
                //INNER JOIN company_contact_persons ON company.id=company_contact_persons.company_id 
		//h1($q);
                list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['c_id'];
			$out[$id] = $row;
                        $out[$id]['company_contact'] = $this->get_my_reference_array_direct("SELECT *,csa.c_id as id FROM csa WHERE csa_id = $id", 'id');
		}
                //pre($out);
		return $out;
	} 
	########################## Plate Planner Ends here ###########################
	public function company_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
		if(empty($filter)) $filter = "itemId = $id";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_item_list($filter, $records, $orderby);
		if(empty($deleteRecord)){ $out['myreason'] = 'Item not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		//Checking whether the invoice is deletable or not
		$q['PR'] = "SELECT itemId FROM pr_item WHERE itemId = ";
		$q['PO'] = "SELECT itemId FROM pur_order_item WHERE itemId = ";
		$found = false;
		foreach($q as $key=>$value)
		{
			$q1 = "$value $id LIMIT 1";
			list($opt1, $rs1) = run_query($dbc, $q1, $mode='single', $msg='');	
			if($opt1) {$found = true; $found_case = $key; break; }
		}
		// If this item has been found in any one of the above query we can not delete		
		if($found) {$out['myreason'] = 'Item  entered in <b>'.$found_case.'</b> so could not be deleted.'; return $out;}
		
		//Running the deletion queries
		$delquery = array();
		$delquery['stock'] = "DELETE FROM item WHERE itemId = $id LIMIT 1";
		$delquery['stock_item'] = "DELETE FROM stock_item WHERE itemId = $id";
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>'$key query failed');
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Item successfully deleted');
	}
	public function print_looper_nesting($multiId, $options=array())
	{
		global $dbc;
		$out = array();
		if(is_null($multiId) || empty($multiId)) return $out;
		
		$item = new item();
		//Explode to get an array of all the Id
		$multiId = explode('-', $multiId);
		foreach($multiId as $key=>$value){
			$id = trim($value);
			if(empty($id)) continue;
			
			//read the record statistics
			$rcdstat = $this->get_nesting_list("nestingId = $id");
			if(empty($rcdstat)) continue;
			$temp = $rcdstat[$id];
			//$out = $temp;
			$out[$id] = $temp;
			//$out[$id]['sf_item'] = $this->get_my_reference_array_direct("SELECT * FROM job_route_batch_item INNER JOIN item USING(itemId) WHERE jrbId = $temp[jrbId]", 'itemId');
			//$out[$id]['adr'] = $party->get_party_adr($temp['partyId']);*/
		}
		//pre($out);
		return $out;
	}
    public function get_constant_company_data($id)
    {
        global $dbc;
        $out = array();
        $q = "SELECT * FROM `_constant` WHERE company_id = '$id'";
        list($opt, $rs) = run_query($dbc, $q, 'single');
        if($opt) $out[$id] = $rs;
        return $out;
    }


    /* New companies for different products */

    public function ocompany_save()
    {
    	global $dbc;	
    	$out = array('status'=>false, 'myreason'=>'');    	

    	$cp_short_name = $_POST['com_code'];
    	$cp_name = $_POST['com_name'];

    	/*list($status, $d1) = $this->get_van_se_data();
    	if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);*/		
    	
    	//start the transaction
    	mysqli_query($dbc, "START TRANSACTION");
    	
    	// query to save
    	$id = $_SESSION[SESS.'data']['dealer_id'].date('Ymdhis');
    	$dealer_id = $_SESSION[SESS.'data']['dealer_id'];
		$q_max_id="SELECT max(auto) AS id FROM `other_company` LIMIT 1";
		$r_max_id=mysqli_query($dbc,$q_max_id) ;
		$run_idd=mysqli_fetch_assoc($r_max_id);
		$idd=$run_idd['id'];
    	$q = "INSERT INTO `other_company` (`id`,`cp_short_name`, `cp_name`,`dealer_id`) VALUES ('$idd','$cp_short_name', '$cp_name','$dealer_id')";
        // h1($q);
    	$r = mysqli_query($dbc,$q) ;
    	if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$d1['what'].'Company table error');}
    	$rId = $id;	

    	mysqli_commit($dbc);    		

		//Final success
    	return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
    }

    public function ocompany_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');

		// list($status,$d1)=$this->get_van_se_data();
		// if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);

		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_ocompany_list("id = $id");
		pre($originaldata);
		die;
                 
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);

		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
                    $dealer_id = $_SESSION[SESS.'data']['dealer_id'];*/

		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");

		$cp_short_name = mysqli_real_escape_string($dbc,$_POST['com_code']);
		$cp_name = mysqli_real_escape_string($dbc,$_POST['com_name']);

		// query to update 
		$q="UPDATE other_company SET cp_short_name = '$cp_short_name',`cp_name` = '$cp_name' WHERE `auto`='$id'";
		/*h1($q);
		die;*/
		
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$d1['what'].'Company Table error') ;} 

		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		$rId = $id;
		
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated', 'rId'=>$rId);
	}

    public function get_ocompany_list($filter='',  $records = '', $orderby='')
    {
    	global $dbc;
    	$out = array();		

		// if user has send some filter use them.
    	$filterstr = $this->oo_filter($filter, $records, $orderby);

    	$q = "SELECT * FROM other_company $filterstr ";
        // h1($q);
        // die;

    	list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
    			if(!$opt) return $out; // if no order placed send blank array
    			while($row = mysqli_fetch_assoc($rs))
    			{
    				$id = $row['id'];
    				$out[$id] = $row; // storing the item id
    			}
    			return $out;
    }
        
}// class end here
?>