<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
<script>

	function viewitems(itemid)
	{
    	var serviceurl = '<?php echo base_url()?>admin/itemcode/ajaxdetail/'+ itemid;
    	//alert(serviceurl);
        $("#quoteitems").html('loading ...');
        $("#itemsmodal").modal();
            $.ajax({
		      type:"post",
		      url: serviceurl,              
              }).done(function(data){
		        $("#quoteitems").html(data);
		        //$("#itemsmodal").modal();
		    });
	}
	
function awardbiditems()
{
	var total = parseInt($('#selectedtotal').html());
	if(total==0)
	{
		alert('Total amount cannot be 0');
		return false;
	}
	var ids = [];
	$('.selection-item:checked').each(function() {
		ids.push($(this).val());
    });
	$("#awardbid").val('');
    $("#itemids").val(ids.join(','));
	$("#awardmodal").modal();
}

function usedefaultaddresscheckchange()
{
	if($("#usedefaultaddress").attr('checked'))
		$("#shipto").val('<?php echo implode(' ',explode("\n",$project->address));?>');
}
	

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

function paycc(amount,bankaccounarr, bankcnt)
{			
	   if(bankcnt>0){
	   		alert(" You can't proceed. These Supplier/s have not set their bank account details: "+bankaccounarr);	
	   }else{
		var invoicenumber = $('#quoteid').val();
		$("#ccpayinvoicenumber").val(invoicenumber);
		$("#ccpayinvoiceamount").val(amount);
		$("#ccpayamountshow").html(amount);
		$('#shiptocopy').val($('#shipto').val());
		$("#awardmodal").hide();	
		$("#paymodal").modal();		
	   }
}


</script>
<?php 
	//print_r($bids);die;

	$maxcountitems = count($quoteitems); 
	
	$oldcompany2 = "";
	$companyarr = array();
	foreach($bids as $bid){
		
		if($bid->company!="" && $bid->company!=$oldcompany2)
		$companyarr[] = $bid->company;
		$oldcompany2 = $bid->company;
	}
	
	$companycount = 0;
	$itemcount = 0;
	if(count($companyarr)>0){
	
	foreach($quoteitems as $qitem){
		
		if(!in_array($qitem->company,$companyarr)){
			$companycount++;
			$itemcount++;
		}
	 }
	}		
	
	
	$isawarded = $isawarded=='Yes'?true:false;
	$checkedarray = array();
	echo '<script>var tax='.$config['taxpercent'].';</script>';
	$awardeditemcompany = array();
	if($isawarded)
	{
		$awardedtotal = 0;
		if(@$awarded->items)
		foreach($awarded->items as $ai)
		{
			$awardeditemcompany[]=$ai->itemcode . $ai->company;
			$awardedtotal+=$ai->quantity * $ai->ea;
 		}
 		$awardedtotal = round($awardedtotal,2);
 		$awardedtax = $awardedtotal * $config['taxpercent'] / 100;
 		$awardedtax = round($awardedtax,2);
 		$awardedtotalwithtax = $awardedtotal + $awardedtax;
 		$awardedtotalwithtax = round($awardedtotalwithtax,2);
		$highTotal =array_sum($maximum);
		$totalsaved =0;
		if($highTotal > $awardedtotal){
 			$totalsaved = $highTotal - $awardedtotal ;
		}
		
	}
?>
<style>

.findtext
{
	color: #999;
	font-family: Century Gothic;
	font-size: 12px;
	padding-right: 10px;
}

.redrow
{
	background: #eedddd;
}
.greenrow
{
	background: #BFEFFF;
}
</style>

<script>
$(document).ready(function(){

});
</script>
<section class="row-fluid">
	<h3 class="box-header"><?php echo @$heading; ?> <?php if(!$isawarded){?> &nbsp;&nbsp;<a style="font-size:12px;font-weight:normal;" target="_blank" href="<?php echo site_url().'admin/quote/update/'.$quote->id;?>">Edit Quote</a> <?php } ?>
			&nbsp;&nbsp;<a style="font-size:12px;font-weight:normal;" target="_blank" href="<?php echo site_url('admin/message/messages/'.$quote->id);?>">View Messages</a></h3>
	<div class="box">
		<div class="span12">
		   <a class="btn btn-green" href="<?php echo site_url('admin/quote/index/'.$quote->pid);?>">&lt;&lt; Back</a>
		   <br/>
		   <?php echo $this->session->flashdata('message'); ?>
		   <?php echo @$message; ?>
		   
		   <span class="poheading"><?php echo $quote->potype=='Direct'?'Direct':'Via Quote';?></span>
		   <?php if($isawarded){?>
		   <h4>
		   Confirmed on <?php echo $awarded->awardedon;?>
		   <a href="<?php echo site_url('admin/quote/track/'.$quote->id);?>">Track</a>
		   </h4>
		   <?php }else{?>
		   <div class="span12">
		   <table><tr><td>
		   <form method="post" action="<?php echo site_url('admin/quote/confirmdirect');?>">
		   	<input type="hidden" name="quote" value="<?php echo $quote->id;?>"/>
		   	<input type="button" class="btn btn-primary" onclick="awardbiditems();" value="Confirm &amp; Proceed"/>
		   </form>
		   </td><td>
		   <?php if(@$companycount > 0){		   		   
		   echo @$companycount." Company, Assigned to ".@$itemcount." item has not approved your P.O yet (current status is processing).<br> If you confirm and proceed now, the P.O will only include the items shown below and will be closed. <br> If you wish to issue the P.O with all items on your original order, please wait until all orders are approved from all vendors.";
		   
		   } ?>
		   </td></tr></table>
		   </div>
		   <?php }?>
		  <?php 
		  	foreach($bids as $bid)
		  	if($bid->items)
		  	{
		  		$sn = 1;
		  ?>
		      <div class="control-group">
			    <div class="controls"><strong>PO #:<?php echo $quote->ponum; ?>
			      &nbsp; &nbsp; 
			      Company:   <?php echo $bid->companyname;?> &nbsp; &nbsp;
			      Submitted:  <?php echo date('m/d/Y', strtotime($bid->submitdate));?>&nbsp; 
			      <?php if($bid->quotefile){?>
			      	<a href="<?php echo site_url('uploads/quotefile/'.$bid->quotefile);?>" target="_blank">View Attachment</a>
			      <?php }?>
			      </strong>
			      
				  <?php if($bid->draft=='Yes'){ ?>
				    <span class="label label-pink">Draft</span>
				  <?php }?>
			      <br/><br/>
			  
			  <div class="control-group">
				    <table class="table table-bordered">
				    	<tr>
				    		<th>#</th>
				    		<th>Item Code</th>
				    		<th>Item Name</th>
				    		<th>Qty.</th>
				    		<th>Unit</th>
				    		<th>60 day Low. Price</th>
				    		<th>Price EA</th>
				    		<th>Total Price</th>
				    		<th>Date Available</th>
				    		<th>Cost Code</th>
				    		<th>Notes</th>
				    		<th>Status</th>
				    	</tr>
				    	<?php $alltotal=0; foreach($bid->items as $q) if($q->itemcode){?>
				    	<?php $alltotal += $q->quantity * $q->ea;?>
		    			<?php 
							$key = $q->itemcode;
		    				$diff = $q->ea - $minimum[$key];
		    			?>
				    	<tr class="<?php if($q->postatus=='Accepted'){echo 'greenrow';} else{echo 'redrow';}?>">
				    		<td><?php echo $sn++;?></td>
				    		<td>
				    			<?php echo "<a href='javascript:void(0)' onclick=\"viewitems('$q->itemid')\">$q->itemcode</a>"; ?>
				    		</td>
				    		<td><?php echo $q->itemname;?></td>
				    		<td><?php echo $q->quantity;?></td>
				    		<td><?php echo $q->unit;?></td>
				    		<td><?php echo $q->minprice;?></td>
				    		<td>$ <span id="ea<?php echo $q->id;?>"><?php echo $q->ea;?></span></td>
				    		<td>$ <span id="itemtotal<?php echo $q->id;?>"><?php echo round($q->quantity * $q->ea,2);?></span></td>
				    		<td><?php echo $q->daterequested;?></td>
				    		<td><?php echo $q->costcode;?></td>
				    		<td><?php echo $q->notes;?></td>
				    		<td><?php echo $q->postatus;?></td>
				    	</tr>
				    	<?php }?>
				    </table>

				    <?php 
				    	if($bid->messages)
				    	{
				    ?>
				    Messages:
				    <table class="table table-bordered" >
					    <tr>
					    	<th>From</th>
					    	<th>To</th>
					    	<th>Message</th>
					    	<th>Date/Time</th>
                                                <th>&nbsp;</th>
					    </tr>
					    <?php
					    	foreach($bid->messages as $msg)
					    	{
					    ?>
					    <tr>
					    	<td><?php echo $msg->from;?></td>
					    	<td><?php echo $msg->to;?></td>
					    	<td><?php echo $msg->message;?></td>
					    	<td><?php echo $msg->senton;?></td>
                            <td>
                            <?php if($msg->user_attachment!=''){?>
                            <a href="<?php echo site_url('uploads/messages').'/'.$msg->user_attachment;?>" target="_blank" title="View Attachment"><?php echo 'View Attachment';?></a>
                                 <?php }?>

                        </td>
					    </tr>
					    <?php
					    	}
					    ?>
				    </table>
				    <?php
				    	}
				    ?>
				    
				    
				    <div class="well">
					    <form method="post" class="form-horizontal" enctype="multipart/form-data" action="<?php echo site_url('admin/message/sendmessage/'.$bid->quote)?>">
					    	<input type="hidden" name="quote" value="<?php echo $bid->quote;?>"/>
					    	<input type="hidden" name="company" value="<?php echo $bid->company;?>"/>
					    	<input type="hidden" name="from" value="<?php echo $this->session->userdata('fullname')?> (Admin)"/>
					    	<input type="hidden" name="to" value="<?php echo $bid->companyname;?>"/>
					    	<input type="hidden" name="ponum" value="<?php echo $quote->ponum;?>"/>
					    	
					    	
					    	<div class="control-group">
                            <label class="control-label" for="company">Send Message To:</label>
                            <div class="controls">
                            <?php echo $bid->companyname;?>
                            </div>
                            </div>
    
                            <div class="control-group">
                            <label class="control-label" for="message">Message</label>
                            <div class="controls">
                           		<textarea name="message" class="span8" rows="5" required></textarea>
                            </div>
                            </div>
                            
                            <div class="control-group">
                            <label class="control-label" for="userfile">Attachment</label>
                            <div class="controls">
                           		 <input type="file" name="userfile" size="10"  />
                            </div>
                            </div>
                            
                            <div class="control-group">
                            <label class="control-label" for="">&nbsp;</label>
                            <div class="controls">
                           		 <input type="submit" value="Send" class="btn btn-primary"/>
                            </div>
                            </div>
    
				
					    </form>
				    </div>

<!--
                                    <div align="right" id="div_xml_type6">
             <form id="olditemform" class="form-horizontal" enctype="multipart/form-data" method="post"
		    	action="<?php echo base_url(); ?>admin/quote/updateattach">
			  	<input type="hidden" name="quoteid" value="<?php echo $this->validation->id;?>"/>
                    <div class="control-group">

		    <div class="controls">
		   Add Attachment   <input type="file" name="userfile" size="20"  />
                      </div>
		    </div> <input type="submit" value="Upload" class="btn btn-primary"/>
                    <br><a href="<?php echo site_url('uploads/quote').'/'.@$this->validation->quoteattachment ;?>" target="_blank">  <?php echo @$this->validation->quoteattachment;?>
                      </a>
</form></div>-->
			    </div>
			 
			    <br/>
			    <br/>
		    <?php }?>
	    </div>
    </div>
</section>

        
		        <div id="awardmodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
        	<form id="editform" class="stylemoduleform" method="post" action="<?php echo site_url('admin/quote/confirmdirect');?>">
			<input type="hidden" id="awardbid" name="bid">
            <input type="hidden" id="quoteid" name="quote" value="<?php echo $quote->id;?>">
            <input type="hidden" id="pid" name="pid" value="<?php echo $quote->pid;?>">
            <input type="hidden" id="itemids" name="itemids">
            <div class="modal-header">
        		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
            	<h3>Award Bid</h3>
        	</div>
        	<div class="modal-body">
          
        	<table>
	        	<tr>
		        	<td colspan="2"><strong>Shipping Address:</strong> <br/>
			        	<input type="checkbox" value="1" id="usedefaultaddress" name="usedefaultaddress" onchange="usedefaultaddresscheckchange()"/>
						<label for="usedefaultaddress">Use Default Project Address?</label>
			        	<textarea class="span6" rows="2" id="shipto" name="shipto"></textarea>
		        	</td>
	        	</tr>

        	</table>	

        	</div>
        	<div class="modal-footer">
        		<input type="hidden" name="quote" value="<?php echo $quote->id;?>"/>
        		&nbsp;<input type="button" data-dismiss="modal" class="close btn btn-primary" value="Cancel">&nbsp;
        		 <?php if($bid->creditonly==1) {?>
        		 <input type="button" class="btn btn-primary" value="Award&Pay" onclick="paycc('<?php echo @$alltotal?>','<?php if(count($bankaccarray)>0){ echo implode(",",$bankaccarray); }else echo ""; ?>','<?php echo count($bankaccarray);?>');"" />&nbsp;      		
        		<?php } else {?>
        		<input type="submit" class="btn btn-primary" value="Award"/>&nbsp;
        		<?php }?>
        		
        	</div>
            </form>
        </div>



        <div id="itemsmodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
        	
            <div class="modal-header">
        		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
        	</div>
        	<div class="modal-body" id="quoteitems">
        	</div>
            
        </div>
        
        
        
        <div id="paymodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
    <div class="modal-header">
    	<h3>
    	Pay by credit card
		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
		</h3>
	</div>
	<div class="modal-body" id="quoteitems">
        <form method="post" action="<?php echo site_url('admin/quote/payquotebycc/');?>" onsubmit="return validatecc();">
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
            	<input type="hidden" id="shiptocopy" name="shiptocopy"></textarea>
                <input type="submit" class="btn btn-primary arrow-right" value="Process">
            </div>
        </form>
	</div>

</div>