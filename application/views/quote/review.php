<?php //echo '<pre>'; print_r($quote);die;?>
<?php echo "<script>var changetierurl='".site_url('company/changetier')."';</script>";?>
<?php echo "<script>var changeitemtierurl='".site_url('company/changeitemtier')."';</script>";?>
<?php echo "<script>var changepriceurl='".site_url('company/changeitemprice')."';</script>";?>
<?php echo '<script>var getpriceqtydetails="' . site_url('quote/getpriceqtydetails') . '";</script>' ?>
<?php echo '<script>var setcompanypriceurl ="' . site_url('quote/setcompanyprice') . '";</script>' ?>
<?php if(@$tiers->tier1) echo "<script>var tier1=".$tiers->tier1."</script>";?>
<?php if(@$tiers->tier2)  echo "<script>var tier2=".$tiers->tier2."</script>";?>
<?php if(@$tiers->tier3)  echo "<script>var tier3=".$tiers->tier3."</script>";?>
<?php if(@$tiers->tier4)  echo "<script>var tier4=".$tiers->tier4."</script>";?>

<?php echo '<script>var getcompanypriceurl ="' . site_url('quote/getcompanyprice') . '";</script>' ?>

<?php echo '<script>var setcompanypriceurl ="' . site_url('quote/setcompanyprice') . '";</script>' ?>

<script type="text/javascript">

$(document).ready(function(){
$('.date').datepicker();
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


function setitemtier(tierlevel, tierprice, itemid,purchasingadmin){
	
	if(confirm('Do you want to change the tier for this item?'))
	{
		url = changeitemtierurl;
		var notes = '*Given '+tierlevel+' price';
		var quote = $("#hiddenquoteid").val();
		$.ajax({
		      type:"post",
		      data: "purchasingadmin="+purchasingadmin+"&tier="+tierlevel+"&itemid="+itemid+'&notes='+notes+'&quote='+quote,
		      url: url
		    }).done(function(data){
			    alert(data);
		    });
		    
		    $('#'+$("#hiddenpriceid").val()).val(tierprice);
		    
		    $('#'+$("#hiddennotesid").val()).html(notes);
		    
		    var priceidarr = $("#hiddenpriceid").val();
			var uniid = priceidarr.split("ea");
			calculatetotalprice(uniid[1]);		    
	}
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
	
	var selectbuttondefault = "<input type='button' class='btn btn-small' onclick='setitemtier(\"tier0\","+tier0price+","+itemid+","+purchasingadmin+")' value='Select'>";
	$("#pricelistdefault").html(tier0price+'&nbsp;&nbsp;&nbsp;'+selectbuttondefault);
	
	if (typeof tier1 !== "undefined") 
	{
		tier1price = Number(price + (tier1 * price/100)).toFixed(2);
	}
	else
	{
		tier1price = '';
	}	
	
	if(tier1price != '')
	{
		var selectbuttontier1 = "<input type='button' class='btn btn-small' onclick='setitemtier(\"tier1\","+tier1price+","+itemid+","+purchasingadmin+")' value='Select'>";
		$("#pricelisttier1").html(tier1price+'&nbsp;&nbsp;&nbsp;&nbsp;'+selectbuttontier1);
		$("#pricelisttierlabel1").css('display','');
	}
	
	if (typeof tier2 !== "undefined") 
	{
		tier2price = Number(price + (tier2 * price/100)).toFixed(2);
	}
	else
	{
		tier2price = '';
	}	
	
	if(tier2price != '')
	{		
		var selectbuttontier2 = "<input type='button' class='btn btn-small' onclick='setitemtier(\"tier2\","+tier2price+","+itemid+","+purchasingadmin+")' value='Select'>";
		$("#pricelisttier2").html(tier2price+'&nbsp;&nbsp;&nbsp;&nbsp;'+selectbuttontier2);
		$("#pricelisttierlabel2").css('display','');
	}	
	
	if (typeof tier3 !== "undefined") 
	{
		tier3price = Number(price + (tier3 * price/100)).toFixed(2);
	}
	else
	{
		tier3price = '';
	}	
	if(tier3price != '')
	{	
		var selectbuttontier3 = "<input type='button' class='btn btn-small' onclick='setitemtier(\"tier3\","+tier3price+","+itemid+","+purchasingadmin+")' value='Select'>";
		$("#pricelisttier3").html(tier3price+'&nbsp;&nbsp;&nbsp;&nbsp;'+selectbuttontier3);
		$("#pricelisttierlabel3").css('display','');
	}	
	
	if (typeof tier4 !== "undefined") 
	{
		tier4price = Number(price + (tier4 * price/100)).toFixed(2);
	}
	else
	{
		tier4price = '';
	}	
	if(tier4price != '')	
	{			
		var selectbuttontier4 = "<input type='button' class='btn btn-small' onclick='setitemtier(\"tier4\","+tier4price+","+itemid+","+purchasingadmin+")' value='Select'>";
		$("#pricelisttier4").html(tier4price+'&nbsp;&nbsp;&nbsp;&nbsp;'+selectbuttontier4);
		$("#pricelisttierlabel4").css('display','');
	}
	
	$("#hiddenitemid").val(itemid);
    $("#hiddenprice").val(price);
    $("#hiddenquantityid").val(quantityid);
    $("#hiddenpriceid").val(priceid);    
    $("#hiddenpurchaser").val(purchasingadmin);
    $("#itemnamebox").html(itemcode+"  /  "+itemname);
    $("#hiddennotesid").val(notelabel);  
    $("#hiddenquoteid").val(quote); 
    if(<?php echo @$company->id!="" ?>)
    showqtydiscountprev('<?php echo $company->id; ?>');
    
}




function showqtydiscountprev(companyid){
 
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
 	if(data!="No Quantity Discount Available")
 	$('#discountbtn').css('display','block');
 }
 });
 
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


function selectquantity(qty, quant, price, priceid,notes,tierlevel)
{ 
 $("#"+quant).val(qty); 
 $("#"+priceid).val(price); 

 var uniid = priceid.split("ea");
 calculatetotalprice(uniid[1]);	
 
 $("#pricelist").css('display', 'block');
 url = changeitemtierurl;
 var itemid = $("#hiddenitemid").val();
 var purchasingadmin = $("#hiddenpurchaser").val();
 var quote = $("#hiddenquoteid").val();
 
 $.ajax({
 	type:"post",
 	data: "purchasingadmin="+purchasingadmin+"&tier="+tierlevel+"&itemid="+itemid+'&notes='+notes+'&quote='+quote+'&qty='+qty,
 	url: url
 }).done(function(data){
 	alert(data);
 });
 
 $('#'+$("#hiddennotesid").val()).html(notes);
}

function displaypricemodal(){
 
 $("#pricelist").css('display', 'block');
}



function allowonlydigits(e,elementid,errorid){
     //if the letter is not digit then display error and don't type anything
      if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which!=46) {    
        //display error message                
      $("#"+errorid).html("Digits Only").show().fadeOut("slow");  
      $("#"+errorid).css('color','red');
      return false;
    }

}


function calculatetotalprice(id)
{
	var quantityid = 'quantity'+id;
	var eaid = 'ea'+id;
	var totalpriceid = 'totalprice'+id;
	document.getElementById(totalpriceid).value = document.getElementById(eaid).value * document.getElementById(quantityid).value;
}




function setcompanypriceprompt(val,companyid,itemid,quote,purchasingadmin){

	if(confirm("Do you want to save this as company's price for this item?"))
	{
		if(val==0){
			alert('Price cannot be set to 0');	
		}else{
		$.ajax({
			type:"post",
			data: "companyid="+companyid+"&val="+val+"&itemid="+itemid+"&quote="+quote+"&purchasingadmin="+purchasingadmin,
			url: setcompanypriceurl
		}).done(function(data){
						
			alert(data);			
			
		});
		}
	}
}



function showcompanyprice(companyid,itemid,purchasingadmin,itemcode,itemname){
	
	$.ajax({
		type:"post",
		data: "companyid="+companyid+"&itemid="+itemid+"&purchasingadmin="+purchasingadmin,
		url: getcompanypriceurl
	}).done(function(data){
		
		if(data){
			$("#myModalbody").css('display','block');
			$("#myModalfooter").css('display','block');
			$("#myModalLabelchng").html('Company Price <br> <span id="pricelistitemcode2"></span> (<span id="pricelistitemname2"></span>)');
			$("#companypricemodal").modal();
			$("#pricelistitemcode2").html(itemcode);
			$("#pricelistitemname2").html(itemname);	
			$('#itemprice').val(data);
			var phtml = '<input type="button" class="btn btn-primary" onclick="setcompanypriceprompt2('+companyid+','+itemid+','+purchasingadmin+');" value="Update"/>';
			$("#pricebtn").html(phtml);
			
		}else{
			$('#itemprice').val('');
			$("#myModalbody").css('display','none');
			$("#myModalfooter").css('display','none');
			$("#companypricemodal").modal();						
			$("#myModalLabelchng").html('No Company Price Exists <br> <span id="pricelistitemcode2"></span> (<span id="pricelistitemname2"></span>) ');
			$("#pricelistitemcode2").html(itemcode);
			$("#pricelistitemname2").html(itemname);
		}
	});

}


function setcompanypriceprompt2(companyid,itemid,purchasingadmin){

	var val=$('#itemprice').val();
	if(confirm("Do you want to save this as company's price for this item?"))
	{
		if(val==0){
			alert('Price cannot be set to 0');	
		}else{
		$.ajax({
			type:"post",
			data: "companyid="+companyid+"&val="+val+"&itemid="+itemid+"&purchasingadmin="+purchasingadmin,
			url: setcompanypriceurl
		}).done(function(data){
            $("#companypricemodal").modal('hide');						
			alert(data);			
			
		});
		}
	}
}


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
									      PO#: <?php echo $quote->ponum;?></td>	<td style="width:400px;">Revision History</td></tr>
									      <tr><td>
									      Due: <?php echo $quote->duedate;?></td><td>Number of Revisions:&nbsp;<?php if(isset($revisionno)) echo $revisionno-1; else echo 0; ?></span></tr>	<tr><td>								      
									      Company: <?php echo $company->title;?>
									      <br/>
									      Contact: <?php echo $company->contact;?>
									      </strong>
									      
									     </td>									     
									     <td>
									      <!-- <a target="_blank" href="<?php echo site_url('quote/viewquote/'.$quote->id); ?>">Original</a><br> -->									       <?php if(isset($bid->id)) { $quotearr = explode(".",$bid->quotenum);  ?> <a href="<?php echo site_url('quote/viewbid/'.$bid->id);?>">Quote #: &nbsp;<?php echo $quotearr[0].".000"; ?></a>&nbsp; Date: <?php if(isset($bid->submitdate)) echo date("m/d/Y", strtotime($bid->submitdate)); else echo ''; ?><br><?php } ?>
									     <?php  if(isset($revisionno)) { $quotearr = explode(".",$bid->quotenum);  for($i=2;$i<=$revisionno;$i++) { ?><a href="<?php echo site_url('quote/viewbids/'.$bid->id.'/'.$i);?>">Quote #: &nbsp;<?php echo $quotearr[0]."."; printf('%03d',($i-1)); ?></a>&nbsp; Date: <?php if(isset($bid->$i)) echo date("m/d/Y", strtotime($bid->$i)); else echo ''; ?> <br><?php } } ?> </td></tr>
									      <td colspan="2">   
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
								    		<?php echo $q->itemname;?>
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
							    	
							    	
							    	<tr>
							    		<td>
							    			<input type="hidden" name="costcode<?php echo $q->id;?>" value="<?php echo $q->costcode;?>"/>
								    		<input type="hidden" name="itemid<?php echo $q->id;?>" value="<?php echo $q->itemid;?>"/>
								    		<input type="hidden" id="itemcode<?php echo $q->id;?>" name="itemcode<?php echo $q->id;?>" value="<?php echo $q->itemcode;?>"/>
								    		<textarea id="itemname<?php echo $q->id;?>" style="width: 150px" name="itemname<?php echo $q->id;?>" required><?php echo htmlspecialchars_decode($q->itemname, ENT_COMPAT);//htmlentities($q->itemname);?></textarea>
							    		</td>
							    		<td><input type="text" class="highlight nonzero nopad width50 input-sm" id="quantity<?php echo $q->id;?>" name="quantity<?php echo $q->id;?>" value="<?php echo $q->quantity;?>" onblur="calculatetotalprice('<?php echo $q->id?>')" onkeypress="return allowonlydigits(event,'quantity<?php echo $q->id;?>', 'eaerrmsg<?php echo $q->id;?>')" ondrop="return false;" onpaste="return false;" /> <br/> &nbsp;<span id="eaerrmsg<?php echo $q->id;?>"/>
							    								    		
							    		</td>
							    		<td><input type="text" class="nopad width50" id="unit<?php echo $q->id;?>" name="unit<?php echo $q->id;?>" value="<?php echo $q->unit;?>"/></td>
							    		<td>							    			
							    		
							    		   <?php  if(@$q->companyitem->ea && @$q->companyitem->ea!=0){?>
							    			<a href="javascript:void(0)" onclick="viewPricelist('<?php echo $q->itemid; ?>','quantity<?php echo $q->id;?>','ea<?php echo $q->id;?>','<?php echo $q->purchasingadmin;?>','<?php echo htmlentities(addslashes((@$q->companyitem->itemcode)?$q->companyitem->itemcode:$q->itemcode))?>','<?php echo htmlentities(addslashes((@$q->companyitem->itemname)?$q->companyitem->itemname:$q->itemname))?>','<?php echo @$q->companyitem->ea?>', 'notelabel<?php echo $q->id;?>','<?php echo @$q->quote;?>','<?php if (@$q->companyitem->itemcode!="" || @$q->companyitem->itemcode!="") echo "true"; else echo "false"; ?>')">
							    				<i class="fa fa-search"></i>
							    			</a>
							    			<?php }?><br>
							    		
											<input type="text" class="highlight nonzero nopad width50 input-sm" id="ea<?php echo $q->id;?>" name="ea<?php echo $q->id;?>" value="<?php if(@$q->ispriceset) echo $q->ea;?>" onchange="calculatetotalprice('<?php echo $q->id?>'); //askpricechange(this.value,'<?php echo $q->itemid?>','<?php echo $q->id?>');"  onkeypress="return allowonlydigits(event,'ea<?php echo $q->id;?>', 'eaerrmsg1<?php echo $q->id;?>')" ondrop="return false;" onpaste="return false;" onblur="setcompanypriceprompt(this.value,'<?php echo $company->id; ?>','<?php echo $q->itemid?>','<?php echo @$q->quote;?>','<?php echo @$q->purchasingadmin;?>');"  /> <br/> &nbsp;<span id="eaerrmsg1<?php echo $q->id;?>"/> <label id="notelabel<?php echo $q->id;?>" name="notelabel<?php echo $q->id;?>" ><?php if(isset($q->noteslabel)) echo $q->noteslabel;?></label>
							    			<input type="hidden" id="ismanual<?php echo $q->id?>" name="ismanual<?php echo $q->id?>" value="<?php echo @$q->ismanual;?>"/>  <br>  <?php if(@$q->ispriceset){ ?><a href="javascript:void(0)" onclick="showcompanyprice('<?php echo $company->id; ?>','<?php echo $q->itemid?>','<?php echo @$q->purchasingadmin;?>','<?php echo htmlentities(addslashes((@$q->companyitem->itemcode)?$q->companyitem->itemcode:$q->itemcode))?>','<?php echo htmlentities(addslashes((@$q->companyitem->itemname)?$q->companyitem->itemname:$q->itemname))?>')">
							    			Edit Company Price
							    		</a><?php }?>
							    		</td>
							    		<td>	
											<input type="text" id="totalprice<?php echo $q->id;?>" class="price highlight nonzero nopad width50 input-sm" name="totalprice<?php echo $q->id;?>" value="<?php echo $q->totalprice;?>" onkeypress="return allowonlydigits(event,'ea<?php echo $q->id;?>', 'eaerrmsg2<?php echo $q->id;?>')" ondrop="return false;" onpaste="return false;" /> <br/> &nbsp;<span id="eaerrmsg2<?php echo $q->id;?>"/>
							    		</td>
							    		
							    		<td>
							    			<input type="text" class="date highlight nopad" name="daterequested<?php echo $q->id;?>" value="<?php echo $q->daterequested;?>" data-date-format="mm/dd/yyyy" onchange="$('#costcode<?php echo $q->id;?>').focus();" style="width: 100px;"/>		    			
							    		</td>
							    		<td><textarea style="width: 150px" id="notes<?php echo $q->id;?>" name="notes<?php echo $q->id;?>" class="highlight"><?php echo $q->notes;?></textarea></td>	
							    		<td>&nbsp;</td>						    		
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
											<input type="text" name="quotenum" value="<?php if(isset($revisionno) && isset($quotenum) && $quotenum!="") { $quotearr = explode(".",$bid->quotenum); echo $quotearr[0]."."; printf('%03d',($revisionno)); } elseif(isset($quotenum) && $quotenum!="") { echo $quotenum; } else { echo $fstr;   printf('%06d',($invid)); echo ".000"; } ?>"/>		
												
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
          <div class="row form-row" id="pricelisttierlabel1" style="display:none;">
            <div class="col-md-8">
              Tier1 Price: 
            </div>
            <div class="col-md-4">
              <span id="pricelisttier1"></span>
            </div>
          </div>
          <div class="row form-row" id="pricelisttierlabel2" style="display:none;">
            <div class="col-md-8">
              Tier2 Price: 
            </div>
            <div class="col-md-4">
              <span id="pricelisttier2"></span>
            </div>
          </div>
          <div class="row form-row" id="pricelisttierlabel3" style="display:none;">
            <div class="col-md-8">
              Tier3 Price: 
            </div>
            <div class="col-md-4">
              <span id="pricelisttier3"></span>
            </div>
          </div>
          <div class="row form-row" id="pricelisttierlabel4" style="display:none;">
            <div class="col-md-8">
              Tier4 Price: 
            </div>
            <div class="col-md-4">
              <span id="pricelisttier4"></span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
         <button id="discountbtn" class="btn btn-default" style="float: left;display:none;" type="button" onclick="showqtydiscount(<?php echo $company->id; ?>);" >View available qty. discounts</button> <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
	  
  
  
      <div id="companypricemodal" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
                    <div class="modal-dialog">
                      <form id="companypriceform" action="" method="post">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
                          <br>
                          <i class="icon-credit-card icon-7x"></i>
                          <h4 class="semi-bold" id="myModalLabelchng">
                          Company Price:           
                           <span id="pricelistitemcode2"></span>
          					(<span id="pricelistitemname2"></span>)               
                          </h4>
                          <br>
                        </div>
                        
                        <div class="modal-body" id="myModalbody">
                          <div class="row form-row">
                            <div class="col-md-6">
                              <strong>Price:</strong>
                            </div>
                            <div class="col-md-6">
                              <input type="text" id="itemprice">
                            </div>
                          </div>                          
                        </div>
                        <div class="modal-footer" id="myModalfooter">
                          <span id="pricebtn"><input type="button" class="btn btn-primary" value="Update"/></span>
                        </div>
                      </div>
                      </form>
                    </div>
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
  
  
  
