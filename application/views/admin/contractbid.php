<!--
<?php echo "<script>var changetierurl='".site_url('company/changetier')."';</script>";?>
<?php echo "<script>var changeitemtierurl='".site_url('company/changeitemtier')."';</script>";?>
<?php echo "<script>var changepriceurl='".site_url('company/changeitemprice')."';</script>";?>

<?php echo '<script>var getpriceqtydetails="' . site_url('quote/getpriceqtydetails') . '";</script>' ?>
<?php echo "<script>var tier1=".$tiers->tier1.";</script>";?>
<?php echo "<script>var tier2=".$tiers->tier2.";</script>";?>
<?php echo "<script>var tier3=".$tiers->tier3.";</script>";?>
<?php echo "<script>var tier4=".$tiers->tier4.";</script>";?>-->

<?php //echo '<pre>'; print_r($quoteitems);die;?>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery.price_format.js"></script>
<script type="text/javascript" src="<?php // echo base_url();?>templates/admin/js/jquery-ui.js"></script>



<link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/fg.menu.css" type="text/css">
<link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/ui.all.css" type="text/css" id="color-variant-default">
<script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/fg.menu.2.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<script src="<?php echo base_url();?>templates/admin/js/jquery.ui.autocomplete.html.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
<link href="<?php echo base_url();?>templates/front/assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>


<!--<script type="text/javascript">
$(document).ready(function(){
	$(".source1").select2();
	$(".source3").select2();
	$('input[type="checkbox"]').checkbox();
	$('.date').datepicker();
        $('.expire_date').datepicker();
	$('.substituterow').hide();
});



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
	}
}

function viewPricelist(itemid, quantityid, priceid, purchasingadmin, itemcode, itemname, price, notelabel, quote)
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
    $("#hiddenquoteid").val(quote); 
}


function showqtydiscount(companyid){
 
 var itemid = $("#hiddenitemid").val();
 var price = $("#hiddenprice").val();
 var quantityid = $("#hiddenquantityid").val();
 var priceid = $("#hiddenpriceid").val();
 var purchaser = $("#hiddenpurchaser").val();
 var quote = $("#hiddenquoteid").val();
 
 var data = "itemid="+itemid+"&companyid="+companyid+"&price="+price+"&quantityid="+quantityid+"&priceid="+priceid+"&purchaser="+purchaser+"&quote="+quote;

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

</script>-->


<script type="text/javascript">

function calculatetotalprice(id)
{	
	var eaid = 'ea'+id;
	var totalpriceid = 'totalprice'+id;
	document.getElementById(totalpriceid).value = document.getElementById(eaid).value;
}


</script>

<section class="row-fluid">
   <!-- <div class="content"> 
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
			
		</div>		-->
   
   <h3 class="box-header">Bid Invitations</h3>
	<div class="box">
	  <div class="span12">
	   <div class="datagrid-example">
                                    
								<table class="table table-bordered datagrid" >
									<tr>
										<td>
										  <strong>Title
									      : <?php echo $quote->ponum;?></td><td style="width:400px;">Revision History</td></tr>
									      <tr><td>
									      Due: <?php echo $quote->duedate;?><span style="margin-left:500px;"></td><td>Number of Revisions:&nbsp;<?php if(isset($revisionno)) echo $revisionno-1; else echo 0; ?></span></tr>
									      <td>Company: <?php echo $company->companyname;?>
									      <br/>
									      Contact: <?php echo $company->username;?>
									      </strong>
									      </td><td>
									      <!-- <a target="_blank" href="<?php echo site_url('admin/quote/viewquote/'.$quote->id); ?>">Original</a><br> -->									       <?php if(isset($bid->id)) { $quotearr = explode(".",$bid->quotenum);  ?> <a href="<?php echo site_url('admin/quote/viewbid/'.$bid->id);?>">Quote #: &nbsp;<?php echo $quotearr[0].".000"; ?></a>&nbsp; Date: <?php if(isset($bid->submitdate)) echo date("m/d/Y", strtotime($bid->submitdate)); else echo ''; ?><br><?php } ?>
									     <?php  if(isset($revisionno)) { $quotearr = explode(".",$bid->quotenum);  for($i=2;$i<=$revisionno;$i++) { ?><a href="<?php echo site_url('admin/quote/viewbids/'.$bid->id.'/'.$i);?>">Quote #: &nbsp;<?php echo $quotearr[0]."."; printf('%03d',($i-1)); ?></a>&nbsp; Date: <?php if(isset($bid->$i)) echo date("m/d/Y", strtotime($bid->$i)); else echo ''; ?> <br><?php } } ?> </td></tr>
									      <td colspan="2">   
									      	<br/><br/>
									      	Please review the contract below and submit your bid. <br/>
											Enter any questions or notes you may have and  <br/>
											when you are finished click the save bid button.<br/><br/>
											Thank You,<br/>
											<strong><?php if(isset($purchasingadmin->companyname)) echo $purchasingadmin->companyname?></strong>
									     	<br/><br/>
									     </td>
									 </tr>
								</table>								
								
    			    			<br/>
						
    			    			<?php if($invitations) {  ?> 		   
                                     <table id="datatable" class="table table-bordered datagrid">
                                        <thead>
                                            <tr>
                                                <th style="width:10%">Title</th>
                                                <th style="width:10%">Date Due</th>
                                                <th style="width:10%">Date Received</th>
                                                <th style="width:10%">Date Sent</th>
                                                <th style="width:10%">Award Date</th>
                                                <th style="width:20%">Status</th>
                                                <th style="width:15%">Bid</th>
                                                <th style="width:30%">Bid Progress</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
							              <?php
									    	$i = 0;									    	
									    	foreach($invitations as $inv)
									    	{
									    		//echo "<pre>"; print_r($invitations); die;
									    		$i++;
									      ?>
                                            <tr>
                                            
                                                <td class="v-align-middle">
                                                <?php if(!($inv->status == 'New'||$inv->status == 'Processing')){?>
	                                                <a href="<?php echo site_url('admin/quote/contractitems/'.$inv->quote);?>">
	                                                	<?php echo $inv->ponum;?>
	                                                </a>
                                                <?php }else{?>
                                                	<?php echo $inv->ponum;?>
                                                <?php }?>
                                                </td>
                                                
                                                <td class="v-align-middle"><?php echo date('m/d/Y',strtotime($inv->quotedetails->duedate));?></td>                                      								<td class="v-align-middle"><?php if(isset($inv->senton)) echo date('m/d/Y',strtotime($inv->senton));?></td>
                                          <td class="v-align-middle"><?php if(isset($inv->daterequested))   echo date('m/d/Y',strtotime($inv->daterequested));?></td>
                                                <td class="v-align-middle"><?php echo date('m/d/Y',strtotime($inv->quotedetails->podate));?></td>                                        
                                                <td class="v-align-middle"><?php echo $inv->status;?></td>
                                                <td>
                                                	<?php if($inv->status == 'New'||$inv->status == 'Processing'){?>
                                                		<?php if($inv->quotedetails->potype=='Contract'){?>
                                                    	<a href="<?php echo site_url('admin/quote/invitation/'.$inv->invitation);?>">
    										    			<span class="label label-success">BID</span>
    										    		</a>    										    		
										    		<?php }elseif($inv->awardedtothis){?>
                                                	<a href="<?php echo site_url('quote/track/'.$inv->quote.'/'.$inv->award);?>">
										    			<span class="label label-success">TRACK</span>
										    		</a>
										    		<?php } } ?>
										    		<span class="label label-important"><?php echo $inv->status?></span>
										    	</td>
                                                <td class="v-align-middle">
                                                    <div class="progress progress-striped active progress-large">
                      									<div class="progress-bar <?php echo $inv->mark; ?>" style="width: <?php echo $inv->progress?>%;" data-percentage="<?php echo $inv->progress?>%"><?php echo $inv->progress?>% </div>
                   									</div>
                    
                                                </td>
                                            </tr>
                                          <?php } ?>
                                        </tbody>
                                    </table>
                                                 
                <?php }  else { ?>              
                    <div class="errordiv">
      				 <div class="alert alert-info"><button data-dismiss="alert" class="close"></button>
                         <div class="msgBox"> No Quotations Detected on System. </div>                                         
                     </div>
      				</div>
                <?php }?>
    			    			
							    <table class="table no-more-tables general">
							    	<thead>
							    	<tr>
							    		<th>File</th>	
							    		<th>Item Description</th>							    		
							    		<th>Price</th>
							    		<th>Total</th>		 
							    		<th>Notes</th>
							    		<th>NO BID</th>	   									    					    		
							    	</tr>
							    	</thead>
							    	<tbody>
							    	
							    	<form id="olditemform"  method="post" action="<?php echo site_url('admin/quote/placecontractbid'); ?>" enctype="multipart/form-data"> 
								  	<input type="hidden" name="invitation" value="<?php echo $invitation;?>"/>
									<input type="hidden" id="draft" name="draft" value=""/>
									<?php foreach($quoteitems as $q){?>									
							    	<tr>
							    		<td>	
							    			<input type="hidden" name="costcode<?php echo $q->id;?>" value="<?php echo $q->costcode;?>"/>	    			
							    			<?php if(@$q->attach && file_exists("./uploads/quote/".$q->attach)){?>
				                        	<br>
				                        	<a href="<?php echo site_url('uploads/quote').'/'.@$q->attach ;?>" target="_blank">  &nbsp;
				                        	View File
				                          	</a>
				                          	<?php }?>
				                          	<input type="hidden" name="attach<?php echo $q->id;?>" id="attach<?php echo $q->id;?>" value="<?php echo $q->attach;?>"/>
		    							</td>						    		
							    		<td>
							    		<input type="hidden" name="itemname<?php echo $q->id;?>" id="itemname<?php echo $q->id;?>" value="<?php echo $q->itemname;?>"/>
			    							<?php echo htmlentities($q->itemname);?></td>						    		
							    		<td>							    			
											<input type="text" class="highlight nonzero nopad width50 input-sm" id="ea<?php echo $q->id;?>" name="ea<?php echo $q->id;?>" value="<?php echo $q->ea;?>" onchange=" calculatetotalprice('<?php echo $q->id?>'); //askpricechange(this.value,'<?php echo $q->itemid?>','<?php echo $q->id?>');"/>							    			
							    		</td>
							    		<td>	
											<input type="text" id="totalprice<?php echo $q->id;?>" class="price nopad width50 input-sm" name="totalprice<?php echo $q->id;?>" value="<?php echo $q->totalprice;?>"/>
							    		</td>	
							    		<td><textarea style="width: 150px" id="s_notes<?php echo $q->id;?>" name="s_notes<?php echo $q->id;?>" class="highlight"><?php if(isset($q->notes)) echo $q->notes; ?></textarea></td>
							    		<td><input type="checkbox" name="nobid<?php echo $q->id;?>" value="1" class="checkbox nopad"/></td>
							    	</tr>						    			    	
							    	
							    	<?php }?>
							    	
							    	
							    	
							    
			
							    	<tr>
							    		<td>
											Quote#
							    		</td>
							    		<td colspan="4">
											<input type="text" name="quotenum" value="<?php if(isset($quotenum) && $quotenum!="") { echo $quotenum; } elseif(isset($revisionno)) { echo "."; printf('%03d',($revisionno-1)); } else {  echo ".000";  } ?>"/>
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
							    		<td colspan="4">
											<input type="file" name="quotefile"/>
											<?php if($quotefile){?>
											<a href="<?php echo site_url('uploads/quotefile/'.$quotefile);?>" target="_blank">View File</a>
											<?php }?>
							    		</td>
							    	</tr>
                                                               
							    	<tr>
							    		<td colspan="5">
											<input type="button" value="Save Quote" class="btn btn-primary" onclick="$('#draft').val('Yes');$('#olditemform').submit();"/>							    		</td>
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
                
			
		
	  
</section>	  
	
	  
	  
	  
  
 
  
	  
	  			