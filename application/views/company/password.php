
    <div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
			<h3>Change Password</h3>		
		</div>		
	   <div id="container">
		
		<div class="row">
                    <div class="col-md-12">
                        <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                                <div class="tools">	<a href="javascript:;" class="collapse"></a>
									<a href="#grid-config" data-toggle="modal" class="config"></a>
									<a href="javascript:;" class="reload"></a>
									<a href="javascript:;" class="remove"></a>
                                </div>
                            </div>
                            
                            <div class="grid-body no-border">
                            	<div class="row">
                    				<div class="col-md-8 col-sm-8 col-xs-8">
                    				<form id="profileform" name="profileform" class="animated fadeIn" method="post" action="<?php echo site_url('company/savepassword');?>">
				                      
				                      <div class="form-group">
				                        <label class="form-label">Current Password</label>
				                        <div class="controls">
				                          <input type="password" class="form-control" name="epassword" required>
				                        </div>
				                      </div>
				                      <div class="form-group">
				                        <label class="form-label">New Password</label>
				                        <div class="controls">
				                          <input type="password" class="form-control" name="password" required>
				                        </div>
				                      </div>
				                      <div class="form-group">
				                        <label class="form-label">Confirm New Password</label>
				                        <div class="controls">
				                          <input type="password" class="form-control" name="cpassword" required>
				                        </div>
				                      </div>
                    				
				                      <div class="form-group">
				                        <label class="form-label"></label>
				                        <div class="controls">
				                          <input type="submit" value="Save" class="btn btn-primary btn-cons general">
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
	  </div> 