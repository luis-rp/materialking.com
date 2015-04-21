
<script type="text/javascript">
function loadbillitems(obj)
{
	var billid = $(obj).val();
	
	$("#customerbilldetail").attr('action',"<?php echo base_url('site/customerbill'); ?>");
	$("#customerbilldetail").submit();
}

function showreport(billnum,i)
{	
	if($('a','td#billdetailid_'+i).text() == "Expand"){
		$('a','td#billdetailid_'+i).text('Collapse');
		$(jq("reportdiv"+billnum)).css('display','');
		$("#billitemdetailsrow").css('display','');
	}else{
		$('a','td#billdetailid_'+i).text('Expand');
		$(jq("reportdiv"+billnum)).css('display','none');
		$("#billitemdetailsrow").css('display','none');
	}
}

function paycc(ptype,idnumber)
{
	if(confirm("Do You really want to Change The Payment Type?")){
		if(ptype != 'Credit Card')
		{
			return true;
		}		
		
		$('#paymenttype_' + idnumber + " option:first-child").attr("selected", true);
		var invoicenumber = $('#billnumber').val();
		$("#ccpayinvoicenumber").val(invoicenumber);		
		$("#ccpayamountshow").html($("#ccpayinvoiceamount").val());		
		$("#ccpayref").val($("#refnum_"+idnumber).val());
		$("#paymodal").modal();
	}else {
	var hidepaytype = $('#hiddenpaytype' + idnumber).val();
	$('#paymenttype_' + idnumber +' option[value="' + hidepaytype + '"]').prop('selected', true);
	}
}


    
function jq( myid ) { 
    return "." + myid.replace( /(:|\.|\[|\])/g, "\\$1" ); 
}
</script>
<style>
.headtd {color:white;text-align:center;}
.othertd {text-align:center;}
</style>
<div id="content">
    <div class="container">
        <div id="main">
            <div class="row">
                <div class="span12">
                	<h3 class="titlebox" style="padding:0px 0px 0px 8px;text-align:center;"><strong>Customer Bill Details</strong></h3>
                    	<div class="properties-rows">
                        	<div class="row">
						       <div class="span12">
						          
								<form name="customerbilldetail" id="customerbilldetail" action="" method="POST" >
							    <div style="text-align:center;background-color:white;">
								
									<span style="font-weight:bold;">Select Bill : </span>&nbsp;<span>
										 <select id="billid" name="billid" onchange="loadbillitems(this)">
												<option value="">Choose</option>
													<?php if(isset($billdetails) && count($billdetails) > 0) {
															foreach ($billdetails as $key=>$val) {
																if(isset($selectedbill) && $val['billid'] == $selectedbill) {
																$selected = ' selected ';
															} else {
																$selected = ' ';
															} ?>
												<option value="<?php echo $val['billid'];?>"  <?php echo $selected;?> >
												<?php echo $val['billname'];?></option>	
											<?php	} } ?>
											</select></span>
										
                                </div>       
                                      									
       
		<?php if(isset($billItemdetails) && $billItemdetails != '') { ?>
		 <div style="background-color:white;">
			<table class="table table-bordered" id="">
				<tr style="background-color:black;"> 
					<th style="color:white;text-align:center;">Bill #Name</th>
					<th style="color:white;text-align:center;">Name</th>
					<th style="color:white;text-align:center;">Email</th>
					<th style="color:white;text-align:center;">Address</th>
					<th style="color:white;text-align:center;">Due Date</th>
					<th style="color:white;text-align:center;">Mark up total %</th>
					<th style="color:white;text-align:center;">Payable To</th>
				</tr>
				<tr>
					<td style="text-align:center;"> <?php if(isset($billinfo[0]['billname']) && $billinfo[0]['billname'] != '') { echo $billinfo[0]['billname']; } else { echo ''; } ?> </td>
					<td style="text-align:center;"> <?php if(isset($billinfo[0]['name']) && $billinfo[0]['name'] != '') { echo $billinfo[0]['name']; } else { echo ''; } ?> </td>
					<td style="text-align:center;"> <?php if(isset($billinfo[0]['email']) && $billinfo[0]['email'] != '') { echo $billinfo[0]['email']; } else { echo ''; } ?> </td>
					<td style="text-align:center;"> <?php if(isset($billinfo[0]['address']) && $billinfo[0]['address'] != '') { echo $billinfo[0]['address']; } else { echo ''; } ?> </td>
					<td style="text-align:center;"> <?php if(isset($billinfo[0]['customerduedate']) && $billinfo[0]['customerduedate'] != '') { echo date('m/d/Y', strtotime($billinfo[0]['customerduedate'])); } else { echo ''; } ?> </td>
					<td style="text-align:center;"> <?php if(isset($billinfo[0]['markuptotalpercent']) && $billinfo[0]['markuptotalpercent'] != '') { echo $billinfo[0]['markuptotalpercent']; } else { echo ''; } ?> </td>
					<td style="text-align:center;"> <?php if(isset($billinfo[0]['customerpayableto']) && $billinfo[0]['customerpayableto'] != '') { echo $billinfo[0]['customerpayableto']; } else { echo ''; } ?> </td>				
				</tr>				
			</table>		
			</div>
			
			
	<div style="background-color:white;">
			<table id="" border="1" width="100%"  class="table table-bordered">  
				<tr style="background-color:black;">
					<th style="color:white;text-align:center;">Bill#  </th>					
        			<th style="color:white;text-align:center;">Sent On</th>
        			<th style="color:white;text-align:center;">Due Date</th>
        			<th style="color:white;text-align:center;">Total Price</th>
				</tr>	
			
	<?php	
			$totalprice = 0;	
			$markuptotalpercent = 0;
			$subtotal = 0;
			$subtotal1 = 0;
			$finaltotal = 0;
			$finaltotal1 = 0;
		
			$serviceItemTax1 = 0;
			$finaltot1 = 0 ;
			$totPrice1 = 0 ;
			if(isset($billservicedetails) && @$billservicedetails != '')
			{
				foreach ($billservicedetails as $key1=>$v)
				{ 
					($v['quantity'] == '' || $v['quantity'] == 0) ? $qty = 1 : $qty =  $v['quantity'];           
                	$totPrice1 = $v['price'] * $qty;      	                		
                	$serviceItemTax1 += $totPrice1 + ($totPrice1 * ($v['tax']/100));	
				}
			} 
		
				
			foreach ($billItemdetails as $k=>$value)
			{  
				$markuptotalpercent = (isset($value['markuptotalpercent']) && $value['markuptotalpercent'] != '') ? $value['markuptotalpercent'] : 0;
				$totalprice += $value['quantity'] * $value['ea']; 
				$subtotal1 = $totalprice + ($totalprice * $markuptotalpercent/100);
		     	$finaltotal1 = $subtotal1 + (@$subtotal1*@$settings->taxrate/100);
					 
				$bankaccnt = "";
				$bankrounting = "";
				$bankname ="";
				$amountpaid = ($value['amountpaid']!="")?$value['amountpaid']:0;
				$amountref = $value['refnum'];
				$paystatus = $value['status'];
				//echo "<pre>",print_r($value); echo "@@".$selectedbill; die;
				if((isset($selectedbill)) && ($value['billid'] == $selectedbill) && ($value['bankname']!="") && ($value['accountnumber']!="") && ($value['routingnumber']!=""))
				{
					$bankname = $value['bankname'];
					$bankaccnt = $value['accountnumber'];
					$bankrounting = $value['routingnumber'];							
				}

		  } 		 
		     $subtotal = $totalprice + ($totalprice * $markuptotalpercent/100);
		     $finaltotal = $subtotal + (@$totalprice*@$settings->taxrate/100);
		     $finaltot1 = $serviceItemTax1 + $finaltotal; 
    	?>
    	<tr> 
					<td style="text-align:center;" id="billdetailid_<?php echo $billinfo[0]['billid'];?>"><?php if(isset($billinfo[0]['billname']) && $billinfo[0]['billname'] != '') { echo $billinfo[0]['billname']; } else { echo ''; } ?> 
						<a href="javascript:void(0)" onclick="showreport('<?php echo $billinfo[0]['billid'];?>','<?php echo $billinfo[0]['billid'];?>');">Expand</a>			
						<input type="hidden" name="billnumber" id="billnumber" value="<?php if(isset($billinfo[0]['billid']) && $billinfo[0]['billid'] != '') { echo $billinfo[0]['billid']; } else { echo ''; } ?>"/>
					</td>					
					<td style="text-align:center;"><?php echo date('m/d/Y', strtotime($billinfo[0]['billedon']));?> </td>
					<td style="text-align:center;"><?php echo date('m/d/Y', strtotime($billinfo[0]['customerduedate']));?> </td>
					<td style="text-align:center;">$<?php echo number_format($finaltot1,2);?> </td>
			    </tr>
	    	<tr>
    			<td colspan="4">
    					<?php if($paystatus!= "Verified"){?>
						<select name="paymenttype" id="paymenttype_<?php echo $value['billid'];?>" required onchange="paycc(this.value,<?php echo $value['billid'];?>,'<?php echo $value['totalprice']?>');">
            				<option value="">Select Payment Type</option>
            				<?php if($bankaccnt!="" &&  $bankrounting!="" && $bankname!=""){?>
            				<option <?php echo $value['paymenttype']=='Credit Card'?'SELECTED':'';?> value="Credit Card">Credit Card</option>
            				<?php }?>
            				<option <?php echo $value['paymenttype']=='Cash'?'SELECTED':'';?> value="Cash">Cash</option>
            				<option <?php echo $value['paymenttype']=='Check'?'SELECTED':'';?> value="Check">Check</option>
            			</select>
            			<input type="text" value="<?php if(isset($amountref) && @$amountref != '') echo $amountref; else echo '';?>" name="refnum" id="refnum_<?php echo $value['billid'];?>" style="width:80px;">
            			 Amount:<input type="text" value="<?php if(isset($amountpaid) && @$amountpaid!= '') echo $amountpaid; else echo '';?>" name="amountpaid" id="amountpaid_<?php echo $value['billid'];?>" style="width:80px;">
            			
                    	<input type="submit" name="btnSave" id="btnSave" value="Save">
                    	
                    	<?php }else{//verified payment, show notes?>
                    	<?php echo $value['paymentstatus']; ?>	/ <?php echo $value['paymenttype'];?> / <?php echo $amountref;?>
                    		<?php } ?>
                    </td>
	    	</tr>
	    	
	  <tr id="billitemdetailsrow" style="display:none;"> 
	  <td colspan="4">
	
	<table class="table table-bordered reportdiv<?php echo $value['billid']; ?> dclose" style="display:none;">
		<tr style="background-color:black;">
			<th width="10%" style="color:white;text-align:center;">Item Image  </th>
			<th width="20%" style="color:white;text-align:center;">Itemcode  </th>
			<th width="25%" style="color:white;text-align:center;">Itemname</th>
			<th width="10%" style="color:white;text-align:center;">Date Requested</th>
			<th width="10%" style="color:white;text-align:center;">Cost Code</th>
			<th width="5%" style="color:white;text-align:center;">Qty</th>
			<th width="5%" style="color:white;text-align:center;">Unit</th>
			<th width="5%" style="color:white;text-align:center;">Price</th>
			<th width="10%" style="color:white;text-align:center;">Total Price</th>
			
		</tr>	
	<?php	
			$totalprice = 0;	
			
			foreach ($billItemdetails as $k=>$value)
			{
				if ($value['item_img'] && file_exists('./uploads/item/' . $value['item_img'])) 
				 { 
				 	 $imgName = site_url('uploads/item/'.$value['item_img']); 
				 } 
				 else 
				 { 
				 	 $imgName = site_url('uploads/item/big.png'); 
                 }
				?>	
				 <tr> 
					<td style="text-align:center;"><img src="<?php echo $imgName;?>" width="80" height="80"></td>
					<td style="text-align:center;"><?php echo $value['itemcode'];?> </td>
					<td style="text-align:center;"><?php echo $value['itemname'];?> </td>
					<td style="text-align:center;"><?php echo date('m/d/Y', strtotime($value['daterequested']));?> </td>
					<td style="text-align:center;"><?php echo $value['costcode'];?> </td>
					<td style="text-align:center;"><?php echo $value['quantity'];?> </td>
					<td style="text-align:center;"><?php echo $value['unit'];?> </td>
					<td style="text-align:center;">$<?php echo $value['ea'];?> </td>
					<td style="text-align:center;">$<?php echo number_format($value['quantity'] * $value['ea'],2);?> </td>					
				</tr>
				</tr>
	<?php   } ?>
	 			
	</table>
	
	</td>
	</tr>
	<tr>
	    <td colspan="4">
	    <div style="padding-left:70%;">
	    <table class="table table-striped">
	      <tr>
		    <th style="text-align:right;">Markup Total (<?php echo $markuptotalpercent.'%'?>) : </th>
		    <td style="text-align:left;">$<?php echo number_format(($totalprice * $markuptotalpercent/100),2); ?></td>	
		  </tr>
		  <tr>
		    <th style="text-align:right;">Subtotal : </th>
		    <td style="text-align:left;">$<?php echo number_format($subtotal,2); ?></td>
		    		
	       </tr>
	       <tr>   	
		    <th style="text-align:right;">Tax : </th>		
		    <td style="text-align:left;">$<?php echo number_format(($totalprice*$settings->taxrate/100),2); ?></td>
	       </tr>
	       <?php 
			$serviceItemTax = 0;
			$finaltot = 0 ;
			if(isset($billservicedetails) && @$billservicedetails != '')
			{
				foreach ($billservicedetails as $key1=>$v)
				{ 
					($v['quantity'] == '' || $v['quantity'] == 0) ? $qty = 1 : $qty =  $v['quantity'];           
                	$totPrice = $v['price'] * $qty;      	                		
                	
					 ?>
					
					 <tr>   	
				    	<th style="text-align:right;"><?php echo $v['servicelaboritems'];?> : </th>
				    	<td style="text-align:left;">$<?php echo number_format($v['price'],2);?></td>
				    	
				    </tr>
				    <tr> 
				   		 <th style="text-align:right;">Qty : </th>  					    	
				    	<td style="text-align:left;"><?php echo $qty;?></td>
				    	
				    </tr>
				    <tr>   	
				    	<th style="text-align:right;">Tax &nbsp; (<?php echo number_format($v['tax'],2);?> % ) : </th> 
				    	<td style="text-align:left;">$<?php echo $totPrice * ($v['tax']/100); ?></td>
				    </tr>
<?php 			$serviceItemTax += $totPrice + ($totPrice * ($v['tax']/100));	}
			} $finaltot = $serviceItemTax + $finaltotal; 

			  $amouttopay = $finaltot - $amountpaid; 	
			?>	
			 <tr>   	
		    	<th style="text-align:right;">Total : </th>
		    	<td style="text-align:left;">$<?php echo number_format($finaltot,2); ?></td>
	         </tr>       
	      </table> 
	      </div> 	
		 </td>
	</tr>       
	</table>
	<?php	} ?>
	</div>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>
</div>


<div id="paymodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
    <div class="modal-header">
    	<h3>
    	Pay by credit card
		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
		</h3>
	</div>
	<div class="modal-body" id="quoteitems">
        <form method="post" action="<?php echo site_url('site/paybillbycc/');?>" onsubmit="return validatecc();">
	        <input type="hidden" id="ccpayinvoicenumber" name="invoicenum"/>
	        <input type="hidden" id="ccpayinvoiceamount" name="amount" value="<?php echo round($amouttopay,2);?>"/>
	         <input type="hidden" id="ccpayref" name="ccpayref"/>	        
            <div class="control-group">
                <label class="control-label" for="card">
                   Total Amount to pay
                </label>
                <div class="controls">
                   $<span id="ccpayamountshow"></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="card">
                   Credit Card Number
                    <span class="form-required" title="This field is required.">*</span>
                </label>
                <div class="controls">
                    <input type="text" id="card" name="card" required style="width: 250px;">
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
            <div class="form-actions">
                <input type="submit" class="btn btn-primary arrow-right" value="Process">
            </div>
        </form>
	</div>

</div>

