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
			<h3>PO Performance : <?php echo $quote->ponum;?> </h3>
			<h4>
				<font color="green">Items Won : <?php echo $itemswon;?></font>
				&nbsp;&nbsp;&nbsp;
				<font color="red">Items Lost : <?php echo $itemslost;?></font>
				<?php if($award && $itemswon && @$award->id){?>
				<a href="<?php echo site_url('quote/track/'.$quote->id.'/'.$award->id);?>">Track</a>
				<?php }?>
				<a class="pull-right" href="<?php echo site_url('message/messages/'.$quote->id);?>">View Messages</a>
			</h4>
			<?php if($itemswon){?>
				<a href="<?php echo site_url('quote/getawardedpdf/'.$quote->id); ?>">PDF</a>
			<?php }?>
			<br/>
			<?php if($bid){?>
				<a href="<?php echo site_url('quote/viewbid/'.$bid->id); ?>">View Quote</a>
			<?php }?>
		</div>		
	   <div id="container">
		<?php 
		    	if($allawardeditems)
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
									    		$i++;
									      ?>
                                            <tr class="<?php echo $ai->company == $company->id?'awarded-to-me':'not-awarded-to-me';?>">
                                                <td class="v-align-middle"><?php echo $ai->itemcode;?></td>
                                                <td class="v-align-middle"><?php echo $ai->itemname;?></td>
                                                <td class="v-align-middle"><?php echo $ai->quantity;?></td>
                                                <td class="v-align-middle"><?php echo $ai->unit;?></td>
                                                <td class="v-align-middle">$<?php echo $ai->ea;?></td>
                                                <td class="v-align-middle">$<?php echo round($ai->quantity * $ai->ea,2);?></td>
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