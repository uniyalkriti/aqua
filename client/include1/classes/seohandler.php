<?php 
/*$dbc = mysqli_connect('localhost','root','','medgyan');
// This class will handle all the task related to purchase order creation
include_once('myfilter.php');*/
class seohandler extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	//This function will prepare a clean name of the service, removing unaccepatable characters
	function _prepare_url_text($string, $seperator='-')
	{
		//echo $string;
		$not_accept = '#[^-a-zA-Z0-9_ ]#';	
		$string = preg_replace($not_accept,'', $string);
		
		$string = trim($string);
		$string = preg_replace('#[-_ ]+#', $seperator, $string);
		return $string;
	}
	//This function will return my seo friendly url
	function url_create($urlcategory, $option)
	{
		$url = BASE_URL_ROOT;
		switch($urlcategory){
			//The menu to show the school links
			case'company-add12':
			{
				if(ENABLESEO)
					$url .= 'client/company/'.$this->_prepare_url_text($option['name']).'.html';
				else
					$url .= 'index.php?option=school&id='.$option['id'].'&search=1';
				break;
			}
			case'company-add':
			{
				if(ENABLESEO)
					$url .= 'company/'.$this->_prepare_url_text($option['name']).'.html';
				else
					$url .= 'index.php?option=college';
				break;
			}
			
			case'school-view':
			{
				$url='';
				if(ENABLESEO)
					$url .= 'school/view/'.$option['id'].'/'.$this->_prepare_url_text($option['name']).'.html';
				else
					$url .= 'index.php?option=view-school&id='.$option['id'];
				break;
			}
			case'course':
			{
				if(ENABLESEO)
					$url .= 'course/'.$option['mode'].'/'.$option['id'].'/'.$this->_prepare_url_text($option['name']).'.html';
				else
					$url .= 'index.php?option=course&mode=1&id='.$option['id'];
				break;
			}
			case'view-college':
			{
			    $url='';
				if(ENABLESEO)
					$url .= 'view/college/'.$option['id'].'/'.$option['cbcId'].'/'.$this->_prepare_url_text($option['name']).'.html';
				else
					 $url .= 'index.php?option=view-college&id='.$option['id'].'&cbcId='.$option['cbcId'];
					//$college_link = 'index.php?option=view-college&cbcId='.$val['cbcId'].'&id='.$val['colgId'];  
				break;
			}
			
			case'branch description':
			{
			    $url='';
				if(ENABLESEO)
					$url .= 'branch/description/'.$option['id'].'/'.$option['cbcId'].'/'.$this->_prepare_url_text($option['name']).'.html';
				else
					 $url .= 'index.php?option=view-college&id='.$option['id'].'&cbcId='.$option['cbcId'];
					//$college_link = 'index.php?option=view-college&cbcId='.$val['cbcId'].'&id='.$val['colgId'];  
				break;
			}
			case'view-college-course':
			{
				
				if(ENABLESEO)
					$url .= 'view/college/course/'.$option['id'].'/'.$option['lastshow'].'/'.$this->_prepare_url_text($option['name']).'.html';
				else
					$url .= 'index.php?option=view-college-course&id='.$option['id'].'&lastshow='.$option['lastshow'];
					//$college_link = 'index.php?option=view-college&cbcId='.$val['cbcId'].'&id='.$val['colgId'];  
				break;
			}
			case'view-album':
			{
				
				if(ENABLESEO)
					$url .= 'view-album/'.$option['mode'].'/'.$option['id'].'/'.$this->_prepare_url_text($option['name']).'.html';
				else
				
					$url .= 'index.php?option=view-album&mode=1&id='.$option['id'];
					
					//$college_link = 'index.php?option=view-college&cbcId='.$val['cbcId'].'&id='.$val['colgId'];  
				break;
			}
			
			case'course-college':
			{
				$url = '';
				if(ENABLESEO)
					$url .= 'course/college/'.$option['mode'].'/'.$option['cbId'].'/'.'/'.$option['cId'].'/'.$this->_prepare_url_text($option['name']).'.html';
				else
					$url .= 'index.php?option=college&mode=2&cbId='.$option['cbId'].'&cId='.$option['cId'];
					//$college_link = 'index.php?option=view-college&cbcId='.$val['cbcId'].'&id='.$val['colgId'];  
				break;
			}
			case'career-activity':
			{
				$url = '';
				if(ENABLESEO)
					$url .= 'activity/'.$option['mode'].'/'.$option['id'].'/'.$this->_prepare_url_text($option['name']).'.html';
				else
					$url .= 'index.php?option=career-activity&mode=1&id='.$option['id'];
					//$college_link = 'index.php?option=view-college&cbcId='.$val['cbcId'].'&id='.$val['colgId'];  
				break;
			}
			case'view-career-detail':
			{
				$url = '';
				if(ENABLESEO)
					$url .= 'view/career/'.$option['id'].'/'.$this->_prepare_url_text($option['name']).'.html';
				else
					$url .= 'index.php?option=view-career-detail&id='.$option['id'];
					//$college_link = 'index.php?option=view-college&cbcId='.$val['cbcId'].'&id='.$val['colgId'];  
				break;
			}
			case'product':
			{
				$url = '';
				if(ENABLESEO)
					$url .= 'product/'.$option['mode'].'/'.'/'.$option['id'].'/'.$this->_prepare_url_text($option['name']).'.html';
				else
					$url .= 'index.php?option=product&mode=1&id='.$option['id'];
					//$college_link = 'index.php?option=view-college&cbcId='.$val['cbcId'].'&id='.$val['colgId'];  
				break;
			}
			case'view-suplier':
			{
				$url = '';
				if(ENABLESEO)
					$url .= 'view/supplier/'.$option['id'].'/'.$this->_prepare_url_text($option['name']).'.html';
				else
					$url .= 'index.php?option=view-suplier&id='.$option['id'];
					//$college_link = 'index.php?option=view-college&cbcId='.$val['cbcId'].'&id='.$val['colgId'];  
				break;
			}
			case'scholarship':
			{
				$url = '';
				if(ENABLESEO)
					$url .= 'scholarship/'.$option['id'].'/'.$this->_prepare_url_text($option['name']).'.html';
				else
					$url .= 'index.php?option=scholarship&id='.$option['id'];
					//$college_link = 'index.php?option=view-college&cbcId='.$val['cbcId'].'&id='.$val['colgId'];  
				break;
			}
			case'education-loan':
			{
				
				if(ENABLESEO)
					$url .=$this->_prepare_url_text($option['name']).'.html';
				else
					$url .= 'index.php?option=education-loan';
					//$college_link = 'index.php?option=view-college&cbcId='.$val['cbcId'].'&id='.$val['colgId'];  
				break;
			}
			case'bank-loan':
			{
				
				if(ENABLESEO)
					$url .= 'bank/loan/'.$option['id'].'/'.$this->_prepare_url_text($option['name']).'.html';
				else
					$url .= 'index.php?option=bank-loan&id='.$option['id'];
					//$college_link = 'index.php?option=view-college&cbcId='.$val['cbcId'].'&id='.$val['colgId'];  
				break;
			}
			case'competitive-category':
			{
				
				if(ENABLESEO)
					$url .= 'competitive/category/'.$option['mode'].'/'.'/'.$option['id'].'/'.$this->_prepare_url_text($option['name']).'.html';
				else
					$url .= 'index.php?option=competitive-category&mode='.$option['mode'].'&id='.$option['id'];
					//$college_link = 'index.php?option=view-college&cbcId='.$val['cbcId'].'&id='.$val['colgId'];  
				break;
			}
			case'exam-category-detail':
			{
				
				if(ENABLESEO)
					$url .= 'exam/category/detail/'.$option['reecId'].'/'.'/'.$option['id'].'/'.$this->_prepare_url_text($option['name']).'.html';
				else
					$url .= 'index.php?option=exam-category-detail&reecId='.$option['reecId'].'&id='.$option['id'];
					//$college_link = 'index.php?option=view-college&cbcId='.$val['cbcId'].'&id='.$val['colgId'];  
				break;
			}
			case'university':
			{
				if(ENABLESEO)
					$url .= 'university/'.$option['id'].'/'.$this->_prepare_url_text($option['name']).'.html';
				else
					$url .= 'index.php?option=university&id='.$option['id'];
				break;
			}
			case'university-view':
			{
				$url='';
				if(ENABLESEO)
					$url .= 'school/view/'.$option['id'].'/'.$this->_prepare_url_text($option['name']).'.html';
				else
					$url .= 'index.php?option=university-details&id='.$option['id'];
				break;
			}
			case'college-details':
			{
				if(ENABLESEO)
					$url .= 'college/'.$option['id'].'/'.$this->_prepare_url_text($option['name']).'.html';
				else
					$url .= 'index.php?option=college-details&id='.$option['id'];
				break;
			}
			
			//exam-category-detail
		}
		
		return $url;
	}
}	

?>