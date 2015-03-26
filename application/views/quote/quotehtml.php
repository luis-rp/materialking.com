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
			<table border="0" style="text-align:center;" cellpadding="8" cellspacing="2">
        		<tr>
            		<th>Item Image</th>
            		<th>Item Code</th>
            		<th>Item Name</th>
            		<th>Qty</th>
            		<th>Price</th>
            		<th>Unit</th>
            		<th>Notes</th>
            		<th>Ext. Price</th>
        		</tr>
        		<?php
        		$total = 0;
        		foreach($biditems as $bi)
        		{
        		    $total += $bi->quantity * $bi->ea;
        		    
        		    if ($bi->item_img && file_exists('./uploads/item/' . $bi->item_img)) 
					{ 
					 	 $imgName = site_url('uploads/item/'.$bi->item_img); 
					} 
					else 
					{ 
					 	 $imgName = site_url('uploads/item/big.png'); 
                    }
        		?>
        		<?php $extprice=$bi->quantity * $bi->ea;?>
    		    <tr>
    		    <td><img width="80" height="80" src="<?php echo $imgName;?>"></td>
        		<td><?php echo ($bi->itemcode)?$bi->itemcode:$bi->defaultitemcode; ?></td>
        		<td><?php echo ($bi->itemname)?$bi->itemname:$bi->defaultitemname; ?></td>
        		<td><?php echo $bi->quantity; ?></td>
        		<td><?php echo $bi->ea; ?></td>
        		<td><?php echo $bi->unit; ?></td>
        		<td><?php echo $bi->notes; ?></td>
        		<td><?php echo number_format($extprice,2); ?></td>
        		</tr>
        		<?php
        		}
        		$total = round($total,2);
        		$tax = $total * $taxpercent / 100;
        		$tax = round($tax,2);
        		$alltotal = $total + $tax;
        		?>

    		    <tr>
        		<td colspan="8">&nbsp;</td>
        		</tr>
        	    <tr>

        		<td colspan="3" align="left">Supplier Quote # </td>
        		<td colspan="5"><?php echo $bid->quotenum; ?></td>
                    </tr>
    		    <tr>
				<?php
				   $newdate = '';
				   if($bid->expire_date != '0000-00-00')
				   {
	 					$olddate=strtotime($bid->expire_date); 
						$newdate = date('m/d/Y', $olddate);
				   }
				   else 
				   {
				   		$newdate = '00/00/0000';
				   }
				?>	
        		<td colspan="3" align="left">Supplier Expire Date</td>
        		<td colspan="5"><?php  echo $newdate; ?></td>
                    </tr>

                <tr>

        		<td colspan="3" align="left">Supplier Quote Date</td>
        		<td colspan="5"><?php $olddate1=strtotime($bid->submitdate); $newdate1 = date('m/d/Y', $olddate1); echo $newdate1; ?></td>
                    </tr>

    		    <tr>

        		<td colspan="3" align="left">Quote Total: </td>
        		<td colspan="5">$<?php echo number_format($total,2); ?></td>
        		</tr>

    		    <tr>
        		<td colspan="3" align="left">Tax: </td>
        		<td colspan="5">$<?php echo number_format($tax,2); ?></td>
        		</tr>

    		    <tr>
        		<td colspan="3" align="left">Total: </td>
        		<td colspan="5">$<?php echo number_format($alltotal,2); ?></td>
        		</tr>

			</table>
		</td>
	</tr>

</table>