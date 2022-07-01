<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class threshold extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	
	######################################## catalog start here ######################################################		
	public function get_threshold_se_data()
	{  
		$d1 = array();
		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'id'];
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Threshold';
		return array(true,$d1);
	}
	######################################## threshold save code  start here ######################################################
	public function threshold_save()
	{
		global $dbc;

		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_threshold_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to save

        $id = $_SESSION[SESS.'data']['dealer_id'].date('Ymdhis');
        $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
        $str_arr = array();

        if(!empty($d1['product_id']))
        {
            foreach($d1['product_id'] as $key=>$value)
            {
                $tp_q = "SELECT id FROM `threshold` WHERE product_id='$value' AND dealer_id='$dealer_id'";
                $tp_e = mysqli_query($dbc,$tp_q);

                $trs_product = mysqli_num_rows($tp_e);

                if(!$trs_product)
                {
	                $str_arr[] = '(NULL,\''.$dealer_id.'\',\''.$value.'\',\''.$d1['buy_quantity'][$key].'\',\''.$d1['max_qty'][$key].'\')';
                }else{
                	$new_qty = $d1['buy_quantity'][$key];
                        $max_qty = $d1['max_qty'][$key];

                	$tp_u_q = "UPDATE `threshold` SET `qty` = '$new_qty',`max_qty`='$max_qty' WHERE `product_id` = '$value' AND `dealer_id` = '$dealer_id'";                	
	                mysqli_query($dbc,$tp_u_q);
                }
            }
        }

        if(count($str_arr)>0)
        {
	        $str = implode(',' , $str_arr);
			$q = "INSERT INTO `threshold` (`id`,`dealer_id`, `product_id`, `qty`,`max_qty`) VALUES $str";
			$r = mysqli_query($dbc,$q) ;
			if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>$d1['what'].'Van table error');}
			$rId = $id;
        }
		
		mysqli_commit($dbc);
             
		//Logging the history		
		//history_log($dbc, 'Add', 'Catalog <b>'.$d1['name'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	######################################## threshold code edit start here ######################################################
	public function threshold_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_threshold_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		$originaldata = $this->get_threshold_list("thresholdId = $id");
                // echo $id; exit;
		$originaldata = $originaldata[$id];
		//$modifieddata = $this->get_modified_data($originaldata, $d1);
		//if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
                    $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE threshold SET `product_id` = '$d1[product_id]', qty = '$d1[qty]' WHERE id='$id'";
              //  h1($q);
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$d1['what'].'Van Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		$rId = $id;
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'item <strong>'.$d1['itemname'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated', 'rId'=>$rId);
	}	
	######################################## threshold list code  start here ######################################################
	public function get_threshold_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$dealer_id = $_SESSION[SESS.'data']['dealer_id'];
		$state_id  = $_SESSION[SESS.'data']['state_id'];

		// $q = "SELECT * FROM threshold WHERE dealer_id=$dealer_id ORDER BY id ASC";

		$q = "SELECT catalog_view.product_id as pid,catalog_view.itemcode as itemcode, catalog_view.product_name, s.qty,s.max_qty FROM `catalog_view` LEFT JOIN `threshold` s ON catalog_view.product_id=s.product_id AND dealer_id=$dealer_id ORDER BY catalog_view.c1_id,pid ASC";


		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		
		if(!$opt) return $out; // if no order placed send blank array

		while($row = mysqli_fetch_assoc($rs))
		{
            $id = $row['pid'];
            //$row['name'] = //$this->getProduct($row['product_id']);
            $out[$id] = $row; // storing the item id
                        
		}
        
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
