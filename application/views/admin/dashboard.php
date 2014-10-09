
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
<script src='<?php echo base_url(); ?>templates/admin/js/jquery-ui.js'></script>
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
	 		
		$("#form-selector").submit();
		
	}

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

	#loadingevent {
		position: absolute;
		top: 5px;
		right: 5px;
	}

	#calendarevent {
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
<?php if(isset($settingtour) && $settingtour==1) { ?>
<div id="tourcontrols" class="tourcontrols" style="right: 30px;">
<p>First time here?</p>
<span class="button" id="activatetour">Start the tour</span>
<span class="closeX" id="canceltour"></span></div>
<?php  } ?>
<?php $mp = $this->session->userdata('managedprojectdetails');?>
<section class="row-fluid">
<h3 class="box-header" style="display:inline" >
				<span id="step1" >Your Dashboard</span>
				&nbsp;

				<?php if($this->session->userdata('usertype_id') == 2){?>
				<a class="btn btn-primary pull-right" href="<?php echo site_url('site/items');?>"><strong>Go to store</strong></a>&nbsp;&nbsp;
				<span class="pull-right" style="width: 5px;">&nbsp;</span>
				<a class="btn btn-primary pull-right" href="<?php echo site_url('admin/dashboard/application');?>"><strong>Your Credit Application</strong></a>
				<?php } ?>
			</h3>
	<div class="box">
		<div class="span12">

			<br/>
			<div class="well">
				<form class="form-horizontal" action="<?php echo base_url()?>admin/dashboard/project" method="post" id="form-selector">
					<div class="control-group">
						<label for="inputEmail" class="control-label">
						<strong>Select Your Project</strong>
						</label>
						<div class="controls">
							<select name="pid" id="pid" onchange="changeproject();">
								<option value="0">Company Dashboard</option>
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

				<h3 class="box-header" style=" width:94.5%">Statistics</h3>
				

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

	   			<!--  <tr>
	   			<td>3.</td>
	   			<td>Number of Item Codes</td>
	   			<td><span class="badge"> echo count($itemcodes);</span></td>
	   			</tr>-->

	   			<tr>
	   			<td>3.</td>
	   			<td>Total Number of Direct Orders</td>
	   			<td><span class="badge"><?php echo count($directquotes);?></span></td>
	   			</tr>

	   			<tr>
	   			<td>4.</td>
	   			<td>Total Number of Quotes</td>
	   			<td><span class="badge badge-warning"> <?php echo count($quotes);?></span></td>
	   			</tr>

	   			<!--  <tr>
	   			<td>6.</td>
	   			<td>Total Number of Quotes Requested</td>
	   			<td><span class="badge badge-warning"> echo $invited;</span></td>
	   			</tr>-->

	   			<!--  <tr>
	   			<td>7.</td>
	   			<td>Total Number of Quotes Pending</td>
	   			<td><span class="badge badge-red"> echo $pending;</span></td>
	   			</tr>-->

	   			<tr>
	   			<td>5.</td>
	   			<td>Total Number of Awarded Quotes</td>
	   			<td><span class="badge badge-green"><?php echo $awarded;?></span></td>
	   			</tr>
	   			<?php if($this->session->userdata('usertype_id') == 1){?>
	   			<tr>
	   			<td>6.</td>
	   			<td>Number of Companies:</td>
	   			<td><span class="badge badge-info"> <?php echo count($companies);?></span></td>
	   			</tr>
	   			<?php }?>
	    		</table>


				<?php if($this->session->userdata('usertype_id') == 2){?>
	    		<h3 class="box-header" style="width:94.5%">Companies in Your Network</h3>
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
					<div> 
    				<a class="btn btn-green" href="<?php echo site_url('site/suppliers')?>">
    					Browse Suppliers
    				</a>
				<?php } ?>
				<a class="btn btn-green" href="<?php echo site_url('admin/dashboard/export')?>">Export Statistics</a>&nbsp;<a class="btn btn-green" href="<?php echo site_url('admin/dashboard/dashboard_pdf')?>">View PDF</a>
				</div>
	    	</div>

			
	    	<?php if($this->session->userdata('managedprojectdetails')){?>
	    	<div class="span7">
	    		<h3 class="box-header" style="width:94.5%">Cost Code Statistics for the Project '<?php echo $this->session->userdata('managedprojectdetails')->title;?>' </h3>
	    		<?php if(@$costcodesjson){?>
	    		<div id="chart_pie" style="height: 420px;"></div>
	    		<?php } else {?>

	    		<div style="width:94.5%; height:auto; text-align:center;  vertical-align:middle; border:2px solid silver;margin:0;padding:8px; ">
	    		<img src="<?php echo base_url(); ?>templates/admin/images/nopie.png"/>
	    		</div>

	    		<?php }?>
				<?php if(isset($Totalawardedtotal) && $Totalawardedtotal!='') { ?>
					<br><div><strong>Total Project Savings:&nbsp;$<?php echo $Totalawardedtotal; ?></strong></div>
				<?php } ?>
				
	    	</div>
			<?php }else{?>
			<div  class="span8" style="margin-left:1%;">
		

			<div class="well span4" style=" margin-top:15px; width:100%;" >
					<h3 class=" box-header" >Activity Feed</h3>
					<h5>Recent Messages</h5>
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

				<h5>Recent Quotes Sent</h5>
					<table cellpadding="3" class="table table-bordered stat">
					<?php if(isset($newquotes)) { ?>
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


				<h5>Recent Quotes Awarded</h5>
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


				<h5>Recent Cost Codes Created</h5>
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


				<h5>Recent Projects Created</h5>
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



				<h5>Recent Users Created</h5>
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


				<h5>Recent Network Connections</h5>
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

		<div  class="span12">	
				
				<!--<div class="well span4" style="width:100% !important;" >-->
                <div class="well span3"  style="width:30%;" >
					<h3 class=" box-header" style="width:94.5%">Overdue Invoices & Payment Requests</h3>					
					<table cellpadding="3" class="table table-bordered stat">
					<?php if(isset($invoices)) { ?>
					  <tr>
					  <td>Invoice</td>
					  <td>Due Date</td>
					  <td>Payment Status</td>
                      <td>Verification</td>
					  </tr>
				<?php foreach ($invoices as $invoice)
            if($invoice->invoicenum && $invoice->quote->purchasingadmin == $this->session->userdata('purchasingadmin') && ($invoice->paymentstatus!="Paid" || $invoice->status!="Verified") && date('Y-m-d', strtotime( $invoice->datedue)) < date('Y-m-d')  && $invoice->datedue)
            { ?>

					  <tr>
					  <td><?php echo $invoice->invoicenum; ?></td>
					  <td><?php if($invoice->datedue) { $datetime = strtotime($invoice->datedue); echo date("m/d/Y", $datetime); }?></td>
					   <td><?php echo $invoice->paymentstatus;?><br>
                      <?php //if($i->paymentstatus=='Paid') { $olddate=strtotime($invoice->paymentdate); $newdate = date('m/d/Y', $olddate); echo $newdate; }?></td>
                      <td><?php echo $invoice->status;?></td>
					  </tr>

				<?php } ?>
				<?php } else { ?>
				<tr><td>No Invoices Found</td></tr>
				<?php } ?>
				</table>
				
				</div>

				
				
			<!--	<div style="clear:both;"></div>-->
				
				
	<!--			<div class="well span4" style="width:100% !important; margin-left:0px; " >-->
    			<div class="well span3" style="width: 40%;" >
					<h3 class=" box-header" style="width:94.5%">Overdue Backorders</h3>
					<table cellpadding="3" class="table table-bordered stat">
					<?php if(isset($backtracks)) { ?>
					 
				<?php $i=0; foreach($backtracks as $backtrack) { ?>
					<?php if(isset($backtrack['items'])) { ?>
					<table class="table table-bordered">
					<tr><td colspan="6"><h5><?php echo $backtrack['quote']->ponum;?><h5></td></tr>
			    	<tr>
			    		<th width="170">Item Code</th>
			    		<th width="200">Item Name</th>
			    		<th width="200">Company</th>
			    		<th width="60">Due Qty.</th>
			    		<th width="50">Unit</th>
			    		<th width="75">ETA</th>			    		
			    	</tr>
			    	
					<?php foreach($backtrack['items'] as $item)
			    			{?>
			    	<tr>
			    		<td><?php echo $item->itemcode;?></td>
			    		<td><?php echo $item->itemname;?></td>
			    		<td><?php echo $item->companyname;?></td>
			    		<td><?php echo $item->duequantity;?></td>
			    		<td><?php echo $item->unit;?></td>
			    		<td><?php echo $item->daterequested;?></td>			    			    				
			    	</tr>
			    	<?php }?>
			    	</table>	
			    	<?php if(isset($item->pendingshipments) && ($item->pendingshipments!="")) {?>
                       <p style="text-align:right;">*Note&nbsp;<?php echo $item->pendingshipments;?>&nbsp;Pending Acknowledgement</p>
                 <?php  } ?>
			    <?php $i++; } ?>					  

				<?php } ?>
				
				<?php if($i== 0) { ?>
				<tr><td>No Overdue Backorders Found</td></tr>
				<?php } ?>
				
				<?php } else { ?>
				<tr><td>No Overdue Backorders Found</td></tr>
				<?php } ?>
				</table>
				</div>

				
				
				
				
				
		<!-- <div   class="well span3">
		<div class="tiles-title extrabox"  style="float:left; width:100%">
					<h3 class=" box-header" style=" width:94.5%">PO Calendar</h3>

		<section class="row-fluid">
			<div class="box">
				<div class="span12">
	
					<div id='loading' style='display:none'>Loading...</div>
					<div id='calendar'></div>

				</div>
    		</div>
		</section>
		 </div>
	 </div>	-->
		

		<!-- <div   class="well span3">
		<div class="tiles-title extrabox"  style="float:left;margin-left:0px; width:100%">
		

					
				<section class="row-fluid">
					<h3 class="box-header">Event Calendar</h3>

					<div class="box">
    					<div class="span12">

    						<div id='loadingevent' style='display:none'>Loading...</div>
    						<div id='calendarevent'></div>

    					</div>
    				</div>
				</section>


				</div>
		<?php //making error dashboard}?>
			</div> -->

			<?php }?>	
							
				
				
			<?php // }?>
			</div>
			<?php // }?>
			
		</div>
		<div style="clear:both;"></div>

		

	</div>
</section>
