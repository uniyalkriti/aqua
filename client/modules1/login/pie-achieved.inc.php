<?php if (!defined('BASE_URL')) die('direct script access not allowed'); ?>
<?php
include '../../include/date-picker.php';
$forma = 'Direct Invoice Details'; // to indicate what type of form this is
$formaction = $p;
$myobj = new dealer_sale();
$cls_func_str = 'challan'; //The name of the function in the class that will do the job
$myorderby = 'user_sales_order.id DESC'; // The orderby clause for fetching of the data
$myfilter = 'user_sales_order.order_id ='; //the main key against which we will fetch data in the get_item_category_function
$loc_level = $_SESSION[SESS . 'constant']['location_level'];
//Getting the user credentials for this page access
$auth = user_auth($dbc, $_SESSION[SESS . 'data']['id'], $formaction);
$userid = $_SESSION[SESS . 'data']['id'];
$sesId = $_SESSION[SESS . 'data']['id'];
$role_id = $_SESSION[SESS . 'data']['urole']; //vat_amt  surcharge taxable_amt
$state_id = $_SESSION[SESS . 'data']['state_id'];
$surcharge=  myrowval('state', 'surcharge', 'stateid='.$state_id);
//here we get dealer id
//$dealer_id = $myobj->get_dealer_id($sesId, $role_id);
$dealer_id =  $_SESSION[SESS.'data']['dealer_id'];
//if(empty($dealer_id))
//    dealer_view_page_auth(); // checking the user current page view
$location_list = $myobj->get_dealer_location_id_list($dealer_id);
$location_list = implode(',', $location_list);
$monthsale = $_GET['date'];
function moneyFormatIndia($num1) {
	//$num1='';
    $explrestunits = "" ;
	$number = explode('.',$num1);
	$num = $number[0];
	//print_r($num1);
      //  echo strlen($num);
    if(strlen($num)>3) {
        $lastthree = substr($num, strlen($num)-3, strlen($num));
        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for($i=0; $i<sizeof($expunit); $i++) {
            // creates each of the 2's group and adds a comma to the end
            if($i==0) {
                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
            } else {
                $explrestunits .= $expunit[$i].",";
               
            }
            
        }
       //  print_r($explrestunits);
       // exit;
        if($number[1]!= ''){
        $thecash = $explrestunits.$lastthree.".".$number[1];
        }
        else{
            $thecash = $explrestunits.$lastthree;
        }
    } else {
        $thecash = $num;
    }
  //  echo $thecash;
    return $thecash; // writes the final format where $currency is the currency symbol.
}
?>
<div class="row">
<?php
$data = array();

$query_sale = "SELECT SUM(qty) qty,SUM(product_rate*qty) value, c1_name,c1_id FROM 
    `challan_order_details` usod INNER JOIN `challan_order` uso ON usod.ch_id = uso.id
    INNER JOIN catalog_view ON catalog_view.product_id = usod.product_id 
    WHERE DATE_FORMAT(ch_date,'%Y-%m') = '2017-06' AND ch_dealer_id = '$dealer_id' group by catalog_view.c1_id"; //user_id
//echo $query_sale;
$r1 = mysqli_query($dbc,$query_sale);
    while($result = mysqli_fetch_assoc($r1))
{
  
$id = $result['c1_id'];
$data[$id]['name'] = $result['c1_name'];
$data[$id]['stock'] = $result['value'];
  
}

?>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
 <div class="col-xs-6 col-sm-6 widget-container-col ui-sortable" id="widget-container-col-6" style="min-height: 100px;">
		<div class="panel-group">
			<div class="panel panel-primary">
			  <div class="panel-heading"><i class="fa fa-laptop"> <strong>PRODUCT STOCK VALUE CATEGORYWISE</strong></i></div>			  
				  <div class.l,k4="panel-body">
					<div class="ui-sortable-handle" id="widget-box-5">
                                             <span style="float:left">
<i class="ace-icon fa fa-bookmark" style="color:#ad0606"></i><strong> Blends : &#8377; <?=moneyFormatIndia($data['120150507011222']['stock'], 2, '.', ',');?></strong><br/>
<i class="ace-icon fa fa-bookmark" style="color:#f67c0f"></i><strong> Sprinkler : &#8377; <?=moneyFormatIndia($data['120150507011140']['stock'], 2, '.', ',');?></strong><br/>                                               
<i class="ace-icon fa fa-bookmark" style="color:#43114a"></i><strong> Straight Premium : &#8377; <?=moneyFormatIndia($data['120150507011152']['stock'], 2, '.', ',');?></strong><br/>                                               
<i class="ace-icon fa fa-bookmark" style="color:#0a87b2"></i><strong> specialties Spices : &#8377; <?=moneyFormatIndia($data['120150507011211']['stock'], 2, '.', ',');?></strong><br/>                                               
<i class="ace-icon fa fa-bookmark" style="color:#d5e007"></i><strong> Hing : &#8377; <?=moneyFormatIndia($data['120150507011301']['stock'], 2, '.', ',');?></strong><br/>                                               
<i class="ace-icon fa fa-bookmark" style="color:#4d59f0"></i><strong> Whole : &#8377; <?=moneyFormatIndia($data['120150622100550']['stock'], 2, '.', ',');?></strong><br/>                                               

                                            </span>
                                            <span style="float:center">
    
   <a href="index.php?option=balance-stock"><canvas id="piechartp" width="250" height="250"></canvas></a>
  
  <script type="text/javascript">
    // Get the context of the canvas element we want to select
     var ctx = document.getElementById("piechartp").getContext("2d");
    var blends = "<?=$data['120150507011222']['stock']?>";
    var sprinkler = "<?=$data['120150507011140']['stock']?>";
    var straight = "<?=$data['120150507011152']['stock']?>";
    var spices = "<?=$data['120150507011211']['stock']?>";
    var hing = "<?=$data['120150507011301']['stock']?>";
    var whole = "<?=$data['120150622100550']['stock']?>";
   /// alert(amt);
   // alert(amtp);
    var data = [{
        value: blends,
         color:"#ad0606",
        highlight: "#cc1212",
        label: "Blends"
    },
    {
        value: sprinkler,
         color:"#f67c0f",
        highlight: "#f5953f",
        label: "Sprinkler"
    },
    {
        value: straight,
         color:"#43114a",
        highlight: "#a274a9",
        label: "Straight Premium"
    },
    {
        value: spices,
         color:"#0a87b2",
        highlight: "#5da3bb",
        label: "Specialities Spices"
    },
    {
        value: hing,
        color: "#d5e007",
        highlight: "#e7ec7e",
        label: "Hing"
    },
    {
        value: whole,
        color: "#4d59f0",
        highlight: "#7b84ec",
        label: "Whole"
    }
   ];
    
    var options = {
      animateScale: true
    };

    var myNewChart = new Chart(ctx).Pie(data,options);

  </script> </span>

</div></div></div></div></div></div>
