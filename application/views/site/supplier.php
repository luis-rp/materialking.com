<?php echo '<script>var loggedin = ' . ($this->session->userdata('site_logintype') == 'users' ? 'true' : 'false') . ';</script>' ?>
<?php echo '<script>var loginurl = "' . site_url('network/login/users') . '";</script>' ?>
<?php echo '<script>var joinurl = "' . site_url('network/join') . '";</script>' ?>
<?php echo '<script>var addtocarturl="' . site_url('cart/addtocart') . '";</script>' ?>
<?php echo '<script>var itemsurl="' . site_url('site/items') . '";</script>' ?>
<?php echo '<script>var getpriceqtydetails="' . site_url('site/getpriceqtydetails') . '";</script>' ?>
<?php echo '<script>var getpriceperqtydetails="' . site_url('site/getpriceperqtydetails') . '";</script>' ?>
<?php echo '<script>var getnewprice="' . site_url('site/getnewprice') . '";</script>' ?>

<?php
/*
  $geocode=file_get_contents("http://maps.google.com/maps/api/geocode/json?address=".urlencode(str_replace("\n",", ",$supplier->address))."&sensor=false");
  $output= json_decode($geocode);
  print_r($output);
  $lat = $output->results[0]->geometry->location->lat;
  $long = $output->results[0]->geometry->location->lng;
 */
$lat = $supplier->com_lat;
$long = $supplier->com_lng;
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/site/assets/css/windy.css" />		
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/site/assets/css/style1.css" />

<script type="text/javascript" src="https://api.github.com/repos/twbs/bootstrap?callback=callback"></script>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery.timepicker.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">

<link href="<?php echo base_url(); ?>templates/admin/css/jquery.timepicker.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">

<script>
    $(document).ready(function() {
        InitMap();
        $("#day").datepicker();
        $("#time").timepicker({
            'minTime': '6:00am',
            'maxTime': '11:30pm',
            'showDuration': false
        });
    });

    function industryitems(id)
    {
    	$("#typei").val(id);
    	$("#supplierform").submit();
    }

    function setlabel()
    {
        $type = $("#requresttype").val();
        if($type=='Request Phone Assistance')
        {
            $("#daytd").html('Best Day To Call');
            $("#timetd").html('Best Time To Call');
        }
        else
        {
            $("#daytd").html('Appointment Date Requested');
            $("#timetd").html('Appointment Time Requested');
        }
            
    }

    function InitMap() {
        google.maps.event.addDomListener(window, 'load', LoadMapProperty);
    }

    function LoadMapProperty() {
        var locations = new Array(
                [<?php echo $lat; ?>,<?php echo $long; ?>]
                );
        var markers = new Array();
        var mapOptions = {
            center: new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $long; ?>),
            zoom: 14,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: false
        };

        var map = new google.maps.Map(document.getElementById('property-map'), mapOptions);

        $.each(locations, function(index, location) {
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(location[0], location[1]),
                map: map,
                icon: 'http://html.realia.byaviators.com/assets/img/marker-transparent.png'
            });

            var myOptions = {
                content: '<div class="infobox"><div class="image"><img src="<?php if($supplier->logo) { echo base_url(); ?>uploads/logo/thumbs/<?php echo $supplier->logo; } else { echo base_url(); ?>templates/site/assets/img/default/big.png <?php } ?>" alt="" width="100"></div><div class="title"><a href=""><?php echo $supplier->title; ?></a></div><div class="area"><div class="price">&nbsp;</div><span class="key"><?php echo $supplier->contact; ?><br/><?php echo $supplier->city.",&nbsp;".$supplier->state; ?></span><span class="value"></span></div><div class="link"><a href="<?php echo site_url('store/items/' . $supplier->username); ?>">Go to Store</a></div></div>',
                disableAutoPan: false,
                maxWidth: 0,
                pixelOffset: new google.maps.Size(-146, -190),
                zIndex: null,
                closeBoxMargin: "",
                closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif",
                boxStyle: { 
                    background: "#fff"
                    ,opacity: 1
                   },
                infoBoxClearance: new google.maps.Size(1, 1),
                position: new google.maps.LatLng(location[0], location[1]),
                isHidden: false,
                pane: "floatPane",
                enableEventPropagation: false
            };
            marker.infobox = new InfoBox(myOptions);
            marker.infobox.isOpen = false;

            var myOptions = {
                draggable: true,
                content: '<div class="marker"><div class="marker-inner"></div></div>',
                disableAutoPan: true,
                pixelOffset: new google.maps.Size(-21, -58),
                position: new google.maps.LatLng(location[0], location[1]),
                closeBoxURL: "",
                isHidden: false,
                // pane: "mapPane",
                enableEventPropagation: true
            };
            marker.marker = new InfoBox(myOptions);
            marker.marker.open(map, marker);
            markers.push(marker);

            google.maps.event.addListener(marker, "click", function(e) {
                var curMarker = this;

                $.each(markers, function(index, marker) {
                    // if marker is not the clicked marker, close the marker
                    if (marker !== curMarker) {
                        marker.infobox.close();
                        marker.infobox.isOpen = false;
                    }
                });

                if (curMarker.infobox.isOpen === false) {
                    curMarker.infobox.open(map, this);
                    curMarker.infobox.isOpen = true;
                    map.panTo(curMarker.getPosition());
                } else {
                    curMarker.infobox.close();
                    curMarker.infobox.isOpen = false;
                }

            });
        });
    }
</script>
<script type="text/javascript">

   
    function PrintElem(elem)
    {
        PopupPrint($(elem).html());
    }
    function PopupPrint(data) 
    {
        var mywindow = window.open('', 'my div', 'height=500,width=500,left=100,top=100');
        mywindow.document.write('<html><head><title>my div</title>');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.print();
        mywindow.close();
        return true;
    }
</script>
<link rel="stylesheet" href="<?php echo base_url(); ?>templates/admin/css/jRating.jquery.css" type="text/css" />
<script type="text/javascript" src="<?php echo base_url(); ?>templates/admin/js/jRating.jquery.js"></script>

<script>
$(document).ready(function() {
	$(".subscriberDialog").hide();
	$(".subscriber").click(function(){
		
		$(".subscriberDialog").dialog();

	});	
	
	$('.fixedrating').jRating({
		length:5,
		bigStarsPath : '<?php echo site_url('templates/admin/css/icons/stars.png');?>',
		nbRates : 0,
		isDisabled:true,
		sendRequest: false,
		canRateAgain : false,
		decimalLength:1,
		 onClick : function(element,rate) {
	         
	        },
		onError : function(){
			alert('Error : please retry');
		}
	});
});
</script>

<script>
    function addtocart(itemid, companyid, price, minqty, isdeal)
    {
    	if(typeof(minqty)==='undefined') minqty = 0;
    	if(typeof(isdeal)==='undefined') isdeal = 0;
        //var qty = prompt("Please enter the quantity you want to buy",minqty?minqty:"1");
        
       	$("#hiddenprice").val(price);
        $("#cartprice").modal();
        var selected = "";
        var strselect = ('Qty');
        strselect += '&nbsp;<select style="width:50px;" id="qtycart" onchange="showmodifiedprice('+itemid+','+companyid+','+price+');">';
        for (i = 1; i <=100; i++) { 
        	if(i == minqty) 
        	selected = 'selected';
        	else
        	selected = "";
           	strselect += '<option value="'+i+'"'+selected+'>'+i+'</option>';
   			} 
   		strselect += '</select>&nbsp;&nbsp; <input type="button" class="btn btn-primary" value="Add to cart" onclick="addtocart2('+itemid+','+companyid+','+price+','+minqty+','+isdeal+')" id="addtocart" name="addtocart"/>';
        $('#cartqtydiv').html(strselect);
        
        var data = "itemid="+itemid+"&companyid="+companyid;

        $.ajax({
        	type:"post",
        	data: data,
        	url: getpriceqtydetails
        }).done(function(data){
        	if(data){

        		$("#qtypricebox").html("");
        		$("#qtypricebox").html(data);
        	}
        });
        
        var data2 = "itemid="+itemid+"&companyid="+companyid+"&qty="+minqty+"&price="+price;
        
        $.ajax({
        	type:"post",
        	data: data2,
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
    
    function showmodifiedprice(itemid, companyid, price){
    	
    	qty = ($('#qtycart').val());
    	var data2 = "itemid="+itemid+"&companyid="+companyid+"&qty="+qty+"&price="+price;
        
        $.ajax({
        	type:"post",
        	data: data2,
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
    
    function addtocart2(itemid, companyid, price, minqty, isdeal){
    	
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
        var data = "itemid=" + itemid + "&company=" + companyid + "&price=" + $("#hiddenprice").val() + "&qty=" + qty + "&isdeal=" + isdeal;
        //alert(data); return false;
        $.ajax({
            type: "post",
            data: data,
            url: addtocarturl
        }).done(function(data) {
            alert(data);
            window.location = window.location;
        });
    	
    }
</script>

<form id="supplierform" method="post" action="<?php echo site_url('site/suppliers')?>">
	<input type="hidden" id="typei" name="typei"/>
</form>

<div id="content">
    <div class="container">
        <div id="main">
            <div class="row">
                <div class="span9">
                 <h3 class="titlebox">
                	<table width="100%">
                    	<tr>
                        	<td align="left"> <h3 class="page-header titlebox" style="padding:0px 0px 0px 7px; margin:0px; color:#FFFFFF; margin-right:5px;"><?php echo $supplier->title;?></h3></td>
                        	<td align="right">
                            	<!-- AddThis Button BEGIN -->
                            	<div class="addthis_toolbox addthis_default_style " style="float:right; width:auto">
                            	<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
                            	<a class="addthis_button_tweet"></a>
                            	<a class="addthis_counter addthis_pill_style"></a>                            	</div>
                            	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-536087a3159911fb"></script>                        	</td>
                    	</tr>
                    </table>
                  
                 </h3>
                    <div class="carousel property">
                    </div>

                    <div class="property-detail">
                        <div class="pull-left overview">
                            <div class="row">
                                <div class="span4" id="mydiv">
                                    <p>
                                        <?php if($supplier->logo !=""){?>
                                                <img width="60" src="<?php echo site_url('uploads/logo/'.$supplier->logo);?>"/>
                                                <?php } else {?>
                                                <img width="60" height="45" src="<?php echo base_url(); ?>templates/site/assets/img/logo.png"/><span style="margin-left: 20px;font-size: 16px;font-weight: bold;line-height: 30px;"><?php echo $supplier->title;?></span>       
                                                <?php } ?>  
                                                </p>
                                    <br/>
                                    <table width="100%" style="font-size: 11px;">
                                        <tr>
                                            <td>Join Date:</td>
                                            <td><?php echo date('m/d/Y',strtotime($supplier->regdate)); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Location:</td>
                                            <td><?php echo nl2br($supplier->address); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Email:</td>
                                            <td><?php echo $supplier->primaryemail; ?></td>
                                        </tr>
                                        <?php if($supplier->phone){?>
                                        <tr>
                                            <td>Tel:</td>
                                            <td><?php echo $supplier->phone; ?></td>
                                        </tr>
                                        <?php }?>
                                        <?php if($supplier->fax){?>
                                        <tr>
                                            <td>Fax:</td>
                                            <td><?php echo $supplier->fax; ?></td>
                                        </tr>
                                        <?php }?>
                                        <tr>
                                            <td width="27%">Contact Person:</td>
                                            <td><?php echo $supplier->contact; ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <br/>
                              <div class="invoice-button-action-set">
                                  <p style="float: left;line-height: 17px;">
                                      <button type="button" class="btn btn-primary" onclick="PrintElem(mydiv)" style="border-radius: 2px;padding: 0 10px;">
                                        Print
                                      </button>
                                </p>
                                 <!-- <div style="float: left;margin-left: 20px;" class="addthis_toolbox addthis_default_style ">
                                     <a class="addthis_counter addthis_pill_style" addthis:url="<?php // echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];?>#mydiv" addthis:title=""></a>
                        </div>-->
                                <!--<a class="shareEmail" href="" title="Share by Email"><img src="http://png-2.findicons.com/files/icons/573/must_have/48/mail.png"/></a>-->
                              </div>

                        </div>
                        <div class="row expe" style="margin-left: 3px;">
                            <p><?php echo $supplier->about; ?></p>
                        </div>
                        <div class="content">
                        
                         <?php if(isset($types[0]->category) && $types[0]->category!="") { ?><p>&nbsp;</p>  
                         
                         <h3 class="titlebox" style="padding:0px 0px 0px 8px">Manufacturers Carried:</h3> <?php } ?>
                        	
                            <ul style="float:left; display:inline-block;list-style-type: none; margin:0px; padding:0px;;text-align:center">
            			    <?php 
            			        foreach ($types as $type)
                                    if ($type->category == 'Manufacturer') {
                            ?>
                            	<li style="display:inline;padding-right:5px;text-align:center">
                                    <a style="text-decoration:none;" href="<?php echo site_url('store/items/'.$supplier->username.'/'.$type->id);?>">
                                    <?php if($type->image){?>
                                    <img src="<?php echo site_url('uploads/type/thumbs/'. $type->image);?>" alt="<?php echo $type->title; ?>"/>
                                    <?php }else{?>
                                    <?php echo $type->title; ?>
                                    <?php }?>
                                    </a>
                                </li>
                            <?php } ?>
                            </ul>
                        </div>
						
						<?php if($feedbacks){?>
                        <div>
                            <p>&nbsp;</p><br/>
                              <h3 class="titlebox" style="padding:0px 0px 0px 8px">
                           Feedback:</h3>
                            <table class="table">
                            	<?php foreach($feedbacks as $feedback){?>
                            	<tr>
                            		<td width="200"><?php echo $feedback->companyname;?></td>
                            		<td>
                                		<div class="fixedrating" data-average="<?php echo $feedback->rating;?>" data-id="1"></div>
                                		(<?php echo number_format($feedback->rating,2);?> / 5.00)
                            		</td>
                            		<td><?php echo $feedback->feedback;?></td>
                            	</tr>
                            	<?php }?>
                            </table>
                        </div>
                        <?php }?>
                        
                        <?php if(@$dealfeed){?>
                        	<br/>
                            <h3 class="titlebox2" style="padding:0px 0px 0px 8px">Supplier Deals</h3>
                            
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
                            <div style="font-size:12px; border:1px solid #CCC; margin-left:0; padding-left:0px;margin-bottom:5px;padding-right:0px;" class="property featuredspan">
                                <div class="image span2">
                                    <div class="content">
                                        <a href="<?php echo site_url("site/item/".$di->url);?>">
                                        <?php if($di->image) {?>
                        			<img style="width: 81px;height:80px" src="<?php echo site_url('uploads/item/thumbs/'.$di->image);?>" width="81" height="80">
                        			<?php } else {?>
                        		<img style="width: 81px;height:80px" width="81" height="80" src="<?php echo site_url('uploads/item/big.png');?>"/>
                        		<?php }?>
                        			<br/>
                        			<?php echo $di->itemname?>
                        			</a>
                        			<?php if($di->filename){?>
                        			<br/>
                        			<a href="<?php echo site_url("uploads/item/".$di->filename);?>" target="_blank">
                        			View Details
                        			</a>
                        			<?php }?>
                                  </div>
                                </div>

                                <div class="body1 span6">
                                    <div class="title-price row">
                                        <div class="title1 span5">
                                            <h2><a href="<?php echo site_url('site/item/'.$di->url);?>"><?php echo $di->itemcode;?></a>  </h2>
                                            <p><?php echo $di->dealnote;?></p>
                                            <div class="area">
                                                <span class="key"><strong>Quantity Available:</strong></span>
                                                <span class="value"><?php echo $di->qtyavailable;?></span>

                                                <span class="key"><strong>Time Remaning:</strong></span>
                                                <span class="value"><?php echo $remaining;?></span>
                                                
                                                <br>
                                                <span class="key"><strong>Minimum Qty:</strong></span>
                                                <span class="value"><?php echo $di->qtyreqd;?></span>

                                                <span class="key"><strong>Part #:</strong></span>
                                                <span class="value">1234567</span>
                                          </div>
                                              
                                        </div>
                                        
                                       <div class="price">
                                          
                                    
                                    <?php if($di->price){?>
                                        	<img title="<?php if(isset($di->phone)) echo $di->phone; ?>" style="height:30px;widht:30px;" src="<?php echo site_url('templates/front/assets/img/icon/phone.png');?>" /><br/>Call for Price
                                       <?php }else{?>
                                    	 $<?php echo $di->dealprice;?>
                                            <br><br>
                                            <a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart(<?php echo $di->itemid; ?>, <?php echo $di->company; ?>, <?php echo $di->dealprice ? $di->dealprice : 0; ?>, <?php echo $di->qtyreqd ? $di->qtyreqd : 0; ?>,1)">
                                    <i class="icon icon-plus"></i> Buy
                                        <?php } ?>
                                    
                                </a>
                                      </div>
                                    </div>
                                </div>
                        </div>
                        <?php } ?>
                        
                        	
                        <?php }?>
                        
                        
                        <?php if ($this->session->userdata('pms_site_cart')) { ?>
                        <div class="pull-right">
                            <a href="<?php echo site_url('cart'); ?>">
                                <img src="<?php echo site_url('templates/site/assets/img/shopping_cart.png');?>"/>
                            </a>
                        </div>
                        <?php } ?>
                        
                        <?php if($inventory){?>
                        	<br/>
                        	  <h3 class="titlebox2" style="padding:0px 0px 0px 8px">Featured Items:</h3>
                        	<?php
                                    foreach ($inventory as $inv)
                                    if ($inv->ea) 
                                    {
                                       $price = $inv->ea;
                                       $inv->qtyreqd = 0;
                                ?>
                                
                        	<div class="property featuredspan" style="border:1px solid #CCC; margin:0; padding-left:0px;margin-bottom:5px;padding-right:0px;">
                                <div class="image span2">
                                    <div class="content">
                                    <?php if($inv->image){?>
                                     <img alt="<?php echo urlencode($inv->itemname); ?>" src="<?php echo site_url('uploads/item/thumbs/'. $inv->image);?>" style="max-height: 120px; padding: 20px;width:120px; height:120px" width="120" height="120">
                                     <?php } else {?>
                                     <img src="<?php echo site_url('uploads/item/big.png');?>" style="max-height: 120px; padding: 20px;width:120px; height:120px;" width="120" height="120">
                                     <?php }?>
                                    </div>
                                </div>

                                <div class="body1 span6">
                                    <div class="title-price row">
                                        <div class="title1 span5">
                                            <h2><a href="<?php echo site_url('site/item/'.$inv->url);?>"><?php echo $inv->itemcode; ?></a></h2>
                                            <p><?php echo $inv->companynotes; ?></p>
                                           
                                            <div class="area">
                                                <span class="key"><strong>Item Name:</strong></span>
                                                <span class="value"><?php echo $inv->itemname ?></span>

                                                <span class="key"><strong>Unit:</strong></span>
                                                <span class="value">EA</span>
                                                
                                                <br>
                                                <span class="key"><strong>Manufacturer:</strong></span>
                                                <span class="value"><?php echo $inv->manufacturername; ?></span>

                                                <span class="key"><strong>Part #:</strong></span>
                                                <span class="value"><?php echo $inv->partnum ?></span>
                                                
                                                 <span class="key"><strong>Stock:</strong></span>
                                                <span class="value"><?php echo $inv->qtyavailable;?></span>
                                                
                                                <br/>
                                                <span class="key"><strong>Availablibility:</strong></span>
                                                <span class="value"><?php echo $inv->instock?'Available':'Not Available';?></span>
                                                
                                                <span class="key"><strong>Min Order Qty:</strong></span>
                                                <span class="value"><?php echo $inv->minqty ?></span>
                                                
                                          </div>
                                              
                                        </div>
                                        
                                        <div class="price">
                                            
                                        <?php if($inv->price){?>
                                        	<img title="<?php if(isset($inv->phone)) echo $inv->phone; ?>" style="height:30px;widht:30px;" src="<?php echo site_url('templates/front/assets/img/icon/phone.png');?>" /><br/>Call for Price
                                       <?php }else{?>
                                    	 <?php echo '$'.$inv->ea;?>
                                            <br><br>
                                            <a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart(<?php echo $inv->itemid; ?>, <?php echo $inv->company; ?>, <?php echo $price ? $price : 0; ?>)">
                                            <i class="icon icon-plus"></i> Buy
                                        </a>
                                        <?php } ?>
                                        
                                        </div>
                                    </div>
                                </div>
                        </div>
                         <?php } ?>
                        <?php }?>
						<br/>
                        
                       <h3 class="titlebox" style="padding:0px 0px 0px 8px">Map
                        <a name="map" id="map"></a>
                        <?php $addressarray = explode(" ",$supplier->address);
                        		$i=1;
                        		$addresslink = "";
                        		foreach($addressarray as $add){
                        			if(count($addressarray)>$i)
                        				$addresslink .= $add."+";
                        			else 
                        				$addresslink .= $add;
                        			$i++;	
                        		}
                        		 
                        ?>
                        <span style=" color: #fff;float: right;padding: 0 10px 0 8px;"><a target="_blank" style="color:#fff" href="<?php echo 'https://maps.google.com/maps?daddr='.$addresslink; ?>">Driving Directions</a></span></h3>
						
						
						
                        <div id="property-map"></div>
                        
                        <a name="form"></a>
                          <h3 class="titlebox" style="padding:0px 0px 0px 8px">Request Assistance</h3>
                        <?php echo $this->session->flashdata('message'); ?>
        				<form method="post" action="<?php echo site_url('site/sendrequest/'.$supplier->id);?>">
        					<input type="hidden" name="redirect" value="supplier/<?php echo $supplier->username?>"/>
                            <div class="newbox">
        					<table>
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
        							<td><input type="text" name="phone"  required/></td>
        						</tr>
        						<tr>
        							<td id="daytd">Best day to call</td>
        							<td><input type="text" id="day" name="day"/></td>
        						</tr>
        						<tr>
        							<td id="timetd">Best time to call</td>
        							<td><input type="text" id="time" name="time"/></td>
        						</tr>
        						
        						<tr>
        							<td>Regarding</td>
        							<td><textarea name="regarding" rows="5" style="width: 350px;"></textarea>
        						</tr>
        						<tr>
        							<td></td>
        							<td><input type="submit" class="btn btn-primary" value="Send"/></td>
        						</tr>
        					</table>
                          </div>
        				</form>
                    </div>
              </div>

                <div class="sidebar span3">
                    <div class="widget contact">
                        <div class="title">
                            <h2 class="block-title">Main Menu</h2>
                        </div>

                        <div class="content">
                        	<table width="100%" cellpadding="4">
                        		<tr>
                        			<td><b>Mailing List:</b> </td>
                        			<td><a href="#" class="subscriber">Join</a></td>
                        		</tr>
                        		<tr>
                        			<td><b>Connection:</b> </td>
                        			<td><?php echo $supplier->joinstatus?$supplier->joinstatus:'Guest';?></td>
                        		</tr>
                        		<tr>
                        			<td colspan="2"><b>About Us:</b><br/><?php echo $supplier->shortdetail;?></td>
                        		</tr>
                        		<tr>
                        			<td><a href="#form">Contact</a></td>
                        			<td></td>
                        		</tr>
                        		<tr>
                        			<td><a href="#map">Location</a></td>
                        			<td></td>
                        		</tr>
                        		<?php if(0){?>
                        		<tr>
                        			<td>Manufacturer Carried: </td>
                        			<td>
                        			    <?php foreach ($types as $type)
                                            if ($type->category == 'Manufacturer') {
                                                ?>
                                                <a href="<?php echo site_url('store/items/'.$supplier->username.'/'.$type->id);?>">
                                                <?php echo $type->title; ?>
                                                </a>
                                                <br/> 
                                        <?php } ?>
                        			
                        			</td>
                        		</tr>
                        		<?php }?>
                        		<tr>
                        			<td><b>Industry:</b></td>
                        		</tr>
                        		<tr>
                        			<td colspan="2">
                        			<ul class="inlist">
                        			    <?php foreach ($types as $type)
                                            if ($type->category == 'Industry') {
                                                ?>
                                                <li>
                                                <a href="javascript:void(0)" onclick="industryitems('<?php echo $type->id;?>')">
                                                <?php echo $type->title; ?>
                                                </a>
                                                </li>
                                        <?php } ?>
                                      </ul>
                        			</td>
                        		</tr>
                        		<?php if($rating){?>
                        		<tr>
                        			<td>Reviews: </td>
                        			<td><?php echo $rating;?> <?php echo number_format($ratingvalue,2);?> / 5.00</td>
                        		</tr>
                        		<?php }?>
                        		<tr>
                        			<td colspan="2">
                            			<a href="<?php echo site_url('store/items/'.$supplier->username);?>">
                            			Go to Store
                            			<img src="<?php echo site_url('templates/site/assets/img/shopping_cart.png');?>"/>
                            			</a>
                        			</td>
                        		</tr>
                        	</table>

                        </div>
                    </div>
                </div>
                
                <?php if(@$dealfeed){?>
                 <div class="sidebar span3">
                    <div class="widget contact">
                        <div class="title">
                            <h2 class="block-title">Supplier Deals</h2>
                        </div>
                        <div class="content">
                        	<table>
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
                        		<td>
                        		<?php if($di->image){?>
                        			<img src="<?php echo site_url('uploads/item/thumbs/'.$di->image);?>" width="80" height="80">
                        			<?php } else {?>
                        			<img style="width: 81px;height:80px" width="81" height="80" src="<?php echo site_url('uploads/item/big.png');?>"/>
                        			<?php }?>
                        		</td>
                        		<td>
                        		<a href="<?php echo site_url("site/item/".$di->url);?>"><?php echo $di->itemname?></a>
                        		($<?php echo $di->dealprice;?> Min. Qty: <?php echo $di->qtyreqd;?>)
                        		</td>
                        		<td>
                        		<a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart(<?php echo $di->itemid; ?>, <?php echo $di->company; ?>, <?php echo $di->dealprice ? $di->dealprice : 0; ?>, <?php echo $di->qtyreqd ? $di->qtyreqd : 0; ?>,1)">
                                    <i class="icon icon-plus"></i>
                                </a>
                                </td>
                        	</tr>
                        	<tr>
                        		<td colspan="3"><?php echo $remaining;?> remaining</td>
                        	</tr>
                        	<tr>
                        		<td colspan="3">Hurry up, only <span class="red"><?php echo $di->qtyavailable;?> items</span> Remaining</td>
                        	</tr>
                        	<?php }?>
                        	</table>
                        </div>
                    </div>
                 </div>
                 <?php }?>

                 <?php if(@$similarsuppliers){?>
                 <div class="sidebar span3">
                    <div class="widget contact">
                    <div class="title">
                            <h2 class="block-title">Similar Suppliers</h2>
                      </div>
                        <div class="content">
                                <div class="control-group">
                                	
                                    <div class="controls">
                                    	<table cellpadding="5">
                                    	<?php foreach($similarsuppliers as $ri){?>
                                    		<tr>
                                    			<td>
                                    				<?php if($ri->logo){?>
                                    				<img width="40" src="<?php echo site_url('uploads/logo/'.$ri->logo);?>"/>
                                    				<?php } else {?>
                                    				<img width="45" height="45" src="<?php echo base_url(); ?>templates/site/assets/img/logo.png"/>
                                    				<?php } ?>
                                    			</td>
                                    			<td>
                                    				<a href="<?php echo site_url('site/supplier/'.$ri->username);?>" target="_blank"><?php echo $ri->title?></a>
                                    			</td>
                                    		</tr>
                                    	<?php }?>
                                    	</table>
                                    </div>
                                </div>
                           
                        </div>
                    </div>
                 </div>
                 <?php }?>
                
                <?php if($adforsupplier){?>
                   <script type="text/javascript" src="<?php echo base_url();?>templates/site/assets/js/modernizr.custom.79639.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>templates/site/assets/js/jquery.windy.js"></script>
        <script type="text/javascript">	
			$(function() {

				var $el = $( '#wi-el' ),
					windy = $el.windy(),
					allownavnext = false,
					allownavprev = false;

				$( '#nav-prev' ).on( 'mousedown', function( event ) {

					allownavprev = true;
					navprev();
				
				} ).on( 'mouseup mouseleave', function( event ) {

					allownavprev = false;
				
				} );

				$( '#nav-next' ).on( 'mousedown', function( event ) {

					allownavnext = true;
					navnext();
				
				} ).on( 'mouseup mouseleave', function( event ) {

					allownavnext = false;
				
				} );

				function navnext() {
					if( allownavnext ) {
						windy.next();
						setTimeout( function() {	
							navnext();
						}, 150 );
					}
				}
				
				function navprev() {
					if( allownavprev ) {
						windy.prev();
						setTimeout( function() {	
							navprev();
						}, 150 );
					}
				}

			});
		</script>
                    <div class="sidebar span3">
                    <div class="widget contact">
                    <div class="title">
                            <h2 class="block-title">Suppliers Classified Lisings</h2>
                      </div>
                        <div class="content">
                           
                                <div class="control-group">
                               
                                   <div class="controls windy-demo">
                                   		<ul id="wi-el" class="wi-container">
                                    	<?php foreach($adforsupplier as $key=>$ad){?>
                                    	<li><img width="190" height="116" src="<?php echo base_url("/uploads/ads/".$ad->image);?>" alt="image<?php echo $key;?>"/><h4><?php echo $ad->title;?> $<?php echo $ad->price;?></h4><p><a href="<?php echo base_url("/classified/ad/".$ad->id);?>" class="btn btn-primary">Details</a></p></li>
                                     	<?php } ?>
                                    	</ul>
                                    	<nav>
											<span id="nav-prev">prev</span>
											<span id="nav-next">next</span>
										</nav>
                                  </div>
                                </div>
                            
                        </div>
                    </div>
                 </div>
                <?php }?>
            </div>
        </div>
    </div>
</div>
<div class="subscriberDialog">
	<form action="<?php echo base_url();?>subscriber/addsubscriber" method="post">
		<h3>Subscribe to our Mailing List</h3>
		<p>Enter your email below to subscribe to our mailing list</p>
		<label for="name">Name:</label><input type="text" name="name" id="name">
		<label for="mail">Mail:</label><input type="text" name="mail" id="mail">
		<input type="hidden" value="<?php echo $supplier->id; ?>" name="cid">
		<input type="submit" value="Subscribe!">
	</form>
</div>

<div id="cartprice" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;width:365px;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <i class="icon-credit-card icon-7x"></i>
          
          <h4 class="semi-bold" id="myModalLabel">
          Select Quantity  
          </h4>
          <br>
        </div>
        <div class="modal-body">

        <div id="qtypricebox"></div>  
          
        <div >
            <div id="cartqtydiv" class="col-md-8">             
            </div>
            <div class="col-md-4">
              <span id="qtylistprice"></span>
            </div>
          </div>  
        
        <div id="cartsavediv"></div>   
          
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
