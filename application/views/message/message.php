
    <div class="content">  
		<div class="page-title">	
			<h3>Send Reply </h3>		
		</div>		
	   <div id="container">
		
		<div class="row">
     	
     	<div class="col-md-12">
              <div class="grid simple">
                <div class="grid-title no-border">
                  <h4>&nbsp;</h4>
                  <div class="tools"> <a class="collapse" href="javascript:;"></a> <a class="config" data-toggle="modal" href="#grid-config"></a> <a class="reload" href="javascript:;"></a> <a class="remove" href="javascript:;"></a> </div>
                </div>
                
                <div class="grid-body no-border">
                  <div class="row">
                    <div class="col-md-8 col-sm-8 col-xs-8">
                     <form method="post" action="<?php echo site_url('message/sendmessage/'.$message->quote)?>">
                        <input type="hidden" name="purchasingadmin" value="<?php echo $message->purchasingadmin;?>"/>
                        <input type="hidden" name="adminid" value="<?php echo $message->adminid;?>"/>
                        <input type="hidden" name="quote" value="<?php echo $message->quote;?>"/>
    			    	<input type="hidden" name="company" value="<?php echo $company->id;?>"/>
    			    	<input type="hidden" name="from" value="<?php echo $company->title;?>"/>
    			    	<input type="hidden" name="to" value="<?php echo $message->from;?>"/>
			    	
                      <div class="form-group general">
                        <label class="form-label">From:</label>
                        <span> <?php echo $message->from;?></span>
                        <div class="controls">
                        </div>
                      </div>
                      
                      
                      <div class="form-group general">
                        <label class="form-label">To:</label>
                        <span> <?php echo $message->to;?></span>
                        <div class="controls">
                        </div>
                      </div>
                      
                
                       <div class="form-group general">
                        <label class="form-label">Date/Time:</label>
                        <span><?php echo date("m/d/Y H:i A", strtotime($message->senton));?></span>
                        <div class="controls">
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label class="form-label">Message:</label>
                        <span><?php echo nl2br($message->message);?></span>
                        <div class="controls">
                        </div>
                      </div>
                      
                      
                      <div class="form-group general">
                        <label class="form-label"><strong>Compose New Message</strong></label>
                        <div class="controls">
                           <textarea id="text-editor" name="message" class="form-control" rows="9" required></textarea>
                        </div>
                      </div>
                      
                       <div class="form-group general">
                        <label class="form-label">&nbsp;</label>
                        <div class="controls">
                        <input type="submit" value="Reply" class="btn btn-primary"/>
                        </div>
                      </div>
                    </form>
                     
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
        </div> 
			
		</div>
	  </div> 
  