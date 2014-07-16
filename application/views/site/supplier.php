<?php echo '<script>var loggedin = ' . ($this->session->userdata('site_logintype') == 'users' ? 'true' : 'false') . ';</script>' ?>
<?php echo '<script>var loginurl = "' . site_url('network/login/users') . '";</script>' ?>
<?php echo '<script>var joinurl = "' . site_url('network/join') . '";</script>' ?>
<?php echo '<script>var addtocarturl="' . site_url('cart/addtocart') . '";</script>' ?>
<?php echo '<script>var itemsurl="' . site_url('site/items') . '";</script>' ?>

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
                content: '<div class="infobox"><div class="image"><img src="<?php if($supplier->logo) { echo base_url(); ?>uploads/logo/thumbs/<?php echo $supplier->logo; } else echo base_url(); ?>templates/site/assets/img/default/big.png" alt="" width="100"></div><div class="title"><a href=""><?php echo $supplier->title; ?></a></div><div class="area"><div class="price">&nbsp;</div><span class="key"><?php echo $supplier->contact; ?><br/><?php echo $supplier->city; ?> <?php echo $supplier->state; ?></span><span class="value"></span></div><div class="link"><a href="">View more</a></div></div>',
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
        var mywindow = window.open('', 'my div', 'height=100,width=100,left=100,top=100');
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
        var qty = prompt("Please enter the quantity you want to buy",minqty?minqty:"1");
        if(isNaN(parseInt(qty)))
        {
            return false;
        }
        if(qty < minqty)
        {
            alert('Minimum quantity to order is '+ minqty);
            return false;
        }
        var data = "itemid=" + itemid + "&company=" + companyid + "&price=" + price + "&qty=" + qty + "&isdeal=" + isdeal;
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
                	<table width="100%">
                    	<tr>
                        	<td align="left"> <h2 class="page-header"><?php echo $supplier->title;?></h2></td>
                        	<td align="right">
                            	<!-- AddThis Button BEGIN -->
                            	<div class="addthis_toolbox addthis_default_style ">
                            	<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
                            	<a class="addthis_button_tweet"></a>
                            	<a class="addthis_counter addthis_pill_style"></a>
                            	</div>
                            	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-536087a3159911fb"></script>
                        	</td>
                    	</tr>
                    </table>
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
                                                <img width="60" height="45" src="<?php echo base_url(); ?>templates/site/assets/img/logo.png"/>
                                        <?php } ?>
                                        <span style="margin-left: 20px;font-size: 16px;font-weight: bold;line-height: 30px;"><?php echo $supplier->title;?></span>
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
                        <div class="row" style="margin-left: 3px;">
                            <p><?php echo $supplier->about; ?></p>
                        </div>
                        <div class="content">
                        
                         <?php if(isset($types[0]->category) && $types[0]->category!="") { ?><p>&nbsp;</p>  <h2>Manufacturers Carried:</h2> <?php } ?>
                        	
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
                            
                            <h2>Feedback:</h2>
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
                            <h2 class="block-title">Supplier Deals</h2>
                            
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
                            <div style="font-size:12px; border:1px solid #CCC; margin-left:-10px; padding-left:0px;margin-bottom:5px;padding-right:0px;" class="property featuredspan">
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
                                            <h2>nh414s</h2>
                                            <p><?php echo $di->dealnote;?></p>
                                            <div class="area">
                                                <span class="key"><strong>Quantity Available:</strong></span>
                                                <span class="value"><?php echo $di->qtyavailable;?></span>

                                                <span class="key"><strong>Quantity Remaning:</strong></span>
                                                <span class="value"><?php echo $remaining;?></span>
                                                
                                                <br>
                                                <span class="key"><strong>Minimum Qty:</strong></span>
                                                <span class="value"><?php echo $di->qtyreqd;?></span>

                                                <span class="key"><strong>Part #:</strong></span>
                                                <span class="value">1234567</span>
                                                </div>
                                              
                                        </div>
                                        
                                        <div class="price">
                                           $<?php echo $di->dealprice;?>
                                            <br><br>
                                            <a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart(<?php echo $di->itemid; ?>, <?php echo $di->company; ?>, <?php echo $di->dealprice ? $di->dealprice : 0; ?>, <?php echo $di->qtyreqd ? $di->qtyreqd : 0; ?>,1)">
                                    <i class="icon icon-plus"></i> Buy
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
                        	<h2>Featured Items:</h2>
                        	<?php
                                    foreach ($inventory as $inv)
                                    if ($inv->ea) 
                                    {
                                       $price = $inv->ea;
                                       $inv->qtyreqd = 0;
                                ?>
                                
                        	<div class="property featuredspan" style="border:1px solid #CCC; margin-left:-10px; padding-left:0px;margin-bottom:5px;padding-right:0px;">
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
                                            <?php echo '$'.$inv->ea;?>
                                            <br><br>
                                            <a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart(<?php echo $inv->itemid; ?>, <?php echo $inv->company; ?>, <?php echo $price ? $price : 0; ?>)">
                                            <i class="icon icon-plus"></i> Buy
                                        </a>
                                        </div>
                                    </div>
                                </div>
                        </div>
                         <?php } ?>
                        <?php }?>
						<br/>
                        <a name="map"></a>
                        <p>&nbsp;</p>
                        <h2>Map</h2>
                        <div id="property-map"></div>
                        
                        <a name="form"></a>
                        <h2>Request Assistance</h2>
                        <?php echo $this->session->flashdata('message'); ?>
        				<form method="post" action="<?php echo site_url('site/sendrequest/'.$supplier->id);?>">
        					<input type="hidden" name="redirect" value="supplier/<?php echo $supplier->username?>"/>
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
                            			Go to store
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
                        <div class="content">
                            <form>
                                <div class="control-group">
                                	<label class="control-label" for="radirange">
                                    	<h5>Similar Suppliers</h5>
                                    </label>
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
                            </form>
                        </div>
                    </div>
                 </div>
                 <?php }?>
                
                
            </div>
        </div>
    </div>
</div>