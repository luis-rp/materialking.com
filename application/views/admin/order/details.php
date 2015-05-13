<script src="<?php echo base_url(); ?>templates/site/assets/js/creditcard.js"></script>

<?php echo '<script>var updateorderitemreceivedurl ="' . site_url('admin/order/updateorderitemreceived') . '";</script>' ?>

<script> 
	function validatecc()
	{		  
	  cn = $("#card").val();
	  ct =  $("#creditcardtypes").val();
	  //alert(cn+'-'+ct);return false;
	  if (!checkCreditCard (cn,ct)) {
		alert (ccErrors[ccErrorNo]);
		return false;
	  }

	  cvc = $("#cvc").val();	  	  
	  if(cvc.length != 3 || isNaN(cvc))
	  {
		  alert('Wrong cvc code');
		  return false;
	  }
		
	  return true;
	}
</script>
<script type="text/javascript" charset="utf-8">
$(document).ready( function() {
});
function paycc(ptype,company, amount, subtotal)
{
	if(ptype != 'Credit Card')
	{
		return true;
	}
	$('#paymenttype' + company + " option:first-child").attr("selected", true);
	$("#ccpaycompany").val(company);
	$("#ccpayamount").val(amount);
	$("#ccpaysubtotal").val(subtotal);
	$("#ccpayamountshow").html(amount);
	$("#paymodal").modal();
}

function updateorderitemrecvd(orderitemid, quantity,itemid){
	var received = 0;
	if($('#isreceived'+orderitemid).is(':checked'))
		received = 1;
		
		$.ajax({
            type:"post",
            url: updateorderitemreceivedurl,
            data: "isreceived="+received+"&id="+orderitemid+"&quantity="+quantity+"&itemid="+itemid  
        }).done(function(data){    
        	
        	if(data==1)
        	alert('item received status updated successfully!');
        	
        });
}

function viewitems2(itemid)
{
	var serviceurl = '<?php echo base_url()?>admin/itemcode/ajaxdetail/'+ itemid;
	//alert(quoteid);
	$("#quoteitemdetails").html('loading ...');

	$.ajax({
		type:"post",
		url: serviceurl,
	}).done(function(data){			
		$("#quoteitemdetails").html(data);
		$("#quoteitemdetails").css({display: "block"});
		$("#quoteitemdetailsm").css({display: "block"});
		$("#quoteitemdetailsm").removeClass("hide");
		//$("#quoteitemdetailsm").modal();
	});
}

function closepop()
{
	$("#quoteitemdetails").html('');
	$("#quoteitemdetails").css({display: "none"});
	$("#quoteitemdetailsm").css({display: "none"});
}

</script>
<a class="btn btn-green" href="<?php echo base_url();?>admin/order">&lt;&lt; Back</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo site_url('admin/order/details_export').'/'.$orderid; ?>" class="btn btn-green">Export</a>&nbsp;&nbsp;<a href="<?php echo site_url('admin/order/details_pdf').'/'.$orderid; ?>" class="btn btn-green">View PDF</a>
<a href="<?php echo base_url();?>admin/order/add_to_project/<?php echo $orderid;?>" class="btn btn-green"> Assign Order</a>
<section class="row-fluid">
	<table>
	<tr>
<!--	<td></td>
	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>-->
	<td><?php if(@$orderitems[0]->accepted == 0){ echo "<b>Awaiting P.O. Approval from Supplier. Once Supplier(s) Accept your Order, You will receive <br> an E-mail Notification. You can return here after your order is Processed to Pay for your <br> order, and Assign the Order cost to a Project and Cost Code<b>"; } ?></td>
	</tr>
	</table>
    <h3 class="box-header">Order items for order# <?php echo $order->ordernumber?> <?php if(!is_null($order->project)){ echo "Order assigned to ".$order->prjName;}?></h3>
	<div class="box">
	  <div class="span12">
	
	   <?php echo $this->session->flashdata('message'); ?>
	    

			<div>
				<?php if($orderitems) { ?>
                                    
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width:10%">Item Code</th>
                            <th style="width:10%">Quantity</th>
                            <th style="width:10%">Item Image</th>
                            <th style="width:20%">Company</th>
                            <th style="width:20%">Price</th>
                            <th style="width:10%">Total</th>
                            <th style="width:10%">Status</th>
                            <th style="width:10%">Received</th>
                        </tr>
                    </thead>
                    
					<tbody>
		              <?php
				    	$i = 0;
				    	$gtotal = 0;
				    	$imgName = '';
				    	//echo '<pre>',print_r($orderitems);die;
				    	foreach($orderitems as $item)
				    	{
				    		 if (isset($item->itemdetails->item_img) && file_exists('./uploads/item/' . $item->itemdetails->item_img)) 
							 { 
							 	 $imgName = site_url('uploads/item/'.$item->itemdetails->item_img); 
							 } 
							 else 
							 { 
							 	 $imgName = site_url('uploads/item/big.png'); 
	                         }
	                         
				    		$total = $item->quantity * $item->price;
				    		$gtotal+=$total;
				    		$i++;
				      ?>
                        <tr>
                            <td><a href="javascript:void(0)" onclick="viewitems2('<?php echo $item->itemdetails->itemid; ?>');"> <?php echo $item->itemdetails->itemname;?> </a></td>                                    
                            <td><?php echo $item->quantity;?></td>
                            <td><img style="max-height: 120px; padding: 0px;width:80px; height:80px;float:left;" src='<?php echo $imgName;?>'></td>
                            <td><?php echo $item->companyName;?></td>
                            <td>$<?php echo $item->price;?></td>
                            <td>$<?php echo number_format($total,2);?></td>
                            <td><?php if($item->status=="Void") echo "Declined"; else echo $item->status;?></td>
                            <td><input type="checkbox" onchange="updateorderitemrecvd('<?php echo $item->id;?>','<?php echo $item->quantity;?>','<?php echo $item->itemid;?>');" id="isreceived<?php echo $item->id;?>" <?php if($item->isreceived==1) echo "checked";?>/></td>
                        </tr>
                      <?php } ?>
                      <?php 
                            $taxpercent = $order->taxpercent;
                    	    $tax = $gtotal * $taxpercent/100;
                    	    $totalwithtax = $tax+$gtotal+$order->shipping;
                      ?>
                        <tr>
                            <td colspan="5">Total</td>
                            <td colspan="3">$<?php echo number_format($gtotal,2);?></td>
                        </tr>
                        <tr>
                            <td colspan="5">Tax</td>
                            <td colspan="3">$<?php echo number_format($tax,2);?></td>
                        </tr>
                         <tr>
                            <td colspan="5">shipping Rate</td>
                            <td colspan="3">$<?php echo number_format($order->shipping,2);?></td>
                        </tr>
                        <tr>
                            <td colspan="5">Total</td>
                            <td colspan="3">$<?php echo number_format($totalwithtax,2);?></td>
                        </tr>
                    </tbody>
                </table>
            	<?php }else{ ?>
            	No Purchase Orders.
            	<?php }?>
            </div>
 
      </div>
    </div>
</section>

<?php if($companyamounts && $order->type=='Manual') { ?>
<section class="row-fluid">
	<h3 class="box-header">Payments for order# <?php echo $order->ordernumber?> <?php if(!is_null($order->project)){ echo "Order assigned to ".$order->prjName;}?></h3>
	<div class="box">
	  <div class="span12">
			<div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width:20%">Company</th>
                            <th style="width:10%">Amount</th>
                            <th style="width:10%">Tax</th>
                            <th style="width:10%">Shipping</th>
                             <th style="width:10%">Total amount</th>
                            <th style="width:10%">Payment</th>
                            <th style="width:20%">Type</th>
                            <th style="width:20%">Notes/Check No./Txn Id</th>
                            <th style="width:10%">Status</th>
                            <th style="width:10%">Action</th>
                        </tr>
                    </thead>
                    
					<tbody>
		              <?php
				    	$i = 0;
				    	foreach($companyamounts as $item)
				    	{
				    		$i++;
				    		$tax = $item->amount * $order->taxpercent / 100;
				    		$tax = number_format($tax,2);
				      ?>
				        <?php if($item->accepted==1 && $item->paymentstatus !='Paid'){?>
				        <form method="post" action="<?php echo site_url('admin/order/pay');?>">
				        <input type="hidden" name="paymentstatus" value="Paid"/>
				        <input type="hidden" name="amount" value="<?php echo $item->amount + $tax;?>"/>
				        <input type="hidden" name="orderid" value="<?php echo $order->id;?>"/>
				        <input type="hidden" name="company" value="<?php echo $item->id;?>"/>
                        <tr>
                            <td><?php echo $item->title;?></td>
                            <td>$<?php echo $item->amount;?></td>
                            <td>$<?php echo $tax;?></td>
                            <td>$<?php echo number_format($order->shipping,2);?></td>
                            <td>$<?php echo number_format(($order->shipping+$tax+$item->amount),2);?></td>
                            <td>
                            	
                            	<?php if($item->paymentstatus =='Requested Payment'){?>
                            	<font color="red">
                            	<?php echo $item->paymentstatus;?>
                            	<br>
                            	<?php echo date('m/d/Y',strtotime($item->paymentnote)); ?>
                            	</font>
                            	<?php }else{echo $item->paymentstatus;}?>
                            </td>
                            <td>
                                <select id="paymenttype<?php echo $item->id;?>" name="paymenttype" required onchange="paycc(this.value, <?php echo $item->id;?>, <?php echo $item->amount+$tax+$order->shipping;?>, <?php echo $item->amount;?>)">
                                    <option value="">Select Payment Type</option>
                                    <?php if($item->bankaccount && @$item->bankaccount->routingnumber && @$item->bankaccount->accountnumber){?>
                                    <option value="Credit Card">Credit Card</option>
                                    <?php }?>
                                    <option value="Cash">Cash</option>
                                    <option value="Check">Check</option>
                                </select>
                            </td>
                            <td><input type="text" name="paymentnote" required/></td>
                            <td><?php echo $item->status;?></td>
                            <td><input type="submit" value="Pay" class="btn btn-primary"/></td>
                        </tr>
                        </form>
                        <?php }else{?>
                        <tr>
                            <td><?php echo $item->title;?></td>
                            <td>$<?php echo $item->amount;?></td>
                            <td>$<?php echo $tax;?></td>
                            <td>$<?php echo number_format($order->shipping,2);?></td>
                            <td>$<?php echo number_format($order->shipping+$tax+$item->amount,2);?></td>
                            <td><?php echo $item->paymentstatus;?></td>
                            <td><?php echo $item->paymenttype;?></td>
                            <td><?php echo $item->paymentnote;?></td>
                            <td><?php if($item->status=="Void") echo "Declined"; else echo $item->status;?></td>
                            <td> 
                            	<?php if($item->accepted == -1){?>
                            	<font color="#ff0000">Declined</font>
                            	<?php }?>
                            </td>
                        </tr>
                        <?php }?>
                      <?php } ?>
                    </tbody>
                </table>
            </div>
 
      </div>
    </div>
</section>
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

Send Message:
<br/>
<form action="<?php echo site_url('admin/order/sendemail/'.$order->id);?>" method="post">
	
	<table>
		<tr>
			<td>Company:</td>
			<td>
				<select name="company">
    				<?php foreach($companies as $cmp){?>
    					<option value="<?php echo $cmp->id?>"><?php echo $cmp->title?></option>
    				<?php }?>
				</select>
			</td>
		</tr>
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
		<tr>
			<td></td>
			<td><input type="submit" class="btn btn-primary" value="Send"/></td>
		</tr>
	</table>
</form>

<?php if($transfers) { ?>
<section class="row-fluid">
	<h3 class="box-header">Transfers for order# <?php echo $order->ordernumber?> <?php if(!is_null($order->project)){ echo "Order assigned to ".$order->prjName;}?></h3>
	<div class="box">
	  <div class="span12">
			<div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width:40%">Transferid</th>
                            <th style="width:20%">Company</th>
                            <th style="width:10%">Amount</th>
                            <th style="width:20%">Status</th>
                        </tr>
                    </thead>
                    
					<tbody>
		              <?php
				    	$i = 0;
				    	foreach($transfers as $item)
				    	{  
				    		$i++;
 				    		  $sql = "SELECT shipping FROM 
							   ".$this->db->dbprefix('orderdetails')." t
							   WHERE orderid='$item->orderid' AND company=$item->company";
 								$getshipping = $this->db->query($sql)->result();
 							
							//$totamount = number_format($item->amount + $order->shipping + ($item->amount * $order->taxpercent/100),2);
 				    		//$totamount = number_format($item->amount + $getshipping[0]->shipping ,2); 
 				    		$totamount = number_format($item->amount,2);?> 
                        <tr>
                            <td><?php echo $item->transferid;?></td>
                            <td><?php echo $item->companynames;?></td>
                            <td>$<?php echo $totamount;?></td>
                            <td><?php echo ucfirst($item->status); ?></td>
                        </tr>
                      <?php } ?>
                    </tbody>
                </table>
            </div>
 
      </div>
    </div>
</section>
<?php }?>

<section class="row-fluid">
	<h3 class="box-header">Cost Codes for order# <?php echo $order->ordernumber?> <?php if(!is_null($order->project)){ echo "Order assigned to ".$order->prjName;}?></h3>
	<div class="box">
	  <div class="span12">

	    

			<div>
				<?php if(@$costcodes) { ?>
                                    
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width:40%">Cost Code</th>
                                                <th style="width:40%">Budget</th>
                                                <th style="width:40%">Spent</th>
                                                <th style="width:20%">Actions</th>
                                            </tr>
                                        </thead>
                                        
										<tbody>
							              <?php
									   
									    	foreach($costcodes as $cc)
									    	{
									    		
									      ?>
                                            <tr>
                                                <td><?php echo $cc->code;?></td>
                                                <td><?php echo $cc->cost;?></td>
                                                <td><?php echo round(@$totalwithtax,2);?></td>
                                                <td><a href="<?php  echo base_url(); ?>/admin/costcode/items/<?php echo $cc->code;?>" class="view"><span class="icon-2x icon-search"></span></a></td>

                                            </tr>
                                          <?php } ?>
                                            
                                        </tbody>
                                    </table>
            	<?php }else{ ?>
            	Order Not Assigned To Cost Code
            	<?php }?>
            </div>
 
      </div>
    </div>
</section>
        
<div id="paymodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
	
    <div class="modal-header">
    	<h3>
    	Pay by credit card
		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
		</h3>
	</div>
	<div class="modal-body" id="quoteitems">
        <form method="post" action="<?php echo site_url('admin/order/paybycc/');?>" onsubmit="return validatecc();">
			
	        <input type="hidden" name="orderid" value="<?php echo $order->id;?>"/>
	        <input type="hidden" id="ccpaycompany" name="company" value=""/>
	        <input type="hidden" id="ccpayamount" name="amount" value=""/>
	        <input type="hidden" id="ccpaysubtotal" name="subtotal" value=""/>	        
            <div class="control-group">
                <label class="control-label" for="card">
                   Total Amount to pay
                </label>
                <div class="controls">
                   $<span id="ccpayamountshow"></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="card">
                   Credit Card Number
                    <span class="form-required" title="This field is required.">*</span>
                </label>
                <div class="controls">
                    <input type="text" id="card" name="card" required style="width: 250px;">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="card">
                   Credit Card Type
                    <span class="form-required" title="This field is required.">*</span>
                </label>
                <div class="controls">
                    
		            <select id="creditcardtypes" name="creditcardtypes">
			            <option value="visa">Visa</option>
			            <option value="mastercard">Master Card</option>
			            <option value="amex">American Express</option>
			            <option value="dinersclub">Diners club</option>
			            <option value="discover">Discover</option>
		            </select> 
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="inputEmail">
                   CVC Code:
                    <span class="form-required" title="This field is required.">*</span>
                </label>
                <div class="controls">
                    <input type="text" id="cvc" name="cvc" required>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="inputMessage">
                    Expiry Date
                </label>

                <div class="controls">
                	
                    <select id="month" name="month" style="width: 95px;">
                    	<?php for($i=1; $i<13; $i++){?>
	                    <option value="<?php echo str_pad($i, 2, '0',STR_PAD_LEFT);?>"><?php echo str_pad($i, 2, '0',STR_PAD_LEFT);?></option>
	                    <?php }?>
                    </select>
                    
                    <select id="year" name="year" style="width: 125px;">
                    	<?php for($i = date('Y'); $i < date('Y')+10; $i++){?>
	                    <option value="<?php echo $i;?>"><?php echo $i;?></option>
	                    <?php }?>
                    </select>
                    
                </div>
            </div>

            <div class="form-actions">
                <input type="submit" class="btn btn-primary arrow-right" value="Process">
            </div>
        </form>
	</div>
    
</div>
 <div id="quoteitemdetailsm" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
        	
            <div class="modal-header">
        		<input style="float:right;margin-top:2px;" type="button" id="cls" name="cls" class="btn btn-green" value="close" onclick="closepop();" />
        		
        	</div>
        	<div class="modal-body" id="quoteitemdetails">
        	</div>
            
</div>