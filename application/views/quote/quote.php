<?php //echo '<pre>'; print_r($originalitems);die;?>
<?php echo "<script>var changetierurl='".site_url('company/changetier')."';</script>";?>
<?php echo "<script>var changeitemtierurl='".site_url('company/changeitemtier')."';</script>";?>
<?php echo "<script>var changepriceurl='".site_url('company/changeitemprice')."';</script>";?>
<?php // echo '<script>var getpriceqtydetails="' . site_url('site/getpriceqtydetails') . '";</script>' ?>
<?php echo '<script>var getpriceqtydetails="' . site_url('quote/getpriceqtydetails') . '";</script>' ?>
<?php echo "<script>var tier1=".$tiers->tier1.";</script>";?>
<?php echo "<script>var tier2=".$tiers->tier2.";</script>";?>
<?php echo "<script>var tier3=".$tiers->tier3.";</script>";?>
<?php echo "<script>var tier4=".$tiers->tier4.";</script>";?>
<script type="text/javascript">
<!--
$(document).ready(function(){
	$(".source1").select2();
	$(".source3").select2();
	$('input[type="checkbox"]').checkbox();
	$('.date').datepicker();
        $('.expire_date').datepicker();
	$('.substituterow').hide();
});

function setExpDate(interval){
    
    var today = new Date();
    var expDate = today;
    expDate.setDate(today.getDate() + interval);
    
    var dd = expDate.getDate();
    var mm = expDate.getMonth()+1; //January is 0!
    var yyyy = expDate.getFullYear();
    if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm} var newexpDate = mm+'/'+dd+'/'+yyyy;
    $('.expire_date').val(newexpDate);    
}

function calculatetotalprice(id)
{
	var quantityid = 'quantity'+id;
	var eaid = 'ea'+id;
	var totalpriceid = 'totalprice'+id;
	document.getElementById(totalpriceid).value = document.getElementById(eaid).value * document.getElementById(quantityid).value;
}

function s_calculatetotalprice(id)
{
	var quantityid = 's_quantity'+id;
	var eaid = 's_ea'+id;
	var totalpriceid = 's_totalprice'+id;
	document.getElementById(totalpriceid).value = document.getElementById(eaid).value * document.getElementById(quantityid).value;
}

function checksubstitute(id)
{
	if($('#substitute'+id).attr('checked'))
		$('#substituterow'+id).show();
	else
		$('#substituterow'+id).hide();
}
function updateitem(itemid,itemcode,itemname,price,heading)
{
	$("#itemmodal").modal();
	$("#itemform").trigger("reset");
	$("#itemformheading").html(heading);
	$("#itemformid").val(itemid);
	//$("#itemformcode").html(itemcode.replace('&quot;','"'));
	//$("#itemformname").html(itemname.replace('&quot;','"'));
	//$("#itemformprice").html(price);
	
	$("#itemformcodet").val(itemcode.replace('&quot;','"').replace('&apos;','"'));
	$("#itemformnamet").val(itemname.replace('&quot;','"').replace('&apos;','"'));
	$("#itemformpricet").val(price);
	
}
function askpricechange(price,itemid, id)
{
	if(confirm('Do you want to change the listprice for this item?'))
	{
		url = changepriceurl;
		
		$.ajax({
		      type:"post",
		      data: "itemid="+itemid+"&price="+price,
		      url: url
		    }).done(function(data){
			    var t0p = price;
			    var t1p =(parseFloat(parseFloat(price) + parseFloat(price * tier1 / 100))).toFixed(2);
			    var t2p = (parseFloat(parseFloat(price) + parseFloat(price * tier2 / 100))).toFixed(2);
			    var t3p = (parseFloat(parseFloat(price) + parseFloat(price * tier3 / 100))).toFixed(2);
			    var t4p = (parseFloat(parseFloat(price) + parseFloat(price * tier4 / 100))).toFixed(2);

			    var optionshtml = '';
			    optionshtml += '<option value="'+ t0p +'">Tier0 - '+ t0p +'</option>';
			    optionshtml += '<option value="'+ t1p +'">Tier1 - '+ t1p +'</option>';
			    optionshtml += '<option value="'+ t2p +'">Tier2 - '+ t2p +'</option>';
			    optionshtml += '<option value="'+ t3p +'">Tier3 - '+ t3p +'</option>';
			    optionshtml += '<option value="'+ t4p +'">Tier4 - '+ t4p +'</option>';
			    //alert(optionshtml);
			    $("#tier"+id).html(optionshtml);
		    });
	}
	else
	{
		$("#ismanual"+id).val('1');
		$("#tier"+id).hide();
	}
}
function asktierchange(pa,tier)
{
	tiers = tier.split(' - ');
	tier = tiers[0];
	//alert(tier);return;
	if(confirm('Do you want to change the tier for this company?'))
	{
		url = changetierurl;
		
		$.ajax({
		      type:"post",
		      data: "purchasingadmin="+pa+"&tier="+tier,
		      url: url
		    }).done(function(data){
			    
		    });
	}
}

function setitemtier(tierlevel, tierprice, itemid,purchasingadmin){
	
	if(confirm('Do you want to change the tier for this item?'))
	{
		url = changeitemtierurl;
		var notes = '*Given '+tierlevel+' price';
		$.ajax({
		      type:"post",
		      data: "purchasingadmin="+purchasingadmin+"&tier="+tierlevel+"&itemid="+itemid+'&notes='+notes,
		      url: url
		    }).done(function(data){
			    alert(data);
		    });
		    
		    $('#'+$("#hiddenpriceid").val()).val(tierprice);
		    
		    $('#'+$("#hiddennotesid").val()).html(notes);
	}
}

function viewPricelist(itemid, quantityid, priceid, purchasingadmin, itemcode, itemname, price, notelabel)
{
	$("#pricelist").modal();
	$("#itemnamebox").html('');
	$("#pricelistitemcode").html(itemcode);
	$("#pricelistitemname").html(itemname);
	price = Number(price);
	tier0price = price.toFixed(2);
	var selectbuttondefault = "<input type='button' class='btn btn-small' onclick='setitemtier(\"tier0\","+tier0price+","+itemid+","+purchasingadmin+")' value='Select'>";
	$("#pricelistdefault").html(tier0price+'&nbsp;&nbsp;&nbsp;'+selectbuttondefault);
	
	
	tier1price = Number(price + (tier1 * price/100)).toFixed(2);
	var selectbuttontier1 = "<input type='button' class='btn btn-small' onclick='setitemtier(\"tier1\","+tier1price+","+itemid+","+purchasingadmin+")' value='Select'>";
	$("#pricelisttier1").html(tier1price+'&nbsp;&nbsp;&nbsp;&nbsp;'+selectbuttontier1);
	
	tier2price = Number(price + (tier2 * price/100)).toFixed(2);
	var selectbuttontier2 = "<input type='button' class='btn btn-small' onclick='setitemtier(\"tier2\","+tier2price+","+itemid+","+purchasingadmin+")' value='Select'>";
	$("#pricelisttier2").html(tier2price+'&nbsp;&nbsp;&nbsp;&nbsp;'+selectbuttontier2);
	
	
	tier3price = Number(price + (tier3 * price/100)).toFixed(2);
	var selectbuttontier3 = "<input type='button' class='btn btn-small' onclick='setitemtier(\"tier3\","+tier3price+","+itemid+","+purchasingadmin+")' value='Select'>";
	$("#pricelisttier3").html(tier3price+'&nbsp;&nbsp;&nbsp;&nbsp;'+selectbuttontier3);
	
	
	tier4price = Number(price + (tier4 * price/100)).toFixed(2);
	var selectbuttontier4 = "<input type='button' class='btn btn-small' onclick='setitemtier(\"tier4\","+tier4price+","+itemid+","+purchasingadmin+")' value='Select'>";
	$("#pricelisttier4").html(tier4price+'&nbsp;&nbsp;&nbsp;&nbsp;'+selectbuttontier4);
	
	
	$("#hiddenitemid").val(itemid);
    $("#hiddenprice").val(price);
    $("#hiddenquantityid").val(quantityid);
    $("#hiddenpriceid").val(priceid);    
    $("#hiddenpurchaser").val(purchasingadmin);
    $("#itemnamebox").html(itemcode+"  /  "+itemname);
    $("#hiddennotesid").val(notelabel);   
}


/*function showqtydiscount(companyid){
 
 var itemid = $("#hiddenitemid").val();
 var price = $("#hiddenprice").val();
 
 var data = "itemid="+itemid+"&companyid="+companyid+"&price="+price;
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
 
}*/


function showqtydiscount(companyid){
 
 var itemid = $("#hiddenitemid").val();
 var price = $("#hiddenprice").val();
 var quantityid = $("#hiddenquantityid").val();
 var priceid = $("#hiddenpriceid").val();
 var purchaser = $("#hiddenpurchaser").val();
 
 var data = "itemid="+itemid+"&companyid="+companyid+"&price="+price+"&quantityid="+quantityid+"&priceid="+priceid+"&purchaser="+purchaser;

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
 $("#pricelist").css('display', 'block');
 url = changeitemtierurl;
 var itemid = $("#hiddenitemid").val();
 var purchasingadmin = $("#hiddenpurchaser").val();
 $.ajax({
 	type:"post",
 	data: "purchasingadmin="+purchasingadmin+"&tier="+tierlevel+"&itemid="+itemid+'&notes='+notes,
 	url: url
 }).done(function(data){
 	alert(data);
 });
 
 $('#'+$("#hiddennotesid").val()).html(notes);
}


function displaypricemodal(){
 
 $("#pricelist").css('display', 'block');
}
//-->
</script>


    <div class="content"> 
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title"> <a href="<?php echo site_url('quote/invitation_export').'/'.$invitekey; ?>" class="btn btn-green">Export</a> &nbsp;&nbsp;<a href="<?php echo site_url('quote/invitation_pdf').'/'.$invitekey; ?>" class="btn btn-green">View PDF</a><br />	
			<h3>
				Bid Invitations  <a class="pull-right btn btn-primary btn-xs btn-mini" href="<?php echo site_url('message/messages/'.$invitekey);?>">View Messages</a>
			</h3>
			<h4>
				<?php if($draft){?>
				RFQ is active, Pending Award, Your bid has been submitted. 
				You can still make revisions and re-submit your quote below. 
				<?php }else{?>
				RFQ is active, Pending Award, Please Submit Your Bid.
				<?php }?>
				
			</h4>
			
		</div>		
	   <div id="container">
		<div class="row">
                    <div class="col-md-12">
                        <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                                
                            </div>
                            <div class="grid-body no-border">
                                   
								<table class="no-more-tables general" border="0">
									<tr>
										<td>
										  <strong>
									      PO#: <?php echo $quote->ponum;?></td><td style="width:400px;">Revision History</td></tr>
									      <tr><td>
									      Due: <?php echo $quote->duedate;?><span style="margin-left:500px;"></td><td>Number of Revisions:&nbsp;<?php if(isset($revisionno)) echo $revisionno-1; else echo 0; ?></span></tr>
									      <td>Company: <?php echo $company->title;?>
									      <br/>
									      Contact: <?php echo $company->contact;?>
									      </strong>
									      </td><td>
									      <!-- <a target="_blank" href="<?php echo site_url('quote/viewquote/'.$quote->id); ?>">Original</a><br> -->									       <?php if(isset($bid->id)) { $quotearr = explode(".",$bid->quotenum);  ?> <a href="<?php echo site_url('quote/viewbid/'.$bid->id);?>">Quote #: &nbsp;<?php echo $quotearr[0].".000"; ?></a>&nbsp; Date: <?php if(isset($bid->submitdate)) echo date("m/d/Y", strtotime($bid->submitdate)); else echo ''; ?><br><?php } ?>
									     <?php  if(isset($revisionno)) { $quotearr = explode(".",$bid->quotenum);  for($i=2;$i<=$revisionno;$i++) { ?><a href="<?php echo site_url('quote/viewbids/'.$bid->id.'/'.$i);?>">Quote #: &nbsp;<?php echo $quotearr[0]."."; printf('%03d',($i-1)); ?></a>&nbsp; Date: <?php if(isset($bid->$i)) echo date("m/d/Y", strtotime($bid->$i)); else echo ''; ?> <br><?php } } ?> </td></tr>
									      <td colspan="2">   
									      	<br/><br/>
									      	Please enter your Price EA, Date Available and add any Notes you may <br/>
											have related to each item. When you are finished, Click the Save Quote <br/>
											button.<br/><br/>
											Thank You,<br/>
											<strong><?php if(isset($purchasingadmin->companyname)) echo $purchasingadmin->companyname?></strong>
									     	<br/><br/>
									     </td>
									 </tr>
								</table>
								
								<br/>
    								Tier Level:
    								<form method="post" action="<?php echo site_url('company/changetier/'.$invitation);?>">
    								  <input type="hidden" name="purchasingadmin" value="<?php if(isset($purchasingadmin->id)) echo $purchasingadmin->id?>"/>
    				    			  <select name="tier" onchange="this.form.submit()" style="width: auto;padding-top:5px;">
    				    				<option value="tier0" <?php if($patier=='tier0'){echo 'SELECTED';}?>>Tier 0</option>
    				    				<option value="tier1" <?php if($patier=='tier1'){echo 'SELECTED';}?>>Tier 1</option>
    				    				<option value="tier2" <?php if($patier=='tier2'){echo 'SELECTED';}?>>Tier 2</option>
    				    				<option value="tier3" <?php if($patier=='tier3'){echo 'SELECTED';}?>>Tier 3</option>
    				    				<option value="tier4" <?php if($patier=='tier4'){echo 'SELECTED';}?>>Tier 4</option>
    				    			  </select>
    				    			</form>
    			    			<br/>
						
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
							    		<th width="75">No Bid</th>
							    		<th>Subst</th>
							    	</tr>
							    	</thead>
							    	<tbody>
							    	
							    	<form id="olditemform"  method="post" action="<?php echo site_url('quote/placebid'); ?>" enctype="multipart/form-data"> 
								  	<input type="hidden" name="invitation" value="<?php echo $invitation;?>"/>
									<input type="hidden" id="draft" name="draft" value=""/>
									<?php foreach($quoteitems as $q)if(@$q->itemid){//print_r($q);?>
									<?php if(@$originalitems[$q->itemid]){?>
							    	<tr>
							    		<td>
							    		<?php echo $originalitems[$q->itemid]->itemname;?>
							    		<?php //if($q->showinventorylink){?>
							    		<a href="javascript:void(0)" 
							    			onclick="updateitem(<?php echo html_escape("'$q->itemid',
							    		'".htmlentities(@$q->companyitem->itemcode)."',
							    		'".htmlentities(@$q->companyitem->itemname)."',
							    		'".htmlentities(@$q->companyitem->ea)."',
							    		'".htmlentities(@$q->orgitem->itemname)."'");?>)">
							    			<i class="fa fa-edit"></i>
							    		</a>
							    		<?php //}?>
							    		<?php if($q->attachment){?>
							    			<a href="<?php echo site_url('uploads/item/'.$q->attachment);?>" target="_blank">View</a>
							    		<?php }?>
							    		</td>
							    		<td><?php echo $originalitems[$q->itemid]->quantity;?></td>
							    		<td><?php echo $originalitems[$q->itemid]->unit;?></td>
							    		<td>$<?php echo $originalitems[$q->itemid]->ea;?></td>
							    		<td><?php echo round($originalitems[$q->itemid]->ea * $originalitems[$q->itemid]->quantity,2);?></td>
							    		<td><?php echo $originalitems[$q->itemid]->daterequested;?></td>
							    		<td><?php echo $originalitems[$q->itemid]->notes;?></td>
							    		<td></td>
							    		<td></td>
							    	</tr>
							    	<?php }?>
							    	<tr>
							    		<td>
							    			<input type="hidden" name="costcode<?php echo $q->id;?>" value="<?php echo $q->costcode;?>"/>
								    		<input type="hidden" name="itemid<?php echo $q->id;?>" value="<?php echo $q->itemid;?>"/>
								    		<input type="hidden" id="itemcode<?php echo $q->id;?>" name="itemcode<?php echo $q->id;?>" value="<?php echo $q->itemcode;?>"/>
								    		<textarea id="itemname<?php echo $q->id;?>" style="width: 150px" name="itemname<?php echo $q->id;?>" required><?php echo htmlspecialchars_decode($q->itemname, ENT_COMPAT);//htmlentities($q->itemname);?></textarea>
							    		</td>
							    		<td><input type="text" class="nopad width50" id="quantity<?php echo $q->id;?>" name="quantity<?php echo $q->id;?>" value="<?php echo $q->quantity;?>" onblur="calculatetotalprice('<?php echo $q->id?>')"/></td>
							    		<td><input type="text" class="nopad width50" id="unit<?php echo $q->id;?>" name="unit<?php echo $q->id;?>" value="<?php echo $q->unit;?>"/></td>
							    		<td>
							    			<?php if(@$q->companyitem->ea){?>
							    			<a href="javascript:void(0)" onclick="viewPricelist('<?php echo $q->itemid; ?>','quantity<?php echo $q->id;?>','ea<?php echo $q->id;?>','<?php echo $q->purchasingadmin;?>','<?php echo htmlentities(@$q->companyitem->itemcode)?>','<?php echo htmlentities(@$q->companyitem->itemname)?>','<?php echo @$q->companyitem->ea?>', 'notelabel<?php echo $q->id;?>');">
							    				<i class="fa fa-search"></i>
							    			</a>
							    			<?php }?>
											<input type="text" class="highlight nonzero nopad width50 input-sm" id="ea<?php echo $q->id;?>" name="ea<?php echo $q->id;?>" value="<?php echo $q->ea;?>" onchange="calculatetotalprice('<?php echo $q->id?>'); //askpricechange(this.value,'<?php echo $q->itemid?>','<?php echo $q->id?>');"/> <label id="notelabel<?php echo $q->id;?>" name="notelabel<?php echo $q->id;?>" ><?php if(isset($q->notes)) echo $q->notes;?></label>
							    			<input type="hidden" id="ismanual<?php echo $q->id?>" name="ismanual<?php echo $q->id?>" value="<?php echo @$q->ismanual;?>"/>
							    		</td>
							    		<td>	
											<input type="text" id="totalprice<?php echo $q->id;?>" class="price nopad width50 input-sm" name="totalprice<?php echo $q->id;?>" value="<?php echo $q->totalprice;?>"/>
							    		</td>
							    		
							    		<td>
							    			<input type="text" class="date highlight nopad" name="daterequested<?php echo $q->id;?>" value="<?php echo $q->daterequested;?>" data-date-format="mm/dd/yyyy" onchange="$('#costcode<?php echo $q->id;?>').focus();" style="width: 100px;"/>
							    			
							    			<?php if($q->willcall){
							    			    echo '<br/>For Pickup/Will Call';
							    			}?>
							    			<input type="hidden" name="willcall<?php echo $q->id?>" value="<?php echo $q->willcall;?>"/>
							    		</td>
							    		<td><textarea style="width: 150px" id="notes<?php echo $q->id;?>" name="notes<?php echo $q->id;?>" class="highlight"><?php echo $q->notes;?></textarea></td>
							    		<td><input type="checkbox" name="nobid<?php echo $q->id;?>" value="1" class="checkbox nopad"/></td>
							    		<td><input type="checkbox" id="substitute<?php echo $q->id;?>" name="substitute<?php echo $q->id;?>" value="1" onchange="checksubstitute('<?php echo $q->id;?>');" class="checkbox nopad"/></td>
							    	</tr>
							    	
							    	<tr id="substituterow<?php echo $q->id;?>" class="substituterow">
							    		<td>
								    		<input type="hidden" name="s_itemid<?php echo $q->id;?>" value="<?php echo $q->itemid;?>"/>
							    			<input type="hidden" class="costcode nopad" name="s_costcode<?php echo $q->id;?>" value="<?php echo $q->costcode;?>"/>
							    			<input type="hidden" class="itemcode nopad" id="s_itemcode<?php echo $q->id;?>" name="s_itemcode<?php echo $q->id;?>" value="<?php echo $q->itemcode;?>"/>
							    			<textarea style="width: 150px" id="s_itemname<?php echo $q->id;?>" name="s_itemname<?php echo $q->id;?>" required></textarea>
							    		</td>
							    		<td><input type="text" class="nopad width50 input-sm" id="s_quantity<?php echo $q->id;?>" name="s_quantity<?php echo $q->id;?>" onblur="s_calculatetotalprice('<?php echo $q->id?>')"/></td>
							    		<td><input type="text" class="nopad width50 input-sm" id="s_unit<?php echo $q->id;?>" name="s_unit<?php echo $q->id;?>"/></td>
							    		<td>
											<input type="text" class="highlight nopad width50 input-sm" id="s_ea<?php echo $q->id;?>" name="s_ea<?php echo $q->id;?>" onblur="s_calculatetotalprice('<?php echo $q->id?>')"/>
							    			
							    			
							    		</td>
							    		<td>
											<input type="text" id="s_totalprice<?php echo $q->id;?>" name="s_totalprice<?php echo $q->id;?>" class="price nopad width50 input-sm"/>
							    		</td>
							    		
							    		<td>
							    			<?php if($q->willcall){
							    			    echo '<br/>For Pickup/Will Call';
							    			}?>
							    			<input style="width:100px;" type="text" class="date highlight nopad input-sm" name="s_daterequested<?php echo $q->id;?>" data-date-format="mm/dd/yyyy" onchange="$('#costcode<?php echo $q->id;?>').focus();"/>
							    			
							    			<input type="hidden" name="s_willcall<?php echo $q->id?>" value="<?php echo $q->willcall;?>"/>
							    		</td>
							    		<td><textarea style="width: 150px" id="s_notes<?php echo $q->id;?>" name="s_notes<?php echo $q->id;?>" class="highlight"></textarea></td>
							    		<td></td>
							    		<td></td>
							    	</tr>
							    	<?php }?>
							    	<tr>
							    		<td>
											Quote#
							    		</td>
							    		<td colspan="5">
											<input type="text" name="quotenum" value="<?php if(isset($quotenum) && $quotenum!="") { echo $quotenum; } elseif(isset($revisionno)) { echo "."; printf('%03d',($revisionno-1)); } else {  echo ".000";  } ?>"/>
							    		</td>
							    		<td>
											<?php // if($draft){?>
											<!--<a href="<?php // echo site_url('quote/viewbid/'.$bid->id);?>">View Quote</a>-->
											<?php // }?>
							    		</td>
							    		<td colspan="4">
											
							    		</td>
							    	</tr>
							    	<?php if($quote->quoteattachment){?>
							    	<tr>
							    		<td>
											Attachment By Purchasing Admin:
							    		</td>
							    		<td colspan="10">
                                        	<a href="<?php echo site_url('uploads/quote').'/'.$quote->quoteattachment ;?>" target="_blank">  
                                        	    View File
                                          </a>
							    		</td>
							    	</tr>
                                                                <?php }?>
							    	<tr>
							    		<td>
											Your Attachment
							    		</td>
							    		<td colspan="10">
											<input type="file" name="quotefile"/>
											<?php if($quotefile){?>
											<a href="<?php echo site_url('uploads/quotefile/'.$quotefile);?>" target="_blank">View File</a>
											<?php }?>
							    		</td>
							    	</tr>
                                                                <tr>
                                                                    <td> Expire Date</td>
                                                                    <td colspan="10"> <input type="text" name="expire_date" class="expire_date" value="<?php echo (isset($expire_date) && $expire_date!="" && $expire_date!="0000-00-00")?date('m/d/Y',  strtotime($expire_date)):'';?>"/>&nbsp; &nbsp; &nbsp; <a href="javascript:void(0)" onclick="setExpDate(30);">+30days</a>, <a href="javascript:void(0)" onclick="setExpDate(60);">+60Days</a>, <a href="javascript:void(0)" onclick="setExpDate(90);">+90Days</a></td>
                                                                </tr>
							    	<tr>
							    		<td colspan="11">
											<input type="button" value="Save Quote" class="btn btn-primary" onclick="$('#draft').val('Yes');$('#olditemform').submit();"/>
							    		</td>
							    	</tr>
							    	
							    	<input type="hidden" name="hiddenitemid" id="hiddenitemid" />
							    	<input type="hidden" name="hiddenprice" id="hiddenprice" />
							    	<input type="hidden" name="hiddenquantityid" id="hiddenquantityid" />
							    	<input type="hidden" name="hiddenpriceid" id="hiddenpriceid" />
							    	<input type="hidden" name="hiddenpurchaser" id="hiddenpurchaser" />
							    	<input type="hidden" name="hiddennotesid" id="hiddennotesid" />
							    	
							    	</form>
							    	</tbody>
						    	</table>
								
								
                            </div>
                        </div>
                    </div>
                </div>
                
			
		</div>
	  </div> 
	  
	  
	  
	   <div id="itemmodal" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
                    <div class="modal-dialog">
                      <form id="itemform" action="<?php echo site_url('inventory/updateitem/'.$invitation);?>" method="post">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
                          <br>
                          <i class="icon-credit-card icon-7x"></i>
                          <h4 class="semi-bold" id="myModalLabel">
                          Setup Inventory:
                          <span id="itemformheading"></span>
                          <input type="hidden" id="itemformid" name="itemid">
                          </h4>
                          <br>
                        </div>
                        
                        <div class="modal-body">
                          <div class="row form-row">
                            <div class="col-md-6">
                              <strong>Item Code:</strong>
                            </div>
                            <div class="col-md-6">
                              <input type="text" id="itemformcodet" name="itemcode" required>
                            </div>
                          </div>
                          <div class="row form-row">
                            <div class="col-md-6">
                              <strong>Item Name:</strong>
                            </div>
                            <div class="col-md-6">
                              <input type="text" id="itemformnamet" name="itemname" required>
                            </div>
                          </div>
                          <div class="row form-row">
                            <div class="col-md-6">
                              <strong>Item Price:</strong>
                            </div>
                            <div class="col-md-6">
                              <input type="text" id="itemformpricet" name="ea" required>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <input type="submit" class="btn btn-primary" value="Save Change"/>
                        </div>
                      </div>
                      </form>
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
          <br>
        </div>
        <div class="modal-body">
          <div class="row form-row">
            <div class="col-md-8">
              List Price: 
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