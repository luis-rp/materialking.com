<script type="text/javascript" src="<?php echo base_url();?>templates/front/js/ckeditor/ckeditor.js"></script>
<div class="content">  
    	 <?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">
			<h3>Edit Newsletter Predefined Template</h3>		
		</div>
	
	   <div id="container">
		<div class="row">
				
                    <div class="col-md-12">
                        <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                            </div>
                            
                           <div class="grid-body no-border">
                            	<div class="row">
                    				<form id="profileform" name="profileform" class="animated fadeIn" method="post" action="<?php echo site_url('company/addpretemplate'); ?>" enctype="multipart/form-data">
         
                    				<div class="col-md-10 col-sm-10 col-xs-10">
                    				  <div class="form-group">
				                        <label class="form-label">Template Name:</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="title" value="<?php echo @$title;?>" required>
				                        </div>
				                      </div>
                    				
				                      <div class="form-group">
				                        <label class="form-label">Template Body:</label>
				                        <p>Please use your Subscriber information inside { } to math the data</p>
				                        <div class="controls">
				                          <textarea rows="10" cols="40" class="form-control ckeditor" id="body" name="body"><?php echo @$body;?></textarea>
				                        </div>
				                      </div>

				                      <div class="form-group">
				                        <label class="form-label"></label>
				                        <div class="controls">
				                       
				                          <input type="submit" value="Use This Template" class="btn btn-primary btn-cons general">
				                          *After Use this Tempalte please go to Your Templates
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