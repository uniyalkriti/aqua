<?php #  login.php

/*
* This is the main content module.
* This page is included by index.php.
*/
if (!defined('BASE_URL'))
	require_once('../../page_not_direct_allow.php');

/*$ref = new ref_class4();
$rs = $ref->save_ref_course_lavel();
//pre($rs);
echo $rs['reason'];
*/
/*$ref1 = new ref_class1();
$rs = $ref1->get_aima_rating_list($filter='',  $records = '', $orderby='');
pre($rs);*/

/*$ref2 = new ref_class2();
//$rs = $ref2->get_entrance_list();
$rs = $ref2->get_entrance_list($filter='',  $records = '', $orderby='');
pre($rs);
*/
/*$ref2 = new ref_class4();
//$rs = $ref2->get_entrance_list();
$rs = $ref2->get_ref_course_lavel_list($filter='',  $records = '', $orderby='');
pre($rs);*/
//echo $rs['reason'];


$ref1 = new loc_country();
$rs = $ref1->save_country_name();
echo $rs['reason'];



?>
