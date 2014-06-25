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
	 $('#datatable_filter').hide();
});      
</script>
<script type="text/javascript">

$(document).ready(function(){
	$('.datefield').datepicker();
});
</script>

<section class="row-fluid">
	<h3 class="box-header">My Purchased Items</h3>
	<div class="box">
	  <div class="span12">
	
	   <?php echo $this->session->flashdata('message'); ?>
		   <br/>
		   
		   <form class="form-inline" action="<?php echo site_url('admin/order')?>" method="post">
                From: <input type="text" name="searchfrom" value="<?php echo @$_POST['searchfrom']?>" class="datefield" style="width: 70px;"/>
                &nbsp;&nbsp;
                To: <input type="text" name="searchto" value="<?php echo @$_POST['searchto']?>" class="datefield" style="width: 70px;"/>
                &nbsp;&nbsp;
                Order #:
				<input type="text" name="ordernumber" value="<?php echo @$_POST['ordernumber']?>" style="width: 70px;"/>
                &nbsp;&nbsp;
                <input type="submit" value="Filter" class="btn btn-primary"/>
                <a href="<?php echo site_url('admin/order');?>">
                	<input type="button" value="Show All" class="btn btn-primary"/>
                </a>
           </form>
	    
			<div>
				<?php if($orders) { ?>
                                    <table id="datatable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width:10%">Order#</th>
                                                <th style="width:10%">Ordered On</th>
                                                <th style="width:10%">Project</th>
                                                <th style="width:10%">Type</th>
                                                <th style="width:10%">Txn ID</th>
                                                <th style="width:10%">Amount</th> 
                                                <th style="width:30%">Details</th>
                                                <th style="width:10%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
							              <?php
							              	$total = 0; 
                                            $oldorderid = ""; 
									    	$i = 0;
									    	foreach($orders as $order)
									    	{
									    		$i++;
									    		if($order->id != $oldorderid){ 
                                                $total = 0; 
                                                $total +=  ($order->totalprice) + ($order->totalprice)*$order->taxpercent/100; 
                                            }else{ 
                                                $total +=  ($order->totalprice) + ($order->totalprice)*$order->taxpercent/100; 
                                            } 
									      ?>
                                            <tr>
                                                <td><?php echo $order->ordernumber;?></td>
                                                <td><?php echo date('m/d/Y',strtotime($order->purchasedate)); ?> </td>
                                                <td><?php echo $order->prjName;?>, <?php echo $order->codeName;?></td>
                                                <td><?php echo $order->type;?></td>
                                                <td><?php echo $order->txnid;?></td>
                                                <td><?php echo "$ ".round($total,2);?></td> 
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
                                                <td>
                                                	<a href="<?php echo site_url('admin/order/details/'.$order->id);?>">
                                                		<span class="icon icon-search"></span>
                                                	</a>
                                                	<a href="<?php echo site_url('admin/order/add_to_project/'.$order->id);?>">
                                                		<span class="icon icon-list-ul"></span>
                                                	</a>
                                                </td>
                                            </tr>
                                           <?php $oldorderid = $order->id;  } ?>
                                        </tbody>
                                    </table>
            	<?php }else{ ?>
            	No Purchase Orders.
            	<?php }?>
            </div>
      </div>
    </div>
</section>