<script type="text/javascript">
$.noConflict();
 </script>

<script type="text/javascript">
function showOptions(obj)
{
	var fieldType = $(obj).val();

	if(fieldType == 'radio' || fieldType == 'dropdown'|| fieldType == 'checkbox')
	{
		$("#optionDiv").css("display","block");
	}
}
</script>
<div class="content">

  <div class="container">
  	<div class="row">
  	  <div class="col-md-12">
         <div class="grid simple ">
            <div class="grid-title no-border">
              <?php  if(isset($message)) echo $message; else echo ''; ?>
            <h4>Create Form Fields to Create Required Fields for Your Account Network Requests</h4>
            </div>

                  <div class="grid-body no-border">
                   <div class="row">
            		<form  class="animated fadeIn" role="form" method="post" action="<?php echo site_url('company/createformdata');?>">
                     <div class="col-md-6 col-sm-6 col-xs-6">

  						<div class="form-group">
    						<label for="label" class="form-label">Enter Label*</label>
    							<div class="controls">
      							<input type="text" class="form-control" id="label" name="label" placeholder="Enter Label" required>
    							</div>
 					    </div>

   						<div class="form-group">
    						<label for="id" class="form-label">Enter Input Type*</label>
  							<select class="form-control" name="type" id="type" required onchange="showOptions(this);" >
  								<option value="text" selected>Text</option>
  								<option value="password">Password</option>
  								<option value="email">Email</option>
  								<option value="textarea">Textarea</option>
  								<option value="checkbox">Checkbox</option>
  								<option value="radio">Radio</option>
  								<option value="dropdown">Dropdown</option>
							</select>
						</div>
						<div id="optionDiv" style="display:none;">
 						<div class="form-group">
    						<label for="option" class="form-label">Enter Options</label>
   								 <div class="controls">
      							 <input type="text" class="form-control" id="frm_option" name="frm_option" placeholder="Enter Options">
      							 <span class="help-block">Options Only for Checkbox/Radio/Dropdown and Seperated by comma.</span>
    							 </div>
  						</div>
						</div>
 					     <div class="form-group">
				                        <label class="form-label"></label>
				                        <div class="controls">
				                         <input type="submit" value="Create Form Field" class="btn btn-primary btn-lg">&nbsp;&nbsp;
	<a href="<?php echo base_url() . 'company/formview';?>"><input type="button"  class="btn btn-primary" value="View Current Form"></a>
<a href="<?php echo base_url() . 'company/formsubmission';?>"><input type="button"  class="btn btn-primary" value="View Submissions"></a>
				                        </div>
				                      </div>
 						 </div>
					</form>
                 </div>
              </div>
          </div>
  		</div>
	</div>