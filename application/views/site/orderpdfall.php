
			<h4>Order #: <?php echo $orderid;?></h4>
			<br/>
			<table class="table table-bordered span12">
			 <tr><td><strong>Supplier Name:</strong></td><td> <?php echo $cart[0]['companydetails']->title;?> </td></tr>
			 <tr><td><strong>Supplier Address:</strong></td><td> <?php echo $cart[0]['companydetails']->address;?> </td></tr>
			 <tr><td><strong>Supplier Phone:  </strong></td><td><?php echo $cart[0]['companydetails']->phone;?></td> </tr> 
			 <tr><td><strong>Type :</strong> </td><td><?php echo $paymentType;?></td> </tr>
			 <tr><td colspan="5">&nbsp;</td></tr>
			 </table>
			  <div class="property-detail">
			 <table class="table table-bordered" border="1" width="50%">
            	<tr>
            		<th>Item</th>
            		<th>Company</th>
            		<th>Price</th>
            		<th>Quantity</th>
            		<th>Total</th>
            	</tr>
            	<?php 
                	$gtotal=0; 
                	foreach ($cart as $item)
                	{
                	    $total = $item['quantity']*$item['price'];
                	    $gtotal+=$total;
            	?>
            	<tr>
            		<td><?php echo $item['itemdetails']->itemname;?></td>
            		<td><?php echo $item['companydetails']->title;?></td>
            		<td><?php echo $item['price'];?></td>
            		<td><?php echo $item['quantity']?></td>
            		<td><?php echo number_format($total,2);?></td>
            	</tr>
            	<?php }?>
            	<tr><td colspan="5">&nbsp;</td></tr>
            	<?php 
            	    $tax = $gtotal*$settings->taxpercent/100;
            	    $totalwithtax = number_format($tax+$gtotal,2);
            	?>
            	<tr>
            		<td colspan="4" align="right">Total</td>
            		<td>$<?php echo number_format($gtotal,2);?></td>
            	</tr>
            	
            	<tr>
            		<td colspan="4" align="right">Tax</td>
            		<td>$<?php echo number_format($tax,2);?></td>
            	</tr>
            	
            	<tr>
            		<td colspan="4" align="right">Total</td>
            		<td>$<?php echo $totalwithtax;?></td>
            	</tr>
            	
            </table>
            </div>