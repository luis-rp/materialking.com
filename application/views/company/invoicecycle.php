<script type="text/javascript">
$.noConflict();
 </script>

<script type="text/javascript">


$(document).ready(function(){
	//$('.date').datepicker({dateFormat:'mm/dd/yy'});
	$(".daterequested").datepicker();
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

function preloadoptions(fromid)
	 {
	 	//alert("#smodal"+fromid);
    	$("#smodal"+fromid).modal();   	   
     }



</script>
    <div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
			<h3>Invoice Cycle</h3>		
		</div>		
	   <div id="container">
		
		<div class="row">
			<div class="col-md-12">
				<div class="grid simple ">
										
					<div class="grid-body no-border">
						<form id="profileform" name="profileform" class="animated fadeIn" method="post" 
								action="<?php echo site_url('company/saveinvoicecycle');?>">
						<table id="datatable" class="table no-more-tables general">
						<?php
						if(isset($admins) && count($admins) > 0)
						{ ?>
							<tr>
								<th>&nbsp;</th>
								<th>Company</th>						
								<th>discount(%)</th>
								<th>discount date</th>
								<th>penalty(%)</th>
								<th>Due Date</th>
								<th>Term</th>								
							</tr>
							<?php 
							foreach($admins as $admin)
							{								
							?>
							<tr>
								<td><a class="remove" href="<?php echo site_url('company/deleteinvoicecycle/'.$admin->purchasingadmin);?>" onclick="javascript:return confirm('Do You Really Want to Delete This Company?');">X</a></td>	
								<td><?php echo $admin->purchasingcompany;?></td>		
								
								<td><input  class="form-control" type="text" name="discount_percent[<?php echo $admin->purchasingadmin;?>]" 
									value="<?php echo @$admin->discount_percent;?>" />								
								</td>
								
								<td><input  class="form-control daterequested" type="text" name="discountdate[<?php echo $admin->purchasingadmin;?>]" 
									value="<?php echo (@$admin->discountdate=='0000-00-00'||@$admin->discountdate=='')?'':date('m/d/Y',strtotime(@$admin->discountdate));?>" data-date-format="mm/dd/yyyy" />									
								</td>
								
								<td><input  class="form-control" type="text" name="penalty_percent[<?php echo $admin->purchasingadmin;?>]" 
									value="<?php echo @$admin->penalty_percent;?>" />									
								</td>
								
								<td><input  class="form-control daterequested" type="text" name="duedate[<?php echo $admin->purchasingadmin;?>]" 
									value="<?php echo (@$admin->duedate=='0000-00-00'||@$admin->duedate=='')?'':date('m/d/Y',strtotime(@$admin->duedate));?>" data-date-format="mm/dd/yyyy" />									
								</td>
														
								<td>																
								<select class="form-control" name="term[<?php echo $admin->purchasingadmin;?>]"  id="term<?php echo $admin->purchasingadmin;?>" required style="width:100px;">
  <option value="30" <?php if(@$admin->term=='30') {echo 'selected="SELECTED"';}?> >30</option>
  <option value="60" <?php if(@$admin->term=='60') {echo 'selected="SELECTED"';}?> >60</option>
  <option value="90" <?php if(@$admin->term=='90') {echo 'selected="SELECTED"';}?> >90</option>  
</select>								
								</td>			
							</tr>
							<?php } ?>
							<tr>
								
								<td colspan="10" align="right"><input type="submit" value="Save" class="btn btn-primary btn-cons general"></td>
							</tr>
						<?php } else { echo  '<div class="alert alert-info"><a data-dismiss="alert" class="close" href="#"></a><div class="msgBox">No Invoice Due Date Set.</div></div>'; } ?>	
						</table>
						</form>
					</div>
				</div>
			</div>
		</div>
			
	</div>
  </div> 