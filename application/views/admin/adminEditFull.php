<script type="text/javascript">
<!--
$(document).ready(function(){
	$('#about').wysihtml5();
});
//-->
</script>
<?php echo $this->session->flashdata('message'); ?>
<section class="row-fluid">
	<h3 class="box-header"><?php echo $title; ?></h3>
	<div class="box">
	<div class="span9">
	

  <div class="pull-left" style="width:70%;">
   <form class="form-horizontal" method="post" action="<?php echo $action; ?>" enctype="multipart/form-data">
   <input type="hidden" name="id" value="<?php echo $this->validation->id;?>"/>
    <br/>
    <?php //if($this->session->userdata('usertype_id') == 1){?>
   
     
   <!-- <div class="control-group">
    <label class="control-label" for="category" required>Category</label>
    <div class="controls">
    <select name="category">
	 <?php foreach($categories as $cat){?>
	  <option value='<?php echo $cat->id;?>' <?php if($this->validation->category==$cat->id){ echo 'SELECTED'; }?>><?php echo $cat->catname;?></option>
     <?php }?>
	</select>
    </div>
    </div>-->   
   
 
        
    <div class="control-group">
    <label class="control-label" for="username">User Name:</label>
    <div class="controls">
    <input type="text" name="username" class="span4" class="text" required value="<?php echo $this->validation->username; ?>"/>
	<?php echo $this->validation->username_error; ?>
    </div>
    </div>

    <div class="control-group">
    <label class="control-label" for="username">Company Name:</label>
    <div class="controls">
    <input type="text" name="companyname" class="span4" class="text" required value="<?php echo $this->validation->companyname; ?>"/>
    <?php echo $this->validation->companyname_error; ?>
    </div>
    </div>

    <div class="control-group">
    <label class="control-label" for="email">Email Address*</label>
    <div class="controls">
   <input type="email" name="email" required class="text span4" value="<?php echo $this->validation->email; ?>"/>
    <?php echo $this->validation->email_error; ?>
    </div>
    </div>

    <div class="control-group">
    <label class="control-label" for="email">Password</label>
    <div class="controls">
   <input type="password" name="password" required class="text span4" value=""/>
    <?php echo $this->validation->password_error; ?>
    </div>
    </div>


    <div class="control-group">
    <label class="control-label" for="email">Other Emails:</label>
    <div class="controls">
    </div>
    </div>

    <div class="control-group">
    <label class="control-label" for="email">Short Details: (300 characters only)</label>
    <div class="controls">
    </div>
    </div>


    <div class="control-group">
    <label class="control-label" for="about">About:</label>
    <div class="controls">
      <textarea rows="10" cols="40" class="span10" id="about" name="about"><?php echo  $this->validation->about ?></textarea>
    </div>
    </div>

    <br>

    <p>Contact:,Company Type:</p>
  

     <div class="control-group">
    <label class="control-label" for="city">City</label>
        <div class="controls">
          <input type="text" class="span4" name="city" id="city" value="<?php echo $this->validation->city;?>" required>
        </div>
    </div>

     <div class="control-group" >
        <label class="control-label" for="state">State:</label>
        <div class="controls">
            <input type="text" class="span4" name="state" id="state" value="<?php echo $this->validation->state;?>" >
        </div>
      </div>

    <div class="control-group" >
        <label class="control-label" for="zip">Zip:</label>
        <div class="controls">
           <input type="text" class="span4" name="zip" id="zip" value="<?php echo $this->validation->zip;?>" required>
        </div>
      </div>

       <div class="control-group">
        <label class="control-label">Street Address *</label>
        <div class="controls">
          <textarea rows="2"  class="span4" name="street" required><?php echo $this->validation->street; ?></textarea>
            
        </div>
      </div>

       <div class="control-group">
        <label class="control-label">Phone:</label>
        <div class="controls">
            <input type="text" class="span4" name="phone" id="phone" value="<?php echo $this->validation->phone;?>">
            <?php echo $this->validation->phone_error; ?>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label">Fax:</label>
        <div class="controls">
            <input type="text" class="span4" name="fax" id="fax" value="<?php echo $this->validation->fax;?>">
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="avatarlink">Avatar</label>
        
        <div class="controls">
          <input type="file"  name="avatarlink" id="avatarlink"/>
           <input type="submit" value="Save/Upload" class="btn btn-primary btn-xs" >
          <?php if($this->validation->avatarlink){?>
          <br/>
          <img src="<?php echo site_url('uploads/avatar/'.$this->validation->avatarlink);?>" width="100" height="100"/>
          <?php }?>
        </div>
      </div>

      <!-- <div class="control-group">
        <label class="form-control">Logo:</label>
        <div class="controls">
          <input type="text" class="span4" name="fax" id="fax" value="">
        </div>
      </div>

       <div class="control-group">
        <label class="control-label">Invoice Notes:</label>
        <div class="controls">
          <textarea rows="2" cols="40" class="span4" name="invoicenote"></textarea>
        </div>
      </div>

      <div class="form-group">
        <label class="control-label">Team:</label>
        <div class="controls">
          
        </div>
      </div>
      <div class="form-group">
        <label class="control-label">Facebook Page URL:</label>
        <div class="controls">
          <input type="text" class="span4" name="fbpageurl" id="fbpageurl" value="">
        </div>
      </div>

      <div class="form-group">
        <label class="control-label">Business Hours:</label>
        <div class="controls">
         
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Disable Welcome Tour</label>
            <div class="controls">                                      
                <input name="pagetour" type="checkbox" >  
            </div>
      </div>

  
    
    <div class="control-group">
    <label class="control-label" for="fullname">Full Name *</label>
    <div class="controls"> 		
	<input type="text" name="fullname" class="span4" class="text" required value="<?php echo $this->validation->fullname; ?>"/>
	<?php echo $this->validation->fullname_error; ?>
    </div>
    </div>   
  
    
    
    <?php if(!$this->validation->id){?>
    <div class="control-group">
    <label class="control-label">Password *</label>
    <div class="controls">
    <input type="password" name="password" id="password" class="span4" class="text" value="<?php echo $this->validation->password; ?>" required/>
	<?php echo $this->validation->password_error; ?>
    </div>
    </div>
    <?php }?>

    <?php if($this->session->userdata('usertype_id') < 3){?>
    <div class="control-group">
    <label class="control-label">Status</label>
    <div class="controls">
  	<input type="radio" name="status" value="1" <?php echo $this->validation->set_radio('status', '1'); ?>/> Active <br/><br/>
	<input type="radio" name="status" value="0" <?php echo $this->validation->set_radio('status', '0'); ?>/> Deactive
	<?php echo $this->validation->status_error; ?>
    </div>
    </div>
    <?php }else{?>
    <input type="hidden" name="status" value="<?php $this->validation->status;?>"/>
    <?php } ?>-->


    <div class="control-group">
    <label class="control-label">&nbsp;</label>
    <div class="controls">
     <input type="submit" class="btn btn-primary" value="Save"/>
    </div>
    </div>
    
  </form>
  
         </div><!-- End of Pull left -->
   <?php if(isset($adminusers) && count($adminusers) > 0) { ?>
	   <div class="pull-right" style="width:26%;">
		   <div class="table-responsive">
			   <h3>Existing Users</h3>
				  <table class="table table-hover">
				  <tr><th>User Name</th></tr>
				    <?php foreach ($adminusers as $adminuser) { ?>
				  		<tr><td><?php echo $adminuser->fullname; ?></td></tr>
				     <?php } ?>
				  </table>
			</div>
	   </div><!-- End of Pull right -->
   <?php } ?>
	   
	   <div style="clear:both;"></div>
    
    </div>
    </div>
</section>
