<table>
	<tr>
		<td>QUOTATION</td>
	</tr>
	<tr>
		<td><?php echo $company->title;?></td>
	</tr>
	<?php if($company->logo){?>
	<tr>
		<td><img src="<?php echo site_url('uploads/logo/thumbs/'.$company->logo);?>" width="100"/></td>
	</tr>
	<?php }?>
	<tr>
		<td>Quote#: <?php echo $quote->ponum;?></td>
	</tr>
	<tr>
		<td>Contact: <?php echo $company->contact;?></td>
	</tr>
	<tr>
		<td>Quote Date: <?php echo $quote->podate;?></td>
	</tr>
	<tr>
		<td>Due Date: <?php echo $quote->duedate;?></td>
	</tr>
	
	<tr>
		<td colspan="2"></td>
	</tr>
	
	<tr>
		<td colspan="2">
			<table>
        		<tr>
            		<th>Itemcode</th>
            		<th>Itemname</th>
            		<th>Qty</th>
            		<th>Price</th>
            		<th>Unit</th>
            		<th>Notes</th>
        		</tr>
        		<?php 
        		$total = 0;
        		foreach($quoteitems as $bi)
        		{
        		    $total += $bi->quantity * $bi->ea;
        		?>
    		    <tr>
        		<td><?php echo $bi->itemcode; ?></td>
        		<td><?php echo $bi->itemname; ?></td>
        		<td><?php echo $bi->quantity; ?></td>
        		<td><?php echo $bi->ea; ?></td>
        		<td><?php echo $bi->unit; ?></td>
        		<td><?php echo $bi->notes; ?></td>
        		</tr>
        		<?php 
        		}
        		$total = round($total,2);
        		$tax = $total * $taxpercent / 100;
        		$tax = round($tax,2);
        		$alltotal = $total + $tax;
        		?>
        		
    		    <tr>
        		<td colspan="6">&nbsp;</td>
        		</tr>
        	    
    		    <tr>
                        
        		<td>Quote Total: </td>
        		<td colspan="5"><?php echo $total; ?></td>
        		</tr>
        		
    		    <tr>
        		<td>Tax: </td>
        		<td colspan="5"><?php echo $tax; ?></td>
        		</tr>
        		
    		    <tr>
        		<td>Total: </td>
        		<td colspan="5"><?php echo $alltotal; ?></td>
        		</tr>
        		
			</table>
		</td>
	</tr>
	
</table>