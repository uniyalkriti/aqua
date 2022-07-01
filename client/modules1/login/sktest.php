<?php #  login.php

/*
* This is the main content module.
* This page is included by index.php.
*/
if (!defined('BASE_URL'))
	require_once('../../page_not_direct_allow.php');

$ref = new ref_class1();
$rs = $ref->edit_course_mode(5);
//pre($rs);

echo $rs['myreason'];

/*$ref1 = new ref_class1();
$rs = $ref1->get_aima_rating_list($filter='',  $records = '', $orderby='');
pre($rs);*/
?>
