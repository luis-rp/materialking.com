
<!--<style>
.gm-style img { max-width: 20%; }
.gm-style label { width: auto; display: inline; }
</style>-->

<?php echo '<script>var readnotifyurl="'.site_url('dashboard/readnotification').'";</script>'?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
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


		<!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> -->
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?v=3&amp;sensor=true"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/gmap3.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/gmap3.infobox.min.js"></script>
		<link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/realia-blue.css" type="text/css" id="color-variant-default">

<link href='<?php echo base_url(); ?>templates/admin/css/fullcalendar.css' rel='stylesheet' />
<script src='<?php echo base_url(); ?>templates/admin/js/jquery-ui.js'></script>
<script src='<?php echo base_url(); ?>templates/admin/js/fullcalendar.js'></script>
<link href="http://materialking.com/templates/admin/css/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">

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
	
	
	function gotoproject(project){
		
		$("#pid").val(project);
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
	 	
	 InitMap();
	 	
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
			

	function InitMap() {
        google.maps.event.addDomListener(window, 'load', LoadMap);
    }		
			
			
        function LoadMap() {
        var locations = new Array(
            <?php echo $latlongs; ?>
        );
        var markers = new Array();
        var mapOptions = {
            center: new google.maps.LatLng(<?php echo $mapcenter; ?>),
            zoom: 9,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: false
        };

        var map = new google.maps.Map(document.getElementById('map'), mapOptions);


        <?php foreach ($popups as $k => $popup) { ?>
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(<?php echo $k; ?>),
            map: map,
            icon: 'http://html.realia.byaviators.com/assets/img/marker-transparent.png'
        });

        var myOptions = {
            content: '<?php echo $popup; ?>',
            disableAutoPan: false,
            maxWidth: 0,
            pixelOffset: new google.maps.Size(-146, -190),
            zIndex: 100,
            closeBoxMargin: "",
            closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif",
            infoBoxClearance: new google.maps.Size(1, 1),
            boxStyle: { 
                background: "#fff"
                ,opacity: 1
               },
            position: new google.maps.LatLng(location[0], location[1]),
            isHidden: false,
            pane: "floatPane",
            enableEventPropagation: false
        };
        marker.infobox = new InfoBox(myOptions);
        marker.infobox.isOpen = false;

        var myOptions = {
            draggable: true,
            content: '<div class="marker"><div class="marker-inner"></div></div>',
            disableAutoPan: true,
            closeBoxURL: "",
            pixelOffset: new google.maps.Size(-21, -58),
            position: new google.maps.LatLng(location[0], location[1]),
            isHidden: false,
            // pane: "mapPane",
            enableEventPropagation: true
        };
        marker.marker = new InfoBox(myOptions);
        marker.marker.open(map, marker);
        markers.push(marker);

        google.maps.event.addListener(marker, "click", function(e) {
            var curMarker = this;

            $.each(markers, function(index, marker) {
                // if marker is not the clicked marker, close the marker
                if (marker !== curMarker) {
                    marker.infobox.close();
                    marker.infobox.isOpen = false;
                }
            });

            if (curMarker.infobox.isOpen === false) {
                curMarker.infobox.open(map, this);
                curMarker.infobox.isOpen = true;
                map.panTo(curMarker.getPosition());
            } else {
                curMarker.infobox.close();
                curMarker.infobox.isOpen = false;
            }
        });
        <?php } ?>

    }
    
    
	function readnotification(id)
	{
				$.ajax({
					type:"post",
					url: readnotifyurl,
					data: "id="+id
				}).done(function(data){
					//alert(data);
				});
				return true;
	}

	 function preloadoptions()
	 {
	 	$.noConflict();
    	$("#smodal").modal();   	   
     }
 </script>
<div class="container" style="margin-top:10em;">&nbsp;</div>
<?php if(isset($settingtour) && $settingtour==1) { ?>
<div id="tourcontrols" class="tourcontrols" style="right: 30px;">
<p>First time here?</p>
<span class="button" id="activatetour">Start the tour</span>
<span class="closeX" id="canceltour"></span>
<?php  } ?>
<?php $mp = $this->session->userdata('managedprojectdetails');?>

</div>






<div>
<section class="row-fluid" style="margin:0px auto; padding:0px; width:93.4%;">
<?php echo @$messageemailinv;?>
<h3 class="box-header" style="display:inline; width:98.4%">
				<span id="step1" >Your Dashboard</span>
				&nbsp;
                <?php echo $this->session->flashdata('message'); ?>                
				<?php if($this->session->userdata('usertype_id') == 2){?>
				<a class="btn btn-primary pull-right" href="<?php echo site_url('site/items');?>"><strong>Go to store</strong></a>&nbsp;&nbsp;
				<span class="pull-right" style="width: 5px;">&nbsp;</span>
				<a class="btn btn-primary pull-right" href="<?php echo site_url('admin/dashboard/application');?>"><strong>Your Credit Application</strong></a>
				<?php } ?>
			</h3>
            </section>
            </div>
           
	<div class="box">
		<div class="span12">
		<?php if(!$this->session->userdata('managedprojectdetails')){?>
       <div style="text-align:center;">
       <?php	 if($this->session->userdata('usertype_id') != 3) {	 ?>
          <a href="<?php echo site_url('admin/project/add');?>" target="_blank">Add New Project</a>&nbsp;&nbsp;&nbsp;&nbsp;
          <a href="<?php echo site_url('admin/costcode/add');?>" target="_blank">Add New Cost Code</a>&nbsp;&nbsp;&nbsp;&nbsp;
          <a href="<?php echo site_url('admin/admin/add');?>" target="_blank">Add New Employee</a>&nbsp;&nbsp;&nbsp;&nbsp;
          <a href="javascript:void(0)" onclick="preloadoptions();">Invite Supplier</a>
          </div>
          
          
          
          
          
          
          <?php } } ?>
			<div class="well">
				<form class="form-horizontal" action="<?php echo base_url()?>admin/dashboard/project" method="post" id="form-selector">
					<div class="control-group">
						<label for="inputEmail" class="control-label">
						<strong>Select Your Project</strong>
						</label>
						<div class="controls" style="text-align:left;">
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
			
			<div class="well span11" style="width:75% !important;">			
				<h3 class="box-header" style="width:97.5%;">My Project Map</h3>
			    <div class="map-wrapper" style="float:left;width:100%;height:400px;">
					<div class="map">
	            	<div id="map" style="height:400px;width:100%;" class="map-inner" ></div>
	           		</div>
				</div>
			</div>	
			
			</div>			
			<br/>
			<div class="well span6" id="step2">            
				<h3 class="box-header" style=" width:94.5%">Statistics</h3>
				<table class="table table-bordered stat">
	   			<tr>
	   			<td>1.</td>
	   			<td>Number of Project</td>
	   			<td><span class="badge badge-blue"><?php echo count($projects);?></span> </td>
	   			</tr>

	   			<tr>
	   			<td>2.</td>
	   			<td>Number of Cost Code</td>
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
							<td style="word-break:break-all;">
								<strong><?php echo $njc->title;?></strong>
							</td>
							<td style="word-break:break-all;">
								<strong><?php echo ($njc->totalcredit!="" && $njc->totalcredit!=0)?number_format($njc->totalcredit,2):'0.00';?></strong>
							</td>
							<td style="word-break:break-all;">
								<strong><?php echo ($njc->credit!="" && $njc->credit!=0)?number_format($njc->credit,2):'0.00'; ?></strong>
							</td>
							<td style="word-break:break-all;">
								<strong><?php echo number_format($njc->due,2);?></strong>
							</td>
							<?php if(0){?>
							<td style="word-break:break-all;">
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
					<br>
    				<a class="btn btn-green" href="<?php echo site_url('site/suppliers')?>">Browse Suppliers</a>
				<?php } ?>
			
		<?php	 if($this->session->userdata('usertype_id') != 3) {	 ?>
				<a class="btn btn-green" href="<?php echo site_url('admin/dashboard/export')?>">Export Statistics</a><br><br>
				<a class="btn btn-green" href="<?php echo site_url('admin/dashboard/dashboard_pdf')?>">View PDF</a>&nbsp;
				<a class="btn btn-green"  href="javascript:void(0)" onclick="preloadoptions();">Invite Your Supplier</a>
		<?php } ?>	
			<?php	 if($this->session->userdata('usertype_id') != 3) {	 ?>				
				<?php if(@$this->session->userdata('managedprojectdetails')){?>
				<br><br>
				     <h3 class="box-header" style="width:94.5%">Filter Recommended Suppliers</h3>
				     <p><strong>What type of work will be performed on this job?</strong></p>
			    	 <form method="post" action="<?php echo site_url('admin/dashboard');?>">
						<table class="table table-bordered">
						<?php $fd=explode(",",$filterdata->filter); ?>
						<tr><td><?php foreach($types as $type) if($type->category=='Industry'){?>
  <input name="types[]" type="checkbox" value="<?php echo $type->id;?>" <?php if(@$fd) { if(in_array($type->id,$fd)) echo 'checked="checked"'; else echo ''; } ?>>
			      		<?php echo $type->title;?><br/><?php }?></td></tr>	
			      		<tr><td><input type="submit" name="suppliersearch" id="suppliersearch" value="Filter Suppliers"></td></tr>									
					<?php if(@$this->session->userdata('address')) echo "<tr><td><h4>Jobsite Address:".$this->session->userdata('managedprojectdetails')->address." </h4></td></tr>"; ?>	 
					</table>
					</form>
		 		
			   <?php } } ?>	
				<br><br>
		<?php	 if($this->session->userdata('usertype_id') != 3) {	 ?>		
				<h3 class="box-header" style="width:94.5%">Recommended Suppliers</h3>
				
				<table class="table table-bordered">
				<tr><th>Supplier Name</th><th>Location</th><th>Industry</th><th>View-Apply</th></tr>
				 <?php if(count($suppliers)>0){ foreach ($suppliers as $supplier) { ?>
				<tr>
				<td style="word-break:break-all;"><?php echo $supplier->title; ?></td>
				<td style="word-break:break-all;"> <?php if (isset($supplier->city) && isset($supplier->state)) {  
					  echo $supplier->city.",&nbsp;".$supplier->state; } else { echo $supplier->address; } ?>
                </td>
				<td style="word-break:break-all;"><?php echo $supplier->industry; ?></td>
				<td style="word-break:break-all;"><a href="<?php echo site_url('site/supplier/' . $supplier->username); ?>" target="_blank">View-Apply</a></td>
				</tr>
				  <?php } } else{ ?>
				  <?php echo "<tr><td colspan='4'><b>No Suppliers Found</b></td></tr>"; } ?>
				</table>
				
			<?php } ?>	
			
			
			
			<?php if($this->session->userdata('managedprojectdetails')){?>
			<?php if(isset($promembers)) {?>
	    	
	    	  <table class="table table-bordered">
				<caption><strong>Project Team Members<strong>&nbsp;&nbsp;			
				<a href="<?php echo base_url(); ?>admin/admin/index" target="_blank">Manage Users</a></caption>	
				<tr><th>Username</th><th>Position</th></tr>
				<!--<tr>
				<td><?php echo $mainuser->username; ?></td>
				<td><?php echo $mainuser->position; ?></td>
				</tr>-->
				 <?php foreach ($promembers as $promember) { ?>
				<tr>
				<td style="word-break:break-all;"><?php echo $promember->username; ?></td>
				<td style="word-break:break-all;"><?php echo $promember->position; ?></td>
				</tr>
				  <?php } ?>
				</table>
	    	
			<?php } }?>					
		</div>		
	    	<?php if($this->session->userdata('managedprojectdetails')){?>
	    	
	    	<div class="span10" style="padding:19px;">
	 			 <h3 class="box-header">Cost Code Statistics for the Project '<?php echo $this->session->userdata('managedprojectdetails')->title;?>' </h3>	
			    		<?php if(@$costcodesjson){?>
			    			<div style="width:100%;border:2px solid silver;border-bottom:none;">
					 			<span><strong>Total Project Savings:&nbsp;<?php echo "$".number_format($Totalawardedtotal,2); ?></strong><span></span>
						    </div>
			    		
			    			<div id="chart_pie" style="width:100%;height:auto;text-align:center;vertical-align:middle;border:2px solid silver;border-top:none;">&nbsp;</div>
			    		<?php } else {?>		
			    			<div style="width:100%;height:auto;text-align:center;vertical-align:middle;border:2px solid silver;border-top:none;">
			    			<img src="<?php echo base_url(); ?>templates/admin/images/nopie.png"/>
			    			</div>            
	    				<?php }?>
	    		 		
	    	</div>
	    
	    	

			<?php }else{?>
			<div  class="span10" style="margin-left:1%;">
		

			<div class="span4" style=" margin-top:15px; width:100%;" >
					<h3 class=" box-header" >Activity Feed</h3>
					
					 <div>
					  <div class="tiles-body">
						<div class="controller">
							<a class="reload" href="javascript:;"></a>
							<a class="remove" href="javascript:;"></a>
						</div>
				<!--		<div class="tiles-title">
					<?php if($newcontractnotifications[0]->notify_type=='contract')  echo "Contract Notifications"; else echo " Contract Notifications"?>&nbsp;&nbsp;									<?php if($newcontractnotifications){?>
							 <a class="remove" href="<?php echo site_url('admin/dashboard/allclear');?>">Clear Notifications</a>	
						<?php }?>				
						</div>	
							 
						<?php if(!$newcontractnotifications){?>
							<span class="label label-important">No New Contract Notifications</span>
						<?php }?>
						<?php foreach($newcontractnotifications as $newnote){?>

						<div class="date pull-right">
								<a class="remove" href="<?php echo site_url('admin/dashboard/close/'.$newnote->id);?>">X</a>
						  </div>
							<a href="<?php echo $newnote->link?>" onclick="return readnotification('<?php echo $newnote->id?>');">
							<div class="notification-messages <?php echo $newnote->class;?>" onclick="return readnotification('<?php echo $newnote->id?>');">
								<div class="user-profile">
									<img width="35" height="35" data-src-retina="<?php echo base_url();?>templates/front/assets/img/alert.png" data-src="<?php echo base_url();?>templates/front/assets/img/alert.png" alt="" src="<?php echo base_url();?>templates/front/assets/img/alert.png">
								</div>
								<div class="message-wrapper">
									<div class="heading">
										<?php echo $newnote->message;?>
									</div>
									<div class="description">
										<?php echo $newnote->submessage;?> / <?php echo $newnote->tago;?>
									</div>
								</div>
							</div>
							</a>
						<?php }?>
					</div></div>-->
					
					
					<?php if(isset($msgs)){ ?>
					<h5>Recent Messages&nbsp;&nbsp;
					<a class="remove" href="<?php echo site_url('admin/dashboard/closeallmessage');?>">Clear Messages</a>
					</h5>
					
					<table cellpadding="3" class="table table-bordered stat">
					  <tr>
						  <td>Message</td>
						  <td>From</td>
						  <td>To</td>
						  <td>Sent On</td>
						  <td>&nbsp;</td>
					  </tr>
				     <?php foreach($msgs as $msg) { ?>
					  <tr>
						  <td><?php echo $msg->message; ?></td>
						  <td><?php echo $msg->from; ?></td>
						  <td><?php echo $msg->to; ?></td>
						  <td><?php $datetime = strtotime($msg->senton); echo date("m/d/Y", $datetime);?></td>
						  <td style="text-align:right;">
						  <a class="remove" href="<?php echo site_url('admin/dashboard/closemessage/'.$msg->id);?>">X</a>
						  </td>
					  </tr>
                     <?php } ?>
				 </table>
				<?php } ?>
				
				
				
				
			<?php if(isset($newquotes) && count($newquotes) > 0) { ?>
				<h5>Recent Quotes Sent&nbsp;&nbsp;<?php if(isset($newquotes)) { ?>
					<a class="remove" href="<?php echo site_url('admin/dashboard/closeallquote');?>">Clear Recent Quotes Sent</a><?php } ?></h5>
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
				<?php } ?>

				<?php if(isset($awardquotes) && count($awardquotes) > 0) { ?> 
				<h5>Recent Quotes Awarded&nbsp;&nbsp;<?php if(isset($awardquotes)) { ?>
					<a class="remove" href="<?php echo site_url('admin/dashboard/closeallaward');?>">Clear Recent Quotes Awarded</a><?php } ?></h5>
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
			<?php } ?>

			<?php if(isset($newcostcodes) && count($newcostcodes) > 0) { ?>
				<h5>Recent Cost Codes Created&nbsp;&nbsp;<?php if(isset($newcostcodes)) { ?>
					<a class="remove" href="<?php echo site_url('admin/dashboard/closeallcostcode');?>">Clear Recent Cost Codes Created</a><?php } ?></h5>
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
			<?php } ?>

			<?php if(isset($newprojects) && count($newprojects) > 0) { ?>
				<h5>Recent Projects Created&nbsp;&nbsp;<?php if(isset($newprojects)) { ?>
					<a class="remove" href="<?php echo site_url('admin/dashboard/closeallproject');?>">Clear Recent Projects Created</a><?php } ?></h5>
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
				<?php } ?>


				<?php if(isset($users)) { ?>
					<h5>Recent Users Created&nbsp;&nbsp;
					<a class="remove" href="<?php echo site_url('admin/dashboard/closeallusers');?>">Clear Recent Users Created</a></h5>
					<table cellpadding="3" class="table table-bordered stat">				
					  <tr>
						  <td>User</td>
						  <td>Creation Date</td>
						  <td>&nbsp;</td>
					  </tr>
				<?php foreach($users as $user) { ?>

					  <tr>
						  <td><?php echo $user->username; ?></td>
						  <td><?php $datetime = strtotime($user->created_date); echo date("m/d/Y", $datetime);?></td>
						  <td style="text-align:right;">
						 <a class="remove" href="<?php echo site_url('admin/dashboard/closeusers/'.$user->id);?>">X</a></td>
					  </tr>			
				<?php }  ?>			
				</table>
              <?php } ?>

				<?php if(isset($networks) && count($networks) > 0) { ?>
				<h5>Recent Network Connections&nbsp;&nbsp;<?php if(isset($networks)) { ?>
					<a class="remove" href="<?php echo site_url('admin/dashboard/closeallnetwork');?>">Clear Recent Network Connections</a><?php } ?></h5>
					<table cellpadding="3" class="table table-bordered stat">
					<?php if(isset($networks)) { ?>
					  <tr>
					  <td>Company</td>
					  <td>Accepted On</td>
					  <td>&nbsp;</td>
					  </tr>
				<?php foreach($networks as $network) 
				{	
					if($network->title != '')
					{ ?>                   
					  <tr>
					  <td><?php echo $network->title; ?></td>
					  <td><?php $datetime = strtotime($network->acceptedon); echo date("m/d/Y", $datetime);?></td>
					  <td style="text-align:right;"><a class="remove" href="<?php echo site_url('admin/dashboard/closenetwork/'.$network->id);?>">X</a></td>
					  </tr>

				<?php } } ?>
				<?php } else { ?>
				<tr><td>No Recent Networks Created</td></tr>
				<?php } ?>
				</table>
				<?php } ?>

				</div>
		</div>

			
		
			 <?php if($this->session->userdata('usertype_id') != 3) 
			         {	
			         	if(isset($invoices)) { 
			         		if(isset($invoiceCntr) && $invoiceCntr != 0) { ?>	
			    <div  class="span12">
                <div class="span3"  style="width:45%;" >
					<h3 class=" box-header" style="width:94.5%">Overdue Invoices & Payment Requests</h3>					
					<table cellpadding="3" class="table table-bordered stat">
					
					
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
				</table>
				</div>
			<?php } } } ?>	
				

				
		<?php  if(isset($backtracks)) { 
			
				if(isset($backtracksCnt) && @$backtracksCnt != '')
				{ ?>
    			<div class="span3" style="width:45%;" >
					<h3 class=" box-header" style="width:94.5%">Overdue Backorders</h3>
					<table cellpadding="3" class="table table-bordered stat">
					
					 
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
				</table>
				</div>
             <?php } }
             
             if($msgs == '' &&  $newquotes == '' &&  $awardquotes == '' &&  $newcostcodes == '' &&  $newprojects == '' &&  $users == '' &&  $networks == '' &&  $invoiceCntr == 0 && $backtracksCnt == '')
             { ?>
             	<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Recent Activity.</div></div>	
             		
       <?php }
               ?>
				
				
				
				
				
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

		

	</div>
	
   <div id="smodal" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
       
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <h4 class="semi-bold" id="myModalLabel">Email Invitation</h4>     
        </div>
        
        <div class="modal-body"> 
        <!-- <form  action="<?php echo base_url()?>admin/dashboard/supplier_invitation" method="post">
      <table class="table table-bordered  col-lg-10">
	  		<tr>
	  			<td><strong>Supplier Company Name</strong></td>
	  			<td><strong> Contact Name</strong></td>
	  			<td><strong>Email</strong></td>
	  			<td><strong>Action</strong></td>
	  		</tr>
	  		 <?php foreach ($invitesuppliers as $supplier) { ?>   
	  		<tr>
	  			<td> <?php echo $supplier->title;?></td>
	  			<td><?php echo $supplier->contact;?></td>
	  			<td><?php echo $supplier->primaryemail;?></td>
	  			<td><?php if($supplier->send!=1) { ?><input type="checkbox"  name="check[]" value="<?php echo $supplier->id;?>"><?php } else { echo "Already Sent Invitation.";} ?></td>
	  		</tr>
	  		  <?php } ?>
	  		
	  	</table> -->
	  <!--	<br><input type="submit" value="Send Invitation" class="btn btn-primary"/>
	  	</form><br>-->
	  	
	  	
	        <form class="form" action="<?php echo base_url()?>admin/dashboard/supplier_email_invitation" method="post">
			  
	         <div class="form-group">
			   <label class="sr-only" for="ctitle">Company Name</label>
			    <input type="text" class="form-control" id="ctitle" name="ctitle" required style="width:80%;">			   
			  </div> 
	        
			   <div class="form-group">
			   <label class="sr-only" for="cname">Contact Name</label>
			    <input type="text" class="form-control" id="cname" name="cname" required style="width:80%;">			  
			  </div> 
			  
			  <div class="form-group">
			   <label class="sr-only" for="email">Email</label>
			    <input type="email" class="form-control" id="email" name="email" required style="width:80%;">		  
			  </div> 
			  
			   <div class="form-group">		  
			    <button type="submit" class="btn btn-primary">Send Invitation</button>
			  </div> 
			  
			</form>
		
        </div>       
              
        <div class="modal-footer">
          <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
 
  </div>
  </div>
   
	
</section>





