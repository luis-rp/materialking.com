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
			<h3>Invoices</h3>		
		</div>
	
	   <div id="container">
	   		<div class="combofixed">
	   
				<form method="post" class="form-inline"  action="<?php echo site_url('quote/invoices') ?>">
				<table cellpadding="2">
				<tr>
				<td>From Date:</td>
				<td><input type="text" style="width:100px" name="searchfrom" value="<?php echo @$_POST['searchfrom']?>" class="date"/></td>
				<td>To:</td>
				<td><input type="text" style="width:100px" name="searchto" value="<?php echo @$_POST['searchto']?>" class="date"/> </td>
				<td>Status:</td>
				<td>
				<select name="searchstatus" class="form-control selectpicker show-tick" style="width:auto">
                            	<option value=''>All</option>
                            	<option value='Pending' <?php if(@$_POST['searchstatus'] =='Pending'){echo 'SELECTED';}?>>Pending</option>
                            	<option value='Verified' <?php if(@$_POST['searchstatus'] =='Verified'){echo 'SELECTED';}?>>Verified</option>
                            	<option value='Error' <?php if(@$_POST['searchstatus'] =='Error'){echo 'SELECTED';}?>>Error</option>
	                        </select>
	                 </td>
	                 
	                 <td>&nbsp;Company:</td>
	                 <td>
                         	<select name="searchpurchasingadmin" class="form-control selectpicker show-tick"  style="width: 140px;">
                            	<option value=''>All</option>
                            	<?php foreach($purchasingadmins as $pa){?>
                            	<option value='<?php echo $pa->id;?>' <?php if(@$_POST['searchpurchasingadmin'] ==$pa->id){echo 'SELECTED';}?>><?php echo $pa->fullname;?></option>
                            	<?php }?>
                            </select>
	                 </td>
	             
	                 <td>&nbsp;Payment:</td>
	             	<td>
                        <select id="searchpaymentstatus" name="searchpaymentstatus" class="form-control selectpicker show-tick" style="width: 140px;">
                            <option value=''>All</option>
                            <option value="Paid" <?php if (@$_POST['searchpaymentstatus'] == 'Paid') { echo 'SELECTED'; } ?>>Paid</option>
                           <option value="Requested Payment" <?php if (@$_POST['searchpaymentstatus'] == 'Requested Payment') { echo 'SELECTED'; } ?>>Requested Payment</option>
                            <option value="Unpaid" <?php if (@$_POST['searchpaymentstatus'] == 'Unpaid') { echo 'SELECTED'; } ?>>Unpaid</option>
                        </select>    
	                </td>
	                 
	             <td>&nbsp;Keyword:</td>
	             <td><input style="width:100px" type="text" name="searchkeyword" value="<?php echo @$_POST['searchkeyword']?>"/></td>
	             
	             <td><button class="btn btn-success btn-cons" type="submit">Filter</button></td>
				</tr>
				
				<tr><td colspan="14">&nbsp;</td></tr>
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
                            <br/>
                                    <table class="table no-more-tables general">
                                        <thead>
                                            <tr>
                                                <th style="width:20%">PO Number</th>
                                                <th style="width:80%">Invoices</th>
                                            </tr>
                                        </thead>                                        
                                       </table>    
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
                                                			<td><?php echo $i->receiveddate;?></td>
                                                			<td>$<?php echo $i->totalprice;?></td>
                                                			<td><?php echo $i->paymentstatus;?></td>
                                                			<td><?php echo $i->status;?></td>
                                                			<td>
                                                			<input type="text" value="<?php if($i->datedue) echo date("m/d/Y", strtotime($i->datedue)) ;?>" class="date" style="width:100px;"
                                                			data-date-format="mm/dd/yyyy" readonly 
                                                			onchange="changeduedate('<?php echo $i->invoicenum;?>',this.value)"/>
                                                			</td>
                                                		</tr>
                                                		<?php }?>
                                                <?php } ?>
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