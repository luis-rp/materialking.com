<style>
.views-exposed-widget{ float:left;}
</style>
<section id="big-map">

		<div id="flatads-main-map"></div>

		<script type="text/javascript">
		var mapDiv,
			map,
			infobox;
		jQuery(document).ready(function($) {

			mapDiv = $("#flatads-main-map");
			mapDiv.height(500).gmap3({
				map: {
					options: {
						"draggable": true
						,"mapTypeControl": true
						,"mapTypeId": google.maps.MapTypeId.ROADMAP
						,"scrollwheel": false
						,"panControl": true
						,"rotateControl": false
						,"scaleControl": true
						,"streetViewControl": true
						,"zoomControl": true
						
					}
				}
				,marker: {
					values: [


							<?php	foreach($ads as $ad) { 
										foreach($ad as $ad_item){ if($ad_item['latitude']!="" && $ad_item['longitude']!="") {
							?>
							 	{


									latLng: [<?php echo $ad_item['latitude']; ?>,<?php echo $ad_item['longitude']; ?>],
									options: {
										icon: "<?php echo base_url("templates/classified/assets/images/") ?>icon.png",
										shadow: "<?php echo base_url("templates/classified/assets/images/") ?>shadow.png",
									},
									data: '<div class="marker-holder"><div class="marker-content"><div class="marker-image"><img src="<?php echo base_url("uploads/ads/".$ad_item['image']); ?>" /></div><div class="marker-info-holder"><div class="marker-info"><div class="marker-info-title"><?php echo addslashes($ad_item['title']); ?></div><div class="marker-info-extra"><div class="marker-info-price"><?php echo $ad_item['price']; ?></div><div class="marker-info-link"><a href="<?php echo base_url("classified/ad/".$ad_item['id']); ?>">Details</a></div></div></div></div><div class="arrow-down"></div><div class="close"></div></div></div>'
								}
							,

					<?php } } } ?>	
						
					],
					options:{
						draggable: false
					},
					cluster:{
		          		radius: 20,
						// This style will be used for clusters with more than 0 markers
						0: {
							content: "<div class='cluster cluster-1'>CLUSTER_COUNT</div>",
							width: 62,
							height: 62
						},
						// This style will be used for clusters with more than 20 markers
						20: {
							content: "<div class='cluster cluster-2'>CLUSTER_COUNT</div>",
							width: 82,
							height: 82
						},
						// This style will be used for clusters with more than 50 markers
						50: {
							content: "<div class='cluster cluster-3'>CLUSTER_COUNT</div>",
							width: 102,
							height: 102
						},
						events: {
							click: function(cluster) {
								map.panTo(cluster.main.getPosition());
								map.setZoom(map.getZoom() + 2);
							}
						}
		          	},
					events: {
						click: function(marker, event, context){
							map.panTo(marker.getPosition());

							var ibOptions = {
							    pixelOffset: new google.maps.Size(-125, -88),
							    alignBottom: true
							};

							infobox.setOptions(ibOptions)

							infobox.setContent(context.data);
							infobox.open(map,marker);

							// if map is small
							var iWidth = 260;
							var iHeight = 300;
							if((mapDiv.width() / 2) < iWidth ){
								var offsetX = iWidth - (mapDiv.width() / 2);
								map.panBy(offsetX,0);
							}
							if((mapDiv.height() / 2) < iHeight ){
								var offsetY = -(iHeight - (mapDiv.height() / 2));
								map.panBy(0,offsetY);
							}

						}
					}
				}
				 		 	},"autofit");

			map = mapDiv.gmap3("get");
		    infobox = new InfoBox({
		    	pixelOffset: new google.maps.Size(-50, -65),
		    	closeBoxURL: '',
		    	enableEventPropagation: true
		    });
		    mapDiv.delegate('.infoBox .close','click',function () {
		    	infobox.close();
		    });

		    if (Modernizr.touch){
		    	map.setOptions({ draggable : false });
		        var draggableClass = 'inactive';
		        var draggableTitle = "Activate map";
		        var draggableButton = $('<div class="draggable-toggle-button '+draggableClass+'">'+draggableTitle+'</div>').appendTo(mapDiv);
		        draggableButton.click(function () {
		        	if($(this).hasClass('active')){
		        		$(this).removeClass('active').addClass('inactive').text("Activate map");
		        		map.setOptions({ draggable : false });
		        	} else {
		        		$(this).removeClass('inactive').addClass('active').text("Deactivate map");
		        		map.setOptions({ draggable : true });
		        	}
		        });
		    }

		jQuery( "#advance-search-slider" ).slider({
		      	range: "min",
		      	value: 500,
		      	min: 1,
		      	max: 10,
		      	slide: function( event, ui ) {
		       		jQuery( "#geo-radius" ).val( ui.value );
		       		jQuery( "#geo-radius-search" ).val( ui.value );

		       		jQuery( ".geo-location-switch" ).removeClass("off");
		      	 	jQuery( ".geo-location-switch" ).addClass("on");
		      	 	jQuery( "#geo-location" ).val("on");

		       		mapDiv.gmap3({
						getgeoloc:{
							callback : function(latLng){
								if (latLng){
									jQuery('#geo-search-lat').val(latLng.lat());
									jQuery('#geo-search-lng').val(latLng.lng());
								}
							}
						}
					});

		      	}
		    });
		    jQuery( "#geo-radius" ).val( jQuery( "#advance-search-slider" ).slider( "value" ) );
		    jQuery( "#geo-radius-search" ).val( jQuery( "#advance-search-slider" ).slider( "value" ) );

		    jQuery('.geo-location-button .fa').click(function()
			{
				
				if(jQuery('.geo-location-switch').hasClass('off'))
			    {
			        jQuery( ".geo-location-switch" ).removeClass("off");
				    jQuery( ".geo-location-switch" ).addClass("on");
				    jQuery( "#geo-location" ).val("on");

				    mapDiv.gmap3({
						getgeoloc:{
							callback : function(latLng){
								if (latLng){
									jQuery('#geo-search-lat').val(latLng.lat());
									jQuery('#geo-search-lng').val(latLng.lng());
								}
							}
						}
					});

			    } else {
			    	jQuery( ".geo-location-switch" ).removeClass("on");
				    jQuery( ".geo-location-switch" ).addClass("off");
				    jQuery( "#geo-location" ).val("off");
			    }
		           
		    });

		});
		</script>



		<div id="advanced-search-widget">

			<div class="container">

				<div class="advanced-search-widget-content" style="float:left; width:100%;">

					<!--<div class="advanced-search-title">

						Search around my position

					</div>-->
                    <form accept-charset="UTF-8" id="views-exposed-form-search-view-other-ads-page" method="get" action="http://alexgurghis.com/themes/flatads">
                    
                    <div class="views-exposed-widget views-widget-filter-search_api_views_fulltext" id="edit-search-api-views-fulltext-wrapper">
					        <div class="views-widget">
					          	<div class="control-group form-type-textfield form-item-search-api-views-fulltext form-item">
									<div class="controls"> 
										<input type="text" class="form-text" maxlength="128" size="30" value="" name="s" id="edit-search-api-views-fulltext" placeholder="Enter keyword...">
									</div>
								</div>
						    </div>
						</div>
                        
                        <div class="views-exposed-widget views-widget-filter-field_ad_location" id="edit-ad-location-wrapper">
						   	<div class="views-widget">
						        <div class="control-group form-type-select form-item-ad-location form-item">
									<div class="controls"> 
										
                                        <select id="category" name="category">
                            	<?php foreach($categories as $cat){?>
                            	<option value="<?php echo $cat->id;?>" ><?php echo $cat->catname;?></option>
                            	<?php }?>
                            </select>
									</div>
								</div>
						    </div>
						</div>


                        <div class="views-exposed-widget views-widget-filter-field_category" id="edit-field-category-wrapper">
                                                    <div class="views-widget">
                                                        <div class="control-group form-type-select form-item-field-category form-item">
                                                            <div class="controls"> 
                                                                <select id="items" name="items">
                            	<?php foreach($items as $item){?>
                            	<option value="<?php echo $item->id;?>" ><?php echo $item->itemcode;?></option>
                            	<?php }?>
                            </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                
                                                
					<div class="advanced-search-slider">

						<div class="geo-location-button">

							<div class="geo-location-switch off"><i class="fa fa-location-arrow"></i></div>

						</div>

						<div id="advance-search-slider" class="value-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" aria-disabled="false">
							<a class="ui-slider-handle ui-state-default ui-corner-all" href="#">
								<span class="range-pin">
									<input type="text" name="geo-radius" id="geo-radius" value="500" data-default-value="1000">
								</span>
							</a>
						</div>

					</div>
                    </form>

				</div>

			</div>

		</div>

	

	</section>
	





  <section id="featured-list" style="opacity: 1;">
        
        <div class="container">
            
            <h3>Check out our Premium Featured Ads</h3>
            
            <div id="tabs" class="full">
			    	
                			    	
                <ul class="tabs quicktabs-tabs quicktabs-style-nostyle"> 
			    	<li class="grid-feat-ad-style"><a class="current" href="#">Grid View</a></li>
			    	<li class="list-feat-ad-style"><a class="" href="#">List View</a></li>
                </ul>

                <div class="pane" style="display: block;">
                 
                    <div id="carousel-buttons">
			    	    <a href="#" id="carousel-prev" class="hidden" style="display: none;">← Previous </a>
			    	    <a href="#" id="carousel-next" class="hidden" style="display: none;"> Next →</a>
			        </div>

					<div class="caroufredsel_wrapper" style="display: block; text-align: start; float: none; top: auto; right: auto; bottom: auto; left: auto; z-index: auto; margin: 0px; overflow: hidden; position: relative; width: 0px; height: 0px;"><div id="projects-carousel" style="text-align: left; float: none; position: absolute; top: 0px; right: auto; bottom: auto; left: 0px; margin: 0px; width: 0px; height: 0px;">

			    		
						
						
			    			
												
						
			    	</div></div>

			    											
				<!--  	<script>

						jQuery(document).ready(function () {

							jQuery('#projects-carousel').carouFredSel({
								auto: false,
								prev: '#carousel-prev',
								next: '#carousel-next',
								pagination: "#carousel-pagination",
								mousewheel: true,
								swipe: {
									onMouse: true,
									onTouch: true
								} 
							});

						});
											
					</script> -->
					<!-- end scripts -->

			    </div>

			    <div class="pane" style="display: none;">		

			    	
					
					
			    													
					
			    </div>

			</div>
        
        </div>

    </section>

    <section id="categories-homepage">
        
        <div class="container">

	                    
            <h3>Browse our  1 Ads from 1 Categories</h3>

            <div class="full">

            	
            <!-- Categories -->
           <?php 

		    		$current = -1;
							      
					
							foreach ($ads as $key=>$value) {
								
							

				 ?>

            	<div class="category-box span3 <?php if($current%4 == 0) { echo 'first'; } ?>">

            		<div class="category-header">

            		

		    			<span class="cat-title"><a href="#"><h4><?php echo $key; ?></h4></a></span>

		    			<span class="category-total"><h4></h4></span>

            		</div>

            		<div class="category-content">

            			<ul>   

		    				<?php

		    					$currentCat = 0;

								foreach($value as $ad) {
									$currentCat++;
							?>

								<li>
								  	<a href="<?php echo base_url("classified/ad/".$ad['id']);?>" >
										<?php echo $ad['title'];?>
									</a>
								  	<span class="category-counter"></span>
								</li>

							<?php } ?> 

							<?php if($currentCat > 5) { ?>
								<!-- 
		    					<li>
		    						<a href="#">View all subcategories &rarr;</a>
		    					</li>-->

		    				<?php } ?>

		    			</ul>

            		</div>

            	</div>

            	<?php } ?>
            <!-- categories end -->

            	
            </div>

        </div>

    </section>

    
 <script type="text/javascript">
$.noConflict();
 </script>   
 <script>
		// perform JavaScript after the document is scriptable.
		jQuery(document).ready(function() {
		//	jQuery("ul.tabs").tabs("> .pane", {effect: 'fade', fadeIn: 200});
		
		
	 jQuery('#category').change(function(){ //any select change on the dropdown with id country trigger this code
	 jQuery("#items > option").remove(); //first of all clear select items
	 var category_id = jQuery('#category').val(); // here we are taking country id of the selected one.
	 jQuery.ajax({
	 type: "POST",
	 url: "<?php echo site_url('classified/get_items'); ?>/"+category_id, //here we are calling our user controller and get_cities method with the country_id
	 
	 success: function(items) //we're calling the response json array 'cities'
	 {
	 jQuery.each(items,function(id,myItems) //here we're doing a foeach loop round each city with id as the key and city as the value
	 {
		 //var opt = jQuery('<option />'); // here we're creating a new select option with for each city
		 //opt.val(id);
		 //opt.text(myItems);
		 jQuery('#items').append( new Option(myItems,id) );
		 //jQuery('#items').append(opt); //here we will append these new select options to a dropdown with the id 'cities'
	 });
	 }
	 
	 });
	 
	 });
 });
 // ]]>
</script>

<script type='text/javascript' src='<?php echo base_url(); ?>templates/front/js/chosen.jquery.min.js'></script>
<script type='text/javascript' src='<?php echo base_url(); ?>templates/front/js/jquery.isotope.min.js'></script>
<script type='text/javascript' src='//code.jquery.com/ui/1.10.4/jquery-ui.js'></script>
<script type='text/javascript' src='<?php echo base_url(); ?>templates/front/js/modernizr.touch.js'></script>
<script type='text/javascript' src='<?php echo base_url(); ?>templates/front/js/jquery.ui.touch-punch.min.js'></script>


