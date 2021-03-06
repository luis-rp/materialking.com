<?php echo '<script>var readnotifyurl="'.site_url('dashboard/readnotification').'";</script>'?>
<?php echo '<script>var emailalerturl="'.site_url('dashboard/sendemailalert').'";</script>'?>
<?php echo '<script>var alertsentdateurl="'.site_url('dashboard/alertsentdate').'";</script>'?>

<script type="text/javascript">
$.noConflict();
 </script>

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

function sendemailalert(invoice,admin,price,datedue,invoiceid,ponum){

	//alert(invoice);
	$.ajax({
		type:"post",
		url: emailalerturl,
		async:false,
		data: "invoice="+invoice+"&admin="+admin+"&price="+price+"&datedue="+datedue+"&invoiceid="+invoiceid+"&ponum="+ponum
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

function invoice(invoicenum,quoteid)
{
	$("#invoicenum").val(invoicenum);
	$("#invoicequote").val(quoteid);
	$("#invoiceform").submit();
}

function preloadoptions(fromid)
	 {
	 	//alert("#smodal"+fromid);
    	$("#smodal"+fromid).modal();   	   
     }
     
     function preload(fromid)
	 {
	 	//alert("#smodal"+fromid);
    	$("#pmodal"+fromid).modal();   	   
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
						 
						 <div class="tiles-title">NOTIFICATIONS</div><br>
						 <?php if(!$newnotifications){?>
							<span class="label label-important">No New Notifications</span>
						 <?php }?>
						 <?php foreach($newnotifications as $newnote){
						 	 ?>

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
						<?php }  ?>
					</div>
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
								<a class="remove" href="<?php if(isset($errorLog['id'])) echo site_url('dashboard/errorlogclose/'.$errorLog['id']); else echo '';?>">X</a>
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

							<div class="notification-messages" style="height:140px; overflow:auto;">

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
											<br/><a href="<?php echo site_url('dashboard/creditapplication/'.$penreq->from->id);?>">View Application</a>&nbsp;&nbsp;
											<?php if($formdata){?> <a href="<?php echo site_url('company/formsubmission/'.$penreq->from->id);?>" target="_blank" >View Submission Data</a><?php }?>
											<?php }else{?>
											No Application information
											<?php }?>
										<?php }?>
                                           
                                           <a href="javascript:void(0)" onclick="preload('<?php echo htmlentities(@$penreq->fromid)?>');">View Stats</a>
									</div>
									<div class="description">
										<a class="btn btn-primary btn-xs btn-mini" href="<?php echo site_url('dashboard/acceptreq/'.$penreq->id);?>">Accept</a>
										&nbsp;
										<a class="btn btn-primary btn-xs btn-mini" href="<?php echo site_url('dashboard/rejectreq/'.$penreq->id);?>">Reject</a>
									</div>
								</div>

							</div>
						<?php } }?>
					</div>
				</div>
				
					
					<div class="tiles white">
					  <div class="tiles-body">
						
						<div class="tiles-title">
							SEND PAST DUE INVOICE ALERTS
						</div>
					  <br>
						<?php if(!$invoices){ ?>
							<span class="label label-important">No Past Due Invoices</span>
						<?php } ?>

							<div class="notification-messages" style="background-color:#fff;">
								<div class="message-wrapper" style="height:auto !important;">
									<div class="heading">
										<table class="table">
											<?php if(isset($invoices) && count($invoices)>0) { ?>
											  <tr>
												  <th>Invoice</th>
												  <th>Due Date</th>
												  <th>Action</th>
												  <th>Message</th>
											  </tr>
										<?php foreach($invoices as $invoice) {  ?>
											  <tr>
												  <td><a href="javascript:void(0)" onclick="invoice('<?php echo $invoice->invoicenum;?>','<?php echo $invoice->quoteid;?>');"><?php echo $invoice->invoicenum; ?></a>
												  </td>
												  <td><?php echo $invoice->datedue; ?>
												  </td>
												  <td><input class="sendbutton" type="button" name="<?php echo $invoice->invoicenum; ?>" id="<?php echo $invoice->invoicenum; ?>" onclick="sendemailalert('<?php echo $invoice->invoicenum; ?>', '<?php echo $invoice->purchasingadmin;?>','<?php echo $invoice->totalprice; ?>', '<?php echo $invoice->datedue; ?>','<?php echo $invoice->id; ?>','<?php echo $invoice->ponum;?>');" value="Send Alert" >
												   </td>
												  <td class="errormsg" id="<?php echo $invoice->id; ?>"><?php if(isset($invoice->alertsentdate) && $invoice->alertsentdate!="") echo "Alert Sent ".date("m/d/Y",strtotime($invoice->alertsentdate)); ?></td>
											  </tr>					
										<?php } }?>										
									</table>
                                  </div>
                                  
                                  <div class="description">
										&nbsp;
									</div>
                                  
								</div>
							</div>					
					</div>
					<form id="invoiceform" method="post" action="<?php echo site_url('quote/invoice');?>">
                	<input type="hidden" id="invoicenum" name="invoicenum"/>
                	<input type="hidden" id="invoicequote" name="invoicequote"/>
                </form>
				</div>

				<!--<div class="tiles white">
					  <div class="tiles-body">
						<div class="tiles-title">
							PURCHASING COMPANIES IN YOUR NETWORK
						</div>
					  <br>
						<?php if(!$networkjoinedpurchasers){?>
							<span class="label label-important">You have no purchasing companies in your network</span>
						<?php }
							else{ ?>
							<form id="allcompany" method="POST" action="<?php echo site_url('dashboard');?>">
							<input type="submit" name="allcompany" class="btn btn-primary btn-xs" value="Show All Companies">
							</form>

							<?php
							foreach($networkjoinedpurchasers as $njp){ ?>
                                       
							<div class="notification-messages">
								<div class="date pull-right">
								<a class="remove" href="<?php echo site_url('dashboard/networkdelete/'.$njp->purchasingadmin);?>"
									onclick="javascript:return confirm('Do You Really Want to Delete This Company From Network?');">X</a>
						    	</div>
								<div class="message-wrapper" style="width:100% !important;">
									<div class="heading" style="padding-right:2px !important;font-size:12px !important;">
										<?php echo $njp->fullname;?> of <?php echo $njp->companyname;?>
										 <?php if($njp->creditonly=='1') {?>
										 &nbsp;&nbsp;&nbsp;<span style="color:red;">*Credit Card Only Account.</span><?php } ?>
										<br/><a href="<?php echo site_url('company/networkconnections');?>">Manage Connection</a>
										<?php if($njp->accountnumber){?>
										/ Account Number: <?php echo $njp->accountnumber;?>

										<?php }?>
										<?php if(isset($njp->message)){?>
										<br/>Message: <?php echo $njp->message;?>
										<?php }?>
										<?php if($njp->wishtoapply){?>
											/ <a href="<?php echo site_url('dashboard/creditapplication/'.$njp->purchasingadmin);?>">View Application</a>
										<?php }?>
						&nbsp;<a href="javascript:void(0)" onclick="preloadoptions('<?php echo htmlentities(@$njp->purchasingadmin)?>');">View Stats</a>&nbsp;
										<br/>Resident of: <?php echo $njp->address;?>
								</div>
							</div>
							</div>
						<?php }  }?>
					</div>
				</div>-->

			</div>
		</div>

	</div>
</div>

 <?php $oldfromid=""; $i=0; foreach ($userdata as $u){  ?>
 <div id="smodal<?php echo $u->purchasingadmin;?>" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <i class="icon-credit-card icon-7x"></i>
          <h4 class="semi-bold" id="myModalLabel" style="text-align:left"><?php echo @$u->companyname;?></h4> 
          <h5 class="semi-bold" id="myModalLabel" style="text-align:left"><?php echo @$u->fullname;?></h5>
          <h6 class="semi-bold" id="myModalLabel" style="text-align:left"><?php echo @$u->address;?></h6>       
        </div>
        
        <div class="modal-body">       
        	<div>
          		<h6 class="semi-bold" id="myModalLabel" style="text-align:center">Member Since&nbsp;
          		<?php $olddate1=strtotime(@$u->regdate); $newdate1 = date('M d, Y', $olddate1); echo $newdate1; ?></h6> 
        	</div>
        
         	<div>
        		<h4 class="semi-bold" id="myModalLabel">User Statistics</h4>
        	</div>
        	<hr style="height:2px;border-width:0;color:green;background-color:green">
	        <div style="margin-left:90px;">      
		       <div>
		        	<p><?php echo "Total Number of Projects&nbsp;:".count(@$u->projects);?></p>
		        </div> 
		        <div>
		        	<p><?php echo "Total Number of Direct Orders&nbsp;:".count(@$u->directquotes);?></p>
		        </div> 
		        <div>
		        	<p><?php echo "Total Number of Quotes&nbsp;:".count(@$u->quotes);?></p>
		        </div> 
		        <div>
		        	<p><?php echo "Total Number of Awarded Quotes&nbsp;:".@$u->awarded;?></p>
		        </div>  
	        </div>	  
        </div>       
        <div class="modal-footer">
          <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
        </div>
        
      </div>   
    </div>
  </div>
  <?php $oldfrommid=$u->purchasingadmin; $i++; } ?> 
  
  <?php //echo "<pre>"; print_r($udata); die;?>
  <?php $oldfromid=""; $i=0; foreach ($udata as $u){  ?>
 <div id="pmodal<?php echo $u->fromid;?>" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <i class="icon-credit-card icon-7x"></i>
          <h4 class="semi-bold" id="myModalLabel" style="text-align:left"><?php echo @$u->from->companyname;?></h4> 
          <h5 class="semi-bold" id="myModalLabel" style="text-align:left"><?php echo @$u->from->fullname;?></h5>
          <h6 class="semi-bold" id="myModalLabel" style="text-align:left"><?php echo @$u->from->address;?></h6>       
        </div>
        
        <div class="modal-body">       
        	<div>
          		<h6 class="semi-bold" id="myModalLabel" style="text-align:center">Member Since&nbsp;
          		<?php $olddate1=strtotime(@$u->from->regdate); $newdate1 = date('M d, Y', $olddate1); echo $newdate1; ?></h6> 
        	</div>
        
         	<div>
        		<h4 class="semi-bold" id="myModalLabel">User Statistics</h4>
        	</div>
        	<hr style="height:2px;border-width:0;color:green;background-color:green">
	        <div style="margin-left:90px;">      
		       <div>
		        	<p><?php echo "Total Number of Projects&nbsp;:".count(@$u->projects);?></p>
		        </div> 
		        <div>
		        	<p><?php echo "Total Number of Direct Orders&nbsp;:".count(@$u->directquotes);?></p>
		        </div> 
		        <div>
		        	<p><?php echo "Total Number of Quotes&nbsp;:".count(@$u->quotes);?></p>
		        </div> 
		        <div>
		        	<p><?php echo "Total Number of Awarded Quotes&nbsp;:".@$u->awarded;?></p>
		        </div>  
	        </div>	  
        </div>       
        <div class="modal-footer">
          <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
        </div>
        
      </div>   
    </div>
  </div>
  <?php $oldfrommid=$u->fromid; $i++; }  ?>  