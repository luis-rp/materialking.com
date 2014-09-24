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
  <?php  if(isset($message)) echo $message; else echo ''; ?>
		<div class="page-title">	
		
			<h3>Form Builder</h3>		
		</div>	
  <div id="container">
  	<div class="row">
  	  <div class="col-md-12">
         <div class="grid simple ">
            <div class="grid-title no-border">
            
            
            </div>

                  <div class="grid-body no-border">
                   <div class="row">
            			<div><a href="<?php echo base_url() . 'company/createformnetwork';?>"><input type="button"  class="btn btn-primary" value="Manage Network Request Requirements"></a></div>
            			<br><br><br><br><br>
						<div><a href="<?php echo base_url() . 'company/createformsubscriptions';?>"><input type="button"  class="btn btn-primary" value="Manage Newsletter Request Requirements"></a></div>
                 </div>
              </div>
          </div>
  		</div>
	</div>