<script>
/*$(document).ready(function() {

});*/


$(document).ready(function (e) {
		$("#sendsmsform").on('submit',(function(e) {
		e.preventDefault();
		var companyid = $('#companysmsid').val();
		$.ajax({
		url: "<?php echo site_url('cart/sendsmsviaajax'); ?>/"+companyid,
		type: "POST",
		data: new FormData(this),		
		contentType: false,
		cache: false,
		processData:false,
		success: function(data)
		{
			var msgstr ='<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Email was sent successfully</div></div>';
			$('#messagesuccess').html(msgstr);
		},
		error: function()
		{
		}
		});
		}));
		
	});

</script>

<div id="content">
<div id="messagesuccess"></div>
<div class="container">
    <div id="main">
        <div class="row">
            <div class="span9">
                <h1 class="page-header"><?php echo $message;?></h1>
                <?php echo $messagenotification;?>
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
            		<th>Item Code</th>
            		<th>Item Name</th>
            		<th>Supplier Company</th>
            		<th>Price</th>
            		<th>Quantity</th>
            		<th>Total</th>
            	</tr>
            	<?php 
				if(isset($totalordershipping) && $totalordershipping!='')
				$totalRates=$totalordershipping; 
				else
				{
					$totalRates=$totalordershipping=0;
				}
				$gtotal=0; foreach ($cart as $item){$total = $item['quantity']*$item['price'];$gtotal+=$total;
				$companyid = $item['companydetails']->id;  
            	/*if(is_object($item['rate'])){
            	
            		$totalRates +=$item['rate']->rate;
            	}*/
            	?>
            	<tr>
            		<td><?php echo $item['itemdetails']->itemorgcode;?></td>
            		<td><?php echo $item['itemdetails']->itemname;?></td>
            		<td><?php echo $item['companydetails']->title;?></td>
            		<td>$<?php echo $item['price'];?></td>
            		<td><?php echo $item['quantity']?></td>
            		<td>$<?php echo number_format($total,2);?></td>
            	</tr>
            	<?php }?>
            	<?php 
            	    $tax = round($gtotal*$settings->taxpercent/100,2);           	    
            	    // Adding comission, 0.25 transaction fees for each supplier transaction, 0.3 processing fee + shippinglabel 0.5	
					$processingandhandling = ($gtotal*$comissionper/100) + ($companycount*0.25) + 0.8; 	
						
					$totalwithtax = $tax+$gtotal+$totalRates;
					
            	    $finaltotal = $tax+$gtotal+$totalRates + $processingandhandling; 
            	                	    
            	    for($i=0;$i<$companycount;$i++)
						$finaltotal += ($finaltotal*2.9/100);   

					$processingandhandling = ($finaltotal - $totalwithtax);
            	    
            	?>
            	<tr>
            		<td colspan="5" align="right">SubTotal</td>
            		<td>$<?php echo number_format($gtotal,2);?></td>
            	</tr>
            	
            	<tr>
            		<td colspan="5" align="right">Tax</td>
            		<td>$<?php echo number_format($tax,2);?></td>
            	</tr>
            	<tr>
            		<td colspan="5" align="right">Shipment rate</td>
            		<td>$<?php echo number_format($totalordershipping,2);?></td>
            	</tr>
            	<tr>
            		<td colspan="4" align="right">Handling & Processing</td>
            		<td>$<?php echo number_format($processingandhandling,2);?></td>
            	</tr>
            	<tr>
            		<td colspan="5" align="right">Total</td>
            		<td>$<?php echo number_format($finaltotal,2);?></td>
            	</tr>
            </table>
            
            <?php }
			
			?>
            
                </div>
            </div>
            
            <div class="sidebar span3">
                <div class="widget contact">
    <div class="title">
        <h2 class="block-title">Contact Supplier</h2>
    </div>

    <div class="content">
    
        <form id="sendsmsform" name="sendsmsform"  method="post"  action="<?php echo site_url('cart/sendsms/'.$companyid);?>">
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