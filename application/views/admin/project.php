<script type="text/javascript">
<!--
$(document).ready(function(){
	$('#intro').wysihtml5();
	$('#description').wysihtml5();	
	$('#startdate').datepicker()                       //  id with "date-two" will pop up a datepicker
    .on('changeDate', function(){                // when the datechanges
	$('#startdate').datepicker('hide');      // hide the datepicker
  });
  
$('body').on('keydown', '#title', function(e) {
    if (e.which == 9) {
        e.preventDefault();
        editor.composer.element.focus();
        // do your code
    }
});  
  
});
//-->
</script>

        <script src="<?php echo base_url(); ?>templates/admin/js/bootstrap-tour.min.js" type="text/javascript"></script>
<section class="row-fluid" >
	<h3 class="box-header"><?php echo $heading; ?></h3>
	<div class="box">
	<div class="span12">
	
	<?php echo $message; ?>
   <?php echo $this->session->flashdata('message'); ?>
   <form class="form-horizontal" id="form-add-prj" method="post" action="<?php echo $action; ?>"> 
   <input type="hidden" name="id" value="<?php echo $this->validation->id;?>"/>
    <br/>
    
    <div class="control-group">
	    <label class="control-label">Project Name *</label>
	    <div class="controls">
	      <input type="text" id="title" name="title" class="span4" value="<?php echo $this->validation->title; ?>">
	      <?php echo $this->validation->title_error;?>
	    </div>
    </div>
    
    <div class="control-group">
	    <label class="control-label">Description</label>
	    <div class="controls">
	      <textarea id="description" class="span7" rows="6" name="description" ><?php echo $this->validation->description; ?></textarea>
	      <?php echo $this->validation->description_error;?>
	    </div>
    </div>
    
    <div class="control-group">
	    <label class="control-label">Address</label>
	    <div class="controls">
	      <textarea id="address" class="span7" rows="6" name="address" ><?php echo $this->validation->address; ?></textarea>
	      <?php echo $this->validation->address_error;?>
	    </div>
    </div>
    
    <div class="control-group">
	    <label class="control-label">Start Date *</label>
	    <div class="controls">
	      <input type="text" id="startdate" name="startdate" class="span3" 
	      data-date-format="mm/dd/yyyy"
	      value="<?php if($this->validation->startdate){ echo date("m/d/Y", strtotime($this->validation->startdate)); }else{ echo date("m/d/Y");} ?>">
	      <?php echo $this->validation->startdate_error;?>
	    </div>
    </div>
    <div style="margin-bottom: 50px;">*Please be sure to fill out the Project Information and click Save*</div>
    <div class="control-group">
    <label class="control-label">&nbsp;</label>
    <div class="controls">
     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <input name="add" type="submit" class="btn btn-primary" id="step6" value="Save Project"/>
    </div>
    </div>
    
  </form>
    
    </div>
    </div>
</section>
