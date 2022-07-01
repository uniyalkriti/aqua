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
								<h3 class="header smaller red">{{Lang::get('common.export')}} </h3>
								<div class="row">
									<div class="col-xs-4 col-sm-3 pricing-span-header">
										<div class="widget-box transparent">
											<div class="widget-header">
												<h5 class="widget-title bigger">Details</h5>
											</div>

											<div class="widget-body">
												<div class="widget-main no-padding">
													<ul class="list-unstyled list-striped pricing-table-header">
														<li>Selected Count {{Lang::get('common.location1')}}</li>
														<li>Selected Count {{Lang::get('common.location2')}}</li>
														<li>Selected Count {{Lang::get('common.location3')}}</li>
														<li>Selected Count Town</li>
														<li>{{Lang::get('common.total')}} Records</li>
														<li style="color:purple">{{'Click Here For Download'}}
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
													<h5 class="widget-title bigger lighter">{{Lang::get('common.retailer')}} Export</h5>
												</div>
												<div class="widget-body">
													<div class="widget-main no-padding">
														<ul class="list-unstyled list-striped pricing-table">
															<li>{{!empty($count_zone)?$count_zone:'==Not Selected=='}}</li>
															<li>{{!empty($count_region)?$count_region:'==Not Selected=='}}</li>
															<li>{{!empty($count_state)?$count_state:'==Not Selected=='}}</li>
															<li>{{!empty($count_town)?$count_town:'==Not Selected=='}}</li>
															<li>{{$retailer_count}}</li>
														</ul>
													</div>
													<div>
														<a href="ExportRetailer?{{$query_string}}" class="btn btn-block btn-sm btn-danger">
															<span>Export {{Lang::get('common.retailer')}}</span>
														</a>
													</div>
												</div>
											</div>
										</div>

										<div class="pricing-span">
											<div class="widget-box pricing-box-small widget-color-orange">
												<div class="widget-header">
													<h5 class="widget-title bigger lighter">{{Lang::get('common.user')}} Export</h5>
												</div>

												<div class="widget-body">
													<div class="widget-main no-padding">
														<ul class="list-unstyled list-striped pricing-table">
															<li>{{!empty($count_zone)?$count_zone:'==Not Selected=='}}</li>
															<li>{{!empty($count_region)?$count_region:'==Not Selected=='}}</li>
															<li>{{!empty($count_state)?$count_state:'==Not Selected=='}}</li>
															<li>{{!empty($count_town)?$count_town:'==Not Selected=='}}</li>
															<li>{{$user_count}}</li>
														</ul>
													</div>

													<div>
														<a href="userExport?{{$query_string}}" class="btn btn-block btn-sm btn-warning">
															<span>Export {{Lang::get('common.user')}} Data</span>
														</a>
													</div>
												</div>
											</div>
										</div>

										<div class="pricing-span">
											<div class="widget-box pricing-box-small widget-color-blue">
												<div class="widget-header">
													<h5 class="widget-title bigger lighter">{{Lang::get('common.distributor')}} Export</h5>
												</div>

												<div class="widget-body">
													<div class="widget-main no-padding">
														<ul class="list-unstyled list-striped pricing-table">
															<li>{{!empty($count_zone)?$count_zone:'==Not Selected=='}}</li>
															<li>{{!empty($count_region)?$count_region:'==Not Selected=='}}</li>
															<li>{{!empty($count_state)?$count_state:'==Not Selected=='}}</li>
															<li>{{!empty($count_town)?$count_town:'==Not Selected=='}}</li>
															<li>{{$dealer_count}}</li>
														</ul>
													</div>

													<div>
														<a href="dealerExport?{{$query_string}}" class="btn btn-block btn-sm btn-primary">
															<span>Export {{Lang::get('common.distributor')}} Data</span>
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
