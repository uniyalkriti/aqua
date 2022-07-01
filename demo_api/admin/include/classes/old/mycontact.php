<?php
class mycontact
{
	public $ad_array = array(
				'name'=>array('postfname'=>'name', 'label'=>'Person Name', 'rule'=>'', 'vtype'=>'char', 'optional'=>false),
				'email'=>array('postfname'=>'email', 'label'=>'Person Email', 'rule'=>'eamil', 'vtype'=>'char', 'optional'=>false),
				'notes'=>array('postfname'=>'notes', 'label'=>'Notes', 'rule'=>'', 'vtype'=>'char', 'optional'=>true),
				'stamp'=>array('postfname'=>'auto|insert', 'label'=>'Person Name', 'rule'=>'', 'vtype'=>'timestamp', 'optional'=>true),
				'ipadr'=>array('postfname'=>'auto|insert', 'label'=>'Person Name', 'rule'=>'', 'vtype'=>'ipadr', 'optional'=>true)
				);	
	public $er = NULL;		
				
	function d_validate($reqvalue)
	{
		$foundvalue = array();
		foreach($this->ad_array as $key => $value)
		{
			if(isset($_POST[$value['postfname']])) 
				$foundvalue[$key] = $_POST[$value['postfname']];
			elseif($value['vtype'] == 'ipadr')
				$foundvalue[$key] = $_SERVER['REMOTE_ADDR'];
			
		}
		//pre($foundvalue);
		if(count($reqvalue) <= count($foundvalue))
			return array(true, $foundvalue);	
		else
			return array(false,'Not proper values found');
	}
	
	function d_insert()
	{
		$d = array('name','email','notes');
		list($validflag, $c) = $this->d_validate($d);
		if(!$validflag) {$this->er = $c; return false;}
		$q =  "INSERT INTO contacts (id, name, email, notes, stamp, ipaddress) VALUES '$c[name]', '$c[email]', '$c[notes]', NOW(), '$c[ipadr]'";	
		if(true)
			echo $q;
		return true;
	}
}
?>