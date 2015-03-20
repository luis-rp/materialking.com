<?php echo '<script type="text/javascript">var getprojectfromcostcodeurl ="'.site_url('admin/costcode/getprojectfromcostcode').'";</script>'?>
<?php echo '<script type="text/javascript">var getcostcodefromprojecturl ="'.site_url('admin/costcode/getcostcodefromproject').'";</script>'?>
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
					url: getcostcodefromprojecturl,
					success: function (data) {
						if(data){
							$('#changecost').hide();
							$('#changeajaxcost').show();
							$('#changeajaxcosttr').html(data);
							$('#parent').empty();		
							$('#parent').append( new Option("Top Parent","") ); 				
							$('#parent').append(data);														
							$('#parent').val('');
						} else 
						{
							$('#changecost').hide();
							$('#changeajaxcost').show();
							$('#changeajaxcosttr').html("<p>No Cost Code is Present</p>");
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

function changeproject(catid){
	$. ajax ({
		
					type: "POST",					
					data: {"catid" : catid},
					url: getprojectfromcostcodeurl,
					success: function (data) {
						if(data){							
							$('#project').empty();		
						//	$('#project').append( new Option("Select Project","") ); 				
							$('#project').append(data);														
							$('#project').val('');
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
	<h3 class="box-header"><?php echo $heading; ?> &nbsp;&nbsp; <a href="<?php echo base_url();?>admin/costcode" class="btn btn-green"> Back </a></h3>
	<div class="box">
	<div class="span12">
	<div class="pull-left" style="width:70%;">
	
	<?php echo @$message; ?>
   <?php echo $this->session->flashdata('message'); ?>
   <span style="font-weight:bold;">Note: * Fields Are Mandatory</span> 
   <form class="form-horizontal" method="post" action="<?php echo $action; ?>" enctype="multipart/form-data"> 
   <input type="hidden" name="id" value="<?php echo $this->validation->id;?>"/>
    <br/>
    
    <div class="control-group">
    <label class="control-label">Select Parent</label>
    <div class="controls">
      <select id="parent" name="parent" onchange="changeproject(this.value);">
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
      	<option value="<?php echo $p->id;?>" <?php if(isset($parents)) { if($p->id==$parents){?>selected<?php } } ?>>
      		<?php echo $p->title;?>
      	</option>
      	<?php }?>
	  </select>
      <?php echo $this->validation->parent_error;?>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">Cost Code <span style="color:red;font-weight:bold;"> * </span>:</label>
    <div class="controls">
      <input type="text" id="code" name="code" class="span3" value="<?php echo $this->validation->code; ?>">
      <?php echo $this->validation->code_error;?>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">Contract Cost Code:</label>
    <div class="controls">
       <input type="checkbox" name="forcontract" id="forcontract" <?php echo @$this->validation->forcontract?'checked="CHECKED"':''?>" />
       <p>*If you plan to sub-contract any work for this cost-code please check the Contract Cost Code box.</p> 
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">Budget <span style="color:red;font-weight:bold;"> * </span>:</label>
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
    <label class="control-label">Turn Off Estimated Cost:</label>
    <div class="controls">
       <input type="checkbox" name="estimate" id="estimate" <?php echo @$this->validation->estimate?'checked="CHECKED"':''?>" />
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">Image:</label>
    <div class="controls">
       <input type="file" name="UploadFile" id="UploadFile" />
        <a href="<?php echo site_url('uploads/costcodeimages') . '/' . @$this->validation->costcode_image; ?>" target="_blank"> 
          <?php echo @$this->validation->costcode_image; ?>
        </a> 
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">&nbsp;</label>
    <div class="controls">
     <input name="add" type="submit" class="btn btn-primary" value="Update Costcode" id="step11"/>
    </div>
    </div>
    
  </form>
      
         </div><!-- End of Pull left -->
    <?php if(isset($costcodesdata) && count($costcodesdata) > 0) { ?>
	   <div class="pull-right" style="width:26%;">
		   <div class="table-responsive">
			   <h3>Existing Cost Codes</h3>
				  <table id="changecost" class="table table-hover">
				  <tr><th>Cost Code Name</th></tr>
				    <?php foreach ($costcodesdata as $costcode) { ?>
				  		<tr><td ><?php echo $costcode->code; ?></td></tr>
				     <?php } ?>
				  </table>
				   <table id="changeajaxcost" class="table table-hover" style="display:none;">
				        <tr><th>Cost Code Name</th></tr>				    
				  		<tr id="changeajaxcosttr"></tr>				     
				  </table>
			</div>
	   </div><!-- End of Pull right -->
   <?php } ?>
	   
	   <div style="clear:both;"></div> 
  
    </div>
    </div>
</section>
