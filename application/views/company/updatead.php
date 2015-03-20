<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/admin/css/jquery-ui.css">

<script type='text/javascript' src='//code.jquery.com/ui/1.10.4/jquery-ui.js?ver=2013-07-18'></script>
<script src="<?php echo base_url(); ?>templates/admin/js/bootstrap-tagsinput.min.js" type="text/javascript"></script>

<script type="text/javascript" src='http://maps.google.com/maps/api/js?sensor=false&libraries=places'></script>
<script src="<?php echo base_url(); ?>templates/front/js/locationpicker.jquery.js" type="text/javascript"></script>  
<script type="text/javascript" src="<?php echo base_url();?>templates/front/js/ckeditor/ckeditor.js"></script>                


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

var upload_number = 2;
	function addFileInput() {
	 	var d = document.createElement("div");
	 	var file = document.createElement("input");
	 	file.setAttribute("type", "file");
	 	file.setAttribute("name", "UploadFile[]");
	 	d.appendChild(file);
	 	document.getElementById("moreUploads").appendChild(d);
	 	upload_number++;
	}
 
</script>

    <div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
			<h3>Update Ad  <a class="btn btn-primary btn-sm btn-small" href="<?php echo site_url('company/ads'); ?>">&lt;&lt; Back</a></h3>		
		</div>	
	    <div id="container">
            <?php echo @$message; ?>
                <div class="row">
                 <div class="col-md-12">
                    <div class="grid simple ">                                                  
                        <div class="grid-body no-border">
                           <div class="row">   
               
			                <form class="animated fadeIn" method="post" action="<?php echo base_url("company/updatead/".$adsid); ?>" enctype="multipart/form-data" role="form">
			                <input type="hidden" name="adsid" id="adsid" value="<?php if(isset($adsid)) echo $adsid;?>">
						      <div class="col-md-10 col-sm-10 col-xs-10">
						                     
			                    <div class="form-group">
			                        <label class="form-label">Title</label>
			                        <div class="controls">
			                            <input type="text" id="title" name="title" class="form-control" value='<?php if(isset($ads[0]->title)) echo $ads[0]->title; else echo '';?>' onkeydown="return checkEnter(event);">
			                        </div>
			                    </div>
			                    
			                    <div class="form-group">    
			                        <label class="form-label">Address</label>
			                        <div class="controls">
			                            <input type="text" id="address" name="address" class="form-control" value='<?php if(isset($ads[0]->address)) echo $ads[0]->address; else echo '';?>' autocomplete="off"  onkeydown="return checkEnter(event);" >
			                        </div>
			                        <span class=".sr-only">Start typing an address and select from the dropdown.</span>
			                    </div>
			                        
			                   
			                    <div class="form-group">
			                        <label class="form-label">Price</label>
			                        <div class="controls">
			                            <input type="text" id="price" name="price" class="form-control" value='<?php if(isset($ads[0]->price)) echo $ads[0]->price; else echo '';?>' onkeydown="return checkEnter(event);"  style="width:150px;">		                         
			                        </div>
			                         <label class="form-label">Price Unit</label>
			                        <div class="controls">
			                            <input type="text" id="priceunit" name="priceunit" class="form-control" value='<?php if(isset($ads[0]->priceunit)) echo $ads[0]->priceunit; else echo '';?>' onkeydown="return checkEnter(event);"  style="width:150px;">		                         
			                        </div>
			                    </div>
                    
				                 <div class="form-group">
				                     <label class="form-label">Category</label>
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
                    
                      
				                     <div class="form-group">
				                        <label class="form-label">Item</label>
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
				               
			                        <div id="map-container" style="padding-top: 32px;">
									<div id="map-canvas" style="border:1px solid #e4e4e4;"></div>
			
								    <script>
			                                $('#map-canvas').locationpicker({
			                                location: {latitude: <?php echo $ads[0]->latitude; ?>, longitude: <?php echo $ads[0]->longitude; ?>},	
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
					                        	<input type="text" id="latitude" name="latitude" class="form-control" value="<?php if(isset($ads[0]->latitude)) echo $ads[0]->latitude;?> "> 
					                        </div>
					                    </div>
					
					                    <div class="form-group">
					                        <label class="form-label">Longitude</label>
					                        <div class="controls">
					                            <input type="text" id="longitude" name="longitude" class="form-control" value="<?php if(isset($ads[0]->longitude)) echo $ads[0]->longitude;?>">				                            
					                        </div>
					                    </div>					       
					
					                   <div class="form-group">
					                        <label class="form-label">Image</label>
					                        <div class="controls">
					                            <input type="file" class="fileu" name="UploadFile[]" id="UploadFile" onchange="document.getElementById('moreUploadsLink').style.display = 'block';"  />
					                            <div id="moreUploads"></div>
					                            <div id="moreUploadsLink" style="display:none;">
					                            	<a href="javascript:void(0);" onclick="javascript:addFileInput();">Add another Image</a>
												</div><br />
					                            <!--<a href="<?php //echo site_url('uploads/ads') . '/' . @$this->validation->ad_img; ?>" target="_blank">  -->
					                            <table class="table table-striped">
												<tr>
													<th>Image</th><th>Delete</th>
												</tr>
												<?php  foreach($image as $items)  { ?>
												<tr>
													<td><img src="<?php echo site_url('uploads/ads/'.$items->image);?>" height="100px" width="100px" class="img-thumbnail" alt="<?php echo $items->image;?>"/></td>
													<td><a class="close"  href="<?php echo base_url("company/deleteadimage/".$items->id);?>" onclick="return confirm('Are you really want to delete this image?');">&times;</a></td>
												</tr>
												<?php } ?>
											</table>
					                            </a> 
					                        </div>
					                    </div>	
					                    
					                    <div class="form-group">
					                        <label class="form-label">Description</label>
					                        <div class="controls">
					                            <textarea rows="10" cols="40" class="form-control ckeditor" id="description" name="description" ><?php if(isset($ads[0]->description)) echo $ads[0]->description;?></textarea>
					                        </div>
					                    </div>
					                    
					                   <div class="form-group">
					                        <label class="form-label">Tags</label>
					                        <div class="controls">
					                             <input type="text" id="tags" name="tags" class="form-control" value='<?php if(isset($ads[0]->tags)) echo $ads[0]->tags;?>'  data-role="tagsinput">
					                        </div>
					                    </div>
					                 
					                    <div class="form-group">
					                            <label class="form-label">&nbsp;</label>
					                            <div class="controls">
					                                <input name="add" type="submit" class="btn btn-primary" value="Update Ad"/>
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
    