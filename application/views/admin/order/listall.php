<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>

<script type="text/javascript" charset="utf-8">
$(document).ready( function() {

	$('#datatable').dataTable( {
		"aaSorting": [],
		"sPaginationType": "full_numbers",
		"aoColumns": [
		        		null,
		        		null,
		        		null,
		        		null,
		        		null,
        		{ "bSortable": false}
		
			]
		} );
	 $('.dataTables_length').hide();
});      
</script>


	
<section class="row-fluid">
	<h3 class="box-header">Orders</h3>
	<div class="box">
	  <div class="span12">
	
	   <?php echo $this->session->flashdata('message'); ?>
	    
	    <div class="datagrid-example">
			<div style="width:100%;margin-bottom:20px;">
				<?php if($orders) { ?>
                                    <table id="datatable" class="table table-bordered datagrid">
                                        <thead>
                                            <tr>
                                                <th style="width:10%">Order#</th>
                                                <th style="width:15%">Ordered On</th>
                                                <th style="width:15%">Ordered By</th>
                                                <th style="width:10%">Type</th>
                                                
                                                <th style="width:10%">TXN ID</th>
                                                <th style="width:30%">Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
							              <?php
									    	$i = 0;
									    	foreach($orders as $order)
									    	{
									    		$i++;
									      ?>
                                            <tr>
                                                <td><?php echo $order->ordernumber;?></td>
                                                <td><?php echo $order->purchasedate;?></td>
                                                <td><?php echo $order->purchaser->companyname;?></td>
                                                <td><?php echo $order->type;?></td>
                                                <td><?php echo $order->txnid;?></td>
                                                <td>
                                                	<table class="table table-bordered datagrid">
                                                		<tr>
                                                			<th>Company</th>
                                                			<th>Total</th>
                                                		</tr>
                                                		<?php foreach($order->details as $detail){?>
                                                		<tr>
                                                			<td><?php echo $detail->company;?></td>
                                                			<td><?php echo round(($detail->total + ($detail->total*$detail->taxpercent)/100 ),2);?></td> 
                                                		</tr>
                                                		<?php }?>
                                                	</table>
                                                </td>
                                            </tr>
                                          <?php } ?>
                                        </tbody>
                                    </table>
            	<?php }else{ ?>
            	No Purchase Orders.
            	<?php }?>
            </div>
         </div>
      </div>
    </div>
</section>