
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
				    		<th width="20%">Item Name</th>
				    		<th>Qty.</th>
				    		<th>Unit</th>
				    		<th>Price EA</th>
				    		<th>Total Price</th>
				    		<th>Date Requested</th>
				    		<th>Cost Code</th>
				    		<th>Notes</th>
				    	</tr>
				    	<?php $alltotal=0; foreach($quoteitems as $q){?><?php $alltotal+=$q->totalprice;?>
				    	<tr>
				    		<td><?php echo $q->itemcode;?></td>
				    		<td><?php echo $q->itemname;?></td>
				    		<td><?php echo $q->quantity;?></td>
				    		<td><?php echo $q->unit;?></td>
				    		<td>$ <?php echo $q->ea;?></td>
				    		<td>$ <?php echo $q->totalprice;?></td>
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