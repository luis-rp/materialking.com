<?php //print_r($quoteitems);die;?>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery.price_format.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/fg.menu.css" type="text/css">
<link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/ui.all.css" type="text/css" id="color-variant-default">
<script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/fg.menu.2.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<script src="<?php echo base_url();?>templates/admin/js/jquery.ui.autocomplete.html.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">

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
<script type="text/javascript">

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
    $("#selectCategoryWindow").hide();
    $("#selectItemWindow").hide();
    $("#browseItemsFromStore").click(function(){
    	//$('#selectCategoryWindow').dialog({ height: 'auto'  });
    	$('#selectCategoryWindow').dialog({ height: '600'  });
        });
    $("#browseItem").click(function(){
    	$('#selectItemWindow').dialog({ height: "auto"  });
    });
       //toggleradius();

	// BUTTONS
	$('.fg-button').hover(
		function(){ $(this).removeClass('ui-state-default').addClass('ui-state-focus'); },
		function(){ $(this).removeClass('ui-state-focus').addClass('ui-state-default'); }
	);
	
	// MENUS    	
/*	$('#hierarchy').menu({
		content: $('#hierarchy').next().html(),
		crumbDefaultText: ' '
	});*/
	/*$('#hierarchyitem').menu({
	content: $('#hierarchyitem').next().html(),
	crumbDefaultText: ' '
});
*/


	$('#hierarchybreadcrumb').menu2({
		content: $('#hierarchybreadcrumb').next().html(),
		backLink: false
	});
	
	$('#hierarchybreadcrumbitem').menu2({
		content: $('#hierarchybreadcrumbitem').next().html(),
		backLink: false
	});
	
		 /* highlight matches text */
    /*var highlight = function (string) {
        $(".ui-autocomplete li.match").each(function () {
            var matchStart = $(this).text().toLowerCase().indexOf("" + string.toLowerCase() + "");
            var matchEnd = matchStart + string.length - 1;
            var beforeMatch = $(this).text().slice(0, matchStart);
            var matchText = $(this).text().slice(matchStart, matchEnd + 1);
            var afterMatch = $(this).text().slice(matchEnd + 1);
            $(this).html(beforeMatch + "<em>" + matchText + "</em>" + afterMatch);
        });
    };*/
 
 
    /* filter products */
    /*$("#itemcode").on("keypress change click input", function () {
        if (this.value.length > 0) {
            $(".ui-autocomplete li").removeClass("match").hide().filter(function () {
                return $(this).text().toLowerCase().indexOf($("#itemcode").val().toLowerCase()) != -1;
            }).addClass("match").show();
            highlight(this.value);
            $(".ui-autocomplete").show();
        }
        else {
            $(".ui-autocomplete, .ui-autocomplete li").removeClass("match").hide();
        }
    });*/

    $.widget( "app.autocomplete", $.ui.autocomplete, {
        
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
        
    });	
	
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
	var invname = $('input[name="supplyname[]"]').map(function(){return $(this).val();}).get();
	$('#suna').val(invname); 
	
	var invemail = $('input[name="supplyemail[]"]').map(function(){return $(this).val();}).get();
	$('#suem').val(invemail); 
	
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
	var projectid = document.getElementById('pid').value;
	var idid = codeid.replace('itemcode','itemid');
	var incrementid = codeid.replace('itemcode','itemincrement');
	var nameid = codeid.replace('itemcode','itemname');
	var unitid = codeid.replace('itemcode','unit');
	var eaid = codeid.replace('itemcode','ea');
	var quantityid = codeid.replace('itemcode','quantity');
	var notesid = codeid.replace('itemcode','notes');
	var totalpriceid = codeid.replace('itemcode','totalprice');
		if(itemcode!=""){
		var url = '<?php echo base_url()?>admin/quote/getitembycode';
		//alert(url);
		$.ajax({
			type:"post",
			data: "code="+encodeURIComponent(itemcode)+"&projectid="+encodeURIComponent(projectid),
			url: url
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
				document.getElementById(totalpriceid).value = document.getElementById(quantityid).value * obj.ea;
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

function calculatetotalprice(id)
{
	var quantityid = 'quantity'+id;
	var eaid = 'ea'+id;
	var totalpriceid = 'totalprice'+id;
	document.getElementById(totalpriceid).value = document.getElementById(eaid).value * document.getElementById(quantityid).value;

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

function viewminprices(codeid,quant, priceid)
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

function selectcompany(codeid, company, price, lowpricenote)
{
	
	var betterprice = codeid.replace('itemid','betterprice');	
	$('#'+betterprice).html('');
	var priceid = codeid.replace('itemid','ea');
	$("#"+priceid).val(price);
	
	$('#'+betterprice).html(lowpricenote);
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


</script>


<script type="text/javascript">
$(function() {
    <?php  /*if($this->validation->itemchk=='1') { ?>
            $('#attachchkbox').attr('checked','checked');
            <?php }*/ ?>
    //xmltype6();
    //autocomplete
    $(".costcode").autocomplete({
        source: "<?php echo base_url(); ?>admin/quote/findcostcode",
        minLength: 1
    });
    
    //autocomplete
    $(".itemcode").autocomplete({
        source: "<?php echo base_url(); ?>admin/quote/finditemcode?term=c",
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
			$('#selectboxid').html('<select name="catiditem" id="catiditem" ></select>');
			$.each(items,function(id,myItems) //here we're doing a foeach loop round each city with id as the key and city as the value
			{
				var opt = $('<option />'); // here we're creating a new select option with for each city
				opt.val(id);
				opt.text(myItems);
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

function nextinvite(count) {
	 	var count=parseInt(count)+1;
	 	$("#supplydata"+count).show();
	 	
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
	<h3 class="box-header"><?php echo $heading;?>  &nbsp;&nbsp;<!--<a style="font-size:12px;font-weight:normal;" target="_blank" href="<?php echo site_url().'admin/quote/bids/';?>">View Bids</a> - &nbsp;<a style="font-size:12px;font-weight:normal;" target="_blank" href="<?php echo site_url().'admin/message/messages/';?>">View Messages</a>--> </h3>
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
		   <input type="hidden" id="suem" name="suem" value=""/>
		   <input type="hidden" id="suna" name="suna" value=""/>
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
			      Delivery or Pick-Up Date: &nbsp; &nbsp;
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
<script type="text/javascript">
function allowonlydigits(e,elementid,errorid){
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which!=46) {                   
      $("#"+errorid).html("Digits Only").show().fadeOut("slow");  
      $("#"+errorid).css('color','red');
      return false;
    } }
    
    


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
			     <h3 class="box-header" style="display:inline;""> <label class="control-label">Items</label></h3>
                            
		    </div>
                  
                  <!-- <div align="right" style="padding-right: 20px;padding-bottom:10px;  ">
                      <b>Include Item Attachments <input type="checkbox" name="attachchkbox"  id="attachchkbox" onchange="xmltype6()" ></b></div> -->
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
		    			<input type="hidden" id="itemincrement<?php echo $q->id;?>" name="itemincrement<?php echo $q->id;?>" value="<?php echo $q->increment;?>"/>
			    		<input type="text" required class="span12 itemcode itemcodeold" id="itemcode<?php echo $q->id;?>" name="itemcode<?php echo $q->id;?>" value="<?php echo $q->itemcode;?>" onblur="fetchItem('itemcode<?php echo $q->id;?>');" onchange="showhideviewprice('<?php echo $q->id;?>');"/>
			    		<a href="javascript:void(0)" onclick="viewminprices('itemid<?php echo $q->id;?>','quantity<?php echo $q->id;?>','ea<?php echo $q->id;?>')">View Prices</a>
		    		</td>
		    		<td>
			    		<textarea id="itemname<?php echo $q->id;?>" name="itemname<?php echo $q->id;?>" required <?php if ($this->session->userdata('usertype_id') == 2){echo 'readonly';}?>><?php echo htmlentities($q->itemname);?></textarea>
		    		</td>
		    		<td><input type="text" class="highlight nonzero nopad width50 input-sm span12" id="quantity<?php echo $q->id;?>" name="quantity<?php echo $q->id;?>" value="<?php echo $q->quantity;?>" onblur="checkupdateincrementquantity(this.value,'<?php echo $q->id;?>'); calculatetotalprice('<?php echo $q->id?>');" 
onkeypress="return allowonlydigits(event,'quantity<?php echo $q->id;?>', 'eaerrmsg<?php echo $q->id;?>')" ondrop="return false;" onpaste="return false;"/><br>
		    		<span id="eaerrmsg<?php echo $q->id;?>"></span>
		    		<span style="color:red" id="incrementmessage<?php echo $q->id?>"></span> </td>
		    		
		    		<td><input type="text" class="span12" id="unit<?php echo $q->id;?>" name="unit<?php echo $q->id;?>" value="<?php echo $q->unit;?>" required/></td>
		    		<td>
		    			<div class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="text" class="highlight nonzero nopad width50 input-sm span9" id="ea<?php echo $q->id;?>" name="ea<?php echo $q->id;?>" value="<?php echo $q->ea;?>" onblur="calculatetotalprice('<?php echo $q->id?>')"
 onkeypress="return allowonlydigits(event,'ea<?php echo $q->id;?>', 'eaerrmsg1<?php echo $q->id;?>')" ondrop="return false;" onpaste="return false;" required/>
						<br/>
		    			</div> <span id="eaerrmsg1<?php echo $q->id;?>" ></span>	
		    			<br><?php $minpricenote = ""; if(count($minprices)>0) foreach($minprices[$q->itemid] as $minp){
		    						if($q->ea>$minp->price){
		    							$minpricenote = "* Lower price available"; break;
		    						}
		    			}?>
		    			<span id="betterprice<?php echo $q->id;?>"><?php echo @$minpricenote;?></span>
		    		</td>
		    		<td>
		    			<div class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="text" id="totalprice<?php echo $q->id;?>" class="highlight nonzero nopad width50 input-sm span9 price totalamount-old" name="totalprice<?php echo $q->id;?>" value="<?php echo $q->totalprice;?>" 
 onkeypress="return allowonlydigits(event,'totalprice<?php echo $q->id;?>', 'eaerrmsg2<?php echo $q->id;?>')" ondrop="return false;" onpaste="return false;" required/><br/> 
		    			</div><span id="eaerrmsg2<?php echo $q->id;?>"></span>	
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
		    			<input type="hidden" id="itemincrement" name="itemincrement" />
		    			<input type="text" id="itemcode" name="itemcode" required class="span itemcode" onblur="fetchItem('itemcode');" onchange="showhideviewprice('');"/>
		    			<span id="showpricelink"><a href="javascript:void(0)" onclick="viewminprices('itemid',0,0)">View Prices</a></span>
		    			<span id="showpricelinkbrow"><a href="javascript:void(0)" id="browseItem">Browse Item</a></span>
		    			<div><span id="showItemsFromStore"><a href="javascript:void(0)" id='browseItemsFromStore'>Browse Items From Store</a></span></div>
                    </td>
		    		<td>
		    			<textarea id="itemname" name="itemname" required <?php if ($this->session->userdata('usertype_id') == 2){echo 'readonly';}?>></textarea>
		    		</td>
		    		<td><input type="text" id="quantity" name="quantity" class="span12" onblur="return checkincrementquantity(this.value);calculatetotalprice('')" required  onkeyup="this.value=this.value.replace(/[^0-9]/g,'');"/><br><span style="color:red" id="incrementmessage"></span></td>
		    		<td><input type="text" id="unit" name="unit" class="span12"/></td>
		    		<td>
		    			<div class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="text" id="ea" name="ea" class="span9 price" onblur="calculatetotalprice('')"  onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" required/>						
		    			</div>
		    			<br><span id="betterprice"></span>
		    		</td>
		    		<td>
		    			<div class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="text" id="totalprice" name="totalprice" class="span9 price totalamount-new"  onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" required/>
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
    		    			
    		    			
    		    			<?php if(@$this->validation->quoteattachment){?>
                        	<br>
                        	<a href="<?php echo site_url('uploads/quote').'/'.@$this->validation->quoteattachment ;?>" target="_blank">  &nbsp;
                        	View Attachment
                          	</a>
                          	<?php }?>
                          	</div> 
    			
			</div>
		    <?php }?>
		    
		    <?php if($quoteitems){?>
		     <div class="span12">
		    	
		    <div class="span3">			    	
				    <div class="controls">
				    	Our Price Guestimate:$<?php echo @$guesttotal;?>
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
			<?php if($quoteitems){?>   
		    <div class="span3">
			    
				    <div class="controls">
				    <label class="control-label">&nbsp;</label>
				    	<input type="submit" value="Upload" class="btn btn-primary"/>
				    </div>
			   </div>
		    <?php } ?>	   
		    </div>
	    </form>
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
                              <!-- <div>
                                  Include Internet and retailers
                                  <input type="checkbox" name="internetret" id="internetret">
                                  </div> -->
                              <div>
                                 Supply Network
                                 <input type="checkbox" name="supplynet" id="supplynet" checked >
                                  </div><br>
                           
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
					    </div><br/>
					    
					    
					    <label class="control-label"><strong>Invite Via Email:</strong></label>
					      <hr/>
					    <div class="controls">
					    	 <div id="supplydata"> 
                                 Name:<input type="text" name="supplyname[]" id="supplyname"  style="width:80px;">&nbsp;
                                 Email:<input type="email" name="supplyemail[]" id="supplyemail"  style="width:120px;">&nbsp;
                                 <input type="button" name="nextsup" id="nextsup" class="btn btn-default" value="Add Another" onclick="nextinvite('0')">                                            </div>
                            
                            <?php $i=1; while($i<=10) { ?>			
                             <div id="supplydata<?php echo $i;?>" style="display:none;float:left;"> 
                                 Name:<input type="text" name="supplyname[]" id="supplyname"  style="width:80px;">
                                 Email:<input type="email" name="supplyemail[]" id="supplyemail"  style="width:120px;">
                            <input type="button" name="nextsup" id="nextsup" class="btn btn-default" value="Add Another" onclick="nextinvite('<?php echo $i;?>')">                                </div><br>
                            	<?php $i++; }?>
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
        
        
        <div id="selectCategoryWindow">
       		 <h2>Item Filter</h2>
                    
                    <div>
                        
                        <form id="categorysearchform" name="categorysearchform" method="post" action="<?php echo base_url('site/items');?>">
                            <input type="hidden" name="keyword" value="<?php echo isset($keyword)?$keyword:"";?>"/>
                            <input type="hidden" id="breadcrumb" name="breadcrumb"/>
                            <input type="hidden" id="formcategory" name="category" value="<?php echo isset($_POST['category'])?$_POST['category']:"";?>"/>
                            
                            <div class="location control-group">
                            	<?php $this->load->view('site/catmenu.php');?>
                            </div>
                        </form>
                       
                    </div>
        </div>
        <div id="selectItemWindow">
       		 <h2>Item Filter</h2>
                    
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
                       
                    </div>
        </div>
        
   