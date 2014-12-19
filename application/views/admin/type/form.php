
<script type="text/javascript">
<!--
$(document).ready(function(){
	
	<?php if($this->validation->category =='Manufacturer') {?>	
	$('#industrydiv').css('display','block');
	<?php } ?>
	
});
//-->

function hideviewindustry(val){
	
	if(val == 'Manufacturer')	
	$('#industrydiv').css('display','block');
	else
	$('#industrydiv').css('display','none');
}

</script>

<section class="row-fluid">
	<h3 class="box-header mainheading"><?php echo $heading; ?></h3>
	<div class="box">
	<div class="span12">
	
	  <?php if(@$message){echo '<div class="alert alert-block alert-danger fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>'.$message.'</div>';}?>
	  <?php echo $this->session->flashdata('message'); ?>

   <form class="form-horizontal" method="post" enctype="multipart/form-data" action="<?php echo $action; ?>"> 
   <input type="hidden" name="id" value="<?php echo $this->validation->id;?>"/>
    <br/>
    
    <div class="control-group">
    <label class="control-label">Type:</label>
    <div class="controls">
      <input type="text" id="title" name="title" class="span4" value="<?php echo $this->validation->title; ?>">
      <?php echo $this->validation->title_error;?>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">Category</label>
    <div class="controls">
      <select id="category" name="category" onchange="hideviewindustry(this.value);">
      	<option value="Industry" <?php if($this->validation->category=='Industry'){echo 'SELECTED';}?>>
      		Industry
      	</option>
      	<option value="Manufacturer" <?php if($this->validation->category=='Manufacturer'){echo 'SELECTED';}?>>
      		Manufacturer
      	</option>
      </select>
    </div>
    </div>
    
    
    <div id="industrydiv" class="control-group" style="display:none;">
    <label class="control-label">Industry</label>
    <div class="controls">
      <select id="parent_id" name="parent_id">
      	<option value="">Select Industry</option>
      	<?php foreach($types as $type){?>
	    <option value="<?php echo $type->id;?>" <?php if($this->validation->parent_id == $type->id){echo 'SELECTED';}?>><?php echo $type->title;?></option>
	    <?php }?>
     </select>
    </div>
    </div>
    
    
    <div class="control-group">
    <label class="control-label">Type:</label>
    <div class="controls">
        <input type="file" name="image" size="20"  />
        <?php if($this->validation->image){?>
            <img src="<?php echo site_url('uploads/type/thumbs/'. @$this->validation->image);?>"/>
        <?php }?>
        <?php echo $this->validation->image_error;?>
    </div>
    </div>
    
   
    <div class="control-group">
    <label class="control-label">&nbsp;</label>
    <div class="controls">
     <input type="submit" class="btn btn-primary" value="Update"/>
    </div>
    </div>
    
  </form>
    
    </div>
    </div>
</section>