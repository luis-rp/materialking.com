<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript">

$(document).ready(function(){
	$('.datefield').datepicker();
});
</script>
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
<section class="row-fluid">
	<h3 class="box-header">My Purchased Items <a href="<?php echo site_url('admin/order/orders_export'); ?>" class="btn btn-green">Export</a> &nbsp;&nbsp; <a href="<?php echo site_url('admin/order/orders_pdf'); ?>" class="btn btn-green">View PDF</a></h3>

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
                    Company:
				    <select id="searchcompany" name="searchcompany" style="width: 150px;">
					 <option value=''>All Companies</option>
					  <?php if(count($companies)>0) { foreach($companies as $company){?>
						<option value="<?php echo $company->id?>"
							<?php if(@$_POST['searchcompany']==$company->id){echo 'SELECTED';}?>
							>
							<?php if(isset($company->title) && $company->title!="") echo $company->title?>
						</option>
					 <?php } }?>
				    </select>
				 &nbsp;&nbsp;
                        Paid Status:
                        <select id="searchpaymentstatus" name="searchpaymentstatus" style="width: 100px;">
                            <option value=''>All</option>
                            <option value="Paid" <?php if (@$_POST['searchpaymentstatus'] == 'Paid') { echo 'SELECTED'; } ?>>Paid</option>
                           <option value="Requested Payment" <?php if (@$_POST['searchpaymentstatus'] == 'Requested Payment') { echo 'SELECTED'; } ?>>Requested Payment</option>
                            <option value="Unpaid" <?php if (@$_POST['searchpaymentstatus'] == 'Unpaid') { echo 'SELECTED'; } ?>>Unpaid</option>
                        </select>   
                &nbsp;&nbsp;
                        Order Status:
                        <select id="searchorderstatus" name="searchorderstatus" style="width: 100px;">
                            <option value=''>All</option>
                            <option value="Accepted" <?php if (@$_POST['searchorderstatus'] == 'Accepted') { echo 'SELECTED'; } ?>>Accepted</option>
                           <option value="Void" <?php if (@$_POST['searchorderstatus'] == 'Void') { echo 'SELECTED'; } ?>>Void</option>
                            <option value="Pending" <?php if (@$_POST['searchorderstatus'] == 'Pending') { echo 'SELECTED'; } ?>>Pending</option>
                        </select>         
                &nbsp;&nbsp;
                Project:
                <select name="searchproject" id="searchproject"  style="width: 100px;">
								<option value="">View All</option>
								<?php foreach($projects as $p){?>
						      	<option value="<?php echo $p->id;?>" <?php if($p->id==@$_POST['searchproject']){echo 'SELECTED';}?>>
						      		<?php echo $p->title;?>
						      	</option>
						      	<?php }?>
							</select>
                &nbsp;&nbsp;
                Costcode:
                <select name="searchcostcode" id="searchcostcode"  style="width: 100px;">
								<option value="">View All</option>
								<?php foreach($costcode as $c){?>
						      	<option value="<?php echo $c->id;?>" <?php if($c->id==@$_POST['searchcostcode']){echo 'SELECTED';}?>>
						      		<?php echo $c->code;?>
						      	</option>
						      	<?php }?>
							</select>
                &nbsp;&nbsp;
                <div style="float:right;margin-top:5px;margin-right:16px;">
                <input type="submit" value="Filter" class="btn btn-primary"/>
                <a href="<?php echo site_url('admin/order');?>">
                	<input type="button" value="Show All" class="btn btn-primary"/>
                </a>
                </div>
           </form>

			<div>
				<?php if($orders) { ?>
                                    <table id="datatable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width:8%">Order#</th>
                       							<th style="width:7%">Ordered On</th>
                                                <th style="width:8%">Project</th>
                                                <th style="width:7%">Type</th>
                                                <th style="width:7%">Txn ID</th>
                                                <th style="width:8%">Total Amount</th>
                                                <th style="width:9%">Total Amount Paid</th>
                                                <th style="width:8%">Total Amount Due</th>
                                                <th style="width:28%">Details</th>
                                                <th style="width:10%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
							              <?php
							              	$total = 0;
                                            $oldorderid = "";
									    	$i = 0;
									    	$finaltotal = 0;
									    	$finalpaid = 0;
									    	$finaldue = 0;
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
                                                <td><?php if(isset($order->prjName)) echo $order->prjName.",";?> <?php if(isset($order->codeName)) echo $order->codeName;?></td>
                                                <td><?php echo $order->type;?></td>
                                                <td><?php echo $order->txnid?$order->txnid:$order->paymentnote;?></td>
                                                <td><?php echo "$ ".round($total+$order->shipping,2);?></td>
                                                <?php
                                               $totalpaid=0;
                                               $totaldue=0;
                                               $finaltotal += $total+$order->shipping;
                                               foreach($order->details as $detail)
                                                   {
                                               		  if($detail->paymentstatus=='Paid')
                                               			{
                                               				$totalpaid +=round(($detail->shipping+$detail->total + ($detail->total*$detail->taxpercent)/100 ),2);
                                               			}

                                               		  if($detail->paymentstatus=='Unpaid')
                                               			{
                                               				$totaldue +=round(($order->shipping+$detail->total + ($detail->total*$detail->taxpercent)/100 ),2);
                                               			}
                                               			
                                               			if($detail->paymentstatus=='Requested Payment')
                                               			{
                                               				$totaldue +=round(($order->shipping+$detail->total + ($detail->total*$detail->taxpercent)/100 ),2);
                                               			}
                                               			
                                                   }
                                                  $finaldue += $totaldue;
                                                  $finalpaid += $totalpaid;
                                                  ?>
                                                <td>$<?php echo $totalpaid; ?></td>
                                                <td>$<?php echo $totaldue; ?></td>
                                                <td>
                                                	<table class="table table-bordered datagrid">
                                                		<tr>
                                                			<th>Company</th>
                                                			<th>Paid Status</th>
                                                			<th>Order Status</th>
                                                            <th>Tax</th>
                                                            <th>Shipping</th>
                                                			<!--<th>Total</th>-->
                                                		</tr>
                                                		<?php foreach($order->details as $detail){?>
                                                		<tr>
                                                			<td><?php echo $detail->company;?></td>
                                                			<td><?php echo $detail->paymentstatus;?></td>
                                                			<td><?php if($detail->status=="Void") echo "Declined"; else echo $detail->status;?></td>
     														<td><?php echo $detail->taxpercent;?>%</td>
     														<td>$<?php echo $detail->shipping;?></td>                                           			
     									<!--	<td>$<?php //echo round(($detail->shipping+$detail->total + ($detail->total*$detail->taxpercent)/100 ),2);?></td>-->
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
                                           <tr><td colspan="5"></td><td style="text-align:center;"><strong>Grand Total</strong></td><td style="text-align:center;"><strong>Total Paid</strong></td><td style="text-align:center;"><strong>Total Due</strong></td><td colspan="2"></td></tr>
                                           <tr style="text-align:center;"><td colspan="5"></td><td style="text-align:center;"><strong><?php echo "$ ".round($finaltotal,2);?></strong></td><td style="text-align:center;"><strong><?php echo "$ ".round($finalpaid,2);?></strong></td><td style="text-align:center;"><strong><?php echo "$ ".round($finaldue,2);?></strong></td><td colspan="2"></td></tr>
                                           
                                        </tbody>
                                    </table>
            	<?php }else{ ?>
            	No Store Purchases
            	<?php }?>
            </div>
      </div>
    </div>
</section>