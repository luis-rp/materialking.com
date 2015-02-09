<script type="text/javascript">
$.noConflict();
 </script>

<script type="text/javascript">


$(document).ready(function(){
	$('.date').datepicker({dateFormat:'mm/dd/yy'});
});

function changetier(newval,oldval,id){
	
	if(confirm("Are you sure you wish to change company tier level?")){
		$('#' + id).val(newval);
	}else
		$('#' + id).val(oldval);
	
}


function allowonlydigits(e,elementid,errorid){
     //if the letter is not digit then display error and don't type anything
       if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which!=46) {    
        //display error message                
      $("#"+errorid).html("Digits Only").show().fadeOut("slow");  
      $("#"+errorid).css('color','red');
      return false;
    }

}



</script>
    <div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
			<h3>Tier Price Settings</h3>		
		</div>		
	   <div id="container">
		
		<div class="row">
			<div class="col-md-12">
				<div class="grid simple ">
					<!--<div class="grid-title no-border">
						<h4>Tier Price Settings</h4>
						
					</div>-->
					
					<div class="grid-body no-border">
						<div class="row">
							<div class="col-md-8 col-sm-8 col-xs-8">
							<form id="profileform" name="profileform" class="animated fadeIn" method="post" 
								action="<?php echo site_url('company/savetier');?>">
							  
							  <div class="form-group">
								<label class="form-label">Tier 0</label>
								<div class="controls">
								  <input type="text" class="form-control" name="tier0" value="0" readonly>
								</div>
							  </div>
							  
							  <div class="form-group">
								<label class="form-label">Tier 1</label>
								<div class="controls">
								  <input type="text" class="form-control" name="tier1" value="<?php echo $tier->tier1;?>" required>
								</div>
							  </div>
							  
							  <div class="form-group">
								<label class="form-label">Tier 2</label>
								<div class="controls">
								  <input type="text" class="form-control" name="tier2" value="<?php echo $tier->tier2;?>" required>
								</div>
							  </div>
							  
							  <div class="form-group">
								<label class="form-label">Tier 3</label>
								<div class="controls">
								  <input type="text" class="form-control" name="tier3" value="<?php echo $tier->tier3;?>" required>
								</div>
							  </div>
							  
							  <div class="form-group">
								<label class="form-label">Tier 4</label>
								<div class="controls">
								  <input type="text" class="form-control" name="tier4" value="<?php echo $tier->tier4;?>" required>
								</div>
							  </div>
							
							  <div class="form-group">
								<label class="form-label"></label>
								<div class="controls">
								  <input type="submit" value="Save" class="btn btn-primary btn-cons general">
								</div>
							  </div>
							
							</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
						
	</div>
  </div> 