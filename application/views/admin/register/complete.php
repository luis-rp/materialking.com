	<section class="signin-container">
		<a href="#" title="EZPZP" class="header">
			<img src="<?php echo base_url(); ?>/templates/admin/images/applogo.png" alt="EZPZP">
			<span>&nbsp;</span>
		</a>
		
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
					<input type="text" name="fullname" placeholder="Full Name" id="fullname" tabindex="1" style="margin-top:3px;" required>
					<input type="text" name="username" placeholder="User Name" id="username" tabindex="1" style="margin-top:3px;" required>
					<input type="password" name="password" placeholder="Your Password" id="password" tabindex="2" style="margin-top:3px;" required>
					<input type="password" name="repassword" placeholder="Confirm Password" id="repassword" tabindex="3" style="margin-top:3px;" required>
					<textarea name="address" placeholder="Complete Address, Include City/State/Zip" id="address" required rows="4" style="margin-top:3px; width: 96%"></textarea>
				</div>

				<button type="submit" class="btn btn-primary btn-block" tabindex="4">Continue</button>
			</fieldset>
		<?php echo form_close(); ?>
		
	</section>