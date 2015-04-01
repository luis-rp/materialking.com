<script type="text/javascript">
$.noConflict();
 </script>


    <div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
			<h3>Forthcoming PO's </h3>		
		</div>		
	   <div id="container">      
		
		<?php 
		    	if($forthcoming)
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
                                                <th style="width:6%">View Details</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
							              <?php 
									    	$i = 0;
									    	foreach($forthcoming as $ponum=>$ftc)
									    	{ ?>
                                            <tr>
                                                <td id="cellid_<?php echo @$ponum;?>" class="v-align-middle"><?php echo $ponum;?>
                                                <!-- <a href="javascript:void(0)" onclick="showdetail('<?php echo @$bck2->id;?>');">Expand</a> -->						                                    </td>
                                                <td class="v-align-middle"><?php echo $ftc['podate'];?></td>
                                                <td class="v-align-middle">
                                                	<a class="btn-xs btn-mini" target="_blank" href="<?php echo site_url('quote/track/'.$ftc['quoteid'].'/'.$ftc['awardid']);?>">Details</a>
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
                  No Forthcoming PO items Detected.
                  </div>
                 </div>
      </div>
      
                <?php }?>
			
		</div>
	  </div>