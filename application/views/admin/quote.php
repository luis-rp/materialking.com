<?php //print_r($quoteitems);die;?>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery.price_format.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<script src="<?php echo base_url();?>templates/admin/js/jquery.ui.autocomplete.html.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">

<!-- <script src="<?php echo base_url();?>templates/admin/js/jqBootstrapValidation.js"></script> -->

<script type="text/javascript">
<!--
$(document).ready(function(){
	//$('#intro').wysihtml5();
	//$('#content').wysihtml5();
	$('#deliverydate').datepicker();
	$('#podate').datepicker();
	$('#duedate').datepicker();
	$('.daterequested').datepicker();
	//$('textarea').autosize();
	//$('.price').priceFormat({prefix: '$ ', centsSeparator: '.',thousandsSeparator: ','});
	//$("input,select,textarea").not("[type=submit]").jqBootstrapValidation();
	$('html, body').animate({scrollTop:$(document).height()}, 'slow');
	$("#showpricelink").hide();
        $("#showpricelinkbrow").show();
        
       //toggleradius();
});

//datedefault = true;
//ccdefault = true;
function defaultdate(dateval)
{
	//if(datedefault)
	if(dateval)
	if(confirm('Do you want to make this date default for this session?'))
	{
		$("#makedefaultdeliverydate").val('1');
		$(".daterequested").val(dateval);
	}
}

function defaultcostcode(code)
{

	//if(ccdefault)
    if(code!=''){
    	if(confirm('Do you want to make this costcode default for this session?'))
    	{
    		url = "<?php echo site_url('admin/quote/makedefaultcostcode');?>";
    		$.ajax({
    		      type:"post",
    		      data: "defaultcostcode="+code,
    		      url: url
    		    }).done(function(data){
    				$(".costcode").val(code);
    		    });
    	}
	}
}

function saveitemname(id)
{
	<?php if ($this->session->userdata('usertype_id') == 2){?>return false;<?php }?>
	var itemid = $("#itemid"+id).val();
	var itemname = $("#itemname"+id).val();
	var d = "itemid="+encodeURIComponent(itemid)+"&itemname="+itemname;
	alert(d);
	if(itemname=='')
		return false;
	if(confirm('Would you like to update item name in item code database?'))
	{
		url = "<?php echo site_url('admin/quote/updateitemnamewithcode');?>";
		alert(url);
		$.ajax({
		      type:"post",
		      data: d,
		      url: url
		    }).done(function(data){
				alert(data);
		    });
	}
}

function invite()
{
	var matches = [];
	$(".invite:checked").each(function() {
	    matches.push(this.value);
	});
	$('#invitees').val(matches.join(','));

	var remind = [];
	$(".remind:checked").each(function() {
	    remind.push(this.value);
	});
	$('#reminders').val(remind.join(','));

	var revise = [];
	$(".revision:checked").each(function() {
		revise.push(this.value);
	});
	$('#revisions').val(revise.join(','));
	
	$('#mainform').submit();

	return false;
}
function fetchItem(codeid)
{
	var itemcode = document.getElementById(codeid).value;
	prid = document.getElementById('pid').value;
	var idid = codeid.replace('itemcode','itemid');
	var nameid = codeid.replace('itemcode','itemname');
	var unitid = codeid.replace('itemcode','unit');
	var eaid = codeid.replace('itemcode','ea');
	var quantityid = codeid.replace('itemcode','quantity');
	var notesid = codeid.replace('itemcode','notes');
	var totalpriceid = codeid.replace('itemcode','totalprice');
	
	var url = '<?php echo base_url()?>admin/quote/getitembycode';
	//alert(url);
    $.ajax({
      type:"post",
      data: "code="+encodeURIComponent(itemcode)+"&projectid="+prid,
      url: url
    }).done(function(data){
        var obj = $.parseJSON(data);
        if(obj.itemname !== undefined)
        {
	        document.getElementById(idid).value = obj.itemid;
	        document.getElementById(nameid).value = obj.itemname;
	        document.getElementById(unitid).value = obj.unit;
	        document.getElementById(eaid).value = obj.ea;
	        document.getElementById(notesid).value = obj.notes;
	        document.getElementById(totalpriceid).value = document.getElementById(quantityid).value * obj.ea;
        }
    });
}

function calculatetotalprice(id)
{
	var quantityid = 'quantity'+id;
	var eaid = 'ea'+id;
	var totalpriceid = 'totalprice'+id;
	document.getElementById(totalpriceid).value = document.getElementById(eaid).value * document.getElementById(quantityid).value;

}



function showhideviewprice(id)
{ // alert(id);
   // if(id==0){
   //     var itemcode = document.getElementById('subcatiditem').value;
   // }else{
	var itemcode = document.getElementById('itemcode'+id).value;

//    } //alert(itemcode);
	if(itemcode=='')
	{
		$("#showpricelink"+id).hide();
                $("#showpricelinkbrow"+id).show();

	}
	else
	{
		$("#showpricelink"+id).show();
                $("#showpricelinkbrow"+id).hide();
	}
}

function viewminprices(codeid)
{
	var itemid = document.getElementById(codeid).value;
	
	if(itemid=='')
	{
		return false;
	}
	var serviceurl = '<?php echo base_url()?>admin/itemcode/getcompanypricetable/';
	var d = "id="+itemid+"&codeid="+codeid;
	//alert(d);
	$.ajax({
	      type:"post",
	      url: serviceurl,
	      data: d
	    }).done(function(data){
	        $("#minpricemodal").html(data);
	        $("#minpricemodal").modal();
	    });
}

function viewminpricesbrow(codeid)
{
	var serviceurl = '<?php echo base_url()?>admin/itemcode/getcompanypricetablebrow/';
	//alert(serviceurl);
	$.ajax({
	      type:"post",
	      url: serviceurl
	    //  data: "code="+encodeURIComponent(itemcodeshow)+"&codeid="+codeid
	    }).done(function(data){  //alert(data);
	       // $("#minpriceitemcodebrow").html(itemcodeshow);
	        $("#minpricesbrow").html(data);
	        $("#minpricemodalbrow").modal();
	    });
}

function selectcompany(codeid, company, price)
{
	var priceid = codeid.replace('itemid','ea');
	$("#"+priceid).val(price);
}


//-->
</script>


<script type="text/javascript">
$(function() {
    <?php if($this->validation->itemchk=='1') { ?>
            $('#attachchkbox').attr('checked','checked');
            <?php } ?>
    xmltype6();
    //autocomplete
    $(".costcode").autocomplete({
        source: "<?php echo base_url(); ?>admin/quote/findcostcode",
        minLength: 1
    });
    
    //autocomplete
    $(".itemcode").autocomplete({
        source: "<?php echo base_url(); ?>admin/quote/finditemcode",
        minLength: 1,
        html: true
    });
 
});


function getcatitem(catid){
    var serviceurl = '<?php echo base_url()?>admin/itemcode/getcatitem';
	//alert(catid);
	$.ajax({
	      type:"post",
	      url: serviceurl,
	      data: "catid="+catid
	    }).done(function(data){

	        $("#catiditem").html(data);

	    });
}

function savclose()
{
	//alert('gdgds');
	var itemcode = document.getElementById('catiditem').value;
   	$("#itemcode").val(itemcode);
    fetchItem('itemcode');
}
</script>

<style>
.findtext
{
	color: #999;
	font-family: Century Gothic;
	font-size: 12px;
	padding-right: 10px;
}
</style>

<section class="row-fluid">
	<h3 class="box-header"><?php echo $heading; ?></h3>
	<div class="box">
		<div class="span12">
		   <?php echo $this->session->flashdata('message'); ?>
		   <?php echo @$message; ?>
		   <br/>
		   <a class="btn btn-primary" href="<?php echo site_url('admin/quote/index/'.$pid);?>">&lt;&lt; Back</a>
		   <br/>
		   <form id="mainform" class="form-horizontal" method="post" action="<?php echo $action; ?>"> 
		   <input type="hidden" name="id" value="<?php echo $this->validation->id;?>"/> 
		   <input type="hidden" name="pid" id="pid" value="<?php echo $pid;?>"/>
		   <input type="hidden" name="potype" value="<?php echo $this->validation->potype;?>"/>
		   <input type="hidden" id="invitees" name="invitees" value=""/>
		   <input type="hidden" id="reminders" name="reminders" value=""/>
		   <input type="hidden" id="revisions" name="revisions" value=""/>
		   <br/>
		    
		    <div class="control-group">
			    <div class="controlss">PO # &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; 
                  <input type="text" id="ponum" name="ponum" style="width: 20%" class="input small" value="<?php echo $this->validation->ponum; ?>" required>
			      
			      &nbsp; &nbsp; 
			      <?php if(0){?>
			      Company: &nbsp; &nbsp; &nbsp; 
					<select id="company" name="company">
					<?php foreach($companylist as $company){?>
						<option value="<?php echo $company->id;?>" <?php if($this->validation->company==$company->id){echo 'selected="selected"';}?>><?php echo $company->title;?></option>
					<?php }?>
					</select>
					<?php }?>
			      &nbsp; &nbsp; 
			      Subject: &nbsp; &nbsp; 
			      <input type="text" id="subject" name="subject" style="width: 26%" class="input" value="<?php echo $this->validation->subject; ?>">
			    </div>
			    <div class="controls">
			    	<?php echo $this->validation->ponum_error;?>
			    </div>
		    </div>
		    
		    <div class="control-group">
			    <div class="controlss">
			      Delivery Date: &nbsp; &nbsp; 
			      <input type="text" id="deliverydate" name="deliverydate" class="input small span2" onchange="defaultdate(this.value);"
			      	value="<?php echo $this->validation->deliverydate; ?>" data-date-format="mm/dd/yyyy">
			      <input type="hidden" id="makedefaultdeliverydate" name="makedefaultdeliverydate"/>
			       &nbsp; &nbsp; 
			      PO Date: &nbsp; &nbsp; 
			      <input type="text" id="podate" name="podate" class="input small span2"
			      	value="<?php echo $this->validation->podate; ?>" data-date-format="mm/dd/yyyy">
			      	&nbsp; &nbsp; &nbsp; &nbsp; 
			      Due Date: &nbsp; &nbsp; 
			      <input type="text" id="duedate" name="duedate" class="input small span2"
			      	value="<?php echo $this->validation->duedate; ?>" data-date-format="mm/dd/yyyy">
			      <input name="add" type="button" class="btn btn-primary" value="Save &amp; Continue" onclick="$('#mainform').submit();"/>
			    </div>
			    <div class="controls">
			    	<?php echo $this->validation->podate_error;?>
			    </div>
		    </div>
                  <input  type="hidden"  name="itemchk" id="itemchk" >
                  
		  </form>
		  <script  type="text/javascript">

       function xmltype6()
       {
//alert('dsfd');
           if(document.getElementById('attachchkbox').checked==true){
               document.getElementById('itemchk').value='1';

           }else{
                document.getElementById('itemchk').value='0';
           }
       }
//Changed On 21st jan 2014 Start
       function toggleradius(){
           if(document.getElementById('localresult').checked){
               $('#mileid').show();
                 getcomplist();

           }else{
               $('#mileid').hide();
               $('#locradius').val('');
               getcomplist();
           }
       }
       function getcomplist(){

           var localresult = document.getElementById('localresult').checked;
           var supplyresult = document.getElementById('supplynet').checked;
           var internetresult = document.getElementById('internetret').checked;
//           if(supplyresult==true){ supplyresult=1;}else{var supplyresult=0;}
           if(localresult!=true && internetresult!=true){
               document.getElementById('supplynet').setAttribute("checked","checked");
               supplyresult =1;
           }
           var radiusval = $('#locradius').val();
           if(localresult==true){ var localresult=1;}else{var localresult=0;}
           if(internetresult==true){ internetresult=1;}else{var internetresult=0;}


var serviceurl = '<?php echo base_url()?>admin/quote/getcompany_ajax';
	//alert(serviceurl);
	$.ajax({
	      type:"post",
	      url: serviceurl,
	      data: "localresult="+localresult+"&supplyresult="+supplyresult+"&internetresult="+internetresult+"&radiusval="+radiusval+"&id=<?php echo $this->validation->id;?>"
	    }).done(function(data){

                if(data==''){
                    $("#invitecomp").html('<b>No company found</b>');
                }else{
                    $("#invitecomp").html(data);
                }

	    });
       }
  // End
</script>
                     <hr/>
		  <?php if($this->validation->id && !$awarded){?>
			  <div class="pull-right">
			  	<?php if(!$costcodes){?>
			  	<font color="red">No costcodes exist for this project.</font>
			  	<?php }?>
			  	<a href="<?echo site_url('admin/costcode');?>">Manage Costcodes</a>
			  </div>
		    <div class="control-group">
			    <label class="control-label">Items</label>
                            
		    </div>
                  
                  <div align="right" style="padding-right: 20px;padding-bottom:10px;  ">
                      <b>Include Item Attachments <input type="checkbox" name="attachchkbox"  id="attachchkbox" onchange="xmltype6()" ></b></div>
		  <div class="control-group">
		    <table class="table table-bordered">
		    	<tr>
		    		<th>Item Code</th>
		    		<th>Item Name</th>
		    		<th>Qty.</th>
		    		<th>Unit</th>
		    		<th>Price EA</th>
		    		<th>Total Price</th>
		    		<th>Date Requested</th>
		    		<th>Cost Code</th>
		    		<th>Notes</th>
		    		<th>Delete</th>
		    	</tr>
		    	<?php if($quoteitems){?>
		    	<form id="olditemform" class="form-horizontal" method="post"
		    	action="<?php echo base_url(); ?>admin/quote/updateitems/<?php echo $this->validation->id;?>"> 
			  	<input type="hidden" name="quote" value="<?php echo $this->validation->id;?>"/>
		    	<?php foreach($quoteitems as $q){?>
		    	<tr>
		    		<td>
			    		<input type="hidden" name="company<?php echo $q->id;?>" />
		    			<input type="hidden" id="itemid<?php echo $q->id;?>" name="itemid<?php echo $q->id;?>" class="span itemid" value="<?php echo $q->itemid;?>"/>
			    		<input type="text" required class="span12 itemcode itemcodeold" id="itemcode<?php echo $q->id;?>" name="itemcode<?php echo $q->id;?>" value="<?php echo $q->itemcode;?>" onblur="fetchItem('itemcode<?php echo $q->id;?>');" onchange="showhideviewprice('<?php echo $q->id;?>');"/>
			    		<a href="javascript:void(0)" onclick="viewminprices('itemid<?php echo $q->id;?>')">View Prices</a>
		    		</td>
		    		<td>
			    		<textarea id="itemname<?php echo $q->id;?>" name="itemname<?php echo $q->id;?>" required <?php if ($this->session->userdata('usertype_id') == 2){echo 'readonly';}?>><?php echo htmlentities($q->itemname);?></textarea>
		    		</td>
		    		<td><input type="text" class="span12" id="quantity<?php echo $q->id;?>" name="quantity<?php echo $q->id;?>" value="<?php echo $q->quantity;?>" onblur="calculatetotalprice('<?php echo $q->id?>')" required/></td>
		    		<td><input type="text" class="span12" id="unit<?php echo $q->id;?>" name="unit<?php echo $q->id;?>" value="<?php echo $q->unit;?>" required/></td>
		    		<td>
		    			<div class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="text" class="span9" id="ea<?php echo $q->id;?>" name="ea<?php echo $q->id;?>" value="<?php echo $q->ea;?>" onblur="calculatetotalprice('<?php echo $q->id?>')" required/>
		    			</div>
		    		</td>
		    		<td>
		    			<div class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="text" id="totalprice<?php echo $q->id;?>" class="span9 price totalamount-old" name="totalprice<?php echo $q->id;?>" value="<?php echo $q->totalprice;?>" required/>
		    			</div>
		    		</td>
		    		
		    		<td>
		    			<input type="text" class="span12 daterequested" name="daterequested<?php echo $q->id;?>" value="<?php echo $q->daterequested;?>" data-date-format="mm/dd/yyyy" required onchange="defaultdate(this.value);$('#costcode<?php echo $q->id;?>').focus();"/>
		    			or<br/>
		    			<input type="checkbox" name="willcall<?php echo $q->id;?>" value="1" <?php if($q->willcall){echo 'CHECKED';}?>/>For Pickup/Will Call
		    		</td>
		    		<td>
		    			<select id="costcode<?php echo $q->id;?>" name="costcode<?php echo $q->id;?>" class="costcode" onchange="defaultcostcode(this.value)">
		    				<?php foreach($costcodes as $costcode){?>
		    				<option value="<?php echo $costcode->code;?>" 
		    				<?php if($q->costcode==$costcode->code){echo 'SELECTED';}?>>
		    				<?php echo $costcode->code;?>
		    				</option>
		    				<?php }?>
		    			</select>
		    		</td>
		    		<td><textarea id="notes<?php echo $q->id;?>" name="notes<?php echo $q->id;?>"><?php echo $q->notes;?></textarea></td>
		    		<td>
		    		<?php echo anchor ( 'admin/quote/deleteitem/' . $q->id.'/'.$this->validation->id, '<span class="icon-2x icon-trash"></span>', array ('class' => 'delete', 'onclick' => "return confirm('Are you sure want to Delete this item?')" ) ) ?>
					
					</td>
		    	</tr>
		    	<?php }?>
		    	<tr>
		    		<td colspan="10">
					<input type="submit" value="Update Items" class="btn btn-primary"/>
		    		</td>
		    	</tr>
		    	</form>
		    	<?php }?>
		    	<?php if(!$invited){?>
			  	<form id="newitemform" class="form-horizontal" method="post"
			  	action="<?php echo base_url(); ?>admin/quote/additem/<?php echo $this->validation->id;?>"> 
			  	
		    	<tr>
		    		<td>
		    			<input type="hidden" id="itemid" name="itemid" class="span itemid"/>
		    			<input type="text" id="itemcode" name="itemcode" required class="span itemcode" onblur="fetchItem('itemcode');" onchange="showhideviewprice('');"/>
		    			<span id="showpricelink"><a href="javascript:void(0)" onclick="viewminprices('itemid')">View Prices</a></span>
		    			<span id="showpricelinkbrow"><a href="javascript:void(0)" onclick="viewminpricesbrow('itemcodeshow');">Browse Item</a></span>
                    </td>
		    		<td>
		    			<textarea id="itemname" name="itemname" required <?php if ($this->session->userdata('usertype_id') == 2){echo 'readonly';}?>></textarea>
		    		</td>
		    		<td><input type="text" id="quantity" name="quantity" class="span12" onblur="calculatetotalprice('')" required/></td>
		    		<td><input type="text" id="unit" name="unit" class="span12"/></td>
		    		<td>
		    			<div class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="text" id="ea" name="ea" class="span9 price" onblur="calculatetotalprice('')" required/>
		    			</div>
		    		</td>
		    		<td>
		    			<div class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="text" id="totalprice" name="totalprice" class="span9 price totalamount-new" required/>
		    			</div>
		    		</td>
		    		<td>
    		    		<input type="text" id="daterequested" name="daterequested" class="span daterequested" 
    		    		value="<?php echo $this->session->userdata('defaultdeliverydate')?$this->session->userdata('defaultdeliverydate'):'';?>"
    		    		data-date-format="mm/dd/yyyy" required
    		    		onchange="defaultdate(this.value);$('#costcode').focus();"/>
    		    		or<br/>
		    			<input type="checkbox" name="willcall" value="1"/> For Pickup/Will Call
		    		</td>
		    		<td>
		    			<select id="costcode" name="costcode" class="costcode" onchange="defaultcostcode(this.value)">
		    				<?php foreach($costcodes as $costcode){?>
		    				<option value="<?php echo $costcode->code;?>" 
		    				<?php if($this->session->userdata('defaultcostcode')==$costcode->code){echo 'SELECTED';}?>>
		    				<?php echo $costcode->code;?>
		    				</option>
		    				<?php }?>
		    			</select>
		    			
		    		</td>
		    		<td><textarea id="notes" name="notes"></textarea></td>
		    		<td></td>
		    	</tr>
		    	<tr>
		    		<td colspan="15">
		    		<input type="hidden" name="quote" value="<?php echo $this->validation->id;?>"/>
                                
		    		<input type="submit" value="Add Next Item" class="btn btn-primary"/>
					</td>
		    	</tr>
		    	</form>
		    	<?php }?>
		    </table>
		    </div>
             <div align="right" >
                 <form class="form-horizontal" enctype="multipart/form-data" method="post"
    		    	action="<?php echo base_url(); ?>admin/quote/updateattach">
    			  	<input type="hidden" name="quoteid" value="<?php echo $this->validation->id;?>"/>
                        <div class="control-group">
    		   
                		    <div class="controls">
                		   	Add Attachment   <input type="file" name="userfile" />
                          	</div> 
    		    			</div> 
    		    			<input type="submit" value="Upload" class="btn btn-primary"/>
    		    			<?php if(@$this->validation->quoteattachment){?>
                        	<br>
                        	<a href="<?php echo site_url('uploads/quote').'/'.@$this->validation->quoteattachment ;?>" target="_blank">  
                        	View Attachment
                          	</a>
                          	<?php }?>
    			</form>
			</div>
		    <?php }?>
		    <div class="span12">
		       <?php if($this->validation->id && !$awarded){?>
			   <div class="span3">
			    	<label class="control-label">Sub Total:</label>
				    <div class="controls">
				    	<input type="text" value="<?php echo $this->validation->subtotal;?>" disabled class=" price"/>
				    </div>
			    </div>
			    
			   
			   <div class="span3">
			    	<label class="control-label">Tax Total:</label>
				    <div class="controls">
				    	<input type="text" value="<?php echo $this->validation->taxtotal;?>" disabled class=" price"/>
				    </div>
			   </div>
			   <div class="span3">
			    	<label class="control-label">Total:</label>
				    <div class="controls">
				    	<input type="text" value="<?php echo $this->validation->total;?>" disabled/>
				    </div>
			   </div>
			   <?php }?>
		    
		    </div>
	    
	    	<p>&nbsp;</p>
	    
	    	<?php if($this->validation->id && $quoteitems && !$awarded){?>
		    	<div class="row span12">
				    <div class="control-group span4">
					    <label class="control-label"><strong>Request Quote to:</strong></label>
					      <hr/>
                              <!-- Start On 21st Jan 2013 -->
                              <div>
                                  Local results Only<input type="checkbox" name="localresult" id="localresult" onchange="toggleradius();">
                                  <span id="mileid"><br>Mile radius from location<input type="text" name="locradius" id="locradius" size="3" style="width: 38px;height:15px; " onchange="getcomplist()"> </span>
                              </div>
                              <div>
                                  Include Internet and retailers
                                  <input type="checkbox" name="internetret" id="internetret">
                                  </div>
                              <div>
                                 Supply Network
                                 <input type="checkbox" name="supplynet" id="supplynet" checked >
                                  </div>
                              <br>
                              <div class="controls" id="invitecomp">
    					    	<?php $i = 0; foreach($companylist as $c) if(!in_array($c->id, $invited)){ $i++;?>
    					    		<input type="checkbox" class="invite" value="<?php echo $c->id;?>" />
    					    		&nbsp;&nbsp; <?php echo $c->title;?>
    					    		<br/>
    					    	<?php }?>
    					    	<?php if(!$companylist){?>
    					    	No suppliers in your network.
    					    	<br/>
    					    	<a href="<?php echo site_url('site/suppliers')?>">
    					    		Find Suppliers
    					    	</a>
    					    	<?php }?>
    					    </div>
                            <!-- End On 21st Jan 2013 -->
							<!-- <div class="controls">
					    	<?php $i = 0; foreach($companylist as $c) if(!in_array($c->id, $invited)){ $i++;?>
					    		<input type="checkbox" class="invite" value="<?php echo $c->id;?>" />
					    		&nbsp;&nbsp; <?php echo $c->title;?>
					    		<br/>
					    	<?php }?>
					    </div>-->
				    </div>
				    <div class="control-group span4">
					    <label class="control-label"><strong>Send Reminder to:</strong></label>
					      <hr/>
					    <div class="controls">
					    	<?php foreach($companylist as $c) if(in_array($c->id, $invited)){ $i++;?>
					    		<input type="checkbox" class="remind" value="<?php echo $c->id;?>"
					    		<?php if($invitations[$c->id]->remindedon){echo 'CHECKED';}?>
					    		/>
					    		&nbsp;&nbsp; <?php echo $c->title;?>
					    		<br/>
					    	<?php }?>
					    </div>
				    </div>
				    <?php if(0){?>
				    <div class="control-group span4">
					    <label class="control-label"><strong>Send Revision to:</strong></label>
					      <hr/>
					    <div class="controls">
					    	<?php foreach($companylist as $c) if(in_array($c->id, $invited)){ $i++;?>
					    		<input type="checkbox" class="revision" value="<?php echo $c->id;?>" 
					    		<?php if($invitations[$c->id]->revisionsenton){echo 'CHECKED';}?>
					    		/>
					    		&nbsp;&nbsp; <?php echo $c->title;?>
					    		<br/>
					    	<?php }?>
					    </div>
				    </div>
				    <?php }?>
				</div>
			    <?php if($i){?>
		    	<br/><br/>
		    	<input name="add" type="button" class="btn btn-primary" value="Submit Proposal" onclick="invite();"/>
		    	<?php }?>

		    <?php }?>
	    
	    </div>
    </div>
</section>

        <div id="minpricemodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
        	
            <div class="modal-header">
        		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
            	<h3>Company prices : <span id="minpriceitemcode"></span></h3>
        	</div>
        	<div class="modal-body" id="minprices">
        	</div>
            
        </div>



		<div id="minpricemodalbrow" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">

            <div class="modal-header">
        	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
<!--            	<h3>fhghfg : <span id="minpriceitemcodebrow"></span></h3>-->
        	</div>
        	<div class="modal-body" id="minpricesbrow">
        	</div>

        </div>