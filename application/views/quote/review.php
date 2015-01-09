<?php //echo '<pre>'; print_r($quote);die;?>

<script type="text/javascript">
<!--
$(document).ready(function(){

});

function selectall(sel)
{
	$(".statusradio").each(function(){
		if($(this).attr('value')==sel)
			$(this).attr('checked',true);
		else
			$(this).attr('checked',false);
	});
}
//-->
</script>


    <div class="content"> 
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
			<h3>
				PO Review
			</h3>
			<h4>
    			<?php if(@$purchasingtier){?>
    				<?php echo $purchasingadmin->companyname;?>&nbsp;&nbsp;&nbsp;&nbsp;
    				Tier: <?php echo $purchasingtier->tier;?>&nbsp;&nbsp;
    				Total Credit: $<?php echo $purchasingtier->totalcredit;?>&nbsp;&nbsp;
    				Credit Limit: $<?php echo $purchasingtier->creditlimit;?>&nbsp;&nbsp;
    			<?php }?>
				<a class="pull-right btn btn-primary btn-xs btn-mini" href="<?php echo site_url('message/messages/'.$quote->id);?>">View Messages</a>
				<br/>
			</h4>
		</div>		
	   <div id="container">
		<div class="row">
                    <div class="col-md-12">
                        <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                                <div class="tools">	<a href="javascript:;" class="collapse"></a>
									<a href="#grid-config" data-toggle="modal" class="config"></a>
									<a href="javascript:;" class="reload"></a>
									<a href="javascript:;" class="remove"></a>
                                </div>
                            </div>
                            <div class="grid-body no-border">
                                   
								<table class="table no-more-tables general">
									<tr>
										<td>
										  <strong>
									      PO#: <?php echo $quote->ponum;?>
									      <br/>
									      Due: <?php echo $quote->duedate;?>
									      <br/>
									      Company: <?php echo $company->title;?>
									      <br/>
									      Contact: <?php echo $company->contact;?>
									      </strong>
									      <br/><br/>
									      	Please review each item for accepting or rejecting. When you are finished, Click the Save PO <br/>
											button.<br/><br/>
											Thank You,<br/>
											<strong><?php echo $purchasingadmin->companyname?></strong>
									     	<br/><br/>
									     </td>
									 </tr>
								</table>
								
						
							    <table class="table no-more-tables general">
							    	<thead>
							    	<tr>
							    		<th>Item Name</th>
							    		<th>Qty</th>
							    		<th width="100">Unit</th>
							    		<th>Price</th>
							    		<th>Total</th>
							    		<th>Date Avail.</th>
							    		<th>Notes</th>
							    		<th>Status</th>
							    	</tr>
							    	</thead>
							    	<tbody>
							    	
							    	<form id="olditemform"  method="post" action="<?php echo site_url('quote/reviewpo'); ?>" enctype="multipart/form-data"> 
								  	<input type="hidden" name="invitation" value="<?php echo $invitation;?>"/>
									<?php foreach($quoteitems as $q){//print_r($q);?>
							    	<tr>
							    		<td>
								    		<?php echo htmlentities($q->itemname);?>
							    		</td>
							    		<td><?php echo $q->quantity;?></td>
							    		<td><?php echo $q->unit;?></td>
							    		<td><?php echo $q->ea;?></td>
							    		<td><?php echo $q->totalprice;?></td>
							    		
							    		<td>
							    		    <?php echo $q->daterequested;?>
							    			<?php if($q->willcall){
							    			    echo '<br/>For Pickup/Will Call';
							    			}?>
							    		</td>
							    		<td><?php echo $q->notes;?></td>
							    		<td width="120">
							    			<?php if(@$q->postatus=='Pending' || !@$q->postatus){?>
							    			<input type="radio" class="statusradio" name="postatus[<?php echo $q->id;?>]" value="Pending" checked>
							    			Pending
							    			<br/>
							    			<?php }?>
							    			<input type="radio" class="statusradio" name="postatus[<?php echo $q->id;?>]" value="Accepted" <?php if(@$q->postatus=='Accepted'){echo 'CHECKED';}?>>
							    			Accept
							    			<br/>
							    			<input type="radio" class="statusradio" name="postatus[<?php echo $q->id;?>]" value="Rejected" <?php if(@$q->postatus=='Rejected'){echo 'CHECKED';}?>>
							    			Reject
							    			
							    		</td>
							    		
							    	</tr>
							    	
							    	<?php }?>
							    	<tr>
							    		<td colspan="7">
											
							    		</td>
							    		<td>
											<input type="button" value="Accept All" onclick="selectall('Accepted')">
											<input type="button" value="Reject All" onclick="selectall('Rejected')">
							    		</td>
							    	</tr>
							    	<tr>
							    		<td>
											Quote#
							    		</td>
							    		<td colspan="7">
							    		<?php $sub=strtoupper($this->session->userdata('company')->title); $subst=substr($sub,0,4); $fstr=$subst."Q";?>
											<input type="text" name="quotenum" value="<?php if(isset($quotenum) && $quotenum!="") { echo $quotenum; } else { 
												echo $fstr;printf('%06d',($invid)); echo ".000";}?>" required/>
							    		</td>							    		
							    	</tr>
							    	<tr>
							    		<td>
											Attachment
							    		</td>
							    		<td colspan="7">
											<input type="file" name="quotefile"/>
											<?php if($quotefile){?>
											<a href="<?php echo site_url('uploads/quotefile/'.$quotefile);?>" target="_blank">View</a>
											<?php }?>
							    		</td>
							    	</tr>
							    	<tr>
							    		<td colspan="8">
											<input type="submit" value="Save PO" class="btn btn-primary"/>
							    		</td>
							    	</tr>
							    	</form>
							    	</tbody>
						    	</table>
								
								
                            </div>
                        </div>
                    </div>
                </div>
                
			
		</div>
	  </div> 