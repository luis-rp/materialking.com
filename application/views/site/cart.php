<?php echo '<script>var removefromcarturl="'.site_url('cart/removecartitem').'";</script>'?>
<?php echo '<script>var updatecarturl="'.site_url('cart/updatecartitem').'";</script>'?>

<script>


function removefromcart(itemid, companyid)
{
	var data = "itemid="+itemid+"&company="+companyid;
    
    $.ajax({
	      type:"post",
	      data: data,
	      url: removefromcarturl,
	    }).done(function(data){
			alert(data);
			window.location=window.location;
	    });
}

function updatecart(itemid,companyid,quantity,incrementval)
{
	var data = "itemid="+itemid+"&company="+companyid+"&quantity="+quantity;
    //alert(data);return;
      
	if(incrementval>0){
		if((quantity%incrementval)!=0){
			alert('Sorry this item is only available in increments of '+incrementval);
			return false;
		}
	}
    
    $.ajax({
	      type:"post",
	      data: data,
	      url: updatecarturl,
	    }).done(function(data){
			alert(data);
			window.location=window.location;
	    });
}


	$(document).ready(function (e) {
		$("#sendsmsform").on('submit',(function(e) {
		e.preventDefault();
		var companyid = $('#companysmsid').val();
		$.ajax({
		url: site_url('cart/sendsmsviaajax/'+companyid),
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
                <h1 class="page-header">Your Cart</h1>
                <div class="carousel property">
                	<a class="btn btn-primary" href="<?php echo site_url('site/items')?>">&lt; Keep shopping</a>
                </div>
               
                <div class="property-detail">
                
            <br/>
             <table class="table table-bordered">
            	<tr>
            		<th>Item Name</th>
            		<th>Company</th>
            		<th>Price</th>
            		<th>Quantity</th>
            		<th>Total</th>
            		<th>Action</th>
            	</tr>
            	<?php 
            	    $gtotal=0; foreach ($cart as $item){$total = $item['quantity']*$item['price'];$gtotal+=$total;
            	    $companyid = $item['companydetails']->id;  
            	?>
            	<tr>
            		<td><?php echo $item['itemdetails']->itemname;?></td>
            		<td><?php echo $item['companydetails']->title;?></td>
            		<td>$<?php echo $item['price'];?></td>
            		<td><input class="span1" type="text" value="<?php echo $item['quantity']?>" onchange="updatecart(<?php echo $item['itemid'];?>,<?php echo $item['company'];?>,this.value, <?php echo $item['increment'];?>)"/></td>
            		<td>$<?php echo number_format($total,2);?></td>
            		<td align="center">
	            		<a class="btn btn-primary" href="javascript:void(0)" onclick="removefromcart(<?php echo $item['itemid'];?>, <?php echo $item['company'];?>)">
	            			<i class="icon icon-minus"></i>
	            		</a>
            		</td>
            	</tr>
            	<?php }?>
            	<?php 
            	    $tax = $gtotal*$settings->taxpercent/100;
            	    
            	    $totalwithtax = number_format($tax+$gtotal,2);
            	?>
            	<tr>
            		<td colspan="4" align="right">Total</td>
            		<td colspan="2">$<?php echo number_format($gtotal,2);?></td>
            	</tr>
            	
            	<tr>
            		<td colspan="4" align="right">Tax</td>
            		<td colspan="2">$<?php echo number_format($tax,2);?></td>
            	</tr>
            	
            	<tr>
            		<td colspan="4" align="right">Total</td>
            		<td colspan="2">$<?php echo $totalwithtax;?></td>
            	</tr>
            </table>	
     		<?php
			$adderror=$this->session->userdata('cart_address_error');
			echo $adderror;
			
			$cadderr['cart_address_error'] = null;
			$this->session->set_userdata($cadderr);
			?>
            <form name="formCart" action="" id="formCart"  method="post">
       
            	 <table class="table table-bordered">
            	 	<tr><th colspan="2">Shipping Information</th></tr>
            	 	<tr><td>
            	 		<div class="control-group">
			                <label class="control-label" for="shippingName">
			                    Name
			                    <span class="form-required" title="This field is required.">*</span>
			                </label>
			                <div class="controls">
			                    <input type="text" id="shippingName" name="shippingName">
			                </div>
			        	  </div>
            	 		</td><td>
            	 			 <div class="control-group">
			                <label class="control-label" for="shippingStreet">
			                    Street
			                    <span class="form-required" title="This field is required.">*</span>
			                </label>
			                <div class="controls">
			                    <input type="text" id="shippingStreet" name="shippingStreet">
			                </div>
			        	  </div>
            	 		</td></tr>
            	 		
            	 		<tr><td>
            	 			<div class="control-group">
			                <label class="control-label" for="shippingCity">
			                    City
			                    <span class="form-required" title="This field is required.">*</span>
			                </label>
			                <div class="controls">
			                    <input type="text" id="shippingCity" name="shippingCity">
			                </div>
			        	  		</div>
            	 		</td><td>
            	 				  <div class="control-group">
			                <label class="control-label" for="shippingState">
			                    State
			                    <span class="form-required" title="This field is required.">*</span>
			                </label>
			                <div class="controls">
			                    <input type="text" id="shippingState" name="shippingState">
			                </div>
			        	  </div>
            	 		</td></tr>
            	 		<tr><td>
            	 			  <div class="control-group">
			                <label class="control-label" for="shippingZip">
			                    Zip
			                    <span class="form-required" title="This field is required.">*</span>
			                </label>
			                <div class="controls">
			                    <input type="text" id="shippingZip" name="shippingZip">
			                </div>
			        	  </div>
            	 		</td><td>
            	 			  <div class="control-group">
				                <label class="control-label" for="shippingCountry">
				                    Country
				                    <span class="form-required" title="This field is required.">*</span>
				                </label>
				                <div class="controls">
				                    <input type="text" id="shippingCountry" name="shippingCountry">
				                </div>
				        	  </div>
            	 		</td></tr>
            	 </table>
          
            	 <?php if($this->session->userdata('site_loggedin')) {?>
            	 <input type="button" id="submitProcess" class="btn btn-primary arrow-right" value="USE P.O.(PURCHASE ORDER) ">
                 <?php }?>
            	
            	 <input type="button" id="submitCC" class="btn btn-primary arrow-right" value="Credit Card Payment">
            	 
            	 <p>*Your Shipping Options will be shown on the next page.</p>
            	 <script type="text/javascript">
            	 $(document).ready(function() {
            		
						$("#submitProcess").click(function(){
								$("#formCart").attr("action","<?php echo site_url('cart/manualpayment')?>");
								$("#formCart").submit();
							});
						$("#submitCC").click(function(){
							$("#formCart").attr("action","<?php echo site_url('cart/ccpayment')?>");
							$("#formCart").submit();
							});
            	 });
				</script>
            	 <!-- 
            			<?php if($canmanualorder){//if($this->session->userdata('site_loggedin')){?>
            			<a class="btn btn-primary arrow-right" href="<?php echo site_url('cart/manualpayment')?>">Process Order</a>
            			&nbsp;&nbsp;
            			<?php }?>
            			<a class="btn btn-primary arrow-right" href="<?php echo site_url('cart/ccpayment')?>">Credit Card Payment</a>-->
            		
            	</form>
            
                </div>
                
                
            </div>
            
            <div class="sidebar span3">
                <div class="widget contact">
    <div class="title">
        <h2 class="block-title">Contact Supplier</h2>
    </div>

    <div class="content">
        <form id="sendsmsform" name="sendsmsform" method="post" action="<?php echo site_url('cart/sendsms/'.$companyid);?>" >
            <div class="control-group">
                <label class="control-label" for="inputName">
                    Name
                    <span class="form-required" title="This field is required.">*</span>
                </label>
                <div class="controls">
                    <input type="text" id="inputName" name="inputName" required>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="inputEmail">
                    Email
                    <span class="form-required" title="This field is required.">*</span>
                </label>
                <div class="controls">
                    <input type="text" id="inputEmail" name="inputEmail" required>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="inputMessage">
                    Message
                    <span class="form-required" title="This field is required.">*</span>
                </label>

                <div class="controls">
                    <textarea id="inputMessage" name="inputMessage" required></textarea>
                </div>
            </div>
			<input type="hidden" name="companysmsid" id="companysmsid" value="<?php echo $companyid;?>"/>
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