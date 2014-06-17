<section class="row-fluid">
	<h3 class="box-header"><?php echo $heading; ?></h3>
	<div class="box">
	<div class="span9">
	
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
    
    </div>
    </div>
</section>
