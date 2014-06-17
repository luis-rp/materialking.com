<script type="text/javascript">
<!--
$(document).ready(function(){
	$('#manufacturerdate').datepicker();
});
//-->
</script>

<section class="row-fluid">
	<h3 class="box-header"><?php echo $heading; ?></h3>
	<div class="box">
	<div class="span12">
	
	<?php echo @$message; ?>
   <?php echo $this->session->flashdata('message'); ?>

   <form class="form-horizontal" method="post" action="<?php echo $action; ?>"> 
   <input type="hidden" name="id" value="<?php echo $this->validation->id;?>"/>
    <br/>
    
    <div class="control-group">
    <label class="control-label">Manufacturer:</label>
    <div class="controls">
      <input type="text" id="title" name="title" class="span3" value="<?php echo $this->validation->title; ?>">
      <?php echo $this->validation->title_error;?>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">&nbsp;</label>
    <div class="controls">
     <input name="add" type="submit" class="btn btn-primary" value="Update Manufacturer"/>
    </div>
    </div>
    
  </form>
    
    </div>
    </div>
</section>
