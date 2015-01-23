<html>
<head>
<script type="text/javascript">
function loadbillitems(obj)
{
	var billid = $(obj).val();
	
	$("#customerbilldetail").attr('action',"<?php echo base_url('site/customerbill'); ?>");
	$("#customerbilldetail").submit();
}

function showreport(invoicenum,i)
{	
	if($('a','td#billdetailid_'+i).text() == "Expand"){
		$('a','td#billdetailid_'+i).text('Collapse');
		$(jq("reportdiv"+invoicenum)).css('display','block');
	}else{
		$('a','td#billdetailid_'+i).text('Expand');
		$(jq("reportdiv"+invoicenum)).css('display','none');
	}
}

function paycc(ptype,idnumber,amount)
{
	if(confirm("Do You really want to Change The Payment Type?")){
		if(ptype != 'Credit Card')
		{
			return true;
		}
		$('#paymenttype_' + idnumber + " option:first-child").attr("selected", true);
		//var invoicenumber = $('#invoicenumber_' + idnumber).val();
		//$("#ccpayinvoicenumber").val(invoicenumber);
		//$("#ccpayinvoiceamount").val(amount);
		//$("#ccpayamountshow").html(amount);
		//$("#paymodal").modal();
	}else {
	var hidepaytype = $('#hiddenpaytype' + idnumber).val();
	$('#paymenttype_' + idnumber +' option[value="' + hidepaytype + '"]').prop('selected', true);
	}
}

function update_invoice_payment_status(idnumber)
{	
   // var invoice_payment_status_value = $('#invoice_payment_' + idnumber + " option:selected").val();
    var payment_type = $('#paymenttype_' + idnumber + " option:selected").val();
    var amount_value = $('#amountpaid_' + idnumber).val();    
    var refnum_value = $('#refnum_' + idnumber + "").val();
    
   /* if(invoice_payment_type_value == 'Credit Card')
		return false;
	if(invoice_payment_type_value=='')
		return false;*/
		
    var url = "<?php echo base_url("site/update_customer_bill_payment_status");?>";
    
    $.ajax({
        type: "POST",
        url: url,
        data: {billid:idnumber, paymenttype: payment_type, refnum: refnum_value, amount: amount_value}
    }).done(function(data) {
    	//$('#paymentstatus' + idnumber).html('Paid');
        //$('#message_div').html(data);
    });
}
    
function jq( myid ) { 
    return "." + myid.replace( /(:|\.|\[|\])/g, "\\$1" ); 
}
</script>
</head>
<body>

<div class="control-group">
<form id="customerbilldetail" action="" method="POST" >
<table class="" align="center" width="40%">
	<tr><td colspan="2" align="center"><h3>Customer Bill Details </h3></td></tr>
	<tbody>
		<tr>
			<td> Select Bill </td>
			<td> 
				<select id="billid" name="billid" onchange="loadbillitems(this)">
					<option value="">Choose</option>
					<?php 
					$selectedbill = '';
					if(isset($billdetails) && count($billdetails) > 0)
					{
						foreach ($billdetails as $key=>$val)
						{
							if(isset($selectedbill) && $val['billid'] == $selectedbill)
							{
								$selectedBill = ' selected ';
							}
							else 
							{
								$selectedbill = ' ';
							}
							?>
							<option value="<?php echo $val['billid'];?>"  <?php echo $selectedbill;?> ><?php echo $val['billname'];?></option>	
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
				<tr>
					<td>Payment Type:</td><td> <?php if(isset($billinfo[0]['customerpaymenttype']) && $billinfo[0]['customerpaymenttype'] != '') { echo $billinfo[0]['customerpaymenttype']; } else { echo ''; } ?> </td>
				</tr>
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
        			<th>Payment</th>
        			<th>Verification</th>
        			<th>Details</th>
				</tr>	
			
	<?php	
			$totalprice = 0;	
			foreach ($billItemdetails as $k=>$value)
			{  ?>
				<tr> 
					<td id="billdetailid_<?php echo $value['billid'];?>"><?php echo $value['billname'];?> 
						<a href="javascript:void(0)" onclick="showreport('<?php echo $value['billid'];?>','<?php echo $value['billid'];?>');">Expand</a>			
						<input type="hidden" name="billnumber_<?php echo $value['billid'];?>" id="billnumber_<?php echo $value['billid'];?>" value="<?php echo $value['billid'];?>"/>
					</td>
					<!--<td><?php echo $value['itemname'];?> </td>-->
					<td><?php echo date('m/d/Y', strtotime($value['billedon']));?> </td>
					<td><?php echo date('m/d/Y', strtotime($value['daterequested']));?> </td>
					<td><?php echo $value['quantity'] * $value['ea'];?> </td>
					<td>
						<select id="paymenttype_<?php echo $value['billid'];?>" required onchange="paycc(this.value,<?php echo $value['billid'];?>,'<?php echo $value['totalprice']?>');">
            				<option value="">Select Payment Type</option>
            				<?php //if($item->bankaccount && @$item->bankaccount->routingnumber && @$item->bankaccount->accountnumber){?>
            				<!--<option <?php echo $item->paymenttype=='Credit Card'?'SELECTED':'';?> value="Credit Card">Credit Card</option>-->
            				<?php //}?>
            				<option <?php echo $value['paymenttype']=='Cash'?'SELECTED':'';?> value="Cash">Cash</option>
            				<option <?php echo $value['paymenttype']=='Check'?'SELECTED':'';?> value="Check">Check</option>
            			</select>
            			<input type="text" value="<?php echo $value['paymentstatus']=='Paid'?$value['paymentstatus']:'';?>" name="refnum" id="refnum_<?php echo $value['billid'];?>" style="width:80px;">
            			 Amount:<input type="text" value="<?php echo $value['amountpaid'];?>" name="amountpaid" id="amountpaid_<?php echo $value['billid'];?>" style="width:80px;">
            			
                    	<button onclick="update_invoice_payment_status('<?php echo $value['billid'];?>')">Save</button>
                    </td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<!--<td><?php echo $value['costcode'];?> </td>-->
				</tr>
	<?php	 $totalprice += $value['quantity'] * $value['ea']; 
			 $markuptotalpercent += $value['markuptotalpercent'];
			 
		  } 		 
		     $subtotal = $totalprice + ($totalprice * $markuptotalpercent/100);
		     $finaltotal = $subtotal + (@$subtotal*@$settings->taxrate/100);
		     
    	?>
			<tr>   	
		    	<td colspan="3" style="padding-left:5; text-align:right;">Markup Total (<?php echo $markuptotalpercent.'%'?>)</td>
		    	<td style="padding-left:5;"><?php echo ($totalprice * $markuptotalpercent/100); ?></td>
				<td style="padding-left:5;">&nbsp;</td>
		        <td style="padding-left:5;">&nbsp;</td>
	       </tr>
	       <tr>   	
		    	<td colspan="3" style="padding-left:5; text-align:right;">Subtotal</td>
		    	<td style="padding-left:5;"><?php echo $subtotal; ?></td>
				<td style="padding-left:5;">&nbsp;</td>
		        <td style="padding-left:5;">&nbsp;</td>
	       </tr>
	        <tr>   	
		    	<td colspan="3" style="padding-left:5; text-align:right;">Tax</td>
		    	<td style="padding-left:5;"><?php echo ($subtotal*$settings->taxrate/100); ?></td>
				<td style="padding-left:5;">&nbsp;</td>
		        <td style="padding-left:5;">&nbsp;</td>
	       </tr>
	        <tr>   	
		    	<td colspan="3" style="padding-left:5; text-align:right;">Total</td>
		    	<td style="padding-left:5;"><?php echo $finaltotal; ?></td>
				<td style="padding-left:5;">&nbsp;</td>
		        <td style="padding-left:5;">&nbsp;</td>
	       </tr>
	</table>
	<br><br>
	<div>
	<?php	
			$totalprice = 0;	
			foreach ($billItemdetails as $k=>$value)
			{  ?>
	<table class="table table-bordered reportdiv<?php echo $value['billid']; ?> dclose" style="display:none;">
		<tr>
			<th>Itemcode  </th>
			<th>Itemname</th>
			<th>Qty</th>
			<th>Unit</th>
			<th>Price</th>
			<th>Total Price</th>
			<th>Date Requested</th>
			<th>Cost Code</th>
		</tr>	
		
				 <tr> 
					<td><?php echo $value['itemcode'];?> </td>
					<td><?php echo $value['itemname'];?> </td>
					<td><?php echo $value['quantity'];?> </td>
					<td><?php echo $value['unit'];?> </td>
					<td><?php echo $value['ea'];?> </td>
					<td><?php echo $value['quantity'] * $value['ea'];?> </td>
					<td><?php echo date('m/d/Y', strtotime($value['daterequested']));?> </td>
					<td><?php echo $value['costcode'];?> </td>
				</tr>
				</tr>
	<?php   } ?>
	 			
	</table>
	</div>
	<?php	} ?>
</div>
</form>
</body>
</html>