<?php //echo '<pre>'; print_r($originalitems);die;?>
<?php echo "<script>var changetierurl='".site_url('company/changetier')."';</script>";?>
<?php echo "<script>var changeitemtierurl='".site_url('company/changeitemtier')."';</script>";?>
<?php echo "<script>var changepriceurl='".site_url('company/changeitemprice')."';</script>";?>
<?php // echo '<script>var getpriceqtydetails="' . site_url('site/getpriceqtydetails') . '";</script>' ?>
<?php echo '<script>var getpriceqtydetails="' . site_url('quote/getpriceqtydetails') . '";</script>' ?>
<?php echo '<script>var setcompanypriceurl ="' . site_url('quote/setcompanyprice') . '";</script>' ?>
<?php echo '<script>var getcompanypriceurl ="' . site_url('quote/getcompanyprice') . '";</script>' ?>
<?php echo '<script>var showpricehistoryurl ="' . site_url('quote/showpricehistory') . '";</script>' ?>

<?php echo '<script>var itemcodeupdateurl="'.site_url('inventory/updateitemcode').'";</script>'?>
<?php echo '<script>var itemnameupdateurl="'.site_url('inventory/updateitemname').'";</script>'?>
<?php echo '<script>var partnumupdateurl="'.site_url('inventory/updatepartnum').'";</script>'?>
<?php echo '<script>var manufacturerupdateurl="'.site_url('inventory/updatemanufacturer').'";</script>'?>
<?php echo '<script>var itempriceupdateurl="'.site_url('inventory/updateitemprice').'";</script>'?>
<?php echo '<script>var minqtyupdateurl="'.site_url('inventory/updateminqty').'";</script>'?>
<?php echo '<script>var showbidpricehistoryurl ="' . site_url('inventory/showbidpricehistory') . '";</script>' ?>


<?php if(@$tiers->tier1) echo "<script>var tier1=".$tiers->tier1."</script>";?>
<?php if(@$tiers->tier2)  echo "<script>var tier2=".$tiers->tier2."</script>";?>
<?php if(@$tiers->tier3)  echo "<script>var tier3=".$tiers->tier3."</script>";?>
<?php if(@$tiers->tier4)  echo "<script>var tier4=".$tiers->tier4."</script>";?>

<script type="text/javascript">
$.noConflict();
 </script>

<script type="text/javascript">
<!--
$(document).ready(function(){
	$(".source1").select2();
	$(".source3").select2();
	$('input[type="checkbox"]').checkbox();
	$('.date').datepicker();
        $('.expire_date').datepicker();
	$('.substituterow').hide();	
	
	$('.subtotcls').change(setsubtotal);   
	$('#subtotal').change(setfinaltotal);   
});

function setsubtotal(){
	var subtot = 0;
	$('.subtotcls').each(function(i,v){

		subtot = subtot + parseFloat(v.value);

	});
	$('#subtotal').val(subtot);
}

function setfinaltotal(){

	var tot = parseFloat($('#subtotal').val()) + parseFloat($('#subtotal').val()*<?php echo @$taxpercent/100; ?>);

	$('#finaltotal').val(tot)
}
    

function onover()
{
	$('.date').datepicker();
}

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
	$('.subtotcls').trigger('change');
	$('#subtotal').trigger('change');
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
function updateitem(id,itemid,itemcode,itemname,price,heading,imgname)
{	
	$("#itemmodal").modal();
	$("#itemform").trigger("reset");
	$("#itemformheading").html(heading);
	$("#itemformid").val(itemid);
	$("#inventoryitemimage").html('<img style="max-height: 120px; padding: 0px;width:80px; height:80px;float:left;" src='+imgname+'>');
	$("#preloaditemdata").html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" onclick=\'preloadoptions('+id+','+itemid+',"'+itemcode+'","'+imgname+'");\'>Select/View Preloaded Options</a>');
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
		    $('#'+$("#hiddenpriceid").val()).trigger("change");
		    $('#'+$("#hiddennotesid").val()).html(notes);
		    $('.subtotcls').trigger('change');
		    $('#subtotal').trigger('change');
	}
}

function viewPricelist(itemid, quantityid, priceid, purchasingadmin, itemcode, itemname, price, notelabel, quote, isdefault,isdiscount,imgname)
{
	$("#pricelist").modal();
	$("#itemnamebox").html('');
	$("#pricelistitemcode").html(itemcode);
	$("#pricelistitemname").html(itemname);	
	$("#itemimage").html('<img style="max-height: 120px; padding: 0px;width:80px; height:80px;float:left;" src='+imgname+'>');
	
	if(isdiscount==1)
	$('#qtydiscountbtn').css('display','block');
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

function allowonlydigits(e,elementid,errorid){
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which!=46) {    
        //display error message                
      $("#"+errorid).html("Digits Only").show().fadeOut("slow");  
      $("#"+errorid).css('color','red');
      return false;
    }

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
			$('#totalprice'+itemid).val(val*$('#quantity'+itemid).val());
		});
		}
	}
}


function showcompanyprice(companyid,itemid,purchasingadmin,itemcode,itemname,quote,imgname,id){
	
	$.ajax({
		type:"post",
		data: "companyid="+companyid+"&itemid="+itemid+"&purchasingadmin="+purchasingadmin,
		url: getcompanypriceurl
	}).done(function(data){
		$("#myModalbody").html('');
		if(data){
			$("#myModalbody").css('display','block');
			$("#myModalfooter").css('display','block');
			$("#myModalLabelchng").html('Company Price <br> <span id="pricelistitemcode2"></span> (<span id="pricelistitemname2"></span>)');
			$("#companypricemodal").modal();
			$("#pricelistitemcode2").html(itemcode);
			$("#pricelistitemname2").html(itemname);	
			$("#myModalbody").html(data);
			//$('#itemprice').val(data);
			var phtml = '<input type="button" class="btn btn-primary" onclick="setcompanypriceprompt2('+companyid+','+itemid+','+purchasingadmin+','+quote+','+id+');" value="Update"/>';
			$("#itemlogo").html('<img style="max-height: 120px; padding: 0px;width:90px; height:90px;float:left;" src='+imgname+'>');
			$("#pricebtn").html(phtml);
			
		}else{
			$("#itemlogo").html('<img style="max-height: 120px; padding: 0px;width:90px; height:90px;float:left;" src='+imgname+'>');
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


function setcompanypriceprompt2(companyid,itemid,purchasingadmin,quote,id){

	var val=$('#itemprice').val();
	if(confirm("Do you want to save this as company's price for this item?"))
	{
		if(val==0){
			alert('Price cannot be set to 0');	
		}else{
		$.ajax({
			type:"post",
			data: "companyid="+companyid+"&val="+val+"&itemid="+itemid+"&purchasingadmin="+purchasingadmin+"&quote="+quote,
			url: setcompanypriceurl
		}).done(function(data){
            $("#companypricemodal").modal('hide');	           
            $("#ea"+id).val(val);		
            $("#ea"+id).trigger("change");			
            $('.subtotcls').trigger('change');
            $('#subtotal').trigger('change');
			alert(data);			
			
		});
		}
	}
}

function addFileInput(count) 
	{
	 	var count=parseInt(count)+1;
	 	$("#quotefile"+count).show();	 	
	}
	

function preloadoptions(id,itemid,itemcode,imgname)
{
    $('#masterdefaultpricehid').val('');
	if($("#modal"+itemid).length > 0){
		$("#itemmodal").modal('hide');
		$("#modal"+itemid).modal();
		$('#masterdefaultpricehid').val(id);
		$("#masterimagelogo_"+itemid).html('<img style="max-height: 120px; padding: 0px;width:90px; height:90px;float:left;" src='+imgname+'>');
    	$("#itemcodedisplay_"+itemid).html(itemcode);
	}else
	alert("No Preloaded Options Exist for this item");
}

function displayitemmodal(itemid){
    
	$("#modal"+itemid).css('display', 'none');
 	$("#itemmodal").css('display', 'block');
 	$("#itemmodal").modal();
}	


function setmasteroption(id,itemid,manufacturerid,partnum,itemname,listprice,minqty,itemcode){

	if ($('#check'+id).is(':checked') ) {
		$('.check'+itemid).prop('checked', false);
		$('#check'+id).prop('checked', true);
		updateItemcode(itemid,itemcode);
		updateItemname(itemid,itemname);
		updatePartnum(itemid,partnum);
		updateManufacturer(itemid,manufacturerid);
		updateItemprice(itemid,listprice);
		updateMinqty(itemid,minqty);		
		
		alert("itemcode updated successfully");
		$("#ea"+$('#masterdefaultpricehid').val()).val(listprice);
		
		$("#itemformcodet").val(itemcode);
		$("#itemformnamet").val(itemname);
		$("#itemformpricet").val(listprice);
		
	}
}



    function updateItemcode(itemid,itemcode)
    {
        var data = "itemid="+itemid+"&itemcode="+itemcode;

        $.ajax({
		      type:"post",
		      data: data,
		      url: itemcodeupdateurl
		    }).done(function(data){

		    });
    }
    function updateItemname(itemid,itemname)
    {
        var data = "itemid="+itemid+"&itemname="+encodeURIComponent(itemname);

        $.ajax({
		      type:"post",
		      data: data,
		      url: itemnameupdateurl
		    }).done(function(data){
		    	//alert(data);
		    });
    }
    function updatePartnum(itemid,partnum)
    {
        var data = "itemid="+itemid+"&partnum="+partnum;
        $.ajax({
		      type:"post",
		      data: data,
		      url: partnumupdateurl
		    }).done(function(data){

		    });
    }
    function updateManufacturer(itemid,manufacturer)
    {
        var data = "itemid="+itemid+"&manufacturer="+manufacturer;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: manufacturerupdateurl
		    }).done(function(data){
		    	//alert(data);
		    });
    }
    function updateItemprice(itemid,itemprice)
    {
        var data = "itemid="+itemid+"&ea="+itemprice;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: itempriceupdateurl
		    }).done(function(data){

		    });
    }
    function updateMinqty(itemid,minqty)
    {
        var data = "itemid="+itemid+"&minqty="+minqty;
        
        if(minqty<=0)
        alert("Minimum Quantity required => 1");
        else{
        
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: minqtyupdateurl
		    }).done(function(data){

		    });
        }    
    }

     function showhistory(quoteid,companyid, itemid, imgname)
    {
        $.ajax({
            type:"post",
            url: showpricehistoryurl,
            data: "quoteid="+quoteid+"&companyid="+companyid+"&itemid="+encodeURIComponent(itemid)
        }).done(function(data){    
        	
        	var arr = data.split('*#*#$');        	
            $("#pricehistory").html(arr[0]);
            $("#itemcode").html(arr[1]);
            $("#historycompanyname").html(arr[2]+' to <br> ( '+arr[3] +' )');
            $("#itemimagelogo").html('<img style="max-height: 120px; padding: 0px;width:80px; height:80px;float:left;" src='+imgname+'>');
            $("#historymodal").modal();
        });
    }
    
    function showbidhistory(itemid,imgname)
    {
    	
    	$("#itemcode").html('');
        $.ajax({
            type:"post",
            url: showbidpricehistoryurl,
            data: "itemid="+itemid
        }).done(function(data){    
        	
        	var arr = data.split('*#*#$');        	
            $("#pricehistory1").html(arr[0]);
            $("#itemcode1").html(arr[1]);
            $("#orderpricehistory1").html(arr[2]);          
            $("#itemimagelogo1").html('<img style="margin-top:-2em ;  max-height: 120px; padding: 0px;width:80px; height:80px;float:left;" src='+imgname+'>');          
            $("#historymodal1").modal();
        });
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
                                   <?php 
		                           		$biditemPrice = 0;
		                           		if(isset($biditems) && $biditems != '')
		                           		{
		                           			foreach ($biditems as $key=>$val)
		                           			{
		                           				$biditemPrice += $val->totalprice;
		                           			}
		                           			$biditemPrice = $biditemPrice + ($biditemPrice * $taxpercent/100);
		                           		}
									?>
								<table class="no-more-tables general" border="0">
									<tr>
										<td style="width:60%;">
										  <strong>PO# : <?php echo $quote->ponum;?></strong>
										</td>
										<td style="width:40%;"><strong>Revision History</strong>
										</td>
									</tr>
									
									<tr>
										<td style="width:60%;">
									      <strong>Due : </strong> <?php echo $quote->duedate;?>								     
									    </td>
									    <td style="width:40%;">
									    	<strong>Number of Revisions : </strong><?php if(isset($revisionno)) echo $revisionno-1; else echo 0; ?>
									    </td>
									</tr>
									
									<tr>
									<td style="width:60%;">
										  <strong>Project : </strong> <?php if(isset($project)) echo $project->title;?>
									      <br/>
									      <strong>Company : </strong><?php echo $company->title;?>
									      <br/>
									      <strong>Contact : </strong><?php echo $company->contact;?>									      
									 </td>
									 <td style="width:40%;">
									      <!-- <a target="_blank" href="<?php echo site_url('quote/viewquote/'.$quote->id); ?>">Original</a><br> -->									       <?php if(isset($bid->id)) { $quotearr = explode(".",$bid->quotenum);  ?> <a href="<?php echo site_url('quote/viewbid/'.$bid->id);?>">Quote #: &nbsp;<?php echo $quotearr[0].".000"; ?></a>&nbsp; <strong>Date: </strong> <?php if(isset($bid->submitdate)) echo date("m/d/Y", strtotime($bid->submitdate)); else echo ''; ?> &nbsp; <strong>Total :</strong> $ <?php echo number_format($biditemPrice,2);?><br><?php } ?>
									      
									      
									     <?php  if(isset($revisionno)) { $quotearr = explode(".",$bid->quotenum);  for($i=2;$i<=$revisionno;$i++) 
									     {
									     	$str = explode("#$#$#",$bid->$i);
									     	$bidTotPrice = $str[1] + ($str[1] * $taxpercent /100);
									     	?><a href="<?php echo site_url('quote/viewbids/'.$bid->id.'/'.$i);?>">Quote #: &nbsp;<?php echo $quotearr[0]."."; printf('%03d',($i-1)); ?></a>&nbsp; <strong> Date:</strong> <?php if(isset($str[0])) echo date("m/d/Y", strtotime($str[0])); else echo ''; ?>  <strong> Total : </strong> $ <?php echo number_format($bidTotPrice,2);?> <br><?php } } ?> 
									  </td>
								   </tr>
								   
								   <tr>
									      <td style="width:60%;">   
									      	<br/>
									      	Please enter your Price EA, Date Available and add any Notes you may <br/>
											have related to each item. When you are finished, Click the Save Quote <br/>
											button.<br/><br/>
											Thank You,<br/>
											<strong><?php if(isset($purchasingadmin->companyname)) echo $purchasingadmin->companyname?></strong>
									     	<br/>
									     </td>
									     <td style="width:40%;">
		<span><strong>Company : </strong></span><span><?php echo @$purchasingadmin->companyname; ?></span><br />
		<span><strong>Contact Name : </strong></span><span><?php echo @$purchasingadmin->fullname; ?></span><br />
		<span><strong>Contact Phone : </strong></span><span><?php echo @$purchasingadmin->phone; ?></span><br />
		<span><strong>Contact Email : </strong></span><span><?php echo @$purchasingadmin->email; ?></span><br />
		<span><strong>Project : </strong></span><span><?php echo @$proname; ?></span><br />
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
							    		<th>&nbsp;</th>
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
									<?php 
									$distot="";
									foreach($quoteitems as $q)if(@$q->itemid){ 
										$distot += $q->totalprice;
										//echo "<pre>"; print_r($q); die;?>
									<?php if(@$originalitems[$q->itemid]){?>
							    	<tr>
							    		<td>
							    		<?php if(isset($q->orgitem->item_img) && $q->orgitem->item_img!= "" && file_exists("./uploads/item/".$q->orgitem->item_img)) 
								    		{ ?>
	                                        <?php $imgName = site_url('uploads/item/'.$q->orgitem->item_img);  } 
	                                        else { ?>
	                                        <?php $imgName = site_url('uploads/item/big.png');  } ?>
	                                        
							    		<?php echo $originalitems[$q->itemid]->itemname;?>
							    		<?php if(@$q->companyitem->ea){ if(@$q->companyitem->ea<=0){ $q->companyitem->ea=""; } } //if($q->showinventorylink){
										
										$itemCode = (@$q->companyitem->itemcode) ? (@$q->companyitem->itemcode) : (@$q->itemcode);
							    		$itemName = (@$q->companyitem->itemname) ? (@$q->companyitem->itemname) : (@$q->itemname);
							    		
							    		$itemCode1 = '"'.htmlentities($itemCode).'"';
							    		$itemName1 = '"'.htmlentities($itemName).'"';
										?>
							    		<!--<a href="javascript:void(0)" 
							    			onclick="updateitem(<?php echo html_escape("'$q->id', '$q->itemid',
							    		'".$itemCode."',
							    		'".$itemName."',
							    		'".htmlentities(@$q->companyitem->ea)."',
							    		'".html_escape(@$q->orgitem->itemname)."'");?>)">
							    			<i class="fa fa-edit"></i>
							    		</a>-->
							    		
							    		<a href='javascript:void(0)' 
							    			onclick='updateitem(<?php echo html_escape("'$q->id', '$q->itemid',
							    		".$itemCode1.",
							    		".$itemName1.",
							    		'".htmlentities(@$q->companyitem->ea)."',
							    		'".html_escape(@$q->orgitem->itemname)."'");?>,"<?php echo $imgName;?>")'>
							    			<i class="fa fa-edit"></i>
							    		</a>
							    		
							    		<?php //}?>
							    		<?php if($q->attachment){?>
							    			<a href="<?php echo site_url('uploads/item/'.$q->attachment);?>" target="_blank">View</a>
							    		<?php }?>
							    		<br>
                                                <a href="javascript: void(0);" onclick="showbidhistory('<?php echo @$q->itemid ?>','<?php echo $imgName;?>');"><i class="icon icon-search"></i>Company Price History</a>
							    		</td>
							    		<td>&nbsp;</td>
							    		<td><?php echo $originalitems[$q->itemid]->quantity;?></td>
							    		<td><?php echo $originalitems[$q->itemid]->unit;?></td>
							    		<td><?php echo $originalitems[$q->itemid]->ea==0?"RFQ":"$".$originalitems[$q->itemid]->ea;?></td>	
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
								    		<br>
								    		<?php if(isset($q->orgitem->item_img) && $q->orgitem->item_img!= "" && file_exists("./uploads/item/".$q->orgitem->item_img)) 
								    		{ ?>
	                                                 <img style="max-height: 120px; padding: 5px;" height="120" width="120" src="<?php echo site_url('uploads/item/'.$q->orgitem->item_img) ?>" alt="<?php echo $q->orgitem->item_img;?>">
	                                        <?php $imgName = site_url('uploads/item/'.$q->orgitem->item_img);  } 
	                                        else { ?>
	                                            <img style="max-height: 120px; padding: 5px;" src="<?php echo base_url(); ?>templates/site/assets/img/default/big.png" alt="">
	                                        <?php $imgName = site_url('uploads/item/big.png');  } ?>
							    		</td>
							    		<td>
							    		<?php if(@$q->firstplacebid && file_exists("./uploads/logo/firstplace.jpg")) 
								    		{ ?>
	                                                 <img style="max-height: 120px; padding: 5px;" height="75" width="75" src="<?php echo site_url('uploads/logo/firstplace.jpg') ?>" alt="<?php echo "First Place";?>">
	                                        <?php }?> 
							    		</td>
							    		<td><input type="text" class="highlight nonzero nopad width50 input-sm" id="quantity<?php echo $q->id;?>" name="quantity<?php echo $q->id;?>" value="<?php echo $q->quantity;?>" onblur="calculatetotalprice('<?php echo $q->id?>')" onkeypress="return allowonlydigits(event,'quantity<?php echo $q->id;?>', 'eaerrmsg<?php echo $q->id;?>');" ondrop="return false;" onpaste="return false;" /> <br/> &nbsp;<span id="eaerrmsg<?php echo $q->id;?>"></span>
							    								    		
							    		</td>
							    		<td><input type="text" class="nopad width50" id="unit<?php echo $q->id;?>" name="unit<?php echo $q->id;?>" value="<?php echo $q->unit;?>"/></td>
							    		<td>
							    		
							    			<?php if(@$q->companyitem->ea && @$q->companyitem->ea!=0){?>
							    			<a href="javascript:void(0)" onclick="viewPricelist('<?php echo $q->itemid; ?>','quantity<?php echo $q->id;?>','ea<?php echo $q->id;?>','<?php echo $q->purchasingadmin;?>','<?php echo htmlentities(addslashes((@$q->companyitem->itemcode)?$q->companyitem->itemcode:$q->itemcode))?>','<?php echo htmlentities(addslashes((@$q->companyitem->itemname)?$q->companyitem->itemname:$q->itemname))?>','<?php echo @$q->companyitem->ea?>', 'notelabel<?php echo $q->id;?>','<?php echo @$q->quote;?>','<?php if (@$q->companyitem->ea!="" || @$q->companyitem->ea!="") echo "true"; else echo "false"; ?>','<?php echo @$q->isdiscount;?>','<?php echo $imgName;?>')">
							    				<i class="fa fa-search"></i>
							    			</a>
							    			<?php }?>
											<input type="text" class="highlight nonzero nopad width50 input-sm" id="ea<?php echo $q->id;?>" name="ea<?php echo $q->id;?>" value="<?php echo $q->ea;?>" onchange="calculatetotalprice('<?php echo $q->id?>');"  onkeypress="return allowonlydigits(event,'ea<?php echo $q->id;?>', 'eaerrmsg1<?php echo $q->id;?>');" ondrop="return false;" onpaste="return false;"   onblur="setcompanypriceprompt(this.value,'<?php echo $company->id; ?>','<?php echo $q->itemid?>','<?php echo @$q->quote;?>','<?php echo @$q->purchasingadmin;?>');" /> <br/> &nbsp;<span id="eaerrmsg1<?php echo $q->id;?>"/> <label id="notelabel<?php echo $q->id;?>" name="notelabel<?php echo $q->id;?>" ><?php if(isset($q->noteslabel)) echo $q->noteslabel;?></label>
							    			<input type="hidden" id="ismanual<?php echo $q->id?>" name="ismanual<?php echo $q->id?>" value="<?php echo @$q->ismanual;?>"/> <br>  <?php if(@$q->ispriceset){ ?><a href="javascript:void(0)" onclick="showcompanyprice('<?php echo $company->id; ?>','<?php echo $q->itemid?>','<?php echo @$q->purchasingadmin;?>','<?php echo htmlentities(addslashes((@$q->companyitem->itemcode)?$q->companyitem->itemcode:$q->itemcode))?>','<?php echo htmlentities(addslashes((@$q->companyitem->itemname)?$q->companyitem->itemname:$q->itemname))?>','<?php echo @$q->quote;?>','<?php echo $imgName;?>','<?php echo $q->id;?>')">
							    			Edit Company Price
							    		</a><?php }?>
							    		<br>
							    	<?php 	
							    		$itemCode = (@$q->companyitem->itemcode) ? (@$q->companyitem->itemcode) : (@$q->itemcode);
							    		$itemName = (@$q->companyitem->itemname) ? (@$q->companyitem->itemname) : (@$q->itemname);
							    		
							    		$itemCode1 = '"'.htmlentities($itemCode).'"';
							    		//$itemName1 = '"'.htmlentities($itemName).'"';
																	    		
							    		if(@$q->priceset == 0)
							    		{ ?><a href="javascript:void(0)" 
							    			onclick="updateitem(<?php echo html_escape("'$q->id', '$q->itemid',
							    		".$itemCode1.",
							    		'".html_escape($itemName)."',
							    		'".htmlentities(@$q->companyitem->ea)."',
							    		'".html_escape(@$q->orgitem->itemname)."'");?>,'<?php echo $imgName;?>')">
							    			*Set List Price
							    		</a><?php }
							    	
							    		
							    		if(@$q->comppriceset == 0)
							    		{ ?><a href="javascript:void(0)" onclick="showcompanyprice('<?php echo $company->id; ?>','<?php echo $q->itemid?>','<?php echo @$q->purchasingadmin;?>','<?php echo htmlentities(addslashes((@$q->companyitem->itemcode)?$q->companyitem->itemcode:$q->itemcode))?>','<?php echo htmlentities(addslashes((@$q->companyitem->itemname)?$q->companyitem->itemname:$q->itemname))?>','<?php echo @$q->quote;?>','<?php echo $imgName;?>','<?php echo $q->id;?>')">
							    			*Set Company Price
							    		</a><?php }?>
							    		
							    		<?php if(@$q->companyitem->company != '' && @$q->companyitem->itemid != '') { ?>		
							    		<a href="javascript: void(0);" onclick="showhistory('<?php echo @$q->quote ?>','<?php echo @$q->companyitem->company ?>','<?php echo @$q->companyitem->itemid ?>','<?php echo $imgName;?>')"><i class="icon icon-search"></i>Price History</a>	
							    		<?php }?>
							    		</td>
							    		<td>	
											<input type="text" id="totalprice<?php echo $q->id;?>" class="price highlight nonzero nopad width50 input-sm subtotcls" name="totalprice<?php echo $q->id;?>" value="<?php echo $q->totalprice;?>" onkeypress="return allowonlydigits(event,'ea<?php echo $q->id;?>', 'eaerrmsg2<?php echo $q->id;?>')" ondrop="return false;" onpaste="return false;" /> <br/> &nbsp;<span id="eaerrmsg2<?php echo $q->id;?>"/>
											
							    		</td>
							    		
							    		<td>
							    			<input type="text" class="date highlight nopad" name="daterequested<?php echo $q->id;?>" value="<?php echo $q->daterequested;?>" data-date-format="mm/dd/yyyy" onclick="onover()" onchange="$('#costcode<?php echo $q->id;?>').focus();" style="width: 100px;"/>
							    			
							    			<?php if($q->willcall){
							    			    echo '<br/>For Pickup/Will Call';
							    			}?>
							    			<input type="hidden" name="willcall<?php echo $q->id?>" value="<?php echo $q->willcall;?>"/>
							    		</td>
							    		<td><textarea style="width: 150px" id="notes<?php echo $q->id;?>" name="notes<?php echo $q->id;?>" class="highlight"><?php echo $q->notes;?></textarea></td>
							    		<td><input type="checkbox" name="nobid<?php echo $q->id;?>" value="1" class="checkbox nopad"/></td>
							    		<td><input type="checkbox" id="substitute<?php echo $q->id;?>" name="substitute<?php echo $q->id;?>" 
							    			value="1" onchange="checksubstitute('<?php echo $q->id;?>');" class="checkbox nopad"/></td>
							    	</tr>							 
							    	<tr id="substituterow<?php echo $q->id;?>" class="substituterow" style="display:none;">
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
							    	<?php  }?>
							    	<tr>
							    		<td>
											Quote#
							    		</td>
							    		<td colspan="3">
							    		<?php $sub=strtoupper($this->session->userdata('company')->title); $subst=substr($sub,0,4); $fstr=$subst."Q";?>
											<input type="text" name="quotenum" value="<?php if(isset($revisionno) && isset($quotenum) && $quotenum!="") { $quotearr = explode(".",$bid->quotenum); echo $quotearr[0]."."; printf('%03d',($revisionno)); } elseif(isset($quotenum) && $quotenum!="") { echo $quotenum; } else { echo $fstr;   printf('%06d',($invid)); echo ".000"; } ?>"/>
							    		</td>
							    		<td style="text-align:right;">
							    		<span><strong>SubTotal : </strong></span>
							    		</td>
							    		<td><input type="text" id="subtotal" readonly value="<?php if(isset($distot) && $distot!="") { echo round($distot,2); } ?>" /> 
							    		</td>
							    		<td style="text-align:right;">
											<span><strong>Total : </strong></span>
							    		</td>
							    		<td><input type="text" id="finaltotal" readonly value="<?php if(isset($distot) && $distot!="") { 
												$distotwithtax = $distot + ($distot * $taxpercent/100);
												echo round($distotwithtax,2); } ?>" />
											 
							    		</td>
							    		<td colspan="2">
											&nbsp;
							    		</td>
							    	</tr>
							    	<?php if($quote->quoteattachment){?>
							    	<tr>
							    		<td>Attachment By Purchasing Admin:</td>
								<td colspan="10"><a href="<?php echo site_url('uploads/quote').'/'.$quote->quoteattachment ;?>" target="_blank">View File</a></td>	 	                                </tr> <?php }?>
							    	
								<tr>
							    		<td>Your Attachment</td>
										<td colspan="5">								    		
							    		<input type="file" name="quotefile[]" id="quotefile" onclick="addFileInput('0')" />
											<?php $i=1; while($i<=10) { ?>
												<input type="file" name="quotefile[]" id="quotefile<?php echo $i;?>" style="display:none;" onclick="addFileInput('<?php echo $i;?>')" />
											<?php $i++; }?>
							    		
											<?php if($quotefile)
												{ 
													$quotefilearray=explode(",",$quotefile);												
													foreach ($quotefilearray as $file)  { 
														if($file && file_exists("./uploads/quotefile/".$file)){ ?>													
												       <a href="<?php echo site_url('uploads/quotefile/'.$file);?>" target="_blank">View File</a>&nbsp;
												  <?php } } } ?>											
							    		</td>
							    		<td colspan="5"><?php if(@$largesms==1) { ?><span>This user is currently set to Credit Card only Status And Your Bank Account Settings are not set up. Please <a href="<?php echo site_url('company/bankaccount'); ?>" target="_blank" > Click Here. </a> to set up Your Bank Account Settings. <a href="<?php echo site_url('company/invoicecycle'); ?>" target="_blank"> Click Here. </a> to set up Users billing terms to allow purchasing on their account.</span> <?php } ?></td>
							    	</tr>								    	
                                    <tr>
                                    <td> Expire Date</td>
                                    <td colspan="10"> <input type="text" name="expire_date" class="expire_date" value="<?php echo (isset($expire_date) && $expire_date!="" && $expire_date!="0000-00-00")?date('m/d/Y',  strtotime($expire_date)):date("m/d/Y");?>"/>&nbsp; &nbsp; &nbsp; <a href="javascript:void(0)" onclick="setExpDate(30);">+30days</a>, <a href="javascript:void(0)" onclick="setExpDate(60);">+60Days</a>, <a href="javascript:void(0)" onclick="setExpDate(90);">+90Days</a></td>
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
	  
	  
	  
	  <div id="itemmodal" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
                    <div class="modal-dialog">
                      <form id="itemform" action="<?php echo site_url('inventory/updateitem/'.$invitation);?>" method="post">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
                          <br>
                          <i class="icon-credit-card icon-7x"></i><div id="inventoryitemimage"></div><br>
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
                              <input type="text" id="itemformpricet" name="ea" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" required>
                            </div>
                          </div>
                        </div>      
                        <span id="preloaditemdata">                 
                          </span> 
                        <div class="modal-footer">
                          <input type="submit" class="btn btn-primary" value="Save Change"/>
                        </div>
                      </div>
                      </form>
                    </div>
                  </div>
	  
	  
                  
  
   <?php $olditemid=""; $i=0; if(isset($masterdefaults)) { foreach ($masterdefaults as $masterdata){?>
    <?php if($olditemid!=$masterdata->itemid) {?>
    <div id="modal<?php echo $masterdata->itemid;?>" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" onclick="displayitemmodal(<?php echo $masterdata->itemid;?>);" type="button">x</button>
          <i class="icon-credit-card icon-7x"></i>
          <h4 class="semi-bold" id="myModalLabel">
           <div id="masterimagelogo_<?php echo $masterdata->itemid;?>"></div>
           Master Default Options:        
          <div id="itemcodedisplay_<?php echo $masterdata->itemid;?>"></div>  
          </h4>
          <br>
        </div>
        <div class="modal-body">
        
        <div class="row form-row">
            <div class="col-md-3">
             Manufacturer
            </div>
            <div class="col-md-2">
             Part No.
            </div>
             <div class="col-md-3">
             Item Name
            </div>
             <div class="col-md-2">
             List Price
            </div>
             <div class="col-md-2">
             Min. Qty.
            </div>
          </div> 
        
    <?php } ?>        
         <div class="row form-row">
            <div class="col-md-3">
             <?php echo $masterdata->title;?>
            </div>
            <div class="col-md-2">
              <span><?php echo $masterdata->partnum;?></span>
            </div>
             <div class="col-md-3">
              <span><?php echo $masterdata->itemname;?></span>
            </div>
             <div class="col-md-2">
              <span><?php echo $masterdata->price;?></span>
            </div>
             <div class="col-md-2">
              <span><?php echo $masterdata->minqty;?></span>
              <span>&nbsp;&nbsp;<input type="checkbox" class="check<?php echo $masterdata->itemid;?>" name="check<?php echo $masterdata->id;?>" id="check<?php echo $masterdata->id;?>" value="<?php echo $masterdata->id;?>" onclick="setmasteroption('<?php echo $masterdata->id;?>','<?php echo $masterdata->itemid;?>','<?php echo $masterdata->manufacturer;?>','<?php echo $masterdata->partnum;?>','<?php echo htmlentities(addslashes($masterdata->itemname));?>','<?php echo $masterdata->price;?>','<?php echo $masterdata->minqty;?>','<?php echo htmlentities(addslashes(@$masterdata->itemcode));?>')"></span> 
            </div>            
          </div>  
     <?php if($masterdata->itemid!=@$masterdefaults[$i+1]->itemid) {?>    
         
        </div>
        <div class="modal-footer">
         <input type="hidden" id="masterdefaultpricehid">
          <button data-dismiss="modal" class="btn btn-default" onclick="displayitemmodal(<?php echo $masterdata->itemid;?>);" type="button">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <?php } ?>    
  
  <?php $olditemid=$masterdata->itemid; $i++; } }?>              
                  
                  
                         
                  
                  
  <div id="pricelist" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <br>
          <i class="icon-credit-card icon-7x"></i>
          <h4 class="semi-bold" id="myModalLabel"><div id="itemimage"></div><br>
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
            <div class="col-md-8" > 
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
          <div class="row form-row">
            <div class="col-md-8"  id="pricelisttierlabel3" style="display:none;">
              Tier3 Price: 
            </div>
            <div class="col-md-4">
              <span id="pricelisttier3"></span>
            </div>
          </div>
          <div class="row form-row">
            <div class="col-md-8"  id="pricelisttierlabel4" style="display:none;">
              Tier4 Price: 
            </div>
            <div class="col-md-4">
              <span id="pricelisttier4"></span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
         <button class="btn btn-default" id="qtydiscountbtn" style="float: left;display:none;" type="button" onclick="showqtydiscount(<?php echo $company->id; ?>);" >View available qty. discounts</button> <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
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
                          <br><div id="itemlogo"></div>
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
  
<div id="historymodal" aria-hidden="true" aria-labelledby="myModalLabel2" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
	<div class="modal-dialog">
	  <div class="modal-content">
	    <div class="modal-header">
	        <h4><div id="itemimagelogo"></div> <span id='itemcode'></span></h4>
	        <button aria-hidden="true" onclick="$('#historymodal').modal('hide')" class="close" type="button">x</button>
	        <h3>Price History - <span id="historycompanyname"></span></h3>
	    </div>
	    <div class="modal-body" id="pricehistory" style="height:150px;overflow-y:auto;">
	    </div>
	 </div> 
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
 </div> 	  
<div id="historymodal1" aria-hidden="true" aria-labelledby="myModalLabel2" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
	<div class="modal-dialog" style="width:70%">
	  <div class="modal-content">
	    <div class="modal-header">
	        <h4><span id='itemcode1'></span> - Bid History 
	        <button aria-hidden="true" onclick="$('#historymodal1').modal('hide')" class="close" type="button">x</button>
	        <!--<h3><span id="historycompanyname"></span> - Bid History </h3>-->
	          <div id="itemimagelogo1"></div>
	          </h4>
             <br>
	    </div>
	    <div class="modal-body" id="pricehistory1" style="height:200px;overflow-y:auto;">
	    </div>
	    <div class="modal-body" id="orderpricehistory1" style="height:200px;overflow-y:auto;">
	    </div>
	 </div> 
  </div>
</div> 