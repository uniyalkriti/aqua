<?php
class tally_case_report extends myfilter {

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
            
            $q="SELECT `tally_opening_stock`.`opening` AS apr_open,`tally_opening_stock`.`id`, `csa_id`,`csa`.`csa_name`,`csa`.`csa_code`, `tally_opening_stock`.`product_id`, `tally_catalog_product`.`name` AS `pname`, `from_date`, `to_date`, `server_date_time`,`location_view`.`l3_name`, `tally_catalog_product`.`itemcode` AS `itemcode`,`location_view`.`l3_id` AS cstate_id FROM `tally_opening_stock` INNER JOIN `csa` ON `csa`.`c_id`=`tally_opening_stock`.`csa_id` INNER JOIN `tally_catalog_product` ON `tally_catalog_product`.`id`=`tally_opening_stock`.`product_id` INNER JOIN `location_view` ON `location_view`.`l3_id`=`csa`.`state_id` $filterstr  GROUP BY `tally_opening_stock`.`product_id` ORDER BY csa_name,pname";
         //  h1($q); //exit;
    $rs = mysqli_query($dbc, $q);
    $i =1;     
    while ($row = mysqli_fetch_assoc($rs)) {
        $id = $row['id'];
        $out[$id] = $row;       
       $i++;
       $unit=myrowval('tally_catalog_product','quantity_per_case','id='.$row['product_id']);
      // h1($unit);
       $condo="`tally_opening_stock`.`csa_id`=".$row['csa_id']." AND tally_opening_stock.product_id=".$row['product_id'];
       $opening=$this->get_oopening_stock($condo,$unit);
       $openings=explode("|",$opening);
       $opening=$openings[0];
       $opening_pcs=$openings[1];

       $cond1="`tally_demo`.`csa_id`=".$row['csa_id']." AND tally_demo_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$odate."' AND date_format(ch_date,'%Y%m%d')<='".$prev_date."' AND `tally_demo`.`drcr`='CR' AND `tally_demo`.`invoice_type` IN (4,5,6,7)";
                $opurchase11=$this->get_stock($cond1,$unit);
                $opurchase1s=explode("|",$opurchase11);
                $opurchase1=$opurchase1s[0];
                $opurchase1_pcs=$opurchase1s[1];

        $cond2="`tally_demo`.`csa_id`=".$row['csa_id']." AND tally_demo_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$odate."' AND date_format(ch_date,'%Y%m%d')<='".$prev_date."' AND `tally_demo`.`drcr`='DR' AND `tally_demo`.`invoice_type` IN (4,5,6,7)";
                $opurchase22=$this->get_stock($cond2,$unit);
                $opurchase2s=explode("|",$opurchase22);
                $opurchase2=$opurchase2s[0];
                $opurchase2_pcs=$opurchase2s[1];

        $cond3="`tally_demo`.`csa_id`=".$row['csa_id']." AND tally_demo_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$odate."' AND date_format(ch_date,'%Y%m%d')<='".$prev_date."' AND `tally_demo_details`.`cr_dr`='CR' AND `tally_demo`.`invoice_type` IN (8,9)";
                $opurchase33 = $this->get_stock($cond3,$unit); 
                $opurchase3s=explode("|",$opurchase33);
                $opurchase3=$opurchase3s[0];
                $opurchase3_pcs=$opurchase3s[1];           
                $opurchase=$opurchase1-$opurchase2+$opurchase3;
                $opurchase_pcs=$opurchase1_pcs-$opurchase2_pcs+$opurchase3_pcs;

        $cond4="`tally_demo`.`csa_id`=".$row['csa_id']." AND tally_demo_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$odate."' AND date_format(ch_date,'%Y%m%d')<='".$prev_date."' AND `tally_demo`.`drcr`='CR' AND `tally_demo`.`invoice_type` IN (0,1,2,3)";
                $obilled11=$this->get_stock($cond4,$unit);
                $obilled1s=explode("|",$obilled11);
                $obilled1=$obilled1s[0];
                $obilled1_pcs=$obilled1s[1];

        $cond5="`tally_demo`.`csa_id`=".$row['csa_id']." AND tally_demo_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$odate."' AND date_format(ch_date,'%Y%m%d')<='".$prev_date."' AND `tally_demo`.`drcr`='DR' AND `tally_demo`.`invoice_type` IN (0,1,2,3)";
                $obilled22=$this->get_stock($cond5,$unit);
                $obilled2s=explode("|",$obilled22);
                $obilled2=$obilled2s[0];
                $obilled2_pcs=$obilled2s[1];

        $cond6="`tally_demo`.`csa_id`=".$row['csa_id']." AND tally_demo_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$odate."' AND date_format(ch_date,'%Y%m%d')<='".$prev_date."' AND `tally_demo_details`.`cr_dr`='DR' AND `tally_demo`.`invoice_type` IN (8,9)";
                $obilled33=$this->get_stock($cond6,$unit);
                $obilled3s=explode("|",$obilled33);
                $obilled3=$obilled3s[0];
                $obilled3_pcs=$obilled3s[1];
                $obilled=$obilled2-$obilled1+$obilled3;
                $obilled_pcs=$obilled2_pcs-$obilled1_pcs+$obilled3_pcs;

        $opening_stock=($opening+$opurchase)-$obilled;
        $opening_stock_pcs=($opening_pcs+$opurchase_pcs)-$obilled_pcs;

        $cond7="`tally_demo`.`csa_id`=".$row['csa_id']." AND tally_demo_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$start."' AND date_format(ch_date,'%Y%m%d')<='".$end."' AND `tally_demo`.`drcr`='DR' AND `tally_demo`.`invoice_type` IN (4,5,6,7)";
                $inward11 = $this->get_stock($cond7,$unit);
                $inward1s=explode("|",$inward11);
                $inward1=$inward1s[0];
                $inward1_pcs=$inward1s[1];
        $cond8="`tally_demo`.`csa_id`=".$row['csa_id']." AND tally_demo_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$start."' AND date_format(ch_date,'%Y%m%d')<='".$end."' AND `tally_demo`.`drcr`='CR' AND `tally_demo`.`invoice_type` IN (4,5,6,7)";
                $inward22 = $this->get_stock($cond8,$unit);
                $inward2s=explode("|",$inward22);
                $inward2=$inward2s[0];
                $inward2_pcs=$inward2s[1];
        $cond9="`tally_demo`.`csa_id`=".$row['csa_id']." AND tally_demo_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$start."' AND date_format(ch_date,'%Y%m%d')<='".$end."' AND `tally_demo_details`.`cr_dr`='CR' AND `tally_demo`.`invoice_type` IN (8,9)";
                $inward33 = $this->get_stock($cond9,$unit);
                $inward3s=explode("|",$inward33);
                $inward3=$inward3s[0];
                $inward3_pcs=$inward3s[1];

                $inward=$inward2-$inward1+$inward3;
                if(empty($inward)){
                    $inward=0;
                }
                $inward_pcs=$inward2_pcs-$inward1_pcs+$inward3_pcs;
                if(empty($inward_pcs)){
                    $inward_pcs=0;
                }

        $cond10="`tally_demo`.`csa_id`=".$row['csa_id']." AND tally_demo_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$start."' AND date_format(ch_date,'%Y%m%d')<='".$end."' AND `tally_demo`.`drcr`='CR' AND `tally_demo`.`invoice_type` IN (0,1,2,3)";
                $outward1=$this->get_stock($cond10,$unit);
                $outward1s=explode("|",$outward1);
                $outward1=$outward1s[0];
                $outward1_pcs=$outward1s[1];

        $cond11="`tally_demo`.`csa_id`=".$row['csa_id']." AND tally_demo_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$start."' AND date_format(ch_date,'%Y%m%d')<='".$end."' AND `tally_demo`.`drcr`='DR' AND `tally_demo`.`invoice_type` IN (0,1,2,3)";
                $outward2=$this->get_stock($cond11,$unit);
                $outward2s=explode("|",$outward2);
                $outward2=$outward2s[0];
                $outward2_pcs=$outward2s[1];
         $cond12="`tally_demo`.`csa_id`=".$row['csa_id']." AND tally_demo_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$start."' AND date_format(ch_date,'%Y%m%d')<='".$end."' AND `tally_demo_details`.`cr_dr`='DR' AND `tally_demo`.`invoice_type` IN (8,9)";
                $outward3=$this->get_stock($cond12,$unit);
                $outward3s=explode("|",$outward3);
                $outward3=$outward3s[0];
                $outward3_pcs=$outward3s[1];   
                $outward=$outward2-$outward1+$outward3;
                if(empty($outward)){
                    $outward=0;
                }
                $outward_pcs=$outward2_pcs-$outward1_pcs+$outward3_pcs;
                if(empty($outward_pcs)){
                    $outward_pcs=0;
                }                
        
        $closing=$opening_stock+$inward-$outward;
        $closing_pcs=$opening_stock_pcs+$inward_pcs-$outward_pcs;
         
       
       $rate=myrowval('tally_product_rate_list','dealer_rate','product_id='.$row['product_id'].' AND state_id='.$row['cstate_id']);  
       $rate_pcs=myrowval('tally_product_rate_list','dealer_pcs_rate','product_id='.$row['product_id'].' AND state_id='.$row['cstate_id']);       

                $out[$id]['opening'] = $opening_stock;
                $out[$id]['inward'] = $inward;
                $out[$id]['outward'] = $outward;
                $out[$id]['closing'] = $closing;
                $out[$id]['rate'] = $rate;

                $out[$id]['opening_pcs'] = $opening_stock_pcs;
                $out[$id]['inward_pcs'] = $inward_pcs;
                $out[$id]['outward_pcs'] = $outward_pcs;
                $out[$id]['closing_pcs'] = $closing_pcs;
                $out[$id]['rate_pcs'] = $rate_pcs;

              $gstcond="`tally_demo`.`csa_id`=".$row['csa_id']." AND tally_demo_details.product_id=".$row['product_id']." AND date_format(ch_date,'%Y%m%d')>='".$start."' AND date_format(ch_date,'%Y%m%d')<='".$end."' ";        
          //  $out[$id]['gst_amt']=$this->get_gst_details_stock($gstcond);
            $out[$id]['gst_rate']=$this->get_gst_percent($gstcond);
        } 
    
   // pre($out);
    return $out;
}


     public function get_gst_percent($gstcond)
    {
        global $dbc;
        $out = array();
        $q = "SELECT `gst`  FROM tally_demo_details   INNER JOIN tally_demo ON tally_demo.id=tally_demo_details.td_id WHERE $gstcond";
        //h1($q);
        $r=mysqli_query($dbc,$q);
        $rs=mysqli_fetch_assoc($r);
        return $rs['gst']; 
    }
public function get_oopening_stock($cond,$unit)
    {
        global $dbc;
        $out = array();
        $qc = "SELECT ROUND(sum(opening)) AS case_qty FROM tally_opening_stock WHERE $cond AND unit='CASE'";
       // h1($qc);
        $rc=mysqli_query($dbc,$qc);
        $rsc=mysqli_fetch_assoc($rc);
        $cqty=$rsc['case_qty'];
        if(empty($cqty))
            $cqty=0;

        $qp = "SELECT ROUND(sum(opening)) AS pcs_qty FROM tally_opening_stock WHERE $cond AND unit='PCS'";
        //h1($qp);
        $rp=mysqli_query($dbc,$qp);
        $rsp=mysqli_fetch_assoc($rp);
        if(empty($rsp['pcs_qty'])){
            $pcs=0;
            $pcs_qty=0;
        }else{
            $pcs=$rsp['pcs_qty'];
            $pcs_qty=0;
        }
        if($pcs==0 || $unit==0 || $cqty==0){
            $case_qty=0;
            $pcs_qty=$pcs;
        }else{
            $case_qty=ROUND($pcs/$unit);
            $pcs_qty=$pcs%$unit;
        }

        $qty=$cqty+$case_qty;

        return $qty."|".$pcs_qty; 
    } 
    public function get_stock($cond,$unit)
    {
        global $dbc;
        $out = array();
        $qc = "SELECT sum(qty) AS case_qty FROM tally_demo_details  INNER JOIN tally_catalog_product ON tally_demo_details.product_id=tally_catalog_product.id INNER JOIN tally_demo ON tally_demo.id=tally_demo_details.td_id WHERE $cond AND unit='CASE'";
        //h1($qc);
        $rc=mysqli_query($dbc,$qc);
        $rsc=mysqli_fetch_assoc($rc);
        $cqty=$rsc['case_qty'];
        if(empty($cqty))
            $cqty=0;

        $qp = "SELECT sum(qty) AS pcs_qty FROM tally_demo_details  INNER JOIN tally_catalog_product ON tally_demo_details.product_id=tally_catalog_product.id INNER JOIN tally_demo ON tally_demo.id=tally_demo_details.td_id WHERE $cond AND unit='PCS'";
        //h1($qp);
        $rp=mysqli_query($dbc,$qp);
        $rsp=mysqli_fetch_assoc($rp);
        if(empty($rsp['pcs_qty'])){
            $pcs=0;
            $pcs_qty=0;
        }else{
            $pcs=$rsp['pcs_qty'];
            $pcs_qty=0;
        }
        if($pcs==0 || $unit==0 || $cqty==0){
            $case_qty=0;
            $pcs_qty=$pcs;
        }else{
            $case_qty=ROUND($pcs/$unit);
            $pcs_qty=$pcs%$unit;
        }

        $qty=$cqty+$case_qty;

        return $qty."|".$pcs_qty; 
    }                  
}

?>