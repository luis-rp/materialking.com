
<section class="row-fluid">
	<h3 class="box-header"><?php echo @$heading; ?> <?php if($this->session->userdata('usertype_id')<3){?>
		   <a class="btn btn-green" href="<?php echo site_url('admin/quote/index/'.$quote->pid);?>">&lt;&lt; Back</a>
		   <?php }else{?>
		   <a class="btn btn-green" href="<?php echo site_url('admin/purchaseuser/quotes');?>">&lt;&lt; Back</a>
		   <?php }?></h3>
	<div class="box">
		<div class="span12">
		   
		   <br/>
		   <?php echo $this->session->flashdata('message'); ?>
		   <br/>
		   <?php echo @$message; ?>
		      <div class="control-group">
			    <div class="controls">
				    <div class="poheading">PO #:<?php echo $quote->ponum; ?> &nbsp; &nbsp; Date:<?php echo $quote->podate;?>&nbsp;</div>
			       <br/>
			    </div>
		      </div>
			    
			  <div class="control-group">
				    <table class="table table-bordered">
				    	<tr>
				    		<th width="15%">Item Code</th>
				    		<th width="8%">Item Image</th>
				    		<th width="10%">Company</th>
				    		<th width="15%">Item Name</th>
				    		<th width="5%">Qty.</th>
				    		<th width="5%">Unit</th>
				    		<th width="5%">Price EA</th>
				    		<th width="5%">Total Price</th>
				    		<th width="10%">Date Requested</th>
				    		<th width="10%">Cost Code</th>
				    		<th width="10%">Notes</th>
				    	</tr>
				    	<?php $alltotal=0; 
				    	
				    	foreach($quoteitems as $q)
				    	{?><?php $alltotal+=$q->totalprice;
				    	
				    		if ($q->item_img && file_exists('./uploads/item/' . $q->item_img)) 
							 { 
							 	 $imgName = site_url('uploads/item/'.$q->item_img); 
							 } 
							 else 
							 { 
							 	 $imgName = site_url('uploads/item/big.png'); 
	                         }
				    	?>
				    	<tr>
				    		<td><?php echo $q->itemcode;?></td>
				    		<td><img style="max-height: 120px; padding: 0px;width:80px; height:80px;float:left;" src='<?php echo $imgName;?>'></td>
				    		<td><?php echo $q->title;?></td>
				    		<td><?php echo $q->itemname;?></td>
				    		<td><?php echo $q->quantity;?></td>
				    		<td><?php echo $q->unit;?></td>
				    		<td><?php if($q->ea==0) { echo "RFQ";} else { echo "$".$q->ea;}?></td>
				    		<td><?php echo "$".$q->totalprice;?></td>
				    		<td><?php echo $q->daterequested;?></td>
				    		<td><?php echo $q->costcode;?></td>
				    		<td><?php echo $q->notes;?></td>
				    	</tr>
				    	<?php }?>
				    	<?php 
							$taxtotal = $alltotal * $config['taxpercent'] / 100;
							$grandtotal = $alltotal + $taxtotal;
				    	?>
				    	<tr>
				    		<td colspan="5" style="text-align:right">Subtotal: </td>
				    		<td colspan="4">$ <?php echo $alltotal;?></td>
				    	</tr>
				    	<tr>
				    		<td colspan="5" style="text-align:right">Tax: </td>
				    		<td colspan="4">$ <?php echo $taxtotal;?></td>
				    	</tr>
				    	<tr>
				    		<td colspan="5" style="text-align:right">Total: </td>
				    		<td colspan="4">$ <?php echo $grandtotal;?></td>
				    	</tr>
				    </table>
			    </div>
			 
			    <br/>
			    <br/>
	    
	    </div>
    </div>
</section>