<script type="text/javascript">
<!--
$(document).ready(function(){
	$('#intro').wysihtml5();
	$('#content').wysihtml5();
	$('#companydate').datepicker();
});
//-->
</script>

<section class="row-fluid">
	<h3 class="box-header"><?php echo $heading; ?></h3>
	<div class="box">
	<div class="span12">
	
	<?php echo $message; ?>

   <form class="form-horizontal" method="post" action="<?php echo $action; ?>"> 
   <input type="hidden" name="id" value="<?php echo $this->validation->id;?>"/>
    <br/>
    
    <div class="control-group">
    <label class="control-label">Company Name</label>
    <div class="controls">
      <input type="text" id="title" name="title" class="span4" value="<?php echo $this->validation->title; ?>">
      <?php echo $this->validation->title_error;?>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">Primary Email</label>
    <div class="controls">
      <input type="text" id="primaryemail" name="primaryemail" class="span4" value="<?php echo $this->validation->primaryemail; ?>">
      <?php echo $this->validation->primaryemail_error;?>
    </div>
    </div>
    
    <div class="control-group">
	    <label class="control-label">Username</label>
	    <div class="controls">
	      <?php echo $this->validation->username;?>
	    </div>
    </div>
    
    <div class="control-group">
	    <label class="control-label">Password</label>
	    <div class="controls">
	      <input type="password" id="password" name="password" class="span4">
	      <?php if($this->session->userdata('usertype_id') == 1){?>Existing password: <?php echo $this->validation->pwd;?>
	      <br/><?php }?>(Enter to change)
	    </div>
    </div>
    <?php if(0){?>
    <div class="control-group">
    <label class="control-label">Other Emails</label>
    <div class="controls">
       <textarea id="email" class="span4" rows="4" name="email" ><?php echo $this->validation->email; ?></textarea>
      <?php echo $this->validation->email_error;?>
    </div>
    </div>
    <?php }?>
    <div class="control-group">
	    <label class="control-label">Contact</label>
	    <div class="controls">
	      <input type="text" id="contact" name="contact" class="span4" value="<?php echo $this->validation->contact; ?>">
	      <?php echo $this->validation->contact_error;?>
	    </div>
    </div>
    
    <div class="control-group">
	    <label class="control-label">Complete Address, Include City/State/Zip</label>
	    <div class="controls">
	      <textarea id="address" class="span5" rows="6" name="address" ><?php echo $this->validation->address; ?></textarea>
	      <?php echo $this->validation->address_error;?>
	    </div>
    </div>
    
    <div class="control-group">
	    <label class="control-label">Type:</label>
	    <div class="controls">
	      <table class="table table-bordered span6">
	      	<tr>
	      		<th class="span6">Industry</th>
	      		<th class="span6">Manufacturer</th>
	      	</tr>
	      	<tr valign="top">
	      		<td>
	      			<?php foreach($types as $type) if($type->category=='Industry'){?>
	      			<input name="types[]" type="checkbox" value="<?php echo $type->id;?>" <?php echo @$type->checked?'checked="checked"':'';?>>
	      			<?php echo $type->title;?>
	      			<br/>
	      			<?php }?>
	      		</td>
	      		<td>
	      			<?php foreach($types as $type) if($type->category=='Manufacturer'){?>
	      			<input name="types[]" type="checkbox" value="<?php echo $type->id;?>" <?php echo @$type->checked?'checked="checked"':'';?>>
	      			<?php echo $type->title;?>
	      			<br/>
	      			<?php }?>
	      		</td>
	      	</tr>
	      </table>
	    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">&nbsp;</label>
    <div class="controls">
     <input type="submit" class="btn btn-primary" value="Update Company List"/>
    </div>
    </div>
    
  </form>
    
    </div>
    </div>
</section>
