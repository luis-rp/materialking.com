
<div class="container">
  <div class="row login-container animated fadeInUp">  
        <div class="col-md-7 col-md-offset-2 tiles white no-padding">
		 <div class="p-t-30 p-l-40 p-b-20 xs-p-t-10 xs-p-l-10 xs-p-b-10"> 
          <h2 class="normal">Change Password</h2>
          <p class="p-b-20 general"><span class="text-success semi-bold">Dear <?php echo $company->title;?></span><br/>
          Please create your new password below.</p>
		  
        </div>
        <?php echo $this->session->flashdata('message'); ?>
        <div class="tiles grey p-t-20 p-b-20 text-black">
			  <form id="frm_login" name="frm_login" class="animated fadeIn" method="post" action="<?php echo site_url('company/savechange');?>">
			  		<input type="hidden" name="passkey" value="<?php echo $company->passkey;?>">
                    <div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
                      
                      <div class="col-md-6 col-sm-6">
                       <label class="form-label text-success semi-bold general">Password</label>
                       <input name="password" id="login_pass" type="password"  class="form-control">
                      </div>
                      
                       <div class="col-md-6 col-sm-6">
                       <label class="form-label text-success semi-bold general">Retype Password</label>
                       <input name="repassword" id="rlogin_pass" type="password" class="form-control">
                      </div>
                      
                      <div class="col-md-6 col-sm-6">
                       <label class="form-label general">&nbsp;</label>
                       <button type="submit" class="btn btn-primary btn-cons" id="login_toggle" onclick="document.frm_login.submit()">Register</button>
                      </div>
                      

                    </div>
			  </form>
		</div> 
		  
      </div>   
  </div>
</div>
<!-- END CONTAINER -->