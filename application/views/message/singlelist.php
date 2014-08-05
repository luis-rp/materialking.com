
                     <div class="combofixed">       
                       <form method="post" class="form-inline" action="<?php echo site_url('message') ?>">
                        <div class="form-group">
                        <label class="form-label">Select Company</label>
                        <span>
                         	<select name="searchpurchasingadmin" class="form-control selectpicker show-tick" style="width:auto" onchange="this.form.submit()">
                            	<option value=''>All</option>
                            	<?php foreach($purchasingadmins as $pa){?>
                            	<option value='<?php echo $pa->id;?>' <?php if(@$_POST['searchpurchasingadmin'] ==$pa->id){echo 'SELECTED';}?>><?php echo $pa->fullname;?></option>
                            	<?php }?>
                            </select>
                        </span>
                      </div>
                     </form>
					</div>
        <div class="content">
        	<?php echo $this->session->flashdata('message'); ?>
    		<div class="page-title">	
    			<h3>
    				Messages
    			</h3>
    			<a href="<?php echo site_url('message');?>">&lt; &lt; View All</a>
    		</div>		
    	   <div id="container">
    		
    		
    	<div class="row">
    	<?php 
    	if($messages)
    	{
		    	foreach ($messages as $po)
		    	{
		 ?>
		 
        <div class="col-md-10 col-vlg-7">
       <div class="page-title">	<i class="icon-custom-left"></i>
                    	<h3>
                    	PO:
						<?php if($po['quote']['status'] == 'Awarded'){?>
                    	<a href="<?php echo site_url('quote/track/'.$po['quote']['id']);?>">
		 				<?php echo $po['quote']['ponum'];?>
		 				</a>
		 				<?php }elseif($po['quote']['invitation']){?>
                    	<a href="<?php echo site_url('quote/invitation/'.$po['quote']['invitation']);?>">
		 				<?php echo $po['quote']['ponum'];?>
		 				</a>
		 				<?php }?>
		 				- <?php echo $po['quote']['status']?>
		 				<?php if($po['quote']['status'] == 'Awarded'){?>
		 				(<?php echo $po['quote']['awarditems'];?> items awarded.)
		 				<?php }?>
		 				<a href="<?php echo site_url('message/messages/'.$po['quote']['messagekey']);?>">
		 					EXPAND
		 				</a>
		 				
		 				<?php if($po['quote']['complete'] == 'Yes'){?>
		 				<a style="color:red;" href="<?php echo site_url('message/archivemessage/'.$po['quote']['id']);?>"> Archive </a>
		 				<?php }?>
		 				
                    	</h3>
                </div>
          <ul class="cbp_tmtimeline">
          
          <?php
		    	foreach($po['messages'] as $msg)
		    	{
		    ?>
            <li>
              <time class="cbp_tmtime" datetime="2013-04-10 18:30">
                <span class="messagedate"><?php echo $msg->showago;?></span>
                <span class="time"><?php echo $msg->showdate;?></span>
              </time>
              <div class="<?php if(strpos($msg->from, '(Admin)') && 0){ echo 'cbp_tmicon success animated bounceIn'; } else { echo 'cbp_tmicon primary animated bounceIn'; } ?>"> <i class="fa fa-comments"></i> </div>
              <div class="cbp_tmlabel">
                <div class="p-t-10 p-l-30 p-r-20 p-b-20 xs-p-r-10 xs-p-l-10 xs-p-t-5">
                  <h4 class="inline m-b-5"><span class="text-success semi-bold"><?php echo $msg->from;?></span> </h4>
                  <h5 class="inline muted semi-bold m-b-5"><?php echo $msg->showemail;?></h5>
                  <p class="m-t-5 dark-text general"><?php echo $msg->message;?></p>
                </div>
                <div class="clearfix"></div>
                <div class="tiles grey p-t-10 p-b-10 p-l-20">
                  <ul class="action-links">
                    <li>
                        <?php if(strpos($msg->from, '(Admin)')){?>
    		    		<a class="btn btn-danger btn-sm btn-small" href="<?php echo site_url('message/viewmessage/'.$msg->id);?>">
    		    			<i class="fa fa-comments"></i> Reply
    		    		</a>
		    		    <?php }?>
    		    		<?php if($msg->user_attachment){?>
    		    		
    		    		<a class="btn btn-danger btn-sm btn-small" target="_blank" href="<?php echo site_url('uploads/messages/'.$msg->user_attachment);?>">Download Attachment</a>
    		    		
    		    		<?php }?>
		    		</li>
                  </ul>
                  <div class="clearfix"></div>
                </div>
              </div>
            </li>
           
            <?php break;} ?>
           
          </ul>
        </div>
        
        <?php } ?>

      <?php } else {?>
    
      <div class="errordiv">
      <div class="alert alert-info">
                  <button data-dismiss="alert" class="close"></button>
                  <div class="msgBox">
                  No Message detected.
                  </div>
                 </div>
      </div>
                  
      <?php } ?>
      
			
		</div>
	  </div> 
</div>