<div class="container">
  <div class="row login-container animated fadeInUp">  
        <div class="col-md-7 col-md-offset-2 tiles white no-padding">
		 <div class="p-t-30 p-l-40 p-b-20 xs-p-t-10 xs-p-l-10 xs-p-b-10">
		 <p><strong><?php if(isset($sms)) echo $sms;?></strong></p> 
		 <h2 class="normal">Welcome To EZPZP</h2>
          <h2 class="normal">Login To Your Account</h2>
          <p class="p-b-20 general">Enter Your Username and Password 
    	  Or <a href="<?php echo site_url('company/register');?>">Create New Account</a></p>
    	  <p class="p-b-20 general"><a href="<?php echo site_url('company/forgot');?>">Forgot your username/password?</a></p>
          <?php if($message){?>
          		<div class="alert alert-error col-md-11">
                  <button data-dismiss="alert" class="close"></button>
                  <?php  echo $message; ?>
                </div>
                <p>&nbsp;</p><br/>
          <?php  }?>
        </div>
        
        <?php if(!isset($sms)) { echo $this->session->flashdata('message'); }?>
        
		<div class="tiles grey p-t-20 p-b-20 text-black">
			  <form id="frm_login" name="frm_login" class="animated fadeIn" method="post" action="<?php echo site_url('company/checklogin');?>">
			  		
                    <div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
                      <div class="col-md-6 col-sm-6 ">
                       <label class="form-label text-success semi-bold general">Login User Name</label>
                        <input name="username" id="login_username" type="text"  class="form-control">
                      </div>
                      
                      <div class="col-md-6 col-sm-6">
                       <label class="form-label text-success semi-bold general">Password</label>
                        <input name="password" id="login_pass" type="password"  class="form-control">
                      </div>
                     
                      <div class="col-md-3 col-sm-3 pull-right">
                        <button type="submit" class="btn btn-primary btn-cons general" id="login_toggle" onclick="document.frm_login.submit()">Login</button>
                      </div>
                    </div>
			  </form>
		</div> 
      </div>   
  </div>
</div>
<!-- END CONTAINER -->