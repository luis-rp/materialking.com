<script type="text/javascript" src='http://maps.google.com/maps/api/js?sensor=false&libraries=places'></script>
<script src="<?php echo base_url(); ?>templates/front/js/locationpicker.jquery.js" type="text/javascript"></script> 

<script type="text/javascript">
<!--
$(document).ready(function(){
	$('#intro').wysihtml5();
	$('#content').wysihtml5();
	$('#companydate').datepicker();
});
//-->
</script>


<script>
  $(document).ready(function()
  {
    $("#password").val("");
  });
  
  	   
function checkEnter(event)
{ 
	if (event.keyCode == 13) 
   {
       return false;
    }
}
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

    <?php if(isset($this->validation->com_lat) && $this->validation->com_lat!="") {
				         	$lat=$this->validation->com_lat;
				         }
				         else {
				         	$lat=34.167139;
				         }
				         
				         if(isset($this->validation->com_lng) && $this->validation->com_lng!="") {
				         	$lang=$this->validation->com_lng;
				         }
				         else {
				         	$lang=-118.434677;
				         } ?>
   <div class="control-group">
	    <label class="control-label">Address</label>
	    <div class="controls">
	      
	      	<span>Start typing an address and select from the dropdown.</span> <br />               
              <input type="text" id="address" name="address" class="span5"  autocomplete="off" onkeydown="return checkEnter(event);" >            					<div id="map-container">
						 <div id="map-canvas"></div>

							<script>
                                $('#map-canvas').locationpicker({
                                location: {latitude:<?php echo $lat; ?>, longitude:<?php echo $lang; ?>},	
                                radius: 600,
                                inputBinding: {
                                    latitudeInput: $('#latitude'),
                                    longitudeInput: $('#longitude'),
                                    locationNameInput: $('#address')        
                                },
                                enableAutocomplete: true,                              
                                });
							</script>     
                   </div>         
	    </div>
	    <?php echo $this->validation->address_error;?>
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
    <label class="control-label">Make it Premium</label>
    <div class="controls">
     <input type="checkbox" name="company_type" value="<?php echo $this->validation->company_type; ?>"
    <?php echo @$this->validation->company_type==1?'checked="checked"':'';?>>
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
