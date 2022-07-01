<?php
class daily_attendance extends myfilter {

    public $id = NULL;
    public $id_detail = NULL;

    public function __construct($id = NULL) {
        parent::__construct();
        $this->id = $id;
    }
   
################################################################################
public function get_daily_attendance_se_data()
	{  
		$d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS.'data']['id'];
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = ' Tracking Address'; //whether to do history log or not
		return array(true,$d1);
	}
	public function daily_attendance_save()
	{
       // h1(rahul);
      //  print_r($_POST);
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
        list($status, $d1) = $this->get_daily_attendance_se_data();
        
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
    
        foreach($d1['uda_id'] AS $k=>$v){
            $p=$v;
            $remarks=$d1['remarks'][$k];
            $lat_lng=$d1['lat_lng'][$k];
            $attn_address=$d1['attn_address'][$k];
            $q = "UPDATE `user_daily_attendance` SET  `lat_lng`='$lat_lng',`track_addrs`='$attn_address',
            `remarks`='$remarks' WHERE id='$p' ";
        //       h1($q);         
                $r = mysqli_query($dbc,$q) ;
        }
        //query to update user daily track address with lat lang //
	
		
        if(!$r)
        {
          
        mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Tracking Address could not be saved, some error occurred');}
		mysqli_commit($dbc);
		//Logging the history		
		history_log($dbc, 'Add', 'Tracking Address <b>'.$d1['name'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}

###########################################################################
public function get_daily_attendance_list($filter='',  $records = '', $orderby='')
{
    global $dbc;
    $out = array();	
    $filterstr = $this->oo_filter($filter, $records, $orderby);
            $q1 = "SELECT uda.* ,DATE_FORMAT(uda.work_date, '%d/%m/%Y') AS work_date  FROM `user_daily_attendance` as uda $filterstr   and 
            (track_addrs='' or track_addrs=', , , ') group by user_id";
        //  $filterstr  ";
    //  h1($q1);
           // $product = array();
            list($opt1, $rs1) = run_query($dbc, $q1, $mode='multi', $msg='');
            if($opt1)
            {
                 while($row = mysqli_fetch_assoc($rs1)){
                        $id = $row['id'];                        
                        $out[$id] = $row;	
          
          // $out[$id]['p_role_name'] = myrowval('_role','rolename',"role_id=".$row['prole_id']);
          // $out[$id]['user_beat'] = $this->get_user_beat($row['pid']);   
               
                }
            }
          
         //  pre($out);
    return $out;
}
/******/
###########################################################################
public function get_daily_tracking_list($filter='',  $records = '', $orderby='')
{
   global $dbc;
  // $dbc=mysqli_connect('162.222.39.114','root','Dt#Nz%EPe!B5@f1bf','msell_baidyanath');
    $out = array();	
    $filterstr = $this->oo_filter($filter, $records, $orderby);
            $q1 = "SELECT udt.* ,  DATE_FORMAT(udt.track_date, '%d/%m/%Y') AS track_date   FROM `user_daily_tracking` as udt $filterstr and 
            track_address='' or track_address=',,,,'  order by user_id ";
        //  $filterstr  ";
     // h1($q1);
           // $product = array();
            list($opt1, $rs1) = run_query($dbc, $q1, $mode='multi', $msg='');
            if($opt1)
            {
                 while($row = mysqli_fetch_assoc($rs1)){
                        $id = $row['id'];                        
                        $out[$id] = $row;	
          
          // $out[$id]['p_role_name'] = myrowval('_role','rolename',"role_id=".$row['prole_id']);
          // $out[$id]['user_beat'] = $this->get_user_beat($row['pid']);   
               
                }
            }
          
         //  pre($out);
    return $out;
}
###########################################
################################################################################
public function get_daily_tracking_se_data()
	{  
		$d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS.'data']['id'];
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = ' Tracking Address'; //whether to do history log or not
		return array(true,$d1);
	}
	public function daily_tracking_save()
	{
       
      //  print_r($_POST);
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
        list($status, $d1) = $this->get_daily_tracking_se_data();
        
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
    
        foreach($d1['uda_id'] AS $k=>$v){
            $p=$v;
            
            $lat_lng=$d1['lat_lng'][$k];
            $attn_address=$d1['attn_address'][$k];
            $q = "UPDATE `user_daily_tracking` SET  `lat_lng`='$lat_lng',`track_address`='$attn_address'
            WHERE id='$p' ";
             //  h1($q);         
                $r = mysqli_query($dbc,$q) ;
        }
        //query to update user daily track address with lat lang //
	
		
        if(!$r)
        {
          
        mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Tracking Address could not be saved, some error occurred');}
		mysqli_commit($dbc);
		//Logging the history		
		history_log($dbc, 'Add', 'Tracking Address <b>'.$d1['name'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
##############DAILY CHECKOUT ###########################################
###############checkout##2019-02-07####

public function get_daily_checkout_se_data()
	{  
		$d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS.'data']['id'];
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = ' Tracking Address'; //whether to do history log or not
		return array(true,$d1);
	}
	public function daily_checkout_save()
	{
       // h1(rahul);
   //     print_r($_POST);
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
        list($status, $d1) = $this->get_daily_checkout_se_data();
        
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
    
        foreach($d1['uda_id'] AS $k=>$v){
            $p=$v;
            $remarks=$d1['remarks'][$k];
            $lat_lng=$d1['lat_lng'][$k];
            $attn_address=$d1['attn_address'][$k];
            $q = "UPDATE `check_out` SET  `lat_lng`='$lat_lng',`attn_address`='$attn_address',
            `remarks`='$remarks' WHERE id='$p' ";
            //   h1($q); // die;       
                $r = mysqli_query($dbc,$q) ;
        }
        //query to update user daily track address with lat lang //
	
		
        if(!$r)
        {
          
        mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Tracking Address could not be saved, some error occurred');}
		mysqli_commit($dbc);
		//Logging the history		
		history_log($dbc, 'Add', 'Tracking Address <b>'.$d1['name'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}

##############

public function get_daily_checkout_list($filter='',  $records = '', $orderby='')
{
    global $dbc;
    $out = array();	
    $filterstr = $this->oo_filter($filter, $records, $orderby);
  // pre($filter);
            $q1 = "SELECT check_out.* ,  DATE_FORMAT(check_out.work_date, '%d/%m/%Y') AS work_date from `check_out`  $filterstr   and (attn_address=', , ,'  OR 
            attn_address='')  group by user_id";
        //  $filterstr  ";
          // h1($q1);
           // $product = array();
            list($opt1, $rs1) = run_query($dbc, $q1, $mode='multi', $msg='');
            if($opt1)
            {
                 while($row = mysqli_fetch_assoc($rs1)){
                        $id = $row['id'];                        
                        $out[$id] = $row;	
          
          // $out[$id]['p_role_name'] = myrowval('_role','rolename',"role_id=".$row['prole_id']);
          // $out[$id]['user_beat'] = $this->get_user_beat($row['pid']);   
               
                }
            }
          
         //  pre($out);
    return $out;
}
/******/

       
}

?>
