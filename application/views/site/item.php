<?php //echo '<pre>'; print_r($inventory);die;?>
<?php echo '<script>var addtocarturl="' . site_url('cart/addtocart') . '";</script>' ?>
<?php echo '<script>var checkbankaccount="' . site_url('cart/checkbankaccount') . '";</script>' ?>

<?php echo '<script>var getpriceqtydetails="' . site_url('site/getpriceqtydetails') . '";</script>' ?>

<?php echo '<script>var getpriceperqtydetails="' . site_url('site/getpriceperqtydetails') . '";</script>' ?>

<?php echo '<script>var getnewprice="' . site_url('site/getnewprice') . '";</script>' ?>

<?php echo '<script>var addpoquoteurl = "' . site_url('site/addpoquote') . '";</script>' ?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/datatable.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/site/assets/js/jquery.elevatezoom.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery.timepicker.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">

<link href="<?php echo base_url(); ?>templates/admin/css/jquery.timepicker.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">

<style type="text/css">

 .videocontent {
	width:80%;
	max-width: 640px;
	margin: 0 auto;
}

 object {
position:absolute;
top:0px;
left:0px;}

 .ui-tooltip {
	padding: 8px;
	font-size:19px !important;
	font-weight:bold !important;
	position: absolute;
	z-index: 9999;
	max-width: 300px;
	-webkit-box-shadow: 0 0 5px #aaa;
	box-shadow: 0 0 5px #aaa;
	color:#06A7EA !important;
 }

</style>


<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script>
$(function() {
$( document ).tooltip();
});
</script>

<link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/jquery.jqzoom.css" type="text/css">
<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/modules/data.js"></script>
<script src="http://code.highcharts.com/modules/drilldown.js"></script>
<script>
	$(document).ready(function(){
		/*$('.jqzoom').jqzoom({
            zoomType: 'innerzoom',
            preloadImages: false,
            alwaysOn:false
        });*/

		<?php if(isset($item->zoom) && $item->zoom==1) {  ?> $("#contentimage").elevateZoom();  <?php } ?>
		 $("#bigimage").elevateZoom();
		 
	$('#deliverydate').datepicker();
	$('#podate').datepicker();
	$('#duedate').datepicker();

	});
	function setmiles(miles)
	{
		$("#radirange").val(miles);
		$("#searchform").submit();
	}

	function getExtension(filename) {
		var parts = filename.split('.');
		return parts[parts.length - 1];
	}

	function isVideo(filename) {
		var ext = getExtension(filename);
		switch (ext.toLowerCase()) {
			case 'm4v':
			case 'avi':
			case 'mpg':
			case 'mp4':
			case 'flv':
			case 'wmv':
			case 'movie':
			case 'mpeg':
			case 'mpe':
			case 'qt':
			case 'mov':
			// etc
			return true;
		}
		return false;
	}

	function changeimage(source, srctype)
	{
		if(srctype == 'video'){
			$('#videolist').css({display: "block"});
			$('#imagelist').css({display: "none"});

			$('embed').replaceWith($('embed').clone().attr('src','http://www.youtube.com/v/'+source));
    		document.getElementById('loading').style.display = 'none';
		}else {
		$('#imagelist').css({display: "block"});
		$('#videolist').css({display: "none"});
		$("#bigimage").attr('src',source);
        $("#bigimage").attr('data-zoom-image1',source);
        $("#bigimage").elevateZoom();
		}
	}
</script>
<script type="text/javascript" charset="utf-8">
    $(document).ready( function() {
        $("#requestLink").click(function(){
			$("#requestModalForm").modal();
            });
    	$("#daterequested").datepicker();
        $(".daytd",".request-form").datepicker();
        $(".timetd",".request-form").timepicker({
            'minTime': '6:00am',
            'maxTime': '11:30pm',
            'showDuration': false
        });

        $(".daytd",".request-modal-form").datepicker();
        $(".timetd",".request-modal-form").timepicker({
            'minTime': '6:00am',
            'maxTime': '11:30pm',
            'showDuration': false
        });

    	$('#datatable').dataTable( {
    		"sPaginationType": "full_numbers",
    		"aaSorting": [[ 8, "asc" ]],
    		"aoColumns": [
            		null,
            		null,
            		null,
            		null,
            		null,
            		null,
            		null,
            		null,
            		null,
            		{ "bSortable": false }
    			],
   			 "aoColumnDefs": [
                { "sType": 'currency', "aTargets": [5] }
                ]
    		} );

    	var validChars = "$��c" + "0123456789" + ".-,'";

        // Init the regex just once for speed - it is "closure locked"
        var str = jQuery.fn.dataTableExt.oApi._fnEscapeRegex( validChars ), re = new RegExp('[^'+str+']');


        jQuery.fn.dataTableExt.aTypes.unshift(
             function ( data )
                {
                        if ( typeof data !== 'string' || re.test(data) ) {
                                return null;
                        }

                        return 'currency';
                }
        );

        jQuery.extend( jQuery.fn.dataTableExt.oSort, {
                "currency-pre": function ( a ) {
                        a = (a==="-") ? 0 : a.replace( /[^\d\-\.]/g, "" );
                        return parseFloat( a );
                },

                "currency-asc": function ( a, b ) {
                        return a - b;
                },

                "currency-desc": function ( a, b ) {
                        return b - a;
                }
        } );



   	 	 $('.dataTables_length').hide();
      	 $('#datatable_filter').hide();
      	 $('#datatable_paginate').hide();
    	 $('#datatable_info').hide();
    })

</script>

<script>


    function setlabel()
    {
        $type = $("#requresttype").val();
        if($type=='Request Phone Assistance')
        {
            $("#daytd-label").html('Best Day To Call');
            $("#timetd-label").html('Best Time To Call');
        }
        else
        {
            $("#daytd-label").html('Appointment Date Requested');
            $("#timetd-label",".request-form").html('Appointment Time Requested');
        }

    }

    function setlabelReqModal()
    {
        $type = $("#requresttype-modal-req").val();
        if($type=='Request Phone Assistance')
        {
            $("#daytd-modal-label").html('Best Day To Call');
            $("#timetd-modal-label").html('Best Time To Call');
        }
        else
        {
            $("#daytd-modal-label").html('Appointment Date Requested');
            $("#timetd-modal-label").html('Appointment Time Requested');
        }

    }

    function addtocart(imgname,itemid, companyid, price, minqty, unit, itemcode,itemname,increment,isdeal,qtyreqd)
    { 
    	if(typeof(minqty)==='undefined') { miqty = 0; } 
    	if(increment==0) { increment=1;} 
    	if(typeof(isdeal)==='undefined') isdeal = 0;  		
        //var qty = prompt("Please enter the quantity you want to buy",minqty?minqty:"1");
        $('#cartqtydiv').html('');
		$("#cartsavediv").html('');
		$("#qtypricebox").html('');
		$("#itemnamebox").html('');
       	$("#hiddenprice").val(price);
       	$("#ftqtypricebox").html("Price "+unit+" :");
       	$("#ftqtypriceunitbox").html("$"+ price);
        $("#cartprice").modal();
        var selected = "";
        $("#itemnamebox").html(itemcode+"  /  "+itemname);
        $("#itemimage").html('<img width="120" height="120" style="max-height: 120px; padding: 0px;width:120px; height:120px;float:right;" src='+imgname+'>');
        $("#unitbox").html("Unit Type: "+unit+"<br/>");
        var strselect = ('Qty');
        strselect += '&nbsp;<select style="width:80px;" id="qtycart" onchange="showmodifiedprice('+itemid+','+companyid+','+price+','+isdeal+');">';
        
        /*for (i = increment; i <=500; i+=increment) {
        	if(i == minqty)
        	selected = 'selected';
        	else
        	selected = "";
           	strselect += '<option value="'+i+'"'+selected+'>'+i+'</option>';
   			}*/
   			
   		increment = parseInt(increment);
   		i = increment;
       	while(i <=500){
       		if(i == minqty)
        	selected = 'selected';
        	else
        	selected = "";
           	strselect += '<option value="'+i+'"'+selected+'>'+i+'</option>';
       		i=i+increment;	
       	}	 			
       
   		strselect += '</select>&nbsp;&nbsp; <input type="button" class="btn btn-primary" value="Add to cart" onclick="addtocart2('+itemid+','+companyid+','+price+','+minqty+','+isdeal+','+qtyreqd+')" id="addtocart" name="addtocart"/>';
        $('#cartqtydiv').html(strselect);
        //if(!isdeal) {
        	var data = "itemid="+itemid+"&companyid="+companyid+"&price="+price;
        	
        	
        	$("#qtypricebox").html("");
        	$.ajax({
        		type:"post",
        		data: data,
        		sync: false,
        		url: getpriceqtydetails
        	}).done(function(data){
        		if(data){

        			$("#qtypricebox").html(data);
        		}
        	});
			if($("#qtycart").val() != '')
        	{
        		minqty = $("#qtycart").val()
        	}
        	var data2 = "itemid="+itemid+"&companyid="+companyid+"&qty="+minqty+"&price="+price;

        	$.ajax({
        		type:"post",
        		data: data2,
        		sync: false,
        		url: getpriceperqtydetails
        	}).done(function(data){
        		if(data){

        			$("#cartsavediv").html("");
        			$("#cartsavediv").html(data);
        		}
        	});

        	$.ajax({
        		type:"post",
        		data: data2,
        		url: getnewprice,
        		sync:false
        	}).done(function(data){
        		if(data){

        			if(data!="norecord")
        			$("#hiddenprice").val(data);
        		}
        	});
       // }

    }
    
   /* function addtocart(itemid, companyid, price, minqty, unit, itemcode,itemname, isdeal,increment)
    {
    	if(typeof(minqty)==='undefined') { minqty = 0; }
    	if(typeof(isdeal)==='undefined') isdeal = 0;
        //var qty = prompt("Please enter the quantity you want to buy",minqty?minqty:"1");
        $('#cartqtydiv').html('');
		$("#cartsavediv").html('');
		$("#qtypricebox").html('');
		$("#itemnamebox").html('');
       	$("#hiddenprice").val(price);
        $("#cartprice").modal();
        var selected = "";
        $("#itemnamebox").html(itemcode+"  /  "+itemname);
        $("#unitbox").html("Unit Type: "+unit+"<br/>");
        var strselect = ('Qty');
        strselect += '&nbsp;<select style="width:80px;" id="qtycart" onchange="showmodifiedprice('+itemid+','+companyid+','+price+','+isdeal+');">';
        for (i = 1; i <=500; i++) {
        	if(i == minqty)
        	selected = 'selected';
        	else
        	selected = "";
           	strselect += '<option value="'+i+'"'+selected+'>'+i+'</option>';
   			}
   		strselect += '</select>&nbsp;&nbsp; <input type="button" class="btn btn-primary" value="Add to cart" onclick="addtocart2('+itemid+','+companyid+','+price+','+minqty+','+isdeal+')" id="addtocart" name="addtocart"/>';
        $('#cartqtydiv').html(strselect);
        if(!isdeal) {
        	var data = "itemid="+itemid+"&companyid="+companyid+"&price="+price;
        	$("#qtypricebox").html("");
        	$.ajax({
        		type:"post",
        		data: data,
        		sync: false,
        		url: getpriceqtydetails
        	}).done(function(data){
        		if(data){

        			$("#qtypricebox").html(data);
        		}
        	});

        	var data2 = "itemid="+itemid+"&companyid="+companyid+"&qty="+minqty+"&price="+price;

        	$.ajax({
        		type:"post",
        		data: data2,
        		sync: false,
        		url: getpriceperqtydetails
        	}).done(function(data){
        		if(data){

        			$("#cartsavediv").html("");
        			$("#cartsavediv").html(data);
        		}
        	});

        	$.ajax({
        		type:"post",
        		data: data2,
        		url: getnewprice,
        		sync:false
        	}).done(function(data){
        		if(data){

        			if(data!="norecord")
        			$("#hiddenprice").val(data);
        		}
        	});
        }

    } */

    function showmodifiedprice(itemid, companyid, price, isdeal){

    	qty = ($('#qtycart').val());
    	var data2 = "itemid="+itemid+"&companyid="+companyid+"&qty="+qty+"&price="+price;
    	//if(!isdeal) {
    		$.ajax({
    			type:"post",
    			data: data2,
    			sync: false,
    			url: getpriceperqtydetails
    		}).done(function(data){
    			if(data){

    				$("#cartsavediv").html("");
    				$("#cartsavediv").html(data);
    			}
    		});

    		$.ajax({
    			type:"post",
    			data: data2,
    			url: getnewprice,
    			sync:false
    		}).done(function(data){
    			if(data){

    				if(data!="norecord")
    				{
    					$("#hiddenprice").val(data);
    					$("#ftqtypriceunitbox").html("$"+ data);
    				}
    				else
    				{
    					$("#ftqtypriceunitbox").html("$"+ price);
    				}
    			}
    		});
    	//}
    }

    function addtocart2(itemid, companyid, price, minqty, isdeal,qtyreqd){
       
    	qty = ($('#qtycart').val());

    	if(isNaN(parseInt(qty)))
        {
            return false;
        }
        if(qty < minqty)
        {
            alert('Minimum quantity to order is '+ minqty);
            return false;
        }
        
        /*if(qtyreqd!=1){
        if(qty < qtyreqd)
        {
            alert('Minimum quantity required to order is '+ qtyreqd);
            return false;
        }}*/
        
        var data2 = "company=" + companyid;
        
        $.ajax({
            type: "post",
            data: data2,
            url: checkbankaccount,
            sync:false
        }).done(function(data) {
            if(data!='true'){            	
            	alert('Supplier has not set bank account settings');
            	return false;
            }else{

            	var data = "itemid=" + itemid + "&company=" + companyid + "&price=" + $("#hiddenprice").val() + "&qty=" + qty + "&isdeal=" + isdeal;
            	//alert(data); return false;
            	$.ajax({
            		type: "post",
            		data: data,
            		url: addtocarturl,
            		sync:false
            	}).done(function(data) {
            		alert(data);
            		window.location = window.location;
            	});

            }

        });

    }
</script>




<?php echo '<script>var costcodeurl = "' . site_url('site/getcostcodes') . '";</script>' ?>
<?php echo '<script>var quoteurl = "' . site_url('site/getquotes') . '";</script>' ?>

<script>
    function getquotecombo()
    {
    	var pid = $("#additemproject").val();
    	d = "pid="+pid;
    	$.ajax({
            type: "post",
            url: quoteurl,
            data: d
        }).done(function(data) {
            $("#additempo").html(data);
        	//document.getElementById("additempo").innerHTML = data;
        });

    }

    function getcostcodecombo()
    {
    	var pid = $("#additemproject").val();
    	d = "pid="+pid;
    	$.ajax({
            type: "post",
            url: costcodeurl,
            data: d
        }).done(function(data) {
            $("#additemcostcode").html(data);
        });
    }

</script>
<?php echo '<script>var rfqurl = "' . site_url('site/additemtoquote') . '";</script>' ?>
<script>
	function addtopo(itemid, increment)
	{
		$("#addform").trigger("reset");
		$("#additemid").val(itemid);
		if(increment>0){
		$("#additemqty").val(increment);
		$("#incrementqty").val(increment);
		}else{
		$('#additemqty').val('');
		$("#incrementqty").val(1);
		}
		//$('#additemproject').attr('selectedIndex',0);
		//$('#additemproject option:first-child').attr("selected", "selected");
		//document.getElementById('additemproject').value=2;		
		$("#additempo").html('<select name="quote" required></select>');
		$('#additemcostcode').html('<select name="costcode" required></select>');
		getquotecombo();
		getcostcodecombo()
		$("#addtoquotemodal").modal();
	}
	
	function addtopo1(quote)
	{
		//var serviceurl = '<?php echo base_url()?>admin/itemcode/ajaxdetail/'+ itemid;

		var string = '<h3>RFQ created for the item.</h3><div><a target="_blank" href="<?php echo site_url("admin/quote/update/"); ?>/'+quote+'">Click here to view the Quote</a><br/><br/><br/><button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>';
		$("#modalhtm").html(string);
		$("#addtoquotemodal1").modal();

	}
	
	function rfqformsubmit()
	{
		if($('#additemqty').val()%$("#incrementqty").val()!=0){
			alert('Sorry this item is only available in increments of '+$("#incrementqty").val());
			return false;
		}
		
		var quote = $('[name="quote"]').val();
		var d = $("#addtoquoteform").serialize();

        $.ajax({
            type: "post",
            url: rfqurl,
            data: d
        }).done(function(data) {
            if (data == 'Success')
            {
                //alert('RFQ created for the item.')
                addtopo1(quote);
            }
            else
            {
                alert(data);
            }
            $("#addtoquotemodal").modal('hide');
        });
        return false;
	}
	
	
	function changetab(tabname){
		if(tabname == 'pricetab'){
			$('.property-detail').css('display','none');
			$("#button").css("background-color","#00bbe4"); 
			$(".titlebox").css("background-color","#00bbe4"); 
			$(".page-header").css("background-color","#00bbe4"); 
			$('#iframes').css('display','block');
		}else{
			$('.property-detail').css('display','block');
			$("#button").css("background-color","#06a7ea"); 
			$(".titlebox").css("background-color","#06a7ea"); 
			$(".page-header").css("background-color","#06a7ea"); 
			$('#iframes').css('display','none');
		}
		
	}
	
	
	    function addpo(){

    	var pid=$('#additemproject').val();
    	if(pid){

    		$('#addtoquotemodal').modal();
    		//$('#additemproject').val('');
    		$('#addtoquotemodal').modal('hide');
    		$('#Addpomodal').modal();
    	}
    }
    
    
    function savepo(){
    	
    	var pid=$('#additemproject').val();
    	var ponum = $("#ponum").val();
    	if(ponum=="")
    		alert("Please Enter PO");
    	
    	var podate = $("#podate").val();
		var duedate = $('#duedate').val();
		var deliverydate = $('#deliverydate').val();
		
		var d = "pid="+pid+"&ponum="+ponum+"&podate="+podate+"&duedate="+duedate+"&deliverydate="+deliverydate;
		
		$.ajax({
			type: "post",
			url: addpoquoteurl,
			dataType: 'json',
			data: d
		}).done(function(data) {			
			if(data=="Duplicate PO#"){
				alert(data);
			}else{		
					var option = new Option(data.ponum, data.poid);
					$('[name="quote"]').append($(option));
					$('[name="quote"]').val(data.poid);
					$('#Addpomodal').modal('hide');
					$("#addtoquotemodal").modal();
				
			}
		});
    }
	
	
</script>

<div id="content">
    <div class="container">
        <div id="main">
            <div class="row">
            			<div class="location control-group" style="margin:0% 0% 0% 2.5%; width:97.5%">
               				<form id="categorysearchform" name="categorysearchform" method="post" action="<?php echo base_url('site/items');?>">
                            <input type="hidden" name="keyword" value="<?php echo isset($keyword)?$keyword:"";?>"/>
                            <input type="hidden" id="breadcrumb" name="breadcrumb"/>
                            <input type="hidden" id="formcategory" name="category" value="<?php echo isset($_POST['category'])?$_POST['category']:"";?>"/>                  
                           	<?php $this->load->view('site/catmenu.php');?>
                   			</form>                        
                        
                        	<form id="manufacturersearchform" name="manufacturersearchform" method="post" action="<?php echo base_url('site/items');?>">
                            <!-- <input type="hidden" name="keyword" value="<?php // echo isset($keyword)?$keyword:"";?>"/>
                            <input type="hidden" id="breadcrumb" name="breadcrumb"/> -->
                            <input type="hidden" id="formmanufacturer" name="manufacturer" value="<?php echo isset($_POST['manufacturer'])?$_POST['manufacturer']:"";?>"/>                            
                            	<?php $this->load->view('site/manufacturermenu.php');?>                            
                        	</form>
                      </div>   
                     
                <div class="span9">
                	<div class="breadcrumb-pms"><ul class="breadcrumb"><?php echo $breadcrumb;?></ul></div>
                	<?php if(isset($item->searchquery) && $item->searchquery!="") { ?>              	
                	<div>
	                  <button type="button" id="button" class="btn btn-primary btn-lg" onclick="changetab('pricetab');" style="border-radius: 10px;">
	                   <strong>Price Check</strong></button>	                	
                 	</div>
                 	<?php } ?>
                	
                    <h3 class="titlebox">
                	 <div class="span4">
                	 	<a href="#" onclick="changetab('itemtab');">
                	 		<h2 class="page-header"><b><?php echo $item->itemcode;?></b></h2>
                	 	</a>
                	 </div>
                            	<!-- AddThis Button BEGIN -->
                    <div class="span4"><div class="addthis_toolbox addthis_default_style ">
                            	<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
                            	<a class="addthis_button_tweet"></a>
                            	<a class="addthis_counter addthis_pill_style"></a>
                            	</div>
                   <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-536087a3159911fb"></script>
                 <!-- AddThis Button END -->
              </div>
                    </h3>



                    <div class="property-detail">
                             <div class="Quotedetail">
                         <?php echo (isset($totalQuote))?$totalQuote:"0";?> Lifetime Buying Leads Posted on MaterialKing.com
                    </div>
                    
                    <div>
                    
                      <?php if(isset($cat_title))  { ?>
	                        <div style="margin-top: 8px;margin-left: 2px;margin-bottom: 8px">
								<div style="background:#007BC3;color:#FFFFFF;font-weight:bold!important;width:385px;height:23px;margin-left: 20px; padding:8px;"><?php if(isset($cat_title)) { echo $cat_title;} ?> </div>
								<div style="background:#2A2A2A;opacity:.80;color:#FFFFFF;width:385px;height:45px;margin-left: 20px;padding:8px;"><?php if(isset($cat_text)) { echo $cat_text;} ?> </div>
							</div>
						<?php } ?>
                        
                        <?php  if(isset($cat_image) && $cat_image!= "" && file_exists("./uploads/category-banners/".$cat_image)) { ?>
                        <div class="category-image" style="margin: 0px 0px 5px;">
                            <img src="<?php echo site_url('uploads/category-banners/'.$cat_image);?>" class="cat-image" style="width:830px; height:200px;">
                        </div>
                        <?php } ?>
                        
                       
						
				    </div>
                        <div class="pull-left overview">
                            <div class="row">
                                <div class="span3" style="text-align: center">

                                    <div class="clearfix">
                                        <div id="imagelist" <?php if($filetype=='video') { ?> style="display:none;" <?php } ?> class="clearfix">
                                            <a href="javascript:void(0);" rel='gal1'>
                                                <img id="bigimage" alt="<?php echo $item->item_img_alt_text;?>" src="<?php echo (@$item->images[0]->filename && file_exists("./uploads/item/".$item->images[0]->filename))?site_url('uploads/item/' . $item->images[0]->filename):site_url('uploads/item/big.png'); ?>" data-zoom-image1="<?php echo (@$item->images[0]->filename && file_exists("./uploads/item/".$item->images[0]->filename)) ?site_url('uploads/item/' . $item->images[0]->filename):site_url('uploads/item/big.png'); ?>"  style="border: 4px solid #666;with:250px;height:250px">
                                            </a>

                                        </div>
                                        <div  id="videolist" <?php if($filetype=='video') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?> class="clearfix">
                                        <embed class="youtube-player" type="text/html" width="260" height="280" src="">
                                        <div id="loading" style="float:left; width:100%; height:100%; text-align:center;">
        									<span style="background-color:Yellow; font-size:xx-large">Please Wait while loading</span>
   										</div>
                                        </div>
                                    	<br/>

                                    	<div class="clearfix" >
                                    	<ul id="thumblist" class="clearfix" >
                                    		<?php foreach($item->images as $img){?>
                                    		<li>
                                    		<?php if (isset($img->is_video) && $img->is_video ==1 ) { ?>
                                    		<a class="zoomThumbActive" href='javascript:void(0);'  onclick="changeimage('<?php echo $img->filename ?>', 'video');">                             <img style="width:50px;height:45px;margin-right:3px;"  src='<?php echo site_url('uploads/item/videologo.jpg') ?>'  alt="<?php echo $item->item_img_alt_text;?>"></a>
                                    		<?php } else { ?>
                                    		<a class="zoomThumbActive" href='javascript:void(0);'  onclick="changeimage('<?php echo site_url('uploads/item/' . $img->filename) ?>', 'image');"> <img style="width:50px;height:45px;margin-right:3px;"  src='<?php echo (@site_url('uploads/item/thumbs/' . $img->filename) && file_exists("./uploads/item/thumbs/".$img->filename))?site_url('uploads/item/thumbs/' . $img->filename):site_url('uploads/item/thumbs/big.png'); ?>'  alt="<?php echo $item->item_img_alt_text;?>"></a>       <?php } ?>

                                    		</li>
                                    		<?php }?>
                                    	</ul>
                                    	</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="greatedeals">
                            <p><?php echo $item->listinfo; ?></p>
                        </div>
                        <br/>


                       <div>
                            <?php $newStr = '';
                            if($item->wiki){
                            	$patternsrc = '/src="([^"]*)"/';
                            	preg_match($patternsrc, $item->wiki, $matches);
                            	if(isset($matches[1]) && $matches[1]!=""){
                            		$src = $matches[1];
                            		unset($matches);
                            	}else
                            	$src = "";

                            	$patternalt = '/alt="([^"]*)"/';
                            	preg_match($patternalt, $item->wiki, $matchesalt);
                            	if(isset($matchesalt[1]) && $matchesalt[1]!=""){
                            		$alt = $matchesalt[1];
                            		unset($matchesalt);
                            	}
                            	else
                            	$alt = "";

                           $newWiki = $item->wiki;
                           $newStr = str_replace($src,'',$newWiki);
                       ?>
                       <p><?php echo $newStr.'<img id="contentimage" data-zoom-image3="'.$src.'" src="'.$src.'" alt="'.$alt.'" >'; ?></p>
                        <?php } else { ?>
                        <p><?php echo $item->wiki;?> </p>
                        <?php } ?>
                        </div>


                        <div class="tabbable"> <!-- Only required for left/right tabs -->
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab1" data-toggle="tab">Overview</a></li>
                                <li><a href="#tab2" data-toggle="tab">Description</a></li>
                                <li><a href="#tab3" data-toggle="tab">Details</a></li>
                                <li><a href="#tab4" data-toggle="tab">Files</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab1">
                                    <p>
                                        <strong> Item Name:</strong> <?php echo $item->itemname; ?>
                                        <br/>
                                        <strong>Unit:</strong> <?php echo $item->unit; ?><br/>
                                        <?php if(isset($item->increment)) {  ?>
                                        <strong>Increment:</strong> <?php if($item->increment==0) { echo "1";} else { echo $item->increment;} ?><?php } ?>
                                        
                                        <?php if(!empty($item->tags)){?><div><strong>Tags:</strong><ul class="tags"><?php
                                        $tags = explode(",",$item->tags);
                                        foreach ($tags as $tag){
										$tag = trim($tag);

										?>
                                         <li><a class="tag" href="<?php echo site_url("site/tag/".str_replace('%2F', '/', urlencode(urlencode($tag))));?>"><?php echo $tag;?></a></li>
                                        <?php } ?></ul></div><?php } ?>
                                    </p>
                                </div>
                                <div class="tab-pane" id="tab2">
                                    <p><?php echo nl2br($item->description); ?></p>
                                </div>
                                <div class="tab-pane" id="tab3">
                                    <p><?php echo nl2br($item->details); ?></p>
                                </div>
                                 <div class="tab-pane" id="tab4">
                                   <?php
		//echo '<pre>@@@',print_r($item);
                                   if(isset($item->files) && $item->files!="") 
                                           {
                                           	$files=explode(",",$item->files);
                                            $filecount=count($files); 
                                           // echo '@@'.$filecount;
                                            if(isset($item->filename) && $item->filename!="") 
                                                {
                                                	$filename=explode(",",$item->filename);
                                                    $filenamecount=count($filename); 	
                                                } 
                                                ?>
                                            
                                            <ul>     
                                            <?php /*if($filecount==$filenamecount) 
                                                 {*/
                                               
                                                 	for ($x=0; $x<$filecount; $x++)
                                                 	 {                                                 	 	 
                                                 	 	 if(file_exists("./uploads/item/".$files[$x]))
                                                 	 	  { ?>  
                                                 	
   <li class="active"><a href="<?php echo site_url('uploads/item/'.$files[$x]) ?>" target="_blank"><?php echo $files[$x];?></a></li>
                                                 	
                                                 	<?php } } //}?>
   
											   <?php 
													   if(isset($item->wiki) && $item->wiki !='')
													   {
															$email  = $item->wiki;
															$domain = strstr($email, 'src="');
															$newSrc = str_replace('src="',"'",$domain);
															$newSrc1 = str_replace('">',"'",$newSrc);															
													
														if($newSrc1 != '') {			?>
													<li><a  target="_blank" href=<?php echo $newSrc1; ?> >Wiki image </a> </li>
												<?php  } } ?>	
												<?php 
													   if(isset($item->listinfo) && $item->listinfo !='')
													   {
															$email  = $item->listinfo;
															$domain = strstr($email, 'src="');
															$newSrc = str_replace('src="',"'",$domain);
															$newSrc1 = str_replace('">',"'",$newSrc);															
														if($newSrc1 != '') {		?>
													<li><a  target="_blank" href=<?php echo $newSrc1; ?> >List info image </a> </li>
												<?php  } } ?>		
												
                                                 	</ul>
                                                 	<?php }  else { echo "No Files For This Item."; }  ?>
                                  	
                                </div>
                            </div>
                        </div>
                          <?php if(@$item->featureditem){ if($item->featuredsupplierdetails->saleitemdata==0){ ?>
                          <br/>
                          <div class="newbox">
                          <h3 class="titlebox1">Featured Seller</h3>
                          <table class="span12">
                              <tr>
                                  <td>Sold By:&nbsp;<a href="<?php echo site_url('site/supplier/'.$item->featuredsupplierdetails->username); ?>" target="_blank" style="text-decoration:none;">
                                  <span style="font-size:18px;font-weight:bolder;font-family: Arial, Helvetica, sans-serif;">
                                  <?php echo $item->featuredsupplierdetails->title;?></span></a>&nbsp;
                                  <?php 
                                  if($this->session->userdata('site_loggedin'))
                                  {
                                  	if($item->featureditem->price){ echo ' at "CALL"'; } else { echo " at $".$item->featureditem->ea; }
                                  }
                                  else 
                                  {
                                  	if($item->featuredsupplierdetails->availprice==1)
                                  	{
                                  		echo "at&nbsp;<span style='color:red;'>Members Only.</span>";
                                  	}
                                  	else {
                                  		
                                  		if($item->featureditem->price){ echo ' at "CALL"'; } else { echo " at $".$item->featureditem->ea; }
                                  	}
                                  	
                                  }
                                 
                                  ?></td>
                                 <td>
										 <?php 
										 if ($item->item_img && file_exists('./uploads/item/' . $item->item_img)) 
										 { 
										 	 $imgName = site_url('uploads/item/'.$item->item_img); 
										 } 
										 else 
										 { 
										 	 $imgName = site_url('uploads/item/big.png'); 
	                                     }?>
                                      <?php if($item->featureditem->price){?>
                                        	<img style="height:30px;widht:30px;" src="<?php echo site_url('templates/front/assets/img/icon/phone.png');?>" title="<?php if(isset($item->featuredsupplierdetails->phone)) echo $item->featuredsupplierdetails->phone; ?>" /><br/><p>Call for Price</p>
                                       <?php }else{
                                       	if($this->session->userdata('site_loggedin')) {
                                       	?>
                                  	
                                    	<a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart('<?php echo $imgName; ?>',<?php echo $item->id; ?>, <?php echo $item->featuredsupplier; ?>, <?php echo $item->featureditem->ea ? $item->featureditem->ea : 0; ?>,<?php echo (isset($item->featureditem->minqty))?$item->featureditem->minqty:'1';?>,'<?php echo $item->unit ? $item->unit : '';?>','<?php echo htmlspecialchars(addslashes($item->itemcode));?>', '<?php echo htmlspecialchars(addslashes($item->itemname));?>',<?php echo $item->increment; ?>,'1',<?php echo (isset($item->featureditem->deal))?$item->featureditem->deal:1; ?>)">
                                      	<i class="icon icon-shopping-cart"></i> Buy Now
                                      </a>
                                        <?php } elseif($item->featuredsupplierdetails->availprice!=1){ ?>
                                      
                                        	<a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart('<?php echo $imgName; ?>',<?php echo $item->id; ?>, <?php echo $item->featuredsupplier; ?>, <?php echo $item->featureditem->ea ? $item->featureditem->ea : 0; ?>,<?php echo (isset($item->featureditem->minqty))?$item->featureditem->minqty:'1';?>,'<?php echo $item->unit ? $item->unit : '';?>','<?php echo htmlspecialchars(addslashes($item->itemcode));?>', '<?php echo htmlspecialchars(addslashes($item->itemname));?>',<?php echo $item->increment; ?>,'1', <?php echo (isset($item->featureditem->deal))?$item->featureditem->deal:1; ?>)">
                                      	<i class="icon icon-shopping-cart"></i> Buy Now
                                      </a>
                                        
                                      <?php  }} ?>
                                  </td>
                              </tr>
                              <?php if(@$item->featureditem->manufacturername){?>
                              <tr>
                              	<td colspan="2">Manufacturer: <?php echo $item->featureditem->manufacturername;?></td>
                              </tr>
                              <?php }?>
                          </table>
                      </div>
                          <?php } } ?>

                            <?php
                            if ($filtermanufacturer) {
                            ?>

                            <div class="newbox" style="margin-top:12px;">
                              <h3 class="titlebox1">View By Manufacturer</h3>
                              <table width="90%">
                                <tr>
                                  <td><form style="margin:0px;" method="post" action="<?php echo site_url('site/item/'.$item->id);?>">
                                      <select name="select" id="manufacturer1" onchange="$('#manufacturer').val($('#manufacturer1').val());$('#searchform').submit()">
                                        <option value="">All</option>
                                        <?php foreach($filtermanufacturer as $fimf) if($fimf){?>
                                        <option value="<?php echo $fimf->id;?>" <?php echo $fimf->id==@$_POST['manufacturer']?'SELECTED':'';?>><?php echo $fimf->title;?></option>
                                        <?php }?>
                                      </select>
                                  </form></td>
                                </tr>
                              </table>
                            </div>


						    <?php }?>
                            <div class="pull-right" style="margin:10px 0px;">

                            <?php if ($this->session->userdata('site_loggedin')){?>
                        		<a class="btn btn-primary" href="javascript:void(0)" onclick="addtopo(<?php echo $item->id; ?>,<?php echo $item->increment; ?>)">
                                    <i class="icon icon-plus"></i> <br/>Add to RFQ
                                </a>
                            	<br/><br/>
                            <?php }else{?>
                            <a class="btn btn-primary" style="margin-left:30px;" href="javascript:void(0)" onclick="$('#createmodal').modal();">
                            <i class="icon icon-plus"></i> <br/>Add to RFQ
                            </a>
                            <?php } ?>
                                <a href="<?php echo site_url('company/register'); ?>">
                                    Have one to sell? Start selling now!
                                </a>
                            </div>
                            <br/><br/>
                            <div class="searchnewbox">
                            <h4 style="float:left">
                            	Search Radius(miles):
                            	<a href="javascript:void(0)" onclick="setmiles(20)">20</a>&nbsp;&nbsp;
                            	<a href="javascript:void(0)" onclick="setmiles(60)">60</a>&nbsp;&nbsp;
                            	<a href="javascript:void(0)" onclick="setmiles(120)">120</a>&nbsp;&nbsp;
                            	<a href="javascript:void(0)" onclick="setmiles(240)">240</a>&nbsp;&nbsp;
                            	<a href="javascript:void(0)" onclick="setmiles(2400)">View All</a>&nbsp;&nbsp;
                            </h4>
                            <?php if(!$inventory){?>
                            No nearby suppliers for the item. Try a larger search radius.
                            <?php }?>
                            </div>
                            <div class="newbox">
								
                            <?php if($inventory) { ?>
                            <div style="margin:0px; padding:0px; width:100%; overflow-x:scroll;">
                            <table id="datatable newtable" class="table table-bordered smallfont" style="table-layout:fixed;width:1500px;word-break: break-all;">
                            	<thead>
                                <tr>
                                 	<th width="150px">Buy</th>
                                    <th width="150px">Supplier</th>
                                    <th width="150px">Price</th>
                                    <th width="150px">Code</th>
                                    <th width="150px">Item</th>
                                    <th width="150px">Manuf.</th>
                                    <th width="150px">Part#</th>
                                    <th width="150px">Stock</th>
                                    <th width="150px">Address</th>
                                    <th width="150px">Dist(mi)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php                                 
                                    
                                    foreach ($inventory as $inv) { 
                                    if ($inv->ea) {  
                                    	if(@$inv->companydetails->saleitemdata==0){
                                    	
                                        $price = $inv->ea;
                                ?>
                                <tr>
                                
                                	<td style="padding:0px;word-break:break-all;" align="center">
                                	<?php if ($item->item_img && file_exists('./uploads/item/' . $item->item_img)) { ?>                                       
	                                    <?php $imgName = site_url('uploads/item/'.$item->item_img); ?>
	                                <?php } else { ?>	                                    
	                                        <?php $imgName = site_url('uploads/item/big.png'); 
	                                   }?>
                                	
                                        <?php if($inv->price){?>
                                        	<img style="height:30px;widht:30px;" src="<?php echo site_url('templates/front/assets/img/icon/phone.png');?>" title="<?php if(isset($item->featuredsupplierdetails->phone)) echo $item->featuredsupplierdetails->phone; ?>"/><br/>Call for Price
                                       <?php }
                                       else {	
                                       	if($this->session->userdata('site_loggedin')) {?>
                                    	<a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart('<?php echo $imgName; ?>',<?php echo $inv->itemid; ?>, <?php echo $inv->company; ?>, <?php echo $inv->ea; ?>, <?php echo $inv->minqty; ?>,'<?php echo $inv->unit ? $inv->unit : '';?>','<?php echo htmlspecialchars(addslashes($inv->itemcode));?>', '<?php echo htmlspecialchars(addslashes($inv->itemname));?>',<?php echo $inv->increment; ?>,'1',<?php echo (isset($inv->deal))?$inv->deal:1; ?>)">
                                            <i class="icon icon-plus"></i>
                                        </a>
                                        
                                        <br><?php if(count($inv->hasdiscount)!=0) echo "Volume Discount.";?>
                                        <?php } elseif(@$inv->companydetails->availprice!=1) { ?>
                                        	
                                        	<a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart('<?php echo $imgName; ?>',<?php echo $inv->itemid; ?>, <?php echo $inv->company; ?>, <?php echo $inv->ea; ?>, <?php echo $inv->minqty; ?>,'<?php echo $inv->unit ? $inv->unit : '';?>','<?php echo htmlspecialchars(addslashes($inv->itemcode));?>', '<?php echo htmlspecialchars(addslashes($inv->itemname));?>',<?php echo $inv->increment; ?>,'1',<?php echo (isset($inv->deal))?$inv->deal:1; ?>)">
                                            <i class="icon icon-plus"></i>
                                        </a>
                                        
                                        <br><?php if(count($inv->hasdiscount)!=0) echo "Volume Discount.";?>
                                        	
                                      <?php  } }?>
                                    </td>
                                    
                                    <td style="padding:0px;word-break:break-all;"><a href="<?php echo site_url('site/supplier/'.@$inv->companydetails->username);?>"><?php echo @$inv->companydetails->title . $inv->joinstatus; ?></a> 
                                    <br>
                                     <?php  if (@$inv->companydetails->company_type==1) 
				                    	  { ?>                                                
				                         	<img style="width:95px;" src="<?php echo site_url('uploads/logo/thumbs/premium_member.jpeg') ?>" alt="Premium Member">
				                    <?php } ?>
                                    
                                    </td>
                                    <td style="padding:0px;word-break:break-all;">
                                             	
                                  <?php   	
                                   
                                  if($this->session->userdata('site_loggedin'))
                                  {
                                  	if($inv->price) { echo '"CALL"';} else { echo '$'.$inv->ea;}
                                  	 if($inv->ea != $inv->listprice && $inv->listprice != ''){ ?>
                                  	 <br/><strike>$<?php echo $inv->listprice ?></strike><?php }
                                  }
                                  else 
                                  {
                                  	if(@$inv->companydetails->availprice==1)
                                  	{
                                  		echo "<span style='color:red;'>Members Only.</span>";
                                  	}
                                  	else {
                                  		
                                  		if($inv->price) { echo '"CALL"';} else { echo '$'.$inv->ea;} 
                                    			if($inv->ea != $inv->listprice && $inv->listprice != ''){ ?>
                                    		<br/><strike>$<?php echo $inv->listprice ?></strike><?php }
                                  	}
                                  	
                                  }
                                    	
                                  ?>  	                                    	
                                    	<br>Min.Order:<?php echo $inv->minqty;?>
                                    </td>
                                    <td style="padding:0px;word-break:break-all;" class="tinyfont"><?php echo $inv->itemcode ?> </td>
                                    <td style="padding:0px;"><?php echo $inv->itemname ?> </td>
                                    <td  style="padding:0px;word-break:break-all;"><?php echo $inv->manufacturername ?> </td>
                                    <td  style="padding:0px;word-break:break-all;"class="tinyfont"><?php echo $inv->partnum ?> </td>
                                    <td style="padding:0px; word-break: keep-all;">
                                    <?php echo $inv->instock ? 'Yes' : 'Call For Availability 555-555-5555'; ?>
                                    <?php echo $inv->qtyavailable?'<br>Stock:'.$inv->qtyavailable:'';?><br>
                                    <?php echo $inv->backorder ? 'Backorder' : ''; ?><br>
                                    <?php echo $inv->shipfrom ? 'Ships From Manufacturer.' : ''; ?>
                                    

                                    </td>
                                   
                                    <td style="padding:0px;word-break:keep-all;" class="tinyfont"><?php echo nl2br(@$inv->companydetails->address); ?> </td>
                                    <td><?php echo @$inv->dist ? number_format($inv->dist, 2) : ' '; ?></td>
                                    
                                </tr>
                                <?php } } }   ?>
                                </tbody>
                            </table>
                            </div>
                            <?php } ?>
</div>
                        <?php if($amazon){ ?>
                       <div class="newbox">
                         <table class="table table-bordered">
                            <tr>
                                <th>Amazon Item Name</th>
                                <th>Item Price</th>
                                <th>Link to Product</th>
                            </tr>
                            <tr>

                                <td><?php echo $amazon->amazon_name; ?></td>
                                <td>$<?php echo $amazon->amazon_price; ?></td>
                                <td><a target="_blank" href="<?php echo $amazon->amazon_url; ?>">Click here</a></td>
                            </tr>

                        </table> 
                        
<script type="text/javascript" language="javascript">
var amzn_assoc_ad_type = "contextual";
var amzn_assoc_tracking_id = "tactic03-20";
var amzn_assoc_marketplace = "amazon";
var amzn_assoc_region = "US";
var amzn_assoc_placement = "SEJ6ODWYHLLADOUU";
var amzn_assoc_linkid = "SEJ6ODWYHLLADOUU";
var amzn_assoc_emphasize_categories = "2619525011, 1055398, 228013";
var amzn_assoc_fallback_products = "";
var amzn_assoc_width = "728";
var amzn_assoc_height = "90";
</script>
<script type="text/javascript" language="javascript" src="//z-na.amazon-adsystem.com/widgets/q?ServiceVersion=20070822&Operation=GetScript&ID=OneJS&WS=1&MarketPlace=US&source=ac"></script>

                        
</div>
                        <?php } ?>
                        <?php if(count($inventory)!=0){?>                         
					    <script type="text/javascript">

					   $(function () {
						   var dataChart = new Array;
						   var suppliers =new Array();
						   var ser = new Array();
						   var total = 0;
						<?php
						$invcnt = 0;
						foreach ($inventory as $inv)
						if ($inv->ea && $inv->ea>0 &&  @$inv->companydetails->company_type==1 && !$inv->price)
						{
							if($this->session->userdata('site_loggedin'))
							{
							$invcnt++;
							$price = $inv->ea;
							?>
							ser.push({name:"<?php echo @$inv->companydetails->title; ?>",data:[parseFloat("<?php echo $price; ?>")]});
							total = total + <?php echo $price;?>;
							<?php
							}
							 elseif (@$inv->companydetails->availprice==0)
							  { 
							    	$invcnt++;
							$price = $inv->ea;
							?>
							ser.push({name:"<?php echo @$inv->companydetails->title; ?>",data:[parseFloat("<?php echo $price; ?>")]});
							total = total + <?php echo $price;?>;
							<?php
							  
							  }
						}
						?>
			          var totalSuppliers = <?php echo $invcnt; ?>;

					        $('#container-highchart').highcharts({
					            chart: {
					                type: 'column'
					            },
					            title: {
					                text: 'Current Market Prices'
					            },
					            subtitle: {
					                text: 'Fair Market Price = $ '+parseFloat(total/totalSuppliers).toFixed(2)
					            },
					            xAxis: {
					                categories: ["Suppliers"],
					                title: {
					                    text: null
					                }
					            },
					            yAxis: {
					                min: 0,
					                title: {
					                    text: 'Current Price $',
					                    align: 'high'
					                },
					                labels: {
					                    overflow: 'justify'
					                }
					            },
					            tooltip: {
					                valueSuffix: ' $'
					            },
					            plotOptions: {
					                bar: {
					                    dataLabels: {
					                        enabled: true
					                    }
					                }
					            },
					            plotOptions: {
				                       series: {
				                           dataLabels: {
				                        	   verticalAlign: "bottom",
				                               enabled: true,
				                               format: '$ {point.y:.2f}'
				                           }
				                       }
				                   },

					            credits: {
					                enabled: false
					            },
					            series: ser
					        });
					    });
					     </script>
					    <?php if($invcnt>0){?> 
					    <div id="container-highchart" class="span4" style="min-width: 200px ;height: 500px; margin: 0 auto; width:100%"></div>				
					    	<?php }  } ?>
					  
					   
					    					
					   
		        <h3 class="titlebox1">Request Assistance</h3>
                        <a name="form"></a>
                        <?php //echo $this->session->flashdata('message'); ?>

        				<form method="post" action="<?php echo site_url('site/sendrequest/'.$item->id);?>" class="request-form">
        					<input type="hidden" name="redirect" value="item/<?php echo $item->url?>"/>
        					  <div class="newbox"> <table cellpadding="4" cellspacing="4">
        						<tr>
        							<td width="200">Type:</td>
        							<td>
        								<select id="requresttype" name="type" onchange="setlabel()">
        									<option value="Request Phone Assistance">Request Phone Assistance</option>
        									<option value="Schedule Appointment">Schedule Appointment</option>
        								</select>
        							</td>
        						</tr>
        						<tr>
        							<td>Name</td>
        							<td><input type="text" name="name" required/></td>
        						</tr>
        						<tr>
        							<td>Email</td>
        							<td><input type="email" name="email" required/></td>
        						</tr>

        						<tr>
        							<td>Phone</td>
        							<td><input type="text" name="phone" required/></td>
        						</tr>

        						<tr>
        							<td id="daytd-label">Best day to call</td>
        							<td><input type="text" class="daytd" name="daytd"/></td>
        						</tr>

        						<tr>
        							<td id="timetd-label">Best time to call</td>
        							<td><input type="text" class="timetd" name="timetd" value="6:00am"/></td>
        						</tr>

        						<tr>
        							<td>Regarding</td>
        							<td><textarea name="regarding" rows="5" style="width: 350px;"></textarea>
        						</tr>
        						<tr>
        							<td></td>
        							<td><input type="submit" class="btn btn-primary" value="Send"/></td>
        						</tr>
        					</table> </div>
        				</form>
                    </div>
                    
                    <iframe id="iframes" src="<?php echo @$searchquery;?>" style="height: 1085px; margin-left: 15px; margin-bottom: -20px; display:none" frameborder="0"></iframe>
                    
                </div>


			    <div class="sidebar span3">




				    <div class="widget contact">
                        <div class="title">
                            <h2 class="block-title">Search Supplier</h2>
                        </div>
                        <div class="content_sup">
                            <form id="searchform" method="post">
                                <div class="control-group">
                                    <label class="control-label" for="address">
                                        To find availability near your job site or current location please enter the address:
                                    </label>
                                    <div class="controls">
                                        <input type="text" id="address" name="address" value="<?php echo @$_POST['address']; ?>">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="radirange">
                                        Show suppliers within radius of:
                                    </label>
                                    <div class="controls">
                                        <input style="width: 60px" type="text" id="radirange" name="miles" value="<?php echo @$_POST['miles']; ?>"> Miles
                                    </div>
                                </div>
                                <input type="hidden" id="manufacturer" name="manufacturer" value="<?php echo @$_POST['manufacturer'];?>"/>

                                <?php if ($this->session->userdata('site_loggedin')) { ?>
                                    <div class="control-group">
                                        <label class="control-label" for="radirange">
                                            Only Show suppliers in my Network:
                                        </label>
                                        <div class="controls">
                                            <input type="checkbox" value="1" id="innetwork" name="innetwork" <?php echo @$_POST['innetwork'] ? 'checked="checked"' : ''; ?>/>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="form-actions">
                                    <br/>
                                    <input type="submit" class="btn btn-primary arrow-right" value="Search">
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php if($item->articles){?>
                    <div class="widget contact">
                        	<label class="control-label" for="radirange">
                                    	<h2 class="block-title">Information and Resources</h2>
                                    </label>
                        <div class="content_sup">
                            <form>
                                <div class="control-group">

                                    <div class="controls">

                                    	<?php foreach($item->articles as $article){?>
                                    		<a href="<?php echo site_url('site/article/'.$article->url);?>"><?php echo $article->title?></a><br/>
                                    	<?php }?>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                    <?php }?>

                    <?php if(@$relateditems){?>
                    <div class="widget contact">
                    	<label class="control-label" for="radirange">
                                    	<h5 class="block-title">Related Items</h5>
                                    </label>
                        <div class="content_sup">
                            <form>
                                <div class="control-group">

                                    <div class="controls">
                                    	<table>
                                    	<?php foreach($relateditems as $ri){?>
                                    		<tr>
                                    			<td>
                                    				<?php if($ri->item_img && file_exists('./uploads/item/' .$ri->item_img)){?>
                                    				<img style="width:75px;height:50px" src="<?php echo site_url('uploads/item/'.$ri->item_img);?>"/>
                                    				<?php $imgName = site_url('uploads/item/'.$ri->item_img); }
                                    				 else {?>
                                    				<img style="width:75px;height:50px" src="<?php echo site_url('uploads/item/big.png');?>"/>
                                    				<?php $imgName = site_url('uploads/item/big.png');  }?>
                                    			</td>
                                    			<td>
                                    				<a href="<?php echo site_url('site/item/'.$ri->url);?>" target="_blank"><?php echo $ri->itemname?></a>
                                    			</td>
                                    		</tr>
                                    	<?php }?>
                                    	</table>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php }?>


                    <?php if(@$dealfeed){//NOT USED NOW?>
                    <div class="widget contact">
                    <label for="radirange" class="control-label">
                                <h5 class="block-title">Supplier Deals</h5>
                            </label>
                        <div class="content_sup">

                        	<table style="font-size: 12px;">
                        	<?php
                        	foreach($dealfeed as $di)
                        	{
								$diff = abs(strtotime(date('Y-m-d H:i')) - strtotime($di->dealdate));
								$years = floor($diff / (365*60*60*24)); $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

								$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
								$hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60));
								$minuts = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
								$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60));

                                $remaining = "$days days, $hours hrs, $minuts mins";
                        	?>
                        	<tr>
                        		<td style="text-align:center">
                        		<a href="<?php echo site_url("site/supplier/".$di->companyusername);?>"><?php echo $di->companyname?></a>
                        		</td></tr>

                        	<tr>
                        		<td style="text-align:center">
                        		<?php if($di->image  && file_exists('./uploads/item/'.$di->image)) {?>
                        			<img style="width: 81px;height:80px" src="<?php echo site_url('uploads/item/thumbs/'.$di->image);?>" width="81" height="80">
                        		<?php $imgName = site_url('uploads/item/thumbs/'.$di->image); } else {?>
                        		<img style="width: 81px;height:80px" width="81" height="80" src="<?php echo site_url('uploads/item/big.png');?>"/>
                        		<?php $imgName = site_url('uploads/item/big.png'); }?>
                        		</td></tr>

                                <tr>
                        		<td style="text-align:center">
                        		<a href="<?php echo site_url("site/item/".$di->url);?>"><?php echo $di->itemname?></a>

                        		</td>
                                </tr>


                        	<tr style="text-align:center">
                        		<td  style="text-align:center"><?php echo $remaining;?> remaining</td>
                        	</tr>
                        	<tr>
                        		<td style="text-align:center">Hurry up, only <span class="red"><?php echo $di->qtyavailable;?> items</span> remaining</td>
                        	</tr>
                            <tr>
                            <td style="background:#06A7EA; font-weight:bold; color:#fff; padding:2px 0px 2px 10px">	($<?php echo $di->dealprice;?> Min. Qty: <?php echo $di->qtyreqd;?>) 	<a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart('<?php echo $imgName;?>', <?php echo $di->itemid; ?>, <?php echo $di->company; ?>, <?php echo $di->dealprice ? $di->dealprice : 0; ?>, <?php echo $di->qtyreqd ? $di->qtyreqd : 0; ?>,'<?php echo $di->unit ? $di->unit : '';?>','<?php echo htmlspecialchars(addslashes($di->itemcode));?>', '<?php echo htmlspecialchars(addslashes($di->itemname));?>',<?php echo $di->increment; ?>,1,<?php echo (isset($di->deal))?$di->deal:1; ?>)">
                                    <i class="icon icon-plus"></i>
                                </a></td>
                            </tr>

                        	<?php }?>
                        	</table>

                        </div>
                    </div>
                    <?php }?>

                    <div class="widget contact">
                    	<label class="control-label" for="radirange">
                                    	<h5>Free Professional Assistance</h5>
                                    </label>
                        <div class="content_sup">
                            <form>
                                <div class="control-group">

                                    <div class="controls">
                                    	We are construction procurement professionals and are happy to help.
                                    </div>
                                    <a href="javascript:void(0)" id="requestLink">Request</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php  if($adforitem){?>
                    <div class="widget contact">
                       	<label class="control-label" for="radirange">
                                    	<h5>Classified Listings</h5>
                                    </label>
                        <div class="content_sup">
                            <form>
                                <div class="control-group">

                                    <div class="controls">
                                    	<?php foreach($adforitem as $ad){?>
                                    	<div>
                                    		<img alt="" src="<?php echo base_url("/uploads/ads/".$ad->image);?>">
                                    	</div>
                                    	<div style="margin-top: -30px;background-color: #616261;  opacity: 0.8; color:#FFF;">
                                    	<div>
                                    	<p><?php echo $ad->title;?> $<?php echo $ad->price;?></p>
                                    	</div>
                                    		<div style="text-align:right;">
                                    			<a href="<?php echo base_url("/site/ad/".$ad->id);?>" class="btn btn-primary">Details</a>
                                    		</div>
                                    	</div>
                                    	<?php } ?>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>




        </div>
    </div>
</div>
</div>


        <!-- Modal -->
        <div class="modal hide fade" id="addtoquotemodal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title nobottompadding" id="myModalLabel">Request for quote</h3>
                    </div>
                    <form id="addtoquoteform" action="<?php echo site_url('site/additemtoquote'); ?>" method="post" onsubmit="rfqformsubmit(); return false;">
                        <input type="hidden" id="additemid" name="itemid" value=""/>
                        <div class="modal-body">
                            <h4>Select Project</h4>
                            <select id="additemproject" onchange="getquotecombo();getcostcodecombo();">
                                <option value="">Select</option>
                                <?php foreach($projects as $up){?>
                                	<option value="<?php echo $up->id?>"><?php echo $up->title;?></option>
                                <?php }?>
                            </select>

                            <h4>Select PO</h4>
                            <span id="additempo">
                            <select name="quote" required>
                                <?php if(0)foreach($userquotes as $uq){?>
                                	<option value="<?php echo $uq->id?>"><?php echo $uq->ponum;?></option>
                                <?php }?>
                            </select>
                            </span>

                            <!-- <a href="javascript:void(0)" target="_blank" onclick="var pid=$('#additemproject').val();if(pid){$(this).attr('href','<?php echo site_url('admin/quote/add/');?>/'+pid);$('#additemproject').val('');$('#addtoquotemodal').modal('hide');}else{return false;}">Add PO</a> -->

                            <a href="javascript:void(0)" onclick="addpo()">Add PO</a>  
                            
                            <h4>Quantity</h4>
                            <input type="text" id="additemqty" name="quantity" onkeyup="this.value=this.value.replace(/[^0-9]/g,'');" required/>
                            <input type="hidden" id="incrementqty" name="incrementqty" />
                            <h4>Costcode</h4>
                            <span id="additemcostcode">
                            <select name="costcode" required>
                                <?php if(0)foreach($userquotes as $uq){?>
                                	<option value="<?php echo $uq->id?>"><?php echo $uq->ponum;?></option>
                                <?php }?>
                            </select>
                            </span>

                            <h4>Date Requested</h4>
                            <input type="text" id="daterequested" name="daterequested"/>

                            <br/><br/>
                            <div>
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="rfqformsubmit();">Add</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

         <!-- Request Modal Form -->
        <div class="modal hide fade" id="requestModalForm">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">

                           <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
                            <h3 class="modal-title nobottompadding" id="myModalRequestLabel">Request Assistance</h3>
                    </div>
                   <form method="post" action="<?php echo site_url('site/sendrequest/'.$item->id);?>" class="request-modal-form">
        					<input type="hidden" name="redirect" value="item/<?php echo $item->url?>"/>
        					  <div class="newbox"> <table cellpadding="4" cellspacing="4">
        						<tr>
        							<td width="200">Type:</td>
        							<td>
        								<select id="requresttype-modal-req" name="type" onchange="setlabelReqModal()">
        									<option value="Request Phone Assistance">Request Phone Assistance</option>
        									<option value="Schedule Appointment">Schedule Appointment</option>
        								</select>
        							</td>
        						</tr>
        						<tr>
        							<td>Name</td>
        							<td><input type="text" name="name" required/></td>
        						</tr>
        						<tr>
        							<td>Email</td>
        							<td><input type="email" name="email" required/></td>
        						</tr>

        						<tr>
        							<td>Phone</td>
        							<td><input type="text" name="phone" required/></td>
        						</tr>

        						<tr>
        							<td id="daytd-modal-label">Best day to call</td>
        							<td><input type="text" class="daytd" name="daytd"/></td>
        						</tr>

        						<tr>
        							<td id="timetd-modal-label">Best time to call</td>
        							<td><input type="text" class="timetd" name="timetd" value="6:00am"/></td>
        						</tr>

        						<tr>
        							<td>Regarding</td>
        							<td><textarea name="regarding" rows="5" style="width: 350px;"></textarea>
        						</tr>
        						<tr>
        							<td></td>
        							<td><input type="submit" class="btn btn-primary" value="Send"/></td>
        						</tr>
        					</table> </div>
        				</form>
                </div>
            </div>
        </div>
        <!-- End Modal Form -->

        <div id="cartprice" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;width:365px;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <i class="icon-credit-card icon-7x"></i>

          <h4 class="semi-bold" id="myModalLabel">
          <div id="itemnamebox"></div>
          <div id="itemimage"></div>
          <br> Select Quantity
          </h4>
          <br>
          <div id="unitbox"></div>
        </div>
        <div class="modal-body">

        <div id="qtypricebox"></div>
       
		
        <div>
            <div id="cartqtydiv" class="col-md-8">
            </div>
            <div class="col-md-4">
              <span id="qtylistprice"></span>
            </div>
          </div>

        <div id="cartsavediv"></div>
		 <div id="ftqtypricebox"></div>	
		 <span id="ftqtypriceunitbox"></span>	
        </div>
        <div class="modal-footer">
          <input type="hidden" name="hiddenprice" id="hiddenprice" />
          <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  
  
  		<div class="modal hide fade" id="addtoquotemodal1">
            <div class="modal-dialog">
                <div class="modal-content">

                    <form id="addtoquoteform" action="<?php echo site_url('site/additemtoquote'); ?>" method="post" return false;">
                        <input type="hidden" id="additemid" name="itemid" value=""/>
                        <div class="modal-body">
                        <div id="modalhtm">

                        </div>
                        </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        
        
      
        
      <div id="Addpomodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">

            <div class="modal-header">
        	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
            <h3>Please Add Your P.O. Now</h3>
        	</div>
        	<div class="modal-body">
        	
        	<div class="control-group">
			    <div class="controlss">PO # &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; 
                  <input type="text" id="ponum" name="ponum" style="width: 20%" class="input small" >		</div>
		    </div>
		    <br><br>		    
		    <div class="control-group">
			    <div class="controlss">
			      Delivery or Pick-Up Date: &nbsp; &nbsp;
			      <input type="text" id="deliverydate" name="deliverydate" class="input small span2" 
			      	data-date-format="mm/dd/yyyy">			      
			       &nbsp; &nbsp; <br><br>
			      PO Date: &nbsp; &nbsp; 
			      <input type="text" id="podate" name="podate" class="input small span2"
			      	data-date-format="mm/dd/yyyy">
			      	&nbsp; &nbsp; &nbsp; &nbsp; <br><br>
			     Bid Due Date: &nbsp; &nbsp; 
			      <input type="text" id="duedate" name="duedate" class="input small span2"
			      data-date-format="mm/dd/yyyy">
			      <input name="add" type="button" class="btn btn-primary" value="Save" onclick="savepo();"/>
			    </div>			   
		    </div>
        	
        	</div>

   </div>  
        
        