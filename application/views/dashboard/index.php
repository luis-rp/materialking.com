<?php echo '<script>var readnotifyurl="'.site_url('dashboard/readnotification').'";</script>'?>
<?php echo '<script>var emailalerturl="'.site_url('dashboard/sendemailalert').'";</script>'?>
<?php echo '<script>var alertsentdateurl="'.site_url('dashboard/alertsentdate').'";</script>'?>
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

function sendemailalert(invoice,admin,price,datedue, invoiceid){

	//alert(invoice);
	$.ajax({
		type:"post",
		url: emailalerturl,
		async:false,
		data: "invoice="+invoice+"&admin="+admin+"&price="+price+"&datedue="+datedue+"&invoiceid="+invoiceid
	}).done(function(data){
		if(data == "success")
			$('#'+invoiceid).html('Email Alert Sent');
		else
			$('#'+invoiceid).html('*Error in sending Email');
	});

	$.ajax({
		type:"post",
		url: alertsentdateurl,
		async:false,
		data: "invoice="+invoice+"&admin="+admin+"&price="+price+"&datedue="+datedue+"&invoiceid="+invoiceid
	}).done(function(data){

		$('#'+invoiceid).html(data);

	});

}

</script>
    <div class="content">
    <?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">
			<h3>Dashboard </h3>
		</div>

	   <div id="container">

		<div class="row">

			<div class="col-md-6 col-sm-6">
				<div class="tiles white">
				 <div style="height:500px;overflow:auto;">
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

					<div class="tiles-body">
						<div class="controller">
							<a class="reload" href="javascript:;"></a>
							<a class="remove" href="javascript:;"></a>
						</div>
						<div class="tiles-title">
							ERROR LOG
						</div>
					  <br>
						<?php if(!$logDetails){?>
							<span class="label label-important">No New Errors</span>
						<?php }?>
						<?php foreach($logDetails as $key=>$errorLog){ ?>
					
						<div class="date pull-right">
								<a class="remove" href="<?php if(isset($errorLog->id)) echo site_url('dashboard/close/'.$errorLog->id); else echo '';?>">X</a>
						  </div>

							<a href="<?php echo site_url('quote/track/'.$errorLog['quoteid'].'/'.$errorLog['award']);?>" onclick="return readnotification('<?php echo $errorLog['id']?>');">
							<div class="notification-messages " onclick="return readnotification('<?php echo $errorLog['id']?>');">
								<div class="user-profile">
									<img width="35" height="35" data-src-retina="<?php echo base_url();?>templates/front/assets/img/alert.png" data-src="<?php echo base_url();?>templates/front/assets/img/alert.png" alt="" src="<?php echo base_url();?>templates/front/assets/img/alert.png">
								</div>
								<div class="message-wrapper">
									<div class="heading">
										For the PO# <?php echo $errorLog['ponum'];?>   &nbsp;&nbsp; Error :  <?php echo $errorLog['error'];?> 
									</div>
									<div class="description">
									    Error Comments:	<?php echo $errorLog['comments'];?> / <?php echo $tago[$key];?>
									</div>
								</div>
							</div>
							</a>
						<?php }?>
						</div>
					</div>
					<div class="tiles-title extrabox">
					<div class="heading">SEND PAST DUE INVOICE ALERTS:</div>
					<table cellpadding="3">
					  <tr>
					  <td>Invoice</td>
					  <td>Due Date</td>
					  <td>&nbsp;</td>
					  <td>&nbsp;</td>
					  </tr>
				<?php foreach($invoices as $invoice) { ?>

					  <tr>
					  <td><?php echo $invoice->invoicenum; ?></td>
					  <td><?php echo $invoice->datedue; ?></td>
					  <td><input class="sendbutton" type="button" name="<?php echo $invoice->invoicenum; ?>" id="<?php echo $invoice->invoicenum; ?>" onclick="sendemailalert('<?php echo $invoice->invoicenum; ?>', '<?php echo $invoice->purchasingadmin;?>','<?php echo $invoice->totalprice; ?>', '<?php echo $invoice->datedue; ?>','<?php echo $invoice->id; ?>');" value="Send Alert" > </td>
					  <td class="errormsg" id="<?php echo $invoice->id; ?>"><?php if(isset($invoice->alertsentdate) && $invoice->alertsentdate!="") echo "Alert Sent ".date("m/d/Y",strtotime($invoice->alertsentdate)); ?></td>
					  </tr>


				<?php } ?>
				</table>
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
							else{
							foreach($newrequests as $penreq){?>

							<div class="notification-messages">
								<div class="user-profile">
									<img width="35" height="35" data-src-retina="<?php echo base_url();?>templates/front/assets/img/notification-alert.png" data-src="<?php echo base_url();?>templates/front/assets/img/notification-alert.png" alt="" src="<?php echo base_url();?>templates/front/assets/img/notification-alert.png">
								</div>
								<div class="message-wrapper">
									<div class="heading">
										<?php if($penreq->from){?>
										You got a request from <?php echo $penreq->from->fullname;?> of <?php echo $penreq->from->companyname;?>
										<?php }else{?>
										No request information
										<?php }?>
										&nbsp; <?php echo $penreq->tago;?>
										<?php if($penreq->accountnumber){;?>
										<br/>Account Number: <?php echo $penreq->accountnumber;?>
										<?php }?>
										<?php if($penreq->message){;?>
										<br/>Message: <?php echo $penreq->message;?>
										<?php }?>
										<?php if($penreq->wishtoapply){?>
											<?php if($penreq->from){?>
											<br/><a href="<?php echo site_url('dashboard/creditapplication/'.$penreq->from->id);?>">View Application</a>
											<?php }else{?>
											No Application information
											<?php }?>
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

						<?php } }?>
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
							else{
							foreach($networkjoinedpurchasers as $njp){?>
							<div class="date pull-right">
								<a class="remove" href="<?php echo site_url('dashboard/networkdelete/'.$njp->purchasingadmin);?>"
							onclick="javascript:return confirm('Do You Really Want to Delete This Company From Network?');">X</a>
						    </div>
							<div class="notification-messages">
								<div class="message-wrapper">
									<div class="heading">
										<?php echo $njp->fullname;?> of <?php echo $njp->companyname;?>
										<br/><a href="<?php echo site_url('company/tier');?>">Manage Connection</a>
										<?php if($njp->accountnumber){?>
										/ Account Number: <?php echo $njp->accountnumber;?>

										<?php }?>
										<?php if(isset($njp->message)){?>
										<br/>Message: <?php echo $njp->message;?>
										<?php }?>
										<?php if($njp->wishtoapply){?>
											/ <a href="<?php echo site_url('dashboard/creditapplication/'.$njp->purchasingadmin);?>">View Application</a>
										<?php }?>
										<br/>Resident of: <?php echo $njp->address;?>
									</div>
								</div>
							</div>
						<?php } }?>
					</div>
				</div>
				
			</div>
		</div>

	</div>
</div>
