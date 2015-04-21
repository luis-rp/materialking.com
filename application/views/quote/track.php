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

<script type="text/javascript">
$.noConflict();
 </script>

<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/datatable.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>
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
					null,
					null,
					{ "bSortable": false},
					{ "bSortable": false}

				]
			} );
	 $('.dataTables_length').hide();
	 
	 /*$('#daterequested').change(function() {
	 	alert('gfgf');
	 });*/
	 
	 $('.daterequested').datepicker({ autoclose: true });
	})
	function invoice(invoicenum,invoicequote)
	{
		$("#invoicenum").val(invoicenum);
		$("#invoicequote").val(invoicequote);	
		$("#invoiceform").submit();
	}

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
	
	
	
	/*function caloclick(){
		$('.daterequested').datepicker();
	}*/

</script>



<script>
$(document).ready(function(){
	
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
</style>

<form id="invoiceform" method="post" action="<?php echo site_url('quote/invoice');?>">
	<input type="hidden" id="invoicenum" name="invoicenum"/>
	<input type="hidden" id="invoicequote" name="invoicequote"/>
</form>

    <div class="content">
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title"> <a href="<?php echo site_url('quote/track_export').'/'.$quoteid.'/'.$award; ?>" class="btn btn-green">Export</a>&nbsp;&nbsp; <a href="<?php echo site_url('quote/track_pdf').'/'.$quoteid.'/'.$award; ?>" class="btn btn-green">View PDF</a>&nbsp; &nbsp; <a href="<?php echo site_url('quote/getawardedpdf').'/'.$quoteid; ?>" class="btn btn-green">View P.O.</a><br />
			<h3>
			Items for the PO# <?php echo $quote->ponum;?>
			<?php if(@$bid->quotenum){?>
			| Quote Ref# <?php echo $bid->quotenum;?>
			<?php }?>

			<?php if(isset($messagekey)){ ?>
		 <a class="pull-right btn btn-primary" href="<?php echo site_url('message/messages/'.$messagekey);?>">View Messages</a>
			<?php } ?>
		 &nbsp; &nbsp;
			<a class="pull-right btn btn-primary" href="<?php echo site_url('quote/items/'.$quote->id);?>">View Performance</a>
			 </h3>
		</div>
				<br/>
	   <div id="container">
		<?php
		    	if($awarditems)
		    	{
		    ?>
		<div class="row">

			<form method="post" action="<?php echo site_url('quote/shipitems/'.$quote->id.'/'.$award);?>" enctype="multipart/form-data">
            <div class="col-md-12">

                <div class="grid simple ">
                    <div class="grid-title no-border">
                    		    Order Date: <?php if(isset($quote->podate)) echo $quote->podate; else echo '';?>
			<br/>
			Company: <?php if(isset($purchasingadmin->companyname)) echo $purchasingadmin->companyname; else echo '';?>
			<br/>

                    </div>
                    <div class="grid-body no-border">

                        <div class="progress progress-striped active progress-large">
    				    	<div class="progress-bar progress-bar-success" style="width: <?php echo $per;?>;" data-percentage="<?php echo $per;?>"><?php echo $per;?> items received</div>
    					</div>

                        <div class="progress progress-striped active progress-large">
    				    	<div class="progress-bar progress-bar-success" style="width: <?php echo $quote->progress;?>%;" data-percentage="<?php echo $quote->progress;?>%">PO Progress: <?php echo $quote->progress;?>%</div>
    					</div>

    						<table id="datatable" class="table no-more-tables general">
                                <thead>
                                    <tr>
                                        <th width="12%">Item Code/Name</th>
                                        <th width="12%">Item Image</th>
                                        <th width="6%">Qty.</th>
                                        <th width="6%">Unit</th>
                                        <th width="6%">Price</th>
                                        <th width="6%">Total</th>
                                        <th width="6%">Requested</th>
                                        <th width="12%">Notes</th>
                                        <th width="6%">Shipped</th>
                                        <th width="6%">Due</th>
                                        <th width="8%">Ship Qty</th>
                                        <th width="14%">Ref#</th>
                                    </tr>
                                </thead>

                                <tbody>
    				              <?php
    						    	$i = 0; $remaining =0;
    						    	//echo '<pre>',print_r($awarditems);die;
    						    	$subtot=0;
    						    	$totwithtax=0;
    						    	foreach($awarditems as $ai)
    						    	{
    						    		$i++;
    						      ?>
                                    <tr class="<?php echo $ai->quantity - $ai->received > 0?'still-due':'';?>">
                                        <td class="v-align-middle" style="word-break:break-all;"><?php echo $ai->itemcode;?>
                                        <br/>
                                        <span style="font-size:11px; color:#999999;float:left;"><?php echo $ai->itemname;?></span>
                                       
                                        </td>
                                        <td>
                                        
                                        <?php if(isset($ai->item_img) && $ai->item_img!= "" && file_exists("./uploads/item/".$ai->item_img)) 
								    		{ ?>
	                                           <img style="max-height: 120px;max-width: 100px; padding: 5px;" height="100%" width="100%" src="<?php echo site_url('uploads/item/'.$ai->item_img) ?>" alt="<?php echo $ai->item_img;?>">
	                                        <?php } else { ?>
	                                            <img style="max-height: 120px;max-width: 100px;  padding: 5px;"height="100%" width="100%" src="<?php echo site_url('uploads/item/big.png') ?>" alt="">
	                                        <?php } ?>
	                                     
                                        </td>
                                        <td class="v-align-middle" style="word-break:break-all;"><?php echo $ai->quantity;?></td>
                                        <td class="v-align-middle" style="word-break:break-all;"><?php echo $ai->unit;?></td>
                                        <td class="v-align-middle" style="word-break:break-all;">$<?php echo $ai->ea;?></td>
                                        <td class="v-align-middle" style="word-break:break-all;">$<?php echo round($ai->totalprice,2); ?></td>
                                        <td class="v-align-middle" style="word-break:break-all;">
                                        <?php echo $ai->daterequested;
                                              $orgdate=date('Y-m-d', strtotime( $ai->daterequested));
                                              $surrentdate=date('Y-m-d');                                     
								              $date1 = date_create($surrentdate);
											  $date2 = date_create($orgdate);
											  $diff12 = date_diff($date1, $date2);
											  //$days = $diff12->d;
											  echo "<br>Due in&nbsp;".$diff12->format("%R%a days");?></td>
                                        <td class="v-align-middle" style="word-break:break-all;"><?php echo $ai->notes;?></td>
                                        <td class="v-align-middle" style="word-break:break-all;">
                                        <?php // echo $ai->received; 
										if($invoices){
											foreach($invoices as $invoice){
												$totalshipped = $ai->received;
												if(@$invoice->invoice_type=="error")
													$totalshipped +=  abs($invoice->quantity);
											}
											echo $totalshipped; 
										}else 
										echo $ai->received; 
										?><br>
                                        <?php if($ai->pendingshipments){?>
                                        <br/><?php echo round($ai->pendingshipments,2);?>
                                        <?php }?>
                                        </td>
                                        <td class="v-align-middle" style="word-break:break-all;">
                                        <?php if(($ai->received) < 0) echo $ai->quantity; else echo $ai->quantity - $ai->received;?>
                                        <?php if($ai->pendingshipments){?>
                                        <br/><?php echo $ai->pendingshipments;?> Pending Acknowledgement
                                        <?php }?>
                                        </td>
                                        <td style="word-break:break-all;">
                                        	<?php if($ai->quantity - $ai->received){?>
                                        	<input class="form-control" type="text" name="quantity<?php echo $ai->id;?>" onkeyup="this.value=this.value.replace(/[^0-9]/g,'');"/>
                                        	<?php }?>
                                        </td>
                                        <td style="word-break:break-all;">
                                        	<?php  $isupfrontinvoice = 0; if($ai->quantity - $ai->received){?>                                       
                                        	<input class="form-control" type="text"  name="invoicenum<?php echo $ai->id;?>" value="" />
                                        	<?php }?>
                                        </td>
                                    </tr>
                                  <?php $remaining += (@$ai->quantity - @$ai->received);  
                                  $subtot +=($ai->quantity*$ai->ea);
    						    	$totwithtax +=$ai->totalprice;} ?>
                                  <tr>
                                  <td colspan="5" style="text-align:right;">
                                  SubTotal : 
                                  </td>
                                   <td style="text-align:left;">
                                   <?php if($subtot!=""){  echo "$".number_format($subtot,2);} ?>
                                  </td>
                                  <td style="text-align:right;">
                                  Total : 
                                  </td>
                                  <td colspan="5" style="text-align:left;">
                                   <?php if($totwithtax!=""){  echo "$".number_format($totwithtax,2);} ?>
                                  </td>
                                  </tr>
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        	<div class="col-md-12">
                 <div class="grid simple ">
                    <div class="grid-title no-border">
                        <h3>Shipping Document</h3>
                    </div>
                    <div class="grid-body no-border">

			<input type="hidden" name="quote" value="<?php echo $quote->id;?>">
			<input type="hidden" name="award" value="<?php echo $award;?>">
			<input type="hidden" name="purchasingadmin" value="<?php echo $quote->purchasingadmin;?>">
			<input type="file" name="filename">
			<br/>
			<?php if(@$remaining>0){ ?><input type="submit" class="btn btn-primary" value="Send Shipment"/><?php } ?>
                    </div>
                 </div>
             </div>
        		<?php if($shippingdocs){?>
            <div class="col-md-12">
                 <div class="grid simple ">
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

        
         <div>
                <?php
				$invoicearrayofnum = array();
				if($invoices){
					
					foreach($invoices as $i)
				    {
				    	$invoicearrayofnum[] = $i->invoicenum;
				    }
					
				}
                if(!empty($errorLog))
                {
                    ?>
                     <hr>
                         <h3 class="box-header">Error Log</h3>
                       <table  class='table table-bordered'>
                            <tbody>
                                <tr>
                                     <th>company</th>
                                     <th>Error</th>
                                     <th>Item</th>
                                     <th>Qty</th>
                                     <th>Invoice#</th>
                                     <th>Date</th>
                                 </tr>
                        <?php
                         foreach($errorLog as $error)
                         { ?>
                                <tr>
                                    <td><?php echo $error->title;?></td>
                                    <td><?php echo $error->error;?></td>
                                    <td><?php echo $error->itemcode;?></td>
                                    <td><?php echo $error->quantity;?></td>
                                    <td><?php if(in_array($error->invoicenum."-Error",$invoicearrayofnum)) {?><a href="javascript:void(0);" onclick="invoice('<?php echo $error->invoicenum."-Error"; ?>',<?php echo $quote->id; ?>);"><?php echo $error->invoicenum;?></a><?php } else echo $error->invoicenum; ?> </td>
                                    <td><?php echo (isset($error->date) && $error->date!="" && $error->date!="0000-00-00" && $error->date!="1969-12-31")?date("m/d/Y",  strtotime($error->date)):"";?></td>
                                </tr>
                        <?php
                        }?>
                       </tbody>
                 </table>
                 <?php    }
                  ?>
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
				        				        
				        if(@$i->discount_percent){
				        	
				        	$amount = $amount - ($amount*$i->discount_percent/100);
				        }

				        if(@$invoice->penalty_percent){
				        	
				        	$amount = $amount + (($amount*$i->penalty_percent/100)*$i->penaltycount);
				        }
				        
				        $amount = $amount + ($amount*$settings->taxpercent/100);
				        
				        $amount = number_format($amount,2);
				?>
				<tr>
					<td>
					<a href="javascript:void(0)" onclick="invoice('<?php echo $i->invoicenum;?>','<?php echo $i->quote->id;?>');">
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
	                  	/ <?php echo @$i->paymenttype;?> / <?php echo @$i->refnum;?>
	                  	<?php }?>
					</td>
					<td><?php if($i->paymentstatus!='Credit'){?><input type="text" class="span daterequested highlight" id="daterequested<?php echo $i->invoicenum;?>" name="daterequested" value="<?php if($i->datedue){ echo date('m/d/Y',strtotime($i->datedue)); }else{ echo "No Date Set"; }?>" data-date-format="mm/dd/yyyy" onchange="changeduedate('<?php echo $i->invoicenum;?>',this.value)" /><?php } ?>
					 <input type="hidden" id="originaldate<?php echo $i->invoicenum;?>" value="<?php if($i->datedue){ echo date('m/d/Y',strtotime($i->datedue)); } ?>" />				
					</td>
				</tr>
				<?php }?>
				<input type="hidden" id="canceldate" /> 	
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