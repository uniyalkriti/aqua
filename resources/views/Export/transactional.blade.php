<?php
$query_string = Request::getQueryString() ? ('&' . Request::getQueryString()) : '';
?> 
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css" />
		<!-- ace styles -->
		<link rel="stylesheet" href="assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		<style type="text/css">
			  .blink {
	      animation: blink 2s steps(10, start) infinite;
	      -webkit-animation: blink 1s steps(5, start) infinite;
	    }
	    @keyframes blink {
	      to {
	        visibility: hidden;
	      }
	    }
	    @-webkit-keyframes blink {
	      to {
	        visibility: hidden;
	      }
	    }
		</style>
	</head>

	<body class="no-skin">
		<div class="main-container ace-save-state" id="main-container">
			<div class="main-content">
				<div class="main-content-inner">
					<div class="page-content">
						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
								<div class="space-24"></div>
								<h3 class="header smaller red">Export Transactiona Data </h3>
								<div class="row">
									<div class="col-xs-4 col-sm-3 pricing-span-header">
										<div class="widget-box transparent">
											<div class="widget-header">
												<h5 class="widget-title bigger lighter">Details</h5>
											</div>

											<div class="widget-body">
												<div class="widget-main no-padding">
													<ul class="list-unstyled list-striped pricing-table-header">
														<li>Selected Date</li>
														<li>Selected Count Zone</li>
														<li>Selected Count Region</li>
														<li>Selected Count State</li>
														<li>Selected Count Town</li>
														<li>Total Records</li>
														<li style="color:green">{{'Click Here For Download'}}
															&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
															<i class="fa fa-hand-o-right blink" aria-hidden="true" style="font-size:28px;"></i>
														</li>
													</ul>
												</div>
											</div>
										</div>
									</div>

									<div class="col-xs-8 col-sm-9 pricing-span-body">
										<div class="pricing-span">
											<div class="widget-box pricing-box-small widget-color-red3">
												<div class="widget-header">
													<h5 class="widget-title bigger lighter">Sale Data Export</h5>
												</div>
												<div class="widget-body">
													<div class="widget-main no-padding">
														<ul class="list-unstyled list-striped pricing-table">
															<li>{{(!empty($from_date) && !empty($to_date))?$from_date.'to'.$to_date:'==Not Selected=='}}</li>
															<li>{{!empty($count_zone)?$count_zone:'==Not Selected=='}}</li>
															<li>{{!empty($count_region)?$count_region:'==Not Selected=='}}</li>
															<li>{{!empty($count_state)?$count_state:'==Not Selected=='}}</li>
															<li>{{!empty($count_town)?$count_town:'==Not Selected=='}}</li>
															<li>{{$sale_order_query_count}}</li>
														</ul>
													</div>
													<div>
													<!-- 	<a href="saleOrderData?{{$query_string}}" class="btn btn-block btn-sm btn-danger">
															<span>Sale Data Export</span>&nbsp<i class="fa fa-file-excel-o" aria-hidden="true"></i>
														</a> -->
														<a href="export_sale_data?{{$query_string}}" class="btn btn-block btn-sm btn-danger">
															<span>Sale Data Export</span>&nbsp<i class="fa fa-file-excel-o" aria-hidden="true"></i>
														</a>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div><!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->
			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
			</a>
		</div><!-- /.main-container -->

</body>
</html>
