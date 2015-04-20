
<div class="container">
  <div class="row login-container animated fadeInUp">  
        <div class="col-md-7 col-md-offset-2 tiles white no-padding">
		 <div class="p-t-30 p-l-40 p-b-20 xs-p-t-10 xs-p-l-10 xs-p-b-10"> 
          <h2 class="normal">Resend Activation</h2>
          <p class="p-b-20 general">
          Please provide your email to receive your activation link.
          </p>
		  
        </div>
        <?php echo $this->session->flashdata('message'); ?>
        <div class="tiles grey p-t-20 p-b-20 text-black">
			  <form id="frm_login" name="frm_login" class="animated fadeIn" method="post" action="<?php echo site_url('company/sendkeyagain');?>">
			  		<div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">

                      <div class="col-md-6 col-sm-6">
                       <label class="form-label text-success semi-bold general">Email</label>
                       <input name="email" id="login_pass" type="email" class="form-control">
                      </div>
                      
                      <div class="col-md-6 col-sm-6">
                       <label class="form-label general">&nbsp;</label>
                       <button type="submit" class="btn btn-primary btn-cons" id="login_toggle" onclick="document.frm_login.submit()">Get Link</button>
                       <a href="<?php echo site_url('company');?>" class="btn btn-primary btn-cons">
                       	Return to Login
                       </a>
                      </div>
                      

                    </div>
			  </form>
		</div> 
		  
      </div>   
  </div>
</div>
<!-- END CONTAINER -->