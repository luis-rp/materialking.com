<?php //print_r($states);die;?>
<div class="container">
  <div class="row login-container animated fadeInUp">  
        <div class="col-md-7 col-md-offset-2 tiles white no-padding">
		 <div class="p-t-30 p-l-40 p-b-20 xs-p-t-10 xs-p-l-10 xs-p-b-10"> 
          <h2 class="normal">Create New Account!</h2>
          <p class="p-b-20 general">
              Please fill up below form to create a new account. 
              <br/>
              Once you complete you get a link to provided email to activate your account.
          </p>
		  <p class="p-b-20 general"><a href="<?php echo site_url('company/resend');?>">Resend Activation Link?</a></p>
        </div>
        
        <?php echo $this->session->flashdata('message'); ?>
        <div class="tiles grey p-t-20 p-b-20 text-black">
			  <form id="frm_login" name="frm_login" class="animated fadeIn" method="post" action="<?php echo site_url('company/saveregister');?>">
			  		<div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
                      <div class="col-md-6 col-sm-6 ">
                       <label class="form-label text-success semi-bold general">Company Name *</label>
                        <input name="title" id="title" type="text"  class="form-control" required>
                      </div>
                      
                      <div class="col-md-6 col-sm-6">
                       <label class="form-label text-success semi-bold general">Email *</label>
                       <input name="primaryemail" id="primaryemail" type="text"  class="form-control" required>
                      </div>
                      
                       <div class="col-md-6 col-sm-6">
                       <label class="form-label text-success semi-bold general">Contact Name *</label>
                       <input name="contact" id="contact" type="text" class="form-control" required>
                      </div>
                      
                      <div class="col-md-6 col-sm-6">
                       <label class="form-label text-success semi-bold general">Street Address*</label>
                       <input name="street" id="street" type="text"  class="form-control" required>
                      </div>
                      
                      <?php if(1){?>
                       <div class="col-md-6 col-sm-6">
                       <label class="form-label text-success semi-bold general">City*</label>
                       <input name="city" id="city" type="text" class="form-control" required>
                      </div>
                      
                      <div class="col-md-6 col-sm-6">
                       	 <label class="form-label text-success semi-bold general">State*</label>
                         <select name="state" id="state" required>
	                        <?php foreach($states as $st){?>
                        	<option value='<?php echo $st->state_abbr;?>'><?php echo $st->state_name;?></option>
                        	<?php }?>
                         </select>
                      </div>
                      
                       <div class="col-md-6 col-sm-6">
                       <label class="form-label text-success semi-bold general">Zip*</label>
                       <input name="zip" id="zip" type="text" class="form-control" required>
                      </div>
                      
                      <?php }?>
                      
                       <div class="col-md-6 col-sm-6">
                       <label class="form-label text-success semi-bold general">Phone*</label>
                       <input name="phone" id="phone" type="text" class="form-control" required>
                      </div>
                      
                       <div class="col-md-6 col-sm-6">
                       <label class="form-label text-success semi-bold general">Fax</label>
                       <input name="fax" id="fax" type="text" class="form-control">
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