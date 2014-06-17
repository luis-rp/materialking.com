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
					Please fill up below form to chnage your password.
				</div>
		</div>
				
		<?php echo form_open('admin/register/savechange') . "\n"; ?>
			<input type="hidden" name="passkey" value="<?php echo $user->passkey;?>">
			<fieldset>
				<div class="fields">
					<input type="password" name="password" placeholder="New Password" id="password" tabindex="1">
					<input type="password" name="repassword" placeholder="Confirm Password" id="repassword" tabindex="2">
				</div>
				
				<button type="submit" class="btn btn-primary btn-block" tabindex="4">Change</button>
			</fieldset>
		<?php echo form_close(); ?>
		
	</section>