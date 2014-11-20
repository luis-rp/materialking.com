<?php
	$recsum =0;
    $qntsum =0;
	foreach($awarditems as $ai)
	{
		$recsum = $recsum + $ai->received;
        $qntsum = $qntsum + $ai->quantity;
	}
	if($qntsum==0) $per=0;
	else $per = number_format(($recsum/$qntsum)*100,2);
    $per .='%';
?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/datatable.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>
<link href="<?php echo base_url();?>templates/front/assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
 <?php echo '<script>var datedueurl="' . site_url('quote/invoicedatedue') . '";</script>' ?>
<script type="text/javascript" charset="utf-8">
	$(document).ready( function() {
		$('#datatable').dataTable( {
			"sPaginationType": "full_numbers",
			"bPaginate":false,
			"aoColumns": [
					null,
					null,
					null,
					null,
					null,
					null,
					null,					
					{ "bSortable": false},
					{ "bSortable": false}

				]
			} );
	 $('.dataTables_length').hide();
	 $('.daterequested').datepicker();
	})
	function invoice(invoicenum)
	{
		$("#invoicenum").val(invoicenum);
		$("#invoiceform").submit();
	}

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


</script>



<script>
$(document).ready(function(){
	$(".datefield").datepicker();
    <?php if($per=='0.00%') { ?>
    $("#timelineid").attr("class","bar madras");
    $("#timelineid").css("width",'100%');
    <?php }else{ ?>
    	$("#timelineid").css("width",'<?php echo $per;?>');
    <?php } ?>

    $("#timelineid").css("width",'<?php echo $quote->progress;?>%');
});
</script>
<style>
tr.still-due td
{
	color: #990000;
}

h3 {
	background: none repeat scroll 0 0 #000;
    border-radius: 6px 6px 0 0;
    color: #fff;
    display: inline-block;
    font-size: 18px;
    font-weight: bold;
    padding: 0.5% 0 0.5% 1%;
    position: relative;
    top: 10px;
    width: 100%;
}

label
{
display:none;
}

.progress
{
/*background-color: #ebeef1;
    background-image: none;
    box-shadow: none;*/
}
</style>

<form id="invoiceform" method="post" action="<?php echo site_url('quote/invoice');?>">
	<input type="hidden" id="invoicenum" name="invoicenum"/>
</form>

    <div class="content">
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title"> <a href="<?php echo site_url('admin/quote/track_purchase_export').'/'.$quoteid.'/'.$award; ?>" class="btn btn-green">Export</a>&nbsp;&nbsp; <a href="<?php echo site_url('admin/quote/track_purchase_pdf').'/'.$quoteid.'/'.$award; ?>" class="btn btn-green">View PDF</a>&nbsp; &nbsp; <a href="<?php echo site_url('admin/quote/items_pdf').'/'.$quoteid; ?>" class="btn btn-green">View P.O.</a><br />
			<h3>
			Items for the Contract <?php echo $quote->ponum;?>
			<?php if(@$bid->quotenum){?>
			| Quote Ref# <?php echo $bid->quotenum;?>
			<?php }?>
			<div class="pull-right" style="margin-right:10px;">
			<a class="pull-left btn btn-info" href="<?php echo site_url('admin/quote/contractitems/'.$quote->id);?>">View Performance</a>
            &nbsp;&nbsp;
			<?php if(isset($quoteid)){ ?>
		 		<a class="pull-right btn btn-info" href="<?php echo site_url('admin/message/messages/'.$quoteid);?>">View Messages</a>
			<?php }  ?>		
			</div>
			 </h3>
		</div>
				<br/>
	   <div id="container" style="width:100%;margin-left:2%;">
		<?php
		    	if($awarditems)
		    	{
		    ?>
		<div class="row">

			<form method="post" action="<?php echo site_url('admin/quote/shipitems/'.$quote->id.'/'.$award);?>" enctype="multipart/form-data">
            <div class="col-md-12">

                <div class="grid simple ">
                    <div class="grid-title no-border" style="width:98%;">
                    	Award Date: <?php if(isset($quote->podate)) echo $quote->podate; else echo '';?><br/>
			            Company: <?php if(isset($purchasingadmin->companyname)) echo $purchasingadmin->companyname; else echo '';?><br/>			
                </div>
                
                    <div class="grid-body no-border" style="width:98%;">
                        <div class="progress progress-striped active progress-large">
    				    	<div class="progress-bar progress-bar-success" style="width:<?php echo $per;?>;" data-percentage="<?php echo $per;?>">
    				    	  <?php echo $per;?>items&nbsp;received
    				    	</div>
    					</div>

                        <div class="progress progress-striped active progress-large">
    				    	<div class="progress-bar progress-bar-success" style="width:<?php echo $quote->progress;?>%;" data-percentage="<?php echo $quote->progress;?>%">Contract Progress: <?php echo $quote->progress;?>%</div>
    					</div>

    						<table id="datatable" class="table no-more-tables general">
                                <thead>
                                    <tr>
                                        <th style="width:20%">File Name</th>
                                        <th style="width:5%">Description</th>                                       
                                        <th style="width:10%">Price</th>
                                        <th style="width:10%">Total</th>
                                        <!-- <th style="width:10%">Requested</th> -->
                                        <th style="width:15%">Notes</th>
                                        <th style="width:5%">Completed</th>
                                        <th style="width:5%">Due</th>
                                        <th style="width:5%">Update Progress</th>
                                        <th style="width:5%">Ref#</th>
                                    </tr>
                                </thead>

                                <tbody>
    				              <?php
    						    	$i = 0;
    						    	foreach($awarditems as $ai)
    						    	{
    						    		$i++;
    						      ?>
                                    <tr class="<?php echo $ai->quantity - $ai->received > 0?'still-due':'';?>">
                                        <td class="v-align-middle"><?php echo $ai->attach;?></td>
                                        <td class="v-align-middle"><?php echo $ai->itemname;?></td>                                        
                                        <td class="v-align-middle">$<?php echo $ai->ea;?></td>
                                        <td class="v-align-middle">$<?php echo round($ai->totalprice,2);?></td>
                                        <!-- <td class="v-align-middle"><?php echo $ai->daterequested;?></td> -->
                                        <td class="v-align-middle"><?php echo $ai->notes;?></td>
                                        <td class="v-align-middle"><?php echo $ai->received;?></td>
                                        <!-- <td class="v-align-middle">
                                        <?php echo $ai->quantity - $ai->received;?>
                                        <?php if($ai->pendingshipments){?>
                                        <br/><?php echo $ai->pendingshipments;?> Pending Acknowledgement
                                        <?php }?>
                                        </td> -->
                                        <td class="v-align-middle">
                                        <?php echo '100%';?>
                                        </td>
                                        <td>
                                        	<div class="progress progress-striped active progress-large">
                      									<div class="progress-bar <?php echo @$inv->mark; ?>" style="width: <?php echo '0'; // echo $inv->progress?>%;" data-percentage="<?php echo '0'; ?>%"><?php echo '0';  ?>% </div>
                   									</div>
                                        </td>
                                        <td>                                       	
                                        	<input style="width:100px" type="text" name="invoicenum<?php echo $ai->id;?>"/>                        	
                                        </td>
                                    </tr>
                                  <?php } ?>
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        	<div class="col-md-12">
                 <div class="grid simple ">
                    <div class="grid-title no-border">
                        <h3 style="width:97%;">Billing Document</h3>
                    </div>
                    <div class="grid-body no-border">

			<input type="hidden" name="quote" value="<?php echo $quote->id;?>">
			<input type="hidden" name="award" value="<?php echo $award;?>">
			<input type="hidden" name="purchasingadmin" value="<?php echo $quote->purchasingadmin;?>">
			<input type="file" name="filename">
			<br/>
			<input type="submit" class="btn btn-primary" value="Send Progress Update"/>
                    </div>
                 </div>
             </div>
        		<?php if($shippingdocs){?>
            <div class="col-md-12">
                 <div class="grid simple" style="width:98%;">
                    <div class="grid-title no-border">
        		<h4>Existing Documents</h4>
                    </div>
                     <div class="grid-body no-border">
        		<table class="borderless general">
                                <thead>
        			<tr>
        				<th>Date</th>
        				<th>REF#</th>
        				<th>View</th>
        			</tr>
                                </thead>
                                <tbody>
        			<?php foreach($shippingdocs as $sd){?>
        			<tr>
                                        <td><?php echo date("m/d/Y",  strtotime($sd->uploadon));?></td>
                                        <td><?php echo $sd->invoicenum;?></td>
        				<td><a href="<?php echo site_url('uploads/shippingdoc/'.$sd->filename);?>" target="_blank">View</a></td>
        			</tr>
        			<?php }?>
                                </tbody>
        		</table>
        	</div>
            </div>
            </div>
            <?php }?>
        	</form>
        </div>

        <?php if($shipments){?>

        <div class="row">
               <div class="col-md-12">
    		    <div class="grid simple ">
                    <div class="grid-title no-border">
        	<h4>Shipments Made For PO# <?php echo $quote->ponum;?> </h4>
                    </div>
                    <div class="grid-body no-border">

			<table class="borderless general">
				<tr>
					<th>Ref#</th>
					<th>Item</th>
					<th>Quantity</th>
					<th>Sent On</th>
					<th>Status</th>
				</tr>
				<?php
				    foreach($shipments as $s)
				    {
				?>
				<tr>
					<td><?php echo $s->invoicenum;?></td>
					<td><?php echo $s->itemname;?></td>
					<td><?php echo $s->quantity;?></td>
					<td><?php echo date('m/d/Y',strtotime($s->shipdate));?></td>
					<td><?php echo $s->accepted?'Accepted':'Pending';?></td>
				</tr>
				<?php }?>
			</table>
        </div>
               </div>
             </div>
          </div>
        <?php }?>

        <?php if($invoices){?>
        <div class="row">
            <div class="col-md-12">
    		    <div class="grid simple ">
                    <div class="grid-title no-border">
        	<h4>Existing Invoices For PO# <?php echo $quote->ponum;?> </h4>
                    </div>
                    <div class="grid-body no-border">

			<table class="borderless general">
				<tr>
					<th>Invoice#</th>
					<th>Status</th>
					<th>Received On</th>
					<th>Total Cost</th>
					<th>Payment Status</th>
					<th>Due Date</th>
				</tr>
				<?php
				    foreach($invoices as $i)
				    {
				        $amount = $i->totalprice;
				        $amount = $amount + ($amount*$settings->taxpercent/100);
				        $amount = number_format($amount,2);
				?>
				<tr>
					<td>
					<a href="javascript:void(0)" onclick="invoice('<?php echo $i->invoicenum;?>');">
					<?php echo $i->invoicenum;?>
					</a>
					</td>
					<td><?php echo $i->status;?></td>
					<td><?php echo date('m/d/Y', strtotime($i->receiveddate));?></td>
					<td>$<?php echo $amount;?></td>
					<td>
						<?php echo $i->paymentstatus;?>

	                  	<?php if($i->paymentstatus=='Unpaid'){?>
	                  	<form action="<?php echo site_url('quote/requestpayment/'.$quote->id.'/'.$award);?>" method="post">
	                  		<input type="hidden" name="invoicenum" value="<?php echo $i->invoicenum;?>"/>
	                  		<input type="submit" name="companystatus" value="Request Payment">
	                  	</form>
	                  	<?php }elseif($i->status=='Verified'){?>
	                  	/ <?php echo $i->paymenttype;?> / <?php echo $i->refnum;?>
	                  	<?php }?>
					</td>
					<td><input type="text" class="span daterequested highlight" name="daterequested" value="<?php if($i->datedue){ echo date('m/d/Y',strtotime($i->datedue)); }else{ echo "No Date Set"; }?>" data-date-format="mm/dd/yyyy" onchange="changeduedate('<?php echo $i->invoicenum;?>',this.value)" /></td>
				</tr>
				<?php }?>
			</table>
        </div>
                 </div>
              </div>
        </div>

        <?php }?>

        <?php } else { //if awarded items ?>

            <div class="errordiv">
    			<div class="alert alert-info">
                  	<button data-dismiss="alert" class="close"></button>
                  	<div class="msgBox">
                  		No Items Detected on System.
                  	</div>
             	</div>
    		</div>
        <?php }?>

		</div>
	  </div>