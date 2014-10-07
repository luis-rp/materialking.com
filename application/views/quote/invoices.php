<?php echo '<script>var datedueurl="' . site_url('quote/invoicedatedue') . '";</script>' ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>

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
var datetext = "";
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
}
function invoice(invoicenum)
{
	$("#invoicenum").val(invoicenum);
	$("#invoiceform").submit();
}


</script>

    <div class="content">
    	 <?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">

			<h3>Invoices <a href="<?php echo site_url('quote/invoices_export'); ?>" class="btn btn-primary btn-xs btn-mini">Export</a> &nbsp;&nbsp; <a href="<?php echo site_url('quote/invoices_pdf'); ?>" class="btn btn-primary btn-xs btn-mini">View PDF</a></h3>
		</div>

	   <div id="container">
	   		<div class="combofixed" id="tablebox" style="position:relative; padding:2% 2% 0% 0%; margin:0px; width:100%; background:#FFF">

				<form method="post" class="form-inline"  action="<?php echo site_url('quote/invoices') ?>">
				<table cellpadding="0">
				<tr>
				<td class="tablebox">From Date:</td>

				<td class="tablebox">To:</td>

				<td class="tablebox">Status:</td>


	                 <td class="tablebox">&nbsp;Company:</td>


	                 <td class="tablebox">&nbsp;Payment:</td>


	             <td class="tablebox">&nbsp;Keyword:</td>


				</tr>
				<tr>

				<td class="tablebox"><input type="text" style="width:110px" name="searchfrom" value="<?php echo @$_POST['searchfrom']?>" class="date"/></td>

				<td class="tablebox"><input type="text" style="width:110px" name="searchto" value="<?php echo @$_POST['searchto']?>" class="date"/> </td>

				<td class="tablebox">
				<select name="searchstatus" class="form-control selectpicker show-tick" style="width:140px">
                            	<option value=''>All</option>
                            	<option value='Pending' <?php if(@$_POST['searchstatus'] =='Pending'){echo 'SELECTED';}?>>Pending</option>
                            	<option value='Verified' <?php if(@$_POST['searchstatus'] =='Verified'){echo 'SELECTED';}?>>Verified</option>
                            	<option value='Error' <?php if(@$_POST['searchstatus'] =='Error'){echo 'SELECTED';}?>>Error</option>
	                        </select>
	                 </td>

	             <td class="tablebox">
                         	<select name="searchpurchasingadmin" class="form-control selectpicker show-tick"  style="width:120px;">
                            	<option value=''>All</option>
                            	<?php foreach($purchasingadmins as $pa){?>
                            	<option value='<?php echo $pa->id;?>' <?php if(@$_POST['searchpurchasingadmin'] ==$pa->id){echo 'SELECTED';}?>><?php echo $pa->fullname;?></option>
                            	<?php }?>
                            </select>
	                 </td>


	             <td class="tablebox">
                        <select id="searchpaymentstatus" name="searchpaymentstatus" class="form-control selectpicker show-tick" style="width: 130px;">
                            <option value=''>All</option>
                            <option value="Paid" <?php if (@$_POST['searchpaymentstatus'] == 'Paid') { echo 'SELECTED'; } ?>>Paid</option>
                           <option value="Requested Payment" <?php if (@$_POST['searchpaymentstatus'] == 'Requested Payment') { echo 'SELECTED'; } ?>>Requested Payment</option>
                            <option value="Unpaid" <?php if (@$_POST['searchpaymentstatus'] == 'Unpaid') { echo 'SELECTED'; } ?>>Unpaid</option>
                        </select>
	                </td>


	             <td class="tablebox"><input style="width:120px" type="text" name="searchkeyword" value="<?php echo @$_POST['searchkeyword']?>"/></td>


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
                </form>
                    <div class="col-md-12">
                        <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                            </div>

                            <div class="grid-body no-border">

                                    <table id="datatable" class="table no-more-tables general">
                                    <thead>
                                       <tr>
                  	             			<th style="width:20%">PO Number</th>
                                   			<th>Invoice#</th>
                                   			<th>Received On</th>
                                            <th>Total Cost</th>
                                            <th>Payment Status</th>
                                            <th>Verification</th>
                                            <th>Date Due</th>
                                         </tr>
									</thead>
									<tbody>
							              <?php
							              $finaltotal = 0;
							              $totalpaid= 0;
							              $totalunpaid= 0;
									    	foreach($invoices as $ponum=>$invs)
									    	{
									      			foreach($invs as $i){?>
                                                		<tr>
                                                			<td class="v-align-middle"><?php echo $ponum;?> </td>
                                                			<td>
                                                			<a href="javascript:void(0)" onclick="invoice('<?php echo $i->invoicenum;?>');">
                                                			<?php echo $i->invoicenum;?>
                                                			</a>
                                                			</td>
                                                			<td><?php echo date('m/d/Y', strtotime($i->receiveddate));?></td>
                                                			<td>$<?php echo $i->totalprice;?></td>
                                                			<td><?php echo $i->paymentstatus;?><br>
                                                			<?php if($i->paymentstatus=='Paid') { $olddate=strtotime($i->paymentdate); $newdate = date('m/d/Y', $olddate); echo $newdate; }?></td>
                                                			<td><?php echo $i->status;?></td>
                                                			<td>
                                                			<input type="text" value="<?php if($i->datedue){ echo date("m/d/Y", strtotime($i->datedue)); }else{echo "No Date Set";} ;?>" class="date" style="width:100px;"
                                                			data-date-format="mm/dd/yyyy" readonly
                                                			onchange="changeduedate('<?php echo $i->invoicenum;?>',this.value)"/>
                                                			</td>
                                                		</tr>
                                                		<?php $finaltotal += $i->totalprice;
																if($i->paymentstatus=='Paid')
									    							{
									    							$totalpaid+= $i->totalprice;
									    							}

									    						if($i->paymentstatus=='Unpaid')
									    							{
									    							$totalunpaid+= $i->totalprice;
									    							}
     								      								}?>
                                                <?php } ?>
                                                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align="right">Total:</td>
                                                <td><?php echo "$ ".round($finaltotal,2);?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align="right">Total Paid:</td>
                                                <td><?php echo "$ ".round($totalpaid,2);?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align="right">Total Due:</td>
                                                <td><?php echo "$ ".round($totalunpaid,2);?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                        </tbody>
                                    </table>
                            </div>
                        </div>
                    </div>
                </div>

                <?php } else {?>
       				<span style="display: block;position:absolute;z-index:9999;margin-top:10px; margin-left:30px;" class="label label-important">No Invoice Detected.</span>
                <?php }?>

		</div>
	  </div>