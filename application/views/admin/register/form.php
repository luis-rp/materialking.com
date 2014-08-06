<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>   
<script type="text/javascript">
   function callfbregister(){
   
	$("#registerform").attr("action","<?php echo site_url(); ?>fblogin");
	$('form#registerform').submit();
   }
   
   </script>

   
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
					
		<?php //  echo form_open('admin/register/saveregister') . "\n"; ?>
		<form id="registerform" accept-charset="utf-8" method="POST" action="<?php echo site_url('admin/register/saveregister'); ?>">
			<fieldset>
				<div class="fields">
					<input type="text" name="companyname" placeholder="Company Name" id="companyname" tabindex="1" required>
					<input name="email" placeholder="Email Address" id="email" tabindex="2" required>
				</div>
				<button type="submit" class="btn btn-primary btn-block" tabindex="4">Register</button>
				<button type="button" id="fbloginbtn" class="btn-block" style="background-color:#3B5998;line-height:26px;margin-top:15px;color:#FFFFFF;" tabindex="4" onclick="callfbregister();">Register Using Facebook</button>
				<a class="btn btn-primary" style="display:block;" href="<?php echo site_url('admin/login');?>">Back To Login</a>
				<a class="btn btn-primary" style="display:block;" href="<?php echo site_url('admin/register/forgot');?>">Forgot Password?</a>
				
			</fieldset>
		<?php echo form_close(); ?>
		
	</section>