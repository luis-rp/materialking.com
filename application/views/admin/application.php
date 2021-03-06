<script type="text/javascript">
<!--
$(document).ready(function(){
	
});
//-->

var upload_number = 2;
	function addFileInput() {
	 	var d = document.createElement("div");
	 	var file = document.createElement("input");
	 	file.setAttribute("type", "file");
	 	file.setAttribute("name", "UploadFile[]");
	 	d.appendChild(file);
	 	document.getElementById("moreUploads").appendChild(d);
	 	upload_number++;
	}
</script>

<section class="row-fluid">
	<h3 class="box-header">Master Credit Application</h3>
	<div class="box">
	<div class="span10">
	
    <?php echo $this->session->flashdata('message'); ?>
	
       <form class="form-horizontal" method="post" action="<?php echo site_url('admin/dashboard/saveappl'); ?>" ENCTYPE="multipart/form-data">
        	<table class="table table-bordered">
        		<tr>
        			<td>Type</td>
        			<td>
        				<input type="radio" name="neworupdate" value="New" <?php if(@$appl->neworupdate=='New'){echo 'checked';} ?>/> New<br/>
        				<input type="radio" name="neworupdate" value="Update" <?php if(@$appl->neworupdate=='Update'){echo 'checked';} ?>/> Update
        			</td>
        		</tr>
        		<tr>
        			<td colspan="2">Primary Location of Material Purchases</td>
        		</tr>
        		<tr>
        			<td>City</td>
        			<td><input class="span4" type="text" name="primarylocationcity" value="<?php echo @$appl->primarylocationcity;?>"/></td>
        		</tr>
        		<tr>
        			<td>State</td>
        			<td><input class="span4" type="text" name="primarylocationstate" value="<?php echo @$appl->primarylocationstate;?>"/></td>
        		</tr>
        		<tr>
        			<td colspan="2">Applicant</td>
        		</tr>
        		<tr>
        			<td>Name</td>
        			<td><input class="span7" type="text" name="name" value="<?php echo @$appl->name;?>"/></td>
        		</tr>
        		<tr>
        			<td>Address</td>
        			<td><input class="span7" type="text" name="address" value="<?php echo @$appl->address;?>"/></td>
        		</tr>
        		<tr>
        			<td>City</td>
        			<td><input class="span7" type="text" name="city" value="<?php echo @$appl->city;?>"/></td>
        		</tr>
        		<tr>
        			<td>State</td>
        			<td><input class="span3" type="text" name="state" value="<?php echo @$appl->state;?>"/></td>
        		</tr>
        		<tr>
        			<td>Zip</td>
        			<td><input class="span3" type="text" name="zipcode" value="<?php echo @$appl->zipcode;?>"/></td>
        		</tr>
        		<tr>
        			<td>Phone</td>
        			<td><input class="span6" type="text" name="phone" value="<?php echo @$appl->phone;?>"/></td>
        		</tr>
        		<tr>
        			<td>Fax</td>
        			<td><input class="span6" type="text" name="fax" value="<?php echo @$appl->fax;?>"/></td>
        		</tr>
        		<tr>
        			<td>Mobile</td>
        			<td><input class="span6" type="text" name="mobile" value="<?php echo @$appl->mobile;?>"/></td>
        		</tr>
        		<tr>
        			<td>Email</td>
        			<td>
        			<input type="text" class="span6" name="email" value="<?php echo @$appl->email;?>"/>
        			<br/>
        			<input type="checkbox" name="sendoffers" value="<?php echo @$appl->sendoffers?'checked':'';?>"/>
        			Yes! Please send me special offers and updates via email .
        			</td>
        		</tr>
        		<tr>
        			<td colspan="2">
        			Principals/Officers
        			</td>
        		</tr>
        		<tr>
        			<td colspan="2">
        				<table class="table">
        					<tr>
        						<th>Title</th>
        						<th>Names</th>
        						<th>City/State</th>
        						<th>Social Security#</th>
        						<th>Date of Birth</th>
        						<th>Phone</th>
        						<th>Insolvency*</th>
        					</tr>
        					<?php for($i=1; $i<5; $i++){?>
        					<?php $title = "officer".$i."title";?>
        					<?php $name = "officer".$i."name";?>
        					<?php $city = "officer".$i."city";?>
        					<?php $socialnum = "officer".$i."socialnum";?>
        					<?php $dob = "officer".$i."dob";?>
        					<?php $phone = "officer".$i."phone";?>
        					<?php $insolvency = "officer".$i."insolvency";?>
        					<tr>
        						<td><input class="span12" type="text" name="<?php echo $title;?>" value="<?php echo $appl->$title?>"/></td>
        						<td><input class="span12" type="text" name="<?php echo $name;?>" value="<?php echo $appl->$name?>"/></td>
        						<td><input class="span12" type="text" name="<?php echo $city;?>" value="<?php echo $appl->$city?>"/></td>
        						<td><input class="span12" type="text" name="<?php echo $socialnum;?>" value="<?php echo $appl->$socialnum?>"/></td>
        						<td><input class="span12" type="text" name="<?php echo $dob;?>" value="<?php echo $appl->$dob?>"/></td>
        						<td><input class="span12" type="text" name="<?php echo $phone;?>" value="<?php echo $appl->$phone?>"/></td>
        						<td><input class="span12" type="text" name="<?php echo $insolvency;?>" value="<?php echo $appl->$insolvency?>"/></td>
        					</tr>
        					<?php }?>
        				</table>
        			</td>
        		</tr>
        		<tr>
        			<td colspan="2">
        			* List the year of any bankruptcy or insolvency by principal/officer 
        			or any affiliated corporation, LLC, partnership or business.
        			</td>
        		</tr>
        		<tr>
        			<td colspan="2">
        			Billing Information
        			</td>
        		</tr>
        		<tr>
        			<td>
        				Billing Address
        			</td>
        			<td>
        				<input class="span6" type="text" name="billingaddress" value="<?php echo @$appl->billingaddress;?>"/>
        				(if different from above)
        			</td>
        		</tr>
        		<tr>
        			<td>City</td>
        			<td><input class="span6" type="text" name="billingcity" value="<?php echo @$appl->billingcity;?>"/></td>
        		</tr>
        		<tr>
        			<td>State</td>
        			<td><input class="span6" type="text" name="billingstate" value="<?php echo @$appl->billingstate;?>"/></td>
        		</tr>
        		<tr>
        			<td>Zip</td>
        			<td><input class="span6" type="text" name="billingzip" value="<?php echo @$appl->billingzip;?>"/></td>
        		</tr>
        		<tr>
        			<td>Sales Tax Exemption #</td>
        			<td><input class="span6" type="text" name="stnum" value="<?php echo @$appl->stnum;?>"/></td>
        		</tr>
        		<tr>
        			<td>State</td>
        			<td><input type="text" name="ststate" value="<?php echo @$appl->ststate;?>"/></td>
        		</tr>
        		<tr>
        			<td>Zip</td>
        			<td><input type="text" name="stzip" value="<?php echo @$appl->stzip;?>"/></td>
        		</tr>
        		<tr>
        			<td>Are Purchase Orders Issued?</td>
        			<td>
        				<select name="poissued">
        					<option></option>
        					<option value="Yes" <?php if(@$appl->poissued == 'Yes'){echo 'SELECTED';}?>>Yes</option>
        					<option value="No" <?php if(@$appl->poissued == 'No'){echo 'SELECTED';}?>>No</option>
        				</select>
        			</td>
        		</tr>
        		<tr>
        			<td>Are job names required?</td>
        			<td>
        				<select name="jobnamereqd">
        					<option></option>
        					<option value="Yes" <?php if(@$appl->jobnamereqd == 'Yes'){echo 'SELECTED';}?>>Yes</option>
        					<option value="No" <?php if(@$appl->jobnamereqd == 'No'){echo 'SELECTED';}?>>No</option>
        				</select>
        			</td>
        		</tr>
        		<tr>
        			<td>Special Billing Instructions</td>
        			<td><input class="span6" type="text" name="billinginstructions" value="<?php echo @$appl->billinginstructions;?>"/></td>
        		</tr>
        		<tr>
        			<td>Company Tax ID#</td>
        			<td><input class="span6" type="text" name="companytaxid" value="<?php echo @$appl->companytaxid;?>"/></td>
        		</tr>
        		<tr>
        			<td>Dun & Bradstreet (D&B) D-U-N-S number (if available)</td>
        			<td><input class="span6" type="text" name="dunsnum" value="<?php echo @$appl->dunsnum;?>"/></td>
        		</tr>
        		<tr>
        			<td colspan="2">About Your Company (Please attach financial statements for last 2 years)</td>
        		</tr>
        		<tr>
        			<td colspan="2">
        				<?php $aboutyourcompany = explode(', ',$appl->aboutyourcompany);?>
        				<table class="table">
        					<tr>
        						<td>
        							<input type="checkbox" name="aboutyourcompany[]" value="Single Family" <?php if(in_array('Single Family',$aboutyourcompany)){echo 'checked';}?>/>
        							Single Family
        						</td>
        						<td>
        							<input type="checkbox" name="aboutyourcompany[]" value="Commercial" <?php if(in_array('Commercial',$aboutyourcompany)){echo 'checked';}?>/>
        							Commercial
        						</td>
        						<td>
        							<input type="checkbox" name="aboutyourcompany[]" value="Fire Protection" <?php if(in_array('Fire Protection',$aboutyourcompany)){echo 'checked';}?>/>
        							Fire Protection
        						</td>
        						<td>
        							<input type="checkbox" name="aboutyourcompany[]" value="Water Works" <?php if(in_array('Water Works',$aboutyourcompany)){echo 'checked';}?>/>
        							Water Works
        						</td>
        					</tr>
        					<tr>
        						<td>
        							<input type="checkbox" name="aboutyourcompany[]" value="Multi-Family" <?php if(in_array('Multi-Family',$aboutyourcompany)){echo 'checked';}?>/>
        							Multi-Family
        						</td>
        						<td>
        							<input type="checkbox" name="aboutyourcompany[]" value="HVAC R C" <?php if(in_array('HVAC R C',$aboutyourcompany)){echo 'checked';}?>/>
        							HVAC R C
        						</td>
        						<td>
        							<input type="checkbox" name="aboutyourcompany[]" value="Plumbing" <?php if(in_array('Plumbing',$aboutyourcompany)){echo 'checked';}?>/>
        							Plumbing
        						</td>
        						<td>
        							<input type="checkbox" name="aboutyourcompany[]" value="Segment Code/Other" <?php if(in_array('Segment Code/Other',$aboutyourcompany)){echo 'checked';}?>/>
        							Segment Code/Other
        							<input type="text" name="segmentcodeother" value="<?php echo @$appl->segmentcodeother;?>"/>
        						</td>
        					</tr>
        					<tr>
        						<td>
        							<input type="checkbox" name="aboutyourcompany[]" value="Residential-Remodel" <?php if(in_array('Residential-Remodel',$aboutyourcompany)){echo 'checked';}?>/>
        							Residential-Remodel
        						</td>
        						<td>
        							<input type="checkbox" name="aboutyourcompany[]" value="Industrial PVF" <?php if(in_array('Industrial PVF',$aboutyourcompany)){echo 'checked';}?>/>
        							Industrial PVF
        						</td>
        						<td>
        							<input type="checkbox" name="aboutyourcompany[]" value="Government" <?php if(in_array('Government',$aboutyourcompany)){echo 'checked';}?>/>
        							Government
        						</td>
        						<td>
        							<input type="checkbox" name="aboutyourcompany[]" value="Estimated Monthly Volume $" <?php if(in_array('Estimated Monthly Volume $',$aboutyourcompany)){echo 'checked';}?>/>
        							Estimated Monthly Volume $
        							<input type="text" name="estimatedmonthlyvolume" value="<?php echo @$appl->estimatedmonthlyvolume;?>"/>
        						</td>
        					</tr>
        				</table>
        			</td>
        		</tr>
        		<tr>
        			<td>Date Business Started</td>
        			<td><input class="span4" type="text" name="startdate" value="<?php echo @$appl->startdate;?>"/></td>
        		</tr>
        		<tr>
        			<td>No. of Employees</td>
        			<td><input class="span4" type="text" name="employeenum" value="<?php echo @$appl->employeenum;?>"/></td>
        		</tr>
        		<tr>
        			<td>Surety/Bonding Company</td>
        			<td><input class="span4" type="text" name="bondingcompany" value="<?php echo @$appl->bondingcompany;?>"/></td>
        		</tr>
        		<tr>
        			<td>Date of Incorporation</td>
        			<td><input class="span4" type="text" name="incorporationdate" value="<?php echo @$appl->incorporationdate;?>"/></td>
        		</tr>
        		<tr>
        			<td>State of Incorporation</td>
        			<td><input class="span4" type="text" name="incorporationstate" value="<?php echo @$appl->incorporationstate;?>"/></td>
        		</tr>
        		<tr>
        			<td>Select the entity type</td>
        			<td>
        				<select name="entitytype">
        					<option></option>
        					<option value="Corporation" <?php if(@$appl->entitytype == 'Corporation'){echo 'SELECTED';}?>>Corporation</option>
        					<option value="LLC" <?php if(@$appl->entitytype == 'LLC'){echo 'SELECTED';}?>>LLC</option>
        					<option value="Sole Proprietor" <?php if(@$appl->entitytype == 'Sole Proprietor'){echo 'SELECTED';}?>>Sole Proprietor</option>
        					<option value="Partnership" <?php if(@$appl->entitytype == 'Partnership'){echo 'SELECTED';}?>>Partnership</option>
        				</select>
					</td>
        		</tr>
        		<tr>
        			<td colspan="2">
        				<table class="table">
        					<tr>
        						<th>Type of license hold</th>
        						<th>State</th>
        						<th>Name of the holder</th>
        						<th>Number</th>
        						<th>Expiration Date</th>
        					</tr>
        					<tr>
        						<td><input class="span12" type="text" name="licensetype1" value="<?php echo @$appl->licensetype1?>"/></td>
        						<td><input class="span12" type="text" name="licensestate1" value="<?php echo @$appl->licensestate1?>"/></td>
        						<td><input class="span12" type="text" name="licenseholder1" value="<?php echo @$appl->licenseholder1?>"/></td>
        						<td><input class="span12" type="text" name="licensenumber1" value="<?php echo @$appl->licensenumber1?>"/></td>
        						<td><input class="span12" type="text" name="licenseexpdate1" value="<?php echo @$appl->licenseexpdate1?>"/></td>
        					</tr>
        					<tr>
        						<td><input class="span12" type="text" name="licensetype2" value="<?php echo @$appl->licensetype2?>"/></td>
        						<td><input class="span12" type="text" name="licensestate2" value="<?php echo @$appl->licensestate2?>"/></td>
        						<td><input class="span12" type="text" name="licenseholder2" value="<?php echo @$appl->licenseholder2?>"/></td>
        						<td><input class="span12" type="text" name="licensenumber2" value="<?php echo @$appl->licensenumber2?>"/></td>
        						<td><input class="span12" type="text" name="licenseexpdate2" value="<?php echo @$appl->licenseexpdate2?>"/></td>
        					</tr>
        				</table>
        			</td>
        		</tr>
        		<tr>
        			<td colspan="2">References</td>
        		</tr>
        		<tr>
        			<td colspan="2">
        				<table class="table">
        					<tr>
        						<th>Name</th>
        						<th>Address</th>
        						<th>Phone Number</th>
        						<th>Fax Number</th>
        						<th>Contact Name</th>
        					</tr>
        					<tr>
        						<td><input class="span12" type="text" name="ref1name" value="<?php echo @$appl->ref1name?>"/></td>
        						<td><input class="span12" type="text" name="ref1address" value="<?php echo @$appl->ref1address?>"/></td>
        						<td><input class="span12" type="text" name="ref1phone" value="<?php echo @$appl->ref1phone?>"/></td>
        						<td><input class="span12" type="text" name="ref1fax" value="<?php echo @$appl->ref1fax?>"/></td>
        						<td><input class="span12" type="text" name="ref1contact" value="<?php echo @$appl->ref1contact?>"/></td>
        					</tr>
        					<tr>
        						<td><input class="span12" type="text" name="ref2name" value="<?php echo @$appl->ref2name?>"/></td>
        						<td><input class="span12" type="text" name="ref2address" value="<?php echo @$appl->ref2address?>"/></td>
        						<td><input class="span12" type="text" name="ref2phone" value="<?php echo @$appl->ref2phone?>"/></td>
        						<td><input class="span12" type="text" name="ref2fax" value="<?php echo @$appl->ref2fax?>"/></td>
        						<td><input class="span12" type="text" name="ref2contact" value="<?php echo @$appl->ref2contact?>"/></td>
        					</tr>
        					<tr>
        						<td><input class="span12" type="text" name="ref3name" value="<?php echo @$appl->ref3name?>"/></td>
        						<td><input class="span12" type="text" name="ref3address" value="<?php echo @$appl->ref3address?>"/></td>
        						<td><input class="span12" type="text" name="ref3phone" value="<?php echo @$appl->ref3phone?>"/></td>
        						<td><input class="span12" type="text" name="ref3fax" value="<?php echo @$appl->ref3fax?>"/></td>
        						<td><input class="span12" type="text" name="ref3contact" value="<?php echo @$appl->ref3contact?>"/></td>
        					</tr>
        					<tr>
        						<td><input class="span12" type="text" name="ref4name" value="<?php echo @$appl->ref4name?>"/></td>
        						<td><input class="span12" type="text" name="ref4address" value="<?php echo @$appl->ref4address?>"/></td>
        						<td><input class="span12" type="text" name="ref4phone" value="<?php echo @$appl->ref4phone?>"/></td>
        						<td><input class="span12" type="text" name="ref4fax" value="<?php echo @$appl->ref4fax?>"/></td>
        						<td><input class="span12" type="text" name="ref4contact" value="<?php echo @$appl->ref4contact?>"/></td>
        					</tr>
        					<TR>
							<TD class="clsStdLabel1">Attachment:</TD>
							<!--<TD><INPUT TYPE="file" id="UploadFile"  value="" name="UploadFile" style='WIDTH:300px' ></TD>-->
							<TD>
							
                                				<table class="table">
                                					
                                						<?php if($attachmentdata)
                                							{
                                								$count = 1;
                                							foreach ($attachmentdata as $key=>$val) {	?>
                                							<tr>
                                								<td>
											    			<a href="<?php echo site_url('uploads/attachments/'.$val['attachmentname']);?>" target="_blank"> View Attachment <?php echo $count; ?></a>
											    				</td>
                                							</tr>
											    		<?php $count ++;} 
                                							}?>                                						
                                						
                                				</table>
                          
							<table id="searchTextResults" cellspacing="" border="0" class="" width="100%">
								    <tr>
								    	<td> 
							    			<input type="file" name="UploadFile[]" id="UploadFile" onchange="document.getElementById('moreUploadsLink').style.display = 'block';" />
											<div id="moreUploads"></div>
											
										</td> 
									</tr>
									<tr>
										<td>
											<div id="moreUploadsLink" style="display:none;"><a href="javascript:addFileInput();">Attach another File</a>
											</div> 
										</td>
									</tr>
							    </table> 
							</TD>
						</TR>
        				</table>
        			</td>
        		</tr>
        		<tr>
        			<td colspan="2"><input type="submit" value="Save" class="btn btn-primary"/></td>
        		</tr>
        	</table>
        
       </form>
    
    </div>
    </div>
</section>
