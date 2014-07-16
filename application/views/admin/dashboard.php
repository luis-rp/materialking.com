  <script src="<?php echo base_url(); ?>templates/admin/js/bootstrap-tour.min.js" type="text/javascript"></script>
<?php if($this->session->userdata('managedprojectdetails')){?>

	<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/app.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/plugins.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/plugins/sparkline/jquery.sparkline.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/plugins/flot/jquery.flot.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/plugins/flot/jquery.flot.tooltip.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/plugins/flot/jquery.flot.resize.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/plugins/flot/jquery.flot.time.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/plugins/flot/jquery.flot.pie.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/plugins/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>
	
      
<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/highcharts-3d.js"></script>
<script src="http://code.highcharts.com/modules/exporting.js"></script>
	<script>
	$(document).ready(function(){

		 var d_pie = [];
		  <?php $i=0; foreach($costcodesjson as $cj){?>
		  d_pie[<?php echo $i;?>]= [ "<?php echo $cj->label;?>",  <?php echo $cj->data;?> ];
		  <?php $i++;}?>

		    $('#chart_pie').highcharts({
		        chart: {
		            type: 'pie',
		            options3d: {
						enabled: true,
		                alpha: 45,
		                beta: 0
		            }
		        },
		        title: {
		            text: ''
		        },
		        tooltip: {
		            pointFormat: '<b>{point.percentage:.1f}%</b>'
		        },
		        plotOptions: {
		            pie: {
		                allowPointSelect: true,
		                cursor: 'pointer',
		                depth: 35,
		                dataLabels: {
		                    enabled: true,
		                    format: '{point.name}'
		                }
		            }
		        },
		        series:  [{
		            type: 'pie',
		            name: '',
		            data: d_pie
		        }]
		        
		    });

	/*	 $.plot("#chart_pie", d_pie, $.extend(true, {}, Plugins.getFlotDefaults(), {
		  series: {
		   pie: {
		    show: true,
		    radius: 1,
		    label: {
		     show: true
		    }
		   }
		  },
		  grid: {
		   hoverable: true
		  },
		  tooltip: true,
		  tooltipOpts: {
		   content: '%p.0%, %s', // show percentages, rounding to 2 decimal places
		   shifts: {
		    x: 20,
		    y: 0
		   }
		  }
		 }));
*/

	});   
	</script>
	
<?php }?>
<?php $mp = $this->session->userdata('managedprojectdetails');?>
<section class="row-fluid">
	<div class="box">
		<div class="span12">
			<h3 class="box-header">
				Your Dashboard
				&nbsp;
				
				<?php if($this->session->userdata('usertype_id') == 2){?>
				<a class="btn btn-primary pull-right" href="<?php echo site_url('site/items');?>"><strong>Go to store</strong></a>&nbsp;&nbsp;
				<span class="pull-right" style="width: 5px;">&nbsp;</span>
				<a class="btn btn-primary pull-right" href="<?php echo site_url('admin/dashboard/application');?>"><strong>Your Credit Application</strong></a>
				<?php } ?>
			</h3>
			<br/>
			<div class="well">
				<form class="form-horizontal" action="<?php echo base_url()?>admin/dashboard/project" method="post">
					<div class="control-group">
						<label for="inputEmail" class="control-label">
						<strong>Select Your Project</strong>
						</label>
						<div class="controls">
							<select name="pid" onchange="this.form.submit();">
								<option value="0">Select</option>
								<?php foreach($projects as $p){?>
								<option value="<?php echo $p->id;?>" <?php if(@$mp->id==$p->id){echo 'SELECTED';}?>>
								    <?php echo $p->title?>
								</option>
								<?php }?>
							</select>
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
					
			<br/>
			<div class="well span4" id="step2">
				<h3 class="box-header">Statistics</h3>
				
				<table class="table table-bordered stat">
	   			<tr>
	   			<td>1.</td>
	   			<td>Number of Project</td>
	   			<td><span class="badge badge-blue"><?php echo count($projects);?></span> </td>
	   			</tr>
	   			
	   			<tr>
	   			<td>2.</td>
	   			<td>Number of Cost Code:</td>
	   			<td><span class="badge"><?php echo count($costcodes);?></span> </td>
	   			</tr>
	   			
	   			<tr>
	   			<td>3.</td>
	   			<td>Number of Item Codes</td>
	   			<td><span class="badge"><?php echo count($itemcodes);?></span></td>
	   			</tr>
	   			
	   			<tr>
	   			<td>4.</td>
	   			<td>Total Number of Direct Orders</td>
	   			<td><span class="badge"><?php echo count($directquotes);?></span></td>
	   			</tr>
	   			
	   			<tr>
	   			<td>5.</td>
	   			<td>Total Number of Quotes</td>
	   			<td><span class="badge badge-warning"> <?php echo count($quotes);?></span></td>
	   			</tr>
	   			
	   			<tr>
	   			<td>6.</td>
	   			<td>Total Number of Quotes Requested</td>
	   			<td><span class="badge badge-warning"><?php echo $invited;?></span></td>
	   			</tr>
	   			
	   			<tr>
	   			<td>7.</td>
	   			<td>Total Number of Quotes Pending</td>
	   			<td><span class="badge badge-red"><?php echo $pending;?></span></td>
	   			</tr>
	   			
	   			<tr>
	   			<td>8.</td>
	   			<td>Total Number of Awarded Quotes</td>
	   			<td><span class="badge badge-green"><?php echo $awarded;?></span></td>
	   			</tr>
	   			<?php if($this->session->userdata('usertype_id') == 1){?>
	   			<tr>
	   			<td>9.</td>
	   			<td>Number of Companies:</td>
	   			<td><span class="badge badge-info"> <?php echo count($companies);?></span></td>
	   			</tr>
	   			<?php }?>
	    		</table>

				
				<?php if($this->session->userdata('usertype_id') == 2){?>
	    		<h3 class="box-header">Companies in Your Network</h3>
	    		<?php if(!$networkjoinedcompanies){?>
					<span class="label label-important">No companies have joined your network.</span>
				<?php }else{?>
					<table class="table table-bordered stat">
					<tr>
						<th>Company</th>
						<th>Credit Limit</th>
						<th>Credit Remaining</th>
						<th>Amount Due</th>
					</tr>
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
					</table>
				<?php } ?>
    				<a href="<?php echo site_url('site/suppliers')?>">
    					Browse Suppliers
    				</a>
				<?php } ?>
	    	</div>
	    	
	    	
	    	<?php if($this->session->userdata('managedprojectdetails')){?>
	    	<div class="span7">
	    		<h3 class="box-header">Cost Code Statistics for the Project '<?php echo $this->session->userdata('managedprojectdetails')->title;?>' </h3>
	    		<?php if(@$costcodesjson){?>
	    		<div id="chart_pie" style="height: 420px;"></div>
	    		<?php } else {?>
	    		
	    		<div style="width:400px; height:400px; text-align:center; display: table-cell; vertical-align:middle; border:2px solid silver; ">
	    		<img src="<?php echo base_url(); ?>templates/admin/images/nopie.png"/>
	    		</div>
	    		
	    		<?php }?>
	    		
	    	</div>
			<?php }else{?>
			<div id="step1" class="span4">
			&nbsp;&nbsp;&nbsp;&nbsp;
			</div>
			<?php }?>
			
		</div>
	</div>
</section>