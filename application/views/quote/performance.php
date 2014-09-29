<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/datatable.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>
                
 <script type="text/javascript" charset="utf-8">
                        $(document).ready( function() {
                        	$('#datatable').dataTable( {
                        		"sPaginationType": "full_numbers",
                        		"aoColumns": [
		                        		null,
		                        		null,
		                        		null,
		                        		null
                        		
                        			],
                        		"aaSorting": [[ 3, "desc" ]]
                        		} );

                        	jQuery.extend(jQuery.fn.dataTableExt.oSort, {
                        	    "percent-pre": function (a) {
                        	        var x = (a == "-") ? 0 : a.replace(/%/, "");
                        	        return parseFloat(x);
                        	    },

                        	    "percent-asc": function (a, b) {
                        	        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
                        	    },

                        	    "percent-desc": function (a, b) {
                        	        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
                        	    }
                        	});
                        	
                       	 $('.dataTables_length').hide();
                        })
                       
</script>
 
    <div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
		
			<h3>Performance By Item 	 <a href="<?php echo site_url('quote/performanceexport'); ?>" class="btn btn-primary btn-xs btn-mini">Export</a> &nbsp;<a href="<?php echo site_url('quote/performancePDF'); ?>" class="btn btn-primary btn-xs btn-mini">View PDF</a> </h3>		
		</div>		
	   <div id="container">
		
		<?php 
		    	if($items)
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
                                                <th style="width:25%">Itemcode</th>
                                                <th style="width:15%">Bids</th>
                                                <th style="width:25%">Awards</th>
                                                <th style="width:35%">Win Rate(%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
							              <?php
									    	$i = 0;
									    	foreach($items as $item)
									    	{
									    		$i++;
									      ?>
                                            <tr>
                                                <td class="v-align-middle"><?php echo $item->itemcode;?></td>
                                                <td class="v-align-middle"><?php echo $item->bidcount;?></td>
                                                <td class="v-align-middle"><?php echo $item->awardcount;?></td>
                                                <td class="v-align-middle"><?php echo $item->performance;?></td>
                                            </tr>
                                          <?php } ?>
                                        </tbody>
                                    </table>
                                    <br/>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php } else { ?>
                
                    <div class="errordiv">
      				 <div class="alert alert-info">
	                  <button data-dismiss="alert" class="close"></button>
	                  <div class="msgBox">
	                  No Quotations Detected on System.
	                  </div>
	                 </div>
      				</div>
                <?php }?>
			
		</div>
	  </div> 