<script type="text/javascript">
<!--
$(document).ready(function(){
	$('#costcodedate').datepicker();
});
//-->

function changeparent(projectid){
	
	$. ajax ({
					type: "POST",					
					data: {"projectid" : projectid},
					url: "getcostcodefromproject",
					success: function (data) {
						if(data){
							$('#parent').empty();		
							$('#parent').append( new Option("Top Parent","") ); 				
							$('#parent').append(data);							
							$('#parent').val('');
						}
					},
					error: function(x,y,z){
						alert('An error has occurred:\n' + x + '\n' + y + '\n' + z);
					}
				});
	
}

</script>
        <script src="<?php echo base_url(); ?>templates/admin/js/bootstrap-tour.min.js" type="text/javascript"></script>
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
    <label class="control-label">Select Parent</label>
    <div class="controls">
      <select id="parent" name="parent">
      	<option value="0">Top Parent</option>
      	<?php echo $parentcombooptions;?>
	  </select>
      <?php echo $this->validation->parent_error;?>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">Select Project</label>
    <div class="controls">
      <select id="project" name="project" onchange="changeparent(this.value);">
      	<?php foreach($projects as $p){?>
      	<option value="<?php echo $p->id;?>" <?php if(isset($parents[0]->project)) { if($p->id==$parents[0]->project){echo 'SELECTED';} } ?>>
      		<?php echo $p->title;?>
      	</option>
      	<?php }?>
	  </select>
      <?php echo $this->validation->parent_error;?>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">Cost Code *:</label>
    <div class="controls">
      <input type="text" id="code" name="code" class="span3" value="<?php echo $this->validation->code; ?>">
      <?php echo $this->validation->code_error;?>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">Budget *:</label>
    <div class="controls">
      $ &nbsp;<input type="text" id="cost" name="cost" class="span2" 
      onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');"
      value="<?php echo $this->validation->cost; ?>">
      <?php echo $this->validation->cost_error;?>
    </div>
    </div>
    
     <div class="control-group">
    <label class="control-label">Description:</label>
    <div class="controls">
      <textarea rows="4" class="span4" id="cdetail" name="cdetail"><?php echo $this->validation->cdetail; ?></textarea>
      <?php echo $this->validation->cdetail_error;?>
    </div>
    </div>
    
    
    <div class="control-group">
    <label class="control-label">&nbsp;</label>
    <div class="controls">
     <input name="add" type="submit" class="btn btn-primary" value="Update Costcode" id="step11"/>
    </div>
    </div>
    
  </form>
    
    </div>
    </div>
</section>
