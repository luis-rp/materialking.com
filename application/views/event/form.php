          
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<script src="<?php echo base_url();?>templates/admin/js/jquery.ui.autocomplete.html.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">


<script event="text/javascript">
<!--
$(document).ready(function(){
	$('.date').datepicker({dateFormat:'mm/dd/yy'});
	$('.time').timepicker();
});
//-->
</script>


<div class="content">
  <div class="container">
  	<div class="row">
  	  <div class="col-md-12">
         <div class="grid simple ">
            <div class="grid-title no-border">
               <?php  if(isset($message)) echo $message; else echo ''; ?>
            <h4><?php echo $heading; ?></h4>
            </div>

                  <div class="grid-body no-border">
                   <div class="row">
            		   <form class="animated fadeIn" method="post" encevent="multipart/form-data" action="<?php echo $action; ?>"> 
   <input type="hidden" name="id" value="<?php echo $this->validation->id;?>"/>
    <br/>
    
    <div class="form-group">
    <label class="form-label">Event Name:</label>
    <div class="controls">
      <input type="text" id="title" name="title" class="span4" value="<?php echo $this->validation->title; ?>">
      <?php echo $this->validation->title_error;?>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">Date:</label>
    <div class="controls">
        <input type="text" style="width:180px;" name="evtdate" class="span3 date" value="<?php if($this->validation->evtdate) echo date("m/d/Y", strtotime($this->validation->evtdate)); ?>">
        <?php echo $this->validation->evtdate_error;?>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">Start:</label>
    <div class="controls">
        <input type="text" id="eventstart" name="eventstart" class="span2 time" value="<?php echo $this->validation->eventstart; ?>">&nbsp; &nbsp; eg: 06:00 AM
        <?php echo $this->validation->eventstart_error;?>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">End:</label>
    <div class="controls">
        <input type="text" id="eventend" name="eventend" class="span2 time" value="<?php echo $this->validation->eventend; ?>">&nbsp; &nbsp; eg: 09:00 PM
        <?php echo $this->validation->eventend_error;?>
    </div>
    </div>
    
    <div class="control-group">
	    <label class="control-label">Location</label>
	    <div class="controls">
	      <textarea id="location" class="span5" rows="4" name="location" ><?php echo $this->validation->location; ?></textarea>
	      <?php echo $this->validation->location_error;?>
	    </div>
    </div>
    
    <div class="control-group">
	    <label class="control-label">Notes</label>
	    <div class="controls">
	      <textarea id="notes" class="span7" rows="6" name="notes" ><?php echo $this->validation->notes; ?></textarea>
	      <?php echo $this->validation->notes_error;?>
	    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">Contact Name:</label>
    <div class="controls">
      <input type="text" id="contactname" name="contactname" class="span4" value="<?php echo $this->validation->contactname; ?>">
      <?php echo $this->validation->contactname_error;?>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">Contact Phone:</label>
    <div class="controls">
      <input type="text" id="contactphone" name="contactphone" class="span4" value="<?php echo $this->validation->contactphone; ?>">
      <?php echo $this->validation->contactphone_error;?>
    </div>
    </div>
    
    <!-- <div class="control-group">
    <label class="control-label">Users:</label>
    <div class="controls">
	      <table class="table table-bordered span6">
	      	<tr valign="top">
	      		<td>
	      			<?php foreach($users as $u){?>
	      			<input name="users[]" type="checkbox" value="<?php echo $u->id;?>" <?php echo $u->checked;?>>
	      			<?php echo $u->fullname;?>
	      			<br/>
	      			<?php }?>
	      		</td>
	      	</tr>
	      </table>
    </div>
    </div> -->
    
   
    <div class="control-group">
    <label class="control-label">&nbsp;</label>
    <div class="controls">
     <input type="submit" class="btn btn-primary" value="Save"/>
    </div>
    </div>
    
  </form>
                 </div>
              </div>
          </div>
  		</div>
	</div>