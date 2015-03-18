<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/admin/css/jquery-ui.css">

<script type='text/javascript' src='//code.jquery.com/ui/1.10.4/jquery-ui.js?ver=2013-07-18'></script>
<script src="<?php echo base_url(); ?>templates/admin/js/bootstrap-tagsinput.min.js" type="text/javascript"></script>

<script type="text/javascript" src='http://maps.google.com/maps/api/js?sensor=false&libraries=places'></script>
<script src="<?php echo base_url(); ?>templates/front/js/locationpicker.jquery.js" type="text/javascript"></script> 
<script type="text/javascript" src="<?php echo base_url();?>templates/front/js/ckeditor/ckeditor.js"></script>               
<!--<style>
.dataTables_filter
{
	margin-right:30px;
}
</style>-->

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
 
function checkEnter(event)
{ 
	if (event.keyCode == 13) 
   {
       return false;
    }
}


/*function check_file_upload(){
    	
	if($('.fileu').val() !="" || $('.fileu').val() !="undefined");
	return true;
	else{
	alert("Please upload file");	
	return false;
	}
}*/
 
</script>
    <div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>	 	
    	<div class="page-title">	
			<h3>New Ad  <a class="btn btn-primary btn-sm btn-small" href="<?php echo site_url('company/ads'); ?>">&lt;&lt; Back</a></h3>		
		</div>	  	
	     <div id="container">
               <?php echo @$message; ?>
              <div class="row">
                 <div class="col-md-12">
                    <div class="grid simple ">                                                  
                        <div class="grid-body no-border">
                           <div class="row">
                            
                		   <form class="animated fadeIn" method="post" action="<?php echo base_url("company/saveAd"); ?>" enctype="multipart/form-data" role="form"> 
                             <div class="col-md-10 col-sm-10 col-xs-10">
                             
                		       <div class="form-group">                   
	                        		<label for="title" class="form-label">Title</label> 
	                        		<div class="controls">	                     
	                            	<input type="text" id="title" name="title" class="form-control" value="" onkeydown="return checkEnter(event);">
	                            	</div>
                         	  </div>
                         		
                        	  <div class="form-group">
                        	  	<label for="address" class="form-label">Address</label>
                                <div class="controls">	
                            	<input type="text" id="address" name="address" class="form-control"  autocomplete="off" onkeydown="return checkEnter(event);" >
                            	</div> 
                            	 <span class=".sr-only">Start typing an address and select from the dropdown.</span>                     
                             </div>
                             

		                    <div class="form-group">
		                        <label class="form-label">Price</label>
		                        <div class="controls">
		                            <input type="text" id="price" name="price" class="form-control" value="" onkeydown="return checkEnter(event);">		                      						</div>
		                    </div>
		                    
                    
		                     <div class="form-group">
		                        <label class="form-label">Category</label>
		                        <div class="controls">		                        
		                            <select id="category" name="category">
		                            	<?php foreach($categories as $cat){?>
		                            	<option value="<?php echo $cat->id;?>" ><?php echo $cat->catname;?></option>
		                            	<?php }?>
    	                           </select> 
    	                       </div>     
		                    </div>
                    
                      
		                     <div class="form-group">
		                        <label class="form-label">Item</label>
		                        <div class="controls">
		                            <select id="items" name="items">
		                            	<?php foreach($items as $key=>$item){ ?>
		                            	<option value="<?php echo $key;?>" ><?php echo $item;?></option>
		                            	<?php }?>
		                            </select>
		                        </div>
		                    </div>
                    
                            <div id="map-container" style="padding-top:32px;">
						    <div id="map-canvas" style="border:1px solid #e4e4e4;"></div>

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
                      
                   
        
             
		                    <div class="form-group">
		                        <label class="form-label">Latitude</label>
		                        <div class="controls">
		                        <input type="text" id="latitude" name="latitude" class="form-control" value="<?php if(isset($company->com_lat)) echo $company->com_lat;?>"> 
		                        </div>
		                    </div>

		                    <div class="form-group">
		                        <label class="form-label">Longitude</label>
		                        <div class="controls">
		                        <input type="text" id="longitude" name="longitude" class="form-control" value="<?php if(isset($company->com_lng)) echo $company->com_lng;?>">                        </div>
		                    </div>
          
                
		                    <div class="form-group">
		                        <label class="form-label">Image</label>
		                        <div class="controls">
		                            <input type="file" class="fileu" name="adfile[]" multiple size="20"  />
		                            <a href="<?php echo site_url('uploads/ads') . '/' . @$this->validation->ad_img; ?>" target="_blank">  
		                            </a> 
		                        </div>
		                    </div>
		                    		                    
		                    
		                    <div class="form-group">
		                        <label class="form-label">Description</label>
		                        <div class="controls">
		                  <textarea rows="10" cols="40" class="form-control ckeditor" id="description" name="description"><?php echo $company->about;?></textarea>
		                        </div>
		                    </div>
                    
		                   <div class="form-group">
		                        <label class="form-label">Tags</label>
		                        <div class="controls">
		                             <input type="text" id="tags" name="tags" class="form-control" value=""  data-role="tagsinput">
		                        </div>
		                    </div>
                 
		                    <div class="form-group">
		                            <label class="form-label">&nbsp;</label>
		                            <div class="controls">
		                                <input name="add" type="submit" class="btn btn-primary" value="Add Ad"/>
		                            </div>
		                    </div>
		             </div>
		           </form> 
		                   
            	</div>
          	</div>
         </div>
      </div>
    </div>
  </div>
</div>

  







