<?php
	/* @All Weather 1.1 */
	// Include config file
	///include("include/config.php");
	// Include common file
	//include("common.php");
	// Include functions
	//include("include/functions.php");
	// Include check actions
	//include("include/checkaction.php");
?>
<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js"><!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>
		AdminFlare - Dashboard
	</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width">
	
	<script src="assets/javascripts/1.2.3/adminflare-demo-init.min.js" type="text/javascript"></script>

	<link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700" rel="stylesheet" type="text/css">
	<script type="text/javascript">
		// Include Bootstrap stylesheet 
		document.write('<link href="assets/css/' + DEMO_ADMINFLARE_VERSION + '/' + DEMO_CURRENT_THEME + '/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">');
		// Include AdminFlare stylesheet 
		document.write('<link href="assets/css/' + DEMO_ADMINFLARE_VERSION + '/' + DEMO_CURRENT_THEME + '/adminflare.min.css" media="all" rel="stylesheet" type="text/css" id="adminflare-css">');
	</script>
	<script src="http://code.highcharts.com/highcharts.js"></script>
	<!---for dropdown---->
	<link href='<?php echo base_url(); ?>templates/admin/css/fullcalendar.css' rel='stylesheet' />
<script src='<?php echo base_url(); ?>templates/admin/js/jquery-ui.js'></script>
<script src='<?php echo base_url(); ?>templates/admin/js/fullcalendar.js'></script>
<style>
.big{
width:150px;
margin-bottom:10px;
}

</style>



<script>
	
	$(document).ready(function() {
	
		$('#calendar').fullCalendar({
			editable: false,
			events: "<?php echo base_url(); ?>admin/quote/jsonlist",

			eventDrop: function(event, delta) {
				alert(event.title + ' was moved ' + delta + ' days\n' +
					'(should probably update your database)');
			},

			loading: function(bool) {
				if (bool) $('#loading').show();
				else $('#loading').hide();
			}

		});


		$('#calendarevent').fullCalendar({
			editable: false,
			events: "<?php echo base_url(); ?>admin/event/jsonlist",

			eventDrop: function(event, delta) {
				//alert(event.title + ' was moved ' + delta + ' days\n' + '(should probably update your database)');
			},

			loading: function(bool) {
				if (bool) $('#loadingevent').show();
				else $('#loadingevent').hide();
			}

		});

	});

	
	function changeproject(){
		var value= $("#pid").val();
		$("#selected_value").val(value);
		$("#form-selector").submit();
	}
	
</script>
	<!----->
		
		
	
	<script src="assets/javascripts/1.2.3/modernizr-jquery.min.js" type="text/javascript"></script>
	<script src="assets/javascripts/1.2.3/adminflare-demo.min.js" type="text/javascript"></script>
	<script src="assets/javascripts/1.2.3/bootstrap.min.js" type="text/javascript"></script>
	<script src="assets/javascripts/1.2.3/adminflare.min.js" type="text/javascript"></script>

	<style type="text/css">
		/* ======================================================================= */
		/* Server Statistics */
		.well.widget-pie-charts .box {
			margin-bottom: -20px;
		}

		/* ======================================================================= */
		/* Why AdminFlare */
		#why-adminflare ul {
			position: relative;
			padding: 0 10px;
			margin: 0 -10px;
		}

		#why-adminflare ul:nth-child(2n) {
			background: rgba(0, 0, 0, 0.02);
		}

		#why-adminflare li {
			padding: 8px 10px;
			list-style: none;
			font-size: 14px;
			padding-left: 23px;
		}

		#why-adminflare li i {
			color: #666;
			font-size: 14px;
			margin: 3px 0 0 -23px;
			position: absolute;
		}


		/* ======================================================================= */
		/* Supported Browsers */
		#supported-browsers header { color: #666; display: block; font-size: 14px; }
			
		#supported-browsers header strong { font-size: 18px; }

		#supported-browsers .span10 { margin-bottom: -15px; text-align: center; }

		#supported-browsers .span10 div {
			margin-bottom: 15px;
			margin-right: 15px;
			display: inline-block;
			width: 120px;
		}

		#supported-browsers .span10 div:last-child { margin-right: 0; }

		#supported-browsers .span10 img { height: 40px; width: 40px; }

		#supported-browsers .span10 span { line-height: 40px; font-size: 14px; font-weight: 600; }
		
		@media (max-width: 767px) {
			#supported-browsers header { text-align: center; margin-bottom: 20px; }
		}

		/* ======================================================================= */
		/* Status panel */
		.status-example { line-height: 0; position:relative; top: 22px }
		/*=========================================================================*/
		#step3{width:92% !important;}
	</style>
	
	<script type="text/javascript">
		$(document).ready(function () {
			$('a[rel=tooltip]').tooltip();

			// Easy Pie Charts
			var easyPieChartDefaults = {
				animate: 2000,
				scaleColor: false,
				lineWidth: 12,
				lineCap: 'square',
				size: 100,
				trackColor: '#e5e5e5'
			}
			$('#easy-pie-chart-1').easyPieChart($.extend({}, easyPieChartDefaults, {
				barColor: '#3da0ea'
			}));
			$('#easy-pie-chart-2').easyPieChart($.extend({}, easyPieChartDefaults, {
				barColor: '#e7912a'
			}));
			$('#easy-pie-chart-3').easyPieChart($.extend({}, easyPieChartDefaults, {
				barColor: '#bacf0b'
			}));
			$('#easy-pie-chart-4').easyPieChart($.extend({}, easyPieChartDefaults, {
				barColor: '#4ec9ce'
			}));
			$('#easy-pie-chart-5').easyPieChart($.extend({}, easyPieChartDefaults, {
				barColor: '#ec7337'
			}));
			$('#easy-pie-chart-6').easyPieChart($.extend({}, easyPieChartDefaults, {
				barColor: '#f377ab'
			}));
			// Visits Chart
			var visitsChartData = [{
				// Visits
				/*label: 'Visits',
				data: [
					[6, 1300],
					[7, 1600],
					[8, 1900],
					[9, 2100],
					[10, 2500],
					[11, 2200],
					[12, 2000],
					[13, 1950],
					[14, 1900],
					[15, 2000]
				]
			},{ */
				// Returning Visits
				label: 'Visits',
				data: [
					[6, 500],
					[7, 600],
					[8, 550],
					[9, 600],
					[10, 800],
					[11, 900],
					[12, 800],
					[13, 850],
					[14, 830],
					[15, 1000]
				],
				filledPoints: true
			}];
			$('#visits-chart').simplePlot(visitsChartData, {
				series: {
					points: {
						show: true,
						radius: 5
					},
					lines: {
						show: true
					}
				},
				xaxis: {
					tickDecimals: 2
				},
				yaxis: {
					tickSize: 1000
				}
			}, {
				height: 205,
				tooltipText: "y + ' visitors at ' + x + '.00h'"
			});
			// Comments Tab
			$('.comment-remove').click(function () {
				bootbox.confirm("Are you sure?", function (result) {
					alert("Confirm result: " + result);
				});
				return false;
			});
			// New Users Tab
			$('#tab-users a').tooltip();
		});
	</script>
</head>
<body>



<script type="text/javascript">demoSetBodyLayout();</script>

	
	<!-- / Left navigation panel -->
	
	<!-- Page content
		================================================== -->
	<section class="container">

		<!-- Server statistics
			================================================== -->
		<section class="row-fluid">
		<h3 class="box-header f-left" style="display:inline" >
		<div class="span7">
			   <span id="step1" >Your Dashboard</span></div>
            <div class="span2 m-bottom-15">   			  
			  <?php if($this->session->userdata('usertype_id') == 2){?>
				<a class="btn btn-primary pull-right cus-btn" href="<?php echo site_url('site/items');?>"><strong>Go to store</strong></a>&nbsp;&nbsp;
			</div>
			
			<div class="span3">   
				<a class="btn btn-primary pull-right cus-btn" href="<?php echo site_url('admin/dashboard/application');?>"><strong>Your Credit Application</strong></a>
			</div>
				
				  <div class="span2">  <?php }else{ ?>
				<a class="btn btn-primary pull-right cus-btn" href="<?php echo site_url('site/items');?>"><strong>Go to store</strong></a></div>
				<div class="span3">   
				
				<a class="btn btn-primary pull-right cus-btn" href="<?php echo site_url('admin/dashboard/application');?>"><strong>Your Credit Application</strong></a>
				<?php } ?></div>
			</h3>
			<div class="well widget-pie-charts">
				<?php if(!$this->session->userdata('managedprojectdetails')){?>
       <div style="text-align:center;">
	   <div class="row-fluid">
	   <div class="span4">
         <a class="btn btn-large btn btn-inverse big" href="<?php echo site_url('admin/project/add');?>" target="_blank">Add New Project</a>&nbsp;&nbsp;&nbsp;&nbsp;</div>
		 <div class="span4">
           <a class="btn btn-large btn btn-danger big" href="<?php echo site_url('admin/costcode/add');?>" target="_blank">Add New Cost Code</a>&nbsp;&nbsp;&nbsp;&nbsp;</div>
		   <div class="span4">
		   <a class="btn btn-large btn btn-pink big p-cus" href="<?php echo site_url('admin/admin/add');?>" target="_blank">Add New Employee</a></div></div>
          <?php } ?>
				<h3 class="box-header">
					
				</h3>
				
			</div>
			
			
			</section>
			
			<section class="row-fluid">
			
				<h3 class="box-header">
					<i class="icon-home"></i>
					Projects
				</h3>
			
				<div class = "box">
					<div class="well">
						<form class="form-horizontal" action="<?php echo base_url()?>index.php/admin/dashboard/project" method="post" id="form-selector">
							<div class="control-group">
								<label for="inputEmail" class="control-label">
								<strong>Select Your Project</strong>
								</label>
								<div class="controls">
								<?php  
								//print_r($this->session);
								
								 $i = $this->session->userdata('managedprojectdetails')->id;
								?>
								
									<select name="pid" id="pid" onchange="changeproject();">
										<option value="0">Company Dashboard</option>
										<?php foreach($projects as $p){?>
										<option <?php if($i==$p->id){ echo 'selected'; } ?>  value="<?php echo $p->id;?>" 
										<?php if(@$mp->id==$p->id){echo 'selected';}?>>
											<?php echo $p->title?> 
										</option>
										<?php }?> 
									</select>
									<input type="hidden" name="selected_value" id="selected_value" value="">
									<?php if($this->session->userdata('managedprojectdetails')){?>
									<span class="pull-right">
									<strong>
									Current Project: <?php echo $this->session->userdata('managedprojectdetails')->title;?>
									</strong>
									</span>
									<?php }?>
								</div>
							</div>

						</form>
						
						
						
					</div>
				</div>
			</section>
			
			<section class="row-fluid">
			
				<h3 class="box-header">
					<i class="icon-home"></i>
					Export
				</h3>
			
				<div class = "box">
				
					<div class="well span" id="step3">
						<a class="btn btn-green" href="<?php echo site_url('admin/dashboard/export')?>">Export Statistics</a>&nbsp;<a class="btn btn-green" href="<?php echo site_url('admin/dashboard/dashboard_pdf')?>">View PDF</a>
					</div>
				
				</div>
			
			</section>
			
			<section class="row-fluid">
			
			<h3 class="box-header">
					<i class="icon-home"></i>
					Bids statictics
				</h3>
			<div class = "box">
			
				<?php
							$pendingquotes = array();
								if(isset($pendingbids))
								{
								foreach ($pendingbids as $pending) {
									if (!isset($pendingquotes[$pending->quote])) {
										$pendingquotes[$pending->quote] = array();
										$pendingquotes[$pending->quote]['id'] = $pending->quote;
										$pendingquotes[$pending->quote]['count'] = 1;
										$pendingquotes[$pending->quote]['ponum'] = $pending->quotedetails->ponum;
										$pendingquotes[$pending->quote]['potype'] = $pending->quotedetails->potype;
									} else {
										$pendingquotes[$pending->quote]['count'] ++;
									}
								}
								}
						?>
				
				<div class=" no-border non-collapsible">
					<div class="span2 pie-chart">
						<div id="easy-pie-chart-1" data-percent="<?php echo $completedBids + count($pendingbids); ?>">
							<?php echo $completedBids + count($pendingbids); ?>
						</div>
						<div class="caption">
							All Bids
						</div>
					</div>
					
					<div class="span2 pie-chart">
						<div id="easy-pie-chart-2" data-percent="0">
							0
						</div>
						<div class="caption">
							New Bids
						</div>
					</div>

					<div class="span2 pie-chart">
						<div id="easy-pie-chart-3" data-percent="0">
							0
						</div>
						<div class="caption">
							Bids Processing
						</div>
					</div>

					<div class="span2 pie-chart">
						<div id="easy-pie-chart-4" data-percent="<?php echo $awardedbids;  ?>">
							<?php echo $awardedbids;  ?>
						</div>
						<div class="caption">
							Bids Awarded 
						</div>
					</div>

					<div class="span2 pie-chart">
						<div id="easy-pie-chart-5" data-percent="<?php echo $completedBids; ?>">
							<?php echo $completedBids; ?>
						</div>
						<div class="caption">
							Bids Completed
						</div>
					</div>

					<div class="span2 pie-chart">
						
						<div id="easy-pie-chart-6" data-percent="<?php echo count($pendingbids);  ?>">
							<?php 
								
								echo count($pendingbids); 
							?>
						</div>
						<div class="caption">
							Bids Pending
						</div>
					</div>
				</div>
			
			</div>
		</section>
		<!-- / Server statistics -->

		<!-- ================================================== -->
			<section class="row-fluid">
			
				<!-- Daily visits chart
					================================================== -->
				<div class="span8">
					<h3 class="box-header">
						<i class="icon-home"></i>
						Daily Purchases chart
					</h3>

					<div class="box">
						<div id="visits-chart"></div>
					</div>
				</div>
				<!-- / Daily visits chart -->
				
				<!-- Daily statistics
					================================================== -->
				<div id="<div id="counters" class="span4">
					<h3 class="box-header">
						<i class="icon-signal"></i>
						Statistics
					</h3>
					<div class="box no-border no-padding widget-statistics">
					
						<div class="rounded-borders">
							<div class="counter small">
								<span>
								<?php echo count($projects);?>
								</span>
							</div>
							<div class="counter-label">
								Number of Projects
							</div>
						</div>
						
						<div class="rounded-borders">
							<div class="counter small">
								<span>
								<?php echo count($costcodes);?>
								</span>
							</div>
							<div class="counter-label">
								Number of Cost Code
							</div>
						</div>
						
						<div class="rounded-borders">
							<div class="counter small">
								<span>
								<?php echo count($directquotes);?>
								</span>
							</div>
							<div class="counter-label">
								Total Number of Direct Orders
							</div>
						</div>
						
						<div class="rounded-borders">
							<div class="counter small">
								<span>
								<?php echo count($quotes);?>
								</span>
							</div>
							<div class="counter-label">
								Total Number of Quotes
							</div>
						</div>
						
						<div class="rounded-borders">
							<div class="counter small">
								<span>
								<?php echo count($awarded);?>
								</span>
							</div>
							<div class="counter-label">
								Total Number of Awarded Quotes
							</div>
						</div>
						
						<div class="rounded-borders">
							<div class="counter small">
								<span>
								<?php echo count($companies);?>
								</span>
							</div>
							<div class="counter-label">
								Number of Companies:
							</div>
						</div>
					</div>
				</div>
				<!--div class = "span12">
					<div class="col-md-6 col-vlg-4 visible-xlg">
						<div class="row tiles-container tiles white m-b-10 visible-xlg">
						  <div class="col-md-7  col-sm-7 b-grey b-r ">
							<h4 class="semi-bold text-center b-grey b-b no-margin p-t-20 p-b-10">California USA</h4>
							<div class="b-grey b-b">
							  <h4 class="semi-bold text-center text-error">Sunday</h4>
							  <h1 class="semi-bold text-center text-error"> 32&deg; </h1>
							  <h5 class="text-center text-error">partly cloudy</h5>
							  <div class="row auto m-t-10 m-b-10" >
								<div class="col-md-3 col-sm-3 col-xs-3  no-padding col-md-offset-2 col-sm-offset-2 col-xs-offset-2">
								  <canvas id="white_widget_cloudy_big" width="48"  height="48" class="h-align-middle "></canvas>
								</div>
								<div class="col-md-5 col-sm-5 col-xs-5 no-padding">
								  <div class="m-t-10">
									<div class="pull-left m-l-5">
									  <canvas id="white_widget_13" width="16"  height="16" class="inline"></canvas>
									  <div class="inline">
										<h5 class="semi-bold no-margin ">54</h5>
										<p class="bold text-extra-small ">MPH</p>
									  </div>
									</div>
									<div class="pull-right m-r-10">
									  <canvas id="white_widget_14" width="16"  height="16" class="inline"></canvas>
									  <div class="inline">
										<h5 class="semi-bold no-margin ">53</h5>
										<p class="bold text-extra-small ">MM</p>
									  </div>
									</div>
								  </div>
								</div>
							  </div>
							</div>
							<div class="row auto m-t-15">
							  <div class="col-md-2 col-sm-2 col-xs-2 no-padding col-md-offset-1 col-xs-offset-1 b-grey b-r">
								<p class="text-center no-margin">11:30</p>
								<p class="text-center no-margin">PM</p>
								<canvas id="white_widget_01" width="20" height="20" class="h-align-middle m-t-10"></canvas>
								<h5 class="semi-bold text-center text-error">32&deg;</h5>
							  </div>
							  <div class="col-md-2 col-sm-2 col-xs-2 no-padding b-grey b-r">
								<div class="text-center">11:30</div>
								<div class="text-center">PM</div>
								<canvas id="white_widget_02" width="20"  height="20" class="h-align-middle m-t-10"></canvas>
								<h5 class="semi-bold text-center text-error">32&deg;</h5>
							  </div>
							  <div class="col-md-2 col-sm-2 col-xs-2 no-padding b-grey b-r">
								<div class="text-center">11:30</div>
								<div class="text-center">PM</div>
								<canvas id="white_widget_03" width="20"  height="20" class="h-align-middle m-t-10"></canvas>
								<h5 class="semi-bold text-center text-error">32&deg;</h5>
							  </div>
							  <div class="col-md-2 col-sm-2 col-xs-2 no-padding b-grey b-r">
								<div class="text-center">11:30</div>
								<div class="text-center">PM</div>
								<canvas id="white_widget_04" width="20"  height="20" class="h-align-middle m-t-10"></canvas>
								<h5 class="semi-bold text-center text-error">32&deg;</h5>
							  </div>
							  <div class="col-md-2 col-sm-2 col-xs-2 no-padding b-grey">
								<div class="text-center">11:30</div>
								<div class="text-center">PM</div>
								<canvas id="white_widget_05" width="20"  height="20" class="h-align-middle m-t-10"></canvas>
								<h5 class="semi-bold text-center text-error">32&deg;</h5>
							  </div>
							</div>
						  </div>
						  <div class="col-md-5 col-sm-5 tiles grey">
							<div class=" p-t-25 p-r-10 p-l-10 p-b-15">
							  <div class="p-b-10 m-b-10 b-grey b-b">
								<div class="pull-left"> <span class="bold text-black m-r-15 text-right">Sun</span>
								  <canvas id="white_widget_06" width="20"  height="20" class="inline m-l-10"></canvas>
								</div>
								<div class="pull-right"> <span class="semi-bold text-grey">32 - 28</span> <span class="bold text-error">C&deg; </span> </div>
								<div class="clearfix"></div>
							  </div>
							  <div class="p-b-10 m-b-10 b-grey b-b">
								<div class="pull-left"> <span class="bold  text-black m-r-15">Mon</span>
								  <canvas id="white_widget_07" width="20"  height="20" class="inline m-l-10"></canvas>
								</div>
								<div class="pull-right"> <span class="semi-bold text-grey">32 - 28</span> <span class="bold text-error">C&deg; </span> </div>
								<div class="clearfix"></div>
							  </div>
							  <div class="p-b-10 m-b-10 b-grey b-b">
								<div class="pull-left"> <span class="bold  text-black m-r-15">Tue</span>
								  <canvas id="white_widget_08" width="20"  height="20" class="inline m-l-10"></canvas>
								</div>
								<div class="pull-right"> <span class="semi-bold text-grey">32 - 28</span> <span class="bold text-error">C&deg; </span> </div>
								<div class="clearfix"></div>
							  </div>
							  <div class="p-b-10 m-b-10 b-grey b-b">
								<div class="pull-left"> <span class="bold  text-black m-r-5">Wed</span>
								  <canvas id="white_widget_09" width="20"  height="20" class="inline m-l-10"></canvas>
								</div>
								<div class="pull-right"> <span class="semi-bold text-grey">32 - 28</span> <span class="bold text-error">C&deg; </span> </div>
								<div class="clearfix"></div>
							  </div>
							  <div class="p-b-10 m-b-10 b-grey b-b">
								<div class="pull-left"> <span class="bold  text-black m-r-5">Thur</span>
								  <canvas id="white_widget_10" width="20"  height="20" class="inline m-l-10"></canvas>
								</div>
								<div class="pull-right"> <span class="semi-bold text-grey">32 - 28</span> <span class="bold text-error">C&deg; </span> </div>
								<div class="clearfix"></div>
							  </div>
							  <div class="p-b-10 m-b-10 b-grey b-b">
								<div class="pull-left"> <span class="bold  text-black m-r-15">Fri</span>
								  <canvas id="white_widget_11" width="20"  height="20" class="inline m-l-10"></canvas>
								</div>
								<div class="pull-right"> <span class="semi-bold text-grey">32 - 28</span> <span class="bold text-error">C&deg; </span> </div>
								<div class="clearfix"></div>
							  </div>
							  <div class="p-b-10 m-b-10 b-grey">
								<div class="pull-left"> <span class="bold  text-black m-r-10">Sat</span>
								  <canvas id="white_widget_12" width="20"  height="20" class="inline m-l-10"></canvas>
								</div>
								<div class="pull-right"> <span class="semi-bold text-grey">32 - 28</span> <span class="bold text-error">C&deg; </span> </div>
								<div class="clearfix"></div>
							  </div>
							</div>
						  </div>
						</div>
				</div-->
				<!-- / Daily statistics -->
			</section>
		
		<section class="row-fluid">
				<h3 class="box-header">
				</h3>
				<h3 class="box-header">
								<i class="icon-time"></i>
								Companies in Your Network
							</h3>
				<div class="box">
					<div class="span8">
						<?php if($this->session->userdata('usertype_id') == 2){?>
							<?php if(!$networkjoinedcompanies){?>
								<span class="label label-important">No companies have joined your network.</span>
							<?php }else{?>
								<table class="table">
								
								<thead>
									<tr>
										<th>
											Company
										</th>
										<th>
											Credit Limit
										</th>
										<th>
											Credit Remaining
										</th>
										<th>
											Amount Due
										</th>
									</tr>
								</thead>
								<tbody>
								
								
								<?php foreach($networkjoinedcompanies as $njc){?>
									<tr>
										<td>
											<strong><?php echo $njc->title;?></strong>
										</td>
										<td>
											<strong><?php echo $njc->totalcredit;?></strong>
										</td>
										<td>
											<strong><?php echo $njc->credit;?></strong>
										</td>
										<td>
											<strong><?php echo $njc->due;?></strong>
										</td>
										<?php if(0){?>
										<td>
											<?php if($njc->due && $njc->due!='0.00'){?>
											<form method="post" action="<?php echo site_url('admin/dashboard/payall');?>">
												<input type="hidden" name="company" value="<?php echo $njc->id?>"/>
												<input type="submit" value="Pay" class="btn btn-primary"/>
											</form>
											<?php }?>
										</td>
										<?php }?>
									</tr>
								<?php }?>
								</tbody>
								</table>
							<?php } ?>
							<div> 
								<!--a class="btn btn-green" href="<?php echo site_url('site/suppliers')?>">
									Browse Suppliers
								</a-->
							<?php }else{ ?>
										<div class="alert alert-error">
											<a data-dismiss="alert" class="close" href="#">×</a>
											No companies have joined your network.
										</div>
							<?php } ?>
							<!--div class="well span" id="step2">
									<a class="btn btn-green" href="<?php echo site_url('admin/dashboard/export')?>">Export Statistics</a>&nbsp;<a class="btn btn-green" href="<?php echo site_url('admin/dashboard/dashboard_pdf')?>">View PDF</a>
								</div-->

							</div>
					</div> 
				</div>
			</section>
			
		<section class = "row-fluid" >
				
				<div class="span12">
				<h3 class="box-header">
								<i class="icon-time"></i>
								Recommended Suppliers
							</h3>
				<div class = "box">
				<table class="table table-bordered">
						<thead>
							<tr>
								<th>
									Supplier Name
								</th>
								<th>
									Location
								</th>
								<th>
									Industry
								</th>
								<th>
									View-Apply
								</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($suppliers as $supplier) { ?>
							<tr>
								<td><?php echo $supplier->title; ?></td>
								<td> <?php if (isset($supplier->city) && isset($supplier->state)) {  
									echo $supplier->city.",&nbsp;".$supplier->state; } else { echo $supplier->address; } ?>
								</td>
								<td><?php echo $supplier->industry; ?></td>
								<td><a href="<?php echo site_url('site/supplier/' . $supplier->username); ?>" target="_blank">View-Apply</a></td>
							</tr>
				  <?php } ?>	
						</tbody>
					</table>
				</div>	
				</div>	
		</section>	

		<!--=============================================================-->
		<section class="row-fluid">
			
			<!-- Support tickets
				================================================== -->
			
			<!-- / Support tickets -->

			<!-- Last activity
				================================================== -->
				<div class="span12">
							<h3 class="box-header">
								<i class="icon-time"></i>
								Activity Feed
							</h3>
							<div class="box">
								<div class="tabbable">
								
									<!-- Tabs
										================================================== -->
									<ul class="nav nav-tabs box-wide">
									
										<li class="active">
											<a href="#tab-Recent-Messages" data-toggle="tab">Recent Messages  </a>
										</li>
										
										<li>
											<a href="#tab-Recent-Quotes-Sent" data-toggle="tab">Recent Quotes Sent </a>
										</li>
										<li>
											<a href="#tab-Recent-Quotes-Awarded" data-toggle="tab">Recent Quotes Awarded   </a>
										</li>
										
										<li>
											<a href="#tab-Recent-Cost-Codes-Created" data-toggle="tab">Recent Cost Codes Created  </a>
										</li>
									</ul>
									<!-- / Tabs -->
									
									<!-- Tabs content
										================================================== -->
									<div class="tab-content box-wide box-no-bottom-padding">
									
										<!-- Recent comments tab content -->
										<div class="tab-pane fade in widget-comments active" id="tab-Recent-Messages">
												
											<div class="comment">
												<img src="<?php echo base_url();?>assets/images/avatar.png" alt="">
												<div class="content">
													<span class="commented-by">
													<a title="" href="#">Recent Messages&nbsp;&nbsp;</a>
													</span>
													<?php if(isset($msgs)) { ?>
													<a class="remove" href="<?php echo site_url('admin/dashboard/closeallmessage');?>">Clear Messages</a><?php } ?>
													<table cellpadding="3" class="table table-bordered stat">
													<?php if(isset($msgs)) { ?>
													<tr>
													<td>Message</td>
													<td>From</td>
													<td>To</td>
													<td>Sent On</td>
													<td>&nbsp;</td>
													</tr>
													<?php foreach($msgs as $msg) { // if(strpos($msg->to, '(Admin)') > 0) { ?>

													<tr>
													<td><?php echo $msg->message; ?></td>
													<td><?php echo $msg->from; ?></td>
													<td><?php echo $msg->to; ?></td>
													<td><?php $datetime = strtotime($msg->senton); echo date("m/d/Y", $datetime);?></td>
													<td style="text-align:right;"><a class="remove" href="<?php echo site_url('admin/dashboard/closemessage/'.$msg->id);?>">X</a></td>
													</tr>

													<?php // }
													} ?>
													<?php } else { ?>
													<tr><td>No Messages Found</td></tr>
													<?php } ?>
													</table>
												</div>
											</div>
										</div>
										<!-- / Recent comments tab content-->
										
										<!-- Recent threads tab content -->
											<div class="tab-pane fade in widget-comments " id="tab-Recent-Quotes-Sent">
												
											<div class="comment">
												<img src="<?php echo base_url();?>assets/images/avatar.png" alt="">
												<div class="content">
													<span class="commented-by">
													<a title="" href="#">Recent Quotes Sent&nbsp;&nbsp;</a>
													</span>
													<?php if(isset($newquotes)) { ?>
													<a class="remove" href="<?php echo site_url('admin/dashboard/closeallquote');?>">Clear Recent Quotes Sent</a><?php } ?>
													<table cellpadding="3" class="table table-bordered stat">
													<?php if(isset($newquotes)) { //echo "<pre>"; print_r($newquotes); die; ?>
													<tr>
													<td>Quote</td>
													<td>Status</td>
													<td>Sent On</td>
													<td>&nbsp;</td>
													</tr>
													<?php foreach($newquotes as $quote) {?>

													<tr>
													<td><?php echo $quote->ponum; ?></td>
													<td><?php
													$quote->invitations = $this->quote_model->getInvitedquote($quote->id);
													$quote->pendingbids = $this->quote_model->getbidsquote($quote->id);
													$quote->awardedbid = $this->quote_model->getawardedbidquote($quote->id);

													$quote->status = $quote->awardedbid ? 'AWARDED' : ($quote->pendingbids ? 'PENDING AWARD' : ($quote->invitations ? 'NO BIDS' : ($quote->potype == 'Direct' ? '-' : 'NO INVITATIONS'))); echo $quote->status; ?></td>
													<td><?php $datetime = strtotime($quote->creation_date); echo date("m/d/Y", $datetime);?></td>
													<td style="text-align:right;"><a class="remove" href="<?php echo site_url('admin/dashboard/closequote/'.$quote->id);?>">X</a></td>
													</tr>

													<?php } ?>
													<?php } else { ?>
													<tr><td>No Recent Quotes Found</td></tr>
													<?php } ?>
													</table>
												</div>
											</div>
										</div>
										<!-- / Recent threads tab content -->
										<div class="tab-pane fade in widget-comments " id="tab-Recent-Quotes-Awarded">
												
											<div class="comment">
												<img src="<?php echo base_url();?>assets/images/avatar.png" alt="">
												<div class="content">
														<span class="commented-by">
														<a title="" href="#">Recent Quotes Awarded&nbsp;&nbsp;</a>
														</span>

														<?php if(isset($awardquotes)) { ?>
														<a class="remove" href="<?php echo site_url('admin/dashboard/closeallaward');?>">Clear Recent Quotes Awarded</a><?php } ?>
														<table cellpadding="3" class="table table-bordered stat">
														<?php if(isset($awardquotes)) { ?>
														<tr>
														<td>Quote</td>
														<td>Awarded On</td>
														<td>&nbsp;</td>
														</tr>
														<?php foreach($awardquotes as $awardquote) { ?>

														<tr>
														<td><?php echo $awardquote->ponum; ?></td>
														<td><?php $datetime = strtotime($awardquote->awardedon); echo date("m/d/Y", $datetime);?></td>
														<td style="text-align:right;"><a class="remove" href="<?php echo site_url('admin/dashboard/closeaward/'.$awardquote->awardid);?>">X</a></td>
														</tr>

														<?php } ?>
														<?php } else { ?>
														<tr><td>No Recent Awarded Quotes Found</td></tr>
														<?php } ?>
														</table>
												</div>
											</div>
										</div>
										
										<!--  Recent threads tab content -->
										<div class="tab-pane fade in widget-comments " id="tab-Recent-Cost-Codes-Created">
												
											<div class="comment">
												<img src="<?php echo base_url();?>assets/images/avatar.png" alt="">
												<div class="content">
													<span class="commented-by">
													<a title="" href="#">Recent Cost Codes Created&nbsp;&nbsp;</a>
													</span>

													<?php if(isset($newcostcodes)) { ?>
													<a class="remove" href="<?php echo site_url('admin/dashboard/closeallcostcode');?>">Clear Recent Cost Codes Created</a><?php } ?>
													<table cellpadding="3" class="table table-bordered stat">
													<?php if(isset($newcostcodes)) { ?>
													<tr>
													<td>CostCode</td>
													<td>Project</td>
													<td>Creation Date</td>
													<td>&nbsp;</td>
													</tr>
													<?php  foreach($newcostcodes as $costcode) { ?>

													<tr>
													<td><?php echo $costcode->code; ?></td>
													<td><?php echo $costcode->title; ?></td>
													<td><?php $datetime = strtotime($costcode->creation_date); echo date("m/d/Y", $datetime);?></td>
													<td style="text-align:right;"><a class="remove" href="<?php echo site_url('admin/dashboard/closecostcode/'.$costcode->id);?>">X</a></td>
													</tr>

													<?php } ?>
													<?php } else { ?>
													<tr><td>No Recent Cost Codes Created</td></tr>
													<?php } ?>
													</table>
												</div>
											</div>
										</div>
										<!-- / Recent threads tab content -->
										
									<!-- / Tabs content -->
								</div>
								<!-- / .tabbable -->
							</div>
						</div>

					</div>			<!-- / Last activity -->
		</section>
		<section class="row-fluid">	
				<div class="span12">
							<h3 class="box-header">
								<i class="icon-time"></i>
								Activity Feed
							</h3>
							<div class="box">
								<div class="tabbable">
									<!-- Tabs
										================================================== -->
									<ul class="nav nav-tabs box-wide">
										<li>
											<a href="#tab-Recent-Projects-Created" data-toggle="tab">Recent Projects Created  </a>
										</li>
										<li>
											<a href="#tab-Recent-Users-Created " data-toggle="tab">Recent Users Created </a>
										</li>
										<li>
											<a href="#tab-Recent-Network-Connections" data-toggle="tab">Recent Network Connections   </a>
										</li>
									</ul>
									<!-- / Tabs -->
									
									<!-- Tabs content
										================================================== -->
								
										<!-- Recent threads tab content -->
									<div class="tab-content box-wide box-no-bottom-padding">	
										<!-- / Recent threads tab content -->
										<div class="tab-pane fade in widget-comments " id="tab-Recent-Projects-Created">
												
											<div class="comment">
												<img src="<?php echo base_url();?>assets/images/avatar.png" alt="">
												<div class="content">
														<span class="commented-by">
														<a title="" href="#">Recent Projects Created&nbsp;&nbsp;</a>
														</span>
														<?php if(isset($newprojects)) { ?>
														<a class="remove" href="<?php echo site_url('admin/dashboard/closeallproject');?>">Clear Recent Projects Created</a><?php } ?>
														<table cellpadding="3" class="table table-bordered stat">
														<?php if(isset($newprojects)) { ?>
														<tr>
														<td>Project</td>
														<td>Creation Date</td>
														<td>&nbsp;</td>
														</tr>
														<?php foreach($newprojects as $project) { ?>

														<tr>
														<td><?php echo $project->title; ?></td>
														<td><?php $datetime = strtotime($project->creation_date); echo date("m/d/Y", $datetime);?></td>
														<td style="text-align:right;"><a class="remove" href="<?php echo site_url('admin/dashboard/closeproject/'.$project->id);?>">X</a></td>
														</tr>

														<?php } ?>
														<?php } else { ?>
														<tr><td>No Recent Projects Created</td></tr>
														<?php } ?>
														</table>
												</div>
											</div>
										</div>
										<!-- Recent users activity tab content -->
										<div class="tab-pane fade in widget-comments " id="tab-Recent-Users-Created">
												
											<div class="comment">
												<img src="<?php echo base_url();?>assets/images/avatar.png" alt="">
												<div class="content">
														<span class="commented-by">
														<a title="" href="#">Recent Users Created&nbsp;&nbsp;</a>
														</span>
														<?php if(isset($users)) { ?>
														<a class="remove" href="<?php echo site_url('admin/dashboard/closeallusers');?>">Clear Recent Users Created</a><?php } ?>
														<table cellpadding="3" class="table table-bordered stat">
														<?php if(isset($users)) { ?>
														<tr>
														<td>User</td>
														<td>Creation Date</td>
														<td>&nbsp;</td>
														</tr>
														<?php foreach($users as $user) { ?>

														<tr>
														<td><?php echo $user->username; ?></td>
														<td><?php $datetime = strtotime($user->created_date); echo date("m/d/Y", $datetime);?></td>
														<td style="text-align:right;"><a class="remove" href="<?php echo site_url('admin/dashboard/closeusers/'.$user->id);?>">X</a></td>
														</tr>

														<?php } ?>
														<?php } else { ?>
														<tr><td>No Recent Users Created</td></tr>
														<?php } ?>
														</table>
												</div>
											</div>
										</div>
										<!-- Recent users activity tab content -->
										<div class="tab-pane fade in widget-comments active " id="tab-Recent-Network-Connections">
												
											<div class="comment">
												<img src="<?php echo base_url();?>assets/images/avatar.png" alt="">
												<div class="content">
															<span class="commented-by">
															<a title="" href="#">Recent Network Connections&nbsp;&nbsp;</a>
															</span>
															<?php if(isset($networks)) { ?>
															<a class="remove" href="<?php echo site_url('admin/dashboard/closeallnetwork');?>">Clear Recent Network Connections</a><?php } ?>
															<table cellpadding="3" class="table table-bordered stat">
															<?php if(isset($networks)) { ?>
															<tr>
															<td>Company</td>
															<td>Accepted On</td>
															<td>&nbsp;</td>
															</tr>
															<?php foreach($networks as $network) { ?>

															<tr>
															<td><?php echo $network->title; ?></td>
															<td><?php $datetime = strtotime($network->acceptedon); echo date("m/d/Y", $datetime);?></td>
															<td style="text-align:right;"><a class="remove" href="<?php echo site_url('admin/dashboard/closenetwork/'.$network->id);?>">X</a></td>
															</tr>

															<?php } ?>
															<?php } else { ?>
															<tr><td>No Recent Networks Created</td></tr>
															<?php } ?>
															</table>
												</div>
											</div>
										</div>
									</div>
									<!-- / Tabs content -->
								</div>
								<!-- / .tabbable -->
							</div>
						</div>

					</div>			<!-- / Last activity -->
		</section>
		<section class = "row-fluid" >
					
					<div class="span12">
					<h3 class="box-header">
						<i class="icon-money"></i>
						Overdue Invoices & Payment Requests
					</h3>
					<div class = "box">
					<table class="table">
					<?php if(isset($invoices)) { ?>
							<thead>
								<tr>
									<th>
										Invoice
									</th>
									<th>
										Due Date
									</th>
									<th>
										Payment Status
									</th>
									<th>
										Verification
									</th>
								</tr>
							</thead>
							<tbody>
									<?php foreach ($invoices as $invoice)
									if($invoice->invoicenum && $invoice->quote->purchasingadmin == $this->session->userdata('purchasingadmin') && ($invoice->paymentstatus!="Paid" || $invoice->status!="Verified") && date('Y-m-d', strtotime( $invoice->datedue)) < date('Y-m-d')  && $invoice->datedue)
									{ ?>
								<tr>
									<td><?php	if($invoice->quote->potype=='Contract') { ?>
									<a href="<?php echo site_url('admin/quote/contract_invoice/'.$invoice->invoicenum.'/'.$invoice->quote->id)?>" target="_blank"><?php echo $invoice->invoicenum; ?></a>
									<?php  } else { ?>
									<a href="<?php echo site_url('admin/quote/invoice/'.$invoice->invoicenum.'/'.$invoice->quote->id)?>" target="_blank"><?php echo $invoice->invoicenum; ?></a><?php } ?></td>
									<td><?php if($invoice->datedue) { $datetime = strtotime($invoice->datedue); echo date("m/d/Y", $datetime); }?></td>
									<td><?php echo $invoice->paymentstatus;?><br>
									<?php //if($i->paymentstatus=='Paid') { $olddate=strtotime($invoice->paymentdate); $newdate = date('m/d/Y', $olddate); echo $newdate; }?></td>
									<td><?php echo $invoice->status;?></td>
								</tr>
								  <?php } ?>
								<?php } else { ?>
							<tr><td>No Invoices Found</td></tr>
							<?php } ?>
							</tbody>
							
						
						</table>
					</div>	
					</div>	
			</section>	
			<section class = "row-fluid" >
				<div class="span12">
				<h3 class="box-header">
					<i class="icon-move"></i>
					Overdue Backorders
				</h3>
				<div class = "box">
				<table class="table">
						<?php if(isset($backtracks)) { ?>
						<?php $i=0; foreach($backtracks as $backtrack) { ?>
						<?php if(isset($backtrack['items'])) { ?>
						<thead>
							<tr>
							<td><h5><?php echo $backtrack['quote']->ponum;?><h5></td>
							</tr>
							<tr>
								<th>
									Item Code
								</th>
								<th>
									Item Name
								</th>
								<th>
									Company
								</th>
								<th>
									Due Qty.
								</th>
								<th>
									Unit
								</th>
								<th>
									ETA
								</th>	
							</tr>
						</thead>
						<tbody>
							<?php foreach($backtrack['items'] as $item){?>
							<tr>
								<td><?php echo $item->itemcode;?></td>
								<td><?php echo $item->itemname;?></td>
								<td><?php echo $item->companyname;?></td>
								<td><?php echo $item->duequantity;?></td>
								<td><?php echo $item->unit;?></td>
								<td><?php echo $item->daterequested;?></td>
							</tr>
								<?php }?>
								<?php if(isset($item->pendingshipments) && ($item->pendingshipments!="")) {?>
								<p style="text-align:right;">*Note&nbsp;<?php echo $item->pendingshipments;?>&nbsp;Pending Acknowledgement</p>
								<?php  } ?>
								<?php $i++; } ?>					  

								<?php } ?>

								<?php if($i== 0) { ?>
								<tr><td><span class="label label-important">No Overdue Backorders Found</span></td></tr>
								<?php } ?>

								<?php } else { ?>
								<tr><td><span class="label label-important">No Overdue Backorders Found</span></td></tr>
								<?php } ?>
						</tbody>
						
					
					</table>
				</div>	
				</div>	
		</section>	

		

		<!-- Why AdminFlare
			================================================== -->
		<!-- / Why AdminFlare -->

		<!-- Supported browsers
			================================================== -->
		
		<!-- / Supported browsers -->

	</section>
</body>
</html>