<script type="text/javascript">
$.noConflict();
 </script>

<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>

<script type="text/javascript" src="<?php echo base_url();?>templates/front/js/ckeditor/ckeditor.js"></script>

<?php if(@$tier->tier1) echo '<script>var tier1='.$tier->tier1.';</script>'?>

<?php if(@$tier->tier2) echo '<script>var tier2='.$tier->tier2.';</script>'?>

<?php if(@$tier->tier3) echo '<script>var tier3='.$tier->tier3.';</script>'?>

<?php if(@$tier->tier4) echo '<script>var tier4='.$tier->tier4.';</script>'?>

<?php echo '<script>var itemupdateurl="'.site_url('inventory/updateitem').'";</script>'?>

<?php echo '<script>var itemcodeupdateurl="'.site_url('inventory/updateitemcode').'";</script>'?>

<?php echo '<script>var itemnameupdateurl="'.site_url('inventory/updateitemname').'";</script>'?>

<?php echo '<script>var partnumupdateurl="'.site_url('inventory/updatepartnum').'";</script>'?>

<?php echo '<script>var manufacturerupdateurl="'.site_url('inventory/updatemanufacturer').'";</script>'?>

<?php echo '<script>var itempriceupdateurl="'.site_url('inventory/updateitemprice').'";</script>'?>

<?php echo '<script>var minqtyupdateurl="'.site_url('inventory/updateminqty').'";</script>'?>

<?php echo '<script>var qtyavailableupdateurl="'.site_url('inventory/updateqtyavailable').'";</script>'?>

<?php echo '<script>var iteminstockupdateurl="'.site_url('inventory/updateiteminstock').'";</script>'?>

	<?php echo '<script>var updatebackorderurl="'.site_url('inventory/updatebackorder').'";</script>'?>

	<?php echo '<script>var updateshipfromurl="'.site_url('inventory/updateshipfrom').'";</script>'?>

<?php echo '<script>var itemsurl="'.site_url('inventory/itemsjson').'";</script>'?>

<?php echo '<script>var qtydiscountupdateurl="'.site_url('inventory/addqtydiscount').'";</script>'?>

<?php echo '<script>var viewqtydiscounturl="'.site_url('inventory/viewqtydiscount').'";</script>'?>

<?php echo '<script>var qtydeleteurl="'.site_url('inventory/deleteitemqtydiscount').'";</script>'?>

<?php echo '<script>var itempricecheckurl="'.site_url('inventory/updatecheckprice').'";</script>'?>

<?php echo '<script>var itemtierpricecheckurl="'.site_url('inventory/updatetierprice').'";</script>'?>

<?php echo '<script>var saleitemurl="'.site_url('inventory/saleitem').'";</script>'?>

<?php echo '<script>var blockitemurl="'.site_url('inventory/blockitem').'";</script>'?>

<?php echo '<script>var availpriceurl="'.site_url('inventory/availprice').'";</script>'?>

<?php echo '<script>var getallcompanypricesurl="'.site_url('inventory/getallcompanyprices').'";</script>'?>

<?php echo '<script>var setcompanypriceurl ="' . site_url('quote/setcompanyprice') . '";</script>' ?>

<?php echo '<script>var getcompanynamesurl="'.site_url('inventory/getcompanynames').'";</script>'?>

<?php echo '<script>var setnewcompanypriceurl ="' . site_url('inventory/setnewcompanyprice') . '";</script>' ?>

<?php echo '<script>var compricedeleteurl="'.site_url('inventory/compricedelete').'";</script>'?>

<?php echo '<script>var showbidpricehistoryurl ="' . site_url('inventory/showbidpricehistory') . '";</script>' ?>

<?php echo '<script>var setallitemsmanufacturerurl ="' . site_url('inventory/setallitemsmanufacturer') . '";</script>' ?>

<?php echo '<script>var setmasteroptionurl ="' . site_url('inventory/setmasteroption') . '";</script>' ?>

<?php echo '<script>var getpreloadoptionsurl ="' . site_url('inventory/getpreloadoptions') . '";</script>' ?> 

<?php echo '<script>var deletecompanyitemurl ="' . site_url('inventory/deletecompanyitem') . '";</script>' ?>

<?php echo '<script>var unsetallitemsmanufacturerurl ="' . site_url('inventory/unsetallitemsmanufacturer') . '";</script>' ?>

<script type="text/javascript" charset="utf-8">
	$(document).ready( function() {
	});
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
    function updateIteminstock(itemid,instock)
    {
    	instock = instock==true?1:0;
        var data = "itemid="+itemid+"&instock="+instock;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: iteminstockupdateurl
		    }).done(function(data){
				//alert(data);
		    });
    }


    function updateqtyavailable(itemid,qtyavailable)
    {
        var data = "itemid="+itemid+"&qtyavailable="+qtyavailable;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: qtyavailableupdateurl
		    }).done(function(data){

		    });
    }
    function updateItemisfeature(itemid,isfeature)
    {
    	isfeature = isfeature==true?1:0;
        var data = "itemid="+itemid+"&isfeature="+isfeature;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: itemupdateurl
		    }).done(function(data){
				//alert(data);
		    });
    }
    function updateIteminstore(itemid,instore)
    {
    	instore = instore==true?1:0;
        var data = "itemid="+itemid+"&instore="+instore;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: itemupdateurl
		    }).done(function(data){
				//alert(data);
		    });
    }
    function viewPricelist(itemcode,itemname,price,imgname)
    {    	
    	if (typeof tier1 !== "undefined") 
		{				
			$("#pricelisttierlabel1").css('display','');
			$("#pricelisttier1").html(parseFloat(parseFloat(price) + (tier1 * price/100)).toFixed(2));
		}
		else
		{
			$("#pricelisttierlabel1").css('display','none');
		}
	
		if (typeof tier2 !== "undefined") 
		{
			$("#pricelisttier2").html(parseFloat(parseFloat(price) + (tier2 * price/100)).toFixed(2));
			$("#pricelisttierlabel2").css('display','');
		}
		else
		{
			$("#pricelisttierlabel2").css('display','none');
		}
		
		if (typeof tier3 !== "undefined") 
		{
			$("#pricelisttier3").html(parseFloat(parseFloat(price) + (tier3 * price/100)).toFixed(2));
			$("#pricelisttierlabel3").css('display','');
		}
		else
		{
			$("#pricelisttierlabel3").css('display','none');
		}
		
		if (typeof tier4 !== "undefined") 
		{
			$("#pricelisttier4").html(parseFloat(parseFloat(price) + (tier4 * price/100)).toFixed(2));
			$("#pricelisttierlabel4").css('display','');
		}
		else
		{
			$("#pricelisttierlabel4").css('display','none');
		}
    	$("#pricelist").modal();
    	$("#pricelistitemcode").html(itemcode);
    	$("#pricelistitemname").html(itemname);
    	$("#pricelistitemimage").html('<img style="max-height: 120px; padding: 0px;width:80px; height:80px;float:left;" src='+imgname+'>');
    	price = Number(price);
    	$("#pricelistdefault").html(price.toFixed(2));
    //	$("#pricelisttier1").html(Number(price + (tier1 * price/100)).toFixed(2));
    }

    function viewqtydiscount(itemid,itemcode,itemname,price,imgname)
    {    	
    	$("#qtypricelist").modal();
    	$("#qtyitemcode").html(itemcode);
    	$("#qtyitemname").html(itemname);
    	$("#itemimage").html('<img style="margin-top:-5em ;  max-height: 120px; padding: 0px;width:100px; height:100px;float:left;" src='+imgname+'>');
    	price = Number(price);
    	$("#qtylistprice").html(price.toFixed(2));

    	var data = "itemid="+itemid;
    	$("#qtypriceplacer").html("");
    	$.ajax({
    		type:"post",
    		data: data,
    		url: viewqtydiscounturl
    	}).done(function(data){
    		if(data){

    			$("#qtypriceplacer").html("");
    			$("#qtypriceplacer").html(data);
    		}
    	});

    	$("#addiscount").html('<table><tr><td>Qty</td><td>Price</td><td>&nbsp</td></td><tr><td><input type="text" name = "discqty" id="discqty"></td><td><input type="text" name = "discprice" id="discprice"></td><td><input type="button" value = "Add" onclick="addqtydiscount();"><input type="hidden" name="qtyitemid" id="qtyitemid" value="'+itemid+'" </td></tr><table>');
    }


    function addqtydiscount(){

    	var data = "itemid="+$("#qtyitemid").val()+"&qty="+$("#discqty").val()+"&price="+$("#discprice").val();

    	$.ajax({
    		type:"post",
    		data: data,
    		url: qtydiscountupdateurl
    	}).done(function(data){
    		if(data){

    			$("#qtypriceplacer").html("");
    			$("#qtypriceplacer").html(data);
    			$("#htmlqtymessage").html("Quantity-Price details added successfully!");
    			$(".alert-success").css({display: "block"});
    		}
    	});

    }

function delqtydiscount(id,itemid){

    	$.ajax({
    		type:"post",
    		data: "id="+id,
    		url: qtydeleteurl,
    		sync:false
    	}).done(function(data){
    		if(data){
    			if(data=="success")
    			$("#htmlqtymessage").html("Quantity-Price details deleted successfully!");
    			else
    			$("#htmlqtymessage").html("*Error in deleting Quantity-Price details!");
    		}
    	});

    	var data2 = "itemid="+itemid;

    	$.ajax({
    		type:"post",
    		data: data2,
    		url: viewqtydiscounturl,
    		sync:false
    	}).done(function(data){
    		if(data){

    			$("#qtypriceplacer").html("");
    			$("#qtypriceplacer").html(data);
    		}
    	});

    }

    function updatecheckprice(itemid,price)
    {
    	price = price==true?1:0;
    	var data = "itemid="+itemid+"&price="+price;
    	//alert(data);
    	$.ajax({
    		type:"post",
    		data: data,
    		url: itempricecheckurl
    	}).done(function(data){
    		//alert(data);
    	});
    }

    function updateistierprice(itemid,tierprice)
	{
    	tierprice = tierprice==true?1:0;
    	var data = "itemid="+itemid+"&tierprice="+tierprice;
    	//alert(data);
    	$.ajax({
    		type:"post",
    		data: data,
    		url: itemtierpricecheckurl
    	}).done(function(data){
    		//alert(data);
    	});
    }

       function saleitem(saleitemdata)
      {
    	saleitemdata = saleitemdata==true?1:0;
    	//document.write(saleitemdata);
        var data = "saleitemdata="+saleitemdata;
        $.ajax({
		      type:"post",
		      data: data,
		      url: saleitemurl
		    }).done(function(data){
		    });
      }
      
       function blockitem(blockitemdata)
      {
    	blockitemdata = blockitemdata==true?1:0;
    	//document.write(saleitemdata);
        var data = "blockitemdata="+blockitemdata;
        $.ajax({
		      type:"post",
		      data: data,
		      url: blockitemurl
		    }).done(function(data){
		    });
      }
      
      function availableprice(availprice)
      {
    	availprice = availprice==true?1:0;
    	//document.write(saleitemdata);
        var data = "availprice="+availprice;
        $.ajax({
		      type:"post",
		      data: data,
		      url: availpriceurl
		    }).done(function(data){
		    });
      }
      
      function updatebackorder(itemid,backorder)
    {
    	backorder = backorder==true?1:0;
        var data = "itemid="+itemid+"&backorder="+backorder;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: updatebackorderurl
		    }).done(function(data){
				//alert(data);
		    });
    }
    
    function updateshipfrom(itemid,shipfrom)
    {
    	shipfrom = shipfrom==true?1:0;
        var data = "itemid="+itemid+"&shipfrom="+shipfrom;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: updateshipfromurl
		    }).done(function(data){
				//alert(data);
		    });
    }

    
    function preloadoptions(itemid,imgname,itemcode)
    {
    	$("#masterdefaultbody").html('');
    	$.ajax({
		type:"post",
		data: "itemid="+itemid,
		dataType: 'json',
		url: getpreloadoptionsurl
	}).done(function(data){
		
		if(data){
			var chtml = "";
			
			chtml += '<div class="row form-row"><div class="col-md-2">Manufacturer</div><div class="col-md-3">Part No.</div><div class="col-md-3">Item Name</div><div class="col-md-2">List Price</div><div class="col-md-2">Min. Qty.</div></div> ';
			
			$.each(data, function( index, value ) {
				
				chtml +='<div class="row form-row"><div class="col-md-2">'+value.title+'</div><div class="col-md-3"><span style="word-break:break-all !important;">'+value.partnum+'</span></div><div class="col-md-3"><span>'+value.itemname+'</span></div><div class="col-md-2"><span>'+value.price+'</span></div><div class="col-md-2"><span>'+value.minqty+'</span><span>&nbsp;&nbsp;<input type="checkbox" class="check'+itemid+'" name="check'+value.id+'" id="check'+value.id+'" value="'+value.id+'" onclick="setmasteroption(\''+value.id+'\',\''+itemid+'\',\''+value.manufacturer+'\',\''+value.partnum+'\',\''+value.itemname+'\',\''+value.price+'\',\''+value.minqty+'\',\''+value.itemcode+'\')"></span></div></div>';
				
			});			
							
			$("#masterdefaultbody").html(chtml);	
			
			$("#masterdefaultmodal").modal();   	
    		$("#masterimagelogo_").html('<img style="max-height: 120px; padding: 0px;width:90px; height:90px;float:left;" src='+imgname+'>');
    		$("#itemcodedisplay_").html(itemcode);
			
		}else
    		alert("No Preloaded Options Exist for this item");
    		
	}); 
    	
	}	
    
    
    function showallcompanyprice(itemid,itemname,itemcode,imgname)
    {
	$('#myModalbody').html('');	
	var phtml = "";
    $("#allcompanypricesmodal").modal();	
    $("#pricelistitemcode2").html(itemcode);
    $("#pricelistitemname2").html(itemname);
    $("#itemimageall").html('<img style="margin-top:-1em ;  max-height: 120px; padding: 0px;width:100px; height:100px;float:left;" src='+imgname+'>');
   	$.ajax({
		type:"post",
		data: "itemid="+itemid,
		dataType: 'json',
		url: getallcompanypricesurl
	}).done(function(data){
		
		if(data){
			phtml += '<div class="row form-row"><div class="col-md-6"><strong>Name:</strong></div><div class="col-md-6"><strong>Price:</strong></div></div>';		var i =0;		
			$.each(data, function( index, value ) {
			
			phtml += '<div class="row form-row"><div class="col-md-6"><strong>'+value.companyname+'</strong></div><div class="col-md-6"><strong><input type="text" onblur="setcompanypriceprompt(this.value,'+value.company+','+value.itemid+','+value.purchasingadmin+');" value="'+value.price+'"/></strong> <span><a href="#"><img style="margin-left:5px;width:14px;" onclick="delcompanyprice('+value.company+','+value.itemid+','+value.purchasingadmin+')"  src="<?php echo base_url();?>templates/front/assets/img/icon/delete.ico" /></a></span> </div></div>';	
			i++;
				
			});
			
			if(i>0)
			$('#myModalbody').html(phtml);	
			else
			$('#myModalbody').html('No company prices exists');	
		}else{ 
			$('#myModalbody').html('No company prices exists');	
					
		}
	});
	
	var chtml = "";
	$.ajax({
		type:"post",
		data: "itemid="+itemid,
		dataType: 'json',
		url: getcompanynamesurl
	}).done(function(data){
		
		if(data){
			
			$.each(data, function( index, value ) {
			
				if(value.companyname=="")
				value.companyname = value.fullname;	
				
				chtml += '<option value="'+value.id+'">'+value.companyname+'</option>';	
				
			});			
		
			$("#addnewcompanyprice").html('<div class="col-md-6">&nbsp;&nbsp;<select name="selectcompany" id="selectcompany"><option value="">select Company</option>'+chtml+'</select></div><div class="col-md-6"><input type="text" name = "newcprice" id="newcprice"></td><td><input type="button" value = "Add" onclick="addcompanyprice('+itemid+');"></div>');	
		}
	});   	   	
    	
    }   
    
    
    function addcompanyprice(itemid){

    	if($("#newcprice").val()==0){
			alert('Price cannot be set to 0');	
		}else{
		$.ajax({
			type:"post",
			data: "val="+$("#newcprice").val()+"&itemid="+itemid+"&purchasingadmin="+$("#selectcompany").val(),
			url: setnewcompanypriceurl
		}).done(function(data){ 			 				
			
			/*$("#companypriceplacer").html("");
    		$("#companypriceplacer").html(data);*/
    		$("#myModalbody").append(data);
    		$('#newcprice').val('');
    		$('#selectcompany').val('');
    		alert("Company price added successfully!");
			
		});
		}

    }
    
    
    
    function delcompanyprice(companyid,itemid,purchasingadmin){

    	$.ajax({
    		type:"post",
    		data: "companyid="+companyid+"&itemid="+itemid+"&purchasingadmin="+purchasingadmin,
    		url: compricedeleteurl,
    		sync:true
    	}).done(function(data){
    		if(data){
    			if(data=="success")
    			alert("Company Price deleted successfully!");
    			else
    			alert("*Error in deleting Company Price");
    		}
    	});
    	
    	
		$('#myModalbody').html('');	
		var phtml = "";
    	$.ajax({
		type:"post",
		data: "itemid="+itemid,
		dataType: 'json',
		sync:true,
		url: getallcompanypricesurl
	}).done(function(data){
		
		if(data){
			phtml += '<div class="row form-row"><div class="col-md-6"><strong>Name:</strong></div><div class="col-md-6"><strong>Price:</strong></div></div>';		var i =0;		
			$.each(data, function( index, value ) {
			if(purchasingadmin!=value.purchasingadmin){
			phtml += '<div class="row form-row"><div class="col-md-6"><strong>'+value.companyname+'</strong></div><div class="col-md-6"><strong><input type="text" onblur="setcompanypriceprompt(this.value,'+value.company+','+value.itemid+','+value.purchasingadmin+');" value="'+value.price+'"/></strong> <span><a href="#"><img style="margin-left:5px;width:14px;" onclick="delcompanyprice('+value.company+','+value.itemid+','+value.purchasingadmin+')"  src="<?php echo base_url();?>templates/front/assets/img/icon/delete.ico" /></a></span> </div></div>';	
			i++;
			}
				
			});
			
			if(i>0)
			$('#myModalbody').html(phtml);	
			else
			$('#myModalbody').html('No company prices exists');	
		}else{ 
			$('#myModalbody').html('No company prices exists');	
					
		}
	});

    }

    
    
    
function setcompanypriceprompt(val,companyid,itemid,purchasingadmin){

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
						
			alert(data);			
			
		});
		}
	}
}
    
    
    function setmasteroption(id,itemid,manufacturerid,partnum,itemname,listprice,minqty,itemcode){

    	if ($('#check'+id).is(':checked') ) {
			$('.check'+itemid).prop('checked', false);
			$('#check'+id).prop('checked', true);
    		$('#itemnamedata'+itemid).val(itemname);
    		$('#selectoption'+itemid).val(manufacturerid);
    		$('#part'+itemid).val(partnum);
    		$('#price1'+itemid).val(listprice);
    		$('#minqty'+itemid).val(minqty);
			$('#itemcodedata'+itemid).val(itemcode);
    	}else{

    		$('#itemnamedata'+itemid).val('');
    		$('#selectoption'+itemid).val('');
    		$('#part'+itemid).val('');
    		$('#price1'+itemid).val('');
    		$('#minqty'+itemid).val('');
    		$('#itemcodedata'+itemid).val('');    		
    		itemname= "";
    		manufacturerid = "";
    		partnum = "";
    		listprice = "";
    		minqty = "";
    		itemcode = "";
    	}
    	
    	
    	$.ajax({
            type:"post",
            url: setmasteroptionurl,
            data: "itemid="+itemid+"&itemname="+itemname+"&manufacturer="+manufacturerid+"&partnum="+partnum+"&ea="+listprice+"&minqty="+minqty+"&itemcode="+itemcode
        }).done(function(data){    
        	
        	alert(data);
        	
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
            $("#pricehistory").html(arr[0]);
            $("#itemcode").html(arr[1]);
            $("#orderpricehistory").html(arr[2]);          
            $("#itemimagelogo").html('<img style="margin-top:-2em ;  max-height: 120px; padding: 0px;width:80px; height:80px;float:left;" src='+imgname+'>');          
            $("#historymodal").modal();
        });
    }
</script>
<?php echo '<script>var formurl = "'.site_url('inventory/showeditform').'";</script>';?>
<?php echo '<script>var dealurl = "'.site_url('inventory/showdealform').'";</script>';?>
<script>
function updateitem(id)
{
	var d = "itemid="+id;
	formurl = formurl+"/"+id;
    /*$.ajax({
        type: "post",
        url: formurl,
        data: d
    }).done(function(data) {
        $("#editbody").html(data);
        $("#editmodal").modal();
    });*/
    window.open(formurl,null,
"height=700,width=700,status=yes,toolbar=no,menubar=no,location=no");
}
function updatedeal(id)
{
	var d = "itemid="+id;
	dealurl = dealurl+"/"+id;
    /*$.ajax({
        type: "post",
        url: dealurl,
        data: d
    }).done(function(data) {
        $("#editbody").html(data);
        $("#editmodal").modal();
    });*/

    window.open(dealurl,null,"height=700,width=700,status=yes,toolbar=no,menubar=no,location=no");

}

function clearall(id)
{
	$("#name"+id).val("");
	$("#itemcodedata"+id).val("");
	$("#itemnamedata"+id).val("");
	$("#selectoption"+id).val("");
	$("#price1"+id).val('');
	$("#price"+id).attr('checked', false); 
	$("#part"+id).val("");	
	$("#tierprice"+id).attr('checked', false); 
	$("#instock"+id).attr('checked', false); 
	$("#stock"+id).val("");
	$("#instore"+id).attr('checked', false); 
	$("#isfeature"+id).attr('checked', false); 
	$("#backorder"+id).attr('checked', false); 
	$("#shipfrom"+id).attr('checked', false); 
	//$("#minqty"+id).val("").trigger('change');
	$("#minqty"+id).val("");
	
	var data = "itemid="+id;

        $.ajax({
		      type:"post",
		      data: data,
		      url: deletecompanyitemurl
		    }).done(function(data){
		    	if(data==1){
		    	alert("This Item was deleted successfully From Your Company."); }
		    	else
		    	{
		    		alert("item Not Found.");
		    	}

		    });
}



function setallmanufactureritems(){

	$("#manufacturermodal").modal();	

}


function setitemsmanufacturer(manufacturerid){
	
	if ($('#checkmanufacturer'+manufacturerid).is(':checked') ) {
	var instore = 0;	
	if(confirm("Would you also like to set all manufacturer items to be available in your online store?"))
	{
		instore = 1;
	}	
	
	$.ajax({
			type:"post",
			data: "manufacturer="+manufacturerid+"&instore="+instore,			
			url: setallitemsmanufacturerurl
		}).done(function(data){		
			
		 	alert(data);
		 	location.reload();
		});
		
		/*$('.checkmanufacturercls').prop('checked', false);
		$('#checkmanufacturer'+manufacturerid).prop('checked', true);*/
		
	}
	
	if (!$('#checkmanufacturer'+manufacturerid).is(':checked') ) {		
	
	$.ajax({
			type:"post",
			data: "manufacturer="+manufacturerid,			
			url: unsetallitemsmanufacturerurl
		}).done(function(data){		
			
		 	alert(data);
		 	location.reload();
		});
		
		/*$('.checkmanufacturercls').prop('checked', false);
		$('#checkmanufacturer'+manufacturerid).prop('checked', false);*/
		
	}
	
	
}

            
</script>

<style>
.awarded-to-me td
{
	color: green;
}
.not-awarded-to-me td
{
	color: red;
}
</style>
<?php //print_r($items);die;?>
    <div class="content">
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">

			<h3>Inventory <a href="<?php echo site_url('inventory/export'); ?>" class="btn btn-primary btn-xs btn-mini">Export</a> &nbsp;&nbsp;<a href="<?php echo site_url('inventory/inventoryPDF'); ?>" class="btn btn-primary btn-xs btn-mini">View PDF</a> 	<span style="float:right; margin:0px 9px 0px 0px"><a href="<?php echo site_url('store/items/' . $company->username); ?>" target="_blank" class="btn btn-primary btn-xs btn-mini">Go to my store</a></span></h3>
			<div class="pull-right">
				<a href="<?php echo site_url("store/items/".$this->session->userdata('company')->username);?>" target="_blank">

				</a>
			</div>
		</div>
	   <div id="container">
		<div class="row">
                    <div class="col-md-12">

                        <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                            <div class="pull-left">    
                            <a href="javascript:void(0)" onclick="setallmanufactureritems();">Set All Manufacturer Items</a>	
                                    &nbsp;&nbsp;&nbsp;&nbsp;    
                            </div> 
                                           
							<?php if($this->session->userdata('company')->company_type!='3') {?>
                                <div class="pull-right">                        
                                   
                                	<input type="checkbox" id ='availprice' name ='availprice' <?php echo $company->availprice?'checked="CHECKED"':''?>"
                                    onchange="availableprice(this.checked);"/>&nbsp;&nbsp;<span>Pricing Available to site members only.</span>&nbsp;&nbsp;
                                	
                 					<input type="checkbox" id ='blockitemdata' name ='blockitemdata' <?php echo $company->blockitemdata?'checked="CHECKED"':''?>"
                                    onchange="blockitem(this.checked);"/>&nbsp;&nbsp;<span>Lock my store to my members only.</span>&nbsp;&nbsp;

                 				
                 				<input type="checkbox" id ='saleitemdata' name ='saleitemdata' <?php echo $company->saleitemdata?'checked="CHECKED"':''?>"
                                    onchange="saleitem(this.checked);"/>&nbsp;&nbsp;<span>Do Not List My Items For Sale Online.</span>              			
                                </div>
                                <div style="clear:both;"></div>
                            <?php } ?>  
                            
                                <form action="<?php echo site_url('inventory');?>" method="post">
                                <table id="datatable" class="table table-bordered">

                                <tr>
                                	<td>Item Code/Name</td>
                                	<td>Category</td>
                                	<td>Brand</td>
                                	<td>Active Deals only</td> 
                                	<td>My items only</td> 
                                	<td>Filter Options</td>
                                	<td>Action</td>                             	
                                </tr>
                                <tr>                               	
                                	<td><input type="text" name="searchitem" value="<?php echo @$_POST['searchitem'];?>"/></td>                           	
                                	<td>
                                	<select id="category" name="category" class="form-control" style="width:140px">
                                		<option value="">All</option>
                                    	<?php foreach($categories as $cat){?>
                                    	<option value="<?php echo $cat->id;?>" <?php if(@$_POST['category']==$cat->id){echo 'selected';}?>><?php echo $cat->catname;?></option>
                                    	<?php }?>
                                    </select>
                                    </td>
                                   
                                	<td>
                                	<select id="manufacturer" name="manufacturer" class="form-control" style="width:90px">
                                		<option value="">All</option>
                                    	<?php foreach($manufacturers as $mf){?>
                                    	<option value="<?php echo $mf->id;?>" <?php if(@$_POST['manufacturer']==$mf->id){echo 'selected';}?>><?php echo $mf->title;?></option>
                                    	<?php }?>
                                    </select>
                                    </td>
                                    
                                   <td>
                                   		<input type="checkbox" name="activdeal" id="activdeal" <?php if(@$_POST['activdeal']!=""){echo 'checked';}?> >
                                   </td>
                                    
                                    <td>
                                   <input type="checkbox" name="serachmyitem" id="serachmyitem" <?php if(@$_POST['serachmyitem']!=""){echo 'checked';}?> >
                                    </td>
                                  
                                    <td>
                                    <select id="filteroption" name="filteroption" class="form-control" style="width:140px">
                                    <option value=''>All</option>
                                <option value="backorder" <?php if(@$_POST['filteroption']=="backorder"){echo 'selected';}?>>Shows Backorder Items Only</option>
                   <option value="shipfrom"  <?php if(@$_POST['filteroption']=="shipfrom"){echo 'selected';}?>>Shows Ships From Manufacturer Items Only</option>
             <option value="qtydiscount"  <?php if(@$_POST['filteroption']=="qtydiscount"){echo 'selected';}?>>Shows Items with Qty Discounts Only</option>
                              <option value="serachmyitem"  <?php if(@$_POST['filteroption']=="serachmyitem"){echo 'selected';}?>>Shows My Store Items Only</option>
                                <option value="isfeature"  <?php if(@$_POST['filteroption']=="isfeature"){echo 'selected';}?>>Shows Featured Items Only</option>                                 	
                                    </select>
                                    </td>
                                    
                                    <td>
                                	<input type="submit" value="Search" class="btn btn-primary btn-xs"/>
                                	<?php if(@$_POST['searchitem'] || @$_POST['category'] || @$_POST['manufacturer']){?>
                                	<a href="<?php echo site_url('inventory');?>">
                                		<input type="button" value="Clear" class="btn btn-primary btn-xs"/>
                                	</a>
                                	<?php }?>
                                	</td>
                                	</tr>
                                	</table>
                                </form>
                            </div>
                		    <?php
                		    	if($items)
                		    	{
                		    ?>
                            <div class="grid-body no-border">
                                    <table id="datatable" class="table no-more-tables general">
                                        <thead>
                                            <tr>
                                                <!-- <th style="width:10%">Item Code</th>  -->
                                                <th>Item Name</th>
                                                <th><font color="#fff">Code/Name</font></th>
                                                <th><font color="#fff">Manufacturer</font></th>
                                                <th><font color="#fff">Part#</font></th>
                                                <th><font color="#fff">List Price</font></th>
                                                <th><font color="#fff">Min. Qty.</font></th>
                                                <th><font color="#fff">Stock</font></th>
                                                <?php if($this->session->userdata('company')->company_type!='3') {?>
                                                <th><font color="#fff">Store/<br/>Featured</font></th>
                                                <th><font color="#fff">Action</font></th>
                                                <?php } ?>
                                            </tr>
                                        </thead>

                                        <tbody>
							              <?php
									    	$i = 0;
									    
									    	foreach($items as $item)
									    	{
									    		//echo "<pre>"; print_r($item); die;
									    		
											 if ($item->item_img && file_exists('./uploads/item/' . $item->item_img)) 
											 { 
											 	 $imgName = site_url('uploads/item/'.$item->item_img); 
											 } 
											 else 
											 { 
											 	 $imgName = site_url('uploads/item/big.png'); 
		                                     }
									    		$i++;
									      ?>
                                            <tr>
                                                <!-- <td class="v-align-middle"><?php echo $item->itemcode;?></td> -->
                                                <td class="v-align-middle">
                                                <span id="name<?php echo $item->id;?>"><?php echo $item->itemname;?></span>
                                                <br>
                                                <a href="javascript: void(0);" onclick="showbidhistory('<?php echo @$item->id ?>','<?php echo $imgName;?>');"><i class="icon icon-search"></i>Price History</a><br>
                                                <img src="<?php echo $imgName;?>" width="80" height="80">
                                                </td>

                                                <td class="v-align-middle">
                                                	<input type="text" class="form-control" placeholder="Itemcode" id="itemcodedata<?php echo $item->id;?>"
                                                	value="<?php echo @$item->companyitem->itemcode?>"
                                                	onchange="updateItemcode('<?php echo $item->id?>',this.value);"/>

                                                	<input type="text" class="form-control" placeholder="Itemname" id="itemnamedata<?php echo $item->id;?>"
                                                	value="<?php echo @$item->companyitem->itemname?>"
                                                	onchange="updateItemname('<?php echo $item->id?>',this.value);"/>
                                                </td>

                                                <td class="v-align-middle">
                                                	<select onchange="updateManufacturer('<?php echo $item->id?>',this.value);" class="form-control" id="selectoption<?php echo $item->id;?>">
                                                		<option value="">Select Manufacturer</option>
                                                		<?php foreach($manufacturers as $mf){?>
                                                			<option value="<?php echo $mf->id;?>" <?php if($mf->id == @$item->companyitem->manufacturer){echo 'SELECTED';}?>><?php echo $mf->title?></option>
                                                		<?php }?>
                                                	</select>
                                                	<br>
                                                	<a href="javascript:void(0)" onclick="preloadoptions('<?php echo htmlentities(@$item->id)?>','<?php echo $imgName;?>','<?php echo (@$item->companyitem->itemcode?$item->companyitem->itemcode:$item->itemcode)?>');">Select/View Preloaded Options</a>  
                                                <br>
                                                <a href="javascript:void(0)" onclick="showallcompanyprice('<?php echo htmlentities(@$item->id)?>','<?php echo htmlentities(addslashes(@$item->companyitem->itemname?$item->companyitem->itemname:$item->itemname))?>','<?php echo htmlentities(@$item->companyitem->itemcode?$item->companyitem->itemcode:$item->itemcode)?>','<?php echo $imgName;?>');">Select/View Company Prices</a>	
                                                </td>

                                                <td class="v-align-middle">
                                                	<input type="text" class="form-control" placeholder="Part#" id="part<?php echo $item->id;?>"
                                                	value="<?php echo @$item->companyitem->partnum?>"
                                                	onchange="updatePartnum('<?php echo $item->id?>',this.value);"/>
                                                </td>

                                                <td class="v-align-middle">
                                                	<input type="text"  class="form-control" placeholder="Price" id="price1<?php echo $item->id;?>"
                                                	value="<?php if(@$item->companyitem->ea != 0) echo @$item->companyitem->ea; ?>"
                                                	onchange="updateItemprice('<?php echo $item->id?>',this.value);" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');"/>
                                                	<?php if(@$item->companyitem->ea>0){?>
                                                	<a href="javascript:void(0)" onclick="viewPricelist('<?php echo htmlentities(@$item->companyitem->itemcode?$item->companyitem->itemcode:$item->itemcode)?>','<?php echo htmlentities(addslashes(@$item->companyitem->itemname?$item->companyitem->itemname:$item->itemname))?>','<?php echo @$item->companyitem->ea?>','<?php echo $imgName;?>');">
                                                		<i class="fa fa-search"></i>
                                                	</a>
                                                	<?php }?>
                                                	<br/>
                                                	<input type="checkbox" id ='price<?php echo $item->id;?>' name = 'price' <?php echo @$item->companyitem->price?'checked="CHECKED"':''?>"
                  											 onchange="updatecheckprice('<?php echo $item->id?>',this.checked);"/>&nbsp;Call for price&nbsp;
                                                </td>

                                                 <td class="v-align-middle">
                                                	<input type="text"  class="form-control" placeholder="Min Qty" id="minqty<?php echo $item->id;?>"
                                                	value="<?php echo (@$item->increment)?$item->increment:@$item->companyitem->minqty?>"
                                                	onchange="updateMinqty('<?php echo $item->id?>',this.value);" onkeyup="this.value=this.value.replace(/[^0-9]/g,'');"/><br/>
                                                	<a href="javascript: void(0)" onclick="viewqtydiscount('<?php echo $item->id?>','<?php echo htmlentities(@$item->companyitem->itemcode?$item->companyitem->itemcode:$item->itemcode)?>','<?php echo htmlentities(addslashes(@$item->companyitem->itemname?$item->companyitem->itemname:$item->itemname))?>','<?php echo @$item->companyitem->ea?>','<?php echo $imgName;?>');">Qty. Discounts</a>
                                                	<br/>
                                                	<input type="checkbox" id ='tierprice<?php echo $item->id;?>' name = 'tierprice' <?php echo @$item->companyitem->tierprice?'checked="CHECKED"':''?>"
                  											 onchange="updateistierprice('<?php echo $item->id?>',this.checked);"/>&nbsp;Apply Tier Price Disc. On Top of Qty. Disc. <?php if(@$item->increment && $item->increment != 1) echo "<br> *This qty. has been set to be sold in increments of ".$item->increment." only"; ?>
                                                </td>
                                                
                                                <td class="v-align-middle">
                                                	<input type="checkbox" 
                                                	<?php echo @$item->companyitem->instock?'checked="CHECKED"':''?>" id="instock<?php echo $item->id;?>"
                                                	onchange="updateIteminstock('<?php echo $item->id?>',this.checked);"/>

                                                	<input type="text" class="form-control" placeholder="Stock" id="stock<?php echo $item->id;?>"
                                                	value="<?php echo @$item->companyitem->qtyavailable?>"
                                                	onchange="updateqtyavailable('<?php echo $item->id?>',this.value);" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');"/>
                                                	
                                                	<input type="checkbox"  name="backorder" id="backorder<?php echo $item->id;?>" 
                                                	<?php echo @$item->companyitem->backorder?'checked="CHECKED"':''?>" 
                                                	onchange="updatebackorder('<?php echo $item->id?>',this.checked);"/>Backorder<br> 
                                                	                                              	
                                                	<input type="checkbox"  name="shipfrom" id="shipfrom<?php echo $item->id;?>" 
                                                	<?php echo @$item->companyitem->shipfrom?'checked="CHECKED"':''?>"
                                                	onchange="updateshipfrom('<?php echo $item->id?>',this.checked);"/>Ships From Manufacturer.
                                                </td>
                                                
                                                <?php if($this->session->userdata('company')->company_type!='3') {?>  
                                                <td class="v-align-middle">
                                                	<input type="checkbox"
                                                	<?php echo @$item->companyitem->instore?'checked="CHECKED"':''?>" id="instore<?php echo $item->id;?>"
                                                	onchange="updateIteminstore('<?php echo $item->id?>',this.checked);"/>

                                                	<input type="checkbox" 
                                                	<?php echo @$item->companyitem->isfeature?'checked="CHECKED"':''?>" id="isfeature<?php echo $item->id;?>"
                                                	onchange="updateItemisfeature('<?php echo $item->id?>',this.checked);"/>
                                                </td>
												
												
                                                <td class="v-align-middle">
                                                	<a href="javascript:void(0);" onclick="updateitem('<?php echo $item->id;?>')">My Store</a>
                                                	<br/>
                                                	<a href="javascript:void(0);" onclick="updatedeal('<?php echo $item->id;?>')">Deal Setup</a>
                                                	<br/>
                                                	<a href="javascript:void(0);" onclick="clearall('<?php echo $item->id;?>')">Clear Fields</a>
                                                </td>
                                               <?php } ?>
                                               
                                            </tr>
                                          <?php } ?>
                                        </tbody>
                                    </table>
                                    <br/>
                                    <div class="pagination pagination-centered">
                                        <?php $this->view('inventory/paging'); ?>
                                    </div>
                            </div>

                <?php } else { ?>
                        </div>
                    </div>
                </div>

                    <div class="errordiv">
      				<div class="alert alert-info">
	                  <button data-dismiss="alert" class="close"></button>
	                  <div class="msgBox">
	                  No Items Detected on System.
	                  </div>
	                 </div>
     	 		   </div>
                <?php }?>

		</div>
  </div>


  <div id="pricelist" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <i class="icon-credit-card icon-7x"></i>
          <h4 class="semi-bold" id="myModalLabel"><div id="pricelistitemimage"></div><br>
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
          <div class="row form-row"  id="pricelisttierlabel1" style="display:none;">
            <div class="col-md-8">
              Tier1 Price:
            </div>
            <div class="col-md-4">
              <span id="pricelisttier1"></span>
            </div>
          </div>
          <div class="row form-row"  id="pricelisttierlabel2" style="display:none;">
            <div class="col-md-8">
              Tier2 Price:
            </div>
            <div class="col-md-4">
              <span id="pricelisttier2"></span>
            </div>
          </div>
          <div class="row form-row"  id="pricelisttierlabel3" style="display:none;">
            <div class="col-md-8">
              Tier3 Price:
            </div>
            <div class="col-md-4">
              <span id="pricelisttier3"></span>
            </div>
          </div>
          <div class="row form-row"  id="pricelisttierlabel4" style="display:none;">
            <div class="col-md-8">
              Tier4 Price:
            </div>
            <div class="col-md-4">
              <span id="pricelisttier4"></span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

  <div id="editmodal" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none; min-width: 700px;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
        </div>
        <div class="modal-body" id="editbody">

        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>




  <div id="qtypricelist" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <i class="icon-credit-card icon-7x"></i>
          <div style="display: none;" class="alert alert-success fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button><div id="htmlqtymessage"></div></div>
          <h4 class="semi-bold" id="myModalLabel" style="width:80%;float:right;height:50px;">
          Price Details:
          <span id="qtyitemcode"  ></span>
          (<span id="qtyitemname"></span>)
          <br/> Qty. Discount Setup:
          </h4>
          <br><br>          
          <div id="itemimage"></div>
          <br><br><br>
        </div>

        <div class="modal-body">
          <div class="row form-row">
            <div class="col-md-8">
              List Price:
            </div>
            <div class="col-md-4">
              <span id="qtylistprice"></span>
            </div>
          </div>
          <div id="qtypriceplacer"></div>

          <div id="addiscount" class="row form-row"></div>

        </div>
        <div class="modal-footer">
          <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  
  
  
  <div id="masterdefaultmodal" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <i class="icon-credit-card icon-7x"></i>
          <h4 class="semi-bold" id="myModalLabel">         
          <div id="masterimagelogo_<?php // echo $masterdata->itemid;?>"></div>
           Master Default Options:          
           <div id="itemcodedisplay_<?php // echo $masterdata->itemid;?>"></div>
          </h4>
          <br>
        </div>
        <div id="masterdefaultbody" class="modal-body">            
    
         <!-- <div class="row form-row">
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
          </div>  --> 
              
        </div>
        <div class="modal-footer">
          <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  
  
  
  
   <div id="allcompanypricesmodal" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
                    <div class="modal-dialog">
                      <form id="companypriceform" action="" method="post">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
                          <br>
                          <i class="icon-credit-card icon-7x"></i>
                          <h4 class="semi-bold" id="myModalLabelchng" style="width:80%;float:right;">
                         All Companies Prices:       
                          <span id="pricelistitemcode2"></span>
          (<span id="pricelistitemname2"></span>)                   
                          </h4>
                          <div id="itemimageall"></div>
                          <br><br><br><br>
                        </div>
                        
                        <div class="modal-body" id="myModalbody">
                          <div class="row form-row">
                            <div class="col-md-6">
                              <strong>Name:</strong>
                            </div>
                            <div class="col-md-6">
                               <strong>Price:</strong>
                            </div>
                          </div>                          
                        </div>
                        
                        <div id="companypriceplacer"></div>
                        
                        <div id="addnewcompanyprice" class="row form-row"></div>
                        
                        <div class="modal-footer" id="myModalfooter">
                          <span id="pricebtn"><input type="button" class="btn btn-primary" data-dismiss="modal" class="close" value="close"/></span>
                        </div>
                      </div>
                      </form>
                    </div>
                  </div>
                  
  
<div id="historymodal" aria-hidden="true" aria-labelledby="myModalLabel2" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
	<div class="modal-dialog" style="width:70%">
	  <div class="modal-content">
	    <div class="modal-header">
	        <h4><span id='itemcode'></span> - Bid History 
	        <button aria-hidden="true" onclick="$('#historymodal').modal('hide')" class="close" type="button">x</button>
	        <!--<h3><span id="historycompanyname"></span> - Bid History </h3>-->
	          <div id="itemimagelogo"></div>
	          </h4>
             <br>
	    </div>
	    <div class="modal-body" id="pricehistory" style="height:250px;overflow-y:auto;">
	    </div>
	    <div class="modal-body" id="orderpricehistory" style="height:250px;overflow-y:auto;">
	    </div>
	 </div> 
  </div>
</div> 
                  




  
<div id="manufacturermodal" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <i class="icon-credit-card icon-7x"></i>
          <h4 class="semi-bold" id="myModalLabel">         
           Select Manufacturer To set all items to inventory:   
          </h4>
          <br>
        </div>
        <div class="modal-body">
        
        <div class="row form-row">
            <div class="col-md-3">
             Manufacturer
            </div>
            <div class="col-md-2">
             Set/Unset Items
            </div>                    
          </div> 
        
     <?php  foreach($manufacturers as $mf){ $count=0; ?>     
                 
             <?php
             $cquery = "SELECT m.*
					    FROM ".$this->db->dbprefix('item')." i join 
					    ".$this->db->dbprefix('masterdefault')." m on i.id = m.itemid and m.manufacturer = '".$mf->id."' 
					     group by m.itemid";
             $hasitems = $this->db->query($cquery)->result();
             
             if($hasitems){
             	
             	 /*$cquery1 = "SELECT m.*
					    FROM ".$this->db->dbprefix('companyitem')." m WHERE m.manufacturer = '".$mf->id."' 
					     group by m.itemid";
             	$hasitems1 = $this->db->query($cquery1)->result();*/
             	$count = count($hasitems);
             ?>
              <div class="row form-row">   
             <div class="col-md-3">
            <?php  echo $mf->title." (".$count.")";?>
            </div>            
             <div class="col-md-2">              
              <span>&nbsp;&nbsp;<input type="checkbox" class="checkmanufacturercls" name="checkmanufacturer<?php echo $mf->id;?>" id="checkmanufacturer<?php echo $mf->id;?>" <?php if(@$mf->applied_to_items==1) echo 'checked'; ?>  value="<?php echo $mf->id;?>" onclick="setitemsmanufacturer('<?php echo $mf->id;?>')"></span>
            </div>            
          </div>      
           <?php } } ?>
        </div>
        <div class="modal-footer">
          <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
        </div>
      </div>
      
    </div>
    
  </div>