<?php //echo '<pre>'; print_r($quoteitems);die;?>
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
	$('html, body').animate({scrollTop:$(document).height()}, 'slow');
	$("#showpricelink").hide();
});
//datedefault = true;
//ccdefault = true;
function defaultdate(dateval)
{
	//if(datedefault)
	if(dateval)
	if(confirm('Do you want to make this date default for this session?'))
	{
		//datedefault = false;
		$("#makedefaultdeliverydate").val('1');
		$(".daterequested ").val(dateval);
	}
}

function defaultcostcode(code)
{
	
	//if(ccdefault)
	if(code)
	if(confirm('Do you want to make this costcode default for this session?'))
	{
		//ccdefault = false;
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

function fetchItem(codeid)
{
	var itemcode = document.getElementById(codeid).value;

	var idid = codeid.replace('itemcode','itemid');
	
	var nameid = codeid.replace('itemcode','itemname');
	var unitid = codeid.replace('itemcode','unit');
	var eaid = codeid.replace('itemcode','ea');
	var quantityid = codeid.replace('itemcode','quantity');
	var notesid = codeid.replace('itemcode','notes');
	var totalpriceid = codeid.replace('itemcode','totalprice');
	var companyid = codeid.replace('itemcode','company');


    $.ajax({
      type:"post",
      url: '<?php echo base_url()?>admin/quote/getitembycode/',
      data: "code="+encodeURIComponent(itemcode)
    }).done(function(data){
        var obj = $.parseJSON(data);
        if(obj.itemname !== undefined)
        {
	        document.getElementById(idid).value = obj.itemid;
	        document.getElementById(nameid).value = obj.itemname;
	        document.getElementById(unitid).value = obj.unit;
	        document.getElementById(eaid).value = obj.ea;
	        document.getElementById(notesid).value = obj.notes;
	        var totalprice = document.getElementById(quantityid).value * obj.ea;
	        totalprice = Math.round(totalprice * 100) / 100;
	        document.getElementById(totalpriceid).value = totalprice;

	        getminpricecompanies(obj.itemid, companyid, '');
        }
    });
}


function saveitemname(id)
{
	<?php if ($this->session->userdata('usertype_id') == 2){?>return false;<?php }?>
	
	var itemid = $("#itemid"+id).val();
	var itemname = $("#itemname"+id).val();
	if(itemname=='')
		return false;
	if(confirm('Would you like to update item name in item code database?'))
	{
		url = "<?php echo site_url('admin/quote/updateitemnamewithcode');?>";
		$.ajax({
		      type:"post",
		      data: "itemid="+encodeURIComponent(itemid)+"&itemname="+itemname,
		      url: url
		    }).done(function(data){
				
		    });
	}
}

function getminprice(companyid)
{
	var price = $('option:selected', $("#"+companyid)).attr('price');
	//if(price=='')return;
	

	var eaid   = companyid.replace('company','ea');
	var quantityid = companyid.replace('company','quantity');
	var totalpriceid = companyid.replace('company','totalprice');

    document.getElementById(eaid).value = price;
    var totalprice = document.getElementById(quantityid).value * price;
    totalprice = Math.round(totalprice * 100) / 100;
    document.getElementById(totalpriceid).value = totalprice;
}

function getminpricecompanies(itemid, companyid, selected)
{
    $.ajax({
      type:"post",
      url: '<?php echo base_url()?>admin/itemcode/getminpricecompanies',
      data: "itemid="+itemid
    }).done(function(data){
        //alert(data);
        var ophtml = '<option price="0">Select Company</option>';
    	$($.parseJSON(data)).map(function () {
        	sel = '';
        	if(this.id==selected)
            	sel = 'selected';
    		ophtml += '<option price="'+this.price+'" value="'+this.id+'" '+sel+'>'+this.title+'</option>';
    	});
    	
    	$('#'+companyid).html(ophtml);
    });
}

function calculatetotalprice(id)
{
	var quantityid = 'quantity'+id;
	var eaid = 'ea'+id;
	var totalpriceid = 'totalprice'+id;
    var totalprice = document.getElementById(quantityid).value * document.getElementById(eaid).value;
    totalprice = Math.round(totalprice * 100) / 100;
    document.getElementById(totalpriceid).value = totalprice;
	//document.getElementById(totalpriceid).value = document.getElementById(eaid).value * document.getElementById(quantityid).value;

}

function showhideviewprice(id)
{
	var itemcode = document.getElementById('itemcode'+id).value;
	if(itemcode=='')
	{
		$("#showpricelink"+id).hide();
	}
	else
	{
		$("#showpricelink"+id).show();
	}
}

function viewminprices(codeid)
{
	var itemid = document.getElementById(codeid).value;
	
	if(itemcode=='')
	{
		return false;
	}
	var serviceurl = '<?php echo base_url()?>admin/itemcode/getcompanypricetable/';
	//alert(serviceurl);
	var d = "id="+itemid+"&codeid="+codeid;
	
	$.ajax({
	      type:"post",
	      url: serviceurl,
	      data: d//"code="+encodeURIComponent(itemcode)+"&codeid="+codeid
	    }).done(function(data){
	        //$("#minpriceitemcode").html(itemcode);
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
	var companyid = codeid.replace('itemid','company');
	var priceid = codeid.replace('itemid','ea');
	$("#"+companyid).val(company);
	$("#"+priceid).val(price);
}
function checkzero(classname)
{
	var ret = true;
	$("."+classname).each(function(){
		if($(this).val()=='' || $(this).val()=='0' || $(this).val()=='0.00')
			ret = false;
	});
	if(!ret)
		alert('Item price can not be 0');
	return ret;
}
<?php if($this->validation->id){?>
function showawardform()
{
	$("#awardmodal").modal();
}

function usedefaultaddresscheckchange()
{
	if($("#usedefaultaddress").attr('checked'))
		$("#shipto").val('<?php //echo htmlentities($quote->project->address);?>');
}
<?php }?>
//-->
</script>


<script type="text/javascript">
$(function() {
    
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
	<h3 class="box-header"><?php echo $heading; ?> &nbsp;&nbsp;<a style="font-size:12px;font-weight:normal;" target="_blank" href="<?php echo site_url().'admin/quote/bids/'.$quoteitems[0]->quote;?>">View Bids</a> - &nbsp;<a style="font-size:12px;font-weight:normal;" target="_blank" href="<?php echo site_url().'admin/message/messages/'.$quoteitems[0]->quote ;?>">View Messages</a></h3>
	<div class="box">
		<div class="span12">
		   <?php echo $this->session->flashdata('message'); ?>
		   <?php echo @$message; ?>
		   <br/>
		   <a class="btn btn-primary" href="<?php echo site_url('admin/quote/index/'.$pid);?>">&lt;&lt; Back</a>
		   <br/>
		   <form id="mainform" class="form-horizontal" method="post" action="<?php echo $action; ?>"> 
		   <input type="hidden" name="id" value="<?php echo $this->validation->id;?>"/> 
		   <input type="hidden" name="pid" value="<?php echo $pid;?>"/>
		   <input type="hidden" name="potype" value="<?php echo $this->validation->potype;?>"/>
		   <input type="hidden" id="invitees" name="invitees" value=""/>
		   <br/>
		    
		    <div class="control-group">
			    <div class="controlss">PO # &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; 
			      <input type="text" id="ponum" name="ponum" style="width: 20%" class="input small" value="<?php echo $this->validation->ponum; ?>" required>
			      
			      &nbsp; &nbsp;
			      &nbsp; &nbsp; 
			      Subject: &nbsp; &nbsp; 
			      <input type="text" id="subject" name="subject" class="input" style="width: 26%"  value="<?php echo $this->validation->subject; ?>">
			      
			    </div>
			    <div class="controls">
			    	<?php echo $this->validation->ponum_error;?>
			    </div>
		    </div>
		    
		    <div class="control-group">
			    <div class="controlss">
			      Delivery or Pick-Up Date: &nbsp; &nbsp;
			      <input type="text" id="deliverydate" name="deliverydate" class="input small span2" 
			        onchange="defaultdate(this.value);"
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
		    
		  </form>
		  
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
		    	<form id="olditemform" class="form-horizontal" method="post" action="<?php echo base_url(); ?>admin/quote/updateitems/<?php echo $this->validation->id;?>"> 
			  	<input type="hidden" name="quote" value="<?php echo $this->validation->id;?>"/>
		    	<?php foreach($quoteitems as $q){?>
		    	<tr>
		    		<td>
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
						<input type="text" class="span12" id="ea<?php echo $q->id;?>" name="ea<?php echo $q->id;?>" value="<?php echo $q->ea;?>" onblur="calculatetotalprice('<?php echo $q->id?>')" readonly required/>
		    			</div>
		    		</td>
		    		<td>
		    			<div class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="text" id="totalprice<?php echo $q->id;?>" class="span9 price" name="totalprice<?php echo $q->id;?>" value="<?php echo $q->totalprice;?>" required/>
		    			</div>
		    		</td>
		    		
		    		<td>
		    			<input type="text" class="span12 daterequested" name="daterequested<?php echo $q->id;?>" value="<?php echo $q->daterequested;?>" data-date-format="mm/dd/yyyy" required onchange="defaultdate(this.value);$('#costcode<?php echo $q->id;?>').focus();"/>
		    			or<br/>
		    			<input type="checkbox" name="willcall<?php echo $q->id;?>" value="1" <?php if($q->willcall){echo 'CHECKED';}?>/> For Pickup/Will Call
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
		    		<td><textarea id="notes<?php echo $q->id;?>" name="notes<?php echo $q->id;?>" ><?php echo $q->notes;?></textarea></td>
		    		<td>
		    		<?php echo anchor ( 'admin/quote/deleteitem/' . $q->id.'/'.$this->validation->id, '<span class="icon-2x icon-trash"></span>', array ('class' => 'delete', 'onclick' => "return confirm('Are you sure want to Delete this item?')" ) ) ?>
					
					</td>
		    	</tr>
		    	<tr>
		    		<td colspan="10" style="text-align: center">
				    	<select id="company<?php echo $q->id;?>" name="company<?php echo $q->id;?>" onchange="getminprice('company<?php echo $q->id;?>')">
				    	<?php if(0) foreach($companylist as $company){?>
				    		<option value="<?php echo $company->id;?>" <?php if($q->company==$company->id){echo 'selected="selected"';}?>><?php echo $company->title;?></option>
				    	<?php }?>
				    	</select>
				    	<script>getminpricecompanies('<?php echo $q->itemid;?>','company<?php echo $q->id;?>','<?php echo $q->company;?>');</script>
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
		    	<?php if(!$bids){?>
			  	<form id="newitemform" class="form-horizontal" method="post" 
			  		action="<?php echo base_url(); ?>admin/quote/additem/<?php echo $this->validation->id;?>" 
			  		onsubmit="return checkzero('pricefieldnew')"> 
			  	
		    	<tr>
		    		<td>
		    			<input type="hidden" id="itemid" name="itemid" class="span itemid"/>
		    			<input type="text" id="itemcode" name="itemcode" required class="span itemcode" onblur="fetchItem('itemcode');showhideviewprice('');" onchange="//showhideviewprice('');"/>
		    			<span id="showpricelink"><a href="javascript:void(0)" onclick="viewminprices('itemid')">View Prices</a></span>
		    			<span id="showpricelinkbrow"><a href="javascript:void(0)" onclick="viewminpricesbrow('itemcodeshow');">Browse Item</a></span>
		    		</td>
		    		<td>
		    			<textarea id="itemname" name="itemname" required <?php if ($this->session->userdata('usertype_id') == 2){echo 'readonly';}?>></textarea>
		    		</td>
		    		<td><input type="text" id="quantity" name="quantity" class="span" onblur="calculatetotalprice('')" required/></td>
		    		<td><input type="text" id="unit" name="unit" class="span"/></td>
		    		<td>
		    			<div class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="text" id="ea" name="ea" class="span9 price pricefieldnew" onblur="calculatetotalprice('')" readonly required/>
		    			</div>
		    		</td>
		    		<td>
		    			<div class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="text" id="totalprice" name="totalprice" class="span9 price" required/>
		    			</div>
		    		</td>
		    		<td>
    		    		<input type="text" id="daterequested" name="daterequested" class="span daterequested" 
    		    		value="<?php echo $this->session->userdata('defaultdeliverydate')?$this->session->userdata('defaultdeliverydate'):'';?>"
    		    		data-date-format="mm/dd/yyyy" required onchange="defaultdate(this.value);$('#costcode').focus();" />
    		    		or<br/>
		    			<input type="checkbox" name="willcall" value="1" /> For Pickup/Will Call
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
		    		<td colspan="10" style="text-align: center">
				    	<select id="company" name="company" onchange="getminprice('company')">
				    	<?php foreach($companylist as $company){?>
				    		<option value="<?php echo $company->id;?>"><?php echo $company->title;?></option>
				    	<?php }?>
				    	</select>
		    		</td>
		    	</tr>
		    	<tr>
		    		<td colspan="10">
		    		<input type="hidden" name="quote" value="<?php echo $this->validation->id;?>"/>
		    		<input type="submit" value="Add Next Item" class="btn btn-primary"/>
					</td>
		    	</tr>
		    	</form>
		    	<?php }?>
		    </table>
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
		    	<?php if($potype == 'Bid'){?>
			    <div class="control-group">
				    <label class="control-label"><strong>Select Company Name for Requesting Quote</strong></label>
				      <hr/>
				    <div class="controls">
				    	<?php $i = 0; foreach($companylist as $c) if(!in_array($c->id, $invited)){ $i++;?>
				    		<input type="checkbox" class="invite" value="<?php echo $c->id;?>" />
				    		&nbsp;&nbsp; <?php echo $c->title;?>
				    		<br/>
				    	<?php }if($i){?>
				    	<br/><br/>
				    	<input name="add" type="button" class="btn btn-primary" value="Submit Proposal" onclick="invite();"/>
				    	<?php }?>
				    </div>
			    </div>
			    <?php }else{//'direct purchase order'?>
			    <div class="control-group">
				      <hr/>
				    <div class="controls">
				    	<form id="assignform" method="post" action="<?php echo site_url('admin/quote/assignpo');?>">
						<input type="hidden" name="id" value="<?php echo $this->validation->id;?>">
				    	<input type="submit" class="btn btn-primary" value="Send PO"/>
				    	</form>
				    </div>
			    </div>
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
        
        <div id="awardmodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
        	<form id="editform" class="stylemoduleform" method="post" action="<?php echo site_url('admin/quote/assignpo');?>">
			<input type="hidden" name="id" value="<?php echo $this->validation->id;?>">
            <input type="hidden" name="company" value="<?php echo $this->validation->company;?>">
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
        		<input type="submit" class="btn btn-primary" value="Award"/>
        	</div>
            </form>
        </div>



		<div id="minpricemodalbrow" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">

            <div class="modal-header">
        	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
<!--            	<h3>fhghfg : <span id="minpriceitemcodebrow"></span></h3>-->
        	</div>
        	<div class="modal-body" id="minpricesbrow">
        	</div>

        </div>
