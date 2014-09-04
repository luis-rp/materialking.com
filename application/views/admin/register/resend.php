	<section class="signin-container" style="padding:30px;">
		 <h1><a href="#" title="EZPZP" class="header">
			<img src="<?php echo base_url(); ?>/templates/admin/images/applogo.png" alt="EZPZP">
			<span>&nbsp;</span>
		</a> </h1>
		
		<div id="login_error">
		<?php echo $this->session->flashdata('message'); ?>
		</div>
		
			<div class="box">
				<div class="span3" style="margin-left:0px;width:auto;">
					Please enter your email to retrieve activation link.
				</div>
			</div>
					
		<?php echo form_open('admin/register/sendkeyagain',array('class'=>"form-horizontal")) . "\n"; ?>
			<fieldset>
				<div class="fields">
					<input type="email" name="email" placeholder="Email" id="email" tabindex="1">
				</div>
				<input type="submit" class="btn btn-primary btn-block" name="reqtype" value="Get Link" style="background:#E46837;"/>
				<a class="btn btn-primary" href="<?php echo site_url('admin/login');?>">Back To Login</a>
				&nbsp; &nbsp;
				<a class="btn btn-primary" href="<?php echo site_url('admin/register');?>">Create Account</a>
			</fieldset>
		<?php echo form_close(); ?>
		
	</section>