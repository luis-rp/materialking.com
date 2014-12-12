 <script type="text/javascript">
 $(document).ready(function(){
 tour3 = new Tour({
	  steps: [
	  {
	    element: "#step1",
	    title: "Step 1",
	    content: "Welcome to the on-page tour for Invoices"
	  },
	]
	});
	$("#activatetour").click(function(e){
		  e.preventDefault();
			$("#tourcontrols").remove();
			tour3.restart();
			// Initialize the tour
			tour3.init();
			start();
		});
	$('#canceltour').live('click',endTour);
 });
 function start(){
		// Start the tour
			tour3.start();
		 }
 function endTour(){
	 $("#tourcontrols").remove();
	 tour3.end();
		}
 </script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.datefield').datepicker();
    });
    function showInvoice(invoicenum,invoicequote)
    {
        $("#invoicenum").val(invoicenum);
        $("#invoicequote").val(invoicequote);	
        $("#invoiceform").submit();
    }
    
    function showContractInvoice(invoicenum,invoicequote)
    {
        $("#invoicenum").val(invoicenum);
        $("#invoicequote").val(invoicequote);	
        $("#invoiceform").attr('action', "<?php echo site_url('admin/quote/contract_invoice'); ?>");
        $("#invoiceform").submit();
    }
    
    function update_invoice_status(invoice_number) {
        var invoice_status_value = $('#invoice_' + invoice_number + " option:selected").val();
        var url = "<?php echo base_url("admin/quote/update_invoice_status");?>";
        $.ajax({
            type: "POST",
            url: url,
            data: {id:invoice_number, status: invoice_status_value}
        }).done(function(data) {
            $('#message_div').html(data);
        });
    }
    function update_invoice_payment_status(idnumber)
    {
        var invoice_payment_status_value = $('#invoice_payment_' + idnumber + " option:selected").val();
        var invoice_payment_type_value = $('#invoice_paymenttype_' + idnumber + " option:selected").val();
        var invoice_payment_amount_value = $('#invoice_paymentamount_' + idnumber).html();
        var invoice_number = $('#invoicenumber_' + idnumber).html();
        var refnum_value = $('#refnum_' + idnumber + "").val();
        if(invoice_payment_type_value == 'Credit Card')
			return false;
		if(invoice_payment_type_value=='')
			return false;
        var url = "<?php echo base_url("admin/quote/update_invoice_payment_status");?>";
        //alert(invoice_payment_status_value);
        $.ajax({
            type: "POST",
            url: url,
            data: {invoicenum:invoice_number, paymentstatus: invoice_payment_status_value, paymenttype: invoice_payment_type_value, refnum: refnum_value, amount: invoice_payment_amount_value}
        }).done(function(data) {
        	$('#paymentstatus' + idnumber).html('Paid');
            $('#message_div').html(data);
        });
    }
</script>
<style type="text/css">
    .box { padding-bottom: 0; }
    .box > p { margin-bottom: 20px; }
    #popovers li, #tooltips li {
        display: block;
        float: left;
        list-style: none;
        margin-right: 20px;
    }
    .adminflare > div { margin-bottom: 20px; }
</style>
<script src="<?php echo base_url(); ?>templates/site/assets/js/creditcard.js"></script>
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
<script type="text/javascript" charset="utf-8">
$(document).ready( function() {
});
function paycc(ptype,idnumber,amount)
{
	if(confirm("Do You really want to Change The Payment Type?")){
		if(ptype != 'Credit Card')
		{
			return true;
		}
		$('#invoice_paymenttype_' + idnumber + " option:first-child").attr("selected", true);
		var invoicenumber = $('#invoicenumber_' + idnumber).html();
		$("#ccpayinvoicenumber").val(invoicenumber);
		$("#ccpayinvoiceamount").val(amount);
		$("#ccpayamountshow").html(amount);
		$("#paymodal").modal();
	}else {
	var hidepaytype = $('#hiddenpaytype' + idnumber).val();
	$('#invoice_paymenttype_' + idnumber +' option[value="' + hidepaytype + '"]').prop('selected', true);
	}
}

function shownotice(newval,oldval,id){

	if(confirm("Do You really want to Change The Payment Type?")){
		$('#refnum_' + id).val(newval);
	}else
		$('#refnum_' + id).val(oldval);

}

function showreport()
{
	$("#reportdiv").toggle();
}

</script>
 <?php if(isset($settingtour) && $settingtour==1) { ?>
<div id="tourcontrols" class="tourcontrols" style="right: 30px;">
<p>First time here?</p>
<span class="button" id="activatetour">Start the tour</span>
<span class="closeX" id="canceltour"></span></div><?php } ?>
<section class="row-fluid">
    <h3 class="box-header" style="display:inline;" ><span id="step1"><?php echo $heading; ?></span> - <?php echo ($this->session->userdata('managedprojectdetails')) ? $this->session->userdata('managedprojectdetails')->title : "no title" ?><a href="<?php echo site_url('admin/quote/export'); ?>" class="btn btn-green">Export</a>&nbsp;&nbsp;<a href="<?php echo site_url('admin/quote/invoicepdf'); ?>" class="btn btn-green">View PDF</a><br /></h3>
    <div class="box">
        <div class="span12">
            <div id="message_div">
                <?php echo $this->session->flashdata('message'); ?>
            </div>
            <div class="datagrid-example">
            	<div>
                    <form id="invoiceform" class="form-inline" style="padding:0px; margin:0px" method="post" action="<?php echo site_url('admin/quote/invoice'); ?>">
                        <input type="hidden" id="invoicenum" name="invoicenum"/>                        
                        <input type="hidden" id="invoicequote" name="invoicequote"/>
                    </form>
                    <form class="form-inline" action="<?php echo site_url('admin/quote/invoices') ?>" method="post">
                        Invoice#: <input type="text" name="searchinvoicenum" value="<?php echo @$_POST['searchinvoicenum'] ?>"/>
                            &nbsp;&nbsp;
                        From: <input type="text" name="searchfrom" value="<?php if(isset($_POST['searchfrom'])) echo @$_POST['searchfrom']; else echo date('m/d/Y', strtotime("now -30 days") ) ?>" class="datefield" style="width: 70px;"/>
                        &nbsp;&nbsp;
                        To: <input type="text" name="searchto" value="<?php if(isset($_POST['searchto'])) echo @$_POST['searchto']; else echo date('m/d/Y'); ?>" class="datefield" style="width: 70px;"/>
                        &nbsp;&nbsp;
                        Company:
                        <select id="searchcompany" name="searchbycompany" style="width: 120px;">
                            <option value=''>All Companies</option>
                            <?php foreach ($companies as $company) { ?>
                                <option value="<?php echo $company->id ?>"
                                <?php
                                if (@$_POST['searchbycompany'] == $company->id) {
                                    echo 'SELECTED';
                                }
                                ?>
                                        >
                                <?php echo $company->title ?>
                                </option>
<?php } ?>
                        </select>
                        &nbsp;&nbsp;
                        Status:
                        <select id="searchstatus" name="searchstatus" style="width: 100px;">
                            <option value=''>All</option>
                            <option value="Pending" <?php if (@$_POST['searchstatus'] == 'Pending') { echo 'SELECTED'; } ?>>Pending</option>
                            <option value="Verified" <?php if (@$_POST['searchstatus'] == 'Verified') { echo 'SELECTED'; } ?>>Verified</option>
                            <option value="Error" <?php if (@$_POST['searchstatus'] == 'Error') { echo 'SELECTED'; } ?>>Error</option>
                        </select>
                        &nbsp;&nbsp;
                        Payment:
                        <select id="searchpaymentstatus" name="searchpaymentstatus" style="width: 100px;">
                            <option value=''>All</option>
                            <option value="Paid" <?php if (@$_POST['searchpaymentstatus'] == 'Paid') { echo 'SELECTED'; } ?>>Paid</option>
                           <option value="Requested Payment" <?php if (@$_POST['searchpaymentstatus'] == 'Requested Payment') { echo 'SELECTED'; } ?>>Requested Payment</option>
                            <option value="Unpaid" <?php if (@$_POST['searchpaymentstatus'] == 'Unpaid') { echo 'SELECTED'; } ?>>Unpaid</option>
                        </select>
                        &nbsp;&nbsp;
                        <input type="submit" value="Filter" class="btn btn-primary"/>
                        <a href="<?php echo site_url('admin/quote/invoices'); ?>">
                            <input type="button" value="Show All" class="btn btn-primary"/>
                        </a>
                    </form>
                    <?php if (!@$items) echo 'No Invoices Found.'; ?>
            	</div>
                <div>
                    <table id="datatable" class="table table-bordered">
                    	<thead>
                    		<tr>
                    			<th>PO#</th>
                    			<th>Invoice#</th>
                    			<th>Received On</th>
                    			<th>Due Date</th>
                    			<th>Total Cost</th>
                    			<th>Payment</th>
                    			<th>Verification</th>
                    			<th>Details</th>
                    		</tr>
                    	</thead>
                    	<tbody>
                    		<?php $i=0;
                    		$finaltotal = 0;
                    		$totalpaid= 0;
                    		$totalunpaid= 0;
                    		foreach($items as $item){ $i++;?>
                    		<tr>
                    			<td><?php echo $item->ponum;?></td>
                    			<td id="invoicenumber_<?php echo $i;?>"><?php echo $item->invoicenum;?></br>
                    		    <a href="javascript:void(0)" onclick="showreport();">Expand</a>
                    			</td>
                    			<td><?php echo date('m/d/Y', strtotime($item->receiveddate));?></td>
                    			<?php //if(isset($item->quote->duedate) && $item->quote->duedate!="") { echo $item->quote->duedate; } else echo "";?>
                    			<td><?php if($item->datedue) { echo date("m/d/Y", strtotime($item->datedue));  } else{ echo "No Date Set";}?></td>
                    			<td id="invoice_paymentamount_<?php echo $i;?>"><?php echo $item->totalprice;?></td>
                    			<td>
                    				<span id="paymentstatus<?php echo $i;?>"><?php echo $item->paymentstatus;?></span>&nbsp;
                    				<?php if($item->status != 'Verified'){?>
                    				<select id="invoice_paymenttype_<?php echo $i;?>" required onchange="paycc(this.value,<?php echo $i;?>,'<?php echo $item->totalprice?>');">
                    				<option value="">Select Payment Type</option>
                    				<?php if($item->bankaccount && @$item->bankaccount->routingnumber && @$item->bankaccount->accountnumber){?>
                    				<option <?php echo $item->paymenttype=='Credit Card'?'SELECTED':'';?> value="Credit Card">Credit Card</option>
                    				<?php }?>
                    				<option <?php echo $item->paymenttype=='Cash'?'SELECTED':'';?> value="Cash">Cash</option>
                    				<option <?php echo $item->paymenttype=='Check'?'SELECTED':'';?> value="Check">Check</option>
                    				</select>
                    				<input type="hidden" id="hiddenpaytype<?php echo $i;?>" name="hiddenpaytype<?php echo $i;?>" value="<?php echo $item->paymenttype;?>" />
                    				<input type="text" value="<?php echo $item->paymentstatus=='Paid'?$item->refnum:'';?>" name="refnum" id="refnum_<?php echo $i;?>" onblur="shownotice(this.value, '<?php echo $item->paymentstatus=='Paid'?$item->refnum:'';?>',<?php echo $i;?>);">
                    				<button onclick="update_invoice_payment_status('<?php echo $i;?>')">Save</button>
                    				<?php }else{//verified payment, show notes?>
                    				/ <?php echo $item->paymenttype;?> / <?php echo $item->refnum;?>
                    				<?php }?>
                    				<?php if($item->paymentstatus=='Requested Payment'){?>
                    				<br/>
                    				<i class="icon-lightbulb">
                    				Payment Requested by
                    				<?php echo (@$item->companydetails->title)?$item->companydetails->title:$item->companydetails->companyname;?>
                    				on <?php echo $item->refnum;?>
                    				</i>
                    				<?php }?>
                    			</td>
                    			<td><?php echo $item->status;?></td>
                    			<td><?php echo $item->actions;?></td>
                    		</tr>
                    		<?php
                    		$finaltotal += $item->totalprice;
                    		if($item->paymentstatus=='Paid')
                    		{
                    			$totalpaid+= $item->totalprice;
                    		}

                    		if($item->paymentstatus=='Unpaid' || $item->paymentstatus=='Requested Payment')
                    		{
                    			$totalunpaid+= $item->totalprice;
                    		}

                    		}?>
                    		<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td style="text-align:right;">Total:</td><td><?php echo "$ ".round($finaltotal,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                    		<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td style="text-align:right;">Total Paid:</td><td><?php echo "$ ".round($totalpaid,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                    		<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td style="text-align:right;">Total Due:</td><td><?php echo "$ ".round($totalunpaid,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                    	</tbody>
                    </table>
                </div>
                
                <div id="reportdiv" style="display:none;">
              
                 <table class="table table-bordered">
			    	<tr>
			    		<th width="120">Company</th>
			    		<th width="75">PO#</th>
			    		<th width="120">Item Code</th>
			    		<th width="200">Item Name</th>
			    		<th width="50">Unit</th>
			    		<th width="50">Qty.</th>
			    		<th width="50">EA</th>
			    		<th width="50">Total</th>
			    		<th width="50">Payment</th>
			    		<th width="50">Verification</th>
			    		<th width="120">Cost Code</th>
			    	</tr>
			    	<?php
			    	foreach($reports as $report)
			    		{
			    	foreach($report->items as $item)
			    		{
			    			if($item->potype == "Contract" )
			    			$amount = $item->ea;
			    			else 
			    			$amount = $item->quantity * $item->ea;
			    			$tax=3;
			    			$amount = round($amount + ($amount*$tax/100),2);
			    			$amount += $amount;
			    	?>
			    	<tr>
			    		<td><?php echo $item->companyname;?></td>
			    		<td><?php echo $item->ponum;?></td>
			    		<td><?php echo $item->itemcode;?></td>
			    		<td><?php echo $item->itemname;?></td>
			    		<td><?php echo $item->unit;?></td>
			    		<td><?php echo $item->quantity;?></td>
			    		<td><?php echo round($item->ea,2);?></td>
			    		<td>$<?php echo round($amount,2);?></td>
			    		<td><?php echo $item->paymentstatus;?></td>
			    		<td><?php echo $item->status;?></td>
			    		<td><?php echo $item->costcode;?></td>			    		
			    	</tr>
			    	<?php
			    		} }
			    	?>
		      </table>
              </div>  
                
                
                
                
            </div>
        </div>
    </div>
</section>
<div id="paymodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
    <div class="modal-header">
    	<h3>
    	Pay by credit card
		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
		</h3>
	</div>
	<div class="modal-body" id="quoteitems">
        <form method="post" action="<?php echo site_url('admin/quote/payinvoicebycc/');?>" onsubmit="return validatecc();">
	        <input type="hidden" id="ccpayinvoicenumber" name="invoicenum"/>
	        <input type="hidden" id="ccpayinvoiceamount" name="amount"/>
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