
			<h4>Order #: <?php echo $orderid;?></h4>
			<br/>
			 <table class="table table-bordered" border="1" width="80%">
            	<tr>
            		<th>Item Image</th>
            		<th>Type</th>
            		<th>Supplier Name</th>
            		<th>Supplier Address</th>
            		<th>Supplier Phone</th>
            		<th>Item</th>
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
                	    
                	     if ($item['itemdetails']->item_img && file_exists('./uploads/item/' . $item['itemdetails']->item_img)) 
						 { 
						 	 $imgName = site_url('uploads/item/'.$item['itemdetails']->item_img); 
						 } 
						 else 
						 { 
						 	 $imgName = site_url('uploads/item/big.png'); 
                         }
            	?>
            	<tr>
        			<td><img style="width:80px;height:80px" src="<?php echo $imgName;?>"/></td>
        			<td><?php echo $paymentType;?></td>
        			<td><?php echo $item['companydetails']->title;?></td>
        			<td><?php echo $item['companydetails']->address;?></td>
        			<td><?php echo $item['companydetails']->phone;?></td>
            		<td><?php echo $item['itemdetails']->itemname;?></td>            		
            		<td style="text-align:right;"><?php echo $item['price'];?></td>
            		<td style="text-align:center;"><?php echo $item['quantity']?></td>
            		<td style="text-align:right;"><?php echo number_format($total,2);?></td>
            	</tr>
            	<?php }?>
            	<tr><td colspan="9">&nbsp;</td></tr>
            	<?php 
            	    $tax = $gtotal*$settings->taxpercent/100;
            	    
					$shipping_vals=0;
					@session_start();
					if(isset($_SESSION['cart_shipping_vals']))
					$shipping_vals= $_SESSION['cart_shipping_vals'];
					
					$totalwithtax = number_format($tax+$gtotal+$shipping_vals,2);
            	?>
            	<tr>
            		<td colspan="8" align="right">Total</td>
            		<td style="text-align:right;">$<?php echo number_format($gtotal,2);?></td>
            	</tr>
            	
            	<tr>
            		<td colspan="8" align="right">Tax</td>
            		<td style="text-align:right;">$<?php echo number_format($tax,2);?></td>
            	</tr>
                
                <tr>
            		<td colspan="8" align="right">Shipment rate</td>
            		<td style="text-align:right;">$<?php echo number_format($shipping_vals,2);?></td>
            	</tr>
            	
            	<tr>
            		<td colspan="8" align="right">Total</td>
            		<td style="text-align:right;">$<?php echo $totalwithtax;?></td>
            	</tr>
            </table>