<?php

class csa extends myfilter {

    public $id = NULL;
    public $id_detail = NULL;

    public function __construct($id = NULL) {
        parent::__construct();
        $this->id = $id;
    }

    ######################################## WORK DEALER Starts here ####################################################

    public function get_csa_se_data() {
        $d1 = $_POST;

        $d1['uid'] = $_SESSION[SESS . 'data']['id'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'csa'; //whether to do history log or not
        return array(true, $d1);
    }

    public function csa_save() {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');

        list($status, $d1) = $this->get_csa_se_data();
        if (!$status)
            return array('status' => false, 'myreason' => $d1['myreason']);
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        $q = "INSERT INTO `csa` (`csa_name`,`state_id`, `active_status`, `email`, `mobile`, `adress`, `csa_ss`, `line`,`town`,`contact_person`,`created_date_time`) 
			VALUES ('$d1[csa_name]', '$d1[state_id]', '$d1[active_status]', '$d1[email]', '$d1[mobile]', '$d1[adress]', '$d1[csa_ss]','$d1[line]','$d1[town]','$d1[contact_person]',NOW());";

        $r = mysqli_query($dbc, $q);
        // h1($q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'CSA Could not be saved, some error occurred');
        }
        $rId = mysqli_insert_id($dbc);
      
        mysqli_commit($dbc);
      
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }
   

    public function csa_edit($id) {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_csa_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
 
      
        mysqli_query($dbc, "START TRANSACTION");
        // query to update
        $q = "UPDATE csa SET csa_name = '$d1[csa_name]', state_id = '$d1[state_id]', active_status = '$d1[active_status]', email = '$d1[email]', mobile = '$d1[mobile]', adress = '$d1[adress]', csa_ss = '$d1[csa_ss]', line = '$d1[line]', town='$d1[town]',contact_person='$d1[contact_person]' WHERE c_id = '$id'";
        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'CSA Table error');
        }
      
        mysqli_commit($dbc);
      
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated');
    }

   
    public function get_csa_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();       
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT * FROM csa $filterstr";
    // h1($q);
         list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
         if (!$opt)
            return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['c_id'];
            $out[$id] = $row; // storing the item id
       
         }

        return $out;
    }
   

    public function csa_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
		if(empty($filter)) $filter = "csa.c_id = '$id'";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_csa_list($filter, $records, $orderby);
                
		if(empty($deleteRecord)){ $out['myreason'] = 'CSA not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		
		//Running the deletion queries
		$delquery = array();
		$delquery['csa_details'] = "UPDATE csa SET active_status = '0' WHERE c_id = '$id'";
                
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>'$key query failed');
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
                return array('status'=>true, 'myreason'=>'CSA successfully deleted');
	}
    
    
    
}

?>
