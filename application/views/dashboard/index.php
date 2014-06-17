<?php echo '<script>var readnotifyurl="'.site_url('dashboard/readnotification').'";</script>'?>
<script>
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
</script>
    <div class="content">  
		<div class="page-title">	
			<h3>Dashboard </h3>		
		</div>
		
	   <div id="container">
	    
		<div class="row">
		
			<div class="col-md-6 col-sm-6">
				<div class="tiles white">
					  <div class="tiles-body">
						<div class="controller">								
							<a class="reload" href="javascript:;"></a>
							<a class="remove" href="javascript:;"></a>									
						</div>
						<div class="tiles-title">
							NOTIFICATIONS
						</div>							
					  <br>
						<?php if(!$newnotifications){?>
							<span class="label label-important">No New Notifications</span>
						<?php }?>
						<?php foreach($newnotifications as $newnote){?>
						
						<div class="date pull-right">
								<a class="remove" href="<?php echo site_url('dashboard/close/'.$newnote->id);?>">X</a>
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
					</div>
				</div>					
			</div>
		
			<div class="col-md-6 col-sm-6">
			
				<div class="tiles white">
					  <div class="tiles-body">
						<div class="controller">								
							<a class="reload" href="javascript:;"></a>
							<a class="remove" href="javascript:;"></a>									
						</div>
						<div class="tiles-title">
							PENDING REQUESTS
						</div>							
					  <br>
						<?php if(!$newrequests){?>
							<span class="label label-important">No Pending Requests</span>
						<?php }
							else
							foreach($newrequests as $penreq){?>
							
							<div class="notification-messages">
								<div class="user-profile">
									<img width="35" height="35" data-src-retina="<?php echo base_url();?>templates/front/assets/img/notification-alert.png" data-src="<?php echo base_url();?>templates/front/assets/img/notification-alert.png" alt="" src="<?php echo base_url();?>templates/front/assets/img/notification-alert.png">
								</div>
								<div class="message-wrapper">
									<div class="heading">
										You got a request from <?php echo $penreq->from->fullname;?> of <?php echo $penreq->from->companyname;?>
										&nbsp; <?php echo $penreq->tago;?>
										<?php if($penreq->accountnumber){;?>
										<br/>Account Number: <?php echo $penreq->accountnumber;?>
										<?php }?>
										<?php if($penreq->wishtoapply){?>
											<br/><a href="<?php echo site_url('dashboard/creditapplication/'.$penreq->from->id);?>">View Application</a>
										<?php }?>
										
									</div>
									<div class="description">
										<a class="btn btn-primary btn-xs btn-mini" href="<?php echo site_url('dashboard/acceptreq/'.$penreq->id);?>">Accept</a>
										&nbsp;
										<a class="btn btn-primary btn-xs btn-mini" href="<?php echo site_url('dashboard/rejectreq/'.$penreq->id);?>">Reject</a>
									</div>
								</div>
								
							</div>
							</a>
							
						<?php }?>					
					</div>
				</div>
			
				<div class="tiles white">
					  <div class="tiles-body">
						<div class="tiles-title">
							PURCHASING COMPANIES IN YOUR NETWORK
						</div>							
					  <br>
						<?php if(!$networkjoinedpurchasers){?>
							<span class="label label-important">You have no purchasing companies in your network</span>
						<?php }
							else
							foreach($networkjoinedpurchasers as $njp){?>
							
							<div class="notification-messages">
								<div class="message-wrapper">
									<div class="heading">
										<?php echo $njp->fullname;?> of <?php echo $njp->companyname;?>
										<br/><a href="<?php echo site_url('company/tier');?>">Manage Connection</a>
										<?php if($njp->accountnumber){;?>
										/ Account Number: <?php echo $njp->accountnumber;?>
										
										<?php }?>
										<?php if($njp->wishtoapply){?>
											/ <a href="<?php echo site_url('dashboard/creditapplication/'.$njp->purchasingadmin);?>">View Application</a>
										<?php }?>
										<br/>Resident of: <?php echo $njp->address;?>
									</div>
								</div>
							</div>
						<?php }?>					
					</div>
				</div>	
			</div>
		</div>
		
	</div>
</div>
		