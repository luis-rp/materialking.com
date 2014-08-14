<script type="text/javascript">
<!--
$(document).ready(function(){
	$('#intro').wysihtml5();
	$('#content').wysihtml5();
	$('#companydate').datepicker();
});
//-->
</script>

<script type="text/javascript">

    function IsValidZip(zip) {
        var isValid = /^[0-9]{5}(?:-[0-9]{4})?$/.test(zip);
        if (!isValid){
       alert('Invalid Zip Format. Please Enter XXXXX or XXXXX-XXXX Format');
      document.getElementById("zip").value = "";
    }
    }
</script>

<script>
  $(document).ready(function()
  {
    $("#password").val("");
  });
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
    <label class="control-label">Company Name*</label>
    <div class="controls">
      <input type="text" id="title" name="title" class="span4" value="<?php echo $this->validation->title; ?>" required>
      <?php echo $this->validation->title_error;?>
    </div>
    </div>

    <div class="control-group">
    <label class="control-label">Primary Email*</label>
    <div class="controls">
      <input type="text" id="primaryemail" name="primaryemail" class="span4" value="<?php echo $this->validation->primaryemail; ?>" required>
      <?php echo $this->validation->primaryemail_error;?>
    </div>
    </div>

    <div class="control-group">
	    <label class="control-label">Username*</label>
	    <div class="controls">
	      <input type="text" id="username" name="username" class="span4" value="<?php if(isset($this->validation->username)) echo $this->validation->username; else echo ''; ?>" required>
	    </div>
    </div>

    <div class="control-group">
	    <label class="control-label">Password</label>
	    <div class="controls">
	      <input type="password" id="password" name="password" class="span4">
	      <?php if($this->session->userdata('usertype_id') == 1){?>Existing password: <?php if(isset($this->validation->pwd)) echo $this->validation->pwd; else echo '';?>
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
	    <label class="control-label">Street Address*</label>
	    <div class="controls">
	      <textarea id="street" class="span5" rows="6" name="street" required><?php echo $this->validation->street; ?></textarea>
	      <?php echo $this->validation->address_error;?>
	    </div>
    </div>

    <div class="control-group">
	    <label class="control-label">City*</label>
	    <div class="controls">
<input type="text" id="city" name="city" class="span4" value="<?php if(isset($this->validation->city)) echo $this->validation->city; else echo ''; ?>" required>
	    </div>
    </div>

    <div class="control-group">
        <label class="control-label" >State*</label>
           <select name="state" id="state" required  style="margin-left: 19px;">
             <?php   foreach ($company as $com) { $fetchstate=$com->state;}?>
	            <?php foreach($states as $st){?>
                  <option value='<?php echo $st->state_abbr;?>' <?php if($fetchstate == $st->state_abbr){echo 'SELECTED';}?>><?php echo $st->state_name;?></option>
                <?php  } ?>
           </select>
     </div>

    <div class="control-group">
	    <label class="control-label">Zip*</label>
	    <div class="controls">
	<input type="text" id="zip" name="zip" class="span4" value="<?php if(isset($this->validation->zip)) echo $this->validation->zip; else echo ''; ?>" onchange="IsValidZip(this.form.zip.value)" required>
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
