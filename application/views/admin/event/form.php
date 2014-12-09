
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

<section class="row-fluid">
	<h3 class="box-header mainheading"><?php echo $heading; ?></h3>
	<div class="box">
	<div class="span12">
	
	 <div class="pull-left" style="width:70%;">
	
	  <?php if(@$message){echo '<div class="alert alert-block alert-danger fade in"><button event="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>'.$message.'</div>';}?>
	  <?php echo $this->session->flashdata('message'); ?>
	  <?php if($this->validation->id){?>
	  <a href="<?php echo site_url('admin/event/delete/'.$this->validation->id);?>"
	  	 onclick="return confirm('Do you want to delete this event?');"
	  	 class="btn btn-primary">
	  	 Delete
	  </a>
	  <a href="<?php echo site_url('admin/event/comments/'.$this->validation->id);?>"
	  	 
	  	 class="btn btn-primary">
	  	 Comments
	  </a>
	  <?php }?>
   <form class="form-horizontal" method="post" encevent="multipart/form-data" action="<?php echo $action; ?>"> 
   <input type="hidden" name="id" value="<?php echo $this->validation->id;?>"/>
    <br/>
    
    <div class="control-group">
    <label class="control-label">Event Name:</label>
    <div class="controls">
      <input type="text" id="title" name="title" class="span4" value="<?php echo $this->validation->title; ?>">
      <?php echo $this->validation->title_error;?>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">Date:</label>
    <div class="controls">
        <input type="text" name="evtdate" class="span3 date" value="<?php if($this->validation->evtdate) echo date("m/d/Y", strtotime($this->validation->evtdate)); ?>">
        <?php echo $this->validation->evtdate_error;?>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">Start:</label>
    <div class="controls">
        <input type="text" id="eventstart" name="eventstart" class="span2 time" value="<?php echo $this->validation->eventstart; ?>">
        <?php echo $this->validation->eventstart_error;?>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">End:</label>
    <div class="controls">
        <input type="text" id="eventend" name="eventend" class="span2 time" value="<?php echo $this->validation->eventend; ?>">
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
    
    <div class="control-group">
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
    </div>
    
   
    <div class="control-group">
    <label class="control-label">&nbsp;</label>
    <div class="controls">
     <input type="submit" class="btn btn-primary" value="Save"/>
    </div>
    </div>
    
  </form>
    
       </div><!-- End of Pull left -->
 
   <?php if(isset($events) && count($events) > 0) { ?>
	   <div class="pull-right" style="width:26%;">
		   <div class="table-responsive">
			   <h3>Existing Events</h3>
				  <table class="table table-hover">
				  <tr><th>Event Name</th><th>Event Date</th></tr>
				    <?php foreach ($events as $event) { ?>
				  		<tr><td><?php echo $event->title; ?></td><td><?php echo $event->evtdate; ?></td></tr>
				     <?php } ?>
				  </table>
			</div>
	   </div><!-- End of Pull right -->
   <?php } ?>
	   
	   <div style="clear:both;"></div>
  
    </div>
    </div>
</section>