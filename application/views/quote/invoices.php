<?php echo '<script>var datedueurl="' . site_url('quote/invoicedatedue') . '";</script>' ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>

 <script type="text/javascript">
$.noConflict();
 </script>
<script type="text/javascript">

$(document).ready(function(){
	$('.date').datepicker();

	$('#datatable').dataTable( {
		"aaSorting": [],
		"sPaginationType": "full_numbers",
		"aoColumns": [
		        		null,
		        		null,
		        		null,
		        		null,
		        		null,
		        		null,
		        		null
			]
		} );
	 $('.dataTables_length').hide();

});

/*var datetext = "";
function changeduedate(invoicenum,datedue)
{
	if(datetext!= datedue) {
	datetext = datedue;
	var data = "invoicenum="+invoicenum+"&datedue="+datedue;
	$.ajax({
        type: "post",
        data: data,
        url: datedueurl
    }).done(function(data) {
    });

	}
}*/

	var datetext = "";
	var isconfirm = "";
	function changeduedate(invoicenum,datedue)
	{			
		if(datetext!= datedue) {
			if(confirm("Do you want to set the invoice due date to"+datedue)){
			datetext = datedue;
			isconfirm = "yes";
			$('#originaldate'+invoicenum).val(datedue);
			var data = "invoicenum="+invoicenum+"&datedue="+datedue;
			$.ajax({
				type: "post",
				data: data,
				url: datedueurl
			}).done(function(data) {
			});

		}else{
				$('#daterequested'+invoicenum).val($('#originaldate'+invoicenum).val());
				datetext = $('#originaldate'+invoicenum).val();			
				$('#canceldate').val(datedue);
				datedue = $('#originaldate'+invoicenum).val();			
		}
		}else{ 
				if(isconfirm == ""){
				$('#daterequested'+invoicenum).val($('#originaldate'+invoicenum).val());				
				datetext = $('#canceldate').val();									
				}
				
		}
	}

function invoice(invoicenum,invoicequote)
{
	$("#invoicenum").val(invoicenum);
	$("#invoicequote").val(invoicequote);	
	$("#invoiceform").submit();
}


</script>

    <div class="content">
    	 <?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">

			<h3>Invoices <a href="<?php echo site_url('quote/invoices_export'); ?>" class="btn btn-primary btn-xs btn-mini">Export</a> &nbsp;&nbsp; <a href="<?php echo site_url('quote/invoices_pdf'); ?>" class="btn btn-primary btn-xs btn-mini">View PDF</a></h3>
		</div>

	   <div id="container">
	   		<div class="combofixed" id="tablebox" style="position:relative; padding:2% 2% 0% 0%; margin:0px; width:100%; background:#FFF;z-index:1;">

				<form method="post" class="form-inline"  action="<?php echo site_url('quote/invoices') ?>">
				<table cellpadding="0" style="margin-top:40px;width:100%;word-wrap:break-word;">
				<tr>
				<td class="tablebox" style="padding: 10px 4px !important;">From Date:</td>

				<td class="tablebox" style="padding: 10px 4px !important;">To:</td>

				<td class="tablebox" style="padding: 10px 4px !important;">Status:</td>


	                 <td class="tablebox" style="padding: 10px 4px !important;">&nbsp;Company:</td>


	                 <td class="tablebox" style="padding: 10px 4px !important;">&nbsp;Payment:</td>


	             <td class="tablebox" style="padding: 10px 4px !important;">&nbsp;Keyword:</td>


				</tr>
				<tr>

				<td class="tablebox" style="padding: 10px 4px !important;"><input type="text" style="width:110px" name="searchfrom" value="<?php echo @$_POST['searchfrom']?>" class="date"/></td>

				<td class="tablebox" style="padding: 10px 4px !important;"><input type="text" style="width:110px" name="searchto" value="<?php echo @$_POST['searchto']?>" class="date"/> </td>

				<td class="tablebox" style="padding: 10px 4px !important;">
				<select name="searchstatus" class="form-control selectpicker show-tick" style="width:140px">
                            	<option value=''>All</option>
                            	<option value='Pending' <?php if(@$_POST['searchstatus'] =='Pending'){echo 'SELECTED';}?>>Pending</option>
                            	<option value='Verified' <?php if(@$_POST['searchstatus'] =='Verified'){echo 'SELECTED';}?>>Verified</option>
                            	<option value='Error' <?php if(@$_POST['searchstatus'] =='Error'){echo 'SELECTED';}?>>Error</option>
                            	<option value='pastdue' <?php if(@$_POST['searchstatus'] =='pastdue'){echo 'SELECTED';}?>>Past Due</option>
	                        </select>
	                 </td>

	             <td class="tablebox" style="padding: 10px 4px !important;">
                         	<select name="searchpurchasingadmin" class="form-control selectpicker show-tick"  style="width:120px;">
                            	<option value=''>All</option>
                            	<?php foreach($purchasingadmins as $pa){?>
                            	<option value='<?php echo $pa->id;?>' <?php if(@$_POST['searchpurchasingadmin'] ==$pa->id){echo 'SELECTED';}?>><?php echo $pa->companyname;?></option>
                            	<?php }?>
                            </select>
	                 </td>


	             <td class="tablebox" style="padding: 10px 4px !important;">
                        <select id="searchpaymentstatus" name="searchpaymentstatus" class="form-control selectpicker show-tick" style="width: 130px;">
                            <option value=''>All</option>
                            <option value="Paid" <?php if (@$_POST['searchpaymentstatus'] == 'Paid') { echo 'SELECTED'; } ?>>Paid</option>
                           <option value="Requested Payment" <?php if (@$_POST['searchpaymentstatus'] == 'Requested Payment') { echo 'SELECTED'; } ?>>Requested Payment</option>
                            <option value="Unpaid" <?php if (@$_POST['searchpaymentstatus'] == 'Unpaid') { echo 'SELECTED'; } ?>>Unpaid</option>
                        </select>
	                </td>


	             <td class="tablebox" style="padding: 10px 4px !important;"><input style="width:120px" type="text" name="searchkeyword" value="<?php echo @$_POST['searchkeyword']?>"/></td>


				</tr>
				<tr>
                <td colspan="13"><button class="btn btn-success btn-cons" type="submit">Filter</button></td></tr>
				</table>

               </form>
        </div>

		<?php
		    	if($invoices)
		    	{
		    ?>
		<div class="row">
				<form id="invoiceform" method="post" action="<?php echo site_url('quote/invoice');?>">
                	<input type="hidden" id="invoicenum" name="invoicenum"/>
                	<input type="hidden" id="invoicequote" name="invoicequote"/>
                	
                </form>
                    <div class="col-md-12">
                        <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                            </div>

                            <div class="grid-body no-border">                                 
                                    <table id="datatable" class="table no-more-tables general" style="margin-top:40px;table-layout: fixed;width:100%;word-wrap:break-word;">
                                    <thead>
                                       <tr>
                  	             			<th style="padding: 10px 4px !important;width:15%">PO Number</th>
                  	             			<th style="padding: 10px 4px !important;width:13%">Company</th>
                                   			<th style="padding: 10px 4px !important;width:10%">Invoice#</th>
                                   			<th style="padding: 10px 4px !important;width:10%">Received On</th>
                                            <th style="padding: 10px 4px !important;width:10%">Total Cost</th>
                                            <th style="padding: 10px 4px !important;width:10%">Payment Status</th>
                                            <th style="padding: 10px 4px !important;width:10%">Verification</th>
                                            <th style="padding: 10px 4px !important;width:12%">Date Due</th>
                                            <th style="padding: 10px 4px !important;width:10%">Attachment</th>
                                         </tr>
									</thead>
									<tbody>
							              <?php
							              $finaltotal = 0;
							              $totalpaid= 0;
							              $totalunpaid= 0;
									    	foreach($invoices as $ponum=>$invs)  
									    	{ 
     								      			foreach($invs as $i){  ?>
                                                		<tr style="background-color:<?php if($i->paymentstatus=='Paid' && $i->status=='Verified') { echo "#ADEBAD"; } elseif($i->paymentstatus=='Unpaid' && $i->status=='Pending' && strtotime(date('m/d/Y')) > strtotime(date("m/d/Y", strtotime($i->datedue)))) { echo "#FF8080"; } elseif($i->paymentstatus=='Paid' && $i->status=='Pending') { echo "#FFDB99";} elseif($i->paymentstatus=='Unpaid'  && $i->status=='Pending'){ echo "pink";} 
 elseif($i->paymentstatus=='Requested Payment' && $i->status=='Pending' && strtotime(date('m/d/Y')) > strtotime(date("m/d/Y", strtotime($i->datedue)))) { echo "#FF8080"; }?>">
                                                			<td class="v-align-middle" style="padding: 10px 4px !important;"><?php echo $ponum;?> </td>
                                                			<td class="v-align-middle" style="padding: 10px 4px !important;"><?php echo $i->quote->companyname;?> </td>
                                                			<td style="padding: 10px 4px !important;">
                                                			<a href="javascript:void(0)" onclick="invoice('<?php echo $i->invoicenum;?>','<?php echo $i->quote->id;?>');">
                                                			<?php echo $i->invoicenum;?>
                                                			</a>
                                                			</td>
                                                			<td style="padding: 10px 4px !important;"><?php if(@$i->receiveddate) echo date('m/d/Y', strtotime($i->receiveddate));?></td>
                               <td>$<?php $gtotal= $i->totalprice;

                               if(@$i->discount_percent){

                               	$gtotal = $gtotal - ($gtotal*$i->discount_percent/100);
                               }

                               if(@$i->penalty_percent){

                               	$gtotal = $gtotal + (($gtotal*$i->penalty_percent/100)*$i->penaltycount);
                               }
                               
                               $gtotal=(($gtotal*$i->taxrate)/100)+$gtotal;
                               
                               echo number_format($gtotal,2); ?></td>
                               
                                                			<td style="padding: 10px 4px !important;"><?php echo $i->paymentstatus;?><br>
                                                			<?php if($i->paymentstatus=='Paid') { $olddate=strtotime($i->paymentdate); $newdate = date('m/d/Y', $olddate); echo $newdate; }?></td>
                                                			<td style="padding: 10px 4px !important;"><?php echo $i->status;?></td>
                                                			<td style="padding: 10px 4px !important;">
                                    			<input type="text" id="daterequested<?php echo $i->invoicenum;?>" value="<?php if($i->datedue){ echo date("m/d/Y", strtotime($i->datedue)); }else{ echo "No Date Set";} ?>" class="date" style="width:90px;"
                                                			data-date-format="mm/dd/yyyy" readonly
                                                			onchange="changeduedate('<?php echo $i->invoicenum;?>',this.value)"/>
                                                			<input type="hidden" id="originaldate<?php echo $i->invoicenum;?>" value="<?php if($i->datedue){ echo date('m/d/Y',strtotime($i->datedue)); } ?>" />
                                                			</td>
                                                			<td style="padding: 10px 4px !important;"><?php if(@$i->sharewithsupplier && $i->sharewithsupplier == 1) { 
											        if(@$i->attachmentname)
											        { ?>
											        	<a href="<?php echo site_url('uploads/invoiceattachments/'.$i->attachmentname);?>" target="_blank">View Attached File</a>
											     <?php   }  } ?> </td>
                                                		</tr>
                                                		<?php $finaltotal += $gtotal;
																if($i->paymentstatus=='Paid')
									    							{
									    							$totalpaid+= $gtotal;
									    							}

									    						if($i->paymentstatus=='Unpaid' || $i->paymentstatus=='Requested Payment' || $i->paymentstatus=='Credit')
									    							{
									    							$totalunpaid+= $gtotal;
									    							}
     								      								}   ?>
                                                <?php } ?> <input type="hidden" id="canceldate" />
                                                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align="right">Total:</td>
                                                <td><?php echo "$ ".round($finaltotal,2);?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align="right">Total Paid:</td>
                                                <td><?php echo "$ ".round($totalpaid,2);?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align="right">Total Due:</td>
                                                <td><?php echo "$ ".round($totalunpaid,2);?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                        </tbody>
                                    </table>
                                    <br />
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
                  	             			<td style="padding: 0px;">Payment Stauts=Paid and Status=Verified</td>
                                         </tr>
                                          <tr>
                  	             			<td style="background-color:#FF8080;width:5%;padding: 0px;">&nbsp;</td>
                  	             			<td style="padding: 0px;">Payment Stauts=Unpaid/Requested Payment, Status=Pending and Due Date is Past</td>
                                         </tr>
                                          <tr>
                  	             			<td style="background-color:#FFDB99;width:5%;padding: 0px;">&nbsp;</td>
                  	             			<td style="padding: 0px;">Payment Stauts=Paid and Status=Pending</td>
                                         </tr>
                                         <tr>
                  	             			<td style="background-color:pink;width:5%;padding: 0px;">&nbsp;</td>
                  	             			<td style="padding: 0px;">Payment Stauts=Unpaid and Status=Pending</td>
                                         </tr>                                                                               
                                           </tbody>
                                    </table>
                            </div>
                        </div>
                    </div>
                </div>

                <?php } else {?>
                <br>
       		<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#"></a><div class="msgBox">No Invoice Detected</div></div>
                <?php }?>

		</div>
	  </div>