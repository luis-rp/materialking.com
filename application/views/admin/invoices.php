<?php echo '<script>var sharewithsupplierurl="'.site_url('admin/quote/updatesharesuplliercheck').'";</script>'?>
 
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
        var invoice_number = $('#invoicenumber_' + idnumber).val();
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

function upload_attachment(receivedid,invoicenum)
{
	$("#hidreceivedid").val(receivedid);
	$("#hidinvoicenum").val(invoicenum);
	$("#frmInvoice").attr('action','<?php echo site_url('admin/quote/uploadPaymentAttachment') ?>');
	$("#frmInvoice").submit();
}

 function updatesharewithSupplier(receivedid,invoicenum)
	{		
		if($('#sharewithsupplier_'+receivedid).attr('checked')) {			
			sharewithsupplier = 1;
		}
		else
		{
			sharewithsupplier = 0;
		}
		var data = "receivedid="+receivedid+"&invoicenum="+invoicenum+"&sharewithsupplier="+sharewithsupplier;
		
		$.ajax({
			type:"post",
			data: data,
			url: sharewithsupplierurl
		}).done(function(data){
			if(sharewithsupplier == 1)
			{
				alert("Attachment shared with supplier.");
			}	
		});
	}

function jq( myid ) {
 
    return "." + myid.replace( /(:|\.|\[|\])/g, "\\$1" );
 
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
                        Company:<?php 
                      //  $newCompanylist = '';
                                               
                        if(@$companylist1 && $companylist1 != '')
                        {
                        	foreach ($companylist1 as $key=>$val)
                        	{                        		
                        		if(@$val && $val != '')
                        		{                        	
                        			$newCompanylist[$val->id] = $val; 
                        		}	
                        	}
                        }
                       
                        ?>
                        
                        
                        <select id="searchcompany" name="searchbycompany" style="width: 120px;">
                            <option value=''>All Companies</option>
                            <?php
							if(@$newCompanylist)
							{
                            foreach ($newCompanylist as $company) { ?>
                                <option value="<?php echo $company->id ?>"
                                <?php
                                if (@$_POST['searchbycompany'] == $company->id) {
                                    echo 'SELECTED';
                                }
                                ?>
                                        >
                                <?php echo $company->title ?>
                                </option>
						<?php } } ?>
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
                    <?php if (!@$items) echo '<div class="alert"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Invoices Found.</div></div>'; ?>
            	</div>
                <div>         
                 <form name="frmInvoice" id="frmInvoice" enctype="multipart/form-data" method="POST">
                 <input type="hidden" id="hidinvoicenum" name="hidinvoicenum" value="">
                 <input type="hidden" id="hidreceivedid" name="hidreceivedid" value="">
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
                    		$future = array();
                    		$current = array();
                    		$daysold60 = array();
                    		$daysold90 = array();
                    		$daysold120 = array();
                    		/*echo "<pre>",print_r($items);
                    		echo "<pre>",print_r($aginginvoices); die;*/
                    		foreach($items as $item){ $i++;
                    		?>
                    		<tr style="background-color:<?php if($item->paymentstatus=='Paid' && $item->status=='Verified') { echo "#ADEBAD"; } elseif($item->paymentstatus=='Unpaid' && $item->status=='Pending' && strtotime(date('m/d/Y')) > strtotime(date("m/d/Y", strtotime($item->datedue)))) { echo "#FF8080"; } elseif($item->paymentstatus=='Paid' && $item->status=='Pending') { echo "#FFDB99";} elseif($item->paymentstatus=='Unpaid'  && $item->status=='Pending'){ echo "pink";} 
 elseif($item->paymentstatus=='Requested Payment' && $item->status=='Pending' && strtotime(date('m/d/Y')) > strtotime(date("m/d/Y", strtotime($item->datedue)))) { echo "#FF8080"; }?>">
                    			<td><?php echo $item->ponum;?></td>
                    			<td id="invoicenumberid_<?php echo $i;?>"><?php echo $item->invoicenum;?></br>
                    		    <a href="javascript:void(0)" onclick="showreport('<?php echo $item->invoicenum;?>','<?php echo $i;?>');">Expand</a> &nbsp; <?php if (strpos(@$item->invoicenum,'paid-in-full-already') !== false) {  echo '<br>*Pre-Paid-Order'; }?>						<input type="hidden" name="invoicenumber_<?php echo $i;?>"" id="invoicenumber_<?php echo $i;?>"" value="<?php echo $item->invoicenum;?>"/>
                    			</td>
                    			<td><?php if(@$item->receiveddate) echo date('m/d/Y', strtotime($item->receiveddate));?>
                    			 &nbsp; <?php if (strpos(@$item->invoicenum,'paid-in-full-already') !== false) {  echo '<br>*Pre-Paid-Order'; }?>
                    			</td>
                    			<?php //if(isset($item->quote->duedate) && $item->quote->duedate!="") { echo $item->quote->duedate; } else echo "";?>                                
                    			<td><?php if($item->datedue) { echo date("m/d/Y", strtotime($item->datedue));  } else{ echo "No Date Set";}?>
                    			 &nbsp; <?php if (strpos(@$item->invoicenum,'paid-in-full-already') !== false) {  echo '<br>*Pre-Paid-Order'; }?>
                    			</td>
                    			<td id="invoice_paymentamount_<?php echo $i;?>"><?php echo "$".$item->totalprice;?>
                    			 &nbsp; <?php if (strpos(@$item->invoicenum,'paid-in-full-already') !== false) {  echo '<br>*Pre-Paid-Order'; }?>
                    			</td>
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
                    				<!-- <input type="text" value="<?php echo $item->paymentstatus=='Paid'?$item->refnum:'';?>" name="refnum" id="refnum_<?php echo $i;?>" onblur="shownotice(this.value, '<?php echo $item->paymentstatus=='Paid'?$item->refnum:'';?>',<?php echo $i;?>);">-->
                    				<input type="text" value="<?php echo $item->paymentstatus=='Paid'?$item->refnum:'';?>" name="refnum" id="refnum_<?php echo $i;?>">
                    				<button onclick="update_invoice_payment_status('<?php echo $i;?>')">Save</button>
                    				<input style="width:100px;color: transparent;" type="file" name="UploadFile[<?php echo $item->receivedid;?>]" ><?php if(@$item->attachment) echo $item->attachment; else echo ''; ?>
                    				<input type="button" name="btnupload" id="btnupload" value="Upload Attachment" onclick="upload_attachment('<?php echo $item->receivedid;?>','<?php echo $item->invoicenum;?>')">
                    				<input type="checkbox" name="sharewithsupplier_<?php echo $item->receivedid;?>" id="sharewithsupplier_<?php echo $item->receivedid;?>" <?php if(@$item->sharewithsupplier && $item->sharewithsupplier == 1) echo ' checked '; else echo '';?>  onclick="updatesharewithSupplier('<?php echo $item->receivedid;?>','<?php echo $item->invoicenum;?>')">Share with Supplier
                    				<?php }else{//verified payment, show notes?>
                    				/ <?php echo $item->paymenttype;?> / <?php echo $item->refnum;?>
                    				<input style="width:100px;color: transparent;" type="file" name="UploadFile[<?php echo $item->receivedid;?>]"> <?php if(@$item->attachment) echo $item->attachment; else echo ''; ?>
                    				<input type="button" name="btnupload" id="btnupload" value="Upload Attachment" onclick="upload_attachment('<?php echo $item->receivedid;?>','<?php echo $item->invoicenum;?>')">
                    				<input type="checkbox" name="sharewithsupplier_<?php echo $item->receivedid;?>" id="sharewithsupplier_<?php echo $item->receivedid;?>" onclick="updatesharewithSupplier('<?php echo $item->receivedid;?>','<?php echo $item->invoicenum;?>')" <?php if(@$item->sharewithsupplier && $item->sharewithsupplier == 1) echo ' checked '; else echo '';?>  >Share with Supplier 
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
                    		$finaltotal += str_replace( ',', '', $item->totalprice);
                    		if($item->paymentstatus=='Paid')
                    		{
                    			$totalpaid+= str_replace( ',', '', $item->totalprice);
                    		}

                    		if($item->paymentstatus=='Unpaid' || $item->paymentstatus=='Requested Payment')
                    		{
                    			$item->totalprice = str_replace( ',', '', $item->totalprice );
                    			$totalunpaid+= $item->totalprice;
                    			
                    			/*$datediff = strtotime($item->datedue) - time();
     							$datediff = abs(floor($datediff/(60*60*24)));
                    			if($item->datedue>=date('Y-m-d')){                    			
                    				$future[] = $item->totalprice;
                    			}elseif($datediff>=1 && $datediff<=30){ 
                    				$current[] = $item->totalprice;
                    			}elseif($datediff>=31 && $datediff<=60){ 
                    				$daysold60[] = $item->totalprice;
                    			}elseif($datediff>=61 && $datediff<=90){ 
                    				$daysold90[] = $item->totalprice;
                    			}elseif($datediff>=91 && $datediff<=120){ 
                    				$daysold120[] = $item->totalprice;
                    			}*/
                    		}

                    		}
                    		
                    		foreach($aginginvoices as $ainvoice){
								  
                    			if($ainvoice->paymentstatus=='Unpaid' || $ainvoice->paymentstatus=='Requested Payment')
                    			{  
                    				if(@$taxpercent)
                    				$ainvoice->totalprice = $ainvoice->totalprice + ($ainvoice->totalprice*$taxpercent/100);
                    				else 
                    				$ainvoice->totalprice = $ainvoice->totalprice;
                    				
                    				$datediff = strtotime($ainvoice->datedue) - time();
                    				$datediff = abs(floor($datediff/(60*60*24)));
                    				if($ainvoice->datedue>=date('Y-m-d') || $ainvoice->datedue==""){
                    					$future[] = $ainvoice->totalprice;
                    				}elseif($datediff>=1 && $datediff<=30){
                    					$current[] = $ainvoice->totalprice;
                    				}elseif($datediff>=31 && $datediff<=60){
                    					$daysold60[] = $ainvoice->totalprice;
                    				}elseif($datediff>=61 && $datediff<=90){
                    					$daysold90[] = $ainvoice->totalprice;
                    				}elseif($datediff>=91 && $datediff<=120){
                    					$daysold120[] = $ainvoice->totalprice;
                    				}
                    			}
                    			
                    		}
                    		                 		
                    		?>
                    		<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td style="text-align:right;">Total:</td><td><?php echo "$ ".round($finaltotal,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                    		<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td style="text-align:right;">Total Paid:</td><td><?php echo "$ ".round($totalpaid,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                    		<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td style="text-align:right;">Total Due:</td><td><?php echo "$ ".round($totalunpaid,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                    	</tbody>
                    </table>
                </div>
            </form>    
                <div>   
			    <?php if(isset($reports)) { foreach ($reports as $report) { $newhtmltable =""; ?>
			    	    
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
				    		$amount = (($item->invoice_type != "fullpaid")?($item->invoice_type == "alreadypay"?0:$item->quantity):$item->aiquantity) * $item->ea; 
				    		if(@$taxpercent)
                    				$amount = $amount + ($amount*$taxpercent/100);
				    		
				    		?>
				    	
				    	<tr>
				    		<td><?php echo $item->companyname;?></td>
				    		<td><?php echo $item->ponum;?></td>
				    		<td><a href="<?php echo site_url("site/item/".$item->itemurl);?>" target="_blank"> <?php echo $item->itemcode;?></a></td>
				    		<td><?php echo $item->itemname;?></td>
				    		<td><?php echo $item->unit;?></td>
				    		<td><?php echo ($item->invoice_type != "fullpaid")?$item->quantity:$item->aiquantity;?>
				    		<?php if(strpos(@$item->invoicenum,"paid-in-full-already") !== false) echo "<br>*Pre-Paid Order"; else echo "";?>
				    		</td>
				    		<td>$<?php echo round($item->ea,2);?></td>
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
				    		$amount = (($item->invoice_type != "fullpaid")?($item->invoice_type == "alreadypay"?0:$item->quantity):$item->aiquantity) * $item->ea;
				    	
				    	$newhtmltable .= '<tr>
				    		<td>'.$item->companyname.'</td>
				    		<td>'.$item->ponum.'</td>
				    		<td>'.$item->itemcode.'</td>
				    		<td>'.$item->itemname.'</td>
				    		<td>'.$item->unit.'</td>
				    		<td>'.($item->invoice_type != "fullpaid")?$item->quantity:$item->aiquantity.'</td>
				    		<td>'.round($item->ea,2).'</td>
				    		<td>$'.round($amount,2).'</td>
				    		<td>'.$item->paymentstatus.'</td>
				    		<td>'.$item->status.'</td>
				    		<td>'.$item->costcode.'</td>			    		
				    	</tr></table>';
				    	
				    		
				    		
				    	} }?> </table>  <?php echo $newhtmltable; } }?>
		      
              </div>             
                
              <div>
              <?php if(count($future>0) || count($current>0) || count($daysold60>0) || count($daysold90>0) || count($daysold120>0)){?>
              <span style="text-align:center;"><b>A/R Aging Table</b></span>
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
			    		<td><?php echo number_format(array_sum($future),2); ?></td>  
			    		<td><?php echo number_format(array_sum($current),2); ?></td>
			    		<td><?php echo number_format(array_sum($daysold60),2); ?></td>
			    		<td><?php echo number_format(array_sum($daysold90),2); ?></td>
			    		<td><?php echo number_format(array_sum($daysold120),2); ?></td>
			    		<td><?php echo number_format(array_sum($future)+array_sum($current)+array_sum($daysold60)+array_sum($daysold90)+array_sum($daysold120),2); ?></td>
			    	</tr>
              <?php } ?>
              </div>  
                
            </div>
        </div>
         <?php if(@$items) { ?> 
			   <table style="width:24%;margin-left:74%;">
                <thead>
                   <tr>
	             			<th style="width:3%;padding: 0px;">Color</th>
	             			<th style="padding: 0px;text-align:center;">Description</th>
                     </tr>
				</thead>
				<tbody>
                     <tr >
	             			<td style="background-color:#ADEBAD;width:3%;padding: 0px;">&nbsp;</td>
	             			<td style="padding: 0px;">Payment=Paid and Verification=Verified</td>
                     </tr>
                      <tr>
	             			<td style="background-color:#FF8080;width:5%;padding: 0px;">&nbsp;</td>
	             			<td style="padding: 0px;">Payment=Unpaid/Requested Payment, Verification=Pending and Due Date is Past</td>
                     </tr>
                      <tr>
	             			<td style="background-color:#FFDB99;width:5%;padding: 0px;">&nbsp;</td>
	             			<td style="padding: 0px;">Payment=Paid and Verification=Pending</td>
                     </tr>
                     <tr>
	             			<td style="background-color:pink;width:5%;padding: 0px;">&nbsp;</td>
	             			<td style="padding: 0px;">Payment=Unpaid and Verification=Pending</td>
                     </tr>                                                                               
                       </tbody>
                </table>
               <br>
       <?php } ?> 
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