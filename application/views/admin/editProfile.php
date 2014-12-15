<section class="row-fluid">
	<h3 class="box-header"><?php echo $heading; ?></h3>
	<div class="box">
	<div class="span9">
	<div class="pull-left" style="width:70%;">
	<?php echo $message; ?>

   <form class="form-horizontal" method="post" action="<?php echo $action; ?>">
   <input type="hidden" name="id" value="<?php echo $this->validation->id;?>"/>
    <br/>
    
    
    <div class="control-group">
    <label class="control-label" for="username">Old Password *</label>
    <div class="controls">
    <input class="text span4" type="password" name="password" id="password" disabled="true" value="<?php echo $this->validation->password; ?>"/>
    
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label" for="email">New Password</label>
    <div class="controls">
   <input class="text span4" type="password" name="newpassword" id="newpassword" value="<?php echo $this->validation->newpassword; ?>"/>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label" for="fullname">Retype New Password</label>
    <div class="controls"> 		
	<input class="text span4" type="password" name="rnewpassword" id="rnewpassword" value="<?php echo $this->validation->rnewpassword; ?>"/>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">&nbsp;</label>
    <div class="controls">
     <input name="add" type="submit" class="btn btn-primary" value="Change Password"/>
    </div>
    </div>
    
  </form>
  
      </div><!-- End of Pull left -->
   <?php if(isset($permissions) && count($permissions) > 0) { ?>
	   <div class="pull-right" style="width:26%;">
		   <div class="table-responsive">
			   <h3>Existing Permissions</h3>
				  <table class="table table-hover">
				  <tr><th>Quote Name</th><th>Project Name</th></tr>
				    <?php foreach ($permissions as $permission) { ?>
				  		<tr><td><?php echo $permission->quotename; ?></td><td><?php echo $permission->projectname; ?></td></tr>
				     <?php } ?>
				  </table>
			</div>
	   </div><!-- End of Pull right -->
   <?php } ?>
	   
	   <div style="clear:both;"></div>
    
    </div>
    </div>
</section>
