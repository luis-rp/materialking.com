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

function preloadoptions(fromid)
	 {
	 	//alert("#smodal"+fromid);
    	$("#smodal"+fromid).modal();   	   
     }



</script>
    <div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
			<h3>Network Connections</h3>		
		</div>		
	   <div id="container">
		
		<div class="row">
			<div class="col-md-12">
				<div class="grid simple ">
										
					<div class="grid-body no-border">
						<form id="profileform" name="profileform" class="animated fadeIn" method="post" 
								action="<?php echo site_url('company/savepurchasingtier');?>">
						<table id="datatable" class="table no-more-tables general">
						<?php
						if(isset($admins) && count($admins) > 0)
						{ ?>
							<tr>
								<th>&nbsp;</th>
								<th>Company</th>
								<th>Contact</th>
								<th>Tier</th>
								<th>Credit limit</th>
								<!--<th>Start Date</th>
								<th>End Date</th>-->
								<th>Credit remaining</th>
								<th>Amount Due</th>
								<th>Credit Card Only</th>
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
								<td><?php echo $admin->purchasingcompany;?><br />
								<a href="javascript:void(0)" onclick="preloadoptions('<?php echo htmlentities($admin->purchasingadmin)?>');">View Stats</a>
								</td>
								<td><?php echo $admin->purchasingfullname;?><br />								
								<a href="<?php echo site_url('dashboard/creditapplication/'.$admin->purchasingadmin);?>" target="_blank">
								<span>View Credit App.</span></a><br />
								<a href="<?php echo site_url('report/index/0/'.$admin->purchasingadmin);?>" target="_blank">
								<span>Run Report.</span></a> <br />
								<a href="<?php echo site_url('quote/invoices/'.$admin->purchasingadmin);?>" target="_blank">
								<span>View Invoices</span></a>
								</td>
								<td>
																
								<select class="form-control" onchange="changetier(this.value,'<?php echo $admin->tier;?>','tier<?php echo $admin->purchasingadmin;?>');" name="tier[<?php echo $admin->purchasingadmin;?>]" id="tier<?php echo $admin->purchasingadmin;?>" required style="width:100px;">
  <option name="tier[<?php echo $admin->purchasingadmin;?>]" value="tier0" <?php if($admin->tier=='tier0') {echo 'selected="SELECTED"';}?> >Tier 0</option>
  <option name="tier[<?php echo $admin->purchasingadmin;?>]" value="tier1" <?php if($admin->tier=='tier1') {echo 'selected="SELECTED"';}?> >Tier 1</option>
  <option name="tier[<?php echo $admin->purchasingadmin;?>]" value="tier2" <?php if($admin->tier=='tier2') {echo 'selected="SELECTED"';}?> >Tier 2</option>
  <option name="tier[<?php echo $admin->purchasingadmin;?>]" value="tier3" <?php if($admin->tier=='tier3') {echo 'selected="SELECTED"';}?> >Tier 3</option>
  <option name="tier[<?php echo $admin->purchasingadmin;?>]" value="tier4" <?php if($admin->tier=='tier4') {echo 'selected="SELECTED"';}?> >Tier 4</option>
</select>
								
								</td>
								<td><p <?php if($admin->creditonly==1) { ?>style="display:none;"<?php } ?>>
									<input class="form-control" type="text" name="creditlimit[<?php echo $admin->purchasingadmin;?>]" value="<?php echo $admin->totalcredit;?>" onkeypress="return allowonlydigits(event,'<?php echo $admin->purchasingadmin;?>', 'eaerrmsg<?php echo $admin->purchasingadmin;?>')"  
							 /> 
									<br>&nbsp;<span id="eaerrmsg<?php echo $admin->purchasingadmin;?>"></span></p>	
									<?php if($admin->creditonly==1) { ?><span style="color:red;">*Credit Card </span><?php } ?>
								</td>
								<!--<td><p <?php //if($admin->creditonly==1) { ?>style="display:none;"<?php //} ?>>
									<input  class="form-control" type="text" name="creditfrom[<?php //echo $admin->purchasingadmin;?>]" 
									value="<?php //echo ($admin->creditfrom=='0000-00-00'||$admin->creditfrom=='')?'':date('m/d/Y',strtotime($admin->creditfrom));?>" class="date" data-date-format="mm/dd/yyyy" /></p>
									<?php //if($admin->creditonly==1) { ?><span style="color:red;">Card</span><?php //} ?>
								</td>
								<td><p <?php //if($admin->creditonly==1) { ?>style="display:none;"<?php //} ?>>
									<input class="form-control" type="text" name="creditto[<?php //echo $admin->purchasingadmin;?>]" 
									value="<?php //echo ($admin->creditto=='0000-00-00'||$admin->creditto=='')?'':date('m/d/Y',strtotime($admin->creditto));?>" class="date" data-date-format="mm/dd/yyyy" /></p>
									<?php //if($admin->creditonly==1) { ?><span style="color:red;">only</span><?php //} ?>
								</td>-->
								<td><p <?php if($admin->creditonly==1) { ?>style="display:none;"<?php } ?>>
									<?php echo $admin->creditlimit;?></p><?php if($admin->creditonly==1) { ?><span style="color:red;">Only Account</span><?php } ?>
								</td>								
								<td>
									<?php echo $admin->amountdue;?>
								</td>
								<td>					
<input type="checkbox" name="creditonly[<?php echo $admin->purchasingadmin;?>]" <?php if($admin->creditonly==1) {?> checked="CHECKED" <?php } ?> />
								</td>
							</tr>
							<?php } ?>
							<tr>
								
								<td colspan="10" align="right"><input type="submit" value="Save" class="btn btn-primary btn-cons general"></td>
							</tr>
						<?php } else { echo  '<div class="alert alert-info"><a data-dismiss="alert" class="close" href="#"></a><div class="msgBox">No Network Connections.</div></div>'; } ?>	
						</table>
						</form>
					</div>
				</div>
			</div>
		</div>
			
	</div>
  </div> 
  <?php //echo "<pre>"; print_r($admins); die;?>
   <?php $oldfromid=""; $i=0; foreach ($admins as $admin){ //echo "<pre>data-"; print_r($u->id);  ?>
 <div id="smodal<?php echo $admin->purchasingadmin;?>" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <i class="icon-credit-card icon-7x"></i>
          <h4 class="semi-bold" id="myModalLabel" style="text-align:left"><?php echo @$admin->purchasingcompany;?></h4> 
          <h5 class="semi-bold" id="myModalLabel" style="text-align:left"><?php echo @$admin->purchasingfullname;?></h5>
          <h6 class="semi-bold" id="myModalLabel" style="text-align:left"><?php echo @$admin->purchasingaddress;?></h6>       
        </div>
        
        <div class="modal-body">       
        	<div>
          		<h6 class="semi-bold" id="myModalLabel" style="text-align:center">Member Since&nbsp;
          		<?php $olddate1=strtotime(@$admin->purchasingregdate); $newdate1 = date('M d, Y', $olddate1); echo $newdate1; ?></h6> 
        	</div>
        
         	<div>
        		<h4 class="semi-bold" id="myModalLabel">User Statistics</h4>
        	</div>
        	<hr style="height:2px;border-width:0;color:green;background-color:green">
	        <div style="margin-left:90px;">      
		       <div>
		        	<p><?php echo "Total Number of Project&nbsp;".count(@$admin->pro);?></p>
		        </div> 
		        <div>
		        	<p><?php echo "Total Number of Direct Orders&nbsp;".count(@$admin->directquo);?></p>
		        </div> 
		        <div>
		        	<p><?php echo "Total Number of Quotes&nbsp;".count(@$admin->quo);?></p>
		        </div> 
		        <div>
		        	<p><?php echo "Total Number of Awarded Quotes&nbsp;".@$admin->awar;?></p>
		        </div>  
	        </div>	  
        </div>       
        <div class="modal-footer">
          <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
        </div>
        
      </div>   
    </div>
  </div>
  <?php $oldfrommid=$admin->purchasingadmin; $i++; } //die;?> 