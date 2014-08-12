<section class="row-fluid">
	<?php echo $this->session->flashdata('message');?>
	<h3 class="box-header"><i class="icon-cog"></i>Application Settings</h3>
	<div class="box">
	<div class="span12">
	
   <form class="form-horizontal" method="post" action="<?php echo $action; ?>" enctype="multipart/form-data"> 
   <input type="hidden" name="id" value="<?php echo $this->validation->id;?>"/>
   <input type="hidden" name="id" value="1"/>
    <br/>
    
    <div class="control-group">
    <label class="control-label" for="taxrate">Tax Rate</label>
    <div class="controls">
     <input type="text" id="taxrate" name="taxrate" class="span2" value="<?php echo $this->validation->taxrate;?>"> %
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label" for="pricedays">Price Trend Days</label>
    <div class="controls">
     <input type="text" id="pricedays" name="pricedays" class="span2" value="<?php echo $this->validation->pricedays;?>">
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label" for="pricepercent">Target Price</label>
    <div class="controls">
     <input type="text" id="pricepercent" name="pricepercent" class="span2" value="<?php echo $this->validation->pricepercent;?>"> %
    </div>
    </div>
    
  
    <div class="control-group">
    <label class="control-label" for="adminemail">Admin Email</label>
    <div class="controls">
     <input type="email" required id="adminemail" class="span3" name="adminemail" value="<?php echo $this->validation->adminemail;?>">
    </div>
    </div>
    
     <div class="control-group">
    <label class="control-label">Tick Checkbox to Enable Tour</label>
    <div class="controls">
     <?php  $is_checked = ($this->validation->tour) ? $this->validation->tour : $this->input->post('tour');
                $tour = array('id' => 'tour','checked' => ($is_checked == '1') ? true : false,'name' => 'tour','value' => $is_checked,);?>
     <?php echo form_checkbox($tour); ?>
    </div>
    </div>
    
    
    <div class="control-group">
    <label class="control-label">&nbsp;</label>
    <div class="controls">
     <input name="add" type="submit" class="btn btn-primary" value="Update"/>
    </div>
    </div>
    
  </form>
    
    </div>
    </div>
</section>
