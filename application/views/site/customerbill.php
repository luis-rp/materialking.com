<html>
<head>
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

/*function update_invoice_payment_status(idnumber)
{	
   // var invoice_payment_status_value = $('#invoice_payment_' + idnumber + " option:selected").val();
    var payment_type = $('#paymenttype_' + idnumber + " option:selected").val();
    var amount_value = $('#amountpaid_' + idnumber).val();    
    var refnum_value = $('#refnum_' + idnumber + "").val();
  
    var url = "<?php echo base_url("customerbill/update_customer_bill_payment_status");?>";
    
    $.ajax({
        type: "POST",
        url: url,
        data: {billid:idnumber, paymenttype: payment_type, refnum: refnum_value, amount: amount_value}
    }).done(function(data) {
    	//$('#paymentstatus' + idnumber).html('Paid');
        //$('#message_div').html(data);
    });
}*/
    
function jq( myid ) { 
    return "." + myid.replace( /(:|\.|\[|\])/g, "\\$1" ); 
}
</script>
</head>
<body>

<div class="control-group">
<form name="customerbilldetail" id="customerbilldetail" action="" method="POST" >
<table class="" align="center" width="40%">
	<tr><td colspan="2" align="center"><h3>Customer Bill Details </h3></td></tr>
	<tbody>
		<tr>
			<td> Select Bill </td>
			<td> 
				<select id="billid" name="billid" onchange="loadbillitems(this)">
					<option value="">Choose</option>
					<?php 
					
					if(isset($billdetails) && count($billdetails) > 0)
					{
						foreach ($billdetails as $key=>$val)
						{
							if(isset($selectedbill) && $val['billid'] == $selectedbill)
							{
								$selected = ' selected ';
							}
							else 
							{
								$selected = ' ';
							}
							?>
							<option value="<?php echo $val['billid'];?>"  <?php echo $selected;?> ><?php echo $val['billname'];?></option>	
			<?php			}
					} ?>
				</select>
			</td>
		</tr>
	</tbody>
</table>

<?php //echo '@@'.$selectedbill.'===='.$billid;
		if(isset($billItemdetails) && $billItemdetails != '')
		{ ?>
			<table id="" border="1" width="40%" align="center">
				<tr> 
					<td>Bill #Name:</td><td> <?php if(isset($billinfo[0]['billname']) && $billinfo[0]['billname'] != '') { echo $billinfo[0]['billname']; } else { echo ''; } ?> </td>
				</tr>
				<tr>
					<td>Name: </td><td> <?php if(isset($billinfo[0]['name']) && $billinfo[0]['name'] != '') { echo $billinfo[0]['name']; } else { echo ''; } ?> </td>
				</tr>	
				<tr>
					<td>Email:</td><td> <?php if(isset($billinfo[0]['email']) && $billinfo[0]['email'] != '') { echo $billinfo[0]['email']; } else { echo ''; } ?> </td>
				</tr>
				<tr>
					<td>Address: </td><td> <?php if(isset($billinfo[0]['address']) && $billinfo[0]['address'] != '') { echo $billinfo[0]['address']; } else { echo ''; } ?> </td>
				</tr>	
				<tr>
					<td>Due Date: </td><td> <?php if(isset($billinfo[0]['customerduedate']) && $billinfo[0]['customerduedate'] != '') { echo date('m/d/Y', strtotime($billinfo[0]['customerduedate'])); } else { echo ''; } ?> </td>
				</tr>
				<!--<tr>
					<td>Payment Type:</td><td> <?php if(isset($billinfo[0]['customerpaymenttype']) && $billinfo[0]['customerpaymenttype'] != '') { echo $billinfo[0]['customerpaymenttype']; } else { echo ''; } ?> </td>
				</tr>-->
				<tr>
					<td>Mark up total %:</td><td> <?php if(isset($billinfo[0]['markuptotalpercent']) && $billinfo[0]['markuptotalpercent'] != '') { echo $billinfo[0]['markuptotalpercent']; } else { echo ''; } ?> </td>
				</tr>
				<tr>
					<td>Payable To:</td><td> <?php if(isset($billinfo[0]['customerpayableto']) && $billinfo[0]['customerpayableto'] != '') { echo $billinfo[0]['customerpayableto']; } else { echo ''; } ?> </td>
				</tr>	
			</table>
	<br><br>		
	
			<table id="" border="1" width="100%"  class="table table-bordered">  
				<tr>
					<th>Bill#  </th>
					<!--<th>Itemname</th>			-->		
        			<th>Sent On</th>
        			<th>Due Date</th>
        			<th>Total Price</th>
        			<!--<th>Payment</th>
        			<th>Verification</th>
        			<th>Details</th>-->
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
					<td id="billdetailid_<?php echo $billinfo[0]['billid'];?>"><?php if(isset($billinfo[0]['billname']) && $billinfo[0]['billname'] != '') { echo $billinfo[0]['billname']; } else { echo ''; } ?> 
						<a href="javascript:void(0)" onclick="showreport('<?php echo $billinfo[0]['billid'];?>','<?php echo $billinfo[0]['billid'];?>');">Expand</a>			
						<input type="hidden" name="billnumber" id="billnumber" value="<?php if(isset($billinfo[0]['billid']) && $billinfo[0]['billid'] != '') { echo $billinfo[0]['billid']; } else { echo ''; } ?>"/>
					</td>					
					<td><?php echo date('m/d/Y', strtotime($billinfo[0]['billedon']));?> </td>
					<td><?php echo date('m/d/Y', strtotime($billinfo[0]['customerduedate']));?> </td>
					<td style="padding-left:5; text-align:right;">$<?php echo number_format($finaltot1,2);?> </td>
					<!---->
					<!--<td><?php //echo $value['costcode'];?> </td>-->
			    </tr>
	    	<tr>
    			<td align="right">
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
	    	<!--</table>-->
	  <tr id="billitemdetailsrow" style="display:none;"> 
	  <td colspan="6">
	
	<table class="table table-bordered reportdiv<?php echo $value['billid']; ?> dclose" style="display:none;">
		<tr>
			<th width="10%">Item Image  </th>
			<th width="20%">Itemcode  </th>
			<th width="25%">Itemname</th>
			<th width="10%">Date Requested</th>
			<th width="10%">Cost Code</th>
			<th width="5%">Qty</th>
			<th width="5%">Unit</th>
			<th width="5%">Price</th>
			<th width="10%">Total Price</th>
			
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
					<td><img src="<?php echo $imgName;?>" width="80" height="80"></td>
					<td><?php echo $value['itemcode'];?> </td>
					<td><?php echo $value['itemname'];?> </td>
					<td><?php echo date('m/d/Y', strtotime($value['daterequested']));?> </td>
					<td><?php echo $value['costcode'];?> </td>
					<td><?php echo $value['quantity'];?> </td>
					<td><?php echo $value['unit'];?> </td>
					<td style="padding-left:5; text-align:right;">$<?php echo $value['ea'];?> </td>
					<td style="padding-left:5; text-align:right;">$<?php echo number_format($value['quantity'] * $value['ea'],2);?> </td>					
				</tr>
				</tr>
	<?php   } ?>
	 			
	</table>
	
	</td>
	</tr>
	    <!--	<table id="" border="1" width="100%"  class="table table-bordered">  -->
			<tr>   	
		    	<td colspan="3" style="padding-left:5; text-align:right;">Markup Total (<?php echo $markuptotalpercent.'%'?>)</td>
		    	<td style="padding-left:5; text-align:right;">$<?php echo number_format(($totalprice * $markuptotalpercent/100),2); ?></td>				
	       </tr>
	       <tr>   	
		    	<td colspan="3" style="padding-left:5; text-align:right;">Subtotal</td>
		    	<td style="padding-left:5; text-align:right;">$<?php echo number_format($subtotal,2); ?></td>
	       </tr>
	        <tr>   	
		    	<td colspan="3" style="padding-left:5; text-align:right;">Tax</td>
		    	<td style="padding-left:5; text-align:right;">$<?php echo number_format(($totalprice*$settings->taxrate/100),2); ?></td>
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
				    	<td colspan="3" style="padding-left:5; text-align:right;"><?php echo $v['servicelaboritems'];?></td>
				    	<td colspan="3" style="padding-left:5;text-align:right; ">$<?php echo number_format($v['price'],2);?></td>
				    </tr>
				    <tr>   	
				    	<td colspan="3" style="padding-left:5; text-align:right;">Qty</td>
				    	<td colspan="3" style="padding-left:5;text-align:right; "><?php echo $qty;?></td>
				    </tr>
				    <tr>
				    	<td colspan="3" style="padding-left:5; text-align:right;">Tax &nbsp; (<?php echo number_format($v['tax'],2);?> % )</td> 
				    	<td style="padding-left:5; text-align:right;">$<?php echo $totPrice * ($v['tax']/100); ?></td>
			       	</tr>
<?php 			$serviceItemTax += $totPrice + ($totPrice * ($v['tax']/100));	}
			} $finaltot = $serviceItemTax + $finaltotal; 

			  $amouttopay = $finaltot - $amountpaid; 	
			?>
	        <tr>   	
		    	<td colspan="3" style="padding-left:5; text-align:right;">Total</td>
		    	<td style="padding-left:5;text-align:right;">$<?php echo number_format($finaltot,2); ?></td>
	       </tr>
	</table>
	<br><br>
	
	<?php	} ?>
</div>
</form>


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


</body>
</html>