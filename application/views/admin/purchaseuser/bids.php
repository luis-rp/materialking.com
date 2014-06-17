<?php 
	//echo count($awarded->items);
    //echo '<pre>';print_r($bids);die;
	$maxcountitems = count($quoteitems); 
	
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
<script>
function showOriginal(quote,itemcode)
{
    $("#itemmodal").modal();
	url = '<?php echo base_url()?>admin/quote/getquoteitem';
	//alert(url);return false;
	$.ajax({
      type:"post",
      data:"quote="+quote+"&itemcode="+itemcode,
      url: url,
    }).done(function(data){
    	$("#itemdetails").html(data);
    });
}
function viewitems(quoteid)
	{
    	var serviceurl = '<?php echo base_url()?>admin/itemcode/ajaxdetail/'+ quoteid;
        $("#quoteitems").html('loading ...');
        $("#itemsmodal").modal();
            $.ajax({
		      type:"post",
		      url: serviceurl,
		      data: "quote="+quoteid,
              
              
              }).done(function(data){
		        $("#quoteitems").html(data);
		        //$("#itemsmodal").modal();
		    });
	}
</script>
<section class="row-fluid">
	<h3 class="box-header"><?php echo @$heading; ?></h3>
	<div class="box">
		<div class="span12">
		   <a class="btn btn-green" href="<?php echo site_url('admin/purchaseuser/quotes');?>">&lt;&lt; Back</a>
		   <a class="btn btn-green" href="<?php echo site_url('admin/purchaseuser/messages/'.$quote->id);?>">Messages</a>
		   <br/>
		   <?php echo $this->session->flashdata('message'); ?>
		   <?php echo @$message; ?>
		   
		   <span class="poheading"><?php echo $quote->potype=='Direct'?'Direct':'Via Quote';?></span>
		   <?php if($isawarded){?>
		   <h3>
		   Awarded on <?php echo $awarded->awardedon;?>
		   <a href="<?php echo site_url('admin/purchaseuser/track/'.$quote->id);?>">Track</a>
		   </h3>
		   <div class="span12">
		   <table class="table table-bordered span4 awarded-table">
			   <tr><td class="span4">Subtotal:</td><td class="span8"><?php echo $awardedtotal;?></td>
               <td class="span4">Total Saved:</td><td class="span8"><?php echo $totalsaved;?></td>
			   <tr><td>Tax:</td><td><?php echo $awardedtax;?></td>
			   <tr><td>Total:</td><td><?php echo $awardedtotalwithtax;?></td>
		   </table>
           
		   </div>
           
		   <?php }?>
		  <?php 
		  	foreach($bids as $bid)
		  	{
		  		$sn = 1;
		  ?>
		      <div class="control-group">
			    <div class="controls"><strong>PO #:<?php echo $quote->ponum; ?>
			      &nbsp; &nbsp; 
			      Company:   <?php echo $bid->companyname;?> &nbsp; &nbsp;
			      Submitted:  <?php echo date('m/d/Y', strtotime($bid->submitdate));?>&nbsp; 
			      </strong>
			      
				  <?php if($bid->draft=='Yes'){ ?>
				    <span class="label label-pink">Draft</span>
				  <?php }?>
			      <?php 
			      	if($maxcountitems > count($bid->items))
			      	{
			      ?>
			      
			      	  <div style="color:red">*This company did not some items: <span class="btn btn-mini btn-red" onclick="$('#notbid<?php echo $bid->id;?>').modal();">Show</span></div>
			      	  
					  <div id="notbid<?php echo $bid->id;?>" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
					  	<div class="modal-header">
					  		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
					  		<h4>Items not bid by <?php echo $bid->companyname;?></h4>
					  	</div>
					  	<div class="modal-body">
					      <table class="table table-bordered">
					      	<tr>
					    		<th>Item Code</th>
					    		<th>Item Name</th>
					    		<th>Qty.</th>
				    		</tr>
				    		<?php 
				    			foreach($quoteitems as $quoteitem)
				    			{
				    				$notbid = true;
				    				foreach($bid->items as $biditem)
				    					if($quoteitem->itemcode == $biditem->itemcode)
				    						$notbid = false;
				    			
				    			if($notbid)
				    			{
				    		?>
						      	<tr>
						    		<td><?php echo $quoteitem->itemcode?></td>
						    		<td><?php echo $quoteitem->itemname?></td>
						    		<td><?php echo $quoteitem->quantity?></td>
					    		</tr>
				    		<?php 
				    			}
				    			}
				    		?>
					      </table>
					      </div>
			   		  </div>
				  </div>
			      <?php
			      	}
			      	else 
			      	{
			      		echo '<br/><br/>';
			      	}
			      ?>
		      </div>
			  
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
				    		<th>Price Requested</th>
				    		<th>Total Price</th>
				    		<th>Date Available</th>
				    		<th>Cost Code</th>
				    		<th>Notes</th>
				    	</tr>
				    	<?php $alltotal=0; foreach($bid->items as $q) if($q->itemcode){?>
				    	<?php $alltotal += $q->quantity * $q->ea;?>
		    			<?php 
		    				
							$key = $q->itemcode;
		    				$diff = $q->ea - $minimum[$key];
		    			?>
				    	<tr class="<?php if(in_array($q->itemcode.$bid->company, $awardeditemcompany)){echo 'awarded-item';} elseif($q->substitute){echo 'substitute-item';}?>">
				    		<td><?php echo $sn++;?></td>
				    		<td>
				    			<?php echo $q->itemcode; ?>
				    			<?php if($q->substitute){?><small><span class="label label-red">Substitute</span></small><?php }?>
				    		</td>
				    		<td><?php echo $q->itemname;?></td>
				    		<td><?php echo $q->quantity;?></td>
				    		<td><?php echo $q->unit;?></td>
				    		<td><?php echo $q->minprice;?></td>
				    		<td>
				    			$ <?php echo $q->ea;?>				    			
				    		</td>
				    		<td>$ <?php echo $q->reqprice; ?></td>
				    		<td>$ <span id="itemtotal<?php echo $q->id;?>"><?php echo round($q->quantity * $q->ea,2);?></span></td>
				    		<td>
				    			<?php echo $q->daterequested;?>
				    			<?php if(@$q->originaldate) if(@$q->originaldate != $q->daterequested){ echo '<br/><span style="color:red">Req:'.$q->originaldate.'</span>';}?>
				    		</td>
				    		<td>
				    			<?php echo $q->costcode;?>
				    		</td>
				    		<td><?php echo $q->notes;?>&nbsp;</td>
				    	</tr>
				    	<?php }?>
				    	<?php 
				    		$alltotal = round($alltotal,2);
							$taxtotal = $alltotal * $config['taxpercent'] / 100;
				    		$taxtotal = round($taxtotal,2);
							$grandtotal = $alltotal + $taxtotal;
				    		$grandtotal = round($grandtotal,2);
							$diff = $alltotal - $minimum['totalprice'];
				    	?>
				    	<tr>
				    		<td colspan="<?php echo $isawarded?7:8;?>" style="text-align:right">Subtotal: </td>
				    		<td colspan="5">$ <?php echo number_format($alltotal,2);?></td>
				    	</tr>
				    	<tr>
				    		<td colspan="<?php echo $isawarded?7:8;?>" style="text-align:right">Tax: </td>
				    		<td colspan="5">$ <?php echo $taxtotal;?></td>
				    	</tr>
				    	<tr>
				    		<td colspan="<?php echo $isawarded?7:8;?>" style="text-align:right">Total: </td>
				    		<td colspan="5">$ <?php echo $grandtotal;?></td>
				    	</tr>
				    </table>
			    </div>
				    
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
		    <?php }?>
	    </div>
    </div>
</section>