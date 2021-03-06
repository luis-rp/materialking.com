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
$.noConflict();
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
			<h3>Orders&nbsp;&nbsp;&nbsp;&nbsp;<a  href="<?php echo site_url('order/export')?>" class="btn btn-primary">Export</a>&nbsp;&nbsp;<a  href="<?php echo site_url('order/order_pdf')?>" class="btn btn-primary">View PDF</a></h3>
		</div>
	   <div id="container">
		<?php
		    	if($orders)
		    	{
		    ?>
		<div class="row">
                    <div class="col-md-12">
                    <div>
				   		<div class="combofixed" style="background-color:white;position:relative;margin:0px;">
				   		
			    		   <form class="form-inline" action="<?php echo site_url('order')?>" method="post">
			    		   
			    		    <div class="form-group">
			    		     <label class="form-label"> From</label>
			    		       <div class="controls">
			                     <input type="text" name="searchfrom" value="<?php echo @$_POST['searchfrom']?>" class="datefield"/>
			                   </div>
			                </div>
			                
			               <div class="form-group">
			    		     <label class="form-label"> To</label>
			    		       <div class="controls">		           
			                     <input type="text" name="searchto" value="<?php echo @$_POST['searchto']?>" class="datefield"/>
			                    </div>
			                </div>
			                
			               <div class="form-group">
			    		     <label class="form-label"> Company</label>
			    		       <div class="controls">		           			                    
			    				<select id="purchasingadmin" name="purchasingadmin"> 
			    					<option value=''>All Companies</option>
			    					<option value='guest'>Guest</option>
			    					<?php foreach($purchasingadmins as $company){?>
			    						<option value="<?php echo $company->id?>"
			    							<?php if(@$_POST['purchasingadmin']==$company->id){echo 'SELECTED';}?>
			    							>
			    							<?php echo $company->companyname?>
			    						</option>
			    						
			    					<?php }?>
			    				</select>
			    		       </div>
			                </div>
			                
			                		
			                <div class="form-group">
			    		     <label class="form-label"> Payment Status</label>
			    		       <div class="controls">	   		                   
			                    <select id="searchpaymentstatus" name="searchpaymentstatus" class="form-control selectpicker show-tick">
                            		<option value=''>All</option>
                                    <option value="Paid" <?php if (@$_POST['searchpaymentstatus'] == 'Paid') { echo 'SELECTED'; } ?>>Paid</option>
              <option value="Requested Payment" <?php if (@$_POST['searchpaymentstatus'] == 'Requested Payment') { echo 'SELECTED'; } ?>>Requested Payment</option>
                                    <option value="Unpaid" <?php if (@$_POST['searchpaymentstatus'] == 'Unpaid') { echo 'SELECTED'; } ?>>Unpaid</option>
                                </select>
                                </div>
                             </div>
                             
                             
			    			 <div class="form-group">
			    		     <label class="form-label"> Order Status</label>
			    		       <div class="controls">		 		    				
			    				  <select id="searchorderstatus" name="searchorderstatus" class="form-control selectpicker show-tick">
                            		<option value=''>All</option>
                                    <option value="Pending" <?php if (@$_POST['searchorderstatus'] == 'Pending') { echo 'SELECTED'; } ?>>Pending</option>
              						<option value="Accepted" <?php if (@$_POST['searchorderstatus'] == 'Accepted') { echo 'SELECTED'; } ?>>Accepted</option>
			    				</select>
			    			   </div>
                             </div>
                             	
			    			 <div class="form-group">
			    		     <label class="form-label">Order Type</label>
			    		       <div class="controls">					    				 
			    				 <select id="ordertype" name="ordertype" class="form-control selectpicker show-tick">
                            		<option value=''>All</option>
                                    <option value="Credit Card" <?php if (@$_POST['ordertype'] == 'Credit Card') { echo 'SELECTED'; } ?>>Credit Card</option>
              						<option value="Manual" <?php if (@$_POST['ordertype'] == 'Manual') { echo 'SELECTED'; } ?>>Manual</option>
			    				</select>
			    			   </div>
                             </div>	
			    				
			    				<div class="form-group">
			    				    <label class="form-label">Search By Email</label>
			    				  <div class="controls">		    				
			    					<input type="text" name="searchbyguest" id="searchbyguest" placeholder="Search By Email">
			    					</div>
			    				</div>
			    				<div class="form-group">
			    				    <label class="form-label">Search By Keyword</label>
			    				  <div class="controls">		    				
			    					<input type="text" name="searchbykeyword" id="searchbykeyword" placeholder="Search By Keyword">
			    					</div>
			    				</div>
			    				
			    				 <div class="form-group">
				                     <label class="form-label"></label>
				                        <div class="controls">
			                   			 <input type="submit" value="Filter" class="btn btn-primary"/>
			                    			<a href="<?php echo site_url('report');?>">
			                    			<input type="button" value="Show All" class="btn btn-primary"/>
			                    			</a>
			                    		</div>
			                    	</div>
			               </form>
			        </div></div>

                        <div class="grid simple ">
                           <!-- <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                            </div>-->
                            <div class="grid-body no-border">
                                    <table id="datatable" class="table no-more-tables general" style="margin-top:40px;table-layout: fixed;width:100%;">
                                        <thead>
                                            <tr>
                                                <th style="width:11%">Order#</th>
                                                <th style="width:14%">Ordered by</th>
                                                <th style="width:8%">Payment Status</th>
                                                <th style="width:8%">Order Status</th>
                                                <th style="width:9%">Ordered On</th>
                                                <th style="width:8%">Amount</th>
                                                <th style="width:8%">Type</th>
                                                <th style="width:10%">Txn Id</th>
                                                <th style="width:7%">Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody style="word-wrap:break-word;">
							              <?php
									    	$i = 0;
									    	$finaltotal = 0;
									    	$totalpaid=0;
									    	$totalunpaid=0;

									    	foreach($orders as $order)
									    	{
									    		
									    		$i++;
									    		$tot="";
									    		if($order->status =="Void")
									    		{
									    			$tot=0;
									    		}
									    		else 
									    		{
									    			$tot=$order->amount;
									    		}
									      ?>
                                           <tr>
                                                <td style="padding: 10px 4px !important;"><?php if(isset($order->ordernumber)) echo $order->ordernumber;?></td>
                                                <td style="padding: 10px 4px !important;"><?php if(isset($order->purchaser->companyname)) echo $order->purchaser->companyname;?></td>
                                                <td style="padding: 10px 4px !important;"><?php if(isset($order->paymentstatus)) echo $order->paymentstatus;?></td>
                                                <td style="padding: 10px 4px !important;"><?php if(isset($order->status)) { if($order->status =="Void") echo "Declined"; elseif($order->status =="Accepted") echo "Approved"; else echo $order->status; } ?></td>
                                                <td style="padding: 10px 4px !important;"><?php if(isset($order->purchasedate)) echo date("d F Y", strtotime($order->purchasedate));?></td>
                                                <td style="padding: 10px 4px !important;"><?php if(isset($order->amount)) echo $tot;?></td>
                                                <td style="padding: 10px 4px !important;"><?php if(isset($order->type)) echo $order->type;?></td>
                                                <td style="padding: 10px 4px !important;"><?php if(isset($order->txnid)) echo $order->txnid;?></td>
                                                <td style="padding: 10px 4px !important;"><?php if(isset($order->id)) { ?>
                                                	<a href="<?php echo site_url('order/details/'.$order->id);?>">
                                                		<span class="icon icon-search">Details</span>
                                                	</a>
                                                	<?php } ?>
                                                </td>
                                            </tr>
                                          <?php  
                                          
                                          
                                          $finaltotal += $tot;

									    	if($order->paymentstatus=='Paid')
									    	{
									    		$totalpaid+= $tot;
									    	}

									    	if($order->paymentstatus=='Unpaid' || $order->paymentstatus=='Requested Payment')
									    	{
									    		$totalunpaid+= $tot;
									    	}


									    	}?>
                                          </tr>
                                          <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align="right">Total:</td>
                                          <td><?php echo "$ ".round($finaltotal,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                          <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align="right">Total Paid:</td>
                                          <td><?php echo "$ ".round($totalpaid,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                          <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align="right">Total Unpaid:</td>
                                          <td><?php echo "$ ".round($totalunpaid,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>

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