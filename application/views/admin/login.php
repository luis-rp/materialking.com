
	<section class="signin-container" style=" padding:30px;">
    <h1>
		<a href="#" title="EZPZP" class="header">
			<img src="<?php echo base_url(); ?>/templates/admin/images/applogo.png" alt="EZPZP">
			<span>&nbsp;</span>
		</a></h1>
		
		<div id="login_error">
		<?php echo $this->session->flashdata('message'); ?>
		</div>
		
		<div class="box">
						<div class="span3" style="margin-left:0px;width:auto;">
							Welcome to EZPZP Login. Fill up form to get access on Purchasing Dashboard.
						</div>
					</div>
					
		<?php echo form_open('admin/login/process_login') . "\n"; ?>
			<fieldset>
				<div class="fields">
					<input type="text" name="username" placeholder="Username" id="username" tabindex="1">
					<input type="password" name="password" placeholder="Password" id="password" tabindex="2">
				</div>
				
				<button type="submit" class="btn btn-primary btn-block" tabindex="4">Sign In</button>
				
				<a class="btn btn-primary btn-block" href="<?php echo site_url('admin/register/forgot');?>">Forgot Username/Password</a>
				
				<a class="btn btn-primary btn-block" href="<?php echo site_url('admin/register');?>">Register Now</a>
				<a class="btn btn-primary btn-block" href="<?php echo site_url('site/items');?>">Go to Store</a>
			</fieldset>
		<?php echo form_close(); ?>
		
	</section>