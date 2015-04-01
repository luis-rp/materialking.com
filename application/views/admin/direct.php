<?php //echo '<pre>'; print_r($quoteitems);die;?>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery.price_format.js"></script>
<script type="text/javascript" src="<?php // echo base_url();?>templates/admin/js/jquery-ui.js"></script>



<link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/fg.menu.css" type="text/css">
<link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/ui.all.css" type="text/css" id="color-variant-default">
<script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/fg.menu.2.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<script src="<?php echo base_url();?>templates/admin/js/jquery.ui.autocomplete.html.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">

<!-- <script src="<?php echo base_url();?>templates/admin/js/jqBootstrapValidation.js"></script> -->

<script type="text/javascript">

$(document).ready(function() {
	//$('#intro').wysihtml5();
	//$('#content').wysihtml5();
	$('#deliverydate').datepicker();
	$('#podate').datepicker();
	$('#duedate').datepicker();
	$('.daterequested').datepicker();
	//$('textarea').autosize();
	$('html, body').animate({scrollTop:$(document).height()}, 'slow');
	$("#showpricelink").hide();
	$("#showpricelinkbrow").show();
	
	 $("#browseItem").click(function(){
    	$('#selectItemWindow').dialog({ height: "auto"  });
    });    
 
    $("#selectItemWindow").hide();
	
    // BUTTONS
	$('.fg-button').hover(
		function(){ $(this).removeClass('ui-state-default').addClass('ui-state-focus'); },
		function(){ $(this).removeClass('ui-state-focus').addClass('ui-state-default'); }
	);
    
    $('#hierarchybreadcrumb').menu2({
		content: $('#hierarchybreadcrumb').next().html(),
		backLink: false
	});
	
	$('#hierarchybreadcrumbitem').menu2({
		content: $('#hierarchybreadcrumbitem').next().html(),
		backLink: false
	});
	
	/*$.widget( "app.autocomplete", $.ui.autocomplete, {
        
        // Which class get's applied to matched text in the menu items.
        options: {
            highlightClass: "ui-state-highlight"
        },
        
        _renderItem: function( ul, item ) {

            // Replace the matched text with a custom span. This
            // span uses the class found in the "highlightClass" option.
            var re = new RegExp( "(" + this.term + ")", "gi" ),
                cls = this.options.highlightClass,
                template = "<span class='" + cls + "'>$1</span>",
                label = item.label.replace( re, template ),
                $li = $( "<li/>" ).appendTo( ul );
            
            // Create and return the custom menu item content.
            $( "<a/>" ).attr( "href", "#" )
                       .html( label )
                       .appendTo( $li );
            
            return $li;
            
        }
        
    });*/	
    
	 if($("#quoteCnt").val() == 0)
	   {
	    	$(".newitemrow").css('display','');
	   }
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
	var projectid = document.getElementById('pid').value;
	var incrementid = codeid.replace('itemcode','itemincrement');
	var nameid = codeid.replace('itemcode','itemname');
	var unitid = codeid.replace('itemcode','unit');
	var eaid = codeid.replace('itemcode','ea');
	var quantityid = codeid.replace('itemcode','quantity');
	var notesid = codeid.replace('itemcode','notes');
	var totalpriceid = codeid.replace('itemcode','totalprice');
	var companyid = codeid.replace('itemcode','company');

	if(itemcode!=""){
		$.ajax({
			type:"post",
			url: '<?php echo base_url()?>admin/quote/getitembycode/',
			data: "code="+encodeURIComponent(itemcode)+"&projectid="+encodeURIComponent(projectid)
		}).done(function(data){
			var obj = $.parseJSON(data);
			if(obj.itemname !== undefined)
			{
				document.getElementById(idid).value = obj.itemid;
				document.getElementById(incrementid).value = obj.increment;
				document.getElementById(nameid).value = obj.itemname;
				document.getElementById(unitid).value = obj.unit;
				document.getElementById(eaid).value = obj.ea;
				document.getElementById(notesid).value = obj.notes;
				var totalprice = document.getElementById(quantityid).value * obj.ea;
				totalprice = Math.round(totalprice * 100) / 100;
				document.getElementById(totalpriceid).value = totalprice;

				getminpricecompanies(obj.itemid, companyid, 1, '');
			}
		});
	}else{
		document.getElementById(idid).value = "";
		document.getElementById(incrementid).value = "";
		document.getElementById(nameid).value = "";
		document.getElementById(unitid).value = "";
		document.getElementById(eaid).value = "";
		document.getElementById(notesid).value = "";
		document.getElementById(totalpriceid).value = "";
	}
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
	var optionvalue = $('option:selected', $("#"+companyid)).val();	
	var price = $('option:selected', $("#"+companyid)).attr('price');
	//if(price=='')return;
	var eaid   = companyid.replace('company','ea');
	var quantityid = companyid.replace('company','quantity');
	var totalpriceid = companyid.replace('company','totalprice');
	var newvendorspanid = companyid.replace('company','newvendorspan');	

    document.getElementById(eaid).value = price;
   
    if(optionvalue == "addnewcompany"){		
		$('#'+eaid).attr("readonly", false);
		$('#'+newvendorspanid).css('display','block');		
	}else{
		$('#'+eaid).attr("readonly", true);
		$('#'+newvendorspanid).css('display','none');		
	}	
    $('#'+eaid).attr("readonly", false);
    var totalprice = document.getElementById(quantityid).value * price;
    totalprice = Math.round(totalprice * 100) / 100;
    document.getElementById(totalpriceid).value = totalprice;    
    var betterprice = companyid.replace('company','betterprice');
    
    $('#'+betterprice).html("");
	$('select#'+companyid).find('option').each(function() {
    		if(parseFloat(price)>parseFloat($(this).attr('price')) && parseFloat($(this).attr('price'))!=0){
    		$('#'+betterprice).html("* There is a better price available");
    		}
   	});
    
}

function getminpricecompanies(itemid, companyid, quantity, selected, selectedea)
{		
    $.ajax({
      type:"post",
      url: '<?php echo base_url()?>admin/itemcode/getminpricecompanies',
      data: "itemid="+itemid+"&quantity="+quantity
    }).done(function(data){
        //alert(data);
        var betterprice = companyid.replace('company','betterprice');
        $('#'+betterprice).html("");
        var ophtml = '<option price="0">Select Company</option>';
    	$($.parseJSON(data)).map(function () {
        	sel = '';
        	if(this.id==selected){
            	sel = 'selected';
        	}	
    		ophtml += '<option price="'+this.price+'" value="'+this.id+'" '+sel+'>'+this.title+'</option>';
    		
    		if(parseFloat(selectedea)>parseFloat(this.price)){    		
    			if(parseFloat(this.price)>0)	
    			$('#'+betterprice).html("* Lower price avaialble");
    		}
    	});
    	
    	ophtml += '<option value="addnewcompany" price="" >Add New Vendor</option>';
    	
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

function checkincrementquantity(quantity){
	
	var incrementval = $('#itemincrement').val();	
	$('#incrementmessage').html('');
	if(incrementval>0){
		if((quantity%incrementval)!=0){
			$('#incrementmessage').html('Sorry this item is only available in increments of '+incrementval);
			$('#quantity').val('');
			$('#quantity').focus();
			return false;
		}else{
			$('#incrementmessage').html('');
		}
	}
	
}


function checkmaxstockreached(quantity,id){
	
	if(id==0)
	var itemid = $("#itemid").val();
	else
	var itemid = $("#itemid"+id).val();
	
	var data = "itemid="+itemid+"&quantity="+quantity;	
	$.ajax({
		type:"post",
		data: data,
		url : "<?php echo site_url('admin/inventorymanagement/checkmaxstock');?>"
	}).done(function(data){
		
		if(data == 1){
			alert('Max Stock Level already reached for this item');
		}
		
	});	
}


function checkupdateincrementquantity(quantity,id){	
	var incrementval = $('#itemincrement'+id).val();
	$('#incrementmessage'+id).html('');
	if(incrementval>0){
		if((quantity%incrementval)!=0){
			$('#incrementmessage'+id).html('Sorry this item is only available in increments of '+incrementval);
			$('#quantity'+id).val('');
			$('#quantity'+id).focus();
			return false;
		}else{
			$('#incrementmessage'+id).html('');
		}
	}	
}

function showhideviewprice(id)
{
	var itemcode = document.getElementById('itemcode'+id).value;
	if(itemcode=='')
	{
		$("#showpricelink"+id).hide();
		$("#showpricelink"+id).hide();
        $("#showpricelinkbrow"+id).show();
	}
	else
	{
		$("#showpricelink"+id).show();
		$("#showpricelink"+id).show();
        $("#showpricelinkbrow"+id).hide();
	}
}

function viewminprices(codeid,quant,priceid)
{
	var itemid = document.getElementById(codeid).value;
	
	if(quant==0){
		var quantity = document.getElementById('quantity').value;
		
		if(quantity=='')
		{
			alert("Please enter quantity");
			return false;
		}
		
	}else{
		var quantity = document.getElementById(quant).value;
		if(quantity=='')
		{
			alert("Please enter quantity");
			return false;
		}
	}
	
	if(itemid=='')
	{
		return false;
	}
	var serviceurl = '<?php echo base_url()?>admin/itemcode/getcompanypricetable/';
	var d = "id="+itemid+"&codeid="+codeid+"&quantity="+quantity+"&quantid="+quant+"&priceid="+priceid;
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
	var companyid = codeid.replace('itemid','company');
	var priceid = codeid.replace('itemid','ea');
	$("#"+companyid).val(company);
	$("#"+priceid).val(price);
}

function selectquantity(qty, quant, price, priceid)
{
	if(quant==0){
		
		var incrementval = $('#itemincrement').val();
		$('#incrementmessage').html('');
		if(incrementval>0){
			if((qty%incrementval)!=0){
				$('#incrementmessage').html('Sorry this item is only available in increments of '+incrementval);
				return false;
			}else{
				$('#incrementmessage').html('');
			}
		}
		
		document.getElementById('quantity').value = qty;
		document.getElementById('ea').value = price;
		
	}else{
		
		var id = priceid.substr(2);
		var incrementval = $('#itemincrement'+id).val();
		$('#incrementmessage'+id).html('');
		if(incrementval>0){
			if((qty%incrementval)!=0){
				$('#incrementmessage'+id).html('Sorry this item is only available in increments of '+incrementval);
				return false;
			}else{
				$('#incrementmessage'+id).html('');
			}
		}
		
		document.getElementById(quant).value = qty;		
		document.getElementById(priceid).value = price;
	}
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
	
	$.ui.autocomplete.prototype._renderItem = function (ul, item) {
    item.label = item.label.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + $.ui.autocomplete.escapeRegex(this.term) + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<span class='ui-state-highlight'>$1</span>");
    return $("<li></li>")
            .data("item.autocomplete", item)
            .append("<a>" + item.label + "</a>")
            .appendTo(ul);
};
    
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
    var serviceurl = '<?php echo base_url()?>admin/itemcode/gatcatitem3';
	//alert(catid);
	$.ajax({
		type:"post",
		url: serviceurl,
		data: "catid="+catid,
		success: function(items) //we're calling the response json array 'cities'
		{
			$('#selectboxid').html('<select onkeypress="showselectimage();" onmouseover="showselectimage();"  name="catiditem" id="catiditem" ></select>');
			$.each(items,function(id,myItems) //here we're doing a foeach loop round each city with id as the key and city as the value
			{
				var opt = $('<option title="'+myItems[1]+'" />'); // here we're creating a new select option with for each city
				opt.val(id);
				opt.text(myItems[0]);
				$('#catiditem').append(opt); //here we will append these new select options to a dropdown with the id 'cities'
				//savclose();
			});
		}
	})
}

function savclose()
{
	var itemcode = document.getElementById('catiditem').value;
	$("#itemcode").val(itemcode);
    fetchItem('itemcode');
    $('#selectItemWindow').dialog('close');
    $('.fg-menu-container').css({display: "none"});
}
</script>

<script type="text/javascript">
function allowonlydigits(e,elementid,errorid){
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which!=46) {              
      $("#"+errorid).html("Digits Only").show().fadeOut("slow");  
      $("#"+errorid).css('color','red');
      return false;
    } }

  
function displayBlankRow()
{
	$(".newitemrow").css('display','');
	
	url = "<?php echo site_url('admin/quote/showrecentcompanies');?>";
    	$.ajax({
    		type:"post",
    		data: "quote=<?php echo $this->validation->id;?>",
    		url: url
    	}).done(function(data){
    		$('#newcompanybodyid').html(data);  
    		if(data){
    		$('#recentcomp').css('display','block');
    		}
    	});
}


function showrecentaddedcompanies(){
	
	$('#newcompanymodal').modal();
}


function setnewcompany(companyname,companyemail){
	
	$('#newvendorspan').css('display','block');	
	$('#ea').attr("readonly", false);
	$('#addsupplyname').val(companyname);
	$('#addsupplyemail').val(companyemail);
	
}


function checknewitem(){
	
	if($('#itemcode').val()!="" && $('#itemname').val()!="" && $('#unit').val()!="" && $('#itemid').val()==""){
		
		if(confirm('Do you want to add/Save this item?'))
		{

			var itemcode = $("#itemcode").val();
			var itemname = $("#itemname").val();
			var unit = $("#unit").val();
			var d = "itemcode="+itemcode+"&itemname="+itemname+"&unit="+unit;

			useritemurl = "<?php echo site_url('admin/quote/addNewUserItem');?>";

			$.ajax({
				type:"post",
				data: d,
				url: useritemurl
			}).done(function(data){
				if(data)
				$('#itemid').val(data);
				$('#ea').attr("readonly", false);
				alert("Item got added successfully!");
			});
			
			
		}
	}
	
}


function checkcompanyusername(id,value){
	
	var usercheckurl = '<?php echo base_url()?>admin/quote/checkuserexist/';    		
    		$.ajax({
    			type:"post",
    			url: usercheckurl,
    			async: false,
    			data: "username="+value
    		}).done(function(data){
    			if(data == 1){
    				//$('#nonnetworkmessage').html("Username "+user+" already exists");    				
    				alert("Username "+value+" already exists"); 
    				$('#'+id).val('');  				 
    			}    			    			
    		}); 
}


function checkcompanyuseremail(id,email){
	
	var emailcheckurl = '<?php echo base_url()?>admin/quote/checkemailexist/';
	$.ajax({
		type:"post",
		url: emailcheckurl,
		async: false,
		data: "email="+email
	}).done(function(data){
		if(data == 1){
			alert("Email "+email+" already exists");
			$('#'+id).val('');
		}

	});

}


function showspanimage(image,itemid){
	
	$(".imgspcls").html('');	
	var imagehtml = '<img src="'+image+'" width="64"/>';	
	$("#imgsp"+itemid).html(imagehtml);
}	


function showselectimage(){	
	$("#imgselect").html('');	
	$("#imgselectpos2").html('');
	var imagehtml = '<img src="'+$('#catiditem :selected').attr('title')+'" width="64"/>';	
	$("#imgselect").html(imagehtml);	
	$("#imgselectpos2").html(imagehtml);	
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

<style type="text/css">
	
	#menuLog { font-size:1.0em; margin:10px 20px 20px; }
	.hidden { position:absolute; top:0; left:-9999px; width:1px; height:1px; overflow:hidden; }
	
	.fg-button { clear:left; margin:0 4px 40px 0px; padding: .4em 1em; text-decoration:none !important; cursor:pointer; position: relative; text-align: center; zoom: 1; }
	.fg-button .ui-icon { position: absolute; top: 50%; margin-top: -8px; left: 50%; margin-left: -8px; }
	a.fg-button { float:left;  }
	button.fg-button { width:auto; overflow:visible; } /* removes extra button width in IE */
	
	.fg-button-icon-left { padding-left: 2.1em; }
	.fg-button-icon-right { padding-right: 2.1em; }
	.fg-button-icon-left .ui-icon { right: auto; left: .2em; margin-left: 0; }
	.fg-button-icon-right .ui-icon { left: auto; right: .2em; margin-left: 0; }
	.fg-button-icon-solo { display:block; width:8px; text-indent: -9999px; }	 /* solo icon buttons must have block properties for the text-indent to work */	
	
	.fg-button.ui-state-loading .ui-icon { background: url(spinner_bar.gif) no-repeat 0 0; }
	.fg-menu-container{z-index:1000;}
</style>

<section class="row-fluid">
	<h3 class="box-header"><?php echo $heading; ?> &nbsp;&nbsp;<!--<a style="font-size:12px;font-weight:normal;" target="_blank" href="<?php echo site_url().'admin/quote/bids/';?>">View Bids</a> - &nbsp;<a style="font-size:12px;font-weight:normal;" target="_blank" href="<?php echo site_url().'admin/message/messages/';?>">View Messages</a>--></h3>
	<div class="box">
		<div class="span12">
		   <?php echo $this->session->flashdata('message'); ?>
		   <?php echo @$message; ?>
		   <br/>
		   <a class="btn btn-primary" href="<?php echo site_url('admin/quote/index/'.$pid);?>">&lt;&lt; Back</a>
		   <br/>
		   <form id="mainform" class="form-horizontal" method="post" action="<?php echo $action; ?>"> 
		   <input type="hidden" name="quoteCnt" id="quoteCnt" value="<?php echo count($quoteitems);?>">       
		   <input type="hidden" name="id" value="<?php echo $this->validation->id;?>"/> 
		   <input type="hidden" name="pid" id="pid" value="<?php echo $pid;?>"/>
		   <input type="hidden" name="potype" value="<?php echo $this->validation->potype;?>"/>
		   <input type="hidden" id="invitees" name="invitees" value=""/>
		   <br/>
		    
		    <div class="control-group">
			    <div class="controlss">PO # &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; 
			      <input type="text" id="ponum" name="ponum" style="width: 20%" class="input small" value="<?php echo $this->validation->ponum; ?>" required>
			      
			      &nbsp; &nbsp;
			      &nbsp; &nbsp; 
			      <!--Subject: &nbsp; &nbsp; 
			      <input type="text" id="subject" name="subject" class="input" style="width: 26%"  value="<?php echo $this->validation->subject; ?>">-->
			      
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
			      <input type="text" id="podate" name="podate" class="input small span2 daterequested"
			      	value="<?php echo $this->validation->podate; ?>" data-date-format="mm/dd/yyyy">
			      	&nbsp; &nbsp; &nbsp; &nbsp; 
			     Bid Due Date: &nbsp; &nbsp; 
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
		  <div class="pull-right" style="margin:6px 5px 0px 0px;">
		  	<?php if(!$costcodes){?>
		  	<font color="red">No costcodes exist for this project.</font>
		  	<?php }?>
		  	<a class="btn btn-green" href="<?echo site_url('admin/costcode');?>">Manage Costcodes</a>
		  </div>
		    <div class="control-group">
			    <h3 class="box-header" style="float:none"><label class="control-label">Items</label></h3>
		    </div>
		  <div class="control-group">
		    <table class="table table-bordered">
		    	<tr>
		    		<th width="15%">Item Code</th>
		    		<th width="15%">Item Name</th>
		    		<th width="5%">Qty.</th>
		    		<th width="5%">Unit</th>
		    		<th width="9%">Price EA</th>
		    		<th width="9%">Total Price</th>
		    		<th width="10%">Date Requested</th>
		    		<th width="10%">Cost Code</th>
		    		<th width="10%">Notes</th>
		    		<th width="3%">Delete</th>
		    	</tr>
		    	<?php if($quoteitems) {?>
		    	<form id="olditemform" class="form-horizontal" method="post" action="<?php echo base_url(); ?>admin/quote/updateitems/<?php echo $this->validation->id;?>"> 
			  	<input type="hidden" name="quote" value="<?php echo $this->validation->id;?>"/>
		    	<?php 
		    	foreach($quoteitems as $q)
		    	{		    		
		    		if(isset($q->item_img) && $q->item_img!= "" && file_exists("./uploads/item/".$q->item_img)) 
		    		{ 
                       $imgName = site_url('uploads/item/'.$q->item_img);
                    } 
                    else 
                    { 
                       $imgName = site_url('uploads/item/big.png');
                    } 
		    		?>
		    	<tr>
		    		<td>
		    			<input type="hidden" id="itemid<?php echo $q->id;?>" name="itemid<?php echo $q->id;?>" class="span itemid" value="<?php echo $q->itemid;?>"/>
		    			<input type="hidden" id="itemincrement<?php echo $q->id;?>" name="itemincrement<?php echo $q->id;?>" value="<?php echo $q->increment;?>"/>
		    			
			    		<!--<input type="text" required class="span12 itemcode itemcodeold" id="itemcode<?php echo $q->id;?>" name="itemcode<?php echo $q->id;?>" value="<?php echo $q->itemcode;?>" onblur="fetchItem('itemcode<?php echo $q->id;?>');" onchange="showhideviewprice('<?php echo $q->id;?>');"/>-->
			    		
			    		<textarea required class="span12 itemcode itemcodeold" id="itemcode<?php echo $q->id;?>" name="itemcode<?php echo $q->id;?>" onblur="fetchItem('itemcode<?php echo $q->id;?>');" onchange="showhideviewprice('<?php echo $q->id;?>');"/><?php echo $q->itemcode;?></textarea>
			    		
			    		<br>
			    		<img src="<?php echo $imgName;?>" width="90" height="90" style="padding-top:5px;">
			    		<br>
			    		
		    			<a href="javascript:void(0)" onclick="viewminprices('itemid<?php echo $q->id;?>','quantity<?php echo $q->id;?>','ea<?php echo $q->id;?>')">View Prices</a>
		    		</td>
		    		<td>
			    		<textarea id="itemname<?php echo $q->id;?>" name="itemname<?php echo $q->id;?>" required <?php if ($this->session->userdata('usertype_id') == 2){echo 'readonly';}?> style="width:95%;height:130px;"><?php echo htmlentities($q->itemname);?></textarea>
		    		</td>
		    		<td><input type="text" class="highlight nonzero nopad width50 input-sm span12" id="quantity<?php echo $q->id;?>" name="quantity<?php echo $q->id;?>" value="<?php echo $q->quantity;?>" onblur="checkupdateincrementquantity(this.value,'<?php echo $q->id;?>');  calculatetotalprice('<?php echo $q->id?>'); checkmaxstockreached(this.value,'<?php echo $q->id?>'); " 
			      	onkeypress="return allowonlydigits(event,'quantity<?php echo $q->id;?>', 'eaerrmsg<?php echo $q->id;?>')" ondrop="return false;" onpaste="return false;" required/><br><span style="color:red" id="incrementmessage<?php echo $q->id?>"></span><span id="eaerrmsg<?php echo $q->id;?>"></span></td>
		    		
		    		<td><input type="text" class="span12" id="unit<?php echo $q->id;?>" name="unit<?php echo $q->id;?>" value="<?php echo $q->unit;?>" required/></td>
		    		<td>
		    			<div class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="text" class="highlight nonzero nopad width50 input-sm span8" id="ea<?php echo $q->id;?>" name="ea<?php echo $q->id;?>" value="<?php echo $q->ea;?>" onblur="calculatetotalprice('<?php echo $q->id?>'); " onkeypress="return allowonlydigits(event,'ea<?php echo $q->id;?>', 'eaerrmsg1<?php echo $q->id;?>')" ondrop="return false;" onpaste="return false;" readonly required/>
		    			</div><span id="eaerrmsg1<?php echo $q->id;?>"></span>
		    		</td>
		    		<td>
		    			<div class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="text" id="totalprice<?php echo $q->id;?>" class="highlight nonzero nopad width50 input-sm span9 price" name="totalprice<?php echo $q->id;?>" value="<?php echo $q->totalprice;?>"  onkeypress="return allowonlydigits(event,'totalprice<?php echo $q->id;?>', 'eaerrmsg2<?php echo $q->id;?>')" ondrop="return false;" onpaste="return false;" required/>
		    			</div><span id="eaerrmsg2<?php echo $q->id;?>"></span>
		    		</td>
		    		
		    		<td>
		    			<input type="text" class="span12 daterequested" name="daterequested<?php echo $q->id;?>" value="<?php echo $q->daterequested;?>" data-date-format="mm/dd/yyyy" required onchange="defaultdate(this.value);$('#costcode<?php echo $q->id;?>').focus();"/>
		    			or<br/>
		    			<input type="checkbox" name="willcall<?php echo $q->id;?>" value="1" <?php if($q->willcall){echo 'CHECKED';}?>/> For Pickup/Will Call
		    		</td>
		    		<td>
		    			<select id="costcode<?php echo $q->id;?>" name="costcode<?php echo $q->id;?>" class="costcode" onchange="defaultcostcode(this.value)" style="width:150px;">
		    				<?php foreach($costcodes as $costcode){?>
		    				<option value="<?php echo $costcode->code;?>" 
		    				<?php if($q->costcode==$costcode->code){echo 'SELECTED';}?>>
		    				<?php echo $costcode->code;?>
		    				</option>
		    				<?php }?>
		    			</select>
		    		</td>
		    		<td><textarea id="notes<?php echo $q->id;?>" name="notes<?php echo $q->id;?>" style="width:95%;height:130px;"><?php echo $q->notes;?></textarea></td>
		    		<td>
		    		<?php echo anchor ( 'admin/quote/deleteitem/' . $q->id.'/'.$this->validation->id, '<span class="icon-2x icon-trash"></span>', array ('class' => 'delete', 'onclick' => "return confirm('Are you sure want to Delete this item?')" ) ) ?>
					
					</td>
		    	</tr>
		    	<tr>
		    		<td colspan="4" style="text-align:right;"><span id="betterprice<?php echo $q->id;?>"></span></td>
		    		<td colspan="6" style="text-align: left">
				    	<select id="company<?php echo $q->id;?>" name="company<?php echo $q->id;?>" onchange="getminprice('company<?php echo $q->id;?>')">
				    	<?php if(0) foreach($companylist as $company){?>
				    		<option value="<?php echo $company->id;?>" <?php if($q->company==$company->id){echo 'selected="selected"';}?>><?php echo $company->title;?></option>
				    	<?php }?>
				    	<option value="addnewcompany<?php echo $q->id;?>" price="" >Add New Vendor</option>
				    	</select>
				    	<script>getminpricecompanies('<?php echo $q->itemid;?>','company<?php echo $q->id;?>','<?php echo $q->quantity;?>','<?php echo $q->company;?>','<?php echo $q->ea;?>');</script>
				    	
				    	
				    	<div id="supplydata"> 
				    	  <?php if(is_array(@$nonnetcompanies)){ if(array_key_exists($q->id,$nonnetcompanies)){ foreach($nonnetcompanies[$q->id] as $nccomp){ ?>
				    	  
				    	 <div class="controls">
					       <label class="control-label" for="addsupplyname">Company Name</label> 	
                           <input type="text" name="addsupplyname<?php echo $q->id;?>" id="addsupplyname<?php echo $q->id;?>" value="<?php if($q->id == @$nccomp->quoteitemid) echo @$nccomp->companyname;?>" onblur="checkcompanyusername(this.id,this.value);"  style="width:50%;">
                         </div>  
                          
                        <div class="controls">
					    	<label class="control-label" for="addsupplyusername">Contact Name</label>
                            <input type="text" name="addsupplyusername<?php echo $q->id;?>" id="addsupplyusername<?php echo $q->id;?>"  value="<?php if($q->id == @$nccomp->quoteitemid) echo @$nccomp->contact;?>" style="width:50%;">
                        </div> 
                          
                       <div class="controls">
					    	<label class="control-label" for="addsupplyemail">Company Email</label>  
                            <input type="email" name="addsupplyemail<?php echo $q->id;?>" id="addsupplyemail<?php echo $q->id;?>" value="<?php if($q->id == @$nccomp->quoteitemid) echo @$nccomp->companyemail;?>" onblur="checkcompanyuseremail(this.id,this.value);"  style="width:50%;">
                       </div>     
               			
               		<?php } }else{ ?>
               		  <span id='newvendorspan<?php echo $q->id;?>' style="display:none;">
               		  
               		   <div class="controls">
					       <label class="control-label" for="addsupplyname">Company Name</label> 	
               			   <input type="text" name="addsupplyname<?php echo $q->id;?>" id="addsupplyname<?php echo $q->id;?>" onblur="checkcompanyusername(this.id,this.value);"  style="width:50%;">
               		  </div>
               			 
               		  <div class="controls">
					       <label class="control-label" for="addsupplyusername">Contact Name</label>
                           <input type="text" name="addsupplyusername<?php echo $q->id;?>" id="addsupplyusername<?php echo $q->id;?>" style="width:50%;">
                      </div> 
               			 
               		   <div class="controls">
					    	<label class="control-label" for="addsupplyemail">Company Email</label>  	 
                            <input type="email" name="addsupplyemail<?php echo $q->id;?>" id="addsupplyemail<?php echo $q->id;?>" onblur="checkcompanyuseremail(this.id,this.value);"   style="width:50%;">
                       </div>
                       
               		  </span>	
               		<?php } } else{ ?>
               		  <span id='newvendorspan<?php echo $q->id;?>' style="display:none;">
               		  
               		   <div class="controls">
					       <label class="control-label" for="addsupplyname">Company Name</label> 
               			   <input type="text" name="addsupplyname<?php echo $q->id;?>" id="addsupplyname<?php echo $q->id;?>" onblur="checkcompanyusername(this.id,this.value);"  style="width:50%;">
               		  </div>
               			 
               		  <div class="controls">
					       <label class="control-label" for="addsupplyusername">Contact Name</label>
                           <input type="text" name="addsupplyusername<?php echo $q->id;?>" id="addsupplyusername<?php echo $q->id;?>" style="width:50%;">
                      </div> 
               			 
               		  <div class="controls">
					    	<label class="control-label" for="addsupplyemail">Company Email</label>  	 
                            <input type="email" name="addsupplyemail<?php echo $q->id;?>" id="addsupplyemail<?php echo $q->id;?>" onblur="checkcompanyuseremail(this.id,this.value);"   style="width:50%;">
                      </div>
                      
               		  </span>	
               		<?php } ?>
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
			  	
		    	<tr class="newitemrow" style="display:none;">
		    		<td>
		    			<input type="hidden" id="itemid" name="itemid" class="span itemid"/>
		    			<input type="hidden" id="itemincrement" name="itemincrement" />
		    			<input type="text" id="itemcode" name="itemcode" required class="span itemcode" onblur="fetchItem('itemcode');showhideviewprice('');" onchange="//showhideviewprice('');"/>
		    			<span id="showpricelink"><a href="javascript:void(0)" onclick="viewminprices('itemid',0,0)">View Prices</a></span>
		    			<!-- <span id="showpricelinkbrow"><a href="javascript:void(0)" onclick="viewminpricesbrow('itemcodeshow');">Browse Item</a></span> -->
		    			<span id="showpricelinkbrow"><a href="javascript:void(0)" id="browseItem">Browse Item</a></span>
		    		</td>
		    		<td>
		    			<textarea id="itemname" name="itemname" style="width:95%;height:130px;" required <?php // if ($this->session->userdata('usertype_id') == 2){echo 'readonly';}?>></textarea>
		    		</td>
		    		<td><input type="text" id="quantity" name="quantity" class="highlight nonzero nopad width50 input-sm span" onblur="checkincrementquantity(this.value); calculatetotalprice(''); checkmaxstockreached(this.value,0); " onkeyup="this.value=this.value.replace(/[^0-9]/g,'');" required/><br><span style="color:red" id="incrementmessage"></td>
		    		<td><input type="text" id="unit" name="unit" onblur="return checknewitem();"  class="span"/></td>
		    		<td>
		    			<div class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="text" id="ea" name="ea" class="highlight nonzero nopad width50 input-sm span8 price pricefieldnew" onblur="calculatetotalprice('')" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" readonly required/>
		    			</div>
		    		</td>
		    		<td>
		    			<div class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="text" id="totalprice" name="totalprice" class="highlight nonzero nopad width50 input-sm span9 price" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" required/>
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
		    		<td><textarea id="notes" name="notes" style="width:95%;height:130px;"></textarea></td>
		    		<td></td>
		    	</tr>
		    	<tr class="newitemrow" style="display:none;">
		    		<td colspan="4" style="text-align:right;"><span id="betterprice"></span></td>
		    		<td colspan="6" style="text-align:left">
				    	<select id="company" name="company" onchange="getminprice('company')">
				    	<?php foreach($companylist as $company){?>
				    		<option value="<?php echo $company->id;?>"><?php echo $company->title;?></option>
				    	<?php }?>
				    		<option value="addnewcompany" price="" >Add New Vendor</option>
				    	</select>				    	
				    	
				    	<!-- <select id="nonnetcompany" name="nonnetcompany" onchange="getminprice('nonnetcompany')">
				    	<?php  if(isset($nonnetuser)){
					    	          foreach($nonnetuser as $c) { ?>					    	          	
					    		       <option value="<?php echo $c->id;?>"><?php echo $c->title;?></option>
    					<?php }  }?>
					    </select> -->	
				    	  	
				    	<div id="supplydata"> 				    	
				    	  <span style="display:none;" id="recentcomp"><a href="javascript:void(0)" onclick="showrecentaddedcompanies();" >Select Recent Companies</a></span>	
				    		
				    	  <span id='newvendorspan' style="display:none;">
                   <!--Name:<input type="text" name="addsupplyname" id="addsupplyname" onblur="checkcompanyusername(this.id,this.value);" style="width:80px;">&nbsp;
       Email:<input type="email" name="addsupplyemail" id="addsupplyemail" onblur="checkcompanyuseremail(this.id,this.value);"  style="width:120px;">&nbsp;-->
                   
                    			<div class="controls">
					    	     <label class="control-label" for="addsupplyname">Company Name</label>
                                 <input type="text" name="addsupplyname" id="addsupplyname" onblur="checkcompanyusername(this.id,this.value);" style="width:50%;">
                                 </div> 
                              
                                 <div class="controls">
					    	     <label class="control-label" for="addsupplyusername">Contact Name</label>
                                 <input type="text" name="addsupplyusername" id="addsupplyusername" style="width:50%;">
                                  </div> 
                                  
                                 <div class="controls">
					    	     <label class="control-label" for="addsupplyemail">Company Email</label>
                               <input type="email" name="addsupplyemail" id="addsupplyemail" onblur="checkcompanyuseremail(this.id,this.value);" style="width:50%;">
                                 </div>                 
               		 </span>
				    	
		    		</td>
		    	</tr>
		    	<tr>
		    		<td colspan="10">
		    		<input type="hidden" name="quote" value="<?php echo $this->validation->id;?>"/>
		    		<input type="submit" value="Add Next Item" class="btn btn-primary" onclick="displayBlankRow();"/>
		    		<input type="submit" value="Save & Continue" class="btn btn-primary"/>
					</td>
		    	</tr>
		    	</form>
		    	<?php }?>
		    </table>
		    </div>
		    <?php }?>
		    
		    <?php if($quoteitems){?>
		    <div class="span12">
		    <div class="span3">			    	
				    <div class="controls">
				    <table class="table table-striped">
					    <tr>
						    <th>
						    	Our Price Guesstimate
						    </th>
					    </tr>
					    <tr>
						    <td style="color:green;">
						    	<?php echo "$".@$guesttotal;?>
						    </td>
					    </tr>
					     <?php if(@$guesttotalmessage) { ?>
					    <tr>
						    <td>
						    	<?php echo @$guesttotalmessage;?>
						    </td>
					    </tr>
					    <?php } ?>
				    </table>	
				    </div>
			</div>
		    </div>
		    <?php } ?>		  
		  
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

        
        <div id="selectItemWindow">
       		 <h2>Item Filter  &nbsp; <span class="imgselect" id="imgselect"></span> </h2>
                    
                    <div>
                            <input type="hidden" name="keyword" value="<?php echo isset($keyword)?$keyword:"";?>"/>
                            <input type="hidden" id="breadcrumbitem" name="breadcrumb"/>
                            <input type="hidden" id="formcategoryitem" name="category" value="<?php echo isset($_POST['category'])?$_POST['category']:"";?>"/>
                          
                            <div class="location control-group">
                            	<script>
								 function filtercategoryitems(id)
								 {
									 $("#formcategory").val(id);
									 getcatitem(id);
								  /* 
								    document.forms['categorysearchform'].submit();*/
								    //setTimeout("doPost()", 10);
								    return false;
								 }
							
								 
								</script>
								<a tabindex="0" href="#news-items-3" class="fg-button fg-button-icon-right ui-widget ui-state-default ui-corner-all" id="hierarchybreadcrumbitem">
									<span class="ui-icon ui-icon-triangle-1-s"></span><?php if(isset($catname) && $catname!="") echo $catname; else echo "Select Category"; ?>
								</a>
								<div id="news-items-3" class="hidden">
								    <?php echo @$categorymenuitems;?>
								</div>
                            </div>
                              <!-- <select name="catiditem" id="catiditem" ></select> -->
                            <div id="selectboxid"></div>
                       <div style="clear:both;"></div>
                       <div align="center"><button aria-hidden="true" data-dismiss="modal" class="btn btn-primary" type="button" onclick="javascript:savclose()">Save</button></div>
                       
                       &nbsp; <span style="margin-left:150px;" class="imgselectpos2" id="imgselectpos2"></span>
                       
                    </div>
        </div>

        
        
        
        
         
 <div id="newcompanymodal" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <i class="icon-credit-card icon-7x"></i>
          <h4 style="text-align:center;" class="semi-bold" id="myModalLabel">
          Companies          
          </h4>         
        </div>
        <div class="modal-body" id="newcompanybodyid">                
        </div>          
        <div class="modal-footer">
          <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>