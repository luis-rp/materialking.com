<?php //var_dump($popups);die;?>
<script>
	function getlatlong()
	{
		var address = $("#inputLocation").val();
		
		if(address)
		{
    		var geocoder = new google.maps.Geocoder();
            geocoder.geocode({ 'address': address }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var latitude = results[0].geometry.location.lat();
                    var longitude = results[0].geometry.location.lng();
                    $("#latitude").val(latitude);
                    $("#longitude").val(longitude);
                    //alert("Latitude: " + latitude + "\nLongitude: " + longitude);
                } else {
                    alert("Request failed.")
                }
            });
		}
        return true;
	}
</script>
<script>
    $(document).ready(function() {
        InitEzmark();
        InitMap();
        InitChosen();
    });

    function InitEzmark() {
        $('input[type="checkbox"]').ezMark();
        $('input[type="radio"]').ezMark();
    }

    function InitChosen() {
        $('select').chosen({
            disable_search_threshold: 10
        });
    }

    function LoadMap() {
        var locations = new Array(
            <?php echo $latlongs; ?>
        );
        var markers = new Array();
        var mapOptions = {
            center: new google.maps.LatLng(<?php echo $mapcenter; ?>),
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: false
        };

        var map = new google.maps.Map(document.getElementById('map'), mapOptions);


        <?php foreach ($popups as $k => $popup) { ?>
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(<?php echo $k; ?>),
            map: map,
            icon: 'http://html.realia.byaviators.com/assets/img/marker-transparent.png'
        });

        var myOptions = {
            content: '<?php echo $popup; ?>',
            disableAutoPan: false,
            maxWidth: 0,
            pixelOffset: new google.maps.Size(-146, -190),
            zIndex: 100,
            closeBoxMargin: "",
            closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif",
            infoBoxClearance: new google.maps.Size(1, 1),
            boxStyle: { 
                background: "#fff"
                ,opacity: 1
               },
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
            closeBoxURL: "",
            pixelOffset: new google.maps.Size(-21, -58),
            position: new google.maps.LatLng(location[0], location[1]),
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
        <?php } ?>

    }

    function InitMap() {
        google.maps.event.addDomListener(window, 'load', LoadMap);
    }
</script>

<div id="content"><div class="map-wrapper">
        <div class="map">
            <div id="map" class="map-inner"></div>
            <div class="container">
                <div class="row">
                    <div class="span3">
                        <div class="property-filter pull-right">
                            <div class="content">
                                <form method="post" action="" onsubmit="return getlatlong()">
                                    <div class="location control-group">
                                        <label class="control-label" for="inputLocation">
                                            Location
                                        </label>
                                        <div class="controls">
                                        	<input type="hidden" id="latitude" name="lat"/>
                                        	<input type="hidden" id="longitude" name="lng"/>
                                            <input type="text" id="inputLocation" name="location" value="<?php echo ($this->input->post('location')) ? $this->input->post('location') : $my_location; ?>">

<!--                                                <select id="inputLocation" name="citystates">
                                            <?php foreach ($citystates as $cst) { ?>
                        <option value="<?php echo $cst->citystate; ?>" <?php
                                                if ($cst->citystate == @$_POST['citystates']) {
                                                    echo 'selected="selected"';
                                                }
                                                ?>><?php echo $cst->citystate; ?></option>
                                            <?php } ?>
    </select>-->

                                        </div>
                                    </div>

                                    <div class="type control-group">
                                        <label class="control-label" for="inputType">
                                            Industry
                                        </label>
                                        <div class="controls">
                                            <select id="typei" name="typei">
                                                <option value=''>All</option>
                                                <?php
                                                foreach ($types as $t)
                                                    if ($t->category == 'Industry') {
                                                        ?>
                                                        <option value='<?php echo $t->id; ?>' <?php
                                                        if ($t->id == @$_POST['typei']) {
                                                            echo 'selected="selected"';
                                                        }
                                                        ?>><?php echo $t->title; ?></option>
                                                            <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="type control-group">
                                        <label class="control-label" for="inputType">
                                            Manufacturer
                                        </label>
                                        <div class="controls">
                                            <select id="typem" name="typem">
                                                <option value=''>All</option>
                                                <?php
                                                foreach ($types as $t)
                                                    if ($t->category == 'Manufacturer') {
                                                        ?>
                                                        <option value='<?php echo $t->id; ?>' <?php
                                                        if ($t->id == @$_POST['typem']) {
                                                            echo 'selected="selected"';
                                                        }
                                                        ?>><?php echo $t->title; ?></option>
                                                            <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-actions">
                                        <input type="submit" value="Filter Now!" class="btn btn-primary btn-large">
                                    </div>
                                    <?php if ($norecords) { ?>
                                        <div class="form-actions">
                                            <div class="notfound"><?php echo $norecords; ?></div>
                                        </div>
                                    <?php } ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<div class="bottom-wrapper">
        <div class="bottom container">
            <div class="bottom-inner row">
                <div class="item span4">
                    <div class="address decoration"></div>
                    <h2><a>Find Suppliers & Material</a></h2>
                    <p>Find new suppliers, build relationships and do better business.</p>
                </div>

                <div class="item span4">
                    <div class="key decoration"></div>
                    <h2><a>Suite of Powerful Purchasing Tools</a></h2>
                    <p>Automated tools to help you buy low, track pricing trends, get material on time, track backorders, manage your spending, 
                        assign material to jobs and workers, have accurate billings, schedule materials, catch errors and more.</p>
                </div>

                <div class="item span4">
                    <div class="gps decoration"></div>
                    <h2><a>Easy Procurement &amp; Sourcing</a></h2>
                    <p>EZPZP is a Fast and Easy cloud based software that allows you to save time and be more successful.</p>
                </div>

            </div>
        </div>
    </div>
            
        </div>
    </div>

    <div class="container">
        <div id="main">
            <?php /*?><div class="row">
                <div class="span9">
                    <h3 class="titlebox" style="padding:0px 0px 0px 8px"><b>Featured Suppliers</b></h3>
                    <div class="properties-grid">
                        <div class="row">
                            <?php foreach ($featured as $fc) { ?>
                                <div class="property span3">
                                    <div class="image">
                                        <div class="content" style="text-align: center; padding-top:3px;">
                                            <?php if ($fc->logo) { ?>
                                                <img style="width:175px; height:160px;" src="<?php echo site_url('uploads/logo/thumbs/' . $fc->logo) ?>" alt="<?php echo $fc->logo; ?>">
                                            <?php } else { ?>
                                                <img style="width:175px; height:160px;"src="<?php echo base_url(); ?>templates/site/assets/img/default/big.png" alt="big.png">
                                            <?php } ?>
                                        </div>
                                        <div class="price"></div>
                                        <div class="reduced"><?php echo $fc->title; ?> </div>
                                    </div>

                                    <div class="title">
                                        <h2><a href="<?php echo site_url('site/supplier/' . $fc->username); ?>"><?php echo $fc->contact; ?></a></h2>
                                    </div>

                                    <div class="location"><?php echo $fc->address; ?><?php //echo $fc->state; ?> </div>
                                    <div class="area">

                                    </div>
                                    <?php echo $fc->joinstatus; ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                </div>
                <div class="sidebar span3">
                    <div class="widget our-agents">
                    </div>
                    <div class="hidden-tablet">
                        <div class="widget properties last">
                            <div class="title">
                                <h2>Latest Suppliers</h2>
                            </div>
                            <div class="content">
                                <?php foreach ($recentcompanies as $rc) { ?>
                                    <div class="property">
                                        <div class="image">
                                            <?php if ($rc->logo) { ?>
                                                <img src="<?php echo site_url('uploads/logo/thumbs/' . $rc->logo) ?>" alt="<?php echo $rc->logo; ?>" style="height: 81px">
                                            <?php } else { ?>
                                                <img src="<?php echo base_url(); ?>templates/site/assets/img/default/big.png" alt="big.png">
                                            <?php } ?>
                                        </div>

                                        <div class="wrapper">
                                            <div class="title">
                                                <h3>
                                                    <a href="<?php echo site_url('site/supplier/' . $rc->username); ?>"><?php echo isset($rc->title) ? $rc->title : "no_title"; ?></a>
                                                </h3>
                                            </div>

                                            <?php if (isset($rc->city) && isset($rc->state)) { ?>
                                                <div class="location2"><?php echo $rc->city.",&nbsp;".$rc->state; ?></div>
                                            <?php } else {  ?>
                                                <div class="location"><?php echo $rc->contact; ?></div>
                                            <?php } ?>
                                            <?php echo $rc->joinstatus; ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <br/><br/>
                            </div>
                        </div>
                       
                    </div>
                </div>
            </div><?php */?>
          
		  
		    <!-- ----------------------------------------------------------------------------->
		 
		    <?php 
		
			$ssql         = "SELECT * FROM  `cms_content` WHERE `id` = '1'";
			$current_res  = mysql_query($ssql);	
			$current_row  = mysql_fetch_assoc($current_res);
			
			
			$curent_id    = $current_row['id']; 
			$title        = $current_row['title']; 
			$content      = $current_row['content']; 
			$curent_image = $current_row['image']; 
			$curent_video = $current_row['video']; 
			
			
						
			$SITE_ROOT = $_SERVER['DOCUMENT_ROOT'].'/';
			
			$SITE_URL  = site_url();
			
			$PRODUCT_IMAGE_ROOT =  $SITE_ROOT.'uploads/content-manager/';
			
			$PRODUCT_IMAGE_URL = $SITE_URL.'uploads/content-manager/';
			
	     ?>	 
		 		 
		 
		 	<div class="row video_container">
			  
			<?php if(trim($curent_video) != '' || (trim($curent_image) != '' && file_exists($PRODUCT_IMAGE_ROOT.$curent_image))){ ?>
			
			  <div class="v_img_box">
			  
				  <?php 
				  
				  if(trim($curent_video) != '')
				  {
					 echo stripslashes($curent_video);
				  }
				  else if(trim($curent_image) != '' && file_exists($PRODUCT_IMAGE_ROOT.$curent_image))
				  {
						?>
						<img src="<?php echo  $PRODUCT_IMAGE_URL.$current_row['image']; ?>" height="235" width="234" border="0" />
						<?php
				  }
				  else
				  {
					?>
						<img src="<?php echo  $PRODUCT_IMAGE_URL; ?>no_image_available_big.png" height="235" width="234" border="0" />
					<?php
				  }			  
				  ?>
			  
			  </div>
			  
			  
			   <div class="v_text_box">
			 		<div class="v_title"><?php echo $title; ?></div>
			  		<div class="v_cnt"><?php echo $content; ?></div>
			  </div>
			  
			  
			 <?php }else{ ?> 
			 
			 <div class="v_text_box_full">
					<div class="v_title"><?php echo $title; ?></div>
			  		<div class="v_cnt"><?php echo $content; ?></div>
			 </div>
			  
			 <?php } ?>
			  			 			  
			</div>
		 		 
		 	<style>
			
				.v_img_box img
				{
				width:234px;
				height:235px;
				}
			
			
				.v_cnt
				{
					font-size:14px;
					text-align:justify;				
				}	
			
				.v_title
				{
					color: #06A7EA;
					font-size: 18px;
					font-weight: normal;
					margin: 0;
					padding: 0;
					vertical-align: top;
					width:100%;
					margin-bottom:10px;					
				}
			
				.video_container {
    background-color: #FFFFFF;
    float: left;
    height: auto; 
    margin-bottom: 20px;
    margin-left: 0;
    margin-top:33px;
    padding: 7px;
    width: 100%;
}
			
				.v_img_box {
					float: left;
					height: 237px;
					margin-right: 6px;
					width: 234px;
				}
			
				.v_text_box {
					float: right;
					height: auto;
					width: 442px;
					overflow:auto;
				}
					
					
				.v_text_box_full
				{
					float: left;
					height: auto;
					width: 100%;
					overflow:auto;
				}			
			
			</style>
		 
		 <!-- ----------------------------------------------------------------------------->
	
		
		  
		  
		  
		    <br/>
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- homepage ad widesky -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:970px;height:90px"
                 data-ad-client="ca-pub-6061943556362932"
                 data-ad-slot="1694611401"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
            <?php
            if($suppliers_10_miles){
                ?>
            <div class="row">
                <div class="span12">
                    <h1 class="page-header">Suppliers List</h1>
                    <div class="properties-rows">
                        <div class="row">

                            <?php
                            foreach ($suppliers_10_miles as $supplier) {
                                
                                ?>
                                <div class="property span9">
                                    <div class="row">
                                        <div class="image span3">
                                            <div class="content">
                                                <?php if ($supplier->logo) { ?>
                                                    <img style="padding-top: 5px; width:175px; height:160px" src="<?php echo site_url('uploads/logo/thumbs/' . $supplier->logo) ?>" alt="">
                                                <?php } else { ?>
                                                    <img style="padding-top: 5px; width:175px; height:160px" src="<?php echo base_url(); ?>templates/site/assets/img/default/big.png" alt="">
                                                <?php } ?>

                                            </div>
                                        </div>

                                        <div class="body_home span6">
                                            <div class="title-price row">
                                                <div class="title span4">
                                                    <h2><a href="<?php echo site_url('site/supplier/' . $supplier->username); ?>"><?php echo $supplier->title; ?></a></h2>
                                                </div>
                                                <div class="price">
                                                    <?php echo $supplier->address; ?>
                                                </div>
                                            </div>

                                            <div class="location2"><?php echo $supplier->contact; ?></div>
<?php /*?><div class="btn btn-primary arrow-right"><a href="<?php echo site_url('site/supplier/' . $supplier->username); ?>">View Profile</a></div><br/><br/>
 <div class="btn btn-primary arrow-right"><a href="<?php echo site_url('store/items/' . $supplier->username); ?>">Go to Store</a></div><br/><br/>
 <?php if(!empty($supplier->joinstatus)){?><div class="btn btn-primary arrow-right"><a href="javascript:void(0);"><?php echo $supplier->joinstatus;?></a></div><?php }?><br><br><?php */?>
                                            <?php //echo $supplier->shortdetail; ?>
                                            <br><br>
                                            <div class="area">
                                                <?php //echo $supplier->joinstatus; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>
                            <br><br>
                        </div>
                    </div>

                </div>
                
            </div>
            <?php } ?>

            <div class="features">
            </div>
        </div>
    </div>

    
</div>