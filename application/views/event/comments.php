<?php //echo '<pre>';print_r($messages);die;?>
<script type="text/javascript">
<!--
$(document).ready(function(){
	
});
</script>
<div class="content">
  <div class="container">
  	<div class="row">	
  		<div class="col-md-12">
         <div class="grid simple ">
		   <div class="grid-title no-border">
               <?php  echo $this->session->flashdata('message'); ?>
            <h4><b><?php echo $event->title;?></b></h4>
            </div>
	  
	  	<table class="table table-bordered  col-lg-10">
	  		<tr>
	  			<td><strong>Title</strong></td>
	  			<td><?php echo $event->title;?></td>
	  		</tr>
	  		<tr>
	  			<td><strong>Date</strong></td>
	  			<td><?php echo date("m/d/Y", strtotime( $event->evtdate));?></td>
	  		</tr>
	  		<tr>
	  			<td><strong>Start</strong></td>
	  			<td><?php echo $event->eventstart;?></td>
	  		</tr>
	  		<tr>
	  			<td><strong>End</strong></td>
	  			<td><?php echo $event->eventend;?></td>
	  		</tr>
	  		<tr>
	  			<td><strong>Location</strong></td>
	  			<td><?php echo $event->location;?></td>
	  		</tr>
	  		<tr>
	  			<td><strong>Notes</strong></td>
	  			<td><?php echo $event->notes;?></td>
	  		</tr>
	  		<tr>
	  			<td><strong>Contact Name</strong></td>
	  			<td><?php echo $event->contactname;?></td>
	  		</tr>
	  		<tr>
	  			<td><strong>Contact Phone</strong></td>
	  			<td><?php echo $event->contactphone;?></td>
	  		</tr>
	  	</table>
	  	<a href="<?php echo site_url('event/update/'.$event->id);?>"><input type="button" value="Edit" class="btn btn-primary"/></a>
	 
	  <!-- <div class="span6">
			<h4>Comments</h4>
			<?php 
			if($comments)
			{
			?>
			<div class="box widget-chat">
			<?php 
				foreach ($comments as $msg)
				{
				?>		
				<div class="message">
					<img alt="" src="<?php echo base_url(); ?>templates/admin/images/avatar.png">
					<div>
    					<?php echo date("m/d/Y", strtotime($msg->commentdate));?>
    					<strong><?php echo $msg->from;?></strong> says:
						<span class="pull-right"><?php //echo $msg->showago;?></span>
					</div>
        			<div>
        				<?php echo $msg->comment;?>
        			</div>
				</div>

				<?php }?>
			</div>
			
			<?php 
			}
			else
			{
			    echo 'No comments posted for this event yet.';
			}
		    ?>
			<form class="form-inline" method="post" 
				action="<?php echo site_url('event/sendcomment/'.$event->id);?>">
				<input type="hidden" name="event" value="<?php echo $event->id?>"/>
		    	<input type="hidden" name="user" value="<?php echo $this->session->userdata('id')?>"/>
		    	<input type="hidden" name="commentdate" value="<?php echo date('Y-m-d H:i:s');?>"/>
		    	
				<textarea id="textarea-chat-example" name="comment" required rows="50" 
				style="word-wrap: break-word; resize: horizontal; height: 100px; width: 500px"></textarea>
				<br/><br/>
				<input type="submit" value="Send" class="btn btn-primary"/>
			</form>
			
		</div> -->
    </div>
   </div>
  </div>
 </div>
</div> 