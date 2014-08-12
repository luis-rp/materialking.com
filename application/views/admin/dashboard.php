
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


<link href='<?php echo base_url(); ?>templates/admin/css/fullcalendar.css' rel='stylesheet' />
<script src='<?php echo base_url(); ?>templates/admin/js/jquery-ui.custom.min.js'></script>
<script src='<?php echo base_url(); ?>templates/admin/js/fullcalendar.js'></script>
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
		
	});

</script>

<style>
	#loading {
		position: absolute;
		top: 5px;
		right: 5px;
		}

	#calendar {
		width: 100%;
		}

</style>

 <script type="text/javascript">
	 $(document).ready(function(){
 tour4 = new Tour({
	  steps: [
	  {
	    element: "#step1",
	    title: "Step 1",
	    content: "Welcome to the on-page tour for Dashboard"
	  },
	 
   	  
	]
	});

	$("#activatetour").click(function(e){
		  e.preventDefault();
			$("#tourcontrols").remove();
			tour4.restart();
			// Initialize the tour
			tour4.init();
			start();
		});
	 });
		$('#canceltour').live('click',endTour);
	 function start(){
		 
			// Start the tour
				tour4.start();
			 }
	 function endTour(){
		 
		 $("#tourcontrols").remove();
		 tour4.end();
			}
 </script>
 
<?php if($settingtour) { ?> 
<div id="tourcontrols" class="tourcontrols" style="right: 30px;">
<p>First time here?</p>
<span class="button" id="activatetour">Start the tour</span>
<span class="closeX" id="canceltour"></span></div><?php } ?>
<?php $mp = $this->session->userdata('managedprojectdetails');?>
<section class="row-fluid">
	<div class="box">
		<div class="span12">
			<h3 class="box-header" style="display:inline" id="step1">
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
								<option value="0">Select Company Dashboard</option>
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
				<a class="btn btn-green" href="<?php echo site_url('admin/dashboard/export')?>">Export Statistics</a>
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
			<div id="step1" class="span4" style="width:338px !important;">
			<?php if(($this->session->userdata('usertype_id') != 3)  && ($this->session->userdata('tour') == "unfinished")){ ?>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<?php }else{?>
				<?php if($settingtour) { ?>
				&nbsp;&nbsp;&nbsp;&nbsp;<a class="btn btn-primary" href="<?php echo base_url("/admin/admin/restart_tour");?>">Restart Tour</a>
			<?php } ?>
				
			<div class="well span4" style=" margin-top:15px; width:100%;" >
					<h3 class=" box-header">Activity Feed</h3>
					<h5>Recent Messages</h5>
					<table cellpadding="3" class="table table-bordered stat">
					<?php if(isset($msgs)) { ?>
					  <tr>
					  <td>Message</td>
					  <td>From</td>							  
					  <td>Sent On</td>
					  </tr>		
				<?php foreach($msgs as $msg) { if(strpos($msg->to, '(Admin)') > 0) { ?>

					  <tr>
					  <td><?php echo $msg->message; ?></td>
					  <td><?php echo $msg->from; ?></td>	
					  <td><?php $datetime = strtotime($msg->senton); echo date("M d, Y H:i A", $datetime);?></td>							    
					  </tr>			  			  

				<?php } } ?>
				<?php } else { ?>
				<tr><td>No Messages Found</td></tr>
				<?php } ?>
				</table>					
				
				<h5>Recent Quotes Sent</h5>
					<table cellpadding="3" class="table table-bordered stat">
					<?php if(isset($newquotes)) { ?>
					  <tr>
					  <td>Quote</td>
					  <td>Sent On</td>
					  </tr>		
				<?php foreach($newquotes as $quote) {?>

					  <tr>
					  <td><?php echo $quote->ponum; ?></td>					 
					  <td><?php $datetime = strtotime($quote->podate); echo date("M d, Y H:i A", $datetime);?></td>							    
					  </tr>			  			  

				<?php } ?>
				<?php } else { ?>
				<tr><td>No Recent Quotes Found</td></tr>
				<?php } ?>
				</table>	
				
				
				<h5>Recent Quotes Awarded</h5>
					<table cellpadding="3" class="table table-bordered stat">
					<?php if(isset($awardquotes)) { ?>
					  <tr>
					  <td>Quote</td>
					  <td>Awarded On</td>
					  </tr>		
				<?php foreach($awardquotes as $awardquote) { ?>

					  <tr>
					  <td><?php echo $awardquote->ponum; ?></td>					 
					  <td><?php $datetime = strtotime($awardquote->awardedon); echo date("M d, Y H:i A", $datetime);?></td>							    
					  </tr>			  			  

				<?php } ?>
				<?php } else { ?>
				<tr><td>No Recent Awarded Quotes Found</td></tr>
				<?php } ?>
				</table>
				
				
				<h5>Recent Cost Codes Created</h5>
					<table cellpadding="3" class="table table-bordered stat">
					<?php if(isset($newcostcodes)) { ?>
					  <tr>
					  <td>CostCode</td>
					  <td>Project</td>
					  <td>Creation Date</td>
					  </tr>		
				<?php  foreach($newcostcodes as $costcode) { ?>

					  <tr>
					  <td><?php echo $costcode->code; ?></td>		
					  <td><?php echo $costcode->title; ?></td>				 
					  <td><?php $datetime = strtotime($costcode->creation_date); echo date("M d, Y H:i A", $datetime);?></td>							    
					  </tr>			  			  

				<?php } ?>
				<?php } else { ?>
				<tr><td>No Recent Cost Codes Created</td></tr>
				<?php } ?>
				</table>
				
				
				<h5>Recent Projects Created</h5>
					<table cellpadding="3" class="table table-bordered stat">
					<?php if(isset($newprojects)) { ?>
					  <tr>
					  <td>Project</td>
					  <td>Creation Date</td>
					  </tr>		
				<?php foreach($newprojects as $project) { ?>

					  <tr>					  
					  <td><?php echo $project->title; ?></td>				 
					  <td><?php $datetime = strtotime($project->creation_date); echo date("M d, Y H:i A", $datetime);?></td>							    
					  </tr>			  			  

				<?php } ?>
				<?php } else { ?>
				<tr><td>No Recent Projects Created</td></tr>
				<?php } ?>
				</table>
				
				
				
				<h5>Recent Users Created</h5>
					<table cellpadding="3" class="table table-bordered stat">
					<?php if(isset($users)) { ?>
					  <tr>
					  <td>User</td>
					  <td>Creation Date</td>
					  </tr>		
				<?php foreach($users as $user) { ?>

					  <tr>					  
					  <td><?php echo $user->companyname; ?></td>				 
					  <td><?php $datetime = strtotime($user->regdate); echo date("M d, Y H:i A", $datetime);?></td>							    
					  </tr>			  			  

				<?php } ?>
				<?php } else { ?>
				<tr><td>No Recent Users Created</td></tr>
				<?php } ?>
				</table>
				
				
				<h5>Recent Network Connections</h5>
					<table cellpadding="3" class="table table-bordered stat">
					<?php if(isset($networks)) { ?>
					  <tr>
					  <td>Company</td>
					  <td>Accepted On</td>
					  </tr>		
				<?php foreach($networks as $network) { ?>

					  <tr>					  
					  <td><?php echo $network->title; ?></td>				 
					  <td><?php $datetime = strtotime($network->acceptedon); echo date("M d, Y H:i A", $datetime);?></td>							    
					  </tr>			  			  

				<?php } ?>
				<?php } else { ?>
				<tr><td>No Recent Networks Created</td></tr>
				<?php } ?>
				</table>
				
				
				</div>	
		</div>
		<div id="step1" class="span4">	
				
				<div class="well span4" style="width:100% !important;" >
					<h3 class=" box-header">Overdue Invoices & Payment Requests</h3>
					<h5>Invoices with Past Due Date</h5>
					<table cellpadding="3" class="table table-bordered stat">
					<?php if(isset($invoices)) { ?>
					  <tr>
					  <td>Invoice</td>
					  <td>Due Date</td>							    
					  </tr>		
				<?php foreach($invoices as $invoice) { ?>

					  <tr>
					  <td><?php echo $invoice->invoicenum; ?></td>
					  <td><?php echo $invoice->datedue; ?></td>						 		  
					  </tr>			  			  

				<?php } ?>
				<?php } else { ?>
				<tr><td>No Invoices Found</td></tr>
				<?php } ?>
				</table>	
				<div class="tiles-title extrabox" >
					
					<h5>Orders Requested Payment By Supplier</h5>
					<table cellpadding="3" class="table table-bordered stat">
					<?php if(isset($invoicespay)) { ?>
					  <tr>
					  <td>Invoice</td>
					  <td>Due Date</td>				
					  <td>Payment Alert Date</td>				  
					  </tr>		
				<?php foreach($invoicespay as $invoice) { ?>

					  <tr>
					  <td><?php echo $invoice->invoicenum; ?></td>
					  <td><?php echo $invoice->datedue; ?></td>	
					   <td><?php echo $invoice->alertsentdate; ?></td>  						  
					  </tr>			  			  

				<?php } ?>
				<?php } else { ?>
				<tr><td>No Orders Found</td></tr>
				<?php } ?>
				</table>	
				</div>
				</div>
				
				
				<div style="clear:both;"></div>
				
				
				<div class="well span4" style="width:100% !important; margin-left:0px; " >
					<h3 class=" box-header">Overdue Backorders</h3>
					<h5>Backorders with Past Due Date</h5>
					<table cellpadding="3" class="table table-bordered stat">
					<?php if(isset($backorders)) { ?>
					  <tr>
					  <td>Invoice</td>
					  <td>Due Date</td>							  
					  </tr>		
				<?php foreach($backorders as $invoice) { ?>

					  <tr>
					  <td><?php echo $invoice['quote']->ponum; ?></td>
					  <td><?php echo $invoice['quote']->duedate; ?></td>						    
					  </tr>			  			  

				<?php } ?>
				<?php } else { ?>
				<tr><td>No Orders Found</td></tr>
				<?php } ?>
				</table>	
				</div>
				
				
				
				
				
				
							
				
				
			<?php // }?>
			</div>
			<?php // }?>
			
		</div>
		<div style="clear:both;"></div>
	<div  id="step1" class="well span4">
		<div class="tiles-title extrabox"  style="float:left;">
					<h3 class=" box-header">PO Calendar</h3>
		<section class="row-fluid">
			<div class="box">
				<div class="span12">
	
					<div id='loading' style='display:none'>Loading...</div>
					<div id='calendar'></div>

				</div>
    		</div>
		</section>
		 </div>
	 </div>	
		
		<div  id="step1" class="well span4">
		<div class="tiles-title extrabox"  style="float:left;margin-left: 40px;">
					<h3 class=" box-header">Upcoming Events</h3>
					
					<table cellpadding="3" class="table table-bordered stat">
					<?php if(isset($events)) { ?>
					  <tr>
					  <td>Event</td>
					  <td>Event Date</td>							  
					  </tr>		
				<?php foreach($events as $event) { ?>

					  <tr>
					  <td><?php echo $event->title; ?></td>
					  <td><?php echo $event->evtdate; ?></td>						    
					  </tr>			  			  

				<?php } ?>
				<?php } else { ?>
				<tr><td>No Events Found</td></tr>
				<?php } ?>
				</table>	
				</div>
		<?php }?>
			</div>
			<?php }?>	
		
	</div>
</section>