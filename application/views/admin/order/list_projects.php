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
	
<?php }?>
<?php $total;$i = 0;
									    	$gtotal = 0;
									    	foreach($orderitems as $item)
									    	{
									    		$total = number_format($item->quantity * $item->price,2);
									    		$gtotal+=$total;
									    		$i++;
											}
											$total = number_format($gtotal,2);
											
														$tax = number_format(($order->taxpercent * $total)/100,2);
											?>
<script type="text/javascript">
            $(document).ready(function() {
                
                $(".form-horizontal").submit(function(obj){
                                var msj = "Please confirm the assignment of Order #<?php echo $order->ordernumber;?> , To Project Name ("+$('.pid option:selected').text()+") , Cost-Code ("+$('.ccid option:selected').text()+"), Total Value of Order =  <?php echo $total+$tax;?> ";
                		if(!confirm(msj)){return false;}
                 });
                $(".pid").change(function(event){
                    <?php if(is_null($order->project)){?>
                    
                	<?php }else{?>
                	var msj = "Are you sure you wish to re-assign order value";
                	<?php }?>
                	
                        $.ajax("<?php echo base_url()?>admin/costcode/get_cc_by_project",{data:{projectfilter:$(this).val()},dataType:"json",type:"POST",success:function(data,textStatus){
                                                $(".ccid").empty();
                                   for(var i=0;i<data.length;i++){

                                                $(".ccid").append('<option value="'+data[i].id+'">'+data[i].code+'</option>');
                                           }
                                   $(".cost-code").show();
                                   }
                               });
                        
                    });
            });
</script>
<section class="row-fluid">
	<div class="box">
		<div class="span12">
			
			<h3 class="box-header">Assign Order to Project</h3>
			<div class="well">
				<?php if(@$orderitems[0]->accepted == 1){?>
				<form class="form-horizontal" action="<?php echo base_url()?>admin/order/add_to_project/<?php echo $orderid;?>" method="post" >
					<div class="control-group">
						<label for="inputEmail" class="control-label">
						<strong>Select Project to assign order to</strong>
						</label>
						<div class="controls">
							<select class="pid" name="pid">
								<option value="0">Select</option>
								<?php foreach($projects as $p){?>
								<option value="<?php echo $p->id;?>" <?php if($p->id==$order->project){ echo "selected"; }?>><?php echo $p->title?></option>
								<?php }?>
							</select>
						</div>
					</div>
					
					<div class="control-group cost-code" <?php if(is_null($order->costcode)){ echo "style='display:none'";}?>>
						<label for="inputEmail" class="control-label">
						<strong>Select Cost Code</strong>
						</label>
						<div class="controls">
							<select name="ccid" class="ccid" >
							<?php if(!is_null($order->costcode)){?><option value="<?php echo $order->costcode;?>"><?php echo $order->codeName;?></option><?php }?>
							</select>
						</div>
					</div>
				
				<div>
				<h3 class="box-header">Order Items for Order# <?php echo $order->ordernumber;?></h3>
						<table class="table">
                                        <thead>
                                            <tr>
                                                <th style="width:20%">ORDER #</th>
                                                <th style="width:30%">DATE</th>
                                                <th style="width:20%">TYPE</th>
                                           
                                            </tr>
                                        </thead>
                                        
										<tbody>
											<tr>
												<td><?php echo $order->ordernumber;?></td>
												<td><?php echo date('m/d/Y',strtotime($order->purchasedate));?></td>
												<td><?php echo $order->type;?></td>
											</tr>
											<tr><td>SubTotal:</td><td></td><td>$<?php $i = 0;
									    	$gtotal = 0;
									    	foreach($orderitems as $item)
									    	{
									    		$total = number_format($item->quantity * $item->price,2);
									    		$gtotal+=$total;
									    		$i++;
									    		log_message('debug',var_export($item,true));
											}
											echo number_format($gtotal,2);
											?></td></tr>
											<tr><td>Tax:</td><td></td><td>$<?php echo $tax;?></td></tr>
											<tr><td>Total:</td><td></td><td>$<?php echo $tax+number_format($gtotal,2);?></td></tr>
										</tbody>
										</table>
				</div>
				<input type="submit" value="Assign">
				</form>
				<?php }	elseif(@$orderitems[0]->accepted == 0){
					 		echo 'Order status is pending approval by supplier(s) on P.O.  When status is changed, you will receive an email notification. You can <br> return here after your order is processed to review your order, pay your supplier and assign this order to a project and cost code';
					  } elseif(@$orderitems[0]->accepted == -1){
					  		echo 'Order is declined by one of the supplier(s)';	
					  }
				?>
			</div>
					
			<br/>
			
			<div>
				<?php if($orderitems) { ?>
                                    
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width:20%">Item Code</th>
                                                 <th style="width:20%">Company</th>
                                                <th style="width:30%">Quantity</th>
                                                <th style="width:20%">Price</th>
                                                <th style="width:10%">Total</th>
                                                <th style="width:10%">Status</th>
                                            </tr>
                                        </thead>
                                        
										<tbody>
							              <?php
									    	$i = 0;
									    	$gtotal = 0;
									    	foreach($orderitems as $item)
									    	{
									    		$total = number_format($item->quantity * $item->price,2);
									    		$gtotal+=$total;
									    		$i++;
									      ?>
                                            <tr>
                                                <td><?php echo $item->itemdetails->itemname;?></td>
                                                <td><?php echo $item->companyname;?></td>
                                                <td><?php echo $item->quantity;?></td>
                                                <td>$<?php echo $item->price;?></td>
                                                <td><?php echo $total;?></td>
                                                <td><?php if($item->status=="Void") echo "Declined"; else echo $item->status;?></td>
                                            </tr>
                                          <?php } ?>
                                            <tr>
                                                <td colspan="3">SubTotal</td>
                                                <td colspan="2">$<?php echo number_format($gtotal,2);?></td>
                                            </tr>
                                            <tr><td  colspan="3">Tax</td><td colspan="2">$<?php echo $tax;?></td></tr>
											<tr><td  colspan="3">Total</td><td colspan="2">$<?php echo $tax+number_format($gtotal,2);?></td></tr>
                                        </tbody>
                                    </table>
            	<?php }else{ ?>
            	No Purchase Orders.
            	<?php }?>
            </div>
		</div>
	
		
	</div>
</section>