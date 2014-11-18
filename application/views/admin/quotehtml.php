<table>
	<tr>
		<td>QUOTATION</td>
	</tr>
	<tr>
		<td><?php echo $purchaser->companyname;?></td>
	</tr>
	<?php /* if($company->logo){?>
	<tr>
		<td><img src="<?php echo site_url('uploads/logo/thumbs/'.$company->logo);?>" width="100"/></td>
	</tr>
	<?php } */?>
	<tr>
		<td>Quote#: <?php echo $quote->ponum;?></td>
	</tr>
	<!-- <tr>
		<td>Contact: <?php // echo $company->contact;?></td>
	</tr> -->
	<tr>
		<td>Award Date: <?php echo $quote->podate;?></td>
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
        			<th>Filename</th>     		
            		<th>Description</th>            		
            		<th>Price</th>            		
        		</tr>
        		<?php
        		$total = 0;
        		foreach($biditems as $bi)
        		{
        		    $total += $bi->ea;
        		?>        		
    		    <tr>        		
    		    <td><?php echo $bi->attach; ?></td>
        		<td><?php echo ($bi->itemname)?$bi->itemname:''; ?></td>        		
        		<td><?php echo $bi->ea; ?></td>        		
        		</tr>
        		<?php
        		}
        		$total = round($total,2);
        		$tax = $total * $taxpercent / 100;
        		$tax = round($tax,2);
        		$alltotal = $total + $tax;
        		?>

    		    <tr>
        		<td colspan="7">&nbsp;</td>
        		</tr>
        	    <tr>

        		<td>Contract Quote # </td>
        		<td colspan="7"><?php echo $bid->quotenum; ?></td>
                </tr>  		    

                <tr>

        		<td>Quote Date</td>
        		<td colspan="7"><?php $olddate1=strtotime($bid->submitdate); $newdate1 = date('m/d/Y', $olddate1); echo $newdate1; ?></td>
                    </tr>

    		    <tr>

        		<td>Quote Total: </td>
        		<td colspan="7">$<?php echo number_format($total,2); ?></td>
        		</tr>

    		    <tr>
        		<td>Tax: </td>
        		<td colspan="7">$<?php echo number_format($tax,2); ?></td>
        		</tr>

    		    <tr>
        		<td>Total: </td>
        		<td colspan="7">$<?php echo number_format($alltotal,2); ?></td>
        		</tr>

			</table>
		</td>
	</tr>

</table>