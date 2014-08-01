<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/admin/css/jquery-ui.css">
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&amp;language=en"></script>
<script type='text/javascript' src='//code.jquery.com/ui/1.10.4/jquery-ui.js?ver=2013-07-18'></script>
<script src="<?php echo base_url(); ?>templates/admin/js/bootstrap-tagsinput.min.js" type="text/javascript"></script>
                
<style>
.dataTables_filter
{
	margin-right:30px;
}
</style>

 <script type="text/javascript">// <![CDATA[
 $(document).ready(function(){
	 $('#category').change(function(){ 
	 $("#items > option").remove();
	 var category_id = $('#category').val();
	 $.ajax({
	 type: "POST",
	 url: "<?php echo site_url('company/getitems'); ?>/"+category_id, 
	 
	 success: function(items)
	 {
	 $.each(items,function(id,myItems)
	 {
		 var opt = $('<option />');
		 opt.val(id);
		 opt.text(myItems);
		 $('#items').append(opt);
	 });
	 }
	 });
	 });
 });
 // ]]>
</script>




    <div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>
		
	   <div id="container">
                     <div class="combofixed">       
                      
                       
                       
<section class="row-fluid">
    <div class="page-title">	
			<h3>New Ad</h3>		
		</div>		
    <div class="box">
            <div class="span12">

                <?php echo @$message; ?>
                <?php echo $this->session->flashdata('message'); ?>
                <a class="btn btn-green" href="<?php echo site_url('company/ads'); ?>">&lt;&lt; Back</a>
                <br/>
                <form class="" method="post" action="<?php echo base_url("company/saveAd"); ?>" enctype="multipart/form-data">
                <div  style="width:48%; float:left;">
         
                    <br/>

                  
                    
                    <div class="control-group">
                        <label class="control-label">Title</label>
                        <div class="controls">
                            <input type="text" id="title" name="title" class="span10" value="">
                            <?php //echo $this->validation->itemcode_error; ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Price</label>
                        <div class="controls">
                            <input type="text" id="price" name="price" class="span10" value="">
                         
                        </div>
                    </div>
                    
                     <div class="control-group">
                        <label class="control-label">Category</label>
                        <div class="controls">
                            <select id="category" name="category">
                            	<?php foreach($categories as $cat){?>
                            	<option value="<?php echo $cat->id;?>" ><?php echo $cat->catname;?></option>
                            	<?php }?>
                            </select>
                        </div>
                    </div>
                    
                      
                     <div class="control-group">
                        <label class="control-label">Item</label>
                        <div class="controls">
                            <select id="items" name="items">
                            	<?php foreach($items as $item){?>
                            	<option value="<?php echo $item->id;?>" ><?php echo $item->itemcode;?></option>
                            	<?php }?>
                            </select>
                        </div>
                    </div>
                    
                   
                     <div id="map-container">
                        <label class="">Address</label>
                        <div class="">
                            <input type="text" id="address" name="address" class="span10" value="">
                            <p class="help-block">Start typing an address and select from the dropdown.</p>

						    <div id="map-canvas"></div>

						    <script type="text/javascript">

								jQuery(document).ready(function($) {

									var geocoder;
									var map;
									var marker;

									var geocoder = new google.maps.Geocoder();

									function geocodePosition(pos) {
									  geocoder.geocode({
									    latLng: pos
									  }, function(responses) {
									    if (responses && responses.length > 0) {
									      updateMarkerAddress(responses[0].formatted_address);
									    } else {
									      updateMarkerAddress('Cannot determine address at this location.');
									    }
									  });
									}

									function updateMarkerPosition(latLng) {
									  jQuery('#latitude').val(latLng.lat());
									  jQuery('#longitude').val(latLng.lng());
									}

									function updateMarkerAddress(str) {
									  jQuery('#address').val(str);
									}

									function initialize() {

									  var latlng = new google.maps.LatLng(0, 0);
									  var mapOptions = {
									    zoom: 2,
									    center: latlng
									  }

									  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

									  geocoder = new google.maps.Geocoder();

									  marker = new google.maps.Marker({
									  	position: latlng,
									    map: map,
									    draggable: true
									  });

									  // Add dragging event listeners.
									  google.maps.event.addListener(marker, 'dragstart', function() {
									    updateMarkerAddress('Dragging...');
									  });
									  
									  google.maps.event.addListener(marker, 'drag', function() {
									    updateMarkerPosition(marker.getPosition());
									  });
									  
									  google.maps.event.addListener(marker, 'dragend', function() {
									    geocodePosition(marker.getPosition());
									  });

									}

									google.maps.event.addDomListener(window, 'load', initialize);

									jQuery(document).ready(function() { 
									         
									  initialize();
									          
									  jQuery(function() {
									    jQuery("#address").autocomplete({
									      //This bit uses the geocoder to fetch address values
									      source: function(request, response) {
									        geocoder.geocode( {'address': request.term }, function(results, status) {
									          response(jQuery.map(results, function(item) {
									            return {
									              label:  item.formatted_address,
									              value: item.formatted_address,
									              latitude: item.geometry.location.lat(),
									              longitude: item.geometry.location.lng()
									            }
									          }));
									        })
									      },
									      //This bit is executed upon selection of an address
									      select: function(event, ui) {
									        jQuery("#latitude").val(ui.item.latitude);
									        jQuery("#longitude").val(ui.item.longitude);

									        var location = new google.maps.LatLng(ui.item.latitude, ui.item.longitude);

									        marker.setPosition(location);
									        map.setZoom(16);
									        map.setCenter(location);

									      }
									    });
									  });
									  
									  //Add listener to marker for reverse geocoding
									  google.maps.event.addListener(marker, 'drag', function() {
									    geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
									      if (status == google.maps.GeocoderStatus.OK) {
									        if (results[0]) {
									          jQuery('#address').val(results[0].formatted_address);
									          jQuery('#latitude').val(marker.getPosition().lat());
									          jQuery('#longitude').val(marker.getPosition().lng());
									        }
									      }
									    });
									  });
									  
									});

								});

						    </script>
                        </div>
                        </div>
                   
                    
                    <div class="control-group">
                        <label class="control-label">Latitude</label>
                        <div class="controls">
                        	<input type="text" id="latitude" name="latitude" class="span10" value=""> 
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Longitude</label>
                        <div class="controls">
                            <input type="text" id="longitude" name="longitude" class="span10" value="">
                            
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">File</label>
                        <div class="controls">
                            <input type="file" name="adfile[]" multiple size="20"  />
                            <a href="<?php echo site_url('uploads/ads') . '/' . @$this->validation->ad_img; ?>" target="_blank">  
                            </a> 
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Description</label>
                        <div class="controls">
                            <textarea class="span10" rows="10" id="description" name="description" ></textarea>
                        </div>
                    </div>
                       <div class="control-group">
                        <label class="control-label">Tags</label>
                        <div class="controls">
                             <input type="text" id="tags" name="tags" class="span10" value=""  data-role="tagsinput">
                        </div>
                    </div>
                 
                    <div class="control-group">
                            <label class="control-label">&nbsp;</label>
                            <div class="controls">
                                <input name="add" type="submit" class="btn btn-primary" value="Update Itemcode List"/>
                            </div>
                    </div>
             

                </div>
                </form>
            </div>
    </div>
	<!--<div class="control-group">
                    <label class="control-label">Attachment</label>
                    <div class="controls">
                        <form action="<?php //echo base_url();    ?>admin/itemcode/fileupload" id="uploadfrm" name="uploadfrm" enctype="multipart/form-data" method="post">
      <input type="file" id="filesel" name="filesel"  >
      <input type="submit" name="btnupload" class="btn btn-primary" value="Upload" />

        </form>
    </div>
    </div>-->
</section>
                       
                       
                      </div>
                  
		
		
			
		</div>
	  </div> 








