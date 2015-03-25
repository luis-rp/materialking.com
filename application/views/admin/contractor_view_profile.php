<script type="text/javascript" src="<?php echo base_url();?>templates/front/js/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
	function checkForm(form)
	{
		re =/^[A-Za-z0-9._-]+$/
		if(!re.test(form.username.value))
		{
			alert("Error: Username must contain only letters, numbers,dot and underscores!");
			form.username.focus();
			return false;
		}
	}

	function countChar(val)
	{
		var len = val.value.length;
		if (len >= 300) {
			val.value = val.value.substring(0, 300);
		} else {
			$('#charNum').text(300 - len + " Remaning") ;
		}
	}

	var upload_number = 2;
	function addFileInput() 
	{
	 	var d = document.createElement("div");
	 	var file = document.createElement("input");
	 	file.setAttribute("type", "file");
	 	file.setAttribute("name", "UploadFile[]");
	 	d.appendChild(file);
	 	document.getElementById("moreUploads").appendChild(d);
	 	upload_number++;
	}
	
	var upload_number = 2;
	function addFileInput2() 
	{
	 	var d = document.createElement("div");
	 	var file = document.createElement("input");
	 	file.setAttribute("type", "file");
	 	file.setAttribute("name", "UploadFile2[]");
	 	d.appendChild(file);
	 	document.getElementById("moreUploads2").appendChild(d);
	 	upload_number++;
	}
	
	var upload_number = 2;
	function addFileInput1() {
	 	var d = document.createElement("div");
	 	var file = document.createElement("input");
	 	file.setAttribute("type", "file");
	 	file.setAttribute("name", "UploadFile1[]");
	 	d.appendChild(file);
	 	document.getElementById("moreUploads1").appendChild(d);
	 	upload_number++;
	}
	
	$(document).ready(function(){
	 $("#addMemberBtn").click(function(){
			$("#addmember").modal();
			});
			
	  $(".editMemberBtn").click(function(){
	   var id = $(this).attr("name");
	   $.ajax({
		    url:"<?php echo base_url("company/getMemberInfo/");?>/"+id,
		    type:"GET",
		    success:function(msg){		    
		    	$("#idMember","#editMember").val(msg.id);
		    	$("#memberName","#editMember").val(msg.name);
		   		$("#memberTitle","#editMember").val(msg.title);
		   		$("#memberPhone","#editMember").val(msg.phone);
		   		$("#memberEmail","#editMember").val(msg.email);
		   		
		   		$("#memberLinkedin","#editMember").val(msg.linkedin);
		   		
				
		    },
		    dataType : "json"
		});
	  
		
	   $("#editMember").modal();
	   });
	   		
	   });
	   
	   

</script>

<section class="row-fluid">
	<?php echo $this->session->flashdata('message');?>
	<h3 class="box-header"><i class="icon-cog"></i>Contractor Profile</h3>
		<div class="box">
			<div class="span12">	
			   <form class="form-horizontal" method="post" action="<?php echo site_url('admin/contractor_profile/saveprofile'); ?>" enctype="multipart/form-data" onsubmit="return checkForm(this);" autocomplete="off">
			   
			    <div class="control-group">
				    <label class="control-label" for="username">Username *</label>
				    <div class="controls">
				    	<input type="text"  name="username" class="span4" value="<?php echo $contractor->username;?>" required>
				    </div>
			    </div>
			   
			   
			     <div class="control-group">
					 <label class="control-label" for="companyname">Company Name *</label>
					 <div class="controls">
					 	<input type="text" class="span4"  name="companyname" value="<?php echo $contractor->companyname;?>" required>
					 </div>
				 </div>
			   
			   
	 			<div class="control-group">
					<label class="control-label" for="email">Email *</label>
					<div class="controls">
						<input type="text" class="span4" name="email" value="<?php echo $contractor->email;?>" required>
					</div>
				</div>
				                      
				 <div class="control-group">
					 <label class="control-label" for="password">Password</label>
	 				 <div class="controls">
					 	<input type="password" class="span4" name="password" value="" autocomplete="off">
					 </div>
				 </div>				                          				
				                     
				 <div class="control-group">
					 <label class="control-label" for="fullname">Full Name *</label>
					 <div class="controls">
					 	<input type="text" class="span4" name="fullname" value="<?php echo $contractor->fullname;?>" required>
					 </div>
				 </div>
				               
                 <div class="control-group">
				 <label class="control-label" for="city">City *</label>
				 <div class="controls">
				 <input type="text" class="span4" name="city" id="city" value="<?php echo $contractor->city;?>" required>
				 </div>
				 </div>
				                      
				<div class="control-group">
					<label class="control-label" for="state">State</label>
					<div class="controls">
						<select name="state" id="state" required>
			    		    <?php foreach($states as $st){?>
			                	<option value='<?php echo $st->state_abbr;?>' <?php if($contractor->state == $st->state_abbr){echo 'SELECTED';}?>>
			                		<?php echo $st->state_name;?>
			                	</option>
			                 <?php }?>
						 </select>
					 </div>
				 </div>
				                      
				 <div class="control-group">
					 <label class="control-label" for="zip">Zip *</label>
					 <div class="controls">
					 	<input type="text" class="span4" name="zip" id="zip" value="<?php echo $contractor->zip;?>" required>
					 </div>
				 </div>
				                    
				                      
				<div class="control-group">
				    <label class="control-label" for="street">Street *</label>
					<div class="controls">
						<textarea rows="2"  class="span4" name="street" required><?php echo $contractor->street;?></textarea>
					</div>
				</div>
                    				
				<div class="control-group">
					<label class="control-label" for="phone">Phone</label>
					<div class="controls">
						<input type="text" class="span4" name="phone" id="phone" value="<?php echo $contractor->phone;?>">
					</div>
				</div>
                    				
				<div class="control-group">
					<label class="control-label" for="fax">Fax</label>
					<div class="controls">
						<input type="text" class="span4" class="control-control" name="fax" id="fax" value="<?php echo $contractor->fax;?>">
					</div>
				</div>
				
				<div class="control-group">
					 <label class="control-label" for="shortdetail">Short Details (300 characters only)</label>
					 <div class="controls">
					 	<textarea rows="4" cols="40"  class="span4" id="shortdetail" onkeyup="countChar(this)" name="shortdetail">
					 		<?php echo $contractor->shortdetail;?>
					 	</textarea>
					 <div id="charNum"></div>
					 </div>
				 </div>
				                      
				 <div class="control-group">
					<label class="control-label" for="about">About</label>
					 <div class="controls">
					 	<textarea rows="10" cols="40" class="ckeditor" id="about" name="about"><?php echo $contractor->about;?></textarea>
					 </div>
				 </div>
				
				<div class="control-group">
			        <label class="control-label">Logo</label>
					  <div class="controls">
						 <input type="file"  name="logo" id="logo"/>			                          
							   <?php if($contractor->logo){?><br/>
							      <img src="<?php echo site_url('uploads/logo/'.$contractor->logo);?>" width="100" height="100"/>
							    <?php }?>
					  </div>
			    </div>
                    		
				<div class="control-group">
					<label class="control-label">Add Images</label>
					   <div class="controls">
						<input type="file" name="UploadFile[]" id="UploadFile" onchange="document.getElementById('moreUploadsLink').style.display = 'block';" />
						<div id="moreUploads"></div>
				    	<div id="moreUploadsLink" style="display:none;">
				    		<a href="javascript:void(0);" onclick="javascript:addFileInput();">Add another Image</a>
						</div>
                      
						<?php if(count($contractorimages) > 0) {?>            
						<table class="table table-striped">
						<tr>
							<th>Image</th><th>Delete</th>
						</tr>
						<?php  foreach($contractorimages as $image)  { ?>
						<tr>
					    	<td>
					    		<img src="<?php echo site_url('uploads/ContractorImages/'.$image->image);?>" height="100px" width="100px" class="img-thumbnail" alt="<?php echo $image->image;?>"/>
					    	</td>
							<td>
								<a class="close"  href="<?php echo base_url("admin/contractor_profile/deletecontractorimage/".$image->id);?>" onclick="return confirm('Are you sure, you want to delete this image?');">&times;</a>
							</td>
						</tr>
						<?php } ?>
					   </table>	
					   <?php } ?>
					    </div>
				</div>
				                      
				                      
				<div class="control-group">
					<label class="control-label">Add Gallery Images</label>
					 <div class="controls">
						<input type="file" name="UploadFile2[]" id="UploadFile2" onchange="document.getElementById('moreUploadsLink2').style.display = 'block';" />
						<div id="moreUploads2"></div>
						<div id="moreUploadsLink2" style="display:none;">
							<a href="javascript:addFileInput2();">Add another Image for Gallery</a>
						</div>
					
						<?php if(count($contractorgallery) > 0) { ?>
						<table class="table table-striped">
						<tr>
							<th>Image</th><th>Delete</th>
						</tr>
						<?php  foreach($contractorgallery as $gallery)  { ?>
						<tr>
							<td>
								<img src="<?php echo site_url('uploads/ContractorGallery/'.$gallery->image);?>" height="100px" width="100px" class="img-thumbnail" alt="<?php echo $gallery->image;?>"/>
							</td>
							<td>
								<a class="close"  href="<?php echo base_url("admin/contractor_profile/deletecontractorgalleryimage/".$gallery->id);?>" onclick="return confirm('Are you sure, you want to delete this image?');">&times;</a>
							</td>
						</tr>
						<?php } ?>
						</table>
						<?php } ?>
						</div>	
				</div>
				                      
				                      
				<div class="control-group">
	     			<label class="control-label">Add Files</label>
	     			  <div class="controls">
						<input type="file" name="UploadFile1[]" id="UploadFile1" onchange="document.getElementById('moreUploadsLink1').style.display = 'block';" />
						<div id="moreUploads1"></div>
						<div id="moreUploadsLink1" style="display:none;">
							<a href="javascript:addFileInput1();">Add another File</a>
						</div>
                      
						<?php if(count($contractorfiles) > 0) { ?>               
						<table class="table table-striped">
						<tr>
							<th>Files</th><th>Is Private</th><th>Delete</th>
						</tr>
						<?php  foreach($contractorfiles as $files)  { ?>
						<tr>
							<td>
								<?php $arr1=explode('.',$files->file); $ext=end($arr1);
										if($ext=='gif' || $ext=='tif' || $ext=='jpg' || $ext=='png' || $ext=='GIF' || $ext=='TIF' || $ext=='JPG' || $ext=='PNG') { ?>
                 						<img  src="<?php echo site_url('uploads/ContractorFiles/'.$files->file);?>" height="100px" width="100px" class="img-thumbnail" alt="<?php echo $files->file;?>">
                 				<?php } else { echo $files->file; } ?>
                 			</td>
							<td>
							<input type="checkbox" id="file1[<?php echo $files->id;?>]" name="file1[<?php echo $files->id;?>]" <?php if(isset($files->private) && $files->private==1) {echo "checked='checked'";}?>/>
							<input type="hidden" name="checkid[]" value="<?php echo $files->id;?>"/>
							</td>
							<td>
							<a class="close"  href="<?php echo base_url("admin/contractor_profile/deletecontractfile/".$files->id);?>" onclick="return confirm('Are you sure, you want to delete this File?');">&times;</a>
							</td>
					   </tr>
					   <?php } ?>
					   </table>
					   <?php } ?>
					   </div>
				</div>
				                      
				<!--<div class="control-group">
					<label class="control-label">Team Members</label>
					<div class="controls">
						<a href="javascript:void(0);" id="addMemberBtn">Add Team Member</a>
						<?php if(count($contractorteam) > 0) { ?>
						<table class="table table-striped">
		        		<tr>
							<th>Name</th><th>Title</th><th>Picture</th><th>Phone</th><th>Email</th><th>LinkedIn</th><th>Actions</th>
						</tr>
						<?php  foreach($contractorteam as $member)  { ?>
						<tr>
							<td><?php echo $member->name;?></td>
							<td><?php echo $member->title;?></td>
							<td><img width=200 height=200 src="<?php echo base_url("uploads/ContractorTeam/".$member->picture);?>"/></td>
							<td><?php echo $member->phone;?></td>
							<td><?php echo $member->email;?></td>
							<td><a href="http://<?php echo $member->linkedin;?>"><?php echo $member->linkedin;?></a></td>
							<td><a href="#" class="editMemberBtn" name="<?php echo $member->id;?>">Edit</a>&nbsp;-&nbsp;
								<a href="<?php echo base_url("company/deletecontractmember/".$member->id);?>" onclick="return confirm('Are you sure?')">Delete</a></td>
						</tr>
						<?php } ?>
						</table>
				        <?php }?>
				     </div>
				</div>-->
                    				
				                      
				<div class="control-group">
					<label class="control-label" for="fbpageurl">Facebook Page URL</label>
					<div class="controls">
						<input type="text" class="span4" name="fbpageurl" id="fbpageurl" value="<?php echo $contractor->fbpageurl;?>">
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="twitter">Twitter</label>
					<div class="controls">
						<input type="text" class="span4" name="twitter" id="twitter" value="<?php echo $contractor->twitter;?>">
					</div>
				</div> 
				                                   		
			    <div class="control-group">
				    <label class="control-label"></label>
				    <div class="controls">
				     	<input type="submit" class="btn btn-primary" value="SAVE">
				    </div>
			    </div>
					
		</form>
	 </div>
	</div> 	
  </section>


<!-- <div class="modal fade" id="addmember" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
       	<form id="formMember" name="formMember" class="animated fadeIn" method="post" action="<?php //echo site_url('admin/contractor_profile/addcontractember');?>" enctype="multipart/form-data">
        <div class="modal-body" style="background-color:#FFFFFF;">
          <div class="row form-row">
            <div class="col-md-8">
             	
              					     <div class="form-group">
				                        <label class="form-label">Name:</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="memberName" id="memberName" >
				                        </div>
				                      </div>
				                      
				                       <div class="form-group">
				                        <label class="form-label">Title:</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="memberTitle" id="memberTitle" >
				                        </div>
				                      </div>
				                      
				                       <div class="form-group">
				                        <label class="form-label">Phone:</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="memberPhone" id="memberPhone" >
				                        </div>
				                      </div>
				                      
				                      <div class="form-group">
				                        <label class="form-label">Email:</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="memberEmail" id="memberEmail" >
				                        </div>
				                      </div>
				                      
				                       <div class="form-group">
				                        <label class="form-label">LinkedInd:</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="memberLinkedin" id="memberLinkedin" >
				                        </div>
				                      </div>
				                      
				                      <div class="form-group">
				                        <label class="form-label">Picture:</label>
				                        <div class="controls">
				                         <input type="file" name="memberPicture" id="memberPicture" />
				                        </div>
				                      </div>
				                      
				                      
          		 
            </div>
          </div>
         
        </div>
        <div class="modal-footer">
          <input type="submit" class="btn btn-primary" value="Save"/>
          <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
          
        </div>
          </form>
      </div>
    </div>
  </div>
  
     <div class="modal fade" id="editMember" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
       	<form id="formMember" name="formMember" class="animated fadeIn" method="post" action="<?php //echo site_url('company/editMember');?>" enctype="multipart/form-data">
        <div class="modal-body" style="background-color:#FFFFFF;">
          <div class="row form-row">
            <div class="col-md-8">
             	
              					     <div class="form-group">
				                        <label class="form-label">Name:</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="memberName" id="memberName" >
				                        </div>
				                      </div>
				                      
				                       <div class="form-group">
				                        <label class="form-label">Title:</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="memberTitle" id="memberTitle" >
				                        </div>
				                      </div>
				                      
				                       <div class="form-group">
				                        <label class="form-label">Phone:</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="memberPhone" id="memberPhone" >
				                        </div>
				                      </div>
				                      
				                      <div class="form-group">
				                        <label class="form-label">Email:</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="memberEmail" id="memberEmail" >
				                        </div>
				                      </div>
				                      
				                       <div class="form-group">
				                        <label class="form-label">LinkedInd:</label>
				                        <div class="controls">
				                          <input type="text" class="form-control" name="memberLinkedin" id="memberLinkedin" >
				                        </div>
				                      </div>
				                      
				                      <div class="form-group">
				                        <label class="form-label">Picture:</label>
				                        <div class="controls">
				                         <input type="file" name="memberPicture" id="memberPicture" />
				                        </div>
				                      </div>
				                      
				                      
          		 
            </div>
          </div>
         
        </div>
        <div class="modal-footer">
          <input name="idMember" id="idMember" type="hidden" value=""/>
          <input type="submit" class="btn btn-primary" value="Save"/>
          <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
          
        </div>
          </form>
      </div>
    </div>
  </div>-->