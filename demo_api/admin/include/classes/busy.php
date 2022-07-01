<?php
class busy extends myfilter {

    public $id = NULL;
    public $id_detail = NULL;

    public function __construct($id = NULL) {
        parent::__construct();
        $this->id = $id;
    }
    
     public function get_my_reference_value_direct($q, $primarykey)
	{
		global $dbc;
		$out = array();
		list($opt, $rs) = run_query($dbc, $q, 'multi');
		if(!$opt) return $out;
		while($row = mysqli_fetch_assoc($rs)){ 
        $id=$row[$primarykey];
        $out[$id]=$row;
        }               
		return $out;

	}
  
    public function get_ss_stock_list($filter = '', $records = '', $orderby = '') {
    global $dbc;
    $out = array();

    $start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
    $end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
          
     $filterstr = $this->oo_filter($filter, $records, $orderby);  
            
            $q="SELECT `tally_stock`.`id` AS `id`, `csa_id`,`csa`.`csa_name`,`csa`.`csa_code`, `tally_stock`.`product_id`, `tally_catalog_product`.`name` AS `pname`, `from_date`, `to_date`, `opening`, `inward`, `outward`, `closing`, `server_date_time`,`location_view`.`l3_name` FROM `tally_stock` INNER JOIN `csa` ON `csa`.`c_id`=`tally_stock`.`csa_id` INNER JOIN `tally_catalog_product` ON `tally_catalog_product`.`id`=`tally_stock`.`product_id` INNER JOIN `location_view` ON `location_view`.`l3_id`=`csa`.`state_id` $filterstr";
          // h1($q); //exit;
    $rs = mysqli_query($dbc, $q);
    $i =1;     
    while ($row = mysqli_fetch_assoc($rs)) {
        $id = $row['id'];
        $out[$id] = $row;       
       $i++;
    }  
    //pre($out);
    return $out;
} 
public function get_ss_bill_list($filter = '', $records = '', $orderby = '') {
    global $dbc;
    $out = array();

    $start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
    $end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
          
     $filterstr = $this->oo_filter($filter, $records, $orderby);  
            
            $q="SELECT `busy_bill`.`id`, `ch_no`, `first_ch_no`, `ch_date`, `vocher_id`, `ss_voucher_id`, `busy_bill`.`csa_id`,`csa`.`csa_code`,`csa`.`csa_name`, `dealer_id`, `dealer_name`,`dealer`.`dealer_code`,`dealer`.`name` AS `dname`,`invoice_type`, `amount`, `amount_round`, `cancel_date`, `server_date_time`,l3_name AS state,l4_name AS town FROM `busy_bill` INNER JOIN `csa` ON `csa`.`c_id`=`busy_bill`.`csa_id` INNER JOIN `dealer` ON `dealer`.`id`=`busy_bill`.`dealer_id` INNER JOIN `location_view` ON `location_view`.`l3_id`=`csa`.`state_id` $filterstr GROUP BY `busy_bill`.`id` ORDER BY `busy_bill`.`ch_date`,`busy_bill`.`ch_no` DESC";
          // h1($q); //exit;
    $rs = mysqli_query($dbc, $q);
    $i =1;     
    while ($row = mysqli_fetch_assoc($rs)) {
        $id = $row['id'];
        $out[$id] = $row; 
        $qcd="SELECT busy_bill_details.`id` AS `id`, `td_id`, `busy_bill_details`.`product_id`, `qty`, `rate`, `gst`, `gst_amt`, `dis_per`, `dis_amt`, `taxable_amt`, `item_for`, `tally_catalog_product`.`name` AS `pname` FROM `busy_bill_details` INNER JOIN `tally_catalog_product` ON `tally_catalog_product`.`id`=`busy_bill_details`.`product_id` WHERE td_id='$id'";
        //h1($qcd);
        $out[$id]['challan_details'] = $this->get_my_reference_value_direct($qcd,'id');      
       $i++;
    }  
    //pre($out);
    return $out;
}

    public function get_ss_opening_stock_list($filter = '', $records = '', $orderby = '') {
    global $dbc;
    $out = array();

    $start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
    $end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
    $prev_date = date('Ymd', strtotime($start .' -1 day'));
    $odate_year = substr($start, 0, 4);
    //$odate=$odate_year."0401";
    $odate="20100401";


    
          
     $filterstr = $this->oo_filter($filter, $records, $orderby);  
            
            $q="SELECT `busy_opening_stock`.`qty` AS apr_open,`busy_opening_stock`.`id`, `csa_id`,`csa`.`csa_name`,`csa`.`csa_code`, `busy_opening_stock`.`product_id`, `tally_catalog_product`.`name` AS `pname`,`location_view`.`l3_name`, `tally_catalog_product`.`itemcode` AS `itemcode` FROM `busy_opening_stock` INNER JOIN `csa` ON `csa`.`c_id`=`busy_opening_stock`.`csa_id` INNER JOIN `tally_catalog_product` ON `tally_catalog_product`.`id`=`busy_opening_stock`.`product_id` INNER JOIN `location_view` ON `location_view`.`l3_id`=`csa`.`state_id` $filterstr  GROUP BY `busy_opening_stock`.`product_id` ORDER BY csa_name,pname";
           //h1($q); //exit;
    $rs = mysqli_query($dbc, $q);
    $i =1;     
    while ($row = mysqli_fetch_assoc($rs)) {
        $id = $row['id'];
        $out[$id] = $row;       
       $i++;

        $condo="`busy_opening_stock`.`csa_id`=".$row['csa_id']." AND busy_opening_stock.product_id=".$row['product_id'];
            $opening=$this->get_oopening_stock($condo);

        $cond2="`busy_bill`.`csa_id`=".$row['csa_id']." AND busy_bill_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$odate."' AND date_format(ch_date,'%Y%m%d')<='".$prev_date."' AND `busy_bill_details`.`cr_dr`='CR'";
            $opurchase=$this->get_ocinward_stock($cond2); 

        $cond3="`busy_bill`.`csa_id`=".$row['csa_id']." AND busy_bill_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$odate."' AND date_format(ch_date,'%Y%m%d')<='".$prev_date."' AND `busy_bill_details`.`cr_dr`='DR'";
            $obilled=$this->get_ocoutward_stock($cond3);

        $opening_stock=$opening+$opurchase+$obilled;

        $cond1="`busy_bill`.`csa_id`=".$row['csa_id']." AND busy_bill_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$start."' AND date_format(ch_date,'%Y%m%d')<='".$end."' AND `busy_bill_details`.`cr_dr`='CR'";
            $inward = $this->get_cinward_stock($cond1);
                if(empty($inward)){
                    $inward=0;
                }
        $cond5="`busy_bill`.`csa_id`=".$row['csa_id']." AND busy_bill_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$start."' AND date_format(ch_date,'%Y%m%d')<='".$end."' AND `busy_bill_details`.`cr_dr`='DR'";
            $outward=$this->get_coutward_stock($cond5);
                if(empty($outward)){
                    $outward=0;
                }
        $closing=$opening_stock+$inward+$outward;

                $out[$id]['opening'] = $opening_stock;
                $out[$id]['inward'] = $inward;
                $out[$id]['outward'] = abs($outward);
                $out[$id]['closing'] = $closing;
    } 
    
    //pre($out);
    return $out;
}
public function get_oopening_stock($cond)
    {
        global $dbc;
        $out = array();
        $q = "SELECT ROUND(sum(qty)) AS oqty FROM busy_opening_stock INNER JOIN tally_catalog_product ON busy_opening_stock.product_id=tally_catalog_product.id WHERE $cond";
        //h1($q);
        $r=mysqli_query($dbc,$q);
        $rs=mysqli_fetch_assoc($r);
        return $rs['oqty']; 
    } 
    public function get_ocinward_stock($cond)
    {
        global $dbc;
        $out = array();
        $q = "SELECT sum(qty) AS pqty FROM busy_bill_details  INNER JOIN tally_catalog_product ON busy_bill_details.product_id=tally_catalog_product.id INNER JOIN busy_bill ON busy_bill.id=busy_bill_details.td_id WHERE $cond";
        //h1($q);
        $r=mysqli_query($dbc,$q);
        $rs=mysqli_fetch_assoc($r);
        return $rs['pqty']; 
    }
    public function get_odinward_stock($cond)
    {
        global $dbc;
        $out = array();
        $q = "SELECT sum(qty) AS pqty FROM busy_bill_details  INNER JOIN tally_catalog_product ON busy_bill_details.product_id=tally_catalog_product.id INNER JOIN busy_bill ON busy_bill.id=busy_bill_details.td_id WHERE $cond";
        //h1($q);
        $r=mysqli_query($dbc,$q);
        $rs=mysqli_fetch_assoc($r);
        return $rs['pqty']; 
    }
    public function get_ocoutward_stock($cond)
    {
        global $dbc;
        $out = array();
        $q = "SELECT sum(qty) AS bqty FROM busy_bill_details  INNER JOIN tally_catalog_product ON busy_bill_details.product_id=tally_catalog_product.id INNER JOIN busy_bill ON busy_bill.id=busy_bill_details.td_id WHERE $cond";
        //h1($q);
        $r=mysqli_query($dbc,$q);
        $rs=mysqli_fetch_assoc($r);
        return $rs['bqty']; 
    }
    public function get_odoutward_stock($cond)
    {
        global $dbc;
        $out = array();
        $q = "SELECT sum(qty) AS bqty FROM busy_bill_details  INNER JOIN tally_catalog_product ON busy_bill_details.product_id=tally_catalog_product.id INNER JOIN busy_bill ON busy_bill.id=busy_bill_details.td_id WHERE $cond";
        //h1($q);
        $r=mysqli_query($dbc,$q);
        $rs=mysqli_fetch_assoc($r);
        return $rs['bqty']; 
    }
    public function get_cinward_stock($cond)
    {
        global $dbc;
        $out = array();
       $q = "SELECT sum(qty) AS pqty FROM busy_bill_details  INNER JOIN tally_catalog_product ON busy_bill_details.product_id=tally_catalog_product.id INNER JOIN busy_bill ON busy_bill.id=busy_bill_details.td_id WHERE $cond";
        //h1($q);
        $r=mysqli_query($dbc,$q);
        $rs=mysqli_fetch_assoc($r);
        return $rs['pqty']; 
    }
    public function get_dinward_stock($cond)
    {
        global $dbc;
        $out = array();
        $q = "SELECT sum(qty) AS pqty FROM busy_bill_details  INNER JOIN tally_catalog_product ON busy_bill_details.product_id=tally_catalog_product.id INNER JOIN busy_bill ON busy_bill.id=busy_bill_details.td_id WHERE $cond";
        //h1($q);
        $r=mysqli_query($dbc,$q);
        $rs=mysqli_fetch_assoc($r);
        return $rs['pqty']; 
    }
    public function get_coutward_stock($cond)
    {
        global $dbc;
        $out = array();
        $q = "SELECT sum(qty) AS bqty FROM busy_bill_details  INNER JOIN tally_catalog_product ON busy_bill_details.product_id=tally_catalog_product.id INNER JOIN busy_bill ON busy_bill.id=busy_bill_details.td_id WHERE $cond";
        //h1($q);
        $r=mysqli_query($dbc,$q);
        $rs=mysqli_fetch_assoc($r);
        return $rs['bqty']; 
    }
    public function get_doutward_stock($cond)
    {
        global $dbc;
        $out = array();
        $q = "SELECT sum(qty) AS bqty FROM busy_bill_details  INNER JOIN tally_catalog_product ON busy_bill_details.product_id=tally_catalog_product.id INNER JOIN busy_bill ON busy_bill.id=busy_bill_details.td_id WHERE $cond";
        //h1($q);
        $r=mysqli_query($dbc,$q);
        $rs=mysqli_fetch_assoc($r);
        return $rs['bqty']; 
    }                   
}

?>