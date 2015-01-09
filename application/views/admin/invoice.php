<script type="text/javascript">
  function sendDueDatealert(invoicenum,ponum,companyid,quote,award)
    {    	
            $.ajax({
                type: "post",
                data: "invoicenum=" + invoicenum+"&ponum="+ponum+"&companyid="+companyid+"&quote="+quote+"&award="+award,
                url: "sendduedatealert"
            }).done(function(data) { 
            	$("#msgtag").css("display","block");
            	$("#msgtag").html('<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Request due date alert sent via email.</div></div>');
               //window.location = window.location;
            });
    }
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.datefield').datepicker();
    });
    function showInvoice(invoicenum)
    {
        $("#invoicenum").val(invoicenum);
        $("#invoiceform").submit();
    }
    
    function showContractInvoice(invoicenum)
    {
        $("#invoicenum").val(invoicenum);
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

</script>

<section class="row-fluid">
    <h3 class="box-header"><?php echo @$heading; ?> - <?php echo ($this->session->userdata('managedprojectdetails')) ? $this->session->userdata('managedprojectdetails')->title : "no project title" ?></h3>
    <div class="box">
        <div class="span12">
            <a class="btn btn-green" href="javascript:void(0)" onclick="history.back();">&lt;&lt; Back</a>
            <br/>
            <br/> <br/>
            <div id="msgtag">
            <?php echo $this->session->flashdata('message'); ?>
            <?php echo @$message; ?>
			</div>
            <div align="center">
                <h4>
                    INVOICE #: <?php echo $invoice->invoicenum; ?> 
                    STATUS: <font color=#FF0000""> <?php echo $invoice->status; ?></font>
                    PAYMENT: <font color=#FF0000""> <?php echo $invoice->paymentstatus; ?></font>
                    <?php if(isset($invoice->datedue) && $invoice->datedue !=""){?>
                    DUE DATE: <font color=#FF0000""> <?php  echo date("m/d/Y", strtotime( $invoice->datedue)); ?></font>
                    <br/>
                    <font color=#FF0000""> <?php if($invoice->status == "Verified" && $invoice->paymentstatus == "Paid" && isset($invoice->items[0]->paymentdate)) {  
                    // echo "<pre>",print_r($invoice);
                    	if(strtotime($invoice->items[0]->paymentdate) < strtotime($invoice->datedue))
                    	echo "PAID EARLY";
                    	elseif (strtotime($invoice->items[0]->paymentdate) == strtotime($invoice->datedue))
                    	echo "PAID ON TIME";
                    	elseif (strtotime($invoice->items[0]->paymentdate) > strtotime($invoice->datedue))
                    	echo "PAID LATE";
                    	echo "&nbsp;&nbsp;". date("m/d/Y",  strtotime($invoice->items[0]->paymentdate));
                    } else { echo $invoice->datedue > date('Y-m-d')?'Upcoming':'Overdue'; } ?></font>
                	<?php } else {?> <input type="button" value="Request Due Date" id="btnRequestDuedate" name="btnRequestDuedate" class="btn btn-primary btn-small" onclick="sendDueDatealert('<?php echo $invoice->invoicenum; ?>','<?php echo $quote->ponum ?>','<?php echo $invoice->items[0]->companyid; ?>','<?php echo $invoice->quote; ?>','<?php echo $invoice->items[0]->award; ?>');">  <?php }?>
                </h4>
            </div>
            
            <div class="newbox">
            <table width="100%" cellspacing="2" cellpadding="2">
                <tr>
                    <td width="33%" align="left" valign="top">
                        <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
                            <tr>
                                <th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Project Information</strong></font></th>
                            </tr>
                            <tr>
                                <td width="33%" valign="top">Project Title</td>
                                <td width="7%" valign="top">&nbsp;</td>
                                <td width="60%" valign="top"><?php echo $project->title; ?></td>
                            </tr>
                            <tr>
                                <td valign="top">Address</td>
                                <td valign="top">&nbsp;</td>
                                <td valign="top"><?php echo $project->address; ?></td>
                            </tr>
                        </table>
                    </td>
                    <td width="10" align="left" valign="top">&nbsp;</td>
                    <td width="65%" align="left" valign="top">
                        <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
                            <tr>
                                <th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Purchase Order Information</strong></font></th>
                            </tr>
                            <tr>
                                <td width="33%" valign="top">PO#</td>
                                <td width="7%" valign="top">&nbsp;</td>
                                <td width="60%" valign="top"><?php echo $quote->ponum ?></td>
                            </tr>
                            <tr>
                                <td valign="top">Subject</td>
                                <td valign="top">&nbsp;</td>
                                <td valign="top"><?php echo $quote->subject; ?></td>
                            </tr>
                            <tr>
                                <td valign="top">PO# Date</td>
                                <td valign="top">&nbsp;</td>
                                <td valign="top"><?php echo $quote->podate; ?></td>
                            </tr>
                        </table></td>
                </tr>
                <tr>
                    <td align="left" valign="top">&nbsp;</td>
                    <td align="left" valign="top">&nbsp;</td>
                    <td align="left" valign="top">&nbsp;</td>
                </tr>
                <tr>
                    <td align="left" valign="top">
                        <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
                            <tr>
                                <th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>From</strong></font></th>
                            </tr>
                            <tr>
                                <td width="33%" valign="top">Contact</td>
                                <td width="7%" valign="top">&nbsp;</td>
                                <td width="60%" valign="top"><?php echo $purchasingadmin->fullname; ?></td>
                            </tr>
                            <tr>
                                <td valign="top">Company</td>
                                <td valign="top">&nbsp;</td>
                                <td valign="top"><?php echo $purchasingadmin->companyname; ?></td>
                            </tr>
                            <tr>
                                <td valign="top">Address</td>
                                <td valign="top">&nbsp;</td>
                                <td valign="top"><?php echo nl2br($purchasingadmin->address); ?></td>
                            </tr>
                            <tr>
                                <td valign="top">Phone</td>
                                <td valign="top">&nbsp;</td>
                                <td valign="top"><?php echo $purchasingadmin->phone; ?></td>
                            </tr>
                            <tr>
                                <td valign="top">Fax</td>
                                <td valign="top">&nbsp;</td>
                                <td valign="top"><?php echo $purchasingadmin->fax; ?></td>
                            </tr>
                        </table></td>
                    <td align="left" valign="top">&nbsp;</td>
                    <td align="left" valign="top">
                        <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
                            <tr>
                                <th bgcolor="#000033"><font color="#FFFFFF"><strong>Ship to</strong></font></th>
                            </tr>
                            <tr>
                                <td><?php echo nl2br($awarded->shipto); ?></td>
                            </tr>
                        </table></td>
                </tr>

            </table>

            <table width="100%" cellspacing="0" cellpadding="4">
                <tr>
                    <td>Items:</td>
                </tr>
            </table>

            <br/>

            <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
                <thead>
                    <tr>
                        <th bgcolor="#000033"><font color="#FFFFFF">Item No.</font></th>
                        <th bgcolor="#000033"><font color="#FFFFFF">Description</font></th>
                        <th bgcolor="#000033"><font color="#FFFFFF">Company</font></th>
                        <th bgcolor="#000033"><font color="#FFFFFF">Date Requested</font></th>
                        <th bgcolor="#000033"><font color="#FFFFFF">Date Received</font></th>
                        <th bgcolor="#000033"><font color="#FFFFFF">Quantity</font></th>
                        <th bgcolor="#000033"><font color="#FFFFFF">Unit</font></th>
                        <th bgcolor="#000033"><font color="#FFFFFF">Unit Price</font></th>
                        <th bgcolor="#000033"><font color="#FFFFFF">Total Price</font></th>
                    </tr>
                </thead>
                <?php
                $totalprice = 0;
                $i = 0;
                foreach ($invoice->items as $invoiceitem) {
                    $invoiceitem = (array) $invoiceitem;
                    $totalprice += $invoiceitem['ea'] * $invoiceitem['quantity'];
                    echo '<tr nobr="true">
						    <td style="border: 1px solid #000000;">' . ++$i . '</td>
						    <td style="border: 1px solid #000000;">' . htmlentities($invoiceitem['itemname']) . '</td>
						    <td style="border: 1px solid #000000;">' . htmlentities($invoiceitem['companyname']) . '</td>
						    <td style="border: 1px solid #000000;">' . $invoiceitem['daterequested'] . '</td>
						    <td style="border: 1px solid #000000;">' . date("m/d/Y h:i A", strtotime($invoiceitem['shipdate'])) . '</td>
						    <td style="border: 1px solid #000000;">' . $invoiceitem['quantity'] . '</td>
						    <td style="border: 1px solid #000000;">' . $invoiceitem['unit'] . '</td>
						    <td align="right" style="border: 1px solid #000000;">$ ' . $invoiceitem['ea'] . '</td>
						    <td align="right" style="border: 1px solid #000000;">$ ' . $invoiceitem['ea'] * $invoiceitem['quantity'] . '</td>
						  </tr>
						  ';
                }
                $taxtotal = $totalprice * $config['taxpercent'] / 100;
                $grandtotal = $totalprice + $taxtotal;
                echo '<tr>
					    <td colspan="7" rowspan="3">
                      		<div style="width:70%">
                          		<br/>
                          		<h4 class="semi-bold">Terms and Conditions</h4>
                                <p>'.@$company->invoicenote.'</p>
                                <h5 class="text-right semi-bold">Thank you for your business</h5>
                      		</div>
                  		</td>
					    <td align="right">Subtotal</td>
					    <td align="right">$ ' . number_format($totalprice, 2) . '</td>
					  </tr>
					  <tr>
					    <td align="right">Tax</td>
					    <td align="right">$ ' . number_format($taxtotal, 2) . '</td>
					  </tr>
					  <tr>
					    <td align="right">Total</td>
					    <td align="right">$ ' . number_format($grandtotal, 2) . '</td>
					  </tr>
					';
                ?>
            </table>
            </div>
        </div>
        
        
        
        <div class="box">
        <div class="span12">
            <div id="message_div">
                <?php echo $this->session->flashdata('message'); ?>
            </div>
            <div class="datagrid-example">
            	<div>
                    <form id="invoiceform" class="form-inline" style="padding:0px; margin:0px" method="post" action="<?php echo site_url('admin/quote/invoice'); ?>">
                        <input type="hidden" id="invoicenum" name="invoicenum"/>
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
                    			<!-- <th>Details</th> -->
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
                    			<td id="invoicenumber_<?php echo $i;?>"><?php echo $item->invoicenum;?></td>
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
                    			<!--<td><?php echo $item->actions;?></td>-->
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
                    		<!-- <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td style="text-align:right;">Total:</td><td><?php echo "$ ".round($finaltotal,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                    		<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td style="text-align:right;">Total Paid:</td><td><?php echo "$ ".round($totalpaid,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                    		<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td style="text-align:right;">Total Due:</td><td><?php echo "$ ".round($totalunpaid,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>-->
                    	</tbody>
                    </table>
                </div>
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