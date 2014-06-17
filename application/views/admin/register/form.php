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
				Please Fill up below form to register. Once you submit form, your will get a link for verification.
				<a href="<?php echo site_url('admin/register/resend');?>">Resend Activation Link</a>
			</div>
		</div>
					
		<?php echo form_open('admin/register/saveregister') . "\n"; ?>
			<fieldset>
				<div class="fields">
					<input type="text" name="companyname" placeholder="Company Name" id="companyname" tabindex="1" required>
					<input name="email" placeholder="Email Address" id="email" tabindex="2" required>
				</div>
				<button type="submit" class="btn btn-primary btn-block" tabindex="4">Register</button>
				
				<a class="btn btn-primary" href="<?php echo site_url('admin/login');?>">Back To Login</a>
				<a class="btn btn-primary" href="<?php echo site_url('admin/register/forgot');?>">Forgot Password?</a>
				
			</fieldset>
		<?php echo form_close(); ?>
		
	</section>