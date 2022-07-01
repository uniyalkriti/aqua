<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class scheme extends myfilter
{
	public $poid = NULL;
	public function __construct()
	{
		parent::__construct();
	}

       ##################################################SCHEME STARTS HERE ############################################
        public function get_scheme_se_data()
        {
            $d1 = $_POST;
            $d1['uid'] = $_SESSION[SESS . 'data']['id'];
            $d1['myreason'] = 'Please fill all the required information';
            $d1['what'] = 'Scheme'; //whether to do history log or not
            return array(true, $d1);
        }
    
    public function scheme_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_scheme_se_data();
                
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		//query to save
		$q = "INSERT INTO `scheme` (`scheme_id`, `scheme_name`, `created`, `intype`) VALUES (NULL , '$d1[scheme_name]', NOW(), '$d1[intype]')";
               
		$r = mysqli_query($dbc,$q) ;
		if(!$r){ mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Scheme could not be saved, some error occurred');}
		$rId = mysqli_insert_id($dbc);
                $str = array();
               
                if(!empty($d1['product_id']))
                {
                    foreach($d1['product_id'] as $key=>$value)
                    {
                        $start_date = !empty($d1['start_date'][$key]) ?  get_mysql_date($d1['start_date'][$key]) : '';
                        $end_date =  !empty($d1['end_date'][$key]) ?  get_mysql_date($d1['end_date'][$key]) : '';
                        $str[] = '(NULL,\''.$rId.'\',\''.$value.'\',\''.$d1['buy_quantity'][$key].'\',\''.$d1['scheme_quantity'][$key].'\',\''.$start_date.'\',\''.$end_date.'\')';
                    }
                }
                
                $str = implode(',' , $str);
                $q = "INSERT INTO `scheme_product_details` (`id`, `scheme_id`, `product_id`, `buy_quantity`, `scheme_quantity`, `start_date`, `end_date`) VALUES $str";
                $r = mysqli_query($dbc,$q) ;
                if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Scheme could not be saved, some error occurred');}
                mysqli_commit($dbc);
	     //Final success
                return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function scheme_edit($id)
	{
		global $dbc;	 
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_scheme_se_data();
               
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE `scheme` SET scheme_name = '$d1[scheme_name]', created = NOW(), intype = '$d1[intype]' WHERE scheme_id = '$id'";
                     $r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Scheme could not be updated some error occurred.');}
		mysqli_commit($dbc);
		$rId = $id;
                     $q = "DELETE FROM scheme_product_details WHERE scheme_id = '$id'";
                     $r = mysqli_query($dbc, $q);
                     if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Scheme could not be updated some error occurred.');}
                $str = array();
                if(!empty($d1['product_id']))
                {
                    foreach($d1['product_id'] as $key=>$value)
                    {
                        $start_date = !empty($d1['start_date'][$key]) ?  get_mysql_date($d1['start_date'][$key]) : '';
                        $end_date =  !empty($d1['end_date'][$key]) ?  get_mysql_date($d1['end_date'][$key]) : '';
                        $str[] = '(NULL,\''.$rId.'\',\''.$value.'\',\''.$d1['buy_quantity'][$key].'\',\''.$d1['scheme_quantity'][$key].'\',\''.$start_date.'\',\''.$end_date.'\')';
                    }
                }
                $str = implode(',' , $str);
                $q = "INSERT INTO `scheme_product_details` (`id`, `scheme_id`, `product_id`, `buy_quantity`, `scheme_quantity`, `start_date`, `end_date`) VALUES $str";
                $r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Scheme could not be saved, some error occurred');}
                mysqli_commit($dbc);
		//Saving the user modification history
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated', 'rId'=>$rId);
	}	
	
	public function get_scheme_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		//if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM scheme $filterstr";
                //h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
                     $product_map=  get_my_reference_array('catalog_product','id','name');
                     $state_map=  get_my_reference_array('location_2','id','name');
                    while($row = mysqli_fetch_assoc($rs))
                    {
                           $id = $row['scheme_id'];
                           $out[$id] = $row;
                           $q="SELECT *,catalog_product.name,DATE_FORMAT(start_date, '%d/%m/%Y') AS start_date, DATE_FORMAT(end_date, '%d/%m/%Y') AS end_date FROM scheme_product_details INNER JOIN catalog_product ON catalog_product.id = scheme_product_details.product_id INNER JOIN catalog_2 ON catalog_product.catalog_id = catalog_2.id WHERE scheme_id = '$id' ORDER BY catalog_2.name ASC ";

                          $out[$id]['scheme_product'] = $this->get_my_reference_array_direct($q, 'id');
                    }
                    //pre($out);
		return $out;
               
	} 
        public function scheme_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
		if(empty($filter)) $filter = "id = '$id'";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_scheme_list($filter, $records, $orderby);
                
		if(empty($deleteRecord)){ $out['myreason'] = 'Scheme not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		
		//Running the deletion queries
		$delquery = array();
		$delquery['scheme'] = "DELETE FROM scheme WHERE id = '$id'";
                
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>'$key query failed');
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
                return array('status'=>true, 'myreason'=>'Scheme successfully deleted');
	}
        public function get_scheme_dealer_list($filter = '', $records = '', $orderby = '') 
        {
            global $dbc;
            $filterstr = $this->oo_filter($filter, $records, $orderby);
            $out = array();
            $scheme_level = $_SESSION[SESS . 'constant']['scheme_level'];
            $dealer_level = $_SESSION[SESS . 'constant']['dealer_level'];
            $q = "SELECT dealer.* FROM location_$scheme_level ";
            for($i = $scheme_level; $i < $dealer_level; $i++)
            {
                $nextlevel = $i + 1;
                $q .= " INNER JOIN location_$nextlevel ON location_$i.id = location_$nextlevel.location_".$i."_id";
            }
            $q .= " INNER JOIN dealer_location_rate_list ON dealer_location_rate_list.location_id = location_$dealer_level.id INNER JOIN dealer ON dealer.id = dealer_location_rate_list.dealer_id $filterstr";
            //echo $q;
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if(!$opt) return $out;  
            while ($row = mysqli_fetch_assoc($rs)) {
                $id = $row['id'];
                $out[$id] = $row;
            }
            return $out;
    }
    public function get_scheme_dealer_se_data()
    {
            $d1 = $_POST;
            $d1['uid'] = $_SESSION[SESS . 'data']['id'];
            $d1['myreason'] = 'Please fill all the required information';
            $d1['what'] = 'Dealer Scheme'; //whether to do history log or not
            return array(true, $d1);
    }
    public function dealer_scheme_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_scheme_dealer_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
                $q = "INSERT INTO `scheme_dealer` (`sd_id`, `scheme_id`, `country_id`, `location_id`, `flag`) VALUES (NULL, '$d1[scheme_id]', '$d1[location_1_id]', '$d1[location_2_id]', '0')";
                $r = mysqli_query($dbc,$q);
                if(!$r){ mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Scheme could not be saved, some error occurred');}
                $rId = mysqli_insert_id($dbc);
                
                $str = array();
                if(!empty($d1['dealer_id']))
                {
                    foreach($d1['dealer_id'] as $key=>$value)
                    {
                      
                        $str[] = '(NULL,\''.$rId.'\',\''.$value.'\')';
                    }
                }
                $str = implode(',' , $str);
		//query to save
		$q = "INSERT INTO `scheme_dealer_details` (`sc_details_id`, `sd_id`, `dealer_id`) VALUES $str";
		$r = mysqli_query($dbc,$q);
		if(!$r){ mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Scheme could not be saved, some error occurred');}
                mysqli_commit($dbc);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	 public function dealer_scheme_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_scheme_dealer_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
                $q = "UPDATE scheme_dealer SET scheme_id = '$d1[scheme_id]', country_id = '$d1[location_1_id]', location_id = '$d1[location_2_id]', flag='0' WHERE sd_id = '$id'";
                $r = mysqli_query($dbc,$q);
                if(!$r){ mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Scheme could not be saved, some error occurred');}
                $rId = $id;
                $q = "DELETE FROM scheme_dealer_details WHERE sd_id = '$rId'";
                $r = mysqli_query($dbc, $q);
                $str = array();
                if(!empty($d1['dealer_id']))
                {
                    foreach($d1['dealer_id'] as $key=>$value)
                    {
                      
                        $str[] = '(NULL,\''.$rId.'\',\''.$value.'\')';
                    }
                }
                $str = implode(',' , $str);
		//query to save
		$q = "INSERT INTO `scheme_dealer_details` (`sc_details_id`, `sd_id`, `dealer_id`) VALUES $str";
		$r = mysqli_query($dbc,$q);
		if(!$r){ mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Scheme could not be saved, some error occurred');}
                mysqli_commit($dbc);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
        public function get_dealer_scheme_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM scheme_dealer INNER JOIN scheme_dealer_details USING(sd_id) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
                $scheme_map = get_my_reference_array('scheme', 'scheme_id', 'scheme_name');
                $state_map = get_my_reference_array('location_2', 'id', 'name');
                while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['sd_id'];
			$out[$id] = $row;
                        $out[$id]['location_1_id'] = $row['country_id'];
                        $out[$id]['location_2_id'] = $row['location_id'];
                        $out[$id]['scheme_name'] = $scheme_map[$row['scheme_id']];
                        $out[$id]['state'] = $state_map[$row['location_id']];
                        $out[$id]['scheme_dealer_details'] = $this->get_my_reference_array_direct($q="SELECT * FROM scheme_dealer_details INNER JOIN dealer ON dealer.id = scheme_dealer_details.dealer_id WHERE sd_id = '$id'", 'sc_details_id');
		}
		return $out;
               
	} 
  }// class end here
?>