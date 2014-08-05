<script type="text/javascript">
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

function showReplyBox(val)
{
	$("#replymsg_"+val).css("display","");
}

function clearFilters()
{	
	$("#searchmsg").val("");
	$("#ponumsearch").val("");
}

</script>

<section class="row-fluid">
	<h3 class="box-header">Messages</h3>
	<?php echo $this->session->flashdata('message'); ?>
	<div class="box">
	  <div class="span12">
	  <form name="frmMsg" id="frmMsg" class="form-inline" method="post" 
            						action="<?php echo site_url('admin/message/messages');?>">
	  <table id="filters" name="filters" width="80%">
	  <tr>
	  		<td><strong>Search PO Number</strong></td>
	   		<td><input type="text" name="ponumsearch" id="ponumsearch" style="width:240px;" class="input" value="<?php if(isset($ponumsearch) && $ponumsearch!="") echo $ponumsearch;?>"> </td>
	   		<td><strong>Search Message</strong></td>
	   		<td><input type="text" name="searchmsg" id="searchmsg" style="width:240px;;" class="input" value="<?php if(isset($searchmsg) && $searchmsg!="") echo $searchmsg;?>"> </td>
	   		<td><strong>Sort By </strong></td>
	  		<td>
	   			<select name="sortby" id="sortby" style="width:100px;">
	   			<option value="date" <?php if(isset($sortbyoption) &&  $sortbyoption =='date') echo " selected ";else echo ''?> >Date</option>
	   			<option value="ponumber" <?php if(isset($sortbyoption) && $sortbyoption =='ponumber') echo " selected ";else echo ''?>>PO Number</option>	   			
	   			<option value="company" <?php if(isset($sortbyoption) && $sortbyoption =='company') echo " selected ";else echo ''?>>Company</option>
	   			</select>
	   		</td> 	 
	   		<td><input class="btn btn-primary" type="submit" value="Search"></td>
	   		<td><input class="btn btn-primary" type="submit" value="Reset" onclick="clearFilters();"></td>
		</tr>
		</table>
		</form>
			<?php 			
			if($messages)
			{
				foreach ($messages as $po)
				{
			?>
			<h4>PO#:<?php echo $po['quote']['ponum'];?> &nbsp;&nbsp;<a style="font-size:12px;font-weight:normal;" target="_blank" href="<?php echo site_url().'admin/quote/bids/'.$po['quote']['id'];?>">View Bids Page</a> - &nbsp;<a style="font-size:12px;font-weight:normal;" target="_blank" href="<?php echo site_url().'admin/quote/track/'.$po['quote']['id'] ;?>">View Tracking Page</a>   </h4>
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
						<span class="pull-right"><?php echo $msg->showago;?> <img src="<?php echo base_url();?>templates/admin/images/mail_reply_sender.png" onclick="showReplyBox(<?php echo $msg->id;?>);" title="Reply"> </span>
						<div>
							<?php echo $msg->message;?>
                            <?php //if(strpos($msg->to, '(Admin)') > 0){?>
            				<!--<div class="widget-actions">-->
            					<form name="frmMessage" id="frmMessage" class="form-inline" method="post" 
            						action="<?php echo site_url('admin/message/sendmessage/'.$po['quote']['id'].'/messages/'.$filterquote);?>">
            						<input type="hidden" name="quote" value="<?php echo $po['quote']['id']?>"/>
            				    	<input type="hidden" name="company" value="<?php echo $company;?>"/>
            				    	<input type="hidden" name="from" value="<?php echo $this->session->userdata('fullname')?> (Admin)"/>
            				    	<input type="hidden" name="to" value="<?php echo $companyname;?>"/>
            				    	<input type="hidden" name="ponum" value="<?php echo $po['quote']['ponum'];?>"/>
            				    	
            						
            						<div class="widget-actions" id="replymsg_<?php echo $msg->id;?>" style="display:none;">
            							<textarea id="textarea-chat-example" name="message" required rows="1" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 45px;"></textarea><br>
            							<input type="submit" value="Send" class="btn btn-primary"/>
            						</div>
            					</form>
            				<!--</div>-->
    		    		    <?php // } ?>
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