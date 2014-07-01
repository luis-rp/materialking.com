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
		        		null
		
			]
		} );
	 $('.dataTables_length').hide();
});      
</script>
<script type="text/javascript">
<!--
$(document).ready(function(){
	$('.datefield').datepicker();
});
</script>
    <div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
			<h3>Orders</h3>
		</div>		
	   <div id="container">
		<?php 
		    	if($orders)
		    	{
		    ?>
		<div class="row">
                    <div class="col-md-12">
				   		<div class="combofixed">
					   
			    		   <form class="form-inline" action="<?php echo site_url('order')?>" method="post">
			                    From: <input type="text" name="searchfrom" value="<?php echo @$_POST['searchfrom']?>" class="datefield" style="width: 70px;"/>
			                    &nbsp;&nbsp;
			                    To: <input type="text" name="searchto" value="<?php echo @$_POST['searchto']?>" class="datefield" style="width: 70px;"/>
			                    &nbsp;&nbsp;
			                    Company:
			    				<select id="purchasingadmin" name="purchasingadmin">
			    					<option value=''>All Companies</option>
			    					<?php foreach($purchasingadmins as $company){?>
			    						<option value="<?php echo $company->id?>"
			    							<?php if(@$_POST['purchasingadmin']==$company->id){echo 'SELECTED';}?>
			    							>
			    							<?php echo $company->companyname?>
			    						</option>
			    					<?php }?>
			    				</select>
			                    &nbsp;&nbsp;
			                    <input type="submit" value="Filter" class="btn btn-primary"/>
			                    <a href="<?php echo site_url('report');?>">
			                    	<input type="button" value="Show All" class="btn btn-primary"/>
			                    </a>
			               </form>
			               <br/><br/><br/><br/>
			        </div>

                        <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                            </div>
                            <div class="grid-body no-border">
                                    <table id="datatable" class="table no-more-tables general">
                                        <thead>
                                            <tr>
                                                <th style="width:20%">Order#</th>
                                                <th style="width:30%">Ordered by</th>
                                                <th style="width:30%">Payment Status</th>
                                                <th style="width:20%">Ordered On</th>
                                                <th style="width:30%">Amount</th>
                                                <th style="width:10%">Type</th>
                                                <th style="width:10%">Txn Id</th>
                                                <th style="width:10%">Actions</th>
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
                                                <td><?php echo $order->purchaser->companyname;?></td>
                                                <td><?php echo $order->paymentstatus;?></td>
                                                <td><?php echo date("d F Y", strtotime($order->purchasedate));?></td>
                                                <td><?php echo $order->amount;?></td>
                                                <td><?php echo $order->type;?></td>
                                                <td><?php echo $order->txnid;?></td>
                                                <td>
                                                	<a href="<?php echo site_url('order/details/'.$order->id);?>">
                                                		<span class="icon icon-search">Details</span>
                                                	</a>
                                                </td>
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
	                  No Orders Detected on System.
	                  </div>
	                 </div>
	      			</div>
                <?php }?>
			
		</div>
	  </div> 