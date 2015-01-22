<html>
<head>
<script type="text/javascript">
function loadbillitems(obj)
{
	var billid = $(obj).val();
	
	$("#customerbilldetail").attr('action',"<?php echo base_url('site/customerbill'); ?>");
	$("#customerbilldetail").submit();
}
</script>
</head>
<body>

<div class="control-group">
<form id="customerbilldetail" action="" method="POST" >
<table class="table table-bordered">
	<tr><td align="center"><h3>Customer Bill Details </h3></td></tr>
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
<br><br>
<?php //echo '@@'.$selectedbill;
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
			
			<table id="" border="1" width="100%"  class="table table-bordered">  
				<tr>
					<th> Itemcode  </th>
					<th>Itemname</th>
					<th>Qty</th>
					<th>Unit</th>
					<th>Price</th>
					<th>Total Price</th>
					<th>Date Requested</th>
					<th>Cost Code</th>
				</tr>	
			
	<?php		foreach ($billItemdetails as $k=>$value)
			{ ?>
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
	<?php	} ?>
	</table>
	<?php	} ?>
</div>
</form>
<?php //echo '<pre>$billdetails',print_r($billdetails);?>
<?php //echo '<pre>',print_r($billItemdetails);?>
</body>

</html>
