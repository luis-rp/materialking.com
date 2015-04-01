<script type="text/javascript">
$.noConflict();
 </script>

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
	//	echo '<pre>',print_r($items);die;
		function bubble_sort($items) 
		{
		    $size = count($items);
		    
		    for ($i=0; $i<$size; $i++) 
		    {
		        for ($j=0; $j<$size-1-$i; $j++) 
		        {
		            if ($items[$j+1]->performance > $items[$j]->performance) 
		            {
		                swap($items, $j, $j+1);
		            }
		        }
		    }
		    return $items;
		}
		
		function swap(&$arr, $a, $b) 
		{
		    $tmp = $arr[$a];
		    $arr[$a] = $arr[$b];
		    $arr[$b] = $tmp;
		}
	
		$newItems = bubble_sort($items);
				
		if($newItems)
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
                                                <th style="width:20%">Itemcode</th>
                                                <th style="width:10%">Item Image</th>
                                                <th style="width:5%">Bids</th>
                                                <th style="width:5%">Awards</th>
                                                <th style="width:10%">Win Rate(%)</th>
                                                <th style="width:10%">Total Qty. via Bids</th>
                                                <th style="width:10%">Avg Order Qty. via Bids</th>
                                                <th style="width:10%">Store Sales</th>
                                                <th style="width:10%">Total Qty. Via Store</th><br>
												<th style="width:10%">Avg Order Qty. Via Store</th>
                                            </tr>
                                        </thead>
                                        <tbody>
							              <?php 
									    	$i = 0;
									    	foreach($newItems as $item)
									    	{									    		
									    		 if ($item->item_img && file_exists('./uploads/item/' . $item->item_img)) 
												 { 
												 	 $imgName = site_url('uploads/item/'.$item->item_img); 
												 } 
												 else 
												 { 
												 	 $imgName = site_url('uploads/item/big.png'); 
			                                     }
			                                     
			                                     $i++;
									      ?>
                                            <tr>
                                                <td class="v-align-middle"><?php echo $item->itemcode?$item->itemcode:$item->orgitemcode;?></td>
                                                <td><img style="max-height: 120px; padding: 0px;width:80px; height:80px;float:left;" src='<?php echo $imgName;?>'></td>
                                                <td class="v-align-middle"><?php echo $item->bidcount;?></td>
                                                <td class="v-align-middle"><?php echo $item->awardcount;?></td>
                                                <td class="v-align-middle"><?php echo $item->performance;?></td>
                                                <td class="v-align-middle"><?php echo $item->totalquantity;?></td>
                                                <td class="v-align-middle"><?php echo ceil($item->totalquantity/$item->bidcount);?></td>
                                                 <td class="v-align-middle"><?php echo round($item->storesales,2);?></td>
                                                <td class="v-align-middle"><?php echo $item->totalstoreqty;?></td>
                                                <td class="v-align-middle"><?php echo ceil($item->avgstoreqty);?></td>
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