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
			<h3>New Ad</h3>		
		</div>		
    <div class="box">
            <div class="span12">

                <?php echo @$message; ?>
                <?php echo $this->session->flashdata('message'); ?>
                <a class="btn btn-green" href="<?php echo site_url('company/ads'); ?>">&lt;&lt; Back</a>
                <br/>
                <form class="" method="post" action="<?php echo base_url("company/saveAd"); ?>" enctype="multipart/form-data">
                <div  style="width:100%; float:left;">
         
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
                    
             
 
                   
                        <label class="">Address</label>
                        <div class="">
                            <input type="text" id="address" name="address" class="span10" value="" autocomplete="off">
                            <p class="help-block">Start typing an address and select from the dropdown.</p>
							
                            <div id="map-container" style="float:right;">
						    <div id="map-canvas"></div>

							<script>
                                $('#map-canvas').locationpicker({
                                location: {latitude: <?php echo $company->com_lat; ?>, longitude: <?php echo $company->com_lng; ?>},	
                                radius: 300,
                                inputBinding: {
                                    latitudeInput: $('#latitude'),
                                    longitudeInput: $('#longitude'),
                                    locationNameInput: $('#address')        
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








