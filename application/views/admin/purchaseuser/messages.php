<?php //echo '<pre>';print_r($messages);die;?>
<script type="text/javascript">
<!--
$(document).ready(function(){
	
});
function sendnewmessage()
{
	var a = $("#newmsgform").attr('action');
	var q = $("#quote").val();
	a = a.replace('quote_id',q);
	alert(a);
	return false;
}
</script>

<section class="row-fluid">
	<h3 class="box-header">Messages</h3>
	<?php echo $this->session->flashdata('message'); ?>
	<div class="box">
	  <div class="span12">
			<?php 
			if($messages)
			{
				foreach ($messages as $po)
				{
			?>
			<h4>PO#:<?php echo $po['quote']['ponum'];?></h4>
			<div class="box widget-chat">
			<?php 
				foreach($po['messages'] as $msg)
				{
					$company = $msg->company;
					$companyname = $msg->companyname;
				?>		
				<div class="<?php if(strpos($msg->to, '(Admin)') > 0){ echo "message right"; } else { echo "message";}?>">
					<img alt="" src="<?php echo base_url(); ?>templates/admin/images/avatar.png">
					<div>
					<?php echo $msg->showdate;?>
					<strong><?php echo $msg->from;?></strong> says:
						<span class="pull-right"><?php echo $msg->showago;?></span>
						<div>
							<?php echo $msg->message;?>
                            <?php if(strpos($msg->to, '(Admin)') > 0){?>
            				<div class="widget-actions">
            					<form class="form-inline" method="post" 
            						action="<?php echo site_url('admin/message/sendmessage/'.$po['quote']['id'].'/messages/'.$filterquote);?>">
            						<input type="hidden" name="quote" value="<?php echo $po['quote']['id']?>"/>
            				    	<input type="hidden" name="company" value="<?php echo $company;?>"/>
            				    	<input type="hidden" name="from" value="<?php echo $this->session->userdata('fullname')?> (Admin)"/>
            				    	<input type="hidden" name="to" value="<?php echo $companyname;?>"/>
            				    	<input type="hidden" name="ponum" value="<?php echo $po['quote']['ponum'];?>"/>
            				    	
            						<input type="submit" value="Reply" class="btn btn-primary"/>
            						<div>
            							<textarea id="textarea-chat-example" name="message" required rows="1" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 45px;"></textarea>
            						</div>
            					</form>
            				</div>
    		    		    <?php }?>
						</div>
					</div>
				</div>

				<?php }?>
				<?php if(0){?>
				<?php }?>
			</div>
			
			<?php 
				}
			}
			else 
			{
				echo 'No Messages.';
			}
		    ?>
			
		</div>
    </div>
</section>