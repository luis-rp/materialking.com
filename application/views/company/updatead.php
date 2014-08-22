<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/admin/css/jquery-ui.css">

<script type='text/javascript' src='//code.jquery.com/ui/1.10.4/jquery-ui.js?ver=2013-07-18'></script>
<script src="<?php echo base_url(); ?>templates/admin/js/bootstrap-tagsinput.min.js" type="text/javascript"></script>

<script type="text/javascript" src='http://maps.google.com/maps/api/js?sensor=false&libraries=places'></script>
<script src="<?php echo base_url(); ?>templates/front/js/locationpicker.jquery.js" type="text/javascript"></script>                
<style>
.dataTables_filter
{
	margin-right:30px;
}
</style>

 <script type="text/javascript">// <![CDATA[
 $(document).ready(function(){
	 $('#category').change(function(){ //any select change on the dropdown with id country trigger this code
	 $("#items > option").remove(); //first of all clear select items
	 var category_id = $('#category').val(); // here we are taking country id of the selected one.
	 $.ajax({
	 type: "POST",
	 url: "<?php echo site_url('company/get_items'); ?>/"+category_id, //here we are calling our user controller and get_cities method with the country_id
	 
	 success: function(items) //we're calling the response json array 'cities'
	 {
	 $.each(items,function(id,myItems) //here we're doing a foeach loop round each city with id as the key and city as the value
	 { 
		 var opt = $('<option />'); // here we're creating a new select option with for each city
		 opt.val(id);
		 opt.text(myItems);
		 $('#items').append(opt); //here we will append these new select options to a dropdown with the id 'cities'
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
			<h3>Update Ad</h3>		
		</div>		
    <div class="box">
            <div class="span12">

                <?php echo @$message; ?>
                <?php echo $this->session->flashdata('message'); ?>
                <a class="btn btn-green" href="<?php echo site_url('company/ads'); ?>">&lt;&lt; Back</a>
                <br/>
                <form class="" method="post" action="<?php echo base_url("company/updatead"); ?>" enctype="multipart/form-data">
                <input type="hidden" name="adsid" id="adsid" value="<?php if(isset($adsid)) echo $adsid;?>"
                <div  style="width:100%; float:left;">
         
                    <br/>

                  
                    
                    <div class="control-group">
                    
                    <div style="float:left;">
                        <label class="control-label">Title</label>
                        <div class="controls">
                            <input type="text" id="title" name="title" class="span10" value='<?php if(isset($ads[0]->title)) echo $ads[0]->title; else echo '';?>'>
                            <?php //echo $this->validation->itemcode_error; ?>
                        </div>
                        </div>
                         <div style="float:left; margin-left:40px;">
                        <label class="">Address</label>
                        <div class="">
                            <input type="text" id="address" name="address" class="span10" value='<?php if(isset($ads[0]->address)) echo $ads[0]->address; else echo '';?>' autocomplete="off" style="float:left;" >
                            <p class="help-block" style="float:left;margin-left:10px;" >Start typing an address and select from the dropdown.</p>
                        </div>
                        </div>
                    </div>
<div style="clear:both;"></div>
                    <div class="control-group">
                        <label class="control-label">Price</label>
                        <div class="controls">
                            <input type="text" id="price" name="price" class="span10" value='<?php if(isset($ads[0]->price)) echo $ads[0]->price; else echo '';?>'>
                         
                        </div>
                    </div>
                    
                     <div class="control-group">
                        <label class="control-label">Category</label>
                        <div class="controls">
                            <select id="category" name="category">
                            	<?php 
                            	$selCategory = "";
                            		foreach($categories as $cat){
                            		if(isset($ads[0]->category))
                            		{
                            			if($ads[0]->category == $cat->id)
                            			{
                            				$selCategory = " selected ";
                            			}
                            			else 
                            			{
                            				$selCategory = "";
                            			}
                            		}
                            		?>
                            	<option value="<?php echo $cat->id;?>" <?php echo $selCategory;?> ><?php echo $cat->catname;?></option>
                            	<?php }?>
                            </select>
                        </div>
                    </div>
                    
                      
                     <div class="control-group">
                        <label class="control-label">Item</label>
                        <div class="controls">
                            <select id="items" name="items">
                            	<?php 
                            		$selitem = "";
                            		foreach($items as $item){
                            		if($ads[0]->itemid == $item->id)
                            			{
                            				$selitem = " selected ";
                            			}
                            			else 
                            			{
                            				$selitem = "";
                            			}
                            		?>
                            	<option value="<?php echo $item->id;?>" <?php echo $selitem;?> ><?php echo $item->itemcode;?></option>
                            	<?php }?>
                            </select>
                        </div>
                    </div>
                    
             
 
                   
                        
							<div style="clear:both;"></div>
                            <div id="map-container" style="padding-top: 32px;">
						    <div id="map-canvas"></div>

							<script>
                                $('#map-canvas').locationpicker({
                                location: {latitude: <?php echo $ads[0]->latitude; ?>, longitude: <?php echo $ads[0]->longitude; ?>},	
                                radius: 300,
                                inputBinding: {
                                    latitudeInput: $('#latitude'),
                                    longitudeInput: $('#longitude')
                                    //locationNameInput: $('#address')        
                                },
                                enableAutocomplete: true,
                                onchanged: function(currentLocation, radius, isMarkerDropped) {
                                    alert("Location changed. New location (" + currentLocation.latitude + ", " + currentLocation.longitude + ")");					
                                    $('#latitude').val(currentLocation.latitude);	
                                    $('#longitude').val(currentLocation.longitude);                                   
                                   
                                }	
                                });
							</script>
                        
                   </div>
         <div style="clear:both;"></div>
             
                    <div class="control-group">
                        <label class="control-label">Latitude</label>
                        <div class="controls">
                        	<input type="text" id="latitude" name="latitude" class="span10" value="<?php if(isset($ads[0]->latitude)) echo $ads[0]->latitude;?> "> 
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Longitude</label>
                        <div class="controls">
                            <input type="text" id="longitude" name="longitude" class="span10" value="<?php if(isset($ads[0]->longitude)) echo $ads[0]->longitude;?>">
                            
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
                            <textarea class="span10" rows="10" id="description" name="description" ><?php if(isset($ads[0]->description)) echo $ads[0]->description;?></textarea>
                        </div>
                    </div>
                       <div class="control-group">
                        <label class="control-label">Tags</label>
                        <div class="controls">
                             <input type="text" id="tags" name="tags" class="span10" value='<?php if(isset($ads[0]->tags)) echo $ads[0]->tags;?>'  data-role="tagsinput">
                        </div>
                    </div>
                 
                    <div class="control-group">
                            <label class="control-label">&nbsp;</label>
                            <div class="controls">
                                <input name="add" type="submit" class="btn btn-primary" value="Update Ads"/>
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