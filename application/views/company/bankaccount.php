<script src="<?php echo base_url();?>templates/front/assets/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>templates/front/assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>
<link href="<?php echo base_url();?>templates/front/assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css"/>

<?php echo '<script>var pwdurl="'.site_url('company/checkpwd').'";</script>'?>

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

function showpwdfield()
{
   $("#pwd").show();
}

function checkpwd(pwd)
    {
        var data = "pwd="+pwd;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: pwdurl
		    }).done(function(data){
		    	if(data == 1){
		    		$("#message").html("<font color='green'> Correct Password. </font>");
		    		$("#enableaccountnumber").css('display','block'); 
		    		$('#enableaccountnumber').prop('required',true); 
		    		$("#disableaccountnumber").css('display','none');
		    		$("#disableaccountnumber").removeAttr('required');
		    		
		    		$("#enableroutingnumber").css('display','block'); 
		    		$('#enableroutingnumber').prop('required',true);
		    		$("#disableroutingnumber").css('display','none');
		    		$("#disableroutingnumber").removeAttr('required');
		    		
		    		$("#passwordDiv").css('display','none');
		    		
		    		$("#btnSave").removeAttr('disabled');
		    		
		    		$("#bankname").removeAttr('disabled');
		    		
		    		return true;   				 
    			}
    			else
    			{
    				$("#message").html("<font color='red'>Wrong Password. </font>");						
    				return false; 
    				
    			}

		    });
    }
                        
</script>

    <div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
			<h3>Bank Account Settings</h3>		
		</div>		
	   <div id="container">

	   <?php if(isset($bankaccount->accountnumber) && $bankaccount->accountnumber!="") {  
	   	     $acclen=strlen($bankaccount->accountnumber);
		     $acclen=$acclen-4; 
		     $accstr=substr_replace($bankaccount->accountnumber,str_repeat("X",$acclen),0,-4); } 
		     
		     if(isset($bankaccount->routingnumber) && $bankaccount->routingnumber!="") { 
		     $routlen=strlen($bankaccount->routingnumber);
			 $routlen=$routlen-3; 
			 $routstr=substr_replace($bankaccount->routingnumber,str_repeat("X",$routlen),0,-3);  }   ?>
				                                             				                          						
		<div class="row">
                    <div class="col-md-12">
                        <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                                <div class="tools">	<a href="javascript:;" class="collapse"></a>
									<a href="#grid-config" data-toggle="modal" class="config"></a>
									<a href="javascript:;" class="reload"></a>
									<a href="javascript:;" class="remove"></a>
                                </div>
                            </div>
                            
                            <div class="grid-body no-border">
                            	<div class="row">
                    				<form id="profileform" name="profileform" class="animated fadeIn" method="post" action="<?php echo site_url('company/savebankaccount');?>" enctype="multipart/form-data">
				                    				                    
				                    <div class="col-md-8 col-sm-8 col-xs-8">
                    				  <div class="form-group">
				                        <label class="form-label">Bank Name</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="bankname" id="bankname"
												value="<?php if(isset($bankaccount->bankname)) { echo $bankaccount->bankname; }?>" required
												<?php if(isset($bankaccount->bankname)) { ?> disabled <?php } ?>>
				                        </div>
				                      </div>
                    				</div>
                    					                    
				                    <div class="col-md-8 col-sm-8 col-xs-8">
                    				  <div class="form-group">
				                        <label class="form-label">Account Number</label>
				                        <div class="controls">
				                          <input type="text" id="disableaccountnumber" class="form-control" name="disableaccountnumber" 
												value="<?php if(isset($accstr)) { echo $accstr; } ?>" required
												<?php if(isset($accstr)) { ?> disabled <?php } ?>>
				                          
				                         <input type="text"  id="enableaccountnumber" class="form-control" name="enableaccountnumber" style="display:none;"
				                          		value="<?php if(isset($bankaccount->accountnumber)) { echo $bankaccount->accountnumber; } ?>">
				                        </div>
				                      </div>
                    				</div>
                    					                    
				                    <div class="col-md-8 col-sm-8 col-xs-8">
                    				  <div class="form-group">
				                        <label class="form-label">Routing Number</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" id="disableroutingnumber" name="disableroutingnumber" 
				                          		 value="<?php if(isset($routstr)) {  echo $routstr;} ?>" required
				                          		 <?php if(isset($routstr)) { ?> disabled <?php } ?>>
				                                             			                          
				                           <input type="text" class="form-control" id="enableroutingnumber" name="enableroutingnumber"  style="display:none;"
				                                  value="<?php if(isset($bankaccount->routingnumber)) {  echo $bankaccount->routingnumber; } ?>">
				                        </div>
				                      </div>
                    				</div>
                    				
                    				<div class="col-md-8 col-sm-8 col-xs-8">	
				                      <div class="form-group">
				                        <label class="form-label"></label>
				                        <div class="controls">
				                          <input type="submit" id="btnSave" value="Save" class="btn btn-primary btn-cons general" 
				                                  <?php if(isset($bankaccount->accountnumber)) { ?> disabled <?php } ?>>
				                        </div>
				                      </div>
                    				</div>
                    			
                    			<div id="passwordDiv">
                    				<div class="col-md-8 col-sm-8 col-xs-8">	
				                      <div class="form-group">
				                        <label class="form-label"></label>
				                        <div class="controls">
				                          <input type="button" value="Change Bank Setting" class="btn btn-primary btn-cons general" onclick="showpwdfield()"
											<?php if(!isset($bankaccount->accountnumber)) { ?> disabled <?php } ?>>
				                        </div>
				                      </div>
                    				</div>
                    				
                    			<div class="col-md-8 col-sm-8 col-xs-8" id="pwd" style="display:none;">
                    				  <div class="form-group">
				                        <label class="form-label">Please Enter Your Login Password & Press <mark>Enter</mark></label>
				                        <div class="controls">
				                          <input type="password" class="form-control" name="loginpwd" onchange="checkpwd(this.value)">
				                        </div>
				                        <div class="controls" id="message"></div>				                          
				                        
				                      </div>
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