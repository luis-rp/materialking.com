<script type="text/javascript">
$.noConflict();
 </script>

<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/datatable.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>
 
<script type="text/javascript" charset="utf-8">
                        $(document).ready( function() {
                        	$('#datatable').dataTable( {
                        		"aaSorting": [],
                        		"bPaginate": false,
                        		"sPaginationType": "full_numbers",
                        		"aoColumns": [
		                        		null,
		                        		null,
		                        		null,
		                        		{ "bSortable": false},
		                        		null,
		                        		null,
		                        		{ "bSortable": false},
		                        		{ "bSortable": false}
                        		
                        			]
                        		} );
                       	 $('.dataTables_length').hide();
                        })
                       
</script>

<style>
.awarded-to-me td
{
	color: green;
}
.not-awarded-to-me td
{
	color: red;
}
</style> 
    <div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">
			
			<h3>PO Performance : <?php echo $quote->ponum;?> <font color="green">Items Won : <?php echo $itemswon;?></font> 	 <a href="<?php echo site_url('quote/items_export/'.$quoteid); ?>" class="btn btn-primary btn-xs btn-mini">Export</a> 	&nbsp;&nbsp; <a href="<?php echo site_url('quote/items_pdf/'.$quoteid); ?>" class="btn btn-primary btn-xs btn-mini">View PDF</a> 
				&nbsp;&nbsp;&nbsp;
				<font color="red">Items Lost : <?php echo $itemslost;?></font>
				<?php if($award && $itemswon && @$award->id){?>
				<a class="btn btn-primary btn-xs btn-mini" href="<?php echo site_url('quote/track/'.$quote->id.'/'.$award->id);?>">Track</a>
				<?php }?>
				<?php if(isset($messagekey)){ ?>
				<a class="btn btn-primary btn-xs btn-mini" href="<?php echo site_url('message/messages/'.$messagekey);?>">View Messages</a>
				<?php } ?> 		<?php if($itemswon){?>
				<a class="btn btn-primary btn-xs btn-mini" href="<?php echo site_url('quote/getawardedpdf/'.$quote->id); ?>">View P.O as PDF</a>
			<?php }?>
		 
			<?php if($bid){?>
				<a class="btn btn-primary btn-xs btn-mini" href="<?php echo site_url('quote/viewbid/'.$bid->id); ?>">View Bid as HTML</a>
			<?php }?> </h3>
			<h4>
				
			</h4>
	
		</div>		
	   <div id="container">
		<?php 
		    	if(@$allawardeditems)
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
                                                <th style="width:20%">Item Code</th>
                                                <th style="width:15%">Item Image</th>
                                                <th style="width:25%">Item Name</th>
                                                <th style="width:5%">QTY.</th>
                                                <th style="width:5%">Unit</th>
                                                <th style="width:10%">Price</th>
                                                <th style="width:10%">Total</th>
                                                <th style="width:10%">Requested</th>
                                                <th style="width:25%">Notes</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
							              <?php
									    	$i = 0;
									    	foreach($allawardeditems as $ai)
									    	{   
									    		 $bidea = 0;
									    	
									    		 if ($ai->item_img && file_exists('./uploads/item/' . $ai->item_img)) 
												 { 
												 	 $imgName = site_url('uploads/item/'.$ai->item_img); 
												 } 
												 else 
												 { 
												 	 $imgName = site_url('uploads/item/big.png'); 
			                                     }
			                                    $totalprice = 0; 
									    		$i++;
									      ?>
                                            <tr class="<?php echo $ai->company == $company->id?'awarded-to-me':'not-awarded-to-me';?>">
                                                <td class="v-align-middle"><?php echo $ai->itemcode;?></td>
                                                <td class="v-align-middle"><img style="max-height: 120px; padding: 0px;width:80px; height:80px;float:left;" src='<?php echo $imgName;?>'></td>
                                                <td class="v-align-middle"><?php echo $ai->itemname;?></td>
                                                <td class="v-align-middle"><?php echo $ai->quantity;?></td>
                                                <td class="v-align-middle"><?php echo $ai->unit;?></td>
                                                <td class="v-align-middle"><?php if(isset($biditems)) { foreach($biditems as $biditem) {  if($biditem->itemid == $ai->itemid) { echo "$".$biditem->ea; $bidea = $biditem->ea; $totalprice += $ai->totalprice; } } } ?></td>
                                                <td class="v-align-middle"><?php if($bidea!=0) { // echo "$".round($ai->quantity * $bidea,2); 
									      		echo "$".round($totalprice,2); } 
                                                //else { echo "$".round($ai->quantity * $ai->ea,2); } ?></td>
                                                <td class="v-align-middle"><?php echo $ai->daterequested;?></td>
                                                <td class="v-align-middle"><?php echo $ai->notes;?></td>
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
                      No Items Detected on System.
                      </div>
                     </div>
      				</div>
                <?php }?>
			
		</div>
	  </div> 