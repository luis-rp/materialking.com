
    <div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
			<h3>Bid Back Orders </h3>		
		</div>		
	   <div id="container">
                     <div class="combofixed">       
                       <form method="post" class="form-inline" action="<?php echo site_url('quote/backtracks') ?>">
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
		
		<?php 
		    	if($backtracks)
		    	{
		    ?>
		<div class="row">
                    <div class="col-md-12">
                        <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                               
                            </div>
                            
                            <div class="grid-body no-border">
                                    <table class="table no-more-tables general">
                                        <thead>
                                            <tr>
                                                <th style="width:9%">PO Number</th>
                                                <th style="width:22%">PO Date</th>
                                                <th style="width:6%">Details</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
							              <?php
									    	$i = 0;
									    	foreach($backtracks as $bck)
									    	{
									    		$bck = $bck['quote'];
									    		$i++;
									      ?>
                                            <tr>
                                                <td class="v-align-middle"><?php echo $bck->ponum;?> </td>
                                                <td class="v-align-middle"><?php echo $bck->podate;?></td>
                                                <td class="v-align-middle">
                                                	<a class="btn btn-primary btn-xs btn-mini" href="<?php echo site_url('quote/viewbacktrack/'.$bck->id);?>">Details</a>
                                                </td>
                                            </tr>
                                          <?php } ?>
                                        </tbody>
                                    </table>
                                    
                                      <div class="row">
                                     	<ul class="pagination pull-right">
                                    	<?php echo @$pagination; ?>
                                    	</ul>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php } else {?>
                	    <div class="errordiv">
      				<div class="alert alert-info">
                  <button data-dismiss="alert" class="close"></button>
                  <div class="msgBox">
                  No Backorder Items Detected.
                  </div>
                 </div>
      </div>
      
                <?php }?>
			
		</div>
	  </div> 