<script type="text/javascript" src="<?php echo base_url();?>templates/front/js/ckeditor/ckeditor.js"></script>
<div class="content">  
    	 <?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">
			<h3>Create new Newsletter Template</h3>		
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
                    				<form id="profileform" name="profileform" class="animated fadeIn" method="post" action="<?php if($action=="new"){ echo site_url('company/addtemplate'); }else{ echo site_url('company/updatetemplate/'.$id); }?>" enctype="multipart/form-data">
         
                    				<div class="col-md-10 col-sm-10 col-xs-10">
                    				  <div class="form-group">
				                        <label class="form-label">Template Name:</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="title" value="<?php echo @$title;?>" required>
				                        </div>
				                      </div>
                    				
				                      <div class="form-group">
				                        <label class="form-label">Template Body:</label>
				                        <p>Please use your Subscriber information inside { } to match the data</p>
				                        <p><?php foreach($fields as $field){ ?>{<?php echo $field->Name;?>},<?php } ?>{name},{email}</p>
				                        <div class="controls">
				                          <textarea rows="10" cols="40" class="form-control ckeditor" id="body" name="body"><?php echo @$body;?></textarea>
				                        </div>
				                      </div>

				                      <div class="form-group">
				                        <label class="form-label"></label>
				                        <div class="controls">
				                       <?php if($action=="new"){  ?> <input type="hidden" name="cid" value="<?php echo $cid;?>"> <?php } ?>
				                          <input type="submit" value="Save" class="btn btn-primary btn-cons general">
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