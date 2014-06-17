
<div class="container">
  <div class="row login-container animated fadeInUp">  
        <div class="col-md-7 col-md-offset-2 tiles white no-padding">
		 <div class="p-t-30 p-l-40 p-b-20 xs-p-t-10 xs-p-l-10 xs-p-b-10"> 
          <h2 class="normal">Username/Password Recovery</h2>
          <p class="p-b-20 general">
          Please provide your email to get username/password change link.
          </p>
		  
        </div>
        <?php echo $this->session->flashdata('message'); ?>
        <div class="tiles grey p-t-20 p-b-20 text-black">
			  <form id="frm_login" name="frm_login" class="animated fadeIn" method="post" action="<?php echo site_url('company/sendforgot');?>">
			  		<div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
					  <input type="hidden" id="reqtype" name="type" value=""/>
                      <div class="col-md-6 col-sm-6">
                       <label class="form-label text-success semi-bold general">Email</label>
                       <input name="email" id="login_pass" type="email" class="form-control">
                      </div>
                      
                      <div class="col-md-3 col-sm-3">
                       <label class="form-label general">&nbsp;</label>
                       <button type="button" class="btn btn-primary btn-cons" id="login_toggle" onclick="$('#reqtype').val('username');document.frm_login.submit()">Get Username</button>
                      </div>
                      
                      <div class="col-md-3 col-sm-3">
                       <label class="form-label general">&nbsp;</label>
                       <button type="button" class="btn btn-primary btn-cons" id="login_toggle" onclick="$('#reqtype').val('reset');document.frm_login.submit()">Get Link</button>
                      </div>
                      <a href="<?php echo site_url('company');?>">
                       	Return to Login
                       </a>
                      

                    </div>
			  </form>
		</div> 
		  
      </div>   
  </div>
</div>
<!-- END CONTAINER -->