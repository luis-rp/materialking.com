

<script type="text/javascript">

function calculatetotalprice(id)
{
	var quantityid = 'quantity'+id;
	var eaid = 'ea'+id;
	var totalpriceid = 'totalprice'+id;
	document.getElementById(totalpriceid).value = document.getElementById(eaid).value * document.getElementById(quantityid).value;
}

</script>

<div id="content">
 <?php echo $this->session->flashdata('message'); ?>	
    <div class="container">
        <div id="main">
            <div class="row">
                  <div class="col-md-12">
                   <div class="grid simple">
                       <div class="grid-title no-border">
                       <h4>Bid Invitation</h4>
                       </div>
                       <div class="grid-body no-border">                                
								<table class="no-more-tables general" border="0">
									<tr>
									    <td><strong>PO#:</strong><?php echo $quote->ponum;?></td>
									</tr>
									<tr>
									     <td><strong>Due:</strong><?php echo $quote->duedate;?></td>
								    </tr>
								    <tr>
									     <td><strong>Project:</strong><?php if(isset($project)) echo $project->title;?></td>
								    </tr>
								    <tr>
								         <td>   
									      	
									      	Please enter your Price EA, Date Available and add any Notes you may <br/>
											have related to each item. When you are finished, Click the Save Quote <br/>
											button.<br/><br/>
											Thank You,<br/>
											<strong>
											<?php if(isset($purchasingadmin->companyname)) echo $purchasingadmin->companyname?>
											</strong>
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
							    		
							    	</tr>
							    	</thead>
							    	<tbody>
							    	
							    	<form id="olditemform"  method="post" action="<?php echo site_url('quote/placebid'); ?>" enctype="multipart/form-data"> 
								  	<input type="hidden" name="invitation" value="<?php echo $invitation;?>"/>
									<input type="hidden" id="draft" name="draft" value=""/>
									<?php foreach($quoteitems as $q)if(@$q->itemid){?>
									<?php if(@$originalitems[$q->itemid]){?>
							    	<tr>
							    		<td><?php echo $originalitems[$q->itemid]->itemname;?></td>
							    		<td><?php echo $originalitems[$q->itemid]->quantity;?></td>
							    		<td><?php echo $originalitems[$q->itemid]->unit;?></td>
							    		<td>$<?php echo $originalitems[$q->itemid]->ea;?></td>
							    		<td><?php echo round($originalitems[$q->itemid]->ea * $originalitems[$q->itemid]->quantity,2);?></td>
							    		<td><?php echo $originalitems[$q->itemid]->daterequested;?></td>
							    		<td><?php echo $originalitems[$q->itemid]->notes;?></td>						    		
							    	</tr>
							    	<?php }?>
							    	<tr>
							    		<td>
							    			<input type="hidden" name="costcode<?php echo $q->id;?>" value="<?php echo $q->costcode;?>"/>
								    		<input type="hidden" name="itemid<?php echo $q->id;?>" value="<?php echo $q->itemid;?>"/>
								    		<input type="hidden" id="itemcode<?php echo $q->id;?>" name="itemcode<?php echo $q->id;?>" value="<?php echo $q->itemcode;?>"/>
							    		</td>
							    		
							    		<td><input type="text" class="highlight nonzero nopad width50 input-sm" id="quantity<?php echo $q->id;?>" 
							    			name="quantity<?php echo $q->id;?>" value="<?php echo $q->quantity;?>" 
							    			onblur="calculatetotalprice('<?php echo $q->id?>')" onkeypress="return allowonlydigits(event,'quantity<?php echo $q->id;?>', 'eaerrmsg<?php echo $q->id;?>')" ondrop="return false;" onpaste="return false;" /> <br/> &nbsp;<span id="eaerrmsg<?php echo $q->id;?>"/>							    								</td>
							    		
							    		<td><input type="text" class="nopad width50" id="unit<?php echo $q->id;?>" name="unit<?php echo $q->id;?>" value="<?php echo $q->unit;?>"/></td>
							    		
							    		<td>
											<input type="text" class="highlight nonzero nopad width50 input-sm" id="ea<?php echo $q->id;?>" name="ea<?php echo $q->id;?>" value="<?php echo $q->ea;?>" onchange="calculatetotalprice('<?php echo $q->id?>');"  onkeypress="return allowonlydigits(event,'ea<?php echo $q->id;?>', 'eaerrmsg1<?php echo $q->id;?>')" ondrop="return false;" onpaste="return false;" /> <br/> &nbsp;<span id="eaerrmsg1<?php echo $q->id;?>"/> 
											<label id="notelabel<?php echo $q->id;?>" name="notelabel<?php echo $q->id;?>" ><?php if(isset($q->noteslabel)) echo $q->noteslabel;?></label>
							    			<input type="hidden" id="ismanual<?php echo $q->id?>" name="ismanual<?php echo $q->id?>" value="<?php echo @$q->ismanual;?>"/>
							    		</td>
							    		
							    		<td>	
											<input type="text" id="totalprice<?php echo $q->id;?>" class="price highlight nonzero nopad width50 input-sm" name="totalprice<?php echo $q->id;?>" value="<?php echo $q->totalprice;?>" onkeypress="return allowonlydigits(event,'ea<?php echo $q->id;?>', 'eaerrmsg2<?php echo $q->id;?>')" ondrop="return false;" onpaste="return false;" /> <br/> &nbsp;<span id="eaerrmsg2<?php echo $q->id;?>"/>
							    		</td>
							    		
							    		<td>
							    			<input type="text" class="date highlight nopad" name="daterequested<?php echo $q->id;?>" value="<?php echo $q->daterequested;?>" data-date-format="mm/dd/yyyy"  style="width: 100px;"/>
							    			
							    			<?php if($q->willcall){
							    			    echo '<br/>For Pickup/Will Call';
							    			}?>
							    			<input type="hidden" name="willcall<?php echo $q->id?>" value="<?php echo $q->willcall;?>"/>
							    		</td>
							    		
							    		<td><textarea style="width: 150px" id="notes<?php echo $q->id;?>" name="notes<?php echo $q->id;?>" class="highlight"><?php echo $q->notes;?></textarea></td>
							    		
							    	</tr>	
							    	<?php  }?>
							    	<tr>
							    		<td>
											Quote#
							    		</td>
							    		<td colspan="5">
							    		
											<input type="text" name="quotenum" value=""/>
							    		</td>
							    		
							    		<td colspan="4">
											
							    		</td>
							    	</tr>
					
							    	<tr>
							    		<td>
											Your Attachment
							    		</td>
							    		<td colspan="7">
											<input type="file" name="quotefile"/>
							    		</td>
							    	</tr>
                                                               
							    	<!--<tr>
							    		<td colspan="7">
											<input type="button" value="Save Quote" class="btn btn-primary" onclick="$('#olditemform').submit();"/>
							    		</td>
							    	</tr>-->
							    	
							    	<input type="hidden" name="hiddenitemid" id="hiddenitemid" />
							    	<input type="hidden" name="hiddenprice" id="hiddenprice" />
							    	<input type="hidden" name="hiddenquantityid" id="hiddenquantityid" />
							    	<input type="hidden" name="hiddenpriceid" id="hiddenpriceid" />
							    	<input type="hidden" name="hiddenpurchaser" id="hiddenpurchaser" />
							    	<input type="hidden" name="hiddennotesid" id="hiddennotesid" />
							    	<input type="hidden" name="hiddenquoteid" id="hiddenquoteid" />
							    	
							    	</form>
							    	</tbody>
						    	</table>
								
								
                            </div>
                        </div>
                    </div>             
            </div>
        </div>
    </div>

</div>





	  
	  
	  
	  

        
  
	  
	  			
