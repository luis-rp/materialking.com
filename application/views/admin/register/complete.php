	<section class="signin-container" style="padding:30px;">
		<h1><a href="#" title="EZPZP" class="header">
			<img src="<?php echo base_url(); ?>/templates/admin/images/applogo.png" alt="EZPZP">
			<span>&nbsp;</span>
		</a></h1>
<script type="text/javascript">

    function IsValidZip(zip) {
        var isValid = /^[0-9]{5}(?:-[0-9]{4})?$/.test(zip);
        if (!isValid){
       alert('Invalid Zip Format Please Enter XXXXX or XXXXX-XXXX Format');
      document.getElementById("zip").value = "";
    }
    }
</script>
		<div id="login_error">
		<?php echo $this->session->flashdata('message'); ?>
		</div>

		<div class="box">
			<div class="span3" style="margin-left:0px;width:auto;">
				Please submit your Username and Password to gain access on Purchasing admin Dashboard.
			</div>
			</div>

		<?php echo form_open('admin/register/savecomplete') . "\n"; ?>
			<input type="hidden" name="regkey" value="<?php echo $user->regkey;?>"/>
			<fieldset>
				<div class="fields">
					<input type="text" name="fullname" placeholder="Full Name" id="fullname" value="<?php if(isset($user->fullname) && $user->fullname!="")
					echo $user->fullname;?>" tabindex="1" style="margin-top:3px;" required>
					<input type="text" name="username" placeholder="User Name" id="username" value="<?php if(isset($user->username) && $user->username!="") echo $user->username;?>" tabindex="2" style="margin-top:3px;" required>
					<input type="password" name="password" placeholder="Your Password" id="password" value="<?php if(isset($user->password) && $user->password!="") echo $user->password;?>" tabindex="3" style="margin-top:3px;" required>
					<input type="password" name="repassword" placeholder="Confirm Password" id="repassword" value="<?php if(isset($user->password) && $user->password!="") echo $user->password;?>" tabindex="4" style="margin-top:3px;" required>
					
					<label style="margin-top:3px;color:white;font-weight:bold;">Select Category</label>
					<select name="category" id="category" tabindex="5" style="margin-top:3px;width:100%;" placeholder="Select" required>
	                        <?php foreach($categories as $cat){?>
                        	<option value='<?php echo $cat->id;?>'><?php echo $cat->catname;?></option>
                        	<?php }?>
                         </select>
			
					<input type="text" name="city" placeholder="City" id="city" value="<?php if(isset($user->city) && $user->city!="") echo $user->city;?>" tabindex="6" required><br/>
					<input type="text" name="state" placeholder="State" id="state" value="<?php if(isset($user->state) && $user->state!="") echo $user->state;?>" tabindex="7" required><br/>
					<input type="text" name="zip" placeholder="Zip" id="zip" value="<?php if(isset($user->zip) && $user->zip!="") echo $user->zip;?>" tabindex="8"
					onchange="IsValidZip(this.form.zip.value)" required>
					 <textarea name="street" tabindex="9" placeholder="Street Address" id="street" required rows="4" style="margin-top:3px; width: 96%" required><?php if(isset($user->street) && $user->street!="") echo $user->street;?></textarea>
				</div>
					<input type="hidden" name="hiddenuserid" id="hiddenuserid" value="<?php if(isset($user->id) && $user->id!="") echo $user->id; ?>"/>
				<button type="submit" class="btn btn-primary btn-block" tabindex="10">Continue</button>
			</fieldset>
		<?php echo form_close(); ?>

	</section>