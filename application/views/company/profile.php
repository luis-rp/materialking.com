<script>
      function countChar(val) 
      {
        var len = val.value.length;
        if (len >= 300) {
          val.value = val.value.substring(0, 300);
        } else {
          $('#charNum').text(300 - len + " Remaning") ;
        }
      };
    </script>
    

<script type="text/javascript" src="<?php echo base_url();?>templates/front/js/ckeditor/ckeditor.js"></script>

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

    <div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
			<h3>Edit Profile</h3>
			<a class="btn btn-primary btn-sm btn-small" href="<?php echo site_url('site/supplier/'.$company->username)?>" target="_blank">View Profile</a>
		</div>		
	   <div id="container">
		
		<div class="row">
                    <div class="col-md-12">
                        <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                            </div>
                            
                            <div class="grid-body no-border">
                            	<div class="row">
                    				<form id="profileform" name="profileform" class="animated fadeIn" method="post" action="<?php echo site_url('company/saveprofile');?>" enctype="multipart/form-data">
				                     
				                     <div class="col-md-10 col-sm-8 col-xs-10">
                    				  <div>
				                        <label class="form-label">Username : <?php echo $company->username;?></label>
				                        <div class="controls">
				                         
				                        </div>
				                      </div>
				                     </div>
				                     
                    				<div class="col-md-10 col-sm-10 col-xs-10">
                    				  <div class="form-group">
				                        <label class="form-label">Company Name:</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="title" value="<?php echo $company->title;?>" required>
				                        </div>
				                      </div>
                    				
				                      <div class="form-group">
				                        <label class="form-label">Primary Email:</label>
				                        <div class="controls">
				                          <input type="text" class="form-control input-sm" name="primaryemail" value="<?php echo $company->primaryemail;?>" required>
				                        </div>
				                      </div>
				                      
				             
				                      <div class="form-group">
				                        <label class="form-label">Other Emails:</label>
				                        <div class="controls">
				                          <ul id="emaillist" class="list-inline">
				                          <?php if(@$emails)foreach(@$emails as $email){?>
				                          	<li style="padding-top: 5px;padding-left:0px">
				                          		<input type="text" name="emails[]" size="28" value="<?php echo $email->email;?>">
				                          		<button class="btn btn-danger btn-sm btn-small" style="margin-top: 3px;" onclick="$(this).closest('li').remove()" type="button"><i class="fa fa-times-circle"></i>&nbsp;</button>
				                          	</li>
				                          <?php }?>
				                          </ul>
				                          <input type="button" onclick="showEmailForm()" value="+ Add Email" class="btn btn-primary btn-sm btn-small"/>
				                        </div>
				                      </div>
                    				
                    				<div class="form-group">
				                        <label class="form-label">Short Details: (300 characters only)</label>
				                        <div class="controls">
				                          <textarea rows="4" cols="40" class="form-control" id="shortdetail" onkeyup="countChar(this)" name="shortdetail"><?php echo $company->shortdetail;?></textarea>
				                        <div id="charNum"></div>
				                        </div>
				                      </div>
				                      
				                      <div class="form-group">
				                        <label class="form-label">About:</label>
				                        <div class="controls">
				                          <textarea rows="10" cols="40" class="form-control ckeditor" id="about" name="about"><?php echo $company->about;?></textarea>
				                        </div>
				                      </div>
                    				
				                      <div class="form-group">
				                        <label class="form-label">Contact</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="contact" value="<?php echo $company->contact;?>">
				                        </div>
				                      </div>
				                      
				                       <div class="form-group">
				                        <label class="form-label">Company Type:</label>
				                        <div class="controls">
									      <table class="table table-bordered span6">
									      	<tr>
									      		<th class="span6">Industry</th>
									      		<th class="span6">Manufacturer</th>
									      	</tr>
									      	<tr valign="top">
									      		<td>
									      			<?php foreach($types as $type) if($type->category=='Industry'){?>
									      			<input name="types[]" type="checkbox" value="<?php echo $type->id;?>" <?php echo $type->checked;?>>
									      			<?php echo $type->title;?>
									      			<br/>
									      			<?php }?>
									      		</td>
									      		<td>
									      			<?php foreach($types as $type) if($type->category=='Manufacturer'){?>
									      			<input name="types[]" type="checkbox" value="<?php echo $type->id;?>" <?php echo $type->checked;?>>
									      			<?php echo $type->title;?>
									      			<br/>
									      			<?php }?>
									      		</td>
									      	</tr>
									      </table>
				                        </div>
				                      </div>

                    				 <?php if(1){?>
                    				 <div class="form-group">
				                        <label class="form-label">City</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="city" id="city" value="<?php echo $company->city;?>" required>
				                        </div>
				                      </div>
				                      
				                      <div class="form-group">
				                        <label class="form-label">State:</label>
				                        <div class="controls">
				                         <select name="state" id="state" required>
    				                        <?php foreach($states as $st){?>
                                        	<option value='<?php echo $st->state_abbr;?>' <?php if($company->state == $st->state_abbr){echo 'SELECTED';}?>><?php echo $st->state_name;?></option>
                                        	<?php }?>
				                         </select>
				                        </div>
				                      </div>
				                      
				                      <div class="form-group">
				                        <label class="form-label">Zip</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="zip" id="zip" value="<?php echo $company->zip;?>" required>
				                        </div>
				                      </div>
				                      <?php }?>
				                      
				                      <div class="form-group">
				                        <label class="form-label">Street Address *</label>
				                        <div class="controls">
				                          <textarea rows="2"  class="form-control" name="street" required><?php echo $company->street;?></textarea>
				                        	
				                        </div>
				                      </div>
                    				
				                      <div class="form-group">
				                        <label class="form-label">Phone:</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="phone" id="phone" value="<?php echo $company->phone;?>">
				                        </div>
				                      </div>
                    				
				                      <div class="form-group">
				                        <label class="form-label">Fax:</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="fax" id="fax" value="<?php echo $company->fax;?>">
				                        </div>
				                      </div>
                    				
				                      <div class="form-group">
				                        <label class="form-label">Logo</label>
				                        <div class="controls">
				                          <input type="file"  name="logo" id="logo"/>
				                          
				                          <?php if($company->logo){?>
				                          <br/>
				                          <img src="<?php echo site_url('uploads/logo/thumbs/'.$company->logo);?>" width="100" height="100"/>
				                          <?php }?>
				                        </div>
				                      </div>
				                      
				                      <div class="form-group">
				                        <label class="form-label">Invoice Notes:</label>
				                        <div class="controls">
				                          <textarea rows="2" cols="40" class="form-control" name="invoicenote"><?php echo $company->invoicenote;?></textarea>
				                        </div>
				                      </div>
                    				
				                      <div class="form-group">
				                        <label class="form-label"></label>
				                        <div class="controls">
				                          <input type="submit" value="Save" class="btn btn-primary btn-cons general">
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