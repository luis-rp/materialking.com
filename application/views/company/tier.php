<script type="text/javascript">
$.noConflict();
 </script>

<script>
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
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
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
			<h3>Tier Pricing</h3>		
		</div>		
	   <div id="container">
		
		<div class="row">
			<div class="col-md-12">
				<div class="grid simple ">
					<div class="grid-title no-border">
						<h4>Tier Price Settings</h4>
						
					</div>
					
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
		
		<div class="row">
			<div class="col-md-12">
				<div class="grid simple ">
					<div class="grid-title no-border">
						<h4>Tier Pricing for Purchasing Company</h4>
						
					</div>
					
					<div class="grid-body no-border">
						<form id="profileform" name="profileform" class="animated fadeIn" method="post" 
								action="<?php echo site_url('company/savepurchasingtier');?>">
						<table id="datatable" class="table no-more-tables general">
							<tr>
								<th>&nbsp;</th>
								<th>Company</th>
								<th>Contact</th>
								<th>Tier</th>
								<th>Credit limit</th>
								<th>Start Date</th>
								<th>End Date</th>
								<th>Credit remaining</th>
								<th>Amount Due</th>
							</tr>
							<?php 
							foreach($admins as $admin)
							{
								if(!@$admin->tier)
								{
									$admin->tier='tier0';
								}
							?>
							<tr>
								<td><a class="remove" href="<?php echo site_url('company/deletepurchasingtier/'.$admin->purchasingadmin);?>" onclick="javascript:return confirm('Do You Really Want to Delete This Company?');">X</a></td>	
								<td><?php echo $admin->purchasingcompany;?></td>
								<td><?php echo $admin->purchasingfullname;?></td>
								<td>
								
								<!-- <table style="border:none" cellpadding="1" border="0">
								<tr>
								<td style="border:none">Tier 0:</td>
								<td style="border:none"><input type="radio" required name="tier[<?php echo $admin->purchasingadmin;?>]" value="tier0" <?php if($admin->tier=='tier0'){echo 'checked="CHECKED"';}?>/></td>
								</tr>
								
								<tr>
								<td style="border:none">Tier 1:</td>
								<td style="border:none"><input type="radio" required name="tier[<?php echo $admin->purchasingadmin;?>]" value="tier1" <?php if($admin->tier=='tier1'){echo 'checked="CHECKED"';}?>/></td>
								</tr>
								
								<tr>
								<td style="border:none">Tier 2:</td>
								<td style="border:none"><input type="radio" required name="tier[<?php echo $admin->purchasingadmin;?>]" value="tier2" <?php if($admin->tier=='tier2'){echo 'checked="CHECKED"';}?>/></td>
								</tr>
								
								<tr>
								<td style="border:none">Tier 3:</td>
								<td style="border:none"><input type="radio" required name="tier[<?php echo $admin->purchasingadmin;?>]" value="tier3" <?php if($admin->tier=='tier3'){echo 'checked="CHECKED"';}?>/></td>
								</tr>
								
								<tr>
								<td style="border:none">Tier 4:</td>
								<td style="border:none"><input type="radio" required name="tier[<?php echo $admin->purchasingadmin;?>]" value="tier4" <?php if($admin->tier=='tier4'){echo 'checked="CHECKED"';}?>/></td>
								
								</tr>
								</table> -->
								
								<select onchange="changetier(this.value,'<?php echo $admin->tier;?>','tier<?php echo $admin->purchasingadmin;?>');" name="tier[<?php echo $admin->purchasingadmin;?>]" id="tier<?php echo $admin->purchasingadmin;?>" required>
  <option name="tier[<?php echo $admin->purchasingadmin;?>]" value="tier0" <?php if($admin->tier=='tier0') {echo 'selected="SELECTED"';}?> >Tier 0</option>
  <option name="tier[<?php echo $admin->purchasingadmin;?>]" value="tier1" <?php if($admin->tier=='tier1') {echo 'selected="SELECTED"';}?> >Tier 1</option>
  <option name="tier[<?php echo $admin->purchasingadmin;?>]" value="tier2" <?php if($admin->tier=='tier2') {echo 'selected="SELECTED"';}?> >Tier 2</option>
  <option name="tier[<?php echo $admin->purchasingadmin;?>]" value="tier3" <?php if($admin->tier=='tier3') {echo 'selected="SELECTED"';}?> >Tier 3</option>
  <option name="tier[<?php echo $admin->purchasingadmin;?>]" value="tier4" <?php if($admin->tier=='tier4') {echo 'selected="SELECTED"';}?> >Tier 4</option>
</select>
								
								</td>
								
								<td>
									<input style="width:100px" type="text" name="creditlimit[<?php echo $admin->purchasingadmin;?>]" value="<?php echo $admin->totalcredit;?>" onkeypress="return allowonlydigits(event,'<?php echo $admin->purchasingadmin;?>', 'eaerrmsg<?php echo $admin->purchasingadmin;?>')"  /> 
									<br>&nbsp;<span id="eaerrmsg<?php echo $admin->purchasingadmin;?>"></span>	
								</td>
								<td>
									<input  style="width:70px" type="text" name="creditfrom[<?php echo $admin->purchasingadmin;?>]" 
									value="<?php echo ($admin->creditfrom=='0000-00-00'||$admin->creditfrom=='')?'':date('m/d/Y',strtotime($admin->creditfrom));?>" class="date" data-date-format="mm/dd/yyyy"/>
								</td>
								<td>
									<input  style="width:70px" type="text" name="creditto[<?php echo $admin->purchasingadmin;?>]" 
									value="<?php echo ($admin->creditto=='0000-00-00'||$admin->creditto=='')?'':date('m/d/Y',strtotime($admin->creditto));?>" class="date" data-date-format="mm/dd/yyyy"/>
								</td>
								<td>
									<?php echo $admin->creditlimit;?>
								</td>
								<td>
									<?php echo $admin->amountdue;?>
								</td>
							</tr>
							<?php }?>
							<tr>
								
								<td colspan="8" align="right"><input type="submit" value="Save" class="btn btn-primary btn-cons general"></td>
							</tr>
						</table>
						</form>
					</div>
				</div>
			</div>
		</div>
			
	</div>
  </div> 