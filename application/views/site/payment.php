<script src="<?php echo base_url(); ?>templates/site/assets/js/creditcard.js"></script>
<?php //print_r($_SESSION['newregister']);?>
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
<script>
$(document).ready(function() {

});
</script>

<div id="content">
<div class="container">
    <div id="main">
        <div class="row">
            <div class="span9">
           
             <?php echo $this->session->flashdata('message'); ?>
                <h1 class="page-header">Pay With Credit Card</h1>
                
           <div class="property-detail">
                
            <br/>
            <?php if($cart){?>
            <h4>Order Items:</h4>
            
              <table class="table table-bordered">
            	<tr>
            		<th>Item</th>
            		<th>Supplier Company</th>
            		<th>Price</th>
            		<th>Quantity</th>
            		<th>Total</th>
            	</tr>
            	<?php $gtotal=0;$totalRates = 0; foreach ($cart as $item){$total = $item['quantity']*$item['price'] ;$gtotal+=$total;
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
            	    $tax = number_format($tax,2);
            	    
            	    $gtotal = number_format($gtotal,2);
            	    
            	    $totalwithtax = number_format($tax+$gtotal+$totalRates,2);
            	?>
            	<tr>
            		<td colspan="4" align="right">SubTotal</td>
            		<td>$<?php echo $gtotal;?></td>
            	</tr>
            	
            	<tr>
            		<td colspan="4" align="right">Tax</td>
            		<td>$<?php echo $tax;?></td>
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
        <h2 class="block-title">Credit Card Info</h2>
    </div>

    <div class="content">
        <form method="post" action="<?php echo site_url('cart/ccpost');?>" onsubmit="return validatecc();">
        	<?php if(!$this->session->userdata('site_loggedin')){?>
            <div class="control-group">
                <label class="control-label" for="card">
                   Email
                    <span class="form-required" title="This field is required.">*</span>
                </label>
                <div class="controls">
                    <input type="text" id="email" name="email" required email>
                </div>
            </div>
        	<?php }else{?>
        	<input type="hidden" name="email" value="<?php echo $this->session->userdata('site_loggedin')->email;?>"/>
        	<?php }?>
            <div class="control-group">
                <label class="control-label" for="card">
                   Credit Card Number
                    <span class="form-required" title="This field is required.">*</span>
                </label>
                <div class="controls">
                    <input type="text" id="card" name="card" required>
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
			<div class=shippingInfo">
				<input type="hidden" name="name" value="<?php echo $name;?>">
				<input type="hidden" name="street" value="<?php echo $street;?>">
				<input type="hidden" name="city" value="<?php echo $city;?>">
				<input type="hidden" name="state" value="<?php echo $state;?>">
				<input type="hidden" name="zip" value="<?php echo $zip;?>">
				<input type="hidden" name="country" value="<?php echo $country;?>">
			</div>
            <div class="form-actions">
                <input type="submit" class="btn btn-primary arrow-right" value="Process">
            </div>
        </form>
    </div>
</div>
            </div>
            
            
        </div>
    </div>
</div>
    </div>