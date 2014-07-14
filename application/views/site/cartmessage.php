<script>
$(document).ready(function() {

});
</script>

<div id="content">
<div class="container">
    <div id="main">
        <div class="row">
            <div class="span9">
                <h1 class="page-header"><?php echo $message;?></h1>
                <?php 
                if($this->session->userdata('site_loggedin')){?>
                <a href="<?php echo base_url().'admin/order/details/'.$order;?>">Order Management</a>
                <?php }
                ?>
                
           <div class="property-detail">
                
            <br/>
            <?php if($cart && $status=='Success'){?>
            <h4>Order Items:</h4>
            
              <table class="table table-bordered">
            	<tr>
            		<th>Item</th>
            		<th>Supplier Company</th>
            		<th>Price</th>
            		<th>Quantity</th>
            		<th>Total</th>
            	</tr>
            	<?php $gtotal=0;$totalRates=0; foreach ($cart as $item){$total = $item['quantity']*$item['price'];$gtotal+=$total;
            	if(is_object($item['rate'])){
            	
            		$totalRates +=$item['rate']->rate;
            	}
            	?>
            	<tr>
            		<td><?php echo $item['itemdetails']->itemname;?></td>
            		<td><?php echo $item['companydetails']->title;?></td>
            		<td>$<?php echo $item['price'];?></td>
            		<td><?php echo $item['quantity']?></td>
            		<td>$<?php echo number_format($total,2);?></td>
            	</tr>
            	<?php }?>
            	<?php 
            	    $tax = $gtotal*$settings->taxpercent/100;
            	    
            	    $totalwithtax = number_format($tax+$gtotal+$totalRates,2);
            	?>
            	<tr>
            		<td colspan="4" align="right">SubTotal</td>
            		<td>$<?php echo number_format($gtotal,2);?></td>
            	</tr>
            	
            	<tr>
            		<td colspan="4" align="right">Tax</td>
            		<td>$<?php echo number_format($tax,2);?></td>
            	</tr>
            	<tr>
            		<td colspan="4" align="right">Shipment rate</td>
            		<td>$<?php echo $totalRates;?></td>
            	</tr>
            	<tr>
            		<td colspan="4" align="right">Total</td>
            		<td>$<?php echo $totalwithtax;?></td>
            	</tr>
            </table>
            
            <?php }?>
            
                </div>
            </div>
            
            <div class="sidebar span3">
                <div class="widget contact">
    <div class="title">
        <h2 class="block-title">Contact Supplier</h2>
    </div>

    <div class="content">
        <form method="post">
            <div class="control-group">
                <label class="control-label" for="inputName">
                    Name
                    <span class="form-required" title="This field is required.">*</span>
                </label>
                <div class="controls">
                    <input type="text" id="inputName">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="inputEmail">
                    Email
                    <span class="form-required" title="This field is required.">*</span>
                </label>
                <div class="controls">
                    <input type="text" id="inputEmail">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="inputMessage">
                    Message
                    <span class="form-required" title="This field is required.">*</span>
                </label>

                <div class="controls">
                    <textarea id="inputMessage"></textarea>
                </div>
            </div>

            <div class="form-actions">
                <input type="submit" class="btn btn-primary arrow-right" value="Send">
            </div>
        </form>
    </div>
</div>
            </div>
            
            
        </div>
    </div>
</div>
    </div>