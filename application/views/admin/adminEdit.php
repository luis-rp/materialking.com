<script type="text/javascript">
<!--
$(document).ready(function(){
	$('#about').wysihtml5();
});
//-->
</script>
<?php echo $this->session->flashdata('message'); ?>
<section class="row-fluid">
	<h3 class="box-header"><?php echo $title; ?></h3>
	<div class="box">
	<div class="span9">
	
	
  <div class="pull-left" style="width:70%;">
   <form class="form-horizontal" method="post" action="<?php echo $action; ?>">
   <input type="hidden" name="id" value="<?php echo $this->validation->id;?>"/>
    <br/>
    <?php //if($this->session->userdata('usertype_id') == 1){?>
    <div class="control-group">
    <label class="control-label" for="usertype_id">User Type</label>
    <div class="controls">
    <select name="usertype_id">
	<?php
	
		foreach($userarrays as $userarray) {
		echo '<option value="'.$userarray['id'].'" ';
		echo $this->validation->usertype_id == $userarray['id'] ? ' selected="selected"' : '';
		echo '>'.$userarray['userType'].'</option>';
		}
		?>
	<?php echo $this->validation->usertype_id_error; ?>
	</select>
    </div>
    </div>
    <?php //}
    //elseif($this->session->userdata('usertype_id') == 2){
    	if($this->session->userdata('usertype_id') == 2){?>
    <input type="hidden" name="purchasingadmin" value="<?php echo $this->session->userdata('id');?>"/>
    <?php }?>
    
    
   <!-- <div class="control-group">
    <label class="control-label" for="category" required>Category</label>
    <div class="controls">
    <select name="category">
	 <?php foreach($categories as $cat){?>
	  <option value='<?php echo $cat->id;?>' <?php if($this->validation->category==$cat->id){ echo 'SELECTED'; }?>><?php echo $cat->catname;?></option>
     <?php }?>
	</select>
    </div>
    </div>-->
        
    <div class="control-group">
    <label class="control-label" for="username">User Name *</label>
    <div class="controls">
    <input type="text" name="username" class="span4" class="text" required value="<?php echo $this->validation->username; ?>"/>
	<?php echo $this->validation->username_error; ?>
    </div>
    </div>
  
    
    <div class="control-group">
    <label class="control-label" for="fullname">Full Name *</label>
    <div class="controls"> 		
	<input type="text" name="fullname" class="span4" class="text" required value="<?php echo $this->validation->fullname; ?>"/>
	<?php echo $this->validation->fullname_error; ?>
    </div>
    </div>
  
    <div class="control-group">
    <label class="control-label" for="email">Email Address*</label>
    <div class="controls">
   <input type="email" name="email" required class="text span4" value="<?php echo $this->validation->email; ?>"/>
	<?php echo $this->validation->email_error; ?>
    </div>
    </div>
    
    
    <?php if(!$this->validation->id){?>
    <div class="control-group">
    <label class="control-label">Password *</label>
    <div class="controls">
    <input type="password" name="password" id="password" class="span4" class="text" value="<?php echo $this->validation->password; ?>" required/>
	<?php echo $this->validation->password_error; ?>
    </div>
    </div>
    <?php }?>

    <?php if($this->session->userdata('usertype_id') < 3){?>
    <div class="control-group">
    <label class="control-label">Status</label>
    <div class="controls">
  	<input type="radio" name="status" value="1" <?php echo $this->validation->set_radio('status', '1'); ?>/> Active <br/><br/>
	<input type="radio" name="status" value="0" <?php echo $this->validation->set_radio('status', '0'); ?>/> Deactive
	<?php echo $this->validation->status_error; ?>
    </div>
    </div>
    <?php }else{?>
    <input type="hidden" name="status" value="<?php $this->validation->status;?>"/>
    <?php } ?>
    <div class="control-group">
    <label class="control-label">&nbsp;</label>
    <div class="controls">
     <input type="submit" class="btn btn-primary" value="Save"/>
    </div>
    </div>
    
  </form>
  
         </div><!-- End of Pull left -->
   <?php if(isset($adminusers) && count($adminusers) > 0) { ?>
	   <div class="pull-right" style="width:26%;">
		   <div class="table-responsive">
			   <h3>Existing Users</h3>
				  <table class="table table-hover">
				  <tr><th>User Name</th></tr>
				    <?php foreach ($adminusers as $adminuser) { ?>
				  		<tr><td><?php echo $adminuser->fullname; ?></td></tr>
				     <?php } ?>
				  </table>
			</div>
	   </div><!-- End of Pull right -->
   <?php } ?>
	   
	   <div style="clear:both;"></div>
    
    </div>
    </div>
</section>
