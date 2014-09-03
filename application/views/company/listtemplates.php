
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>

    <div class="content">  
    	 <?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">
		 
			<h3>Newsletter Template List</h3>		
		</div>
	
	   <div id="container">
	   		        
		<?php 
		    	if($templates)
		    	{
		    ?>
		<div class="row">
				
                    <div class="col-md-12">
                        <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                            </div>
                            
                            <div class="grid-body no-border">
                            
                                    <table id="datatable" class="table no-more-tables general">
                                    <thead>
                                       <tr>
                  	             			<th style="width:20%">Name</th>
                  	             			<th># Send </th>
                  	             			<th># Errors</th>
                                   			<th>Action</th>
                                   			
                                         </tr>
									</thead>	
									<tbody>                                    
							              <?php
									    	foreach($templates as $tmp)
									    	{
									    		var_dump($tmp);
									    		?>
									      			
                                                		<tr>
                                                			<td class="v-align-middle"><?php echo $tmp->title;?> </td>
                                                			<td><?php echo $tmp->numSent;?></td>
                                                			<td><?php echo $tmp->numErrors;?></td>
                                                			<td><p><a href="<?php echo base_url("/company/edittemplate/".$tmp->id);?>"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo base_url("/subscriber/sendNewsletter/".$tmp->id);?>">Launch!!!</a></p></td>
                                                		</tr>
                                                		
                                                <?php } ?>
                                        </tbody>
                                    </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php } else {?>
       				<span style="display: block;position:absolute;z-index:9999;margin-top:10px; margin-left:30px;" class="label label-important">No Templates.</span>
                <?php }?>
			
		</div>
	  </div> 