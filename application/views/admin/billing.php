<?php echo '<script>var datedueurl="' . site_url('admin/quote/billdatedue') . '";</script>' ?> 
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
       
       <?php if (@$message_hidden_div != ''): ?>
			$('#message_div').html('<?php echo $message_hidden_div; ?>');				
	   <?endif; ?>
	   $('#message_hidden_div').val('');
        
    });
    
    function showBill(invoiceid,invoicequote){
    	
    	$("#billid").val(invoiceid);
        $("#billquote").val(invoicequote);	
        $("#billform").submit();
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
    function update_bill_payment_status(idnumber)
    {
        var invoice_payment_status_value = $('#invoice_payment_' + idnumber + " option:selected").val();
        var invoice_payment_type_value = $('#invoice_paymenttype_' + idnumber + " option:selected").val();
        var invoice_payment_amount_value = $('#invoice_paymentamount_' + idnumber).html();
        var total_due_amount_value = $('#total_due_' + idnumber).html();
        var invoice_number = $('#invoicenumber_' + idnumber).val();
        var refnum_value = $('#refnum_' + idnumber + "").val();
        var amountpaid = $('#amountpaid_' + idnumber).val();
        if($('#ispaid_' + idnumber).attr("checked"))
        var ispaid = 1;
        else
        var ispaid = 0;
        
        if(invoice_payment_type_value == 'Credit Card')
			return false;
		if(invoice_payment_type_value=='')
			return false;
			
		if(amountpaid == "" || amountpaid == 0)	
			return false;
		
		invoice_payment_amount_value = invoice_payment_amount_value.replace('$', '');
    	invoice_payment_amount_value = invoice_payment_amount_value.replace(/,/g, '');
    	invoice_payment_amount_value = parseFloat(invoice_payment_amount_value,10);	
    	
    	total_due_amount_value = total_due_amount_value.replace('$', '');
    	total_due_amount_value = total_due_amount_value.replace(/,/g, '');
    	total_due_amount_value = parseFloat(total_due_amount_value,10);	
				
    	if(amountpaid>total_due_amount_value){
			$('#message_div').html('<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Amount to pay cannot be greater than Total Due Amout</div></div>');
    		return false;
    	}	
        var url = "<?php echo base_url("admin/quote/update_bill_payment_status");?>";
        //alert(invoice_payment_status_value);
        $.ajax({
            type: "POST",
            url: url,
            data: {invoicenum:invoice_number, paymentstatus: invoice_payment_status_value, paymenttype: invoice_payment_type_value, refnum: refnum_value, amountpaid: amountpaid, ispaid:ispaid, invoice_payment_amount_value:invoice_payment_amount_value, total_due_amount_value:total_due_amount_value  }
        }).done(function(data) {
        	if(total_due_amount_value - amountpaid == 0)	
        		$('#paymentstatus' + idnumber).html('Paid');
        	else
        		$('#paymentstatus' + idnumber).html('Partial');
            	$('#message_div').html(data);
            	$('#message_hidden_div').val(data);
            	$('#messageform').submit();
        });
    }
    
    
    function setpaidamount(idnumber,chk){
    	    	
    	 if(chk == true){
    	 var total_due = $('#total_due_' + idnumber).html();
    	 total_due = total_due.replace('$', '');
    	 total_due = total_due.replace(/,/g, '');
    	 total_due = parseFloat(total_due,10);
    	 $('#amountpaid_' + idnumber).val(total_due);
    	 }else{
    	 	$('#amountpaid_' + idnumber).val('');
    	 }
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
		var invoicenumber = $('#invoicenumber_' + idnumber).val();
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

function showreport(invoicenum,i)
{	
	//$(".dclose").css('display','none');
	if($('a','td#invoicenumberid_'+i).text() == "Expand"){
		$('a','td#invoicenumberid_'+i).text('Collapse');
		$(jq("reportdiv"+invoicenum)).css('display','block');
	}else{
		$('a','td#invoicenumberid_'+i).text('Expand');
		$(jq("reportdiv"+invoicenum)).css('display','none');
	}
}

function jq( myid ) {
 
    return "." + myid.replace( /(:|\.|\[|\])/g, "\\$1" );
 
}

var datetext = "";
var isconfirm = "";

function changeduedate(count,invoicenum,datedue)
	{			
		if(datetext!= datedue) {
			if(confirm("Do you want to set the invoice due date to"+datedue)){
			datetext = datedue;
			isconfirm = "yes";
			$('#originaldate'+count).val(datedue);
			var data = "id="+invoicenum+"&customerduedate="+datedue;
			$.ajax({
				type: "post",
				data: data,
				url: datedueurl
			}).done(function(data) {
			});

		}else{
				$('#daterequested'+count).val($('#originaldate'+count).val());
				datetext = $('#originaldate'+count).val();			
				$('#canceldate').val(datedue);
				datedue = $('#originaldate'+count).val();			
		}
		}else{ 
				if(isconfirm == ""){
				$('#daterequested'+count).val($('#originaldate'+count).val());				
				datetext = $('#canceldate').val();									
				}
				
		}
	}
	
	
	function showhistorymodal(billid){
		
		$('#billhistorymodal'+billid).modal();
	}

</script>
 <?php if(isset($settingtour) && $settingtour==1) { ?>
<div id="tourcontrols" class="tourcontrols" style="right: 30px;">
<p>First time here?</p>
<span class="button" id="activatetour">Start the tour</span>
<span class="closeX" id="canceltour"></span></div><?php } ?>
<section class="row-fluid">

    <h3 class="box-header" style="display:inline;" ><span id="step1"><?php echo $heading; ?></span> - <?php echo ($this->session->userdata('managedprojectdetails')) ? $this->session->userdata('managedprojectdetails')->title : "no title" ?>
   <!-- <a href="<?php echo site_url('admin/quote/export'); ?>" class="btn btn-green">Export</a>&nbsp;&nbsp;
    <a href="<?php echo site_url('admin/quote/invoicepdf'); ?>" class="btn btn-green">View PDF</a><br />--></h3>
    <div class="box">
        <div class="span12">
        	 <form id="messageform" method="post" action="<?php echo site_url('admin/quote/billings/'.$this->session->userdata('managedprojectdetails')->id); ?>">
            <div id="message_div">
                <?php // echo $this->session->flashdata('message'); ?>
            </div>
            <input type="hidden" name="message_hidden_div" id="message_hidden_div" value=""/>
            </form>
            <div class="datagrid-example">
            	<div>
                     <form id="billform" method="post" action="<?php echo site_url('admin/quote/bill'); ?>">
                            <input type="hidden" id="billid" name="billid"/>
                            <input type="hidden" id="billquote" name="billquote" />
                     </form>
                        
                    <form class="form-inline" action="<?php echo site_url('admin/quote/billings') ?>" method="post">
                        Bill#: <input type="text" name="searchinvoicenum" value="<?php echo @$_POST['searchinvoicenum'] ?>"/>
                            &nbsp;&nbsp;
                       From: <input type="text" name="searchfrom" value="<?php if(isset($_POST['searchfrom'])) echo @$_POST['searchfrom']; else echo date('m/d/Y', strtotime("now -30 days") ) ?>" class="datefield" style="width: 70px;"/>
                        &nbsp;&nbsp;
                        To: <input type="text" name="searchto" value="<?php if(isset($_POST['searchto'])) echo @$_POST['searchto']; else echo date('m/d/Y'); ?>" class="datefield" style="width: 70px;"/>
                        &nbsp;&nbsp;
                        Customer:
                        <select id="searchcustomer" name="searchcustomer" style="width: 120px;">
                            <option value=''>All Customers</option>
                            <?php foreach($customers as $cust) { ?>
                                <option value="<?php echo $cust->id ?>"
                                <?php
                                if (@$cust->id == @$_POST['searchcustomer']) {
                                    echo 'SELECTED';
                                }
                                ?>
                                        >
                                <?php echo $cust->name ?>
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
                       <a href="<?php echo site_url('admin/quote/billings'); ?>">
                            <input type="button" value="Show All" class="btn btn-primary"/>
                        </a>
                    </form>
                    <?php if (!@$items) echo '<div class="alert"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Billing Data Found.</div></div>'; ?>
            	</div>
                <div>
                      
                    <table id="datatable" class="table table-bordered">
                    	<thead>
                    		<tr>
                    			<th>Customer</th>
                    			<th>Bill#</th>
                    			<th>Sent Date</th>
                    			<th>Due Date</th>
                    			<th>Total Due</th>
                    			<th>Total Cost</th>
                    			<th>Payment</th>
                    			<!-- <th>Verification</th> -->
                    			<th>Details</th>
                    		</tr>
                    	</thead>
                    	<tbody>
                    		<?php $i=0;
                    		$finaltotal = 0;
                    		$totalpaid= 0;
                    		$totalunpaid= 0;
                    		$future = array();
                    		$current = array();
                    		$daysold60 = array();
                    		$daysold90 = array();
                    		$daysold120 = array();
                    		//echo "<pre>",print_r($items); die;
                    		foreach($items as $item){ $i++;
                    		?>
                    		<tr>
                    			<td><?php echo $item->customername;?></td>
                    			<td id="invoicenumberid_<?php echo $i;?>"><?php echo $item->billname;?></br>
                    		   <!-- <a href="javascript:void(0)" onclick="showreport('<?php echo $item->billname;?>','<?php echo $i;?>');">Expand</a>-->						<input type="hidden" name="invoicenumber_<?php echo $i;?>"" id="invoicenumber_<?php echo $i;?>"" value="<?php echo $item->id;?>"/>
                    			</td>
                    			<td><?php echo date('m/d/Y', strtotime($item->billedon));?></td>
                    			<td><input type="text" id="customerduedate<?php echo $i;?>" name="customerduedate" value="<?php if($item->customerduedate){ echo date("m/d/Y", strtotime($item->customerduedate)); }else{echo "No Date Set";} ;?>" class="datefield" style="width:100px;" data-date-format="mm/dd/yyyy" onchange="changeduedate('<?php echo $i;?>','<?php echo $item->id;?>',this.value)" />
                    			
                    			<input type="hidden" id="originaldate<?php echo $i;?>" value="<?php if($item->customerduedate){ echo date('m/d/Y',strtotime($item->customerduedate)); } ?>" />
                    			
                    			<input type="hidden" id="canceldate" />
                    			</td>
                    			
                    			<td id="total_due_<?php echo $i;?>"><?php echo "$".$item->totaldue;?></td>
                    			
                    			<td id="invoice_paymentamount_<?php echo $i;?>"><?php echo "$".$item->total;?></td>
                    			<td>
                    				<span id="paymentstatus<?php echo $i;?>"><?php echo $item->paymentstatus;?></span>&nbsp;
                    				<?php if($item->status != 'Verified'){?>
                    				<select id="invoice_paymenttype_<?php echo $i;?>" required onchange="paycc(this.value,<?php echo $i;?>,'<?php echo $item->total;?>');">
                    				<option value="">Select Payment Type</option>
                    				<!-- <?php if($item->bankaccount && @$item->bankaccount->routingnumber && @$item->bankaccount->accountnumber){?>
                    				<option <?php echo $item->paymenttype=='Credit Card'?'SELECTED':'';?> value="Credit Card">Credit Card</option>
                    				<?php }?> -->
                    				<option <?php // echo $item->paymenttype=='Cash'?'SELECTED':'';?> value="Cash">Cash</option>
                    				<option <?php // echo $item->paymenttype=='Check'?'SELECTED':'';?> value="Check">Check</option>
                    				</select>
                    				<input type="hidden" id="hiddenpaytype<?php echo $i;?>" name="hiddenpaytype<?php echo $i;?>" value="<?php // echo $item->paymenttype;?>" />
                    				<!-- <input type="text" value="<?php echo $item->paymentstatus=='Paid'?$item->refnum:'';?>" name="refnum" id="refnum_<?php echo $i;?>" onblur="shownotice(this.value, '<?php echo $item->paymentstatus=='Paid'?$item->refnum:'';?>',<?php echo $i;?>);">-->
                    				<input type="text" value="<?php // echo $item->paymentstatus=='Paid'?$item->refnum:'';?>" name="refnum" id="refnum_<?php echo $i;?>">
                    				Total Value Paid? &nbsp;<input type="checkbox" name="ispaid" id="ispaid_<?php echo $i;?>" onclick="setpaidamount('<?php echo $i;?>',this.checked);" <?php if($item->ispaid ==1 ) { ?> checked='checked' <?php } ?> >
                    				$ <input placeholder='Enter Amount' type="text" name="amountpaid" id="amountpaid_<?php echo $i;?>" value="<?php // echo $item->amountpaid;?>" >
                    				<button onclick="update_bill_payment_status('<?php echo $i;?>')">Save</button>
                    				<?php }else{//verified payment, show notes?>
                    				/ <?php echo $item->paymenttype;?> / <?php echo $item->refnum;?>
                    				<?php }?>
                    				<br/><a href="javascript:void(0)" onclick="showhistorymodal('<?php echo $item->id;?>');">view payment history</a>
                    				<?php if($item->paymentstatus=='Requested Payment'){?>
                    				<br/>
                    				<i class="icon-lightbulb">
                    				Payment Requested by
                    				<?php echo (@$item->companydetails->title)?$item->companydetails->title:$item->companydetails->companyname;?>
                    				on <?php echo $item->refnum;?>
                    				</i>
                    				<?php }?>
                    			</td>
                    			<!-- <td>&nbsp;</td> -->
                    			<td><?php echo $item->actions;?></td>
                    		</tr>
                    		<!--<?php
                    		$finaltotal += str_replace( ',', '', $item->total);
                    		
                    		$totalpaid+= str_replace( ',', '', $item->totalpaid);
                    		
                    		/*if($item->paymentstatus=='Unpaid' || $item->paymentstatus=='Requested Payment')
                    		{*/
                    			$item->total = str_replace( ',', '', $item->total );
                    			
                    			$item->totaldue = str_replace( ',', '', $item->totaldue );
                    			
                    			$totalunpaid+= str_replace( ',', '',($item->totaldue));
                    			
                    			$datediff = strtotime($item->customerduedate) - time();
     							$datediff = abs(floor($datediff/(60*60*24)));
                    			if($item->customerduedate>=date('Y-m-d')){                    			
                    				$future[] = ($item->totaldue);
                    			}elseif($datediff>=1 && $datediff<=30){ 
                    				$current[] = ($item->totaldue);
                    			}elseif($datediff>=31 && $datediff<=60){ 
                    				$daysold60[] = ($item->totaldue);
                    			}elseif($datediff>=61 && $datediff<=90){ 
                    				$daysold90[] = ($item->totaldue);
                    			}elseif($datediff>=91 && $datediff<=120){ 
                    				$daysold120[] = ($item->totaldue);
                    			}
                    		//} ?>-->

                    	<?php	}                 		
                    		?>
                    		<tr><td>&nbsp;</td><td>&nbsp;</td><td style="text-align:right;">Total:</td><td><?php echo "$ ".round($finaltotal,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                    		<tr><td>&nbsp;</td><td>&nbsp;</td><td style="text-align:right;">Total Paid:</td><td><?php echo "$ ".round($totalpaid,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                    		<tr><td>&nbsp;</td><td>&nbsp;</td><td style="text-align:right;">Total Due:</td><td><?php echo "$ ".round($totalunpaid,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                    	</tbody>
                    </table>
                </div>
                
                <div>   
			    <!--<?php if(isset($reports)) { foreach ($reports as $report) { $newhtmltable =""; ?>
			    	    
			   <table class="table table-bordered reportdiv<?php echo $report->invoicenum; ?> dclose" style="display:none;">
			     
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
				    	<?php //$totalallprice=""; 
				    	foreach($report->items as $item) {
				    		if($item->invoicenum==$report->invoicenum){
				    		if($item->potype == "Contract" )
				    		$amount = $item->ea;
				    		else 
				    		$amount = $item->quantity * $item->ea; ?>
				    	
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
				    	<?php  }else{
				    		
				    	$newhtmltable .= '<table class="table table-bordered reportdiv'.$item->invoicenum.' dclose" style="display:none;">
			     
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
			    	</tr>';
				    	
				    	if($item->potype == "Contract" )
				    		$amount = $item->ea;
				    		else 
				    		$amount = $item->quantity * $item->ea;
				    	
				    	$newhtmltable .= '<tr>
				    		<td>'.$item->companyname.'</td>
				    		<td>'.$item->ponum.'</td>
				    		<td>'.$item->itemcode.'</td>
				    		<td>'.$item->itemname.'</td>
				    		<td>'.$item->unit.'</td>
				    		<td>'.$item->quantity.'</td>
				    		<td>'.round($item->ea,2).'</td>
				    		<td>$'.round($amount,2).'</td>
				    		<td>'.$item->paymentstatus.'</td>
				    		<td>'.$item->status.'</td>
				    		<td>'.$item->costcode.'</td>			    		
				    	</tr></table>';
				    	
				    		
				    		
				    	} }?> </table>  <?php echo $newhtmltable; } }?>-->
		      
              </div>             
                
              <div>
              <?php if(count($future>0) || count($current>0) || count($daysold60>0) || count($daysold90>0) || count($daysold120>0)){?>
              <span style="text-align:center;"><b>Aging Table</b></span>
              <table class="table table-bordered">
			     
			    	<tr>
			    		<th width="75">Future</th>
			    		<th width="75">Current</th>
			    		<th width="75">30-60</th>
			    		<th width="75">60-90</th>
			    		<th width="75">90-120</th>			    		
			    		<th width="75">Total</th>			    		
			    	</tr>
			    	<tr>
			    		<td><?php echo array_sum($future); ?></td>  
			    		<td><?php echo array_sum($current); ?></td>
			    		<td><?php echo array_sum($daysold60); ?></td>
			    		<td><?php echo array_sum($daysold90); ?></td>
			    		<td><?php echo array_sum($daysold120); ?></td>
			    		<td><?php echo array_sum($future)+array_sum($current)+array_sum($daysold60)+array_sum($daysold90)+array_sum($daysold120); ?></td>
			    	</tr>
			    </table>	
              <?php } ?>
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




<?php foreach($items as $item){ ?>

<div class="modal hide fade" id="billhistorymodal<?php echo $item->id;?>" style="display: none;">
		<div class="modal-header">		
		<h3>Bill History</h3>
		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
    	</div>
          <div class="modal-body">
         <!-- <table>
          <tr>
          <th>Bill#&nbsp;<?php echo @$item->billname;?></th><th>Bill Total&nbsp;<?php echo @$item->total;?></th><th>Still Due&nbsp;<?php echo @$item->totaldue;?></th>
          </tr>          
          </table>-->
          <div>
          <span style="font-weight:bolder;font-size:14px;">Bill#:&nbsp;</span><span><?php echo @$item->billname.",";?></span>&nbsp;
          <span style="font-weight:bolder;font-size:14px;">Bill Total:&nbsp;</span><span><?php echo @$item->total.",";?></span>&nbsp;
          <span style="font-weight:bolder;font-size:14px;">Still Due:&nbsp;</span><span><?php echo @$item->totaldue;?></span>
         
          </div>
          <table>          
          <?php if(@$payment_history){ 
          $htmlbd = "";
          $htmlhd = "";
          foreach ($payment_history as $pay_h){ if($pay_h->bill == $item->id){
          
          	$htmlhd = '<tr>
          <th>Amount Paid</th><th>Payment Type</th><th>Date Paid</th>
          </tr>';
          	
          	$htmlbd .='<tr>
          <td>'.@$pay_h->amountpaid.'</td><td>'.@$pay_h->paymenttype.'</td><td>'.@$pay_h->paymentdate.'</td>
          </tr>';
           } } 
          }else{ 
          $htmlbd = "<tr><td>No Payment History Available</td></tr>";
          }
          
          echo $htmlhd."".$htmlbd;     ?>     
          
          </table>
          
          </div>
      </div>
     

<?php } ?>