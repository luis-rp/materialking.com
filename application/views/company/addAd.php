<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/admin/js/adminflare.min.js">
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/datatable.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>

<style>
.dataTables_filter
{
	margin-right:30px;
}
</style>



<script type="text/javascript">

    $(document).ready(function() {        
        
        $('#description').wysihtml5();
        
        //$('#description').wysihtml5();
        //$('#details').wysihtml5();


       // $('#tagsInput').tagsinput();
    });



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
                <form class="form-horizontal" method="post" action="<?php echo base_url("company/saveAd"); ?>" enctype="multipart/form-data">
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
                        <label class="control-label">Location</label>
                        <div class="controls">
                            <input type="text" id="location" name="location" class="span10" value="">
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
                            <input type="file" name="adfile" size="20"  />
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








