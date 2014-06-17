<script type="text/javascript" charset="utf-8">
$(document).ready( function() {
	$("#status").select2();
});      
</script>

    <div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
			<h3>Order items for order# <?php echo $order->ordernumber?></h3>
			<?php if($order->txnid){?>
			<h3>Transaction ID: <?php echo $order->txnid?></h3>
			<?php }?>
			<h3>Buyer Email: <?php echo $order->email?></h3>
			<h3>Buyer Company: <?php echo $order->purchaser->companyname?></h3>
		</div>		
	   <div id="container">
		<?php 
	    	if($orderitems)
	    	{
	    	    if($order->txnid)
	    	    {
	    	        $orderitems[0]->paymenttype = 'Credit Card';
	    	        $orderitems[0]->paymentstatus = 'Paid';
	    	        $orderitems[0]->paymentnote = $order->txnid;
	    	    }
	    ?>
		<div class="row">
                    <div class="col-md-12">
                        <div class="grid simple ">
                            <div class="grid-title no-border">
                            	<?php if($orderitems[0]->accepted == 1){?>
                                	<br/>Payment Status: <font color='red'><?php echo strtoupper($orderitems[0]->paymentstatus);?></font>
                                	<?php if($orderitems[0]->paymenttype != ''){?>
                                	<br/>Payment Type: <?php echo $orderitems[0]->paymenttype;?>
                                	<?php }?>
                                	<?php if($orderitems[0]->paymenttype == 'Requested Payment' && $orderitems[0]->paymentnote != ''){?>
                                	<br/>Requested on: <?php echo $orderitems[0]->paymentnote;?>
                                	<?php }?>
                                	<?php if($orderitems[0]->paymenttype == 'Cash' && $orderitems[0]->paymentnote != ''){?>
                                	<br/>Notes: <?php echo $orderitems[0]->paymentnote;?>
                                	<?php }?>
                                	<?php if($orderitems[0]->paymenttype == 'Check' && $orderitems[0]->paymentnote != ''){?>
                                	<br/>Check #: <?php echo $orderitems[0]->paymentnote;?>
                                	<?php }?>
                                	<?php if($orderitems[0]->paymenttype == 'Credit Card' && $orderitems[0]->paymentnote != ''){?>
                                	<br/>Txn Id: <?php echo $orderitems[0]->paymentnote;?>
                                	<?php }?>
                                	<br/>
                                	<?php if($orderitems[0]->paymentstatus=='Paid'){?>
    	                                <?php if($orderitems[0]->status != ''){?>
    	                            	Order Status: <?php echo $orderitems[0]->status?$orderitems[0]->status:'Pending';?>
    	                            	<?php }?>
    	                            	<br/>
    	                            	<?php if($orderitems[0]->status == 'Pending' || $orderitems[0]->status == ''){?>
    	                            	<a href="<?php echo site_url('order/status/'.$order->id.'/Accepted'); ?>">Approve</a>
    	                            	&nbsp;
    	                            	<a href="<?php echo site_url('order/status/'.$order->id.'/Void'); ?>">Void</a>
    	                            	<?php }?>
                                	<?php }elseif($orderitems[0]->paymentstatus=='Unpaid'){?>
                                		<a href="<?php echo site_url('order/requestpayment/'.$order->id); ?>">Request Payment</a>
                                	<?php }elseif($orderitems[0]->paymentstatus=='Requested Payment'){?>
                                		<?php echo date('m/d/Y', strtotime($orderitems[0]->paymentnote));?>
                                	<?php }?>
                                <?php }elseif($orderitems[0]->accepted == 0){?>
                                <a href="<?php echo site_url('order/accept/'.$order->id.'/1'); ?>">Accept Order</a>
                                <a href="<?php echo site_url('order/accept/'.$order->id.'/-1'); ?>">Reject Order</a>
                                <?php }else{?>
                                ORDER DECLINED
                                <?php }?>
                            </div>
                            <div class="grid-body no-border">
                                    <table id="datatable" class="table no-more-tables general">
                                        <thead>
                                            <tr>
                                                <th style="width:20%">Item</th>
                                                <th style="width:30%">Quantity</th>
                                                <th style="width:20%">Price</th>
                                                <th style="width:10%">Total</th>
                                            </tr>
                                        </thead>
                                        
										<tbody>
							              <?php
									    	$i = 0;
									    	$gtotal = 0;
									    	foreach($orderitems as $item)
									    	{
									    		$i++;
									    		$total = $item->quantity * $item->price;
									    		$gtotal += $total;
									    		
									      ?>
                                            <tr>
                                                <td><?php echo $item->itemdetails->itemname;?></td>
                                                <td><?php echo $item->quantity;?></td>
                                                <td>$<?php echo $item->price;?></td>
                                                <td>$<?php echo number_format($total,2);?></td>
                                            </tr>
                                          <?php } ?>
                                          <?php 
                                                $taxpercent = $order->taxpercent;
                                        	    $tax = $gtotal*$taxpercent/100;
                                        	    $totalwithtax = $tax+$gtotal;
                                          ?>
                                            <tr>
                                                <td colspan="3">Total</td>
                                                <td>$<?php echo number_format($gtotal,2);?></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">Tax</td>
                                                <td>$<?php echo number_format($tax,2);?></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">Total</td>
                                                <td>$<?php echo number_format($totalwithtax,2);?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <br/>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php } else { ?>
                
                <div class="errordiv">
      				 <div class="alert alert-info">
                      <button data-dismiss="alert" class="close"></button>
                      <div class="msgBox">
                      No Order Items Detected on System.
                      </div>
                     </div>
      			</div>
                <?php }?>
                <?php if($messages){?>
                Earlier Messages
                <table class="table table-bordered">
                	<tr>
                		<th>Date</th>
                		<th>Subject</th>
                		<th>From</th>
                		<th>To</th>
                		<th>Message</th>
                	</tr>
                	<?php foreach($messages as $message){?>
                	<tr <?php if($message->paymentrequest){?>style="color:red"<?php }?>>
                		<td><?php echo date('m/d/Y',strtotime($message->senton)); ?></td>
                		<td><?php echo $message->subject;?></td>
                		<td><?php echo $message->fromname;?></td>
                		<td><?php echo $message->toname;?></td>
                		<td><?php echo $message->message;?></td>
                	</tr>
                	<?php }?>
                </table>
                <?php }?>
                <?php if($order->email){?>
                Send Message:
                <br/>
				<form action="<?php echo site_url('order/sendemail/'.$order->id);?>" method="post">
					<input type="hidden" name="to" value="<?php echo $order->email;?>"/>
					<table>
						<tr>
							<td>Subject:</td>
							<td>
								<input type="text" name="subject" value="Message for order# <?php echo $order->ordernumber;?>" style="width: 250px;"/>
							</td>
						</tr>
						<tr>
							<td>Message:</td>
							<td><textarea name="message" rows="5" style="width: 350px;"></textarea>
						</tr>
						<?php if($orderitems[0]->paymentstatus != 'Paid'){?>
						<tr>
							<td>Request for payment:</td>
							<td>
								<input type="checkbox" value="1" name="paymentrequest"/>
							</td>
						</tr>
						<?php }?>
						<tr>
							<td></td>
							<td><input type="submit" class="btn btn-primary" value="Send"/></td>
						</tr>
					</table>
				</form>
				<?php }?>
		</div>
	  </div> 