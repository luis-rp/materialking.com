<?php //echo '<pre>'; print_r($quote);die;?>
<?php echo '<script>var getpriceqtydetails="' . site_url('quote/getpriceqtydetails') . '";</script>' ?>

<?php echo "<script>var tier1=".$tiers->tier1.";</script>";?>
<?php echo "<script>var tier2=".$tiers->tier2.";</script>";?>
<?php echo "<script>var tier3=".$tiers->tier3.";</script>";?>
<?php echo "<script>var tier4=".$tiers->tier4.";</script>";?>

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


function viewPricelist(itemid, quantityid, priceid, purchasingadmin, itemcode, itemname, price, notelabel, quote, isdefault)
{
	$("#pricelist").modal();
	$("#itemnamebox").html('');
	$("#pricelistitemcode").html(itemcode);
	$("#pricelistitemname").html(itemname);	
	if(isdefault == "false")
	$("#defaultmessage").html('No Default item data has been set. Click the edit icon next to the item name  <br> Or visit your inventory tab to set defaults for this item');
	price = Number(price);
	tier0price = price.toFixed(2);
	
	$("#pricelistdefault").html(tier0price+'&nbsp;&nbsp;&nbsp;');
	
	
	tier1price = Number(price + (tier1 * price/100)).toFixed(2);
	$("#pricelisttier1").html(tier1price+'&nbsp;&nbsp;&nbsp;&nbsp;');
	
	tier2price = Number(price + (tier2 * price/100)).toFixed(2);
	$("#pricelisttier2").html(tier2price+'&nbsp;&nbsp;&nbsp;&nbsp;');
	
	
	tier3price = Number(price + (tier3 * price/100)).toFixed(2);	
	$("#pricelisttier3").html(tier3price+'&nbsp;&nbsp;&nbsp;&nbsp;');
	
	
	tier4price = Number(price + (tier4 * price/100)).toFixed(2);
	$("#pricelisttier4").html(tier4price+'&nbsp;&nbsp;&nbsp;&nbsp;');
	
	
	$("#hiddenitemid").val(itemid);
    $("#hiddenprice").val(price);
    $("#hiddenquantityid").val(quantityid);
    $("#hiddenpriceid").val(priceid);    
    $("#hiddenpurchaser").val(purchasingadmin);
    $("#itemnamebox").html(itemcode+"  /  "+itemname);
    $("#hiddennotesid").val(notelabel);  
    $("#hiddenquoteid").val(quote); 
}




function showqtydiscount(companyid){
 
 var itemid = $("#hiddenitemid").val();
 var price = $("#hiddenprice").val();
 var quantityid = $("#hiddenquantityid").val();
 var priceid = $("#hiddenpriceid").val();
 var purchaser = $("#hiddenpurchaser").val();
 var quote = $("#hiddenquoteid").val();
 
 var data = "itemid="+itemid+"&companyid="+companyid+"&price="+price+"&quantityid="+quantityid+"&priceid="+priceid+"&purchaser="+purchaser+"&quote="+quote+"&potype=direct";

 $("#qtypricebox").html("");
 $.ajax({
 type:"post",
 data: data,
 sync: false,
 url: getpriceqtydetails
 }).done(function(data){
 if(data){
 $("#pricelist").css('display', 'none');
 $("#qtydiscount").modal();
 $("#qtypricebox").html(data);
 }
 });
 
}


function displaypricemodal(){
 
 $("#pricelist").css('display', 'block');
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
							    		<td><?php echo $q->ea;?>
							    		<br/>
							    		<?php  if(@$q->companyitem->ea && @$q->companyitem->ea!=0){?>
							    			<a href="javascript:void(0)" onclick="viewPricelist('<?php echo $q->itemid; ?>','quantity<?php echo $q->id;?>','ea<?php echo $q->id;?>','<?php echo $q->purchasingadmin;?>','<?php echo htmlentities(addslashes((@$q->companyitem->itemcode)?$q->companyitem->itemcode:$q->itemcode))?>','<?php echo htmlentities(addslashes((@$q->companyitem->itemname)?$q->companyitem->itemname:$q->itemname))?>','<?php echo @$q->companyitem->ea?>', 'notelabel<?php echo $q->id;?>','<?php echo @$q->quote;?>','<?php if (@$q->companyitem->itemcode!="" || @$q->companyitem->itemcode!="") echo "true"; else echo "false"; ?>')">
							    				<i class="fa fa-search"></i>
							    			</a>
							    			<?php }?>
							    		</td>
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
	  
	  
	  
	  
	  
	  
	  <div id="pricelist" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <br>
          <i class="icon-credit-card icon-7x"></i>
          <h4 class="semi-bold" id="myModalLabel">
          Price Details:
          <span id="pricelistitemcode"></span>
          (<span id="pricelistitemname"></span>)
          </h4>
          <span id="defaultmessage"></span>
          <br>
        </div>
        <div class="modal-body">
          <div class="row form-row">
            <div class="col-md-8">
              List  Price: 
            </div>
            <div class="col-md-4">
              <span id="pricelistdefault"></span>
            </div>
          </div>
          <div class="row form-row">
            <div class="col-md-8">
              Tier1 Price: 
            </div>
            <div class="col-md-4">
              <span id="pricelisttier1"></span>
            </div>
          </div>
          <div class="row form-row">
            <div class="col-md-8">
              Tier2 Price: 
            </div>
            <div class="col-md-4">
              <span id="pricelisttier2"></span>
            </div>
          </div>
          <div class="row form-row">
            <div class="col-md-8">
              Tier3 Price: 
            </div>
            <div class="col-md-4">
              <span id="pricelisttier3"></span>
            </div>
          </div>
          <div class="row form-row">
            <div class="col-md-8">
              Tier4 Price: 
            </div>
            <div class="col-md-4">
              <span id="pricelisttier4"></span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
         <button class="btn btn-default" style="float: left;" type="button" onclick="showqtydiscount(<?php echo $company->id; ?>);" >View available qty. discounts</button> <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
	  
  
  <div id="qtydiscount" aria-hidden="true" aria-labelledby="myModalLabel2" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
      	 <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button" onclick="displaypricemodal();">x</button>
          <br>
          <i class="icon-credit-card icon-7x"></i>
          <h4 class="semi-bold" id="myModalLabel">
          <div id="itemnamebox"></div>
           Quantity Discounts          
          </h4>          
        </div>        
        <div class="modal-body">

        <div id="qtypricebox"></div>                   
            
        <div class="modal-footer">          
          <button data-dismiss="modal" class="btn btn-default" type="button" onclick="displaypricemodal();">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div> 