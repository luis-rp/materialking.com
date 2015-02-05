
<script type="text/javascript" charset="utf-8">
$(document).ready(function(){
   $("#phone").mask("(999) 999-9999");
   $("#fax").mask("(999) 999-9999");
   $('#about').wysihtml5();
});

function showEmailForm()
{
	$("#newemail").val('');
	$("#addEmailModal").modal();
}

function addEmail()
{
	var email = $("#newemail").val();
	var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (!filter.test(email)) 
	{
		alert("Please enter valid email address");
		return false;
	}
	if(email)
	{
		var li = '<li style="padding-top: 5px;padding-left:0px">';
	  	li += '<input type="text" size="28" name="emails[]" value="'+email+'"> ';
	  	li += '<button class="btn btn-danger btn-sm btn-small" style="margin-top: 3px;" onclick="$(this).closest(\'li\').remove()" type="button"><i class="fa fa-times-circle"></i>&nbsp;</button>';
	  	li += '</li>';
		var lis = $("#emaillist").html();
		
		$("#emaillist").html(lis + li);
		$("#addEmailModal").modal('hide');
	}
}
                        
</script>



<section class="row-fluid">
<?php echo $this->session->flashdata("message"); ?>
  <h3 class="box-header"><i class="icon-picture"></i>Bank Account Settings</h3>
    <div class="box">
      <div class="span12">
      
		<!--<div class="row">
                    <div class="col-md-12">
                        <div class="grid simple ">-->
                          <!--  <div class="grid-title no-border">
                                <h4>&nbsp;</h4>-->
                                <div class="tools">	<a href="javascript:;" class="collapse"></a>
									<a href="#grid-config" data-toggle="modal" class="config"></a>
									<a href="javascript:;" class="reload"></a>
									<a href="javascript:;" class="remove"></a>
                                </div>
                            <!--</div>-->
                            
                          <!--  <div class="grid-body no-border">
                            	<div class="row">-->
                    				<form id="profileform" name="profileform" class="form-horizontal" method="post" action="<?php echo site_url('admin/admin/savebankaccount');?>" enctype="multipart/form-data">
				                    				                    
				                  <!--  <div class="col-md-8 col-sm-8 col-xs-8">-->
                    				  <div class="control-group">
				                        <label class="control-label">Bank Name</label>
				                        <div class="controls">
				                          <input type="text" class="span4" name="bankname" value="<?php echo $bankaccount->bankname;?>" required>
				                        </div>
				                      </div>
                    			<!--	</div>-->
                    					                    
				                  <!--  <div class="col-md-8 col-sm-8 col-xs-8">-->
                    				  <div class="control-group">
				                        <label class="control-label">Account Number</label>
				                        <div class="controls">
				                          <input type="text" class="span4" name="accountnumber" value="<?php echo $bankaccount->accountnumber;?>" required>
				                        </div>
				                      </div>
                    				<!--</div>-->
                    					                    
				                   <!-- <div class="col-md-8 col-sm-8 col-xs-8">-->
                    				  <div class="control-group">
				                        <label class="control-label">Routing Number</label>
				                        <div class="controls">
				                          <input type="text" class="span4" name="routingnumber" value="<?php echo $bankaccount->routingnumber;?>" required>
				                        </div>
				                      </div>
                    				<!--</div>-->
                    				
                    				<!--<div class="col-md-8 col-sm-8 col-xs-8">	-->
				                      <div class="control-group">
				                        <label class="control-label"></label>
				                        <div class="controls">
				                          <input type="submit" value="Save" class="btn btn-primary btn-cons general">
				                        </div>
				                      </div>
                    				<!--</div>
                    				-->
                    				</form>
                    			</div>
                            </div>
                       <!-- </div>
                    </div>
                </div>
     </div>
</div>-->


				<div class="modal fade" id="addEmailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                       
                        <div class="modal-body" style="background-color:#FFFFFF;">
                          <div class="row form-row">
                            <div class="col-md-8">
                             <label class="form-label text-success semi-bold general">Company Email:</label>
                               <input type="text" class="form-control" id="newemail" />
                            </div>
                          </div>
                         
                        </div>
                        <div class="modal-footer">
                          <input type="button" class="btn btn-primary" onclick="addEmail()" value="Save"/>
                          <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                          
                        </div>
                      </div>
                    </div>
                  </div>
</section>






<!--<script src="<?php echo base_url();?>templates/front/assets/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>templates/front/assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>
<link href="<?php echo base_url();?>templates/front/assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css"/>-->



   
	  
	  
	  